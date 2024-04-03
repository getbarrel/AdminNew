<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2020-02-11
 * Time: 오후 2:05
 */

include $_SERVER["DOCUMENT_ROOT"]."/class/database.class";

$db = new database;
$slave_mdb = new database;
$master_db = new database;

$add_table = "shop_add_mileage_2";
$use_table = "shop_use_mileage_2";
$remove_table = "shop_remove_mileage_2";
$log_table = "shop_mileage_log_2";
$add_ix = "am_ix";
$use_ix = "um_ix";


$start = $_GET['start'];
$max = '10000';
$limit = "limit $start,$max";

$sql = "select 
            'add' as type , am_ix as idx, uid,
            add_type as input_type,oid,
            od_ix as od_ix, pid as pid,
            am_mileage as mileage,
            am_state as state,
            reserve_type as reserve_type,
            auto_cancel as auto_cancel,
            message,date,regdate,
            extinction_date as extinction_date
        from 
          shop_add_mileage    
 ";

$sql .=" union all ";


$sql .="select 
            'use' as type, um_ix as idx, uid,
            use_type as input_type,oid,
            '' as od_ix, '' as pid,
            um_mileage as mileage,
            um_state as state,
            '' as reserve_type,
            '' as auto_cancel,
            message,date,regdate,
            '' as extinction_date          
            
        from
          shop_use_mileage
 ";
$sql .= " order by regdate asc  $limit";
echo $sql;

$db->query($sql);
$data = $db->fetchall();
echo $db->total;

if(is_array($data)){
    foreach($data as $key=>$val){

        $mileage_data = array();
        $mileage_data['idx'] = $val['idx'];
        $mileage_data['uid'] = $val['uid'];
        $mileage_data['type'] = $val['input_type'];
        $mileage_data['oid'] = $val['oid'];
        $mileage_data['od_ix'] = $val['od_ix'];
        $mileage_data['pid'] = $val['pid'];
        $mileage_data['mileage'] = $val['mileage'];
        $mileage_data['state'] = $val['state'];
        $mileage_data['reserve_type'] = $val['reserve_type'];
        $mileage_data['auto_cancel'] = $val['auto_cancel'];
        $mileage_data['message'] = $val['message'];
        $mileage_data['date'] = $val['date'];
        $mileage_data['regdate'] = $val['regdate'];
        $mileage_data['extinction_date'] = $val['extinction_date'];
        $mileage_data['state_type'] = $val['type'];
        $mileage_data['save_type'] = 'mileage';


        InsertMileageInfo2($mileage_data);
    }
    $next_start = $start+$max;
    echo "<script>location.href='/admin/mileage_migration.php?start=".$next_start."';</script>";
    exit;
}




