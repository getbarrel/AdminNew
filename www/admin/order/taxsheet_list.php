<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;
$db2 = new Database;
/*
$help_text = "	-  세금계산서발행 예정 목록입니다. <br>
	체크박스를 체크후 일괄발송을 누르시면 발행이 됩니다.
		";
*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

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
	$birDate = $birYY.$birMM.$birDD;
}
$Script = "
<script type='text/javascript'>
function init(){

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

	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;

}

function ChangeUsableDate(frm){
	if(frm.date.checked){
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

function clearAll(frm){
		for(i=0;i < frm.oid.length;i++){
				frm.oid[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.oid.length;i++){
				frm.oid[i].checked = true;
		}
}

function fixAll(frm){

	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;

	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}
</script>
";
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

		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='margin-bottom:100px;'>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("세금계산서 발행예정내역", "증빙서 > 세금계산서 발행예정내역")."</td>
		</tr>
		<tr>
			<td>
				<form name='searchmember' style='display:inline;'>
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
						<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
							<table class='box_shadow' cellpadding=0 cellspacing=0 style='width:100%;' align=left>
								<tr>
									<th class='box_01'></th>
									<td class='box_02'></td>
									<th class='box_03'></th>
								</tr>
								<tr>
									<th class='box_04'></th>
									<td class='box_05' valign=top style='padding:2px;'>
										<TABLE height=0 cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>
											<TR>
												<TD bgColor=#ffffff style='padding:0 0 0 0;height:30px;'>
													<table border=0 cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
													<col width = 15% >
													<col width = * >
														<tr height=30>
														  <th class='search_box_title' ><label for='date'>요청일자</label><input type='checkbox' name='date' id='date' value='1' onclick='ChangeUsableDate(document.searchmember);' ".CompareReturnValue("1",$date,"checked")." /></th>
														  <td class='search_box_item' >
															<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
																<tr>
																	<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
																	<TD style='padding:0 5px;' align=center> ~ </TD>
																	<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>

																	<TD style='padding-left:10px;'>
																		<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
																		<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
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
														<tr height=30>
															<th class='search_box_title' >발급유무</th>
															<td class='search_box_item' >
																<table>
																	<tr>
																		<td>
																			<input type='radio' name='tax_yn' id='tax_yn_' value='' ".($tax_yn == "" ? "checked":"")."><label for='tax_yn_'>전체</label>
																			<input type='radio' name='tax_yn' id='tax_yn_c' value='C' ".($tax_yn == "C" ? "checked":"")."><label for='tax_yn_c'>발급완료 </label>
																			<input type='radio' name='tax_yn' id='tax_yn_y'  value='Y' ".($tax_yn == "Y" ? "checked":"")."><label for='tax_yn_y'>발급대기</label>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr height=30>
															<th class='search_box_title' >조건검색</th>
															<td class='search_box_item' >
																<table>
																	<tr>
																		<td>
																			<select name='search_type'>
																				<option value='name_comname'>주문자명+사업자명</option>
																				<option value='oid'>주문번호</option>
																				<option value='com_number'>사업자번호</option>
																			</select>
																		<input type='text' class='textbox' name='search_text' size=20>
																		</td>
																		<td>
																		사업자 번호 검색시 예시 : 214-10-12345
																		</td>
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
						<td colspan=3 align=center  style='padding:20px 0'>
							<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
						</td>
					</tr>
				</table>
				</form>
			</td>
		</tr>
		<!--tr>
			<td align=right><a href='taxsheet_list.excel.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a></td>
		</tr-->
		<form name='taxbill_form' method='post' action='taxbill.act.php' target='act'>
		<input type='hidden' id='oid'>
		<input type='hidden' name='act' value='select_update'>
		<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
		<tr>
			<td>
			".PrintTaxList()."
			</td>
		</tr>
		</form>";
$mstring .= "<tr><td style='padding-bottom:10px;' colspan=7>".HelpBox("세금계산서 관리", $help_text)."</td></tr>";
$mstring .="</table>";

$Contents = $mstring;


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();ChangeUsableDate(document.searchmember);";
$P->Navigation = "증빙서 > 세금계산서 발행예정내역";
$P->title = "세금계산서 발행예정내역";
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintTaxList(){
	global $db, $mdb, $db2,$admininfo,$page,$nset,$tax_yn,$FromYY,$FromMM,$FromDD,$ToYY,$ToMM,$ToDD,$orderby,$ordertype,$search_type,$search_text,$sns_product_type,$auth_update_msg,$auth_delete_msg;

	if ($orderby != "" || $ordertype != ""){
		$orderby_str = " order by $orderby $ordertype ";
	}else{
		$orderby_str = " order by regdate desc ";
	}
	$where1=" AND od.product_type NOT IN (".implode(',',$sns_product_type).")";
	if($tax_yn == "Y"){
		$where1 .= " and taxsheet_yn = 'Y' ";
	}else if($tax_yn == "C"){
		$where1 .= " and taxsheet_yn = 'C' ";
	}

	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

	if($startDate != "" && $endDate != ""){
		$where1 .= " and  MID(replace(o.date,'-',''),1,8) between  $startDate and $endDate ";
	}
	if($search_text != ""){
		if($search_type == "name_comname"){
			$where1 .= " and (AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."')  LIKE '%$search_text%' or ccd.com_name LIKE '%$search_text%') ";
		}else{
			$where1 .= " and $search_type LIKE '%$search_text%' ";
		}
	}
	/*$sql = "select o.oid, count(*) as order_detail_cnt
		from shop_order o, ".TBL_COMMON_USER." cu , ".TBL_SHOP_ORDER_DETAIL." od, ".TBL_SHOP_PRODUCT." p
		where taxsheet_yn = 'Y' and o.oid = od.oid
		and o.uid = cu.code
		$where1
		";*/

	/*
	$sql = "select o.oid
		from shop_order o, ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_ORDER_DETAIL." od
		where taxsheet_yn = 'Y' and o.oid = od.oid
		and o.uid = cmd.code
		$where1
		";//order_detail_cnt 때문에 결과값이 1줄 생김 쓰이는 곳도 없는 것 같아서 뺌 kbk 07-25
	*/
	// 검색시 회원명과 , 회사이름 검색필드가 없어서 아래 형태로 쿼리 변경 2012.01.13

	if($db->dbms_type == "oracle"){
		$sql = "select cmd.name as name, cmd.mail, ccd.com_number,cu.id as id,cmd.code as code,o.oid as oid,o.date_ as regdate ,o.taxsheet_yn as tax_yn
			from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_COMMON_COMPANY_DETAIL." ccd , ".TBL_SHOP_ORDER_DETAIL." od
			where taxsheet_yn = 'Y' and o.oid = od.oid and cu.code = cmd.code and cu.company_id = ccd.company_id
			and o.uid_ = cmd.code $where1";
	}else{
		$sql = "select cmd.name as name, cmd.mail, ccd.com_number,cu.id as id,cmd.code as code,o.oid as oid,o.date as regdate ,o.taxsheet_yn as tax_yn
			from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_COMMON_COMPANY_DETAIL." ccd , ".TBL_SHOP_ORDER_DETAIL." od
			where taxsheet_yn = 'Y' and o.oid = od.oid and cu.code = cmd.code and cu.company_id = ccd.company_id
			and o.uid = cmd.code $where1";
	}

	//echo $sql;
	$mdb->query($sql);
	$total = $mdb->total;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

