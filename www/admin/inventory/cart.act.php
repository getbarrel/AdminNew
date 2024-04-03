<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");


$db = new Database;
//print_r($_GET);
if($act == "countadd"){

	$sql = "update inventory_order_detail_tmp set order_cnt = '".$order_cnt."', order_coprice = '".$order_coprice."'  where iodt_ix = '".$iodt_ix."' ";
	//echo $sql;
	$db->query($sql);

	echo "<script>parent.document.location.reload();</script>";
}

if ($act == "select_order"){

		//print_r($_POST);
		//exit;
		if($order_list_type == "P"){
			$sql = "update inventory_order_detail_tmp set order_yn = 'N'  where company_id = '".$_SESSION["admininfo"]["company_id"]."' ";
		}else{
			$sql = "update inventory_order_detail_tmp set order_yn = 'N'  where charger_ix = '".$_SESSION["admininfo"]["charger_ix"]."' ";
		}
		$db->query($sql);

		for($i=0;$i<count($iodt_ix);$i++){
				$sql = "update inventory_order_detail_tmp set order_yn = 'Y' where  iodt_ix = '".$iodt_ix[$i]."'  ";
				//echo $sql."<br><br>";
				$db->query($sql);
		}

		echo("<script>parent.document.location.href='infoinput.php?order_list_type=$order_list_type&ci_ix=$ci_ix';</script>");
}

