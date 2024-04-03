<?
include("../class/layout.class");

$db = new Database;
$dbm = new Database;
$Script = "	<script language='javascript'>


		</script>";


$db->query("SELECT * FROM ".TBL_SHOP_POLL_TITLE." where pt_ix = '$pt_ix' ");
$db->fetch();

if($db->total){
	$db->fetch();
	$act = "poll_update";
	$pt_ix = $db->dt[pt_ix];
}else{
	$act = "poll_insert";
}

$mstring ="<div id ='zone_'><form name=poll action='poll.act.php' method='post'><input type=hidden name=act value='$act'>";
$mstring .="<input type=hidden name=pg_ix value='$pg_ix'><input type=hidden name=pt_ix value='$pt_ix'><input type=hidden name=mmode value='$mmode'>";
$mstring .="<table cellpadding=5 cellspacing=0 width=100% border=0 align=center>
						 <tr>
							<td align='left' colspan=6 > ".GetTitleNavigation("설문관리", "마케팅지원 > 설문관리 ")."</td>
						 </tr>";
$mstring .= "<tr align=center bgcolor=#ffffff>
							<td colspan='3'>
								<table border='0' width='100%' cellspacing='1' cellpadding='0'>
								<tr>
									<td >
										<table border='0' class='search_table_box' width='100%' cellspacing='1' cellpadding='5' bgcolor='#c0c0c0'>
											<col width='15%'>
											<col width='35%'>
											<col width='15%'>
											<col width='35%'>
											<tr height=30 bgcolor='#ffffff' >
												<td class='search_box_title' align='left' style='padding-left:10px;'> 설문 제목</td>
												<td class='search_box_item' colspan=3>&nbsp;<input type=text class='textbox' name=title size=100 value='".$db->dt[title]."'></td>
											</tr>
											<tr height=25 bgcolor='#ffffff' >
												<td class='search_box_title' align='left' style='padding-left:10px;'> 설문노출여부</td>
												<td class='search_box_item' align='left'>
													<input type=radio name='disp' value='1' checked><label for='disp_1'>사용</label>
	    										<input type=radio name='disp' value='0' ><label for='disp_0'>사용하지않음</label>
												</td>
												<td class='search_box_title' align='left' style='padding-left:10px;'> 설문형식 </td>
												<td class='search_box_item'>&nbsp;
												<select name='poll_type' onchange=\"if(this.value == 1){\$('poll_add_btn_area').style.display='inline';}else{\$('poll_add_btn_area').style.display='none';}\">
													<option value=''>설문형식</option>
													<option value='1' ".($db->dt[poll_type] == "1" ? "selected":"").">객관식</option>
													<option value='2' ".($db->dt[poll_type] == "2" ? "selected":"").">주관식</option>
												</select>
												<div id='poll_add_btn_area' ".($db->dt[poll_type] == "1" ? "style='display:inline;'":"style='display:none;'")." ><a onclick=\"javascript:CopyPollItemRow('pollItem')\" style='cursor:pointer;'>설문항목추가 </a></div>
												</td>
											</tr>
										</table><br><br>";

	$dbm->query("SELECT * FROM ".TBL_SHOP_POLL_FIELD." where pt_ix = '".$db->dt[pt_ix]."'  order by fieldnumber");
					if($dbm->total){
						for($i=0;$i < $dbm->total;$i++){
							$dbm->fetch($i);

		$mstring .= "
								<table border='0' id='pollItem' width='100%' cellspacing='1' cellpadding='5' style='margin:5 0 5 0;' bgcolor='#c0c0c0'>
											<col width='*'>
											<col width='85%'>
											<tr height=25 bgcolor='#ffffff' >
												<td class=leftmenu align='left' style='padding-left:10px;'> 설문항목 </td>
												<td align='left'><span id='number' style='padding-left:10px;' >".($i+1).".</span><input type=hidden id='pf_ix' name=pf_ix[] size=60 value='".$dbm->dt[pf_ix]."'> <input type=text id='fielddesc' name=fielddesc[] size=60 value='".$dbm->dt[fielddesc]."'> 결과 : <input type=text id='result' name=result[] size=5 value='".$dbm->dt[result]."'> <img src='../images/i_close.gif' ondblclick=\"DeleteRow($(this));\" style='cursor:pointer;vertical-align:middle;' />
												</td>
											</tr>
								</table>
								";

						}
					}else{
						if($db->dt[poll_type] == "1"){
		$mstring .= "
										<table border='0' id='pollItem' width='100%' cellspacing='1' cellpadding='5' style='margin:5 0 5 0;' bgcolor='#c0c0c0'>
											<col width='*'>
											<col width='85%'>
											<tr height=25 bgcolor='#ffffff' >
												<td class=leftmenu align='left' style='padding-left:10px;'> 설문항목 </td>
												<td align='left'>
												<span id='number' style='padding-left:10px;' >1.</span><input type=hidden  id='pf_ix' name=pf_ix[] size=60 value=''><input type=text  id='fielddesc' name=fielddesc[] size=60 value=''> 결과 : <input type=text id='result' name=result[] size=5 value=''> <img src='../images/i_close.gif' ondblclick=\"DeleteRow($(this));\" style='cursor:pointer;vertical-align:middle;' />
												</td>
											</tr>
										</table>";
						}
					}
