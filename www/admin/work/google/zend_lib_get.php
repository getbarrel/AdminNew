<?php

function GoogleSync($google_mail, $google_pass, $chargerinfo){
	//global $chargerinfo;
    // load library
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	ini_set('include_path', ".:/usr/local/lib/php:".$_SERVER["DOCUMENT_ROOT"]."/include/pear:".$_SERVER["DOCUMENT_ROOT"]."/admin/work/");
    require_once 'Zend/Loader.php';

	session_start();
    Zend_Loader::loadClass('Zend_Gdata');
    Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
    Zend_Loader::loadClass('Zend_Gdata_Calendar');
    Zend_Loader::loadClass('Zend_Http_Client');
    
	$db = new Database;

    // 캘린더 서비스를 위해 인증된 HTTP 클라이언트 생성
    $gcal = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
    $user = $google_mail;
    $pass = $google_pass;
    $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $gcal);
	if($client){
		$gcal = new Zend_Gdata_Calendar($client);
		
		// 이벤트 목록을 얻기 위해 질의 생성
		$query = $gcal->newEventQuery();
		$query->setUser('default');
		$query->setVisibility('private');
		$query->setProjection('full');
		$query->setMaxResults(50);

		$start_date = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-30, date("Y")));
		$end_date = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+30, date("Y")));

		$query->setStartMin($start_date);
		$query->setStartMax($end_date);
		$query->setSingleEvents(false);
		$query->setOrderby('starttime');
		$query->setSortOrder('descending');//ascending

		
		// 캘린더 피드 획득과 해석
		// 결과 출력
		try {
		  $feed = $gcal->getCalendarEventFeed($query);
		} catch (Zend_Gdata_App_Exception $e) {
		  echo "Error: " . $e->getResponse();
		}
	   
		foreach ($feed as $event) {
			//print_r($event);
			//exit;
			
	/*
			echo "<li>\n";
			echo "<h2>" . stripslashes($event->title) . "</h2>\n";
			echo "summary : ".stripslashes($event->summary) . " <br/>\n";
			echo "event_id : ".stripslashes($event->id) . "  <br/>\n";
			echo "published : ".stripslashes($event->published) . " :::".date("Ymd",$event->published)."<br/>\n";
			echo "updated : ".stripslashes($event->updated) . " <br/>\n";
			echo "content : ".stripslashes($event->content) . " <br/>\n";
			echo "link : ".stripslashes($event->link) . " <br/>\n";

			//$when = $event->when();
			//print_r($when);
			echo "startTime : ".$event->when[0]->startTime . '<br />';
			echo "endTime : ".$event->when[0]->endTime . '<br />';
			echo "where : ". $event->where[0] . '<br />';



			foreach ($event->author as $author) {
			echo "name : ".stripslashes($author->name) . " <br/>\n";
			echo "email : ".stripslashes($author->email) . " <br/>\n";
			}
			
			foreach ($event->when as $when) {
			echo "starttime : ".stripslashes($when->starttime) . " <br/>\n";
			echo "endTime : ".stripslashes($when->endTime) . " <br/>\n";
			}
			*/
			//echo "</li>\n";
			//exit;
			//echo strlen($event->id)."::".$event->id."<br>";
			$google_event_id = stripslashes($event->id);
			$updated = stripslashes($event->updated);
			$company_id = $chargerinfo['company_id'];
			$charger_ix = $chargerinfo[charger_ix];
			$reg_name = $chargerinfo[charger];;
			$event_id = stripslashes($event->id);
			$work_title = stripslashes($event->title);
			$work_detail = stripslashes($event->content);
			$work_where = stripslashes($event->where);

			$ep = $event->extendedProperty[0];
			if(is_object($ep)){
				//echo "takeAttributeFromDOM : ".$ep->takeAttributeFromDOM("sync_yn")."<br>";
				//echo "getName : ".$ep->getName()."<br>";
				//echo "getValue : ".$ep->getValue()."<br>";
				if($ep->getName() == "sync_yn"){
				//echo("\$".$ep->getName()." = \"".$ep->getValue()."\";");
				eval("\$".$ep->getName()." = \"".$ep->getValue()."\";");
				}

			}


			$s_t = explode('T', $event->when[0]->startTime);
	 
			$sdate = str_replace("-","",$s_t[0]);
			$stime = $s_t[1];
			$stime = substr($stime, 0, 5);
			if($stime == ""){
				$stime == "00:00";
			}

			$e_t = explode('T', $event->when[0]->endTime);
			
	 
			$dday = str_replace("-","",$e_t[0]);
			$dtime = $e_t[1];
			$dtime = substr($dtime, 0, 5);
			if($dtime == ""){
				$dtime == "00:00";
			}
			
			$sql = "select wl_ix, work_title, work_detail, CONCAT(LEFT(sdate,4),'-', MID(sdate,5,2),'-', RIGHT(sdate,2)) as sdate, stime, CONCAT(LEFT(dday,4),'-', MID(dday,5,2),'-', RIGHT(dday,2)) as dday, dtime, wl.work_where, 				UNIX_TIMESTAMP(google_updated) as google_updated_ts , google_updated, update_date, UNIX_TIMESTAMP(update_date) as update_date_ts 
					from work_list wl
					where google_event_id = '".$google_event_id."' ";
			$db->query($sql);

			if(!$db->total){					 
			$sql = "select  'C' as data_type, cr.charger_ix, wl.wl_ix, wl.work_title, wl.work_detail, wl.work_where, CONCAT(LEFT(wl.sdate,4),'-', MID(wl.sdate,5,2),'-', RIGHT(wl.sdate,2)) as sdate, IF(wl.stime = '','00:00',wl.stime) as stime, wl.update_date, cr.google_updated,
					CONCAT(LEFT(wl.dday,4),'-', MID(wl.dday,5,2),'-', RIGHT(wl.dday,2)) as dday, IF(wl.dtime = '','00:00',wl.dtime) as dtime, wl.work_where, UNIX_TIMESTAMP(cr.google_updated) as google_updated_ts , UNIX_TIMESTAMP(cr.update_date) as update_date_ts 
					from work_list wl , work_charger_relation cr
					where wl.is_schedule = '1' and wl.wl_ix = cr.wl_ix  and cr.google_event_id = '".$google_event_id."'	";
			$db->query($sql);
			}

			
			unset($sql);

			if($db->total){
				$db->fetch();
				$wl_ix = $db->dt[wl_ix];
				$local_google_updated_unixtimestamp = $db->dt[google_updated_ts];
				$local_update_date_unixtimestamp = $db->dt[update_date_ts];
				$local_google_updated = $db->dt[google_updated];
				$local_update_date = $db->dt[update_date];

				//$updated_infos = explode(array("-",":"),$updated);
				$updated_infos = preg_split("/[-]|[\s]|[:]|[T]|[.]/",date_google_to_sql($updated));
				
				//print_r($updated_infos);
				//exit;
				$google_updated_unixtimestamp = mktime($updated_infos[3],$updated_infos[4],$updated_infos[5],$updated_infos[1],$updated_infos[2],$updated_infos[0]);
				$google_updated = date("Y-m-d H:i:s",$google_updated_unixtimestamp);
				//echo $local_google_updated_unixtimestamp.":::".$google_updated_unixtimestamp."==".($local_google_updated_unixtimestamp<$google_updated_unixtimestamp).":::".$work_title.":::".$updated."<br>";
				//exit;
				if($db->dt[data_type] == "C"){
						
						if($local_update_date_unixtimestamp > $google_updated_unixtimestamp){
							
							$updateEvents = updateEvent ($client, $google_event_id, $db->dt) ;
							$sql = "update work_charger_relation set  
									google_updated = '".date_google_to_sql($updateEvents->updated)."', update_date = '".date_google_to_sql($updateEvents->updated)."'
									where wl_ix='".$wl_ix."' and charger_ix = '".$db->dt[charger_ix]."' ";
							//echo $sql."<br><br>";
							$db->query($sql);
						}
				}else{
					if($local_update_date_unixtimestamp < $google_updated_unixtimestamp){
						/**
						* 서버에 있는 업데이트 타임이 구글 캘린더에 있는 업데이트 타임보다 이전일때 
						*/
						//echo "--> local 업데이트 :".date("Y-m-d H:i:s");
						if($local_google_updated_unixtimestamp < $google_updated_unixtimestamp){
							if($stime == ""){
								$stime == "00:00";
							}
							if($dtime == ""){
								$dtime == "00:00";
							}
							$sql = "update work_list set 
								work_title='$work_title',work_detail='$work_detail', 
								sdate='$sdate',stime='$stime',dday='$dday', dtime='$dtime', work_where='$work_where', update_date = NOW(), edit_date = NOW(), google_updated = '".date_google_to_sql($updated)."'
								where wl_ix='$wl_ix' ";
							//echo $sql."<br><br>";
							$db->query($sql);
						}
					}else if($local_update_date_unixtimestamp > $google_updated_unixtimestamp){
						/**
						* 서버에 있는 업데이트 타임이 구글 캘린더에 있는 업데이트 타임보다 이전일때 
						*/
						$updateEvents = updateEvent ($client, $google_event_id, $db->dt) ;
						//print_r($updateEvents);
						//echo "--> google 업데이트 :".date_google_to_sql($updateEvents->updated);
						$sql = "update work_list set  
								google_updated = '".date_google_to_sql($updateEvents->updated)."', update_date = '".date_google_to_sql($updateEvents->updated)."'
								where wl_ix='".$wl_ix."' ";
						//echo $sql."<br><br>";
						$db->query($sql);
					}
				}
			}else{
				if($sync_yn == "1"){
					$event->delete();
					
				}else{
					if($stime == ""){
						$stime == "00:00";
					}
					if($dtime == ""){
						$dtime == "00:00";
					}
				$sql = "insert into work_list
					(wl_ix,group_ix,company_id,charger_ix,work_title,work_detail,status,complete_rate,is_schedule, is_hidden,is_report,sdate,stime,dday,dtime,work_where, reg_name, reg_charger_ix, google_event_id, google_updated, update_date, edit_date, regdate) 
					values
					('','11','$company_id','$charger_ix','$work_title','$work_detail','WR','0','1','0','0','$sdate','$stime','$dday','$dtime','$work_where','".$chargerinfo[charger]."','".$chargerinfo[charger_ix]."','".$google_event_id."','".date_google_to_sql($updated)."','".date_google_to_sql($updated)."',NOW(),NOW())";

					$db->query($sql);

					addExtendedProperty ($client, $google_event_id, 'sync_yn', '1');
				}
			}
			unset($sync_yn);
		}
		//echo "wl_ix : $wl_ix 로컬데이타 : ".$local_update_date_unixtimestamp."(".$local_update_date.") < 구글 : ".$google_updated_unixtimestamp ."(".$google_updated.")::::".$sql."<br><br>\n\n";
		//echo $google_event_id;
		
		
    }

	$sql = "select 'B' as data_type, wl.charger_ix, wl_ix, work_title, work_detail, CONCAT(LEFT(sdate,4),'-', MID(sdate,5,2),'-', RIGHT(sdate,2)) as sdate, IF(wl.stime = '','00:00',wl.stime) as stime, update_date,
			CONCAT(LEFT(dday,4),'-', MID(dday,5,2),'-', RIGHT(dday,2)) as dday, IF(wl.dtime = '','00:00',wl.dtime) as dtime, wl.work_where, UNIX_TIMESTAMP(google_updated) as google_updated 
			from work_list wl where is_schedule = '1' and google_event_id is null 
			and wl.charger_ix='".$chargerinfo[charger_ix]."' 
			union 
			select 'C' as data_type, cr.charger_ix, wl.wl_ix, wl.work_title, wl.work_detail, CONCAT(LEFT(wl.sdate,4),'-', MID(wl.sdate,5,2),'-', RIGHT(wl.sdate,2)) as sdate, IF(wl.stime = '','00:00',wl.stime) stime, wl.update_date,
			CONCAT(LEFT(wl.dday,4),'-', MID(wl.dday,5,2),'-', RIGHT(wl.dday,2)) as dday, IF(wl.dtime = '','00:00',wl.dtime) as dtime, wl.work_where, UNIX_TIMESTAMP(wl.google_updated) as google_updated 
			from work_list wl , work_charger_relation cr
			where wl.is_schedule = '1' and cr.google_event_id is null
			and wl.wl_ix = cr.wl_ix 
			and cr.charger_ix='".$chargerinfo[charger_ix]."' 
			order by update_date desc 
			limit 30 ";
	//echo nl2br($sql)."<br><br>";
	//echo "<br><br>";
	/*
			

	*/

	$db->query($sql);
	$google_new_events = $db->fetchall();

	for($i=0;$i < count($google_new_events);$i++){
		$new_event_id = InsertEvent($client, $google_new_events[$i]);
		if($google_new_events[$i][data_type] == "B"){
			if($new_event_id){
				$sql = "update work_list set google_event_id = '$new_event_id',  google_updated = NOW(), edit_date = NOW() WHERE wl_ix='".$google_new_events[$i][wl_ix]."'";
				//echo $sql;
				$db->query($sql);
				addExtendedProperty ($client, $new_event_id, 'sync_yn', '1');
			}
		}else if($google_new_events[$i][data_type] == "C"){
			$sql = "update work_charger_relation set google_event_id = '$new_event_id', google_updated = NOW() WHERE wl_ix='".$google_new_events[$i][wl_ix]."' and charger_ix='".$google_new_events[$i][charger_ix]."' ";
				//echo nl2br($sql);
				//echo "<br><br>";
				$db->query($sql);
				addExtendedProperty ($client, $new_event_id, 'sync_yn', '1');
		}
	}

	

}

