<?
include("../class/database.class");

session_start();

$db = new Database;
$table = "shop_add_delivery_area";
if($act == "change"){

    $sql = "update ".$table." set
            price = ".$price."
            , regdate = now()
            where ix = ".$ix." 
    ";
    $qRes = $db->query($sql);

    $res = array();
    $msg = '';
    if($qRes == true){
        $msg = '정상적으로 수정되었습니다.';
        $res['result'] = 'success';
    }else{
        $msg = '수정에 실패했습니다.';
        $res['result'] = 'fail';
    }
    $res['msg'] = $msg;

    if($return_type == 'json'){
        echo json_encode($res);
    }else{
        echo "<script language='javascript'>alert(".$msg.");</script>";
    }
}

if($act == "delete"){

//	echo "<script language='javascript'>alert('즐겨찾기가 정상적으로 삭제되었습니다.');parent.document.location.reload();</script>";

}
?>