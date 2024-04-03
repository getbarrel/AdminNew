<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include("../product/category.lib.php");
//include_once($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
//include($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");
session_start();

$db = new Database;

if($act == "innerview"){

	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><body>
	<form name='estimatefrm' action='estimate.product.act.php' onsubmit='return CheckValue(this);' method='get' target='act'>
	<input type=hidden name=act value='relations'>
	<input type=hidden name=ecid value=".$ecid.">
	".PrintProductList($cid, $depth,$start,$page,$ecid)."</body>
	</form>
	</html>";		
	echo "
	<Script javascript='language'>
	parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
	</Script>";
	exit;	
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title></title>
<!--link rel="stylesheet" type="text/css" href="../css/pop.css"-->
<LINK REL='stylesheet' HREF='../include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='../css/design.css' TYPE='text/css'>

<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>

<style>
p.title		{ font-weight: bold; color: #ffffff; background: #F66B88; font-size: 14px; text-align: left; padding: 10px; }
td		{ margin: 0px; padding: 0px; border:0px;}
div		{ margin: 0px; padding: 0px; border:0px;}
</style>
<Script Language='JavaScript'>
function setCategory(cname,cid,depth,id){
	//alert(cname +":::"+cid+"::::"+depth+":::"+id);
	//document.location.href='estimate.pop.php?act=innerview&cid='+cid+'&depth='+depth;
	var ecid = '<?=$ecid?>';
	window.frames['act'].location.href='estimate.pop.php?act=innerview&cid='+cid+'&depth='+depth+'&ecid='+ecid;
}


function clearAll(frm){
		for(i=0;i < frm.pid.length;i++){
				frm.pid[i].checked = false;
		}
} 
function checkAll(frm){

       	for(i=0;i < frm.pid.length;i++){
				frm.pid[i].checked = true;
		}
}
function fixAll(frm){
	
	var value = $('input[name=all_fix]').attr('checked');

	if (value == 'checked'){
		
		$('input[name^=pid]').attr('checked',true);
		$('input[name=all_fix]').attr('checked',true);
			
	}else{
		$('input[name^=pid]').attr('checked',false);
		$('input[name=all_fix]').attr('checked',false);
	}
}

</Script>
<script Language="JavaScript">

function CheckValue(frm){
	
	for(i=0;i < frm.pid.length;i++){
		if(frm.pid[i].checked){
			return true;
		}
	}
	alert('추가하실 제품을 한개이상 선택하셔야 합니다.');
	return false;
}

</script>
<script language="javascript">
var language = "korea";
var permit = "permit:";
</script>
<script src="/admin/js/jquery-1.8.3.js"></script>
<script src="/admin/js/jquery-ui.js"></script>
<script language="javascript" src="/admin/js/jquery.combobox.js"></script>
<script language="javascript" src="/admin/js/jquery.blockUI.js"></script>
<script language="javascript" src="/admin/js/jquery.cookie.js"></script>
<script language="JavaScript" src="/admin/js/admin.js"></Script>
<script language="JavaScript" src="/admin/js/auto.validation.js"></Script>
<script language="JavaScript" src="/admin/js/dd.js"></Script>
<script language="JavaScript" src="/admin/js/admin_bottom.js"></Script>
<script language="JavaScript" src="/admin/v3/js/menus_layer.js"></Script>
<script type="text/javascript" language=javascript src="/admin/js/facebox.js"></script>
<script type="text/javascript" src="/admin/js/jquery.numeric.js"></script>
<script type="text/javascript" src="/admin/js/jquery.hotkeys-0.7.9.js"></script>
<script language='JavaScript' src='/admin/js/admin_bottom.js'></Script>
<script language='JavaScript' src='/admin/v3/js/menus_layer.js'></Script>
<script type='text/javascript' language=javascript src='/admin/js/facebox.js'></script>
<script type='text/javascript' src='/admin/js/jquery.numeric.js'></script>

</head>
<body class="POP" topmargin=0 leftmargin=0>
<div class="pop">

		
	<table cellpadding=0 cellspacing=0 width=100%>	
	<tr>
		<td class="top_orange" colspan="2"></td>
	</tr>
	<tr height=35 bgcolor=#efefef>
		<td  colspan="2"> 
			<table width='100%' border='0' cellspacing='0' cellpadding='0' >
				<tr> 
					<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap>
						<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> ⊙ 견적상품 등록하기 
					</td>
					<td width='90%' align='right' valign='top' >
						&nbsp;
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=150 style='border:0px solid gray;'  align=left style='padding:7px;'><?=Category()?></td>
		<form name="estimatefrm" action="estimate.product.act.php" onsubmit="return CheckValue(this);" method="get" target='act'>
		<input type=hidden name=act value='relations'>
		<input type=hidden name=ecid value='<?=$ecid?>'>
		<td style='padding:7px;' valign=top id='product_list'>
			<?=PrintProductList($cid, $depth,$start,$page,$ecid)?>
		</td>
		</form>
	</tr>
	</table>



</div>
 <div id='loading' style='display:none;border:0px solid red;width:0px;height:0px;padding-top:13px;text-align:center;'>
	<table class='layer_box' border=0 cellpadding=0 cellspacing=0 style='width:250px;height:70px;' >
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
			<td class='box_05' rowspan=2 valign=top style='padding:15px 15px 5px 25px;font-size:12px;line-height:150%;text-align:left;' >
			<table>
				<tr>
					<td><img src='/admin/images/indicator_.gif' border=0><!--img src='../images/loading_large.gif' border=0--></td>
					<td style='padding-left:20px;'> 정보를 처리중입니다...</td>
				</tr>
			</table>
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
<div id='layerBg' style='border:0px solid gray;'></div>
<iframe name='act' id='act' width=0 height=0 frameborder=0></iframe>
</body>
</html>

<?


function PrintProductList($cid, $depth,$start,$page,$ecid){
	global $start,$page, $orderby, $admininfo ;
	
	$max = 10;
	
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
		
	$db = new Database;
	$sql = "SELECT distinct p.id,p.pname, p.sellprice, p.reserve FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = 1 and p.id = r.pid";
	$db->query($sql);

	$total = $db->total;
	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.reserve  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = 1 and  p.id = r.pid order by vieworder limit $start,$max";
	$db->query($sql);		

	$mString = "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver style='font-size:10px;'>";	
	$mString .= "<tr align=center bgcolor=#efefef height=25><td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.estimatefrm)'></td><td class=m_td>상품코드</td><td class=m_td>상풍명</td><td class=m_td>가격</td><td width=70 class=e_td>삭제</td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=5 align=center>등록된 상품 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);			
			$mString .= "<tr height=27 bgcolor=#ffffff>
						<td class=table_td_white align=center><input type=checkbox class=nonborder id='pid' name=pid[] value='".$db->dt[id]."'></td>
						<td class=table_td_white align=center>".$db->dt[pcode]."</td>
						<td class=table_td_white>".cut_str($db->dt[pname],40)."</td>
						<td align=right>".number_format($db->dt[sellprice])." 원 </td>
						<td class=table_td_white align=center><a href=\"JavaScript:deleteCategory('delete','".$db->dt[erid]."','$pid')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td>
						</tr>";
			$mString .= "<tr height=1><td colspan=5 background='../image/dot.gif'></td></tr>";
		}
	}
	$mString .= "<tr height=1><td colspan=5 class=dot-x></td></tr>";
	$mString .= "<tr bgcolor=#ffffff height=30><td colspan=5  align=right><input type='image' src='../images/".$admininfo["language"]."/btn_s_ok.gif' style='border:0px;'></td></tr>";
	$mString = $mString."</table>
	
	";
	$mString .= "<table cellpadding=0 cellspacing=0 width=100%>
					<tr height=50 bgcolor=#ffffff><td colspan=8 align=center>".page_bar($total, $page, $max,  "&max=$max&act=innerview&cid=$cid&depth=$depth&ecid=$ecid")."</td></tr>
				 </table>
				
				";
	return $mString;
	
}


?>