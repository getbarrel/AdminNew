<?

function topmenu($default_path='/admin/basic'){
global $admininfo, $admin_config, $PHP_SELF;
//print_r($admininfo);
//print_r($admin_config);
//print_r(strpos($admininfo[permit], "01-01"));
		$mstring = "
		<table cellpadding='3' cellspacing=0 border=0 align=left>
			<tr>
				<td class='top_shadow'></td>";
	$mdb = new Database;
	//print_r($admininfo);

if($admininfo[use_work] != "1"){
	if($admin_config[mall_use_inventory] == "N"){
		$inventory_str = " and menu_div != 'inventory' ";
	}

	if($admininfo[mall_type] == "O"){// 입점형
		//$sql = 	"SELECT * FROM admin_menu_div where disp = 1 and gnb_use_biz = 'Y' ".$inventory_str." order by vieworder asc ";
		$sql = "Select distinct am.menu_div, amd.div_name, amd.gnb_name, amd.basic_link
				from admin_menus am left join admin_auth_templet_detail aatd on am.menu_code = aatd.menu_code
				and auth_templet_ix = '".$admininfo[charger_roll]."'
				and am.disp_auth = 'Y' , admin_menu_div amd
				where amd.div_name = am.menu_div and amd.gnb_use_openmarket = 'Y' and disp_auth = 'Y' and auth_read = 'Y'
				and am.use_openmarket = 'Y' $inventory_str
				order by amd.vieworder asc ";
	}else if($admininfo[mall_type] == "B"){// 입점형
		//$sql = 	"SELECT * FROM admin_menu_div where disp = 1 and gnb_use_biz = 'Y' ".$inventory_str." order by vieworder asc ";
		$sql = "Select distinct am.menu_div, amd.div_name, amd.gnb_name, amd.basic_link
				from admin_menus am left join admin_auth_templet_detail aatd on am.menu_code = aatd.menu_code
				and auth_templet_ix = '".$admininfo[charger_roll]."'
				and am.disp_auth = 'Y' , admin_menu_div amd
				where amd.div_name = am.menu_div and amd.gnb_use_biz = 'Y' and disp_auth = 'Y' and auth_read = 'Y'
				and am.use_business = 'Y' $inventory_str
				order by amd.vieworder asc ";

	}else if($admininfo[mall_type] == "F" || $admininfo[mall_type] == "R"){ // 무료형 , 임대형
		//$sql = 	"SELECT * FROM admin_menu_div where disp = 1 and gnb_use_soho = 'Y' ".$inventory_str." order by vieworder asc ";
		$sql = "Select distinct am.menu_div, amd.div_name, amd.gnb_name, amd.basic_link
				from admin_menus am left join admin_auth_templet_detail aatd on am.menu_code = aatd.menu_code
				and auth_templet_ix = '".$admininfo[charger_roll]."'
				and am.disp_auth = 'Y' , admin_menu_div amd
				where amd.div_name = am.menu_div and amd.gnb_use_soho = 'Y' and disp_auth = 'Y' and auth_read = 'Y'
				and am.use_soho = 'Y' $inventory_str
				order by amd.vieworder asc ";
	}else if($admininfo[mall_type] == "H"){ // 무료형 , 임대형
		//$sql = 	"SELECT * FROM admin_menu_div where disp = 1 and gnb_use_soho = 'Y' ".$inventory_str." order by vieworder asc ";
		$sql = "Select distinct am.menu_div, amd.div_name, amd.gnb_name, amd.basic_link
				from admin_menus am left join admin_auth_templet_detail aatd on am.menu_code = aatd.menu_code
				and auth_templet_ix = '".$admininfo[charger_roll]."'
				and am.disp_auth = 'Y' , admin_menu_div amd
				where amd.div_name = am.menu_div and amd.gnb_use_home = 'Y' and disp_auth = 'Y' and auth_read = 'Y'
				and am.use_home = 'Y' $inventory_str
				order by amd.vieworder asc ";
	}else{
		if($admin_config[mall_use_inventory] == "N"){
			$inventory_str = " and div_name != 'inventory' ";
		}

		$sql = 	"SELECT * FROM admin_menu_div where disp = 1 ".$inventory_str." order by vieworder asc ";
	}


	//echo nl2br($sql);
	//exit;
	$mdb->query($sql);
	$mdb->fetch();
	//echo "auth_read : ".$mdb->dt[auth_read] .":::".$mdb->total;
	if(!$mdb->total){
		echo "<script language='javascript'>alert('해당메뉴에 대한 접근권한이 없습니다.');history.back();</script>";
		exit;
	}else{
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$menu_on_src = str_replace("/","_",$mdb->dt[div_name])."_on.gif";
			$menu_off_src = str_replace("/","_",$mdb->dt[div_name]).".gif";
			if(substr_count ($PHP_SELF, "/".str_replace("/","_",$mdb->dt[div_name])."/")){
				$menu_src = str_replace("/","_",$mdb->dt[div_name])."_on.gif";
			}else{
				$menu_src = str_replace("/","_",$mdb->dt[div_name]).".gif";
			}
			//echo $admininfo[admin_level];
			if($admininfo[admin_level] == 9){
				$gnb_name = $mdb->dt[gnb_name];
			}else{
				$gnb_name = str_replace("셀러관리","상점관리",$mdb->dt[gnb_name]);
			}

			if($admininfo[mall_type] == "H"){
				$gnb_name = str_replace("상점관리","사이트관리",$mdb->dt[gnb_name]);
				$gnb_name = str_replace("프로모션/전시","운영관리",$gnb_name);
			}
			//ONMOUSEOVER=\"javascript:showSubMenuLayer('".$mdb->dt[div_name]."');/*relationOnMouseOut();*/\" ONMOUSEOUT=\"javascript:hideSubMenuLayer('".$mdb->dt[div_name]."')\"
			$mstring .= "
					<td  ID=gnb_link_".str_replace("/","_",$mdb->dt[div_name])."  onmouseover=\"showSubMenuLayer('".str_replace("/","_",$mdb->dt[div_name])."');\"  onmouseout=\"hideSubMenuLayer('".str_replace("/","_",$mdb->dt[div_name])."');\"   ".(strlen($mdb->dt[gnb_name]) > 18 ? "width=75":"width=60")." align=center style='padding:5px 3px;vertical-align:top;' nowrap>
						<div align=center  STYLE='text-align:center;position:relative;' onmouseover=\"MM_swapImage('Image".($i+1)."','','$default_path/".$menu_on_src."',1)\" onmouseout='MM_swapImgRestore()'  nowrap>
						<A HREF='".$mdb->dt[basic_link]."' STYLE='position:relative;' nowrap>
						<img  src='$default_path/".$menu_src."' vspace=2  ID='gnb_link_text_".str_replace("/","_",$mdb->dt[div_name])."' border=0 name=Image".($i+1)." onmouseover=\"this.src='$default_path/".$menu_on_src."'\"  onmouseout=\"this.src='$default_path/".$menu_src."'\"><br>".$gnb_name."
						</A>
						</div>
					</td>";
		}
	}

if($admininfo[admin_level] ==9){
/*

		if(substr_count ($PHP_SELF, "/logstory/commerce/")){
			$menu_src = "static_on.gif";
		}else{
			$menu_src = "static.gif";
		}

		$mstring .= "
				<td ALIGN=center WIDTH=75 >
					<div ID=gnb_link_static STYLE='position:relative' ONMOUSEOVER=\"javascript:showSubMenuLayer('static');\" ONMOUSEOUT=\"javascript:hideSubMenuLayer('static')\" nowrap>
					<A HREF='/admin/logstory/commerce/productviewbyreferer.php?SubID=SM114641Sub' onmouseover=\"MM_swapImage('Image9','','$default_path/static_on.gif',1)\" onmouseout='MM_swapImgRestore()'><img src='$default_path/$menu_src' border=0 ID='gnb_link_text_static' name=Image9><br>이커머스분석</A>
					</div>
				</td>";
		if(substr_count ($PHP_SELF, "/logstory/report/")){
			$menu_src = "log_on.gif";
		}else{
			$menu_src = "log.gif";
		}
		$mstring .= "
				<td ALIGN=center WIDTH=60  nowrap>
					<div ID=gnb_link_log STYLE='position:relative' ONMOUSEOVER=\"javascript:showSubMenuLayer('log');\" ONMOUSEOUT=\"javascript:hideSubMenuLayer('log')\">
					<A HREF='/admin/logstory/report/pageview1.php?SubID=SM114641Sub' onmouseover=\"MM_swapImage('Image10','','$default_path/log_on.gif',1)\" onmouseout='MM_swapImgRestore()'><img src='$default_path/$menu_src' border=0 ID='gnb_link_text_log' name=Image10><br>로그분석</A>
					</div>
				</td>";

		if($admininfo[charger_id] == "forbiz"){
			if(substr_count ($PHP_SELF, "/campaign/")){
				$menu_src = "mailing_on.gif";
			}else{
				$menu_src = "mailing.gif";
			}
			$mstring .= "
					<td ALIGN=center WIDTH=80 nowrap>
						<div ID=gnb_link_campaign STYLE='position:relative' ONMOUSEOVER=\"javascript:showSubMenuLayer('campaign');\" ONMOUSEOUT=\"javascript:hideSubMenuLayer('campaign')\">
						<A HREF='/admin/campaign/addressbook_list.php' onmouseover=\"MM_swapImage('Image13','','$default_path/mailing_on.gif',1)\" onmouseout='MM_swapImgRestore()'><img src='$default_path/$menu_src' border=0 ID='gnb_link_text_campaign' name=Image13><br>메일링/SMS</A>
						</div>
					</td>";
		}
		*/
}
}else{
	//if(($admininfo[charger_id] == "sigi1074" || $admininfo[charger_id] == "tech9" )){

	//}
		if(substr_count ($PHP_SELF, "/campaign/")){
			$menu_src = "mailing_on.gif";
		}else{
			$menu_src = "mailing.gif";
		}
		$mstring .= "
				<td ALIGN=center WIDTH=80 nowrap>
					<div ID=gnb_link_campaign STYLE='position:relative' ONMOUSEOVER=\"javascript:showSubMenuLayer('campaign');/*relationOnMouseOut();*/\" ONMOUSEOUT=\"javascript:hideSubMenuLayer('campaign')\">
					<A HREF='/admin/campaign/addressbook_list.php' onmouseover=\"MM_swapImage('Image13','','$default_path/mailing_on.gif',1)\" onmouseout='MM_swapImgRestore()'><img src='$default_path/$menu_src' border=0 ID='gnb_link_text_campaign' name=Image13><br>메일링/SMS</A>
					</div>
				</td>";

		if(substr_count ($PHP_SELF, "/work/")){
			$menu_src = "estimate_on.gif";
		}else{
			$menu_src = "estimate.gif";
		}
		$mstring .= "
				<td ALIGN=center WIDTH=60 nowrap>
					<div ID=gnb_link_work STYLE='position:relative' ONMOUSEOVER=\"javascript:showSubMenuLayer('work');/*relationOnMouseOut();*/\" ONMOUSEOUT=\"javascript:hideSubMenuLayer('work')\">
					<A HREF='/admin/work/work_list.php?list_type=myjob' onmouseover=\"MM_swapImage('Image11','','$default_path/estimate_on.gif',1)\" onmouseout='MM_swapImgRestore()'><img src='$default_path/$menu_src' border=0 ID='gnb_link_text_work' name=Image11><br>업무관리</A>
					</div>
				</td>";


	if(($admininfo[charger_id] == "sigi1074" || $admininfo[charger_id] == "tech9" )){
		if(substr_count ($PHP_SELF, "/kms/")){
			$menu_src = "estimate_on.gif";
		}else{
			$menu_src = "estimate.gif";
		}
		$mstring .= "
				<td ALIGN=center WIDTH=60 nowrap>
					<div ID=gnb_link_kms STYLE='position:relative' ONMOUSEOVER=\"javascript:showSubMenuLayer('kms');/*relationOnMouseOut();*/\" ONMOUSEOUT=\"javascript:hideSubMenuLayer('kms')\">
					<A HREF='/admin/kms/' onmouseover=\"MM_swapImage('Image11','','$default_path/estimate_on.gif',1)\" onmouseout='MM_swapImgRestore()'><img src='$default_path/$menu_src' border=0 ID='gnb_link_text_kms' name=Image11><br>KMS 관리</A>
					</div>
				</td>";

		if(substr_count ($PHP_SELF, "/deepzoom/")){
			$menu_src = "estimate_on.gif";
		}else{
			$menu_src = "estimate.gif";
		}
		$mstring .= "
				<td ALIGN=center WIDTH=70 nowrap>
					<div ID=gnb_link_deepzoom STYLE='position:relative' ONMOUSEOVER=\"javascript:showSubMenuLayer('deepzoom');/*relationOnMouseOut();*/\" ONMOUSEOUT=\"javascript:hideSubMenuLayer('deepzoom')\">
					<A HREF='/admin/cms/' onmouseover=\"MM_swapImage('Image11','','$default_path/estimate_on.gif',1)\" onmouseout='MM_swapImgRestore()'><img src='$default_path/$menu_src' border=0 ID='gnb_link_text_deepzoom' name=Image11><br>컨텐츠 관리</A>
					</div>
				</td>";

		if(substr_count ($PHP_SELF, "/tax/")){
			$menu_src = "estimate_on.gif";
		}else{
			$menu_src = "estimate.gif";
		}
		$mstring .= "
				<td ALIGN=center WIDTH=80 nowrap>
					<div ID=gnb_link_tax STYLE='position:relative' ONMOUSEOVER=\"javascript:showSubMenuLayer('tax');/*relationOnMouseOut();*/\" ONMOUSEOUT=\"javascript:hideSubMenuLayer('tax')\" class=small>
					<A HREF='/admin/tax/' onmouseover=\"MM_swapImage('Image11','','$default_path/estimate_on.gif',1)\" onmouseout='MM_swapImgRestore()'><img src='$default_path/$menu_src' border=0 ID='gnb_link_text_tax' name=Image11 ><br>전자세금계산서</A>
					</div>
				</td>";
	}
}
	$mstring .= "
			</tr>
		</table>";

	return $mstring;
	exit;

}


