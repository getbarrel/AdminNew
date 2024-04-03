<?
$update_kind_type = 'update_state';
$page_type = 'update_state';
if($info == "state_waite" || $info == ""){
	$menu_title = '상품대기승인';
	$menu_name = "상품대기승인";
	$info == "state_waite";
}else{
	$menu_title = '상품승인거부';
	$menu_name = "상품승인거부";
	$info == 'state_cancel';
}

$help_width ='500';

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
$db3 = new Database;
include_once "product_query.php";			//상품출력 쿼리 및 검색조건
include_once "product_list_search.php";		//검색폼

$Contents11 .="
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

$Contents .="
	<form name=listform method=post action='goods_batch.act.php' onsubmit='return SelectUpdate(this)' enctype='multipart/form-data' target='act' >
	<!--onsubmit='return CheckDelete(this)'iframe_act -->
	<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
	<input type='hidden' id='pid' value=''>
	<input type='hidden' name='act' value='update'>
	<input type='hidden' name='mode' value = '".$mode."'><!--검색모드 (일반, 엑셀검색)-->
	<input type='hidden' name='search_type' id='listfrom_search_type' value='p.pname'><!--엑설검색 검색타입-->
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
			<col width='8%'>
			<col width='*'>
			<col width='15%'>
			<col width='10%'>
			<col width='20%'>
			<col width='11%'>
			<tr bgcolor='#cccccc' align=center>
				<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
				<td class=m_td>이미지</td>
				<td class=m_td>상품정보</td>
				<td class=m_td>고시정보</td>
				<td class=m_td>상품설정</td>
				<td class=m_td>옵션</td>
				<td class=e_td>등록일/수정일</td>
			</tr>";

