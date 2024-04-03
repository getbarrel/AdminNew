<?
include("../class/layout.class");

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

if($pg_ix != ""){
	$db->query("select * from shop_poll_group where pg_ix = '$pg_ix'");
	$db->fetch();
	$g_title = $db->dt[g_title];
	$act = "group_update";

	$use_date = $db->dt[use_date];
	if($use_date){
		$sDate = ChangeDate($db->dt[sdate],"Y/m/d");
		$eDate = ChangeDate($db->dt[edate],"Y/m/d");
		$startDate = $db->dt[sdate];
		$endDate = $db->dt[edate];
	}
}else{
	$act = "group_insert";
}

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


$Script = "	<script language='javascript'>
function ChangeRegistDate(frm){
	if(frm.use_date.checked){
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

function ChangeVisitDate(frm){
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

	var frm = document.poll;
	onLoad('$sDate','$eDate');

	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;

}

function init_date(FromDate,ToDate) {
	var frm = document.poll;


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
	var frm = document.poll;

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
	var frm = document.poll;

	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);


	init_date(FromDate,ToDate);

}
		</script>";


$mstring ="<form name=poll action='poll.act.php'><input type=hidden name=act value='$act'>";
$mstring .="<input type=hidden name=pg_ix value=$pg_ix>";
$mstring .="<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
					 <tr>
						<td align='left'> ".GetTitleNavigation("설문관리", "마케팅지원 > 설문 관리 ")."</td>
					</tr>
					<tr>
				    <td align='left' colspan=5 style='padding-bottom:20px;'>
				        <div class='tab'>
				            <table class='s_org_tab' style='width:100%'>
				                <tr>
				                    <td class='tab'>
				                        <table id='tab_01'  >
				                            <tr>
				                                <th class='box_01'></th>
				                                <td class='box_02' onclick=\"document.location.href='poll_list.php'\">설문 리스트</td>
				                                <th class='box_03'></th>
				                            </tr>
				                        </table>
				                        <table id='tab_02' class=on>
				                            <tr>
				                                <th class='box_01'></th>
				                                <td class='box_02' onclick=\"document.location.href='poll_group.php'\">설문 그룹 만들기</td>
				                                <th class='box_03'></th>
				                            </tr>
				                        </table>
										<table id='tab_03' >
				                            <tr>
				                                <th class='box_01'></th>
				                                <td class='box_02' onclick=\"document.location.href='poll_result.php'\">설문 결과보기</td>
				                                <th class='box_03'></th>
				                            </tr>
				                        </table>
				                    </td>
				                    <td class='btn'  align=right>
										<a href='?mode=excel&".$QUERY_STRING."'><img src='../image/btn_excel_save.gif' border=0></a>
				                    </td>
				                </tr>
				            </table>
				        </div>
				    </td>
				</tr>";
$mstring .= "<tr align=left bgcolor=#ffffff><td width=500 colspan='3' height='25px;'><img src='/admin/images/dot_org.gif' style='' align=absmiddle> <b>설문 관리</b></td></tr>";
$mstring .= "<tr align=center bgcolor=#ffffff>
							<td colspan='3'>
								<table border='0' width='100%' cellspacing='1' cellpadding='0' >
								<tr>
									<td >
										<table border='0' width='100%' cellspacing='1' cellpadding='3' bgcolor='#c0c0c0' class='search_table_box'>
											<tr height=25 bgcolor='#ffffff' >
												<td class='search_box_title' align='left' style='padding-left:10px;' width=15%> 설문 제목</td>
												<td class='search_box_item' width='35%'>&nbsp;<input type=text class='textbox' name=g_title size=40 value='".$db->dt[g_title]."'></td>
												<td class='search_box_title' align='left' style='padding-left:10px;' width=15%> 관련 상품 </td>
												<td class='search_box_item' width='35%'>&nbsp;<input type=text class='textbox' name=pname size=40 value='".$db->dt[pname]."'></td>
											</tr>
											<tr height=25 bgcolor='#ffffff' >
												<td class='search_box_title' align='left' style='padding-left:10px;' width=15%> 설명</td>
												<td class='search_box_item' width='75%' colspan=3>&nbsp;<textarea class=textbox style='margin:10px 0;height:50px;width:90%' name=g_desc >".$db->dt[g_desc]."</textarea></td>
											</tr>
											<tr height=27 bgcolor=#ffffff>
											  <td class='search_box_title' align='left' style='padding-left:10px;' width=15%> 설문노출일자</td>
											  <td class='search_box_item' align=left colspan=3 style='padding-left:5px;'>
												<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
													<tr><td colspan=4><input type='checkbox' name='use_date' id='use_date' value='1' onclick='ChangeRegistDate(document.poll);' ".($use_date == 1 ? "checked":"")."><label for='use_date'>설문기간사용</label></td></tr>
													<tr>
														<TD width=230 nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
														<TD width=20 align=center> ~ </TD>
														<TD width=230 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
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
											<tr height=25 bgcolor='#ffffff' >
												<td class='search_box_title' align='left' style='padding-left:10px;' width=15%> 설문노출여부</td>
												<td  class=''search_box_item'width='75%' align='left' colspan=3>
													<input type=radio name='disp' value='1' checked><label for='disp_1'>사용</label>
	    										<input type=radio name='disp' value='0' ><label for='disp_0'>사용하지않음</label>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>

							</td>
						</tr>
						<tr height='50'><td colspan='5' align='right'><!--a href='poll.php'--><input type=image src='../image/b_save.gif' border='0'></td></tr>
						</form>
						</table>";
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >설문그룹을 추가하신후 질문항목을 작성하실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ></td></tr>

</table>
";


$help_text = HelpBox("설문그룹 관리", $help_text);
//$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:4px;'><table><tr><td valign=bottom><b>설문 관리</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_메일SMS리스트관리(090322)_config.xml',800,517,'manual_view')\"  title='메일/SMS 관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a></td></tr></table></div>", $help_text,200)."</div>";

$Contents = $mstring.$help_text;

$Script .= "<script language='javascript' src='../include/DateSelect.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->OnloadFunction = "init();ChangeRegistDate(document.poll);";
$P->Navigation = "마케팅지원 > 설문 그룹 만들기";
$P->title = " 설문 그룹 만들기";
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();


function FieldInsert($pollnumber, $fieldnumber, $disp){
global $id;
$dbm = new Database;
$dbm->query("SELECT * FROM ".TBL_SHOP_POLL_FIELD." where number = '$pollnumber'  order by fieldnumber");
if($dbm->total > 0){
	$actstring = "fieldupdate";
	$submitstring = "수정하기";
}else{
	$actstring = "fieldinsert";
	$submitstring = "저장하기";
}

	$mstring = "<div id='TG_VIEW_".$pollnumber."' style='position: relative; display: none;'>";
	$mstring .="<form name='field$pollnumber' action='poll.act.php'><input type=hidden name=pollnumber value=$pollnumber><input type=hidden name=act value=$actstring><input type=hidden name=fieldsize value='$fieldnumber'><input type=hidden name=id value=$id>";
	$mstring .= "<table cellapdding=0 cellspaicng=0>";
	for($i=0;$i<$fieldnumber;$i++){
		$dbm->fetch($i);
		if($i==0){
			$mstring .= "<tr><td>".($i+1)."</td><td><input type=text name=fielddesc".($i)." size=60 value='".$dbm->dt[fielddesc]."'> (".$dbm->dt[result].")</td><td  valign=top style='padding-left:10px;' rowspan=10>표시 : <input type='checkbox' name='disp' style='border:1px solid #ffffff' value=1 ".($disp==1 ? "checked":"")."> &nbsp;&nbsp;&nbsp;&nbsp;<input type=image src='/admin/image/btc_modify.gif' border=0></td></tr>";
		}else{
			$mstring .= "<tr><td>".($i+1)."</td><td><input type=text name=fielddesc".($i)." size=60 value='".$dbm->dt[fielddesc]."'> (".$dbm->dt[result].")</td></tr>";
		}
	}
	$mstring .= "</table></form></div>";

	return $mstring;

}


function SelectFieldNumber($selectfield)
{
	$divname = array ("1","2","3","4","5","6","7","8","9");

	$pos = 0;
	$strDiv = "<Select name='fieldnum'>\n";
	$strDiv = $strDiv."<option value=0>항목수</option>\n";
	while(hasMoreElements(&$divname))
	{
	       	if( $pos == $selectdiv )
	       	{
	        	$strDiv = $strDiv."<option value='".($pos+1)."' Selected>".$divname[$pos]."</option>\n";
	       	}else{
	       		$strDiv = $strDiv."<option value='".($pos+1)."'>".$divname[$pos]."</option>\n";
		}
	       	$pos++;
	}

	$strDiv = $strDiv."</Select>\n";

	return $strDiv;

}



/*

create table companyinfo (
company_id varchar(32) not null ,
company_name varchar(50) null default null,
business_number varchar(40) null default null,
business_kind varchar(40) null default null,
ceo varchar(20) null default null,
business_item varchar(50) null default null,
company_address varchar(200) null default null,
bank_owner varchar(20) null default null,
bank_name varchar(20) null default null,
bank_number varchar(30) null default null,
business_day datetime null default null,
admin_id varchar(20) null default null,
admin_pass varchar(32) null default null,
phone varchar(20) null default null,
fax varchar(20) null default null,
charger varchar(20) null default null,
charger_email varchar(20) null default null,
homepage varchar(50) null default null,
shipping_company varchar(30) null default null,
primary key(company_id));
*/
?>