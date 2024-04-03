<?
include_once($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
ini_set('include_path', ".:/usr/local/lib/php:".$_SERVER["DOCUMENT_ROOT"]."/include/pear");
include_once($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");

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
	if($act=='stock_update'){
		extract($unserialize_search_value);
		$act='stock_update';
	}else{
		extract($unserialize_search_value);
	}
}



$db = new MySQL;
$db2 = new MySQL;

if ($act == "stock_update"){
	if($update_type == 2){// 선택상품일때
			// 선택상품일때는 POST 로 넘어온 goodss_pid 를 참조
		//print_r($_POST);
		$sql = "select  p.id as id, p.pname, p.co_pid, p.co_goods
						FROM ".TBL_SHOP_PRODUCT." p where  p.id IN (".implode(',',$_POST[goodss_pid]).") ";
		$db->query($sql);
		//echo emplode;
		//exit;
	}else{ // 검색상품일때

			$co_type_str .= " and p.co_goods = '2'";

			if($orderby != "" && $ordertype != ""){
				$orderbyString = " group by p.id   order by $orderby $ordertype ";
			}else{
				$orderbyString = " group by p.id   order by p.regdate desc ";
			}

			if($mode == "search"){
				switch ($depth){
					case 0:
						$cut_num = 3;
						break;
					case 1:
						$cut_num = 6;
						break;
					case 2:
						$cut_num = 9;
						break;
					case 3:
						$cut_num = 12;
						break;
					case 4:
						$cut_num = 15;
						break;
				}
				$where = "";
				if($search_text != ""){
					$where .= "and p.".$search_type." LIKE '%".$search_text."%' ";
				}

				if($sprice && $eprice){
					$where .= "and sellprice between $sprice and $eprice ";
				}

				if($status_where){
					$where .= " and ($status_where) ";
				}
				if($brand2 != ""){
					$where .= " and brand = ".$brand2."";
				}

				if($brand_name != ""){
					$where .= " and brand_name LIKE '%".$brand_name."%' ";
				}

				if($disp != ""){
					$where .= " and p.disp = ".$disp;
				}

				if($co_type_str){
					$where .= $co_type_str ;
				}

				if($state2 != ""){
					$where .= " and state = ".$state2."";
				}


				if($cid2 != ""){
					$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
				}else{
					$where .= "";
				}
				if($admininfo[admin_level] == 9){
					if($company_id != ""){
						$addWhere = "and admin ='".$company_id."'";
					}else{
						unset($addWhere);
					}
					$sql = "SELECT p.id as id, p.pname, p.co_pid, p.co_goods
					FROM ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid  and r.basic = 1 , ".TBL_COMMON_COMPANY_DETAIL." c
					where c.company_id = p.admin and p.admin is not null $addWhere $where  $orderbyString ";
					//echo $sql;
					$db->query($sql);
				}else{
					$sql = "SELECT p.id as id, p.pname, p.co_pid, p.co_goods
					FROM ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid  and r.basic = 1 , ".TBL_COMMON_COMPANY_DETAIL." c
					where c.company_id = p.admin and admin ='".$admininfo[company_id]."' $where  $orderbyString ";


					$db->query($sql);
				}
				//echo $sql;
			}else{
				if ($cid2 == ""){
					if($admininfo[admin_level] == 9){
						if($company_id != ""){
							$addWhere = "and admin ='".$company_id."'";
						}else{
							$addWhere = " and admin ='".$admininfo[company_id]."' "; // 상품공유의 경우는 자기 자신의 상품만 공유 할수 있다.
							$addWhere = "";
						}
					}else{
						$addWhere = "and admin ='".$admininfo[company_id]."'";
						$addWhere = "";
					}

					if($co_type_str){
						$where .= $co_type_str ;
					}

					$sql = "SELECT p.id as id, p.pname, p.co_pid, p.co_goods
					FROM ".TBL_SHOP_PRODUCT." p  left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid  and r.basic = 1 ,  ".TBL_COMMON_COMPANY_DETAIL." c
					where c.company_id = p.admin and p.admin is not null
					$where
					$addWhere
					$orderbyString";
					// r.cid,
					//right join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid  and r.basic = 1

					//echo nl2br($sql);
					//exit;
					$db->query($sql);


					//echo $sql;
				}else{
					switch ($depth){
						case 0:
							$cut_num = 3;
							break;
						case 1:
							$cut_num = 6;
							break;
						case 2:
							$cut_num = 9;
							break;
						case 3:
							$cut_num = 12;
							break;
						case 4:
							$cut_num = 15;
							break;
					}

					if($admininfo[admin_level] == 9){
						if($company_id != ""){
							$addWhere = "and admin ='".$company_id."'";
						}else{
							unset($addWhere);
						}
					}else{
						$addWhere = "and admin ='".$admininfo[company_id]."'";
					}

					if($co_type_str){
						$where .= $co_type_str ;
					}

					$sql = "SELECT p.id as id, p.pname, p.co_pid, p.co_goods
						FROM ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid and r.basic = 1
						and r.cid = '".$cid2."'  , ".TBL_COMMON_COMPANY_DETAIL." c
						where c.company_id = p.admin and r.cid = '".$cid2."' $addWhere $where $orderbyString ";

					//	echo $sql;
					//	exit;
						$db->query($sql);
				}
			}
	}

	//print_r($goodsinfos);

	$goodsinfos = $db->fetchall();

	for($i=0; $i < count($goodsinfos); $i++){
		$goodsinfo = (array)$goodsinfos[$i];
		$goodss_pid[$i] = $goodsinfo[co_pid];
	}

	//print_r($_POST);
	echo "<script language='JavaScript' src='../js/jquery-1.4.js'></Script>\n
	<script language='javascript' src='../_language/language.php'></script>\n
	<script language='javascript'>
	var goodss_pid = new Array();\n";

	for($i=0; $i < count($goodss_pid); $i++){
		echo "goodss_pid[".$i."] = '".$goodss_pid[$i]."';\n";
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
					{'act': 'b2b_goodss_stock_check_one','goodss_pid': goodss_pid[bs_i],'sc_state': '".$_POST[sc_state]."','img_update': '".$img_update."','sc_disp': '".$_POST[sc_disp]."','price_setting':'".$_POST[price_setting]."','margin_plus': '".$_POST[margin_plus]."','margin_cross': '".$_POST[margin_cross]."','round_type': '".$_POST[round_type]."','round_precision': '".$_POST[round_precision]."','dupe_process': '".$_POST[dupe_process]."','c_cid': '".$_POST[c_cid]."'},
				url: 'goods_list.act.php',
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
							parent.document.getElementById('select_update_loadingbar').innerHTML = \"<table align=center><tr><td><img src='/admin/images/indicator.gif' border=0 width=32 height=32 align=absmiddle> </td><td>[\"+goodss_pid[bs_i]+\" - \"+data+\"] <br><a href='\"+goodss_pid[bs_i]+\"' target=_blank></a></td></tr></table> \";
							bs_i++;
							//alert(parent.document.getElementById('select_update_loadingbar').innerHTML);

							if(goodss_pid.length > bs_i){
								setTimeout(\"GoodssProductReg()\",900);
							}else{
								//pause(1000);
								setTimeout(\"EndProcess()\",1000);

							}

						}catch(e){
							alert(e.message + '['+goodss_pid[bs_i]+']<--bs_i : ['+bs_i+']');
							//setTimeout(\"GoodssProductReg()\",900);
						}


				}
			});

	}

	function EndProcess(){
		alert('상품정보 재고 정보 업데이트가 완료되었습니다.');
		parent.select_update_unloading();
	}

	function pause(numberMillis) {
		var now = new Date();
		var exitTime = now.getTime() + numberMillis;
		while (true) {
			now = new Date();
			if (now.getTime() > exitTime)
			return;
		}
	}
	</script>
	";
}



if ($act == "b2b_goodss_stock_check_one"){
	$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
	//echo " goodss_pid : ".$goodss_pid;
	GoodssProductStockCheck($goodss_pid);
}
?>