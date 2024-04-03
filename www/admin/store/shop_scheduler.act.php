<?
include("../../class/database.class");
include($_SERVER["DOCUMENT_ROOT"].'/admin/class/cron.class');
$cron = new Crontab();
session_start();

$db = new Database;
if($_POST){
	if($_POST['minute'] == 0){
		$cron_m = '*';
	}else{
		$cron_m = "*/".$_POST['minute'];
	}
	
	if($_POST['hour'] == 0){
		$cron_h = '*';
	}else{
		$cron_h = $_POST['hour'];
	}

	if($_POST['day'] == 0){
		$cron_d = '*';
	}else{
		$cron_d = $_POST['day'];
	}

	if($_POST['weekday'] == 0){
		$cron_w = '*';
	}else{
		$cron_w = $_POST['weekday'];
	}

	if($_POST['month'] == 0){
		$cron_mo = '*';
	}else{
		$cron_mo = $_POST['month'];
	}

	
}

//print_r($_POST);
if($act == 'insert'){
	
	

	if($_POST['file_type'] == 'input'){
		if(!is_file($_POST['scheduler_file_name_input']) && $_POST['action_type'] !='self' ){
			echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('해당 경로에 파일이 존재하지 않거나 경로가 잘못 되었습니다.','top_reload');</script>";
			exit;	
		}
		$scheduler_file_name = $_POST['scheduler_file_name_input'];
	}else{
		$scheduler_file_name = $_POST['scheduler_file_name'];
	}

	$scheduler_file_name = str_replace("//","/",$scheduler_file_name);
	$sql = "select * from shop_schedule_setting where file = '".$scheduler_file_name."'";
	$db->query($sql);
	if(empty($db->total)){
		$sql = "insert into shop_schedule_setting (type, file_type,action_type,auto_type, file, month, weekday, day, hour, minute,schedul_time, site_code, comment, regdate) values ('".$_POST['type']."','".$_POST['file_type']."','".$_POST['action_type']."','".$_POST['auto_type']."', '".$scheduler_file_name."', '".$_POST['month']."', '".$_POST['weekday']."', '".$_POST['day']."', '".$_POST['hour']."', '".$_POST['minute']."', '".$_POST['schedul_time']."', '".$_POST['site_code']."', '".$_POST['comment']."', NOW()) ";
		$db->query($sql);
		
		if($_POST['action_type'] == 'web'){
			$cron_option = "lynx --dump";
			$cron_file_name = "'http://".$_SERVER['HTTP_HOST']."/".str_replace($_SERVER['DOCUMENT_ROOT'],'',$scheduler_file_name)."'";
		}else if($_POST['action_type'] == 'shell'){
			$cron_option = "sh";
			$cron_file_name = $scheduler_file_name;
		}else{
			$cron_file_name = $scheduler_file_name;
		}
		if(empty($_POST['schedul_time'])){
			$cron_name = "".$cron_m." ".$cron_h." ".$cron_d." ".$cron_w." ".$cron_mo." ".$cron_option." ".$cron_file_name."";
		}else{
			$cron_name = "".$_POST['schedul_time']." ".$cron_option." ".$cron_file_name."";
		}
		if($_POST['auto_type'] == 'Y'){
//			$cron->addJob($cron_name);
		}

		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('스케줄 정보가 등록되었습니다.','top_reload');</script>";
		exit;
	}else{
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('해당 스케줄 파일은 이미 등록 되어있습니다.','top_reload');</script>";
		exit;
	}
}

