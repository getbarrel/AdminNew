<?php
	include("../../class/layout.class");
	include_once("demandship.config.php");

	$od_ix = array_filter(array_map('trim',explode(',', $_GET['od_ix'])));


	$db = new Database;
	$db2 = new Database;
	$db5 = new Database;

	if($_GET['act'] == 'make_label'){
		$od_ix_cnt = count($od_ix);

		$sql = "select demandship_service_key, company_id
				  from common_seller_delivery
				 where company_id = '". $_SESSION['admininfo']['company_id'] ."' limit 1";
		$db->query($sql);
		$sellkey = $db->fetch();

		if(!empty($sellkey['demandship_service_key']) && count($sellkey['company_id']) > 0){

			$items = itemsRtn($od_ix);

			if(count($items) > 0){
				$postData = "{
						\"shipments\":[";
						for($i=0; $i<count($items); $i++){
							$postData .= "\"".$items[$i]['ds_id']."\"";
							if((count($items)-1) != $i) $postData .= ",";
						}
				$postData .= "
						]
					}";
				$data = (!empty($postData)) ? "--data '".$postData."'" : '';

				$header = array();
				$header[] = "Content-Type: application/json";
				$header[] = "Authorization: Bearer ".$sellkey['demandship_service_key'];
				$header[] = "Accept: application/json";
				$header = implode("' -H '", $header);

				$actionUrl = DEMANDSHIP_URL . "/api/v1/shipments/print";

				$command = "curl -H '".$header."' ".$data." ".$actionUrl."";
				$response = shell_exec($command);
				//echo $response;
				//echo $command;
				//exit;
				$res = json_decode($response, true);
				//print_r($res);
				//exit;

				if(@array_key_exists('shipments', $res)){
						foreach($res['shipments'] as $item){
							$sql = "update shop_delivery_overseas set
										base64_label_pdf = '".$item["base64_label_pdf"]."', 
										tracking_number = '".$item["tracking_number"]."', 
										editdate = '".date("Y-m-d H:i:s")."'
									where
										ds_id = '".$item["ds_id"]."'
							";
							$db->query($sql);

							$sql = "select tracking_number, order_from from shop_delivery_overseas where ds_id = '".$item["ds_id"]."'";
							$db5->query($sql);
							$tracking_number = $db5->fetch();

							switch($tracking_number['order_from']){
							case "storefarm":
								$quick_code = '01';
							case "auction":
								$quick_code = '01';
							case "11my":
								$quick_code = '01';
							break;
							default:
								$quick_code = '601';
							}

							$sql = "update shop_order_detail set quick='".$quick_code."', invoice_no='".$tracking_number['tracking_number']."' where invoice_no = '".$item["ds_id"]."'";
							$db->query($sql);
							//echo $sql;
							/*
							echo $item["ds_id"];
							echo ' : ';
							echo $item["tracking_number"];
							echo ' : ';
							echo $item["shipping_cost"];
							echo ' : ';
							echo $item["base64_label_pdf"];
							*/
						}
				}else if(@array_key_exists('error', $res)){
					$sql = "update shop_delivery_overseas set
								code = '".$res['status_code']."', 
								etc = '".$res['message']."', 
								editdate = '".date("Y-m-d H:i:s")."'
							where
								ds_id = '".$item["ds_id"]."'
					";
					$db->query($sql);
				}
				//print_r($response);

				$validationResult = validation($items);

				if($validationResult != true){
					echo($validationResult);
					exit;
				}

				$pdfPath = $_SERVER["DOCUMENT_ROOT"]."/admin/openapi/demandship/labels/";

				$items = itemsRtn($od_ix);

				foreach($items as $item){
					base64DataToPdf($item['base64_label_pdf'], $pdfPath.$item["ds_id"].".pdf");
					$dsIdList[] = $pdfPath . "/" . $item["ds_id"] . ".pdf";
				}


				$outputFileName = sprintf("%d.pdf", time());
				$outputFullPath = sprintf("%s%s", $pdfPath, $outputFileName);

				$cmd = sprintf("gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=%s %s", $outputFullPath, implode(" ", $dsIdList) );
				shell_exec($cmd);
				$result = json_encode(array("fileName" => $outputFileName));
				echo($result);

			}
		}
	}


	function validation($data) {
		if(count($data) == 0){
			$res_data[] = array('status' => 'msg');
			$res_data[] = array('data' => '디멘드쉽으로 정보를 전송한 후 라벨을 생성해주세요.');
			return json_encode($res_data);
		}
		return true;
	}

	function base64DataToPdf($base64Data, $path) {
		//$_SERVER["DOCUMENT_ROOT"]."/admin/openapi/demandship/labels/".$item["ds_id"].".pdf";
		$pdfData = base64_decode($base64Data);

		$pdfFile = fopen($path, "w");
		fwrite($pdfFile, $pdfData . "\r\n");
		fclose($pdfFile);
	}

	function itemsRtn($od_ix){
		$db3 = new Database;
		$sql = sprintf("select ds_id, base64_label_pdf, tracking_number
						  from shop_delivery_overseas
						 where od_ix in (%s) order by sd_ix desc", implode(",", $od_ix) );
		$db3->query($sql);
		return $db3->fetchall();
	}