<?
if($menu_type == "left" || $menu_type == ""){
	if($menu == "Y"){
		setcookie("HIDE_MENU","Y", time()+3600000,"/",$HTTP_HOST);
	}else{
		setcookie("HIDE_MENU","N", time()+3600000,"/",$HTTP_HOST);
	}
}

if($menu_type == "top"){
	if($menu == "Y"){
		setcookie("TOP_MENU_HIDDEN","Y", time()+3600000,"/",$HTTP_HOST);
	}else{
		setcookie("TOP_MENU_HIDDEN","N", time()+3600000,"/",$HTTP_HOST);
	}
}

if($menu_type == "right"){
	if($menu == "Y"){
		setcookie("RIGHT_MENU_HIDDEN","Y", time()+3600000,"/",$HTTP_HOST);
	}else{
		setcookie("RIGHT_MENU_HIDDEN","N", time()+3600000,"/",$HTTP_HOST);
	}
}

?>