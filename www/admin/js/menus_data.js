
var subMenus_storeleft = new Array();
subMenus_storeleft[0] = new Array();
subMenus_storeleft[0][0] = "alert(1)";
subMenus_storeleft[0][1] = "분류 이동";
subMenus_storeleft[0][2] = "topmenu";
subMenus_storeleft[1] = new Array();
subMenus_storeleft[1][0] = "parent.order_up(parent.document.category_order)";
subMenus_storeleft[1][1] = "<img src='../image/t.gif' align=absmiddle> 위로 이동";
subMenus_storeleft[1][2] = "submenu";
subMenus_storeleft[2] = new Array();
subMenus_storeleft[2][0] = "parent.order_down(parent.document.category_order)";
subMenus_storeleft[2][1] = "<img src='../image/b.gif' align=absmiddle> 아래로 이동";
subMenus_storeleft[2][2] = "submenu";
subMenus_storeleft[3] = new Array();
subMenus_storeleft[3][0] = "alert(4)";
subMenus_storeleft[3][1] = "<img src='../image/folder.gif' align=absmiddle style='padding-right:3px;'> 하부 분류 추가";
subMenus_storeleft[3][2] = "onlymenu";

subMenus_storeleft[4] = new Array();
subMenus_storeleft[4][0] = "return true;";
subMenus_storeleft[4][1] = "<img src='../image/tviewcbx.gif' align=absmiddle> 전체트리 확장";
subMenus_storeleft[4][2] = "onlymenu";
subMenus_storeleft[5] = new Array();
subMenus_storeleft[5][0] = "return true;";
subMenus_storeleft[5][1] = " 속성";
subMenus_storeleft[5][2] = "onlymenu";

/*

subMenus_storeleft[6] = new Array();
subMenus_storeleft[6][0] = "http://hompy.sayclub.com/hp_best_pump.nwz";
subMenus_storeleft[6][1] = "운영형태 설정";
subMenus_storeleft[6][2] = "submenu";
subMenus_storeleft[7] = new Array();
subMenus_storeleft[7][0] = "http://hompy.sayclub.com/hp_best_pump.nwz";
subMenus_storeleft[7][1] = "운영정보설정";
subMenus_storeleft[7][2] = "topmenu";
subMenus_storeleft[8] = new Array();
subMenus_storeleft[8][0] = "http://hompy.sayclub.com/hp_best_pump.nwz";
subMenus_storeleft[8][1] = "배송료 설정";
subMenus_storeleft[8][2] = "submenu";
subMenus_storeleft[9] = new Array();
subMenus_storeleft[9][0] = "http://hompy.sayclub.com/hp_best_pump.nwz";
subMenus_storeleft[9][1] = "은행계좌관리";
subMenus_storeleft[9][2] = "submenu";
/*
subMenus_storeleft[10] = new Array();
subMenus_storeleft[10][0] = "http://hompy.sayclub.com/hp_best_pump.nwz";
subMenus_storeleft[10][1] = "거래처관리";
subMenus_storeleft[10][2] = "submenu";
*/


var subMenus_hompy = new Array();
if(permit.indexOf('01-01') > 0){
	subMenus_hompy[0] = new Array();
	subMenus_hompy[0][0] = "document.location.href='/admin/store/mallinfo.php'";
	subMenus_hompy[0][1] = "쇼핑몰 환경설정";
	subMenus_hompy[0][2] = "normal";
}
if(permit.indexOf('01-18') > 0){
	subMenus_hompy[1] = new Array();
	subMenus_hompy[1][0] = "document.location.href='/admin/store/mall_manage.php'";
	subMenus_hompy[1][1] = "쇼핑몰 운영설정";
	subMenus_hompy[1][2] = "normal";
}
if(permit.indexOf('01-02') > 0){
	subMenus_hompy[2] = new Array();
	subMenus_hompy[2][0] = "document.location.href='/admin/store/basicinfo.php'";
	subMenus_hompy[2][1] = "기본정보설정";
	subMenus_hompy[2][2] = "normal";
}
if(permit.indexOf('01-02-01') > 0){
	subMenus_hompy[3] = new Array();
	subMenus_hompy[3][0] = "document.location.href='/admin/store/admin_manage.php'";
	subMenus_hompy[3][1] = "관리자 설정";
	subMenus_hompy[3][2] = "normal";
}

if(permit.indexOf('01-03') > 0){
	subMenus_hompy[4] = new Array();
	subMenus_hompy[4][0] = "document.location.href='/admin/store/delivery.php'";
	subMenus_hompy[4][1] = "배송/택배정책";
	subMenus_hompy[4][2] = "normal";
}
if(permit.indexOf('01-04') > 0){
	subMenus_hompy[5] = new Array();
	subMenus_hompy[5][0] = "document.location.href='/admin/store/bank.php'";
	subMenus_hompy[5][1] = "무통장계좌관리";
	subMenus_hompy[5][2] = "normal";
}
if(permit.indexOf('01-14') > 0){
	subMenus_hompy[6] = new Array();
	subMenus_hompy[6][0] = "document.location.href='/admin/store/keyword_list.php'";
	subMenus_hompy[6][1] = "키워드관리";
	subMenus_hompy[6][2] = "normal";
}
if(permit.indexOf('01-15') > 0){
	subMenus_hompy[7] = new Array();
	subMenus_hompy[7][0] = "document.location.href='/admin/store/reserve_rule.php'";
	subMenus_hompy[7][1] = "적립금관리";
	subMenus_hompy[7][2] = "normal";
}
if(permit.indexOf('01-05') > 0){
	subMenus_hompy[8] = new Array();
	subMenus_hompy[8][0] = "document.location.href='/admin/store/company_list.php'";
	subMenus_hompy[8][1] = "입점업체 관리";
	subMenus_hompy[8][2] = "normal";
}
if(permit.indexOf('01-09') > 0){
	subMenus_hompy[9] = new Array();
	subMenus_hompy[9][0] = "document.location.href='/admin/store/settlement_desc.php'";
	subMenus_hompy[9][1] = "결제모듈신청";
	subMenus_hompy[9][2] = "normal";
}
if(permit.indexOf('01-06') > 0){
	subMenus_hompy[10] = new Array();
	subMenus_hompy[10][0] = "document.location.href='/admin/store/inipay.php'";
	subMenus_hompy[10][1] = "결제모듈(inicis)";
	subMenus_hompy[10][2] = "normal";
}
if(permit.indexOf('01-07') > 0){
	subMenus_hompy[11] = new Array();
	subMenus_hompy[11][0] = "document.location.href='/admin/store/allthegate.php'";
	subMenus_hompy[11][1] = "결제모듈(althegate)";
	subMenus_hompy[11][2] = "normal";
}
if(permit.indexOf('01-08') > 0){
	subMenus_hompy[12] = new Array();
	subMenus_hompy[12][0] = "document.location.href='/admin/store/lgdacom.php'";
	subMenus_hompy[12][1] = "결제모듈(LGDACOM)";
	subMenus_hompy[12][2] = "normal";
}
if(permit.indexOf('01-09') > 0){
	subMenus_hompy[13] = new Array();
	subMenus_hompy[13][0] = "document.location.href='/admin/store/kcp.php'";
	subMenus_hompy[13][1] = "결제모듈(KCP)";
	subMenus_hompy[13][2] = "normal";
}
if(permit.indexOf('01-11') > 0){
	subMenus_hompy[14] = new Array();
	subMenus_hompy[14][0] = "document.location.href='/admin/store/company.add.php'";
	subMenus_hompy[14][1] = "정보수정";
	subMenus_hompy[14][2] = "normal";
}


