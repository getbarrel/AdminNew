<?
include("../class/layout.class");

$Script = "
<script language='JavaScript' src='member.js'></Script>
<style>
.width_class {width:150px;}
input {border:1px solid #c6c6c6;padding:3px;}
.member_table td {text-align:left;}
</style>";

$db = new Database;
$mdb = new Database;
$cdb = new Database;
$rproduct_db = new Database;

if(empty($page_type)){
	$sql = "select cp.*,c.* from ".TBL_SHOP_CUPON."  c , ".TBL_SHOP_CUPON_PUBLISH." cp
					where  c.cupon_ix = cp.cupon_ix and c.cupon_ix ='$cupon_ix' ";

	$db->query($sql);
	$db->fetch();

	switch($db->dt[cupon_use_div]){
		case 'A' : $use_div_str = 'PC + Mobile';
			break;
		case 'G' : $use_div_str = 'PC 전용';
			break;
		case 'M' : $use_div_str = 'Mobile 전용';
			break;
		default : $use_div_str = '';
			break;
	}

	if($db->dt[cupon_sale_type] == 1){
		$sale_str = '정률할인(%)';
		$sale_unit = '%';
	}else if($db->dt[cupon_sale_type] == 2){
		$sale_str = '정액할인(원)';
		$sale_unit = '원';
	}else if($db->dt[cupon_sale_type] == 3){
        $sale_str = '전액할인';
	}
    if($db->dt[cupon_sale_type] == 3){
        $sale_str .= ' / 본사 / 본사부담 (전액)';
    }else {
        if ($db->dt[cupon_acnt] == 1) {
            $sale_str .= ' / 본사 / ' . number_format($db->dt[cupon_sale_value]) . $sale_unit . ' (본사부담 ' . number_format($db->dt[haddoffice_rate]) . $sale_unit . ')';
        } else {
            if ($db->dt[cupon_acnt] == 2) {
                $sale_str .= ' / 본사 + 셀러 / ' . number_format($db->dt[cupon_sale_value]) . $sale_unit . ' (본사부담 ' . number_format($db->dt[haddoffice_rate]) . $sale_unit . ' + 셀러부담 ' . number_format($db->dt[seller_rate]) . $sale_unit . ')';
            }
        }
    }

	if($db->dt[use_date_type] == 1){
		if($db->dt[publish_date_type] == 1){
			$date_type = '년';
		}else if($db->dt[publish_date_type] == 2){
			$date_type = '개월';
		}else if($db->dt[publish_date_type] == 3){
			$date_type = '일';
		}
		$date_differ = $db->dt[publish_date_differ];
		$use_date_type = '발행일';
		$priod_str = $use_date_type."로부터 ".$date_differ." ".$date_type."간";

	}else if($db->dt[use_date_type] == 2){
		if($db->dt[regist_date_type] == 1){
			$date_type = '년';
		}else if($db->dt[regist_date_type] == 2){
			$date_type = '개월';
		}else if($db->dt[regist_date_type] == 3){
			$date_type = '일';
		}
		$date_differ = $db->dt[regist_date_differ];
		$use_date_type = '발급일';
		$priod_str = $use_date_type."로부터 ".$date_differ." ".$date_type."간";
	}else if($db->dt[use_date_type] == 3){
		$use_date_type = '사용기간';
		$priod_str = "".substr($db->dt[use_sdate], 0, 10)." ~ ".substr($db->dt[use_edate], 0, 10)." ";
	}else if($db->dt[use_date_type] == 9){
		$use_date_type = '사용기간';
		$priod_str = '제한없음';
	}

	if($db->dt[use_date_type] == 1){
		if($db->dt[publish_date_type] == 1){
			$date_type = '년';
		}else if($db->dt[publish_date_type] == 2){
			$date_type = '개월';
		}else if($db->dt[publish_date_type] == 3){
			$date_type = '일';
		}
		$date_differ = $db->dt[publish_date_differ];
		$use_date_type = '발행일';
		$priod_str = $use_date_type."로부터 ".$date_differ." ".$date_type."간";

	}else if($db->dt[use_date_type] == 2){
		if($db->dt[regist_date_type] == 1){
			$date_type = '년';
		}else if($db->dt[regist_date_type] == 2){
			$date_type = '개월';
		}else if($db->dt[regist_date_type] == 3){
			$date_type = '일';
		}
		$date_differ = $db->dt[regist_date_differ];
		$use_date_type = '발급일';
		$priod_str = $use_date_type."로부터 ".$date_differ." ".$date_type."간";
	}else if($db->dt[use_date_type] == 3){
		$use_date_type = '사용기간';
		$priod_str = "".substr($db->dt[use_sdate], 0, 10)." ~ ".substr($db->dt[use_edate], 0, 10)." ";
	}else if($db->dt[use_date_type] == 9){
		$use_date_type = '사용기간';
		$priod_str = '제한없음';
	}

	if($db->dt[use_date_type] == 1){
		if($db->dt[publish_date_type] == 1){
			$date_type = '년';
		}else if($db->dt[publish_date_type] == 2){
			$date_type = '개월';
		}else if($db->dt[publish_date_type] == 3){
			$date_type = '일';
		}
		$date_differ = $db->dt[publish_date_differ];
		$use_date_type = '발행일';
		$priod_str = $use_date_type."로부터 ".$date_differ." ".$date_type."간";

	}else if($db->dt[use_date_type] == 2){
		if($db->dt[regist_date_type] == 1){
			$date_type = '년';
		}else if($db->dt[regist_date_type] == 2){
			$date_type = '개월';
		}else if($db->dt[regist_date_type] == 3){
			$date_type = '일';
		}
		$date_differ = $db->dt[regist_date_differ];
		$use_date_type = '발급일';
		$priod_str = $use_date_type."로부터 ".$date_differ." ".$date_type."간";
	}else if($db->dt[use_date_type] == 3){
		$use_date_type = '사용기간';
		$priod_str = "".substr($db->dt[use_sdate], 0, 10)." ~ ".substr($db->dt[use_edate], 0, 10)." ";
	}else if($db->dt[use_date_type] == 9){
		$use_date_type = '사용기간';
		$priod_str = '제한없음';
	}

	$limit_str = array();
	if($db->dt[publish_min] == 'Y'){
		$limit_str[] = number_format($db->dt[publish_condition_price])." 원 이상에만 적용 가능";
	}

    if($db->dt[publish_max_product] == 'Y'){
        $limit_str[] = number_format($db->dt[publish_max_price])." 원 미만 까지 할인 가능";
    }

    if($db->dt[publish_max] == 'Y'){
        $limit_str[] = "최대 ".number_format($db->dt[publish_limit_price])." 원까지 할인 가능";
    }

	if($db->dt[use_product_type] == 3){
		
		if($pre_type == "group_cupon"){
			$sql = "Select crp.pid, p.pname ".$product_image_column_str." from shop_cupon_relation_product crp, shop_product p where p.id = crp.pid and publish_tmp_ix = '".$db->dt[publish_tmp_ix]."' order by crp.vieworder asc";
		}else{
			$sql = "Select crp.pid, p.pname ".$product_image_column_str." from shop_cupon_relation_product crp, shop_product p where p.id = crp.pid and publish_ix = '".$db->dt[publish_ix]."' order by crp.vieworder asc";
		}
		$rproduct_db->query($sql);

		$rproduct_str = "특정 상품 / ".$rproduct_db->total." 개";
	}else if($db->dt[use_product_type] == 2){
		if($pre_type == "group_cupon"){
			$sql = "Select crc.cid from shop_cupon_relation_category crc, shop_category_info c
						where c.cid = crc.cid and publish_tmp_ix = '".$db->dt[publish_tmp_ix]."'
						order by crc.cpc_ix asc";
		}else{
			$sql = "Select crc.cid from shop_cupon_relation_category crc, shop_category_info c
						where c.cid = crc.cid and publish_ix = '".$db->dt[publish_ix]."'
						order by crc.cpc_ix asc";
		}
		$rproduct_db->query($sql);
		$rtotal = $rproduct_db->total;
		$rproduct_db->fetch(0);
		
		$rproduct_str = "특정 카테고리 / ".getCategoryPathByAdmin($rproduct_db->dt[cid], 4)." 외 ".($rtotal-1)." 개";
	}else if($db->dt[use_product_type] == 4){

		if($pre_type == "group_cupon"){
			$sql = "Select brand_name from shop_cupon_relation_brand crb, shop_brand b
						where crb.b_ix = b.b_ix and crb.publish_tmp_ix = '".intval($db->dt[publish_tmp_ix])."'";
		}else{
			$sql = "Select brand_name from shop_cupon_relation_brand crb, shop_brand b
						where crb.b_ix = b.b_ix and crb.publish_ix = '".intval($db->dt[publish_ix])."'";
		}
		$rproduct_db->query($sql);

		$rproduct_str = "특정 브랜드 (".$rproduct_db->total." 개)";

	}else if($db->dt[use_product_type] == 5){
		$sql = "SELECT ccd.company_id, ccd.com_name 
					FROM common_company_detail ccd, shop_cupon_relation_seller crs 
					where ccd.company_id = crs.company_id and  crs.publish_ix = '".intval($db->dt[publish_ix])."'  ";
		$rproduct_db->query($sql);
		$selected_sellers = $rproduct_db->fetchall();

		$rproduct_str = "특정 셀러의 상품 / ".$selected_sellers[0][com_name]." 외 ".(count($selected_sellers)-1)." 개";

	}else{
		$rproduct_str = "전체 상품";
	}

	if($db->dt[publish_type] == "1"){
		$sql = "SELECT cmd.code, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id as user_id 
					FROM common_user cu, common_member_detail cmd, shop_cupon_publish_config cpc 
					where cu.code = cmd.code and cmd.code = cpc.r_ix and cpc.publish_type = '".$db->dt[publish_type]."'  and publish_ix = '".$db->dt[publish_ix]."'  ";
		$db->query($sql);
		$selected_members = $db->fetchall();

		$publish_str = " / ".$selected_members[0][name]." 외 ".number_format(count($selected_members)-1)."명";
	}else if($db->dt[publish_type] == "4"){
		$sql = "SELECT gi.gp_ix, gi.gp_name 
					FROM shop_groupinfo gi, shop_cupon_publish_config cpc 
					where gi.gp_ix = cpc.r_ix and cpc.publish_type = '".$db->dt[publish_type]."'  and publish_ix = '".$db->dt[publish_ix]."'  ";
		$db->query($sql);
		$selected_groups = $db->fetchall();

		$gp_list = "";
		for($j = 0; $j < count($selected_groups); $j++){
			$gp_list[$j] = $selected_groups[$j][gp_name];
		}
		$publish_str = " / ".implode(", ", $gp_list);
	}

    $sql = "select            
			  config_value as front_url
			from
			  shop_mall_config
		    where
			mall_ix = '".$_SESSION['admininfo']['mall_ix']."'
			and config_name = 'front_url'";

    $mdb->query($sql);
    $mdb->fetch();
    $front_url = $mdb->dt['front_url'];

    $encKey = fbEncrypt($db->dt[publish_ix]);
    $coupon_down_uri = $front_url."/popup/couponDown/?p=".$encKey;
    $coupon_down_org_uri = $front_url."/popup/couponDown/?p=".$db->dt[publish_ix];

    switch($db->dt[cupon_div]){
        case 'G':
            $coupon_div_name = "상품쿠폰";
            break;
        case 'C':
            $coupon_div_name = "장바구니쿠폰";
            break;
        case 'D':
            $coupon_div_name = "배송비쿠폰";
            break;
    }

	$Contents = "
	<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
		<TR>
			<td align=center colspan=2 valign=top>
			<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
				<tr >
					<td align='left' colspan=2> ".GetTitleNavigation("쿠폰 상세", "쿠폰 관리 > 쿠폰 상세", false)."</td>
				</tr>
				<tr>
					<td align=center> <!-- style='padding: 0 10px 0 10px;height:569px;vertical-align:top' -->
					<table border='0' cellspacing='1' cellpadding='5' width='100%'>
					<tr>
					  <td bgcolor='#F8F9FA'>
						<table border='0' width='100%' cellspacing='1' cellpadding='0'>
							<tr>
								<td >
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
										<col width=15%>
										<col width=35%>
										<col width=15%>
										<col width=35%>
									<tr>
										<td class='input_box_title' nowrap> 이름 </td>
										<td class='input_box_item' colspan='3'>".$db->dt[publish_name]."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 설명 </td>
										<td class='input_box_item' colspan='3'>".$db->dt[publish_desc]."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 종류 </td>
										<td class='input_box_item' colspan='3'>".$coupon_div_name."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 사용범위 </td>
										<td class='input_box_item' colspan='3'>".$use_div_str."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 사용여부 </td>
										<td class='input_box_item' colspan='3'>".($db->dt[is_use] == "1" ? "사용":"미사용")."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 노출여부 </td>
										<td class='input_box_item' colspan='3'>".($db->dt[disp] == "1" ? "노출":"미노출")."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 혜택 </td>
										<td class='input_box_item' colspan='3'>".$sale_str."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 혜택 제한 </td>
										<td class='input_box_item' colspan='3'>".(empty($limit_str) ? "없음" : implode(" / ", $limit_str))."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 발급 방식 </td>
										<td class='input_box_item' colspan='3'>".($_ISSUE_TYPE[$db->dt[issue_type]]["text"])."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 발급 대상 </td>
										<td class='input_box_item' colspan='3'>".($_PUBLISH_TYPE[$db->dt[publish_type]]["text"])."".$publish_str."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 사용 기간 </td>
										<td class='input_box_item' colspan='3'>".$priod_str."</td>
									</tr>								
									<tr>
										<td class='input_box_title' nowrap> 사용가능 상품 </td>
										<td class='input_box_item' colspan='3'>".$rproduct_str."</td>
									</tr>								
									<tr>
										<td class='input_box_title' nowrap> 발급 기간 </td>
										<td class='input_box_item' colspan='3'>".date("Y-m-d",$db->dt[cupon_use_sdate])." ~ ".date("Y-m-d",$db->dt[cupon_use_edate])."</td>
									</tr>							
									<tr>
										<td class='input_box_title' nowrap> 수동발급 URL </td>
										<td class='input_box_item' colspan='3'>
											<input type='text' class='textbox' id='codeVar' style='width:450px;' readonly value='".$coupon_down_uri."' />	
											<input type='button' onclick=\"doCopy()\" value='복사' />
											<div style='padding:5px; 0;'> 원본 URL : ".$coupon_down_org_uri." (참고용 URL 로 사용불가)</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						</table>
					  </td>
					</tr>
					</tr>
					</table>
					</td>
				</tr>
			</table>
			</td>
		</tr>
	</TABLE>";

    $Script .="
	<script>
	   function doCopy(){
		   
			var copyUrl = $('#codeVar');
			copyUrl.select();
			document.execCommand('Copy');
			alert('url이 복사되었습니다.');
	   }
	</script>
	";

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->Navigation = "프로모션(마케팅)/전시 > 쿠폰관리 > 쿠폰 상세";
	$P->NaviTitle = "쿠폰 상세";
	$P->title = "쿠폰 상세";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$sql = "select gc.* , m.code,  IFNULL(m.name,'-') as name
				from shop_gift_certificate gc
				left join common_member_detail m on gc.reg_mem_ix = m.code
				where gc.gc_ix='".$gc_ix."'";

	$db->query($sql);
	$db->fetch();

	if($db->dt[gift_type]=="G"){
		$gift_type_str = "구매상품권";
	}else if($db->dt[gift_type]=="M"){
		$gift_type_str = "정회원 상품권";
	}else if($db->dt[gift_type]=="R"){
		$gift_type_str = "마일리지 지급 상품권";

		$sale_detail = number_format($db->dt[gift_amount])." 마일리지 지급";
	}else if($db->dt[gift_type]=="C" || $db->dt[gift_type]=="U"){
		if($db->dt[gift_type]=="C"){
			$gift_type_str = "쿠폰 지급 상품권(랜덤 시리얼 넘버) ";
		}else{
			$gift_type_str = "쿠폰 지급 상품권(동일 시리얼 넘버)";
		}

		$cp_sql = "select c.cupon_ix, c.cupon_sale_type, c.cupon_acnt, c.cupon_sale_value, cp.publish_name from 
						shop_gift_certificate_cupon gcc 
						left join shop_cupon_publish cp on gcc.gift_cupon_ix=cp.publish_ix 
						inner join shop_cupon c on c.cupon_ix=cp.cupon_ix
						where gc_ix='".$db->dt[gc_ix]."' order by publish_ix asc";
		$cdb->query($cp_sql);
		$coupon_infos = $cdb->fetchall("object");

		if(count($coupon_infos) > 1){
			$cp_list = array();
			for($c=0; $c < count($coupon_infos); $c++){
				$cp_list[$c] = $coupon_infos[$c][publish_name]." 쿠폰";
			}
			$sale_detail = implode(", ", $cp_list)." 증정";
		}else{
			$sale_detail = $coupon_infos[0][publish_name]." 쿠폰 증정";
		}
	}else{
		$gift_type_str = "";
	}

	$Contents = "
	<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
		<TR>
			<td align=center colspan=2 valign=top>
			<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
				<tr >
					<td align='left' colspan=2> ".GetTitleNavigation("쿠폰 상세", "쿠폰 관리 > 쿠폰 상세", false)."</td>
				</tr>
				<tr>
					<td align=center> <!-- style='padding: 0 10px 0 10px;height:569px;vertical-align:top' -->
					<table border='0' cellspacing='1' cellpadding='5' width='100%'>
					<tr>
					  <td bgcolor='#F8F9FA'>
						<table border='0' width='100%' cellspacing='1' cellpadding='0'>
							<tr>
								<td >
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
										<col width=15%>
										<col width=35%>
										<col width=15%>
										<col width=35%>
									<tr>
										<td class='input_box_title' nowrap> 이름 </td>
										<td class='input_box_item' colspan='3'>".$db->dt[gift_certificate_name]."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 설명 </td>
										<td class='input_box_item' colspan='3'>".$db->dt[memo]."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 상세 정보 </td>
										<td class='input_box_item' colspan='3' style='padding: 10px;'>
										<span>-유형 : ".$gift_type_str."</span></br>
										<span>-혜택 : ".$sale_detail."</span></br>
										".($db->dt[gift_type] == "R" ? "<span>-사용기간 : ".$db->dt[gift_start_date]." ~ ".$db->dt[gift_end_date]."</span>" : "")."
										</td>
									</tr>
								</table>
							</td>
						</tr>
						</table>
					  </td>
					</tr>
					</tr>
					</table>
					</td>
				</tr>
			</table>
			</td>
		</tr>
	</TABLE>";

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->Navigation = "프로모션(마케팅)/전시 > 쿠폰관리 > 쿠폰 상세";
	$P->NaviTitle = "쿠폰 상세";
	$P->title = "쿠폰 상세";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

?>
