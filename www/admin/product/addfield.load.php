<?php
include("../../class/database.class");


$cid = $_GET['cid'];
$form = $_GET['form'];
$mode = $_GET['mode'];
$pid = $_GET['pid'];


header("Content-Tye: application/x-javascript");	

if($mode == "view"){
	$db = new Database;
	$db->query("SELECT * FROM shop_category_addfield where cid = '$cid' order by f_code asc");
	
	if($db->total){	
		for($i=1;$i <= 10;$i++){
			echo "document.forms['$form'].elements['etc".$i."'].value = ''; \n";	
			echo "document.forms['$form'].elements['etc".$i."_value'].value = ''; \n";
			echo "document.forms['$form'].elements['etc".$i."_search'].checked = false; \n";
			
		}	
		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
			echo "document.forms['$form'].elements['".$db->dt[f_code]."'].value = '".$db->dt[f_name]."'; \n";	
			
			echo "document.forms['$form'].elements['".$db->dt[f_code]."_value'].value = '".$db->dt[f_value]."'; \n";
			
			if($db->dt[f_search] == "1"){
				echo "document.forms['$form'].elements['".$db->dt[f_code]."_search'].checked = true; \n";
			}
			
			//echo "document.forms['$form'].elements['".$db->dt[f_code]."_type'].options['".$db->dt[f_type]."'].selected = true; \n";
			
			echo "var obj_ = document.forms['$form'].elements['".$db->dt[f_code]."_type'];\n";
			echo "for(i=0;i < obj_.length;i++){\n";
			echo "	if(obj_.options[i].value == '".$db->dt[f_type]."'){\n";
			echo "		obj_.options[i].selected = true;\n";
			echo "	}\n";
			echo "}\n";
			
			//echo "document.forms['$form'].elements['".$db->dt[f_code]."_type'].value = '".$db->dt[f_type]."'; \n";
		}	
	}else{
		for($i=1;$i <= 10;$i++){
			echo "document.forms['$form'].elements['etc".$i."'].value = ''; \n";	
		}	
	}
}

	
if($mode == "input_field"){
	$db = new Database;

	
	$db->query("SELECT brand, company, etc1, etc2, etc3, etc4, etc5, etc6, etc7, etc8, etc9, etc10 FROM shop_product where id = '$pid' ");
	//echo ("SELECT brand, company, etc1, etc2, etc3, etc4, etc5, etc6, etc7, etc8, etc9, etc10 FROM shop_product where id = '$pid' ");
	$db->fetch();
	$company = $db->dt[company];
	$brand = $db->dt[brand];
	$porudct = $db->dt;
	//print_r($porudct);
	//echo  $porudct[company]."\n";
	
	$db->query("SELECT * FROM shop_category_addfield where cid = '$cid' order by f_code asc");
	
	if($db->total){	
		
		echo "mstring = \"<table cellpadding=5 cellspacing=1 bgcolor=#ffffff width='100%'>\"; \n";
		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
										
			echo "mstring += \"	<tr bgcolor='#ffffff'>\";\n";
			echo "mstring += \"		<td bgcolor='#efefef' width=15% nowrap><img src='/manage/image/ico_dot.gif' align=absmiddle> ".$db->dt[f_name]."</td>\";\n";
			if($db->dt[f_type] == "text"){
				echo "mstring += \"		<td width=85%><input type=text class='textbox' name='".$db->dt[f_code]."'  style='width:100%' value='".str_replace("\"","&quot;",$porudct[$db->dt[f_code]])."'></td>\";\n";
			}else{
				echo "mstring += \"		<td width=85%>".makeSelect($db->dt,$porudct[$db->dt[f_code]])."</td>\";\n";
			}
			
			echo "mstring += \"	</tr>\";\n";
		}	
		echo "mstring += \"</table>\"; \n";
		//echo "alert(mstring); \n";
		echo "document.getElementById('add_info_area').innerHTML = mstring; \n";
	}	
	
	
	$db->query("SELECT b_ix, brand_name FROM shop_brand where disp=1 and cid ='$cid' ");
	
	if($db->total){
		echo "document.forms['$form'].elements['brand'].length = ".($db->total+1)."; \n";
		echo "document.forms['$form'].elements['brand'].options[0].text = '브랜드 선택'; \n";
		echo "document.forms['$form'].elements['brand'].options[0].value = ''; \n";
		for($i=0 ; $i < $db->total ; $i++){	
			$db->fetch($i);
			
			
			echo "document.forms['$form'].elements['brand'].options[".($i+1)."].text = '".$db->dt[brand_name]."'; \n";
			echo "document.forms['$form'].elements['brand'].options[".($i+1)."].value = '".$db->dt[brand_name]."'; \n";
			if($db->dt[brand_name] == $brand){
				echo "document.forms['$form'].elements['brand'].options[".($i+1)."].selected = true; \n";
			}
		}
	}else{
		echo "document.forms['$form'].elements['brand'].length = 1; \n";
		echo "document.forms['$form'].elements['brand'].options[0].text = '브랜드 선택'; \n";
		echo "document.forms['$form'].elements['brand'].options[0].value = ''; \n";
	}
	
	
	$db->query("SELECT company_name FROM shop_company where disp=1 and cid ='$cid' ");
	
	if($db->total){
		echo "document.forms['$form'].elements['company'].length = ".($db->total+1)."; \n";
		echo "document.forms['$form'].elements['company'].options[0].text = '제조사 선택'; \n";
		echo "document.forms['$form'].elements['company'].options[0].value = ''; \n";
		for($i=0 ; $i < $db->total ; $i++){	
			$db->fetch($i);
			echo "document.forms['$form'].elements['company'].options[".($i+1)."].text = '".$db->dt[company_name]."'; \n";
			echo "document.forms['$form'].elements['company'].options[".($i+1)."].value = '".$db->dt[company_name]."'; \n";
			
			
			if($db->dt[company_name] == $company){
				echo "document.forms['$form'].elements['company'].options[".($i+1)."].selected = true; \n";
			}
		}
	}else{
		echo "document.forms['$form'].elements['company'].length = 1; \n";
		echo "document.forms['$form'].elements['company'].options[0].text = '제조사 선택'; \n";
		echo "document.forms['$form'].elements['company'].options[0].value = ''; \n";
	}
	
}else{
	echo "document.getElementById('add_info_area').innerHTML = '선택된 카테고리에 해당하는 부가정보가 존재 하지 않습니다.'; \n";
}