function addExtendedProperty ($client, $eventId,      $name='http://www.example.com/schemas/2005#mycal.id', $value='1234'){   
	//echo $eventId." ";
	$gcal = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
	$gc = new Zend_Gdata_Calendar($client);   
	if ($event = getEvent($client, $eventId)) {     
		$extProp = $gc->newExtendedProperty($name, $value);     
		$extProps = array_merge($event->extendedProperty, array($extProp));  
		//print_r($extProps);
		$event->extendedProperty = $extProps;     
		$eventNew = $event->save();    
		return $eventNew;   
	} else {     
		return null;   
	} 
}

function date_google_to_sql($str)
{
	$t = explode('T', $str);
 
	$date = $t[0];
	$time = $t[1];
	$time = substr($time, 0, 8);
 
	$str = $date . ' ' . $time;
	return $str;
}

function getEvent($client, $event_id)
{
	$gcal = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
  // print_r($gcal);
	$gcal = new Zend_Gdata_Calendar($client);
	/*
	$query = $gcal->newEventQuery();
	$query->setUser('default');
	$query->setVisibility('private');
	$query->setProjection('full');
	$query->setEvent($event_id);
 */
	try {
		$event = $gcal->getCalendarEventEntry($event_id);
		//$event = $gcal->getCalendarEventEntry($query);
		return $event;
	} catch (Zend_Gdata_App_Exception $e) {
		echo "get Event Error: " . $e->getMessage()."<br><br>";
		return null;
	}
}



