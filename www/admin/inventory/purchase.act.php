<?
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");
include_once("./inventory.lib.php");

$db = new Database;

if($act=="get_company_safestock"){
	$db->query("select ifnull(sum(safestock),0) as safestock from inventory_goods_safestock where company_id='".$company_id."' and gid='".$gid."' and unit='".$unit."' ");
	$db->fetch("object");
	echo json_encode($db->dt);
	exit;
}

if($act=="get_goods_barcode"){
	$sql="select 
				g.*,gu.*, sum(ips.stock) as stock , ccd.company_id as ci_ix, ccd.com_name as ci_name
			from 
				inventory_goods g 
				left join common_company_detail ccd on (ccd.company_id=g.ci_ix) ,
				inventory_goods_unit gu
				left join inventory_product_stockinfo ips on (ips.gid=gu.gid and ips.unit=gu.unit and ips.company_id='".$company_id."')
			where
				g.gid=gu.gid and gu.barcode='".$barcode."' ";

	$db->query($sql);
	$db->fetch("object");
	if($db->total){
		$db->dt["unit_text"] = $ITEM_UNIT[$db->dt["unit"]];
		echo json_encode($db->dt);
	}
	exit;
}


if($act=="order_detail_tmp_insert"){

	$sql="select 
			gu.gu_ix, ccd.company_id as ci_ix, ccd.com_name as ci_name, gu.buying_price , sum(ips.stock) as stock
		from 
			inventory_goods g 
			left join inventory_goods_unit gu on (g.gid=gu.gid) 
			left join inventory_product_stockinfo ips on (ips.company_id='".$_SESSION["admininfo"]["company_id"]."' and ips.gid=gu.gid and ips.unit=gu.unit)
			left join common_company_detail ccd on (ccd.company_id=g.ci_ix) 
		where 
			g.gid='".$gid."' and gu.unit='".$unit."'  ";
	$db->query($sql);
	$db->fetch("object");
	
	$gu_ix = $db->dt["gu_ix"];
	$ci_ix = $db->dt["ci_ix"];
	$ci_name = $db->dt["ci_name"];
	$buying_price = $db->dt["buying_price"];
	$stock = $db->dt["stock"];

	$db->query("insert into inventory_order_detail_tmp (iodt_ix,ci_ix,ci_name,gid,gu_ix,unit,change_amount,gname,standard,cnt,company_id,com_name,charger_ix,charger,order_yn,regdate) values('','$ci_ix','$ci_name','$gid','$gu_ix','$unit','$change_amount','$gname','$standard','1','".$_SESSION["admininfo"]["company_id"]."','".$_SESSION["admininfo"]["company_name"]."','".$_SESSION["admininfo"]["charger_ix"]."','".$_SESSION["admininfo"]["charger"]."','N',NOW())");
	
	$db->query("select * from inventory_order_detail_tmp where iodt_ix = (select last_insert_id())");
	$db->fetch("object");
	$db->dt["stock"] = $stock;
	$db->dt["unit_text"] = $ITEM_UNIT[$db->dt["unit"]];
	$db->dt["buying_price"] = $buying_price;
	echo json_encode($db->dt);
	exit;
}

if($act=="order_detail_tmp_delete"){
	$db->query("delete from inventory_order_detail_tmp where iodt_ix in ('".implode("','",$iodt_ix)."')");
	exit;
}

