<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");
include("../inventory/inventory.lib.php");
include("../../include/cash_manage.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/receipt.lib.php");

//include($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");

if($act!="part_cancel"){//부분취소!
	//include($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");
}


include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
$shmop = new Shared("b2c_coupon_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$coupon_data = $shmop->getObjectForKey("b2c_coupon_rule");
$coupon_data = unserialize(urldecode($coupon_data));

$db = new Database;
$mdb = new Database;

$oid = $_REQUEST['oid'];
$act = $_REQUEST['act'];

$ADMIN_MESSAGE = $_SESSION["admininfo"]["charger"]."(".$_SESSION["admininfo"]["charger_id"].")";

if($act == "orderinfo_update"){

	$sql = "UPDATE ".TBL_SHOP_ORDER." SET bmail='".$bmail."', btel='".$btel."', bmobile = '".$bmobile."' WHERE oid='".$oid."' ";
	$db->query($sql);

	foreach($deliveryinfo as $key=>$val){

		if($val[zipcode2] != "" || $val[zipcode2] != NULL){
			$zipcode = $val[zipcode1]."-".$val[zipcode2];
		}else{
			$zipcode = $val[zipcode1];
		}


		$sql = "UPDATE shop_order_detail_deliveryinfo SET
			rname='".$val[rname]."',
			r_first_name='".$val[r_first_name]."',
			r_last_name='".$val[r_last_name]."',
			r_first_kana='".$val[r_first_kana]."',
			r_last_kana='".$val[r_last_kana]."',
			rmail='".$val[rmail]."',
			rtel='".$val[rtel]."',
			rmobile='".$val[rmobile]."',
			zip='".$zipcode."',
			addr1='".$val[addr1]."',
			addr2='".$val[addr2]."',
			msg='".$val[msg]."'
			WHERE odd_ix='".$key."'";
		$db->query($sql);

		foreach($val[product] as $pkey=>$pval){
			$sql = "UPDATE shop_order_detail SET
			due_date='".$pval[due_date]."',
			msgbyproduct='".$pval[msg]."'
			WHERE od_ix='".$pkey."'";
			$db->query($sql);
		}

	}

	set_order_status($oid,"","주문정보 변경",$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],"");

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('주문정보가 정상적으로 수정되었습니다.');parent.location.reload();location.href='about:blank';</script>");
}

if($act == "claim_update"){

	$sql="UPDATE shop_order_status SET
		c_type = '".$c_type."' ,
		reason_code = '".$reason_code."',
		status_message = '"."[".fetch_order_status_div('DC',$apply_status,"title",$reason_code)."]".$msg."'
	WHERE
		oid = '".$oid."'
	and
		od_ix in (select od_ix from shop_order_detail where oid='".$oid."' and claim_group=".$claim_group.")
	and
		status='".$apply_status."' ";
	$db->query($sql);

	if($delivery_info_odd_ix !=""){

		if(! empty($zip2)){
			$return_zip_all = $zip1."-".$zip2;
		}else{
			$return_zip_all = $zip1;
		}

		$sql="UPDATE shop_order_detail_deliveryinfo SET
			zip = '".$return_zip_all."' ,
			rname = '".$rname."',
			rtel = '".$rtel."',
			addr1 = '".$addr1."',
			addr2 = '".$addr2."',
			msg = '".$delivery_msg."'
		WHERE
			odd_ix = '".$delivery_info_odd_ix."'";
		$db->query($sql);
	}

	if($return_delivery_info_odd_ix !=""){
		if(! empty($return_zip2)){
			$return_zip_all = $return_zip1."-".$return_zip2;
		}else{
			$return_zip_all = $return_zip1;
		}

		$sql="UPDATE shop_order_detail_deliveryinfo SET
			zip = '".$return_zip_all."' ,
			rname = '".$return_rname."',
			rtel = '".$return_rtel."',
			addr1 = '".$return_addr1."',
			addr2 = '".$return_addr2."',
			msg = '".$return_delivery_msg."',
			delivery_method = '".$delivery_method."',
			quick = '".$quick."',
			invoice_no = '".$deliverycode."'
		WHERE
			odd_ix = '".$return_delivery_info_odd_ix."'";
		$db->query($sql);
	}

	$sql="select od_ix from shop_order_detail where oid='".$oid."' and claim_group=".$claim_group." and claim_delivery_od_ix!=0";
	$db->query($sql);
	if($db->total){
		$od_all=$db->fetchall();
		foreach($od_all as $odall){
			$od_ix[] = $odall["od_ix"];
		}
	}

	$sql="UPDATE ".TBL_SHOP_ORDER_DETAIL." SET claim_fault_type = '".fetch_order_status_div('DC',$apply_status,"type",$reason_code)."', exchange_delivery_type='".$exchange_delivery_type."' where od_ix in ('".implode("','",$od_ix)."')";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script>
	<script language='javascript'>
		//show_alert('정상적으로 요청되었습니다.');
		alert('정상적으로 처리되었습니다.');

		if(top.window.dialogArguments) {//showModalDialog를 사용할 경우 부모창 새로 고침을 위해 수정 kbk 13/08/03
			var opener=top.window.dialogArguments;
			top.window.returnValue=true;
		} else {
			top.opener.document.location.reload();
		}
		top.window.close();
	</script>");
	exit;
	exit;
}

