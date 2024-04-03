<?
include("../class/database.class");

set_time_limit(9999999);

//define_syslog_variables();
//openlog("phplog", LOG_PID , LOG_LOCAL0);

//*재고 수불부 크론은 다음날 새벽에 돌려야함*//
/*

CREATE TABLE IF NOT EXISTS `inventory_product_stockinfo_bydate` (
  `psib_ix` int(10) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `vdate` varchar(8) NOT NULL COMMENT '재고기준 일자',
  `gid` varchar(10) DEFAULT NULL COMMENT '상품아이디',
  `unit` int(5) unsigned  DEFAULT NULL COMMENT '상품아이디',
  `gu_ix` int(10) unsigned DEFAULT NULL COMMENT '상품물류단품인덱스',
  `company_id` varchar(32) NOT NULL COMMENT '관리업체',
  `pi_ix` int(6) unsigned NOT NULL COMMENT '보관창고',
  `ps_ix` int(8) unsigned NOT NULL COMMENT '보관장소',
  `basic_stock` int(8) DEFAULT '0' COMMENT '기초재고',
  `input_cnt` int(8) DEFAULT '0' COMMENT '입고수량',
  `input_price` int(8) unsigned DEFAULT '0' COMMENT '입고단가',
  `delivery_cnt` int(8) DEFAULT '0' COMMENT '출고수량',
  `delivery_price` int(8) unsigned DEFAULT '0' COMMENT '출고단가',
  `etc_delivery_cnt` int(8) DEFAULT '0' COMMENT '기타출고수량',
  `etc_delivery_price` int(8) unsigned DEFAULT '0' COMMENT '기타출고단가',
  `stock_cnt` int(8) DEFAULT '0' COMMENT '재고수량',
  `stock_price` int(8) unsigned DEFAULT '0' COMMENT '재고단가',
  `regdate` datetime NOT NULL COMMENT '등록일자',
  PRIMARY KEY (`psib_ix`),
  KEY (`vdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='일자별/상품별 재고 상세정보' AUTO_INCREMENT=1 ;

*/
//syslog(LOG_INFO, "재고수불부 크론 START");

$db = new Database;

$sql="select
		count(gu_ix) as inventory_goods_unit_total
	from
		inventory_goods_unit ";

$db->query($sql);
$db->fetch();

$inventory_goods_unit_total = $db->dt["inventory_goods_unit_total"];

