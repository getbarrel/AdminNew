<?
include("../class/layout.class");

$db = new Database;
$cdb = new Database;
$goods_setup_info = getBasicSellerSetup($admininfo[company_id]."_goods_multi_price_setup");//품목다중가격 기본설정

	if($gp_ix){
		$sql = "SELECT
					distinct gp_ix ,gi.*
				FROM
					".TBL_SHOP_GROUPINFO." gi 
				where
					gp_ix = '".$gp_ix."'
					order by gi.gp_level asc";

		$db->query($sql);
		$db->fetch();
		$act = "update";
	}else{
		$act = "insert";  // ?? w왜 act로 되어있는지 모름
		//$act = "act";
	}

	$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr>
		<td align='left' colspan=2 style='padding-bottom:0px;'> ".GetTitleNavigation("회원그룹관리", "회원관리 > 회원그룹관리 ")."</td>
	</tr>
	<tr>
		<td align='left' colspan=2 style='padding:3px 0px;'>
			".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>그룹수정하기</b></div>")."
		</td>
	</tr>
	</table>

	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='search_table_box'>
	<col width='18%'>
	<col width='32%'>
	<col width='18%'>
	<col width='32%'>";
	if($_SESSION["admin_config"][front_multiview] == "Y"){
	$Contents01 .= "
	<tr height=28>
		<td class='search_box_title' > 기본사이트</td>
		<td class='search_box_item' colspan=3>".GetDisplayDivision($db->dt['mall_ix'], "select")." <span class=small>전시 구분을 선택하실경우 해당 사이트에 노출됩니다.</span></td>
	</tr>";
	}
	$Contents01 .= "
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_title'> <b>회원그룹명 <img src='".$required3_path."'></b> </td>
		<td class='search_box_item' ><input type=text class='textbox point_color' name='gp_name' value='".$db->dt[gp_name]."' style='width:140px;' validation='true' title='회원그룹명'> </td>
		<td class='search_box_title'> <b>그룹등급 <img src='".$required3_path."'></b> </td>
		<td class='search_box_item'><input type=text class='textbox point_color numeric' name='gp_level' value='".$db->dt[gp_level]."' style='width:100px;' validation='true' title='그룹등급'> </td>
	</tr>
	<tr bgcolor=#ffffff height='30'>
	    <td class='search_box_title'> <b>회원타입 <img src='".$required3_path."'></b> </td>
		<td class='search_box_item'>
			<!--<input type=radio name='selling_type' id='mem_type_A' value='A' title='전체회원' ".($db->dt[selling_type] == "A" || $db->dt[selling_type] == "" ? "checked":"")."><label for='mem_type_A'>전체회원</lable>-->
			<input type=radio name='selling_type' id='selling_type_R' value='R' title='소매회원' ".($db->dt[selling_type] == "R" ? "checked":"")." checked><label for='selling_type_R'>소매</lable>
			&nbsp;
			<!--<input type=radio name='selling_type' id='selling_type_W' value='W' title='도매회원' ".($db->dt[selling_type] == "W" ? "checked":"")."><label for='selling_type_W'>도매</lable>-->
		</td>
		<td class='search_box_title'> <b>가격 노출 타입<img src='".$required3_path."'> </b> </td>
		<td class='search_box_item'>
			<input type='radio' name='dc_standard_price' id='dc_standard_price_l' value='l' ".($db->dt[dc_standard_price] == 'l' ?'checked':'')."><label for='dc_standard_price_l'> 판매가 </label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			<input type='radio' name='dc_standard_price' id='dc_standard_price_s' value='s' ".($db->dt[dc_standard_price] == 's' || $db->dt[dc_standard_price] == ''?'checked':'')."><label for='dc_standard_price_s'> 할인가 </label> 
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			<!--label for='dc_standard_price_p'><input type='radio' name='dc_standard_price' id='dc_standard_price_p' value='p' ".($db->dt[dc_standard_price] == 'p' ?'checked':'')."> 프리미엄가 </label-->
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_title'> <b>사용유무 <img src='".$required3_path."'></b></td>
		<td class='search_box_item' >
			<input type=radio name='disp' id='disp_1' value='1' ".($db->dt[disp] == "1" || $db->dt[disp] == "" ? "checked":"")."><label for='disp_1'>사용</label>
			&nbsp;
			<input type=radio name='disp' id='disp_0' value='0' ".($db->dt[disp] == "0" ? "checked":"")."><label for='disp_0'>미사용</label>
		</td>
		<td class='search_box_title'> <b>레벨관리 사용유무 <img src='".$required3_path."'></b></td>
		<td class='search_box_item' >
			<input type=radio name='all_disp' id='all_disp_1' value='1' ".($db->dt[all_disp] == "1" || $db->dt[all_disp] == "" ? "checked":"")."><label for='all_disp_1'>사용</label>
			&nbsp;
			<input type=radio name='all_disp' id='all_disp_0' value='0' ".($db->dt[all_disp] == "0" ? "checked":"")."><label for='all_disp_0'>미사용</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_title'> <b>쿠폰사용 가능여부 <img src='".$required3_path."'> </b> </td>
		<td class='search_box_item'>
			<input type='radio' name='use_coupon_yn' id='use_coupon_y' value='Y' ".($db->dt[use_coupon_yn] == 'Y' || $db->dt[use_coupon_yn] == '' ?'checked':'')."><label for='use_coupon_y'> 사용 </label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			<input type='radio' name='use_coupon_yn' id='use_coupon_n' value='N' ".($db->dt[use_coupon_yn] == 'N' ?'checked':'')."><label for='use_coupon_n'> 사용안함 </label> 
		</td>
		<td class='search_box_title'> <b>".constant('MILEAGE_NAME')." 사용/적립 가능여부 <img src='".$required3_path."'> </b> </td>
		<td class='search_box_item'>
			<input type='radio' name='use_reserve_yn' id='use_reserve_y' value='Y' ".($db->dt[use_reserve_yn] == 'Y' || $db->dt[use_reserve_yn] == '' ?'checked':'')."><label for='use_reserve_y'> 사용 </label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			<input type='radio' name='use_reserve_yn' id='use_reserve_n' value='N' ".($db->dt[use_reserve_yn] == 'N' ?'checked':'')."><label for='use_reserve_n'> 사용안함 </label>
		</td>
	</tr>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_title'> <b>회원그룹할인율  <img src='".$required3_path."'</b> </td>
		<td class='search_box_item'>
			<table width='100%' border='0'>
			<col width='4%'>
			<col width='12%'>
			<col width='4%'>
			<col width='12%'>
			<col width='*'>
			<tr>
				<td>
					<input type='radio' name='use_discount_type' id='use_discount_type' value='' ".($db->dt[use_discount_type] == ''?'checked':'')." onclick=\"$('#discount_area').hide();\"> 
				</td>
				<td>
					<label for='use_discount_type'>미사용</label>
				</td>
				<td>
					<input type='radio' name='use_discount_type' id='use_discount_type_g' value='g' ".($db->dt[use_discount_type] == 'g' ?'checked':'')." onclick=\"$('#discount_area').show();\"> 
				</td>
				<td>
					<label for='use_discount_type_g'>사용</label> 
				</td>
				<td colspan='2'>
				    <div id='discount_area' ".($db->dt[use_discount_type] == 'g' ? '':"style='display:none;'").">
				           <input type=text class='textbox point_color' name='retail_dc' value='".($db->dt[retail_dc]?$db->dt[retail_dc]:'0')."' style='width:50px;' validation='true' title='할인율'> %
				            &nbsp;&nbsp;&nbsp;&nbsp;
                        할인금액
					</div>
				</td>
			</tr>
			<!--tr>
				<td>
					<input type='radio' name='use_discount_type' id='use_discount_type_c' value='c' ".($db->dt[use_discount_type] == 'c'?'checked':'')."> 
				</td>
				<td>
					<label for='use_discount_type_c'>카테고리 할인율 적용</label> : 
					<span class='blu'> 카테고리 할인율 적용시에는 상품 분류관리 > 카테고리별 할인율 에서 설정하셔야 합니다. </span>
					&nbsp;&nbsp;&nbsp;
					<a href='../product/category_discount_info.php'> 설정 이동 </a>
				</td>
			</tr-->
			<!--
			<tr>
				<td>
					<input type='radio' name='use_discount_type' id='use_discount_type_w' value='w' ".($db->dt[use_discount_type] == 'w'?'checked':'')."> 
				</td>
				<td>
					<label for='use_discount_type_w'>품목별 가격적용(WMS 사용 상품만 가능)</label> : 

					도매	<select name='whole_wms_discount_type' style='width:100px;'>
							<option value='a' ".($db->dt[whole_wms_discount_type] == 'a'?'selected':'').">".$goods_setup_info[batch][rate][W][a_name]."</option>
							<option value='b' ".($db->dt[whole_wms_discount_type] == 'b'?'selected':'').">".$goods_setup_info[batch][rate][W][b_name]."</option>
							<option value='c' ".($db->dt[whole_wms_discount_type] == 'c'?'selected':'').">".$goods_setup_info[batch][rate][W][c_name]."</option>
							<option value='d' ".($db->dt[whole_wms_discount_type] == 'd'?'selected':'').">".$goods_setup_info[batch][rate][W][d_name]."</option>
							<option value='e' ".($db->dt[whole_wms_discount_type] == 'e'?'selected':'').">".$goods_setup_info[batch][rate][W][e_name]."</option>
							</select>
					&nbsp;&nbsp;&nbsp;
					소매	<select name='retail_wms_discount_type' style='width:100px;'>
							<option value='a' ".($db->dt[retail_wms_discount_type] == 'a'?'selected':'').">".$goods_setup_info[batch][rate][R][a_name]."</option>
							<option value='b' ".($db->dt[retail_wms_discount_type] == 'b'?'selected':'').">".$goods_setup_info[batch][rate][R][b_name]."</option>
							<option value='c' ".($db->dt[retail_wms_discount_type] == 'c'?'selected':'').">".$goods_setup_info[batch][rate][R][c_name]."</option>
							<option value='d' ".($db->dt[retail_wms_discount_type] == 'd'?'selected':'').">".$goods_setup_info[batch][rate][R][d_name]."</option>
							<option value='e' ".($db->dt[retail_wms_discount_type] == 'e'?'selected':'').">".$goods_setup_info[batch][rate][R][e_name]."</option>
							</select>
				</td>
			</tr>-->
			</table>
		</td>
		<td class='search_box_title'> <b>무료배송  <img src='".$required3_path."'</b> </td>
		<td class='search_box_item'>
            <input type='radio' name='shipping_free_yn' id='shipping_free_yn_y' value='Y' ".($db->dt[shipping_free_yn] == 'Y' ?'checked':'')."> <label for='shipping_free_yn_y'>적용</label> &nbsp;&nbsp;&nbsp;&nbsp; 
			<input type='radio' name='shipping_free_yn' id='shipping_free_yn_n' value='N' ".($db->dt[shipping_free_yn] == 'N' || $db->dt[shipping_free_yn] == ''?'checked':'')."><label for='shipping_free_yn_n'> 적용안함 </label> 
        </td>
	</tr>
	<tr ".($db->dt[use_discount_type] == 'g' ? '':"style='display:none;'")." id='discount_category_area'> 
	    <td class='search_box_title'>카테고리별할인 사용유무</td>
	    <td class='search_box_item' colspan='3'>
	        <input type='radio' name='use_discount_category_yn' id='use_discount_category_yn_n' value='N' ".($db->dt[use_discount_category_yn] == 'N' || $db->dt[use_discount_category_yn] == '' ?'checked':'')."><label for='use_discount_category_yn_n'> 미사용 </label>  
			<input type='radio' name='use_discount_category_yn' id='use_discount_category_yn_y' value='Y' ".($db->dt[use_discount_category_yn] == 'Y' ?'checked':'')."><label for='use_discount_category_yn_y'> 사용함 </label>
        </td>
	</tr>
	<tr ".($db->dt[use_discount_category_yn] == 'Y' ? '':"style='display:none;'")." id='discount_category_mileage_area'> 
	    <td class='search_box_title'>카테고리별 할인상품 <br> 마일리지 적립 안함</td>
	    <td class='search_box_item' colspan='3'>
	        <input type='radio' name='use_discount_category_mileage_yn' id='use_discount_category_mileage_yn_n' value='N' ".($db->dt[use_discount_category_mileage_yn] == 'N' || $db->dt[use_discount_category_mileage_yn] == '' ?'checked':'')."><label for='use_discount_category_mileage_yn_n'> 미사용 </label>  
			<input type='radio' name='use_discount_category_mileage_yn' id='use_discount_category_mileage_yn_y' value='Y' ".($db->dt[use_discount_category_mileage_yn] == 'Y' ?'checked':'')."><label for='use_discount_category_mileage_yn_y'> 사용함 </label>
        </td>
	</tr>
    <tr ".($db->dt[use_discount_category_yn] == 'Y' ? '':"style='display:none;'")." id='discount_category_item_area'>
        <td class='input_box_title'>
            <div style='float:left;padding-top:5px;'><b>할인카테고리선택</b></div>
            <div style='float:left;padding-left:20px;'>
                <img src='../images/icon/search_icon.gif' value='검색' onclick=\"PoPWindow('../product/search_category.php?group_code=',800,600,'add_brand_category')\" style='cursor:pointer;'>
            </div>
        </td>
        <td class='input_box_item' colspan=3 >
            <div id='selected_category_6' style='padding:10px;overflow-y:scroll;max-height:100px;'>
            <table width='98%' cellpadding='0' cellspacing='0' id='objMd'>
            <colgroup>
                <col width='*'>
                <col width='600'>
            </colgroup>
            <tbody>";

	if($gp_ix){
	    $sql = "select * from shop_group_discount_category where gp_ix = '".$gp_ix."'";
	    $db->query($sql);
	    $discount_category = $db->fetchall();
	    $cid = array();
	    if(is_array($discount_category)){
	        foreach($discount_category as $key=>$val){
                $cid[] = $val['cid'];
            }
        }
    }

    if(count($cid) > 0){

    for($k=0;$k<count($cid);$k++){

    $re_cid = $cid[$k];
    $sql = "select * from shop_category_info where cid = '".$re_cid."'";
    $slave_db->query($sql);
    $slave_db->fetch();
    $depth = $slave_db->dt[depth];

    for($i=0;$i<=$depth;$i++){
        $this_cid = substr(substr($re_cid, 0,($i*3+3)).'000000000000',0,15);
        $sql = "select * from shop_category_info where cid = '".$this_cid."'";
        $slave_db->query($sql);
        $slave_db->fetch();
        $cname = $slave_db->dt[cname];
        $relation_cname[$k] .= $cname;
        if($i < $depth){
            $relation_cname[$k] .= " > ";
        }
    }

        $Contents01 .= "	<tr style='height:26px;' id='row_".$re_cid."'>
                            <td>
                            <input type='hidden' name='cid[]' id='cid_".$re_cid."' value='".$re_cid."'>".$relation_cname[$k]."</td><td><a href='javascript:void(0)' onclick=\"cid_del('".$re_cid."')\"><img src='../images/korean/btc_del.gif' border='0'></a>
                            </td>
                        </tr>";
    }
    }
