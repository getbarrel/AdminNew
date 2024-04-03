<?
include("../class/layout.class");
include_once("../lib/barobill.lib.php");

$db = new Database;
$sdb = new Database;


if($act == "inversely_bill"){
	
	$db->query("SELECT com_name, com_number, com_business_status, com_ceo, com_business_category, com_addr1, com_addr2, tax_person_name, tax_person_mail,tax_person_phone FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE com_type = 'A' ");
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
	$t_cnt=count($ar_ix);

	for($i=0;$i<count($ar_ix);$i++){
		
		//팝빌 용 신규 시스템
	
		/*공급받는자 정보 호출*/
		
		//$oid = explode('|',$oid[$i]);
		
		$sql = "select 
			*
		FROM shop_accounts_remittance ar left join common_company_detail c on (ar.company_id=c.company_id)
			where ar.ar_ix='$ar_ix[$i]'	";
		
		$db->query($sql);
		$db->fetch();
		
		$com_mail = $db->dt[com_email];
		$company_id = $db->dt[company_id];
		
		$tax_per="3"; // 면세 계산서 발행
		
		$p_tax_free_price = $db->dt[p_tax_free_price];//상품대금 공급가
		$p_tax_price = 0;//상품대금 세액
		$p_tax_total_price = $p_tax_free_price;//상품대금 합계
		
		$d_tax_free_price = $db->dt[d_tax_free_price];//배송대금 공급가
		$d_tax_price = 0;//배송대금 세액
		$d_tax_total_price = $d_tax_free_price;//배송대금 합계
		
		$total_tax_coprice = $p_tax_free_price+$d_tax_free_price; //총 공급가 합계
		$total_tax_price = 0; // 총 세액 합계
		$total_tax_total_price = $total_tax_coprice; // 총 세액 합계
		
		$tex_item_info = array();
		
		$tex_item_info[0][product] = '상품대금';
		$tex_item_info[0][supply_price] = $p_tax_free_price;
		$tex_item_info[0][tax_price] = $p_tax_price;
		
		$tex_item_info[1][product] = '배송대금';
		$tex_item_info[1][supply_price] = $d_tax_free_price;
		$tex_item_info[1][tax_price] = $d_tax_price;
		
		if($publish_type == '2'){//역발행
			
			$s_company_name		= $db->dt[com_name];					// 회사명
			$s_company_number	= $db->dt[com_number];					// 사업자번호
			$s_company_number2	= $db->dt[com_number];					// 사업자번호
			$s_state			= $db->dt[com_business_status];					// 업태
			$s_name				= $db->dt[com_ceo];								// 성명
			$s_item				= $db->dt[com_business_category];					// 업종
			$s_address			= $db->dt[com_addr1]." ".$db->dt[com_addr2] ;					// 주소
			$s_personin			= $db->dt[tax_person_name];							// 담당자
			$s_email			= $db->dt[tax_person_mail];		// 담당자 이메일
			$s_tel				= $db->dt[tax_person_phone];

		}
		//$s_company_number = "214-88-68761";
		
		$supply_price = $total_tax_coprice ;//공급가액
		$tax_price = $total_tax_price ;//세액
		$total_price = $total_tax_total_price ;//합계금액
		$claim_kind = "2"; // 입금전에 신청한 정보이기 때문에 청구로 전송
		
		//echo $sql;
		//exit;
		/*공급받는자 정보 끝*/

		/*세금계산서 옵션 정보*/
		/*세금계산서 옵션 정보*/
		
		$sql = "select
					ac_term_div,ac_term_date1,ac_term_date2
				from
					common_seller_delivery 
				where
					company_id = '".$company_id."'";
		$sdb->query($sql);
		$sdb->fetch();
		
		$ac_term_div = $sdb->dt[ac_term_div];
		$ac_term_date1 = $sdb->dt[ac_term_date1];
		$ac_term_date2 = $sdb->dt[ac_term_date2];
		
		if($ac_term_div == '1'){
			$today = strtotime('-1 months');
			$end_day = date("t", $today);
			//$signdate = date('Y-m-'.$end_day);
			$signdate = date('Y-m-t',$today);
			//$month_date = date('m');
			$month_date = date('m',$today);
		}else if($ac_term_div == '2'){
			if(date('d') < 16){
				$signdate = date('Y-m-15');
				$month_date = date('m');
				$end_day = 15;
			}else{
				$today = strtotime('-1 months');
				$end_day = date("t", $today);
				//$signdate = date('Y-m-'.$end_day);
				$signdate = date('Y-m-t',$today);
				//$month_date = date('m');
				$month_date = date('m',$today);
			}
		}


		$publish_type = 2; //1 : 매출 2 : 매입 3 : 위수탁
		$tax_type = 2; // 1:세금계산서 2:계산서
		$tax_div = 2; // 1:구매자 2:판매자
		
		$SQL = "
			INSERT INTO 
				tax_sales 
			SET 
				publish_type		='$publish_type',
				tax_div				='$tax_div',
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
				r_items				='$r_item',
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
			echo $SQL."<br><br>";
			$db->query($SQL);

			$SQL_C = "SELECT idx FROM tax_sales ORDER BY idx DESC LIMIT 1";
			$db->query($SQL_C);
			$db->fetch();
			$p_idx = $db->dt[idx];
			//echo $p_idx;

			
			//품목명 넣기 위해 
			
			for($j=0; $j < count($tex_item_info); $j++){

				//$t_mon = date('m');
				//$t_day = date('d');
				$t_mon = $month_date; 
				$t_day = $end_day;
				
				$SQL_2 = "
					INSERT INTO 
						tax_sales_detail 
					SET
						p_idx = '".$p_idx."',
						t_mon = '".$t_mon."',
						t_day = '".$t_day."',
						product = '".$tex_item_info[$j][product]."',
						p_size = '',
						cnt = '',
						price = '',
						p_price = '".$tex_item_info[$j][supply_price]."',
						tax = '".$tex_item_info[$j][tax_price]."',
						comment = '',
						signdate = now()
					";
					//echo $SQL_2."<br>";
					//exit;
					$db->query($SQL_2);
			}
		
			
			
			
			
			
			/*팝빌 연동*/
		include_once($DOCUMENT_ROOT."/admin/tax/popbill/common.php");
		
		
		//임시저장이나 발행이나 popbill에 임시문서로 우선 저장 되어야 하기에 조건 없이 임시저장을 진행 함
		
		$Taxinvoice = new Taxinvoice();
		
		if($publish_type == '1'){
			$publish_type_text = "정발행";
			$chargeDirection = "정과금";
		}else if($publish_type == '2'){
			$publish_type_text = "역발행";
			$chargeDirection = "역과금";
		}else if($publish_type == '3'){
			$publish_type_text = "위수탁";
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
		//$s_company_number = "2148868761";
		
		
		$Taxinvoice->writeDate = element($signdate,'');
		$Taxinvoice->issueType = element($publish_type_text,'');
		$Taxinvoice->chargeDirection = element($chargeDirection,'');
		$Taxinvoice->purposeType = element($claim_kind,'');
		$Taxinvoice->taxType = element($tax_per,'');
		$Taxinvoice->issueTiming = '직접발행';

		$Taxinvoice->invoicerCorpNum = element($s_company_number,'');
		$Taxinvoice->invoicerCorpName = element($s_company_name,'');
		//if($publish_type != '2'){
		$Taxinvoice->invoicerMgtKey = element($p_idx,'');
		//}
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
		
				
		
		$f_msg="";
				
		for($j=0; $j < count($tex_item_info); $j++){
			$ydate = date('Y');
			/*$t_mon = date('m');
			$t_day = date('d');
			*/
			$t_mon = $month_date; 
			$t_day = $end_day;
			
			$purchaseDT = $ydate.$t_mon.$t_day;
			
			$Taxinvoice->detailList[$j]->serialNum = element($j+1,'');
			$Taxinvoice->detailList[$j]->purchaseDT = element($purchaseDT,'');
			$Taxinvoice->detailList[$j]->itemName = element($tex_item_info[$j][product],'');
			$Taxinvoice->detailList[$j]->spec = '';
			$Taxinvoice->detailList[$j]->qty = '';
			$Taxinvoice->detailList[$j]->unitCost = '';
			$Taxinvoice->detailList[$j]->supplyCost = element($tex_item_info[$j][supply_price],'');
			$Taxinvoice->detailList[$j]->tax = element($tex_item_info[$j][tax_price],'');
			$Taxinvoice->detailList[$j]->remark = '';
		}	
		
		//var_dump($Taxinvoice);
		//print_r($Taxinvoice);
		//exit;
		echo $_SESSION[admin_config][popbill_id];
		
		try {
			if($publish_type == '1'){
				$result = $TaxinvoiceService->Register($s_company_number,$Taxinvoice,$_SESSION[admin_config][popbill_id],false);
			}else if($publish_type == '2'){
				$result = $TaxinvoiceService->Register($r_company_number,$Taxinvoice,$_SESSION[admin_config][popbill_id],false);
			}else{
				$result = $TaxinvoiceService->Register($s_company_number,$Taxinvoice,$_SESSION[admin_config][popbill_id],false);
			}
			//echo $status;
			//exit;

			if($result->code=="1"){
				//0.임시저장 1.발행 2.발행대기 3.발행취소 4.승인요청 5.승인거부 6.승인취소 7.계산서작성중
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
					$db->query($SQL);
				
				//정상적으로 발행될경우 정산 관리 의 상태 업데이트 
				$sql = "update shop_accounts_remittance set bill_ix = '$p_idx' where ar_ix='$ar_ix[$i]'	";	
				
				$db->query($sql);	
				
				$s_cnt++;
				//echo "<script>alert ('임시발행 완료.'); parent.location.href='./taxbill_list.php?pre_type=inversely_apply';</script>";
				//exit;
			}else{
				$f_cnt++;
				if($f_cnt==0)	$f_msg .= $result->message;
				else			$f_msg .= ",".$result->message;
			}
		}
		catch(PopbillException $pe) {
		$f_cnt++;
		//	echo "<script>alert ('".$pe->getMessage()."');</script>";
		//	echo '['.$pe->getCode().'] '.$pe->getMessage();
			//echo 1;
		//	echo "<script>alert([".$pe->getCode()."] ".$pe->getMessage().");</script>";
		//	exit;
		}
		//exit;
		/*
		//역발행은 반대로!
		$db->query("SELECT com_name, com_number, com_business_status, com_ceo, com_business_category, com_addr1, com_addr2, tax_person_name, tax_person_mail FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE com_type = 'A' ");
		$seller = $db->fetch();

		$s_cnt=0;
		$f_cnt=0;
		$t_cnt=count($ac_ix);

		for($i=0;$i<count($ac_ix);$i++){

			$sql = "SELECT a.*,ccd.com_name,ccd.com_number,ccd.com_business_status, ccd.com_ceo, ccd.com_business_category,ccd.com_addr1, ccd.com_addr2,ccd.tax_person_name,ccd.tax_person_mail from 
				(
					select * FROM shop_accounts_remittance WHERE ar_ix ='".$ar_ix[$i]."'
				) a left join common_company_detail ccd  on (a.company_id=ccd.company_id)
				 ";
			$db->query($sql);
			$buyer = $db->fetch();
			
			$seller[ac_ix]=$buyer[ac_ix];
			$seller[ac_date]=$buyer[ac_date];
			$seller[company_id]=$buyer[company_id];

			$expect[supply_price]=$expect_coprice[$ac_ix[$i]];
			$expect[tax_price]=$expect_tax[$ac_ix[$i]];
			$expect[total_price]=$expect_total[$ac_ix[$i]];

			$real[supply_price]=$coprice[$ac_ix[$i]];
			$real[tax_price]=$tax[$ac_ix[$i]];
			$real[total_price]=$total[$ac_ix[$i]];

			$product[mon]=date("m");
			$product[day]=date("d");

			$product[product]=$seller[ac_date]."월 상품매입";

			//insert_tax_sales -> ../lib/barobill.lib.php 
			//publish_type 1,매출 2,매입 3,위수탁
			//tax_div 구분 1:구매자,2:판매자
			//tax_type 1.세금계산서 2.계산서
			//tax_per 1:과세 2:면세
			$tax_info[publish_type]="2";
			$tax_info[tax_div]="2";
			$tax_info[tax_type]="1";
			$tax_info[tax_per]="1";
			$p_idx = insert_tax_sales($tax_info,$buyer,$seller,$expect,$real,$product);
			
			if($p_idx){
				if(barobill_input($p_idx)){
					$s_cnt++;
					$db->query("update shop_accounts set taxbill_yn = 'Y' where ac_ix ='".$ac_ix[$i]."' ");
				}else{
					$f_cnt++;
					$db->query("delete from tax_sales where idx='".$p_idx."' ");
					$db->query("delete from tax_sales_detail where idx='".$p_idx."' ");
				}
			}else{
				$f_cnt++;
			}
		}
		*/
	}

	echo "<script>alert('총: ".$t_cnt." 건중 성공: ".$s_cnt." 실패: ".$f_cnt." 건이 처리되었습니다.');top.location.reload();</script>";
	if($f_cnt > 0){
		echo "<script>alert('실패내용 : ".$f_msg."');</script>";
	}
	echo "<script>top.location.reload();</script>";
	exit;

}

if($act == "bill_issue"){
	if($bill_ix){
		include_once($DOCUMENT_ROOT."/admin/tax/popbill/common.php");
		$result = $TaxinvoiceService->Issue($com_num,ENumMgtKeyType::SELL,$bill_ix,$_SESSION["admininfo"]["company_name"]."(".$_SESSION["admininfo"]["charger_id"].") 발행",null,true);
		if($result->code=="1"){
			
			$SQL = "
				update  
					tax_sales 
				SET 
					status ='1'
				where 
					idx = '$bill_ix'
				";
			$db->query($SQL);

			$sql = "update shop_accounts_remittance set bill_no = '$bill_ix', bill_status='1', bill_date=NOW() where ar_ix='$ar_ix'";
			$db->query($sql);

			echo "<script>alert('정상적으로 발행되었습니다.');</script>";
			exit;
		}else{
			echo "<script>alert('".$result->message."');</script>";
			exit;
		}
	}else{
		echo "<script>alert('발행에 실패 하였습니다.');</script>";
		exit;
	}
}

if($act == "again_bill"){
	/*
	if(barobill_input($idx)){
		echo "<script>alert('처리되었습니다.');top.location.reload();</script>";
		exit;
	}else{
		exit;
	}
	*/
}

?>