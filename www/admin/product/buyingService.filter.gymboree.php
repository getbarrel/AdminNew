<?

$datas = "";
$soldout_message = "";

//exit;
//echo $bs_url;
$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
//print $snoopy->results;
$datas = split("\n",$snoopy->results);


//print_r($datas);
// echo "<br><b style='color:blue;'>bs_url : ".$bs_url."</b><br>";
//preg_match_all("|<img .*src=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
$body = "";
$xml_path = "";
for($i=0;$i < count($datas);$i++){
	if(!$body && substr_count($datas[$i],"<body")){
		$body = $datas[$i];
		//echo $body;
		preg_match_all("|<body.* '(.*)'.*>|U",$body,$xml_path, PREG_PATTERN_ORDER);
	}
}
//print_r($xml_path);
//exit;
$url_info = "";
$url_info = parse_url($bs_url);
//print_r($url_info);
//echo $url_info[query];
$url_query_info = "";
parse_str($url_info[query],$url_query_info);
//print_r($url_query_info);
//echo $url_query_info["PRODUCT<>prd_id"];
$xml_url = "";
$xml_url = $xml_path[1][0];

if($xml_url){
		$snoopy->fetch($xml_path[1][0]);
		//echo $xml_url."<br>";
		//exit;
		//function object2array($object) { return @json_decode(@json_encode($object),1); } 

		//$layoutXmlPath = $xml_path[1][0];//$_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/templet/" . $admin_config["selected_templete"] . "/layout.xml";
		//echo $snoopy->results;
		//exit;
		$xml_string = "";
		$xml_object = "";
		$xml_string = str_replace("\n","",$snoopy->results);
		$xml_object = new SimpleXMLElement($xml_string, LIBXML_NOCDATA | LIBXML_NOBLANKS);
		//$xml_array=object2array($xml_object); 
		//print_r($xml_object);
		//exit;
		//$layoutXml = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
		//print_r($layoutXml );
		$pname = "";
		$pname = $xml_object->silhouette[0]->head[0]->attributes()->name;//$xml_array["silhouette"]["head"]["@attributes"][name];//$xml_object->silhouette[0]->head[0]->attributes()->name;
		//$pname = $xml_object->silhouette[0]->head[0]->attributes["name"];
		$code = "";
		$code = $xml_object->silhouette[0]->head[0]->attributes()->code;//$xml_array["silhouette"]["head"]["@attributes"][code];;//
		$shotinfo = "";
		$shotinfo =  $xml_object->silhouette[0]->left[0]->options[0];//$xml_array["silhouette"]["left"]["options"];// //->item(0)->nodeValue
		$prod_desc = "";
		$prod_desc =  $xml_object->silhouette[0]->left[0]->description[0];//$xml_array["silhouette"]["left"]["description"];
		//$products = $xml_object->silhouette[0]->fit[0]->product;//$xml_array["silhouette"]["fit"]["product"];
		//echo $prod_desc;
		//exit;
		//print_r($xml_object->silhouette[0]->head[0]);
		//print_r($products);
		//exit;
		$products = "";
		unset($products);
		$products = $xml_object->xpath("//product[*]");
		//echo count($xml_array["silhouette"]["fit"]["product"]);
		//exit;
		//echo count($products);
		//exit;
		$options = "";
		if(count($products) > 0){
		//echo "옵션정보";
		$i=0;
		 $options[0][option_name] = "COLOR";
		 $options[0][option_kind] = "s";
		 $options[0][option_use] = "1";
		 $options[0][option_type] = "9";
		
		$goods_info = "";
		$goods_detail_images = "";
	    $goods_detail_images_name = "";
		$pcode = "";
		$prod_img_src = "";
		$prod_zoom = "";
		$price = "";
		$sale_price = "";
		$goods_options_info = "";
		unset($goods_detail_images);
		unset($goods_info);

		  foreach ($products as $product){
			  $goods_info = $product->attributes();
			    $goods_detail_images[$i] = $goods_info["image"];
				$goods_detail_images_name[$i] = $goods_info["alt"];
			  if($url_query_info["PRODUCT<>prd_id"] == $goods_info["id"]){
				  //echo $url_query_info["PRODUCT<>prd_id"] ."==". $goods_info["id"]."<br>";
					  $pcode = $goods_info["id"];
					
					
					  $options[0][details][$i]["option_div"] = $goods_info["alt"];
					
					  if(strval($code) == strval($goods_info[code])){
						// echo strval($pcode) ."==".strval ($goods_info[code])." = ".((strval ($pcode) == strval ($goods_info[code]) ? "참":"거짓"))."<br>";
						$prod_img_src = $goods_info["image"];//$product->attributes()->image;
						$prod_zoom = $goods_info["view-larger-url"];
						$price = str_replace("$","",strip_tags($goods_info["db-sale-price"]));
						$sale_price = str_replace("$","",strip_tags($goods_info["db-sale-price"]));
						//echo "priceL:".$price;
						if($sale_price != ""){
							$price = $sale_price;
						}
						//echo "prod_img_src:".$prod_img_src;
						//echo "count : ".count($product->sku);
						//exit;
						if(count($product->sku) > 0){
							
							$options[1][option_name] = "SIZE";
							$options[1][option_kind] = "s";
							$options[1][option_use] = "1";
							$options[1][option_type] = "9";

							//$option[0] = "SIZE";
							//print_r($product->sku);
							$stock_bool = false;
							$soldout_message = "상품 전체 재고 없음";
							for($j=0, $x=0;$j < count($product->sku);$j++){
								$goods_options_info = $product->sku[$j]->attributes();
								//print_r($goods_options_info);
								//$option[$j+1] = $products[$i]["sku"][$j]->attributes()->title;
								if(strval($goods_options_info["in-stock"]) == "true"){
									$stock_bool = true;
									$soldout_message = "";
									$options[1][details][$x]["option_div"]= strval($goods_options_info["title"]);
									//echo $goods_options_info["db-sale-price"];
									//exit;
									if(strval($goods_options_info["db-sale-price"])){
										$options[1][details][$x]["price"] = "";//str_replace("$","",strip_tags(strval($goods_options_info["db-sale-price"])));
									}else{
										$options[1][details][$x]["price"] = "";//str_replace("$","",strip_tags(strval($goods_options_info["reg-price"])));
									}
									$options[1][details][$x]["option_code"] = strval($goods_options_info["id"]);	
									//$options[1]["option_stock"][$x] = strval($goods_options_info["in-stock"]);	
									$x++;
									//
									//}else{
									//	$options[1]["option_stock"][$x] = "0";					
								}
								//$_option[$x]["option_div"] = $product->sku[$j]->attributes()->["title"];
								//$_option[$j]["option_price"] = $product->sku[$j]->attributes()->["reg-price"];
							}
						}else{
							
							$soldout_message = "사이즈 정보 없음";
						}
					  }
			  }
				//$prod_img_src = $products[$i]->attributes()->image;
			//	print_r($products[$i]);
			$i++;
		  }
		}  

		//print_r($options[1]);
		//http://s7ondemand1.scene7.com/is/image/Gymboree/140091405?$PRODMAIN$
		//print_r($options[1]);option2
		//print_r( $goods_detail_images);
		//exit;
		$prod_desc_prod = "";
		unset($prod_desc_prod);
		if(count($goods_detail_images) > 0){
			$_prod_desc = "<table align=center>\n";	
			$_prod_desc .= "<tr><td align=center><div style='text-align:left;'>".$prod_desc."</div></td></tr>\n";
			$_prod_desc .= "<tr><td align=center style='padding-bottom:20px;'><div style='text-align:left;'>".str_replace("#","<li>","<li>".$shotinfo)."</div></td></tr>\n";
			$_prod_desc .= "<tr><td align=center style='padding:5px 0px 30px 0px;'><iframe frameborder=0 width=620 height=780 src=\"".$prod_zoom ."\"></iframe><tr></tr>\n";
			for($i=0;$i < count($goods_detail_images);$i++){
				$_prod_desc .= "<tr><td align=center style='padding:0px 0px 0px 0px;'><b>".$goods_detail_images_name[$i]."</b></td></tr>\n";
				$_prod_desc .= "<tr><td align=center style='padding:5px 0px 30px 0px;'><IMG src=\"".$goods_detail_images[$i]."\"></td></tr>\n";
				
				
			}
			$_prod_desc .= "</table>\n\n";

			$prod_desc_prod = $_prod_desc;
		}
}
$shotinfo = "";

if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
	//echo $soldout_message;
}else{
	$stock_bool = true;
}

$pcode = $bs_site."_".$pcode;

//echo $price."<br>";
//echo $pcode."<br>";
//echo $shotinfo."<br>";
//echo $prod_desc."<br>";
//print_r($options[1]);
//exit;
   /*
   $page_name = $layout->contents;
   $contents_add = $layout->contents_add;
   $templet_name = "templet 미정의";
   $page_link = "page link 미정의";
   $page_desc = "page desc 미정의";
   $page_help = "page help 미정의";
   $page_type = "page type 미정의";
   $page_navi = "page navi 미정의";
   */


?>