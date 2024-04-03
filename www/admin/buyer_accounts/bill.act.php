<?
include("../class/layout.class");
include_once("../lib/barobill.lib.php");

$db = new Database;
$udb = new Database;

if($act == "preiod_ready"){

	for($i=0;$i<count($oid);$i++){

		$db->query("update shop_order set tax_period_apply_date = NOW() where oid = '".$oid[$i]."' ");
	}
	echo "<script>alert('처리되었습니다.');top.location.reload();</script>";
	exit;
}

if($act == "preiod_bill"){
	$db->query("SELECT com_name, com_number, com_business_status, com_ceo, com_business_category, com_addr1, com_addr2, tax_person_name, tax_person_mail FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE com_type = 'A' ");
	$buyer = $db->fetch();
	
	$s_cnt=0;
	$f_cnt=0;
	$t_cnt=count($uid);

	for($i=0;$i<count($uid);$i++){

		$sql = "SELECT o.*,ccd.com_name,ccd.com_number,ccd.com_business_status, ccd.com_ceo, ccd.com_business_category,ccd.com_addr1, ccd.com_addr2,ccd.tax_person_name,ccd.tax_person_mail from 
			(
				select o.uid as code ,od.pname, count(od.pcnt) as pcnt , o.tax_period_apply_date as apply_date
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od WHERE o.oid=od.oid AND  od.status <> '' AND o.uid ='".$uid[$i]."' AND  o.tax_period_apply_date ='".$tax_period_apply_date[$i]."' 
			) o left join common_user cu using(code) left join common_member_detail cmd using(code) left join common_company_detail ccd  on (cu.company_id=ccd.company_id)
			 ";
		$db->query($sql);
		$seller = $db->fetch();

		$expect[supply_price]=$expect_coprice[$uid[$i]][$tax_period_apply_date[$i]];
		$expect[tax_price]=$expect_tax[$uid[$i]][$tax_period_apply_date[$i]];
		$expect[total_price]=$expect_total[$uid[$i]][$tax_period_apply_date[$i]];

		$real[supply_price]=$coprice[$uid[$i]][$tax_period_apply_date[$i]];
		$real[tax_price]=$tax[$uid[$i]][$tax_period_apply_date[$i]];
		$real[total_price]=$total[$uid[$i]][$tax_period_apply_date[$i]];

		$product[mon]=date("m");
		$product[day]=date("d");
		$product[product]="상품구매 : ".($seller[pcnt] > 1 ? $seller[pname]." 외 ".number_format($seller[pcnt]-1) : $seller[pname]);
		
		//insert_tax_sales -> ../lib/barobill.lib.php 
		//publish_type 1,매출 2,매입 3,위수탁
		//tax_div 구분 1:구매자,2:판매자
		//tax_type 1.세금계산서 2.계산서
		//tax_per 1:과세 2:면세
		$tax_info[publish_type]="1";
		$tax_info[tax_div]="1";
		$tax_info[tax_type]="2";
		$tax_info[tax_per]="2";
		$p_idx = insert_tax_sales($tax_info,$buyer,$seller,$expect,$real,$product);
		
		if($p_idx){
			if(barobill_input($p_idx)){
				$s_cnt++;
				$db->query("update shop_order set tax_affairs_yn = 'Y' , bill_ix ='".$p_idx."' where uid ='".$uid[$i]."' AND  tax_period_apply_date ='".$tax_period_apply_date[$i]."' ");
			}else{
				$f_cnt++;
				$db->query("delete from tax_sales where idx='".$p_idx."' ");
				$db->query("delete from tax_sales_detail where idx='".$p_idx."' ");
			}
		}else{
			$f_cnt++;
		}
	}

	echo "<script>alert('총: ".$t_cnt." 건중 성공: ".$s_cnt." 실패: ".$f_cnt." 건이 처리되었습니다.');top.location.reload();</script>";
	exit;
}

if($act == "order_bill"){

	$db->query("SELECT com_name, com_number, com_business_status, com_ceo, com_business_category, com_addr1, com_addr2, tax_person_name, tax_person_mail FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE com_type = 'A' ");
	$buyer = $db->fetch();

	$s_cnt=0;
	$f_cnt=0;
	$t_cnt=count($oid);

	for($i=0;$i<count($oid);$i++){

		$sql = "SELECT o.*,ccd.com_name,ccd.com_number,ccd.com_business_status, ccd.com_ceo, ccd.com_business_category,ccd.com_addr1, ccd.com_addr2,ccd.tax_person_name,ccd.tax_person_mail FROM
			(
				SELECT  o.uid as code ,od.pname, count(od.pcnt) as pcnt 
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				WHERE o.oid=od.oid AND od.status <> '' AND o.oid ='".$oid[$i]."'
			) o  left join common_user cu using(code) left join common_member_detail cmd using(code) left join common_company_detail ccd  on (cu.company_id=ccd.company_id)";
		$db->query($sql);
		$seller = $db->fetch();

		$expect[supply_price]=$expect_coprice[$oid[$i]];
		$expect[tax_price]=$expect_tax[$oid[$i]];
		$expect[total_price]=$expect_total[$oid[$i]];

		$real[supply_price]=$coprice[$oid[$i]];
		$real[tax_price]=$tax[$oid[$i]];
		$real[total_price]=$total[$oid[$i]];

		$product[mon]=date("m");
		$product[day]=date("d");
		$product[product]="상품구매 : ".($seller[pcnt] > 1 ? $seller[pname]." 외 ".number_format($seller[pcnt]-1) : $seller[pname]);
		
		//insert_tax_sales -> ../lib/barobill.lib.php 
		//publish_type 1,매출 2,매입 3,위수탁
		//tax_div 구분 1:구매자,2:판매자
		//tax_type 1.세금계산서 2.계산서
		//tax_per 1:과세 2:면세
		$tax_info[publish_type]="1";
		$tax_info[tax_div]="1";
		$tax_info[tax_type]="2";
		$tax_info[tax_per]="2";
		$p_idx = insert_tax_sales($tax_info,$buyer,$seller,$expect,$real,$product);
	
		if($p_idx){
			if(barobill_input($p_idx)){
				$s_cnt++;
				$db->query("update shop_order set tax_affairs_yn = 'Y', bill_ix='".$p_idx."' where oid = '".$oid[$i]."' ");
			}else{
				$f_cnt++;
				$db->query("delete from tax_sales where idx='".$p_idx."' ");
				$db->query("delete from tax_sales_detail where idx='".$p_idx."' ");
			}
		}else{
			$f_cnt++;
		}
	}

	echo "<script>alert('총: ".$t_cnt." 건중 성공: ".$s_cnt." 실패: ".$f_cnt." 건이 처리되었습니다.');top.location.reload();</script>";
	exit;
}

if($act == "again_bill"){
	if(barobill_input($idx)){
		echo "<script>alert('처리되었습니다.');top.location.reload();</script>";
		exit;
	}else{
		exit;
	}
}

?>