function store_menu($default_path='/admin'){
global $admin_config, $admininfo;

	if($admininfo[mall_type] == "H"){
	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_site.gif'></td></tr>
	</table>";
	}else{
	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_store.gif'></td></tr>
	</table>";
	}

	$menus = getMenuData("store");

	$mstring .= getnerateLeftMenu($default_path, $menus);


	return $mstring;
}



function seller_menu($default_path='/admin'){
global $admin_config, $admininfo;

	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_".($admininfo[admin_level] == 9 ? "seller":"store").".gif'></td></tr>
	</table>";

	$menus = getMenuData("seller");

	$mstring .= getnerateLeftMenu($default_path, $menus);

	return $mstring;
}

function basic_menu($default_path='/admin'){
global $admin_config, $admininfo;

	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_".($admininfo[admin_level] == 9 ? "seller":"store").".gif'></td></tr>
	</table>";

	$menus = getMenuData("basic");

	$mstring .= getnerateLeftMenu($default_path, $menus);

	return $mstring;
}



function product_menu($default_path='/admin', $category_tree=""){
	global $admininfo;
	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_product.gif'></td></tr>
	</table>";

	$menus = getMenuData("product");
	$mstring .= getnerateLeftMenu($default_path, $menus, $category_tree);

	return $mstring;

}




function display_menu($default_path='/admin', $category_tree=""){
	global $admininfo;
	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_display.gif'></td></tr>
	</table>";


	$menus = getMenuData("display");
	$mstring .= getnerateLeftMenu($default_path, $menus, $category_tree);

	return $mstring;

}

//SNS관련 메뉴 시작
function sns_menu($default_path='/admin', $category_tree=""){
	global $admininfo;
	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_social.gif'></td></tr>
	</table>";
	$menus = getMenuData("sns");
	$mstring .= getnerateLeftMenu($default_path, $menus, $category_tree);

	return $mstring;

}
//SNS관련메뉴 종료

function estimate_menu($default_path='/admin', $category_tree=""){
	global $admininfo;
	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_estimation.gif'></td></tr>
	</table>";
	$menus = getMenuData("estimate");
	$mstring .= getnerateLeftMenu($default_path, $menus, $category_tree);
	/*
	<table cellpadding=0 bgcolor='#c0c0c0' cellspacing=1 width=156 border=0 style='border-collapse:separate; border-spacing:1px;'>";
	if(substr_count ($admininfo[permit], "09-01")){
		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/estimate/estimate.list.php' class='menu_style1_a'>견적현황</a></td></tr>";
	}
	if(substr_count ($admininfo[permit], "09-02")){
		$mstring .= "<tr height=20 bgcolor='#FCE7C8'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/estimate/category.php' class='menu_style1_a'>견적카테고리</a></td></tr>";
	}
	if(substr_count ($admininfo[permit], "09-03")){
		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/estimate/estimate.product.php' class='menu_style1_a'>견적상품등록</a></td></tr>";
	}
	if(substr_count ($admininfo[permit], "09-04")){
		$mstring .= "<tr height=20 bgcolor='#FCE7C8'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/estimate/estimate.intra.php' class='menu_style1_a'>내부견적서</a></td></tr>";
	}
	$mstring .= "</table>";
	*/
	return $mstring;

}

function design_menu22($default_path='/admin', $category_tree=""){
	global $admininfo, $admin_config;


	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_order.gif'></td></tr>
	</table>";
	$menus = getMenuData("design");
	$mstring .= getnerateLeftMenu($default_path, $menus);

	return $mstring;
}

