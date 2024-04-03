<?
/*
구매후기를 작성한 회원에게 적립금을 수동으로 주기 위해 회원의 아이디로 검색할 수 있도록 추가함 kbk 13/06/05

수정 : company_id -> cu.company_id 로 수정 bgh 2013-06-12
*/
include("../class/layout.class");
//include($_SERVER["DOCUMENT_ROOT"]."/class/bbs.lib.php");
//include_once($_SERVER["DOCUMENT_ROOT"]."/include/sns.config.php");
if($admininfo[mall_type] == "H"){
	header("Location:./contactus_info.php");
}
include("../logstory/class/sharedmemory.class");

$db = new Database;
if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
}

if(empty($valuation_s) && empty($valuation_e)){
	$valuation_s = 0;
	$valuation_e = 5;
}

$shmop = new Shared("use_after");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$datas = $shmop->getObjectForKey("use_after");
$datas = unserialize(urldecode($datas));

$Script = "	<script language='javascript'>


		function showObj(id)
		{
			obj = eval(id+'.style');
			obj.display = 'block';

			document.lyrstat.opend.value = id;
		}

		function hideObj(id)
		{
			obj = eval(id+'.style');
			obj.display = 'none';

			document.lyrstat.opend.value = '';
		}

		function swapObj(id)
		{

			obj = eval(id+'.style');
			stats = obj.display;

			if (stats == 'none')
			{
				if (document.lyrstat.opend.value)
					hideObj(document.lyrstat.opend.value);

				showObj(id);
			}
			else
			{
				hideObj(id);
			}
		}

		function useAfterDelete(bbs_ix){
			if(confirm('해당 상품후기를 삭제하시겠습니까?')){
				window.frames['act'].location.href='useafter.act.php?act=delete&bbs_ix='+bbs_ix
			}
		}

		function useAfterModify(uf_ix){
			if(confirm(language_data['useafter.list.php']['A'][language])){
			//'사용후기를 정말로 삭제하시겠습니까? '
				window.frames['act'].location.href='useafter.act.php?act=delete&uf_ix='+uf_ix
				//document.getElementById('iframe_act').src='useafter.act.php?act=delete&uf_ix='+uf_ix;
			}
		}

		function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		frm.FromYY.disabled = false;
		frm.FromMM.disabled = false;
		frm.FromDD.disabled = false;
		frm.ToYY.disabled = false;
		frm.ToMM.disabled = false;
		frm.ToDD.disabled = false;
	}else{
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;
	}
}


function init(){

	var frm = document.searchmember;
	onLoad('$sDate','$eDate');
	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;


}

function init2(){

	var frm = document.searchmember;
	onLoad('$sDate','$eDate');


}

function init_date(FromDate,ToDate) {
	var frm = document.searchmember;


	for(i=0; i<frm.FromYY.length; i++) {
		if(frm.FromYY.options[i].value == FromDate.substring(0,4))
			frm.FromYY.options[i].selected=true
	}
	for(i=0; i<frm.FromMM.length; i++) {
		if(frm.FromMM.options[i].value == FromDate.substring(5,7))
			frm.FromMM.options[i].selected=true
	}
	for(i=0; i<frm.FromDD.length; i++) {
		if(frm.FromDD.options[i].value == FromDate.substring(8,10))
			frm.FromDD.options[i].selected=true
	}


	for(i=0; i<frm.ToYY.length; i++) {
		if(frm.ToYY.options[i].value == ToDate.substring(0,4))
			frm.ToYY.options[i].selected=true
	}
	for(i=0; i<frm.ToMM.length; i++) {
		if(frm.ToMM.options[i].value == ToDate.substring(5,7))
			frm.ToMM.options[i].selected=true
	}
	for(i=0; i<frm.ToDD.length; i++) {
		if(frm.ToDD.options[i].value == ToDate.substring(8,10))
			frm.ToDD.options[i].selected=true
	}
}



