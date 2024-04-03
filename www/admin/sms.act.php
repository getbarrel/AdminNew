<?
include("./class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

session_start();

$db = new Database;

if ($act == "send_mail"){

	//echo (count($mobiles));
	$cominfo = getcominfo();
	$sdb = new Database;
	$s = new SMS();

	$sms_able_count =  $s->getSMSAbleCount($admininfo);
	if($sms_able_count > 0){
			$s->send_phone = $cominfo[com_phone];
			$s->send_name = $cominfo[com_name];


			for($i=0;$i<count($mobiles);$i++){
				//echo $mobiles[$i];
				list($name, $mem_id, $mobile) = split("[|]",$mobiles[$i],3);
			/*	원래로직
				$s->dest_phone = str_replace("-","",$mobile);
				$s->dest_name = "$name";
				$s->msg_body =$sms_contents;
			
				$s->sendbyone($admininfo);
			*/


				//	직원 및 회원이 아닌 사람에게 메일 발송 금지 (id와 이메일 주소로 검색해서 내용이 없으면 안보낸다
					if(count($mobiles) > "0") {

						if($db->dbms_type == "oracle"){
							$db->query("SELECT CU.id, CUD.mail, CUD.pcs, CUD.info,	 CUD.sms FROM ".TBL_COMMON_USER." AS CU LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." AS CUD ON CU.code = CUD.code WHERE CU.id = '".trim($mem_id)."' AND REPLACE(UNHEX(CUD.pcs),'".$db->ase_encrypt_key."','-','')  = '".addslashes(trim(str_replace("-","",$mobile)))."'" );
						}else{
							$db->query("SELECT CU.id, CUD.mail, CUD.pcs, CUD.info,	 CUD.sms FROM ".TBL_COMMON_USER." AS CU LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." AS CUD ON CU.code = CUD.code WHERE CU.id = '".trim($mem_id)."' AND REPLACE(AES_DECRYPT(UNHEX(CUD.pcs),'".$db->ase_encrypt_key."'),'-','')  = '".addslashes(trim(str_replace("-","",$mobile)))."'" );
						}

						$db->fetch();
						$wel_memberChk = $db->dt;

						if($wel_memberChk[id] == $mem_id) {
							$s->dest_phone = str_replace("-","",$mobile);
							$s->dest_name = "$name";
							$s->msg_body =$sms_contents;
						
							$s->sendbyone($admininfo);

								echo("<script language='javascript'>alert('정상적으로SMS가 발송되었습니다.');</script>");
								echo("<script language='javascript'>self.close();</script>");
						} else {
								echo("<script language='javascript'>alert('SMS 발송이 실패하였습니다.');</script>");
								echo("<script language='javascript'>self.close();</script>");
						}

					}
				//	//직원 및 회원이 아닌 사람에게 메일 발송 금지

			}

			//echo("<script language='javascript'>alert('정상적으로SMS가 발송되었습니다.');</script>");
			//echo("<script language='javascript'>self.close();</script>");

	}else{
			echo("<script language='javascript'>alert('사용가능한 SMS 수량이 없습니다. 충전후 SMS 발송이 가능합니다..');</script>");
			echo("<script language='javascript'>self.close();</script>");
	}

}


if ($act == "send_sms_manual"){

    //echo (count($mobiles));
    $cominfo = getcominfo();
    $sdb = new Database;
    $s = new SMS();

    $sms_able_count =  $s->getSMSAbleCount($admininfo);
    if($sms_able_count > 0){
        $s->send_phone = $cominfo[com_phone];
        $s->send_name = $cominfo[com_name];
        $mobiles = array();
        if(strpos($sms_phone_area,",") !== false){
            $mobiles = explode(",",$sms_phone_area);
        }else if(strpos($sms_phone_area,"\n") !== false){//\n

            $mobiles = explode("\n",$sms_phone_area);
        }else{

            $mobiles[] = $sms_phone_area;
        }

        for($i=0;$i<count($mobiles);$i++){
            //echo $mobiles[$i];

            $s->dest_phone = str_replace("-","",trim($mobiles[$i]));
            $s->dest_name = "";
            $s->msg_body =$sms_contents;

            $s->sendbyone($admininfo);
        }

        echo("<script language='javascript'>alert('정상적으로SMS가 발송되었습니다.');</script>");
        echo("<script language='javascript'>top.document.location.reload();</script>");
    }else{
        echo("<script language='javascript'>alert('사용가능한 SMS 수량이 없습니다. 충전후 SMS 발송이 가능합니다..');</script>");
        echo("<script language='javascript'>self.close();</script>");
    }

}

?>