<?

include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database();

$sql="SELECT round(
          avg(
               (unix_timestamp(di_date) - unix_timestamp(ic_date))
             / 60
             / 60
             / 24),
          2)
          AS average_delivery_day,
       company_id
FROM shop_order_detail
WHERE     ic_date >= date_sub(NOW(), INTERVAL 1 MONTH)
      AND di_date IS NOT NULL
      AND ic_date < di_date
GROUP BY company_id";
$db->query($sql);

if( $db->total > 0 ){
    $list = $db->fetchall("object");
    for ($i=0;$i < count($list);$i++){
        $sql = "UPDATE common_seller_detail
            SET average_delivery_day = '".$list[$i]['average_delivery_day']."'
            WHERE company_id = '".$list[$i]['company_id']."'";
        $db->query($sql);
    }
}
