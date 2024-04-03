<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../inventory/inventory.lib.php");
//auth(8);
//print_r($admininfo);


if($_COOKIE[inventory_goods_max_limit]){
	$max = $_COOKIE[inventory_goods_max_limit]; //페이지당 갯수
}else{
    if($_GET['max']){
        $max = $_GET['max'];
    }else{
        $max = 20;
    }

}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;

if($admininfo[admin_level] == 9){
	$where = "where g.gid Is NOT NULL ";

	if($admininfo[mem_type] == "MD"){
		$where .= " and g.admin in (".getMySellerList($admininfo[charger_ix]).") ";
	}

}else{
	$where = "where g.gid Is NOT NULL and g.admin ='".$admininfo[company_id]."' ";
}


if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
	//다중검색 시작 2014-04-10 이학봉
	if($search_text != ""){
		if(strpos($search_text,",") !== false){
			$search_array = explode(",",$search_text);
			$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
			$where .= "and ( ";
			$count_where .= "and ( ";
			for($i=0;$i<count($search_array);$i++){
				$search_array[$i] = trim($search_array[$i]);
				if($search_array[$i]){
					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
						$count_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
						$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
			}
			$where .= ")";
			$count_where .= ")";
		}else if(strpos($search_text,"\n") !== false){//\n
			$search_array = explode("\n",$search_text);
			$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
			$where .= "and ( ";
			$count_where .= "and ( ";

			for($i=0;$i<count($search_array);$i++){
				$search_array[$i] = trim($search_array[$i]);
				if($search_array[$i]){
					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
						$count_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
						$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
			}
			$where .= ")";
			$count_where .= ")";
		}else{
			$where .= " and ".$search_type." = '".trim($search_text)."'";
			$count_where .= " and ".$search_type." = '".trim($search_text)."'";
		}
	}
}else{
	if($search_type !="" && $search_text != ""){
		$where .= "and ".$search_type." LIKE '%".$search_text."%' ";
	}
}



if($item_account != ""){
	$where .= "and g.item_account = '".$item_account."' ";
}

if($is_use !=''){
    $where .= "and g.is_use = '".$is_use."' ";
}

if(is_array($status)){
	for($i=0;$i < count($status);$i++){
		if($status[$i] != ''){
			if($status_str == ""){
				$status_str .= "'".$status[$i]."'";
			}else{
				$status_str .= ", '".$status[$i]."' ";
			}
		}
	}

	if($status_str != ""){
		$where .= "and g.status in ($status_str) ";
	}
}else{
	if($status){
		$where .= "and g.status = '$status' ";
	}
}

/*
if($stock_status == "soldout"){
	$stock_where = "and (stock = 0 or option_stock_yn = 'N') ";
}else if($stock_status == "shortage"){
	$stock_where = "and (stock < safestock or option_stock_yn = 'R') ";
}else if($stock_status == "surplus"){
	$stock_where = "and (stock > safestock or option_stock_yn = 'Y')";
}
*/

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
	$where .= " and g.cid LIKE '".substr($cid2,0,$cut_num)."%' ";
}

if($ci_ix != ""){
	$where .= " and g.ci_ix = '".$ci_ix."' ";
}
if($mode=="search"){

	if($view_goods_unit==1){

		$sql = "select count(*) as total
			from inventory_goods g 
			left join inventory_goods_unit gu on (g.gid=gu.gid)
			$where 
			 $stock_where ";
		
		$db->query($sql);
		$db->fetch();
		$total = $db->dt[total];

	}else{

		$sql = "select g.gid
			from inventory_goods g 
			left join inventory_goods_unit gu on (g.gid=gu.gid)
			$where 
			$stock_where 
			group by g.gid ";

		$db->query($sql);
		$total = $db->total;
	}


	$orderbyString = "order by g.regdate desc";

	if($mode == "excel"){
		
		if($view_goods_unit==1){
			$sql = "select *
				from inventory_goods g 
				left join inventory_goods_unit gu on (g.gid=gu.gid)
				$where   
				 $stock_where $orderbyString ";
		}else{
			$sql = "select g.*
				from inventory_goods g 
				left join inventory_goods_unit gu on (g.gid=gu.gid)
				$where   
				 $stock_where $orderbyString
				group by g.gid ";
		}

	}else if($mode=="update_excel"){

		$sql = "select data.* ,
			(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.company_id   limit 1) as company_name 
			from (
				select g.*, gu.unit ,gu.gu_ix
				from inventory_goods g 
				left join inventory_goods_unit gu on (g.gid=gu.gid)
				$where
				$stock_where 
			) data ";
	}else{

		if($view_goods_unit==1){
			$sql = "select data.* ,
				(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.ci_ix   limit 1) as company_name 
				from (
					select 
						g.*,
						date_format(g.regdate,'%Y-%m-%d') as g_regdate, gu.unit ,gu.gu_ix
					from inventory_goods g
					left join inventory_goods_unit gu on (g.gid=gu.gid)
					$where
					$stock_where 
					$orderbyString 
					LIMIT $start, $max
				) data
				 ";
		}else{
			$sql = "select data.* ,
				(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.ci_ix   limit 1) as company_name 
				from (
					select
						g.*,
						date_format(g.regdate,'%Y-%m-%d') as g_regdate  , gu.unit ,gu.gu_ix
					from inventory_goods g 
					left join inventory_goods_unit gu on (g.gid=gu.gid)
					$where
					$stock_where 
					group by g.gid  
					$orderbyString 
					LIMIT $start, $max
				) data
				 ";
		}

	}

	$db->query($sql);

	$goods_infos = $db->fetchall();

}

if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
	}
	$str_page_bar = page_bar($total, $page, $max, $query_string,"");
}else{
	$str_page_bar = page_bar($total, $page, $max, "&max=$max","");
}


?>