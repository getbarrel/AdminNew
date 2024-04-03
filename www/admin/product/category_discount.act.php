<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");
$db = new Database;
$db2 = new Database;

if($mode == 'update'){
	$cid = $_REQUEST[cid];
	/*
	2014-05-21 수정내용 이학봉

	1. 개별설정일 경우에만 데이타가 저장됨
	- 할인율 설정 카테고리
	- 사용안하는 카테고리는 할인율을 0%로 설정하도록
	- 데이타가 없는 카테고리는 상위를 자동으로 찾아가게되고 개별설정된 상위 할인율을 따라가게 되고 0뎁스까지 설정이 안되어 있으면 할인율을 0% 로 인식함

	*/


	if($is_use == '3'){

		$db->query("delete from shop_category_discount where cid = '".$cid."'");

		foreach($group_discount as $set_group =>$value){

			if(is_array($value[group_list])){	//
				foreach($value[group_list] as $key => $gp_ix){

					//if($gp_ix){
						$db->query("select gp_name from shop_groupinfo where gp_ix = '".$gp_ix."'");
						$db->fetch();
						$gp_name = $db->dt[gp_name];

						$sql = "insert into shop_category_discount set
									cid = '".$cid."',
									depth = '".$this_depth."',
									is_use = '".$is_use."',
									set_group = '".$set_group."',
									gp_ix = '".$gp_ix."',
									gp_name = '".$gp_name."',
									wholesale_dc_rate = '".$value[wholesale_dc_rate]."',
									dc_rate = '".$value[dc_rate]."',
									regdate = NOW();
									";
						$db->query($sql);
					//}
				}
			}else{	//그룹선택없이 추가할경우는 사용안하는 카테고리에 대한 설정

				$sql = "insert into shop_category_discount set
							cid = '".$cid."',
							depth = '".$this_depth."',
							is_use = '".$is_use."',
							set_group = '".$set_group."',
							gp_ix = '',
							gp_name = '',
							wholesale_dc_rate = '".$value[wholesale_dc_rate]."',
							dc_rate = '".$value[dc_rate]."',
							regdate = NOW();
							";
				$db->query($sql);
			}
		}

	}else{	//일반 카테고리는 사용여부만 따라가면됨.

		/*
		$db->query("delete from shop_category_discount where cid = '".$cid."'");

		$sql = "insert into shop_category_discount set
					cid = '".$cid."',
					is_use = '".$is_use."',
					wholesale_dc_rate = '0',
					dc_rate = '0',
					regdate = NOW();";

		$db->query($sql);
		*/
	}

	//하위카테고리 개념이 없어져서 실행안되게 처리 2014-05-21 이학봉
	if($all_depth == '1' && false){		//하위 카테고리 포함(개별설정 미포함일경우)

		//해당 카테고리 하위에 있는 카테고리중(개별설정만 제외하고 동일 사용여부를 적용)
		$is_use = ($is_use == '3'?'2':$is_use);	//개별배송으로 저장될경우 하위는 상위설정으로 저장함
		$like_cid = substr($cid,0,($this_depth == '0'?'3':($this_depth + 1) * 3));

		$sql = "select * from shop_category_info where cid like '".$like_cid."%' and cid !='".$cid."'";
		$db->query($sql);
		$cid_array = $db->fetchall();

		for($i=0;$i<count($cid_array);$i++){
			$ori_cid = $cid_array[$i][cid];
			$sql = "select is_use from shop_category_discount where cid = '".$ori_cid."'";
			$db->query($sql);
			$db->fetch();

			if($db->total > 0 && $db->dt[is_use] == '3'){	//개별설정일 경우
				continue;
			}else{
				$db->query("delete from shop_category_discount where cid = '".$ori_cid."'");

				$sql = "insert into shop_category_discount set
						cid = '".$ori_cid."',
						depth = '".$this_depth."',
						is_use = '".$is_use."',
						wholesale_dc_rate = '0',
						dc_rate = '0',
						regdate = NOW();";
				$db->query($sql);

			}
		}
	}

	$sql = "select gp_ix, cid, depth, wholesale_dc_rate, dc_rate  from shop_category_discount ";
	$db->query($sql);
	$_category_discount_info = $db->fetchall("object");
	for($i=0;$i < count($_category_discount_info);$i++){
		$category_discount_info[$_category_discount_info[$i][gp_ix]][$_category_discount_info[$i][cid]][gp_ix] = $_category_discount_info[$i][gp_ix];
		$category_discount_info[$_category_discount_info[$i][gp_ix]][$_category_discount_info[$i][cid]][cid] = $_category_discount_info[$i][cid];
		$category_discount_info[$_category_discount_info[$i][gp_ix]][$_category_discount_info[$i][cid]][depth] = $_category_discount_info[$i][depth];
		$category_discount_info[$_category_discount_info[$i][gp_ix]][$_category_discount_info[$i][cid]][wholesale_dc_rate] = $_category_discount_info[$i][wholesale_dc_rate];
		$category_discount_info[$_category_discount_info[$i][gp_ix]][$_category_discount_info[$i][cid]][dc_rate] = $_category_discount_info[$i][dc_rate];
	}
	$category_discount_info = urlencode(serialize($category_discount_info));
	//print_r($_POST);
	//echo $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop = new Shared("category_discount_info");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$shmop->setObjectForKey($category_discount_info,"category_discount_info");

	echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>parent.document.location.href='category_discount_info.php';</Script>";

}

