<?
$support_file["image"] = array("psd","ai","jpg","gif","bmp","eps","png","tif");
$support_file["doc"] = array("ppt","doc","xls","xlsx","hwp");
$support_file["media"] = array("asf","wmv","mp3","mpa","mpg");

function cms_menu(){
global $mdb, $admininfo, $list_view_type;
$mstring = "
<table cellpadding=0  cellspacing=0 width=156 border=0>
	<tr><td align=center style='padding-bottom:5px;'><img src='../v3/images/".$admininfo[language]."/left_title_cms.gif'></td></tr>
</table>


<table cellpadding='0' width='100%' bgcolor='#c0c0c0' cellspacing=1 style='border-collapse:separate; border-spacing:1px;margin-bottom:5px;' >
	<tr height=24 bgcolor='#efefef'>
		<td align=center class='leftmenu' style='padding:0px;height:25px;'>
		<a href='./' ><b>컨텐츠 목록</b></a>
		</td>
	</tr>
</table>

<table width='100%' border='0' cellpadding='0' cellspacing='1' bgcolor='#c0c0c0'  style='border-collapse:separate; border-spacing:1px;margin-bottom:5px;' class='table_border'>
	<col width=50%>
	<col width=*>
	<tr height=24 bgcolor='#efefef'>
		<td align=center class='leftmenu' style='height:25px;padding:0px;'>
		<a href=\"javascript:LayerShow('cmsreg')\" id='deepzoom_reg'><b>컨텐츠 등록</b></a><!--DeepZoomReg()-->
		<!--td align=center class='leftmenu' style='padding:0px''>
		<a href='gallery.php'><b>갤러리등록</b></a>
		</td-->
	</tr>
</table>";
if(substr_count ($_SERVER["PHP_SELF"],"index.php")){
$mstring .= "
<table cellpadding=0 bgcolor='#c0c0c0' cellspacing=1 width=100% border=0 style='border-collapse:separate; border-spacing:1px;'>

	<tr height=20 bgcolor='#efefef'>
		<td align=left class='leftmenu' style='height:25px;padding:0px 0px 0px 10px;' onclick=\"alert($('#tree_image_group').html());\" style='cursor:hand;' title='클릭하시면 부서/직원 트리메뉴가 노출됩니다.'>
		<IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<b>컨텐츠분류</b>
		</td>
	</tr>
	<tr height=20 bgcolor='#FFFFFF'>
		<td align=left style='height:20px;border-top:0px solid gray;vertical-align:top;".($_COOKIE["tree_image_group_view"] == '0' ? "height:0px;":"height:150px;")."'>
		<div id='tree_image_group' style='".($_COOKIE["tree_image_group_view"] == '0' ? "display:none;":"")."overflow-y:auto;overflow-x:hidden;height:140px;width:157px;border: 0px solid silver;'></div>
		</td>
	</tr>
</table>";
}
$mstring .= "
<table cellpadding=0 width=100% bgcolor='#c0c0c0' cellspacing=1 style='border-collapse:separate; border-spacing:1px;'>";
	//if(substr_count ($admininfo[permit], "15-01")){
		$mstring .= "<tr height=20 bgcolor='#efefef'><td align=left class='leftmenu' style='height:25px;padding:0px 0px 0px 10px;'><IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='../cms/user.php' class='menu_style1_a'>사용자 관리</a></td></tr>";
	//}
	//if(substr_count ($admininfo[permit], "15-01")){
		$mstring .= "<tr height=20 bgcolor='#efefef'><td align=left class='leftmenu' style='height:25px;padding:0px 0px 0px 10px;'><IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='../cms/data_group.php' class='menu_style1_a'>컨텐츠 그룹관리</a></td></tr>";
	//}
	/*
	$mstring .= "<tr height=20 bgcolor='#efefef'><td align=left class='leftmenu' style='height:25px;'><IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='../deepzoom/gallery.list.php' class='menu_style1_a'>갤러리관리</a></td></tr>";
	*/
	$mstring .= "

</table>
	";


return $mstring;

}



function getDataGroupInfoSelect($obj_id, $obj_txt, $parent_group_ix, $selected, $return_type="select", $depth=1, $property=""){

	$mdb = new Database;
	
	if($depth == 1){
		$sql = 	"SELECT dzg.*
				FROM cms_data_group dzg 
				where group_depth = '$depth'
				group by group_ix ";
	}else if($depth == 2){
		$sql = 	"SELECT dzg.*
				FROM cms_data_group dzg 
				where group_depth = '$depth' and parent_group_ix = '$parent_group_ix'
				group by group_ix ";
	}
	//echo $sql;
	$mdb->query($sql);
	
	if($return_type == "select"){
		$mstring = "<select name='$obj_id' id='$obj_id' $property>";
		$mstring .= "<option value=''>$obj_txt</option>";
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
	}else{
		$datas = $mdb->fetchall();
		return $datas;
	}
	
	
}


function workCompanyUserList($company_id, $object_id = "charger_ix", $department="", $charger_ix="",$property=""){
	$mdb = new Database;
	if($department){
		$sql = "SELECT ci.company_id, cmd.code, cmd.name , cmd.mail , cmd.pcs 
				FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_COMPANY_DETAIL." ccd, service_ing si
				WHERE cu.code = cmd.code and cu.company_id = cu.company_id 
				and cu.code = si.mem_ix
				and cmd.department = TRIM('".$department."')  ";

		$mdb->query($sql);

	}else{
		$sql = "SELECT ci.company_id, cmd.code, cmd.name , cmd.mail , cmd.pcs 
				FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_COMPANY_DETAIL." ccd, service_ing si
				WHERE cu.code = cmd.code and cu.company_id = cu.company_id 
				and cu.code = si.mem_ix ";

		$mdb->query($sql);
	}
	if ($mdb->total){
			$SelectString = "<Select name='".$object_id."' id='".str_replace("[]","",$object_id)."' $property>";
			$SelectString = $SelectString."<option value=''>담당자 선택</option>";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			
			if(is_array($charger_ix)){
				if(in_array($mdb->dt[code],$charger_ix)){
					$SelectString = $SelectString."<option value='".$mdb->dt[code]."' selected>".$mdb->dt[name]." </option>";
				}else{
					//$SelectString = $SelectString."<option value='".$mdb->dt[code]."'>".$mdb->dt[name]."</option>";
				}
			}else{
				if($charger_ix == $mdb->dt[code]){
					$SelectString = $SelectString."<option value='".$mdb->dt[code]."' selected>".$mdb->dt[name]." </option>";
				}else{
					$SelectString = $SelectString."<option value='".$mdb->dt[code]."'>".$mdb->dt[name]." </option>";
				}
			}
		}
		$SelectString = $SelectString."</Select>";
	}
	return $SelectString;
}
?>