$mString = "<ul class='total_cnt_area' >
					<li class='front'>전체건수 : ".$total." 건</li>
					<li class='back'></li>
				  </ul>";
	$mString .= "<table cellpadding=4 cellspacing=0 border=0 width=100% class='list_table_box'>";

	$mString .= "<tr align=center bgcolor=#efefef height=25>
							<td class='s_td' width='30' align=center><input type='checkbox' name='all_fix' id='all_fix' value='Y' onclick=\"fixAll(document.taxbill_form)\"></td>
							<td class=m_td width='80'><a href='?orderby=name&ordertype=".($ordertype == "" || $ordertype == "asc" ? "desc":"asc")."&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&auth=$auth'><b>주문자명(ID)</b></a></td>
							<td class=m_td width='120'><a href='?orderby=oid&ordertype=".($ordertype == "" || $ordertype == "asc" ? "desc":"asc")."&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&auth=$auth'><b>주문번호</b></a></td>
							<td class=m_td width='40'>과세</td>
							<td class=m_td width='150'>주문상품명</td>
							<td class=m_td width='100'>사업자번호</td>
							<td class=m_td width='150'>담당자/이메일/발행일</td>
							<td class=m_td width='150'>구분/입금여부</td>
							<td class=e_td width='80'>관리 </td>
						</tr>
						";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=90><td colspan=9 align=center>세금계산서 내역이 존재 하지 않습니다.</td></tr>";
		$mString .= "</table>";
	}else{

		if($db->dbms_type == "oracle"){
			$sql = "select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail , ccd.com_number,cu.id as id,cmd.code as code,o.oid as oid,o.date_ as regdate ,o.taxsheet_yn as tax_yn
			from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_COMMON_COMPANY_DETAIL." ccd , ".TBL_SHOP_ORDER_DETAIL." od
			where taxsheet_yn = 'Y' and o.oid = od.oid and cu.code = cmd.code and cu.company_id = ccd.company_id
			and o.uid_ = cmd.code
			$where1 group by od.oid
			";
		}else{
			$sql = "select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail , ccd.com_number,cu.id as id,cmd.code as code,o.oid as oid,o.date as regdate ,o.taxsheet_yn as tax_yn
			from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_COMMON_COMPANY_DETAIL." ccd , ".TBL_SHOP_ORDER_DETAIL." od
			where taxsheet_yn = 'Y' and o.oid = od.oid and cu.code = cmd.code and cu.company_id = ccd.company_id
			and o.uid = cmd.code
			$where1 group by od.oid
			";
		}
		//echo $sql;
		$db->query($sql);

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

			$mString = $mString."<tr height=30 bgcolor=#ffffff align=center>
			<td><input type='checkbox' name='oid[]' id='oid' value='".$db->dt[oid]."'></td>
			<td bgcolor='#efefef'><a href=\"javascript:PopSWindow('../member/member_view.php?code=".$db->dt[code]."',950,500,'member_info')\">".$db->dt[name]."<br>(".$db->dt[id].")</a></td>
			<td align=left style='padding-left:20px;'>".$db->dt[oid]."</td>
			<td bgcolor='#efefef' colspan='2' align='left'>";

			$sql = "SELECT *
			FROM `shop_order_detail`
			WHERE `oid` = '".$db->dt[oid]."'
			group by surtax_yorn order by surtax_yorn
			";

			$db2->query($sql);

			$mString = $mString."<table cellpadding=0 cellspacing=0 border=0 width=100%>";

			for($j=0;$j < $db2->total;$j++){
				$db2->fetch($j);
				if($db2->dt[surtax_yorn] == "Y") $surtax_yorn = "비과세";
				else $surtax_yorn = "과세";

				$mString = $mString."<tr height='21'>
				<td width='60' align='center'>".$surtax_yorn."</td>
				<td width='150' align='center'>".$db2->dt[pname]."</td>
				</tr>";
			}
			$mString = $mString."</table>";


			$mString = $mString."</td>
			<td bgcolor='#ffffff'>".$db->dt[com_number]." </td>
			<td width='150' align='center' bgcolor='#efefef'>
			<input type='text' name='tax_charge_name[".$db->dt[oid]."]' class='textbox' value='".$db->dt[name]."' style='width:138px;margin:2px 0px;'><br/>
			<input type='text' name='tax_charge_email[".$db->dt[oid]."]' class='textbox' value='".$db->dt[mail]."' style='width:138px;margin:2px 0px;'><br/>
			<select name='taxt_regdate_y[".$db->dt[oid]."]' style='width:60px;'>
			";
			$toyear = date("Y");
			$tomonth = date("m");
			$today = date("d");
			$todate = date("Y-m-d");
			$endyear = $toyear - 3;
			//echo $tomonth;
			for($b=$toyear; $b>$endyear; $b--){
				if($b == $toyear) $checked = " selected ";
				else $checked = "";
				$mString .= "<option value='$b'$checked>$b</option>\n";
			}
			$mString = $mString."</select>
			<select name='taxt_regdate_m[".$db->dt[oid]."]' style='width:40px;'>
			";
			for($c=1; $c<=12; $c++){
				if($c == $tomonth) $checked = " selected ";
				else $checked = "";
				if($c < 10) $c = "0".$c;
				$mString .= "<option value='$c'$checked>$c</option>\n";
			}
			$mString = $mString."</select>
			<select name='taxt_regdate_d[".$db->dt[oid]."]' style='width:40px;'>
			";
			for($d=1; $d<=31; $d++){
				if($d == $today) $checked = " selected ";
				else $checked = "";
				if($d < 10) $d = "0".$d;
				$mString .= "<option value='$d'$checked>$d</option>\n";
			}
			$mString = $mString."</select>

			</td>
			<td width='150' align='center'>
			<select name='tax_deposit_states[".$db->dt[oid]."]' style='width:100px;'>
				<option value='Y'>입금 </option>
				<option value='N'>미입금 </option>
			</select><br/>
			<input type='text' class='textbox' name='bank_date[".$db->dt[oid]."]' value='".$todate."' style='width:90px;margin:2px 0px;'>
			</td>
			<td bgcolor='#efefef'>";
			if($db->dt[tax_yn] == "Y"){
				if($db->dt[ing_cnt] > 0){
					$mString .= "
                    <img src='../images/".$admininfo["language"]."/btn_auth_ok.gif' onclick=\"alert('아직 주문완료 처리되지 않은 상품이 있습니다. 확인후 주문완료 처리후 세금계산서를 발행하실수 있습니다.');\"
				    style='cursor:pointer;margin:2px 0px; vertical-align:middle;'> ";
				}else{
					$mString .= "
                    <img src='../images/".$admininfo["language"]."/btn_auth_ok.gif' onclick=\"PoPWindow('taxbill.php?uid=".$db->dt[code]."&oid=".$db->dt[oid]."&order_type=".$db->dt[order_type]."',680,800,'taxbill')\"
				    style='cursor:pointer;margin:2px 0px; vertical-align:middle;'> ";
				}
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				    $mString .= "<img src='../images/".$admininfo["language"]."/btc_del.gif' onclick=\"document.frames['act'].location.href='taxbill.act.php?act=delete&oid=".$db->dt[oid]."'\" style='cursor:pointer; vertical-align:middle;'>";
                }else{
                    $mString .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' style='cursor:pointer; vertical-align:middle;'></a>";
                }

			}else{
				$mString = $mString."<img src='../images/".$admininfo["language"]."/btn_auth_com.gif' onclick=\"PoPWindow('taxbill.php?uid=".$db->dt[code]."&oid=".$db->dt[oid]."&order_type=".$db->dt[order_type]."',680,800,'taxbill')\" style='cursor:pointer; vertical-align:middle;'></a>";
			}


			$mString = $mString."</td>
			</tr>
			<tr height=1><td colspan=9 class='dot-x'></td></tr>
			";

		}

	$mString .= "</table>";
	$mString .= "<ul class='paging_area' >
						<li class='front'>".page_bar($total, $page, $max,  "&max=$max&receipt_yn=$receipt_yn&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&orderby=$orderby&ordertype=$ordertype&search_type=$search_type&search_text=$search_text","")."</li>";
						if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                            $mString.="
                            <li class='back'><input type=image src='../images/".$admininfo["language"]."/btn_allok.gif' align=absmiddle></li>";
                        }else{
                            $mString.="
                            <li class='back'><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_allok.gif' align=absmiddle></a></li>";
                        }
                        $mString.="
					  </ul>";

	}




	return $mString;
}


?>
