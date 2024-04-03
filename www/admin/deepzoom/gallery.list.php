<?
include("../class/layout.work.class");
include("gallery.lib.php");

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
//echo $_COOKIE["dynatree-image_group-select"];

//$sql = "select * from mykms where uid = '".$admininfo[charger_ix]."' ";

$sql = "select dgi.* from deepzoom_gallery_info dgi, common_member_detail cmd 
		where dgi.charger_ix = cmd.code and dgi.charger_ix = '".$admininfo[charger_ix]."'  $where   ";

//echo $sql; 
$db->query($sql);
$total = $db->total;
$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&list_type=$list_type&group_ix=$group_ix&dp_ix=$dp_ix&charger_ix=$charger_ix&birthday_yn=$birthday_yn&sex=$sex&mailsend_yn=$mailsend_yn&smssend_yn=$smssend_yn&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");

//echo $total;


$sql = $sql." order by regdate desc LIMIT $start, $max";
//echo $sql;
$db->query($sql);



$Contents = "
<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("갤러리 목록", "갤러리관리 > 갤러리 목록 ")."
	
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
<div id='loading' style='display:none;border:0px solid red;width:100%;height:100%;padding-top:13px;'><img src='/admin/images/indicator.gif' border=0></div>";

$Contents .= "

<style>
	.fader{opacity:0;display:none;height:27px;}
</style>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td colspan=2>

<div id='result_area' style='display:inline;width:100%;float:left;'>";

//print_r($admininfo);
if($total == 0){
	$inner_view .= "<div style='width:870px;height:450px;float:left;padding:7px;text-align:center;padding-top:300px;' >검색된 갤러리 정보가 없습니다.</div>";
}else{
	for ($i = 0; $i < $db->total; $i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;

		$inner_view .= "<div style='width:870px;height:150px;float:left;padding:7px;text-align:left;border:3px solid #efefef;margin:3px;' id='image_".$db->dt[dgi_ix]."'>
						<div style='height:20px;text-align:left;font-weight:bold;float:left;padding:5px 5px 0px 0px;border:0px solid red;vertical-align:bottom;'>갤러리명 : ".$db->dt[gallery_name]."</div>
						<div style='height:25px;float:left;border:0px solid red;'>
						<a href='gallery.php?dgi_ix=".$db->dt[dgi_ix]."'><img src='../images/orange/btn_edit.gif'  align=absmiddle></a> 
						<img src='../images/orange/image_del.gif' style='cursor:hand;' onclick=\"DeleteGallery('".$db->dt[dgi_ix]."')\" > 
						</div>";
		$inner_view .= "<div style='height:25px;float:left;cursor:pointer;padding:0px 3px;' onclick=\"full_popup('gallery_sample.php?dgi_ix=".$db->dt[dgi_ix]."')\"><img src='../images/orange/gallery_view.gif'  align=absmiddle></div>
						<div style='height:25px;clear:right;cursor:pointer;' onclick=\"DeepZoomGalleryCopy('".$db->dt[dgi_ix]."')\"><img src='../images/orange/gallery_copy.gif'  align=absmiddle></div>";
						
		$inner_view .= "<div style='padding:10px;'>".relationGalleryImageList($db->dt[dgi_ix],"clipart")."</div>";
		$inner_view .= "</div>";
	}
}
$inner_view .= "<div style='width:850px;height:50px;float:left;padding:7px;text-align:center;padding-top:30px;' >".$str_page_bar."</div>";

$Contents .= $inner_view."	</div></td>
	</tr>
</table><br><br><br>
";


if($mmode == "pop"){
	$P = new ManagePopLayOut();
		
	$P->addScript = "<script type='text/javascript' src='../work/js/jquery-1.4.4.min.js'></script>
<script type='text/javascript' src='../work/js/jquery-ui-1.8.6.custom.min.js'></script>
<script type='text/javascript' src='../work/js/ui/ui.core.js'></script>
<script type='text/javascript' src='../work/js/ui/ui.datepicker.js'></script>
<script language='javascript' src='../work/js/jquery.blockUI.js'></script>
<script type='text/javascript' src='common.js'></script>
<script type='text/javascript' src='deepzoom.js'></script>".$Script;
	//$P->OnloadFunction = "init();";
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
	//$P->OnloadFunction = "init();";
	$P->strLeftMenu = deepzoom_menu();
	$P->Navigation = "HOME > 갤러리 관리 > 갤러리 목록";
	$P->strContents = $Contents;
	$P->prototype_use = false;
	$P->right_menu = "";
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
								전체갤러리 ";
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