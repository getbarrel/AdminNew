<?
/*
리스트에서 주문의 상태 조건 값을 수정 kbk 13/06/05
*/
include("../class/layout.class");

$db = new Database;
$db2 = new Database;
$mdb = new Database;

$help_text = "	-  현금영수증신청 목록입니다. 발행 취소를 했을 경우 재발행이 불가능 합니다. <br>

		";

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
	onLoad('$sDate','$eDate');";
	if($regdate != "1"){
		$Script .= "
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;";
	}

$Script .= "
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

		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("현금영수증 발행내역", "주문관리 > 현금영수증 발행내역")."</td>
		</tr>
		<tr>
			<td>
				<form name='searchmember'>
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
						<td align='left' colspan=2 height=50 width='100%' valign=top style='padding-top:5px;'>
							<table class='box_shadow' style='width:100%;' align=left cellpadding='0' cellspacing='0' border='0'>
								<tr>
									<th class='box_01'></th>
									<td class='box_02'></td>
									<th class='box_03'></th>
								</tr>
								<tr>
									<th class='box_04'></th>
									<td class='box_05' valign=top>
										<TABLE height=0 cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>
											<TR>
												<TD bgColor=#ffffff style='padding:0 0 0 0;height:30px;'>
													<table border=0 cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
													<col width = 20% >
													<col width = * >
														<tr height=27>
														  <th class='search_box_title' ><label for='regdate'>요청일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></th>
														  <td class='search_box_item' >
															<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff cellpadding='0' cellspacing='0' border='0'>
																<tr>
																	<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
																	<TD  style='padding:0 5px;' align=center> ~ </TD>
																	<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
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
														<tr>
															<th class='search_box_title' >발급유무</th>
															<td class='search_box_item' >
																<table>
																	<tr>
																		<td>
																			<input type='radio' name='receipt_yn' value='C' ".($receipt_yn == "C" ? "checked":"").">발급완료 <input type='radio' name='receipt_yn' value='Y' ".($receipt_yn == "Y" ? "checked":"").">발급대기
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr >
															<th class='search_box_title' >조건검색</th>
															<td class='search_box_item' >
																<table style='height:20px;' border=0 >
																	<tr>
																		<td>
																			<select name='search_type'>
																				<option value='md.name'>주문자명</option>
																				<option value='order_no'>주문번호</option>
																			</select>
																		</td>
																		<td>
																			<input type='text' class=textbox name='search_text' size=20>
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
						<td colspan=3 align=center  style='padding:10px 0 0 0'>
							<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
						</td>
					</tr>
				</table>
				</form>
			</td>
		</tr>

		<tr>
			<td>
			".PrintEventList()."
			</td>
		</tr>";
$mstring .= "<tr><td style='padding-bottom:10px;' colspan=7>".HelpBox("현금영수증 관리", $help_text)."</td></tr>";
$mstring .="</table>";

$Contents = $mstring;
$Script .= "
<script type='text/javascript'>
function deleteReceipt(oid){
	if(confirm(language_data['receipt_list.php']['A'][language])){//현금영수증 신청을 삭제 하시겠습니까?
		window.frames['act'].location.href='receipt.act.php?oid='+oid;
	}
}
</script>
";

