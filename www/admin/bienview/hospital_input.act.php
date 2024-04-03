<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-07-28
 * Time: 오후 6:33
 */
include $_SERVER['DOCUMENT_ROOT']."/admin/class/layout.class";

if($act == 'insert'){
    $sql = "insert bienview_hospital_info set
                info_type = '".$_POST['info_type']."',
                com_name = '".$_POST['com_name']."',
                com_zip = '".$_POST['com_zip']."',
                com_addr1 = '".$_POST['com_addr1']."',
                com_addr2 = '".$_POST['com_addr2']."',
                homepage = '".$_POST['homepage']."',
                phone = '".$_POST['phone']."',
                x_code = '".$_POST['x_code']."',
                y_code = '".$_POST['y_code']."',
                latitude = '".$_POST['latitude']."',
                longitude = '".$_POST['longitude']."',
                disp = '".$_POST['disp']."',
                editdate = NOW(),
                regdate = NOW()

    ";
    $db->query($sql);

    echo "<script>alert('병원/약국 등록이 완료 되었습니다.');top.opener.location.reload();top.self.close();</script>";
    exit;
}

if($act == 'update'){
    $sql = "update bienview_hospital_info set
                info_type = '".$_POST['info_type']."',
                com_name = '".$_POST['com_name']."',
                com_zip = '".$_POST['com_zip']."',
                com_addr1 = '".$_POST['com_addr1']."',
                com_addr2 = '".$_POST['com_addr2']."',
                homepage = '".$_POST['homepage']."',
                phone = '".$_POST['phone']."',
                x_code = '".$_POST['x_code']."',
                y_code = '".$_POST['y_code']."',
                latitude = '".$_POST['latitude']."',
                longitude = '".$_POST['longitude']."',
                disp = '".$_POST['disp']."',
                editdate = NOW()
            where 
                ho_ix = '".$_POST['ho_ix']."'
    ";
    $db->query($sql);
    echo "<script>alert('병원/약국 수정이 완료 되었습니다.');top.opener.location.reload();top.self.close();</script>";
    exit;
}
if($act == 'delete'){
    $sql = "delete from bienview_hospital_info where ho_ix = '".$_GET['ho_ix']."'";
    $db->query($sql);
    echo "<script>alert('병원/약국 삭제가 완료 되었습니다.');top.location.reload();</script>";
    exit;
}