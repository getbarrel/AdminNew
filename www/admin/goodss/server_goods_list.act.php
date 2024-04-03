<?
include_once($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
ini_set('include_path', ".:/usr/local/lib/php:".$_SERVER["DOCUMENT_ROOT"]."/include/pear");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");


$install_path = "../../include/";

include_once("SOAP/Client.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/goodss/goodss_reg.lib.php");

if($admininfo[company_id] == ""){
	echo "<script language='javascript'>alert('관리자 로그인후 사용하실수 있습니다.');location.href='/admin/admin.php'</script>";
	exit;
}

if($search_searialize_value){
	//	echo $search_searialize_value;
	$unserialize_search_value = unserialize(urldecode($search_searialize_value));
	//print_r ($unserialize_search_value);
	//exit;
	extract($unserialize_search_value);
}



$db = new Database;
$db2 = new Database;
/*
if ($update_kind == "b2b_goods_reg"){

	if($update_type == 2){// 선택회원일때
		for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',
			if($admininfo[admin_level] == 9){
				$sql = "UPDATE ".TBL_SHOP_PRODUCT." p SET state = ".$c_state." , p.disp = ".$c_disp.",  editdate = NOW() Where id = '".$select_pid[$i]."' "; // ,state = ".$state." , disp = '".$disp."'

				$db->query ($sql);
			}
		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 판매/진열 상태 정보변경이 적상적으로 완료되었습니다.');</script>");
		echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");

	}else{// 검색회원일때

			//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");
			$sql = "update ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid set state = ".$c_state." , p.disp = ".$c_disp.",  editdate = NOW() $where  ";
			//echo $sql;
			$db->query($sql);

			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('전체상품의 판매/진열 상태 정보변경이 적상적으로 완료되었습니다.');</script>");
			echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");

	}

}
*/
//print_r($_POST);

if ($act == "b2b_goods_regs"){

	if($update_type == 2){// 선택상품일때
			// 선택상품일때는 POST 로 넘어온 goodss_pid 를 참조
		$goods_pid = $_POST[goodss_pid];
		asort($goods_pid);
		//print_r($goods_pid);
		//exit;
	}else{ // 검색상품일때

			$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
			// server.php 의 namespace 와 일치해야함
			$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

			## 한글 인자의 경우 에러가 나므로 인코딩함.


			$service_infos = (array)$soapclient->call("getUsableServiceInfo",$params = array("mall_ix"=> $admininfo[mall_ix]),	$options);
				//echo $co_goodsinfo;

			$useable_service = (array)$service_infos[useable_service];
			$userable_service_infos = (array)$service_infos[userable_service_infos];


			foreach($unserialize_search_value as $key => $value){
				$search_rules[$key]= $value;//urlencode($value);
			}

			//print_r($search_rules);
			//exit;

			$goodsinfos = $soapclient->call("getCoGoodsByServer",$params = array("useable_service"=> $useable_service, "search_rules"=> $search_rules,  "paginginfo"=> $paginginfo,  "selet_type"=> "id"),	$options);
			//print_r($goodsinfos);
			//exit;
			$goodsinfos = (array)$goodsinfos;
			for($i=0; $i < count($goodsinfos[goods]); $i++){
				$goodsinfo = (array)$goodsinfos[goods][$i];
//	print_r($goodsinfo);
				$goodss_pid[$i] = $goodsinfo[pid];
			}





	}

	//print_r($_POST);
	echo "<script language='JavaScript' src='../js/jquery-1.4.js'></Script>\n
	<script language='javascript' src='../_language/language.php'></script>\n
	<script language='javascript'>
	var goodss_pid = new Array();\n";

	//최신상품을 맨 마지막으로 보내서 vieworder 를 큰숫자로 넣기 위함 2013-03-08 홍진영
	$z=0;
	for($i=(count($goodss_pid)-1); $i >= 0; $i--){
		echo "goodss_pid[".$z."] = '".$goodss_pid[$i]."';\n";
		$z++;
	}
//echo $act;
//exit;

//exit;


	echo "
	var bs_i = 0;
	GoodssProductReg();

	function GoodssProductReg(){
		//alert(bs_i+'::'+goodss_pid[bs_i]);
		//parent.document.search_form.this_url.value = goodss_pid[bs_i];//

		$.ajax({
				type: 'POST',
				data:
					{'act': 'b2b_goods_reg_one','goodss_pid': goodss_pid[bs_i],'company_id': '".$_POST[company_id]."','sc_state': '".$_POST[sc_state]."','img_update': '".$img_update."','sc_disp': '".$_POST[sc_disp]."','price_setting':'".$_POST[price_setting]."','margin_percent': '".$_POST[margin_percent]."','margin_cross': '".$_POST[margin_cross]."','round_type': '".$_POST[round_type]."','round_precision': '".$_POST[round_precision]."','dupe_process': '".$_POST[dupe_process]."','c_cid': '".$_POST[c_cid]."'},
				url: 'server_goods_list.act.php',
				dataType: 'html',
				async: true,
				beforeSend: function(){
					//alert(1);
					 //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open');
					 //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events');
				},
				success: function(data){
					//alert(data);
						try{
							//alert(goodss_pid[bs_i][3]);
							parent.document.getElementById('select_update_loadingbar').innerHTML = \"<table align=center><tr><td><img src='/admin/images/indicator.gif' border=0 width=32 height=32 align=absmiddle> </td><td>[\"+data+\"] <br><a href='\"+goodss_pid[bs_i]+\"' target=_blank>\"+goodss_pid[bs_i]+\"</a></td></tr></table> \";
							bs_i++;

							if(goodss_pid.length > bs_i){
								setTimeout(\"GoodssProductReg()\",900);
							}else{
								alert('상품정보 가져오기가 완료되었습니다.');
								parent.select_update_unloading();
							}

						}catch(e){
							alert(e.message + '['+goodss_pid[bs_i]+']<--bs_i : ['+bs_i+']');
							//setTimeout(\"GoodssProductReg()\",900);
						}


				}
			});

	}
	</script>
	";
}

if ($act == "b2b_goods_reg_one"){
	$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
	//echo goodss_pid;
	GoodssProductCopy($goodss_pid);
}

if ($act == "b2b_goods_reg"){

//echo count($pid);
	/*
	if($approval_type == 1){
		$approval_str = "state = 1 ";
	}else if($approval_type == 2){
		$approval_str = "state = 1, disp = 1 ";
	}else if($approval_type == 3){
		$approval_str = "disp = 0 ";
	}else if($approval_type == 4){
		$approval_str = "state = 0 ";
	}
	*/

	$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);


	if($update_type == 2){// 선택회원일때


	}else{ // 검색회원일때
			## 한글 인자의 경우 에러가 나므로 인코딩함.

			//$admininfo[mall_ix] 가 생성이 안되서 안되고 있음*********************************** 스케줄링 돌릴때
			$service_infos = (array)$soapclient->call("getUsableServiceInfo",$params = array("mall_ix"=> $admininfo[mall_ix]),	$options);
				//echo $co_goodsinfo;

			$useable_service = (array)$service_infos[useable_service];
			$userable_service_infos = (array)$service_infos[userable_service_infos];

			if(is_array($unserialize_search_value)){
				foreach($unserialize_search_value as $key => $value){
					$search_rules[$key]= $value;//urlencode($value);
				}
			}



			//print_r($_SESSION);

			$goodsinfos = $soapclient->call("getCoGoodsByServer",$params = array("useable_service"=> $useable_service, "search_rules"=> $search_rules,  "paginginfo"=> $paginginfo,  "selet_type"=> "id"),	$options);
			$goodsinfos = (array)$goodsinfos;
			//print_r($goodsinfos);
			//exit;
			for($i=0; $i < count($goodsinfos[goods]); $i++){
				$goodsinfo = (array)$goodsinfos[goods][$i];
//	print_r($goodsinfo);
				$goodss_pid[$i] = $goodsinfo[pid];
			}

	}
	//print_r($goodss_pid);
	//exit;
		if($admininfo[admin_level] == 9){
				//echo $hostserver;
				//$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
				// server.php 의 namespace 와 일치해야함
				//$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

				for($i=0;$i < count($goodss_pid);$i++){
					//$ids = split("-",$goodss_pid[$i]);
					//echo $admininfo[company_id]."::::".$co_company_id."::::".$pid."<br>";
					$sql = "SELECT id FROM ".TBL_SHOP_PRODUCT." WHERE co_pid = '".$goodss_pid[$i]."'";
					//echo $sql;
					//exit;
					$db->query($sql);

					if($db->total){
						if($_POST[dupe_process] == "update" || true){

							GoodssProductCopy($goodss_pid[$i]);
						}
					}else{
							GoodssProductCopy($goodss_pid[$i]);
					}
					//	exit;
					//exit;
					//print_r($ret);
				}
				/*
				if($ret){
					echo("<script>alert('서버에 정상적으로 등록 되었습니다.');parent.document.location.reload();</script>");
				}else{
					//echo("<script>alert('서버에 이미 공유된 상품 입니다.');</script>");
				}
				*/
				echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상품이 정상적으로 등록 되었습니다.');parent.document.location.reload();</script>");
				exit;
			}


	echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>";

}



?>