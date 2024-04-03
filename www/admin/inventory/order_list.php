<?
include("../class/layout.class");
include("inventory.lib.php");
//include_once($_SERVER["DOCUMENT_ROOT"]."/include/sns.config.php");

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

if(!$ptype || $ptype=="") $ptype=1;

if($ptype==1) {
	$tab_on_txt1="class='on'";
	$tab_on_txt2="";
	$arr_product_type=$shop_product_type;
} else {
	$tab_on_txt1="";
	$tab_on_txt2="class='on'";
	$arr_product_type=$sns_product_type;
}

$Script = "	<script language='javascript'>

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

function ChangeLimitedPriodDate(frm){
	if(frm.limitpriod.checked){
		frm.vFromYY.disabled = false;
		frm.vFromMM.disabled = false;
		frm.vFromDD.disabled = false;
		frm.vToYY.disabled = false;
		frm.vToMM.disabled = false;
		frm.vToDD.disabled = false;
	}else{
		frm.vFromYY.disabled = true;
		frm.vFromMM.disabled = true;
		frm.vFromDD.disabled = true;
		frm.vToYY.disabled = true;
		frm.vToMM.disabled = true;
		frm.vToDD.disabled = true;
	}
}

function init(){

	var frm = document.searchmember;
	onLoad('$sDate','$eDate');
	if(!frm.regdate.checked){
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;
	}
	if(!frm.limitpriod.checked){
		frm.vFromYY.disabled = true;
		frm.vFromMM.disabled = true;
		frm.vFromDD.disabled = true;
		frm.vToYY.disabled = true;
		frm.vToMM.disabled = true;
		frm.vToDD.disabled = true;
		}


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

	for(i=0; i<frm.vFromYY.length; i++) {
		if(frm.vFromYY.options[i].value == FromDate.substring(0,4))
			frm.vFromYY.options[i].selected=true
	}
	for(i=0; i<frm.vFromMM.length; i++) {
		if(frm.vFromMM.options[i].value == FromDate.substring(5,7))
			frm.vFromMM.options[i].selected=true
	}
	for(i=0; i<frm.vFromDD.length; i++) {
		if(frm.vFromDD.options[i].value == FromDate.substring(8,10))
			frm.vFromDD.options[i].selected=true
	}


	for(i=0; i<frm.vToYY.length; i++) {
		if(frm.vToYY.options[i].value == ToDate.substring(0,4))
			frm.vToYY.options[i].selected=true
	}
	for(i=0; i<frm.vToMM.length; i++) {
		if(frm.vToMM.options[i].value == ToDate.substring(5,7))
			frm.vToMM.options[i].selected=true
	}
	for(i=0; i<frm.vToDD.length; i++) {
		if(frm.vToDD.options[i].value == ToDate.substring(8,10))
			frm.vToDD.options[i].selected=true
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


function orderPrint(ioid,act) {
	window.frames['act'].location.href= 'order_print.act.php?act='+act+'&ioid='+ioid;
}

function onLoad(FromDate, ToDate) {
	var frm = document.searchmember;

	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
	LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate);
	LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate);

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
			<td align='left'> ".GetTitleNavigation("발주(사입)내역", "재고관리 > 발주(사입)작성 > 발주(사입)내역 ")."</td>
		</tr>
		<tr>
			<td>
				<form name='searchmember'>
    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
		<tr>
		<td style='width:100%;' valign=top colspan=3>
			<table width=100%  border=0 cellpadding='0' cellspacing='0'>
				<!--tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>사용후기 검색하기</b></td></tr-->
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
											<col width='15%'>
											<col width='35%'>
											<col width='15%'>
											<col width='35%'>
											<tr height='27'>
												<!--th class='search_box_title' bgcolor='#efefef' width='150' align=center>조건검색 : </th>
												<td class='search_box_item'>
													<table cellpaddig='0' cellspacing='0' border='0' width='100%'>
														<tr>
															<td width='90'>
															<select name=search_type>
																<option value='gname' ".CompareReturnValue("pname",$search_type,"selected").">재고상품명</option>
																<option value='gid' ".CompareReturnValue("pid",$search_type,"selected").">상품아이디(key)</option>
																<option value='uf_name' ".CompareReturnValue("uf_name",$search_type,"selected").">작성자</option>
																<option value='uf_contents' ".CompareReturnValue("uf_contents",$search_type,"selected").">내용</option>
															</select>
															</td>
															<td width='*'><input type=text class=textbox name='search_text' value='".$search_text."' style='width:200px;height:18px;' ></td>

														</tr>
													</table>
												</td-->
												<th class='search_box_title'align=center>업체명 : </th>
												<td class='search_box_item'>
													".SelectSupplyCompany($ci_ix,'ci_ix','select','false')."
												</td>
												<th class='search_box_title'align=center>처리상태 : </th>
												<td class='search_box_item'>
													<select name=status>
														<option value='' ".CompareReturnValue("",$status,"selected").">선택하기</option>
														<option value='AR' ".CompareReturnValue("AR",$status,"selected").">".$inventory_order_status["AR"]."</option>
														<option value='OR' ".CompareReturnValue("OR",$status,"selected").">".$inventory_order_status["OR"]."</option>
														<option value='WR' ".CompareReturnValue("WR",$status,"selected").">".$inventory_order_status["WR"]."</option>
														<option value='WC' ".CompareReturnValue("WC",$status,"selected").">".$inventory_order_status["WC"]."</option>
														<option value='CC' ".CompareReturnValue("CC",$status,"selected").">".$inventory_order_status["CC"]."</option>
														<option value='OC' ".CompareReturnValue("OC",$status,"selected").">".$inventory_order_status["OC"]."</option>
														<option value='WP' ".CompareReturnValue("WP",$status,"selected").">".$inventory_order_status["WP"]."</option>
														<option value='DC' ".CompareReturnValue("DC",$status,"selected").">".$inventory_order_status["DC"]."</option>
													</select>
												</td>
											</tr>
											 <tr height=27>
												<td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>접수일자</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".(($regdate==1)?"checked":"")."></td>
											  <td class='search_box_item' align=left style='padding-left:5px;' colspan=3>
												<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff >
												<tr>
													<TD  nowrap>
														<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY style='width:65px;'></SELECT> 년
														<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM style='width:45px;'></SELECT> 월
														<SELECT name=FromDD style='width:45px;'></SELECT> 일
													</TD>
													<TD style='padding:0 5px;' align=left> ~ </TD>
													<TD nowrap>
														<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY style='width:65px;'></SELECT> 년
														<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM style='width:45px;'></SELECT> 월
														<SELECT name=ToDD style='width:45px;'></SELECT> 일
													</TD>
													<TD style='padding-left:10px;'>
														<a href=\"javascript:select_date('$today','$today',2);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
															<a href=\"javascript:select_date('$vyesterday','$vyesterday',2);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
															<a href=\"javascript:select_date('$voneweekago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
															<a href=\"javascript:select_date('$v15ago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
															<a href=\"javascript:select_date('$vonemonthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
															<a href=\"javascript:select_date('$v2monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
															<a href=\"javascript:select_date('$v3monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
													</TD>
												</tr>
											</table>
											  </td>
											</tr>
											<tr height=27>
											  <td class='search_box_title' ><label for='limitpriod'>납기일자</label><input type='checkbox' name='limitpriod' id='limitpriod' value='1' onclick='ChangeLimitedPriodDate(document.searchmember);' ".CompareReturnValue("1",$limitpriod,"checked")."></td>
											  <td class='search_box_item'  colspan=3  >
												<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
													<tr>
														<TD nowrap>
															<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY style='width:65px;'></SELECT> 년
															<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM style='width:45px;'></SELECT> 월
															<SELECT name=vFromDD style='width:45px;'></SELECT> 일
														</TD>
														<TD style='padding:0 5px;' align=center>~</TD>
														<TD nowrap>
															<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY style='width:65px;'></SELECT> 년
															<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM style='width:45px;'></SELECT> 월
															<SELECT name=vToDD style='width:45px;'></SELECT> 일
														</TD>
														<TD style='padding-left:10px;'>
															<a href=\"javascript:select_date('$today','$today',2);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
															<a href=\"javascript:select_date('$vyesterday','$vyesterday',2);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
															<a href=\"javascript:select_date('$voneweekago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
															<a href=\"javascript:select_date('$v15ago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
															<a href=\"javascript:select_date('$vonemonthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
															<a href=\"javascript:select_date('$v2monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
															<a href=\"javascript:select_date('$v3monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
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
		".PrintOrderList()."
		</td>
	</tr>
	</table>";

$Contents = $mstring;

//$Contents .= HelpBox("사용후기 관리", $help_text);

//$Script = "<script language='javascript' src='basicinfo.js'></script>";
$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = inventory_menu();
$P->Navigation = "재고관리 > 발주(사입)요청관리 > 발주(사입)내역";
$P->title = "발주(사입)내역";
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintOrderList(){
	global $admininfo, $admin_config, $DOCUMENT_ROOT,$nset,$page,$uf_valuation,$search_type,$search_text,$_GET,$auth_update_msg,$auth_delete_msg;
	global $inventory_order_status,$db;

	$mdb = new Database;

	$where = " where io.ci_ix = ici.ci_ix ";


	if($search_type != "" && $search_text != ""){
		$where .= " and io.".$search_type." LIKE '%$search_text%' ";
	}

	if($_GET["status"]){
		$where .= " and  io.status ='".$_GET["status"]."' ";
	}

	$startDate = $_GET["FromYY"].$_GET["FromMM"].$_GET["FromDD"];
	$endDate = $_GET["ToYY"].$_GET["ToMM"].$_GET["ToDD"];

	if($startDate != "" && $endDate != ""){
		$where .= " and  date_format(io.regdate , '%Y%m%d') between  $startDate and $endDate ";
	}


	$startDate = $_GET["vFromYY"].$_GET["vFromMM"].$_GET["vFromDD"];
	$endDate = $_GET["vToYY"].$_GET["vToMM"].$_GET["vToDD"];
	if($startDate != "" && $endDate != ""){
		$where .= " and ( $startDate < date_format(io.limit_priod_s , '%Y%m%d')  or date_format(io.limit_priod_e , '%Y%m%d') > $endDate  ) ";
	}

	$sql = "select COUNT(*) as total from inventory_order io, inventory_customer_info ici  $where ";

	//echo $sql."<br>";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];
	$max = 10;
	//echo $total;
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
	//$mString ="<form name='form5' action='useafter.act.php' method='post' target='act'><input type='hidden' name='act' value='update'>";
	$mString = "<div style='overflow-x:hidden;width:100%;'>";
	$mString .= "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver border='0' style='padding:0px;margin:0px;' class='list_table_box'>
	<tr bgcolor=#efefef style='font-weight:600;'>
		<td class=s_td width='3%' height=27 align='center' nowrap>번호</td>
		<td class=m_td width='9%' align='center'>접수일자</td>
		<td class=m_td width='8%' align='center'>접수번호</td>
		<td class=m_td width='8%' align='center'>업체명</td>
		<td class=m_td width='9%' align='center'>납기일</td>
		<td class=m_td width='6%' align='center'>발주자</td>
		<td class='m_td' width='7%' align='center' nowrap>발주금액 합계</td>
		<td class='m_td' width='7%' align='center'>부가세 합계</td>
		<td class='m_td' width='8%' align='center'>처리상태</td>
		<td class='m_td' width='7%' align='center'>업체담당</td>
		<td class='m_td' width='15%' align='center'>실입고증</td>
		<td class=e_td width='15%' align='center'>관리</td>
		</tr>
		";
	if ($total == 0){
		$mString .= "<tr bgcolor=#ffffff height=50><td class='list_box_td' colspan=12 align=center>발주 내역이 존재 하지 않습니다.</td></tr>";
	}else{

		$sql = "select io.* , ici.customer_name from inventory_order io, inventory_customer_info ici  $where order by  io.regdate desc limit $start, $max";
		//echo $sql."<br>";
		$mdb->query($sql);

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			$no = $total - ($page - 1) * $max - $i;

			$filepath = $_SERVER["DOCUMENT_ROOT"]."".$admininfo["mall_data_root"]."/inventory/order/".$mdb->dt[ioid]."/".$mdb->dt[real_input_file];

			if(is_file($filepath)){
				$download = "<a href='./real_input_download.php?file_name=".$mdb->dt[real_input_file]."&ioid=".$mdb->dt[ioid]."'>다운로드</a>";
			}else{
				$download = "
					<form name='upload_frm_".$mdb->dt[ioid]."' method='post' action='./order_pop.act.php' enctype='multipart/form-data' onSubmit='return CheckFormValue(this);' target='act'>
						<input type='hidden' name='act' value='file_register' /><input type='hidden' name='ioid' value='".$mdb->dt[ioid]."'/>
						".InputFileHtmlControl('real_input','../images/'.$admininfo["language"].'/btn_file_select.gif','margin-left:0px;')." <input type='image' src='../images/".$admininfo["language"]."/real_file_input.gif' /> 
					</form>";
			}

			if($mdb->dt[status]=="AR"){
				$sql = "select aa.ala_ix,aa.approve_yn,aa.approve_date,adi.disp_name,adi.charger_name,adi.charger_ix from common_authline_approve aa ,common_authline_detail_info adi where aa.aldt_ix = adi.aldt_ix and aa.ioid = '".$mdb->dt[ioid]."' and approve_yn = 'N' order by adi.order_approve asc ";
				$db->query($sql);
				$db->fetch();
			}

			$mString .= "<tr bgcolor=#ffffff align=center height='30'>
			<input type='hidden' name='ioid[]' value='".$mdb->dt[ioid]."'>
			<td class='list_box_td' bgcolor='#efefef'>".$no."</td>
			<td class='list_box_td' bgcolor='#ffffff' nowrap>".$mdb->dt[regdate]."</td>
			<td class='list_box_td' bgcolor='#efefef' nowrap><a href=\"javascript:PoPWindow3('../inventory/order_detail.php?ioid=".$mdb->dt[ioid]."',950,700,'order_detail')\">".$mdb->dt[ioid]."</a></td>
			
			
			<td bgcolor='#ffffff' nowrap>".$mdb->dt[customer_name]."</td>
			<td class='list_box_td point' nowrap>".$mdb->dt[limit_priod_s]." ~ ".$mdb->dt[limit_priod_e]."</td>
			<td bgcolor='#ffffff' nowrap>".$mdb->dt[order_charger]."</td>
			<td bgcolor='#ffffff' nowrap>".number_format($mdb->dt[total_price])."</td>
			<td bgcolor='#ffffff' nowrap>".number_format($mdb->dt[total_price]*0.1)."</td>
			<td class='list_box_td' bgcolor='#ffffff' nowrap> ".$inventory_order_status[$mdb->dt[status]].($mdb->dt[status]=="AR" ?  "(".$db->dt[charger_name].")" : "" )."</td>
			<td class='list_box_td' bgcolor='#efefef' nowrap>".$mdb->dt[incom_company_charger]." </td>
			<td class='list_box_td' bgcolor='#efefef' nowrap>".$download."</td>
			<td class='list_box_td' bgcolor='#ffffff' align=center nowrap>			 ";

			if($mdb->dt[status] == "WC" || $mdb->dt[status] == "AR"){
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$mString .= "
					<a href=\"javascript:PoPWindow3('../inventory/order_detail.php?ioid=".$mdb->dt[ioid]."',950,700,'order_detail')\"><img  src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
				}else{
					$mString .= "
					<a href=\"".$auth_update_msg."\"><img  src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
				}
			}else{
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$mString .= "
					<a href=\"javascript:PoPWindow3('../inventory/order_detail.php?ioid=".$mdb->dt[ioid]."',950,700,'order_detail')\"><img  src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
					<a href=\"javascript:PoPWindow3('../inventory/order_detail.php?ioid=".$mdb->dt[ioid]."',950,700,'order_detail')\"><img  src='../images/".$admininfo["language"]."/bts_warehouse.gif' border=0></a>
					<a href=\"javascript:PoPWindow3('../inventory/order_detail.php?ioid=".$mdb->dt[ioid]."&order_type=direct',950,700,'order_detail')\"><img  src='../images/".$admininfo["language"]."/bts_direct_delivery.gif' border=0></a>";
				}else{
					$mString .= "
					<a href=\"".$auth_update_msg."\"><img  src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
					<a href=\"".$auth_update_msg."\"><img  src='../images/".$admininfo["language"]."/bts_warehouse.gif' border=0></a>
					<a href=\"".$auth_update_msg."\"><img  src='../images/".$admininfo["language"]."/bts_direct_delivery.gif' border=0></a> ";
				}
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$mString .= " <a href=JavaScript:OrderInfoDelete('".$mdb->dt[ioid]."')><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}else{
					$mString .= " <a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}

			}

			if($mdb->dt[status] != "AR"){
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$mString .= " <a href=\"JavaScript:orderPrint('".$mdb->dt[ioid]."','excal')\">발주서엑셀다운</a>";
				}else{
					$mString .= " <a href=\"".$auth_update_msg."\">발주서엑셀다운</a> ";
				}
			}

			$mString .= "
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
	$mString .= "
					</table></div>
					<table cellpadding=0 cellspacing=0 width=100%>
					<tr height=30 bgcolor=#ffffff>
						<td colspan=6 align=center style='padding:10px 0 0 0'>".page_bar($total, $page, $max,  $query_string, "")."</td>
					</tr>
					<!--tr height=30 bgcolor=#ffffff>
						<td colspan=6 align=right><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;'></td>
					</tr-->
					</table>
					"; //</form>

	return $mString;
}

