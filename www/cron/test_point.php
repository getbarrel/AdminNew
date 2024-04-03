<?php
include("$DOCUMENT_ROOT/class/layout.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/admin/logstory/class/sharedmemory.class');

echo '------------------------------------------------ <br>';
$user_id = 'ka@CUwSHIYZaO';
$user_pw = '11111!A';

$makeshop_id = 'dewytree1';
echo  hash("sha512", md5($user_pw) . $makeshop_id . $user_id);
echo '<br>94274134cd72db9d7f11e0f5b8c728469331842ea68134d8d50c1ba593a79ca8';
/*  #####################################################################################################  */
exit;
// -- 차감 기준으로 다시 계산해보자

//공휴일  , 단위
$sql = "SELECT  * FROM shop_mall_config WHERE config_name = 'holiday_text'AND mall_ix = 'bd926889c59e66f37a2ac64b56949a56' ";
$db->query($sql);
$q1 = $db->fetchall("object");
$holiday_text = $q1[0]['config_value'];

//주문 자동 취소일
$sql = "SELECT  * FROM shop_mall_config WHERE config_name = 'mall_cc_interval'AND mall_ix = 'bd926889c59e66f37a2ac64b56949a56' ";
$db->query($sql);
$q2 = $db->fetchall("object");
$mall_cc_interval = $q2[0]['config_value'];
$mall_cc_interval = 5;

$order_date = date('Ymd', strtotime('2019-03-15')); //오늘 주문일
echo '<hr>';
echo  'order_date == '; echo $order_date;
echo 'mall_cc_interval === '.$mall_cc_interval.'  ';
echo '<hr>';



$ex_hoilday = explode(',',$holiday_text); //추가 휴일
echo 'holiday_text';
print_r($ex_hoilday);
$i = 0;//무한루프 안빠지기 위한 방어코딩
$mus_date = 0; //휴일이 추가 되는 일자

/// 우선 최대 돌아야 하는 기준은?
/// 영업일 기준 5일 !

//영업일 기준으로 일단 형변환해서 날짜를 가지고 있음
$date = date('Ymd' ,strtotime($order_date));
$odate = date('Ymd' ,strtotime($order_date));
//$date = date('Ymd' ,strtotime($order_date));
//$date = date('Ymd' ,strtotime($date. '+1 day'));

//while($mall_cc_interval >= 0){
//    echo $j.'__';
//    $j++;
//    $mall_cc_interval--;
//}

//자동 취소일  만큼 무조건 돈다
for($i = 0; $mall_cc_interval >= 1; $i++){
    echo '<br>현재 비교일'.$date. '__i =='.$i;
    $is_hoily = false;
    //해당일이 토요일 일요일 인지 구분
    if(date('w',strtotime($date)) == 0 || date('w',strtotime($date)) == 6){
        //주말 휴일은 영업일이 아니니 플러스
        //$date = date('Ymd' ,strtotime($date. '+1 day'));
        $is_hoily = true;
        echo '<hr>주말 ::'.$date.'::';
    }else{
        //나머지 평일
        //설정 휴일 값 계산
        foreach($ex_hoilday as $key => $val){
            //휴일 값이 존재 * 잘못된 값이 들어오면 0으로 되어서 에러회피 기능 가능
            //휴일 값이 월~금요일 중에 오늘과 동일 하면 하루 플러스
            if(strtotime($val) && strtotime($val) == strtotime($date)) {
                //$date = date('Ymd' ,strtotime($date. '+1 day'));
                $is_hoily = true;
                echo '<hr>휴일 ::'.$date.'::';
            }
        }
    }

    //비교날짜 +1
    $date = date('Ymd' ,strtotime($date. '+1 day'));
    if($is_hoily){
        //휴일 추가
        $odate = date('Ymd' ,strtotime($odate. '+1 day'));
    }else{
        //일반 영업일 추가 일반일이니 차감
        if($mall_cc_interval > 0) {
            $odate = date('Ymd', strtotime($odate . '+1 day'));
        }
        $mall_cc_interval--;
    }

    echo '<hr>영업일 기준  ::'.$odate.'::';
    echo 'cc =='. $mall_cc_interval;
    echo '<hr>';
    if($i >= 365){
        //무한 루프 방지. 최대 365일을 넘어갈수는 없다.
        break;
    }
}

//echo '===>'.$date;
var_dump($odate);



exit;

//공휴일  , 단위
$sql = "SELECT  * FROM shop_mall_config WHERE config_name = 'holiday_text'AND mall_ix = 'bd926889c59e66f37a2ac64b56949a56' ";
$db->query($sql);
$q1 = $db->fetchall("object");
$holiday_text = $q1[0]['config_value'];

//주문 자동 취소일
$sql = "SELECT  * FROM shop_mall_config WHERE config_name = 'mall_cc_interval'AND mall_ix = 'bd926889c59e66f37a2ac64b56949a56' ";
$db->query($sql);
$q2 = $db->fetchall("object");
$mall_cc_interval = $q2[0]['config_value'];
$mall_cc_interval = 5;

$order_date = date('Ymd', strtotime('2019-03-15')); //오늘 주문일
echo '<hr>';
echo  'order_date == '; echo $order_date;
echo 'mall_cc_interval === '.$mall_cc_interval.'  ';
echo '<hr>';



$ex_hoilday = explode(',',$holiday_text); //추가 휴일
echo 'holiday_text';
print_r($ex_hoilday);
$i = 0;//무한루프 안빠지기 위한 방어코딩
$add_date = 0; //휴일이 추가 되는 일자



//주말 만큼 더함
for($i=1; $i <= $mall_cc_interval; $i++){
    $date = date('Ymd' ,strtotime($order_date. '+'.$i.' day'));
    //0 부터 일요일  6은 토요일
    if(date('w',strtotime($date)) == 0 || date('w',strtotime($date)) == 6){
        $add_date++;
    }
}

echo 'add_date ==';
echo $add_date;

//휴일 추가 만큼 더하기
foreach($ex_hoilday as $key=>$val){
    //날짜형태가 맞아야만 가능 방어코딩
    if(strtotime($val) && strtotime($val) >= strtotime($order_date)) {
        if(date('w', strtotime($val)) >= 1 && date('w', strtotime($val)) <= 5) {
            $add_date++;
        }
    }
}

echo 'add_date2 ==';
echo $add_date;

$last_day = $mall_cc_interval + $add_date; //기존 취소일 + 휴가 추가 일
$date = date('Ymd' ,strtotime($order_date .' + '.$last_day.' day'));

//최종으로 휴일자가 끼워져있으면 그 다음 일까지만 더함
while ($i < 100){
    $i++;
    if(date('w',strtotime($date)) >= 1 && date('w',strtotime($date)) <= 5){
        //휴일 추가 만큼 더하기
        foreach($ex_hoilday as $key=>$val){
            //같은 날짜가 있으면 +1
            if(strtotime($val) == strtotime($date)) {
                $date = date('Ymd' ,strtotime($date. '+1 day'));
            }
        }
        break;
    }else{
        $date = date('Ymd' ,strtotime($date. '+1 day'));
    }
}

echo '<hr> total ==';
echo $date;


//잘못 구함..
exit;
$add_date = 0;
for($i=1; $i <= $mall_cc_interval; $i++){
    $date = date('Y-m-d' ,strtotime($order_date. '+'.$i.' day'));
    //0 부터 일요일  6은 토요일
    if(date('w',strtotime($date)) == 0 || date('w',strtotime($date)) == 6){
        $add_date++;
    }
}

//$holiday_text = '--';
$ex_hoilday = explode(',',$holiday_text);
//print_r($ex_hoilday);

//휴일 추가 만큼 더하기
foreach($ex_hoilday as $key=>$val){
    //날짜형태가 맞아야만 가능 -- 에러 방지
    if(strtotime($val)) {
        if(date('w', strtotime($val)) >= 1 && date('w', strtotime($val)) <= 5) {
            $add_date++;
        }
    }
}
 echo '추가일 ' . $add_date;
//추가일 + 인터벌 최종으로 더한 날짜를 기준으로 토요일이면 +0 일요일 이면 +1
$add_date = $add_date + $mall_cc_interval;
$date = date('Y-m-d' ,strtotime($order_date. '+'.$add_date.' day'));

if(date('w',strtotime($date)) == 0){
    $add_date = $add_date +1;
}

if(date('w',strtotime($date)) == 6){
    $add_date = $add_date +2;
}
///최종 날짜
echo '<hr>최종배송일:';
echo $date = date('Y-m-d' ,strtotime($order_date. '+'.$add_date.' day'));
exit;


/*
 * 쿠폰 정책
 *

$shmop = new Shared("b2c_coupon_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/_shared/";
$shmop->SetFilePath();
$coupon_data = $shmop->getObjectForKey("b2c_coupon_rule");
$coupon_data = unserialize(urldecode($coupon_data));
*/


/**
 * 마일리지 정책
 */
$shmop = new Shared("b2c_mileage_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$reserve_data = $shmop->getObjectForKey("b2c_mileage_rule");
$reserve_data = unserialize(urldecode($reserve_data));


echo UserSellingType();


echo '<pre>'; print_r($reserve_data); echo '</pre>';


$reserve_data[use_unit] = 15;
$reserve_data[mileage_use_yn] = 'Y';

/**
 * 굽기..
 */
//$data = urlencode(serialize($reserve_data));
//$path = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
//if(!is_dir($path)){
//    mkdir($path, 0777);
//    chmod($path,0777);
//}else{
//    chmod($path,0777);
//}
//
//$shmop = new Shared("b2c_mileage_rule");
//$shmop->filepath = $path;
//$shmop->SetFilePath();
//$shmop->setObjectForKey($data,"b2c_mileage_rule");


/*
 * function GetReserveRate(){	//적립금 설정 불러오기
    global $_SESSION, $slave_mdb;

    if(UserSellingType() == 'R'){
        $Shared_file = "b2c_mileage_rule";	//마일리지 설정값 파일명
    }else if(UserSellingType() == 'W'){
        $Shared_file = "b2b_mileage_rule";
    }else{
        $Shared_file = "b2c_mileage_rule";	//마일리지 설정값 파일명
    }

    //적립금정보 가져옴
    $reserve_data = get_shared_memory($Shared_file);

    //그룹별 적용 적립금 상태이면 변경
    if($reserve_data['mileage_info_use'] == "FS"){
        $gp_ix = 0;
        if(isset($_SESSION['user']['gp_ix'])) $gp_ix = $_SESSION['user']['gp_ix'];
        //echo $gp_ix;
        $sql = "select  group_reserve_rate from shop_groupinfo where gp_ix = ".$gp_ix." limit 1";
        $slave_mdb->query($sql);
        $slave_mdb->fetch();
        $reserve_data['goods_mileage_rate_2'] = $slave_mdb->dt['group_reserve_rate'];
        $reserve_data['mobile_mileage_rate_2'] = $slave_mdb->dt['group_reserve_rate'];
    }


    return $reserve_data;

}
 */
?>

<html>
<head>
    <script
        src="https://code.jquery.com/jquery-3.3.1.slim.js"
        integrity="sha256-fNXJFIlca05BIO2Y5zh1xrShK3ME+/lYZ0j+ChxX2DA="
        crossorigin="anonymous"></script>
</head>
<li id="low_level">
    <div class="stit"><a href="#none">초급</a></div>
</li>
<li id="middle_level">
    <div class="stit"><a href="#none">중급</a></div>
</li>
<li id="high_level">
    <div class="stit"><a href="#none">고급</a></div>
</li>


</html>
