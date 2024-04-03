<?
	include("../class/layout.class");
	
	$db = new MySQL;
	$mdb = new MySQL;
	$db2 = new MySQL;

	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
	if ($FromYY == ""){

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

	if ($vFromYY == ""){

		$sDate2 = date("Y/m/d", $before10day);
		$eDate2 = date("Y/m/d");

		$startDate2 = date("Ymd", $before10day);
		$endDate2 = date("Ymd");

	}else{

		$sDate2 = $vFromYY."/".$vFromMM."/".$vFromDD;
		$eDate2 = $vToYY."/".$vToMM."/".$vToDD;
		$startDate2 = $vFromYY.$vFromMM.$vFromDD;
		$endDate2 = $vToYY.$vToMM.$vToDD;

		}

	if ($birYY == ""){

		$sDate3 = date("Y/m/d");
		$eDate3 = date("Y/m/d");

		$startDate3 = date("Ymd");
		$endDate3 = date("Ymd");
	}else{

		$sDate3 = $birYY."/".$birMM."/".$birDD;
		$eDate3 = "none";
		$startDate3 = $birYY.$birMM.$birDD;
		$endDate3 = "none";
		$birDate = $birYY.$birMM.$birDD;
		}


	$max = 20; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}else{
	$start = ($page - 1) * $max;
		}


$Script = "	
	<script language='javascript'>

	$(document).ready(function(){
		ChangeRegistDate();

	});

	function ChangeRegistDate(frm){
		var value = $('#regdate').attr('checked');

		if(value == 'checked'){
			$('#start_datepicker').attr('disabled',false);
			$('#end_datepicker').attr('disabled',false);
		}else{
			$('#start_datepicker').attr('disabled',true);
			$('#end_datepicker').attr('disabled',true);
		}
	}

	function init(){
		var frm = document.searchmember;
		//onLoad('$sDate','$eDate');
	
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

	function onLoad(FromDate, ToDate) {
		var frm = document.searchmember;

		LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
		LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);

		init_date(FromDate,ToDate);
	}

	function go_con_del(cm_ix){
		yes = confirm('정말로 삭제하시겠습니까?');
		if(yes){
			window.frames['act'].location.href = 'apply_company_member.act.php?act=delete&mode=top&cm_ix='+cm_ix;
		}else{
			return;
		}
	}

	function approve_company(code) {
		yes = confirm('회원 승인 하시겠습니까?');
		if(yes){
		window.frames['iframe_act'].location.href = 'company_member.act.php?act=change&mode=top&code='+code;
		}else{
			return;
			}
		}

$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy/mm/dd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+0d');
		}
	}

	});

	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});

function setSelectDate(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}
</script>";

if($authorized != ""){
	$where .= " and u.authorized = '$authorized' ";
}

if($search_text != ""){
	if($search_type=="name") $where.=" and AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
	else $where .= " and $search_type LIKE '%$search_text%' ";
}

//$startDate = $_GET["FromYY"]."-".$_GET["FromMM"]."-".$_GET["FromDD"]." 00:00:00";
//$endDate = $_GET["ToYY"]."-".$_GET["ToMM"]."-".$_GET["ToDD"]." 23:59:59";

if($_GET["FromYY"] != "" && $_GET["ToYY"] != ""){
	$where .= " and  cm.regdate between  '".$startDate."' and '".$endDate."' ";
}

$sdate = str_replace("/","",$sdate);
$edate = str_replace("/","",$edate);


if($regdate == "1"){
	if($sdate != "" && $edate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(cmd.date_ , 'YYYYMMDD') between  $sdate and $edate ";
		}else{
			$where .= " and  MID(replace(cmd.date,'-',''),1,8) between  $sdate and $edate ";
		}
	}
}

//and u.authorized = 'N'
$sql = "SELECT 
			count(u.code) as total
		FROM 
			".TBL_COMMON_USER." as  u 
			inner join ".TBL_COMMON_MEMBER_DETAIL." cmd ON u.code=cmd.code
			inner join common_company_detail as ccd on (u.company_id = ccd.company_id)
			inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
		where 
			u.mem_type = 'C'
			and u.mem_div = 'S'
			
			and ccd.company_id = '".$admininfo[company_id]."'
			$where"; 

