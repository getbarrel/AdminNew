<?
include("../web.config");
include("../../class/database.class");
include("../lib/imageResize.lib.php");

session_start();

$db = new Database;


if ($act == 'insert')
{
	
	$sql = $sql."INSERT INTO ".TBL_SHOP_ADDIMAGE." (id, pid) ";
	$sql = $sql." values('', '$pid') ";
	
	$db->query($sql);
	$db->query("SELECT id FROM ".TBL_SHOP_ADDIMAGE." WHERE id=LAST_INSERT_ID()");
	$db->fetch();
	
	$image_info = getimagesize ($allimg);	
	$image_type = substr($image_info['mime'],-3);

	if ($addbimg_size > 0)
	{
		if($image_type == "gif"){
			copy($addbimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[0]."_add.gif");
			resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[0]."_add.gif",500,500);
			
			if($add_chk_mimg == 1){
				MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[0].".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$db->dt[0]."_add.gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$db->dt[0]."_add.gif",50,50);
			}
			
			if($add_chk_cimg == 1){
				MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[0].".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[0]."_add.gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[0]."_add.gif",50,50);
			}
			
		}else{
			copy($addbimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[0]."_add.gif");
			
			//Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$id."_add.gif", $_SERVER["DOCUMENT_ROOT"]."/shop/images/addimg/c_".$id."_add.gif", MIRROR_NONE);
			resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[0]."_add.gif",500,500);
			
			if($add_chk_mimg == 1){
				Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[0]."_add.gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$db->dt[0]."_add.gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$db->dt[0]."_add.gif",300,300);
			}
			
			if($add_chk_cimg == 1){
				Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[0]."_add.gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[0]."_add.gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[0]."_add.gif",50,50);
			}
		}
	}
	
	if ($addmimg_size > 0)
	{
		copy($addmimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$db->dt[0]."_add.gif");
	}
	
	if ($addcimg_size > 0)
	{
		copy($addcimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[0]."_add.gif");
	}
	
/*	
	if ($addsimg_size > 0)
	{
		copy($addsimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[0]."_add.gif");
	}else{
		if ($addbimg_size > 0){
			Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[0]."_add.gif", $_SERVER["DOCUMENT_ROOT"]."/shop/images/addimg/c_".$db->dt[0]."_add.gif", MIRROR_NONE);
			resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[0]."_add.gif",50,50);
		}
	}
*/	
	echo "<html>
		  <meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
			<body>
			<div id='option_view'>
			".PrintAddImage($pid)."
			</div>
			</body>
		</html>\n	";
		
	echo "<Script Language='JavaScript'>parent.document.getElementById('addimgarea').innerHTML=document.getElementById('option_view').innerHTML;parent.document.forms['addimageform'].reset();alert(language_data['img.add.php']['A'][language]);</Script>";	//'정상적으로 입력되었습니다'
	//header("Location:../product_input.php");
}

if ($act == "delete")
{
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$id."_add.gif"))
	{
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$id."_add.gif");
	}
	
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$id."_add.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$id."_add.gif");	
	}
	
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$id."_add.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$id."_add.gif");	
	}

	$db->query("DELETE FROM ".TBL_SHOP_ADDIMAGE." WHERE id='$id'");
	
	echo "<html>
		  <meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
			<body>
			<div id='option_view'>
			".PrintAddImage($pid)."
			</div>
			</body>
		</html>\n	";

	echo "<Script Language='JavaScript'>parent.document.getElementById('addimgarea').innerHTML=document.getElementById('option_view').innerHTML;alert(language_data['img.add.php']['B'][language]);</Script>";	//'정상적으로 삭제되었습니다'
	//header("Location:../product_input.php");
}


function PrintAddImage($pid){
	global $db, $admin_config;
	
	$sql = "select id from ".TBL_SHOP_ADDIMAGE." a where pid = '$pid' ";
	$db->query($sql);
	
	$mString = "<table cellpadding=5 cellspacing=0 width='100%' bgcolor=silver>";		
	$mString = $mString."<tr height=1><td colspan=4 bgcolor=silver></td></tr>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class=small>번호</td><td  class=small colspan=2>클립아트 ID</td><td  class=small>중간이미지</td><td  class=small>큰이미지</td><td  class=small>삭제</td></tr>";
	$mString = $mString."<tr height=1><td colspan=4 bgcolor=silver></td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=80><td colspan=6 align=center>입력된 추가이미지가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff><td  align=center  class=small>".($i+1)."</td><td  ><img src='".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[id]."_add.gif' align=absmiddle style='border:1px solid gray'></td><td  class=small><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[id]."_add.gif')\">c_".$db->dt[id]."_add.gif</a></td><td  class=small><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/m_".$db->dt[id]."_add.gif')\">m_".$db->dt[id]."_add.gif</a></td><td><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[id]."_add.gif')\">b_".$db->dt[id]."_add.gif</a></td><td align=center  class=small><a href=\"JavaScript:deleteAddimage('delete','".$db->dt[id]."','$pid')\"><img src='../image/btc_del.gif'></a></td></tr>";
			$mString = $mString."<tr height=1><td colspan=6 background='../image/dot.gif'></td></tr>";
		}
	}
	$mString = $mString."</table>";
	
	return $mString;
}

function PrintAddImageX($pid){
	global $db, $admin_config;
	
	$sql = "select id from ".TBL_SHOP_ADDIMAGE." a where pid = '$pid' ";
	$db->query($sql);
	
	$mString = "<table cellpadding=5 cellspacing=0 width=100% bgcolor=silver>";
	$mString = $mString."<tr height=1><td colspan=4 bgcolor=silver></td></tr>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td>번호</td><td>클립아트 ID</td><td>중간 이미지</td><td>큰이미지</td><td>삭제</td></tr>";
	$mString = $mString."<tr height=1><td colspan=4 bgcolor=silver></td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=80><td colspan=5 align=center>입력된 추가이미지가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff><td  align=center>".($i+1)."</td><td><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[id]."_add.gif')\">c_".$db->dt[id]."_add.gif</a></td><td><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/m_".$db->dt[id]."_add.gif')\">m_".$db->dt[id]."_add.gif</a></td><td><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[id]."_add.gif')\">b_".$db->dt[id]."_add.gif</a></td><td align=center><a href=\"JavaScript:deleteAddimage('delete','".$db->dt[id]."','$pid')\"><img src='../image/btc_del.gif'></a></td></tr>";
			$mString = $mString."<tr height=1><td colspan=5 background='../image/dot.gif'></td></tr>";
		}
	}
	$mString = $mString."</table>";
	
	return $mString;
}
?>