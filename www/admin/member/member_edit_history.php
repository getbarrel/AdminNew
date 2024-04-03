<?
	include("../class/layout.class");
	include("../webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
	include("../campaign/mail.config.php");


	$db = new Database;
	$mdb = new Database;
	$sms_design = new SMS;
	$ig_db = new Database;

	$update_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U");
	$delete_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D");
	$create_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C");
	$excel_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E");

	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
	

	if($before_update_kind){
		$update_kind = $before_update_kind;
	}
	if($_COOKIE["member_update_kind"]){
		$update_kind = $_COOKIE["member_update_kind"];	// 왜 페이지에 해당되는 일괄처리상태로 안하고 선택했던 쿠키값으로 다른페이지에서도 전에 상태로 나오게 했나요? - 이학봉
	}else if(!$update_kind){
		$update_kind = "sms";
	}


	if($_COOKIE[max_limit]){
		$max = $_COOKIE[max_limit]; //페이지당 갯수
	}else{
		$max = 15;
	}

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}

	if($gp_ix != ""){
		$where .= " and cmed.gp_ix = '".$gp_ix."' ";
		$count_where .= " cmed.and gp_ix = '".$gp_ix."' ";
		$cmd_where .= " and cmed.gp_ix = '".$gp_ix."' ";
	}


	if($mailsend_yn != "A" && $mailsend_yn != ""){
		$where .= " and cmd.info =  '$mailsend_yn' ";
		$count_where .= " and cmd.info =  '$mailsend_yn' ";
		$cmd_where .= " and cmd.info =  '$mailsend_yn' ";
	}

	if($mem_type != ""){
		$where .= " and cmed.mem_type =  '$mem_type' ";
		$count_where .= " and cmed.mem_type =  '$mem_type' ";
		$cmd_where .= " and cmed.mem_type =  '$mem_type' ";
	}

	if($mem_div != ""){
		$where .= " and cmed.mem_div =  '$mem_div' ";
		$count_where .= " and cmed.mem_div =  '$mem_div' ";
		$cmd_where .= " and cmed.mem_div =  '$mem_div' ";
	}

	if($smssend_yn != "A" && $smssend_yn != ""){
		$where .= " and cmd.sms =  '$smssend_yn' ";
		$count_where .= " and cmd.sms =  '$smssend_yn' ";
		$cmd_where .= " and cmd.sms =  '$smssend_yn' ";
	}

	$search_text = trim($search_text);
	if($db->dbms_type == "oracle"){
		if($search_type != "" && $search_text != ""){
			if($search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
				$where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
				$count_where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			}else{
				$where .= " and $search_type LIKE  '%$search_text%' ";
				$count_where .= " and $search_type LIKE  '%$search_text%' ";
			}
		}
	}else{
		if($search_type != "" && $search_text != ""){

			if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
				$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
				$count_where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			}else{
				$where .= " and $search_type LIKE  '%$search_text%' ";
				$count_where .= " and $search_type LIKE  '%$search_text%' ";
			}
		}
	}

	$startDate = str_replace("-","",$cmd_sdate);
	$endDate = str_replace("-","",$cmd_edate);

	if($startDate != "" && $endDate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(cu.date_ , 'YYYYMMDD') between  $startDate and $endDate ";
			$count_where .= " and  to_char(cu.date_ , 'YYYYMMDD') between  $startDate and $endDate ";
		}else{
			$where .= " and  MID(replace(cu.date,'-',''),1,8) between  $startDate and $endDate ";
			$count_where .= " and  MID(replace(cu.date,'-',''),1,8) between  $startDate and $endDate ";
		}
	}

	$vstartDate = str_replace("-","",$slast);
	$vendDate = str_replace("-","",$elast);

	if($vstartDate != "" && $vendDate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(cmed.regdate_ , 'YYYYMMDD') between  $vstartDate and $vendDate ";
			$count_where .= " and  to_char(cmed.regdate_ , 'YYYYMMDD') between  $vstartDate and $vendDate ";
		}else{
			$where .= " and  MID(replace(cmed.edit_date,'-',''),1,8) between  $vstartDate and $vendDate ";
			$count_where .= " and  MID(replace(cmed.edit_date,'-',''),1,8) between  $vstartDate and $vendDate ";
		}
	}

	if($_COOKIE[distinct] == 1){
		$group_by  = " group by cmed.code";
	}else{
		$group_by = " group by cmed.code, cmed.regdate ";
	}

	// 전체 갯수 불러오는 부분
	/*
	$sql = "select
					*
				from
					common_member_edit_history as cmed
					inner join common_user as cu on (cmed.code = cu.code)
					inner join common_member_detail as cmd on (cu.code = cmd.code)
				where
					1
					$where
					$group_by
					order by regdate DESC 
					LIMIT $start, $max";
		
	$db->query($sql);
	$db->fetch();
	$total = $db->total;
	*/

	if($db->dbms_type == "oracle"){//ccd.com_name, mg.gp_level,mg.gp_name, 
		$sql = "select
					cmed.*,
					cmd.info,
					cmd.sms
				from
					common_member_edit_history as cmed
					inner join common_user as cu on (cmed.code = cu.code)
					inner join common_member_detail as cmd on (cu.code = cmd.code)
				where
					1
					$where
					$group_by
					order by cmed.regdate DESC 
					LIMIT $start, $max";
	}else{
		$sql = "select
					SQL_CALC_FOUND_ROWS
					cmed.*,
					date_format(cmed.regdate, '%Y-%m-%d') edit_date2,
					cmd.info,
					cmd.sms
				from
					common_member_edit_history as cmed
					inner join common_user as cu on (cmed.code = cu.code)
					inner join common_member_detail as cmd on (cu.code = cmd.code)
				where
					1
					$where
					$group_by
					order by cmed.regdate DESC 
					LIMIT $start, $max";
	}

	$db->query($sql);
	$script_time[query_end] = time();
	
	$mdb->query("select FOUND_ROWS() as total ");
	$mdb->fetch();
	$total = $mdb->dt[total];

	$str_page_bar = page_bar($total, $page,$max, "&max=$max&update_kind=$update_kind&search_type=$search_type&search_text=$search_text&region=$region&gp_ix=$gp_ix&age=$age&birthday_yn=$birthday_yn&sex=$sex&mailsend_yn=$mailsend_yn&smssend_yn=$smssend_yn&mem_type=$mem_type&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");//gp_level 을 사용하지 않아서 gp_ix 로 바꿈 kbk 13/02/19


