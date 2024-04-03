<?
include_once("../product/goods_input.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include_once("../class/layout.class");


if($admininfo[admin_level] < 9){
	//header("Location:/admin/seller/");
}

if(!$menu_name){					//메뉴,타이틀명
	$menu_title = '상품일괄수정';
	$menu_name = "카테고리";
}

if(!$update_kind_type){					//기본페이지는 카테고리 일괄수정 (일괄수정폼 관련 변수값)
	$update_kind_type = "category";
}

if(!$help_width){
	$help_width ='400';
}

if($_COOKIE[$page_type."_max_limit"]){
	$max = $_COOKIE[$page_type."_max_limit"]; //페이지당 갯수
}else{
	$max = 50;
}

if($before_update_kind){
	$update_kind_type = $before_update_kind;
}

if($_COOKIE["goodsinfo_update_kind"]){
	//$update_kind = $_COOKIE["goodsinfo_update_kind"];
}
if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;
$db2 = new Database;
$rdb = new Database;

include_once "../product/product_query.php";			//상품출력 쿼리 및 검색조건
include_once "../product/product_list_search.php";		//검색폼

//echo nl2br($sql);
if($update_kind_type == 'update_state'){	//상품승인대기시 탭
$Contents .="
	<table border=0 cellpadding=0 cellspacing=0 width='100%'>
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("상품리스트", "상품관리 > 상품리스트")."</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:15px;'>
			<div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".($info == "state_waite" || $info == "" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'><a href='?info=state_waite'>승인대기</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info == "state_cancel"? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'><a href='?info=state_cancel'>승인거부</a></td>
						<th class='box_03'></th><!--onclick=\"ShowSearchDiv('excel')\"-->
					</tr>
					</table>
				</td>
				<td class='btn'>
				</td>
			</tr>
			</table>
		</div>
		</td>
	</tr>
	</table>";

}

$Contents .="
	<form name=listform method=post action='goods_batch.act.php' onsubmit='return SelectUpdate(this)' enctype='multipart/form-data' target='act' >
	<!--onsubmit='return CheckDelete(this)'iframe_act -->
	<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
	<input type='hidden' id='pid' value=''>
	<input type='hidden' name='act' value='update'>
	<input type='hidden' name='mode' value = '".$mode."'><!--검색모드 (일반, 엑셀검색)-->
	<input type='hidden' name='search_type' id='listfrom_search_type' value='p.pname'><!--엑설검색 검색타입-->
	<input type='hidden' name='search_act_total' value='$total'> 
	<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td valign=top style='padding-top:33px;'>";

$Contents .= "
		</td>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

$innerview = "
			<table width='100%' cellpadding=0 cellspacing=0 border=0 >
				<tr height=30>
					<td align=left>
					상품수 : ".number_format($total)." 개
					</td>
					<td align=right>
					</td>
					<td align=right>
				목록수 : <select name='max' id='max'>
							<option value='5' ".($_COOKIE[$page_type."_max_limit"] == '5'?'selected':'').">5</option>
							<option value='10' ".($_COOKIE[$page_type."_max_limit"] == '10'?'selected':'').">10</option>
							<option value='20' ".($_COOKIE[$page_type."_max_limit"] == '20'?'selected':'').">20</option>
							<option value='30' ".($_COOKIE[$page_type."_max_limit"] == '30'?'selected':'').">30</option>
							<option value='50' ".($_COOKIE[$page_type."_max_limit"] == '50'?'selected':'').">50</option>
							<option value='100' ".($_COOKIE[$page_type."_max_limit"] == '100'?'selected':'').">100</option>
						</select>
				</td>
				</tr>
			</table>";

$innerview .= "
			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
				<col width='2%'>
				<col width='11%'>
				<col width='8%'>
				<col width='9%'>
				<col width='8%'>";
if($page_type == "update_seller"){
$innerview .= "
				<col width='8%'>";
}
$innerview .= "
				<col width='*'>
				<!--col width='18%'-->
				<col width='5%'>
				<col width='5%'>
				<col width='8%'>
				<!--col width='8%'-->";
if($page_type != "update_sell_priod_date"){
$innerview .= "
				<col width='6%'>";
}
if($page_type == "update_price"){
	/*
	$innerview .= "
				<col width='8%'>
				<col width='8%'>
				<col width='8%'>";
				*/
}else if($page_type == "update_delivery_policy"){
	$innerview .= ""; 
}else if($page_type == "update_wish"){
	$innerview .= "
				<col width='6%'>
				<col width='5%'>
				";
}

$innerview .= "
				<tr bgcolor='#cccccc' align=center>
					<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<td class=m_td>상품등록일<br>최근수정일</td>
					<td class=m_td>시스템코드</td>";

if($page_type == 'update_delivery_policy'){
$innerview .= "
					<td class=m_td>배송타입</td>";
}else{
$innerview .= "
					<td class=m_td>상품코드</td>";
}

$innerview .= "
					<td class=m_td>셀러명</td>";
if($page_type == "update_seller"){
$innerview .= "
					<td class=m_td>입점업체</td>";
}
if($page_type == "update_product_md"){
$innerview .= "
					<td class=m_td>담당자 ID/이름</td>";
}
$innerview .= "
					<td class=m_td>상품명</td>
					<!--td class=m_td>카테고리</td-->
					<td class=m_td>노출여부</td>
					<td class=m_td>판매상태</td>";
if($page_type != "update_basic_info" && $page_type != "update_movie" && $page_type != "update_product_point"){
$innerview .= "
					<td class=m_td>소매가/할인가</td>";
}
if($page_type == "update_product_point"){
$innerview .= "
					<td class=m_td>".OrderByLink("상품레벨점수", "product_point", $ordertype)."</td>";
}

if($page_type == "update_price"){
$innerview .= "
					<td class=e_td>적립금</td>";
}else if($page_type == "update_brand"){
$innerview .= "		<td class=e_td>브랜드</td>
					<td class=e_td>원산지</td>
					<td class=e_td>제조사</td>";
}else if($page_type == "update_delivery_policy"){
$innerview .= "		<td class=e_td>배송정책</td>
					";
}else if($page_type == "update_wish"){
$innerview .= "		<td class=e_td>클릭수<br>판매수<br>전환율</td>
					<td class=e_td>관련상품</td>";
}else if($page_type == "update_basic_info"){
$innerview .= "		<td class=e_td>아이콘</td>
					<td class=e_td>SNS</td>
					<td class=e_td>키워드</td>";
}else if($page_type == "update_movie"){
$innerview .= "		<td class=e_td>동영상</td>
					<td class=e_td>바이럴</td>";
}else if($page_type == "update_mandatory_type"){
$innerview .= "		<td class=e_td>클릭수<br>판매수<br>전환율</td>";
}else if($page_type == "update_product_md"){
	$innerview .= "		<td class=e_td>클릭수<br>판매수<br>전환율</td>";
}else if($page_type == "update_product_commission"){
	$innerview .= "		<td class=e_td>상품별수수료</td>";
}else if($page_type == "update_product_point"){
	$innerview .= "		<td class=e_td>관리</td>";
}else if($page_type == "update_available_stock"){
	$innerview .= "		<td class=e_td>가용재고</td>";
}else{
	$innerview .= "
					<td class=e_td>카테고리할인</td>";
}


$innerview .= "
				</tr>";

if(count($goods_datas) == 0){
//if($db->total == 0){
	if($page_type == 'update_brand'){
		$colspan='12';
	}else if($page_type == 'update_delivery_policy' ){
		$colspan='10';
	}else if($page_type == 'update_sell_priod_date' ){
		$colspan='10';
		
	}else if($page_type == 'update_seller' || $page_type == 'update_wish'  || $page_type == 'update_basic_info' || $page_type == 'update_product_md'){
		$colspan='11';
	}else{
		$colspan='10';
	}
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan='".$colspan."' align=center>등록된 상품이 없습니다.</td></tr>";

}else{
	//$goods_datas = $db->fetchall();
	for ($i = 0; $i < count($goods_datas); $i++)
	{
		//$db->fetch($i);
		/*
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $goods_datas[$i][id], "s", $goods_datas[$i])) || $image_hosting_type=='ftp') {
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $goods_datas[$i][id], "s", $goods_datas[$i]);
		}else{
			$img_str = "../image/no_img.gif";
		}*/

		$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $goods_datas[$i][id], "s", $goods_datas[$i]);

	if(!empty($goods_datas[$i][md_code])){
		$sql="select
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
					cu.id
				from	
					".TBL_COMMON_USER." as cu
					inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
				where
					mem_div = 'MD'
					and mem_type = 'A'
					and cu.code = '".$goods_datas[$i][md_code]."'
					order by cmd.name ASC";
		$db->query($sql);
		$db->fetch();
		$MD = $db->dt[id]." / ".$db->dt[name];
	}else{
		$MD="";
	}

	$innerview .= "
				<tr bgcolor='#ffffff' height='45'>
					<td class='list_box_td list_bg_gray'><input type=checkbox class=nonborder id='cpid' name='select_pid[]' value='".$goods_datas[$i][id]."'></td>
					<td class='list_box_td '>".$goods_datas[$i][regdate]."<br>".$goods_datas[$i][editdate]."</td>
					<td class='list_box_td '>".$goods_datas[$i][id]."</td>";
	if($page_type == 'update_delivery_policy'){
	
		if($goods_datas[$i][delivery_type] == '1'){
			$delivery_type_text = '통합배송';
		}else{
			$delivery_type_text = '입점업체배송';
		}
	$innerview .= "
					<td class='list_box_td '>".$delivery_type_text."</td>";
	}else{
	$innerview .= "
					<td class='list_box_td '>".$goods_datas[$i][pcode]."</td>";
	}
	$innerview .= "
					<td class='list_box_td '><span style='cursor:pointer;' class='helpcloud' help_width='220' help_height='70' help_html='".GET_SELLER_INFO($goods_datas[$i][admin])."'> ".$goods_datas[$i][com_name]."</span></td>";

	if($page_type == "update_product_md"){
	$innerview .= "
					<td class='list_box_td '>".$MD."</td>";
	}

	if($page_type == "update_seller"){
	$innerview .= "
					<td class='list_box_td '>".$goods_datas[$i][trade_name]."</td>";
	}

	$innerview .= "
					<td class='list_box_td point' style='text-align:left;line-height:140%;'>
						<table style='width:200px;'>
							<tr>
								<td><a href='/shop/goods_view.php?id=".$goods_datas[$i][id]."' target='_blank' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $goods_datas[$i][id], $LargeImageSize, $goods_datas[$i])."'><img src='".$img_str."' width=50 height=50></a></td>
								<td>
									<!--".getCategoryPathByAdmin($goods_datas[$i][cid], 4)."<br>-->
									<a href='../product/goods_input.php?id=".$goods_datas[$i][id]."'><b>".$goods_datas[$i][pname]."</b>(".$goods_datas[$i][pcode].")</a>
								</td>
							</tr>
						</table>
					</td>
					<!--td class='list_box_td list_bg_gray' align='left' style='text-align:left;'></td-->
					<td align=center >";
						if($goods_datas[$i][disp] == 1){
							$innerview .= "노출";
						}else if($goods_datas[$i][disp] == 0){
							$innerview .= "미노출";
						}
$innerview .= "		</td>
					<td class='list_box_td ' >";
						if($goods_datas[$i][state] == 1){
							$innerview .= "판매중";
						}else if($goods_datas[$i][state] == 6){
							$innerview .= "승인대기";
						}else if($goods_datas[$i][state] == 7){
							$innerview .= "본사대기상품";
						}else if($goods_datas[$i][state] == 0){
							$innerview .= "일시품절";
						}else if($goods_datas[$i][state] == 2){
							$innerview .= "판매중지";
						}else if($goods_datas[$i][state] == 8){
							$innerview .= "승인거부";
						}else if($goods_datas[$i][state] == 9){
							$innerview .= "판매금지";
						}
$innerview .= "		</td>";

if($page_type != "update_basic_info" && $page_type != "update_movie" && $page_type != "update_product_point"){
$innerview .= "
					<td class='list_box_td ' nowrap>
						".number_format($goods_datas[$i][listprice])." / ".number_format($goods_datas[$i][sellprice])."
					</td>";
}
if($page_type == "update_product_point"){
$innerview .= "
					<td class='list_box_td ' nowrap>
						".number_format($goods_datas[$i][product_point])."
					</td>";
}

if($page_type == "update_price"){
$innerview .= "	
					<td class='list_box_td list_bg_gray' nowrap>";
						if($goods_datas[$i][reserve_yn] == "Y"){
							$innerview .= $goods_datas[$i][reserve]."/".$goods_datas[$i][reserve_rate]."%<br>";
						}else{
							$innerview .= "미적용(기본설정)<br>";
						}
						if($goods_datas[$i][wholesale_reserve_yn] == "Y"){
							$innerview .= $goods_datas[$i][wholesale_reserve]."/".$goods_datas[$i][wholesale_reserve_rate]."%<br>";
						}else{
							$innerview .= "미적용(기본설정)";
						}
			$innerview .= "	
					</td>";
}else if($page_type == "update_brand"){
$innerview .= "		
					<td class='list_box_td ' nowrap>
						".$goods_datas[$i][brand_name]."
					</td>
					<td class='list_box_td ' nowrap>
						".$goods_datas[$i][origin]."
					</td>
					<td class='list_box_td ' nowrap>
						".$goods_datas[$i][company]."
					</td>";
}else if($page_type == "update_delivery_policy"){
					
$innerview .= "		
					<td class='list_box_td ' nowrap>
						".($goods_datas[$i][delivery_policy] == '1'?"<span style='cursor:pointer;' class='helpcloud' help_width='220' help_height='70' help_html='".GetProductDliveryPolicyText($goods_datas[$i][id],'R')."'>기본 <img src='/admin/images/Q_icon.png'></span><br>".GetDeliveryName($goods_datas[$i][id],'R'):" <span style='cursor:pointer;' class='helpcloud' help_width='220' help_height='70' help_html='".GetProductDliveryPolicyText($goods_datas[$i][id],'R')."'>개별 <img src='/admin/images/Q_icon.png'></span><br>".GetDeliveryName($goods_datas[$i][id],'R'))."
					</td>
";
}else if($page_type == "update_wish"){
					
$innerview .= "		
					<td class='list_box_td ' nowrap>
						".number_format($goods_datas[$i][view_cnt])." / ".number_format($goods_datas[$i][order_cnt])." / ".($goods_datas[$i][view_cnt] > 0?round($goods_datas[$i][order_cnt]/$goods_datas[$i][view_cnt] * 100,2):'0')."%
					</td>
					<td class='list_box_td ' nowrap>
						".$goods_datas[$i][relation_cnt]." 개 
					</td>";
}else if($page_type == "update_basic_info"){
$innerview .= "		
					<td class='list_box_td ' nowrap>
						".($goods_datas[$i][icons] == ""?"미지정":"사용 <span style='cursor:pointer;' class='helpcloud' help_width='220' help_height='70' help_html='".GetProductIcons($goods_datas[$i][icons])."'><img src='/admin/images/Q_icon.png'></span>")."
					</td>
					<td class='list_box_td ' nowrap>
						".($goods_datas[$i][sns_btn_yn] == 'Y'?"사용 <span style='cursor:pointer;' class='helpcloud' help_width='220' help_height='70' help_html='".GetProductSns($goods_datas[$i][sns_btn])."'><img src='/admin/images/Q_icon.png'></span>":"미사용")."
					</td>
					<td class='list_box_td ' nowrap>
						".(strlen($goods_datas[$i][search_keyword]) > '20'?cut_str($goods_datas[$i][search_keyword],20)." <span style='cursor:pointer;' class='helpcloud' help_width='220' help_height='70' help_html='".$goods_datas[$i][search_keyword]."'><img src='/admin/images/Q_icon.png'></span>":$goods_datas[$i][search_keyword])."
					</td>";
}else if($page_type == "update_movie"){
$innerview .= "		
					<td class='list_box_td ' nowrap>
						".($goods_datas[$i][movie] == ""?"미지정":cut_str($goods_datas[$i][movie],20)." <span style='cursor:pointer;' class='helpcloud' help_width='220' help_height='70' help_html='".$goods_datas[$i][movie]."'><img src='/admin/images/Q_icon.png'></span>")."
					</td>
					<td class='list_box_td ' nowrap>
						".($goods_datas[$i][viral_url_total] == 0?"미지정":" 자세히보기 <span style='cursor:pointer;' class='helpcloud' help_width='220' help_height='70' help_html='".GetProductViralInfo($goods_datas[$i][id])."'><img src='/admin/images/Q_icon.png'></span>")."
					</td>";
}else if($page_type == "update_mandatory_type"){
					
$innerview .= "		
					<td class='list_box_td ' nowrap>
						".number_format($goods_datas[$i][view_cnt])." / ".number_format($goods_datas[$i][order_cnt])." / ".($goods_datas[$i][view_cnt] > 0?round($goods_datas[$i][order_cnt]/$goods_datas[$i][view_cnt] * 100,2):'0')."%
					</td>";
}else if($page_type == "update_product_md"){
					
$innerview .= "		
					<td class='list_box_td ' nowrap>
						".number_format($goods_datas[$i][view_cnt])." / ".number_format($goods_datas[$i][order_cnt])." / ".($goods_datas[$i][view_cnt] > 0?round($goods_datas[$i][order_cnt]/$goods_datas[$i][view_cnt] * 100,2):'0')."%
					</td>";
}else if($page_type == "update_product_commission"){
					
$innerview .= "		
					<td class='list_box_td ' nowrap>
						".$goods_datas[$i][wholesale_commission]."% / ".$goods_datas[$i][commission]."%
					</td>";
}else if($page_type == "update_product_point"){
$innerview .= "		
					<td class='list_box_td ' nowrap>
						".$goods_datas[$i][total_point]."

					<a href=\"javascript:PoPWindow('product_point.pop.php?pid=".$goods_datas[$i][id]."',850,550,'product_point_pop')\">
						<img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'>
					</a>

					</td>";
}else if($page_type == "update_available_stock"){
$innerview .= "		
					<td class='list_box_td ' nowrap>
						".$goods_datas[$i][available_stock]." 개
					</td>";
}else{
$innerview .= "	
					<td class='list_box_td list_bg_gray' nowrap>
						".getProductCategoryRate($goods_datas[$i][id],$goods_datas[$i][cid])."%
					</td>";
}

$innerview .= "	
				</tr>";
	}
}

