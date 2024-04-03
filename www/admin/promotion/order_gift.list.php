<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;
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
$Script = "<script language='javascript'>
function OrderGiftDelete(uid){
	if(confirm('해당 사은품을 정말로 삭제하시겠습니까?'))
	{//language_data['order_gift.list.php']['A'][language]
		window.frames['act'].location.href= 'order_gift.act.php?act=delete&uid='+uid;
	}


}
function ChangeUsableRegistDate(frm){
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

function ChangeUsableVisitDate(frm){
	if(frm.visitdate.checked){
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

	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;

	frm.vFromYY.disabled = true;
	frm.vFromMM.disabled = true;
	frm.vFromDD.disabled = true;
	frm.vToYY.disabled = true;
	frm.vToMM.disabled = true;
	frm.vToDD.disabled = true;
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
	}else{
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

}




function onLoad(FromDate, ToDate) {
	var frm = document.searchmember;

	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
	LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate);
	LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate);

	init_date(FromDate,ToDate,1);
	init_date(FromDate,ToDate,2);

}
</script>";


$mstring ="
		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center >
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("구매금액별 사은품 관리", "프로모션/전시 > 구매금액별 사은품 관리 ")."</td>
		</tr>
		<tr>
			<td>";
		$mstring .= "
		<table class='box_shadow' style='width:100%;height:100%;' cellpadding=0 cellspacing=0>
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05'  valign=top style='padding:2px 2px 2px 2px'>
				<form name=searchmember method='get' ><!--SubmitX(this);'-->
				<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
					<tr height=27>
					  <td class='search_box_title' align=center>조건검색 </td>
					  <td class='search_box_item' align=left style='padding-left:5px;' colspan=3>
					  <select name=search_type>
					<option value='prod_name' ".CompareReturnValue("prod_name",$search_type,"selected").">사은품명</option>

					  </select>
					  <input class='textbox' type=text name='search_text' value='".$search_text."' style='width:50%;position:relative; top:-2px;' >
					  </td>
					</tr>
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

		 $mstring .= "
					<tr height=27>
					  <td class='search_box_title' align=center><label for='regdate'>시작일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeUsableRegistDate(document.searchmember);'></td>
					  <td class='search_box_item' align=left colspan=3 style='padding-left:5px;'>
						<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
						<tr>
							<TD width=220 nowrap ><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
							<TD width=20 align=center> ~ </TD>
							<TD width=220 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
							<TD>
								<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../image/b_btn_s_1week01.gif'></a>
								<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../image/b_btn_s_15day01.gif'></a>
								<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../image/b_btn_s_1month01.gif'></a>
								<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../image/b_btn_s_2month01.gif'></a>
								<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../image/b_btn_s_3month01.gif'></a>
							</TD>
						</tr>
					</table>
					  </td>
					</tr>
					<tr height=27>
					    <td class='search_box_title' align=center><label for='visitdate'>종료일자</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeUsableVisitDate(document.searchmember);'></td>
					    <td class='search_box_item' align=left colspan=3  style='padding-left:5px;'>
					        <table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
					            <tr>
					                <TD width=220 nowrap><SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
					                <TD width=20 align=center> ~ </TD>
					                <TD width=220 nowrap><SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월 <SELECT name=vToDD></SELECT> 일</TD>
					                <TD>
					                    <a href=\"javascript:select_date('$voneweekago','$today',2);\"><img src='../image/b_btn_s_1week01.gif'></a>
					                    <a href=\"javascript:select_date('$v15ago','$today',2);\"><img src='../image/b_btn_s_15day01.gif'></a>
					                    <a href=\"javascript:select_date('$vonemonthago','$today',2);\"><img src='../image/b_btn_s_1month01.gif'></a>
					                    <a href=\"javascript:select_date('$v2monthago','$today',2);\"><img src='../image/b_btn_s_2month01.gif'></a>
					                    <a href=\"javascript:select_date('$v3monthago','$today',2);\"><img src='../image/b_btn_s_3month01.gif'></a>
					                </TD>
					            </tr>
					        </table>
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
		</table>			";

		$mstring .= "


			</td>

		</tr>
		<tr height=50>
			<td style='padding:0 20 0 20' colspan=4 align=center><input type=image src='../image/bt_search.gif' align=absmiddle  ></td>
		</tr>
		<tr>
			<td>
			".PrintGiftList()."
			</td>
		</tr>
		";
