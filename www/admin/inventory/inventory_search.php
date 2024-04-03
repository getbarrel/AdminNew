<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include("../class/layout.class");
include("./inventory.lib.php");


if($max == ""){
	$max = 100; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;

/*
if($admininfo[admin_level] == 9){
	if($company_id){
		$where = "where p.id Is NOT NULL and p.id = r.pid and p.stock_use_yn = 'Y' and p.admin ='".$company_id."'
						and p.id = po.pid and po.option_kind = 'b'
						and po.opn_ix = pod.opn_ix
						and p.inventory_info = pi.pi_ix ";

		$where = "where g.gid =gu.gid  ";


	}else{
		$where = "where p.id Is NOT NULL and p.id = r.pid and p.stock_use_yn = 'Y'
						and p.id = po.pid and po.option_kind = 'b'
						and po.opn_ix = pod.opn_ix
						and p.inventory_info = pi.pi_ix ";

		$where = "where g.gid =gu.gid  ";
	}
}else{
	$where = "where p.id Is NOT NULL and p.id = r.pid  and p.stock_use_yn = 'Y' and p.admin ='".$admininfo[company_id]."'
					and p.id = po.pid and po.option_kind = 'b'
					and po.opn_ix = pod.opn_ix
					and p.inventory_info = pi.pi_ix";

	$where = "where g.gid =gu.gid  ";
}
*/

	$where = "where g.gid =gu.gid  ";
	
	if($admininfo[admin_level] == 9){
		if($company_id){
			$where .= " and g.admin ='".$company_id."'";
		}
	}else{
		$where .= " and g.admin ='".$admininfo[company_id]."'";
	}


	if($pid != ""){
		$where = $where."and p.id = $pid ";
	}

	if($search_text != ""){
		$where = $where."and ".$search_type." LIKE '%".$search_text."%' ";
	}

	if($disp != ""){
		$where .= " and g.disp = ".$disp;
	}

//echo $state;
	if($state2 != ""){
		//session_register("state");
		$where = $where." and g.state = ".$state2." ";
	}
	if($brand2 != ""){
		//session_register("brand");
		$where .= " and brand = ".$brand2."";
	}

	/*if($brand_name != ""){
		$where .= " and brand_name LIKE '%".$brand_name."%' ";
	}*/

	if($brand != ""){
		//session_register("brand");
		$where .= " and brand = ".$brand."";
	}

	if($cid2 != ""){
		//session_register("cid");
		//session_register("depth");
		$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
	}else{
		$where .= "";
	}


if($stock_status == "soldout"){
	$stock_where = "and (stock = 0 or option_stock_yn = 'N') ";
}else if($stock_status == "shortage"){
	$stock_where = "and (stock < safestock or option_stock_yn = 'R') ";
}else if($stock_status == "surplus"){
	$stock_where = "and (stock > safestock or option_stock_yn = 'Y')";
}

switch ($depth){
	case 0:
		$cut_num = 3;
		break;
	case 1:
		$cut_num = 6;
		break;
	case 2:
		$cut_num = 9;
		break;
	case 3:
		$cut_num = 12;
		break;
	case 4:
		$cut_num = 15;
		break;
}

if ($cid2){
	$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%' ";
}



if($_COOKIE[view_inventory_search] != 1){		//재고 없는 품목 포함 
	$stock_join_type = " right join ";
	//$where .= "and ips.stock > 0 "; //// 가용재고 처리를 위해서 주석처리
	$where .= "and ips.stock != 0 "; //// 재고가 없는것들은 제외!
}else{
	$stock_join_type = " left join ";
}

/***** 상품및 옵션 선택할때는 온라인 창고의 재고들만 SUM 해야함 2014-07-16 Hong *****/

if($type=="goods" || $type=="stock_options" || $type=="box_options" || $type=="set_options" || $type == "addoptions" || $type == "set2options" || $type == "codi_options"){
	$goup_by =" group by g.gid , gu.unit";
	//$pi_where =" and pi.online_place_yn='Y' ";
}else{
	$goup_by =" group by g.gid , gu.unit, ips.pi_ix ";
}

/*
$sql = "select count(*) as total
		from inventory_goods g, inventory_goods_item gi, inventory_product_stockinfo ips, inventory_place_info pi
		$where and  gu.gid = ips.gid and ips.pi_ix = pi.pi_ix
		 $stock_where $orderbyString ";
*/

$sql = "select
				count(*) as total
			from (
				select 
					g.cid,g.gname, g.gcode, g.admin,  g.ci_ix, g.pi_ix, pi.place_name,  ifnull(sum(ips.stock),0) as stock, gu.* 
				from 
					inventory_goods g 
					right join inventory_goods_unit gu  on g.gid = gu.gid
					".$stock_join_type." (
						select ips.* from inventory_product_stockinfo ips, inventory_place_info pi where ips.pi_ix = pi.pi_ix and pi.online_place_yn = 'Y'
					) ips on gu.gid = ips.gid and gu.unit = ips.unit
					left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
				$where    
					 $stock_where 
					 $goup_by ) data
		 ";

$db->query($sql);
$db->fetch();
$total = $db->dt[total];
	//echo $db->total;
	//exit;


if($orderby == "date"){
	$orderbyString = "order by g.regdate desc ";
}else{
	$orderbyString = "order by g.gid asc ";
	//$orderbyString = "order by g.regdate desc ";
}

/*
	$sql = "select p.id,  r.cid, p.pcode, p.pname, p.sellprice, p.coprice, p.regdate,p.vieworder,p.disp, p.surtax_yorn, po.opn_ix, stock, safestock, pi.place_name, pod.id as  opndt_ix,option_name, option_div,option_code,option_price, option_stock, p.sell_ing_cnt, option_sell_ing_cnt, option_safestock,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2
			from  ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_PRODUCT." p
			, ".TBL_SHOP_PRODUCT_OPTIONS." po
			, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod
			, inventory_place_info pi
			$where $stock_where $orderbyString
			LIMIT $start, $max";
*/

/*
	$sql = "select g.gid, g.cid,g.gname, gi.gid, gi.unit , sum(stock) as stock
		from inventory_goods g,
		inventory_goods_unit gu,
		inventory_product_stockinfo ips
		$where
		and  g.gid = ips.gid
		group by g.gid, g.cid,g.gname, gi.gid, gi.unit
		$orderbyString
		LIMIT $start, $max
		";
*/
//surtax_div


$sql = "select 
			g.cid,g.gname, g.gid,
			g.gcode,g.barcode,
			g.admin, 
			g.model,
			g.origin, 
			g.company, 
			g.b_ix, g.ci_ix,
			g.pi_ix, 
			g.surtax_div,
			g.standard,
			g.color,
			g.size,
			pi.place_name, 
			ifnull(sum(ips.stock),0) as stock,
			gu.* 
		from 
			inventory_goods g 
			right join inventory_goods_unit gu  on g.gid =gu.gid
			".$stock_join_type."  (
				select ips.* from inventory_product_stockinfo ips, inventory_place_info pi where ips.pi_ix = pi.pi_ix and pi.online_place_yn = 'Y'
			) ips on gu.gid = ips.gid and gu.unit = ips.unit
			left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
		$where	
			$stock_where 
			$goup_by
			$orderbyString 
			LIMIT $start, $max";


$db->query($sql);

$goods_infos = $db->fetchall();

$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&buying_status=$buying_status&sdate=$sdate&edate=$edate&type=".$_REQUEST["type"]."&seq=".$_REQUEST["seq"]."&search_type=".$_REQUEST["search_type"]."&search_text=".$_REQUEST["search_text"]."","");//&from=$from 추가 kbk 13/08/08

$Script = "<script language='javascript' src='../js/table_changeorder.js'></script>
<script language='JavaScript' >

if(window.dialogArguments){
	var opener = window.dialogArguments;
}else{
	var opener = window.opener;
}

function reloadView(){

	if($('#view_inventory_search').attr('checked') == true || $('#view_inventory_search').attr('checked') == 'checked'){		
		$.cookie('view_inventory_search', '1', {expires:1,domain:document.domain, path:'/', secure:0});
	}else{		
		$.cookie('view_inventory_search', '0', {expires:1,domain:document.domain, path:'/', secure:0});
	}
	
	document.location.reload();

}

function CheckSearch(frm){	
	//alert(frm.search_type.value);
	if(frm.search_type.value.length < 1){
		alert('검색타입을 입력해주세요');
		return false;
	}

	if(frm.search_text.value.length < 1){
		alert('검색어를 입력해주세요');
		return false;
	}
}

function SelectMember(company_id, com_name){
	//alert($('#company_id',opener.document).parent().html());
	$('#company_id',opener.document).val(company_id);
	$('#com_name',opener.document).val(com_name);
	self.close();
}


function clearAll(){
	$('input[id^=gid_]').attr('checked',false);
}

function checkAll(){
	//alert(55);
     $('input[id^=gid_]').attr('checked',true);
	
}
function fixAll(){
	if ($('#all_fix').attr('checked')){
		checkAll();
		//frm.all_fix.checked = true
	}else{
		clearAll();
		//frm.all_fix.checked = false;
	}
}

function sendsubul(gid){
	opener.location.href='./stock_subul.php?subul_type=date&gid='+gid;
	self.close();

}

function SelectOneGoods(gu_ix,stock){
	var options_obj_id = '".$type."';
	var bool = true;
	$.blockUI.defaults.css = {}; 
	$.blockUI({ message: $('#loading'), css: {left:'40%', top:'40%',  width: '10px' , height: '10px' ,padding:  '10px'} });  
	setTimeout($.unblockUI, 500); 
	
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'get_goods_unit', 'gu_ix':gu_ix},
		url: './inventory_goods_input.act.php',  
		dataType: 'json', 
		async: false, 
		beforeSend: function(){ 
		},  
		success: function(data){ 
			if(data != null){
				gname = data['gname'];
				standard = data['standard'];
				unit = data['unit_text'];
						
				if($('#use_unit_text').val() == 1){
					use_option_name = gname+'('+unit+')';
				}else if($('#use_unit_text').val() == 2){
					use_option_name = standard;
				}else if($('#use_unit_text').val() == 3){
					use_option_name = gname+'-'+standard;
				}
				barcode = data['barcode'];
				//stock = data['stock'];
				safestock = data['safestock'];
				buying_price = data['buying_price'];
				wholesale_price = data['wholesale_price'];
				//wholesale_sellprice = data['wholesale_sellprice'];
				wholesale_sellprice = data['wholesale_price'];
				sellprice = data['sellprice'];
				//discount_price = data['discount_price'];
				discount_price = data['sellprice'];
				gid = data['gid'];
				//매입업체
				trade_admin = data['ci_ix'];
				trade_name = data['com_name'];
				//원산지
				origin = data['origin'];
				//브랜드
				b_ix = data['b_ix'];
				brand_name = data['brand_name'];
				//제조사
				company = data['company'];
			
				if(options_obj_id=='excel_input_order'){
					bool=false;
					opener.InventoryGoodsJoin('".$no."',data);
					self.close();
				}

			}else{
				bool=false;
				alert('데이터를 가지고 오는데 실패하였습니다.');
			}
		} ,
		error:function(x, o, e){
			alert(x.status + ' : '+ o +' : '+e);
		}
	});
	

	if(bool){
		$('input[name=pcode]',opener.document).val(gu_ix);
		$('input[name=gid]',opener.document).val(gid);
		$('input[name=paper_pname]',opener.document).val(use_option_name);
		$('input[name=pname]',opener.document).val(use_option_name);
		$('input[name=barcode]',opener.document).val(barcode);
		$('input[name=stock]',opener.document).val(stock);
		$('input[name=safestock]',opener.document).val(safestock);
		$('input[name=coprice]',opener.document).val(buying_price);
		$('input[name=wholesale_price]',opener.document).val(wholesale_price);
		$('input[name=wholesale_sellprice]',opener.document).val(wholesale_sellprice);
		$('input[name=listprice]',opener.document).val(sellprice);
		$('input[name=sellprice]',opener.document).val(discount_price);

		$('input[name=trade_admin]',opener.document).val(trade_admin);
		$('input[name=trade_name]',opener.document).val(trade_name);
		$('input[name=origin]',opener.document).val(origin);
		$('input[name=b_ix]',opener.document).val(b_ix);
		$('input[name=brand_name]',opener.document).val(brand_name);
		$('select[name=company]',opener.document).val(company);

		opener.calcurate_maginrate(opener.document.product_input)	//마진률 자동 계산 2013-12-16 이학봉

		if(options_obj_id == 'goods'){
			self.close();
		}
	}
}";