function design_menu($default_path='/admin', $category_tree=""){
global $SubID, $admininfo;

$mstring = "
<SCRIPT language=javascript id=clientEventHandlersJS>
<!--
var SMinitiallyOpenSub11464 = '".$SubID."';
//-->
</SCRIPT>
<SCRIPT language=javascript  src='../include/design_menu.js'></SCRIPT>
";
$mstring .= 	"<table cellpadding=0  cellspacing=0 width=196 border=0>
					<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_design.gif'></td></tr>
			</table>
			<table cellpadding=0 cellspacing=1 bgcolor='#c0c0c0'  width=196 border=0 style='border-collapse:separate; border-spacing:1px;'>";
		if ($treeview != ""){
$mstring .= "<TR><TD width=100% bgcolor=#ffffff style='overflow:auto;width:290;'>".$treeview."</TD></TR>";
		}

		  if ($SubID == "SM1146411Sub") $dispstring = "block"; else $dispstring = "none";

$mstring = $mstring."
		  <TR>
		    <TD >
		      <DIV class=SM_p11464 id=SM1146411 onmouseover=\"SMcs11464(this, 'SM_po11464', '')\"  onmouseout=\"SMcs11464(this, 'SM_p11464', '')\" style='padding-top:0px;vertical-align:top'>
		      <table cellpadding=0 cellspacing=0 border=0><tr><td onclick=\"SMpoc11464('SM1146411Sub','SM1146411')\" height='22' valign='middle'><IMG id=SM1146411I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;레이아웃 디자인</td><td style='padding-left:5px;' style='vertical-align:top'><!--img src='/admin/image/btn_spop_manage.gif'--></td></tr></table></DIV>
		      <DIV class=SM_cb11464 id=SM1146411Sub style='DISPLAY: ".$dispstring."; OVERFLOW: hidden; POSITION: relative;height:220px;background-color: #edeeed;'>
		      <DIV class=SM_c11464 id=SM1146430Sub30 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><A href='$default_path/design/design.mod.php?mod=layout&page_name=layout.htm&SubID=SM1146411Sub' ><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 쇼핑몰 레이아웃</a> <!--a href=\"javascript:PoPWindow('design.mod.php?mod=layout&page_name=layout.htm&mmode=pop',960,600,'design')\"'><img src='/admin/image/btn_spop_manage.gif'  valign=bottom'></a--> </DIV>
		      <DIV class=SM_c11464 id=SM1146419Sub19 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><a href='$default_path/design/design.mod.php?mod=layout/header&page_name=header_top.htm&SubID=SM1146411Sub' ><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 상단(header)</A></DIV>
		      <DIV class=SM_c11464 id=SM1146419Sub19 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><A href='$default_path/design/design.mod.php?mod=layout/leftmenu&page_name=ms_community_leftmenu.htm&SubID=SM1146411Sub' ><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 좌측메뉴(leftmenu)</A></DIV>
		      <DIV class=SM_c11464 id=SM1146419Sub19 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><A href='$default_path/design/design.mod.php?mod=layout/contents_add&page_name=contents_add.htm&SubID=SM1146411Sub' ><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 추가컨텐츠</A></DIV>
		      <DIV class=SM_c11464 id=SM1146430Sub31 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><A href='$default_path/design/design.mod.php?mod=layout/rightmenu&page_name=today_history2.htm&SubID=SM1146411Sub' ><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 오른쪽메뉴(rightmenu)</A></DIV>
		      <DIV class=SM_c11464 id=SM1146430Sub32 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><A href='$default_path/design/design.mod.php?mod=layout/footer&page_name=footer_menu.htm&SubID=SM1146411Sub' ><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 하단설명(footer)</A></DIV>
		      <DIV class=SM_c11464 id=SM1146430Sub32 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><A href='$default_path/design/design.mod.php?mod=css&page_name=mallstory.css&SubID=SM1146411Sub' ><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 스타일시트(css)</A></DIV>
		      <DIV class=SM_c11464 id=SM1146430Sub32 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><A href='$default_path/design/design.mod.php?mod=js&page_name=mallstory.js&SubID=SM1146411Sub' ><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 자바스크립트(js)</A></DIV>
		      <DIV class=SM_c11464 id=SM1146430Sub32 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><A href='$default_path/design/design.mod.php?mod=etc&page_name=category_sbulayer.htm&SubID=SM1146411Sub' ><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 레이아웃기타</A></DIV>
		      </DIV>
		      </TD>
		  </TR>";


if ($SubID == "SM114641Sub") $dispstring = "block"; else $dispstring = "none";

$mstring = $mstring."
		   <TR>
		    <TD >
		      <DIV class=SM_p11464 id=SM114641 onmouseover=\"SMcs11464(this, 'SM_po11464', '')\" onmouseout=\"SMcs11464(this, 'SM_p11464', '')\"  style='padding-top:0px;vertical-align:top'><table cellpadding=0 cellspacing=0 border=0><tr><td onclick=\"SMpoc11464('SM114641Sub','SM114641')\" height='22' valign='middle'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;페이지 상세디자인</td><td style='padding-left:5px;' style='vertical-align:top'><!--img src='/admin/image/btn_spop_manage.gif'--></td></tr></table></DIV>
		      <DIV class=SM_cb11464 id=SM114641Sub style='DISPLAY: ".$dispstring."; OVERFLOW: hidden; POSITION: relative;HEIGHT: 375px'>
		     $category_tree
		      </DIV>
		      </TD>
		  </TR>";

  			if ($SubID == "SM22464243Sub") $dispstring = "block"; else $dispstring = "none";


$mstring = $mstring."
		  <TR>
		    <TD >
		      <DIV class=SM_p11464 id=SM22464243 onmouseover=\"SMcs11464(this, 'SM_po11464', '')\" onmouseout=\"SMcs11464(this, 'SM_p11464', '')\" style='padding-top:0px;vertical-align:top'>

			  <table cellpadding=0 cellspacing=0 border=0>
			  <tr><td onclick=\"SMpoc11464('SM22464243Sub','SM22464243')\" height='22' valign='middle'>
		      <IMG id=SM22464243I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;기타 디자인관리</td><td style='padding-left:5px;' style='vertical-align:top'>
			  </td></tr></table>
			  </DIV>
		      <DIV class=SM_cb11464 id=SM22464243Sub
		      style='DISPLAY: ".$dispstring."; OVERFLOW: hidden;POSITION: relative;HEIGHT: 175px;background-color: #edeeed;'>
		      <A href='$default_path/design/design_layout.php?SubID=SM22464243Sub' >
		      <DIV class=SM_c11464 id=SM11464186Sub186 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 레이아웃 일괄관리</DIV></A>
		      <A href='$default_path/design/design.html.php?SubID=SM22464243Sub' >
		      <DIV class=SM_c11464 id=SM11464186Sub186 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> HTML 라이브러리</DIV></A>
		      <A href='$default_path/design/design_title.php?SubID=SM22464243Sub' >
		      <DIV class=SM_c11464 id=SM11464186Sub186 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 타이틀 디자인</DIV></A>
		      <A href='$default_path/design/design_bbs_templet.php?SubID=SM22464243Sub' >
		      <DIV class=SM_c11464 id=SM11464186Sub186 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 게시판디자인</DIV></A>
		      <!--A href='$default_path/design/main_flash.php?SubID=SM22464243Sub' >
		      <DIV class=SM_c11464 id=SM11464186Sub186 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 메인 플래쉬관리</DIV></A>
			  <A href='$default_path/design/banner.php?SubID=SM22464243Sub' >
		      <DIV class=SM_c11464 id=SM11464186Sub186 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 배너관리</DIV></A-->
			  <A href='$default_path/design/product_icon.php?SubID=SM22464243Sub' >
		      <DIV class=SM_c11464 id=SM11464186Sub186 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 아이콘관리</DIV></A>
		      <!--A href='$default_path/design/design.php?pcode=spc_0019&page_name=point_shop.htm&SubID=SM22464243Sub' >
		      <DIV class=SM_c11464 id=SM11464286Sub286 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 포인트/DIV></a>
		      <A href='$default_path/design/design.php?pcode=spc_0022&page_name=cobuy.htm&SubID=SM22464243Sub' >
		      <DIV class=SM_c11464 id=SM11464264Sub264 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 공동구매</DIV></A>
		      <A href='$default_path/design/design.php?pcode=spc_0020&page_name=cupon.htm&SubID=SM22464243Sub' >
		      <DIV class=SM_c11464 id=SM11464264Sub264 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='".$default_path."/images/icon/left_dot.gif' border=0 align=absmiddle> 쿠폰목록</DIV></A-->

		      <DIV class=SMEmptyDiv11464></DIV></DIV></TD></TR>
		   ";
if($admininfo[mall_type] != "H"){
$mstring = $mstring."
		   <TR>
		    <TD >
		      <DIV class=SM_p11464 id=SM114641 onmouseover=\"SMcs11464(this, 'SM_po11464', '')\" onmouseout=\"SMcs11464(this, 'SM_p11464', '')\"  style='padding-top:0px;vertical-align:top'><table cellpadding=0 cellspacing=0 border=0><tr><td  height='22' valign='middle'> <A href='$default_path/design/design_photoskin.php' ><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;매직스킨관리</a></td><td style='padding-left:5px;' style='vertical-align:top'></td></tr></table></DIV>

		      </TD>
		  </TR>";
}
$mstring .= "</TBODY></TABLE>";

   return $mstring;
}


function order_menu($default_path='/admin'){
	global $admininfo, $admin_config;


	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_order.gif'></td></tr>
	</table>";
	$menus = getMenuData("order");
	$mstring .= getnerateLeftMenu($default_path, $menus);

	return $mstring;

}


function member_menu($default_path='/admin'){
	global $admininfo;
	//echo $admininfo[permit];

	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_member.gif'></td></tr>

	</table>";

	$menus = getMenuData("member");
	$mstring .= getnerateLeftMenu($default_path, $menus, $category_tree);

	return $mstring;

}

function marketting_menu($default_path='/admin'){
global $admin_config, $admininfo;

	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_marketting.gif'></td></tr>
	</table>";

	$menus = getMenuData("marketting");
	$mstring .= getnerateLeftMenu($default_path, $menus);


	return $mstring;

}

function database_menu($default_path='/admin'){
	global $admininfo;

	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_data.gif'></td></tr>
	</table>";

	$menus = getMenuData("database");
	$mstring .= getnerateLeftMenu($default_path, $menus);

	/*
	<table cellpadding=0 bgcolor='#c0c0c0' cellspacing=1 width=156 border=0 style='border-collapse:separate; border-spacing:1px;'>";
	if(substr_count ($admininfo[permit], "10-01")){
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/database/tableinfo.php' class='menu_style1_a'>테이블 정보</a></td></tr>";
	}
	if(substr_count ($admininfo[permit], "10-02")){
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/database/backup.list.php' class='menu_style1_a'>DataBase 백업및 복구</a></td></tr>";
	}
	if(substr_count ($admininfo[permit], "10-03")){
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/database/check_db.php' class='menu_style1_a'>DataBase 테이블체크</a></td></tr>";
	}
	$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/database/explain_query.php' class='menu_style1_a'>쿼리 실행계획 </a></td></tr>";
	$mstring .= "<!--tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/member/member_personal.php' class='menu_style1_a'>상품 DB 백업</a></td></tr>";
	$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/member/mail.write.php' class='menu_style1_a'>회원 DB 백업</td></tr-->";
	$mstring .= "</table>";
*/
	return $mstring;

}


