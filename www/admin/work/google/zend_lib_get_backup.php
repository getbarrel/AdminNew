<!DOCTYPE html 
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Listing calendar contents</title>
    <style>
    body {
      font-family: Verdana;      
    }
    li {
      border-bottom: solid black 1px;      
      margin: 10px; 
      padding: 2px; 
      width: auto;
      padding-bottom: 20px;
    }
    h2 {
      color: red; 
      text-decoration: none;  
    }
    span.attr {
      font-weight: bolder;  
    }
    </style>    
  </head>
  <body>
    <?php
    // load library
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear:$DOCUMENT_ROOT/admin/work/");
    require_once 'Zend/Loader.php';

	session_start();
    Zend_Loader::loadClass('Zend_Gdata');
    Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
    Zend_Loader::loadClass('Zend_Gdata_Calendar');
    Zend_Loader::loadClass('Zend_Http_Client');
    
	$db = new Database;

    // 캘린더 서비스를 위해 인증된 HTTP 클라이언트 생성
    $gcal = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
    $user = "gooody@gmail.com";
    $pass = "tlsgnstlr";
    $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $gcal);
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
	
	
	
    ?>
    <h1><?php echo $feed->title; ?></h1>
    <?php echo $feed->totalResults; ?> event(s) found.
    <p/>
    <ol>

    <?php    
//	print_r($feed);
//	exit;
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
		try{
			//print_r($event->extendedProperty );
		//echo "sync_yn : ".stripslashes($event->extendedProperty["sync_yn"]) . " <br/>\n";
			$ep = $event->extendedProperty[0];
			if(is_object($ep)){
				//echo "takeAttributeFromDOM : ".$ep->takeAttributeFromDOM("sync_yn")."<br>";
				echo "getName : ".$ep->getName()."<br>";
				echo "getValue : ".$ep->getValue()."<br>";
			}

		} catch (Zend_Gdata_App_Exception $e) {
		  echo "Error: " . $e->getResponse();
		}
		
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
		
		echo "</li>\n";

		//exit;
		$google_event_id = stripslashes($event->id);
		$company_id = $_SESSION['admininfo']['company_id'];
		$charger_ix = $admininfo[charger_ix];
		$reg_name = $admininfo[charger];;
		$event_id = stripslashes($event->id);
		$work_title = stripslashes($event->title);
		$work_detail = stripslashes($event->content);

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
		

		//$db->query("select wl_ix form work_list where google_event_id = '".$event_id."' ");
/*
		if($db->total){
			$db->fetch();
			$wl_ix = $db->dt[wl_ix];

			$sql = "update work_list set 
				group_ix='$group_ix',company_id='$company_id',charger_ix='$charger_ix',work_title='$work_title',work_detail='$work_detail',
				sdate='$sdate',stime='$stime',dday='$dday', dtime='$dtime', complete_rate='$complete_rate'
				where wl_ix='$wl_ix' ";
		}else{
			$sql = "insert into work_list
				(wl_ix,group_ix,company_id,charger_ix,work_title,work_detail,status,complete_rate,is_hidden,is_report,sdate,stime,dday,dtime,reg_name, reg_charger_ix, google_event_id, regdate) 
				values
				('','1','$company_id','$charger_ix','$work_title','$work_detail','WR','0','0','0','$sdate','$stime','$dday','$dtime','".$admininfo[charger]."','".$admininfo[charger_ix]."','".$google_event_id."',NOW())";
		}
	
		//echo $sql;

		$db->query($sql);
		exit;
*/	
    }
    echo "</ul>";
    ?>
    </ol>

  </body>
</html>     
<?
function date_google_to_sql($str)
{
	$t = explode('T', $str);
 
	$date = $t[0];
	$time = $t[1];
	$time = substr($time, 0, 8);
 
	$str = $date . ' ' . $time;
	return $str;
}
?>