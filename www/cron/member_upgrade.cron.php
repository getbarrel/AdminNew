<?

/**
 *  회원그룹 자동업데이트 크론 - 테이블 락을 피하기 위해 query 나누어주기.
 *
 * @author pyw
 * @date 2016.03.24
 */

set_time_limit(9999999999);
ini_set('memory_limit', -1);

include($_SERVER["DOCUMENT_ROOT"] . "/class/database.class");
include($_SERVER["DOCUMENT_ROOT"] . "/include/global_util.php");

$db = new Database();
$db2 = new Database();

// all_disp     : 자동설정사용여부
// gp_type      : 회원그룹 갱신일 (1:매월 1일, 2:매일, 3:매주 월요일, 4:매년 1월 1일)

$sql = "SELECT all_disp, gp_type FROM shop_groupinfo LIMIT 0, 1";
$db->query($sql);
$db->fetch();

$all_disp = $db->dt[all_disp];
$gp_type = $db->dt[gp_type];

//자동설정사용여부를 사용할때만 적용되어야 한다.
if ($all_disp !== "1") {
    exit;
}
//회원그룹 갱신일 (1:매월 1일, 2:매일, 3:매주 월요일, 4:매년 1월 1일)
//테스트 모드
if ($gp_type == '1') {
    if (date("d") != "01") {
        exit;
    }
} else if ($gp_type == '3') {
    if (date("w") != "1") {
        exit;
    }
} else if ($gp_type == '4') {
    if (!(date("m") == "01" && date("d") == "01")) {
        exit;
    }
}

$not_gp_ix = array('8');

//사용중인 그룹목록을 가져온다.
$sql = "SELECT
			gp_ix
			, gp_name
			, order_price
			, ed_order_price
		FROM shop_groupinfo
		WHERE disp = '1'
		 AND gp_ix not in ('" . implode("','", $not_gp_ix) . "')
		 ORDER BY gp_level ASC";
$db->query($sql);
$arr_gp_data = $db->fetchall("object");

$sql = "SELECT
    cmd.code
    ,cmd.gp_ix
FROM common_user cu, common_member_detail cmd
WHERE cu.code=cmd.code and cu.mem_type='M' and cmd.gp_ix not in ('" . implode("','", $not_gp_ix) . "')";
$db->query($sql);
$members = $db->fetchall("object");

if (!is_array($members)) {
    continue;
}

//사용중인 그룹을 루프 돌린다.
//그 후 산정기간을 산출해 회원을 뽑아 온다.
foreach ($members as $mem_key => $mem_data) {

    $first_day = date('Y-m-d', strtotime('-1 month'));  //산정기간의 첫날
    $last_day = date('Y-m-d', strtotime('-1 day'));    //지난달 마지막일

    //매출액을 가져온다.
    $sql = "SELECT
				SUM(CASE WHEN op.payment_status ='F' THEN (-product_price-delivery_price) ELSE (product_price+delivery_price) END) AS total_price
				FROM (
					SELECT sod.oid FROM shop_order_detail sod INNER JOIN shop_order so ON sod.oid = so.oid
					WHERE user_code = '" . $mem_data["code"] . "' AND
					sod.status IN ('DC','BF') AND sod.dc_date BETWEEN '" . $first_day . "' AND '" . $last_day . "' + INTERVAL 1 DAY GROUP BY sod.oid
				) od
				LEFT JOIN shop_order_price op ON od.oid=op.oid";
    $db2->query($sql);
    $db2->fetch();

    $total_price = $db2->dt["total_price"]; //누구냐.... 2 빼먹은 사람....
    if (empty($total_price)) {
        $total_price = 0;
    }
    foreach ($arr_gp_data as $gp_key => $gp_value) {
        //그룹 등급 업 기준이 된다면 등급업을 해준다.

        if ($gp_value["order_price"] <= $total_price && $total_price < $gp_value["ed_order_price"]) {
            if ($mem_data['gp_ix'] != $gp_value['gp_ix']) {
                $sql = "UPDATE common_member_detail SET
                        gp_ix = '" . $gp_value['gp_ix'] . "'
                        , gp_change_date = now()
                    WHERE
                        code = '" . $mem_data["code"] . "'";

                $db2->query($sql);
                $txt = '회원그룹 [' . $gp_value['gp_name'] . '] 으로 변경';
                member_edit_history($mem_data["code"], 'gp_ix', $txt, $mem_data['gp_ix'], $gp_value['gp_ix'], "", "시스템(CRON)", '/cron/member_upgrade.cron.php');
                preferredConditionGiveCoupon('2', $mem_data["code"], $gp_value['gp_ix']);
            }
        }   // foreach $members
    }
}// foreach $arr_gp_data