function cscenter_menu($default_path='/admin'){
global $admin_config, $admininfo;

	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_cscenter.gif'></td></tr>
	</table>";

	$menus = getMenuData("cscenter");
	$mstring .= getnerateLeftMenu($default_path, $menus);
/*
	$mstring = "
	<table cellpadding=0 bgcolor='#c0c0c0' cellspacing=1 width=156 border=0 style='border-collapse:separate; border-spacing:1px;'>";

	if(substr_count ($admininfo[permit], "06-02")){
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/cscenter/counsel_info.php' class='menu_style1_a'>주문상담내역</td></tr>";
	}

	if(substr_count ($admininfo[permit], "06-05")){
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/cscenter/useafter.list.php' class='menu_style1_a'>사용후기 관리</td></tr>";
	}
	if(substr_count ($admininfo[permit], "06-09")){
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/cscenter/product_qna.php' class='menu_style1_a'>상품 Q&A 관리</td></tr>";
	}

	if(substr_count ($admininfo[permit], "06-10")){
		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/cscenter/contactus_info.php' class='menu_style1_a'>제휴문의</a></td></tr>";
	}

	$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/cscenter/bug_info.php' class='menu_style1_a'>버그신고</a></td></tr>";

	if(substr_count ($admininfo[permit], "06-11")){
		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/cscenter/poll_list.php' class='menu_style1_a'>설문관리</a></td></tr>";
	}

	$mstring .= "
	</table>";
*/
	return $mstring;

}

function bbsmanage_menu($default_path='/admin'){
global $admin_config, $admininfo;
	$mdb = new Database;
	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_bbsmanage.gif'></td></tr>
	</table>";
	$mstring .= "
	<table cellpadding=0 class='line_color' cellspacing=0 width=156 border=0 style='border-collapse:collapse; border-spacing:1px;'>";//class='table_border'

	$menus = getMenuData("bbsmanage");
	$mdb->query("select bmc.* , bg.div_ix, bg.div_name as group_name from bbs_manage_config bmc , shop_bbs_group bg where bmc.board_group = bg.div_ix and disp = 1 and bmc.board_style = 'bbs' and bmc.recent_list_display = 'Y' ");
	$bbs_menes = $mdb->fetchall();

	for($i=0;$i < count($menus);$i++){

		$navis = explode(">",str_replace("HOME > ","",$menus[$i][menu_name]));

		if(md5($_SERVER["PHP_SELF"]) == $menus[$i][menu_code]){
			if($menus[$i][menu_param] && substr_count ($menus[$i][menu_link], $menus[$i][menu_param]."=".$_GET[$menus[$i][menu_param]])){
				$selectedClass = "leftmenu leftmenu_seleted";
			}else{
				$selectedClass = "leftmenu";
			}
		}else{
			$selectedClass = "leftmenu";
		}
		if(count($navis[1]) >= 1){


			if($depth1_name != $navis[0]){
				$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='".(substr_count($menus[$i][menu_name],$navis[0]) ? "leftmenu leftmenu_seleted":"")."'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;".$navis[0]."</td></tr>";

			}
			$depth1_name = $navis[0];
			$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='".$selectedClass."' style='padding-left:20px;'> <img src='/admin/images/icon/dot_orange.gif' align=absmiddle> <a href='".$menus[$i][menu_link]."' class='menu_style1_a'>".(count($navis[0]) >= 1 ? $navis[1]:$menus[$i][menu_name])."</a></td></tr>";


		}else{
			if("b3844808846874c7072c75b170558590" == $menus[$i][menu_code]){
				$mstring.= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='".$selectedClass."'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='".$menus[$i][menu_link]."' class='menu_style1_a'>".$menus[$i][menu_name]."</a></td></tr>";

				for($j=0;$j<count($bbs_menes);$j++){
					$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='".($_GET["board"] == $bbs_menes[$j][board_ename] ? "leftmenu leftmenu_seleted":"leftmenu")."' style='padding-left:15px;'> <img src='/admin/images/icon/dot_orange.gif' align=absmiddle> <a href='./bbs.php?mode=list&board=".$bbs_menes[$j][board_ename]."' >".$bbs_menes[$j][board_name]."</a></td></tr>";
				}
			}else{
			$mstring.= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='".$selectedClass."'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='".$menus[$i][menu_link]."' class='menu_style1_a'>".$menus[$i][menu_name]."</a></td></tr>";
			}

		}
	}
	$mstring.= "</table>";

	return $mstring;

}



function cogoods_menu($default_path='/admin'){
global $admin_config, $admininfo;

	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_share.gif'></td></tr>
	</table>";
	$mstring .= "
	<table cellpadding=0 class='line_color' cellspacing=0 width=156 border=0 style='border-collapse:collapse; border-spacing:1px;'>";//class='table_border'

	$menus = getMenuData("cogoods");

	for($i=0;$i < count($menus);$i++){
		$mstring.= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='".$menus[$i][menu_link]."' class='menu_style1_a'>".$menus[$i][menu_name]."</a></td></tr>";
	}
	$mstring.= "</table><br>";
	$mstring.= "
	<table cellpadding=0 class='line_color'  cellspacing=0 width=156 border=0 style='border-collapse:collapse; border-spacing:1px;'>";

	if(substr_count ($admininfo[permit], "12-01")){
		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/cogoods/hostserver.php' class='menu_style1_a'>호스트서버관리</a></td></tr>";
	}
	if(substr_count ($admininfo[permit], "12-02")){
		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/cogoods/co_seller_shop.php' class='menu_style1_a'>공유서버 업체 등록</a></td></tr>";
	}
	if(substr_count ($admininfo[permit], "12-03")){
		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/cogoods/co_sellershop_apply.php' class='menu_style1_a'>입점신청관리</a></td></tr>";
	}
	if(substr_count ($admininfo[permit], "12-04")){
		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/cogoods/co_goods.php' class='menu_style1_a'>상품공유하기</a></td></tr>";
	}
	/*
		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/cogoods/co_goods.php' class='menu_style1_a'>상품공유하기</a></td></tr>";
	//if(substr_count ($admininfo[permit], "12-04")){
		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/cogoods/co_goods_server.php?co_type=co_goods_local' class='menu_style1_a'>공유상품 가져오기</a></td></tr>";
	//}
	*/

	$mstring.= "
	</table>";

	return $mstring;
}

function inventory_menu($default_path='/admin', $category_tree=""){
	global $admininfo;
	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_inventory.gif'></td></tr>
	</table>";
	$mstring .= "
	<table cellpadding=0 class='line_color' cellspacing=1 width=156 border=0 style='border-collapse:collapse; border-spacing:1px;'>";//class='table_border'

	$menus = getMenuData("inventory");
	$mstring .= getnerateLeftMenu($default_path, $menus);
	/*
	for($i=0;$i < count($menus);$i++){
		$mstring.= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='".$menus[$i][menu_link]."' class='menu_style1_a'>".$menus[$i][menu_name]."</a></td></tr>";
	}
	$mstring.= "</table><br>";
*/
/*
	$mstring.= "
	<table cellpadding=0 class='line_color' cellspacing=1 width=156 border=0 style='border-collapse:collapse; border-spacing:1px;'>";
	if($category_tree){
	$mstring .= "
			<tr height=0 bgcolor='#FFFFFF'><td align=left>$category_tree</td></tr>";
	}
	$mstring .= "<tr height=20 bgcolor='#FCE7C8'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/inventory/inventory_goods_input.php' class='menu_style1_a'>재고관리 상품등록</a></td></tr>";
	$mstring .= "<tr height=20 bgcolor='#FCE7C8'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/inventory/stock_report.php' class='menu_style1_a'>재고현황</a></td></tr>";
	if(substr_count ($admininfo[permit], "13-01")){
		$mstring .= "<tr height=20 bgcolor='#FCE7C8'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/inventory/stock_input.list.php' class='menu_style1_a'>입고내역</a></td></tr>";
	}
	if(substr_count ($admininfo[permit], "13-02")){
		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/inventory/stock_output.list.php' class='menu_style1_a'>출고내역</a></td></tr>";
	}
	if(substr_count ($admininfo[permit], "13-05")){
		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/inventory/warehousing_list.php' class='menu_style1_a'>입고처관리</a></td></tr>";
	}
	if(substr_count ($admininfo[permit], "13-06")){
		$mstring .= "<tr height=20 bgcolor='#FCE7C8'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/inventory/sale_agency_list.php' class='menu_style1_a'>판매처관리</a></td></tr>";
	}
	if(substr_count ($admininfo[permit], "13-07")){
		$mstring .= "<tr height=20 bgcolor='#FCE7C8'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/inventory/storehouse_list.php' class='menu_style1_a'>보관장소관리</a></td></tr>";

	}

	$mstring .= "</table>";
*/
	return $mstring;

}



function campaign_menu($default_path='/admin', $category_tree=""){
	global $admininfo;
	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_campaign.gif'></td></tr>
	</table>";
	$mstring .= "
	<table cellpadding=0 class='line_color' cellspacing=0 width=156 border=0 style='border-collapse:collapse; border-spacing:1px;'>";//class='table_border'
	/*
	$menus = getMenuData("campaign");

	for($i=0;$i < count($menus);$i++){
		$mstring.= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='".$menus[$i][menu_link]."' class='menu_style1_a'>".$menus[$i][menu_name]."</a></td></tr>";
	}
	$mstring.= "</table><br>";
	*/
	$mstring.= "
	<table cellpadding=4 bgcolor='#c0c0c0' cellspacing=1 width=156 border=0 style='border-collapse:separate; border-spacing:1px;'>";
	if($category_tree){
	$mstring .= "<tr height=0 bgcolor='#FFFFFF'><td align=left>$category_tree</td></tr>";
	}

	//if(substr_count ($admininfo[permit], "14-02")){
		$mstring .= "<tr height=23 bgcolor='#FCE7C8'><td align=left class='leftmenu' ><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/campaign/addressbook_list.php' class='menu_style1_a'>메일링/SMS 관리</a></td></tr>";
	//}
	//if(substr_count ($admininfo[permit], "14-01")){
		$mstring .= "<tr height=23 bgcolor='#FCE7C8'><td align=left class='leftmenu' ><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/campaign/addressbook_group.php' class='menu_style1_a'>주소록 그룹관리</a></td></tr>";
	//}
	//if(substr_count ($admininfo[permit], "14-03")){
		$mstring .= "<tr height=23 bgcolor='#FFFFFF'><td align=left class='leftmenu' ><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/campaign/addressbook_add.php' class='menu_style1_a'>주소록 등록관리</a></td></tr>";
	//}
	//if(substr_count ($admininfo[permit], "14-04")){
		$mstring .= "<tr height=23 bgcolor='#FFFFFF'><td align=left class='leftmenu' ><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/campaign/addressbook_add_excel.php' class='menu_style1_a'>주소록 일괄등록</a></td></tr>";

		$mstring .= "<tr height=23 bgcolor='#FFFFFF'><td align=left class='leftmenu' ><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/campaign/mail_list.php' class='menu_style1_a'>메일목록</a></td></tr>";
	//}


	$mstring .= "</table>";

	return $mstring;

}


