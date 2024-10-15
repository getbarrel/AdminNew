<?

include("../logstory/class/sharedmemory.class");
$shmop = new Shared("delay_order_process_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$delay_rule = $shmop->getObjectForKey("delay_order_process_rule");
$delay_rule = unserialize(urldecode($delay_rule));

/*
상품명에 cut_str 걸려있는거 다 제거함 kbk 13/08/06
*/

$addQaDir = "";
if($admin_config['mall_domain'] == "0925admintest.barrelmade.co.kr"){
    $addQaDir = "/QA";
}

if($pre_type==ORDER_STATUS_EXCHANGE_READY){

	$Contents .= "
	<script type='text/javascript'>
	<!--
		var ContentsboxTop = 0;
		var ContentsboxTitleHeight = 0;

		$(document).ready(function() {
			ContentsboxTitleHeight=$('#scroll_title').css('height');
			$('#scroll_list').css('margin-top',ContentsboxTitleHeight);

			$('#scroll_div').scroll(function() {

					 ContentsboxTop=$('#scroll_title').position().top; 
					
					if(ContentsboxTop < 0){
						$('#scroll_title').css('margin-top',-ContentsboxTop+'px'); 
					}else if(ContentsboxTop > 0){
						$('#scroll_title').css('margin-top',ContentsboxTop+'px'); 
					}else{
						$('#scroll_title').css('margin-top','0px'); 
					}
			});
		});
	//-->
	</script>

	<div style='width:100%;overflow-y:scroll;overflow-x:scroll;position:relative;' id='scroll_div'>
	  <table width='200%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box' style='position:absolute;top:0px;margin-top:0px;' id='scroll_title'>
		<col width='15px'>
		<col width='5%'>
		<col width='10%'>
		<col width='*'>
		<col width='7%'>
		<col width='4%'>
		<col width='5%'>
		<col width='5%'>
		<col width='6%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='4%'>
		<col width='4%'>
		<tr height='25' >
			<td class='s_td sc_grid' align='center'  rowspan='2'><input type=checkbox  name='all_fix2' onclick='fixAll2(document.listform)'></td>
			<td align='center sc_grid' class='m_td' rowspan='2'><b>판매처</b></td>
			<td align='center sc_grid'  class='m_td' rowspan='2'><b>주문일자/주문번호<br/>주문자명/수취인</b></td>
			<td align='center sc_grid' class='m_td' rowspan='2' nowrap><b>상품명/옵션/</b></td>
			<td align='center sc_grid' class='m_td' rowspan='2' ><b>교환요청상품</b></td>
			<td align='center sc_grid' class='m_td' rowspan='2' ><b>발송수량</b></td>
			<td align='center' class='m_td' rowspan='2'><b>교환요청일</b></td>
			<td align='center' class='m_td' rowspan='2'><b>처리상태</b></td>
			<td align='center' class='m_td' rowspan='2'><b>교환사유</b></td>
			<td align='center' class='m_td' rowspan='2'><b>발송여부</b></td>
			<td align='center' class='m_td' colspan='3'><b>발송방법</b></td>
			<td align='center' class='m_td' rowspan='2'><b>회수상품상태</b></td>
			<td align='center' class='m_td' rowspan='2'><b>추가결제금액</b></td>
			<td align='center' class='m_td' rowspan='2'><b>결제방법</b></td>
			<td align='center' class='m_td' rowspan='2'><b>입금확인</b></td>
			<td align='center' class='m_td' rowspan='2'><b>관리</b></td>
		</tr>
		<tr>
			<td align='center' class='m_td' ><b>배송선택</b></td>
			<td align='center' class='m_td' ><b>배송방법/택배사</b></td>
			<td align='center' class='m_td' ><b>송장번호</b></td>
		</tr>
	</table>
	<table width='200%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box' id='scroll_list'>
		<col width='15px'>
		<col width='5%'>
		<col width='10%'>
		<col width='*'>
		<col width='7%'>
		<col width='4%'>
		<col width='5%'>
		<col width='5%'>
		<col width='6%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='4%'>
		<col width='4%'>
	<tr>";

	$order_datas = $master_db->fetchall();
	if(count($order_datas)){

		$addWhere = "";

		if(is_array($type)){
			if($type_str != ""){
				$addWhere .= "and od.status in ($type_str) ";
			}
		}else{
			if($type){
				$addWhere .= "and od.status = '$type' ";
			}
		}

		if($search_type && $search_text){
			if($search_type == "od.pname" || $search_type == "od.invoice_no" || $search_type == "od.option_text"){
				$addWhere .= "and $search_type LIKE '%".trim($search_text)."%' ";
			}
		}

		if($view_type == 'pos_order'){
			$addWhere .= "and od.status = '".ORDER_STATUS_RETURN_COMPLETE."' ";
		}

		if($_COOKIE["order_view_type"] == 1){
			$addWhere .= str_replace("WHERE","and",$where);
		}

		if($admininfo[admin_level] == 9){
			if($admininfo[mem_type] == "MD"){
				$addWhere .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}
		}else if($admininfo[admin_level] == 8){
			$addWhere .= " and od.company_id ='".$admininfo[company_id]."'  ";
		}

		if(is_array($p_admin) && count($p_admin) == 1){
			if($p_admin[0]=="A"){
				$addWhere .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
			}elseif($p_admin[0]=="S"){
				$addWhere .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
			}
		}else{
			if($p_admin=="A"){
				$addWhere .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
			}elseif($p_admin=="S"){
				$addWhere .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
			}
		}

		if($stock_use_yn != ""){
			$addWhere .= "and od.stock_use_yn = '".$stock_use_yn."'";
		}

		for ($i = 0; $i < count($order_datas); $i++)
		{
			//$slave_db->fetch($i);

			$sql = "SELECT o.oid, o.delivery_box_no, o.payment_price, o.payment_agent_type, o.user_code as user_id, o.buserid, o.bmobile, o.bname, o.gp_ix,
			 o.mem_group, o.status as ostatus, o.order_date as regdate,  o.total_price, 

			od.od_ix, od.product_type, od.pid, od.brand_name, od.pname, od.set_group, od.set_name, od.sub_pname, od.option_text, od.coprice,od.listprice,od.psprice, od.pcnt, od.ptprice, od.pt_dcprice,
			od.commission, od.delivery_status, od.stock_use_yn, od.order_from, od.pcode, od.admin_message, od.status, od.exchange_delivery_type, od.claim_delivery_od_ix, od.claim_group,
			od.company_name, od.company_id, od.reserve, od.co_pid, date_format(od.ic_date,'%Y-%m-%d') as incom_date, od.return_product_state, od.ea_date , od.ra_date,

			odd.delivery_method, odd.quick, odd.invoice_no, odd.send_yn, odd.send_type,

			op.method as add_method, op.pay_status as add_status, op.payment_price as add_payment_price,
			
			(select status from shop_order_detail where od_ix=od.claim_delivery_od_ix) as claim_apply_status,
			(select regdate from shop_order_status where oid=od.oid and od_ix=od.od_ix and status=od.status order by regdate desc limit 1) as status_regdate
			

			FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od 
			left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix)
			left join shop_order_payment op on (op.oid=od.oid and op.claim_group=od.claim_group and op.pay_type='A')
			where o.oid = od.oid and o.oid = '".$order_datas[$i][oid]."' 
			$addWhere
			ORDER BY od.company_id asc , status asc ";

			$slave_db->query($sql);
			$order_detail_datas = $slave_db->fetchall();
			$od_count = count($order_detail_datas);//$ddb->total;

			for($j=0;$j < count($order_detail_datas);$j++){

				//$ddb->fetch($j);

				$sql="SELECT 
						DATE_FORMAT(os.regdate,'%Y-%m-%d') as apply_date,
						os.status_message
					FROM 
						shop_order_status os 
					WHERE
						os.oid = '".$order_detail_datas[$j][oid]."'
						and os.od_ix = '".$order_detail_datas[$j][claim_delivery_od_ix]."'
						and os.status='".ORDER_STATUS_EXCHANGE_APPLY."'
						ORDER BY os.regdate DESC LIMIT 0,1";

				$slave_db->query($sql);
				$slave_db->fetch();
				$apply_date=$slave_db->dt["apply_date"];
				$status_message=$slave_db->dt["status_message"];

				$one_status = getOrderStatus($order_detail_datas[$j][status]).($order_detail_datas[$j][admin_message]!="" ? "<br><b class='grn'>".$order_detail_datas[$j][admin_message]."</b>":"")."<br>".str_replace(' ','<br/>',$order_detail_datas[$j][status_regdate]);

				$Contents .= "<tr height=28 >";
				$Contents .= "<td class='' align='center'><input type=checkbox name='od_ix[]' id='od_ix' oid='".$order_detail_datas[$j][oid]."' set_group='".$order_detail_datas[$j][set_group]."' stock_use_yn='".$order_detail_datas[$j][stock_use_yn]."' value='".$order_detail_datas[$j][od_ix]."' ></td>";

				if($order_detail_datas[$j][oid] != $b_oid){

					$u_etc_info=get_order_user_info($order_detail_datas[$j][user_id]);

					$b_mem_info = "<b style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='주문자 <br/>ID/성명 : ".$order_detail_datas[$j][buserid]."/".wel_masking_seLen($order_detail_datas[$j][bname], 1, 1)."<br/>핸드폰 : ".$order_detail_datas[$j][bmobile]." <br/>회원그룹 : ".$order_detail_datas[$j][mem_group]." <br/>최근로그인 : ".$u_etc_info["user_last"]." <br/>최근주문(30일) : ".$u_etc_info["user_order_cnt"]."건' />".Black_list_check($order_detail_datas[$j][user_id],($order_detail_datas[$j][gp_ix]=='2' ? "<b class='red'>VIP</b>" : "").wel_masking_seLen($order_detail_datas[$j][bname], 1, 1).( $order_detail_datas[$j][buserid] ? "(<span class='small'>".$order_detail_datas[$j][buserid]."</span>)" : "(<span class='small'>비회원</span>)"))."</b> <br/> ".($_SESSION["admininfo"]["admin_level"] > 8 && $order_detail_datas[$j][user_id] ? "<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PopSWindow2('/admin/member/member_cti.php?mmode=pop&code=".$order_detail_datas[$j][user_id]."&mmode=pop',1280,800,'member_view')\"  style='cursor:pointer;'>" : "");

					$recipient_info=getOrderRecipientInfo($order_detail_datas[$j]);
					$recipient_=$recipient_info["recipient"];
					$recipient_str=$recipient_info["recipient_str"];
					$recipient_width=$recipient_info["recipient_width"];
					$recipient_height=$recipient_info["recipient_height"];

					$r_mem_info= "<b style='cursor:pointer' class='helpcloud' help_width='".$recipient_width."' help_height='".$recipient_height."' help_html='".$recipient_str."' />".wel_masking_seLen($recipient_, 1, 1)."</b>";

					$Contents .= "
					 <td style='line-height:140%' align=center class='list_box_td' rowspan='".($od_count)."'>
						".getOrderFromName($order_detail_datas[$j][order_from])."
					</td>
					<td class='list_box_td point' style='line-height:140%' align=center rowspan='".($od_count)."'>
						".$order_detail_datas[$j][regdate]."<br>
						<font color='blue' ><b>
							<span style='color:#007DB7;font-weight:bold;' >".$order_detail_datas[$j][oid]."</span></b>".($order_detail_datas[$j][delivery_box_no] ? "<b style='color:red;'>-".$order_detail_datas[$j][delivery_box_no]."</b>":"")."
						</font> <br>
						<span class='helpcloud' help_width='55' help_height='15' help_html='주문서'>
							<img src='../images/icon/paper.gif' style='cursor:pointer' align='absmiddle' onclick=\"PopSWindow('../order/orders.read.php?oid=".$order_detail_datas[$j][oid]."&pid=".$order_detail_datas[$j][pid]."&mmode=pop',960,600)\"/>
							<a href=\"javascript:PopSWindow('../order/orders.edit.php?oid=".$order_detail_datas[$j][oid]."&pid=".$order_detail_datas[$j][pid]."&mmode=personalization&mem_ix=".$mem_ix."',960,600,'order_edit');\" ><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'></a>
						</span><br/>
						".$b_mem_info." / ".$r_mem_info."
					</td>";
				}

				$Contents .= "<td class='list_box_td' style='padding-left:10px'>
								<TABLE style='text-align:left;'>
									<TR>
										<TD align='center'>
											<!-- a  href='/shop/goods_view.php?id=".$order_detail_datas[$j][pid]."' target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $order_detail_datas[$j][pid], "m", $order_detail_datas[$j])."'  width=50 style='margin:5px;'></a -->
											<a  href='/shop/goods_view.php?id=".$order_detail_datas[$j][pid]."' target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $order_detail_datas[$j][pid], "slist", $order_detail_datas[$j])."'  width=50 style='margin:5px;'></a><br/>";

										if($order_detail_datas[$j][product_type]=='21'||$order_detail_datas[$j][product_type]=='31'){
										$Contents .= "<label class='helpcloud' help_width='190' help_height='15' help_html='".($order_detail_datas[$j][product_type]=='21' ? "서브스크립션 커머스(배송상품)" : "로컬딜리버리 커머스(배송상품)")."'><img src='../images/".$admininfo[language]."/s_product_type_".$order_detail_datas[$j][product_type].".gif' align='absmiddle' ></label> ";
										}
										if($order_detail_datas[$j][company_id]==$HEAD_OFFICE_CODE){
											$Contents .= "<label class='helpcloud' help_width='70' help_height='15' help_html='본사상품'><img src='../images/".$admininfo[language]."/s_admin_product.gif' align='absmiddle' ></label> ";
										}
										if($order_detail_datas[$j][stock_use_yn]=='Y'){
										$Contents .= "<label class='helpcloud' help_width='140' help_height='15' help_html='(WMS)재고관리 상품'><img src='../images/".$admininfo[language]."/s_inventory_use.gif' align='absmiddle' ></label>";
										}

					$Contents .= "</TD>
										<td width='5'>
										</td>
										<TD style='line-height:140%'>";

					$seller_info_str= GET_SELLER_INFO($order_detail_datas[$j][company_id]);

					if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
						$Contents .= "<b style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='".$seller_info_str."'>".($order_detail_datas[$j][company_name] ? $order_detail_datas[$j][company_name]:$order_detail_datas[$j][pname])."</b> <img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PopSWindow('../seller/seller_company.php?company_id=".$order_detail_datas[$j][company_id]."&mmode=pop',960,600,'brand');\"  style='cursor:pointer;'><br>";
					}

					$Contents .= "<a  href='../".$folder_name."/goods_input.php?id=".$order_detail_datas[$j][pid]."' target=_blank />";

					if($order_detail_datas[$j][product_type]=='99'||$order_detail_datas[$j][product_type]=='21'||$order_detail_datas[$j][product_type]=='31'){
						$Contents .= "<b class='".($order_detail_datas[$j][product_type]=='99' ? "red" : "blue")."' >[".$order_detail_datas[$j][company_name]."] ".$order_detail_datas[$j][pname]."</b><br/><strong>".$order_detail_datas[$j][set_name]."<br /></strong>".$order_detail_datas[$j][sub_pname];
					}else{
						$Contents .= "[".$order_detail_datas[$j][company_name]."] ".$order_detail_datas[$j][pname];
					}

					$Contents .= "</a>";

					if(strip_tags($order_detail_datas[$j][option_text])){
						$Contents .= "<br/> ▶ ".strip_tags($order_detail_datas[$j][option_text]);
					}

					$Contents .="
										</TD>
									</TR>
								</TABLE>
							</td>";

							if($order_detail_datas[$j][exchange_delivery_type]=="I"){
								$ec_delivery_type = "입고후발송";
							}elseif($order_detail_datas[$j][exchange_delivery_type]=="C"){
								$ec_delivery_type = "맞교환발송";
							}elseif($order_detail_datas[$j][exchange_delivery_type]=="F"){
								$ec_delivery_type = "선발송";
							}else{
								$ec_delivery_type = $order_detail_datas[$j][exchange_delivery_type];
							}

							/*
							if($order_detail_datas[$j][claim_delivery_status]==ORDER_STATUS_EXCHANGE_READY){
								$ec_delivery_img="<br/><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_delivery_ready.gif' style='cursor:pointer' onclick=\"if(confirm('교환배송예정 상품을 배송준비중으로 바꾸시겠습니까?')){window.frames['act'].location.href='./orders.goods_list.act.php?act=status_update&change_status=".ORDER_STATUS_DELIVERY_READY."&od_ix=".$order_detail_datas[$j][claim_delivery_od_ix]."'}\" />";
								//<!--br/>".getOrderStatus($order_detail_datas[$j][claim_delivery_status])."-->
							}else{
								$ec_delivery_img="";
							}
							*/

							$Contents .="<td class='list_box_td' style='line-height:140%'>
								".getOrderStatus($order_detail_datas[$j]["claim_apply_status"])."
								".$ec_delivery_img."
							</td>";

					$Contents .="
							<td class='list_box_td' align=center nowrap>".$order_detail_datas[$j][pcnt]."</td>
							<td class='list_box_td' align=center nowrap>".str_replace(" ","</br>",$apply_date)."</td>
							<td class='list_box_td point' align=center>".$ec_delivery_type."<br/>".$one_status."</td>
							<td class='list_box_td ' align=center >".$status_message."</td>
							<td class='list_box_td ' align='center' >".($order_detail_datas[$j][send_yn]=='Y' ? "발송":"<span class='red'>미발송</span>")."</td>";

			if($order_detail_datas[$j][send_type]=="1"){
				$send_type="직접발송";
			}elseif($order_detail_datas[$j][send_type]=="2"){
				$send_type="지정택배요청";
			}else{
				$send_type="-";
			}

			$Contents .="<td class='list_box_td point' align='center' >".$send_type."</td>
								<td class='list_box_td' align=center>".DeliveryMethod("",$order_detail_datas[$j][delivery_method],"","text")."<br/>".deliveryCompanyList($order_detail_datas[$j][quick],"text")."</td>
								<td class='list_box_td ' align='center' >".($order_detail_datas[$j][invoice_no] ? $order_detail_datas[$j][invoice_no] : "-")."</td>";

			if($order_detail_datas[$j][return_product_state]=="G"){
				$return_product_state="양호";
			}elseif($order_detail_datas[$j][return_product_state]=="B"){
				$return_product_state="불량";
			}else{
				$return_product_state="-";
			}

			$Contents .="<td class='list_box_td ' align='center' >".$return_product_state."</td>
								<td class='list_box_td ' align='center' >".number_format($order_detail_datas[$j][add_payment_price])."원</td>
								<td class='list_box_td ' align='center' >".getMethodStatus($order_detail_datas[$j][add_method])."</td>
								<td class='list_box_td ' align='center' >".($order_detail_datas[$j][add_status] ? getOrderStatus($order_detail_datas[$j][add_status]) : "-")."</td>";
			$Contents .= "<td lass='list_box_td '  align='center' >";
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					  $Contents .= "
									<a href=\"../order/orders.edit.php?oid=".$order_detail_datas[$j][oid]."&pid=".$order_detail_datas[$j][pid]."&mmode=".$mmode."&mem_ix=".$mem_ix."\"><img src='../images/".$admininfo["language"]."/btn_modify.gif' align=absmiddle  style='cursor:pointer;'></a>
									<br/><input type='button' value='신청정보 보기' align=absmiddle onclick=\"ShowModalWindow('./claim_apply.php?act=claim_update&oid=".$order_detail_datas[$j][oid]."&claim_group=".$order_detail_datas[$j][claim_group]."&apply_status=".substr($pre_type,0,1)."A',800,800,'claim_apply');\">
					  ";

					}else{
					   $Contents .=  "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_modify.gif' align=absmiddle style='cursor:pointer;'></a>";
					}
				$Contents .= "
							</td>";
		$Contents .= "</tr>";

				$b_oid = $order_detail_datas[$j][oid];
			}

		}
	}else{

		$Contents .= "<tr height=50><td colspan=16 align=center>조회된 결과가 없습니다.</td></tr>";
	}
	$Contents .= "
	  </table>
	 </div>";


}elseif($pre_type==ORDER_STATUS_EXCHANGE_ING || $pre_type==ORDER_STATUS_EXCHANGE_APPLY ||$pre_type==ORDER_STATUS_RETURN_ING || $pre_type==ORDER_STATUS_RETURN_APPLY ){

	if($pre_type==ORDER_STATUS_EXCHANGE_ING || $pre_type==ORDER_STATUS_EXCHANGE_APPLY){
		$table_title = "교환";
		$exchange_bool=true;
		$join_on_query =" and os.status='".ORDER_STATUS_EXCHANGE_APPLY."' ";
	}elseif($pre_type==ORDER_STATUS_RETURN_ING || $pre_type==ORDER_STATUS_RETURN_APPLY){
		$table_title = "반품";
		$exchange_bool=false;
		$join_on_query =" and os.status='".ORDER_STATUS_RETURN_APPLY."' ";
	}

	$Contents .= "
	<script type='text/javascript'>
	<!--
		var ContentsboxTop = 0;
		var ContentsboxTitleHeight = 0;

		$(document).ready(function() {
			ContentsboxTitleHeight=$('#scroll_title').css('height');
			$('#scroll_list').css('margin-top',ContentsboxTitleHeight);

			$('#scroll_div').scroll(function() {

					 ContentsboxTop=$('#scroll_title').position().top; 
					
					if(ContentsboxTop < 0){
						$('#scroll_title').css('margin-top',-ContentsboxTop+'px'); 
					}else if(ContentsboxTop > 0){
						$('#scroll_title').css('margin-top',ContentsboxTop+'px'); 
					}else{
						$('#scroll_title').css('margin-top','0px'); 
					}
			});
		});
	//-->
	</script>


	<div style='width:100%;overflow-y:scroll;overflow-x:scroll;position:relative;' id='scroll_div'>
	  <table width='200%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box' style='position:absolute;top:0px;margin-top:0px;' id='scroll_title'>
		<col width='15px'>
		<col width='5%'>
		<col width='10%'>
		<col width='*'>
		".($exchange_bool ? "<col width='7%'>" : "")."
		<col width='4%'>
		<col width='5%'>
		<col width='5%'>
		<col width='6%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='4%'>
		<col width='4%'>
		<tr height='25' >
			<td class='s_td sc_grid' align='center'  rowspan='2'><input type=checkbox  name='all_fix2' onclick='fixAll2(document.listform);initCheck(this);'></td>
			<td align='center sc_grid' class='m_td' rowspan='2'><b>판매처</b></td>
			<td align='center sc_grid'  class='m_td' rowspan='2'><b>주문일자/주문번호<br/>주문자명/수취인</b></td>
			<td align='center sc_grid' class='m_td' rowspan='2' nowrap><b>상품명/옵션/</b></td>
			".($exchange_bool ? "<td align='center sc_grid' class='m_td' rowspan='2' ><b>교환배송상품</b></td>" : "")."
			<td align='center sc_grid' class='m_td' rowspan='2' ><b>".$table_title."수량</b></td>
			<td align='center' class='m_td' rowspan='2'><b>".$table_title."요청일</b></td>
			<td align='center' class='m_td' rowspan='2'><b>처리상태</b></td>
			<td align='center' class='m_td' rowspan='2'><b>".$table_title."사유</b></td>
			<td align='center' class='m_td' rowspan='2'><b>발송여부</b></td>
			<td align='center' class='m_td' colspan='3'><b>발송방법</b></td>
			<td align='center' class='m_td' rowspan='2'><b>회수상품상태</b></td>
			<td align='center' class='m_td' rowspan='2'><b>추가결제금액</b></td>
			<td align='center' class='m_td' rowspan='2'><b>결제방법</b></td>
			<td align='center' class='m_td' rowspan='2'><b>입금확인</b></td>
			<td align='center' class='m_td' rowspan='2'><b>관리</b></td>
		</tr>
		<tr>
			<td align='center' class='m_td' ><b>배송선택</b></td>
			<td align='center' class='m_td' ><b>배송방법/택배사</b></td>
			<td align='center' class='m_td' ><b>송장번호</b></td>
		</tr>
	</table>
	<table width='200%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box' id='scroll_list'>
		<col width='15px'>
		<col width='5%'>
		<col width='10%'>
		<col width='*'>
		".($exchange_bool ? "<col width='7%'>" : "")."
		<col width='4%'>
		<col width='5%'>
		<col width='5%'>
		<col width='6%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='5%'>
		<col width='4%'>
		<col width='4%'>
	<tr>";


	$order_datas = $master_db->fetchall();
	if(count($order_datas)){

		$addWhere = " AND od.status !='SR' ";

		if(is_array($type)){
			if($type_str != ""){
				$addWhere .= "and od.status in ($type_str) ";
			}
		}else{
			if($type){
				$addWhere .= "and od.status = '$type' ";
			}
		}

		if($search_type && $search_text){
			if($search_type == "od.pname" || $search_type == "od.invoice_no" || $search_type == "od.option_text"){
				$addWhere .= "and $search_type LIKE '%".trim($search_text)."%' ";
			}
		}

		if($view_type == 'pos_order'){
			$addWhere .= "and od.status = '".ORDER_STATUS_RETURN_COMPLETE."' ";
		}

		if($_COOKIE["order_view_type"] == 1){
			$addWhere .= str_replace("WHERE","and",$where);
		}

		if($admininfo[admin_level] == 9){
			if($admininfo[mem_type] == "MD"){
				$addWhere .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}
		}else if($admininfo[admin_level] == 8){
			$addWhere .= " and od.company_id ='".$admininfo[company_id]."'  ";
		}

		if(is_array($p_admin) && count($p_admin) == 1){
			if($p_admin[0]=="A"){
				$addWhere .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
			}elseif($p_admin[0]=="S"){
				$addWhere .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
			}
		}else{
			if($p_admin=="A"){
				$addWhere .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
			}elseif($p_admin=="S"){
				$addWhere .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
			}
		}

		if($stock_use_yn != ""){
			$addWhere .= "and od.stock_use_yn = '".$stock_use_yn."'";
		}

		//(select status from shop_order_detail where od_ix=od.claim_delivery_od_ix) as claim_delivery_status
		for ($i = 0; $i < count($order_datas); $i++)
		{
			//$slave_db->fetch($i);
			//$slave_db->total

			$sql = "SELECT o.oid, o.delivery_box_no, o.payment_price, o.payment_agent_type, o.user_code as user_id, o.buserid, o.bmobile, o.bname, o.gp_ix,
			 o.mem_group, o.status as ostatus, o.order_date as regdate,  AES_DECRYPT(UNHEX(o.refund_bank),'".$db->ase_encrypt_key."') as refund_bank1, AES_DECRYPT(UNHEX(o.refund_bank_name),'".$db->ase_encrypt_key."') as refund_bank_name1, o.total_price, 

			od.od_ix, od.product_type, od.pid, od.brand_name, od.pname, od.set_group, od.set_name, od.sub_pname, od.option_text, od.coprice,od.listprice,od.psprice, od.pcnt, od.ptprice, od.pt_dcprice,
			od.commission, od.delivery_status, od.stock_use_yn, od.order_from, od.pcode, od.admin_message, od.status, od.exchange_delivery_type, od.claim_delivery_od_ix, od.claim_group,
			od.company_name, od.company_id, od.reserve, od.co_pid, date_format(od.ic_date,'%Y-%m-%d') as incom_date, od.return_product_state, od.ea_date , od.ra_date, od.add_info, 

			odd.delivery_method, odd.quick, odd.invoice_no, odd.send_yn, odd.send_type,

			op.method as add_method, op.pay_status as add_status, op.payment_price as add_payment_price,

			(select regdate from shop_order_status where oid=od.oid and od_ix=od.od_ix and status=od.status order by regdate desc limit 1) as status_regdate
			

			FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od 
			left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix)
			left join shop_order_payment op on (op.oid=od.oid and op.claim_group=od.claim_group and op.pay_type='A')
			where o.oid = od.oid and o.oid = '".$order_datas[$i][oid]."' 
			$addWhere
			ORDER BY od.company_id asc , status asc ";

			$slave_db->query($sql);
			$order_detail_datas = $slave_db->fetchall();
			$od_count = count($order_detail_datas);//$ddb->total;

			for($j=0;$j < count($order_detail_datas);$j++){//$ddb->total

				//$ddb->fetch($j);

				$sql="SELECT 
						DATE_FORMAT(os.regdate,'%Y-%m-%d') as apply_date,
						os.status_message
					FROM 
						shop_order_status os 
					WHERE
						os.oid = '".$order_detail_datas[$j][oid]."'
						and os.od_ix = '".$order_detail_datas[$j][od_ix]."'
						$join_on_query
						ORDER BY os.regdate DESC LIMIT 0,1";

				$slave_db->query($sql);
				$slave_db->fetch();
				$apply_date=$slave_db->dt["apply_date"];
				$status_message=$slave_db->dt["status_message"];

				$one_status = getOrderStatus($order_detail_datas[$j][status]).($order_detail_datas[$j][admin_message]!="" ? "<br><b class='grn'>".$order_detail_datas[$j][admin_message]."</b>":"")."<br>".str_replace(' ','<br/>',$order_detail_datas[$j][status_regdate]);

				$Contents .= "<tr height=28 >";
				$Contents .= "<td class='' align='center'><input type=checkbox ".($type == "RD" || $type == "ED" ? "onclick='initCheck(this)'" : "")." name='od_ix[]' id='od_ix' oid='".$order_detail_datas[$j][oid]."' set_group='".$order_detail_datas[$j][set_group]."' stock_use_yn='".$order_detail_datas[$j][stock_use_yn]."' value='".$order_detail_datas[$j][od_ix]."' ></td>";

				if($order_detail_datas[$j][oid] != $b_oid){

					$u_etc_info=get_order_user_info($order_detail_datas[$j][user_id]);

					$b_mem_info = "<b style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='주문자 <br/>ID/성명 : ".$order_detail_datas[$j][buserid]."/".wel_masking_seLen($order_detail_datas[$j][bname], 1, 1)."<br/>핸드폰 : ".$order_detail_datas[$j][bmobile]." <br/>회원그룹 : ".$order_detail_datas[$j][mem_group]." <br/>최근로그인 : ".$u_etc_info["user_last"]." <br/>최근주문(30일) : ".$u_etc_info["user_order_cnt"]."건' />".Black_list_check($order_detail_datas[$j][user_id],($order_detail_datas[$j][gp_ix]=='2' ? "<b class='red'>VIP</b>" : "").wel_masking_seLen($order_detail_datas[$j][bname], 1, 1).( $order_detail_datas[$j][buserid] ? "(<span class='small'>".$order_detail_datas[$j][buserid]."</span>)" : "(<span class='small'>비회원</span>)"))."</b> <br/> ".($_SESSION["admininfo"]["admin_level"] > 8 && $order_detail_datas[$j][user_id] ? "<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PopSWindow2('/admin/member/member_cti.php?mmode=pop&code=".$order_detail_datas[$j][user_id]."&mmode=pop',1280,800,'member_view')\"  style='cursor:pointer;'>" : "");

					$recipient_info=getOrderRecipientInfo($order_detail_datas[$j]);
					$recipient_=$recipient_info["recipient"];
					$recipient_str=$recipient_info["recipient_str"];
					$recipient_width=$recipient_info["recipient_width"];
					$recipient_height=$recipient_info["recipient_height"];

					$r_mem_info= "<b style='cursor:pointer' class='helpcloud' help_width='".$recipient_width."' help_height='".$recipient_height."' help_html='".$recipient_str."' />".wel_masking_seLen($recipient_,1 ,1 )."</b>";

					$Contents .= "
					 <td style='line-height:140%' align=center class='list_box_td' rowspan='".($od_count)."'>
						".getOrderFromName($order_detail_datas[$j][order_from])."
					</td>
					<td class='list_box_td point' style='line-height:140%' align=center rowspan='".($od_count)."'>
						".$order_detail_datas[$j][regdate]."<br>
						<font color='blue' ><b>
							<span style='color:#007DB7;font-weight:bold;' >".$order_detail_datas[$j][oid]."</span></b>".($order_detail_datas[$j][delivery_box_no] ? "<b style='color:red;'>-".$order_detail_datas[$j][delivery_box_no]."</b>":"")."
						</font><br>
						<span class='helpcloud' help_width='55' help_height='15' help_html='주문서'>
							<img src='../images/icon/paper.gif' style='cursor:pointer' align='absmiddle' onclick=\"PopSWindow('../order/orders.read.php?oid=".$order_detail_datas[$j][oid]."&pid=".$order_detail_datas[$j][pid]."&mmode=pop',960,600)\"/>
							<a href=\"javascript:PopSWindow('../order/orders.edit.php?oid=".$order_detail_datas[$j][oid]."&pid=".$order_detail_datas[$j][pid]."&mmode=personalization&mem_ix=".$mem_ix."',960,600,'order_edit');\" ><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'></a>
						</span><br/>
						".$b_mem_info." / ".$r_mem_info."
					</td>";
				}

				$Contents .= "<td class='list_box_td' style='padding-left:10px'>
								<TABLE style='text-align:left;'>
									<TR>
										<TD align='center'>
											<!-- a  href='/shop/goods_view.php?id=".$order_detail_datas[$j][pid]."' target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $order_detail_datas[$j][pid], "m", $order_detail_datas[$j])."'  width=50 style='margin:5px;'></a -->
											<a  href='/shop/goods_view.php?id=".$order_detail_datas[$j][pid]."' target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $order_detail_datas[$j][pid], "slist", $order_detail_datas[$j])."'  width=50 style='margin:5px;'></a><br/>";

										if($order_detail_datas[$j][product_type]=='21'||$order_detail_datas[$j][product_type]=='31'){
										$Contents .= "<label class='helpcloud' help_width='190' help_height='15' help_html='".($order_detail_datas[$j][product_type]=='21' ? "서브스크립션 커머스(배송상품)" : "로컬딜리버리 커머스(배송상품)")."'><img src='../images/".$admininfo[language]."/s_product_type_".$order_detail_datas[$j][product_type].".gif' align='absmiddle' ></label> ";
										}
										if($order_detail_datas[$j][company_id]==$HEAD_OFFICE_CODE){
											$Contents .= "<label class='helpcloud' help_width='70' help_height='15' help_html='본사상품'><img src='../images/".$admininfo[language]."/s_admin_product.gif' align='absmiddle' ></label> ";
										}
										if($order_detail_datas[$j][stock_use_yn]=='Y'){
										$Contents .= "<label class='helpcloud' help_width='140' help_height='15' help_html='(WMS)재고관리 상품'><img src='../images/".$admininfo[language]."/s_inventory_use.gif' align='absmiddle' ></label>";
										}

					$Contents .= "</TD>
										<td width='5'>
										</td>
										<TD style='line-height:140%'>";

					$seller_info_str= GET_SELLER_INFO($order_detail_datas[$j][company_id]);

					if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
						$Contents .= "<b style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='".$seller_info_str."'>".($order_detail_datas[$j][company_name] ? $order_detail_datas[$j][company_name]:$order_detail_datas[$j][pname])."</b> <img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PopSWindow('../seller/seller_company.php?company_id=".$order_detail_datas[$j][company_id]."&mmode=pop',960,600,'brand');\"  style='cursor:pointer;'><br>";
					}

					$Contents .= "<a  href='../".$folder_name."/goods_input.php?id=".$order_detail_datas[$j][pid]."' target=_blank />";

					if($order_detail_datas[$j][product_type]=='99'||$order_detail_datas[$j][product_type]=='21'||$order_detail_datas[$j][product_type]=='31'){
						//$Contents .= "<b class='".($order_detail_datas[$j][product_type]=='99' ? "red" : "blue")."' >[".$order_detail_datas[$j][brand_name]."] ".$order_detail_datas[$j][pname]."</b><br/><strong>".$order_detail_datas[$j][set_name]."<br /></strong>".$order_detail_datas[$j][sub_pname];
					}else{
						//$Contents .= "[".$order_detail_datas[$j][brand_name]."] ".$order_detail_datas[$j][pname];
					}

                    $Contents .= "[".$order_detail_datas[$j][company_name]."] ".$order_detail_datas[$j][pname]; //셀러명으로 고정

					$Contents .= "</a>";

					if(strip_tags($order_detail_datas[$j][option_text])){
						$Contents .= "<br/> ▶ ".strip_tags($order_detail_datas[$j][option_text])." / ".$order_detail_datas[$j][add_info];;
					}

					$Contents .="
										</TD>
									</TR>
								</TABLE>
							</td>";

							if($exchange_bool){

								if($order_detail_datas[$j][exchange_delivery_type]=="I"){
									$ec_delivery_type = "입고후발송";
								}elseif($order_detail_datas[$j][exchange_delivery_type]=="C"){
									$ec_delivery_type = "맞교환발송";
								}elseif($order_detail_datas[$j][exchange_delivery_type]=="F"){
									$ec_delivery_type = "선발송";
								}else{
									$ec_delivery_type = $order_detail_datas[$j][exchange_delivery_type];
								}

								/*
								if($order_detail_datas[$j][claim_delivery_status]==ORDER_STATUS_EXCHANGE_READY){
									$ec_delivery_img="<br/><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_delivery_ready.gif' style='cursor:pointer' onclick=\"if(confirm('교환배송예정 상품을 배송준비중으로 바꾸시겠습니까?')){window.frames['act'].location.href='./orders.goods_list.act.php?act=status_update&change_status=".ORDER_STATUS_DELIVERY_READY."&od_ix=".$order_detail_datas[$j][claim_delivery_od_ix]."'}\" />";
									//<!--br/>".getOrderStatus($order_detail_datas[$j][claim_delivery_status])."-->
								}else{
									$ec_delivery_img="";
								}
								*/

								$Contents .="<td class='list_box_td' style='line-height:140%'>
									<b>".$ec_delivery_type."</b>
									
									".$ec_delivery_img."
								</td>";
							}

					$Contents .="
							<td class='list_box_td detailCnt' align=center nowrap>".$order_detail_datas[$j][pcnt]."</td>
							<td class='list_box_td' align=center nowrap>".str_replace(" ","</br>",$order_detail_datas[$j][strtolower(substr($pre_type,0,1))."a_date"])."</td>
							<td class='list_box_td point' align=center>".$one_status."</td>
							<td class='list_box_td ' align=center >".$status_message."</td>
							<td class='list_box_td ' align='center' >".($order_detail_datas[$j][send_yn]=='Y' ? "발송":"<span class='red'>미발송</span>")."</td>";

			if($order_detail_datas[$j][send_type]=="1"){
				$send_type="직접발송";
			}elseif($order_detail_datas[$j][send_type]=="2"){
				$send_type="지정택배요청";
			}else{
				$send_type="-";
			}

			$Contents .="<td class='list_box_td point' align='center' >".$send_type."</td>
								<td class='list_box_td' align=center>".DeliveryMethod("",$order_detail_datas[$j][delivery_method],"","text")."<br/>".deliveryCompanyList($order_detail_datas[$j][quick],"text")."</td>
								<td class='list_box_td ' align='center' >".($order_detail_datas[$j][invoice_no] ? $order_detail_datas[$j][invoice_no] : "-")."</td>";

			if($order_detail_datas[$j][return_product_state]=="G"){
				$return_product_state="양호";
			}elseif($order_detail_datas[$j][return_product_state]=="B"){
				$return_product_state="불량";
			}else{
				$return_product_state="-";
			}

			$Contents .="<td class='list_box_td ' align='center' >".$return_product_state."</td>
								<td class='list_box_td ' align='center' >".number_format($order_detail_datas[$j][add_payment_price])."원</td>
								<td class='list_box_td ' align='center' >".getMethodStatus($order_detail_datas[$j][add_method])."</td>
								<td class='list_box_td ' align='center' >".($order_detail_datas[$j][add_status] ? getOrderStatus($order_detail_datas[$j][add_status]) : "-")."</td>";
			$Contents .= "<td lass='list_box_td '  align='center' >";
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					  $Contents .= "
									<a href=\"../order/orders.edit.php?oid=".$order_detail_datas[$j][oid]."&pid=".$order_detail_datas[$j][pid]."&mmode=".$mmode."&mem_ix=".$mem_ix."\"><img src='../images/".$admininfo["language"]."/btn_modify.gif' align=absmiddle  style='cursor:pointer;'></a>
									<br/><input type='button' value='신청정보 보기' align=absmiddle onclick=\"ShowModalWindow('./claim_apply.php?act=claim_update&oid=".$order_detail_datas[$j][oid]."&claim_group=".$order_detail_datas[$j][claim_group]."&apply_status=".substr($pre_type,0,1)."A',800,800,'claim_apply');\">
					  ";

					}else{
					   $Contents .=  "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_modify.gif' align=absmiddle style='cursor:pointer;'></a>";
					}
				$Contents .= "
							</td>";
		$Contents .= "</tr>";

				$b_oid = $order_detail_datas[$j][oid];
			}

		}
	}else{

		$Contents .= "<tr height=50><td colspan=16 align=center>조회된 결과가 없습니다.</td></tr>";
	}
	$Contents .= "
	  </table>
	 </div>";


}elseif($pre_type=='MethodBank'||$pre_type=='refund'||$pre_type==ORDER_STATUS_INCOM_READY||$pre_type==ORDER_STATUS_INCOM_COMPLETE||$pre_type==ORDER_STATUS_DEFERRED_PAYMENT||$pre_type==ORDER_STATUS_REFUND_APPLY||$pre_type==ORDER_STATUS_REFUND_COMPLETE || $pre_type =='sos_product'){

	$Contents .= "
		<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box' style='border-bottem:0px;'>
		<col width='30px'/>
		".($pre_type=='MethodBank' || $pre_type==ORDER_STATUS_INCOM_READY||$pre_type==ORDER_STATUS_INCOM_COMPLETE || $pre_type=='sos_product' ? "<col width='30px'/>" : "" )."
		".($admininfo[admin_level]==9 ? "<col width='8%'/>" : "" )."
		<col width='15%'/>
		<col width='*'/>
		<col width='17%'/>
		<col width='8%'/>
		<col width='11%'/>
		<col width='8%'/>
		<col width='11%'/>
		<col width='6%'/>
		<tr height='35' >
			<td align='center' class='s_td' style='background-color:#fff7da;' >";
			if($pre_type=='MethodBank'||$pre_type==ORDER_STATUS_INCOM_READY||$pre_type=='refund'||$pre_type==ORDER_STATUS_REFUND_APPLY||$pre_type==ORDER_STATUS_REFUND_COMPLETE ){
				$Contents .= "<input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'>";
			}elseif($pre_type==ORDER_STATUS_INCOM_COMPLETE||$pre_type ==ORDER_STATUS_DEFERRED_PAYMENT){
				$Contents .= "<input type=checkbox  name='all_fix_oid' onclick='fixAllOid(document.listform)'>";
			}elseif($pre_type == 'sos_product'){
			    $Contents .= "<input type=checkbox  name='all_fix2' onclick='fixAll2(document.listform)'>";
            }
	$Contents .= "</td>
			".($pre_type=='MethodBank'||$pre_type==ORDER_STATUS_INCOM_READY||$pre_type==ORDER_STATUS_INCOM_COMPLETE ? "<td align='center' class='m_td' style='background-color:#fff7da;' ><font color='#000000' ><b>지연알림</b></font></td>" : "" )."
			".($admininfo[admin_level]==9 ? "<td align='center' class='m_td' style='background-color:#fff7da;' ><font color='#000000' ><b>판매처</b></font></td>" : "" )."
			<td align='center'  class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' ><b>주문일</b></font></td>
			<td align='center' class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' ><b>주문번호</b></font></td>
			<td align='center' class='m_td' style='background-color:#fff7da;' nowrap><font color='#000000' ><b>주문자/수취인</b></font></td>
			<td align='center' class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' ><b>결제방법/구분</b></font></td>
			<td align='center' class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' ><b>입금상태(입금일)</b></font></td>
			<td align='center' class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' ><b>영수증</b></font></td>
			".($admininfo[admin_level]==9 ? "<td align='center' class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' ><b>총 결제금액</b></font></td>" : "" )."
			<td align='center' class='e_td' style='background-color:#fff7da;'  nowrap><font color='#000000' ><b>관리</b></font></td>
		</tr>
		</table>
		 <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' ".($pre_type=='MethodBank' ? "" : "class='list_table_box'")." >";
	if($pre_type!='MethodBank'){
		$Contents .= "
		<tr height='45' >
			<td width='*' align='center' class='m_td' ><font color='#000000' ><b>주문상세번호/상품명/옵션</b></font></td>
			<td width='8%' align='center' class='m_td' nowrap ><font color='#000000' ><b>정가<br/>/판매가(할인가)</b></font></td>
			<td width='8%' align='center' class='m_td' nowrap ><font color='#000000' ><b>할인금액</b></font></td>
			<td width='8%' align='center' class='m_td' nowrap ><font color='#000000' ><b>결제금액(수량)</b></font></td>
			<td width='7%' align='center' class='m_td' nowrap ><font color='#000000' ><b>배송방법/배송비</b></font></td>
			<td width='7%' align='center' class='m_td' nowrap ><font color='#000000' ><b>적립금</b></font></td>
			".($pre_type!='refund' && $pre_type!=ORDER_STATUS_REFUND_APPLY && $pre_type!=ORDER_STATUS_REFUND_COMPLETE ? "<td width='7%' align='center' class='m_td' nowrap ><font color='#000000' ><b>재고/진행/부족</b></font></td>" : "")."
			<td width='7%' align='center' class='m_td' nowrap ><font color='#000000' ><b>처리상태</b></font></td>
			".($admininfo[admin_level]==9 ? "<td width='7%' align='center' class='m_td' nowrap ><font color='#000000' ><b>출고처리상태</b></font></td>" : "" )."
			".($pre_type=='refund'||$pre_type==ORDER_STATUS_REFUND_APPLY||$pre_type==ORDER_STATUS_REFUND_COMPLETE ? "<td width='7%' align='center' class='m_td' nowrap ><font color='#000000' ><b>환불상태</b></font></td>" : "" )."";

			if($pre_type==ORDER_STATUS_INCOM_COMPLETE){
				$Contents .= "
				<td width='5%' align='center' class='m_td' ><font color='#000000' ><b>발송예정일</b></font></td>";
			}
		$Contents .= "
		</tr>";
	}


	if($admininfo[admin_level]==9 ){
		if($pre_type==ORDER_STATUS_INCOM_COMPLETE)	$_colspan = "10";
		else																	$_colspan = "9";
	}else{
		if($pre_type==ORDER_STATUS_INCOM_COMPLETE)	$_colspan = "9";
		else																	$_colspan = "8";
	}
	$order_datas = $master_db->fetchall();
	if(count($order_datas)){//$slave_db->total

		$addWhere = " AND od.status !='SR' ";

		if($pre_type=='refund'||$pre_type==ORDER_STATUS_REFUND_APPLY||$pre_type==ORDER_STATUS_REFUND_COMPLETE){
			if(is_array($refund_type)){
				if($refund_type_str != ""){
					$addWhere .= " and od.refund_status in ($refund_type_str) ";
				}
			}else{
				if($refund_type){
					$addWhere .= " and od.refund_status = '$refund_type' ";
				}
			}
		}

		//입금확인리스트에서 취소 건도 포함뒤어 나와서 확인후 해당 조건을 추가 2014-02-05 이학봉 시작
		if(is_array($type)){
			if($type_str != ""){
				$addWhere .= "and od.status in ($type_str) ";
			}
		}else{
			if($type){
				$addWhere .= "and od.status = '$type' ";
			}
		}

		if($search_type && $search_text){
			if($search_type == "od.pname" || $search_type == "od.invoice_no" || $search_type == "od.option_text"){
				$addWhere .= "and $search_type LIKE '%".trim($search_text)."%' ";
			}
		}

		if($admininfo[admin_level] == 9){
			if($admininfo[mem_type] == "MD"){
				$addWhere .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}
		}else if($admininfo[admin_level] == 8){
			$addWhere .= " and od.company_id ='".$admininfo[company_id]."'  ";
		}

		if(is_array($p_admin) && count($p_admin) == 1){
			if($p_admin[0]=="A"){
				$addWhere .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
			}elseif($p_admin[0]=="S"){
				$addWhere .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
			}
		}else{
			if($p_admin=="A"){
				$addWhere .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
			}elseif($p_admin=="S"){
				$addWhere .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
			}
		}

		if($stock_use_yn != ""){
			$addWhere .= "and od.stock_use_yn = '".$stock_use_yn."'";
		}

        if($product_type != ""){
            $addWhere .= "and od.product_type = '".$product_type."'";
        }

		for ($i = 0; $i < count($order_datas); $i++)
		{
			//$slave_db->total
			//$slave_db->fetch($i);

			$sql = "SELECT

			o.oid, o.delivery_box_no, o.payment_price, o.payment_agent_type, o.user_code as user_id, o.buserid, o.bmobile, o.bname, o.gp_ix,
			o.mem_group, o.status as ostatus, o.order_date as regdate, o.refund_method, o.refund_bank, o.refund_bank_name, o.total_price,od.fc_date,
			AES_DECRYPT(UNHEX(o.refund_bank),'".$db->ase_encrypt_key."') as refund_bank1, AES_DECRYPT(UNHEX(o.refund_bank_name),'".$db->ase_encrypt_key."') as refund_bank_name1,
			od.od_ix, od.product_type, od.pid, od.brand_name, od.pname, od.set_group, od.set_name, od.sub_pname, od.option_text, od.coprice,od.listprice,od.psprice, od.pcnt, od.ptprice, od.pt_dcprice,
			od.commission, od.delivery_status, od.stock_use_yn, od.order_from, od.pcode, od.admin_message, od.status, od.refund_status, od.option_kind,od.ode_ix,
			od.company_name, od.company_id, od.reserve, od.co_pid, date_format(od.ic_date,'%Y-%m-%d') as incom_date, od.due_date,od.co_oid,od.co_od_ix,
			od.real_lack_stock,

			od.delivery_type,od.delivery_policy,od.delivery_package,od.delivery_method,od.delivery_pay_method,od.ori_company_id,od.delivery_addr_use,od.factory_info_addr_ix,

			(select IFNULL(delivery_dcprice,'0') as delivery_dcprice 
				from 
					shop_order_delivery 
				where
					ode_ix=od.ode_ix
			) as delivery_totalprice,
			
			(select regdate from shop_order_status where oid=od.oid and (case when od.status='IC' then status='IC' else (od_ix=od.od_ix and status=od.status) end) order by regdate desc limit 1) as status_regdate,
			(case when (od.stock_use_yn ='Y' and (od.option_kind = 'x2' or od.option_kind = 'b' or od.option_kind = 'x' or od.option_kind = 's2' or od.option_kind = 'c')) or od.order_from != 'self' then
				(select sum(stock) from inventory_product_stockinfo ps where ps.gid=gu.gid and ps.unit=gu.unit)
			else
				(case when od.option_id != 0 then pod.option_stock else p.stock end)
			end) as stock,
			
			(case when (od.stock_use_yn ='Y' and (od.option_kind = 'x2' or od.option_kind = 'b' or od.option_kind = 'x' or od.option_kind = 's2' or od.option_kind = 'c')) or od.order_from != 'self' then
				gu.sell_ing_cnt
			else
				(case when od.option_id != 0 then pod.option_sell_ing_cnt else p.sell_ing_cnt end)
			end) as sell_ing_cnt,
			od.mall_ix

			FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od 
			left join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod on (od.option_id=pod.id)
			left join ".TBL_SHOP_PRODUCT." p on (p.id=od.pid)
			left join inventory_goods_unit gu on (gu.gu_ix=od.gu_ix)

			where o.oid = od.oid and o.oid = '".$order_datas[$i][oid]."' 
			$addWhere
			ORDER BY o.oid desc, od.ode_ix ASC, od.pid ASC, od.set_group asc";


			//$ddb->query($sql);

			/*
			if ($order_detail_datas[$j][status] == ORDER_STATUS_DELIVERY_COMPLETE)		{
				$delete = "<a href=\"javascript:alert(language_data['orders.list.php']['A'][language]);\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle></a>";//[처리완료] 기록은 삭제할 수 없습니다.
			}elseif ($order_detail_datas[$j][status] != ORDER_STATUS_CANCEL_COMPLETE && $order_datas[$i][method] == "1"){
				$delete = "<a href=\"javascript:order_delete('delete','".$order_datas[$i][oid]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle></a>";
			}else{
				$delete = "<a href=\"javascript:act('delete','".$order_datas[$i][oid]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:2px;'></a>";
			}
			*/
			$slave_db->query($sql);
			$order_detail_datas = $slave_db->fetchall();
			$od_count = count($order_detail_datas);//$ddb->total;
			$bcompany_id = '';

            $origin_currency_unit = $admin_config["currency_unit"];
			for($j=0;$j < count($order_detail_datas);$j++){//$ddb->total

				//$ddb->fetch($j);
                $admin_config["currency_unit"] = check_currency_unit($order_detail_datas[$j]['mall_ix']);

				if($order_detail_datas[$j][oid] != $b_oid){

					$method_info = getOrderMethodInfo($order_detail_datas[$j]);
					$method_=$method_info["method"];
					$method_str=$method_info["method_str"];
					$method_width=$method_info["method_width"];
					$method_height=$method_info["method_height"];
					$receipt_type_str=$method_info["receipt"];

					if($pre_type=='MethodBank'){
						$method_bank_info=$method_info["method_pay_info"];
					}

					$method = "<label class='helpcloud' help_width='".$method_width."' help_height='".$method_height."' help_html='".$method_str."'>".getMethodStatus($method_,"img")."</label>";

					$u_etc_info=get_order_user_info($order_detail_datas[$j][user_id]);

					$b_mem_info = "<b style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='주문자 <br/>ID/성명 : ".$order_detail_datas[$j][buserid]."/".wel_masking_seLen($order_detail_datas[$j][bname], 1, 1)."<br/>핸드폰 : ".$order_detail_datas[$j][bmobile]." <br/>회원그룹 : ".$order_detail_datas[$j][mem_group]." <br/>최근로그인 : ".$u_etc_info["user_last"]." <br/>최근주문(30일) : ".$u_etc_info["user_order_cnt"]."건' />".Black_list_check($order_detail_datas[$j][user_id],($order_detail_datas[$j][gp_ix]=='2' ? "<b class='red'>VIP</b>" : "").wel_masking_seLen($order_detail_datas[$j][bname], 1, 1).( $order_detail_datas[$j][buserid] ? "(<span class='small'>".$order_detail_datas[$j][buserid]."</span>)" : "(<span class='small'>비회원</span>)"))."</b> <br/> ".($_SESSION["admininfo"]["admin_level"] > 8 && $order_detail_datas[$j][user_id] ? "<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PopSWindow2('/admin/member/member_cti.php?mmode=pop&code=".$order_detail_datas[$j][user_id]."&mmode=pop',1280,800,'member_view')\"  style='cursor:pointer;'>" : "");

					$recipient_info=getOrderRecipientInfo($order_detail_datas[$j]);
					$recipient_=$recipient_info["recipient"];
					$recipient_str=$recipient_info["recipient_str"];
					$recipient_width=$recipient_info["recipient_width"];
					$recipient_height=$recipient_info["recipient_height"];

					$r_mem_info= "<b style='cursor:pointer' class='helpcloud' help_width='".$recipient_width."' help_height='".$recipient_height."' help_html='".$recipient_str."' />".wel_masking_seLen($recipient_,1 ,1 )."</b>";

					$Contents .= "
							<tr>
								<td class='' style='background-color:#fff7da;height:30px;font-weight:bold;padding:0px;' class=blue colspan='".$_colspan."' >
									<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' ".($pre_type=='MethodBank' ? "class='list_table_box'" : "").">
										<col width='30px'/>
		".($pre_type=='MethodBank' || $pre_type==ORDER_STATUS_INCOM_READY||$pre_type==ORDER_STATUS_INCOM_COMPLETE ? "<col width='30px'/>" : "" )."
		".($admininfo[admin_level]==9 ? "<col width='8%'/>" : "" )."
		<col width='15%'/>
		<col width='*'/>
		<col width='17%'/>
		<col width='8%'/>
		<col width='11%'/>
		<col width='8%'/>
		<col width='11%'/>
		<col width='6%'/>
										<tr height=45>
											<td align='center' ".($pre_type=='MethodBank' ? "rowspan='2'" : "style='background-color:#fff7da;'")."><input type=checkbox name='oid[]' id='oid' value='".$order_detail_datas[$j][oid]."' ".($pre_type==ORDER_STATUS_INCOM_COMPLETE||$pre_type ==ORDER_STATUS_DEFERRED_PAYMENT ||$pre_type=='sos_product' ? "onclick=\"fixAllOdix(this)\"" : "")."><input type=hidden name='bstatus[".$order_detail_datas[$j][oid]."]' value='".$order_detail_datas[$j][ostatus]."'><input type='hidden' id='od_status_".str_replace("-","",$order_detail_datas[$j][oid])."'></td>";

											if($pre_type=='MethodBank'||$pre_type==ORDER_STATUS_INCOM_READY||$pre_type==ORDER_STATUS_INCOM_COMPLETE){
												$delay_datetime=explode(" ",$order_detail_datas[$j][status_regdate]);
												$delay_date=explode("-",$delay_datetime[0]);
												$delay_time=explode(":",$delay_datetime[1]);

												if($delay_rule["ir_ic_yn"]=="Y" && $order_detail_datas[$j][ostatus] =='IR' && ($delay_rule["ir_ic_day"]!="" && @mktime($delay_time[0],$delay_time[1],$delay_time[2],$delay_date[1],$delay_date[2],$delay_date[0]) < (time()-(86400*$delay_rule["ir_ic_day"]))))
													$alarm_img_str="<img src='../images/icon/alarm_danger.gif'>";
												elseif($delay_rule["ic_dr_yn"]=="Y" && $order_detail_datas[$j][ostatus] =='IC' && ($delay_rule["ic_dr_day"]!="" && @mktime($delay_time[0],$delay_time[1],$delay_time[2],$delay_date[1],$delay_date[2],$delay_date[0]) < (time()-(86400*$delay_rule["ic_dr_day"]))))
													$alarm_img_str="<img src='../images/icon/alarm_danger.gif'>";
												else
													$alarm_img_str="";

												$Contents .= "<td align='center' ".($pre_type=='MethodBank' ? "rowspan='2'" : "style='background-color:#fff7da;'").">".$alarm_img_str."</td>";
											}

											if($admininfo[admin_level]==9){
												$Contents .= "<td align='center' ".($pre_type=='MethodBank' ? "rowspan='2'" : "style='background-color:#fff7da;'").">
                                                                <font color='#000000' >
                                                                <b>".getOrderFromName($order_detail_datas[$j][order_from])."</b><br>
                                                                ".GetDisplayDivision($order_detail_datas[$i]['mall_ix'], "text")."
                                                                </font>
                                                               </td>";
											}

											$Contents .= "
											<td  align='center'  ".($pre_type=='MethodBank' ? "rowspan='2'" : "style='background-color:#fff7da;'")."><font color='orange' ><b>".$order_detail_datas[$j][regdate]."</b></font></td>
											<td  align='center'  ".($pre_type=='MethodBank' ? "rowspan='2'" : "style='background-color:#fff7da;'").">
												<font color='blue' ><b>
													<span style='color:#007DB7;font-weight:bold;' >".$order_detail_datas[$j][oid]."</span></b>".($order_detail_datas[$j][delivery_box_no] ? "<b style='color:red;'>-".$order_detail_datas[$j][delivery_box_no]."</b>":"")."
												</font><br>
												<span class='helpcloud' help_width='55' help_height='15' help_html='주문서'>
													<img src='../images/icon/paper.gif' style='cursor:pointer' align='absmiddle' onclick=\"PopSWindow('../order/orders.read.php?oid=".$order_detail_datas[$j][oid]."&pid=".$order_detail_datas[$j][pid]."&mmode=pop',960,600)\"/>
													<a href=\"javascript:PopSWindow('../order/orders.edit.php?oid=".$order_detail_datas[$j][oid]."&pid=".$order_detail_datas[$j][pid]."&mmode=personalization&mem_ix=".$mem_ix."',960,600,'order_edit');\" ><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'></a>
												</span>
											</td>
											<td  align='center'  ".($pre_type=='MethodBank' ? "rowspan='2'" : "style='background-color:#fff7da;'")." nowrap>
												<font color='#000000' >".$b_mem_info." / ".$r_mem_info."</font>
											</td>
											<td  align='center'  ".($pre_type=='MethodBank' ? "rowspan='2'" : "style='background-color:#fff7da;'")." nowrap>
												<font color='#000000' ><b>".$method." / ".getPaymentAgentType($order_detail_datas[$j][payment_agent_type],'img')."</b></font>
											</td>
											<td  align='center' ".($pre_type=='MethodBank' ? "style='padding:3px;'" : "style='background-color:#fff7da;'")." >
												<font color='red' ><b>".getOrderStatus($order_detail_datas[$j][ostatus]).(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") && $order_detail_datas[$j][ostatus] =='IR' && $admininfo[admin_level]==9 ? " <img src='../images/".$admininfo["language"]."/btn_incom_complete.gif' align=absmiddle onclick=\"ChangeStatus('status_update', '".$order_detail_datas[$j][oid]."','', 'IR', '".ORDER_STATUS_INCOM_COMPLETE."')\" style='cursor:pointer;' >":"").($order_detail_datas[$j][ostatus] =='IC' && $order_detail_datas[$j][incom_date] ? "<br/>(<span  >".$order_detail_datas[$j][incom_date]."</span>)" : "")."</b></font>
											</td>
											<td  align='center'  ".($pre_type=='MethodBank' ? "style='padding:3px;'" : "style='background-color:#fff7da;'")." nowrap>
												<font color='#000000' ><b>".$receipt_type_str."</b></font>
											</td>";

											if($admininfo[admin_level]==9){
												$exchange_rate_payment_price = getOrderExchangeRatePaymentPrice($order_detail_datas[$j]);
												$Contents .= "<td  align='center'  ".($pre_type=='MethodBank' ? "" : "style='background-color:#fff7da;'")." nowrap><font color='red' ><b>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($order_detail_datas[$j][total_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]." ".($exchange_rate_payment_price > 0 ? "<br/>(".number_format($exchange_rate_payment_price,2).")" : "")."</b></font></td>";
											}

											$Contents .= "
											<td align='center'  ".($pre_type=='MethodBank' ? "style='padding:3px;'" : "style='background-color:#fff7da;'")." nowrap>";

										if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
												$Contents .= "<a href=\"../order/orders.edit.php?oid=".$order_detail_datas[$j][oid]."&pid=".$order_detail_datas[$j][pid]."&mmode=".$mmode."&mem_ix=".$mem_ix."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'></a>";
										}else{
												$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'></a> ";
										}

					$Contents .= "
											</td>
										</tr>";

					if($admininfo[admin_level]==9){
						if($pre_type=='refund' || $pre_type==ORDER_STATUS_REFUND_APPLY || $pre_type==ORDER_STATUS_REFUND_COMPLETE){

							if(substr_count($method_ ,ORDER_METHOD_BANK) > 0 || substr_count($method_ ,ORDER_METHOD_VBANK) > 0 || substr_count($method_ ,ORDER_METHOD_ASCROW) > 0){

								if($method_info['refund_bank_code']=="NO_REFUND_DATA"){
									$Contents .= "
											<tr>
												<td align='right' style='background-color:#fff7da;padding-bottom:3px;' colspan='10'>  환불정보가 없습니다.&nbsp;&nbsp;</td>
											</tr>";

								}else{
									$Contents .= "
												<tr>
													<td align='right' style='background-color:#fff7da;padding-bottom:3px;' colspan='10'>  환불입금계좌 : ".$arr_banks_name[$method_info['refund_bank_code']]." ".(empty($method_info['refund_bank_number']) ? $method_info['refund_bank_number1'] : $method_info['refund_bank_number'])." &nbsp;&nbsp; 환불입금자명 : ".$method_info['refund_bank_owner']."&nbsp;&nbsp;</td>
												</tr>";
								}
							}

						}else{
							if($method_bank_info!=""){

								$Contents .= "
												<tr>
													<td align='right' colspan='4' style='padding:5px;'>".$method_bank_info."&nbsp;&nbsp;</td>
												</tr>";
							}
						}
					}
					$Contents .= "
									</table>
								</td>
							</tr>";
				}

				if($pre_type!='MethodBank'){

					$delivery_method = getDeliveryMethod($order_detail_datas[$j][delivery_method]);			//배송방법 텍스트 리턴
					$delivery_pay_type = getDeliveryPayType($order_detail_datas[$j][delivery_pay_method]);
					$one_status = getOrderStatus($order_detail_datas[$j][status]).($order_detail_datas[$j][admin_message]!="" ? "<br><b class='grn'>".$order_detail_datas[$j][admin_message]."</b>":"")."<br>".str_replace(' ','<br/>',$order_detail_datas[$j][status_regdate]);

					$sql="select * from shop_order_detail_discount where od_ix='".$order_detail_datas[$j][od_ix]."' ";
					$slave_mdb->query($sql);
					if($slave_mdb->total){
						$dc_info = $slave_mdb->fetchall("object");
					}else{
						$dc_info = "";
					}

					$dc_coupon_info = getOrderDetailCouponDcInfo($dc_info);
					$dc_coupon_str=$dc_coupon_info["coupon_str"];
					$dc_coupon_width=$dc_coupon_info["coupon_width"];
					$dc_coupon_height=$dc_coupon_info["coupon_height"];

					$dc_etc_info = getOrderDetailEtcDcInfo($dc_info);
					$dc_etc_str=$dc_etc_info["etc_str"];
					$dc_etc_width=$dc_etc_info["etc_width"];
					$dc_etc_height=$dc_etc_info["etc_height"];

					$discount_info = $currency_display[$admin_config["currency_unit"]]["front"].number_format($order_detail_datas[$j][ptprice]-$order_detail_datas[$j][pt_dcprice]).$currency_display[$admin_config["currency_unit"]]["back"];

					if($dc_etc_str!=""){
						$discount_info.=" <label class='helpcloud' help_width='".$dc_etc_width."' help_height='".$dc_etc_height."' help_html='".$dc_etc_str."'><img src='../images/icon/q_icon.png' align=''></label>";
					}

					if($dc_coupon_str!=""){
						$discount_info.=" <label class='helpcloud' help_width='".$dc_coupon_width."' help_height='".$dc_coupon_height."' help_html='".$dc_coupon_str."'><img src='../images/".$admininfo[language]."/s_use_coupon.gif' align=''></label>";
					}

					$Contents .= "
									<tr>
										<td >
											<TABLE>
												<TR>";
							if($pre_type==ORDER_STATUS_INCOM_COMPLETE||$pre_type ==ORDER_STATUS_DEFERRED_PAYMENT||$pre_type =='sos_product'){
								$Contents .= "<td width='15px;'><input type=checkbox name='od_ix[]' id='od_ix' oid='".$order_detail_datas[$j][oid]."' set_group='".$order_detail_datas[$j][set_group]."' value='".$order_detail_datas[$j][od_ix]."'></td>";
							}

							$Contents .= "<TD align='center'>
								<!-- a  href='/shop/goods_view.php?id=".$order_detail_datas[$j][pid]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $order_detail_datas[$j][pid], 'm',$order_detail_datas[$j])."'  target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $order_detail_datas[$j][pid], 'm',$order_detail_datas[$j])."'  width=50 style='margin:5px;'></a -->
								<a  href='/shop/goods_view.php?id=".$order_detail_datas[$j][pid]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $order_detail_datas[$j][pid], 'list',$order_detail_datas[$j])."'  target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $order_detail_datas[$j][pid], 'slist',$order_detail_datas[$j])."'  width=50 style='margin:5px;'></a><br/>";

								if($order_detail_datas[$j][product_type]=='21'||$order_detail_datas[$j][product_type]=='31'){
								$Contents .= "<label class='helpcloud' help_width='190' help_height='15' help_html='".($order_detail_datas[$j][product_type]=='21' ? "서브스크립션 커머스(배송상품)" : "로컬딜리버리 커머스(배송상품)")."'><img src='../images/".$admininfo[language]."/s_product_type_".$order_detail_datas[$j][product_type].".gif' align='absmiddle' ></label> ";
								}
								if($order_detail_datas[$j][company_id]==$HEAD_OFFICE_CODE){
									$Contents .= "<label class='helpcloud' help_width='70' help_height='15' help_html='본사상품'><img src='../images/".$admininfo[language]."/s_admin_product.gif' align='absmiddle' ></label> ";
								}
								if($order_detail_datas[$j][stock_use_yn]=='Y'){
								$Contents .= "<label class='helpcloud' help_width='140' help_height='15' help_html='(WMS)재고관리 상품'><img src='../images/".$admininfo[language]."/s_inventory_use.gif' align='absmiddle' ></label>";
								}

								if($bcompany_id != $order_detail_datas[$j][company_id]){
									$seller_info_str= GET_SELLER_INFO($order_detail_datas[$j][company_id]);
								}

								$Contents .= "
													</TD>
													<td width='5'></td>
													<TD style='line-height:140%'>";

								$Contents .= "<span style='color:#007DB7;font-weight:bold;' >".$order_detail_datas[$j][od_ix]." ".($order_detail_datas[$j][co_oid] ? "(".$order_detail_datas[$j][co_oid].")" : "")."</span><br/>";

								if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
									//$Contents .= "<b style='cursor:pointer' class='helpcloud' help_width='230' help_html='".$seller_info_str."'>".($order_detail_datas[$j][company_name] ? $order_detail_datas[$j][company_name]:"-")."</b> <img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PopSWindow('../seller/seller_company.php?company_id=".$order_detail_datas[$j][company_id]."&mmode=pop',960,600,'brand');\"  style='cursor:pointer;'><br>";
								}
								if($order_detail_datas[$j][co_pid] != "" && $order_detail_datas[$j][co_pid] != "0000000000"){
									$Contents .= "<img src='../images/".$admininfo["language"]."/ico_wholesale.gif' border=0 align=absmiddle  title='도매주문'>  ";
								}

								$Contents .= "<a href='../".$folder_name."/goods_input.php?id=".$order_detail_datas[$j][pid]."' target=_blank />";

								if($order_detail_datas[$j][product_type]=='99'||$order_detail_datas[$j][product_type]=='21'||$order_detail_datas[$j][product_type]=='31'){
									$Contents .= "<b class='".($order_detail_datas[$j][product_type]=='99' ? "red" : "blue")."' >[".$order_detail_datas[$j][company_name]."] ".$order_detail_datas[$j][pname]."</b><br/><strong>".$order_detail_datas[$j][set_name]."<br /></strong>".$order_detail_datas[$j][sub_pname];
								}else{
									$Contents .= "[".$order_detail_datas[$j][company_name]."] ".$order_detail_datas[$j][pname];
								}

								$Contents .= "</a>";

								if(strip_tags($order_detail_datas[$j][option_text])){
									$Contents .= "<br/> ▶ ".strip_tags($order_detail_datas[$j][option_text]);
								}

								$Contents .="
													</TD>
												</TR>
											</TABLE>
										</td>
										<td class='' align=center>
											".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($order_detail_datas[$j][listprice])."".$currency_display[$admin_config["currency_unit"]]["back"]."<br/>
											/".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($order_detail_datas[$j][psprice])."".$currency_display[$admin_config["currency_unit"]]["back"]."
										</td>
										<td class='' align=center>".$discount_info."</td>
										<td class='' align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($order_detail_datas[$j][pt_dcprice])."".$currency_display[$admin_config["currency_unit"]]["back"]." (".number_format($order_detail_datas[$j][pcnt])."개)</td>";


								//배송비 분리 시작 2014-05-21 이학봉
                                if($b_ode_ix != $order_detail_datas[$j][ode_ix]){

                                    $sql = "SELECT 
                                                        COUNT(DISTINCT(od.od_ix)) AS com_cnt
                                                    FROM 
                                                        ".TBL_SHOP_ORDER." o,
                                                        ".TBL_SHOP_ORDER_DETAIL." od
                                                    where 
                                                        o.oid = od.oid 
                                                        and o.oid = '".$order_detail_datas[$j][oid]."' 
                                                        and od.ode_ix='".$order_detail_datas[$j][ode_ix]."'
                                                        $addWhere 
                                                        ";

                                    $slave_mdb->query($sql);//$slave_mdb는 상단에서 선언
                                    $slave_mdb->fetch();
                                    $com_cnt=$slave_mdb->dt["com_cnt"];

                                    $Contents .="<td class='' align=center style='line-height:140%;' rowspan='".$com_cnt."'>
                                                    ".$currency_display[$admin_config["currency_unit"]]["front"].number_format($order_detail_datas[$j][delivery_totalprice]).$currency_display[$admin_config["currency_unit"]]["back"]."
                                                    </td>";
                                }
								//배송비 분리 끝 2014-05-21 이학봉

					$Contents .="	<td class='' align=center>".number_format($order_detail_datas[$j][reserve])."P</td>";
					if($pre_type!='refund' && $pre_type!=ORDER_STATUS_REFUND_APPLY && $pre_type!=ORDER_STATUS_REFUND_COMPLETE){
						$Contents .= "
										<td class='' align=center>
											".number_format($order_detail_datas[$j][stock])."/-".number_format($order_detail_datas[$j][sell_ing_cnt])."/".($order_detail_datas[$j][stock]-$order_detail_datas[$j][sell_ing_cnt] < 0 ? "<b class='red'>".number_format($order_detail_datas[$j][stock]-$order_detail_datas[$j][sell_ing_cnt])."</b>" : "-0")."";

											if($order_detail_datas[$j][stock_use_yn]=='Y'){
												$Contents .="<br/>";

												if($order_detail_datas[$j][real_lack_stock] < 0){
													$Contents .="<b class='red'>".$order_detail_datas[$j][real_lack_stock]."</b> <img src='../images/icon/alarm_danger.gif' align='absmiddle'>";
												}else{
													$Contents .="<b class='grn'>".$order_detail_datas[$j][real_lack_stock]."</b>";
												}
											}
										$Contents .="
										</td>";
					}
					$Contents .= "
										<td class='point' align='center'>".$one_status."</td>";
					if($admininfo[admin_level]==9){

						$Contents .= "<td class='point' align='center'>".getOrderStatus($order_detail_datas[$j][delivery_status])."</td>";
						if($pre_type=='refund'||$pre_type==ORDER_STATUS_REFUND_APPLY||$pre_type==ORDER_STATUS_REFUND_COMPLETE){
							$Contents .= "<td class='point' align='center'>
								".getOrderStatus($order_detail_datas[$j][refund_status])."";

								if($order_detail_datas[$j][refund_status]=='FC'){
									$Contents .= "<br>".$order_detail_datas[$j][fc_date];
								}
								if($order_detail_datas[$j][refund_status]=='FA'){
									$Contents .= "<br/><img src='../images/".$admininfo["language"]."/btn_part_cancel.gif' align=absmiddle onclick=\"ShowModalWindow('../order/refund_price_give.php?oid=".$order_detail_datas[$j][oid]."',1000,1000,'refund_price_give');\" style='cursor:pointer;' />";
								}
								if($admininfo['department'] == 8 || $admininfo['department'] == 2 || $admininfo['department'] == 9){
                                    if($order_detail_datas[$j][refund_status]!='FC'){
                                        $Contents .= "<br/><img src='../images/".$admininfo["language"]."/btn_buy_confirm.jpg' align=absmiddle onclick=\"btnConfirmation('".$order_detail_datas[$j][oid]."','".$order_detail_datas[$j][od_ix]."');\" style='cursor:pointer;' />";
                                        //$Contents .= "<br/><img src='../images/".$admininfo["language"]."/btn_refund_complete.jpg' align=absmiddle onclick=\"btnComplete('".$order_detail_datas[$j][oid]."','".$order_detail_datas[$j][od_ix]."');\" style='cursor:pointer;' />";
                                    }
								}
							$Contents .= "
							</td>";
						}
					}

					if($pre_type==ORDER_STATUS_INCOM_COMPLETE){
						if($order_detail_datas[$j][due_date]=="0000-00-00" || $order_detail_datas[$j][due_date]==""){
							$Contents .= "<td align='center' >당일</td>";
						}else{
							$Contents .= "<td align='center' >".$order_detail_datas[$j][due_date]."</td>";
						}
					}

					$Contents .= "
									</tr>";
				}

				$b_oid = $order_detail_datas[$j][oid];
				$bcompany_id = $order_detail_datas[$j][company_id];
				$bproduct_id = $order_detail_datas[$j][pid];
				$bset_group = $order_detail_datas[$j][set_group];
				$b_product_type = $order_detail_datas[$j][product_type];
				$b_factory_info_addr_ix  = $order_detail_datas[$j][factory_info_addr_ix];
				$b_delivery_type = $order_detail_datas[$j][delivery_type];
				$b_ode_ix = $order_detail_datas[$j][ode_ix];
				$bori_ode_ix = $order_detail_datas[$j][ode_ix];
			}
            $admin_config["currency_unit"] = $origin_currency_unit;
		}
	}else{

		$Contents .= "<tr height=50><td colspan=".$_colspan." align=center>조회된 결과가 없습니다.</td></tr>";
	}

	$Contents .= "
		</table>";

}elseif($pre_type==ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE || $pre_type==ORDER_STATUS_CANCEL_COMPLETE || $pre_type==ORDER_STATUS_SOLDOUT_CANCEL
    || $pre_type==ORDER_STATUS_CANCEL_APPLY){

	$Contents .= "
	  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
		<tr height='25' >";
		if($pre_type == ORDER_STATUS_CANCEL_APPLY){
			$Contents .= "<td class='s_td' align='center' width='30px'><input type=checkbox  name='all_fix2' onclick='fixAll2(document.listform)'></td>
			<td class='m_td' align='center' width='30px'>지연알림</td>";
		}
	$Contents .= "
			<td width='16%' align='center' class='m_td'><b>주문일자/주문번호".($admininfo[admin_level]==9 ? "/판매처" : "" )."</b></td>
			<td width='10%' align='center'  class='m_td' nowrap><b>주문자명/수취인</b></td>
			<td width='*' align='center' class='m_td' nowrap><b>주문상세번호/상품명/옵션</b></td>
			<td width='8%' align='center' class='m_td' ><b>정가/<br/>판매가(할인가)</b></td>
			<td width='8%' align='center' class='m_td' nowrap><b>할인금액</b></td>
			<td width='8%' align='center' class='m_td' ><b>결제금액(수량)</b></td>";
			if($pre_type==ORDER_STATUS_CANCEL_COMPLETE){
				$Contents .= "<td width='7%' align='center' class='m_td' nowrap><b>환불상태</b></td>";
			}
	$Contents .= "
			<td width='7%' align='center' class='m_td' nowrap><b>주문처리상태</b></td>
			<td width='7%' align='center' class='m_td' nowrap><b>출고처리상태</b></td>
			<td width='10%' align='center' class='e_td' nowrap><b>취소사유</b></td>";
			if($pre_type==ORDER_STATUS_CANCEL_APPLY){
				/*
				$Contents .= "<td width='6%' align='center' class='m_td' nowrap><b>관리</b></td>";
				*/
			}
	$Contents .= "
		</tr>
	  ";

	$order_datas = $master_db->fetchall();
	if(count($order_datas)){

		$addWhere = " AND od.status !='SR' ";

		if(is_array($type)){
			if($type_str != ""){
				$addWhere .= "and od.status in ($type_str) ";
			}
		}else{
			if($type){
				$addWhere .= "and od.status = '$type' ";
			}
		}

		if($_COOKIE["order_view_type"] == 1){
			$addWhere .= str_replace("WHERE","and",$where);
		}

		if($search_type && $search_text){
			if($search_type == "od.pname" || $search_type == "od.invoice_no" || $search_type == "od.option_text"){
				$addWhere .= "and $search_type LIKE '%".trim($search_text)."%' ";
			}
		}

		if($admininfo[admin_level] == 9){
			if($admininfo[mem_type] == "MD"){
				$addWhere .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}
		}else if($admininfo[admin_level] == 8){
			$addWhere .= " and od.company_id ='".$admininfo[company_id]."' ";
		}

		if(is_array($p_admin) && count($p_admin) == 1){
			if($p_admin[0]=="A"){
				$addWhere .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
			}elseif($p_admin[0]=="S"){
				$addWhere .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
			}
		}else{
			if($p_admin=="A"){
				$addWhere .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
			}elseif($p_admin=="S"){
				$addWhere .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
			}
		}

		if($stock_use_yn != ""){
			$addWhere .= "and od.stock_use_yn = '".$stock_use_yn."'";
		}


		for ($i = 0; $i < count($order_datas); $i++)
		{
			//$slave_db->total
			//$slave_db->fetch($i);

			$sql = "SELECT 
			
			o.oid, o.delivery_box_no, o.payment_price, o.payment_agent_type, o.user_code as user_id, o.buserid, o.bmobile, o.bname, o.gp_ix,
			o.mem_group, o.status as ostatus, o.order_date as regdate, o.total_price,

			od.od_ix, od.product_type, od.pid, od.brand_name, od.pname, od.set_group, od.set_name, od.sub_pname, od.option_text, od.coprice,od.listprice,od.psprice, od.pcnt, od.ptprice, od.pt_dcprice,
			od.delivery_status, od.stock_use_yn, od.order_from, od.pcode, od.admin_message, od.status,
			od.company_name, od.company_id, od.reserve, od.co_pid, date_format(od.ic_date,'%Y-%m-%d') as incom_date, od.return_product_state, od.refund_status,

			(select regdate from shop_order_status where oid=od.oid and od_ix=od.od_ix and status=od.status order by regdate desc limit 1) as status_regdate

			FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od 
			where o.oid = od.oid and o.oid = '".$order_datas[$i][oid]."' 
			$addWhere
			ORDER BY od.company_id asc , status asc ";

			$slave_db->query($sql);
			$order_detail_datas = $slave_db->fetchall();
			$od_count = count($order_detail_datas);//$ddb->total;

			$bcompany_id = '';
			for($j=0;$j < count($order_detail_datas);$j++){

				//$ddb->fetch($j);

				$one_status = getOrderStatus($order_detail_datas[$j][status]).($order_detail_datas[$j][admin_message]!="" ? "<br><b class='grn'>".$order_detail_datas[$j][admin_message]."</b>":"")."<br>".str_replace(' ','<br/>',$order_detail_datas[$j][status_regdate]);

				$sql="SELECT 
						case when c_type='B' then '구매자' when c_type='S' then '셀러' when c_type='M' then 'MD' else c_type end as c_type_text,
						DATE_FORMAT(os.regdate,'%Y-%m-%d') as apply_date,
						os.status_message
					FROM 
						shop_order_status os 
					WHERE
						os.oid='".$order_detail_datas[$j][oid]."'
					and
						os.od_ix = '".$order_detail_datas[$j][od_ix]."'
					and 
						os.status='".$pre_type."'
					ORDER BY os.regdate DESC LIMIT 1 ";
				$slave_db->query($sql);
				$slave_db->fetch();
				$apply_date=$slave_db->dt["apply_date"];
				$status_message="<b>".$slave_db->dt["c_type_text"]."</b>:".$slave_db->dt["status_message"];

				$sql="select * from shop_order_detail_discount where od_ix='".$order_detail_datas[$j][od_ix]."' ";
				$slave_db->query($sql);
				if($slave_db->total){
					$dc_info = $slave_db->fetchall("object");
				}else{
					$dc_info = "";
				}

				$dc_coupon_info = getOrderDetailCouponDcInfo($dc_info);
				$dc_coupon_str=$dc_coupon_info["coupon_str"];
				$dc_coupon_width=$dc_coupon_info["coupon_width"];
				$dc_coupon_height=$dc_coupon_info["coupon_height"];

				$dc_etc_info = getOrderDetailEtcDcInfo($dc_info);
				$dc_etc_str=$dc_etc_info["etc_str"];
				$dc_etc_width=$dc_etc_info["etc_width"];
				$dc_etc_height=$dc_etc_info["etc_height"];

				$discount_info = $currency_display[$admin_config["currency_unit"]]["front"].number_format($order_detail_datas[$j][ptprice]-$order_detail_datas[$j][pt_dcprice]).$currency_display[$admin_config["currency_unit"]]["back"];

				if($dc_etc_str!=""){
					$discount_info.=" <label class='helpcloud' help_width='".$dc_etc_width."' help_height='".$dc_etc_height."' help_html='".$dc_etc_str."'><img src='../images/icon/q_icon.png' align=''></label>";
				}

				if($dc_coupon_str!=""){
					$discount_info.=" <label class='helpcloud' help_width='".$dc_coupon_width."' help_height='".$dc_coupon_height."' help_html='".$dc_coupon_str."'><img src='../images/".$admininfo[language]."/s_use_coupon.gif' align=''></label>";
				}

				$Contents .= "<tr height=28 >";

				if($pre_type == ORDER_STATUS_CANCEL_APPLY){
					$Contents .= "<td class='list_box_td' align='center'><input type=checkbox name='od_ix[]' id='od_ix' oid='".$order_detail_datas[$j][oid]."' set_group='".$order_detail_datas[$j][set_group]."' value='".$order_detail_datas[$j][od_ix]."' ></td>";

					$delay_datetime=explode(" ",$order_detail_datas[$j][status_regdate]);
					$delay_date=explode("-",$delay_datetime[0]);
					$delay_time=explode(":",$delay_datetime[1]);

					if($delay_rule["ca_cc_yn"]=="Y"&&($delay_rule["ca_cc_day"]!="" && @mktime($delay_time[0],$delay_time[1],$delay_time[2],$delay_date[1],$delay_date[2],$delay_date[0]) < (time()-(86400*$delay_rule["ca_cc_day"]))))
						$alarm_img_str="<img src='../images/icon/alarm_danger.gif'>";
					else
						$alarm_img_str="";

					$Contents .= "<td class='list_box_td' align='center'>".$alarm_img_str."</td>";
				}

				if($order_detail_datas[$j][oid] != $b_oid){

					$u_etc_info=get_order_user_info($order_detail_datas[$j][user_id]);

					$b_mem_info = "<b style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='주문자 <br/>ID/성명 : ".$order_detail_datas[$j][buserid]."/".wel_masking_seLen($order_detail_datas[$j][bname], 1, 1)."<br/>핸드폰 : ".$order_detail_datas[$j][bmobile]." <br/>회원그룹 : ".$order_detail_datas[$j][mem_group]." <br/>최근로그인 : ".$u_etc_info["user_last"]." <br/>최근주문(30일) : ".$u_etc_info["user_order_cnt"]."건' />".Black_list_check($order_detail_datas[$j][user_id],($order_detail_datas[$j][gp_ix]=='2' ? "<b class='red'>VIP</b>" : "").wel_masking_seLen($order_detail_datas[$j][bname], 1, 1).( $order_detail_datas[$j][buserid] ? "(<span class='small'>".$order_detail_datas[$j][buserid]."</span>)" : "(<span class='small'>비회원</span>)"))."</b> <br/> ".($_SESSION["admininfo"]["admin_level"] > 8 && $order_detail_datas[$j][user_id] ? "<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PopSWindow2('/admin/member/member_cti.php?mmode=pop&code=".$order_detail_datas[$j][user_id]."&mmode=pop',1280,800,'member_view')\"  style='cursor:pointer;'>" : "");

					$recipient_info=getOrderRecipientInfo($order_detail_datas[$j]);
					$recipient_=$recipient_info["recipient"];
					$recipient_str=$recipient_info["recipient_str"];
					$recipient_width=$recipient_info["recipient_width"];
					$recipient_height=$recipient_info["recipient_height"];

					$r_mem_info= "<b style='cursor:pointer' class='helpcloud' help_width='".$recipient_width."' help_height='".$recipient_height."' help_html='".$recipient_str."' />".wel_masking_seLen($recipient_, 1, 1)."</b>";

					$Contents .= "
					<td class='list_box_td point' style='line-height:140%' align=center rowspan='".$od_count."'>
						".$order_detail_datas[$j][regdate]."<br>
						<font color='blue' ><b>
							<span style='color:#007DB7;font-weight:bold;' >".$order_detail_datas[$j][oid]."</span></b>".($order_detail_datas[$j][delivery_box_no] ? "<b style='color:red;'>-".$order_detail_datas[$j][delivery_box_no]."</b>":"")."
						</font><br>
						<span class='helpcloud' help_width='55' help_height='15' help_html='주문서'>
							<img src='../images/icon/paper.gif' style='cursor:pointer' align='absmiddle' onclick=\"PopSWindow('../order/orders.read.php?oid=".$order_detail_datas[$j][oid]."&pid=".$order_detail_datas[$j][pid]."&mmode=pop',960,600)\"/>
							<a href=\"javascript:PopSWindow('../order/orders.edit.php?oid=".$order_detail_datas[$j][oid]."&pid=".$order_detail_datas[$j][pid]."&mmode=personalization&mem_ix=".$mem_ix."',960,600,'order_edit');\" ><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'></a>
						</span>
						".($admininfo[admin_level]==9 ? "<br/>".getOrderFromName($order_detail_datas[$j][order_from]) : "")."
					</td>
					 <td style='line-height:140%' align=center class='list_box_td' rowspan='".$od_count."'>
						".$b_mem_info." / ".$r_mem_info."
					 </td>";
				}

				$Contents .= "<td class='list_box_td' style='padding-left:10px'>
								<TABLE style='text-align:left;'>
									<TR>
										<TD align='center'>
										<!-- a  href='/shop/goods_view.php?id=".$order_detail_datas[$j][pid]."' target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $order_detail_datas[$j][pid], "m", $order_detail_datas[$j])."'  width=50 style='margin:5px;'></a -->
										<a  href='/shop/goods_view.php?id=".$order_detail_datas[$j][pid]."' target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $order_detail_datas[$j][pid], "slist", $order_detail_datas[$j])."'  width=50 style='margin:5px;'></a><br/>";

										if($order_detail_datas[$j][product_type]=='21'||$order_detail_datas[$j][product_type]=='31'){
											$Contents .= "<label class='helpcloud' help_width='190' help_height='15' help_html='".($order_detail_datas[$j][product_type]=='21' ? "서브스크립션 커머스(배송상품)" : "로컬딜리버리 커머스(배송상품)")."'><img src='../images/".$admininfo[language]."/s_product_type_".$order_detail_datas[$j][product_type].".gif' align='absmiddle' ></label> ";
										}
										if($order_detail_datas[$j][company_id]==$HEAD_OFFICE_CODE){
											$Contents .= "<label class='helpcloud' help_width='70' help_height='15' help_html='본사상품'><img src='../images/".$admininfo[language]."/s_admin_product.gif' align='absmiddle' ></label> ";
										}
										if($order_detail_datas[$j][stock_use_yn]=='Y'){
										$Contents .= "<label class='helpcloud' help_width='140' help_height='15' help_html='(WMS)재고관리 상품'><img src='../images/".$admininfo[language]."/s_inventory_use.gif' align='absmiddle' ></label>";
										}

					$Contents .= "
										</TD>
										<td width='5'>
										</td>
										<TD style='line-height:140%'>";

				$Contents .= "<span style='color:#007DB7;font-weight:bold;' >".$order_detail_datas[$j][od_ix]."</span><br/>";

				if($bcompany_id != $order_detail_datas[$j][company_id]){
					$seller_info_str= GET_SELLER_INFO($order_detail_datas[$j][company_id]);
				}

				if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
					$Contents .= "<b style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='".$seller_info_str."'>".($order_detail_datas[$j][company_name] ? $order_detail_datas[$j][company_name]:"-")."</b> <img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PopSWindow('../seller/seller_company.php?company_id=".$order_detail_datas[$j][company_id]."&mmode=pop',960,600,'brand');\"  style='cursor:pointer;'><br>";
				}

				$Contents .= "<a  href='../".$folder_name."/goods_input.php?id=".$order_detail_datas[$j][pid]."' target=_blank />";

				if($order_detail_datas[$j][product_type]=='99'||$order_detail_datas[$j][product_type]=='21'||$order_detail_datas[$j][product_type]=='31'){
					$Contents .= "<b class='".($order_detail_datas[$j][product_type]=='99' ? "red" : "blue")."' >[".$order_detail_datas[$j][company_name]."] ".$order_detail_datas[$j][pname]."</b><br/><strong>".$order_detail_datas[$j][set_name]."<br /></strong>".$order_detail_datas[$j][sub_pname];
				}else{
					$Contents .= "[".$order_detail_datas[$j][company_name]."] ".$order_detail_datas[$j][pname];
				}

				$Contents .= "</a>";

				if(strip_tags($order_detail_datas[$j][option_text])){
					$Contents .= "<br/> ▶ ".strip_tags($order_detail_datas[$j][option_text]);
				}

				$Contents .="
										</TD>
									</TR>
								</TABLE>
							</td>
							<td class='list_box_td' align=center nowrap>
								".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($order_detail_datas[$j][listprice])."".$currency_display[$admin_config["currency_unit"]]["back"]."<br/>
								/".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($order_detail_datas[$j][psprice])."".$currency_display[$admin_config["currency_unit"]]["back"]."
							</td>
							<td class='list_box_td ' align=center>".$discount_info."</td>
							<td class='list_box_td ' align=center nowrap>
								".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($order_detail_datas[$j][pt_dcprice])."".$currency_display[$admin_config["currency_unit"]]["back"]." (".number_format($order_detail_datas[$j][pcnt])."개)
							</td>";

		if($pre_type==ORDER_STATUS_CANCEL_COMPLETE){
			$Contents .="<td class='list_box_td point' align='center' >".getOrderStatus($order_detail_datas[$j][refund_status])."</td>";
		}
					$Contents .="
					<td class='list_box_td point' align='center' >".$one_status."</td>
					<td class='list_box_td point' align='center' >".getOrderStatus($order_detail_datas[$j][delivery_status])."<br>".str_replace(" ","<br/>",$order_detail_datas[$j][delivery_status_regdate])."<br><font color='red'><b>".$order_detail_datas[$j][dps_status]."</b></font></td>
					<td class='list_box_td' align=center>".$status_message."</td>";
	$Contents .= "</tr>";

				$b_oid = $order_detail_datas[$j][oid];
				$bcompany_id = $order_detail_datas[$j][company_id];
			}

		}
	}else{

		if($pre_type == ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE){
			$no_data_colspan=9;
		}elseif($pre_type == ORDER_STATUS_CANCEL_COMPLETE || $pre_type == ORDER_STATUS_SOLDOUT_CANCEL){
			$no_data_colspan=9;
		}elseif($pre_type == ORDER_STATUS_CANCEL_APPLY){
			$no_data_colspan=11;
		}

		$Contents .= "<tr height=50><td colspan=".$no_data_colspan." align=center>조회된 결과가 없습니다.</td></tr>";

	}
	$Contents .= "
	  </table>";



}
?>