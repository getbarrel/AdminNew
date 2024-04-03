<?
/*
stock_options 일경우 파일옵션과,텍스트옵션을 위한 사용유무 구분값 추가 2014-03-04 이학봉
text_option_use = '".($_options["text_option_use"] == "" ? "0":"1")."',
file_option_use = '".($_options["file_option_use"] == "" ? "0":"1")."'
*/
function OptionUpdate($mdb, $pid, $_options, $option_kind="x"){
	//$mdb->debug = true;
	//print_r($_options);
		if($_options["option_name"]){
			if($_options["opn_ix"]){
				$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and opn_ix = '".trim($_options["opn_ix"])."' and option_kind = '".$_options["option_kind"]."' ");
			}else{
				$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_name = '".trim($_options["option_name"])."' and option_kind = '".$_options["option_kind"]."' ");
			}

			if($_options["option_use"]){
				$_options_use = $_options["option_use"];
			}else{
				$_options_use = 0;
			}

			if(count($_options["global_oinfo"]) > 0){
				foreach($_options["global_oinfo"] as $colum => $li){
					foreach($li as $ln => $val){
						$_options["global_oinfo"][$colum][$ln] = urlencode($val);
					}
				}
			}
			
			$_options["global_oinfo"] = json_encode($_options["global_oinfo"]);

			if($mdb->total){
				$mdb->fetch();
				$opn_ix = $mdb->dt[opn_ix];

				$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set
								global_oinfo='".trim($_options["global_oinfo"])."',
								option_name='".trim($_options["option_name"])."', 
								option_kind='".$_options["option_kind"]."', 
								option_type='".$_options["option_type"]."',
								option_use='".($_options["option_use"] == "" ? "0":"1")."',
								box_total='".$_options["box_total"]."',
								text_option_use = '".($_options["text_option_use"] == "" ? "0":"1")."',
								file_option_use = '".($_options["file_option_use"] == "" ? "0":"1")."'
								where opn_ix = '".$opn_ix."' ";
								//echo nl2br($sql)."<br>";
				$mdb->query($sql);

                //글로벌 데이터 추가
                $sql = "update shop_product_options_global set
								global_oinfo='".trim($_options["global_oinfo"])."',
								option_name='".trim($_options["english_option_name"])."', 
								option_kind='".$_options["option_kind"]."', 
								option_type='".$_options["option_type"]."',
								option_use='".($_options["option_use"] == "" ? "0":"1")."',
								box_total='".$_options["box_total"]."',
								text_option_use = '".($_options["text_option_use"] == "" ? "0":"1")."',
								file_option_use = '".($_options["file_option_use"] == "" ? "0":"1")."'
								where opn_ix = '".$opn_ix."' ";
                //echo nl2br($sql)."<br>";
                $mdb->query($sql);
			}else{
				$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, global_oinfo, option_name, option_kind, option_type, option_use, box_total, regdate)
								VALUES
								('','$pid','".$_options["global_oinfo"]."','".$_options["option_name"]."','".$_options["option_kind"]."','".$_options["option_type"]."','".($_options["option_use"] == "" ? "0":"1")."','".$_options["box_total"]."',NOW())";
				$mdb->sequences = "SHOP_GOODS_OPTIONS_SEQ";

				$mdb->query($sql);

				if($mdb->dbms_type == "oracle"){
					$opn_ix = $mdb->last_insert_id;
				}else{
					$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
					$mdb->fetch();
					$opn_ix = $mdb->dt[opn_ix];
				}

				//글로벌 데이터 추가
                if(empty($_options["english_option_name"])){
                    $_options["english_option_name"] = $_options["option_name"];
                }
                $sql = "INSERT INTO shop_product_options_global (opn_ix, pid, global_oinfo, option_name, option_kind, option_type, option_use, box_total, regdate)
								VALUES
								('$opn_ix','$pid','".$_options["global_oinfo"]."','".$_options["english_option_name"]."','".$_options["option_kind"]."','".$_options["option_type"]."','".($_options["option_use"] == "" ? "0":"1")."','".$_options["box_total"]."',NOW())";
                $mdb->sequences = "SHOP_GOODS_OPTIONS_SEQ";

                $mdb->query($sql);
			}
			//echo $sql."<br>";
			//exit;

			$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set insert_yn='N' where opn_ix='".$opn_ix."' ";
			//echo $sql."<br><br>";
			$mdb->query($sql);
            $sql = "update shop_product_options_detail_global set insert_yn='N' where opn_ix='".$opn_ix."' ";
            $mdb->query($sql);
			$option_stock_yn = "";
			//for($i=0;$i < count($_options["details"]);$i++){
			$jj = 0;
			foreach($_options["details"] as $key => $details){	//옵션 키값이 일정하지 않음으로 for문을 쓰면안됨 2014-06-14 이학봉
				$_option_detail = $details;

                if (empty($_option_detail[english_option_size])) {
                    $_option_detail[english_option_size] = $_option_detail[option_size];
                }

                if($_option_detail['english_coprice'] == '') {
                    $_option_detail['english_coprice'] = getExchangeNationPrice($_option_detail['coprice']);
                }
                if($_option_detail['english_listprice'] == '') {
                    $_option_detail['english_listprice'] = getExchangeNationPrice($_option_detail['listprice']);
                }
                if($_option_detail['english_sellprice'] == '') {
                    $_option_detail['english_sellprice'] = getExchangeNationPrice($_option_detail['sellprice']);
                }

				if( $_options["option_type"] == 'o' ){//칼라+색상
					$option_div = $_option_detail[option_color] . '+' . $_option_detail[option_size];
                    $english_option_div = $_option_detail[english_option_color] . '+' . $_option_detail[english_option_size];
				}else if( $_options["option_type"] == 'c' ){//컬러
					$option_div = $_option_detail[option_color];
                    $english_option_div = $_option_detail[english_option_color];
				}else if( $_options["option_type"] == 's' ){//색상
					$option_div = $_option_detail[option_size];
                    $english_option_div = $_option_detail[english_option_size];
				}else{
					$option_div = $_option_detail[option_div];
                    $english_option_div = $_option_detail[english_option_div];
				}

				if($_option_detail[premiumprice] == ''){
				    $_option_detail[premiumprice] = 0;
                }

				if($_option_detail[soldout] == ''){
				    $_option_detail[soldout] = 0;
                }

				//for($j=0;$j < count($_option_details);$j++){
				//	$_option_detail = $_option_details[$j];
					
				//foreach($_option_detail as $opsd_key=>$opsd_value) {
					if($option_div){
						/*
						if($_option_detail[code] != "" ){
							$sql = "select item_stock, item_safestock from inventory_goods_item where gi_ix='".$_option_detail[code]."' ";
							$mdb->query($sql);
							if($mdb->total){
								$mdb->fetch();
								$_option_detail[stock] = $mdb->dt[item_stock];
								if($_option_detail[safestock] == "") $_option_detail[safestock] = $mdb->dt[item_safestock];
							}
						}
						*/
						
						/*
						$custom_option_div = customOptionDivDivision($_option_detail[option_div]);
						$_option_detail[option_color] = $custom_option_div['color'];
						$_option_detail[option_size] = $custom_option_div['size'];
						*/

						$_global_odinfo = array();
						//재고관리 상세는 구조가 좀틀림!
						if(count($_option_detail['global_odinfo']) > 0){
							foreach($_option_detail['global_odinfo'] as $colum => $li){
								foreach($li as $ln => $ln_array){
									foreach($ln_array as $_key => $val){
										$_global_odinfo[$key]['global_odinfo'][$colum][$ln] = urlencode($val);
									}
								}
							}
						}

						$_global_odinfo[$key]["global_odinfo"] = json_encode($_global_odinfo[$key]["global_odinfo"]);
//echo "<pre>";
//print_r($_global_odinfo[$key]["global_odinfo"]);
//exit;
						$mdb->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE option_div = '".trim($option_div)."' and opn_ix = '".$opn_ix."' and option_code = '".trim($_option_detail[code])."' ");

						if($mdb->total){
							$mdb->fetch();
							$opd_ix = $mdb->dt[id];

							$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
										global_odinfo='".$_global_odinfo[$key]["global_odinfo"]."',
										option_div='".$option_div."',
										set_group_seq = '".$jj."',
										option_code='".$_option_detail[code]."',
										option_gid='".$_option_detail[gid]."',
										option_coprice='".$_option_detail[coprice]."',
										option_listprice='".$_option_detail[listprice]."',
										option_price='".$_option_detail[sellprice]."',
										option_premiumprice='".$_option_detail[premiumprice]."',
										option_wholesale_listprice='".$_option_detail[wholesale_listprice]."',
										option_wholesale_price='".$_option_detail[wholesale_price]."',
										option_stock='".$_option_detail[stock]."',
										option_safestock='".$_option_detail[safestock]."' ,
										option_soldout='".$_option_detail[soldout]."' ,
										option_barcode='".$_option_detail[barcode]."' ,
										option_surtax_div = '".$_option_detail[option_surtax_div]."',
										option_color = '".$_option_detail[option_color]."',
										option_size = '".$_option_detail[option_size]."',
										option_etc1 = '".$_option_detail['set_cnt']."',
										insert_yn='Y'
										where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";
                            $mdb->query($sql);

                            $sql = "update shop_product_options_detail_global set
										global_odinfo='".$_global_odinfo[$key]["global_odinfo"]."',
										option_div='".$english_option_div."',
										set_group_seq = '".$jj."',
										option_code='".$_option_detail[code]."',
										option_gid='".$_option_detail[gid]."',
										option_coprice='".$_option_detail[english_coprice]."',
										option_listprice='".$_option_detail[english_listprice]."',
										option_price='".$_option_detail[english_sellprice]."',
										option_premiumprice='".$_option_detail[premiumprice]."',
										option_wholesale_listprice='".$_option_detail[wholesale_listprice]."',
										option_wholesale_price='".$_option_detail[wholesale_price]."',
										option_stock='".$_option_detail[stock]."',
										option_safestock='".$_option_detail[safestock]."' ,
										option_soldout='".$_option_detail[soldout]."' ,
										option_barcode='".$_option_detail[barcode]."' ,
										option_surtax_div = '".$_option_detail[option_surtax_div]."',
										option_color = '".$_option_detail[english_option_color]."',
										option_size = '".$_option_detail[english_option_size]."',
										option_etc1 = '".$_option_detail['set_cnt']."',
										insert_yn='Y'
										where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";

                            sellingCntUpdate($_option_detail[gid]);
						}else{
							$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." 
										(id, pid, global_odinfo, opn_ix, option_div, option_code,option_coprice,option_listprice, option_price, option_premiumprice, option_wholesale_listprice,  option_wholesale_price, option_stock, option_safestock, option_soldout, option_barcode, insert_yn, regdate,option_surtax_div, option_gid, set_group_seq, option_color, option_size,option_etc1) 
										values
										('','$pid','".$_global_odinfo[$key]["global_odinfo"]."','$opn_ix','".$option_div."','".$_option_detail[code]."','".$_option_detail[coprice]."','".$_option_detail[listprice]."','".$_option_detail[sellprice]."','".$_option_detail[premiumprice]."','".$_option_detail[wholesale_listprice]."','".$_option_detail[wholesale_price]."','".$_option_detail[stock]."','".$_option_detail[safestock]."','".$_option_detail[soldout]."', '".$_option_detail[barcode]."' ,'Y', NOW(),'".$_option_detail[option_surtax_div]."','".$_option_detail[gid]."', '".$jj."','".$_option_detail[option_color]."','".$_option_detail[option_size]."','".$_option_detail[etc]."') ";
                            $mdb->query($sql);

                            $_id = $mdb->insert_id();

                            $sql = "INSERT INTO shop_product_options_detail_global
										(id, pid, global_odinfo, opn_ix, option_div, option_code,option_coprice,option_listprice, option_price, option_premiumprice, option_wholesale_listprice,  option_wholesale_price, option_stock, option_safestock, option_soldout, option_barcode, insert_yn, regdate,option_surtax_div, option_gid, set_group_seq, option_color, option_size,option_etc1) 
										values
										('$_id','$pid','".$_global_odinfo[$key]["global_odinfo"]."','$opn_ix','".$english_option_div."','".$_option_detail[code]."','".$_option_detail[english_coprice]."','".$_option_detail[english_listprice]."','".$_option_detail[english_sellprice]."','".$_option_detail[premiumprice]."','".$_option_detail[wholesale_listprice]."','".$_option_detail[wholesale_price]."','".$_option_detail[stock]."','".$_option_detail[safestock]."','".$_option_detail[soldout]."', '".$_option_detail[barcode]."' ,'Y', NOW(),'".$_option_detail[option_surtax_div]."','".$_option_detail[gid]."', '".$jj."','".$_option_detail[english_option_color]."','".$_option_detail[english_option_size]."','".$_option_detail[etc]."') ";

						}
						$mdb->sequences = "SHOP_GOODS_OPTIONS_DT_SEQ";
						$mdb->query($sql);

						if($_option_detail[stock] == 0 && ($option_stock_yn == "" || $option_stock_yn == "R")){
							$option_stock_yn = "N";
						}

						if($_option_detail[stock] < $_option_detail[safestock] && $option_stock_yn == ""){
							$option_stock_yn = "R";
						}
					//}
				}

				$jj++;
			}
			$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
			//echo $sql;
			$mdb->query($sql);

            $sql = "delete from shop_product_options_detail_global where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
            //echo $sql;
            $mdb->query($sql);

			if($_options["option_kind"] == "b" && $_options_use){
				$sql = "SELECT sum(option_stock) as option_stock,sum(option_safestock) as option_safestock  
							FROM ".TBL_SHOP_PRODUCT_OPTIONS." po, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod 
							WHERE po.opn_ix = pod.opn_ix and po.option_use = 1 and pod.opn_ix='$opn_ix' and  pod.option_soldout != '1' ";

				$mdb->query($sql);

				$mdb->fetch();
				$option_stock = $mdb->dt[option_stock];
				$option_safestock = $mdb->dt[option_safestock];

				if($sell_ing_cnt == ""){
					$sell_ing_cnt = 0;
				}
				$mdb->query("update ".TBL_SHOP_PRODUCT." set stock = '".$option_stock."' ,safestock = '$option_safestock' where id ='$pid'");

                $mdb->query("update ".TBL_SHOP_PRODUCT."_global set stock = '".$option_stock."' ,safestock = '$option_safestock' where id ='$pid'");
				if($option_stock_yn){
					$mdb->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = '$option_stock_yn' where id ='$pid'");

                    $mdb->query("update ".TBL_SHOP_PRODUCT."_global set option_stock_yn = '$option_stock_yn' where id ='$pid'");
				}
			
			}

		}else{

			$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '".$pid."' and option_kind = '".$option_kind."'");

			if($mdb->total){
				$mdb->fetch();
				$opn_ix = $mdb->dt[opn_ix];
				$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."'  ";
				$mdb->query($sql);
                $sql = "delete from shop_product_options_detail_global where opn_ix='".$opn_ix."'  ";
                $mdb->query($sql);
			}
			$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '".$pid."' and option_kind = '".$option_kind."' ";
			$mdb->query($sql);
            $sql = "delete from shop_product_options_global where pid = '".$pid."' and option_kind = '".$option_kind."' ";
            $mdb->query($sql);


            if($option_kind == "b" ) {
                $mdb->query("update " . TBL_SHOP_PRODUCT . " set option_stock_yn = 'N' where id ='$pid'");
                $mdb->query("update " . TBL_SHOP_PRODUCT."_global set option_stock_yn = 'N' where id ='$pid'");
            }
		}
		$mdb->debug = false;
}