if(permit.indexOf('01-11-1') > 0){
	subMenus_hompy[15] = new Array();
	subMenus_hompy[15][0] = "document.location.href='/admin/store/company_list.php'";
	subMenus_hompy[15][1] = "입점업체 관리자 설정";
	subMenus_hompy[15][2] = "normal";
}

if(permit.indexOf('01-13') > 0){
	subMenus_hompy[16] = new Array();
	subMenus_hompy[16][0] = "document.location.href='/admin/store/bbs.php'";
	subMenus_hompy[16][1] = "입점업체공지사항";
	subMenus_hompy[16][2] = "normal";
	subMenus_hompy[17] = new Array();
	subMenus_hompy[17][0] = "document.location.href='/admin/store/b2b_qna.php'";
	subMenus_hompy[17][1] = "입점업체1:1문의";
	subMenus_hompy[17][2] = "normal";
}

if(permit.indexOf('01-16') > 0){
	subMenus_hompy[18] = new Array();
	subMenus_hompy[18][0] = "document.location.href='/admin/store/admin_log.php'";
	subMenus_hompy[18][1] = "관리자로그";
	subMenus_hompy[18][2] = "normal";
}

if(permit.indexOf('01-17') > 0){
	subMenus_hompy[19] = new Array();
	subMenus_hompy[19][0] = "document.location.href='/admin/store/con_log.php'";
	subMenus_hompy[19][1] = "접속로그";
	subMenus_hompy[19][2] = "normal";
}
/*
subMenus_hompy[9] = new Array();
subMenus_hompy[9][0] = "document.location.href='/admin/store/qna.php'";
subMenus_hompy[9][1] = "Q&A관리(재입고 코디관련)";
subMenus_hompy[9][2] = "normal";
subMenus_hompy[10] = new Array();
subMenus_hompy[10][0] = "document.location.href='/admin/store/qna.php?ctgr=mallstory_qna'";
subMenus_hompy[10][1] = "Q&A관리(반품및 교환관련)";
subMenus_hompy[10][2] = "normal";
subMenus_hompy[11] = new Array();
subMenus_hompy[11][0] = "document.location.href='/admin/store/bbs.php'";
subMenus_hompy[11][1] = "BlahBlah 관리";
subMenus_hompy[11][2] = "normal";
*/

var subMenus_club = new Array();
if(permit.indexOf('03-01') > 0){
	subMenus_club[0] = new Array();
	subMenus_club[0][0] = "document.location.href='/admin/product/category.php'";
	subMenus_club[0][1] = "상품분류관리";
	subMenus_club[0][2] = "normal";
}

if(permit.indexOf('03-02') > 0){
	subMenus_club[1] = new Array();
	subMenus_club[1][0] = "document.location.href='/admin/product/goods_input.php'";
	subMenus_club[1][1] = "상품등록관리";
	subMenus_club[1][2] = "normal";
}
if(permit.indexOf('03-03') > 0){
	subMenus_club[2] = new Array();
	subMenus_club[2][0] = "document.location.href='/admin/product/product_input_excel.php'";
	subMenus_club[2][1] = "상품일괄등록관리";
	subMenus_club[2][2] = "normal";
}
if(permit.indexOf('03-15') > 0){
	subMenus_club[3] = new Array();
	subMenus_club[3][0] = "document.location.href='/admin/product/goods_batch.php'";
	subMenus_club[3][1] = "상품정보 일괄변경";
	subMenus_club[3][2] = "normal";
}
if(permit.indexOf('03-04') > 0){
	subMenus_club[4] = new Array();
	subMenus_club[4][0] = "document.location.href='/admin/product/product_list.php'";
	subMenus_club[4][1] = "상품리스트";
	subMenus_club[4][2] = "normal";
}


if(permit.indexOf('03-12') > 0){
	subMenus_club[6] = new Array();
	subMenus_club[6][0] = "document.location.href='/admin/product/product_approval.php'";
	subMenus_club[6][1] = "상품승인관리";
	subMenus_club[6][2] = "normal";
/*	
	subMenus_club[4] = new Array();
	subMenus_club[4][0] = "document.location.href='/admin/product/product_list_noncategory.php'";
	subMenus_club[4][1] = "카테고리 미등록 상품";
	subMenus_club[4][2] = "normal";
*/	
}

if(permit.indexOf('03-05') > 0){
	subMenus_club[7] = new Array();
	subMenus_club[7][0] = "document.location.href='/admin/product/goods_stock.php'";
	subMenus_club[7][1] = "간편재고관리";
	subMenus_club[7][2] = "normal";
}




/*
if(permit.indexOf('03-11') > 0){
	subMenus_club[8] = new Array();
	subMenus_club[8][0] = "document.location.href='/admin/product/main_goods.php'";
	subMenus_club[8][1] = "메인페이지상품관리";
	subMenus_club[8][2] = "normal";
}

if(permit.indexOf('03-12') > 0){
	subMenus_club[9] = new Array();
	subMenus_club[9][0] = "document.location.href='/admin/product/promotion_goods.list.php'";
	subMenus_club[9][1] = "프로모션 상품관리";
	subMenus_club[9][2] = "normal";
}

if(permit.indexOf('03-06') > 0){
	subMenus_club[10] = new Array();
	subMenus_club[10][0] = "document.location.href='/admin/product/product_order3.php'";
	subMenus_club[10][1] = "분류별 상품배열";
	subMenus_club[10][2] = "normal";
}

if(permit.indexOf('03-07') > 0){
	subMenus_club[11] = new Array();
	subMenus_club[11][0] = "document.location.href='/admin/product/product_order.php'";
	subMenus_club[11][1] = "분류별 히트상품배열";
	subMenus_club[11][2] = "normal";
}
*/
if(permit.indexOf('03-08') > 0){
	subMenus_club[12] = new Array();
	subMenus_club[12][0] = "document.location.href='/admin/product/brand.php'";
	subMenus_club[12][1] = "브랜드관리";
	subMenus_club[12][2] = "normal";
}

if(permit.indexOf('03-09') > 0){
	subMenus_club[13] = new Array();
	subMenus_club[13][0] = "document.location.href='/admin/product/product_make_order.php'";
	subMenus_club[13][1] = "생산지지서 발행";
	subMenus_club[13][2] = "normal";
}

