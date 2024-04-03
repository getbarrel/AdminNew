<?
include_once($_SERVER ['DOCUMENT_ROOT']."/class/database.class");
include_once($_SERVER ['DOCUMENT_ROOT']."/include/global_util.php");
$db = new Database;

$now = strtotime("-2 year", time());
$date = date('Y-m-d H:i:s', $now);

$sql = "SELECT  
                cmd.code, 
                cmd.info as email_agree,
                date_format(agree_infodate, '%Y-%m-%d') as email_agree_date,
                cmd.sms as sms_agree,
                date_format(cmd.smsdate, '%Y-%m-%d') as sms_agree_date,
                AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
                AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$slave_db->ase_encrypt_key."') as name,
                ps.is_allowable AS push_agree,
                date_format(ps.regdate, '%Y-%m-%d') AS push_agree_date
        FROM common_member_detail cmd 
        LEFT JOIN mobile_push_service ps
          ON cmd.code = ps.user_code
	   WHERE info='1' 
	    AND agree_infodate < '".$date."' 
	    AND agree_infodate !='0000-00-00 00:00:00'
			";
$db->query($sql);

$member_list = $db->fetchall("object");

for($i=0; $i <count($member_list);$i++){

    $mail_info['mem_name'] = $member_list[$i]["name"];

    if($member_list[$i]["email_agree"] == '1'){
        $mail_info['email_agree'] = '수신동의';
    }else{
        $mail_info['email_agree'] = '수신거부';
    }

    if($member_list[$i]["sms_agree"] == '1'){
        $mail_info['sms_agree'] = '수신동의';
    }else{
        $mail_info['sms_agree'] = '수신거부';
    }


    $mail_info['email_agree_date'] = $member_list[$i]["email_agree_date"];


    if($member_list[$i]["sms_agree_date"] == ""){
        $mail_info['sms_agree_date'] = $member_list[$i]["email_agree_date"];
    }else{
        $mail_info['sms_agree_date'] = $member_list[$i]["sms_agree_date"];
    }

    if($member_list[$i]["push_agree"] == "1"){
        $mail_info["push_agree"] = '수신동의';
        $mail_info['push_agree_date'] = $member_list[$i]["push_agree_date"];
    }else{
        $mail_info["push_agree"] = '수신거부';
        $mail_info['push_agree_date'] = $member_list[$i]["push_agree_date"];
    }

    $mail_info['mem_name'] = $member_list[$i]["name"];
    $mail_info['mem_mail'] = str_replace("'","",$member_list[$i]["mail"]);


    sendMessageByStep('information_agreement', $mail_info);

    $sql = "update common_member_detail set agree_infodate='".date("Y-m-d H:i:s")."' where code='".$member_list[$i]["code"]."'";
    $db->query($sql);
}
?>