<?

//shop_order  ptprice 를 select 하면서 오류가 뜸.. 바비맘은 total_price 를 함. shop_order 테이블에는 ptprice 필드가 존재하지 않음. 그래서 바비맘에 소스로 덮었음. 2013-04-24 이학봉

$script_time[start] = time();
include("../class/layout.class");
include("$DOCUMENT_ROOT/class/sms.class");
$script_time[start] = time();
//print_r($_SESSION);

$db = new Database;
$mdb = new Database;
$sms_design = new SMS;
//print_r($admininfo);


//print_r($bbs_datas);


$Script = "
<style>



/*메인*/
#contents_frame h4{padding:0 0 7px 1px}
#contents_frame h4 span{background:url('/admin/images/dot_org.gif') left center no-repeat;padding:0 0 0 16px;}
#contents_frame

/***********************************************************/
.show_main_title{position:relative;padding-top:14px;}
.show_main_title em{position:absolute;right:0;top:17px;font-style:normal;font-weight:normal;font-size:11px;color:#898989;}
.show_main_BG{background:url('/admin/v3/images/".$_SESSION["admininfo"]['language']."/show_main_BG02.gif') no-repeat;float:left; width:914px; height:282px;position:relative;}
.show_main_BG p{text-align:center;margin:0;padding:0;font-weight:bold;}
.show_main_BG .show_main_text01{width:104px;position:absolute;left:20px;top:48px;color:#000;}
.show_main_BG .show_main_text02{width:104px;position:absolute;left:149px;top:48px;color:#000;}
.show_main_BG .show_main_text03{width:104px;position:absolute;left:266px;top:48px;color:#000;}
.show_main_BG .show_main_text04{width:40px;position:absolute;left:408px;top:51px;color:#000;}
.show_main_BG .show_main_text05{width:40px;position:absolute;left:492px;top:51px;color:#000;}
.show_main_BG .show_main_text06{width:104px;position:absolute;left:555px;top:48px;}
.show_main_BG .show_main_text07{width:104px;position:absolute;left:675px;top:48px;}
.show_main_BG .show_main_text08{width:104px;position:absolute;left:794px;top:48px;}
.show_main_BG .show_main_text09{width:86px;position:absolute;left:31px;top:153px;}
.show_main_BG .show_main_text10{width:86px;position:absolute;left:211px;top:167px;}
.show_main_BG .show_main_text11{width:86px;position:absolute;left:501px;top:119px;}
.show_main_BG .show_main_text12{width:86px;position:absolute;left:693px;top:171px;}
.show_main_BG .show_main_text13{width:86px;position:absolute;left:439px;top:238px;}
.show_main_BG .show_main_text14{width:86px;position:absolute;left:542px;top:238px;}
.show_main_BG .show_main_text15{width:86px;position:absolute;left:634px;top:238px;}

.show_main_text01, .show_main_text02, .show_main_text03{color:#000;}
.show_main_text06, .show_main_text07, .show_main_text08{color:#fff;}
.show_main_BG .show_main_text04 a, .show_main_BG .show_main_text05 a{font-size:12px;}
.show_main_BG p a{font-size:14px;}
.show_main_BG .show_main_text09 a, .show_main_BG .show_main_text15 a{color:#f00e0e;}
.show_main_BG .show_main_text06 a, .show_main_BG .show_main_text07 a, .show_main_BG .show_main_text08 a{color:#fff;}

/**************************/
.second_notice{float:left;}
.second_notice .banners01{margin-top:16px;}
.second{width:100%;min-width:1330px;padding:10px 0 0 0;}
.second .notice_mall{position:relative;width:552px;}
.second .notice_mall .notice_main{height:212px;border:1px solid #c5c5c5;border-top:2px solid #959595;}
.second .notice_mall th{height:30px;border-bottom:1px dotted #a8a8a8;font-weight:normal;}
.second .notice_mall td.td_list{height:26px;text-align:center;}
.second .notice_mall p{position:absolute;top:5px;right:0;margin:0;}
/*******************************/




/*******************************************************************/
.BG_third_table{background:#f7f7f7;}
.third{float:left;width:996px;}
.bottom_right{width:350px;float:left;margin-left:12px;}
.bottom_right dl{padding:0;margin:0;}
.bottom_right01{position:relative;}
.bottom_right01 dl{padding:15px 0 0 0;}
.bottom_right01 p{position:absolute;right:0;top:18px;margin:0;}
.bottom_right01 dl dt{padding-bottom:7px;border-bottom:2px solid #959595;}
.bottom_right01 dl dt span{padding-left:16px;background:url('/admin/images/dot_org.gif') left center no-repeat;}
.bottom_right01 dl dd{margin:0;border:1px solid #c5c5c5;border-top:0 none;line-height:100%;}
.bottom_right01 table tr td{height:27px;padding:0 12px 0 17px;border-bottom:1px solid #c5c5c5;}
.bottom_right01 table .last_tr td{border-bottom:0 none;} 
.bottom_right01 td span{font-weight:bold;color:#f00e0e;}
.bottom_right02{}
.bottom_right02 .bottom_right02_2{margin-top:22px;}
.bottom_right02 div{position:relative;}
.bottom_right02 .more_btn_img{position:absolute;right:0;top:3px;}
.bottom_right02 table{text-align:center;border-left:1px solid #cfceca;}
.bottom_right02 table td span{font-weight:bold;color:#636363;}
.bottom_right02 .red_font{color:red;}
.bottom_right02 .bottom_right02_1 tr th{height:31px;background:#f0f0f0;border-top:2px solid #959595;border-right:1px solid #cfceca;font-weight:normal;}
.bottom_right02 .bottom_right02_1 tr td{height:30px;border-right:1px solid #cfceca;border-bottom:1px solid #cfceca;}
.left_field{float:left;width:914px;}
.right_field{width:289px;float:left;margin-left:12px;}
.right_field .bottom_right02_1 tr th{height:31px;background:#f8f1de;border-top:2px solid #959595;border-right:1px solid #cfceca;font-weight:normal;}
.right_field .bottom_right02_1 tr td{height:30px;border-right:1px solid #cfceca;border-bottom:1px solid #cfceca;}
.right_field .sales_stats{margin:16px 0 0 0;border:1px solid #c5c5c5;}
.right_field .sales_stats dt{position:relative;padding:10px 0 8px 9px;background:#f8f1de;}
.right_field .sales_stats dt span{position:absolute;right:12px;top:11px;font-size:11px;}
.right_field .sales_stats dd{margin:0;}
.banners02{margin-top:16px;border:1px solid #c5c5c5;border-bottom:0 none;}
.banners02 p{line-height:0;font-size:0;border-bottom:1px solid #c5c5c5;}
.banners03{margin-top:16px;line-height:0;font-size:0;border:1px solid #c5c5c5;}

</style>
<Script language='javascript'>
function showTabContents(vid, tab_id){
	var area = new Array('recent_product','recent_product_shortage','recent_product_soldout');
	var tab = new Array('tab_01','tab_02','tab_03');

	for(var i=0; i<area.length; ++i){
		if(area[i]==vid){
			document.getElementById(vid).style.display = 'block';
			document.getElementById(tab_id).className = 'on';
		}else{
			document.getElementById(area[i]).style.display = 'none';
			document.getElementById(tab[i]).className = '';
		}
	}
}

function useAfterDelete(uf_ix){
	if(confirm(language_data['seller/index.php']['A'][language])){//사용후기를 정말로 삭제하시겠습니까?
		window.frames['act'].location.href='../marketting/useafter.act.php?act=delete&uf_ix='+uf_ix
	}
}
function checkAllkrDomain(obj){
	var frm = document.domain_search;
	for(var i=0;i<frm.kdomain.length;i++){
		if(obj.checked){
			frm.kdomain[i].checked = true;
		}else{
			frm.kdomain[i].checked = false;
		}
	}
}

function checkAllcomDomain(obj){
	var frm = document.domain_search;
	for(var i=0;i<frm.edomain.length;i++){
		if(obj.checked){
			frm.edomain[i].checked = true;
		}else{
			frm.edomain[i].checked = false;
		}
	}
}

$(document).ready(function (){
	//PoPWindow('../qna_pop.php',600,400,'add_brand_category');
});

</Script>";


//$script_time[sms_start] = time();
$sms_cnt = $sms_design->getSMSAbleCount($admininfo);
//$script_time[sms_end] = time();

$Contents01 = "

<!-----------------------------------------------------------------------------------------------------------여기부터------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>

<div class='second'>
	<div class='left_field'>
		<div class='second_notice'>
			<div class='notice_mall'>
				<h4><span><img src='/admin/v3/images/".$_SESSION["admininfo"]['language']."/notice_mall.png' alt='공지사항' align='absmiddle' /></span></h4>
				<div class='notice_main'>
					<table cellpadding='0' border='0' cellspacing='0' width='100%'>
						<col width='90' />
						<col width='70' />
						<col width='*' />
						<col width='80' />
						<tr>
							<th>공지일자</th>
							<th>공지분류</th>
							<th>제목</th>
							<th>조회수</th>
						</tr>
						<tr><td colspan='4' height='10'></td></tr>
							";
								$bbs_notice_fetch=fetch_bbs("bbs_b2b_notice",6);
								$bbs_notice_cnt=count($bbs_notice_fetch);

								for($i=0;$i<$bbs_notice_cnt;$i++) {
									$Contents01 .= "
									<tr>
										<td class='td_list'>
											<span class='date'>".str_replace("-",".",substr($bbs_notice_fetch[$i]["regdate"],0,10))."</span>
										</td>
										<td class='td_list'>
											".get_bbs_div($bbs_notice_fetch[$i]["bbs_div"])."
										</td>
										<td class='td_list' style='padding-left:10px;text-align:left;'>
											<a href='/admin/seller/bbs.php?mode=read&board=b2b_notice&bbs_ix=".$bbs_notice_fetch[$i]["bbs_ix"]."'>".cut_str($bbs_notice_fetch[$i]["bbs_subject"],50)."</a>
										</td>
										<td class='td_list'>".$bbs_notice_fetch[$i][bbs_hit]."</td>";
								}
								if($bbs_notice_cnt<=0) {
									$Contents01 .= "<td colspan='4' style='text-align:center;'>등록된 글이 없습니다.</td>";
								}
							$Contents01 .= "</tr>
					</table>
				</div>
				<p><a href='/admin/seller/bbs.php'><img src='../images/btn_more.gif' alt='더보기' align='absmiddle' /></a></p>	
			</div>
			<div class='banners01'>
				<a href=\"javascript:alert('준비중입니다.')\"><img src='/admin/v3/images/".$_SESSION["admininfo"]['language']."/download_guide_banner01.jpg' alt='' align='absmiddle' /></a>
			</div>
		</div>
		

		<div class='bottom_right'>
			<div class='bottom_right02'>
				<div class='bottom_right02_1'>
					<h4><span><img src='/admin/v3/images/".$_SESSION["admininfo"]['language']."/qna_today.gif' alt='문의사항(오늘)/미처리' align='absmiddle' /></span></h4>
					<div class='more_btn_img'><style='display:none' img src='../images/btn_more.gif' alt='문의사항(오늘)/미처리 더보기' /></div>
					<table cellpadding=0 cellspacing=0 border='0' width='100%'>
						<col width='116' />
						<col width='*' />
						<col width='116' />
						<tr>
							<th>상품문의</th>
							<th>1:1문의</th>
							<th>셀러 1:1문의</th>
						</tr>
						<tr>
							<td><span class='red_font'>".get_Qna_Today('product_qna','N')."</span>/".get_Qna_Today('product_qna')."</td>
							<td><span class='red_font'>".get_Qna_Today('qna','N')."</span>/".get_Qna_Today('qna')."</td>
							<td><span>".get_Qna_Today('seller_qna','N')."</span>/".get_Qna_Today('seller_qna')."</td>
						</tr>
					</table>
				</div>
			</div>
			<div class='bottom_right01'>
				<dl>
					<dt><span><img src='/admin/v3/images/".$_SESSION["admininfo"]['language']."/simple_stats_day.gif' alt='간편통계(일기준/전일)' align='absmiddle' /></span></dt>
					<dd>
						<table width='100%' cellpadding=0 cellspacing=0 border='0'>
						<col width='199'>
						<col width='*'>
							<tr>
								<td class='BG_third_table'>오늘의 매출</a></td>
								<td class='align_right'><strong>".number_format(get_order_Statistics('today'))."</strong>원</td>
							</tr>
							<tr>
								<td class='BG_third_table'>금주의 매출</a></td>
								<td class='align_right'><strong>".number_format(get_order_Statistics('week'))."</strong>원</td>
							</tr>
							<tr>
								<td class='BG_third_table'>정산예정금액</a></td>
								<td class='align_right'><strong>".number_format(get_order_Statistics('AR'))."</strong>원</td>
							</tr>
							<tr class='last_tr'>
								<td class='BG_third_table'>정산확정금액(합계)</a></td>
								<td class='align_right'><strong>".number_format(get_order_Statistics('AC'))."</strong>원</td>
							</tr>
						</table>
					</dd>
				</dl>
				<p><a href='#'><style='display:none' img src='../images/btn_more.gif' alt='더보기' align='absmiddle' /></a></p>	
			</div>
			<div class='bottom_right01'>
				<dl>
					<dt><span><img src='/admin/v3/images/".$_SESSION["admininfo"]['language']."/claim_today.gif' alt='구매자 미처리 클래임(오늘)/미처리' align='absmiddle' /></span></dt>
					<dd>
						<table width='100%'>
						<col width=199'>
						<col width='*'>
							<tr class='t_head'>
								<td class='BG_third_table'><a href='/admin/order/incom_before_cancel.php'>입금전취소(자동)</td>
								<td align='right'><strong>".get_order_claim('IB')."</strong></td>
							</tr>
							<tr>
								<td class='BG_third_table'><a href='/admin/order/incom_after_cancel.php'>입금후취소(자동)</a></td>
								<td align='right'><strong>".get_order_claim('CC')."</strong></td>
							</tr>
							<tr>
								<td class='BG_third_table'><a href='/admin/order/cancel_apply.php'>취소요청(배송준비중)</a></td>
								<td align='right'><span>".get_order_claim('CA')."</span>/".get_order_claim('CA','ALL')."</td>
							</tr>
							<tr>
								<td class='BG_third_table'><a href='/admin/order/exchange_apply.php'>교환요청(미완료)</a></td>
								<td align='right'><span>".get_order_claim('EA')."</span>/".get_order_claim('CC','ALL')."</td>
							</tr>
							<tr class='last_tr'>
								<td class='BG_third_table'><a href='/admin/order/return_apply.php'>반품요청(미완료)</a></td>
								<td align='right'><span>".get_order_claim('RI')."</span>/".get_order_claim('RI','ALL')."</td>
							</tr>
						</table>
					</dd>
				</dl>
				<p><a href='#'><style='display:none' img src='../images/btn_more.gif' alt='더보기' align='absmiddle' /></a></p>	
			</div>
		</div>
		
		<div style='float:left;width:914px;'>
			<h4 class='show_main_title'>
				<span><img src='/admin/v3/images/".$_SESSION["admininfo"]['language']."/show_main.gif' alt='한눈에보는 쇼핑몰 현황' align='absmiddle' style='vertical-align:middle;' /></span>
				<em>*최근 3개월 기준으로 표시됩니다.</em>
			</h4>
			<div class='show_main_BG'>";
				$fetch_pohc=PrintOrderHistoryByCnt();
				$Contents01 .= "<p class='show_main_text01'><a href='/admin/order/before_payment.php'>".number_format($fetch_pohc["incom_ready_cnt"])."</a>건</p><!-- 입금예정-->
				<p class='show_main_text02'><a href='/admin/order/incom_complete.php'>".number_format($fetch_pohc["incom_end_cnt"])."</a>건</p><!-- 입금확인-->
				<p class='show_main_text03'><a href='/admin/order/delivery_ready.php'>".number_format($fetch_pohc["delivery_ready_cnt"])."</a>건</p><!-- 배송준비중-->
				<p class='show_main_text04'><a href='/admin/order/warehouse_delivery_apply.php'>".number_format($fetch_pohc["warehouse_apply_cnt"])."</a></p><!-- 출고요청-->
				<p class='show_main_text05'><a href='/admin/order/warehouse_delivery_ready.php'>".number_format($fetch_pohc["warehouse_ready_cnt"])."</a></p><!--출고대기 -->
				<p class='show_main_text06'><a href='/admin/order/delivery_ing.php'>".number_format($fetch_pohc["delivery_ing_cnt"])."</a>건</p><!-- 배송중-->
				<p class='show_main_text07'><a href='/admin/order/delivery_complete.php'>".number_format($fetch_pohc["delivery_complete_cnt"])."</a>건</p><!--배송완료 -->
				<p class='show_main_text08'><a href='/admin/order/buy_finalized.php'>".number_format($fetch_pohc["order_complete_cnt"])."건</p></a><!-- 거래완료-->
				<p class='show_main_text09'><a href='/admin/order/incom_before_cancel.php'>".number_format($fetch_pohc["incom_before_cnt"])."</a></p><!-- 입금전취소-->
				<p class='show_main_text10'><a href='/admin/order/cancel_apply.php'>".number_format($fetch_pohc["cancel_apply_cnt"])."</a></p><!-- 취소요청-->
				<p class='show_main_text11'><a href='/admin/order/exchange_apply.php'>".number_format($fetch_pohc["exchange_apply_cnt"])."</a></p><!-- 교환요청-->
				<p class='show_main_text12'><a href='/admin/order/return_apply.php'>".number_format($fetch_pohc["return_apply_cnt"])."</a></p><!-- 반품요청-->
				<p class='show_main_text13'><a href='/admin/order/incom_after_cancel.php'>".number_format($fetch_pohc["cancel_complete_cnt"])."</a></p><!--취소완료 -->
				<p class='show_main_text14'><a href='/admin/buyer_accounts/refund_apply.php'>".number_format($fetch_pohc["refund_apply_cnt"])."</a></p><!-- 환불요청-->
				<p class='show_main_text15'><a href='/admin/buyer_accounts/refund_complete.php'>".number_format($fetch_pohc["refund_complete_cnt"])."</a></p><!-- 환불완료-->
			</div>
		</div>
	</div>
	<div class='right_field'>";
	//주석처리 20160401
	if(false){
		$Contents01 .="
		<div class='bottom_right02'>
			<div class='bottom_right02_1'>
				<h4><span><img src='/admin/v3/images/".$_SESSION["admininfo"]['language']."/qna_today.gif' alt='패널티' align='absmiddle' /></span></h4>
				<!-- <div class='more_btn_img'>
				<a href='../seller/seller_level_point.php'>
				<img src='../images/btn_more.gif' alt='패널티 더보기' /></a></div> -->
				<table cellpadding=0 cellspacing=0 border='0' width='100%'>
					<col width='94' />
					<col width='*' />
					<col width='94' />
					<tr>
						<th>나의 등급현황</th>
						<th>이달의 패널티</th>
						<th>전달 패널티</th>
					</tr>
					<tr>
						<td><strong>".get_Seller_group('group')."</strong></td>
						<td><strong>".getSellerPanalty('N')."</strong></td>
						<td><strong>".getSellerPanalty('D')."</strong></td>
					</tr>
				</table>
			</div>
		</div>
		<dl class='sales_stats'>
			<dt>
				<img src='/admin/v3/images/".$_SESSION["admininfo"]['language']."/sales_stats.png' alt='주간매출통계' align='absmiddle' />
				<span>2014.06.09~2014.06.15</span>
			</dt>
			<dd><img src='../images/stats.gif' alt='' align='absmiddle' /></dd>
		</dl>
		";
		if($_SESSION["admininfo"]['language']=='english'){
			$Contents01 .="<div style='margin-top:19px;'><img src='/admin/v3/images/".$_SESSION["admininfo"]['language']."/banners03.gif' alt='banner' /></div>";
		}else{
		$Contents01 .="
			<div class='banners02'>
				<p><a href='/admin/seller/Seller_Event_proposal_1.0.zip'><img src='../images/banner02.jpg' alt='' align='absmiddle' /></a></p>
				<p><a href='/admin/seller/Design_Guide_1.0.zip'><img src='../images/product_banners02_2.jpg' alt='' align='absmiddle' /></a></p>
			</div>
			<div class='banners03'>
				<a href='/admin/seller/daisomall_invoice_registered_and_calculate_ver01.zip'><img src='../images/banners03.jpg' alt='' align='absmiddle' /></a>
			</div>";
		}
	}

		$Contents01 .="
	</div>
	<div style='clear:both;font-size:0;line-height:0;'></div>
</div>

<!-----------------------------------------------------------------------------------------------------------여기까지------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>

";


$Contents = $Contents01;


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = seller_menu();
$P->strContents = $Contents;
$P->Navigation = "HOME > 셀러 메인화면";
$P->TitleBool = false;
$P->title = "쇼핑몰정보설정";
$P->ContentsWidth = "100%";

echo $P->PrintLayOut();



sleep(1);
if($_SESSION["admininfo"][admin_level] == 8){
echo MainOFPopUp($admininfo['charger_ix']);
AlertOrderDelay();
}

$script_time[end] = time();
if($admininfo[charger_id] == "forbiz"){
	//print_r($script_time);
}

//2014-08-30 Hong
function AlertOrderDelay(){
	global $admininfo;

	$odb = new Database;

	/*include_once("../logstory/class/sharedmemory.class");
	$shmop = new Shared("delay_order_process_rule");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$delay_rule = $shmop->getObjectForKey("delay_order_process_rule");
	$delay_rule = unserialize(urldecode($delay_rule));
	
	if($delay_rule["ic_dr_yn"]=="Y"){
		
		$sql="select count(*) as cnt from shop_order_detail where status='IC' and NOW() >= DATE_ADD(ic_date,INTERVAL ".$delay_rule["ic_dr_day"]." DAY) and company_id='".$_SESSION["admininfo"]["company_id"]."' group by oid";
		$odb->query($sql);
		$odb->fetch();

		if($odb->dt[cnt] > 0){
			echo "<script type='text/javascript'>
			<!--
				if(confirm('입금확인후 ".$delay_rule["ic_dr_day"]." 일 지난 주문건이 ".$odb->dt[cnt]." 건 있습니다. \\n\\n입금확인리스트로 이동하시겠습니까?')){
					location.href='/admin/order/incom_complete.php';
				}
			//-->
			</script>";
		}

	}*/
	
	$sql = "select 	count(od_ix) ic_cnt  from  ".TBL_SHOP_ORDER_DETAIL."  where company_id='".$_SESSION["admininfo"]["company_id"]."' and status ='IC' ";
	$odb->query($sql);
	$odb->fetch();
	$ic_cnt = $odb->dt[ic_cnt];

	$sql = "select 	count(od_ix) dr_cnt  from  ".TBL_SHOP_ORDER_DETAIL."  where company_id='".$_SESSION["admininfo"]["company_id"]."' and status ='DR' ";
	$odb->query($sql);
	$odb->fetch();
	$dr_cnt = $odb->dt[dr_cnt];

	$sql = "select 	count(od_ix) ra_cnt  from  ".TBL_SHOP_ORDER_DETAIL."  where company_id='".$_SESSION["admininfo"]["company_id"]."' and status ='RA' ";
	$odb->query($sql);
	$odb->fetch();
	$ra_cnt = $odb->dt[ra_cnt];

	$sql = "select count(*) pna_cnt from ".TBL_SHOP_PRODUCT_QNA." where bbs_re_bool = 'N' and company_id = '".$_SESSION["admininfo"]["company_id"]."' ";
	$odb->query($sql);
	$odb->fetch();
	$pna_cnt = $odb->dt[pna_cnt];

	if($ic_cnt > 0 || $dr_cnt > 0 || $ra_cnt > 0 || $pna_cnt > 0  ){
		echo "<script type='text/javascript'>
			<!--
				alert('신규주문 (".$ic_cnt.")건 \\n배송준비중 (".$dr_cnt.")건 \\n반품미처리 (".$ra_cnt.")건 \\n상품Q&A 미답변 (".$pna_cnt.")건');
			//-->
			</script>";
	}
}


function PrintOrderHistoryByCnt(){//3개월치 구매건수 kbk 13/10/14
	global $admininfo,$sns_product_type;
	$odb = new Database;

	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));


	if($_SESSION["admininfo"][admin_level] == 9){
		if($_SESSION["admininfo"][mem_type] == "MD"){
			$addWhere = " and od.company_id in (".getMySellerList($_SESSION["admininfo"][charger_ix]).") ";
		}
		if($odb->dbms_type == "oracle"){

		$sql = "Select 
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_READY."' then 1 else 0 end),0) as delivery_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' then 1 else 0 end),0) as warehouse_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' then 1 else 0 end),0) as warehouse_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_ING."' then 1 else 0 end),0) as delivery_ing_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_COMPLETE."' then 1 else 0 end),0) as delivery_complete_cnt,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as order_complete_cnt_old,
					IFNULL(sum(case when status = '".ORDER_STATUS_BUY_FINALIZED."' then 1 else 0 end),0) as order_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' then 1 else 0 end),0) as incom_before_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_APPLY."' then 1 else 0 end),0) as cancel_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_APPLY."' then 1 else 0 end),0) as exchange_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_APPLY."' then 1 else 0 end),0) as return_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_COMPLETE."' then 1 else 0 end),0) as cancel_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_APPLY."' then 1 else 0 end),0) as refund_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_COMPLETE."' then 1 else 0 end),0) as refund_complete_cnt
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					where to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-90,substr($vdate,0,4)))."','%Y-%m-%d') <= od.regdate
					and od.regdate <= to_date('".date("Y-m-d")."','%Y-%m-%d')
					AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere ";
		}else{
		$sql = "Select 
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_READY."' then 1 else 0 end),0) as delivery_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' then 1 else 0 end),0) as warehouse_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' then 1 else 0 end),0) as warehouse_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_ING."' then 1 else 0 end),0) as delivery_ing_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_COMPLETE."' then 1 else 0 end),0) as delivery_complete_cnt,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as order_complete_cnt_old,
					IFNULL(sum(case when status = '".ORDER_STATUS_BUY_FINALIZED."' then 1 else 0 end),0) as order_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' then 1 else 0 end),0) as incom_before_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_APPLY."' then 1 else 0 end),0) as cancel_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_APPLY."' then 1 else 0 end),0) as exchange_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_APPLY."' then 1 else 0 end),0) as return_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_COMPLETE."' then 1 else 0 end),0) as cancel_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_APPLY."' then 1 else 0 end),0) as refund_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_COMPLETE."' then 1 else 0 end),0) as refund_complete_cnt
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					where '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-90,substr($vdate,0,4)))."' <= date_format(od.regdate,'%Y%m%d')
					and date_format(od.regdate,'%Y%m%d') <= '".date("Ymd")."'
					AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere ";
		}
	}else if($_SESSION["admininfo"][admin_level] == 8){
		$addWhere = " and od.company_id = '".$_SESSION["admininfo"][company_id]."'";
		if($odb->dbms_type == "oracle"){

			$sql = "Select 
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_READY."' then 1 else 0 end),0) as delivery_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' then 1 else 0 end),0) as warehouse_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' then 1 else 0 end),0) as warehouse_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_ING."' then 1 else 0 end),0) as delivery_ing_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_COMPLETE."' then 1 else 0 end),0) as delivery_complete_cnt,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as order_complete_cnt_old,
					IFNULL(sum(case when status = '".ORDER_STATUS_BUY_FINALIZED."' then 1 else 0 end),0) as order_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' then 1 else 0 end),0) as incom_before_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_APPLY."' then 1 else 0 end),0) as cancel_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_APPLY."' then 1 else 0 end),0) as exchange_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_APPLY."' then 1 else 0 end),0) as return_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_COMPLETE."' then 1 else 0 end),0) as cancel_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_APPLY."' then 1 else 0 end),0) as refund_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_COMPLETE."' then 1 else 0 end),0) as refund_complete_cnt
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					where to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."','%Y-%m-%d') <= od.regdate
					and od.regdate <= to_date('".date("Y-m-d")."','%Y-%m-%d')
					AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere ";
		}else{
		$sql = "Select 
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_READY."' then 1 else 0 end),0) as delivery_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' then 1 else 0 end),0) as warehouse_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' then 1 else 0 end),0) as warehouse_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_ING."' then 1 else 0 end),0) as delivery_ing_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_COMPLETE."' then 1 else 0 end),0) as delivery_complete_cnt,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as order_complete_cnt_old,
					IFNULL(sum(case when status = '".ORDER_STATUS_BUY_FINALIZED."' then 1 else 0 end),0) as order_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' then 1 else 0 end),0) as incom_before_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_APPLY."' then 1 else 0 end),0) as cancel_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_APPLY."' then 1 else 0 end),0) as exchange_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_APPLY."' then 1 else 0 end),0) as return_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_COMPLETE."' then 1 else 0 end),0) as cancel_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_APPLY."' then 1 else 0 end),0) as refund_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_COMPLETE."' then 1 else 0 end),0) as refund_complete_cnt
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					where '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' <= date_format(od.regdate,'%Y%m%d')
					and date_format(od.regdate,'%Y%m%d') <= '".date("Ymd")."'
					AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere ";
		}
	}


	//echo nl2br($sql);

	$odb->query($sql);
	return $odb->fetch();
}