if($mode == "excel"){
	$goods_infos = $db->fetchall();
	$info_type = "edit_history";
	include("excel_out_columsinfo.php");
	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='member_".$info_type."' ";
	$db->query($sql);
	$db->fetch();
	$stock_report_excel = $db->dt[conf_val];

	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='member_edit_".$info_type."' ";
	$db->query($sql);
	$db->fetch();
	$stock_report_excel_checked = $db->dt[conf_val];

	$check_colums = unserialize(stripslashes($stock_report_excel_checked));

	$columsinfo = $colums;

	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$inventory_excel = new PHPExcel();

	// 속성 정의
	$inventory_excel->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("accounts plan price List")
								 ->setSubject("accounts plan price List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("accounts plan price List");
	$col = 'A';

	if(is_array($check_colums) && count($check_colums) > 0 ) {
        foreach ($check_colums as $key => $value) {
            $inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
            $col++;
        }

        $before_pid = "";

        for ($i = 0; $i < count($goods_infos); $i++) {
            $j = "A";
            foreach ($check_colums as $key => $value) {
                if ($key == "gp_ix") {
                    $mdb->query("SELECT gp_name FROM " . TBL_SHOP_GROUPINFO . " WHERE gp_ix = '" . $goods_infos[$i][gp_ix] . "'  ");
                    $mdb->fetch(0);
                    $value_str = $mdb->dt[gp_name];
                } else if ($key == "mem_type") {
                    switch ($goods_infos[$i][mem_type]) {
                        case "M":
                            $value_str = "일반회원";
                            break;
                        case "C":
                            $value_str = "사업자회원";
                            break;
                        case "A":
                            $value_str = "직원(관리자)";
                            break;
                    }
                } else if ($key == "mem_div") {
                    switch ($goods_infos[$i][mem_div]) {
                        case "MD":
                            $value_str = "MD담당자";
                            break;
                        case "S":
                            $value_str = "셀러";
                            break;
                        case "D":
                            $value_str = "기타";
                            break;
                    }
                } else if ($key == "edit_text") {
                    $edit_date = $goods_infos[$i][edit_date];
                    $regdate = $goods_infos[$i][regdate];

                    $sql = "select
						column_text
						from
							common_member_edit_history 
						where
							code  = '" . $goods_infos[$i][code] . "'
							and edit_date = '" . $edit_date . "'
							and regdate = '" . $regdate . "'";

                    $mdb->query($sql);
                    $history_text_array = $mdb->fetchall();

                    for ($k = 0; $k < count($history_text_array); $k++) {
                        if ($k == count($history_text_array) - 1) {
                            $history_text .= $history_text_array[$k][column_text];
                        } else {
                            $history_text .= $history_text_array[$k][column_text] . ", ";
                        }
                    }
                    $value_str = $history_text;

                } else {
                    $value_str = $goods_infos[$i][$value];//$db1->dt[$value];
                }

                $inventory_excel->getActiveSheet()->setCellValue($j . ($z + 2), $value_str);
                $j++;

                unset($history_text);
            }
            $z++;
        }
        // 첫번째 시트 선택
        $inventory_excel->setActiveSheetIndex(0);

        // 너비조정
        $col = 'A';
        foreach ($check_colums as $key => $value) {
            $inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
    }else{
        $inventory_excel->getActiveSheet(0)->setCellValue($col . "1", "엑셀 다운로드 형식 설정이 안되어 있습니다. 엑셀 설정하기를 클릭하여 저장 후 다시 엑셀 저장 시도해주세요.");
        $inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
	}
	//header('Content-Type: application/vnd.ms-excel');
	//header('Content-Disposition: attachment;filename="member_'.$info_type.'.xls"');
	//header('Cache-Control: max-age=0');

	//$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	//$objWriter->save('php://output');




		//	wel_ 엑셀 다운로드 히스토리 저장
			$ig_excel_dn_history_SQL = "
				INSERT INTO
					ig_excel_dn_history
				SET
					code = '".$admininfo[charger_ix]."',
					ip = '". $_SERVER['REMOTE_ADDR']."',
					dn_type = 'member_edit_history',
					dn_reason = '".addslashes($irs)."',
					dn_text = '".addslashes($QUERY_STRING)."',
					regDt = '".date("Y-m-d H:i:s")."'
			";
			$ig_db->query($ig_excel_dn_history_SQL);
		//	//wel_ 엑셀 다운로드 히스토리 저장



	//	ig 엑셀 파일 zip 파일로 생성 후 패스워드 걸기	방법2
		$download_filename = 'member_edit_history_'.$info_type.date("YmdHis").'.zip'; 
		$igExcel_file = '../excelDn/member_edit_history_'.$info_type.'.xls'; 

		$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
		$objWriter->save($igExcel_file);


		$ig_dnFile_full = '../excelDn/'.$download_filename;

			if(trim($ipw) == "") {
				$ig_pw = "barrel";
			} else {
				$ig_pw = $ipw;
			}


			shell_exec('zip -P '.$ig_pw.' -r ../excelDn/'.$download_filename.' '.$igExcel_file);

					header('Content-type: application/octet-stream');
					header('Content-Disposition: attachment; filename="' . $download_filename . '"'); // 저장될 파일 이름
					header('Content-Transfer-Encoding: binary');
					header('Content-length: ' . filesize($ig_dnFile_full));
					header('Expires: 0');
					header("Pragma: public");

					ob_clean();
					flush();
					readfile($ig_dnFile_full);


				unlink($igExcel_file);
				unlink($ig_dnFile_full);
	//	//ig 엑셀 파일 zip 파일로 생성 후 패스워드 걸기	방법2




	exit;
}


$Script = "
<script language='javascript'>

function view_go()
{
	var sort = document.view.sort[document.view.sort.selectedIndex].value;

	location.href = 'member.php?view='+sort;
}

function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		$('input[name=cmd_sdate]').attr('disabled',false);
		$('input[name=cmd_edate]').attr('disabled',false);

	}else{
		$('input[name=cmd_sdate]').attr('disabled',true);
		$('input[name=cmd_edate]').attr('disabled',true);
	}
}

function ChangeVisitDate(frm){
	if(frm.visitdate.checked){
		$('input[name=slast]').attr('disabled',false);
		$('input[name=elast]').attr('disabled',false);
	}else{
		$('input[name=slast]').attr('disabled',true);
		$('input[name=elast]').attr('disabled',true);
	}
}