if($_SESSION["admininfo"]["mallstory_version"] == "service"){

	
	$Script .= "
	function MakeOption(){
		var item_infos = new Array();
		var option_length = 0;
		var mk_i = 0;
		var mk_j =0;
		var gname = '';
		var standard = '';
		var use_option_name = '';
		var gid = '';
		var unit = '';
		var buying_price = '';
		var sellprice = '';
		var stock = 0;
		var gu_ix = 0;
		
	/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */
		$('table#'+options_obj_id+'_table',opener.document).find('tr[depth=1]:last').each(function(){
			 option_length = $(this).find('#option_length').val();
		});
		/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */
		mk_i = parseInt(option_length) + 1;
		mk_j = parseInt(option_length) + 1;
		//alert($('input.gid:checked').length);

		if($('input.gid:checked').length > 0){
			$('input.gid:checked').each(function(){
				if($(this).attr('checked') == 'checked'){
					//alert($(this).val());
					gid = $(this).val() ;
					gname = $(this).attr('gname') ;
					standard = $(this).attr('standard') ;
					unit = $(this).attr('unit') ;
					if($('#use_unit_text').val() == 1){
						use_option_name = gname+'('+unit+')';
					}else if($('#use_unit_text').val() == 2){
						use_option_name = standard;
					}else if($('#use_unit_text').val() == 3){
						use_option_name = gname+'-'+standard;
					}
					
					buying_price = $(this).attr('buying_price');
					sellprice = $(this).attr('sellprice');
					wholesale_price = $(this).attr('wholesale_price');
					discount_price = $(this).attr('discount_price');
					wholesale_sellprice = $(this).attr('wholesale_sellprice');
					safestock = $(this).attr('safestock') ;
					stock = $(this).attr('stock') ;
					gu_ix = $(this).attr('gu_ix') ;
					barcode = $(this).attr('barcode') ;

					//alert(g_ix);
					item_infos[mk_i] = {gid:gid,gname:use_option_name,unit:unit,buying_price:buying_price, wholesale_price:wholesale_price, sellprice:sellprice, safestock:safestock,stock:stock, gu_ix:gu_ix, barcode:barcode, wholesale_sellprice:wholesale_sellprice, discount_price:discount_price};
					//item_infos[mk_i]['unit'] = $(this).attr('unit');
					

					mk_i++;
				}
			});
			
			if($('#stock_save').attr('checked') == 'checked'){
				$('table.options_basic_item_input_0',opener.document).each(function(){
					if($(this).attr('opt_idx') != 0){
						$(this).remove();
					}
				});

				for(j=0; j < $('input.gid:checked').length ; j++){
					//alert($('table.options_basic_item_input_0', opener.document).length+'<'+($('table.options_basic_item_input_0', opener.document).length));
					if($('#stock_save').attr('checked') == 'checked'){
						if(($('table.options_basic_item_input_0', opener.document).length) <= j){
							opener.copyOptions('options_basic_item_input_0');	//
							//alert(1);
							//opener.newCopyOptions('options_basic_item_input_0');	
						}
					}else{
						opener.copyOptions('options_basic_item_input_0');
					}
				}
			}else{
				for(j=0; j < $('input.gid:checked').length ; j++){
						opener.copyOptions('options_basic_item_input_0');
				}
			}

			$('table.options_basic_item_input_0',opener.document).each(function(){
				//alert(item_infos[mk_j]['gid']);
				//alert(mk_i +'> '+mk_j +':::'+ (mk_i > mk_j));
				if(mk_i > mk_j){
					
					$(this).find('input[id^=options_price_stock_option_div]').val(item_infos[mk_j]['gname']+'('+ item_infos[mk_j]['unit']+')');
					$(this).find('input[id^=options_price_stock_option_coprice]').val(item_infos[mk_j]['buying_price']);
					$(this).find('input[id^=options_price_stock_option_price]').val(item_infos[mk_j]['sellprice']);
					$(this).find('input[id^=options_price_stock_option_code]').val(item_infos[mk_j]['gu_ix']);
					$(this).find('input[id^=options_price_stock_option_safestock]').val(item_infos[mk_j]['safestock']);
					$(this).find('input[id^=options_price_stock_option_stock]').val(item_infos[mk_j]['stock']);
					
					mk_j++;
				}
				
			});
			
		}else{
			alert('옵션에 등록하고자 하는 품목을 선택해주세요');
		}
	}";

}else{

	$Script .= "
	function MakeOption(){
		var item_infos = new Array();
		var option_length = 0;
		var mk_i = 0;
		var mk_j =0;
		var gname = '';
		var standard = '';
		var use_option_name = '';
		var gid = '';
		var unit = '';
		var buying_price = '';
		var sellprice = '';
		var stock = 0;
		var gu_ix = 0;
		var options_obj_id = '".$type."';
		var inputOption = false;

		/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */
		$('table#'+options_obj_id+'_table',opener.document).find('tr[depth=1]:last').each(function(){
			 option_length = $(this).find('#option_length').val();
			 if($(this).find('input[name^=stock_options]').val()){
			 	inputOption = true; 
			 }
		});
		/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */
		mk_i = parseInt(option_length) + 1;
		mk_j = parseInt(option_length) + 1;
		//alert($('input.gid:checked').length);

		if($('input.gid:checked').length > 0){
			$.blockUI.defaults.css = {}; 
			$.blockUI({ message: $('#loading'), css: {left:'40%', top:'40%',  width: '10px' , height: '10px' ,padding:  '10px'} });  

			$('input.gid:checked').each(function(){
				if($(this).attr('checked') == 'checked'){
					//alert($(this).val());
					gid = $(this).val() ;
					gname = $(this).attr('gname') ;
					standard = $(this).attr('standard') ;
					unit = $(this).attr('unit') ;

					if($('#use_unit_text').val() == 1){
						use_option_name = gname+'('+unit+')';
					}else if($('#use_unit_text').val() == 2){
						//use_option_name = standard;
						use_option_name = gname;
					}else if($('#use_unit_text').val() == 3){
						use_option_name = gname+'-'+standard;
					}

					
					buying_price = $(this).attr('buying_price') ;
					sellprice = $(this).attr('sellprice') ;
					wholesale_price = $(this).attr('wholesale_price') ;
					discount_price = $(this).attr('discount_price');
					wholesale_sellprice = $(this).attr('wholesale_sellprice');
					safestock = $(this).attr('safestock') ;
					stock = $(this).attr('stock') ;
					gu_ix = $(this).attr('gu_ix') ;
					barcode = $(this).attr('barcode');
					surtax_div = $(this).attr('surtax_div');
;
					item_infos[mk_i] = {gid:gid,gname:use_option_name,unit:unit,buying_price:buying_price, wholesale_price:wholesale_price, sellprice:sellprice, safestock:safestock,stock:stock, gu_ix:gu_ix, barcode:barcode, wholesale_sellprice:wholesale_sellprice, discount_price:discount_price, surtax_div:surtax_div};
					//alert(mk_i+':::'+gname);
					//item_infos[mk_i]['unit'] = $(this).attr('unit');
					
					mk_i++;
				}
			});
					//$.unblockUI;
			setTimeout($.unblockUI, 500); 


			if($('#stock_save').attr('checked') == 'checked'){
				$('table#'+options_obj_id+'_table',opener.document).find('tr[depth=1]').each(function(){
					if($('table#'+options_obj_id+'_table',opener.document).find('tr[depth=1]').length > 1){
						//alert($('table#'+options_obj_id+'_table',opener.document).find('tr[depth=1]').length);
						if($(this).attr('opt_idx') != 0){
							$(this).remove();
						}
					}else{
						$(this).find('input[id^=add_option_div]').val('');
					}
				});

				for(j=0; j < $('input.gid:checked').length ; j++){

					if($('#stock_save').attr('checked') == 'checked'){
						if(($('table#'+options_obj_id+'_table', opener.document).length) <= j){
							opener.AddOptionsCopyRow(options_obj_id+'_table',options_obj_id);	
						}
					}else{
						opener.AddOptionsCopyRow(options_obj_id+'_table',options_obj_id);
					}
				}
			}else{
				if(inputOption == false){					
					for(j=1; j < $('input.gid:checked').length ; j++){
							opener.AddOptionsCopyRow(options_obj_id+'_table', options_obj_id);
					}
				}else{					
					for(j=0; j < $('input.gid:checked').length ; j++){
							opener.AddOptionsCopyRow(options_obj_id+'_table', options_obj_id);
					}
				}
			}
		
			
			//alert($('table#'+options_obj_id+'_table').find('tr[depth=1]').length);
			$('table#'+options_obj_id+'_table',opener.document).find('tr[depth=1]').each(function(k,v){
			console.log(k+'==='+options_obj_id);
			console.log(k+'==='+item_infos[mk_j]);
				//alert(item_infos[mk_j]['gid']);
				//alert(mk_i +'> '+mk_j +':::'+ (mk_i > mk_j));
				//alert($(this).find('input[id^=add_option_div]').val()+';;;'+mk_j +':::'+item_infos[mk_j]['gname']);
				if($(this).find('input[id^=add_option_div]').val() == ''){
					//if(mk_i > mk_j){
						
						$(this).find('input[id^=add_option_div]').val(item_infos[mk_j]['gname']);
						$(this).find('input[id^=add_option_color]').val(item_infos[mk_j]['gname']);
						$(this).find('input[id^=add_option_size]').val(item_infos[mk_j]['gname']);
						
						$(this).find('input[id^=add_option_coprice]').val(item_infos[mk_j]['buying_price']);
						$(this).find('input[id^=add_option_listprice]').val(item_infos[mk_j]['sellprice']);
						$(this).find('input[id^=add_option_sellprice]').val(item_infos[mk_j]['sellprice']);
						$(this).find('input[id^=add_option_wholesale_listprice]').val(item_infos[mk_j]['wholesale_price']);
						$(this).find('input[id^=add_option_wholesale_price]').val(item_infos[mk_j]['wholesale_price']);
						$(this).find('input[id^=add_option_code]').val(item_infos[mk_j]['gu_ix']);
						$(this).find('input[id^=add_option_gid]').val(item_infos[mk_j]['gid']);
						$(this).find('input[id^=add_option_safestock]').val(item_infos[mk_j]['safestock']);
						$(this).find('input[id^=add_option_stock]').val(item_infos[mk_j]['stock']);
						$(this).find('input[id^=add_option_barcode]').val(item_infos[mk_j]['barcode']);

						$(this).find('select[id^=add_option_surtax_div]').val(item_infos[mk_j]['surtax_div']);

						mk_j++;
					//}
				}
				
			});
			console.log(options_obj_id);

			
			if(options_obj_id){
				opener.CalcurateMinimumPrice(options_obj_id);
			}
		}else{
			alert('옵션에 등록하고자 하는 품목을 선택해주세요');
		}
	}";
}