$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->Navigation = "증빙서 > 현금영수증 발행내역";
$P->title = "현금영수증 발행내역";
$P->OnloadFunction = "init();";
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintEventList(){
	global $db, $mdb,$db2,$admininfo,$page,$nset;
	global $search_type, $search_text,$receipt_yn,$FromYY,$FromMM,$FromDD,$ToYY,$ToMM,$ToDD,$orderby,$ordertype,$auth_update_msg;

	if ($orderby != "" || $ordertype != ""){
		$orderby_str = " order by $orderby $ordertype ";
	}else{
		$orderby_str = " order by regdate desc ";
	}

	if($search_text != ""){
		$where .= " and $search_type LIKE '%$search_text%' ";
	}

	if($receipt_yn == "C"){
		$where .= " and b.receipt_yn = 'Y' ";
	}else if($receipt_yn == "Y"){
		$where .= " and b.receipt_yn = 'N' ";
	}

	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

	if($startDate != "" && $endDate != ""){
		$where1 .= " and  MID(replace(o.date,'-',''),1,8) between  $startDate and $endDate ";
		$where2 .= " and  MID(replace(o.regdate,'-',''),1,8) between  $startDate and $endDate ";
	}

	//$sql = "select a.*,b.*,m.code,m.name from receipt b left join receipt_result a on b.order_no = a.oid , mallstory_member m where b.id = m.id $where ";
	$sql = "select o.oid, count(*) as order_detail_cnt ,
		sum(case when od.status in ('CC','RC','DC','FC','AR','AC','AA') then 1 else 0 end) as complet_cnt,
		sum(case when od.status in ('CC','RC','FC') then 1 else 0 end) as cancel_cnt,
		sum(case when od.status in ('IR','IC','DR','DI','EA','EI','ED','EC','FA','RA','RI','RD','CA') then 1 else 0 end) as ing_cnt
		from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_USER." m , ".TBL_COMMON_MEMBER_DETAIL." md , ".TBL_SHOP_ORDER_DETAIL." od
		where receipt_y = 'Y' and taxsheet_yn not in ('Y','C') and o.oid = od.oid
		and o.uid = m.code AND m.code=md.code  $where $where1
		group by o.oid having cancel_cnt != order_detail_cnt
		";

	/*if($mdb->dbms_type == "oracle"){
		$sql = "select o.oid, count(o.oid) as order_detail_cnt ,
			sum(case when od.status in ('CC','RC','DC','FC','AR','AC','AA') then 1 else 0 end) as complet_cnt,
			sum(case when od.status in ('CC','RC','FC') then 1 else 0 end) as cancel_cnt,
			sum(case when od.status in ('IR','IC','DR','DI','EA','EI','ED','EC','FA','RA','RI','RD','CA') then 1 else 0 end) as ing_cnt
			from ".TBL_SHOP_ORDER." o,receipt b, ".TBL_COMMON_USER." m , ".TBL_COMMON_MEMBER_DETAIL." md , ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid=b.order_no and o.oid = od.oid
			and o.uid_ = m.code AND m.code=md.code  $where $where1
			group by o.oid having sum(case when od.status in ('CC','RC','FC') then 1 else 0 end) != count(o.oid)
			";
	}else{
		$sql = "select o.oid, count(*) as order_detail_cnt ,
			sum(case when od.status in ('CC','RC','DC','FC','AR','AC','AA') then 1 else 0 end) as complet_cnt,
			sum(case when od.status in ('CC','RC','FC') then 1 else 0 end) as cancel_cnt,
			sum(case when od.status in ('IR','IC','DR','DI','EA','EI','ED','EC','FA','RA','RI','RD','CA') then 1 else 0 end) as ing_cnt
			from ".TBL_SHOP_ORDER." o,receipt b, ".TBL_COMMON_USER." m , ".TBL_COMMON_MEMBER_DETAIL." md , ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid=b.order_no and o.oid = od.oid
			and o.uid = m.code AND m.code=md.code  $where $where1
			group by o.oid having cancel_cnt != order_detail_cnt
			";
	}*/
	//주문 상태에 매칭되는 상태값 변경함 kbk 13/06/05
	if($mdb->dbms_type == "oracle"){
		$sql = "select o.oid, count(o.oid) as order_detail_cnt ,
			sum(case when od.status in ('DC','EC','AR','AC','AA','AI') then 1 else 0 end) as complet_cnt,
			sum(case when od.status in ('CC','RC','FC','SO') then 1 else 0 end) as cancel_cnt,
			sum(case when od.status in ('IR','DR','DI','EA','EI','ED','ET','EG','FA','RA','RI','RD','CA','WS') then 1 else 0 end) as ing_cnt
			from ".TBL_SHOP_ORDER." o,receipt b, ".TBL_COMMON_USER." m , ".TBL_COMMON_MEMBER_DETAIL." md , ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid=b.order_no and o.oid = od.oid
			and o.uid_ = m.code AND m.code=md.code  $where $where1
			group by o.oid having sum(case when od.status in ('CC','RC','FC') then 1 else 0 end) != count(o.oid)
			";
	}else{
		$sql = "select o.oid, count(*) as order_detail_cnt ,
			sum(case when od.status in ('DC','EC','AR','AC','AA','AI') then 1 else 0 end) as complet_cnt,
			sum(case when od.status in ('CC','RC','FC','SO') then 1 else 0 end) as cancel_cnt,
			sum(case when od.status in ('IR','DR','DI','EA','EI','ED','ET','EG','FA','RA','RI','RD','CA','WS') then 1 else 0 end) as ing_cnt
			from ".TBL_SHOP_ORDER." o,receipt b, ".TBL_COMMON_USER." m , ".TBL_COMMON_MEMBER_DETAIL." md , ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid=b.order_no and o.oid = od.oid
			and o.uid = m.code AND m.code=md.code  $where $where1
			group by o.oid having cancel_cnt != order_detail_cnt
			";
	}

	$mdb->query($sql);
	$total = $mdb->total;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$mString .= "<ul class='total_cnt_area' >
						<li class='front'>전체건수 : ".$total." 건</li>
						<li class='back'><!--a href='receipt_list.excel.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a--></li>
					  </ul>";
	$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100% class='list_table_box'>";

	$mString .= "<tr align=center bgcolor=#efefef height=25>

							<td style='padding:3px 0px;' class=s_td width='11%' ><a href='?orderby=name&ordertype=".($ordertype == "" || $ordertype == "asc" ? "desc":"asc")."&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&auth=$auth'>주문자(ID)</a></td>
							<td class=m_td width='*'><a href='?orderby=oid&ordertype=".($ordertype == "" || $ordertype == "asc" ? "desc":"asc")."&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&auth=$auth'>주문번호</a></td>
							<td class=m_td width='7%'>입금여부</td>
							<td class=m_td width='7%'>주문상품</td>
							<td class=m_td width='7%'>취소/반품</td>
							<td class=m_td width='7%'>주문완료</td>
							<td class=m_td width='5%'>처리중</td>
							<td class=m_td width='8%'><a href='?orderby=oid&ordertype=".($ordertype == "" || $ordertype == "asc" ? "desc":"asc")."&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&auth=$auth'>요청일자</a></td>
							<td class=m_td width='8%'>사용용도</td>
							<td class=m_td width='10%'>승인번호</td>
							<td class=m_td width='8%'>승인날짜</td>
							<td class=e_td width='10%'>관리</td>
						</tr>
						";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=50><td colspan=12 align=center>현금영수증 내역이 존재 하지 않습니다.</td></tr>";

	}else{

	//	$sql = "select a.*,b.*,m.code,m.name from receipt b left join receipt_result a on b.order_no = a.oid , common_member_detail cmd where b.id = m.id $where $orderby_str limit $start , $max";

		/*if($mdb->dbms_type == "oracle"){
			$sql = "select AES_DECRYPT(UNHEX(md.name),'".$db->ase_encrypt_key."') as name,m.id as id,m.code as code,o.oid as oid,o.date_ as regdate ,'1' as order_type,
			count(*) as order_detail_cnt ,
			sum(case when od.status in ('IC') then 1 else 0 end) as income_cnt,
			sum(case when od.status in ('CC','RC','DC','FC','EC','AR','AC','AA') then 1 else 0 end) as complet_cnt,
			sum(case when od.status in ('CC','RC','FC') then 1 else 0 end) as cancel_cnt,
			sum(case when od.status in ('IR','DR','DI','EA','EI','ED','FA','RA','RI','RD','CA') then 1 else 0 end) as ing_cnt
			from ".TBL_SHOP_ORDER." o, receipt b, ".TBL_COMMON_USER." m , ".TBL_COMMON_MEMBER_DETAIL." md , ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = b.order_no and o.oid = od.oid
			and o.uid = m.code AND m.code=md.code
			$where $where1
			group by o.oid,m.id,md.name,m.code,o.date_ having sum(case when od.status in ('CC','RC','FC') then 1 else 0 end)  != count(o.oid)
			$orderby_str limit $start, $max";
		}else{
			$sql = "select AES_DECRYPT(UNHEX(md.name),'".$db->ase_encrypt_key."') as name,m.id as id,m.code as code,o.oid as oid,o.date as regdate ,'1' as order_type,
			count(*) as order_detail_cnt ,
			sum(case when od.status in ('IC') then 1 else 0 end) as income_cnt,
			sum(case when od.status in ('CC','RC','DC','FC','EC','AR','AC','AA') then 1 else 0 end) as complet_cnt,
			sum(case when od.status in ('CC','RC','FC') then 1 else 0 end) as cancel_cnt,
			sum(case when od.status in ('IR','DR','DI','EA','EI','ED','FA','RA','RI','RD','CA') then 1 else 0 end) as ing_cnt
			from ".TBL_SHOP_ORDER." o, receipt b, ".TBL_COMMON_USER." m , ".TBL_COMMON_MEMBER_DETAIL." md , ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = b.order_no and o.oid = od.oid
			and o.uid = m.code AND m.code=md.code
			$where $where1
			group by o.oid having cancel_cnt != order_detail_cnt
			$orderby_str limit $start, $max";
		}*/
		//주문 상태에 매칭되는 상태값 변경함 kbk 13/06/05
		if($mdb->dbms_type == "oracle"){
			$sql = "select AES_DECRYPT(UNHEX(md.name),'".$db->ase_encrypt_key."') as name,m.id as id,m.code as code,o.oid as oid,o.date_ as regdate ,'1' as order_type,
			count(*) as order_detail_cnt ,
			sum(case when od.status in ('IC') then 1 else 0 end) as income_cnt,
			sum(case when od.status in ('DC','EC','AR','AC','AA','AI') then 1 else 0 end) as complet_cnt,
			sum(case when od.status in ('CC','RC','FC','SO') then 1 else 0 end) as cancel_cnt,
			sum(case when od.status in ('IR','DR','DI','EA','EI','ED','ET','EG','FA','RA','RI','RD','CA','WS') then 1 else 0 end) as ing_cnt
			from ".TBL_SHOP_ORDER." o, receipt b, ".TBL_COMMON_USER." m , ".TBL_COMMON_MEMBER_DETAIL." md , ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = b.order_no and o.oid = od.oid
			and o.uid = m.code AND m.code=md.code
			$where $where1
			group by o.oid,m.id,md.name,m.code,o.date_ having sum(case when od.status in ('CC','RC','FC','SO') then 1 else 0 end)  != count(o.oid)
			$orderby_str limit $start, $max";
		}else{
			$sql = "select AES_DECRYPT(UNHEX(md.name),'".$db->ase_encrypt_key."') as name,m.id as id,m.code as code,o.oid as oid,o.date as regdate ,'1' as order_type,
			count(*) as order_detail_cnt ,
			sum(case when od.status in ('IC') then 1 else 0 end) as income_cnt,
			sum(case when od.status in ('DC','EC','AR','AC','AA','AI') then 1 else 0 end) as complet_cnt,
			sum(case when od.status in ('CC','RC','FC','SO') then 1 else 0 end) as cancel_cnt,
			sum(case when od.status in ('IR','DR','DI','EA','EI','ED','ET','EG','FA','RA','RI','RD','CA','WS') then 1 else 0 end) as ing_cnt
			from ".TBL_SHOP_ORDER." o, receipt b, ".TBL_COMMON_USER." m , ".TBL_COMMON_MEMBER_DETAIL." md , ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = b.order_no and o.oid = od.oid
			and o.uid = m.code AND m.code=md.code
			$where $where1
			group by o.oid having cancel_cnt != order_detail_cnt
			$orderby_str limit $start, $max";
		}
		//echo $sql;
		$db->query($sql);

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

			//$no = $no + 1;
			if($db->dt[order_type] == "1"){
				$order_type = "주문";
			}elseif($db->dt[order_type] == "2"){
				$order_type = "정회원";
			}else{
				$order_type = "부가서비스";
			}
			$sql = "select * from receipt_result where oid = '".$db->dt[oid]."'";
			$db2->query($sql);
			if($db2->total){
				$db2->fetch();
				if($db2->dt[m_ruseopt] == "0"){
					$status = "소득공제용";
				}else{
					$status = "지출증빙용";
				}
				$auth_code=$db2->dt[m_rcash_noappl];
				$receipt_regdate = substr($db2->dt[regdate],0,10);
			}else{
				$sql = "select * from receipt where order_no = '".$db->dt[oid]."' ";
				$db2->query($sql);
				$db2->fetch();

				if($db2->dt[m_useopt] == "0"){
					$status = "소득공제용";
				}else{
					$status = "지출증빙용";
				}
				$auth_code="";
				$receipt_regdate = "";
			}
			$mString = $mString."<tr height=30 bgcolor=#ffffff align=center>

			<td class='list_box_td list_bg_gray' ><a href=\"javascript:PopSWindow('../member/member_view.php?code=".$db->dt[code]."',950,500,'member_info')\">".$db->dt[name]." (".$db->dt[id].")</a></td>
			<td class='list_box_td point' style='padding:5px;' nowrap>".$db->dt[oid]."</td>
			<td class='list_box_td list_bg_gray' >".$db->dt[income_cnt]."</td>

			<td class='list_box_td' ><a href='orders.edit.php?oid=".$db->dt[oid]."' target='_blank' title='클릭:주문내역보기'><font color='#3333FF'>".$db->dt[order_detail_cnt]."</font></a></td>
			<td class='list_box_td list_bg_gray' >".$db->dt[cancel_cnt]."</td>
			<td class='list_box_td' >".($db->dt[complet_cnt]-$db->dt[cancel_cnt])."</td>
			<td class='list_box_td list_bg_gray' ><a href='orders.edit.php?oid=".$db->dt[oid]."' target='_blank' title='클릭:주문내역보기'><font color='#FF0033'>".$db->dt[ing_cnt]."</font></a></td>
			<td class='list_box_td point' >".substr($db->dt[regdate],0,10)."</td>
			<td class='list_box_td list_bg_gray' >".$status."</td>
			<td class='list_box_td' >".$auth_code."</td>
			<td class='list_box_td list_bg_gray' >".$receipt_regdate." </td>
			<td class='list_box_td' >";
			if($auth_code == ""){
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
    				if($db->dt[ing_cnt] > 0){
    					$mString = $mString."<img src='../images/".$admininfo["language"]."/btn_auth_ok.gif' onclick=\"alert(language_data['receipt_list.php']['B']['".$admininfo["language"]."']);\" style='cursor:pointer'>";
    				}else{
    					$mString = $mString."<a href=\"javascript:PoPWindow('receipt_apply.php?oid=".$db->dt[oid]."','660','440','receipt_apply')\"><img src='../images/".$admininfo["language"]."/btn_auth_ok.gif' align=absmiddle></a>";
    				}
                }else{
                    if($db->dt[ing_cnt] > 0){
    					$mString = $mString."<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_auth_ok.gif' style='cursor:pointer'>";
    				}else{
    					$mString = $mString."<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_auth_ok.gif' align=absmiddle></a>";
    				}
                }
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
    				if($db->dt[ing_cnt] > 0){
    					$mString = $mString." <img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 onclick=\"deleteReceipt('".$db->dt[oid]."')\" style='cursor:pointer'>";//'아직 주문완료 처리되지 않은 상품이 있습니다. 확인후 주문완료 처리후 현금영수증을 발행하실수 있습니다.'
    				}else{
    					$mString = $mString." <img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 onclick=\"deleteReceipt('".$db->dt[oid]."')\" style='cursor:pointer' align=absmiddle>";
    				}
                }else{
                    if($db->dt[ing_cnt] > 0){
    					$mString = $mString." <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 style='cursor:pointer'></a>";
    				}else{
    					$mString = $mString." <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 style='cursor:pointer' align=absmiddle></a>";
    				}
                }
			}else{
                $mString .= "<a href=\"javascript:PoPWindow('/admin/order/receipt_view.php?oid=".$db->dt[oid]."','500','370','receipt_view')\"><img src='../images/".$admininfo["language"]."/btn_auth_com.gif ' align=absmiddle></a>";
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
    				$mString.="
                    <a href=\"javascript:PoPWindow('receipt_mod.php?oid=".$db->dt[oid]."','560','440','')\"><img src='../images/".$admininfo["language"]."/btc_cancle.gif' align=absmiddle></a>";
                }else{
                    $mString.="
                    <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_cancle.gif' align=absmiddle></a>";
                }
			}
			/*if($db->dt[module_div] == "1"){
			$mString = $mString."<a href=\"javascript:PoPWindow('/shop/inicis/sample/INIcancel_write.php?tid=".$db->dt[m_tid]."','520','180','')\"><img src='/admin/image/btn_cancle.gif'></a>";
			}else if($db->dt[module_div] == "0"){
			$mString = $mString."<a href=\"javascript:PoPWindow('/cash/AGSCash.php?oid=".$db->dt[oid]."&Adm_no=".$db->dt[m_rcash_noappl]."','560','440','')\"><img src='/admin/image/btn_cancle.gif'></a>";
			}else{
			$mString = $mString."<a href=\"javascript:PoPWindow('/shop/lgdacom/cashreceipt_write.php?oid=".$db->dt[oid]."&Adm_no=".$db->dt[m_rcash_noappl]."','560','440','')\"><img src='/admin/image/btn_cancle.gif'></a>";
			}*/

			$mString = $mString."</td>
			</tr>
			";
		}


	}


	$mString .= "</table>";
	$mString .= "<ul class='paging_area' >
						<li class='front'>".page_bar($total, $page, $max,  "&max=$max&receipt_yn=$receipt_yn&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&orderby=$orderby&ordertype=$ordertype&search_type=$search_type&search_text=$search_text","")."</li>
						<li class='back'></li>
					  </ul>";

	return $mString;
}


?>