if($act=="status_update_pop"){
	
	if($delivery_type=="A"){
		$sql="select ccd.com_name, pi.place_name from common_company_detail ccd , inventory_place_info pi where ccd.company_id='".$company_id."' and pi.pi_ix='".$pi_ix."' ";
		$db->query($sql);
		$db->fetch();
		$delivery_name= $db->dt["com_name"]." > ".$db->dt["place_name"];
	}else{//외부 직배송
		$company_id="";
		$pi_ix="";
	}

	//$delivery_zip = $delivery_zip1."-".$delivery_zip2;
	if($delivery_zip2 != "" || $delivery_zip2 != NULL){
		$delivery_zip = "$delivery_zip1-$delivery_zip2";
	}else{
		$delivery_zip = $delivery_zip1;
	}
	$sql="update inventory_order set 
		company_id='".$company_id."',
		pi_ix='".$pi_ix."',
		delivery_name='".$delivery_name."',
		delivery_zip='".$delivery_zip."',
		delivery_addr1='".$delivery_addr1."',
		delivery_addr2='".$delivery_addr2."',
		msg='".$msg."',
		status='".$status."',
		".($status=="OR" ? "or_date=NOW()," : "")."
		editdate=NOW()
	where ioid = '".$ioid."' ";
	$db->query($sql);

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 ".$inventory_order_status[$status]."변경 되었습니다.');parent.opener.document.location.reload();parent.self.close();</script>";
	exit;
}

if($act=="order_ready"){

	$ioid = "1".substr(date("YmdHis"),1)."-".rand(10000, 99999);

	foreach($item_infos as $info){
		
		list($gid,$unit)=explode("|",$info["gid_unit"]);

		$sql="select * from inventory_goods_unit where gid='".$gid."' and unit='".$unit."' ";
		$db->query($sql);
		$db->fetch();
		$gu_ix = $db->dt["gu_ix"];
		
		$buying_price=$info["buy_price"]*$info["cnt"];
		$tax_price=round($buying_price/1.1);
		$coprice=$buying_price-$tax_price;

		$sql="insert into inventory_order_detail (iod_ix,ioid,gid,gu_ix,unit,gname,standard,buy_price,cnt,coprice,tax_price,buying_price,regdate) values('','".$ioid."','".$gid."','".$gu_ix."','".$unit."','".$info["gname"]."','".$info["standard"]."','".$info["buy_price"]."','".$info["cnt"]."','".$coprice."','".$tax_price."','".$buying_price."',NOW())";
		$db->query($sql);

		$goods_price+=$buying_price*$info["cnt"];
		$goods_cnt+=$info["cnt"];
	}

	if($delivery_type=="A"){
		$sql="select ccd.com_name, pi.place_name from common_company_detail ccd , inventory_place_info pi where ccd.company_id='".$company_id."' and pi.pi_ix='".$pi_ix."' ";
		$db->query($sql);
		$db->fetch();
		$delivery_name= $db->dt["com_name"]." > ".$db->dt["place_name"];
	}else{//외부 직배송
		$company_id="";
		$pi_ix="";
	}
	
	//$delivery_zip = $delivery_zip1."-".$delivery_zip2;
	if($delivery_zip2 != "" || $delivery_zip2 != NULL){
		$delivery_zip = "$delivery_zip1-$delivery_zip2";
	}else{
		$delivery_zip = $delivery_zip1;
	}

	
	$total_price=$goods_price+$delivery_price;

	$sql="insert into inventory_order (ioid,ci_ix,ci_name,limit_date,company_id,pi_ix,delivery_name,delivery_zip,delivery_addr1,delivery_addr2,msg,goods_price,goods_cnt,delivery_price,total_price,status,charger_ix,charger,or_date,editdate,regdate) values ('$ioid','$ci_ix','$com_name','$limit_date','$company_id','$pi_ix','$delivery_name','$delivery_zip','$delivery_addr1','$delivery_addr2','$msg','$goods_price','$goods_cnt','$delivery_price','$total_price','OR','$charger_ix','$charger',NOW(),NOW(),NOW())";
	$db->query($sql);

	if(!$_POST["is_continue"]){
		$reload_str = "parent.document.location.reload();";
	}

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 발주예정에 등록 되었습니다.');".$reload_str."</script>";
}


if($search_searialize_value){
	$unserialize_search_value = unserialize(urldecode($search_searialize_value));
	extract($unserialize_search_value);
}