function ChangeBirDate(frm){
	if(frm.bir.checked){
		frm.birYY.disabled = false;
		frm.birMM.disabled = false;
		frm.birDD.disabled = false;
	}else{
		frm.birYY.disabled = true;
		frm.birMM.disabled = true;
		frm.birDD.disabled = true;
	}
}

function init(){

	var frm = document.searchmember;
	onLoad('$sDate','$eDate','$sDate2','$eDate2','$sDate3');";

$Script .= "
}

function deleteMemberInfo(act, code){
 	if(confirm('해당회원 정보를 정말로 삭제하시겠습니까? \\n 삭제시 관련된 모든 정보가 삭제됩니다.')){
		window.frames['iframe_act'].location.href= 'member.act.php?act='+act+'&code='+code;
 	}
}


$(document).ready(function (){
	$('#max').change(function(){
		var value= $(this).val();
		
		$.cookie('max_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});

		document.location.reload();
		
	});

});

function reloadView(){

	if($('#distinct').attr('checked') == true || $('#distinct').attr('checked') == 'checked'){		
		$.cookie('distinct', '1', {expires:1,domain:document.domain, path:'/', secure:0});
	}else{		
		$.cookie('distinct', '0', {expires:1,domain:document.domain, path:'/', secure:0});
	}
	
	document.location.reload();

}


function ChangeUpdateForm(selected_id){
	var area = new Array('batch_update_reserve','batch_update_group','batch_update_sms','batch_update_coupon','batch_update_sendemail');

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';
			$.cookie('member_update_kind', selected_id.replace('batch_update_',''), {expires:1,domain:document.domain, path:'/', secure:0});
		}else{
			document.getElementById(area[i]).style.display = 'none';
		}
	}
}



	function BatchSubmit(frm){

		if(frm.update_type.value == 1 && frm.search_searialize_value.length < 1){
			alert(language_data['member_batch.php']['A'][language]);//'적용대상중 [검색회원전체]는  검색후 사용 가능합니다. 확인후 다시 시도해주세요'
			return false;
		}

		//alert($('#update_kind_group').attr('checked'));
		//return false;
		if($('#update_kind_reserve').attr('checked')){
			if(frm.reserve.value == ''){
				alert(language_data['member_batch.php']['N'][language]);//'적립금 지금액/차감액을 입력해주세요'
				frm.reserve.focus();
				return false;
			}

			if(frm.etc.value == ''){
				alert(language_data['member_batch.php']['B'][language]);//'적립금 적립내용을 입력해주세요'
				frm.etc.focus();
				return false;
			}
		}else if($('#update_kind_group').attr('checked')){
			if(frm.update_gp_ix.value == ''){
				alert(language_data['member_batch.php']['C'][language]);//'변경하시고자 하는 회원그룹을 선택해주세요'
				if(frm.update_gp_ix.value == '' && !frm.update_gp_ix.disabled){
					frm.update_gp_ix.focus();
				}
				return false;
			}

		}else if($('#update_kind_sms').attr('checked')){
			if(frm.sms_text.value.length < 1){
				alert(language_data['member_batch.php']['A'][language]);//'SMS 발송 내역을 입력하신후 보내기 버튼을 클릭해주세요'
				frm.sms_text.focus();
				return false;
			}
		}else if($('#update_kind_coupon').attr('checked')){
			if(frm.publish_ix.value == ''){
				alert(language_data['member_batch.php']['E'][language]);//'지급 하시고자 하는 쿠폰을 선택해주세요'
				if(frm.publish_ix.value == ''){
					frm.publish_ix.focus();
				}
				return false;
			}
		}else if($('#update_kind_sendemail').attr('checked')){

			if(frm.email_subject.value.length < 1){
				alert(language_data['member_batch.php']['F'][language]);//'이메일 제목을 입력해주세요'
				frm.email_subject.focus();
				return false;
			}

			frm.mail_content.value = iView.document.body.innerHTML;

			if(frm.mail_content.value.length < 1 || frm.mail_content.value == '<P>&nbsp;</P>'){
				alert(language_data['member_batch.php']['G'][language]);//'이메일 내용을 입력하신후 보내기 버튼을 클릭해주세요'
				//frm.mail_content.focus();
				return false;
			}
		}

		if(frm.update_type.value == 1){
			if($('#update_kind_reserve').attr('checked')){
				if(!confirm(language_data['member_batch.php']['I'][language])){return false;}//'검색회원 적립금 일괄 지급을 하시겠습니까?'
			}else if($('#update_kind_group').attr('checked')){
				if(!confirm(language_data['member_batch.php']['J'][language])){return false;}//'검색회원 전체의 회원그룹 변경을 하시겠습니까?'
			}else if($('#update_kind_sms').attr('checked')){
				if(!confirm(language_data['member_batch.php']['K'][language])){return false;}//'검색회원 전체에게 SMS 발송을 하시겠습니까?'
			}else if($('#update_kind_coupon').attr('checked')){
				if(!confirm(language_data['member_batch.php']['L'][language])){return false;}//'검색회원 전체에게 쿠폰일괄지급을 하시겠습니까?'
			}else if($('#update_kind_sendemail').attr('checked')){
				if(!confirm(language_data['member_batch.php']['M'][language])){return false;}//'검색회원 전체에게 이메일발송을 하시겠습니까?'
			}
			//alert(frm.update_kind.value);
		}else if(frm.update_type.value == 2){
			var code_checked_bool = false;
			for(i=0;i < frm.code.length;i++){
				if(frm.code[i].checked){
					code_checked_bool = true;
				}
				//	frm.code[i].checked = false;
			}
			if(!code_checked_bool){
				alert(language_data['member_batch.php']['H'][language]);//'선택된 회원이 없습니다. 변경/발송 하시고자 하는 수신자를 선택하신 후 저장/보내기 버튼을 클릭해주세요'
				return false;
			}
		}
	}

	function input_check_num() {
		var sms_cnt=document.getElementById('remainder_sms_cnt');
		var email_cnt=document.getElementById('remainder_email_cnt');
		var frm=document.list_frm;
		if(frm.update_type.value==2) {
			var code_checked_num = 0;
			for(i=1;i < frm.code.length;i++){
				if(frm.code[i].checked){
					code_checked_num++;
				}
			}
			sms_cnt.innerHTML=code_checked_num;
			email_cnt.innerHTML=code_checked_num;
		}
	}

	function LoadEmail(email_type){
		if(email_type == 'new'){
			//$('#email_subject_text').css('display','inline');
			$('#email_select_area').css('display','none');
		}else if(email_type == 'box'){
			//$('#email_subject_text').css('display','none');
			$('#email_select_area').css('display','inline');
		}
	}

	$(document).ready(function() {
		$('select#email_subject_select').change(function(){
			if($(this).val() != ''){
				$.ajax({
					type: 'GET',
					data: {'act': 'mail_info', 'mail_ix': $(this).val()},
					url: '../campaign/mail.act.php',
					dataType: 'json',
					async: true,
					beforeSend: function(){
					},
					success: function(mail_info){
						document.getElementById('iView').contentWindow.document.body.innerHTML = mail_info.mail_text;
						$('#email_subject_text').val(mail_info.mail_title);
						//alert(mail_info);
						//$('#row_'+wl_ix).slideRow('up',500);
					}
				});
			}
		});

		$('#state').change(function() {

			var value = $(this).val();

			if(value == '2'){
				$('#use_state1').css('display','none');
				$('#use_state2').css('display','');

				$('#use_state1').attr('disabled',true);
				$('#use_state2').attr('disabled',false);
			}else{
				$('#use_state1').css('display','');
				$('#use_state2').css('display','none');

				$('#use_state1').attr('disabled',false);
				$('#use_state2').attr('disabled',true);
			}
		});
	});


