<?
	#proc.publish.php
	include("../class/layout.class");
	//include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	//include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;
	
	$db->query("SELECT * FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE company_id = '".$admininfo[company_id]."'");
	$db->fetch();

	$s_company_name		= $db->dt[com_name];					// 회사명
	$s_company_number	= $db->dt[com_number];					// 사업자번호
	$s_state			= $db->dt[com_business_status];					// 업태
	$s_name				= $db->dt[com_ceo];								// 성명
	$s_item				= $db->dt[com_business_category];					// 업종
	$s_address			= $db->dt[com_addr1]." ".$db->dt[com_addr2] ;					// 주소
	$s_personin			= $db->dt[com_ceo];							// 담당자
	//$s_email			= explode("@",$db->dt[com_email]);		// 담당자 이메일
	$s_email			= $db->dt[com_email];		// 담당자 이메일
	$s_tel				= $db->dt[com_phone];					
	//echo $_FILES[xls][tmp_name];
	
	//echo phpinfo();

	require_once 'Excel/reader.php';

	// ExcelFile($filename, $encoding);
	$data = new Spreadsheet_Excel_Reader();

	// Set output Encoding.
	$data->setUTFEncoder('mb');
	$data->setOutputEncoding('utf-8');
	
	$data->read($_FILES[xls][tmp_name]);

	error_reporting(E_ALL ^ E_NOTICE);
