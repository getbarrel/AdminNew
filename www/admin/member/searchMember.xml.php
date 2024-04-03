<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
header("Pragma: no-cache");
header('Content-type: text/xml;');
//header("Content-type: charset=euc-kr"); 

$db = new Database;

$where = " where code != '' ";
if($region != ""){
	$where .= " and addr1 LIKE  '%".iconv("CP949","utf-8",$region)."%' ";
}






if($sex == "M" || $sex == "W"){	
	$where .= " and sex_div =  '".iconv("CP949","utf-8",$sex)."' ";
}

if($mailsend_yn == "Y"){	
	$where .= " and info =  1 ";
}

if($smssend_yn == "Y"){	
	$where .= " and sms =  1 ";
}

if($search_type != "" && $search_text != ""){	

	$where .= " and $search_type LIKE  '%".iconv("CP949","utf-8",$search_text)."%' ";

}

//$startDate = $FromYY.$FromMM.$FromDD;
//$endDate = $ToYY.$ToMM.$ToDD;
	
if($startDate != "" && $endDate != ""){	
	$where .= " and  MID(replace(date,'-',''),1,8) between  $startDate and $endDate ";
}

//$vstartDate = $vFromYY.$vFromMM.$vFromDD;
//$vendDate = $vToYY.$vToMM.$vToDD;
	
if($vstartDate != "" && $vendDate != ""){	
	$where .= " and  MID(replace(last,'-',''),1,8) between  $vstartDate and $vendDate ";
}

 
$sql = "Select code, name, mail, pcs from ".TBL_COMMON_MEMBER_DETAIL." $where ";
//echo $sql;
//exit;

$db->query($sql);

if($db->total){
		$xmlTmp = "<members>";
		$xmlTmp .= "<total>".$db->total." 건 </total>";
	for($i=0;($i < $db->total && $i < 100) ; $i++){
		$db->fetch($i);		
		$xmlTmp .= "	<member>";
		$xmlTmp .= "		<mem_num>".($i+1)."</mem_num>";
		$xmlTmp .= "		<mem_code>".$db->dt[code]."</mem_code>";
		$xmlTmp .= "		<mem_name>".$db->dt[name]."</mem_name>";
		$xmlTmp .= "		<mem_mail>".$db->dt[mail]."</mem_mail>";
		$xmlTmp .= "		<mem_mobile>".utf8_encode($db->dt[pcs])."</mem_mobile>";
		$xmlTmp .= "	</member>";
	}
/*	
	for($i=0;$i < $db->total; $i++){
		$db->fetch($i);		
		$xmlTmp .= "	<member>";
		$xmlTmp .= "		<mem_num>".($i+1)."</mem_num>";
		$xmlTmp .= "		<mem_code>".$db->dt[code])."</mem_code>";
		$xmlTmp .= "		<mem_name>".$db->dt[name])."</mem_name>";
		$xmlTmp .= "		<mem_mail>".$db->dt[mail])."</mem_mail>";
		$xmlTmp .= "		<mem_mobile>".utf8_encode($db->dt[pcs])."</mem_mobile>";
		$xmlTmp .= "	</member>";
	}
*/	
	$xmlTmp .= "</members>";
}else{
	$xmlTmp = "<members>";	
	$xmlTmp .= "<total>0 건 </total>";
	$xmlTmp .= "</members>";
}
echo $xmlTmp;


# Get Age (留
function reg2age($Str){
 if(strlen($Str)>6){
  if(substr($Str,6,1) > 2 )
   $add_year=2000;
  else
   $add_year=1900;
 }else
  $add_year=1900;
  
 $year = $add_year + substr($Str,0,2);
 $month = substr($Str,2,2);
 $day = substr($Str,4,2);
 $curr_year = $year;
 $today_year = date('Y');
 
 $curr_etc = $month . $day;
 $today_etc = date('m').date('d');
 
 $Age = $today_year - $curr_year;
 $Age += (($curr_etc - $today_etc)>0)?-1:0;
 return $Age;;
}

# Get Age (援
function reg2agekorea($Str){
 if(strlen($Str)>6){
  if(substr($Str,6,1) > 2 )
   $add_year=2000;
  else
   $add_year=1900;
 }else
  $add_year=1900;
  
 $year = $add_year + substr($Str,0,2);
 $month = substr($Str,2,2);
 $day = substr($Str,4,2);
 $curr_year = $year;
 $today_year = date('Y');
 
 $curr_etc = $month . $day;
 $today_etc = date('m').date('d');
 
 $Age = $today_year - $curr_year + 1;
 return $Age;;
}

?>