if(permit.indexOf('03-10') > 0){
	subMenus_club[14] = new Array();
	subMenus_club[14][0] = "document.location.href='/admin/product/company.php'";
	subMenus_club[14][1] = "제조사관리";
	subMenus_club[14][2] = "normal";
}

//if(permit.indexOf('03-04') > 0){
	subMenus_club[15] = new Array();
	subMenus_club[15][0] = "document.location.href='/admin/product/product_bsgoods.php?bsmode=list'";
	subMenus_club[15][1] = "구매대행 상품관리";
	subMenus_club[15][2] = "normal";
//}

//SNS메뉴 시작
var subMenus_sns = new Array();
if(permit.indexOf('16-01') > 0){
	subMenus_sns[0] = new Array();
	subMenus_sns[0][0] = "document.location.href='/admin/sns/category.php'";
	subMenus_sns[0][1] = "상품분류관리";
	subMenus_sns[0][2] = "normal";
}

if(permit.indexOf('16-02') > 0){
	subMenus_sns[1] = new Array();
	subMenus_sns[1][0] = "document.location.href='/admin/sns/goods_input.php'";
	subMenus_sns[1][1] = "상품등록관리";
	subMenus_sns[1][2] = "normal";
}
if(permit.indexOf('16-15') > 0){
	subMenus_sns[3] = new Array();
	subMenus_sns[3][0] = "document.location.href='/admin/sns/goods_batch.php'";
	subMenus_sns[3][1] = "상품정보 일괄변경";
	subMenus_sns[3][2] = "normal";
}
if(permit.indexOf('16-04') > 0){
	subMenus_sns[4] = new Array();
	subMenus_sns[4][0] = "document.location.href='/admin/sns/product_list.php'";
	subMenus_sns[4][1] = "상품리스트";
	subMenus_sns[4][2] = "normal";
}

if(permit.indexOf('16-06') > 0){
	subMenus_sns[10] = new Array();
	subMenus_sns[10][0] = "document.location.href='/admin/sns/product_order3.php'";
	subMenus_sns[10][1] = "분류별 상품배열";
	subMenus_sns[10][2] = "normal";
}

if(permit.indexOf('16-09') > 0){
	subMenus_sns[11] = new Array();
	subMenus_sns[11][0] = "document.location.href='/admin/sns/goods_orders_list.php'";
	subMenus_sns[11][1] = "배송상품 주문관리";
	subMenus_sns[11][2] = "normal";
}

if(permit.indexOf('16-10') > 0){
	subMenus_sns[12] = new Array();
	subMenus_sns[12][0] = "document.location.href='/admin/sns/coupon_orders_list.php'";
	subMenus_sns[12][1] = "쿠폰상품 발행관리";
	subMenus_sns[12][2] = "normal";
}

if(permit.indexOf('16-07') > 0){
	subMenus_sns[13] = new Array();
	subMenus_sns[13][0] = "document.location.href='/admin/sns/coupon_list.php'";
	subMenus_sns[13][1] = "발행쿠폰 관리";
	subMenus_sns[13][2] = "normal";
}

if(permit.indexOf('16-08') > 0){
	subMenus_sns[14] = new Array();
	subMenus_sns[14][0] = "document.location.href='/admin/sns/free_goods_list.php'";
	subMenus_sns[14][1] = "무료쿠폰 관리";
	subMenus_sns[14][2] = "normal";
}

if(permit.indexOf('16-16') > 0){
	subMenus_sns[15] = new Array();
	subMenus_sns[15][0] = "document.location.href='/admin/sns/sp_coupon.list.php'";
	subMenus_sns[15][1] = "스페셜쿠폰 관리";
	subMenus_sns[15][2] = "normal";
}



var subMenus_display = new Array();
if(permit.indexOf('03-01') > 0){
	subMenus_display[0] = new Array();
	subMenus_display[0][0] = "document.location.href='/admin/display/main_flash.php'";
	subMenus_display[0][1] = "메인프로모션 관리";
	subMenus_display[0][2] = "normal";
}

if(permit.indexOf('03-01') > 0){
	subMenus_display[1] = new Array();
	subMenus_display[1][0] = "document.location.href='/admin/display/main_goods.php'";
	subMenus_display[1][1] = "메인페이지 상품관리";
	subMenus_display[1][2] = "normal";
}

if(permit.indexOf('03-02') > 0){
	subMenus_display[2] = new Array();
	subMenus_display[2][0] = "document.location.href='/admin/display/event.list.php'";
	subMenus_display[2][1] = "이벤트/기획전 관리";
	subMenus_display[2][2] = "normal";
}
if(permit.indexOf('03-03') > 0){
	subMenus_display[3] = new Array();
	subMenus_display[3][0] = "document.location.href='/admin/display/promotion_goods.list.php'";
	subMenus_display[3][1] = "프로모션 상품관리";
	subMenus_display[3][2] = "normal";
}
if(permit.indexOf('03-15') > 0){
	subMenus_display[4] = new Array();
	subMenus_display[4][0] = "document.location.href='/admin/display/promotion_goods.list.php'";
	subMenus_display[4][1] = "메인 프로모션 상품관리";
	subMenus_display[4][2] = "normal";
}

if(permit.indexOf('03-04') > 0){
	subMenus_display[5] = new Array();
	subMenus_display[5][0] = "document.location.href='/admin/display/banner.php'";
	subMenus_display[5][1] = "일반배너관리";
	subMenus_display[5][2] = "normal";
}

if(permit.indexOf('03-04') > 0){
	subMenus_display[6] = new Array();
	subMenus_display[6][0] = "document.location.href='/admin/display/popup.list.php'";
	subMenus_display[6][1] = "팝업관리";
	subMenus_display[6][2] = "normal";
}

if(permit.indexOf('03-12') > 0){
	subMenus_display[7] = new Array();
	subMenus_display[7][0] = "document.location.href='/admin/display/cupon_regist_list.php'";
	subMenus_display[7][1] = "쿠폰관리";
	subMenus_display[7][2] = "normal";
}
/*
if(permit.indexOf('03-12') > 0){
	subMenus_display[8] = new Array();
	subMenus_display[8][0] = "document.location.href='/admin/display/product_approval.php'";
	subMenus_display[8][1] = "분류별 상품배열";
	subMenus_display[8][2] = "normal";
}*/
//SNS상품분류 종료
/*
subMenus_club[4] = new Array();
subMenus_club[4][0] = "http://club.sayclub.com/club_mylist.nwz";
subMenus_club[4][1] = "메인상품관리";
subMenus_club[4][2] = "normal";

subMenus_club[5] = new Array();
subMenus_club[5][0] = "http://club.sayclub.com/club_mylist.nwz";
subMenus_club[5][1] = "상품정보이동";
subMenus_club[5][2] = "normal";
*/
var subMenus_design = new Array();

