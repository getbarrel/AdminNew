<?
include("../class/layout.class");

$db = new Database;
$cdb = new Database;

/*
CREATE TABLE IF NOT EXISTS `shop_product_level` (
  `pl_ix` int(10) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(100) NOT NULL COMMENT '상품그룹명',
  `level` int(10) NOT NULL COMMENT '상품그룹등급',
  `group_type` char(1) NOT NULL DEFAULT '1' COMMENT '1 :매출 + 점수 2:매출 3:등급',
  `status` text NOT NULL COMMENT '매출데이타 상태',
  `font_color` varchar(10) NOT NULL DEFAULT '#000000' COMMENT '등급별 색상',
  `is_use_yn` char(1) NOT NULL DEFAULT '1' COMMENT '1:사용 0:미사용',
  `is_auto_yn` char(1) NOT NULL DEFAULT '0' COMMENT '자동설정 사용유무 1:사용 0:미사용',
  `setup_date` date NOT NULL COMMENT '상품자동 설정일',
  `period` int(10) NOT NULL DEFAULT '0' COMMENT '산정기간',
  `keep_period` int(10) NOT NULL DEFAULT '0' COMMENT '유지기간',
  `st_price` int(10) NOT NULL DEFAULT '0' COMMENT '시작매출액',
  `ed_price` int(10) NOT NULL DEFAULT '0' COMMENT '마감매출액',
  `st_point` int(10) NOT NULL DEFAULT '0' COMMENT '시작점수',
  `ed_point` int(10) NOT NULL DEFAULT '0' COMMENT '마감점수',
  `edit_date` datetime NOT NULL COMMENT '수정일자',
  PRIMARY KEY (`pl_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='상품 그룹관리'
*/


if($info_type == ""){
	$info_type = 'basic';
}
if($info_type == 'basic'){

	if($pl_ix){
		$sql = "select * from `shop_product_level` where pl_ix = '".$pl_ix."'";
		$db->query($sql);
		$db->fetch();
	}

	$helpbox_title = "상품등급 관리";

}else if($info_type == 'setup'){
	$sql = "select * from `shop_product_level` where 1 limit 0,1";
	$db->query($sql);
	$db->fetch();
	$status_info = unserialize($db->dt[status]);

	$helpbox_title = "상품등급 설정";
}else if($info_type == 'penalty'){

	$product_level = getSharedInfo('product_level_setting');
	$helpbox_title = "상품 포인트 설정";
}

