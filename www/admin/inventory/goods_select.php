<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include("../class/layout.class");
include("./inventory.lib.php");


if($max == ""){
	$max = 10; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


$db = new Database;


if($page_type == "adjustment"){	//재고 조정시 품목선택에서 재고수량 없는 품목도 노출함 2014-07-28 이학봉
	$stock_join_type = " left join ";
}else{
	if($_COOKIE[view_shotage_goods] != 1){
		$stock_join_type = " right join ";
	}else{
		$stock_join_type = " left join ";
	}
}

if($admininfo[admin_level] == 9){
	if($company_id){
		$where = "where p.id Is NOT NULL and p.id = r.pid and p.stock_use_yn = 'Y' and p.admin ='".$company_id."'
						and p.id = po.pid and po.option_kind = 'b'
						and po.opn_ix = pod.opn_ix
						and p.inventory_info = pi.pi_ix ";

		$where = "where g.gid =gu.gid ";
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



	if($pid != ""){
		$where = $where."and p.id = $pid ";
	}

/*
	if($search_type != "" && $search_text != ""){
		$where .= "and ".$search_type." LIKE '%".$search_text."%' ";
	}
*/

	if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
		//다중검색 시작 2014-04-10 이학봉
		if($search_text != ""){
			if(strpos($search_text,",") !== false){
				$search_array = explode(",",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$where .= "and ( ";
				
				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							if($search_type == "g.gid"){
								$where .= $search_type." LIKE '%".trim($search_array[$i])."%'";
							}else{
								$where .= $search_type." = '".trim($search_array[$i])."'";
							}
						}else{
							if($search_type == "g.gid"){
								$where .= $search_type." LIKE '%".trim($search_array[$i])."%' or ";
							}else{
								$where .= $search_type." = '".trim($search_array[$i])."' or ";
							}
						}
					}
				}
				$where .= ")";
			}else if(strpos($search_text,"\n") !== false){//\n
				$search_array = explode("\n",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$where .= "and ( ";

				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							if($search_type == "g.gid"){
								$where .= $search_type." LIKE '%".trim($search_array[$i])."%'";
							}else{
								$where .= $search_type." = '".trim($search_array[$i])."'";
							}
						}else{
							if($search_type == "g.gid"){
								$where .= $search_type." LIKE '%".trim($search_array[$i])."%' or ";
							}else{
								$where .= $search_type." = '".trim($search_array[$i])."' or ";
							}
						}
					}
				}
				$where .= ")";
			}else{
				if($search_type == "g.gid"){
					$where .= " and ".$search_type." LIKE '%".trim($search_text)."%'";
				}else{
					$where .= " and ".$search_type." = '".trim($search_text)."'";
				}
			}
		}

	}else{	//검색어 단일검색
		if($search_text != ""){
			if(substr_count($search_text,",")){
				$where .= " and ".$search_type." in ('".str_replace(",","','",str_replace(" ","",$search_text))."') ";
			}else{
				$where .= " and ".$search_type." LIKE '%".trim($search_text)."%' ";
			}
		}
	}


	if($disp != ""){
		$where .= " and g.disp = ".$disp;
	}

