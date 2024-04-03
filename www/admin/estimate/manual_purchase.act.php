<?
include("../class/layout.class");
include("../../class/database.class");
include("../inventory/inventory.lib.php");
include("../logstory/class/sharedmemory.class");
include("../../include/cash_manage.lib.php");

//echo "<pre>";
//print_r ($_REQUEST);
//exit;


$db = new Database;
$pdb = new Database;

if($act=="get_goods_mult_price"){
	
	
	for($i=0;$i<count($gid_unit);$i++){
		list($gid[],$unit[]) = explode("|",$gid_unit[$i]);
	}

	if($wms_discount_value!=""){
		if($wms_discount_type=="whole"){
			$where="where is_wholesale='W' ";
		}else{
			$where="where is_wholesale='R' ";
		}

		$sql = "select gid,unit, type_".$wms_discount_value."_sellprice as price from inventory_goods_multi_price ".$where."  and gid in ('".implode("','",$gid)."') and concat(gid,'|',unit) in ('".implode("','",$gid_unit)."') ";
		$db->query($sql);
	}else{
		$sql = "select gid,unit, ".($wms_discount_type=="whole" ? "wholesale_price" : "sellprice")." as price from inventory_goods_unit where gid in ('".implode("','",$gid)."') and concat(gid,'|',unit) in ('".implode("','",$gid_unit)."') ";
		$db->query($sql);
	}

	if($db->total){
		$data=$db->fetchall("object");
		
		foreach($data as $dt){
			$resulte[$dt[gid]."|".$dt[unit]]=$dt[price];
		}
	}

	foreach($gid_unit as $key=>$val){
		$return[$key][gid_unit]=$val;
	
		if($resulte[$val]!=""){
			$return[$key][price]=$resulte[$val];
		}else{
			$return[$key][price]="X";
		}
	}
	echo json_encode($return);

	exit;
}

if($act=="get_goods_barcode"){

	$sql = "select data.*, 
		(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.company_id   limit 1) as company_name ,
		(select psprice/1.1 as lately_price from shop_order_detail od where od.pcode = data.gu_ix  and order_from = 'offline' and oid in (select oid  from shop_order where user_com_id= '".$ci_ix."' ) order by regdate limit 1) as lately_price 
		from 
			(select g.cid,g.gname, g.gid, g.gcode, g.admin, g.ci_ix, g.surtax_div, ips.pi_ix, pi.place_name, ips.ps_ix,  pi.company_id,  ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.unit , gu.buying_price, gu.wholesale_price, gu.sellprice, ips.vdate, ips.expiry_date,gu.offline_wholesale_price ,gu.gu_ix
			from inventory_goods g 
			right join inventory_goods_unit gu  on g.gid =gu.gid
			left join  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit
			left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
			left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
			where gu.barcode='".$barcode."'
			 group by g.gid , gu.unit, ips.pi_ix, ips.expiry_date
		) data";
	$db->query($sql);
	$db->fetch("object");

	if($db->total){
		$db->dt["unit_text"] = $ITEM_UNIT[$db->dt["unit"]];
		$db->dt["surtax_text"] = getSurTaxDiv($db->dt["surtax_div"], "surtax_div","","text");

		echo json_encode($db->dt);
	}
	exit;
}

