<?php

include 'common.php';


echo '세금계산서 수정 테스트'.chr(10).'처리결과 : ' ;

$Taxinvoice = new Taxinvoice();

$Taxinvoice->writeDate = '20140410';
$Taxinvoice->issueType = '정발행';
$Taxinvoice->chargeDirection = '정과금';
$Taxinvoice->purposeType = '영수';
$Taxinvoice->taxType = '과세';
$Taxinvoice->issueTiming = '직접발행';

$Taxinvoice->invoicerCorpNum = '1231212312';
$Taxinvoice->invoicerCorpName = '공급자상호';
$Taxinvoice->invoicerMgtKey = '123123';
$Taxinvoice->invoicerCEOName = '공급자 대표자성명';
$Taxinvoice->invoicerAddr = '공급자 주소';
$Taxinvoice->invoicerContactName = '공급자 담당자성명';
$Taxinvoice->invoicerEmail = 'tester@test.com';
$Taxinvoice->invoicerTEL = '070-0000-0000';
$Taxinvoice->invoicerHP = '010-0000-0000';
$Taxinvoice->invoicerSMSSendYN = false;

$Taxinvoice->invoiceeType = '사업자';
$Taxinvoice->invoiceeCorpNum = '8888888888';
$Taxinvoice->invoiceeCorpName = '공급받는자 상호';
$Taxinvoice->invoiceeCEOName = '공급받는자 대표자성명';
$Taxinvoice->invoiceeAddr = '공급받는자 주소';
$Taxinvoice->invoiceeContactName1 = '공급받는자 담당자성명';
$Taxinvoice->invoiceeEmail1 = 'tester@test.com';
$Taxinvoice->invoiceeTEL1 = '070-0000-0000';
$Taxinvoice->invoiceeHP1 = '010-0000-0000';
$Taxinvoice->invoiceeSMSSendYN = false;

$Taxinvoice->supplyCostTotal = '100000';
$Taxinvoice->taxTotal = '10000';
$Taxinvoice->totalAmount = '110000';

$Taxinvoice->originalTaxinvoiceKey = '';
$Taxinvoice->serialNum = '123';
$Taxinvoice->cash = '';
$Taxinvoice->chkBill = '';
$Taxinvoice->note = '';
$Taxinvoice->credit = '';
$Taxinvoice->remark1 = '비고1';
$Taxinvoice->remark2 = '비고2';
$Taxinvoice->remark3 = '비고3';
$Taxinvoice->kwon = '1';
$Taxinvoice->ho = '1';

$Taxinvoice->businessLicenseYN = false;
$Taxinvoice->bankBookYN = false;
$Taxinvoice->faxreceiveNum = '';
$Taxinvoice->faxsendYN = false;

$Taxinvoice->detailList = array();

$Taxinvoice->detailList[] = new TaxinvoiceDetail();
$Taxinvoice->detailList[0]->serialNum = 1;
$Taxinvoice->detailList[0]->purchaseDT = '20140410';
$Taxinvoice->detailList[0]->itemName = '품목명1번';
$Taxinvoice->detailList[0]->spec = '규격';
$Taxinvoice->detailList[0]->qty = '1';
$Taxinvoice->detailList[0]->unitCost = '100000';
$Taxinvoice->detailList[0]->supplyCost = '100000';
$Taxinvoice->detailList[0]->tax = '10000';
$Taxinvoice->detailList[0]->remark = '품목비고';

$Taxinvoice->detailList[] = new TaxinvoiceDetail();
$Taxinvoice->detailList[1]->serialNum = 2;
$Taxinvoice->detailList[1]->itemName = '품목명2번';


try {
	$result = $TaxinvoiceService->Update('1231212312',ENumMgtKeyType::SELL,'123123',$Taxinvoice,null,false);
	echo $result->message;
}
catch(PopbillException $pe) {
	echo '['.$pe->getCode().'] '.$pe->getMessage();
}
echo chr(10);
?>
