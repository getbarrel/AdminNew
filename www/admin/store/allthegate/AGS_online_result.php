<?php
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$Retailer_id = trim($_POST["Retailer_id"]);			//상점아이디
	$Service_pw = trim($_POST["Service_pw"]);			//상점비밀번호
	$Corp_type = trim($_POST["Corp_type"]);				//법인/개인구분(1:법인 , 2:개인)
	$Corp_no = trim($_POST["Corp_no"]);					//사업자번호
	$Corp_nm = trim($_POST["Corp_nm"]);					//상점명
	$Corp_emp = trim($_POST["Corp_emp"]);				//업체담당자명
	$Zip_cd = trim($_POST["Zip_cd"]);					//우편번호
	$Addr1 = trim($_POST["Addr1"]);						//주소
	$Addr2 = trim($_POST["Addr2"]);						//상세주소
	$Tel_no = trim($_POST["Tel_no"]);					//업체전화번호
	$Fax_no = trim($_POST["Fax_no"]);					//업체팩스번호
	$Email_id = trim($_POST["Email_id"]);				//업체이메일
	$url = trim($_POST["url"]);							//URL
	$Ceo_nm = trim($_POST["Ceo_nm"]);					//대표자명
	$Ceo_no = trim($_POST["Ceo_no"]);					//대표자주민번호
	$Ceo_zipcd = trim($_POST["Ceo_zipcd"]);				//대표자자택우편번호
	$Ceo_addr1 = trim($_POST["Ceo_addr1"]);				//대표자주소
	$Ceo_addr2 = trim($_POST["Ceo_addr2"]);				//대표자상세주소
	$Prod_nm = trim($_POST["Prod_nm"]);					//주요취급상품
	$Acct_no = trim($_POST["Acct_no"]);					//정산계좌번호
	$Acct_nm = trim($_POST["Acct_nm"]);					//예금주명
	$Center_cd = trim($_POST["Center_cd"]);				//은행코드(은행코드표 참조)
	$Biz_type = trim($_POST["Biz_type"]);				//업태
	$Biz_kind = trim($_POST["Biz_kind"]);				//업종
	$handp_no = trim($_POST["handp_no"]);				//업체핸드폰번호
	$Credit_yn = trim($_POST["Credit_yn"]);				//신용카드사용여부
	$Acct_yn = trim($_POST["Acct_yn"]);					//계좌이체사용여부
	$Hp_yn = trim($_POST["Hp_yn"]);						//핸드폰결제사용여부
	$Ptn_id = trim($_POST["Ptn_id"]);					//파트너아이디
	$vir_yn = trim($_POST["vir_yn"]);					//가상계좌사용여부(0:사용, 빈값:미사용)
	$escrow_acct_yn = trim($_POST["escrow_acct_yn"]);	//우리에스크로계좌이체사용여부
	$escrow_vir_yn = trim($_POST["escrow_vir_yn"]);		//우리에스크로가상계좌사용여부
	$rSuccyn = trim($_POST["rSuccyn"]);					//성공여부
	$rResmsg = trim($_POST["rResmsg"]);					//결과메세지

					
	/****************************************************************************
	*
	* 여기서 DB 작업을 해 주세요.
	* 주의) rSuccyn 값이 'Y' 일경우 신용카드승인성공
	* 주의) rSuccyn 값이 'N' 일경우 신용카드승인실패
	* DB 작업을 하실 경우 rSuccyn 값이 'Y' 또는 'N' 일경우에 맞게 작업하십시오. 
	*
	****************************************************************************/
	if($rSuccyn == "Y"){
		$db = new Database;
		$db->query("update shop_shopinfo set allthegate_id = '".$Retailer_id."',escrow_method_bank = '".$Acct_yn."' , escrow_method_vbank = '".$vir_yn."' where mall_div = 'B'");
		echo "<script>alert('신청프로세서가 정상적으로 마쳤습니다.');self.close();</script>";
	}else{
		echo "<script>alert('신청프로세서가 실패하였습니다. 실패메세지는 '".$rResmsg."'.');self.close();</script>";
	}
?>