function PrintOrderHistory(){
	global $admininfo, $admin_config, $currency_display;
	$odb = new Database;

	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));



	if($admininfo[admin_level] == 9){
		/*
		if($odb->dbms_type == "oracle"){
			$sql = "$sql = "select '기간      ','매출액(".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"].") ', '주문건수','입금예정', '입금확인','배송준비/배송중', '교환 ','주문취소' from dual
					union ";
		}else{
			$sql = "select '기간      ','매출액(".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"].") ', '주문건수','입금예정', '입금확인','배송준비/배송중', '교환 ','주문취소'
					union
					";
		}
		*/
		$sql = "
		Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
		IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
		IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
		IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
		IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price
		from ".TBL_SHOP_ORDER." where order_date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."' AND '".date("Y-m-d 23:59:59",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."'
		union
		Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
		IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
		IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
		IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
		IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price
		from ".TBL_SHOP_ORDER." where order_date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."' AND '".date("Y-m-d 23:59:59",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."'
		union
		Select '".date("m/d")." 오늘 ',
		IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
		IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
		IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
		IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price
		from ".TBL_SHOP_ORDER." where order_date between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
		union
		Select '최근1주',
		IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
		IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
		IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
		IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price
		from ".TBL_SHOP_ORDER." where order_date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))."' and '".date("Y-m-d 00:00:00")."'
		union
		Select '금주',
		IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
		IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
		IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
		IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price
		from ".TBL_SHOP_ORDER." where order_date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($firstday,4,2),substr($firstday,6,2),substr($firstday,0,4)))."' and '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($lastday,4,2),substr($lastday,6,2),substr($lastday,0,4)))."'
		union
		Select '최근1개월',
		IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
		IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
		IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
		IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price
		from ".TBL_SHOP_ORDER." where order_date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."'
		union
		Select '전체 ',
		IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
		IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
		IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
		IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
		IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price
		from ".TBL_SHOP_ORDER." ";
	

		//echo $sql;
		//exit;
		/*
		$sql = "Select
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then total_price else '0' end) as today_total_price,
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m%d') between '".$firstday."' and $lastday then total_price else '0' end) as thisweek_total_price,
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else '0' end) as thismonth_total_price,
					sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else '0' end) as thismonth_cancel_total_price,
					sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end) as ready_cnt,
					sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end) as order_end_cnt,
					sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end) as thismonth_return_total_cnt
				 	from ".TBL_SHOP_ORDER."  ";
				 	*/
	//echo $sql;
	}else if($admininfo[admin_level] == 8){

			/*
		if($odb->dbms_type == "oracle"){
			$sql = "$sql = "select '기간      ','매출액(".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"].") ', '주문건수','입금예정', '입금확인','배송준비/배송중', '교환 ','주문취소' from dual
					union ";
		}else{
			$sql = "select '기간      ','매출액(".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"].") ', '주문건수','입금예정', '입금확인','배송준비/배송중', '교환 ','주문취소'
					union
					";
		}
		*/
		if($odb->dbms_type == "oracle"){
			$sql = "
			Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price,
			1 as vieworder
			from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and date_format(date_,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."'
			union
			Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price,
			2 as vieworder
			from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and date_format(date_,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."'
			union
			Select '".date("m/d")." 오늘 ',
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price,
			3 as vieworder
			from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and date_format(date_,'%Y%m%d') =  '".date("Ymd")."'
			union
			Select '최근1주',
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price,
			4 as vieworder
			from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."'  and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))."'  <=  date_format(date_,'%Y%m%d') and date_format(date_,'%Y%m%d') <= '".date("Ymd")."'
			union
			Select '금주',
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price,
			5 as vieworder
			from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and '".$firstday."' <= date_format(date_,'%Y%m%d')  and date_format(date_,'%Y%m%d') <=   '".$lastday."'
			union
			Select '최근1개월',
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price,
			6 as vieworder
			from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' <= date_format(date_,'%Y%m%d') and date_format(date_,'%Y%m%d') <= '".date("Ymd")."'
			union
			Select '전체',
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price,
			7 as vieworder
			from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' order by vieworder asc ";
		}else{
			$sql = "
			Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price
			from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and order_date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."' AND '".date("Y-m-d 23:59:59",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."'
			union
			Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price
			from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and order_date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."' AND '".date("Y-m-d 23:59:59",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."'
			union
			Select '".date("m/d")." 오늘 ',
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price
			from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and order_date between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1주',
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price
			from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."'  and order_date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."'
			union
			Select '금주',
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price
			from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and order_date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($firstday,4,2),substr($firstday,6,2),substr($firstday,0,4)))."' and '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($lastday,4,2),substr($lastday,6,2),substr($lastday,0,4)))."'
			union
			Select '최근1개월',
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price
			from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and order_date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."'
			union
			Select '전체',
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else 0 end),0) as total_price,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
			IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else 0 end),0) as delivery_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
			IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else 0 end),0) as cancel_total_price
			from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
			where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' ";
		}
	}


	$odb->query($sql);
	$datas = $odb->getrows();
	$datas_title[0] = "기간";
	$datas_title[1] = "매출액(원)";
	$datas_title[2] = "주문건수(건)";
	$datas_title[3] = "입금예정(건)";
	$datas_title[4] = "입금확인(건)";
	$datas_title[5] = "배송준비/배송중(건)";
	$datas_title[6] = "교환(건)";
	$datas_title[7] = "주문취소(건)";

	$mstring = "
				<table cellpadding=0 cellspacing=1 width=100% bgcolor=#c0c0c0 class='list_table_box'>
				<col width=*>
				<col width=10%>
				<col width=10%>
				<col width=10%>
				<col width=15%>
				<col width=15%>
				<col width=15%>
				<col width=10%>
		";
	for($i=0;$i<count($datas)-1;$i++){
		//print_r($datas);
			if($i == 0){
				$mstring .= "<tr bgcolor=#ffffff align=center height=30>
					<td class='s_td'>".$datas_title[$i]."</td>
					<td class='s_td'>".$datas[$i][0]."</td>
					<td class='m_td'>".$datas[$i][1]."</td>
					<td class='m_td'>".$datas[$i][2]."</td>
					<td class='m_td' nowrap><b>".$datas[$i][3]."</b></td>
					<td class='m_td'>".$datas[$i][4]."</td>
					<td class='m_td'>".$datas[$i][5]."</td>
					<td class='m_td'>".$datas[$i][6]."</td>
					<!--td class='e_td'>".$datas[$i][7]."</td-->
					</tr>";
			}else{
				$mstring .= "<tr bgcolor=#ffffff height=27 align=right>
					<td class='s_td'>".$datas_title[$i]."</td>
					<td class='list_box_td list_bg_gray' nowrap><b>".$datas[$i][0]."</b></td>
					<td class='list_box_td number' >".number_format($datas[$i][1])."</td>
					<td class='list_box_td list_bg_gray number' >".number_format($datas[$i][2])." </td>
					<td class='list_box_td point number' ><b>".number_format($datas[$i][3])."</b></td>
					<td class='list_box_td list_bg_gray number'  >".number_format($datas[$i][4])."</td>
					<td class='list_box_td number' >".number_format($datas[$i][5])."</td>
					<td class='list_box_td list_bg_gray number' >".number_format($datas[$i][6])."</td>
					<!--td class='list_box_td point number' >".number_format($datas[$i][7])."</td-->
					</tr>";
			}
	}
	$mstring .= "</table>";
	return $mstring;


}