$Contents01 .= "
                </tbody>
                </table>
            </div>
        </td>
    </tr>
	<!--
	<tr bgcolor=#ffffff height='30'>
		<td class='search_box_title'> <b>배송할인율 <img src='".$required3_path."'></b> </td>
		<td class='search_box_item' colspan='3'>
		<input type=radio name='shipping_dc_yn' id='shipping_dc_n' value='0' title='그룹등급' ".($db->dt[shipping_dc_yn] == "0" || $db->dt[shipping_dc_yn] == "" ? "checked":"")."> 미사용
		<input type=radio name='shipping_dc_yn' id='shipping_dc_y' value='1' title='그룹등급' ".($db->dt[shipping_dc_yn] == "1" ? "checked":"")."> 사용
		<input type='text' class='textbox point_color' name='shipping_dc_price' value ='".$db->dt[shipping_dc_price]."' title='할인금액'> 원 할인적용 
		&nbsp;&nbsp;&nbsp;<input type=radio name='shipping_dc_yn' id= 'shipping_dc_f' value='2' title='무료배송' ".($db->dt[shipping_dc_yn] == "2" ? "checked":"")."> 무료배송
	</tr>-->
	<!-- 이후 사용할지 몰라 우선 주석 처리
	<tr bgcolor=#ffffff height='30'>
		<td class='search_box_title'> <b>APP할인율 <img src='".$required3_path."'></b> </td>
		<td class='search_box_item' colspan='3'>
		<input type=radio name='app_dc_yn' id='app_dc_yn_n' value='0' title='APP할인율' ".($db->dt[app_dc_yn] == "0" || $db->dt[app_dc_yn] == "" ? "checked":"")."> 미사용
		<input type=radio name='app_dc_yn' id='app_dc_yn_y' value='1' title='APP할인율' ".($db->dt[app_dc_yn] == "1" ? "checked":"")."> 사용
		<input type='text' class='textbox point_color' name='app_dc_rate' value ='".$db->dt[app_dc_rate]."' title='APP할인율' size='2'> % 할인적용
	</tr>
	-->
	<tr bgcolor=#ffffff height='30'>
		<td class='search_box_title'> 그룹 이미지 </td>
		<td class='search_box_item' colspan=3>
		<input type=file class='textbox' name='organization_img' title='그룹이미지'> <span class=small>그룹을 표현하는 이미지를 등록하실 수 있습니다.</span>";
