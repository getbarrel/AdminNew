<?
include('../class/layout.class');

$db = new Database;
$mdb = new Database;

$Script = "

";
$db->query("select use_product_type from ".TBL_SHOP_CUPON_PUBLISH." where cupon_no='".$cupon_no."' ");
$db->fetch();

$use_product_type = $db->dt[use_product_type];

if($use_product_type=="4"){
	$reflection_title="브랜드";
	$db->query("select b.brand_name as title , CONCAT('/event/goods_brand.php?b_ix=',b.b_ix) as link  from shop_brand b , shop_cupon_relation_brand crb  where b.b_ix=crb.b_ix and crb.publish_ix = (select publish_ix from shop_cupon_publish where cupon_no='".$cupon_no."' ) ");
	$reflection_list = $db->fetchall();

}elseif($use_product_type=="2"){
	$reflection_title="카테고리";
	
	/*
	SELECT  b.cid, b.cname, b.depth
    FROM    shop_ct_info b
        ,   (
                SELECT  a.cid, a.depth, a.vlevel1, a.vlevel2, a.vlevel3, a.vlevel4, a.vlevel5
                FROM    shop_ct_info a
                WHERE   a.cid = in_cid
            ) r
    WHERE   b.category_use = '1'    -- 카테고리 노출여부
    AND     b.vlevel1 = r.vlevel1
    AND     b.vlevel2 IN ( 0, r.vlevel2 )
    AND     b.vlevel3 IN ( 0, r.vlevel3 )
    AND     b.vlevel4 IN ( 0, r.vlevel4 )
    AND     b.vlevel5 IN ( 0, r.vlevel5 )
    ORDER BY b.depth, b.vlevel1, b.vlevel2, b.vlevel3, b.vlevel4, b.vlevel5;
	*/

	$db->query("select ci.cname,ci.cid,ci.depth from shop_category_info ci , shop_cupon_relation_category crc  where  SUBSTRING(crc.cid,1,(crc.depth+1)*3) = SUBSTRING(ci.cid,1,(ci.depth+1)*3) and crc.publish_ix = (select publish_ix from shop_cupon_publish where cupon_no='".$cupon_no."' ) order by ci.depth ");
	$list = $db->fetchall();

	for($i=0;$i<count($list);$i++){
		$db->query("SELECT  b.cid, b.cname, b.depth
		FROM    shop_category_info b
			,   (
					SELECT  a.cid, a.depth, a.vlevel1, a.vlevel2, a.vlevel3, a.vlevel4, a.vlevel5
					FROM    shop_category_info a
					WHERE   a.cid = '".$list[$i][cid]."'
				) r
		WHERE   b.category_use = '1'
		AND     b.vlevel1 = r.vlevel1
		AND     b.vlevel2 IN ( 0, r.vlevel2 )
		AND     b.vlevel3 IN ( 0, r.vlevel3 )
		AND     b.vlevel4 IN ( 0, r.vlevel4 )
		AND     b.vlevel5 IN ( 0, r.vlevel5 )
		OR b.cid=r.cid
		ORDER BY b.depth , b.vlevel1, b.vlevel2, b.vlevel3, b.vlevel4, b.vlevel5");

		$cate=$db->fetchall();

		$cate_title="";

		for($j=0;$j<count($cate);$j++){
			if($j == 0)		$cate_title=$cate[$j][cname];
			else					$cate_title.=" > ".$cate[$j][cname];
		}
		/*
		if(){
			$cate_title.=" > ".$list[$i][cname];
		}*/

		$reflection_list[$i][title]=$cate_title;
		$reflection_list[$i][link]='/shop/goods_list.php?cid='.$list[$i][cid]."&depth=".$list[$i][depth];
	}
	
//print_r($reflection_list);
}elseif($use_product_type=="3"){
	$reflection_title="상품";

	$db->query("select p.pname as title , CONCAT('/shop/goods_view.php?id=',p.id) as link from ".TBL_SHOP_PRODUCT." p ,  ".TBL_SHOP_CUPON_RELATION_PRODUCT." crp where  p.id = crp.pid and crp.publish_ix = (select publish_ix from shop_cupon_publish where cupon_no='".$cupon_no."' ) ");
	$reflection_list = $db->fetchall();
}


$Contents = "
<link rel='stylesheet' href='".$admininfo[mall_data_root]."/templet/stylestory/css/common.css' type='text/css' />
<link rel='stylesheet' href='".$admininfo[mall_data_root]."/templet/stylestory/css/mypage.css' type='text/css' />
<link rel='stylesheet' href='".$admininfo[mall_data_root]."/templet/stylestory/css/dmarket_02.css' type='text/css' />
<div class='reviews_area' style='width:500px;'>
	<div class='review_box' style='width:500px;background:#b6bd0a;'>
		<div class='ag_modal_title02' style='width:476px;'>
			<img src='".$admininfo[mall_data_root]."/templet/stylestory/images/common/pop_title_img.gif' title='' align='absmiddle'> <img src='".$admininfo[mall_data_root]."/templet/stylestory/images/common/couphon_apply_title.gif' title='쿠폰적용범위확인' alt='쿠폰적용범위확인' align='absmiddle'>
		</div>
		<div class='modal_type02' style='width:490px;margin:0 auto;'>
			<div class='modal_type02_box' style='width:460px;'>
				<ul class='receipt_ul01'>
					<li>
						- 선택된 쿠폰의 사용가능한 범위 입니다. 
					</li>
					<li style='line-height:120%;'>
						- 상세 상품 적용을 알고 싶을 경우에는 하단에 상품번호를 입력하시고 검색을 클릭하시면 &nbsp; &nbsp; &nbsp;해당상품의 쿠폰적용 상태를 알수 있습니다. 
					</li>
				</ul>
				<div class='couphon_enter'>
					<form name='search' method='get' target=''>
						<table cellpadding='0' cellspacing='0' border='0' width='336' style='width:336px;margin:0 auto;'>
							<col width='60' />
							<col width='3' />
							<col width='*' />
							<col width='3' />
							<col width='47' />
							<tr>
								<td style='color:#555;'> 
									쿠폰등록
								</td>
								<td class='inputbox_01'></td> 
								<td class='inputbox_02'>
									<input type='text' id='cupon_no' name='cupon_no' value='".$cupon_no."'  class='input_text'/>
								</td>
								<td class='inputbox_03'></td>
								<td align='right'>
									<input type='image' src='".$admininfo[mall_data_root]."/templet/stylestory/images/btns/search_btn.gif' title='검색' alt='검색' align='absmiddle' />
								</td>
							</tr>
						</table>
					</form>
				</div>
				<div class='apply_cate'>
					<h1>
						적용".$reflection_title."
					</h1>
					<ul>";

					for($i=0; $i<count($reflection_list); $i++){
						$Contents .= "<li>
							<span><a href='".$reflection_list[$i]['link']."' as target='_blank'>".$reflection_list[$i]['title']."</a></span>
						</li>";
					
					}
			
$Contents .= "
					</ul>
				</div>
				<div style='padding:20px 0;text-align:center;'>
					<!--a href='#'>
						<img src='".$admininfo[mall_data_root]."/templet/stylestory/images/btns/print_btn03.gif' alt='인쇄' title='인쇄' />
					</a-->
					<a href='javascript:self.close();'>
						<img src='".$admininfo[mall_data_root]."/templet/stylestory/images/btns/pop_close_btn.gif' alt='닫기' title='닫기' />
					</a>
				</div>
			</div>
		</div>
	</div>
</div>";
                               
$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = '쿠폰적용범위 확인';
$P->NaviTitle = '쿠폰적용범위 확인';
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>