function SetOptionUpdate($mdb, $pid, $_options){
	//$mdb->debug = true;
	//print_r($_options);
	if(is_array($_options)){
		$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set
					insert_yn = 'N'
					where pid = '$pid'  and option_kind in ('s2','x2') ";
		$mdb->query($sql);

        $sql = "update shop_product_options_global set
					insert_yn = 'N'
					where pid = '$pid'  and option_kind in ('s2','x2') ";
        $mdb->query($sql);

	//for($x=0;$x < count($_options);$x++){
	$x = 0;
	foreach($_options as $key => $option_info){

		if($option_info["option_name"] && $x == 0){
			if($option_info["opn_ix"]){
				$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and opn_ix = '".trim($option_info["opn_ix"])."' and option_kind = '".$option_info["option_kind"]."' ");
			}else{
				$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_name = '".trim($option_info["option_name"])."' and option_kind = '".$option_info["option_kind"]."' ");
			}

			if($option_info["option_use"]){
				$_options_use = $option_info["option_use"];
			}else{
				$_options_use = 0;
			}

			if($mdb->total){
				$mdb->fetch();
				$opn_ix = $mdb->dt[opn_ix];

				$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set
								option_name='".trim($option_info["option_name"])."', 
								option_kind='".$option_info["option_kind"]."', 
								option_type='".$option_info["option_type"]."',
								option_use='".($option_info["option_use"] == "" ? "0":"1")."',
								box_total='".$option_info["box_total"]."',
								insert_yn = 'Y'
								where opn_ix = '".$opn_ix."' ";
				$mdb->query($sql);

                $sql = "update shop_product_options_global set
								option_name='".trim($option_info["english_option_name"])."', 
								option_kind='".$option_info["option_kind"]."', 
								option_type='".$option_info["option_type"]."',
								option_use='".($option_info["option_use"] == "" ? "0":"1")."',
								box_total='".$option_info["box_total"]."',
								insert_yn = 'Y'
								where opn_ix = '".$opn_ix."' ";
                $mdb->query($sql);
			}else{
				$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_type, option_use, box_total, insert_yn, regdate)
								VALUES
								('','$pid','".$option_info["option_name"]."','".$option_info["option_kind"]."','".$option_info["option_type"]."','".($option_info["option_use"] == "" ? "0":"1")."','".$option_info["box_total"]."','Y',NOW())";
				$mdb->sequences = "SHOP_GOODS_OPTIONS_SEQ";

				$mdb->query($sql);


				if($mdb->dbms_type == "oracle"){
					$opn_ix = $mdb->last_insert_id;
				}else{
					$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
					$mdb->fetch();
					$opn_ix = $mdb->dt[opn_ix];
				}

                //글로벌 데이터 추가
                if(empty($option_info["english_option_name"])){
                    $option_info["english_option_name"] = $option_info["option_name"];
                }

                $sql = "INSERT INTO shop_product_options_global (opn_ix, pid, option_name, option_kind, option_type, option_use, box_total, insert_yn, regdate)
								VALUES
								('$opn_ix','$pid','".$option_info["english_option_name"]."','".$option_info["option_kind"]."','".$option_info["option_type"]."','".($option_info["option_use"] == "" ? "0":"1")."','".$option_info["box_total"]."','Y',NOW())";
                $mdb->sequences = "SHOP_GOODS_OPTIONS_SEQ";

                $mdb->query($sql);
			}
			
			$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set insert_yn='N' where opn_ix='".$opn_ix."' ";
			$mdb->query($sql);
            $sql = "update shop_product_options_detail_global set insert_yn='N' where opn_ix='".$opn_ix."' ";
            $mdb->query($sql);
			//$set_group = $_options[$x]["option_kind"];
			$option_stock_yn = "";
		}
			//for($i=0;$i < count($_options[$x]["details"]);$i++){
			if(is_array($option_info["details"])){
				$i = 0;
				//print_r($option_info["details"]);
				foreach($option_info["details"] as $key => $details) {	//옵션 키값이 일정하지 않음으로 for문을 쓰면안됨 2014-06-14 이학봉
					$_option_detail = $details;

					//for($j=0;$j < count($_option_details);$j++){
					//	$_option_detail = $_option_details[$j];
						
					//foreach($_option_detail as $opsd_key=>$opsd_value) {
						if($_option_detail[option_div]){
							/*
							if($_option_detail[code] != "" ){
								$sql = "select item_stock, item_safestock from inventory_goods_item where gi_ix='".$_option_detail[code]."' ";
								$mdb->query($sql);
								if($mdb->total){
									$mdb->fetch();
									$_option_detail[stock] = $mdb->dt[item_stock];
									if($_option_detail[safestock] == "") $_option_detail[safestock] = $mdb->dt[item_safestock];
								}
							}
							*/

                            if($_option_detail['english_coprice'] == '') {
                                $_option_detail['english_coprice'] = getExchangeNationPrice($_option_detail['coprice']);
                            }
                            if($_option_detail['english_listprice'] == '') {
                                $_option_detail['english_listprice'] = getExchangeNationPrice($_option_detail['listprice']);
                            }
                            if($_option_detail['english_sellprice'] == '') {
                                $_option_detail['english_sellprice'] = getExchangeNationPrice($_option_detail['sellprice']);
                            }

							$mdb->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE option_div = '".trim($_option_detail[option_div])."' and opn_ix = '".$opn_ix."' and set_group = '".$x."'");

							if($mdb->total){
								$mdb->fetch();
								$opd_ix = $mdb->dt[id];

								$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
											set_group='".$x."',
											set_group_seq = '".$i."',
											option_div='".$_option_detail[option_div]."',
											option_code='".$_option_detail[code]."',
											option_gid='".$_option_detail[gid]."',
											option_coprice='".$_option_detail[coprice]."',
											option_listprice='".$_option_detail[listprice]."',
											option_price='".$_option_detail[sellprice]."',
											option_premiumprice='".$_option_detail[premiumprice]."',
											option_wholesale_listprice='".$_option_detail[wholesale_listprice]."',
											option_wholesale_price='".$_option_detail[wholesale_price]."',
											option_stock='".$_option_detail[stock]."',
											option_safestock='".$_option_detail[safestock]."' ,
											option_soldout='".$_option_detail[soldout]."' ,
											option_barcode='".$_option_detail[barcode]."' ,
											option_etc1='".$_option_detail[set_cnt]."' ,
											insert_yn='Y'
											where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";
                                $mdb->query($sql);

                                $sql = "update shop_product_options_detail_global set
											set_group='".$x."',
											set_group_seq = '".$i."',
											option_div='".$_option_detail[english_option_div]."',
											option_code='".$_option_detail[code]."',
											option_gid='".$_option_detail[gid]."',
											option_coprice='".$_option_detail[english_coprice]."',
											option_listprice='".$_option_detail[english_listprice]."',
											option_price='".$_option_detail[english_sellprice]."',
											option_premiumprice='".$_option_detail[premiumprice]."',
											option_wholesale_listprice='".$_option_detail[wholesale_listprice]."',
											option_wholesale_price='".$_option_detail[wholesale_price]."',
											option_stock='".$_option_detail[stock]."',
											option_safestock='".$_option_detail[safestock]."' ,
											option_soldout='".$_option_detail[soldout]."' ,
											option_barcode='".$_option_detail[barcode]."' ,
											option_etc1='".$_option_detail[set_cnt]."' ,
											insert_yn='Y'
											where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";
                                sellingCntUpdate($_option_detail[gid]);

							}else{
								$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." 
											(id, pid, opn_ix, set_group, set_group_seq, option_div, option_code,option_coprice,option_listprice, option_price, option_premiumprice, option_wholesale_listprice,  option_wholesale_price, option_stock, option_safestock, option_soldout, option_barcode,option_etc1, insert_yn, regdate, option_gid) 
											values
											('','$pid','$opn_ix','".$x."','".$i."','".$_option_detail[option_div]."','".$_option_detail[code]."','".$_option_detail[coprice]."','".$_option_detail[listprice]."','".$_option_detail[sellprice]."','".$_option_detail[premiumprice]."','".$_option_detail[wholesale_listprice]."','".$_option_detail[wholesale_price]."','".$_option_detail[stock]."','".$_option_detail[safestock]."','".$_option_detail[soldout]."', '".$_option_detail[barcode]."' ,'".$_option_detail[set_cnt]."' ,'Y', NOW(),'".$_option_detail[gid]."') ";
                                $mdb->query($sql);

                                $_id = $mdb->insert_id();

                                $sql = "INSERT INTO shop_product_options_detail_global 
											(id, pid, opn_ix, set_group, set_group_seq, option_div, option_code,option_coprice,option_listprice, option_price, option_premiumprice, option_wholesale_listprice,  option_wholesale_price, option_stock, option_safestock, option_soldout, option_barcode,option_etc1, insert_yn, regdate, option_gid) 
											values
											('$_id','$pid','$opn_ix','".$x."','".$i."','".$_option_detail[english_option_div]."','".$_option_detail[code]."','".$_option_detail[english_coprice]."','".$_option_detail[english_listprice]."','".$_option_detail[english_sellprice]."','".$_option_detail[premiumprice]."','".$_option_detail[wholesale_listprice]."','".$_option_detail[wholesale_price]."','".$_option_detail[stock]."','".$_option_detail[safestock]."','".$_option_detail[soldout]."', '".$_option_detail[barcode]."' ,'".$_option_detail[set_cnt]."' ,'Y', NOW(),'".$_option_detail[gid]."') ";
							}
							$mdb->sequences = "SHOP_GOODS_OPTIONS_DT_SEQ";
							//echo $sql."<br><br>";
							$mdb->query($sql);

							if($_option_detail[stock] == 0 && ($option_stock_yn == "" || $option_stock_yn == "R")){
								$option_stock_yn = "N";
							}

							if($_option_detail[stock] < $_option_detail[safestock] && $option_stock_yn == ""){
								$option_stock_yn = "R";
							}
						//}
					}
					$i++;
				}
			}
			$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
			//echo $sql;
			$mdb->query($sql);

            $sql = "delete from shop_product_options_detail_global where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
            //echo $sql;
            $mdb->query($sql);

			$sql = "SELECT max(option_stock) as option_stock,max(option_safestock) as option_safestock 
						FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." 
						WHERE opn_ix='".$opn_ix."'";

			$mdb->query($sql);
			$mdb->fetch();
			$option_stock = $mdb->dt[option_stock];
			$option_safestock = $mdb->dt[option_safestock];
			if($sell_ing_cnt == ""){
				$sell_ing_cnt = 0;
			}
			$mdb->query("update ".TBL_SHOP_PRODUCT." set stock = '".$option_stock."' ,safestock = '".$option_safestock."' where id ='".$pid."'");
			if($option_stock_yn){
				$mdb->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = '$option_stock_yn' where id ='".$pid."' ");
			}
			$x++;
		
	}

		$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_kind in ('s2','x2') and insert_yn = 'N' ");

		if($mdb->total){
			$mdb->fetch();
			$opn_ix = $mdb->dt[opn_ix];
			$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."'  ";
			$mdb->query($sql);

            $sql = "delete from shop_product_options_detail_global where opn_ix='".$opn_ix."'  ";
            $mdb->query($sql);
		}
		$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind in ('s2','x2') and insert_yn = 'N' ";
		$mdb->query($sql);

        $sql = "delete from shop_product_options_global where pid = '$pid' and option_kind in ('s2','x2') and insert_yn = 'N' ";
        $mdb->query($sql);

		/*
		
		$mdb->debug = false;
		*/
	}else{
		$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_kind in ('s2','x2') ");

		if($mdb->total){
			$mdb->fetch();
			$opn_ix = $mdb->dt[opn_ix];
			$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."'  ";
			$mdb->query($sql);

            $sql = "delete from shop_product_options_detail_global where opn_ix='".$opn_ix."'  ";
            $mdb->query($sql);
		}
		$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind in ('s2','x2') ";
		$mdb->query($sql);

        $sql = "delete from shop_product_options_global where pid = '$pid' and option_kind in ('s2','x2') ";
        $mdb->query($sql);
		//$mdb->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = 'N' where id ='$pid'");
	}
}