</script>";

$Contents = "
<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>
<table width='100%' border='0' align='center'>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("전체회원관리", "회원관리 > 전체회원관리 ")."</td>
</tr>
<tr>
	<td>";

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "본사관리 > $menu_name")."</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:20px;'>
		<div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "member" || $info_type == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'>회원정보 수정 리스트</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

				</td>
			</tr>
			</table>
		</div>
		</td>
	</tr>
	</table>";

$Contents .= "
	<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<th class='box_01'></th>
		<td class='box_02'></td>
		<th class='box_03'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<td class='box_05'  valign=top style='padding:5px 0px 5px 0px'>
 			<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
		<form name=searchmember method='get'><!--SubmitX(this);'-->
            <input type='hidden' name=mc_ix value='".$mc_ix." '>
		    <col width='12%'>
			<col width='*'>
			<tr height=27>
				<td class='search_box_title' >조건검색 </td>
				<td class='search_box_item'>
					<table cellpadding=0 cellspacing=0 width=100%>
						<col width='80'>
						<col width='*'>
						<tr>
							<td>
							  <select name=search_type>
									<option value='cmed.name' ".CompareReturnValue("cmed.name",$search_type,"selected").">이름</option>
									<option value='cmed.id' ".CompareReturnValue("cmed.id",$search_type,"selected").">아이디</option>
									<option value='chager_name' ".CompareReturnValue("chager_name",$search_type,"selected").">수정자명</option>
							  </select>
							</td>
							<td>
								<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:70%;font-size:12px;padding:1px;' >
							</td>
						</tr>
					</table>
				</td>
				<td class='search_box_title' >회원그룹 </td>
				<td class='search_box_item' >
				".makeGroupSelectBox($mdb,"gp_ix",$gp_ix)."
				</td>
			</tr>
			<tr height=27>
				<td class='search_box_title' >회원타입 </td>
				<td class='search_box_item' >
					<input type=radio name='mem_div' value='' id='sex_all'  ".CompareReturnValue("",$mem_div,"checked")." checked><label for='sex_all'>전체</label>
					<input type=radio name='mem_div' value='S' id='sex_man'  ".CompareReturnValue("S",$mem_div,"checked")."><label for='sex_man'>셀러</label>
					<input type=radio name='mem_div' value='MD' id='sex_women' ".CompareReturnValue("MD",$mem_div,"checked")."><label for='sex_women'>MD담당자</label>
					<input type=radio name='mem_div' value='D' id='sex_d' ".CompareReturnValue("D",$mem_div,"checked")."><label for='sex_d'>기타</label>
				</td>
				<td class='search_box_title' >회원구분 </td>
				<td class='search_box_item' >
					<input type=radio name='mem_type' value=''  id='mem_type_'  ".CompareReturnValue("",$mem_type,"checked")." checked><label for='mem_type_'>전체</label>
					<input type=radio name='mem_type' value='M' id='mem_type_m' ".CompareReturnValue("M",$mem_type,"checked")."><label for='mem_type_m'>일반회원</label>
					<input type=radio name='mem_type' value='C' id='mem_type_c' ".CompareReturnValue("C",$mem_type,"checked")."><label for='mem_type_c'>사업자회원</label>
					<input type=radio name='mem_type' value='A' id='mem_type_s' ".CompareReturnValue("S",$mem_type,"checked")."><label for='mem_type_s'>직원(관리자)</label>
				</td>
		    </tr>
			<tr height=27>
				<td class='search_box_title' >이메일 발송여부 </td>
				<td class='search_box_item'  >
				<input type=radio name='mailsend_yn' value='A' id='mailsend_a'  ".CompareReturnValue("A",$mailsend_yn,"checked")." checked><label for='mailsend_a'>전체</label>
				<input type=radio name='mailsend_yn' value='1' id='mailsend_y'  ".CompareReturnValue("1",$mailsend_yn,"checked")."><label for='mailsend_y'>수신회원만</label><input type=radio name='mailsend_yn' value='0' id='mailsend_n' ".CompareReturnValue("0",$mailsend_yn,"checked")."><label for='mailsend_n'>수신거부회원포함</label>
				</td>
				<td class='search_box_title' >SMS 발송여부 </td>
				<td class='search_box_item'  >
					<input type=radio name='smssend_yn' value='A' id='smssend_a'  ".CompareReturnValue("A",$smssend_yn,"checked")." checked><label for='smssend_a'>전체</label>
					<input type=radio name='smssend_yn' value='1' id='smssend_y'  ".CompareReturnValue("1",$smssend_yn,"checked")."><label for='smssend_y'>수신회원만</label>
					<input type=radio name='smssend_yn' value='0' id='smssend_n' ".CompareReturnValue("0",$smssend_yn,"checked")."><label for='smssend_n'>수신거부회원포함</label>
				</td>
			</tr>";

		$vdate = date("Ymd", time());
		$today = date("Y/m/d", time());
		$vyesterday = date("Y/m/d", time()-84600);
		$voneweekago = date("Y/m/d", time()-84600*7);
		$vtwoweekago = date("Y/m/d", time()-84600*14);
		$vfourweekago = date("Y/m/d", time()-84600*28);
		$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
		$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
		$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
		$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
		$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
		$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
		$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

	$Contents .= "
			<tr height=27>
				<td class='search_box_title' ><label for='regdate'>가입일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
				<td class='search_box_item'  colspan=3 >
					".search_date('cmd_sdate','cmd_edate',$cmd_sdate,$cmd_edate)."
				</td>
			</tr>
			<tr height=27>
				<td class='search_box_title' ><label for='visitdate'>수정일</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeVisitDate(document.searchmember);' ".CompareReturnValue("1",$visitdate,"checked")."></td>
				<td class='search_box_item'  colspan=3  >
					".search_date('slast','elast',$slast,$elast)."
				</td>
			</tr>
			</table>
		</td>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
	</table>
	</td>