if($act=="order_applay_complete"){

	if($update_type=="1"){//검색한 상품일때!
		$db->query("SELECT * FROM common_company_detail where com_type = 'A'");
		$db->fetch();
		$a_company_id = $db->dt[company_id];

		//본사 직원이 아닐경우! 사업장은 자기 사업장만!
		if($_SESSION["admininfo"]["company_id"]!=$a_company_id){
			$company_id=$_SESSION["admininfo"]["company_id"];
		}

		$where="";

		if($company_id!=""){
			$where.=" and odt.company_id='".$company_id."' ";
		}

		if($search_company_id){
			$where.=" and odt.company_id='".$search_company_id."' ";
		}

		if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
			//다중검색 시작 2014-04-10 이학봉
			if($search_text != ""){
				if(strpos($search_text,",") !== false){
					$search_array = explode(",",$search_text);
					$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
					$where .= "and ( ";
					$count_where .= "and ( ";
					for($i=0;$i<count($search_array);$i++){
						$search_array[$i] = trim($search_array[$i]);
						if($search_array[$i]){
							if($i == count($search_array) - 1){
								$where .= $search_type." = '".trim($search_array[$i])."'";
								$count_where .= $search_type." = '".trim($search_array[$i])."'";
							}else{
								$where .= $search_type." = '".trim($search_array[$i])."' or ";
								$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
							}
						}
					}
					$where .= ")";
					$count_where .= ")";
				}else if(strpos($search_text,"\n") !== false){//\n
					$search_array = explode("\n",$search_text);
					$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
					$where .= "and ( ";
					$count_where .= "and ( ";

					for($i=0;$i<count($search_array);$i++){
						$search_array[$i] = trim($search_array[$i]);
						if($search_array[$i]){
							if($i == count($search_array) - 1){
								$where .= $search_type." = '".trim($search_array[$i])."'";
								$count_where .= $search_type." = '".trim($search_array[$i])."'";
							}else{
								$where .= $search_type." = '".trim($search_array[$i])."' or ";
								$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
							}
						}
					}
					$where .= ")";
					$count_where .= ")";
				}else{
					$where .= " and ".$search_type." = '".trim($search_text)."'";
					$count_where .= " and ".$search_type." = '".trim($search_text)."'";
				}
			}

		}else{	//검색어 단일검색
			if($search_text != ""){
				if($search_type == "gname_gid"){
					$where .= "and (odt.gname LIKE '%".$search_text."%' or odt.gid LIKE '%".$search_text."%') ";
				}else{
					$where .= "and ".$search_type." LIKE '%".$search_text."%' ";
				}
			}
		}

		$sql="select odt.* from 
			inventory_order_detail_tmp odt 
			where odt.order_yn ='N' $where ";

		$db->query($sql);
		$odt_infos = $db->fetchall("object");
	}

	if($update_kind=="update_ci_ix"){//매입처 변경

		if($update_type=="1"){//검색한 상품일때!
			$iodt_ix="";
			foreach($odt_infos as $oi){
				$iodt_ix[]=$oi["iodt_ix"];
			}
		}

		$sql="update  inventory_order_detail_tmp set ci_ix='".$ci_ix."', ci_name='".$ci_name."' where iodt_ix in ('".implode("','",$iodt_ix)."') ";
		$db->query($sql);
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 매입처가 변경 되었습니다.');parent.document.location.reload();</script>";
		exit;
	}elseif($update_kind=="applay_complete"){

		if($update_type=="2"){//선택한 상품일때 상품일때!
			$sql="select odt.* from 
			inventory_order_detail_tmp odt 
			where odt.order_yn ='N' and odt.iodt_ix in ('".implode("','",$iodt_ix)."') ";
			$db->query($sql);
			$odt_infos = $db->fetchall("object");
		}
		
		$ci_info=array();
		foreach($odt_infos as $oi){
			if($oi["ci_ix"]!=""){//매입처가 선택되어있을경우에만!
				if(empty($ci_info[$oi["ci_ix"]]["ioid"])){
					$ci_info[$oi["ci_ix"]]["ioid"]="1".substr(date("YmdHis"),1)."-".rand(10000, 99999);
					$ci_info[$oi["ci_ix"]]["ci_name"]=$oi["ci_name"];
				}
				
				$BUY_PRICE=$buy_price[$oi["iodt_ix"]];
				$CNT=$cnt[$oi["iodt_ix"]];
				$BUYING_PRICE=$BUY_PRICE*$CNT;
				$COPRICE=$BUYING_PRICE/1.1;
				$TAX_PRICE=$BUYING_PRICE-$COPRICE;

				$sql="insert into inventory_order_detail (iod_ix,ioid,gid,gu_ix,unit,gname,standard,buy_price,cnt,coprice,tax_price,buying_price,regdate) values ('','".$ci_info[$oi["ci_ix"]]["ioid"]."','".$oi["gid"]."','".$oi["gu_ix"]."','".$oi["unit"]."','".$oi["gname"]."','".$oi["standard"]."','".$BUY_PRICE."','".$CNT."','".$COPRICE."','".$TAX_PRICE."','".$BUYING_PRICE."',NOW())";
				$db->query($sql);

				$sql="update  inventory_order_detail_tmp set ioid='".$ci_info[$oi["ci_ix"]]["ioid"]."', order_yn='Y' where iodt_ix = '".$oi["iodt_ix"]."' ";
				$db->query($sql);

				$ci_info[$oi["ci_ix"]]["goods_price"]+=$BUYING_PRICE;
				$ci_info[$oi["ci_ix"]]["goods_cnt"]+=$CNT;
			}
		}
		
		if($delivery_type=="A"){
			$sql="select ccd.com_name, pi.place_name from common_company_detail ccd , inventory_place_info pi where ccd.company_id='".$company_id."' and pi.pi_ix='".$pi_ix."' ";
			$db->query($sql);
			$db->fetch();
			$delivery_name= $db->dt["com_name"]." > ".$db->dt["place_name"];
		}else{//외부 직배송
			$company_id="";
			$pi_ix="";
		}

		//$delivery_zip = $delivery_zip1."-".$delivery_zip2;
		if($delivery_zip2 != "" || $delivery_zip2 != NULL){
			$delivery_zip = "$delivery_zip1-$delivery_zip2";
		}else{
			$delivery_zip = $delivery_zip1;
		}
		foreach($ci_info as $ci_ix => $info){

			$goods_price=$ci_info[$ci_ix]["goods_price"];
			$goods_cnt=$ci_info[$ci_ix]["goods_cnt"];
			$total_price=$goods_price+$delivery_price;

			$sql="insert into inventory_order (ioid,ci_ix,ci_name,limit_date,company_id,pi_ix,delivery_name,delivery_zip,delivery_addr1,delivery_addr2,msg,goods_price,goods_cnt,delivery_price,total_price,status,charger_ix,charger,ac_date,editdate,regdate) values ('".$info["ioid"]."','".$ci_ix."','".$info["ci_name"]."','".$limit_date."','".$company_id."','".$pi_ix."','".$delivery_name."','".$delivery_zip."','".$delivery_addr1."','".$delivery_addr2."','".$msg."','".$goods_price."','".$goods_cnt."','".$delivery_price."','".$total_price."','AC','".$charger_ix."','".$charger."',NOW(),NOW(),NOW())";
			$db->query($sql);
		}
		
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 청구요청 확정으로 변경 되었습니다.');parent.document.location.reload();</script>";
		exit;
	}
}