$db->query($sql);
$db->fetch();
$total = $db->dt[total];

$sql = "SELECT
		u.date,
		u.authorized,
		u.request_info,
		u.id,
		u.auth,
		u.mem_type,
		u.mem_div,
		cmd.*,
		AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
		AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
		AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel,
		AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs
	from
		".TBL_COMMON_USER." as  u 
		inner join ".TBL_COMMON_MEMBER_DETAIL." cmd ON u.code=cmd.code
		inner join common_company_detail as ccd on (u.company_id = ccd.company_id)
		inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
	where
		u.mem_type = 'C'
		and u.mem_div = 'S'
		and ccd.company_id = '".$admininfo[company_id]."'
		$where order by u.date desc limit $start , $max";

$db->query($sql);

$Contents = "
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
						<td class='box_02'  ><a href='member_status_list.php?info_type=member'>관리자 추가요청</a> </td>
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
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr>
	    <td align='left' style='padding-bottom:10px;'> ".GetTitleNavigation("리오더신청", "고객센타 > 리오더신청")."</td>
	</tr>
	<tr>
		<td>
		<form name='searchmember'>
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
			<td style='width:100%;' valign=top colspan=3>
				<table width=100%  border=0 cellpadding='0' cellspacing='0'>
					<!--tr><td style='border-bottom:2px solid #efefef' height=25><img src='../images/dot_org.gif' align=absmiddle> <b>상품Q&A 검색하기</b></td></tr-->
					<tr>
						<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
							<table class='box_shadow' style='width:100%;' align=left border=0 cellpadding='0' cellspacing='0'>
								<tr>
									<th class='box_01'></th>
									<td class='box_02'></td>
									<th class='box_03'></th>
								</tr>
								<tr>
									<th class='box_04'></th>
									<td class='box_05'  valign=top style='padding:0px'>
										<TABLE cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>
										<TR>
											<TD bgColor=#ffffff style='padding:0 0 0 0;'>
											<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
												<tr height=27>
													<th class='search_box_title' bgcolor='#efefef' width='15%' align='center'>신청처리상태 </th>
													<td class='search_box_item' width='*' align=left style='padding-left:5px;'>
														<input type='radio' id='status_a' name='auth' value='' checked ".CompareReturnValue("",$auth,"checked")."> <label for='status_a'>전체</label>
														<input type='radio' id='approve_y' name='auth' value='4' ".CompareReturnValue("Y",$auth,"checked")."> <label for='approve_y'>승인</label>
														<input type='radio' id='approve_n' name='auth' value='0' ".CompareReturnValue("N",$auth,"checked")."> <label for='approve_n'>미승인</label>
														<!--<input type='radio' id='approve_x' name='authorized' value='X' ".CompareReturnValue("X",$authorized,"checked")."> <label for='approve_x'>승인거부</label>-->
													</td>
												</tr>
												 <tr height=27>
													<th class='search_box_title' bgcolor='#efefef' width='15%' align='center'>조건검색</th>
													<td class='search_box_item' width='*' align=left style='padding-left:5px;'>
														<select name=search_type>
															<option value='name' ".CompareReturnValue("name",$search_type,"selected").">신청회원명</option>
															<option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected").">회사명</option>
															<option value='represent_name' ".CompareReturnValue("represent_name",$search_type,"selected").">담당자명</option>
														</select>
															<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:200px;'>
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
											<tr height=30>
												<td class='input_box_title' ><label for='regdate'>신청일</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.search_seller);' ".CompareReturnValue("1",$regdate,"checked")."></td>
												<td class='input_box_item'>
													<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100%>
														<col width=70>
														<col width=20>
														<col width=70>
														<col width=*>
														<tr>
															<TD nowrap>
															<input type='text' name='sdate' class='textbox point_color' value='".$sdate."' style='height:20px;width:100px;text-align:center;' id='start_datepicker'>
															<!--SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년
															<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월
															<SELECT name=FromDD></SELECT> 일 -->
															</TD>
															<TD align=center> ~ </TD>
															<TD nowrap>
															<input type='text' name='edate' class='textbox point_color' value='".$edate."' style='height:20px;width:100px;text-align:center;' id='end_datepicker'>
															<!--SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년
															<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월
															<SELECT name=ToDD></SELECT> 일 -->
															</TD>
															<TD style='padding:0px 10px'>
																<a href=\"javascript:setSelectDate('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
																<a href=\"javascript:setSelectDate('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
																<a href=\"javascript:setSelectDate('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
																<a href=\"javascript:setSelectDate('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
																<a href=\"javascript:setSelectDate('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
																<a href=\"javascript:setSelectDate('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
																<a href=\"javascript:setSelectDate('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
															</TD>
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
				</table>";
	$Contents .= "
			</td>
		</tr>
		<tr>
			<td colspan=3 align=center style='padding:30px 0 20px 0'>
				<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0></td>
		</tr>
		</table>
		</form>
		</td>
	</tr>
