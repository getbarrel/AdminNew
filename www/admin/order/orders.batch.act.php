<?
	include("../class/layout.class");
	include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

	$db = new Database;
	// 조건절 셋팅

	if($search_searialize_value){
		$unserialize_search_value = unserialize(urldecode($search_searialize_value));
		extract($unserialize_search_value);
	}

	if($_POST["update_kind"]){
		$update_kind = $_POST["update_kind"];
	}



// 검색 조건 설정 부분
if($view_type == 'sc_order'){
    $where = "WHERE od.status !='SR' AND od.product_type IN (".implode(',',$sns_product_type).") ";
    $folder_name = "sns";
}else{
    $where = "WHERE od.status !='SR' ";
    $folder_name = "product";
}

if($mode != "search"){
    $orderdate = 1;
}

if(!$date_type){
    $date_type = "o.order_date";
}

if($orderdate){
    //$where .= "and date_format(".$date_type.",'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
    $where .= "and ".$date_type." between '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";
}

if($mmode == "personalization" && $mem_ix != ""){
    $where .= " and o.user_code = '".$mem_ix."' ";
}

if(is_array($type)){
    for($i = 0; $i < count($type); $i++){
        if($type[$i]){
            if($type_str == ""){
                $type_str .= "'".$type[$i]."'";
            }else{
                $type_str .= ", '".$type[$i]."' ";
            }
        }
    }
    if($type_str != "") {
        $where .= "and od.status in ($type_str) ";
    }
}else{
    if($type){
        $where .= "and od.status = '$type' ";
    }
}

if(is_array($refund_type)){
    for($i = 0; $i < count($refund_type); $i++){
        if($refund_type[$i]){
            if($refund_type_str == ""){
                $refund_type_str .= "'".$refund_type[$i]."'";
            }else{
                $refund_type_str .= ", '".$refund_type[$i]."' ";
            }
        }
    }

    if($refund_type_str != ""){
        $where .= "and od.refund_status in ($refund_type_str) ";
    }
}else{
    if($refund_type){
        $where .= "and od.refund_status = '$refund_type' ";
    }
}

$left_join = "";
if(is_array($method)){
    for($i=0;$i < count($method);$i++){
        if($method[$i] != ""){
            if($method_str == ""){
                $method_str .= "'".$method[$i]."'";
            }else{
                $method_str .= ", '".$method[$i]."' ";
            }
        }
    }
    if($method_str != ""){
        $where .= "and op.method in ($method_str) ";
        $left_join .= " left join shop_order_payment op on (op.oid=od.oid and op.method in ($method_str)) ";
    }
}else{
    if($method){
        $where .= "and op.method = '$method' ";
        $left_join .= " left join shop_order_payment op on (op.oid=od.oid and op.method = '$method') ";
    }
}

if($_GET['mode'] == 'search'){
    if(!isset($_GET['mult_search_use'])) {
        $mult_search_use = 0;
    }
}else {
    $mult_search_use = 1;
}