if(permit.indexOf('02-01') > 0){
	subMenus_design[1] = new Array();
	subMenus_design[1][0] = "document.location.href='/admin/design/design.mod.php?mod=layout&page_name=layout.htm&SubID=SM1146411Sub'";
	subMenus_design[1][1] = "레이아웃 디자인";
	subMenus_design[1][2] = "normal";
}

if(permit.indexOf('02-02') > 0){
	subMenus_design[2] = new Array();
	subMenus_design[2][0] = "document.location.href='/admin/design/design.php?pcode=000000000000000&depth=0&category_display_type=P&SubID=SM114641Sub'";
	subMenus_design[2][1] = "페이지 상세디자인";
	subMenus_design[2][2] = "normal";
}

if(permit.indexOf('02-03') > 0){
	subMenus_design[3] = new Array();
	subMenus_design[3][0] = "document.location.href='/admin/design/design_layout.php?SubID=SM22464243Sub'";
	subMenus_design[3][1] = "기타디자인관리";
	subMenus_design[3][2] = "normal";
}

if(permit.indexOf('02-03') > 0){
	subMenus_design[6] = new Array();
	subMenus_design[6][0] = "document.location.href='/admin/design/design_layout.php?SubID=SM22464243Sub'";
	subMenus_design[6][1] = "레이아웃 일괄관리";
	subMenus_design[6][2] = "submenu";
}

if(permit.indexOf('02-03') > 0){
	subMenus_design[7] = new Array();
	subMenus_design[7][0] = "document.location.href='/admin/design/design.html.php?SubID=SM22464243Sub'";
	subMenus_design[7][1] = "HTML 라이브러리";
	subMenus_design[7][2] = "submenu";
}

if(permit.indexOf('02-03') > 0){
	subMenus_design[8] = new Array();
	subMenus_design[8][0] = "document.location.href='/admin/design/design_title.php?SubID=SM22464243Sub'";
	subMenus_design[8][1] = "타이틀 디자인";
	subMenus_design[8][2] = "submenu";
}

if(permit.indexOf('02-03') > 0){
	subMenus_design[9] = new Array();
	subMenus_design[9][0] = "document.location.href='/admin/design/design_bbs_templet.php?SubID=SM22464243Sub'";
	subMenus_design[9][1] = "게시판 디자인";
	subMenus_design[9][2] = "submenu";
}
/*
if(permit.indexOf('02-03') > 0){
	subMenus_design[10] = new Array();
	subMenus_design[10][0] = "document.location.href='/admin/design/main_flash.php?SubID=SM22464243Sub'";
	subMenus_design[10][1] = "메인 플래쉬관리";
	subMenus_design[10][2] = "submenu";
}

if(permit.indexOf('02-03') > 0){
	subMenus_design[11] = new Array();
	subMenus_design[11][0] = "document.location.href='/admin/design/banner.php?SubID=SM22464243Sub'";
	subMenus_design[11][1] = "배너관리";
	subMenus_design[11][2] = "submenu";
}
*/

if(permit.indexOf('02-03') > 0){
	subMenus_design[12] = new Array();
	subMenus_design[12][0] = "document.location.href='/admin/design/product_icon.php?SubID=SM22464243Sub'";
	subMenus_design[12][1] = "아이콘관리";
	subMenus_design[12][2] = "submenu";
}


if(permit.indexOf('02-03') > 0){
	subMenus_design[13] = new Array();
	subMenus_design[13][0] = "document.location.href='/admin/design/design_photoskin.php'";
	subMenus_design[13][1] = "포토스킨관리";
	subMenus_design[13][2] = "normal";
}



var subMenus_order = new Array();
if(permit.indexOf('04-01') > 0){
	subMenus_order[0] = new Array();
	subMenus_order[0][0] = "document.location.href='/admin/order/orders.list.php'";
	subMenus_order[0][1] = "매출진행 관리";
	subMenus_order[0][2] = "normal";
}
if(permit.indexOf('04-02') > 0){
	subMenus_order[1] = new Array();
	subMenus_order[1][0] = "document.location.href='/admin/order/orders.list.php?type=IR'";
	subMenus_order[1][1] = "입금예정 내역";
	subMenus_order[1][2] = "submenu";
}
if(permit.indexOf('04-03') > 0){
	subMenus_order[2] = new Array();
	subMenus_order[2][0] = "document.location.href='/admin/order/orders.list.php?type=IC'";
	subMenus_order[2][1] = "입금확인 내역";
	subMenus_order[2][2] = "submenu";
}
if(permit.indexOf('04-04') > 0){
	subMenus_order[3] = new Array();
	subMenus_order[3][0] = "document.location.href='/admin/order/orders.list.php?type=DI'";
	subMenus_order[3][1] = "배송중 내역";
	subMenus_order[3][2] = "submenu";//special
}
if(permit.indexOf('04-05') > 0){
	subMenus_order[4] = new Array();
	subMenus_order[4][0] = "document.location.href='/admin/order/orders.list.php?type=DC'";
	subMenus_order[4][1] = "배송완료 내역";
	subMenus_order[4][2] = "submenu";
}
if(permit.indexOf('04-06') > 0){
	subMenus_order[5] = new Array();
	subMenus_order[5][0] = "document.location.href='/admin/order/orders.list.php?type=RA'";
	subMenus_order[5][1] = "반품요청 내역";
	subMenus_order[5][2] = "submenu";
}
if(permit.indexOf('04-07') > 0){
	subMenus_order[6] = new Array();
	subMenus_order[6][0] = "document.location.href='/admin/order/orders.list.php?type=RC'";
	subMenus_order[6][1] = "반품완료 내역";
	subMenus_order[6][2] = "submenu";
}
if(permit.indexOf('04-10') > 0){
	subMenus_order[7] = new Array();
	subMenus_order[7][0] = "document.location.href='/admin/order/orders.list.php?type=CA'";
	subMenus_order[7][1] = "취소요청 내역";
	subMenus_order[7][2] = "submenu";
}
if(permit.indexOf('04-08') > 0){
	subMenus_order[8] = new Array();
	subMenus_order[8][0] = "document.location.href='/admin/order/orders.list.php?type=CC'";
	subMenus_order[8][1] = "취소완료 내역";
	subMenus_order[8][2] = "submenu";
}

if(permit.indexOf('04-09') > 0){
	subMenus_order[9] = new Array();
	subMenus_order[9][0] = "document.location.href='/admin/order/receipt_list.php'";
	subMenus_order[9][1] = "현금영수증관리";
	subMenus_order[9][2] = "normal";
}

if(permit.indexOf('04-20') > 0){
	subMenus_order[10] = new Array();
	subMenus_order[10][0] = "document.location.href='/admin/order/summary.php'";
	subMenus_order[10][1] = "매출요약";
	subMenus_order[10][2] = "normal";
}

if(permit.indexOf('04-25') > 0){
	subMenus_order[11] = new Array();
	subMenus_order[11][0] = "document.location.href='/admin/order/orders_memo.php'";
	subMenus_order[11][1] = "주문상담내역";
	subMenus_order[11][2] = "normal";
}