</tr>
<tr height=50>
	<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
</tr>
</table>
<br>
</form>";

$Contents .= "
<form name='list_frm' method='POST' onsubmit='return BatchSubmit(this);' action='member_edit_history.act.php'  target='act'>
<input type='hidden' name='code[]' id='code'>
<input type='hidden' name=before_update_kind value='".$update_kind."'>
<input type='hidden' name=update_kind value='".$update_kind."'>
<input type='hidden' name='group_by' value='".$group_by."'>
<input type='hidden' name='start' value='".$start."'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
<col width='20%'>
<col width='70%'>
<col width='10%'>
<tr height=30 >
	<td>	
		<input type='checkbox' name='distinct' id='distinct' value='distinct' onclick=\"reloadView('complete')\" ".($_COOKIE[distinct] == 1 ? "checked":"")." > 중복 ID제외 (최신수정 내역만 노출)
	</td>
	<td align=right>
	";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
		$Contents .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
		<a href='excel_config.php?".$QUERY_STRING."&info_type=edit_history&excel_type=member_edit_history_excel' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
	}else{
		$Contents .= "
		<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
	}

	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
		//$Contents .= " <a href='member_edit_history.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		$Contents .= "<a href='javascript:ig_excel_dn_chk(\"member_edit_history.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."\");'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}else{
		$Contents .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}

$Contents .= "
	</td>
	<td align=right>
	목록수 : <select name='max' id='max'>
				<option value='5' ".($_COOKIE[max_limit] == '5'?'selected':'').">5</option>
				<option value='10' ".($_COOKIE[max_limit] == '10'?'selected':'').">10</option>
				<option value='20' ".($_COOKIE[max_limit] == '20'?'selected':'').">20</option>
				<option value='30' ".($_COOKIE[max_limit] == '30'?'selected':'').">30</option>
				<option value='50' ".($_COOKIE[max_limit] == '50'?'selected':'').">50</option>
				<option value='100' ".($_COOKIE[max_limit] == '100'?'selected':'').">100</option>
			</select>
	</td>
</tr>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<col width='3%'>
	<col width='5%'>
	<col width='8%'>
	<col width='8%'>
	<col width='8%'>
	<col width='8%'>
	<col width='8%'>
	<col width='8%'>
	<col width='29%'>
	<col width='8%'>
	<col width='7%'>
<tr height='27' bgcolor='#ffffff'>
	<td align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
	<td align='center' class='m_td'><font color='#000000'><b>순번</b></font></td>
	<td align='center' class='m_td'><font color='#000000'><b>정보수정일</b></font></td>
	<td align='center' class='m_td' nowrap><font color='#000000'><b>그룹</b></font></td>
	<td align='center' class='m_td' nowrap><font color='#000000'><b>회원구분</b></font></td>
	<td align='center' class='m_td' nowrap><font color='#000000'><b>회원타입</b></font></td>
	<td align='center' class=m_td><font color='#000000'><b>이름</b></font></td>
	<td align='center' class=m_td><font color='#000000'><b>아이디</b></font></td>
	<td align='center' class=m_td><font color='#000000'><b>수정내역</b></font></td>
	<td align='center' class=m_td><font color='#000000'><b>수정자명</b></font></td>
	<td align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
</tr>";

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);
		
		$edit_date = $db->dt[edit_date];
		$regdate = $db->dt[regdate];

		$sql = "select
				column_text
				from
					common_member_edit_history 
				where
					code  = '".$db->dt[code]."'
					and edit_date = '".$edit_date."'
					and regdate = '".$regdate."'";

		$mdb->query($sql);
		$history_text_array = $mdb->fetchall();

		for($j=0;$j<count($history_text_array);$j++){
			if($j == count($history_text_array)-1){
				$history_text .= $history_text_array[$j][column_text];
			}else{
				$history_text .= $history_text_array[$j][column_text].", ";
			}
		}
	
		$no = $total - ($page - 1) * $max - $i;

		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT gp_name FROM ".TBL_SHOP_GROUPINFO." WHERE gp_ix = '".$db->dt[gp_ix]."'  ");
		}else{
			$mdb->query("SELECT gp_name FROM ".TBL_SHOP_GROUPINFO." WHERE gp_ix = '".$db->dt[gp_ix]."'  ");
		}

		$mdb->fetch(0);
		$gp_name = $mdb->dt[gp_name];

		if($db->dt[is_id_auth] != "Y"){
			$is_id_auth = "미인증";
		}else{
			$is_id_auth = "";
		}

		switch($db->dt[mem_type]){

		case "M":
			$mem_type = "일반회원";
			break;
		case "C":
			$mem_type = "사업자회원";
			break;
		case "A":
			$mem_type = "직원(관리자)";
			break;
		}

		switch($db->dt[mem_div]){
			case "MD":
			$mem_div = "MD담당자";
			break;
			case "S":
			$mem_div = "셀러";
			break;
			case "D":
			$mem_div = "기타";
			break;
		}
		
		$Contents = $Contents."
		<tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
			<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."' onClick='input_check_num()'></td>
			<td class='list_box_td' >".$no."</td>
			<td class='list_box_td' style='padding:0px 5px;' nowrap><span title='".$db->dt[organization_name]."'>".$db->dt[edit_date2]."</span></td>
			<td class='list_box_td' nowrap>".$gp_name."</td>
			<td class='list_box_td' nowrap>".$mem_type."</td>
			<td class='list_box_td' nowrap>".$mem_div."</td>
			<td class='list_box_td' >".wel_masking_seLen($db->dt[name], 1, 1)."</td>
			<td class='list_box_td point' nowrap><a href=\"javascript:PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\" style='cursor:pointer' >".Black_list_check($db->dt[code],$db->dt[id])."</td>
			<td class='list_box_td' ><font color=red> ".$history_text."</font></td>
			<td class='list_box_td' >".wel_masking_seLen($db->dt[chager_name], 1, 1)."</td>";
			$Contents .= "
			<td class='list_box_td ctr'  style='padding:5px;' nowrap>";
			if($update_auth){
				$Contents .= "<img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_view.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_view')\" style='cursor:pointer;' alt='고객상담' title='고객상담'/> ";
			}else{
				$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle alt='고객상담' title='고객상담' ></a> ";
			}
			$Contents .= "
			</td>
		</tr>";
		unset($history_text);
	}