if ($db->dt[organization_img] != '' && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/member_group/".$db->dt[organization_img])){
    $Contents01 .= "<table width='100%' border='0' style='margin-top:3px;'>
           <col width='50px'>
			<col width='*'>
			<tr>
				<td>
				    <img src='".$admin_config[mall_data_root]."/images/member_group/".$db->dt[organization_img]."' width=40>
				</td>
				<td><input type='button' value='이미지 삭제' onclick=\"if(confirm('이미지를 정말로 삭제하시겠습니까?')){window.frames['act'].location.href='./group.act.php?act=img_delete&gp_ix=".$gp_ix."'}\" /></td>
			</tr>
			</table>";
}
            $Contents01 .= "
		</td>
	</tr>
	</table>";

$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents02 .= "
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box' style='margin-top:3px;'>
	<tr bgcolor=#efefef style='font-weight:bold' align='center' height=30>
		<td class='s_td' width='3%' height=25>번호</td>
		".($_SESSION["admin_config"][front_multiview] == "Y" ? "<td class='m_td' width='4%'> 기본사이트</td>":"")."
		<td class='m_td' width='2%'>그룹등급</td>
	    <td class='m_td' width='6%'>그룹명</td>
	    <td class='m_td' width='6%'>그룹이미지</td>
		<td class='m_td' width='3%'>회원타입</td>
	    <td class='m_td' width='3%'>회원수</td>
		<td class='m_td' width='3%'>회원점유율</td>
		<td class='m_td' width='4%'>판매가설정</td>
		<td class='m_td' width='5%'>할인율 적용</td>
		<td class='m_td' width='5%'>기타</td>
		<td class='m_td' width='3%'>무료배송</td>
	    <td class='m_td' width='3%'>사용여부</td>
	    <td class='e_td' width='4%'>관리</td>
	</tr>";