function PrintBoardRecentList(){
	global $db, $mdb;

	$sql = "select COUNT(*) from ".TBL_SHOP_BBS_USEAFTER."  ";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[0];

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=0 cellspacing=0 width='100%' bgcolor=silver>
		<tr align=center bgcolor=#efefef height=27 style='font-weight:bold'>
			<td width='20%' class='s_td'>상품</td>
			<td class='m_td'>내용</td>
			<td width='10%' class='m_td' nowrap>작성자</td>
			<td width='15%' class='m_td'>등록일</td>
			<td width='10%' class='e_td'>관리</td>
		</tr>";
	$mString = $mString."<tr height=2 bgcolor=#ffffff><td colspan=5 ></td></tr>";
	$mString = $mString."<tr height=1><td colspan=5 class=dot-x></td></tr>";
	//$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class=s_td width='25%'>상품</td><td class=m_td width='40%'>내용</td><td class=m_td width='10%' nowrap>작성자</td><td class=m_td width='15%'>등록일</td><td class=e_td width='10%'>관리</td></tr>";
	if ($total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=5 align=center>사용후기 내역이 존재 하지 않습니다.</td></tr>";
	}else{

		$db->query("select p.pname, u.* from ".TBL_SHOP_BBS_USEAFTER." u , ".TBL_SHOP_PRODUCT." p where u.pid = p.id  order by  regdate desc limit 0,6");

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

			//$no = $no + 1;

			$mString .= "<tr height=25 bgcolor=#ffffff align=center>
			<td bgcolor='#efefef'>".$db->dt[pname]."]</td>
			<td align=left style='padding-left:20px;'>".cut_str(strip_tags($db->dt[uf_contents]),30)."</td>
			<td bgcolor='#efefef'>".$db->dt[uf_name]."</td>
			<td>".str_replace("-",".",substr($db->dt[regdate],0,10))."</td>
			<td bgcolor='#efefef' align=center>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			   $mString.="
				<a href=JavaScript:useAfterDelete('".$db->dt[uf_ix]."')><img  src='../image/nbtn_delete.gif' border=0></a>
				";
			}else{
				$mString.="
				<a href=\"javascript:alert('삭제권한이 없습니다.')\"><img  src='../image/nbtn_delete.gif' border=0></a>
				";
			}
			$mString .="
			</td>
			</tr>
			<tr height=1><td colspan=5 class=dot-x></td></tr>
			";
		}

	}

	//<tr height=50 bgcolor=#ffffff><td colspan=6 align=left>".page_bar($total, $page, $max,  "&max=$max")."</td></tr>
	$mString .= "</table>";

	return $mString;
}