if($act=="status_update" || $act=="send_email" || $act=="send_sms" || $act=="all_stocked"){

	if($update_type=="1"){//검색한 상품
		$where=" where ioid !='' and status not in ('ACC','ORC','OCC') ";
		
		if(!$date_type){
			$date_type = $PAGE_INFO["date"];
		}
		
		if($orderdate){
			$where .= " and date_format(".$date_type.",'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
		}
		
		if($company_id){
			$where .= " and company_id = '".$company_id."' ";
		}
		
		if($pi_ix){
			$where .= " and pi_ix = '".$pi_ix."' ";
		}

		if($ci_ix){
			$where .= " and ci_ix = '".$ci_ix."' ";
		}
		
		if(is_array($status)){
			$where .= " and status in ('".implode("','",$status)."') ";
		}elseif($status){
			$where .= " and status = '".$status."' ";
		}

		if($search_type && $search_text){
			$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
		}

		$sql = "select * from inventory_order $where";
		$db->query($sql);
		$iorder = $db->fetchall("object");

		foreach($iorder as $io){
			$ioid[]=$io["ioid"];
		}
	}
}

if($act=="status_update"){

	$update_str="";

	if($u_status=="OR"){
		$update_str=" or_date=NOW(), ";
	}elseif($u_status=="OC"){
		$update_str=" oc_date=NOW(), ";
	}
	
	$db->query("update inventory_order set status='".$u_status."', $update_str editdate=NOW() where ioid in ('".implode("','",$ioid)."')");
	
	if($mmode=="pop"){
		$script="parent.opener.document.location.reload();parent.self.close();";
	}else{
		$script="parent.document.location.reload();";
	}
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 ".$inventory_order_status[$u_status]."변경 되었습니다.');".$script."</script>";
	exit;
}