function InsertMileageInfo2($mileage_data){
    global $slave_mdb, $master_db, $_SESSION;
    global $add_table, $use_table,$remove_table,$log_table,$add_ix,$use_ix;

    extract($mileage_data,EXTR_SKIP);//넘어온 배열의 key 는 변수명 value 값이 변수 값으로 변환 JK

    $GLOBALS['date'] = $date;
    $GLOBALS['regdate'] = $regdate;
    include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");



    if($uid && $mileage > 0){
        $sql = "select mg.selling_type from ".TBL_COMMON_MEMBER_DETAIL." cmd left join ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix where cmd.code = '".$uid."' ";
        $slave_mdb->query($sql);
        $slave_mdb->fetch();

        if($save_type == "point"){

            if($slave_mdb->dt[selling_type] == 'R'){
                $Shared_file = "b2c_point_rule";
                $com_type = 'b2c';
            }else if($slave_mdb->dt[selling_type] == 'W'){
                $Shared_file = "b2b_point_rule";
                $com_type = 'b2b';
            }else{
                $Shared_file = "b2c_point_rule";	//마일리지 설정값 파일명
                $com_type = 'b2c';
            }


        }else{

            if($slave_mdb->dt[selling_type] == 'R'){
                $Shared_file = "b2c_mileage_rule";	//마일리지 설정값 파일명
                $com_type = 'b2c';
            }else if($slave_mdb->dt[selling_type] == 'W'){
                $Shared_file = "b2b_mileage_rule";
                $com_type = 'b2b';
            }else{
                $Shared_file = "b2c_mileage_rule";	//마일리지 설정값 파일명
                $com_type = 'b2c';
            }
        }

        $reserve_data = getBasicSellerSetup($Shared_file);

        if(($reserve_data[mileage_info_use] != "" || $reserve_data[point_use_yn] == "Y") && $uid ){
            if($state_type == 'add'){

                //신규 마일리지 관리 관련 내용 시작  JK160322
                //적립 완료는 * 회원가입 , 배송완료 or 구매확정 , 취소에 의한 재적립 또는 관리자에 의한 수동 적립일때 사용 따라서 state 값이 1 로 접수되면 무조건 마일리지 적립 테이블에 기록

                //마일리지 적립 범위가 본사 상품만 사용이며, 주문에 의한 적립 타입일 경우 아래 조건을 통해 충족 여부를 판단 한다.
                if($reserve_data[mileage_use_yn] == 'N' && $pid){
                    $sql = "select * from ".TBL_SHOP_PRODUCT." where id = '".$pid."' and admin = '".$_SESSION['shopcfg']['company_id']."'";
                    $slave_mdb->query($sql);

                    //적립하고자 하는 상품이 본사 상품이 아닐경우 마일리지 정책에 따라 프로세스를 종료 시킨다.
                    if(empty($slave_mdb->total)){
                        return;
                    }
                }


                //* 적립 테이블과 로그 테이블에 내용 등록

                //이미 동일한 주문건의 적립이 존재 할경우 프로세스 진행 하지 않고 return
                if(!empty($oid) && !empty($od_ix)){
                    $sql = "select * from ".$add_table." where oid = '".$oid."' and od_ix = '".$od_ix."'";
                    $slave_mdb->query($sql);
                    if(!empty($slave_mdb->total)){
                        return;
                    }
                }

                $sql = "select * from ".$add_table." where am_ix = '".$idx."'  ";
                $master_db->query($sql);
                if($master_db->total){
                    //echo $sql;
                    return;
                }

                $sql = "insert into ".$add_table."
								(am_ix,uid,add_type,oid,od_ix,pid,am_mileage,am_state,reserve_type,auto_cancel,message,date,regdate,extinction_date)
							values
								('".$idx."','".$uid."','".$type."','".$oid."','".$od_ix."','".$pid."','".$mileage."','1','".$com_type."','N','".$message."','".$date."','".$regdate."','".$extinction_date."') ";
                $master_db->query($sql);


                $add_type_ix = $idx;
                //$add_type_ix = $master_db->insert_id();

                $sql = "select total_mileage from ".$log_table." where uid = '".$uid."' order by ml_ix desc limit 1";
                $master_db->query($sql);
                $master_db->fetch();

                $total_mileage = $master_db->dt[total_mileage];

                if(empty($total_mileage)){
                    $total_mileage = 0;
                }

                $new_total_mileage = $total_mileage + $mileage;

                $sql = "insert into ".$log_table."
								(uid,log_type,type_ix,oid,od_ix,pid,ptprice,payprice,ml_mileage,total_mileage,ml_state,message,date,regdate)
							values
								('".$uid."','".$state_type."','".$add_type_ix."','".$oid."','".$od_ix."','".$pid."','".$ptprice."','".$payprice."','".$mileage."','".$new_total_mileage."','1','".$message."','".$date."','".$regdate."') ";
                $master_db->query($sql);

                if($save_type == 'point'){
                    $user_sql = "update ".TBL_COMMON_USER." set point = '".$new_total_mileage."' where code = '".$uid."'";
                }else{
                    $user_sql = "update ".TBL_COMMON_USER." set mileage = '".$new_total_mileage."' where code = '".$uid."'";
                }

                //$master_db->query($user_sql);



                if($save_type != "point"){
                    addExtinctionProcess2($mileage, $uid);
                }

            }else if($state_type == 'use'){

                $sql = "select * from ".$use_table." where um_ix = '".$idx."'  ";
                $master_db->query($sql);
                if($master_db->total){
                    return;
                }

                $sql = "insert into ".$use_table."
							(um_ix,uid,use_type,oid,um_mileage,um_state,message,date,regdate)
						values
							('".$idx."','".$uid."','".$type."','".$oid."','".$mileage."','1','".$message."','".$date."','".$regdate."') ";
                $master_db->query($sql);


                $use_type_ix = $idx;
                //$use_type_ix = $master_db->insert_id();

                $sql = "select total_mileage from ".$log_table." where uid = '".$uid."' order by ml_ix desc limit 1";
                $master_db->query($sql);
                $master_db->fetch();

                $total_mileage = $master_db->dt[total_mileage];

                if(empty($total_mileage)){
                    $total_mileage = 0;
                }

                $new_total_mileage = $total_mileage - $mileage;

                $sql = "insert into ".$log_table."
								(uid,log_type,type_ix,oid,od_ix,pid,ptprice,payprice,ml_mileage,total_mileage,ml_state,message,date,regdate)
							values
								('".$uid."','".$state_type."','".$use_type_ix."','".$oid."','".$od_ix."','".$pid."','".$ptprice."','".$payprice."','".$mileage."','".$new_total_mileage."','2','".$message."','".$date."','".$regdate."') ";
                $master_db->query($sql);

                if($save_type == 'point'){
                    $user_sql = "update ".TBL_COMMON_USER." set point = '".$new_total_mileage."' where code = '".$uid."'";
                }else{
                    $user_sql = "update ".TBL_COMMON_USER." set mileage = '".$new_total_mileage."' where code = '".$uid."'";
                }
               // $master_db->query($user_sql);

                if($save_type != "point"){
                    useExtinctionProcess2($mileage, $use_type_ix, $uid);
                }
            }
        }
    }
}

