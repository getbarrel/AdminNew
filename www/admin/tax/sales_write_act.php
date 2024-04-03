<?
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	//include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
	//include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
	include("../class/layout.class");
	$db = new Database;

//	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");
//print_r($_POST);
//	exit;
	$DataLink = "/home/dev/www/admin/tax/file/";
	$FileName = $_FILES["DataFile"]["name"];				# 파일명
	$File	  = $_FILES["DataFile"]["tmp_name"];			# 파일
	$FileSize = $_FILES["DataFile"]["size"];				# 파일 사이즈

	if($_POST[s_email1] && $_POST[s_email2])		$s_email = $_POST[s_email1]."@".$_POST[s_email2];
	if($_POST[r_email1] && $_POST[r_email2])		$r_email = $_POST[r_email1]."@".$_POST[r_email2];
	if($_POST[hp1] && $_POST[hp2] && $_POST[hp3])	$sms_number = $_POST[hp1]."-".$_POST[hp2]."-".$_POST[hp3];
	if($_POST[fax1] && $_POST[fax2] && $_POST[fax3]) $fax_number	= $_POST[fax1]."-".$_POST[fax2]."-".$_POST[fax3];

	#-- Function Info [S]  -------------------------------------#
	/*if (!defined ("ALERT")){
		function ALERT($msg){
		   echo("<script language=\"javascript\"> 
		   alert('$msg');
		   return;
		   </script>");
		   EXIT;
		}
	define ("ALERT", 1);
	}*/
	
	function FileSizeCheck($FileSize,$LimitSize){
		if($FileSize > $LimitSize){
			$LimitSize = number_format($LimitSize);
			ALERT ('파일용량은 $LimitSize Byte 이하로 제한 합니다.');
		}
	}

	// FileTailName Check
	//$TailName = "php|phtm|inc|class|htm|shtm|pl|cgi|ztx|dot";	# 업로드 금지 파일
	Function FileNameCheck($FileName, $TailName){
		if (preg_match("/\.($TailName)/i", $FileName)){
			ALERT ("업로드하실 수 없는 확장자 및 파일명입니다.");
		}
	}

	// File Upload
	function FileUpload($File, $FileUploadName){
		if(move_uploaded_file($File, $FileUploadName)){
			echo "$FileName : $FileRename 업로드";
		}else{
			ALERT ("업로드에 문제가 발생하였습니다. 다시 업로드 해주시기 바랍니다.");
		}
	}

	#-- Function Info [E]  -------------------------------------#
	

	#-- File Upload [S]  ---------------------------------------#
	for($i=0; $i < sizeof($FileName); $i++)
	{
		if($FileName[$i]){
			
			// 파일 사이즈 체크
			FileSizeCheck($FileSize[$i], 2000000);

			// 업로드 금지 파일 체크
			$TailName = "php|phtm|inc|class|htm|shtm|pl|cgi|ztx|dot";	# 제한확장자
			FileNameCheck($FileName[$i], $TailName);
			
			$tail = strrchr($FileName[$i], ".");
			$FileRename[$i] = time()."_".$i.$tail;

			// Save To Folder And Check
			$FileUploadName[$i] = $DataLink.$FileRename[$i];
			FileUpload($File[$i], $FileUploadName[$i]);
		}
	}
	#-- File Upload [E]  ---------------------------------------#

	
	if($idx == "" || $act == "modify")
	{
		//if($_POST[send_type] == "2" && $_POST[status] == "1")	$_POST[status] = "4";		// 승인요청 status 변경 (1.발행 2.임시발행 3.발행취소 4.승인요청 5.승인거부 6.승인취소)
		
		$SQL = "
		INSERT INTO 
			tax_sales 
		SET 
			publish_type		='$_POST[publish_type]',
			tax_type			='$_POST[tax_type]',
			numbering_k			='$_POST[numbering_k]',
			numbering_h			='$_POST[numbering_h]',
			numbering			='$_POST[numbering]',
			s_company_number	='$_POST[s_company_number]',
			s_company_j			='$_POST[s_company_j]',
			s_company_name		='$_POST[s_company_name]',
			s_name				='$_POST[s_name]',
			s_address			='$_POST[s_address]',
			s_state				='$_POST[s_state]',
			s_items				='$_POST[s_item]',
			s_personin			='$_POST[s_personin]',
			s_tel				='$_POST[s_tel]',
			s_email				='$s_email',
			r_company_number	='$_POST[r_company_number]',
			r_company_j			='$_POST[r_company_j]',
			r_company_name		='$_POST[r_company_name]',
			r_name				='$_POST[r_name]',
			r_address			='$_POST[r_address]',
			r_state				='$_POST[r_state]',
			r_items				='$_POST[r_item]',
			r_personin			='$_POST[r_personin]',
			r_tel				='$_POST[r_tel]',
			r_email				='$r_email',
			company_j			='$_POST[company_j]',
			tax_per				='$_POST[tax_per]',
			marking				='$_POST[marking]',
			supply_price		='$_POST[supply_price]',
			tax_price			='$_POST[tax_price]',
			total_price			='$_POST[total_price]',
			cash				='$_POST[cash]',
			cheque				='$_POST[cheque]',
			pro_note			='$_POST[pro_note]',
			outstanding			='$_POST[outstanding]',
			claim_kind			='$_POST[claim_kind]',
			signdate			='$_POST[signdate]',
			re_signdate			=now(),
			send_type			='$_POST[send_type]',
			sms_chk				='$_POST[sms_chk]',
			sms_number			='$sms_number',
			fax_chk				='$_POST[fax_chk]',
			fax_number			='$fax_number',
			memo				='$_POST[memo]',
			document_type		='$_POST[document_type]',
			m_kind				='$_POST[m_kind]',
			status				='7'
		";
		//echo $SQL."<br><br>";
		$db->query($SQL);

		$SQL_C = "SELECT idx FROM tax_sales ORDER BY idx DESC LIMIT 1";
		$db->query($SQL_C);
		$db->fetch();
		$p_idx = $db->dt[idx];
		//echo $p_idx;

		for($i=1; $i <= sizeof($_POST[p_price]); $i++)
		{
			if($p_price[$i] > 0)
			{
			$t_mon_c = str_pad($t_mon[$i], 2, "0", STR_PAD_LEFT);
			$t_day_c = str_pad($t_day[$i], 2, "0", STR_PAD_LEFT);
				$SQL_2 = "
				INSERT INTO 
					tax_sales_detail 
				SET
					p_idx = '$p_idx',
					t_mon = '$t_mon_c',
					t_day = '$t_day_c',
					product = '$product[$i]',
					p_size = '$p_size[$i]',
					cnt = '$cnt[$i]',
					price = '$price[$i]',
					p_price = '$p_price[$i]',
					tax = '$tax[$i]',
					comment = '$comment[$i]',
					signdate = now()
				";
				//echo $SQL_2."<br>";
				$db->query($SQL_2);
			}
		}


		# 파일정보 
		for($y=0; $y < sizeof($FileName); $y++)
		{
			if($FileName[$y] != "")
			{
				$SQL3 = "
				INSERT INTO 
					tax_datafile 
				SET 
					p_idx = '$p_idx',
					file = '$FileName[$y]',
					file_rename = '$FileRename[$y]',
					signdate = now();
				";
				$db->query($SQL3);
			}
		}
		
		
		/*팝빌 연동*/
		include("popbill/common.php");
		
		
		//임시저장이나 발행이나 popbill에 임시문서로 우선 저장 되어야 하기에 조건 없이 임시저장을 진행 함
		
		$Taxinvoice = new Taxinvoice();
		
		if($_POST[publish_type] == '1'){
			$publish_type = "정발행";
			$chargeDirection = "정과금";
		}else if($_POST[publish_type] == '2'){
			$publish_type = "역발행";
			$chargeDirection = "역과금";
		}else if($_POST[publish_type] == '3'){
			$publish_type = "위수탁";
		}
		if($_POST[claim_kind] == '1'){
			$claim_kind = "영수";
		}else if($_POST[claim_kind] == '2'){
			$claim_kind = "청구";
		}
		if($_POST[tax_per] == '1'){
			$tax_per = "과세";
		}else if($_POST[tax_per] == '2'){
			$tax_per = "영세";
		}else if($_POST[tax_per] == '3'){
			$tax_per = "면세";
		}
		
		$s_company_number = str_replace('-','',$_POST[s_company_number]);
		$r_company_number = str_replace('-','',$_POST[r_company_number]);
		$signdate = str_replace('-','',$_POST[signdate]);
		
		//임시 테스트용 사업자 번호 
		//$s_company_number = "2148868761";
		
		
		$Taxinvoice->writeDate = element($signdate,'');
		$Taxinvoice->issueType = element($publish_type,'');
		$Taxinvoice->chargeDirection = element($chargeDirection,'');
		$Taxinvoice->purposeType = element($claim_kind,'');
		$Taxinvoice->taxType = element($tax_per,'');
		$Taxinvoice->issueTiming = '직접발행';

		$Taxinvoice->invoicerCorpNum = element($s_company_number,'');
		$Taxinvoice->invoicerCorpName = element($_POST[s_company_name],'');
		if($_POST[publish_type] != '2'){
		$Taxinvoice->invoicerMgtKey = element($p_idx,'');
		}
		$Taxinvoice->invoicerCEOName = element($_POST[s_name],'');
		$Taxinvoice->invoicerAddr = element($_POST[s_address],'');
		$Taxinvoice->invoicerContactName = element($_POST[s_personin],'');
		$Taxinvoice->invoicerEmail = element($s_email,'');
		$Taxinvoice->invoicerTEL = element($_POST[s_tel],'');
		$Taxinvoice->invoicerHP = '';
		$Taxinvoice->invoicerBizClass = element($_POST[s_item],'');
		$Taxinvoice->invoicerBizType = element($_POST[s_state],'');
		if($_POST[publish_type] != '2'){
		$Taxinvoice->invoicerSMSSendYN = true;
		}else{
		$Taxinvoice->invoicerSMSSendYN = false;
		}
		$Taxinvoice->invoiceeType = '사업자';
		$Taxinvoice->invoiceeCorpNum = element($r_company_number,'');
		$Taxinvoice->invoiceeCorpName = element($_POST[r_company_name],'');
		if($_POST[publish_type] == '2'){
		$Taxinvoice->invoiceeMgtKey = element($p_idx,'');
		}
		$Taxinvoice->invoiceeCEOName = element($_POST[r_name],'');
		$Taxinvoice->invoiceeAddr = element($_POST[r_address],'');
		$Taxinvoice->invoiceeContactName1 = element($_POST[r_personin],'');
		$Taxinvoice->invoiceeEmail1 = element($r_email,'');
		$Taxinvoice->invoiceeTEL1 = element($_POST[r_tel],'');
		$Taxinvoice->invoiceeHP1 = '';
		$Taxinvoice->invoiceeBizClass = element($_POST[r_item],'');
		$Taxinvoice->invoiceeBizType = element($_POST[r_state],'');
		if($_POST[publish_type] == '2'){
		$Taxinvoice->invoiceeSMSSendYN = true;
		}else{
		$Taxinvoice->invoiceeSMSSendYN = false;
		}
		$Taxinvoice->supplyCostTotal = element($_POST[supply_price],'');
		$Taxinvoice->taxTotal = element($_POST[tax_price],'0');
		$Taxinvoice->totalAmount = element($_POST[total_price],'');
		
		if($_POST[act] == 'modify'){
		$Taxinvoice->modifyCode = element((int)$_POST[m_kind],'');//수정사유코드
		//$Taxinvoice->orgNTSConfirmNum = element($_POST[national_tax_no],'');//국세청승인번호
		}
		
		$Taxinvoice->originalTaxinvoiceKey = element($_POST[popbill_tax_no],'');//팝빌승인번호

		
		$Taxinvoice->serialNum = element($_POST[numbering],'');
		$Taxinvoice->cash = element($_POST[cash],'');
		$Taxinvoice->chkBill = element($_POST[cheque],'');
		$Taxinvoice->note = element($_POST[pro_note],'');
		$Taxinvoice->credit = element($_POST[outstanding],'');
		$Taxinvoice->remark1 = element($_POST[marking],'');
		$Taxinvoice->remark2 = element($_POST[marking2],'');
		$Taxinvoice->remark3 = element($_POST[marking3],'');
		$Taxinvoice->kwon = element((int)$_POST[numbering_k],'0');
		$Taxinvoice->ho = element((int)$_POST[numbering_h],'0');

		$Taxinvoice->businessLicenseYN = false;
		$Taxinvoice->bankBookYN = false;
		$Taxinvoice->faxreceiveNum = null;
		$Taxinvoice->faxsendYN = false;

		$Taxinvoice->detailList = array();

		$Taxinvoice->detailList[] = new TaxinvoiceDetail();
		
		//print_r($Taxinvoice);
		$z = 0;
		for($i=1; $i <= sizeof($_POST[p_price]); $i++){
			if($p_price[$i] > 0 || $p_price[$i] !=''){
				
				
				if($t_mon[$i] !='' && $t_day[$i] !=''){
					$ydate = date('Y');
					
					$t_mon_c = str_pad($t_mon[$i], 2, "0", STR_PAD_LEFT);
					$t_day_c = str_pad($t_day[$i], 2, "0", STR_PAD_LEFT);
					
					$purchaseDT = $ydate.$t_mon_c.$t_day_c;
				}
				
				$Taxinvoice->detailList[$z]->serialNum = element($i,'');
				$Taxinvoice->detailList[$z]->purchaseDT = element($purchaseDT,'');
				$Taxinvoice->detailList[$z]->itemName = element($product[$i],'');
				$Taxinvoice->detailList[$z]->spec = element($p_size[$i],'');
				$Taxinvoice->detailList[$z]->qty = element($cnt[$i],'');
				$Taxinvoice->detailList[$z]->unitCost = element($price[$i],'');
				$Taxinvoice->detailList[$z]->supplyCost = element($p_price[$i],'');
				$Taxinvoice->detailList[$z]->tax = element($tax[$i],'');
				$Taxinvoice->detailList[$z]->remark = element($comment[$i],'');
				
				$z ++;
			}
		}
		//var_dump($Taxinvoice);
		//print_r($Taxinvoice);
		//exit;
		
		try {
			if($_POST[publish_type] == '1'){
				$result = $TaxinvoiceService->Register($s_company_number,$Taxinvoice,$_SESSION[admin_config][popbill_id],false);
			}else if($_POST[publish_type] == '2'){
				$result = $TaxinvoiceService->Register($r_company_number,$Taxinvoice,$_SESSION[admin_config][popbill_id],false);
			}else{
				$result = $TaxinvoiceService->Register($s_company_number,$Taxinvoice,$_SESSION[admin_config][popbill_id],false);
			}
			if($result->code=="1"){
			//echo $status;
			//exit;
				if($status == "0"){
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
					
					if($m_kind == 1){// 수정세금계산서 기재사항착오정정으로 인한 -계산서 자동 발행 프로세스
						getModifyOriTax();
					}
						
					echo "<script>alert ('임시발행 완료.'); parent.location.href='./sales_list_3.php';</script>";
					exit;
				}else if($status == "1" || $status == "3"){
					// 세금계산서를 바로 발행 할 경우 임시 저장 후 발행 프로세스 
					
					try {
						
						if($_POST[publish_type] == '1'	){//정발행
							$result = $TaxinvoiceService->Issue($s_company_number,ENumMgtKeyType::SELL,$p_idx,$_POST[s_company_name].' : 발행내역',null,true,null);
							
							if($status == "3"){ // 국세청 즉시 발행 일 경우 
								try {
									$result = $TaxinvoiceService->SendToNTS($s_company_number,ENumMgtKeyType::SELL,$p_idx,$_SESSION[admin_config][popbill_id]);
								}catch(PopbillException $pe) {
									echo "<script>alert ('".$pe->getMessage()."');</script>";
									echo '['.$pe->getCode().'] '.$pe->getMessage();
									exit;
								}
							}
							
						}else if($_POST[publish_type] == '2'){//역발행
							$result = $TaxinvoiceService->Request($r_company_number,ENumMgtKeyType::BUY,$p_idx,$_POST[s_company_name].' : 역)발행요청',$_SESSION[admin_config][popbill_id]);
							
							if($status == "3"){ // 국세청 즉시 발행 일 경우 
								try {
									$result = $TaxinvoiceService->SendToNTS($r_company_number,ENumMgtKeyType::BUY,$p_idx,$_SESSION[admin_config][popbill_id]);
								}catch(PopbillException $pe) {
									echo "<script>alert ('".$pe->getMessage()."');</script>";
									echo '['.$pe->getCode().'] '.$pe->getMessage();
									exit;
								}
							}
							
						}
						
						
						
						// 정상적으로 발행이 완료 될 경우 업데이트 
						$SQL = "
							update  
								tax_sales 
							SET 
								status ='1'
							where 
								idx = '$p_idx'
							";
							//echo $SQL."<br><br>";
							$db->query($SQL);
					
						if($m_kind == 1){// 수정세금계산서 기재사항착오정정으로 인한 -계산서 자동 발행 프로세스
							getModifyOriTax();
						}
						echo "<script>alert ('발행 완료.".$status."'); parent.location.href='./sales_list.php?publish_type=1';</script>";
						exit;
						//echo '['.$result->code.'] '.$result->message;
					}
					catch(PopbillException $pe) {
						echo "<script>alert ('".$pe->getMessage()."');</script>";
						echo '['.$pe->getCode().'] '.$pe->getMessage();
						
					}
					exit;
					
				}
			}else{
				echo "<script>alert('".$result->message."');</script>";
				exit;
			}
		}
		catch(PopbillException $pe) {
			echo "<script>alert ('".$pe->getMessage()."');</script>";
			echo '['.$pe->getCode().'] '.$pe->getMessage();
			//echo 1;
		//	echo "<script>alert([".$pe->getCode()."] ".$pe->getMessage().");</script>";
			exit;
		}
		
		
		
		// 권 호 모디 
		/*팝빌 연동 [끝]*/
		exit;
		
		if($status == "2")
		{
			echo "<script>alert ('등록되었습니다.'); parent.location.href='./sales_list2.php';</script>";
		}
		else
		{
			echo "<script>alert ('등록되었습니다.'); parent.location.href='./sales_list.php';</script>";
		}
		die;
	}
	else
	{
		
		/*팝빌 연동*/
		include_once("popbill/common.php");

		if($status == "1" || $status == "3"){
		//임시로 등록된 문서를 발행하는 프로세스
			//echo $signdate;
			if($signdate > date('Y-m-d')){
				if($_POST[publish_type] == '1'	){//정발행
				echo "<script>alert ('미래 세금계산서는 발행 하실 수 없습니다.\\n발행 가능 일자 : ".$signdate."'); parent.location.href='./sales_write.php?idx=$idx';</script>";
				}else if($_POST[publish_type] == '2'){//역발행
				echo "<script>alert ('미래 세금계산서는 발행 하실 수 없습니다.\\n발행 가능 일자 : ".$signdate."'); parent.location.href='./purchase_write.php?idx=$idx';</script>";
				}
				exit;
			}
		
			$s_company_number = str_replace('-','',$_POST[s_company_number]);
			$r_company_number = str_replace('-','',$r_company_number);
			//임시 테스트용 사업자 번호 
			//$s_company_number = "2148868761";
			//echo $s_company_number;
			try {
				/*
				* 발행을 위해서는 공인인증서가 등록되어 있어야 합니다.
				* 세금계산서의 발행은 공급자또는 수탁자에 의해서 이루어 지며, Issue함수의 parameter는 다음과 같다.
				* $CorpNum 		=> 행위자 사업자번호
				* $MgtKeyType 	=> 관리번호 종류, SELL(매출), BUY(매입), TRUSTEE(수탁) 중 1개 
				* $MgtKey 		=>  파트너 관리번호
				* $Memo 		=> 발행시 메모
				* $EmailSubject => 발행시 전달되는 메일의 제목을 변경하고자 할때 기재, 미기재시 기본 제목으로 전송.
				* $ForceIssue 	=> 지연발행건 강제발행여부. 해당 세금계산서의 가산세가 예상될 경우 발행되지 않고 오류를 반환한다.
				*					만약강제로 발행하고자 할 경우 true로 호출하면 발생가능하다.
				* $userid 		=> 팝빌 회원아이디
				*/
				//$result = $TaxinvoiceService->Issue('1231212312',ENumMgtKeyType::SELL,'123123','발행 메모',null,true,'userid');
				if($_POST[publish_type] == '1'	){//정발행
					$result = $TaxinvoiceService->Issue($s_company_number,ENumMgtKeyType::SELL,$idx,$_POST[s_company_name].' : 발행내역',null,true,null);
					
					if($status == "3"){ // 국세청 즉시 발행 일 경우 
						$result = $TaxinvoiceService->SendToNTS($s_company_number,ENumMgtKeyType::SELL,$idx,$_SESSION[admin_config][popbill_id]);
					}
				}else if($_POST[publish_type] == '2'){//역발행
					$result = $TaxinvoiceService->Request($r_company_number,ENumMgtKeyType::BUY,$idx,$_POST[s_company_name].' : 역)발행요청',$_SESSION[admin_config][popbill_id]);
					
					if($status == "3"){ // 국세청 즉시 발행 일 경우 
						$result = $TaxinvoiceService->SendToNTS($r_company_number,ENumMgtKeyType::BUY,$idx,$_SESSION[admin_config][popbill_id]);
					}
				}
				
				//$result = $TaxinvoiceService->Issue($s_company_number,ENumMgtKeyType::SELL,$idx,$_POST[s_company_name].' : 발행내역',null,true,null);
				
				// 발행이 완료 되면 DB의 발행 상태를 업데이트 한다.
				$SQL = "
					UPDATE 
						tax_sales 
					SET 
						status	='1'
					WHERE 
						idx = '$idx'
					";
					//echo $SQL."<br><br>";
					$db->query($SQL);
				
				echo "<script>alert ('발행 완료.'); parent.location.href='./sales_list.php?publish_type=1';</script>";
				exit;
				//echo '['.$result->code.'] '.$result->message;
			}
			catch(PopbillException $pe) {
				echo "<script>alert ('".$pe->getMessage()."');</script>";
				echo '['.$pe->getCode().'] '.$pe->getMessage();
				echo 1;
			}
			exit;
		}else if($status == "0"){// status 0 : 임시 저장
			$Taxinvoice = new Taxinvoice();
			
			if($_POST[publish_type] == '1'){
				$publish_type = "정발행";
				$chargeDirection = "정과금";
			}else if($_POST[publish_type] == '2'){
				$publish_type = "역발행";
				$chargeDirection = "역과금";
			}else if($_POST[publish_type] == '3'){
				$publish_type = "위수탁";
			}
			if($_POST[claim_kind] == '1'){
				$claim_kind = '영수';
			}else if($_POST[claim_kind] == '2'){
				$claim_kind = '청구';
			}
			if($_POST[tax_per] == '1'){
				$tax_per = "과세";
			}else if($_POST[tax_per] == '2'){
				$tax_per = "영세";
			}else if($_POST[tax_per] == '3'){
				$tax_per = "면세";
			}
			
			$s_company_number = str_replace('-','',$_POST[s_company_number]);
			$r_company_number = str_replace('-','',$_POST[r_company_number]);
			$signdate = str_replace('-','',$_POST[signdate]);
			
			//임시 테스트용 사업자 번호 
			//$s_company_number = "2148868761";
			
			
			$Taxinvoice->writeDate = element($signdate,'');
			$Taxinvoice->issueType = element($publish_type,'');
			$Taxinvoice->chargeDirection = element($chargeDirection,'');
			$Taxinvoice->purposeType = element($claim_kind,'');
			$Taxinvoice->taxType = element($tax_per,'');
			$Taxinvoice->issueTiming = '직접발행';

			$Taxinvoice->invoicerCorpNum = element($s_company_number,'');
			$Taxinvoice->invoicerCorpName = element($_POST[s_company_name],'');
			$Taxinvoice->invoicerMgtKey = element($idx,'');
			$Taxinvoice->invoicerCEOName = element($_POST[s_name],'');
			$Taxinvoice->invoicerAddr = element($_POST[s_address],'');
			$Taxinvoice->invoicerContactName = element($_POST[s_personin],'');
			$Taxinvoice->invoicerEmail = element($s_email,'');
			$Taxinvoice->invoicerTEL = element($_POST[s_tel],'');
			$Taxinvoice->invoicerHP = '';
			$Taxinvoice->invoicerBizClass = element($_POST[s_item],'');
			$Taxinvoice->invoicerBizType = element($_POST[s_state],'');
			$Taxinvoice->invoicerSMSSendYN = false;

			$Taxinvoice->invoiceeType = '사업자';
			$Taxinvoice->invoiceeCorpNum = element($r_company_number,'');
			$Taxinvoice->invoiceeCorpName = element($_POST[r_company_name],'');
			$Taxinvoice->invoiceeCEOName = element($_POST[r_name],'');
			$Taxinvoice->invoiceeAddr = element($_POST[r_address],'');
			$Taxinvoice->invoiceeContactName1 = element($_POST[r_personin],'');
			$Taxinvoice->invoiceeEmail1 = element($r_email,'');
			$Taxinvoice->invoiceeTEL1 = element($_POST[r_tel],'');
			$Taxinvoice->invoiceeHP1 = '';
			$Taxinvoice->invoiceeBizClass = element($_POST[r_item],'');
			$Taxinvoice->invoiceeBizType = element($_POST[r_state],'');
			$Taxinvoice->invoiceeSMSSendYN = false;

			$Taxinvoice->supplyCostTotal = element($_POST[supply_price],'');
			$Taxinvoice->taxTotal = element($_POST[tax_price],'0');
			$Taxinvoice->totalAmount = element($_POST[total_price],'');

			$Taxinvoice->originalTaxinvoiceKey = '';
	
			
			$Taxinvoice->serialNum = element($_POST[numbering],'');
			
			$Taxinvoice->cash = element($_POST[cash],'');
			$Taxinvoice->chkBill = element($_POST[cheque],'');
			$Taxinvoice->note = element($_POST[pro_note],'');
			$Taxinvoice->credit = element($_POST[outstanding],'');
			
			$Taxinvoice->remark1 = element($_POST[marking],'');
			$Taxinvoice->remark2 = element($_POST[marking2],'');
			$Taxinvoice->remark3 = element($_POST[marking3],'');
			$Taxinvoice->kwon = element((int)$_POST[numbering_k],'0');
			$Taxinvoice->ho = element((int)$_POST[numbering_h],'0');

			$Taxinvoice->businessLicenseYN = false;
			$Taxinvoice->bankBookYN = false;
			$Taxinvoice->faxreceiveNum = null;
			$Taxinvoice->faxsendYN = false;

			$Taxinvoice->detailList = array();

			$Taxinvoice->detailList[] = new TaxinvoiceDetail();
			
			//print_r($product);
			$z = 0;
			for($i=1; $i <= sizeof($_POST[p_price]); $i++){
				if(!empty($product[$i])){
					
					
					if($t_mon[$i] !='' && $t_day[$i] !=''){
						$ydate = date('Y');
						$t_mon_c = str_pad($t_mon[$i], 2, "0", STR_PAD_LEFT);
						$t_day_c = str_pad($t_day[$i], 2, "0", STR_PAD_LEFT);
						$purchaseDT = $ydate.$t_mon_c.$t_day_c;
					}
					
					$Taxinvoice->detailList[$z]->serialNum = element($i,'');
					$Taxinvoice->detailList[$z]->purchaseDT = element($purchaseDT,'');
					$Taxinvoice->detailList[$z]->itemName = element($product[$i],'');
					$Taxinvoice->detailList[$z]->spec = element($p_size[$i],'');
					$Taxinvoice->detailList[$z]->qty = element($cnt[$i],'');
					$Taxinvoice->detailList[$z]->unitCost = element($price[$i],'');
					$Taxinvoice->detailList[$z]->supplyCost = element($p_price[$i],'');
					$Taxinvoice->detailList[$z]->tax = element($tax[$i],'0');
					$Taxinvoice->detailList[$z]->remark = element($comment[$i],'');
					
					$z ++;
				}
			}
			//var_dump($Taxinvoice);
			//print_r($Taxinvoice);
			//exit;
			try {
				//$result = $TaxinvoiceService->Register($s_company_number,$Taxinvoice,null,false);
				if($_POST[publish_type] == '1'	){//정발행
					$result = $TaxinvoiceService->Update($s_company_number,ENumMgtKeyType::SELL,$idx,$Taxinvoice,null,false);
				}else if($_POST[publish_type] == '2'){//역발행
					$result = $TaxinvoiceService->Update($r_company_number,ENumMgtKeyType::BUY,$idx,$Taxinvoice,null,false);
				}
				//print_R($result);
				//echo $result->message;
				//exit;
				
				if($result->code != 1){
					echo "<script>alert ('".$result->message."');</script>";
					exit;
				}
				
				// 저장이 성공적으로 바로빌에 들어갈 경우 내부 DB 업데이트 처리 
				$SQL = "
					UPDATE 
						tax_sales 
					SET 
						publish_type		='$_POST[publish_type]',
						tax_type			='$_POST[tax_type]',
						numbering_k			='$_POST[numbering_k]',
						numbering_h			='$_POST[numbering_h]',
						numbering			='$_POST[numbering]',
						s_company_number	='$_POST[s_company_number]',
						s_company_j			='$_POST[s_company_j]',
						s_company_name		='$_POST[s_company_name]',
						s_name				='$_POST[s_name]',
						s_address			='$_POST[s_address]',
						s_state				='$_POST[s_state]',
						s_items				='$_POST[s_item]',
						s_personin			='$_POST[s_personin]',
						s_tel				='$_POST[s_tel]',
						s_email				='$s_email',
						r_company_number	='$_POST[r_company_number]',
						r_company_j			='$_POST[r_company_j]',
						r_company_name		='$_POST[r_company_name]',
						r_name				='$_POST[r_name]',
						r_address			='$_POST[r_address]',
						r_state				='$_POST[r_state]',
						r_items				='$_POST[r_item]',
						r_personin			='$_POST[r_personin]',
						r_tel				='$_POST[r_tel]',
						r_email				='$r_email',
						company_j			='$_POST[company_j]',
						tax_per				='$_POST[tax_per]',
						marking				='$_POST[marking]',
						supply_price		='$_POST[supply_price]',
						tax_price			='$_POST[tax_price]',
						total_price			='$_POST[total_price]',
						cash				='$_POST[cash]',
						cheque				='$_POST[cheque]',
						pro_note			='$_POST[pro_note]',
						outstanding			='$_POST[outstanding]',
						claim_kind			='$_POST[claim_kind]',
						signdate			='$_POST[signdate]',
						re_signdate			=now(),
						send_type			='$_POST[send_type]',
						sms_chk				='$_POST[sms_chk]',
						sms_number			='$sms_number',
						fax_chk				='$_POST[fax_chk]',
						fax_number			='$fax_number',
						memo				='$_POST[memo]',
						status				='$_POST[status]'
					WHERE 
						idx = '$idx'
					";
					//echo $SQL."<br><br>";
					$db->query($SQL);


					for($z=1; $z <= sizeof($p_price); $z++)
					{
					
						if($p_price[$z] != "")
						{
							//echo "idx2 ::::: ".$idx2[$z-1]."<br><br>";
							
						if($idx2[$z-1] == "")
							{
							
							$t_mon_c = str_pad($t_mon[$z], 2, "0", STR_PAD_LEFT);
							$t_day_c = str_pad($t_day[$z], 2, "0", STR_PAD_LEFT);
							
							//	echo "-------<br>";
								$SQL_2 = "
								INSERT INTO 
									tax_sales_detail 
								SET
									p_idx = '$idx',
									t_mon = '$t_mon_c',
									t_day = '$t_day_c',
									product = '$product[$z]',
									p_size = '$p_size[$z]',
									cnt = '$cnt[$z]',
									price = '$price[$z]',
									p_price = '$p_price[$z]',
									tax = '$tax[$z]',
									comment = '$comment[$z]',
									signdate = now()
								";
								echo $SQL_2."<br>";
								$db->query($SQL_2);
							}
							else if($idx2[$z-1] != '')
							{
								$t_mon_c = str_pad($t_mon[$z], 2, "0", STR_PAD_LEFT);
								$t_day_c = str_pad($t_day[$z], 2, "0", STR_PAD_LEFT);
								$SQL_3 = "
								UPDATE
									tax_sales_detail 
								SET
									t_mon = '$t_mon_c',
									t_day = '$t_day_c',
									product = '$product[$z]',
									p_size = '$p_size[$z]',
									cnt = '$cnt[$z]',
									price = '$price[$z]',
									p_price = '$p_price[$z]',
									tax = '$tax[$z]',
									comment = '$comment[$z]'
								WHERE 
									idx = '".$idx2[$z-1]."'
									
								";
								echo $SQL_3."<br>";
								$db->query($SQL_3);
							}

							//echo "<br>00<br>";
						}
					//	echo "<br>11<br>";
					}

					# 파일정보 
					for($y=0; $y < sizeof($FileName); $y++)
					{
						if($FileName[$y] != "")
						{
							$SQL3 = "
							INSERT INTO 
								tax_datafile 
							SET 
								p_idx = '$idx',
								file = '$FileName[$y]',
								file_rename = '$FileRename[$y]',
								signdate = now();
							";
							$db->query($SQL3);
						}
					}
				
				
				echo "<script>alert ('수정되었습니다.'); parent.location.href='./sales_list_3.php';</script>";
				exit;
			}
			catch(PopbillException $pe) {
				echo "<script>alert ('".$pe->getMessage()."');</script>";
				echo '['.$pe->getCode().'] '.$pe->getMessage();
				//echo "<script>alert([".$pe->getCode()."] ".$pe->getMessage().")</script>";
			}
		}
		// 권 호 모디 
		/*팝빌 연동 [끝]*/
		exit;

		if($status == "2")
		{
			echo "<script>alert ('수정되었습니다.'); parent.location.reload();</script>";
		}
		else
		{
			echo "<script>alert ('발행되었습니다..'); parent.location.href='./sales_list.php';</script>";
		}
		die;
	}

