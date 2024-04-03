<?
include("../../class/database.class");
$install_path = "../../include/";
include("SOAP/Client.php");

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
	//$db->query("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");

	$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

	## 한글 인자의 경우 에러가 나므로 인코딩함.


	$categorys = (array)$soapclient->call("getGoodssCategoryList",$params = array("cid"=> $cid, "depth"=> ($depth+1)),	$options);

}else{
	//$db->query("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid = '' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
	echo "<script type='text/javascript'>top.document.forms['$form'].elements['".$target."'].length = 1;</script> \n";
	exit;
}

echo "<script type='text/javascript'>top.document.forms['$form'].elements['goodss_cid'].value = '".$cid."';</script> \n";
echo "<script type='text/javascript'>top.document.forms['$form'].elements['goodss_depth'].value = '".$depth."';</script> \n";
if (count($categorys)){

			//if($target == "cid0_1" || $target == "cid1_1" || $target == "cid2_1" || $target == "cid3_1"){

      //}
        echo "<script type='text/javascript'>
			top.document.forms['$form'].elements['".$target."'].length = ".(count($categorys)+1).";
			top.document.forms['$form'].elements['".$target."'].setAttribute('validation','false');
			var op_len=top.document.forms['$form'].elements['".$target."'].options.length;
			for(var i=0;i<op_len;i++) {
				top.document.forms['$form'].elements['".$target."'].options[i].selected = false;
			}
			top.document.forms['$form'].elements['".$target."'].options[0].selected = true;
			top.document.forms['$form'].elements['goodss_cid'].value = '".$cid."';
			top.document.forms['$form'].elements['goodss_depth'].value = '".$depth."';
		</script>";

        for($i=0; $i < count($categorys); $i++){
                $db->fetch($i);
				$category = (array)$categorys[$i];

                echo "<script type='text/javascript'>
					top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$category[cname]."';
					top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$category[cid]."';
				</script>";
        }

		/**** 하위 카테고리의 값을 초기화 kbk ****/
		$childs_num=substr($target,-1);
		$childs_txt=substr($target,0,-1);
		if($childs_num<3) {
			/*for($i=1;$i<(4-$child_num);$i++) {
				$childs_obj=$childs_txt.($childs_num+1);
				echo "<script type='text/javascript'>
					alert('".$i."');
					top.document.forms['$form'].elements['".$childs_obj."'].length = 1;
					top.document.forms['$form'].elements['".$childs_obj."'].setAttribute('validation','false');
				</script>";
			}*/

			//수정 kbk 12/03/22
			for($i=($childs_num+1);$i<4;$i++) {
				$childs_obj=$childs_txt.($i);
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
	  //현재구조에서는 필요가 없으므로 주석처리
      	/*echo "<script type='text/javascript'>
			//top.document.forms['$form'].elements['".$target."'].value = '".$cid."';
			//top.document.forms['$form'].elements['".$target."'].length = 1;
			//top.document.forms['$form'].elements['".$target."'].setAttribute('validation','false');
		</script>";*/

		//수정 kbk 12/03/22
		$childs_num=substr($target,-1);
		$childs_txt=substr($target,0,-1);
		if($childs_num<3) {
			for($i=$childs_num;$i<4;$i++) {
				$childs_obj=$childs_txt.($i);
				echo "<script type='text/javascript'>
					top.document.forms['$form'].elements['".$childs_obj."'].length = 1;
					top.document.forms['$form'].elements['".$childs_obj."'].setAttribute('validation','false');
				</script>";
			}
		}
		exit;
}





?>