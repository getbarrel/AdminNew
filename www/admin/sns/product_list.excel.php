<?

///////////////// CREATE EXCEL FILE METHOD /////////////////
function xlsBOF() {
echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
return;
}
function xlsEOF() {
echo pack("ss", 0x0A, 0x00);
return;
}
function xlsWriteNumber($Row, $Col, $Value) {
echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
echo pack("d", $Value);
return;
}
function xlsWriteLabel($Row, $Col, $Value, $lang='' ) {
$lang = ($lang)? $lang:'euc-kr';
$Value = mb_convert_encoding($Value,$lang,"utf-8");
$L = strlen($Value);
echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
echo $Value;
return;
}
///////////////////// END //////////////////////////
include("../class/layout.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');

header('Pragma: public');
header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
header('Content-Type: text/plain; charset=UTF-8');
header('Content-Transfer-Encoding: binary');
header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera
header("Content-type: application/x-msexcel");                    // This should work for the rest
header('Content-Disposition: attachment; filename='.iconv("utf-8","CP949","상품리스트").'_'.date("Ymd").'.xls');

$db = new Database;
if($orderby != "" && $ordertype != ""){
	$orderbyString = " group by p.id   order by $orderby $ordertype ";
}else{
	$orderbyString = " group by p.id   order by r.regdate desc ";
}

if($mode == "search"){
	switch ($depth){
		case 0:
			$cut_num = 3;
			break;
		case 1:
			$cut_num = 6;
			break;
		case 2:
			$cut_num = 9;
			break;
		case 3:
			$cut_num = 12;
			break;
		case 4:
			$cut_num = 15;
			break;
	}
	$where = "";
	if($search_text != ""){
		$where .= "and p.".$search_type." LIKE '%".trim($search_text)."%' ";
	}

	if($sprice && $eprice){
		$where .= "and sellprice between $sprice and $eprice ";
	}

	if($status_where){
		$where .= " and ($status_where) ";
	}
	if($brand2 != ""){
		$where .= " and brand = ".$brand2."";
	}

	if($brand_name != ""){
		$where .= " and brand_name LIKE '%".trim($brand_name)."%' ";
	}

	if($disp != ""){
		$where .= " and p.disp = ".$disp;
	}

	if($one_commission){
		$where .= " and p.one_commission = '".$one_commission."'";
	}



	if($state2 != ""){
		$where .= " and state = ".$state2."";
	}


	if($cid2 != ""){
		$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
	}else{
		$where .= "";
	}
	if($admininfo[admin_level] == 9){
		if($company_id != ""){
			$addWhere = "and admin ='".$company_id."'";
		}else{
			unset($addWhere);
		}
		$sql = "SELECT distinct p.id, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid, p.stock,  p.search_keyword,state, p.brand_name, csd.delivery_price,  p.basicinfo,
		p.company, p.pcode, p.coprice, p.listprice,  p.disp, p.editdate, p.reserve, p.reserve_rate,  case when p.one_commission = 'Y' then p.commission else csd.commission end as commission , p.etc2, p.one_commission, p.commission as goods_commission , csd.commission as company_commission,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp
		FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c,  ".TBL_COMMON_SELLER_DELIVERY." csd
		where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid  $addWhere $where $orderbyString $limit_string";
		//echo $sql;
		$db->query($sql);
	}else{
		$sql = "SELECT distinct p.id, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid, p.stock,  p.search_keyword,state, p.brand_name, csd.delivery_price,  p.basicinfo,
		p.company, p.pcode, p.coprice, p.listprice,p.disp, p.editdate, p.reserve, p.reserve_rate, case when p.one_commission = 'Y' then p.commission else csd.commission end as commission, p.etc2, p.one_commission, p.commission as goods_commission , csd.commission as company_commission,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2
		FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c,  ".TBL_COMMON_SELLER_DELIVERY." csd
		where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid  and admin ='".$admininfo[company_id]."' $where $orderbyString $limit_string";


		$db->query($sql);
	}
	//echo $sql;
}else{

	if ($cid2 == ""){
		if($admininfo[admin_level] == 9){
			if($company_id != ""){
				$addWhere = "and admin ='".$company_id."'";
			}else{
				unset($addWhere);
			}
			//$tmp_sql = "create temporary table ".TBL_LOGSTORY_BYPAGE."_tmp ENGINE = MEMORY select vdate, pageid, ncnt, nduration from ".TBL_SHOP_PRODUCT_RELATION." where vdate = '$vdate' ";

			$sql = "SELECT distinct (p.id) as id, p.pname, p.brand,p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid, p.stock,  p.search_keyword,state, p.brand_name, c.delivery_price,  p.basicinfo,
			p.company, p.pcode, p.coprice, p.listprice,  p.disp, p.editdate, p.reserve, p.reserve_rate,case when p.one_commission = 'Y' then p.commission else c.commission end as commission, p.etc2,p.one_commission, p.commission as goods_commission , c.commission as company_commission,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp
			FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c,  ".TBL_COMMON_SELLER_DELIVERY." csd
			where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid $where $addWhere $orderbyString $limit_string";
			//echo $sql;
			$db->query($sql);
		}else{
			$sql = "SELECT distinct (p.id) as id ,p.brand, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid, p.stock,  p.search_keyword,state, p.brand_name,  c.delivery_price,  p.basicinfo,
			p.company, p.pcode, p.coprice, p.listprice,  p.disp, p.editdate, p.reserve, p.reserve_rate, case when p.one_commission = 'Y' then p.commission else c.commission end as commission, p.etc2,p.one_commission, p.commission as goods_commission , c.commission as company_commission,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2
			FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c,  ".TBL_COMMON_SELLER_DELIVERY." csd
			where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid and admin ='".$admininfo[company_id]."' $where $orderbyString $limit_string";


			//echo $sql;
			$db->query($sql);
		}

		//echo $sql;
	}else{
		switch ($depth){
			case 0:
				$cut_num = 3;
				break;
			case 1:
				$cut_num = 6;
				break;
			case 2:
				$cut_num = 9;
				break;
			case 3:
				$cut_num = 12;
				break;
			case 4:
				$cut_num = 15;
				break;
		}

		if($admininfo[admin_level] == 9){
			$sql = "SELECT distinct (p.id) as id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder, r.cid, p.stock, p.search_keyword,state, p.brand, b.brand_name,  p.size, p.natural_item,  c.delivery_price,  p.delivery_type, p.basicinfo,
				p.company, p.pcode, p.coprice, p.listprice, p.disp, p.editdate,  p.reserve_rate,  case when p.one_commission = 'Y' then p.commission else c.commission end as commission, p.etc2, p.one_commission, p.commission as goods_commission , c.commission as company_commission,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp
				FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c, ".TBL_SNS_BRAND." b,  ".TBL_COMMON_SELLER_DELIVERY." csd
				where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid and p.brand = b.b_ix and r.cid = '".$cid2."' $where $orderbyString $limit_string";

			//echo $sql;

			$db->query($sql);
		}else{
			$sql = "SELECT distinct (p.id) as id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder, r.cid, p.stock, p.search_keyword,state, p.brand, b.brand_name,  p.size, p.natural_item,  c.delivery_price, p.delivery_type, p.basicinfo,
				p.company, p.pcode, p.coprice, p.listprice,   p.disp, p.editdate,  p.reserve_rate, case when p.one_commission = 'Y' then p.commission else c.commission end as commission, p.etc2, p.one_commission, p.commission as goods_commission , c.commission as company_commission,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2
				FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c, ".TBL_SNS_BRAND." b,  ".TBL_COMMON_SELLER_DELIVERY." csd
				where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid and p.brand = b.b_ix and r.cid = '".$cid2."' and admin ='".$admininfo[company_id]."' $where $orderbyString $limit_string";

				//echo $sql;
				$db->query($sql);

				//echo "test".$db->total;

		}
	}
}
xlsBOF();


xlsWriteLabel(0,0,"상품명");
xlsWriteLabel(0,1,"상품코드");
xlsWriteLabel(0,2,"업체명");
xlsWriteLabel(0,3,"원산지");
xlsWriteLabel(0,4,"브랜드");
xlsWriteLabel(0,5,"재고량");
xlsWriteLabel(0,6,"유사검색어");
xlsWriteLabel(0,7,"옵션1");
xlsWriteLabel(0,8,"공급가");
xlsWriteLabel(0,9,"정가");
xlsWriteLabel(0,10,"판매가(할인가)");
xlsWriteLabel(0,11,"수수료");
xlsWriteLabel(0,12,"배송선택");
xlsWriteLabel(0,13,"배송료");
xlsWriteLabel(0,14,"기본이미지");
xlsWriteLabel(0,15,"상세정보");
//------ row 1 data ------
for($i=0;$i<$db->total;$i++){
	$db->fetch($i);
	xlsWriteLabel(($i+1), 0, $db->dt[pname]);
	xlsWriteLabel(($i+1), 1, $db->dt[pcode]);
	xlsWriteLabel(($i+1), 2, $db->dt[com_name]);
	xlsWriteLabel(($i+1), 3, $db->dt[company]);
	xlsWriteLabel(($i+1), 4, $db->dt[brand]);
	xlsWriteNumber(($i+1), 5, $db->dt[stock]);
	xlsWriteLabel(($i+1), 6, $db->dt[search_keyword]);
	xlsWriteLabel(($i+1), 7, $db->dt[option_text]);
	xlsWriteNumber(($i+1), 8, $db->dt[coprice]);
	xlsWriteNumber(($i+1), 9, $db->dt[listprice]);
	xlsWriteNumber(($i+1), 10, $db->dt[sellprice]);
	xlsWriteLabel(($i+1), 11, $db->dt[one_commission]);
	xlsWriteLabel(($i+1), 12, $db->dt[delivery_type]);
	xlsWriteNumber(($i+1), 13, $db->dt[delivery_price]);
	xlsWriteLabel(($i+1), 14, "http://".$HTTP_HOST.$admin_config[mall_data_root]."/images/product/b_".$db->dt[id].".gif");
	xlsWriteLabel(($i+1), 15, $db->dt[basicinfo]);
}


xlsEOF();

?>
<?
include("../class/layout.class");

header( "Content-type: application/vnd.ms-excel" );
header( "Content-Disposition: attachment; filename=".iconv("utf-8","CP949","상품리스트")."_".date("Ymd").".csv" );
header( "Content-Description: PHP5 Generated Data" );
header("Content-charset=euc-kr");
$db = new Database;
//	$limit_string = "LIMIT $start, $max";


if($orderby != "" && $ordertype != ""){
	$orderbyString = " group by p.id   order by $orderby $ordertype ";
}else{
	$orderbyString = " group by p.id   order by r.regdate desc ";
}

if($mode == "search"){
	switch ($depth){
		case 0:
			$cut_num = 3;
			break;
		case 1:
			$cut_num = 6;
			break;
		case 2:
			$cut_num = 9;
			break;
		case 3:
			$cut_num = 12;
			break;
		case 4:
			$cut_num = 15;
			break;
	}
	$where = "";
	if($search_text != ""){
		$where .= "and p.".$search_type." LIKE '%".trim($search_text)."%' ";
	}

	if($sprice && $eprice){
		$where .= "and sellprice between $sprice and $eprice ";
	}

	if($status_where){
		$where .= " and ($status_where) ";
	}
	if($brand2 != ""){
		$where .= " and brand = ".$brand2."";
	}

	if($brand_name != ""){
		$where .= " and brand_name LIKE '%".trim($brand_name)."%' ";
	}

	if($disp != ""){
		$where .= " and p.disp = ".$disp;
	}

	if($one_commission){
		$where .= " and p.one_commission = '".$one_commission."'";
	}



	if($state2 != ""){
		$where .= " and state = ".$state2."";
	}


	if($cid2 != ""){
		$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
	}else{
		$where .= "";
	}
	if($admininfo[admin_level] == 9){
		if($company_id != ""){
			$addWhere = "and admin ='".$company_id."'";
		}else{
			unset($addWhere);
		}
		$sql = "SELECT distinct p.id, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid, p.stock,  p.search_keyword,state, p.brand_name, csd.delivery_price,  p.basicinfo,
		p.company, p.pcode, p.coprice, p.listprice,  p.disp, p.editdate, p.reserve, p.reserve_rate,  case when p.one_commission = 'Y' then p.commission else csd.commission end as commission , p.etc2, p.one_commission, p.commission as goods_commission , csd.commission as company_commission,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp
		FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c,  ".TBL_COMMON_SELLER_DELIVERY." csd
		where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid  $addWhere $where $orderbyString $limit_string";
		//echo $sql;
		$db->query($sql);
	}else{
		$sql = "SELECT distinct p.id, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid, p.stock,  p.search_keyword,state, p.brand_name, csd.delivery_price,  p.basicinfo,
		p.company, p.pcode, p.coprice, p.listprice,  p.disp, p.editdate, p.reserve, p.reserve_rate, case when p.one_commission = 'Y' then p.commission else csd.commission end as commission, p.etc2, p.one_commission, p.commission as goods_commission , csd.commission as company_commission,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2
		FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c,  ".TBL_COMMON_SELLER_DELIVERY." csd
		where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid  and admin ='".$admininfo[company_id]."' $where $orderbyString $limit_string";


		$db->query($sql);
	}
	//echo $sql;
}else{

	if ($cid2 == ""){
		if($admininfo[admin_level] == 9){
			if($company_id != ""){
				$addWhere = "and admin ='".$company_id."'";
			}else{
				unset($addWhere);
			}
			//$tmp_sql = "create temporary table ".TBL_LOGSTORY_BYPAGE."_tmp ENGINE = MEMORY select vdate, pageid, ncnt, nduration from ".TBL_SHOP_PRODUCT_RELATION." where vdate = '$vdate' ";

			$sql = "SELECT distinct (p.id) as id, p.pname, p.brand,p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid, p.stock,  p.search_keyword,state, p.brand_name, csd.delivery_price,  p.basicinfo,
			p.company, p.pcode, p.coprice, p.listprice,  p.disp, p.editdate, p.reserve, p.reserve_rate, case when p.one_commission = 'Y' then p.commission else csd.commission end as commission, p.etc2,p.one_commission, p.commission as goods_commission , csd.commission as company_commission,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp
			FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c,  ".TBL_COMMON_SELLER_DELIVERY." csd
			where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid $where $addWhere $orderbyString $limit_string";
			//echo $sql;
			$db->query($sql);
		}else{
			$sql = "SELECT distinct (p.id) as id ,p.brand, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid, p.stock,  p.search_keyword,state, p.brand_name,  csd.delivery_price,  p.basicinfo,
			p.company, p.pcode, p.coprice, p.listprice,  p.disp, p.editdate, p.reserve, p.reserve_rate, case when p.one_commission = 'Y' then p.commission else csd.commission end as commission, p.etc2,p.one_commission, p.commission as goods_commission , csd.commission as company_commission,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2
			FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c,  ".TBL_COMMON_SELLER_DELIVERY." csd
			where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid and admin ='".$admininfo[company_id]."' $where $orderbyString $limit_string";


			//echo $sql;
			$db->query($sql);
		}

		//echo $sql;
	}else{
		switch ($depth){
			case 0:
				$cut_num = 3;
				break;
			case 1:
				$cut_num = 6;
				break;
			case 2:
				$cut_num = 9;
				break;
			case 3:
				$cut_num = 12;
				break;
			case 4:
				$cut_num = 15;
				break;
		}

		if($admininfo[admin_level] == 9){
			$sql = "SELECT distinct (p.id) as id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder,r.cid, p.stock, p.search_keyword,state, p.brand, b.brand_name,  p.size, p.natural_item,  csd.delivery_price,  p.delivery_type, p.basicinfo,
				p.company, p.pcode, p.coprice, p.listprice,  p.disp, p.editdate,  p.reserve_rate,  case when p.one_commission = 'Y' then p.commission else csd.commission end as commission, p.etc2, p.one_commission, p.commission as goods_commission , csd.commission as company_commission,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp
				FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c, ".TBL_SNS_BRAND." b ,  ".TBL_COMMON_SELLER_DELIVERY." csd
				where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid and p.brand = b.b_ix and r.cid = '".$cid2."' $where $orderbyString $limit_string";

			//echo $sql;

			$db->query($sql);
		}else{
			$sql = "SELECT distinct (p.id) as id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder,  r.cid, p.stock, p.search_keyword,state, p.brand, b.brand_name,  p.size, p.natural_item,  csd.delivery_price, p.delivery_type, p.basicinfo,
				p.company, p.pcode, p.coprice, p.listprice,   p.disp, p.editdate,  p.reserve_rate, case when p.one_commission = 'Y' then p.commission else csd.commission end as commission, p.etc2, p.one_commission, p.commission as goods_commission , csd.commission as company_commission,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2
				FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c, ".TBL_SNS_BRAND." b ,  ".TBL_COMMON_SELLER_DELIVERY." csd
				where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid and p.brand = b.b_ix and r.cid = '".$cid2."' and admin ='".$admininfo[company_id]."' $where $orderbyString $limit_string";

				//echo $sql;
				$db->query($sql);

				//echo "test".$db->total;

		}
	}
}

//$datas[0] = "상품명\t상품코드\t업체명\t원산지\t사이즈\t소재(재질)\t브랜드\t재고량\t유사검색어\t옵션1\t판매가\t할인가\t수수료\t마일리지\t배송선택\t배송료\t기본이미지\t추가이미지1\t추가이미지2\t추가이미지3\t추가이미지4\t추가이미지5\t상세정보\n";
/*
for ($i = 0; $i < $db->total; $i++)
{
	$db->fetch($i);
	$datas[$i] = array(
								"상품명"=>$db->dt[pname],
								"상품코드"=>$db->dt[pcode],
								"업체명"=>$db->dt[com_name],
								"원산지"=>$db->dt[company],
								"사이즈"=>$db->dt[size],
								"소재재질"=>$db->dt[natural_item],
								"브랜드"=>$db->dt[brand],
								"재고"=>$db->dt[stock],
								"유사검색어"=>str_replace(array("\r\n","\n","\r"),"",$db->dt[search_keyword]),
								"판매가"=>$db->dt[sellprice],
								"할인가"=>$db->dt[listprice],
								"수수료"=>$db->dt[commission],
								"마일리지"=>$db->dt[etc2],
								"배송선택"=>$db->dt[delivery_type],
								"배송료"=>$db->dt[delivery_price],
								"기본이미지"=>"http://www.thezoom.co.kr/".$admin_config[mall_data_root]."/images/product/b_".$db->dt[id].".gif",
								"상세정보"=>$db->dt[basicinfo]
								);
	//."\t".$db->dt[com_name]."\t".$db->dt[company]."\t".$db->dt[size]."\t".$db->dt[natural_item]."\t".$db->dt[brand]."\t".$db->dt[stock]."\t".str_replace(array("\r\n","\n","\r"),"",$db->dt[search_keyword])."\t옵션정보\t".$db->dt[sellprice]."\t".$db->dt[listprice]."\t".$db->dt[commission]."\t".$db->dt[etc2]."\t".$db->dt[delivery_type]."\t".$db->dt[delivery_price]."\thttp://www.thezoom.co.kr/".$admin_config[mall_data_root]."/images/product/b_".$db->dt[id].".gif\t추가이미지1\t추가이미지2\t추가이미지3\t추가이미지4\t추가이미지5\t".str_replace(array("\r\n","\n","\r"),"",$db->dt[basicinfo])."\n"
	//	$db->fetch($i);

}
*/

//$mstring = "상품명\t상품코드\t업체명\t원산지\t브랜드\t재고량\t유사검색어\t옵션1\t공급가\t정가\t판매가(할인가)\t수수료\t마일리지\t배송선택\t배송료\t기본이미지\t추가이미지1\t추가이미지2\t추가이미지3\t추가이미지4\t추가이미지5\t상세정보\n";
$mstring = "상품명,상품코드,업체명,원산지,브랜드,재고량,유사검색어,옵션1,공급가,정가,판매가(할인가),수수료,배송선택,배송료,기본이미지,상세정보\n";

for ($i = 0; $i < $db->total; $i++)
{
	$db->fetch($i);
	$mstring .= str_replace(array(","),"",addslashes($db->dt[pname])).",".$db->dt[pcode].",".$db->dt[com_name].",".$db->dt[company].",".$db->dt[brand_name].",".$db->dt[stock].",".str_replace(array("\r\n","\n","\r"),"",$db->dt[search_keyword]).",옵션정보,".$db->dt[coprice].",".$db->dt[listprice].",".$db->dt[sellprice].",".$db->dt[commission].",".$db->dt[delivery_type].",".$db->dt[delivery_price].",http://".$HTTP_HOST.$admin_config[mall_data_root]."/images/product/b_".$db->dt[id].".gif,".str_replace(array("\r\n","\n","\r","\t"),"",addslashes($db->dt[basicinfo]))."\n";//
	//$mstring .= $db->dt[pname]."\t".$db->dt[pcode]."\t".$db->dt[com_name]."\t".$db->dt[company]."\t".$db->dt[brand]."\t".$db->dt[stock]."\t".str_replace(array("\r\n","\n","\r"),"",$db->dt[search_keyword])."\t옵션정보\t".$db->dt[coprice]."\t".$db->dt[listprice]."\t".$db->dt[sellprice]."\t".$db->dt[commission]."\t".$db->dt[etc2]."\t".$db->dt[delivery_type]."\t".$db->dt[delivery_price]."\thttp://www.welbay.co.kr/".$admin_config[mall_data_root]."/images/product/b_".$db->dt[id].".gif\t추가이미지1\t추가이미지2\t추가이미지3\t추가이미지4\t추가이미지5\t".str_replace(array("\r\n","\n","\r","\t"),"",$db->dt[basicinfo])."\n";//
//	$db->fetch($i);

}
echo iconv("utf-8","CP949",$mstring);
exit;

//$datas = $db->fetchall("assoc");

//print_r($datas);
require_once "../../include/excel.php";

$export_file = "xlsfile://tmp/example.xls";//저장하고싶은 파일이름, 앞에 꼭 xlsfile:// 를 붙여야한다.

$fp = fopen($export_file, "wb");

if (!is_resource($fp)){
    die("Cannot open $export_file");
}

// 엑셀에 들어갈 내용을 배열로 만든다.

$assoc = array(

    array("Sales Person" => "엑셀된다 만만세", "Q1" => "$3255", "Q2" => "$3167", "Q3" => 3245, "Q4" => 3943),

    array("Sales Person" => "Jim Brown", "Q1" => "$2580", "Q2" => "$2677", "Q3" => 3225, "Q4" => 3410),

    array("Sales Person" => "John Hancock", "Q1" => "$9367", "Q2" => "$9875", "Q3" => 9544, "Q4" => 10255),

);
//print_r($assoc);
fwrite($fp, serialize($datas));

fclose($fp);


$export_file = "xlsfile://tmp/example.xls";//다운로드할 파일 이름

header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");

header ("Cache-Control: no-cache, must-revalidate");

header ("Pragma: no-cache");

header ("Content-type: application/x-msexcel");

header ("Content-Disposition: attachment; filename=\"" . basename($export_file) . "\"" );

header ("Content-Description: PHP/INTERBASE Generated Data" );

readfile($export_file);

exit;
?>