function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	if(dType == 1){
		for(i=0; i<frm.FromYY.length; i++) {
			if(frm.FromYY.options[i].value == FromDate.substring(0,4))
				frm.FromYY.options[i].selected=true
		}
		for(i=0; i<frm.FromMM.length; i++) {
			if(frm.FromMM.options[i].value == FromDate.substring(5,7))
				frm.FromMM.options[i].selected=true
		}
		for(i=0; i<frm.FromDD.length; i++) {
			if(frm.FromDD.options[i].value == FromDate.substring(8,10))
				frm.FromDD.options[i].selected=true
		}


		for(i=0; i<frm.ToYY.length; i++) {
			if(frm.ToYY.options[i].value == ToDate.substring(0,4))
				frm.ToYY.options[i].selected=true
		}
		for(i=0; i<frm.ToMM.length; i++) {
			if(frm.ToMM.options[i].value == ToDate.substring(5,7))
				frm.ToMM.options[i].selected=true
		}
		for(i=0; i<frm.ToDD.length; i++) {
			if(frm.ToDD.options[i].value == ToDate.substring(8,10))
				frm.ToDD.options[i].selected=true
		}
	}

}




function onLoad(FromDate, ToDate) {
	var frm = document.searchmember;

	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);

	init_date(FromDate,ToDate);

}
		</script>";
/*
<tr>
	    <td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 사업자 정보</b></div>")."</td>
	  </tr>
*/

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
$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left'> ".GetTitleNavigation("상품 후기 관리", "고객센타 > 상품 후기 관리 ")."</td>
		</tr>
		<tr>
			<td>
				<form name='searchmember'>
    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
		<tr>
		<td style='width:100%;' valign=top colspan=3>
			<table width=100%  border=0>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef' width='90%'><img src='../images/dot_org.gif' align=absmiddle> <b>상품 후기 검색하기</b></td>";
					if(strpos($_SESSION['admininfo']['admin_id'], 'forbiz') !== false) {
                        $mstring .= "
					<td style='border-bottom:2px solid #efefef;' width='10%'>
						<a href='./cscenter.manage.php?page_type=use_after'><input type='button' value='후기 게시판 설정' style='margin: 3px;cursor:pointer;'></a>
					</td>";
                    }
					$mstring .="
				</tr>
				<tr>
					<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
						<table class='box_shadow' style='width:100%;' align=left cellpadding='0' cellspacing='0' border='0'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'></td>
								<th class='box_03'></th>
							</tr>
							<tr>
								<th class='box_04'></th>
								<td class='box_05' valign=top>
									<TABLE cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
									<TR>
										<TD bgColor=#ffffff style='padding:0 0 0 0;'>
										<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
											<tr height=27>
												<th class='search_box_title' bgcolor='#efefef' width='150' align=center>후기분류</th>
												<td class='search_box_item'>
													<select name='bbs_div'>
														<option value=''>전체</option>
														<option value='1' ".(($bbs_div=="1")?"selected":"").">프리미엄</option>
														<option value='2' ".(($bbs_div=="2")?"selected":"").">일반</option>
													</select>
												</td>
											</tr>
											<tr height=27>
												<th class='search_box_title' bgcolor='#efefef' width='150' align=center>관리자 댓글 여부</th>
												<td class='search_box_item'>
													<input type='radio' name='cmt' value='' id='cmt' ".(($cmt=="")?"checked":"")." /> <label for='cmt'>전체</label>
													<input type='radio' name='cmt' value='1' id='cmt1' ".(($cmt=="1")?"checked":"")." /> <label for='cmt1'>작성완료</label>
													<input type='radio' name='cmt' value='2' id='cmt2' ".(($cmt=="2")?"checked":"")." /> <label for='cmt2'>작성대기</label>
												</td>
											</tr>
											<tr height=27>
												<th class='search_box_title' bgcolor='#efefef' width='150' align=center>이미지 첨부 여부</th>
												<td class='search_box_item'>
													<input type='radio' name='img' value='' id='img' ".(($img=="")?"checked":"")." /> <label for='img'>전체</label>
													<input type='radio' name='img' value='1' id='img1' ".(($img=="1")?"checked":"")." /> <label for='img1'>첨부</label>
													<input type='radio' name='img' value='2' id='img2' ".(($img=="2")?"checked":"")." /> <label for='img2'>미첨부</label>
												</td>
											</tr>
											 <tr height=27>
												<td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>등록일자</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".(($regdate==1)?"checked":"")."></td>
											  <td class='search_box_item' align=left style='padding-left:5px;'>
												<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff >
												<tr>
													<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY style='width:60px;'></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM style='width:45px;'></SELECT> 월 <SELECT name=FromDD style='width:45px;'></SELECT> 일 </TD>
													<TD style='padding:0 5px;' align=left> ~ </TD>
													<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY style='width:60px;'></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM style='width:45px;'></SELECT> 월 <SELECT name=ToDD style='width:45px;'></SELECT> 일</TD>
													<TD style='padding-left:10px;'>
														<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
														<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
														<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
														<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
														<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
													</TD>
												</tr>
											</table>
											  </td>
											</tr>
											<tr height='27'>
												<th class='search_box_title'align=center>평균평점 </th>
												<td class='search_box_item'>
													<select name='valuation_s'>
														<option value='0' ".(empty($valuation_s) ? "selected" : "").">0 이상</option>
														<option value='1' ".($valuation_s == 1 ? "selected" : "").">1.0 이상</option>
														<option value='2' ".($valuation_s == 2 ? "selected" : "").">2.0 이상</option>
														<option value='3' ".($valuation_s == 3 ? "selected" : "").">3.0 이상</option>
														<option value='4' ".($valuation_s == 4 ? "selected" : "").">4.0 이상</option>
														<option value='5' ".($valuation_s == 5 ? "selected" : "").">5.0 이상</option>
													</select>
													~
													<select name='valuation_e'>
														<option value='0' ".(empty($valuation_e) ? "selected" : "").">0 이하</option>
														<option value='1' ".($valuation_e == 1 ? "selected" : "").">1.0 이하</option>
														<option value='2' ".($valuation_e == 2 ? "selected" : "").">2.0 이하</option>
														<option value='3' ".($valuation_e == 3 ? "selected" : "").">3.0 이하</option>
														<option value='4' ".($valuation_e == 4 ? "selected" : "").">4.0 이하</option>
														<option value='5' ".($valuation_e == 5 ? "selected" : "").">5.0 이하</option>
													</select>
												</td>
											</tr>
											<tr height=27>
												<th class='search_box_title' bgcolor='#efefef' width='150' align=center>조건검색 </th>
												<td class='search_box_item'>
												<table cellpaddig='0' cellspacing='0' border='0' width='100%'>
												<tr>
													<td width='100'>
													<select name=search_type>
														<option value='pname' ".CompareReturnValue("pname",$search_type,"selected").">상품명</option>
														<option value='bbs_name' ".CompareReturnValue("bbs_name",$search_type,"selected").">작성자명</option>
														<option value='bbs_contents' ".CompareReturnValue("bbs_contents",$search_type,"selected").">내용</option>
													</select>
													</td>
													<td width='210'><input type=text class=textbox name='search_text' value='".$search_text."' style='width:200px;height:15px;' ></td>
													<td width='*'></td>
													</tr>
													</table>
												</td>
											</tr>
										</table>
										</TD>
									</TR>

									</TABLE>
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
				<tr >
					<td colspan=3 align=center  style='padding:10px 0 20px 0'>
						<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>
	</form>
	</td>
	</tr>
	<tr>
			<td style='clear:both;'>
			".PrintUseAfter()."
			</td>
		</tr>
		";
