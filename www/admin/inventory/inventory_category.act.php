<?
@include_once("../web.config");
include_once("../../class/database.class");
include_once("../lib/imageResize.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");



//	print_r($goods_items);
//	exit;

session_start();
$db = new Database;

$tb = $_SESSION['admin_config']["mall_inventory_category_div"]=="P"	?	TBL_SHOP_CATEGORY_INFO:"inventory_category_info";

if($admininfo[company_id] == ""){
	echo "<script language='JavaScript' src='../_language/language.php'></Script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";
	//'관리자 로그인후 사용하실수 있습니다.'
	exit;
}

if ($act == "add"){
	if(trim($sub_cid) != "" && trim($cid) != "") {

		$sql = "select * from ".$tb." where cid = '$cid'";
		$db->query($sql);
		$db->fetch(0);

		$level1 = $db->dt["vlevel1"];
		$level2 = $db->dt["vlevel2"];
		$level3 = $db->dt["vlevel3"];
		$level4 = $db->dt["vlevel4"];
		$level5 = $db->dt["vlevel5"];

		if ($sub_depth+1 == 1){
			$level1 = getMaxlevel($cid,$sub_depth);
		}else if($sub_depth+1 ==2){
			$level2 = getMaxlevel($cid,$sub_depth);
		}else if($sub_depth+1 ==3){
			$level3 = getMaxlevel($cid,$sub_depth);
		}else if($sub_depth+1 ==4){
			$level4 = getMaxlevel($cid,$sub_depth);
		}else if($sub_depth+1 ==5){
			$level5 = getMaxlevel($cid,$sub_depth);
		}

		$sql = "insert into ".$tb."
					(cid, depth,vlevel1, vlevel2, vlevel3,vlevel4,vlevel5,cname,category_use,category_code,regdate)
					values
					('$sub_cid', '$sub_depth','$level1','$level2','$level3','$level4','$level5','$sub_category','$category_use','$category_code',NOW())";
		$db->query($sql);

		echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert('정상적으로 등록되었습니다.');parent.document.location.href='inventory_category.php?cid=".$cid."&depth=".$sub_depth."';</Script>";
		exit;
	}else{
		echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert('정보가 정확하지 않습니다. 상위 분류를 선택해 주세요.');</Script>";
	}
}

if ($act == "update"){

	$sql = "update ".$tb." set  cname='".$this_category."' , category_code='".$category_code."' , category_use='".$category_use."'  where cid = '".$cid."' ";
	$db->query($sql);

	echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert('정상적으로 수정되었습니다.');parent.document.location.href='inventory_category.php?cid=".$cid."';</Script>";
	exit;
}

if ($act == "delete"){

	$sql = "SELECT * FROM ".$tb." ci where cid = '".$cid."' ";
	$db->query($sql);
	$db->fetch();
	$this_depth = $db->dt[depth];
	$cid = $db->dt[cid];

	if (CheckSubCategory($cid,$this_depth)){
		//if($sub_cartegory_delete == "1"){
			$sql = "select count(*) as total from inventory_goods where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'  ";
			$db->query($sql);
			$db->fetch();
			$total = $db->dt[total];
			if($total > 0){
				echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert('해당 분류 하부에 등록된 품목이 존재 합니다.');</Script>";
				exit;
			}else{
				$sql = "delete from ".$tb." where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
				$db->query($sql);
			}
	}else{
		$sql = "delete from ".$tb." where cid = '$cid'";
		$db->query($sql);
	}

	echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert('정상적으로 삭제되었습니다.');parent.document.location.href='inventory_category.php';</Script>";
	exit;
}