if(permit.indexOf('04-11') > 0){
	subMenus_order[12] = new Array();
	subMenus_order[12][0] = "document.location.href='/admin/order/accounts_plan_price.php'";
	subMenus_order[12][1] = "정산예정내역";
	subMenus_order[12][2] = "normal";
}

if(permit.indexOf('04-12') > 0){
	subMenus_order[13] = new Array();
	subMenus_order[13][0] = "document.location.href='/admin/order/accounts.php'";
	subMenus_order[13][1] = "정산관리";
	subMenus_order[13][2] = "normal";
}

if(permit.indexOf('04-14') > 0){
	subMenus_order[14] = new Array();
	subMenus_order[14][0] = "document.location.href='/admin/order/accounts_plan_delivery_price.php'";
	subMenus_order[14][1] = "배송비 정산예정내역";
	subMenus_order[14][2] = "normal";
}

if(permit.indexOf('04-15') > 0){
	subMenus_order[15] = new Array();
	subMenus_order[15][0] = "document.location.href='/admin/order/accounts_delivery.php'";
	subMenus_order[15][1] = "배송비 정산관리";
	subMenus_order[15][2] = "normal";
}

if(permit.indexOf('04-13') > 0){
	subMenus_order[16] = new Array();
	subMenus_order[16][0] = "document.location.href='/admin/order/cash.php'";
	subMenus_order[16][1] = "캐쉬관리";
	subMenus_order[16][2] = "normal";
}
/*subMenus_order[7] = new Array();
subMenus_order[7][0] = "document.location.href='/admin/order/adjustment.php'";
subMenus_order[7][1] = "정산관리";
subMenus_order[7][2] = "normal";*/

var subMenus_sattlement = new Array();
/*
subMenus_sattlement[0] = new Array();
subMenus_sattlement[0][0] = "http://sattlement.sayclub.com/sattlement_catalog.nwz?cat=1000000001";
subMenus_sattlement[0][1] = "캐릭터몰";
subMenus_sattlement[0][2] = "normal";
subMenus_sattlement[1] = new Array();
subMenus_sattlement[1][0] = "http://sattlement.sayclub.com/sattlement_catalog.nwz?cat=1000000224";
subMenus_sattlement[1][1] = "홈피몰";
subMenus_sattlement[1][2] = "normal";
subMenus_sattlement[2] = new Array();
subMenus_sattlement[2][0] = "http://sattlement.sayclub.com/sattlement_catalog.nwz?cat=1000000113";
subMenus_sattlement[2][1] = "아이템몰";
subMenus_sattlement[2][2] = "normal";
subMenus_sattlement[3] = new Array();
subMenus_sattlement[3][0] = "http://mobile.sayclub.com/mobile_sattlement_home.nwz";
subMenus_sattlement[3][1] = "모바일몰";
subMenus_sattlement[3][2] = "normal";
subMenus_sattlement[4] = new Array();
subMenus_sattlement[4][0] = "http://sattlement.sayclub.com/sattlement_catalog.nwz?cat=1000002409";
subMenus_sattlement[4][1] = "아름다운가게";
subMenus_sattlement[4][2] = "normal";
subMenus_sattlement[5] = new Array();
subMenus_sattlement[5][0] = "http://sattlement.sayclub.com/sattlement_present_main.nwz";
subMenus_sattlement[5][1] = "선물증정코너";
subMenus_sattlement[5][2] = "normal";
*/

var subMenus_member = new Array();
if(permit.indexOf('05-01') > 0){
	subMenus_member[0] = new Array();
	subMenus_member[0][0] = "document.location.href='/admin/member/member.php'";
	subMenus_member[0][1] = "전체 회원관리";
	subMenus_member[0][2] = "normal";
}
if(permit.indexOf('05-12') > 0){
	subMenus_member[1] = new Array();
	subMenus_member[1][0] = "document.location.href='/admin/member/dropmember.php'";
	subMenus_member[1][1] = "탈퇴회원관리";
	subMenus_member[1][2] = "normal";
}
if(permit.indexOf('05-02') > 0){
	subMenus_member[2] = new Array();
	subMenus_member[2][0] = "document.location.href='/admin/member/member_company.php'";
	subMenus_member[2][1] = "기업 회원정보관리";
	subMenus_member[2][2] = "normal";
}
if(permit.indexOf('05-03') > 0){
	subMenus_member[3] = new Array();
	subMenus_member[3][0] = "document.location.href='/admin/member/member_personal.php'";
	subMenus_member[3][1] = "개인 회원정보관리";
	subMenus_member[3][2] = "normal";
}
/*
if(permit.indexOf('05-04') > 0){
	subMenus_member[4] = new Array();
	subMenus_member[4][0] = "document.location.href='/admin/member/mail.write.php'";
	subMenus_member[4][1] = "메일 발송";
	subMenus_member[4][2] = "normal";
}
if(permit.indexOf('05-05') > 0){
	subMenus_member[5] = new Array();
	subMenus_member[5][0] = "document.location.href='/admin/member/sms.write.php'";
	subMenus_member[5][1] = "SMS 발송";
	subMenus_member[5][2] = "normal";
}
*/
if(permit.indexOf('05-06') > 0){
	subMenus_member[6] = new Array();
	subMenus_member[6][0] = "document.location.href='/admin/member/member_excel.php'";
	subMenus_member[6][1] = "회원정보백업";
	subMenus_member[6][2] = "normal";
}

if(permit.indexOf('05-07') > 0){
	subMenus_member[7] = new Array();
	subMenus_member[7][0] = "document.location.href='/admin/member/group.php'";
	subMenus_member[7][1] = "회원그룹관리";
	subMenus_member[7][2] = "normal";
}
if(permit.indexOf('05-08') > 0){
	subMenus_member[8] = new Array();
	subMenus_member[8][0] = "document.location.href='/admin/member/reserve.php'";
	subMenus_member[8][1] = "적립금관리";
	subMenus_member[8][2] = "normal";
}
if(permit.indexOf('05-09') > 0){
	subMenus_member[9] = new Array();
	subMenus_member[9][0] = "document.location.href='/admin/member/member_batch.php'";
	subMenus_member[9][1] = "회원정보 일괄관리";
	subMenus_member[9][2] = "normal";
}
if(permit.indexOf('05-11') > 0){
	subMenus_member[10] = new Array();
	subMenus_member[10][0] = "document.location.href='/admin/member/mail.manage.php?mc_ix=0001'";
	subMenus_member[10][1] = "자동메일발송관리";
	subMenus_member[10][2] = "normal";
}

/*
subMenus_member[2] = new Array();
subMenus_member[2][0] = "document.location.href='/admin/member/mail.php'";
subMenus_member[2][1] = "설문조사관리";
subMenus_member[2][2] = "normal";
subMenus_member[3] = new Array();
subMenus_member[3][0] = "http://saymall.sayclub.com/saymall_member_magic.nwz";
subMenus_member[3][1] = "나의아이템";
subMenus_member[3][2] = "normal";
subMenus_member[4] = new Array();
subMenus_member[4][0] = "http://member.sayclub.com/profile.nwz";
subMenus_member[4][1] = "나의정보";
subMenus_member[4][2] = "normal";
*/