if($search_type && $search_text){


    if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
        //다중검색 시작 2014-04-10 이학봉
        if($search_text != ""){

            if(strpos($search_text,",") !== false){
                $search_array = explode(",",$search_text);
                $search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));

                $where .= "and $search_type in ( ";
                $count_where .= "and $search_type in ( ";

                for($i=0;$i<count($search_array);$i++){
                    if($search_type == 'o.bmobile' || $search_type == 'odd.rmobile') {
                        $search_array[$i] = format_phone(trim($search_array[$i]));
                    }else {
                        $search_array[$i] = trim($search_array[$i]);
                    }
                    if($search_array[$i]){
                        if($i == count($search_array) - 1){
                            $where .= "'".trim($search_array[$i])."'";
                            $count_where .= "'".trim($search_array[$i])."'";
                        }else{
                            $where .= "'".trim($search_array[$i])."' , ";
                            $count_where .= "'".trim($search_array[$i])."' , ";
                        }
                    }
                }
                $where .= ")";
                $count_where .= ")";
            }else if(strpos($search_text,"\n") !== false){//\n
                $search_array = explode("\n",$search_text);
                $search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));

                $where .= "and $search_type in ( ";
                $count_where .= "and $search_type in ( ";

                for($i=0;$i<count($search_array);$i++){
                    if($search_type == 'o.bmobile' || $search_type == 'odd.rmobile') {
                        $search_array[$i] = format_phone(trim($search_array[$i]));
                    }else {
                        $search_array[$i] = trim($search_array[$i]);
                    }
                    if($search_array[$i]){
                        if($i == count($search_array) - 1){
                            $where .= "'".trim($search_array[$i])."'";
                            $count_where .= "'".trim($search_array[$i])."'";
                        }else{
                            $where .= "'".trim($search_array[$i])."' , ";
                            $count_where .= "'".trim($search_array[$i])."' , ";
                        }
                    }
                }
                $where .= ")";
                $count_where .= ")";
            }else{
                if($search_type == 'o.bmobile' || $search_type == 'odd.rmobile') {
                    $where .= " and ".$search_type." = '".format_phone(trim($search_text))."'";
                    $count_where .= " and ".$search_type." = '".trim($search_text)."'";
                }else {
                    $where .= " and ".$search_type." = '".trim($search_text)."'";
                    $count_where .= " and ".$search_type." = '".trim($search_text)."'";
                }
            }

            if ( substr_count($search_type, 'odd.') > 0) {
                $left_join .= " left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix) ";
            }

            if(substr_count($left_join,'shop_order_payment op')==0){
                if (substr_count($search_type, 'op.') > 0) {
                    $left_join .= " left join shop_order_payment op on (op.oid=od.oid) ";
                }
            }
        }
    }else {

        // 주문자휴대폰 or 주문번호
        if ($search_type == "combi_mboid") {
            $where .= "and (   REPLACE(bmobile,'-','') LIKE '%" . trim($search_text) . "%' 
                        or REPLACE(odd.rmobile,'-','') LIKE '%" . trim($search_text) . "%' 
                        or bmobile LIKE '%" . trim($search_text) . "%'  
                        or odd.rmobile LIKE '%" . trim($search_text) . "%' 
                        or o.oid LIKE '%" . trim($search_text) . "%'
                        ) ";
        } else if ($search_type == "combi_name") {
            $where .= "and (bname LIKE '%" . trim($search_text) . "%'  or odd.rname LIKE '%" . trim($search_text) . "%') ";
        } else if ($search_type == "combi_cooid") {
            $where .= "and (od.co_oid = '" . trim($search_text) . "'  or od.co_od_ix = '" . trim($search_text) . "') ";
        } else if ($search_type == "combi_email") {
            $where .= "and (bmail LIKE '%" . trim($search_text) . "%'  or odd.rmail LIKE '%" . trim($search_text) . "%') ";
        } else if ($search_type == "combi_tel") {
            $where .= "and (REPLACE(btel,'-','') LIKE '%" . trim($search_text) . "%'  or REPLACE(odd.rtel,'-','') LIKE '%" . trim($search_text) . "%' or btel LIKE '%" . trim($search_text) . "%' or odd.rtel LIKE '%" . trim($search_text) . "%') ";
        } else if ($search_type == "combi_mobile") {
            $where .= "and ( REPLACE(bmobile,'-','') LIKE '%" . trim($search_text) . "%'  or REPLACE(odd.rmobile,'-','') LIKE '%" . trim($search_text) . "%' or bmobile LIKE '%" . trim($search_text) . "%'  or odd.rmobile LIKE '%" . trim($search_text) . "%' ) ";
        } else {
            if ($search_type == "o.bmobile" || $search_type == "odd.rmobile") {
                $where .= "and ( REPLACE(" . $search_type . ",'-','') LIKE '%" . trim($search_text) . "%' or " . $search_type . " LIKE '%" . trim($search_text) . "%' ) ";
            } else {
                $where .= "and $search_type LIKE '%" . trim($search_text) . "%' ";
            }
        }
        if ($search_type == "combi_mboid" || $search_type == "combi_name" || $search_type == "combi_email" || $search_type == "combi_tel" || $search_type == "combi_mobile" || substr_count($search_type, 'odd.') > 0) {
            $left_join .= " left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix) ";
        }

        if(substr_count($left_join,'shop_order_payment op')==0){
            if (substr_count($search_type, 'op.') > 0) {
                $left_join .= " left join shop_order_payment op on (op.oid=od.oid) ";
            }
        }
    }
}

