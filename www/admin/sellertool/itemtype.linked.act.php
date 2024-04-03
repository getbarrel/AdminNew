<?
include("../../class/database.class");
include("sellertool.lib.php");
include_once("../openapi/openapi.lib.php");

$db = new Database;
/*
if($cid3_1)
    $target_cid = $cid3_1;
else if($cid2_1)
    $target_cid = $cid2_1;
else if($cid1_1)
    $target_cid = $cid1_1;
else if($cid0_1)
    $target_cid = $cid0_1;
*/
if($target_cid == ""){
	$target_cid = $cid2;
}
if($target_depth == ""){
	$target_depth = $depth;
}


if($target_cid != ""){
    $db->query("select cname from ".$category_table." where cid = '$origin_cid'");
    $db->fetch();
    $origin_name = $db->dt[cname];
}

if ($act == "insert" || $act == "json_insert"){
	$sql = "select
                *
            from
                sellertool_itemtype_linked_relation
            where
                origin_cid = '".$origin_cid."'
            AND
                target_cid = '".$target_cid."'
			AND
				site_code = '".$site_code."'
            ";
    $db->query($sql);
    if($db->total){
        echo "<script>alert('이미 등록한 카테고리입니다.');self.close();</script>";
        exit;
    }
	$target_name = $cname;

	$sql = "insert into sellertool_itemtype_linked_relation 
				(ila_ix,site_code,origin_cid,origin_name,origin_depth,target_cid,target_name,target_depth,rel_date)
				values
				('$ila_ix','$site_code','$origin_cid','$origin_name','$origin_depth','$target_cid','$target_name','$target_depth',NOW())";
	//echo $sql;
	//exit;
	$db->query($sql);

	//$db->query("update shop_category_linked_all set relation_yn = 'Y' where ila_ix = $ila_ix");
	//echo  "<script language='javacript'>alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>";
	if($act == "json_insert"){
		$db->query("SELECT ilr_ix, rel_date FROM sellertool_itemtype_linked_relation WHERE ilr_ix=LAST_INSERT_ID()");
		$db->fetch();

		//$results["sql"] = $sql;
		$results["message"] = $target_name." 정상적으로 등록되었습니다.";
		$results["data"] = $db->dt;
		
		echo json_encode($results);
		exit;
	}else{
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력되었습니다.');parent.opener.document.location.reload();parent.window.close();</script>");
	}
	exit;
}

if ($act == "update"){

	$target_name = $cname;
	$sql = "update sellertool_itemtype_linked_relation set
				ila_ix='$ila_ix',
                site_code='$site_code',
                origin_cid='$origin_cid',
				origin_name='$origin_name',
                target_cid='$target_cid',
                target_name='$target_name',
                target_depth='$target_depth',
                editdate=NOW()
                where ilr_ix='$ilr_ix'";
	//echo $sql;
	//exit;
	$db->query($sql);

    //$db->query("update shop_category_linked_all set relation_yn = 'Y' where ila_ix = $ila_ix");

	//echo  "<script language='javacript'>alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>";
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.opener.document.location.reload();parent.window.close();</script>");
}
if ($act == 'delete'){

        $db->query("delete from sellertool_itemtype_linked_relation where ilr_ix = '$ilr_ix'");

       // $db->query("update shop_category_linked_all set relation_yn = 'N' where ila_ix = '$ila_ix'");

        echo 'SUCCESS';
}
/*
if ($act == 'get_itemtype_list'){

	if(!is_object($OAL)){
		$OAL = new OpenAPI($site_code);
	}

	//echo $a."<br>pid : ".$pid."  site_code : ".$site_code."<br>" ;
	$result = $OAL->searchCategory($cname);
	//echo $result;
	echo json_encode($result);
	exit;
}
*/
?>