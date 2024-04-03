<?php
GoogleSync('gooody@gmail.com', 'tlsgnstlr');

function GoogleSync($google_mail, $google_pass){
	global $admininfo;
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
    $gcal = new Zend_Gdata_Calendar($client);

try{   
    $listFeed = $gcal->getCalendarListFeed();   
}catch (Zend_Gdata_App_Exception $e){   
    echo "Error: " . $e->getMessage();   
}   
// echo it back so you can see the id   
echo "<ul>";   
foreach($listFeed as $listEntry) {     
	echo "<li>".$listEntry->title."(Event Feed: " .$listEntry->id.")</li>";   
} 
echo "</ul>";  

/*
$timezone = "00:00:00-08:00"; //My application is in PST (Vancouver, BC)   
$today = date('Y-m-d') . $timezone;   
$tonight = date('Y-m-d',strtotime($today + 24*60*60)) . $timezone;   
  
$queryDefault = service->getEventQuery();   
              ->setUser('default'); // for this query we'll look at the default calendar   
              ->setVisibility('public');  // since we aren't planning on editing the calendar at this point, we can just set this to public; otherwise, it'd have to be private.   
              ->setOrderBy('starttime');   
              ->setStartMin($today);   
              ->setStartMax($today);   
  
$queryOtherCalendar = $queryDefault->setUser('[THAT26CHARACHTERSTRING]%40group.calendar.google.com');  
*/
    
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
		

		echo "<li>\n";
		echo "<h2>" . stripslashes($event->title) . "</h2>\n";
		echo "summary : ".stripslashes($event->summary) . " <br/>\n";
		echo "event_id : ".stripslashes($event->id) . "  <br/>\n";
		echo "published : ".stripslashes($event->published) . " :::".date("Ymd",$event->published)."<br/>\n";
		echo "updated : ".stripslashes($event->updated) . " <br/>\n";
		echo "content : ".stripslashes($event->content) . " <br/>\n";
		echo "link : ".stripslashes($event->link) . " <br/>\n";

		//$when = $event->when();
		//print_r($event);
		//exit;
		echo "startTime : ".$event->when[0]->startTime . '<br />';
		echo "endTime : ".$event->when[0]->endTime . '<br />';
		echo "where : ". $event->where[0] . '<br />';

		//echo "reminder->method : ".$event->when[0]->reminder[0]->method . '<br />';
		//echo "reminder->minutes : ".$event->reminder[0]->minutes . '<br />';



		foreach ($event->author as $author) {
		echo "name : ".stripslashes($author->name) . " <br/>\n";
		echo "email : ".stripslashes($author->email) . " <br/>\n";
		}
		
		foreach ($event->when as $when) {
		//	print_r($when);
		echo "starttime : ".stripslashes($when->starttime) . " <br/>\n";
		echo "endTime : ".stripslashes($when->endTime) . " <br/>\n";
		
		//	foreach ($when->reminder as $rm) {
		//		echo "reminder>method : ".stripslashes($rm[0]->method) . " <br/>\n";
		//	}
		
		}
//exit;
		//foreach ($event->reminder as $reminder) {
		//echo "starttime : ".stripslashes($when->starttime) . " <br/>\n";
		//echo "endTime : ".stripslashes($when->endTime) . " <br/>\n";
		//echo "reminder : ".stripslashes($when->reminder) . " <br/>\n";
		
		//}
		
		

		
		$ep = $event->extendedProperty[0];
		if(is_object($ep)){
			//echo "takeAttributeFromDOM : ".$ep->takeAttributeFromDOM("sync_yn")."<br>";
			echo "getName : ".$ep->getName()."<br>";
			echo "getValue : ".$ep->getValue()."<br>";
			eval("\$".$ep->getName()." = \"".$ep->getValue()."\";");

		}

		echo "</li>\n";
	}

		exit;
	

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
  $when = $gcal->newWhen();
  $when->startTime =  $event_infos[sdate]."T".$event_infos[stime].":00.000".$tzOffset.":00";
  $when->endTime = $event_infos[dday]."T".$event_infos[dtime].":00.000".$tzOffset.":00";
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