if (!$db->total){
$Contents = $Contents."
<tr height=50>
	<td colspan='11' align='center'>등록된 데이타가 없습니다.</td>
</tr>";
}

$Contents .= "
</table>
<table width=100% border='0'>
<tr>
	<!--<td align='left'><input type='checkbox' value='' id='check_member_list'> 선택수정 히스토리 <input type='button' value='삭제'>-->
	<td align='right'>".$str_page_bar."</td>
</tr>
</table>";

/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원에게 SMS 또는 메일을 보내실려면 보내고자 하는 회원을 선택하신후 '선택회원 SMS 보내기' 버튼을 클릭하신후 메일을 보내실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원정보를 백업하기 위해서는 회원정보 검색후 엑셀로 저장 버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색기능을 통해서 조건에 맞는 회원을 빠르게 검색하실수 있습니다</td></tr>
</table>
";*/

$help_text = "
<div id='batch_update_reserve' ".(($update_kind == "reserve" || $update_kind == "") ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>마일리지 일괄변경</b> <span class=small style='color:gray'><!--적립금 금액 및 내용을 입력후 저장 버튼을 클릭해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' )."</span></div>
<table cellpadding=3 cellspacing=0 width=100% class='input_table_box'>
	<col width=170>
	<col width=*>
	<tr>
		<td class='input_box_title'> <b>마일리지 상태</b></td>
		<td class='input_box_item'>
			<select name='state' id='state'>
				<!--<option value=0>대기</option>
				<option value=9>취소</option>-->
				<option value=1>완료(+)</option>
				<option value=2>사용(-)</option>
			</select>
		</td>
	</tr>
	<!--tr>
		<td class='input_box_title'> <b>사용구분</b></td>
		<td class='input_box_item'>
			<select name='use_state' id='use_state1'>
				<option value=1>상품구매</option>
				<option value=2>주문취소</option>
				<option value=3>주문반품</option>
				<option value=4>마케팅</option>
				<option value=5>키타</option>
			</select>
			<select name='use_state' id='use_state2' style='display:none'  disabled='true'>
				<option value=20>상품구매사용</option>
				<option value=21>적립소멸</option>
				<option value=24>주문반품</option>
				<option value=22>기타</option>
			</select>
		</td>
	</tr-->

	<tr>
		<td class='input_box_title'> <b>마일리지 적립액 / 사용액</b></td>
		<td class='input_box_item'> <input type=text name='reserve'  class=textbox value='' onkeydown='onlyEditableNumber(this)' onkeyup='onlyEditableNumber(this)'  style='width:80px' com_numeric=true dir='rtl'> <span class='small blu'></span></td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>마일리지 적립내용</b></td>
		<td class='input_box_item'> <input type=text name='etc'  class=textbox value='' style='width:250px' ></td>
	</tr>
</table>
<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
	<tr height=50>
		<td colspan=4 align=center>";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			$help_text .= "
			<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
		}else{
			$help_text .= "
			<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
		}
		$help_text .= "
		</td>
	</tr>
</table>
</div>

<div id='batch_update_group' ".($update_kind == "group" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>회원그룹 일괄변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 회원그룹 선택후 저장 버튼을 클릭해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C' )."</span></div>
<table cellpadding=3 cellspacing=0 width=100% style='border:1px solid #e2e2e2;'>
	<col width=200>
	<col width=*>
	<tr>
		<td bgcolor='#efefef'>
			 <b>회원그룹</b>
			<input type='checkbox' name='mem_group_use' id='bir' value='1' onclick=\"if(this.checked){\$('#update_gp_ix').removeAttr('disabled');}else{\$('#update_gp_ix').attr('disabled','disabled');}\">
		</td>
		<td >".makeGroupSelectBox($mdb,"update_gp_ix",$update_gp_ix, " disabled")." <span class=small style='color:gray'><!--회원그룹 변경에 따라 회원등급이 자동 변경됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D' )."</span></td></tr>
	<!--tr height=1><td colspan=4 class='dot-x'></td></tr>
	<tr>
		<td bgcolor='#efefef'> <b>회원등급</b><input type='checkbox' name='mem_level_use' id='bir' value='1' onclick=\"if(this.checked){\$('update_gp_level').disabled = false;}else{\$('update_gp_level').disabled = true;}\"></td>
		<td>
		".makeGroupLevelSelectBox($mdb,"update_gp_level",$update_gp_level, " disabled")."
		</td>
	</tr-->
</table>
<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
	<tr height=30><td colspan=4 align=left><span class=small style='color:gray'><!--회원그룹 변경시 회원 등급이 자동으로 변경됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E' )."</span></td></tr>
	<tr height=50>
		<td colspan=4 align=center>";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			$help_text .= "
			<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
		}else{
			$help_text .= "
			<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
		}
		$help_text .= "
		</td>
	</tr>