if(is_array($order_from)){
    for($i = 0; $i < count($order_from); $i++){
        if($order_from[$i] != ""){
            if($order_from_str == ""){
                $order_from_str .= "'".$order_from[$i]."'";
            }else{
                $order_from_str .= ",'".$order_from[$i]."' ";
            }
        }
    }

    if($order_from_str != ""){
        $where .= "and od.order_from in ($order_from_str) ";
    }
}else{
    if($order_from){
        $where .= "and od.order_from = '$order_from' ";
    }
}

if(is_array($payment_agent_type)){
    for($i = 0; $i < count($payment_agent_type); $i++){
        if($payment_agent_type[$i] != ""){
            if($payment_agent_type_str == ""){
                $payment_agent_type_str .= "'".$payment_agent_type[$i]."'";
            }else{
                $payment_agent_type_str .= ", '".$payment_agent_type[$i]."' ";
            }
        }
    }

    if($payment_agent_type_str != ""){
        $where .= "and o.payment_agent_type in ($payment_agent_type_str) ";
    }
}else{
    if($payment_agent_type){
        $where .= "and o.payment_agent_type = '$payment_agent_type' ";
    }
}

if(is_array($delivery_status)){
    for($i = 0; $i < count($delivery_status); $i++){
        if($delivery_status[$i] != ""){
            if($delivery_status_str == ""){
                $delivery_status_str .= "'".$delivery_status[$i]."'";
            }else{
                $delivery_status_str .= ", '".$delivery_status[$i]."' ";
            }
        }
    }

    if($delivery_status_str != ""){
        $where .= "and od.delivery_status in ($delivery_status_str) ";
    }
}else{
    if($delivery_status){
        $where .= "and od.delivery_status = '$delivery_status' ";
    }
}

if($md_code != ""){
    $where .= "and od.md_code = '".$md_code."'";
}

if(is_array($p_admin) && count($p_admin) == 1){
    if($p_admin[0] == "A"){
        $where .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
    }else if($p_admin[0] == "S"){
        $where .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
    }
}else{
    if($p_admin == "A"){
        $where .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
    }else if($p_admin == "S"){
        $where .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
    }
}

if($stock_use_yn != ""){
    $where .= "and od.stock_use_yn = '".$stock_use_yn."'";
}

if($mall_ix != ""){
    $where .= "and od.mall_ix = '".$mall_ix."'";
}

if($gp_ix != ""){
    $where .= "and o.gp_ix = '".$gp_ix."'";
}

if(is_array($cid)){
    for($i = 0; $i < count($cid); $i++){
        if($cid[$i] != ""){
            if($cid_str == ""){
                $cid_str .= "'".$cid[$i]."'";
            }else{
                $cid_str .= ", '".$cid[$i]."' ";
            }
        }
    }

    if($cid_str != ""){
        $where .= "and od.cid in ($cid_str) ";
    }
}else{
    if($cid){
        $where .= "and od.cid = '$cid' ";
    }
}

if(!empty($payment_sprice) && !empty($payment_eprice)){
    $where .= " and o.payment_price between '".$payment_sprice."' and '".$payment_eprice."' ";
}