function PrintRecentProductList($stock_status=""){
	global $db, $mdb, $admin_config, $admininfo, $DOCUMENT_ROOT, $currency_display,$image_hosting_type;

	$where = array();
	if($stock_status == "soldout"){
		$where[] = "(option_stock_yn = 'Y' or stock = 0 or state = 0 )";
	}else if($stock_status == "shortage"){

		if($mdb->dbms_type == "oracle"){
			$where[] = "(option_stock_yn = 'S' or (stock < safestock and stock != 0 ))";
		}else{
			$where[] = "(option_stock_yn = 'S' or (stock < safestock && stock != 0 ))";
		}
	}
	$where = (count($where) > 0)	?	' where '.implode(' AND ', $where):'where 1=1 ';


	if($admininfo[admin_level] == 9){
		$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1
					$where
					order by mp1.regdate desc
					limit 0,5  ";
	}else{
		$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1
					$where and mp1.admin ='".$admininfo[company_id]."'
					order by mp1.regdate desc
					limit 0,5  ";
	}

	//echo nl2br($sql)."<br><br>";
	$mdb->query($sql);


	$mString = "<table cellpadding=4 cellspacing=0 border=0 width='100%' bgcolor=silver>";
	//echo $admin_config["currency_unit"];
	if ($mdb->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=2 align=center>등록된 상품이 없습니다.</td></tr>";
	}else{

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			if(file_exists("$DOCUMENT_ROOT".PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[id], "s" , $mdb->dt)) || $image_hosting_type=='ftp'){
				$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[id], "s", $mdb->dt);
			}else{
				$img_str = "../image/no_img.gif";
			}

			$mString = $mString."<tr height=45 bgcolor=#ffffff align=center>
			<td bgcolor='#ffffff' width=50><a href='../product/goods_input.php?id=".$mdb->dt[id]."'><img src='".$img_str."' width=50 height=50 border=0 style='border:1px solid #c0c0c0'></a></td>
			<td align=left style='padding:4px 4px 4px 10px'>
				<table border=0 cellpadding=2 cellspacing=0 width=100%>
					<tr>
						<td>".($mdb->dt[brand_name] ? "[".$mdb->dt[brand_name]."]":"")."</td>
					<tr>
						<td><a href='../product/goods_input.php?id=".$mdb->dt[id]."'>".cut_str($mdb->dt[pname],20)."</a></td>
					</tr>
					<tr>
						<td >".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($mdb->dt[sellprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					</tr>
				</table>
			</td>
			</tr>
			<tr height=1><td colspan=6 class=dot-x></td></tr>
			";
		}

	}


	$mString = $mString."</table>";

	return $mString;
}

