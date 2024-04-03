<?
include("../../class/database.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');
$cid = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];
//echo $form;

// iframe 용으로 작업 2011-04-07 kbk

//header("Content-Type: application/x-javascript");

$db = new Database;
//echo ("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 <br><br>");
if($cid){
	//echo ("SELECT cid, cname FROM open_category_info where depth ='".($depth+1)."' and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
	$db->query("SELECT cid, cname FROM ".TBL_SNS_CATEGORY_INFO." where depth ='".($depth+1)."' and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
}else{
	//$db->query("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid = '' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
	echo "<script type='text/javascript'>top.document.forms['$form'].elements['".$target."'].length = 1;</script> \n";
	exit;
}

echo "<script type='text/javascript'>top.document.forms['$form'].elements['selected_cid'].value = '".$cid."';</script> \n";
echo "<script type='text/javascript'>top.document.forms['$form'].elements['selected_depth'].value = '".$depth."';</script> \n";
if ($db->total){

			//if($target == "cid0_1" || $target == "cid1_1" || $target == "cid2_1" || $target == "cid3_1"){

      //}
        echo "<script type='text/javascript'>
			top.document.forms['$form'].elements['".$target."'].length = ".($db->total+1).";
			top.document.forms['$form'].elements['".$target."'].setAttribute('validation','true');
			var op_len=top.document.forms['$form'].elements['".$target."'].options.length;
			for(var i=0;i<op_len;i++) {
				top.document.forms['$form'].elements['".$target."'].options[i].selected = false;
			}
			top.document.forms['$form'].elements['".$target."'].options[0].selected = true;
		</script>";

        for($i=0; $i < $db->total; $i++){
                $db->fetch($i);

                echo "<script type='text/javascript'>
					top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[cname]."';
					top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[cid]."';
				</script>";
        }

		/**** 하위 카테고리의 값을 초기화 kbk ****/
		$childs_num=substr($target,-1);
		$childs_txt=substr($target,0,-1);
		if($childs_num<3) {
			for($i=1;$i<(4-$child_num);$i++) {
				$childs_obj=$childs_txt.($childs_num+1);
				echo "<script type='text/javascript'>
					top.document.forms['$form'].elements['".$childs_obj."'].length = 1;
					top.document.forms['$form'].elements['".$childs_obj."'].setAttribute('validation','false');
				</script>";
			}
		}
		/**** 하위 카테고리의 값을 초기화 kbk ****/


		exit;
}else{
			//if($target == "cid0_1" || $target == "cid1_1" || $target == "cid2_1" || $target == "cid3_1"){
      //  echo "document.forms['$form'].elements['cid'].value = '".$cid."'; \n";
      //}
      	echo "<script type='text/javascript'>
			top.document.forms['$form'].elements['".$target."'].value = '".$cid."';
			top.document.forms['$form'].elements['".$target."'].length = 1;
			top.document.forms['$form'].elements['".$target."'].setAttribute('validation','false');
		</script>";
		exit;
}





?>