$Contents = "
	<table width='100%' border='0' cellpadding=0 cellspacing=0 align='center'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("전체사원리스트", "기초정보관리 > 본사관리 ")."</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:20px;'> 
			<div class='tab'>
			<table class='s_org_tab'>
				<col width='555'>
				<col width='*'>
				<tr>
					<td class='tab'>
						<table id='tab_01' ".(($info_type == "basic" || $info_type == "") ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02'  ><a href='?info_type=basic&code=".$code."&mmode=$mmode'>상품등급관리</a></td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_02' ".($info_type == "setup" ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' ><a href='?info_type=setup&code=".$code."&mmode=$mmode'>상품등급 설정</a>
							</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_03' ".($info_type == "penalty" ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' ><a href='?info_type=penalty&code=".$code."&mmode=$mmode'>상품 포인트 설정</a>
							</td>
							<th class='box_03'></th>
						</tr>
						</table>
					</td>
					<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'></td>
				</tr>
			</table>
			</div>
		</td>
	</tr>
	</table>";

if($info_type == "" || $info_type == "basic"){

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>상품등급 수정하기</b>
			</td>
		</tr>
	</table>

	<form name='product_level_frm' action='product_level_info.act.php' method='post' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target=''>
	<input name='act' type='hidden' value='product_level_update'>
	<input name='pl_ix' type='hidden' value='".$pl_ix."' validation='true'>
	<input name='info_type' type='hidden' value='basic'>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='search_table_box'>
	<col width='18%'>
	<col width='32%'>
	<col width='18%'>
	<col width='32%'>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_title'> <b>상품등급명 <img src='".$required3_path."'></b> </td>
		<td class='search_box_item' >
			<input type=text class='textbox point_color' name='group_name' value='".$db->dt[group_name]."' style='width:140px;' validation='true' title='상품등급명'>
		</td>
		<td class='search_box_title'> <b>사용유무 <img src='".$required3_path."'></b></td>
		<td class='search_box_item'>
			<input type=radio name='is_use_yn' id='is_use_yn_1' value='1' checked><label for='is_use_yn_1'>사용</label>
			<input type=radio name='is_use_yn' id='is_use_yn_0' value='0' ><label for='is_use_yn_0'>미사용</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff height='30'>
		<td class='search_box_title'> <b>상품등급 <img src='".$required3_path."'></b> </td>
		<td class='search_box_item' colspan='3'>
			<input type=text class='textbox point_color' name='level' value='".$db->dt[level]."' style='width:40px;' validation='true' title='상품등급'>
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' style='display:none;' >
		<td class='search_box_title'> <b>폰트색상 </b> </td>
		<td class='search_box_item' colspan='3'>
			<input type='text' maxlength='6' class='textbox point_color'  id='colorpickerField1' name='font_color' value='".$db->dt[font_color]."' style='width:50px;' title='폰트색상'>
			<span class='small blu'> * 입력필드를 클릭하면 색상표가 나오며, 색상표안에 마우스를 드레그하여 원하는 색상을 선택할수 있습니다.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height='30'>
		<td class='search_box_title'> 그룹 이미지 </td>
		<td class='search_box_item' colspan=3>
			<input type=file class='textbox' name='sellergruop_img' title='그룹이미지'> 
				<span class=small> 등급 표현하는 이미지를 등록하실수 있습니다.</span>
			<div id='organization_img_area' ></div>
		</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'  height='30'>
	<tr bgcolor=#ffffff >
		<td></td>
	</tr>
	</table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >
		</td>
	</tr>
	</table>";
}else{
$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>
		</td>
	</tr>
	</table>";
}

$Contents .= "</form>";

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'  height='30'>
	<tr bgcolor=#ffffff >
		<td></td>
	</tr>
	</table>
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box' style='margin-top:3px;'>
	<col width='4%'>
	<col width='8%'>
	<col width='*'>
	<col width='20%'>
	<col width='10%'>
	<col width='10%'>
	<col width='10%'>
	<col width='10%'>
	<tr bgcolor=#efefef style='font-weight:bold' align='center' height=30>
		<td class='s_td' >번호</td>
		<td class='m_td' >상품등급</td>
		<td class='m_td' >상품 등급명</td>
		<td class='m_td' >등급이미지</td>
		<td class='m_td' >상품수</td>
		<td class='m_td' >상품점유율</td>
		<td class='m_td' >사용유무</td>
		<td class='e_td'>관리</td>
	</tr>";

if($db->dbms_type == "oracle"){
	$sql = "SELECT
				pl.*,
				(select count(*) as cnt from shop_product where product_level = pl.pl_ix) as total_cnt
			from
				`shop_product_level` as pl
			where
				1
				order by pl_ix ASC";
}else{
	$sql = "SELECT
				pl.* , count(*) as total_cnt
			from
				shop_product_level  pl
				left join shop_product p on p.product_level = pl.pl_ix
			where
				1
				group by pl_ix
				order by pl_ix ASC
				";
}

$db->query($sql);
$data_array = $db->fetchall();

if($db->total){
	for($i=0;$i < count($data_array);$i++){

	switch($data_array[$i][is_use_yn]){
		case '1':
			$is_use_yn = '사용';
		break;
		case '0':
			$is_use_yn = '미사용';
		break;
	}

	$Contents .= "
		<tr bgcolor=#ffffff align='center' height=30>
			<td class='list_box_td'>".($i+1)."</td>
			<td class='list_box_td'>".$data_array[$i][level]." </td>
			<td class='list_box_td point'>".$data_array[$i][group_name]."</td>
			<td class='list_box_td' align=center style='padding:10px;'>";
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/product_level/product_level_".$data_array[$i][pl_ix].".gif")){
				$Contents .= "<img src='".$admin_config[mall_data_root]."/images/basic/product_level/product_level_".$data_array[$i][pl_ix].".gif' width=40>";
			}
	$Contents .= "
			</td>
			<td class='list_box_td'>".$data_array[$i][total_cnt]." </td>
			<td class='list_box_td'>".round($cnt_rate,2)."% </td>
			<td class='list_box_td'>".$is_use_yn."</td>
			<td class='list_box_td'>";
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){//gp_ix,gp_name,mem_type,font_color,wholesale_dc,retail_dc,shipping_dc_price,organization_name, organization_id,organization_img
					$Contents.="
					<a href=\"?pl_ix=".$data_array[$i][pl_ix]."&info_type=".$info_type."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a>";
				}else{
					 $Contents.="
					<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a>";
				}
	$Contents .= "
			</td>
		</tr>";
	}
}else{
	$Contents .= "
		<tr bgcolor=#ffffff height=50>
			<td class='list_box_td' align=center colspan=8>등록된 그룹이 없습니다. </td>
		</tr>";
	}
	$Contents .= "
	</table>";

	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

}else if($info_type == "setup"){	//상품등급 설정

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>상품등급 설정하기</b>
			</td>
		</tr>
	</table>

	<form name='product_level_frm' action='product_level_info.act.php' method='post' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target='act'>
	<input name='act' type='hidden' value='product_level_setup'>
	<input name='pl_ix' type='hidden' value='".$pl_ix."' validation='true'>
	<input name='info_type' type='hidden' value='setup'>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='search_table_box'>
	<col width='18%'>
	<col width='32%'>
	<col width='18%'>
	<col width='32%'>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_title'> <b>자동설정 사용유무 <img src='".$required3_path."'></b></td>
		<td class='search_box_item' colspan='3'>
			<input type=radio name='is_auto_yn' id='is_auto_yn_1' value='1' ".($db->dt[is_auto_yn]=='1' || $db->dt[is_auto_yn]==''?'checked':'')."><label for='is_auto_yn_1'>사용</label>
			<input type=radio name='is_auto_yn' id='is_auto_yn_0' value='0' ".($db->dt[is_auto_yn]=='0'?'checked':'')."><label for='is_auto_yn_0'>미사용</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_title'> <b>매출적용 상태값 <img src='".$required3_path."'></b></td>
		<td class='search_box_item' colspan='3'>
			<table width='100%' border='0'>
			<tr>
				<td>
				<input type='checkbox' name='status[]' id='status_".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' ".(is_array($status_info)?in_array(ORDER_STATUS_INCOM_COMPLETE,$status_info)?"checked":"":"")."> 
				<label for='status_".ORDER_STATUS_INCOM_COMPLETE."'>입금확인</label>&nbsp;
				<input type='checkbox' name='status[]' id='status_".ORDER_STATUS_DELIVERY_READY."' value='".ORDER_STATUS_DELIVERY_READY."' ".(is_array($status_info)?in_array(ORDER_STATUS_DELIVERY_READY,$status_info)?"checked":"":"")."> 
				<label for='status_".ORDER_STATUS_DELIVERY_READY."'>배송준비중</label>
				<input type='checkbox' name='status[]' id='status_".ORDER_STATUS_DELIVERY_DELAY."' value='".ORDER_STATUS_DELIVERY_DELAY."' ".(is_array($status_info)?in_array(ORDER_STATUS_DELIVERY_DELAY,$status_info)?"checked":"":"")."> 
				<label for='status_".ORDER_STATUS_DELIVERY_DELAY."'>배송지연</label>
				<input type='checkbox' name='status[]' id='status_".ORDER_STATUS_DELIVERY_ING."' value='".ORDER_STATUS_DELIVERY_ING."' ".(is_array($status_info)?in_array(ORDER_STATUS_DELIVERY_ING,$status_info)?"checked":"":"").">
				<label for='status_".ORDER_STATUS_DELIVERY_ING."'>배송중</label>
				<input type='checkbox' name='status[]' id='status_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".(is_array($status_info)?in_array(ORDER_STATUS_DELIVERY_COMPLETE,$status_info)?"checked":"":"")."> 
				<label for='status_".ORDER_STATUS_DELIVERY_COMPLETE."'>배송완료</label>
				<input type='checkbox' name='status[]' id='status_".ORDER_STATUS_BUY_FINALIZED."' value='".ORDER_STATUS_BUY_FINALIZED."' ".(is_array($status_info)?in_array(ORDER_STATUS_BUY_FINALIZED,$status_info)?"checked":"":"").">
				<label for='status_".ORDER_STATUS_BUY_FINALIZED."'>거래완료</label>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_title'> <b>상품자동설정일(시작일) <img src='".$required3_path."'></b></td>
		<td class='search_box_item' colspan='3'>
			<input type='text' name='setup_date' id='setup_date' value='".$db->dt[setup_date]."'  class='textbox point_color' style='width:80px;'>
		</td>
	</tr>
	<!--tr bgcolor=#ffffff  height='30' >
		<td class='search_box_title'> <b>산정기간</b></td>
		<td class='search_box_item' colspan='3'>
			상품 자동설정일로부터&nbsp;&nbsp;&nbsp;<input type='text' name='period' id='period' value='".$db->dt[period]."' class='textbox point_color numeric' maxlength='2' style='width:30px;'>&nbsp;&nbsp;개월 산정
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_title'> <b>유지기간</b></td>
		<td class='search_box_item' colspan='3'>
			유지기간&nbsp;&nbsp;&nbsp;<input type='text' name='keep_period' id='keep_period' value='".$db->dt[keep_period]."' class='textbox point_color numeric' maxlength='2' style='width:30px;'>&nbsp;&nbsp;개월
		</td>
	</tr-->
	<tr bgcolor=#ffffff  height='170' >
		<td class='search_box_title'> <b>회원그룹할인율  <img src='".$required3_path."'</b> </td>
		<td class='search_box_item' colspan='3' style='line-height:160%;padding:10px;'>			 
					<input type='radio' name='group_type' id='group_type_1' value='1' ".($db->dt[group_type] == '1' || $db->dt[group_type] == ''?'checked':'')."> <label for='group_type_1'>매출액과 등급점수 모든 적용되어야 자동으로 등급이 변경됩니다.</label><br>				 
					<input type='radio' name='group_type' id='group_type_2' value='2' ".($db->dt[group_type] == '2'?'checked':'')."><label for='group_type_2'>매출액만 적용되어 자동으로 등급이 변경됩니다. </label><br>
					<input type='radio' name='group_type' id='group_type_3' value='3' ".($db->dt[group_type] == '3'?'checked':'')."> <label for='group_type_3'>등급점수만 적용되어 자동으로 등급이 변경됩니다.</label>
		</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'  height='30'>
	<tr bgcolor=#ffffff >
		<td></td>
	</tr>
	</table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >
		</td>
	</tr>
	</table>";
}else{
$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>
		</td>
	</tr>
	</table>";
}

