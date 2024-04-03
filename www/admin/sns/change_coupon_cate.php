<?
include($_SERVER["DOCUMENT_ROOT"]."/include/sns.config.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");

$db = new Database;
if($ch_type=="S") {//자신이 구매한 쿠폰 중 배송완료 된 쿠폰만 불러옴 : 팔기
	$sql="SELECT p.* FROM ".TBL_SHOP_PRODUCT." p LEFT JOIN ".TBL_SNS_PRODUCT_RELATION." r ON p.id=r.pid LEFT JOIN ".TBL_SNS_PRODUCT_ETCINFO." e ON p.id=e.pid LEFT JOIN ".TBL_SHOP_ORDER_DETAIL." od ON p.id=od.pid LEFT JOIN ".TBL_SHOP_ORDER." o ON o.oid=od.oid WHERE p.product_type IN (4,5,6) AND r.cid='".$cid."' AND e.spei_eDate >= unix_timestamp(now()) AND od.status='DC' AND o.uid='".$_SESSION["user"]["code"]."' GROUP BY od.pid ";
} else if($ch_type=="B") {//현재 등록되어 있는 쿠폰 중 사용기간이 유효한 쿠폰을 불러옴 : 사기
	$sql="SELECT p.* FROM ".TBL_SHOP_PRODUCT." p LEFT JOIN ".TBL_SNS_PRODUCT_RELATION." r ON p.id=r.pid LEFT JOIN ".TBL_SNS_PRODUCT_ETCINFO." e ON p.id=e.pid WHERE p.product_type IN (4,5,6) AND r.cid='".$cid."' AND p.disp=5 AND e.spei_eDate >= unix_timestamp(now()) ";
} else {//등록되어 있는 모든 쿠폰을 불러옴 : 검색
	$sql="SELECT p.* FROM ".TBL_SHOP_PRODUCT." p LEFT JOIN ".TBL_SNS_PRODUCT_RELATION." r ON p.id=r.pid LEFT JOIN ".TBL_SNS_PRODUCT_ETCINFO." e ON p.id=e.pid WHERE p.product_type IN (4,5,6) AND r.cid='".$cid."' ";
}
//echo $sql;

$db->query($sql);
$fetch=$db->fetchall();
$fetch_cnt=count($fetch);
if($fetch_cnt>0) {
	for($i=0;$i<$fetch_cnt;$i++) {
		if($pid==$fetch[$i]["id"]) {

			$sel_text="selected";
		} else {
			$sel_text="";
		}
		echo "<script type='text/javascript'>
			var frm=eval('top.document.".$element."');
			frm.".$cname.".length=".($fetch_cnt+1).";
			frm.".$cname.".options[".($i+1)."].value='".$fetch[$i]["id"]."';
			frm.".$cname.".options[".($i+1)."].text='".$fetch[$i]["pname"]."';
			frm.".$cname.".options[".($i+1)."].selected='".$sel_text."';


		</script>";
	}
	if($pid=="") {
		echo "<script type='text/javascript'>
			var frm=eval('top.document.".$element."');
			frm.".$cname.".options[0].selected='selected';
		</script>";
	}
} else {
	echo "<script type='text/javascript'>
		var frm=eval('top.document.".$element."');
		frm.".$cname.".length=1;
		frm.".$cname.".options[0].selected='selected';
	</script>";
}
?>