<?php

/*************************************************
# HISTORY #

# create - 2013/11/04

*************************************************/

include ("./server.class");
include ('SOAP/Server.php');
include ($_SERVER["DOCUMENT_ROOT"]."/include/xmlWriter.php");
@include ("./lib/product.lib.php");


include ("./class/app.class");


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


//appapi/app/class/App.class
$App = new App;

//디버그 모드
if($_GET["debug"]=="1"){

	if($_GET["result_data_type"]=="xml"){
		$App->ResultDataType = "xml";
		header("Content-type: text/xml");
	}else{
		echo "<pre>";
	}

	$_act = $_GET["act"];

}else{

	if($_POST["result_data_type"]=="xml"){
		$App->ResultDataType = "xml";
	}

	$_act = $_POST["act"];

	if(empty($_act))
		$_act = $_GET["act"];

}

define_syslog_variables();
openlog("phplog", LOG_PID , LOG_LOCAL0);


switch ($_act) {
	
	case "getfaqlist":
		/*************************************************
		# FAQ게시판 #

		# 입력 파라미터(input parameter)
			bbs_name => 게시판테이블명
			search_text => 검색명
		# 출력 파라미터(output parameter)
			{
				"code":"00",
				"msg":"",
				"data":{
					"div":[
								{
									"idx_":1,
									"bbs_div":"18",
									"div_name":"회원관련문의"
								},
								{
									"idx_":2,
									"bbs_div":"20",
									"div_name":"아이디/비밀번호찾기"
								},
								{
									"idx_":3,
									"bbs_div":"19",
									"div_name":"주문/배송문의"
								}
							],
					"contants":[
								{
									"idx_":1,
									"bbs_ix":"1",
									"bbs_div":"53",
									"bbs_subject":"[교환문의] 교환 신청 및 교환 상품을 어떻게 해서 보내야 하나요?",
									"bbs_contents":"일반적으로 반품접수 후 1~2일(주말,공휴일제외) 또는 3~4일이내에(주말,공휴일제외) 회수 방문 합니다. 상품의 최초 인수 상태 그대로 포장하여 택배 기사로부터 반송장을 받은 후 상품을 건네주시면 됩니다."
								},
								{
									"idx_":2,
									"bbs_ix":"2",
									"bbs_div":"19",
									"bbs_subject":"[주문/배송문의] 배송되지 않아 취소접수를 했는데 배송이 왔어요.",
									"bbs_contents":"취소 내역을 확인하지 못하고 출고되는 경우가 간혹 있습니다. 이미 취소하신 상품이므로 택배 기사님 연락 시 인수하지 마시고 수취거부 하시면 됩니다. 만일, 상품을 인수하셨다면 [고객센터 ☎ 070-4322-6522]로 문의주시면 신속하게 회수 처리 해 드립니다. "
								}
							]
					}
			}
		*************************************************/
		$_RESULT_ = $App->getFAQList($_POST);
	break;

	case "getbbslist":
		/*************************************************
		# 일반게시판 #

		# 입력 파라미터(input parameter)
			bbs_name => 게시판테이블명
		# 출력 파라미터(output parameter)
			{
				"code":"00"
				"msg":""
				"data":{
					"contants":[
						{
							"idx_":1,
							"bbs_ix":"1",
							"bbs_subject":"교환 신청 및 교환 상품을 어떻게 해서 보내야 하나요?",
							"bbs_contents":"일반적으로 반품접수 후 1~2일(주말,공휴일제외) 또는 3~4일이내에(주말,공휴일제외) 회수 방문 합니다. 상품의 최초 인수 상태 그대로 포장하여 택배 기사로부터 반송장을 받은 후 상품을 건네주시면 됩니다."
						},
						{
							"idx_":2,
							"bbs_ix":"2",
							"bbs_subject":"배송되지 않아 취소접수를 했는데 배송이 왔어요.",
							"bbs_contents":"취소 내역을 확인하지 못하고 출고되는 경우가 간혹 있습니다. 이미 취소하신 상품이므로 택배 기사님 연락 시 인수하지 마시고 수취거부 하시면 됩니다. 만일, 상품을 인수하셨다면 [고객센터 ☎ 070-4322-6522]로 문의주시면 신속하게 회수 처리 해 드립니다. "
						}
					]
				}
			}
		*************************************************/
		$_RESULT_ = $App->getBBSList($_POST);
	break;

	case "getmanagepolicy":
		/*************************************************
		# 운영정책 #

		# 입력 파라미터(input parameter)

		# 출력 파라미터(output parameter)
			{
				"code":"00"
				"msg":""
				"data":"이미지URL"
			}
		*************************************************/
		$_RESULT_ = $App->getManagePolicy();
	break;

	case "setpasswordinitialize":
		/*************************************************
		# 비밀번호초기화 #

		# 입력 파라미터(input parameter)
			id => 회원아이디
			pcs => 핸드폰번호
			pw =>  변경비밀번호

		# 출력 파라미터(output parameter)
			{
				"code":"00", : 06=> 아이디와 핸드폰번호가 일치하는 아이디가 없습니다.
				"msg":""
			}
		*************************************************/
		$_RESULT_ = $App->setPasswordInitialize($_POST);
	break;

	case "setsellerevaluationscore":
		/*************************************************
		# 업체평가 #

		# 입력 파라미터(input parameter)
			company_id => 업체코드
			code => 회원코드
			name =>  회원명or아이디
			score => 점수
			msg => 메세지

		# 출력 파라미터(output parameter)
			{
				"code":"00",
				"msg":"",
				"data":[
					{
					"idx_":1,
					"score":"5",
					"name":"TEST \ub370\uc774\ud130",
					"msg":"TEST \ub85c \ub123\uc5b4\ubd24\uc2b5\ub2c8\ub2e4.",
					"regdate":"2013-12-11"
					},
					{
					"idx_":2,
					"score":"5",
					"name":"TEST \ub370\uc774\ud130",
					"msg":"TEST \ub85c \ub123\uc5b4\ubd24\uc2b5\ub2c8\ub2e4.",
					"regdate":"2013-12-11"
					}
				],
				"avg_score":"??"

			}
		*************************************************/
		$_RESULT_ = $App->setSellerEvaluationScore($_POST);
	break;

	case "getsellerevaluationscorelist":
		/*************************************************
		# 업체평가댓글리스트 #

		# 입력 파라미터(input parameter)
			company_id => 업체코드

		# 출력 파라미터(output parameter)
			{
				"code":"00",
				"msg":"",
				"data":[
					{
					"idx_":1,
					"score":"5",
					"name":"TEST \ub370\uc774\ud130",
					"msg":"TEST \ub85c \ub123\uc5b4\ubd24\uc2b5\ub2c8\ub2e4.",
					"regdate":"2013-12-11"
					},
					{
					"idx_":2,
					"score":"5",
					"name":"TEST \ub370\uc774\ud130",
					"msg":"TEST \ub85c \ub123\uc5b4\ubd24\uc2b5\ub2c8\ub2e4.",
					"regdate":"2013-12-11"
					}
				]
			}
		*************************************************/
		$_RESULT_ = $App->getSellerEvaluationScoreList($_POST);
	break;
	
	case "setgoodswish":
		/*************************************************
		# 관심상품 #

		# 입력 파라미터(input parameter)
			div => 처리구분
				-> insert [추가]
				-> delete [삭제]
			pid => 상품코드
			code => 회원코드

		# 출력 파라미터(output parameter)
			{
				"code":"00", 98:로그인후 이용가능합니다. , 21:이미등록된 관심상품입니다.
				"msg":"",
				"data":null
				}
			}
		*************************************************/
		$_RESULT_ = $App->setGoodsWish($_POST);
	break;

	case "setproductcomment":
		/*************************************************
		# 상품댓글 #
			//setsellercomment => setproductcomment
		# 입력 파라미터(input parameter)
			div => 처리구분
				-> insert [추가]
				-> update [수정]
				-> delete [삭제]
			company_id => 업체코드
			pid => 상품코드
			code => 회원코드
			name => 회원명
			msg => 내용

		# 출력 파라미터(output parameter)
			{
				"code":"00",
				"msg":"",
				"data":[
					{
					"idx_":1,
					"cc_ix":"0000000001",
					"company_id":"12dkwndfk2wkndfkldsfo2",
					"code":"12dkwndfk2wkndfkldsfo2",
					"name":"TEST \ub370\uc774\ud130",
					"msg":"TEST \ub85c \ub123\uc5b4\ubd24\uc2b5\ub2c8\ub2e4."
					}
					,
					{
						...
					}
				]

			}
		*************************************************/
		$_RESULT_ = $App->setProductComment($_POST);
	break;

	case "setsellerfollower":
		/*************************************************
		# 셀러팔로우 -> 셀러 즐겨찾기 #

		# 입력 파라미터(input parameter)
			div => 처리구분
				-> insert [추가]
				-> delete [삭제]
			company_id => 업체코드
			code => 회원코드

		# 출력 파라미터(output parameter)
			{
				"code":"00", => 98:로그인후 이용가능합니다., 11:이미팔로우 되어 있습니다.
				"msg":"",
				"data":"21" (Follower 수)
				}
			}
		*************************************************/
		$_RESULT_ = $App->setSellerFollower($_POST);
	break;

	case "accreditnum":
		/*************************************************
		# 인증번호 #

		# 입력 파라미터(input parameter)
			pcs => 핸드폰번호 (010-3887-4023)

		# 출력 파라미터(output parameter)
			{
				"code":"00",
				"msg":"",
				"data":"*****" (5자리)
				}
			}
		*************************************************/
		$_RESULT_ = $App->accreditNum($_POST);
	break;

	case "memberjoin":
		/*************************************************
		# 회원가입 #

		# 입력 파라미터(input parameter)
			id => 아이디
			pw => 비밀번호
			name => 이름
			pcs => 핸드폰번호 (010-3887-4023)
			mail => 이메일

		# 출력 파라미터(output parameter)
			{
				"code":"00", => 11:이미 등록된 아이디입니다.,12:회원등록에 필요한 정보가 부족합니다.
				"msg":"",
				"data":""
				}
			}
		*************************************************/
		$_RESULT_ = $App->memberJoin($_POST);
	break;

	case "login":
		/*************************************************
		# 로그인 #

		# 입력 파라미터(input parameter)
			id => 아이디
			pw => 비밀번호

		# 출력 파라미터(output parameter)
			{
				"code":"00", => 01:아이디또는 비밀번호 불일치,02:계정승인 대기중,03:승인 거절
				"msg":"",
				"data":{
					"code":"ea0e17366276c14eb075b486b376227a", => 회원코드
					"company_id":"cb06306007b32db862ce70d0487b6ca0", => 회원업체코드
					"id":"hong861114", =>아이디
					"name":"\ud64d\uc9c4\uc601", => 회원명
					"pcs":"010-0000-0000", => 폰번호
					"mail":"test@nate.com", => 이메일
					"gp_level":"1", => 그룹레벨
					"gp_name":"\uc9c1\uc6d0", => 그룹명
					"gp_ix":"9", => 그룹코드
					"is_delivery_price":"1", => 배송비부과여부
					"is_buying_price":"1", => 사입비부과여부
					"mem_type":"M", => 회원타입 ('M':일반,'F':외국인,'P':해외거주자,'S':셀러,'A':관리자,'MD':엠디)
					"price_view":"1", => 구매가격타입 (0:권장소비자가,1:도매가)
					"payment":"S", => 결제가능방법(P:예치금,B:무통장,S:예치금)
					"commission_rate":"0" => 상품구매시 구매액의 수수료
				}
			}
		*************************************************/
		$_RESULT_ = $App->login($_POST);
	break;

	case "getgoods":
		/*************************************************
		# 상품상세내용 #

		# 입력 파라미터(input parameter)
			pid => 상품아이디

		# 출력 파라미터(output parameter)
			Array
				(
					code => "00" : 성공 , "99" : 오류
					msg => ""
					data => Array
									(
										pid => 상품아이디
										pname => 상품명
										sellprice => 판매가
										basicinfo => 상세내용
										stock => 상품수량
										stock_use_yn => 재고관리사용여부
										cname => 카테고리명
										company_id = > 업체아이디
										search_keyword => 태그
										addr => 상품주소
										delivery_price => 배송비
										img = > 이미지
										options => 옵션
										link_url => 상품URL
									)
																	:
																	:
																	:
				)
		*************************************************/
		$_RESULT_ = $App->getGoods($_POST);
	break;

	case "getgoodslist":
		/*************************************************
		# 상품리스트 #

		# 입력 파라미터(input parameter)
			start => 몇번째번째 부터 상품가지고올 인덱스 0번서부터
			max => 가지고올 상품갯수
			company_id => 셀러아이디
			cid => 카테고리 코드
			depth => 카테고리 깊이
			search_text => 검색어
			lowprice => 낮은가격
			highprice => 높은가격
			orderby => 리스트정렬 방식
				-> regdate [최신순]
				-> popularity [인기순]
				-> lowprice [저가순]
				-> highprice [고가순]

		# 출력 파라미터(output parameter)
			Array
				(
					code => "00" : 성공 , "99" : 오류
					msg => ""
					data => Array
									(
										pid => 상품아이디
										admin => 관리셀러아이디
										pname => 상품명
										sellprice => 판매가
										img => 이미지URL
										regdate => 등록일
										delivery_policy = > 배송정책
											-> 3 [무료배송]
											-> 1 [유료배송]
										wish_cnt => 회원의관심수
										addr => 상품주소
									)
																	:
																	:
																	:
				)
		*************************************************/
		$_RESULT_ = $App->getGoodsList($_POST);
	break;

	case "getmainsellerlist":
		/*************************************************
		# 메인인기상점리스트#

		# 입력 파라미터(input parameter)

		# 출력 파라미터(output parameter)
			Array
				(
					code => "00" : 성공 , "99" : 오류
					msg => ""
					data => Array
									(
										company_id => 상점코드
										store_name => 상점이름
										store_img => 이미지경로
										follower_cnt => 관심등록갯수
									)
																	:
																	:
																	:
				)
		*************************************************/
		$_RESULT_ = $App->getMainSellerList($_POST);
	break;

	case "getsellerlist":
		/*************************************************
		# 상점리스트 #

		# 입력 파라미터(input parameter)
			start => 리스트 시작 인덱스 [필수아님]
				-> 0 (기본)
			max => 리스트 가지고올 갯수 [필수아님]
				-> 30 (기본)
			wh_recommend => 인기상점 여부
				-> Y (기본)
			orderby => 리스트 노출순서 [필수아님]
				-> random (기본)

		# 출력 파라미터(output parameter)
			Array
				(
					code => "00" : 성공 , "99" : 오류
					msg => ""
					data => Array
									(
										company_id => 상점코드
										store_name => 상점이름
										store_img => 이미지경로
										follower_cnt => 관심등록갯수
									)
																	:
																	:
																	:
				)
		*************************************************/
		$_RESULT_ = $App->getSellerList($_POST);
	break;

	case "getbanner":
		/*************************************************
		# 메인상품리스트 (기존 상품리스트와 같지만 따로 프로모션만 불러올수 있기 때문에 따로 처리) #

		# 입력 파라미터(input parameter)
			div => 구분값 [필수아님]
				-> app_main (메인배너)

		# 출력 파라미터(output parameter)
			Array
				(
					code => "00" : 성공 , "99" : 오류
					msg => ""
					data => Array
									(
										img => 이미지경로
									)
																	:
																	:
																	:
				)
		*************************************************/
		$_RESULT_ = $App->getBanner($_POST);
	break;

	case "getmaingoods":
		/*************************************************
		# 메인상품리스트 (기존 상품리스트와 같지만 따로 프로모션만 불러올수 있기 때문에 따로 처리) #

		# 입력 파라미터(input parameter)
			div => 구분값 [필수아님]
				-> popularity or null (인기상품)
				-> recommend (추천상품)
			wh_date => 검색조건(날짜) [필수아님] !! 이슈 있음
				-> today 오늘의인기 

		# 출력 파라미터(output parameter)
			Array
				(
					code => "00" : 성공 , "99" : 오류
					msg => ""
					data => Array
									(
										pid => 상품시스템코드
										pname => 상품명
										sellprice => 판매가
										img => 이미지경로
									)
																	:
																	:
																	:
				)
		*************************************************/
		$_RESULT_ = $App->getMainGoods($_POST);
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
										depth => 카테고리 깊이
										vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 => 깊이 정렬값
									)
																	:
																	:
																	:
				)
		*************************************************/
		$_RESULT_ = $App->getCategorys($parent_cid);
	break;

	case "getmaincategorys":
		/*************************************************
		# 메인 카테고리 리스트에 뿌려지는 카테고리 리스트 (1,2뎁스리스트만 리턴) #

		# 입력 파라미터(input parameter)
			없음

		# 출력 파라미터(output parameter)
			Array
				(
					code => "00" : 성공 , "99" : 오류
					msg => ""
					data => Array
									(
										cid => 카테고리아이디
										cname => 카테고리명
										depth => 카테고리 깊이
										vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 => 깊이 정렬값
										child_category => array(
																				부모카테고리와 파라미터명 같음
																			) ;
																		:
																		:
																		:
									)
																	:
																	:
																	:
				)

			-> 예시
			Array
			(
				[code] = "99"
				[msg] => ""
				[data] => Array
					[0] => Array
						(
							[cid] => 001000000000000
							[cname] => TOP&BLOUSE
							[depth] => 0
							[vlevel1] => 1
							[vlevel2] => 0
							[vlevel3] => 0
							[vlevel4] => 0
							[vlevel5] => 0
							[child_category] => Array
								(
									[0] => Array
										(
											[cid] => 001001000000000
											[cname] => 여성상의
											[depth] => 1
											[vlevel1] => 1
											[vlevel2] => 1
											[vlevel3] => 0
											[vlevel4] => 0
											[vlevel5] => 0
										)
													:
													:
													:
		*************************************************/
		$_RESULT_ = $App->getMainCategorys();
	break;

	case "insertgood":
		/*************************************************
		# 상품등록 #
		
		# 입력 파라미터(input parameter)
			user_data => 회원정보 json 파싱데이터
			basicimagefile => 기본이미지 컬럼명
			cate1 => 카테고리-1
			cate2 => 카테고리-2
			imgfile1
			imgfile2
			imgfile3
			imgfile4
			imgfile5
			imgfile6
			goods_name => 상품명
			coprice => 원가
			wholesaleprice => 도매가
			listprice => 소매가
			stock => 수량
			delivery_price => 배송비
			delivery_policy => 무료배송비 여부 Y or null
			search_keyword => 태그
			basicinfo => 상세설명
			sns_facebook => 공유 페이스북 여부 Y or null
			sns_twitter => 공유 트위터 여부 Y or null

		# 출력 파라미터(output parameter)
			pid => 상품아이디

		*************************************************/
		$_RESULT_ = $App->insertGood($_POST);
	break;

	default:

		$_RESULT_["code"]="99";
		$_RESULT_["msg"]="파라미터 act : ".$_POST["act"]." 에 대한 처리 내용이 없습니다.";
}