/*
subMenus_zeronine[0] = new Array();
subMenus_zeronine[0][0] = "document.location.href='/admin/static/salesbydate.php'";
subMenus_zeronine[0][1] = "매출통계";
subMenus_zeronine[0][2] = "normal";
subMenus_zeronine[1] = new Array();
subMenus_zeronine[1][0] = "http://saymall.sayclub.com/saymall_zeronine_character.nwz";
subMenus_zeronine[1][1] = "상품통계";
subMenus_zeronine[1][2] = "normal";
subMenus_zeronine[2] = new Array();
subMenus_zeronine[2][0] = "javascript:openzeronineHompy('controlmenu=skin');";
subMenus_zeronine[2][1] = "회원통계";
subMenus_zeronine[2][2] = "normal";
subMenus_zeronine[3] = new Array();
subMenus_zeronine[3][0] = "http://saymall.sayclub.com/saymall_zeronine_magic.nwz";
subMenus_zeronine[3][1] = "회원이용도";
subMenus_zeronine[3][2] = "normal";
subMenus_zeronine[4] = new Array();
subMenus_zeronine[4][0] = "http://zeronine.sayclub.com/profile.nwz";
subMenus_zeronine[4][1] = "상점방문자";
subMenus_zeronine[4][2] = "normal";
*/

var subMenus_zeronine = new Array();

var subMenus_marketting = new Array();
if(permit.indexOf('06-01') > 0){
	subMenus_marketting[0] = new Array();
	subMenus_marketting[0][0] = "document.location.href='/admin/marketting/kois.php'";
	subMenus_marketting[0][1] = "KOIS 광고대행";
	subMenus_marketting[0][2] = "normal";
}

if(permit.indexOf('06-02') > 0){
	subMenus_marketting[1] = new Array();
	subMenus_marketting[1][0] = "document.location.href='/admin/marketting/keyword.php'";
	subMenus_marketting[1][1] = "키워드광고";
	subMenus_marketting[1][2] = "normal";
}
if(permit.indexOf('06-12') > 0){
	subMenus_marketting[2] = new Array();
	subMenus_marketting[2][0] = "document.location.href='/admin/marketting/naver.php'";
	subMenus_marketting[2][1] = "네이버 지식쇼핑";
	subMenus_marketting[2][2] = "normal";
}
/*
subMenus_marketting[1] = new Array();
subMenus_marketting[1][0] = "document.location.href='/admin/marketting/poll.php'";
subMenus_marketting[1][1] = "설문조사 관리";
subMenus_marketting[1][2] = "normal";
*/

if(permit.indexOf('06-05') > 0){
	subMenus_marketting[4] = new Array();
	subMenus_marketting[4][0] = "document.location.href='/admin/marketting/daum.php'";
	subMenus_marketting[4][1] = "다음쇼핑하우";
	subMenus_marketting[4][2] = "normal";
}
if(permit.indexOf('06-09') > 0){
	subMenus_marketting[5] = new Array();
	subMenus_marketting[5][0] = "document.location.href='/admin/marketting/nate.php'";
	subMenus_marketting[5][1] = "네이트 쇼핑";
	subMenus_marketting[5][2] = "normal";
}
if(permit.indexOf('06-06') > 0){
	subMenus_marketting[6] = new Array();
	subMenus_marketting[6][0] = "document.location.href='/admin/marketting/about.php'";
	subMenus_marketting[6][1] = "어바웃";
	subMenus_marketting[6][2] = "normal";
}

if(permit.indexOf('06-07') > 0){
	subMenus_marketting[7] = new Array();
	subMenus_marketting[7][0] = "document.location.href='/admin/marketting/sms.point.php'";
	subMenus_marketting[7][1] = "인터파크 오픈스타일";
	subMenus_marketting[7][2] = "normal";
}
/*
if(permit.indexOf('06-08') > 0){
	subMenus_marketting[8] = new Array();
	subMenus_marketting[8][0] = "document.location.href='/admin/marketting/cupon_regist_list.php'";
	subMenus_marketting[8][1] = "쿠폰관리";
	subMenus_marketting[8][2] = "normal";
}
if(permit.indexOf('06-10') > 0){
	subMenus_marketting[9] = new Array();
	subMenus_marketting[9][0] = "document.location.href='/admin/marketting/contactus_info.php'";
	subMenus_marketting[9][1] = "제휴문의관리";
	subMenus_marketting[9][2] = "normal";
}

if(permit.indexOf('06-11') > 0){
	subMenus_marketting[10] = new Array();
	subMenus_marketting[10][0] = "document.location.href='/admin/marketting/poll_list.php'";
	subMenus_marketting[10][1] = "설문관리";
	subMenus_marketting[10][2] = "normal";
}
*/

/*if(permit.indexOf('06-13') > 0){
subMenus_marketting[11] = new Array();
subMenus_marketting[11][0] = "document.location.href='/admin/marketting/as.php'";
subMenus_marketting[11][1] = "AS접수관리";
subMenus_marketting[11][2] = "normal";
}*/


/*if(permit.indexOf('06-13') > 0){
subMenus_marketting[11] = new Array();
subMenus_marketting[11][0] = "document.location.href='/admin/marketting/bug_info.php'";
subMenus_marketting[11][1] = "버그신고관리";
subMenus_marketting[11][2] = "normal";
}
if(permit.indexOf('06-14') > 0){
subMenus_marketting[12] = new Array();
subMenus_marketting[12][0] = "document.location.href='/admin/marketting/idea_info.php'";
subMenus_marketting[12][1] = "상품/아이디어제안 관리";
subMenus_marketting[12][2] = "normal";
}
if(permit.indexOf('06-15') > 0){
subMenus_marketting[13] = new Array();
subMenus_marketting[13][0] = "document.location.href='/admin/marketting/order_gift.list.php'";
subMenus_marketting[13][1] = "구매금액별 사은품 관리";
subMenus_marketting[13][2] = "normal";
}*/


