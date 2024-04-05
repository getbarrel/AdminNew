<?
include($_SERVER["DOCUMENT_ROOT"]."/shop/common/util.php");
include("../class/layout.class");
include('../../include/xmlWriter.php');
header("Pragma: no-cache");
header('Content-Type: text/xml; charset=utf-8');
function PrintProductListXML($cid, $depth){
    global $start,$page, $orderby, $admin_config,$mall_ix;
    global $max, $page, $start, $mode, $disp;
    global $company_id, $search_type, $search_text;
    global $admininfo,$product_image_column_str,$product_type, $state, $one_commission;
    global $service_type, $soho, $designer, $mirrorpick;

    if($max == ""){
        $max = 5; //푄L쨋쨈?걔뮤숏?else{
        $max = $max;
    }

    if ($page == ''){
        $start = 0;
        $page  = 1;
    }else{
        if($max !== 'nolimit')
            $start = ($page - 1) * $max;
    }

    $db = new Database;

    if($admininfo[admin_level] < 9){
        $product_company_id_str = " and p.admin = '".$admininfo[company_id]."' ";
    }

    if($mode == "list"){
        //$db->query('SELECT COUNT(DISTINCT(p.id)) as cnt FROM '.TBL_SHOP_PRODUCT.' p INNER JOIN '.TBL_SHOP_PRODUCT_RELATION.' pr ON pr.pid = p.id WHERE pr.cid LIKE "'.substr($cid, 0, (($depth+1) * 3)).'%" AND p.disp = '1' '.$product_company_id_str);
        if($product_type == 'estimate' || $service_type == "coupon"){
            //$product_type_where = " and p.product_type !='99' ";
        }else if($product_type == '77'){
            //$product_type_where = " and p.product_type ='77' ";
        }else if($product_type == 'basic'){
            $product_type_where = " and p.product_type ='0' ";
        }else if($service_type == 'discount'){
            $product_type_where = " and p.product_type ='0' ";
        }

        if($state){
            $product_type_where .= " and p.state ='".$state."' ";
        }

        if($soho){
            $product_type_where .= " and p.soho ='".$soho."' ";
        }

        if($designer){
            $product_type_where .= " and p.designer ='".$designer."' ";
        }

        if($mirrorpick){
            $product_type_where .= " and p.mirrorpick ='".$mirrorpick."' ";
        }

        if($one_commission){
            $product_type_where .= " and p.one_commission ='N' ";
        }

        if($mall_ix){
            $product_type_where .= " and p.mall_ix ='".$mall_ix."' ";
        }

        if($disp){
            $product_type_where .= " and p.disp ='".$disp."' ";
        }
        $sql = "SELECT COUNT(DISTINCT(p.id)) as cnt
				FROM ".TBL_SHOP_PRODUCT." p 
				INNER JOIN ".TBL_SHOP_PRODUCT_RELATION." pr ON pr.pid = p.id
				WHERE pr.cid LIKE '".substr($cid, 0, (($depth+1) * 3))."%'  
				and p.is_delete = 0
				$product_type_where ".$product_company_id_str;

        $db->query($sql);
        //AND p.disp = '1'
        $db->fetch();
        $total = $db->dt[cnt];

        $sql = "SELECT distinct p.id, p.pcode, p.pname, p.sellprice,p.reserve,vieworder , p.view_cnt, p.regdate, brand_name,p.coprice,
                        p.wholesale_price,p.wholesale_sellprice,p.listprice, p.disp, p.state ,p.stock,p.sell_ing_cnt,
                        p.one_commission ".$product_image_column_str." 
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r 
					where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' 
					and p.is_delete = 0
					$product_type_where $product_company_id_str 
					order by vieworder desc";
        if($max !== 'nolimit')
            $sql .= " LIMIT $start, $max ";
//		echo nl2br($sql);
        //exit;
        $db->query($sql);
        //and p.disp = '1'
    }else{
        $orderByList = array();
        if($search_type && $search_text){

            $search_text = str_replace("<br />","",$search_text);
            //$search_text = iconv("utf-8","CP949", $search_text);
            //$search_str = " and ".$search_type." LIKE '%".$search_text."%'";

            //다중검색 시작 2014-04-10 이학봉
            if($search_text != ""){
                /*
                2014.10.22 신훈식
                1. 콤마가 포함되어 등록된 상품명의 경우는 , 로 분리하지 않는다.
                    - 아래  && $search_type != "p.pname" 내용 추가
                */
                if(strpos($search_text,",") !== false && $search_type != "p.pname"){
                    $search_array = explode(",",$search_text);
                    $search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
                    $search_str .= "and ( ";

                    for($i=0;$i<count($search_array);$i++){
                        $search_array[$i] = trim($search_array[$i]);
                        if($search_array[$i]){
                            if($i == count($search_array) - 1){
                                $search_type." LIKE '%".trim($search_array[$i])."%'";
                            }else{
                                $search_type." LIKE '%".trim($search_array[$i])."%' or ";
                            }
                            $orderByList[] = $search_array[$i];
                        }
                    }
                    $search_str .= ")";
                }else if(strpos($search_text,"<br>") !== false){//\n
                    $search_array = explode("<br>",$search_text);
                    $search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
                    $search_str .= "and ( ";

                    for($i=0;$i<count($search_array);$i++){
                        $search_array[$i] = trim($search_array[$i]);
                        if($search_array[$i]){
                            if($i == count($search_array) - 1){
                                $search_str .= $search_type." LIKE '%".trim($search_array[$i])."%'";
                            }else{
                                $search_str .= $search_type." LIKE '%".trim($search_array[$i])."%' or ";
                            }
                            $orderByList[] = $search_array[$i];
                        }
                    }
                    $search_str .= ")";
                }else{
                    $search_str .= " and ".$search_type." LIKE '%".trim($search_text)."%'";
                    $orderByList[] = $search_array[$i];
                }
            }

        }
        if($company_id){
            $company_id_str = " and p.admin = '".$company_id."'";
        }

        if($product_type == 'estimate' || $service_type == "coupon"){
            //$product_type_where = " and p.product_type !='99' ";
        }else if($product_type == '77'){
            //$product_type_where = " and p.product_type ='77' ";
        }else if($product_type == 'basic'){
            $product_type_where = " and p.product_type ='0' ";
        }else if($service_type == 'discount'){
            $product_type_where = " and p.product_type ='0' ";
        }

        if($state){
            $product_type_where .= " and p.state ='".$state."' ";
        }

        if($soho){
            $product_type_where .= " and p.soho ='".$soho."' ";
        }

        if($designer){
            $product_type_where .= " and p.designer ='".$designer."' ";
        }

        if($mirrorpick){
            $product_type_where .= " and p.mirrorpick ='".$mirrorpick."' ";
        }

        if($disp){
            $product_type_where .= " and p.disp ='".$disp."' ";
        }

        if($one_commission){
            $product_type_where .= " and p.one_commission ='N' ";
        }

        if($mall_ix){
            $product_type_where .= " and p.mall_ix ='".$mall_ix."' ";
        }

        if($product_type != '77'){
            $tableOfJoinTotal = " INNER JOIN ".TBL_SHOP_PRODUCT_RELATION." r ON r.pid = p.id ";
            $tableOfJoin = " INNER JOIN ".TBL_SHOP_PRODUCT_RELATION." r ON r.pid = p.id ";
            $whereOfJoin = " p.id = r.pid and p.product_type !='77' and ";
        }

        $tableOfJoinTotal .= " left join inventory_goods_unit i on p.pcode=i.gu_ix ";
        $tableOfJoin .= " left join inventory_goods_unit i on p.pcode=i.gu_ix ";

        if(($search_type == 'p.id' || $search_type == 'p.pcode') && count($orderByList)>0){
            $order_by_str = "order by (case ";
            foreach($orderByList as $key => $ob){
                $order_by_str .= " when  $search_type like '%".$ob."%' then ".$key;
            }
            $order_by_str .= " else 9999 end) asc , id asc";
        }else{
            $order_by_str = "order by vieworder desc";
        }

        if($search_type == 'p.pcode'){
            $gid_str = str_replace("and"," or ",$search_str);
            $gid_str = str_replace("p.pcode","i.gid",$gid_str);
            $order_by_str = str_replace("p.pcode","i.gid",$order_by_str);
            $search_str .= $gid_str;
        }
        if($search_type == 'p.id'){
            $gid_str = str_replace("and"," or ",$search_str);
            $search_str .=  $gid_str;
        }
        //echo "SELECT COUNT(DISTINCT(p.id)) as cnt FROM ".TBL_SHOP_PRODUCT." p ".$tableOfJoin." WHERE ".$whereOfJoin." p.is_delete = 0 ".$product_type_where.$search_str.$company_id_str.$product_company_id_str;
        $db->query("SELECT COUNT(DISTINCT(p.id)) as cnt FROM ".TBL_SHOP_PRODUCT." p ".$tableOfJoin." WHERE ".$whereOfJoin." p.is_delete = 0 ".$product_type_where.$search_str.$company_id_str.$product_company_id_str);
        // p.disp = '1'
        $db->fetch();
        $total = $db->dt[cnt];

        $sql = "SELECT 
					distinct p.id, p.pcode, p.soho, p.designer, p.mirrorpick,
					p.pname, p.sellprice,  p.reserve ,vieworder ,
					brand_name ,p.coprice,p.wholesale_price,
					p.wholesale_sellprice,p.listprice , p.disp, p.state, p.one_commission, p.product_type, i.gid,
					p.stock,p.sell_ing_cnt,
					if(p.is_sell_date = '1',p.sell_priod_sdate <= NOW() and p.sell_priod_edate >= NOW(),'1=1')
					".$product_image_column_str." 
				FROM 
					".TBL_SHOP_PRODUCT." p ".$tableOfJoin."
				where 
					".$whereOfJoin." p.is_delete = 0

					$product_type_where 
					$search_str 
					$company_id_str 
					$product_company_id_str 
					$order_by_str";
        if($max !== 'nolimit')
            $sql .= " LIMIT $start, $max ";
        //					and p.disp = '1'
        $db->query($sql);
    }
    $products = $db->fetchall();

    if(count($products)){
        $script_times["product_discount_start"] = time();
        for($i=0 ; $i < count($products) ;$i++){
            $_array_pid[] = $products[$i][id];
            $goods_infos[$products[$i][id]][pid] = $products[$i][id];
            $goods_infos[$products[$i][id]][amount] = $products[$i][pcount];
            $goods_infos[$products[$i][id]][cid] = $products[$i][cid];
            $goods_infos[$products[$i][id]][depth] = $products[$i][depth];
        }

        $discount_info = DiscountRult($goods_infos, $cid, $depth);

        if(is_array($products))
        {
            foreach ($products as $key => $sub_array) {
                $select_ = array("icons_list"=>explode(";",$sub_array[icons]));
                array_insert($sub_array,50,$select_);
                //echo str_pad($sub_array[id], 10, "0", STR_PAD_LEFT)."<br>";
                $discount_item = $discount_info[$sub_array[id]];
                //print_r($discount_item);
                $_dcprice = $sub_array[sellprice];
                if(is_array($discount_item)){
                    foreach($discount_item as $_key => $_item){
                        if($_item[discount_value_type] == "1"){ // %
                            //echo $_item[discount_value]."<br>";
                            $_dcprice = roundBetter($_dcprice*(100 - $_item[discount_value])/100, $_item[round_position], $_item[round_type]);//$_dcprice*(100 - $_item[discount_value])/100;
                        }else if($_item[discount_value_type] == "2"){// 원
                            $_dcprice = $_dcprice - $_item[discount_value];
                        }
                        $discount_desc[] = $_item;//array("discount_type"=>$_item[discount_type], "haddoffice_value"=>$_item[discount_value], "discount_value"=>$_item[discount_value],
                    }
                }
                $_dcprice = array("dcprice"=>$_dcprice);
                array_insert($sub_array,72,$_dcprice);
                $discount_desc = array("discount_desc"=>$discount_desc);
                array_insert($sub_array,73,$discount_desc);
                $products[$key] = $sub_array;
                if($products[$key][uf_valuation] != "") $products[$key][uf_valuation] = round($products[$key][uf_valuation], 0);
                else $products[$key][uf_valuation] = 0;
            }
            //print_r($products);
        }
    }

    if(count($products)){
        $xml = new XmlWriter_();
        $xml->push('relationProducts', array('total'=>$total));

        for ($i=0;$i < count($products);$i++) {
            //$db->fetch($i);

            $state = $products[$i][state];
            if($products[$i][disp] != '1' ) {
                $state = 88;
            }
            if($products[$i][state] != '1'){
                $state = 88;
            }
            if(($products[$i][stock] - $products[$i][sell_ing_cnt]) < 1){
                $state = 88;
            }


            $disp = $products[$i][disp];


            $xml->push('products', array('pid' => $products[$i][id]));
            $xml->element('pid', $products[$i][id]);
            $xml->element('tb_pid', "tb_".$products[$i][id]);
            //$xml->element('pname', strip_tags(htmlspecialchars($products[$i][pname])).$one_commission);    //
            $xml->element('pname', strip_tags(htmlspecialchars($products[$i][pname])));    //
            $xml->element('listprice', $products[$i][listprice]);
            $xml->element('sellprice', $products[$i][sellprice]);
            $xml->element('reserve', $products[$i][reserve]);
            $xml->element('vieworder', $products[$i][vieworder]);
            $xml->element('view_cnt', $products[$i][view_cnt]);
            $xml->element('regdate', $products[$i][regdate]);

            $xml->element('coprice', $products[$i][coprice]);
            $xml->element('wholesale_price', $products[$i][wholesale_price]);
            $xml->element('wholesale_sellprice', $products[$i][wholesale_sellprice]);
            $xml->element('opn_ix', $products[$i][opn_ix]);
            $xml->element('option_name', $products[$i][option_name]);
            $xml->element('state', $state);
            $xml->element('disp', $disp);
            $xml->element('dcprice', $products[$i][dcprice]);
            $xml->element('one_commission', $products[$i][one_commission]);
            $xml->element('product_type', $products[$i][product_type]);
            $xml->element('gid', $products[$i][gid]);

            $xml->element('soho', $products[$i][soho]);
            $xml->element('designer', $products[$i][designer]);
            $xml->element('mirrorpick', $products[$i][mirrorpick]);

            //$xml->element('pname', $relation_good[3]);    //iconv('EUC-KR','UTF-8',strip_tags($relation_good[3]))
            //$xml->element('img_src', $admin_config[mall_data_root]."/images/product/c_".$relation_good[0].".gif");
            //$xml->element('img_src', PrintImage($admin_config['mall_data_root'].'/images/product', $products[$i][id], 'c', $products[$i] ));
            $xml->element('img_src', PrintImage($admin_config['mall_data_root'].'/images/addimgNew', $products[$i][id], 'slist', $products[$i] ));

            $xml->element('brand_name', strip_tags(htmlspecialchars($products[$i][brand_name])));
            $xml->pop();

        }

        $xml->pop();
        return $xml->getXml();
    }

}
if($mode == "list_pageing"){
    $db = new Database;
    if($admininfo[admin_level] < 9){
        $product_company_id_str = " and p.admin = '".$admininfo[company_id]."' ";
    }
    echo ("SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = '1' and p.is_delete = 0;");
    $db->query("SELECT count(distinct p.id) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = '1' and p.is_delete = 0 $product_company_id_str ;");
    $db->fetch();

    echo $db->dt[total];
}