if($act == "insert" || $act == "select_tmp_insert"){

	//print_r($admininfo);
	//exit;
	//$db->debug = true;
	/**
	  수동주문 등록

	  체크사항 
	  1. 등록시 품목정보에 의한 구매인지? 상품정보에 의한 구매인지 구분값 필요
	  2. 주문 테이블에 company_id, company_name 를 어떤 정보를 넣어 줘야 하는지 ? 
	  3. 품목정보와 맞춰서 부가세 정보를 주문테이블이나 상품품 테이블에 맞춰넣는다.
	  4. 아래 값들에 대해서 확인필요
	*/

	//수주서 정보입력
	$shmop = new Shared("manual_purchase_tmp");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();

	
	if($act == "select_tmp_insert"){
		$total_cnt = count($mpt_ix);
	}else{
		$total_cnt = 1;
	}

	for($i=0;$i<$total_cnt;$i++){
		
		if($act == "select_tmp_insert"){
			$data="";
			$data = $shmop->getObjectForKey($mpt_ix[$i]);
			$data = unserialize($data);
			extract($data);
			$act = "select_tmp_insert";
		}

		$apply_date = trim($apply_date);	// 수주서 요청일
		$charger_name = trim($charger_name);	//수주서요청자
		$charger_ix = trim($charger_ix);		//수주요청자 코드
		$com_name = trim($com_name);	//매출처명
		$ci_ix = trim($ci_ix);	// 매출처 키값
		$com_number = $com_number1."-".$com_number2."-".$com_number3;	//사업자번호
		$com_phone = $com_phone1."-".$com_phone2."-".$com_phone3;		//대표전화번호
		$com_mobile = $com_mobile1."-".$com_mobile2."-".$com_mobile3;		//담당자 핸드폰번호
		$com_zip = trim($com_zip);	//사업장 우편코드
		$com_addr1 = trim($com_addr1);	//사업장 주소1
		$com_addr2 = trim($com_addr2);	//사업장 주소2
		$seller_message = trim($seller_message);	//수주서 기타요청사항

		//결제 정보 입력
		$type = trim($type);		//결제상태
		$status = $type;

		if($type == 'DP'){	//후불제 일경우 결제타입을 기타 100 으로 잡음 2013-07-05 이학봉
			$method = ORDER_METHOD_CASH;	//결제타입 - 현금결제
		}else{
			$method = trim($method);	//결제타입
		}

		$bank_ix = trim($bank_ix);	//무통장입금일 경우 무통장계좌
		$bank_input_date = trim($bank_input_date);	//무통장 입금일 경우 무통장 입금예정일

		$vbank_holder = trim($vbank_holder);	//가상계좌 입금일경우 예금주
		$vbank_name = trim($vbank_name);		//가상계좌 입금일경우 은행명
		$vbank_num =  $vbank_num1."-".$vbank_num2."-".$vbank_num3;	//가상계좌 입금일 경우 가상계좌 번호
		
		$card_name = trim($card_name);			//카드결제일경우 카드사명
		$card_num = $card_num1."-".$card_num2."-".$card_num3;	// 카드결제일 경우 카드번호
		$card_expiry = $card_expiry1."-".$card_expiry2;			//카드결제일 경우 카드유효기간

		$delivery_method = trim($delivery_method);	//배송타입
		$delivery_basic_policy = trim($delivery_basic_policy);	//배송비 지불방식
		$delivery_price = trim($delivery_price);	//선불일경우 배송금액
		$shipping_date = trim($shipping_date);		//배송예정일

		//증빙서류 정보
		$voucher_div = trim($voucher_div);	//증빙서류 종류
		$voucher_num_div = trim($voucher_num_div);	// 개인소득공제일경우 휴대폰/현금영수증 구분

		$voucher_phone = $voucher_phone1."-".$voucher_phone2."-".$voucher_phone3;	//개인소득공제 휴대폰 번호
		$phone_voucher_name = trim($phone_voucher_name);							//개인소득공제 휴대폰 사용자명

		$voucher_card = $voucher_card1."-".$voucher_card2."-".$voucher_card3;		//개인소득공제 일경우 현금영수증 카드번호
		$card_voucher_name = trim($card_voucher_name);								//개인소득공제 일경우 현금영수증 사용자명

		$expense_num  = $expense_num1."-".$expense_num2."-".$expense_num3;			//지출증빙 번호
		$certificate_yn = $certificate_yn;											//세금게산서 발급시 결제완료후/기간별 발급
		
		//배송지 정보
		$member_id = trim($member_id);			//주문자 아이디
		$rname = trim($rname);					//주문자 이름
		$tel = $tel1."-".$tel2."-".$tel3;		//주문자 전화번호
		$pcs = $pcs1."-".$pcs2."-".$pcs3;		//주문자 핸드폰번호
		$r_mail = trim($r_mail);	//주문자 메일
		//$zip = $zip1."-".$zip2;		//주문자 우편번호
		if($zip2 != "" || $zip2 != NULL){
			$zip = "$zip1-$zip2";
		}else{
			$zip = $zip1;
		}
		$addr1 = trim($addr1);		//주문자 주소 1
		$addr2 = trim($addr2);		//주문자 주소2
		$bname = trim($bname);		//수취인 이름
		$bmember_phone = $bmember_phone1."-".$bmember_phone2."-".$bmember_phone3;		//수취인 전화번호
		$bmember_pcs = $bmember_pcs1."-".$bmember_pcs2."-".$bmember_pcs3;			//수취인 핸드폰번호
		$delivery_message = trim($delivery_message);	//배송 요청사항

		$oid = make_shop_order_oid();

		$sql="SELECT loan_price,noaccept_price FROM common_company_detail WHERE company_id='".$ci_ix."'";
		$db->query($sql);
		$db->fetch();
		$loan_price = $db->dt[loan_price];

		if($loan_price!=0){

			$noaccept_price = $db->dt[noaccept_price];
			$remain_loan_price = $db->dt[loan_price]-$db->dt[noaccept_price];
			
			$check_sum_total_price=0;
			foreach($manual_orderinfo as $key => $value){
				$check_sum_total_price += $value[total_price];
			}

			if($check_sum_total_price > $remain_loan_price){
				if($act == "select_tmp_insert"){
					echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('잔여여신금액를 넘어선 주문건이 있어 중지되었습니다. 수동수주서를 다시한번 확인해주시기 바랍니다.');parent.document.location.reload()</script>";
					exit;
				}else{
					echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('주문 금액이 잔여여신 ".number_format($remain_loan_price)." 원 보다 작아야합니다.');</script>";
					exit;
				}
			}
		}


		$sql = "insert into shop_order_detail_deliveryinfo (odd_ix,oid,od_ix,order_type,rname,rtel,rmobile,rmail,zip,addr1,addr2,msg,regdate) values('','$oid','','1','$bname','$bmember_phone','$bmember_pcs','$r_mail','$zip','".$addr1."','".$addr2."','$delivery_message',NOW())";
		$db->query($sql);
		if($db->dbms_type == "oracle"){
			$odd_ix = $db->last_insert_id;
		}else{
			$odd_ix = $db->insert_id();
		}

		$sum_total_price=0;
		$tax_product_price=0;
		$tax_free_product_price=0;
		
		$sql = "select * from shop_delivery_template where company_id='".$_SESSION["admininfo"]["company_id"]."' and is_basic_template='1' and product_sell_type='W' ";
		$db->query($sql);
		$db->fetch();
		$dt_ix=$db->dt[dt_ix];

		foreach($manual_orderinfo as $key => $value){

			$sql = "SELECT 
							g.gname,
							g.standard,
							g.surtax_div,
							g.admin,
							ccd.com_name,
							ccd.com_phone
					FROM
						inventory_goods g 
						inner join common_company_detail as ccd on (g.admin = ccd.company_id)
					WHERE 
						gid = '".$value[gid]."' ";
			$pdb->query($sql);
			$pdb->fetch();
			$pname = $pdb->dt[gname]; // 품목명을 구매 상품명으로 대체	OK
			$select_option_text = $value[standard]; // 품목명을 구매 상품명으로 대체 $db->dt[standard];	OK

			//품목정보에 있는 부가세 정보 // 1:부가세 포함, 2:부과세 별도, 3:영세율적용, 4:면세율적용, 5:부가세없음		OK
			if($pdb->dt[surtax_div] == '4'){
				$surtax_yorn = 'Y';
			}else if($pdb->dt[surtax_div] == '3'){
				$surtax_yorn = 'P';
			}else{
				$surtax_yorn = 'N';
			}

			$p_company_id = $pdb->dt[admin];		//상품 입점업체 키
			$p_com_name = $pdb->dt[com_name];		//상품 입점업체 명
			$p_com_phone = $pdb->dt[com_phone];		//상품 입점업체 명


			$db->query("SELECT * FROM inventory_goods_unit where gid='".$value[gid]."' and unit = '".$value[unit]."'");
			$db->fetch();
			$gu_ix = $db->dt[gu_ix]; // 품목, 단위 코드를 상품코드로 대체		OK
			$coprice = $db->dt[buying_price];		//품목정보에 기본매입가
			$barcode = $db->dt[barcode];			//바코드
			//$listprice = $db->dt[wholesale_price];	//품목정보에 기본도매가
			$listprice = $value[psprice];

			$pid = $value[gid];	//OK
			$product_type = "0";				//OK
			$select_option_id = "";				//OK
			$option_price = 0;				//$value[sellprice];

			$psprice = round($value[sellprice]*1.1);		//단가
			$count = $value[pcount];				//주문수량
			$total_price = $value[total_price];//총 결제금액

			$one_commission = "N"; //개별수수료 사용안함
			$commission = "0"; //자사 상품이라 정산하지 않아도 됨
			$stock_use_yn = "Y";		//재고관리 사용여부
			$account_type = "3";// 자사 상품이라 정산하지 않아도 됨..
			
			//배송관련!
			$delivery_type="1";//$_POST["delivery_type"]; // 1: 통합배송, 2: 개별배송
			$delivery_package="N";//Y:개별배송 N:묶음배송
			$delivery_policy="2";//:무료배송 2:고정배송비 3:주문결제금액 할인 4:수량별할인 5:출고지별 배송비 6: 상품1개단위 배송비
			$delivery_method=$_POST["delivery_method"];//배송방법(1:택배,2:화물,3:직배송,4:방문수령)

			$sql = "select company_id from common_company_detail where com_type = 'A' limit 0,1";
			$db->query($sql);
			$db->fetch();
			$ori_company_id = $db->dt[company_id];//통합배송일경우 본사 키값으로 묶어주는 역할

			$delivery_addr_use="0";//출고지별 배송비 사용 1:사용 0:미사용
			$factory_info_addr_ix="0";//출고지 키값

			
			$sql = "insert into ".TBL_SHOP_ORDER_DETAIL."
			(od_ix,oid,order_from,buyer_type,pid,pcode,barcode,product_type,pname,gid,gu_ix,option_id,option_text,option_price,pcnt,coprice,listprice,psprice,ptprice,status,odd_ix,company_id,company_name,com_phone,one_commission,commission,surtax_yorn,stock_use_yn,regdate,delivery_method,dt_ix,delivery_type,delivery_package,delivery_policy,delivery_pay_method,ori_company_id,delivery_addr_use,factory_info_addr_ix,account_type,pt_dcprice,due_date) values
			('','$oid','".$order_from."','2','$pid','".$value[gid]."','$barcode','$product_type','$pname','".$value[gid]."','$gu_ix','$select_option_id','$select_option_text','$option_price','$count','$coprice','$listprice','$psprice','$total_price','$status','$odd_ix','$p_company_id','$p_com_name','$p_com_phone','$one_commission','$commission','$surtax_yorn','$stock_use_yn',NOW(),'$delivery_method','$dt_ix','$delivery_type','$delivery_package','$delivery_policy','$delivery_basic_policy','$ori_company_id','$delivery_addr_use','$factory_info_addr_ix','$account_type','$total_price','$shipping_date')";
			$db->query($sql);
	
			
			$pid_array=array();

			$sql = "select id from ".TBL_SHOP_PRODUCT." p where p.stock_use_yn='Y' and pcode = '".$gu_ix."' ";
			$db->query($sql);
			$p_info = $db->fetchall("object");
			for($j=0;$j<count($p_info);$j++){
				$pid_array[]=$p_info[$j][id];
			}

			$sql = "select od.id as opnd_ix ,pid from ".TBL_SHOP_PRODUCT." p inner join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od on (p.id=od.pid) where p.stock_use_yn='Y' and option_code = '".$gu_ix."' ";
			$db->query($sql);
			if($db->total){
				$option_dt_info = $db->fetchall("object");
				for($j=0;$j<count($option_dt_info);$j++){

					$pid_array[]=$option_dt_info[$j][pid];
					$db->query("update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set option_sell_ing_cnt = option_sell_ing_cnt + '".$count."' where id = '".$option_dt_info[$j][opnd_ix]."' ");
				}
				$pid_array = array_unique($pid_array);
			}

			$db->query("update ".TBL_SHOP_PRODUCT." set sell_ing_cnt = sell_ing_cnt + '".$count."', order_cnt = order_cnt + '".$count."' where id in ('".implode("','",$pid_array)."') ");

			$db->query("update inventory_goods_unit set sell_ing_cnt = sell_ing_cnt + '".$count."', order_cnt = order_cnt + '".$count."' where gu_ix ='$gu_ix' ");

			//real_lack_stock update
			if($gu_ix){
				
				$sql="select real_lack_stock from shop_order_detail  where gu_ix = '".$gu_ix."' and status in ('IR','IC','DR','DD') and oid !='".$oid."' order by regdate desc limit 0,1";
				$db->query($sql);
				if($db->total){
					$db->fetch();

					$item_stock_sum = $db->dt[real_lack_stock];
				}else{
					$sql = "select sum(ps.stock) as stock
					from inventory_goods_unit gu  left join inventory_product_stockinfo ps on (ps.unit = gu.unit and ps.gid=gu.gid)
					where gu.gu_ix = '".$gu_ix."' ";
					$db->query($sql);
					$db->fetch();

					$item_stock_sum = $db->dt[stock];
				}
				

				$sql="select od_ix, pcnt from shop_order_detail  where oid='".$oid."' and gu_ix = '".$gu_ix."'";
				$db->query($sql);

				if($db->total){
					$od_info = $db->fetchall("object");

					$real_lack_stock = $item_stock_sum;

					for($j=0;$j<count($od_info);$j++){
						$real_lack_stock -= $od_info[$j][pcnt];
						$sql="update shop_order_detail set real_lack_stock='".$real_lack_stock."' where od_ix='".$od_info[$j][od_ix]."' ";
						$db->query($sql);
					}
				}
			}


			$sum_total_price += $total_price;
			if($surtax_yorn=="Y"){
				$tax_product_price += $total_price;
			}elseif($surtax_yorn=="N"){
				$tax_free_product_price += $total_price;
			}
		}
		
		$expect_product_price = $sum_total_price;
		$payment_product_price = $sum_total_price;

		if($type== 'IC'){
			if($delivery_basic_policy == "1"){
				$expect_delivery_price = $delivery_price;
				$payment_delivery_price = $delivery_price;
			}else{
				$expect_delivery_price = '0';
				$payment_delivery_price = '0';
			}
		}else{
			if($delivery_basic_policy == "1"){
				$expect_delivery_price = $delivery_price;
			}else{
				$expect_delivery_price = '0';
			}
			$payment_product_price = '0';
			$payment_delivery_price = '0';
		}

		$sql = "insert into shop_order_delivery (ode_ix,oid,company_id,delivery_type,delivery_package,delivery_policy,delivery_method,ori_company_id,delivery_addr_use,factory_info_addr_ix,delivery_price,delivery_dcprice,delivery_pay_type,regdate) values ('','$oid','$p_company_id','$delivery_type','$delivery_package','$delivery_policy','$delivery_method','$ori_company_id','$delivery_addr_use','$factory_info_addr_ix','$expect_delivery_price','$expect_delivery_price','$delivery_basic_policy',NOW())";

		$db->sequences = "SHOP_ORDER_DELIVERY_SEQ";
		$db->query($sql);

		$sql = "select cu.code, cu.id,  cu.company_id, ccd.com_name, cmd.sex_div,
				".(date("Y")+1)."-date_format(cmd.birthday,'%Y') as age,
				AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, mg.gp_level,
				AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, 
				AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel, 
				AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs, 
				AES_DECRYPT(UNHEX(cmd.zip),'".$db->ase_encrypt_key."') as zip, 
				AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') as addr1, 
				AES_DECRYPT(UNHEX(cmd.addr2),'".$db->ase_encrypt_key."') as addr2, 
				cu.authorized as authorized, cu.is_id_auth as is_id_auth, cu.mem_type as mem_type,
				cu.visit, date_format(cu.date,'%Y.%m.%d') as regdate, mg.gp_name, cu.last AS last, cmd.gp_ix
				from ".TBL_COMMON_USER." as cu 
				inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
				left join ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix
				left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (cu.company_id = ccd.company_id)
				where cu.id = '".$member_id."'";

		$db->query($sql);
		$db->fetch();
		$user_code = $db->dt[code]; 
		$mem_group = $db->dt[gp_name];
		$com_name = $db->dt[com_name];
		$sex = $db->dt[sex_div];
		$age = $db->dt[age];

		$vb_info = $vbank_holder." ".$vbank_name." ".$vbank_num1."-".$vbank_num2."-".$vbank_num3; // 가상계좌 정보

		$delivery_price = $delivery_price; // 배송비
		$delivery_method = $delivery_method;	//배송종류
		$use_cupon_price = ""; //주문 상품상세 정보에 들어가기 때문에 주문정보에는 없어도 될듯...
		$reserve_price = ""; // 적립금 사용금액 
		$member_sale_price = ""; // 회원할인금액
		$payment_price = $total_price;
		$baddr = $addr1." ".$addr2;	//주문자 주소
		
		/*증빙 문서 관련 차후 처리하기!!!
		taxsheet_yn,receipt_y,
		,'".$taxsheet_yn."','$receipt_y'
		if($voucher_div == '3'){
			$taxsheet_yn = "Y";		//세금계산서발행여부
			$receipt_y = "N";		//현금영수증 발행여부
		}else if($voucher_div == '1'){
			$taxsheet_yn = "N";
			$receipt_y = "Y";
		}
		*/

		$user_ip=$_SERVER["REMOTE_ADDR"];
		$user_agent=$_SERVER["HTTP_USER_AGENT"];

		$db->query("SELECT * FROM ".TBL_SHOP_BANKINFO." where disp = '1' and bank_ix = '$bank_ix' ");
		$db->fetch();
		$bank = $db->dt[bank_name] ." ". $db->dt[bank_number]." ".$db->dt[bank_owner];
		
		$db->query("SELECT department FROM ".TBL_COMMON_MEMBER_DETAIL." where code = '".$charger_ix."' ");
		$db->fetch();
		$dp_ix = $db->dt[department];

		$payment_price = $expect_product_price+$expect_delivery_price;

		$sql = "insert into ".TBL_SHOP_ORDER."
			(oid,buyer_type,user_code, user_com_id, com_name, buserid, bname, sex, age, mem_group,btel,bmobile,bmail,bzip,baddr,order_date,static_date,status, est_ix,total_price,payment_price,user_ip,user_agent, payment_agent_type,org_delivery_price,delivery_price,org_product_price,product_price)
			values
			('$oid','2','$user_code','".$ci_ix."','".$com_name."','".$member_id."','$rname','".$sex."','".$age."','$mem_group','$tel','$pcs','$r_mail','$zip','$baddr',NOW(),".date("Ymd").",'$status','".$est_ix."','$payment_price','".$payment_price."','".$user_ip."','".$user_agent."','".($_SESSION["admin_config"]["mall_page_type"] == "M" ? "M":"W")."','".$expect_delivery_price."','".$expect_delivery_price."','".$expect_product_price."','".$expect_product_price."')";
		$db->query($sql);

		/*증빙 문서 관련 차후 처리하기!!!
		if($method=="0") {
			if($receipt_y == "Y"){
				if($voucher_div == '1'){
					if($voucher_num_div == '1'){
						$confirm_no = $voucher_phone1.$voucher_phone2.$voucher_phone3;
						$receipt_rname = $phone_voucher_name;
					}else if($voucher_num_div == '2'){
						$confirm_no = $voucher_card1.$voucher_card2.$voucher_card3.$voucher_card4;
						$receipt_rname = $card_voucher_name;
					}
				}else if($voucher_div == '2'){
					$confirm_no = $expense_num1.$expense_num2.$expense_num3;
					$receipt_rname = $rname;
				}

				$sql="insert into receipt(order_no,order_type,m_useopt,m_number,id,receipt_yn,regdate,rname) values('$oid','1','1','".$confirm_no."','".$member_id."','N',NOW(),'".$receipt_rname."')";
				$db->query($sql);
			}
		}
		*/
		
		table_order_price_data_creation($oid,"","",'G','P',$expect_product_price,$payment_product_price,"",0,0,0);	// 상품 주문금액
		table_order_price_data_creation($oid,"","",'G','D',$expect_delivery_price,$payment_delivery_price,"",0,0,0);	// 배송비 관련 금액

		$etc_info["bank"]=$bank;
		$etc_info["bank_input_date"]=$bank_input_date;
		$etc_info["vb_info"]=$vb_info;

		$tax_price=$tax_product_price+$expect_delivery_price;
		$tax_free_price=$tax_free_product_price;
		table_order_payment_data_creation($oid,'G',$status,$method,$tax_price,$tax_free_price,$payment_price,$etc_info);

		set_order_status($oid,$status,"수동주문",$_SESSION["admininfo"]["charger"]."(".$_SESSION["admininfo"]["charger_id"].")","");

		$db->query("UPDATE ".TBL_SHOP_ORDER_DETAIL." SET status='$status' WHERE oid='$oid' ");
		$db->query("UPDATE ".TBL_COMMON_MEMBER_DETAIL." set recent_order_date = NOW() where code = '".$charger_ix."' ");


		if($status=="DP"){ //후불(외상)
			$noaccept_data="";
			$noaccept_data[company_id]=$ci_ix;
			$noaccept_data[oid]=$oid;
			$noaccept_data[price]=$payment_price;
			$noaccept_data[msg]="[시스템] 수동주문생성";
			setNoacceptDeposit($noaccept_data);
		}

		if($act == "insert"){
			if($_mpt_ix!=''){
				$db->query("DELETE FROM shop_manual_purchase_tmp where mpt_ix = '".$_mpt_ix."' ");
				$shmop->setObjectForKeyClear($_mpt_ix);
			}
		}else{
			$db->query("DELETE FROM shop_manual_purchase_tmp where mpt_ix = '".$mpt_ix[$i]."' ");
			$shmop->setObjectForKeyClear($mpt_ix[$i]);
		}
	}

	if($mmode == "pop"){
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('수동주문이 정상적으로 완료 되었습니다.');parent.self.close()</script>";
	}else{
		if($act == "select_tmp_insert"){
			echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('수동주문이 정상적으로 완료 되었습니다.');parent.document.location.href='manual_purchase_tmp_list.php'</script>";
		}else{
			echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('수동주문이 정상적으로 완료 되었습니다.');parent.document.location.href='manual_purchase_list.php'</script>";
		}
	}
	exit;
}

