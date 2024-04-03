<?
/**
 * 수행결과(resultCode를 위한) 표준 객체
 */
if(!class_exists(resultData)){
    class resultData{
        var $resultCode; //결과코드 ex)success,fail 고정
        var $message; //결과메시지
        var $productNo; //상품번호
    }
}


/**
 * 카테고리 한뎁스의 목록을 담을 표준 객체
 */
if (!class_exists(categoryData)){
    class categoryData{
        
        var $disp_name;
        var $disp_no;
        var $depth;
        var $parent_no;
        
    }
}

/**
 * 주소정보 표준 객체
 */
if(!class_exists(addressItem)){
    class addressItem{
        var $memNo; //회원번호(제휴사)
        var $addrSeq; //주소seq(제휴사)
        var $addrNm; //주소명
        var $rcvrNm; //이름
        var $gnrlTlphnNo; //일반전화번호
        var $prtblTlphnNo; //휴대전화번호
        var $mailNO; //우편번호
        var $mailNOSeq; //우편번호 seq (제휴사)
        var $dtlsAddr; //상세주소
        var $baseAddrYN; //기본주소 여부
        
    }
}

/**
 * 우편번호 표준 객체
 */
if(!class_exists(zipcodeItem)){
    class zipcodeItem{
        var $addr;
        var $mailNO;
        var $mailNOSeq;
        var $sidoNM;
        var $sigunguNM;
    }
}

/**
 * 주문정보 표준
 */
if(!class_exists(orderItem)){
    class orderItem{
        var $addPrdNo; //추가구성상품의 원상품번호 0 : 추가구성상품 아님 0이 아닐경우 : 추가구성상품의 원상품번호
        var $addPrdYn;  //추가 구성 상품 유무Y : 추가구성상품 있음 N : 추가구성상품 없음
        var $bndlDlvSeq; //묶음배송일련번호
        var $bndlDlvYN; // 묶음 배송 유무 Y : 묶음 배송 N : 개별 배송
        var $custGrdNm; //고객등급 우수고객 일반고객
        var $dlvCst; //배송비
        var $dlvCstType; //배송비 착불 여부 1 : 선불 2 : 착불 3 : 무료
        var $dlvNo; //배송번호
        var $memID; //회원ID
        var $memNo; //회원번호 - 2012/01/13(금)부터 신규제공
        var $ordAmt; //주문총액: 판매단가*수량(주문 -취소 -반품)+옵션가
        var $ordBaseAddr; //주문자 기본주소
        var $ordDlvReqCont; //배송시 요청사항
        var $ordDt; //주문일시
        var $ordDtlsAddr; //구매자 상세주소
        var $ordEmail; //구매자 E-Mail
        var $ordMailNo; //구매자 우편번호
        var $ordNm; //구매자 이름
        var $ordNo; //11번가 주문번호
        var $ordOptWonStl; //- 주문상품옵션결제금액
        var $ordPayAmt; //- 결제금액 : 주문금액 + 배송비 - 판매자 할인금액 - mo쿠폰
        var $ordPrdSeq; //주문순번
        var $ordPrtblTel; //구매자 휴대폰번호
        var $ordQty; //수량
        var $ordStlEndDt; //결제완료일시
        var $ordTlphnNo; //주문자전화번호
        var $plcodrCnfDt; //발주확인일시
        var $prdNm; //상품명 
        var $prdNo; //11번가상품번호
        var $prdStckNo; //주문상품옵션코드
        var $rcvrBaseAddr; //배송기본주소
        var $rcvrDtlsAddr; //배송상세주소
        var $rcvrMailNo; //배송지우편번호
        var $rcvrNm; //수령자명
        var $rcvrPrtblNo; //수령자핸드폰번호
        var $rcvrTlphn; //수령자전화번호
        var $selPrc; //판매가(객단가)
        var $sellerDscPrc; //판매자 할인금액
        var $sellerPrdCd; //판매자상품번호
        var $slctPrdOptNm; //주문상품옵션명
        var $tmallDscPrc; //11번가 할인금액 
        var $gblDlvYn; //전세계배송여부 - 2012/09/25(화)부터 신규제공

    }
}
/**
 * 주문상태 정보
 */
if(!class_exists(orderStatusItem)){
    class orderStatusItem{
        var $dlvNo; //배송번호 ex)75347773
        var $ordCnQty; //취소수량(취소요청 수량도 포함)
        var $ordNo; //주문번호
        var $ordPrdSeq; //주문순번
        var $ordPrdStatNm;//주문상태 (배송중, 배송준비중, 반품신청, 취소신청...)
        var $statusCd; //솔루션 주문상태코드
        var $ordQty;//최초주문수량
        var $prdNm;//상품명
        var $prdNo;//상품번호
        var $sellerID; // 판매자 아이디
    }
}
?>