if ($act == "get_category_infos")
{

	if($root && $root != "source"){
		$sql = "SELECT * FROM ".$tb." ci where cid = '".$root."' ";
		$db->query($sql);
		$db->fetch();
		$depth = $db->dt[depth];
		$cid = $db->dt[cid];

		$sql = "SELECT ci.*, (select count(*) from ".$tb." where depth != ci.depth and substr(cid,1,(ci.depth+1)*3) = substr(ci.cid,1,(ci.depth+1)*3))  as sub_cnt FROM ".$tb." ci
		where depth = '".($depth+1)."' and cid LIKE '".substr($cid,0,3*($depth+1))."%'
		order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5";

	}else{
			$sql = "SELECT ci.*, (select count(*) from ".$tb." where depth != ci.depth and substr(cid,1,(ci.depth+1)*3) = substr(ci.cid,1,(ci.depth+1)*3))  as sub_cnt FROM ".$tb." ci
			where depth in('0')
			order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5";
	}

	$db->query($sql);

	$total = $db->total;

	$datas = $db->fetchall("object"); //오라클때문에 수정
	for ($i = 0; $i < count($datas); $i++)
	{

	//	$db->fetch($i);

		if ($datas[$i]["sub_cnt"] > 0){
			if($datas[$i]["depth"] < 1){
				$expanded = true;
			}else{
				$expanded = false;
			}
			$trees[] = array("classes"=>"folder","text"=>"&nbsp;<a href='#' ondblclick='EditCategory($(this))'>".$datas[$i]["cname"]."</a> &nbsp;&nbsp;&nbsp;<a href=\"javascript:addSubCategory('".$datas[$i]["cid"]."');\">+</a>","id"=>$datas[$i]["cid"], "expanded"=> $expanded, "children"=>getSubCategory($datas[$i]["cid"], $datas[$i]["depth"]));
			//"hasChildren"=> true,
			//"expanded"=>"true",
			//$trees[] = array("text"=>"&nbsp;<input type=text class=textbox name='cname' value='".$datas[$i]["cname"]."'>","id"=>$datas[$i]["cid"],"hasChildren"=> "true");
		}else{

			$trees[] = array("classes"=>"folder","text"=>"&nbsp;<a href='#' ondblclick='EditCategory($(this))' onclick=\"$(this).css('background-color','#efefef');\">".$datas[$i]["cname"]."</a> &nbsp;&nbsp;&nbsp;<a href=\"javascript:addSubCategory('".$datas[$i]["cid"]."');\">+</a>","id"=>$datas[$i]["cid"], "expanded"=> $expanded,"hasChildren"=> false);
			//$trees[$b_0depth_i][children][] = array("text"=>$datas[$i]["cname"]);
			//unset($sub_trees);
		}

	}
	//print_r($trees);
	//exit;
	$datas = json_encode($trees);
	if($root && $root != "source"){
		echo $datas;
	}else{
		echo $datas;//echo '[{"text":" 재고상품 분류","expanded": true,"classes": "important","children":'.$datas."}]";
	}
	exit;
}

if($act == "getLeftmenuData"){

	$trees = getMenuData("inventory","tree");
	//echo print_r($trees);
	echo json_encode($trees);
}


if($act == "product_cnt"){
	if($cid && $cid!='000000000000000'){
		$sql = "select
					count(gu_ix) as cnt
				from
					inventory_goods g , inventory_goods_unit gu
				where
					g.gid=gu.gid
				and
					g.cid = '".$cid."'";
		$db->query($sql);
		$db->fetch();

		if($db->dt[cnt]){
			$product_cnt = $db->dt[cnt];
		}else{
			$product_cnt = '0';
		}

		$sql = "select
					count(gu_ix) as cnt
				from
					inventory_goods g , inventory_goods_unit gu
				where
					g.gid=gu.gid
				and
					g.cid like '".substr($cid,0,3+(3*$depth))."%'";
		$db->query($sql);
		$db->fetch();

		if($db->dt[cnt]){
			$product_total_cnt = $db->dt[cnt];
		}else{
			$product_total_cnt = '0';
		}

		$data_array[product_cnt] = $product_cnt;
		$data_array[product_total_cnt] = $product_total_cnt;

		$datas = json_encode($data_array);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;

	}

}