if($page_type != "adjustment"){
	if($pi_ix != ""){
		$where .= "and pi.pi_ix = '".$pi_ix."' ";
	}
}

	if($ps_ix != ""){
		$where .= "and ips.ps_ix = '".$ps_ix."' ";
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

	if($stock_company_id != ""){
		$stock_join_where =" and ips.company_id = '$stock_company_id' ";
	}

if($stock_status == "soldout"){
	$stock_where = "and (stock = 0 or option_stock_yn = 'N') ";
}else if($stock_status == "shortage"){
	$stock_where = "and (stock < safestock or option_stock_yn = 'R') ";
}else if($stock_status == "surplus"){
	$stock_where = "and (stock > safestock or option_stock_yn = 'Y')";
}

/*불량창고-온라인상품이 아닌경우 재고에서 빠져야 함 jk150623*/
if($page_type=='stocked'){
	$sql="select pi_ix from inventory_place_info where online_place_yn = 'Y' ";
	$db->query($sql);
	$place = $db->getrows();
	$stock_join_where = " and ips.pi_ix in ('".implode("','",$place[0])."')";
}

/*END*/

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

if($goods_type == 'apply_goods'){
$sql = "select count(*) as total from (
		select g.cid,g.gname, g.gcode, g.admin,  g.ci_ix, g.surtax_div, g.pi_ix, pi.place_name,  ifnull(sum(ips.stock),0) as stock, gu.*
		from  inventory_order_detail_tmp odt
		left join inventory_goods g on odt.gid = g.gid
		right join inventory_goods_unit gu  on g.gid =gu.gid
		left join  inventory_place_info pi on g.pi_ix = pi.pi_ix
		".$stock_join_type."  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
		$where
		 $stock_where
		 group by g.gid , gu.unit, ips.pi_ix) data
		 ";
}else{
	if($page_type == "stocked"){
		$sql = "select count(*) as total from (
				select g.cid,g.gname, g.gcode, g.admin,  g.ci_ix, g.surtax_div, g.pi_ix, pi.place_name,  ifnull(sum(ips.stock),0) as stock, gu.*
				from inventory_goods g
				right join inventory_goods_unit gu  on g.gid =gu.gid
				left join  inventory_place_info pi on g.pi_ix = pi.pi_ix
				".$stock_join_type."   inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
				$where
				 $stock_where
				 group by g.gid , gu.unit) data
				 ";
	}elseif($page_type == "adjustment"){
		$sql = "select count(*) as total from (
				select g.cid,g.gname, g.gcode, g.admin,  g.ci_ix, g.surtax_div, g.pi_ix, pi.place_name,  ifnull(sum(ips.stock),0) as stock, gu.*
				from 
					inventory_goods g
					right join inventory_goods_unit gu  on g.gid =gu.gid
					left join  inventory_place_info pi on g.pi_ix = pi.pi_ix
					".$stock_join_type."   inventory_product_stockinfo ips on (gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where." and pi.pi_ix = '".$pi_ix."')
				$where
				 $stock_where
				 group by g.gid , gu.unit) data
				 ";
	}else{
		$sql = "select count(*) as total from (
				select g.cid,g.gname, g.gid, g.gcode, g.admin, g.ci_ix, g.surtax_div, ips.pi_ix, pi.place_name, ips.ps_ix,  pi.company_id,  ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.unit , gu.buying_price, gu.sellprice, ips.vdate, ips.expiry_date
			from inventory_goods g
			right join inventory_goods_unit gu  on g.gid =gu.gid
			".$stock_join_type."  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
			left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
			left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
			$where
			 $stock_where
			 group by g.gid , gu.unit, ips.pi_ix
			 $orderbyString ) data
				 ";

	}
}

$db->query($sql);
$db->fetch();
$total = $db->dt[total];
//	echo $db->total;
	//exit;


//if($orderby == "date"){
	$orderbyString = "order by g.gcode, g.gid, g.regdate desc ";
//}else{
//	$orderbyString = "order by g.gid desc ";
//}

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



if($goods_type == 'apply_goods'){
	$sql = "select data.*,
		(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.company_id   limit 1) as company_name
		from
			(select g.cid,g.gname, g.gid, g.gcode, g.admin, g.ci_ix, g.surtax_div, ips.pi_ix, pi.place_name, ips.ps_ix,  pi.company_id,  ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.unit , gu.buying_price, gu.wholesale_price, gu.sellprice, ips.vdate, ips.expiry_date,gu.offline_wholesale_price,gu.change_amount
			left join inventory_goods g on odt.gid = g.gid
			right join inventory_goods_unit gu  on g.gid =gu.gid
			".$stock_join_type."   inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
			left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
			left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
			$where
			 $stock_where
			 group by g.gid , gu.unit, ips.pi_ix
			 $orderbyString
			 LIMIT $start, $max
		) data
		 ";
}else{
	if($page_type == "stocked" || $page_type == "adjustment"){
	$sql = "select data.*,
		(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.company_id   limit 1) as company_name
		from
			(select g.cid,g.gname, g.gid, g.gcode, g.admin, g.standard, g.ci_ix, g.surtax_div, ips.pi_ix, pi.place_name, ips.ps_ix,  pi.company_id,  ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.unit , gu.buying_price, gu.wholesale_price, gu.sellprice, ips.vdate, ips.expiry_date,gu.offline_wholesale_price,gu.change_amount
			from inventory_goods g
			right join inventory_goods_unit gu  on g.gid =gu.gid
			".$stock_join_type."   inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
			left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
			left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
			$where
			 $stock_where
			 group by g.gid , gu.unit
			 $orderbyString
			 LIMIT $start, $max
		) data
		 ";
	}else{
		if($_SESSION["admininfo"]["mallstory_version"] == "service"){
			$sql = "select data.*,
			(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.company_id   limit 1) as company_name
			from
				(select g.cid,g.gname, g.gid, g.gcode, g.admin, g.standard, g.ci_ix, g.surtax_div, ips.pi_ix, pi.place_name, ips.ps_ix,  pi.company_id,  ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.unit , gu.buying_price, gu.wholesale_price, gu.sellprice, ips.vdate, ips.expiry_date,gu.offline_wholesale_price ,gu.gu_ix,gu.change_amount
				from inventory_goods g
				right join inventory_goods_unit gu  on g.gid =gu.gid
				".$stock_join_type."  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
				left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
				left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
				$where
				 $stock_where
				 group by g.gid , gu.unit, ips.pi_ix , ips.ps_ix, ips.expiry_date
				 $orderbyString
				 LIMIT $start, $max
			) data";
		}else{
			$sql = "select data.*,
			(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.company_id   limit 1) as company_name
			,(select round(psprice/1.1) as lately_price from shop_order_detail od , shop_order o where od.oid=o.oid and od.gu_ix = data.gu_ix  and order_from = 'offline' and o.user_com_id= '".$user_com_id."' order by o.order_date limit 1) as lately_price
			from
				(select g.cid,g.gname, g.gid, g.gcode, g.admin, g.standard, g.ci_ix, g.surtax_div, ips.pi_ix, pi.place_name, ips.ps_ix,  pi.company_id,  ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.unit , gu.buying_price, gu.wholesale_price, gu.sellprice, ips.vdate, ips.expiry_date,gu.offline_wholesale_price ,gu.gu_ix
				from inventory_goods g
				right join inventory_goods_unit gu  on g.gid =gu.gid
				".$stock_join_type."  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
				left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
				left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
				$where
				 $stock_where
				 group by g.gid , gu.unit, ips.pi_ix , ips.ps_ix, ips.expiry_date
				 $orderbyString
				 LIMIT $start, $max
			) data";
		}
	}
}
//echo nl2br($sql);
//exit;
$db->query($sql);