if($act == "tmp_insert"){
	
	$order_total_price = 0;
	$order_pcount = 0;

	foreach($manual_orderinfo as $key => $value){
		$order_total_price += $value[total_price]*$value[pcount];
		$order_pcount += $value[pcount];
	}
	
	if($_mpt_ix!=""){
		$db->query("update shop_manual_purchase_tmp set ci_name='$com_name',ci_ix='$ci_ix',status='$type',due_date='$shipping_date',com_name='$bname',pcount='$order_pcount',total_price='$order_total_price' where mpt_ix='$_mpt_ix' ");
		$mpt_ix = $_mpt_ix;
	}else{
		$db->query("insert into shop_manual_purchase_tmp(mpt_ix,code,ci_name,ci_ix,status,due_date,com_name,pcount,total_price,regdate) values('','".$admininfo[charger_ix]."','$com_name','$ci_ix','$type','$shipping_date','$bname','$order_pcount','$order_total_price',NOW()) ");
		$mpt_ix = $db->insert_id();
	}
	

	$data = serialize($_POST);
	$shmop = new Shared("manual_purchase_tmp");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$shmop->setObjectForKey($data,$mpt_ix);

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 등록되었습니다.');</script>";
	echo "</script><script>parent.location.href='/admin/offline_order/manual_purchase_tmp_list.php';</script>";
	exit;
}

if($act == "select_tmp_delete"){
	
	$shmop = new Shared("manual_purchase_tmp");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();

	for($i=0;$i<count($mpt_ix);$i++){
		$db->query("DELETE FROM shop_manual_purchase_tmp where mpt_ix = '".$mpt_ix[$i]."' ");
		$shmop->setObjectForKeyClear($mpt_ix[$i]);
	}

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제되었습니다.');</script>";
	echo "</script><script>parent.location.reload();</script>";
	exit;
}
?>