<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");

if($admininfo[admin_level] < 9){
	header("Location:/admin/seller/");
}

$reserve_data = getBasicSellerSetup('b2c_mileage_rule');
$whole_reserve_data = getBasicSellerSetup('b2b_mileage_rule');

if($_GET['sdate'] !=""){
	$sDate = $_GET['sdate'];
}else{
	$sDate = date("Y-m-d", strtotime(date("Y-m-d")."-7day"));
}

if($_GET['edate'] !=""){
	$eDate = $_GET['edate'];
}else{
	$eDate = date("Y-m-d");
}

if($before_update_kind){
	$update_kind = $before_update_kind;
}

if($_COOKIE["goodsinfo_update_kind"]){
	$update_kind = $_COOKIE["goodsinfo_update_kind"];
}else if(!$update_kind){
	$update_kind = "display";
}

if($max == ""){
	$max = 1000; //페이지당 갯수
}else{
	$max = $max;
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


$db = new Database;
$db2 = new Database;

 
include "product_static_query.php";


if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&mode=$mode&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&sprice=$sprice&eprice=$eprice&state2=$state2&disp=$disp&brand_name=$brand_name&cid2=$cid2&depth=$depth&company_id=$company_id&event=$event&best=$best&sale=$sale&wnew=$wnew&mnew=$mnew");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype");
	//echo $total.":::".$page."::::".$max."<br>";
}

$Contents =	"
<table cellpadding=0 cellspacing=0 width='100%'>
<script  id='dynamic'></script>
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("".$static_div_name."별 상품통계", "상품통계 > ".$static_div_name."별 상품통계")."</td>
	</tr>

	";

