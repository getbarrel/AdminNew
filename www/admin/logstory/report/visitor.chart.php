<?php
if(!function_exists('sg_load')){$__v=phpversion();$__x=explode('.',$__v);$__v2=$__x[0].'.'.(int)$__x[1];$__u=strtolower(substr(php_uname(),0,3));$__ts=(@constant('PHP_ZTS') || @constant('ZEND_THREAD_SAFE')?'ts':'');$__f=$__f0='ixed.'.$__v2.$__ts.'.'.$__u;$__ff=$__ff0='ixed.'.$__v2.'.'.(int)$__x[2].$__ts.'.'.$__u;$__ed=@ini_get('extension_dir');$__e=$__e0=@realpath($__ed);$__dl=function_exists('dl') && function_exists('file_exists') && @ini_get('enable_dl') && !@ini_get('safe_mode');if($__dl && $__e && version_compare($__v,'5.2.5','<') && function_exists('getcwd') && function_exists('dirname')){$__d=$__d0=getcwd();if(@$__d[1]==':') {$__d=str_replace('\\','/',substr($__d,2));$__e=str_replace('\\','/',substr($__e,2));}$__e.=($__h=str_repeat('/..',substr_count($__e,'/')));$__f='/ixed/'.$__f0;$__ff='/ixed/'.$__ff0;while(!file_exists($__e.$__d.$__ff) && !file_exists($__e.$__d.$__f) && strlen($__d)>1){$__d=dirname($__d);}if(file_exists($__e.$__d.$__ff)) dl($__h.$__d.$__ff); else if(file_exists($__e.$__d.$__f)) dl($__h.$__d.$__f);}if(!function_exists('sg_load') && $__dl && $__e0){if(file_exists($__e0.'/'.$__ff0)) dl($__ff0); else if(file_exists($__e0.'/'.$__f0)) dl($__f0);}if(!function_exists('sg_load')){$__ixedurl='http://www.sourceguardian.com/loaders/download.php?php_v='.urlencode($__v).'&php_ts='.($__ts?'1':'0').'&php_is='.@constant('PHP_INT_SIZE').'&os_s='.urlencode(php_uname('s')).'&os_r='.urlencode(php_uname('r')).'&os_m='.urlencode(php_uname('m'));$__sapi=php_sapi_name();if(!$__e0) $__e0=$__ed;if(function_exists('php_ini_loaded_file')) $__ini=php_ini_loaded_file(); else $__ini='php.ini';if((substr($__sapi,0,3)=='cgi')||($__sapi=='cli')||($__sapi=='embed')){$__msg="\nPHP script '".__FILE__."' is protected by SourceGuardian and requires a SourceGuardian loader '".$__f0."' to be installed.\n\n1) Download the required loader '".$__f0."' from the SourceGuardian site: ".$__ixedurl."\n2) Install the loader to ";if(isset($__d0)){$__msg.=$__d0.DIRECTORY_SEPARATOR.'ixed';}else{$__msg.=$__e0;if(!$__dl){$__msg.="\n3) Edit ".$__ini." and add 'extension=".$__f0."' directive";}}$__msg.="\n\n";}else{$__msg="<html><body>PHP script '".__FILE__."' is protected by <a href=\"http://www.sourceguardian.com/\">SourceGuardian</a> and requires a SourceGuardian loader '".$__f0."' to be installed.<br><br>1) <a href=\"".$__ixedurl."\" target=\"_blank\">Click here</a> to download the required '".$__f0."' loader from the SourceGuardian site<br>2) Install the loader to ";if(isset($__d0)){$__msg.=$__d0.DIRECTORY_SEPARATOR.'ixed';}else{$__msg.=$__e0;if(!$__dl){$__msg.="<br>3) Edit ".$__ini." and add 'extension=".$__f0."' directive<br>4) Restart the web server";}}$__msg.="</body></html>";}die($__msg);exit();}}return sg_load('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');