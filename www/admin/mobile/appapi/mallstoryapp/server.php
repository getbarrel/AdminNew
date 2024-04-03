<?php

/*************************************************
# HISTORY #

# create - 2013/11/04

*************************************************/

include ("./server.class");
include ('SOAP/Server.php');
//include ($_SERVER["DOCUMENT_ROOT"]."/include/xmlWriter.php");
@include ("./lib/product.lib.php");


include ("./class/mallstoryapp.class");

/*
ini_set("user_agent","Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
ini_set("max_execution_time", 0);
ini_set("memory_limit", "10000M");
*/

/*************************************************
# 공통 파라미터(common parameter)
	act => 처리명령어 [필수]
	result_data_type => 결과데이터타입 [필수아님]
		-> json <기본>
		-> xml
#디버그 모드
	debug  => 화면에 결과값을 뿌려준다
		-> 1
*************************************************/

//appapi/mallstoryapp/class/mallstoryapp.class
$MApp = new MallstoryApp;

//디버그 모드
if($_GET["debug"]=="1"){

	if($_GET["result_data_type"]=="xml"){
		$MApp->ResultDataType = "xml";
		header("Content-type: text/xml");
	}else{
		echo "<pre>";
	}

	$_act = $_GET["act"];

}else{

	if($_POST["result_data_type"]=="xml"){
		$MApp->ResultDataType = "xml";
	}

	$_act = $_POST["act"];
}

define_syslog_variables();
openlog("phplog", LOG_PID , LOG_LOCAL0);


//if($_SESSION["admininfo"]["company_id"]){
	switch ($_act) {

		case "getadmininfo":
			/*************************************************
			# 관리자정보 #

			# 입력 파라미터(input parameter)
				id = > 로그인 아이디
			# 출력 파라미터(output parameter)
				Array
					(
						code => "00" : 성공 , "99" : 오류
						msg => ""
						data => Array
										(
											admin_level => 관리자 LEVEL
										)
					)

				{"code":"00","msg":"","data":{"admin_level":"8"}}
			*************************************************/
			$_RESULT_ = $MApp->getAdminInfo($_GET);
		break;

		case "getsellerlist":
			/*************************************************
			# 입점업체 리스트 #
				
			# 입력 파라미터(input parameter)
				userid = > 로그인 아이디
			# 출력 파라미터(output parameter)
				Array
					(
						code => "00" : 성공 , "99" : 오류
						msg => ""
						data => Array
										(
											company_id => 업체코드
											com_name => 업체명
										)
						admininfodata =>
										Array
										(
											cid => 카테고리아이디
											cname => 카테고리명
										)
					)
			*************************************************/
			$_RESULT_ = $MApp->getSellerList($_GET);
		break;

		case "getcategorys":
			/*************************************************
			# 부모 카테고리에서 하위 카테고리 리스트 불러오는 함수 #

			# 입력 파라미터(input parameter)
				parent_cid => 부모카테고리코드 [필수아님]

			# 출력 파라미터(output parameter)
				Array
					(
						code => "00" : 성공 , "99" : 오류
						msg => ""
						data => Array
										(
											cid => 카테고리아이디
											cname => 카테고리명
										)
					)
			*************************************************/
			$_RESULT_ = $MApp->getCategorys($parent_cid);
		break;
		
		
		case "getnewcontents":
			/*************************************************
			# 새로 등록된 상품리스트 혹은 주문이 있는지 검사해서 수량을 리턴 #

			# 입력 파라미터(input parameter)

			# 출력 파라미터(output parameter)
				Array
					(
						code => "00" : 성공 , "99" : 오류
						msg => ""
						data => Array
										(
											name => "goods" : 상품 , "order" : 주문
											count => "2"
										)
					)
			*************************************************/
			$_RESULT_ = $MApp->getNewContents();
		break;

		case "insertgood":
			/*************************************************
			# 상품등록 #
			
			# 입력 파라미터(input parameter)
				goods_name => 상품명
				cate1 => 카테고리-1
				cate2 => 카테고리-2
				cate3 => 카테고리-3
				goods_code => 상품코드
				price0 = > 원가
				price1 = > 도매가
				price2 = > 소매가
				option => 옵션 ([{"opt_name":"벽걸이","opt_count":"1"},{"opt_name":"확장리모콘","opt_count":"2"}])
				basicimagefile => 기본이미지 컬럼명
				imgfile1
				imgfile2
				imgfile3
				imgfile4
				imgfile5
				imgfile6
				detailimgfile1
				detailimgfile2
				detailimgfile3
				detailimgfile4
				detailimgfile5
				detailimgfile6
				basicinfo => 상품상세설명
				origin => 원산지
				company => 제조사

			# 출력 파라미터(output parameter)

			*************************************************/
			$_RESULT_ = $MApp->insertGood($_POST);
		break;

		default:

			$_RESULT_["code"]="88";
			$_RESULT_["msg"]="파라미터 act : ".$_POST["act"]." 에 대한 처리 내용이 없습니다.";
	}
/*
}else{
	$_RESULT_["code"]="77";
	$_RESULT_["msg"]="로그인후 이용해주시기 바랍니다.";
}*/

if($_GET["debug"]=="1"){
	if($MApp->ResultDataType == "xml"){
		echo $_RESULT_;
	}else{
		//print_r($_RESULT_);
		$_RESULT_ = str_replace("\"true\"","true",json_encode($_RESULT_));
		$_RESULT_ = str_replace("\"false\"","false",$_RESULT_);
		echo $_RESULT_;
	}
}else{

	//syslog(LOG_NOTICE, json_encode($_COOKIE));

	//syslog(LOG_NOTICE, json_encode($_GET));

	$_RESULT_ = str_replace("\"true\"","true",json_encode($_RESULT_));
	$_RESULT_ = str_replace("\"false\"","false",$_RESULT_);
	echo $_RESULT_;
}

closelog();

?>
