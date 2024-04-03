<?
@include_once("../web.config");
include_once("../../class/database.class");
include_once("../lib/imageResize.lib.php");
include_once("../class/layout.class");

session_start();
$db = new Database;
$db2 = new Database;
//$db->debug = true;
//$db2->debug = true;

if($admininfo[company_id] == ""){
	echo "<script language='JavaScript' src='../_language/language.php'></Script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";
	//'관리자 로그인후 사용하실수 있습니다.'
	exit;
}


//echo "bs_act:".$bs_act;

if ($act == 'insert' || $act == "tmp_insert"){

	/*
	if($options_price_stock["option_name"]){

		if($options_price_stock["option_use"]){
			$options_price_stock_use = $options_price_stock["option_use"];
		}else{
			$options_price_stock_use = 0;
		}


			$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_TMP." (opnt_ix, option_name, option_kind, option_type, company_id, charger_ix, regdate)
						VALUES
						('','".$options_price_stock["option_name"]."','".$options_price_stock["option_kind"]."','".$options_price_stock["type"]."','".$admininfo[company_id]."','".$admininfo[charger_ix]."',NOW())";
			$db->sequences = "SHOP_GOODS_OPTIONS_TMP_SEQ";

			$db->query($sql);

			if($db->dbms_type == "oracle"){
				$opnt_ix = $db->last_insert_id;
			}else{
				$db->query("SELECT opnt_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS_TMP." WHERE opnt_ix=LAST_INSERT_ID()");
				$db->fetch();
				$opnt_ix = $db->dt[opnt_ix];
			}
		for($j=0;$j < count($options_price_stock["option_div"]);$j++){
			if($options_price_stock[option_div][$j]){
					$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." (opndt_ix, opnt_ix, option_div,option_price, option_stock, option_safestock, option_code) ";
					$sql = $sql." values('','$opnt_ix','".$options_price_stock[option_div][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[stock][$j]."','".$options_price_stock[safestock][$j]."','".$options_price_stock[code][$j]."') ";
					$db->sequences = "SHOP_GOODS_OPTIONS_DT_TMP_SEQ";

					$db->query($sql);

					if($options_price_stock[stock][$j] == 0 && ($option_stock_yn == "" || $option_stock_yn == "R")){
						$option_stock_yn = "N";
					}

					if($options_price_stock[stock][$j] < $options_price_stock[safestock][$j] && $option_stock_yn == ""){
						$option_stock_yn = "R";
					}
			}
		}

	}
	*/

	if($option_all_use == "Y"){
		$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." where opnt_ix='".$opnt_ix."'  ");
		$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_TMP." where opnt_ix = '".$opnt_ix."' and option_kind in ('s','p') ");
	}else{

		foreach($_POST["options"] as $ops_key=>$ops_value) {

			if($options[$ops_key]["option_name"]){

				$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_TMP." (opnt_ix, option_name, option_kind, option_type, opt_code, disp, company_id, charger_ix, regdate)
								VALUES
								('','".$options[$ops_key]["option_name"]."','".$options[$ops_key]["option_kind"]."','".$options[$ops_key]["option_type"]."','".$options[$ops_key]["opt_code"]."','".$options[$ops_key]["option_use"]."','".$admininfo[company_id]."','".$admininfo[charger_ix]."',NOW())";
				$db->sequences = "SHOP_GOODS_OPTIONS_TMP_SEQ";
				$db->query($sql);

				$db->query("SELECT opnt_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS_TMP." WHERE opnt_ix=LAST_INSERT_ID()");
				$db->fetch();
				$opnt_ix = $db->dt[opnt_ix];
				
				$path = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp";

				if(!is_dir($path)){
					mkdir($path, 0777);
					chmod($path,0777);
				}else{
					chmod($path,0777);
				}
				
				if($_FILES["options"]["size"][$ops_key]["option_imgfile"] > 0){

					$path = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp/".$opnt_ix;

					if(!is_dir($path)){
						mkdir($path, 0777);
						chmod($path,0777);
					}
					
					$option_img_path = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp/".$opnt_ix."/option_img.gif";
					copy($_FILES["options"]["tmp_name"][$ops_key]["option_imgfile"], $option_img_path);
				}

				foreach($options[$ops_key]["details"] as $od_key=>$od_value) {

					if($options[$ops_key][details][$od_key][option_div]){
	
						$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." (opndt_ix, opnt_ix, option_div, option_div_engish, option_div_china, option_code,option_coprice,option_price, option_stock, option_safestock, opt_dt_code, disp) ";
						$sql = $sql." values('','".$opnt_ix."','".trim($options[$ops_key][details][$od_key][option_div])."','".trim($options[$ops_key][details][$od_key][option_div_engish])."','".trim($options[$ops_key][details][$od_key][option_div_china])."','".$options[$ops_key][details][$od_key][code]."','".$options[$ops_key][details][$od_key][coprice]."','".$options[$ops_key][details][$od_key][price]."','0','0','".$options[$ops_key][details][$od_key][opt_dt_code]."','".$options[$ops_key][details][$od_key][option_use]."') ";
						$db->sequences = "SHOP_GOODS_OPTIONS_DT_TMP_SEQ";
						$db->query($sql);

						if($_FILES["options"]["size"][$ops_key]["details"][$od_key]["option_imgfile"] > 0){

							$db->query("SELECT opndt_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." WHERE opndt_ix=LAST_INSERT_ID()");
							$db->fetch();
							$opndt_ix = $db->dt[opndt_ix];

							$path = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp/".$opnt_ix;

							if(!is_dir($path)){
								mkdir($path, 0777);
								chmod($path,0777);
							}
							
							$option_img_path = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp/".$opnt_ix."/option_detail_img_".$opndt_ix.".gif";
							copy($_FILES["options"]["tmp_name"][$ops_key]["details"][$od_key]["option_imgfile"], $option_img_path);
						}
					}
				}
			}

		}

	}// option_all_use 있는지 여부
	
	/*
	if($display_options){
		for($i=0;$i < count($_POST["display_options"]);$i++){
			if($display_options[$i]["dp_title"] && $display_options[$i]["dp_desc"]){
				if($display_options[$i]["dp_use"]){
					$dp_use = $display_options[$i]["dp_use"];
				}else{
					$dp_use = "0";
				}

				$sql = "insert into ".TBL_SHOP_PRODUCT_DISPLAYINFO." (dp_ix,dp_title,dp_desc,dp_use, regdate) values('','".$display_options[$i]["dp_title"]."','".$display_options[$i]["dp_desc"]."','".$dp_use."',NOW()) ";
				$db->sequences = "SHOP_GOODS_DISPLAYINFO_SEQ";
				$db->query($sql);
			}
		}
	}
	*/



	if(!$bs_act){
		if($act == "tmp_insert"){
			echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('옵션 임시정보 등록이 정상적으로 처리 되었습니다.');parent.opener.document.location.reload();parent.self.close();</script>";
		}else{
			if($mmode == "pop"){
				echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('옵션 임시정보 등록이 정상적으로 처리 되었습니다.');parent.opener.document.location.reload();parent.self.close();</script>";
			}else{
				echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('옵션 임시정보 등록이 정상적으로 처리 되었습니다.');parent.document.location.href='../product/goods_list.php';</script>";
			}
		}
	}
}

if ($act == "delete")
{


	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_TMP." WHERE opnt_ix='".$opnt_ix."'");
	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." WHERE opnt_ix='".$opnt_ix."'");

	//이미지 삭제 추가!
	$path = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp/".$opnt_ix;
	if (file_exists($path)){
		rmdirr($path);
	}
	
	echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('옵션임시정보 삭제가 정상적으로 처리 되었습니다.');parent.document.location.reload();</script>";
}

