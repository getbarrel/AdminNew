<?
include("$DOCUMENT_ROOT/class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");

$P = new msLayOut("");
$db = new MySQL();
$db2 = new MySQL();
$db3 = new MySQL();

$db->query("SELECT * FROM shop_mall_privacy_setting where mall_ix = '".$_SESSION["layout_config"][mall_ix]."'   ");
if($db->total){
	for($i=0; $i < $db->total;$i++){
	$db->fetch($i);
	$mall_config[$db->dt[config_name]] = $db->dt[config_value];
	}
}


// 테스트
if($_GET['tid'] != '' && $_GET['tdate'] ==''){
        $qry = "UPDATE common_user set last ='".date('Y-m-d')."' WHERE id = '".$_GET['tid']."' LIMIT 1";
        $db->query($qry);
}else if($_GET['tid'] != '' && $_GET['tdate'] !=''){
        $qry = "UPDATE common_user set last = date_format('".$_GET['tdate']."','%Y-%m-%d') WHERE id = '".$_GET['tid']."' LIMIT 1";
        $db->query($qry);
}else{
        echo "No : id, date";
}
// 테스트



// 1년
$sleep_date = date('Ymd',strtotime("-".$mall_config[sleep_date]." year")); //휴먼상태 적용 날짜


if($mall_config[sleep_user_mailing] == 'Y'){

	//휴면안내 메일 전송 사용일때
	$mailing_day = explode('|',$mall_config[sleep_user_mailing_day]);//전송 일자 가져옴

    if(is_array($mailing_day)){

        for($i = 0; $i < count($mailing_day); $i++){

			$sleep_mail_date = date('Ymd', strtotime("-".$mailing_day[$i]." days", strtotime($sleep_date)));

            // 발송 대상
			$sql = "SELECT 
						cu.id,
						AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
						AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
						AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs
					FROM 
						common_user cu
					LEFT JOIN
						common_member_detail cmd on cu.code = cmd.code
					WHERE 
					    cu.mem_type = 'M' 
					AND	AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') != 'a@a.com' 
					AND	MID(replace(if ( isnull(cu.last), cu.date, cu.last ) ,'-',''),1,8) = '".$sleep_mail_date."'";

            // 테스트
            if($_GET['tid'] != ''){
                $sql .= " AND cu.id = '".$_GET['tid']."' limit 1";
            }
            // 테스트

			$db->query($sql);

			if($db->total){
				for($z=0; $z < $db->total; $z++){
					$db->fetch($z);

					$mail_info[mem_name]   = $db->dt[name];
					$mail_info[mem_mail]   = $db->dt[mail];
					$mail_info[mem_id]     = $db->dt[id];
					$mail_info[mem_mobile] = $db->dt[pcs];
					$mail_info[noti_date]  = $mailing_day[$i];

				 	sendMessageByStep('member_exit_sleep', $mail_info);
				}
			}

		}
	}
}


// 테스트
// 메일 테스트 이하 임시 exit 처리, 테스트 이후 exit 풀 것
//exit;
// 테스트




$sql = "SELECT 
			cu.id,cu.code,cmd.name as name2,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
			AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs
		FROM 
			common_user cu,
			common_member_detail cmd 
		where 
		    cu.code = cmd.code 
		    and cu.mem_type = 'M'
		    and AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') != 'a@a.com'  
		    and	MID(replace(if ( isnull(cu.last), cu.date, cu.last ) ,'-',''),1,8) < '".$sleep_date."' ";
		
$db->query($sql);