if($db->total == 0){
	$colspan='10';
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan='".$colspan."' align=center>등록된 상품이 없습니다.[".$page_type."-".$colspan."]</td></tr>";

}else{
	$product_array = $db->fetchall();
	for ($i = 0; $i < count($product_array); $i++){
		
		$sql = "select dt_ix from shop_product_delivery where pid = '".$product_array[$i][id]."' and is_wholesale = 'R' order by delivery_div limit 0,1";
		$db3->query($sql);
		$db3->fetch();
		$dt_ix = $db3->dt[dt_ix];

		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $product_array[$i][id], "s", $product_array[$i])) || $image_hosting_type=='ftp') {
			//$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $product_array[$i][id], "s", $product_array[$i]);
		}else{
			//$img_str = "../image/no_img.gif";
		}

		$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $product_array[$i][id], "s", $product_array[$i]);

		switch($product_array[$i][stock_use_yn]){
			case 'N':
				$stock_use_yn = '사용안함';
			break;

			case 'Q':
				$stock_use_yn = '빠른재고관리';
			break;

			case 'Y':
				$stock_use_yn = 'WMS 사용';
			break;
		
		}

		if($product_array[$i][mandatory_type]){
			$mandatory_type = explode("|",$product_array[$i][mandatory_type]);

			$sql = "select * from shop_mandatory_info where mi_ix = '".$mandatory_type[0]."'";
			$rdb->query($sql);
			$rdb->fetch();
			$mandatory_name = $rdb->dt[mandatory_name];
		}

	$innerview .= "
				<tr bgcolor='#ffffff' height='45'>
					<td class='list_box_td list_bg_gray'><input type=checkbox class=nonborder id='cpid' name='select_pid[]' value='".$product_array[$i][id]."'></td>
					<td class='list_box_td' align=center style='padding:5px 0px;'>";
					if($admininfo[mall_use_multishop]){
						$innerview .= "<span style='cursor:pointer;' class='helpcloud' help_width='220' help_height='70' help_html='".get_state_info($product_array[$i][id],$product_array[$i][state])."'>";
						if($product_array[$i][state] == 1){
							$innerview .= "<div id='state_txt_".$product_array[$i][id]."'><a href='product_list.act.php?act=state_update&pid=".$product_array[$i][id]."&state=".$product_array[$i][state]."'   target='iframe_act'><img src='../images/".$admininfo["language"]."/btn_sell.gif' align=absmiddle></a></div>";
						}else if($product_array[$i][state] == 6){
							$innerview .= "	<span style='color:red;font-weight:bold;'>[등록신청중]</span>";
						}else if($product_array[$i][state] == 0){
							$innerview .= "<div id='state_txt_".$product_array[$i][id]."'><a href='product_list.act.php?act=state_update&pid=".$product_array[$i][id]."&state=".$product_array[$i][state]."'   target='iframe_act'><img src='../images/".$admininfo["language"]."/btn_sold_out.gif' align=absmiddle></a></div>";
						}
						$innerview .= "</span>
						<div style='padding:2px;'></div>";

						if($product_array[$i][disp] == 1){
							$innerview .= "<div id='disp_txt_".$product_array[$i][id]."'><a href='product_list.act.php?act=disp_update&pid=".$product_array[$i][id]."&disp=".$product_array[$i][disp]."'  target='iframe_act'><img src='../images/".$admininfo["language"]."/btn_off_view.gif' align=absmiddle></a></div>";
						}else if($product_array[$i][disp] == 0){
							$innerview .= "<div id='disp_txt_".$product_array[$i][id]."'><a href='product_list.act.php?act=disp_update&pid=".$product_array[$i][id]."&disp=".$product_array[$i][disp]."'   target='iframe_act'><img src='../images/".$admininfo["language"]."/btn_on_view.gif' align=absmiddle></a></div>";
						}
					}
	$innerview .= "<br><a href='/shop/goods_view.php?id=".$product_array[$i][id]."' target='_blank' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $product_array[$i][id], $LargeImageSize, $product_array[$i])."'><img src='".$img_str."' width=50 height=50></a><br>";
	$innerview .= "
					</td>
					<td class='list_box_td' style='text-align:left;line-height:120%;'>
						<table >
						<tr>
							<td>
								<b>셀러업체 :</b> <span style='cursor:pointer;color:#0054FF;' class='helpcloud' help_width='220' help_height='70' help_html='".GET_SELLER_INFO($product_array[$i][admin])."'>[".$product_array[$i][com_name]."]</span><br>
								<b>상품명 :</b> <a href='goods_input.php?id=".$product_array[$i][id]."'><b>".$product_array[$i][pname]."</b>(".$product_array[$i][pcode].")</a><br>
								<b>카테고리 :</b> ".getCategoryPathByAdmin($product_array[$i][cid], 4)."<br>
								<b>검색키워드 :</b>".$product_array[$i][search_keyword]."<br>
								<b>배송정책 :</b>".(product_list_policy_text($dt_ix) == ''?'<b>미지정</b>':product_list_policy_text($dt_ix))."<br>
							</td>
						</tr>
						</table>
					</td>
					<td class='list_box_td ' style='text-align:left;'>
						<table>
						<tr>
							<td>
								<b>고시정보 :</b> <span style='cursor:pointer;color:#0054FF;' class='helpcloud' help_width='500' help_height='100' help_html='".select_mantatory_info($product_array[$i][id])."'>[".$mandatory_name."]</span><br>
								<b>소매가 :</b> ".number_format($product_array[$i][sellprice])." 원<br>
								<b>원산지 :</b> ".$product_array[$i][origin]."<br>
								<b>제조사 :</b> ".$product_array[$i][company]."<br>
							</td>
						</tr>
						</table>
					</td>
					<td class='list_box_td ' style='text-align:left;'>
						<table>
						<tr>
							<td>
								<b>면세제품 :</b> ".($product_array[$i][surtax_yorn]=='N'?'과세':'면세')."<br>
								<b>19금여부 :</b> ".($product_array[$i][is_adult]=='0'?'미적용':'적용')."<br>
							</td>
						</tr>
						</table>
					</td>
					<td class='list_box_td '  style='text-align:left;'>
						<table>
						<tr>
							<td>
								<b>재고관리 :</b> ".$stock_use_yn."<br>
								<b>옵션관리 :</b><br>
								".produt_select_option($product_array[$i][id])."<br>
							</td>
						</tr>
						</table>
					</td>
					<td class='list_box_td '>".$product_array[$i][regdate]."<br>".($product_array[$i][editdate]=='0000-00-00 00:00:00'?'-':$product_array[$i][editdate])."</td>
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
include ('./product_list_update.php');	//상품업데이트 폼 2014-04-16 이학봉
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
	<script Language='JavaScript' src='product_list.js'></script>
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
	$P->strLeftMenu = product_menu();
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


