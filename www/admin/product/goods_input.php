<?php
/*
	2013-05-31 홍진영 상품정보 CKEDITOR로 교체
*/
include_once("../class/layout.class");
include_once("goods_input.lib.php");
include_once("car.lib.php");
include_once("buyingService.lib.php");
include_once("realestate.lib.php");
include_once("../inventory/inventory.lib.php");
@include("../buyingservice/buying.lib.php");

$db = new Database;
$db2 = new Database;
$adb = new Database;
$pdb = new Database;
$rdb = new Database;
$rdb2 = new Database;

$language_list = getTranslationType("","","array");
foreach ($language_list as $k => $l) {
    if($l['language_code'] == 'korean'){
        unset($language_list[$k]);
        break;
    }
}
$globalInfo = getGlobalInfo();

//환율 정보 가져오기
$currencyInfo = getCurrencyInfo('','','array');
$exchange_info = "";
if(is_array($currencyInfo)){
    foreach($currencyInfo as $key=>$val){
        if($val['currency_code'] == 'USD'){
            $exchange_info = $val['exchange_rate'];
        }
    }
}

if($_SESSION["admininfo"][admin_level] == 9){
    $DISPLAY_WHOSALE_INFO = true;
}else{
    $DISPLAY_WHOSALE_INFO = false;
}

//echo md5($_SERVER["PHP_SELF"])."<br>";

//print_r($admin_config);

$page_code = md5("/admin/product/goods_input.php");
//echo $page_code."<br>";

$sql = "select * from shop_delivery_template where company_id = '".$admininfo[company_id]."' and is_basic_template = '1' and product_sell_type = 'R'";
$db->query($sql);
if(!$db->total){
//	echo "<script language='JavaScript'>alert('상품등록을 위해서는 셀러 기본 배송정책 설정이 필요합니다. ');document.location.href='../seller/seller_delivery_info.php'; </script>";
//	exit;
}

if($id != ""){
    if($_SESSION["admininfo"][admin_level] == 9){
        $db->query("SELECT * FROM ".TBL_SHOP_PRODUCT." where id = $id");
    }else{
        $db->query("SELECT * FROM ".TBL_SHOP_PRODUCT." where id = $id and admin = '".$admininfo[company_id]."'");
    }
    if($db->total == 0){
        echo "<script language='JavaScript'>alert('해당 상품정보가 없습니다.');history.go(-1); </script>";
        exit;
    }

    $sql = "select            
			  config_value as front_url
			from
			  shop_mall_config
		    where
			mall_ix = '".$_SESSION['admininfo']['mall_ix']."'
			and config_name = 'front_url'";

    $db2->query($sql);
    $db2->fetch();
    $front_url = $db2->dt['front_url'];

    $client_host = $front_url."/shop/goodsView/".$id."?viewMode=preview";
}

$sql = "select * from shop_buyingservice_info order by regdate desc limit 0,1 ";
$db->query ($sql);

if($db->total){
    $db->fetch();

    $exchange_rate = $db->dt[exchange_rate];
    $bs_basic_air_shipping = $db->dt[bs_basic_air_shipping];
    $bs_add_air_shipping = $db->dt[bs_add_air_shipping];
    $bs_duty_rate = $db->dt[bs_duty];
    $bs_supertax_rate = $db->dt[bs_supertax_rate];
    $bs_clearance_fee = $db->dt[clearance_fee];
}

if($id){
    $sql = "select * from shop_product_buyingservice_priceinfo where pid = '".$id."' order by regdate desc limit 0,1 ";
    $db->query ($sql);

    if($db->total){
        $db->fetch();

        $orgin_price = $db->dt[orgin_price];
        $exchange_rate = $db->dt[exchange_rate];
        $air_wt = $db->dt[air_wt]; // 예상무게 는 기 입력된 값을 가져옴
        $air_shipping = $db->dt[air_shipping]; // 항공운송료 값도 기 입력된 값을 가져옴
        $duty = (float)$db->dt[duty];
        $clearance_fee = $db->dt[clearance_fee];
        $clearance_type = $db->dt[clearance_type];
        $bs_fee_rate = $db->dt[bs_fee_rate];
        $bs_fee = $db->dt[bs_fee];

    }
}

if($id == ""){
    $sql = "select commission,wholesale_commission,account_type from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '".$_SESSION["admininfo"][company_id]."'";

}else{
    $sql = "select csd.commission,csd.wholesale_commission,csd.account_type  from ".TBL_COMMON_SELLER_DELIVERY." csd , shop_product p where p.id = '".$id."' and p.admin = csd.company_id";
}

$db->query($sql);
$db->fetch();

$company_commission = $db->dt[commission];
$commission = $db->dt[commission];
$company_wholesale_commission = $db->dt[wholesale_commission];		// 이학봉 2013-10-29 셀러관련
$wholesale_commission = $db->dt[wholesale_commission];				// 이학봉 2013-10-29 셀러관련shop_icon
$account_type = $db->dt[account_type];								// 이학봉 2013-10-29 셀러관련


$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"][mall_data_root]."/images/product", $id, 'Y');

$db->query("select idx from shop_icon where disp = '1' and icon_type='P' order by idx");
if($db->total){
    $icon_list = $db->fetchall();
    //print_r($icon_list);
}

$Script = "
<style>
div.changeable_area {padding-bottom:20px;}
div#drop_relation_product { width:100%;height:100%;overflow:auto;padding:1px;border:1px solid silver }
div#drop_relation_product.hover { border:5px dashed #aaa; background:#efefef; }

ul {
	LIST-STYLE-IMAGE: none; LIST-STYLE-TYPE: none;padding:0px;
}
li{
	list-style-tyle:none;
	margin:0px;
	padding:0px;
}
  #sortlist {
      list-style-type:none;
      margin:0;
      padding:0;
   }
   #sortlist li {
     font:13px Verdana;
     margin:0;
     padding:0px;
     cursor:move;
   }
  .ctr_1 {text-align:center;}


.side-btn {display:block; position:absolute; top:215px; right:0; width:37px; font-size:0;}
.side-btn a {display:block;}
</style>

<script language='JavaScript'>

function sendMessage(msg){
	window.HybridApp.callAndroid(msg);
}
</script>
<script type='text/javascript' src='/admin/js/jquery.multisortable.js'></script>
<script type='text/javascript' src='../js/ms_productSearch.js'></script>
<script id='dynamic'></script>
<script language='javascript' src='../display/color/jscolor.js'></script>
<Script Language='JavaScript'>
var bs_basic_air_shipping = '$bs_basic_air_shipping';
var bs_add_air_shipping = '$bs_add_air_shipping';
var duty_rate = '$bs_duty_rate';
var bs_supertax_rate = '$bs_supertax_rate';
var bs_clearance_fee = '$bs_clearance_fee';

$(function() {
    $('#itemBoxWrap').sortable({
        placeholder:'itemBoxHighlight',
        start: function(event, ui) {
            ui.item.data('start_pos', ui.item.index());
        }
    });
    $( '#image_area' ).sortable();
});

jscolor.presets.default = {
	width: 141,               // make the picker a little narrower
	position: 'right',        // position it to the right of the target
	previewPosition: 'right', // display color preview on the right
	previewSize: 40,          // make the color preview bigger
	palette: [
		'#000000', '#7d7d7d', '#870014', '#ec1c23', '#ff7e26',
		'#fef100', '#22b14b', '#00a1e7', '#3f47cc', '#a349a4',
		'#ffffff', '#c3c3c3', '#b87957', '#feaec9', '#ffc80d',
		'#eee3af', '#b5e61d', '#99d9ea', '#7092be', '#c8bfe7',
	],
};

function ChangeOptionName(pid, obj){

	var option_kind = obj[obj.selectedIndex].option_kind;
	document.getElementById('option_kind_value').innerHTML = getOptionKind(option_kind);
	if(option_kind == 'b'){
		document.getElementById('pricebyoption_title').innerHTML = '옵션별가격 *';
		var oobj = document.all.option_kind_view;
		for(i=0;i < oobj.length;i++){
			oobj[i].style.display = 'block';
		}

		var pobj = document.all.option_price_line;
		for(i=0;i < pobj.length;i++){
			pobj[i].style.display = 'block';
		}

	}else if(option_kind == 'p'){
		document.getElementById('pricebyoption_title').innerHTML = '옵션별 추가가격 *';
		var oobj = document.all.option_kind_view;
		for(i=0;i < oobj.length;i++){
			oobj[i].style.display = 'none';
		}

		var pobj = document.all.option_price_line;
		for(i=0;i < pobj.length;i++){
			pobj[i].style.display = 'block';
		}

	}else if(option_kind == 's'){
		document.getElementById('pricebyoption_title').innerHTML = '옵션별가격 *';
		var oobj = document.all.option_kind_view;
		for(i=0;i < oobj.length;i++){
			oobj[i].style.display = 'none';
		}

		var pobj = document.all.option_price_line;
		for(i=0;i < pobj.length;i++){
			pobj[i].style.display = 'none';
		}

	}

	window.forms['optionform'].option_kind.value = option_kind;
	window.frames['act'].location.href='option.act.php?act=view&pid='+pid+'&opn_ix='+obj.value;
	//document.getElementById('act').src='option.act.php?act=view&pid='+pid+'&opn_ix='+obj.value;//kbk
}

function getOptionKind(option_kind){

	if(option_kind == 'b'){
		return '가격재고 관리 옵션';
	}else if(option_kind == 'p'){
		return '가격추가옵션';
	}else if(option_kind == 's'){
		return '선택옵션';
	}else{
		return '';
	}

}

//상품이미지 신규개발 HMPart
var sel_file;
 
$(document).ready(function() {
	$('#productImg').on('change', handleImgFileSelect);
});

function handleImgFileSelect(e) {
	var files = e.target.files;
	var filesArr = Array.prototype.slice.call(files);

	var reg = /(.*?)\/(jpg|jpeg|png|bmp|gif)$/;

	filesArr.forEach(function(f) {
		if (!f.type.match(reg)) {
			alert('이미지만 사용 가능합니다.');
			$('#productImg').val('');
			return;
		}

		/* 이미지 미리보기 
		sel_file = f;

		var reader = new FileReader();
		reader.onload = function(e) {
			$('#imgView').attr('src', e.target.result);
		}
		reader.readAsDataURL(f);
		*/
	});
}

var formData

function imgAdd(imgFrm){
	var fileCheck = $('#productImg').val();

	if(!fileCheck){
		alert('업로드할 파일을 선택하세요.');
		return false;
	}
console.log(imgFrm);
	formData = new FormData(imgFrm);
console.log(formData);
	$.ajax({
		url: './fileupload_ok.php', // url where upload the image
		type : 'POST',
		dataType : 'json',
		enctype : 'multipart/form-data',
		processData : false,
		contentType : false,
		data : formData,
		async : false,
		success : function(datas){
			var ele = document.getElementById('image_area');
			var eleCount = ele.childElementCount;
			eleCount = eleCount + 1;

			var str = '';

			str += '<li id=li_productImage_'+eleCount+' vieworder=' + eleCount + ' viewcnt=' + eleCount + ' style=float:left;width:110px;>';
			str += '<table width=95% cellspacing=1 cellpadding=0 style=table-layout:fixed;height:180px;>';
			//str += '<tr><td class=small style=background-color:gray;color:#ffffff;height:25%;width:100%;text-align:center; nowrap>'+eleCount+' 이미지</td></tr>';
			str += '<tr><td><img src='+datas.dir+'/'+datas.img+' width=100px height=100px>';
			str += '<input type=hidden name=imgName[] id=imgName_'+eleCount+' value='+datas.img+' />';
			str += '<input type=hidden name=imgTemp[] id=imgTemp_'+eleCount+' value='+datas.dir+' />';
			str += '</td></tr>';
			str += '<tr><td align=center><button type=button onclick=ingDel('+eleCount+')>삭제</td></tr>';
			str += '</table>';
			str += '</li>';

			$('#image_area').append(str);

			$('#productImg').val('');
		}
	});
}

function ingDel(imgCnt){
	$.ajax({
		url: './fileupload_ok.php', // url where upload the image
		type : 'POST',
		dataType : 'json',
		data : {'mode':'del', 'imgTemp':$('#imgTemp_'+imgCnt).val(), 'imgName':$('#imgName_'+imgCnt).val()},
		success : function(backData){
			$('#imgInsYN').prop('checked', true);
			$('#li_productImage_'+imgCnt).remove();
		}
	});
}
// //상품이미지 신규개발


function loadCategory(obj,target) {
	
	var trigger = obj.find('option:selected').val();
	var form = obj.closest('form').attr('name');
	var depth = obj.attr('depth');//sel.getAttribute('depth');
	
	$.ajax({ 
			type: 'GET', 
			data: {'return_type': 'json', 'form':form, 'trigger':trigger, 'depth':depth, 'target':target},
			url: './category.load.php',  
			dataType: 'json', 
			async: true, 
			beforeSend: function(){ 
				
			},  
			success: function(datas){
				$('select[class=cid]').each(function(){
					if(parseInt($(this).attr('depth')) > parseInt(depth)){
						$(this).find('option').not(':first').remove();
					}
				});
				 
				if(datas != null){
					$.each(datas, function(i, data){ 
						$('select[name='+target+']').append(\"<option value='\"+data.cid+\"'>\"+data.cname+\"</option>\");
					});  
				}else{
					categoryadd('','select');
				}
			} 
		});  
}

function loadMinishopCategory(obj,target) {
	
	var trigger = obj.find('option:selected').val();
	var form = obj.closest('form').attr('name');
	var depth = obj.attr('depth');//sel.getAttribute('depth');
	
	$.ajax({ 
			type: 'GET', 
			data: {'return_type': 'json', 'form':form, 'trigger':trigger, 'depth':depth, 'target':target, type:'minishop'},
			url: './category.load.php',  
			dataType: 'json', 
			async: true, 
			beforeSend: function(){ 
				
			},  
			success: function(datas){
				$('select[class=minishop_cid]').each(function(){
					if(parseInt($(this).attr('depth')) > parseInt(depth)){
						$(this).find('option').not(':first').remove();
					}
				});
				 
				if(datas != null){
					$.each(datas, function(i, data){ 
						$('select[name='+target+']').append(\"<option value='\"+data.cid+\"'>\"+data.cname+\"</option>\");
					});  
				}
			} 
		});  
}

function StandardLoadCategory(obj,target) {

	var trigger = obj.find('option:selected').val();
	var form = obj.closest('form').attr('name');
	var depth = obj.attr('depth');//sel.getAttribute('depth');

	$.ajax({
			type: 'GET',
			data: {'return_type': 'json', 'form':form, 'trigger':trigger, 'depth':depth, 'target':target},
			url: '../product/standard_category.load.php',
			dataType: 'json',
			async: true,
			beforeSend: function(){

			},
			success: function(datas){
				$('select[class=standard_cid]').each(function(){
					if(parseInt($(this).attr('depth')) > parseInt(depth)){
						$(this).find('option').not(':first').remove();
					}
				});

				if(datas != null){
					$.each(datas, function(i, data){
							$('select[name='+target+']').append(\"<option value='\"+data.cid+\"'>\"+data.cname+\"</option>\");
					});
				}
			}
		});
}

function loadCategory2(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	//var depth = sel.depth; // 호환성 kbk
	var depth = sel.getAttribute('depth');
	//if(depth == 2){
	//document.write('category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	//}
	//alert(trigger);
	//dynamic.src = 'category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target; // 호환성 kbk

	if(sel.selectedIndex!=0) {
		// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
		//document.getElementById('act').src = 'category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		window.frames['act'].location.href = 'category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	}

}

function init()
{
	CKEDITOR.replace('basicinfo',{
		startupFocus : false,height:500
	});

	CKEDITOR.replace('m_basicinfo',{
		startupFocus : false,height:500
	});
";

if(count($language_list) > 0) {
    foreach ($language_list as $key => $li) {
        $Script .= "
        CKEDITOR.replace('".$li['language_code']."_basicinfo',{
		startupFocus : false,height:500
	});

	CKEDITOR.replace('".$li['language_code']."_m_basicinfo',{
		startupFocus : false,height:500
	});
        ";
    }
}

if ($id != ""){
    $db->query("SELECT * FROM  ".TBL_SHOP_PRODUCT_RELATION." where pid = '$id' and basic = '1' " );
    $db->fetch();
    $cid = $db->dt[cid];

    $db->query("SELECT * FROM  shop_product_standard_relation where pid = '$id' and basic = '1' " );
    $db->fetch();
    $standard_cid = $db->dt[cid];

    $db->query("SELECT * FROM ".TBL_SHOP_PRODUCT." where id = $id");

    if($db->total != 0){
        $db->fetch(0);

        $product_info = $db->dt;
        $mall_ix = $db->dt[mall_ix];
        $hotcon_event_id = $db->dt[hotcon_event_id];
        $hotcon_pcode = $db->dt[hotcon_pcode];
        $category_add_infos = json_decode(urldecode($db->dt[category_add_infos]),true);

        $product_color_chip = $db->dt[product_color_chip];
        $pcode = $db->dt[pcode];

        if($pcode){
            $sql = "select gid from inventory_goods_unit where gu_ix = '".$pcode."'";
            $rdb2->query($sql);
            $rdb2->fetch();

            $gid = $rdb2->dt[gid];
        }
        $act = "update";
        $company = $db->dt[company];					//제조사

        $buying_company = $db->dt[buying_company];
        $paper_pname = $db->dt[paper_pname];
        $md_code = $db->dt[md_code];					//담당자
        $trade_admin = $db->dt[trade_admin];			//매입업체
        $barcode = $db->dt[barcode];
        $barcode = str_replace("'","&#39;",trim($barcode));
        $delivery_coupon_yn = $db->dt[delivery_coupon_yn];
        $coupon_use_yn = $db->dt[coupon_use_yn];
        $editdate = $db->dt[editdate];
        $regdate = $db->dt[regdate];

        $state = $db->dt[state];
        $brand = $db->dt[brand];
        $origin = $db->dt[origin];
        $basicinfo = $db->dt[basicinfo];
        $m_basicinfo = $db->dt[m_basicinfo];
        $shotinfo = $db->dt[shotinfo];
        $etc = $db->dt["etc"];

        $pname = str_replace("'","&#39;",trim($db->dt[pname]));
        if($mode == 'copy'){
            $pname = "[복사상품] ".$pname;
        }
        $global_pinfo = json_decode(trim($db->dt[global_pinfo]),true);

        $preface = str_replace("'","&#39;",trim($db->dt[preface]));

		$b_preface = $db->dt[b_preface];
		$i_preface = $db->dt[i_preface];
		$u_preface = $db->dt[u_preface];
		$c_preface = str_replace("'","&#39;",trim($db->dt[c_preface]));

		$listNum = $db->dt[listNum];
		$overNum = $db->dt[overNum];
		$slistNum = $db->dt[slistNum];
		$nailNum = $db->dt[nailNum];
		$pattNum = $db->dt[pattNum];

		$laundry_cid = $db->dt[laundry_cid];

        $sell_ing_cnt = $db->dt[sell_ing_cnt];
        $stock = $db->dt[stock];
        $safestock = $db->dt[safestock];
        $available_stock = $db->dt[available_stock];


        $movie = $db->dt[movie];
        $movie_thumbnail = $db->dt['movie_thumbnail'];
        $movie_now = $db->dt['movie_now'];
        $add_info = $db->dt['add_info'];
        $search_keyword = str_replace("'","&#39;",$db->dt[search_keyword]);
        $disp = $db->dt[disp];
        $wholesale_yn = $db->dt[wholesale_yn];	//도매/소매
        $offline_yn = $db->dt[offline_yn];	//온오프라인 사용유무
        $surtax_yorn = $db->dt[surtax_yorn];
        $supply_company = $db->dt[supply_company];
        $inventory_info = $db->dt[inventory_info];
        $auto_sync_wms = $db->dt[auto_sync_wms];

        $delivery_coupon_yn  = $db->dt[delivery_coupon_yn];
        $cupon_use_yn = $db->dt[cupon_use_yn];

        $delivery_type = $db->dt[delivery_type];
        $delivery_method = $db->dt[delivery_method];
        $product_type = $db->dt[product_type];
        $delivery_company = $db->dt[delivery_company];
        $wholesale_reserve_yn = $db->dt[wholesale_reserve_yn];
        $wholesale_reserve = $db->dt[wholesale_reserve];
        $wholesale_reserve_rate = $db->dt[wholesale_reserve_rate];
        $wholesale_rate_type = $db->dt[wholesale_rate_type];
        $rate_type = $db->dt[rate_type];
        $reserve_yn = $db->dt[reserve_yn];
        $reserve = $db->dt[reserve];
        $reserve_rate = $db->dt[reserve_rate];
        $sns_btn_yn = $db->dt[sns_btn_yn];
        $sns_btn = $db->dt[sns_btn];

        $stock_use_yn = $db->dt[stock_use_yn];
        $buyingservice_coprice = $db->dt[buyingservice_coprice];
        $wholesale_price = $db->dt[wholesale_price];
        $wholesale_sellprice = $db->dt[wholesale_sellprice];

        $coprice = $db->dt[coprice];
        $sellprice = $db->dt[sellprice];
        $premiumprice = $db->dt[premiumprice];
        $ap_price = $db->dt[ap_price];
        $listprice = $db->dt[listprice];
        $bimg_text = $db->dt[bimg];
        $admin = $db->dt[admin];

        /*사은품 금액영역*/
        $gift_sprice = $db->dt[gift_sprice];
        $gift_eprice = $db->dt[gift_eprice];
        $gift_qty = $db->dt[gift_qty];
        $gift_selectbox_cnt = $db->dt[gift_selectbox_cnt];
		$gift_selectbox_nooption_yn = $db->dt[gift_selectbox_nooption_yn];
        /*종료*/

        $one_commission = $db->dt[one_commission];
        $goods_commission = $db->dt[commission];
        $goods_wholesale_commission = $db->dt[wholesale_commission];	// 이학봉 2013-10-29 셀러관련
        if($one_commission == "Y"){
            $commission = $db->dt[commission];
            $wholesale_commission = $db->dt[wholesale_commission];	// 이학봉 2013-10-29 셀러관련
        }
        $account_type = $db->dt[account_type];						// 이학봉 2013-10-29 셀러관련

        $delivery_policy = $db->dt[delivery_policy];
        if($delivery_policy == ""){
            $delivery_policy = 2;
        }
        $delivery_product_policy = $db->dt[delivery_product_policy];

        $delivery_package = $db->dt[delivery_package];
        $delivery_price = $db->dt[delivery_price];
        $free_delivery_yn = $db->dt[free_delivery_yn];
        $free_delivery_count = $db->dt[free_delivery_count];
        $bs_goods_url = $db->dt[bs_goods_url];
        $bs_site = $db->dt[bs_site];
        $currency_ix = $db->dt[currency_ix];
        $price_policy = $db->dt[price_policy];

        $sell_priod_sdate = $db->dt[sell_priod_sdate];
        $sell_priod_edate = $db->dt[sell_priod_edate];
        $allow_order_type = $db->dt[allow_order_type];
        $allow_order_cnt_byonesell = $db->dt[allow_order_cnt_byonesell];
        $allow_order_cnt_byoneperson = $db->dt[allow_order_cnt_byoneperson];
        $orgin = $db->dt[orgin];
        $make_date = $db->dt[make_date];
        $expiry_date = $db->dt[expiry_date];

        $substitude_yn = $db->dt[substitude_yn];
        $substitude_total = $db->dt[substitude_total];
        $substitude_seller = $db->dt[substitude_seller];
        $substitude_rate = $db->dt[substitude_rate];

        $mandatory_array = explode("|",$db->dt[mandatory_type]);	//상품고시가 substr 제대로 처이 안대서 저장시 | 구분값을 추가하고 | 로 구분하여 처리하였음	2013-06-05 이학봉
        $mi_code = $mandatory_array[0];
        $mandatory_type_2 = $mandatory_array[1];
        $mandatory_type_3 = $mandatory_array[2];

        $mandatory_array_global = explode("|",$db->dt[mandatory_type_global]);	//글로벌 정보
        $mi_code_global = $mandatory_array_global[0];
        $mandatory_type_2_global = $mandatory_array_global[1];
        $mandatory_type_3_global = $mandatory_array_global[2];

        $download_img = $db->dt[download_img];
        $download_desc = $db->dt[download_desc];

        $cupon_use_yn = $db->dt[cupon_use_yn];
        $relation_display_type= $db->dt[relation_display_type];
        $relation_text1 = $db->dt[relation_text1];
        $relation_text2 = $db->dt[relation_text2];
        $relation_product_cnt = $db->dt[relation_product_cnt];

        $soho = $db->dt[soho];
        $designer = $db->dt[designer];
        $mirrorpick = $db->dt[mirrorpick];


        $style_info = json_decode($db->dt[style]);
        $tag_info = json_decode($db->dt[tag]);

        //개별상품 등록 업그레이드후 새로 추가된 컬럼 2014-01-29 이학봉
        $is_adult = $db->dt[is_adult];									//190금 사용여부
        $remain_stock = $db->dt[remain_stock];							//남은 가용재고
        $is_sell_date = $db->dt[is_sell_date];							//판매기간 사용여부
        $wholesale_allow_max_cnt = $db->dt[wholesale_allow_max_cnt];	//최대 판매 수량(도매)
        $allow_max_cnt = $db->dt[allow_max_cnt];						//최대 판매수량(소매)
        $wholesale_allow_basic_cnt = $db->dt[wholesale_allow_basic_cnt];//기본 시작 수량(도매)
        $allow_basic_cnt = $db->dt[allow_basic_cnt];					//기본 시작 수량(소매)

        $allow_byoneperson_cnt = $db->dt[allow_byoneperson_cnt];					//ID당 구매수량(소매)
        $wholesale_allow_byoneperson_cnt = $db->dt[wholesale_allow_byoneperson_cnt];	//ID당 구매수량(도매)

        $md_one_commission = $db->dt[md_one_commission];						//md 상품개별설정
        $md_discount_name = $db->dt[md_discount_name];							//md 할인 이벤트명
        $md_sell_date_use = $db->dt[md_sell_date_use];							//md 할인 기간설정
        $md_sell_priod_sdate = $db->dt[md_sell_priod_sdate];					//md 할인 기간 시작
        $md_sell_priod_edate = $db->dt[md_sell_priod_edate];					//md 할인 기간 끝
        $whole_head_company_sale_rate = $db->dt[whole_head_company_sale_rate];	//md 도매할인율 본사부담
        $whole_seller_company_sale_rate = $db->dt[whole_seller_company_sale_rate];	//md 도매할인율 셀러부담
        $head_company_sale_rate = $db->dt[head_company_sale_rate];					//md 소매할인율 본사부담
        $seller_company_sale_rate = $db->dt[seller_company_sale_rate];				//md 소매할인율 셀러부담

        $c_ix = $db->dt[c_ix];	//제조사 코드
        $dt_ix_retail = $db->dt[dt_ix_retail];	//배송정책 템플릿 값(소매)
        $dt_ix_whole = $db->dt[dt_ix_whole];	//배송정책 템플릿 값(도매)

        //개별상품 등록 업그레이드후 새로 추가된 컬럼 2014-01-29 이학봉

        $is_mobile_use = $db->dt[is_mobile_use];		//모바일 사용유무

        $product_weight = $db->dt[product_weight];		//무게KG

        $etc1 = $db->dt[etc1];
        $etc2 = $db->dt[etc2];
        $etc3 = $db->dt[etc3];
        $etc4 = $db->dt[etc4];
        $etc5 = $db->dt[etc5];
        $etc6 = $db->dt[etc6];
        $etc7 = $db->dt[etc7];
        $etc8 = $db->dt[etc8];
        $etc9 = $db->dt[etc9];
        $etc10 = $db->dt[etc10];
        $vieworder = $db->dt[vieworder];
        $mandatory_use = $db->dt[mandatory_use];
        $mandatory_use_global = $db->dt['mandatory_use_global'];

        $exchangeable_yn = $db->dt[exchangeable_yn];
        $returnable_yn = $db->dt[returnable_yn];
        $admin_memo = $db->dt[admin_memo];
        $wear_info = $db->dt[wear_info];

        if($product_type == "2"){
            $sql = "select * from shop_product_auction where pid = '$id' ";
            $adb->query($sql);
            $adb->fetch();
            $FromYY = substr($adb->dt[startdate],0,4);
            $FromMM = substr($adb->dt[startdate],5,2);
            $FromDD = substr($adb->dt[startdate],8,2);
            $FromHH = substr($adb->dt[startdate],11,2);
            $FromII = substr($adb->dt[startdate],14,2);
            $ToYY = substr($adb->dt[plusdate],0,4);
            $ToMM = substr($adb->dt[plusdate],5,2);
            $ToDD = substr($adb->dt[plusdate],8,2);
            $ToHH = substr($adb->dt[plusdate],11,2);
            $ToII = substr($adb->dt[plusdate],14,2);
            $startprice = $adb->dt[startprice];
            $plus_count = $adb->dt[plus_count];

            $car_defailt_validation = "false";
            $realestate_defailt_validation = "false";
            $hotel_default_validation = "false";
            $tour_default_validation="false";

        }else if($product_type == "7"){
            $sql = "select * from shop_product_car where pid = '$id'";
            $db->query($sql);
            if($db->total){
                $db->fetch();

                $vechile_div= $db->dt[vechile_div];
                $mf_ix= $db->dt[mf_ix];
                $md_ix=$db->dt[md_ix];
                $gr_ix=$db->dt[gr_ix];
                $vt_ix=$db->dt[vt_ix];
                $vintage=$db->dt[vintage];
                $mileage=$db->dt[mileage];
                $displacement=$db->dt[displacement];
                $transmission=$db->dt[transmission];
                $color=$db->dt[color];
                $fuel=$db->dt[fuel];
                $license_plate=$db->dt[license_plate];
                $car_condition=$db->dt[car_condition];
            }
            $car_defailt_validation = "true";
            $realestate_defailt_validation = "false";
            $hotel_default_validation = "false";
            $tour_default_validation="false";
        }else if($product_type == "8"){

            $sql = "select * from shop_product_property where pid = '$id'";
            $db->query($sql);
            if($db->total){
                $db->fetch();

                $rg_ix= $db->dt[rg_ix];

                $dimensions=$db->dt[dimensions];
                $deal_type=$db->dt[deal_type];
                $property_type=$db->dt[property_type];
                $loans=$db->dt[loans];
                $maintenance_cost=$db->dt[maintenance_cost];
                $heating_fuel=$db->dt[heating_fuel];
                $posisbile_date=$db->dt[posisbile_date];

                $sql = 	"SELECT cr.*
							FROM shop_realestate_region cr
							where rg_ix = '$rg_ix'  ";

                $db->query($sql);
                $db->fetch();

                if($db->dt[depth] == 2){
                    $parent_rg_ix = $db->dt[parent_rg_ix];
                }
            }

            $car_defailt_validation = "false";
            $realestate_defailt_validation = "true";
            $hotel_default_validation = "false";
            $tour_default_validation="false";

        }else if($product_type == "9"){

            $sql = "select * from shop_product_hotel where pid = '$id'";
            $db->query($sql);
            if($db->total){
                $db->fetch();

                $hotel_rg_ix= $db->dt[rg_ix];
                $hotel_level=$db->dt[hotel_level];
                $room_level=$db->dt[room_level];

                $sql = 	"SELECT cr.*
							FROM shop_realestate_region cr
							where rg_ix = '$hotel_rg_ix'  ";

                $db->query($sql);
                $db->fetch();

                if($db->dt[depth] == 2){
                    $parent_hotel_rg_ix = $db->dt[parent_rg_ix];
                }
            }

            $car_defailt_validation = "false";
            $realestate_defailt_validation = "false";
            $hotel_default_validation = "true";
            $tour_default_validation="false";

        }else if($product_type == "10"){

            $sql = "select * from shop_product_sightseeing where pid = '$id'";
            $db->query($sql);

            if($db->total){
                $db->fetch();

                $sightseeing_rg_ix= $db->dt[rg_ix];
                $sql = 	"SELECT cr.*
							FROM shop_realestate_region cr
							where rg_ix = '$sightseeing_rg_ix'  ";
                $db->query($sql);
                $db->fetch();

                if($db->dt[depth] == 2){
                    $parent_sightseeing_rg_ix = $db->dt[parent_rg_ix];
                }
            }

            $car_defailt_validation = "false";
            $realestate_defailt_validation = "false";
            $hotel_default_validation = "false";
            $tour_default_validation="true";

        }else{
            $car_defailt_validation = "false";
            $realestate_defailt_validation = "false";
            $hotel_default_validation = "false";
            $tour_default_validation="false";
        }

        if ($FromYY == "" || $FromYY == "0000"){
            $after10day = mktime(date('H'), date('i'), 0, date("m")  , date("d")+20, date("Y"));
            $sDate = date("Y/m/d/H/i" );
            $eDate = date("Y/m/d/H/i",$after10day);
            $vintage = date("Y/m" );
            $startDate = date("YmdHi");
            $endDate = date("YmdHi",$after10day);
        }else{
            $sDate = $FromYY."/".$FromMM."/".$FromDD."/".$FromHH."/".$FromII;
            $eDate = $ToYY."/".$ToMM."/".$ToDD."/".$ToHH."/".$ToII;
            $startDate = $FromYY.$FromMM.$FromDD.$FromHH.$FromII;
            $endDate = $ToYY.$ToMM.$ToDD.$ToHH.$ToII;
        }

        $Script=$Script. "

			";

        $icons = explode(";",$db->dt["icons"]);
        for($i=0;$i<count($icons);$i++){
            $icons_checked[$icons[$i]] = "1";
        }

        //글로벌 관련 변수 생성 처리
        $sql = "select * from shop_product_global where id = '$id'";
        $db->query($sql);
        $db->fetch();

        $global_param = array('pname', 'preface', 'add_info', 'search_keyword', 'basicinfo', 'm_basicinfo', 'coprice', 'listprice', 'sellprice', 'relation_text1', 'relation_text2');
        foreach($language_list as $l){
            foreach($global_param as $_param){
                ${$l['language_code'].'_'.$_param} = $db->dt[$_param];
            }
        }
    }




}else{
    $disp = "1";
    $act = "insert";
    $vintage = date("Y/m" );
    $admin = $_SESSION["admininfo"]["company_id"];

    $car_defailt_validation = "false";
    $realestate_defailt_validation = "false";
    $hotel_default_validation = "false";

    $relation_display_type = "M";
    $display_auto_type = "order_cnt";
    $available_stock = 0;

    $surtax_yorn = "N";
    $is_mobile_use = "A";
    $auto_sync_wms = "Y";

    if ($FromYY == ""){

        $after10day = mktime(date('H'), date('i'), 0, date("m")  , date("d")+20, date("Y"));

        //	$sDate = date("Y/m/d");
        $sDate = date("Y/m/d/H/i" );
        $eDate = date("Y/m/d/H/i",$after10day);

        $startDate = date("YmdHi");
        $endDate = date("YmdHi",$after10day);
    }else{
        $sDate = $FromYY."/".$FromMM."/".$FromDD."/".$FromHH."/".$FromII;
        $eDate = $ToYY."/".$ToMM."/".$ToDD."/".$ToHH."/".$ToII;
        $startDate = $FromYY.$FromMM.$FromDD.$FromHH.$FromII;
        $endDate = $ToYY.$ToMM.$ToDD.$ToHH.$ToII;
    }

    // 기본 브랜드 세팅
    $sql = "select seller_brand from common_seller_detail where company_id = '".$admininfo[company_id]."'";
    $db->query($sql);
    $db->fetch();
    $brand = $db->dt[seller_brand];
}

if($_SESSION["admininfo"][admin_level] == 9 && ($_SESSION["admininfo"][mall_type] == "O" || $_SESSION["admininfo"][mall_type] == "E")){
    $Script .="
			onLoad('$sDate','$eDate');";
}

$Script .="
}

function onDropAction(mode, pid,rp_pid)
{
	//outTip(img3);
	//alert(1);
	//parent.document.frames['act'].location.href='./relation.category.act.php?mode='+mode+'&pid='+pid+'&rp_pid='+rp_pid;
	//parent.document.getElementById('act').src='./relation.category.act.php?mode='+mode+'&pid='+pid+'&rp_pid='+rp_pid;
	parent.window.frames['act'].location.href='./relation.category.act.php?mode='+mode+'&pid='+pid+'&rp_pid='+rp_pid;

}
//var cate = new Array();";
if($_SESSION["admininfo"][admin_level] == 9){
    $Script=$Script. "
function init_date(FromDate,ToDate) {
	var frm = document.product_input;


	for(i=0; i<frm.FromYY.length; i++) {
		if(frm.FromYY.options[i].value == FromDate.substring(0,4))
			frm.FromYY.options[i].selected=true
	}
	for(i=0; i<frm.FromMM.length; i++) {
		if(frm.FromMM.options[i].value == FromDate.substring(5,7))
			frm.FromMM.options[i].selected=true
	}
	for(i=0; i<frm.FromDD.length; i++) {
		if(frm.FromDD.options[i].value == FromDate.substring(8,10))
			frm.FromDD.options[i].selected=true
	}
	for(i=0; i<frm.FromHH.length; i++) {
		if(frm.FromHH.options[i].value == FromDate.substring(11,13))
			frm.FromHH.options[i].selected=true
	}
	for(i=0; i<frm.FromII.length; i++) {
		if(frm.FromII.options[i].value == FromDate.substring(14,16))
			frm.FromII.options[i].selected=true
	}

	for(i=0; i<frm.ToYY.length; i++) {
		if(frm.ToYY.options[i].value == ToDate.substring(0,4))
			frm.ToYY.options[i].selected=true
	}
	for(i=0; i<frm.ToMM.length; i++) {
		if(frm.ToMM.options[i].value == ToDate.substring(5,7))
			frm.ToMM.options[i].selected=true
	}
	for(i=0; i<frm.ToDD.length; i++) {
		if(frm.ToDD.options[i].value == ToDate.substring(8,10))
			frm.ToDD.options[i].selected=true
	}
	for(i=0; i<frm.ToHH.length; i++) {
		if(frm.ToHH.options[i].value == ToDate.substring(11,13))
			frm.ToHH.options[i].selected=true
	}
	for(i=0; i<frm.ToII.length; i++) {
		if(frm.ToII.options[i].value == ToDate.substring(14,16))
			frm.ToII.options[i].selected=true
	}
	for(i=0; i<frm.vintage_year.length; i++) {
		if(frm.vintage_year.options[i].value == ToDate.substring(0,4))
			frm.vintage_year.options[i].selected=true
	}
	for(i=0; i<frm.vintage_month.length; i++) {
		if(frm.vintage_month.options[i].value == ToDate.substring(5,7))
			frm.vintage_month.options[i].selected=true
	}

}

function onLoad(FromDate, ToDate) {
	var frm = document.product_input;";
    if($_SESSION["admininfo"][admin_level] == 9 && ($_SESSION["admininfo"][mall_type] == "O" || $_SESSION["admininfo"][mall_type] == "E") ){
        $Script .= "
	//LoadValuesAuction(frm.FromYY, frm.FromMM, frm.FromDD,frm.FromHH,frm.FromII, FromDate);
	//LoadValuesAuction(frm.ToYY, frm.ToMM, frm.ToDD,frm.ToHH,frm.ToII, ToDate);";

        $Script .= "
	LoadValues(frm.vintage_year, frm.vintage_month, null, '".str_replace("-","/",$vintage)."');
	init_date(FromDate,ToDate);";
    }
    $Script .= "
}";
}
$Script=$Script. "
//search_type
function standard_category_add()
{
	var ret;
	var str = new Array();
	var dupe_bool = false;
	var obj = $('form[name=product_input]').find('select[class^=standard_cid]');
	var admin_level = '".$_SESSION["admininfo"]["admin_level"]."';

	/*if(admin_level == 8){
		if($('input[type=radio][name=basic]').length > 0){
			alert('카테고리 입력은 한개만 가능합니다. ');
			return false;
		}
	}*/

	obj.each(function(index){
		if($(this).find('option:selected').val()){
			str[str.length] =  $(this).find('option:selected').text();
			ret = $(this).find('option:selected').val();
		}
	});

	if (!ret){
		alert(language_data['goods_input.php']['A'][language]);//'카테고리를 선택해주세요'
		return;
	}

	var cate = $('input[name^=display_standard_category]');//document.getElementsByName('display_standard_category[]'); // 호환성 kbk

	cate.each(function(){
		if(ret == $(this).val()){
			dupe_bool = true;
			alert(language_data['goods_input.php']['B'][language]);
			//'이미등록된 카테고리 입니다.'
			return;
		}
	});
	if(dupe_bool){
		return ;
	}

	var obj = $('#objStandardCategory');

	var selected_category_length = obj.find('tr').length;
	if(selected_category_length == 0){
		var input_str = \"<input type=radio name=standard_basic id='standard_basic_\"+ ret + \"' value='\"+ ret + \"' validation=true title='기본카테고리' checked>\";
	}else{
		var input_str = \"<input type=radio name=standard_basic id='standard_basic_\"+ ret + \"' value='\"+ ret + \"' validation=true title='기본카테고리' >\";
	}

	obj.append(\"<tr id='num_tr' height=30 class=''><td><input type=text name=display_standard_category[] id='_category' value='\" + ret + \"' style='display:none'></td><td id='currPosition'>\"+input_str+\"</td><td><label for='standard_basic_\"+ret+\"'>\"+str.join(\" > \")+\"</label></td><td><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0 onClick=\\\" $(this).closest('tr').remove();\\\" style='cursor:point;'></td></tr>\");

	if(selected_category_length == 0){
		get_standard_category_add_infomation(ret);
		get_lazada_options( ret );
	}
	
	$('#objCategory').find('input[id^=basic_]').click(function(){
		get_standard_category_add_infomation($(this).val());
	});
}


$(document).ready(function(){
	get_lazada_options( $('input[name=standard_basic]:checked').val() );
})

function get_lazada_options( selected_category_cid ){
	if( $('input[name=\"partner_prd_reg[]\"][value=lazada]:checked').length ){
		$.ajax({
			url : '../product/goods_input.act.php',
			type : 'POST',
			data : {cid:selected_category_cid, act:'get_lazada_options'},
			dataType: 'json',
			error: function(data,error){// 실패시 실행함수
					alert(error);
			},
			success: function(datas){
				
				$('#opnt_ix_lazada').remove();
				$('#opnt_ix_lazada_box').remove();

				//초기화
				if(datas != null){
					var opts = datas.opt.split('|');
					if( opts.length > 0 ){

						opts.sort();

						html = '<div id=\"opnt_ix_lazada\" opnt_ix=\"lazada\" option_type=\"s\" class=\"make_option\" option_selected=\"0\" style=\"float:left;width:90px;border:1px solid silver;background-color:#efefef;padding:5px;margin:3px 3px 3px 0px;cursor:pointer;text-align:center;\" onclick=\"selectTmpOption(\'lazada\')\">라자다옵션</div>';

						$('#favorites_options_area').prepend( $(html) );
						
					
						html = '<div id=\"opnt_ix_lazada_box\" style=\"clear: both; width: 100%; border: 0px solid blue; display: none;\"><div id=\"opnt_first_area_lazada\" title=\"lazada\" style=\"float:left;width:90px;border:1px solid silver;background-color:#efefef;padding:5px;margin:3px 3px 3px 0px;\">라자다<span style=\"width:30px;\">&nbsp;</span><a href=\"javascript:selectTmpOptionDetailAll(\'lazada\');\"><b style=\"font-size:12px;\">ALL</b></a>&nbsp; <a href=\"javascript:tmpOptionDetailAdd(\'lazada\');\"><b style=\"font-size:12px;\">+</b></a></div>';

						for(i=0; i < opts.length; i++){
							html += '<div id=\"opndt_ix_lazada_'+ i +'\" opnt_ix=\"lazada\" opndt_ix=\"lazada_'+ i +'\" class=\"make_option_detail_lazada\" option_detail_selected=\"0\" style=\"float:left;width:90px;border:1px solid silver;background-color:#ffffff;padding:5px;margin:3px;cursor:pointer;\" onclick=\"selectTmpOptionDetail(\'lazada_'+ i +'\')\">'+opts[i]+'</div>';
						}

						html += '</div>';
						$('#favorites_options_area').after( $(html) );
					}
				}
			}
		});
	}
}

function standard_category_del(el)
{

	idx = el.rowIndex;
	var obj = document.getElementById('objStandardCategory');
	obj.deleteRow(idx);
	var cObj=\$('input[name=basic]');
	var cObj_num=0;
	if(cObj.length == null){
		//cObj[0].checked = true; // 0이 나오지 null이 나오지 않음 kbk
	}else{
		for(var i=0;i<cObj.length;i++){
			if(cObj[i].checked){
				cObj_num++;
			}
		}
		if(cObj_num==0) {
			cObj[0].checked = true;
		}
	}
	//cate.splice(idx,1);
}

function categoryadd(type, add_type)
{
	if(type == 'M'){
		//미니샵용
		var mLayer = 'minishop_';
	}else{
		var mLayer = '';
	}

	if(add_type == 'search'){
		var table_id = 'search_category';
	}else{
		var table_id = 'select_category';
	}
	var ret;
	var str = new Array();
	var dupe_bool = false;
	var obj = $('form[name=product_input]').find('table[id='+table_id+'] select[class^='+mLayer+'cid]');
	var admin_level = '".$_SESSION["admininfo"]["admin_level"]."';

	if(type != 'M'){
		if(admin_level == 8){
			if($('input[type=radio][name='+mLayer+'basic]').length > 0){
				alert('카테고리 입력은 한개만 가능합니다. ');
				return false;
			}
		}
	}

	obj.each(function(index){
		if($(this).find('option:selected').val()){
			str[str.length] =  $(this).find('option:selected').text();
			ret = $(this).find('option:selected').val();
		}
	});

	if (!ret){
		alert(language_data['goods_input.php']['A'][language]);//'카테고리를 선택해주세요'
		return;
	}

	var cate = $('input[name^=display_category]');//document.getElementsByName('display_category[]'); // 호환성 kbk

	cate.each(function(){
		if(ret == $(this).val()){
			dupe_bool = true;
			alert(language_data['goods_input.php']['B'][language]);
			//'이미등록된 카테고리 입니다.'
			return;
		}
	});

	if(dupe_bool){
		return ;
	}

	var obj = $('#'+mLayer+'objCategory');
	var selected_category_length = obj.find('tr').length;
	if(selected_category_length == 1){
		var input_str = \"<input type=radio name=\"+mLayer+\"basic id='\"+mLayer+\"basic_\"+ ret + \"' value='\"+ ret + \"' validation=true title='기본카테고리' checked>\";	
	}else{
		var input_str = \"<input type=radio name=\"+mLayer+\"basic id='\"+mLayer+\"basic_\"+ ret + \"' value='\"+ ret + \"' validation=true title='기본카테고리' >\";
	}

	obj.append(\"<tr id='num_tr' height=30 class=''><td width=5><input type=text name=\"+mLayer+\"display_category[] id='_category' value='\" + ret + \"' style='display:none'></td><td id='currPosition' width=50>\"+input_str+\"</td><td><label for='basic_\"+ret+\"'>\"+str.join(\" > \")+\"</label></td><td width=100><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0 onClick=\\\" category_del(this.parentNode.parentNode);l\\\" style='cursor:point;'></td></tr>\");//$(this).closest('tr').remove();
	 
	if(selected_category_length == 0){
		get_category_add_infomation(ret);
	}
	
	$('#objCategory').find('input[id^=basic_]').click(function(){
		get_category_add_infomation($(this).val());
	});
}

function get_category_add_infomation(cid){ 
	$.ajax({
			url : '../product/goods_input.act.php',
			type : 'POST',
			data : {cid:cid, act:'get_category_addinfo'},
			dataType: 'html',
			error: function(data,error){// 실패시 실행함수
					alert(error);
			},
			success: function(html){
				$('div#cateogry_add_infomation_area').html(html);
			}
		});
}


function get_standard_category_add_infomation(standard_cid){ 
	//alert(standard_cid);
	var sites = $('input[name^=partner_prd_reg]:checked').serialize();
	//alert(sites);
	$.ajax({
			url : '../product/goods_input.act.php',
			type : 'POST',
			data : {standard_cid:standard_cid, sites:sites, act:'get_standarad_category_addinfo'},
			dataType: 'html',
			error: function(data,error){// 실패시 실행함수
					alert(error);
			},
			success: function(html){
				//alert(html);
				$('div#standard_cateogry_add_infomation_area').html(html);
			}
		});
}

function category_del(el)
{

	idx = el.rowIndex;
	var obj = $('#objCategory');
	//obj.deleteRow(idx);
	$(el).remove();
	var cObj=\$('input[name=basic]');
	//var cObj_num=0;
	//alert(cObj.length);
	if(cObj.length == 0){
		$('div#cateogry_add_infomation_area').html(''); 
	}else{ 
			$('input[name=basic]:first').attr('checked','checked'); 
			//alert($('input[name=basic]:first').val());
			get_category_add_infomation($('input[name=basic]:first').val());
	} 
}

$(document).ready(function() {
	$('#objCategory').find('input[id^=basic_]').click(function(){
		get_category_add_infomation($(this).val());
	});
});
</Script>";
$Contents = "
	<span class='side-btn'>
		<a href='javascript:void(0);'><img src='../v3/images/btn/bt_page_up.gif' alt='up' /></a>
		<a href='javascript:void(0);'><img src='../v3/images/btn/bt_page_down.gif' alt='down' /></a>
	</span>
	<table cellpadding=0 width=100%>
		<tr>
		    <td align='left' colspan=4 > ".GetTitleNavigation("개별상품등록", "상품관리 > 개별상품등록")."</td>
		</tr>";
if($_SESSION["admininfo"][charger_id] == "forbiz" && false){
    $Contents .= "
		<tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    	<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' class='on' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='goods_input.php'\">일반 상품 등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='goods_input_quick.php'\">빠른 상품 등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td class='btn'>
						</td>
					</tr>
					</table>
				</div>
		</td>
	</tr>";
}
$Contents .= "
	</table>";
$Contents .= "
			<!--form name='product_input' action='product_input.act.php' method='post' enctype='multipart/form-data'-->
			<form name='product_input' id='product_input' action='../product/goods_input.act.php' method='post' enctype='multipart/form-data' onsubmit='return SubmitX(this);' target='iframe_act'><!--iframe_act-->
			<input type='hidden' name=act value='insert'>
			<!--<input type='hidden' name=admin value='".$admin."'>-->
			<input type='hidden' name=id value='".$id."'>
			<input type='hidden' name=bpid value='".$id."'>
			<input type='hidden' name=mmode value='".$mmode."'>
			<input type='hidden' name=mode value='".$mode."'>
			<input type='hidden' name=etc8 value='".$etc8."'>
			<input type='hidden' name=etc9 value='".$etc9."'>
			<input type='hidden' name=vieworder value='".$vieworder."'>
			
			<table width=100%>
			<tr height=30 align=left>
				<td width=500>";
if($id){
//$Contents .= "<a href=\"/shop/goods_view.php?id=".$id."\" target=_blank><img src='../v3/images/btn/bt_preview.png' border=0 align=absmiddle style='cursor:pointer'></a>";
} else {
    if(substr_count($_SERVER["HTTP_USER_AGENT"],"Mobile")){
        $Contents .= "<input type=\"button\" value=\"업로드하기\" onclick=\"sendMessage('upload')\"/>";
    }
}

$Contents .= "  <!--a href=\"JavaScript:PoPWindow('/shop/goods_view.php?id=".$id."',980,800,'comparewindow');\">보기</a--></td>";
if ($id == "" || $mode == "copy"){
    $Contents .= "<td align=right>";
    if(checkMenuAuth($page_code,"C")){
        $Contents .= "<span class='helpcloud' help_height='30' help_html='상품정보 저장후 현재 페이지가 유지됩니다.'>
							<img src='../images/".$_SESSION["admininfo"]["language"]."/btn_save_tmp.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'tmp_insert');\">
							</span> ";
        //bt_save.png
        $Contents .= "<img src='../images/".$_SESSION["admininfo"]["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'insert');\">
		<!--img src='../v3/images/btn/bt_save.png' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'insert');\"-->";
    }
    $Contents .= "</td>";
}else{
    $Contents .= "<td align=right>";
    if($id){
        $Contents .= "
				<a href='".$client_host."' target=_blank><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_preview.gif' border=0 align=absmiddle style='cursor:pointer;margin-right:5px;'></a>";
    }
    if(checkMenuAuth($page_code,"U")){
        $Contents .= "<span class='helpcloud' help_height='30' help_html='상품정보 저장후 현재 페이지가 유지됩니다.'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_save_tmp.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'tmp_update');\"></span> ";
        $Contents .= "<img src='../images/".$_SESSION["admininfo"]["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'update')\"> ";
    }
    if(checkMenuAuth($page_code,"D") && $state==9 ){
        $Contents .= "<img src='../images/".$_SESSION["admininfo"]["language"]."/b_del.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'delete')\">";
    }
    $Contents .= "</td>";
}

$Contents = $Contents."
			</tr>
			</table>";
$Contents .= "<div class='targetMenu'>
                <a href='#categoryAdd' class='mGift'>카테고리등록</a> 
                <a href='#mdSet'>MD 설정</a> 
                <a href='#basicInfo'>기본정보</a> 
                <a href='#addInfo'>추가정보</a> 
                <a href='#filterInfo' class='mGift'>필터정보</a>
                <a href='#mandatory' class='mGift'>상품고시정보</a>
                <a href='#priceInfo' class='mGift'>가격정보</a>
                <a href='#productDeliverySet' class='mGift'>상품별배송정책설정</a>
                <a href='#priceStock' class='mNormal mGift'>가격+재고관리</a>
                <a href='#codiProduct' class='mGift mSet'>코디상품옵션</a>
                <a href='#display' class='mGift'>디스플레이옵션</a>
                <a href='#colorChip1' class='mGift'>colorChip1</a>
                <a href='#colorChip2' class='mGift'>colorChip2</a>
                <a href='#addProduct' class='mGift'>추가구성상품</a>
                <a href='#addGift' class='mGift'>사은품등록</a>
                <a href='#productInfo' class='mGift'>상품상세정보</a>
                <a href='#image'>이미지추가</a>
                <a href='#addImage'>추가이미지등록</a>
              </div> ";
if(($_SESSION["admininfo"][mall_type] == "O" || $_SESSION["admininfo"][mall_type] == "E")){

    if($goods_input_type == "social"){

        $Contents .= $SocialHelp_text;

        $Contents = $Contents."<table cellspacing=0 cellpadding=0 border=0 width='100%'>
			<tr bgcolor='#cccccc' height='36'>
					<td bgcolor=\"#efefef\" width=120 style='padding-left:10px;'>
						<b style='font-size:15px;'>상품구분</b>
					</td>
					<td colspan=3 bgcolor=\"#efefef\" align='left'>
						<table border=0 cellpadding=0 cellspacing=0 width=550>
							<tr>
								<td>
									<input type='radio' name='product_type' id='product_type_4' value='4' ".(($product_type == '4' || $product_type == '') ? ' checked':'')." onclick=\"ShowGoodsTypeInfo('scGoodsInfo');\"> <label for='product_type_4'>소셜커머스(배송상품)</label->
									
									<!--
									<input type='radio' name='product_type' id='product_type_5' value='5' ".(($product_type == '5') ? ' checked':'')." onclick=\"ShowGoodsTypeInfo('CouponInfo');\"> <label for='product_type_5'>쿠폰상품</label>

									<input type='radio' name='product_type' id='product_type_6' value='6' ".(($product_type == '6') ? ' checked':'')." onclick=\"ShowGoodsTypeInfo('TravelInfo');\"> <label for='product_type_6'>여행상품</label>
									-->

									<input type='radio' name='product_type' id='product_type_21' value='21' ".(($product_type == '21') ? ' checked':'')." onclick=\"ShowGoodsTypeInfo('subscription');\"> <label for='product_type_21'>서브스크립션 커머스(배송상품)</label>";
        if($product_type == '') {//소셜커머스 상품을 숨김으로써 상품 정보 입력일 때 배송상품 스크립트 실행 kbk 13/09/11

        }
        $Contents.="<input type='radio' name='product_type' id='product_type_31' value='31' ".(($product_type == '31') ? ' checked':'')." onclick=\"ShowGoodsTypeInfo('local_delivery');\"> <label for='product_type_31'>로컬딜리버리 커머스(배송상품)</label>
								</td>

							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height='3'></td>
				</tr>
			</table><br>";
    }else{

        $Contents .= "<table cellspacing=0 cellpadding=0 border=0 width='100%' ".($_SESSION["admininfo"]["admin_level"] == "8" ? "style='display:none;'":"" ).">
			<tr bgcolor='#cccccc' height='36'>
					<td bgcolor=\"ffffff\" width=120 style='padding-left:15px;'>
						<b class=blk style='font-size:15px;'>상품구분</b>
					</td>
					<td colspan=3 bgcolor=\"#ffffff\" align='left'>
						<table border=0 cellpadding=0 cellspacing=0 width=700>
							<tr>
								<td>
									<input type=radio name=product_type id='product_type_0' class='product_type' value='0' ".($product_type == "0" || $product_type == "" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('GoodsInfo');\" ".(($id && $product_type != '0' && $product_type != '12') ? "disabled":"")."> <label for='product_type_0' >일반상품 </label>
									<input type=radio name=product_type id='product_type_55'  value='55' ".($product_type == "55" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('sos');\" ".(($id && $mode!='copy' && $product_type != '55') ? "disabled":"")."> <label for='product_type_55' >SOS 티켓</label>";
        $Contents .= "			<input type=radio name=product_type id='product_type_99' class='product_type' value='99' ".($product_type == "99" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('setGoodsInfo'".(($id) ? ",'Y'":"").");\" ".(($id && $product_type != '99') ? "disabled":"").">
									<label for='product_type_99' >
										<span class='helpcloud' help_height='30' help_html='등록되어 있는 상품을 활용해서 세트 상품을 구성 할수 있습니다'>세트상품</span>
									</label>";
        if(false){
            $Contents .="			<input type=radio name=product_type id='product_type_88' class='product_type' value='88' ".($product_type == "88" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('planGoodsInfo');\" ".(($id && $mode!='copy' && $product_type != '88') ? "disabled":"")."> <label for='product_type_88' >기획상품 </label>";
        }
        /*
                    $Contents .= "
                                            <input type=radio name=product_type id='product_type_1'  value='1' ".($product_type == "1" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('buyingServiceInfo');\"  ".(($id && $mode!='copy' && $product_type != '1') ? "disabled":"").">
                                            <label for='product_type_1' >
                                                <span class='helpcloud' help_html='해외사이트에 등록되어 있는 구매대행 상품을 등록/관리 할 수 있습니다'>구매대행(사이트상품)</span>
                                            </label>

                                            <input type=radio name=product_type id='product_type_2' value='2' ".($product_type == "2" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('buyingServiceInfo');\"  ".(($id && $mode!='copy' && $product_type != '2') ? "disabled":"").">
                                            <label for='product_type_2' >구매대행(선매입)</label>";

                    if($_SESSION["admininfo"][charger_id] == "forbiz" && false){
                    $Contents .= "
                                            <input type=radio name=product_type id='product_type_6'  value='6' ".($product_type == "6" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('GoodsInfo');\" ".(($id && $mode!='copy' && $product_type != '6') ? "disabled":"").">
                                            <label for='product_type_6' >모바일등록상품  </label>

                                            <!--input type=radio name=product_type id='product_type_7'  value='7' ".($product_type == "7" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('CarArea');\"> <label for='product_type_7' >자동차  </label-->
                                            ";
                }
        */
        if($_SESSION["admininfo"][admin_level] == '9'){
            $Contents .="				<input type=radio name=product_type id='product_type_77' value='77' ".($product_type == "77" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('freeGift');\" ".(($id && $mode!='copy' && $product_type != '77') ? "disabled":"").">
        							<label for='product_type_77' >사은품 </label>";
        }

        if(false){
            $Contents .="
									<input type=radio name=product_type id='product_type_12'  value='12' ".($product_type == "12" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('group_goods');\" ".(($id && $mode!='copy' && $product_type != '12' && $product_type != '0') ? "disabled":"").">
									<label for='product_type_12' >딜상품  </label>";
        }

        $Contents .= "		</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height='3'></td>
				</tr>
			</table><br>";
    }
}else if($_SESSION["admininfo"][mall_type] == "B" || $_SESSION["admininfo"][mall_type] == "F"){
    $Contents .= "	<table cellspacing=0 cellpadding=0 border=0 width='100%'>
				<tr bgcolor='#cccccc' height='36'>
					<td bgcolor=\"#efefef\" width=120 style='padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'> <b>상품구분</b></td>
					<td colspan=3 bgcolor=\"#efefef\" align='left'>
						<table border=0 cellpadding=0 cellspacing=0 width=550>
							<tr>
								<td>
									<input type=radio name=product_type id='product_type_0' value='0' ".($product_type == "0" || $product_type == "" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('GoodsInfo');\"> <label for='product_type_0' >일반상품 </label>
									<input type=radio name=product_type id='product_type_6'  value='6' ".($product_type == "6" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('GoodsInfo');\"> <label for='product_type_6' >모바일등록상품  </label>";

    if($_SESSION["admininfo"][charger_id] == "forbiz" ){
        $Contents .= "	<input type=radio name=product_type id='product_type_99' value='99' ".($product_type == "99" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('setGoodsInfo');\">							<label for='product_type_99' >세트상품 </label>
									";
    }
    //if($_SESSION["admininfo"][charger_id] == "forbiz" ){
    $Contents .= "
									<input type=radio name=product_type id='product_type_1'  value='1' ".($product_type == "1" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('buyingServiceInfo');\"> <label for='product_type_1' >구매대행(사이트상품)  </label>
									<input type=radio name=product_type id='product_type_2'  value='2' ".($product_type == "2" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('buyingServiceInfo');\"> <label for='product_type_2' >구매대행(선매입)  </label>";
    //}
    $Contents .= "

									<!--input type=radio name=product_type id='product_type_3'  value='3' ".($product_type == "3" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('hotcon');\"> <label for='product_type_3' >하트콘 상품</label>
									<input type=radio name=product_type id='product_type_11'  value='11' ".($product_type == "11" ? "checked":"")." onclick=\"dateSelect('2');ShowGoodsTypeInfo('AuctionInfo');\"> <label for='product_type_11' >최저가경매</label-->

								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height='3'></td>
				</tr>
				</table><br>";
}else{
    //$Contents .= "<input type=hidden name=product_type id='product_type_0' value='".$product_type."' > ";
    $Contents .= "
	<table cellspacing=0 cellpadding=0 border=0 width='100%'>
		<tr bgcolor='#cccccc' height='36'>
			<td bgcolor=\"#efefef\" width=120 style='padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'> <b>상품구분</b></td>
			<td colspan=3 bgcolor=\"#efefef\" align='left'>
				<table border=0 cellpadding=0 cellspacing=0 width=550>
					<tr>
						<td>
							<input type=radio name=product_type id='product_type_0' value='0' ".($product_type == "0" || $product_type == "" ? "checked":"")."> <label for='product_type_0' >일반상품</label>
							<input type=radio name=product_type id='product_type_6'  value='6' ".($product_type == "6" ? "checked":"")."> <label for='product_type_6' >모바일등록상품</label>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height='3'></td>
		</tr>
	</table>";
}

if(($_SESSION["admininfo"][mall_type] == "O" || $_SESSION["admininfo"][mall_type] == "E") || $_SESSION["admininfo"][mall_type] == "B" || $_SESSION["admininfo"][mall_type] == "F"){
    /*
    $goodsHelp_text = "
    <table cellpadding=0 cellspacing=0 class='small' >
        <col width=8>
        <col width=*>
        <tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >자사가 직접 소싱 하는 상품을 등록 관리 합니다.</td></tr>
        <tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >경매 상품과 , 해외구매 대행 상품은 별도로 등록 되어야 합니다.</td></tr>
        <tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 상품 타입에 따라 쇼핑몰 에서 구분되어 상품이 표시됩니다.</td></tr>

    </table>
    ";*/
    if($_SESSION["admininfo"]["admin_level"] == "9"){
       // $goodsHelp_text = getTransDiscription($page_code,'A');
       // $GoodsHelp = HelpBox("일반 상품관리", $goodsHelp_text);
    }


    $buyingServiceHelp_text = "
<!--table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >구매대행 상품 등록하기 버튼을 클릭하셔서 구매대행 상품정보를 자동으로 가져 오실수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 구매대행 상품은 자동으로 원천사이트의 정보변경을 체크하여 관리 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >변경된 정보는 자동으 시스템에 반영됩니다.</td></tr>

</table-->  ".getTransDiscription($page_code,'S')."
";
    $buyingServiceHelp_text = getTransDiscription($page_code,'B');
    $buyingServiceHelp = HelpBox("구매대행 상품관리", $buyingServiceHelp_text);

    $planGoodsInfoHelp_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >세트상품은 아래 세트상품구성 부분에서 상품을 조회 선택 구성하실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >세트상품 구성은 선택된 상품의 옵션 구성정보를 바탕으로 재구성 하실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >구성된 세트상품의 구성정보는 세트상품옵션 정보로 구성되게 됩니다. </td></tr>
</table>
";
//$setGoodsHelp_text = getTransDiscription($page_code,'C');
    $planGoodsInfoHelp = HelpBox("기획 상품관리", $planGoodsInfoHelp_text);


    $HotConHelp_text = "
<!--table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >하트콘은 MMS 를 통해 상품쿠폰을 수령할수 있는 상품입니다..</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >할인된 가격을 통해서 상품을 구매할 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >하트콘 상품을 사용하시기 위해서는 별도의 신청이 필요합니다. 문의전화 :  02-2058-2214 </td></tr>
</table-->
";
    $HotConHelp_text = getTransDiscription($page_code,'C');
    $HotConHelp = HelpBox("HotCon 상품관리", $HotConHelp_text);

    $setGoodsHelp_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >세트상품은 아래 세트상품구성 부분에서 상품을 조회 선택 구성하실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >세트상품 구성은 선택된 상품의 옵션 구성정보를 바탕으로 재구성 하실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >구성된 세트상품의 구성정보는 세트상품옵션 정보로 구성되게 됩니다. </td></tr>
</table>
";
//$setGoodsHelp_text = getTransDiscription($page_code,'C');
    $setGoodsHelp = HelpBox("Set 상품관리", $setGoodsHelp_text);

    $FreeGiftHelp_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >세트상품은 아래 세트상품구성 부분에서 상품을 조회 선택 구성하실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >세트상품 구성은 선택된 상품의 옵션 구성정보를 바탕으로 재구성 하실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >구성된 세트상품의 구성정보는 세트상품옵션 정보로 구성되게 됩니다. </td></tr>
</table>
";
//$setGoodsHelp_text = getTransDiscription($page_code,'C');
    $FreeGiftHelp = HelpBox("사은품 상품관리", $FreeGiftHelp_text);


    $Contents .= "
				<div id='GoodsInfo' ".($product_type == "0" || $product_type == "" ? "style='display:block;padding-bottom:20px;'":"style='display:none;'").">
				<table width=100% border=0>
					<tr>
						<td>".$GoodsHelp."</td>";
   /* if($id){
        $Contents .= "
						<td width=100 valign=bottom>".GenerateQRCode($_SESSION["admin_config"][mall_data_root]."/images", $id)."</td>";
    }*/
    $Contents .= "
					</tr>
				</table>
				</div>";

    $Contents .= "
			<div id='setGoodsInfo' ".($product_type == "99" ? "style='display:block;padding-bottom:20px;'":"style='display:none;padding-bottom:20px;'").">
				".$setGoodsHelp."
			</div>";

    $Contents .= "
			<div id='freeGift' ".($product_type == "77" ? "style='display:block;padding-bottom:20px;'":"style='display:none;padding-bottom:20px;'").">
				".$FreeGiftHelp."
			</div>";

    $Contents .= "
			<div id='planGoodsInfo' ".($product_type == "88" ? "style='display:block;padding-bottom:20px;'":"style='display:none;padding-bottom:20px;'").">
				".$planGoodsInfoHelp."
			</div>";

    $Contents .= "
				<div id='buyingServiceInfo' ".($product_type == "1" ? "style='display:block;'":"style='display:none;'").">
				<table width=100%>
				<tr>
					<td>$buyingServiceHelp</td>
					<td width=200 align=center><a href=\"javascript:PoPWindow3('../product/buyingService.php?mmode=pop',760,700,'buyingService')\"'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_buyingServiceReg.gif'></a></td>
				</tr>
				</table><br>
				</div>
				<div id='AuctionInfo' ".($product_type == "11" ? "style='display:block;'":"style='display:none;'").">
				<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:5px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b> 최저가경매 추가입력 </b></td><td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>
				<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver>
					<tr bgcolor='#ffffff' height=25>
						<td width=120 bgcolor=\"#efefef\" align=left class=small nowrap> 경매시간 </td>
						<td style='padding-left:10px' id='dateselect'><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY id=FromYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM id=FromMM></SELECT> 월 <SELECT name=FromDD id=FromDD></SELECT> 일 <SELECT name=FromHH id=FromHH></SELECT> 시 <SELECT name=FromII id=FromII></SELECT> 분~ <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY id=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM id=ToMM></SELECT> 월 <SELECT name=ToDD id=ToDD></SELECT> 일 <SELECT name=ToHH id=ToHH></SELECT> 시 <SELECT name=ToII id=ToII></SELECT> 분</td>
					</tr>
					<tr bgcolor='#ffffff' height=25>
						<td bgcolor=\"#efefef\" align=left class=small nowrap> 경매시작가 </td>
						<td style='padding-left:10px'><input type='text' id='start_price' name='startprice' value='1000' size=30></td>
					</tr>
					<tr bgcolor='#ffffff' height=25>
						<td bgcolor=\"#efefef\" align=left class=small nowrap> 시간연장 횟수 </td>
						<td style='padding-left:10px'><input type='text' name='plus_count' size=10 value='".$plus_count."'>회 &nbsp;&nbsp;&nbsp;&nbsp;<!--* 0을 입력하면 사용안함이 됩니다.--> ".getTransDiscription($page_code,'T')."</td>
					</tr>
				</table><br></div>
				<div id='hotcon' ".($product_type == "3" ? "style='display:block;padding-bottom:20px;'":"style='display:none;padding-bottom:20px;'").">
				<table width=100% border=0>
				<tr>

					<td align=center style='padding:20px 0 0 0;vertical-align:bottom'>

					<table width=100% border=0>
					<tr>
						<td width=200 align=center style='font-size:15px;'>
						<b>이벤트코드</b>
						</td>
						<td width=200 align=center style='font-size:15px;'>
						<b>하트콘 상품코드</b>
						</td>
					</tr>
					<tr>
						<td width=200 align=center>
						<input type='text' class='textbox' name='hotcon_event_id' style='font-size:25px;width:80px;height:34px;' value='$hotcon_event_id'>
						</td>
						<td width=200 align=center>
						<input type='text' class='textbox' name='hotcon_pcode' style='font-size:25px;width:180px;height:34px;' value='$hotcon_pcode'>
						</td>
					</tr>
					</table>
					</td>
					<td rowspan=2>$HotConHelp</td>
				</tr>

				</table>
				</div>
				<div id='CarArea' ".($product_type == "7" ? "style='display:block;padding-bottom:20px;'":"style='display:none;padding-bottom:20px;'").">
				<table width=100% border=0>
				<tr>

					<td align=center style='padding:0px 0 0 0;vertical-align:bottom'>


					<table width=100% border=0 cellpadding=5 cellspacing=1 bgcolor=silver style='margin:5px 0px;'>
					<col width='15%'>
					<col width='35%'>
					<col width='15%'>
					<col width='35%'>
					 <tr bgcolor=#ffffff >
						<td bgcolor='#efefef' align=left nowrap>  <b>자동차/오토바이 구분 </b> </td>
						<td align=left>
							<input type=radio name='vechile_div' id='vechile_div_c' value='C' onclick=\"loadManufacturer(this,'mf_ix');ChangeVechileType('자동차 유형', this.value);\"  ".($vechile_div == "C" ? "checked":"")."><label for='vechile_div_c'>자동차</label>
							<input type=radio name='vechile_div' id='vechile_div_b' value='B' onclick=\"loadManufacturer(this,'mf_ix');ChangeVechileType('자동차 유형', this.value);\"  ".($vechile_div == "B" ? "checked":"")." ><label for='vechile_div_b'>오토바이</label>
						</td>
						<td bgcolor='#efefef' align=left  style='font-size:12px;'>
						<b>자동차 유형</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						".makeVechileTypeSelectBox($vechile_div,"vt_ix",$vt_ix,"자동차 유형","class='car_info'  style='border:1px solid silver;width:150px;font-size:12px;' validation='".$car_defailt_validation."' title='자동차 차종'")."
						</td>
					  </tr>
					<tr>
						<td bgcolor='#efefef' align=left  style='font-size:12px;'>
						 <b>제조사 / 모델 / 등급</b>
						</td>
						<td bgcolor='#ffffff' align=left colspan=3>
						<table  border=0 cellpadding=0 cellspacing=0 >
							<col width='155px'>
							<col width='155px'>
							<col width='155px'>
							<tr bgcolor='#ffffff'>
								<td align=left >
								".makeManufacturerSelectBox($vechile_div,"mf_ix",$mf_ix,"제조사"," class='car_info' onChange=\"loadModel(this,'md_ix')\" style='border:1px solid silver;width:150px;font-size:12px;' validation='".$car_defailt_validation."' title='자동차 제조사'")."
								</td>

								<td align=left style='font-size:15px;'>
								".makeModelSelectBox($vechile_div,"md_ix",$md_ix,$mf_ix,"모델","class='car_info' onChange=\"loadGrade(this,'gr_ix')\"  style='border:1px solid silver;width:150px;font-size:12px;' validation='".$car_defailt_validation."' title='자동차 모델'")."
								</td>
								<td align=left style='font-size:15px;'>
								".makeVechileGradeSelectBox($vechile_div,"gr_ix",$gr_ix,$md_ix,"등급","class='car_info' style='border:1px solid silver;width:150px;font-size:12px;' validation='".$car_defailt_validation."' title='자동차 등급'")."
								</td>
							</tr>
							</table>
						</td>

					</tr>
					<tr>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>년식</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						<SELECT onchange=javascript:onChangeDate(this.form.vintage_year,this.form.vintage_month,null) name=vintage_year id=vintage_year class='car_info' ></SELECT> 년
						<SELECT onchange=javascript:onChangeDate(this.form.vintage_year,this.form.vintage_month,null) name=vintage_month id=vintage_month class='car_info' ></SELECT> 월
						</td>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>차량 상태</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						<input type='radio' class='textbox car_info' name='car_condition' id='car_condition_n' style='border:0px' value='N' ".($car_condition == "N" ? "checked":"")." validation=".$car_defailt_validation." title='차량 상태'><label for='car_condition_n'>새차</label>
						<input type='radio' class='textbox car_info' name='car_condition' id='car_condition_u' style='border:0px' value='U' ".($car_condition == "U" ? "checked":"")." validation=".$car_defailt_validation." title='차량 상태'><label for='car_condition_u'>중고차</label>
						</td>
					</tr>
					<tr>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>주행거리</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						<input type='text' class='textbox car_info' name='mileage' style='font-size:12px;width:200px;height:20px;' value='$mileage' validation=".$car_defailt_validation." title='주행거리'> km
						</td>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>배기량</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						<input type='text' class='textbox car_info' name='displacement' style='font-size:12px;width:200px;height:20px;' value='$displacement' validation=".$car_defailt_validation." title='배기량'> CC
						</td>
					</tr>
					<tr>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>변속기</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						<input type='radio' class='textbox car_info' name='transmission' id='transmission_a' style='border:0px' value='A' ".($transmission == "A" ? "checked":"")." title='변속기'><label for='transmission_a'>오토</label>
						<input type='radio' class='textbox car_info' name='transmission' id='transmission_m' style='border:0px' value='M' ".($transmission == "M" ? "checked":"")." title='변속기'><label for='transmission_m'>수동</label>
						<input type='radio' class='textbox car_info' name='transmission' id='transmission_sa' style='border:0px' value='SA' ".($transmission == "SA" ? "checked":"")." title='변속기'><label for='transmission_sa'>세미오트</label>
						<input type='radio' class='textbox car_info' name='transmission' id='transmission_cvt' style='border:0px' value='CVT' ".($transmission == "CVT" ? "checked":"")." title='변속기'><label for='transmission_cvt'>CVT</label>
						</td>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>색상</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						<input type='text' class='textbox car_info' name='color' style='font-size:12px;width:200px;height:20px;' value='$color' validation=".$car_defailt_validation." title='색상'>
						</td>
					</tr>
					<tr>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>연료</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						<input type='radio' class='textbox car_info' name='fuel' id='fuel_gas' style='border:0px' value='GAS' ".($fuel == "GAS" ? "checked":"")." validation=".$car_defailt_validation." title='연료'><label for='fuel_gas'>가스</label>
						<input type='radio' class='textbox car_info' name='fuel' id='fuel_gasoline' style='border:0px'  value='GASOLINE' ".($fuel == "GASOLINE" ? "checked":"")." validation=".$car_defailt_validation." title='연료'><label for='fuel_gasoline'>휘발유</label>
						<input type='radio' class='textbox car_info' name='fuel' id='fuel_diesel' style='border:0px'  value='DIESEL' ".($fuel == "DIESEL" ? "checked":"")." validation=".$car_defailt_validation." title='연료'><label for='fuel_diesel'>경유</label>
						</td>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>차량번호</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						<input type='text' class='textbox car_info' name='license_plate' style='font-size:12px;width:200px;height:20px;' value='$license_plate' validation=".$car_defailt_validation." title='차량번호'>
						</td>
					</tr>

					</table>
					</td>
				</tr>
				</table>
				</div>
				<div id='RealEstateArea' ".($product_type == "8" ? "style='display:block;padding-bottom:20px;'":"style='display:none;padding-bottom:20px;'").">
				<table width=100% border=0>
				<tr>

					<td align=center style='padding:0px 0 0 0;vertical-align:bottom'>
					<table width=100% border=0 cellpadding=5 cellspacing=1 bgcolor=silver style='margin:5px 0px;'>
					<col width='15%'>
					<col width='35%'>
					<col width='15%'>
					<col width='35%'>
					<tr>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>지역</b>
						</td>
						<td bgcolor='#ffffff' align=left >
							".getRegionInfoSelect('parent_rg_ix', '1차 지역',$parent_rg_ix, $parent_rg_ix, 1, " onChange=\"loadRegion(this,'rg_ix')\" validation='".$realestate_defailt_validation."' title='지역' class='property_info' ")."
							".getRegionInfoSelect('rg_ix', '2차 지역',$parent_rg_ix, $rg_ix, 2, "validation='".$realestate_defailt_validation."' title='지역' class='property_info' ")."
						</td>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>면적</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						<input type='text' class='textbox property_info' name='dimensions' style='font-size:12px;width:200px;height:20px;' value='$dimensions' validation=".$realestate_defailt_validation."  title='면적'>
						</td>
					</tr>
					<tr>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>거래 형태</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						<input type='radio' class='textbox property_info' name='deal_type' id='deal_type_s' style='border:0px' value='S' ".($deal_type == "S" ? "checked":"")." validation=".$realestate_defailt_validation." title='거래 형태'><label for='deal_type_s'>매매</label>
						<input type='radio' class='textbox property_info' name='deal_type' id='deal_type_l' style='border:0px'  value='L' ".($deal_type == "L" ? "checked":"")." validation=".$realestate_defailt_validation." title='거래 형태'><label for='deal_type_l'>임대</label>
						</td>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>매물 종류</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						<input type='radio' class='textbox property_info' name='property_type' id='property_type_apt' style='border:0px' value='A' ".($property_type == "A" ? "checked":"")." validation=".$realestate_defailt_validation." title='매물 종류'><label for='property_type_apt'>아파트</label>
						<input type='radio' class='textbox property_info' name='property_type' id='property_type_house' style='border:0px'  value='H' ".($property_type == "H" ? "checked":"")." validation=".$realestate_defailt_validation." title='매물 종류'><label for='property_type_house'>단독주택</label>
						<input type='radio' class='textbox property_info' name='property_type' id='property_type_store' style='border:0px'  value='S' ".($property_type == "S" ? "checked":"")." validation=".$realestate_defailt_validation." title='매물 종류'><label for='property_type_store'>상가</label>
						<input type='radio' class='textbox property_info' name='property_type' id='property_type_store' style='border:0px'  value='G' ".($property_type == "T" ? "checked":"")." validation=".$realestate_defailt_validation." title='매물 종류'><label for='property_type_store'>토지</label>
						</td>
					</tr>

					<tr>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>대출가능금액</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." <input type='text' class='textbox integer property_info' name='loans' style='font-size:12px;width:200px;height:20px;' value='$loans' validation=".$realestate_defailt_validation." title='대출가능금액'> ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
						</td>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>관리비</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." <input type='text' class='textbox integer property_info' name='maintenance_cost' style='font-size:12px;width:200px;height:20px;' value='$maintenance_cost' validation=".$realestate_defailt_validation."  title='관리비'> ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
						</td>
					</tr>

					<tr>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>난방 연료</b>
						</td>
						<td bgcolor='#ffffff' align=left>
							<select name='heating_fuel'>
								<option value='1'>가스</option>
								<option value='2'>난방유</option>
								<option value='3'>열병합발전</option>
								<option value='4'>태양열발전</option>
							</select>
						<!--input type='radio' class='textbox property_info' name='heating_fuel' id='heating_fuel' style='border:0px' value='N' ".($heating_fuel == "N" ? "checked":"")." validation=".$realestate_defailt_validation." title='연료'><label for='heating_fuel'>GAS</label>
						<input type='radio' class='textbox property_info' name='heating_fuel' id='heating_fuel' style='border:0px' value='U' ".($heating_fuel == "U" ? "checked":"")." validation=".$realestate_defailt_validation." title='연료'><label for='heating_fuel'>중앙난방</label>
						<input type='radio' class='textbox property_info' name='heating_fuel' id='heating_fuel' style='border:0px' value='U' ".($heating_fuel == "U" ? "checked":"")." validation=".$realestate_defailt_validation." title='연료'><label for='heating_fuel'>기름</label-->
						</td>
						<td bgcolor='#efefef' align=left style='font-size:12px;'>
						 <b>입주 가능일자</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						<input type='text' class='textbox property_info' name='posisbile_date' style='font-size:12px;width:200px;height:20px;' value='$posisbile_date' validation=".$realestate_defailt_validation."  title='입주 가능일자'>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</div>";
    if(true){

        $Contents .= "
				<div id='TravelHotelArea' ".($product_type == "9" ? "style='display:block;padding-bottom:20px;'":"style='display:none;padding-bottom:20px;'").">
				<table width=100% border=0>
				<!--tr>
					<td>
					<input type='radio' class='textbox travel_hotel_info' name='travel_product_type' id='travel_product_type_h' style='border:0px' value='H' ".($travel_product_type == "H" || $travel_product_type == "" ? "checked":"")." validation=".$realestate_defailt_validation." title='여행상품타입'><label for='travel_product_type_h'>호텔</label>
					<input type='radio' class='textbox travel_hotel_info' name='travel_product_type' id='travel_product_type_t' style='border:0px' value='S' ".($travel_product_type == "T" ? "checked":"")." validation=".$realestate_defailt_validation." title='여행상품타입'><label for='travel_product_type_t'>관광상품</label>
					<input type='radio' class='textbox travel_hotel_info' name='travel_product_type_A' id='travel_product_type_a' style='border:0px' value='A' ".($travel_product_type == "A" ? "checked":"")." validation=".$realestate_defailt_validation." title='여행상품타입'><label for='travel_product_type_a'>항공권</label>
					</td>
				</tr-->
				<tr>

					<td align=center style='padding:0px 0 0 0;vertical-align:bottom'>

					<table width=100% border=0 cellpadding=0 cellspacing=0 bgcolor=silver style='margin:5px 0px;' class='input_table_box'>
					<col width='15%'>
					<col width='35%'>
					<col width='15%'>
					<col width='35%'>
					<tr>
						<td class='input_box_title'>
						  <b>지역</b>
						</td>
						<td class='input_box_item' colspan=3>
							".getRegionInfoSelect('parent_hotel_rg_ix', '1차 지역',$parent_hotel_rg_ix, $parent_hotel_rg_ix, 1, " onChange=\"loadRegion(this,'hotel_rg_ix')\" class='travel_hotel_info' validation='".$hotel_default_validation."' title='지역' ")."
							".getRegionInfoSelect('hotel_rg_ix', '2차 지역',$parent_hotel_rg_ix, $hotel_rg_ix, 2, "class='travel_hotel_info' validation='".$hotel_default_validation."' title='지역'  ")."
						</td>

					</tr>
					<tr>
						<td class='input_box_title'>
						 <b>호텔등급</b>
						</td>
						<td class='input_box_item'>
						<select name='hotel_level' class='travel_hotel_info' validation='".$hotel_default_validation."' title='호텔등급' >
							<option value='' ".($hotel_level == "" ? "selected":"").">호텔등급</option>
							<option value='1' ".($hotel_level == "1" ? "selected":"").">1등급</option>
							<option value='2' ".($hotel_level == "2" ? "selected":"").">2등급</option>
							<option value='3' ".($hotel_level == "3" ? "selected":"").">3등급</option>
							<option value='4' ".($hotel_level == "4" ? "selected":"").">4등급</option>
							<option value='5' ".($hotel_level == "5" ? "selected":"").">5등급</option>
							<option value='6' ".($hotel_level == "6" ? "selected":"").">6등급</option>
						</select>
						</td>
						<td class='input_box_title'>
						 <b>객실 등급</b>
						</td>
						<td class='input_box_item'>
						<select name='room_level' class='travel_hotel_info' validation='".$hotel_default_validation."' title='객실 등급' >
							<option value='' ".($room_level == "" ? "selected":"").">객실등급</option>
							<option value='SD' ".($room_level == "SD" ? "selected":"").">스탠다드</option>
							<option value='DX' ".($room_level == "DX" ? "selected":"").">디럭스,</option>
							<option value='SP' ".($room_level == "SP" ? "selected":"").">슈페리어</option>
							<option value='ST' ".($room_level == "ST" ? "selected":"").">스위트룸</option>
						</select>
						</td>
					</tr>

					<!--tr>
						<td bgcolor='#efefef' align=center style='font-size:12px;'>
						<b>대출가능금액</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." <input type='text' class='textbox travel_hotel_info' name='loans' style='font-size:12px;width:200px;height:20px;' value='$loans' validation='".$hotel_default_validation."' title='대출가능금액'> ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
						</td>
						<td bgcolor='#efefef' align=center style='font-size:12px;'>
						<b>관리비</b>
						</td>
						<td bgcolor='#ffffff' align=left>
						".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." <input type='text' class='textbox travel_hotel_info' name='maintenance_cost' style='font-size:12px;width:200px;height:20px;' value='$maintenance_cost' validation='".$hotel_default_validation."'  title='관리비'> ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
						</td>
					</tr-->

					</table>
					</td>
				</tr>
				</table>
				</div>
				<div id='TravelTourismArea' ".($product_type == "10" ? "style='display:block;padding-bottom:20px;'":"style='display:none;padding-bottom:20px;'").">
				<table width=100% border=0 cellpadding=0 cellspacing=0 bgcolor=silver style='margin:5px 0px;' class='input_table_box'>
					<col width='15%'>
					<col width='*'>
					<tr>
						<td class='input_box_title'>
						  <b>지역</b>
						</td>
						<td class='input_box_item' >
							".getRegionInfoSelect('parent_sightseeing_rg_ix', '1차 지역',$parent_sightseeing_rg_ix, $parent_sightseeing_rg_ix, 1, " onChange=\"loadRegion(this,'sightseeing_rg_ix')\" class='travel_tour_info' validation='".$tour_default_validation."' title='지역' ")."
							".getRegionInfoSelect('sightseeing_rg_ix', '2차 지역',$parent_sightseeing_rg_ix, $sightseeing_rg_ix, 2, "class='travel_tour_info' validation='".$tour_default_validation."' title='지역'  ")."
						</td>

					</tr>
				</table>
				</div>
				";
    }
}

if($_SESSION["admin_config"]["use_sellertool"] == "Y"){
    $Contents .=	"<table width='100%' cellpadding=0 cellspacing=0>
				<tr height=30>
					<td style='padding-bottom:10px;'>
						".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 제휴사 연동정보 설정  </b><span class=small></span> </td><td align=right style='padding-right:20px;' class=small> </td></tr></table>")."
					</td>
				</tr>
				</table>

				<table cellpadding=3 cellspacing=0 width='100%' border='0' class='input_table_box' align='center' >
					<col width=15%>
					<col width=35%>
					<col width=15%>
					<col width=35%>
					<tr>
						<td class='input_box_title'> <b>국내 제휴사 상품연동여부</b>    </td>
						<td class='input_box_item' colspan=3>
							";

    $sql = "select * from sellertool_get_product where pid = '".$id."' and state = '1'  ";
    $db->query($sql);
    $partner_prd_reg = $db->fetchall();
    //print_r($partner_prd_reg);
    if(is_array($partner_prd_reg)){
        for($i=0; $i < count($partner_prd_reg); $i++){
            $Contents .= "
								<input type='hidden' name='partner_prd_reg_before[]' value='".$partner_prd_reg[$i]['site_code']."' />
							";
            $partner_prd_data[] =  $partner_prd_reg[$i]['site_code'];
        }
    }else{
        $partner_prd_data = array();
    }
    //$sql = "select * from sellertool_site_info where api_yn = 'Y'";
    $sql = "select * from sellertool_site_info where api_yn = 'Y' and site_div = '1' and site_code not in (select site_code from sellertool_not_company where state= '1' and company_id = '".$admin."')";
    $db2->query($sql);

    if($db2->total){
        for($i=0; $i < $db2->total; $i++){
            $db2->fetch($i);
            //$sql = "select state from sellertool_get_product where company_id = '".$company_id."' and site_code = '".$db2->dt[site_code]."'";

            //$mdb->query($sql);
            if(in_array($db2->dt[site_code],$partner_prd_data)){
                $Contents .= "
									<input type=checkbox name='partner_prd_reg[]' id='partner_prd_reg_".$db2->dt[site_code]."' value='".$db2->dt[site_code]."' onclick=\"get_standard_category_add_infomation($('input[name=standard_basic]').val())\"  checked ><label for='partner_prd_reg_".$db2->dt[site_code]."'>".$db2->dt[site_name]."</label>";
            }else{
                $Contents .= "
									<input type=checkbox name='partner_prd_reg[]' id='partner_prd_reg_".$db2->dt[site_code]."' value='".$db2->dt[site_code]."' onclick=\"get_standard_category_add_infomation($('input[name=standard_basic]').val())\"  ".($act =='insert' ? "checked" : "" )." ><label for='partner_prd_reg_".$db2->dt[site_code]."'>".$db2->dt[site_name]."</label>";
            }

        }
    }
    $Contents .= "
							
						</td>
					</tr>
					<tr>
						<td class='input_box_title'> <b>해외 제휴사 상품연동여부</b>    </td>
						<td class='input_box_item' colspan=3>
							";


    //$sql = "select * from sellertool_site_info where api_yn = 'Y'";
    $sql = "select * from sellertool_site_info where api_yn = 'Y' and site_div = '2' and site_code not in (select site_code from sellertool_not_company where state= '1' and company_id = '".$admin."')";
    $db2->query($sql);

    if($db2->total){
        for($i=0; $i < $db2->total; $i++){
            $db2->fetch($i);
            //$sql = "select state from sellertool_get_product where company_id = '".$company_id."' and site_code = '".$db2->dt[site_code]."'";

            //$mdb->query($sql);
            if(in_array($db2->dt[site_code],$partner_prd_data)){
                $Contents .= "
									<input type=checkbox name='partner_prd_reg[]' id='partner_prd_reg_".$db2->dt[site_code]."' onclick=\"get_standard_category_add_infomation($('input[name=standard_basic]').val())\" value='".$db2->dt[site_code]."' checked ><label for='partner_prd_reg_".$db2->dt[site_code]."'>".$db2->dt[site_name]."</label>";
            }else{
                $Contents .= "
									<input type=checkbox name='partner_prd_reg[]' id='partner_prd_reg_".$db2->dt[site_code]."' onclick=\"get_standard_category_add_infomation($('input[name=standard_basic]').val())\" value='".$db2->dt[site_code]."' ".($act =='insert' ? "checked" : "" )." ><label for='partner_prd_reg_".$db2->dt[site_code]."'>".$db2->dt[site_name]."</label>";
            }

        }
    }
    $Contents .= "
							
						</td>
					</tr>
					</table><br>
					";
}
/*
$Contents .= "
			<table width='100%' cellpadding=0 cellspacing=0 style='border:1px solid silver;'>
				<tr>
					<td class='input_box_title' style='width:130px;background-color:#ffffff;'> 서비스 분류 <img src='".$required3_path."'> </td>
					<td class='input_box_item' style='padding:0px;' style='line-height:150%'  colspan=3>
						<div style='float:left;margin-right:5px;'><input type=checkbox class='service_type' name='soho' id='soho'  value='1' ".($soho ? "checked":"")."><label for='soho'>트렌드</label></div>
						<div style='float:left;margin-right:5px;'><input type=checkbox class='service_type' name='designer' id='designer'  value='1'  ".($designer ? "checked":"")."><label for='designer'>디자이너</label></div>
						<div style='float:left;margin-right:5px;'><input type=checkbox class='service_type' name='mirrorpick' id='mirrorpick'  value='1'  ".($mirrorpick ? "checked":"")."><label for='mirrorpick'>미러픽</label></div>
					</td>
				</tr>
			</table>
			<div style='clear:both;height:70px;'></div>";
*/
//$_SESSION["admin_config"]["use_sellertool"]
if(($admininfo["admin_level"] == '9' && $_SESSION["admin_config"]['mall_use_standard_category'] == 'Y') ||   $goods_input_type == "globalsellertool"){
    $Contents .= "
			<div id='standard_category_div'>
			<table width='100%' cellpadding=0 cellspacing=0>
			<tr height=30>
				<td style='padding-bottom:0px;' ><!---->
				".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><b class=blk > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 표준카테고리 등록 </b><span class=small><!--하단에 카테고리를 선택하신후 카테고리 등록하기 버튼을 클릭하세요.(다중 카테고리 등록지원)-->".getTransDiscription($page_code,'D')." </span></td><td align=right style='padding-right:0px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--><img src='/admin/images/btn_group_open.png' open_src='/admin/images/btn_group_close.png' close_src='/admin/images/btn_group_open.png' alt='Minus' align=absmiddle style='margin-left:5px;' onclick=\"moreDisplay(this, 'div#standard_category_area');\"></td></tr></table>")."
				</td>
			</tr>
			</table><br>
			<div id='standard_category_area' style='display:none;'>
			<div class='tab' style='padding-bottom:15px;'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab' >
					<table id='standard_category_tab_01' class='on'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"showStandardCategoryTab('select_standard_category','standard_category_tab_01');\">선택등록</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='standard_category_tab_02'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"showStandardCategoryTab('search_standard_category','standard_category_tab_02');\">검색어등록</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td class='btn'>
				</td>
			</tr>
			</table>
			<br>
			</div>";

    $Contents .= "
			<input type='hidden' name=selected_cid value='".$standard_cid."'>
			<input type='hidden' name=selected_depth value=''>
			<input type='hidden' id='_category' value=''>
			<input type='hidden' id='_category' value=''>
			<input type='hidden' id='basic' value=''>
			<!--input type='hidden' name=cid_1 value=''>
			<input type='hidden' name=cid_2 value=''>
			<input type='hidden' name=cid_3 value=''-->

			<table cellpadding=0 cellspacing=0  border='0' width='100%'  id='select_standard_category' >
				<!--col width=15%-->
				<col width=90%>
				<tr>
					<!--td class='input_box_title'  nowrap> <b>카테고리 </b> </td-->
					<td  >
						<table width=100% border=0 cellpadding=0 cellspacing=0>
							<col width='20%'>
							<col width='20%'>
							<col width='20%'>
							<col width='20%'>
							<col width='20%'>
							<tr>
								<td style='padding:5px 0px 5px 2px;'>".getStandardCategoryMultipleSelect("--1차분류--", "standard_cid0", "standard_cid","onChange=\"StandardLoadCategory($(this),'standard_cid1',2)\" title='1차분류' ", 0, $standard_cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getStandardCategoryMultipleSelect("--2차분류--", "standard_cid1",  "standard_cid","onChange=\"StandardLoadCategory($(this),'standard_cid2',2)\" title='2차분류'", 1, $standard_cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getStandardCategoryMultipleSelect("--3차분류--", "standard_cid2", "standard_cid", "onChange=\"StandardLoadCategory($(this),'standard_cid3',2)\" title='3차분류'", 2, $standard_cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getStandardCategoryMultipleSelect("--4차분류--", "standard_cid3", "standard_cid", "onChange=\"StandardLoadCategory($(this),'standard_cid4',2)\" title='4차분류'", 3, $standard_cid)."</td>
								<td style='padding:5px 0px 5px 2px;'>".getStandardCategoryMultipleSelect("--5차분류--", "standard_cid4", "standard_cid", "onChange=\"StandardLoadCategory($(this),'standard_cid_1',2)\" title='5차분류'", 4, $standard_cid)."</td>
								<!--td style='padding:5px 4px 5px 6px;'><img src='../images/".$_SESSION["admininfo"]["language"]."/category_add.gif' align=absmiddle border=0 onclick=\"standard_category_add()\" style='cursor:pointer;'></td-->
							</tr>
						</table>";
    $Contents .= "	</td>
				</tr>
			</table>

			<table cellpadding=0 cellspacing=0  border='0' width='100%' class='input_table_box' id='search_standard_category' style='display:none;'>
				<col width=15%>
				<col width=90%>
				<tr>
					<td class='input_box_title' nowrap> <b>표준 카테고리 </b> </td>
					<td class='input_box_item'>
						<table width=100% border='0' cellpadding=0 cellspacing=0>
							<col width='15%'>
							<col width='10%'>
							<col width='38%'>
							<col width='38%'>
							<tr>
								<td style='padding:5px 0px 5px 2px;'>
								<textarea name='search_standard_category_text' id='search_standard_category_text' style='padding:0px;height:105px;width:99%' class='tline textbox'>".$search_category."</textarea>
								</td>
								<td align='center'>
								<img src='../images/".$_SESSION["admininfo"]["language"]."/search_category.gif' align=absmiddle border=0 onclick=\"search_standard_category()\" style='cursor:pointer;'></td>
								<td style='padding:5px 0px 5px 2px;'>".getStandardCategoryMultipleSelect("--1차분류--", "search_standard_category_list", "standard_cid","", 0, $standard_cid)." </td>
								<!--td style='padding:5px 4px 5px 6px;'><img src='../images/".$_SESSION["admininfo"]["language"]."/category_add.gif' align=absmiddle border=0 onclick=\"standard_category_add()\" style='cursor:pointer;'></td-->
							</tr>
						</table>";

    $Contents .= "	</td>
				</tr>
			</table><br>
			</div>

			<table border=0 cellpadding=0 cellspacing=0 width='100%' style='padding:5px 10px 5px 10px;border:1px solid silver' >
				<col width=100%>
				<tr>
					<td style='padding:10px 10px;'>";
    if($id != "" || true){
        $Contents .= PrintStandardCategoryRelation($id);
    }else{
        $Contents .= "<table width=100% cellpadding=0 cellspacing=0 id=objStandardCategory >
										<col width=5>
										<col width=50>
										<col width=*>
										<col width=30>
										
						</table>";
    }
    $Contents .= "	</td>
				</tr>
				<!--tr><td class='small' height='25' style='padding-left:15px;'> <span class='small'> </span></td></tr-->
			</table><br><br><br><br>
			</div>";

}

if($goods_input_type != "globalsellertool"){
    $Contents .= "
			<div id='category_div'>
			<table width='100%' cellpadding=0 cellspacing=0>
			<tr height=30>
				<td style='padding-bottom:10px;' >
				".colorCirCleBox("#efefef","100%","<table cellpadding=10 width=100%><tr><td style='padding:5px;padding-left:10px;'><b class=blk> <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 카테고리등록 <a name='categoryAdd'></a></b><span class=small><!--하단에 카테고리를 선택하신후 카테고리 등록하기 버튼을 클릭하세요.(다중 카테고리 등록지원)-->".getTransDiscription($page_code,'D')." </span></td><td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--> </td></tr></table>")."
				</td>
			</tr>
			</table>

			<div id='category_area' style=''>
			<div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<table id='category_tab_01' class='on'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"showCategoryTab('select_category','category_tab_01');\">선택등록</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='category_tab_02'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"showCategoryTab('search_category','category_tab_02');\">검색어등록</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td class='btn'>
				</td>
			</tr>
			</table>
			</div><br>";

    $Contents .= "
			<input type='hidden' name=selected_cid value='".$cid."'>
			<input type='hidden' name=selected_depth value=''>
			<input type='hidden' id='_category' value=''>
			<input type='hidden' id='_category' value=''>
			<input type='hidden' id='basic' value=''>
			<!--input type='hidden' name=cid_1 value=''>
			<input type='hidden' name=cid_2 value=''>
			<input type='hidden' name=cid_3 value=''-->

			<table cellpadding=0 cellspacing=0  border='0' width='100%'  id='select_category' style='display:;'>
				<!--col width=15%-->
				<col width=90%>
				<tr>
					<!--td class='input_box_title'  nowrap> <b>카테고리 </b> </td-->
					<td class=''>
						<table width=100% border=0 cellpadding=0 cellspacing=0>
							<col width='20%'>
							<col width='20%'>
							<col width='20%'>
							<col width='20%'>
							<col width='20%'>
							<tr>
								<td style='padding:5px 0px 5px 0px;'>".getCategoryMultipleSelect("--1차분류--", "cid0", "cid","onChange=\"loadCategory($(this),'cid1',2)\" title='1차분류' ", 0, $cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--2차분류--", "cid1",  "cid","onChange=\"loadCategory($(this),'cid2',2)\" title='2차분류'", 1, $cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--3차분류--", "cid2", "cid", "onChange=\"loadCategory($(this),'cid3',2)\" title='3차분류'", 2, $cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--4차분류--", "cid3", "cid", "onChange=\"loadCategory($(this),'cid4',2)\" title='4차분류'", 3, $cid)."</td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--5차분류--", "cid4", "cid", "onChange=\"loadCategory($(this),'cid_1',2)\" title='5차분류'", 4, $cid)."</td>
								<!--td style='padding:5px 4px 5px 6px;vertical-align:top;'><img src='../images/".$_SESSION["admininfo"]["language"]."/category_add.gif' align=absmiddle border=0 onclick=\"categoryadd('','select')\" style='cursor:pointer;'></td-->
							</tr>
						</table>";
    $Contents .= "	</td>
				</tr>
			</table>

			<table cellpadding=0 cellspacing=0  border='0' width='100%' class='input_table_box' id='search_category' style='display:none;'>
				<col width=15%>
				<col width=90%>
				<tr>
					<td class='input_box_title' nowrap> <b>카테고리 </b> </td>
					<td class='input_box_item'>
						<table width=100% border='0' cellpadding=0 cellspacing=0>
							<col width='15%'>
							<col width='10%'>
							<col width='38%'>
							<col width='38%'>
							<tr>
								<td style='padding:5px 0px 5px 2px;'>
								<textarea name='search_category_text' id='search_category_text' style='padding:0px;height:105px;width:99%' class='tline textbox'>".$search_category."</textarea>
								</td>
								<td align='center'>
								<img src='../images/".$_SESSION["admininfo"]["language"]."/search_category.gif' align=absmiddle border=0 onclick=\"search_multcategory()\" style='cursor:pointer;'></td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--1차분류--", "search_category_list", "cid","", 0, $cid)." </td>
								<!--td style='padding:5px 4px 5px 6px;'><img src='../images/".$_SESSION["admininfo"]["language"]."/category_add.gif' align=absmiddle border=0 onclick=\"categoryadd('','search')\" style='cursor:pointer;'></td-->
							</tr>
						</table>";

    $Contents .= "	</td>
				</tr>
			</table><br>
			</div>

			<table border=0 cellpadding=0 cellspacing=0 width='100%' style='padding:5px 10px 5px 10px;border:1px solid silver' >
				<col width=100%>
				<tr>
					<td style='padding:10px 10px;'>";
    if($id != "" || true){
        if(in_array($product_type,$sns_product_type)){
            $Contents .= PrintRelation2($id);
        }else{
            $Contents .= PrintRelation($id);
        }
    }else{
        $Contents .= "<table width=100% cellpadding=0 cellspacing=0 id=objCategory >
										<col width=5>
										<col width=50>
										<col width=*>
										<col width=30>
						</table>";
    }
    $Contents .= "	</td>
				</tr>
				<tr><td class='small' height='25' style='padding-left:15px;'> <span class='small'> <!--* 첫번째 선택된 카테고리가 기본카테고리로 지정되며 라디오 버튼 클릭으로 기본카테고리를 변경 하실 수 있습니다>--> ".getTransDiscription($page_code,'E')." </span></td></tr>
			</table>
			<div style='clear:both;height:70px;'></div>
			</div>";
}
//[S] 미니샵용 카테고리 등록
if($_SESSION["admininfo"]["admin_level"] == 8 && $goods_input_type != "globalsellertool"){

    $Contents .= "
			<div id='minishop_category_div'>
			<table width='100%' cellpadding=0 cellspacing=0>
			<tr height=30>
				<td style='padding-bottom:10px;' >
				".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:0px 0px 15px 5px;padding-left:10px;'><b class=blk > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 미니샵 카테고리등록 </b><span class=small><!--하단에 카테고리를 선택하신후 카테고리 등록하기 버튼을 클릭하세요.(다중 카테고리 등록지원)-->".getTransDiscription($page_code,'D')." </span></td><td align=right style='padding-right:0px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>")."
				</td>
			</tr>
			</table>";
    if(false){
        $Contents .= "
			<div class='tab' style='".($id != "" ? "display:block;":"display:none;")."'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<table id='category_tab_01' class='on'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02'>선택등록</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td class='btn'>
				</td>
			</tr>
			</table>
			</div><br>";
    }
    $Contents .= "
			<table cellpadding=0 cellspacing=0  border='0' width='100%'   id='select_category' style='display:;'>
				<col width=15%>
				<col width=90%>
				<tr>
					<!--td class='input_box_title'  nowrap> <b>카테고리 </b> </td-->
					<td class=''>
						<table width=100% border=0 cellpadding=0 cellspacing=0>
							<col width='20%'>
							<col width='20%'>
							<col width='20%'>
							<col width='20%'>
							<col width='20%'>
							<tr>
								<td style='padding:0px 0px 5px 0px;'>".getCategoryMinishopMultipleSelect("--1차분류--", "minishop_cid0", "minishop_cid","onChange=\"loadMinishopCategory($(this),'minishop_cid1',2)\" title='1차분류' ", 0, $cid)." </td>
								<td style='padding:0px 0px 5px 2px;'>".getCategoryMinishopMultipleSelect("--2차분류--", "minishop_cid1",  "minishop_cid","onChange=\"loadMinishopCategory($(this),'minishop_cid2',2)\" title='2차분류'", 1, $cid)." </td>
								<td style='padding:0px 0px 5px 2px;'>".getCategoryMinishopMultipleSelect("--3차분류--", "minishop_cid2", "minishop_cid", "onChange=\"loadMinishopCategory($(this),'minishop_cid3',2)\" title='3차분류'", 2, $cid)." </td>
								<td style='padding:0px 0px 5px 2px;'>".getCategoryMinishopMultipleSelect("--4차분류--", "minishop_cid3", "minishop_cid", "onChange=\"loadMinishopCategory($(this),'minishop_cid4',2)\" title='4차분류'", 3, $cid)."</td>
								<td style='padding:0px 0px 5px 2px;'>".getCategoryMinishopMultipleSelect("--5차분류--", "minishop_cid4", "minishop_cid", "onChange=\"loadMinishopCategory($(this),'minishop_cid_1',2)\" title='5차분류'", 4, $cid)."</td>
								<!--td style='padding:0px 4px 5px 6px;vertical-align:top;'><img src='../images/".$_SESSION["admininfo"]["language"]."/category_add.gif' align=absmiddle border=0 onclick=\"categoryadd('M')\" style='cursor:pointer;'></td-->
							</tr>
						</table>";
    $Contents .= "	</td>
				</tr>
			</table>

			<table cellpadding=0 cellspacing=0  border='0' width='100%' class='input_table_box' id='search_category' style='display:none;'>
				<!--col width=15%-->
				<col width=90%>
				<tr>
					<!--td class='input_box_title' nowrap> <b>카테고리 </b> </td-->
					<td class='input_box_item'>
						<table width=100% border='0' cellpadding=0 cellspacing=0>
							<col width='15%'>
							<col width='10%'>
							<col width='38%'>
							<col width='38%'>
							<tr>
								<td style='padding:5px 0px 5px 2px;'>
								<textarea name='search_category_text' id='search_category_text' style='padding:0px;height:105px;width:99%' class='tline textbox'>".$search_category."</textarea>
								</td>
								<td align='center'>
								<img src='../images/".$_SESSION["admininfo"]["language"]."/search_category.gif' align=absmiddle border=0 onclick=\"search_multcategory()\" style='cursor:pointer;'></td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--1차분류--", "search_category_list", "cid","", 0, $cid)." </td>
								<!--td style='padding:5px 4px 5px 6px;'><img src='../images/".$_SESSION["admininfo"]["language"]."/category_add.gif' align=absmiddle border=0 onclick=\"categoryadd()\" style='cursor:pointer;'></td-->
							</tr>
						</table>";

    $Contents .= "	</td>
				</tr>
			</table><br>
			

			<table border=0 cellpadding=0 cellspacing=0 width='100%' style='padding:5px 10px 5px 10px;border:1px solid silver' >
				<col width=100%>
				<tr>
					<td style='padding:10px 10px;'>";
    if($id != ""){
        $Contents .= PrintMinishopRelation($id);
    }else{
        $Contents .= "<table width=100% cellpadding=0 cellspacing=0 id=minishop_objCategory >
										<col width=5>
										<col width=50>
										<col width=*>
										<col width=30>
						</table>";
    }
    $Contents .= "	</td>
				</tr>
				<tr><td class='small' height='25' style='padding-left:15px;'> <span class='small'> <!--* 첫번째 선택된 카테고리가 기본카테고리로 지정되며 라디오 버튼 클릭으로 기본카테고리를 변경 하실 수 있습니다>--> ".getTransDiscription($page_code,'E')." </span></td></tr>
			</table>
			<div style='clear:both;height:70px;'></div>
			</div>";

}
//[E] 미니샵용 카테고리 등록

$Contents .= 	"<div id='cateogry_add_infomation_area'>".displayCategoryAddInfomation($cid, $depth, $category_add_infos)."</div>";
$Contents .= 	"<div id='standard_cateogry_add_infomation_area'>".displayStandardCategoryAddInfomation()."</div>";


if($admininfo["admin_level"] == '9'){
    $Contents .= "	
			<table width='100%' cellpadding=0 cellspacing=0>
			<tr height=30>
				<td style='padding-bottom:10px;'>
					".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><b class=blk> <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> MD 설정 <a name='mdSet'></a></b><span class=small>".getTransDiscription($page_code,'Z')." </span></td><td align=right style='padding-right:20px;' class=small></td></tr></table>")."
				</td>
			</tr>
			</table>";

    $Contents .= "
			<table cellpadding=0 cellspacing=0  border=0 width='100%' class='input_table_box'>
				<col width=15%>
				<col width=90%>";
    if($_SESSION["admin_config"][front_multiview] == "Y"){
        $Contents .= "
	<tr>
		<td class='input_box_title' > 프론트 전시 구분</td>
		<td class='input_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
	</tr>";
    }
    $Contents .= "";

    $Contents .= "
				<tr>
					<td class='input_box_title'> MD설정 </td>
					<td class='input_box_item' style='line-height:150%' colspan=3>
					".MDSelect($md_code)." 
					</td>
				</tr>
			</table>
			<div style='clear:both;height:70px;'></div>";

}else{
    $Contents .= "<input type='hidden' name='md_code' id='md_code' value='".$md_code."'>";
}

$Contents .= "
				<table width='100%' cellpadding=0 cellspacing=0  >
				<tr height=30>
					<td style='padding-bottom:10px;'>
						".colorCirCleBox("#efefef","100%","<table cellpadding=0 cellspacing=0 width=100%><tr><td style='padding:5px 5px 5px 10px;'><b class=blk > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 기본정보 : <a name='basicInfo'></a></b><span class=small><!--굵은 글씨로 되어 있는 항목이 필수 정보입니다.--> ".getTransDiscription($page_code,'F')." </span> </td><td align=right style='padding-right:0px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>")."
					</td>
				</tr>
				</table>

				<table cellpadding=0 cellspacing=0 width='100%' class='input_table_box' id='product_type_use'>
				<col width=15%>
				<col width=35%>
				<col width=15%>
				<col width=35%>";

if($id){
    $Contents .= "
				<tr>
					<td class='input_box_title' nowrap> <b>상품등록일/최종수정일</b> </td>
					<td class='input_box_item'>".$regdate." / ".$editdate."</td>
					<td class='input_box_title'> 상품시스템코드(자동생성) </td>
					<td class='input_box_item'>
						".$id."
					</td>
				</tr>";
}
$Contents .= "
				<tr>
					<td class='input_box_title' nowrap> <b>상품명 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item' style='padding:5px 10px;line-height:200%'>
						<input type=text class='textbox' name=pname size=28 style='width:88%' value='$pname' validation=true title='제품명' maxlength=100 placeholder='기본상품명(50자이내)'>";

if(count($language_list) > 0){
//    $Contents .= "<img src='/admin/images/btn_group_open.png' open_src='/admin/images/btn_group_close.png' close_src='/admin/images/btn_group_open.png' alt='Minus' align=absmiddle style='margin-left:5px;' onclick=\"moreDisplay(this, 'input#global_pinfo_pname');\">";

    foreach($language_list as $key => $li){
        //if ($key != 0) $Contents .= "<br/>";

        $_name = $li['language_code'].'_pname';
        $_value = $$_name;

        $Contents .= "<input type=text class='textbox' id='global_pinfo_pname' name=\"".$_name."\" size=28 maxlength=100 style='width:88%;margin-top:3px;' value='".$_value."' validation=false title='글로벌 상품명 (".$li[language_name].")' placeholder='".$li[language_name]."(50자이내)' >";
    }
}


$Contents .= "
					</td>
					<td class='input_box_title'> <b>상품코드</b></td>
					<td class='input_box_item' style='padding:10px;'>
						<table cellpadding=0 cellspacing=0 border='0' width='50%'>
							<tr>
								<td>
									<input type=text class='textbox' name=pcode  style='width:100px;' value='$pcode' validation=false title='상품코드'>
								</td>
								<td > 
									<div class='pcode_stockinfo_loade' style='display:none;cursor:pointer;text-align:center;margin-left:5px;' onclick=\"ShowModalWindow('../inventory/inventory_search.php?type=goods',1000,690,'inventory_search')\">
									<img src='../images/korean/stock.gif' alt='재고정보불러오기' title='재고정보불러오기' />
									</div>
								</td>
							</tr>
							<tr>
								<td style='padding-top:3px;' colspan='2'>
									<div class='pcode_stockinfo_loade' style='display:none;cursor:pointer;text-align:center;'>
									<input type=text class='textbox'  style='width:100px;'  value='$gid' name='gid' id='gid' title='품목시스템코드' readonly /> * WMS 품목코드
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>";

$Contents .= "
                <tr >
                    <td class='input_box_title'> 상품머리말 </td>
                    <td class='input_box_item' style='padding:10px 10px;line-height:150%' colspan=3>
					<div>
						진하게 <input type='checkbox' name='b_preface' ".("Y" == $b_preface ? "checked":"").">
						기울기 <input type='checkbox' name='i_preface' ".("Y" == $i_preface ? "checked":"").">
						밑줄 <input type='checkbox' name='u_preface' ".("Y" == $u_preface ? "checked":"").">
						색상코드 <input type='text' name='c_preface' id='c_preface' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_preface ? "#000000":$c_preface)."'>
					</div>
					
                    <input type=text class='textbox' name='preface' style='width:90%' value='$preface' placeholder='한국어'><br>";

                if(count($language_list) > 0){
                    foreach($language_list as $key => $li){
                        $_name = $li['language_code'].'_preface';
                        $_value = $$_name;

                        $Contents .= "<input type=text class='textbox' name=\"".$_name."\" size=28 style='width:90%;margin-top:3px;' value='".$_value."' validation=false title='글로벌 상품머리말 (".$li[language_name].")' placeholder='".$li[language_name]."' >";
                    }
                }

$Contents .= "		
				<tr >
					<td class='input_box_title' id='add_info_title'> 색상명 </td>
					<td class='input_box_item' style='padding:10px 10px;line-height:150%' colspan=3>
					<input type=text class='textbox' name='add_info' style='width:90%' value='$add_info' placeholder='한국어'><br>";

if(count($language_list) > 0){
    foreach($language_list as $key => $li){
        $_name = $li['language_code'].'_add_info';
        $_value = $$_name;

        $Contents .= "<input type=text class='textbox' name=\"".$_name."\" size=28 style='width:90%;margin-top:3px;' value='".$_value."' validation=false title='글로벌 추가정보 (".$li[language_name].")' placeholder='".$li[language_name]."' >";
    }
}

$Contents .= "				
				</tr>
				<tr id='product_77_keyword'>
					<td class='input_box_title'> 검색키워드 </td>
					<td class='input_box_item' style='padding:10px 10px;line-height:150%' colspan=3>
					<input type=text class='textbox' name='search_keyword' style='width:90%' value='$search_keyword' placeholder='한국어'><br>";

if(count($language_list) > 0){
    foreach($language_list as $key => $li){
        $_name = $li['language_code'].'_search_keyword';
        $_value = $$_name;

        $Contents .= "<input type=text class='textbox' name=\"".$_name."\" size=28 style='width:90%;margin-top:3px;' value='".$_value."' validation=false title='글로벌 검색키워드 (".$li[language_name].")' placeholder='".$li[language_name]."' >";
    }
}
$Contents .= "	
					<br>※<span class=small > <!--검색어를 등록하시면 검색이 검색어가 같이 포함되어 노출되게 됩니다-->  ".getTransDiscription($page_code,'I')." </span></td>
				</tr>
				<!--tr id='product_77_keyword'>
					<td class='input_box_title'> 상품 Color chip </td>
					<td class='input_box_item' style='padding:10px 10px;line-height:150%' colspan=3>
						<input type='text' class='textbox' name='product_color_chip' value='$product_color_chip' maxlength='200'>
					</td>
				</tr>
				<tr bgcolor='#ffffff' id='product_77_shotinfo' height=50>
					<td class='input_box_title' nowrap> 상품간략소개 </td>
					<td class='input_box_item' colspan='3'>
					<textarea name='shotinfo' style='padding:3px;height:30px;width:98%' class='textbox'>".$shotinfo."</textarea>
					</td>
				</tr-->
				<tr>
					<td class='input_box_title'> 원산지 </td>
					<td class='input_box_item' style='line-height:150%'>
					".OriginSelect($origin,'origin','origin_name','og_ix','validation=false')." 
					</td>
					<td class='input_box_title'> 세탁주의관리 </td>
					<td class='input_box_item' style='line-height:150%'>
					".getLaundryList("OneDepth", "laundry_one_depth", "onChange=\"loadLaundry('laundry_one_depth','laundry_two_depth','laundry')\" title='선택' ", '0', substr($laundry_cid,0,3)."000000000000",'laundry')."
					".getLaundryList("TwoDepth", "laundry_two_depth", "title='선택' ", '1', substr($laundry_cid,0,6)."000000000",'laundry')."
					</td>
					<!--td class='input_box_title'> 제조사 <img src='".$required3_path."'></td>
					<td class='input_box_item'>
					<input type='text' class='textbox' style='width:140px;'name='company' id='company' title='제조사' validation=true value='".$company."'>
					</td-->
				</tr>
				<tr>
					<td class='input_box_title'> 브랜드 </td>
					<td class='input_box_item'>
					".BrandListSelect($brand, $cid)."
					</td>
					<td class='input_box_title'> 면세제품 <img src='".$required3_path."'></td>
					<td class='input_box_item'>
						<input type=radio  name=surtax_yorn value='N' id='surtax_yorn_n' ".($surtax_yorn == "N" ? "checked":"")."> <label for='surtax_yorn_n'>과세</label> 
						<input type=radio  name=surtax_yorn value='Y' id='surtax_yorn_y' ".($surtax_yorn == "Y" ? "checked":"")."> <label for='surtax_yorn_y'>면세</label>  
						<!--PG 및 정리가 안되서 주석처리! 영세 사용시 주문할때 PG 에서 과세 비과세 부분 수정해야하함! 2014-05-31 Hong-->
						<!--input type=radio  name=surtax_yorn value='P' ".($surtax_yorn == "P" ? "checked":"")."> 영세 --> 
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> <b>옵션유형 선택 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' style='padding:4px 10px 4px 10px;' id='option_type_td' ".($product_type == "77" ? "":"colspan=3")." nowrap>
						<table  cellpadding=3 cellspacing=0 border=0 width='100%' >
							<tr>
								<td align=left height=25>";

if($admininfo["admin_level"] == '9'){
    /*
    $Contents .= "
									<input type=radio  name=stock_use_yn value='N' id='stock_use_n' ".($stock_use_yn == "N" ? "checked":"")." onclick=\"stockCheck('N')\"><label for='stock_use_n'>기본옵션</label>";
    */
}

$Contents .= "
    <div id='stock_use_q_area' style='display:none;'>
									<span class='helpcloud' help_height='50' help_html='<b>빠른재고관리 란?</b> 재고 입출고의 내역은 관리하지 않고 재고 수량만 관리하는 기능입니다.'>
									<input type=radio  name=stock_use_yn value='Q' id='stock_use_q'  ".($stock_use_yn == "Q" ? "checked":"")." onclick=\"stockCheck('Q')\">
									<label for='stock_use_q'>가격+재고 옵션</label>
									</span>
									</div>
									";

if($_SESSION["admin_config"][mall_use_inventory] == "Y"){
    //if($_SESSION["admininfo"][admin_level] == 9){
    $Contents .= "
<div id='stock_use_y_area' >
									<span class='helpcloud' help_height='30' help_html='별도의 WMS(재고관리시스템) 과 연동되어 재고관리가 진행됩니다.'>
									<input type=radio  name=stock_use_yn value='Y' id='stock_use_y'  ".($stock_use_yn == "Y" || $stock_use_yn == "" ? "checked":"")." onclick=\"stockCheck('Y')\"><label for='stock_use_y'>WMS 옵션</label>
									</span>
									</div>
									";
    //}
}

$Contents .= "
								</td>
							</tr>
							<tr style='display:none;'>
								<td align=left id='stock_input_area' style='padding-left:0px;".($stock_use_yn == "Y" || $stock_use_yn == "" ? "":"display:inline;")."'>";
if($admininfo['admin_level'] == '8'){
    $Contents .= "
										<input type=\"text\" class='textbox integer numeric' size=10 name=stock value='$stock' id='stock' style='".($_SESSION["layout_config"]["mall_use_inventory"] == "Y" && $stock_use_yn != "Q" ? "background:#efefef;":"")."' ".(($stock_use_yn == "N" || $_SESSION["layout_config"]["mall_use_inventory"] == "Y") && $stock_use_yn != "Q"? "readonly":"")." validation=false title='재고수량'>";
    $Contents .= "
										<input type='hidden' class='textbox integer numeric' size=10 name=available_stock value='".$available_stock."' id='available_stock'>
										<input type='hidden' class='textbox integer numeric' size=10 name=remain_stock value='".$remain_stock."' id='remain_stock'  >
									";
}else{
    $Contents .= "
								<table  cellpadding=0 cellspacing=0 border=0 width=50% class='input_table_box'>";

    if($admininfo['admin_level'] == '9'){
        $Contents .= "
									<col width='35%'>
									<col width='*'>";
        /*
        $Contents .= "
        <col width='100px'>
        <col width='100px'>
        <col width='100px'>
        <col width='100px'>
        <col width='100px'>
        <col width='100px'>
        <col width='100px'>
        <col width='100px'>
        <col width='100px'>
        <col width='100px'>";
        */
    }else{
        $Contents .= "
									<col width='18%'>
									<col width='*'>";
    }
    $Contents .= "
									<tr >";
    $Contents .= "<tr>
										<td class='input_box_title ctr' style='padding:0px;'>
										재고 <img src='".$required3_path."'>
										</td>
										<td class='input_box_item'>
											<input type=\"text\" class='textbox integer numeric' size=10 name=stock value='$stock' id='stock' style='".($_SESSION["layout_config"]["mall_use_inventory"] == "Y" && $stock_use_yn != "Q" ? "background:#efefef;":"")."' ".(($stock_use_yn == "N" || $_SESSION["layout_config"]["mall_use_inventory"] == "Y") && $stock_use_yn != "Q"? "readonly":"")." validation=false title='재고수량'>";
    if($admininfo['admin_level'] == '9'){
        $Contents .= "
											<img src='/admin/images/btn_group_open.png' open_src='/admin/images/btn_group_close.png' close_src='/admin/images/btn_group_open.png' alt='Minus' align=absmiddle style='margin-left:5px;' onclick=\"moreDisplay(this, 'tr#stock_info');\">";
    }
    $Contents .= "
										</td>
										</tr>";
    if($admininfo['admin_level'] == '9'){
        $Contents .= "<tr id='stock_info' style='display:none;'>
										<td class='input_box_title ctr' style='padding:0px;'>
										안전재고
										</td>
										<td class='input_box_item '>
											<input type=\"text\" class='textbox integer numeric' size=10 name=safestock value='$safestock' id='safestock' ".($stock_use_yn == "N" ? "readonly":"").">
										</td>
										</tr>";

        $Contents .= "<tr id='stock_info' style='display:none;'>
										<td class='input_box_title ctr' style='padding:0px;'>
										판매진행재고
										</td>
										<td class='input_box_item '>
											<input type=\"text\" class='textbox integer numeric' size=10 name=sell_ing_cnt value='".($mode!="copy"?$sell_ing_cnt:"0")."' id='sell_ing_cnt' ".($stock_use_yn == "N" ? "readonly":"readonly")." onclick=\"alert('판매진행중 재고는 임의로 수정하실 수 없습니다.');\"><!-- 상품 복사를 하면 판매진행중재고는 0 처리 kbk 13/07/01 -->
										</td></tr>";
    }

    if($admininfo['admin_level'] == '9'){
        $Contents .= "<tr id='stock_info' style='display:none;'>
										<td class='input_box_title ctr' style='padding:0px;'>
										가용재고
										</td>
										<td class='input_box_item '>
										<input type='text' class='textbox integer numeric' size=10 name=available_stock value='".$available_stock."' id='available_stock'>
										</td>
										</tr>";

        $Contents .= "<tr id='stock_info' style='display:none;'>
										<td class='input_box_title ctr' style='padding:0px;'>
										남은가용재고
										</td>
										<td class='input_box_item '>
										<input type='text' class='textbox integer numeric' size=10 name=remain_stock value='".$remain_stock."' id='remain_stock'  >
										</td>
										</tr>";
    }else{
        $Contents .= "
										<input type='hidden' class='textbox integer numeric' size=10 name=available_stock value='".$available_stock."' id='available_stock'>
										<input type='hidden' class='textbox integer numeric' size=10 name=remain_stock value='".$remain_stock."' id='remain_stock'  >
									";

    }
    $Contents .= "
									</tr>
								</table>";
}
if(false){
    $Contents .= "
								<div class='small' style='padding:5px 2px 2px 2px;line-height:140%;'>
									<!--* 재고가 0 이라면 품절상태가 됨--> ".getTransDiscription($page_code,'U')."<br>
									* 빠른재고는 WMS를 사용하지 않더라도 재고를 사용할 수 있습니다.<br/>
									* 판매진행재고란 ? 상품을 발송하기 전의 재고상태를 말하며, 입금전 수량과 임금후의 주문수량을 파악할 수 있으며, 상품을 배송중 처리시 자동으로 재고에서 차감됩니다.<br/>
									* 재고란 ? 실재고를 입력하고 주문상품의 실재 배송처리를 할 경우에 재고는 차감됩니다. 재고 수량이 0일 경우 혹은 (재고-판매진행재고가=0) 일 경우는 <br>프론트에서 고객이 주문시 자동으로 품절상태를 알려줍니다.<br/>
									*가용재고란? 재고간 0이 될경우 품절로 고객이 주문을 할 수 없을때 - 재고를 설정하여 재고가 0이여도 주문을 넣을수 있개 해주는 수량입니다.
									 ".( false ? getTransDiscription($page_code,'H') : "")." 
								</div>";
}
$Contents .= "
								</td>
								<td style='padding:5px 2px 2px 2px'></td>
							</tr>
						</table>
					</td>
					<td class='input_box_title' id='gift_qty_td1' ".($product_type == "77" ? "style='display:block;'":"style='display:none;'")."> <b>수량</b></td>
					<td class='input_box_item' id='gift_qty_td2' ".($product_type == "77" ? "style='display:block;'":"style='display:none;'").">
                        <input type=text class='textbox' style='width:100px;' value='$gift_qty' name='gift_qty' id='gift_qty' title='사은품 수량' />  
                    </td>
				</tr>
				<tr>
					<td class='input_box_title'> <b>판매상태 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' style='padding-top:5px;'>".SellState($state,"radio",$id)."</td>
					<td class='input_box_title'> <b>재고자동연동</b></td>
					<td class='input_box_item'>
					    <input type=radio  name=auto_sync_wms value='Y' id='auto_sync_wms_y' ".($auto_sync_wms == "Y" ? "checked":"")."> <label for='auto_sync_wms_y'>연동</label>
						<input type=radio  name=auto_sync_wms value='N' id='auto_sync_wms_n' ".($auto_sync_wms == "N" ? "checked":"")."> <label for='auto_sync_wms_n'>비연동</label>  
                    </td>
				</tr>
				<tr>
					<td class='input_box_title'> <b>노출여부 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' ".($admininfo[admin_level] != '9'?'colspan="3"':'').">".displayProduct($disp)."</td>";

if($admininfo[admin_level] == '9'){

    /*
        is_mobile_use
        A : PC+모바일+APP
        M : 모바일+APP
        W : PC
        B : 모바일
        P : APP
        H : PC+모바일
        L : PC+APP
    */
    $Contents .="
					<td class='input_box_title'> <b>모바일상품여부</b></td>
					<td class='input_box_item'>
						<input type='hidden' name='is_mobile_use' id='is_mobile_use' value='".$is_mobile_use."' > 
						<input type='checkbox' class='is_mobile_use' view_type='W' ".($is_mobile_use == "A" || $is_mobile_use == "W" ? "checked" : "")." id='is_mobile_use_w' > 
						<label for='is_mobile_use_w'> 웹</label>
						<input type='checkbox' class='is_mobile_use' view_type='M' ".($is_mobile_use != "W" ? "checked" : "")." id='is_mobile_use_m' > 
						<label for='is_mobile_use_m'> 모바일</label>
						(
							<input type='checkbox' class='is_mobile_use' view_type='B' ".($is_mobile_use != "W" && $is_mobile_use != "P" && $is_mobile_use != "L" ? "checked" : "")." id='is_mobile_use_b' > 
							<label for='is_mobile_use_b'> WEB</label>
							<input type='checkbox' class='is_mobile_use' view_type='P' ".($is_mobile_use != "W" && $is_mobile_use != "B" && $is_mobile_use != "H" ? "checked" : "")." id='is_mobile_use_p' > 
							<label for='is_mobile_use_p'> APP</label>
						)

						<script type='text/javascript'>
						<!--
							$('.is_mobile_use').on({
								click : function(){
									is_mobile_use_click($(this));
								}
							})

							function is_mobile_use_click(tisOhj){
								
								var click_view_type = tisOhj.attr('view_type');
								if(click_view_type=='M'){
									if(tisOhj.is(':checked')){
										$('.is_mobile_use[view_type=B],.is_mobile_use[view_type=P]').attr('checked',true);
									}else{
										$('.is_mobile_use[view_type=B],.is_mobile_use[view_type=P]').attr('checked',false);
									}
								}

								if(click_view_type=='B' || click_view_type=='P'){
									if(tisOhj.is(':checked')){
										$('.is_mobile_use[view_type=M]').attr('checked',true);
									}else{
										if($('.is_mobile_use[view_type=B]:checked,.is_mobile_use[view_type=P]:checked').length > 0){
											$('.is_mobile_use[view_type=M]').attr('checked',true);
										}else{
											$('.is_mobile_use[view_type=M]').attr('checked',false);
										}
									}
								}

								if($('.is_mobile_use[view_type=W]:checked,.is_mobile_use[view_type=B]:checked,.is_mobile_use[view_type=P]:checked').length == 3){
									$('#is_mobile_use').val('A');
								}else if($('.is_mobile_use[view_type=B]:checked,.is_mobile_use[view_type=P]:checked').length == 2){
									$('#is_mobile_use').val('M');
								}else if($('.is_mobile_use[view_type=W]:checked,.is_mobile_use[view_type=B]:checked').length == 2){
									$('#is_mobile_use').val('H');
								}else if($('.is_mobile_use[view_type=W]:checked,.is_mobile_use[view_type=P]:checked').length == 2){
									$('#is_mobile_use').val('L');
								}else if($('.is_mobile_use[view_type=W]:checked').length == 1){
									$('#is_mobile_use').val('W');
								}else if($('.is_mobile_use[view_type=B]:checked').length == 1){
									$('#is_mobile_use').val('B');
								}else if($('.is_mobile_use[view_type=P]:checked').length == 1){
									$('#is_mobile_use').val('P');
								}else{
									$('#is_mobile_use').val('A');
								}
							}
						//-->
						</script>
					</td>";

    /*
    커스텀 마이징 로 인한 주석처리
    $Contents .="
        <td class='input_box_title'> <b>모바일상품여부</b></td>
        <td class='input_box_item'>
            <input type='radio' name='is_mobile_use' value='A' id='is_mobile_use_a' ".($is_mobile_use == "A" || $is_mobile_use == "" ? "checked":"")." >
            <label for='is_mobile_use_a'> 전체</label>
            <input type='radio' name='is_mobile_use' value='M' id='is_mobile_use_m' ".($is_mobile_use == "M" ? "checked":"")." > <label for='is_mobile_use_m'> 모바일 </label>
            <input type='radio' name='is_mobile_use' value='W' id='is_mobile_use_w' ".($is_mobile_use == "W" ? "checked":"")." > <label for='is_mobile_use_w'> 웹 </label>
        </td>";
    */

}

$Contents .="
				</tr>
				<tr id=''>
					<td class='input_box_title'> 판매기간 </td>
					<td class='input_box_item' style='padding:10px;' colspan='3'>
						<table cellpadding='0' cellspacing='2' border='0' bgcolor=#ffffff >
							<tr>
								<td nowrap>";
                                if($product_type != '77') {
                                    $Contents .= "<span id='no_sell_date'><input type='radio' name='is_sell_date' value='0' id='is_sell_date_0' " . ($is_sell_date == "0" || $is_sell_date == "" ? "checked" : "") . " onclick=\"$('*[id^=sell_priod]').attr('disabled','disabled');$('td.sell_proid_area').hide();\" style='background-color:red;'> <label for='is_sell_date_0'>미적용</label></span>";
                                }
                                $Contents .="<span id='sell_date'><input type='radio' name='is_sell_date' value='1' id='is_sell_date_1' ".($is_sell_date == "1" ? "checked":"")." onclick=\"$('*[id^=sell_priod]').attr('disabled',false);$('td.sell_proid_area').show();\"> <label for='is_sell_date_1'>적용</label></span>
								</td>
							</tr>
							<tr>
								<td class='sell_proid_area' style='padding:5px 5px 5px 0px;".($is_sell_date == "1" ? "" : "display:none;")."' nowrap>
									<div>
									".search_date('sell_priod_sdate','sell_priod_edate',$sell_priod_sdate,$sell_priod_edate,'Y',"A", ($is_sell_date == "0" || $is_sell_date == "" ? "disabled title='판매기간'  ":" title='판매기간'  "))."</div>
									<div style='clear:both;'>
									- 판매시작 날짜가 현재일 이전이면 상품이 등록되지 않습니다.<br>
									</div>
								</td>
							</tr>
						</table>
						
					</td>
					<!--
					<td class='input_box_title'> 상품무게 </td>
					<td class='input_box_item' style='padding:10px;'>
						<input type='text' name='product_weight' value='".$product_weight."' /> Kg
					</td>
					-->
				</tr>
				<tr>
                    <td class='input_box_title'> 동영상 URL </td>
                    <td class='input_box_item' colspan=3 style='line-height:150%'>                    
                    <input type=text class='textbox' name='movie' style='width:80%' value='".$movie."'>
                    <label><input type='checkbox' value='Y' name='movie_now' ".($movie_now == "Y" ? "checked" : "").">동영상 바로노출</label>
                    </td>
                </tr>
				<tr>
                    <td class='input_box_title'> 동영상 썸네일 URL </td>
                    <td class='input_box_item' colspan=3 style='line-height:150%'>
                    <input type=text class='textbox' name='movie_thumbnail' style='width:80%' value='".$movie_thumbnail."'></td>
                </tr>
                <tr>
                    <td class='input_box_title'> 교환/반품 여부 </td>
                    <td class='input_box_item' colspan='3' style='line-height:150%'>
                        <input type='checkbox' name='exchangeable_yn' ".($exchangeable_yn == "N" ? "checked" : "")." id='exchangeable_yn' value='N' > 교환신청 불가능
                        <input type='checkbox' name='returnable_yn' ".($returnable_yn == "N"  ? "checked" : "")." id='returnable_yn' value='N' > 반품신청 불가능
                    </td>
                </tr>
                <tr>
                    <td class='input_box_title'> 관리자 메모 </td>
                    <td class='input_box_item' colspan=3 style='line-height:150%'>
                    <textarea type=text class='textbox' name='admin_memo' id='admin_memo' style='width:90%'>".$admin_memo."</textarea>
                    </td>
                </tr>
                
				";
if(false){
    $Contents .=getStyle("style[]",$style,"","checkbox",$style_info)."";
}
$Contents .="
				</table>
				<div style='clear:both;height:70px;'></div>
				<table width='100%' cellpadding=0 cellspacing=0>
				<tr height=30>
					<td style='padding-bottom:10px;'>
						".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><b style='cursor:pointer;' class=blk> <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 착장정보 </b></div>")."
					</td>
				</tr>
				</table>
				<table cellpadding=0 cellspacing=0 width='100%'>
				<tr>
					<td><textarea name='wear_info' style='width:98%;height:350px;'>".$wear_info."</textarea></td>
				</tr>
				</table>

				<div style='clear:both;height:70px;'></div>
				<table width='100%' cellpadding=0 cellspacing=0>
				<tr height=30>
					<td style='padding-bottom:10px;'>
						".colorCirCleBox("#efefef","100%","<table cellpadding=0 cellspacing=0 width=100%><tr><td style='padding:5px 5px 5px 10px;'><b class=blk > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 추가정보  </b><a name='addInfo'></a><span class=small></span> </td><td align=right style='padding-right:0px;' class=small> </td>
						<td align=right style='padding:0px 0px 5px 0px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--><img src='/admin/images/btn_group_open.png' open_src='/admin/images/btn_group_close.png' close_src='/admin/images/btn_group_open.png' alt='Minus' align=absmiddle style='margin-left:5px;' onclick=\"moreDisplay(this, 'table#add_goods_info','div#add_goods_info_result');\"></td>
						</tr></table>")."
					</td>
				</tr>
				</table>
				<div id='add_goods_info_result' style='padding:20px;border:1px solid silver;margin-bottom:10px;'><b style='color:#ff2a32'> [GUIDE]</b> 변경을 위해서는 설정하기 버튼을 클릭하세요</div>
				<table cellpadding=0 cellspacing=0 width='100%' class='input_table_box' id='add_goods_info' style='display:none;'>
				
				";

if($_SESSION["admininfo"][admin_level] == 9){
    if($_SESSION["admininfo"][mall_type] == "B" || ($_SESSION["admininfo"][mall_type] == "O" || $_SESSION["admininfo"][mall_type] == "E") || $_SESSION["admininfo"][mall_type] == "BW"){//도매형 추가 kbk 12/06/04
        $Contents .= "<tr class='hide_class'>
						<td class='input_box_title' nowrap> <b>셀러업체 <img src='".$required3_path."'></b></td>
						<td class='input_box_item'>
						".companyAuthList($admin , "validation=true title='셀러업체' ",'company_id','company_id','com_name','input')."
						</td>
						<td class='input_box_title'> <b>매입업체 </b></td>
						<td class='input_box_item' >".TradeCompanyList($trade_admin)."</td>
						
					</tr>";
    }
}else{
    $Contents .= "<input type='hidden' name='company_id' id='company_id' value='".$admin."'>
									<input type='hidden' name='trade_admin' id='trade_admin' value='".$trade_admin."'>
								";
}
$Contents .= "
				
				<tr class='hide_class'>
					<td class='input_box_title'> <span class='helpcloud' help_height='60' help_html='<b>매입상품명 이란?</b> 재고상품의 품목명을 의미하며 그외 도매처에서는 장기명으로 칭하기도함.'>매입상품명</span> </td>
					<td class='input_box_item'>
					<input type=text class='textbox' name=paper_pname size=28 style='width:90%' value='$paper_pname'  title='장기명'>
					</td>
					<td class='input_box_title'> <b>바코드  </b></td>
					<td class='input_box_item' ><input type=text class='textbox' name=barcode size=28 style='width:150px;' value='$barcode' validation=false title='바코드'></td>
				</tr>

				";

if($_SESSION["admininfo"][admin_level] == 9 && false){
    $Contents .="	<tr id='product_77_dc'>
					<td class='input_box_title'><b>배송쿠폰 사용여부</b></td>
					<td class='input_box_item' >
						<input type=radio  name=delivery_coupon_yn value='Y' id='nointerest_y'  ".($delivery_coupon_yn == "Y" || $delivery_coupon_yn == "" ? "checked":"").">
						<label for='nointerest_y'>사용함</label>
						<input type=radio  name=delivery_coupon_yn value='N' id='nointerest_n'   ".($delivery_coupon_yn == "N" ? "checked":"").">
						<label for='nointerest_n'>사용안함</label>
					</td>
					<td class='input_box_title'><b>상품쿠폰 사용여부</b></td>
					<td class='input_box_item'>
						<input type=radio  name=coupon_use_yn value='Y' id='coupon_use_y'  ".($coupon_use_yn == "Y" || $coupon_use_yn == ""? "checked":"")." ".($product_type=="99"?"disabled":"").">
						<label for='coupon_use_y'>사용함</label>
						<input type=radio  name=coupon_use_yn value='N' id='coupon_use_n'  ".($coupon_use_yn == "N" || $product_type=="99" ? "checked":"")." ".($product_type=="99"?"disabled":"").">
						<label for='coupon_use_n'>사용안함</label>
					</td>
				</tr>";
}else{
    $Contents .="
				<input type='hidden' name='coupon_use_yn' value='".$coupon_use_yn."'>
				<input type='hidden' name='delivery_coupon_yn' value='".$delivery_coupon_yn."'>
				";
}

$Contents .="
				
				
				<!--
				<tr id='option_use_setting' style='display:'>
					<td class='input_box_title'> 가격재고 옵션 사용설정</td>
					<td class='input_box_item' ' style='padding:4px 0px 4px 5px;' colspan=3 nowrap>
						<table  cellpadding=3 cellspacing=0 border=0 >
							<tr>
								<td align=left height=25>
									<input type=radio  name='option_use' value='n' id='option_use_n' checked onclick=\"stockCheck('N')\"><label for='option_use_n'>미사용</label>
									<input type=radio  name='option_use' value='y' id='option_use_y'  onclick=\"stockCheck('N')\"><label for='option_use_y'>사용 </label>
								</td>
							</tr>
							<tr>
								<td align=left id='stock_input_area' style='padding-left:5px;'>
								<div class='small' style='padding:5px 2px 2px 2px;line-height:140%;'>
								
									1) 미사용 : 상품 기본 옵션 + 추가 상품등록 기능 사용가능<br/>
									2) 가격재고옵션 : 상품옵션이 2가지 이상으로 옵션 등록이 필요할때 사용<br/>
									3) 가격재고옵션 + 추가상품등록옵션 : 상품옵션이 2가지 이상이며, 추가 구성상품으로 기타 상품을 등록하여 사용 가능<br/>
									* 기본옵션(재고무관)은 모두 적용 사용 가능함
									
								</div>
								</td>
								
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> 도매/소매 <img src='".$required3_path."'></td>
					<td class='input_box_item' colspan=3><input type=radio  name=wholesale_yn value='A' checked > 전체<input type=radio  name=wholesale_yn value='Y' ".($wholesale_yn == "Y" ? "checked":"")."> 도매 <input type=radio  name=wholesale_yn value='N' ".($wholesale_yn == "N" ? "checked":"")."> 소매</td>
					<td class='input_box_title'> 오프라인 상품 등록여부 </td>
					<td class='input_box_item'><input type=radio  name=offline_yn value='Y' checked > 포스판매 사용<input type=radio  name=offline_yn value='N' ".($offline_yn == "N" ? "checked":"").">포스판매 사용안함</td>
				</tr-->
				";

if($goods_input_type != "globalsellertool"){
    if($_SESSION["admininfo"][admin_level] == 9){
        $Contents .="
						<tr>
							<td class='input_box_title' > 아이콘노출<br><br>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:PoPWindow('../design/product_icon.php?mmode=pop',960,700,'brand')\"'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_pop_icon.gif' align=absmiddle border=0></a> </td>
							<td class='input_box_item' colspan=3 style='padding:10px;'>
								<table width='100%'>
									<tr>
										<td>
											<table border=0>
												<tr>
													";
        if(count($icon_list) >0 ){
            for($i=0;$i<count($icon_list);$i++){
                $Contents .=	"<td><input type=\"checkbox\" name='icon_check[]' class=nonborder id=icon_check value=".$icon_list[$i][idx]." ".($icons_checked[$icon_list[$i][idx]] == "1" ? "checked":"")."></td><td><img src='".$_SESSION["admin_config"][mall_data_root]."/images/icon/".$icon_list[$i][idx].".gif' align='absmiddle' style='vertical-align:middle'></td>";
                if($i%8==0 && $i>0) $Contents .=	"</tr></table><table border=0><tr>";
            }
        }

        $Contents .="
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>";
    }else{
        $Contents .=	"<input type='hidden' name='movie' value='".$movie."'>";
    }
}
$Contents .=	"<tr class='hide_class'>
						<td class='input_box_title' > 19금 상품 </td>
						<td class='input_box_item' colspan='3'>
							<input type='radio' name='is_adult' id='is_adult_0' value='0' ".($is_adult == "0" || $is_adult == ""? "checked":"")."><label for='is_adult_0'>미적용</label>
							<input type='radio' name='is_adult' id='is_adult_1' value='1' ".($is_adult == "1"? "checked":"")."><label for='is_adult_1'>적용</label>
						</td>
					</tr>
				</table>
				<div style='clear:both;height:70px;'></div>
				";
$Contents .= "	
	        <div id='filter_area' >
			<table width='100%' cellpadding=0 cellspacing=0>
			<tr height=30>
				<td style='padding-bottom:10px;'>
					".colorCirCleBox("#efefef","100%",
                    "<table cellpadding=0 width=100%>
                            <tr>
                                <td style='padding:5px;padding-left:10px;'>
                                    <b class=blk >  
                                    <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 필터정보 
                                    </b><a name='filterInfo'></a>
                                    <span class=small>".getTransDiscription($page_code,'Z')." </span>
                                </td>
                                <td align=right style='padding-right:20px;' class=small></td>
                                <td align=right><input type='button' value='관리' onclick=\"javascript:PoPWindow('./product_filter.php',960,700,'product_filter')\"></td>
                             </tr>
                         </table>")."
				</td>
			</tr>
			</table>";

$Contents .= "
			<table cellpadding=0 cellspacing=0  border=0 width='100%' class='input_table_box'>
				<col width=15%>
				<col width=90%>
				<tr>
					<td class='input_box_title'> 의류 </td>
					<td class='input_box_item' style='line-height:150%' colspan=3>
					   ".getFilterItem('CLOTHING',$id)."
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> 슈즈 </td>
					<td class='input_box_item' style='line-height:150%' colspan=3>
					   ".getFilterItem('SHOES',$id)."
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> ACC </td>
					<td class='input_box_item' style='line-height:150%' colspan=3>
					   ".getFilterItem('ACC',$id)."
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> 색상 </td>
					<td class='input_box_item' style='line-height:150%' colspan=3>
					   ".getFilterItem('COLOR',$id)."
					</td>
				</tr>
			</table>
			</div>
			<div style='clear:both;height:70px;'></div>
			";

if($goods_input_type == "globalsellertool" ){
    $Contents .="
			<div id='globalsellingtool_info'>
				<table width='100%' cellpadding=0 cellspacing=0>
				<tr height=30>
					<td style='padding-bottom:10px;'>
						".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:0px;'><b class=blk style='font-size:15px;'> 해외판매 상품 정보  </b><span class=small></span> </td><td align=right style='padding-right:20px;' class=small> </td></tr></table>")."
					</td>
				</tr>
				</table>
				<table cellpadding=0 cellspacing=0 width='100%' class='input_table_box' id='product_type_use'>
				<col width=15%>
				<col width=35%>
				<col width=15%>
				<col width=35%>
				<tr bgcolor=#ffffff >
					<td class='input_box_title'> <b>무게(KG)/부피</b></td>
					<td class='input_box_item'>
						<input type='text' class='textbox ' name='product_weight' id='product_weight' value='".$product_weight."' style='width:40px;'> <b>KG<b>
						/
						<input type='text' class='textbox ' name='product_width' id='product_width' value='".$product_width."' style='width:40px;margin-left:0px;'> <b>W(cm)<b>

						<input type='text' class='textbox ' name='product_depth' id='product_depth' value='".$product_depth."' style='width:40px;margin-left:10px;'> <b>D(cm)<b>

						<input type='text' class='textbox ' name='product_height' id='product_height' value='".$product_height."' style='width:40px;margin-left:10px;'> <b>H(cm)<b>
					</td>
					<td class='input_box_title' > <b>HSCODE NO.</b> </td> 
					<td class='input_box_item' ><input type=text class='textbox' name='hscode' value='' style='width:150px;' validation=true title='HSCODE'> </td>
				</tr>
				</table><div style='clear:both;height:70px;'></div>
				";
}

/*
if($_SESSION["layout_config"]["mall_use_inventory"] == "Y"){
    if($_SESSION["admininfo"][mall_type] != "O"){
$Contents .= "<tr id='product_77_supply'>
            <td class='input_box_title'> 매입처 </td>
            <td class='input_box_item'>".SelectSupplyCompany($supply_company,"supply_company",($supply_company != "" ? "text":"select"), "false")."</td>

            <td class='input_box_title'><b>개별쿠폰 사용</b></td>
            <td class='input_box_item'>
                <input type=radio  name=cupon_use_yn value='Y' id='cupon_use_y'  ".($cupon_use_yn == "Y" || $cupon_use_yn == "" ? "checked":"").">
                <label for='cupon_use_y'>사용함</label>
                <input type=radio  name=cupon_use_yn value='N' id='cupon_use_n'  ".($cupon_use_yn == "N"? "checked":"").">
                <label for='cupon_use_n'>사용안함</label>
            </td>
        </tr>";
    }
}else{
    if($_SESSION["admininfo"][mall_type] != "O"){
    $Contents .= "
        <tr bgcolor='#ffffff'>
            <td class='input_box_title'> 사입처</td>
            <td class='input_box_item' colspan=3><!--input type=text class='textbox' name=company size=28 style='width:100%'-->
            <table cellpadding=0 cellspacing=0 >
                <tr>
                    <td><div id='buying_comapny_select_area'>".printBuyingCompany($buying_company,$cid)."</div></td>
                    <td style='padding-left:5px;'><a href=\"javascript:PoPWindow3('../product/buying_company.php?mmode=pop',960,600,'buying_company')\"'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_pop_manage.gif' align=absmiddle border=0></a></td>
                </tr>
            </table>
            </td>
        </tr>";
    }
}*/
/*
$Contents .="


			<div id='product_77_info'>
				<table width='100%' cellpadding=0 cellspacing=0>
				<tr height=30>
					<td style='padding-bottom:10px;'>
						".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:0px;'><b class=blk style='font-size:15px;'> 추가정보  </b><span class=small></span> </td><td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--><img src='/admin/images/btn_group_open.png' open_src='/admin/images/btn_group_close.png' close_src='/admin/images/btn_group_open.png' alt='Minus' align=absmiddle style='margin-left:5px;' onclick=\"moreDisplay(this, 'table#add_goods_info2','div#add_goods_info2_result');\"></td></tr></table>")."
					</td>
				</tr>
				</table>
				<div id='add_goods_info2_result' style='padding:20px;border:1px solid silver;margin-bottom:10px;'><b style='color:#ff2a32'> [GUIDE]</b> 변경을 위해서는 설정하기 버튼을 클릭하세요</div>
				<table cellpadding=3 cellspacing=0 width='100%' border='0' class='input_table_box' align='center' id='add_goods_info2' style='display:none;'>
					<col width=15%>
					<col width=35%>
					<col width=15%>
					<col width=35%>";

if($goods_input_type != "inventory"){
	if($goods_input_type != "globalsellertool"){
		if($_SESSION["admininfo"][admin_level] == 9){
		$Contents .="
						<tr>
							<td class='input_box_title' > 아이콘노출<br><br>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:PoPWindow('../design/product_icon.php?mmode=pop',960,700,'brand')\"'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_pop_icon.gif' align=absmiddle border=0></a> </td>
							<td class='input_box_item' colspan=3 style='padding:10px;'>
								<table width='100%'>
									<tr>
										<td>
											<table border=0>
												<tr>
													";
													if(count($icon_list) >0 ){
														for($i=0;$i<count($icon_list);$i++){
															$Contents .=	"<td><input type=\"checkbox\" name='icon_check[]' class=nonborder id=icon_check value=".$icon_list[$i][idx]." ".($icons_checked[$icon_list[$i][idx]] == "1" ? "checked":"")."></td><td><img src='".$_SESSION["admin_config"][mall_data_root]."/images/icon/".$icon_list[$i][idx].".gif' align='absmiddle' style='vertical-align:middle'></td>";
															if($i%8==0 && $i>0) $Contents .=	"</tr></table><table border=0><tr>";
														}
													}

													$Contents .="
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>";
		$sns_btn_arr = unserialize($sns_btn);

		$Contents .=	"
						<tr>
							<td class='input_box_title'> SNS공유버튼 노출 </td>
							<td class='input_box_item' colspan=3 style='line-height:150%'>
							<input type=\"checkbox\" name='sns_btn_yn' class=nonborder id=sns_btn_yn value='Y' ".($sns_btn_yn == "Y" || $sns_btn_yn == ""? "checked":"")."> <label for='sns_btn_yn'>사용함</label> (
							<input type=\"checkbox\" name='sns_btn[btn_use1]' class=nonborder id=btn_use1 value=facebook ".($sns_btn_arr[btn_use1] == "facebook"? "checked":"")."> <label for='btn_use1'>페이스북</label>
							<input type=\"checkbox\" name='sns_btn[btn_use2]' class=nonborder id=btn_use2 value=twitter ".($sns_btn_arr[btn_use2] == "twitter"? "checked":"")."> <label for='btn_use2'>트위터</label>

							<input type=\"checkbox\" name='sns_btn[btn_use3]' class=nonborder id=btn_use3 value=me2day ".($sns_btn_arr[btn_use3] == "me2day"? "checked":"")."> <label for='btn_use3'>미투데이</label>
							<input type=\"checkbox\" name='sns_btn[btn_use4]' class=nonborder id=btn_use4 value=yozm ".($sns_btn_arr[btn_use4] == "yozm"? "checked":"")."> <label for='btn_use4'>요즘</label>

							)
							</td>
						</tr>";

		$Contents .=	"
						<tr>
							<td class='input_box_title'> 동영상 URL(NEWS) </td>
							<td class='input_box_item' colspan=3 style='line-height:150%'>
							<input type=text class='textbox' name='movie' style='width:90%' value='".$movie."'></td>
						</tr>";
		}else{
			$Contents .=	"<input type='hidden' name='movie' value='".$movie."'>";
		}
	}
	$Contents .=	"

					<!--tr height=1><td colspan=4 class='dot-x'></td></tr-->";

	if (($_SESSION["admininfo"][mall_type] == "O" || $_SESSION["admininfo"][mall_type] == "E") || $_SESSION["admininfo"][mall_type] == "BW" ){

	//if($admininfo[admin_level] == '9'){

	//}
	}
	$Contents .=	"
				</table><div style='clear:both;height:70px;'></div>
			</div>";
}// 재고관리가 아닐때 까지...
*/

$Contents .= $SocailAddContents;

//필수고시 (국내)
$Contents .="
			<div id='mandatory_info_zone_table'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr >
					<td style='padding-bottom:10px;'>
					".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><b class=blk > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 상품 고시 정보(국내)</b><a name='mandatory'></a></div>")."
					</td>
				</tr>
				<tr>
					<td  nowrap>
					<input type='radio' name='mandatory_use' value='Y' ".($mandatory_use == 'Y' || $mandatory_use == "" ? "checked":"")." onclick=\"showMandatoryArea('Y')\" id='mandatory_use_y'><label for='mandatory_use_y'>사용</label>
					<input type='radio' name='mandatory_use' value='N' ".($mandatory_use == 'N' ? "checked":"")." onclick=\"showMandatoryArea('N')\" id='mandatory_use_n'><label for='mandatory_use_n'>미사용</label>
					</td>
				</tr>
				</table>

				<table cellpadding=0 cellspacing=0 width='100%' border='0' class='MandatoryArea' ".($mandatory_use == 'N' ? "style='display:none;'":"").">
				<col width='650px'>
				<col width='100px'>
				<col width='*'>
				<tr height=40>
					<td  >
					<select name='mandatory_type_1' id='mandatory_select_1' onchange='MandatoryChange(1)' style='height:30px;' ".($mandatory_use == 'Y' || $mandatory_use == "" ? "validation='true'":"validation='false'")." title='상품 고시 정보 분류'>
						<option value='' ".($mi_code == "" ? "selected":"").">상품분류를 선택해주세요</option>";

$sql = "select * from shop_mandatory_info where is_use = '1' and mall_ix != '20bd04dac38084b2bafdd6d78cd596b2' order by mi_code ASC";
$db->query($sql);
$mandatory_array = $db->fetchall();
for($i=0;$i<count($mandatory_array);$i++){
    $Contents .="
						<option value='".$mandatory_array[$i][mi_code]."' ".($mandatory_array[$i][mi_code] == $mi_code ? "selected":"")."> ".$mandatory_array[$i][mandatory_name]."</option>";
}
$Contents .="
					</select>
					<select name='mandatory_type_2' id='mandatory_select_2' onchange='MandatoryChange(2)' ".($mi_code == "0"||$mi_code == ""? "style='display:none;height:30px;'":"style='height:30px;'")." >
						<option value='1' ".($mandatory_type_2 == "1" ? "selected":"")."> 국내상품</option>
						<!--<option value='2' ".($mandatory_type_2 == "2" ? "selected":"")."> 해외상품</option>-->
					</select> 
					<a href =\"JavaScript:PopSWindow('./reg_guide.php',703,652,'comparewindow');\" ><img src ='../images/".$_SESSION["admininfo"]["language"]."/product_guide.gif' style='margin-bottom:3px;' align=absmiddle></a>
					</td>
					<td></td>
				<tr>
				</table>

				<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver id='mandatory_info_zone' class='input_table_box mandatory_info_zone' opt_idx=0 style='margin-bottom:10px;".($mandatory_use == 'N' ? "display:none;":"")."'>
					<col width='20%'>
					<col width='30%'>
					<col width='20%'>
					<col width='30%'>
					<tr height=25 bgcolor='#ffffff' align=center>
						<td bgcolor=\"#efefef\" class=small>항목</td>
						<td bgcolor=\"#efefef\" class=small> 내용</td>
						<td bgcolor=\"#efefef\" class=small> 항목</td>
						<td bgcolor=\"#efefef\" class=small> 내용</td>
					</tr>
					<tr bgcolor='#ffffff' align=center>
						<td colspan=4 id='mandatory_info_td'>";


$sql = "select * from shop_product_mandatory_info where pid = '".$id."' order by pmi_ix asc ";
$db->query($sql);
$mandatory_info = $db->fetchall();

if($db->total && $id!=""){
    for($i=0;$i < count($mandatory_info);$i = $i + 2){

        $Contents .= "	<table width=100% id='mandatory_info' class='mandatory_info".($i==0?"_basic":"")."' mandatory_info_cnt='".$i."' cellspacing=0 cellpadding=0 >
						<col width='20%'>
						<col width='30%'>
						<col width='20%'>
						<col width='30%'>
						<tr align='center'>
							<td height='30'>
								<input type='hidden' id='mandatory_info_pmi_ix_a' class='' name='mandatory_info[".$i."][pmi_ix]' value='".$mandatory_info[$i][pmi_ix]."' />
								<input type='hidden' id='mandatory_info_pmi_code_a' class='' name='mandatory_info[".$i."][pmi_code]' value='".$mandatory_info[$i][pmi_code]."' />
								<input type=text id='mandatory_info_title_a' name='mandatory_info[".$i."][pmi_title]'  style='width:90%;vertical-align:middle;border:0px;' readonly value='".$mandatory_info[$i][pmi_title]."' title='상품 고시 정보'>
							</td>
							<td>
								<input type=text id='mandatory_info_desc_a' class='textbox' name='mandatory_info[".$i."][pmi_desc]' style='width:85%' value='".$mandatory_info[$i][pmi_desc]."' title ='".$mandatory_info[$i][pmi_title]."' validation='".($mandatory_use == "Y" ? "true" : "false")."' title='상품 고시 정보'>
							</td>
							<td >
								<input type='hidden' id='mandatory_info_pmi_ix_b' class='' name='mandatory_info[".($i+1)."][pmi_ix]' value='".$mandatory_info[($i+1)][pmi_ix]."' />
								<input type='hidden' id='mandatory_info_pmi_code_b' class='' name='mandatory_info[".($i+1)."][pmi_code]' value='".$mandatory_info[($i+1)][pmi_code]."' />
								<input type=text id='mandatory_info_title_b'  name='mandatory_info[".($i+1)."][pmi_title]' style='width:90%;vertical-align:middle;border:0px;' readonly value='".$mandatory_info[($i+1)][pmi_title]."' title='상품 고시 정보'>
							</td>
							<td>
								<input type=text id='mandatory_info_desc_b' class='textbox' name='mandatory_info[".($i+1)."][pmi_desc]' style='width:85%' value='".$mandatory_info[($i+1)][pmi_desc]."' title ='".$mandatory_info[($i+1)][pmi_title]."' validation='".($mandatory_info[($i+1)][pmi_title] != "" && $mandatory_use == "Y" ? "true" : "false")."' title='상품 고시 정보'>";
        /*
        $Contents .= "
        <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.mandatory_info').length > 1){document.getElementById('mandatory_info_td').removeChild(this.parentNode.parentNode.parentNode.parentNode);}else{clearInputBox('mandatory_info');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>";
        */

        $Contents .= "
							</td>
						</tr>
						<tr>
							<td height='13' colspan='2'>
								<div class='mandatory_info_comment_".($i)." small' style='padding:0px 20px;text-align:right;'></div>
							</td>
							<td colspan='2'>
								<div class='mandatory_info_comment_".($i+1)." small' style='padding:0px 20px;text-align:right;' ></div>
							</td>
						</tr>
						</table>";
    }
}else{
    $Contents .= "
						<table width=100% id='mandatory_info' class='mandatory_info_basic' mandatory_info_cnt='0' cellspacing=0 cellpadding=0 >
						<col width='20%'>
						<col width='30%'>
						<col width='20%'>
						<col width='30%'>
						<tr align='center'>
							<td height='30'>
								<input type='hidden' id='mandatory_info_pmi_ix_a' class='' name='mandatory_info[0][pmi_ix]' value='' />
								<input type='hidden' id='mandatory_info_pmi_code_a' class='' name='mandatory_info[0][pmi_code]' value='' />
								<input type=text id='mandatory_info_title_a'   name='mandatory_info[0][pmi_title]' style='width:90%;vertical-align:middle;border:0px;' value='' title='' readonly title='상품 고시 정보'>
							</td>
							<td>
								<input type=text id='mandatory_info_desc_a' class='textbox' name='mandatory_info[0][pmi_desc]' style='width:85%' value='' validation='".($mandatory_use == 'N' ? "false" : "true")."' title='상품 고시 정보'>
							</td>
							<td>
								<input type='hidden' id='mandatory_info_pmi_ix_b' class='' name='mandatory_info[1][pmi_ix]' value='' />
								<input type='hidden' id='mandatory_info_pmi_code_b' class='' name='mandatory_info[1][pmi_code]' value='' />
								<input type=text id='mandatory_info_title_b'  name='mandatory_info[1][pmi_title]' style='width:90%;vertical-align:middle;border:0px;' value='' title='' readonly title='상품 고시 정보'>
							</td>
							<td>
								<input type=text id='mandatory_info_desc_b' class='textbox' name='mandatory_info[1][pmi_desc]' style='width:85%' value='' validation='".($mandatory_use == 'N' ? "false" : "true")."' title='상품 고시 정보'>";
    /*
    $Contents .= "
    <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.mandatory_info').length > 1){document.getElementById('mandatory_info_td').removeChild(this.parentNode.parentNode.parentNode.parentNode);}else{clearInputBox('mandatory_info');}\" title='더블클릭시 해당 라인이 삭제 됩니다.'>";
    */
    $Contents .= "
							</td>
						</tr>
						<tr>
							<td height='13' colspan='2'>
								<div class='mandatory_info_comment_0 small' style='padding:0px 20px;text-align:right;'></div>
							</td>
							<td colspan='2'>
								<div class='mandatory_info_comment_1 small' style='padding:0px 20px;text-align:right;'></div>
							</td>
						</tr>
						</table>";
}
$Contents .= "
					</td>
				</tr>
			</table><div style='clear:both;height:70px;'></div>
		</div>";

//필수고시 (글로벌)
$Contents .="
			<div id='mandatory_info_zone_table_global'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr >
					<td style='padding-bottom:10px;'>
					".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><b class=blk > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 상품 고시 정보(글로벌)</b><a name='mandatory'></a></div>")."
					</td>
				</tr>
				<tr>
					<td  nowrap>
					<input type='radio' name='mandatory_use_global' value='Y' ".($mandatory_use_global == 'Y' ? "checked":"")." onclick=\"showMandatoryAreaGlobal('Y')\" id='mandatory_use_global_y'><label for='mandatory_use_global_y'>사용</label>
					<input type='radio' name='mandatory_use_global' value='N' ".($mandatory_use_global == 'N' || $mandatory_use_global == "" ? "checked":"")." onclick=\"showMandatoryAreaGlobal('N')\" id='mandatory_use_global_n'><label for='mandatory_use_global_n'>미사용</label>
					</td>
				</tr>
				</table>

				<table cellpadding=0 cellspacing=0 width='100%' border='0' class='MandatoryAreaGlobal' ".($mandatory_use_global == 'N' || $mandatory_use_global == "" ? "style='display:none;'":"").">
				<col width='650px'>
				<col width='100px'>
				<col width='*'>
				<tr height=40>
					<td  >
					<select name='mandatory_type_1_global' id='mandatory_select_1_global' onchange='MandatoryChangeGlobal(1)' style='height:30px;' ".($mandatory_use_global == 'Y'  ? "validation='true'":"validation='false'")." title='상품 고시 정보 분류(글로벌)'>
						<option value='' ".($mi_code_global == "" ? "selected":"").">상품분류를 선택해주세요</option>";

$sql = "select * from shop_mandatory_info where is_use = '1' and mall_ix = '20bd04dac38084b2bafdd6d78cd596b2'  order by mi_code ASC";
$db->query($sql);
$mandatory_array = $db->fetchall();

for($i=0;$i<count($mandatory_array);$i++){
    $Contents .="
						<option value='".$mandatory_array[$i][mi_code]."' ".($mandatory_array[$i][mi_code] == $mi_code_global ? "selected":"")."> ".$mandatory_array[$i][mandatory_name]."</option>";
}
$Contents .="
					</select>
					<select name='mandatory_type_2_global' id='mandatory_select_2_global' onchange='MandatoryChangeGlobal(2)' ".($mi_code_global == "0"||$mi_code_global == ""? "style='display:none;height:30px;'":"style='height:30px;'")." >
						<option value='1' ".($mandatory_type_2_global == "1" ? "selected":"")."> 국내상품</option>
						<!--<option value='2' ".($mandatory_type_2_global == "2" ? "selected":"")."> 해외상품</option>-->
					</select> 
					
					</td>
					<td></td>
				<tr>
				</table>

				<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver id='mandatory_info_zone_global' class='input_table_box mandatory_info_zone_global' opt_idx=0 style='margin-bottom:10px;".($mandatory_use_global == 'N' || $mandatory_use_global == "" ? "display:none;":"")."'>
					<col width='20%'>
					<col width='30%'>
					<col width='20%'>
					<col width='30%'>
					<tr height=25 bgcolor='#ffffff' align=center>
						<td bgcolor=\"#efefef\" class=small>항목</td>
						<td bgcolor=\"#efefef\" class=small> 내용</td>
						<td bgcolor=\"#efefef\" class=small> 항목</td>
						<td bgcolor=\"#efefef\" class=small> 내용</td>
					</tr>
					<tr bgcolor='#ffffff' align=center>
						<td colspan=4 id='mandatory_info_td_global'>";


$sql = "select * from shop_product_mandatory_info_global where pid = '".$id."' order by pmi_ix asc ";
$db->query($sql);
$mandatory_info = $db->fetchall();

if($db->total && $id!=""){
    for($i=0;$i < count($mandatory_info);$i = $i + 2){

        $Contents .= "	<table width=100% id='mandatory_info_global' class='mandatory_info_global".($i==0?"_basic":"")."' mandatory_info_cnt_global='".$i."' cellspacing=0 cellpadding=0 >
						<col width='20%'>
						<col width='30%'>
						<col width='20%'>
						<col width='30%'>
						<tr align='center'>
							<td height='30'>
								<input type='hidden' id='mandatory_info_pmi_ix_a_global' class='' name='mandatory_info_global[".$i."][pmi_ix]' value='".$mandatory_info[$i][pmi_ix]."' />
								<input type='hidden' id='mandatory_info_pmi_code_a_global' class='' name='mandatory_info_global[".$i."][pmi_code]' value='".$mandatory_info[$i][pmi_code]."' />
								<input type=text id='mandatory_info_title_a_global' name='mandatory_info_global[".$i."][pmi_title]'  style='width:90%;vertical-align:middle;border:0px;' readonly value='".$mandatory_info[$i][pmi_title]."' title='상품 고시 정보(글로벌)'>
							</td>
							<td>
								<input type=text id='mandatory_info_desc_a_global' class='textbox' name='mandatory_info_global[".$i."][pmi_desc]' style='width:85%' value='".$mandatory_info[$i][pmi_desc]."' title ='".$mandatory_info[$i][pmi_title]."' validation='".($mandatory_use_global == "Y" ? "true" : "false")."' title='상품 고시 정보(글로벌)'>
							</td>
							<td >
								<input type='hidden' id='mandatory_info_pmi_ix_b_global' class='' name='mandatory_info_global[".($i+1)."][pmi_ix]' value='".$mandatory_info[($i+1)][pmi_ix]."' />
								<input type='hidden' id='mandatory_info_pmi_code_b_global' class='' name='mandatory_info_global[".($i+1)."][pmi_code]' value='".$mandatory_info[($i+1)][pmi_code]."' />
								<input type=text id='mandatory_info_title_b_global'  name='mandatory_info_global[".($i+1)."][pmi_title]' style='width:90%;vertical-align:middle;border:0px;' readonly value='".$mandatory_info[($i+1)][pmi_title]."' title='상품 고시 정보(글로벌)'>
							</td>
							<td>
								<input type=text id='mandatory_info_desc_b_global' class='textbox' name='mandatory_info_global[".($i+1)."][pmi_desc]' style='width:85%' value='".$mandatory_info[($i+1)][pmi_desc]."' title ='".$mandatory_info[($i+1)][pmi_title]."' validation='".($mandatory_info[($i+1)][pmi_title] != "" && $mandatory_use_global == "Y" ? "true" : "false")."' title='상품 고시 정보(글로벌)'>";
        /*
        $Contents .= "
        <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.mandatory_info').length > 1){document.getElementById('mandatory_info_td').removeChild(this.parentNode.parentNode.parentNode.parentNode);}else{clearInputBox('mandatory_info');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>";
        */

        $Contents .= "
							</td>
						</tr>
						<tr>
							<td height='13' colspan='2'>
								<div class='mandatory_info_comment_global_".($i)." small' style='padding:0px 20px;text-align:right;'></div>
							</td>
							<td colspan='2'>
								<div class='mandatory_info_comment_global_".($i+1)." small' style='padding:0px 20px;text-align:right;' ></div>
							</td>
						</tr>
						</table>";
    }
}else{
    $Contents .= "
						<table width=100% id='mandatory_info_global' class='mandatory_info_basic_global' mandatory_info_cnt_global='0' cellspacing=0 cellpadding=0 >
						<col width='20%'>
						<col width='30%'>
						<col width='20%'>
						<col width='30%'>
						<tr align='center'>
							<td height='30'>
								<input type='hidden' id='mandatory_info_pmi_ix_a_global' class='' name='mandatory_info_global[0][pmi_ix]' value='' />
								<input type='hidden' id='mandatory_info_pmi_code_a_global' class='' name='mandatory_info_global[0][pmi_code]' value='' />
								<input type=text id='mandatory_info_title_a_global'   name='mandatory_info_global[0][pmi_title]' style='width:90%;vertical-align:middle;border:0px;' value='' title='' readonly title='상품 고시 정보(글로벌)'>
							</td>
							<td>
								<input type=text id='mandatory_info_desc_a_global' class='textbox' name='mandatory_info_global[0][pmi_desc]' style='width:85%' value='' validation='".($mandatory_use_global == 'Y' ? "true" : "false")."' title='상품 고시 정보(글로벌)'>
							</td>
							<td>
								<input type='hidden' id='mandatory_info_pmi_ix_b_global' class='' name='mandatory_info_global[1][pmi_ix]' value='' />
								<input type='hidden' id='mandatory_info_pmi_code_b_global' class='' name='mandatory_info_global[1][pmi_code]' value='' />
								<input type=text id='mandatory_info_title_b_global'  name='mandatory_info_global[1][pmi_title]' style='width:90%;vertical-align:middle;border:0px;' value='' title='' readonly title='상품 고시 정보(글로벌)'>
							</td>
							<td>
								<input type=text id='mandatory_info_desc_b_global' class='textbox' name='mandatory_info_global[1][pmi_desc]' style='width:85%' value='' validation='".($mandatory_use_global == 'Y' ? "true" : "false")."' title='상품 고시 정보(글로벌)'>";
    /*
    $Contents .= "
    <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.mandatory_info').length > 1){document.getElementById('mandatory_info_td').removeChild(this.parentNode.parentNode.parentNode.parentNode);}else{clearInputBox('mandatory_info');}\" title='더블클릭시 해당 라인이 삭제 됩니다.'>";
    */
    $Contents .= "
							</td>
						</tr>
						<tr>
							<td height='13' colspan='2'>
								<div class='mandatory_info_comment_global_0 small' style='padding:0px 20px;text-align:right;'></div>
							</td>
							<td colspan='2'>
								<div class='mandatory_info_comment_global_1 small' style='padding:0px 20px;text-align:right;'></div>
							</td>
						</tr>
						</table>";
}
$Contents .= "
					</td>
				</tr>
			</table><div style='clear:both;height:70px;'></div>
		</div>";
if($id){

    $Contents .= "
			<table cellpadding=0 cellspacing=0  border=0 width='100%'  >
				<col width=49%>
				<col width=1%>
				<col width=49%>
				<tr height=30>
					<td  >
						".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:0px;'><b class=blk style='font-size:15px;'> 이벤트/기획전  정보</b><span class=small>  </span></td><td align=right style='padding-right:20px;' class=small></td></tr></table>")."
					</td>
					<td rowspan=2></td>
					<td  >
						".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:0px;'><b class=blk style='font-size:15px;'> 기획/특별 할인정보</b><span class=small>  </span></td><td align=right style='padding-right:20px;' class=small></td></tr></table>")."
					</td>
				</tr>
				<tr>
					<td class='input_box_item' style='line-height:150%;vertical-align:top;padding:10px;'>  ";
    $sql = "select e.* from shop_event e, shop_event_product_relation er where e.event_ix = er.event_ix and er.pid = '".$id."' group by e.event_ix";

    $db->query($sql);
    $event_infos = $db->fetchall("object");
    for($i=0;$i < count($event_infos);$i++){
        if($_SESSION["admininfo"][admin_level] == 9){
            $Contents .= "<a href='../display/event.write.php?event_ix=".$event_infos[$i][event_ix]."' target=_blank>".$event_infos[$i][event_title]."</a><br>";
        }else{
            $Contents .= "".$event_infos[$i][event_title]."<br>";
        }
    }

    $Contents .= "
					</td>
					<td class='input_box_item' style='line-height:150%;vertical-align:top;padding:10px;'> ";
    /*
    $sql = "select * from shop_discount d, shop_discount_product_group dpg, shop_discount_product_relation dpr
                where d.dc_ix = dpr.dc_ix and dpg.group_code = dpr.group_code and dpr.pid = '".$id."'
                group by d.dc_ix  ";

    $db->query($sql);
    $discount_infos = $db->fetchall("object");
    for($i=0;$i < count($discount_infos);$i++){
        $Contents .= "<a href='../promotion/discount.php?dc_ix=".$discount_infos[$i][dc_ix]."' target=_blank>".($discount_infos[$i][discount_type] == "SP" ? "[특별할인]":"[기획할인]")."".$discount_infos[$i][discount_sale_title]." ".$discount_infos[$i][group_name]." ".$discount_infos[$i][sale_rate]." ".($discount_infos[$i][discount_sale_type] == "1" ? "%":"원")." </a><br>";
    }
    */
    $sql = "SELECT p.id, 1 as pcount, r.cid, c.depth
					FROM ".TBL_SHOP_PRODUCT." p
					right join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid and r.basic = '1'
					right join ".TBL_SHOP_CATEGORY_INFO." c	 on r.cid = c.cid 
					,
					(
						select dpr.pid from shop_discount_product_relation dpr where dpr.pid = '".$id."'
						union
						select r.pid from shop_discount_display_relation ddr , ".TBL_SHOP_PRODUCT_RELATION." r where r.cid=ddr.r_ix and r.pid = '".$id."' and r.basic = '1' and ddr.relation_type='C'
						union
						select '".$id."' as pid from shop_discount_display_relation ddr where ddr.relation_type='B' and ddr.r_ix = '".$brand."'
						union
						select '".$id."' as pid from shop_discount_display_relation ddr where ddr.relation_type='S' and ddr.r_ix = '".$admin."'
					) t
					where  t.pid = p.id and p.id = '".$id."' 
					group by p.id ";
    $slave_db->query($sql);
    $products = $slave_db->fetchall();
    if(count($products) > 0){
        for($i=0 ; $i < count($products) ;$i++){
            $_array_pid[] = $products[$i][id];
            $goods_infos[$products[$i][id]][pid] = $products[$i][id];
            $goods_infos[$products[$i][id]][amount] = $products[$i][pcount];
            $goods_infos[$products[$i][id]][cid] = $products[$i][cid];
            $goods_infos[$products[$i][id]][depth] = $products[$i][depth];
        }

        $discount_infos = DiscountRult($goods_infos, $cid, $depth);
        //print_r($discount_infos);
        //exit;
        $max_discount_value = 0;
        if(is_array($discount_infos)){
            foreach($discount_infos as $_key => $_discount_info){
                foreach($_discount_info as $key => $discount_info){
                    if($_SESSION["admininfo"][admin_level] == 9){
                        $Contents .= "<a href='../promotion/".($discount_info[discount_type] == "SP" ? "special_discount.php":"discount.php")."?dc_ix=".$discount_info[dc_ix]."'  target=_blank>".($discount_info[discount_type] == "SP" ? "[특별할인]":"[기획할인]")."".$discount_info[discount_desc]." ".$discount_info[group_name]." ".$discount_info[discount_value]." ".($discount_info[discount_value_type] == "1" ? "%":"원")." (본사 : ".$discount_info[headoffice_discount_value]." ".($discount_info[discount_value_type] == "1" ? "%":"원").", 셀러 : ".$discount_info[seller_discount_value]." ".($discount_info[discount_value_type] == "1" ? "%":"원").")</a><br>";
                    }else{
                        $Contents .= "".($discount_info[discount_type] == "SP" ? "[특별할인]":"[기획할인]")."".$discount_info[discount_desc]." ".$discount_info[group_name]." ".$discount_info[discount_value]." ".($discount_info[discount_value_type] == "1" ? "%":"원")." (본사 : ".$discount_info[headoffice_discount_value]." ".($discount_info[discount_value_type] == "1" ? "%":"원").", 셀러 : ".$discount_info[seller_discount_value]." ".($discount_info[discount_value_type] == "1" ? "%":"원").")<br>";
                    }
                    if($discount_info[discount_value] > $max_discount_value){
                        $max_discount_value = $discount_info[discount_value];

                        if($discount_info[discount_value_type] == "1"){ // %
                            $max_dcprice = roundBetter($sellprice*(100 - $discount_info[discount_value])/100, $discount_info[round_position], $discount_info[round_type]);//$_dcprice*(100 - $discount_info[discount_value])/100;
                        }else if($discount_info[discount_value_type] == "2"){// 원
                            $max_dcprice = $sellprice - $discount_info[discount_value];
                        }
                    }
                }
            }
        }
    }
    $Contents .= "
					</td>
				</tr>
			</table><div style='clear:both;height:70px;'></div>";
}

$Contents .= "
		<div id='product_77_info'>
			<table width='100%' cellpadding=0 cellspacing=0 id='product_77_price_title'>
			<tr >
				<td style='padding-bottom:10px;'>
					".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;border-bottom:0px solid #ff2a32'><b class=blk > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 가격정보</b><a name='priceInfo'></a>  </div>")."
				</td>
			</tr>
			</table>";
//'동영상 메뉴얼 준비중입니다'
if ($_SESSION["admininfo"][admin_level] != 9){
    if($act == "update"){
        $dispString = "style='display:none;'";
        $readonlyString = " readonly";
        $colorString = ";background-color:#efefef;color:gray";
        $message = "onclick=\"alert(language_data['goods_input.php']['C'][language]);\"";
        //'가격 정보를 수정하시고자 할대는 MD와 상의해 주세요'
    }else{
        //$dispString = "style='display:none;'";
        //$readonlyString = " readonly";
        //$colorString = ";background-color:#efefef;color:gray";
        //$message = "onclick=\"alert('입점업체는 공급가격만 입력하실수 있습니다');\"";
    }
}

$st_date = date('Y-m-d 00:00:00');
$ed_date = date('Y-m-d 23:59:59');

$sql = "select count(id) as total from shop_priceinfo where pid = '".$id."' and regdate between '".$st_date."' and '".$ed_date."'";
$db->query($sql);
$db->fetch();
$priceinfo_total = $db->dt[total];

$Contents = $Contents."
			<table cellpadding=0 cellspacing=1 bgcolor=#ffffff width='100%' id='product_77_price' class=''>
				<tr>
					<td align='left' colspan=4 style='padding-bottom:0px;'>
					<div class='tab'>
							<table class='s_org_tab' width=100%>
							<tr>
								<td class='tab'>
									<table id='p_tab_01'  class='on'>
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"showPriceTabContents('price_info','p_tab_01');\">가격정보입력</td>
										<th class='box_03'></th>
									</tr>
									</table>
									<table id='p_tab_02'  ".($_SESSION["admininfo"][admin_level] == 9 ? "style='display:none;":"style='display:none;' ").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"showPriceTabContents('detail_price_info','p_tab_02');\">수수료계산기</td>
										<th class='box_03'></th>
									</tr>
									</table>
									<table id='p_tab_03'  >
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"showPriceTabContents('price_history_info','p_tab_03');\">가격정보 변경내역".($priceinfo_total > 0 ? " <IMG src='/admin/v3/images/btns/new_icon.gif' border=0>" :'')."</td>
										<th class='box_03'></th>
									</tr>
									</table>
								</td>
								<td class='btn' style='text-align:right;' align=right>
									".($max_discount_value > 0 ? "<span style='color:red;'>기획/특별 할인이 적용되었습니다. 가격변경시 중복 할인이 적용될 수 있습니다.</span>":"")."
								</td>
							</tr>
							</table>
					</div>
					<div class='mallstory t_no'>
						<!-- my_movie start -->

						<!--div class='my_box'-->
							<div class='doong' id='price_info_box' style='display:block;vertical-align:top;' >
								<table cellpadding=3 cellspacing=0 width=100% border=0>
									<tr id=\"buyingServiceClearanceType\" style='".($product_type == "1" ? "":"display:none;")."'>
										
										<td>";
if($admininfo[mall_type] != "H" && $admininfo[mall_type] != "S"){
    $Contents .= "
											<table width=100% cellpadding=0 cellspacing=0>
												<col width=90%>
												<col width=10%>
												<tr>
													<td align=left>
														<table cellpadding=1 cellspacing=1 bgcolor=#c0c0c0 width=100%>
															<col width=10%>
															<col width=20%>
															<col width=10%>
															<col width=25%>
															<col width=10%>
															<col width=25%>
															<tr bgcolor='#ffffff' height=30 align=center>
																<td bgcolor='#efefef' nowrap> 개별가격관리 </td>
																<td  nowrap>
																<input type='radio' name='price_policy' value='N' ".($price_policy == 'N' || $price_policy == "" ? "checked":"")." onclick=\"deliveryTypeView('N')\" id='price_policy_n'><label for='price_policy_n'>사용안함</label>
																<input type='radio' name='price_policy' value='Y' ".($price_policy == 'Y' ? "checked":"")." onclick=\"deliveryTypeView('Y')\" id='price_policy_y'><label for='price_policy_y'>사용</label>
																</td>
																<td bgcolor='#efefef'>통관타입</td>
																<td style='padding:1px 3px 1px 1px' nowrap>
																<input type='radio' name='clearance_type' id='clearance_type_1' onclick='caculateBuyingServicePrice(this);' value='1' ".($clearance_type == "1" ? "checked":"")."><label for='clearance_type_1'>목록통관</label>
																<input type='radio' name='clearance_type' id='clearance_type_0' onclick='caculateBuyingServicePrice(this);' value='0' ".($clearance_type == "0" ? "checked":"")."> <label for='clearance_type_0'>일반통관</label>
																<input type='radio' name='clearance_type' id='clearance_type_9' onclick='caculateBuyingServicePrice(this);' value='9' ".($clearance_type == "9" ? "checked":"")."> <label for='clearance_type_9'>국내배송</label>
																</td>
																<td bgcolor='#efefef'>환율타입</td>
																<td >".getBuyingServiceCurrencyInfo($currency_ix,"select","")." </td>
																<!--td bgcolor='#efefef'>가격반올림</td>
																<td ><select name='round_precision' id='round_precision' disabled>
																	<option value='2'>100자리</option>
																	<option value='3'>1000자리</option>
																	<option value='4'>10000자리</option>
																</select>
																	<input type='radio' name='round_type' id='round_type_1' value='round' disabled checked><label for='round_type_1'>반올림 </label>
																	<input type='radio' name='round_type' id='round_type_2' value='floor' disabled><label for='round_type_2'>버림</label></td-->
															</tr>
															<tr bgcolor='#ffffff' height=30 align=center>
																<td bgcolor='#efefef'>상품 URL</td>
																<td colspan=5 style='text-align:left;padding:0px 0px 0px 6px'>
																<table width=100%>
																	<col width='*'>
																	<col width='120px'>
																	<tr>
																		<td><input  type=hidden name='bs_site' value='".$bs_site."'><input  type=text class='textbox'  name='bs_goods_url' value='".$bs_goods_url."' style='width:98%;'></td>
																		<td><a href='".$bs_goods_url."' class=small target=_blank><b class=blu><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_buy_agency.gif' align=absmiddle style='margin:5px 5px;'></b></a>
																		</td>
																	</tr>
																</table>
																</td>
															</tr>
														</table>
													</td>
													<td align=center>".($product_type == "1" ? " <a href=\"javascript:PoPWindow('../product/buyingService_pricehistory.php?mmode=pop&id=$id',960,600,'brand')\"'><img src='../image/btn_bs_priceinfo.gif' align=absmiddle></a>":"<a href=\"javascript:alert(language_data['goods_input.php']['E'][language]);\"'><img src='../image/btn_bs_priceinfo.gif' align=absmiddle></a>")."</td>
												</tr>

											</table>";
}

$Contents .= "
										</td>
										
									</tr>
									<tr>
										<td width=100% >
											<table cellpadding=1 cellspacing=1 bgcolor=silver width=100% id=\"buyingServiceInfTable\" style='width:100%;".($product_type == "1" ? "":"display:none;")."'>
											".($_SESSION["admininfo"][admin_level] == 9 ? "<col width=17%>":"")."
											<col width=17%>
											<col width=17%>
											<col width=17%>
											<col width=15%>
											<col width=*>
											<tr bgcolor='#efefef' height=35 align=center>
												".($_SESSION["admininfo"][admin_level] == 9 ? "<td>Orgin 원가($)</td>":"")."
												<td >환율 </td>
												<td class='small' nowrap>예상무게(파운드)/항공운송료($)</td>
												<td >관세 / 부가세</td>
												<td >통관수수료 </td>
												<!--td >딜러  ".($_SESSION["admininfo"][admin_level] == 9 ? "<span class=small onclick=\"copyPrice(document.product_input, document.product_input.prd_dealer_price.value, 4);\" style='cursor:pointer;color:red'>복사→</span>":"")."</td-->
												<td class='small'>구매대행 수수료율(%)/수수료  </td>
											</tr>
											<tr bgcolor='#fbfbfb' height=35 align=center>
												".($_SESSION["admininfo"][admin_level] == 9 ? "<td ><input  type=hidden name='b_orgin_price' value='".$orgin_price."'><input  type=text class='textbox' size=10  name='orgin_price' value='".$orgin_price."' style='text-align:right;background-color:#efefef' ></td>":"")."
												<td>
													<input type=hidden name=b_exchange_rate value='$exchange_rate'>
													".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." <input type=text class='textbox numeric' name=exchange_rate size=10 value='$exchange_rate' maxlength=16 onkeyup='this.value=FormatNumber3(this.value);' style='TEXT-ALIGN:right;padding-right:3px;background-color:#efefef;".$colorString." ' readonly> ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
												</td>
												<td nowrap>
													<input  type=hidden name='b_air_wt' value='$air_wt' >
													<input  type=text class='textbox numeric' size=2  name='air_wt' value='$air_wt' style='text-align:right;' maxlength=16 onkeyup='caculateBuyingServicePrice(this);this.value=FormatNumber3(this.value);'> lbs /
													<input  type=hidden name='b_air_shipping' value='$air_shipping' >
													<input  type=text class='textbox numeric' size=2  name='air_shipping' value='$air_shipping'  style='text-align:right;background-color:#efefef' readonly> $
												</td>
												<td>
													<input type=hidden name=b_duty value='$duty'>
													".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." <input type=text class='textbox numeric' name=duty value='$duty' size=10  style='TEXT-ALIGN:right;padding-right:3px;;background-color:#efefef;".$colorString."'  maxlength=16 onkeyup='this.value=FormatNumber3(this.value);'   readonly> ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
												</td>
												<td >
													<input type=text class='textbox numeric' name=clearance_fee size=10 style='text-align:right' value='$clearance_fee'  onkeyup='this.value=FormatNumber3(this.value);calcurate_maginrate(document.product_input)' style='TEXT-ALIGN:right;padding-right:3px;;background-color:#efefef;".$colorString."' readonly >
													".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." <input type=hidden name=b_clearance_fee value='$clearance_fee' > ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
												</td>
												<td nowrap>
													<input type=hidden name=b_bs_fee_rate value='$bs_fee_rate'>
													<input type=text class='textbox numeric' name=bs_fee_rate size=4 value='$bs_fee_rate' maxlength=16 onkeyup='caculateBuyingServicePrice(this);this.value=FormatNumber3(this.value);' style='TEXT-ALIGN:right;padding-right:3px;;".$colorString."' $message  $readonlyString>
													<input type=hidden name=b_bs_fee value='$bs_fee'>
													".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." <input type=text class='textbox' name=bs_fee size=8 value='$bs_fee' onkeydown='onlyEditableNumber(this)' maxlength=16 onkeyup='caculateBuyingServicePrice(this);this.value=FormatNumber3(this.value);' style='TEXT-ALIGN:right;padding-right:3px;".$colorString."' $message  $readonlyString> ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
												</td>
											</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td>";
if($_SESSION["admininfo"][mall_type] == "BW" || true){

    $Contents .= "					<table cellpadding=1 cellspacing=1 bgcolor=silver width=100% class='input_table_box'>";
    if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "
									<col width=13%>";
    }
    $Contents .= "
									<col width=15%>
									<col width=15%>
									";
    if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        if($max_discount_value > 0){
            $Contents .= "				<col width=7.5%>
								<col width=7.5%>
								<col width=7.5%>
								<col width=7.5%>";
        }else{
            $Contents .= "				<col width=10%>
								<col width=10%>
								<col width=10%>";
        }
    }
    if($_SESSION["admininfo"][mall_type] != "O"){
        //	$Contents .= "				<col width=20%>";
    }
    /*
            $Contents .= "				<tr bgcolor='#efefef' height=35 align=center >
                                            <td>공급가</td>";
        //if($DISPLAY_WHOSALE_INFO){
        if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
            $Contents .= "			<td colspan=2>도매가</td>";
        }
        if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
            if($_SESSION["admininfo"][admin_level] == 9){
                $Contents .= "			<td colspan=".($max_discount_value > 0 ? "4":"3").">소매가</td>";
            }else{
                $Contents .= "			<td colspan=".($max_discount_value > 0 ? "3":"3").">소매가</td>";
            }
        }

        $Contents .= "
                                        </tr>";
    */
    $Contents .= "
									<tr bgcolor='#efefef' height=35 align=center>";
    $Contents .= "
										<td  >".($_SESSION["admininfo"][admin_level] == 9 ? "<b>구매단가(원가) <img src='".$required3_path."'></b>":"<b>공급가격</b>")." </td>";
//if($DISPLAY_WHOSALE_INFO){
    if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "					<td ><b>도매 판매가 ".($_SESSION["admininfo"][mall_type] == "BW" ? "<img src='".$required3_path."'>":"")."</b></td>";
        $Contents .= "					<td ><b>도매 할인가 ".($_SESSION["admininfo"][mall_type] == "BW" ? "<img src='".$required3_path."'>":"")."</b></td>";
    }
    if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "					<td ><b>판매가 ".($_SESSION["admininfo"][mall_type] != "BW" ? "<img src='".$required3_path."'>":"")."</b> ";

        $Contents .= "					".($_SESSION["admininfo"][admin_level] == 9 ? "<span class=small onclick=\"copyPrice(document.product_input, document.product_input.listprice.value, 1);calcurate_maginrate(document.product_input)\" style='cursor:pointer;color:red'> 복사→</span>":"")."</td>
												<td >
												<b>실판매가(할인가) ".($_SESSION["admininfo"][mall_type] != "BW" ? "<img src='".$required3_path."'>":"")."</b> ";
        $Contents .= "					</td>";
        if(false){
            $Contents .= "					<td ><b>프리미엄가 ".($_SESSION["admininfo"][mall_type] != "BW" ? "<img src='".$required3_path."'>":"")."</b> ";
        }
        //코웰용
        $Contents .= "					".(false ? "<span class=small onclick=\"copyPrice(document.product_input, document.product_input.sellprice.value, 5);calcurate_maginrate(document.product_input)\" style='cursor:pointer;color:red'> 복사→</span>":"")."</td>";
    }

    //if($max_discount_value > 0){
//    if($_SESSION["admininfo"][admin_level] == 9){
//        $Contents .= "					<td ><b>특별할인가</b></td>";
//    }

    $Contents .= "				</tr>
									<tr bgcolor='#fbfbfb' height=35 align=center>";

    $Contents .= "					<td class='point_color'><input type=hidden name=bcoprice value='$coprice' >
												".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]."
												<input type=text class='textbox  numeric' name=coprice size=13 style='text-align:right;padding-right:3px;' value='".($coprice?$coprice:'0')."'  onkeyup='calcurate_maginrate(document.product_input)' style='TEXT-ALIGN:right;padding-right:3px;".$colorString."'  $message  $readonlyString ".(($_SESSION["admininfo"][mall_type] == "O" || $_SESSION["admininfo"][mall_type] == "E") ? "":"validation=true")." title='구매단가(공급가)'>
													".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
												</td>";
    //if($DISPLAY_WHOSALE_INFO){
    if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "					<td class='point_color'><input type=hidden name=bwholesale_price value='$wholesale_price' >
												".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]."
												<input type=text class='textbox numeric' name=wholesale_price size=13 style='text-align:right;padding-right:3px;' value='$wholesale_price'  onkeyup='calcurate_maginrate(document.product_input)' style='TEXT-ALIGN:right;padding-right:3px;".$colorString."'  $message  $readonlyString ".($_SESSION["admininfo"][mall_type] == "BW" ? "validation=true":"")." title='도매가'>
													".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
												</td>";
        $Contents .= "					<td class='point_color'><input type=hidden name=bwholesale_sellprice value='$wholesale_sellprice' >
												".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]."
												<input type=text class='textbox numeric' name=wholesale_sellprice size=13 style='text-align:right;padding-right:3px;' value='$wholesale_sellprice'  onkeyup='calcurate_maginrate(document.product_input)' style='TEXT-ALIGN:right;padding-right:3px;".$colorString."'  $message  $readonlyString ".($_SESSION["admininfo"][mall_type] == "BW" ? "validation=true":"")." title='도매가 판매가(할인가)'>
													".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
												</td>";
    }
    /*	//셀러가격수정 방지용 다이소 요청으로 잠시 풀어줌 2014-07-04 이학봉
    $Contents .= "					<td class='point_color'><input type=hidden name=blistprice value='$listprice'> ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." <input type=text class='textbox numeric' name=listprice value='$listprice' size=13  style='TEXT-ALIGN:right;padding-right:3px;".$colorString."'  maxlength=16  $message  $readonlyString ".($_SESSION["admininfo"][mall_type] != "BW" ? "validation=true":"validation=false")." title='정가'> ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
                                    </td>
                                    <td class='point_color'>
                                        <input type=hidden name=bsellprice value='$sellprice'>
                                        ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]."
                                        <input type=text class='textbox numeric' name=sellprice size=13 value='$sellprice'  maxlength=16 onkeyup='calcurate_maginrate(document.product_input)' style='TEXT-ALIGN:right;padding-right:3px;".$colorString." ' $message  $readonlyString ".($_SESSION["admininfo"][mall_type] != "BW" ? "validation=true":"validation=false")." title='판매가(할인가)'>
                                        ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
                                    </td>";
    */
    if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "					<td class='point_color'><input type=hidden name=blistprice value='$listprice'> ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." <input type=text class='textbox numeric' name=listprice value='$listprice' size=13  style='TEXT-ALIGN:right;padding-right:3px;'  maxlength=16  ".($_SESSION["admininfo"][mall_type] != "BW" ? "validation=true":"validation=false")." title='정가'> ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
										</td>";
        $Contents .= "
										<td class='point_color'>
											<input type=hidden name=bsellprice value='$sellprice'>
											".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]."
											<input type=text class='textbox numeric' name=sellprice size=13 value='$sellprice'  maxlength=16 onkeyup='calcurate_maginrate(document.product_input)' style='TEXT-ALIGN:right;padding-right:3px;'  ".($_SESSION["admininfo"][mall_type] != "BW" ? "validation=true":"validation=false")." title='판매가(할인가)'>
											".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
										</td>";
        if(false){
            $Contents .= "
										<td class='point_color'>
											<input type=hidden name=bpremiumprice value='$premiumprice'>
											".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]."
											<input type=text class='textbox numeric' name=premiumprice size=13 value='$premiumprice'  maxlength=16  style='TEXT-ALIGN:right;padding-right:3px;'  ".($_SESSION["admininfo"][mall_type] != "BW" ? "validation=true":"validation=false")." title='프리미엄가' disabled>
											".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
										</td>";
        }
    }
    //if($max_discount_value > 0){
//    if($_SESSION["admininfo"][admin_level] == 9){
//        $Contents .= "
//										<td class='point_color'>
//											<input type=hidden name=bsellprice value='$sellprice'>
//											".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]."
//											 ".number_format($max_dcprice)."
//											".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
//										</td>";
//    }

    $Contents .= "				</tr>";

    if(count($language_list) > 0){
        foreach($language_list as $key => $li){
            $_name_c = $li['language_code'].'_coprice';
            $_value_c = $$_name_c;

            $_name_l = $li['language_code'].'_listprice';
            $_value_l = $$_name_l;

            $_name_s = $li['language_code'].'_sellprice';
            $_value_s = $$_name_s;

            $Contents .= "<tr height='35' align='center'>
                        <td class='point_color'>
                            <input type=text class='textbox numeric' name=\"".$_name_c."\" size=13 value='".$_value_c."' validation=false title='글로벌 구매단가(원가) (".$li[language_name].")' placeholder='".$li[language_name]."' >
                        </td>
                        <td class='point_color'>
                            <input type=text class='textbox numeric' name=\"".$_name_l."\" size=13 value='".$_value_l."' validation=false title='글로벌 판매가 (".$li[language_name].")' placeholder='".$li[language_name]."' >
                        </td>
                        <td class='point_color'>
                            <input type=text class='textbox numeric' name=\"".$_name_s."\" size=13 value='".$_value_s."' validation=false title='글로벌 실판매가(할인가) (".$li[language_name].")' placeholder='".$li[language_name]."' >
                        </td>
                      </tr>";
        }
    }
    $Contents .= "	

									<tr height=35 align=center class='price_add_setting' style='".($wholesale_reserve_yn == "Y" ? "":"display:none;")."'>
										<td bgcolor='#efefef' >마진율(%) /  할인율(%)</td>";
    //if($DISPLAY_WHOSALE_INFO){
    if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "					
										<td bgcolor='#fbfbfb' colspan=2 >
											<input  type=text class='textbox' size=8  name='wholesale_basic_margin' style='text-align:right;padding-right:3px;;background-color:#efefef' readonly> % /
											<input  type=text class='textbox' size=8  name='wholesale_sale_rate' style='text-align:right;padding-right:3px;;background-color:#efefef' readonly> %
										</td>";
    }
    if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "			
										<td bgcolor='#fbfbfb' colspan=".($_SESSION["admininfo"][admin_level] == 9 ? "2":"2").">
											<input  type=text class='textbox' size=8  name='basic_margin' style='text-align:right;padding-right:3px;;background-color:#efefef' readonly> % /
											<input  type=text class='textbox' size=8  name='sale_rate' style='text-align:right;padding-right:3px;;background-color:#efefef' readonly> %
										</td>";
    }
    $Contents .= "			
									</tr>";

    if($admininfo[admin_level] == '9'){
        $Contents .= "
									<tr bgcolor='#efefef' height=35 align=center class='price_add_setting' style='".($wholesale_reserve_yn == "Y" ? "":"display:none;")."'>
										<td nowrap>개별 적립금 사용유무</td>";
        //if($DISPLAY_WHOSALE_INFO){
        if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){

            $Contents .= "	
										<td bgcolor='#fbfbfb' colspan=2>
										<table>
											<tr>
												<td><!-- 적립금 소수점 자리 처리는 Round2 대신 Floor 함수를 써서 내림 적용한다 kbk 13/07/17 -->
												<input type='radio' name='wholesale_reserve_yn' id='wholesale_reserve_n' value='N' ".($wholesale_reserve_yn == "N" || $wholesale_reserve_yn == "" ? "checked":"")."><label for='wholesale_reserve_n'>미적용</label>
												<input type='radio' name='wholesale_reserve_yn' id='wholesale_reserve_y'  value='Y' ".($wholesale_reserve_yn == "Y" ? "checked":"")."><label for='wholesale_reserve_y'>적용</label> 
												</td>
												<td bgcolor='#fbfbfb'>
													<input type=hidden class='textbox integer' name=wholesale_reserve size=13 style='text-align:right;padding-right:3px;' value='$wholesale_reserve' readonly>
													<input type=hidden name=bwholesale_reserve size=15 style='text-align:right;padding-right:3px; vertical-align:middle;' value='$wholesale_reserve' readonly>

													<input type=text class='textbox integer' name=wholesale_rate1 style='text-align:right;padding-right:3px;width:50px;' value='$wholesale_reserve_rate' onkeyup=\"if(this.form.sellprice.value == ''){alert(language_data['goods_input.php']['F'][language]);}else{this.form.wholesale_reserve.value=Floor(filterNum(this.form.wholesale_sellprice.value) * this.value/100,1,'F');}\"> &nbsp;

													<select name=wholesale_rate_type style='font-size:12px;width:40px; height:22px;  vertical-align:middle;'>
														<option value=1 ".CompareReturnValue(1,$wholesale_rate_type," selected")."> % </option>
														<option value=2 ".CompareReturnValue(2,$wholesale_rate_type," selected")."> 원 </option>
													</select>
												</td>
											</tr>
										</table>
										</td>";
        }
        if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
            $Contents .= "	
										<td bgcolor='#fbfbfb' colspan=".($_SESSION["admininfo"][admin_level] == 9 ? "2":"2").">
											<table>
											<tr>
												<td>
												<input type='radio' name='reserve_yn' id='reserve_n' value='N' ".($reserve_yn == "N" || $reserve_yn == "" ? "checked":"")."><label for='reserve_n'>미적용</label>
												<input type='radio' name='reserve_yn' id='reserve_y'  value='Y' ".($reserve_yn == "Y" ? "checked":"")."><label for='reserve_y'>적용</label> 
												</td>												
												<td bgcolor='#fbfbfb'><!-- 적립금 소수점 자리 처리는 Round2 대신 Floor 함수를 써서 내림 적용한다 kbk 13/07/17 -->
													<input type=hidden class='textbox integer' name=reserve size=13 style='text-align:right;padding-right:3px;' value='$reserve' readonly>
													<input type=hidden name=breserve size=15 style='text-align:right;padding-right:3px; vertical-align:middle;' value='$reserve' readonly>
							
													<input type=text class='textbox integer' name=rate1 style='text-align:right;padding-right:3px;width:50px;' value='$reserve_rate' onkeyup=\"if(this.form.sellprice.value == ''){alert(language_data['goods_input.php']['F'][language]);}else{this.form.reserve.value=Floor(filterNum(this.form.sellprice.value) * this.value/100,1,'F');}\"> &nbsp;

													<select name=rate_type style='font-size:12px;width:40px; height:22px;  vertical-align:middle;'>
														<option value=1 ".CompareReturnValue(1,$rate_type," selected")."> % </option>
														<option value=2 ".CompareReturnValue(2,$rate_type," selected")."> 원 </option>
													</select>
												</td>
											</tr>
										</table>
										</td>";
        }
        $Contents .= "	
									</tr>";
    }else{
        $Contents .= "	<input type='hidden' name='wholesale_reserve_yn' id='wholesale_reserve_n' value='N'>
							<input type='hidden' name='reserve_yn' id='reserve_n' value='N'>
							<input type=hidden name=reserve value='".$reserve."'>
							<input type='hidden' name='rate_type' id='rate_type' value='1'>";
    }
    if($goods_input_type != "globalsellertool"){
        $Contents .= "	
									<tr height=35 class='price_add_setting' style='display:none;'>
										<td bgcolor='#efefef' align=center>기본 시작 수량</td>";
        //if($DISPLAY_WHOSALE_INFO){
        if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
            $Contents .= "	
										<!--<td bgcolor='#fbfbfb' colspan=2  style='padding-left:10px;' >
											<input  type=text class='textbox numeric' size=4  name='wholesale_allow_basic_cnt' id='wholesale_allow_basic_cnt' value= '".$wholesale_allow_basic_cnt."' style='padding-left:3px;'> <strong>0 일경우 최소 판매수량 상관없음</strong>
										</td>-->";
        }
        if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
            $Contents .= "	
										<td bgcolor='#fbfbfb' colspan=".($_SESSION["admininfo"][admin_level] == 9 ? "2":"2")." style='padding-left:10px;'>
											<input  type=text class='textbox numeric' size=4  name='allow_basic_cnt' id='allow_basic_cnt' value= '".$allow_basic_cnt."' style='padding-left:3px;'> <strong>0 일경우 최소 판매수량 상관없음</strong>
										</td>";
        }
        $Contents .= "	
									</tr>";


            $Contents .= "
										<tr height=35 class='price_add_setting' style='display:none'>
											<td bgcolor='#efefef'  align=center>최대 판매 수량</td>";
            //if($DISPLAY_WHOSALE_INFO){
            if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
                $Contents .= "	
											<td bgcolor='#fbfbfb' colspan=2  style='padding-left:10px;'>
												<input  type=text class='textbox numeric' size=4  name='wholesale_allow_max_cnt' id='wholesale_allow_max_cnt' value= '".$wholesale_allow_max_cnt."'  style='padding-left:3px;'> <strong>0 일경우 최대 판매수량 상관없음</strong>
											</td>";
            }
            if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
                $Contents .= "	
											<td bgcolor='#fbfbfb' colspan=".($_SESSION["admininfo"][admin_level] == 9 ? "2":"2")." style='padding-left:10px;'>
												<input  type=text class='textbox numeric' size=4  name='allow_max_cnt' id='allow_max_cnt' value= '".$allow_max_cnt."' style='padding-left:3px;'> <strong>0 일경우 최대 판매수량 상관없음</strong>
											</td>";
            }
            $Contents .= "	
										</tr>";


        $Contents .= "
									<tr height=35 class='price_add_setting' style='display:none'>
										<td bgcolor='#efefef'  align=center>ID당 구매 수량</td>";
        //if($DISPLAY_WHOSALE_INFO){
        if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
            $Contents .= "	
										<!--<td bgcolor='#fbfbfb' colspan=2  style='padding-left:10px;'>
											<input  type=text class='textbox numeric' size=4  name='wholesale_allow_byoneperson_cnt' id='wholesale_allow_byoneperson_cnt' value= '".$wholesale_allow_byoneperson_cnt."'  style='padding-left:3px;'> <strong>0 일경우 최대 판매수량 상관없음</strong>
										</td>-->";
        }
        if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
            $Contents .= "	
										<td bgcolor='#fbfbfb' colspan=".($_SESSION["admininfo"][admin_level] == 9 ? "2":"2")." style='padding-left:10px;'>
											<input  type=text class='textbox numeric' size=4  name='allow_byoneperson_cnt' id='allow_byoneperson_cnt' value= '".$allow_byoneperson_cnt."' style='padding-left:3px;'> <strong>0 일경우 최대 판매수량 상관없음</strong>
										</td>";
        }
        $Contents .= "	
									</tr>";

        if($admininfo[admin_level] == '9'){
            $Contents .= "	
									<!--tr bgcolor='#efefef' height=35 align=center>
										<td nowrap> 한정판매수량</td>
										<td bgcolor='#fbfbfb' ".($DISPLAY_WHOSALE_INFO ? "colspan=".($max_discount_value > 0 ? "6":"5")."":"colspan=2")."  align='left'>
											<input type='radio' name='allow_order_type' id='allow_order_type_0' value='0' ".($allow_order_type== '0' || $allow_order_type== ''?'checked':'')." title='한정판매수량'><label for='allow_order_type_0'> 미적용</label>
											<input type='radio' name='allow_order_type' id='allow_order_type_1' value='1' ".($allow_order_type== '1' ?'checked':'')." title='한정판매수량'> <label for='allow_order_type_1'> 적용</label>&nbsp;&nbsp;
											<input type='text' class='textbox numeric' name='allow_order_cnt_byonesell' id='allow_order_cnt_byonesell' title='최대구매수량' style='width:50px;' value='".$allow_order_cnt_byonesell."' com_numeric=true> 개
											<span class='small blu'> * 현재 판매가능한 수량은  ".allow_max_ordercnt($id)." 개 입니다. </span>
										</td>
									</tr-->";
        }else{
            $Contents .= "	<input type='hidden' name='allow_order_type' id='allow_order_type_0' value='".$allow_order_type."'>
							<input type='hidden' name='allow_order_cnt_byonesell' id='allow_order_cnt_byonesell' value='".$allow_order_cnt_byonesell."'>";
        }

        $Contents .= "	
									<tr height=35 class='changeable_area' style='display:none;'>
										<td bgcolor='#efefef' align=center>복수 구매 할인</td>";
        //if($DISPLAY_WHOSALE_INFO){
        if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
            $Contents .= "	
										<td bgcolor='#fbfbfb' colspan=2 style='padding:5px 0px 5px 8px;'>
											<table cellpadding=0 cellspacing=0 border='0' width=100%  id='whole_product_mult_rate_table'>
											<col width=20>
											<col width=40>
											<col width=45>
											<col width=22%>
											<col width=23%>
											<col width=17%>
											<col width='*'>";

            $sql = "select * from shop_product_mult_rate where pid = '".$id."' and is_wholesale = 'W'";
            $rdb->query($sql);
            $whole_rate_array = $rdb->fetchall();
            for($i =0;$i<count($whole_rate_array) || $i<=0;$i++){

                $Contents .= "
											<tr depth=1 item=1 id='whole_product_mult_rate_tr' height=27>
												<td>
													<input type='checkbox' class='textbox numeric' name='wholesale_rate[".$i."][whole][is_use]' id='whole_is_use' value='1' ".($whole_rate_array[$i][is_use] == "1"?'checked':'')." onclick=\"if( $(this).attr('checked') == 'checked'){ $(this).closest('tr').find('*[id^=whole_]').attr('disabled',false);}else{ $(this).closest('tr').find('*[id^=whole_]').not('input[type=checkbox]').attr('disabled','disabled');};\">
													<input type='hidden' name='wholesale_rate[".$i."][whole][mr_id]' id='whole_mr_id' value='".$whole_rate_array[$i][mr_id]."'>
												</td>
												<td>
													<input  type=text class='textbox numeric'  name='wholesale_rate[".$i."][whole][sell_mult_cnt]' id='whole_sell_mult_cnt' value='".$whole_rate_array[$i][sell_mult_cnt]."' style='padding-left:0px;width:30px;' ".($whole_rate_array[$i][is_use] == "1" ? '':'disabled').">
												</td>
												<td> 개 이상 </td>
												<td nowrap><input type='text' class='textbox numeric' name='wholesale_rate[".$i."][whole][rate_price]' id='whole_rate_price' value='".$whole_rate_array[$i][rate_price]."' style='padding-left:3px;width:40px;' ".($whole_rate_array[$i][is_use] == "1" ? '':'disabled')."> 
													<select name='wholesale_rate[".$i."][whole][rate_div]' id='whole_rate_div' ".($whole_rate_array[$i][is_use] == "1" ? '':'disabled')." onchange=\"if( $(this).val() == 2){ $(this).closest('tr').find('select[id^=whole_]').not('select[id=whole_rate_div]').attr('disabled','disabled'); }else{ $(this).closest('tr').find('select[id^=whole_]').not('select[id=whole_rate_div]').attr('disabled',false); }\">
														<option value='1' ".($whole_rate_array[$i][rate_div] == "1"?'selected':'').">%</option>
														<option value='2' ".($whole_rate_array[$i][rate_div] == "2"?'selected':'').">원</option>
													</select>

												</td>
												<td style='padding-right:2px;' nowrap>
													사사오입 
													<select name='wholesale_rate[".$i."][whole][round_type]' id='whole_round_type' ".($whole_rate_array[$i][is_use] == "1" ? '':'disabled').">
														<option value='1' ".($whole_rate_array[$i][round_type] == "1" ? 'selected':'').">반올림</option>
														<option value='3' ".($whole_rate_array[$i][round_type] == "3" || $whole_rate_array[$i][round_type] == "" ? 'selected':'').">내림</option>
														<option value='4' ".($whole_rate_array[$i][round_type] == "4" ? 'selected':'').">올림</option>
													</select>
												</td>
												<td nowrap>
													<select name='wholesale_rate[".$i."][whole][round_cnt]' id='whole_round_cnt' ".($whole_rate_array[$i][is_use] == "1" ? '':'disabled').">
														<option value='1' ".($whole_rate_array[$i][round_cnt] == "1" ? 'selected':'').">일 자리</option>
														<option value='2' ".($whole_rate_array[$i][round_cnt] == "2" ? 'selected':'').">십 자리</option>
														<option value='3' ".($whole_rate_array[$i][round_cnt] == "3" ? 'selected':'').">백 자리</option>
														<option value='4' ".($whole_rate_array[$i][round_cnt] == "4" ? 'selected':'').">천 자리</option>
													</select>
													 자리
												</td>
												<td style='padding-left:10px;'>
													<img src='../images/i_add.gif' border=0 style='cursor:pointer;' align=absmiddle onclick=\"AddMultTable('whole_product_mult_rate_table','whole');\">
													<img src='../images/i_close.gif' style='cursor:pointer;' id='del_whole_mult_table_tr' align=absmiddle onclick=\"DelMultTable('whole');\">
												</td>
											</tr>";
            }
            $Contents .= "	
											</table>
										</td>";
        }

        if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
            $Contents .= "	
										<td bgcolor='#fbfbfb' colspan=".($max_discount_value > 0 ? "3":"3")." style='padding:7px 0px 7px 8px;'>
											<table cellpadding=0 cellspacing=0 border='0' width=100% id='retail_product_mult_rate_table'>
											<col width=20>
											<col width=40>
											<col width=45>
											<col width=22%>
											<col width=23%>
											<col width=17%>
											<col width='*'>";

            $sql = "select * from shop_product_mult_rate where pid = '".$id."' and is_wholesale = 'R'";
            $rdb->query($sql);
            $retail_rate_array = $rdb->fetchall();
            for($i =0;$i<count($retail_rate_array) || $i<=0;$i++){
                $Contents .= "	
											<tr depth=1 item=1 id='retail_product_mult_rate_tr' height=27>
												<td>
													<input type='checkbox' name='wholesale_rate[".$i."][retail][is_use]' id='retail_is_use' value='1' ".($retail_rate_array[$i][is_use] == "1"?'checked':'')." onclick=\"if( $(this).attr('checked') == 'checked'){ $(this).closest('tr').find('*[id^=retail_]').attr('disabled',false);}else{ $(this).closest('tr').find('*[id^=retail_]').not('input[type=checkbox]').attr('disabled','disabled');};\">
													<input type='hidden' name='wholesale_rate[".$i."][retail][mr_id]' id='retail_mr_id' value='".$retail_rate_array[$i][mr_id]."'>
												</td>
												<td nowrap>
													<input  type=text class='textbox numeric'  name='wholesale_rate[".$i."][retail][sell_mult_cnt]' id='retail_sell_mult_cnt' value='".$retail_rate_array[$i][sell_mult_cnt]."' style='padding-left:0px;width:30px;' ".($retail_rate_array[$i][is_use] == "1" ? '':'disabled').">
												</td>
												<td> 개 이상 </td>
												<td nowrap><input type='text' class='textbox numeric' name='wholesale_rate[".$i."][retail][rate_price]' id='retail_rate_price' value='".$retail_rate_array[$i][rate_price]."' style='padding-left:3px;width:40px;'  ".($retail_rate_array[$i][is_use] == "1" ? '':'disabled')."> 
													<select name='wholesale_rate[".$i."][retail][rate_div]' id='retail_rate_div'  ".($retail_rate_array[$i][is_use] == "1" ? '':'disabled')." onchange=\"if( $(this).val() == 2){ $(this).closest('tr').find('select[id^=retail_]').not('select[id=retail_rate_div]').attr('disabled','disabled'); }else{ $(this).closest('tr').find('select[id^=retail_]').not('select[id=retail_rate_div]').attr('disabled',false); }\">
														<option value='1' ".($retail_rate_array[$i][rate_div] == "1"?'selected':'').">%</option>
														<option value='2' ".($retail_rate_array[$i][rate_div] == "2"?'selected':'').">원</option>
													</select>
												</td>
												<td style='padding-right:2px;' nowrap>
													사사오입 
													<select name='wholesale_rate[".$i."][retail][round_type]' id='retail_round_type'  ".($retail_rate_array[$i][is_use] == "1" ? '':'disabled').">														
														<option value='1' ".($retail_rate_array[$i][round_type] == "1" ? 'selected':'').">반올림</option>
														<option value='3' ".($retail_rate_array[$i][round_type] == "3" || $retail_rate_array[$i][round_type] == "" ? 'selected':'').">내림</option>
														<option value='4' ".($retail_rate_array[$i][round_type] == "4" ? 'selected':'').">올림</option>
													</select>
												</td>
												<td nowrap>
													<select name='wholesale_rate[".$i."][retail][round_cnt]' id='retail_round_cnt'  ".($retail_rate_array[$i][is_use] == "1" ? '':'disabled').">
														<option value='1' ".($retail_rate_array[$i][round_cnt] == "1" ? 'selected':'').">일 자리</option>
														<option value='2' ".($retail_rate_array[$i][round_cnt] == "2" ? 'selected':'').">십 자리</option>
														<option value='3' ".($retail_rate_array[$i][round_cnt] == "3" ? 'selected':'').">백 자리</option>
														<option value='4' ".($retail_rate_array[$i][round_cnt] == "4" ? 'selected':'').">천 자리</option>

													</select>
													 자리
												</td>
												<td style='padding-left:10px;'>
													<img src='../images/i_add.gif' border=0 style='cursor:pointer;' align=absmiddle onclick=\"AddMultTable('retail_product_mult_rate_table','retail','".$mr_id."');\" id='btn_option_detail_add'>
													<img src='../images/i_close.gif' style='cursor:pointer;' id='del_retail_mult_table_tr' align=absmiddle onclick=\"DelMultTable('retail');\">
												</td>
											</tr>";
            }
            $Contents .= "	
											</table>
										</td>
										";
        }
        $Contents .= "	
									</tr>";
    }
    $Contents .= "	
								</table>
								<div  style='height:25px;background-color:#ffffff;text-align:center;padding-top:12px;border:1px solid silver;margin-top:3px;' onclick=\"moreDisplay(this, 'tr.price_add_setting');\"><img src='../v3/images/btn/bt_arrow_down.png' align=absmiddle></div>";
}else{

    $Contents .= "					<table cellpadding=1 cellspacing=1 bgcolor=silver width=100% >
								<col width=25%>
								<col width=25%>
								<col width=25%>";

    if($_SESSION["admininfo"][mall_type] != "O"){
        $Contents .= "			<col width=25%>";
    }
    $Contents .= "				<tr bgcolor='#efefef' height=35 align=center>";
    $Contents .= "
										<td ".(($_SESSION["admininfo"][mall_type] == "O" || $_SESSION["admininfo"][mall_type] == "E") ? "style='display:none;'":"").">".($_SESSION["admininfo"][admin_level] == 9 ? "<b>구매단가(원가) <img src='".$required3_path."'></b>":"<b>공급가격 <img src='".$required3_path."'></b>")." </td>";
    $Contents .= "					<td >
											<b>정가 </b> <img src='".$required3_path."'>
											".($_SESSION["admininfo"][admin_level] == 9 ? "<span class=small onclick=\"copyPrice(document.product_input, document.product_input.listprice.value, 1);calcurate_maginrate(document.product_input)\" style='cursor:pointer;color:red'> 복사→</span>":"")."</td>
										<td ><b>판매가(할인가)</b> <img src='".$required3_path."'></td>";

    if($_SESSION["admininfo"][mall_type] != "O"){
        $Contents .= "
										<td >마진(%) </td>";
    }
    $Contents .= "

									</tr>
									<tr bgcolor='#fbfbfb' height=35 align=center>";

    $Contents .= "
										<td ".(($_SESSION["admininfo"][mall_type] == "O" || $_SESSION["admininfo"][mall_type] == "E") ? "style='display:none;'":"")."><input type=hidden name=bcoprice value='$coprice' >
										".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]."
											<input type=text class='textbox numeric' name=coprice size=13 style='text-align:right;padding-right:3px;' value='$coprice'  onkeyup='calcurate_maginrate(document.product_input)' style='TEXT-ALIGN:right;padding-right:3px;".$colorString."'  $message  $readonlyString ".(($_SESSION["admininfo"][mall_type] == "O" || $_SESSION["admininfo"][mall_type] == "E") ? "":"validation=true")." title='구매단가(공급가)'>
											".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
										</td>";
    $Contents .= "							<td>
											<input type=hidden name=blistprice value='$listprice'> ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." <input type=text class='textbox numeric' name=listprice value='$listprice' size=13  style='TEXT-ALIGN:right;padding-right:3px;".$colorString."'  maxlength=16  $message  $readonlyString ".($_SESSION["admininfo"][mall_type] != "BW" ? "validation=true":"validation=false")." title='정가'> ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."</td>
										<td>
											<input type=hidden name=bsellprice value='$sellprice'>
											".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]."
											<input type=text class='textbox numeric' name=sellprice size=13 value='$sellprice'  maxlength=16 onkeyup='calcurate_maginrate(document.product_input)' style='TEXT-ALIGN:right;padding-right:3px;".$colorString." ' $message  $readonlyString ".($_SESSION["admininfo"][mall_type] != "BW" ? "validation=true":"validation=false")." title='판매가(할인가)'>
											".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
										</td>";

    $Contents .= "
										<td ".(($_SESSION["admininfo"][mall_type] == "O" || $_SESSION["admininfo"][mall_type] == "E") ? "style='display:none;'":"")."> <input  type=text class='textbox' size=13  name='basic_margin' style='text-align:right;padding-right:3px;;background-color:#efefef' readonly></td>";



    $Contents .= "
									</tr>
									</table>";

    $Contents .= "
								</td>
							</tr>";


    $Contents .="	<tr ".($_SESSION["admininfo"][admin_level] != 9 ? "style='display:none'":"").">
								<td>
									<table cellpadding=1 cellspacing=1 bgcolor=silver width=100% border=0>
									<col width=25% />
									<col width=25% />
									<col width=25% />
									<col width=25% />
									<tr bgcolor='#efefef' height=35 align=center>

										<td nowrap>개별 적립금 사용유무</td>
										<td bgcolor='#fbfbfb'>
										<input type='radio' name='reserve_yn' value='Y' ".($reserve_yn == "Y" ? "checked":"").">적용 <input type='radio' name='reserve_yn' value='N' ".($reserve_yn == "N" || $reserve_yn == "" ? "checked":"")."> 적용안함
										</td>
										<td nowrap>개별 적립금 </td>
										<td bgcolor='#fbfbfb'><!-- 적립금 소수점 자리 처리는 Round2 대신 Floor 함수를 써서 내림 적용한다 kbk 13/07/17 -->
											<input type=text class='textbox integer' name=reserve size=13 style='text-align:right;padding-right:3px;' value='$reserve'>
											<input type=hidden name=breserve size=15 style='text-align:right;padding-right:3px; vertical-align:middle;' value='$reserve' readonly>
											<select name=rate1 style='font-size:12px;width:50px; height:22px;  vertical-align:middle;' onchange=\"if(this.form.sellprice.value == ''){alert(language_data['goods_input.php']['F'][language]);}else{this.form.reserve.value=Floor(filterNum(this.form.sellprice.value) * this.value/100,1,1);}\">
												<option value=0 ".CompareReturnValue(0,$reserve_rate," selected").">0%</option>
												<option value='0.5' ".CompareReturnValue(0.5,$reserve_rate," selected").">0.5%</option>
												<option value=1 ".CompareReturnValue(1,$reserve_rate," selected").">1%</option>
												<option value='1.5' ".CompareReturnValue(1.5,$reserve_rate," selected").">1.5%</option>
												<option value='2' ".CompareReturnValue(2,$reserve_rate," selected").">2%</option>
												<option value='2.5' ".CompareReturnValue(2.5,$reserve_rate," selected").">2.5%</option>
												<option value=3 ".CompareReturnValue(3,$reserve_rate," selected").">3%</option>
												<option value=5 ".CompareReturnValue(5,$reserve_rate," selected").">5%</option>
												<option value=7 ".CompareReturnValue(7,$reserve_rate," selected").">7%</option>
												<option value=10 ".CompareReturnValue(10,$reserve_rate," selected").">10%</option>
												<!--option value=37 >37%</option-->
											</select>
										</td>

									</tr>
									</table>
								 
							</td>
							
						</tr>
					</table>
					";
}
$Contents .="
					</td>
					<!--td style='vertical-align:top;padding-top:40px;'><img src='/admin/images/btn_group_open.png' open_src='/admin/images/btn_group_close.png' close_src='/admin/images/btn_group_open.png' alt='Minus' align=absmiddle style='margin-left:5px;' onclick=\"moreDisplay(this, 'tr.price_add_setting');\"></td-->
				</tr>
						<tr>
							<td colspan=2>";

$help_text2 = getTransDiscription($page_code,'J');


$help_text = "				<table cellpadding=1 cellspacing=0 bgcolor=#c0c0c0 width=100%>
								<col width=20%>
								<col width=20%>
								<col width=20%>
								<col width=20%>
								<col width=20%>
								<tr bgcolor='#ffffff' height=25 align=center>
									<td align=left>카드수수료(<input type=text class='textbox' name=card_pay value='4' size=2 readonly style='border:1px;text-align:center'>% 기준)</td>
									<td>적립금사용(현금) </td>
									<td >무이자 수수료</td>
									<td ><b>토탈수수료</b></td>
									<td rowspan=2> &nbsp;<input type=button onclick='calcurate_margin(document.product_input);' value='계산하기'></td>
								</tr>
								<tr bgcolor='#ffffff' height=25 align=center>

									<td align=left><input type=text class='textbox' name=card_price value='' style='width:80%;text-align:right;background-color:#efefef' size=8 readonly></td>
									<td> + <input size=8 type=text class='textbox' name='reserve_price' style='text-align:right;width:80%;background-color:#efefef' readonly></td>
									<td> + <input size=8 type=text class='textbox' name='nointerest_price' style='text-align:right;width:80%;background-color:#efefef' value='' readonly></td>
									<td> = <input size=8 type=text class='textbox' name='margin' style='text-align:right;width:80%;background-color:#efefef' value='' readonly></td>
								</tr>
								<tr bgcolor='#ffffff'>
									<td  colspan=5 style='padding-top:10px;'>
									$help_text2
									</td>
								</tr>
								</table>";

$price_history_text = "
								<table cellpadding=1 cellspacing=0 class='list_table_box' bgcolor=#c0c0c0 width=100%>
								<col width=15%>
								<!--<col width=15%>-->
								<!--<col width=14%>-->
								<col width=13%>
								<col width=13%>
								<col width=20%>
								<col width=10%>
								<tr bgcolor='#ffffff' height=25 align=center>
									<td align=left class=s_td>구매단가(원가)</td>
									<!--<td class=m_td>도매 판매가</td>-->
									<!--<td class=m_td>도매 할인가</td>-->
									<td class=m_td>소매 판매가</td>
									<td class=m_td>소매 할인가</td>
									<td class=m_td>수정 담당자</td>
									<td class=e_td>변경일자</td>
								</tr>";
$sql = "select * from shop_priceinfo where pid = '".$id."' order by regdate desc  ";

$db->query($sql);

$price_historys = $db->fetchall();

for($i=0;$i < count($price_historys);$i++){
    $price_history_text .= "
								<tr bgcolor='#ffffff' height=25 align=center>";
    $price_history_text .= "<td align=left class='list_box_td list_bg_gray' >".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." ".number_format($price_historys[$i][coprice])." ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."</td>";
    $price_history_text .= "
									<!--<td class='list_box_td ' >".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." ".number_format($price_historys[$i][wholesale_price])." ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."</td>-->
									<!--<td class='list_box_td list_bg_gray' >".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." ".number_format($price_historys[$i][wholesale_sellprice])." ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."</td>-->
									<td class='list_box_td ' >".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." ".number_format($price_historys[$i][listprice])." ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."</td>
									<td class='list_box_td list_bg_gray' >".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." ".number_format($price_historys[$i][sellprice])." ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."</td>
									<td class='list_box_td ' > ".$price_historys[$i][charger_info]." </td>
									<td class='list_box_td list_bg_gray' >".$price_historys[$i][regdate]."</td>
								</tr>";
}
$price_history_text .= "	</table>
";

$Contents .= "<div id='price_info' style='width:100%;'>".HelpBox("가격정보입력", $help_text2)."</div>";
$Contents .= "<div id='detail_price_info' style='position:relative;display:none'>".HelpBox("판매수수료 상세내역", $help_text, 200)."</div>";
$Contents .= "<div id='price_history_info' style='position:relative;display:none'>".$price_history_text."</div>";

$Contents .= "			</td>

						</tr>
					</table>
					</div>
				</div>
			<!--/div-->
			</td>
			
		</tr>
		</table><div style='clear:both;height:70px;'></div>
	</div>
	";

/*
$admin_delievery_policy = getTopDeliveryPolicy($db);

if($id ==""){ // 신규 상품 등록일 경우 해당 입점업체의 정책대로 저장 한다.


	//$db2->fetch();

		if($db2->dbms_type == "oracle"){
						$sql = "select case when delivery_policy != '1' then delivery_freeprice else ".$admin_delievery_policy[delivery_freeprice]." end  as delivery_freeprice ,
				case when delivery_policy != '1' then delivery_price else ".$admin_delievery_policy[delivery_price]." end as delivery_price,
				case when delivery_policy != '1' then delivery_basic_policy else '".$admin_delievery_policy[delivery_basic_policy]."' end as delivery_basic_policy
				from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '".$_SESSION["admininfo"][company_id]."' ";

		}else{
			$sql = "select if(delivery_policy != '1',delivery_freeprice, '".$admin_delievery_policy[delivery_freeprice]."') as delivery_freeprice ,
				if(delivery_policy != '1',delivery_price, '".$admin_delievery_policy[delivery_price]."') as delivery_price,
				if(delivery_policy != '1',delivery_basic_policy, '".$admin_delievery_policy[delivery_basic_policy]."') as delivery_basic_policy
				from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '".$_SESSION["admininfo"][company_id]."' ";
		}


}else{
	if($db2->dbms_type == "oracle"){
			$sql = "select case when delivery_policy != '1' then delivery_freeprice else ".$admin_delievery_policy[delivery_freeprice]." end  as delivery_freeprice ,
			case when delivery_policy != '1' then delivery_price else ".$admin_delievery_policy[delivery_price]." end as delivery_price,
			case when delivery_policy != '1' then delivery_basic_policy else '".$admin_delievery_policy[delivery_basic_policy]."' end as delivery_basic_policy
			from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '".$admin."' ";

	}else{
	$sql = "select if(delivery_policy != '1',delivery_freeprice, ".$admin_delievery_policy[delivery_freeprice].") as delivery_freeprice ,
			if(delivery_policy != '1',delivery_price, ".$admin_delievery_policy[delivery_price].") as delivery_price,
			if(delivery_policy != '1',delivery_basic_policy, '".$admin_delievery_policy[delivery_basic_policy]."') as delivery_basic_policy
			from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '".$admin."' ";
	}

}

$db2->query($sql);
$db2->fetch();
*/
//배송비 정책 시작 2014-05-13 이학봉
if($_SESSION["admininfo"][mall_type]){
    if($goods_input_type == "inventory"){
        $_Contents = "
		<table cellpadding=0 cellspacing=0><tr><td ><b class=blk> <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 상품별 배송 정책 설정 </b><a name='productDeliverySet'></a></td><td><input type=checkbox name='delivery_setting_display_yn' id='delivery_setting_display_yn' onclick=\"$('#delivery_setting_zone').toggle();\"></td><td><label for='delivery_setting_display_yn'>표시</label></td></tr></table>";
    }else{
        $_Contents = "<b class=blk > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 상품별 배송 정책 설정 </b><a name='productDeliverySet'></a>";
    }

    $Contents .= "
		<div id='delivery_setting_table'>
	
			<table width='100%' cellpadding=0 cellspacing=0 id='delivery_setting_zone_title'>
				<tr height=30>
					<td style='padding-bottom:10px;'>
						".colorCirCleBox("#efefef","100%","<table cellpadding=0 cellspacing=0 width=100%><tr><td style='padding:5px 5px 5px 10px;'>".$_Contents."</td><td align=right style='padding:0px 0px 5px 0px;'><img src='/admin/images/btn_group_open.png' open_src='/admin/images/btn_group_close.png' close_src='/admin/images/btn_group_open.png' alt='Minus' align=absmiddle style='margin-left:5px;' onclick=\"moreDisplay(this, 'div#delivery_setting_zone' , 'div#delivery_setting_result_zone');\"></td></tr></table>")."
					</td>
				</tr>
			</table>";

    $Contents .= "	<input type='hidden' name='delivery_company' value='".($delivery_company == "" ? "MI":$delivery_company)."'>
				<div ".($goods_input_type == "inventory" ? "style='display:none;'":"")." id='delivery_setting_zone' style='display:none;'>
				
			<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box'>
				<col width='18%'>
				<col width='*'>
				<tr>
					<td class='input_box_title' nowrap> 배송타입 선택</td>
					<td class='input_box_item' nowrap>";

    if($admininfo[admin_level] == '9'){
        $Contents .= "		
					<input type='radio' name='delivery_type' value='1' id='delivery_type_1' ".($delivery_type == '1' || $delivery_type == "" ? "checked":"")."  onclick=\"changeDeliveryArea_gi('1', '".$id."');\"><label for='delivery_type_1'>통합 배송</label>
					<div style='display:none;'><input type='radio' name='delivery_type' value='2' id='delivery_type_2' ".($delivery_type == '2' ? "checked":"")."  onclick=\"changeDeliveryArea_gi('2', '".$id."');\"><label for='delivery_type_2'>입점업체 개별 배송</label></div>";
    }else{
        $Contents .= "		
					<div style='display:none;'><input type='radio' name='delivery_type' value='2' id='delivery_type_2' ".($delivery_type == '2' || $delivery_type == "" ? "checked":"")." ><label for='delivery_type_2'>입점업체 개별 배송</label></div>";
    }

    $Contents .= "		
					</td>
				</tr>
				<tr>
					<td class='input_box_title' nowrap> 상품별 개별정책 설정</td>
					<td class='input_box_item' nowrap>
					<input type='radio' name='delivery_policy' value='1' id='delivery_policy_1' ".($delivery_policy == '1' || $delivery_policy == "" ? "checked":"")." onclick=\"deliveryTypeView('1')\" ><label for='delivery_policy_1'>사용안함</label>
					<input type='radio' name='delivery_policy' value='2' id='delivery_policy_2' ".($delivery_policy == '2' ? "checked":"")." onclick=\"deliveryTypeView('2')\"><label for='delivery_policy_2'>사용</label>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' nowrap> 배송비 정책";
    if($admininfo[admin_level] == '9'){
        $Contents .= "		
								<div id='delivery_template_img_r' ".($delivery_policy == '1' || $id == "" ? "style='display:none;'":"").">
									<input type='button' value='상세 설정 확인' onclick=\"javascript:popup_delivery_template('".$id."','retail')\" style='cursor:pointer;'>
								</div>";
    }

    if($delivery_type == '1'){	//통합배송일경우 본사 정책
        $sql = "select company_id from common_company_detail where com_type = 'A' limit 0,1";
        $db->query($sql);
        $db->fetch();
        $admin = $db->dt[company_id];
    }else{
        $admin = $admin;
    }

    if($id){
        $sql = "select * from shop_delivery_template where dt_ix in (select dt_ix from shop_product_delivery where pid = '".$id."')";
        $db->query($sql);

        if($db->total == '0'){
            $sql = "select * from shop_delivery_template where company_id = '".$admin."' and is_basic_template = '1' and product_sell_type = 'R'";
            $db->query($sql);
        }
    }else{

        $sql = "select * from shop_delivery_template where company_id = '".$admin."' and is_basic_template = '1' and product_sell_type = 'R'";
        $db->query($sql);
    }

    if($db->total){
        $template_array = $db->fetchall();

        $template_contents = "<span id='basic_template_delivery'>".get_delivery_policy_text($template_array,'0')."</span>";
        $template_contents .="
						<input type='checkbox' name='dt_ix[".$template_array[0][product_sell_type]."][".$template_array[0][delivery_div]."][template_check]' id='template_basic_dt_check' value='1' checked >
						<input type='hidden' name='dt_ix[".$template_array[0][product_sell_type]."][".$template_array[0][delivery_div]."][dt_ix]' id='template_basic_dt_r' value='".$template_array[0][dt_ix]."' validation=true>";
    }

    $Contents .= "		
					</td>
					<td class='input_box_item' id='policy_input_retail' ".($delivery_policy == '1' || $id == "" ? "style='display:none;padding:3px;'":"style='padding:3px;'")." nowrap>
						<table cellpadding=0 cellspacing=0 width=99% class='input_table_box' >
						<col width='18%'>
						<col width='*'>
						<tr bgcolor='#ffffff' height=25 align=center>
							<td class='input_box_title' nowrap> 배송비 정책(소매)</td>
							<td class='input_box_item'>
								<input type='radio' class='textbox' name='delivery_div_show' id='delivery_div_1' style='border:0px;' onclick=\"showDeliveryFromAjax_gi('1', '".$id."');\" ".($template_array[0][delivery_div] == 1 || empty($id) ? "checked" : "")."><label for='delivery_div_1'>택배/방문수령</label>
								<!-- <input type='radio' class='textbox' name='delivery_div_show' id='delivery_div_3' style='border:0px;' onclick=\"showDeliveryFromAjax_gi('3', '".$id."');\" ".($template_array[0][delivery_div] == 3 ? "checked" : "")."><label for='delivery_div_3'>직배송차량</label> --></br>

								<div id='delivery_template_area' style='float:left;padding-top:2px;'>";
    if(empty($id)){
        $Contents .= select_delivery_template($admin,'R','1',$id,'checked');
    }else{
        $Contents .= select_delivery_template($admin,'R',$template_array[0][delivery_div],$id,'checked');
    }

    $Contents .= "
								</div>
							</td>
						</tr>
						</table>
					</td>

					<td class='input_box_item' id='policy_text_retail' style='padding:0 0 0 10px;".($delivery_policy == '2' ? "display:none;":"")."' nowrap>";

    $Contents .= $template_contents;
    $Contents .="
					</td>
				</tr>";


    $Contents .="
			</table>";

    $help_text_delivery = "
			<table cellpadding=1 cellspacing=0 width=100%>
				<col width='8'>
				<col width='*'>
				<tr bgcolor='#ffffff' height=25 align=center>
					<td><img src='/admin/image/icon_list.gif' ></td>
					<td class='small' align='left'>환불 시 현재 입력된 배송 정보를 바탕으로 배송비가 계산됩니다.</td>
				</tr>
				<tr bgcolor='#ffffff' height=25 align=center>
					<td><img src='/admin/image/icon_list.gif' ></td>
					<td class='small' align='left'>결제 시 설정된 값과 환불 시 설정된 값이 다를 경우 배송비 책정에 차이가 있을 수 있습니다.</td>
				</tr>
			</table>";

    $Contents .="
			</div>
			<div id='delivery_setting_result_zone' style='padding:20px;border:1px solid silver;margin-bottom:0px;'>".get_delivery_policy_text($template_array,'0')."</div>
			<div style='clear:both;height:70px;'></div>
		</div>";
}else{
    $Contents .= "<input type='hidden' name='delivery_company' value='MI' >";
}

//배송비 정책 끝 2014-05-13 이학봉


if($_SESSION["admininfo"][mall_type] !='F'  ){//소호형일때는 안나오도록 2012-08-28 홍진영
    $_Contents = "<div style='display:none;' class='none_div' >";
    if($_SESSION["admininfo"][mall_use_multishop] ){
        if($_SESSION["admininfo"][admin_level] ==9 || $_SESSION["admininfo"][admin_level] == 8 ){
            if($goods_input_type == "inventory"){

                $_Contents .= "
                 
				<table cellpadding=0 cellspacing=0 >
				<tr>
					<td style='width:15px;'>
						<b class=blk > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'>수수료정보 </b>
					</td>
					<td>
						<input type=checkbox name='fee_setting_display_yn' id='fee_setting_display_yn' onclick=\"$('#fee_setting_zone').toggle()\">
					</td>
					<td>
						<label for='fee_setting_display_yn'>표시</label>
					</td>
				</tr>
				</table>";
            }else{
                $_Contents .= "<b class=blk  > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 수수료정보 </b>";
            }

            $Contents .= "	
		<div id='fee_setting_zone_table' style='display:none;'>

			<table width='100%' cellpadding=0 cellspacing=0 ".($_SESSION["admininfo"][admin_level] == 8 ? "style='display:none;'":"")." >
			<tr height=30 style='display:none;'>
				<td style='padding-bottom:10px;' >
					".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'> ".$_Contents." </td><td align=right ></td></tr></table>")."
				</td>
			</tr>
			</table>

			<div ".(($goods_input_type == "inventory" || $_SESSION["admininfo"][admin_level] == 8 || true) ? "style='display:none;'":"")." id='fee_setting_zone'>
				<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' ".($_SESSION["admininfo"][admin_level] == 8 ? "style='display:none;'":"'display:none;'").">
					<col width='18%'>
					<col width='32%'>
					<col width='18%'>
					<col width='32%'>
					<tr>
						<td class='input_box_title' > 개별수수료 사용 </td>
						<td class='input_box_item' colspan=3  >
							<input type='radio' name='one_commission' id='one_commission_n' value='N' ".($one_commission == 'N' || $one_commission == "" ? "checked":"")." onclick=\"commissionChange(this.form)\"> <label for='one_commission_n'>사용안함 </label>
							<input type='radio' name='one_commission' id='one_commission_y' value='Y' ".($one_commission == 'Y' ? "checked":"")." onclick=\"commissionChange(this.form)\"> <label for='one_commission_y'>사용</label>
							<input type='hidden' name='the_one_commission' id='the_one_commission_n' value='N' > 
						</td>
					</tr>
					
					<tr id='account_info_div' style='display:none;height:70px;'>
						<td class='input_box_title' > 정산방식 </td>
						<td class='input_box_item' colspan=3  >
							<table width='100%' cellpadding=0 cellspacing=0 style='padding-top:3px;padding-bottom:3px;'>
							<tr>
								<td>
									<input type='radio' name='account_type' id='account_type_1' value='1' ".($account_type == '1' || $account_type == "" ? "checked":"")." > <label for='account_type_1'>판매가 정산방식 (판매가에 수수료 적용)</label>
								</td>
							</tr>
							<tr>
								<td>
									<input type='radio' name='account_type' id='account_type_2' value='2' ".($account_type == '2' ? "checked":"")."> <label for='account_type_2'>매입가 정산방식 (공급가로 정산되며, 하단 수수료에 0 이 아닌 숫자를 입력시 그 숫자의 % 만큼 차감후 정산처리됩니다.)</label>
								</td>
							</tr>
							<tr>
								<td>
									<input type='radio' name='account_type' id='account_type_3' value='3' ".($account_type == '3' ? "checked":"")." > <label for='account_type_3'>미정산 (선매입이고 본사에 재고가 있으며, 상품등록을 셀러가 진행시에 사용되며, 정산에서 제외됩니다.)</label>
								</td>
							</tr>
							</table>
						</td>
					</tr>
					<tr id='account_info_div' style='display:none'>
						<td class='input_box_title' >소매 수수료  </td>
						<td class='input_box_item' id='DP' style='padding-top:5px;' >
							<input style='width:40px' type='text' class='textbox numeric' size='30' name='commission' id='commission' value='".$commission."' goods_commission='".$goods_commission."' company_commission='".$company_commission."' style='TEXT-ALIGN:right' > % 단위로 입력하시기 바랍니다. <br>
							<!--% 단위로 입력하시기 바랍니다. * 개별수수료 사용 선택시에만 입력하실 수 있습니다. onkeyup=\"if(this.value.length > 0){this.goods_commission=this.value;}\"--> ".getTransDiscription($page_code,'K')."	<!--commissionChange(this.form) 비율입력시 삭제했음 2014-0-27 이학봉-->
						</td>

						<td class='input_box_title' >도매 수수료  </td>
						<td class='input_box_item' id='DP' style='padding-top:5px;' >
							<input style='width:40px' type='text' class='textbox numeric' size='30' name='wholesale_commission' id='wholesale_commission' value='".$wholesale_commission."' whole_goods_commission='".$goods_wholesale_commission."' whole_company_commission='".$company_wholesale_commission."' style='TEXT-ALIGN:right' > % 단위로 입력하시기 바랍니다. <br>
							<!--% 단위로 입력하시기 바랍니다. * 개별수수료 사용 선택시에만 입력하실 수 있습니다.onkeyup=\"if(this.value.length > 0){this.whole_goods_commission=this.value;}\"--> ".getTransDiscription($page_code,'K')."
						</td>
					</tr>

				</table>
			<div style='clear:both;height:70px;'></div>
			</div>
			
		</div>";
        }
    }
    $_Contents .= "</div>";
}

/*	MD추가 할인 사용안함 2014-05-23 이학봉
if($_SESSION["admininfo"][mall_type] !='F' ){	//MD 추가할인 2014-01-29 이학봉
	if($_SESSION["admininfo"][mall_use_multishop]){

		if($_SESSION["admininfo"][admin_level] ==9 || $_SESSION["admininfo"][admin_level] == 8 ){

		$_Contents = "<img src='../images/dot_org.gif' align=absmiddle style='position:relative;'> <b class=blk> MD 추가 할인 (즉시 할인에 추가할인되며 , (쿠폰보다 우선순위) 정산시 할인율 적용후 정산처리됨) </b>";

$Contents .= "
	<div id='md_rate_zone_table'>

		<table width='100%' cellpadding=0 cellspacing=0 ".($_SESSION["admininfo"][admin_level] == 8 ? "style='display:none;'":"")." >
			<tr height=30>
				<td style='padding-bottom:10px;'>
					".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'> ".$_Contents." </td><td align=right ></td></tr></table>")."
				</td>
			</tr>
		</table>

		<div id='md_rate_zone'>
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box'>
			<col width='18%'>
			<col width='82%'>
			<tr>
				<td class='input_box_title' nowrap> 상품 개별 설정 </td>
				<td class='input_box_item' nowrap>
					<input type='radio' name='md_one_commission' id='md_one_commission_n' value='N' ".($md_one_commission == 'N' || $md_one_commission == "" ? "checked":"").">
					<label for='md_one_commission_n'>사용안함 </label>
					<input type='radio' name='md_one_commission' id='md_one_commission_y' value='Y' ".($md_one_commission == 'Y' ? "checked":"").">
					<label for='md_one_commission_y'>사용</label>
					&nbsp;&nbsp;&nbsp;추가할 이벤트명 <input type='text' class='textbox' name='md_discount_name' value='".$md_discount_name."' id='md_discount_name' style='width:150px;'>
				</td>
			</tr>
			<tr>
				<td class='input_box_title' nowrap> 기간설정 </td>
				<td class='input_box_item' nowrap>
					<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
					<col width=170>
					<col width=*>

					<tr>
						<td>
							<input type='radio' name='md_sell_date_use' value='0' id='md_sell_date_use_0' ".($md_sell_date_use == "0" || $md_sell_date_use == "" ? "checked":"")."> <label for='md_sell_date_use_0'>기간제한 없음</label>
							<input type='radio' name='md_sell_date_use' value='1' id='md_sell_date_use_1' ".($md_sell_date_use == "1" ? "checked":"")."> <label for='md_sell_date_use_1'>기간</label>
						</td>
						<td>
							".search_date('md_sell_priod_sdate','md_sell_priod_edate',$md_sell_priod_sdate,$md_sell_priod_edate)."
						</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class='input_box_title' nowrap> 도매할인율 </td>
				<td class='input_box_item' nowrap>
					<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
					<col width=135>
					<col width=20>
					<col width=135>
					<col width=20>
					<col width=*>
					<tr>
						<td>
							본사 부담
							<input type='text' class='textbox numeric' name='whole_head_company_sale_rate' id='whole_head_company_sale_rate' value='".($whole_head_company_sale_rate !=""?$whole_head_company_sale_rate:'0')."' style='width:50px;' onkeyup=\"change_company_sale_rate('whole');\"> %
						<TD align=center>
							+
						</TD>
						<TD nowrap>
							셀러 부담
							<input type='text' class='textbox numeric' name='whole_seller_company_sale_rate' id='whole_seller_company_sale_rate' value='".($whole_seller_company_sale_rate !=""?$whole_seller_company_sale_rate:'0')."' style='width:50px;' onkeyup=\"change_company_sale_rate('whole');\"> %
						</TD>
						<TD align=center>
							=
						</TD>
						<TD nowrap>
							<input type='text' class='textbox numeric' name='whole_total_company_sale_rate' id='whole_total_company_sale_rate' value='".($whole_head_company_sale_rate + $whole_seller_company_sale_rate)."' style='width:50px;'> %
						</TD>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class='input_box_title' nowrap> 소매할인율 </td>
				<td class='input_box_item' nowrap>
					<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
					<col width=135>
					<col width=20>
					<col width=135>
					<col width=20>
					<col width=*>
					<tr>
						<td>
							본사 부담
							<input type='text' class='textbox numeric' name='head_company_sale_rate' id='retail_head_company_sale_rate' value='".($head_company_sale_rate !=""?$head_company_sale_rate:'0')."' style='width:50px;' onkeyup=\"change_company_sale_rate('retail');\"> %
						<TD align=center>
							+
						</TD>
						<TD nowrap>
							셀러 부담
							<input type='text' class='textbox numeric' name='seller_company_sale_rate' id='retail_seller_company_sale_rate' value='".($seller_company_sale_rate !=""?$seller_company_sale_rate:'0')."' style='width:50px;' onkeyup=\"change_company_sale_rate('retail');\"> %
						</TD>
						<TD align=center>
							=
						</TD>
						<TD nowrap>

							<input type='text' class='textbox numeric' name='total_company_sale_rate' id='retail_total_company_sale_rate' value='".($head_company_sale_rate + $seller_company_sale_rate)."' style='width:50px;'> %
						</TD>
					</tr>
					</table>
				</td>
			</tr>
		</table>
		</div><br>
	</div>";
		}
	}
}
*/


if(false){
    $Contents .="<div id='setGoodsInfoSet' ".($product_type == "99" ? "style='display:block;padding-bottom:20px;'":"style='display:block;'").">";
    $Contents .="<table width='100%' cellpadding=0 cellspacing=0>
				<tr height=30>
				<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:0px;'><b style='vertical-align:middle;font-size:15px;' class=blk> 세트상품등록 </b> <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,2,'productList_2');\"><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a></div>")."</td>
				</tr>
				</table>";

    $Contents .="		<table border=0 cellpadding=0 cellspacing=0 width='100%'>
					<tr bgcolor='#ffffff'>
						<td >
							<div style='width:100%;' id='set_goods_area' >".relationSetProductList($id, "clipart")."</div>
						</td>
					</tr>
					<tr bgcolor='#ffffff'>
						<td >
							<div style='width:100%;' >";
    $Contents .= "<div style='float:left;width:120px;border:1px solid silver;background-color:#efefef;padding:5px;margin:3px;cursor:pointer;;text-align:center;' onclick='SetGoodsDisplay()'>세트상품옵션구성</div>";
    //$Contents .= "<div style='".(($_SESSION["layout_config"]["mall_use_inventory"] == "Y" && $_SESSION["admininfo"][admin_level] == 9) ? "display:block;":"display:none;")."float:left;width:110px;border:1px solid silver;background-color:#efefef;padding:5px;margin:3px;cursor:pointer;text-align:center;' onclick=\"PoPWindow('../inventory/inventory_search.php',800,680,'inventory_search')\">재고정보 생성하기</div>";

    $Contents .="
							</div>
						</td>
					</tr>
					<tr bgcolor='#ffffff'>
						<td style='margin:0px;'>
							<div style='width:100%;margin:10px 0px;' id='set_goods_item_area' >세트상품으로 구성하고자 하는 상품을 선택하신후 세트 상품 옵션 구성을 선택하세요</div>
						</td>
					</tr>
					<tr bgcolor='#ffffff'>
						<td  id='set_goods_item_btn' >
						<table cellpadding=0 cellspacing=0>
							<tr>
								<td><input type=text class='textbox numeric' name='set_goods_cnt' id='set_goods_cnt' value='' title='세트상품수량'></td>
								<td><div style='display:inline-block;width:120px;border:1px solid silver;background-color:#efefef;padding:5px;margin:3px;cursor:pointer;;text-align:center;' onclick='SetMakeOption()'>세트상품옵션으로저장</div><div style='display:inline-block;'><input type=checkbox name='stock_save' id='stock_save'><label for='stock_save'>구성정보 재고 자동생성</label></div></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr bgcolor='#F8F9FA'>
						<td colspan=2 nowrap>
						<div style='clear:both;width:100%;padding:10px;'>
						<span class=small><!--* 검색/추가된 상품 이미지를 드레그앤드롭으로 노출 순서를 좌.우로 조정 할 수 있습니다<br />* 더블클릭 시 상품이 개별 삭제 됩니다.--> ".getTransDiscription($page_code,'M')." </span></div>";

    $Contents .= "
						</td>
					</tr>
				</table><br>";
    $Contents .= "
			</div>";
}

if($goods_input_type != "globalsellertool"){
    if($goods_input_type == "inventory" || $goods_input_type == "globalsellertool"){

        $_Contents = "	<table cellpadding=0 cellspacing=0>
				<tr> 
					<td>
						<b style='cursor:pointer;' onclick=\"newCopyOptions('options_input')\"  class=blk style='font-size:15px;' > 옵션정보  </b>
					</td>
					<td>
						<input type=checkbox name='basic_option_display_yn' id='basic_option_display_yn' onclick=\"$('#basic_option_zone').toggle();\">
					</td>
					<td>
						<label for='basic_option_display_yn'>표시</label>
					</td>
				</tr>
				</table>";
    }else{
        $_Contents = "<!--textarea id='option_info_text' style='width:1000px;height:30px;margin-bottom:10px;'></textarea><br-->
				
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td style='padding:5px 5px 5px 10px;' > 
						<b style='cursor:pointer;' class=blk > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 상품 기본옵션(재고관리와 상관없이 사용할 수 있는 옵션)</b>
					</td>
					<td align=right style='padding-bottom:5px;'><img src='/admin/images/btn_group_open.png' open_src='/admin/images/btn_group_close.png' close_src='/admin/images/btn_group_open.png' alt='Minus' align=absmiddle style='margin-left:5px;' onclick=\"moreDisplay(this, 'div#basic_option_zone', 'div#basic_option_result_zone' );\"></td>
					<!--td align='right'>
						<a href='javascript:sumit_product_option()'>
						<img src='../v3/images/btn/bt_preview.png' border=0 align=absmiddle style='cursor:pointer'>
						</a>
					</td-->
				</tr>
				</table>";
    }

    $sql = "select * from shop_product_options where pid = '".$id."' and option_kind in ('c1','c2','i1','i2','p','s','r') order by option_vieworder asc ";
    $db->query($sql);
    $options = $db->fetchall();

    $Contents .="
				<table width='100%' cellpadding=0 cellspacing=0 id='basic_options_div_tab' ".($id == "" || count($options) == 0 ? "style='display:none;'" : "").">
				<tr height=30>
					<td style='padding-bottom:10px;'>
						".colorCirCleBox("#efefef","100%","<div style=''>".$_Contents."</div>")."
					</td>
				</tr>
				</table>";

    if(count($options) > 0){
        $Contents .= "<div id='basic_option_result_zone' style='padding:20px;border:1px solid silver;margin-bottom:10px;'> <b style='color:#ff2a32'> [GUIDE]</b> 옵션정보 변경을 원하시면 설정하기 버튼을 클릭하세요 </div>";
        $Contents .= "<div ".(($goods_input_type == "inventory" || $goods_input_type == "globalsellertool") ? "style='display:none;'":"")." id='basic_option_zone' class='changeable_area' style='display:none;'>";
    }else{
        $Contents .= "<div id='basic_option_result_zone' style='padding:20px;border:1px solid silver;margin-bottom:10px;display:none;'><b style='color:#ff2a32'> [GUIDE]</b> 설정된 옵션정보가 없습니다. </div>";
        $Contents .= "<div style='display:none;' id='basic_option_zone' class='changeable_area' >";
    }

    $Contents .= "	
				<table cellpadding=0 cellspacing=0 width='100%' >
				<col width='80px'>
				<col width='400px'>
				<col width='*'>
				<tr height=30>
					<td>
						<img src='../v3/images/btn/btn_option_add.png' border=0 align=absmiddle style='cursor:hand;margin:0 0 3px 0;cursor:pointer;' onclick=\"newCopyOptions('options_input')\"  >
					</td>
					<td>
						<input type=checkbox name='option_all_use' valign='middle' id='option_all_use' value='Y' align=absmiddle><label for='option_all_use' >옵션전체사용안함</label> 
						<span class=small><!--(선택 후 저장하시면 옵션정보가 모두 삭제됩니다)--> ".getTransDiscription($page_code,'L')."</span>
					</td>
					<td style='text-align:right;padding-bottom:4px;'>
					</td>
				</tr>
				</table>
				<input type=hidden id='option_name' inputid='option_name' value=''><input type=hidden id='options_option_use'  value='1'>";


//if($db->total && $id!=""){	//새로 추가시 노출이 안되어서 주석처리했음 2014-05-20 이학봉

    for($i=0;($i < count($options)  || $i < 1);$i++){
        $Contents .= "
				<table width='100%' cellpadding=0 cellspacing=1 bgcolor=silver id='options_input' class='input_table_box options_input' idx=".$i." style='margin-bottom:10px' >
				<col width='4%'>
				<col width='17%'>
				<col width='*'>
				<col width='6%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'> 
				<tr height=25 bgcolor='#ffffff' align=center>
					<td bgcolor=\"#efefef\" class=small nowrap>
					사용
					</td>
					<td bgcolor=\"#efefef\" class=small nowrap>
					 옵션명
					</td>
					<td bgcolor=\"#efefef\" class=small nowrap>
					 옵션종류
					</td>
					<td bgcolor=\"#efefef\" class=small> 품절여부</td>
					<td bgcolor=\"#efefef\" class=small> 옵션구분 *</td>
					<td bgcolor=\"#efefef\" class=small> 추가가격 *</td>
					<td bgcolor=\"#efefef\" class=small> 옵션코드</td>
					<td bgcolor=\"#efefef\" class=small> 기타  </td>
				</tr>
				<tr bgcolor='#ffffff' align=center>
					<td valign=top style='padding-top:6px;'>
						<input type=hidden name='options[".$i."][opn_ix]' id='option_opn_ix' value='".$options[$i][opn_ix]."'>
						<input type=checkbox name='options[".$i."][option_use]' id='options_option_use' value='1' ".(($options[$i][option_use] == 1) ? "checked":"")." style='margin:0 0 0 0' align=absmiddle>
					</td>
					<td valign=top style='padding:4px 0px;'>";

        $Contents .= "<span id='".$i."'>
						<input type=text class='textbox' name='options[".$i."][option_name]' id='option_name' inputid='option_name' style='width:115px;vertical-align:middle' value='".$options[$i][option_name]."' placeholder='기본 옵션명'/>
						</span>
							".($i == 0 ? "<!-- 옵션 삭제 -->":"<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;margin:5px;' ondblclick=\"if($('.options_input').length > 1){ \$('.options_input[idx=".$i."]').remove();showMessage('options_input_status_area_".$i."','해당 옵션 구분정보가 삭제 되었습니다.');}else{alert(language_data['goods_input.php']['G'][language]);}\" title='더블클릭시 해당 테이블이 삭제 됩니다.'>")." ";

        /*
        if(count($language_list) > 0 && $globalInfo['global_use']=='Y' && $globalInfo['global_oname_type']=='D'){

            $global_oinfo = json_decode(trim($options[$i][global_oinfo]),true);

            if(count($global_oinfo) > 0){
                foreach($global_oinfo as $colum => $li){
                    foreach($li as $ln => $val){
                        $global_oinfo[$colum][$ln] = urldecode($val);
                    }
                }
            }

            foreach($language_list as $key => $li){

            $Contents .= "<input type=text class='textbox global_option' name='options[".$i."][global_oinfo][option_name][".$li['language_code']."]' style='width:115px;vertical-align:middle;margin-top:3px;' value='".$global_oinfo['option_name'][$li['language_code']]."' placeholder='".$li['language_name']."' title='".$li['language_name']."'>";
            }
        }
        */
        $Contents .= "
					</td>
					<td style='padding:4px 4px 4px 4px;' nowrap>
						<select name='options[".$i."][option_type]' class='option_type'  style='font-size:12px;'>
							<option value='1' ".($options[$i][option_type] == "1" ? "selected":"").">수량</option>
							<!--option value='2' ".($options[$i][option_type] == "2" ? "selected":"").">상품</option-->
						</select>
						<select name='options[".$i."][option_kind]' id='option_kind_0' class='option_kind'  style='font-size:12px;'>
							<option value='c1' ".($options[$i][option_kind] == "c1" ? "selected":"").">조합옵션(필수)</option>
							<option value='i1' ".($options[$i][option_kind] == "i1" ? "selected":"").">독립옵션(필수)</option>
						</select>
						
						<br/>
						<img src='../v3/images/btn/bt_addoptionline.png'  border=0 align=absmiddle style='cursor:pointer;margin-top:5px;' onclick=\"copyOptions('options_item_input_".$i."');showMessage('options_input_status_area_".$i."','해당옵션 구분정보가 추가 되었습니다.');\" />

					 

					</td>
					<td colspan=5 id='options_basic_item_input_table' style='padding:5px;'>
					<input type=hidden id='options_item_option_div_".$i."' inputid='options_item_option_div_".$i."' value=''>
					<input type=hidden id='options_item_option_code_".$i."' value=''>";

        $sql = "select * from shop_product_options_detail where pid = '".$id."' and opn_ix = '".$options[$i][opn_ix]."' order by set_group_seq asc,id asc ";
        $db->query($sql);

        //if($db->total){
        for($j=0;($j < $db->total || $j < 1);$j++){
            $db->fetch($j);
            $opnd_ix = $db->dt[id];
            $Contents .= "<table width=100% border=0 id='options_item_input_".$i."' class='options_item_input_".$i."' idx=".$i." detail_idx=".$j." cellspacing=4 cellpadding=0>
						<colgroup>
						<col width='12%' />
						<col width='22%' />
						<col width='22%' />
						<col width='22%' />
						<col width='22%' />
						</colgroup>
						<tbody>
						<tr>
							<td align=center>
								<input type=checkbox class='textbox' name='options[".$i."][details][".$j."][option_soldout]' id='options_item_option_soldout_0' size=28 style='vertical-align:middle;border:1px;' value='1' ".($db->dt[option_soldout] == "1" ? "checked":"")." />
							</td>
							<td>
								<table border='0' cellpadding='0' cellspacing='0' width='100%'>
									<tr>";

            if(file_exists($_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"][mall_data_root]."/images/product".$uploaddir."/options/options_detail_".$opnd_ix."_s.gif")){
                $Contents .= "<td>
										<img src='".$_SESSION["admin_config"][mall_data_root]."/images/product".$uploaddir."/options/options_detail_".$opnd_ix."_s.gif' width=30 height=30 />
									</td>";
            }
            $Contents .= "
										<td>
											<input type=hidden  id='options_item_opd_ix_".$j."' name='options[".$i."][details][".$j."][opd_ix]' value='".$db->dt[id]."' />
											<input type=text class='textbox options_detail_option_div_".$i."'  id='options_item_option_div_".$j."' name='options[".$i."][details][".$j."][option_div]' inputid='options_item_option_div_".$i."' style='width:80%;vertical-align:middle' value='".$db->dt[option_div]."' placeholder='기본 옵션구분' />";

            /*
            if(count($language_list) > 0 && $globalInfo['global_use']=='Y' && $globalInfo['global_oname_type']=='D'){

                $global_odinfo = json_decode(trim($db->dt[global_odinfo]),true);

                if(count($global_odinfo) > 0){
                    foreach($global_odinfo as $colum => $li){
                        foreach($li as $ln => $val){
                            $global_odinfo[$colum][$ln] = urldecode($val);
                        }
                    }
                }

                foreach($language_list as $key => $li){

                $Contents .= "
                    <input type=text class='textbox global_option' name='options[".$i."][details][".$j."][global_odinfo][option_div][".$li['language_code']."]' style='width:80%;vertical-align:middle;margin-top:3px;' value='".$global_odinfo['option_div'][$li['language_code']]."' placeholder='".$li['language_name']."' title='".$li['language_name']."'>";
                }
            }
            */

            $Contents .= "
										</td>
									</tr>
								</table>
							</td>
							<td>
								<input type=text class='textbox numeric' name='options[".$i."][details][".$j."][price]' id='options_item_option_price_".$j."' style='width:90%' value='".$db->dt[option_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' />
							</td>
							<td>
								<input type=text class='textbox' name='options[".$i."][details][".$j."][code]' id='options_item_option_code_".$j."' style='width:90%' value='".$db->dt[option_code]."' >
							</td>";
            $Contents .= "	<td>
								<input type=text class='textbox' name='options[".$i."][details][".$j."][etc1]' id='options_item_option_etc1_".$j."' style='width:70%' value='".$db->dt[option_etc1]."'>
								<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;margin:5px;'  title='더블클릭시 해당 라인이 삭제 됩니다.....' ondblclick=\"if($('table#options_item_input_".$i."').length > 1){ \$(this).parent().parent().parent().parent().remove();}else{if($('.options_input').length > 1){ $(this).parent().parent().parent().parent().parent().parent().parent().parent().remove();}else{ $(this).parent().parent().parent().parent().parent().parent().find('input[type=text]').val(''); } }\" >
							</td>";

            $Contents .= "
						</tr>
					</tbody>
					</table>";
        }
        //}

        $Contents .= "
					</td>
				</tr>
				<tr>
					<td colspan=8 style='background-color:#ffffff;'>
					<table width=100%>
					<col width='100px'>
					<col width='65px'>
					<col width='100px'>
					<col width='*'>
					<tr>";

        $Contents .= "
						<td>".getOptionTmp($i)." </td>
						<td>
							<img src='../images/".$_SESSION["admininfo"]["language"]."/btn_favorite_option_use.gif' id='btn_favorite_option_use' onclick=\"SetOptionTmp($('#favorite_option_".$i."'));\" style='cursor:pointer;'>
						</td>
						<td>
							<a href='./goods_options_tmp.php' target=_blank>
							<img src='../v3/images/btn/btn_favorite_option_add2.png' id='btn_favorite_option_use'   style='cursor:pointer;'>
							</a>
						</td>";

        $Contents .= "
						<td>
							<div style='height:30px;text-align:right;color:gray;line-height:220%;' id='options_input_status_area_".$i."'></div>
						</td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
";
    }


    $Contents .="
        <div style='clear:both;height:70px;'></div>
		</div>
		<div style='line-height:130%;padding:10px 0px 20px 0px;display:none;' >
		재고관리가 필요 없는 상품일 경우 옵션 추가를 이용하여 아래 예와 같이 옵션을 분리 적용 하실 수 있습니다.<br>
		예) 옵션1 ? 옵션명 : 색상 / 옵션구분 : RED, BLUE<br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;옵션2 ? 옵션명 : 사이즈 / 옵션구분 : 95size, 100size, 105size<br>
		</div>";
}
if($product_type == "99" || $goods_input_type == "social"){
    $sql = "select * from shop_product_options where pid = '".$id."' and pid != '0' and option_kind = 'x' order by opn_ix ";
    //echo $sql;
    $db->query($sql);
    if($db->total) {
        $db->fetch();
        $box_options = $db->dt;
        $use_option_type = "box_option";
    }

    $sql = "select * from shop_product_options where pid = '".$id."' and pid != '0' and option_kind in ('s2','x2') order by opn_ix ";
    //echo $sql;
    $db->query($sql);
    if($db->total) {
        $db->fetch();
        $set2_options = $db->dt;
        $use_option_type = "set2_option";
    }

    if(empty($use_option_type)) {//직배송일 경우 옵션이 b 이기에 해당 옵션을 못 불러오기에 추가 kbk 13/08/31
        $sql = "select * from shop_product_options where pid = '".$id."' and pid != '0' and option_kind in ('b') order by opn_ix ";
        //echo $sql;
        $db->query($sql);
        if($db->total) {
            $db->fetch();
            $stock_options = $db->dt;
            $use_option_type = "non_set_option";
        }
    }
    if(empty($use_option_type)) {//코디옵션일경우
        $sql = "select * from shop_product_options where pid = '".$id."' and pid != '0' and option_kind in ('c') order by opn_ix ";
        //echo $sql;
        $db->query($sql);
        if($db->total) {
            $db->fetch();
            $codi_options = $db->dt;
            $use_option_type = "codi_option";
        }
    }
}else{
    $sql = "select * from shop_product_options where pid = '".$id."' and pid != '0' and option_kind = 'b' order by opn_ix ";
    //echo $sql;
    $db->query($sql);
    if($db->total) {
        $db->fetch();
        $stock_options = $db->dt;
        $use_option_type = "non_set_option";

        $sql = "select * from shop_product_options_global where opn_ix = '".$stock_options['opn_ix']."'";
        $db->query($sql);
        $db->fetch();
        foreach($language_list as $l){
            ${$l['language_code'].'_stock_options'} = $db->dt;
        }
    }
}

if(!$use_option_type){
    if($product_type == "99"){
        $use_option_type = "codi_option";
    }else if($product_type == "77"){
        $use_option_type = "codi_option";
    }else{
        $use_option_type = "non_set_option";
    }
}

if($_SESSION["admininfo"]["admin_level"] > 8){
    $Contents .="	
				<table width='100%' cellpadding=0 cellspacing=0 id='options_div_tab' ".($stock_use_yn == "N" ? "style='display:none;'" : "").">
				<tr height=30>
					<td style='padding-bottom:15px;'>
					<div class='tab'>
					<table class='s_org_tab' width='100%'>
					<tr>
						<td class='tab'>
							<table id='option_tab_01'  ".($use_option_type == "non_set_option" ? "class='changeable_area non_set_options on' ":"class='changeable_area non_set_options'")." ".($product_type == "99" ? "style='display:none;' ":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showOptionTabContents('stock_option_zone','option_tab_01');\">가격+재고관리 옵션<a name='priceStock'></a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='option_tab_02'  ".($use_option_type == "box_option" ? "class='changeable_area set_options on' ":" class='changeable_area set_options' ")." ".($product_type != "99" ? "style='display:none;' ":"").">
							<tr style='display:none;'>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showOptionTabContents('box_option_zone','option_tab_02');\">초이스 박스옵션</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='option_tab_03'  ".($use_option_type == "set_option"  ? "class='changeable_area set_options on' ":" class='changeable_area set_options'")." ".($product_type != "99" ? "style='display:none;' ":"style='display:none;'").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showOptionTabContents('set_option_zone','option_tab_03');\">세트옵션</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='option_tab_05'  ".($use_option_type == "codi_option" ? "class='changeable_area set_options on' ":" class='changeable_area set_options' ")." ".(($product_type == "99" ) ? "":"'display:none;'").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showOptionTabContents('codi_option_zone','option_tab_05');\">코디상품옵션</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						
						<td class='btn'>
							<input type='hidden' name='use_option_type' id='use_option_type' value='".$use_option_type."'>
						</td>
						<td align='right'>
							<!--a href='javascript:sumit_product_option()'><img src='../v3/images/btn/bt_preview.png' border=0 align=absmiddle style='cursor:pointer'></a-->
						</td>
					</tr>
					</table>
					</div>
					</td>
				</tr>
				</table>";
}

$Contents .= "<div id='stock_option_zone' ".($use_option_type == "non_set_option" && $stock_use_yn != "N" ? "":"style='display:none' ")." class='changeable_area non_set_options'>
				<table width='100%' cellpadding=0 cellspacing=0 >
				<tr height=30>
					<td style='padding-bottom:10px;'>
						".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><b style='cursor:pointer;' class=blk> <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 가격+재고 관리 옵션정보_(한 상품에 여러 재고를 등록 고객이 선택하여 구매할 수 있는 상품(재고)옵션) </b></div>")."
					</td>
				</tr>
				</table>";

$Contents .= "
				<table width='100%' border=0 cellpadding=0 cellspacing=1 bgcolor=silver border='0' id='stock_options_table' class='input_table_box stock_options_table' idx=0 style='margin-bottom:10px;'>
				<col width='15%'>
				<col width='7%'>";
//if($DISPLAY_WHOSALE_INFO){
if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
    $Contents .= "
				<col width='7%'>";
}

if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
    $Contents .= "
				<col width='14%'>";
}
$Contents .= "
				<col width='5%'>
				<col width='11%'>
				<col width='6%'>
				<col width='11%'>
				<col width='5%'>
				<col width='4%'>
				<tr bgcolor='#ffffff' height=34>
					<td  ".($_SESSION["admin_config"]["selling_type"] == "WR"  ? "colspan=10":"colspan=9").">
						<table border=0 width=100% >
							<col width='5%'>
							<col width='1%'>
							<col width='19%'>
							<col width='1%'>
							<col width='1%'>
							<col width='13%'>
							<col width='30%'>
							<col width='*'>
							<tr>
								<td style='padding-left:10px;'>
									<input type=hidden name='stock_options[0][opn_ix]' id='option_opn_ix' value='".$stock_options[opn_ix]."' >
									<input type=checkbox name='stock_options[0][option_use]' id='add_option_use '  value='1' title='사용'  ".($stock_options[option_use] == "1" ? "checked":"").">
								</td>
								<td>
									<input type='hidden' name='stock_options[0][option_kind]' value='b' id='option_kind' class='option_kind'>
								</td>
								<td >
								<table width=100%>
									<tr>
										<td><b>옵션명 : </b></td>
										<td>
										<input type=text class='textbox point_color stock_title' name='stock_options[0][option_name]' id='add_option_name' inputid='option_name' style='margin:3px;width:115px;vertical-align:middle' value='".$stock_options[option_name]."' title='옵션명' placeholder='한국어'><!-- 옵션 삭제 -->";

if(count($language_list) > 0){
    foreach($language_list as $key => $li){
        $_name = $li['language_code'].'_stock_options';
        $_value = $$_name;

        $Contents .= "<br/><input type=text class='textbox point_color stock_title' name='stock_options[0][".$li['language_code']."_option_name]' inputid='".$li['language_code']."_option_name' style='margin:3px;width:115px;vertical-align:middle' value='".$_value[option_name]."' title='옵션명' placeholder='".$li[language_name]."'>";
    }
}

$Contents .= "
										</td>
									</tr>
								</table>
								</td>
								<td>
									<!--img src='../images/".$_SESSION["admininfo"]["language"]."/btn_option_detail_add.gif' border=0 align=absmiddle style='cursor:pointer;'  id='btn_option_detail_add' onclick=\"AddOptionsCopyRow('stock_options_table','stock_options');\" /-->
									
 
								</td>
								<td>
									<div class='stockinfo_loade' style='display:inline;cursor:pointer;text-align:center;' id='btn_inventory_search' onclick=\"ShowModalWindow('../inventory/inventory_search.php?type=stock_options&seq=0',1000,690,'inventory_search')\"><img src='../images/korean/stock.gif' alt='재고정보불러오기' title='재고정보불러오기' /></div>
								</td>
								<td>
									<!--<input type='radio' class='options_price_stock_type' name='stock_options[0][option_type]' id='option_type_9' value='9' ".($stock_options[option_type] == '9' || $stock_options[option_type] == ''?'checked':'')."> <label for='option_type_9'>기타</label>-->
									<div style='display:none;'><input type='radio' class='options_price_stock_type' name='stock_options[0][option_type]' id='option_type_9' value='9' ".($stock_options[option_type] == '9' || $stock_options[option_type] == ''?'checked':'')."> <label for='option_type_9'>기타</label></div> 
									<!--<input type='radio' class='options_price_stock_type' name='stock_options[0][option_type]' id='option_type_o' value='o' ".($stock_options[option_type] == '9' || $stock_options[option_type] == 'o'?'checked':'')."> <label for='option_type_o'>A+B</label>-->
									<input type='radio' class='options_price_stock_type' name='stock_options[0][option_type]' id='option_type_c' value='c' ".($stock_options[option_type] == 'c' || $stock_options[option_type] == '' ? 'checked':'')."> <label for='option_type_c'>버튼옵션</label>
									<input type='radio' class='options_price_stock_type' name='stock_options[0][option_type]' id='option_type_s' value='s' ".($stock_options[option_type] == 's'?'checked':'')."> <label for='option_type_s'>일반옵션</label>
									
									<script type='text/javascript'>
										$('.options_price_stock_type').click(function(){
											if( $(this).val() == 'o' ){
												$('.options_price_stock_option_div_area').hide();
												$('.options_price_stock_option_color_area').show();
												$('.options_price_stock_option_size_area').show();
											} else if ( $(this).val() == 'c' ) {
												$('.options_price_stock_option_div_area').hide();
												$('.options_price_stock_option_color_area').show();
												$('.options_price_stock_option_size_area').hide();
											} else if ( $(this).val() == 's' ) {
												$('.options_price_stock_option_div_area').hide();
												$('.options_price_stock_option_color_area').hide();
												$('.options_price_stock_option_size_area').show();
											} else { // 기타(기본)
												//$('.options_price_stock_option_div_area').show();
												//$('.options_price_stock_option_color_area').hide();
												//$('.options_price_stock_option_size_area').hide();	
											    $('.options_price_stock_option_div_area').hide();
												$('.options_price_stock_option_color_area').show();
												$('.options_price_stock_option_size_area').hide();
											}
										})
									</script>
								</td>
								<td>
                                    <button type='button' onclick=\"change_price(this, 'option')\">옵션가동일</button>
                                    <button type='button' onclick=\"change_price(this, 'zero')\">0원 변경</button>
                                    <button type='button' onclick=\"change_price(this, 'currency')\" exchange='$exchange_info' >환율 적용(".$exchange_info.")</button>
							    </td>
								<td style='text-align:right;padding-right:5px;'>
									<img src='../v3/images/btn/bt_addoptionline.png' border=0 align=absmiddle style='cursor:pointer;'  id='btn_option_detail_add' onclick=\"AddOptionsCopyRow('stock_options_table','stock_options');\" />
									<!--input type='checkbox' name='stock_options[0][text_option_use]' id='text_option_use' value='1' ".($stock_options[text_option_use] == '1'?'checked':'')."> <label for='text_option_use'>텍스트 옵션</label>
									<input type='checkbox' name='stock_options[0][file_option_use]' id='file_option_use' value='1' ".($stock_options[file_option_use] == '1'?'checked':'')."> <label for='file_option_use'>파일 옵션</label-->
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr height=34 id='box_option_info' style='display:none;'>
					<td colspan=10 class='point_color' style='padding-left:10px;'>
					<table width=100%>
						<col width='130px'>
						<col width='115px'>
						<col width='*'>
						<tr>
						<td>총 1박스 합계 수량 </td>
						<td> <input type=text class='textbox number' name='box_total' id='box_total'  style='width:68px' value='".$stock_options[box_total]."' onkeydown='onlyEditableNumber(this)' onkeyup='onlyEditableNumber(this)'> 개 </td>
						<td> (등록된 상품중에 1박스 수량 만큼 구매를 하게 됩니다.)</td>
						</tr>
					</table> 
					</td>
				</tr>
				<tr height=55 bgcolor='#ffffff' align=center>
					<td bgcolor=\"#efefef\" class=small> 옵션구분 *</td>
					<td bgcolor=\"#efefef\" class=small> 공급가격 <br><a href=\"javascript:price_check('coprice','stock_options_table');\"><img src='../v3/images/btn/bt_basicprice.png' style='margin-top:5px;' align=absmiddle></a></td>";
//if($DISPLAY_WHOSALE_INFO){
if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
    $Contents .= "	
					<td bgcolor=\"#efefef\" class=small> 도매가격 <br><a href=\"javascript:price_check('wholesale_listprice','stock_options_table');\"><img src='../v3/images/btn/bt_basicprice.png' align=absmiddle ></a></td>";
}

//코웰용($_SESSION["admininfo"][admin_level] == 9 ? "<span class=small onclick=\"copyPrice(document.product_input, document.product_input.sellprice.value, 6);calcurate_maginrate(document.product_input)\" style='cursor:pointer;color:red'> 복사→</span>":"")
if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
    $Contents .= "
					<td bgcolor=\"#efefef\" class=small> 소매가격 <br><a href=\"javascript:price_check('retail_listprice','stock_options_table');\"><img src='../v3/images/btn/bt_basicprice.png' align=absmiddle></a>				
					</td>";
}
$Contents .= "
					<td bgcolor=\"#efefef\" class=small> 품절여부 <input type='checkbox' class='devAllSoldOut' onclick=\"allSoldOut(this)\" itemArea='$i' ></td>
					<td bgcolor=\"#efefef\" class=small> 판매진행재고/실재고<br/><input type='text' class='textbox' size='3' id='input_stock' style='margin:0px 2px;'  /><a href=\"javascript:input_stock('stock_options_table');\"><img src='../v3/images/btn/bt_batch.png' align=absmiddle></a></td>
					<td bgcolor=\"#efefef\" class=small> 안전재고 </td>
					<td bgcolor=\"#efefef\" class=small> 옵션(품목)코드  </td>
					<td bgcolor=\"#efefef\" class=small> 바코드 <br/>기타 </td>
					<td bgcolor=\"#efefef\" class=small> 관리  </td>
				</tr>";
$sql = "select 
          pod.* 
        from 
          shop_product_options_detail pod 
        left join 
          inventory_goods ig on pod.option_gid = ig.gid
        left join
          shop_product_options_sort_by_value sb on ig.size = sb.value
        where
          pod.pid = '".$stock_options["pid"]."' 
        and 
          pod.pid != '0'  
        and 
          pod.opn_ix = '".$stock_options[opn_ix]."' 
        order by  
           sb.view_order asc ,set_group_seq asc
          ";
//echo $sql;
//$sql = "select * from shop_product_options_detail where pid = '".$stock_options["pid"]."' and pid != '0'  and opn_ix = '".$stock_options[opn_ix]."' order by  set_group_seq asc ";
$db->query($sql);

//if($db->total){
$stock_options_detail = $db->fetchall();

$sql = "select 
          pod.* 
        from 
          shop_product_options_detail_global pod 
        left join 
          inventory_goods ig on pod.option_gid = ig.gid
        left join
          shop_product_options_sort_by_value sb on ig.size = sb.value
        where
          pod.pid = '".$stock_options["pid"]."' 
        and 
          pod.pid != '0'  
        and 
          pod.opn_ix = '".$stock_options[opn_ix]."' 
        order by  
            sb.view_order asc ,set_group_seq asc
          ";
//$sql = "select * from shop_product_options_detail_global where pid = '".$stock_options["pid"]."' and pid != '0'  and opn_ix = '".$stock_options[opn_ix]."' order by  set_group_seq asc ";
$db->query($sql);
foreach($language_list as $l){
    ${$l['language_code'].'_stock_options_detail'} = $db->fetchall();
}

for($i=0;($i < count($stock_options_detail) || $i < 1);$i++){

    $Contents .= "	<tr height=30 bgcolor='#ffffff'  align='center' depth=1 item=1 class='items'>
					<td style='padding:10px 0px;'> 
					<input type='hidden' name='option_length' id ='option_length' value='".$i."'>
					<div style='display:none;'>".$stock_options_detail[$i][id]."</b> &nbsp;<input type=text class='textbox point_color add_option_div options_price_stock_option_div' name='stock_options[0][details][".$i."][option_div]' id='add_option_div' style='width:80%;vertical-align:middle' value='".$stock_options_detail[$i][option_div]."'></div>
					<div class='options_price_stock_option_color_area' ".($stock_options[option_type] == 'o' || $stock_options[option_type] == 'c' || $stock_options[option_type] == '' ? "" : "style='display:none;'").">
						A : <input type=text class='textbox point_color add_option_color options_price_stock_option_color' name='stock_options[0][details][".$i."][option_color]' id='add_option_color' style='margin-top:5px;width:62%;vertical-align:middle' value='".$stock_options_detail[$i][option_color]."'>";

    if(count($language_list) > 0){
        foreach($language_list as $key => $li){
            $_name = $li['language_code'].'_stock_options_detail';
            $_value = $$_name;

            $Contents .= "<br/>&nbsp&nbsp&nbsp&nbsp&nbsp<input type=text class='textbox point_color add_option_color options_price_stock_option_color' name='stock_options[0][details][".$i."][".$li['language_code']."_option_color]' id='".$li['language_code']."_add_option_color' style='margin-top:5px;width:62%;vertical-align:middle' value='".$_value[$i][option_color]."' placeholder='".$li[language_name]."'>";
        }
    }

    $Contents .= "
					</div>

					<div class='options_price_stock_option_size_area' ".($stock_options[option_type] == 'o' || $stock_options[option_type] == 's' ? "" : "style='display:none;'").">
						B : <input type=text class='textbox point_color add_option_size options_price_stock_option_size' name='stock_options[0][details][".$i."][option_size]' id='add_option_size' style='margin-top:5px;width:65%;vertical-align:middle' value='".$stock_options_detail[$i][option_size]."'>";

    if(count($language_list) > 0){
        foreach($language_list as $key => $li){
            $_name = $li['language_code'].'_stock_options_detail';
            $_value = $$_name;

            $Contents .= "<br/>&nbsp&nbsp&nbsp&nbsp&nbsp<input type=text class='textbox point_color add_option_size options_price_stock_option_size' name='stock_options[0][details][".$i."][".$li['language_code']."_option_size]' id='".$li['language_code']."_add_option_size' style='margin-top:5px;width:65%;vertical-align:middle' value='".$_value[$i][option_size]."' placeholder='".$li[language_name]."'>";
        }
    }

    $Contents .= "
					</div>
					";

    /*
    if(count($language_list) > 0 && $globalInfo['global_use']=='Y' && $globalInfo['global_oname_type']=='D'){

        $global_odinfo = json_decode(trim($stock_options_detail[$i][global_odinfo]),true);

        if(count($global_odinfo) > 0){
            foreach($global_odinfo as $colum => $li){
                foreach($li as $ln => $val){
                    $global_odinfo[$colum][$ln] = urldecode($val);
                }
            }
        }

        foreach($language_list as $key => $li){

        $Contents .= "
            <input type=text class='textbox global_option' name='stock_options[0][details][".$i."][global_odinfo][option_div][".$li['language_code']."][]' style='width:80%;vertical-align:middle;margin-top:3px;' value='".$global_odinfo['option_div'][$li['language_code']]."' placeholder='".$li['language_name']."' title='".$li['language_name']."'>";
        }
    }
    */

    $Contents .= "
					</td>
					<td>
					    <input type=text class='textbox numeric stock_readonly' name='stock_options[0][details][".$i."][coprice]'  id='add_option_coprice' style='width:80%' value='".$stock_options_detail[$i][option_coprice]."'>";

    if(count($language_list) > 0){
        foreach($language_list as $key => $li){
            $_name = $li['language_code'].'_stock_options_detail';
            $_value = $$_name;

            $Contents .= "<br/><input type=text class='textbox numeric stock_readonly' name='stock_options[0][details][".$i."][".$li['language_code']."_coprice]'  id='".$li['language_code']."_add_option_coprice' style='width:80%;margin-top:3px;' value='".$_value[$i][option_coprice]."' placeholder='".$li[language_name]."'>";
        }
    }
    $Contents .= "	
					</td>";
    //if($DISPLAY_WHOSALE_INFO){
    if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "	
					<td>
						<input type=text class='textbox number' name='stock_options[0][details][".$i."][wholesale_listprice]' id='add_option_wholesale_listprice' style='width:80%' value='".$stock_options_detail[$i][option_wholesale_listprice]."' onkeydown='onlyEditableNumber(this)' title='도매판매가' onkeyup='onlyEditableNumber(this)' ><br>
						<input type=text class='textbox number' name='stock_options[0][details][".$i."][wholesale_price]' id='add_option_wholesale_price' style='margin:3px;width:80%' value='".$stock_options_detail[$i][option_wholesale_price]."' onkeydown='onlyEditableNumber(this)' title='도매할인가'  onkeyup='onlyEditableNumber(this)' >
					</td>";
    }
    if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "	
					<td style='padding:5px;'>
						<input type=text class='textbox numeric' name='stock_options[0][details][".$i."][listprice]' title='판매가' id='add_option_listprice' style='width:42%' value='".$stock_options_detail[$i][option_listprice]."' placeholder='판매가'>
						<input type=text class='textbox numeric' name='stock_options[0][details][".$i."][sellprice]' title='할인가' id='add_option_sellprice' style='margin-top:3px;width:42%' value='".$stock_options_detail[$i][option_price]."' placeholder='할인가'>";

        if(count($language_list) > 0){
            foreach($language_list as $key => $li){
                $_name = $li['language_code'].'_stock_options_detail';
                $_value = $$_name;

                $Contents .= "<br/>
                    <input type=text class='textbox numeric' name='stock_options[0][details][".$i."][".$li['language_code']."_listprice]' title='판매가' id='".$li['language_code']."_add_option_listprice' style='margin-top:3px;width:42%' value='".$_value[$i][option_listprice]."' placeholder='".$li[language_name]." 판매가'>
						<input type=text class='textbox numeric' name='stock_options[0][details][".$i."][".$li['language_code']."_sellprice]' title='할인가' id='".$li['language_code']."_add_option_sellprice' style='margin-top:3px;width:42%' value='".$_value[$i][option_price]."' placeholder='".$li[language_name]." 할인가'>";
            }
        }
    }
    $Contents .= "	
					<td>
						<input type=checkbox name='stock_options[0][details][".$i."][soldout]' id='add_option_soldout' class='option_soldout'  value='1' ".($stock_options_detail[$i][option_soldout] == "1" ? "checked":"").">
					</td>
					<td>
						<input type=text class='textbox number' name='stock_options[0][details][".$i."][sell_ing_cnt]' id='add_option_sell_ing_cnt' style='width:30px;margin:0px 3px;'  value='".($mode!="copy"?$stock_options_detail[$i][option_sell_ing_cnt]:"0")."' title='판매진행중 재고' readonly>
						<input type=text class='textbox number stock_readonly' name='stock_options[0][details][".$i."][stock]' id='add_option_stock' style='width:50px;".($_SESSION["layout_config"]["mall_use_inventory"] == "Y" ? "background:#efefef;":"")."' value='".$stock_options_detail[$i][option_stock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' ></td>
					<td>
						<input type=text class='textbox number stock_readonly' name='stock_options[0][details][".$i."][safestock]' id='add_option_safestock' style='width:70%' value='".$stock_options_detail[$i][option_safestock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
					<td>
						<input type=text class='textbox stock_readonly' name='stock_options[0][details][".$i."][code]' id='add_option_code' style='width:30%' value='".$stock_options_detail[$i][option_code]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'>
						<input type=text class='textbox stock_readonly' name='stock_options[0][details][".$i."][gid]' id='add_option_gid' style='width:50%' value='".$stock_options_detail[$i][option_gid]."'>
					</td>
					<td>
						<input type=text class='textbox stock_readonly' name='stock_options[0][details][".$i."][barcode]' id='add_option_barcode' style='width:80%' value='".$stock_options_detail[$i][option_barcode]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'>
						<input type=text class='textbox' name='stock_options[0][details][".$i."][etc]' id='add_option_etc' style='width:80%;margin-top:3px;' value='".$stock_options_detail[$i][option_etc1]."' readonly>
					</td>
					<td>
						<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;margin:0px 0px 4px 3px' ondblclick=\"if($('#stock_options_table').find('tr[depth^=1]').length > 1){\$(this).parent().parent().remove();}else{ }\" title='더블클릭시 해당 라인이 삭제 됩니다.'></td>
				</tr>";
}
//}
$Contents .= "
				</table>
			</div>";

$sql = "select * from shop_product_options where pid = '".$id."' and pid != '0' and option_kind = 'x' order by opn_ix ";
$db->query($sql);
if($db->total) {
    $db->fetch();
    $box_options = $db->dt;
}
$Contents .= "<div id='box_option_zone' ".($use_option_type == "box_option" ?  "" : "style='display:none' ")." class='changeable_area set_options'>
				<table width='100%' cellpadding=0 cellspacing=0>
				<tr height=30>
					<td style='padding-bottom:10px;'>
						".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><b style='cursor:pointer;' class=blk><img src='../images/dot_org.gif' align=absmiddle style='vertical-align:middle;'> 박스 옵션정보 </b></div>")."
					</td>
				</tr>
				</table>

				<table width='100%' cellpadding=0 cellspacing=1 bgcolor=silver border='0' id='box_options_table' class='input_table_box box_options_table' idx=0 style='margin-bottom:10px;' >
				<col width='20%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='5%'>
				<col width='11%'>
				<col width='6%'>
				<col width='11%'>
				<col width='11%'>
				<col width='4%'>
				<tr bgcolor='#ffffff' height=34>
					<td ".($_SESSION["admin_config"]["selling_type"] == "WR" ? "colspan=10":"colspan=9").">
						<table>
							<tr>
								<td style='padding-left:10px;'>
									<input type=hidden name='box_options[0][option_type]' value='9' >
									<input type=hidden name='box_options[0][opn_ix]' id='option_opn_ix' value='".$box_options[opn_ix]."' >
									<input type=checkbox name='box_options[0][option_use]' id='add_option_use'  value='1' title='사용' ".($box_options[option_use] == "1" ? "checked":"").">
								</td>
								<td>
									<select name='box_options[0][option_kind]' id='option_kind' class='option_kind' style='font-size:12px;' onchange=\"if(this.value == 'x'){ $('#box_option_info').show();}else{ $('#box_option_info').hide();}\" validation=true title='옵션종류'>
										<option value=''>옵션종류</option>
										<option value='x' selected>박스 옵션</option>
									</select>
								</td>
								<td >
									<b>옵션명 : </b><input type=text class='textbox point_color' name='box_options[0][option_name]' id='add_option_name' style='width:115px;vertical-align:middle' value='".$box_options[option_name]."' title='옵션명'></span><!-- 옵션 삭제 -->
								</td>												
								<td>
									<!--img src='../images/".$_SESSION["admininfo"]["language"]."/btn_option_detail_add.gif' border=0 align=absmiddle style='cursor:pointer;'  id='btn_option_detail_add' onclick=\"AddOptionsCopyRow('box_options_table','box_options');\" /-->
									<img src='../v3/images/btn/bt_addoptionline.png' border=0 align=absmiddle style='cursor:pointer;'  id='btn_option_detail_add' onclick=\"AddOptionsCopyRow('box_options_table','box_options');\" />
									
								</td>
								<td>
									<div class='stockinfo_loade' style='display:inline;cursor:pointer;text-align:center;' id='btn_inventory_search' onclick=\"ShowModalWindow('../inventory/inventory_search.php?type=box_options&seq=0',1000,690,'inventory_search')\"><img src='../images/korean/stock.gif' alt='재고정보불러오기' title='재고정보불러오기' /></div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr height=34 id='box_option_info' >
					<td ".($_SESSION["admin_config"]["selling_type"] == "WR" ? "colspan=10":"colspan=9")." class='point_color' style='padding-left:10px;'>
					<table width=100%>
						<col width='130px'>
						<col width='115px'>
						<col width='*'>
						<tr>
						<td item='this'>총 1박스 합계 수량 </td>
						<td> <input type=text class='textbox number' name='box_options[0][box_total]' id='box_total'  style='width:68px' value='".$box_options[box_total]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' title='총 1박스 합계 수량'> 개 </td>
						<td> (등록된 상품중에 1박스 수량 만큼 구매를 하게 됩니다.)</td>
						</tr>
					</table> 
					</td>
				</tr>
				<tr height=25 bgcolor='#ffffff' align=center>
					<td bgcolor=\"#efefef\" class=small> 옵션구분 *</td>
					<td bgcolor=\"#efefef\" class=small> 공급가격 *</td>";
//if($DISPLAY_WHOSALE_INFO){
if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
    $Contents .= "	
					<td bgcolor=\"#efefef\" class=small> 도매가격 *</td>";
}
if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
    $Contents .= "	
					<td bgcolor=\"#efefef\" class=small> 소매가격 *</td>";
}


$Contents .= "	
					<td bgcolor=\"#efefef\" class=small> 품절여부</td>
					<td bgcolor=\"#efefef\" class=small> 판매진행재고/실재고  </td>
					<td bgcolor=\"#efefef\" class=small> 안전재고 </td>
					<td bgcolor=\"#efefef\" class=small> 옵션(품목)코드  </td>
					<td bgcolor=\"#efefef\" class=small> 바코드  </td>
					<td bgcolor=\"#efefef\" class=small> 관리  </td>
				</tr>";

$sql = "select * from shop_product_options_detail where pid = '".$box_options["pid"]."' and pid != '0'  and opn_ix = '".$box_options[opn_ix]."' order by set_group_seq asc,  id asc ";
$db->query($sql);
//if($db->total){
$box_options_detail = $db->fetchall();

for($i=0;($i < count($box_options_detail) || $i < 1);$i++){

    $Contents .= "	<tr height=30 bgcolor='#ffffff'  align='center' depth=1 item=1>
					<td>
					<input type='hidden' name='option_length' id ='option_length' value='".$i."'>
					<input type=text class='textbox point_color add_option_div' name='box_options[0][details][".$i."][option_div]' id='add_option_div' style='width:80%;vertical-align:middle' value='".$box_options_detail[$i][option_div]."'></td>
					<td><input type=text class='textbox number stock_readonly' name='box_options[0][details][".$i."][coprice]'  id='add_option_coprice' style='width:80%' value='".$box_options_detail[$i][option_coprice]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>";
    //if($DISPLAY_WHOSALE_INFO){
    if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "	
					<td>
						<input type=text class='textbox number' name='box_options[0][details][".$i."][wholesale_listprice]' title='도매판매가' id='add_option_wholesale_listprice' style='width:80%' value='".$box_options_detail[$i][option_wholesale_listprice]."' onkeydown='onlyEditableNumber(this)'  onkeyup=\"onlyEditableNumber(this);CalcurateMinimumPrice('box_options');\" ><br>
						<input type=text class='textbox number' name='box_options[0][details][".$i."][wholesale_price]' title='도매할인가' id='add_option_wholesale_price' style='margin:3px;width:80%' value='".$box_options_detail[$i][option_wholesale_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup=\"onlyEditableNumber(this);CalcurateMinimumPrice('box_options');\" >
					</td>";
    }
    if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "	
					<td style='padding:5px;'>
						<input type=text class='textbox number' name='box_options[0][details][".$i."][listprice]' title='판매가' id='add_option_listprice' style='width:80%' value='".$box_options_detail[$i][option_listprice]."' onkeydown='onlyEditableNumber(this)'  onkeyup=\"onlyEditableNumber(this);CalcurateMinimumPrice('box_options');\"><br>
						<input type=text class='textbox number' name='box_options[0][details][".$i."][sellprice]' title='할인가' id='add_option_sellprice' style='margin:3px;width:80%' value='".$box_options_detail[$i][option_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup=\"onlyEditableNumber(this);CalcurateMinimumPrice('box_options');\"  >";
        if($_SESSION["admininfo"]["admin_level"] > 8){
            $Contents .= "
						<input type=text class='textbox number' name='box_options[0][details][".$i."][premiumprice]' title='프리미엄가' id='add_option_premiumprice' style='margin:3px;width:80%' value='".$box_options_detail[$i][option_premiumprice]."' onkeydown='onlyEditableNumber(this)'  onkeyup=\"onlyEditableNumber(this);CalcurateMinimumPrice('box_options');\" disabled >
					</td>";
        }
    }
    $Contents .= "	
					<td><input type=checkbox name='box_options[0][details][".$i."][soldout]' id='add_option_soldout'  value='1' ".($box_options_detail[$i][option_soldout] == "1" ? "checked":"")."></td>
					<td>
						<input type=text class='textbox number' name='box_options[0][details][".$i."][sell_ing_cnt]' id='add_option_sell_ing_cnt' style='width:30px;margin:0px 3px;'  value='".($mode!="copy"?$box_options_detail[$i][option_sell_ing_cnt]:"0")."' title='판매진행중 재고' readonly>
						<input type=text class='textbox  number stock_readonly' name='box_options[0][details][".$i."][stock]' id='add_option_stock' style='width:50px;".($_SESSION["layout_config"]["mall_use_inventory"] == "Y" ? "background:#efefef;":"")."' value='".$box_options_detail[$i][option_stock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' ></td>
					<td><input type=text class='textbox stock_readonly' name='box_options[0][details][".$i."][safestock]' id='add_option_safestock' style='width:70%' value='".$box_options_detail[$i][option_safestock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
					<td>
						<input type=text class='textbox stock_readonly' name='box_options[0][details][".$i."][code]' id='add_option_code' style='width:30%' value='".$box_options_detail[$i][option_code]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'>
						<input type=text class='textbox stock_readonly' name='box_options[0][details][".$i."][gid]' id='add_option_gid' style='width:50%' value='".$box_options_detail[$i][option_gid]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'>
					</td>
					<td><input type=text class='textbox stock_readonly' name='box_options[0][details][".$i."][barcode]' id='add_option_barcode' style='width:80%' value='".$box_options_detail[$i][option_barcode]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
					<td><img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;margin:0px 0px 4px 3px' ondblclick=\"if($('#box_options_table').find('tr[depth^=1]').length > 1){\$(this).parent().parent().remove();}else{alert($('#box_options_table').find('tr[depth^=1]').length);}\" title='더블클릭시 해당 라인이 삭제 됩니다.'></td>
				</tr>";
}

$Contents .= "
				</table>
			</div>";

$sql = "select * from shop_product_options where pid = '".$id."' and pid != '0' and option_kind = 's1' order by opn_ix ";
$db->query($sql);
if($db->total) {
    $db->fetch();
    $set_options = $db->dt;
}

$Contents .= "<div id='set_option_zone' ".($use_option_type == "set_option" ? "":"style='display:none' ")." class='changeable_area set_options' >
				<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><b style='cursor:pointer;' class=blk><img src='../images/dot_org.gif' align=absmiddle style='vertical-align:middle;'> 세트 옵션정보 </b></div>")."</td></tr></table>
				<table width='100%' cellpadding=0 cellspacing=1 bgcolor=silver border='0' id='set_options_table' class='input_table_box set_options_table' idx=0 style='margin-bottom:10px;' >
					<col width='20%'>
					<col width='7%'>
					<col width='7%'>
					<col width='7%'>
					<col width='5%'>
					<col width='6%'>
					<col width='6%'>
					<col width='20%'>
					<col width='7%'>
					<col width='4%'>
					<tr bgcolor='#ffffff' height=34>
						<td ".($_SESSION["admin_config"]["selling_type"] == "WR" ? "colspan=10":"colspan=9").">
							<table>
								<tr>
									<td style='padding-left:10px;'>
										<input type=hidden name='set_options[0][option_type]' value='9' >
										<input type=hidden name='set_options[0][opn_ix]' id='option_opn_ix' value='".$set_options[opn_ix]."' >
										<input type=checkbox name='set_options[0][option_use]' id='add_option_use'  value='1' title='사용' ".($set_options[option_use] == "1" ? "checked":"").">
									</td>
									<td>
										<select name='set_options[0][option_kind]' id='option_kind' class='option_kind' style='font-size:12px;' validation=true title='옵션종류'><!--onchange=\"if(this.value == 'x'){ $('#set_option_info').show();}else{ $('#set_option_info').hide();}\" -->
											<option value=''>옵션종류</option>
											<option value='set' selected>세트 옵션</option>	
										</select>
									</td>
									<td ><b>옵션명 : </b><input type=text class='textbox point_color' name='set_options[0][option_name]' id='add_option_name'  style='width:115px;vertical-align:middle' value='".$set_options[option_name]."' title='옵션명'></span><!-- 옵션 삭제 -->
									</td>												
									<td>	<img src='../images/".$_SESSION["admininfo"]["language"]."/btn_option_detail_add.gif' border=0 align=absmiddle style='cursor:pointer;'  id='btn_option_detail_add' onclick=\"AddOptionsCopyRow('set_options_table','set_options');\" />
									</td>
									<td>
									<div class='stockinfo_loade' style='display:inline;cursor:pointer;text-align:center;' id='btn_inventory_search' onclick=\"ShowModalWindow('../inventory/inventory_search.php?type=set_options&seq=0',1000,690,'inventory_search')\"><img src='../images/korean/stock.gif' alt='재고정보불러오기' title='재고정보불러오기' /></div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<!--tr height=34 id='set_option_info' >
						<td colspan=10 class='point_color' style='padding-left:10px;'>
						<table width=100%>
							<col width='130px'>
							<col width='115px'>
							<col width='*'>
							<tr>
							<td>총 1박스 합계 수량 :  </td>
							<td> <input type=text class='textbox number' name='set_options[0][set_total]' id='set_total'  style='width:68px' value='".$set_options[set_total]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' title='총 1박스 합계 수량'> 개 </td>
							<td> (등록된 상품중에 1박스 수량 만큼 구매를 하게 됩니다.)</td>
							</tr>
						</table> 
						</td>
					</tr-->
					<tr height=25 bgcolor='#ffffff' align=center>
						<td bgcolor=\"#efefef\" class=small> 옵션구분 *</td>";
if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
    $Contents .= "
						<td bgcolor=\"#efefef\" class=small> 공급가격 *</td>";
}
if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
    $Contents .= "
						<td bgcolor=\"#efefef\" class=small> 도매가격 *</td>";
}
$Contents .= "						
						<td bgcolor=\"#efefef\" class=small> 소매가격 *</td>
						<td bgcolor=\"#efefef\" class=small> 품절여부</td>
						<td bgcolor=\"#efefef\" class=small> 판매진행재고/실재고  </td>
						<td bgcolor=\"#efefef\" class=small> 안전재고 </td>
						<td bgcolor=\"#efefef\" class=small> 구성상품</td>
						<td bgcolor=\"#efefef\" class=small> 바코드  </td>
						<td bgcolor=\"#efefef\" class=small> 관리  </td>
					</tr>";

$sql = "select 
					* 
					from
						shop_product_options_detail 
					where
						pid = '".$set_options["pid"]."' and pid != '0'  and opn_ix = '".$set_options[opn_ix]."' order by set_group_seq asc,  id asc ";
$db->query($sql);
$set_options_detail = $db->fetchall();
//if($db->total){

for($i=0;($i < count($set_options_detail) || $i < 1);$i++){

    $Contents .= "		<tr height=30 bgcolor='#ffffff'  align='center' depth=1 item=1>
						<td>
						<input type='hidden' name='option_length' id ='option_length' value='".$i."'>
						<input type=text class='textbox point_color add_option_div' name='set_options[0][details][".$i."][option_div]' id='add_option_div' style='width:80%;vertical-align:middle' value='".$set_options_detail[$i][option_div]."'></td>
						<td><input type=text class='textbox number stock_readonly' name='set_options[0][details][".$i."][coprice]'  id='add_option_coprice' style='width:80%' value='".$set_options_detail[$i][option_coprice]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>";
    if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "
						<td>
							<input type=text class='textbox number' name='set_options[0][details][".$i."][wholesale_listprice]' title='도매판매가' id='add_option_wholesale_listprice' style='width:80%' value='".$set_options_detail[$i][option_wholesale_listprice]."' onkeydown='onlyEditableNumber(this)' onkeyup='onlyEditableNumber(this)' ><br>
							<input type=text class='textbox number' name='set_options[0][details][".$i."][wholesale_price]' title='도매할인가' id='add_option_wholesale_price' style='margin:3px;width:80%' value='".$set_options_detail[$i][option_wholesale_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' >
						</td>";
    }

    if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "
						<td style='padding:5px;'>
							<input type=text class='textbox number' name='set_options[0][details][".$i."][listprice]' title='판매가' id='add_option_listprice' style='width:80%' value='".$set_options_detail[$i][option_listprice]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'><br>
							<input type=text class='textbox number' name='set_options[0][details][".$i."][sellprice]' title='할인가' id='add_option_sellprice' style='margin:3px;width:80%' value='".$set_options_detail[$i][option_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'  >";
        if($_SESSION["admininfo"]["admin_level"] > 8){
            $Contents .= "	
							<input type=text class='textbox number' name='set_options[0][details][".$i."][premiumprice]' title='프리미엄가' id='add_option_premiumprice' style='margin:3px;width:80%' value='".$set_options_detail[$i][option_premiumprice]."' onkeydown='onlyEditableNumber(this)'  onkeyup=\"onlyEditableNumber(this);\"  disabled>
						</td>";
        }
    }
    $Contents .= "
						<td><input type=checkbox name='set_options[0][details][".$i."][soldout]' id='add_option_soldout'  value='1' ".($set_options_detail[$i][option_soldout] == "1" ? "checked":"")."></td>
						<td>
							<input type=text class='textbox number' name='set_options[0][details][".$i."][sell_ing_cnt]' id='add_option_sell_ing_cnt' style='width:30px;margin:0px 3px;'  value='".($mode!="copy"?$set_options_detail[$i][option_sell_ing_cnt]:"0")."' title='판매진행중 재고' readonly>
							<input type=text class='textbox  number stock_readonly' name='set_options[0][details][".$i."][stock]' id='add_option_stock' style='width:50px;".($_SESSION["layout_config"]["mall_use_inventory"] == "Y" ? "background:#efefef;":"")."' value='".$set_options_detail[$i][option_stock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' ></td>
						<td><input type=text class='textbox stock_readonly' name='set_options[0][details][".$i."][safestock]' id='add_option_safestock' style='width:70%' value='".$set_options_detail[$i][option_safestock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
						<td style='padding:5px 5px;'>
						 <img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle onclick=\"ms_productSearch.show_productSearchBox(event,".$i.",'productSetList_".$i."');\" id='setoption_product_search' style='cursor:pointer;'><br>
						  <div style='width:100%;padding:5px;' ><!--id='group_product_area_".$i."' -->".relationSetOption($id, $i, "clipart")."</div>
						<!--input type=text class='textbox stock_readonly' name='set_options[0][details][".$i."][code]' id='add_option_code' style='width:80%' value='".$set_options_detail[$i][option_code]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'-->
						</td>
						<td><input type=text class='textbox stock_readonly' name='set_options[0][details][".$i."][barcode]' id='add_option_barcode' style='width:80%' value='".$set_options_detail[$i][option_barcode]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
						<td><img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;margin:0px 0px 4px 3px' ondblclick=\"if($('#set_options_table').find('tr[depth^=1]').length > 1){\$(this).parent().parent().remove();}else{alert($('#set_options_table').find('tr[depth^=1]').length);}\" title='더블클릭시 해당 라인이 삭제 됩니다.'></td>
					</tr>";
}

$Contents .= "
					</table>
				</div>";

$sql = "select * from shop_product_options where pid = '".$id."' and pid != '0' and option_kind in ('s2','x2') order by opn_ix ";
//echo $sql;
$db->query($sql);
if($db->total) {
    $set2options = $db->fetchall();

    $sql = "select * from shop_product_options_global where pid = '".$id."' and pid != '0' and option_kind in ('s2','x2') order by opn_ix ";
    $db->query($sql);
    foreach($language_list as $l){
        ${$l['language_code'].'_set2options'} = $set2options = $db->fetchall();
    }
}

$Contents .= "	<div id='set2_option_zone' ".($use_option_type == "set2_option" ? "":"style='display:none' ")." class='changeable_area set_options' >";
$i=0;
$Contents .= "
				<table width='100%' cellpadding=0 cellspacing=1 bgcolor=silver border='0' id='set2option_table'  style='margin-bottom:10px;' >
				<col width='14%'>
				<col width='9%'>
				<col width='9%'>
				<col width='9%'>
				<col width='5%'>
				<col width='11%'>
				<col width='6%'>
				<col width='11%'>
				<col width='11%'>
				<col width='4%'>
				<tr bgcolor='#ffffff' height=34>
					<td ".($_SESSION["admin_config"]["selling_type"] == "WR" ? "colspan=10":"colspan=9").">
						<table>
							<tr>
								<td style='padding-left:10px;'><input type=hidden name='set2options[".$i."][option_type]' value='9' >
									<input type=checkbox name='set2options[".$i."][option_use]' id='set2_option_use'  value='1' title='사용' ".($set2options[$i][option_use] == "1" ? "checked":"").">
								</td>
								<td>
									<select name='set2options[".$i."][option_kind]' id='option_kind' class='option_kind' style='font-size:12px;'  validation=false title='옵션종류'>
										<option value=''>옵션종류</option>
										<!--option value=x>선택박스</option-->
										<option value=s2  selected>세트박스 상품</option>
										<!-- <option value=x2 >박스 상품</option> -->
									</select>
									 <b>옵션명 : </b>
								</td>
								<td>
								    <input type=text class='textbox point_color' name='set2options[".$i."][option_name]' id='add_option_name' style='width:115px;vertical-align:middle' value='".$set2options[$i][option_name]."' title='세트 옵션명'>";

if(count($language_list) > 0){
    foreach($language_list as $key => $li){
        $_name = $li['language_code'].'_set2options';
        $_value = $$_name;

        $Contents .= "<br/> <input type=text class='textbox point_color' name='set2options[".$i."][".$li['language_code']."_option_name]' id='".$li['language_code']."_add_option_name' style='width:115px;vertical-align:middle' value='".$_value[$i][option_name]."' title='".$li[language_name]." 세트 옵션명' placeholder='".$li[language_name]." 세트 옵션명'>";
    }
}
$Contents .= "
								</td>
								<td><!--img src='../images/".$_SESSION["admininfo"]["language"]."/btn_option_detail_add.gif' border=0 align=absmiddle style='cursor:pointer;'  id='btn_option_detail_add' onclick=\"AddOptionsCopyRow('set2options_table_0','set2options');\" /-->
								</td>
								<td>
								
								</td>
							</tr>
						</table>
					</td>
				</tr>
				</table>";

$sql = "select 
			distinct set_group
		from
			shop_product_options_detail 
		where
			pid = '".$set2options[$i]["pid"]."' 
			and opn_ix = '".$set2options[$i][opn_ix]."' 
			order by  id asc ";
$db->query($sql);
$set2option_info = $db->fetchall();

$opn_ix = $set2options[$i][opn_ix];
$pid = $set2options[$i]["pid"];

for($i=0;($i<count($set2option_info) || $i < 1);$i++){

    if($set2option_info[$i][set_group] != ""){
        $set_group = $set2option_info[$i][set_group];
    }else{
        $set_group = $i;
    }

    $no = $i + 1;

    $Contents .= "
				<table width='100%' cellpadding=0 cellspacing=1 bgcolor=silver border='0' id='set2options_table_".$set_group."' class='input_table_box set2options_table' idx=0 style='margin-bottom:10px;' >
				<col width='10%'>
				<col width='9%'>";
    if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "
				<col width='7%'>";
    }
    if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "
				<col width='14%'>";
    }
    $Contents .= "
				<col width='5%'>
				<col width='6%'>
				<col width='6%'>
				<col width='11%'>
				<col width='5%'>
				<col width='4%'>
				<tr height=28  bgcolor='#ffffff' align=center>
					<td bgcolor=\"#efefef\"  > <b>세트상품명 *</b></td>
					<td bgcolor=\"#efefef\" > <b>세트 공급가격 *</b></td>";
    if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "
					<td bgcolor=\"#efefef\" > <b>세트 도매가격 *</b></td>";
    }

    if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "
					<td bgcolor=\"#efefef\" > <b>세트 소매가격 *</b></td>";
    }
    $Contents .= "
					<td bgcolor=\"#efefef\" > 품절여부</td>
					<td bgcolor=\"#efefef\" > 세트 재고  </td>
					<td bgcolor=\"#efefef\" > 세트 안전재고 </td>
					<td bgcolor=\"#efefef\" > - </td>
					<td bgcolor=\"#efefef\" > 바코드  </td>
					<td bgcolor=\"#efefef\" > 관리  </td>
				</tr>";

    $sql = "select 
		* 
	from
		shop_product_options_detail 
	where
		pid = '".$pid."' and pid != '0' 
		and opn_ix = '".$opn_ix."' and set_group = '".$set_group."'
		order by set_group_seq asc, id asc";//
    $db->query($sql);
    $set2options_detail = $db->fetchall("object");

    $sql = "select 
		* 
	from
		shop_product_options_detail_global 
	where
		pid = '".$pid."' and pid != '0' 
		and opn_ix = '".$opn_ix."' and set_group = '".$set_group."'
		order by set_group_seq asc, id asc";
    $db->query($sql);
    foreach($language_list as $l){
        ${$l['language_code'].'_set2options_detail'} = $db->fetchall("object");
    }

    $j=0;
    $Contents .= "
				<tr height=30 bgcolor='#efefef'  align='center' item=1 setrow=1 options='1'>
					<td>
					<input type='hidden' name='option_length_table' id ='option_length_table' value='".$set_group."'><!-- 세트상품전용 table 개수 체크하기-->
					<input type=text class='textbox point_color' name='set2options[".$set_group."][details][".$j."][option_div]' id='add_option_div' style='width:80%;vertical-align:middle' value='".$set2options_detail[$j][option_div]."' title='세트상품명'>";

    if(count($language_list) > 0){
        foreach($language_list as $key => $li){
            $_name = $li['language_code'].'_set2options_detail';
            $_value = $$_name;

            $Contents .= "<br/> <input type=text class='textbox point_color' name='set2options[".$set_group."][details][".$j."][".$li['language_code']."_option_div]' id='".$li['language_code']."_add_option_div' style='width:80%;margin-top:3px;vertical-align:middle' value='".$_value[$j][option_div]."' title='".$li[language_name]." 세트상품명' placeholder='".$li[language_name]." 세트상품명'>";
        }
    }
    $Contents .= " </td>
					<td>
						<input type=text class='textbox numeric' name='set2options[".$set_group."][details][".$j."][coprice]'  id='add_option_coprice' sum_id='add_option_coprice' style='width:80%' value='".$set2options_detail[$j][option_coprice]."' onclick=\"alert('구성품의 품의 정보를 수정할 경우 자동반영 됩니다.');\" readonly>";

    if(count($language_list) > 0){
        foreach($language_list as $key => $li){
            $_name = $li['language_code'].'_set2options_detail';
            $_value = $$_name;

            $Contents .= "<br/> <input type=text class='textbox numeric' name='set2options[".$set_group."][details][".$j."][".$li['language_code']."_coprice]' id='".$li['language_code']."_add_option_coprice' sum_id='".$li['language_code']."_add_option_coprice' style='width:80%;margin-top:3px;' value='".$_value[$j][option_coprice]."' onclick=\"alert('구성품의 품의 정보를 수정할 경우 자동반영 됩니다.');\">";
        }
    }
    $Contents .= "
					</td>";
    if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "
					<td style='padding:5px;'>
						<input type=text class='textbox number' name='set2options[".$set_group."][details][".$j."][wholesale_listprice]' id='add_option_wholesale_listprice' sum_id='add_option_wholesale_listprice' style='width:80%' value='".$set2options_detail[$j][option_wholesale_listprice]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' title='세트 도매가' onclick=\"alert('구성품의 품의 정보를 수정할 경우 자동반영 됩니다.');\" readonly>
						<input type=text class='textbox number' name='set2options[".$set_group."][details][".$j."][wholesale_price]' id='add_option_wholesale_price' sum_id='add_option_wholesale_price' style='margin:3px;width:80%' value='".$set2options_detail[$j][option_wholesale_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' title='세트 도매가' onclick=\"alert('구성품의 품의 정보를 수정할 경우 자동반영 됩니다.');\" readonly>
					</td>";
    }
    if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "
					<td style='padding:5px;'>
						<input type=text class='textbox numeric' name='set2options[".$set_group."][details][".$j."][listprice]' id='add_option_listprice' sum_id='add_option_listprice'  style='width:42%' value='".$set2options_detail[$j][option_listprice]."' title='세트 정가' onclick=\"alert('구성품의 품의 정보를 수정할 경우 자동반영 됩니다.');\" readonly>
						<input type=text class='textbox numeric' name='set2options[".$set_group."][details][".$j."][sellprice]' id='add_option_sellprice' sum_id='add_option_sellprice'  style='width:42%' value='".$set2options_detail[$j][option_price]."' title='세트 판매가' onclick=\"alert('구성품의 품의 정보를 수정할 경우 자동반영 됩니다.');\" readonly>";

        if(count($language_list) > 0){
            foreach($language_list as $key => $li){
                $_name = $li['language_code'].'_set2options_detail';
                $_value = $$_name;

                $Contents .= "<br/> <input type=text class='textbox numeric' name='set2options[".$set_group."][details][".$j."][".$li['language_code']."_listprice]' id='".$li['language_code']."_add_option_listprice' sum_id='".$li['language_code']."_add_option_listprice'  style='margin-top:3px;width:42%' value='".$_value[$j][option_listprice]."' title='".$li[language_name]." 세트 정가' onclick=\"alert('구성품의 품의 정보를 수정할 경우 자동반영 됩니다.');\" readonly>
						<input type=text class='textbox numeric' name='set2options[".$set_group."][details][".$j."][".$li['language_code']."_sellprice]' id='".$li['language_code']."_add_option_sellprice' sum_id=".$li['language_code']."_add_option_sellprice'  style='margin-top:3px;width:42%' value='".$_value[$j][option_price]."' title='".$li[language_name]." 세트 판매가' onclick=\"alert('구성품의 품의 정보를 수정할 경우 자동반영 됩니다.');\" readonly>";
            }
        }

        $Contents .= "
					</td>";
    }
    $Contents .= "
					<td><input type=checkbox name='set2options[".$set_group."][details][".$j."][soldout]' id='add_option_soldout'  value='1' ".($set2options_detail[$j][option_soldout] == 1 ? "checked":"")."--></td>
					<td>
						<input type=text class='textbox number' name='set2options[".$set_group."][details][".$j."][sell_ing_cnt]' id='add_option_sell_ing_cnt' style='width:30px;margin:0px 3px;'  value='".($mode!="copy"?$set2options_detail[$j][option_sell_ing_cnt]:"0")."' title='판매진행중 재고' readonly>
						<input type=text class='textbox number' name='set2options[".$set_group."][details][".$j."][stock]' id='add_option_stock' sum_id='add_option_stock' style='width:50px;".($_SESSION["layout_config"]["mall_use_inventory"] == "Y" ? "background:#efefef;":"")."' value='".$set2options_detail[$j][option_stock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' >
					</td>
					<td><input type=text class='textbox' name='set2options[".$set_group."][details][".$j."][safestock]' id='add_option_safestock' style='width:70%' value='".$set2options_detail[$j][option_safestock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
					<td>
						<input type=text class='textbox' name='set2options[".$set_group."][details][".$j."][code]' id='add_option_code' style='width:30%' value='".$set2options_detail[$j][option_code]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'>
						<input type=text class='textbox' name='set2options[".$set_group."][details][".$j."][gid]' id='add_option_gid' style='width:50%' value='".$set2options_detail[$j][option_gid]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'>
					</td>
					<td><input type=text class='textbox' name='set2options[".$set_group."][details][".$j."][barcode]' id='add_option_barcode' style='width:80%' value='".$set2options_detail[$j][option_barcode]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
					<td><img src='../images/i_close.gif' align=absmiddle id='close_btn'  style='cursor:pointer;margin:0px 0px 4px 3px' ondblclick=\"if($('.set2options_table').length > 1){\$(this).parent().parent().parent().parent().remove();$('table[class=set2options_table]:last').find('img[id^=close_btn]').show();}else{ $(this).parent().parent().find('input[type=text]').val('');}\" title='더블클릭시 해당 라인이 삭제 됩니다.'></td>
				</tr>
				<tr height=28 bgcolor='#ffffff' align=center>
					<td bgcolor=\"#efefef\" class=small> 
					<table>
						<tr>
							<td>	구성상품 * </td>
							<td>
							<div class='stockinfo_loade' style='display:inline;cursor:pointer;text-align:center;' id='btn_inventory_search' onclick=\"ShowModalWindow('../inventory/inventory_search.php?type=set2options&seq=".$set_group."',1000,690,'inventory_search')\"><img src='../images/korean/stock.gif' alt='재고정보불러오기' title='재고정보불러오기' style='margin-bottom:3px;' align=absmiddle/></div>
							</td>
						</tr>
					</table>
					</td><!--img src='../images/".$admininfo["language"]."/btn_add2.gif'  style='cursor:pointer' align=absmiddle--></td>
					<td bgcolor=\"#efefef\" class=small> 공급가격 *</td>";
    if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "
					<td bgcolor=\"#efefef\" class=small> 도매가격 *</td>";
    }
    if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "
					<td bgcolor=\"#efefef\" class=small> 소매가격 *</td>";
    }
    $Contents .= "
					<td bgcolor=\"#efefef\" class=small> 구성수량</td>
					<td bgcolor=\"#efefef\" class=small> 판매진행재고/실재고  </td>
					<td bgcolor=\"#efefef\" class=small> 안전재고 </td>
					<td bgcolor=\"#efefef\" class=small> 옵션(품목)코드  </td>
					<td bgcolor=\"#efefef\" class=small> 바코드  </td>
					<td bgcolor=\"#efefef\" class=small> 관리  </td>
				</tr>";


    for($j=1, $z=0;($j < count($set2options_detail) || $j < 2);$j++,$z++){
        $Contents .= "<tr height=30 bgcolor='#ffffff'  align='center' depth=1 item=1 class='sets_options'>
					<td>
                        <input type='hidden' name='option_length' id ='option_length' value='".$j."'>
                        <input type=text class='textbox point_color set2option_div' name='set2options[".$set_group."][details][".$j."][option_div]' id='add_option_div' style='width:80%;vertical-align:middle' value='".$set2options_detail[$j][option_div]."'>";

        if(count($language_list) > 0){
            foreach($language_list as $key => $li){
                $_name = $li['language_code'].'_set2options_detail';
                $_value = $$_name;

                $Contents .= "<br/> <input type=text class='textbox point_color set2option_div' name='set2options[".$set_group."][details][".$j."][".$li['language_code']."_option_div]' id='".$li['language_code']."_add_option_div' style='width:80%;margin-top:3px;vertical-align:middle' value='".$_value[$j][option_div]."' title='".$li[language_name]." 구성상품' placeholder='".$li[language_name]." 구성상품'>";
            }
        }

        $Contents .= "
					</td>
					<td>
					    <input type=text class='textbox numeric' name='set2options[".$set_group."][details][".$j."][coprice]'  id='add_option_coprice'  style='width:80%' value='".$set2options_detail[$j][option_coprice]."' onkeyup=\"OptionCalcuration('add_option_coprice',$(this).parent().parent().parent().parent().attr('id'),'sum');\">";

        if(count($language_list) > 0){
            foreach($language_list as $key => $li){
                $_name = $li['language_code'].'_set2options_detail';
                $_value = $$_name;

                $Contents .= "<br/> <input type=text class='textbox numeric' name='set2options[".$set_group."][details][".$j."][".$li['language_code']."_coprice]' id='".$li['language_code']."_add_option_coprice' style='width:80%;margin-top:3px;' value='".$_value[$j][option_coprice]."' onkeyup=\"OptionCalcuration('".$li['language_code']."_add_option_coprice',$(this).parent().parent().parent().parent().attr('id'),'sum');\">";
            }
        }

        $Contents .= "
					</td>";
        if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
            $Contents .= "
					<td style='padding:5px;'>
						<input type=text class='textbox number' name='set2options[".$set_group."][details][".$j."][wholesale_listprice]' id='add_option_wholesale_listprice' style='width:80%' value='".$set2options_detail[$j][option_wholesale_listprice]."' onkeydown='onlyEditableNumber(this)'  onkeyup=\"OptionCalcuration('add_option_wholesale_listprice',$(this).parent().parent().parent().parent().attr('id'),'sum');onlyEditableNumber(this);CalcurateMinimumPrice('set2options');\">
						<input type=text class='textbox number' name='set2options[".$set_group."][details][".$j."][wholesale_price]' id='add_option_wholesale_price' style='margin:3px;width:80%' value='".$set2options_detail[$j][option_wholesale_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup=\"OptionCalcuration('add_option_wholesale_price',$(this).parent().parent().parent().parent().attr('id'),'sum');onlyEditableNumber(this);CalcurateMinimumPrice('set2options');\">
					</td>";
        }
        if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
            $Contents .= "
					<td style='padding:5px;'>
						<input type=text class='textbox numeric' name='set2options[".$set_group."][details][".$j."][listprice]' id='add_option_listprice' style='width:42%' value='".$set2options_detail[$j][option_listprice]."' onkeyup=\"OptionCalcuration('add_option_listprice',$(this).parent().parent().parent().parent().attr('id'),'sum');\">
						<input type=text class='textbox numeric' name='set2options[".$set_group."][details][".$j."][sellprice]' id='add_option_sellprice' style='width:42%' value='".$set2options_detail[$j][option_price]."' onkeyup=\"OptionCalcuration('add_option_sellprice',$(this).parent().parent().parent().parent().attr('id'),'sum');\">";

            if(count($language_list) > 0){
                foreach($language_list as $key => $li){
                    $_name = $li['language_code'].'_set2options_detail';
                    $_value = $$_name;

                    $Contents .= "<br/>
                        <input type=text class='textbox numeric' name='set2options[".$set_group."][details][".$j."][".$li['language_code']."_listprice]' id='".$li['language_code']."_add_option_listprice' style='margin-top:3px;width:42%' value='".$_value[$j][option_listprice]."' onkeyup=\"OptionCalcuration('".$li['language_code']."_add_option_listprice',$(this).parent().parent().parent().parent().attr('id'),'sum');\" title='".$li[language_name]." 구성상품 정가' placeholder='".$li[language_name]." 구성상품 정가'>
                         <input type=text class='textbox numeric' name='set2options[".$set_group."][details][".$j."][".$li['language_code']."_sellprice]' id='".$li['language_code']."_add_option_sellprice' style='margin-top:3px;width:42%' value='".$_value[$j][option_price]."' onkeyup=\"OptionCalcuration('".$li['language_code']."_add_option_sellprice',$(this).parent().parent().parent().parent().attr('id'),'sum');\" title='".$li[language_name]." 구성상품 판매가' placeholder='".$li[language_name]." 구성상품 판매가'>";
                }
            }

            $Contents .= "
					</td>";
        }
        $Contents .= "
					<td><input type=text class='textbox number'  name='set2options[".$set_group."][details][".$j."][set_cnt]' id='add_option_set_cnt'  style='width:60%'  value='".$set2options_detail[$j][option_etc1]."' onkeyup=\"OptionCalcuration('add_option_set_cnt',$(this).parent().parent().parent().parent().attr('id'),'sum');onlyEditableNumber(this)\"><!--".($set2options_detail[$j][option_etc1] == 1 ? "checked":"")."--></td>
					<td>
						<input type=text class='textbox number' name='set2options[".$set_group."][details][".$j."][sell_ing_cnt]' id='add_option_sell_ing_cnt' style='width:30px;margin:0px 3px;'  value='".($mode!="copy"?$set2options_detail[$j][option_sell_ing_cnt]:"0")."' title='판매진행중 재고' readonly>
						<input type=text class='textbox number' name='set2options[".$set_group."][details][".$j."][stock]' id='add_option_stock' style='width:50px;".($_SESSION["layout_config"]["mall_use_inventory"] == "Y" ? "background:#efefef;":"")."' value='".$set2options_detail[$j][option_stock]."' onkeydown=\"onlyEditableNumber(this)\"  onkeyup=\"OptionCalcuration('add_option_stock',$(this).parent().parent().parent().parent().attr('id'),'min');onlyEditableNumber(this)\" >
					</td>
					<td><input type=text class='textbox' name='set2options[".$set_group."][details][".$j."][safestock]' id='add_option_safestock' style='width:70%' value='".$set2options_detail[$j][option_safestock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
					<td>
						<input type=text class='textbox' name='set2options[".$set_group."][details][".$j."][code]' id='add_option_code' style='width:30%' value='".$set2options_detail[$j][option_code]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'>
						<input type=text class='textbox' name='set2options[".$set_group."][details][".$j."][gid]' id='add_option_gid' style='width:50%' value='".$set2options_detail[$j][option_gid]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'>
					</td>
					<td><input type=text class='textbox' name='set2options[".$set_group."][details][".$j."][barcode]' id='add_option_barcode' style='width:80%' value='".$set2options_detail[$j][option_barcode]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
					<td>
						
						<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;margin:0px 0px 4px 0px' ondblclick=\"if($(this).parent().parent().parent().find('tr[depth^=1]').length > 1){\$(this).parent().parent().remove(); }else{  $(this).parent().parent().find('input[type=text]').val('');}allOptionCalcuration();\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
						<img src='../images/i_add.gif' border=0 style='cursor:pointer;margin:0px 0px 4px 0px;' align=absmiddle onclick=\"AddOptionsCopyRow('set2options_table_".$set_group."','set2options','".$set_group."');\" id='btn_option_detail_add'>
						</td>
				</tr>";
    }
    $Contents .= "</table>";
}

$Contents .= "</div>";

//}

//코디옵션 추가 이학봉 시작 2014-01-09 이학봉

$sql = "select * from shop_product_options where pid = '".$id."' and pid != '0'  and option_kind = 'c' order by opn_ix asc ";
$db->query($sql);
if($db->total > 0) {
    $codi_options = $db->fetchall("object");

    $sql = "select * from shop_product_options_global where pid = '" . $id . "' and pid != '0'  and option_kind = 'c' order by opn_ix asc ";
    $db->query($sql);
    foreach ($language_list as $l) {
        ${$l['language_code'] . '_codi_options'} = $db->fetchall("object");
    }
}

$Contents .= "<div id='codi_option_zone' ".($use_option_type == "codi_option" ? "":"style='display:none' ")." class='changeable_area set_options' >
				<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=30>
						<td style='padding-bottom:10px;'>
							".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><b style='cursor:pointer;' class=blk><img src='../images/dot_org.gif' align=absmiddle style='vertical-align:middle;'> 코디상품옵션(코디옵션 추가) </b><a name='codiProduct'></a><img src='../v3/images/btn/btn_option_add.png' border=0 align=absmiddle style='cursor:pointer;' id='btn_option_add' onclick=\"AddCopyOptions('codi_options_table','codi_options','codi_option_zone')\"/><label><input type='radio' class='codi_options_price_stock_type' name='codi_options[0][option_type]' value='n' ".($codi_options[0][option_type] == 'n' || $codi_options[0][option_type] == '' ? 'checked':'').">기본</label><label><input type='radio' class='codi_options_price_stock_type' name='codi_options[0][option_type]' id='option_type_s' value='d' ".($codi_options[0][option_type] == 'd'?'checked':'')."> 옵션1+옵션2</label></div>")."
						</td>
					</tr>
				</table>
				<script type='text/javascript'>
        $('.codi_options_price_stock_type').click(function(){
            if ( $(this).val() == 'd' ) {
                $('.options_price_codi_option_div_area').hide();
                $('.options_price_codi_option_color_size_area').show();
            } else { // 기타(기본)
                $('.options_price_codi_option_div_area').show();
                $('.options_price_codi_option_color_size_area').hide();
            }
        })
    </script>";
if(false){
    $Contents .= "
				<table width='100%' border=0 cellpadding=0 cellspacing=0 style='position:relative'>
					<tr>
						<td>".getOptionTmpTitle()."</td><!--임시 주석처리-->
					</tr>
				</table>
";
}

$i=0;
for($i=0;($i < count($codi_options) || $i < 1);$i++){

    $Contents .= "
				<table width='100%' cellpadding=0 cellspacing=1 bgcolor=silver border='0' id='codi_options_table_".$i."' class='codi_options_table input_table_box ' option_type=codi idx=0 style='margin-bottom:10px;' ><!--class='codi_options_table_".$i."'-->
				<col width='15%'>
				<col width='7%'>";
    if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "
				<col width='7%'>";
    }
    if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "
				<col width='12%'>";
    }
    $Contents .= "
                <col width='5%'>
				<col width='5%'>
				<col width='7%'>
				<col width='6%'>
				<col width='11%'>
				<col width='5%'>
				<col width='4%'>
				<tr height=35 bgcolor='#ffffff' align=center>
					<td colspan='11'>
						<table width=100% border='0'>
						<col width='10'>
						<col width='160'>
						<col width='155'>
						<col width='100'>
						<col width='100'>
						
						<tr options='1'>
							<td style='padding-left:10px;'>
								<input type='hidden' name='option_length_table' id ='option_length_table' value='".$i."'><!-- 세트상품전용 table 개수 체크하기-->
								<input type=hidden name='codi_options[".$i."][opn_ix]' id='option_opn_ix' value='".$codi_options[$i][opn_ix]."' >
								<input type=checkbox name='codi_options[".$i."][option_use]' id='add_option_use' class='setOption' value='1' title='사용'  ".($codi_options[$i][option_use] == "1" ? "checked":"").">
							</td>
							<td>
								<!--input type='hidden' name='codi_options[".$i."][option_kind]' value='b' id='option_kind' class='option_kind' style='font-size:12px;' validation=false title='옵션종류'-->
								<select name='codi_options[".$i."][option_kind]' id='option_kind' class='option_kind' style='font-size:12px;' onchange=\"if(this.value == 'x'){ $('#box_option_info').show();}else{ $('#box_option_info').hide();}\" validation=true title='옵션종류'>
									<option value=''>옵션종류</option>
									<option value=c selected>코디상품 옵션</option>												
								</select>
								
								<b>옵션명 : </b>
							</td>
							<td >
								<input type=text class='textbox point_color' name='codi_options[".$i."][option_name]' id='add_option_name' inputid='option_name' style='width:115px;vertical-align:middle' value='".$codi_options[$i][option_name]."' title='옵션명'>";

    if(count($language_list) > 0){
        foreach($language_list as $key => $li){
            $_name = $li['language_code'].'_codi_options';
            $_value = $$_name;

            $Contents .= "<br/> <input type=text class='textbox point_color' name='codi_options[".$i."][".$li['language_code']."_option_name]' id='".$li['language_code']."_add_option_name' style='width:115px;vertical-align:middle;margin-top:3px;' value='".$_value[$i][option_name]."' title='".$li[language_name]." 옵션명' placeholder='".$li[language_name]." 옵션명'>";
        }
    }

    $Contents .= "
							</td>
							<td>
								<img src='../images/".$_SESSION["admininfo"]["language"]."/btn_option_detail_add.gif' border=0 align=absmiddle style='cursor:pointer;'  id='btn_option_detail_add' onclick=\"AddOptionsCopyRow('codi_options_table_".$i."','codi_options','".$i."');\" />
							</td>
							<td>
								<div class='stockinfo_loade' style='display:inline;cursor:pointer;text-align:center;' id='btn_inventory_search' onclick=\"ShowModalWindow('../inventory/inventory_search.php?type=codi_options&seq=".$i."',1000,690,'inventory_search')\"><img src='../images/korean/stock.gif' alt='재고정보불러오기' title='재고정보불러오기' /></div>
							</td>
							<td>
								<button type='button' onclick=\"change_price(this, 'option')\">옵션가동일</button>
								<button type='button' onclick=\"change_price(this, 'zero')\">0원 변경</button>
								<button type='button' onclick=\"change_price(this, 'currency')\" exchange='$exchange_info' >환율 적용(".$exchange_info.")</button>
							</td>
							<td align='right' style='padding-right:5px;'>
								<div class='stockinfo_loade' style='display:inline;cursor:pointer;text-align:center;' id='del_coditable' onclick=\"del_coditable('".$i."')\"><img src='../images/btn_x.gif' alt='해당 옵션 삭제' title='해당 옵션 삭제' /></div>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr height=25 bgcolor='#ffffff' align=center>
					<td bgcolor=\"#efefef\" class=small> 옵션구분 *</td>
					<td bgcolor=\"#efefef\" class=small> 공급가격 *</td>";
    if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "
					<td bgcolor=\"#efefef\" class=small> 도매가격 *</td>";
    }
    if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
        $Contents .= "
					<td bgcolor=\"#efefef\" class=small> 소매가격 *</td>";
    }
    $Contents .= "
                    <td bgcolor=\"#efefef\" class=small> 구성수량</td>
					<td bgcolor=\"#efefef\" class=small> 품절여부</td>
					<td bgcolor=\"#efefef\" class=small> 판매진행재고/실재고  </td>
					<td bgcolor=\"#efefef\" class=small> 안전재고 </td>
					<td bgcolor=\"#efefef\" class=small> 옵션(품목)코드  </td>
					<td bgcolor=\"#efefef\" class=small> 바코드  </td>
					<td bgcolor=\"#efefef\" class=small> 관리  </td>
				</tr>";
    $sql = "select 
          pod.* 
        from 
          shop_product_options_detail pod 
        left join 
          inventory_goods ig on pod.option_gid = ig.gid
        left join
          shop_product_options_sort_by_value sb on ig.size = sb.value
        where
          pod.pid = '".$id."' 
        and 
          pod.pid != '0'  
        and 
          pod.opn_ix = '".$codi_options[$i][opn_ix]."' 
        order by  
            sb.view_order asc ,opn_ix asc
          ";

    //$sql = "select * from shop_product_options_detail where pid = '".$id."' and pid != '0' and opn_ix='".$codi_options[$i][opn_ix]."' order by opn_ix asc ";
    $db->query($sql);
    $stock_options_detail = $db->fetchall("object");

    $sql = "select 
          pod.* 
        from 
          shop_product_options_detail_global pod 
        left join 
          inventory_goods ig on pod.option_gid = ig.gid
        left join
          shop_product_options_sort_by_value sb on ig.size = sb.value
        where
          pod.pid = '".$id."' 
        and 
          pod.pid != '0'  
        and 
          pod.opn_ix = '".$codi_options[$i][opn_ix]."' 
        order by  
            sb.view_order asc ,opn_ix asc
          ";

    //$sql = "select * from shop_product_options_detail_global where pid = '".$id."' and pid != '0' and opn_ix='".$codi_options[$i][opn_ix]."' order by opn_ix asc ";
    $db->query($sql);
    foreach ($language_list as $l) {
        ${$l['language_code'] . '_stock_options_detail'} = $db->fetchall("object");
    }

    for($j=0;($j < count($stock_options_detail) || $j < 1);$j++){
        $Contents .= "
				<tr height=30 bgcolor='#ffffff'  align='center' depth=1 item=1>
					<td>
					<input type='hidden' name='option_length' id ='option_length' value='".$j."'>
					<div class='options_price_codi_option_div_area' ".($codi_options[$i][option_type] == 'n' || $codi_options[$i][option_type] == '' ? "" : "style='display:none;'").">
                        <input type=text class='textbox point_color add_option_div' name='codi_options[".$i."][details][".$j."][option_div]' id='add_option_div' style='width:80%;vertical-align:middle' value='".$stock_options_detail[$j][option_div]."'>";
            if(count($language_list) > 0){
                foreach($language_list as $key => $li){
                    $_name = $li['language_code'].'_stock_options_detail';
                    $_value = $$_name;

                    $Contents .= "<br/> <input type=text class='textbox point_color add_option_div' name='codi_options[".$i."][details][".$j."][".$li['language_code']."_option_div]' id='".$li['language_code']."_add_option_div' style='width:80%;vertical-align:middle;margin-top:3px;' value='".$_value[$j][option_div]."' title='".$li[language_name]." 옵션구분' placeholder='".$li[language_name]." 옵션구분'>";
                }
            }
        $Contents .= "
                    </div>
                    <div class='options_price_codi_option_color_size_area' ".($codi_options[$i][option_type] == 'd' ? "" : "style='display:none;'").">
                    옵션1:<input type=text class='textbox point_color add_option_color' name='codi_options[".$i."][details][".$j."][option_color]' id='add_option_color' style='margin-top:5px;width:62%;vertical-align:middle' value='".$stock_options_detail[$j][option_color]."'>";

                if(count($language_list) > 0){
                    foreach($language_list as $key => $li){
                        $_name = $li['language_code'].'_stock_options_detail';
                        $_value = $$_name;

                        $Contents .= "<br/>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type=text class='textbox point_color add_option_color' name='codi_options[".$i."][details][".$j."][".$li['language_code']."_option_color]' id='".$li['language_code']."_add_option_color' style='margin-top:5px;width:62%;vertical-align:middle' value='".$_value[$j][option_color]."' placeholder='".$li[language_name]."'>";
                    }
                }

        $Contents .= "<br/>옵션2:<input type=text class='textbox point_size add_option_size' name='codi_options[".$i."][details][".$j."][option_size]' id='add_option_size' style='margin-top:5px;width:62%;vertical-align:middle' value='".$stock_options_detail[$j][option_size]."'>";

                if(count($language_list) > 0){
                    foreach($language_list as $key => $li){
                        $_name = $li['language_code'].'_stock_options_detail';
                        $_value = $$_name;

                        $Contents .= "<br/>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type=text class='textbox point_size add_option_size' name='codi_options[".$i."][details][".$j."][".$li['language_code']."_option_size]' id='".$li['language_code']."_add_option_size' style='margin-top:5px;width:62%;vertical-align:middle' value='".$_value[$j][option_size]."' placeholder='".$li[language_name]."'>";
                    }
                }

    $Contents .= "
					</div>
					</td>

					<td>
					    <input type=text class='textbox numeric stock_readonly' name='codi_options[".$i."][details][".$j."][coprice]'  id='add_option_coprice' style='width:80%' value='".$stock_options_detail[$j][option_coprice]."'>";

        if(count($language_list) > 0){
            foreach($language_list as $key => $li){
                $_name = $li['language_code'].'_stock_options_detail';
                $_value = $$_name;

                $Contents .= "<br/> <input type=text class='textbox numeric stock_readonly' name='codi_options[".$i."][details][".$j."][".$li['language_code']."_coprice]'  id='".$li['language_code']."_add_option_coprice' style='width:80%;margin-top:3px;' value='".$_value[$j][option_coprice]."' title='".$li[language_name]." 공급가' placeholder='".$li[language_name]." 공급가'>";
            }
        }

        $Contents .= "
					</td>";
        if($_SESSION["admin_config"]["selling_type"] == "W" || $_SESSION["admin_config"]["selling_type"] == "WR"){
            $Contents .= "

					<td>
						<input type=text class='textbox number' name='codi_options[".$i."][details][".$j."][wholesale_listprice]' id='add_option_wholesale_listprice' style='width:80%' value='".$stock_options_detail[$j][option_wholesale_listprice]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' ><br>
						<input type=text class='textbox number' name='codi_options[".$i."][details][".$j."][wholesale_price]' id='add_option_wholesale_price' style='margin:3px;width:80%' value='".$stock_options_detail[$j][option_wholesale_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' >
					</td>";
        }
        if($_SESSION["admin_config"]["selling_type"] == "R" || $_SESSION["admin_config"]["selling_type"] == "WR"){
            $Contents .= "

					<td style='padding:5px;'>
						<input type=text class='textbox numeric' name='codi_options[".$i."][details][".$j."][listprice]' id='add_option_listprice' style='width:42%' value='".$stock_options_detail[$j][option_listprice]."'>
						<input type=text class='textbox numeric' name='codi_options[".$i."][details][".$j."][sellprice]' id='add_option_sellprice' style='width:42%' value='".$stock_options_detail[$j][option_price]."'>";

            if(count($language_list) > 0){
                foreach($language_list as $key => $li){
                    $_name = $li['language_code'].'_stock_options_detail';
                    $_value = $$_name;

                    $Contents .= "<br/> <input type=text class='textbox numeric' name='codi_options[".$i."][details][".$j."][".$li['language_code']."_listprice]' id='".$li['language_code']."_add_option_listprice' style='margin-top:3px;width:42%' value='".$_value[$j][option_listprice]."' title='".$li[language_name]." 정가' placeholder='".$li[language_name]." 정가'>
						<input type=text class='textbox numeric' name='codi_options[".$i."][details][".$j."][".$li['language_code']."_sellprice]' id='".$li['language_code']."_add_option_sellprice' style='margin-top:3px;width:42%' value='".$_value[$j][option_price]."' title='".$li[language_name]." 판매가' placeholder='".$li[language_name]." 판매가'>";
                }
            }

            $Contents .= "
					</td>";
        }
        $Contents .= "
					<td>
						<input type=text class='textbox number' name='codi_options[".$i."][details][".$j."][set_cnt]' id='add_option_set_cnt' style='margin:3px;width:70%' value='".$stock_options_detail[$j][option_etc1]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'  >
					</td>
					<td>
						<input type=checkbox name='codi_options[".$i."][details][".$j."][soldout]' id='add_option_soldout'  value='1' ".($stock_options_detail[$j][option_soldout] == "1" ? "checked":"").">
					</td>

					<td>
						<input type=text class='textbox number' name='codi_options[".$i."][details][".$j."][sell_ing_cnt]' id='add_option_sell_ing_cnt' style='width:30px;margin:0px 3px;'  value='".($mode!="copy"?$stock_options_detail[$j][option_sell_ing_cnt]:"0")."' title='판매진행중 재고' readonly>
						<input type=text class='textbox number stock_readonly' name='codi_options[".$i."][details][".$j."][stock]' id='add_option_stock' style='width:50px;".($_SESSION["layout_config"]["mall_use_inventory"] == "Y" ? "background:#efefef;":"")."' value='".$stock_options_detail[$j][option_stock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' >
					</td>

					<td>
						<input type=text class='textbox number stock_readonly' name='codi_options[".$i."][details][".$j."][safestock]' id='add_option_safestock' style='width:70%' value='".$stock_options_detail[$j][option_safestock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'>
					</td>

					<td>
						<input type=text class='textbox stock_readonly' name='codi_options[".$i."][details][".$j."][code]' id='add_option_code' style='width:30%' value='".$stock_options_detail[$j][option_code]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'>
						<input type=text class='textbox stock_readonly' name='codi_options[".$i."][details][".$j."][gid]' id='add_option_gid' style='width:50%' value='".$stock_options_detail[$j][option_gid]."'>
					</td>

					<td>
						<input type=text class='textbox stock_readonly' name='codi_options[".$i."][details][".$j."][barcode]' id='add_option_barcode' style='width:80%' value='".$stock_options_detail[$j][option_barcode]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'>
					</td>
					<td>
						<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;margin:0px 0px 4px 3px' id='close_btn' ondblclick=\"if($('#codi_options_table_".$i."').find('tr[depth^=1]').length > 1){ \$(this).parent().parent().remove();}else{   if($('.codi_options_table').length > 1){ $(this).parent().parent().parent().parent().remove();}else{ $(this).parent().parent().find('input[type=text]').val(''); } }\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
					</td>
				</tr>";
    }
}

$Contents .= "</table>
			</div>
			<div style='clear:both;height:70px;'></div>";

//코디옵션 추가 이학봉 끝 2014-01-09 이학봉

// 옵션 끝

if($goods_input_type != "globalsellertool"){
    if($admininfo[admin_level] == '9'){
        if($goods_input_type == "inventory"){
            $_Contents = "
	<table cellpadding=0 cellspacing=0><tr><td><b class=blk > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 디스플레이옵션  </b><a name='display'></a></td><td><input type=checkbox name='display_option_display_yn' id='display_option_display_yn' onclick=\"$('#display_option_zone').toggle();\"></td><td><label for='display_option_display_yn'>표시</label></td></tr></table>";
        }else{
            $_Contents = "<img src='../images/dot_org.gif' align=absmiddle style='position:relative;'> <b class=blk>  디스플레이옵션  </b><a name='display'></a>";
        }

        $sql = "select * from shop_product_displayinfo where pid = '".$id."' order by dp_ix asc ";

        $db->query($sql);
        $display_options = $db->fetchall();



        $Contents .="<div ".($goods_input_type == "inventory" ? "style='display:none;'":"")." id='display_option_zone'>
				<table width='100%' cellpadding=0 cellspacing=0>
				<tr height=30>
					<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 cellspacing=0 width=100%><tr> <td style='padding:5px 5px 5px 10px;'><b class=blk  > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 디스플레이옵션  </b><a name='display'></a></td><td align=right style='padding:0px 0px 5px 0px;'><img src='/admin/images/btn_group_open.png' open_src='/admin/images/btn_group_close.png' close_src='/admin/images/btn_group_open.png' alt='Minus' align=absmiddle style='margin-left:5px;' onclick=\"moreDisplay(this, 'div#display_option_zone_area', 'div#display_option_result_zone_area' );\"></td></tr></table>")."</td>
					
				</tr>
				</table>";


        if(count($display_options) > 0){
            $Contents .= "<div id='display_option_result_zone_area' style='padding:20px;border:1px solid silver;margin-bottom:10px;'> </div>";
            $Contents .= "<div ".(($goods_input_type == "inventory" || $goods_input_type == "globalsellertool") ? "style='display:none;'":"")." id='display_option_zone_area'  >";
        }else{
            $Contents .= "<div id='display_option_result_zone_area' style='padding:20px;border:1px solid silver;margin-bottom:10px;'>설정된 디스플레이 옵션정보가 없습니다. </div>";
            $Contents .= "<div  ".(($goods_input_type == "inventory" || $goods_input_type == "globalsellertool") ? "style='display:none;'":"")." id='display_option_zone_area'  style='display:none;' >";
        }


        $Contents .= "
				<a onclick=\"DisplayCopyOption('display_options_input')\" style='cursor:pointer;'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_display_option_detail_add.gif' border=0 align=absmiddle style='margin:0 0 3px 0;'></a> <br>
				<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver id='display_options_input' class='input_table_box display_options_input' opt_idx=0 style='margin-bottom:10px'>
				<col width='8%'>
				<col width='22%'>
				<col width='35%'>
				<col width='35%'>
				<tr height=25 bgcolor='#ffffff' align=center>
					<td bgcolor=\"#efefef\" class=small>사용</td>
					<td bgcolor=\"#efefef\" class=small> 추가정보명 *</td>
					<td bgcolor=\"#efefef\" class=small> 추가정보내용 / 노출(20자) *</td>
					<td bgcolor=\"#efefef\" class=small> 기타설명</td>
				</tr>";

//print_r($options);
        if($db->total && $id!=""){//디스플레이 옵션 수정 kbk 12/06/19
            for($i=0;$i < count($display_options);$i++){

                $Contents .= "
				<tr align='center' bgcolor='#ffffff' depth=1 item=1>
					<td height='30'><input type='hidden' class='dp_ix' name='display_options[".$i."][dp_ix]' id='dp_ix'  value='".$display_options[$i][dp_ix]."' /><input type=checkbox name='display_options[".$i."][dp_use]' id='dp_use' value='1' ".($display_options[$i][dp_use] == "1" ? "checked":"") ."></td>
					<td><input type=text class='textbox' name='display_options[".$i."][dp_title]' id='dp_title' inputid='dp_title' style='width:90%;vertical-align:middle' value='".$display_options[$i][dp_title]."'></td>
					<td><input type=text class='textbox' name='display_options[".$i."][dp_desc]' id='dp_desc' inputid='dp_desc' style='width:90%;vertical-align:middle' value='".$display_options[$i][dp_desc]."'></td>
					<td><input type=text class='textbox' name='display_options[".$i."][dp_etc_desc]'  id='dp_etc_desc'  style='width:85%' value='".$display_options[$i][dp_etc_desc]."'> <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('#display_options_input tbody').find('tr[depth^=1]').length > 1){\$(this).parent().parent().remove();}else{DisplayDeleteOption($(this).parent().parent());}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'> <a onclick=\"DisplayCopyOption('display_options_input')\" style='cursor:pointer;'><img src='../images/i_add.gif' border=0 style='margin:0px 0 0px 0;' align=absmiddle></a></td>
				</tr>";
            }
        }else{

            $Contents .= "
				<tr align='center' bgcolor='#ffffff' depth=1 item=1>
					<td height='30'><input type=checkbox name='display_options[0][dp_use]' id='dp_use' value='1'></td>
					<td><input type=text class='textbox' name='display_options[0][dp_title]' id='dp_title' inputid='dp_title' style='width:90%;vertical-align:middle' value='$dp_title'></td>
					<td><input type=text class='textbox' name='display_options[0][dp_desc]' id='dp_desc' inputid='dp_desc' style='width:90%;vertical-align:middle' value='$dp_desc'></td>
					<td><input type=text class='textbox' name='display_options[0][dp_etc_desc]'  id='dp_etc_desc' style='width:85%' value='$dp_etc_desc'>
						<img src='../images/i_close.gif'  align=absmiddle style='margin:0px 0 0px 0;cursor:pointer;' ondblclick=\"if($('#display_options_input tbody').find('tr[depth^=1]').length > 1){\$(this).parent().parent().remove();}else{DisplayDeleteOption($(this).parent().parent());}\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
						<a onclick=\"DisplayCopyOption('display_options_input')\" style='cursor:pointer;'><img src='../images/i_add.gif' border=0 style='margin:0px 0 0px 0;' align=absmiddle></a> </td>
				</tr>";
        }

        $Contents .= "</table>
			<br>
		</div>
		<div style='clear:both;height:70px;'></div>
		</div>";

    }

    if(false){
        $Contents .="<div ".($goods_input_type == "inventory" ? "style='display:none;'":"")." id='viral_zone'>

					<table width='100%' cellpadding=0 cellspacing=0>
						<tr height=30>
							<td style='padding-bottom:10px;'>
								".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:0px;'><b class=blk style='font-size:15px;' > 바이럴 등록</b> <span class=small > - 추가로 노출하고자 하는 정보를 입력하여 상품 상세페이지에 노출 할 수 있습니다.</span></div>")."
							</td>
						</tr>
					</table>";

        $Contents .= "
					<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver id='virals_input' class='input_table_box virals_input' opt_idx=0 style='margin-bottom:10px'>
						<col width='8%'>
						<col width='22%'>
						<col width='35%'>
						<col width='35%'>
						<tr height=25 bgcolor='#ffffff' align=center>
							<td bgcolor=\"#efefef\" class=small>사용</td>
							<td bgcolor=\"#efefef\" class=small> 카페&블로그명(20자) *</td>
							<td bgcolor=\"#efefef\" class=small> URL *</td>
							<td bgcolor=\"#efefef\" class=small> 기타설명</td>
						</tr>";

        $sql = "select * from shop_product_viralinfo where pid = '".$id."' order by vi_ix asc ";
        $db->query($sql);
        $virals = $db->fetchall();
        for($i=0;($i < count($virals) || $i < 1);$i++){

            $Contents .= "
						<tr align='center' bgcolor='#ffffff' depth=1 item=1>
							<td height='30'><input type='hidden' class='vi_ix' name='virals[".$i."][vi_ix]' id='vi_ix'  value='".$virals[$i][vi_ix]."' /><input type=checkbox name='virals[".$i."][vi_use]' id='vi_use' value='1' ".($virals[$i][vi_use] == "1" ? "checked":"") ."></td>
							<td><input type=text class='textbox' name='virals[".$i."][viral_name]' id='viral_name' inputid='viral_name' style='width:90%;vertical-align:middle' value='".$virals[$i][viral_name]."'></td>
							<td><input type=text class='textbox' name='virals[".$i."][viral_url]' id='viral_url' inputid='viral_url' style='width:90%;vertical-align:middle' value='".$virals[$i][viral_url]."'></td>
							<td><input type=text class='textbox' name='virals[".$i."][viral_desc]'  id='viral_desc'  style='width:85%' value='".$virals[$i][viral_desc]."'> <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('#virals_input tbody').find('tr[depth^=1]').length > 1){\$(this).parent().parent().remove();}else{ViralInfoDelete($(this).parent().parent());}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'> <a onclick=\"ViralInfoCopy('virals_input')\" style='cursor:pointer;'><img src='../images/i_add.gif' border=0 style='margin:0px 0 0px 0;' align=absmiddle></a> </td>
						</tr>";
        }

        $Contents .= "
					</table><br>
					<div style='clear:both;height:70px;'></div>
				</div>";
    }
}
if($goods_input_type != "globalsellertool"){

        $Contents .="<div id='product_relation_zone'>";

        $Contents .="
				<table width='100%' cellpadding=0 cellspacing=0>
				<tr height=30>
					<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:0px;padding-left:0px;'>
					<table cellpadding=0 cellspacing=0 width=100%>
						<tr> <td style='padding:5px 5px 5px 10px;'><b class=blk> <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> Color Chip 1  </b><a name='colorChip1'></a></td>
							<td align=right style='padding:0px 0px 5px 0px;'><img src='/admin/images/btn_group_open.png' open_src='/admin/images/btn_group_close.png' close_src='/admin/images/btn_group_open.png' alt='Minus' align=absmiddle style='margin-left:5px;' onclick=\"moreDisplay(this, 'table#relation_product_zone_area', 'div#relation_product_result_zone_area' );\"></td>
						</tr>
					</table>

						 </div>")."
					</td>
					 
				</tr>
				</table>";

        $Contents .="
				<table border=0 cellpadding=0 cellspacing=0 width='100%'  id='relation_product_zone_area' style='display:;'>
					<tr bgcolor='#ffffff'> 
						<td style='padding:5px;'>
						<div style='padding-bottom:10px;'>
							노출 텍스트 : <input type=text name='relation_text1' class='textbox' value='".$relation_text1."' size=10>";
        if(count($language_list) > 0){
            foreach($language_list as $key => $li){
                $_name = $li['language_code'].'_relation_text1';
                $_value = $$_name;

                $Contents .= " <input type=text name='".$_name."' class='textbox' value='".$_value."' size=10 title='Color Chip 1 노출 텍스트 (".$li[language_name].")' placeholder='".$li[language_name]."'>";
            }
        }

        $Contents .="    </div>
						<div id='goods_manual_area_1'>
							<a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,1,'productList_1');\">
							<img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>

							<div style='width:100%;padding:5px 5px 5px 0px;' id='group_product_area_1' >".relationProductList($id, "clipart")."</div>

							<div style='width:100%;float:left;margin-top:10px;'><span class=small>* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.</span>
							</div>
						</div>";

        $Contents .= "
						</td>
					</tr>
					<tr bgcolor='#F8F9FA'>
						<td colspan=2>
						</td>
					</tr>
				</table><div style='clear:both;height:70px;'></div>";

        $Contents .="
				<table width='100%' cellpadding=0 cellspacing=0>
				<tr height=30>
					<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:0px;padding-left:0px;'>
					<table cellpadding=0 cellspacing=0 width=100%>
						<tr> <td style='padding:5px 5px 5px 10px;'><b class=blk> <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> Color Chip 2  </b><a name='colorChip2'></a></td>
							<td align=right style='padding:0px 0px 5px 0px;'><img src='/admin/images/btn_group_open.png' open_src='/admin/images/btn_group_close.png' close_src='/admin/images/btn_group_open.png' alt='Minus' align=absmiddle style='margin-left:5px;' onclick=\"moreDisplay(this, 'table#relation_product_zone_area2', 'div#relation_product_result_zone_area' );\"></td>
						</tr>
					</table>

						 </div>")."
					</td>
					 
				</tr>
				</table>";

        $Contents .="
				<table border=0 cellpadding=0 cellspacing=0 width='100%'  id='relation_product_zone_area2' style='display:;'>
					<tr bgcolor='#ffffff'> 
						<td style='padding:5px;'>
						<div style='padding-bottom:10px;'>
							노출 텍스트 : <input type=text name='relation_text2' class='textbox' value='".$relation_text2."' size=10>";
        if(count($language_list) > 0){
            foreach($language_list as $key => $li){
                $_name = $li['language_code'].'_relation_text2';
                $_value = $$_name;

                $Contents .= " <input type=text name='".$_name."' class='textbox' value='".$_value."' size=10 title='Color Chip 2 노출 텍스트 (".$li[language_name].")' placeholder='".$li[language_name]."'>";
            }
        }

        $Contents .="    </div>
						<div id='goods_manual_area_2'>
							<a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,2,'productList_2');\">
							<img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>

							<div style='width:100%;padding:5px 5px 5px 0px;' id='group_product_area_2' >".relationProductList2($id, "clipart")."</div>

							<div style='width:100%;float:left;margin-top:10px;'><span class=small>* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.</span>
							</div>
						</div>";

        $Contents .= "
						</td>
					</tr>
					<tr bgcolor='#F8F9FA'>
						<td colspan=2>
						</td>
					</tr>
				</table><div style='clear:both;height:70px;'></div>";

    $Contents .="
				<table width='100%' cellpadding=0 cellspacing=0>
				<tr height=30>
					<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:0px;padding-left:0px;'>
					<table cellpadding=0 cellspacing=0 width=100%>
						<tr> <td style='padding:5px 5px 5px 10px;'><b class=blk> <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 연관상품 등록  </b><a name='addProduct'></a></td>
							<td align=right style='padding:0px 0px 5px 0px;'><img src='/admin/images/btn_group_open.png' open_src='/admin/images/btn_group_close.png' close_src='/admin/images/btn_group_open.png' alt='Minus' align=absmiddle style='margin-left:5px;' onclick=\"moreDisplay(this, 'table#relation_product_zone_area4', 'div#relation_product_result_zone_area' );\"></td>
						</tr>
					</table>

						 </div>")."
					</td>
					 
				</tr>
				</table>";

    $Contents .="
				<table border=0 cellpadding=0 cellspacing=0 width='100%'  id='relation_product_zone_area4' style='display:;'>
					<tr bgcolor='#ffffff'> 
						<td style='padding:5px;'>
						<div id='goods_manual_area_4'>
							<a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,4,'productList_4','clipart','basic');\">
							<img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>

							<div style='width:100%;padding:5px 5px 5px 0px;' id='group_product_area_4' >".addProductList1($id, "clipart")."</div>

							<div style='width:100%;float:left;margin-top:10px;'><span class=small>* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.</span>
							</div>
						</div>";

    $Contents .= "
						</td>
					</tr>
					<tr bgcolor='#F8F9FA'>
						<td colspan=2>
						</td>
					</tr>
				</table><div style='clear:both;height:70px;'></div>";

    $Contents .="
				<table width='100%' cellpadding=0 cellspacing=0>
				<tr height=30>
					<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:0px;padding-left:0px;'>
					<table cellpadding=0 cellspacing=0 width=100%>
						<tr> <td style='padding:5px 5px 5px 10px;'><b class=blk> <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 추가 구성 상품  </b><a name='addProduct'></a></td>
							<td align=right style='padding:0px 0px 5px 0px;'><img src='/admin/images/btn_group_open.png' open_src='/admin/images/btn_group_close.png' close_src='/admin/images/btn_group_open.png' alt='Minus' align=absmiddle style='margin-left:5px;' onclick=\"moreDisplay(this, 'table#relation_product_zone_area3', 'div#relation_product_result_zone_area' );\"></td>
						</tr>
					</table>

						 </div>")."
					</td>
					 
				</tr>
				</table>";

    $Contents .="
				<table border=0 cellpadding=0 cellspacing=0 width='100%'  id='relation_product_zone_area3' style='display:;'>
					<tr bgcolor='#ffffff'> 
						<td style='padding:5px;'>
						<div id='goods_manual_area_3'>
							<a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,3,'productList_3','clipart','basic');\">
							<img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>

							<div style='width:100%;padding:5px 5px 5px 0px;' id='group_product_area_3' >".addProductList($id, "clipart")."</div>

							<div style='width:100%;float:left;margin-top:10px;'><span class=small>* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.</span>
							</div>
						</div>";

    $Contents .= "
						</td>
					</tr>
					<tr bgcolor='#F8F9FA'>
						<td colspan=2>
						</td>
					</tr>
				</table><div style='clear:both;height:70px;'></div>";

        $Contents .= "
			</div>";
}

$Contents .="<div id='product_gift_zone' class='' ".($product_type == "77" ? "style='display:none;'":"").">

		<table width='100%' cellpadding=0 cellspacing=0>
		<tr height=30>
			<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:0px;padding-left:0px;'>
			<table cellpadding=0 cellspacing=0 width=100% >
				<tr> 
				    <td style='padding:5px 5px 5px 10px;'>
				        <b class=blk  > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 사은품 등록  </b><a name='addGift'></a>
				        <span style='padding-left:2px' class='helpcloud' help_width='300' help_height='30' help_html='상품상세페이지에서 해당 상품만이 아닌 연계되어 함께 노출하고 싶은 상품을 지정할 수 있습니다.'><img src='/admin/images/icon_q.gif' align=absmiddle /></span>  &nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
				    
					<td align=right style='padding:0px 0px 5px 0px;'>
					    <img src='/admin/images/btn_group_open.png' open_src='/admin/images/btn_group_close.png' close_src='/admin/images/btn_group_open.png' alt='Minus' align=absmiddle style='margin-left:5px;' onclick=\"moreDisplay(this, 'table#relation_gift_zone_area', 'div#relation_product_result_zone_area' );\"></td>
				</tr>
				
			</table>

				 </div>")."
			</td>
			 
		</tr>
		</table>";

//$Contents .= "<div id='relation_product_result_zone_area' style='padding:20px;border:1px solid silver;margin-bottom:10px;'>설정된 관련상품 정보가 없습니다. </div>";
//
$Contents .="
		<table border=0 cellpadding=0 cellspacing=0 width='100%'  id='relation_gift_zone_area'>
		 
			<tr bgcolor='#ffffff'> 
                <td style='padding:5px;'>
                    <div><span>셀렉트박스 노출갯수</span> &nbsp;&nbsp;<input type='text' id='gift_selectbox_cnt' name='gift_selectbox_cnt' value='".($gift_selectbox_cnt > 0 ? $gift_selectbox_cnt:"0")."' style='width:30px;'/>개</div>
					<div>사은품 선택안함 옵션 사용 여부 : <input type='radio' name='gift_selectbox_nooption_yn' value='Y' ".($gift_selectbox_nooption_yn == 'Y' ? "checked":"")."  id='gift_selectbox_nooption_yn_y'><label for='gift_selectbox_nooption_yn_y'>사용</label>
					<input type='radio' name='gift_selectbox_nooption_yn' value='N' ".($gift_selectbox_nooption_yn == 'N' || $gift_selectbox_nooption_yn == "" ? "checked":"")." id='gift_selectbox_nooption_yn_n'><label for='gift_selectbox_nooption_yn_n'>미사용</label></div><br/>
				<div id='goods_gift_area_1'>
					<a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,1,'gift_1','clipart','77');\">
					<img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>

					<div style='width:100%;padding:5px 5px 5px 0px;' id='group_gift_area_1' >".giftProductList($id, "clipart")."</div>

					<div style='width:100%;float:left;margin-top:10px;'><span class=small>* 더블클릭 시 사은품이 개별 삭제 됩니다.</br>* 사은품 노출순서는 조정할 수 없습니다.</span>
					</div>
				</div>
				</td>
			</tr>
			<tr bgcolor='#F8F9FA'>
				<td colspan=2>
				</td>
			</tr>
		</table><div style='clear:both;height:70px;'></div>
	</div>";

$Contents .="
		

			
			<table width='100%' cellpadding=0 cellspacing=0 class='product_detail_zone'>
				<tr height=30>
					<td style='padding-bottom:10px;' colspan=2>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><b class=blk> <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> 상품상세정보</b><a name='productInfo'></a></div>")."</td>
				</tr>
				<tr>
					<td style='padding-bottom:10px;'>
					<input type=checkbox name='goods_desc_copy' id='goods_desc_copy' value='1' ".( $act == "insert" ? "checked" : "")." ><label for='goods_desc_copy'><b>상품상세 이미지  복사</b></label> <!--(체크시 입력한 URL의 이미지를 현재 서버에 복사해 오게 됩니다. 이미지 호스팅을 이용할 경우 체크 하지 마세요.)--> ".getTransDiscription($page_code,'O')."
					</td>
				</tr>
			</table>

			<table cellpadding=0 cellspacing=1 bgcolor=#ffffff width='100%' class='product_detail_zone'>
				<tr>
					<td align='left' colspan=4 style='padding-bottom:0px;'>
					<div class='tab'>
						<table class='s_org_tab'>
						<tr>
							<td class='tab'>
								<table id='b_tab_01'  class='on'>
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"showTabBasicinfo('web_basicinfo_div','b_tab_01');\">웹용 상품 상세정보</td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='b_tab_02'>
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"showTabBasicinfo('mobile_basicinfo_div','b_tab_02');\">모바일 상품 상세정보</td>
									<th class='box_03'></th>
								</tr >
								</table>";

if(count($language_list) > 0){
    $_num = 3;
    foreach($language_list as $key => $li){
        $Contents .= "<table id='b_tab_0".$_num."'>
        <tr>
            <th class='box_01'></th>
            <td class='box_02' onclick=\"showTabBasicinfo('".$li['language_code']."_web_basicinfo_div','b_tab_0".$_num."');\">".$li['language_name']." 웹용 상품 상세정보</td>
            <th class='box_03'></th>
        </tr >
        </table>
        <table id='b_tab_0".($_num +1)."'>
        <tr>
            <th class='box_01'></th>
            <td class='box_02' onclick=\"showTabBasicinfo('".$li['language_code']."_mobile_basicinfo_div','b_tab_0".($_num +1)."');\">".$li['language_name']." 모바일 상품 상세정보</td>
            <th class='box_03'></th>
        </tr>
        </table>";

        $_num += 2;
    }
}

$Contents .="
							</td>
							<td class='btn'>
							</td>
						</tr>
						</table>
					</div>
					</td>
				</tr>
			</table><br>
				
			<div class='doong product_detail_zone' id='web_basicinfo_div' style='display:block;vertical-align:top;' >
				<table width='100%' border='0' cellspacing='0' cellpadding='0' class='input_table_box'>
					<tr>
						<td height='30' colspan='2'>
						<textarea name=\"basicinfo\" id='basicinfo' style='display:none' >".$basicinfo."</textarea>
						</td>
					</tr>
					</table>

					<table width='100%' border='0' cellspacing='0' cellpadding='0'  >
					<tr>
						<td align=left style='display:none;'>
							".($_SESSION["admininfo"][admin_level] == 9 ? "<label for='watermark_desc' style='font-weight:bold'> 워터마크 생성</label>
							<input type=checkbox name='watermark_desc' id='watermark_desc' value=1 >":"")."
						</td>
						<td colspan='2' align='right' class='input_box_item' style='text-align:right;'>&nbsp;</td>
					</tr>
				    </table>
			</div>

			<div class='doong product_detail_zone' id='mobile_basicinfo_div' style='display:none;vertical-align:top;' >
				<!--table width='100%' cellpadding=0 cellspacing=0>
					<tr height=30>
						<td style='padding-bottom:10px;' colspan=2>
							".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 상품상세정보</b></div>")."
						</td>
					</tr>
					<tr>
						<td style='padding-bottom:10px;'>
						<input type=checkbox name='m_goods_desc_copy' id='m_goods_desc_copy' value='1' ".( $act == "insert" ? "checked" : "")."><label for='m_goods_desc_copy'><b>상품상세 이미지  복사</b></label> (체크시 입력한 URL의 이미지를 현재 서버에 복사해 오게 됩니다. 이미지 호스팅을 이용할 경우 체크 하지 마세요.) ".getTransDiscription($page_code,'O')."
						</td>
					</tr>
				</table-->
					
				<table width='100%' border='0' cellspacing='0' cellpadding='0' class='input_table_box'>
					<tr>
						<td height='30' colspan='2'>
						<textarea name=\"m_basicinfo\" id='m_basicinfo' style='display:none' >".$m_basicinfo."</textarea>
						</td>
					</tr>
				</table>

				<table width='100%' border='0' cellspacing='0' cellpadding='0' >
					<tr>
						<td align=left>
							".($_SESSION["admininfo"][admin_level] == 9 ? "<label for='watermark_desc' style='font-weight:bold'> 워터마크 생성</label>
							<input type=checkbox name='m_watermark_desc' id='m_watermark_desc' value=1 >":"")."
						</td>
						<td colspan='2' align='right' class='input_box_item' style='text-align:right;'>&nbsp;</td>
					</tr>
				</table>
				<div style='clear:both;height:70px;'></div>
			</div>
			";

if(count($language_list) > 0){
    foreach($language_list as $key => $li){

        $_name_w = $li['language_code'].'_basicinfo';
        $_value_w = $$_name_w;

        $_name_m = $li['language_code'].'_m_basicinfo';
        $_value_m = $$_name_m;

        $Contents .= "<div class='doong product_detail_zone' id='".$li['language_code']."_web_basicinfo_div' style='display:none;vertical-align:top;' >
				<table width='100%' border='0' cellspacing='0' cellpadding='0' class='input_table_box'>
					<tr>
						<td height='30' colspan='2'>
						<textarea name=\"".$_name_w."\" id='".$_name_w."' style='display:none' >".$_value_w."</textarea>
						</td>
					</tr>
					</table>
					<div style='clear:both;height:70px;'></div>
			</div>
			<div class='doong product_detail_zone' id='".$li['language_code']."_mobile_basicinfo_div' style='display:none;vertical-align:top;' >
				<table width='100%' border='0' cellspacing='0' cellpadding='0' class='input_table_box'>
					<tr>
						<td height='30' colspan='2'>
						<textarea name=\"".$_name_m."\" id='".$_name_m."' style='display:none' >".$_value_m."</textarea>
						</td>
					</tr>
				</table>
				<div style='clear:both;height:70px;'></div>
			</div>";

        $_num += 2;
    }
}

if ($id != ""){
    $img_view_style = "";
}else{
    $img_view_style = " style='display:none;''"	;
}


$image_db = new Database;
$image_db->query("select * from shop_image_resizeinfo order by idx");
$image_info = $image_db->fetchall();

/*
$Contents = $Contents."
			<table cellpadding=0 cellspacing=1 bgcolor=#ffffff width='100%' class='product_detail_zone'>
				<tr>
					<td align='left' colspan=4 style='padding-bottom:0px;'>
					<div class='tab'>
						<table class='s_org_tab'>
						<tr>
							<td class='tab'>
								<table id='image_typetab_01'  class='on'>
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"showTabImageInfo('image_type_01','image_typetab_01');\">정사각형 이미지 정보(기본) - 제휴판매시 사용</td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='image_typetab_02'>
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"showTabImageInfo('image_type_02','image_typetab_02');\">직사각형 이미지 정보</td>
									<th class='box_03'></th>
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
			</table><br>";
*/
$Contents = $Contents."<div id='image_type_01'>".getImageUploadHtmlNew($id, "", "<!--정사각형-->",  $image_info, $product_info)."</div>";
//$Contents = $Contents."<div id='image_type_02' style='display:none;'>".getImageUploadHtml($id, "_rectangular","직사각형", $image_info, $product_info)."</div>";

if($id !=""){//상품정보 수정 히스토리 수정페이지서만 노출함 2014-04-09 이학봉
    $Contents .="
			<table width='100%' cellpadding=0 cellspacing=0 border=0>
			<tr height=25>
				<td style='padding-bottom:3px;'>
					".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:0px;'><b class=blk style='font-size:15px;'> 상품수정 정보</b> <!--span class=small > <img src='/admin/webedit/image/wtool6_1.gif' align=absmiddle> 이미지를 클릭해서 상품상세 이미지를 등록해주세요</span--></div>")."
				</td>
			</tr>
			</table>";
    $Contents .= "
			<table width=100%  opt_idx=0 cellspacing=0 cellpadding=0 class='input_table_box'>
			<!--col width='15%'-->
			<col width='*'>
			<tr height=100>
				<!--td class='input_box_title'><b>상품 수정 정보</b></td-->
				<td class='input_box_item'>
					<div  style='width:98%;height:185px;padding:6px;margin:5px;line-height:140%;overflow:auto' >".nl2br(Product_edit_history_Text($id))."</div>
				</td>
			</tr>
			</table>";
}

/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' style='line-height:120%' >
	<col width=8>
	<col width=*>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>500×500 이미지</b>를 등록한후 이미지 복사를 클릭하시고 저장하시면 <u>300×300, 200×200, 100×100, 50×50</u> 이미지가 자동으로 생성됩니다</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' >별도의 이미지 복사를 원하시면 이미지복사 체크를 푸신 상태에서 원하시는 이미지를 찾아서 등록하시면 됩니다</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' style='line-height:120%'>상품정보의 일괄 관리를 위해서 상품상세 이미지의 경우 외부사이트에서 HTML을 복사해서 넣은경우 복사해온 <u>서버측에서 외부사이트 링크가 허용된 경우</u> <b>이미지가 자동</b>으로 복사되게 됩니다</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' style='line-height:120%' >상품 상세정보페이지에 다른 사이트에 있는 이미지를 붙여 넣고 싶은경우 다른 사이트에 있는 이미지를 드래그해서 넣으면 자동으로 복사 됩니다<br>단, 이미지 원본측에 이미지 복사가 허용된 경우에 한함</td></tr>
</table>
";
*/
//$help_text =  getTransDiscription($page_code,'R');

//$Contents .= HelpBox("개별상품등록", $help_text);



$Contents = $Contents."
			<table width='100%'>
			<tr height=30 align=left><td width=500 align='left'>";
if($id){
    $Contents = $Contents."
				<a href='".$client_host."' target=_blank><img src='../v3/images/btn/bt_preview.png' border=0 align=absmiddle style='cursor:pointer'></a>";
}
$Contents = $Contents."
			</td>";

if ($id == "" || $mode == "copy"){
    $Contents .= "<td align=right>";
    if(checkMenuAuth($page_code,"C")){
        $Contents .= "<span class='helpcloud' help_height='30' help_html='상품정보 저장후 현재 페이지가 유지됩니다.'><img src='../v3/images/btn/bt_tempsave.png' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'tmp_insert');\"></span> ";
        $Contents .= "<img src='../v3/images/btn/bt_save.png' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'insert');\">";
    }
    $Contents .= "</td>";
}else{
    $Contents .= "<td align=right>";
    if(checkMenuAuth($page_code,"U")){
        $Contents .= "<span class='helpcloud' help_height='30' help_html='상품정보 저장후 현재 페이지가 유지됩니다.'><img src='../v3/images/btn/bt_tempsave.png' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'tmp_update');\"></span> ";
        $Contents .= "<img src='../v3/images/btn/bt_save.png' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'update')\"> ";
    }
    if(checkMenuAuth($page_code,"D") && $state==9){
        $Contents .= "<img src='../images/".$_SESSION["admininfo"]["language"]."/b_del.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'delete')\">";
    }
    $Contents .= "</td>";
}
$Contents .= "

			</td></tr>
			</table>
			</form>";
if($_relation_view_type =="small"){
    $ajax_add_string = "tag:'img',overlap:'horizontal',constraint:false, ";
}

if ($id && $mode != "copy"){
    $Contents .= "
<script type='text/javascript'>
/*
Sortable.create('sortlist',
{
	$ajax_add_string
	onUpdate: function()
	{
		//alert(Sortable.serialize('sortlist'));
		new Ajax.Request('/admin/product/product_input.act.php',
		{
			method: 'POST',
			parameters: Sortable.serialize('sortlist')+'&act=vieworder_update&pid=$id',
			onComplete: function(transport){
			//alert(transport.responseText);
			}
		});
	}
});
*/
</script>";
}


$Script = "
<style>
.targetMenu {margin-top:10px;}
.targetMenu > a {padding:3px 2px; margin-right:4px; border:1px solid #ccc;font-weight:bold; float:left;text-decoration:none;}
.targetMenu > a:last-child {margin-right:0px;}
.selected_set_goods{float:left;width:140px;height:30px;border:1px solid silver;background-color:#efefef;padding:5px 5px 5px 0px;margin:3px;cursor:pointer;text-align:center;color:#000000;font-weight:bold;}
.selected_set_goods_detail{float:left;width:140px;height:30px;border:1px solid silver;background-color:#efefef;padding:5px 5px 5px 0px;margin:3px;cursor:pointer;text-align:center;}
.hide_class {display:none;}
</style>
<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n
<script Language='JavaScript' src='../include/zoom.js'></script>
<script Language='JavaScript' src='../product/addoption.js'></script>
<script language='JavaScript' src='../js/dd.js'></script>
<!--script language='JavaScript' src='../js/mozInnerHTML.js'></script-->
<!--script type='text/javascript' src='../marketting/relationAjaxForEvent.js'></script-->
<script Language='JavaScript' src='../include/DateSelect.js'></script>
<script Language='JavaScript' src='../product/goods_input.js?v=".rand()."'></script>
<script Language='JavaScript' src='../product/goods_input.special.js'></script>
<script Language='JavaScript' src='../product/buyingService.js'></script>
<script language='javascript' src='/admin/js/jquery.form.js'></script>
<script language='javascript' src='/admin/js/jquery.form.min.js'></script>
<script Language='JavaScript' src='../product/goods_mandatory_info.js'></script>\n$Script
<script language='JavaScript'>
function substitudeRate(){
	var total = new Number($('#substitude_total').val());
	var rate = new Number($('#substitude_seller').val());
	var substitud = Math.floor((total-rate)*10) / 10;
	$('#substitude_rate').val(substitud);
}
$('document').ready(function (){

	//셀러일경우 수수료 정보를 수정못하도록 disabled 처리함 2014-04-07 이학봉 시작
	var com_type = '".$admininfo[com_type]."';
	var company_id = $('#company_id').val();
	var pid = $('input[name=id]').val();
	var one_commission_use = $('input[name^=one_commission]:checked').val();

	if(com_type == 'S'){
		$('input[name=one_commission]').attr('disabled',true);
		$('input[name=account_type]').attr('disabled',true);
		$('input[id=commission]').attr('readonly',true);
		$('input[id=wholesale_commission]').attr('readonly',true);
	}
	//셀러일경우 수수료 정보를 수정못하도록 disabled 처리함 2014-04-07 이학봉 끝

	//수수료정보 불러오기 2014-04-07 이학봉
	//getSellerSetup(company_id,one_commission_use,pid);
	
	var is_sell_date = '".$is_sell_date."';
	
	if(is_sell_date == '1'){
	    $('#is_sell_date_1').attr('checked',true);
	    $('*[id^=sell_priod]').attr('disabled',false);
	    $('td.sell_proid_area').show();
	}

	$('input[name=account_type]').click(function (){
		var company_id = $('#company_id').val();
		var pid = $('input[name=id]').val();
		var one_commission_use = $('input[name^=one_commission]:checked').val();
		var value = $(this).val();

		if(value == '2'){
			$('#commission').val('0');
			$('#wholesale_commission').val('0');
		}else{
			//getSellerSetup(company_id,one_commission_use,pid);
		}
	});

	var delivery_policy = $('input[name^=delivery_policy]:checked').val();
	deliveryTypeView(delivery_policy);
	
	commissionChange(document.product_input);	//수수료정보 뿌려주기 2014-05-23 이학봉


	$('.service_type').click(function(){
		$('.service_type').attr('checked',false);
		$(this).attr('checked',true);
	});

	////alert($('input[name=listprice]').val());
});

//LargeImageView();



</script>";


$Script .= $AddScript;


//21030830 Hong
$product_type_array=array("0"=>"GoodsInfo","1"=>"buyingServiceInfo","2"=>"buyingServiceInfo","88"=>"planGoodsInfo","99"=>"setGoodsInfo","21"=>"subscription","31"=>"local_delivery","77"=>"freeGift","12"=>"group_goods","55"=>"sos");

if($mmode == "pop"){
    $P = new ManagePopLayOut();
    $P->addScript = $Script;

    if ($id != ""){
        if ($_SESSION["admininfo"][admin_level] == 9){
            $P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);stockCheck('".$stock_use_yn."');ShowGoodsTypeInfo('".$product_type_array[$product_type]."','Y');showTabBasicinfo('web_basicinfo_div','b_tab_01');";//kbk 11/11/08
        }else{
            $P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);stockCheck('".$stock_use_yn."');ShowGoodsTypeInfo('".$product_type_array[$product_type]."','Y');showTabBasicinfo('web_basicinfo_div','b_tab_01');";//kbk 11/11/08
        }
    }else{
        if($goods_input_type == "social"){
            $P->OnloadFunction = "init();stockCheck('Y');ShowGoodsTypeInfo('scGoodsInfo');showTabBasicinfo('web_basicinfo_div','b_tab_01');";
        }else{
            if ($_SESSION["admininfo"][admin_level] == 9){
                $P->OnloadFunction = "init();stockCheck('Q');ShowGoodsTypeInfo('GoodsInfo');showTabBasicinfo('web_basicinfo_div','b_tab_01');";
            }else{
                $P->OnloadFunction = "init();stockCheck('Q');ShowGoodsTypeInfo('GoodsInfo');showTabBasicinfo('web_basicinfo_div','b_tab_01');";
            }
        }
    }
    if($goods_input_type == "social"){
        $P->strLeftMenu = sns_menu();
    }else{
        $P->strLeftMenu = product_menu();
    }
    $P->strContents = $Contents;
    $P->Navigation = "상품관리 > 상품등록 > 개별상품등록";
    $P->NaviTitle = "개별상품등록";
    echo $P->PrintLayOut();
}else{
    $P = new LayOut();
    $P->addScript = $Script;
    if ($id != ""){
        if ($_SESSION["admininfo"][admin_level] == 9){
            $P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);stockCheck('".$stock_use_yn."');ShowGoodsTypeInfo('".$product_type_array[$product_type]."','Y');showTabBasicinfo('web_basicinfo_div','b_tab_01');";//kbk 11/11/08
        }else{

            $P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);stockCheck('".$stock_use_yn."');ShowGoodsTypeInfo('".$product_type_array[$product_type]."','Y');showTabBasicinfo('web_basicinfo_div','b_tab_01');";//kbk 11/11/08
        }

    }else{
        if($goods_input_type == "social"){
            $P->OnloadFunction = "init();stockCheck('Y');ShowGoodsTypeInfo('scGoodsInfo');showTabBasicinfo('web_basicinfo_div','b_tab_01');";
        }else{
            if ($_SESSION["admininfo"][admin_level] == 9){
                $P->OnloadFunction = "init();stockCheck('Y');ShowGoodsTypeInfo('GoodsInfo');showTabBasicinfo('web_basicinfo_div','b_tab_01');";
            }else{
                $P->OnloadFunction = "init();stockCheck('Q');ShowGoodsTypeInfo('GoodsInfo');showTabBasicinfo('web_basicinfo_div','b_tab_01');";
            }
        }
    }

    if($goods_input_type == "inventory"){
        $P->strLeftMenu = inventory_menu();
        $P->Navigation = "재고관리 > 재고상품등록";
        $P->title = "재고상품등록";
    }else if($goods_input_type == "sellertool"){
        $P->strLeftMenu = sellertool_menu();//sellertool_menu();
        $P->Navigation = "쇼핑몰통합관리 > 상품등록/수정";
        $P->title = "상품등록/수정";
    }else if($goods_input_type == "globalsellertool"){
        $P->strLeftMenu = sellertool_menu();//sellertool_menu();
        $P->Navigation = "쇼핑몰통합관리 > 상품등록/수정";
        $P->title = "상품등록/수정";

    }else if($goods_input_type == "social"){
        $P->strLeftMenu = sns_menu();
        $P->Navigation = "소셜커머스 > 상품등록관리";
        $P->title = "상품등록관리";
    }else{
        $P->strLeftMenu = product_menu();
        $P->Navigation = "상품관리 > 상품등록 > 개별상품등록";
        $P->title = "개별상품등록";
    }

    $P->strContents = $Contents;
    echo $P->PrintLayOut();
}



function getImageUploadHtml($id, $type="", $type_text="", $image_info="", $product_info=""){
    $mstring = "
			<div>
			<table width='100%' cellpadding=0 cellspacing=0>
			<tr height=30>
				<td style='padding-bottom:10px;'>
					".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><b class=blk > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> ".$type_text." 이미지 추가</b> <a name='image'></a><span class=small >   ".$image_info[0][width]."*".$image_info[0][height]." <!--이미지를 등록하시면 체크된 작은 이미지는 자동으로 등록됩니다.--> ".getTransDiscription($page_code,'X')."</span>

					".(($_SESSION["admininfo"]["admin_level"] > 8 && $type == "") ? "<a href=\"javascript:PoPWindow('../product/product_resize.php?mmode=pop',560,600,'brand')\"'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_pop_image.gif' border=0></a>" : "")."
					
					<br> <span style='line-height:140%; margin-left:60px;'><!--* 위 아래로 긴 이미지를 사용하실 경우 가로 사이즈만 맞춰주시면 자동으로 조정되어 집니다.--> ".getTransDiscription($page_code,'P')."</div>")." </span>
				</td>
			</tr>
			</table>

			<table border=0 cellpadding=5 cellspacing=0 bgcolor=#ffffff width=100% style='border-top:2px solid #ff2a32;'><!-- class='input_table_box'-->
			<col width=13%> 
			<col width=*>
			<col width=3%>
			<tr bgcolor='#ffffff' height=30 style='border-bottom:1px solid silver;'>
				<td class='input_box_title'  nowrap  >
					 ".$image_info[0][width]."*".$image_info[0][height]." * <span style='font-size:11px;'>확대이미지</span><br>
					<b>이미지복사</b><input type=checkbox name='chk_allimg".$type."' value=1 id='copy_allimg".$type."' inputid='copy_allimg".$type."' onclick='copyImageCheckAll();' checked><br>
					<!--".($_SESSION["admininfo"][admin_level] == 9 ? "<b> deepzoom 생성</b><input type=checkbox name='chk_deepzoom' value=1  ><br>":"")."-->";
    if(false){
        $mstring = $mstring."	
					".($_SESSION["admininfo"][admin_level] == 9 ? "<b> 워터마크 생성</b><input type=checkbox name='watermark' value=1 ><span style='padding-left:2px' class='helpcloud' help_width='450' help_height='50' help_html='썸네일과 상품의 상세페이지에 등록된 이미지에 불법적인 다운로드를 막기위해 이미지 위에 워터마크를 생성할 수 있습니다. 단, 이미지 등록 확장자는 반드시 PNG로 등록해 주셔야 합니다.'><img src='/admin/images/icon_q.gif' align=absmiddle  /></span>":"")."";
    }
    $mstring = $mstring."	
				</td>
				<td class='small'  >
					<table border=0>
						<col width='405px'>
						<col width='100px'>
						<col width='*'>
						<tr>
							<td><input type=file name='allimg".$type."' class='textbox' size=25 style='font-size:8pt'></td>
							<td ".$img_view_style." rowspan=2><a class='screenshot'  rel='".PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $id, "b" ,$product_info,"shop", $type)."'><img src='../v3/images/btn/bt_preview.png'   style='cursor:pointer'></a>
							</td>
							<td rowspan=2>
							
							</td>
							<td rowspan=2>";
    if($type == "" && false){
        $mstring = $mstring."	
							<img src='../images/".$_SESSION["admininfo"]["language"]."/btn_addimage_add.gif' border=0 align=absmiddle style='margin:0 0 3px 0;' onclick=\"moreDisplay(this, 'div.add_image');\">";
    }
    $mstring = $mstring."	
							</td>
						</tr>
						<tr height=10><td colspan= class='small' style='padding-top:5px;'>※ ".$image_info[0][width]."*".$image_info[0][height]." <!--이미지 복사를 클릭하시면 나머지 이미지가 복사됩니다.-->".getTransDiscription($page_code,'Y')."</td></tr>
					</table>
				</td>";
    /*
    $mstring = $mstring."
                    <td rowspan=6 class='input_box_item' style='padding:20px;border:0px solid silver;text-align:center;' align=center valign=middle id='viewimg".$type."'>
                    ";
                //echo PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $id, "m",$product_info);
                //if(file_exists($DOCUMENT_ROOT.PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $id, "m",$product_info))||$image_hosting_type == "ftp"){
                    $mstring = $mstring."<img src='".PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $id, "m" ,$product_info)."' onerror=\"this.src='../images/noimage_152_148.gif'\" style='border:1px solid silver;display:none;'  id=chimg>";
                //}else{
                //	$mstring = $mstring."<img src='../images/noimage_152_148.gif' style='border:1px solid silver' id=chimg>";
                //}
                    $mstring = $mstring."</td>";
    */
    $mstring = $mstring."	
				<td ><img src='/admin/images/btn_group_open.png' open_src='/admin/images/btn_group_close.png' close_src='/admin/images/btn_group_open.png' alt='Minus'   style='margin-left:5px;' onclick=\"moreDisplay(this, 'tr.basic_image_reg".$type."');\"></td>
			</tr>
			<tr bgcolor='#ffffff' height=50 class='basic_image_reg".$type."' style='border-bottom:1px solid silver;display:none;'>
				<td class='input_box_title'  nowrap >
					 ".$image_info[1][width]."*".$image_info[1][height]." * <span style='font-size:11px;'>상세이미지</span><br>이미지복사
					<input type=checkbox name='chk_mimg".$type."' id='copy_img".$type."' inputid='copy_img".$type."' value=1 checked>
				</td>
				<td class='input_box_item'>
					<table>
						<col width='400px'>
						<col width='100px'>
						<col width='*'>
						<tr>
							<td><input type=file name='mimg".$type."' class='textbox' size=25 style='font-size:8pt'></td>
							<td ".$img_view_style."><a class='screenshot'  rel='".PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $id, "m" ,$product_info,"shop", $type)."'><img src='../v3/images/btn/bt_preview.png' ><!--onclick=\"ChnageImg('".PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $id, "m",$product_info, $type)."','".$type."');\" style='cursor:pointer'--></a></td>
						</tr>
					</table>
				</td>
				<td></td>
			</tr>
			<tr bgcolor='#ffffff' height=50  class='basic_image_reg".$type."' style='border-bottom:1px solid silver;display:none;'>
				<td class='input_box_title'  nowrap >
					 ".$image_info[2][width]."*".$image_info[2][height]." * <span style='font-size:11px;'>리스트이미지</span><br>
					이미지복사<input type=checkbox name='chk_msimg".$type."' id='copy_img".$type."' inputid='copy_img".$type."' value=1 checked>
				</td>
				<td class='input_box_item'>
					<table>
						<col width='400px'>
						<col width='100px'>
						<col width='*'>
						<tr>
							<td><input type=file name='msimg".$type."' class='textbox' size=25 style='font-size:8pt'></td>
							<td ".$img_view_style."><a class='screenshot'  rel='".PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $id, "ms" ,$product_info,"shop", $type)."'><img src='../v3/images/btn/bt_preview.png' ></a><!--onclick=\"ChnageImg('".PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $id, "ms",$product_info, $type)."','".$type."');\" style='cursor:pointer'--></td>
						</tr>
					</table>
				</td>
				<td></td>
			</tr>
			<tr bgcolor='#ffffff' height=50  class='basic_image_reg".$type."' style='border-bottom:1px solid silver;display:none;'>
				<td class='input_box_title'  nowrap>
					  ".$image_info[3][width]."*".$image_info[3][height]." * <span style='font-size:11px;'>리스트작은이미지</span><br>
					이미지복사<input type=checkbox name='chk_simg".$type."' id='copy_img".$type."' inputid='copy_img".$type."' value=1 checked>
				</td>
				<td class='input_box_item'>
					<table>
						<col width='400px'>
						<col width='100px'>
						<col width='*'>
						<tr>
							<td><input type=file name='simg".$type."' class='textbox' size=25 style='font-size:8pt'></td>
							<td ".$img_view_style."><a class='screenshot'  rel='".PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $id, "s" ,$product_info,"shop", $type)."'><img src='../v3/images/btn/bt_preview.png' ></a><!--onclick=\"ChnageImg('".PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $id, "s",$product_info, $type)."','".$type."');\" style='cursor:pointer'--></td>
						</tr>
					</table>
				</td>
				<td></td>
			</tr>
			<tr bgcolor='#ffffff' height=50  class='basic_image_reg".$type."' style='border-bottom:1px solid silver;display:none;'>
				<td class='input_box_title'  nowrap>
					 ".$image_info[4][width]."*".$image_info[4][height]." * <span style='font-size:11px;'>썸네일이미지</span><br>
					이미지복사<input type=checkbox name='chk_cimg".$type."' id='copy_img".$type."' inputid='copy_img".$type."' value=1 checked>
				</td>
				<td class='input_box_item' >
					<table>
						<col width='400px'>
						<col width='100px'>
						<col width='*'>
						<tr>
							<td><input type=file name='cimg".$type."' class='textbox' size=25 style='font-size:8pt'></td>
							<td ".$img_view_style."><a class='screenshot'  rel='".PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $id, "c" ,$product_info,"shop", $type)."'><img src='../v3/images/btn/bt_preview.png'></a><!-- onclick=\"ChnageImg('".PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $id, "c",$product_info, $type)."','".$type."');\" style='cursor:pointer'--></td>
						</tr>
					</table>
				</td>
				<td></td>
			</tr>
			
			<!--
			<tr bgcolor='#ffffff' height=50  class='basic_image_reg".$type."' style='border-bottom:1px solid silver;display:none;'>
				<td class='input_box_title'  nowrap>
					개별 등록 이미지 </br> 580*308 * <span style='font-size:11px;'> / APP 배너 이미지 /</span></br><span style='font-size:11px;'>딜이나 상위 상품 노출 시 필수 등록</span>
				</td>
				<td class='input_box_item' >
					<table>
						<col width='400px'>
						<col width='100px'>
						<col width='*'>
						<tr>
							<td><input type=file name='appimg".$type."' class='textbox' size=25 style='font-size:8pt'></td>
							<td ".$img_view_style."><img src='../v3/images/btn/bt_preview.png' onclick=\"ChnageImg('".PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $id, "app",$product_info, $type)."','".$type."');\" style='cursor:pointer'></td>
						</tr>
					</table>
				</td>
				<td></td>
			</tr>
			-->
			<tr bgcolor='#ffffff' height=50  class='basic_image_reg".$type."' style='border-bottom:1px solid silver;display:none;'>
				<td class='input_box_title'  nowrap>
					패턴 이미지 </br> 75*45
				</td>
				<td class='input_box_item' >
					<table>
						<col width='400px'>
						<col width='100px'>
						<col width='*'>
						<tr>
							<td><input type=file name='filter".$type."' class='textbox' size=25 style='font-size:8pt'></td>
							<td ".$img_view_style.">
							<a class='screenshot'  rel='".PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $id, "filter" ,$product_info,"shop", $type)."'>
							<img src='../v3/images/btn/bt_preview.png' style='cursor:pointer'>
							</a>
							</td>
						</tr>
					</table>
				</td>
				<td></td>
			</tr>
			";
    if($type == "" && false){
        $mstring = $mstring."	
				<tr bgcolor='#ffffff' height=50  class='basic_image_reg".$type."' style='border-bottom:1px solid silver;display:none;'>
					<td class='input_box_title'  nowrap>
					 이미지 URL <br>
					</td>
					<td class='input_box_item' colspan=2 style='padding:10px 5px;'>
					<input type=checkbox name='img_url_copy'".$type." id='img_url_copy".$type."' value=1 ".($mode=='copy' && $bimg_text !='' ? "checked":"")."> <label for='img_url_copy' >URL 이미지복사</label> <a class='screenshot'  rel='".$bimg_text."'><img src='../v3/images/btn/bt_preview.png'></a><!-- onclick=\"ChnageImg(document.getElementById('bimg_text').value);\" style='cursor:pointer' align=absmiddle-->
					<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_mobile_upload_image.gif' align=absmiddle onclick=\"PoPWindow('./mobile_upload.php',900,880,'btn_mobile_upload_image')\"  style='cursor:pointer;'>
					<br>
					<input type=text name='bimg_text".$type."' id='bimg_text".$type."' class='textbox' style='width:750px;font-size:8pt;margin:3px;' value='".$bimg_text."'>
					<div class=small> <!--URL 이미지복사를 체크하시면 입력된 이미지 URL 정보를 바탕으로 이미지가 복사됩니다. 단 해당이미지 서버에서 이미지 복사를 차단한 경우는 이미지 복사가 거부될 수 있습니다.--> ".getTransDiscription($page_code,'Q')." </div>
					</td>
					<td></td>
				</tr>";
    }
    $mstring = $mstring."	
				
			</table>";
    if($type == ""){
        /*
        $mstring = $mstring."
                <div  style='height:25px;background-color:#ffffff;text-align:center;padding-top:12px;border:1px solid silver;margin-top:3px;' onclick=\"moreDisplay(this, 'div.add_image');\">상세이미지 추가 <img src='../v3/images/btn/bt_arrow_down.png' align=absmiddle></div><div style='clear:both;height:70px;'></div>";
                */
    }

    $mstring = $mstring."	
			</div><div style='clear:both;height:70px;'></div>";


    return $mstring;
}


function getImageUploadHtmlNew($id, $type="", $type_text="", $image_info="", $product_info=""){

    $db = new Database;

	$Pid = zerofill($id);
	$imgdir = UploadDirText($_SESSION["admin_config"]["mall_data_root"]."/images/productNew", $Pid);
	$imgpath = $_SESSION["admin_config"]["mall_data_root"]."/images/productNew".$imgdir;
	$porductImg = "";

    $sql = "select * from shop_addimage_new where pid = '".$id."' order by sort asc ";
    $db->query($sql);

    if(!$db->total){
        if (file_exists($imgpath)) {
            $handle  = opendir($imgpath); // 디렉토리 open

            $files = array();
            $eleCount = 1;

            // 디렉토리의 파일을 저장
            while (false !== ($filename = readdir($handle))) {
                // 파일인 경우만 목록에 추가한다.
                if(is_file($imgpath . "/" . $filename)){
                    $files[] = $filename;

                }
            }
            closedir($handle); // 디렉토리 close

            sort($files);

            foreach ($files as $f) { // 파일명 출력
                $imgCom = explode("_",$f);

                if(strlen($imgCom[2]) == 14){
                    $imgComName = $imgCom[1]."_".$imgCom[2];
                }else{
                    $imgComName = $imgCom[1];
                }

                $porductImg .= "<input type=hidden name=imgComName value='".$imgComName."' />";

                $porductImg .= '<li id=li_productImage_'.$eleCount.' vieworder='.$eleCount.' viewcnt='.$eleCount.' style=float:left;width:110px;>';
                $porductImg .= '<table width=95% cellspacing=1 cellpadding=0 style=table-layout:fixed;height:180px;>';
                $porductImg .= '<tr><td class=small style=background-color:gray;color:#ffffff;height:25%;width:100%;text-align:center; nowrap>'.$eleCount.' 이미지</td></tr>';
                $porductImg .= "<tr><td><img src='".$imgpath."/".$f."' width=100px height=100px>";
                $porductImg .= "<input type=hidden name=imgName[] id='imgName_".$eleCount."' value='".$f."' />";
                $porductImg .= '<input type=hidden name=imgTemp[] id=imgTemp_'.$eleCount.' value='.$imgpath.' />';
                $porductImg .= '</td></tr>';
                $porductImg .= '<tr><td align=center><button type=button onclick=ingDel('.$eleCount.')>삭제</td></tr>';
                $porductImg .= '</table>';
                $porductImg .= '</li>';
                $eleCount++;
            }
        }
    }else{
        echo "BBB";
    }


    $mstring = "
			<div>
			<table width='100%' cellpadding=0 cellspacing=0>
			<tr height=30>
				<td style='padding-bottom:10px;'>
					".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><b class=blk > <img src='../images/dot_org.gif' align='absmiddle' style='position:relative;'> ".$type_text." 이미지 추가</b> <a name='image'></a><span class=small >   ".$image_info[0][width]."*".$image_info[0][height]." <!--이미지를 등록하시면 체크된 작은 이미지는 자동으로 등록됩니다.--> ".getTransDiscription($page_code,'X')."</span>

					".(($_SESSION["admininfo"]["admin_level"] > 8 && $type == "") ? "<a href=\"javascript:PoPWindow('../product/product_resize.php?mmode=pop',560,600,'brand')\"'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_pop_image.gif' border=0></a>" : "")."
					
					<br> <span style='line-height:140%; margin-left:60px;'><!--* 위 아래로 긴 이미지를 사용하실 경우 가로 사이즈만 맞춰주시면 자동으로 조정되어 집니다.--> ".getTransDiscription($page_code,'P')."</div>")." </span>
				</td>
			</tr>
			</table>

			<table border=0 cellpadding=5 cellspacing=0 bgcolor=#ffffff width=100% style='border-top:2px solid #ff2a32;'>
			<col width=13%> 
			<col width=*>
				<tr bgcolor='#ffffff' height=30 style='border-bottom:1px solid silver;'>
					<td class='input_box_title'  nowrap>상품이미지등록</td>
					<td class='small'>
						<table border=0 style=width:100%;>
							<input type='hidden' id='imgCnt'>
							<col width='225px'>
							<col width='*'>
							<tr>
								<td><input type=file name='productImg' id='productImg' class='textbox' size=25 style='font-size:8pt'></td>
								<td><button type='button' onclick='imgAdd(this.form)'>이미지추가</button></td>
							</tr>
							<tr>
								<td colspan=2><input type='checkbox' name='imgInsYN' id='imgInsYN'>이미지등록요청(상품이미지 등록 및 추가시 반듯이 체크박스에 체크가 되어야 등록 됩니다.)</td>
							</tr>
							<tr>
								<td class='search_box_item' style='padding:10px 10px;' colspan=2>
									<div id='goods_manual_area_1'>
										<div style='width:100%;padding:5px;' id='group_product_area_1'>
											<ui id='image_area'>".$porductImg."</ui>
										</div>
									</div>
									<!-- 미리보기
									<div class='img_wrap'>
										<img id='imgView' width='100px' height='100px'>
									</div>
									-->
								</td>
							</tr>
							<tr>
								<td colspan=2>※ 삭제 클릭시 이미지 복구가 불가능합니다. 삭제 클릭 후 반듯이 하단 저장 버튼을 누르시기 바랍니다.</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor='#ffffff' height=30 style='border-bottom:1px solid silver;'>
					<td class='input_box_title'  nowrap>리스트이미지</td>
					<td class='small'>
						<input type='text' name='listNum' id='listNum' value='".$product_info[listNum]."' style='width:50px;'> * 0으로 설정 시 1번 이미지가 상품리스트 이미지로 등록 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)
					</td>
				</tr>
				<tr bgcolor='#ffffff' height=30 style='border-bottom:1px solid silver;'>
					<td class='input_box_title'  nowrap>마우스오버이미지</td>
					<td class='small'>
						<input type='text' name='overNum' id='overNum' value='".$product_info[overNum]."' style='width:50px;'> * 0으로 설정 시 3번 이미지가 마우스오버 이미지로 등록 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)
					</td>
				</tr>
				<tr bgcolor='#ffffff' height=30 style='border-bottom:1px solid silver;'>
					<td class='input_box_title'  nowrap>리스트작은이미지</td>
					<td class='small'>
						<input type='text' name='slistNum' id='slistNum' value='".$product_info[slistNum]."' style='width:50px;'> * 0으로 설정 시 1번 이미지가 리스트작은 이미지로 등록 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)
					</td>
				</tr>
				<tr bgcolor='#ffffff' height=30 style='border-bottom:1px solid silver;'>
					<td class='input_box_title'  nowrap>썸네일이미지</td>
					<td class='small'>
						<input type='text' name='nailNum' id='nailNum' value='".$product_info[nailNum]."' style='width:50px;'> * 0으로 설정 시 1번 이미지가 썸네일 이미지로 등록 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)
					</td>
				</tr>
				<tr bgcolor='#ffffff' height=30 style='border-bottom:1px solid silver;'>
					<td class='input_box_title'  nowrap>패턴이미지</td>
					<td class='small'>
						<input type='text' name='pattNum' id='pattNum' value='".$product_info[pattNum]."' style='width:50px;'> * 0으로 설정 시 1번 이미지가 패턴 이미지로 등록 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)
					</td>
				</tr>
			</table>";
   
    $mstring = $mstring."	
			</div><div style='clear:both;height:70px;'></div>";


    return $mstring;
}