function buyingservice_menu($default_path='/admin', $category_tree=""){
	global $admininfo;
	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_campaign.gif'></td></tr>
	</table>";
	$mstring .= "
	<table cellpadding=0 class='line_color' cellspacing=0 width=156 border=0 style='border-collapse:collapse; border-spacing:1px;'>";//class='table_border'
	/*
	$menus = getMenuData("campaign");

	for($i=0;$i < count($menus);$i++){
		$mstring.= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='".$menus[$i][menu_link]."' class='menu_style1_a'>".$menus[$i][menu_name]."</a></td></tr>";
	}
	$mstring.= "</table><br>";
	*/
	$mstring.= "
	<table cellpadding=4 bgcolor='#c0c0c0' cellspacing=1 width=156 border=0 style='border-collapse:separate; border-spacing:1px;'>";

	//if(substr_count ($admininfo[permit], "14-02")){
		$mstring .= "<tr height=23 bgcolor='#FCE7C8'><td align=left class='leftmenu' ><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/campaign/addressbook_list.php' class='menu_style1_a'>사입신청목록</a></td></tr>";
	//}
	//if(substr_count ($admininfo[permit], "14-01")){
		$mstring .= "<tr height=23 bgcolor='#FCE7C8'><td align=left class='leftmenu' ><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/campaign/addressbook_group.php' class='menu_style1_a'>주소록 그룹관리</a></td></tr>";
	//}
	//if(substr_count ($admininfo[permit], "14-03")){
		$mstring .= "<tr height=23 bgcolor='#FFFFFF'><td align=left class='leftmenu' ><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/campaign/addressbook_add.php' class='menu_style1_a'>주소록 등록관리</a></td></tr>";
	//}
	//if(substr_count ($admininfo[permit], "14-04")){
		$mstring .= "<tr height=23 bgcolor='#FFFFFF'><td align=left class='leftmenu' ><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/campaign/addressbook_add_excel.php' class='menu_style1_a'>주소록 일괄등록</a></td></tr>";

		$mstring .= "<tr height=23 bgcolor='#FFFFFF'><td align=left class='leftmenu' ><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/campaign/mail_list.php' class='menu_style1_a'>메일목록</a></td></tr>";
	//}


	$mstring .= "</table>";

	return $mstring;

}


function work_menu2($default_path='/admin', $category_tree=""){
	global $admininfo;
	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/".$admininfo[language]."/left_title_work.gif'></td></tr>
	</table>
	<table cellpadding=0 width=100% cellspacing=1 border=0 bgcolor='#c0c0c0' style='border-collapse:separate; border-spacing:1px;margin-bottom:5px;' >
		<col width=50%>
		<col width=*>
		<tr bgcolor='#FCE7C8'>
			<td align=center class='leftmenu' style='height:24px;padding:0px;border-right:1px solid #c0c0c0'>
			<a href='work_list.php?list_view_type=calendar'><b>스케줄</b></a>
			</td>
			<td align=center class='leftmenu' style='padding:0px''>
			<a href='work_list.php?list_type=myjob'><b>업무</b></a>
			</td>
		</tr>
	</table>
	<table cellpadding=0 width=100% cellspacing=0 border=0 bgcolor='#c0c0c0' style='margin-bottom:5px;' class='table_border'>
		<tr height=24 bgcolor='#FCE7C8'>
			<td align=center class='leftmenu' style='height:25px;padding:0px;'>
			<a href='work_add.php?list_view_type=".$list_view_type."'><b>스케줄/업무 등록</b></a>
			</td>

		</tr>
	</table>
	<table cellpadding=0 bgcolor='#c0c0c0' cellspacing=1 width=156 border=0 style='border-collapse:separate; border-spacing:1px;'>";

	if(substr_count ($admininfo[permit], "15-01")){
		$mstring .= "<tr height=20 bgcolor='#FCE7C8'><td align=left class='leftmenu' style='height:25px;'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/work/user.php' class='menu_style1_a'>사용자 관리</a></td></tr>";
	}
	if(substr_count ($admininfo[permit], "15-01")){
		$mstring .= "<tr height=20 bgcolor='#FCE7C8'><td align=left class='leftmenu' style='height:24px;'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/work/work_group.php' class='menu_style1_a'>업무 그룹관리</a></td></tr>";
	}
	/*
	if(substr_count ($admininfo[permit], "15-02")){
		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/work/work_add.php' class='menu_style1_a'>업무 작성</a></td></tr>";
	}*/
	if(substr_count ($admininfo[permit], "15-03")){
		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu' style='height:25px;'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/work/work_list.php' class='menu_style1_a'>업무 목록</a></td></tr>";
	}
	if(substr_count ($admininfo[permit], "15-04")){
		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu' style='height:25px;'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/work/work_reprot.php' class='menu_style1_a'>보고서 관리</a></td></tr>";
	}
	$mstring .= "</table>";

$mstring .= "<table cellpadding=0 width=100% cellspacing=0 border=0 bgcolor='#c0c0c0' style='margin-top:5px;' class='table_border'>
	<tr  bgcolor='#FCE7C8'>
		<td align=center class='leftmenu' style='height:25px;padding:0px;'>
		<a href='work_report.php'><b>보고서출력하기</b></a>
		</td>
	</tr>
</table>
	";
	return $mstring;

}



