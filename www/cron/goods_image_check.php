<?
include_once("../class/layout.class");

$db = new Database;



$sql = "select * from shop_product_image_check where is_square not in (1) and is_update = 0   limit 1000 ";		 
$db->query($sql);
 
$product_info = $db->fetchall("object");
for($i=0;$i < count($product_info);$i++){
	$sql = "select pname from shop_product where id = '".$product_info[$i][pid]."'   ";		 
	$db->query($sql);
	$db->fetch();
	$pname = $db->dt[pname];

	$sql = "select com_name from common_company_detail where company_id = '".$product_info[$i][company_id]."'  ";		 
	$db->query($sql);
	$db->fetch();
	$com_name = $db->dt[com_name];

	$sql = "update shop_product_image_check set 
				pname='".$pname."' ,com_name='".$com_name."' , is_update = 1
				where pid = '".$product_info[$i][pid]."' ";
	//echo nl2br($sql)."<br>";
	$db->query($sql);
	
}
//exit;
if(false){

		if($page == ""){
			$page = 1;
		}
		$sql = "select * from shop_product where etc9 is null order by regdate desc limit 1000 ";		 
		$db->query($sql);
		 
		$product_info = $db->fetchall("object");


		for($i=0;$i < count($product_info);$i++){
			
			$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"]."/data/daiso_data/images/product", $product_info[$i][id], 'Y');
			$basic_img_src = $_SERVER["DOCUMENT_ROOT"]."/data/daiso_data/images/product".$uploaddir."/b_".$product_info[$i][id].".gif";
			$company_id = $product_info[$i][admin];
			$pid = $product_info[$i][id];

			$image_info = @getimagesize ($basic_img_src);
			if($image_info){
				$image_type = substr($image_info['mime'],-3);
				$image_width = $image_info[0];
				$image_height = $image_info[1];

				if($image_width == $image_height){
					$is_square = 1; // 정사각형
				}else{
					$is_square = 2; // 직사각형
				}
				$image_info_str = $image_width."*".$image_height;
				
				//print_r($image_info);
				
			}else{
				$is_square = 9; // 이미지 없음
			}
			$sql = "update shop_product set etc9='".$is_square."' where id = '".$product_info[$i][id]."' ";
			//echo $sql."<br>";
			$db->query($sql);

			$sql = "insert into shop_product_image_check(pic_ix,pid,is_square,image_info,company_id) values('','$pid','$is_square','$image_info_str','$company_id')";
			$db->query($sql);
			//echo $sql."<br>";


		}
		//exit;
		$page++;
		if($page > 100){
			echo "완료";
			exit;
		}
}
sleep(3);
header("Location:/cron/goods_image_check.php?page=$page");

exit;
for($i=0;$i < count($product_info);$i++){
	echo "<img src='http://wiseweb.negagea.kr/s_detail/da/".$product_info[pcode]."/large_1.jpg'>"; 
}
/*
$sql = "select cmd.code, (select sum(total_price) from shop_order where status = 'IC' and uid = cmd.code)  as order_total_price from common_member_detail cmd where recent_order_date <> '' and recent_order_date is not null having order_total_price > 0  ";
$db->query($sql);
$member_orderinfos =$db->fetchall();

foreach($member_orderinfos as $member_orderinfo){
	$sql = "update common_member_detail set order_total_price = '".$member_orderinfo[order_total_price]."' where code = '".$member_orderinfo[code]."' ";
	echo $sql."<br>";
	$db->query($sql);
	//echo"<tr><td><a href='test.php?id=".$product[id]."'>".$product[id]."<br/>".$product[pname]."</a></td></tr>";
}

exit;
*/

exit;
$sql="
select
	*
from
	shop_product
where
	id in
		(
		0000124811,
0000124847,
0000124870,
0000124905,
0000124727,
0000124632,
0000124633,
0000124427,
0000124419,
0000124417,
0000124255,
0000124237,
0000123829,
0000123800,
0000124545,
0000115672,
0000115730,
0000115729,
0000123654,
0000123709,
0000123714,
0000123767,
0000123942,
0000124007,
0000124350,
0000124502,
0000124509,
0000124602,
0000124819,
0000111737,
0000113711,
0000115298,
0000115562,
0000115590,
0000112086,
0000115598,
0000115802,
0000116059,
0000116045,
0000116070,
0000123371,
0000123413,
0000123445,
0000123695,
0000123785,
0000124260,
0000124589,
0000124767,
0000107772,
0000108350,
0000111997,
0000112261,
0000113564,
0000113738,
0000114980,
0000115474,
0000115574,
0000124195,
0000124193,
0000124197,
0000124202,
0000124230,
0000124231,
0000124368,
0000124353,
0000124570,
0000122822,
0000123223,
0000123229,
0000123255,
0000123298,
0000123805,
0000123890,
0000123886,
0000123970,
0000113649,
0000113650,
0000114913,
0000114991,
0000115049,
0000115096,
0000115112,
0000115374,
0000115543,
0000115655,
0000115667,
0000115677,
0000115717,
0000115719,
0000115829,
0000115796,
0000115828,
0000115871,
0000113648,
0000113647,
0000113473,
0000115982,
0000123676,
0000115981,
0000116063,
0000122818,
0000123238,
0000123237,
0000123271,
0000123928,
0000124048,
0000124246,
0000116034,
0000124431,
0000124512
		)
";
$db->query($sql);
$product_info=$db->fetchall();


echo "
<table width=500 style='float:left;'>";
	foreach($product_info as $product){
		echo"<tr><td><a href='test.php?id=".$product[id]."'>".$product[id]."<br/>".$product[pname]."</a></td></tr>";
	}
echo "
</table>
";


if($id){

	$sql="
	select
		*
	from
		shop_product
	where
		id =".$id."
	";
	$db->query($sql);

	$db->fetch();

	echo "<div style='float:left;'>상품 아이디 : ".$db->dt[id]." 상품이름 : ".$db->dt[pname]."<br/>판매가 : ".$db->dt[sellprice]." 도매가 : ".$db->dt[wholesale_sellprice]."<br/>".$db->dt[basicinfo]."</div>";
}


?>