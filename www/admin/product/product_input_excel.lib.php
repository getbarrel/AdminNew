<?

function MakeUploadExcelData($input_type = 'input'){

	//대량수정시 제외되는 체크박스 부분 2014-08-19 이학봉
	$check_out_array = array('id','opn_ix','option_div','stock_options_coprice','stock_options_wholesale_listprice','stock_options_wholesale_price','stock_options_listprice','stock_options_sellprice','stock_options_stock','stock_option_soldout','stock_options_safestock','stock_options_code','stock_options_barcode','basic_opn_ix','basic_option_use','basic_option_kind','basic_option_soldout','basic_option_div','basic_option_price','basic_opd_ix','mandatory_type','pmi_ix','mandatory_type_global','pmi_ix_global');

	include("../logstory/class/sharedmemory.class");
	//auth(8);
	$shmop = new Shared("upload_excel_data_".$_SESSION["admininfo"]["charger_ix"]);
	//	$shmop->clear();
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$upload_excel_data = $shmop->getObjectForKey("upload_excel_data_".$_SESSION["admininfo"]["charger_ix"]);
	//echo "<pre>";
	//print_r($upload_excel_data);exit;

	if($upload_excel_data[session_id()]){
		$mstring = "<table cellpadding=3 cellspacing=0 class='list_table_box' >\n";
		$i = 0;
		$z = 0;
		foreach($upload_excel_data[session_id()] as $key => $value){

			$mstring .= "<tr align=center height=25 depth='".$key."'>\n";
			$mstring .= "\t<td ".($i == 0 ? "class=m_td style='padding:0px 50px;'   nowrap":" class='point ' nowrap")."> ".($i == 0 && $input_type == 'update'? "<input type='checkbox' name='check_all' id='check_all' value='1'>&nbsp;<label for='check_all'>처리현황</label>":"<span id='status_message_".$value["p_no"]."'>".$value["status_message"]."")."</span></td>\n";
			foreach($value as $_key => $_value){
				if($_key != "status" && $_key != "status_message" && $_key != "p_no"){
					$mstring .= "\t<td ".($i == 0 ? "class=m_td nowrap  style='max-width:300px;'":" nowrap  style='max-width:300px;overflow-x: scroll; '").">
											".($i==0 && $input_type == 'update' && !in_array($_key,$check_out_array)?"<input type='checkbox' name='update_check_".$_key."' id='update_check_".$_key."' value='1'>&nbsp;":"")."
											<label for='update_check_".$_key."'>".@htmlspecialchars($_value)."</label>";
					if($_key == "product_type" && $i != 0){
						$mstring .= "<input type=hidden class='upload_excel_infos' id='p_no' name='upload_excel_infos[".$value["p_no"]."][p_no]' value='".$value["p_no"]."' >";
						$z++;
					}

					$mstring .= "
											</td>\n";
				}
			}
			$mstring .= "</tr>\n";

			$i++;
		}
		$mstring .= "</table>\n";
	}
	return $mstring;
}



function MakeUploadExcelData2($input_type = 'input'){
	
	//대량수정시 제외되는 체크박스 부분 2014-08-19 이학봉
	$check_out_array = array('id','opn_ix','option_div','stock_options_coprice','stock_options_wholesale_listprice','stock_options_wholesale_price','stock_options_listprice','stock_options_sellprice','stock_options_stock','stock_option_soldout','stock_options_safestock','stock_options_code','stock_options_barcode','basic_opn_ix','basic_option_use','basic_option_kind','basic_option_soldout','basic_option_div','basic_option_price','basic_opd_ix','mandatory_type','pmi_ix','mandatory_type_global','pmi_ix_global');

	include("../logstory/class/sharedmemory.class");
	//auth(8);
	$shmop = new Shared("upload_excel_data_".$_SESSION["admininfo"]["charger_ix"]);
	//	$shmop->clear();
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$upload_excel_data = $shmop->getObjectForKey("upload_excel_data_".$_SESSION["admininfo"]["charger_ix"]);
	//echo "<pre>";
	//print_r($upload_excel_data);exit;

	if($upload_excel_data[session_id()]){
		//$check_out_array = $upload_excel_data[session_id()][0];

		$mstring = "<table cellpadding=3 cellspacing=0 class='list_table_box' >\n";
		$i = 0;
		$z = 0;
		//echo "<pre>";
		//print_r($upload_excel_data[session_id()]);
		//exit;
		foreach($upload_excel_data[session_id()] as $key => $value){

			$mstring .= "<tr align=center height=25 depth='".$key."'>\n";
			$mstring .= "\t<td ".($i == 0 ? "class=m_td style='padding:0px 50px;'   nowrap":" class='point ' nowrap")."> 
							".($i == 0 && $input_type == 'update'? "<input type='checkbox' name='check_all' id='check_all' value='1'>&nbsp;<label for='check_all'>처리현황</label>":"<span id='status_message_".$value["p_no"]."'>".$value["status_message"]."")."</span>
							</td>\n";
			foreach($value as $_key => $_value){
				if($_key != "status" && $_key != "status_message" && $_key != "p_no"){
					if($i == 0 && false){
						$mstring .= "\t<td class=m_td nowrap>
												<input type='checkbox' name='update_check_".$_key."' id='update_check_".$_key."' value='1'>&nbsp; 
												<label for='update_check_".$_key."'>".@htmlspecialchars($_value)."</label>";

						if($input_type == 'input'){
							if($_key == "product_type" && $i != 0){
								$mstring .= "<input type=hidden class='upload_excel_infos' id='p_no' name='upload_excel_infos[".$value["p_no"]."][p_no]' value='".$value["p_no"]."' >";
								$z++;
							}
						}else{
							if($_key == "product_type" && $i != 0){
								$mstring .= "<input type=hidden class='upload_excel_infos' id='p_no' name='upload_excel_infos[".$value["p_no"]."][p_no]' value='".$value["p_no"]."' >";
								$z++;
							}
						}

						$mstring .= "		</td>\n";
					}else{
						$mstring .= "\t<td ".($i == 0 ? "class=m_td nowrap style='max-width:300px;' ":" style='max-width:300px;overflow-x: scroll;' nowrap")." >
												".($i==0 && $input_type == 'update' && !in_array($_key,$check_out_array)?"<input type='checkbox' name='update_check_".$_key."' id='update_check_".$_key."' value='1'>&nbsp;":"")."
												<label for='update_check_".$_key."'>".@htmlspecialchars($_value)."</label>";
						if($input_type == 'input'){
							if($_key == "product_type" && $i != 0){
								$mstring .= "<input type=hidden class='upload_excel_infos' id='p_no' name='upload_excel_infos[".$value["p_no"]."][p_no]' value='".$value["p_no"]."' >";
								$z++;
							}
						}else{
							if($_key == "id" && $i != 0){
								$mstring .= "<input type=hidden class='upload_excel_infos' id='p_no' name='upload_excel_infos[".$value["p_no"]."][p_no]' value='".$value["p_no"]."' >";
								$z++;
							}
						}

						$mstring .= "		</td>\n";
					}
				}
			}
			$mstring .= "</tr>\n";

			$i++;
		}
		$mstring .= "</table>\n";
	}
	return $mstring;
}

?>