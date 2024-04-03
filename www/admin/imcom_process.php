<?
	include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");
	include($_SERVER["DOCUMENT_ROOT"]."/admin/reseller/reseller.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/admin/inventory/inventory.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/include/cash_manage.lib.php");

	$db = new Database();
	
	//exit;
	//settle_module
	//	mobilians, m_inicis, inicis
	//method
	//		ORDER_METHOD_PHONE (소액), ORDER_METHOD_CARD (카드), ORDER_METHOD_VBANK (가상계좌), ORDER_METHOD_ICHE (실시간), ORDER_METHOD_PAYCO(페이코)
	/*
	$order_date = array(
		array( 
			"_oid" => "20141224153044-14295" ,
			"_tid" => "INIphpVBNKhdaisomall20141224153114574790" ,
			"_settle_module" => "mobilians" ,
			"_method" =>  ORDER_METHOD_VBANK,
			"_status" => "IC"
		),
		array( 
			"_oid" => "20141224153044-14295" ,
			"_tid" => "INIphpVBNKhdaisomall20141224153114574790" ,
			"_settle_module" => "mobilians" ,
			"_method" =>  ORDER_METHOD_VBANK,
			"_status" => "IC"
		)
	);
	*/

	$order_date = array(
		array( "_oid" => "201511282201-0000399" ,"_tid" => "20151128150152" ,"_settle_module" => "kcp" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		/*array( "_oid" => "201510190226-0000061" ,"_tid" => "20151019660255" ,"_settle_module" => "kcp" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC")
		array( "_oid" => "201509150650-0000030" ,"_tid" => "20150915098873" ,"_settle_module" => "kcp" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "201509150405-0000024" ,"_tid" => "20150915090578" ,"_settle_module" => "kcp" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "201509150225-0000023" ,"_tid" => "20150915083101" ,"_settle_module" => "kcp" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "201509150138-0000021" ,"_tid" => "20150915076575" ,"_settle_module" => "kcp" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "201509150112-0000019" ,"_tid" => "20150915072307" ,"_settle_module" => "kcp" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "201509150111-0000018" ,"_tid" => "20150915071435" ,"_settle_module" => "kcp" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "201509150100-0000013" ,"_tid" => "20150915069587" ,"_settle_module" => "kcp" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "201509150057-0000012" ,"_tid" => "20150915067930" ,"_settle_module" => "kcp" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "201509150046-0000009" ,"_tid" => "20150915067189" ,"_settle_module" => "kcp" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "201509150023-0000003" ,"_tid" => "20150915058974" ,"_settle_module" => "kcp" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "201509150006-0000002" ,"_tid" => "20150915051223" ,"_settle_module" => "kcp" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "201509150001-0000001" ,"_tid" => "20150914049647" ,"_settle_module" => "kcp" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC")

		array( "_oid" => "20150310152839-07381" ,"_tid" => "5039331442" ,"_settle_module" => "mobilians" ,"_method" => ORDER_METHOD_PHONE,"_status" => "IC"),
		array( "_oid" => "20150303170506-23611" ,"_tid" => "5022912172" ,"_settle_module" => "mobilians" ,"_method" => ORDER_METHOD_PHONE,"_status" => "IC"),
		array( "_oid" => "20141224144555-98713" ,"_tid" => "INIphpISP_hdaisomall20141224144638289753" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "20141224144248-90516" ,"_tid" => "INIphpCARDhdaisomall20141224144336877815" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "20141224123725-58055" ,"_tid" => "INIphpISP_hdaisomall20141224124056171202" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "20141224123921-53746" ,"_tid" => "INIphpISP_hdaisomall20141224123940736137" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "20141224115915-44911" ,"_tid" => "INIphpCARDhdaisomall20141224120016763032" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "20141223235330-40042" ,"_tid" => "INIphpCARDhdaisomall20141224000036519227" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "20141224155633-56288" ,"_tid" => "INIphpDBNKhdaisomall20141224160419347083" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_ICHE,"_status" => "IC"),
		array( "_oid" => "20141223235613-97791" ,"_tid" => "IniTechPG_hdaisomall20141224171702820963" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141224165858-18933" ,"_tid" => "IniTechPG_hdaisomall20141224170817815405" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223215619-54687" ,"_tid" => "IniTechPG_hdaisomall20141224165843896717" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141217165928-35694" ,"_tid" => "IniTechPG_hdaisomall20141224163516893672" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141224161129-78736" ,"_tid" => "IniTechPG_hdaisomall20141224161613772112" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223135401-17767" ,"_tid" => "IniTechPG_hdaisomall20141224160136820304" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141222132327-55656" ,"_tid" => "IniTechPG_hdaisomall20141224155748893944" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141224114240-90231" ,"_tid" => "IniTechPG_hdaisomall20141224152118820778" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223162429-26991" ,"_tid" => "IniTechPG_hdaisomall20141224145134771224" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141224131018-04816" ,"_tid" => "IniTechPG_hdaisomall20141224142230893158" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223173745-31269" ,"_tid" => "IniTechPG_hdaisomall20141224140330770886" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223223744-52003" ,"_tid" => "IniTechPG_hdaisomall20141224140226819142" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223214932-58259" ,"_tid" => "IniTechPG_hdaisomall20141224134639770591" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223094456-38091" ,"_tid" => "IniTechPG_hdaisomall20141224131539896629" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223232759-84472" ,"_tid" => "IniTechPG_hdaisomall20141224130114893708" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141224123337-85045" ,"_tid" => "IniTechPG_hdaisomall20141224123518771863" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223120336-13411" ,"_tid" => "IniTechPG_hdaisomall20141224120437819318" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223121726-10921" ,"_tid" => "IniTechPG_hdaisomall20141224120348774675" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223152531-54505" ,"_tid" => "IniTechPG_hdaisomall20141224115751817984" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223140205-80571" ,"_tid" => "IniTechPG_hdaisomall20141224114854817695" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141222163040-84045" ,"_tid" => "IniTechPG_hdaisomall20141224113549893409" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141221153153-71481" ,"_tid" => "IniTechPG_hdaisomall20141224112800815684" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141224112037-50395" ,"_tid" => "IniTechPG_hdaisomall20141224112531819085" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141224111705-37596" ,"_tid" => "IniTechPG_hdaisomall20141224112101774988" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223223851-91056" ,"_tid" => "IniTechPG_hdaisomall20141224110634817259" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223122832-63871" ,"_tid" => "IniTechPG_hdaisomall20141224110251819261" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223134904-18183" ,"_tid" => "IniTechPG_hdaisomall20141224100604894006" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223134524-88941" ,"_tid" => "IniTechPG_hdaisomall20141224100528819380" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223164350-88742" ,"_tid" => "IniTechPG_hdaisomall20141224094851773617" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223212712-54763" ,"_tid" => "IniTechPG_hdaisomall20141224083019819469" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223221444-07205" ,"_tid" => "IniTechPG_hdaisomall20141224082701892037" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141222201857-87903" ,"_tid" => "IniTechPG_hdaisomall20141224081521894918" ,"_settle_module" => "inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141224153149-30192" ,"_tid" => "INIMX_CARDmdaisomall20141224153408554477" ,"_settle_module" => "m_inicis" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "20141224153222-05945" ,"_tid" => "INIMX_CARDmdaisomall20141224153337965462" ,"_settle_module" => "m_inicis" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "20141223235915-94111" ,"_tid" => "INIMX_CARDmdaisomall20141224000005745657" ,"_settle_module" => "m_inicis" ,"_method" => ORDER_METHOD_CARD,"_status" => "IC"),
		array( "_oid" => "20141223094924-71541" ,"_tid" => "IniTechPG_mdaisomall20141224155330817267" ,"_settle_module" => "m_inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223211008-66231" ,"_tid" => "IniTechPG_mdaisomall20141224141014819395" ,"_settle_module" => "m_inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141222235115-05136" ,"_tid" => "IniTechPG_mdaisomall20141224130227819032" ,"_settle_module" => "m_inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223230156-64443" ,"_tid" => "IniTechPG_mdaisomall20141224113344814036" ,"_settle_module" => "m_inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223134039-04678" ,"_tid" => "IniTechPG_mdaisomall20141224113123817165" ,"_settle_module" => "m_inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223221348-99076" ,"_tid" => "IniTechPG_mdaisomall20141224112657771821" ,"_settle_module" => "m_inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223235546-30045" ,"_tid" => "IniTechPG_mdaisomall20141224002345815822" ,"_settle_module" => "m_inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141223230842-01772" ,"_tid" => "IniTechPG_mdaisomall20141224002111894997" ,"_settle_module" => "m_inicis" ,"_method" => ORDER_METHOD_VBANK,"_status" => "IC"),
		array( "_oid" => "20141224154216-15079" ,"_tid" => "4861572882" ,"_settle_module" => "mobilians" ,"_method" => ORDER_METHOD_PHONE,"_status" => "IC"),
		array( "_oid" => "20141224154031-90614" ,"_tid" => "4861569067" ,"_settle_module" => "mobilians" ,"_method" => ORDER_METHOD_PHONE,"_status" => "IC"),
		array( "_oid" => "20141224153907-43931" ,"_tid" => "4861563907" ,"_settle_module" => "mobilians" ,"_method" => ORDER_METHOD_PHONE,"_status" => "IC"),
		array( "_oid" => "20141224153833-92782" ,"_tid" => "4861563047" ,"_settle_module" => "mobilians" ,"_method" => ORDER_METHOD_PHONE,"_status" => "IC"),
		array( "_oid" => "20141224153846-08279" ,"_tid" => "4861562962" ,"_settle_module" => "mobilians" ,"_method" => ORDER_METHOD_PHONE,"_status" => "IC")*/
	);

	foreach($order_date as $ol){

		$OID=$ol['_oid'];
		$TID=$ol['_tid'];
		$SETTLE_MODULE=$ol['_settle_module'];
		$METHOD=$ol['_method'];
		$STATUS=$ol['_status'];

		$sql="select status , user_code , bname, bmail, btel from ".TBL_SHOP_ORDER." where oid='".$OID."' AND status in ('SR','IB','IR') ";
		$db->query($sql);
		$order = $db->fetch();

		if($db->total){

			$sql="update ".TBL_SHOP_ORDER." set status = '".ORDER_STATUS_INCOM_COMPLETE."' where oid='".$OID."'  ";
			$db->query($sql);

			$db->query("select expect_product_price, expect_delivery_price from shop_order_price WHERE oid='".$OID."' and payment_status='G' ");
			$db->fetch();
			
			if($STATUS == ORDER_STATUS_INCOM_COMPLETE){
				$expect_product_price = $db->dt[expect_product_price];
				$expect_delivery_price = $db->dt[expect_delivery_price];
			}else{
				$expect_product_price = 0;
				$expect_delivery_price = 0;
			}

			//입금확인 처리시 payment 받은 금액 입력 업데이트
			table_order_price_data_creation($OID,'','','G','P',0,$expect_product_price,"FORBIZ 수동처리",0,0,0);
			if($expect_delivery_price > 0){
				table_order_price_data_creation($OID,'','','G','D',0,$expect_delivery_price,"FORBIZ 수동처리",0,0,0);
			}
			
			if($STATUS == ORDER_STATUS_INCOM_COMPLETE){
				$db->query("update shop_order_payment set pay_status='IC', ic_date = NOW() WHERE oid='".$OID."' and pay_type = 'G' and pay_status='IR'  ");
			}

			$db->query("update shop_order_payment set settle_module='".$SETTLE_MODULE."', tid='".$TID."' WHERE oid='".$OID."' and method = '".$METHOD."' ");

			$sql="select od.*,odd.rmobile from ".TBL_SHOP_ORDER_DETAIL." od left join shop_order_detail_deliveryinfo odd on (od.odd_ix=odd.odd_ix) where od.oid='".$OID."' and status in ('SR','IB','IR')";
			$db->query($sql);
			$order_details = $db->fetchall();
			for($i=0;$i < count($order_details);$i++){

				if($STATUS == ORDER_STATUS_INCOM_COMPLETE){
					$sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_INCOM_COMPLETE."'  , update_date = NOW() , ic_date=NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' ";
					$db->query($sql);

					//입금확인시 페널티 적립
					//셀러판매신용점수 추가 시작 2014-06-15 이학봉	
					InsertPenaltyInfo('1', '1', $OID, $order_details[$i]['od_ix'], $penalty, $order_details[$i]["company_id"], '입금완료 판매신용점수 적립', '', 'ic');
					//셀러판매신용점수 추가 끝 2014-06-15 이학봉

					/*				
					define("POINT_USE_STATE_IC","1"); // 입금완료
					define("POINT_USE_STATE_DC","2"); // 배송완료
					define("POINT_USE_STATE_BF","3"); // 구매확정
					define("POINT_USE_STATE_CC","4"); // 입금후 취소
					define("POINT_USE_STATE_EC","5"); // 교환확정
					define("POINT_USE_STATE_RC","6"); // 반품확정
					define("POINT_USE_STATE_DD","7"); // 입금완료후 발송지연
					define("POINT_USE_STATE_DDA","8"); // 입금완료후 추가 발송지연 
					define("POINT_USE_STATE_ETC","9"); // 기타
					*/
					insertProductPoint('1', POINT_USE_STATE_IC, $OID, $order_details[$i]['od_ix'], $point, $order_details[$i]["pid"], '입금완료 상품점수 적립', '', 'ic');
				}else{
					$sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_INCOM_READY."' , update_date = NOW() where od_ix='".$order_details[$i][od_ix]."' ";
					$db->query($sql);
				}
			}
			
			if($STATUS == ORDER_STATUS_INCOM_COMPLETE){
				set_order_status($OID,ORDER_STATUS_INCOM_COMPLETE,"FORBIZ 수동처리 입금완료","시스템","","","");
				echo $OID." 입금완료<br/>";
			}else{
				set_order_status($OID,ORDER_STATUS_INCOM_COMPLETE,"FORBIZ 수동처리 입금예정","시스템","","","");
				echo $OID." 입금예정<br/>";
			}
		}
	}
?>