if($mode == "search_list_pageing"){
    $db = new Database;

    if($search_type && $search_text){
        //$search_text = "째징쨔??
        $search_text = iconv("utf-8","CP949", $search_text);
        $search_str = " and ".$search_type." LIKE '%".$search_text."%'";
    }
    if($company_id){
        $company_id_str = " and p.admin = '".$company_id."'";
    }

    if($admininfo[admin_level] < 9){
        $product_company_id_str = " and p.admin = '".$admininfo[company_id]."' ";
    }

    //echo ("SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid  and p.disp = '1' $search_str $company_id_str ");
    $db->query("SELECT count(distinct p.id) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and p.disp = '1' and p.is_delete = 0 $search_str $company_id_str $product_company_id_str ");

    $db->fetch();

    echo $db->dt[total];
}

if($mode == "list" || $mode == "search_list"){
    echo PrintProductListXML($cid,$depth);
}

if($mode == "insert"){

    $db = new Database;


    $db->query("select max(vieworder)+1 as vieworder from ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp  where event_ix = '$event_ix' ");
    $db->fetch();
    $vieworder = $db->dt[vieworder];

    $db->query("select event_ix from ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp  where pid = '$pid' and event_ix = '$event_ix' ");

    if($db->total){
        echo "<script>alert('L쨔?쨉低쨉쨩都求褸');</script>
		<script type='text/javascript'>
		parent.Sortable.create('sortlist',
		{

			onUpdate: function()
			{
				//alert(parent.Sortable.serialize('sortlist'));
				new parent.Ajax.Request('/admin/marketting/event.act.php',
				{
					method: 'POST',
					parameters: parent.Sortable.serialize('sortlist')+'&act=vieworder_update&event_ix=$event_ix',
					onComplete: function(transport){
					//alert(transport.responseText);
					}
				});
			}
		});
		</script>";
    }else{

        $sql = "insert into ".TBL_SHOP_EVENT_PRODUCT_RELATION." (erp_ix,pid,event_ix,vieworder, regdate) values('$erp_ix','$pid','$event_ix','$vieworder',NOW())";

        $db->query($sql);


        echo "
		<html>
		<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
		<body>
		<div id='relation_product'>
		".relationProductList($event_ix)."
		</div>
		</body>
		</html>\n
		<Script Language='JavaScript'>
				parent.document.getElementById('relation_product').innerHTML=document.getElementById('relation_product').innerHTML;
		</script>
		<script type='text/javascript'>
		parent.Sortable.create('sortlist',
		{

			onUpdate: function()
			{
				//alert(parent.Sortable.serialize('sortlist'));
				new parent.Ajax.Request('/admin/marketting/event.act.php',
				{
					method: 'POST',
					parameters: parent.Sortable.serialize('sortlist')+'&act=vieworder_update&event_ix=$event_ix',
					onComplete: function(transport){
					//alert(transport.responseText);
					}
				});
			}
		});
		</script>";
    }

}

