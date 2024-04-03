<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("./inventory.lib.php");


//auth(8);
//print_r($admininfo);
if($_COOKIE[inventory_goods_max_limit]){
    $max = $_COOKIE[inventory_goods_max_limit]; //페이지당 갯수
}else{
    $max = 50;
}

if ($page == ''){
    $start = 0;
    $page  = 1;
}else{
    $start = ($page - 1) * $max;
}

if(!$info_type){
    $info_type = "all";
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

if($company_id != ""){
    $where .= "and pi.company_id = '".$company_id."' ";
}

if($pi_ix != ""){
    $where .= "and pi.pi_ix = '".$pi_ix."' ";
}

if($ps_ix != ""){
    $where .= "and ps.ps_ix = '".$ps_ix."' ";
}

if($item_acccount != ""){
    $where .= "and g.item_acccount = '".$item_acccount."' ";
}

if($is_use !=""){
    $where .= "and g.is_use = '".$is_use."' ";
}

if($item_account !=""){
    $where .= "and g.item_account = '".$item_account."' ";
}


if($_COOKIE[view_shotage_goods] != 1){
    $stock_join_type = " right join ";
    //$where .= "and ips.stock > 0 "; //// 가용재고 처리를 위해서 주석처리
    $where .= "and ips.stock != 0 "; //// 재고가 없는것들은 제외!
}else{
    $stock_join_type = " left join ";
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

/*불량창고-온라인상품이 아닌경우 재고에서 빠져야 함 jk150623*/
if($info_type == "all"){
    $sql="select pi_ix from inventory_place_info where online_place_yn = 'Y' ";
    $db->query($sql);
    if($db->total){
        $place = $db->getrows();
        $stock_join_where = " and ips.pi_ix in ('".implode("','",$place[0])."')";
    }
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
    $where .= " and g.cid LIKE '".substr($cid2,0,$cut_num)."%' ";
}

if($start_stock != '' && $end_stock != ''){
    $search_stock_where .= " having stock  between '".$start_stock."' and '".$end_stock."' ";
}


if($info_type == "warehouse"){
    if($groupby_pi_ix || $groupby_ps_ix){

        if($groupby_pi_ix && $groupby_ps_ix){
            $groupby_str .= " group by  ips.pi_ix , ips.ps_ix  ";
        }else if($groupby_pi_ix){
            $groupby_str .= " group by  ips.pi_ix   ";
        }else if($groupby_ps_ix){
            $groupby_str .= " group by  ips.ps_ix    ";
        }
    }else{
        //$groupby_str = "group by  ips.pi_ix , ips.ps_ix ";
        $groupby_str = "group by  ips.pi_ix";
    }

    $where .= " and g.is_use = 'Y' ";

    $sql = "select 
						count(*) as total
					from 
						(select g.cid,g.gname, g.gcode, g.admin, (gu.buying_price*ips.stock) as buying_price,
							gu.sellprice , g.item_account , g.basic_unit, g.ci_ix, pi.pi_ix, 
							pi.place_name, ps.ps_ix,ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.unit, g.gid, gu.safestock, gu.sell_ing_cnt
						from 
							inventory_goods g 
							right join inventory_goods_unit gu  on g.gid =gu.gid
							".$stock_join_type." inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
							left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix	
							left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
						$where    
							$stock_where 
							 $groupby_str) data
					 ";

}else if($info_type == "category"){
    if($groupby_depth){
        $groupby_str = "group by cid ";
    }else{
        //$groupby_str = "group by substr(cid,1,".($groupby_depth*3).") ";
        $groupby_str = "group by substr(cid,1,3) ";
    }

    $where .= " and g.is_use = 'Y' ";

    $sql = "select count(*) as total from 
					(select g.cid,g.gname, g.gcode, g.admin, (gu.buying_price*ips.stock) as buying_price, gu.sellprice , g.item_account , g.basic_unit, g.ci_ix, g.pi_ix, 
					pi.place_name, ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.unit, g.gid,  gu.safestock, gu.sell_ing_cnt
					from inventory_goods g 
					right join inventory_goods_unit gu  on g.gid =gu.gid				
					".$stock_join_type."   inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
					left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix	
					left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
					$where    
					 $stock_where 
					 $groupby_str
					  ) data
					 ";
}else if($info_type == "detail"){
    $sql = "select count(*) as total from
			(select g.cid,g.gname, g.gcode, g.admin, g.item_account , g.basic_unit, g.ci_ix, g.pi_ix, 
			pi.place_name, ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.unit, g.gid, gu.buying_price, gu.safestock , gu.sell_ing_cnt, ips.vdate, ips.expiry_date
			from inventory_goods g 
			right join inventory_goods_unit gu  on g.gid =gu.gid			
			".$stock_join_type."  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
			left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix	
			left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
			$where    
			 $stock_where 
			 group by gu.gid , ips.unit, ips.pi_ix, ips.ps_ix, ips.expiry_date) data ";
}else{
    $sql = "select count(*) as total from
			(select g.cid,g.gname, g.gcode, g.admin, g.item_account , g.basic_unit, g.ci_ix, g.pi_ix, 
			pi.place_name, ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.unit, g.gid, gu.buying_price, gu.safestock , gu.sell_ing_cnt, ips.vdate, ips.expiry_date
			from inventory_goods g 
			right join inventory_goods_unit gu  on g.gid =gu.gid			
			".$stock_join_type."  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
			left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix	
			left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
			$where    
			 $stock_where 
			 group by gu.gid , gu.unit
			 $search_stock_where
			 ) data ";

}


$db->query($sql);
$db->fetch();
$total = $db->dt[total];

//echo $total;
//echo $db->total;
//exit;

if($orderby != "" && $ordertype != ""){
    if(substr_count($orderby,"company_name") > 0){
        $orderbyString2 = " order by $orderby $ordertype ";
    }else{
        $orderbyString = " order by $orderby $ordertype ";
    }
}else{
    if($info_type == "category"){
        $orderbyString = "order by g.cid";
    }else{
        $orderbyString = "order by g.regdate desc";
    }
}


if($info_type == "warehouse"){
    if($groupby_pi_ix || $groupby_ps_ix){

        if($groupby_pi_ix && $groupby_ps_ix){
            $groupby_str = " group by  ips.pi_ix , ips.ps_ix  ";
        }else if($groupby_pi_ix){
            $groupby_str = " group by  ips.pi_ix   ";
        }else if($groupby_ps_ix){
            $groupby_str = " group by  ips.ps_ix    ";
        }
    }else{
        //$groupby_str .= " group by  ips.pi_ix , ips.ps_ix  ";
        $groupby_str = "group by  ips.pi_ix";
    }
    $sql = "select data.*, 
					(select IFNULL(sum(od.cnt),0) as order_ing_cnt from inventory_order o, inventory_order_detail od where o.ioid = od.ioid and od.gid = data.gid and od.unit = data.unit and o.status not in ('AC','ACC','ORC','OCC','WC','GA')) as order_ing_cnt ,
					(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.company_id   limit 1) as company_name
					from 
					(select g.cid,g.gname, g.gcode, g.admin, sum(ips.stock*gu.avg_price) as stock_assets, gu.sellprice , g.item_account , g.basic_unit, g.ci_ix, pi.pi_ix, g.color, g.size,
					pi.place_name,  pi.company_id, ps.ps_ix, ps.section_name, ifnull(sum(ips.stock),'0') as stock , gu.unit, g.gid, g.standard, gu.safestock, gu.sell_ing_cnt, gu.order_cnt, gu.buying_price,gu.barcode, gu.total_stock, gu.avg_price
					from inventory_goods g 
					right join inventory_goods_unit gu  on g.gid =gu.gid
					".$stock_join_type."   inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
					left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
					left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
					$where    
					$stock_where 
					$groupby_str
					$orderbyString 
					".($mode=='excel' || $mmode=='report'? "":"LIMIT $start, $max").") data $orderbyString2
					";

}else if($info_type == "category"){

    if($groupby_depth){
        $groupby_str = "group by ifnull(cid,'') ";
    }else{
        //$groupby_str = "group by substr(cid,1,".($groupby_depth*3).") ";
        $groupby_str = "group by substr(ifnull(cid,''),1,3) ";
    }
    $sql = "select data.*, 
					(select IFNULL(sum(od.cnt),0) as order_ing_cnt from inventory_order o, inventory_order_detail od where o.ioid = od.ioid and od.gid = data.gid and od.unit = data.unit and o.status not in ('AC','ACC','ORC','OCC','WC','GA')) as order_ing_cnt 
					from 
					(select ifnull(g.cid,'')as cid,g.gname, g.gcode, g.admin, sum(ips.stock*gu.avg_price) as stock_assets, gu.sellprice , g.item_account , g.basic_unit, g.ci_ix, g.pi_ix, g.color, g.size,
					pi.place_name, pi.company_id, ps.section_name, ifnull(sum(ips.stock),0) as stock , gu.unit, g.gid, g.standard, gu.safestock, gu.sell_ing_cnt, gu.order_cnt, gu.buying_price, gu.barcode, gu.total_stock, gu.avg_price
					from inventory_goods g 
					right join inventory_goods_unit gu  on g.gid =gu.gid			
					".$stock_join_type."   inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
					left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix	
					left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
					$where    
					 $stock_where 
					 $groupby_str
					 $orderbyString 
					 ".($mode=='excel' || $mmode=='report'? "":"LIMIT $start, $max").") data $orderbyString2
					 ";
}else if($info_type == "detail"){
    $sql = "select data.*, 
					(select IFNULL(sum(od.cnt),0) as order_ing_cnt from inventory_order o, inventory_order_detail od where o.ioid = od.ioid and od.gid = data.gid and od.unit = data.unit and o.status not in ('AC','ACC','ORC','OCC','WC','GA')) as order_ing_cnt ,
					(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.company_id   limit 1) as company_name,
					ifnull((select bp.ps_ix from inventory_goods_basic_place bp where data.gid=bp.gid and data.unit=bp.unit and data.company_id=bp.company_id and data.pi_ix=bp.pi_ix),0) as basic_ps_ix
					from 
					(select g.cid,g.gname, g.gcode, g.admin,  sum(ips.stock*gu.avg_price) as stock_assets, g.item_account , g.basic_unit, gu.sellprice, g.ci_ix, g.color, g.size,
					pi.place_name, pi.company_id,  ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.gu_ix, gu.unit, g.gid,  g.standard, gu.safestock, gu.sell_ing_cnt, gu.order_cnt, gu.buying_price, gu.barcode, ips.vdate, ips.expiry_date, ips.pi_ix, ips.ps_ix, gu.total_stock, gu.avg_price
					from inventory_goods g 
					right join inventory_goods_unit gu  on g.gid =gu.gid
					".$stock_join_type."  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
					left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix			
					left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
					
					$where    
					 $stock_where 
					 group by gu.gid , ips.unit, ips.pi_ix, ips.ps_ix, ips.expiry_date
					 $orderbyString 
					 ".($mode=='excel' || $mmode=='report'? "":"LIMIT $start, $max").") data $orderbyString2 ";
}else{


    $sql = "select data.*, 
					(select IFNULL(sum(od.cnt),0) as order_ing_cnt from inventory_order o, inventory_order_detail od where o.ioid = od.ioid and od.gid = data.gid and od.unit = data.unit and o.status not in ('AC','ACC','ORC','OCC','WC','GA')) as order_ing_cnt ,
					(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.company_id   limit 1) as company_name
					from 
					(select g.cid,g.gname, g.gcode, g.admin,  sum(ips.stock*gu.avg_price) as stock_assets, g.item_account , g.basic_unit, gu.sellprice, g.ci_ix, g.pi_ix, g.color, g.size,
					pi.place_name, pi.company_id,  ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.gu_ix,gu.unit, g.gid, g.standard, gu.safestock, gu.sell_ing_cnt, gu.order_cnt, gu.buying_price, gu.barcode, ips.vdate, gu.total_stock, gu.avg_price
					from inventory_goods g 
					right join inventory_goods_unit gu  on g.gid =gu.gid							
					".$stock_join_type."  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
					left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix			
					left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
					$where    
					 $stock_where 
					 group by gu.gid , gu.unit
					 $search_stock_where
					 $orderbyString 
					 ".($mode=='excel' || $mmode=='report' ? "":"LIMIT $start, $max").") data $orderbyString2";

}

//}
//echo "<br><br>";
//echo nl2br($sql);
//exit;
$db->query($sql);
$goods_infos = $db->fetchall("object");

if($mode == "excel"){

    ini_set('memory_limit','2048M');
    set_time_limit(9999999);

    include("excel_out_columsinfo.php");
    $sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='inventory_excel_".$info_type."' ";

    $db->query($sql);
    $db->fetch();
    $stock_report_excel = $db->dt[conf_val];

    $sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='inventory_excel_checked_".$info_type."' ";
    //echo $sql;
    $db->query($sql);
    $db->fetch();
    $stock_report_excel_checked = $db->dt[conf_val];

    $check_colums = unserialize(stripslashes($stock_report_excel_checked));
    //print_r($check_colums);
    //print_r($colums);
    //exit;
    $columsinfo = $colums;

    include '../include/phpexcel/Classes/PHPExcel.php';
    PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

    date_default_timezone_set('Asia/Seoul');

    $inventory_excel = new PHPExcel();

    // 속성 정의
    $inventory_excel->getProperties()->setCreator("포비즈 코리아")
        ->setLastModifiedBy("Mallstory.com")
        ->setTitle("accounts plan price List")
        ->setSubject("accounts plan price List")
        ->setDescription("generated by forbiz korea")
        ->setKeywords("mallstory")
        ->setCategory("accounts plan price List");
    $col = 'A';
    if(is_array($check_colums)) {
        foreach ($check_colums as $key => $value) {
            $inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
            $col++;

            //xlsWriteLabel(0,$j,$columsinfo[$value][title]);
            //$j++;
        }
    }
    /*
    $inventory_excel->getActiveSheet(0)->setCellValue('A' . 1, "번호");
    $inventory_excel->getActiveSheet(0)->setCellValue('B' . 1, "재고품목아이디");
    $inventory_excel->getActiveSheet(0)->setCellValue('C' . 1, "카테고리");
    $inventory_excel->getActiveSheet(0)->setCellValue('D' . 1, "품목명");
    $inventory_excel->getActiveSheet(0)->setCellValue('E' . 1, "규격");
    $inventory_excel->getActiveSheet(0)->setCellValue('F' . 1, "단품(규격)코드");
    $inventory_excel->getActiveSheet(0)->setCellValue('G' . 1, "보관장소");
    $inventory_excel->getActiveSheet(0)->setCellValue('H' . 1, "재고");
    $inventory_excel->getActiveSheet(0)->setCellValue('I' . 1, "출고예정재고");
    $inventory_excel->getActiveSheet(0)->setCellValue('J' . 1, "안전재고");
    */

    $before_pid = "";

    if($info_type == "warehouse" || $info_type == "category"){
        for ($i = 0; $i < count($goods_infos); $i++)
        {
            $stock_assets_sum += $goods_infos[$i][stock_assets];
            $stock_sum += $goods_infos[$i][stock];
            $stock_assets_total += $goods_infos[$i][stock_assets];
            $order_cnt_sum += $goods_infos[$i][order_cnt];
        }
    }

    for ($i = 0; $i < count($goods_infos); $i++)
    {
        /*
        if(file_exists(InventoryPrintImage($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product", $goods_infos[$i][id], "m"))){
            $img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/product", $goods_infos[$i][id], "m");
        }else{
            $img_str = "../image/no_img.gif";
        }
        */

        $j="A";
        if(is_array($check_colums)) {
            foreach ($check_colums as $key => $value) {
                if ($key == "item_account") {
                    $value_str = $ITEM_ACCOUNT[$goods_infos[$i][item_account]];
                } else if ($key == "ci_ix") {
                    $value_str = strip_tags(SelectSupplyCompany($_SESSION["admininfo"]["company_id"], $goods_infos[$i][ci_ix], 'ci_ix', 'text', 'false'));
                } else if ($key == "pi_ix" && $info_type != "warehouse") {
                    $value_str = $goods_infos[$i][place_name]; //strip_tags(SelectSupplyCompany($goods_infos[$i][ci_ix],'ci_ix','text','false'));
                } else if ($key == "item_barcode") {
                    $value_str = $goods_infos[$i][barcode] . " ";
                } else if ($key == "cid") {
                    $value_str = $goods_infos[$i][cid] . " ";
                } else if ($key == "buying_price_share") {
                    if ($stock_assets_sum > 0) {
                        $value_str = number_format($goods_infos[$i][stock_assets] / $stock_assets_sum * 100, 2);
                    } else {
                        $value_str = 0;
                    }
                } else if ($key == "stock_share") {
                    if ($stock_sum > 0) {
                        $value_str = number_format($goods_infos[$i][stock] / $stock_sum * 100, 2);
                    } else {
                        $value_str = 0;
                    }
                } else if ($key == "order_share") {
                    if ($order_cnt_sum > 0) {
                        $value_str = number_format($goods_infos[$i][order_cnt] / $order_cnt_sum * 100, 2);
                    } else {
                        $value_str = 0;
                    }
                } else if ($key == "wantage_stock") {
                    $value_str = $goods_infos[$i][stock] - $goods_infos[$i][sell_ing_cnt] + $goods_infos[$i][order_ing_cnt] - $goods_infos[$i][safestock];
                    if ($value_str > 0) {
                        $value_str = 0;
                    }
                } else if ($key == "cname") {
                    $value_str = getIventoryCategoryPathByAdmin($goods_infos[$i][cid], 4);

                } else if ($key == "id") {
                    $value_str = $goods_infos[$i][gu_ix];

                } else if ($key == "unit" || $key == "basic_unit") {
                    $value_str = $ITEM_UNIT[$goods_infos[$i][$key]];

                } else if($key == 'soldout_text'){
                    $soldOutText = "";
                    if(($goods_infos[$i][stock]-$goods_infos[$i][sell_ing_cnt]) < 1){
                        $soldOutText = "[품절]";
                    }
                    $value_str = $soldOutText;
                } else {
                    $value_str = $goods_infos[$i][$value];//$db1->dt[$value];
                }
                $inventory_excel->getActiveSheet()->setCellValue($j . ($z + 2), $value_str);
                $j++;
            }
        }
        $z++;


        /*
        $inventory_excel->getActiveSheet()->setCellValue('A' . ($i + 2), ($i + 1));
        $inventory_excel->getActiveSheet()->setCellValue('B' . ($i + 2), $goods_infos[$i][gid]);
        $inventory_excel->getActiveSheet()->setCellValue('C' . ($i + 2), getIventoryCategoryPathByAdmin($goods_infos[$i][cid], 4));
        $inventory_excel->getActiveSheet()->setCellValue('D' . ($i + 2), $goods_infos[$i][gname]);
        $inventory_excel->getActiveSheet()->setCellValue('E' . ($i + 2), $goods_infos[$i][standard]);
        $inventory_excel->getActiveSheet()->setCellValue('F' . ($i + 2), $goods_infos[$i][item_code]);
        $inventory_excel->getActiveSheet()->setCellValue('G' . ($i + 2), $goods_infos[$i][place_name]);
        $inventory_excel->getActiveSheet()->setCellValue('H' . ($i + 2), $goods_infos[$i][stock]);
        $inventory_excel->getActiveSheet()->setCellValue('I' . ($i + 2), $goods_infos[$i][sell_ing_cnt]);
        $inventory_excel->getActiveSheet()->setCellValue('J' . ($i + 2), $goods_infos[$i][safestock]);
        */

    }

    // 첫번째 시트 선택
    $inventory_excel->setActiveSheetIndex(0);

    // 너비조정
    $col = 'A';
    if(is_array($check_colums)) {
        foreach ($check_colums as $key => $value) {
            $inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
    }
    /*
    $inventory_excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $inventory_excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $inventory_excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $inventory_excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $inventory_excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $inventory_excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $inventory_excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    $inventory_excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
    $inventory_excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
    $inventory_excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
    //$inventory_excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
    //$inventory_excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
    */

    if(is_excel_csv()){
        header('Content-Type: application/vnd.ms-excel;');//charset=euckr
        header('Content-Disposition: attachment;filename="stock_report_'.$info_type.'.csv"');
        header('Cache-Control: max-age=0');
        //setlocale(LC_CTYPE, 'ko_KR.eucKR');
        //header("Content-charset=euckr");
        //header("Content-Description: PHP5 Generated Data");
        $objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'CSV');
        $objWriter->setUseBOM(true);
    }else{
        header('Content-Type: application/vnd.ms-excel;');
        header('Content-Disposition: attachment;filename="stock_report_'.$info_type.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
    }

    $objWriter->save('php://output');

    exit;
}
//print_r($_SERVER);

if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
    $query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
}else{
    $query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
}
$str_page_bar = page_bar($total, $page, $max, $query_string,"");



$Contents =	"
<script  id='dynamic'></script>
<table border=0 cellpadding=0 cellspacing=0 width='100%'>
			<tr>
			    <td align='left' colspan=4 > ".GetTitleNavigation("재고현황", "재고관리 > 재고현황")."</td>
			</tr>";
if($mmode != "report"){
    $Contents .=	"
			<tr>
				<td align='left' colspan=4 style='padding-bottom:15px;'>
					<div class='tab'>
					<table class='s_org_tab'>
						<tr>
							<td class='tab'>
								<table id='tab_00' ".(($info_type == "all" || $info_type == "") ? "class='on' ":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='stock_report.php?info_type=all'\">품목별 재고현황</td>
										<th class='box_03'></th>
									</tr>
								</table>
								<table id='tab_01'  ".(($info_type == "detail" ) ? "class='on' ":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='stock_report.php?info_type=detail'\">품목별 상세재고현황</td>
										<th class='box_03'></th>
									</tr>
								</table>
								<!-- 
								<table id='tab_01'  ".(($info_type == "warehouse" ) ? "class='on' ":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='stock_report.php?info_type=warehouse'\">사업장/창고별 재고현황</td>
										<th class='box_03'></th>
									</tr>
								</table>
								<table id='tab_01' ".(($info_type == "category") ? "class='on' ":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='stock_report.php?info_type=category'\">품목분류별 재고현황</td>
										<th class='box_03'></th>
									</tr>
								</table>
								 -->
							</td>
							<td align='right' style='text-align:right;vertical-align:bottom;padding:0 0 6px 4px;'>";
//								if($_SESSION["admininfo"]["charger_id"]=="forbiz" || date("Ymd")=="20150628" || date("Ymd")=="20150629"  || date("Ymd")=="20150631" ||$_SESSION["admininfo"]["charger_id"]=="nameter"){
//									$Contents .= "<input type='button' value='재고 초기화' onclick='inventory_initialization();' class='red' />";
//								}
    $Contents .=	"
							</td>
						</tr>
					</table>
				</div>
				</td>
			</tr>";
}else{

    if($info_type == "all"){
        if($company_id!=""){

            $title_add = SelectEstablishment($company_id,"company_id","text","false");

            if($company_id!="" && $pi_ix!=""){
                $title_add .= SelectInventoryInfo($company_id, $pi_ix,'pi_ix','text','false');
            }

            if($pi_ix!="" && $ps_ix!=""){
                $title_add .= SelectSectionInfo($pi_ix,$ps_ix,'ps_ix',"text","false");
            }
        }else{
            $title_add = "전체";
        }
    }else if($info_type == "category"){
        $title_add = "카테고리";
    }else if($info_type == "warehouse"){
        $title_add = "창고별";
    }else if($info_type == "detail"){

        if($company_id!=""){

            $title_add = SelectEstablishment($company_id,"company_id","text","false");

            if($company_id!="" && $pi_ix!=""){
                $title_add .= SelectInventoryInfo($company_id, $pi_ix,'pi_ix','text','false');
            }

            if($pi_ix!="" && $ps_ix!=""){
                $title_add .= SelectSectionInfo($pi_ix,$ps_ix,'ps_ix',"text","false");
            }
        }else{
            $title_add = "상세현황";
        }

    }else{
        $title_add = "전체";
    }
    $Contents .=	"
			<tr>
				<td align='left' colspan=2 style='padding-bottom:15px;vertical-align:bottom;'>
					<table width='500px' border='0' cellspacing='0' cellpadding='0' >
					<tr>
						<td width='10%' height='31' valign='middle' style='color:#000000;border-bottom:1px solid #efefef;font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-right:20px;' nowrap>
							<img src='../v3/images/common/arrow_icon02.gif' align=absmiddle> 실시간 재고현황 (".$title_add.")
						</td>
						<td width='90%' align='right' valign='middle' style='border-bottom:1px solid #efefef;'>
							&nbsp;$navigation
						</td>
					</tr>
					<tr height=30><td colspan=2>현황일자 : ".date("Y.m.d / H:i:s")."</td></tr>
					</table>
				</td>
				<td align='right' colspan=2 style='padding-bottom:15px;width:330px;'>
					<table cellpadding=0 cellspacing=0 border=0 width=330 align=right class='input_table_box'>
							<col width='33%'>
							<col width='33%'>
							<col width='33%'>						
							<tr height='30'>		
								<td class='list_box_td list_bg_gray' style='text-align:center; padding:5px;'><b> </b></td>		
								<td class='list_box_td list_bg_gray' style='text-align:center; padding:5px;'><b> </b></td>		
								<td class='list_box_td list_bg_gray' style='text-align:center; padding:5px;'><b> </b></td>						
							</tr>
							<tr height='60'>		
								<td class='list_box_td' style='text-align:center; padding:5px;'> </td>		
								<td class='list_box_td' style='text-align:center; padding:5px;'> </td>		
								<td class='list_box_td' style='text-align:center; padding:5px;'> </td>
							</tr>
					</table>
				</td>
			</tr>";

    if($info_type != "warehouse"){
        $Contents .=	"
				<tr>
					<td align='center' colspan=4 >
					".ItemSummary($info_type)."
					</td>
				</tr>
				";
    }

}

if($info_type == "all" || $info_type == ""){
    if($mmode != "report"){
        $Contents .=	"
			<tr>
			 	<td colspan=4 align=left style='padding-bottom:0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:2px 5px 2px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 현재재고현황</b> </span></b> </div>")."</td>
			 </tr>
			<tr>
				<td align='left' colspan=4 style='padding:0px;'> ".ItemSummary($info_type)."</td>
			</tr>
			<tr  >
				<td colspan=4>
					<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
					<input type='hidden' name='mode' value='search'>
					<input type='hidden' name='cid2' value='$cid2'>
					<input type='hidden' name='depth' value='$depth'>
					<input type='hidden' name='info_type' value='$info_type'>

					<!-- input type='hidden' name='sprice' value='0' />
					<input type='hidden' name='eprice' value='1000000'/ -->

					<table class='box_shadow' style='width:100%;height:20px' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05 align=center' style='padding:0px'>
								<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='input_table_box'>
									<col width='150' >
									<col width='*' >
									<col width='150' >
									<col width='*' >
									<tr>
										<td class='input_box_title'>  <b>선택된 품목분류</b>  </td>
										<td class='input_box_item' colspan=3><b id='select_category_path1'>".($search_text == "" ? getIventoryCategoryPathByAdmin($cid2, $depth)."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div></td>
									</tr>
									<tr>
										<td class='input_box_title'><b>품목분류</b></td>
										<td class='input_box_item' colspan=3>
											<table border=0 cellpadding=0 cellspacing=0>
												<tr>
													<td style='padding-right:5px;'>".getInventoryCategoryList("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
													<td style='padding-right:5px;'>".getInventoryCategoryList("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
													<td style='padding-right:5px;'>".getInventoryCategoryList("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
													<td>".getInventoryCategoryList("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<!--td class='input_box_title'>주거래처</td>
										<td class='input_box_item' >
											".SelectSupplyCompany($ci_ix,'ci_ix','select','false')."
										</td-->
										<td class='input_box_title'>사업장/창고</td>
										<td class='input_box_item' colspan=3>
											".SelectEstablishment($company_id,"company_id","select","false","onChange=\"loadPlace(this,'pi_ix')\" ")."
											".SelectInventoryInfo($company_id, $pi_ix,'pi_ix','select','false', "onChange=\"loadPlaceSection(this,'ps_ix')\" ")."
											".SelectSectionInfo($pi_ix,$ps_ix,'ps_ix',"select","false" )." 
										</td>										
									</tr>
									<tr>
										<td class='input_box_title'>품목계정</td>
										<td class='input_box_item' >
											".getItemAccount($item_account)."
										</td>
										<td class='input_box_title'>판매상태</td>
										<td class='input_box_item'>
											<input type=checkbox name='status[]' class=nonborder value='1' id='status_1' validation=false title='사용유무' ".CompareReturnValue("1",$status," checked")."><label for='status_1'>판매중</label>
											<input type=checkbox name='status[]' class=nonborder value='0' id='status_0' validation=false title='사용유무' ".CompareReturnValue("0",$status," checked")."><label for='status_0'>일시품절</label>
											<input type=checkbox name='status[]' class=nonborder value='2' id='status_2' validation=false title='사용유무' ".CompareReturnValue("2",$status," checked")."><label for='status_2'>단종(품절)</label>
										</td>
										<!--td class='input_box_title'>품목사용여부</td>
										<td class='input_box_item'>
											<input type=radio name='is_use' class=nonborder value='' id='is_use_A' validation=false title='사용유무' ".($is_use == "" ? "checked":"")."><label for='is_use_A'>전체</label>
											<input type=radio name='is_use' class=nonborder value='Y' id='is_use_Y' validation=false title='사용유무' ".($is_use == "Y" ? "checked":"")."><label for='is_use_Y'>사용</label>
											<input type=radio name='is_use' class=nonborder value='N' id='is_use_N' validation=false title='사용유무' ".($is_use == "N" ? "checked":"")."><label for='is_use_N'>미사용</label>
										</td-->
									</tr>
									<tr>
										<td class='input_box_title'>  <b>검색어</b>  
											<br/>
											<label for='mult_search_use'>(다중검색 체크)</label> <input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> 
										</td>
										<td class='input_box_item' valign='middle' style='padding-right:5px;' colspan='3'>
											<table cellpadding=0 cellspacing=0>
												<tr>
													<td>
														<select name='search_type'  style=\"font-size:12px;height:22px;min-width:140px;\">
															<option value='g.gcode' ".CompareReturnValue("g.gcode",$search_type).">대표코드</option>
															<option value='g.gid' ".CompareReturnValue("g.gid",$search_type).">품목코드</option>
															<option value='g.gname' ".CompareReturnValue("g.gname",$search_type).">품목명</option>
															<option value='gu.gu_ix' ".CompareReturnValue("gu.gu_ix",$search_type).">시스템코드</option>
															<option value='gu.barcode' ".CompareReturnValue("gu.barcode",$search_type).">바코드</option>
														</select>
													</td>
													<td style='padding-left:5px;'>
														<div id='search_text_input_div'>
															<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
														</div>
														<div id='search_text_area_div' style='display:none;'>
															<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
														</div>
													</td>
													<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td class='input_box_title'>재고수량</td>
										<td class='input_box_item' colspan=3>
											<input type='number' class='textbox' name='start_stock' value='".$start_stock."' style='width:50px' /> ~
											<input type='number' class='textbox' name='end_stock' value='".$end_stock."' style='width:50px' />
										</td>										
									</tr>
									<!--tr>
										<td class='input_box_title'><b>재고상태</b></td>
										<td class='input_box_item' colspan='3'>
										<input type='radio' name='stock_status' value='whole' id='owhole' ".CompareReturnValue("whole","$stock_status"," checked")."><label for='owhole'>전체</label>
										<input type='radio' name='stock_status' value='soldout' id='osoldout' ".CompareReturnValue("soldout","$stock_status"," checked")."><label for='osoldout'>품절</label>
										<input type='radio' name='stock_status' value='shortage' id='oshortage' ".CompareReturnValue("shortage","$stock_status"," checked")."><label for='oshortage'>부족</label>
										<input type='radio' name='stock_status' value='surplus' id='osurplus' ".CompareReturnValue("surplus","$stock_status"," checked")."><label for='osurplus'>여유</label>
										</td>
									</tr-->
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
			
			<tr>
			    <td colspan=1>

					<input type='checkbox' name='view_shotage_goods' id='view_shotage_goods' onclick=\"reloadView('complete')\" ".($_COOKIE[view_shotage_goods] == 1 ? "checked":"")." >
					<label for='view_shotage_goods'> 재고없는품목 포함</label>

					<!--<input type='button' id='erp_connect' value='ERP 매출연동' onclick=\"javascript:PoPWindow3('../../openapi/erp_data.php?type=order',970,800,'stock_report')\">
					<input type='button' id='erp_connect' value='ERP 매출삭제' onclick=\"javascript:PoPWindow3('../../openapi/erp_data.php?type=delete',970,800,'stock_report')\">-->
				</td>
				<td align='right' style='padding:5px 0 5px 0;' colspan='3' >
				
				<span style='position:relative;bottom:7px;'>
				목록수 : <select name='max' id='max' style=''>
						<option value='5' ".($_COOKIE[inventory_goods_max_limit] == '5'?'selected':'').">5</option>
						<option value='10' ".($_COOKIE[inventory_goods_max_limit] == '10'?'selected':'').">10</option>
						<option value='20' ".($_COOKIE[inventory_goods_max_limit] == '20'?'selected':'').">20</option>
						<option value='30' ".($_COOKIE[inventory_goods_max_limit] == '30'?'selected':'').">30</option>
						<option value='50' ".($_COOKIE[inventory_goods_max_limit] == '50'?'selected':'').">50</option>
						<option value='100' ".($_COOKIE[inventory_goods_max_limit] == '100'?'selected':'').">100</option>
						<option value='500' ".($_COOKIE[inventory_goods_max_limit] == '500'?'selected':'').">500</option>
						</select>
				</span>

				<a href=\"javascript:PoPWindow3('stock_report.php?mmode=report&info_type=".$info_type."&".$QUERY_STRING."',970,800,'stock_report')\"> <img src='../images/".$admininfo["language"]."/btn_report_print.gif'></a>
				<a href='?mmode=pop'> </a> ";

        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
            $Contents .= "
			<a href='excel_config.php?excel_type=stock_report_excel&".$QUERY_STRING."' rel='facebox' ><span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></span></a>";
        }else{
            $Contents .= "
			<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
        }

        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
            $Contents .= " <a href='stock_report.php?".$_SERVER["QUERY_STRING"]."&mode=excel'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
        }else{
            $Contents .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
        }
        $Contents .= "
				</td>
			</tr>";
    }

    $Contents .= "
			<tr>
			<td  colspan=4 valign=top style='padding:0px;padding-top:0px;' id=product_stock>
			";

    $innerview = "
			<form name=stockfrm method=post action='product_stock.act.php' target='act'>
			<input type='hidden' name='act' value='update'>
			<input type='hidden' name='cid' value='$cid'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='info_type' value='$info_type'>
			<table cellpadding=3 cellspacing=0  width='100%' class='list_table_box'>
			<col width='55px'>
			<col width='45px'>
			<col width='5%'>
			<col width='5%'>
			<col width='*%'>
			".($mmode != "report" ? "<col width='7%'>" : "")."
			<col width='5%'>
			<col width='7%'>
			<col width='5%'>
			<col width='5%'>
			<col width='5%'>
			<col width='5%'>
			<col width='5%'>
			<col width='5%'>
			<col width='5%'>
			<col width='5%'>
			<col width='6%'>
			<tr align=center height=30>
				<td class=s_td rowspan='2' nowrap>순번</td>
				<td class=m_td rowspan='2'>재고<br/>현황등</td>
				<td class=m_td rowspan='2'>대표코드<br/>/".OrderByLink("품목코드", "g.gcode", $ordertype)."</td>
				<td class=m_td rowspan='2'>시스템<br/>코드</td>
				<td class=m_td rowspan='2' nowrap>이미지/".OrderByLink("품목명", "g.gname", $ordertype)."</td>
				".($mmode != "report" ? "<td class=m_td rowspan='2'>품목계정</td>" : "")."
				<td class=m_td rowspan='2' nowrap>단위</td>
				<td class=m_td rowspan='2' nowrap>색상</td>
				<td class=m_td rowspan='2' nowrap>사이즈</td>
				<td class=m_td rowspan='2' style='padding:0px 5px;' nowrap>규격</td>
				<td class=m_td  colspan='6'>재고현황</td>
				<td class=m_td style='padding:0px 3px;'  rowspan='2' nowrap>재고자산</td>
			</tr>
			
			<!--
			<tr align=center height=30>
				<td class=m_td nowrap>".OrderByLink("재고", "stock", $ordertype)."(가용)</td>
				<td class=m_td nowrap>진행<br/>재고(-)</td>
				<td class=m_td nowrap>발주<br/>미입고(+)</td>
				<td class=m_td nowrap>안전<br/>재고(-)</td>
				<td class=m_td nowrap>부족<br/>재고</td>
				<td class=m_td nowrap>ERP<br/>재고</td>
			</tr>
			-->
			<tr align=center height=30>
				<td class=m_td nowrap>".OrderByLink("가용재고", "stock", $ordertype)."</td>
				<td class=m_td nowrap>안전<br/>재고</td>
				<td class=m_td nowrap>총누적<br/>주문수량</td>
				<td class=m_td nowrap>취소<br/>수량</td>
				<td class=m_td nowrap>연동<br/>재고</td>
				<td class=m_td nowrap>품절<br/>표시</td>
			</tr>
			";

    if(count($goods_infos) == 0){
        $innerview .= "<tr bgcolor=#ffffff height=50><td colspan=".($mmode == "report" ? "15":"15")." align=center> 해당되는  품목이 없습니다.</td></tr>";
    }else{

        $before_pid = "";

        for ($i = 0; $i < count($goods_infos); $i++)
        {

            if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))){
                $img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c");
            }else{
                $img_str = "../image/no_img.gif";
            }

            $no = $total - ($page - 1) * $max - $i;
            $wantage_stock = $goods_infos[$i][stock]-$goods_infos[$i][sell_ing_cnt]+$goods_infos[$i][order_ing_cnt]-$goods_infos[$i][safestock];
            if( $wantage_stock > 0){
                $wantage_stock = 0;
            }

            $innerview .= "<tr bgcolor='#ffffff' height=36 align=center>
						<td class='list_box_td list_bg_gray' align=center>
						<!--input type=checkbox class=nonborder id='cpid' name='select_pid[]' value='".$goods_infos[$i][id]."'-->".$no."</td>";

            if(($goods_infos[$i][safestock] + $wantage_stock) > 0 && $wantage_stock < 0){
                $alarm_img_str = "../images/icon/alarm_warning.gif";
            }elseif(($goods_infos[$i][safestock] + $wantage_stock) < 0 && $wantage_stock < 0){
                $alarm_img_str = "../images/icon/alarm_danger.gif";
            }else{
                $alarm_img_str = "../images/icon/alarm_safe.gif";
            }


            if(($goods_infos[$i][stock]-$goods_infos[$i][sell_ing_cnt]) < 10 &&  ($goods_infos[$i][stock]-$goods_infos[$i][sell_ing_cnt]) > 5){
                $safe_alarm_img_str = "../images/icon/alarm_warning.gif";
            }elseif(($goods_infos[$i][stock]-$goods_infos[$i][sell_ing_cnt]) < 5 ){
                $safe_alarm_img_str = "../images/icon/alarm_danger.gif";
            }else{
                $safe_alarm_img_str = "../images/icon/alarm_safe.gif";
            }

            $soldOutText = "";
            if(($goods_infos[$i][stock]-$goods_infos[$i][sell_ing_cnt]) < 1){
                $soldOutText = "[품절]";
            }

            $innerview .= "
						<td bgcolor=#ffffff ><img src='".$alarm_img_str."'></td>
						<td bgcolor=#ffffff style='padding:0px 3px;'><b>".$goods_infos[$i][gcode]."</b><br/>".$goods_infos[$i][gid]."</td>
						<td bgcolor=#ffffff style='padding:0px 3px;'>".$goods_infos[$i][gu_ix]."</td>
						<td class='list_box_td point'>
							<table cellpadding=0 cellspacing=0>
								<tr>";
            if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))){
                $innerview .= "
									<td bgcolor='#ffffff' align=center style='padding:5px 3px' >
										<a href='../inventory/inventory_goods_input.php?gid=".$goods_infos[$i][gid]."' class='screenshot'  rel='".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "basic")."'><img src='".$img_str."' width=30 height=30 style='border:1px solid #efefef'></a>
									</td>";
            }
            $innerview .= "
									<td bgcolor='#ffffff' align=left style='font-weight:normal;line-height:140%;'>
									<a href=\"javascript:PoPWindow3('../inventory/inventory_goods_input.php?mmode=pop&gid=".$goods_infos[$i][gid]."',970,800,'item_info')\"><b>".$goods_infos[$i][gname]."</b></a>
									</td>
								</tr>
							</table>
						</td>";
            if($mmode != "report"){
                $innerview .= "<td bgcolor=#ffffff style='padding:0px 3px;' nowrap>".$ITEM_ACCOUNT[$goods_infos[$i][item_account]]."</td>";
            }

            $total_order_cnt = getOrderCnt($goods_infos[$i][gid],'complete');
            $cancel_order_cnt = getOrderCnt($goods_infos[$i][gid],'cancel');
            $erp_stock_cnt = getErpSotck($goods_infos[$i][gid]);

            $innerview .= "
					<td bgcolor=#ffffff nowrap>".getUnit($goods_infos[$i][unit], "basic_unit","","text")."</td>
					<td bgcolor=#ffffff nowrap>".$goods_infos[$i][color]."</td>
					<td bgcolor=#ffffff nowrap>".$goods_infos[$i][size]."</td>
					<td bgcolor=#ffffff nowrap>".$goods_infos[$i][standard]."</td>
					<td class='list_box_td point'>".($goods_infos[$i][stock]-$goods_infos[$i][sell_ing_cnt])."</td>
					<td bgcolor=#ffffff><img src='".$safe_alarm_img_str."'></td>
					<td bgcolor=#ffffff>".number_format($total_order_cnt)."</td>
					<td bgcolor=#ffffff>".number_format($cancel_order_cnt)."</td>
					<td bgcolor=#ffffff>".number_format($erp_stock_cnt)."</td>
					<td bgcolor=#ffffff>".$soldOutText."</td>
					<td bgcolor=#ffffff>".number_format($goods_infos[$i][stock]*$goods_infos[$i][avg_price])."</td>
					<!--td bgcolor=#ffffff>".$goods_infos[$i][order_cnt]."</td-->
					<td bgcolor=#ffffff style='".($mmode == "report" ? "display:none;":"display:none;")."padding:0px 5px;' nowrap>
						
						";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $innerview .= "
						<a href=\"javascript:PoPWindow3('../inventory/input_pop.php?gid=".$goods_infos[$i][gid]."',800,700,'input_pop')\"><img src='../images/".$admininfo["language"]."/btn_input.gif'></a>
						<a href=\"javascript:PoPWindow3('../inventory/delivery_pop.php?gid=".$goods_infos[$i][gid]."',900,700,'output_pop')\"><img src='../images/".$admininfo["language"]."/btn_output.gif'></a>
						<a href=\"javascript:PoPWindow3('../inventory/order_pop.php?gid=".$goods_infos[$i][gid]."',800,700,'order_pop')\"><img src='../images/".$admininfo["language"]."/bts_order.gif'></a>
						<!--a href='inventory_goods_input.php?mode=copy&gid=".$goods_infos[$i][gid]."'><img src='../images/".$admininfo["language"]."/btc_copy.gif'></a>
						<a href=\"javascript:PoPWindow3('../inventory/inventory_order.php?gid=".$goods_infos[$i][gid]."&mmode=pop',800,700,'order_pop')\"><img src='../images/".$admininfo["language"]."/btn_depot_move.gif'></a-->
						";
            }else{
                $innerview .= "
						<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_input.gif'></a>
						<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_output.gif'></a>
						<!--a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_order.gif'></a><br>
						<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_depot_move.gif'></a-->
						";
            }
            $innerview .= "
					</td>
					<!--td class='list_box_td list_bg_gray'>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_infos[$i][coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td' style='line-height:150%;'>";
            if($goods_infos[$i][reserve_yn] == "Y"){
                $innerview .= "		<b>개별적용</b><br>";
            }else{
                $innerview .= "		<b>전체정책</b><br>";
            }
            if ($goods_infos[$i][reserve_yn] == "Y"){
                $innerview .= "		".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_infos[$i][reserve])." ".$currency_display[$admin_config["currency_unit"]]["back"]."";
            }else{
                $innerview .= "		".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_infos[$i][sellprice]*$reserve_data[goods_reserve_rate] /100)." ".$currency_display[$admin_config["currency_unit"]]["back"]."";
            }
            $innerview .= "
					</td>

					<td class='list_box_td list_bg_gray' nowrap>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_infos[$i][listprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td' nowrap>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_infos[$i][sellprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td list_bg_gray' style='text-align:center;' nowrap>
						<table align=center>
							<tr>
								<td><a href='cart.php?act=add&id=".$goods_infos[$i][id]."&pcount=1' >발주서품목등록</a></td>
							</tr>
						</table>
					</td-->

				</tr>";



        }

    }
    $innerview .= "</table>";

    if($mmode != "report"){
        $innerview .= "
				<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=40><td>".($stock_status == "shortage" ? "<a href=\"javascript:PrintWindow('./print_stock.php?$QUERY_STRING',700,900,'print_stock')\">재고 내역서 출력</a>":"")."</td>
					<td align=right nowrap>".$str_page_bar."</td></tr>
				</table>";
    }else{
        $innerview .= "<br><br><br>";
    }
    $innerview .= "			
				</form>";

    $Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
			";
    if($mmode != "report"){

        $help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td>각 품목별 및 옵션별로 재고현황을 보실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >옵션 항목의 재고가 부족, 품절일 경우도 리스트에 각 상태에 따라 출력되게 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >재고 상태 검색시 카테고리에 등록되어 있지 않은 품목은 나오지 않습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td>재고자산은 원가법으로 산출하였으며, 현재 이동평균법의 재고원가를 산출됩니다. 약간의 오차가 발생할 수 있으며, 회계용으로 사용하지 마시고 참고용으로 사용하세요.</td></tr>
</table>
";

        $Contents .= HelpBox("품목별 재고현황", $help_text);
    }

}