$Contents .=	"
	<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<input type='hidden' name='sprice' value='0' />
	<input type='hidden' name='eprice' value='1000000' />
	<tr height=150>
		<td colspan=2  >
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:1px'>
						<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
							<col width='15%'>
							<col width='35%'>
							<col width='15%'>
							<col width='35%'>
							<tr>
								<td class='search_box_title'><b>카테고리선택</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
											<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
											<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
											<td>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
										</tr>
									</table>
								</td>
							</tr>
							";
							if($admininfo[mall_type] != "F" && $admininfo[admin_level] == 9){
							$Contents .=	"
							<tr>
								<td class='search_box_title'><b>입점업체</b></td>
								<td class='search_box_item'>".CompanyList2($company_id,"")."</td>
								<td class='search_box_title'><b>브랜드</b></td>
								<td class='search_box_item'>".BrandListSelect($brand, $cid)."</td>
							</tr>
							";
							}else{
							$Contents .=	"
							<tr>
								<td class='search_box_title'><b>브랜드</b></td>
								<td class='search_box_item' colspan=3>".BrandListSelect($brand, $cid)."</td>
							</tr>
							";
							}
								$Contents .=	"
							<tr>
								<td class='search_box_title'><b>진열</b></td>
								<td class='search_box_item'>
								<input type='radio' name='disp'  id='disp_' value='' ".ReturnStringAfterCompare($disp, "", " checked")."><label for='disp_'>전체</label>
								<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>노출함</label>
								<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>노출안함</label>
								</td>
								<td class='search_box_title'><b>판매상태</b></td>
								<td class='search_box_item'>
									<select name='state2' style='font-size:12px;'>
										<option value=''>상태값선택</option>
										<option value='1' ".ReturnStringAfterCompare($state2, "1", " selected").">판매중</option>
										<option value='0' ".ReturnStringAfterCompare($state2, "0", " selected").">일시품절</option>
										<option value='6' ".ReturnStringAfterCompare($state2, "6", " selected").">등록신청중</option>
										<option value='7' ".ReturnStringAfterCompare($state2, "7", " selected").">수정신청중</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>도매/소매</b></td>
								<td class='search_box_item'>
								<input type='radio' name='wholesale_yn'  id='wholesale_yn' value='A' ".ReturnStringAfterCompare($wholesale_yn, "A", " checked")."><label for='wholesale_yn'>전체</label>
								<input type='radio' name='wholesale_yn'  id='wholesale_yn' value='Y' ".ReturnStringAfterCompare($wholesale_yn, "Y", " checked")."><label for='wholesale_yn'>도매</label>
								<input type='radio' name='wholesale_yn'  id='wholesale_yn' value='N' ".ReturnStringAfterCompare($wholesale_yn, "N", " checked")."><label for='wholesale_yn'>소매</label>
								</td>
								<td class='search_box_title'><b>오프라인 상품 등록여부</b></td>
								<td class='search_box_item'>
								<input type='radio' name='offline_yn'  id='offline_yn' value='' ".ReturnStringAfterCompare($offline_yn, "", " checked")."><label for='offline_yn'>전체</label>
								<input type='radio' name='offline_yn'  id='offline_yn' value='Y' ".ReturnStringAfterCompare($offline_yn, "Y", " checked")."><label for='offline_yn'>포스판매 사용</label>
								<input type='radio' name='offline_yn'  id='offline_yn' value='N' ".ReturnStringAfterCompare($offline_yn, "N", " checked")."><label for='offline_yn'>포스판매 사용안함</label>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>검색어</b></td>
								<td class='search_box_item' style='padding-right:5px;margin-top:3px;'>
									<table cellpadding=0 cellspacing=0 >
										<tr >
											<td>
											<select name='search_type'  style=\"font-size:12px;height:20px;\">
												<option value='pname'>상품명</option>
												<option value='pcode'>상품코드</option>
												<option value='id'>상품코드(key)</option>
											</select>
											</td>
											<td style='padding-left:5px;'><INPUT id=search_texts  class='textbox1' value='".$search_text."' onclick='findNames();'  clickbool='false' style='height:16px;FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
											<DIV id=popup style='DISPLAY: none; WIDTH: 160px; POSITION: absolute; HEIGHT: 150px; BACKGROUND-COLOR: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
												<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100% bgColor=#efefef>
													<tr height=20>
														<td width=100%  style='padding:0 0 0 5'>
															<table width=100% cellpadding=0 cellspacing=0 border=0>
																<tr>
																	<td class='p11 ls1'>검색어 자동완성</td>
																	<td class='p11 ls1' onclick='focusOutBool=false;clearNames()' style='cursor:pointer;padding:0 10 0 0' align=right>닫기</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr height=100% >
														<td valign=top bgColor=#efefef style='padding:0 6 5 6' colspan=2>
															<table width=100% height=100% bgcolor=#ffffff>
																<tr>
																	<td valign=top >
																	<div style='POSITION: absolute; overflow-y:auto;HEIGHT: 120px;' id='search_data_area'>
																		<TABLE id=search_table style='table-layout:fixed;'  width=100% cellSpacing=0 cellPadding=1 bgColor=#ffffff border=0>
																		<TBODY id=search_table_body></TBODY>
																		</TABLE>
																	<div>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
												</DIV>
											</td>
											<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
										</tr>
									</table>
								</td>
								<td class='search_box_title'><b>목록갯수</b></td>
								<td class='search_box_item'><select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle><!-- onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&depth=$depth&view=innerview&max='+this.value\"-->
								<option value='5' ".CompareReturnValue(5,$max).">5</option>
								<option value='10' ".CompareReturnValue(10,$max).">10</option>
								<option value='20' ".CompareReturnValue(20,$max).">20</option>
								<option value='50' ".CompareReturnValue(50,$max).">50</option>
								<option value='100' ".CompareReturnValue(100,$max).">100</option>
								<option value='100' ".CompareReturnValue(500,$max).">500</option>
								<option value='100' ".CompareReturnValue(1000,$max).">1000</option>
								</select> <span class='small'><!--한페이지에 보여질 갯수를 선택해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
								</td>
							</tr>
							<tr height=27>
								<td class='search_box_title'><b>등록일자</b></td>
								<td class='search_box_item' colspan=3 >
								".search_date('sdate','edate',$sDate,$eDate)."
								</td>
							</tr>
						</table>
					</td>
					<th class='box_06'></th>
				</tr>
				<tr>
					<th class='box_07'></th>
					<td class='box_08'></td>
					<th class='box_09'></th>
				</tr>
			</table>
		</td>
	</tr>
	<tr >
		<td colspan=2 align=center style='padding:10px 0px'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
		</form>
	</tr>
	</table>";

