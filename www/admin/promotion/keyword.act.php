<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");


$db = new Database;

if ($act == "insert"){

	
	$db->query("SELECT k_ix FROM shop_search_keyword sr WHERE keyword LIKE '%$keyword%' ");
	if(!$db->total){
		$sql = "insert into shop_search_keyword(k_ix,keyword,keyword_global,ref,searchcnt,searchcnt_web,searchcnt_mobile,recommend,disp,regdate) values('','$keyword','$keyword_global','$ref','$searchcnt','$searchcnt_web','$searchcnt_mobile','$recommend','$disp',NOW())";
		$db->sequences = "SHOP_SEARCH_KEYWORD_SEQ";
		$db->query($sql);
	}else{
		$db->fetch();
		$k_ix = $db->dt[k_ix];
		$sql = "update shop_search_keyword set
					keyword='$keyword',keyword_global='$keyword_global',ref='$ref',searchcnt='$searchcnt',searchcnt_web='$searchcnt_web',searchcnt_mobile='$searchcnt_mobile',recommend='$recommend',disp='$disp'
					where k_ix='$k_ix' ";
		$db->query($sql);
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('키워드가 정상적으로 등록되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){

	$sql = "update shop_search_keyword set
					keyword='$keyword',keyword_global='$keyword_global',ref='$ref',searchcnt='$searchcnt',searchcnt_web='$searchcnt_web',searchcnt_mobile='$searchcnt_mobile',recommend='$recommend',disp='$disp'
					where k_ix='$k_ix' ";
	//echo $sql;
	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('키워드가 정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){

	$sql = "delete from shop_search_keyword where k_ix='$k_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('키워드가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}



if ($act == "save"){

	$ms_data = urlencode(serialize($_POST));

	//echo $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop = new Shared("mobile_search_keyword");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$shmop->setObjectForKey($ms_data,$recommend);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('정상적으로 저장되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");

}

?>