if($mode == "delete"){

    $db = new Database;



    $db->query("delete from ".TBL_SHOP_EVENT_PRODUCT_RELATION." where erp_ix = '$erp_ix' ");


    echo "
		<html>
		<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
		<body>
		<div id='relation_product'>
		".relationProductList($event_ix)."
		</div>
		</body>
		</html>\n
		<Script Language='JavaScript'>
				parent.document.getElementById('relation_product').innerHTML=document.getElementById('relation_product').innerHTML;
		</script>
<script type='text/javascript'>
parent.Sortable.create('sortlist',
{

	onUpdate: function()
	{
		//alert(parent.Sortable.serialize('sortlist'));
		new parent.Ajax.Request('/admin/marketting/event.act.php',
		{
			method: 'POST',
			parameters: parent.Sortable.serialize('sortlist')+'&act=vieworder_update&event_ix=$event_ix',
			onComplete: function(transport){
			//alert(transport.responseText);
			}
		});
	}
});
</script>
";

}


function PrintProductList($cid, $depth){
    global $start,$page, $orderby, $admin_config;

    $max = 105;

    if ($page == ''){
        $start = 0;
        $page  = 1;
    }else{
        $start = ($page - 1) * $max;
    }

    $db = new Database;

    $db->query("SELECT distinct p.id,p.pname, p.sellprice,  p.reserve FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = '1'   ");
    $total = $db->total;


    $db->query("SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = '1' order by vieworder desc limit $start,$max");


    $mString = "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver style='font-size:10px;'>";

    /*	$mString .= "<tr align=center bgcolor=#efefef height=25>
                <td class=s_td><!--input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.estimatefrm)'--></td>
                <td class=m_td width=20% >쨩?湄屎d>
                <td class=m_td width=60%>쨩?낯類d>
                <td class=m_td width=20%>째징째蒡/td>
                <!--td width=70 class=e_td>쨩??/td-->
                </tr>";
    */
    if ($db->total == 0){
        $mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=5 align=center>쨉低쨉쨩d쨘쨍째징 쩐占승닝?/td></tr>";
    }else{
        $i=0;
        for($i=0;$i<$db->total;$i++){
            $db->fetch($i);
            $mString .= "<tr height=27 bgcolor=#ffffff>
						<td class=table_td_white align=left style='padding:5px;'>
							<div id='".$db->dt[id]."' style='cursor:hand;'><!--dragable='true' ondragstart='return false'  -->
								<table bgcolor=#ffffff>
									<tr>
										<td><img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif' align=absmiddle></td>
										<td> ".cut_str($db->dt[pname],70)."</td>
									</tr>
								</table>
							</div>
						</td>
						</tr>";
            $mString .= "<tr height=1><td background='../image/dot.gif'></td></tr>";

            $mScript .= "new parent.Draggable('parent.".$db->dt[id]."', {revert: true});\n";
        }
    }
    //$str_page_bar = product_page_bar($total, $page,$max, "&view=innerview&max=$max&cid=$cid");

    //$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
    $mString .= "<tr align=right bgcolor=#ffffff height=30><td colspan=5 align=left >$str_page_bar</td></tr>";
    $mString = $mString."</table>

	";

    $mScript = "<script>\n$mScript</script>";

    return $mString.$mScript;

}


