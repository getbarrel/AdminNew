<?
include("../class/layout.class");
$templet_src = $admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete];
$product_src = $admin_config[mall_data_root]."/images/product";
$images_src = $admin_config[mall_data_root]."/images";
		
?>
<LINK REL=stylesheet HREF=../include/admin.css TYPE=text/css>
<script>
	var contents;
	contents =parent.document.code.page_contents.value
	contents = contents.replace(/{templet_src}/gi,'<?=$templet_src?>');
	contents = contents.replace(/{product_src}/gi,'<?=$product_src?>');
	contents = contents.replace(/{images_src}/gi,'<?=$images_src?>');
	document.write(contents);
</script>