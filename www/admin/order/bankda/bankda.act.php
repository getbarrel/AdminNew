<?php 
/**
 * 뱅크다 ACT
 * 함수호출 및 결과 스크립트 처리
 * //TODO: 사용하지 않는 case가 더 많음. 나중에 정리할 것.
 * 
 * @date 2013.09.16
 * @author bgh
 */
require_once './src/bankda.php';

$bankda = new bankda();

$act		 = $_REQUEST['act'];
$postValue = $_POST;
$result = null;
//print_r($_GET);
//print_r($_POST);
//exit;
switch($act){
	case 'verify':
		// 서비스 가입여부	@return boolean
		$result = $bankda->getShopVerify();
		break;
		
	case 'accountBalance':
		//사용계좌 잔액조회	@return array
		$result = $bankda->getAccountBalance();
		break;
		
	case 'transList':
		//거래내역 조회		@return array
		$result = $bankda->getTransactionList($postValue);
		break;
		
	case 'immediatelyUpdate':
		//선택한 계좌 업데이트
		$bkacctno = $_GET['bkacctno'];
		$result = $bankda->callImmediatelyUpdate($bkacctno);
		if($result){
			$bankda->transactionMatch(true);
			$result['script'] = 'alert("계좌('.$bkacctno.') 즉시조회 성공");parent.location.reload();';
		}
		break;
		
	case 'update':
		$result = $bankda->transactionMatch();
		$result['script'] = 'alert("업데이트 완료되었습니다.");parent.location.reload();';
		break;
		
	case 'update_one':
		$bankda->transactionMatchByOne($oid,$bkid);
		$result['script'] = 'alert("업데이트 완료되었습니다.");parent.location.reload();';
		break;

	case 'update_one_duplicate':
		$bankda->transactionMatchByOneDuplicate($oid,$bkid);
		$result['script'] = 'alert("업데이트 완료되었습니다.");parent.location.reload();';
		break;

	case 'cronUpdate':
		//자동입금확인 업데이트 @call crontab
		$result = $bankda->transactionMatch();
		break;
		
	case 'addAccount':
		//계좌추가
		$result = $bankda->callAddAccount($postValue);
		$result['script'] = 'opener.location.reload();self.close();';
		break;
		
	case 'modifyAccount':
		//계좌수정
		$result = $bankda->callModifyAccount($postValue);
		$result['script'] = 'opener.location.reload();self.close();';
		break;
		
	case 'deleteAccount':
		//계좌삭제
		$bkacctno = $_GET['bkacctno'];
		$result = $bankda->callDeleteAccount($bkacctno);
		$result['script'] = 'parent.location.reload();';
		break;
		
	case 'addUser':
		//이용자 추가
		$result = $bankda->callInsertUser($postValue);
		break;
		
	case 'dropUser':
		//이용자 삭제
		$result = $bankda->callDropUser($postValue);
		break;
		
	case 'checkALLAccount':
		print_r($bankda->callALLAccountInfo());
		break;	
	case 'memo_update':
		$bankda->transactionMemo($memo_text,$bkid);
		//$result['script'] = 'alert("업데이트 완료되었습니다.");parent.location.reload();';
		break;
}

/* script 처리용 리턴 */
if(!empty($result)){
	echo "<script>".$result['script']."</script>";
	exit;
}