function getSellerPanalty($type='D'){

	global $db, $mdb, $_SESSION;

	$company_id = $_SESSION[admininfo][company_id];
	
	$vdate = date("Y-m-d", time());
	$vyesterday = date("Y-m-d", time()-86400);
	$voneweekago = date("Y-m-d", time()-86400*7);
	$v15ago = date("Y-m-d", time()-86400*15);
	$vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-1,substr($vdate,8,2)+1,substr($vdate,0,4)));
	$v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-2,substr($vdate,8,2)+1,substr($vdate,0,4)));
	$v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-3,substr($vdate,8,2)+1,substr($vdate,0,4)));

	$curtime=date("Y-m-d");
	$time=strtotime($curtime );
	
	$month = date('Y-m-t',strtotime('last month',$time));
	$last_month = date('Y-m-t',strtotime('last month',$time));
	
	if($type == 'D'){
		$this_month = " and regdate between '".substr($last_month,0,8)."01 00:00:00"."' and '".$last_month." 23:59:59'";
	}else if($type == 'N'){
		$this_month = " and regdate between '".substr($vdate,0,8)."01 00:00:00"."' and '".$vdate." 23:59:59'";
	}else{
		$this_month = " and regdate between '".substr($last_month,0,8)."01 00:00:00"."' and '".$last_month." 23:59:59'";
	}
	$sql = "select 
				sum(if(state = '1',penalty,0)) as use_penalty,
				sum(if(state = '2',penalty,0)) as cancel_penalty
			from
				common_seller_penalty 
			where
				company_id = '".$company_id."'
				$this_month
				";
	//echo $sql."<br>";		
	$db->query($sql);
	$db->fetch();
	
	return number_format($db->dt[use_penalty]);


}