var subMenus_static = new Array();
if(permit.indexOf('07-01') > 0){
	subMenus_static[0] = new Array();
	subMenus_static[0][0] = "document.location.href='/admin/logstory/commerce/productviewbyreferer.php?SubID=SM114641Sub'";
	subMenus_static[0][1] = "상품분석";
	subMenus_static[0][2] = "normal";
}
if(permit.indexOf('07-02') > 0){
	subMenus_static[1] = new Array();
	subMenus_static[1][0] = "document.location.href='/admin/logstory/commerce/searchmemberlist.php?SubID=SM11464243Sub'";
	subMenus_static[1][1] = "고객리스트";
	subMenus_static[1][2] = "normal";
}
if(permit.indexOf('07-03') > 0){
	subMenus_static[2] = new Array();
	subMenus_static[2][0] = "document.location.href='/admin/logstory/commerce/salessummery.php?SubID=SM1146487Sub'";
	subMenus_static[2][1] = "매출종합분석";
	subMenus_static[2][2] = "normal";
}
if(permit.indexOf('07-04') > 0){
	subMenus_static[3] = new Array();
	subMenus_static[3][0] = "document.location.href='/admin/logstory/commerce/summationbyreferer.php?SubID=SM11464176Sub'";
	subMenus_static[3][1] = "기여도 분석";
	subMenus_static[3][2] = "normal";
}
if(permit.indexOf('07-05') > 0){
	subMenus_static[4] = new Array();
	subMenus_static[4][0] = "document.location.href='/admin/logstory/commerce/salestep.php?SubID=SM11464177Sub'";
	subMenus_static[4][1] = "구매 / 회원가입단계분석";
	subMenus_static[4][2] = "normal";
}

var subMenus_log = new Array();

if(permit.indexOf('08-01') > 0){
	subMenus_log[0] = new Array();
	subMenus_log[0][0] = "document.location.href='/admin/logstory/report/pageview1.php?SubID=SM114641Sub'";
	subMenus_log[0][1] = "페이지 분석";
	subMenus_log[0][2] = "normal";
}
if(permit.indexOf('08-02') > 0){
	subMenus_log[1] = new Array();
	subMenus_log[1][0] = "document.location.href='/admin/logstory/report/visit.php?SubID=SM11464243Sub'";
	subMenus_log[1][1] = "방문자 분석";
	subMenus_log[1][2] = "normal";
}
if(permit.indexOf('08-03') > 0){
	subMenus_log[2] = new Array();
	subMenus_log[2][0] = "document.location.href='/admin/logstory/report/visitor.php?SubID=SM1146487Sub'";
	subMenus_log[2][1] = "순수방문자 분석";
	subMenus_log[2][2] = "normal";
}
if(permit.indexOf('08-04') > 0){
	subMenus_log[3] = new Array();
	subMenus_log[3][0] = "document.location.href='/admin/logstory/report/visitbyreferer.php?SubID=SM11464176Sub'";
	subMenus_log[3][1] = "유입사이트분석";
	subMenus_log[3][2] = "normal";
}
if(permit.indexOf('08-05') > 0){
	subMenus_log[4] = new Array();
	subMenus_log[4][0] = "document.location.href='/admin/logstory/manage/referer.php?SubID=SM11464177Sub'";
	subMenus_log[4][1] = "<b >통계관리자</b>";
	subMenus_log[4][2] = "normal";
}


var subMenus_estimate = new Array();
if(permit.indexOf('09-01') > 0){
	subMenus_estimate[0] = new Array();
	subMenus_estimate[0][0] = "document.location.href='/admin/estimate/estimate.list.php'";
	subMenus_estimate[0][1] = "견적현황";
	subMenus_estimate[0][2] = "normal";
}
if(permit.indexOf('09-02') > 0){
	subMenus_estimate[1] = new Array();
	subMenus_estimate[1][0] = "document.location.href='/admin/estimate/category.php'";
	subMenus_estimate[1][1] = "견적카테고리";
	subMenus_estimate[1][2] = "normal";
}
if(permit.indexOf('09-03') > 0){
	subMenus_estimate[2] = new Array();
	subMenus_estimate[2][0] = "document.location.href='/admin/estimate/estimate.product.php'";
	subMenus_estimate[2][1] = "견적상품등록";
	subMenus_estimate[2][2] = "normal";
}
if(permit.indexOf('09-04') > 0){	
	subMenus_estimate[3] = new Array();
	subMenus_estimate[3][0] = "document.location.href='/admin/estimate/estimate.intra.php'";
	subMenus_estimate[3][1] = "내부견적서";
	subMenus_estimate[3][2] = "normal";
}


var subMenus_database = new Array();
if(permit.indexOf('10-01') > 0){
	subMenus_database[0] = new Array();
	subMenus_database[0][0] = "document.location.href='/admin/database/tableinfo.php'";
	subMenus_database[0][1] = "테이블 정보";
	subMenus_database[0][2] = "normal";
}
if(permit.indexOf('10-02') > 0){
	subMenus_database[1] = new Array();
	subMenus_database[1][0] = "document.location.href='/admin/database/backup.list.php'";
	subMenus_database[1][1] = "DB 백업및 복구";
	subMenus_database[1][2] = "normal";
}
if(permit.indexOf('10-03') > 0){
	subMenus_database[2] = new Array();
	subMenus_database[2][0] = "document.location.href='/admin/database/check_db.php'";
	subMenus_database[2][1] = "DB 테이블체크";
	subMenus_database[2][2] = "normal";
}
/*
subMenus_database[2] = new Array();
subMenus_database[2][0] = "document.location.href='/admin/estimate/estimate.product.php'";
subMenus_database[2][1] = "견적상품등록";
subMenus_database[2][2] = "normal";
subMenus_database[3] = new Array();
subMenus_database[3][0] = "document.location.href='/admin/estimate/estimate.intra.php'";
subMenus_database[3][1] = "내부견적서";
subMenus_database[3][2] = "normal";
*/

var subMenus_bbsmanage = new Array();
if(permit.indexOf('11-01') > 0){
	subMenus_bbsmanage[2] = new Array();
	subMenus_bbsmanage[2][0] = "document.location.href='/admin/bbsmanage/board.manage.list.php'";
	subMenus_bbsmanage[2][1] = "게시판 목록";
	subMenus_bbsmanage[2][2] = "normal";
	subMenus_bbsmanage[2][3] = "<div style='margin-bottom:1px;display:inline;' > <a href=\"javascript:PoPWindow('/admin/bbsmanage/board.manage.list.php?mmode=pop',950,600,'manage_bbs')\"><img src='/admin/image/btn_pop_manage.gif' align=absmiddle ></a></div>";

	subMenus_bbsmanage[3] = new Array();
	subMenus_bbsmanage[3][0] = "document.location.href='/admin/bbsmanage/board.manage.php'";
	subMenus_bbsmanage[3][1] = "게시판 설정";
	subMenus_bbsmanage[3][2] = "normal";
	subMenus_bbsmanage[3][3] = "<div style='margin-bottom:1px;display:inline;' ></div>";
}
if(permit.indexOf('11-02') > 0){
	subMenus_bbsmanage[4] = new Array();
	subMenus_bbsmanage[4][0] = "document.location.href='/admin/bbsmanage/board.recent.list.php'";
	subMenus_bbsmanage[4][1] = "최근게시물 목록 관리";
	subMenus_bbsmanage[4][2] = "normal";
}
if(permit.indexOf('11-03') > 0){
	subMenus_bbsmanage[5] = new Array();
	subMenus_bbsmanage[5][0] = "document.location.href='/admin/bbsmanage/spam.php'";
	subMenus_bbsmanage[5][1] = "게시판 스팸관리";
	subMenus_bbsmanage[5][2] = "normal";
}
if(permit.indexOf('11-04') > 0){
	subMenus_bbsmanage[6] = new Array();
	subMenus_bbsmanage[6][0] = "document.location.href='/admin/bbsmanage/board_group.php'";
	subMenus_bbsmanage[6][1] = "게시판 그룹관리";
	subMenus_bbsmanage[6][2] = "normal";
}