for($limit=0; $limit <= $inventory_goods_unit_total; $limit += 1000){

	$limit_str = "limit ".$limit.", 1000";

	$sql="select
			gid,unit,gu_ix
		from
			inventory_goods_unit $limit_str";

	$db->query($sql);
	$inventory_items= $db->fetchall("object");

	$insert_data=array();
	$insert_data["vdate"]=date("Ymd",strtotime('-1 DAY'));

	foreach($inventory_items as $inventory_item){

		$insert_data["gid"]=$inventory_item["gid"];
		$insert_data["unit"]=$inventory_item["unit"];
		$insert_data["gu_ix"]=$inventory_item["gu_ix"];

		$sql="select
			company_id,pi_ix,ps_ix
		from
			inventory_product_stockinfo
		where
				gid='".$inventory_item["gid"]."'
			and
				unit='".$inventory_item["unit"]."'
		group by
			company_id,pi_ix,ps_ix
		";
		//$sql="select pi_ix from inventory_product_stockinfo where gid='0000000016' and gu_ix='17' group by pi_ix";

		//echo $sql;
		$db->query($sql);


		//현재 창고에 있는 물건들을 셀렉트!
		if($db->total){

			$now_save_warehouses = $db->fetchall("object");

			foreach($now_save_warehouses as $now_save_warehouse){
				
				$insert_data["company_id"]=$now_save_warehouse["company_id"];
				$insert_data["pi_ix"]=$now_save_warehouse["pi_ix"];
				$insert_data["ps_ix"]=$now_save_warehouse["ps_ix"];
				
				//입고
				$sql="select
					sum(amount) as input_cnt, sum((amount * price)/(amount))/count(hd_ix) as input_price
				from
					inventory_history h 
				left join 
					inventory_history_detail hd
				on 
					(h.h_ix = hd.h_ix)
				where
						h.h_div ='1'
					and
						vdate = '".$insert_data["vdate"]."'
					and
						h.company_id='".$inventory_item["company_id"]."'
					and
						h.pi_ix='".$inventory_item["pi_ix"]."'
					and
						h.ps_ix='".$now_save_warehouse["ps_ix"]."'
					and
						hd.gid='".$now_save_warehouse["gid"]."'
					and
						hd.unit='".$now_save_warehouse["unit"]."'
					 ";

				$db->query($sql);
				$db->fetch();

				$insert_data["input_cnt"]=$db->dt["input_cnt"];
				$insert_data["input_price"]=$db->dt["input_price"];
				
				//출고
				$sql="select
					sum(
						case when
							h_type = '01'
						then
							amount
						else
							0
						end
					) as delivery_cnt,

					sum(
						case when
							h_type = '01'
						then
							(amount * price) /(amount)
						else
							0
						end
					)/count(hd_ix) as delivery_price,

					sum(
						case when
							h_type != '01'
						then
							amount
						else
							0
						end
					) as etc_delivery_cnt,

					sum(
						case when
							h_type != '01'
						then
							(amount * price) /(amount)
						else
							0
						end
					)/count(hd_ix) as etc_delivery_price
				from
					inventory_history h 
				left join 
					inventory_history_detail hd
				on 
					(h.h_ix = hd.h_ix)
				where
						h.h_div ='2'
					and
						vdate = '".$insert_data["vdate"]."'
					and
						h.company_id='".$inventory_item["company_id"]."'
					and
						h.pi_ix='".$inventory_item["pi_ix"]."'
					and
						h.ps_ix='".$now_save_warehouse["ps_ix"]."'
					and
						hd.gid='".$now_save_warehouse["gid"]."'
					and
						hd.unit='".$now_save_warehouse["unit"]."'
					 ";
				
				$db->query($sql);
				$db->fetch();

				$insert_data["delivery_cnt"]=$db->dt["delivery_cnt"];
				$insert_data["delivery_price"]=$db->dt["delivery_price"];
				$insert_data["etc_delivery_cnt"]=$db->dt["etc_delivery_cnt"];
				$insert_data["etc_delivery_price"]=$db->dt["etc_delivery_price"];


				$sql="select
					sum(stock) as stock
				from
					 inventory_product_stockinfo
				where
					gid='".$inventory_item["gid"]."'
				and
					unit='".$inventory_item["unit"]."'
				and
					pi_ix='".$now_save_warehouse["pi_ix"]."'
				and
					ps_ix='".$now_save_warehouse["ps_ix"]."'
				";

				$db->query($sql);
				$db->fetch();

				$insert_data["stock_cnt"]=$db->dt["stock"];

				$sql="select
					gu.buying_price as input_price
				from
					inventory_goods_unit gu
				left join
					inventory_goods	g
				on
					(g.gid=gu.gid)
				where
					gu.gid='".$inventory_item["gid"]."'
				and
					gu.gu_ix='".$inventory_item["gu_ix"]."'
				";

				$db->query($sql);
				$db->fetch();

				$insert_data["stock_price"]=$db->dt["input_price"];

				if(isset($insert_data["stock_cnt"])){
					$insert_data["stock_cnt"] =
						$insert_data["stock_cnt"] - TimeErrorCnt($inventory_item["gid"],$inventory_item["unit"],$now_save_warehouse["ps_ix"],date("Ymd"));
				}


				$sql="select
					sum(stock_cnt) as basic_stock
				from
					 inventory_product_stockinfo_bydate
				where
					gid='".$inventory_item["gid"]."'
				and
					gu_ix='".$inventory_item["gu_ix"]."'
				and
					ps_ix='".$now_save_warehouse["ps_ix"]."'
				and
					vdate='".($insert_data["vdate"]-1)."'
				";

				$db->query($sql);
				$db->fetch();

				$insert_data["basic_stock"]=$db->dt["basic_stock"];

				if(empty($insert_data["basic_stock"])){
					$insert_data["basic_stock"] =
						$insert_data["stock_cnt"] - TimeErrorCnt($inventory_item["gid"],$inventory_item["unit"],$now_save_warehouse["ps_ix"],(date("Ymd")-1));
				}

				if(empty($insert_data["basic_stock"]))				$insert_data["basic_stock"]=0;
				if(empty($insert_data["input_cnt"]))				$insert_data["input_cnt"]=0;
				if(empty($insert_data["input_price"]))				$insert_data["input_price"]=0;
				if(empty($insert_data["delivery_cnt"]))				$insert_data["delivery_cnt"]=0;
				if(empty($insert_data["delivery_price"]))			$insert_data["delivery_price"]=0;
				if(empty($insert_data["etc_delivery_cnt"]))			$insert_data["etc_delivery_cnt"]=0;
				if(empty($insert_data["etc_delivery_price"]))		$insert_data["etc_delivery_price"]=0;
				if(empty($insert_data["stock_cnt"]))				$insert_data["stock_cnt"]=0;
				if(empty($insert_data["stock_price"]))				$insert_data["stock_price"]=0;

				$vdate=$insert_data["vdate"];
				$gid=$insert_data["gid"];
				$unit=$insert_data["unit"];
				$gu_ix=$insert_data["gu_ix"];
				$company_id=$insert_data["company_id"];
				$pi_ix=$insert_data["pi_ix"];
				$ps_ix=$insert_data["ps_ix"];
				$basic_stock=$insert_data["basic_stock"];
				$input_cnt=$insert_data["input_cnt"];
				$input_price=$insert_data["input_price"];
				$delivery_cnt=$insert_data["delivery_cnt"];
				$delivery_price=$insert_data["delivery_price"];
				$etc_delivery_cnt=$insert_data["etc_delivery_cnt"];
				$etc_delivery_price=$insert_data["etc_delivery_price"];
				$stock_cnt=$insert_data["stock_cnt"];
				$stock_price=$insert_data["stock_price"];

				$sql="select
					psib_ix
				from
					 inventory_product_stockinfo_bydate
				where
					gid='".$gid."'
				and
					gu_ix='".$gu_ix."'
				and
					ps_ix='".$ps_ix."'
				and
					vdate='".$vdate."'
				";

				$db->query($sql);

				if(!$db->total){
					$sql="insert into inventory_product_stockinfo_bydate(psib_ix,vdate,gid,unit,gu_ix,company_id,pi_ix,ps_ix,basic_stock,input_cnt,input_price,delivery_cnt,delivery_price,etc_delivery_cnt,etc_delivery_price,stock_cnt,stock_price,regdate) values('','$vdate','$gid','$unit','$gu_ix','$company_id','$pi_ix','$ps_ix','$basic_stock','$input_cnt','$input_price','$delivery_cnt','$delivery_price','$etc_delivery_cnt','$etc_delivery_price','$stock_cnt','$stock_price',NOW()) ";
					$db->sequences = "INVENTORY_GOODS_INFO_DATE_SEQ";
					$db->query($sql);
				}else{
					$db->fetch();

					$psib_ix=$db->dt["psib_ix"];

					$sql="update inventory_product_stockinfo_bydate set
					company_id='".$company_id."',
					basic_stock='".$basic_stock."',
					input_cnt='".$input_cnt."',
					input_price='".$input_price."',
					delivery_cnt='".$delivery_cnt."',
					delivery_price='".$delivery_price."',
					etc_delivery_cnt='".$etc_delivery_cnt."',
					etc_delivery_price='".$etc_delivery_price."',
					stock_cnt='".$stock_cnt."',
					stock_price='".$stock_price."'
					where psib_ix='".$psib_ix."'
					";

					$db->query($sql);
				}
			}
		}
	}
}
//syslog(LOG_INFO, "재고수불부 크론 END");
//closelog();

function TimeErrorCnt($gid,$unit,$ps_ix,$standard_date){
	global $db;
	
	$sql="select
		sum(amount) as input_cnt
	from
		inventory_history h 
	left join 
		inventory_history_detail hd
	on 
		(h.h_ix = hd.h_ix)
	where
			h.h_div ='1'
		and
			vdate = '".$standard_date."'
		and
			h.ps_ix='".$ps_ix."'
		and
			hd.gid='".$gid."'
		and
			hd.unit='".$unit."' ";

	$db->query($sql);
	$db->fetch();

	$error_input_cnt = $db->dt["input_cnt"];

	$sql="select
		sum(amount) as delivery_cnt
	from
		inventory_history h 
	left join 
		inventory_history_detail hd
	on 
		(h.h_ix = hd.h_ix)
	where
			h.h_div ='2'
		and
			vdate = '".$standard_date."'
		and
			h.ps_ix='".$ps_ix."'
		and
			hd.gid='".$gid."'
		and
			hd.unit='".$unit."' ";

	$db->query($sql);
	$db->fetch();

	$error_delivery_cnt = $db->dt["delivery_cnt"];

	return ($error_input_cnt - $error_delivery_cnt);
}


?>
