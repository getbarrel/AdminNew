<?php
    include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
    include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");

    $db = new Database;
    $mdb = new Database;

    if($act == 'update') {
        if($gp_ix != 1) {
            //단체일 경우 추가처리
            $set = "";
            //지도자 파일업로드
            if(!empty($_FILES['master_image_file']['name'][0])){
                $file = champFileUpload($cm_ix, $_FILES, 'G');
                $set .= ", group_master_image_url ='".$file['origin']."' ";
                $set .= ", group_master_image_url_path ='".$file['new']."' ";
            }

            //그룹정보 업데이트 먼저
            $handphone = implode('-',$handphone);
            $email = $email1.'@'.$email2;
            $attend_event = implode(',',$attend);

            if($modify_pw == '1'){
                $new_password = hash('sha256', md5($password));
                $password_modify = "password = '$new_password', ";
            }
            $sql = "update championship_group 
                    set 
                        group_name = '$group_name', 
                        group_master = HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')),
                        postnum = '$postnum', 
                        address1 = HEX(AES_ENCRYPT('$address1','".$db->ase_encrypt_key."')),
                        address2 = HEX(AES_ENCRYPT('$address2','".$db->ase_encrypt_key."')),
                        email = HEX(AES_ENCRYPT('$email','".$db->ase_encrypt_key."')),
                        handphone = HEX(AES_ENCRYPT('$handphone','".$db->ase_encrypt_key."')),
                        depositor = HEX(AES_ENCRYPT('$depositor','".$db->ase_encrypt_key."')),
                        attend_event = '$attend_event', 
                        $password_modify
                        editdate = NOW() 
                        $set
                    where gp_ix= $gp_ix";
            $db->query($sql);

            //그룹에 속한 멤버 업데이트
            for($i=0; $i<$member_cnt; $i++) {
                $set = "";
                //그룹 멤버 파일업로드
                if(!empty($_FILES['image_file']['name'][$i])){
                    $file = champFileUpload($g_cm_ix[$i], $_FILES, '', $i);
                    $set .= ", image_url='".$file['origin']."' ";
                    $set .= ", image_url_path='".$file['new']."' ";
                }

                $g_handphone = $g_handphone1[$i].'-'.$g_handphone2[$i].'-'.$g_handphone3[$i];
                $g_email = $g_email1[$i].'@'.$g_email2[$i];

                $sql = "update championship_member 
                        set 
                          name = HEX(AES_ENCRYPT('".$g_name[$i]."','".$db->ase_encrypt_key."')),                         
                          sex= '".$g_sex[$i]."', 
                          birthday = HEX(AES_ENCRYPT('".$g_birthday[$i]."','".$db->ase_encrypt_key."')),
                          size = '".$g_size[$i]."', 
                          handphone = HEX(AES_ENCRYPT('".$g_handphone."','".$db->ase_encrypt_key."')),
                          email = HEX(AES_ENCRYPT('".$g_email."','".$db->ase_encrypt_key."')),
                          attend_group = '".$g_attend_group[$i]."', 
                          attend_event1 = '".$g_attend_event1[$i]."', 
                          attend_event2 = '".$g_attend_event2[$i]."', 
                          editdate = NOW() 
                          $set 
                        where 
                            cm_ix = '".$g_cm_ix[$i]."'";
                $db->query($sql);
            }

        }else {

            $set = '';
            //파일업로드
            if(!empty($_FILES['image_file']['name'][0])){
                $file_name = champFileUpload($cm_ix, $_FILES);
                $set .= ", image_url='".$file_name."' ";
            }

            $handphone = implode('-',$handphone);
            $email = $email1.'@'.$email2;

            if($modify_pw == '1'){
                $new_password = hash('sha256', md5($password));
                $password_modify = "password = '$new_password', ";
            }

            $sql = "update championship_member 
                    set 
                        name = HEX(AES_ENCRYPT('".$name."','".$db->ase_encrypt_key."')),  
                        sex='$sex', 
                        birthday = HEX(AES_ENCRYPT('".$birthday."','".$db->ase_encrypt_key."')),  
                        handphone = HEX(AES_ENCRYPT('".$handphone."','".$db->ase_encrypt_key."')),  
                        email = HEX(AES_ENCRYPT('".$email."','".$db->ase_encrypt_key."')),  
                        size='$size', 
                        postnum='$postnum',                         
                        address1 = HEX(AES_ENCRYPT('".$address1."','".$db->ase_encrypt_key."')),                          
                        address2 = HEX(AES_ENCRYPT('".$address2."','".$db->ase_encrypt_key."')),  
                        class_name='$class_name',
                        attend_group='$attend_group',
                        attend_event1='$attend_event1',
                        attend_event2='$attend_event2',             
                        $password_modify          
                        depositor = HEX(AES_ENCRYPT('".$depositor."','".$db->ase_encrypt_key."'))
                        $set
                    where cm_ix = '$cm_ix'";

            $db->query($sql);

        }

        echo '<script>alert("수정 완료되었습니다.");location.href="/admin/display/championship.detail.php?gp_ix='.$gp_ix.'&cm_ix='.$cm_ix.'";</script>';
    }else if($act == 'delete_group_member') {

        $sql = "select member_cnt, gp_ix from championship_group where gp_ix = (select gp_ix from championship_member where cm_ix = '$cm_ix')";
        $mdb->query($sql);
        $mdb->fetch();
        $member_cnt = $mdb->dt['member_cnt'];
        $gp_ix = $mdb->dt['gp_ix'];


        $sql = "update championship_group set member_cnt = $member_cnt - 1 where gp_ix = $gp_ix;";
        $result = $db->query($sql);

        if($result) {
            $sql = "delete from championship_member where cm_ix = '$cm_ix'";
            $db->query($sql);

        }else {
            $result = false;
        }
        echo json_encode($result);

    }else if($act == 'delete_file') {

        if($type == 'master') {
            $sql = "update championship_group set group_master_image_url = '', group_master_image_url_path = '' where gp_ix = $gp_ix";
            $result = $db->query($sql);
        }else {
            $sql = "update championship_member set image_url = '', image_url_path = '' where cm_ix = $cm_ix";
            $result = $db->query($sql);
        }

        echo json_encode($result);
    }else if ($act == 'delete_member') {

        if($type == 'I') {
            //개인
            $sql = "delete from championship_member where cm_ix = $id";
            $result = $db->query($sql);

        }else {
            //그룹
            $sql = "delete from championship_group where gp_ix = $id";
            $result = $db->query($sql);

            if($result) {
                $sql = "delete from championship_member where gp_ix = $id";
                $result = $db->query($sql);
            }
        }
        echo json_encode($result);

    }else if ($act == 'excel_down') {
        include '../include/phpexcel/Classes/PHPExcel.php';

        $uploads_dir = '/data/barrel_data/championship';
        $url = 'http://'.$_SERVER['SERVER_NAME'].$uploads_dir;

        $obj = new PHPExcel();

        // 속성 정의
        $obj->getProperties()->setCreator("포비즈 코리아")
            ->setLastModifiedBy("Barrel")
            ->setTitle("스프린트챔피언십")
            ->setSubject("스프린트챔피언십")
            ->setDescription("generated by forbiz korea")
            ->setKeywords("Barrel")
            ->setCategory("Barrel");

        $obj->setActiveSheetIndex(0);

        if($type == 'I') {
            //개인 다운로드
            $sql = "select 
                      cm.cm_ix, cm.sex, cm.attend_div,cm.gp_ix,cm.postnum, cm.size,cm.class_name,cm.attend_group,cm.attend_event1,cm.attend_event2,
                    cm.image_url,cm.image_url_path,cm.regdate,cm.editdate,
                    AES_DECRYPT(UNHEX(cm.name),'".$db->ase_encrypt_key."') as name,
                    AES_DECRYPT(UNHEX(cm.birthday),'".$db->ase_encrypt_key."') as birthday,
                    AES_DECRYPT(UNHEX(cm.handphone),'".$db->ase_encrypt_key."') as handphone,
                    AES_DECRYPT(UNHEX(cm.email),'".$db->ase_encrypt_key."') as email,
                    AES_DECRYPT(UNHEX(cm.address1),'".$db->ase_encrypt_key."') as address1,
                    AES_DECRYPT(UNHEX(cm.address2),'".$db->ase_encrypt_key."') as address2,
                    AES_DECRYPT(UNHEX(cm.depositor),'".$db->ase_encrypt_key."') as depositor 
                    from championship_member cm where cm.gp_ix = 1 order by cm.cm_ix desc";
            $db->query($sql);
            $datas = $db->fetchall();
            $total = $db->total;

            $obj->getActiveSheet()->setTitle('챔피언십_개인');

            $col_info = array("이미지", "종목구분", "이름(실명)", "성별", "생년월일", "핸드폰번호", "이메일주소", "티셔츠사이즈", "수령주소", "소속명", "참가그룹", "참가종목1", "참가종목2", "입금자명");

            $col = 'A';
            foreach ($col_info as $ci) {
                $obj->getActiveSheet()->setCellValue($col . "1", $ci);
                $obj->getActiveSheet()->getStyle($col."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('8FBC8F');
                $col++;
            }

            $z = 2;
            for($i=0; $i<$total; $i++) {
                $data = $datas[$i];

                $address = "(".$data['postnum'].") ".$data['address1'].' '.$data['address2'];

                $sql = "select option_value from championship_options where option_key = 'group' and co_ix = '".$data['attend_group']."'";
                $db->query($sql);
                $db->fetch();
                $attend_group = $db->dt['option_value'];

                $sql = "select option_value from championship_options where option_key = 'event' and co_ix in ('".$data['attend_event1']."', '".$data['attend_event2']."') order by field( co_ix, '".$data['attend_event1']."', '".$data['attend_event2']."')";
                $db->query($sql);
                $attend_event = $db->fetchall();

                $col = 'A';
                $obj->getActiveSheet()->setCellValue($col . $z, $url.'/'.$data['image_url_path']);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, "개인");
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $data['name']);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, ($data['sex'] == 'M'?'남자':'여자'));
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $data['birthday']);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $data['handphone']);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $data['email']);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $data['size']);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $address);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $data['class_name']);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $attend_group);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $attend_event[0]['option_value']);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $attend_event[1]['option_value']);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $data['depositor']);
                $col++;
                $z++;
            }

            $col = 'A';
            foreach ($col_info as $ci) {
                $obj->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
                $col++;
            }


            header('Content-Disposition: attachment;filename=sprint_championship.xls');

        }else {
            //그룹 다운로드
            $sql = "select 
                      cg.group_name,
                AES_DECRYPT(UNHEX(cg.group_master),'".$db->ase_encrypt_key."') as group_master,
                AES_DECRYPT(UNHEX(cg.handphone),'".$db->ase_encrypt_key."') as handphone,
                AES_DECRYPT(UNHEX(cg.email),'".$db->ase_encrypt_key."') as email,
                cg.group_master_image_url,cg.regdate as group_regdate, cg.postnum as postnum,
                AES_DECRYPT(UNHEX(cg.address1),'".$db->ase_encrypt_key."') as address1,
                AES_DECRYPT(UNHEX(cg.address2),'".$db->ase_encrypt_key."') as address2,
                 cg.member_cnt, cg.attend_event,
                  AES_DECRYPT(UNHEX(cg.depositor),'".$db->ase_encrypt_key."') as depositor,
                  group_master_image_url_path, group_master_image_url
                    from championship_group cg where cg.gp_ix = '".$gp_ix."' ";
            $db->query($sql);
            $group = $db->fetch();
            $total = $db->total;

            $obj->getActiveSheet()->setTitle('챔피언십_단체');

            $col_info = array("감독자 이미지", "종목구분", "단체명", "감독자(대표자)", "감독자 핸드폰번호", "감독자 이메일주소", "수령주소", "단체전 참가여부", "선수 인원수", "참가비", "입금자명");

            $col = 'A';
            foreach ($col_info as $ci) {
                $obj->getActiveSheet()->setCellValue($col . "1", $ci);
                $obj->getActiveSheet()->getStyle($col."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('8FBC8F');
                $col++;
            }

            $address = "(".$group['postnum'].") ".$group['address1'].' '.$group['address2'];

            $awhere = "";
            $orderby = "";
            if(!empty($group['attend_event'])) {
                $awhere = " and co_ix in (".$group['attend_event'].") ";
                $orderby = " order by field( co_ix, ".$group['attend_event'].") ";
                $attend_event_count = count(explode(',',$group['attend_event']));
            }else{
                $attend_event_count = 0;
            }

            $sql = "select group_concat(option_value) as attend_event, count(option_value) as total_cnt from championship_options where option_key = 'attend' $awhere $orderby ";
            $db->query($sql);
            $db->fetch();
            $attend_event = $db->dt['attend_event'];
            $attend_cnt = $db->dt['total_cnt'];

            $sql = "select 
                        cm.cm_ix, cm.sex, cm.attend_div,cm.gp_ix,cm.postnum, cm.size,cm.class_name,cm.attend_group,cm.attend_event1,cm.attend_event2,
                    cm.image_url,cm.image_url_path,cm.regdate,cm.editdate,
                    AES_DECRYPT(UNHEX(cm.name),'".$db->ase_encrypt_key."') as name,
                    AES_DECRYPT(UNHEX(cm.birthday),'".$db->ase_encrypt_key."') as birthday,
                    AES_DECRYPT(UNHEX(cm.handphone),'".$db->ase_encrypt_key."') as handphone,
                    AES_DECRYPT(UNHEX(cm.email),'".$db->ase_encrypt_key."') as email,
                    AES_DECRYPT(UNHEX(cm.address1),'".$db->ase_encrypt_key."') as address1,
                    AES_DECRYPT(UNHEX(cm.address2),'".$db->ase_encrypt_key."') as address2,
                    AES_DECRYPT(UNHEX(cm.depositor),'".$db->ase_encrypt_key."') as depositor  
                    from championship_member cm where cm.gp_ix = '".$gp_ix."' ";
            $db->query($sql);
            $datas = $db->fetchall();
            $total = $db->total;

            $pay = ((20000 * $attend_event_count) + (40000 * $total));

            $col = 'A';
            $obj->getActiveSheet()->setCellValue($col . '2', $url.'/'.$group['group_master_image_url_path']);
            $col++;
            $obj->getActiveSheet()->setCellValue($col . '2', '단체');
            $col++;
            $obj->getActiveSheet()->setCellValue($col . '2', $group['group_name']);
            $col++;
            $obj->getActiveSheet()->setCellValue($col . '2', $group['group_master']);
            $col++;
            $obj->getActiveSheet()->setCellValue($col . '2', $group['handphone']);
            $col++;
            $obj->getActiveSheet()->setCellValue($col . '2', $group['email']);
            $col++;
            $obj->getActiveSheet()->setCellValue($col . '2', $address);
            $col++;
            $obj->getActiveSheet()->setCellValue($col . '2', $attend_event);
            $col++;
            $obj->getActiveSheet()->setCellValue($col . '2', $group['member_cnt']);
            $col++;
            $obj->getActiveSheet()->setCellValue($col . '2', $pay);
            $col++;
            $obj->getActiveSheet()->setCellValue($col . '2', $group['depositor']);
            $col++;

            $sub_col_info = array("번호", "그룹", "이름(실명)", "성별", "생년월일", "핸드폰번호", "참가그룹", "참가종목1", "참가종목2", "티셔츠사이즈", "이미지경로");

            $col = 'A';
            foreach ($sub_col_info as $sci) {
                $obj->getActiveSheet()->setCellValue($col . "5", $sci);
                $obj->getActiveSheet()->getStyle($col."5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('8FBC8F');
                $col++;
            }



            $z = 6;
            for($i=0; $i<$total; $i++) {
                $data = $datas[$i];

                $sql = "select option_value from championship_options where option_key = 'group' and co_ix = '".$data['attend_group']."'";
                $db->query($sql);
                $db->fetch();
                $attend_group = $db->dt['option_value'];

                $sql = "select option_value from championship_options where option_key = 'event' and co_ix in ('".$data['attend_event1']."', '".$data['attend_event2']."') order by field( co_ix, '".$data['attend_event1']."', '".$data['attend_event2']."')";
                $db->query($sql);
                $attend_event = $db->fetchall();

                $col = 'A';
                $obj->getActiveSheet()->setCellValue($col . $z, ($i+1));
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $group['group_name']);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $data['name']);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, ($data['sex'] == 'M'?'남자':'여자'));
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $data['birthday']);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $data['handphone']);
                $col++;
