<?
include("../class/layout.class");


$db = new Database;

$page_path = getDesignTempletPath($pcode, $depth);
$db->query("select cname, basic_link from ".TBL_SHOP_LAYOUT_INFO." where  cid ='$pcode' ");
$db->fetch();
$page_title = $db->dt[cname];
$basic_link = $db->dt[basic_link];

$page_path = getDesignTempletPath($pcode, $depth);

$db->query("select * from ".TBL_SHOP_DESIGN." where mall_ix = '".$admininfo[mall_ix]."' and pcode ='$pcode' ");
//echo ("select * from ".TBL_SHOP_DESIGN." where mall_ix = '".$admininfo[mall_ix]."' and pcode ='$pcode' ");
$db->fetch();

if($db->total){
	$page_name = $db->dt[contents];
	$contents_add = $db->dt[contents_add];
	//$page_title = $db->dt[page_title];
	$templet_name = $db->dt[templet_name];

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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 TRANSITIONAL//EN">

<head>
<meta http-equiv="content-type" content="text/html; charset=euc-kr" />
<meta name="expires" content="0" />
<meta http-equiv="imagetoolbar" content="no" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<link rel="stylesheet" type="text/css" href="cadvance.css">
<script type="text/javascript" src="design.js"></script>
<script type="text/javascript">
<!--
function view(frm){
	//alert(code.page_contents.innerHTML);
	display.innerHTML = code.page_contents.innerHTML
	frm.action = "view.php";
	frm.target="display";
	frm.submit();
}

function view_all(basic_link){

	if(basic_link){
		document.getElementById('display').src = basic_link;
	}else{
		document.getElementById('display').src = "/main/page.php?pgid=<?=$pcode?>";
	}
}

function save_design(frm){
	if(opener.info_input.page_contents){
		opener.info_input.page_contents.value = code.page_contents.value;
	}

	frm.action = "design.act.php";
	frm.target="act";
	frm.submit();
}
// -->
</script>
<style type="text/css">
<!--
textarea{
width:100%;
height:100%;
background-image:url(../images/textarea_bg.gif);
background-attachment:scroll;
border:1px solid #efefef;
border-right-width:4px;
border-top-width:5px;
overflow:auto;
font:11px/18px verdana;
padding-left:3px;
color:#000000;
}

iframe{
width:100%;
height:100%;
border:1px solid #EBECEB;
border-top-width:5px;
position:relative;
top:1px;
}

input{
padding-top:2px;
margin:0px;
width:100%;
height:100%;
background-color:#EBECEB;
font:bold 11px verdana;
border:2px solid silver;
color:#000000;
cursor:hand;
}
-->
</style>
</head>
<body scroll="auto" class="margin" style="width:100%;height:100%;overflow:hidden;background-image:none;margin:0px;background-color:transparent;" leftmargin="0" topmargin="0" oncontextmenu="cutBubble()" onload="code.page_contents.value=opener.info_input.page_contents.value "><!--code.page_contents.focus();document.execCommand('paste');--><!--document.execCommand('paste');code.page_contents.innerHTML=opener.document.getElementById('page_contents_convert').innerHTML;-->
<form action="view.php" id="code" name="code" method="post" target="display">
<input type='hidden' name=design_act value='update'>
<input type='hidden' name=pcode value='<?=$pcode?>'>
<input type='hidden' name=page_path value='<?=$page_path?>'>
<input type='hidden' name=page_name value='<?=$page_name?>'>
<input type='hidden' name=tab_no value='02'>
<input type='hidden' name=mall_ix value='<?=$admininfo[mall_ix]?>'>
<table width="100%" height="100%" cellspacing="0" border="0" cellpadding="0" style="border:5px solid #FF7400;">
<tr>
<td style="padding:0px;height:25px;position:relative;" bgcolor="tomato">
	<table width=100% height=100% bgcolor=#ffffff>
		<tr height="100%">
			<td width=33%><input type="button" onclick="view(document.code);" value="미리 보기(작업페이지 보기)" style="height:21px;" /></td>
			<?if($pcode){?>
			<td width=33%><input type="button" onclick="view_all('<?=$basic_link?>');" value="미리 보기(전체보기)" style="height:21px;" /></td>
			<?}?>
			<td width=33%><input type="button" onclick="save_design(document.code);" value="저장하기" style="height:21px;" /></td>
		</tr>
	</table>
</td>
<td style="padding:0px;height:25px;position:relative;">
	<input type="button" id="close" name="close" onclick="window.close()" value="창 닫기" style="height:25px;" />
</td>
</tr>
<tr>
<td width="50%" height="92%">
<textarea id="page_contents" name="page_contents" style="color:#000000;" wrap="off" onkeydown="textarea_useTab( this, event );"  >
</textarea>
</td>
</form>
<td width="50%" height="92%">
<iframe src="view.php" id="display" name="display" frameborder="0" framespacing="0" scrolling="yes"></iframe>
</td>
</tr>
<tr>
<td colspan="2" align="center" bgcolor="gray" style="padding:0px;height:30px;position:relative;top:1px;font-weight:bold;color:#ffffff;">
 바꾸고 싶은 내용이 있으면 수정한 다음 다시 실행 할 수 있습니다.
</td>
</tr>
</table>
<iframe name='act' id='act' width=0 height=0 frameborder=0></iframe>
</body>
</html>