if($act=="send_email"){

	$cominfo = getcominfo();
	//테스트메일서버 DB연결

	for($i=0;$i<count($ioid);$i++){
		
		$check_key = md5(uniqid());
		
		$db = new Database;

		$sql="select io.*,ccd.customer_name,ccd.customer_mail from inventory_order io left join common_company_detail ccd on (io.ci_ix=ccd.company_id) where io.ioid='".$ioid[$i]."'";
		$db->query($sql);
		$db->fetch();

		$mail_code=$ioid[$i];
		$sended_mail=$db->dt['customer_mail'];
		$mem_name=$db->dt['customer_name'];
		//$sended_mail="hong861114@nate.com";
		//$mem_name="홍진영";

		 $Tag = curl_init();
		curl_setopt( $Tag , CURLOPT_URL , $_SERVER['HTTP_HOST']."/admin/inventory/purchase_detail.php?ioid=".$ioid[$i]."&view_type=email" ); 
		ob_start();
		curl_exec( $Tag );
		curl_close( $Tag );
		$results = ob_get_contents();
		ob_clean();
		
		$mail_content=str_replace("'","\"",$results);
		$mail_subject=$cominfo['com_name']."에서 보낸 발주내역입니다.";

		$mail_code_text	=	"<P style=\'DISPLAY:none\'><IMG src=\'http://".$HTTP_HOST."/mailling/mail_open.php?check_key=$check_key\'></P>";
		//이메일 본분내용(작성한내용+메일코드+이메일수신거부)
		$__mail_content = "<table width=\'800\' cellpadding=\'0\' cellspacing=\'0\' border=\'0\' style=\'margin:0 auto\'>".$mail_content.$mail_code_text."</table>";
		
		$sql = "update inventory_order set
		email_send='Y',
		email_date=NOW()
		where ioid='".$ioid[$i]."' ";
		$db->query($sql);

		$sql = "
		insert into shop_mailling_history
		(mh_ix,mail_ix, mail_code, mail_sendtype ,ucode, sended_mail, check_key, regdate)
		values
		('','', '".$mail_code."' ,'0' ,'','".$sended_mail."','$check_key', NOW())";
		$db->sequences = "SHOP_MAILLING_HISTORY_SEQ";
		$db->query($sql);
		
		$idb = new Database("115.68.24.80","demo","demo11","tm001");

		$sql = "
		INSERT INTO customer_info
		(mh_ix,user_id,title,content,sender,sender_alias,receiver_alias,send_time,file_name,file_contents,wasRead,wasSend,wasComplete,needRetry,regist_date)
		VALUES
		(last_insert_id(),'forbiz','$mail_subject','$__mail_content','".$cominfo[com_email]."','".$cominfo[shop_name]."','".$mem_name."',NOW(),'','','X','X','X','X',now())";
		$idb->query($sql);

		$sql = "
		INSERT INTO customer_data
		(id,email,first)
		VALUES
		(LAST_INSERT_ID(),'".$sended_mail."','".$mem_name."')";
		$idb->query($sql);
	}

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 이메일 보냈습니다.');parent.document.location.reload();</script>";
	exit;
}