function relationProductList($event_ix){

    global $start,$page, $orderby, $admin_config, $erpid;

    $max = 105;

    if ($page == ''){
        $start = 0;
        $page  = 1;
    }else{
        $start = ($page - 1) * $max;
    }

    $db = new Database;

    $sql = "SELECT distinct p.id,p.pname, p.sellprice,  p.reserve, event_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix' and p.disp = '1'   ";
    $db->query($sql);
    $total = $db->total;

    $sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, event_ix, erp_ix, erp.vieworder
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix'  and p.disp = '1' order by erp.vieworder asc limit $start,$max";
    $db->query($sql);




    if ($db->total == 0){
        $mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";
        $mString = $mString."<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'>쨉低쨉L쨘짜트/짹?? 쨩?d쨘쨍째징 쩐占승닝? <br> 캡 쨩??쨩 L째占싱쨉藥≤絿첩?r>L쨘짜트/짹?? 쨩??쨍쨌?쨉低쨉絳求</td></tr>";
    }else{
        $mString = "<ul id='sortlist'>";

        $i=0;



        for($i=0;$i<$db->total;$i++){
            $db->fetch($i);

            $mString .= "<li id='image_".$db->dt[id]."' >
						<table width=99% border=0 >
						<col width='60'>
						<col width='*'>
						<col width='60'>
						<tr height=27 bgcolor=#ffffff >
						<td class=table_td_white align=center style='padding:5px;'>
							<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif'>
						</td>
						<td class=table_td_white>".cut_str($db->dt[pname],30)."</td>
						<td><a href='relation.category.act.php?mode=delete&event_ix=".$event_ix."&erp_ix=".$db->dt[erp_ix]."'  target=act><img src='../image/btc_del.gif'></a></td>
						</tr><tr height=1><td colspan=5 background='../image/dot.gif'></td></tr>
						</table></li>";
        }
    }

    $mString = $mString."</ul>";

    return $mString;

}