$mstring .="
									</td>
								</tr>
							</table>

							</td>
						</tr>
						<tr><td colspan=3 align=right style='padding:10px;'><!--a href='poll_write.php?mmode=$mmode&pg_ix=$pg_ix'><!img src='../image/btn_newpoll.gif' border=0></a--> <input type=image src='../image/b_save.gif' border=0></td></tr>
						";
$mstring .="</table></form></div>";

//$mstring = ShadowBox($mstring);


$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >설문을 추가하신후 질문항목을 작성하실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >설문형식을 주관식으로 바꾸고 저장하시면 전체 설문항목이 삭제되게 됩니다.</td></tr>

</table>
";


//$help_text = HelpBox("게시판 관리", $help_text);
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:4px;'><table><tr><td valign=bottom><b>설문 관리</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_메일SMS리스트관리(090322)_config.xml',800,517,'manual_view')\"  title='메일/SMS 관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a></td></tr></table></div>", $help_text,200)."</div>";
	$mstring .= "<table border=0 cellpadding=0 width=100%>";
	$mstring .= "<tr height='50'><td colspan='5' align='right'>$help_text</td></tr>";
	$mstring .="</table>
	<form name='lyrstat'>
			<input type='hidden' name='opend' value=''>
		</form>
	";
$Contents = $mstring;
$Script .= "</Script>\n<script language='javascript' src='poll.js'></script>";
if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->Navigation = "HOME > 마케팅지원 > 설문관리";
	$P->NaviTitle = "설문문항 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{

	$P = new LayOut();
	$P->addScript = $Script;
	$P->Navigation = "HOME > 마케팅지원 > 설문관리";
	$P->strLeftMenu = display_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

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


	$mstring = "<table cellapdding=0 cellspaicng=0 width=70% border=0 >
								<form name='field$pollnumber' action='poll.act.php' style='display:inline;'>
							<input type=hidden name=pollnumber value='$pollnumber'>
							<input type=hidden name=act value='$actstring'>
							<input type=hidden name=fieldsize value='$fieldnumber'>
							<input type=hidden name=id value='$id'>";
	for($i=0;$i<$fieldnumber;$i++){
		$dbm->fetch($i);
		if($i==0){
			$mstring .= "<tr>
										<td>".($i+1)."</td>
										<td><input type=text name=fielddesc".($i)." size=60 value='".$dbm->dt[fielddesc]."'> (".$dbm->dt[result].")</td>
										<td  valign=top style='padding-left:10px;' >표시 : <input type='checkbox' name='disp' style='border:1px solid #ffffff' value=1 ".($disp==1 ? "checked":"")."> <input type=image src='/admin/image/btc_modify.gif' border=0></td>
										</tr>";
		}else{
			$mstring .= "<tr>
											<td>".($i+1)."</td>
											<td><input type=text name=fielddesc".($i)." size=60 value='".$dbm->dt[fielddesc]."'> (".$dbm->dt[result].")</td>
									</tr>";
		}
	}
	$mstring .= "</form></table>";

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