if($act=="send_sms"){
	
	include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

	$cominfo = getcominfo();

	$s = new SMS();
	$s->send_phone = $cominfo[com_phone];
	$s->send_name = $cominfo[com_name];
	$s->admin_mode = true;
	$s->send_type = "M";

	for($i=0;$i<count($ioid);$i++){


		$sql="select io.*,ccd.customer_name,ccd.customer_mobile from inventory_order io left join common_company_detail ccd on (io.ci_ix=ccd.company_id) where io.ioid='".$ioid[$i]."'";
		$db->query($sql);
		$db->fetch();

		$customer_mobile=$db->dt['customer_mobile'];
		$mem_name=$db->dt['customer_name'];

		//$customer_mobile="010-3887-4023";
		//$mem_name="홍진영";

		$mc_sms_text = $cominfo[com_name]."에서 발주 (".$ioid[$i].") 를 요청하였습니다. ";

		$s->dest_phone = str_replace("-","",$customer_mobile);
		$s->dest_name = $mem_name;
		$s->msg_body =$mc_sms_text;
		$s->sendbyone($admininfo);


		$sql = "update inventory_order set
		sms_send='Y',
		sms_date=NOW()
		where ioid='".$ioid[$i]."' ";
		$db->query($sql);

	}

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 SMS를 보냈습니다.');parent.document.location.reload();</script>";
	exit;
}


if($act=="part_stocked"){
	//echo "<pre>";
	//$db->debug = true;
	foreach($item_infos as $info){
		if($info[iod_ix]!=""){// && ($info[real_cnt]+$info[cancel_cnt]) > 0

			//if($info[real_cnt] > 0){
			if($info[real_cnt] != ""){
				inventory_order_stock_input($ioid,$info[b_iod_ix],$info[real_cnt]);
			}

			if($info[iod_ix]=="clone"){
				$sql="INSERT INTO inventory_order_detail 
				SELECT 
					'',ioid,gid,gu_ix,unit,gname,standard,buy_price,'".$info[remain_cnt]."',round(buy_price*'".$info[remain_cnt]."'/1.1),((buy_price*'".$info[remain_cnt]."')-round(buy_price*'".$info[remain_cnt]."'/1.1)),(buy_price*'".$info[remain_cnt]."'),'".$info[cancel_cnt]."','".$info[expiry_date]."','".$info[real_cnt]."',regdate
				FROM inventory_order_detail WHERE iod_ix='".$info[b_iod_ix]."' ";
				$db->query($sql);
				
				$sql="UPDATE inventory_order_detail SET 
					cnt = cnt -'".$info[remain_cnt]."',
					coprice = coprice - round(buy_price*'".$info[remain_cnt]."'/1.1),
					tax_price = tax_price - ((buy_price*'".$info[remain_cnt]."')-round(buy_price*'".$info[remain_cnt]."'/1.1)),
					buying_price = buying_price - (buy_price*'".$info[remain_cnt]."')
				WHERE 
					iod_ix='".$info[b_iod_ix]."'";
				
				$db->query($sql);
			}else{
				$sql="update inventory_order_detail set real_cnt=real_cnt+'".$info[real_cnt]."', cancel_cnt=cancel_cnt+'".$info[cancel_cnt]."', expiry_date='".$info[expiry_date]."'  where iod_ix='".$info[iod_ix]."' ";
				
				$db->query($sql);
			}
		}
	}
	
	check_inventory_order_wc($ioid);
 
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리 되었습니다.');parent.document.location.reload();</script>";
	exit;
}

