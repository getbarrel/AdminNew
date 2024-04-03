<?

include("../class/layout.work.class");
include("kms.lib.php");

include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("../class/calender.big.class");


$db = new Database;
$mdb = new Database;



$max = 20; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}

if($mycid != ""){
	//$sql = "select * from kms_data where uid = '".$admininfo[charger_ix]."' and mycid LIKE '".substr($mycid,0,3*($depth+1))."%' ";
	$sql = "select count(*) as total from kms_data mk, kms_mycategory mc, common_member_detail cmd
			where mc.cid = mk.mycid and mk.uid = '".$admininfo[charger_ix]."' and mk.uid = mc.uid and mk.uid = cmd.code and mk.mycid = mc.cid and mk.mycid LIKE '".substr($mycid,0,3*($depth+1))."%'  ";
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];
	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&list_type=$list_type&group_ix=$group_ix&dp_ix=$dp_ix&charger_ix=$charger_ix&birthday_yn=$birthday_yn&sex=$sex&mailsend_yn=$mailsend_yn&smssend_yn=$smssend_yn&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");

	$sql = "select mk.*, mc.depth, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."')  as charger from kms_data mk, kms_mycategory mc, common_member_detail cmdwhere mc.cid = mk.mycid and mk.uid = '".$admininfo[charger_ix]."' and mk.uid = mc.uid and mk.uid = cmd.code and mk.mycid = mc.cid and mk.mycid LIKE '".substr($mycid,0,3*($depth+1))."%' order by idx desc ";



}else if($gcid != ""){
	//$sql = "select * from kms_data where gcid LIKE '".substr($gcid,0,3*($depth+1))."%' and open = 1";
	$sql = "select mk.*, gc.depth, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."')  as charger from kms_data mk, kms_category_info gc, common_member_detail cmdwhere mk.gcid = gc.cid and mk.uid = cmd.code  and mk.gcid LIKE '".substr($gcid,0,3*($depth+1))."%' and open = 1 and mk.company_id = '".$user[company_id]."' and mk.company_id = gc.company_id order by idx desc ";
}else{
	$sql = "select count(*) as total
			from kms_data mk, kms_mycategory mc, common_member_detail cmd
			where mc.cid = mk.mycid and mk.uid = '".$admininfo[charger_ix]."' and mk.uid = mc.uid and mk.uid = cmd.code ";
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];
	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&list_type=$list_type&group_ix=$group_ix&dp_ix=$dp_ix&charger_ix=$charger_ix&birthday_yn=$birthday_yn&sex=$sex&mailsend_yn=$mailsend_yn&smssend_yn=$smssend_yn&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");

	$sql = "select mk.*, mc.depth, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."')  as charger
			from kms_data mk, kms_mycategory mc, common_member_detail cmd
			where mc.cid = mk.mycid and mk.uid = '".$admininfo[charger_ix]."' and mk.uid = mc.uid and mk.uid = cmd.code
			order by idx desc LIMIT $start, $max  ";
}
//echo $sql;
$db->query($sql);


$Contents = "
<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("지식 관리", "지식관리 > 지식 목록 ")."

	</td>
  </tr>
</table>
<script type='text/javascript' >
var list_view_type = '".$_GET["list_view_type"]."';
var list_type = '".$_GET["list_type"]."';
var parent_group_ix = '".$_GET["parent_group_ix"]."';
var group_ix = '".$_GET["group_ix"]."';
var department = '".$_GET["department"]."';
var charger_ix = '".($_GET["charger_ix"] ? $_GET["charger_ix"]:$admininfo[charger_ix])."';
var ss_charger_ix = '".$admininfo[charger_ix]."';
var dp_ix = '".$_GET["dp_ix"]."';
var sdate = '".$_GET["sdate"]."';
var edate = '".$_GET["edate"]."';
</script>
<style type='text/css'>
	#calendar {
		width: 100%;
		margin: 0 auto;
		padding:10px 10px 0px 10px;
		}
	.layerCon {position:absolute;left:50%;top:50%;z-index:1001;display:none;}
	#layerBg {position:absolute;width:100%;height:100%;left:0;top:0;background:#000000;filter:alpha(opacity=70);opacity:0.7;z-index:1000;display:none;}
</style>
<div id='loading' style='display:none;border:0px solid red;width:100%;height:100%;padding-top:13px;'><img src='../images/loading_large.gif' border=0></div>";

$Contents .= "

<style>
	.fader{opacity:0;display:none;height:27px;}