$goods_infos = $db->fetchall();

$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&page_type=$page_type&search_type=$search_type&search_text=$search_text&company_id=$company_id&pi_ix=$pi_ix&ps_ix=$ps_ix&stock_company_id=$stock_company_id","");

$Script = "<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>
<script language='JavaScript' >

$(document).ready(function (){

//다중검색어 시작 2014-04-10 이학봉

	$('input[name=mult_search_use]').click(function (){
		var value = $(this).attr('checked');

		if(value == 'checked'){
			$('#search_text_input_div').css('display','none');
			$('#search_text_area_div').css('display','');
			
			$('#search_text_area').attr('disabled',false);
			$('#search_texts').attr('disabled',true);
		}else{
			$('#search_text_input_div').css('display','');
			$('#search_text_area_div').css('display','none');

			$('#search_text_area').attr('disabled',true);
			$('#search_texts').attr('disabled',false);
		}
	});

	var mult_search_use = $('input[name=mult_search_use]:checked').val();
		
	if(mult_search_use == '1'){
		$('#search_text_input_div').css('display','none');
		$('#search_text_area_div').css('display','');

		$('#search_text_area').attr('disabled',false);
		$('#search_texts').attr('disabled',true);
	}else{
		$('#search_text_input_div').css('display','');
		$('#search_text_area_div').css('display','none');

		$('#search_text_area').attr('disabled',true);
		$('#search_texts').attr('disabled',false);
	}

//다중검색어 끝 2014-04-10 이학봉

});

if(window.dialogArguments){
	var opener = window.dialogArguments;
}else{
	var opener = window.opener;
}


function CheckSearch(frm){
 
	if($('form[name=z]').find('input#search_texts').val().length > 0){ 
		if($('form[name=z]').find('select#search_type').val().length == 0){
			alert('검색타입을 입력해주세요');
			$('form[name=z]').find('select#search_type').focus();
			return false;
		}
	}
	 
}

function SelectMember(company_id, com_name){
	//alert($('#company_id',opener.document).parent().html());
	$('#company_id',opener.document).val(company_id);
	$('#com_name',opener.document).val(com_name);
	self.close();
}


function clearAll(frm){
		for(i=0;i < frm.gid.length;i++){
				frm.gid[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.gid.length;i++){
				frm.gid[i].checked = true;
		}
}
function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;

	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}

function sendsubul(gid){
	opener.location.href='./stock_subul.php?subul_type=date&gid='+gid
}

function GoodsSelectAll(){
	var mk_i = 0;
	var gid = '';
	var gname = '';
	var unit = '';
	var unit_text = '';
	var buying_price = '';
	var sellprice = '';
	var stock = 0;
	var wholesale_price = '';
	if($('input.gid:checked').length > 0){
		$.blockUI.defaults.css = {};
		$.blockUI({ message: $('#loading'), css: {left:'40%', top:'40%',  width: '10px' , height: '10px' ,padding:  '10px'} });

		$('input.gid:checked').each(function(){
			if($(this).attr('checked') == 'checked'){
				gid = $(this).val();
				gname = $(this).attr('gname');
				unit = $(this).attr('unit');
				unit_text = $(this).attr('unit_text');

				surtax_div = $(this).attr('surtax_div');
				surtax_text = $(this).attr('surtax_text');

				buying_price = $(this).attr('buying_price');
				company_name = $(this).attr('company_name');
				place_name = $(this).attr('place_name');
				section_name = $(this).attr('section_name');
				standard = $(this).attr('standard');

				pi_ix = $(this).attr('pi_ix');
				ps_ix = $(this).attr('ps_ix');
				vdate = $(this).attr('vdate');
				expiry_date = $(this).attr('expiry_date');
				stock = $(this).attr('stock');
				wholesale_price = $(this).attr('wholesale_price');
				sellprice = $(this).attr('sellprice');
				change_amount = $(this).attr('change_amount');


				if($(this).attr('lately_price')){
					lately_price = $(this).attr('lately_price') ;
					window.opener.GoodsSelect(gid,gname,unit,unit_text,standard, buying_price, company_name, place_name, section_name, pi_ix, ps_ix, vdate, expiry_date, stock,wholesale_price,sellprice,surtax_div,surtax_text,lately_price,change_amount,gcode);
				}else{
					window.opener.GoodsSelect(gid,gname,unit,unit_text,standard, buying_price, company_name, place_name, section_name, pi_ix, ps_ix, vdate, expiry_date, stock,wholesale_price,sellprice,surtax_div,surtax_text,change_amount);
				}
				//mk_i++;

			}

		});
		//$.unblockUI;
		setTimeout($.unblockUI, 500);

	}
	else{
		alert('옵션에 등록하고자 하는 품목을 선택해주세요');
	}
}