</table>
</div>
<div id='batch_update_sms' ".($update_kind == "sms" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>sms 일괄발송</b> <span class=small style='color:gray'><!--검색/선택된 회원에게 SMS 를 발송합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F' )."</span></div>
<table cellpadding=0 cellspacing=0>
	<col width='200px;'>
	<col width='200px;'>
	<tr>
		<td style='vertical-align:top;'>
			<table class='box_shadow' style='width:139px;height:120px;table-layout:fixed;' >
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05'  valign=top style='padding:5px 7px 5px 7px'>
						<table cellpadding=0 cellspacing=0><!--CheckSpecialChar(this);-->
						<tr><td align=left>mallstory sms </td></tr>
						<tr><td><textarea style='width:106px;height:100px;background-color:#efefef;border:1px solid #e6e6e6;padding:2px;overflow:hidden;' name='sms_text' onkeyup=\"fc_chk_byte(this,80, this.form.sms_text_count);\" ></textarea></td></tr>
						<tr><td height=20 align=right><input type=text name='sms_text_count' style='display:inline;border:0px;text-align:right' size=3 value=0> / 80 byte </td></tr>
						</table>
					</td>
					<th class='box_06'></th>
				</tr>
				<tr>
					<th class='box_07'></th>
					<td class='box_08'></td>
					<th class='box_09'></th>
				</tr>
			</table>
		</td>
	<!--/tr>
	<tr-->";
$cominfo = getcominfo();

$help_text .= "
		<td valign=top style='padding:0 0 0 10px'>
			<table cellpadding=0 cellspacing=0 ><input type=hidden name='sms_send_page' value='1'>
				<tr height=26>
					<td align=left width=90 class=small>보내는사람 : </td>
					<td><input type=text name='send_phone' class=textbox style='display:inline;' size=12 value='".$cominfo[com_phone]."'></td>
				</tr>
				<tr height=22><td align=left class=small>SMS 잔여건수 : </td><td>".$sms_design->getSMSAbleCount($admininfo)." 건 </td></tr>
				<tr height=22><td align=left class=small>발송수/발송대상 : </td><td><b id='sended_sms_cnt' class=blu>0</b> 건 / <b id='remainder_sms_cnt'>$total</b> 명</td></tr>
				<tr height=22>
						<td align=left class=small>발송수량(1회) : </td>
						<td>
						<select name=max>
							<option value='5' >5</option>
							<option value='10'  >10</option>
							<option value='20' >20</option>
							<option value='50' >50</option>
							<option value='100' selected>100</option>
							<option value='200' >200</option>
							<option value='300' >300</option>
							<option value='400' >400</option>
							<option value='500' >500</option>
							<option value='1000' >1000</option>
						</select>
						</td>
				</tr>
				<tr height=22><td align=left class=small>일시정지 : </td><td><input type='checkbox' name='stop' ></td></tr>
				<tr height=50>
					<td align=center colspan=2>";
						if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
							$help_text .= "
							<input type=image src='../images/".$admininfo["language"]."/btn_send.gif' border=0>";
						}else{
							$help_text .= "
							<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_send.gif' border=0></a>";
						}
						$help_text .= "
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<div id='batch_update_coupon' ".($update_kind == "coupon" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>쿠폰 일괄지급</b> <span class=small style='color:gray'><!--지급 하시고자하는 쿠폰을 선택해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'G' )."</span></div>
<table cellpadding=3 cellspacing=0 width=100% class='input_table_box'>
	<col width=170>
	<col width=*>
	<tr height=30>
		<td class='input_box_title'>
			 <b>발행쿠폰 목록</b>
			<input type='checkbox' name='mem_group_use' id='bir' value='1' onclick=\"if(this.checked){\$('#publish_ix').removeAttr('disabled');}else{\$('#publish_ix').attr('disabled','disabled');}\">
		</td>
		<td class='input_box_item'>".CouponPublishSelectBox($mdb,"publish_ix", " disabled")." <span class=small style='color:gray'><!--기 발행된 쿠폰 목록입니다. -->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'H' )."</span></td></tr>
</table>
<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
	<tr height=30><td colspan=4 align=left><span class=small style='color:gray'><!--선택된 회원에게 쿠폰이 발급됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'I' )."</span></td></tr>
	<tr height=50>
		<td colspan=4 align=center>";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			$help_text .= "
			<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
		}else{
			$help_text .= "
			<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
		}
		$help_text .= "
		</td>
	</tr>
</table>
</div>
<div id='batch_update_sendemail' ".($update_kind == "sendemail" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0;'><img src='../images/dot_org.gif' align=absmiddle> <b>email 일괄발송</b> <span class=small style='color:gray'><!--검색/선택된 회원에게 email 을 발송합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'J' )."</span></div>
<table cellpadding=3 cellspacing=0 width=100% class='input_table_box'>
	<col width=15%>
	<col width=35%>
	<col width=15%>
	<col width=35%>
	<tr>
		<td class='input_box_title'> <b>이메일 제목</b></td>
		<td class='input_box_item' colspan=3>
			<table cellpadding=0>
				<tr>
					<td><input type=text name='email_subject' id='email_subject_text'   class=textbox value=''  style='width:250px;height:21px;padding:0px;margin:0px;' ></td>
					<td>
					<input type='radio' name='email_type' id='email_type_new' value='new' ".($email_type == "new" || $email_type == "" ? "checked":"")." onclick=\"LoadEmail('new');\"><label for='email_type_new'>새로작성</label>
					<input type='radio' name='email_type' id='email_type_box' value='box' ".CompareReturnValue("box",$update_kind,"checked")." onclick=\"LoadEmail('box');\"><label for='email_type_box'>기존이메일선택</label>
					</td>
				</tr>
				<tr>
					<td colspan=2 id='email_select_area' style='display:none;'>
					".getMailList("","","display:inline;width:250px;")."
					</td>
				</tr>
			</table>
			<!--
			<input type=text name='email_subject' id='email_subject_text'   class=textbox value=''  style='width:250px' >  <span class='small blu'></span>
			<select name='email_subject_select' id='email_subject_select' style='display:none;width:250px;'>
				<option value=''>이메일을 선택해주세요</option>
			</select>
			<input type='radio' name='email_type' id='email_type_new' value='new' ".($email_type == "new" || $email_type == "" ? "checked":"")." onclick=\"LoadEmail('new');\"><label for='email_type_new'>새로작성</label>
			<input type='radio' name='email_type' id='email_type_box' value='box' ".CompareReturnValue("box",$update_kind,"checked")." onclick=\"LoadEmail('box');\"><label for='email_type_box'>기존이메일선택</label>
			-->
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>참조</b></td>
		<td class='input_box_item' colspan=3>
			<input type=text name='mail_cc'  class=textbox value='' style='width:350px' > <span class='small blu'><!--콤마(,) 구분으로 이메일을 입력해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'K' )."</span>
		</td>
	</tr>
	<tr height=22><input type=hidden name='email_send_page' value='1'>
		<td class='input_box_title'> <b>발송수/발송대상 </b> </td>
		<td class='input_box_item'><b id='sended_email_cnt' class=blu>0</b> 건 / <b id='remainder_email_cnt'>$total</b> 명</td>
		<td class='input_box_title'> <b>발송수량(1회) </b> </td>
		<td class='input_box_item'>
			<select name=email_max>
				<option value='5' >5</option>
				<option value='10'  >10</option>
				<option value='20' >20</option>
				<option value='50' >50</option>
				<option value='100' selected>100</option>
				<option value='200' >200</option>
				<option value='300' >300</option>
				<option value='400' >400</option>
				<option value='500' >500</option>
				<option value='1000' >1000</option>
			</select>
		</td>
	</tr>
	<tr height=22>
		<td class='input_box_title'> <b>일시정지 </b> </td>
		<td class='input_box_item' colspan=3><input type='checkbox' name='email_stop' id='email_stop'><label for='email_stop'>정지</label></td>
	</tr>
	<tr>
		<td class='input_box_item' style='padding:0px;' colspan=4>".WebEdit()."<input type='hidden' name='mail_content' value=''></td>
	</tr>