//                $obj->getActiveSheet()->setCellValue($col . $z, $data['email']);
//                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $attend_group);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $attend_event[0]['option_value']);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $attend_event[1]['option_value']);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $data['size']);
                $col++;
                $obj->getActiveSheet()->setCellValue($col . $z, $url.'/'.$data['image_url_path']);
                $col++;
                $z++;
            }

            $col = 'A';
            foreach ($col_info as $ci) {
                $obj->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
                $col++;
            }

            header('Content-Disposition: attachment;filename=sprint_championship_group.xls');
        }


        header('Content-Type: application/vnd.ms-excel');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel5');
        $objWriter->save('php://output');

        exit;
    }

    function champFileUpload($cm_ix, $file, $type='', $index='0') {

        $uploads_dir = '/data/barrel_data/championship';
        $allowed_ext = array('jpg','jpeg','png','JPG','JPEG','PNG');

        $target = "image_file";

        if($type == 'G'){
            $target = "master_image_file";
        }

        $returnFile = array();
        $error = $file[$target]['error'][$index];
        $name = $file[$target]['name'][$index];
        $size = $file[$target]['size'][$index];

        $ext = array_pop(explode('.', $name));

        $newName = md5($name.microtime()).'.'.$ext;
        $msg = "";

        $returnFile['origin'] = $name;
        $returnFile['new'] = $newName;

        if( $error != UPLOAD_ERR_OK ) {
            // 오류 확인
            switch( $error ) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $msg = "파일이 너무 큽니다. ($error)";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $msg = "파일이 첨부되지 않았습니다. ($error)";
                    break;
                default:
                    $msg = "파일이 제대로 업로드되지 않았습니다. ($error)";
            }
        } else if($size > 2097152) {
            $msg = "파일은 2MB이하로 등록 가능합니다.";
        } else if( !in_array($ext, $allowed_ext) ) {
            $msg = "허용되지 않는 확장자입니다.";
        }

        if(!empty($msg)) {
            echo '<script>alert("'.$msg.'");history.back();</script>';
        }

        $full_path = $uploads_dir."/".$newName;

        move_uploaded_file( $file[$target]['tmp_name'][$index], $full_path);
        // 파일 이동
        return $returnFile;

    }
