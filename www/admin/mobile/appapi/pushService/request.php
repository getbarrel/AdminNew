<?php
/**
 * 안드로이드 푸시 서비스(GCM) 요청페이지
 *
 * @author bgh
 * @last date 2016.03.18
 */
include("./androidpush.php");
include("./iospush.php");


/**
 * senderId & apikey 구하기
 *
 * http://developer.android.com/google/gcm/gs.html
 * 1. create project
 * 2. service -> Google Cloud Messaging for Android 'ON'
 * 3. see URL Querystring '#project:xxxxxxxx' => senderId
 * 4. 밑에 값 바꾸기 & WebviewActivity내에 센더아이디 값 동일 하게 변경하기
 * 5. enjoy :)
 * ※ 권한 에러 401 일시 등록된 아이피 체크 해보기!
 */
$app_div = "webapp";


include("./push.ini.php");
$con = new androidPush($ios_pem,$android_apikey);

//구글 GCM 으로도 아이폰 푸쉬 가능해서 수정
$icon = new iosPush($ios_pem,$android_apikey);


$contents_type = $_POST['contents_type'];
$contents      = $_POST['contents'];
$title         = $_POST['title'];
$link          = $_POST['link'];
$push_title    = trim($_POST['push_title']);


$device_type		= $_POST['device_type'];		// I 아이폰 A 안드로이드
$send_time_type		= $_POST['send_time_type'];		//0:즉시발송 1:예약발송
$send_time_sms		= $_POST['send_time_sms'];		//예약시 날짜
$send_time_hour		= $_POST['send_time_hour'];		//예약시 시
$send_time_minite	= $_POST['send_time_minite'];	//예약시 분
$update_type		= $_POST['update_type'];		//1:검색한회원 전체 2:선택한회원
$query_string		= $_POST['query_string'];		//회원검색관련 query_string
$str_code_list		= $_POST['str_code_list'];		//선택한 회원 코드 ',' 로 나누어져 있다.

$str_code_list		= substr($str_code_list,0,strlen($str_code_list)-1);
$query_string		= urldecode($query_string);

if(!$push_title){
	if(!empty($_SESSION["admininfo"]["company_name"])){
		$push_title = $_SESSION["admininfo"]["company_name"];
	}else{
		$push_title = $_SERVER["HOST_NAME"];
	}

}

//프로토콜 설정
if(! empty($_SERVER['HTTPS'])){
	$protocol = "https://";
}else{
	$protocol = "http://";
}

//링크에 프로토콜 추가
if(substr_count($link, 'http') == 0 && $link){
	$link = $protocol . $link;
}
$data = array(
				'app_div'       => "webapp",
				'title'         => $title,
				'contents_type' => $contents_type,
				'link'          => $link,
				'push_title'    => $push_title,

				'device_type' => $device_type,
				'send_time_type' => $send_time_type,
				'send_time_sms' => $send_time_sms,
				'send_time_hour' => $send_time_hour,
				'send_time_minite' => $send_time_minite,
				'update_type' => $update_type,
				'query_string' => $query_string,
				'str_code_list' => $str_code_list,

				'is_dual_push' => "0"
			);

switch( $contents_type ){
	//이미지 보내는 경우
	case 'img':
		if(! empty($_FILES['push_img']['size'])){
			//upload image
			$dest = makeFileName( $_FILES['push_img']['name'] );

			if(! copy($_FILES['push_img']['tmp_name'], $dest)){
				echo "<script>alert('file upload fail');</script>";
				exit;
			}

			//msg = download url
			$url = str_replace($_SERVER['DOCUMENT_ROOT'], $_SERVER['HTTP_HOST'], $dest);

			//이미지 다운로드 url에 http추가
			$data['title'] = $title;
			$data['msg'] = $protocol . $url;
			$data['description'] = $contents;

			//DB저장용
			$data['contents'] = "http://".$url;//str_replace($_SERVER['DOCUMENT_ROOT'], $_SERVER['HTTP_HOST'], $dest);
		}
		break;
	case 'noti_img':
		if(! empty($_FILES['noti_img']['size'])){
			//upload image
			$dest = makeFileName( $_FILES['noti_img']['name'] );

			if(! copy($_FILES['noti_img']['tmp_name'], $dest)){
				echo "<script>alert('file upload fail');</script>";
				exit;
			}

			//msg = download url
			$url = str_replace($_SERVER['DOCUMENT_ROOT'], $_SERVER['HTTP_HOST'], $dest);

			//이미지 다운로드 url에 http추가
			$data['title'] = $title;
			$data['msg'] = $protocol . $url;
			$data['description'] = $contents;
			//DB저장용
			$data['contents'] = "http://".$url;//str_replace($_SERVER['DOCUMENT_ROOT'], $_SERVER['HTTP_HOST'], $dest);
		}
		break;
	//텍스트 보내는 경우
	case 'txt':
		$data['title']      = $title;
		$data['msg']      = $contents;
		$data['contents'] = $contents;
		break;
	default:
		echo "<script>alert('bad_request');</script>";
		exit;

}
if(empty($data['contents'])){
	if( $contents_type == 'img' ||  $contents_type == 'noti_img'){
		echo "<script>alert('이미지를 등록하세요');</script>";
	}else{
		echo "<script>alert('내용을 입력하세요');</script>";
	}
	exit;
}

//예약 푸시일 경우에는 DB에 저장해준다.
if ( $data["send_time_type"] == "1") {
	//예약푸시가 아닌 경우.
	if( $device_type == 'A') {
		$result = $con->requestPushReserve($data);
	}
	else if(  $device_type == 'I') {
		$result = $icon->requestPushReserve($data);
	}
	else{
		$result = $con->requestPushReserve($data);
		$result = $icon->requestPushReserve($data);
	}
} else {

	//예약푸시가 아닌 경우.
	if( $device_type == 'A') {
		$result = $con->requestPush($data);
	}
	else if(  $device_type == 'I') {
		$result = $icon->requestPush($data);
	}
	else{
		$data[is_dual_push] = "1";
		$result = $con->requestPush($data);
		$result = $icon->requestPush($data);
	}
}

if ($data["send_time_type"] == "1") {
	if($result[result] == TRUE){
		echo "<script>alert('푸시 예약 발송이 저장되었습니다.');</script>";
	}else{
		echo "<script>alert('푸시 예약 발송이 실패하였습니다.');</script>";
	}
}
else {
	if($result == TRUE){
		echo "<script>alert('발송완료 되었습니다.');</script>";
	}else{
		echo "<script>alert('발송실패 하였습니다.');</script>";
	}
}



/**
 * 업로드 파일 명 만들기
 * @param string $file_name
 *
 * @access public
 * @return string
 */
function makeFileName( $file_name ){
	$date = date('Y_m_d');
	$rand = rand(1000,9999);
	$ext = substr(strrchr($file_name,"."),1);
	$ext = strtolower($ext);
	$base_src = $_SERVER['DOCUMENT_ROOT'].$_SESSION["admin_config"]["mall_data_root"].'/images/push_img/';
	if(! is_writable( $base_src )){
		@mkdir($base_src);
		@chmod($base_src, 0777);
	}
	//$dest = $base_src . $date . '_' . $rand . '_' . $file_name;
	$dest = $base_src . $date . '_' . $rand . $ext;
	if(file_exists( $dest )){
		//파일이 존재하면 다시 만든다.
		$dest = makeFileName( $file_name );
	}
	return $dest;
}