</style>
<table width=100% cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td align='left' colspan=4 style='padding:0px 0px 15px 0px;'>
	    	".KMS_Tab($total)."
	    </td>
	</tr>
	<tr>
		<td style='padding:0 0 5 0;' >";

$Contents .= "
		</td>
		<td style='text-align:right;'>
		<!--a href='?mmode=".$mmode."&list_view_type=calendar&list_type=".$list_type."&parent_group_ix=".$parent_group_ix."&group_ix=".$group_ix."&dp_ix=".$dp_ix."&charger_ix=".$charger_ix."&department=".$department."'>달력</a> | <a href='?mmode=".$mmode."&list_view_type=list&list_type=".$list_type."&list_type=".$list_type."&parent_group_ix=".$parent_group_ix."&group_ix=".$group_ix."&dp_ix=".$dp_ix."&charger_ix=".$charger_ix."'>리스트</a--></td>
	</tr>
	<tr>
		<td colspan=2>
		<div  ".($list_type == "search" ? "style='display:block;'":"style='display:none;'").">
		<form name=searchmember method='get' style='display:inline;'><!--SubmitX(this);'-->
		<input type='hidden' name=act value='".$act."'>
		<input type='hidden' name=list_type value='".$list_type."'>
		<input type='hidden' name=list_view_type value='".$list_view_type."'>
		<input type='hidden' name=SelectReport value='".$SelectReport."'>
		<input type='hidden' name=vdate value='".$vdate."'>
		<table width=100% cellpadding=0 cellspacing=0>
			<tr>
				<td >";

		$Contents .= SearchBox();

		$Contents .= "
			</td>
		  </tr>
		  <tr height=50>
				<td style='padding:10 20 0 20' colspan=4 align=center><input type=image src='../image/bt_search.gif' align=absmiddle  > <!--a href=\"javascript:mybox.service('addressbook_add.php?code_ix=','10','450','600', 4, [], Prototype.emptyFunction, [], 'HOME > 회원관리 > 지식대상추가');\">지식 대상추가</a--></td>
			</tr>
		</table><br>
		</form>
		</div>
		</td>
	</tr>


<tr>
	<td colspan=2>
<form name='list_frm' method='POST' onsubmit='return BatchSubmit(this);' action='kt_sms.act.php' target='act' style='display:inline;'><!---->
<input type='hidden' name='idx[]' id='idx'>
<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
<div id='result_area' style='display:inline;width:100%;float:left;'>";

$inner_view .= "
<table width='100%' border='0' cellpadding='0' cellspacing='1' bgcolor='#c0c0c0'  class='list_table_box'>
  <col width=4%'>


  <col width='*'>
  <col width='12%'>
  <col width='14%'>
  <col width='12%'>
  <tbody>
  <tr height='28' bgcolor='#ffffff'>
    <td class='s_td' style='text-align:center;padding:0px;'><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>



    <td align='center' class='m_td'><font color='#000000'><font color='#000000'><b><a href='?mmode=".$mmode."&list_view_type=".$list_view_type."&list_type=".$list_type."&parent_group_ix=".$parent_group_ix."&group_ix=".$group_ix."&charger_ix=".$charger_ix."&dp_ix=".$dp_ix."&orderby=group_name&ordertype=".($ordertype == "desc" ? "asc":"desc")."'>지식구분</a></b></font>/<b>지식</b></font></td>
    <!--td align='center' class='m_td'><font color='#000000'><b>점수</b></font></td-->
    <td align='center' class='m_td'><font color='#000000'><b>담당자</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b><a href='?mmode=".$mmode."&list_view_type=".$list_view_type."&list_type=".$list_type."&parent_group_ix=".$parent_group_ix."&group_ix=".$group_ix."&charger_ix=".$charger_ix."&dp_ix=".$dp_ix."&orderby=dday&ordertype=".($ordertype == "desc" ? "asc":"desc")."'>등록일자</a></b></font></td>
    <td align='center' class='e_td'><font color='#000000'><b>관리</b></font></td>
  </tr>";



	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;

		if($db->dt[sdate] <= date("Ymd") && date("Ymd") <= $db->dt[dday]){
			$work_title_css = "color:#000000;";
		}else{
			$work_title_css = "color:gray;";
		}
		if($db->dt[dday] < date("Ymd") && $db->dt[status] != "WC"){
			$work_title_css = "color:red;";
		}
