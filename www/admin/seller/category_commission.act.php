<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");
$db = new Database;
$db2 = new Database;

if($mode == 'update'){
	$cid = $_REQUEST[cid];

	if($is_use == '3'){

		$db->query("select * from shop_category_commission where cid = '".$cid."'");
		$db->fetch();
		
		if($db->total > 0){
			$sql = "update shop_category_commission set
						is_use = '".$is_use."',
						set_group = '".$set_group."',
						gp_ix = '".$gp_ix."',
						gp_name = '".$gp_name."',
						wholesale_commission = '".$wholesale_commission."',
						commission = '".$commission."',
						editdate = NOW()
					where
						cid = '".$cid."'
					";
			$db->query($sql);
		}else{
			$sql = "insert into shop_category_commission set
					cid = '".$cid."',
					is_use = '".$is_use."',
					set_group = '".$set_group."',
					gp_ix = '".$gp_ix."',
					gp_name = '".$gp_name."',
					wholesale_commission = '".$wholesale_commission."',
					commission = '".$commission."',
					regdate = NOW();
					";
			$db->query($sql);
		}
	}

	$like_cid = substr($cid,0,($this_depth == '0'?'3':($this_depth + 1) * 3));
	$sql = "select cid from shop_category_info where cid like '".$like_cid."%' and cid !='".$cid."'";
	//echo nl2br($sql)."<br><br>";
	$db->query($sql);
	$category_commission_info = $db->fetchall();

	for($i=0;$i<count($category_commission_info);$i++){
		$category_commission_info[$i][wholesale_commission] = $wholesale_commission;
		$category_commission_info[$i][commission] = $commission;
	}

	$shmop = new Shared("category_commission_info");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$shmop->setObjectForKey($category_commission_info,"category_commission_info");

echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>parent.document.location.href='category_commission_info.php';</Script>";

}

if($mode == 'category_commission'){

	if($cid){
		$sql = "select
				*
				from
					shop_category_commission
				where
					cid = '".$cid."'";
		$db->query($sql);
		$data_array = $db->fetchall();
		
		if($data_array[0][is_use]){
			$discount_array[is_use] = $data_array[0][is_use];
		}else{
			$discount_array[is_use] = '2';
		}
		$discount_array[cid] = $cid;

		if(is_array($data_array)){
			for($i=0;$i<count($data_array);$i++){
				$discount_array[discount][wholesale_commission] = $data_array[$i][wholesale_commission];
				$discount_array[discount][commission] = $data_array[$i][commission];
			}
		}else{
			$discount_array[discount][wholesale_commission] = '0';
			$discount_array[discount][commission] = '0';
		}

		$datas = json_encode($discount_array);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;
	
	}
}

//echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>parent.document.location.href='category.php';</Script>";

if($mode == "get_category_name"){
	if($cid){
		$sql = "select * from shop_category_info where cid = '".$cid."'";
		$db->query($sql);
		$db->fetch();
		$depth = $db->dt[depth];
	
		for($i=0;$i<=$depth;$i++){
			$this_cid = substr(substr($cid, 0,($i*3+3)).'000000000000',0,15);
			//echo "$i"."<br>";
			$sql = "select * from shop_category_info where cid = '".$this_cid."'";
			//echo nl2br($sql)."<br>";
			$db2->query($sql);
			$db2->fetch();
			$cname = $db2->dt[cname];
			
			if($i == $depth){
				$relation_cname .= $cname;
			}else{
				$relation_cname .= $cname." > ";
			}
			
		}
		$category_info[$cid] = $relation_cname;
	
		$datas = json_encode($category_info);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;
	}

}

if($mode == 'Initialize_discount'){		//할인율 초기화 ajax
	// 모든 데이타 삭제

	if($admininfo[admin_level] == '9'){
		$sql = "delete from shop_category_commission ";
		$db->query($sql);

		echo "Y";
	}else{
		echo "N";
	}
}


