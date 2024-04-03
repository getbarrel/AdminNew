<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-08-02
 * Time: 오후 9:05
 */
include $_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class";

$db = new database;
if($act == 'insert'){
    //공통 영역 등록

    $sql = "insert 
                bienview_contents_info set
            div_depth = '".$_POST['div_depth']."',
            div_depth_sub = '".$_POST['div_depth_sub']."',
            title = '".$_POST['title']."',
            contents = '".$_POST['contents']."',
            disp = '".$_POST['disp']."',
            term_sdate = '".$_POST['term_sdate']."',
            term_edate = '".$_POST['term_edate']."',
            editdate = NOW(),
            regdate = NOW()

    ";
    $db->query($sql);
    $co_ix = $db->insert_id();

    $dirpath = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/bienview/";
    $filepath = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/bienview/".$_POST['div_depth']."/";
    $nextpath = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/bienview/".$_POST['div_depth']."/".$co_ix."/";
    $urlpath = $admin_config[mall_data_root]."/images/bienview/".$_POST['div_depth']."/".$co_ix."/";
    if(!is_dir($dirpath)){
        mkdir($dirpath);
        chmod($dirpath,0777);
    }
    if(!is_dir($filepath)){
        mkdir($filepath);
        chmod($filepath,0777);
    }
    if(!is_dir($nextpath)){
        mkdir($nextpath);
        chmod($nextpath,0777);
    }

    if($_FILES['file_name']['size'] > 0) {

        copy($_FILES[file_name][tmp_name], $nextpath.$_FILES['file_name']['name']);
        chmod($nextpath.$_FILES['file_name']['name'], 0777);

        $sql = "update bienview_contents_info set 
                  file_name = '".$_FILES['file_name']['name']."', 
                  file_url = '".$urlpath.$_FILES['file_name']['name']."' 
                 where 
                  co_ix = '".$co_ix."'
                  ";
        $db->query($sql);
    }

    if(is_array($_POST['add_info'])){
        foreach($_POST['add_info'] as $key => $val){
            $sql = "insert 
                    bienview_contents_add_".$_POST['div_depth']." set 
                       co_ix = '".$co_ix."',
                       add_key = '".$key."',
                       add_value = '".$val."' 
               ";
            $db->query($sql);
        }
    }

    echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('콘텐츠 정보가 등록되었습니다.','top_reload');</script>");
    exit;
}

if($act == 'update'){
    $sql = "update 
                bienview_contents_info set
            div_depth = '".$_POST['div_depth']."',
            div_depth_sub = '".$_POST['div_depth_sub']."',
            title = '".$_POST['title']."',
            contents = '".$_POST['contents']."',
            disp = '".$_POST['disp']."',
            term_sdate = '".$_POST['term_sdate']."',
            term_edate = '".$_POST['term_edate']."',
            editdate = NOW()
            where 
                 co_ix = '".$_POST['co_ix']."'
    ";
    $db->query($sql);

    if($_FILES['file_name']['size'] > 0) {
        /**
         * 기존에 존재하는 이미지는 삭제 되어야 하기 때문에 추가
         */
        $sql = "select file_url from bienview_contents_info where co_ix = '".$_POST['co_ix']."'";
        $db->query($sql);
        $db->fetch();
        $file_url = $db->dt[file_url];


        $dirpath = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/bienview/".$_POST['div_depth']."/".$_POST['co_ix']."/";
        $filepath = $_SERVER["DOCUMENT_ROOT"].$file_url;

        if(file_exists($dirpath)){
            unlink($filepath);
        }


        $dirpath = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/bienview/";
        $filepath = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/bienview/".$_POST['div_depth']."/";
        $nextpath = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/bienview/".$_POST['div_depth']."/".$co_ix."/";
        $urlpath = $admin_config[mall_data_root]."/images/bienview/".$_POST['div_depth']."/".$co_ix."/";
        if(!is_dir($dirpath)){
            mkdir($dirpath);
            chmod($dirpath,0777);
        }
        if(!is_dir($filepath)){
            mkdir($filepath);
            chmod($filepath,0777);
        }
        if(!is_dir($nextpath)){
            mkdir($nextpath);
            chmod($nextpath,0777);
        }

        copy($_FILES[file_name][tmp_name], $nextpath.$_FILES['file_name']['name']);
        chmod($nextpath.$_FILES['file_name']['name'], 0777);

        $sql = "update bienview_contents_info set 
                  file_name = '".$_FILES['file_name']['name']."', 
                  file_url = '".$urlpath.$_FILES['file_name']['name']."' 
                 where 
                  co_ix = '".$co_ix."'
                  ";
        $db->query($sql);
    }


    if(is_array($_POST['add_info'])){
        foreach($_POST['add_info'] as $key => $val){

            $sql = "select * from bienview_contents_add_".$_POST['div_depth']." where co_ix = '".$co_ix."' and add_key = '".$key."'  ";
            $db->query($sql);

            if($db->total){
                $sql = "update 
                        bienview_contents_add_".$_POST['div_depth']." set 
                     add_value = '".$val."' 
                       where co_ix = '".$co_ix."' and add_key = '".$key."'
               ";
                $db->query($sql);

            }else {
                $sql = "REPLACE into 
                    bienview_contents_add_" . $_POST['div_depth'] . " set 
                       co_ix = '" . $co_ix . "',
                       add_key = '" . $key . "',
                       add_value = '" . $val . "' 
               ";
                $db->query($sql);
            }
        }
    }

    echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('콘텐츠 정보가 수정되었습니다.','top_reload');</script>");
    exit;

}

if($act == 'img_del'){
    if($path){
        if(file_exists($path)) {
            $result = unlink($path);
        }

        $sql = "update bienview_contents_info set 
              file_name = '', 
              file_url = '' 
             where 
              co_ix = '".$co_ix."'
              ";
        $db->query($sql);

        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('이미지가 삭제 되었습니다.','top_reload');</script>");
        exit;

    }
}

if($act == 'delete'){
    //div_depth

    if($co_ix){
        $sql = "select file_url from bienview_contents_info where co_ix = '".$co_ix."'";
        $db->query($sql);
        $db->fetch();
        $file_url = $db->dt[file_url];


        $dirpath = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/bienview/".$div_depth."/".$co_ix."/";
        $filepath = $_SERVER["DOCUMENT_ROOT"].$file_url;

        if(file_exists($dirpath)){
            unlink($filepath);
        }

        $sql = "delete from bienview_contents_info where co_ix = '".$co_ix."' ";
        $db->query($sql);


        $sql = "SHOW TABLES LIKE 'bienview_contents_add_" . $_GET['div_depth'] . "'";
            $db->query($sql);

            if(is_array($db->fetchall())){
            $sql = "delete from bienview_contents_add_" . $_GET['div_depth'] . " where co_ix = '".$co_ix."'  ";
            $db->query($sql);
        }
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('콘텐츠 정보가 삭제 되었습니다.','top_reload');</script>");
        exit;
    }
}