function updateEvent($client, $eventId, $event_infos, $tzOffset = '+09'){

$gcal = new Zend_Gdata_Calendar($client);    

// 이벤트 인출
// 새로운 이벤트 속성 설정과 이벤트 갱신
try {
  $event = $gcal->getCalendarEventEntry($eventId);
  $event->title = $gcal->newTitle($event_infos[work_title]); 
  $event->content = $gcal->newContent($event_infos[work_detail]);
  $event->where = array($gcal->newWhere($event_infos[where])); 
  $when = $gcal->newWhen();
  if($event_infos[stime] == ""){
	$when->startTime =  $event_infos[sdate]."T00:00:00.000".$tzOffset.":00";
  }else{
	$when->startTime =  $event_infos[sdate]."T".$event_infos[stime].":00.000".$tzOffset.":00";
  }
  if($event_infos[stime] == ""){
	 $when->endTime = $event_infos[dday]."T00:00:00.000".$tzOffset.":00";
  }else{
	 $when->endTime = $event_infos[dday]."T".$event_infos[dtime].":00.000".$tzOffset.":00";
  }
//echo $event_infos[sdate]."T".$event_infos[stime].":00.000".$tzOffset.":00";

  $event->when = array($when);         
  $event->save();   

  return $event;
} catch (Zend_Gdata_App_Exception $e) {
  die("updateEvent Error: ".$event_infos[work_title]." ".$eventId."<br> " . $e->getResponse());
  return null;
}


}