if($act == "claim_apply"){		//클레임 요청시 처리 부분

	$od_ix_array=array();
	$com_claim=array();

	$sql="select od.*, o.status as ostatus, o.user_code, pid as id from ".TBL_SHOP_ORDER." o ,".TBL_SHOP_ORDER_DETAIL." od where o.oid=od.oid and od.od_ix in ('".str_replace('|',"','",$od_ix_str)."') ";
	$db->query($sql);
	$order_details = $db->fetchall("object");

	$sql="select ( max(claim_group) +1 ) as claim_group from shop_order_detail where oid='".$oid."' ";
	$db->query($sql);
	$db->fetch();
	$claim_group = $db->dt["claim_group"];

	for($i=0;$i<count($order_details);$i++){

		//아래 shop_order_claim_delivery 값을 넣기 위한 변수 생성!
		$delivery_type = $order_details[$i][delivery_type];
		$company_id = $order_details[$i][company_id];

		if($order_details[$i]["pcnt"] > $apply_cnt[$order_details[$i]["od_ix"]]){//요청수량이 작을때넘김!
			//주문분리 orderSeparate 함수는 lib.function.php 에 있음
			if($status==ORDER_STATUS_EXCHANGE_APPLY||$status==ORDER_STATUS_EXCHANGE_ING){
				if(fetch_order_status_div('DC','EA',"type",$reason_code)=="B"){
					$Coupondata["oid"]=$order_details[$i][oid];
					$Coupondata["od_ix"]=$order_details[$i][od_ix];
//					$CouponReturn = orderUseCouponReturnCheck($Coupondata,$apply_cnt[$product_info[$i][od_ix]]);
//					$coupon_total_dc_price=$CouponReturn["coupon_total_dc_price"];
//					if($coupon_total_dc_price > 0){
//						$od_ix = orderSeparate($order_details[$i]["od_ix"],$apply_cnt[$order_details[$i]["od_ix"]],false,true);
//					}else{
						$od_ix = orderSeparate($order_details[$i]["od_ix"],$apply_cnt[$order_details[$i]["od_ix"]]);
//					}
				}else{
					$od_ix = orderSeparate($order_details[$i]["od_ix"],$apply_cnt[$order_details[$i]["od_ix"]],false,true);
				}
			}else{
				$od_ix = orderSeparate($order_details[$i]["od_ix"],$apply_cnt[$order_details[$i]["od_ix"]]);
			}
		}else{
			//전체 취소일때
			$od_ix = $order_details[$i]["od_ix"];
		}

		$od_ix_array[]=$od_ix;

		if($status==ORDER_STATUS_CANCEL_APPLY||$status==ORDER_STATUS_CANCEL_COMPLETE){//취소요청,취소완료

			//전체 취소일때
			if($apply_all=="Y"){

				if(empty($com_claim[$order_details[$i][company_id]])){
					$sql="select ( max(claim_group) +1 ) as claim_group from shop_order_detail where oid='".$oid."' ";
					$db->query($sql);
					$db->fetch();
					$tmp_claim_group = $db->dt["claim_group"];

					$com_claim[$order_details[$i][company_id]][claim_group]=$tmp_claim_group;
					$com_claim[$order_details[$i][company_id]][delivery_type]=$delivery_type;
				}

				$claim_group = $com_claim[$order_details[$i][company_id]][claim_group];
			}

			$claim_type = "C";

			if($status==ORDER_STATUS_CANCEL_COMPLETE){
				if($order_details[$i][ostatus]==ORDER_STATUS_DEFERRED_PAYMENT){//외상일때 환불 신청 X
					$update_str ="";
				}else{
					$update_str = " , cc_date = NOW(), refund_status='".ORDER_STATUS_REFUND_APPLY."'  , fa_date=NOW() ";
				}
			}

			$sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".$status."' , ca_date = NOW(), update_date = NOW() , claim_fault_type = '".fetch_order_status_div('DR','CA',"type",$reason_code)."', claim_group='".$claim_group."' $update_str
			where  od_ix='".$od_ix."' ";
			$db->query($sql);

			$STATUS_MESSAGE = "[".fetch_order_status_div('DR','CA',"title",$reason_code)."]".$msg;
			set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$od_ix,$order_details[$i][pid],$reason_code,"","",$c_type);
			/*
			if($order_details[$i]["pcnt"] > $apply_cnt[$order_details[$i]["od_ix"]]){//요청수량이 작을때넘김!
				if(fetch_order_status_div('DR','CA',"type",$reason_code)=="B"){
					$Coupondata["oid"]=$order_details[$i][oid];
					$Coupondata["od_ix"]=$order_details[$i][od_ix];
					$CouponReturn = orderUseCouponReturnCheck($Coupondata,$apply_cnt[$product_info[$i][od_ix]]);

					$use_coupon_regist_ix=array();
					//사용할수 있는 쿠폰
					for($z=0;$z<count($CouponReturn["coupon_dc_info"]);$z++){
						$use_coupon_regist_ix[] = $CouponReturn["coupon_dc_info"][$z]["discount_code"];
					}

					//기존 주문 사용할수 없는 쿠폰 추출
					$sql="select * from shop_order_detail_discount where oid = '".$order_details[$i][oid]."' and od_ix='".$order_details[$i][od_ix]."' and dc_ix not in ('".implode("','",$use_coupon_regist_ix)."') and dc_type in ('CP','SCP') ";
					$db->query($sql);
					$no_coupon = $db->fetchall("object");

					for($z=0;$z<count($no_coupon);$z++){
						//기존쿠폰금액 더해주기
						$sql="update ".TBL_SHOP_ORDER_DETAIL." set pt_dcprice = pt_dcprice+'".$no_coupon[$z]["dc_price"]."' where  od_ix='".$order_details[$i][od_ix]."' ";
						$db->query($sql);

						$sql="update ".TBL_SHOP_ORDER_DETAIL." set pt_dcprice = pt_dcprice-'".$no_coupon[$z]["dc_price"]."' where od_ix='".$od_ix."' ";
						$db->query($sql);

						$sql="update shop_order_detail_discount set od_ix='".$od_ix."' where oid = '".$order_details[$i][oid]."' and od_ix='".$order_details[$i][od_ix]."' and dc_type ='".$no_coupon[$z]["dc_type"]."' ";
						$db->query($sql);
					}
				}
			}
			*/

			if($status==ORDER_STATUS_CANCEL_COMPLETE){
				//적립한 마일리지 취소
				InsertReserveInfo($order_details[$i][user_code],$order_details[$i][oid],$od_ix,$id,$reserve,'9','2',$etc,'mileage',$admininfo);	//마일리지,적립금 통합용 함수 2013-06-19 이학봉	적립대기->적립취소
				//inventory.lib.php에

				if(fetch_order_status_div('DR','CA',"type",$reason_code)=="S"){
					//입금후취소완료 셀러판매신용점수 차감
					//셀러판매신용점수 추가 시작 2014-06-15 이학봉
					InsertPenaltyInfo('2','4',$order_details[$i][oid],$order_details[$i][od_ix],$penalty,$order_details[$i]["company_id"],'입금후취소 판매신용점수 차감',$_SESSION["admininfo"],'cc');
					//셀러판매신용점수 추가 끝 2014-06-15 이학봉

					/*
					define("POINT_USE_STATE_IC","1"); // 입금완료
					define("POINT_USE_STATE_DC","2"); // 배송완료
					define("POINT_USE_STATE_BF","3"); // 구매확정
					define("POINT_USE_STATE_CC","4"); // 입금후 취소
					define("POINT_USE_STATE_EI","5"); // 교환승인
					define("POINT_USE_STATE_RI","6"); // 반품승인
					define("POINT_USE_STATE_DD","7"); // 입금완료후 발송지연
					define("POINT_USE_STATE_DDA","8"); // 입금완료후 추가 발송지연
					define("POINT_USE_STATE_ETC","9"); // 기타
					*/
					insertProductPoint('2', POINT_USE_STATE_CC, $order_details[$i]['oid'], $order_details[$i]['od_ix'], $point, $order_details[$i]["pid"], '입금후취소 상품점수 차감', $_SESSION["admininfo"], 'cc');
				}

				UpdateSellingCnt($order_details[$i]);

				//후불 외상시 미수금 처리
				if($order_details[$i]['ostatus']==ORDER_STATUS_DEFERRED_PAYMENT){
					$noaccept_data="";
					$noaccept_data['oid']=$order_details[$i]['oid'];
					$noaccept_data['msg']="<br/>-".date('Ymd')." ".$order_details[$i]['pname']." 취소";
					$noaccept_data['order_cancel_price']=$order_details[$i]['ptprice']-$order_details[$i]['member_sale_price']-$order_details[$i]['use_coupon'];
					setNoacceptOrderCancel($noaccept_data);
				}

				//사용한 상품쿠폰 돌려주기
				if($coupon_data['restore_cc2'] == "Y"){
					$UseCoupon["oid"]=$order_details[$i]['oid'];
					$UseCoupon["od_ix"]=$od_ix;
					$returnCoupon = orderUseCouponReturn($UseCoupon);
				}

			}

		}elseif($status==ORDER_STATUS_RETURN_APPLY||$status==ORDER_STATUS_RETURN_ING){//반품요청,반품승인

			if($i==0){//한번만~~

				$claim_type = "R";

				//order_type (1:정상주문,2:교환,3:반품(역배송),4:수거)
				if($send_yn=="N"){//아직 상품을 안보냈을때
					if($send_type=="2"){//지정택배방문요청(셀러업체와 계약된 택배업체 방문수령 수거)
						$order_type="4";
					}else{//직접발송(구매자께서 개별로 배송할 경우)
						$order_type="3";
					}

					if(! empty($return_zip2)){
						$return_zip_all = $return_zip1."-".$return_zip2;
					}else{
						$return_zip_all = $return_zip1;
					}

					$return_zip = $return_zip_all;
					$delivery_method="";
					$quick="";
					$deliverycode="";
					$delivery_pay_type="";
				}else{//발송했을때
					$send_type="1";
					$order_type="3";
					$return_rname="";
					$return_rtel="";
					$return_zip="";
					$return_addr1="";
					$return_addr2="";
					$return_delivery_msg="";
				}

				//반품정보 입력
				$sql="insert into shop_order_detail_deliveryinfo (odd_ix,oid,od_ix,order_type,rname,rtel,rmobile,rmail,zip,addr1,addr2,msg,due_date,delivery_method,quick,invoice_no,send_yn,send_type,delivery_pay_type,add_delivery_price,regdate) values('','".$order_details[$i]['oid']."','".$od_ix."','".$order_type."','".$return_rname."','".$return_rtel."','".$return_rtel."','','".$return_zip."','".$return_addr1."','".$return_addr2."','".$return_delivery_msg."','','".$delivery_method."','".$quick."','".$deliverycode."','".$send_yn."','".$send_type."','".$delivery_pay_type."','0',NOW())";
				$db->query($sql);
				$return_odd_ix = $db->insert_id();
			}


			$sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".$status."', update_date = NOW(), ra_date=NOW(), claim_fault_type = '".fetch_order_status_div('DC','RA',"type",$reason_code)."', odd_ix='".$return_odd_ix."' , claim_group='".$claim_group."'
			where  od_ix='".$od_ix."' ";
			$db->query($sql);

			// 크리마 반품요청 상태 전송(작업자 에이치엠파트너즈 임우철 팀장 2021.05.26)
			if($status==ORDER_STATUS_RETURN_APPLY) {

				$url = "https://stg.barrelmade.co.kr/shop/crema/orderReSend?ora_ix=".$od_ix;
				//$url = "https://testbarrel.forbiz.co.kr/shop/crema/orderReSend?ora_ix=".$od_ix;

				$cmd = sprintf("curl -X GET \"%s\"", $url);
				$output = shell_exec($cmd);

				//echo $output;
			}
			// // 크리마 반품요청 상태 전송(작업자 에이치엠파트너즈 임우철 팀장 2021.05.26)

			$STATUS_MESSAGE = "[".fetch_order_status_div('DC','RA',"title",$reason_code)."]".$msg;
			set_order_status($order_details[$i][oid],ORDER_STATUS_RETURN_APPLY,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$od_ix,$order_details[$i][pid],$reason_code,"","",$c_type);

			if($status==ORDER_STATUS_RETURN_ING){
				set_order_status($order_details[$i][oid],ORDER_STATUS_RETURN_ING,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$od_ix,$order_details[$i][pid],$reason_code,"","",$c_type);
			}

			/*
			if($order_details[$i]["pcnt"] > $apply_cnt[$order_details[$i]["od_ix"]]){//요청수량이 작을때넘김!
				if(fetch_order_status_div('DC','RA',"type",$reason_code)=="B"){
					$Coupondata["oid"]=$order_details[$i][oid];
					$Coupondata["od_ix"]=$order_details[$i][od_ix];
					$CouponReturn = orderUseCouponReturnCheck($Coupondata,$apply_cnt[$product_info[$i][od_ix]]);

					$use_coupon_regist_ix=array();
					//사용할수 있는 쿠폰
					for($z=0;$z<count($CouponReturn["coupon_dc_info"]);$z++){
						$use_coupon_regist_ix[] = $CouponReturn["coupon_dc_info"][$z]["discount_code"];
					}

					//기존 주문 사용할수 없는 쿠폰 추출
					$sql="select * from shop_order_detail_discount where oid = '".$order_details[$i][oid]."' and od_ix='".$order_details[$i][od_ix]."' and dc_ix not in ('".implode("','",$use_coupon_regist_ix)."') and dc_type in ('CP','SCP') ";
					$db->query($sql);
					$no_coupon = $db->fetchall("object");

					for($z=0;$z<count($no_coupon);$z++){
						//기존쿠폰금액 더해주기
						$sql="update ".TBL_SHOP_ORDER_DETAIL." set pt_dcprice = pt_dcprice+'".$no_coupon[$z]["dc_price"]."' where  od_ix='".$order_details[$i][od_ix]."' ";
						$db->query($sql);

						$sql="update ".TBL_SHOP_ORDER_DETAIL." set pt_dcprice = pt_dcprice-'".$no_coupon[$z]["dc_price"]."' where od_ix='".$od_ix."' ";
						$db->query($sql);

						$sql="update shop_order_detail_discount set od_ix='".$od_ix."' where oid = '".$order_details[$i][oid]."' and od_ix='".$order_details[$i][od_ix]."' and dc_type ='".$no_coupon[$z]["dc_type"]."' ";
						$db->query($sql);
					}
				}
			}
			*/
		}elseif($status==ORDER_STATUS_EXCHANGE_APPLY||$status==ORDER_STATUS_EXCHANGE_ING){//교환요청

			if($i==0){//한번만~~

				$claim_type = "E";

				//order_type (1:정상주문,2:교환,3:반품(역배송),4:수거)
				if($send_yn=="N"){//아직 상품을 안보냈을때
					if($send_type=="2"){//지정택배방문요청(셀러업체와 계약된 택배업체 방문수령 수거)
						$order_type="4";
					}else{//직접발송(구매자께서 개별로 배송할 경우)
						$order_type="3";
					}

					if(! empty($return_zip2)){
						$return_zip_all = $return_zip1."-".$return_zip2;
					}else{
						$return_zip_all = $return_zip1;
					}

					$return_zip = $return_zip_all;
					$delivery_method="";
					$quick="";
					$deliverycode="";
					$delivery_pay_type="";
				}else{//발송했을때
					$send_type="1";
					$order_type="3";
					$return_rname="";
					$return_rtel="";
					$return_zip="";
					$return_addr1="";
					$return_addr2="";
					$return_delivery_msg="";
				}

				$delivery_type=$order_details[$i][delivery_type];//통합배송여부 1:통합배송, 2:입점업체배송
				$delivery_package=$order_details[$i][delivery_package];//Y:개별배송 N:묶음배송
				$delivery_policy="9";//1:무료배송 2:고정배송비 3:주문결제금액 할인 4:수량별할인 5:출고지별 배송비 6: 상품1개단위 배송비 9:클레임배송
				$_delivery_method=$order_details[$i][delivery_method];
				$delivery_pay_method="1";//배송정책구분값(선불:1, 착불:2)
				$delivery_addr_use=$order_details[$i][delivery_addr_use];
				$factory_info_addr_ix=$order_details[$i][factory_info_addr_ix];

				if($total_apply_price > 0)//+환불
					$refund_status=ORDER_STATUS_REFUND_READY;
				else
					$refund_status="";

				//배송비처리하기!
				$sql = "insert into shop_order_delivery (ode_ix,oid,company_id,ori_company_id,delivery_type,delivery_package,delivery_policy,delivery_method,delivery_pay_type,delivery_addr_use,factory_info_addr_ix,pid,delivery_price,delivery_dcprice,regdate) values ('','".$order_details[$i][oid]."','".$order_details[$i][company_id]."','".$claim_group."','".$delivery_type."','".$delivery_package."','".$delivery_policy."','".$_delivery_method."','".$delivery_pay_method."','".$delivery_addr_use."','".$factory_info_addr_ix."','','0','0',NOW())"; //".abs($total_apply_delivery_price)."
				$db->query($sql);
				$ode_ix = $db->insert_id();


				//반품정보 입력
				$sql="insert into shop_order_detail_deliveryinfo (odd_ix,oid,od_ix,order_type,rname,rtel,rmobile,rmail,zip,addr1,addr2,msg,due_date,delivery_method,quick,invoice_no,send_yn,send_type,delivery_pay_type,add_delivery_price,regdate) values('','".$order_details[$i][oid]."','".$od_ix."','".$order_type."','".$return_rname."','".$return_rtel."','".$return_rtel."','','".$return_zip."','".$return_addr1."','".$return_addr2."','".$return_delivery_msg."','','".$delivery_method."','".$quick."','".$deliverycode."','".$send_yn."','".$send_type."','".$delivery_pay_type."','0',NOW())";
				$db->query($sql);
				$return_odd_ix = $db->insert_id();

				if(! empty($zip2)){
					$return_zip_all = $zip1."-".$zip2;
				}else{
					$return_zip_all = $zip1;
				}

				$sql="insert into shop_order_detail_deliveryinfo (odd_ix,oid,od_ix,order_type,rname,rtel,rmobile,rmail,zip,addr1,addr2,msg,due_date,delivery_method,quick,invoice_no,send_yn,send_type,add_delivery_price,regdate) values('','".$order_details[$i][oid]."','','2','".$rname."','".$rtel."','".$rtel."','','".$return_zip_all."','".$addr1."','".$addr2."','".$delivery_msg."','','','','','','','0',NOW())";
				$db->query($sql);
				$delivery_odd_ix = $db->insert_id();
			}


			//교환요청상품관련 처리!!!
			$sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".$status."', update_date = NOW(), dc_date=IFNULL(dc_date,NOW()), bf_date=IFNULL(bf_date,NOW()), ea_date=NOW(), claim_fault_type = '".fetch_order_status_div('DC','RA',"type",$reason_code)."', refund_status='".$refund_status."' , odd_ix='".$return_odd_ix."', claim_group ='".$claim_group."', exchange_delivery_type='".$exchange_delivery_type."' where  od_ix='".$od_ix."' ";
			$db->query($sql);


			$STATUS_MESSAGE = "[".fetch_order_status_div('DC','RA',"title",$reason_code)."]".$msg;
			set_order_status($order_details[$i][oid],ORDER_STATUS_EXCHANGE_APPLY,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$od_ix,$order_details[$i][pid],$reason_code,"","",$c_type);

			if($status==ORDER_STATUS_EXCHANGE_ING){
				set_order_status($order_details[$i][oid],ORDER_STATUS_EXCHANGE_ING,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$od_ix,$order_details[$i][pid],$reason_code,"","",$c_type);
			}

			foreach($ea_pdata[$order_details[$i]["od_ix"]] as $json_data){
				$pinfo = array();
				$pinfo = json_decode(urldecode($json_data),true);

				//동일주문 복사
				$new_od_ix = orderSeparate($od_ix,"",true);

				//교환배송상품관련 처리!!!

				//할인금액 처리
				$sum_dc_price = 0;
                $sql ="select sum(dc_price) as sum_dc_price from shop_order_detail_discount where od_ix='" . $new_od_ix . "'";
                $db->query($sql);
                $db->fetch();
                $sum_dc_price = $db->dt['sum_dc_price'];

				$sql="update ".TBL_SHOP_ORDER_DETAIL." set
						cid = '".$pinfo["cid"]."',
						pid = '".$pinfo["pid"]."',
						brand_code = '".$pinfo["brand_code"]."',
						brand_name = '".$pinfo["brand_name"]."',
						pcode = '".$pinfo["pcode"]."',
						barcode = '".$pinfo["barcode"]."',
						product_type = '".$pinfo["product_type"]."',
						pname = '".$pinfo["pname"]."',
						paper_pname = '".$pinfo["paper_pname"]."',
						trade_company = '".$pinfo["trade_company"]."',
						trade_company_name = (select com_name from common_company_detail where company_id = '".$pinfo["trade_company"]."'),
						gid = '".$pinfo["gid"]."',
						gu_ix = '".$pinfo["gu_ix"]."',
						stock_use_yn = '".$pinfo["stock_use_yn"]."',
						pcnt = '".$pinfo["claim_apply_cnt"]."',
						option_id = '".$pinfo["option_id"]."',
						option_text = '".$pinfo["option_text"]."',
						option_etc='' ,
						option_price ='".$pinfo["option_price"]."',
						coprice = '".$pinfo["coprice"]."',
						listprice = '".$pinfo["listprice"]."',
						psprice = '".$pinfo["psprice"]."',
						dcprice = '".$pinfo["dcprice"]."',
						
						reserve = '".$pinfo["reserve"]."',
						status = '".ORDER_STATUS_EXCHANGE_READY."',
						odd_ix='".$delivery_odd_ix."',
						claim_delivery_od_ix='".$od_ix."',
						claim_group ='".$claim_group."',
						md_code = '".$pinfo["md_code"]."',
						one_commission = '".$pinfo["one_commission"]."',
						commission = '".$pinfo["commission"]."',
						ori_company_id='".$claim_group."',
						delivery_policy = '".$delivery_policy."',
						delivery_pay_method='".$delivery_pay_method."',
						delivery_type = '".$delivery_type."',
						delivery_package='".$delivery_package."',
						delivery_method='".$_delivery_method."',
						delivery_addr_use='".$delivery_addr_use."',
						factory_info_addr_ix='".$factory_info_addr_ix."',
						ode_ix='".$ode_ix."',
						delivery_company_id='',
						delivery_pi_ix='',
						delivery_ps_ix='',
						delivery_basic_ps_ix='',
						delivery_status = '',
						refund_status = '',
						quick = '',
						invoice_no = '',
						input_type = '',
						output_type = '',
						claim_fault_type = '',
						is_check_picking = '',
						is_check_delivery = '',
						return_product_state='',
						accounts_status = '',
						ac_ix = '',
						refund_ac_ix = '',
						update_date = NOW(),
						dr_date=NULL,
						di_date=NULL,
						ac_date = NULL,
						dc_date = NULL,
						bf_date=NULL,
						ea_date = NULL,
						ra_date = NULL,
						fa_date = NULL,
						fc_date = NULL,
						erp_link_date = NULL,
						due_date = ''
					where  od_ix='".$new_od_ix."' ";

				$db->query($sql);

				//같은 상품은 orderSeparate 함수에서 이미 처리됨!
				if($pinfo["ec_select_type"]=="D"){//다른상품을때 할인정보 넣기!
					//delete shop_order_detail_discount
					$sql="delete from shop_order_detail_discount where od_ix='".$new_od_ix."'";
					$db->query($sql);

					//insert shop_order_detail_discount
					if(count($pinfo["discount_desc"]) > 0){
						foreach($pinfo["discount_desc"] as $dc_info){
							//dc_price,dc_price_admin,dc_price_seller,dc_criterion 다시 확인후 처리!! 그리고 주석 삭제!

							$dc_type=$dc_info["discount_type"];
							$dc_title=$_DISCOUNT_TYPE[$dc_info["discount_type"]];
							$dc_price=$dc_info["discount_price"];//할인가격( *수량)

							if($dc_info["discount_value_type"]=="1"){//할인타입//1:% 2:원
								$dc_rate=$dc_info["discount_value"];
								$dc_rate_admin=$dc_info["headoffice_discount_value"];
								$dc_rate_seller=$dc_info["seller_discount_value"];
								$dc_price_admin=round(($dc_price*$dc_rate_admin > 0?$dc_price*$dc_rate_admin/$dc_rate:0));		//Warning: Division by zero in /home/dev/www/shop/card_insert.php on line 237 이부분 에러뜸
								$dc_price_seller=$dc_price-$dc_price_admin;
							}else{
								$dc_rate=0;
								$dc_rate_admin=0;
								$dc_rate_seller=0;
								$dc_price_admin=$dc_info["headoffice_discount_value"];
								$dc_price_seller=$dc_info["seller_discount_value"];
							}

							$dc_criterion="";//아직 없음
							$dc_msg=$dc_info["discount_msg"];
							$dc_ix=$dc_info["discount_code"];

							$sql = "insert into shop_order_detail_discount (oid,od_ix,dc_type,dc_title,dc_rate,dc_price,dc_rate_admin,dc_price_admin,dc_rate_seller,dc_price_seller,dc_criterion,dc_msg,dc_ix,regdate) values('".$order_details[$i][oid]."','".$new_od_ix."','".$dc_type."','".$dc_title."','".$dc_rate."','".$dc_price."','".$dc_rate_admin."','".$dc_price_admin."','".$dc_rate_seller."','".$dc_price_seller."','".$dc_criterion."','".$dc_msg."','".$dc_ix."',NOW())";
							$db->query($sql);
						}
					}

				}else{//동일 상품일떄!
					/*
					if(fetch_order_status_div('DC','EA',"type",$reason_code)=="B"){
						$Coupondata["oid"]=$order_details[$i][oid];
						$Coupondata["od_ix"]=$od_ix;
						$CouponReturn = orderUseCouponReturnCheck($Coupondata,$pinfo["claim_apply_cnt"]);

						$use_coupon_regist_ix=array();
						//사용할수 있는 쿠폰
						for($z=0;$z<count($CouponReturn["coupon_dc_info"]);$z++){
							$use_coupon_regist_ix[] = $CouponReturn["coupon_dc_info"][$z]["discount_code"];
						}

						//기존 주문 사용할수 없는 쿠폰 추출
						$sql="select * from shop_order_detail_discount where oid = '".$order_details[$i][oid]."' and od_ix='".$od_ix."' and dc_ix not in ('".implode("','",$use_coupon_regist_ix)."') and dc_type in ('CP','SCP') ";
						$db->query($sql);
						$no_coupon = $db->fetchall("object");

						for($z=0;$z<count($no_coupon);$z++){
							//기존쿠폰금액 더해주기
							$sql="update ".TBL_SHOP_ORDER_DETAIL." set pt_dcprice = pt_dcprice+'".$no_coupon[$z]["dc_price"]."' where  od_ix='".$od_ix."' ";
							$db->query($sql);

							$sql="update ".TBL_SHOP_ORDER_DETAIL." set pt_dcprice = pt_dcprice-'".$no_coupon[$z]["dc_price"]."' where od_ix='".$new_od_ix."' ";
							$db->query($sql);

							$sql="update shop_order_detail_discount set od_ix='".$new_od_ix."' where oid = '".$order_details[$i][oid]."' and od_ix='".$od_ix."' and dc_type ='".$no_coupon[$z]["dc_type"]."' ";
							$db->query($sql);
						}
					}else{
						//기존 주문 사용할수 없는 쿠폰 추출
						$sql="select * from shop_order_detail_discount where oid = '".$order_details[$i][oid]."' and od_ix='".$od_ix."' and dc_type in ('CP','SCP') ";
						$db->query($sql);
						$no_coupon = $db->fetchall("object");

						for($z=0;$z<count($no_coupon);$z++){
							//기존쿠폰금액 더해주기
							$sql="update ".TBL_SHOP_ORDER_DETAIL." set pt_dcprice = pt_dcprice+'".$no_coupon[$z]["dc_price"]."' where od_ix='".$od_ix."' ";
							$db->query($sql);

							$sql="update ".TBL_SHOP_ORDER_DETAIL." set pt_dcprice = pt_dcprice-'".$no_coupon[$z]["dc_price"]."' where od_ix='".$new_od_ix."' ";
							$db->query($sql);

							$sql="update shop_order_detail_discount set od_ix='".$new_od_ix."' where oid = '".$order_details[$i][oid]."' and od_ix='".$od_ix."' and dc_type ='".$no_coupon[$z]["dc_type"]."' ";
							$db->query($sql);
						}
					}
					*/
				}
			}
		}
	}

	//추가금액일떄


	if($payment_type =="add"){//-추가금액
		$etc_info["claim_type"]=$claim_type;
		$etc_info["claim_group"]=$claim_group;

		if($total_apply_tax_price > 0 || $total_apply_tax_free_price > 0){
			if($total_apply_tax_price < $total_apply_tax_free_price){
				$_TOTAL_APPLY_TAX_PRICE = $total_apply_price;
				$_TOTAL_APPLY_TAX_FREE_PRICE = 0;
			}else{
				$_TOTAL_APPLY_TAX_PRICE = 0;
				$_TOTAL_APPLY_TAX_FREE_PRICE = $total_apply_price;
			}
		}else{
			$_TOTAL_APPLY_TAX_PRICE = $total_apply_tax_price;
			$_TOTAL_APPLY_TAX_FREE_PRICE = $total_apply_tax_free_price;
		}

		table_order_payment_data_creation($oid,'A','IR','',$_TOTAL_APPLY_TAX_PRICE,$_TOTAL_APPLY_TAX_FREE_PRICE,$total_apply_price,$etc_info);

		table_order_price_data_creation($oid,'','','A','P',$total_apply_price,0,strip_tags(getOrderStatus($status))." 추가금액",0,0,0,$claim_group);
	}



	//전체 취소일떄
	if($apply_all=="Y"){
		foreach($apply_all_delivery_price as $company_id => $total_apply_delivery_price){

			$claim_group = $com_claim[$company_id][claim_group];
			$delivery_type = $com_claim[$company_id][delivery_type];

			//클래임 배송비 저장하기!
			$sql="insert into shop_order_claim_delivery(ocde_ix,oid,company_id,delivery_type,claim_group,delivery_price,regdate) values('','$oid','$company_id','$delivery_type','$claim_group','$total_apply_delivery_price',NOW())";
			$db->query($sql);
		}
	}else{
		//클래임 배송비 저장하기!
		$sql="insert into shop_order_claim_delivery(ocde_ix,oid,company_id,delivery_type,claim_group,delivery_price,regdate) values('','$oid','$company_id','$delivery_type','$claim_group','$total_apply_delivery_price',NOW())";
		$db->query($sql);
	}


	//메일보내기!
	if($status==ORDER_STATUS_CANCEL_COMPLETE){

		//새로 새od_ix 가 생겨날수 잇어서 다시 select!
		$sql="select od.*, o.status as ostatus, o.user_code, pid as id from ".TBL_SHOP_ORDER." o ,".TBL_SHOP_ORDER_DETAIL." od where o.oid=od.oid and od.od_ix in ('".implode("','",$od_ix_array)."') ";
		$db->query($sql);
		$order_details = $db->fetchall("object");

		$mdb->query("select * from ".TBL_SHOP_ORDER."  WHERE oid='".$oid."' ");
		$order = $mdb->fetch();

        $sql="select * from shop_order_payment where pay_type = 'G' and pay_status = '".ORDER_STATUS_INCOM_COMPLETE."' and oid = '".$oid."' limit 1";
        $db->query($sql);
        $db->fetch();
        $mail_info['payment_type'] = getMethodStatus($db->dt['method']);

		if($order_details[0][order_from] == 'self'){
			$mail_info[mem_name] = $order[bname];
			$mail_info[mem_mail] = $order[bmail];
			$mail_info[mem_id] = $order[bname];
			$mail_info[mem_mobile] = $order[bmobile];
			$mail_info[msg_code]	=	'402';//MSG 코드 402 : 주문취소
            $mail_info['total_refund_amount'] = $total_apply_price;		    // 환불 예정 금액
            $mail_info['cancel_date'] = date('Y-m-d');		    // 취소요청일
            $mail_info['cancel_reason'] = $STATUS_MESSAGE;		    // 취소사유

			@sendMessageByStep('order_cancel', $mail_info);
		}
	}

	echo("<script language='javascript' src='../js/message.js.php'></script>
	<script language='javascript'>
		//show_alert('정상적으로 요청되었습니다.');
		alert('정상적으로 처리되었습니다.');

		if(top.window.dialogArguments) {
			opener=top.window.dialogArguments;
			top.window.returnValue=true;
		} else {
			top.opener.document.location.reload();
		}
		top.window.close();
	</script>");
	exit;
}