//오라클은 group by 할때 컬럼을 명시해줘야함
//$db->query("SELECT gi.*,COUNT(md.gp_ix) AS cnt FROM ".TBL_SHOP_GROUPINFO." gi LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." md ON gi.gp_ix=md.gp_ix GROUP BY gi.gp_ix order by gi.gp_level asc ");
if($db->dbms_type == "oracle"){
	$db->query("SELECT distinct gi.gp_ix,gi.mall_ix, gi.wholesale_dc,gi.retail_dc,gi.font_color, gi.shipping_dc_price,gi.shipping_dc_yn,gi.shipping_free_yn,gi.gp_name,gi.mem_type, gi.organization_img, gi.sale_rate, gi.gp_level,period,keep_period,order_price,keep_order_cnt,give_coupon, gi.disp, gi.basic, gi.regdate FROM ".TBL_SHOP_GROUPINFO." gi LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." md ON gi.gp_ix=md.gp_ix order by gi.gp_level asc");
}else{
	$sql = "SELECT
				gi.*,
				COUNT(md.code) AS cnt 
			FROM 
				".TBL_SHOP_GROUPINFO." gi 
				LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." md ON gi.gp_ix=md.gp_ix
			where
				1
				GROUP BY gi.gp_ix order by gi.gp_level asc ";

	$db->query($sql);
}

if($db->total){
	for($i=0;$i < $db->total;$i++){

	$db->fetch($i);

	if($cdb->dbms_type == "oracle"){
		$cdb->query("SELECT COUNT(md.code) AS cnt  FROM  ".TBL_COMMON_MEMBER_DETAIL." md where md.gp_ix = '".$db->dt[gp_ix]."'");
		$cdb->fetch(0);
		$cnt = $cdb->dt[cnt];
	}else{
		$cnt = $db->dt[cnt];
	}

	$cdb->query("select count(code) as total_cnt from ".TBL_COMMON_USER." where 1");
	$cdb->fetch();
	$total_cnt = $cdb->dt[total_cnt];

	if($total_cnt > 0){
		$cnt_rate = $cnt / $total_cnt *100;
	}

	if($db->dt[retail_dc]){
		$retail_dc = $db->dt[retail_dc];
	}else{
		$retail_dc = '0';
	}

	switch($db->dt[selling_type]){
		case 'R':
			$type = '소매';
			break;
		case 'W':
			$type = '도매';
			break;
        default:
			$type = '';
			break;
	}

	switch($db->dt[dc_standard_price]){
		case 'l':
			$dc_standard_price = "판매가";
			break;
		case 's':
			$dc_standard_price = "할인가";
			break;
		case 'p':
			$dc_standard_price = "프리미엄가";
			break;
	}

	switch($db->dt[use_discount_type]){
        case '':
            $use_discount_type_text = "미사용";
            break;
		case 'g':
			$use_discount_type_text = '사용('.$retail_dc.'%)';
			break;
		case 'c':
			$use_discount_type_text = '카테고리별 할인';
			break;
		case 'w':
			$use_discount_type_text = '품목별 가격적용<br>'.$goods_setup_info[batch][rate][W][$db->dt[whole_wms_discount_type]."_name"]." / ".$goods_setup_info[batch][rate][R][$db->dt[retail_wms_discount_type]."_name"]."";
			break;
	}

	if($db->dt[use_coupon_yn]=='Y'){
		$use_coupon_yn = '쿠폰 사용 가능';
	}else{
		$use_coupon_yn = '<span class="red">쿠폰 사용 불가능</span>';
	}

	if($db->dt[use_reserve_yn]=='Y'){
		$use_reserve_yn = constant('MILEAGE_NAME').' 사용 가능';
	}else{
		$use_reserve_yn = '<span class="red">'.constant('MILEAGE_NAME').' 사용 불가능</span>';
	}

    if($db->dt[shipping_free_yn]=='Y'){
        $shipping_free_yn = '적용';
    }else{
        $shipping_free_yn = '적용안함';
    }


	$Contents02 .= "
		<tr bgcolor=#ffffff align='center' height=30>
			<td class='list_box_td'>".($i+1)."</td>";
	if($_SESSION["admin_config"]["front_multiview"] == "Y"){
	$Contents02 .= "
			<td class='list_box_td list_bg_gray'>".GetDisplayDivision($db->dt[mall_ix], "text")." </td>";
	}
	$Contents02 .= "
			<td class='list_box_td'>".$db->dt[gp_level]." </td>
			<td class='list_box_td point'>".$db->dt[gp_name]."</td>
			<td class='list_box_td' align=center style='padding:10px;'>";

			if ($db->dt[organization_img] != '' && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/member_group/".$db->dt[organization_img])){
				$Contents02 .= "<img src='".$admin_config[mall_data_root]."/images/member_group/".$db->dt[organization_img]."' width=40>";
			  }
			$Contents02 .= "
			</td>
			<td class='list_box_td'>".$type." </td>
			<td class='list_box_td'>".$cnt." </td>
			<td class='list_box_td'>".round($cnt_rate,2)."% </td>
			<td class='list_box_td'>".$dc_standard_price."</td>
			<td class='list_box_td'>".$use_discount_type_text."</td>
			<td class='list_box_td'>".$use_coupon_yn."<br/>".$use_reserve_yn."</td>
			<td class='list_box_td'>".$shipping_free_yn."</td>
			<td class='list_box_td'>".($db->dt[disp] == "1" ?  "사용":"미사용")."</td>
			<td class='list_box_td'>";
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){//gp_ix,gp_name,mem_type,font_color,wholesale_dc,retail_dc,shipping_dc_price,organization_name, organization_id,organization_img
					$Contents02.="
					<a href=\"?gp_ix=".$db->dt[gp_ix]."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a>";
				}else{
					 $Contents02.="
					<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a>";
				}
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){

					if($db->dt[basic] == "N" || $db->dt[basic] == ""){
						$Contents02 .= "
						<a href=\"javascript:deleteGroupInfo('delete','".$db->dt[gp_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
					}
				}
				$Contents02 .= "
			</td>
		</tr>";
	}
}else{
	$Contents02 .= "
		<tr bgcolor=#ffffff height=50>
			<td class='list_box_td' align=center colspan=8>등록된 그룹이 없습니다. </td>
		</tr>";
	}
	$Contents02 .= "
	</table>";


