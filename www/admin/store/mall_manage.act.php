<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");

$member_reg_rule[join_type] = $_POST["join_type"];
$member_reg_rule[mall_open_yn] = $_POST["mall_open_yn"];
$member_reg_rule[auth_type] = $_POST["auth_type"];					//일반 회원 자동 승인여부
$member_reg_rule[b2b_auth_type] = $_POST["b2b_auth_type"];			//사업자회원 자동 승인여부
$member_reg_rule[seller_auth_type] = $_POST["seller_auth_type"];	//셀러회원 자동 승인여부
$member_reg_rule[auth_method] = $_POST["auth_method"];
$member_reg_rule[mall_use_identificationUse] = $_POST["mall_use_identificationUse"];
$member_reg_rule[mall_use_identification] = $_POST["mall_use_identification"];

$member_reg_rule[mall_use_ipin] = $_POST["mall_use_ipin"]; //아이핀 사용유무
$member_reg_rule[mall_ipin_code] = $_POST["mall_ipin_code"]; // 회원사 코드 ex)D040
$member_reg_rule[mall_ipin_pw] = $_POST["mall_ipin_pw"]; // 사이트 패스워드 ex)30360046

$member_reg_rule[mall_use_certify] = $_POST["mall_use_certify"]; //본인인증 사용유무
$member_reg_rule[mall_certify_code] = $_POST["mall_certify_code"]; // 사이트코드 ex)N249
$member_reg_rule[mall_certify_pw] = $_POST["mall_certify_pw"]; // 사이트 비밀번호 ex)77938475

$member_reg_rule[mall_use_com_number] = $_POST["mall_use_com_number"]; //사업자 인증 사용여부
$member_reg_rule[mall_com_number_id] = $_POST["mall_com_number_id"]; // 사업자 회원사 아이디
$member_reg_rule[mall_com_number_pw] = $_POST["mall_com_number_pw"]; // 사업자 사이트 식별코드

$member_reg_rule[recommend_use] = $_POST["recommend_use"]; //추천인 사용여부
$member_reg_rule[recommend_use_reserve] = $_POST["recommend_use_reserve"]; // 추천인 지급되는 마일리지 금액

$member_reg_rule[mall_deny_id] = $_POST["mall_deny_id"];


/*			>>>>>>>>>>>>>>>>> 개정법안 추가 kbk 13/02/16 >>>>>>>>>>>>>>>>>>>[Start] */
$member_reg_rule[email_auth] = $_POST["email_auth"];
$member_reg_rule[join_type]=$_POST["join_type_b"].$_POST["join_type_f1"].$_POST["join_type_c"];
if($member_reg_rule[join_type]=="") $member_reg_rule[join_type]="B";

/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<[End] */

$data = urlencode(serialize($member_reg_rule));

$shmop = new Shared("member_reg_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$shmop->setObjectForKey($data,"member_reg_rule");
//session_start();

$db = new Database;
$sql = "update shop_shopinfo set 
		mall_deny_id = '$mall_deny_id' , mall_open_yn = '$mall_open_yn' , mall_use_identification = '$mall_use_identification'  
		where mall_ix = '".$admininfo[mall_ix]."'  "; //and mall_div = '".$admininfo[mall_div]."'

$db->query($sql);

echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('회원가입 설정이 정상적으로 처리되었습니다.');parent.document.location.reload();</script>");
?>