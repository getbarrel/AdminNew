<?
include("../class/layout.class");

$db = new Database;

if ($act == "insert") {

    $sql = "insert into dewytree_video(title,video_url,disp,regdate) values('$title','$video_url','$disp',NOW())";
    $db->query($sql);

    if ($db->dbms_type == "oracle") {
        $dv_ix = $db->last_insert_id;
    } else {
        $db->query("SELECT dv_ix FROM dewytree_video WHERE dv_ix=LAST_INSERT_ID()");
        $db->fetch();
        $dv_ix = $db->dt['dv_ix'];
    }

    echo("<script>alert('성공적으로 등록되었습니다.');top.location.href = 'dewytree_video.write.php?dv_ix=$dv_ix';</script>");
}

if ($act == "update") {

    $sql = "update dewytree_video set
				title='" . $title . "',
				video_url='" . $video_url . "',
				disp='" . $disp . "'
				where dv_ix='" . $dv_ix . "'";
    $db->query($sql);

    echo("<script>alert('성공적으로 수정되었습니다.');top.location.href = 'dewytree_video.write.php?dv_ix=$dv_ix';</script>");

}

if ($act == "delete") {
    $db->query("DELETE FROM dewytree_video WHERE dv_ix='$dv_ix'");

    echo("<script>alert('성공적으로 삭제되었습니다.');top.location.href = 'dewytree_video.list.php';</script>");
    exit;
}

?>