if($act=="all_stocked"){

	for($i=0;$i<count($ioid);$i++){
		
		$sql="select * from inventory_order_detail where ioid='".$ioid[$i]."' and (cnt-real_cnt-cancel_cnt) > 0 ";
		$db->query($sql);
	
		$od_info=$db->fetchall("object");

		foreach($od_info as $oi){
			$remain_cnt = $oi["cnt"]-$oi["real_cnt"]-$oi["cancel_cnt"];
			
			if($remain_cnt > 0){
				inventory_order_stock_input($ioid[$i],$oi["iod_ix"],$remain_cnt);
			}
			
			$sql="update inventory_order_detail set real_cnt= real_cnt+'".$remain_cnt."' where ioid='".$ioid[$i]."' and iod_ix='".$oi["iod_ix"]."' ";
			$db->query($sql);
		}

		check_inventory_order_wc($ioid[$i]);
	}
	
	

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('발주입고완료 처리 되었습니다.');parent.document.location.reload();</script>";
	exit;
}


if($act == 'upload_excel'){	//

	include '../include/phpexcel/Classes/PHPExcel.php';
	include("../include/lib/pclzip.lib.php");

	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	if ($excel_file_size > 0){
		copy($excel_file, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
	}

	$objPHPExcel = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);

	$shift_num = 0;

	// 데이터는 2줄부터 시작
	$rownum = 2;

	//$columnVar = $objPHPExcel->setActiveSheetIndex(0)->getCell('A' . ($rownum))->getValue();

	include("../logstory/class/sharedmemory.class");
	$shmop = new Shared("purchase_apply_".$_SESSION["admininfo"]["charger_ix"]);
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_shared/";
	//echo $shmop->filepath;
	$shmop->SetFilePath();
	$upload_excel_data = $shmop->getObjectForKey("purchase_apply_".$_SESSION["admininfo"]["charger_ix"]);
	
	$upload_excel_data="";

	$z=0;
	while(($objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue() != "") && ($rownum < 5000)) {
		$col = 'A';

			$gid = $objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue();
			$unit = '1';
			$excel_stock = $objPHPExcel->getActiveSheet()->getCell('B' . $rownum)->getValue();

			$sql="select 
					gu.gu_ix, g.gname,gu.change_amount,g.standard,
					ccd.company_id as ci_ix,
					ccd.com_name as ci_name, 
					gu.buying_price , 
					sum(ips.stock) as stock
				from 
					inventory_goods g 
					left join inventory_goods_unit gu on (g.gid=gu.gid) 
					left join inventory_product_stockinfo ips on (ips.company_id='".$_SESSION["admininfo"]["company_id"]."' and ips.gid=gu.gid and ips.unit=gu.unit)
					left join common_company_detail ccd on (ccd.company_id=g.ci_ix) 
				where 
					g.gid='".$gid."' and gu.unit='".$unit."'  ";
			$db->query($sql);
			$db->fetch("object");
			
			$gu_ix = $db->dt["gu_ix"];
			$ci_ix = $db->dt["ci_ix"];
			$ci_name = $db->dt["ci_name"];
			$buying_price = $db->dt["buying_price"];
			$stock = $db->dt["stock"];
			$change_amount = $db->dt["change_amount"];
			$gname = $db->dt["gname"];
			$standard = $db->dt["standard"];

			$db->query("insert into inventory_order_detail_tmp (iodt_ix,ci_ix,ci_name,gid,gu_ix,unit,change_amount,gname,standard,cnt,company_id,com_name,charger_ix,charger,order_yn,regdate) values('','$ci_ix','$ci_name','$gid','$gu_ix','$unit','$change_amount','$gname','$standard','$excel_stock','".$_SESSION["admininfo"]["company_id"]."','".$_SESSION["admininfo"]["company_name"]."','".$_SESSION["admininfo"]["charger_ix"]."','".$_SESSION["admininfo"]["charger"]."','N',NOW())");

			$col++;
		$z++;
		$rownum++;
	}

	$shmop->setObjectForKey($upload_excel_data, "purchase_apply_".$_SESSION["admininfo"]["charger_ix"]) ;

	//echo "<script language='javascript' src='../js/message.js.php'></script><script>top.location.href='./purchase_apply.php?up_mode=excel_upload'</script>";

}


