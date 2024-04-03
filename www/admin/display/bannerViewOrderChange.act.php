<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-10-30
 * Time: 오후 1:42
 */
include $_SERVER['DOCUMENT_ROOT']."/admin/class/layout.class";

if($act == 'update'){

    if($_POST['display_cid']){
        $where = " and display_cid = '".$_POST['display_cid']."'";
    }

    if(is_array($_POST['banner_ix'])){
        foreach($_POST['banner_ix'] as $key=>$val){
            $sql = "update shop_bannerinfo set view_order = '".$key."' where banner_ix = '".$val."' and banner_position = '".$_POST['banner_position']."' $where ";
            $db->query($sql);
        }


        echo "<script language='javascript' src='../js/message.js.php'></script>
                    <script language='javascript'>
                    show_alert('배너순서 변경이 완료 되었습니다.','top_reload');top.opener.document.location.reload();
                    </script>";
        exit;
    }
}