$Contents .= "</form>";

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'  height='30'>
	<tr bgcolor=#ffffff >
		<td></td>
	</tr>
	</table>

	<form name='product_level_frm' action='product_level_info.act.php' method='post' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target=''>
	<input type='hidden' name='act' value='product_level_price'>
	<input type='hidden' name='info_type' value='".$info_type."'>
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box' style='margin-top:3px;'>
	<col width='4%'>
	<col width='8%'>
	<col width='*'>
	<col width='25%'>
	<col width='25%'>
	<col width='10%'>
	<col width='7%'>
	<tr bgcolor=#efefef style='font-weight:bold' align='center' height=30>
		<td class='s_td' >번호</td>
		<td class='m_td' >상품등급</td>
		<td class='m_td' >상품 등급명</td>

		<td class='m_td' >매출액</td>
		<td class='m_td' >등급점수</td>
		<td class='m_td' >사용유무</td>
		<td class='e_td' >관리</td>
	</tr>";

if($db->dbms_type == "oracle"){
	$sql = "SELECT
				pl.*,
				(select count(*) as cnt from shop_product where product_level = pl.pl_ix) as total_cnt
			from
				`shop_product_level` as pl
			where
				1
				order by pl_ix ASC";
}else{
	$sql = "SELECT
				pl.*,
				(select count(*) as cnt from shop_product where product_level = pl.pl_ix) as total_cnt
			from
				`shop_product_level` as pl
			where
				1
				order by pl_ix ASC";
}

