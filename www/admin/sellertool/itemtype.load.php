<?
include("../../class/database.class");
include("sellertool.lib.php");
include("../openapi/openapi.lib.php");


$cid = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];
$obj = $_GET['obj'];
//echo $form;
//header("Content-Type: application/x-javascript");

$db = new Database;
//echo ("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 <br><br>");
/*
if($cid){
	//echo ("SELECT cid, cname FROM open_category_info where depth ='".($depth+1)."' and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
	$db->query("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
}else{
	//$db->query("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid = '' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
	//echo "document.forms['$form'].elements['".$target."'].length = 1; \n";
	//echo "document.forms['$form'].elements['cid2'].value = ''; \n";
	echo "<script type='text/javascript'>
		top.document.forms['$form'].elements['".$target."'].length = 1;
		top.document.forms['$form'].elements['cid2'].value = '';
	</script>";
	exit;
}
*/

$OAL = new OpenAPI($site_code);
//if($depth == 0){
//	$category_infos = $OAL->lib->getSubCategory();
//}else{
	//echo $cid;
	if($cid){
		$category_infos = $OAL->lib->getSubItemType($cid);
		//print_r($category_infos);
	}
//}

if (count($category_infos) > 0){


        //echo "document.forms['$form'].elements['cid'].value = '".$cid."'; \n";
        echo "<script language='JavaScript' src='../js/jquery-1.4.js'></Script>
		<script type='text/javascript'>
		 //alert('$depth ::: $obj');
			top.document.forms['$form'].elements['".$target."'].length = ".(count($category_infos)+1).";
			top.document.forms['$form'].elements['".$target."'].setAttribute('validation','false');
			var op_len=top.document.forms['$form'].elements['".$target."'].options.length;
			for(var i=0;i<op_len;i++) {
				top.document.forms['$form'].elements['".$target."'].options[i].selected = false;
			}
			top.document.forms['$form'].elements['".$target."'].options[0].selected = true;
			top.document.forms['$form'].elements['cid2'].value = '".$cid."';
			//alert('".$depth."');
			top.document.forms['$form'].elements['depth'].value = '".$depth."';
			//top.document.forms['$form'].elements['cname'].value = top.document.forms['$form'].elements['".$obj."'][top.document.forms['$form'].elements['".$obj."'].selectedIndex].text;
			
			var this_depth = ".($depth+1).";
			var target_name = '';
			$('.sellertool_category',parent.document).each(function(){
				//alert($(this).attr('depth'));
				if(this_depth < $(this).attr('depth')){
					//alert($(this).attr('depth'));
					top.document.forms['$form'].elements[$(this).attr('name')].length = 1;
				}

				if($('select[name='+$(this).attr('name')+'] option:selected',parent.document).val() != ''){
					//alert(\"$('select[name=\"+$(this).attr('name')+\"] option:selected',parent.document).text()\");
					if(target_name == ''){
						target_name += $('select[name='+$(this).attr('name')+'] option:selected',parent.document).text();
					}else{
						target_name += '>'+$('select[name='+$(this).attr('name')+'] option:selected',parent.document).text();
					}	
					//alert(target_name);
				}
			});
			top.document.forms['$form'].elements['cname'].value = target_name;


		</script>";
        
  
        //for($i=0; $i < $db->total; $i++){
		for($i=0; $i < count($category_infos); $i++){
			$category_info = (array)$category_infos[$i];
              //  $db->fetch($i);


                echo "<script type='text/javascript'>
					top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$category_info[disp_name]."';
					top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$category_info[disp_no]."';
				</script>";
        }
		exit;


}else{
        echo "<script language='JavaScript' src='../js/jquery-1.4.js'></Script>
		<script type='text/javascript'>
			//alert('$depth');
			//sellertool_category

			top.document.forms['$form'].elements['cid2'].value = '".$cid."';			
			top.document.forms['$form'].elements['depth'].value = '".$depth."';
			top.document.forms['$form'].elements['".$target."'].length = 1;
			top.document.forms['$form'].elements['".$target."'].setAttribute('validation','false');

			var this_depth = ".($depth+1).";
			var target_name = '';
			$('.sellertool_category',parent.document).each(function(){
				//alert($(this).attr('depth'));
				if(this_depth < $(this).attr('depth')){
					//alert($(this).attr('depth'));
					top.document.forms['$form'].elements[$(this).attr('name')].length = 1;
				}
//alert(\"$('select[name=\"+$(this).attr('name')+\"] option:selected',parent.document).text()\");
				if($('select[name='+$(this).attr('name')+'] option:selected',parent.document).val() != ''){
					
					if(target_name == ''){
						target_name += $('select[name='+$(this).attr('name')+'] option:selected',parent.document).text();
					}else{
						target_name += '>'+$('select[name='+$(this).attr('name')+'] option:selected',parent.document).text();
					}	
					//alert(target_name);
				}
			});
			top.document.forms['$form'].elements['cname'].value = target_name;
		</script>";
		exit;
}





?>