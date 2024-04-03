<?php
	include("../../class/layout.class");
	include_once("demandship.config.php");

	$od_ix = array_filter(array_map('trim',explode(',', $_GET['od_ix'])));


	$db = new Database;
	$db2 = new Database;

	if($_GET['act'] == 'make_label'){
		$od_ix_cnt = count($od_ix);

		$sql = "select demandship_service_key, company_id
				  from common_seller_delivery
				 where company_id = '". $_SESSION['admininfo']['company_id'] ."' limit 1";

		$db->query($sql);
		$sellkey = $db->fetch();

		if(!empty($sellkey['demandship_service_key']) && count($sellkey['company_id']) > 0){

			$sql = sprintf("select ds_id, base64_label_pdf, tracking_number
							  from shop_delivery_overseas
							 where od_ix in (%s)", implode(",", $od_ix) );

			$db->query($sql);

			$ds_id = $db->fetchall();

			if(count($ds_id) == 0){
				$res_data[] = array('status' => 'msg');
				$res_data[] = array('data' => '디멘드쉽으로 정보를 전송한 후 라벨을 생성해주세요.');
				echo json_encode($res_data);
				exit;
			}



		}
	}















































			$postData = "{";
			$postData .= "\"shipments\":[";
			$shipment_cnt = 0;

			foreach($ds_id as $key => $val){
				if($i != 0)
					$postData .= ',';

				//if(empty($val[1])){
					$shipment_cnt++;
					$postData .= "\"" . $val[0] . "\"";
				//}
				$i++;
			}
			$postData .= "]";
			$postData .= "}";

			/*
			if($shipment_cnt == 0){
				echo '이미 라벨이 생성되어 있습니다.';
				exit;
			}
			*/

			//echo $postData;
			//exit;
			$data = (!empty($postData)) ? "--data '".$postData."'" : '';

			$header = array();
			$header[] = "Content-Type: application/json";
			$header[] = "Authorization: Bearer ".$sellkey['demandship_service_key'];
			$header[] = "Accept: application/json";
			$header = implode("' -H '", $header);

			$actionUrl = DEMANDSHIP_URL . "/api/v1/shipments/print";

			$command = "curl -H '".$header."' ".$data." ".$actionUrl."";
			$response = shell_exec($command);
			//echo $command;
			//echo($response);

			$res = json_decode($response, true);
			//print_r($res);
			//echo $res['base64_label_pdf'];
			//exit;

			$dsidArray = array();

			if(@array_key_exists('shipments', $res)){
					if(count($res['shipments']) > 0){
						foreach($res['shipments'] as $item){
							$sql = "update shop_delivery_overseas set
										base64_label_pdf = '".$item["base64_label_pdf"]."', 
										tracking_number = '".$item["tracking_number"]."', 
										editdate = '".date("Y-m-d H:i:s")."'
									where
										ds_id = '".$item["ds_id"]."'
							";
							$db->query($sql);

							$base64_label = $item["base64_label_pdf"];
							$ds_id_val = $item["ds_id"];

							$res_data[] = array('status' => 'pdf');
							$res_data[] = array('data' => $item["base64_label_pdf"]);
							//echo json_encode($res_data);


							//echo strlen($base64_label);
							$pdf_content = base64_decode($base64_label);

							array_push($dsidArray, $item["ds_id"] . '.pdf');

							$pdf_path = $_SERVER["DOCUMENT_ROOT"]."/admin/openapi/demandship/labels/".$item["ds_id"].".pdf";
							if(file_exists($pdf_path))
								unlink($pdf_path);

							$pdf_file = fopen($pdf_path, "a");
							fwrite($pdf_file, $pdf_content . "\r\n");
							fclose($pdf_file);

							//echo $sql;
							//echo $od_ix_cnt . "건 중 " . $shipment_cnt . "건의 라벨이 생성 되었습니다.";
							//echo $item["base64_label_pdf"];
						}
					}

					//$dsidArray = array("name1.pdf", "name2.pdf" ,"name3.pdf", "name4.pdf");

					//$datadir = "labels/";
					$outputName = $datadir . "merged.pdf";

					$cmd = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=$outputName ";

					foreach($dsidArray as $file) {
						$cmd .= $file . " ";
					}
					echo $cmd;

			}else if(@array_key_exists('error', $res)){
				echo $res['error']['status_code'] . " : " . $res['error']['message'];
			}
		}
	}



function base64ToPdf( $inputfile, $outputfile ) { 
  /* read data (binary) */ 
  $ifp = fopen( $inputfile, "rb" ); 
  $imageData = fread( $ifp, filesize( $inputfile ) ); 
  fclose( $ifp ); 
  /* encode & write data (binary) */ 
  $ifp = fopen( $outputfile, "wb" ); 
  fwrite( $ifp, base64_decode( $imageData ) ); 
  fclose( $ifp ); 
  /* return output filename */ 
  return( $outputfile ); 
} 