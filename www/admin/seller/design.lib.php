<?

$admin_config[mall_page_type] = "MI";

if(empty($minisohp_templete)){
	$minisohp_templete = $admin_config[minishop_templete];
}

if($minisohp_templete != ""){
	if($minisohp_templete){
		$admin_config[minisohp_templete] = $minisohp_templete;
	}

	session_register("admin_config");
}

//print_r($admin_config);

function SelectDesignSkin(){
	global $admin_config, $mod, $page_name, $SubID, $pcode, $depth, $mmode;
	$mstring = "
	<table style='width:100%'>
		<tr>
		<td style='padding:0px 5px 10px 0px'>
			<table class='".($admin_config[mall_page_type] == "P" ? "box_18":"box_15")."' cellpadding=0 style='width:100%;cursor:pointer' onclick=\"".($admin_config[minisohp_templete] == "" ? "alert('선택된 쇼핑몰 스킨이 없습니다. 스킨 선택 후 디자인 수정을 하실수 있습니다.');":"")."\"><!---->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05' style='height:30px;padding:0px;width:50%'>	
					<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
						<tr bgcolor=#ffffff height=30 >";
						if($admin_config[mall_page_type] == "M"){
							$mstring .= "
								<td style='text-align:center;".($admin_config[mall_page_type] == "M" ? "color:gray;font-size:18px;":"font-size:20px;")."' onclick=\"".($admin_config[minisohp_templete] == "" ? "alert('선택된 스킨이 없습니다. 스킨 선택 후 디자인 수정을 하실수 있습니다.');":"document.location='?mall_page_type=P&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth';")."\"><b>선택된 미니샵 스킨 : ".$admin_config[minisohp_templete]."</b></td>
							";
						}else{
							$mstring .= "
								<td style='text-align:center;".($admin_config[mall_page_type] == "M" ? "color:gray;font-size:18px;":"font-size:20px;")."'>
									<table width='100%' cellpadding='3' cellspacing='0' border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
										<colgroup>
											<col width='130px'>
											<col width='*'>
										</colgroup>
										<tbody>
											<tr bgcolor='#ffffff' height='50'>
												<td class='input_box_title' style='text-align:center;color:gray;font-size:13px;padding:0;'>
													<b>미니샵 스킨</b>
												</td>
												<td class='input_box_item point' style='text-align:center;' nowrap=''>
													<b>".SelectDirList("minisohp_templete", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/minishop_templet", $admin_config[minisohp_templete])."</b>
													<a href=\"javascript:document.location='?mall_page_type=MI&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth&mmode=".$mmode."&minisohp_templete='+$('#minisohp_templete').val() \" style='font-size:12px;padding:0 0 0 10px'>작업스킨선택</a>
												</td>							
											</tr>
										</tbody>
									</table>
								</td>
							";
						}
						$mstring .= "							
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
			</table>
		</td>
		
		</tr>
		</table>
	";

	return $mstring;

}


?>