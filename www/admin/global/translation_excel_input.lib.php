<?



function MakeUploadExcelData(){

	include("../logstory/class/sharedmemory.class");
	//auth(8);
	$shmop = new Shared("translation_upload_excel_data");
	//	$shmop->clear();
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$translation_upload_excel_data = $shmop->getObjectForKey("translation_upload_excel_data");
	//print_r($translation_upload_excel_data);
	if($translation_upload_excel_data[session_id()]){
		$mstring = "<table cellpadding=3 cellspacing=0 class='list_table_box' >\n";
		$i = 0;
		$z = 0;

		//print_r($translation_upload_excel_data[session_id()]);
		foreach($translation_upload_excel_data[session_id()] as $key => $value){

			$mstring .= "<tr align=center height=25>\n";
			$mstring .= "\t<td ".($i == 0 ? "class=m_td style='padding:0px 50px;'   nowrap":" class='point ' nowrap")."> ".($i == 0 ? "처리현황":"<span id='status_message_".$value["trans_key"]."'>".$value["status_message"]."")."</span></td>\n";

			foreach($value as $_key => $_value){
				if($_key != "status" && $_key != "status_message" && $_key != "trans_key"){
					$mstring .= "\t<td ".($i == 0 ? "class=m_td nowrap":" ").">
											".str_replace(array("\\t","\\n"),"",trim(@htmlspecialchars($_value)))."";
					if($_key == "language" && $i != 0){
						$mstring .= "<input type=hidden class='upload_excel_infos' id='trans_key' name='upload_excel_infos[".$value["trans_key"]."][trans_key]' value='".$value["trans_key"]."' >";
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

?>