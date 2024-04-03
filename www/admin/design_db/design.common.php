<?php
/*
디자인 새로 생성 시 shop_shopinfo 에 mall_type 필드 값이 P,M,MI 가 아닌 경우의 페이지 경로 값을 설정해 주는 부분이 없어서 에러 발생함 -> else 조건으로 세가지 타입에 해당되지 않는 경우
*/

	if(file_exists($_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/templet/".$selected_templete_text."/layout2.xml")) {//layout2.xml 파일이 있을 때만 kbk 130412
		define_syslog_variables();
		openlog("phplog", LOG_PID , LOG_LOCAL0);

		include_once('../../include/xmlWriter.php');
		include_once("../class/LayoutXml/LayoutXml.class");
		
		//print_r($admin_config);
		//print_r($admin_config);
		if($mall_page_type != "" || $selected_templete != ""){
			if($mall_page_type){
				$admin_config[mall_page_type] = $mall_page_type;
			}
			if($selected_templete){
				$admin_config[selected_templete] = $selected_templete;
		
				if($admin_config[mall_page_type] == "P"){
					$admin_config[selected_templete_general] = $admin_config[selected_templete];		
				}else if($admin_config[mall_page_type] == "M"){
					$admin_config[selected_templete_mobile] = $admin_config[selected_templete];		
				}else if($admin_config[mall_page_type] == "MI"){
					$admin_config[selected_templete_minishop] = $admin_config[selected_templete];
				}
			
			}
			
			session_register("admin_config");
			
		}else{
		
			if($admin_config[mall_page_type] == ""){
				$admin_config[mall_page_type] = "P";
			}
			//if($admin_config[selected_templete] == ""){
				if($admin_config[mall_page_type] == "P"){
					$admin_config[selected_templete] = $admin_config[selected_templete_general];		
				}else if($admin_config[mall_page_type] == "M"){
					$admin_config[selected_templete] = $admin_config[selected_templete_mobile];		
				}else if($admin_config[mall_page_type] == "MI"){
					$admin_config[selected_templete] = $admin_config[selected_templete_minishop];
				}
			//}
			if($admin_config[selected_templete_general] == ""){
				$admin_config[selected_templete_general] = $admin_config[mall_use_templete];
			}
			if($admin_config[selected_templete_mobile] == ""){
				$admin_config[selected_templete_mobile] = $admin_config[mall_use_mobile_templete];
			}
			if($admin_config[selected_templete_minishop] == ""){
				$admin_config[selected_templete_minishop] = $admin_config[minishop_templete];
			}
		
		}
		//echo $admin_config[selected_templete_general];
		//print_r($admin_config);
		

		
		function SelectDesignSkin(){
			global $admin_config, $admininfo, $mod, $page_name, $SubID, $pcode, $depth, $mmode;
			
		$mstring = "
		<table style='width:100%;margin-top:20px;' border=0 >";
		if($admininfo[mall_type] != "O"){
			$mstring .= "
				<col width='50%'>
				<col width='50%'>";
		}else{
			if($admin_config[mall_page_type] == "P"){
			$mstring .= "
				<col width='40%'>
				<col width='30%'>
				<col width='30%'>";
			}else if($admin_config[mall_page_type] == "M"){
			$mstring .= "
				<col width='30%'>
				<col width='40%'>
				<col width='30%'>";
			}else if($admin_config[mall_page_type] == "MI"){
			$mstring .= "
				<col width='30%'>
				<col width='30%'>
				<col width='40%'>";
			}
		}
		$mstring .= "
						<tr>
						<td style='padding:0px 5px 0px 0px'>
							<table class='".($admin_config[mall_page_type] == "P" ? "box_18":"box_15")."' cellpadding=0 style='width:100%;cursor:pointer' onclick=\"".($admin_config[selected_templete_general] == "" ? "alert(language_data['design.common.php']['A'][language]);":"")."\"><!--선택된 쇼핑몰 스킨이 없습니다. 스킨 선택 후 디자인 수정을 하실수 있습니다.-->
								<tr>
									<th class='box_01'></th>
									<td class='box_02'></td>
									<th class='box_03'></th>
								</tr>
								<tr>
									<th class='box_04'></th>
									<td class='box_05' style='height:50px;padding:0px;width:100%'>	
									<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
										<col width='130px'>
										<col width='*'>
										<tr bgcolor=#ffffff height=50 >";
										if($admin_config[mall_page_type] == "P"){
										$mstring .= "
											<td class='input_box_title' style='text-align:center;".($admin_config[mall_page_type] == "P" ? "color:gray;font-size:13px;":"font-size:13px;")."'>
											<b>일반 스킨 </b>
											</td>
											<td class='input_box_item point' style='text-align:center;' nowrap><b>".SelectDirList("selected_templete", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/templet", $admin_config[selected_templete_general])."</b><a href=\"javascript:document.location='?mall_page_type=P&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth&mmode=".$mmode."&selected_templete='+$('#selected_templete').val() \" style='font-size:12px;padding:0 0 0 10px'>작업스킨선택</a>
											</td>";
										
										}else{
										$mstring .= "
											<td class='input_box_title' style='text-align:center;".($admin_config[mall_page_type] == "M" ? "color:gray;font-size:13px;":"font-size:13px;")."' onclick=\"".($admin_config[selected_templete_general] == "" ? "alert(language_data['design.common.php']['B'][language]);":"document.location='?mall_page_type=P&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth';")."\"><b>일반 스킨 </b>
											</td>
											<td class='input_box_item' style='text-align:center;font-weight:bold;' nowrap><b>".$admin_config[selected_templete_general]."</b></td>";
											//'선택된 스킨이 없습니다. 스킨 선택 후 디자인 수정을 하실수 있습니다.'
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
						<td style='padding:0px 0px 0px 5px'>
							<table class='".($admin_config[mall_page_type] == "M" ? "box_18":"box_15")."' cellpadding=0 style='width:100%;cursor:pointer'><!--document.location='?mall_page_type=M&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth';-->
								<tr>
									<th class='box_01'></th>
									<td class='box_02'></td>
									<th class='box_03'></th>
								</tr>
								<tr>
									<th class='box_04'></th>
									<td class='box_05' style='height:50px;padding:0px'>	
									<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
										<col width='130px'>
										<col width='*'>
										<tr bgcolor=#ffffff height=50 >";
										if($admin_config[mall_page_type] == "M"){
										$mstring .= "
											<td class='input_box_title' style='text-align:center;".($admin_config[mall_page_type] == "M" ? "color:gray;font-size:13px;":"font-size:13px;")."' nowrap><b> 모바일 스킨 </b></td>
											<td class='input_box_item point' style='text-align:center;' nowrap><b>".SelectDirList("mall_use_mobile_templete", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/mobile_templet", $admin_config[selected_templete_mobile])."</b><a href=\"javascript:document.location='?mall_page_type=M&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth&mmode=".$mmode."&selected_templete='+$('#mall_use_mobile_templete').val() \" style='font-size:12px;padding:0 0 0 10px'>작업스킨선택</a>
											</td>";
										
										}else{
										$mstring .= "
											<td class='input_box_title' style='text-align:center;".($admin_config[mall_page_type] == "P" ? "color:gray;font-size:13px;":"font-size:13px;")."' onclick=\"".($admin_config[selected_templete_mobile] == "" ? "alert(language_data['design.common.php']['C'][language]);":"document.location='?mall_page_type=M&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth';")."\"><b> 모바일 스킨 </b>
											</td>
											<td class='input_box_item' style='text-align:center;font-weight:bold;' nowrap><b>".$admin_config[selected_templete_mobile]."</b></td>"; //'선택된 모바일 스킨이 없습니다. 모바일 스킨 선택 후 디자인 수정을 하실수 있습니다.'
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
						</td>";
		if($admininfo[mall_type] == "O"){
			$mstring .= "
						<td style='padding:0px 0px 0px 5px'>
							<table class='".($admin_config[mall_page_type] == "MI" ? "box_18":"box_15")."' cellpadding=0 style='width:100%;cursor:pointer'><!--document.location='?mall_page_type=MI&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth';-->
								<tr>
									<th class='box_01'></th>
									<td class='box_02'></td>
									<th class='box_03'></th>
								</tr>
								<tr>
									<th class='box_04'></th>
									<td class='box_05' style='height:50px;padding:0px'>	
									<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
										<col width='130px'>
										<col width='*'>
										<tr bgcolor=#ffffff height=50 >";
										if($admin_config[mall_page_type] == "MI"){
											$mstring .= "
											<td class='input_box_title' style='text-align:center;".($admin_config[mall_page_type] == "MI" ? "color:gray;font-size:13px;":"font-size:13px;")."' class='input_box_title'>
											<b> 미니샵 스킨 : 
											</td>
											<td class='input_box_item point' style='text-align:center;' nowrap>".SelectDirList("minishop_templet", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/minishop_templet", $admin_config[selected_templete_minishop])."</b><a href=\"javascript:document.location='?mall_page_type=MI&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth&mmode=".$mmode."&selected_templete='+$('#minishop_templet').val() \" style='font-size:12px;padding:0 0 0 10px'>작업스킨선택</a>
											</td>";
										
										}else{
											$mstring .= "
											<td class='input_box_title' style='text-align:center;".($admin_config[mall_page_type] == "P" ? "color:gray;font-size:13px;":"font-size:13px;")."' onclick=\"".($admin_config[selected_templete_minishop] == "" ? "alert('선택된 미니샵 스킨이 없습니다. 미니샵 스킨 선택 후 디자인 수정을 하실수 있습니다.');":"document.location='?mall_page_type=MI&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth';")."\">
											<b> 미니샵 스킨</b>
											</td>
											<td class='input_box_item' style='text-align:center;' nowrap><b> ".$admin_config[selected_templete_minishop]."</b></td>";
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
						</td>";
		}
		$mstring .= "
						</tr>
						</table>";
		
			return $mstring;
		
		}
		
		// 아래코드는 디비에 있는 내용과 xml의 내용이 상이하기 때문에
		// 디비를 중심으로 xml을 재생성하는 코드였으나
		// 디자인관련 디비가 삭제된 이상 필요가 없어진듯 
		function updateLayoutXML($mall_ix, $mall_page_type = "P"){
			syslog(LOG_INFO, 'updateLayoutXML BEGIN');
			//global $db, $DOCUMENT_ROOT, $admin_config, $admininfo;
			global $DOCUMENT_ROOT, $admin_config, $admininfo;
			//print_r($admin_config);
			//$xml = new XmlWriter_();
		
			//$xml->push('layouts');
			
			//// 결국 아래의 코드는 layout info 에는 항목이 추가되었으나 shop design 에는 항목이 없어 추가된 항목이 xml 에 반영되지 않아 처리하는 부분으로
			//// xpath 를 이용 검색하여 만일 해당 코드가 없다면 추가하는 방식으로 진행되어야 할것이다.
			//// XML을 DB를 기준으로 추가된 내용을 먼저 추가하고
			//// 메모리에 있는 DB를 중심으로 XML의 내용을 삭제  
			//echo $mall_page_type;
			
			$layoutXmlPath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/templet/" . $admin_config["selected_templete"] . "/layout2.xml";
			
			syslog(LOG_INFO, "design.common.layoutXmlPath : " . $layoutXmlPath);
			
			$layoutXml = new LayoutXml($layoutXmlPath);
			if($mall_page_type == "P"){
				$dirname = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/templet/".$admin_config[selected_templete];
			}else if($mall_page_type == "M"){
				$dirname = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/mobile_templet/".$admin_config[selected_templete];
			}else if($mall_page_type == "MI"){
				$dirname = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/minishop_templet/".$admin_config[selected_templete];
			}
			
			
			syslog(LOG_INFO, 'updateLayoutXML END\n');
		}



		closelog();
	} else {
		define_syslog_variables();
		openlog("phplog", LOG_PID , LOG_LOCAL0);


		include('../../include/xmlWriter.php');
		//print_r($admin_config);
		//print_r($admin_config);
		if($mall_page_type != "" || $selected_templete != ""){
			if($mall_page_type){
				$admin_config[mall_page_type] = $mall_page_type;
			}
			if($selected_templete){
				$admin_config[selected_templete] = $selected_templete;

				if($admin_config[mall_page_type] == "P"){
					$admin_config[selected_templete_general] = $admin_config[selected_templete];		
				}else if($admin_config[mall_page_type] == "M"){
					$admin_config[selected_templete_mobile] = $admin_config[selected_templete];		
				}else if($admin_config[mall_page_type] == "MI"){
					$admin_config[selected_templete_minishop] = $admin_config[selected_templete];
				}
			
			}
			
			session_register("admin_config");
			
		}else{

			if($admin_config[mall_page_type] == ""){
				$admin_config[mall_page_type] = "P";
			}
			//if($admin_config[selected_templete] == ""){
				if($admin_config[mall_page_type] == "P"){
					$admin_config[selected_templete] = $admin_config[selected_templete_general];		
				}else if($admin_config[mall_page_type] == "M"){
					$admin_config[selected_templete] = $admin_config[selected_templete_mobile];		
				}else if($admin_config[mall_page_type] == "MI"){
					$admin_config[selected_templete] = $admin_config[selected_templete_minishop];
				}
			//}
			if($admin_config[selected_templete_general] == ""){
				$admin_config[selected_templete_general] = $admin_config[mall_use_templete];
			}
			if($admin_config[selected_templete_mobile] == ""){
				$admin_config[selected_templete_mobile] = $admin_config[mall_use_mobile_templete];
			}
			if($admin_config[selected_templete_minishop] == ""){
				$admin_config[selected_templete_minishop] = $admin_config[minishop_templete];
			}

		}
		//echo $admin_config[selected_templete_general];
		//print_r($admin_config);


		function SelectDesignSkin(){
			global $admin_config, $admininfo, $mod, $page_name, $SubID, $pcode, $depth, $mmode;
			
		$mstring = "
		<table style='width:100%;margin-top:20px;' border=0 >";
		if($admininfo[mall_type] != "O"){
			$mstring .= "
				<col width='50%'>
				<col width='50%'>";
		}else{
			if($admin_config[mall_page_type] == "P"){
			$mstring .= "
				<col width='40%'>
				<col width='30%'>
				<col width='30%'>";
			}else if($admin_config[mall_page_type] == "M"){
			$mstring .= "
				<col width='30%'>
				<col width='40%'>
				<col width='30%'>";
			}else if($admin_config[mall_page_type] == "MI"){
			$mstring .= "
				<col width='30%'>
				<col width='30%'>
				<col width='40%'>";
			}
		}
		$mstring .= "
						<tr>
						<td style='padding:0px 5px 0px 0px'>
							<table class='".($admin_config[mall_page_type] == "P" ? "box_18":"box_15")."' cellpadding=0 style='width:100%;cursor:pointer' onclick=\"".($admin_config[selected_templete_general] == "" ? "alert(language_data['design.common.php']['A'][language]);":"")."\"><!--선택된 쇼핑몰 스킨이 없습니다. 스킨 선택 후 디자인 수정을 하실수 있습니다.-->
								<tr>
									<th class='box_01'></th>
									<td class='box_02'></td>
									<th class='box_03'></th>
								</tr>
								<tr>
									<th class='box_04'></th>
									<td class='box_05' style='height:50px;padding:0px;width:100%'>	
									<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
										<col width='130px'>
										<col width='*'>
										<tr bgcolor=#ffffff height=50 >";
										if($admin_config[mall_page_type] == "P"){
										$mstring .= "
											<td class='input_box_title' style='text-align:center;".($admin_config[mall_page_type] == "P" ? "color:gray;font-size:13px;":"font-size:13px;")."'>
											<b>일반 스킨 </b>
											</td>
											<td class='input_box_item point' style='text-align:center;' nowrap><b>".SelectDirList("selected_templete", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/templet", $admin_config[selected_templete_general])."</b><a href=\"javascript:document.location='?mall_page_type=P&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth&mmode=".$mmode."&selected_templete='+$('#selected_templete').val() \" style='font-size:12px;padding:0 0 0 10px'>작업스킨선택</a>
											</td>";
										
										}else{
										$mstring .= "
											<td class='input_box_title' style='text-align:center;".($admin_config[mall_page_type] == "M" ? "color:gray;font-size:13px;":"font-size:13px;")."' onclick=\"".($admin_config[selected_templete_general] == "" ? "alert(language_data['design.common.php']['B'][language]);":"document.location='?mall_page_type=P&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth';")."\"><b>일반 스킨 </b>
											</td>
											<td class='input_box_item' style='text-align:center;font-weight:bold;' nowrap><b>".$admin_config[selected_templete_general]."</b></td>";
											//'선택된 스킨이 없습니다. 스킨 선택 후 디자인 수정을 하실수 있습니다.'
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
						<td style='padding:0px 0px 0px 5px'>
							<table class='".($admin_config[mall_page_type] == "M" ? "box_18":"box_15")."' cellpadding=0 style='width:100%;cursor:pointer'><!--document.location='?mall_page_type=M&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth';-->
								<tr>
									<th class='box_01'></th>
									<td class='box_02'></td>
									<th class='box_03'></th>
								</tr>
								<tr>
									<th class='box_04'></th>
									<td class='box_05' style='height:50px;padding:0px'>	
									<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
										<col width='130px'>
										<col width='*'>
										<tr bgcolor=#ffffff height=50 >";
										if($admin_config[mall_page_type] == "M"){
										$mstring .= "
											<td class='input_box_title' style='text-align:center;".($admin_config[mall_page_type] == "M" ? "color:gray;font-size:13px;":"font-size:13px;")."' nowrap><b> 모바일 스킨 </b></td>
											<td class='input_box_item point' style='text-align:center;' nowrap><b>".SelectDirList("mall_use_mobile_templete", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/mobile_templet", $admin_config[selected_templete_mobile])."</b><a href=\"javascript:document.location='?mall_page_type=M&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth&mmode=".$mmode."&selected_templete='+$('#mall_use_mobile_templete').val() \" style='font-size:12px;padding:0 0 0 10px'>작업스킨선택</a>
											</td>";
										
										}else{
										$mstring .= "
											<td class='input_box_title' style='text-align:center;".($admin_config[mall_page_type] == "P" ? "color:gray;font-size:13px;":"font-size:13px;")."' onclick=\"".($admin_config[selected_templete_mobile] == "" ? "alert(language_data['design.common.php']['C'][language]);":"document.location='?mall_page_type=M&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth';")."\"><b> 모바일 스킨 </b>
											</td>
											<td class='input_box_item' style='text-align:center;font-weight:bold;' nowrap><b>".$admin_config[selected_templete_mobile]."</b></td>"; //'선택된 모바일 스킨이 없습니다. 모바일 스킨 선택 후 디자인 수정을 하실수 있습니다.'
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
						</td>";
		if($admininfo[mall_type] == "O"){
			$mstring .= "
						<td style='padding:0px 0px 0px 5px'>
							<table class='".($admin_config[mall_page_type] == "MI" ? "box_18":"box_15")."' cellpadding=0 style='width:100%;cursor:pointer'><!--document.location='?mall_page_type=MI&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth';-->
								<tr>
									<th class='box_01'></th>
									<td class='box_02'></td>
									<th class='box_03'></th>
								</tr>
								<tr>
									<th class='box_04'></th>
									<td class='box_05' style='height:50px;padding:0px'>	
									<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
										<col width='130px'>
										<col width='*'>
										<tr bgcolor=#ffffff height=50 >";
										if($admin_config[mall_page_type] == "MI"){
											$mstring .= "
											<td class='input_box_title' style='text-align:center;".($admin_config[mall_page_type] == "MI" ? "color:gray;font-size:13px;":"font-size:13px;")."' class='input_box_title'>
											<b> 미니샵 스킨 : 
											</td>
											<td class='input_box_item point' style='text-align:center;' nowrap>".SelectDirList("minishop_templet", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/minishop_templet", $admin_config[selected_templete_minishop])."</b><a href=\"javascript:document.location='?mall_page_type=MI&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth&mmode=".$mmode."&selected_templete='+$('#minishop_templet').val() \" style='font-size:12px;padding:0 0 0 10px'>작업스킨선택</a>
											</td>";
										
										}else{
											$mstring .= "
											<td class='input_box_title' style='text-align:center;".($admin_config[mall_page_type] == "P" ? "color:gray;font-size:13px;":"font-size:13px;")."' onclick=\"".($admin_config[selected_templete_minishop] == "" ? "alert('선택된 미니샵 스킨이 없습니다. 미니샵 스킨 선택 후 디자인 수정을 하실수 있습니다.');":"document.location='?mall_page_type=MI&mod=".$mod."&page_name=".$page_name."&SubID=".$SubID."&pcode=".$pcode."&depth=$depth';")."\">
											<b> 미니샵 스킨</b>
											</td>
											<td class='input_box_item' style='text-align:center;' nowrap><b> ".$admin_config[selected_templete_minishop]."</b></td>";
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
						</td>";
		}
		$mstring .= "
						</tr>
						</table>";

			return $mstring;

		}

		function updateLayoutXML($mall_ix, $mall_page_type = "P"){
			global $db, $DOCUMENT_ROOT, $admin_config;
			//print_r($admin_config);
			$xml = new XmlWriter_();

			$xml->push('layouts');
			if($mall_page_type == "P"){
				$db->query("select * from shop_design_skin  ");
				$db->fetch();
				$xml->push('photoskins');
				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					$xml->push('photoskin', array('photoskin_type' => $db->dt[photoskin_type], 'photoskin_name' => $db->dt[photoskin_name]));   
					//$xml->element('photoskin_name', $db->dt[photoskin_name]);   
					$xml->element('photoskin_path', $admin_config[mall_data_root]."/photoskin/".$db->dt[photoskin]);   
					$xml->pop();
					//$skininfo[$db->dt[photoskin_type]][$db->dt[photoskin_name]] = $db->dt[photoskin];
				}
				$xml->pop();

			}
			
			
			//echo $mall_page_type;
			if($mall_page_type == "P"){
				
				$sql = "select d.*, li.basic_link, li.depth from ".TBL_SHOP_LAYOUT_INFO." li left join ".TBL_SHOP_DESIGN." d on d.pcode = li.cid 
						where mall_ix='$mall_ix' and skin_type = '".$mall_page_type."' 
						and templet_name = '".$admin_config[selected_templete]."' ";

			}else if($mall_page_type == "M"){
				$sql = "select d.*, li.basic_link, li.depth from ".TBL_SHOP_LAYOUT_INFO." li left join ".TBL_SHOP_DESIGN." d on d.pcode = li.cid 
						where mall_ix='$mall_ix' and skin_type = '".$mall_page_type."' 
						and templet_name = '".$admin_config[selected_templete]."' ";
			}else if($mall_page_type == "MI"){
				$sql = "select d.*, li.basic_link, li.depth from ".TBL_SHOP_LAYOUT_INFO." li left join ".TBL_SHOP_DESIGN." d on d.pcode = li.cid 
						where mall_ix='$mall_ix' and skin_type = '".$mall_page_type."' 
						and templet_name = '".$admin_config[selected_templete]."' ";
			}
			
			$db->query($sql);
			$layouts = $db->fetchall();
			
			foreach ($layouts as $layout) {    
				//$xml->push('shop', array('species' => $animal[0]));    
				$xml->push('layout', array('pcode' => $layout[pcode],'basic_link' => $layout[basic_link],'depth' => $layout[depth]));   
				

				$xml->element('layout', $layout[layout]);    
			//	$xml->element('page_navi', "<![CDATA[".$layout[page_navi]."]]>" );   
				$xml->element('page_navi', $layout[page_navi]);   
				$xml->element('header1', $layout[header1]);    
				$xml->element('header2', $layout[header2]);    
				$xml->element('leftmenu', $layout[leftmenu]);
				$xml->element('contents', $layout[contents]);
				$xml->element('contents_add', $layout[contents_add]);
				$xml->element('rightmenu', $layout[rightmenu]);
				$xml->element('footer1', $layout[footer1]);
				$xml->element('footer2', $layout[footer2]);
				$xml->element('page_path', trim($layout[page_path]));
				$xml->element('caching', $layout[caching]);
				$xml->element('caching_time', $layout[caching_time]);
				$xml->pop();
			}
				
			$xml->pop();
			//print $xml->getXml();
			//exit;
			if($mall_page_type == "P"){
				$dirname = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[selected_templete];
			}else if($mall_page_type == "M"){
				$dirname = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/mobile_templet/".$admin_config[selected_templete];
			}else if($mall_page_type == "MI"){
				$dirname = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/minishop_templet/".$admin_config[selected_templete];
			}
			/*
			if(!is_dir($dirname)){		
				if(is_writable($path)){
					mkdir($dirname, 0777, true);
					chmod($dirname, 0777);	
				}
			}
			*/
			//$fileName = "main_flash.xml"; 
				
				$fp = fopen($dirname."/layout.xml","w");
				fputs($fp, $xml->getXml());
				fclose($fp);
				
			
		}
	}

?>
