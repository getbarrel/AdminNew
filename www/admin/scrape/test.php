<?php
	//$result = shell_exec("phantomjs /home/dev/www/admin/scrape/gap.js 'http://www.gap.com/browse/product.do?pid=621326&scid=621326022'");
	$result = shell_exec("phantomjs /home/dev/www/admin/scrape/test.js 'http://www.gap.com/browse/product.do?cid=7007&vid=1&pid=353167&scid=353167002'");
	$de = json_decode($result);
	print_r($de);