// 부분취소!
if($act=="part_cancel"){
	if(!empty($total_refund_price) || $direct_pg=="Y"){

		//TODO: call PG cancel process
		include("./cancelService/cancel.php");
		$cancel = new cancel();
		$alert_msg="";

		//우선 PG만 처리하기!!!!!

		// ---------- 주문 기본 정보 ----------
		$sql = "select * from ".TBL_SHOP_ORDER." where oid = '".$oid."'";
		$db->query($sql);
		$db->fetch();
		$order = $db->dt;
		$user_code = $db->dt[user_code];
		$cancelData['mail'] = $db->dt[bmail];
		$cancelData['name'] = $db->dt[bname];
		$cancelData['mobile'] = $db->dt[bmobile];
        $cancelData['user_code'] = $db->dt[user_code];


        // ---------- 환불 신청 사유 ----------
		$sql = "SELECT status_message, regdate FROM shop_order_status WHERE oid='".$oid."' AND ifnull(reason_code,'') != '' ORDER BY regdate DESC LIMIT 0,1";
		$db->query($sql);
		$db->fetch();
		//환불 사유가 길어서 환불요청 실패 이슈로 빈값처리
		//$cancelData['reason'] = $db->dt["status_message"];	   // 환불 사유
		$cancelData['reason'] = ' ';	   // 환불 사유
        $cancelData['refund_apply_date'] = $db->dt["regdate"]; // 환불 신청일


		// ---------- 환불 계좌 정보  ----------
		// ISMS 환불계좌번호 저장 제외
        /*if($user_bank_ix != ""){

			$sql="SELECT bank_ix, ucode, bank_code, use_yn, is_basic, regdate, editdate,
					AES_DECRYPT(UNHEX(bank_name),'".$db->ase_encrypt_key."') as bank_name,
					AES_DECRYPT(UNHEX(bank_number),'".$db->ase_encrypt_key."') as bank_number,
					AES_DECRYPT(UNHEX(bank_owner),'".$db->ase_encrypt_key."') as bank_owner 
				FROM shop_user_bankinfo WHERE bank_ix='".$user_bank_ix."'";
			$db->query($sql);
			$db->fetch();
			$cancelData['bank_code'] = $db->dt["bank_code"];
			$cancelData['bank_number'] = trim($db->dt["bank_number"]);
			$cancelData['bank_owner'] = $db->dt["bank_owner"];

		}else{

			$sql="update shop_order set refund_bank=HEX(AES_ENCRYPT('".$refund_bank_code."|".$refund_bank_number."','".$db->ase_encrypt_key."')), refund_bank_name=HEX(AES_ENCRYPT('".$refund_bank_owner."','".$db->ase_encrypt_key."')) WHERE oid='".$oid."'";
			$db->query($sql);
			$cancelData['bank_code'] = $refund_bank_code;
			$cancelData['bank_number'] = trim($refund_bank_number);
			$cancelData['bank_owner'] = $refund_bank_owner;
		}*/

		$cancelData['bank_code'] = $refund_bank_code;
		$cancelData['bank_number'] = trim($refund_bank_number);
		$cancelData['bank_owner'] = $refund_bank_owner;

		// ---------- 결제 수단에 따라 LOOP ----------
		$pay_etcinfo = array();
        // $refund_price {[4]=>   string(5) "11340"}
        // 가상계좌 => 금액
		foreach($refund_price as $method => $refund_method_price){

			$total_product_price    = $tax_product_price[$method] + $tax_free_product_price[$method];
			$total_delivery_price   = $delivery_price[$method];
			$reserve_product_price  = 0;
			$reserve_delivery_price = 0;
			$save_product_price     = 0;
			$save_delivery_price    = 0;


            // ---------- PC 수동 처리 하겠다. 어드민에선 환불 정보 셋팅 만 ----------
			if($refund_method_price > 0  || $direct_pg=="Y"){

				if($method == ORDER_METHOD_PAYCO ||
				   $method == ORDER_METHOD_CARD ||
				   $method == ORDER_METHOD_PHONE ||
				   $method == ORDER_METHOD_ICHE ||
				   $method == ORDER_METHOD_MOBILE ||
				   $method == ORDER_METHOD_VBANK ||
				   $method == ORDER_METHOD_ASCROW ||
                   $method == ORDER_METHOD_NPAY ||
				   $method == ORDER_METHOD_EXIMBAY ||
                   $method == ORDER_METHOD_TOSS ||
				   $method == ORDER_METHOD_KAKAOPAY ||
				   $direct_pg =="Y" ||
				   $use_deposit == "1"){

					// PG 수동처리
					if ($direct_pg == "Y") {
						$STATUS_MESSAGE = "PG 수동처리";
						$pg_result[$method]["result"] = "success";
						$pg_result[$method]["price"] = $refund_method_price;

                    // 예치금 환불처리
					}else if($use_deposit == "1" && ($method == ORDER_METHOD_BANK || $method == ORDER_METHOD_VBANK || $method==ORDER_METHOD_PHONE)){
						$STATUS_MESSAGE = "예치금 환불처리";
						$pg_result[$method]["result"] = "success";
						$pg_result[$method]["price"] = $refund_method_price;

					}elseif($method == ORDER_METHOD_CART_COUPON){
						$pg_result[$method]["result"] = "success";
						$pg_result[$method]["price"] = $refund_method_price;

					// 기타
					} else {

						$sql = "SELECT SUM(case when pay_type='F' then '0' else payment_price end) as real_price, 
                                        SUM(case when pay_type='F' then -payment_price else payment_price end) as payment_price, 
                                        SUM(case when pay_type='F' then 1 else 0 end) as refund_cnt, 
                                        tid, settle_module, escrow_use, authcode 
                                  from shop_order_payment 
                                  where oid = '".$oid."' 
                                  and method = '".$method."' 
                                  and pay_status = 'IC' 
                                  group by tid having payment_price >= ".$refund_method_price." ";
						$db->query($sql);
						$db->fetch();

						/*
						*	$pay_etcinfo ( 결제 완료후 shop_order_payment 테이블에 저장하기위한 변수 )
						*	이변수는 부분취소시 tid 로 남은 결제금액을 뽑기 위해서 저장해야 한다
						*/
						$pay_etcinfo[$method]["tid"]           	= $cancelData['tid'] = $db->dt["tid"];
						$pay_etcinfo[$method]["settle_module"] 	= $cancelData['settle_module'] = $db->dt["settle_module"];
						$pay_etcinfo[$method]["authcode"]     	= $cancelData['authcode'] = $db->dt["authcode"];

						$cancelData["refund_cnt"] 				= $db->dt["refund_cnt"];
						$cancelData["escrow_use"] 				= $db->dt["escrow_use"];
						$cancelData["cancel_amount"] 			= $refund_method_price;
						$cancelData["cancel_tax_amount"] 		= $tax_product_price[$method]+$delivery_price[$method];
						$cancelData["cancel_tax_free_amount"] 	= $tax_free_product_price[$method];
						$cancelData["real_price"] 				= $db->dt["real_price"];
						$cancelData["remain_price"] 			= $db->dt["payment_price"];
						$cancelData["method"] 					= $method;
						$cancelData["oid"] 						= $oid;
						$cancelData["cancel_msg"] 				= "부분취소";
						$cancelData["company_id"] 				= $_SESSION["admininfo"]["company_id"];

						// kspay 용 정보 추가 20160317
						$cancelData['pname']	= $db->dt[pname];	// 상품명
						$cancelData['bmobile']	= str_replace("-","",$db->dt[bmobile]); // 핸드폰번호

						if ($db->dt["escrow_use"] == "Y") {
							$sql="select status from ".TBL_SHOP_ORDER_DETAIL." where od_ix in ('".implode("','",$od_ix)."') ";
							$db->query($sql);
							$order_details_status = $db->fetchall();

							//for($i=0;$i < count($order_details);$i++){
							//}
							// KCP기본설정
								/*프로세스 요청의 종류를 구분하는 변수 에스크로 상태변경 페이지의 경우에 반드시 ‘mod_escrow’로 설정*/
								$cancelData["req_tx"]		= "mod_escrow";
								if($order_details_status[0]['status'] == "CC"){
									$cancelData["mod_type"]		= "STE2";	// 즉시취소 (배송 전 취소)
								} else if($order_details_status[0]['status'] == "RC"){
									$cancelData["mod_type"]		= "STE9_VP";	// 가상계좌 구매 확인 후 부분환불
								} else {
									$cancelData["mod_type"]		= "STE4";	// 취소 (배송 후 취소)
								}

								/*에스크로 상태 변경 요청의 구분 변수*/
								//$cancelData["mod_type"]		= "STE1";	// 배송시작
								//$cancelData["mod_type"]		= "STE2";	// 즉시취소 (배송 전 취소)
								//$cancelData["mod_type"]		= "STE3";	// 정산보류
								//$cancelData["mod_type"]		= "STE4";	// 취소 (배송 후 취소)
								//$cancelData["mod_type"]		= "STE5";	// 발급계좌해지(가상계좌의 경우에만 사용)
								//$cancelData["mod_type"]		= "STE9_A";	// 계좌이체 구매 확인 후 취소
								//$cancelData["mod_type"]		= "STE9_AP";// 계좌이체 구매 확인 후 부분취소
								//$cancelData["mod_type"]		= "STE9_AR";// 계좌이체 구매 확인 후 환불
								//$cancelData["mod_type"]		= "STE9_V";	// 가상계좌 구매 확인 후 환불
								//$cancelData["mod_type"]		= "STE9_VP";// 가상계좌 구매 확인 후 부분환불

								/*결제 완료 후 결제 건에 대한 고유한 값 해당 값으로 거래건의 상태를 조회/변경/취소가 가능하니 결과 처리 페이지에서 tno를 반드시 저장해주시기 바랍니다. ※ 거래고유번호 전체로 사용 하시기 바랍니다.(임의의 숫자나 파싱하여 사용 불가)*/
								$cancelData["tno"]			= $cancelData['tid'];
							// // KCP기본설정

							/*가상계좌의 경우, 고객이 환불을 받을 때 환불 받을 고객의 계좌번호 입력하는 변수*/
							$cancelData["mod_account"]	= trim($refund_bank_number);

							/*가상계좌의 경우, 고객이 환불을 받을 때 환불 받을 계좌의 계좌주 명 입력하는 변수*/
							$cancelData["mod_depositor"]= $refund_bank_owner;

							/*가상계좌의 경우, 고객이 환불을 받을 때 환불 받을 계좌의 은행코드를 입력하는 변수*/
							$cancelData["mod_bankcode"]	= $refund_bank_code;
						}

						/*
						*	$cancelData ( $cancel->requestCancel(); 에 넘기는 파라미터 )
						*	$cancelData['settle_module']			::		pg 취소 모듈
						*	$cancelData['tid']						::		거래번호
						*	$cancelData['mail']						::		pg 취소 완료후 보낼 메일 (pg 에서 보낼때)
						*	$cancelData['reason']					::		취소 사유
						*	$cancelData['bank_code']				::		가상계좌 취소시 환불 은행코드
						*	$cancelData['bank_number']				::		가상계좌 취소시 환불 계좌번호
						*	$cancelData['bank_owner']				::		가상계좌 취소시 환불 예금주
						*	$cancelData['refund_cnt']				::		부분취소인지 확인하기 위한 값 환불횟수
						*	$cancelData['escrow_use']				::		에스크로 결제 여부
						*	$cancelData['cancel_amount']			::		취소 총금액
						*	$cancelData['cancel_tax_amount']		::		취소 과세금액
						*	$cancelData['cancel_tax_free_amount']	::		취소 비과세금액
						*	$cancelData['real_price']				::		최초 주문금액
						*	$cancelData['remain_price']				::		현제 남은 주문 금액
						*	$cancelData['method']					::		취소 방법 (카드, 실시간, 등등)
						*	$cancelData['oid']						::		주문번호
						*	$cancelData['cancel_msg']				::		취소 메세지
						*	$cancelData['company_id']				::		관리자 사업자키
						*/
						// PG 처리 시도
						if ($db->dt["escrow_use"] == "Y") {
							$cancel_data = $cancel->requestStatus($cancelData);	
						} else {
							$cancel_data = $cancel->requestCancel($cancelData);
						}

						if($cancel_data["result"] == 'success'){
							$pg_result[$method]["result"] = "success";
							$pg_result[$method]["price"] = $refund_method_price;
						}else{
							$pg_result[$method]["result"] = "fail";
							$pg_result[$method]["price"] = $refund_method_price;
							$pg_result[$method]["msg"] = $cancel_data["msg"];
						}

					}
				}
			}
		}


        //성공시 order_payment 와 order_price 데이터 처리
		$refund_bool = true;
		$alert_msg_success = "";
		$alert_msg_fail = "";
		if(count($pg_result)>0){
			foreach($pg_result as $method => $result){
				if($result["result"] != "success"){
					$refund_bool = false;
					$alert_msg_fail.=",".getMethodStatus($method)."(".number_format($result["price"])."원)[".$result["msg"]."]";
				}else{
					$alert_msg_success.=",".getMethodStatus($method)."(".number_format($result["price"])."원)";
				}
			}
		}

        // 1. PG 실패시!!!
        if(!$refund_bool){
			echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('".substr($alert_msg_fail,1)." 건이 실패하여 환불을 중지합니다. ".($alert_msg_success !="" ? substr($alert_msg_success,1)." 성공 건이 있어 다음 환불시 수동PG 처리후 진행해 주시기 바랍니다." : "")." ');".(substr_count($alert_msg_fail,"[실패]") > 0 ? "" : "parent.opener.document.location.reload();")."</script>");
			exit;

		// 2. 환불 성공시 처리 프로세스
		}else{

			// 환불 수단에 따라 LOOP
			foreach($refund_price as $method => $refund_method_price){

				$result = "fail";
				$total_product_price    = $tax_product_price[$method] + $tax_free_product_price[$method];
				$total_delivery_price   = $delivery_price[$method];
				$reserve_product_price  = 0;
				$reserve_delivery_price = 0;
				$save_product_price     = 0;
				$save_delivery_price    = 0;

				// ---- 1 마일리지 복구 ----
				if($method == ORDER_METHOD_RESERVE){

					if($total_product_price > 0){
						//////////////// 마일리지 적립 시작///////////////////////		반품일경우 반품 마일리지 확인필요
						InsertReserveInfo($user_code,$oid,'',$id,$total_product_price,'1','3','주문 환불금액 적립','mileage',$admininfo);	//마일리지,적립금 통합용 함수 2013-06-19 이학봉
						//////////////// 마일리지 적립 끝///////////////////////

						/*신규 포인트,마일리지 접립 함수 JK 160405*/
						$mileage_data[uid] = $user_code;
						$mileage_data[type] = 4;
						$mileage_data[mileage] = $total_product_price;
						$mileage_data[message] = '주문 환불금액 적립';
						$mileage_data[state_type] = 'add';
						$mileage_data[save_type] = 'mileage';
						$mileage_data[oid] = $oid;
						InsertMileageInfo($mileage_data);
					}

					if($total_delivery_price > 0){
						//////////////// 마일리지 적립 시작///////////////////////		반품일경우 반품 마일리지 확인필요
						InsertReserveInfo($user_code,$oid,'',$id,$total_delivery_price,'1','3','배송비 환불금액 적용','mileage',$admininfo);	//마일리지,적립금 통합용 함수 2013-06-19 이학봉
						//////////////// 마일리지 적립 끝///////////////////////

						/*신규 포인트,마일리지 접립 함수 JK 160405*/
						$mileage_data[uid] = $user_code;
						$mileage_data[type] = 5;
						$mileage_data[mileage] = $total_delivery_price;
						$mileage_data[message] = '배송비 환불금액 적용';
						$mileage_data[state_type] = 'add';
						$mileage_data[save_type] = 'mileage';
						$mileage_data[oid] = $oid;
						InsertMileageInfo($mileage_data);
					}

					$reserve_product_price = $total_product_price;
					$reserve_delivery_price = $total_delivery_price;
					$result = "success";


				// ---- 2 예치금 복구 ----
				}elseif($method == ORDER_METHOD_SAVEPRICE){

					if($refund_method_price > 0){
                        $deposit_data = array();
                        $deposit_data['pay_method'] = 'refund';
                        $deposit_data['user_code'] = $user_code;
                        $deposit_data['oid'] = $oid;
                        $deposit_data['deposit'] = $refund_method_price;
                        $deposit_data['history_type'] = 3;
                        $deposit_data['etc'] = "사용한 예치금 환불입금";
                        $deposit_data['charger_ix'] = $_SESSION[admininfo][charger_ix];
                        $deposit_data['use_type'] = "P";

						DepositManagement($deposit_data);
					}
					$save_product_price = $total_product_price;
					$save_delivery_price = $save_delivery_price;
					$result = "success";


				}elseif($method == ORDER_METHOD_CART_COUPON){
					if($cart_coupon_refund=="Y"){
						if($cart_coupon_give=="Y"){
							$sql = "update shop_cupon_regist set use_yn='0', use_oid='' where regist_ix = (select tid from shop_order_payment where oid='".$oid."' and pay_type='G' and method='".ORDER_METHOD_CART_COUPON."' and tid !='' limit 0,1)";
							$db->query($sql);
							set_order_status($oid,ORDER_STATUS_REFUND_COMPLETE,getMethodStatus($method)." 복구완료","시스템","");
						}
						$result = "success";
					}else{
						continue;
					}
				}


				// 환불 성공시 order_payment 와 order_price 데이터 처리 
				if($result == "success"
					|| $method==ORDER_METHOD_PAYCO || $method==ORDER_METHOD_CARD || $method==ORDER_METHOD_NOPAY
					|| $method==ORDER_METHOD_CASH || $method==ORDER_METHOD_BANK  || $method==ORDER_METHOD_VBANK || $method==ORDER_METHOD_ASCROW
					|| $method==ORDER_METHOD_PHONE || $method==ORDER_METHOD_ICHE || $method==ORDER_METHOD_NPAY
                	|| $method == ORDER_METHOD_EXIMBAY || $method == ORDER_METHOD_TOSS || $method == ORDER_METHOD_KAKAOPAY){


                    // 무통장이거나 가상계좌 체크시 입력
					if($method == ORDER_METHOD_BANK || $method == ORDER_METHOD_VBANK || $method==ORDER_METHOD_PHONE || $method==ORDER_METHOD_ASCROW){
					//-------------------------------------예치금 돌려주는 프로세스 넣기!!! --------------------------------------
						if($use_deposit == '1'){
                            $deposit_data = array();
                            $deposit_data['pay_method'] = 'refund';
                            $deposit_data['user_code'] = $user_code;
                            $deposit_data['oid'] = $oid;
                            $deposit_data['deposit'] = $refund_method_price;
                            $deposit_data['history_type'] = 3;
                            $deposit_data['etc'] = "결제수단[".getMethodStatus($method)."] 예치금 환불입금";
                            $deposit_data['charger_ix'] = $_SESSION[admininfo][charger_ix];
                            $deposit_data['use_type'] = "P";

                            DepositManagement($deposit_data);
							set_order_status($oid,ORDER_STATUS_REFUND_COMPLETE,getMethodStatus($method)." -> ".getMethodStatus(ORDER_METHOD_SAVEPRICE)." 으로 환불 - ".number_format($refund_method_price)."원","시스템","");
						}

					}else{
						set_order_status($oid,ORDER_STATUS_REFUND_COMPLETE,getMethodStatus($method)." 환불 - ".number_format($refund_method_price,2)." ","시스템","");
					}

					$status_message = "부분취소 완료";
					//PC 결제정보 처리하기!!

					table_order_payment_data_creation($oid,'F','IC',$method,($tax_product_price[$method]+$delivery_price[$method]),$tax_free_product_price[$method],$refund_method_price,$pay_etcinfo[$method]);

					// 환불금액 존재시
					if($total_product_price > 0){
                        table_order_price_data_creation($oid, '', '', 'F', 'P', $total_product_price, $total_product_price , $status_message, ($method == ORDER_METHOD_RESERVE ? $total_product_price : 0), 0, ($method == ORDER_METHOD_SAVEPRICE ? $total_product_price : 0));
					}

					// 배송비 존재시
					if($total_delivery_price > 0){
						table_order_price_data_creation($oid,'','','F','D',$total_delivery_price,$total_delivery_price,$status_message,$reserve_delivery_price,0,$save_delivery_price);
					}

					$alert_msg.= ",".getMethodStatus($method)."[완료]";
				}else{
					$alert_msg.= ",".getMethodStatus($method)."[실패]";
				}
			}

			// 주문상태변경
			$sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in ('".implode("','",$od_ix)."') ";
			$db->query($sql);
			$order_details = $db->fetchall();

			for($i=0;$i < count($order_details);$i++){

                $STATUS_MESSAGE_DESC = $STATUS_MESSAGE;

				if($refund_method == '1'){
                    $STATUS_MESSAGE_DESC .= "실 환불수단 : 현금";
				}else if($refund_method == '2'){
                    $STATUS_MESSAGE_DESC .= "실 환불수단 : 적립금";
				}

                if($refund_date != ''){
                    $STATUS_MESSAGE_DESC .= " | 실 환불일자 : ".$refund_date;
                }

				set_order_status($order_details[$i][oid],ORDER_STATUS_REFUND_COMPLETE,$STATUS_MESSAGE_DESC,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

				$uptOrderDetail = "UPDATE ".TBL_SHOP_ORDER_DETAIL." SET refund_status = '".ORDER_STATUS_REFUND_COMPLETE."' , fc_date=NOW(), real_refund_method = '".$refund_method."', real_refund_date = '".$refund_date."' WHERE od_ix ='".$order_details[$i][od_ix]."' ";
                $db->query($uptOrderDetail);

				// 크리마 환불완료 상태 전송(작업자 에이치엠파트너즈 임우철 팀장 2021.05.26)
				$url = "https://stg.barrelmade.co.kr/shop/crema/orderReSend?orc_ix=".$od_ix;
				//$url = "https://testbarrel.forbiz.co.kr/shop/crema/orderReSend?orc_ix=".$od_ix;

				$cmd = sprintf("curl -X GET \"%s\"", $url);
				$output = shell_exec($cmd);

				// // 크리마 환불완료 상태 전송(작업자 에이치엠파트너즈 임우철 팀장 2021.05.26)

				if($i==0) {
                    $goodname = $order_details[$i]["pname"];
                }
			}

            // 정산시 배송비 환불 처리!!!!
			foreach($refund_delivery_price as  $ocde_ix => $d_price){
				$db->query("UPDATE shop_order_claim_delivery SET delivery_price = '".$d_price."',ac_target_yn='Y' WHERE ocde_ix ='".$ocde_ix."' ");
			}

            // 최종 처리된 주문 상세 정보 조회
            // $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in ('".implode("','",$od_ix)."') ";
            // $db->query($sql);
            /*
            echo "<xmp>";
            print_r($_POST);
            echo "</xmp>";
            exit;*/

			if($order_details[0][order_from] == 'self'){

                // 환불완료 메일 정보 셋팅
                // $mail_info['total_refund_apply_amount'] = $total_refund_tax_free_product_price;     // 반품신청 총 결제금액
                $mail_info['total_refund_apply_amount'] = $total_refund_tax_product_price;     // 반품신청 총 결제금액
                $mail_info['add_delivery_amount'] = $total_refund_delivery_price;	// 반품 시 추가 배송비
                $mail_info['total_refund_amount'] = $total_refund_price;		    // 환불 예정 금액
				$mail_info[mem_name] = $order[bname];
                $mail_info[mem_mobile] = $order[bmobile];
                $mail_info[mem_mail] = $order[bmail];
                $mail_info['reason'] = $cancelData['reason'];                        // 환불이유
                $mail_info['refund_apply_date'] = $cancelData['refund_apply_date'];  // 환불신청일자

                // 회원/비회원 주문
                if($cancelData['user_code'] == ""){
                    $mail_info['nonmember_yn'] = 'Y';
                }else{
                    $mail_info['nonmember_yn'] = 'N';
                }

                // 주문 결제수단
                $sql="select * from shop_order_payment where pay_type = 'G' and pay_status = '".ORDER_STATUS_INCOM_COMPLETE."' and oid = '".$oid."' limit 1";
                $db->query($sql);
                $db->fetch();
                $mail_info['order_payment_method'] = getMethodStatus($db->dt['method']);

                $sql="select * from shop_order_payment where pay_type = 'F' and pay_status = '".ORDER_STATUS_INCOM_COMPLETE."' and oid = '".$oid."' order by opay_ix desc limit 1";
                $db->query($sql);
                $db->fetch();
                $mail_info['refund_date'] = $db->dt['regdate'];

                // 환불 결제 수단
                $method_desc = '';
                foreach($refund_price as $method => $refund_method_price){
                    $method_desc = getMethodStatus($method);
                    // 가상계좌, 무통장시
                    if($method == '4' || $method == '0'){

                        $sql = "SELECT o.refund_method,
								AES_DECRYPT(UNHEX(o.refund_bank),'".$db->ase_encrypt_key."') as refund_bank, 
								AES_DECRYPT(UNHEX(o.refund_bank_name),'".$db->ase_encrypt_key."') as refund_bank_owner
							FROM shop_order o 
						   where o.oid='".$oid."' ";
                        $db->query($sql);
                        $db->fetch();
                        $rBankInfo = $db->dt;

                        list($rBankInfo["refund_bank_code"],$rBankInfo["refund_bank_account"]) = explode("|",$rBankInfo["refund_bank"]);
			            $mail_info['refund_bank'] = $arr_banks_name[$rBankInfo["refund_bank_code"]];
                        $mail_info['refund_bank_owner'] = $rBankInfo["refund_bank_owner"];
                        $mail_info['refund_bank_account'] = str_repeat("*",strlen($rBankInfo["refund_bank_account"])-3).substr($rBankInfo["refund_bank_account"],strlen($rBankInfo["refund_bank_account"])-3, 3);
                    }
                    $mail_info['refund_method_code'] = $method;
                }

                $mail_info['refund_payment_method'] = $method_desc;
                $mail_info[mem_id] = $order[bname];
				$mail_info[pname] = cut_str($goodname,36) . (count($order_details) > 1 ? ' 외 ' . (count($order_details) - 1) . '건' : '');

/*
				echo "<xmp>";
				var_dump($mail_info);
                echo "</xmp>";
				exit;*/


                $http_type = (!empty($_SERVER['HTTPS'])) ? 'https://' : 'http://';
                $mail_info[domain] = $http_type . $_SERVER['HTTP_HOST'];
                $mail_info['orderDetail'] = $order_details;

                if($order_details[0]['status'] == ORDER_STATUS_RETURN_COMPLETE){
                    @sendMessageByStep('order_refund', $mail_info);
				}

			}

			echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('".substr($alert_msg,1)." 처리 되었습니다.');".(substr_count($alert_msg,"[실패]") > 0 ? "" : "")."</script>");
			echo("<script language='javascript' src='../js/message.js.php'></script>
			<script language='javascript'>

				//parent.self.close();parent.opener.document.location.reload();

				if(top.window.dialogArguments) {//showModalDialog를 사용할 경우 부모창 새로 고침을 위해 수정 kbk 13/08/03
					opener=top.window.dialogArguments;
					top.window.returnValue=true;
				} else {
					top.opener.document.location.reload();
				}
				top.window.close();
			</script>");


			exit;

		}
	}

}
/*
if ($act == "send_mail"){
	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");


	$db->query("Select bmail, bname, user_code FROM ".TBL_SHOP_ORDER." WHERE oid = '".$oid."'");
	$db->fetch();

	$mail_info[mem_name] = $db->dt[bname];
	$mail_info[mem_mail] = $db->dt[bmail];
	$mail_info[mem_id] = $db->dt[bname];
	$email_card_contents_basic = "요청하신 견적서입니다";

	copy("http://".$HTTP_HOST."../order/taxbill.php?mode=excel&oid=".$oid."&user_code=".$db->dt[user_code]."",$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/taxbill.xls");

	$subject = " ".$mail_info[mem_name]." 님, 요청하신 견적서 입니다..";
	SendMail($mail_info, $subject,$email_card_contents_basic,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/taxbill.xls");


	//echo $mail_info[mem_mail];
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 메일이 발송되었습니다.');</script>");
	echo("<script>self.close();</script>");
}
*/


if ($act == "delete"){
	$db->query("DELETE FROM ".TBL_SHOP_ORDER." WHERE oid='$oid'");
	$db->query("DELETE FROM ".TBL_SHOP_ORDER_DETAIL." WHERE oid='$oid'");
	//주문로그는 남겨야함!
	//$db->query("DELETE FROM ".TBL_SHOP_ORDER_STATUS." WHERE oid='$oid'");
	$db->query("DELETE FROM shop_order_delivery WHERE oid='$oid'");//추가 kbk 12/11/22
	$db->query("DELETE FROM shop_order_memo WHERE oid='$oid' ");
	$db->query("DELETE FROM shop_order_price WHERE oid='".$oid."' ");
	$db->query("DELETE FROM shop_order_price_history WHERE oid='".$oid."' ");
	$db->query("DELETE FROM shop_order_detail_deliveryinfo WHERE oid='".$oid."' ");
	$db->query("DELETE FROM shop_order_detail_discount WHERE oid='".$oid."' ");

	set_order_status($oid,'DEL',"주문삭제",$_SESSION["admininfo"]["charger"]."(".$_SESSION["admininfo"]["charger_id"].")",$_SESSION["admininfo"]["company_id"]);

	echo("<script>parent.location.reload();</script>");
	//echo("<script>top.location.href = 'orders.list.php?page=$page';</script>");
}

if ($mmode == "select_delete"){

	for($i=0;$i < count($oid);$i++){
		$db->query("DELETE FROM ".TBL_SHOP_ORDER." WHERE oid='".$oid[$i]."'");
		$db->query("DELETE FROM ".TBL_SHOP_ORDER_DETAIL." WHERE oid='".$oid[$i]."'");
		//주문로그는 남겨야함!
		//$db->query("DELETE FROM ".TBL_SHOP_ORDER_STATUS." WHERE oid='".$oid[$i]."'");
		$db->query("DELETE FROM shop_order_delivery WHERE oid='".$oid[$i]."'");
		$db->query("DELETE FROM shop_order_memo WHERE oid='".$oid[$i]."' ");
		$db->query("DELETE FROM shop_order_price WHERE oid='".$oid[$i]."' ");
		$db->query("DELETE FROM shop_order_price_history WHERE oid='".$oid[$i]."' ");
		$db->query("DELETE FROM shop_order_detail_deliveryinfo WHERE oid='".$oid[$i]."' ");
		$db->query("DELETE FROM shop_order_detail_discount WHERE oid='".$oid[$i]."' ");

		set_order_status($oid[$i],'DEL',"주문삭제",$_SESSION["admininfo"]["charger"]."(".$_SESSION["admininfo"]["charger_id"].")",$_SESSION["admininfo"]["company_id"]);
	}
	echo("<script>parent.location.reload();</script>");
	//echo("<script>top.location.href = 'orders.list.php?page=$page';</script>");
}

?>