function relationProductList2($event_ix){

    global $start,$page, $orderby, $admin_config;

    $max = 105;

    if ($page == ''){
        $start = 0;
        $page  = 1;
    }else{
        $start = ($page - 1) * $max;
    }

    $db = new Database;

    $sql = "SELECT distinct p.id,p.pname, p.sellprice,  p.reserve, event_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix' and p.disp = '1'   ";
    $db->query($sql);
    $total = $db->total;

    $sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, event_ix, erp_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix'  and p.disp = '1' order by erp.vieworder  limit $start,$max";
    $db->query($sql);




    if ($db->total == 0){
        $mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";
        $mString = $mString."<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'>쨉低쨉L쨘짜트/짹?? 쨩?d쨘쨍째징 쩐占승닝? <br> 캡 쨩??쨩 L째占싱쨉藥≤絿첩?r>L쨘짜트/짹?? 쨩??쨍쨌?쨉低쨉絳求</td></tr>";
    }else{
        $mString = "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver style='font-size:10px;'>";

        $i=0;
        for($i=0;$i<$db->total;$i++){
            $db->fetch($i);
            $mString .= "<tr height=27 bgcolor=#ffffff >
						<td class=table_td_white align=center style='padding:5px;'>
							<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif'>
						</td>
						<td class=table_td_white>".cut_str($db->dt[pname],30)."</td>
						<td><a href='relation.category.act.php?mode=delete&event_ix=".$event_ix."&erp_ix=".$db->dt[erp_ix]."'  target=act><img src='../image/btc_del.gif'></a></td>
						</tr>";
            $mString .= "<tr height=1><td colspan=5 background='../image/dot.gif'></td></tr>";
        }
    }
    //$str_page_bar = product_page_bar($total, $page,$max, "&view=innerview&max=$max&cid=$cid");

    //$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
    $mString .= "<tr align=right bgcolor=#ffffff height=30><td colspan=5 align=left >$str_page_bar</td></tr>";
    $mString = $mString."</table>

	";

    return $mString;

}


?>