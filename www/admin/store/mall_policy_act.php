<?
include("../../class/database.class");
$db = new Database;

//print_r($_POST);
//exit;

//약관 수정 & 업데이트
if($mode == "update"){

    //사용유무 해당 코드 전체 변경
    $sql = "UPDATE 
					shop_policy_info
				SET 
					disp = '$disp'
				WHERE
				  pi_code	= '$pi_code'
		";


    $db->query($sql);

    if($pi_code == 'person'){
//        echo 'per';
//        echo $sdate.'---'.$start_date;

        if($sdate == $start_date){
            /*
            for($i=0; $i < count($datas); $i++){
                if($datas[$i][policy_text] != ""){
                  echo  $sql = "UPDATE
									shop_policy_info
								SET
									pi_contents = '".$datas[$i][policy_text]."' , mod_id = '".$admininfo[charger_id]."' , mod_name = '".$admininfo[charger]."' , moddate = now(), link_text='".$link_text."'
								WHERE 
									pi_ix	=	'".$datas[$i][pi_ix]."'
									and pi_type = '".$datas[$i][pi_type]."'
									and startdate = '".$start_date." 00:00:00'
								";
                    $db->query($sql);
                }
            }
            */
            $sql = "UPDATE
									shop_policy_info
								SET
									pi_contents = '".$policy_text."' , mod_id = '".$admininfo[charger_id]."' , mod_name = '".$admininfo[charger]."' , moddate = now(), link_text='".$link_text."'
								WHERE 
									pi_ix	=	'".$pi_ix."'
									-- and pi_type = '".$pi_type."'
									and startdate = '".$start_date." 00:00:00'
								";
            $db->query($sql);

            //exit;

            print("<script type='text/javascript'>alert('수정되었습니다');top.location.href='./mall_policy_list.php?code=".$code."&pi_code=person';</script>");
        }else{

            /*
            for($i=0; $i < count($datas); $i++){
                $sql = "INSERT INTO
								shop_policy_info
							(pi_code,pi_ver,pi_type,pi_contents,contents_type,startdate,disp,reg_id,reg_name,regdate, link_text) 
								VALUES 
							('person','$pi_ver','".$datas[$i][pi_type]."','".$datas[$i][policy_text]."','U','".$start_date." 00:00:00','".$disp."','".$admininfo[charger_id]."','".$admininfo[charger]."',now(), '".$link_text."')
							";
                $db->query($sql);
            }
            */
            $sql = "INSERT INTO
								shop_policy_info
							(pi_code,pi_ver,pi_type,pi_contents,contents_type,startdate,disp,reg_id,reg_name,regdate, link_text) 
								VALUES 
							('person','$pi_ver','".$pi_type."','".$policy_text."','U','".$start_date." 00:00:00','".$disp."','".$admininfo[charger_id]."','".$admininfo[charger]."',now(), '".$link_text."')
							";
            $db->query($sql);

            print("<script type='text/javascript'>alert('등록되었습니다');top.location.href='./mall_policy_list.php?code=".$code."&pi_code=person';</script>");
        }
    }else{
        if($sdate == $start_date){
            if($policy_text != ""){
                $sql = "UPDATE
								shop_policy_info
							SET
								pi_contents = '".$policy_text."' , mod_id = '".$admininfo[charger_id]."' , mod_name = '".$admininfo[charger]."' , moddate = now(), link_text='".$link_text."'
							WHERE 
								pi_ix	=	'".$pi_ix."'
								and startdate = '".$start_date." 00:00:00'
							";
                $db->query($sql);
            }

            print("<script type='text/javascript'>alert('수정되었습니다');top.location.href='./mall_policy_list.php?code=".$code."&pi_code=".$pi_code."';</script>");
        }else{
            $sql = "INSERT INTO
							shop_policy_info
						(pi_code,pi_ver,pi_type,pi_contents,contents_type,startdate,disp,reg_id,reg_name,regdate, link_text) 
							VALUES 
						('".$pi_code."','$pi_ver','".$pi_type."','".$policy_text."','U','".$start_date." 00:00:00','".$disp."','".$admininfo[charger_id]."','".$admininfo[charger]."',now(), '".$link_text."')
						";
            $db->query($sql);

            print("<script type='text/javascript'>alert('등록되었습니다');top.location.href='./mall_policy_list.php?code=".$code."&pi_code=".$pi_code."';</script>");
        }
    }

    exit;
}

//약관삭제
if($mode == "delete"){

    if($pi_code == 'person'){
        $sql = "DELETE 
					FROM
						shop_policy_info
					WHERE
						pi_code = 'person'
						and startdate = '".$before_date." 00:00:00'
					";
        if($db->query($sql)){
            print("<script type='text/javascript'>alert('삭제되었습니다');history.go(-1);</script>");
        }else{
            print("<script type='text/javascript'>alert('문제가 발생하였습니다.\n다시시도해주세요');history.go(-1);</script>");
        }
    }else{
        $sql = "DELETE 
					FROM
						shop_policy_info
					WHERE
						pi_ix = '$pi_ix'
					";
        if($db->query($sql)){
            print("<script type='text/javascript'>alert('삭제되었습니다');history.go(-1);</script>");
        }else{
            print("<script type='text/javascript'>alert('문제가 발생하였습니다.\n다시시도해주세요');history.go(-1);</script>");
        }
    }
}


?>