function ParentCategoryUseUpdate($mdb, $cid, $this_depth){
	$where = "";
	for($i=0;$i <= $this_depth;$i++){
		if(!$where){
			$where .= " where (cid LIKE '".substr($cid,0,($i+1)*3)."%' and depth = '".$i."' ) ";
		}else{
			$where .= " or (cid LIKE '".substr($cid,0,($i+1)*3)."%' and depth = '".$i."' ) ";
		}
	}
	$sql = "select cid, cname,  depth, category_use from ".TBL_SHOP_CATEGORY_INFO."   $where ";

	$mdb->query($sql);

	$parent_category = $mdb->fetchall();
	for($i=0;$i < count($parent_category);$i++){
		$sql = "update ".TBL_SHOP_CATEGORY_INFO." set category_use ='1' where cid = '".$parent_category[$i][cid]."' ";
		//echo $sql;
		$mdb->query($sql);
	}

}


if ($mode == "insert"){

	if(trim($sub_cid) != "" && trim($cid) != "") {// 카테고리 정보가 제대로 안넘어 올 경우를 검사 kbk 12/03/22
		$sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where cid = '$cid'";
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

		$sql = "insert into ".TBL_SHOP_CATEGORY_INFO."
					(cid, depth,vlevel1, vlevel2, vlevel3,vlevel4,vlevel5,cname,cname_color,cname_on,cname_on_color,catimg,catimg_on,leftcatimg,leftcatimg_on, subimg,subimg_on, category_use,category_display_type,category_type, category_link,regdate,category_code,is_adult)
					values
					('$sub_cid', '$sub_depth','$level1','$level2','$level3','$level4','$level5', '$sub_category','$sub_category_color','$sub_category_on','$sub_category_on_color','$category_img_name','$category_img_on_name','$leftcategory_img_name','$leftcategory_img_on_name','$sub_img_name','$sub_img_on_name','$category_use','$category_display_type','$category_type','$category_link',NOW(),'$category_code','$is_adult')";

		$db->query($sql);
	//	echo $sql ;

		echo "<Script Language='JavaScript'>parent.document.location.href='category.php?cid=$cid';</Script>";
	//	Header("Location: category.php");

		updateCategoryXML();
	} else {
		echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert(language_data['category.save.php']['D'][language]);</Script>";
		//카테고리 정보가 정확하지 않습니다. 상위 카테고리를 선택해 주세요.
	}

}

function getMaxlevel($cid,$depth)
{
	global $db;

	$strdepth = $depth + 1;

	$sPos = $depth*3;
	$sql = "select max(vlevel$strdepth)+1 as maxlevel from ".TBL_SHOP_CATEGORY_INFO." where cid LIKE '".substr($cid,0,$sPos)."%'";

	$db->query($sql);
	$db->fetch(0);

//	echo $sql."<br>";
//	echo $db->dt["maxlevel"]."<br>";

	return $db->dt["maxlevel"];

}

function CheckSubCategory($cid,$depth){
	global $db;

	$endpos = $depth*3+3;
	$this_depth = $depth;
	$sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth > $this_depth and cid LIKE '".substr($cid,0,$endpos)."%'";
	$db->query($sql);
	//echo "$sql<br>";

	if ($db->total > 0){
		return true;
	}else{
		return false;
	}

}

function updateCategoryXML(){

	global $DOCUMENT_ROOT, $admin_config;

	$xml = new XmlWriter_();
	$mdb = new Database;
	$mdb->query("select * from ".TBL_SHOP_CATEGORY_INFO." where category_use = 1 order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");
	$categorys = $mdb->fetchall();

	$xml->push('categorys');

	if(count($categorys) > 0){
		foreach ($categorys as $category) {
			//$xml->push('shop', array('species' => $animal[0]));
			$xml->push('category', array('cid' => $category[cid], 'depth' => $category[depth], 'top_cid' => substr($category[cid],0,3)));
			$xml->element('cid', $category[cid]);
			$xml->element('cname', $category[cname]);
			$xml->element('cname_on', $category[cname_on]);
			$xml->element('depth', $category[depth]);
			$xml->pop();
		}

		$xml->pop();
		//print $xml->getXml();

		$dirname = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete];
		/*
		if(!is_dir($dirname)){
			if(is_writable($path)){
				mkdir($dirname, 0777, true);
				chmod($dirname, 0777);
			}
		}
		*/
		//$fileName = "main_flash.xml";
		$fp = fopen($dirname."/categorys.xml","w");
		fputs($fp, $xml->getXml());
		fclose($fp);
	}
}
//echo "<br>maxlevel:".getMaxlevel($cid,$sub_depth);


/*
1. 카테고리 할인율 정보를 위한 추가 2014-04-19 이학봉
*/



?>