$Script .= "
function MakeAddOption(){
	var item_infos = new Array();
	var mk_i = 0;
	var gname = '';
	var standard = '';
	var use_option_name = '';
	var gid = '';
	var unit = '';
	var buying_price = '';
	var sellprice = '';
	var stock = 0;
	var gu_ix = 0;
	var seq = '".$_GET["seq"]."';
	var options_obj_id = '".$type."';

	if($('input.gid:checked').length > 0){
		$.blockUI.defaults.css = {}; 
		$.blockUI({ message: $('#loading'), css: {left:'40%', top:'40%',  width: '10px' , height: '10px' ,padding:  '10px'} });  

		$('input.gid:checked').each(function(){
			if($(this).attr('checked') == 'checked'){

				gid = $(this).val();
				gname = $(this).attr('gname');
				standard = $(this).attr('standard');
				unit = $(this).attr('unit');

				if($('#use_unit_text').val() == 1){
					use_option_name = gname+'('+unit+')';
				}else if($('#use_unit_text').val() == 2){
					//use_option_name = standard;
					use_option_name = gname;
				}else if($('#use_unit_text').val() == 3){
					use_option_name = gname+'-'+standard;
				}

				
				buying_price = $(this).attr('buying_price');
				sellprice = $(this).attr('sellprice') ;
				wholesale_price = $(this).attr('wholesale_price');
				safestock = $(this).attr('safestock') ;
				stock = $(this).attr('stock');
				gu_ix = $(this).attr('gu_ix');
				barcode = $(this).attr('barcode');
				surtax_div = $(this).attr('surtax_div');

				item_infos[mk_i] = {gid:gid,gname:use_option_name,unit:unit,buying_price:buying_price, wholesale_price:wholesale_price, sellprice:sellprice, safestock:safestock,stock:stock, gu_ix:gu_ix, barcode:barcode, surtax_div:surtax_div};

				//item_infos[mk_i]['unit'] = $(this).attr('unit');
				
				mk_i++;
			}
		});

		//$.unblockUI;
		setTimeout($.unblockUI, 500); 

		if($('#stock_save').attr('checked') == 'checked'){
			$($('table#'+options_obj_id+'_table_'+seq,opener.document).find('tr[depth=1]').get().reverse()).each(function(){
				if($('table#'+options_obj_id+'_table_'+seq,opener.document).find('tr[depth=1]').length > 1){
					if($(this).attr('opt_idx') != 0){
						$(this).remove();
					}
				}else{
					$(this).find('input[type=text]').val('');
				}
			});

			for(j=0; j < $('input.gid:checked').length ; j++){
				if($('#stock_save').attr('checked') == 'checked'){
					if(($('table#'+options_obj_id+'_table_'+seq, opener.document).length) <= j){
						opener.AddOptionsCopyRow(''+options_obj_id+'_table_'+seq, ''+options_obj_id+'', seq);	
					}
				}else{
					opener.AddOptionsCopyRow(''+options_obj_id+'_table_'+seq, ''+options_obj_id+'', seq);
				}
			}
		}else{
			var useralble_option_cnt = 0;
			$('table#'+options_obj_id+'_table_'+seq,opener.document).find('tr[depth=1]').each(function(){
				if($(this).find('input[id^=add_option_div]').val() == '' && $(this).find('input[id^=add_option_coprice]').val() == ''){
					useralble_option_cnt++;
				}
			});
			for(j=0; j < $('input.gid:checked').length-useralble_option_cnt ; j++){
					opener.AddOptionsCopyRow(''+options_obj_id+'_table_'+seq, ''+options_obj_id+'', seq);
			}
		}
		var mk_j = 0;

		$('table#'+options_obj_id+'_table_'+seq,opener.document).find('tr[depth=1]').each(function(k,v){
		
			if($(this).find('input[id^=add_option_div]').val() == ''){
				//if(mk_i > mk_j){

					$(this).find('input[id^=add_option_color]').val(item_infos[mk_j]['gname']);
					$(this).find('input[id^=add_option_size]').val(item_infos[mk_j]['gname']);
						
					$(this).find('input[id^=add_option_div]').val(item_infos[mk_j]['gname']);
					$(this).find('input[id^=add_option_coprice]').val(item_infos[mk_j]['buying_price']);
					$(this).find('input[id^=add_option_listprice]').val(item_infos[mk_j]['sellprice']);
					$(this).find('input[id^=add_option_sellprice]').val(item_infos[mk_j]['sellprice']);
					$(this).find('input[id^=add_option_wholesale_listprice]').val(item_infos[mk_j]['wholesale_price']);
					$(this).find('input[id^=add_option_wholesale_price]').val(item_infos[mk_j]['wholesale_price']);
					$(this).find('input[id^=add_option_code]').val(item_infos[mk_j]['gu_ix']);
					$(this).find('input[id^=add_option_gid]').val(item_infos[mk_j]['gid']);
					$(this).find('input[id^=add_option_set_cnt]').val(1);
					
					$(this).find('input[id^=add_option_safestock]').val(item_infos[mk_j]['safestock']);
					$(this).find('input[id^=add_option_stock]').val(item_infos[mk_j]['stock']);
					$(this).find('input[id^=add_option_barcode]').val(item_infos[mk_j]['barcode']);

					$(this).find('select[id^=add_option_surtax_div]').val(item_infos[mk_j]['surtax_div']);

					mk_j++;
				//}
			}
		});

		if(options_obj_id == 'set2options'){

			opener.OptionCalcuration('add_option_coprice','set2options_table_'+seq,'sum');			
			opener.OptionCalcuration('add_option_wholesale_listprice','set2options_table_'+seq,'sum');
			opener.OptionCalcuration('add_option_wholesale_price','set2options_table_'+seq,'sum');
			opener.OptionCalcuration('add_option_listprice','set2options_table_'+seq,'sum');
			opener.OptionCalcuration('add_option_sellprice','set2options_table_'+seq,'sum');
			opener.OptionCalcuration('add_option_stock','set2options_table_'+seq,'min');
			
			//opener.OptionCalcuration('add_option_sell_ing_cnt','set2options_table_'+seq,'sum');
			
		}
		//CalcurateMinimumPrice
		if(options_obj_id){
			opener.CalcurateMinimumPrice(options_obj_id);
		}
		
	}else{
		alert('옵션에 등록하고자 하는 품목을 선택해주세요');
	}
}