var subMenus_cogoods = new Array();
if(permit.indexOf('12-01') > 0){
	subMenus_cogoods[0] = new Array();
	subMenus_cogoods[0][0] = "document.location.href='/admin/cogoods/hostserver.php'";
	subMenus_cogoods[0][1] = "호스트 서버 관리";
	subMenus_cogoods[0][2] = "normal";
}
if(permit.indexOf('12-02') > 0){
	subMenus_cogoods[1] = new Array();
	subMenus_cogoods[1][0] = "document.location.href='/admin/cogoods/co_seller_shop.php'";
	subMenus_cogoods[1][1] = "판매사이트 관리";
	subMenus_cogoods[1][2] = "normal";
}
if(permit.indexOf('12-03') > 0){
	subMenus_cogoods[2] = new Array();
	subMenus_cogoods[2][0] = "document.location.href='/admin/cogoods/co_sellershop_apply.php'";
	subMenus_cogoods[2][1] = "입점신청관리";
	subMenus_cogoods[2][2] = "normal";
}

if(permit.indexOf('12-04') > 0){
	subMenus_cogoods[3] = new Array();
	subMenus_cogoods[3][0] = "document.location.href='/admin/cogoods/co_goods.php'";
	subMenus_cogoods[3][1] = "공유상품관리";
	subMenus_cogoods[3][2] = "normal";
}

if(permit.indexOf('12-05') > 0){
	subMenus_cogoods[4] = new Array();
	subMenus_cogoods[4][0] = "document.location.href='/admin/cogoods/co_sellershop_apply.php'";
	subMenus_cogoods[4][1] = "-";
	subMenus_cogoods[4][2] = "normal";
}

var subMenus_inventory = new Array();
if(permit.indexOf('13-01') > 0){
	subMenus_inventory[0] = new Array();
	subMenus_inventory[0][0] = "document.location.href='/admin/inventory/stock_report.php'";
	subMenus_inventory[0][1] = "재고현황";
	subMenus_inventory[0][2] = "normal";
}
if(permit.indexOf('13-02') > 0){
	subMenus_inventory[1] = new Array();
	subMenus_inventory[1][0] = "document.location.href='/admin/inventory/stock_input.list.php'";
	subMenus_inventory[1][1] = "입고내역";
	subMenus_inventory[1][2] = "normal";
}
if(permit.indexOf('13-03') > 0){
	subMenus_inventory[2] = new Array();
	subMenus_inventory[2][0] = "document.location.href='/admin/inventory/stock_output.list.php'";
	subMenus_inventory[2][1] = "출고내역";
	subMenus_inventory[2][2] = "normal";
}
/*
if(permit.indexOf('12-03') > 0){
	subMenus_inventory[2] = new Array();
	subMenus_inventory[2][0] = "document.location.href='/admin/bbsmanage/board.manage.list.php'";
	subMenus_inventory[2][1] = "판매현황";
	subMenus_inventory[2][2] = "normal";
}
if(permit.indexOf('12-04') > 0){
	subMenus_inventory[3] = new Array();
	subMenus_inventory[3][0] = "document.location.href='/admin/bbsmanage/board.recent.list.php'";
	subMenus_inventory[3][1] = "정산관리";
	subMenus_inventory[3][2] = "normal";
}
*/
if(permit.indexOf('13-05') > 0){
	subMenus_inventory[4] = new Array();
	subMenus_inventory[4][0] = "document.location.href='/admin/inventory/warehousing_list.php'";
	subMenus_inventory[4][1] = "입고처관리";
	subMenus_inventory[4][2] = "normal";
}
if(permit.indexOf('13-06') > 0){
	subMenus_inventory[5] = new Array();
	subMenus_inventory[5][0] = "document.location.href='/admin/inventory/sale_agency_list.php'";
	subMenus_inventory[5][1] = "판매처관리";
	subMenus_inventory[5][2] = "normal";
}
if(permit.indexOf('13-07') > 0){
	subMenus_inventory[6] = new Array();
	subMenus_inventory[6][0] = "document.location.href='/admin/inventory/storehouse_list.php'";
	subMenus_inventory[6][1] = "창고관리";
	subMenus_inventory[6][2] = "normal";
}



var subMenus_campaign = new Array();

if(permit.indexOf('14-02') > 0){
	subMenus_campaign[0] = new Array();
	subMenus_campaign[0][0] = "document.location.href='/admin/campaign/addressbook_list.php'";
	subMenus_campaign[0][1] = "메일링/SMS관리";
	subMenus_campaign[0][2] = "normal";
}
if(permit.indexOf('14-01') > 0){
	subMenus_campaign[1] = new Array();
	subMenus_campaign[1][0] = "document.location.href='/admin/campaign/addressbook_group.php'";
	subMenus_campaign[1][1] = "주소록 그룹관리";
	subMenus_campaign[1][2] = "normal";
}
if(permit.indexOf('14-03') > 0){
	subMenus_campaign[2] = new Array();
	subMenus_campaign[2][0] = "document.location.href='/admin/campaign/addressbook_add.php'";
	subMenus_campaign[2][1] = "주소록 관리";
	subMenus_campaign[2][2] = "normal";
}
if(permit.indexOf('14-04') > 0){
	subMenus_campaign[3] = new Array();
	subMenus_campaign[3][0] = "document.location.href='/admin/campaign/addressbook_add_excel.php'";
	subMenus_campaign[3][1] = "주소록 일괄등록";
	subMenus_campaign[3][2] = "normal";
}



var subMenus_work = new Array();

if(permit.indexOf('15-02') > 0){
	subMenus_work[0] = new Array();
	subMenus_work[0][0] = "document.location.href='/admin/work/user.php'";
	subMenus_work[0][1] = "사용자 관리";
	subMenus_work[0][2] = "normal";
}
if(permit.indexOf('15-01') > 0){
	subMenus_work[1] = new Array();
	subMenus_work[1][0] = "document.location.href='/admin/work/work_group.php'";
	subMenus_work[1][1] = "업무 그룹관리";
	subMenus_work[1][2] = "normal";
}
if(permit.indexOf('15-03') > 0){
	subMenus_work[2] = new Array();
	subMenus_work[2][0] = "document.location.href='/admin/work/work_add.php'";
	subMenus_work[2][1] = "업무작성";
	subMenus_work[2][2] = "normal";
}
if(permit.indexOf('15-04') > 0){
	subMenus_work[3] = new Array();
	subMenus_work[3][0] = "document.location.href='/admin/work/work_list.php'";
	subMenus_work[3][1] = "업무목록";
	subMenus_work[3][2] = "normal";
}