function work_menu(){
global $mdb, $admininfo, $list_view_type;
$mstring = "
<table cellpadding=0  cellspacing=0 width=156 border=0>
	<tr><td align=center style='padding-bottom:5px;'><a href='../work/'><img src='../images/".$admininfo[language]."/left_title_work.gif'></a></td></tr>
</table>

<table cellpadding='0' width='100%' bgcolor='#c0c0c0' cellspacing=1 style='border-collapse:separate; border-spacing:1px;margin-bottom:5px;'  >
	<tr height=24 bgcolor='#efefef'>
		<td align=left class='leftmenu' style='padding:0px;' >

		<div style='height:1px;width:1px;position:absolute;".(($_COOKIE["company_goal_view"] == 1 || $_COOKIE["company_goal_view"] == '')? "display:inline;":"display:none;")."' id='company_goal' >
			<div style='z-index:200;position:relative;left:110px;top:-100px;'>
				<table class='tooltip' border='0' cellpadding='0' cellspacing='0' style='width:600px;height:0px;display:block;' >
					<col width='6px'>
					<col width='10px'>
					<col width='23px'>
					<col width='*'>
					<col width='14px'>
					<tr>
						<th class='tooltip_01'></th>
						<td class='tooltip_02' colspan=3></td>
						<th class='tooltip_03'></th>
					</tr>
					<tr>
						<th class='tooltip_04'></th>
						<td class='tooltip_05' colspan=3 valign=top style='width:600px;height:100%;padding:7px 5px 5px 15px;font-weight:bold;font-size:12px;line-height:150%;text-align:left;' >
						<table width='560px'>
							<tr>
								<td style='color:#ffffff;font-size:16px;line-height:140%;font-weight:bold;'>".nl2br($admininfo["work_op_confs"]["company_goal"])."</td>
								<td valign=top align=right><a href=\"javascript:ToggleComapnyGoal();\"><img src='../images/x.gif'></a></td>
							</tr>
						</table>
						</td>
						<th class='tooltip_06'></th>
					</tr>
					<tr>
						<th class='tooltip_07'></th>
						<td class='tooltip_08'></td>
						<td class='tooltip_08_'><img src='../images/common/tooltip01/bg-tooltip_08_la.png'>	</td>
						<td class='tooltip_08'></td>
						<th class='tooltip_09'></th>
					</tr>
				</table>
			</div>
		</div>
		<div align=center style='vertical-align:middle;padding:6px 0px 6px 0px;'><b onclick=\"ToggleComapnyGoal();\" style='cursor:pointer;font-weight:bold;' class=small>".$admininfo["work_op_confs"]["company_goal_title"]."</b></div>
		</td>

	</tr>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='1' bgcolor='#c0c0c0' style='border-collapse:separate; border-spacing:1px;margin-bottom:5px;'>
	<col width=50%>
	<col width=*>
	<tr bgcolor='#efefef'>
		<td align=center class='leftmenu' style='height:25px;padding:0px;border-right:1px solid #c0c0c0'>
		<a href='work_list.php?list_view_type=calendar'><b>스케줄</b></a>
		</td>
		<td align=center class='leftmenu' style='padding:0px''>
		<a href='work_list.php?list_type=myjob'><b>업무</b></a>
		</td>
	</tr>
</table>
<table cellpadding='0' width='100%' bgcolor='#c0c0c0' cellspacing=1 style='border-collapse:separate; border-spacing:1px;margin-bottom:5px;' >
	<tr height=24 bgcolor='#efefef'>
		<td align=center class='leftmenu' style='padding:0px;height:25px;'>
		<a href='work_add.php?list_view_type=".$list_view_type."'><b>스케줄/업무 등록</b></a>
		</td>

	</tr>
</table>";
if(substr_count ($_SERVER["PHP_SELF"],"work_list.php")){
$mstring .= "
<table cellpadding=0 bgcolor='#c0c0c0' cellspacing='1' width=100% border=0 style='border-collapse:separate; border-spacing:1px;'>";
if(is_array($admininfo["work_confs"]["config_leftmenu"]) ? (in_array( "department", $admininfo["work_confs"]["config_leftmenu"]) ? "checked":""):""){
$mstring .= "
	<tr height=20 bgcolor='#efefef'>
		<td align=left class='leftmenu' onclick=\"ToggleTreeUser();\" style='style='height:25px;padding:0px 0px 0px 10px;cursor:hand;' title='클릭하시면 부서/직원 트리메뉴가 노출됩니다.'>
		<IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<b>부서 / 직원</b>
		</td>
	</tr>
	<tr height=0 bgcolor='#FFFFFF'>
		<td align=left style='border-top:0px solid gray;vertical-align:top;".($_COOKIE["tree_user_view"] == '0' ? "height:0px;":"height:150px;")."'>
		<link href='./dynatree/skin/ui.dynatree.css' rel='stylesheet' type='text/css' id='skinSheet'>
		<script src='./dynatree/jquery.dynatree.js' type='text/javascript'></script>
		<script type='text/javascript' src='work.tree.js'></script>
		<div id='tree_user'  style='".($_COOKIE["tree_user_view"] == '0' ? "display:none;":"")."overflow-y:auto;width:155px;height:150px;border: 0px solid silver;'></div>
		</td>
	</tr>";
}
if(is_array($admininfo["work_confs"]["config_leftmenu"]) ? (in_array( "workgroup", $admininfo["work_confs"]["config_leftmenu"]) ? "checked":""):""){
$mstring .= "
	<tr height=20 bgcolor='#efefef'>
		<td align=left class='leftmenu' style='border-top:1px solid #c0c0c0;cursor:hand;height:25px;padding:0px 0px 0px 10px;' onclick=\"ToggleTreeWorkGroup();\" title='클릭하시면 업무분류 트리메뉴가 노출됩니다.'>
		<IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<b>업무분류</b>
		</td>
	</tr>
	<tr height=0 bgcolor='#FFFFFF'>
		<td align=left style='border-top:0px solid gray;vertical-align:top;".($_COOKIE["tree_work_group_view"] == '0' ? "height:0px;":"height:150px;")."'>
			<div id='tree_work_group' style='".($_COOKIE["tree_work_group_view"] == '0' ? "display:none;":"")."overflow-y:auto;overflow-x:hidden;height:150px;width:155px;border: 0px solid red;'></div>
		</td>
	</tr>";
}
$mstring .= "
</table>";
}
$mstring .= "
<table cellpadding=0 width=100% bgcolor='#c0c0c0' cellspacing=1 style='border-collapse:separate; border-spacing:1px;margin-top:5px;' >";

	//if(substr_count ($admininfo[permit], "15-01")){
	if($admininfo[master] == "Y"){
		$mstring .= "<tr height=20 bgcolor='#efefef'><td align=left class='leftmenu' style='height:25px;border-top:1px solid #c0c0c0'><IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='../work/user.php' class='menu_style1_a'>사용자 관리</a></td></tr>";
	}else{
		$mstring .= "<tr height=20 bgcolor='#efefef'><td align=left class='leftmenu' style='height:25px;border-top:1px solid #c0c0c0'><IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='../work/user.php' class='menu_style1_a'>나의정보 관리</a></td></tr>";
	}
	//}
if($admininfo[master] == "Y"){
	//if(substr_count ($admininfo[permit], "15-01")){
		$mstring .= "<tr height=20 bgcolor='#efefef'><td align=left class='leftmenu' style='height:25px;'><IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='../work/work_group.php' class='menu_style1_a'>업무 그룹관리</a></td></tr>";
	//}
}
	//if(substr_count ($admininfo[permit], "15-01")){
		$mstring .= "<tr height=20 bgcolor='#efefef'><td align=left class='leftmenu' style='height:25px;'><IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='../work/work_report_list.php' class='menu_style1_a'>보고서 </a></td></tr>";
//if($admininfo[master] == "Y" && $admininfo["charger_id"] == "sigi1074"){
		$mstring .= "<tr height=20 bgcolor='#efefef'><td align=left class='leftmenu' style='height:25px;'><IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='../work/work_comment_list.php' class='menu_style1_a'>컴멘트 목록 </a></td></tr>";

		$mstring .= "<tr height=20 bgcolor='#efefef'><td align=left class='leftmenu' style='height:25px;'><IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='../work/work_issue_list.php' class='menu_style1_a'>이슈 목록 </a></td></tr>";
//}
	//}
	$mstring .= "</table>";
if($admininfo[master] == "Y" && $admininfo["charger_id"] == "sigi1074" && $admininfo["charger_id"] == "forbiz"){
$mstring .= "
<table cellpadding=0 width=100% cellspacing=0 border=0 bgcolor='#c0c0c0' style='margin-top:5px;' class='table_border'>
	<tr height=23 bgcolor='#efefef'>
		<td align=center class='leftmenu' style='padding:0px;height:25px;'>
		<a href=\"javascript:PopSWindow('work_report.php?mmode=pop',810,750,'work_report_make')\"><b>보고서 작성하기</b></a>
		</td>
	</tr>
</table>
	";
}

return $mstring;

}

function deepzoom_menu(){
global $mdb, $admininfo, $list_view_type;
$mstring = "
<table cellpadding=0  cellspacing=0 width=156 border=0>
	<tr><td align=center style='padding-bottom:5px;'><img src='../images/leftmenu/left_title_deepzoom.gif'></td></tr>
</table>


<table cellpadding='0' width='100%' bgcolor='#c0c0c0' cellspacing=1 style='border-collapse:separate; border-spacing:1px;margin-bottom:5px;' >
	<tr height=24 bgcolor='#efefef'>
		<td align=center class='leftmenu' style='padding:0px;height:25px;'>
		<a href='./' ><b>이미지 목록</b></a>
		</td>
	</tr>
</table>

<table width='100%' border='0' cellpadding='0' cellspacing='0' bgcolor='#c0c0c0'  style='margin-bottom:5px;' class='table_border'>
	<col width=50%>
	<col width=*>
	<tr height=24 bgcolor='#efefef'>
		<td align=center class='leftmenu' style='height:25px;padding:0px;border-right:1px solid #c0c0c0'>
		<a href=\"javascript:LayerShow('deepzoomreg')\" id='deepzoom_reg'><b>이미지 등록</b></a><!--DeepZoomReg()-->
		<td align=center class='leftmenu' style='padding:0px''>
		<a href='gallery.php'><b>갤러리등록</b></a>
		</td>
	</tr>
</table>";
if(substr_count ($_SERVER["PHP_SELF"],"index.php")){
$mstring .= "
<table cellpadding=0 bgcolor='#c0c0c0' cellspacing=1 width=100% border=0 style='border-collapse:separate; border-spacing:1px;'>

	<tr height=25 bgcolor='#efefef'>
		<td align=left class='leftmenu' style='height:25px;' onclick=\"alert($('#tree_image_group').html());\" style='cursor:hand;' title='클릭하시면 부서/직원 트리메뉴가 노출됩니다.'>
		<IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<b>이미지분류</b>
		</td>
	</tr>
	<tr height=25 bgcolor='#FFFFFF'>
		<td align=left style='height:25px;border-top:0px solid gray;vertical-align:top;".($_COOKIE["tree_image_group_view"] == '0' ? "height:0px;":"height:150px;")."'>
		<div id='tree_image_group' style='".($_COOKIE["tree_image_group_view"] == '0' ? "display:none;":"")."overflow-y:auto;overflow-x:hidden;height:140px;width:155px;border: 0px solid silver;'></div>
		</td>
	</tr>
</table>";
}
$mstring .= "
<table cellpadding=0 width=100% bgcolor='#c0c0c0' cellspacing=1 style='border-collapse:separate; border-spacing:1px;'>";
	//if(substr_count ($admininfo[permit], "15-01")){
		$mstring .= "<tr height=20 bgcolor='#efefef'><td align=left class='leftmenu' style='height:25px;'><IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='../work/user.php' class='menu_style1_a'>사용자 관리</a></td></tr>";
	//}
	//if(substr_count ($admininfo[permit], "15-01")){
		$mstring .= "<tr height=20 bgcolor='#efefef'><td align=left class='leftmenu' style='height:25px;'><IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='../deepzoom/image_group.php' class='menu_style1_a'>이미지 그룹관리</a></td></tr>";
	//}
	$mstring .= "<tr height=20 bgcolor='#efefef'><td align=left class='leftmenu' style='height:25px;'><IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='../deepzoom/gallery.list.php' class='menu_style1_a'>갤러리관리</a></td></tr>";
	$mstring .= "

</table>
	";


return $mstring;

}




function kms_menu(){
global $mdb, $admininfo, $list_view_type;
include("category.lib.php");

$mstring = "
<table cellpadding=0  cellspacing=0 width=156 border=0>
	<tr><td align=center style='padding-bottom:5px;'><img src='../images/leftmenu/left_title_kms.gif'></td></tr>
</table>

<table cellpadding='0' width='100%' cellspacing='0' border='0' bgcolor='#c0c0c0' style='margin-bottom:5px;' class='table_border'>
	<tr height=24 bgcolor='#efefef'>
		<td align=center class='leftmenu' style='padding:0px;'>
		<a href='kms_add.php?list_view_type=".$list_view_type."'><b>지식 등록</b></a>
		</td>

	</tr>
</table>
<table cellpadding=0 bgcolor='#c0c0c0' cellspacing='0' width=100%  class='table_border' >

	<tr height=20 bgcolor='#efefef'>
		<td align=left class='leftmenu' onclick=\"ToggleTreeUser();\" style='cursor:hand;padding:6px 3px 6px 5px' title='클릭하시면 부서/직원 트리메뉴가 노출됩니다.'>
		<IMG id=SM114641I src='../images/icon/dot_orange_triangle.gif' border=0>&nbsp;<b>내 지식분류</b>
		</td>
	</tr>
	<tr height=0 bgcolor='#FFFFFF'>
		<td align=left style='border-top:0px solid gray;vertical-align:top;".($_COOKIE["tree_user_view"] == '0' ? "height:180px;":"height:180px;")."'>

		<div  id='kms_tree' style=height:180px;width:155px;overflow:auto;padding:10px 10px 40px 10px'>".Category()."</div>
		</td>
	</tr>

</table>

	";
$mstring .= "
<table cellpadding=0 width=100% cellspacing=0 border=0 bgcolor='#c0c0c0' style='margin-top:5px;' class='table_border'>
	<tr height=23 bgcolor='#efefef'>
		<td align=center class='leftmenu' style='padding:0px;height:25px;'>
		<a href=\"javascript:LayerShow('kms_category_box')\"><b>지식분류 관리</b></a>
		</td>
	</tr>
</table>";

return $mstring;

}