function select_mantatory_info($pid){
	
	$db = new Database;

	$sql = "select * from shop_product_mandatory_info where pid = '".$pid."' order by pmi_code ASC";
	$db->query($sql);
	$data = $db->fetchall();

	for($i=0;$i<count($data);$i++){
		$return_data .= $data[$i][pmi_title]." : ".$data[$i][pmi_desc]."<br>";
	}

	return $return_data;
}

function produt_select_option($pid){
	
	$db = new Database;

	//기본옵션 : 'c1','c2','i1','i2'
	//가격+재고관리옵션 : b
	//추가 구성상품 옵션 : a
	//박스옵션 : x
	//세트(묶음상품) 옵션 : s2, x2
	//코디상품옵션 : c
	$option_type_1 = array('c1','c2','i1','i2');
	$option_type_2 = array('b');
	$option_type_3 = array('a');
	$option_type_4 = array('x');
	$option_type_5 = array('s2','x2');
	$option_type_6 = array('c');
	$sql = "select * from shop_product_options where pid = '".$pid."'";
	$db->query($sql);
	$data = $db->fetchall();

	for($i=0;$i<count($data);$i++){
		if(in_array($data[$i][option_kind],$option_type_1)){
			$return_data .= "&nbsp;&nbsp;&nbsp;<span style='cursor:pointer;color:#0054FF;' class='helpcloud' help_width='350' help_height='100' help_html='".select_option_detail($data[$i][opn_ix])."'>상품 기본옵션 - ".$data[$i][option_name]."</span><br>";
		}
		if(in_array($data[$i][option_kind],$option_type_2)){
			$return_data .= "&nbsp;&nbsp;&nbsp;<span style='cursor:pointer;color:#0054FF;' class='helpcloud' help_width='350' help_height='100' help_html='".select_option_detail($data[$i][opn_ix])."'>가격+재고관리옵션 - ".$data[$i][option_name]."</span><br>";
		}
		if(in_array($data[$i][option_kind],$option_type_3)){
			$return_data .= "&nbsp;&nbsp;&nbsp;<span style='cursor:pointer;color:#0054FF;' class='helpcloud' help_width='350' help_height='100' help_html='".select_option_detail($data[$i][opn_ix])."'>추가 구성상품 옵션 - ".$data[$i][option_name]."</span><br>";
		}
		if(in_array($data[$i][option_kind],$option_type_4)){
			$return_data .= "&nbsp;&nbsp;&nbsp;<span style='cursor:pointer;color:#0054FF;' class='helpcloud' help_width='350' help_height='100' help_html='".select_option_detail($data[$i][opn_ix])."'>박스옵션 - ".$data[$i][option_name]."</span><br>";
		}
		if(in_array($data[$i][option_kind],$option_type_5)){
			$return_data .= "&nbsp;&nbsp;&nbsp;<span style='cursor:pointer;color:#0054FF;' class='helpcloud' help_width='350' help_height='100' help_html='".select_option_detail($data[$i][opn_ix])."'>세트(묶음상품)옵션션 - ".$data[$i][option_name]."</span><br>";
		}
		if(in_array($data[$i][option_kind],$option_type_6)){
			$return_data .= "&nbsp;&nbsp;&nbsp;<span style='cursor:pointer;color:#0054FF;' class='helpcloud' help_width='350' help_height='100' help_html='".select_option_detail($data[$i][opn_ix])."'>코디상품옵션 - ".$data[$i][option_name]."</span><br>";
		}
	}

	return $return_data;
}

function select_option_detail($opn_ix){
	
	if(!$opn_ix){
		return false;
	}
	
	$db = new Database;

	$sql = "select * from shop_product_options_detail where opn_ix = '".$opn_ix."'";
	$db->query($sql);
	$data = $db->fetchall();

	for($i=0;$i<count($data);$i++){
		$return_data .= "옵션구분 : ".$data[$i][option_div]." 옵션 판매가 : ".$data[$i][option_listprice]." 옵션 할인가 : ".$data[$i][option_price]."<br>";
	}
	
	return $return_data;

}

?>