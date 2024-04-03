<?php
	//$result = shell_exec("phantomjs /home/dev/www/admin/scrape/gap.js 'http://www.gap.com/browse/product.do?pid=621326&scid=621326022'");
	$result = shell_exec("phantomjs /home/dev/www/admin/scrape/zappos.js 'http://www.zappos.com/men-shirts-tops/CKvXARDL1wHAAQLiAgMBGAI.zso?s=goliveRecentSalesStyle/desc/&zfcTest=gs:0#!/men-shirts-tops/CKvXARDL1wHAAQLiAgMBGAI.zso?p=48&s=goliveRecentSalesStyle/desc/'");
	$de = json_decode($result);
	print_r($de);
?>