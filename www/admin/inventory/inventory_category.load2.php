<?
include("../../class/database.class");

$cid = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];
//echo $form;
//header("Content-Type: application/x-javascript");

$db = new Database;
$tb = $_SESSION['admin_config']["mall_inventory_category_div"]=="P"	?	TBL_SHOP_CATEGORY_INFO:"inventory_category_info";


if($cid){
	$db->query("SELECT cid, cname FROM ".$tb." where depth ='".($depth+1)."' and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
}else{
	echo "<script type='text/javascript'>
		top.document.forms['$form'].elements['".$target."'].length = 1;
		top.document.forms['$form'].elements['cid2'].value = '';

		//[Start] 하위 분류 전부 초기화 처리 kbk 13/08/08
		var p_obj=top.document.forms['$form'].elements['".$target."'].parentNode;
		while(p_obj.tagName!='TABLE') {
			p_obj=p_obj.parentNode;
		}
		var c_obj_len=p_obj.getElementsByTagName('SELECT').length;
		for(var i=".($depth+1).";i<c_obj_len;i++) {
			var c_name=p_obj.getElementsByTagName('SELECT')[i].getAttribute('name');
			top.document.forms['$form'].elements[c_name].length = 1;
		}
		//[End] 하위 분류 전부 초기화 처리 kbk 13/08/08
	</script>";
	exit;
}

if ($db->total){


        //echo "document.forms['$form'].elements['cid'].value = '".$cid."'; \n";
        echo "<script type='text/javascript'>
			top.document.forms['$form'].elements['".$target."'].length = ".($db->total+1).";
			top.document.forms['$form'].elements['".$target."'].setAttribute('validation','false');
			var op_len=top.document.forms['$form'].elements['".$target."'].options.length;
			for(var i=0;i<op_len;i++) {
				top.document.forms['$form'].elements['".$target."'].options[i].selected = false;
			}
			top.document.forms['$form'].elements['".$target."'].options[0].selected = true;
			top.document.forms['$form'].elements['cid2'].value = '".$cid."';
			//alert('".$depth."');
			top.document.forms['$form'].elements['depth'].value = '".$depth."';

		</script>";


        for($i=0; $i < $db->total; $i++){
                $db->fetch($i);


                echo "<script type='text/javascript'>
					top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[cname]."';
					top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[cid]."';
				</script>";
        }
		exit;


}else{
        echo "<script type='text/javascript'>
			top.document.forms['$form'].elements['cid2'].value = '".$cid."';

			top.document.forms['$form'].elements['depth'].value = '".$depth."';
			top.document.forms['$form'].elements['".$target."'].length = 1;
			top.document.forms['$form'].elements['".$target."'].setAttribute('validation','false');

			//[Start] 하위 분류 전부 초기화 처리 kbk 13/08/08
			var p_obj=top.document.forms['$form'].elements['".$target."'].parentNode;
			while(p_obj.tagName!='TABLE') {
				p_obj=p_obj.parentNode;
			}
			var c_obj_len=p_obj.getElementsByTagName('SELECT').length;
			for(var i=".($depth+1).";i<c_obj_len;i++) {
				var c_name=p_obj.getElementsByTagName('SELECT')[i].getAttribute('name');
				top.document.forms['$form'].elements[c_name].length = 1;
			}
			//[End] 하위 분류 전부 초기화 처리 kbk 13/08/08
		</script>";
		exit;
}





?>