function get_Qna_Today($type, $status=''){
	global $db, $admininfo;
	
	if($type == 'product_qna'){
		//$where = " where bbs_ix <> '0' ";
		
		if(!empty($status)){
			$where .= " and bbs_re_bool = '$status' ";
		}
		
		$where .= " and regdate LIKE '".date('Y-m-d')."%'";
		
		if($admininfo[admin_level] == 9){
			$sql = "select count(*) data from ".TBL_SHOP_PRODUCT_QNA." where 1 $where ";
		}else{
			$sql = "select count(*) data from ".TBL_SHOP_PRODUCT_QNA." where 1 $where and company_id = '".$admininfo["company_id"]."' ";
		}

	}else if($type == 'qna'){
		
		if($status == 'W'){
			$where .= " and status = 'W' ";
		}else{
			//$where .= " and status != 'W' ";
		}
		
		$where .= " and regdate LIKE '".date('Y-m-d')."%'";
		
		if($admininfo[admin_level] == 9){

			$sql = "select count(*) data from bbs_seller_shop_cs $where ";
			// 전체 갯수 불러오는 부분
			$sql = "SELECT 
						count(*) data 
					FROM
						common_seller_support as fs
					where
						type= 'Q'
						$where";
		}else{
			$sql = "select count(*) data from bbs_seller_shop_cs $where and bbs_etc5 = '".$admininfo["company_id"]."' ";
			$sql = "SELECT 
						count(*) data 
					FROM
						common_seller_support as fs
					where
						type= 'Q'
						$where
						and fs.company_id = '".$admininfo["company_id"]."'";
		}


		
		
	}else if($type == 'seller_qna'){
		$where = " where fs.fs_ix <> '0' ";
		
		if($status == 'N'){
			$where .= " and fs.status != '8' ";
		}
		
		$where .= " and fs.regdate LIKE '".date('Y-m-d')."%'";
		
		if($status == 'N'){
			$where .= " and fs.status != 'C' ";
		}
		
		if($_SESSION["admininfo"]["admin_level"] != '9'){
			$where .= " and fs.company_id = '".$admininfo[company_id]."'";
		}
		$sql = "SELECT 
					count(*) data 
				FROM
					common_seller_support as fs
					$where";

		
	}
	$db->query($sql);
	$db->fetch();
	$data = $db->dt[data];
	
	return $data;
	
}