$a = 0;
$b = 0;
	
	for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
		
		
		unset($p_day);
		unset($t_mon);
		unset($product);
		unset($p_size);
		unset($cnt);
		unset($price);
		unset($p_price);
		unset($tax);
		unset($comment);
		unset($supply_price);
		unset($tax_price);
		
		if($i > 3)
		{
			for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
			//	echo "$j - ".$data->sheets[0]['cells'][$i][$j]."<br>";
			}
			
			
			# 품목1
			if($data->sheets[0]['cells'][$i][21] > 0)
			{
				$t_mon[]	= substr($data->sheets[0]['cells'][$i][16],0,2);
				$t_day[]	= substr($data->sheets[0]['cells'][$i][16],2,2);
				$product[]	= $data->sheets[0]['cells'][$i][17];
				$p_size[]	= $data->sheets[0]['cells'][$i][18];
				$cnt[]		= $data->sheets[0]['cells'][$i][19];
				$price[]	= $data->sheets[0]['cells'][$i][20];
				$p_price[]	= $data->sheets[0]['cells'][$i][21];
				$tax[]		= $data->sheets[0]['cells'][$i][22];
				$comment[]	= $data->sheets[0]['cells'][$i][23];
			}

			# 품목2
			if($data->sheets[0]['cells'][$i][29] > 0)
			{
				$t_mon[]	= substr($data->sheets[0]['cells'][$i][24],0,2);
				$t_day[]	= substr($data->sheets[0]['cells'][$i][24],2,2);
				$product[]	= $data->sheets[0]['cells'][$i][25];
				$p_size[]	= $data->sheets[0]['cells'][$i][26];
				$cnt[]		= $data->sheets[0]['cells'][$i][27];
				$price[]	= $data->sheets[0]['cells'][$i][28];
				$p_price[]	= $data->sheets[0]['cells'][$i][29];
				$tax[]		= $data->sheets[0]['cells'][$i][30];
				$comment[]	= $data->sheets[0]['cells'][$i][31];
			}

			# 품목3
			if($data->sheets[0]['cells'][$i][37] > 0)
			{
				$t_mon[]	= substr($data->sheets[0]['cells'][$i][32],0,2);
				$t_day[]	= substr($data->sheets[0]['cells'][$i][32],2,2);
				$product[]	= $data->sheets[0]['cells'][$i][33];
				$p_size[]	= $data->sheets[0]['cells'][$i][34];
				$cnt[]		= $data->sheets[0]['cells'][$i][35];
				$price[]	= $data->sheets[0]['cells'][$i][36];
				$p_price[]	= $data->sheets[0]['cells'][$i][37];
				$tax[]		= $data->sheets[0]['cells'][$i][38];
				$comment[]	= $data->sheets[0]['cells'][$i][39];
			}
			
			# 품목4
			if($data->sheets[0]['cells'][$i][45] > 0)
			{
				$t_mon[]	= substr($data->sheets[0]['cells'][$i][40],0,2);
				$t_day[]	= substr($data->sheets[0]['cells'][$i][40],2,2);
				$product[]	= $data->sheets[0]['cells'][$i][41];
				$p_size[]	= $data->sheets[0]['cells'][$i][42];
				$cnt[]		= $data->sheets[0]['cells'][$i][43];
				$price[]	= $data->sheets[0]['cells'][$i][44];
				$p_price[]	= $data->sheets[0]['cells'][$i][45];
				$tax[]		= $data->sheets[0]['cells'][$i][46];
				$comment[]	= $data->sheets[0]['cells'][$i][47];
			}

			for($pp = 0; $pp < sizeof($p_price); $pp++)
			{
				$supply_price += $p_price[$pp];
				$tax_price += $tax[$pp];
			}
			
			
			$publish_type		= "1";
			$tax_type			= "1";
			//$numbering_k		= "";
			//$numbering_h		= "";
			//$numbering		= "";

			if($data->sheets[0]['cells'][$i][13] == "과세")	{
				$tax_per = "1";
			}else if($data->sheets[0]['cells'][$i][13] == "영세"){
				$tax_per = "2";
			}else if($data->sheets[0]['cells'][$i][13] == "면세"){
				$tax_per = "3";
			}else{
				$tax_per = "1";
			}

			if($data->sheets[0]['cells'][$i][14] == "영수")	$claim_kind = "1";
			else											$claim_kind = "2";

			if(strpos($data->sheets[0]['cells'][$i][1],"/") > 0)
			{
				$dd = substr($data->sheets[0]['cells'][$i][1],0,2) - 1;
				$signdate = substr($data->sheets[0]['cells'][$i][1],6,4)."-".substr($data->sheets[0]['cells'][$i][1],3,2)."-".$dd;
			}
			else
			{
				$signdate = $data->sheets[0]['cells'][$i][1];
			}

			$r_company_number	= $data->sheets[0]['cells'][$i][2];
			if(strlen($r_company_number) == 10) $r_company_number = substr($r_company_number,0,3)."-".substr($r_company_number,3,2)."-".substr($r_company_number,5,5);
			$r_company_j		= $data->sheets[0]['cells'][$i][3];
			$r_company_name		= $data->sheets[0]['cells'][$i][4];
			$r_name				= $data->sheets[0]['cells'][$i][5];
			$r_address			= $data->sheets[0]['cells'][$i][6];
			$r_state			= $data->sheets[0]['cells'][$i][7];
			$r_items			= $data->sheets[0]['cells'][$i][8];
			$r_personin			= $data->sheets[0]['cells'][$i][9];
			$r_tel				= $data->sheets[0]['cells'][$i][10];
			$sms_number			= $data->sheets[0]['cells'][$i][11];
			$r_email			= $data->sheets[0]['cells'][$i][12];
			//$company_j			= "";
			//$tax_per			= $data->sheets[0]['cells'][$i][11];
			$marking			= $data->sheets[0]['cells'][$i][15];
			//$supply_price		= "";
			//$tax_price			= "";
			$total_price		= $supply_price + $tax_price;
			$cash				= $data->sheets[0]['cells'][$i][48];
			$cheque				= $data->sheets[0]['cells'][$i][49];
			$pro_note			= $data->sheets[0]['cells'][$i][50];
			$outstanding		= $data->sheets[0]['cells'][$i][51];
			//$claim_kind		= $data->sheets[0]['cells'][$i][12];
			//$signdate			= $data->sheets[0]['cells'][$i][0];
			//$re_signdate		
			$send_type			= "1";
			//$sms_chk			
			//$sms_number		
			//$fax_chk			
			//$fax_number		
			//$memo			
			$status				= "0";
			if($sms_number != "") $sms_chk = "y";
			
			$SQL = "
			INSERT INTO 
				tax_sales 
			SET 
				publish_type		='$publish_type',
				tax_type			='$tax_type',
				numbering_k			='$numbering_k',
				numbering_h			='$numbering_h',
				numbering			='$numbering',
				s_company_number	='$s_company_number',
				s_company_j			='$s_company_j',
				s_company_name		='$s_company_name',
				s_name				='$s_name',
				s_address			='$s_address',
				s_state				='$s_state',
				s_items				='$s_item',
				s_personin			='$s_personin',
				s_tel				='$s_tel',
				s_email				='$s_email',
				r_company_number	='$r_company_number',
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
			$db->query($SQL);
			
			
			$SQL_C = "SELECT idx FROM tax_sales ORDER BY idx DESC LIMIT 1";
			$db->query($SQL_C);
			$db->fetch();
			$p_idx = $db->dt[idx];
			//echo $p_idx;


			for($o=0; $o < sizeof($p_price); $o++)
			{
				if($p_price[$o] > 0)
				{
					$SQL_2 = "
					INSERT INTO 
						tax_sales_detail 
					SET
						p_idx = '$p_idx',
						t_mon = '$t_mon[$o]',
						t_day = '$t_day[$o]',
						product = '$product[$o]',
						p_size = '$p_size[$o]',
						cnt = '$cnt[$o]',
						price = '$price[$o]',
						p_price = '$p_price[$o]',
						tax = '$tax[$o]',
						comment = '$comment[$o]',
						signdate = now()
					";
				//	echo $SQL_2."<br>";
					$db->query($SQL_2);
				}
			}
			/*팝빌 연동*/
			include_once("popbill/common.php");
			
			
			//임시저장이나 발행이나 popbill에 임시문서로 우선 저장 되어야 하기에 조건 없이 임시저장을 진행 함
			
			$Taxinvoice = new Taxinvoice();
			
			
				$publish_type = "정발행";
				$chargeDirection = "정과금";
			
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
			
			$company_number = str_replace('-','',$s_company_number);
			$r_company_number = str_replace('-','',$r_company_number);
			$signdate = str_replace('-','',$signdate);
			
			//echo $s_company_number;
			
			//임시 테스트용 사업자 번호 
			//$s_company_number = "2148868761";
			
			
			$Taxinvoice->writeDate = element($signdate,'');
			$Taxinvoice->issueType = element($publish_type,'');
			$Taxinvoice->chargeDirection = element($chargeDirection,'');
			$Taxinvoice->purposeType = element($claim_kind,'');
			$Taxinvoice->taxType = element($tax_per,'');
			$Taxinvoice->issueTiming = '직접발행';

			$Taxinvoice->invoicerCorpNum = element($company_number,'');
			$Taxinvoice->invoicerCorpName = element($s_company_name,'');
			if($publish_type != '2'){
			$Taxinvoice->invoicerMgtKey = element($p_idx,'');
			}
			$Taxinvoice->invoicerCEOName = element($s_name,'');
			$Taxinvoice->invoicerAddr = element($s_address,'');
			$Taxinvoice->invoicerContactName = element($s_personin,'');
			$Taxinvoice->invoicerEmail = element($db->dt[com_email],'');
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

			$Taxinvoice->originalTaxinvoiceKey = '';

			
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
			
			print_r($p_price);
			$z = 0;
			for($c=0; $c <= sizeof($p_price); $c++){
				//echo 111;
				if($p_price[$c] > 0){
					
					
					if($t_mon[$c] !='' && $t_day[$c] !=''){
						$ydate = date('Y');
						$purchaseDT = $ydate.$t_mon[$c].$t_day[$c];
					}
					
					$Taxinvoice->detailList[$z]->serialNum = element($c+1,'');
					$Taxinvoice->detailList[$z]->purchaseDT = element($purchaseDT,'');
					$Taxinvoice->detailList[$z]->itemName = element($product[$c],'');
					$Taxinvoice->detailList[$z]->spec = element($p_size[$c],'');
					$Taxinvoice->detailList[$z]->qty = element($cnt[$c],'');
					$Taxinvoice->detailList[$z]->unitCost = element($price[$c],'');
					$Taxinvoice->detailList[$z]->supplyCost = element($p_price[$c],'');
					$Taxinvoice->detailList[$z]->tax = element($tax[$c],'');
					$Taxinvoice->detailList[$z]->remark = element($comment[$c],'');
					
					$z ++;
				}
			}
			print_r($Taxinvoice);
			try {
				
				$result = $TaxinvoiceService->Register($company_number,$Taxinvoice,'daisomall',false);
				
				
					// 정상적으로 임시발행이 완료 될 경우 업데이트 
					$SQL = "
						update  
							tax_sales 
						SET 
							status ='$status'
						where 
							idx = '$p_idx'
						";
						//echo $SQL."<br><br>";
						$db->query($SQL);	
				$a++;	
			}catch(PopbillException $pe) {
			//  echo "<script>alert ('".$pe->getMessage()."');</script>";
				echo '['.$pe->getCode().'] '.$pe->getMessage();
			//	echo "<script>alert([".$pe->getCode()."] ".$pe->getMessage().");</script>";
			//	exit;
				$b++;
			}
			
			//echo "<br><br><br>";

		}

	}
	@unlink($_FILES[xls][tmp_name]);
	echo "<script>alert ('일괄발행 처리되었습니다. 성공 : ".$a." 실패 : ".$b."');parent.location.reload();</script>";
?>
<script>
//alert ("일괄발행 처리되었습니다. 성공 : ".$a." 실패 : ".$b." ");
//parent.location.reload();
</script>