if(!empty($user_type)){
    if($user_type == 'Y'){
        $where .= " and o.user_code != '' ";
    }else{
        $where .= " and o.user_code = '' ";
    }
}
if($admininfo[admin_level] == 9){
    if($company_id != ""){
        $where .= " and o.oid = od.oid and  od.company_id = '".$company_id."'";
    }else{
        $where .= " and o.oid = od.oid ";
    }

    if($admininfo[mem_type] == "MD"){
        $where .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
    }
}else if($admininfo[admin_level] == 8){
    $where .= " and o.oid = od.oid and od.company_id = '".$admininfo[company_id]."'";
}else{
    $where .= " and o.oid = od.oid ";
}

if($view_type == 'offline_order'){		//영업관리 용도 2013-07-05 이학봉
    $where .= " and od.order_from in ('offline') ";
}else if($view_type == 'pos_order'){		//포스관리 용도 2013-07-05 이학봉
    $where .= " and od.order_from in ('pos') ";
}

if(!empty($product_type)){
    $where .=" and od.product_type = '".$product_type."' ";
}



	//************SMS *************
	if ($update_kind == "sms"){

		$cominfo = getcominfo();
		$sdb = new Database;
		$s = new SMS();
		$s->send_phone = $cominfo[com_phone];
		$s->send_name = $cominfo[com_name];
		$s->admin_mode = true;
		$s->send_type = $send_type;

		if($update_type == 2){// 선택회원일때

			if($db->dbms_type == "oracle"){
				$sql = "SELECT *,to_char(date_,'YY-MM-DD') as regdate FROM ".TBL_SHOP_ORDER." where oid in ('".implode("','",$oid)."') ";
			}else{
				$sql = "SELECT 
                          bname,bmobile,total_price,date_format(order_date,'%Y-%m-%d') as regdate 
                        FROM ".TBL_SHOP_ORDER." where oid in ('".implode("','",$oid)."') ";
			}

			$db->query($sql);


			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);

				$mc_sms_text = str_replace("{site}",$_SESSION["shopcfg"]["shop_name"],$sms_text);
				$mc_sms_text = str_replace("{orderDate}",$db->dt["regdate"],$mc_sms_text);
				$mc_sms_text = str_replace("{totalPrice}",(int)$db->dt["total_price"],$mc_sms_text);

//				if($db->dt["method"]==4)	$mc_sms_text = str_replace("{bank}",$db->dt["vb_info"],$mc_sms_text);
//				else									$mc_sms_text = str_replace("{bank}",$db->dt["bank"],$mc_sms_text);

//				if($send_target=='r'){
//					$mc_sms_text = str_replace("{name}",$db->dt["rname"],$mc_sms_text);
//					$s->dest_phone = trim(str_replace("-","",$db->dt["rmobile"]));
//					$s->dest_name = $db->dt["rname"];
//				}else{
					$mc_sms_text = str_replace("{name}",$db->dt["bname"],$mc_sms_text);
					$s->dest_phone = trim(str_replace("-","",$db->dt["bmobile"]));
					$s->dest_name = $db->dt["bname"];
//				}

				$s->msg_body =$mc_sms_text;

				if($s->dest_phone){
					$s->sendbyone($admininfo);
				}
			}

			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원 전체에게 SMS 가 발송 되었습니다.');parent.document.location.reload();</script>");
		}else{// 검색회원일때
            $max = $sms_max;
			if(!$max){
				$max = 100;
			}

			if ($sms_send_page == ''){
				$start = 0;
				$sms_send_page  = 1;
			}else{
				$start = ($sms_send_page - 1) * $max;
			}

			$sql = "SELECT count(distinct o.oid) as total
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				$where ";
			$db->query($sql);
			$db->fetch();
			$total = $db->dt[total];

			if($total != $search_total){
			    echo "<script>alert('검색대상 수량이 맞지 않습니다. 확인이 필요 합니다.')</script>";
			    exit;
            }

			if($db->dbms_type == "oracle"){
						$sql = "SELECT o.*,to_char(date_,'YY-MM-DD') as regdate
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						ORDER BY o.date_ DESC
						limit $start,$max  ";
			}else{
				$sql = "SELECT o.bname,o.bmobile,o.total_price,date_format(order_date,'%Y-%m-%d') as regdate
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						group by o.oid
						ORDER BY o.order_date DESC
						limit $start,$max  ";
			}

			$db->query($sql);

			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);
				
				$mc_sms_text = str_replace("{site}",$_SESSION["shopcfg"]["shop_name"],$sms_text);
				$mc_sms_text = str_replace("{orderDate}",$db->dt["regdate"],$mc_sms_text);
				$mc_sms_text = str_replace("{totalPrice}",(int)$db->dt["total_price"],$mc_sms_text);

