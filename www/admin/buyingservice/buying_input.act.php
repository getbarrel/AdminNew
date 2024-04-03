<?
include_once($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
//session_start();
$db = new Database;
$db2 = new Database;
//$db->debug = true;
//$db2->debug = true;

if($admininfo[company_id] == ""){
	echo "<script language='JavaScript' src='../_language/language.php'></Script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";
	//'관리자 로그인후 사용하실수 있습니다.'
	exit;
}

//print_r($_POST);
//echo "bs_act:".$bs_act;

if ($act == 'insert' ){

	$sql = "SELECT aid.bai_ix 
	FROM buyingservice_apply_info ai , buyingservice_apply_info_detail aid
	WHERE aid.bai_ix=ai.bai_ix
	and apply_date = DATE_FORMAT(NOW(),'%Y-%m-%d') 
	and ai.mem_ix='".$mem_ix."'
	and buying_status not in ('IC') ";
	
	$db->query($sql);
	$db->fetch();
	if($db->total){  // 하루에 회원이 두번이상 추가할때 (입금완료는 제외)
		if($buying_infos){

			$bai_ix = $db->dt[bai_ix];

			for($i=0;$i < count($_POST["buying_infos"]);$i++){
				//if($buying_infos[$i]["dp_title"] && $buying_infos[$i]["dp_desc"]){
					

					$sql = "insert into buyingservice_apply_info_detail set 
								bai_ix='".$bai_ix."',
								ws_ix='".$buying_infos[$i][ws_ix]."',
								division='".$buying_infos[$i][division]."',
								paper_name='".$buying_infos[$i][paper_name]."',
								goodss_name='".$buying_infos[$i][goodss_name]."',
								color='".$buying_infos[$i][color]."',
								size='".$buying_infos[$i][size]."',
								amount='".$buying_infos[$i][amount]."',
								buying_complete_cnt='".$buying_infos[$i][buying_complete_cnt]."',
								soldout_cancel_cnt='".$buying_infos[$i][soldout_cancel_cnt]."',
								incom_ready_cnt='".$buying_infos[$i][incom_ready_cnt]."',
								buying_price='".$buying_infos[$i][buying_price]."',
								total_price='".$buying_infos[$i][total_price]."',
								pre_payment_price='".$buying_infos[$i][pre_payment_price]."',
								comment='".$buying_infos[$i][comment]."',
								regdate=NOW()
								";
					//$sql = "insert into ".TBL_SHOP_PRODUCT_DISPLAYINFO." (dp_ix,dp_title,dp_desc,dp_use, regdate) values('','".$buying_infos[$i]["dp_title"]."','".$buying_infos[$i]["dp_desc"]."','".$dp_use."',NOW()) ";
					$db->query($sql);
				//}
			}
		}

		if($mmode == "pop"){
			echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('사입정보 등록이 정상적으로 처리 되었습니다.');parent.self.close();</script>";
		}else{
			echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('사입정보 등록이 정상적으로 처리 되었습니다.');parent.document.location.href='../product/goods_list.php';</script>";
		}

	}else{ //회원이 오늘 처음 사입신청했을때

		$sql = "insert into buyingservice_apply_info set 
				buying_mem_name='".$buying_mem_name."',
				mem_ix='".$mem_ix."',
				apply_date='".$apply_date."',
				buying_status='".$buying_status."',
				regdate= NOW()
				";
		//$db->debug = true;			
		$db->query($sql);
		$db->query("SELECT bai_ix FROM buyingservice_apply_info WHERE bai_ix=LAST_INSERT_ID()");
		$db->fetch();
		$bai_ix = $db->dt[bai_ix];

		if($buying_infos){
			for($i=0;$i < count($_POST["buying_infos"]);$i++){
				//if($buying_infos[$i]["dp_title"] && $buying_infos[$i]["dp_desc"]){
					
					$sql = "insert into buyingservice_apply_info_detail set 
								bai_ix='".$bai_ix."',
								ws_ix='".$buying_infos[$i][ws_ix]."',
								division='".$buying_infos[$i][division]."',
								paper_name='".$buying_infos[$i][paper_name]."',
								goodss_name='".$buying_infos[$i][goodss_name]."',
								color='".$buying_infos[$i][color]."',
								size='".$buying_infos[$i][size]."',
								amount='".$buying_infos[$i][amount]."',
								buying_complete_cnt='".$buying_infos[$i][buying_complete_cnt]."',
								soldout_cancel_cnt='".$buying_infos[$i][soldout_cancel_cnt]."',
								incom_ready_cnt='".$buying_infos[$i][incom_ready_cnt]."',
								buying_price='".$buying_infos[$i][buying_price]."',
								total_price='".$buying_infos[$i][total_price]."',
								pre_payment_price='".$buying_infos[$i][pre_payment_price]."',
								comment='".$buying_infos[$i][comment]."',
								regdate=NOW()
								";
					//$sql = "insert into ".TBL_SHOP_PRODUCT_DISPLAYINFO." (dp_ix,dp_title,dp_desc,dp_use, regdate) values('','".$buying_infos[$i]["dp_title"]."','".$buying_infos[$i]["dp_desc"]."','".$dp_use."',NOW()) ";

					$db->query($sql);
				//}
			}
		}
	}

	

	//echo $act;
	//exit;

	
		
		if($mmode == "pop"){
			echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('사입정보 등록이 정상적으로 처리 되었습니다.');parent.self.close();</script>";
		}else{
			echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('사입정보 등록이 정상적으로 처리 되었습니다.');parent.document.location.href='../product/goods_list.php';</script>";
		}
	
	
}




if ($act == 'update' ){
	//print_r($_POST);
	//exit;
	//$db->debug = true;
	$sql = "update buyingservice_apply_info set 
				buying_mem_name='".$buying_mem_name."',
				mem_ix='".$mem_ix."',
				apply_date='".$apply_date."',
				buying_status='".$buying_status."'
				where bai_ix = '".$bai_ix."'
				";
				
	$db->query($sql);
	
	if($buying_infos){
		for($i=0;$i < count($_POST["buying_infos"]);$i++){
			//if($buying_infos[$i]["dp_title"] && $buying_infos[$i]["dp_desc"]){
				$db->query("SELECT bai_ix FROM buyingservice_apply_info_detail WHERE baid_ix='".$buying_infos[$i][baid_ix]."'");
				$db->fetch();
				//$bai_ix = $db->dt[bai_ix];
				if($db->total){
						$sql = "update buyingservice_apply_info_detail set 
							ws_ix='".$buying_infos[$i][ws_ix]."',
							division='".$buying_infos[$i][division]."',
							paper_name='".$buying_infos[$i][paper_name]."',
							goodss_name='".$buying_infos[$i][goodss_name]."',
							color='".$buying_infos[$i][color]."',
							size='".$buying_infos[$i][size]."',
							amount='".$buying_infos[$i][amount]."',
							buying_complete_cnt='".$buying_infos[$i][buying_complete_cnt]."',
							soldout_cancel_cnt='".$buying_infos[$i][soldout_cancel_cnt]."',
							incom_ready_cnt='".$buying_infos[$i][incom_ready_cnt]."',
							buying_price='".$buying_infos[$i][buying_price]."',
							total_price='".$buying_infos[$i][total_price]."',
							pre_payment_price='".$buying_infos[$i][pre_payment_price]."',
							comment='".$buying_infos[$i][comment]."'
									where baid_ix = '".$buying_infos[$i][baid_ix]."' 
									";
						//echo $sql;
						//$sql = "insert into ".TBL_SHOP_PRODUCT_DISPLAYINFO." (dp_ix,dp_title,dp_desc,dp_use, regdate) values('','".$buying_infos[$i]["dp_title"]."','".$buying_infos[$i]["dp_desc"]."','".$dp_use."',NOW()) ";

						$db->query($sql);
				}else{
						$sql = "insert into buyingservice_apply_info_detail set 
							bai_ix='".$bai_ix."',
							ws_ix='".$buying_infos[$i][ws_ix]."',
							division='".$buying_infos[$i][division]."',
							paper_name='".$buying_infos[$i][paper_name]."',
							goodss_name='".$buying_infos[$i][goodss_name]."',
							color='".$buying_infos[$i][color]."',
							size='".$buying_infos[$i][size]."',
							amount='".$buying_infos[$i][amount]."',
							buying_complete_cnt='".$buying_infos[$i][buying_complete_cnt]."',
							soldout_cancel_cnt='".$buying_infos[$i][soldout_cancel_cnt]."',
							incom_ready_cnt='".$buying_infos[$i][incom_ready_cnt]."',
							buying_price='".$buying_infos[$i][buying_price]."',
							total_price='".$buying_infos[$i][total_price]."',
							pre_payment_price='".$buying_infos[$i][pre_payment_price]."',
							comment='".$buying_infos[$i][comment]."',
							regdate=NOW()
							";
				//$sql = "insert into ".TBL_SHOP_PRODUCT_DISPLAYINFO." (dp_ix,dp_title,dp_desc,dp_use, regdate) values('','".$buying_infos[$i]["dp_title"]."','".$buying_infos[$i]["dp_desc"]."','".$dp_use."',NOW()) ";

						$db->query($sql);
				}
			//}
		}
	}

	
	//exit;

	
		if($act == "update"){
			echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('사입정보 수정이 정상적으로 처리 되었습니다.');parent.self.close();</script>";
		}else{
			if($mmode == "pop"){
				echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('사입정보 수정이 정상적으로 처리 되었습니다.');parent.self.close();</script>";
			}else{
				echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('사입정보 수정이 정상적으로 처리 되었습니다.');parent.document.location.href='../product/goods_list.php';</script>";
			}
		}
	
}



if ($act == "delete")
{
	

	$db->query("DELETE FROM buyingservice_apply_info WHERE bai_ix='".$bai_ix."'");
	$db->query("DELETE FROM buyingservice_apply_info_detail WHERE bai_ix='".$bai_ix."'");

	//echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품삭제가 정상적으로 처리 되었습니다.');document.location.href='product_list.php?".$QUERY_STRING."';</script>";
	echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('사입정보가  정상적으로 삭제 되었습니다.');parent.document.location.reload();</script>";

	//header("Location:../product_list.php");
}

if ($act == "detail_delete")
{
	

	$db->query("DELETE FROM buyingservice_apply_info_detail WHERE baid_ix='".$baid_ix."'");

	//echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품삭제가 정상적으로 처리 되었습니다.');document.location.href='product_list.php?".$QUERY_STRING."';</script>";
	echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('사입정보가  정상적으로 삭제 되었습니다.');parent.document.location.reload();</script>";

	//header("Location:../product_list.php");
}



if ($act == "get_options")
{
	

	$db->query("select * from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." where opnt_ix='".$opnt_ix."' order by opndt_ix asc ");
	//$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." WHERE opnt_ix='".$opnt_ix."'");
	$options = $db->fetchall2("object");
	$options = str_replace("\"true\"","true",json_encode($options));
	$options = str_replace("\"false\"","false",$options);
	echo $options;
	//header("Location:../product_list.php");
}


?>