function getMenuData($menu_div){
	global $admininfo, $admin_config;
	$mdb = new Database;

	if($admininfo[mall_type] == "B"){// 입점형
		$auth_where = 	" and am.use_business = 'Y' ";
	}else if($admininfo[mall_type] == "F" || $admininfo[mall_type] == "R"){ // 무료형 , 임대형
		$auth_where = 	" and am.use_soho = 'Y' ";
	}else if($admininfo[mall_type] == "O"){ // 오픈마켓형
	}else{
		$auth_where = 	" ";
	}

	if($admin_config[mall_use_inventory] == "Y"){
		$inventory_str = " and am.menu_code != '43037c74398b256800880b01a050c1c7' ";
	}
	
	if($menu_div == "logstory/report"){
	$sql = "Select am.*, aatd.auth_read, aatd.auth_write_update, aatd.auth_delete
			from admin_menus am left join admin_auth_templet_detail aatd on am.menu_code = aatd.menu_code and auth_templet_ix = '".$admininfo[charger_roll]."'
			and am.menu_div in ('".$menu_div."','logstory/manage') and am.disp_auth = 'Y'
			where am.menu_div in ('".$menu_div."','logstory/manage') and disp_auth = 'Y' and auth_read = 'Y'
			$auth_where $inventory_str
			order by  am.view_order asc, am.regdate asc ";//order by view_order asc
	}else{
	$sql = "Select am.*, aatd.auth_read, aatd.auth_write_update, aatd.auth_delete
			from admin_menus am left join admin_auth_templet_detail aatd on am.menu_code = aatd.menu_code and auth_templet_ix = '".$admininfo[charger_roll]."'
			and am.menu_div = '".$menu_div."' and am.disp_auth = 'Y'
			where am.menu_div = '".$menu_div."' and disp_auth = 'Y' and auth_read = 'Y'
			$auth_where $inventory_str
			order by  am.view_order asc, am.regdate asc ";//order by view_order asc
	}

	//echo nl2br($sql);
	$mdb->query($sql);
	$menus = $mdb->fetchall();
	return $menus;
}

function getnerateLeftMenu($default_path, $menus, $category_tree=""){
	global $admininfo;
	//echo "aaa : ".$_SERVER["PHP_SELF"];

	$mstring .= "
	<table cellpadding=0  cellspacing=0 width=156 border=0 style='border-collapse:collapse;border:1px solid #c0c0c0;'>";//class='table_border'
	if($category_tree){
	$mstring .= "<tr height=0 bgcolor='#FFFFFF'><td align=left >$category_tree</td></tr>";
	}
	for($i=0;$i < count($menus);$i++){

		if($admininfo[mall_type] == "H"){
			$menu_name = str_replace("쇼핑몰","사이트",$menus[$i][menu_name]);
			$menu_name = str_replace("이벤트/기획전","행사",$menu_name);
			$navis = explode(">",str_replace("HOME > ","",$menu_name));
		}else{
			$menu_name = $menus[$i][menu_name];
			$navis = explode(">",str_replace("HOME > ","",$menus[$i][menu_name]));
		}
		//echo $_SERVER["PHP_SELF"].":::".md5($_SERVER["PHP_SELF"]) ."==". $menus[$i][menu_code]."<br>";

		if(md5($_SERVER["PHP_SELF"]) == $menus[$i][menu_code]){

			if($menus[$i][menu_param] && substr_count ($menus[$i][menu_link], $menus[$i][menu_param]."=".$_GET[$menus[$i][menu_param]])){
				$selectedClass = "leftmenu_seleted";
			}else if($menus[$i][menu_param] == ""){
				$selectedClass = "leftmenu_seleted";
			}else{
				$selectedClass = "";
			}
		}else{
			$selectedClass = "";
		}


		if(count($navis[1]) >= 1){


			if($depth1_name != $navis[0]){
				$mstring .= "<tr height=23 bgcolor='#FFFFFF'>
								<td style='border-collapse:separate; border:1px solid #c0c0c0;' align=left class='".(substr_count($menu_name,$navis[0]) ? "leftmenu":"")."'>
									<table cellpadding=0 cellspacing=0>
										<tr>
											<td width=10><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp; </td>
											<td><a href='".$menus[$i][menu_link]."' class='".$selectedClass."'><b>".$navis[0]." </b></a></td>
										</tr>
									</table>

								</td>
							</tr>";

			}
			$depth1_name = $navis[0];
			$mstring .= "<tr height=23 bgcolor='#FFFFFF' style='border-collapse:collapse;'>
							<td align=left class='".$selectedClass."' style='padding:0px 0px 0px 15px;border-collapse:collapse;border:0px;' >
							<table cellpadding=0 cellspacing=0>
								<tr>
									<td width=10><img src='/admin/images/icon/dot_orange.gif' align=absmiddle>  </td>
									<td><a href='".$menus[$i][menu_link]."' class='".$selectedClass."'>".(count($navis[0]) >= 1 ? $navis[1]:$menu_name)." </a></td>
								</tr>
							</table>
							</td>
						</tr>";


		}else{
			$mstring.= "<tr height=20 bgcolor='#FFFFFF'>
				<td align=left class='leftmenu' style='border-collapse:separate; border:1px solid #c0c0c0;padding:3px 0px 3px 10px;'>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td width=10><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0></td>
							<td><a href='".$menus[$i][menu_link]."' class='menu_style1_a'><b>".$menu_name."</b></a></td>
						</tr>
					</table>
				</td>
				</tr>";
		}
	}
	$mstring.= "</table>";

	return $mstring;
}



function checkMenuAuth($menu_code, $check_type = "R"){
	global $admininfo;
	$mdb = new Database;
	//print_r($admininfo);
//echo nl2br($sql);
//	exit;
	if($admininfo[mall_type] == "B"){// 입점형
		$auth_where = 	" and am.use_business = 'Y' ";
	}else if($admininfo[mall_type] == "F" || $admininfo[mall_type] == "R"){ // 무료형 , 임대형
		$auth_where = 	" and am.use_soho = 'Y' ";
	}else{
		$auth_where = 	" ";
	}
	$sql = "Select am.*, aatd.auth_read, aatd.auth_write_update, aatd.auth_delete, aatd.auth_excel
			from admin_menus am ,admin_auth_templet_detail aatd
			where am.menu_code = aatd.menu_code and auth_templet_ix = '".$admininfo[charger_roll]."'
			$auth_where
			and am.menu_code = '".$menu_code."' and (am.disp_auth = 'Y' or auth_read = 'Y')
			";//order by view_order asc

	//echo nl2br($sql)."<br><br>";
	//exit;
	$mdb->query($sql);
	$mdb->fetch();
	//echo "auth_read : ".$mdb->dt[auth_read] .":::".$mdb->total;

	if($mdb->total){
		if($check_type == "R"){
			if($mdb->dt[auth_read] == "N"){
				echo "<script language='javascript'>alert('해당메뉴에 대한 접근권한이 없습니다.1');history.back();</script>";
				exit;
			}
		}else if($check_type == "C" || $check_type == "U"){
			return ($mdb->dt[auth_write_update] == "Y" ? true:false);
		}else if($check_type == "D"){
			return ($mdb->dt[auth_delete] == "Y" ? true:false);
		}else if($check_type == "E"){
			return ($mdb->dt[auth_excel] == "Y" ? true:false);
		}
	}else{
		//echo "<script language='javascript'>alert('해당메뉴에 대한 접근권한이 없습니다. ".$_SERVER["PHP_SELF"]."');history.back();</script>";
		//exit;
	}

	return $menus;
}