$db->query($sql);
$data_array = $db->fetchall();

if($db->total){
	for($i=0;$i < count($data_array);$i++){

	switch($data_array[$i][is_use_yn]){
		case '1':
			$is_use_yn = '사용';
		break;
		case '0':
			$is_use_yn = '미사용';
		break;
	}

	$Contents .= "
		<tr bgcolor=#ffffff align='center' height=30>
			<input type='hidden' name='data[".$data_array[$i][pl_ix]."][pl_ix]' value='".$data_array[$i][pl_ix]."'>
			<td class='list_box_td'>".($i+1)."</td>
			<td class='list_box_td'>".$data_array[$i][level]." </td>
			<td class='list_box_td point'>".$data_array[$i][group_name]."</td>
			<td class='list_box_td' align=center style='padding:10px;'>
				<input type='textbox' class='textbox numeric' name='data[".$data_array[$i][pl_ix]."][st_price]' id='st_price' value='".$data_array[$i][st_price]."' style='width:80px;'> ~
				<input type='textbox' class='textbox numeric' name='data[".$data_array[$i][pl_ix]."][ed_price]' id='ed_price' value='".$data_array[$i][ed_price]."' style='width:80px;'>
			</td>
			<td class='list_box_td' align=center style='padding:10px;'>
				<input type='textbox' class='textbox numeric' name='data[".$data_array[$i][pl_ix]."][st_point]' id='st_point' value='".$data_array[$i][st_point]."' style='width:80px;'> ~
				<input type='textbox' class='textbox numeric' name='data[".$data_array[$i][pl_ix]."][ed_point]' id='ed_point' value='".$data_array[$i][ed_point]."' style='width:80px;'>
			</td>
			<td class='list_box_td'>".$is_use_yn."</td>
			<td class='list_box_td'>";
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents.="
					<a href=\"?pl_ix=".$data_array[$i][pl_ix]."&info_type=".$info_type."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a>";
				}else{
					 $Contents.="
					<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a>";
				}
	$Contents .= "
			</td>
		</tr>";	
	}

}else{
	$Contents .= "
		<tr bgcolor=#ffffff height=50>
			<td class='list_box_td' align=center colspan=8>등록된 그룹이 없습니다. </td>
		</tr>";
}