function addExtinctionProcess2($mileage, $uid)
{
    global $master_db;
    global $remove_table;
    $sql ="SELECT `rm_ix`, `rm_mileage`, `um_ix` FROM $remove_table WHERE `uid` = '".$uid."' AND `am_ix` = 0 ORDER BY `rm_ix`";
    //마일리지 amIx = 0 (-마일리지시 입금 amIx 대상이 없을경우..)
    $master_db->query($sql);
    $remainList = $master_db->fetchall("object");
    if (!empty($remainList)) {
        foreach ($remainList as $val) {
            if ($val['rm_mileage'] >= $mileage) {
                extinctionProcess2($mileage, $val['um_ix'], $uid);
                if ($val['rm_mileage'] > $mileage) {
                    putExtinctionMileage2($val['rm_ix'], ($val['rm_mileage'] - $mileage));
                } else {
                    delExtinctionMileage2($val['rm_ix']);
                }
                break;
            } else {
                extinctionProcess2($val['rm_mileage'], $val['um_ix'], $uid);
                delExtinctionMileage2($val['rm_ix']);
                $mileage -= $val['rm_mileage'];
            }
        }
    }
}

/**
 * 마일리지 사용시 소멸 데이터 내역 입력 프로세스
 * @param int $rmMileage
 * @param int $umIx
 */
function useExtinctionProcess2($rmMileage, $umIx, $uid)
{
    extinctionProcess2($rmMileage, $umIx, $uid);
}

/**
 * 마일리지 소멸 프로세스
 * @param type $rmMileage
 * @param type $umIx
 */