function gnbGenerate($display_type='all'){
	global $admininfo;


	$mdb = new Database;
	//echo $sql;
	if($admininfo[mall_type] == "B"){// 입점형
		$sql = 	"SELECT * FROM admin_menu_div where disp = 1 and gnb_use_biz = 'Y' order by vieworder asc ";
	}else if($admininfo[mall_type] == "F" || $admininfo[mall_type] == "R"){ // 무료형 , 임대형
		$sql = 	"SELECT * FROM admin_menu_div where disp = 1 and gnb_use_soho = 'Y'  order by vieworder asc ";
	}else{
		$sql = 	"SELECT * FROM admin_menu_div where disp = 1 order by vieworder asc ";
	}
	$mdb->query($sql);
	$gnbs = $mdb->fetchall();



	if($display_type == "all" || $display_type == "second"){
		for($i=0;$i < count($gnbs);$i++){
			$menus = getMenuData($gnbs[$i][div_name]);
			//print_r($menus);
			$mstring .= "var subMenus_".str_replace("/","_",$gnbs[$i][div_name])." = new Array();\n\n";

			for($j=0,$add_j=0;$j < count($menus);$j++){
				if($admininfo[mall_type] == "H"){
					$menu_name = str_replace("쇼핑몰","사이트",$menus[$j][menu_name]);
					$menu_name = str_replace("이벤트/기획전","행사",$menu_name);
					$navis = explode(">",str_replace("HOME > ","",$menu_name));
				}else{
					$menu_name = $menus[$j][menu_name];
					$navis = explode(">",str_replace("HOME > ","",$menus[$j][menu_name]));
				}

				//$navis = explode(">",str_replace("HOME > ","",$menus[$j][menu_name]));

				if(count($navis[1]) >= 1){
					if($depth1_name != $navis[0]){
						$mstring .= "subMenus_".str_replace("/","_",$gnbs[$i][div_name])."[".($j+$add_j)."] = new Array();\n";
						$mstring .= "subMenus_".str_replace("/","_",$gnbs[$i][div_name])."[".($j+$add_j)."][0] = \"document.location.href='".$menus[$j][menu_link]."'\";\n";
						$mstring .= "subMenus_".str_replace("/","_",$gnbs[$i][div_name])."[".($j+$add_j)."][1] = \"".$navis[0]."\";\n";
						$mstring .= "subMenus_".str_replace("/","_",$gnbs[$i][div_name])."[".($j+$add_j)."][2] = \"normal\";\n\n";
						$add_j++;
					}
					$depth1_name = $navis[0];

					$mstring .= "subMenus_".str_replace("/","_",$gnbs[$i][div_name])."[".($j+$add_j)."] = new Array();\n";
					$mstring .= "subMenus_".str_replace("/","_",$gnbs[$i][div_name])."[".($j+$add_j)."][0] = \"document.location.href='".$menus[$j][menu_link]."'\";\n";
					$mstring .= "subMenus_".str_replace("/","_",$gnbs[$i][div_name])."[".($j+$add_j)."][1] = \"".(count($navis[0]) >= 1 ? trim($navis[1]):trim($menu_name))."\";\n";
					$mstring .= "subMenus_".str_replace("/","_",$gnbs[$i][div_name])."[".($j+$add_j)."][2] = \"submenu\";\n\n";

				}else{
					$mstring .= "subMenus_".str_replace("/","_",$gnbs[$i][div_name])."[".($j+$add_j)."] = new Array();\n";
					$mstring .= "subMenus_".str_replace("/","_",$gnbs[$i][div_name])."[".($j+$add_j)."][0] = \"document.location.href='".$menus[$j][menu_link]."'\";\n";
					$mstring .= "subMenus_".str_replace("/","_",$gnbs[$i][div_name])."[".($j+$add_j)."][1] = \"".trim($menu_name)."\";\n";
					$mstring .= "subMenus_".str_replace("/","_",$gnbs[$i][div_name])."[".($j+$add_j)."][2] = \"normal\";\n\n";
				}
			}
		}
	}

	if($display_type == "all" || $display_type == "first"){
		for($i=0;$i < count($gnbs);$i++){
			$mstring .= "generateGNBLayer('".str_replace("/","_",$gnbs[$i][div_name])."', subMenus_".str_replace("/","_",$gnbs[$i][div_name]).");\n";
		}
		$mstring .= "\n\n";
	}

/*
	if($display_type == "all" || $display_type == "first"){
		$mstring .= "$(document).ready(function() {\n";
		for($i=0;$i < count($gnbs);$i++){
			$mstring .= "$('#gnb_link_".str_replace("/","_",$gnbs[$i][div_name])."').mouseover(function(){
						showSubMenuLayer('".str_replace("/","_",$gnbs[$i][div_name])."');
					});
					$('#gnb_link_".$gnbs[$i][div_name]."').mouseout(function(){
						hideSubMenuLayer('".str_replace("/","_",$gnbs[$i][div_name])."');
					});
				";

		}
		$mstring .= "});\n\n";
	}
*/

	//print_r($admininfo);

	$sql = "select text_korea, text_trans from admin_language where language_type = '".$admininfo["language"]."' and text_div LIKE '%_lnb' order by CHAR_LENGTH(text_korea) desc  ";
	//echo $sql;
	$mdb->query($sql);
	if($mdb->total){
	//echo "test";
		$dics = $mdb->fetchall();
		//print_r($dics);

		for($i=0;$i < count($dics);$i++){
			$mstring = str_replace($dics[$i]["text_korea"], str_replace("\n","",trim($dics[$i]["text_trans"])), $mstring);
		}
	}
	return $mstring;

}


function getTransDiscription($menu_code , $dic_code, $db="", $var_name="coupon_data", $trans_link_bool=true){
	global $admininfo;
	$sql = "select * from admin_dic where menu_code = '".md5($_SERVER["PHP_SELF"])."' and language_type = '".$admininfo["language"]."' and dic_type = 'DESC' and dic_code = '$dic_code' ";
	//echo $sql."<br><br>";
	$mdb = new Database;
	$mdb->query($sql);
	$mdb->fetch();

	if($mdb->total){
		//exit;
	//echo "test";
	/*
		if($dic_code == "R"){
			echo $var_name."<br>";
			print_r($db);
		}
	*/
		$desc_trans = str_replace("\\","\\\\",$mdb->dt[desc_trans]);
		//$desc_trans = str_replace("'","\'",$desc_trans);
		//$desc_trans = str_replace("\\","\\\\",$desc_trans);
		//$desc_trans = str_replace("\"","\\\"",$desc_trans);

		if((is_array($db) || is_object($db)) && $var_name != ""){
			//exit;
			$$var_name = $db;
			try{
			eval("\$str = \"".$desc_trans."\";");
			//echo("\$str = \"".$desc_trans."\";<br><Br>");
			//echo("\$str = \"".$mdb->dt[desc_trans]."\";<br>");

			}catch(Exception  $e){

			}


			if($admininfo[charger_id] == "forbiz"  && $trans_link_bool ){
				$mstring =  "<div style='font-weight:bold;color:red;padding:5px;display:inline;' title=' (나중에 일괄로 없어짐, 이문구 안나오는곳은 치환함수로 교체 필요함) '>";
				$mstring .=  "<a href='/admin/store/dic.php?dic_ix=".$mdb->dt[dic_ix]."' style='color:red;' target='_blank'>-</a>";
				$mstring .=  "</div> ";
				}
			$mstring .=  $str;
			return $mstring;
		}else if((is_array($db) || is_object($db))){
			eval("\$str = \"".$desc_trans."\";");
			//echo("\$str = \"".$desc_trans."\";<br><Br>");

			$mstring =  $str;
			if($admininfo[charger_id] == "forbiz" && $trans_link_bool ){
			$mstring .=  "<div style='font-weight:bold;color:red;padding:5px;display:inline;' title=' (나중에 일괄로 없어짐, 이문구 안나오는곳은 치환함수로 교체 필요함) '>";
			$mstring .=  "<a href='/admin/store/dic.php?dic_ix=".$mdb->dt[dic_ix]."' style='color:red;' target='_blank'>-</a>";
			$mstring .=  "</div> ";
			}

			return $mstring;
		}else{
			$desc_trans = str_replace("\"","\\\"",$desc_trans);
			try{
			eval("\$str = \"".$desc_trans."\";");
			//echo("\$str = \"".$desc_trans."\";<br><Br>");
			}catch(Exception $e){
				echo $e;
				exit;
			}

			$mstring =  $str;
			if($admininfo[charger_id] == "forbiz"  && $trans_link_bool ){
				$mstring .=  "<div style='font-weight:bold;color:red;padding:5px;display:inline;' title=' (나중에 일괄로 없어짐, 이문구 안나오는곳은 치환함수로 교체 필요함) '>";
				$mstring .=  "<a href='/admin/store/dic.php?dic_ix=".$mdb->dt[dic_ix]."' style='color:red;' target='_blank'>-</a>";
				$mstring .=  "</div> ";
			}
			return $mstring;
			/*
			return "<div style='font-weight:bold;color:red;padding:5px;display:inline;' title=' (나중에 일괄로 없어짐, 이문구 안나오는곳은 치환함수로 교체 필요함) '><!--a href='/admin/store/dic.php?dic_ix=".$mdb->dt[dic_ix]."' style='color:red;' target='_blank'>-</a--></div> ".$mdb->dt[desc_trans];
			*/
		}
	}else{
		$sql = "select * from admin_dic where menu_code = '".md5($_SERVER["PHP_SELF"])."' and language_type = 'korea' and dic_type = 'DESC' and dic_code = '$dic_code' ";
	//echo $sql;
		$mdb = new Database;
		$mdb->query($sql);
		$mdb->fetch();

		if($mdb->total){
			if($db){
				eval("\$str = \"".$mdb->dt[desc_trans]."\";");

				$mstring =  $str;
				if($admininfo[charger_id] == "forbiz"  && $trans_link_bool ){
					$mstring .=  "<div style='font-weight:bold;color:red;padding:5px;display:inline;' title=' (나중에 일괄로 없어짐, 이문구 안나오는곳은 치환함수로 교체 필요함) '>";
					$mstring .=  "<a href='/admin/store/dic.php?dic_ix=".$mdb->dt[dic_ix]."' style='color:red;' target='_blank'>-</a>";
					$mstring .=  "</div> ";
				}
				return $mstring;
			}else{

				$mstring =  $mdb->dt[desc_trans];
				if($admininfo[charger_id] == "forbiz"  && $trans_link_bool ){
					$mstring .=  "<div style='font-weight:bold;color:red;padding:5px;display:inline;' title=' (나중에 일괄로 없어짐, 이문구 안나오는곳은 치환함수로 교체 필요함) '>";
					$mstring .=  "<a href='/admin/store/dic.php?dic_ix=".$mdb->dt[dic_ix]."' style='color:red;' target='_blank'>-</a>";
					$mstring .=  "</div> ";
				}
				return $mstring;
				/*
				return "<div style='font-weight:bold;color:red;padding:5px;display:inline;' title=' (나중에 일괄로 없어짐, 이문구 안나오는곳은 치환함수로 교체 필요함) '><a href='/admin/store/dic.php?dic_ix=".$mdb->dt[dic_ix]."' style='color:red;' target='_blank'>-</a>(영문없음)</div> ".$mdb->dt[desc_trans];
				*/
			}
		}
	}
}
?>