function get_Seller_group($type){
	global $db, $admininfo;
	
	
		$sql = "select 
						sg.group_name as data 
				from 
					common_seller_detail sd
					left join common_seller_group sg on sd.sg_ix = sg.sg_ix
				where 
					sd.company_id = '".$admininfo[company_id]."'";	
	

	$db->query($sql);
	$db->fetch();
	$data = $db->dt[data];
	
	if($data){
		return $db->dt[data];
	}else{
		return "-";
	}
	
}

function get_order_claim($type, $status=''){
	global $db, $admininfo ,$sns_product_type;
	
	
	if($status == ""){
		$where = " and regdate LIKE '".date('Y-m-d')."%' ";
	}

	if($admininfo[admin_level] == 8){
		$where .= " and company_id = '".$admininfo[company_id]."' ";
	}
	
	if($type == 'IB'){
		$pre_type = ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE;
		$sql = "select count(*) data from ".TBL_SHOP_ORDER_DETAIL." where status ='$pre_type' $where ";
	}else if($type == 'CC'){
		$pre_type = ORDER_STATUS_CANCEL_COMPLETE;
		$sql = "select count(*) data from ".TBL_SHOP_ORDER_DETAIL." where status ='$pre_type' $where ";
	}else if($type == 'CA'){
		$pre_type = ORDER_STATUS_CANCEL_APPLY;
		$sql = "select count(*) data from ".TBL_SHOP_ORDER_DETAIL." where status ='$pre_type' $where ";
	}else if($type == 'EA'){
		$pre_type = ORDER_STATUS_EXCHANGE_ING;
		$sql = "select count(*) data from ".TBL_SHOP_ORDER_DETAIL." where status ='$pre_type' $where ";
	}else if($type == 'RI'){
		$pre_type = ORDER_STATUS_RETURN_ING;
		$sql = "select count(*) data from ".TBL_SHOP_ORDER_DETAIL." where status ='$pre_type' $where ";
	}
	
	$db->query($sql);
	$db->fetch();
	$data = $db->dt[data];
	
	return $data;
}