function reloadView(){

	if($('#view_shotage_goods').attr('checked') == true || $('#view_shotage_goods').attr('checked') == 'checked'){
		$.cookie('view_shotage_goods', '1', {expires:1,domain:document.domain, path:'/', secure:0});
	}else{
		$.cookie('view_shotage_goods', '0', {expires:1,domain:document.domain, path:'/', secure:0});
	}
	//alert(document.location);
//	try{
	document.forms['z'].submit();
	//dialogArguments.location.reload();
	//window.location.reload();
//	}catch(e){alert(e.message)}
//alert(1);
	//document.location.href='http://www.naver.com'

}

$(document).ready(function() {
		$('#search_text').focus();
});

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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 품목선택
							</td>
							<td width='90%' align='right' valign='top' >
								&nbsp;
							</td>
						</tr>
					</table>
				</td>
			</tr-->
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("품목정보 검색", "품목정보 검색", false)."</td>
			</tr>";
if($page_type == "order"){
$Contents .= "
			<tr>
				<td align='left' colspan=2 style='padding-bottom:12px;'>
					<div class='tab'>
						<table class='s_org_tab'>
							<tr>
								<td class='tab'>
									<table id='tab_00'  ".($goods_type == '' ? "class='on'":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='goods_select.php'\">품목정보 검색</td>
											<th class='box_03'></th>
										</tr>
									</table>
									<table id='tab_01'  ".($goods_type == 'apply_goods'  ? "class='on'":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='goods_select.php?goods_type=apply_goods'\">청구요청 목록</td>
											<th class='box_03'></th>
										</tr>
									</table>
								</td>
								<td align='right' style='text-align:right;vertical-align:bottom;padding:0 0 6px 4px;'>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>";
}
$Contents .= "
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > -  등록하고자 하는 품목을 검색해주세요</td></tr>
			<tr>
				<td align=center style='padding: 0 5px 0 5px'>
				<form name='z' method='get'  action=''  onSubmit='return CheckSearch(this)' >
				<input type='hidden' name='act' value='search'>
				<input type='hidden' name='page_type' value='".$page_type."'>
				<input type='hidden' name='goods_type' value='".$goods_type."'>
				<input type='hidden' name='user_com_id' value='".$user_com_id."'>
				<input type='hidden' name='stock_company_id' value='".$stock_company_id."'>
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

									<tr>
										<td class='input_box_title'>창고/로케이션</td>
										<td class='input_box_item'  colspan=3>";
										if($page_type == "warehouse_move" || $page_type == "adjustment" ){//|| $page_type == "stocked"
											$Contents .= "
											".SelectEstablishment($company_id,"company_id","text","true","onChange=\"loadPlace(this,'pi_ix')\" ")."
											".SelectInventoryInfo($company_id,$pi_ix,'pi_ix','text','true', "validation=true title='이동창고' onChange=\"loadPlaceSection(this,'ps_ix')\"  ")."
											".SelectSectionInfo($pi_ix,$ps_ix,'ps_ix',"text","true"," title='로케이션' ")." ";
										}else{
											$Contents .= "
											".SelectEstablishment($company_id,"company_id","select","true","onChange=\"loadPlace(this,'pi_ix')\" ")."
											".SelectInventoryInfo($company_id,$pi_ix,'pi_ix','select','true', "validation=true title='이동창고' onChange=\"loadPlaceSection(this,'ps_ix')\"  ")."
											".SelectSectionInfo($pi_ix,$ps_ix,'ps_ix',"select","true"," title='로케이션' ")." ";
										}
										$Contents .= "
										</td>
									</tr>
									<!--
									<tr height='40' valign='middle'>
										<td align='center'  class='input_box_title'><b>품목검색</b></td>
										<td class='input_box_item' colspan=3>
											<table cellpadding=0>
												<tr>
													<td>
													<select name='search_type' id='search_type_single' validation=true>
														<option value=''> 품목  검색</option>
														<option value='g.gcode' ".CompareReturnValue("g.gcode",$search_type)."> 대표코드</option>
														<option value='g.gid' ".CompareReturnValue("g.gid",$search_type)."> 품목코드 </option>
														<option value='g.gname' ".CompareReturnValue("g.gname",$search_type)."> 품목명</option>
														<option value='gu.gu_ix' ".CompareReturnValue("gu.gu_ix",$search_type)."> 시스템코드 </option>
														<option value='gu.barcode' ".CompareReturnValue("gu.barcode",$search_type)."> 바코드 </option>
													</select>
													</td>
													<td>	<input type='text' class='textbox' name='search_text' id='search_text' size='30' value=''></td>
												</tr>
											</table>
											<input type='image' src='../images/".$admininfo['language']."/btn_search.gif' align=absmiddle>
										</td>
										<td class='input_box_title'> 입고처</td>
										<td class='input_box_item'>".SelectSupplyCompany($ci_ix,"ci_ix","select", "false")."</td>
									</tr>-->
									<tr>
										<td class='search_box_title'>  검색어
										<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'><img src='/admin/images/icon_q.gif' align=absmiddle/></span>
										
										<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> 
										<label for='mult_search_use'>(다중검색 체크)</label>
										</td>
										<td class='search_box_item' colspan='3'>
											<table cellpadding=0 cellspacing=0 border='0'>
											<tr>
												<td valign='top'>
													<div style='padding-top:5px;'>
													<select name='search_type' id='search_type'  style=\"font-size:12px;\">
														<option value=''> 품목  검색</option>
														<option value='g.gcode' ".CompareReturnValue("g.gcode",$search_type)."> 대표코드</option>
														<option value='g.gid' ".CompareReturnValue("g.gid",$search_type)."> 품목코드 </option>
														<option value='g.gname' ".CompareReturnValue("g.gname",$search_type)."> 품목명</option>
														<option value='gu.gu_ix' ".CompareReturnValue("gu.gu_ix",$search_type)."> 시스템코드 </option>
														<option value='gu.barcode' ".CompareReturnValue("gu.barcode",$search_type)."> 바코드 </option>
													</select>
													</div>
												</td>
												<td style='padding:5px;'>
													<div id='search_text_input_div'>
														<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
													</div>
													<div id='search_text_area_div' style='display:none;'>
														<textarea name='search_text' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea><!--name='search_text_area' -->
													</div>
												</td>
												<td>
													<div>
														<span class='small blu' > * 다중 검색은 다중 아이디로 검색 지원이 가능합니다. 구분값은 ',' 혹은 'Enter'로 사용 가능합니다. </span>
													</div>
												</td>
											</tr>
											</table>
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
						<tr >
							<td colspan=3 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
						</tr>
						</table>
				</form>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr height=30>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> ".($search_text == "" ? "출고/재고이동이 가능한 품목 목록입니다.":"'".$search_text ."' 로 검색된 결과 입니다.")."</td>
		<td class='p11 ls1' style='padding:0 0 0 5px;text-align:right;' nowrap>";
		if($page_type != "adjustment"){
			$Contents .= "
			<input type='checkbox' name='view_shotage_goods' id='view_shotage_goods' onclick=\"reloadView('complete')\" ".($_COOKIE[view_shotage_goods] == 1 ? "checked":"")." >
			<label for='view_shotage_goods'> 재고없는품목 포함</label>";
		}
		$Contents .= "
		</td>
	</tr>
	<tr><form name='listform' method='post'  onsubmit='return CheckSMS(this);' ><input type=hidden name='act' value='listform' >
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<col width='3%'>
		<col width='7%'>
		<col width='7%'>
		<col width='15%'>
		<col width='5%'>
		<col width='7%'>
		<col width='7%'>
		<col width='7%'>
		<col width='7%'>
		<col width='7%'>
		<col width='7%'>
		<col width='7%'>
		<col width='7%'>
		<col width='7%'>";