$Contents .=	"
	<form name=listform method=post action='goods_batch.act.php' onsubmit='return SelectUpdate(this)'  target='iframe_act' ><!--onsubmit='return CheckDelete(this)'iframe_act -->
	<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
	<input type='hidden' id='pid' value=''>
	<input type='hidden' name='act' value='update'>
	<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td valign=top style='padding-top:33px;'>";

$Contents .= "
		</td>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

$innerview = "
			<table width='100%' cellpadding=0 cellspacing=0 border=0 >
				<tr height=30>
					<td align=left>
					 
					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>
			</table>
			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
				<col width='5%'> 
				<col width='15%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<!-- <col width='7%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'> -->

				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<!-- <col width='7%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'> -->
				<tr bgcolor='#cccccc' height=30 align=center>
					<td class=s_td rowspan=2><!--input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'--></td>
					
					<td class=m_td rowspan=2>".$static_div_name."</td>
					<td class=m_td colspan=3>수량(개)</td>
					<td class=m_td colspan=3>점유율(%)</td>
				</tr>
				<tr height=30>
					<td class=m_td>판매중</td>
					<td class=m_td>일시품절</td>
					<td class=m_td>판매중지</td>
					<!-- <td class='m_td small'><b>수정대기상품</b></td>
					<td class=m_td>승인대기</td>
					<td class=m_td>승인거부</td>
					<td class=e_td>판매금지</td> --> 

					<td class=m_td>판매중</td>
					<td class=m_td>일시품절</td>
					<td class=m_td>판매중지</td>
					<!-- <td class='m_td small'><b>수정대기상품</b></td>
					<td class=m_td>승인대기</td>
					<td class=m_td>승인거부</td>
					<td class=e_td>판매금지</td> --> 
				</tr>

				";


