<?
include("../../class/database.class");

$db = new Database;

if ($act == "orgin_category_info_update"){
	
	$sql = "update shop_buyingservice_url_info set
				orgin_category_info = '".$orgin_category_info."' 
				where bsui_ix = '".$bsui_ix."' ";
	$db->query($sql);
	
	echo "정상적으로 수정되었습니다.";
}


if ($act == "delete"  || $act == "ajax_delete"){
	
	$sql = "delete from shop_buyingservice_url_info 	where bsui_ix = '".$bsui_ix."' ";
	//echo $sql;
	$db->query($sql);
	
	//echo  "<script language='javacript'>alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>";
    if($act == "ajax_delete"){
        echo "정상적으로 삭제되었습니다";
    }else{
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제되었습니다.');parent.document.location.reload();</script>");
    }
    exit;
}

if ($act == "update"){
	
	$sql = "update shop_buyingservice_url_info set 
				cid='".$cid2."',
				depth='".$depth."',
				bs_site='".$bs_site."',
				bs_list_url='".$bs_list_url."',
				bs_list_url_md5='".md5($bs_list_url)."',
				orgin_category_info='".$orgin_category_info."',
				currency_ix='".$currency_ix."',
				disp='".$disp."'	
				where bsui_ix = '".$bsui_ix."' ";
	//echo $sql;
	//exit;
	$db->query($sql);
	
	//echo  "<script language='javacript'>alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>";
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
}

if ($act == "insert"){

    $sql = "insert into shop_buyingservice_url_info set 
				cid='".$cid2."',
				depth='".$depth."',
				bs_site='".$bs_site."',
				bs_list_url='".$bs_list_url."',
				bs_list_url_md5='".md5($bs_list_url)."',
				orgin_category_info='".$orgin_category_info."',
				currency_ix='".$currency_ix."',
				disp='".$disp."'";
    //echo $sql;
    //exit;
    $db->query($sql);

    //echo  "<script language='javacript'>alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>";
    echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 등록되었습니다.');parent.opener.document.location.reload();parent.window.close();</script>");
}

?>