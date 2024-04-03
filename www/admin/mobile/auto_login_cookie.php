<?

if($_GET["act"]=="check"){

	echo "<script type='text/javascript'>
	<!--
		//alert('".$_COOKIE['ck_adminSaveAUTO']."');
		parent.loadeautocookie('".$_COOKIE['ck_adminSaveAUTO']."')
	//-->
	</script>";
	
	//echo $_COOKIE['ck_adminSaveAUTO'];

	//echo "<iframe name='iframeinner' id='iframeinner' width=0 height=0 scrolling=no src='http://".$_GET["domain"]."/admin/mobile/autocookie_script.php?ck_adminSaveAUTO=".$_COOKIE['ck_adminSaveAUTO']."' ></iframe>";
	exit;
}

if($_GET["act"]=="no_auto_login"){
	
	//echo "<script type='text/javascript'>alert('".$_COOKIE['ck_adminSaveAUTO']."');</script>";
	setcookie('ck_adminSaveAUTO', '' , time() + (86400 * 30));
	//echo "<script type='text/javascript'>alert('".$_COOKIE['ck_adminSaveAUTO']."');</script>";
	
	echo "<script type='text/javascript'>document.location.href='./option.php'</script>";
	
	//print_r($_COOKIE);
	exit;

}

if($_GET["act"]=="yes_auto_login"){

	//echo "<script type='text/javascript'>alert('".$_COOKIE['ck_adminSaveAUTO']."');</script>";
	setcookie('ck_adminSaveAUTO', 'Y' , time() + (86400 * 30));
	//echo "<script type='text/javascript'>alert('".$_COOKIE['ck_adminSaveAUTO']."');</script>";
	echo "<script type='text/javascript'>document.location.href='./option.php'</script>";
	//print_r($_COOKIE);
	exit;

}

?>