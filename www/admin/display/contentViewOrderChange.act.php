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
        if($_POST['depth'] == 1){
            $where = " and cid like '".substr($_POST['display_cid'],0,6)."%' ";
        }else{
            $where = " and cid = '".$_POST['display_cid']."' ";
        }
    }

    if(is_array($_POST['con_ix'])){
        foreach($_POST['con_ix'] as $key=>$val){
            $sql = "update shop_content set sort = '".$key."' where con_ix = '".$val."'  $where ";
            $db->query($sql);
        }


        echo "<script language='javascript' src='../js/message.js.php'></script>
                    <script language='javascript'>
                    show_alert('컨텐츠순서 변경이 완료 되었습니다.','top_reload');top.opener.document.location.reload();
                    </script>";
        exit;
    }
}