</table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
$Contents .= "<!--table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
	<tr>
		<td align='right' style='padding-bottom:5px;'><a href='re_order.excel.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a></td>
	</tr>
</table-->";
}

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 >
	<tr>
		<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
			<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>사용자 리스트 </b>
		</td>
	</tr>
	</table>";

$Contents .= "
<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>
<form name='list_frm' action='company.act.php' method='post' onsubmit='return SelectUpdate2(this)' enctype='multipart/form-data' target='iframe_act'>
<input type='hidden' name='code[]' id='code'>
<input type='hidden' name='act' value='seller_all_update'>
<input type='hidden' name='page_name' value='company'>

<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<col width='2%'>
	<col width='3%'>
	<col width='9%'>
	<col width='5%'>
	<col width='5%'>
	<col width='6%'>
	<col width='6%'>
	<col width='8%'>
	<col width='8%'>
	<col width='9%'>
	<col width='5%'>
	<col width='10%'>
	<col width='8%'>

	<tr bgcolor=#efefef align=center height=30 style='font-weight:600;'>
		<td align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
		<td class='s_td'>순번</td>
		<td class='m_td'>신청일</td>
		<td class='m_td'>회원구분</td>
		<td class='m_td'>회원타입</td>
		<td class='m_td'>신청인</td>
		<td class='m_td'>아이디</td>
		<td class='m_td'>전화번호</td>
		<td class='m_td'>핸드폰</td>
		<td class='m_td'>E-mail</td>
		<td class='m_td'>회원그룹</td>
		<td class='e_td'>요청관리</td>
	</tr>";

	if($db->total){
		for($i=0;$i<$db->total;$i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;
		if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/apply_company/".$db->dt["company_file"])){
			$file_name = '다운로드';
		}else{
			$file_name = '-';
		}

		if($db->dt[gp_ix]){
				$sql = "select gp_name from shop_groupinfo where gp_ix = '".$db->dt[gp_ix]."'";
				$mdb->query($sql);
				$mdb->fetch();

			$gp_name = $mdb->dt[gp_name];
		}
		
		if($db->dt[auth] == "4"){
			$request_info_text = "승인(".substr($db->dt[date],0,10).")";
		}else{
			$request_info_text = "미승인 ";
		}
				
		switch($db->dt[mem_type]){
			case "C":
				$mem_type_text = "사업자회원 ";
			break;
			case "M":
				$mem_type_text = "일반회원 ";
			break;
		}

		switch($db->dt[mem_div]){
			case "S":
				$mem_div_text = "셀러 ";
			break;
			case "MD":
				$mem_div_text = "MD관리자 ";
			break;
			case "D":
				$mem_div_text = "기타 ";
			break;
		}
		
		$sql = "select charge_code from common_seller_detail where company_id = '".$admininfo[company_id]."' and charge_code = '".$db->dt[code]."'";
		//echo nl2br($sql)."<br>";
		$db2->query($sql);
		$db2->fetch();
		if($db2->total > 0){
			$seller_master = "<img src='../images/m01.png' border='0'> ";
		}else{
			$seller_master = "<img src='../images/m02.png' border='0'> ";
		}
		$Contents .="
			<tr height=30 align=center>
				<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
				<td class='list_box_td' bgcolor='#efefef'>".$no."</td>
				<td class='list_box_td'>".$db->dt[date]."</td>
				<td class='list_box_td'>".$mem_type_text."</td>
				<td class='list_box_td'>".$seller_master.$mem_div_text."</td>
				<td class='list_box_td' bgcolor='#efefef' ><a href=\"javascript:PopSWindow('/admin/member/member_view.php?code=".$db->dt[code]."',985,600,'member_info')\">".$db->dt[name]."</a> </td>
				<td class='list_box_td' bgcolor='#efefef' >".$db->dt[id]."</td>
				<td class='list_box_td' bgcolor='#efefef' >".$db->dt[tel]."</td>
				<td class='list_box_td' bgcolor='#efefef' >".$db->dt[pcs]."</td>
				<td class='list_box_td'>".$db->dt['mail']."</td>
				<td class='list_box_td'>".$gp_name."</td>
				<td class='list_box_td' bgcolor='#efefef'>";
					if($db->dt[auth]=="4"){
						$Contents .= $request_info_text;
						//$Contents .= "<img src='../images/".$admininfo["language"]."/btn_comform.gif' border=0 align=absmiddle onClick=\"approve_company('".$db->dt[code]."')\" title='승인'/>";
					} else {
						$Contents .= $request_info_text;
						if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
							$Contents .= "<img src='../images/".$admininfo["language"]."/btn_comform.gif' border=0 align=absmiddle onClick=\"approve_company('".$db->dt[code]."')\" title='승인'/>";
						} else {
							//Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle ></a> ";
						}
					}
			$Contents .= "
				</td>
			</tr>";
	}
		$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
	}else{
		$Contents .= "
		<tr height=50>
			<td class='list_box_td' colspan=12 align=center style='padding-top:10px;'>셀러회원 요청이 없습니다.</td>
		</tr>";
		}
		$Contents .="
		</table>";