if($act == 'update'){

	if($_POST['file_type'] == 'input'){
		if(!is_file($_POST['scheduler_file_name_input']) && $_POST['action_type'] !='self' ){
			echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('해당 경로에 파일이 존재하지 않거나 경로가 잘못 되었습니다.','top_reload');</script>";
			exit;	
		}
		$scheduler_file_name = $_POST['scheduler_file_name_input'];
	}else{
		$scheduler_file_name = $_POST['scheduler_file_name'];
	}

	$sql = "select 
				if(minute = 0 ,'*',minute) as minute,
				if(hour = 0 ,'*',hour) as hour,
				if(day = 0 ,'*',day) as day,
				if(weekday = 0 ,'*',weekday) as weekday,
				if(month = 0 ,'*',month) as month,
				schedul_time,
				file as scheduler_file_name,action_type
			from 
				shop_schedule_setting 
			where 
				ss_ix = '".$_POST['ss_ix']."'";
	$db->query($sql);
	$db->fetch();

	if($db->dt['action_type'] == 'web'){
		$cron_option = "lynx --dump";
	
		$cron_file_name = "'http://".$_SERVER['HTTP_HOST']."/".str_replace($_SERVER['DOCUMENT_ROOT'],'',$db->dt['scheduler_file_name'])."'";
	}else if($db->dt['action_type'] == 'shell'){
		$cron_option = "sh";
		$cron_file_name = $db->dt['scheduler_file_name'];
	}else{
		$cron_file_name = $db->dt['scheduler_file_name'];
	}
	
	if(empty($db->dt['schedul_time'])){
		$cron_name = "".$db->dt['minute']." ".$db->dt['hour']." ".$db->dt['day']." ".$db->dt['weekday']." ".$db->dt['month']." ".$cron_option." ".$cron_file_name."";
	}else{
		$cron_name = "".$db->dt['schedul_time']." ".$cron_option." ".$cron_file_name."";
	}
	
//	$cron->removeJob($cron_name);
	


	$sql = "update shop_schedule_setting set 
				type = '".$_POST['type']."',
				file_type = '".$_POST['file_type']."',
				action_type = '".$_POST['action_type']."',
				auto_type = '".$_POST['auto_type']."',
				file = '".$scheduler_file_name."',
				month = '".$_POST['month']."',
				weekday = '".$_POST['weekday']."',
				day = '".$_POST['day']."',
				hour = '".$_POST['hour']."',
				minute = '".$_POST['minute']."',
				schedul_time = '".$_POST['schedul_time']."',
				site_code = '".$_POST['site_code']."',
				comment = '".$_POST['comment']."',
				update_date = NOW()
			where ss_ix = '".$_POST['ss_ix']."'
	";
	$db->query($sql);

	if($_POST['action_type'] == 'web'){
		$cron_option = "lynx --dump";
		$cron_file_name ="'http://".$_SERVER['HTTP_HOST']."/".str_replace($_SERVER['DOCUMENT_ROOT'],'',$scheduler_file_name)."'";
	}else if($_POST['action_type'] == 'shell'){
		$cron_option = "sh";
		$cron_file_name = $scheduler_file_name;
	}else{
		$cron_file_name = $scheduler_file_name;
	}

	if(empty($_POST['schedul_time'])){
		$cron_name = "".$cron_m." ".$cron_h." ".$cron_d." ".$cron_w." ".$cron_mo." ".$cron_option." ".$cron_file_name."";
	}else{
		$cron_name = "".$_POST['schedul_time']." ".$cron_option." ".$cron_file_name."";
	}
	
	if($_POST['auto_type'] == 'Y'){	
//		$cron->addJob($cron_name);
	}
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('스케줄정보가 수정 되어있습니다.','top_reload'); </script>";
	exit;
}

if($act == 'modify'){
	$sql = "select * from shop_schedule_setting where ss_ix = '".$_POST['ix']."'";
	$db->query($sql);
	
	echo json_encode($db->fetch());
	//echo json_encode($db->dt);
	exit;
}

if($act == 'delete'){
	
	$sql = "select 
				if(minute = 0 ,'*',minute) as minute,
				if(hour = 0 ,'*',hour) as hour,
				if(day = 0 ,'*',day) as day,
				if(weekday = 0 ,'*',weekday) as weekday,
				if(month = 0 ,'*',month) as month,
				schedul_time,
				file as scheduler_file_name,action_type
			from 
				shop_schedule_setting 
			where 
				ss_ix = '".$_POST['ss_ix']."'";
	$db->query($sql);
	$db->fetch();

	if($db->dt['action_type'] == 'web'){
		$cron_option = "lynx --dump";
	
		$cron_file_name = "'http://".$_SERVER['HTTP_HOST']."/".str_replace($_SERVER['DOCUMENT_ROOT'],'',$db->dt['scheduler_file_name'])."'";
	}else if($db->dt['action_type'] == 'shell'){
		$cron_option = "sh";
		$cron_file_name = $db->dt['scheduler_file_name'];
	}else{
		$cron_file_name = $db->dt['scheduler_file_name'];
	}

	if(empty($db->dt['schedul_time'])){
		$cron_name = "".$db->dt['minute']." ".$db->dt['hour']." ".$db->dt['day']." ".$db->dt['weekday']." ".$db->dt['month']." ".$cron_option." ".$cron_file_name."";
	}else{
		$cron_name = "".$db->dt['schedul_time']." ".$cron_option." ".$cron_file_name."";
	}
//	$cron->removeJob($cron_name);
		
	$sql = "delete from shop_schedule_setting where ss_ix = '".$_POST['ss_ix']."'";
	$db->query($sql);
	


	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('스케줄정보가 삭제 되어있습니다.','top_reload'); </script>";
	exit;
}
?>