if($page_type == "stocked" || $page_type == "adjustment"){
$Contents .= "
		<tr height='28' bgcolor='#ffffff'>
			<td width=5% height='25' class=s_td ><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
			<td align='center' class=m_td ><font color='#000000'><b>대표코드</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>품목코드</b></font></td>
			<td align='center' class='m_td' ><font color='#000000'><b>품목명</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>단위</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>규격</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>부가세적용</b></font></td>
			<td align='center' class=m_td  nowrap><font color='#000000'><b>입고단가</b></font></td>
			<td align='center' class=m_td  nowrap><font color='#000000'><b>출고단가</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>재고</b></font></td>
		  </tr>";
}else if($page_type == "delivery"){
$Contents .= "
		<tr height='28' bgcolor='#ffffff'>
			<td width=5% height='25' class=s_td ><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
			<td align='center' class=m_td ><font color='#000000'><b>대표코드</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>품목코드</b></font></td>
			<td align='center' class='m_td' ><font color='#000000'><b>품목명</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>단위</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>규격</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>부가세적용</b></font></td>
			<td align='center' class=m_td  nowrap><font color='#000000'><b>입고단가</b></font></td>
			<td align='center' class=m_td  nowrap><font color='#000000'><b>기본소매가</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>재고</b></font></td>
		  </tr>";
}else{

$Contents .= "
		<tr height='28' bgcolor='#ffffff'>
			<td width=3% height='25' class=s_td rowspan=2><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
			<td align='center' class=m_td rowspan=2><font color='#000000'><b>대표코드</b></font></td>
			<td align='center' class=m_td rowspan=2><font color='#000000'><b>품목코드</b></font></td>
			<td align='center' class='m_td' rowspan=2><font color='#000000'><b>품목명</b></font></td>
			<td align='center' class=m_td rowspan=2><font color='#000000'><b>단위</b></font></td>
			<td align='center' class=m_td rowspan=2><font color='#000000'><b>규격</b></font></td>
			<!--td align='center' class=m_td rowspan=2><font color='#000000'><b>부가세</b></font></td-->
			<!--td align='center' class=m_td rowspan=2><font color='#000000'><b>입고일</b></font></td-->
			<td align='center' class=m_td rowspan=2 nowrap><font color='#000000'><b>유통기한</b></font></td>
			<td align='center' class=m_td colspan=3><font color='#000000'><b>사업장/로케이션</b></font></td>
			<td align='center' class=m_td rowspan=2 nowrap><font color='#000000'><b>도매가</b></font></td>
			<td align='center' class=m_td rowspan=2 nowrap><font color='#000000'><b>소매가</b></font></td>
			<td align='center' class=m_td rowspan=2><font color='#000000'><b>재고</b></font></td>
		  </tr>
		  <tr align=center height=30>
				<td class=m_td>사업장</td>
				<td class=m_td>창고</td>
				<td class=m_td>로케이션</td>
				<!--td class=m_td>오프라인</td>
				<td class=m_td>기본</td-->
			</tr>";
}


