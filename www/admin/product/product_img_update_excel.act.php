<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-11-20
 * Time: 오후 1:39
 */
include($_SERVER["DOCUMENT_ROOT"] . "/admin/class/layout.class");
include '../include/phpexcel/Classes/PHPExcel.php';

set_time_limit(9999999999);
ini_set('memory_limit', -1);

$goods_basic_sample[] = array("code"=>"pid","title"=>"상품코드","desc"=>"","type"=>"","comment"=>"상품시스템코드","validation"=>"true","sample"=>"");
$goods_basic_sample[] = array("code"=>"b_img","title"=>"확대이미지","desc"=>"","type"=>"","comment"=>"1130*1600 확대이미지","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"m_img","title"=>"상세이미지","desc"=>"","type"=>"","comment"=>"520*736 상세이미지","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"ms_img","title"=>"리스트이미지","desc"=>"","type"=>"","comment"=>"273*387 리스트이미지","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"s_img","title"=>"리스트작은이미지","desc"=>"","type"=>"","comment"=>"160*226 리스트작은이미지","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"c_img","title"=>"썸네일이미지","desc"=>"","type"=>"","comment"=>"90*130 썸네일이미지","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"filter_img","title"=>"패턴 이미지","desc"=>"","type"=>"","comment"=>"75*45 패턴 이미지","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"add_b_img","title"=>"추가이미지1","desc"=>"","type"=>"","comment"=>"1130*1600 추가이미지1","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"add_m_img","title"=>"추가이미지1","desc"=>"","type"=>"","comment"=>"520*736 추가이미지1","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"add_c_img","title"=>"추가이미지1","desc"=>"","type"=>"","comment"=>"90*130 추가이미지1","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"add_b_img2","title"=>"추가이미지2","desc"=>"","type"=>"","comment"=>"1130*1600 추가이미지2","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"add_m_img2","title"=>"추가이미지2","desc"=>"","type"=>"","comment"=>"520*736 추가이미지2","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"add_c_img2","title"=>"추가이미지2","desc"=>"","type"=>"","comment"=>"90*130 추가이미지2","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"add_b_img3","title"=>"추가이미지3","desc"=>"","type"=>"","comment"=>"1130*1600 추가이미지3","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"add_m_img3","title"=>"추가이미지3","desc"=>"","type"=>"","comment"=>"520*736 추가이미지3","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"add_c_img3","title"=>"추가이미지3","desc"=>"","type"=>"","comment"=>"90*130 추가이미지3","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"add_b_img4","title"=>"추가이미지4","desc"=>"","type"=>"","comment"=>"1130*1600 추가이미지4","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"add_m_img4","title"=>"추가이미지4","desc"=>"","type"=>"","comment"=>"520*736 추가이미지4","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"add_c_img4","title"=>"추가이미지4","desc"=>"","type"=>"","comment"=>"90*130 추가이미지4","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"add_b_img5","title"=>"추가이미지5","desc"=>"","type"=>"","comment"=>"1130*1600 추가이미지5","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"add_m_img5","title"=>"추가이미지5","desc"=>"","type"=>"","comment"=>"520*736 추가이미지5","validation"=>"false","sample"=>"");
$goods_basic_sample[] = array("code"=>"add_c_img5","title"=>"추가이미지5","desc"=>"","type"=>"","comment"=>"90*130 추가이미지5","validation"=>"false","sample"=>"");


if ($_SESSION['admininfo']['admin_id'] == "") {
    echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert('관리자 로그인후 사용하실수 있습니다.');location.href='../'</script>";
    exit;
}

$db = new Database;
$image_db = new Database;

if ($act == "new_excel_input") {    //엑셀정보 저장

    include("../include/lib/pclzip.lib.php");

    PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

    date_default_timezone_set('Asia/Seoul');

    if ($excel_file_size > 0) {
        copy($excel_file, $_SERVER["DOCUMENT_ROOT"] . "" . $admin_config[mall_data_root] . "/images/upfile/" . $excel_file_name);
    }

    $objPHPExcel = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"] . "" . $admin_config[mall_data_root] . "/images/upfile/" . $excel_file_name);

    $shift_num = 0;


    include("../logstory/class/sharedmemory.class");
    $shmop = new Shared("upload_excel_img_data_" . $_SESSION["admininfo"]["charger_ix"]);
    $shmop->filepath = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admininfo"]["mall_data_root"] . "/_shared/";
    //echo $shmop->filepath;
    $shmop->SetFilePath();
    $upload_excel_data = $shmop->getObjectForKey("upload_excel_img_data_" . $_SESSION["admininfo"]["charger_ix"]);

    $upload_excel_data = "";


    $col = 'A';



    foreach ($goods_basic_sample as $key => $value) {
        $upload_excel_data[session_id()][0][$value[code]] = $value[title];
        $col++;
    }

    $z = 0;

    // 데이터는 3줄부터 시작
    for ($rownum = 3; $rownum <= 30000; $rownum++) {

        $pcode = $objPHPExcel->getActiveSheet()->getCell('I' . $rownum)->getValue();

        if ($objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue() != "" || $objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue() == "0") {

            //continue;

            $col = 'A';
            foreach ($goods_basic_sample as $key => $value) {

                // PHPExcel_RichText
                if (is_object($objPHPExcel->getActiveSheet()->getCell($col . $rownum)->getValue())) {
                    $objRichText = new PHPExcel_RichText($objPHPExcel->getActiveSheet()->getCell($col . $rownum));
                    $upload_excel_data[session_id()][$z + 1][$value[code]] = $objRichText->getPlainText();
                } else {
                    $upload_excel_data[session_id()][$z + 1][$value[code]] = $objPHPExcel->getActiveSheet()->getCell($col . $rownum)->getValue();
                }
                $col++;
            }

            $upload_excel_data[session_id()][$z + 1][p_no] = $z;

        }
        $z++;
    }

    $shmop->setObjectForKey($upload_excel_data, "upload_excel_img_data_" . $_SESSION["admininfo"]["charger_ix"]);
    if ($page_type == 'input') {
        echo "<script language='javascript' src='../js/message.js.php'></script><script>top.location.href='./product_img_update_excel.php'</script>";
    } else {
        echo "<script language='javascript' src='../js/message.js.php'></script><script>top.location.href='./product_img_update_excel.php'</script>";
    }
    exit;
}

if ($act == "single_goods_reg" && strlen($p_no) > 0) {

//    if ($page_type == 'update') {
//        $check_array = $_REQUEST[check_data];
//        if (is_array($check_array) && count($check_array)) {
//
//            for ($i = 0; $i < count($check_array); $i++) {
//                $check_infos[$check_array[$i][name]] = $check_array[$i][value];
//            }
//
//        } else {
//            //체크버튼이 없을경우 처리하지 않음
//            $insert_yn = false;
//            //$result_massage .= "<span class='red'>상품시스템코드 체크는 필수 입니다. </span><br/>";
//            return false;
//        }
//    }

    include("../logstory/class/sharedmemory.class");

    $shmop = new Shared("upload_excel_img_data_" . $_SESSION["admininfo"]["charger_ix"]);
    //	$shmop->clear();
    $shmop->filepath = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admininfo"]["mall_data_root"] . "/_shared/";
    $shmop->SetFilePath();
    $upload_excel_data = $shmop->getObjectForKey("upload_excel_img_data_" . $_SESSION["admininfo"]["charger_ix"]);

    $select_excel_goods_infos = filter_by_value($upload_excel_data[session_id()], 'p_no', $p_no);

    foreach ($select_excel_goods_infos as $key => $value) {

        //이미지 등록 사용할 pid 획득
        $pid = trim($value['pid']);

        //상품코드가 존재하지 않을 경우 스킵
        if(empty($pid)){
            echo "<span class='red'>상품시스템코드 체크는 필수 입니다. </span><br/>";
            continue;
        }

        //상품코드를 기준으로 업로드 구조 획득
        $uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product", $pid, 'Y');


        foreach ($value as $_key => $_value) {

            //상품코드 항목 또는 빈 항목일 경우 스킵
            if($_key == 'pid' || empty($_value)){
                continue;
            }
            $path = $_SERVER["DOCUMENT_ROOT"]."".$_SESSION['admininfo']['mall_data_root']."/BatchModifyImages/upload/";
            $imagePath = $path.$_value;

            //상품이미지가 업로드 폴더에 존재하지 않을 경우 스킵
            if(!file_exists($imagePath)){
                continue;
            }

            $fp = fopen($imagePath, "r");
            $image_stream = fread($fp, 64);

            if ( preg_match( '/^\x89PNG\x0d\x0a\x1a\x0a/', $image_stream) )  {
                $type = "png";
            } elseif ( preg_match( '/^GIF8[79]a/', $image_stream) )  {
                $type = "gif";
            } elseif ( preg_match( '/^\xff\xd8/', $image_stream) )  {
                $type = "jpg";
            }else{
                $type = false;
            }

            //업로드 파일이 이미지가 아닐 경우 스킵
            if($type == false){
                continue;
            }

//            $image_info = getimagesize($imagePath);
//            echo $imagePath;
//            print_r($image_info);
//            exit;

            $file_name = "";
            switch($_key){
                case 'b_img':
                    $file_name = "b_" . $pid;
                    break;
                case 'm_img':
                    $file_name = "m_" . $pid;
                    break;
                case 'ms_img':
                    $file_name = "ms_" . $pid;
                    break;
                case 's_img':
                    $file_name = "s_" . $pid;
                    break;
                case 'c_img':
                    $file_name = "c_" . $pid;
                    break;
                case 'filter_img':
                    $file_name = "filter_" . $pid;
                    break;
            }

            if($file_name){
                copy($imagePath, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/".$file_name.".gif");
            }


            //추가이미지일때
            if(strpos($_key,'add_') !== false){
                $add_file_limit = "";
                $add_file_first_name = "";
                switch($_key){
                    case 'add_b_img':
                        $add_file_limit =" limit 0,1";
                        $add_file_first_name = "b";
                        break;
                    case 'add_m_img':
                        $add_file_limit =" limit 0,1";
                        $add_file_first_name = "m";
                        break;
                    case 'add_c_img':
                        $add_file_limit =" limit 0,1";
                        $add_file_first_name = "c";
                        break;
                    case 'add_b_img2':
                        $add_file_limit =" limit 1,1";
                        $add_file_first_name = "b";
                        break;
                    case 'add_m_img2':
                        $add_file_limit =" limit 1,1";
                        $add_file_first_name = "m";
                        break;
                    case 'add_c_img2':
                        $add_file_limit =" limit 1,1";
                        $add_file_first_name = "c";
                        break;
                    case 'add_b_img3':
                        $add_file_limit =" limit 2,1";
                        $add_file_first_name = "b";
                        break;
                    case 'add_m_img3':
                        $add_file_limit =" limit 2,1";
                        $add_file_first_name = "m";
                        break;
                    case 'add_c_img3':
                        $add_file_limit =" limit 2,1";
                        $add_file_first_name = "c";
                        break;
                    case 'add_b_img4':
                        $add_file_limit =" limit 3,1";
                        $add_file_first_name = "b";
                        break;
                    case 'add_m_img4':
                        $add_file_limit =" limit 3,1";
                        $add_file_first_name = "m";
                        break;
                    case 'add_c_img4':
                        $add_file_limit =" limit 3,1";
                        $add_file_first_name = "c";
                        break;
                    case 'add_b_img5':
                        $add_file_limit =" limit 4,1";
                        $add_file_first_name = "b";
                        break;
                    case 'add_m_img5':
                        $add_file_limit =" limit 4,1";
                        $add_file_first_name = "m";
                        break;
                    case 'add_c_img5':
                        $add_file_limit =" limit 4,1";
                        $add_file_first_name = "c";
                        break;
                }
            }

            $sql = "select * from ".TBL_SHOP_ADDIMAGE." where pid = '".$pid."' order by id asc $add_file_limit ";
            $db->query($sql);
            if($db->total){
                $db->fetch();
                $add_img_id = $db->dt['id'];
            }else{
                $sql = "INSERT INTO " . TBL_SHOP_ADDIMAGE . " ( pid,  regdate) values('$pid',NOW()) ";
                $db->query($sql);

                $db->query("SELECT id FROM " . TBL_SHOP_ADDIMAGE . " WHERE id=LAST_INSERT_ID()");
                $db->fetch();
                $add_img_id = $db->dt[id];
            }

            if($add_file_first_name){
                copy($imagePath, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/addimg" . $uploaddir . "/".$add_file_first_name."_".$add_img_id."_add.gif");
            }
        }

//        $upload_excel_data[session_id()][$key][status] = "C";
//        $upload_excel_data[session_id()][$key][status_message] = "등록완료";
        echo "<span class='blue'>등록완료</span>";

    }

    $shmop->setObjectForKey($upload_excel_data, "upload_excel_img_data_" . $_SESSION["admininfo"]["charger_ix"]);
    exit;
}
