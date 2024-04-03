<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

set_time_limit(9999999999);
ini_set('memory_limit',-1);

$db = new MySQL;

$basic_date = date("Y-m-d", strtotime('-1 day'));
$date_1 = date("Y-m-d", strtotime('-1 day'));
$date_7 = date("Y-m-d", strtotime('-7 day'));
$date_10 = date("Y-m-d", strtotime('-10 day'));
$date_15 = date("Y-m-d", strtotime('-15 day'));
$date_30 = date("Y-m-d", strtotime('-30 day'));

$sql="select p.id from shop_product p ";
$db->query($sql);
$products = $db->fetchall("object");

foreach($products as $p){
	$sql="select 
			ifnull((select sum(pcnt) from shop_order_detail where ic_date between '".$date_1." 00:00:00' and '".$basic_date." 23:59:59' and pid='".$p['id']."' ),0) as order_cnt_1,
			ifnull((select sum(pcnt) from shop_order_detail where ic_date between '".$date_7." 00:00:00' and '".$basic_date." 23:59:59' and pid='".$p['id']."' ),0) as order_cnt_7,
			ifnull((select sum(pcnt) from shop_order_detail where ic_date between '".$date_10." 00:00:00' and '".$basic_date." 23:59:59' and pid='".$p['id']."' ),0) as order_cnt_10,
			ifnull((select sum(pcnt) from shop_order_detail where ic_date between '".$date_15." 00:00:00' and '".$basic_date." 23:59:59' and pid='".$p['id']."' ),0) as order_cnt_15,
			ifnull((select sum(pcnt) from shop_order_detail where ic_date between '".$date_30." 00:00:00' and '".$basic_date." 23:59:59' and pid='".$p['id']."' ),0) as order_cnt_30,
			ifnull((select sum(nview_cnt) from commerce_viewingview where vdate between '".str_replace("-","",$date_1)."' and '".str_replace("-","",$basic_date)."' and pid='".$p['id']."' ),0) as view_cnt_1,
			ifnull((select sum(nview_cnt) from commerce_viewingview where vdate between '".str_replace("-","",$date_7)."' and '".str_replace("-","",$basic_date)."' and pid='".$p['id']."' ),0) as view_cnt_7,
			ifnull((select sum(nview_cnt) from commerce_viewingview where vdate between '".str_replace("-","",$date_10)."' and '".str_replace("-","",$basic_date)."' and pid='".$p['id']."' ),0) as view_cnt_10,
			ifnull((select sum(nview_cnt) from commerce_viewingview where vdate between '".str_replace("-","",$date_15)."' and '".str_replace("-","",$basic_date)."' and pid='".$p['id']."' ),0) as view_cnt_15,
			ifnull((select sum(nview_cnt) from commerce_viewingview where vdate between '".str_replace("-","",$date_30)."' and '".str_replace("-","",$basic_date)."' and pid='".$p['id']."' ),0) as view_cnt_30,
			ifnull((select count(*) from shop_wishlist where regdate between '".$date_1."' and '".$basic_date."' and pid='".$p['id']."' ),0) as wish_cnt_1,
			ifnull((select count(*) from shop_wishlist where regdate between '".$date_7."' and '".$basic_date."' and pid='".$p['id']."' ),0) as wish_cnt_7,
			ifnull((select count(*) from shop_wishlist where regdate between '".$date_10."' and '".$basic_date."' and pid='".$p['id']."' ),0) as wish_cnt_10,
			ifnull((select count(*) from shop_wishlist where regdate between '".$date_15."' and '".$basic_date."' and pid='".$p['id']."' ),0) as wish_cnt_15,
			ifnull((select count(*) from shop_wishlist where regdate between '".$date_30."' and '".$basic_date."' and pid='".$p['id']."' ),0) as wish_cnt_30,

			ifnull((select count(*) from bbs_after where regdate between '".$date_1."' and '".$basic_date."' and bbs_etc1='".$p['id']."' ),0) as after_score_1,
			ifnull((select count(*) from bbs_after where regdate between '".$date_7."' and '".$basic_date."' and bbs_etc1='".$p['id']."' ),0) as after_score_7,
			ifnull((select count(*) from bbs_after where regdate between '".$date_10."' and '".$basic_date."' and bbs_etc1='".$p['id']."' ),0) as after_score_10,
			ifnull((select count(*) from bbs_after where regdate between '".$date_15."' and '".$basic_date."' and bbs_etc1='".$p['id']."' ),0) as after_score_15,
			ifnull((select count(*) from bbs_after where regdate between '".$date_30."' and '".$basic_date."' and bbs_etc1='".$p['id']."' ),0) as after_score_30,

			ifnull((select count(*) from bbs_premium_after where regdate between '".$date_1."' and '".$basic_date."' and bbs_etc1='".$p['id']."' ),0) as p_after_score_1,
			ifnull((select count(*) from bbs_premium_after where regdate between '".$date_7."' and '".$basic_date."' and bbs_etc1='".$p['id']."' ),0) as p_after_score_7,
			ifnull((select count(*) from bbs_premium_after where regdate between '".$date_10."' and '".$basic_date."' and bbs_etc1='".$p['id']."' ),0) as p_after_score_10,
			ifnull((select count(*) from bbs_premium_after where regdate between '".$date_15."' and '".$basic_date."' and bbs_etc1='".$p['id']."' ),0) as p_after_score_15,
			ifnull((select count(*) from bbs_premium_after where regdate between '".$date_30."' and '".$basic_date."' and bbs_etc1='".$p['id']."' ),0) as p_after_score_30";
	$db->query($sql);
	$db->fetch();

	$sql="update
			shop_product_addinfo
		set
			order_cnt_1 = '".$db->dt['order_cnt_1']."',
			order_cnt_7 = '".$db->dt['order_cnt_7']."',
			order_cnt_10 = '".$db->dt['order_cnt_10']."',
			order_cnt_15 = '".$db->dt['order_cnt_15']."',
			order_cnt_30 = '".$db->dt['order_cnt_30']."',
			view_cnt_1 = '".$db->dt['view_cnt_1']."',
			view_cnt_7 = '".$db->dt['view_cnt_7']."',
			view_cnt_10 = '".$db->dt['view_cnt_10']."',
			view_cnt_15 = '".$db->dt['view_cnt_15']."',
			view_cnt_30 = '".$db->dt['view_cnt_30']."',
			wish_cnt_1 = '".$db->dt['wish_cnt_1']."',
			wish_cnt_7 = '".$db->dt['wish_cnt_7']."',
			wish_cnt_10 = '".$db->dt['wish_cnt_10']."',
			wish_cnt_15 = '".$db->dt['wish_cnt_15']."',
			wish_cnt_30 = '".$db->dt['wish_cnt_30']."',
			after_score_1 = '".($db->dt['after_score_1'] + $db->dt['p_after_score_1'])."',
			after_score_7 = '".($db->dt['after_score_7'] + $db->dt['p_after_score_7'])."',
			after_score_10 = '".($db->dt['after_score_10'] + $db->dt['p_after_score_10'])."',
			after_score_15 = '".($db->dt['after_score_15'] + $db->dt['p_after_score_15'])."',
			after_score_30 = '".($db->dt['after_score_30'] + $db->dt['p_after_score_30'])."'
		where
			pid = '".$p['id']."'
	";
	$db->query($sql);
}


exit;
?>