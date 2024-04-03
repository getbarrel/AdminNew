<?
include("../class/layout.class");


$db = new Database;


$page_path = getDesignTempletPath($pcode, $depth);
$db->query("select cname from ".TBL_SHOP_LAYOUT_INFO." where  cid ='$pcode' ");
$db->fetch();
$page_title = $db->dt[cname];


$page_path = getDesignTempletPath($pcode, $depth);

$db->query("select * from ".TBL_SHOP_DESIGN." where mall_ix = '".$admininfo[mall_ix]."' and pcode ='$pcode' ");
//echo ("select * from ".TBL_SHOP_DESIGN." where mall_ix = '".$admininfo[mall_ix]."' and pcode ='$pcode' ");
$db->fetch();

if($db->total){	
	$page_name = $db->dt[contents];
	$contents_add = $db->dt[contents_add];
	//$page_title = $db->dt[page_title];
	$templet_name = $db->dt[templet_name];
	//$page_path = $db->dt[page_path];
	$page_link = $db->dt[page_link];
	$page_desc = $db->dt[page_desc];
	$page_help = $db->dt[page_help];
	$page_type = $db->dt[page_type];	
	$layout_act = "update";
}else{
//	if($page_name == ""){
//		$page_name = "ms_index.htm";
//	}
	$page_type = "A";
	$layout_act = "insert";
}
$templet_path = $admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete];
$templet_file_path = $templet_path."/".$page_path."/".$page_name;
$thisfile = load_template($DOCUMENT_ROOT.$templet_file_path);
$thisfile_convert = eregi_replace("{templet_src}",$templet_path, $thisfile);
$thisfile_convert = eregi_replace("@_templet_path",$templet_path, $thisfile_convert);
$thisfile_convert = str_replace("</textarea>","&lt;/textarea&gt;", $thisfile_convert);
$thisfile_convert = str_replace("<textarea","&lt;textarea", $thisfile_convert);
$thisfile_convert = str_replace("</form>","&lt;/form&gt;", $thisfile_convert);
$thisfile_convert = str_replace("<form","&lt;form", $thisfile_convert);

$thisfile = str_replace("</textarea>","&lt;/textarea&gt;", $thisfile);
$thisfile = str_replace("<textarea","&lt;textarea", $thisfile);
$thisfile = str_replace("</FORM>","&lt;/FORM&gt;", $thisfile);
$thisfile = str_replace("<FORM","&lt;FORM", $thisfile);

if($pcode != ""){
	$page_path_string = $admin_config[mall_use_templete]."/$page_path/".$page_name;
}

$Script = '
<style>
UL {
	LIST-STYLE-IMAGE: none; LIST-STYLE-TYPE: none
}
LI{
	list-style-tyle:none;
	margin:0px;
}
.sortabledemo {
	BORDER-RIGHT: #aaaaaa 1px solid; PADDING-RIGHT: 5px; BORDER-TOP: #aaaaaa 1px solid; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; BORDER-LEFT: #aaaaaa 1px solid; PADDING-TOP: 5px; BORDER-BOTTOM: #aaaaaa 1px solid;
	padding:0px;margin:0px;
}
.sortabledemo .green {
	BORDER-RIGHT: #c5dea1 1px solid; BORDER-TOP: #c5dea1 1px solid; BACKGROUND: #ecf3e1; MARGIN: 10px; BORDER-LEFT: #c5dea1 1px solid; CURSOR: move; BORDER-BOTTOM: #c5dea1 1px solid
}
.sortabledemo .orange {
	BORDER-RIGHT: #e8a400 1px solid; BORDER-TOP: #e8a400 1px solid; MARGIN: 10px; BORDER-LEFT: #e8a400 1px solid; CURSOR: move; BORDER-BOTTOM: #e8a400 1px solid; BACKGROUND-COLOR: #fff4d8
}
.handle {
	COLOR: white; BACKGROUND-COLOR: #e8a400
}
.item {
	BORDER-RIGHT: #3b6ea5 1px solid; 
	BORDER-TOP: #3b6ea5 1px solid; 
	BORDER-LEFT: #3b6ea5 1px solid; 
	CURSOR: move; BORDER-BOTTOM: #3b6ea5 1px solid;	
}
.item .subject {
	BACKGROUND: #4678ad; HEIGHT: 25px
}
</style>';