</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<!--tr><td  align=left class='top_orange'  ></td></tr>
			<tr height=35 bgcolor=#efefef>
				<td  style='padding:0 0 0 0;'>
					<table width='100%' border='0' cellspacing='0' cellpadding='0' >
						<tr>
							<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap>
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 재고정보검색
							</td>
							<td width='90%' align='right' valign='top' >
								&nbsp;
							</td>
						</tr>
					</table>
				</td>
			</tr-->
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("재고정보검색", "재고정보검색", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > - 재고관리 옵션으로 등록하고자 하는 품목을 검색해주세요</td></tr>
			<tr>
				<td align=center style='padding: 0 5px 0 5px'>
				<form name='z' method='post'  action=''  onSubmit='return CheckSearch(this)'>
				<input type='hidden' name='act' value='search'>
					<table class='box_shadow' style='width:100%;' cellpadding=0 cellspacing=0>
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05' align=right style='padding: 0 20 0 20'>
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box'>
									<col width='220'>
									<col width='*'>
									<tr height='40' valign='middle'>
										<td align='center'  colspan=2 class='input_box_title'><b>재고정보검색</b>
											<select name='search_type'>
												<option value='g.gcode' ".CompareReturnValue("g.gcode",$search_type).">대표코드</option>
												<option value='g.gid' ".CompareReturnValue("g.gid",$search_type).">품목코드</option>
												<option value='g.gname' ".CompareReturnValue("g.gname",$search_type).">품목명</option>
												<option value='gu.gu_ix' ".CompareReturnValue("gu.gu_ix",$search_type).">시스템코드</option>
												<option value='gu.barcode' ".CompareReturnValue("gu.barcode",$search_type).">바코드</option>
											</select>
										</td>
										<td class='input_box_item' style='padding-left:15px;'>
											<input type='text' class='textbox' name='search_text' size='30' value='".$search_text."'>
											<input type='image' src='../images/".$admininfo['language']."/btn_search.gif' align=absmiddle>
										</td>
									</tr>
								</table>
							</td>
							<th class='box_06'></th>
						</tr>
						<tr>
							<th class='box_07'></th>
							<td class='box_08'></td>
							<th class='box_09'></th>
						</tr>
						</table>
				</form>

				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr height=30>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> ".($search_text == "" ? "재고품목 전체 목록입니다.":"'".$search_text ."' 로 검색된 결과 입니다.")."</td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' align='right'> 
			<input type='checkbox' name='view_inventory_search' id='view_inventory_search' onclick=\"reloadView('complete')\" ".($_COOKIE[view_inventory_search] == 1 ? "checked":"")." ><label for='view_inventory_search'> 재고없는품목 포함</label>
		</td>
	</tr>
	<tr>
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<tr height='28' bgcolor='#ffffff'>";
		if($type != "goods" && $type != "excel_input_order" && $type!='subul'){
	$Contents .= "<td width=3% height='25' class=s_td><input type=checkbox class=nonborder name='all_fix' id='all_fix'  onclick='fixAll()'></td>";
		}
	$Contents .= "
			<td width='7%' align='center' class=m_td><font color='#000000'><b>대표코드</b></font></td>
			<td width='7%' align='center' class=m_td><font color='#000000'><b>품목코드</b></font></td>
			<td width='7%' align='center' class=m_td><font color='#000000'><b >품목코드<br>+단위</b></font></td>
			<td width='*' align='center' class='m_td'><font color='#000000'><b>품목명</b></font></td>
			<td width='5%' align='center' class='m_td'><font color='#000000'><b>색상</b></font></td>
			<td width='5%' align='center' class='m_td'><font color='#000000'><b>사이즈</b></font></td>
			<td width='8%' align='center' class=m_td><font color='#000000'><b>규격(옵션)</b></font></td>
			<td width='5%' align='center' class=m_td><font color='#000000'><b>단위</b></font></td>
			<td width='7%' align='center' class=m_td><font color='#000000'><b>매입가</b></font></td>
			<td width='7%' align='center' class=m_td><font color='#000000'><b>기본도매가</b></font></td>
			<td width='7%' align='center' class=m_td><font color='#000000'><b>기본소매가</b></font></td>
			<td width='8%' align='center' class=m_td><font color='#000000'><b>창고명</b></font></td>
			<td width='7%' align='center' class=m_td><font color='#000000'><b>재고</b></font></td>
		  </tr>";


if(count($goods_infos)){
	for($i=0;$i  < count($goods_infos); $i++){
		//$db->fetch($i);

		 if($type == 'goods'||$type == "excel_input_order"){

			// $onclick_str = "onclick=\"SelectOneGoods('".$goods_infos[$i][gname]."','".$goods_infos[$i][gu_ix]."','".$goods_infos[$i][barcode]."','".$goods_infos[$i][stock]."','".$goods_infos[$i][safestock]."','".$goods_infos[$i][buying_price]."','".$goods_infos[$i][wholesale_price]."','".$goods_infos[$i][sellprice]."','".$goods_infos[$i][b_ix]."','".$goods_infos[$i][origin]."','".$goods_infos[$i][maker]."','".$goods_infos[$i][ci_ix]."');\"";
			//if($(this).css('background-color').replace(/\s/g,'')=='rgb(249,222,209)'){
			 $onclick_str = "SelectOneGoods('".$goods_infos[$i][gu_ix]."','".$goods_infos[$i][stock]."');";
 		 }else{			 

			 $onclick_str = "if($(this).parent().find('#gid_".$goods_infos[$i][gu_ix]."').attr('checked')){ 
									$(this).parent().find('#gid_".$goods_infos[$i][gu_ix]."').attr('checked',false);//$(this).parent().find('td').css('backgroundColor','#efefef');
								}else{ 
									$(this).parent().find('#gid_".$goods_infos[$i][gu_ix]."').attr('checked',true);//$(this).parent().find('td').css('backgroundColor','#efefef');
								};";
		 }

		$Contents .= "<tr height=25 style='text-align:center;cursor:pointer;'  >";
		if($type != "goods" && $type != "excel_input_order" && $type!='subul'){
		$Contents .= "
								<td class='list_box_td list_bg_gray'>
									<input type=checkbox class='nonborder gid' id='gid_".$goods_infos[$i][gu_ix]."' gname='".$goods_infos[$i][gname]."' standard='".$goods_infos[$i][standard]."' unit='".getUnit($goods_infos[$i][unit], "basic_unit","","text")."'  buying_price='".$goods_infos[$i][buying_price]."'  wholesale_price='".$goods_infos[$i][wholesale_price]."' sellprice='".$goods_infos[$i][sellprice]."' safestock='".$goods_infos[$i][safestock]."' stock='".$goods_infos[$i][stock]."' gu_ix='".$goods_infos[$i][gu_ix]."' barcode='".$goods_infos[$i][barcode]."'  wholesale_sellprice='".$goods_infos[$i][wholesale_price]."' discount_price='".$goods_infos[$i][sellprice]."' name=gid[] surtax_div='".$goods_infos[$i][surtax_div]."' value='".$goods_infos[$i][gid]."' >
								</td>";
		}
		//$onclick_str = "";
		$Contents .= "
								<td class='list_box_td list_bg_gray' ".($type=='subul' ? "onclick=\"sendsubul('".$goods_infos[$i][gid]."');\"" : "onclick=\" ".$onclick_str."\" ").">".$goods_infos[$i][gcode]."</td>
								<td class='list_box_td ' ".($type=='subul' ? "onclick=\"sendsubul('".$goods_infos[$i][gid]."');\"" : "onclick=\" ".$onclick_str."\" ").">".$goods_infos[$i][gid]."</td>
								<td class='list_box_td point ' ".($type=='subul' ? "onclick=\"sendsubul('".$goods_infos[$i][gid]."');\"" : "onclick=\" ".$onclick_str."\" ").">".$goods_infos[$i][gu_ix]."</td>
								<td class='list_box_td point' style='text-align:left;padding:0px 5px;' ".($type=='subul' ? "onclick=\"sendsubul('".$goods_infos[$i][gid]."');\"" : "onclick=\" ".$onclick_str."\" ").">".$goods_infos[$i][gname]."</td>
								<td class='list_box_td' ".($type=='subul' ? "onclick=\"sendsubul('".$goods_infos[$i][gid]."');\"" : "onclick=\" ".$onclick_str."\" ")." >".$goods_infos[$i]['color']."</td>
								<td class='list_box_td' ".($type=='subul' ? "onclick=\"sendsubul('".$goods_infos[$i][gid]."');\"" : "onclick=\" ".$onclick_str."\" ")." >".$goods_infos[$i]['size']."</td>
								<td class='list_box_td' ".($type=='subul' ? "onclick=\"sendsubul('".$goods_infos[$i][gid]."');\"" : "onclick=\" ".$onclick_str."\" ")." >".$goods_infos[$i][standard]."</td>
								<td class='list_box_td point' ".($type=='subul' ? "onclick=\"sendsubul('".$goods_infos[$i][gid]."');\"" : "onclick=\" ".$onclick_str."\" ").">".getUnit($goods_infos[$i][unit], "basic_unit","","text")."</td>
								<td class='list_box_td' ".($type=='subul' ? "onclick=\"sendsubul('".$goods_infos[$i][gid]."');\"" : "onclick=\" ".$onclick_str."\" ")." >".$goods_infos[$i][buying_price]."</td>
								<td class='list_box_td' ".($type=='subul' ? "onclick=\"sendsubul('".$goods_infos[$i][gid]."');\"" : "onclick=\" ".$onclick_str."\" ").">".$goods_infos[$i][wholesale_price]."</td>
								<td class='list_box_td' ".($type=='subul' ? "onclick=\"sendsubul('".$goods_infos[$i][gid]."');\"" : "onclick=\" ".$onclick_str."\" ").">".$goods_infos[$i][sellprice]."</td>
								<td class='list_box_td' ".($type=='subul' ? "onclick=\"sendsubul('".$goods_infos[$i][gid]."');\"" : "onclick=\" ".$onclick_str."\" ").">".$goods_infos[$i][place_name]."</td>
								<td class='list_box_td' ".($type=='subul' ? "onclick=\"sendsubul('".$goods_infos[$i][gid]."');\"" : "onclick=\" ".$onclick_str."\" ").">".$goods_infos[$i][stock]."</td>
								</tr>";
	}
}else{
	$Contents .= "
	<tr height=300>
		<td align=center style='padding:0 10px 0 0px' colspan=10>
			품목정보가 존재하지 않습니다.
		</td>
	</tr>";
}


