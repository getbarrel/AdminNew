<?

include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database();

//업데이트 하기전 현재 랭킹을 이전 랭킹으로 업데이트
$sql="UPDATE shop_product_ranking SET pre_ranking = ranking";
$db->query($sql);
$sql="UPDATE shop_product_ranking SET ranking = 0";
$db->query($sql);

//주문에서 랭킹순 으로 select
$sql="SELECT sum(pcnt) AS sum_pcnt, pid, cid
FROM shop_order_detail
WHERE ic_date BETWEEN date_format(date_sub(NOW(), INTERVAL 14 DAY),
                                  '%Y-%m-%d 00:00:00')
                  AND date_format(date_sub(NOW(), INTERVAL 1 DAY),
                                  '%Y-%m-%d 23:59:59')
GROUP BY pid
ORDER BY sum_pcnt DESC";
$db->query($sql);

$list = $db->fetchall("object");
for ($i=0;$i < count($list);$i++){

    $sql="SELECT pid FROM shop_product_ranking WHERE pid ='".$list[$i]['pid']."'";
    $db->query($sql);
    if( $db->total > 0 ){
        $sql = "UPDATE shop_product_ranking
        SET cid = '".$list[$i]['cid']."', ranking = '".($i + 1)."'
        WHERE pid = '".$list[$i]['pid']."'";
    }else{
        $sql = "INSERT INTO shop_product_ranking (pid,cid,ranking)
              VALUES ('".$list[$i]['pid']."','".$list[$i]['cid']."','".($i + 1)."')";
    }
    $db->query($sql);
}

//이전 -> 현재 = 변동 순위
//10 -> 6 = 4
//6 -> 10 = -4
//5 -> 5 = 0
//0 -> 0 = 0
//0 -> 50 = 50
//50 -> 0 = -50

$sql="UPDATE shop_product_ranking SET change_ranking = (CASE WHEN pre_ranking > 0 OR ranking > 0 THEN ranking - pre_ranking ELSE pre_ranking - ranking END)";
$db->query($sql);