if($info_type == "detail"){	//품목별 상세재고현황
    if($mmode != "report"){
        $Contents .=	"
			<tr>
			 	<td colspan=4 align=left style='padding-bottom:0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:2px 5px 2px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 현재재고 현황</b> </span></b> </div>")."</td>
			 </tr>
			<tr>
				<td align='left' colspan=4 style='padding:0px;'> ".ItemSummary($info_type)."</td>
			</tr>
			<tr >
				<td colspan=4>
					<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
					<input type='hidden' name='mode' value='search'>
					<input type='hidden' name='cid2' value='$cid2'>
					<input type='hidden' name='depth' value='$depth'>
					<input type='hidden' name='info_type' value='$info_type'>
					<!--input type='hidden' name='sprice' value='0' />
					<input type='hidden' name='eprice' value='1000000' /-->
					<table class='box_shadow' style='width:100%;height:20px' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05 align=center' style='padding:0px'>
								<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='input_table_box'>
									<col width='150' >
									<col width='*' >
									<col width='150' >
									<col width='*' >
									<tr>
										<td class='input_box_title'>  <b>선택된 품목분류</b>  </td>
										<td class='input_box_item' colspan=3><b id='select_category_path1'>".($search_text == "" ? getIventoryCategoryPathByAdmin($cid2, $depth)."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div></td>
									</tr>
									<tr>
										<td class='input_box_title'><b>품목분류</b></td>
										<td class='input_box_item' colspan=3>
											<table border=0 cellpadding=0 cellspacing=0>
												<tr>
													<td style='padding-right:5px;'>".getInventoryCategoryList("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
													<td style='padding-right:5px;'>".getInventoryCategoryList("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
													<td style='padding-right:5px;'>".getInventoryCategoryList("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
													<td>".getInventoryCategoryList("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<!--td class='input_box_title'>주거래처</td>
										<td class='input_box_item' >
											".SelectSupplyCompany($ci_ix,'ci_ix','select','false')."
										</td-->
										<td class='input_box_title'>사업장/창고</td>
										<td class='input_box_item' colspan=3>
											".SelectEstablishment($company_id,"company_id","select","false","onChange=\"loadPlace(this,'pi_ix')\" ")."
											".SelectInventoryInfo($company_id, $pi_ix,'pi_ix','select','false', "onChange=\"loadPlaceSection(this,'ps_ix')\"  ")."
											".SelectSectionInfo($pi_ix,$ps_ix,'ps_ix',"select","false")." 
										</td>										
									</tr>
									<tr>
										<td class='input_box_title'>품목계정</td>
										<td class='input_box_item' >
											".getItemAccount($item_account)."
										</td>
										<td class='input_box_title'>판매상태</td>
										<td class='input_box_item'>
											<input type=checkbox name='status[]' class=nonborder value='1' id='status_1' validation=false title='사용유무' ".CompareReturnValue("1",$status," checked")."><label for='status_1'>판매중</label>
											<input type=checkbox name='status[]' class=nonborder value='0' id='status_0' validation=false title='사용유무' ".CompareReturnValue("0",$status," checked")."><label for='status_0'>일시품절</label>
											<input type=checkbox name='status[]' class=nonborder value='2' id='status_2' validation=false title='사용유무' ".CompareReturnValue("2",$status," checked")."><label for='status_2'>단종(품절)</label>
										</td>
										<!--td class='input_box_title'>품목사용여부</td>
										<td class='input_box_item'>
											<input type=radio name='is_use' class=nonborder value='' id='is_use_A' validation=false title='사용유무' ".($is_use == "" ? "checked":"")."><label for='is_use_A'>전체</label>
											<input type=radio name='is_use' class=nonborder value='Y' id='is_use_Y' validation=false title='사용유무' ".($is_use == "Y" ? "checked":"")."><label for='is_use_Y'>사용</label>
											<input type=radio name='is_use' class=nonborder value='N' id='is_use_N' validation=false title='사용유무' ".($is_use == "N" ? "checked":"")."><label for='is_use_N'>미사용</label>
										</td-->
									</tr>
									<tr>
										<td class='input_box_title'>  <b>검색어</b>  
											<br/>
											
											<label for='mult_search_use'>(다중검색 체크)</label> <input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> 
								
										</td>
										<td class='input_box_item' valign='middle' style='padding-right:5px;' colspan='3'>
											<table cellpadding=0 cellspacing=0>
												<tr>
													<td>
														<select name='search_type'  style=\"font-size:12px;height:22px;min-width:140px;\">
															<option value='g.gcode' ".CompareReturnValue("g.gcode",$search_type).">대표코드</option>
															<option value='g.gid' ".CompareReturnValue("g.gid",$search_type).">품목코드</option>
															<option value='g.gname' ".CompareReturnValue("g.gname",$search_type).">품목명</option>
															<option value='gu.gu_ix' ".CompareReturnValue("gu.gu_ix",$search_type).">시스템코드</option>
															<option value='gu.barcode' ".CompareReturnValue("gu.barcode",$search_type).">바코드</option>
														</select>
													</td>
													<td style='padding-left:5px;'>
														<div id='search_text_input_div'>
															<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
														</div>
														<div id='search_text_area_div' style='display:none;'>
															<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
														</div>
													</td>
													<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
												</tr>
											</table>
										</td>
									</tr>
									<!--tr>
										<td class='input_box_title'><b>재고상태</b></td>
										<td class='input_box_item' colspan='3'>
										<input type='radio' name='stock_status' value='whole' id='owhole' ".CompareReturnValue("whole","$stock_status"," checked")."><label for='owhole'>전체</label>
										<input type='radio' name='stock_status' value='soldout' id='osoldout' ".CompareReturnValue("soldout","$stock_status"," checked")."><label for='osoldout'>품절</label>
										<input type='radio' name='stock_status' value='shortage' id='oshortage' ".CompareReturnValue("shortage","$stock_status"," checked")."><label for='oshortage'>부족</label>
										<input type='radio' name='stock_status' value='surplus' id='osurplus' ".CompareReturnValue("surplus","$stock_status"," checked")."><label for='osurplus'>여유</label>
										</td>
									</tr-->
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
			
			<tr>
			    <td colspan=1>
				 <input type='checkbox' name='view_shotage_goods' id='view_shotage_goods' onclick=\"reloadView('complete')\" ".($_COOKIE[view_shotage_goods] == 1 ? "checked":"")." >
				<label for='view_shotage_goods'> 재고없는품목 포함</label>
				</td>
				<td align='right' colspan=3 style='padding:5px 0 5px 0;'>

				<span style='position:relative;bottom:7px;'>
				목록수 : <select name='max' id='max' style=''>
						<option value='5' ".($_COOKIE[inventory_goods_max_limit] == '5'?'selected':'').">5</option>
						<option value='10' ".($_COOKIE[inventory_goods_max_limit] == '10'?'selected':'').">10</option>
						<option value='20' ".($_COOKIE[inventory_goods_max_limit] == '20'?'selected':'').">20</option>
						<option value='30' ".($_COOKIE[inventory_goods_max_limit] == '30'?'selected':'').">30</option>
						<option value='50' ".($_COOKIE[inventory_goods_max_limit] == '50'?'selected':'').">50</option>
						<option value='100' ".($_COOKIE[inventory_goods_max_limit] == '100'?'selected':'').">100</option>
						<option value='500' ".($_COOKIE[inventory_goods_max_limit] == '500'?'selected':'').">500</option>
						</select>
				</span>

				<a href=\"javascript:PoPWindow3('stock_report.php?mmode=report&info_type=".$info_type."&".$QUERY_STRING."',970,800,'stock_report')\"> <img src='../images/".$admininfo["language"]."/btn_report_print.gif'></a>
				<a href='?mmode=pop'> </a> ";

        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
            $Contents .= "
			<a href='excel_config.php?".$QUERY_STRING."' rel='facebox' ><span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></span></a>";
        }else{
            $Contents .= "
			<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
        }

        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
            $Contents .= " <a href='stock_report.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
        }else{
            $Contents .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
        }
        $Contents .= "
				</td>
			</tr>";
    }

    $Contents .= "
			<tr>
			<td  colspan=4 valign=top style='padding:0px;padding-top:0px;' id=product_stock>
			";
    $innerview = "
			<form name=stockfrm method=post action='product_stock.act.php' target='act'>
			<input type='hidden' name='act' value='update'>
			<input type='hidden' name='cid' value='$cid'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='info_type' value='$info_type'>
			<table cellpadding=3 cellspacing=0  width='100%' class='list_table_box'>

			<col width='5%'>
			<col width='8%'>
			<col width='*%'>
			".($mmode != "report" ? "<col width='7%'>" : "")."
			<col width='5%'>
			<col width='7%'>
			<col width='6%'>
			<col width='8%'>
			<col width='8%'>
			<col width='8%'>
			<col width='6%'>
			<col width='7%'>

			".($mmode != "report" ? "<col width='8%'>" : "")."
			<tr align=center height=30>
				<td class=s_td rowspan='2' nowrap>순번</td>
				<td class=m_td rowspan='2'>대표코드<br/>/품목코드</td>
				<td class=m_td rowspan='2' nowrap>이미지/".OrderByLink("품목명", "g.gname", $ordertype)."</td>
				<td class=m_td rowspan='2'>품목계정</td>
				<td class=m_td rowspan='2' nowrap>단위</td>
				<td class=m_td rowspan='2' style='padding:0px 5px;' nowrap>규격</td>
				<!--td class=m_td rowspan='2' nowrap>입고일</td-->
				<td class=m_td rowspan='2' nowrap>유통기한</td>
				<td class=m_td colspan='3'>사업장/창고</td>
				<td class=m_td  rowspan='2'>".OrderByLink("현재고", "stock", $ordertype)."(가용)</td>
				<td class=m_td style='padding:0px 3px;'  rowspan='2' nowrap>재고자산</td>
				<!--<td class=m_td rowspan='2' nowrap>판매수량</td>-->
				<td class=e_td rowspan='2' ".($mmode == "report" ? "style='display:none;'":"").">창고이동</td>
			</tr>
			
			<tr align=center height=30>
				<td class=m_td nowrap>".OrderByLink("사업장","company_name", $ordertype)."</td>
				<td class=m_td nowrap>".OrderByLink("창고", "company_name,place_name", $ordertype)."</td>
				<td class=m_td nowrap>".OrderByLink("보관장소", "place_name,section_name", $ordertype)."</td>
				<!--td class=m_td nowrap>".OrderByLink("재고", "stock", $ordertype)."</td>
				<td class=m_td nowrap>진행재고(-)</td>
				<td class=m_td nowrap>발주미입고(+)</td>
				<td class=m_td nowrap>안전재고(-)</td>
				<td class=m_td nowrap>부족재고</td-->
			</tr>
			";

    if(count($goods_infos) == 0){
        $innerview .= "<tr bgcolor=#ffffff height=50><td colspan=".($mmode == "report" ? "12":"13")." align=center> 해당되는  품목이 없습니다.</td></tr>";
    }else{

        $before_pid = "";

        for ($i = 0; $i < count($goods_infos); $i++)
        {

            if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))){
                $img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c");
            }else{
                $img_str = "../image/no_img.gif";
            }

            $no = $total - ($page - 1) * $max - $i;
            $wantage_stock = $goods_infos[$i][stock]-$goods_infos[$i][sell_ing_cnt]+$goods_infos[$i][order_ing_cnt]-$goods_infos[$i][safestock];
            if( $wantage_stock > 0){
                $wantage_stock = 0;
            }

            $innerview .= "<tr bgcolor='#ffffff' height=26 align=center>
						<td class='list_box_td list_bg_gray' align=center nowrap>
						<!--input type=checkbox class=nonborder id='cpid' name='select_pid[]' value='".$goods_infos[$i][id]."'-->".$no."</td>
						<td bgcolor=#ffffff style='padding:0px 3px;' ><b>".$goods_infos[$i][gcode]."</b><br/>".$goods_infos[$i][gid]."</td>
						<td class='list_box_td point' nowrap>
							<table cellpadding=0 cellspacing=0>
								<tr>";
            if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))){
                $innerview .= "
									<td bgcolor='#ffffff' align=center style='padding:5px 3px' >
										<a href='../inventory/inventory_goods_input.php?gid=".$goods_infos[$i][gid]."' class='screenshot'  rel='".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "basic")."'><img src='".$img_str."' width=30 height=30 style='border:1px solid #efefef'></a>
									</td>";
            }
            $innerview .= "
									<td bgcolor='#ffffff' align=left style='font-weight:normal;line-height:140%;'>
									<a href=\"javascript:PoPWindow3('../inventory/inventory_goods_input.php?mmode=pop&gid=".$goods_infos[$i][gid]."',970,800,'goods_info')\"><b>".$goods_infos[$i][gname]."</b></a>
									</td>
								</tr>
							</table>
						</td>
					
					
					<td bgcolor=#ffffff style='padding:0px 3px;' nowrap>".$ITEM_ACCOUNT[$goods_infos[$i][item_account]]."</td>
					
					
					<td bgcolor=#ffffff nowrap>".getUnit($goods_infos[$i][unit], "basic_unit","","text")."</td>
					<td bgcolor=#ffffff nowrap>".$goods_infos[$i][standard]."</td>
					<!--td bgcolor=#ffffff nowrap> ".$goods_infos[$i][vdate]." </td-->
					<td bgcolor=#ffffff nowrap> ".$goods_infos[$i][expiry_date]." </td>
					<td bgcolor=#ffffff nowrap> ".$goods_infos[$i][company_name]." </td>
					<td bgcolor=#ffffff nowrap>".$goods_infos[$i][place_name]."</td>
					<td bgcolor=#ffffff nowrap> ".$goods_infos[$i][section_name]."</td>
					<!--td bgcolor=#ffffff>".number_format($goods_infos[$i][stock_assets])."</td>
					<td bgcolor=#ffffff>".number_format($goods_infos[$i][sellprice])."</td-->
					<!--td class='list_box_td' style='padding:0px 5px;' nowrap>
						".SelectSupplyCompany($goods_infos[$i][ci_ix],'ci_ix','text','false')." 
					</td-->
					<td class='list_box_td point' nowrap>".number_format($goods_infos[$i][stock])."(".($goods_infos[$i][stock] - $goods_infos[$i][sell_ing_cnt]).")</td>
					<!--td bgcolor=#ffffff>".$goods_infos[$i][sell_ing_cnt]."</td>
					<td bgcolor=#ffffff>".$goods_infos[$i][order_ing_cnt]."</td>
					<td bgcolor=#ffffff>".$goods_infos[$i][safestock]."</td>
					<td bgcolor=#ffffff>".number_format($wantage_stock)."</td-->
					<td bgcolor=#ffffff>".number_format($goods_infos[$i][stock_assets])."</td>
					<!--<td bgcolor=#ffffff>".$goods_infos[$i][order_cnt]."</td>-->
					<td bgcolor=#ffffff style='".($mmode == "report" ? "display:none;":"")."padding:5px 5px;' nowrap>";

            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                //$innerview .= "pi_ix :".$goods_infos[$i][pi_ix]." ,basic_pi_ix :".$goods_infos[$i][basic_pi_ix]." ,ps_ix :".$goods_infos[$i][ps_ix]." ,basic_ps_ix :".$goods_infos[$i][basic_ps_ix]." ";

                if($goods_infos[$i][stock] > 0){
                    $innerview .= "<img src='../images/".$admininfo["language"]."/btn_warehouse_move.gif' onclick=\"PoPWindow3('../inventory/warehouse_move_pop.php?gu_ix=".$goods_infos[$i][gu_ix]."&ps_ix=".$goods_infos[$i][ps_ix]."',800,700,'warehouse_move_pop');\" style='cursor:pointer;margin:3px 0;' /><br/> ";
                }

                if($goods_infos[$i][basic_ps_ix]==0){
                    $innerview .= "<span class='red'>기본창고 미지정</span> <!--a href=\"javascript:PoPWindow3('../inventory/inventory_goods_input.php?mmode=pop&gid=".$goods_infos[$i][gid]."',970,800,'goods_info')\" class='red'></a-->";
                }elseif($goods_infos[$i][ps_ix]!=$goods_infos[$i][basic_ps_ix]){
                    if($goods_infos[$i][stock] > 0){
                        $innerview .= "
								<img src='../images/".$admininfo["language"]."/btn_basic_warehouse_move.gif' onclick=\"basic_warehouse_move('".$goods_infos[$i][gu_ix]."','".$goods_infos[$i][stock]."','".$goods_infos[$i][ps_ix]."','".$goods_infos[$i][basic_ps_ix]."')\" style='cursor:pointer;margin-bottom:3px;' />  ";
                    }
                }
            }

            /*
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                    $innerview .= "
                    <a href=\"javascript:PoPWindow3('../inventory/input_pop.php?gid=".$goods_infos[$i][gid]."',800,700,'input_pop')\"><img src='../images/".$admininfo["language"]."/btn_input.gif'></a>
                    <a href=\"javascript:PoPWindow3('../inventory/delivery_pop.php?gid=".$goods_infos[$i][gid]."',900,700,'output_pop')\"><img src='../images/".$admininfo["language"]."/btn_output.gif'></a>
                    <a href=\"javascript:PoPWindow3('../inventory/order_pop.php?gid=".$goods_infos[$i][gid]."',800,700,'order_pop')\"><img src='../images/".$admininfo["language"]."/bts_order.gif'></a>
                    <!--a href='inventory_goods_input.php?mode=copy&gid=".$goods_infos[$i][gid]."'><img src='../images/".$admininfo["language"]."/btc_copy.gif'></a>
                    <a href=\"javascript:PoPWindow3('../inventory/inventory_order.php?gid=".$goods_infos[$i][gid]."&mmode=pop',800,700,'order_pop')\"><img src='../images/".$admininfo["language"]."/btn_depot_move.gif'></a-->
                    ";
                }else{
                    $innerview .= "
                    <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_input.gif'></a>
                    <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_output.gif'></a>
                    <!--a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_order.gif'></a><br>
                    <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_depot_move.gif'></a-->
                    ";
                }
            */
            $innerview .= "
					</td>
					<!--td class='list_box_td list_bg_gray'>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_infos[$i][coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td' style='line-height:150%;'>";
            if($goods_infos[$i][reserve_yn] == "Y"){
                $innerview .= "		<b>개별적용</b><br>";
            }else{
                $innerview .= "		<b>전체정책</b><br>";
            }
            if ($goods_infos[$i][reserve_yn] == "Y"){
                $innerview .= "		".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_infos[$i][reserve])." ".$currency_display[$admin_config["currency_unit"]]["back"]."";
            }else{
                $innerview .= "		".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_infos[$i][sellprice]*$reserve_data[goods_reserve_rate] /100)." ".$currency_display[$admin_config["currency_unit"]]["back"]."";
            }
            $innerview .= "
					</td>

					<td class='list_box_td list_bg_gray' nowrap>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_infos[$i][listprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td' nowrap>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_infos[$i][sellprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td list_bg_gray' style='text-align:center;' nowrap>
						<table align=center>
							<tr>
								<td><a href='cart.php?act=add&id=".$goods_infos[$i][id]."&pcount=1' >발주서품목등록</a></td>
							</tr>
						</table>
					</td-->

				</tr>";



        }

    }
    $innerview .= "</table>";

    if($mmode != "report"){
        $innerview .= "
				<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=40><td>".($stock_status == "shortage" ? "<a href=\"javascript:PrintWindow('./print_stock.php?$QUERY_STRING',700,900,'print_stock')\">재고 내역서 출력</a>":"")."</td>
					<td align=right nowrap>".$str_page_bar."</td></tr>
				</table>";
    }else{
        $innerview .= "<br><br><br>";
    }
    $innerview .= "			
				</form>";

    $Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
			";
    if($mmode != "report"){

        $help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td>각 품목별 및 옵션별로 재고현황을 보실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td>옵션 항목의 재고가 부족, 품절일 경우도 리스트에 각 상태에 따라 출력되게 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td>재고 상태 검색시 카테고리에 등록되어 있지 않은 품목은 나오지 않습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td>재고자산은 원가법으로 산출하였으며, 현재 이동평균법의 재고원가를 산출됩니다. 약간의 오차가 발생할 수 있으며, 회계용으로 사용하지 마시고 참고용으로 사용하세요.</td></tr>
</table>
";

        $Contents .= HelpBox("품목별 상세재고현황", $help_text);
    }

}




