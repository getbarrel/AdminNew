<?php
	include("../class/layout.class");
	include_once("../store/md.lib.php");
	//include("../webedit/webedit.lib.php");
	include ("../basic/company.lib.php");
	include("../buyingservice/buying.lib.php");
	include("../econtract/contract.lib.php");


	if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
		$menu_name = "등록관리";
	}else{
		$menu_name = "거래처 관리";
	}

	if($info_type == ""){
		$info_type = "basic";
	}

	$db = new Database;
	$db2 = new Database;
	$cdb = new Database;
	$mdb = new Database;

	/*popbill 자동가입 프로세스*/
	//getPopbillJoinMember($code);
	/*끝*/
	$sql = "SELECT 
				COUNT(*) as total
			FROM
				common_user as cu 
				inner join common_company_detail as ccd on (ccd.company_id = cu.company_id)
				inner join common_seller_detail as csd on (csd.company_id = ccd.company_id)
				left join common_seller_delivery as csdv on (csd.company_id = csdv.company_id)
			where
				cu.mem_div = 'S' ";
				
	$db->query($sql);
	$db->fetch();
	$vendor_total = $db->dt[total];

	if($company_id == ""){

		if($admininfo["mall_type"] == "B" || $admininfo["mall_type"] == "F" || $admininfo["mall_type"] == "R" || $admininfo["mall_type"] == "BW" ){
			if(!checkMyService("ADD","VENDOR", $vendor_total)){
				echo "<script language='javascript'>alert('입점업체를 6개 이상 등록하시면 추가요금이 발생됩니다. \\n부가운영서비스 > 입점업체추가 서비스에서 서비스 신청후 사용하실 수 있습니다.');history.back();</script>";
				exit;
			}
		}
		$act = "seller_insert";

	}else{

		if($info_type == "basic"){
			if($company_id){	//사업자코드 존재할경우에만 update 처리함.

				$sql = "select
							ccd.*,
							ccd.loan_price,
							csd.seller_date,
							csd.deposit_price,
							csd.seller_message,
							ccd.com_mobile,
							ccd.seller_auth,
							csd.charge_code,
							csd.md_code,
							csd.seller_msg,
							csd.seller_cid,
							csd.seller_brand,
							csg.group_name,
							csg.sg_ix,
							ccw.commercial_disp,ccw.ca_country,ccw.ca_code,ccw.sc_code,ccw.floor,ccw.line,ccw.no,
							ccw.tel as ws_tel ,ccw.charge_phone as ws_charge_phone,ccw.kakao_phone,ccw.kakao_id,ccw.facebook,ccw.twitter,ccw.qq,ccw.wechat,
							csd.seller_level
						from
							common_company_detail as ccd 
							inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
							left join common_company_wholesale ccw on (ccd.company_id=ccw.company_id)
							left join common_seller_group as csg on (csd.sg_ix = csg.sg_ix)
						where
							ccd.company_id = '".$company_id."'";
					
				$db->query($sql);
				$db->fetch();
				$act = "seller_updateinfo";	
			}else{

				$act = "seller_insert";
			}

		}else if($info_type == "order_info"){

			$sql = "SELECT 
						* 
					FROM 
					".TBL_COMMON_COMPANY_DETAIL." as  ccd 
					inner join ".TBL_COMMON_SELLER_DETAIL." as sd on (ccd.company_id = sd.company_id)	
					where ccd.company_id = '".$company_id."'";

			$db->query($sql);
			$seller_array = $db->fetch();

			$act = "seller_updateinfo";

		}else if($info_type == "seller_info"){

			$sql = "SELECT csd.*,	ccd.com_name
					  FROM common_company_detail as ccd left join common_seller_detail as csd on (ccd.company_id = csd.company_id)
				     where ccd.company_id = '".$company_id."'";
			$db->query($sql);
			$db->fetch();
			$team = $db->dt[team];

			$act = "seller_insert";

		}else if($info_type == "delivery_info"){



		}else if($info_type == "seller_setup"){
			
			$sql = "select csd.*, et.contract_group
					  from common_seller_delivery as csd left join econtract_tmp as et on (csd.et_ix = et.et_ix)
			 		 where company_id = '".$company_id."'";
			$db->query($sql);
			$db->fetch();

			$et_ix = $db->dt[et_ix];
			$contract_group = $db->dt[contract_group];
			$econtract_commission = $db->dt[econtract_commission];
			$is_contract = $db->dt[is_contract];

			$seller_use_info = $db->dt[seller_use_info];
			$seller_minishop_use = $db->dt[seller_minishop_use];
			$account_info = $db->dt[account_info];
			$ac_delivery_type = $db->dt[ac_delivery_type];
			$ac_expect_date = $db->dt[ac_expect_date];
			$ac_term_div = $db->dt[ac_term_div];
			$ac_term_date1 = $db->dt[ac_term_date1];
			$ac_term_date2 = $db->dt[ac_term_date2];

			$account_type = $db->dt[account_type];
			$account_method = $db->dt[account_method];			//=
			$wholesale_commission = $db->dt[wholesale_commission];
			$commission = $db->dt[commission];

			$substitude_rate = $db->dt[substitude_rate];

			$seller_grant_use = $db->dt[seller_grant_use];
			$grant_setup_price = $db->dt[grant_setup_price];
			$ac_grant_price = $db->dt[ac_grant_price];
			$account_div = $db->dt[account_div];

			$act = "seller_updateinfo";

		}else if($info_type == 'tax_info'){

			$sql = "SELECT 
						* 
					FROM 
					".TBL_COMMON_COMPANY_DETAIL." as  ccd 
					inner join ".TBL_COMMON_SELLER_DETAIL." as sd on (ccd.company_id = sd.company_id)	
					where ccd.company_id = '".$company_id."'";

			$db->query($sql);
			$seller_array = $db->fetch();

			$act = "seller_updateinfo";
		
		}else{

			if($info_type == "factory_info"){
				$type = "F";
			}else if($info_type == "exchange_info"){
				$type = "E";
			}else if($info_type == "visit_info"){
				$type = "V";
			}
			if($_GET[addr_ix]){
				$where = "  and addr_ix = '".$_GET[addr_ix]."' "; 
			}

			$sql = "select
						*
					from
						shop_delivery_address
					where
						delivery_type = '".$type."'
						and mall_ix = '".$admininfo[mall_ix]."'
						and company_id = '".$company_id."'
				";

			$db->query($sql);
			$delivery_array = $db->fetchall();

			$act = "seller_insert";
		
		}
	}

	if($_GET[info_type] and $_GET[addr_ix]){

		if($info_type == "factory_info"){
			$type = "F";
		}else if($info_type == "exchange_info"){
			$type = "E";
		}else if($info_type == "visit_info"){
			$type = "V";
		}
		if($_GET[addr_ix]){
			$where = "  and addr_ix = '".$_GET[addr_ix]."' "; 
		}

		$sql = "select
					*
				from
					shop_delivery_address
				where
					delivery_type = '".$type."'
					and mall_ix = '".$admininfo[mall_ix]."'
					and company_id = '".$company_id."'
					$where
			";

		$db->query($sql);
		$db->fetch();

		$addr_phone = explode("-",$db->dt[addr_phone]);
		$addr_mobile = explode("-",$db->dt[addr_mobile]);
		$act = "seller_updateinfo";
	}

	$cs_phone = explode("-",$db->dt[cs_phone]);

	$Contents01 = "
		<table width='100%' cellpadding=0 cellspacing=0 border='0' >
		<col width='20%' />
		<col width='30%' />
		<col width='20%' />
		<col width='30%' />
		  <tr>
			<td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "입점업체 관리 > $menu_name")."</td>
		  </tr>
		  <tr>
			<td align='left' colspan=4 style='padding-bottom:20px;'>
			<div class='tab'>
				<table class='s_org_tab'>
				<col width='900px'>
				<col width='*'>
				<tr>
					<td class='tab'>
						<table id='tab_01' ".(($info_type == "basic" || $info_type == "") ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02'  ><a href='?info_type=basic&company_id=".$company_id."&mmode=$mmode'>사업자 정보</a> <span style='padding-left:2px' class='helpcloud' help_width='410' help_height='70' help_html='사업자 정보를 작성하여 등록한 후에 해당 사업자에 관리자화면 로그인 정보를 발급해 줄 수 있습니다.<br />사업자 정보 추가 후 [목록관리] - [사용자 추가]를 통해 관리자에 계정을 발급해 주시기 바랍니다.'><img src='/admin/images/icon_q.gif' /></span></td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_08' ".($info_type == "seller_setup" ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' >";
							if($company_id == ""){
								$Contents01 .= "<a href=\"javascript:alert('사업자 정보를 먼저 입력하십시오.');\">셀러 수수료 설정</a>";
							}else{
								$Contents01 .= "<a href='?info_type=seller_setup&company_id=".$company_id."&mmode=$mmode'>셀러 수수료 설정</a>";
							}

							$Contents01 .= "
							</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_08' ".($info_type == "tax_info" ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' >";
							if($company_id == ""){
								$Contents01 .= "<a href=\"javascript:alert('사업자 정보를 먼저 입력하십시오.');\">계좌정보</a>";
							}else{
								$Contents01 .= "<a href='?info_type=tax_info&company_id=".$company_id."&mmode=$mmode'>계좌정보</a>";
							}

							$Contents01 .= "
							</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_02' ".($info_type == "order_info" ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' >";
							if($company_id == ""){
								$Contents01 .= "<a href=\"javascript:alert('사업자 정보를 먼저 입력하십시오.');\">담당자 정보</a>";
							}else{
								$Contents01 .= "<a href='?info_type=order_info&company_id=".$company_id."&mmode=$mmode'>담당자 정보</a>";
							}
							$Contents01 .= "
							</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_02' ".($info_type == "seller_info" ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' >";
							if($company_id == ""){
								$Contents01 .= "<a href=\"javascript:alert('".getTransDiscription(md5($_SERVER["PHP_SELF"]),'N','','',false)."');\">상점정보 (MiniShop)</a>";
							}else{
								$Contents01 .= "<a href='?info_type=seller_info&company_id=".$company_id."&mmode=$mmode'>상점정보 (MiniShop)</a>";
							}
							$Contents01 .= "
							</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_06' ".($info_type == "exchange_info" ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' >";
							if($company_id == ""){
								$Contents01 .= "<a href=\"javascript:alert('사업자 정보를 먼저 입력하십시오.');\">교환/반품지 관리</a>";
							}else{
								$Contents01 .= "<a href='?info_type=exchange_info&company_id=".$company_id."&mmode=$mmode'>교환/반품지 관리</a>";
							}
							$Contents01 .= "

							</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_03' ".($info_type == "delivery_info" ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' >";
							if($company_id == ""){
								$Contents01 .= "<a href=\"javascript:alert('".getTransDiscription(md5($_SERVER["PHP_SELF"]),'N','','',false)."');\">배송정책</a>";
							}else{
								$Contents01 .= "<a href='?info_type=delivery_info&company_id=".$company_id."&mmode=$mmode'>배송정책</a>";
							}
							$Contents01 .= "

							</td>
							<th class='box_03'></th>
						</tr>
						</table>
	
						<table id='tab_07' ".($info_type == "visit_info" ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' >";
							if($company_id == ""){
								$Contents01 .= "<a href=\"javascript:alert('사업자 정보를 먼저 입력하십시오.');\">방문수령지 관리</a>";
							}else{
								$Contents01 .= "<a href='?info_type=visit_info&company_id=".$company_id."&mmode=$mmode'>방문수령지 관리</a>";
							}

							$Contents01 .= "
							</td>
							<th class='box_03'></th>
						</tr>
						</table>


					</td>
					<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>
					</td>
				</tr>
				</table>
			</div>
			</td>
		  </tr>
		</table>";

	if($info_type == "basic"){

		$seller_date = explode(" ",$db->dt[seller_date]);
		$corporate_number = explode("-",$db->dt[corporate_number]);
		$com_phone = explode("-",$db->dt[com_phone]);
		$com_mobile = explode("-",$db->dt[com_mobile]);
		$seller_cid = $db->dt[seller_cid];
		$seller_msg = $db->dt[seller_msg];

        $seller_brand = $db->dt[seller_brand];

		if(strpos($db->dt[com_number],"-")){
			$com_number = explode("-",$db->dt[com_number]);
		}else{
			$com_number[0] = substr($db->dt[com_number],0,3);
			$com_number[1] = substr($db->dt[com_number],3,2);
			$com_number[2] = substr($db->dt[com_number],5,5);
		}

		$seller_type	= $db->dt[seller_type];
		$seller_type_array = explode("|",$seller_type);
		
		if(is_array($seller_type_array)){
			$checked_1 = (in_array('1',$seller_type_array) ? "checked":"");
		}
		if(is_array($seller_type_array)){
			$checked_2 = (in_array('2',$seller_type_array) ? "checked":"");
		}
		if(is_array($seller_type_array)){
			$checked_3 = (in_array('3',$seller_type_array) ? "checked":"");
		}
		if(is_array($seller_type_array)){
			$checked_4 = (in_array('4',$seller_type_array) ? "checked":"");
		}
		if(is_array($seller_type_array)){
			$checked_5 = (in_array('5',$seller_type_array) ? "checked":"");
		}

	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>사업자 정보 입력</b>
			</td>
		</tr>
		</table>
				
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
		<colgroup>
			<col width='15%' />
			<col width='35%' style='padding:0px 0px 0px 10px'/>
			<col width='15%' />
			<col width='35%' style='padding:0px 0px 0px 10px'/>
		</colgroup>";

	$Contents01 .= "
		<tr>
			<td class='input_box_title'> <b>거래처 코드</b></td>
			<td class='input_box_item'>
				<table width='380' cellpadding=0 cellspacing=0 border=0>
					<tr>
						<td width=250>
						".($act == "seller_insert" ? "시스템 자동 생성" : $db->dt[company_id])."
						</td>
					</tr>
				</table>
			</td>
			<td class='input_box_title'> <b>거래 시작일</b></td>
			<td class='input_box_item'><input type=text  id='seller_date' name='seller_date' value='".$seller_date[0]."' class='textbox'  style='width:100px' validation='false' title='거래 시작일'></td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>거래처 유형 <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
				<input type='checkbox' id ='sales_vendor' name='sell_type[]' value='1' validation='true' multi_check='true' class='multi-check' title='거래처 유형' $checked_1> <label for='sales_vendor'>국내매출</label> &nbsp;
				<input type='checkbox' id ='supply_vendor' name='sell_type[]' value='2' validation='true' multi_check='true' class='multi-check' title='거래처 유형' $checked_2> <label for='supply_vendor'>국내매입</label> &nbsp;
				<input type='checkbox' id ='oversea_sales' name='sell_type[]' value='3' validation='true' multi_check='true' class='multi-check' title='거래처 유형' $checked_3> <label for='oversea_sales'>해외수출</label> &nbsp;
				<input type='checkbox' id ='oversea_supply' name='sell_type[]' value='4' validation='true' multi_check='true' class='multi-check' title='거래처 유형' $checked_4> <label for='oversea_supply'>해외수입</label> &nbsp;
				<input type='checkbox' id ='outsourcing' name='sell_type[]' value='5' validation='true' multi_check='true' class='multi-check' title='거래처 유형' $checked_5> <label for='outsourcing'>외주물류창고</label>
			</td>
			<td class='input_box_title'> <b>담당MD <!--img src='".$required3_path."'--> </b></td>
			<td class='input_box_item'>
				".MDSelect($db->dt[md_code])." 
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>거래처 구분</b></td>
			<td class='input_box_item'>
				<input type='radio' name='seller_division' id='seller_division_1' value='1' ".($db->dt[seller_division] == "1" || $db->dt[seller_division] == "" ? "checked":"")."> <label for='seller_division_1'>일반</label> &nbsp;&nbsp;
				<input type='radio' name='seller_division' id='seller_division_2' value='2' ".($db->dt[seller_division] == "2" ? "checked":"")."> <label for='seller_division_2'>가맹점</label> &nbsp;&nbsp;
			</td>
			<td class='input_box_title'><b>국내외 구분</b></td>
			<td class='input_box_item'>
				<input type='radio' name='nationality' id='nationality_1' value='I' ".($db->dt[nationality] == "I" || $db->dt[nationality] == "" ? "checked":"")."> <label for='nationality_1'>국내</label> &nbsp;&nbsp;
				<input type='radio' name='nationality' id='nationality_2' value='O' ".($db->dt[nationality] == "O" ? "checked":"")."> <label for='nationality_2'>해외</label> &nbsp;&nbsp;
				<input type='radio' name='nationality' id='nationality_3' value='D' ".($db->dt[nationality] == "D" ? "checked":"")."> <label for='nationality_3'>기타</label> &nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>거래처 등급</b></td>
			<td class='input_box_item'>
				<select name=seller_level id='seller_level' style='height:23px; width:70px' validation='false' title='거래처 등급'>	
					<option value=''  ".($db->dt[seller_level] == "" ? "selected":"")."> 선택 </option>
					<option value='1' ".($db->dt[seller_level] == "1" ? "selected":"")."> 우호 </option>
					<option value='2' ".($db->dt[seller_level] == "2" ? "selected":"")."> 양호 </option>
					<option value='3' ".($db->dt[seller_level] == "3" ? "selected":"")."> 보통 </option>
					<option value='4' ".($db->dt[seller_level] == "4" ? "selected":"")."> 위험 </option>
					<option value='5' ".($db->dt[seller_level] == "5" ? "selected":"")."> 블랙리스트 </option>
				</select>
			</td>
			<td class='input_box_title'> <b>물류창고사용여부</b></td>
			<td class='input_box_item'>
				<input type='radio' id='is_wharehouse_11' name='is_wharehouse' value='1' ".($db->dt[is_wharehouse] == "1" || $db->dt[is_wharehouse] == "" ? "checked":"")."> <label for='is_wharehouse_11'>사용</label> &nbsp;&nbsp;
				<input type='radio' id='is_wharehouse_22' name='is_wharehouse' value='0' ".($db->dt[is_wharehouse] == "0" ? "checked":"")."> <label for='is_wharehouse_22'>미사용</label> &nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>상호명 <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
			<input type=text name='com_name' id='com_name' value='".$db->dt[com_name]."' class='textbox'  style='width:170px' validation='true' title='상호명'>
			</td>
			<td class='input_box_title'> <b>대표자명 <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'><input type=text  id='com_ceo' name='com_ceo' value='".$db->dt[com_ceo]."' class='textbox'  style='width:50px' validation='true' title='대표자명'></td>
		</tr>
		<tr>
			<td class='input_box_title'><b>사업자번호  <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
				<input type=text name='com_number_1' id='com_number_1' value='".$com_number[0]."' style='width:30px' maxlength=3  class='textbox' com_numeric=true validation='true' title='사업자번호'> -
				<input type=text name='com_number_2' id='com_number_2' value='".$com_number[1]."' style='width:20px' maxlength=2  class='textbox' com_numeric=true validation='true' title='사업자번호'> -
				<input type=text name='com_number_3' id='com_number_3' value='".$com_number[2]."' style='width:40px' maxlength=5  class='textbox' com_numeric=true validation='true' title='사업자번호'>
				<div style='display:inline;padding:2px;' class=small>예) XXX-XX-XXXXX</div>
			</td>
			<td class='input_box_title'><b>법인번호</b></td>
			<td class='input_box_item'>
				<input type=text name='corporate_number_1' id='corporate_number_1' value='".$corporate_number[0]."' style='width:50px' maxlength=6 class='textbox' com_numeric=true validation='false' title='법인번호'> -
				<input type=text name='corporate_number_2' id='corporate_number_2' value='".$corporate_number[1]."' style='width:60px' maxlength=7 class='textbox' com_numeric=true validation='false' title='법인번호'>
				<div style='display:inline;padding:2px;' class=small>예) XXX-XX-XXXXX</div>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>업태</b>   </td>
			<td class='input_box_item'><input type=text name='com_business_status' id='com_business_status' value='".$db->dt[com_business_status]."' class='textbox'  style='width:150px' validation='false' title='업태'></td>
			<td class='input_box_title' > <b>업종</b>   </td>
			<td class='input_box_item'><input type=text name='com_business_category' id='com_business_category' value='".$db->dt[com_business_category]."' class='textbox'  style='width:150px' validation='false' title='업종'></td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>사업자 유형 <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
				<input type='radio' name='com_div' id='com_div_R' value='R' ".($db->dt[com_div] == "R" || $db->dt[com_div] == "" ? "checked":"")."> <label for='com_div_R'>법인사업자</label> &nbsp;&nbsp;
				<input type='radio' name='com_div' id='com_div_P' value='P' ".($db->dt[com_div] == "P" ? "checked":"")."> <label for='com_div_P'>개인사업자</label> &nbsp;&nbsp;
				<input type='radio' name='com_div' id='com_div_S' value='S' ".($db->dt[com_div] == "S" ? "checked":"")."> <label for='com_div_S'>간이과세자</label> &nbsp;&nbsp;
				<input type='radio' name='com_div' id='com_div_E' value='E' ".($db->dt[com_div] == "E" ? "checked":"")."> <label for='com_div_E'>면세사업자</label> &nbsp;&nbsp;
				<!--<input type='radio' name='com_div' id='com_div_I' value='I' > <label for='com_div_I'>수출입업체</label> &nbsp;&nbsp;-->
			</td>
			<td class='input_box_title'> <b>셀러그룹 <!--img src='".$required3_path."'--> </b></td>
			<td class='input_box_item'>";

			$sql = "select * from common_seller_group where is_use_yn = '1' order by sg_ix ASC";
			$db->query($sql);
			$seller_group = $db->fetchall();

			
			$Contents01 .= "
				<select name=sg_ix id='sg_ix' style='height:23px; width:100px' validation='false' title='셀러그룹'>	
					<option value=''  ".($db->dt[sg_ix] == "" ? "selected":"")."> 선택 </option>";
			for($i=0;$i<count($seller_group);$i++){	

			$Contents01 .= "<option value='".$seller_group[$i][sg_ix]."' ".($seller_group[$i][sg_ix] == $db->dt[sg_ix] ? "selected":"")."> ".$seller_group[$i][group_name]." </option>";
			}
	$Contents01 .= "
				</select>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>전화번호</b></td>
			<td class='input_box_item'>
				<input type=text name='com_phone_1' id='com_phone_1' value='".$com_phone[0]."' style='width:30px' maxlength=3 size=3  class='textbox' com_numeric=true validation='false' title='전화'> -
				<input type=text name='com_phone_2' id='com_phone_2' value='".$com_phone[1]."' style='width:30px' maxlength=4 size=5 class='textbox' com_numeric=true validation='false' title='전화'> -
				<input type=text name='com_phone_3' id='com_phone_3' value='".$com_phone[2]."' style='width:30px' maxlength=4 size=5 class='textbox' com_numeric=true validation='false' title='전화'>
			</td>
			<td class='input_box_title'> <b>핸드폰</b>  <img src='".$required3_path."'> </td>
			<td class='input_box_item'>
				<input type=text name='com_mobile_1' id='com_mobile_1' value='".$com_mobile[0]."' style='width:30px' maxlength=3 size=3  class='textbox' com_numeric=true validation='true' title='핸드폰'> -
				<input type=text name='com_mobile_2' id='com_mobile_2' value='".$com_mobile[1]."' style='width:30px' maxlength=4 size=5 class='textbox' com_numeric=true validation='true' title='핸드폰'> -
				<input type=text name='com_mobile_3' id='com_mobile_3' value='".$com_mobile[2]."' style='width:30px' maxlength=4 size=5 class='textbox' com_numeric=true validation='true' title='핸드폰'>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>이메일</b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='com_email' id='com_email' value='".$db->dt[com_email]."'  style='width:150px' validation='false' title='이메일' email=true>
			</td>
			<td class='input_box_title'> <b>홈페이지</b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='com_homepage' id='com_homepage' value='".$db->dt[com_homepage]."'  style='width:150px'>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>사업장 주소  <img src='".$required3_path."'> </b>    </td>
			<td class='input_box_item' colspan=3>
				<div id='input_address_area' >
				<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
					<col width='80px'>
					<col width='*'>
					<tr>
						<td height=26>
							<input class='textbox'  type=text name='com_zip'  id='zip_b_1' value='".$db->dt[com_zip]."' style='width:60px' validation='true' title='사업장 주소' readonly>
						</td>
						<td style='padding:1px 0 0 5px;'>
							<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='com_addr1'  id='addr_b_1' value='".$db->dt[com_addr1]."' size=50 class='textbox' validation='true' title='사업장 주소' style='width:400px'>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='com_addr2'  id='addr_b_2'  value='".$db->dt[com_addr2]."' size=70 class='textbox' validation='true' title='사업장 상세주소' style='width:400px'> (상세주소)
						</td>
					</tr>
					</table>
				</div>
			</td>
		</tr>
		";

		$ws_tel = explode("-",$db->dt[ws_tel]);
		$ws_charge_phone = explode("-",$db->dt[ws_charge_phone]);
		$kakao_phone = explode("-",$db->dt[kakao_phone]);

	$Contents01 .= "
		  <tr>
			<td class='input_box_title'> <b>도매상권 사용</b>    </td>
			<td class='input_box_item' colspan=3>
				<input type='radio' id='commercial_disp_n' name='commercial_disp' value='N'  ".($db->dt[commercial_disp] == "N" || $db->dt[commercial_disp] == "" ? "checked":"")."> <label for='commercial_disp_n'>미사용</label> &nbsp;&nbsp;
				<input type='radio' id='commercial_disp_y' name='commercial_disp' value='Y' ".($db->dt[commercial_disp] == 'Y' ? "checked":"")." > <label for='commercial_disp_y'>사용</label> 
			</td>
		</tr>
		<tr class='wholesale_tr wholesale_validation' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
			<td class='input_box_title'> <b>상권선택 <img src='".$required3_path."'> </b>    </td>
			<td class='input_box_item'>
				".getCommercialCountry($db->dt[ca_country],"select","onchange=\"window.frames['act'].location.href='../buyingservice/commercial_area.soapload.php?trigger='+this.value+'&target=ca_code&form=edit_form'\"")." ".getSoapCommercialAreaInfo($db->dt[ca_country],$db->dt[ca_code],"onchange=\"window.frames['act'].location.href='../buyingservice/shopping_center.soapload.php?trigger='+this.value+'&target=sc_code&form=edit_form'\"")."
			</td>
			<td class='input_box_title'> <b>상가선택 <img src='".$required3_path."'> </b>    </td>
			<td class='input_box_item'>
				".getSoapShoppingCenter($db->dt[sc_code],"select","onchange=\"window.frames['act'].location.href='../buyingservice/shopping_center_info.soapload.php?trigger='+this.value+'&target=floor&form=edit_form'\"")."
			</td>
		</tr>
		<tr class='wholesale_tr wholesale_validation' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
			<td class='input_box_title'> <b>층/라인/호수 선택 <img src='".$required3_path."'> </b>    </td>
			<td class='input_box_item' colspan='3'>
				".getSoapShoppingCenterFloorInfo($db->dt[sc_code],$db->dt[floor])." 
				".getSoapShoppingCenterLineInfo($db->dt[sc_code],$db->dt[line])." 
				".getSoapShoppingCenterNoInfo($db->dt[sc_code],$db->dt[no])."
			</td>
		</tr>
		<tr class='wholesale_tr wholesale_validation' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
			<td class='input_box_title'> <b>매장 전화번호 <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
				<input type=text name='ws_tel_1' id='ws_tel_1' value='".$ws_tel[0]."' maxlength=3 style='width:20px' class='textbox' com_numeric=true validation='true' title='매장 전화번호'> -
				<input type=text name='ws_tel_2' id='ws_tel_2' value='".$ws_tel[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='매장 전화번호'> -
				<input type=text name='ws_tel_3' id='ws_tel_3' value='".$ws_tel[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='매장 전화번호'>
			</td>
			<td class='input_box_title'> <b>담당자 핸드폰 <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
				<input type=text name='ws_charge_phone_1' id='ws_charge_phone_1' value='".$ws_charge_phone[0]."' maxlength=3 style='width:20px' class='textbox' com_numeric=true validation='true' title='담당자 핸드폰'> -
				<input type=text name='ws_charge_phone_2' id='ws_charge_phone_2' value='".$ws_charge_phone[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='담당자 핸드폰'> -
				<input type=text name='ws_charge_phone_3' id='ws_charge_phone_3' value='".$ws_charge_phone[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='담당자 핸드폰'>
			</td>
		</tr>
		<tr class='wholesale_tr' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
			<td class='input_box_title'> <b>카카오톡 핸드폰번호 </b></td>
			<td class='input_box_item'>
				<input type=text name='kakao_phone_1' id='kakao_phone_1' value='".$kakao_phone[0]."' maxlength=3 style='width:20px' class='textbox' com_numeric=true validation='false' title='카카오톡 핸드폰번호'> -
				<input type=text name='kakao_phone_2' id='kakao_phone_2' value='".$kakao_phone[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='false' title='카카오톡 핸드폰번호'> -
				<input type=text name='kakao_phone_3' id='kakao_phone_3' value='".$kakao_phone[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='false' title='카카오톡 핸드폰번호'>
			</td>
			<td class='input_box_title'> <b>카카오톡 ID </b></td>
			<td class='input_box_item'>
				<input type=text name='kakao_id' id='kakao_id' value='".$db->dt[kakao_id]."' style='width:130px' class='textbox' validation='false' title='카카오톡 ID'>
			</td>
		</tr>
		<tr class='wholesale_tr' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
			<td class='input_box_title'> <b>페이스북 </b></td>
			<td class='input_box_item'>
				<input type=text name='facebook' id='facebook' value='".$db->dt[facebook]."' style='width:130px' class='textbox' validation='false' title='페이스북'>
			</td>
			<td class='input_box_title'> <b>트위터 </b></td>
			<td class='input_box_item'>
				<input type=text name='twitter' id='twitter' value='".$db->dt[twitter]."' style='width:130px' class='textbox' validation='false' title='트위터'>
			</td>
		</tr>
		<tr class='wholesale_tr' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
			<td class='input_box_title'> <b>QQ(중국) </b></td>
			<td class='input_box_item'>
				<input type=text name='qq' id='qq' value='".$db->dt[qq]."' style='width:130px' class='textbox' validation='false' title='QQ(중국)'>
			</td>
			<td class='input_box_title'> <b>WeChat </b></td>
			<td class='input_box_item'>
				<input type=text name='wechat' id='wechat' value='".$db->dt[wechat]."' style='width:130px' class='textbox' validation='false' title='WeChat'>
			</td>
		</tr>";

	$Contents01 .= "
		<!--
		<tr>
			<td class='input_box_title'> <b>본사 담당사업장  <img src='".$required3_path."'> </b></td>
			<td class='search_box_item' colspan='3'>
			<span id='company_name'>".$db->dt[company_name]."</span>
				<table border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td style='padding-right:5px;'>
						".getCompanyList("본사", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='본사' ", '5', $cid2)."</td>
						<td style='padding-right:5px;'>
						".getCompanyList("본사", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='본사'", '15', $cid2)."</td>
						<td style='padding-right:5px;'>
						".getCompanyList("본사", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='본사'", '25', $cid2)."</td>
						<td>".getCompanyList("본사", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='본사'", '35', $cid2)."</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>구매 담당자</b></td>
			<td class='search_box_item' colspan='3'>
			<span id='com_person'></span>
				<table border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td style='padding-right:5px;'>
						".getgroup1($group_ix)."</td>
						<td style='padding-right:5px;'>
						".getdepartment($dp_ix)."</td>
						<td style='padding-right:5px;'>
						".getposition($ps_ix)."</td>
						<td style='padding-right:5px;'>".getduty($cu_ix)."</td>
						<td style='padding-right:5px;'>".get_person($com_group,$department,$position,$duty,$company_id)."</td>
					</tr>
				</table>
			</td>
		</tr>
		-->
		<tr>
			<td class='input_box_title'> <b> 여신한도</b></td>
			<td class='input_box_item'><input type=text name='loan_price' id='loan_price' value='".$db->dt[loan_price]."' class='textbox'  style='width:80px' validation='false' title='여신한도' dir='rtl'> 원</td>
			<td class='input_box_title'><b>보증금</b></td>
			<td class='input_box_item'><input type=text name='deposit_price' id='deposit_price' value='".$db->dt[deposit_price]."' class='textbox'  style='width:80px' validation='false' title='보증금' dir='rtl'> 원</td>
		</tr>
		<!--
		<tr>
			<td class='input_box_title'><b>사업자 인감도장 </b></td>";
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id."/stamp_".$company_id.".gif")){
				$stamp_bool="false";
			}else{
				$stamp_bool="false";
			}

			$Contents01 .= "<td class='input_box_item' colspan=3>
				<table cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td width='320'><input type=file name='stamp_file' size=70 class='textbox'  style='width:300px' validation='$stamp_bool' title='사업자 인감도장'></td>";
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id."/stamp_".$company_id.".gif")){
					$Contents01 .= "<td width='*' style='padding:5px 0px;'><img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/stamp_".$company_id.".gif' width=50></td>
					<td><span style='padding-left:20px;'><b>&nbsp;&nbsp;2M 이하만 등록가능</b></span>
					<a href='javascript:' onclick=\"del('stamp_img','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:20px;'></a></td>
					";
				}

	$Contents01 .= "</tr>
				</table>
			</td>
		</tr>-->
		<tr bgcolor=#ffffff height=110>
			<td class='input_box_title'><b>기타사항</b></td>
			<td class='input_box_item' colspan=3>
			<textarea type=text class='textbox' name='seller_message' id='seller_message' value='".$db->dt[seller_message]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[seller_message]."</textarea>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b> 주요상품 카테고리</b></td>
			<td class='input_box_item'>";
		
					$sql = "select * from shop_category_info where depth = '0' and category_use = '1' and category_type='C'";
					$db->query($sql);
					$cateinfos = $db->fetchall();
	$Contents01 .= "
				<select name='seller_cid' id='seller_cid' style='width:200px;' validation='false' title='주용상품군'>
					<option>선택</option>";
					for($i=0;$i<count($cateinfos);$i++){
					$Contents01 .= "<option value='".$cateinfos[$i]['cid']."' ".($cateinfos[$i]['cid'] == $seller_cid?"selected":"").">".$cateinfos[$i]['cname']."</option>";
					}
	$Contents01 .= "
				</select>
				<input type='hidden' name='md_code' id='md_code' value='".$db->dt[md_code]."'>
				</td>
				<td class='input_box_title'> <b> 주요 브랜드</b></td>
			    <td class='input_box_item'>".BrandListSelect($seller_brand, "")."</td>
		</tr>
		<tr bgcolor=#ffffff height=70>
			<td class='input_box_title'><b>주요판매상품 내용</b></td>
			<td class='input_box_item' colspan=3>
			<textarea type=text class='textbox' name='seller_msg' id='seller_msg' value='".$seller_msg."' style='width:98%;height:50px;padding:2px;'>".$seller_msg."</textarea>
			</td>
		</tr>
		<!--
		<tr>
			<td class='input_box_title'><b>사업자 인감도장 </b></td>";
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif")) $stamp_bool="false";
			else $stamp_bool="false";
			$Contents01 .= "<td class='input_box_item' colspan=3>
				<table cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td width='320'><input type=file name='company_stamp' size=70 class='textbox'  style='width:300px' validation='$stamp_bool' title='사업자 인감도장'></td>
				";
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif")){
					$Contents01 .= "<td width='*' style='padding:5px 0px;'><img src='".$admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif' width=50></td>";
				}
	$Contents01 .= "
					</tr>
				</table>
			</td>
		</tr>-->
		<!--
		<tr>
			<td class='input_box_title'> <b>통장사본 </b></td>
			<td class='input_box_item' colspan=3 >	<input type=file name='bank_file' size=70 class='textbox'  style='width:300px'></td>";
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/seller/".$company_id."/bank_file_".$company_id.".gif")){
					$Contents01 .= "<td><img src='".$admin_config[mall_data_root]."/images/seller/".$company_id."/bank_file_".$company_id.".gif'></td> ";
				}
	$Contents01 .= "
		</tr>-->
		<!--<tr>
				<td class='input_box_title'><b>증빙서류 </b></td>
				<td class='input_box_item' colspan=3 ><input type=file name='ktp_file' size=70 class='textbox'  style='width:300px'></td>";
					if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/seller/".$company_id."/ktp_file_".$company_id.".gif")){
						$Contents01 .= "<td><img src='".$admin_config[mall_data_root]."/images/seller/".$company_id."/ktp_file_".$company_id.".gif'></td> ";
					}
	$Contents01 .= "
		</tr>-->";
		  if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
	$Contents01 .= "
		<!--<tr>
			<td class='input_box_title'> <b>보리보리 브랜드코드</b>    </td>
			<td class='input_box_item' colspan=3>
				<input type=text name='halfclub_code' id='halfclub_code' value='".$db->dt[halfclub_code]."' class='textbox'  style='width:25px' validation='false' title='보리보리 브랜드코드'>
			</td>
		</tr>-->
		<tr>
			<td class='input_box_title'> <b>API상품연동제외</b>    </td>
			<td class='input_box_item' colspan=3>
				";
			$sql = "select * from sellertool_site_info where api_yn = 'Y'";
			$db2->query($sql);

			if($db2->total){
				for($i=0; $i < $db2->total; $i++){
					$db2->fetch($i);
					$sql = "select state from sellertool_not_company where company_id = '".$company_id."' and site_code = '".$db2->dt[site_code]."'";

					$mdb->query($sql);

					if($mdb->total){
						$mdb->fetch();
						$api_get_product_state = $mdb->dt[state];
						$Contents01 .= "
						<input type=checkbox name='api_get_product[]' id='api_get_product_".$i."' value='".$db2->dt[site_code]."' ".CompareReturnValue("1",$mdb->dt[state],"checked")." ><label for='api_get_product_".$i."'>".$db2->dt[site_name]."</label>";
					}else{
						$api_get_product_state = '';
						$Contents01 .= "
						<input type=checkbox name='api_get_product[]' id='api_get_product_".$i."' value='".$db2->dt[site_code]."' ><label for='api_get_product_".$i."'>".$db2->dt[site_name]."</label>";
					}

					$Contents01 .= "
					<input type='hidden' name='api_get_product_befor[".$db2->dt[site_code]."]' value='".$api_get_product_state."'  />";
				}
			}
	$Contents01 .= "
				<span style='color:red; vertical-align:middle;'>[# 체크된 제휴사는 상품을 연동하지 않습니다.]</span>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>셀러업체승인</b>    </td>
			<td class='input_box_item' colspan=3>
				<input type=radio name='seller_auth' id='seller_auth_N' value='N' ".CompareReturnValue("N",$db->dt[seller_auth],"checked")." checked><label for='seller_auth_N'>승인대기</label>
				<input type=radio name='seller_auth' id='seller_auth_Y' value='Y' ".($act == "seller_insert" ? "checked" : CompareReturnValue("Y",$db->dt[seller_auth],"checked"))."><label for='seller_auth_Y'>승인</label>
				<input type=radio name='seller_auth' id='seller_auth_X' value='X' ".CompareReturnValue("X",$db->dt[seller_auth],"checked")."><label for='seller_auth_X'>승인거부</label>
				 &nbsp;&nbsp;&nbsp;&nbsp;<span class=small style='color:gray'><!--입점업체 승인후에 사용자 등록이 가능합니다. --> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span>
			</td>
		</tr>";
		}
	$Contents01 .= "
		</table>";
		$Contents01 .= getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');
	}

	if($info_type == "order_info"){

	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>담당자정보 입력</b>
			</td>
		</tr>
		</table>";

	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
		<colgroup>
			<col width='15%' />
			<col width='35%' style='padding:0px 0px 0px 10px'/>
			<col width='15%' />
			<col width='35%' style='padding:0px 0px 0px 10px'/>
		</colgroup>
		<tr>
			<td class='input_box_title'> <b>담당자명</b></td>
			<td class='input_box_item' colspan='3'>
			<input type=text name='customer_name' value='".$db->dt[customer_name]."' class='textbox'  style='width:200px' validation='false' title='담당자명'>
			</td>
		</tr>

		<tr>
			<td class='input_box_title'> <b>전화번호</b></td>
			<td class='input_box_item'>
			<input type=text name='customer_phone' value='".$db->dt[customer_phone]."' class='textbox'  style='width:200px' validation='false' title='전화번호'>
			</td>
			<td class='input_box_title'> <b>핸드폰번호</b>   </td>
			<td class='input_box_item'><input type=text name='customer_mobile' value='".$db->dt[customer_mobile]."' class='textbox'  style='width:200px' validation='false' title='핸드폰번호'></td>
		</tr>

		<tr>
			<td class='input_box_title'> <b>이메일</b></td>
			<td class='input_box_item'>
			<input type=text name='customer_mail' value='".$db->dt[customer_mail]."' class='textbox'  style='width:200px' email='true' title='이메일'>
			</td>
			<td class='input_box_title'> <b>직급/직책</b>   </td>
			<td class='input_box_item'><input type=text  id='' name='customer_position' value='".$db->dt[customer_position]."' class='textbox'  style='width:200px' validation='false' title='직급/직책'></td>
		</tr>
		<tr bgcolor=#ffffff height=110>
			<td class='input_box_title'><b>기타사항</b></td>
			<td class='input_box_item' colspan=3>
			<textarea type=text class='textbox' name='seller_message' value='".$db->dt[customer_message]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[customer_message]."</textarea>
			</td>
		</tr>
		</table><br>";


	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>C/S 담당자정보</b>
			</td>
		</tr>
		</table>";
	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
		<colgroup>
			<col width='15%' />
			<col width='35%' style='padding:0px 0px 0px 10px'/>
			<col width='15%' />
			<col width='35%' style='padding:0px 0px 0px 10px'/>
		</colgroup>
		<tr>
			<td class='input_box_title'> <b>담당자명</b></td>
			<td class='input_box_item' colspan='3'>
			<input type=text name='cs_name' id='cs_name' value='".$db->dt[cs_name]."' class='textbox'  style='width:200px' validation='false' title='담당자명'>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>전화번호</b></td>
			<td class='input_box_item'>
			<input type=text name='cs_phone'  id='cs_phone' value='".$db->dt[cs_phone]."' class='textbox'  style='width:200px' validation='false' title='전화번호'>
			</td>
			<td class='input_box_title'> <b>핸드폰번호</b></td>
			<td class='input_box_item'><input type=text name='cs_mobile' id='cs_mobile' value='".$db->dt[cs_mobile]."' class='textbox'  style='width:200px' validation='false' title='핸드폰번호'></td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>이메일</b></td>
			<td class='input_box_item'>
			<input type=text name='cs_mail' id='cs_mail' value='".$db->dt[cs_mail]."' class='textbox'  style='width:200px' email='true' title='이메일'>
			</td>
			<td class='input_box_title'> <b>직급/직책</b>   </td>
			<td class='input_box_item'><input type=text id='cs_position' name='cs_position' value='".$db->dt[cs_position]."' class='textbox'  style='width:200px' validation='false' title='직급/직책'></td>
		</tr>
		<tr bgcolor=#ffffff height=110>
			<td class='input_box_title'><b>기타사항</b></td>
			<td class='input_box_item' colspan=3>
			<textarea type=text class='textbox' name='cs_message' id='cs_message' value='".$db->dt[cs_message]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[cs_message]."</textarea>
			</td>
		</tr>
		</table><br>";


	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>세무 담당자</b>
			</td>
		</tr>
		</table>";
	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
		<colgroup>
			<col width='15%' />
			<col width='35%' style='padding:0px 0px 0px 10px'/>
			<col width='15%' />
			<col width='35%' style='padding:0px 0px 0px 10px'/>
		</colgroup>
		<tr>
			<td class='input_box_title'> <b>담당자명</b></td>
			<td class='input_box_item'>
			<input type=text name='tax_person_name' value='".$db->dt[tax_person_name]."' class='textbox' validation='true' style='width:200px' validation='false' title='담당자명'>
			</td>
			<td class='input_box_item' colspan='2'> <input type='checkbox' id='check_all' value='all'> <label for='check_all'>상기내용과 동일시 체크박스에 클릭하세요.</label></td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>전화번호</b></td>
			<td class='input_box_item'>
			<input type=text name='tax_person_phone' value='".$db->dt[tax_person_phone]."' class='textbox'  style='width:200px' validation='false' title='전화번호'>
			</td>
			<td class='input_box_title'> <b>핸드폰번호</b>   </td>
			<td class='input_box_item'><input type=text name='tax_person_mobile' value='".$db->dt[tax_person_mobile]."' class='textbox'  style='width:200px' validation='false' title='핸드폰번호'></td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>이메일</b></td>
			<td class='input_box_item'>
			<input type=text name='tax_person_mail' value='".$db->dt[tax_person_mail]."' class='textbox'  style='width:200px' email=true title='이메일'>
			</td>
			<td class='input_box_title'> <b>직급/직책</b>   </td>
			<td class='input_box_item'><input type=text  id='' name='tax_person_position' value='".$db->dt[tax_person_position]."' class='textbox'  style='width:200px' validation='false' title='직급/직책'></td>
		</tr>
		<tr bgcolor=#ffffff height=110>
			<td class='input_box_title'><b>기타사항</b></td>
			<td class='input_box_item' colspan=3>
			<textarea type=text class='textbox' name='tax_seller_message' value='".$db->dt[tax_person_message]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[tax_person_message]."</textarea>
			</td>
		</tr>
		</table><br>";
	}

	if($info_type == "seller_info"){

	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>상점정보</b>
			</td>
		</tr>
		</table>";
	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box' >
		<col width='15%' />
		<col width='35%' />
		<col width='15%' />
		<col width='35%' />
		<tr bgcolor=#ffffff height=34>
			<td class='input_box_title' nowrap> <b>상점 이름 <img src='".$required3_path."'></b</td>
			<td class='input_box_item' colspan='3'><input type=text name='shop_name' value='".$db->dt[shop_name]."' class='textbox'  style='width:350px' validation=true title='상점 이름'></td>
		</tr>
		<tr bgcolor=#ffffff height=34>
			<td class='input_box_title' > 미니샵 사용여부</td>
			<td class='input_box_item' >
			<input type='radio' name='minishop_use' id='minishop_use_1' value='1' ".CompareReturnValue("1",$db->dt[minishop_use],"checked")."><label for='minishop_use_1'> 사용 </label>&nbsp;
			<input type='radio' name='minishop_use' id='minishop_use_0' value='0' ".CompareReturnValue("0",$db->dt[minishop_use],"checked")."><label for='minishop_use_0'> 미사용 </label>
			</td>
			<td class='input_box_title' nowrap> <b>홈페이지 </b</td>
			<td class='input_box_item' ><input type=text name='homepage' value='".$db->dt[homepage]."' class='textbox'  style='width:200px'></td>
		</tr>
		<tr bgcolor=#ffffff height=104>
			<td class='input_box_title' > <b>상점 설명</b>   </td>
			<td class='input_box_item' colspan=3><textarea name='shop_desc'  style='width:600px;height:70px;padding:5px;' validation=false title='상점 설명'>".$db->dt[shop_desc]."</textarea></td>
		</tr>
		<tr bgcolor=#ffffff height=34>
			<td class='input_box_title'> <b>상점 로고 </b>  </td>
			<td class='input_box_item' colspan=3 style='padding:5px 5px;'>
			<input type=file name='shop_logo_img' size=30 class='textbox'  style='width:300px;'> <!--권장 사이즈 305 * 264 --> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'I')."<br>";

			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_logo_".$company_id.".gif")){
				$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/shopimg/shop_logo_".$company_id.".gif' width='305' height='264' style='margin:10px;'> ";
			}

	$Contents01 .= "
			</td>
		</tr>
		<tr bgcolor=#ffffff height=34>
			<td class='input_box_title' nowrap> <b>상점 이미지 </b>  </td>
			<td class='input_box_item' colspan=3>
			<input type=file name='shop_img' size=30 class='textbox'  style='width:300px'>  <!--권장 사이즈 305 * 264 --> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'J')."<br>";

		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_".$company_id.".gif")){
			$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/shopimg/shop_".$company_id.".gif' width='305' height='264' style='margin:10px;'> ";
		}

	$Contents01 .= "
			</td>
		</tr>
		<tr bgcolor=#ffffff height=34>
			<td class='input_box_title' nowrap> <b>상점 썸네일이미지 </b>  </td>
			<td class='input_box_item' colspan=3>
			<input type=file name='shop_img_thum' size=30 class='textbox'  style='width:200px'>  <!--권장 사이즈 200 * 200 --> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'J')."<br>";

		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_thum_".$company_id.".gif")){
			$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/shopimg/shop_thum_".$company_id.".gif' width='200' height='200' style='margin:10px;'> ";
		}

	$Contents01 .= "
			</td>
		</tr>";

	if($admininfo[mall_type] == "O" || $admininfo[mall_type] == "E"){
	$Contents01 .= "
		<tr bgcolor=#ffffff height=34 >
			<td class='input_box_title'> <b>미니샵 템플릿</b>    </td>
			<td class='input_box_item' colspan=3 style='padding:0px 0px 0px 10px'>
			 ".SelectDirList("minishop_templet", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/minishop_templet", $db->dt[minishop_templet],"false")."
			</td>
		</tr>";

	if($_SESSION["admininfo"]["mem_type"]=="S" && $_SESSION["admininfo"]["admin_level"]==8) {
		$txt_text="text";
		$Contents01 .= "<input type='hidden' name='team' value='".$team."' /><input type='hidden' name='md_code' value='".$db->dt[md_code]."' />";
	} else {
		$txt_text="";
	}
	$Contents01 .= "
	<!--
		<tr bgcolor=#ffffff height=34 >
			<td class='input_box_title'> <b>담당 MD</b>    </td>
			<td colspan=3 style='padding:0px 0px 0px 10px'>
			 ".getRegionInfoSelect('parent_rg_ix', '1차 지역',$parent_rg_ix, $parent_rg_ix, 1, " onChange=\"loadRegion(this,'rg_ix')\" ",$txt_text)."
			 ".getRegionInfoSelect('rg_ix', '2차 지역',$parent_rg_ix, $rg_ix, 2, "validation=false title='지역' onChange=\"loadBranch(this,'branch')\" ",$txt_text)."
			 ".makeBranchSelectBox($cdb,'branch', $rg_ix, $branch, '지사', "validation=false title='지사' onChange=\"loadTeam(this,'team')\" ",$txt_text)."
			 ".makeTeamSelectBox($cdb,'team', $branch,$team,  '팀', "".($db->dt[mem_level] == "11" ? "validation=false":"validation=false")." title='팀' onChange=\"loadSellerManager(this,'md_code')\"  ",$txt_text)."
			 ".getSellerManager($branch, $team, $db->dt[md_code],$txt_text)."
			</td>
		</tr>
		  -->";
	}

	$Contents01 .= "
		</table>
		<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
		<tr height=40>
			<td colspan=4 style='padding:10px 0px 50px 5px'>
				<img src='../image/emo_3_15.gif' align=absmiddle> <span class='small'><!--미니샵 및 상점소개 페이지에서 노출되는 정보입니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')." </span>
			</td>
		</tr>
		</table>";

	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>탑셀러</b>
			</td>
		</tr>
		</table>";


	$topseller_display_disabled = $db->dt[topseller_display] == 0 ? 'disabled' : '';
	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box' >
		<col width='15%' />
		<col width='35%' />
		<col width='15%' />
		<col width='35%' />
		<tr bgcolor=#ffffff height=34>
			<td class='input_box_title'> 노출여부 </td>
			<td class='input_box_item' width='30%' >
				<input type='radio' name='topsellerDisplay' id='topseller_display1' value='1' ".CompareReturnValue("1",$db->dt[topseller_display],"checked")."
				 onclick='$(\"input[name=displayPriority]\").prop(\"disabled\", false)'>
				 
				 <label for='topseller_display1' onclick='$(\"#topseller_display1\").trigger(\"click\")'> 노출 </label>&nbsp;

				<input type='radio' name='topsellerDisplay' id='topseller_display0' value='0' ".CompareReturnValue("0",$db->dt[topseller_display],"checked")."
				 onclick='$(\"input[name=displayPriority]\").val(\"\");$(\"input[name=displayPriority]\").prop(\"disabled\", true)'>
				 
				 <label for='topseller_display0'' onclick='$(\"#topseller_display0\").trigger(\"click\")'> 미노출 </label>

			<td class='input_box_title'> 노출순위 </td>
			<td class='input_box_item' width='30%' ><input type=text name='displayPriority' value='".$db->dt[display_priority]."' class='textbox'  style='width:200px'" . $topseller_display_disabled . "></td>
		</tr>";

	$Contents01 .= "
		</table>";

	$Contents01 .= "<table><tr style='height:50px'><td></td></tr></table>";
	

	//-------------------------------------------------------------------------------------------------------------------------//
	//                                               미니샵 배너 관리 시작                                                       //

	$companyId = $_GET['company_id'];
	if($companyId == ''){
		$companyId = $_SESSION['user']['company_id'];
	}

	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>미니샵 배너관리</b>
			</td>
		</tr>
		</table>";


	$topseller_display_disabled = $db->dt[topseller_display] == 0 ? 'disabled' : '';
	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box' >
		<col width='15%' />
		<col width='*' />
		<tr bgcolor=#ffffff height=34>
			<td class='input_box_title'> 			
				<table cellpadding=0 cellspacing=0 height='78px;'>
					<tr>
						<td width='70px'><b style='color:#000000;'>상세내용 <img src='".$required3_path."'></b><td>
						<td><div style='margin-left:15px;margin-top:5px;'><img src='../images/".$admininfo["language"]."/btn_add.gif' alt='옵션추가' id='banner_addbtn'> <img src='../images/".$admininfo["language"]."/btn_del.gif' alt='옵션삭제' id='banner_delbtn'><div>
						</td>
					</tr>
				</table>
			</td>
			<td class='input_box_item' colspan='3'>

		<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' id='banner_table'>
		";
	//$mfdArr = array();
	$db2->query("SELECT * FROM shop_minishop_banner  where company_id = '".$companyId."' order by ix ASC ");
	if($db2->total){
		$mbArr = $db2->fetchall();
	}
	$clon_no = 0;
	if(is_array($mbArr)){
		foreach($mbArr as $_key=>$_value){

			if($_key == 0) {
			$Contents01 .= "<tbody>";
			} else if($_key == 1){
			$Contents01 .= "<tfoot>";
			}
			$Contents01 .= "
					  <tr bgcolor=#ffffff  class='clone_tr'>

						<td height='25' style='padding:10px 0; solid #d3d3d3;'>
						<input type=hidden name='ix[]' class='ix' value='".$mbArr[$_key][ix]."' style='width:230px;' validation=false>
						 첨부파일 : <input type=file class='textbox' name='file[]' style='width:255px;' validation=false title='파일'> <span class='file_text'>".$mbArr[$_key][file]."<input type='checkbox' name='nondelete[".$mbArr[$_key][ix]."]' value='1' checked>업로드된 파일유지</span><br><br>
						 
						 링&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;크 : <input type=text class='textbox link' name='link[]' value='".$mbArr[$_key][link]."' style='width:248px;' validation=true title='링크'>
						 <br />
						 <br />

						 타&nbsp;이&nbsp;&nbsp;틀 : <input type=text class='textbox title' name='title[]' value='".$mbArr[$_key][title]."' style='width:230px;' validation=true title='타이틀'>
						 <br />
						 <br />

						 배너 우선순위 : <input type=text class='textbox bannerPriority' name='bannerPriority[]' value='" . $mbArr[$_key][banner_priority] . "' style='width:230px;' validation=true title='우선순위'>
						</td>
					  </tr>
					  ";
			if($_key == 0) {
			$Contents01 .= "</tbody>";
			} else {
				$clon_no++;
			}
		}
	} else {
			$Contents01 .= "
					 <tbody>
					  <tr bgcolor=#ffffff  class='clone_tr'>
						<td height='25' style='padding:10px 0; solid #d3d3d3;'>
						<input type=hidden name='ix[]' value='' style='width:230px;' validation=false>
						 첨부파일 : <input type=file class='textbox' name='file[]' style='width:255px;' validation=true title='파일'> <br><br>
						 
						 링&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;크 : <input type=text class='textbox link' name='link[]' value='' style='width:248px;' validation=true title='링크'>
						 <br />
						 <br />

						 타&nbsp;이&nbsp;&nbsp;틀 : <input type=text class='textbox title' name='title[]' value='' style='width:230px;' validation=true title='타이틀'>
						 <br />
						 <br />

						 배너 우선순위 : <input type=text class='textbox title' name='bannerPriority[]' value='' style='width:230px;' validation=true title='우선순위'>
						</td>
					  </tr>
					 </tbody>
					 ";
	}
	if($clon_no == 0){
	$Contents01 .= "<tfoot>";
	}
	$Contents01 .= "
			</tfoot>
			</table>
				</td>
			</tr>";

		$Contents01 .= "
			</table>";


	//                                               미니샵 배너 관리 끝                                                       //
	//-------------------------------------------------------------------------------------------------------------------------//






	$Contents01 .= "<table><tr style='height:50px'><td></td></tr></table>";

	$Contents01 .= " 
        <!--
		<table width='100%' cellpadding=0 cellspacing=0 >
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>거래은행 / 계좌번호</b>
			</td>
		</tr>
		</table>";

	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box' >
		<col width='15%' />
		<col width='35%' />
		<col width='15%' />
		<col width='35%' />
		<tr bgcolor=#ffffff height=34>
			<td class='input_box_title'> 예금주    </td>
			<td class='input_box_item' width='30%' ><input type=text name='bank_owner' value='".$db->dt[bank_owner]."' class='textbox'  style='width:200px'></td>
			<td class='input_box_title'> 거래은행    </td>
			<td class='input_box_item' width='30%' ><input type=text name='bank_name' value='".$db->dt[bank_name]."' class='textbox'  style='width:200px'></td>
		</tr>
		<tr bgcolor=#ffffff height=34>
			<td class='input_box_title'> 계좌번호    </td>
			<td class='input_box_item' colspan=3><input type=text name='bank_number' value='".$db->dt[bank_number]."' class='textbox'  style='width:200px' title='계좌번호' onKeyup='ch_banknum(this)'></td>

		</tr>";

	$Contents01 .= "
		</table>
        -->
        ";
	}


	if($info_type == "delivery_info"){
	/*
	if($max == ""){
		$max = 15; //페이지당 갯수
	}else{
		$max = $max;
	}

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
	*/
	if($info_type == ""){
		$info_type = "basic";
	}
	//$company_id = $admininfo[company_id];		//셀러용

	$db = new Database;
	$db2 = new Database;
	$cdb = new Database;
	$mdb = new Database;

	$sql = "select * from common_seller_delivery where company_id = '".$company_id."'
			";
	$db->query($sql);
	$db->fetch();
	$delivery_free_policy = $db->dt[delivery_free_policy];
	$delivery_company = $db->dt[delivery_company];
	$delivery_product_policy = $db->dt[delivery_product_policy];
	$delivery_deadline_yn = $db->dt[delivery_deadline_yn];
	$delivery_deadline_hour = $db->dt[delivery_deadline_hour];
	$delivery_deadline_minute = $db->dt[delivery_deadline_minute];

	$sql = "select * from shop_delivery_template as dt inner join common_company_detail as ccd on (dt.company_id = ccd.company_id)
			        where dt.company_id = '".$company_id."'
			     order by dt.regdate DESC";

	$db->query($sql);
	$template_dt_array = $db->fetchall();
	$total = count($template_dt_array);

	$act = "seller_updateinfo";
	/*
	if($search_text != ""){
		$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&list_type=$list_type","view");
	}else{
		$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype&list_type=$list_type","view");
	}
	*/
	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>배송정책 설정</b>
			</td>
		</tr>
		</table>";

	$Contents01 .= "
		<form name='edit_form' action='company.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='act'>
		<input name='act' type='hidden' value='template_update'>
		<input name='info_type' type='hidden' value='$info_type'>
		<input name='company_id' type='hidden' value='".$company_id."'>

		<input type='hidden' name='delivery_policy' value='2'><!-- 셀러별 개별정책 설정은 마스터만 설정가능 셀러는 셀러업체 배송정책을 사용-->
		<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='input_table_box'>
		<col width='20%' />
		<col width='80' />

		<tr bgcolor=#ffffff>
			<td class='input_box_title'> 택배업체 설정 </td>
			<td class='input_box_item' style='padding:5px;'>
				<table cellpadding=0 cellspacing=0 border=0 width='640' height='150' style='table-layout:fixed;'>
				<col width='220' />
				<col width='*' />
					<!--tr height=25><td colspan='2'><span>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'V')." </span></td></tr-->
					<tr>
						<td><div id='searchDelieryCompanyArea' style='overflow:auto;height:105px;width:200px;border:1px solid silver;padding:10px;margin:10px 0px;' >".deliveryCompanyList($delivery_company,"seller_list","",$compnay_id)."</div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr bgcolor=#ffffff height=50>
			<td class='input_box_title'> <b> 배송마감 시간 </b></td>
			<td class='input_box_item'>
				<input type=radio name='delivery_deadline_yn' value='N' id='delivery_deadline_n'  ".CompareReturnValue("N",$delivery_deadline_yn,"checked")." onclick=\"$('#delivery_deadline_area').hide();\"><label for='delivery_deadline_n'>미사용</label>
				<input type=radio name='delivery_deadline_yn' value='Y' id='delivery_deadline_y'  ".CompareReturnValue("Y",$delivery_deadline_yn,"checked")." onclick=\"$('#delivery_deadline_area').show();\" ><label for='delivery_deadline_y'>사용</label>
				<span id='delivery_deadline_area' style='".($delivery_deadline_yn=='Y' ? "" : "display:none;" )."'>
                  <select name='delivery_deadline_hour'>";
	                    for ($i=0;$i < 24;$i++){
                            $Contents01 .= "<option value='".$i."' ".($delivery_deadline_hour == $i ? "selected" : "").">".$i."</option>";
                        }
                    $Contents01 .= "
                  </select>시 
                  <select name='delivery_deadline_minute'>";
	                    for ($i=0;$i < 60;$i+=5){
                            $Contents01 .= "<option value='".$i."' ".($delivery_deadline_minute == $i ? "selected" : "").">".$i."</option>";
                        }
                    $Contents01 .= "
                  </select>분
				</span>
			</td>
		</tr>
		</table>
		
		<table width='100%' height='80' cellpadding=0 cellspacing=0 border='0' align='left'>
		<tr bgcolor=#ffffff >
			<td colspan=4 align=center>
				<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >
			</td>
		</tr>
		</table><br><br>
		</form>";

	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 border='0' >
		<tr>
			<td colspan=8>
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
						<td style='width:100%;padding:20px 0px 10px 0px' valign=top colspan=3>
							<img src='../images/dot_org.gif' align=absmiddle> <b>배송정책 템플릿 리스트</b> 전체 : ( ".$total." ) 개
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>

		<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
			<col width='4%'>
			<col width='12%'>
			<col width='15%'>
			<col width='6%'>
			<col width='9%'>
			<col width='6%'>
			<col width='*'>
			<col width='7%'>

			<tr bgcolor=#efefef align=center height=27>
				<td class='s_td'>번호</td>
				<td class='m_td'>배송정책명</td>
				<td class='m_td'>셀러명</td>
				<td class='m_td'>소매/도매</td>
				<td class='m_td'>배송비 결제수단</td>
				<td class='m_td'>배송 방식</td>
				<td class='m_td'>배송비 조건</td>
				<td class='e_td'>관리</td>
			</tr>";

	if($total > 0){

			for($j =0;$j<count($template_dt_array);$j++){

				$no = $total - ($page - 1) * $max - $j;

	$Contents01 .="
					<tr height=32 align=center>";

					$Contents01 .="
						<td class='list_box_td list_bg_gray'>".$no."</td>
						<td class='list_box_td point'>".$template_dt_array[$j][template_name]."</td>
						<td class='list_box_td point'>
							<a href='#'>".$template_dt_array[$j][com_name]."</a>";
						if($template_dt_array[$j][is_basic_template] == '1'){
							$Contents01 .="<span style='color:red;'><br>(기본배송정책)</span>";
						}
					$Contents01 .="
						</td>
						";
					
					switch($template_dt_array[$j][product_sell_type]){
						case 'R':
							$product_sell_type = '소매';
							break;
						case 'W':
							$product_sell_type = '도매';
							break;
					}
					//delivery_basic_policy
					
					switch($template_dt_array[$j][delivery_basic_policy]){
						case '1':
							$delivery_basic_policy = '선불';
							break;
						case '5':
							$delivery_basic_policy = '선불/착불 선택';
							break;
						case '2':
							$delivery_basic_policy = '착불';
							break;
					}

					//delivery_package
					switch($template_dt_array[$j][delivery_package]){
						case 'N':
							$delivery_package = '묶음배송';
							break;
						case 'Y':
							$delivery_package = '개별배송';
							break;
					}

					switch($template_dt_array[$j][delivery_policy]){
						case '1':
							$template_text = "조건 배송비 (".$delivery_package.") : 무료 </br> 착불 인 경우에 (상품 수령 후 지불) / 상품 상세페이지 내 착불 배송료 확인";
							break;
						case '2':
							$template_text = "조건 배송비 (".$delivery_package.") : 고정배송비 ".number_format($template_dt_array[$j][delivery_price])." 원";
							break;
						case '3':
							$sql = "select * from shop_delivery_terms where dt_ix = '".$template_dt_array[$j][dt_ix]."' and delivery_policy_type = '3' order by seq ASC limit 0,1";
							$db->query($sql);
							$db->fetch();
							$template_text = "조건 배송비 (".$delivery_package.") : 주문결제금액 할인 / 주문금액 ".number_format($db->dt[delivery_basic_terms])." 원 미만일경우 ".number_format($db->dt[delivery_price])." 원";
							break;
						case '4':
							$sql = "select * from shop_delivery_terms where dt_ix = '".$template_dt_array[$j][dt_ix]."'  and delivery_policy_type = '4' order by seq ASC limit 0,1";
							$db->query($sql);
							$db->fetch();
							$template_text = "조건 배송비 (".$delivery_package.") : 수량별 할인 / 기본배송비 ".number_format($template_dt_array[$j][delivery_cnt_price])." 원 ".number_format($db->dt[delivery_price])." 개 이상시 ".number_format($db->dt[delivery_basic_terms])." 원 배송비 적용";
							break;
						case '5':
							$sql = "select * from shop_delivery_address where addr_ix = '".$template_dt_array[$j][factory_info_addr_ix]."'";
							$db->query($sql);
							$db->fetch();

							$template_text = "조건 배송비 (".$delivery_package.") : 출고지별 배송비 ( ".$db->dt[addr_name]." )";
							break;
						case '6':
							$template_text = "조건 배송비 (".$delivery_package.") : 상품 1개단위 배송비 ".number_format($template_dt_array[$j][delivery_unit_price])." 원";
							break;
					}
				
	$Contents01 .="
						<td class='list_box_td'>".$product_sell_type."</td>
						<td class='list_box_td'>".$delivery_basic_policy."</td>
						<td class='list_box_td'>".$delivery_package."</td>
						<td class='list_box_td'>".$template_text."</td>";

	$Contents01 .="
						<td class='list_box_td' align=center style='padding:0px 5px' nowrap>";

				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				
					$Contents01 .="
						<a href=\"javascript:PoPWindow3('../product/product_delivery_template.php?mmode=pop&dt_ix=".$template_dt_array[$j][dt_ix]."&page_type=seller&company_id=".$company_id."',960,960,'company')\"'>
						<img src='../images/".$_SESSION["admininfo"]["language"]."/btc_modify.gif' align=absmiddle border=0>
						</a>";
				}else{
					$Contents01 .="
						<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'></a>
						";
				}

				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$Contents01 .="
						<a href=\"JavaScript:DeleteTemplate('".$template_dt_array[$j][dt_ix]."','".$template_dt_array[$j][company_id]."','seller')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle'></a> ";
				}else{
					$Contents01 .="
						<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle'></a> ";
				}

				$Contents01 .="
						</td>";

	$Contents01 .="
					</tr>";
			}
		
	}else{
		$Contents01 .= "<tr height=50><td colspan=8 align=center style='padding-top:10px;'>등록된 배송정책 템플릿이 없습니다.</td></tr>";
	}

	$Contents01 .="</table><br>";

	$Contents01 .="<table width='100%' cellpadding=0 cellspacing=0 border='0' >";
		if( $admininfo[mall_use_multishop] && $admininfo[admin_level] == 9){
			$Contents01 .= "<tr hegiht=30><td colspan=7 align=center style='padding:10px 0px;'>".$str_page_bar."</td></tr>";
			
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
			$Contents01 .= "
							<tr hegiht=30>
								<td colspan=7 align=right style='padding-top:2px;'>
									<a href=\"javascript:PoPWindow3('../product/product_delivery_template.php?mmode=pop&page_type=seller&company_id=".$company_id."',960,960,'company')\"'>
									<img src='../images/".$admininfo["language"]."/btn_delivery_add.gif' border=0>
									</a>
								</td>
							</tr>";
			}
		}else{
			$Contents01 .= "<tr hegiht=30>
								<td colspan=7 align=right style='padding-top:10px 0px;'>".$str_page_bar."</td>
							</tr>";
		}
	$Contents01 .="</table><br>";

	$Contents01 .= "
	<script language='javascript'>

	function DeleteTemplate(dt_ix,company_id,page_type){

		if(confirm('배송정책 템플릿을 삭제하시겠습니까?')){
			document.location.href='../product/product_delivery.act.php?act=delete_template&dt_ix='+dt_ix+'&company_id='+company_id+'&page_type='+page_type;
		}
	}

	</script>
	";

	}

	if($info_type == 'factory_info'){

	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
			<tr>
				<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
					<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>출고지 관리</b>
				</td>
			</tr>
		</table>";

	$Contents01 .= "
		<form name='edit_form' action='company.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='act'><!--target='iframe_act' -->
		<input name='act' type='hidden' value='$act'>
		<input name='info_type' type='hidden' value='$info_type'>
		<input name='company_id' type='hidden' value='".$company_id."'>
		<input name='delivery_type' type='hidden' value='F'>
		<input name='mall_ix' type='hidden' value='".$admininfo[mall_ix]."'>
		<input name='addr_ix' type='hidden' value='$addr_ix'>
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
			<colgroup>
				<col width='20%' />
				<col width='30%' style='padding:0px 0px 0px 10px'/>
				<col width='20%' />
				<col width='30%' style='padding:0px 0px 0px 10px'/>
			</colgroup>
			<tr>
				<td class='input_box_title'> <b>출고지명 <img src='".$required3_path."'> </b></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name='addr_name' value='".$db->dt[addr_name]."' style='width:150px' validation='true' title='출고지명'>
				</td>
				<td class='input_box_title'> <b>담당자명 <img src='".$required3_path."'></b></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name='person_name' value='".$db->dt[person_name]."' title='담당자명' validation='true' style='width:150px'>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'> <b>일반 전화번호 <img src='".$required3_path."'> </b></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name='addr_phone_1' id='addr_phone_1' value='".$addr_phone[0]."' maxlength=3 com_numeric=true style='width:20px' validation='true' title='전화'> - 
					<input type=text class='textbox' name='addr_phone_2' id='addr_phone_2' value='".$addr_phone[1]."' maxlength=4 com_numeric=true style='width:30px' validation='true' title='전화'> -
					<input type=text class='textbox' name='addr_phone_3' id='addr_phone_3' value='".$addr_phone[2]."' maxlength=4 com_numeric=true style='width:30px' validation='true' title='전화'>
				</td>
				<td class='input_box_title'> <b>핸드폰번호 <img src='".$required3_path."'> </b></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name='addr_mobile_1' id='addr_mobile_1' value='".$addr_mobile[0]."' maxlength=3 com_numeric=true style='width:20px' validation='true' title='핸드폰'> - 
					<input type=text name='addr_mobile_2' id='addr_mobile_2' value='".$addr_mobile[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='핸드폰'> -
					<input type=text name='addr_mobile_3' id='addr_mobile_3' value='".$addr_mobile[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='핸드폰'>
				</td>
			</tr>
			<tr>
			<td class='input_box_title'> <b>출고지 주소 <img src='".$required3_path."'> </b></td>
			<td class='input_box_item' colspan=3>
				<div id='input_address_area' >
				<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
					<col width='70px'>
					<col width='*'>
					<tr>
						<td height=26>
							<input type='text' class='textbox' name='com_zip' id='zip_b_1' value='".$db->dt[zip_code]."' maxlength=15  style='width:60px' validation='true' title='우편코드' readonly>
						</td>
						<td style='padding:1px 0 0 5px;'>
							<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='com_addr1'  id='addr_b_1' value='".$db->dt[address_1]."' size=50 class='textbox'  style='width:300px' readonly>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='com_addr2'  id='addr_b_2'  value='".$db->dt[address_2]."' size=70 class='textbox'  style='width:300px'> (상세주소)
						</td>
					</tr>
					</table>
				</div>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'> <b>기본 출고지 사용 <img src='".$required3_path."'> </b></td>
				<td class='input_box_item'>
					<input type=radio class='radio' name='basic_addr_use' value='Y' id='basic_addr_use_1'  validation='false' title='기본 출고지 사용' ".CompareReturnValue("Y",$db->dt[basic_addr_use],"checked")."> <label for='basic_addr_use_1'>사용</lable>
					<input type=radio class='radio' name='basic_addr_use' value='N' id='basic_addr_use_2'  validation='false' title='기본 출고지 사용' ".($db->dt[basic_addr_use] == 'N' || $db->dt[basic_addr_use]==''?'checked':'')."> <label for='basic_addr_use_2'>미사용</lable>
				</td>
				<td class='input_box_title'> <b>코드</b></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name='code' value='".$db->dt[code]."'  validation='false' title='코드'  style='width:100px'>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'> <b>출고지 배송정책 노출여부  <img src='".$required3_path."'> </b></td>
				<td class='input_box_item' colspan='3'>
					<input type=radio class='radio' name='is_delivery_use' value='Y' id='is_delivery_use_y'  validation='true' title='출고지 배송비 사용' ".($db->dt[is_delivery_use] == 'Y' || $db->dt[is_delivery_use]==''?'checked':'')."> <label for='is_delivery_use_y'>사용</lable>
					<input type=radio class='radio' name='is_delivery_use' value='N' id='is_delivery_use_n'  validation='true' title='출고지 배송비 미사용' ".CompareReturnValue("N",$db->dt[is_delivery_use],"checked")."> <label for='is_delivery_use_n'>미사용</lable>
				</td>
			</tr>
	</table><br><br>";

	$Contents01 .= "
	<table width='100%' height='80' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
	</table><br><br>
	";

	$Contents01 .= "
	</form>";

	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
			<tr>
				<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
					<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>출고지 리스트</b>
				</td>
			</tr>
		</table>";

	$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
		<col width='5%'>
		<col width='6%'>
		<col width='7%'>
		<col width='7%'>
		<col width=8%>
		<col width=8%>
		<col width=8%>
		<col width=*>
		<col width=10%>
		<col width=10%>
		<tr bgcolor=#efefef align=center height=27>
			<td class='s_td'>번호</td>
			<td class='m_td'>코드</td>
			<td class='m_td'>주소명</td>
			<td class='m_td'>담당자명</td>
			<td class='m_td'>전화번호</td>
			<td class='m_td'>핸드폰번호</td>
			<td class='m_td'>우편번호</td>
			<td class='m_td'>상세주소</td>
			<td class='m_td'>기본주소여부</td>
			<td class='e_td'>관리</td>
			</tr>";

	if(count($delivery_array) > 0){
		for($i=0;$i<count($delivery_array); $i++){

			$Contents01 .="<tr height=32 align=center>
						<td class='list_box_td list_bg_gray'>".($i+1)."</td>
						<td class='list_box_td '>".$delivery_array[$i][code]."</td>
						<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_name]."</td>
						<td class='list_box_td point' style='padding-left:10px; text-align:left;'>".$delivery_array[$i][person_name]."</td>
						<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_phone]."</td>
						<td class='list_box_td'>".$delivery_array[$i][addr_mobile]."</td>
						<td class='list_box_td list_bg_gray'>".$delivery_array[$i][zip_code]."</td>
						<td class='list_box_td '>".$delivery_array[$i][address_1]."<br>".$delivery_array[$i][address_2]."</td>
						<td class='list_box_td list_bg_gray'>".($delivery_array[$i][basic_addr_use])."</td>
						<td class='list_box_td ' nowrap>";
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents01 .="<a href='./company.add.php?company_id=".$company_id."&addr_ix=".$delivery_array[$i][addr_ix]."&info_type=".$info_type."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
				}else{
					$Contents01 .="<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
				}

				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$Contents01 .="
					<a href=\"JavaScript:DeleteAddr('".$delivery_array[$i][addr_ix]."','".$info_type."','".$company_id."')\">
					<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}else{
					$Contents01 .="<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}
				$Contents01 .="
						</td>
					</tr>";
		}
		$Contents01 .=	"</table>";
		$Contents01 .=	"<table width='100%' cellpadding=0 cellspacing=0>";
	}else{
		$Contents01 .= "
					<tr height=50><td colspan=10 align=center style='padding-top:10px;'>등록된 촐고지 정보가 없습니다.</td></tr>";
	}
	$Contents01 .= "</table>";
	$Contents01 .= "<table>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
		$Contents01 .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
	}else{
		$Contents01 .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
	}
	$Contents01 .="</table><br>";

	}

	if($info_type == 'exchange_info'){


	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
			<tr>
				<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
					<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>교환/반품지 관리</b>
				</td>
			</tr>
		</table>";

	$Contents01 .= "
	<form name='edit_form' action='company.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='iframe_act'><!--target='iframe_act' -->
	<input name='act' type='hidden' value='$act'>
	<input name='info_type' type='hidden' value='$info_type'>
	<input name='company_id' type='hidden' value='".$company_id."'>
	<input name='delivery_type' type='hidden' value='E'>
	<input name='mall_ix' type='hidden' value='".$admininfo[mall_ix]."'>
	<input name='addr_ix' type='hidden' value='$addr_ix'>
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
			 <colgroup>
				<col width='15%' />
				<col width='35%' style='padding:0px 0px 0px 10px'/>
				<col width='15%' />
				<col width='35%' style='padding:0px 0px 0px 10px'/>
			  </colgroup>
			  <tr>
				<td class='input_box_title'> <b>교환/반품지명  <img src='".$required3_path."'> </b></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name='addr_name' value='".$db->dt[addr_name]."'  style='width:150px' validation='true' title='교환/반품지명'>
				</td>
				<td class='input_box_title'> <b>담당자명  </b></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name='person_name' value='".$db->dt[person_name]."' validation='false' title='담당자명'  style='width:150px'>
				</td>
			  </tr>

			  <tr>
				<td class='input_box_title'> <b>일반 전화번호 <img src='".$required3_path."'> </b></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name='addr_phone_1' id='addr_phone_1' value='".$addr_phone[0]."' maxlength=3 com_numeric=true style='width:20px' validation='true' title='전화'> - 
					<input type=text name='addr_phone_2' id='addr_phone_2' value='".$addr_phone[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='전화'> -
					<input type=text name='addr_phone_3' id='addr_phone_3' value='".$addr_phone[2]."' maxlength=4 style='width:30px'class='textbox' com_numeric=true validation='true' title='전화'>
				</td>
				<td class='input_box_title'> <b>핸드폰번호  <img src='".$required3_path."'> </b></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name='addr_mobile_1' id='addr_mobile_1' value='".$addr_mobile[0]."' maxlength=3 com_numeric=true style='width:20px' validation='true' title='핸드폰'> - 
					<input type=text name='addr_mobile_2' id='addr_mobile_2' value='".$addr_mobile[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='핸드폰'> -
					<input type=text name='addr_mobile_3' id='addr_mobile_3' value='".$addr_mobile[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='핸드폰'>
				</td>
			  </tr>
			<tr>
			<td class='input_box_title'> <b> 교환/반품 주소 <img src='".$required3_path."'> </b>    </td>
			<td class='input_box_item' colspan=3>
				<div id='input_address_area' >
				<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
					<col width='70px'>
					<col width='*'>
					<tr>
						<td height=26>
							<input type=text class='textbox' name='com_zip' id='zip_b_1' value='".$db->dt[zip_code]."' style='width:60px' validation='true' title='우편코드' readonly>
						</td>
						<td style='padding:1px 0 0 5px;'>
							<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='com_addr1'  id='addr_b_1' value='".$db->dt[address_1]."' size=50 class='textbox'  style='width:300px'  readonly>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='com_addr2'  id='addr_b_2'  value='".$db->dt[address_2]."' size=70 class='textbox'  style='width:300px'> (상세주소)
						</td>
					</tr>
					</table>
				</div>
				</td>
		  </tr>
		  <tr>
				<td class='input_box_title'> <b>기본 교환/반품지 사용  <img src='".$required3_path."'> </b></td>
				<td class='input_box_item'>
					<input type=radio class='radio' name='basic_addr_use' value='Y' id='basic_addr_use_1'  validation='false' title='기본 교환/반품지 사용' ".CompareReturnValue("Y",$db->dt[basic_addr_use],"checked")."> <label for='basic_addr_use_1'>사용</lable>
					<input type=radio class='radio' name='basic_addr_use' value='N' id='basic_addr_use_2'  validation='false' title='기본 교환/반품지 사용' ".($db->dt[basic_addr_use] == 'N' || $db->dt[basic_addr_use] ==''?'checked':'')."> <label for='basic_addr_use_2'>미사용</lable>
				</td>
				<td class='input_box_title'> <b>코드</b></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name='code' value='".$db->dt[code]."'  validation='false' title='코드'  style='width:100px'>
				</td>
			  </tr>
	</table><br><br>";

	$Contents01 .= "
	<table width='100%' height='80' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
	</table><br><br>
	";

	$Contents01 .= "
	</form>";

	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
			<tr>
				<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
					<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>교환/반품지 리스트</b>
				</td>
			</tr>
		</table>";

	$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
		<col width='5%'>
		<col width='6%'>
		<col width='7%'>
		<col width='7%'>
		<col width=8%>
		<col width=8%>
		<col width=8%>
		<col width=*>
		<col width=10%>
		<col width=10%>
		<tr bgcolor=#efefef align=center height=27>
			<td class='s_td'>번호</td>
			<td class='m_td'>코드</td>
			<td class='m_td'>주소명</td>
			<td class='m_td'>담당자명</td>
			<td class='m_td'>전화번호</td>
			<td class='m_td'>핸드폰번호</td>
			<td class='m_td'>우편번호</td>
			<td class='m_td'>상세주소</td>
			<td class='m_td'>기본주소여부</td>
			<td class='e_td'>관리</td>
			</tr>";

	if(count($delivery_array) > 0){
		for($i=0;$i<count($delivery_array); $i++){

			$Contents01 .="<tr height=32 align=center>
						<td class='list_box_td list_bg_gray'>".($i+1)."</td>
						<td class='list_box_td '>".$delivery_array[$i][code]."</td>
						<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_name]."</td>
						<td class='list_box_td point' style='padding-left:10px; text-align:left;'>".$delivery_array[$i][person_name]."</td>
						<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_phone]."</td>
						<td class='list_box_td'>".$delivery_array[$i][addr_mobile]."</td>
						<td class='list_box_td list_bg_gray'>".$delivery_array[$i][zip_code]."</td>
						<td class='list_box_td '>".$delivery_array[$i][address_1]."<br>".$delivery_array[$i][address_2]."</td>
						<td class='list_box_td list_bg_gray'>".($delivery_array[$i][basic_addr_use])."</td>
						<td class='list_box_td ' nowrap>";
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents01 .="<a href='./company.add.php?company_id=".$company_id."&addr_ix=".$delivery_array[$i][addr_ix]."&info_type=".$info_type."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
				}else{
					$Contents01 .="<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
				}

				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$Contents01 .="
					<a href=\"JavaScript:DeleteAddr('".$delivery_array[$i][addr_ix]."','".$info_type."','".$company_id."')\">
					<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}else{
					$Contents01 .="<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}
				$Contents01 .="
						</td>
					</tr>";
		}
		$Contents01 .=	"</table>";
		$Contents01 .=	"<table width='100%' cellpadding=0 cellspacing=0>";
	}else{
		$Contents01 .= "
					<tr height=50><td colspan=10 align=center style='padding-top:10px;'>등록된 촐고지 정보가 없습니다.</td></tr>";
	}
	$Contents01 .= "</table>";
	$Contents01 .= "<table>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
		$Contents01 .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
	}else{
		$Contents01 .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
	}
	$Contents01 .="</table><br>";
	}

	if($info_type == 'visit_info'){

	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
			<tr>
				<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
					<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>방문수령지 관리</b>
				</td>
			</tr>
		</table>";

	$Contents01 .= "
	<form name='edit_form' action='company.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='iframe_act'><!--target='iframe_act' -->
	<input name='act' type='hidden' value='$act'>
	<input name='info_type' type='hidden' value='$info_type'>
	<input name='company_id' type='hidden' value='".$company_id."'>
	<input name='delivery_type' type='hidden' value='V'>
	<input name='mall_ix' type='hidden' value='".$admininfo[mall_ix]."'>
	<input name='addr_ix' type='hidden' value='$addr_ix'>
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
			 <colgroup>
				<col width='15%' />
				<col width='35%' style='padding:0px 0px 0px 10px'/>
				<col width='15%' />
				<col width='35%' style='padding:0px 0px 0px 10px'/>
			  </colgroup>
			  <tr>
				<td class='input_box_title'> <b>방문수령지명  <img src='".$required3_path."'> </b></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name='addr_name' value='".$db->dt[addr_name]."'  style='width:150px' validation='true' title='방문수령지명'>
				</td>
				<td class='input_box_title'> <b>담당자명  <img src='".$required3_path."'> </b></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name='person_name' value='".$db->dt[person_name]."' validation='true' title='담당자명'  style='width:150px'>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'> <b>일반 전화번호  <img src='".$required3_path."'> </b></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name='addr_phone_1' id='addr_phone_1' value='".$addr_phone[0]."' maxlength=3 com_numeric=true style='width:20px' validation='true' title='전화'> - 
					<input type=text name='addr_phone_2' id='addr_phone_2' value='".$addr_phone[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='전화'> -
					<input type=text name='addr_phone_3' id='addr_phone_3' value='".$addr_phone[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='전화'>
				</td>
				<td class='input_box_title'> <b>핸드폰번호  <img src='".$required3_path."'> </b></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name='addr_mobile_1' id='addr_mobile_1' value='".$addr_mobile[0]."' maxlength=3 com_numeric=true style='width:20px' validation='true' title='핸드폰'> - 
					<input type=text name='addr_mobile_2' id='addr_mobile_2' value='".$addr_mobile[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='핸드폰'> -
					<input type=text name='addr_mobile_3' id='addr_mobile_3' value='".$addr_mobile[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='핸드폰'>
				</td>
			</tr>
			<tr>
			<td class='input_box_title'> <b>방문수령지 주소 <img src='".$required3_path."'> </b></td>
			<td class='input_box_item' colspan=3>
				<div id='input_address_area' >
				<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
					<col width='70px'>
					<col width='*'>
					<tr>
						<td height=26>
							<input type=text class='textbox' name='com_zip' id='zip_b_1' value='".$db->dt[zip_code]."'  style='width:60px' validation='true' title='우편코드' readonly>
						</td>
						<td style='padding:1px 0 0 5px;'>
							<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='com_addr1'  id='addr_b_1' value='".$db->dt[address_1]."' size=50 class='textbox'  style='width:300px' readonly>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='com_addr2'  id='addr_b_2'  value='".$db->dt[address_2]."' size=70 class='textbox'  style='width:300px'> (상세주소)
						</td>
					</tr>
					</table>
				</div>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'> <b>기본 출고지 사용  <img src='".$required3_path."'> </b></td>
				<td class='input_box_item'>
					<input type=radio class='radio' name='basic_addr_use' value='Y' id='basic_addr_use_1'  validation='false' title='기본 출고지 사용' ".CompareReturnValue("Y",$db->dt[basic_addr_use],"checked")."> <label for='basic_addr_use_1'>사용</lable>
					<input type=radio class='radio' name='basic_addr_use' value='N' id='basic_addr_use_2'  validation='false' title='기본 출고지 사용' ".($db->dt[basic_addr_use]=='N' || $db->dt[basic_addr_use] ==''?'checked':'')."> <label for='basic_addr_use_2'>미사용</lable>
				</td>
				<td class='input_box_title'> <b>코드</b></td>
				<td class='input_box_item'>
					<input type=text class='textbox' name='code' value='".$db->dt[code]."'  validation='false' title='코드'  style='width:100px'>
				</td>
			</tr>
	</table><br><br>";

	$Contents01 .= "
	<table width='100%' height='80' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
	</table><br><br>
	";

	$Contents01 .= "
	</form>";

	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
			<tr>
				<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
					<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>방문수령지 리스트</b>
				</td>
			</tr>
		</table>";

	$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
		<col width='5%'>
		<col width='6%'>
		<col width='7%'>
		<col width='7%'>
		<col width=8%>
		<col width=8%>
		<col width=8%>
		<col width=*>
		<col width=10%>
		<col width=10%>
		<tr bgcolor=#efefef align=center height=27>
			<td class='s_td'>번호</td>
			<td class='m_td'>코드</td>
			<td class='m_td'>주소명</td>
			<td class='m_td'>담당자명</td>
			<td class='m_td'>전화번호</td>
			<td class='m_td'>핸드폰번호</td>
			<td class='m_td'>우편번호</td>
			<td class='m_td'>상세주소</td>
			<td class='m_td'>기본주소여부</td>
			<td class='e_td'>관리</td>
			</tr>";

	if(count($delivery_array) > 0){
		for($i=0;$i<count($delivery_array); $i++){

			$Contents01 .="<tr height=32 align=center>
						<td class='list_box_td list_bg_gray'>".($i+1)."</td>
						<td class='list_box_td '>".$delivery_array[$i][code]."</td>
						<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_name]."</td>
						<td class='list_box_td point' style='padding-left:10px; text-align:left;'>".$delivery_array[$i][person_name]."</td>
						<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_phone]."</td>
						<td class='list_box_td'>".$delivery_array[$i][addr_mobile]."</td>
						<td class='list_box_td list_bg_gray'>".$delivery_array[$i][zip_code]."</td>
						<td class='list_box_td '>".$delivery_array[$i][address_1]."<br>".$delivery_array[$i][address_2]."</td>
						<td class='list_box_td list_bg_gray'>".($delivery_array[$i][basic_addr_use])."</td>
						<td class='list_box_td ' nowrap>";
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents01 .="<a href='./company.add.php?company_id=".$company_id."&addr_ix=".$delivery_array[$i][addr_ix]."&info_type=".$info_type."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
				}else{
					$Contents01 .="<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
				}

				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$Contents01 .="
					<a href=\"JavaScript:DeleteAddr('".$delivery_array[$i][addr_ix]."','".$info_type."','".$company_id."')\">
					<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}else{
					$Contents01 .="<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}
				$Contents01 .="
						</td>
					</tr>";
		}
		$Contents01 .=	"</table>";
		$Contents01 .=	"<table width='100%' cellpadding=0 cellspacing=0>";
	}else{
		$Contents01 .= "
					<tr height=50><td colspan=10 align=center style='padding-top:10px;'>등록된 촐고지 정보가 없습니다.</td></tr>";
	}
		$Contents01 .= "</table>";
		$Contents01 .= "<table>";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			$Contents .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
		}else{
			$Contents01 .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
		}
		$Contents01 .="</table><br>";
	}



	if($info_type == 'seller_setup'){

		$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
			<tr>
				<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
					<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>셀러 정산 설정</b>
				</td>
			</tr>
		</table>";

		$Contents01 .= "
		<input name='act' type='hidden' value='".$act."'>
		<input name='seller_minishop_use' type='hidden' value='2'>
	<!--<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='input_table_box'>
		<col width='15%' />
		<col width='85%' />
		
			<tr bgcolor=#ffffff>
				<td class='input_box_title'> <b>프론트 전시 구분 <img src='".$required3_path."'></b>   </td>
				<td class='input_box_item'>
					".GetDisplayDivision($mall_ix, "select")." <span class=small>전시 구분을 선택하실경우 해당 사이트에 노출됩니다.</span>
				</td>
			</tr>
			<tr bgcolor=#ffffff>
				<td class='input_box_title'> <b>셀러 사용여부 <img src='".$required3_path."'></b>   </td>
				<td class='input_box_item'>
					<input type=radio name='seller_use_info' value='1' id='seller_use_info_1'  ".($seller_use_info == "1" || $seller_use_info == "" ? 'checked':'')."><label for='seller_use_info_1'> 사용(수동승인)</label>&nbsp;
					<input type=radio name='seller_use_info' value='2' id='seller_use_info_2'  ".CompareReturnValue("2",$seller_use_info,"checked")."><label for='seller_use_info_2'> 사용(자동승인)</label>&nbsp;
					<input type=radio name='seller_use_info' value='3' id='seller_use_info_3'  ".CompareReturnValue("3",$seller_use_info,"checked")."><label for='seller_use_info_3'> 미사용</label>&nbsp;
				</td>
			</tr>
			<tr bgcolor=#ffffff>
				<td class='input_box_title'> <b>셀러 미니샵 사용여부 <img src='".$required3_path."'></b>   </td>
				<td class='input_box_item'>
					<input type=radio name='seller_minishop_use' value='1' id='seller_minishop_use_1'  ".CompareReturnValue("1",$seller_minishop_use,"checked")." checked><label for='seller_minishop_use_1'> 사용(수동승인)</label>&nbsp;
					<input type=radio name='seller_minishop_use' value='2' id='seller_minishop_use_2'  ".CompareReturnValue("2",$seller_minishop_use,"checked")."><label for='seller_minishop_use_2'> 사용(자동승인)</label>&nbsp;
					<input type=radio name='seller_minishop_use' value='3' id='seller_minishop_use_3'  ".CompareReturnValue("3",$seller_minishop_use,"checked")."><label for='seller_minishop_use_3'> 미사용</label>&nbsp;
				</td>
			</tr>
		</table>
		
		<table width='100%' cellpadding=0 cellspacing=0 border='0'  style='padding-top:20px'>
		<col width='15%' />
		<col width='85%' />
		</table>-->

		<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='input_table_box'>
		<col width='18%' />
		<col width='32%' />
		<col width='18%' />
		<col width='32%' />
		<tr bgcolor=#ffffff height=80>
			<td class='input_box_title'> <b>정산 방식 <img src='".$required3_path."'></b>   </td>
			<td class='input_box_item' colspan='3'>
				<table border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td>
							<input type='radio' name='account_type' id='account_type_1' value='1' ".($account_type == "1" || $account_type == "" ? 'checked':'')."> <label for='account_type_1'>판매가 정산방식( 판매가에 수수료 적용 후 정산처리됩니다.)</label>
						</td>
					</tr>
					
					<tr>
						<td>
							<input type='radio' name='account_type' id='account_type_2' value='2' ".CompareReturnValue('2',$account_type,"checked")."> <label for='account_type_2'>매입가 정산방식 ( 공급가로 정산되며, 하단 수수료에 0 이 아닌 숫자를 입력시 그 숫자의 % 만큼 차감 후 정산 처리됩니다.)</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type='radio' name='account_type' id='account_type_3' value='3' ".CompareReturnValue('3',$account_type,"checked")."> <label for='account_type_3'>미정산 ( 선 매입으로 본사에 재고가 있으며, 상품등록을 셀러가 진행시에 사용되며, 정산에서 제외됩니다.)</label>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr bgcolor=#ffffff>
			<td class='input_box_title'> <b>정산예정내역 <img src='".$required3_path."'></b></td>
			<td class='input_box_item' colspan='3'>
				<input type='hidden' name='account_info' value='1' title='기본설정(기간별) 기간설정'>
				상품별 주문처리상태가
              <select name='ac_delivery_type' id='ac_delivery_type_1' style='width:80px;'>
                <option value='0'> 선택 </opion>
                <option value='".ORDER_STATUS_INCOM_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_COMPLETE,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
                <option value='".ORDER_STATUS_DELIVERY_ING."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_ING,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option>
                <option value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_COMPLETE,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</option>
                <option value='".ORDER_STATUS_BUY_FINALIZED."' ".CompareReturnValue(ORDER_STATUS_BUY_FINALIZED,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_BUY_FINALIZED)."</option>
               </select> 
               으로 변경된 일로부터
                <select name='ac_expect_date' id='ac_expect_date_1' style='width:45px;'>
                    <option value='0'> 선택 </opion>
                    ";
                    for($i=0; $i<=31; $i++){
                        $Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$ac_expect_date,"selected").">".$i." </option>";
                    }

                    $Contents01 .= "
                 </select> 일 후
				<!--
				<table width='100%' cellpadding=0 cellspacing=0 border='0' >
				<col width='20%' />
				<col width='*' />
				<tr bgcolor=#ffffff>
					<td class='input_box_item'>
						<input type='radio' name='account_info' value='2' id='account_info_2' ".CompareReturnValue('2',$account_info,"checked")."> <label for='account_info_2'> 상품별(건별)정산</label>
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='input_box_item' style='padding-left:20px;'>
						배송 처리상태 <select name='ac_delivery_type' id='ac_delivery_type_2' style='width:80px;'>
						<option value='".ORDER_STATUS_INCOM_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_COMPLETE,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
						<option value='".ORDER_STATUS_DELIVERY_ING."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_ING,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option>
						<option value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_COMPLETE,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</option>
						<option value='".ORDER_STATUS_BUY_FINALIZED."' ".CompareReturnValue(ORDER_STATUS_BUY_FINALIZED,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_BUY_FINALIZED)."</option>
						</select> 
						상태 변경후 <select name='ac_expect_date' id='ac_expect_date_2' style='width:45px;'>
						<option value='0'> 선택 </opion>
						";
						for($i=0; $i<=31; $i++){
							$Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$ac_expect_date,"selected").">".$i." </option>";
						}
						
				$Contents01 .= "
						</select> 일 후 정산신청 처리됩니다.
					</td>
				</tr>
				</table>
				-->
			</td>
		</tr>

		<tr bgcolor=#ffffff height=80>
			<td class='input_box_title'> <b>정산확정내역 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item'  colspan='3' style='line-height:150%;'>
				<select name='ac_term_div' id='ac_term_div'  style='width:80px;'>
				<option value='0'> 선택 </opion>
				<option value='1' ".CompareReturnValue('1',$ac_term_div,"selected").">월 1 회</option>
				<option value='2' ".CompareReturnValue('2',$ac_term_div,"selected").">월 2 회</option>
				<option value='3' ".CompareReturnValue('3',$ac_term_div,"selected").">매주 1 회</option>
				</select>
				<select name='ac_term_date1' id='ac_term_date1' style='width:55px;'>
				<option value='0'> 선택 </opion>
				";
				for($i=1; $i<=31; $i++){
					$Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$ac_term_date1,"selected").">".$i." 일 </option>";
				}
				
		$Contents01 .= "
				</select> 
				<select name='ac_term_date2' id='ac_term_date2' style='width:55px;'>
				<option value='0'> 선택 </opion>
				";
				for($i=1; $i<=31; $i++){
					$Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$ac_term_date2,"selected").">".$i." 일 </option>";
				}
				
		$Contents01 .= "
				</select>
				<select name='ac_term_date1' id='ac_term_date1_week' style='width:70px;display:none;'>
					<option value='0' ".CompareReturnValue('0',$ac_term_date1,"selected")."> 일요일 </opion>
					<option value='1' ".CompareReturnValue('1',$ac_term_date1,"selected")."> 월요일 </opion>
					<option value='2' ".CompareReturnValue('2',$ac_term_date1,"selected")."> 화요일 </opion>
					<option value='3' ".CompareReturnValue('3',$ac_term_date1,"selected")."> 수요일 </opion>
					<option value='4' ".CompareReturnValue('4',$ac_term_date1,"selected")."> 목요일 </opion>
					<option value='5' ".CompareReturnValue('5',$ac_term_date1,"selected")."> 금요일 </opion>
					<option value='6' ".CompareReturnValue('6',$ac_term_date1,"selected")."> 토요일 </opion>
				</select> 정산 확정
				<div class='small blu' style='margin-top:4px;padding-left:0px'>※ 매월 설정된 일자의 전일까지 누적된 정산예정내역의 주문 건이 정산확정 내역으로 넘어갑니다.</div>
				<div class='small blu' style='margin-top:4px;padding-left:0px'>※ 정산확정내역에서 운영사와 입점사 상호 협의 하여 정산금액을 최종 조정한 후, 운영사가 송금대기 처리를 합니다.</div>
			</td>
		</tr>
		<tr bgcolor=#ffffff>
			<td class='input_box_title'> <b>정산 지급방식 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item' colspan='3'>
			<input type='radio' id='account_method_cash' name='account_method' value='".ORDER_METHOD_CASH."' ".CompareReturnValue(ORDER_METHOD_CASH,$account_method,"checked")." checked><label for='account_method_cash'> 현금</label>
			<!--
			<input type='radio' id='account_method_service' name='account_method' value='".ORDER_METHOD_SAVEPRICE."' ".CompareReturnValue(ORDER_METHOD_SAVEPRICE,$account_method,"checked")."><label for='account_method_service'> 예치금</label>
			-->
			</td>
		</tr>
		<tr bgcolor=#ffffff >
			<td class='input_box_title'> <b>정산 유형  <img src='".$required3_path."'></b> </td>
			<td class='input_box_item' colspan='3'>
				<div id='account_div' style='float:left;width:230px;position:relative;top:2px;'>
					<input type='radio' id='account_div_c' name='account_div' value='c' ".CompareReturnValue('c',$account_div,"checked")."><label for='account_div_c'> 카테고리별 설정</label>
					<input type='radio' id='account_div_s' name='account_div' value='s' ".($account_div == 's' || $account_div == ""?'checked':'')."><label for='account_div_s'> 셀러별 설정</label>
				</div>
				<div id='account_div_table' style='float:left;'>
					소매 수수료 : 
					<input type='text' id='commission' name='commission' style='width:30px; text-align:center;' value='".$commission."' maxlength='2'><label for='commission'> %</label>&nbsp;&nbsp;&nbsp;
					도매 수수료 : 
					<input type='text' id='wholesale_commission' name='wholesale_commission' style='width:30px; text-align:center;' value='".$wholesale_commission."'  maxlength='2'><label for='wholesale_commission'> %</label>
				</div>
			</td>
		</tr>
		<tr bgcolor=#ffffff  style='display:none'>
			<td class='input_box_title'> <b>전자계약 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item' colspan='3'>
			전자계약 선택 
			".getContractGroup($contract_group, "onchange=\"loadContract($(this), 'et_ix')\"")."
			".getContract($contract_group, $et_ix,"   ")."
			&nbsp;&nbsp;&nbsp;
			계약서내 수수료율 &nbsp;&nbsp;
			<input type='text' class='textbox numeric' name='electron_contract_commission' style='width:40px;' value='".$econtract_commission."'> %
			</td>
		</tr>
		<tr bgcolor=#ffffff style='display:none'>
			<td class='input_box_title'> <b>대리판매 수수료 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item' colspan='3'>
				<input type='text' name='substitude_rate' value='".$substitude_rate."' maxlength=5 onkeyup='substitudeCheck(this);' class='textbox numeric' size=5 /> %&nbsp;&nbsp;&nbsp;<span class='small blu'>* 상품 상세페이지에서 상품별 대리판매 수수료율 설정 가능합니다.</span>
			</td>
		</tr>
		</table>

		<table width='100%' cellpadding=0 cellspacing=0 border='0'  style='padding-top:20px'>
		<col width='15%' />
		<col width='85%' />
		<!--  <tr>
			<td align='left' colspan=2 style='padding:3px 0px'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=middle><b> 배송비 부가 정책</b></div>")."</td>
		  </tr>-->
		</table>";

	$Contents011 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
			<tr>
				<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
					<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>셀러 판매장려금</b>
				</td>
			</tr>
		</table>";
	$Contents011 .= "
		<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='input_table_box'>
		<col width='15%'/>
		<col width='85'/>
		<tr bgcolor=#ffffff >
			<td class='input_box_title'> <b>추가 판매장려금 <img src='".$required3_path."'></b></td>
			<td class='input_box_item' style='padding:10px;'>
				<table cellpadding=0 cellspacing=0 width='100%'>
					<tr>
						<td class='input_box_item'>
							<input type='radio' id='seller_grant_use_1' name='seller_grant_use' value='1' ".CompareReturnValue("1",$seller_grant_use,"checked")." checked><label for='seller_grant_use_1'> 사용</label>
							<input type='radio' id='seller_grant_use_0' name='seller_grant_use' value='0' ".CompareReturnValue("0",$seller_grant_use,"checked")."><label for='seller_grant_use_0'> 미사용</label>
						</td>
					</tr>
					<tr>
						<td class='input_box_item'>
						매출액 
						<input type='text' id='grant_setup_price' name='grant_setup_price' value='".$grant_setup_price."' style='width:60px' dir='rtl'> 원 이상일 경우 정산시 <input type='text' id='ac_grant_price' name='ac_grant_price' value='".$ac_grant_price."' style='width:50px' dir='rtl'> 원 추가 수수료 정산에서 합니다. (VAT 포함)<br>

						* 매출액 목표가 달성시 매 달성 금액 회수만큼 추가 정산 합니다.<br>
						* 정산 기준으로 매월 31일 까지의 매출을 통계하여 측정됩니다.
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>";
	}

	if($info_type == 'tax_info'){


	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 >
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>세금계산서 및 통장관리</b>
			</td>
		</tr>
		</table>";

	$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
		<colgroup>
			<col width='15%' />
			<col width='85%' style='padding:0px 0px 0px 10px'/>
		
		</colgroup>
		<tr>
			<td class='input_box_title'> <b>세금계산서이메일 <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
			<input type=text name='tax_mail' value='".$db->dt[tax_mail]."' class='textbox'  style='width:200px' validation='true' title='세금계산서이메일'>
			</td>

		</tr>
		<tr>
			<td class='input_box_title'> <b>거래처 은행정보 <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
			<select name='basic_bank' style='width:200px;'>
				<option value='' ".($db->dt[basic_bank] == "" ? "selected":"").">은행선택</option>";

				foreach($arr_banks_name as $bank_key => $bank_name){
					$Contents01 .= "<option value='".$bank_key."' ".($db->dt[basic_bank] == $bank_key ? "selected":"").">".$bank_name."</option>";
				}
				$Contents01 .= "
				</select>
				&nbsp;&nbsp;&nbsp;
			예금주 &nbsp;&nbsp;<input type=text name='holder_name' value='".$db->dt[holder_name]."' class='textbox'  style='width:200px' validation='true' title='예금주'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			계좌번호 &nbsp;&nbsp;<input type=text name='bank_num' value='".$db->dt[bank_num]."' class='textbox'  style='width:200px' validation='true' title='계좌번호'>
			</td>
		</tr>
		</table>";

	}

	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){

		if($info_type !="factory_info" && $info_type !="exchange_info" && $info_type !="visit_info" && $info_type !="delivery_info"){
			$ButtonString = "
			<table cellpadding=0 cellspacing=0 border='0' height='100'>
				<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
			</table>
			";
		}
	}

	if($company_id != "" && $info_type == "delivery_info"){
	$ButtonString .= "<script type='text/javascript'>
		window.onload = function(){
			//deliveryTypeView(".$db->dt[delivery_policy].");
			//Content_Input();
			//Init(document.edit_form);
		}
		function Content_Input(){
			document.edit_form.content.value = document.edit_form.delivery_policy_text.value;
		}
		function SubmitX(frm){
			if(!CheckFormValue(frm)){
				return false;
			}
			frm.content.value = iView.document.body.innerHTML;
			frm.content.value = frm.content.value.replace('<P>&nbsp;</P>','')
			//alert(frm.content.value);
			return true;
		}
	</script>
	";
	}


	$Contents = "<form name='edit_form' action='company.act.php' method='post' onsubmit='return ".($act=="seller_insert" ? "seller_submit(this)" : "CheckFormValue(this)")."' enctype='multipart/form-data' style='display:inline;' target='act'>";
	$Contents = $Contents."<table width='100%' border=0>";
	$Contents = $Contents."
	<input name='act' type='hidden' value='$act'>
	<input name='mmode' type='hidden' value='$mmode'>
	<input name='info_type' type='hidden' value='$info_type'>
	<input name='company_id' type='hidden' value='$company_id'>
	";
	//$Contents = $Contents."<tr ><img src='../funny/image/title_basicinfo.gif'><td></td></tr>";
	$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
	$Contents = $Contents."<tr><td>".$Contents04."</td></tr>";
	$Contents = $Contents."<tr><td align=center>".$ButtonString."</td></tr>";
	$Contents = $Contents."</table></form><br><br>";

	$Script = "<script language='javascript' src='./company.add.js'></script>
	<script Language='javascript' src='design.js'></script>
	<script language='javascript'>
	
	function seller_submit(frm){
	    if(!CheckFormValue(frm)){
	        return false;
	    }
	   
	    if( check_com_num( $('#com_number_1').val() + $('#com_number_2').val() + $('#com_number_3').val() ) ){
	        return true;
	    }else{
	        alert('올바른 사업자 번호를 입력해주세요.');
	        return false;
	    }
	}
	
	function check_com_num(bizID)  //사업자등록번호 체크 
    {
        // bizID는 숫자만 10자리로 해서 문자열로 넘긴다. 
        var checkID = new Array(1, 3, 7, 1, 3, 7, 1, 3, 5, 1); 
        var tmpBizID, i, chkSum=0, c2, remander; 
        bizID = bizID.replace(/-/gi,''); 
    
        for (i=0; i<=7; i++) chkSum += checkID[i] * bizID.charAt(i); 
        c2 = '0' + (checkID[8] * bizID.charAt(8)); 
        c2 = c2.substring(c2.length - 2, c2.length); 
        chkSum += Math.floor(c2.charAt(0)) + Math.floor(c2.charAt(1)); 
        remander = (10 - (chkSum % 10)) % 10 ; 
    
        if (Math.floor(bizID.charAt(9)) == remander) return true ; // OK! 
            return false; 
    }
    
	function zipcode(type) {
		var zip = window.open('../member/zipcode.php?zip_type='+type,'','width=440,height=300,scrollbars=yes,status=no');
	}

	function ChangeAddress(obj){
		if(obj.checked){
			document.getElementById('input_address_area').style.display = '';
		}else{
			document.getElementById('input_address_area').style.display = 'none';
		}
	}

	function loadRegion(sel,target) {

		var trigger = sel.options[sel.selectedIndex].value;
		//alert(sel.form.name);
		var form = sel.form.name;

		var depth = sel.getAttribute('depth');

		window.frames['act'].location.href = '../store/region.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

	}

	function loadBranch(sel,target) {

		var trigger = sel.options[sel.selectedIndex].value;
		//alert(sel.form.name);
		var form = sel.form.name;

		var depth = sel.getAttribute('depth');

		window.frames['act'].location.href = '../store/branch.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

	}

	function loadTeam(sel,target) {

		var trigger = sel.options[sel.selectedIndex].value;
		//alert(sel.form.name);
		var form = sel.form.name;

		var depth = sel.getAttribute('depth');

		window.frames['act'].location.href = '../store/team.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

	}

	function loadSellerManager(sel,target) {

		var trigger = sel.options[sel.selectedIndex].value;
		//alert(sel.form.name);
		var form = sel.form.name;

		var depth = sel.getAttribute('depth');

		window.frames['act'].location.href = '../store/sellermanager.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

	}

	function same_com_addr() {
		var fm=document.edit_form;
		fm.return_zip1.value=fm.com_zip.value.substring(0,3);
		fm.return_zip2.value=fm.com_zip.value.substring(4,7);
		fm.return_addr1.value=fm.com_addr1.value;
		fm.return_addr2.value=fm.com_addr2.value;
	}

	$(function() {
		var edit_form=document.edit_form;
		$('#user_id').keyup(function(){
			var PT_idtype =/^[a-zA-Z]{1}[a-zA-Z0-9_]+$/;
			//var PT_idtype =/^[a-z0-9_-]{4,12}$/;

			if(edit_form.user_id.value.length < 4 || edit_form.user_id.value.length > 16 ){

				var alert_text='* 아이디는 4~16자리의 영문, 숫자와 특수기호(_)만 사용하실 수 있습니다.';//개정법안 수정 kbk 13/03/12
				$('#idCheckText').css('color','#FF5A00').html(alert_text);//개정법안 수정 kbk 13/03/12
				edit_form.user_id.focus();
				return false;
			}
			if(edit_form.id.value != ''){
				if(!PT_idtype.test(edit_form.id.value)){
					
					var alert_text='* 아이디는 4~16자리의 영문, 숫자와 특수기호(_)만 사용하실 수 있습니다.';//개정법안 수정 kbk 13/03/12
					
					$('#idCheckText').css('color','#FF5A00').html(alert_text);//개정법안 수정 kbk 13/03/12
					edit_form.user_id.focus();
					return false;
				}
				$.ajax({
					url: '../basic/join_input.act.php',
					type: 'get',
					dataType: 'html',
					data: ({act: 'idcheck',
							id: $('#user_id').val()
					}),
					success: function(result){
					//alert(result);
						//alert(edit_form.id.value);
						if(result == 'Y'){
							 var alert_text='* 사용가능한 아이디 입니다.';//개정법안 수정 kbk 13/03/12
							$('#idCheckText').css('color','#00B050').html(alert_text);//개정법안 수정 kbk 13/03/12
							//$('#id_flag').val('Y');
						//	$('#id_check_value').val(edit_form.id.value);//kbk
							$('#user_id').attr('dup_check','true');//kbk
						}else if(result == 'X'){
							var alert_text='* 가입불가 ID입니다. 다른 ID로 입력해주시기 바랍니다.';//개정법안 수정 kbk 13/03/12
							$('#idCheckText').css('color','#FF5A00').html(alert_text);//개정법안 수정 kbk 13/03/12
						//	$('#id_flag').val('');
						//	$('#id_check_value').val('');//kbk
							$('#user_id').attr('dup_check','false');//kbk
							return false;
						}else if(result=='N'){
							var alert_text='* 사용할수 없는 아이디 입니다.';//개정법안 수정 kbk 13/03/12
							$('#idCheckText').css('color','#FF5A00').html(alert_text);//개정법안 수정 kbk 13/03/12
						//	$('#id_flag').val('');
						//	$('#id_check_value').val('');//kbk
							$('#user_id').attr('dup_check','false');//kbk
							return false;
						} else {
							//alert(result);
							return false;
						}
					}

				});
			}else{
				edit_form.user_id.focus();
				var alert_text='* 아이디가 비어있습니다.';//개정법안 수정 kbk 13/03/12
				$('#idCheckText').html(alert_text);//개정법안 수정 kbk 13/03/12
				return false;
			}
		});

		$('#check_all').click(function(){

			var value = $('#check_all').attr('checked');
			if(value == 'checked'){
				$('input[name=tax_person_name]').val($('input[name=customer_name]').val());
				$('input[name=tax_person_phone]').val($('input[name=customer_phone]').val());
				$('input[name=tax_person_mobile]').val($('input[name=customer_mobile]').val());
				$('input[name=tax_person_mail]').val($('input[name=customer_mail]').val());
				$('input[name=tax_person_position]').val($('input[name=customer_position]').val());
				$('textarea[name=tax_seller_message]').val($('textarea[name=seller_message]').val());
			
			}else{
				$('input[name=tax_person_name]').val('');
				$('input[name=tax_person_phone]').val('');
				$('input[name=tax_person_mobile]').val('');
				$('input[name=tax_person_mail]').val('');
				$('input[name=tax_person_position]').val('');
				$('textarea[name=tax_seller_message]').val('');
			}
		
		});
	}
	);


	$(document).ready(function (){

			$('input[name=account_type]').click(function (){
			var value = $(this).val();
			if(value == '2'){
				
				$('#wholesale_commission').val('0');
				$('#commission').val('0');
				$('#wholesale_commission').attr('readonly',true);
				$('#commission').attr('readonly',true);

			}else{
				$('#wholesale_commission').attr('readonly',false);
				$('#commission').attr('readonly',false);
			}
		});

		if($('input[name=account_type]:checked').val() == '2'){

			$('#wholesale_commission').val('0');
			$('#commission').val('0');
			$('#wholesale_commission').attr('readonly',true);
			$('#commission').attr('readonly',true);
		}else{
			$('#wholesale_commission').attr('readonly',false);
			$('#commission').attr('readonly',false);
		}

		if($('input[name=commercial_disp]:checked').val()=='Y'){
			$('.wholesale_validation').each(function(){
				$(this).find('select,input').each(function(){
					$(this).attr('validation','true');
				})
			})
		}else{
			$('.wholesale_validation').each(function(){
				$(this).find('select,input').each(function(){
					$(this).attr('validation','false');
				})
			})
		}

		$('input[name=commercial_disp]').click(function(){
			if($(this).val()=='Y'){
				$('.wholesale_tr').show();
				$('.wholesale_validation').each(function(){
					$(this).find('select,input').each(function(){
						$(this).attr('validation','true');
					})
				})
				
			}else{
				$('.wholesale_tr').hide();
				$('.wholesale_validation').each(function(){
					$(this).find('select,input').each(function(){
						$(this).attr('validation','false');
					})
				})
			}
		})

		if($('input[name=account_info]:checked').val() == '1' || true){

			$('#ac_delivery_type_2').attr('disabled',true);
			$('#ac_expect_date_2').attr('disabled',true);
			
			$('#ac_delivery_type_1').attr('disabled',false);
			$('#ac_expect_date_1').attr('disabled',false);

			//$('#ac_delivery_type_2').val('0');
			//$('#ac_expect_date_2').val('0');

		}else{

			$('#ac_delivery_type_1').attr('disabled',true);
			$('#ac_expect_date_1').attr('disabled',true);

			$('#ac_delivery_type_2').attr('disabled',false);
			$('#ac_expect_date_2').attr('disabled',false);

			//$('#ac_delivery_type_1').val('0');
			//$('#ac_expect_date_1').val('0');
		}

		$('input[name=account_info]').click(function (){
		
			var value = $(this).val();

			if(value == '1'){

				$('#ac_delivery_type_2').attr('disabled',true);
				$('#ac_expect_date_2').attr('disabled',true);
				
				$('#ac_delivery_type_1').attr('disabled',false);
				$('#ac_expect_date_1').attr('disabled',false);

				//$('#ac_term_div').attr('disabled',false);
				//$('#ac_term_date1_week').attr('disabled',false);
				//$('#ac_term_date1').attr('disabled',false);
				//$('#ac_term_date2').attr('disabled',false);

			}else if (value == '2'){

				$('#ac_delivery_type_1').attr('disabled',true);
				$('#ac_expect_date_1').attr('disabled',true);
				$('#ac_delivery_type_2').attr('disabled',false);
				$('#ac_expect_date_2').attr('disabled',false);

				//$('#ac_term_div').attr('disabled',true);
				//$('#ac_term_date1_week').attr('disabled',true);
				//$('#ac_term_date1').attr('disabled',true);
				//$('#ac_term_date2').attr('disabled',true);
			
			}
		
		});

		$('#ac_term_div').change(function(){
			
			var value = $(this).val();

			if(value == '1'){

				$('#ac_term_date2').css('display','none');
				$('#ac_term_date2').attr('disabled',true);
				
				$('#ac_term_date1_week').css('display','none');
				$('#ac_term_date1_week').attr('disabled',true);
				
				$('#ac_term_date1').css('display','');
				$('#ac_term_date1').attr('disabled',false);

			}else if(value == '2'){
				
				$('#ac_term_date1').css('display','');
				$('#ac_term_date1').attr('disabled',false);

				$('#ac_term_date2').css('display','');
				$('#ac_term_date2').attr('disabled',false);
				
				$('#ac_term_date1_week').css('display','none');
				$('#ac_term_date1_week').attr('disabled',true);
			
			}else if(value == '3'){

				$('#ac_term_date1_week').css('display','');
				$('#ac_term_date1_week').attr('disabled',false);

				$('#ac_term_date1').css('display','none');
				$('#ac_term_date1').attr('disabled',true);

				$('#ac_term_date2').css('display','none');
				$('#ac_term_date2').attr('disabled',true);
			}
		});

		change_term_div();

		$(\"#seller_date\").datepicker({
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'

		});

		$('input[name=account_div]').click(function (){
			var value = $(this).val();
			if(value == 'c'){
				$('#account_div_table').css('display','none');
			}else{
				$('#account_div_table').css('display','');
			}
		});

		var accrount_div_value = $('input[name=account_div][checked]').val(); 
		if(accrount_div_value == 'c'){
			$('#account_div_table').css('display','none');
		}else{
			$('#account_div_table').css('display','');
		}

	});

	function change_term_div(){
		
		value = $('#ac_term_div').val();

		if(value == '1'){

			$('#ac_term_date2').css('display','none');
			$('#ac_term_date2').attr('disabled',true);
			
			$('#ac_term_date1_week').css('display','none');
			$('#ac_term_date1_week').attr('disabled',true);
			
			$('#ac_term_date1').css('display','');
			$('#ac_term_date1').attr('disabled',false);

		}else if(value == '2'){
			
			$('#ac_term_date1').css('display','');
			$('#ac_term_date1').attr('disabled',false);

			$('#ac_term_date2').css('display','');
			$('#ac_term_date2').attr('disabled',false);
			
			$('#ac_term_date1_week').css('display','none');
			$('#ac_term_date1_week').attr('disabled',true);
		
		}else if(value == '3'){

			$('#ac_term_date1_week').css('display','');
			$('#ac_term_date1_week').attr('disabled',false);

			$('#ac_term_date1').css('display','none');
			$('#ac_term_date1').attr('disabled',true);

			$('#ac_term_date2').css('display','none');
			$('#ac_term_date2').attr('disabled',true);
		}

	}

	function loadContract(obj,target) {
		
		var contract_group = obj.find('option:selected').val();
		var form = obj.closest('form').attr('name'); 
		//alert(contract_group);
		$.ajax({ 
			type: 'GET', 
			data: {'act':'getContractList','return_type': 'json',  'contract_group':contract_group},
			url: '../econtract/contract.act.php',  
			dataType: 'json', 
			async: true, 
			beforeSend: function(){  
			},  
			error: function(request,status,error){ 
				alert('code:'+request.status+':: message:'+request.responseText+':: error:'+error);
			},  
			success: function(datas){
				$('select#'+target).find('option').not(':first').remove();
				if(datas != null){
					$.each(datas, function(i, data){ 
							$('select[name='+target+']').append(\"<option value='\"+data.et_ix+\"'>\"+data.contract_title+\"</option>\");
					});  
				}
			} 
		});  
	}

	function DeleteAddr(addr_ix,info_type,company_id){

		if(confirm('해당 정보를 삭제하시겠습니까?')){
			document.location.href='../seller/company.act.php?act=addr_delete&addr_ix='+addr_ix+'&info_type='+info_type+'&company_id='+company_id;
		}
	}
	</script>

	 <Script Language='JavaScript'>
		var eqIndex = ".($clon_no > 0 ? $clon_no : 0).";
		$(document).ready(function () {
			var copy_text;
			$('#banner_addbtn').click(function(){
				eqIndex++;
				copy_text = $('#banner_table tbody:first').html();
				$(copy_text).clone().appendTo('#banner_table tfoot');
				$('.file_text:eq('+eqIndex+')').text('');
				$('.link:eq('+eqIndex+')').val('');
				$('.title:eq('+eqIndex+')').val('');
				$('.bannerPriority:eq('+eqIndex+')').val('');
				$('.ix:eq('+eqIndex+')').val('');
			});

			$('#banner_delbtn').click(function(){
				var len = $('#banner_table .clone_tr').length;
				if(len > 1){
					eqIndex--;
					$('#banner_table .clone_tr:last').remove();
				}else{
					return false;
				}
			});

		});
	</script>";

	if($mmode == "pop"){
		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->strLeftMenu = seller_menu();
		$P->strContents = $Contents;
		$P->Navigation = "셀러관리 > 셀러업체 관리 > $menu_name";
		$P->NaviTitle = "셀러업체 $menu_name";
		echo $P->PrintLayOut();
	}else{

		$P = new LayOut();
		$P->addScript = $Script;
		$P->strLeftMenu = seller_menu();
		$P->strContents = $Contents;
		$P->Navigation = "셀러관리 > 셀러업체 관리 > $menu_name";
		$P->title = "$menu_name";
		echo $P->PrintLayOut();
	}

