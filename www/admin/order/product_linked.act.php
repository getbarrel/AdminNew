<?
include("../class/layout.class");

$db = new Database;

if ($act == "insert") {
    $failList = array();
    if (!empty($site_code) && is_array($site_code)) {
        $list = array();
        foreach ($site_code as $key => $sc) {
            if (!empty($site_code[$key]) && !empty($sg_code[$key]) && !empty($gid[$key]) && !empty($qty[$key])) {
                $list[] = "('" . $site_code[$key] . "','DEWYTREE" . $sg_code[$key] . "','" . $gid[$key] . "','" . $qty[$key] . "','" . $memo[$key] . "',NOW())";
            } else {
                $failList = "(" . $site_code[$key] . "," . $sg_code[$key] . "," . $gid[$key] . "," . $qty[$key] . "," . $memo[$key] . "')";
            }
        }
        $sql = "insert into dewytree_product_linked (site_code, sg_code, gid, qty, memo, regdate) values " . implode(',', $list);
        $db->query($sql);
    }

    echo("<script>alert('" . (count($failList) > 0 ? '등록 실패 ' . count($failList) . '건 [' . implode(',', $failList) . ']' : '성공적으로 등록되었습니다.') . "');top.location.reload();</script>");
    exit;
}

if ($act == "update") {
    $sql = "update dewytree_product_linked set
				gid='" . $gid . "',
				qty='" . $qty . "',
				memo='" . $memo . "'
				where pl_id='" . $pl_id . "'";
    $db->query($sql);
    exit;
}

if ($act == "delete") {
    $db->query("DELETE FROM dewytree_product_linked WHERE pl_id='$pl_id'");
    exit;
}

?>