$select = "
	<select name='update_type' onChange='view_member_num(this,\"$total\")'>
		<option value='1'>검색한 회원 전체에게</option>
		<option value='2'>선택한 회원 전체에게</option>
	</select>
		<input type='radio' name='update_kind' id='batch_update_pos' value='group' ".(($update_kind == "pos" || $update_kind == "") ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_pos');\"><label for='update_kind_group'>셀러 사용자 승인 일괄변경</label>";

	$help_text .= "
	<div id='batch_update_pos' ".($update_kind == "pos" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
	<div style='padding:4px 0px 4px 0px'><img src='../images/dot_org.gif'> <b>셀러 사용자 승인 여부</b> 
		<span class=small style='color:gray'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'P')."</span></div>
		<table width='100%' border=0 cellpadding=3 cellspacing=0 class='input_table_box'>
			<col width='15%'>
			<col width='*'>
			<tr height=30>
				<td class='input_box_title'><b>승인여부 </b></td>
				<td class='input_box_item'>
					<input type='radio' id='use_disp_1' name='use_disp' value='4' checked><label for='use_disp_1'> 승인</label>
					<input type='radio' id='use_disp_0' name='use_disp' value='0' > <label for='use_disp_0'> 미승인</label>
				</td>
			</tr>
		</table>
		<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
			<td colspan=4 align=center>";
			if( checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			$help_text .= "
				<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
			}
		$help_text .= "
			</td> 
		</tr>
		</table>
		</div>
	</form>";
$select_contents .= "".HelpBox($select, $help_text,750)."</form>";
$Contents .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >";
$Contents .="<tr height=40><td  align=right>".page_bar($total, $page, $max,$query_string,"")."</td></tr>";
$Contents = $Contents."<tr><td>".$select_contents."<br></td></tr>";
$Contents .="</table>
<br>";

$P = new LayOut();
$P->addScript = "<script language='javascript' src='member.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>\n".$Script;
$P->OnloadFunction = "";	//init();
$P->strLeftMenu = seller_menu();
$P->jquery_use = true;
$P->prototype_use = false;
$P->Navigation = "HOME > 셀러관리 > 관리자 추가요청";
$P->title = "관리자 추가요청";
$P->strContents = $Contents;
echo $P->PrintLayOut();
?>