if(count($goods_infos)){
	for($i=0;$i  < count($goods_infos); $i++){

		$gname = $goods_infos[$i][gname];

		if(strlen($gname) <= 30){
			$gname = $goods_infos[$i][gname];
		}else{
			$gname = cut_str($goods_infos[$i][gname],30);
		}
		$gname = $goods_infos[$i][gname];
		//$db->fetch($i);
		if($page_type == "stocked" || $page_type == "adjustment"){
		$Contents .= "<tr height=25 >
								<td class='list_box_td' >
									<input type=checkbox class='nonborder gid' id='gid' unit='".$goods_infos[$i][unit]."' unit_text='".getUnit($goods_infos[$i][unit], "basic_unit","","text")."'  gname='".$goods_infos[$i][gname]."' buying_price='".$goods_infos[$i][buying_price]."'  wholesale_price='".$goods_infos[$i][wholesale_price]."' offline_wholesale_price='".$goods_infos[$i][offline_wholesale_price]."' sellprice='".$goods_infos[$i][sellprice]."' company_name='".$goods_infos[$i][company_name]."' standard='".$goods_infos[$i][standard]."' place_name='".$goods_infos[$i][place_name]."' section_name='".$goods_infos[$i][section_name]."' pi_ix='".$goods_infos[$i][pi_ix]."' ps_ix='".$goods_infos[$i][ps_ix]."' stock='".$goods_infos[$i][stock]."' vdate='".$goods_infos[$i][vdate]."' expiry_date='".$goods_infos[$i][expiry_date]."' name=gid[] value='".$goods_infos[$i][gid]."' surtax_div='".$goods_infos[$i][surtax_div]."' surtax_text='".getSurTaxDiv($goods_infos[$i][surtax_div], "surtax_div","","text")."' change_amount='".$goods_infos[$i][change_amount]."'>
								</td>
								<td class='list_box_td list_bg_gray'>".$goods_infos[$i][gcode]."</td>
								<td class='list_box_td point helpcloud'  help_height='35' help_html='클릭시 자동으로 선택품목으로 등록되게 됩니다.' style='cursor:pointer;' style='text-align:center;cursor:pointer;' onclick=\"opener.GoodsSelect('".$goods_infos[$i][gid]."','".$goods_infos[$i][gname]."','".$goods_infos[$i][unit]."','".getUnit($goods_infos[$i][unit], "basic_unit","","text")."','".$goods_infos[$i][standard]."','".$goods_infos[$i][buying_price]."','".$goods_infos[$i][company_name]."','".$goods_infos[$i][place_name]."','".$goods_infos[$i][section_name]."','".$goods_infos[$i][pi_ix]."','".$goods_infos[$i][ps_ix]."','".$goods_infos[$i][vdate]."','".$goods_infos[$i][expiry_date]."','".$goods_infos[$i][stock]."','".$goods_infos[$i][offline_wholesale_price]."','".$goods_infos[$i][sellprice]."','".$goods_infos[$i][surtax_div]."','".getSurTaxDiv($goods_infos[$i][surtax_div], "surtax_div","","text")."','".$goods_infos[$i][change_amount]."');\">".$goods_infos[$i][gid]."</td>
								<td class='list_box_td list_bg_gray' style='text-align:left;padding:0px 5px;' >".$goods_infos[$i][gname]."</td>
								<td class='list_box_td point' >".getUnit($goods_infos[$i][unit], "basic_unit","","text")."</td>
								<td class='list_box_td ' >".$goods_infos[$i][standard]."</td>
								<td class='list_box_td' >".getSurTaxDiv($goods_infos[$i][surtax_div], "surtax_div","","text")."</td>
								<td class='list_box_td' >".number_format($goods_infos[$i][buying_price])."</td>
								<td class='list_box_td' >".number_format($goods_infos[$i][sellprice])."</td>
								<td class='list_box_td point' >".$goods_infos[$i][stock]."</td>
							</tr>";
		}else if($page_type == "delivery"){
		$Contents .= "<tr height=25 >
								<td class='list_box_td' >
									<input type=checkbox class='nonborder gid' id='gid' unit='".$goods_infos[$i][unit]."' unit_text='".getUnit($goods_infos[$i][unit], "basic_unit","","text")."'  gname='".$goods_infos[$i][gname]."' buying_price='".$goods_infos[$i][buying_price]."'  wholesale_price='".$goods_infos[$i][wholesale_price]."' offline_wholesale_price='".$goods_infos[$i][offline_wholesale_price]."' sellprice='".$goods_infos[$i][sellprice]."' company_name='".$goods_infos[$i][company_name]."' standard='".$goods_infos[$i][standard]."' place_name='".$goods_infos[$i][place_name]."' section_name='".$goods_infos[$i][section_name]."' pi_ix='".$goods_infos[$i][pi_ix]."' ps_ix='".$goods_infos[$i][ps_ix]."' stock='".$goods_infos[$i][stock]."' vdate='".$goods_infos[$i][vdate]."' expiry_date='".$goods_infos[$i][expiry_date]."' name=gid[] value='".$goods_infos[$i][gid]."' surtax_div='".$goods_infos[$i][surtax_div]."' surtax_text='".getSurTaxDiv($goods_infos[$i][surtax_div], "surtax_div","","text")."' >
								</td>
								<td class='list_box_td list_bg_gray'>".$goods_infos[$i][gcode]."</td>
								<td class='list_box_td point helpcloud'  help_height='35' help_html='클릭시 자동으로 선택품목으로 등록되게 됩니다.' style='cursor:pointer;' style='text-align:center;cursor:pointer;' onclick=\"opener.GoodsSelect('".$goods_infos[$i][gid]."','".$goods_infos[$i][gname]."','".$goods_infos[$i][unit]."','".getUnit($goods_infos[$i][unit], "basic_unit","","text")."','".$goods_infos[$i][standard]."','".$goods_infos[$i][buying_price]."','".$goods_infos[$i][company_name]."','".$goods_infos[$i][place_name]."','".$goods_infos[$i][section_name]."','".$goods_infos[$i][pi_ix]."','".$goods_infos[$i][ps_ix]."','".$goods_infos[$i][vdate]."','".$goods_infos[$i][expiry_date]."','".$goods_infos[$i][stock]."','".$goods_infos[$i][offline_wholesale_price]."','".$goods_infos[$i][sellprice]."','".$goods_infos[$i][surtax_div]."','".getSurTaxDiv($goods_infos[$i][surtax_div], "surtax_div","","text")."','".$goods_infos[$i][change_amount]."');\">".$goods_infos[$i][gid]."</td>
								<td class='list_box_td list_bg_gray' style='text-align:left;padding:0px 5px;' >".$goods_infos[$i][gname]."</td>
								<td class='list_box_td point' >".getUnit($goods_infos[$i][unit], "basic_unit","","text")."</td>
								<td class='list_box_td ' >".$goods_infos[$i][standard]."</td>
								<td class='list_box_td' >".getSurTaxDiv($goods_infos[$i][surtax_div], "surtax_div","","text")."</td>
								<td class='list_box_td' >".number_format($goods_infos[$i][buying_price])."</td>
								<td class='list_box_td' >".number_format($goods_infos[$i][sellprice])."</td>
								<td class='list_box_td point' >".$goods_infos[$i][stock]."</td>
							</tr>";
		}else{
	$Contents .= "<tr height=25 >
								<td class='list_box_td' >
									<input type=checkbox class='nonborder gid' id='gid' unit='".$goods_infos[$i][unit]."' unit_text='".getUnit($goods_infos[$i][unit], "basic_unit","","text")."'  gname='".$goods_infos[$i][gname]."' buying_price='".$goods_infos[$i][buying_price]."' wholesale_price='".$goods_infos[$i][wholesale_price]."'  offline_wholesale_price='".$goods_infos[$i][offline_wholesale_price]."'  sellprice='".$goods_infos[$i][sellprice]."' company_name='".$goods_infos[$i][company_name]."' standard='".$goods_infos[$i][standard]."' place_name='".$goods_infos[$i][place_name]."' section_name='".$goods_infos[$i][section_name]."' pi_ix='".$goods_infos[$i][pi_ix]."' ps_ix='".$goods_infos[$i][ps_ix]."' stock='".$goods_infos[$i][stock]."' vdate='".$goods_infos[$i][vdate]."' expiry_date='".$goods_infos[$i][expiry_date]."' name=gid[] value='".$goods_infos[$i][gid]."' surtax_div='".$goods_infos[$i][surtax_div]."' surtax_text='".getSurTaxDiv($goods_infos[$i][surtax_div], "surtax_div","","text")."' lately_price ='".$goods_infos[$i][lately_price]."' gcode='".$goods_infos[$i][gcode]."' >
								</td>
								<td class='list_box_td list_bg_gray'>".$goods_infos[$i][gcode]."</td>
								<td class='list_box_td point helpcloud'  help_height='35' help_html='클릭시 자동으로 선택품목으로 등록되게 됩니다.' style='cursor:pointer;' onclick=\"opener.GoodsSelect('".$goods_infos[$i][gid]."','".$goods_infos[$i][gname]."','".$goods_infos[$i][unit]."','".getUnit($goods_infos[$i][unit], "basic_unit","","text")."','".$goods_infos[$i][standard]."','".$goods_infos[$i][buying_price]."','".$goods_infos[$i][company_name]."','".$goods_infos[$i][place_name]."','".$goods_infos[$i][section_name]."','".$goods_infos[$i][pi_ix]."','".$goods_infos[$i][ps_ix]."','".$goods_infos[$i][vdate]."','".$goods_infos[$i][expiry_date]."','".$goods_infos[$i][stock]."','".$goods_infos[$i][wholesale_price]."','".$goods_infos[$i][sellprice]."','".$goods_infos[$i][surtax_div]."','".getSurTaxDiv($goods_infos[$i][surtax_div], "surtax_div","","text")."','".$goods_infos[$i][lately_price]."','".$goods_infos[$i][change_amount]."','".$goods_infos[$i][gcode]."');\">".$goods_infos[$i][gid]."</td>
								<td class='list_box_td list_bg_gray' style='text-align:left;padding:0px 5px;' >".$gname."</td>
								<td class='list_box_td point' >".getUnit($goods_infos[$i][unit], "basic_unit","","text")."</td>
								<td class='list_box_td ' >".$goods_infos[$i][standard]."</td>
								<!--td class='list_box_td' >".getSurTaxDiv($goods_infos[$i][surtax_div], "surtax_div","","text")."</td-->
								<!--td class='list_box_td ' >".$goods_infos[$i][vdate]."</td-->
								<td class='list_box_td ' >".$goods_infos[$i][expiry_date]."</td>
								<td class='list_box_td ' >".$goods_infos[$i][company_name]."</td>
								<td class='list_box_td ' nowrap>".$goods_infos[$i][place_name]." </td>
								<td class='list_box_td ' >".$goods_infos[$i][section_name]."</td>
								<!--td class='list_box_td' >".number_format($goods_infos[$i][offline_wholesale_price])."</td-->
								<td class='list_box_td' >".number_format($goods_infos[$i][wholesale_price])."</td>
								<td class='list_box_td' >".number_format($goods_infos[$i][sellprice])."</td>
								<td class='list_box_td point' >".$goods_infos[$i][stock]."</td>
							</tr>";
		}
	}
}else{
	$Contents .= "<tr align=center height=30>
				<td colspan=13 align=center> 품목정보가 존재 하지 않습니다.</td>
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
		<td align=left style='padding:10px 10px 0 5px' >
		<img src='../images/".$admininfo["language"]."/btn_goods_intoon.gif' border='0' align='absmiddle' onclick='GoodsSelectAll()' style='cursor:pointer;'>
		<!--div onclick='GoodsSelectAll()' style='display:inline-block;border:1px solid gray;padding:5px;width:100px;text-align:center;cursor:pointer;'>선택품목 등록하기</div> <!--div style='display:inline-block'><input type=checkbox name='stock_save' id='stock_save'><label for='stock_save'>품목 전체변경</label></div-->
		</td>
		<td align=right style='padding:10px 0 0 0' >
			".$str_page_bar."
		</td>
	</tr>
	<tr>
		<td align=left style='padding:10px 10px' colspan=2>";

$help_text = "
<table cellpadding=2 cellspacing=0 class='small' style='line-height:120%' >
	<col width=8>
	<col width=*>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td>품목코드를 클릭하시면 해당 품목정보가 대장에 반영되게 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td>선택하고자 하는 품목정보를 검색후 <u>선택품목 등록하기</u> 를 이용해서 품목정보를 자동으로 구성하실 수 있습니다. </td></tr>
</table>
";



$Contents .= HelpBox("품목선택", $help_text,"100");
$Contents .= "
		</td>
	</tr>
</TABLE></form>
";




$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "품목선택";
$P->NaviTitle = "품목선택";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>