<?php 
/**
 * 앱에서 GCM 등록키 DB삭제 호출 페이지
 * 
 * @author bgh 
 * @date 2014.07.17
 */
include ("./androidpush.php");
$con = new androidPush();


$result = array('result'=>false,'code'=>400,'msg'=>'need receive_key');

$input = array(	
				'receive_key' => $_REQUEST["regId"],
				'app_div' => 'webapp'
			);
if(!empty($input['receive_key'])){
	$result = $con->deleteRegistId( $input );
}

echo json_encode($result);