$mstring .="</table>
				</form>";


$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기간별로 설정하신 구매금액별 사은품목록을 보실수 있습니다.</td></tr>
</table>
";


//$help_text = HelpBox("이벤트/기획전 관리", $help_text);
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:4px;'><table><tr><td valign=bottom><b>구매금액별 사은품 관리</b></td></tr></table></div>", $help_text,220)."</div>";

$Contents = $mstring.$help_text;


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->Navigation = "프로모션/전시 > 사은품 관리";
$P->title = "사은품 관리";
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintGiftList(){
	global $db, $mdb, $page, $search_type,$search_text,$auth_delete_msg;
	global $FromYY,$FromMM,$FromDD,$ToYY,$ToMM,$ToDD,$vFromYY,$vFromMM,$vFromDD,$vToYY,$vToDD,$vToMM;


	if($db->dbms_type == "oracle"){
		$where = " where uid_ != '0' ";
	}else{
		$where = " where uid <> '' ";
	}

	if($search_type != "" && $search_text != ""){
		$where .= " and $search_type LIKE  '%$search_text%' ";
	}

	$startDate = $FromYY.$FromMM.$FromDD;



	$endDate = $ToYY.$ToMM.$ToDD;

	if($startDate != "" && $endDate != ""){
		$where .= " and  start_date between  $startDate and $endDate ";
	}

	$vstartDate = $vFromYY.$vFromMM.$vFromDD;
	$vendDate = $vToYY.$vToMM.$vToDD;

	if($vstartDate != "" && $vendDate != ""){
		$where .= " and  end_date between  $vstartDate and $vendDate ";
	}

	$sql = "select * from shop_order_gift $where";

	$mdb->query($sql);
	$total = $mdb->total;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=4 cellspacing=0 border=0 width=100% class='list_table_box' bgcolor=silver>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25>
		<td class=s_td width='35%'>사은품명</td>
		<td class=m_td width='20%'>증정기간</td>
		<td class=m_td width='20%'>증정금액범위</td>
		<td class=m_td width='10%'>등록일</td>
		<td class=e_td width='10%'>관리</td>
		</tr>";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=70><td colspan=5 align=center>사은품 내역이 존재 하지 않습니다.</td></tr>";
		$mString .= "<tr bgcolor=#ffffff ><td colspan=5 align=right><a href='order_gift.write.php'><img src='../images/btm_reg.gif' border=0 ></a></td></tr>";

	}else{

		$db->query("select * from shop_order_gift $where order by regdate desc limit $start,$max");

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

			//$no = $no + 1;
			if($db->dbms_type == "oracle"){
				$uid = $db->dt[uid_];
			}else{
				$uid = $db->dt[uid];
			}

			$mString = $mString."<tr height=30 bgcolor=#ffffff align=center>
			<td bgcolor='#efefef' align=left style='padding:0 0 0 20px'><a href='order_gift.write.php?uid=".$uid."'>- ".$db->dt[prod_name]."</a></td>
			<td align=center >".$db->dt[start_date]." ~ ".$db->dt[end_date]."</td>
			<td bgcolor='#efefef'>".number_format($db->dt[amount])."원 ~ ".number_format($db->dt[limit_amount])."원</td>
			<td>".str_replace("-",".",substr($db->dt[regdate],0,10))."</td>
			<td bgcolor='#efefef' align=center>";

            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                $mString.="
				<a href=\"JavaScript:OrderGiftDelete('".$uid."')\"><img  src='../image/btc_del.gif' border=0></a>
                ";
            }else{
                $mString.="
				<a href=\"".$auth_delete_msg."\"><img  src='../image/btc_del.gif' border=0></a>
                ";
            }
            $mString.="
			</td>
			</tr>
			";
		}
		//$mString .= "<tr height=1 bgcolor=silver><td colspan=5></td></tr>";
		$mString .= "
				</table>
				<table cellpadding=. cellspacing=0 border=0 width=100% style='margin-top:15px;'>
				<col width='70%' />
				<col width='*' />
				<tr>
					<td colspan=3 align=left>".page_bar($total, $page, $max,  "&max=$max&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","")."</td>
					";
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
                    $mString .= "
                    <td colspan=2 align=right><a href='order_gift.write.php'><img src='../images/btm_reg.gif' border=0 ></a></td>
                    ";
                }
        $mString .= "
				</tr>";
	}


	$mString .= "</table>";

	return $mString;
}


?>