function updateEvent2 ($client, $eventId, $event_infos, $tzOffset = '+09')  
{ 
	$gdataCal = new Zend_Gdata_Calendar($client); 
	//echo $event_infos[work_title];
	if ($eventOld = getEvent($client, $eventId)) { 
		//echo "Old title: " . $eventOld->title->text . "<br /><br /><br /><br />\n"; 
		$eventOld->title = $gdataCal->newTitle($event_infos[work_title]); 
		$eventOld->content = $gdataCal->newContent($event_infos[work_detail]); 
		
		$when->startTime = $event_infos[sdate]."T".$event_infos[stime].":00.000".$tzOffset.":00";
		$when->endTime = $event_infos[dday]."T".$event_infos[dtime].":00.000".$tzOffset.":00";
		$eventOld->when = array($when);      
		try { 
			$eventOld->save(); 
		} catch (Zend_Gdata_App_Exception $e) { 
			//var_dump($e);
			//echo "Error: " . $e->getResponse();
			exit;
			return null; 
		} 
		$eventNew = getEvent($client, $eventId); 
		//echo "New title: " . $eventNew->title->text . "<br /><br /><br /><br /><br />\n"; 
		//exit;
		return $eventNew; 
		
	} else { 
	  return null; 
	}
}

function InsertEvent($client, $event_infos, $tzOffset = '+09'){
	try {
		/*
		$newEvent = $gcal->newEventEntry();        
		$newEvent->title = $gcal->newTitle($title);        
		$when = $gcal->newWhen();
		$when->startTime = $start;
		$when->endTime = $end;
		$newEvent->when = array($when);        
		$gcal->insertEvent($newEvent);   
*/

		$gdataCal = new Zend_Gdata_Calendar($client); 
		$newEvent = $gdataCal->newEventEntry(); 

		$newEvent->title = $gdataCal->newTitle($event_infos[work_title]); 
		$newEvent->where = array($gdataCal->newWhere($event_infos[where])); 
		$newEvent->content = $gdataCal->newContent($event_infos[work_detail]); 

		$when = $gdataCal->newWhen(); 
		//echo $event_infos[sdate]."T".$event_infos[stime].":00.000".$tzOffset.":00";
		//exit;
		$when->startTime = $event_infos[sdate]."T".$event_infos[stime].":00.000".$tzOffset.":00"; //"{$startDate}T{$startTime}:00.000{$tzOffset}:00"; 
		$when->endTime = $event_infos[dday]."T".$event_infos[dtime].":00.000".$tzOffset.":00"; //"{$endDate}T{$endTime}:00.000{$tzOffset}:00"; 
		$newEvent->when = array($when); 

		// Upload the event to the calendar server 
		// A copy of the event as it is recorded on the server is returned 
		$createdEvent = $gdataCal->insertEvent($newEvent); 
		return $createdEvent->id->text; 


	} catch (Zend_Gdata_App_Exception $e) {
		echo "Insert Error: " .$event_infos[work_title]." ". $e->getResponse();
	}
	//echo 'Event successfully added!';      
}


function setReminder($client, $eventId, $minutes=15) {   
	$gc = new Zend_Gdata_Calendar($client);   
	$method = "alert";   
	if ($event = getEvent($client, $eventId)) {     
		$times = $event->when;     
		foreach ($times as $when) {         
			$reminder = $gc->newReminder();         
			$reminder->setMinutes($minutes);         
			$reminder->setMethod($method);         
			$when->reminder = array($reminder);     
		}     
		$eventNew = $event->save();     
		return $eventNew;   
	} else {     
		return null;   
	} 
}
?>