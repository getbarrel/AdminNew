<?
include($_SERVER["DOCUMENT_ROOT"]."/include/mail_smtp.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include("../class/layout.class");

$P = new LayOut();
$db = new Database;

$where = " where cu.code != '' and cu.code = cmd.code and ";
if($region != ""){
	$where .= " and addr1 LIKE  '%".$region."%' ";
}








if($mailsend_yn == "Y"){	
	$where .= " and info =  1 ";
}

if($search_type != "" && $search_text != ""){	
	$where .= " and $search_type LIKE  '%$search_text%' ";
}

$startDate = $FromYY.$FromMM.$FromDD;
$endDate = $ToYY.$ToMM.$ToDD;
	
if($startDate != "" && $endDate != ""){	
	$where .= " and  MID(replace(date,'-',''),1,8) between  '$startDate' and '$endDate' ";
}

$vstartDate = $vFromYY.$vFromMM.$vFromDD;
$vendDate = $vToYY.$vToMM.$vToDD;
	
if($vstartDate != "" && $vendDate != ""){	
	$where .= " and  MID(replace(last,'-',''),1,8) between  '$vstartDate' and '$vendDate' ";
}


$sql = "insert into ".TBL_SHOP_TMP." (mall_ix, design_tmp) values ";
$sql .= " ( '".$admininfo[mall_ix]."', '$content') ";
$db->query($sql);

$db->query("select design_tmp as content from ".TBL_SHOP_TMP." where mall_ix ='".$admininfo[mall_ix]."' ");
$db->fetch();
$mail_content = $db->dt[content];
$db->query("delete from ".TBL_SHOP_TMP." where mall_ix ='".$admininfo[mall_ix]."' ");

 //////////////////////////////////////////////////////////////////

	$data_text = $mail_content;  //에디트의 내용
	$data_text_convert = $mail_content;
	$data_text_convert = str_replace("\\","",$data_text_convert);
	preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);

	
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/mail/";

	if(!is_dir($path)){
		mkdir($path, 0777);		
	}


//	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/mail/"; 
	
//	if(substr_count($data_text,"<IMG") > 0){
//		if(!is_dir($path)){
//			mkdir($path, 0777);
			//chmod($path,0777)
//		}
//	}

//print_r ($out);
//exit;
/*
	for($i=0;$i < count($out);$i++){
		for($j=0;$j < count($out[$i]);$j++){
			
			$img = returnImagePath($out[$i][$j]);
			$img = ClearText($img);
			
	
			if(substr_count($img,$admin_config[mall_data_root]."/images/mail/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
				if(substr_count($img,"$HTTP_HOST")>0){	
					$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);
					
					@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/mail/".returnFileName($img));
					if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
						unlink($local_img_path);	
					}
					
					//$data_text = str_replace($img,$admin_config[mall_data_root]."/images/mail/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					$data_text = str_replace("upfile","mail",$data_text);	 
				}else{
					if(copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/mail/".returnFileName($img))){
						$data_text = str_replace($img,$admin_config[mall_data_root]."/images/mail/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환	
					}
				}
			}
			
		
			
		}
	}
*/
$mail_content = $data_text;

//echo $mail_content;
//exit;

//////////////////////////////////////////////////////////////////

$sql = "Select cu.code, cmd.name, cu.id, cmd.mail, SUBSTRING_INDEX(cmd.mail,'@',-1) AS ns from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd $where and mem_level != 'E'";

$db->query($sql);	


if($db->total){
	
	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");	
	
	for($i=0;$i < $db->total; $i++){
		$db->fetch($i);	

		if (ValidateDNS($db->dt['ns'])){
			
			$mail_subject = $mail_title;
			
			
			$mail_info[mem_name] = $db->dt[name];
			$mail_info[mem_mail] = $db->dt[mail];
			$mail_info[mem_id] = $db->dt[id];
			
			//echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
			
			
			//if (mail_smtp($recipients, $to, $from, $subject, $body, $body_type, $file, $file_name, $host)){
			if (SendMail($mail_info, $mail_subject,$mail_content,"")){
				$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일이 발송되었습니다.<br>";
			}else{
				$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일 발송이 실패했습니다.<br>";
			}	
			
			//echo $message;
		}else{
			echo "DNS ERROR";	
		}
	}
	
	echo "<script>alert(language_data['mail.send.php']['A'][language]);history.back();</script>";
	//'정상적으로 메일이 발송되었습니다.'
	
	
}else{
	echo "DATA 가 존재하지 않습니다.";
}


//exit;
		
function ValidateDNS($host)
{
	return (checkdnsrr($host, ANY))? true: false;
}		

?>