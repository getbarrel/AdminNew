<?
@set_time_limit(0);
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");

$db = new Database();


/*마일리지 소멸 대상자 찾기*/

//$fp = fopen(LOG_PATH.'point/mileage_extinction.cron_' . date('Ymd') . '.log', 'a');
$fh = chr(13).'---------- START - [' . date('Y-m-d H:i:s') . '] ----------' . chr(13);
//fwrite($fp, $fh);

$sql = "select 
			* 
		from 
			shop_add_mileage 
		where 
			extinction_date = '".date('Y-m-d')."'	
		
		";

$sql = "select 
			* ,
			(am.am_mileage - ifnull((select sum(rm_mileage) from shop_remove_mileage where am_ix = am.am_ix  and rm_state = 1 group by am_ix) ,0)) extinction_mileage
		from 
			shop_add_mileage am 
		where 
			am.extinction_date = '".date('Y-m-d')."'	
		  HAVING  extinction_mileage > 0";
$db->query($sql);
$extinction_data = $db->fetchall();

if(is_array($extinction_data)){
    foreach($extinction_data as $data){
        $sql = "select 
                    ifnull(sum(rm_mileage),0) as remove_mileage
                from 
                    shop_remove_mileage 
                where 
                    am_ix = '".$data[am_ix]."' and uid = '".$data[uid]."'  and rm_state = 1
                group by am_ix
            ";
        $db->query($sql);
        $db->fetch();
        $remove_mileage = $db->dt[remove_mileage];

        $fh = "am_mileage  = ".$data[am_mileage]. chr(13).chr(13);
        $fh .= "remove_mileage  = ".$remove_mileage. chr(13).chr(13);
//        fwrite($fp, $fh);

        if($data[am_mileage] > $remove_mileage){

            $extinction_mileage = $data[am_mileage] - $remove_mileage;

            $sql = "select mileage,mem_type from common_user where code = '".$data[uid]."'";
            $db->query($sql);
            $db->fetch();
            $mileage = $db->dt['mileage'];      //회원의 Total 포인트
            $mem_type = $db->dt['mem_type'];      //회원의 타입 F: 해외, M국내

            $fh = "user_mileage  = ".$mileage. chr(13).chr(13);
            $fh .= "extinction_mileage  = ".$extinction_mileage. chr(13).chr(13);
//            fwrite($fp, $fh);

            if($mem_type == 'F'){
                $message = "Extinction upon expiration of mileage redemption";
            }else{
                $message = "마일리지 사용 만료에 따른 소멸";
            }

            if($mileage >= $extinction_mileage){
                $fh = "final_extinction_mileage  = ".$extinction_mileage. chr(13).chr(13);
                $fh .= "type  = 1". chr(13).chr(13);
//                fwrite($fp, $fh);

                $mileage_data[uid] = $data['uid'];
                $mileage_data[type] = 5;
                $mileage_data[mileage] = $extinction_mileage;
                $mileage_data[message] = $message;
                $mileage_data[state_type] = 'use';
                $mileage_data[save_type] = 'mileage';
                InsertMileageInfo($mileage_data);
            }else{
                //회원 Total포인트가 소멸포인트보다 작을 경우 소멸포인트에서 회원 Total 포인트를 마이너스 시켜준다.
                $extinction_mileage = $extinction_mileage - $mileage;

                $fh = "final_extinction_mileage  = ".$extinction_mileage. chr(13).chr(13);
                $fh .= "type  = 2". chr(13).chr(13);
//                fwrite($fp, $fh);

                $mileage_data[uid] = $data['uid'];
                $mileage_data[type] = 5;
                $mileage_data[mileage] = $extinction_mileage;
                $mileage_data[message] = $message;
                $mileage_data[state_type] = 'use';
                $mileage_data[save_type] = 'mileage';
                InsertMileageInfo($mileage_data);
            }
            //InsertMileageInfo($data[uid],'5',$extinction_mileage,'마일리지 사용 만료에 따른 소멸','use');
        }
    }
}
//fclose($fp);


?>