if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
$ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >
			<!--img src='../images/".$admininfo["language"]."/b_save.gif' border='0'  style='cursor:pointer;border:0px;' onClick='CheckFormValue(document.group_frm)' /--> 
		</td>
	</tr>
	</table>";
}else{
$ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>
		</td>
	</tr>
	</table>";
}


$Contents = $Contents."<form name='group_frm' action='group.act.php' method='post' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target='act'>
<input name='act' type='hidden' value='".$act."'>
<input name='gp_ix' type='hidden' value='".$gp_ix."'>
<input name='basic' type='hidden' value=''>";
$Contents = $Contents."<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<tr><td width='100%'>";
$Contents = $Contents.$Contents01;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";
$Contents = $Contents."<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= HelpBox("회원그룹관리", $help_text,'100');

/*
<link rel='stylesheet' href='/admin/member/colorpicker/css/colorpicker.css' type='text/css' />
<link rel='stylesheet' media='screen' type='text/css' href='/admin/member/colorpicker/css/layout.css' />
<script type='text/javascript' src='/admin/member/colorpicker/js/jquery.js'></script>
<script type='text/javascript' src='/admin/member/colorpicker/js/colorpicker.js'></script>
<script type='text/javascript' src='/admin/member/colorpicker/js/eye.js'></script>
<script type='text/javascript' src='/admin/member/colorpicker/js/utils.js'></script>
<script type='text/javascript' src='/admin/member/colorpicker/js/layout.js?ver=1.0.2'></script>
 */
 $Script = "
 <script language='javascript'>