if($info_type == "warehouse"){

    if($mmode != "report"){
        $Contents .=	"
			
			<tr height=90>
				<td colspan=2>
					<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
					<input type='hidden' name='mode' value='search'>
					<input type='hidden' name='info_type' value='$info_type'>
					<input type='hidden' name='depth' value='$depth'>
					<input type='hidden' name='groupby_ps_ix' value='$groupby_ps_ix'>
					<!--input type='hidden' name='sprice' value='0' />
					<input type='hidden' name='eprice' value='1000000' /-->
					<table class='box_shadow' style='width:100%;height:20px' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05 align=center' style='padding:0px'>
								<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='input_table_box'>
									<col width='15%' >
									<col width='35% >
									<col width='15%' >
									<col width='35%' >
									<tr>
										<td class='input_box_title'><b>사업장/창고분류</b></td>
										<td class='input_box_item' colspan=3>
											".SelectEstablishment($company_id,"company_id","select","false","onChange=\"loadPlace(this,'pi_ix')\" ")."
											".SelectInventoryInfo($company_id, $pi_ix,'pi_ix','select','false', "")." <!--onChange=\"loadPlaceSection(this,'ps_ix')\" -->
											<!--".SelectSectionInfo($pi_ix,$ps_ix,'ps_ix',"select","false")."-->
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
			";
    }
    $Contents .=	"
			<tr>
			    <td align='right' colspan=4 style='padding:5px 0 5px 0;'>";
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
        //$Contents .= "<a href='stock_report.php?".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
    }else{
        //$Contents .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
    }

    for ($i = 0; $i < count($goods_infos); $i++)
    {
        $stock_assets_sum += $goods_infos[$i][stock_assets];
        $stock_sum += $goods_infos[$i][stock];
    }

    if($mmode != "report"){
        $Contents .="
			<table width='100%' cellpadding=0 cellspacing=0 border='0' >
			<col width=70%>
			<col width=30%>
			<tr height=30 >
			<td align=left><input type='checkbox' name='groupby_ps_ix' id='groupby_ps_ix' onclick=\"".($groupby_ps_ix == 1 ? "location.href='?".str_replace('&groupby_ps_ix=1','&groupby_ps_ix=0',$_SERVER["QUERY_STRING"])."'":"location.href='?".str_replace('&groupby_ps_ix=0','',$_SERVER["QUERY_STRING"]).(strlen($_SERVER["QUERY_STRING"]) > 0 ? "&groupby_ps_ix=1" : "groupby_ps_ix=1")."'")."\" ".($groupby_ps_ix == 1 ? "checked":"")."/> <label for='groupby_ps_ix'> 보관장소 상세보기</label></td>
				<td align=right>
				<a href=\"javascript:PoPWindow3('stock_report.php?mmode=report&".$QUERY_STRING."',970,800,'stock_report')\"><img src='../images/".$admininfo["language"]."/btn_report_print.gif'></a>
				<a href='excel_config.php?".$QUERY_STRING."' rel='facebox' ><span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></span></a>
				 ";

        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
            $Contents .= "<a href='stock_report.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
        }else{
            $Contents .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
        }
        $Contents .="

				</td>
			  </tr>
			</table>
		";
    }
    $Contents .= "
				</td>
			</tr>
			<tr>
			<td valign=top style='padding:0px;padding-top:0px;' id=product_stock colspan='4'>
			";

    if($groupby_ps_ix=='1'){
        $innerview = "
				<form name=stockfrm method=post action='product_stock.act.php' target='act'>
				<input type='hidden' name='act' value='update'>
				<input type='hidden' name='cid' value='$cid'>
				<input type='hidden' name='info_type' value='$info_type'>
				<table cellpadding=0 cellspacing=0  width='100%' class='list_table_box'>
				<col width='9%'>
				<col width='5%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<tr align=center height=30>
					<td class=s_td rowspan='2'>순번</td>
					<td class=m_td colspan='3'>사업장/창고</td>
					<td class=m_td  colspan='4'>재고현황</td>
				</tr>			
				<tr align=center height=30>
					<td class=m_td>**사업장</td>
					<td class=m_td>창고</td>
					<td class=m_td>보관장소</td>
					<td class=m_td>재고수량(개)</td>
					<td class=m_td>재고점유율(%)</td>
					<td class=m_td>재고자산(원)</td>
					<td class=m_td>재고자산점유율(%)</td>
				</tr>
				";

        if(count($goods_infos) == 0){
            $innerview .= "<tr bgcolor=#ffffff height=50><td colspan=7 align=center> 해당되는  품목이 없습니다.</td></tr>";
        }else{

            $innerview .= "<tr height=35>
						<td class='list_box_td list_bg_gray' style='padding:0px 7px;' colspan=4 nowrap>
						<b>합계</b>
						</td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap><b>".number_format($stock_sum)."</b></td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap><b>100%</b></td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap><b>".number_format($stock_assets_sum)."</b></td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap><b>100%</b></td>
					</tr>
					";

            for ($i = 0; $i < count($goods_infos); $i++)
            {

                $no = $i +1;

                $innerview .= "<tr height=35>
						<td class='list_box_td list_bg_gray' style='padding:0px 7px;' nowrap>
							".$no."<input type=hidden name='gid[]' value='".$goods_infos[$i][pi_ix]."'>
						</td>
						<!--td class='list_box_td list_bg_gray' style='padding:0px 7px;' nowrap>
							".($goods_infos[$i][pi_ix] ? $goods_infos[$i][pi_ix]:$goods_infos[$i][pi_ix])."<input type=hidden name='pi_ix[]' value='".$goods_infos[$i][pi_ix]."'>
						</td-->
						<td class='list_box_td' style='padding:5px 10px;'>
							".$goods_infos[$i][company_name]."
						</td>
						<td class='list_box_td point' style='padding:5px 10px;'>
							".$goods_infos[$i][place_name]."
						</td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap>".$goods_infos[$i][section_name]."</td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap>".number_format($goods_infos[$i][stock])."</td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap>".($stock_sum == 0 ? 0:number_format(($goods_infos[$i][stock])/$stock_sum*100,2))." %</td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap>".number_format($goods_infos[$i][stock_assets])."</td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap>".($stock_assets_sum == 0 ? 0:number_format(($goods_infos[$i][stock_assets])/$stock_assets_sum*100,2))." %</td>
						
					</tr>
					";
            }



        }

    }else{
        $innerview = "
				<form name=stockfrm method=post action='product_stock.act.php' target='act'>
				<input type='hidden' name='act' value='update'>
				<input type='hidden' name='cid' value='$cid'>
				<!--input type='hidden' name='depth' value='$depth'-->
				<input type='hidden' name='info_type' value='$info_type'>
				<table cellpadding=0 cellspacing=0  width='100%' class='list_table_box'>
				<col width='5%'>
				<!--col width='5%'-->
				<col width='7%'>
				<!--col width='13%'-->
				<col width='7%'>
				<col width='7%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				
				<tr align=center height=30>
					<td class=s_td rowspan='2'>순번</td>
					<!--td class=m_td rowspan='2'>사업장/창고코드</td-->
					<td class=m_td colspan='2'>사업장/창고</td>
					<td class=m_td  colspan='4'>재고현황</td>
				</tr>			
				<tr align=center height=30>
					<td class=m_td>**사업장</td>
					<td class=m_td>창고</td>
					<!--td class=m_td>보관장소</td-->
					<td class=m_td>재고수량(개)</td>
					<td class=m_td>재고점유율(%)</td>
					<td class=m_td>재고자산(원)</td>
					<td class=m_td>재고자산점유율(%)</td>
				</tr>
				";

        if(count($goods_infos) == 0){
            $innerview .= "<tr bgcolor=#ffffff height=50><td colspan=6 align=center> 해당되는  품목이 없습니다.</td></tr>";
        }else{

            $innerview .= "<tr height=35>
						<td class='list_box_td list_bg_gray' style='padding:0px 7px;' colspan=3 nowrap>
						합계
						</td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap><b>".number_format($stock_sum)."</b></td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap><b>100%</b></td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap><b>".number_format($stock_assets_sum)."</b></td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap><b>100%</b></td>
						
					</tr>
					";

            for ($i = 0; $i < count($goods_infos); $i++)
            {

                $no = $i +1;

                $innerview .= "<tr height=35>
						<td class='list_box_td list_bg_gray' style='padding:0px 7px;' nowrap>
							".$no."<input type=hidden name='gid[]' value='".$goods_infos[$i][pi_ix]."'>
						</td>
						<!--td class='list_box_td list_bg_gray' style='padding:0px 7px;' nowrap>
							".($goods_infos[$i][pi_ix] ? $goods_infos[$i][pi_ix]:$goods_infos[$i][pi_ix])."<input type=hidden name='pi_ix[]' value='".$goods_infos[$i][pi_ix]."'>
						</td-->
						<td class='list_box_td' style='padding:5px 10px;'>
							".$goods_infos[$i][company_name]."
						</td>
						<td class='list_box_td point' style='padding:5px 10px;'>
							".$goods_infos[$i][place_name]."
						</td>
						<!--td class='list_box_td' align=center style='padding:5px;' nowrap>".$goods_infos[$i][section_name]."</td-->
						<td class='list_box_td' align=center style='padding:5px;' nowrap>".number_format($goods_infos[$i][stock])."</td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap>".($stock_sum == 0 ? 0:number_format(($goods_infos[$i][stock])/$stock_sum*100,2))." %</td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap>".number_format($goods_infos[$i][stock_assets])."</td>
						<td class='list_box_td' align=center style='padding:5px;' nowrap>".($stock_assets_sum == 0 ? 0:number_format(($goods_infos[$i][stock_assets])/$stock_assets_sum*100,2))." %</td>
						
					</tr>
					";
            }



        }
    }
    $innerview .= "</table>";
    if($mmode != "report"){
        $innerview .= "
				<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=40><td>".($stock_status == "shortage" ? "<a href=\"javascript:PrintWindow('./print_stock.php?$QUERY_STRING',700,900,'print_stock')\">재고 내역서 출력</a>":"")."</td>
					<td align=right nowrap>".$str_page_bar."</td></tr>
				</table>";
    }else{
        $innerview .= "<br><br><br>";
    }

    $innerview .= "</form>";

    $Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
			";
    if($mmode != "report"){

        $help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td>사업장/창고별 상세 재고현황을 보실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td>재고자산은 원가법으로 산출하였으며, 현재 이동평균법의 재고원가를 산출됩니다. 약간의 오차가 발생할 수 있으며, 회계용으로 사용하지 마시고 참고용으로 사용하세요.</td></tr>
</table>
";

        $Contents .= HelpBox("사업장/창고별 재고현황", $help_text);
    }

}