$inner_view .= "
  <tr height='33' bgcolor='#ffffff' id='row_".$db->dt[idx]."' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor='#ffffff'\">
    <td class='list_box_td list_bg_gray' align='center' ><input type=checkbox name=idx[] id='idx' value='".$db->dt[idx]."'></td>

    <td class='list_box_td point' align='left' style='padding:3px 0 3px 5px;line-height:160%;text-align:left;' onmouseover=\"$('#magnifier_".$db->dt[idx]."').show()\" onmouseout=\"$('#magnifier_".$db->dt[idx]."').hide()\">
		<div class=small style='font-weight:normal;font-weight:bold;'>".$group_name."</div>
		<div style='float:left;padding-right:20px;'><a href=\"kms_read.php?mmode=&idx=".$db->dt[idx]."\"  >	".$db->dt[data_name]." </a></div>
		
	</td>

	<td class='list_box_td list_bg_gray' align='center' ><div id='charger_".$db->dt[idx]."'>".$db->dt[charger]."</div><div id='s_loading_".$db->dt[idx]."' style='display:none;'><img src='/admin/images/indicator.gif' border=0></div></td>
	<td class='list_box_td' align='center' nowrap>".$db->dt[regdate]." </td>
	<td class='list_box_td list_bg_gray' align=center valign=middle nowrap>";
	if($admininfo[charger_ix] == $db->dt[charger_ix] || $admininfo[charger_ix] == $db->dt[reg_charger_ix]){
	$inner_view .= "
		<a href=\"javascript:PopSWindow('work_add.php?mmode=pop&idx=".$db->dt[idx]."',680,750,'work_info')\"><img src='../image/btc_modify.gif' border=0></a>";
	}else{
	$inner_view .= "-";
	}
	if($admininfo[charger_ix] == $db->dt[reg_charger_ix]){
$inner_view .= "
	<a href=\"javascript:DeleteWorkList('".$db->dt[idx]."')\"><img src='../image/btc_del.gif' border=0></a>";
	}
$inner_view .= "
	</td>
  </tr>
  ";

	}

if (!$db->total){

$inner_view .= "
  <tr height=50>
    <td colspan='7' align='center' bgcolor='#ffffff'>등록된 데이타가 없습니다.</td>
  </tr>";

}

$inner_view .= "
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0'  align='left' >
  <tr height='40'>
    <td colspan=2 align=left bgcolor='#ffffff' style='padding:5px;'>
	<a href=\"javascript:alert('기능 준비중입니다.');\"><img src='/admin/image/bt_all_del.gif'></a>
    </td>
    <td  colspan='4' align='right' bgcolor='#ffffff' style='padding-right:10px;'>&nbsp;".$str_page_bar."&nbsp;</td>
  </tr>
  </tbody>
</table>


";

$Contents .= $inner_view."	</div></td>
	</tr>
</table><br><br><br>
<div id='contents_box' style='display:none;vertical-align:top'>
<table class='tooltip' border=0 cellpadding=0 cellspacing=0 style='width:350px;height:0px;display:block;' >
	<col width='6px'>

	<col width='*'>
	<col width='14px'>
	<tr>
		<th class='tooltip_01'></th>
		<td class='tooltip_02' ></td>
		<th class='tooltip_03'></th>
	</tr>

	<tr>
		<th class='tooltip_04' style='vertical-align:top'><div style='position:absolute'><div style='position:relative;z-index:10;left:-14px;'><img src='../images/common/tooltip01/bg-tooltip_04_la.png'></div></div></th>
		<td class='tooltip_05' rowspan=2 valign=top style='padding:5px 5px 5px 5px;font-weight:bold;font-size:12px;line-height:150%;text-align:left;' >
		<table style='width:100%;margin-bottom:5px;'>
			<col width='30%'>
			<col width='*'>
			<col width='30%'>
			<tr><td><!--img src='../image/btc_del.gif' border=0--></td><td align=center><b style='color:#ffffff;font-size:12px;'>편집</b></td><td align=right><img src='../image/btc_del.gif' border=0 onclick=\"$('#contents_box').hide();\" style='cursor:pointer;'></td></tr>
		</table>
		<table class='box_12' cellspacing=0 cellpadding=0 style='width:100%;height:100%;display:block;' >
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05'  valign=top style='padding:5 5 5 5;background-color:#ffffff;color:gray' id='contents_desc'>
				loading...
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
		<th class='tooltip_06'></th>
	</tr>
	<tr>
		<th class='tooltip_04'></th>
		<th class='tooltip_06'></th>
	</tr>
	<tr>
		<th class='tooltip_07'></th>
		<td class='tooltip_08'></td>
		<th class='tooltip_09'></th>
	</tr>
</table>
</div>
";
//exit;
//$category_str ="<div class=box id=img3  style='width:155px;height:375px;overflow:auto;'>".Category()."</div>";

