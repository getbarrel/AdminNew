<?php
	$result = shell_exec("phantomjs /home/dev/www/admin/scrape/gap.js 'http://oldnavy.gap.com/browse/product.do?cid=67551&vid=1&pid=118386&scid=118386002'");
	//$result = shell_exec("phantomjs /home/dev/www/admin/scrape/gap.js 'http://oldnavy.gap.com/browse/product.do?cid=71300&vid=1&pid=856748&scid=856748012'");
	//$result = shell_exec("phantomjs /home/dev/www/admin/scrape/gap.js 'http://www.gap.com/browse/product.do?cid=70551&vid=1&pid=892573&scid=892573022'");
	$de = json_decode($result);
	print_r($de);