function updateGroupInfo(gp_ix,gp_name,gp_level,mem_type,font_color,wholesale_dc,retail_dc,shipping_dc_price,organization_name, organization_id,organization_img,mem_cnt,disp,shipping_dc_yn,shipping_free_yn){

	var frm = document.group_frm;

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
	}else if(mem_type == 'A'){
		$('#mem_type_A').attr('checked','checked');
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
	if(confirm('해당그룹 정보를 정말로 삭제하시겠습니까?')){
		//'해당그룹 정보를 정말로 삭제하시겠습니까?'
		var frm = document.group_frm;
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
    
	$('input:radio[name=use_discount_type]').click(function(){
	   var discount_yn = $(this).val();
	   if(discount_yn == 'g'){
	       $('#discount_category_area').show();
	   }else{
	       $('#discount_category_area').hide();
	   }
	});
	
	$('input:radio[name=use_discount_category_yn]').click(function(){
	    var discount_category_yn = $(this).val();   	   
	    if(discount_category_yn == 'Y'){
           $('#discount_category_item_area').show();
           $('#discount_category_mileage_area').show();
        }else{
           $('#discount_category_item_area').hide();
           $('#discount_category_mileage_area').hide();
        }
	});
});

function cid_del(code){
    $('#row_'+code).remove();
}
</script>
";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = member_menu();
$P->Navigation = "회원관리 > 회원그룹관리";
$P->title = "회원그룹관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>