function CodiOptionUpdate($mdb, $pid, $_options){	//코디옵션추가 함수 2014-01-09 이학봉
	//$mdb->debug = true;
	//print_r($_options);
	if(is_array($_options)){	//기존 옵션 존재시 사용안함으로 변경
		$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set
					insert_yn = 'N'
					where pid = '$pid'  and option_kind in ('c') ";
		$mdb->query($sql);

        $sql = "update shop_product_options_global set
					insert_yn = 'N'
					where pid = '$pid'  and option_kind in ('c') ";
        $mdb->query($sql);
        //for($x=0;$x < count($_options);$x++){
	$x = 0;

        $codiOptionType = $_options[0]["option_type"];

	foreach($_options as $key => $option_info){
		//echo "option_name:".$option_info["option_name"];
		if($option_info["option_name"]){
				if($option_info["opn_ix"]){
					$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and opn_ix = '".trim($option_info["opn_ix"])."' and option_kind = '".$option_info["option_kind"]."' ");
				}else{
					$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_name = '".trim($option_info["option_name"])."' and option_kind = '".$option_info["option_kind"]."' ");
				}

				if($option_info["option_use"]){
					$_options_use = $option_info["option_use"];
				}else{
					$_options_use = 0;
				}

				if($mdb->total){	//기존 옵션 opn_ix 가 존재하면 새로운정보로 update
					$mdb->fetch();
					$opn_ix = $mdb->dt[opn_ix];

					$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set
									option_name='".trim($option_info["option_name"])."', 
									option_kind='".$option_info["option_kind"]."', 
									option_type='".$codiOptionType."',
									option_use='".($option_info["option_use"] == "" ? "0":"1")."',
									box_total='".$option_info["box_total"]."',
									option_vieworder='".$key."',
									insert_yn = 'Y'
									where opn_ix = '".$opn_ix."' ";
									
					$mdb->query($sql);

                    $sql = "update shop_product_options_global set
									option_name='".trim($option_info["english_option_name"])."', 
									option_kind='".$option_info["option_kind"]."', 
									option_type='".$codiOptionType."',
									option_use='".($option_info["option_use"] == "" ? "0":"1")."',
									box_total='".$option_info["box_total"]."',
									option_vieworder='".$key."',
									insert_yn = 'Y'
									where opn_ix = '".$opn_ix."' ";

                    $mdb->query($sql);
				}else{	// 없으면 입력
					$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_type, option_use, option_vieworder, box_total, insert_yn, regdate)
									VALUES
									('','$pid','".$option_info["option_name"]."','".$option_info["option_kind"]."','".$codiOptionType."','".($option_info["option_use"] == "" ? "0":"1")."','".$key."','".$option_info["box_total"]."','Y',NOW())";

					$mdb->sequences = "SHOP_GOODS_OPTIONS_SEQ";

					$mdb->query($sql);


					if($mdb->dbms_type == "oracle"){
						$opn_ix = $mdb->last_insert_id;
					}else{
						$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
						$mdb->fetch();
						$opn_ix = $mdb->dt[opn_ix];
					}

                    //글로벌 데이터 추가
                    if(empty($option_info["english_option_name"])){
                        $option_info["english_option_name"] = $option_info["option_name"];
                    }

                    $sql = "INSERT INTO shop_product_options_global (opn_ix, pid, option_name, option_kind, option_type, option_use, box_total, insert_yn, regdate)
									VALUES
									('$opn_ix','$pid','".$option_info["english_option_name"]."','".$option_info["option_kind"]."','".$codiOptionType."','".($option_info["option_use"] == "" ? "0":"1")."','".$option_info["box_total"]."','Y',NOW())";

                    $mdb->query($sql);
				}

				$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set insert_yn='N' where opn_ix='".$opn_ix."' ";		//해당 옵션에 포함된 상품도 사용안함으로 설정
				$mdb->query($sql);

                $sql = "update shop_product_options_detail_global set insert_yn='N' where opn_ix='".$opn_ix."' ";		//해당 옵션에 포함된 상품도 사용안함으로 설정
                $mdb->query($sql);

				$option_stock_yn = "";
				//for($i=0;$i < count($option_info["details"]);$i++){
				$i=0;
				foreach($option_info["details"] as $dkey => $option_detail){

                        if (empty($option_detail[english_option_size])) {
                            $option_detail[english_option_size] = $option_detail[option_size];
                        }

                        if( $codiOptionType == 'd' ){//옵션1+옵션2
                            $option_div = $option_detail[option_color] . '+' . $option_detail[option_size];
                            $english_option_div = $option_detail[english_option_color] . '+' . $option_detail[english_option_size];
                            $option_detail[option_div] = $option_div;
                        }else{
                            $option_div = $option_detail[option_div];
                            $english_option_div = $option_detail[english_option_div];
                        }

					//foreach($_option_detail as $opsd_key=>$opsd_value) {
						if($option_detail[option_div]){

							$mdb->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE option_div = '".trim($option_detail[option_div])."' and opn_ix = '".$opn_ix."' and set_group = '".$x."'");

                            if($_option_detail['english_coprice'] == '') {
                                $_option_detail['english_coprice'] = getExchangeNationPrice($_option_detail['coprice']);
                            }
                            if($option_detail['english_listprice'] == '') {
                                $option_detail['english_listprice'] = getExchangeNationPrice($option_detail['listprice']);
                            }
                            if($option_detail['english_sellprice'] == '') {
                                $option_detail['english_sellprice'] = getExchangeNationPrice($option_detail['sellprice']);
                            }

							if($mdb->total){
								$mdb->fetch();
								$opd_ix = $mdb->dt[id];

								$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
											set_group='".$x."',
											set_group_seq = '".$i."',
											option_div='".$option_div."',
											option_code='".$option_detail[code]."',
											option_gid='".$option_detail[gid]."',
											option_coprice='".$option_detail[coprice]."',
											option_listprice='".$option_detail[listprice]."',
											option_price='".$option_detail[sellprice]."',
											option_premiumprice='".$option_detail[premiumprice]."',
											option_wholesale_listprice='".$option_detail[wholesale_listprice]."',
											option_wholesale_price='".$option_detail[wholesale_price]."',
											option_stock='".$option_detail[stock]."',
											option_safestock='".$option_detail[safestock]."' ,
											option_soldout='".$option_detail[soldout]."' ,
											option_barcode='".$option_detail[barcode]."' ,
											option_etc1='".$option_detail[set_cnt]."' ,
											option_color = '".$option_detail[option_color]."',
										    option_size = '".$option_detail[option_size]."',
											insert_yn='Y'
											where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";

                                $mdb->query($sql);

                                $sql = "update shop_product_options_detail_global set
											set_group='".$x."',
											set_group_seq = '".$i."',
											option_div='".$english_option_div."',
											option_code='".$option_detail[code]."',
											option_gid='".$option_detail[gid]."',
											option_coprice='".$option_detail[english_coprice]."',
											option_listprice='".$option_detail[english_listprice]."',
											option_price='".$option_detail[english_sellprice]."',
											option_premiumprice='".$option_detail[premiumprice]."',
											option_wholesale_listprice='".$option_detail[wholesale_listprice]."',
											option_wholesale_price='".$option_detail[wholesale_price]."',
											option_stock='".$option_detail[stock]."',
											option_safestock='".$option_detail[safestock]."' ,
											option_soldout='".$option_detail[soldout]."' ,
											option_barcode='".$option_detail[barcode]."' ,
											option_etc1='".$option_detail[set_cnt]."' ,
											option_color = '".$option_detail[english_option_color]."',
										    option_size = '".$option_detail[english_option_size]."',
											insert_yn='Y'
											where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";

                                sellingCntUpdate($option_detail[gid]);
							}else{
								$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." 
											(id, pid, opn_ix, set_group, set_group_seq, option_div, option_code,option_coprice,option_listprice, option_price, option_premiumprice, option_wholesale_listprice,  option_wholesale_price, option_stock, option_safestock, option_soldout, option_barcode,option_etc1, insert_yn, regdate, option_gid, option_color, option_size) 
											values
											('','$pid','$opn_ix','".$x."','".$i."','".$option_div."','".$option_detail[code]."','".$option_detail[coprice]."','".$option_detail[listprice]."','".$option_detail[sellprice]."','".$option_detail[premiumprice]."','".$option_detail[wholesale_listprice]."','".$option_detail[wholesale_price]."','".$option_detail[stock]."','".$option_detail[safestock]."','".$option_detail[soldout]."', '".$option_detail[barcode]."' ,'".$option_detail[set_cnt]."' ,'Y', NOW(),'".$option_detail[gid]."','".$option_detail[option_color]."','".$option_detail[option_size]."') ";
                                $mdb->query($sql);

                                $_id = $mdb->insert_id();

                                $sql = "INSERT INTO shop_product_options_detail_global
											(id, pid, opn_ix, set_group, set_group_seq, option_div, option_code,option_coprice,option_listprice, option_price, option_premiumprice, option_wholesale_listprice,  option_wholesale_price, option_stock, option_safestock, option_soldout, option_barcode,option_etc1, insert_yn, regdate, option_gid, option_color, option_size) 
											values
											('$_id','$pid','$opn_ix','".$x."','".$i."','".$english_option_div."','".$option_detail[code]."','".$option_detail[english_coprice]."','".$option_detail[english_listprice]."','".$option_detail[english_sellprice]."','".$option_detail[premiumprice]."','".$option_detail[wholesale_listprice]."','".$option_detail[wholesale_price]."','".$option_detail[stock]."','".$option_detail[safestock]."','".$option_detail[soldout]."', '".$option_detail[barcode]."' ,'".$option_detail[set_cnt]."' ,'Y', NOW(),'".$option_detail[gid]."','".$option_detail[english_option_color]."','".$option_detail[english_option_size]."') ";
							}

							$mdb->sequences = "SHOP_GOODS_OPTIONS_DT_SEQ";
							//echo $sql."<br><br>";
							$mdb->query($sql);

							if($option_detail[stock] == 0 && ($option_stock_yn == "" || $option_stock_yn == "R")){
								$option_stock_yn = "N";
							}

							if($option_detail[stock] < $option_detail[safestock] && $option_stock_yn == ""){
								$option_stock_yn = "R";
							}
						//}
					}
					$i++;
				}
			
				$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
				//echo $sql;
				$mdb->query($sql);		//옵션에 상품을 넣거나 수정후 나머지 N 설정값들을 삭제
                $sql = "delete from shop_product_options_detail_global where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
                //echo $sql;
                $mdb->query($sql);

                //if($option_info["option_kind"] == 'c') {
                    $sql = "select sum(stock) as option_stock ,'0' as option_safestock
                                    from inventory_product_stockinfo where gid in (
                                    SELECT od.option_gid 
                                    FROM  " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od
                                    WHERE od.pid='" . $pid . "' 
                                    and od.option_soldout != '1'                                   
                                    group by od.option_gid
								)
								";
//                }else {
//                    $sql = "SELECT max(option_stock) as option_stock,max(option_safestock) as option_safestock
//							FROM " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . "
//							WHERE opn_ix='" . $opn_ix . "'";
//                }
				$mdb->query($sql);
				$mdb->fetch();
				$option_stock = $mdb->dt[option_stock];
				$option_safestock = $mdb->dt[option_safestock];
				if($sell_ing_cnt == ""){
					$sell_ing_cnt = 0;
				}
				$mdb->query("update ".TBL_SHOP_PRODUCT." set stock = '".$option_stock."' ,safestock = '".$option_safestock."' where id ='".$pid."'");
				if($option_stock_yn){
					$mdb->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = '$option_stock_yn' where id ='".$pid."' ");
				}
				$x++;
			}
		}
        $mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_kind in ('c') and insert_yn = 'N' ");
        if($mdb->total){
            $list = $mdb->fetchall("object");
            foreach($list  as $li){
                $opn_ix_list[] = $li['opn_ix'];
            }
            $sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix in ('".implode("','",$opn_ix_list)."') and insert_yn = 'N' ";
            $mdb->query($sql);

            $sql = "delete from shop_product_options_detail_global where opn_ix in ('".implode("','",$opn_ix_list)."') and insert_yn = 'N' ";
            $mdb->query($sql);
        }
        $sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind in ('c') and insert_yn = 'N' ";
        $mdb->query($sql);

        $sql = "delete from shop_product_options_global where pid = '$pid' and option_kind in ('c') and insert_yn = 'N' ";
        $mdb->query($sql);
	}else{
		$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_kind in ('c') ");

		if($mdb->total){
			$mdb->fetch();
			$opn_ix = $mdb->dt[opn_ix];
			$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
			$mdb->query($sql);
            $sql = "delete from shop_product_options_detail_global where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
            $mdb->query($sql);
		}
		$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind in ('c') ";
		$mdb->query($sql);
        $sql = "delete from shop_product_options_global where pid = '$pid' and option_kind in ('c') ";
        $mdb->query($sql);

		//$mdb->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = 'N' where id ='$pid'");
	}

}
?>