function getSubCategory($cid, $depth){
	global $tb;

	$mdb = new Database;

	$sql = "SELECT ci.*, (select count(*) from ".$tb." where depth != ci.depth and substr(cid,1,(ci.depth+1)*3) = substr(ci.cid,1,(ci.depth+1)*3))  as sub_cnt FROM ".$tb." ci
		where depth = '".($depth+1)."' and cid LIKE '".substr($cid,0,3*($depth+1))."%'
		order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5";
	//echo nl2br($sql)."<br>";

	$mdb->query($sql);
	$datas = $mdb->fetchall("object"); //오라클때문에 수정
	for ($i = 0; $i < count($datas); $i++)
	{

	//	$mdb->fetch($i);

		if ($datas[$i]["sub_cnt"] > 0){
			if($datas[$i]["depth"] < 1){
				$expanded = true;
			}else{
				$expanded = false;
			}
			$__trees[] = array("classes"=>"folder","text"=>"&nbsp;<a href='#' ondblclick='EditCategory($(this))'>".$datas[$i]["cname"]."</a> &nbsp;&nbsp;&nbsp;<a href=\"javascript:addSubCategory('".$datas[$i]["cid"]."');\">+</a>","id"=>$datas[$i]["cid"], "expanded"=> $expanded, "children"=>getSubCategory($datas[$i]["cid"], $datas[$i]["depth"]));//,"hasChildren"=> true
			//"expanded"=>"true",
			//$trees[] = array("text"=>"&nbsp;<input type=text class=textbox name='cname' value='".$datas[$i]["cname"]."'>","id"=>$datas[$i]["cid"],"hasChildren"=> "true");
		}else{
			$__trees[] = array("classes"=>"folder","text"=>"&nbsp;<a href='#' ondblclick='EditCategory($(this))' onclick=\"$(this).css('background-color','#efefef');\">".$datas[$i]["cname"]."</a> &nbsp;&nbsp;&nbsp;<a href=\"javascript:addSubCategory('".$datas[$i]["cid"]."');\">+</a>","id"=>$datas[$i]["cid"],"hasChildren"=> false);
			//$trees[$b_0depth_i][children][] = array("text"=>$datas[$i]["cname"]);
			//unset($sub_trees);
		}
	}

	return $__trees;
}

function getMaxlevel($cid,$depth)
{
	global $db,$tb;

	$strdepth = $depth + 1;

	$sPos = $depth*3;
	$sql = "select max(vlevel$strdepth)+1 as maxlevel from ".$tb." where cid LIKE '".substr($cid,0,$sPos)."%'";

	$db->query($sql);
	$db->fetch(0);


//	echo $sql."<br>";
//	echo $db->dt["maxlevel"]."<br>";


	return $db->dt["maxlevel"];

}


function CheckSubCategory($cid,$depth){
	global $db,$tb;

	$endpos = $depth*3+3;
	$this_depth = $depth;
	$sql = "select * from ".$tb." where depth > $this_depth and cid LIKE '".substr($cid,0,$endpos)."%'";
	$db->query($sql);
	//echo "$sql<br>";

	if ($db->total > 0){
		return true;
	}else{
		return false;
	}

}


function getNextCid($cid,$depth)
{
	global $db,$tb;
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$sPosA = $depth*3;
	$sPos = $depth*3 + 1;
	$sql = "select IFNULL(max(substr(cid,$sPos,3)),0)+1 as maxid from ".$tb." where cid LIKE '".substr($cid,0,$sPos-1)."%'";
	//echo $sql;
	$db->query($sql);

	if($db->total){
		$db->fetch(0);
		$maxid = $db->dt[maxid];
	}else{
		$maxid = "1";
	}


	//echo $maxid;
	//exit;

/*
	echo $sql."<br>";
	echo $db->dt["maxid"]."<br>";
*/
	if ($depth + 1 == 1){
		$cid1 = setFourChar($maxid);
	}else if ($depth + 1 == 2){
		$cid2 = setFourChar($maxid);
	}else if ($depth + 1 == 3){
		$cid3 = setFourChar($maxid);
	}else if ($depth + 1 == 4){
		$cid4 = setFourChar($maxid);
	}else if ($depth + 1 == 5){
		$cid5 = setFourChar($maxid);
	}



	return "$cid1$cid2$cid3$cid4$cid5";

}


function setFourChar($cid_part)
{
	$chrlen = strlen($cid_part);

	$strCid = "$cid_part";
	for($i=0; $i < 3 - $chrlen ; $i++){
		$strCid = "0".$strCid;
	}

	return $strCid;
}

?>
