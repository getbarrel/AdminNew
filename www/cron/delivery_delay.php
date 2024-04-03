<?
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");

	$odb = new MySQL;
	$db = new MySQL;
	$db1 = new MySQL;


	$sql = "select 	*  from  ".TBL_SHOP_ORDER_DETAIL."  where  status in ('IC','DR','DD') and company_id !='' and oid !='' group by company_id";
	//$sql = "select 	*  from  ".TBL_SHOP_ORDER_DETAIL."  where  status in ('IC','DR','DD') and company_id !='' and oid !='' and oid = '20150515120451-84326' group by company_id"; //테스트 하기위해 주문번호 하나 등록
	$odb->query($sql);
	//echo $sql;
	//exit;
	if($odb->total){
		for($j=0; $j<$odb->total; $j++){
			$odb->fetch($j);
			
			$sql = "select 	count(od_ix) ic_cnt  from  ".TBL_SHOP_ORDER_DETAIL."  where company_id='".$odb->dt["company_id"]."' and status ='IC' ";
			$db->query($sql);
			$db->fetch();
			$ic_cnt = $db->dt[ic_cnt]; //신규주문건
//echo $sql;
			$sql = "select 	*  from  ".TBL_SHOP_ORDER_DETAIL."  where company_id='".$odb->dt["company_id"]."' and status in ('IC','DR','DD') ";
			//echo $sql."<br>";
			$db1->query($sql);
			$delay_company_cnt = 0;
			for($z=0; $z < $db1->total; $z++){
				$db1->fetch($z);
				$ic_date = $db1->dt[ic_date];
				$oid = $db1->dt[oid];
				$od_ix = $db1->dt[od_ix];
				

				$startDate = date('Y-m-d',strtotime($ic_date));
				$interval = 2;

				

				$db->query("SELECT * FROM `shop_mall_config` where mall_ix = '".$_SESSION["admininfo"][mall_ix]."' and config_name in('holiday_text')  ");
				if($db->total){
					for($i=0; $i < $db->total;$i++){
					$db->fetch($i);
						$this_holidays = explode(',',$db->dt[config_value]);
					}
				}

				for($i=0; $i < $interval; $i++){
					$date_all[$i] = getdate(strtotime(date("Y-m-d",strtotime($startDate."+".$i."days"))));
					
					if($date_all[$i][wday] == 0 || $date_all[$i][wday] == 6 ){
					 $interval = $interval + 1;
					// echo 1;
					}
				}
				
				for($i=0; $i < $interval; $i++){
					$date_check[$i] = date("Y-m-d",strtotime($startDate."+".$i."days"));
					
					if(in_array($date_check[$i],$this_holidays)){
						$date_all[$i] = (getdate(strtotime($date_check[$i])));
						//echo $date_all[$i][wday];
						if($date_all[$i][wday] != 0 && $date_all[$i][wday] != 6 ){
							$interval = $interval + 1;
							//echo 1;
						}
					}
				}

				$delay_date = date("Y-m-d",strtotime($startDate."+".$interval."days"));
				$delay_date_check = (getdate(strtotime($delay_date)));
				if($delay_date_check[wday] == 0){
					$delay_date = date("Y-m-d",strtotime($delay_date."+1days"));
					$interval = $interval +1;
				}else if($delay_date_check[wday] == 6){
					$delay_date = date("Y-m-d",strtotime($delay_date."+2days"));
					$interval = $interval +2;
				}

				$today = date('Y-m-d H:i:s');
				$sql = "select count(od_ix)delay_cnt from ".TBL_SHOP_ORDER_DETAIL." where DATE_ADD(ic_date,INTERVAL ".$interval." DAY)  <= '".$today."'  and od_ix='".$od_ix."' and status IN ('IC','DR','DD')";
				//echo $sql."<br>";
				
				$db->query($sql);
				$db->fetch();
				$delay_cnt = $db->dt[delay_cnt];
				$delay_company_cnt = $delay_company_cnt + $delay_cnt;
			}
			//exit;
			
			
			//echo  $ic_cnt."|||".$delay_company_cnt."|||".$oid."<br>";
			
			$sql = "SELECT 
					* 
				FROM 
				".TBL_COMMON_COMPANY_DETAIL." as  ccd 
				inner join ".TBL_COMMON_SELLER_DETAIL." as sd on (ccd.company_id = sd.company_id)	
				where ccd.company_id = '".$odb->dt["company_id"]."'";
			$db->query($sql);
			$db->fetch();
			
			if($db->dt['customer_mobile'] != ''){
				$mobile_number = $db->dt['customer_mobile'];
				$mem_name = $db->dt['customer_name'];
			}else{
				$mobile_number = $db->dt['com_mobile'];
				$mem_name = $db->dt['com_ceo'];
			}
			//echo $mobile_number;
			$mobile_number = '01088745709'; //테스트 데이터
			$mem_name = '문정길'; // 테스트 데이터

			$cominfo = getcominfo2();
			//print_r($cominfo);
			//exit;
			$slave_mdb = new Database;
			$slave_mdb->slave_db_setting();

			$sql = "select * from ".TBL_SHOP_MAILSEND_CONFIG." where mc_code = 'delivery_delay_sms' ";
			$slave_mdb->query($sql);

			if($slave_mdb->total){
				
				$email_info = $slave_mdb->fetch();
				
				if($email_info[mc_sms_usersend_yn] == "Y"){
					include_once ($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
					$s = new SMS();
					$s->send_phone = $cominfo[com_phone];
					$s->send_name = $cominfo[com_name];


					$mc_sms_text = str_replace("{ic_cnt}",$ic_cnt,$email_info[mc_sms_text]);//추가 kbk 13/03/14
					$mc_sms_text = str_replace("{dr_cnt}",$delay_company_cnt,$mc_sms_text);//추가 kbk 13/03/14
					
					//echo $mc_sms_text;
					//exit;
					$s->msg_code	=	'0801'; //sms발송 코드
					$s->dest_phone = str_replace("-","",$mobile_number);
					$s->dest_name = $mem_name;
					$s->msg_body =$mc_sms_text;
					$s->sendbyone($cominfo);
				}
			}
		}
	}

?>