function inventory_order_stock_input($ioid,$iod_ix,$amount){
	global $db;

	$sql = "select od.gid, od.unit, od.standard,
	od.buy_price as price ,
	o.company_id as company_id,
	o.pi_ix as pi_ix,
	ps.ps_ix as ps_ix,
	'".$amount."' as amount
	from inventory_order_detail od
	left join inventory_order o on (o.ioid=od.ioid)
	left join inventory_place_info pi on (pi.pi_ix=o.pi_ix)
	left join inventory_place_section ps on (ps.pi_ix=pi.pi_ix and ps.section_type='S')
	where  od.iod_ix = '".$iod_ix."' ";
	$db->query($sql);
	$delivery_iteminfo = $db->fetchall("object");

	if($delivery_iteminfo[0][ps_ix]!=""){

		$item_info["act_from"] = "inventory";
		//$item_info[pi_ix] = $order_item_info[pi_ix];
		//$item_info[ps_ix] = $order_item_info[ps_ix];
		//$item_info[company_id] = $order_item_info[company_id];
		$item_info[h_div] = "1"; // 1:입고 2: 출고
		$item_info[vdate] = date("Ymd");
		$item_info[ioid] = $ioid;
		$item_info[msg] = "발주 - 부분입고".($msg ? " [".$msg."]" : "");//$_POST["etc"];
		$item_info[h_type] = '01';//01; 상품매입
		$item_info[charger_name] = $_SESSION["admininfo"]["charger"];
		$item_info[charger_ix] = $_SESSION["admininfo"]["charger_ix"];
		$item_info[detail] = $delivery_iteminfo;

		UpdateGoodsItemStockInfo($item_info, $db);
	}
}


function check_inventory_order_wc($ioid){
	global $db;
	
	$sql="select 
				sum(iod.cancel_cnt) as cancel_goods_cnt,
				sum(iod.buy_price*iod.real_cnt) as real_goods_price,
				sum(iod.real_cnt) as real_goods_cnt,
				io.goods_cnt as total_goods_cnt,io.delivery_price
			from
				inventory_order_detail iod , inventory_order io
			where iod.ioid=io.ioid and iod.ioid='".$ioid."' ";
	$db->query($sql);
	$db->fetch();

	$update_str="";

	if($db->dt[total_goods_cnt]==($db->dt[real_goods_cnt]+$db->dt[cancel_goods_cnt])){
		$update_str=" status='WC', wc_date=NOW(), ";
	}elseif($db->dt[real_goods_cnt]>0){
		$update_str=" status='WP', ";
	}
	
	$sql="update inventory_order set 
				$update_str
				cancel_goods_cnt='".$db->dt[cancel_goods_cnt]."',
				real_goods_price='".$db->dt[real_goods_price]."',
				real_goods_cnt='".$db->dt[real_goods_cnt]."',
				real_delivery_price='".$db->dt[delivery_price]."',
				real_total_price='".($db->dt[real_goods_price]+$db->dt[delivery_price])."'
			where ioid='".$ioid."' ";
	$db->query($sql);
}
?>