if($info_type == "category"){
    if($mmode != "report"){
        $Contents .=	"
			<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
			<input type='hidden' name='mode' value='search'>
			<input type='hidden' name='cid2' value='$cid2'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='info_type' value='$info_type'>
			<input type='hidden' name='groupby_depth' value='$groupby_depth'>
			<tr height=50>
				<td colspan=2>
					<table class='box_shadow' style='width:100%;height:20px' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05 align=center' style='padding:0px'>
								<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='input_table_box'>
									<col width='150' >
									<col width='*' >
									<col width='150' >
									<col width='*' >
									<tr>
										<td class='input_box_title'><b>품목분류</b></td>
										<td class='input_box_item' colspan=3>
											<table border=0 cellpadding=0 cellspacing=0>
												<tr>
													<td style='padding-right:5px;'>".getInventoryCategoryList("전체", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
													<td style='padding-right:5px;'>".getInventoryCategoryList("전체", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
													<td style='padding-right:5px;'>".getInventoryCategoryList("전체", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
													<td>".getInventoryCategoryList("전체", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
												</tr>
											</table>
										</td>
									</tr>
									<!--tr>
										<td class='input_box_title'>품목분류별 합산 설정</td>
										<td class='input_box_item' colspan='3'>
											<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' >
											<tr>
												<td class='input_box_item'>
													<input type=radio name=groupby_depth class=nonborder value='1' id='groupby_depth_1' validation=true title='1차분류' checked ".($groupby_depth == "1" ? "checked":"")."><label for='groupby_depth_1'>1차분류</label>
													<input type=radio name=groupby_depth class=nonborder value=2 id='groupby_depth_2' validation=true title='2차분류' ".($groupby_depth == "2" ? "checked":"")."><label for='groupby_depth_2'>2차분류</label>
													<input type=radio name=groupby_depth class=nonborder value=3 id='groupby_depth_3' validation=true title='3차분류' ".($groupby_depth == "3" ? "checked":"")."><label for='groupby_depth_3'>3차분류</label>
													<input type=radio name=groupby_depth class=nonborder value=4 id='groupby_depth_4' validation=true title='4차분류' ".($groupby_depth == "4" ? "checked":"")."><label for='groupby_depth_4'>4차분류</label>
												</td>
											</tr>
											<tr>
												<td style='padding:10px; '>
													<span>
													품목분류별 합산 설정이란? 선택된 뎁스에 따라 상위와 같이 한산되어 나오는 설정 값으로 만약 1뎁스만 보고서를 <br>보고 싶을경우 사업장을 선택해주시고 그 하위창고까지 개별로 노출하여 재고를 파악을 원할 경우는 2~4뎁스를 선택하시면 됩니다.
													</span>
												</td>
											</tr>
											</table>
										</td>
									</tr-->
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
				</td>
			</tr>
			<tr >
				<td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
				</form>
			</tr>
			<tr>
			    <td align='right' colspan=4 style='padding:5px 0 5px 0;'>";

        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
            //$Contents .= "<a href='stock_report.php?".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
        }else{
            //$Contents .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
        }

        for ($i = 0; $i < count($goods_infos); $i++)
        {
            $stock_assets_sum += $goods_infos[$i][stock_assets];
            $stock_sum += $goods_infos[$i][stock];
        }

        $Contents .="
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width=70%>
	<col width=30%>
	<tr height=30 >
	<td align=left><input type='checkbox' name='groupby_depth' id='groupby_depth' onclick=\"".($groupby_depth == 1 ? "location.href='?".str_replace('&groupby_depth=1','&groupby_depth=0',$_SERVER["QUERY_STRING"])."'":"location.href='?".str_replace('&groupby_depth=0','',$_SERVER["QUERY_STRING"]).(strlen($_SERVER["QUERY_STRING"]) > 0 ? "&groupby_depth=1" : "groupby_depth=1")."'")."\" ".($groupby_depth == 1 ? "checked":"")." /><label for='groupby_depth'> 하부 카테고리 상세보기</label></td>
	<td align=right>
		<a href=\"javascript:PoPWindow3('stock_report.php?mmode=report&info_type=".$info_type."&".$QUERY_STRING."',970,800,'stock_report')\"><img src='../images/".$admininfo["language"]."/btn_report_print.gif'></a>
		<a href='excel_config.php?".$QUERY_STRING."' rel='facebox' ><span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></span></a>
		";

        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
            $Contents .= "<a href='stock_report.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
        }else{
            $Contents .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
        }

        $Contents .="

		</td>
	  </tr>
	</table>";

        $Contents .= "
				</td>
			</tr>
			<tr>

			<td valign=top style='padding-top:33px;'>";

        $Contents .=	"
			</td>
			<td valign=top style='padding:0px;padding-top:0px;' id=product_stock>
			";

    }

    $innerview = "
			<form name=stockfrm method=post action='product_stock.act.php' target='act'>
			<input type='hidden' name='act' value='update'>
			<input type='hidden' name='cid' value='$cid'>
			<input type='hidden' name='depth' value='$depth'>

			<table cellpadding=0 cellspacing=0  width='100%' class='list_table_box'>
			<col width='5%'>
			<col width='5%'>
			<col width='13%'>
			<col width='5%'>
			<col width='5%'>
			<col width='7%'>
			<col width='5%'>
			<tr align=center height=30>
				<td class=s_td rowspan='2'>순번</td>
				<td class=m_td rowspan='2'>품목분류코드</td>
				<td class=m_td rowspan='2'>품목분류</td>
				<td class=m_td  colspan='4'>재고현황</td>
			</tr>
			<tr align=center height=30>
				<td class=m_td>재고수량(개)</td>
				<td class=m_td>재고점유율(%)</td>
				<td class=m_td>재고자산(원)</td>
				<td class=m_td>자산점유율율(%)</td>
			
			</tr>
			";

    if(count($goods_infos) == 0){
        $innerview .= "<tr bgcolor=#ffffff height=50><td colspan=6 align=center> 해당되는  품목이 없습니다.</td></tr>";
    }else{


        $innerview .= "<tr height=35>
					<td class='list_box_td list_bg_gray' style='padding:0px 7px;' colspan=3 nowrap>
					합계
					</td>
					<td class='list_box_td' align=center style='padding:5px;' nowrap><b>".number_format($stock_sum)."</b></td>
					<td class='list_box_td' align=center style='padding:5px;' nowrap><b>100%</b></td>
					<td class='list_box_td' align=center style='padding:5px;' nowrap><b>".number_format($stock_assets_sum)."</b></td>
					<td class='list_box_td' align=center style='padding:5px;' nowrap><b>100%</b></td>
					
				</tr>
				";


        for ($i = 0; $i < count($goods_infos); $i++)
        {
            if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))){
                $img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c");
            }else{
                $img_str = "../image/no_img.gif";
            }
            $no = $i +1;

            $innerview .= "<tr height=35>
					<td class='list_box_td list_bg_gray' style='padding:0px 7px;' nowrap>
						".$no."<input type=hidden name='gid[]' value='".$goods_infos[$i][gid]."'>
					</td>
					<td class='list_box_td list_bg_gray' style='padding:0px 7px;' nowrap>
						".($goods_infos[$i][cid] ? $goods_infos[$i][cid]:$goods_infos[$i][cid])."<input type=hidden name='gid[]' value='".$goods_infos[$i][cid]."'>
					</td>
					<td class='list_box_td point' style='padding:5px 10px;text-align:left;'>
						".getIventoryCategoryPathByAdmin($goods_infos[$i][cid], 4)."
					</td>

					<td class='list_box_td' align=center style='padding:5px;' nowrap>".number_format($goods_infos[$i][stock])."</td>
					<td class='list_box_td' align=center style='padding:5px;' nowrap>".($stock_sum == 0 ? 0:number_format(($goods_infos[$i][stock])/$stock_sum*100,2))." %</td>
					<td class='list_box_td' align=center style='padding:5px;' nowrap>".number_format($goods_infos[$i][stock_assets])."</td>
					<td class='list_box_td' align=center style='padding:5px;' nowrap>".($stock_assets_sum == 0 ? 0:number_format(($goods_infos[$i][stock_assets])/$stock_assets_sum*100,2))." %</td>
				</tr>
				";
        }

    }
    $innerview .= "</table>";

    if($mmode != "report"){
        $innerview .= "
				<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=40><td>".($stock_status == "shortage" ? "<a href=\"javascript:PrintWindow('./print_stock.php?$QUERY_STRING',700,900,'print_stock')\">재고 내역서 출력</a>":"")."</td>
					<td align=right nowrap>".$str_page_bar."</td></tr>

				</table>";
    }

    $innerview .= "</form>";

    $Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
			";

    if($mmode != "report"){

        $help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td>품목분류별 상세 재고현황을 보실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td>재고자산은 원가법으로 산출하였으며, 현재 이동평균법의 재고원가를 산출됩니다. 약간의 오차가 발생할 수 있으며, 회계용으로 사용하지 마시고 참고용으로 사용하세요.</td></tr>
</table>
";

        $Contents .= HelpBox("품목 분류별 재고현황", $help_text);
    }

}