if ($act == "insert")
{


	$ioid = date("YmdHis")."-".rand(10000, 99999);


	if($order_list_type == "P"){
		/*
		$sql = "select odt.*,  listprice, coprice, sellprice , r.cid, ici.customer_name, pod.option_price, pod.option_coprice
					from  inventory_order_detail_tmp odt left join shop_product_options_detail pod on odt.pid = pod.pid and odt.opn_ix = pod.opn_ix and odt.opnd_ix = pod.id ,
					shop_product sp left join ".TBL_SHOP_PRODUCT_RELATION." r on sp.id = r.pid and r.basic = '1',
					inventory_customer_info ici
					where ici.ci_ix = odt.ci_ix
					and odt.pid = sp.id and charger_ix = '".$_SESSION["admininfo"]["charger_ix"]."' and odt.order_yn = 'Y'
					order by odt.regdate asc , pid , opnd_ix";
		*/
		$sql = "select odt.*
					from  inventory_order_detail_tmp odt left join inventory_goods_item gi on odt.gid = gi.gid and odt.gi_ix = gi.gi_ix ,
					inventory_goods g ,
					inventory_customer_info ici
					where ici.ci_ix = odt.ci_ix
					and odt.gid = g.gid and charger_ix = '".$_SESSION["admininfo"]["charger_ix"]."' and odt.order_yn = 'Y'
					order by odt.regdate asc , odt.gid , odt.gi_ix";
	}else{
		/*
		$sql = "select odt.*,  listprice, coprice, sellprice , r.cid, ici.customer_name, pod.option_price, pod.option_coprice
					from  inventory_order_detail_tmp odt left join shop_product_options_detail pod on odt.pid = pod.pid and odt.opn_ix = pod.opn_ix and odt.opnd_ix = pod.id ,
					shop_product sp left join ".TBL_SHOP_PRODUCT_RELATION." r on sp.id = r.pid and r.basic = '1',
					inventory_customer_info ici
					where ici.ci_ix = odt.ci_ix
					and odt.pid = sp.id and company_id = '".$_SESSION["admininfo"]["company_id"]."' and odt.order_yn = 'Y'
					order by odt.regdate asc , pid , opnd_ix";
		*/

		$sql = "select odt.*
					from  inventory_order_detail_tmp odt left join inventory_goods_item gi on odt.gid = gi.gid and odt.gi_ix = gi.gi_ix ,
					inventory_goods g ,
					inventory_customer_info ici
					where ici.ci_ix = odt.ci_ix
					and odt.gid = g.gid and odt.company_id = '".$_SESSION["admininfo"]["company_id"]."' and odt.order_yn = 'Y'
					order by odt.regdate asc , odt.gid , odt.gi_ix";
	}


	//echo nl2br($sql)."<br><br>";
	//exit;
	$db->query($sql);

	if($db->total){
		$order_tmp = $db->fetchall();
		//$db->query("begin");
		for($i=0;$i<count($order_tmp);$i++){
			//$db->fetch($i);

				$iodt_ix = $order_tmp[$i][iodt_ix];
				$order_charger_ix = $order_tmp[$i][charger_ix];
				$ci_ix = $order_tmp[$i][ci_ix];
				$pi_ix = $order_tmp[$i][pi_ix];
				$gid = $order_tmp[$i][gid];
				//$opn_ix = $order_tmp[$i][opn_ix];
				$gi_ix = $order_tmp[$i][gi_ix];
				$gname = $order_tmp[$i][gname];
				$item_name = $order_tmp[$i][item_name];
				$order_cnt    = $order_tmp[$i][order_cnt];
				//$options    = $order_tmp[$i][options];
				$option_serial    = $order_tmp[$i][option_serial];
				$order_coprice = $order_tmp[$i][order_coprice];
				/*
				if($order_tmp[$i][option_price] > 0){
					$sellprice = $order_tmp[$i][option_coprice];
				}else{
					$sellprice = $order_tmp[$i][coprice];
				}
				$listprice = $order_tmp[$i][listprice];
				*/
				/*
				if($order_tmp[$i][option_price] > 0){
					$sellprice = $order_tmp[$i][option_price];
				}else{
					$sellprice =$order_tmp[$i][sellprice];
				}
				*/

				/*
				//order_totalprice 값을 받아서 넣음
				$totalprice = $order_cnt*$order_coprice;
				$order_totalprice = $order_totalprice + $totalprice;
				*/

				//$coper = $order_coprice / $sellprice * 100;



				$sql = "insert into inventory_order_detail
							(iod_ix,ioid, ci_ix, pi_ix, gid, gi_ix, gname, item_name, order_cnt,order_coprice,incom_cnt,sellprice,coprice,order_charger_ix, regdate) values('','".$ioid."','".$ci_ix."','".$pi_ix."','".$gid."','".$gi_ix."','".$gname."','".$item_name."','".$order_cnt."','".$order_coprice."',0,'".$sellprice."','".$coprice."','".$order_charger_ix."',NOW()) ";
				//echo $sql."<br><br>";
				//exit;
				$db->sequences = "INVENTORY_ORDER_DT_SEQ";
				$db->query($sql);

				$sql = "delete from inventory_order_detail_tmp where  iodt_ix = '".$iodt_ix."' ";
				$db->query($sql);
		}

		$sql = "insert into inventory_order
				(ioid,order_charger,limit_priod_s,limit_priod_e,ci_ix,incom_company_charger,total_price,total_add_price,pttotal_price,
				b_delivery_price,a_delivery_price,b_tax,a_tax,b_commission,a_commission,
				status,etc,charger_ix, al_ix, regdate) values
				('$ioid','".$admininfo[charger]."','$limit_priod_s','$limit_priod_e','$ci_ix','$incom_company_charger','".$order_totalprice."','".($order_totalprice*0.1)."','".$order_pttotalprice."',
				'$b_delivery_price','$a_delivery_price','$b_tax','$a_tax','$b_commission','$a_commission',
				'AR','$etc','".$_SESSION["admininfo"]["charger_ix"]."','$al_ix',NOW())";

		//echo $sql."<br><br>";
		//exit;
		//$db->sequences = "INVENTORY_ORDER_SEQ";
		$db->query($sql);

		$sql = "select aldt_ix from common_authline_detail_info where al_ix = '".$al_ix."' ";
		$db->query($sql);
		$authline_detail_info = $db->fetchall();

		for($i=0;$i<count($authline_detail_info);$i++){
			$db->sequences = "COMMON_AUTHLINE_APPROVE_SEQ";
			$db->query("insert into common_authline_approve(ala_ix,ioid,aldt_ix,approve_yn) values('','".$ioid."','".$authline_detail_info[$i][aldt_ix]."','N')");
		}

	}
//exit;
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('발주가 정상적으로 처리되었습니다.');</script>");
	echo("<script>parent.document.location.href='order_list.php';</script>");
}


if($act == "delete"){

	$sql = "delete from inventory_order_detail_tmp where iodt_ix = '".$iodt_ix."' ";
			//echo $sql."<br><br>";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('발주정보가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}
/*


CREATE TABLE IF NOT EXISTS `inventory_order_detail` (
  iod_ix int(10) default NULL auto_increment COMMENT '인덱스',
  pid int(10) unsigned zerofill default NULL COMMENT '상품아이디',
  order_cnt int(8) default NULL COMMENT '발주수량',
  incom_cnt int(8) default NULL COMMENT '입고수량',
  sellprice int(10) default NULL COMMENT '단가',
  coprice int(10) default NULL COMMENT '공급가',
  regdate datetime NOT NULL COMMENT '등록일자'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='발주내역 상세정보';



order_charger varchar(20) default NULL COMMENT '발주자',
`input_inventory` int(6) default NULL COMMENT '보관장소키',
  `input_size` int(10) default NULL COMMENT '입고수량',

*/
?>