if($db->total == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=16 align=center> 상품통계는 시스템에 부하를 줄수 있습니다. <!--등록된 상품이 없습니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</td></tr>";

}else{
	
	$innerview .= "
				<tr bgcolor='#ffffff' height=30>
					<td class='list_box_td list_bg_gray' colspan=2>합계</td>
					<td class='list_box_td ' >{state_1_cnt_sum}</td>
					<td align=center class='small'>{state_0_cnt_sum}</td>
					<td align=center class='small'>{state_2_cnt_sum}</td> 
					<!-- <td align=center class='small'>{state_7_cnt_sum}</td>
					<td align=center class='small'>{state_6_cnt_sum}</td>
					<td class='list_box_td ' nowrap>{state_8_cnt_sum}</td>
					<td class='list_box_td list_bg_gray' nowrap>{state_9_cnt_sum}</td> -->

					<td class='list_box_td ' >{state_1_cnt_share}</td>
					<td align=center class='small'>{state_0_cnt_share}</td>
					<td align=center class='small'>{state_2_cnt_share}</td> 
					<!-- <td align=center class='small'>{state_7_cnt_share}</td>
					<td align=center class='small'>{state_6_cnt_share}</td>
					<td class='list_box_td ' nowrap>{state_8_cnt_share}</td>
					<td class='list_box_td list_bg_gray' nowrap>{state_9_cnt_share}</td> -->
					 
				</tr>";

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);
 
		$sum_state_row = $db->dt[state_1_cnt]+$db->dt[state_0_cnt]+$db->dt[state_2_cnt]+$db->dt[state_7_cnt]+$db->dt[state_6_cnt]+$db->dt[state_8_cnt]+$db->dt[state_9_cnt];
		$sum_state += $sum_state_row;
	$innerview .= "
				<tr bgcolor='#ffffff' height=30>
					<td class='list_box_td list_bg_gray'>".($i+1)."<!--input type=checkbox class=nonborder id='cpid' name='select_pid[]' value='".$db->dt[id]."'--></td>
					<td class='list_box_td point' style='line-height:140%;padding:5px;text-align:left;'>";
					if($static_div == "category"){
						$innerview .= "".getCategoryPathByAdmin($db->dt[static_div], 4)."";
					}else{
						$innerview .= "".$db->dt[static_div]."";
					}
					$innerview .= "
					</td>
					<td class='list_box_td ' >".$db->dt[state_1_cnt]."</td>
					<td align=center class='small'>".$db->dt[state_0_cnt]."</td>
					<td align=center class='small'>".$db->dt[state_2_cnt]."</td> 
					<!-- <td align=center class='small'>".$db->dt[state_7_cnt]."</td>
					<td align=center class='small'>".$db->dt[state_6_cnt]."</td>
					<td class='list_box_td ' nowrap>".$db->dt[state_8_cnt]."</td>
					<td class='list_box_td list_bg_gray' nowrap>".$db->dt[state_9_cnt]."</td> -->

					<td class='list_box_td ' >".number_format($db->dt[state_1_cnt]/$sum_state_row*100, 1)."</td>
					<td align=center class='small'>".number_format($db->dt[state_0_cnt]/$sum_state_row*100, 1)."</td>
					<td align=center class='small'>".number_format($db->dt[state_2_cnt]/$sum_state_row*100, 1)."</td> 
					<!-- <td align=center class='small'>".number_format($db->dt[state_7_cnt]/$sum_state_row*100, 1)."</td>
					<td align=center class='small'>".number_format($db->dt[state_6_cnt]/$sum_state_row*100, 1)."</td>
					<td class='list_box_td ' nowrap>".number_format($db->dt[state_8_cnt]/$sum_state_row*100, 1)."</td>
					<td class='list_box_td list_bg_gray' nowrap>".number_format($db->dt[state_9_cnt]/$sum_state_row*100, 1)."</td> -->
					 
				</tr>";

				$state_1_cnt_sum += $db->dt[state_1_cnt];
				$state_0_cnt_sum += $db->dt[state_0_cnt];
				$state_2_cnt_sum += $db->dt[state_2_cnt];
				$state_7_cnt_sum += $db->dt[state_7_cnt];
				$state_6_cnt_sum += $db->dt[state_6_cnt];
				$state_8_cnt_sum += $db->dt[state_8_cnt];
				$state_9_cnt_sum += $db->dt[state_9_cnt];

				
	}
	
	$innerview = str_replace("{state_1_cnt_sum}",$state_1_cnt_sum,$innerview);
	$innerview = str_replace("{state_0_cnt_sum}",$state_0_cnt_sum,$innerview);
	$innerview = str_replace("{state_2_cnt_sum}",$state_2_cnt_sum,$innerview);
	$innerview = str_replace("{state_7_cnt_sum}",$state_7_cnt_sum,$innerview);
	$innerview = str_replace("{state_6_cnt_sum}",$state_6_cnt_sum,$innerview);
	$innerview = str_replace("{state_8_cnt_sum}",$state_8_cnt_sum,$innerview);
	$innerview = str_replace("{state_9_cnt_sum}",$state_9_cnt_sum,$innerview); 

	$state_1_cnt_share = $state_1_cnt_sum/$sum_state*100;
	$state_0_cnt_share = $state_0_cnt_sum/$sum_state*100;
	$state_2_cnt_share = $state_2_cnt_sum/$sum_state*100;
	$state_7_cnt_share = $state_7_cnt_sum/$sum_state*100;
	$state_6_cnt_share = $state_6_cnt_sum/$sum_state*100;
	$state_8_cnt_share = $state_8_cnt_sum/$sum_state*100;
	$state_9_cnt_share = $state_9_cnt_sum/$sum_state*100;

	$innerview = str_replace("{state_1_cnt_share}",number_format($state_1_cnt_share,1),$innerview);
	$innerview = str_replace("{state_0_cnt_share}",number_format($state_0_cnt_share,1),$innerview);
	$innerview = str_replace("{state_2_cnt_share}",number_format($state_2_cnt_share,1),$innerview);
	$innerview = str_replace("{state_7_cnt_share}",number_format($state_7_cnt_share,1),$innerview);
	$innerview = str_replace("{state_6_cnt_share}",number_format($state_6_cnt_share,1),$innerview);
	$innerview = str_replace("{state_8_cnt_share}",number_format($state_8_cnt_share,1),$innerview);
	$innerview = str_replace("{state_9_cnt_share}",number_format($state_9_cnt_share,1),$innerview); 

}
	$innerview .= "</table>
				<table width='100%'>
				<tr height=30>
					<td width=210>

					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>
				<tr height=30><td colspan=2 align=right></td></tr>
				</table>

				";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
		<IFRAME id=bsframe name=bsframe src='' frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>
		<!--iframe id='act' src='' width=0 height=0></iframe-->
			";
 