//				if($db->dt["method"]==4)	$mc_sms_text = str_replace("{bank}",$db->dt["vb_info"],$mc_sms_text);
//				else									$mc_sms_text = str_replace("{bank}",$db->dt["bank"],$mc_sms_text);

//				if($send_target=='r'){
//					$mc_sms_text = str_replace("{name}",$db->dt["rname"],$mc_sms_text);
//					$s->dest_phone = trim(str_replace("-","",$db->dt["rmobile"]));
//					$s->dest_name = $db->dt["rname"];
//				}else{
					$mc_sms_text = str_replace("{name}",$db->dt["bname"],$mc_sms_text);
					$s->dest_phone = trim(str_replace("-","",$db->dt["bmobile"]));
					$s->dest_name = $db->dt["bname"];
//				}

				$s->msg_body =$mc_sms_text;

				if($s->dest_phone){
					$s->sendbyone($admininfo);
				}
			}

			if($total > ($start+$max)){
				echo("<script>
				parent.document.getElementById('sended_sms_cnt').innerHTML = '".($start+$max)."';
				parent.document.getElementById('remainder_sms_cnt').innerHTML = '".($total-($start+$max))."';
				if(!parent.document.forms['listform'].stop.checked){
					parent.document.forms['listform'].sms_send_page.value = ".($sms_send_page+1).";
					parent.document.forms['listform'].submit();
				}
				</script>");
			}else{
				echo("<script language='javascript' src='../_language/language.php'></script>
				<script>
				parent.document.getElementById('sended_sms_cnt').innerHTML = '".($total)."';
				parent.document.getElementById('remainder_sms_cnt').innerHTML = '0';
				alert('".$total." 건의 SMS 가 정상적으로 발송되었습니다');//건의 SMS 가 정상적으로 발송되었습니다
				</script>");
			}
		}
	}

	//************E-mail 발송 *************
	if ($update_kind == "sendemail"){
		include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");

		$cominfo = getcominfo();
		$db = new Database;
		$idb = new Database;

		$sql = "insert into ".TBL_SHOP_TMP." (mall_ix, design_tmp) values ";
		$sql .= " ( '".$admininfo[mall_ix]."', '$mail_content') ";
		$db->query($sql);

		$db->query("select design_tmp as mail_content from ".TBL_SHOP_TMP." where mall_ix ='".$admininfo[mall_ix]."' ");
		$db->fetch();
		$mail_content = $db->dt[mail_content];
		$db->query("delete from ".TBL_SHOP_TMP." where mall_ix ='".$admininfo[mall_ix]."' ");

		if($save_mail){
			$mail_info[mail_content] = $mail_content;
			$mail_info[mail_subject] = $email_subject;
			$mail_info[mail_ix] = $_POST[mail_ix];

			$mail_ix = mail_box("insert", $mail_info);
		}else{
			$mail_ix = $_POST[mail_ix];
		}


		if($update_type == 2){// 선택회원일때
			
			if($db->dbms_type == "oracle"){
				$sql = "SELECT *,to_char(date_,'YY-MM-DD') as regdate FROM ".TBL_SHOP_ORDER." where oid in ('".implode("','",$oid)."') ";
			}else{
				$sql = "SELECT *,date_foramt(date,'%Y-%m-%d') as regdate FROM ".TBL_SHOP_ORDER." where oid in ('".implode("','",$oid)."') ";
			}

			$db->query($sql);

			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);

				$mail_subject = str_replace("{mem_name}",$db->dt[user_name],$email_subject);

				if($send_target=='r'){
					$mail_info[mem_name] = $db->dt["rname"];
					$mail_info[mem_mail] = $db->dt["rmail"];
				}else{
					$mail_info[mem_name] = $db->dt["bname"];
					$mail_info[mem_mail] = $db->dt["bmail"];
				}

				$tmp_email = explode('@',$mail_info[mem_mail]);

				if (ValidateDNS($tmp_email[1])){

					$mail_info[mem_id] = $db->dt[buserid];

					if($i==0)	$mail_info[mail_cc] = $mail_cc;
					else			$mail_info[mail_cc] = "";

					$check_key = md5(uniqid());
					$__mail_content = $mail_content."<P style='DISPLAY: none'><IMG src='http://".$HTTP_HOST."/mailling/mail_open.php?check_key=$check_key'></P>";

					if (SendMail($mail_info, $mail_subject,$__mail_content,"","","Y")){//SendMail 함수에 전달인자 값이 추가되었기에 여기서도 추가해야 사용자한테 발송됨 kbk 13/09/17

						$sql = "insert into shop_mailling_history
								(mh_ix,mail_ix,ucode, sended_mail, check_key, regdate)
								values
								('','".$mail_ix."','".$db->dt[uid]."','".$mail_info[mem_mail]."','$check_key', NOW())";
						//echo $sql;
						$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
						$idb->query($sql);
					}else{
						//$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일 발송이 실패했습니다.<br>";
						$sql = "insert into shop_mailling_history
								(mh_ix,mail_ix,ucode, sended_mail, check_key, is_error, error_text, regdate)
								values
								('','".$mail_ix."','".$db->dt[uid]."','".$mail_info[mem_mail]."','$check_key','1','SEND_ERROR', NOW())
								";
						//echo $sql;
						$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
						$idb->query($sql);
					}

				//echo $message;
				}else{
						$sql = "insert into shop_mailling_history
							(mh_ix,mail_ix,ucode, sended_mail, check_key, is_error, error_text, regdate)
							values
							('','".$mail_ix."','".$db->dt[uid]."','".$mail_info[mem_mail]."','$check_key','1','DNS_ERROR', NOW())
							";
						//echo $sql;
						$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
						$idb->query($sql);
					//echo "DNS ERROR";
				}
			}

			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택주문에 E-mail이 정상적으로 발송되었습니다');</script>");
		}else{// 검색회원일때
			if(!$email_max){
				$email_max = 100;
			}
			if ($email_send_page == ''){
				$start = 0;
				$email_send_page  = 1;
			}else{
				$start = ($email_send_page - 1) * $email_max;
			}
			
			$sql = "SELECT count(distinct o.oid) as total
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				$where ";
			$db->query($sql);
			$db->fetch();
			$total = $db->dt[total];

			if($db->dbms_type == "oracle"){
						$sql = "SELECT o.*,to_char(date_,'YY-MM-DD') as regdate
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						ORDER BY o.date_ DESC
						limit $start,$email_max  ";
			}else{
				$sql = "SELECT o.*,date_foramt(date,'%Y-%m-%d') as regdate
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						ORDER BY o.date DESC
						limit $start,$email_max  ";
			}

			$db->query($sql);

			$send_cnt =0;
			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);

				$mail_subject = str_replace("{mem_name}",$db->dt[name],$email_subject);
				
				if($send_target=='r'){
					$mail_info[mem_name] = $db->dt["rname"];
					$mail_info[mem_mail] = $db->dt["rmail"];
				}else{
					$mail_info[mem_name] = $db->dt["bname"];
					$mail_info[mem_mail] = $db->dt["bmail"];
				}
				
				$tmp_email = explode('@',$mail_info[mem_mail]);

				if (ValidateDNS($tmp_email[1])){
					
					$mail_info[mem_id] = $db->dt[buserid];

					if($i==0)	$mail_info[mail_cc] = $mail_cc;
					else			$mail_info[mail_cc] = "";

					$check_key = md5(uniqid());
					$mail_content = str_replace("{check_key}",$check_key,$mail_content);
					$__mail_content = $mail_content."<P style='DISPLAY: none'><IMG src='http://".$HTTP_HOST."/mailling/mail_open.php?check_key=$check_key'></P>";

					//if (SendMail($mail_info, $mail_subject,$__mail_content,"")){
					if (SendMail($mail_info, $mail_subject,$__mail_content,"","","Y")){//SendMail 함수에 전달인자 값이 추가되었기에 여기서도 추가해야 사용자한테 발송됨 kbk 13/09/17
						//$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일이 발송되었습니다.<br>";
						$sql = "insert into shop_mailling_history
								(mh_ix,mail_ix,ucode, sended_mail, check_key, regdate)
								values
								('','".$mail_ix."','".$db->dt[uid]."','".$mail_info[mem_mail]."','$check_key', NOW())";
						//echo $sql;
						$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
						$idb->query($sql);
					}else{
						//$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일 발송이 실패했습니다.<br>";
						$sql = "insert into shop_mailling_history
								(mh_ix,mail_ix,ucode, sended_mail, check_key,is_error, error_text, regdate)
								values
								('','".$mail_ix."','".$db->dt[uid]."','".$mail_info[mem_mail]."','$check_key', '1','SEND_ERROR', NOW())";
						//echo $sql;
						$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
						$idb->query($sql);
					}

					$send_cnt++;
					//echo $message;
				}else{
						$sql = "insert into shop_mailling_history
							(mh_ix,mail_ix,ucode, sended_mail, check_key, is_error, error_text, regdate)
							values
							('','".$mail_ix."','".$db->dt[uid]."','".$mail_info[mem_mail]."','$check_key','1','DNS_ERROR', NOW())
							";
						//echo $sql;
						$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
						$idb->query($sql);
					echo "DNS ERROR";
				}
			}

			if($total > ($start+$email_max)){
				echo("<script>
				parent.document.getElementById('sended_email_cnt').innerHTML = '".($start+$email_max)."';
				parent.document.getElementById('remainder_email_cnt').innerHTML = '".($total-($start+$email_max))."';
				if(!parent.document.forms['listform'].stop.checked){
					parent.document.forms['listform'].email_send_page.value = ".($email_send_page+1).";
					parent.document.forms['listform'].submit();
				}
				</script>");
			}else{
				if($send_cnt){
					echo("<script language='javascript' src='../_language/language.php'></script>
					<script>
					parent.document.getElementById('sended_email_cnt').innerHTML = '".(($email_send_page-1)*$email_max + $send_cnt)."';
					parent.document.getElementById('remainder_email_cnt').innerHTML = '0';
					alert('".(($email_send_page-1)*$email_max + $send_cnt)." '+language_data['member_batch.act.php']['J'][language]);//건의 이메일이 정상적으로 발송되었습니다
					</script>");
				}else{
					echo("<script language='javascript' src='../_language/language.php'></script>
					<script>
					parent.document.getElementById('sended_email_cnt').innerHTML = '".($send_cnt)."';
					parent.document.getElementById('remainder_email_cnt').innerHTML = '0';
					alert(language_data['member_batch.act.php']['K'][language]);//'발송대상이 존재하지 않습니다. 메일링 수신거부 회원은 메일링 대상이 아닙니다. '
					</script>");
				}
			}
		}

	}

	function ValidateDNS($host)
	{
		return (checkdnsrr($host, ANY))? true: false;
	}


?>