function getModifyOriTax(){
	global $db, $_POST, $_SESSION;
	
	$SQL = "
		INSERT INTO 
			tax_sales 
		SET 
			publish_type		='$_POST[ori_publish_type]',
			tax_type			='$_POST[ori_tax_type]',
			numbering_k			='$_POST[ori_numbering_k]',
			numbering_h			='$_POST[ori_numbering_h]',
			numbering			='$_POST[ori_numbering]',
			s_company_number	='$_POST[ori_s_company_number]',
			s_company_j			='$_POST[ori_s_company_j]',
			s_company_name		='$_POST[ori_s_company_name]',
			s_name				='$_POST[ori_s_name]',
			s_address			='$_POST[ori_s_address]',
			s_state				='$_POST[ori_s_state]',
			s_items				='$_POST[ori_s_items]',
			s_personin			='$_POST[ori_s_personin]',
			s_tel				='$_POST[ori_s_tel]',
			s_email				='$_POST[ori_s_email]',
			r_company_number	='$_POST[ori_r_company_number]',
			r_company_j			='$_POST[ori_r_company_j]',
			r_company_name		='$_POST[ori_r_company_name]',
			r_name				='$_POST[ori_r_name]',
			r_address			='$_POST[ori_r_address]',
			r_state				='$_POST[ori_r_state]',
			r_items				='$_POST[ori_r_items]',
			r_personin			='$_POST[ori_r_personin]',
			r_tel				='$_POST[ori_r_tel]',
			r_email				='$_POST[ori_r_email]',
			company_j			='$_POST[ori_company_j]',
			tax_per				='$_POST[ori_tax_per]',
			marking				='$_POST[ori_marking]',
			supply_price		='$_POST[ori_supply_price]',
			tax_price			='$_POST[ori_tax_price]',
			total_price			='$_POST[ori_total_price]',
			cash				='$_POST[ori_cash]',
			cheque				='$_POST[ori_cheque]',
			pro_note			='$_POST[ori_pro_note]',
			outstanding			='$_POST[ori_outstanding]',
			claim_kind			='$_POST[ori_claim_kind]',
			signdate			='$_POST[ori_signdate]',
			re_signdate			=now(),
			send_type			='$_POST[ori_send_type]',
			sms_chk				='$_POST[ori_sms_chk]',
			sms_number			='$_POST[ori_sms_number]',
			fax_chk				='$_POST[ori_fax_chk]',
			fax_number			='$_POST[ori_fax_number]',
			memo				='$_POST[ori_memo]',
			document_type		='$_POST[document_type]',
			status				='7'
		";
		//echo $SQL."<br><br>";
		$db->query($SQL);

		$SQL_C = "SELECT idx FROM tax_sales ORDER BY idx DESC LIMIT 1";
		$db->query($SQL_C);
		$db->fetch();
		$p_idx = $db->dt[idx];
		//echo $p_idx;


		for($i=0; $i < sizeof($_POST[ori_detail_p_price]); $i++)
		{
			
			$t_mon_c = str_pad($t_mon[$i], 2, "0", STR_PAD_LEFT);
			$t_day_c = str_pad($t_day[$i], 2, "0", STR_PAD_LEFT);
				$SQL_2 = "
				INSERT INTO 
					tax_sales_detail 
				SET
					p_idx = '$p_idx',
					t_mon = '$t_mon_c',
					t_day = '$t_day_c',
					product = '$product[$i]',
					p_size = '$p_size[$i]',
					cnt = '$cnt[$i]',
					price = '$price[$i]',
					p_price = '$p_price[$i]',
					tax = '$tax[$i]',
					comment = '$comment[$i]',
					signdate = now()
				";
				//echo $SQL_2."<br>";
				$db->query($SQL_2);
			
		}
		
		/*팝빌 연동*/
		include("popbill/common.php");
		
		
		//임시저장이나 발행이나 popbill에 임시문서로 우선 저장 되어야 하기에 조건 없이 임시저장을 진행 함
		
		$Taxinvoice = new Taxinvoice();
		
		if($_POST[ori_publish_type] == '1'){
			$publish_type = "정발행";
			$chargeDirection = "정과금";
		}else if($_POST[ori_publish_type] == '2'){
			$publish_type = "역발행";
			$chargeDirection = "역과금";
		}else if($_POST[ori_publish_type] == '3'){
			$publish_type = "위수탁";
		}
		if($_POST[ori_claim_kind] == '1'){
			$claim_kind = "영수";
		}else if($_POST[ori_claim_kind] == '2'){
			$claim_kind = "청구";
		}
		if($_POST[ori_tax_per] == '1'){
			$tax_per = "과세";
		}else if($_POST[ori_tax_per] == '2'){
			$tax_per = "영세";
		}else if($_POST[ori_tax_per] == '3'){
			$tax_per = "면세";
		}
		
		$s_company_number = str_replace('-','',$_POST[ori_s_company_number]);
		$r_company_number = str_replace('-','',$_POST[ori_r_company_number]);
		$signdate = str_replace('-','',$_POST[ori_signdate]);
		
		//임시 테스트용 사업자 번호 
		//$s_company_number = "2148868761";
		
		
		$Taxinvoice->writeDate = element($signdate,'');
		$Taxinvoice->issueType = element($publish_type,'');
		$Taxinvoice->chargeDirection = element($chargeDirection,'');
		$Taxinvoice->purposeType = element($claim_kind,'');
		$Taxinvoice->taxType = element($tax_per,'');
		$Taxinvoice->issueTiming = '직접발행';

		$Taxinvoice->invoicerCorpNum = element($s_company_number,'');
		$Taxinvoice->invoicerCorpName = element($_POST[ori_s_company_name],'');
		if($_POST[ori_publish_type] != '2'){
		$Taxinvoice->invoicerMgtKey = element($p_idx,'');
		}
		$Taxinvoice->invoicerCEOName = element($_POST[ori_s_name],'');
		$Taxinvoice->invoicerAddr = element($_POST[ori_s_address],'');
		$Taxinvoice->invoicerContactName = element($_POST[ori_s_personin],'');
		$Taxinvoice->invoicerEmail = element($s_email,'');
		$Taxinvoice->invoicerTEL = element($_POST[ori_s_tel],'');
		$Taxinvoice->invoicerHP = '';
		$Taxinvoice->invoicerBizClass = element($_POST[ori_s_item],'');
		$Taxinvoice->invoicerBizType = element($_POST[ori_s_state],'');
		if($_POST[ori_publish_type] != '2'){
		$Taxinvoice->invoicerSMSSendYN = true;
		}else{
		$Taxinvoice->invoicerSMSSendYN = false;
		}
		$Taxinvoice->invoiceeType = '사업자';
		$Taxinvoice->invoiceeCorpNum = element($r_company_number,'');
		$Taxinvoice->invoiceeCorpName = element($_POST[ori_r_company_name],'');
		if($_POST[ori_publish_type] == '2'){
		$Taxinvoice->invoiceeMgtKey = element($p_idx,'');
		}
		$Taxinvoice->invoiceeCEOName = element($_POST[ori_r_name],'');
		$Taxinvoice->invoiceeAddr = element($_POST[ori_r_address],'');
		$Taxinvoice->invoiceeContactName1 = element($_POST[ori_r_personin],'');
		$Taxinvoice->invoiceeEmail1 = element($r_email,'');
		$Taxinvoice->invoiceeTEL1 = element($_POST[ori_r_tel],'');
		$Taxinvoice->invoiceeHP1 = '';
		$Taxinvoice->invoiceeBizClass = element($_POST[ori_r_item],'');
		$Taxinvoice->invoiceeBizType = element($_POST[ori_r_state],'');
		if($_POST[ori_publish_type] == '2'){
		$Taxinvoice->invoiceeSMSSendYN = true;
		}else{
		$Taxinvoice->invoiceeSMSSendYN = false;
		}
		$Taxinvoice->supplyCostTotal = element($_POST[ori_supply_price],'');
		$Taxinvoice->taxTotal = element($_POST[ori_tax_price],'0');
		$Taxinvoice->totalAmount = element($_POST[ori_total_price],'');
		
		if($_POST[act] == 'modify'){
		$Taxinvoice->modifyCode = element((int)$_POST[m_kind],'');//수정사유코드
		//$Taxinvoice->orgNTSConfirmNum = element($_POST[national_tax_no],'');//국세청승인번호
		}
		
		$Taxinvoice->originalTaxinvoiceKey = element($_POST[ori_popbill_tax_no],'');//팝빌승인번호

		
		$Taxinvoice->serialNum = element($_POST[ori_numbering],'');
		$Taxinvoice->cash = element($_POST[ori_cash],'');
		$Taxinvoice->chkBill = element($_POST[ori_cheque],'');
		$Taxinvoice->note = element($_POST[ori_pro_note],'');
		$Taxinvoice->credit = element($_POST[ori_outstanding],'');
		$Taxinvoice->remark1 = element($_POST[ori_marking],'');
		$Taxinvoice->remark2 = element($_POST[ori_marking2],'');
		$Taxinvoice->remark3 = element($_POST[ori_marking3],'');
		$Taxinvoice->kwon = element((int)$_POST[ori_numbering_k],'0');
		$Taxinvoice->ho = element((int)$_POST[ori_numbering_h],'0');

		$Taxinvoice->businessLicenseYN = false;
		$Taxinvoice->bankBookYN = false;
		$Taxinvoice->faxreceiveNum = null;
		$Taxinvoice->faxsendYN = false;

		$Taxinvoice->detailList = array();

		$Taxinvoice->detailList[] = new TaxinvoiceDetail();
		
		//print_r($Taxinvoice);
		$z = 0;
		for($i=0; $i < sizeof($_POST[ori_detail_p_price]); $i++){
						
			$ydate = date('Y');
			$purchaseDT = $ydate.$_POST[ori_detail_t_mon][$i].$_POST[ori_detail_t_day][$i];
			
				
			$Taxinvoice->detailList[$z]->serialNum = element($i+1,'');
			$Taxinvoice->detailList[$z]->purchaseDT = element($purchaseDT,'');
			$Taxinvoice->detailList[$z]->itemName = element($_POST[ori_detail_product][$i],'');
			$Taxinvoice->detailList[$z]->spec = element($_POST[ori_detail_p_size][$i],'');
			$Taxinvoice->detailList[$z]->qty = element($_POST[ori_detail_cnt][$i],'');
			$Taxinvoice->detailList[$z]->unitCost = element($_POST[ori_detail_price][$i],'');
			$Taxinvoice->detailList[$z]->supplyCost = element($_POST[ori_detail_p_price][$i],'');
			$Taxinvoice->detailList[$z]->tax = element($_POST[ori_detail_tax][$i],'');
			$Taxinvoice->detailList[$z]->remark = element($_POST[ori_detail_comment][$i],'');
			
			$z ++;
			
		}
		//var_dump($Taxinvoice);
		//print_r($Taxinvoice);
		//exit;
		
		try {
			if($_POST[publish_type] == '1'){
				$result = $TaxinvoiceService->Register($s_company_number,$Taxinvoice,$_SESSION[admin_config][popbill_id],false);
			}else if($_POST[publish_type] == '2'){
				$result = $TaxinvoiceService->Register($r_company_number,$Taxinvoice,$_SESSION[admin_config][popbill_id],false);
			}else{
				$result = $TaxinvoiceService->Register($s_company_number,$Taxinvoice,$_SESSION[admin_config][popbill_id],false);
			}
			//echo $status;
			//exit;
			if($_POST[status] == "0"){
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
				echo $result->message;
				return true;
			}else if($_POST[status] == "1" || $_POST[status] == "3"){
				// 세금계산서를 바로 발행 할 경우 임시 저장 후 발행 프로세스 
				
				try {
					
					if($_POST[publish_type] == '1'	){//정발행
						$result = $TaxinvoiceService->Issue($s_company_number,ENumMgtKeyType::SELL,$p_idx,$_POST[s_company_name].' : 발행내역',null,true,null);
						
						if($_POST[status] == "3"){ // 국세청 즉시 발행 일 경우 
							$result = $TaxinvoiceService->SendToNTS($s_company_number,ENumMgtKeyType::SELL,$p_idx,$_SESSION[admin_config][popbill_id]);
						}
						
					}else if($_POST[publish_type] == '2'){//역발행
						$result = $TaxinvoiceService->Request($r_company_number,ENumMgtKeyType::BUY,$p_idx,$_POST[s_company_name].' : 역)발행요청',$_SESSION[admin_config][popbill_id]);
						
						if($_POST[status] == "3"){ // 국세청 즉시 발행 일 경우 
							$result = $TaxinvoiceService->SendToNTS($r_company_number,ENumMgtKeyType::BUY,$p_idx,$_SESSION[admin_config][popbill_id]);
						}
						
					}
					
					
					
					// 정상적으로 발행이 완료 될 경우 업데이트 
					$SQL = "
						update  
							tax_sales 
						SET 
							status ='1'
						where 
							idx = '$p_idx'
						";
						//echo $SQL."<br><br>";
						$db->query($SQL);
					echo $result->message;
					return true;
					//echo '['.$result->code.'] '.$result->message;
				}
				catch(PopbillException $pe) {
					echo '['.$pe->getCode().'] '.$pe->getMessage();
					
				}
				
				
			}
		}
		catch(PopbillException $pe) {
			echo '['.$pe->getCode().'] '.$pe->getMessage();
			//echo 1;
		//	echo "<script>alert([".$pe->getCode()."] ".$pe->getMessage().");</script>";
			exit;
		}
		

}	
?>