$Contents .= "
	</table><br><br>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >
		</td>
	</tr>
	</table>";

}else{

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>
		</td>
	</tr>
	</table>";

}
$Contents .= "
	</form>";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

}else if($info_type == 'penalty'){

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>고객 구매패턴에 따른 점수 설정</b>
			</td>
		</tr>
	</table>

	<form name='product_level_frm' action='product_level_info.act.php' method='post' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target=''>
	<input name='act' type='hidden' value='product_level_setting'>
	<input name='pl_ix' type='hidden' value='".$pl_ix."' validation='true'>
	<input name='info_type' type='hidden' value='penalty'>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'  height='30'>
	<col width=65%>
	<col width=35%>
	<tr bgcolor=#ffffff >
	<td>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='search_table_box'>
	<col width='18%'>
	<col width='60%'>
	<col width='*'>

	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_title' rowspan='6'> <b>판매</b> </td>
		<td class='search_box_item'>
			입금완료 (+)
		</td>
		<td class='search_box_item ctr'>
			".product_level_point_select('level_ic_point','level_ic_point',$product_level[level_ic_point])." 점
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_item'>
			배송완료 (+)
		</td>
		<td class='search_box_item ctr'>
			".product_level_point_select('level_dc_point','level_dc_point',$product_level[level_dc_point])." 점
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_item'>
			구매확정 (+)
		</td>
		<td class='search_box_item ctr'>
			".product_level_point_select('level_bf_point','level_bf_point',$product_level[level_bf_point])." 점
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_item'>
			입금후 취소 (판매자귀책)/(-)
		</td>
		<td class='search_box_item ctr'>
			".product_level_point_select('level_cc_point','level_cc_point',$product_level[level_cc_point])." 점
		</td>
	</tr>
	
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_item'>
			교환확정 (판매자귀책)/(-)
		</td>
		<td class='search_box_item ctr'>
			".product_level_point_select('level_ec_point','level_ec_point',$product_level[level_ec_point])." 점
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_item'>
			반품확정 (판매자귀책)/(-)
		</td>
		<td class='search_box_item ctr'>
			".product_level_point_select('level_rc_point','level_rc_point',$product_level[level_rc_point])." 점
		</td>
	</tr>

	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_title' rowspan='2'> <b>발송지연</b> </td>
		<td class='search_box_item'>
			입금완료후 ".product_level_point_select('delivery_delay_date','delivery_delay_date',$product_level[delivery_delay_date])." 일 이내 미발송 (-)
		</td>
		<td class='search_box_item ctr'>
			".product_level_point_select('delivery_delay_point','delivery_delay_point',$product_level[delivery_delay_point])." 점
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_item'>
			입금완료후 ".product_level_point_select('delivery_delay_add_date','delivery_delay_add_date',$product_level[delivery_delay_add_date])." 일 이내 미발송 (추가 -)
		</td>
		<td class='search_box_item ctr'>
			".product_level_point_select('delivery_delay_add_point','delivery_delay_add_point',$product_level[delivery_delay_add_point])." 점
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_title' rowspan='3'> <b>기타</b> </td>
		<td class='search_box_item'>
			1번 클릭(+) 
			<select name='click_rule'>
				<option value='1' ".($product_level[click_rule] == 1 ? "selected":"").">1회</option>
				<option value='2' ".($product_level[click_rule] == 2 ? "selected":"").">1일</option>
				<option value='3' ".($product_level[click_rule] == 3 ? "selected":"").">일정무관</option> 
			</select>
		</td>
		<td class='search_box_item ctr'>
			".product_level_point_select('click_point','click_point',$product_level[click_point])." 점
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_item'>
			찜하기(+) 
			<select name='wish_rule'>
				<option value='1' ".($product_level[wish_rule] == 1 ? "selected":"").">1회</option>
				<option value='2' ".($product_level[wish_rule] == 2 ? "selected":"").">1일</option>
				<option value='3' ".($product_level[wish_rule] == 3 ? "selected":"").">일정무관</option> 
			</select>
		</td>
		<td class='search_box_item ctr'>
			".product_level_point_select('wish_point','wish_point',$product_level[wish_point])." 점
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_item'>
			장바구니(+) 
			<select name='cart_rule'>
				<option value='1' ".($product_level[cart_rule] == 1 ? "selected":"").">1회</option>
				<option value='2' ".($product_level[cart_rule] == 2 ? "selected":"").">1일</option>
				<option value='3' ".($product_level[cart_rule] == 3 ? "selected":"").">일정무관</option> 
			</select>
		</td>
		<td class='search_box_item ctr'>
			".product_level_point_select('cart_point','cart_point',$product_level[cart_point])." 점
		</td>
	</tr>
<!--
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_title' rowspan='2'> <b>상품/주문허위 입력<br>(MD수동등록)</b> </td>
		<td class='search_box_item'>
			입금완료후 ".product_level_point_select($select_name,$select_id)." 일 이내 미발송 (-)
		</td>
		<td class='search_box_item'>
			".product_level_point_select($select_name,$select_id)."
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_item'>
			입금완료후 ".product_level_point_select($select_name,$select_id)." 일 이내 미발송 (추가 -)
		</td>
		<td class='search_box_item'>
			".product_level_point_select($select_name,$select_id)."
		</td>
	</tr>
-->
	</table>
		</td>
		<td style='padding-left:10px;vertical-align:top;'>";
		
$help_text = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td >
			<b>등급 점수설정이란?</b><br>
			판매자의 매출액이아닌 상품별 판매패턴을 분석하여 상품의 등급을 자동으로 관리할수 있습니다. <br>
			등급점수는 관리자가 설정하도록 하여 상품관리를 더욱 효율적으로 할수 있습니다.<br>
			또한 해당 점수는 모두 판매자 귀책일 경우에만 적용되며, 고객의 귀책으로 이루어진 점수는 포함되지 않습니다. <br>
			예 : 구매확정 3점<br>
				판매취소 -1점<br>
				판매자 주문취소 -2점<br>
				반품취소 -5점<br>
			등등으로 관리하여 확정시 점수가 부여됩니다.
		</td>
	</tr>
	</table>";


$Contents .= HelpBox($helpbox_title, $help_text,'100');

$Contents .= "
		</td>
	</tr>
	</table>";

	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){

	$Contents .= "
		<table width='100%' cellpadding=0 cellspacing=0 border='0' >
		<tr bgcolor=#ffffff >
			<td colspan=4 style='padding-top:20px;' align=center>
				<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >
			</td>
		</tr>
		</table>";

	}else{

	$Contents .= "
		<table width='100%' cellpadding=0 cellspacing=0 border='0' >
		<tr bgcolor=#ffffff >
			<td colspan=4 align=center>
				<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>
			</td>
		</tr>
		</table>";
	}
}
$Contents .= "</form>";


