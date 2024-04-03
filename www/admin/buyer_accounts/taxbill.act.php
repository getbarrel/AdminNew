<?
include("../class/layout.class");
include_once("../lib/barobill.lib.php");

$db = new Database;
$db2 = new Database;
$udb = new Database;


if($act == "order_taxbill"){

	$db->query("SELECT com_name, com_number, com_business_status, com_ceo, com_business_category, com_addr1, com_addr2, tax_person_name, tax_person_mail, tax_person_phone FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE com_type = 'A' ");
	$buyer = $db->fetch();
	
	
	if($publish_type == '1'){//정발행
		/*공급자 정보 호출*/
		$s_company_name		= $buyer[com_name];					// 회사명
		$s_company_number	= $buyer[com_number];					// 사업자번호
		$s_company_number2	= $buyer[com_number];					// 사업자번호
		$s_state			= $buyer[com_business_status];					// 업태
		$s_name				= $buyer[com_ceo];								// 성명
		$s_item				= $buyer[com_business_category];					// 업종
		$s_address			= $buyer[com_addr1]." ".$buyer[com_addr2] ;					// 주소
		$s_personin			= $buyer[tax_person_name];							// 담당자
		$s_email			= $buyer[tax_person_mail];		// 담당자 이메일
		$s_tel				= $buyer[tax_person_phone];
		/*공급자 정보 끝*/
		
	}else if($publish_type == '2'){//역발행
		/*공급받는자 정보 호출*/
		$r_company_name		= $buyer[com_name];					// 회사명
		$r_company_number	= $buyer[com_number];					// 사업자번호
		$r_company_number2	= $buyer[com_number];					// 사업자번호
		$r_state			= $buyer[com_business_status];					// 업태
		$r_name				= $buyer[com_ceo];								// 성명
		$r_item				= $buyer[com_business_category];					// 업종
		$r_address			= $buyer[com_addr1]." ".$buyer[com_addr2] ;					// 주소
		$r_personin			= $buyer[tax_person_name];							// 담당자
		$r_email			= $buyer[tax_person_mail];		// 담당자 이메일
		$r_tel				= $buyer[tax_person_phone];
		/*공급자 정보 끝*/
	}
	
	$s_cnt=0;
	$f_cnt=0;
	$t_cnt=count($oid);
	$befor_oid_num = "";
	for($i=0;$i<$t_cnt;$i++){
		//	echo 1;
		//팝빌 용 신규 시스템
	
		/*공급받는자 정보 호출*/
		
		$oid_num = explode('|',$oid[$i]);
		//echo $befor_oid_num;
		//exit;
		if($befor_oid_num != $oid_num[0] && $oid_num[0] !="" ){
		
			$befor_oid_num = $oid_num[0];
			$sql = "select 
						AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
						o.bmail,
						bname,bmobile,baddr
						from ".TBL_SHOP_ORDER." o 
						left join ".TBL_COMMON_MEMBER_DETAIL." cmd on o.user_code = cmd.code
						
						where o.oid ='$oid_num[0]' ";
			
			$db->query($sql);
			$db->fetch();
			
			$com_mail = $db->dt[mail];
			$order_mail = $db->dt[bmail];
			$r_personin = $db->dt[bname];
			$r_tel = $db->dt[bmobile];
			//$r_address = $db->dt[baddr];
			
			if($com_mail == ""){
				$com_mail = $order_mail; // 회원 정보가 없을 경우 (비회원) 주문자 메일로 변경 
			}
			
			
			//$sql = "select o.oid from shop_order_payment o where o.oid ='$oid_num[0]' ";
			$sql = "SELECT
							*
					FROM (
						SELECT
							tax_com_name, tax_com_ceo, tax_com_number,
							pay_type,pay_status,method,ic_date,tax_affairs_yn,'Y' as tax_yn, tax_no as bill_no,
							SUM(case when pay_type ='F' then -tax_price else tax_price end) as tax_price,
							'0' as tax_free_price
						FROM shop_order_payment where oid='".$oid_num[0]."' and method !='".ORDER_METHOD_RESERVE."' and tax_price > 0 and tax_ix = '0'
						having tax_price > 0 
						UNION ALL
						SELECT
							tax_com_name, tax_com_ceo, tax_com_number,
							pay_type,pay_status,method,ic_date,tax_affairs_yn,'N' as tax_yn, bill_no as bill_no,
							'0' as tax_price,
							SUM(case when pay_type ='F' then -tax_free_price else tax_free_price end) as tax_free_price
						FROM shop_order_payment where oid='".$oid_num[0]."' and method !='".ORDER_METHOD_RESERVE."' and tax_free_price > 0 and bill_ix = '0'
						having tax_free_price > 0 
					) a ";
			
			$db->query($sql);
			//echo $db->total;
			//exit;
			if($db->total > 0){
				for($z=0; $z < $db->total ; $z ++){
					$db->fetch($z);
					$tax_yn = $db->dt["tax_yn"];
					if($db->dt["tax_yn"]=="Y"){
						$tax_per="1";
						$price = $db->dt["tax_price"];
						$tax = $price - round($price/1.1);
						$tax_type = 1; // 1:세금계산서 2:계산서
					}else{
						$tax_per="3";
						$price = $db->dt["tax_free_price"];
						$tax=0;
						$tax_type = 2; // 1:세금계산서 2:계산서
					}

					//echo $sql;
					//exit;
					$r_company_number = $db->dt[tax_com_number];
					$r_company_number2 = $db->dt[tax_com_number];
					$r_company_name = $db->dt[tax_com_name];
					$r_name = $db->dt[tax_com_ceo];
					//$r_personin = $db->dt[tax_com_ceo];
					$r_email = $com_mail;
					
					//$r_company_number = "214-88-68761";
					
					$supply_price = $price - $tax ;//공급가액
					$tax_price = $tax ;//세액
					$total_price = $price ;//합계금액
					//if($publish_type == 1){//정발행
						$claim_kind = "1"; //정발행일때 영수처리
					//}else{
					//	$claim_kind = "2"; // 입금전에 신청한 정보이기 때문에 청구로 전송
					//}
					
					//echo $sql;
					//exit;
					/*공급받는자 정보 끝*/

					/*세금계산서 옵션 정보*/
					//$signdate = date('Y-m-d');
					$signdate = substr($db->dt[ic_date],0,10);
					//echo $signdate;
					//exit;
					$publish_type = 1; //1 : 매출 2 : 매입 3 : 위수탁
					
					
					$SQL = "
					INSERT INTO 
						tax_sales 
					SET 
						publish_type		='$publish_type',
						tax_type			='$tax_type',
						numbering_k			='$numbering_k',
						numbering_h			='$numbering_h',
						numbering			='$numbering',
						s_company_number	='$s_company_number2',
						s_company_j			='$s_company_j',
						s_company_name		='$s_company_name',
						s_name				='$s_name',
						s_address			='$s_address',
						s_state				='$s_state',
						s_items				='$s_item',
						s_personin			='$s_personin',
						s_tel				='$s_tel',
						s_email				='$s_email',
						r_company_number	='$r_company_number2',
						r_company_j			='$r_company_j',
						r_company_name		='$r_company_name',
						r_name				='$r_name',
						r_address			='$r_address',
						r_state				='$r_state',
						r_items				='$r_items',
						r_personin			='$r_personin',
						r_tel				='$r_tel',
						r_email				='$r_email',
						company_j			='$company_j',
						tax_per				='$tax_per',
						marking				='$marking',
						supply_price		='$supply_price',
						tax_price			='$tax_price',
						total_price			='$total_price',
						cash				='$cash',
						cheque				='$cheque',
						pro_note			='$pro_note',
						outstanding			='$outstanding',
						claim_kind			='$claim_kind',
						signdate			='$signdate',
						re_signdate			=now(),
						send_type			='$send_type',
						sms_chk				='$sms_chk',
						sms_number			='$sms_number',
						fax_chk				='$fax_chk',
						fax_number			='$fax_number',
						memo				='$memo',
						status				='7'
					";
					//echo $SQL."<br><br>";
					//exit;
					$db2->query($SQL);

					$SQL_C = "SELECT idx FROM tax_sales ORDER BY idx DESC LIMIT 1";
					$db2->query($SQL_C);
					$db2->fetch();
					$p_idx = $db2->dt[idx];
					//echo $p_idx;

					
					//품목명 넣기 위해 
					if($tax_yn == 'Y'){
						//과세의 경우 surtax_yorn 값이 N으로 지정되기때문에 jk140828
						$sql = "select count(od_ix) as pcnt ,pname from ".TBL_SHOP_ORDER_DETAIL." where oid = '".$oid_num[0]."' and surtax_yorn = 'N'";
					}else{
						$sql = "select count(od_ix) as pcnt ,pname from ".TBL_SHOP_ORDER_DETAIL." where oid = '".$oid_num[0]."' and surtax_yorn = 'Y'";
					}
					$db2->query($sql);
					$db2->fetch();
					//echo $sql;
					
					//$t_mon = date('m');
					//$t_day = date('d');
					
					$t_mon = substr($signdate,5,2);
					$t_day = substr($signdate,8,2);
					
					$cnt = $db2->dt[pcnt]-1;
					if($cnt > 0){
						$product = mb_substr($db2->dt[pname],0,90,"utf-8")." 외".$cnt." 건";
					}else{
						$product = mb_substr($db2->dt[pname],0,90,"utf-8");
					}

					$sql = "select sum(case when payment_status='F' then -delivery_price else delivery_price end) as delivery_price from shop_order_price where oid = '".$oid_num[0]."' ";
					$db2->query($sql);
					$db2->fetch();
					$delivery_price = $db2->dt[delivery_price];
					if($delivery_price > 0){
						$pinfo[0][product] = "배송비";
						$pinfo[0][tax_price] = $delivery_price - round($delivery_price/1.1);
						$pinfo[0][supply_price] = $delivery_price - $pinfo[0][tax_price];

						$pinfo[1][product] = $product;
						$pinfo[1][tax_price] = $tax_price-$pinfo[0][tax_price];
						$pinfo[1][supply_price] = $supply_price-$pinfo[0][supply_price];
					}else{
						$pinfo[0][product] = $product;
						$pinfo[0][tax_price] = $tax_price;
						$pinfo[0][supply_price] = $supply_price;
					}
					
					foreach($pinfo as $p_i){
						$SQL_2 = "
						INSERT INTO 
							tax_sales_detail 
						SET
							p_idx = '".$p_idx."',
							t_mon = '".$t_mon."',
							t_day = '".$t_day."',
							product = '".$p_i[product]."',
							p_size = '',
							cnt = '',
							price = '',
							p_price = '".$p_i[supply_price]."',
							tax = '".$p_i[tax_price]."',
							comment = '',
							signdate = now()
						";
						/*
						$SQL_2 = "
						INSERT INTO 
							tax_sales_detail 
						SET
							p_idx = '".$p_idx."',
							t_mon = '".$t_mon."',
							t_day = '".$t_day."',
							product = '".$product."',
							p_size = '',
							cnt = '',
							price = '',
							p_price = '".$supply_price."',
							tax = '".$tax_price."',
							comment = '',
							signdate = now()
						*/
						//echo $SQL_2."<br>";
						//exit;
						$db2->query($SQL_2);
					}

						/*팝빌 연동*/
					include_once($DOCUMENT_ROOT."/admin/tax/popbill/common.php");
					
					
					//임시저장이나 발행이나 popbill에 임시문서로 우선 저장 되어야 하기에 조건 없이 임시저장을 진행 함
					
					$Taxinvoice = new Taxinvoice();
					
					if($publish_type == '1'){
						$publish_type = "정발행";
						$chargeDirection = "정과금";
					}else if($publish_type == '2'){
						$publish_type = "역발행";
						$chargeDirection = "역과금";
					}else if($publish_type == '3'){
						$publish_type = "위수탁";
					}
					if($claim_kind == '1'){
						$claim_kind = "영수";
					}else if($claim_kind == '2'){
						$claim_kind = "청구";
					}
					if($tax_per == '1'){
						$tax_per = "과세";
					}else if($tax_per == '2'){
						$tax_per = "영세";
					}else if($tax_per == '3'){
						$tax_per = "면세";
					}
					
					$s_company_number = str_replace('-','',$s_company_number);
					$r_company_number = str_replace('-','',$r_company_number);
					$signdate = str_replace('-','',$signdate);
					
					//임시 테스트용 사업자 번호 
					//$r_company_number = "2148868761";
					
					
					$Taxinvoice->writeDate = element($signdate,'');
					$Taxinvoice->issueType = element($publish_type,'');
					$Taxinvoice->chargeDirection = element($chargeDirection,'');
					$Taxinvoice->purposeType = element($claim_kind,'');
					$Taxinvoice->taxType = element($tax_per,'');
					$Taxinvoice->issueTiming = '직접발행';

					$Taxinvoice->invoicerCorpNum = element($s_company_number,'');
					$Taxinvoice->invoicerCorpName = element($s_company_name,'');
					if($publish_type != '2'){
					$Taxinvoice->invoicerMgtKey = element($p_idx,'');
					}
					$Taxinvoice->invoicerCEOName = element($s_name,'');
					$Taxinvoice->invoicerAddr = element($s_address,'');
					$Taxinvoice->invoicerContactName = element($s_personin,'');
					$Taxinvoice->invoicerEmail = element($s_email,'');
					$Taxinvoice->invoicerTEL = element($s_tel,'');
					$Taxinvoice->invoicerHP = '';
					$Taxinvoice->invoicerBizClass = element($s_item,'');
					$Taxinvoice->invoicerBizType = element($s_state,'');
					if($publish_type != '2'){
					$Taxinvoice->invoicerSMSSendYN = true;
					}else{
					$Taxinvoice->invoicerSMSSendYN = false;
					}
					$Taxinvoice->invoiceeType = '사업자';
					$Taxinvoice->invoiceeCorpNum = element($r_company_number,'');
					$Taxinvoice->invoiceeCorpName = element($r_company_name,'');
					if($publish_type == '2'){
					$Taxinvoice->invoiceeMgtKey = element($p_idx,'');
					}
					$Taxinvoice->invoiceeCEOName = element($r_name,'');
					$Taxinvoice->invoiceeAddr = element($r_address,'');
					$Taxinvoice->invoiceeContactName1 = element($r_personin,'');
					$Taxinvoice->invoiceeEmail1 = element($r_email,'');
					$Taxinvoice->invoiceeTEL1 = element($r_tel,'');
					$Taxinvoice->invoiceeHP1 = '';
					$Taxinvoice->invoiceeBizClass = element($r_item,'');
					$Taxinvoice->invoiceeBizType = element($r_state,'');
					if($publish_type == '2'){
					$Taxinvoice->invoiceeSMSSendYN = true;
					}else{
					$Taxinvoice->invoiceeSMSSendYN = false;
					}
					$Taxinvoice->supplyCostTotal = element($supply_price,'');
					$Taxinvoice->taxTotal = element($tax_price,'0');
					$Taxinvoice->totalAmount = element($total_price,'');
					
					
					$Taxinvoice->originalTaxinvoiceKey = '';//팝빌승인번호

					
					$Taxinvoice->serialNum = element($SerialNum,'');
					$Taxinvoice->cash = element($cash,'');
					$Taxinvoice->chkBill = element($cheque,'');
					$Taxinvoice->note = element($pro_note,'');
					$Taxinvoice->credit = element($outstanding,'');
					$Taxinvoice->remark1 = element($marking,'');
					$Taxinvoice->remark2 = element($marking2,'');
					$Taxinvoice->remark3 = element($marking3,'');
					$Taxinvoice->kwon = element((int)$numbering_k,'0');
					$Taxinvoice->ho = element((int)$numbering_h,'0');

					$Taxinvoice->businessLicenseYN = false;
					$Taxinvoice->bankBookYN = false;
					$Taxinvoice->faxreceiveNum = null;
					$Taxinvoice->faxsendYN = false;

					$Taxinvoice->detailList = array();

					$Taxinvoice->detailList[] = new TaxinvoiceDetail();
					
					//print_r($Taxinvoice);
					
							
					
					$ydate = date('Y');
					$purchaseDT = $ydate.$t_mon.$t_day;
							
					foreach($pinfo as $key => $p_i){
						$Taxinvoice->detailList[$key]->serialNum = element($key+1,'');
						$Taxinvoice->detailList[$key]->purchaseDT = element($purchaseDT,'');
						$Taxinvoice->detailList[$key]->itemName = element($p_i[product],'');
						$Taxinvoice->detailList[$key]->spec = '';
						$Taxinvoice->detailList[$key]->qty = '';
						$Taxinvoice->detailList[$key]->unitCost = '';
						$Taxinvoice->detailList[$key]->supplyCost = element($p_i[supply_price],'');
						$Taxinvoice->detailList[$key]->tax = element($p_i[tax_price],'');
						$Taxinvoice->detailList[$key]->remark = '';
					}

					//var_dump($Taxinvoice);
					//print_r($Taxinvoice);
					//exit;
					
					
					if($publish_type == '1'){
						$result = $TaxinvoiceService->Register($s_company_number,$Taxinvoice,$_SESSION[admin_config][popbill_id],false);
						//print_r($result);
						//exit;
					}else if($publish_type == '2'){
						$result = $TaxinvoiceService->Register($r_company_number,$Taxinvoice,$_SESSION[admin_config][popbill_id],false);
					}else{
						$result = $TaxinvoiceService->Register($s_company_number,$Taxinvoice,$_SESSION[admin_config][popbill_id],false);
					}
					
				//	print_r($result)."<br>";
				//	exit;
					if($result->code=="1"){
						//echo $status;
						//exit;
						
							// 정상적으로 임시발행이 완료 될 경우 업데이트 
							$SQL = "
								update  
									tax_sales 
								SET 
									status ='0'
								where 
									idx = '$p_idx'
								";
								//echo $SQL."<br><br>";
								$db2->query($SQL);	
							if($tax_yn == 'Y'){
								$sql = "update shop_order_payment set tax_ix = '$p_idx' where oid = '".$oid_num[0]."' and taxsheet_yn = 'Y' ";
							}else{
								$sql = "update shop_order_payment set bill_ix = '$p_idx' where oid = '".$oid_num[0]."' and taxsheet_yn = 'Y' ";
							}	
							$db2->query($sql);	
							$s_cnt++;
							
						
							
						//	echo "<script>alert ('임시발행 완료.'); parent.location.href='./taxbill_list.php?view_type=order_apply';</script>";
						//	exit;
					}else{
					//	echo 1;
						$f_cnt++;
						
						
						/********************************** 로그폴더 생성 *************************************************/
						$path = $_SERVER["DOCUMENT_ROOT"]."/_logs/tax_log/";
						if(!is_dir($path)){					
							mkdir($path, 0777);
							chmod($path,0777);
						}
						/********************************** 실패 로그 작성 *************************************************/
						$write = "oid : '".$oid_num[0]."' , tax_ix = '$p_idx', code = '".$result->code."', message = '".$result->message."', s_company_number = '".$s_company_number."', r_company_number = '".$r_company_number."' , log_time = '".date("H:i:s")."' \n";
						$fp = fopen($path."tax_log_".date("Y-m-d").".txt","a+");
						chmod($path."tax_log_".date("Y-m-d").".txt",0777);
						fwrite($fp,$write);
						fclose($fp);
						/********************************** 실패 로그 작성 *************************************************/
						//echo "<script>alert ('".$pe->getMessage()."');</script>";
						//echo '['.$pe->getCode().'] '.$pe->getMessage();
						//echo 1;
					//	echo "<script>alert([".$pe->getCode()."] ".$pe->getMessage().");</script>";
						//exit;
					}
				}
				
			}
		}
		//exit;
	}

	echo "<script>alert('총: ".$t_cnt." 건중 성공: ".$s_cnt." 실패: ".$f_cnt." 건이 처리되었습니다.');top.location.reload();</script>";
	exit;
}

?>