function extinctionProcess2($rmMileage, $umIx, $uid)
{
    global $master_db;
    global $add_table, $remove_table;

    if ($rmMileage > 0) {
        $rmMsg = '적립금 사용에 따른 순차적 차감';

        //마일리지 소멸 대상 데이터 추출
        $sql ="SELECT am.am_mileage - SUM(IFNULL(rm_mileage, 0)) AS remove_mileage,
                       `am`.`am_ix`
                FROM $add_table AS `am`
                     LEFT JOIN $remove_table AS `rm` ON `rm`.`am_ix` = `am`.`am_ix`
                WHERE `am`.`uid` = '".$uid."'
                GROUP BY `am`.`am_ix`
                HAVING `remove_mileage` > 0
                ORDER BY `am_ix`";
        //마일리지 amIx = 0 (-마일리지시 입금 amIx 대상이 없을경우..)
        $master_db->query($sql);
        $removeLogs = $master_db->fetchall("object");

        if (!empty($removeLogs)) { //소멸 내역으로 이미 입력된 데이터가 있는지 확인
            foreach ($removeLogs as $k => $v) {
                $amIx = $v['am_ix'];
                if ($v['remove_mileage'] >= $rmMileage) {
                    $amIx = $v['am_ix'];
                    addExtinctionMileage2($uid, 1, $amIx, $umIx, $rmMileage, $rmMsg);
                    $rmMileage = 0;
                    break;
                } else {
                    addExtinctionMileage2($uid, 1, $amIx, $umIx, $v['remove_mileage'], $rmMsg);
                    $rmMileage -= $v['remove_mileage'];
                }
            }
        }
        //마일리지 - 인 경우 처리
        if ($rmMileage > 0) {
            //우선 amIx를 0으로 처리후 위에서 추가 차감처리
            addExtinctionMileage2($uid, 1, 0, $umIx, $rmMileage, $rmMsg);
        }
    }
}

/**
 * 마일리지 소멸 내역 입력
 * @param int $type
 * @param int $amIx
 * @param int $umIx
 * @param int $mileage
 * @param string $msg
 */
function addExtinctionMileage2($uid, $type, $amIx, $umIx, $mileage, $msg)
{
    global $master_db,$date,$regdate;
    global $remove_table;

    if($date){
        $sql = "insert into $remove_table
				(am_ix,um_ix,rm_mileage,message,uid,rm_state,date,regdate)
			values
				('".$amIx."','".$umIx."','".$mileage."','".$msg."','".$uid."','".$type."','".$date."','".$regdate."') ";
    }else {
        $sql = "insert into $remove_table
				(am_ix,um_ix,rm_mileage,message,uid,rm_state,date,regdate)
			values
				('" . $amIx . "','" . $umIx . "','" . $mileage . "','" . $msg . "','" . $uid . "','" . $type . "',NOW(),NOW()) ";
    }
    $master_db->query($sql);
}

/**
 * 마일리지 소멸 내역 수정
 * @param type $rmIx
 * @param type $rmMileage
 * @return type
 */
function putExtinctionMileage2($rmIx, $rmMileage)
{
    global $master_db;
    global $remove_table;

    $sql = "update $remove_table set rm_mileage='".$rmMileage."' where rm_ix='".$rmIx."'";
    $master_db->query($sql);
}

/**
 * 마일리지 소멸 내역 삭제
 * @param int $rmIx
 */
function delExtinctionMileage2($rmIx)
{
    global $master_db;
    global $remove_table;

    $sql = "delete from $remove_table where rm_ix='".$rmIx."'";
    $master_db->query($sql);
}

function getBasicSellerSetup($Shared_file){

    if(!$Shared_file){
        return false;
    }

    global $_SESSION,$slave_mdb;

    include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");

    $shmop = new Shared($Shared_file);

    if($_SESSION["admininfo"]["mall_data_root"]){
        $mall_data_root = $_SESSION["admininfo"]["mall_data_root"];
    }else if($_SESSION["layout_config"]["mall_data_root"]){
        $mall_data_root = $_SESSION["layout_config"]["mall_data_root"];
    }else{
        $sql = "select mall_data_root from shop_shopinfo where mall_div = 'B'";
        $slave_mdb->query($sql);
        $slave_mdb->fetch();
        $mall_data_root = $slave_mdb->dt['mall_data_root'];
    }

    $shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$mall_data_root."/_shared/";

    $shmop->SetFilePath();
    $reserve_data = $shmop->getObjectForKey($Shared_file);
    $reserve_data = unserialize(urldecode($reserve_data));

    return $reserve_data;
}