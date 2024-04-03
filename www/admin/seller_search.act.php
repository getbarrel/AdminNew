<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include_once("./class/layout.class");
$db = new Database;
$db2 = new Database;

if($act == "select_delivery_template"){
	
	if($mode == 'select_company'){
		if($company_id){//a689d35faa8c8412865c07ff7275eb3a

			$sql = "select 
					* 
					from
						shop_delivery_template
					where
						company_id = '".$company_id."'
						order by dt_ix ASC";
			$db->query($sql);
			$template_array = $db->fetchall();

			for($jj=0;$jj<count($template_array);$jj++){
				$template_text = get_delivery_policy_text($template_array,$jj);
				$data[select][$template_array[$jj][product_sell_type]][$template_array[$jj][delivery_div]][$template_array[$jj][dt_ix]] = $template_text;
			}

			$sql = "select * from shop_delivery_template where company_id = '".$company_id."' and is_basic_template = '1' and product_sell_type = 'R'";
			$db->query($sql);
			$template_array = $db->fetchall();

			if(count($template_array) > 0){
				$template_text = get_delivery_policy_text($template_array,'0');
				$data[input][$template_array[0][product_sell_type]][$template_array[0][delivery_div]][$template_array[0][dt_ix]] = $template_text;
			}
	
		}
	}else{
		if($company_id){//a689d35faa8c8412865c07ff7275eb3a

			$sql = "select 
					* 
					from
						shop_delivery_template
					where
						company_id = '".$company_id."'
						and product_sell_type = '".$product_sell_type."'
						and delivery_div = '".$delivery_div."'
						order by dt_ix ASC";
			$db->query($sql);
			$template_array = $db->fetchall();

			for($jj=0;$jj<count($template_array);$jj++){
				$template_text = get_delivery_policy_text($template_array,$jj);
				$data[$template_array[$jj][dt_ix]] = $template_text;
			}
		
		}

	}



	$datas = $data;
	$datas = json_encode($datas);
	$datas = str_replace("\"true\"","true",$datas);
	$datas = str_replace("\"false\"","false",$datas);
	echo $datas;

}

?>