$Script = "<!--link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' />
<script type='text/javascript' src='../js/ui/ui.core.js'></script>
<script type='text/javascript' src='../js/ui/ui.datepicker.js'></script-->
<script language='javascript'>
$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+0d');
		}
	}

	});

	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});



function setSelectDate(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}

function unloading(){

	parent.document.getElementById('parent_save_loading').style.zIndex = '-1';
	parent.document.getElementById('loadingbar').innerHTML ='';
	parent.document.getElementById('save_loading').innerHTML ='';
	parent.document.getElementById('save_loading').style.display = 'none';
}

function ChangeUpdateForm(selected_id){
	var area = new Array('batch_update_display','batch_update_category','batch_update_reserve','batch_update_pos','batch_update_whole','batch_update_product'); //,'batch_update_sms','batch_update_coupon'

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';
			$.cookie('goodsinfo_update_kind', selected_id.replace('batch_update_',''), {expires:1,domain:document.domain, path:'/', secure:0});
		}else{
			document.getElementById(area[i]).style.display = 'none';
		}
	}
}

</script>
";


//$Contents .= HelpBox("".$static_div_name."별 상품통계", $help_text);
$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($cid2, $depth);
	echo "
	<Script>
	//alert(document.body.innerHTML);
	parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
	try{
	parent.document.getElementById('select_category_path1').innerHTML=\"".($search_text == "" ? $inner_category_path."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."\" ;
	}catch(e){}
	parent.document.search_form.cid2.value ='$cid2';
	parent.document.search_form.depth.value ='$depth';
	parent.LargeImageView();
	parent.unblockLoadingBox();
	</Script>";
}else{
	$Script .= "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	<!--script Language='JavaScript' src='product_input.js'></script--><!--2011.06.18 없는게 정상 주석처리후 확인필요-->
	<script Language='JavaScript' src='product_list.js'></script>
	<script Language='JavaScript' src='../js/scriptaculous.js' type='text/javascript'></script>
	<script Language='JavaScript' type='text/javascript'>
	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.getAttribute('depth');
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
		//alert(depth);
		//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		//alert(1);
		// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
		window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	function loadChangeCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;
		//var depth = sel.getAttribute('depth');
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)

		//dynamic.src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
		window.frames['act'].location.href = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	</script>";

	$P = new LayOut();
	$P->strLeftMenu = product_menu();
	$P->addScript = $Script;
	$P->Navigation = "상품관리 > 상품통계 > ".$static_div_name."별 상품통계";
	$P->title = "".$static_div_name."별 상품통계";
	$P->strContents = $Contents;
	$P->jquery_use = false;

	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}
?>