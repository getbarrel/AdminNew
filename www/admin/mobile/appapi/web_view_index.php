<?php
include $_SERVER['DOCUMENT_ROOT'].'/class/layout.class';
$_SESSION["is_webview"] = true;

header("Location:http://".$_SERVER['HTTP_HOST']);