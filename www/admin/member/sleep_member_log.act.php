<?
include("../class/layout.class");
$db= new MySQL;
if($act == 'delete'){
    $sql="delete from common_user_sleep_log where sl_ix = '$code'";
    $db->query($sql);
    $db->fetch();

    echo "<script>alert('삭제되었습니다.');parent.document.location.reload(); </script>";
}
?>