/*
CREATE TABLE IF NOT EXISTS `inventory_order` (
  `ioid` varchar(20) NOT NULL COMMENT '발주키',
  `order_charger` varchar(255) DEFAULT NULL COMMENT '발주 담당자',
  `limit_priod_s` varchar(10) DEFAULT NULL COMMENT '납기일(시작일)',
  `limit_priod_e` varchar(10) NOT NULL COMMENT '납기일(종료일)',
  `ci_ix` varchar(255) DEFAULT NULL COMMENT '입고처키',
  `incom_company_charger` varchar(255) DEFAULT NULL COMMENT '업체담당자',
  `b_delivery_price` int(8) DEFAULT '0' COMMENT '사전 현지 운송료',
  `a_delivery_price` int(8) DEFAULT '0' COMMENT '사후 현지 운송료',
  `b_tax` int(8) DEFAULT '0' COMMENT '사전 현지 세금',
  `a_tax` int(8) DEFAULT '0' COMMENT '사후 현지 세금',
  `b_commission` int(8) DEFAULT '0' COMMENT '사전 수수료',
  `a_commission` int(8) DEFAULT '0' COMMENT '사후 수수료',
  `total_price` int(10) DEFAULT '0' COMMENT '발주상품 총 금액',
  `total_add_price` int(10) DEFAULT '0' COMMENT '발주상품 총 추가금액',
  `pttotal_price` int(10) DEFAULT '0' COMMENT '최종결제금액',
  `status` varchar(2) DEFAULT NULL COMMENT '상태',
  `etc` varchar(255) DEFAULT NULL COMMENT '기타필드',
  `real_input_file` varchar(255) DEFAULT NULL COMMENT '실입고증',
  `charger_ix` varchar(32) NOT NULL COMMENT '발주담당자',
  `al_ix` int(10) NOT NULL COMMENT '결제라인',
  `regdate` datetime NOT NULL COMMENT '둥ㅀㄱ알',
  PRIMARY KEY (`ioid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='발주내역';


CREATE TABLE IF NOT EXISTS `inventory_order_detail` (
  `iod_ix` int(10) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `ioid` varchar(20) NOT NULL COMMENT '발주아이디',
  `ci_ix` int(10) NOT NULL COMMENT '입고처키값',
  `pi_ix` int(6) unsigned DEFAULT NULL COMMENT '보관장소키',
  `gid` int(10) unsigned zerofill DEFAULT NULL COMMENT '상품아이디',
  `surtax_yorn` char(1) NOT NULL DEFAULT 'N' COMMENT '면세여부',
  `gi_ix` int(10) unsigned DEFAULT NULL COMMENT '단품인덱스',
  `gname` varchar(255) DEFAULT NULL COMMENT '발주상품명',
  `item_name` varchar(100) DEFAULT NULL COMMENT '단품명(규격)',
  `order_cnt` int(8) DEFAULT NULL COMMENT '발주수량',
  `order_coprice` int(10) NOT NULL COMMENT '견적가',
  `incom_cnt` int(8) DEFAULT NULL COMMENT '입고수량',
  `sellprice` int(10) DEFAULT NULL COMMENT '단가',
  `coprice` int(10) DEFAULT NULL COMMENT '공급가',
  `detail_status` varchar(2) NOT NULL DEFAULT 'OR' COMMENT '발주상세상태',
  `order_charger_ix` varchar(32) NOT NULL COMMENT '발주담당자',
  `in_charger_name` varchar(30) DEFAULT NULL COMMENT '입고담당자 이름',
  `in_charger_ix` varchar(32) DEFAULT NULL COMMENT '입고 담당자',
  `regdate` datetime NOT NULL COMMENT '등록일자',
  PRIMARY KEY (`iod_ix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='발주내역 상세정보' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `inventory_order_detail_tmp` (
  `iodt_ix` int(10) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `ci_ix` int(6) unsigned NOT NULL COMMENT '입고처키',
  `company_id` varchar(32) NOT NULL COMMENT '회사키',
  `charger_ix` varchar(32) NOT NULL COMMENT '회원키',
  `pi_ix` int(6) unsigned NOT NULL COMMENT '예정보관장소',
  `ps_ix` int(8) NOT NULL COMMENT '보관장소키',
  `gid` int(10) unsigned zerofill DEFAULT NULL COMMENT '재고상품아이디',
  `surtax_yorn` char(1) NOT NULL DEFAULT 'N' COMMENT '면세여부',
  `gi_ix` int(10) unsigned DEFAULT NULL COMMENT '상품물류 옵션코드',
  `stock_pcode` varchar(30) DEFAULT NULL COMMENT '상품물류 코드',
  `gname` varchar(255) DEFAULT NULL COMMENT '상품명',
  `item_name` varchar(100) DEFAULT NULL COMMENT '단품명(규격)',
  `order_cnt` int(8) DEFAULT NULL COMMENT '발주수량',
  `order_coprice` int(10) DEFAULT NULL COMMENT '발주 견적가(공급가)',
  `order_yn` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '주문여부',
  `regdate` datetime NOT NULL COMMENT '등록일자',
  PRIMARY KEY (`iodt_ix`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='임시발주내역 상세정보' AUTO_INCREMENT=4 ;

*/
?>