if($_GET["debug"]=="1"){
	if($App->ResultDataType == "xml"){
		echo $_RESULT_;
	}else{
		
		
		$_RESULT_ = str_replace("\"true\"","true",json_encode($_RESULT_));
		$_RESULT_ = str_replace("\"false\"","false",$_RESULT_);
		echo $_RESULT_;
		
		//print_r($_RESULT_);
	}
}else{

	//syslog(LOG_NOTICE, json_encode($_POST));
	
	$_RESULT_ = str_replace("\"true\"","true",json_encode($_RESULT_));
	$_RESULT_ = str_replace("\"false\"","false",$_RESULT_);

	//syslog(LOG_NOTICE, json_encode($_RESULT_));
	echo $_RESULT_;
}


function json_encode_align($data){

	$tab_no =1;
	$data_array = explode('"',$data);
	
	foreach($data_array as  $data){
		if($data=="{"){
			echo "{<br/>".for_tab_return($tab_no);
		}elseif(substr_count($data,',') > 0){
			if($data=="},{"){
				echo "<br/>".for_tab_return($tab_no-1)."}<br/><br/>";
				echo for_tab_return($tab_no-1)."{<br/>".for_tab_return($tab_no);
			}else{
				echo $data."<br/>".for_tab_return($tab_no);
			}
		}elseif(substr_count($data,'[') > 0){
			echo ":[<br/>";
			$tab_no++;
			echo for_tab_return($tab_no)."{<br/>";
			$tab_no++;
			echo for_tab_return($tab_no);
		}elseif(substr_count($data,']') > 0){
			$tab_no--;
			echo "<br/>".for_tab_return($tab_no)."}";
			$tab_no--;
			echo "<br/>".for_tab_return($tab_no)."]";
			$tab_no--;
			echo "<br/>".for_tab_return($tab_no)."}";
		}elseif(substr_count($data,'}') > 0){
			echo str_replace("}","",$data);
			$tab_no--;
			echo "<br/>".for_tab_return($tab_no)."}";
		}else{
			if($data==":"){
				echo "".$data."";
			}else{
				echo "'".$data."'";
			}
		}
	}
}

function for_tab_return($no){

	$result="";

	for($i=0;$i<$no;$i++){
		$result .="	";
	}

	return $result;
}


closelog();
?>