$innerview .= "</table>
			<table width='100%'>
			<tr height=40>
				<td width=210>

				</td>
				<td align=right>".$str_page_bar."</td>
			</tr>
			<tr height=30><td colspan=2 align=right></td></tr>
			</table>
			";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
		<IFRAME id=bsframe name=bsframe src='' frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>";


//상품업데이트 폼 시작 
include ('../product/product_list_update.php');	//상품업데이트 폼 2014-04-16 이학봉
$Contents .= "".HelpBox($select, $help_text,$help_width)."</form>";
//상품업데이트 폼 끝 

$Script .= "<script Language='JavaScript' type='text/javascript'>
$(document).ready(function (){

	$('#max').change(function(){
		var value= $(this).val();
		//alert('".$page_type."');
		$.cookie('".$page_type.$info."_max_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});
		document.location.reload();
		
	});

});

</script>";

$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
		$inner_category_path = getCategoryPathByAdmin($cid2, $depth);
	echo "
	<Script>
	//alert(document.body.innerHTML);
	parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
	try{
	parent.document.getElementById('select_category_path1').innerHTML=\"".($search_text == "" ? $inner_category_path."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."\" ;
	}catch(e){}
	parent.document.search_form.cid2.value ='$cid2';
	parent.document.search_form.depth.value ='$depth';
	parent.LargeImageView();
	parent.unblockLoadingBox();
	</Script>";
}else{
	$Script .= "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	<!--script Language='JavaScript' src='product_input.js'></script--><!--2011.06.18 없는게 정상 주석처리후 확인필요-->
	<script Language='JavaScript' src='../product/product_list.js'></script>
	<script Language='JavaScript' src='../js/scriptaculous.js' type='text/javascript'></script>
	<script type='text/javascript' src='../js/ms_productSearch.js'></script>
	<script Language='JavaScript' type='text/javascript'>

	function loadCategory(sel,target){
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.getAttribute('depth');
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
		//alert(depth);
		//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		//alert(1);
		// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
		window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	}

	function loadChangeCategory(sel,target){
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;
		//var depth = sel.getAttribute('depth');
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)

		//dynamic.src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
		window.frames['act'].location.href = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	}
	</script>";

	$P = new LayOut();
	if($page_type == 'update_sellertool_goods'){
		
		$P->strLeftMenu = sellertool_menu();
	}else if($page_type == 'update_product_mrogroup'){
		$P->strLeftMenu = member_menu();
	}else{
		$P->strLeftMenu = product_menu();
	}
	$P->addScript = $Script;
	$P->Navigation = "상품관리 > ".$menu_title." > ".$menu_name;
	$P->title = $menu_name;
	$P->strContents = $Contents;
	$P->jquery_use = false;

	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}
?>