<?
include("../class/layout.work.class");
include("deepzoom.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("../class/calender.big.class");


$db = new Database;
$mdb = new Database;
if($max == ""){
	$max = 12;
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

if($image_type == "image"){
	$where .= " and chk_deepzoom = '0' ";
}else if($image_type == "deepzoom"){
	$where .= " and chk_deepzoom = '1' ";
}

if($group_ix != ""){
	$where .= " and (dig.group_ix in(".$group_ix.") or dig.parent_group_ix in (".$group_ix.") )  ";
}else if($_COOKIE["dynatree-image_group-select"]){
	$where .= " and (dig.group_ix in ('".str_replace(",","','",$_COOKIE["dynatree-image_group-select"])."') )  ";
}

//echo $_COOKIE["dynatree-image_group-select"];

//$sql = "select * from mykms where uid = '".$admininfo[charger_ix]."' ";

$sql = "select di.* from deepzoom_image di, deepzoom_image_group dig, common_member_detail cmd
		where di.group_ix = dig.group_ix and di.charger_ix = cmd.code and di.charger_ix = '".$admininfo[charger_ix]."'  $where   ";

//echo $sql; 
$db->query($sql);
$total = $db->total;
$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&list_type=$list_type&group_ix=$group_ix&dp_ix=$dp_ix&charger_ix=$charger_ix&birthday_yn=$birthday_yn&sex=$sex&mailsend_yn=$mailsend_yn&smssend_yn=$smssend_yn&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");

//echo $total;


$sql = $sql." order by regdate desc LIMIT $start, $max";
//echo $sql;
$db->query($sql);



$Script = "
<script  id='dynamic'></script>

 <script language='javascript'>

function init(){

	var frm = document.searchmember;		
	
	";
if($regdate != "1"){
$Script .= "
	frm.sdate.disabled = true;
	frm.edate.disabled = true;";
}

$Script .= "	

}


</script>";


$Contents = "
<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("이미지 관리", "이미지관리 > 이미지 목록 ")."
	
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

<div id='loading' style='display:none;border:0px solid red;width:100%;height:100%;padding-top:13px;'><img src='/admin/images/indicator.gif' border=0></div>";

$Contents .= "

<style>
	.fader{opacity:0;display:none;height:27px;}
</style>
<table width=100% cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td align='left' colspan=4 style='padding:0px 0px 20px 0px;'> 
	    	".WorkTab($total)."
	    </td>
	</tr>
	<tr>
		<td style='padding:0 0 5 0;' >";

$Contents .= "
		</td>
		<td style='text-align:right;'> 
		</td>
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
				<td style='padding:10 20 0 20' colspan=4 align=center><input type=image src='../image/bt_search.gif' align=absmiddle  > <!--a href=\"javascript:mybox.service('addressbook_add.php?code_ix=','10','450','600', 4, [], Prototype.emptyFunction, [], 'HOME > 회원관리 > 딥줌대상추가');\">딥줌 대상추가</a--></td>		    	
			</tr>
		</table><br>
		</form>
		</div>
		</td>
	</tr>


<tr>
	<td colspan=2>
<!--<form name='list_frm' method='POST' onsubmit='return BatchSubmit(this);' action='kt_sms.act.php' target='act' style='display:inline;'>
<input type='hidden' name='idx[]' id='idx'>
<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>-->
<div id='result_area' style='display:inline;width:100%;float:left;'>";

//print_r($admininfo);
if($total == 0){
	$inner_view .= "<div style='width:850px;height:450px;float:left;padding:7px;text-align:center;padding-top:300px;' >검색된 이미지 정보가 없습니다.</div>";
}else{
	for ($i = 0; $i < $db->total; $i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;

		$inner_view .= "<div style='width:250px;height:270px;float:left;padding:7px;text-align:center;' id='image_".$db->dt[di_ix]."'>
						<div style='padding:3px;display:block;'><img src='".$admininfo[mall_data_root]."/deepzoom/".$db->dt[di_ix]."/s_".$db->dt[deepzoom_file]."' ></div>
						<div style='padding:3px;display:block;'>".$db->dt[deepzoom_name]."</div>
						<div style='cursor:hand;display:inline;' onclick=\"DeleteIamge('".$db->dt[di_ix]."')\"><img src='../images/orange/image_del.gif'></div>";
		if($db->dt[chk_deepzoom] == "1"){
		$inner_view .= "
						<div style='padding:0px 3px;display:inline;cursor:pointer' onclick=\"full_popup('sample.php?di_ix=".$db->dt[di_ix]."')\"><img src='../images/orange/deepzoom_view.gif'></div>
						<div style='display:inline;cursor:pointer' onclick=\"DeepZoomUrlCopy('".$db->dt[di_ix]."')\"><img src='../images/orange/deepzoom_copy.gif'></div>";
		}
		$inner_view .= "</div>";
	}
}
$inner_view .= "<div style='width:850px;height:50px;float:left;padding:7px;text-align:center;padding-top:30px;' >".$str_page_bar."</div>";

$Contents .= $inner_view."	</div></td>
	</tr>
</table><br><br><br>


<div id='deepzoomreg2' class='blockUI' style='display:none;vertical-align:top;background-color:transparent ;' >
<span onclick=\"alert($(this).parent().html());\">확인</span>
</div>
<div id='deepzoomreg' class='blockUI' style='display:none;vertical-align:top;background-color:transparent ;' >
<table class='layer_box' border=0 cellpadding=0 cellspacing=0 style='width:550px;height:0px;display:block;background-color:transparent ;' >
	<col width='11px'>
	<col width='*'>
	<col width='11px'>
	<tr>
		<th class='box_01'></th>
		<td class='box_02' ></td>
		<th class='box_03'></th>
	</tr>
	
	<tr>
		<th class='box_04' style='vertical-align:top'></th>
		<td class='box_05' rowspan=2 valign=top style='padding:5px 15px 5px 15px;font-weight:bold;font-size:12px;line-height:150%;text-align:left;' >	
			<h1 id=\"check_title\">".GetTitleNavigation("이미지 등록", "이미지 관리 > 이미지 등록 ", false)."</h1>
			<form name='deepzoom_frm' method=post enctype='multipart/form-data' action='deepzoom.act.php' onsubmit='return CheckFormValue(this)' style='display:inline;'><!-- target='act' -->
			<input type='hidden' name='act' value='insert'>
			<div class=\"g_box2 \" >				
				<table width='100%' border='0' align='center' bgcolor='#c0c0c0' cellpadding=4 cellspacing=1>
					<!--tr bgcolor=#ffffff>
						<td align='left' colspan=6 > </td>
					</tr-->
					<tr bgcolor=#ffffff>
						<td style='text-align:left;padding-left:10px' onclick='CheckFormValue(document.deepzoom_frm)'><img src='../image/ico_dot2.gif' align=absmiddle> 이미지 분류 : </td>
						<td style='text-align:left;padding-left:10px'>
						".getDeepZoomGroupInfoSelect('parent_group_ix', '1 차그룹',$parent_group_ix, $parent_group_ix, "select", 1, " onChange=\"loadWorkGroup(this,'group_ix')\" ")."
						".getDeepZoomGroupInfoSelect('group_ix', '2 차그룹',$parent_group_ix, $group_ix, "select", 2)."
				
						</td>
					</tr>
					<tr bgcolor=#ffffff>
						<td style='text-align:left;padding-left:10px'><img src='../image/ico_dot2.gif' align=absmiddle> 이미지 이름 : </td>
						<td style='text-align:left;padding-left:10px'><input type='text' class='textbox'  name='deepzoom_name' style='width:95%' validation='true' title='이미지 이름'></td>
					</tr>
					<tr bgcolor=#ffffff>
						<td style='text-align:left;padding-left:10px'><img src='../image/ico_dot2.gif' align=absmiddle> 이미지  : </td>
						<td style='text-align:left;padding-left:10px'>
							<table>
							<tr>
							<td><input type='file' class='textbox' name='deepzoom_file' validation='true' title='이미지파일' align=absmiddle></td>
							<td><input type='checkbox' name='chk_deepzoom' id='chk_deepzoom' validation='false' title='딥줌생성' value='1'></td>
							<td><label for='chk_deepzoom'>딥줌생성</label></td>
							</tr>
							</table>
							<div class='small' style='text-align:left;'>* 딥중 생성 체크박스를 클릭하시면 딥줌 이미지가 함께 생성됩니다.</div>
						</td>
					</tr>
					<tr bgcolor=#ffffff>
						<td style='text-align:left;padding-left:10px'><img src='../image/ico_dot2.gif' align=absmiddle> 이미지 링크 : </td>
						<td style='text-align:left;padding-left:10px'>
							<input type='text' class='textbox'  name='image_link' style='width:95%' validation='true' title='이미지 링크'>
							<div class='small' style='padding:6px 0 0 0'>갤러리 생성시 이미지 클릭시 이동을 원하는 링크를 입력해주세요</div>
						</td>
					</tr>
				</table>
			</div>
			
			
			<p class=\"btns \" style='text-align:center;'>
				<input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' >
				<a id=\"btnCheck_cancel\" href=\"javascript:LayerClose()\"><img src='../image/b_cancel.gif' border=0 align=absmiddle></a>
			</p>
			</form>
		</td>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
</table>
</div>


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
		<th class='tooltip_04' style='vertical-align:top'><div style='position:absolute'><div style='position:relative;z-i...ndex:10;left:-14px;'><img src='../images/common/tooltip01/bg-tooltip_04_la.png'></div></div></th>
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

//$category_str ="<div class=box id=img3  style='width:155px;height:375px;overflow:auto;'>".Category()."</div>";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
		
	$P->addScript = "<script type='text/javascript' src='../work/js/jquery-1.4.4.min.js'></script>
<script type='text/javascript' src='../work/js/jquery-ui-1.8.6.custom.min.js'></script>
<script type='text/javascript' src='../work/js/ui/ui.core.js'></script>
<script type='text/javascript' src='../work/js/ui/ui.datepicker.js'></script>
<script language='javascript' src='../work/js/jquery.blockUI.js'></script>
<script type='text/javascript' src='common.js'></script>
<script type='text/javascript' src='deepzoom.js'></script>".$Script;
	$P->OnloadFunction = "init();";
	$P->strLeftMenu = deepzoom_menu();
	$P->Navigation = "HOME > 딥줌관리 > 딥줌 목록";
	$P->strContents = $Contents;
	$P->prototype_use = false;
	$P->right_menu = "";
	echo $P->PrintLayOut();
	
}else if($mmode == "inner_list"){
	echo $inner_view;
}else{
	$P = new LayOut();
	$P->addScript = "<script type='text/javascript' src='../work/js/jquery-1.4.4.min.js'></script>
<script type='text/javascript' src='../work/js/jquery-ui-1.8.6.custom.min.js'></script>

<script type='text/javascript' src='../work/js/ui/ui.core.js'></script>
<script type='text/javascript' src='../work/js/ui/ui.datepicker.js'></script>
<script language='javascript' src='../work/js/jquery.blockUI.js'></script>
<script language='javascript' src='../work/js/jquery.cookie.js'></script>
<script type='text/javascript' src='common.js'></script>
<link href='../work/dynatree/skin/ui.dynatree.css' rel='stylesheet' type='text/css' id='skinSheet'>
<script src='../work/dynatree/jquery.dynatree.js' type='text/javascript'></script>
<script type='text/javascript' src='deepzoom.js'></script>".$Script;
	$P->OnloadFunction = "init();";
	$P->strLeftMenu = deepzoom_menu();
	$P->Navigation = "HOME > 이미지 관리 > 이미지 목록";
	$P->strContents = $Contents;
	$P->prototype_use = false;
	$P->right_menu = "";
	$P->footer_menu = $footmenu_str;
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


function WorkTab($total){
	global $image_type;
$mstring = "
<div class='tab' >
					<table class='s_org_tab' border=1 style='width:100%;padding:0px;'>
					<col width='*'>
					<col width='250px'>
					<col width='100px'>
					<tr>
						<td class='tab' >
							
							<table id='tab_02'  ".($image_type == "" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?mmode=$mmode&image_type=".$list_view_type."'\">
								전체이미지 ";
								/*
				foreach($work_status  as $key => $value){
					$Contents .= "<input type='checkbox' name='work_status' id='work_status_".($key)."' value='".($key)."'><label for='work_status_".($key)."'>".$value."</label>";
				}
*/
				$mstring .= "	</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' ".($image_type == "image" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?mmode=$mmode&image_type=image'\" >이미지 only</td>
								<th class='box_03'></th>
							</tr>
							</table>
							
							<table id='tab_04' ".($image_type == "deepzoom" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?mmode=$mmode&image_type=deepzoom'\">딥줌 생성이미지</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<!--table id='tab_05' ".($list_type == "mydepartment" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?mmode=$mmode&list_view_type=".$list_view_type."&list_type=mydepartment'\">
									내부서딥줌
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_06' ".($list_type == "today" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?mmode=$mmode&list_view_type=".$list_view_type."&list_type=today'\">오늘의 딥줌</td>
								<th class='box_03'></th>
							</tr>
							</table-->
							<table id='tab_07' ".($image_type == "search" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?mmode=$mmode&image_type=search'\">딥줌 상세 검색</td>
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
		<td class='box_05'  valign=top style='padding:5 5 5 5' >	
 		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>";

$mstring .= "  
		    <tr height=27>
				<td bgcolor='#efefef' align=center>딥줌 그룹 </td>
				<td align=left style='padding-left:5px;' colspan=3>
				
				
				</td>		  
				
			</tr>
			<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center>조건검색 </td>
		      <td align=left style='padding-left:5px;' colspan=3>
			  <table>
				<tr>
					<td>
					  <select name=search_type>						
								<option value='data_name' ".CompareReturnValue("mobile",$search_type,"selected").">딥줌내용 + 딥줌상세</option>
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
		    <tr hegiht=1><td colspan=4 class='dot-x'></td></tr>
		   
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
		    <tr height=27>
		      <td bgcolor='#efefef' align=center><label for='regdate'>등록일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
		      <td align=left colspan=3 style='padding-left:5px;'>
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
		    <tr hegiht=1><td colspan=4 class='dot-x'></td></tr>
		    
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
CREATE TABLE IF NOT EXISTS `deepzoom_image` (
  `di_ix` int(8) unsigned NOT NULL auto_increment,
  `group_ix` varchar(30) default '',
  `deepzoom_name` varchar(255) NOT NULL,
  `deepzoom_file` varchar(255) default NULL,
  `charger_ix` int(8) NOT NULL,
  `regdate` datetime default NULL,
  PRIMARY KEY  (`di_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;
		

CREATE TABLE `deepzoom_image` (
  `di_ix` varchar(32) NOT NULL ,
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