function makeSelect($data_, $selected_value=""){	
	//print_r($data_);
	
	if($data_[f_value] == ""){
		$strDiv = "기본값이 입력되지 않았습니다.";
	}else{
		
		$data = split("[|]",$data_[f_value]);
		
		if(is_array($data)){
			if($data_[f_type] == "select"){
				$strDiv = "<Select name='".$data_[f_code]."' >";
				for($i=0;$i < count($data);$i++){
				       	if( $data[$i] == $selected_value ){
				        	$strDiv = $strDiv."<option value='".$data[$i]."' Selected>".$data[$i]."</option>";
				       	}else{
				       		$strDiv = $strDiv."<option value='".$data[$i]."' >".$data[$i]."</option>";
					}
				}
				$strDiv = $strDiv."</Select>";
				
			}else if($data_[f_type] == "radio"){
				
				for($i=0;$i < count($data);$i++){
					if( $data[$i] == $selected_value ){
						$strDiv = $strDiv."<input type=radio name='".$data_[f_code]."' id='".$data_[f_code]."_$i' value='".$data[$i]."' checked><label for='".$data_[f_code]."_$i'>".$data[$i]."</label>";
					}else{
						$strDiv = $strDiv."<input type=radio name='".$data_[f_code]."' id='".$data_[f_code]."_$i' value='".$data[$i]."' ><label for='".$data_[f_code]."_$i'>".$data[$i]."</label>";
					}
				}
			}
		}else{
			$strDiv = "값이 정상적으로 입력되지 않았습니다.";
		}
	}
	return $strDiv;

}

?>