if($mode == 'category_discount'){

	if($cid){
		$sql = "select
				*
				from
					shop_category_discount
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

		for($i=0;$i<count($data_array);$i++){

			if($data_array[$i][set_group]){		//그룹설정일 경우에는 set_group 이 잇어야함
				$discount_array[set_group][$data_array[$i][set_group]][$i][gp_ix] = $data_array[$i][gp_ix];			//set_group 시 TR내역이 복사가 됨으로 항상 discount 위에 잇어야함
				$discount_array[set_group][$data_array[$i][set_group]][$i][gp_name] = $data_array[$i][gp_name];
				$discount_array[discount][$data_array[$i][set_group]][wholesale_dc_rate] = $data_array[$i][wholesale_dc_rate];
				$discount_array[discount][$data_array[$i][set_group]][dc_rate] = $data_array[$i][dc_rate];
			}
		}

		if($data_array[0][gp_ix]){		//그룹설정이 되엇을경우

			$sql = "select
					*
					from
						shop_groupinfo
					where
						1
						and use_discount_type='c'
						and gp_ix not in (select gp_ix from shop_category_discount where cid = '".$cid."')";
			$db->query($sql);
			$gp_array = $db->fetchall();

			if(count($gp_array) > 0){
				for($i=0;$i<count($gp_array);$i++){
					$discount_array[basic_group_discount][$i][gp_ix] = $gp_array[$i][gp_ix];
					$discount_array[basic_group_discount][$i][gp_name] = $gp_array[$i][gp_name];
				}
			}else{
				$discount_array[basic_group_discount][0][gp_ix] = '';
				$discount_array[basic_group_discount][0][gp_name] = '';
			}
		}else{							//사용여부만 체크되엇을경우
			$sql = "select
					*
					from
						shop_groupinfo
					where
						1
						and use_discount_type='c'
						order by gp_ix ASC";
			$db->query($sql);
			$gp_array = $db->fetchall();

			for($i=0;$i<count($gp_array);$i++){
				$discount_array[nodata_basic_group][$i][gp_ix] = $gp_array[$i][gp_ix];
				$discount_array[nodata_basic_group][$i][gp_name] = $gp_array[$i][gp_name];
			}
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
		$sql = "delete from shop_category_discount ";
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
	//$mdb->debug = true;
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