if($mmode == "pop"){
	$P = new ManagePopLayOut();

	$P->addScript = "<script type='text/javascript' src='kms.js'></script>".$Script;
	$P->OnloadFunction = "";
	$P->strLeftMenu = kms_menu();
	$P->Navigation = "KMS 관리 > 내 지식분류";
	$P->title = "내 지식분류";
	$P->strContents = $Contents;
	$P->prototype_use = false;
	echo $P->PrintLayOut();

}else if($mmode == "inner_list"){
	echo $inner_view;
}else{
	$P = new LayOut();
	$P->addScript = "<script type='text/javascript' src='kms.js'></script>".$Script;
	$P->OnloadFunction = "";
	$P->strLeftMenu = kms_menu();
	$P->Navigation = "KMS 관리 > 내 지식분류";
	$P->title = "내 지식분류";
	$P->strContents = $Contents;
	$P->prototype_use = false;
	//$P->footer_menu = footMenu()."".footAddContents();
	echo $P->PrintLayOut();
}


function getFirstDIV($mdb, $selected, $object_id='parent_group_ix', $depth=1, $property="disabled"){
	$mdb = new Database;

	$sql = 	"SELECT wg.*
			FROM work_group wg
			where group_depth = 1
			group by group_ix ";
	//echo $sql;
	$mdb->query($sql);

	$mstring = "<select name='$object_id' id='$object_id' $property>";
	$mstring .= "<option value=''>1차그룹</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[group_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[group_ix]."' selected>".$mdb->dt[group_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[group_ix]."'>".$mdb->dt[group_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}


function KMS_Tab($total){
	global $list_type;
$mstring = "
<div class='tab' >
					<table class='s_org_tab' border=1 style='width:100%;padding:0px;'>
					<col width='*'>
					<col width='200px'>
					<col width='100px'>
					<tr>
						<td class='tab' >

							<table id='tab_02'  ".($status == "" && $list_type == "" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?mmode=$mmode&list_view_type=".$list_view_type."'\">
								전체지식 ";
								/*
				foreach($work_status  as $key => $value){
					$Contents .= "<input type='checkbox' name='work_status' id='work_status_".($key)."' value='".($key)."'><label for='work_status_".($key)."'>".$value."</label>";
				}
*/
				$mstring .= "	</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' ".($list_type == "before" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?mmode=$mmode&list_view_type=".$list_view_type."&list_type=before'\" >베스트 지식</td>
								<th class='box_03'></th>
							</tr>
							</table>

							<table id='tab_04' ".($list_type == "myjob" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?mmode=$mmode&list_view_type=".$list_view_type."&list_type=myjob'\">내지식</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_05' ".($list_type == "mydepartment" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?mmode=$mmode&list_view_type=".$list_view_type."&list_type=mydepartment'\">
									내부서지식
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_06' ".($list_type == "today" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?mmode=$mmode&list_view_type=".$list_view_type."&list_type=today'\">오늘의 지식</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_07' ".($list_type == "search" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?mmode=$mmode&list_view_type=".$list_view_type."&list_type=search'\">지식 상세 검색</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td align=left style='vertical-align:bottom;padding:0 0 7px 0;'><b></b></td>
						<td style='text-align:right;vertical-align:bottom;padding:0 0 7px 0'>

							총건수 :&nbsp;<b>".$total." 건</b>
						</td>
					</tr>
					</table>
				</div>";

	return $mstring;

}


function SearchBox(){
	global $admininfo, $mdb, $work_status, $charger_ix, $dp_ix;
	global $sdate , $edate, $dday_sdate, $dday_edate, $regdate, $dday;

$mstring .= "

<table class='box_shadow' cellspacing=0 cellpadding=0 style='width:100%;height:100%;display:block;' >
	<tr>
		<th class='box_01'></th>
		<td class='box_02'></td>
		<th class='box_03'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<td class='box_05'  valign=top style='width:100%;' >
 		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='input_table_box '>
		<col width='20%' />
		<col width='*' />
		    <tr>
				<td class='input_box_title'>지식 그룹 </td>
				<td class='input_box_item' style='padding-left:5px;'>
				".getCampaignGroupInfoSelect('parent_group_ix', '1 차그룹',$parent_group_ix, $parent_group_ix, "select", 1, " onChange=\"loadWorkGroup(this,'group_ix')\" ")."
				".getCampaignGroupInfoSelect('group_ix', '2 차그룹',$parent_group_ix, $group_ix, "select", 2)."

				</td>
			</tr>
		    <tr height=27>
		      <td class='input_box_title'>조건검색 </td>
		      <td class='input_box_item' style='padding-left:5px;'>
			  <table>
				<tr>
					<td>
					  <select name=search_type>
								<option value='data_name' ".CompareReturnValue("mobile",$search_type,"selected").">지식내용 + 지식상세</option>
								<option value='charger' ".CompareReturnValue("user_name",$search_type,"selected").">담당자명</option>
					  </select>
					 </td>
					 <td>
					  <input type=text name='search_text' class='textbox' value='".$search_text."' style='width:250px' >
					 </td>
				</tr>
			  </table>
		      </td>
		    </tr>

		    ";

$selectd_date = date("Ymd", time());
$today = date("Ymd", time());
$vyesterday = date("Ymd", time()-84600);
$voneweekago = date("Ymd", time()-84600*7);
$vtwoweekago = date("Ymd", time()-84600*14);
$vfourweekago = date("Ymd", time()-84600*28);
$vyesterday = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2),substr($selectd_date,6,2),substr($selectd_date,0,4))-60*60*24);
$voneweekago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2),substr($selectd_date,6,2),substr($selectd_date,0,4))-60*60*24*7);
$v15ago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2),substr($selectd_date,6,2),substr($selectd_date,0,4))-60*60*24*15);
$vfourweekago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2),substr($selectd_date,6,2),substr($selectd_date,0,4))-60*60*24*28);
$vonemonthago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2)-1,substr($selectd_date,6,2)+1,substr($selectd_date,0,4)));
$v2monthago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2)-2,substr($selectd_date,6,2)+1,substr($selectd_date,0,4)));
$v3monthago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2)-3,substr($selectd_date,6,2)+1,substr($selectd_date,0,4)));

 $mstring .= "
		    <tr>
		      <td class='input_box_title'><label for='regdate'>등록일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
		      <td class='input_box_item' style='padding-left:5px;'>
		      	<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
					<tr>
						<TD width=80 nowrap>
						<input type='text' name='sdate' class='textbox' style='width:80px;text-align:center;' id='start_datepicker' value='$sdate'>
						</TD>
						<TD width=20 align=center> ~ </TD>
						<TD width=80 nowrap>
						<input type='text' name='edate' class='textbox' style='width:80px;text-align:center;' id='end_datepicker' value='$edate'>
						</TD>
						<TD style='padding:4px 0 0 5px'>
							<a href=\"javascript:select_date('$today','$today',1);\"><img src='../image/b_btn_s_today.gif'></a>
							<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../image/b_btn_s_yesterday.gif'></a>
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

	";

	return $mstring;
}
/*


CREATE TABLE `work_userinfo` (
  `code` varchar(32) NOT NULL ,
  `google_mail` varchar(100) DEFAULT NULL,
  `google_pass` varchar(100) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`code`)
) TYPE=MyISAM DEFAULT CHARSET=utf8

CREATE TABLE `shop_addressbook` (
  `idx` int(8) unsigned NOT NULL auto_increment,
  `com_div` varchar(20) default '',
  `div` varchar(30) default '',
  `url` varchar(255) default NULL,
  `page` int(8) default '0',
  `com_name` varchar(50) default NULL,
  `charger` varchar(50) default NULL,
  `phone` varchar(50) default NULL,
  `fax` varchar(20) default NULL,
  `mobile` varchar(20) default NULL,
  `email` varchar(50) default NULL,
  `homepage` varchar(50) default NULL,
  `com_address` varchar(50) default NULL,
  `mail_yn` enum('0','1') default '1',
  `marketer` varchar(100) default '',
  `memo` text,
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idx`),
  KEY `regdate` (`regdate`)
) TYPE=MyISAM DEFAULT CHARSET=utf8


CREATE TABLE `shop_sms_address` (
  `sa_ix` int(8) NOT NULL AUTO_INCREMENT,
  `sg_ix` int(8) DEFAULT NULL,
  `sa_name` varchar(25) NOT NULL DEFAULT '0',
  `sa_mobile` varchar(15) DEFAULT '',
  `sa_sex` enum('M','F')  DEFAULT NULL,
  `sa_etc` varchar(255) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`sa_ix`),
  KEY `regdate` (`regdate`),
) ENGINE=MyISAM  DEFAULT CHARSET=utf8

CREATE TABLE `shop_sms_history` (
  `sh_ix` int(8) NOT NULL AUTO_INCREMENT,
  `send_phone` varchar(50) DEFAULT NULL,
  `dest_mobile` varchar(15) DEFAULT '',
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`sg_ix`)
}
*/
?>