function get_order_Statistics($type){
	global $db, $admininfo,$sns_product_type;
	
	if($type=='today'){
		
		$where =" WHERE status in ('IC','DR','DI','DC')";
		
		$where .= " and regdate LIKE '".date('Y-m-d')."%' ";
		
		if($admininfo[admin_level] == 8){
			$where .= " and company_id = '".$admininfo[company_id]."' ";
		}
		$sql = "select sum(ptprice) data from ".TBL_SHOP_ORDER_DETAIL."  $where ";
		
	}else if($type == 'week'){
		$where =" WHERE status in ('IC','DR','DI','DC') ";
		$now=date("w");
		$m=$now;
		$week_start=date("Y-n-d",strtotime("-$m day"));
		
		//echo $week_start;

		$where .= " and regdate between '".$week_start." 00:00:00"."' and '".date('Y-m-d H:i:s')."' ";
		
		if($admininfo[admin_level] == 8){
			$where .= " and company_id = '".$admininfo[company_id]."' ";
		}
		$sql = "select sum(ptprice) data from ".TBL_SHOP_ORDER_DETAIL."  $where ";
		
		//echo $sql;
	}else if($type == 'AR'){

		include ("../seller_accounts/accounts.lib.php");

        $day = date("Y-m-d");

		$where="
		where od.product_type NOT IN (".implode(',',$sns_product_type).") 
		and od.company_id = '".$admininfo[company_id]."'
		and od.account_type !='3'
		and  DATE_ADD(case od.ac_delivery_type when '".ORDER_STATUS_INCOM_COMPLETE."' then od.ic_date when '".ORDER_STATUS_DELIVERY_READY."' then od.dr_date when '".ORDER_STATUS_DELIVERY_ING."' then od.di_date when '".ORDER_STATUS_DELIVERY_COMPLETE."' then od.dc_date when '".ORDER_STATUS_BUY_FINALIZED."' then od.bf_date else od.dc_date end,INTERVAL od.ac_expect_date DAY) between '".date('Y-m-d')." 00:00:00' and '".date('Y-m-d')." 23:59:59' ";

		$sub_where="
		and odr.product_type NOT IN (".implode(',',$sns_product_type).") 
		and odr.company_id = '".$admininfo[company_id]."'
		and odr.account_type !='3'";

		
		$sql = "select  
				sum(
					case when 
						refund_bool='Y'
					then
						-(p_expect_price-p_dc_allotment_price-p_fee_price+d_expect_price-d_dc_allotment_price) 
					else
						(p_expect_price-p_dc_allotment_price-p_fee_price+d_expect_price-d_dc_allotment_price)
					end 
				) as data
			from (
				select
					o.*,
					case when od.account_type in ('1','') then od.ptprice else od.coprice*od.pcnt end as p_expect_price,
					(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE')) as p_dc_allotment_price,
					case when od.account_type in ('1','') then ((od.ptprice - (select ifnull(sum(dc.dc_price_seller),'0') from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end as p_fee_price,
					(
					case when
						o.refund_bool='Y'
					then
						case when 
							od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.claim_group = od.claim_group and ".str_replace("od.","odr.",$AC_REFUND_QUERY)." ".$sub_where.")
						then
							(
								ocd.delivery_price
							) 
						else
							'0' 
						end
					else 
						case when 
							od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ode_ix = od.ode_ix and ".str_replace("od.","odr.",$AC_NORMARL_QUERY)." ".$sub_where.")
						then
							(
								odv.delivery_price
							) 
						else
							'0' 
						end
					end

				) as d_expect_price,
				(
					case when
						o.refund_bool='Y'
					then
						'0'
					else 
						case when 
							od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ode_ix = od.ode_ix and ".str_replace("od.","odr.",$AC_NORMARL_QUERY)." ".$sub_where.")
						then
							(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.ode_ix=odv.ode_ix and dc.dc_type in ('DCP','DE')) 
						else
							'0' 
						end
					end

				) as d_dc_allotment_price
			from (
				select
					od.oid,od.od_ix,'N' as refund_bool
				from
					".TBL_SHOP_ORDER_DETAIL." od
				left join shop_order_delivery odv on (
					odv.oid=od.oid
					and odv.ode_ix = od.ode_ix
					and odv.delivery_type != '1'
					and odv.delivery_pay_type = '1'
					and odv.ac_ix = '0'
				)
				".$where." and od.ic_date between '".date("Y-m-d", strtotime($day."-7day"))." 00:00:00' and '".$day." 23:59:59'
				".$search_where."
				and
				(
					".$AC_NORMARL_QUERY."
					
				)

				union all

				select
					od.oid,od.od_ix,'Y' as refund_bool
				from
					".TBL_SHOP_ORDER_DETAIL." od
				left join shop_order_claim_delivery ocd on (
					ocd.oid=od.oid
					and ocd.company_id=od.company_id
					and ocd.claim_group=od.claim_group
					and ocd.ac_ix='0' 
					and ocd.ac_target_yn='Y' 
					and ocd.delivery_type != '1'
				)
				".$where." and od.ic_date between '".date("Y-m-d", strtotime($day."-7day"))." 00:00:00' and '".$day." 23:59:59'
				".$search_where."
				and
				(
					".$AC_REFUND_QUERY."
				)
			) o

			left join
					".TBL_SHOP_ORDER_DETAIL." od on o.od_ix = od.od_ix
			left join 
					shop_order_delivery odv on (
				odv.oid=od.oid
				and odv.ode_ix = od.ode_ix
				and odv.delivery_type != '1'
				and odv.delivery_pay_type = '1'
				and odv.ac_ix = '0'
			)
			left join
					shop_order_claim_delivery ocd on (
				ocd.oid=od.oid
				and ocd.company_id=od.company_id
				and ocd.claim_group=od.claim_group
				and ocd.ac_ix='0' 
				and ocd.ac_target_yn='Y' 
				and ocd.delivery_type != '1'
			)
		) a
		";

	}else if($type == 'AC'){
		
		$where .=" WHERE ac.status='".ORDER_STATUS_ACCOUNT_COMPLETE."' ";
		
		//$where .= " and ac.ac_info='1' ";
		
		if($admininfo[admin_level] == 9){
			if($company_id != "") $where .= " and ac.company_id='$company_id' ";

			if($admininfo[mem_type] == "MD"){
				$where .= " and ac.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}

		}else if($admininfo[admin_level] == 8){
			$where .= " and ac.company_id = '".$admininfo[company_id]."' ";
		}
		
		$sql = "select	sum(ac.ac_price) data 
				from
					".TBL_SHOP_ACCOUNTS." ac 
				$where
			";
		
	}
	$db->query($sql);
	$db->fetch();
	$data = $db->dt[data];
	
	return $data;
	
}
?>