$Contents .= "
		</table>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:0 10px 0 0px' colspan=2>
		</td>
	</tr>
	<tr>
		<td align=left style='padding:10px 10px 0 5px' >";
		if($type == "addoptions" || $type == "set2options" || $type == "codi_options"){
			$Contents .= "
			<img src='../images/".$admininfo["language"]."/btn_goods_intoon.gif' border='0' align='absmiddle' onclick='MakeAddOption()' style='cursor:pointer;'title='상품담기'>
		<div style='display:inline-block'><input type=checkbox name='stock_save' id='stock_save' ><label for='stock_save'>옵션전체변경</label></div>";
		}else if($type != "goods" && $type != "excel_input_order" && $type != 'subul'){
			$Contents .= "
			<img src='../images/".$admininfo["language"]."/btn_goods_intoon.gif' border='0' align='absmiddle' onclick='MakeOption()' style='cursor:pointer;' title='상품담기'>
		<div style='display:inline-block'><input type=checkbox name='stock_save' id='stock_save' ><label for='stock_save'>옵션전체변경</label></div>";
		
		}
		if($type != "excel_input_order" && $type != 'subul'){
			$Contents .= "
			<select name='use_unit_text' id='use_unit_text'>
				<option value='2'>규격명</option>
				<option value='1' ".($type=="goods" ? "selected" : "").">품목명(단위)</option>
				<option value='3'>품목명+규격명</option>
			</select>";
		}
		$Contents .= "
		</td>
		<td align=right style='padding:10px 0 0 0' >
			".$str_page_bar."
		</td>
	</tr>
	<tr>
		<td align=left style='padding:10px 10px' colspan=2>";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' style='line-height:120%' >
	<col width=8>
	<col width=*>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' >재고관리는 단품기준으로 관리됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' >품목구성하고자 하는 재고정보를 검색후 <u>상품담기</u> 를 이용해서 옵션으로 자동 구성하실 수 있습니다. </td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' >원하시는 정보를 클릭하시면 옵션정보로 하나씩 추가되게 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' > <u>옵션전체변경</u> 클릭후 옵션을 등록하시면 기존에 등록되어 있던 옵션정보가 초기화 됩니다.</td></tr>
</table>
";



$Contents .= HelpBox("재고품목검색", $help_text,"100");
$Contents .= "
		</td>
	</tr>
</TABLE>
";




$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "재고정보검색";
$P->NaviTitle = "재고정보검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>