$db = new Database;

//$db->query("select mall_raw1 from ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[company_id]."'");
//$db->fetch();
$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' style='vertical-align:top'>
	<tr height=30>
	    <td align='left' colspan=3 > ".GetTitleNavigation("페이지 상세 디자인", "디자인관리 > 페이지 상세 디자인 ")."</td>
	</tr>
</table>
<div>
	<input type='radio' name='layout_type' value='layout_header_frame' id='select_header'><label id='select_header'>상단</label>
	<input type='radio' name='layout_type' value='layout_left_frame' id='select_left'><label id='select_left'>좌측</label>
	<input type='radio' name='layout_type' value='layout_footer_frame' id='select_footer'><label id='select_footer'>하단</label>
	
	<input type='button' value='엘리먼트 추가' onclick=\"addElement('$templet_path/layout/header/header_top.htm');\">
	
</div>

<table border=1>
	<tr>
		<td colspan=2>
		<ul class='sortabledemo' id='layout_header_frame' style='height:100px;width:900px;'>

		</ul>
		</td>
	</tr>
	<tr>
		<td style='float:left;width:180px;display:block;' >
		<ul class='sortabledemo' id='layout_left_frame' style='height:550px;width:200px;'>

		</ul>	
		</td>
		<td style='float:left;width:700px;'>
			<ul class='sortabledemo' id='layout_contents_frame' style='height:550px;width:700px;'>

		</ul>	
		</td>
	</tr>
	<tr>		
		<td colspan=2 style='float:left;width:900px;'>		
		<ul class='sortabledemo' id='layout_footer_frame' style='height:100px;width:900px;'>

		</ul>
		</td>
	</tr>
</tr>
</table>
	

<div id='template' class='item' style='display:none;'>
	<div class='subject'>
		<div style='text-align:right;'>
			<a href=\"javascript:layerControl('#id#', 'none')\">-</a>
			<a href=\"javascript:layerControl('#id#', '')\">ㅁ</a>
			<a href=\"javascript:layerControl('#id#', 'exit')\">x</a>
			&nbsp;&nbsp;&nbsp;
		</div>
		<div id='subject_#id#'>
		</div>
	</div>
	<table style='width:100%;table-layout:fixed;'>
	<tr>
	<td>
	<div id='content_#id#' class='content' #display# >

	</div>
	</td>
	</tr>
	</table>
</div>


<pre id='layout_header_frame_debug'></pre>
<pre id='layout_left_frame_debug'></pre>
<pre id='layout_contents_frame_debug'></pre>
<pre id='layout_footer_frame_debug'></pre>



<script type=\"text/javascript\">
var personalSort	= new personalSorts;
personalSort.create(	\"layout_header_frame,layout_left_frame,layout_contents_frame,layout_footer_frame\", 
						{	\"callBack\":\"loadXml\", \"tmpBody\":\"content\" }, 
						{	\"dropOnEmpty\":true, \"constraint\":false,
							\"onChange\":function(){
								personalSort.makeCookies();
							}
						} 
					);

personalSort.parseAsync( getCookie( \"layout_header_frame\" ) );
personalSort.parseAsync( getCookie( \"layout_left_frame\" ) );
personalSort.parseAsync( getCookie( \"layout_contents_frame\" ) );
personalSort.parseAsync( getCookie( \"layout_footer_frame\" ) );
</script>
 ";

$LO = new LayOut;
$LO->addScript = "<script src='../js/prototype_layout.js' type='text/javascript'></script><script src='src/scriptaculous.js' type='text/javascript'></script><script src='scripts/personalSort.js' type='text/javascript'></script>\n<script src='scripts/ajaxControl.js' type='text/javascript'></script><script src='layout_drag.js' type='text/javascript'></script>\n$Script";
$LO->OnloadFunction = "";//showSubMenuLayer('storeleft');

$LO->strLeftMenu = design_menu();
$LO->strContents = $Contents;
$LO->prototype_use = false;
$LO->PrintLayOut();

?>