$Script = "
<script language='javascript'>

function updateGroupInfo(gp_ix,gp_name,gp_level,mem_type,font_color,wholesale_dc,retail_dc,shipping_dc_price,organization_name, organization_id,organization_img,mem_cnt,disp,shipping_dc_yn,shipping_free_yn){

	var frm = document.product_level_frm;

	frm.act.value = 'update';
	frm.gp_ix.value = gp_ix;
	frm.gp_name.value = gp_name;
	frm.gp_level.value = gp_level;
	frm.mem_type.value = mem_type;
	frm.font_color.value = font_color;
	frm.wholesale_dc.value = wholesale_dc;
	frm.retail_dc.value = retail_dc;
	frm.shipping_dc_price.value = shipping_dc_price;
	
	if(mem_type == 'M'){
		$('#mem_type_M').attr('checked','checked');
	}else if(mem_type == 'C'){
		$('#mem_type_C').attr('checked','checked');
	}

	if(shipping_dc_yn == '1'){
		$('#shipping_dc_y').attr('checked','checked');
	}else if(shipping_dc_yn == '0'){
		$('#shipping_dc_n').attr('checked','checked');
	}else if(shipping_dc_yn == '2'){
		$('#shipping_dc_f').attr('checked','checked');
	}

	if(shipping_free_yn == '1'){
		$('#shipping_free_yn').attr('checked','checked');
	}

	if(mem_cnt > 0){
		frm.disp[0].disabled = true;
		frm.disp[1].disabled = true;

		if(disp == '1'){
			frm.disp[0].checked = true;
		}else{
			frm.disp[1].checked = true;
		}
	}else{
		frm.disp[0].disabled = false;
		frm.disp[1].disabled = false;

		if(disp == '1'){
			frm.disp[0].checked = true;
		}else{
			frm.disp[1].checked = true;
		}
	}
}

function deleteGroupInfo(act, gp_ix){
	if(confirm(language_data['group.php']['A'][language])){
		//'해당그룹 정보를 정말로 삭제하시겠습니까?'
		var frm = document.product_level_frm;
		frm.act.value = act;
		frm.gp_ix.value = gp_ix;
		frm.submit();
	}
}

$(document).ready(function(){

	$('#shipping_free_yn').click(function(){
		$('#shipping_free_yn').toggle(function(){
			$('#shipping_free_yn').attr('checked','true');
		});
	});

	$('#setup_date').datepicker({
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: 'Kalender',
	});
});
</script>
";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = product_menu();
$P->Navigation = "상품관리 > 상품등급관리";
$P->title = "상품등급설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();


function product_level_point_select($select_name,$select_id,$value=''){
	
$data = "
	<select name='".$select_name."' id='".$select_id."' style='width:50px;'>";
	for($i=0;$i<=10;$i++){
		$data .= "<option value='".$i."' ".($value == $i?"selected":"").">".$i."</option>";
	}
$data .= "
	</select>";

return $data;

}
?>