$mstring .="</table>";

$Contents = $mstring;
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >고객이 남긴 제품 사용후기 목록입니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >원치 않은 후기는 삭제하실수 있습니다</td></tr>";*/

	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


if($admininfo[admin_level] == 8){
$help_text .= "
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >입점업체 관리자는 귀사의 제품에 대한 후기만 보실수 있습니다</td></tr>";
}
$help_text .= "
</table>
";

//$Contents .= HelpBox("사용후기 관리", $help_text);

//$Script = "<script language='javascript' src='basicinfo.js'></script>";
$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
if($regdate!=1) $P->OnloadFunction = "init();";
else $P->OnloadFunction = "init2();";
$P->strLeftMenu = cscenter_menu();
$P->Navigation = "고객센타 > 상품 후기 관리";
$P->title = "상품 후기 관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintUseAfter(){
	global $admininfo, $admin_config, $DOCUMENT_ROOT,$nset,$page,$uf_valuation,$search_type,$search_text,$_GET,$product_type_txt,$auth_update_msg,$auth_delete_msg,$image_hosting_type,$product_image_column_str;
	global $bbs_div, $valuation_s, $valuation_e, $cmt, $img, $datas;

	$mdb = new Database;
	$where = array();

	if($search_type != "" && $search_text != ""){
		$where[] = $search_type." LIKE '%$search_text%' ";
	}

	$startDate = $_GET["FromYY"].$_GET["FromMM"].$_GET["FromDD"];
	$endDate = $_GET["ToYY"].$_GET["ToMM"].$_GET["ToDD"];

	if($startDate != "" && $endDate != ""){
		$startDate = $_GET["FromYY"]."-".$_GET["FromMM"]."-".$_GET["FromDD"];
		$endDate = $_GET["ToYY"]."-".$_GET["ToMM"]."-".$_GET["ToDD"];

		$where[] = " b.regdate between  '$startDate 00:00:00' and '$endDate 23:59:59' ";
	}

	if($bbs_div){
		$where[] = " bbs_div='".$bbs_div."' ";
	}

	if($img == 1){
		$where[] = " (bbs_file_1 !='' or bbs_file_2 !='' or bbs_file_3 !='' or bbs_file_4 !='' or bbs_file_5 !='') ";
	}else if($img == 2){
		$where[] = " (bbs_file_1='' and bbs_file_2='' and bbs_file_3='' and bbs_file_4='' and bbs_file_5='') ";
	}

	if($cmt == 1){
		$where[] = " bbs_re_cnt > 0 ";
	}else if($cmt == 2){
		$where[] = " bbs_re_cnt = 0 ";
	}

	if($datas[use_valuation_goods] == "Y" && $datas[use_valuation_delivery] == "Y"){
		$valuation_string = "((valuation_goods+valuation_delivery)/2)";
		$where[] = " ".$valuation_string." >= '".$valuation_s."' and ".$valuation_string." <= '".$valuation_e."' ";
	}else if($datas[use_valuation_goods] == "N" && $datas[use_valuation_delivery] == "N"){
		$valuation_string = "0";
	}else{
		if($datas[use_valuation_goods] == "Y"){
			$valuation_string = "valuation_goods";
		}else if($datas[use_valuation_delivery] == "Y"){
			$valuation_string = "valuation_delivery";
		}

		$where[] = " ".$valuation_string." >= '".$valuation_s."' and ".$valuation_string." <= '".$valuation_e."' ";
	}

	if(! empty($where)){
		$where_str = "where ".implode(" and ", $where);
	}

	$sql = "select COUNT(*) as total from shop_product_after b $where_str ";//회원 아이디 노출을 위해 common_user 테이블 조인 추가 kbk 13/06/05
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];
	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$mString ="<form name='form5' action='useafter.act.php' method='post' target='act'><input type='hidden' name='act' value='update'>";
	$mString .= "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver border='0' style='padding:0px;margin:0px;' class='list_table_box'>";
	$mString .= "<tr bgcolor=#efefef style='font-weight:600;'>
		<td class=s_td width='4%' height=27 align='center'>번호</td>
		<td class=m_td width='7%' align='center'>분류</td>
		<td class=m_td width='20%' align='center'>상품명</td>
		<td class=m_td width='10%' align='center'>평균평점</td>
		<td class=m_td width='*' align='center'>내용</td>
		<td class=m_td width='7%' align='center'>관리자</br>댓글여부</td>
		<td class='m_td' width='5%' align='center'>작성자</td>
		<td class='m_td' width='10%' align='center'>등록일</td>
		<td class=e_td width='10%' align='center'>관리</td>
		</tr>
		";
	if ($total == 0){
		$mString .= "<tr bgcolor=#ffffff height=50><td class='list_box_td' colspan=9 align=center>상품 후기 내역이 존재 하지 않습니다.</td></tr>";
	}else{
		$sql = "select b.*, ".$valuation_string." as avg from shop_product_after b $where_str order by  b.regdate desc limit $start, $max";//회원 아이디 노출을 위해 common_user 테이블 조인 추가 kbk 13/06/05
		$mdb->query($sql);

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			$no = $total - ($page - 1) * $max - $i;

			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[pid], "s", $mdb->dt)) || $image_hosting_type=='ftp'){
				$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[pid], "s", $mdb->dt);
			}else{
				$img_str = "../image/no_img.gif";
			}
			
			$file1 = "";
			if($mdb->dt[bbs_file_1] != "" && file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/product_after/".(int)$mdb->dt[bbs_ix]."/".$mdb->dt[bbs_file_1])){
				$file1 = "<img src='".$admin_config[mall_data_root]."/product_after/".(int)$mdb->dt[bbs_ix]."/".$mdb->dt[bbs_file_1]."' style='border:1px solid silver' width=50 height=50 align=left>";
			}else{
				if($mdb->dt[bbs_file_1] != ""){
					$file1 = "<img src='../image/no_img.gif' style='border:1px solid silver' width=50 height=50 align=left>";
				}
			}

			$mString = $mString."<tr bgcolor=#ffffff align=center height='65'>
			<input type='hidden' name='bbs_ix[]' value='".$mdb->dt[bbs_ix]."'>
			<td class='list_box_td' bgcolor='#efefef'>".$no."</td>
			<td class='list_box_td' bgcolor='#efefef'>".($mdb->dt[bbs_div] == 1 ? "프리미엄후기" : "일반후기")."</td>
			<td class='list_box_td' bgcolor='#ffffff' align=left style='padding:5px;line-height:130%'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td width='60' style='padding:2px;' rowspan=2><a href='/shop/goods_view.php?id=".$mdb->dt[pid]."' target=_blank><img src='".$img_str."' style='border:1px solid silver' width=50 height=50 align=left></a></td>
						<td width='*' align='left'><a href='/shop/goods_view.php?id=".$mdb->dt[pid]."' target=_blank><b>".$mdb->dt[pname]."</b></a>".($mdb->dt[option_name] != "" ? "</br>-".$mdb->dt[option_name]  : "")."</td>
					</tr>
				</table>
			</td>
			<td bgcolor='#ffffff'>".str_repeat("<img src='/admin/images/icon_score_01.gif'>",$mdb->dt[avg])."".str_repeat("<img src='/admin/images/icon_score_02.gif'>",5-$mdb->dt[avg])."</br>(".round($mdb->dt[avg], 2).")</td>
			<td class='list_box_td point' bgcolor='#efefef' align=left style='text-align:left;word-break:break-all;'>
				<a style='cursor:pointer;' onclick=\"javascript:PoPWindow3('useafter.detail.php?bbs_ix=".$mdb->dt[bbs_ix]."',900,800,'history')\"><div style=''>".$file1."</div>".nl2br($mdb->dt[bbs_contents])."</a>
			</td>
			<td class='list_box_td' bgcolor='#ffffff'>".($mdb->dt[bbs_re_cnt] > 0 ? "작성 완료" : "작성 대기")."</td>
			<td class='list_box_td' bgcolor='#ffffff'>".$mdb->dt[bbs_name]."</td>
			<td class='list_box_td' bgcolor='#ffffff'>".str_replace("-",".",$mdb->dt[regdate])."</td>
			<td class='list_box_td' bgcolor='#ffffff' align=center>
				<input type='button' value='수정' style='margin: 1px;' onclick=\"javascript:PoPWindow3('useafter.write.php?mmode=pop&bbs_ix=".$mdb->dt[bbs_ix]."',900,800,'useafter.write')\">
				<input type='button' value='삭제' style='margin: 1px;' onclick=\"useAfterDelete('".$mdb->dt[bbs_ix]."');\"></br>				
			</td>
			</tr>
			";
		}

	}

	if($_SERVER[QUERY_STRING] == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$_SERVER[QUERY_STRING]) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER[QUERY_STRING]) ;
	}
	$mString = $mString."
					</table>
					<table cellpadding=0 cellspacing=0 width=100%>
					<tr height=30 bgcolor=#ffffff>
						<td colspan=6 align=center style='padding:10px 0 0 0'>".page_bar($total, $page, $max,  $query_string, "")."</td>
					</tr>
					<tr height=30 bgcolor=#ffffff>
						<td colspan=6 align=right><a style='cursor:pointer;' onclick=\"javascript:PoPWindow3('useafter.write.php?mmode=pop',900,800,'useafter.write')\"><input type='button' value='상품후기 작성' style='margin: 1px;'></a></td>
					</tr>
					</table>
					</form>";

	return $mString;
}


?>