</table>
<table cellpadding=5 cellspacing=1 width=100% >
	<tr bgcolor=#ffffff>
		<td colspan=2 align=right valign=top style='padding:0px;padding-right:20px;'>
		<a href='javascript:doToggleText();' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
	<a href='javascript:doToggleHtml();' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
		</td>
	</tr>
</table>
<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
	<tr height=50>
		<td colspan=4 align=center>
			<input type=checkbox name='save_mail' id='save_mail' value='1' align=absmiddle>
			<label for='save_mail'>메일함에 저장하기</label>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$help_text .= "
				<input type=image src='../images/".$admininfo["language"]."/btn_send.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle >";
			}else{
				$help_text .= "
				<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_send.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle ></a>";
			}
			$help_text .= "
		</td>
	</tr>
</table>
</div>
";

$select = "
<nobr>
<select name='update_type' onChange='view_member_num(this,\"$total\")'>
<!--	<option value='1'>검색한 회원 전체에게</option>-->
	<option value='2'>선택한회원 전체에게</option>
</select>
<input type='radio' name='update_kind' id='update_kind_group' value='group' ".(($update_kind == "group" || $update_kind == "") ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_group');\"><label for='update_kind_group'>회원그룹 일괄변경</label>

<input type='radio' name='update_kind' id='update_kind_sms' value='sms'  ".(($update_kind == "sms" ) ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_sms');\"><label for='update_kind_sms'>SMS 일괄발송</label>
<input type='radio' name='update_kind' id='update_kind_sendemail' value='sendemail' ".(($update_kind == "sendemail" ) ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_sendemail');\"><label for='update_kind_sendemail'>이메일 일괄발송</label>";
if($admininfo[mall_type] != "H"){
	$select .= "
<input type='radio' name='update_kind' id='update_kind_coupon' value='coupon'  ".(($update_kind == "coupon" ) ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_coupon');\"><label for='update_kind_coupon'>쿠폰 일괄지급</label>
<input type='radio' name='update_kind' id='update_kind_reserve' value='reserve' ".(($update_kind == "reserve" ) ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_reserve');\"><label for='update_kind_reserve'>적립금 일괄지급</label>";
}
$select .= "
</nobr>";

if($admininfo[mall_type] == "H"){
	$Contents .= "".HelpBox($select, $help_text, 520)."</form>";
}else{
	$Contents .= "".HelpBox($select, $help_text, 750)."</form>";
}

$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";

$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>\n".$Script;
$P->OnloadFunction = "";//init();
$P->strLeftMenu = member_menu();
$P->Navigation = "회원관리 > 회원정보 수정 리스트";
$P->title = "회원정보 수정 리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();


//	웰숲 우클릭 방지
include_once("../order/wel_drag.php");
?>



<script type="text/javascript">
	//	wel_ 새벽시간(23시~07시)이나 휴무일 등 업무시간 외 다운로드시 검수 member_excel2003
	function ig_excel_dn_chk(s_val_Data) {
		//console.log(s_val_Data);
		var ig_now = new Date();   //현재시간
		var ig_hour = ig_now.getHours();   //현재 시간 중 시간.




			//	새벽시간(23시~07시), 휴무일(일, 토)
		//if(Number(ig_hour) >= "23" || Number(ig_hour) <= "7" || Number(ig_now.getDay()) == "0" || Number(ig_now.getDay()) == "6") {
			var ig_inputString = prompt('사유를 간략하게 입력하세요.\r\n(20자 이내(띄어쓰기포함), 특수문자 제외)');

			if(ig_inputString != null && ig_inputString.trim() != "") {
				//	엑셀다운로드 진행

					var str_length = ig_inputString.length;		// 전체길이

					if(str_length > "20") {
						alert("사유가 20자 이상 입니다.");
						return false;
					} else {
						var ig_inputString_PW = prompt('파일 비밀번호를 지정해 주세요.\r\n(15자 이하, 영문/숫자만 사용, 공백사용금지)');

							if(ig_inputString_PW != null && ig_inputString_PW.trim() != "") {

								var str_PW_length = ig_inputString_PW.length;		// 전체길이

								if(str_PW_length > "15") {
									alert("비밀번호를 15자 이하로 해주세요.");
									return false;
								} else {
									location.href = s_val_Data+"&irs="+ig_inputString+"&ipw="+ig_inputString_PW;
								}

							} else {
								alert("비밀번호를 입력해 주세요.");
								return false;
							}
					}


			} else {
				alert("사유를 입력하세요");
				return false;
			}
		/*} else {
			//	일반 업무때 다운로드
			var ig_inputString_PW = prompt('파일 비밀번호를 지정해 주세요.\r\n(15자 이하, 영문/숫자만 사용, 공백사용금지)');

				if(ig_inputString_PW != null && ig_inputString_PW.trim() != "") {

					var str_PW_length = ig_inputString_PW.length;		// 전체길이

					if(str_PW_length > "15") {
						alert("비밀번호를 15자 이하로 해주세요.");
						return false;
					} else {
						location.href = s_val_Data+"&ipw="+ig_inputString_PW;
					}

				} else {
					alert("비밀번호를 입력해 주세요.");
					return false;
				}
		}*/



	}
</script>