if ($act == "img_delete")
{

	if($opndt_ix!=""){
		$option_img_path = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp/".$opnt_ix."/option_detail_img_".$opndt_ix.".gif";
	}else{
		$option_img_path = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp/".$opnt_ix."/option_img.gif";
	}

	if (file_exists($option_img_path)){
		@unlink($option_img_path);
		echo "Y";
	}else{
		echo "N";
	}
	exit;
}


if ($act == "update" || $act == "tmp_update")
{

		/*
		if($options_price_stock["option_name"]){
			if($options_price_stock["opnt_ix"]){
				$db->query("SELECT opnt_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS_TMP." WHERE opnt_ix = '".trim($options_price_stock["opnt_ix"])."' and option_kind = 'b'");
			}else{
				$db->query("SELECT opnt_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS_TMP." WHERE opnt_ix = '$opnt_ix' and option_name = '".trim($options_price_stock["option_name"])."' and option_kind = 'b'");
			}

			if($db->total){
				$db->fetch();
				$opnt_ix = $db->dt[opnt_ix];
				$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS_TMP." set
								option_name='".trim($options_price_stock["option_name"])."', option_kind='".$options_price_stock["option_kind"]."', option_type='".$options_price_stock["option_type"]."'
								where opnt_ix = '".$opnt_ix."' ";
				$db->query($sql);
			}else{
				$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_TMP." (opnt_ix, option_name, option_kind, option_type, company_id, charger_ix, regdate)
								VALUES
								('','".$options_price_stock["option_name"]."','".$options_price_stock["option_kind"]."','".$options_price_stock["type"]."','".$admininfo[company_id]."','".$admininfo[charger_ix]."',NOW())";
				$db->sequences = "SHOP_GOODS_OPTIONS_TMP_SEQ";
				$db->query($sql);
				if($db->dbms_type == "oracle"){
					$opnt_ix =  $db->last_insert_id;
					//echo $INSERT_PRODUCT_ID;
					//exit;
				}else{
					$db->query("SELECT opnt_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS_TMP." WHERE opnt_ix=LAST_INSERT_ID()");
					$db->fetch();
					$opnt_ix = $db->dt[opnt_ix];
				}
			}
			//echo $sql."<br>";
			//exit;


			$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." set insert_yn='N' where opnt_ix='".$opnt_ix."' ";
			//echo $sql."<br><br>";
			$db->query($sql);
			$option_stock_yn = "";
			//for($j=0;$j < count($options_price_stock["option_div"]);$j++){
			foreach($options_price_stock["option_div"] as $opsd_key=>$opsd_value) {
				if($options_price_stock[option_div][$opsd_key]){
					$db->query("SELECT opndt_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." WHERE option_div = '".trim($options_price_stock[option_div][$opsd_key])."' and opnt_ix = '".$opnt_ix."' ");

					if($db->total){
						$db->fetch();
						$opndt_ix = $db->dt[opndt_ix];

						$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." set
									option_div='".$options_price_stock[option_div][$opsd_key]."',
									option_code='".$options_price_stock[code][$opsd_key]."',
									option_coprice='".$options_price_stock[coprice][$opsd_key]."',
									option_price='".$options_price_stock[price][$opsd_key]."',
									option_stock='".$options_price_stock[stock][$opsd_key]."',
									option_safestock='".$options_price_stock[safestock][$opsd_key]."' ,
									insert_yn='Y'
									where opndt_ix ='".$opndt_ix."' and opnt_ix = '".$opnt_ix."'";
						//option_useprice='".$options_price_stock[price][$opsd_key]."', 2012-11-06 홍진영(char 1 이기 때문에 오라클에서 에러남)
					}else{
						$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." (opndt_ix, opnt_ix, option_div, option_code,option_coprice,option_price, option_stock, option_safestock) ";
						$sql = $sql." values('','$opnt_ix','".$options_price_stock[option_div][$opsd_key]."','".$options_price_stock[code][$opsd_key]."','".$options_price_stock[coprice][$opsd_key]."','".$options_price_stock[price][$opsd_key]."','".$options_price_stock[stock][$opsd_key]."','".$options_price_stock[safestock][$opsd_key]."') ";
						$db->sequences = "SHOP_GOODS_OPTIONS_DT_TMP_SEQ";
					}

					//echo $sql."<br><br>";
					$db->query($sql);

					if($options_price_stock[stock][$opsd_key] == 0 && ($option_stock_yn == "" || $option_stock_yn == "R")){
						$option_stock_yn = "N";
					}

					if($options_price_stock[stock][$opsd_key] < $options_price_stock[safestock][$opsd_key] && $option_stock_yn == ""){
						$option_stock_yn = "R";
					}
				}
			}
			$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." where opnt_ix='".$opnt_ix."' and insert_yn = 'N' ";
			//echo $sql;
			$db->query($sql);

		}else{

			$db->query("SELECT opnt_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS_TMP." WHERE opnt_ix = '".$opnt_ix."' and option_kind = 'b'");

			if($db->total){
				$db->fetch();
				$opnt_ix = $db->dt[opnt_ix];
				$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." where opnt_ix='".$opnt_ix."'  ";
				$db->query($sql);
			}
			$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_TMP." where opnt_ix = '".$opnt_ix."' and option_kind = 'b' ";
			$db->query($sql);
		}
		*/
		

		foreach($_POST["options"] as $ops_key=>$ops_value) {

			if($options[$ops_key]["opnt_ix"]){

				$db->query("SELECT opnt_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS_TMP." WHERE opnt_ix = '".$options[$ops_key]["opnt_ix"]."' and option_kind in ('s','p') ");

				if($db->total){
					$db->fetch();
					$opnt_ix = $db->dt[opnt_ix];
					$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS_TMP." set
									option_name='".trim($options[$ops_key]["option_name"])."',
									option_kind='".$options[$ops_key]["option_kind"]."',
									option_type='".$options[$ops_key]["option_type"]."',
									opt_code='".$options[$ops_key]["opt_code"]."',
									disp='".$options[$ops_key]["option_use"]."',
									editdate=NOW()
									where opnt_ix = '".$opnt_ix."' ";

					$db->query($sql);

				}else{
					$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_TMP." (opnt_ix, option_name, option_kind, option_type, opt_code, disp, company_id, charger_ix, regdate)
									VALUES
									('','".$options[$ops_key]["option_name"]."','".$options[$ops_key]["option_kind"]."','".$options[$ops_key]["option_type"]."','".$options[$ops_key]["opt_code"]."','".$options[$ops_key]["option_use"]."','".$admininfo[company_id]."','".$admininfo[charger_ix]."',NOW())";
					$db->sequences = "SHOP_GOODS_OPTIONS_TMP_SEQ";
					$db->query($sql);

					$db->query("SELECT opnt_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS_TMP." WHERE opnt_ix=LAST_INSERT_ID()");
					$db->fetch();
					$opnt_ix = $db->dt[opnt_ix];
				}

				$path = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp";

				if(!is_dir($path)){
					mkdir($path, 0777);
					chmod($path,0777);
				}else{
					chmod($path,0777);
				}
				
				if($_FILES["options"]["size"][$ops_key]["option_imgfile"] > 0){

					$path = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp/".$opnt_ix;

					if(!is_dir($path)){
						mkdir($path, 0777);
						chmod($path,0777);
					}
					
					$option_img_path = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp/".$opnt_ix."/option_img.gif";
					copy($_FILES["options"]["tmp_name"][$ops_key]["option_imgfile"], $option_img_path);
				}


				$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." set insert_yn='N'	where opnt_ix='".$opnt_ix."' ";
				$db->query($sql);

				foreach($options[$ops_key]["details"] as $od_key=>$od_value) {
					if($options[$ops_key][details][$od_key][opndt_ix]){
						$db->query("SELECT opndt_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." WHERE opndt_ix = '".trim($options[$ops_key][details][$od_key][opndt_ix])."' ");

						if($db->total){
							$db->fetch();
							$opndt_ix = $db->dt[opndt_ix];
							$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." set
									option_div='".$options[$ops_key][details][$od_key][option_div]."',
									option_div_engish='".$options[$ops_key][details][$od_key][option_div_engish]."',
									option_div_china='".$options[$ops_key][details][$od_key][option_div_china]."',
									option_code='".$options[$ops_key][details][$od_key][code]."',
									option_coprice='".$options[$ops_key][details][$od_key][coprice]."',
									option_price='".$options[$ops_key][details][$od_key][price]."',
									option_stock='0', option_safestock='0' ,
									opt_dt_code='".$options[$ops_key][details][$od_key][opt_dt_code]."',
									disp='".$options[$ops_key][details][$od_key][option_use]."',
									insert_yn='Y'
									where opndt_ix ='".$opndt_ix."' and opnt_ix = '".$opnt_ix."'";
							$db->query($sql);
						}
					}else{
						if($options[$ops_key][details][$od_key][option_div]){
							$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." (opndt_ix, opnt_ix, option_div, option_div_engish, option_div_china, option_code,option_coprice,option_price, option_stock, option_safestock, opt_dt_code, disp) ";
							$sql = $sql." values('','".$opnt_ix."','".trim($options[$ops_key][details][$od_key][option_div])."','".trim($options[$ops_key][details][$od_key][option_div_engish])."','".trim($options[$ops_key][details][$od_key][option_div_china])."','".$options[$ops_key][details][$od_key][code]."','".$options[$ops_key][details][$od_key][coprice]."','".$options[$ops_key][details][$od_key][price]."','0','0','".$options[$ops_key][details][$od_key][opt_dt_code]."','".$options[$ops_key][details][$od_key][option_use]."') ";
							$db->sequences = "SHOP_GOODS_OPTIONS_DT_TMP_SEQ";
							$db->query($sql);

							$db->query("SELECT opndt_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." WHERE opndt_ix=LAST_INSERT_ID()");
							$db->fetch();
							$opndt_ix = $db->dt[opndt_ix];
						}
					}

					if($_FILES["options"]["size"][$ops_key]["details"][$od_key]["option_imgfile"] > 0){

						$path = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp/".$opnt_ix;

						if(!is_dir($path)){
							mkdir($path, 0777);
							chmod($path,0777);
						}
						
						$option_img_path = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp/".$opnt_ix."/option_detail_img_".$opndt_ix.".gif";
						copy($_FILES["options"]["tmp_name"][$ops_key]["details"][$od_key]["option_imgfile"], $option_img_path);
					}
				}

				$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." where opnt_ix='".$opnt_ix."' and insert_yn = 'N' ");
			}
		}

	if($act == "tmp_update"){
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('옵션임시정보 수정이 정상적으로 처리 되었습니다.');parent.document.location.reload();</script>";
	}else{
		if($mmode == "pop"){
			echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('옵션임시정보 수정이 정상적으로 처리 되었습니다.');parent.self.close();</script>";
		}else{
			echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('옵션임시정보 수정이 정상적으로 처리 되었습니다.');parent.document.location.href='../product/product_list.php';</script>";
		}
	}

}

if ($act == "get_options")
{


	$db->query("select * from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." where opnt_ix='".$opnt_ix."' order by opndt_ix asc ");
	//$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." WHERE opnt_ix='".$opnt_ix."'");
	if($db->dbms_type == "oracle"){
		$options = $db->fetchall("object");
	}else{
		$options = $db->fetchall2("object");
	}
	$options = str_replace("\"true\"","true",json_encode($options));
	$options = str_replace("\"false\"","false",$options);
	echo $options;
	//header("Location:../product_list.php");
}


?>
