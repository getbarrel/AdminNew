<?php
if(!function_exists('sg_load')){$__v=phpversion();$__x=explode('.',$__v);$__v2=$__x[0].'.'.(int)$__x[1];$__u=strtolower(substr(php_uname(),0,3));$__ts=(@constant('PHP_ZTS') || @constant('ZEND_THREAD_SAFE')?'ts':'');$__f=$__f0='ixed.'.$__v2.$__ts.'.'.$__u;$__ff=$__ff0='ixed.'.$__v2.'.'.(int)$__x[2].$__ts.'.'.$__u;$__ed=@ini_get('extension_dir');$__e=$__e0=@realpath($__ed);$__dl=function_exists('dl') && function_exists('file_exists') && @ini_get('enable_dl') && !@ini_get('safe_mode');if($__dl && $__e && version_compare($__v,'5.2.5','<') && function_exists('getcwd') && function_exists('dirname')){$__d=$__d0=getcwd();if(@$__d[1]==':') {$__d=str_replace('\\','/',substr($__d,2));$__e=str_replace('\\','/',substr($__e,2));}$__e.=($__h=str_repeat('/..',substr_count($__e,'/')));$__f='/ixed/'.$__f0;$__ff='/ixed/'.$__ff0;while(!file_exists($__e.$__d.$__ff) && !file_exists($__e.$__d.$__f) && strlen($__d)>1){$__d=dirname($__d);}if(file_exists($__e.$__d.$__ff)) dl($__h.$__d.$__ff); else if(file_exists($__e.$__d.$__f)) dl($__h.$__d.$__f);}if(!function_exists('sg_load') && $__dl && $__e0){if(file_exists($__e0.'/'.$__ff0)) dl($__ff0); else if(file_exists($__e0.'/'.$__f0)) dl($__f0);}if(!function_exists('sg_load')){$__ixedurl='http://www.sourceguardian.com/loaders/download.php?php_v='.urlencode($__v).'&php_ts='.($__ts?'1':'0').'&php_is='.@constant('PHP_INT_SIZE').'&os_s='.urlencode(php_uname('s')).'&os_r='.urlencode(php_uname('r')).'&os_m='.urlencode(php_uname('m'));$__sapi=php_sapi_name();if(!$__e0) $__e0=$__ed;if(function_exists('php_ini_loaded_file')) $__ini=php_ini_loaded_file(); else $__ini='php.ini';if((substr($__sapi,0,3)=='cgi')||($__sapi=='cli')||($__sapi=='embed')){$__msg="\nPHP script '".__FILE__."' is protected by SourceGuardian and requires a SourceGuardian loader '".$__f0."' to be installed.\n\n1) Download the required loader '".$__f0."' from the SourceGuardian site: ".$__ixedurl."\n2) Install the loader to ";if(isset($__d0)){$__msg.=$__d0.DIRECTORY_SEPARATOR.'ixed';}else{$__msg.=$__e0;if(!$__dl){$__msg.="\n3) Edit ".$__ini." and add 'extension=".$__f0."' directive";}}$__msg.="\n\n";}else{$__msg="<html><body>PHP script '".__FILE__."' is protected by <a href=\"http://www.sourceguardian.com/\">SourceGuardian</a> and requires a SourceGuardian loader '".$__f0."' to be installed.<br><br>1) <a href=\"".$__ixedurl."\" target=\"_blank\">Click here</a> to download the required '".$__f0."' loader from the SourceGuardian site<br>2) Install the loader to ";if(isset($__d0)){$__msg.=$__d0.DIRECTORY_SEPARATOR.'ixed';}else{$__msg.=$__e0;if(!$__dl){$__msg.="<br>3) Edit ".$__ini." and add 'extension=".$__f0."' directive<br>4) Restart the web server";}}$__msg.="</body></html>";}die($__msg);exit();}}return sg_load('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');