$Contents .="<object id='factory' style='display:none' viewastext classid='clsid:1663ed61-23eb-11d2-b92f-008048fdd814'
codebase='http://".$_SERVER["HTTP_HOST"]."/admin/order/scriptx/smsx.cab#Version=7,1,0,60'>
</object>";

if($view == "innerview"){
    echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
    $inner_category_path = getCategoryPathByAdmin($cid, $depth);
    echo "
	<Script>
	parent.document.getElementById('product_stock').innerHTML = document.body.innerHTML;
	parent.document.getElementById('select_category_path1').innerHTML='".$inner_category3_path."';
	</Script>";
}else{
    $Script = "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<!--script Language='JavaScript' src='../include/zoom.js'></script-->\n
	<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>
	<script Language='JavaScript' type='text/javascript'>
	
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
		

		$('#max').change(function(){
			var value= $(this).val();
			$.cookie('inventory_goods_max_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});
			document.location.reload();
		});

	//다중검색어 끝 2014-04-10 이학봉

	});

	function inventory_initialization(){
		if(confirm('재고를 초기화 하시겠습니까?')){
			if(confirm('재고를 초기화 하시면 데이터가 초기화되어 복구 할수 없습니다. 정말로 하시겠습니까?')){
				window.frames['act'].location.href='./inventory_goods_input.act.php?act=initialization';
			}
		}
	}

	function basic_warehouse_move (gu_ix,delivery_cnt,ps_ix,delivery_ps_ix) {
		if(confirm('기본창고로 이동하시겠습니까?')){
			$.ajax({ 
				type: 'GET', 
				data: {'act': 'warehouse_move','gu_ix':gu_ix,'delivery_cnt':delivery_cnt,'ps_ix':ps_ix,'delivery_ps_ix':delivery_ps_ix},
				url: './warehouse_move.act.php',  
				dataType: 'html', 
				async: false, 
				beforeSend: function(){ 
						//alert(11);
				},  
				success: function(data){ 			
					alert(data);
					document.location.reload();
				} 
			});
		}
	}

	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		var depth = sel.getAttribute('depth');

		//빈값일 경우에는 카테고리 정보 불러오는 파일에서 처리함 kbk 13/08/08
		//if(sel.selectedIndex!=0) {
			window.frames['act'].location.href = 'inventory_category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		//}

	}

	function reloadView(){
	
		if($('#view_shotage_goods').attr('checked') == true || $('#view_shotage_goods').attr('checked') == 'checked'){		
			$.cookie('view_shotage_goods', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{		
			$.cookie('view_shotage_goods', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
		
		document.location.reload();
	
	}

	
	var initBody ;

	function beforePrint() {
		initBody = document.body.innerHTML; document.body.innerHTML = document.getElementById('print_area').innerHTML;
		//alert(document.body.innerHTML);
	}

	function afterPrint() {
		document.body.innerHTML = initBody;
	}

	function printArea() {";

    if($mmode == "report"){
        $Script .= "	window.focus(); window.print();";
    }else{
        $Script .= "	window.print();";
    }
    $Script .= "
	}

	window.onbeforeprint = beforePrint;
	window.onafterprint = afterPrint;";

    if($mmode == "report"){
        $Script .= "
	$(document).ready(function() {
		printArea();
		//printPage();
	});";
    }

    $Script .= "

	function printPage() {
		//alert(1);

		factory.printing.header = ''; // Header에 들어갈 문장
		factory.printing.footer = ''; // Footer에 들어갈 문장
		factory.printing.portrait = true // true 면 세로인쇄, false 면 가로인쇄
		factory.printing.leftMargin = 0.2 // 왼쪽 여백 사이즈
		factory.printing.topMargin = 0.2 // 위 여백 사이즈
		factory.printing.rightMargin = 0.2 // 오른쪽 여백 사이즈
		factory.printing.bottomMargin = 0.2 // 아래 여백 사이즈
		factory.printing.Print(false,window) // 출력하기

	}

	</script>";

    if($mmode == "pop" || $mmode == "report"){
        $P = new ManagePopLayOut();
        $P->addScript = $Script;
        $P->Navigation = "재고관리 > 실시간 재고현황";
        $P->NaviTitle = "실시간 재고현황";
        $P->title = "실시간 재고현황";
        $P->strContents = $Contents;
        $P->OnloadFunction = "";
        $P->layout_display = false;
        echo $P->PrintLayOut();
    }else{
        $P = new LayOut();
        $P->strLeftMenu = inventory_menu();
        $P->addScript = $Script;
        $P->Navigation = "재고관리 > 실시간 재고현황";
        $P->title = "실시간 재고현황";
        $P->strContents = $Contents;



        $P->PrintLayOut();
    }
}


function ItemSummary($info_type = "all"){
    global $currency_display, $admin_config, $admininfo;
    global $groupby_pi_ix , $groupby_ps_ix, $stock_join_where;
    $mdb = new Database;
//print_r($admininfo["company_id"]);
    $vdate = date("Y-m-d", time());
    $today = date("Y-m-d", time());
    $firstday = date("Y-m-d", time()-84600*date("w"));
    $lastday = date("Y-m-d", time()+84600*(6-date("w")));


    if($_COOKIE[view_shotage_goods] != 1){
        $stock_join_type = " right join ";
    }else{
        $stock_join_type = " left join ";
    }


    if($info_type == "warehouse"){
        if($groupby_pi_ix || $groupby_ps_ix){

            if($groupby_pi_ix && $groupby_ps_ix){
                $groupby_str .= " group by  ips.pi_ix , ips.ps_ix  ";
            }else if($groupby_pi_ix){
                $groupby_str .= " group by  ips.pi_ix   ";
            }else if($groupby_ps_ix){
                $groupby_str .= " group by  ips.ps_ix    ";
            }
        }else{
            $groupby_str = "group by  ips.pi_ix , ips.ps_ix ";
        }
    }else if($info_type == "category"){

        if($groupby_depth){
            $groupby_str = "group by substr(cid,1,".($groupby_depth*3).") ";
        }else{
            $groupby_str = "group by cid ";
        }
    }else if($info_type == "detail"){
        $groupby_str = "group by gu.gid , gu.unit"; // 품목기준임으로 전체 검색과 조건이똑같다.
    }else{
        $groupby_str = "group by gu.gid , gu.unit";
    }


    if($admininfo[admin_level] == 9){
        //IFNULL(sum(case when is_use = 'Y'  then total_stock*avg_price else 0 end),0) as stock_price,->
        //IFNULL(sum(case when is_use = 'Y'  then total_stock*avg_price else 0 end),0) as stock_price,
        $sql = "Select 
				IFNULL(sum(case when is_use = 'N'  then 1 else 0 end),0) as is_use_N_whole,
				IFNULL(sum(case when is_use = 'Y'  then 1 else 0 end),0) as is_use_Y_whole,
				IFNULL(sum(case when is_use = 'Y'  then stock else 0 end),0) as stock,
				IFNULL(sum(case when is_use = 'Y'  then stock*avg_price else 0 end),0) as stock_price,
				IFNULL(sum(case when is_use = 'Y'  then stock*sellprice else 0 end),0) as stock_outprice
				from (
					select g.is_use, sum(ips.stock) as stock, gu.buying_price, gu.sellprice   , gu.total_stock, gu.avg_price
					from inventory_goods g 
					right join inventory_goods_unit gu on gu.gid = g.gid
					".$stock_join_type."  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
					".$groupby_str."
				) data  ";

    }else if($admininfo[admin_level] == 8){

        $sql = "Select 
				IFNULL(sum(case when is_use = 'N'  then 1 else 0 end),0) as is_use_N_whole,
				IFNULL(sum(case when is_use = 'Y'  then 1 else 0 end),0) as is_use_Y_whole,
				IFNULL(sum(case when is_use = 'Y'  then stock else 0 end),0) as stock,
				IFNULL(sum(case when is_use = 'Y'  then stock*avg_price else 0 end),0) as stock_price,
				IFNULL(sum(case when is_use = 'Y'  then stock*sellprice else 0 end),0) as stock_outprice
				from (
					select g.is_use, sum(ips.stock) as stock, gu.buying_price, gu.sellprice , gu.total_stock, gu.avg_price
					from inventory_goods g 
					right join inventory_goods_unit gu on gu.gid = g.gid
					".$stock_join_type."  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit ".$stock_join_where."
					where company_id = '".$admininfo["company_id"]."' 
					".$groupby_str."
				) data  ";
    }

    //echo nl2br($sql);
    $mdb->query($sql);
    $mdb->fetch();
    $item_summary = $mdb->dt;
    //$datas = $mdb->getrows(); ()', ' '


    $mstring = "<table width=100% cellpadding=0 cellspacing=0  border=0> 
				
				<tr>
					<td align='left'  width='100%' valign=top style='padding-top:5px;'>
					<table cellpadding=0 cellspacing=1 width='100%' border='0' bgcolor=silver class='input_table_box'>
						<col width='33%'>
						<col width='33%'>
						<col width='*'>
				
						<!--tr height=30  align=center>
							<th bgcolor='#efefef' align='center' colspan=4>현재 재고 현황</th>							
						</tr-->
						<tr height=30  bgcolor='#ffffff'>
							<th bgcolor='#efefef' >품목목록수 </th>
							<th bgcolor='#efefef' >재고총수량</th>
							<th bgcolor='#efefef' >재고총자산</th>
						</tr>
						<tr height=30  bgcolor='#ffffff' align=center>
							<td align='center'>".number_format($item_summary[is_use_Y_whole]+$item_summary[is_use_N_whole])."</td>
							<td align='center'>".number_format($item_summary[stock])."</td>
							<td align='center' style='font-weight:bold;'>".number_format($item_summary[stock_price])."</td>
						</tr>
						";
    /*
    for($i=0;$i<count($datas)+1;$i++){

            $z = $i-1;
            $mstring .= "
                <tr height=30  bgcolor='#ffffff' >
                    <th bgcolor='#efefef' align='center'>".$datas[$z][day]." </th>
                    <td style='padding-right:15px;'> ".number_format($datas[$z][incom_ready_cnt])." 건</td>
                    <td style='padding-right:15px;'> ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($datas[$z][incom_ready_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
                    <td style='padding-right:15px;'> ".number_format($datas[$z][incom_complete_cnt])." 건</td>
                    <td style='padding-right:15px;'> ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($datas[$z][incom_complete_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
                </tr>";
    }
    */
    $mstring .= "
						 
					</table>
					</td>
				</tr>
				<tr>
					<td style='padding:5px 0px;text-align:right;'>.</td>
				</tr>
			</table>";
    return $mstring;
}

function getOrderCnt($gid,$type){
    $db = new database;
    if($type == 'complete'){
        $sql = "select sum(pcnt) pcnt from shop_order_detail where gid = '".$gid."' and status = 'BF'  ";
    }else if($type == 'cancel'){
        $sql = "select sum(pcnt) pcnt from shop_order_detail where gid = '".$gid."' and status in ('CA','CC','RA','RC')  ";
    }

    $db->query($sql);
    $db->fetch();
    return $db->dt['pcnt'];
}

function getErpSotck($gid){
    $db = new database;

    $sql = "select sum(stock) pcnt from tmp_sgdata_stock where gid = '".$gid."' and  process_yn ='Y' group by gid ";


    $db->query($sql);
    $db->fetch();
    return $db->dt['pcnt'];
}
?>