if($db->total){
	$transction  = $db2->query("SET AUTOCOMMIT=0");
	$transction  = $db2->query("BEGIN");
	$transction_ok = true;
	$db2->transction = true;

	for($i=0; $i < $db->total; $i++){
		$db->fetch($i);


		$sql = "insert common_user_sleep_log set
				code = '".$db->dt[code]."',
				id = '".$db->dt[id]."',
				name = '".$db->dt[name2]."',
				status = 'S',
				message = '일반회원->휴면회원',
				change_type = 'S',
				regdate = NOW()
		";


		$transction = $db2->query($sql);
		if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;
		//$db2->debug=true;

		$sql = "insert into common_user_sleep select * from ".TBL_COMMON_USER." where code = '".$db->dt[code]."'  and NOT EXISTS (SELECT code FROM common_user_sleep WHERE code='".$db->dt[code]."')";
		$transction = $db2->query($sql);
		if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

		$sql = "delete from ".TBL_COMMON_USER." where code = '".$db->dt[code]."' ";
		$transction = $db2->query($sql);
		if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

		$sql = "insert into common_member_detail_sleep select * from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$db->dt[code]."'  and NOT EXISTS (SELECT code FROM common_member_detail_sleep WHERE code='".$db->dt[code]."')";
		$transction = $db2->query($sql);
		if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

		$sql = "delete from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$db->dt[code]."' ";
		$transction = $db2->query($sql);
		if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;
/*
		$sql = "insert into common_company_detail_sleep select * from ".TBL_COMMON_COMPANY_DETAIL." where code = '".$db->dt[code]."'  and NOT EXISTS (SELECT code FROM common_company_detail_sleep WHERE code='".$db->dt[code]."')";
		$transction = $db2->query($sql);
		if(!$transction ) $transction_ok = false;

		$sql = "delete from ".TBL_COMMON_COMPANY_DETAIL." where code = '".$db->dt[code]."' ";
		$transction = $db2->query($sql);
		if(!$transction ) $transction_ok = false;
*/

        $sql = "SELECT oid FROM shop_order WHERE user_code = '".$db->dt[code]."'";
        $db2->query($sql);

        if($db2->total > 0) {
            $order_datas = $db2->fetchall("object");
            for($x = 0; $x < count($order_datas); $x++) {
                $oid = $order_datas[$x]['oid'];

                // shop_order - separation_shop_order
                $sql = "INSERT INTO separation_shop_order (oid, btel, bmobile, bmail, bzip, baddr, regdate) 
                            SELECT oid, btel, bmobile, bmail, bzip, baddr, NOW() FROM shop_order WHERE oid = '" . $oid . "'";
                $db2->query($sql);

                $sql = "SELECT * FROM separation_shop_order WHERE oid = '" . $oid . "'";
                $db2->query($sql);
                if ($db2->total > 0) {
                    $sql = "UPDATE shop_order SET
                          btel=''
                          ,bmobile=''
                          ,bmail=''
                          ,bzip=''
                          ,baddr=''
                        WHERE oid = '" . $oid . "'";
                    $db2->query($sql);
                }

                // shop_order_detail_deliveryinfo - separation_shop_order_deliveryinfo
                $sql = "SELECT odd_ix FROM shop_order_detail_deliveryinfo WHERE oid = '" . $oid . "'";
                $db2->query($sql);
                if ($db2->total > 0) {
                    $order_detail_deliveryinfo_datas = $db2->fetchall("object");

                    for ($y = 0; $y < count($order_detail_deliveryinfo_datas); $y++) {
                        $order_detail_deliveryinfo = $order_detail_deliveryinfo_datas[$y];
                        $sql = "INSERT INTO separation_shop_order_deliveryinfo (odd_ix, oid, od_ix, rname, rtel, rmobile, rmail, zip, addr1, addr2, regdate) 
                                  SELECT odd_ix, oid, od_ix, rname, rtel, rmobile, rmail, zip, addr1, addr2, NOW() FROM shop_order_detail_deliveryinfo WHERE odd_ix = '" . $order_detail_deliveryinfo[odd_ix] . "'";
                        $db2->query($sql);

                        $sql = "SELECT * FROM separation_shop_order_deliveryinfo WHERE odd_ix = '" . $order_detail_deliveryinfo[odd_ix] . "'";
                        $db2->query($sql);
                        if ($db2->total > 0) {
                            $sql = "UPDATE shop_order_detail_deliveryinfo SET
                                  rname='휴면회원'
                                  ,rtel=''
                                  ,rmobile=''
                                  ,rmail=''
                                  ,zip=''
                                  ,addr1=''
                                  ,addr2=''
                                WHERE odd_ix = '" . $order_detail_deliveryinfo[odd_ix] . "'";
                            $db2->query($sql);
                        }
                    }
                }
            }
        }
		

		if(!$transction_ok){
			$transction = $db2->query("ROLLBACK");
		}else{
			//exit;
			$transction = $db2->query("COMMIT");

			$mail_info[mem_name] = $db->dt[name];
			$mail_info[mem_mail] = $db->dt[mail];
			$mail_info[mem_id] = $db->dt[id];
			$mail_info[mem_mobile] = $db->dt[pcs];

			sendMessageByStep('member_exit_sleep_d_day', $mail_info);
		}

		/*휴먼계정 상태 변경 쿼리 작업 영역*/
		//$sql = "update common_user set sleep_account='Y' where code = '".$db->dt[code]."'";
	    //	$db2->query($sql);
		/*END*/
		

	}
	exit;
}

exit;


?>