<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
//include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");
include("./inventory.lib.php");

include("../logstory/class/sharedmemory.class");

if($admininfo[admin_level] < 9){
	header("Location:/admin/seller/");
}

if($max == ""){
	$max = 20; //페이지당 갯수
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
		$cut_num = 9;
		break;
}

$where = "where g.gid Is NOT NULL ";


if($search_text != ""){
	$where .= "and g.".$search_type." LIKE '%".trim($search_text)."%' ";
}

if($cid2 != ""){
	//session_register("cid");
	//session_register("depth");
	$where .= " and g.cid LIKE '".substr($cid2,0,$cut_num)."%'";
}


if($sdate != "" && $edate != ""){
	$where .= " and  date_format(g.regdate,'%Y%m%d') between  $sdate and $edate ";
}

//if($admininfo[admin_level] == 9){}

$sql = "SELECT * FROM inventory_goods g $where";
$db2->query($sql);

$total = $db2->total;


$vdate = date("Ymd", time());
$today = date("Ymd", time());
$vyesterday = date("Ymd", time()-84600);
$voneweekago = date("Ymd", time()-84600*7);
$vtwoweekago = date("Ymd", time()-84600*14);
$vfourweekago = date("Ymd", time()-84600*28);
$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));


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
		<td align='left' colspan=4> ".GetTitleNavigation("발주(사입)작성", "발주(사입)관리 > 발주(사입)작성")."</td>
	</tr>

	";

$Contents .=	"
	<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<!--input type='hidden' name='sprice' value='0' />
	<input type='hidden' name='eprice' value='1000000' /-->
	<tr >
		<td colspan=2 >
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
								<td class='input_box_title'>  <b>선택된 카테고리</b>  </td>
								<td class='input_box_item' colspan=3><b id='select_category_path1'>".($search_text == "" ? getIventoryCategoryPathByAdmin($cid2, $depth)."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div></td>
							</tr>
							<tr>
								<td class='search_box_title'><b>카테고리선택</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'>".getInventoryCategoryList("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
											<td style='padding-right:5px;'>".getInventoryCategoryList("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
											<td style='padding-right:5px;'>".getInventoryCategoryList("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
											<td>".getInventoryCategoryList("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>검색어</b></td>
								<td class='search_box_item'>
									<table cellpadding=0 cellspacing=0 >
										<tr >
											<td>
											<select name='search_type'  style=\"font-size:12px;height:20px;\">
												<option value='gid'>품목코드</option>
												<option value='gcode'>대표코드</option>
												<option value='gname'>품목명</option>
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
								<td class='search_box_item'>
									<select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle><!-- onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&depth=$depth&view=innerview&max='+this.value\"-->
									<option value='5' ".CompareReturnValue(5,$max).">5</option>
									<option value='10' ".CompareReturnValue(10,$max).">10</option>
									<option value='20' ".CompareReturnValue(20,$max).">20</option>
									<option value='50' ".CompareReturnValue(50,$max).">50</option>
									<option value='100' ".CompareReturnValue(100,$max).">100</option>
									</select> <span class='small'><!--한페이지에 보여질 갯수를 선택해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
								</td>
							</tr>
							<tr height=27>
							  <td class='search_box_title'><b>등록일자</b></td>
							  <td class='search_box_item' colspan=3 >
								<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
									<col width=70>
									<col width=20>
									<col width=70>
									<col width=*>
									<tr>
										<TD nowrap>
										<input type='text' name='sdate' class='textbox' value='".$sdate."' style='height:20px;width:70px;text-align:center;' id='start_datepicker'>
										</TD>
										<TD align=center> ~ </TD>
										<TD nowrap>
										<input type='text' name='edate' class='textbox' value='".$edate."' style='height:20px;width:70px;text-align:center;' id='end_datepicker'>
										</TD>
										<TD style='padding:0px 10px'>
											<a href=\"javascript:setSelectDate('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
											<a href=\"javascript:setSelectDate('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
											<a href=\"javascript:setSelectDate('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
											<a href=\"javascript:setSelectDate('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
											<a href=\"javascript:setSelectDate('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
											<a href=\"javascript:setSelectDate('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
											<a href=\"javascript:setSelectDate('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
										</TD>
									</tr>
								</table>
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
	</tr>";

$Contents .=	"
	<tr>
		<td valign=top >";

$Contents .= "
		</td>
		<form name=listform method=post  onsubmit='return SelectUpdate(this)'  target='iframe_act' style='display:inline;'><!--onsubmit='return CheckDelete(this)' -->
		<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
		<input type='hidden' id='pid' value=''>
		<input type='hidden' name='act' value='update'>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

$innerview = "<ul class='total_cnt_area' style='width:100%;'>
					<li class='back'>".$str_page_bar."</li>
				  </ul>

			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
				<col width='3%'>
				<col width='10%'>
				<col width='*'>
				<col width='9%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<tr bgcolor='#cccccc' align=center height=30>
					<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<!--td class=m_td>상품코드</td-->
					<td class=m_td>상품분류</td>
					<td class=m_td >상품정보</td>
					<td class=m_td>입고처</td>					
					<td class=m_td>대표코드</td>
					<td class=m_td>단품명</td>
					<td class=m_td>보관장소</td>
					<td class=m_td>입고가(기본)</td>
					<td class=m_td>출고가</td>
					<td class=m_td>재고</td>
					<td class=m_td>안전재고</td>
					<td class=e_td>관리</td>
				</tr>";



$orderbyString = " order by g.regdate desc ";

$sql = "SELECT  g.*, pi.place_name
FROM inventory_goods g left join inventory_place_info pi on g.pi_ix = pi.pi_ix
$where $orderbyString LIMIT $start, $max";


		$sql = "select g.cid,g.gname, g.gcode, g.admin, g.ci_ix, g.pi_ix, pi.place_name,  ifnull(sum(ips.stock),0) as stock, gu.* 
		from inventory_goods g 
		right join inventory_goods_unit gu  on g.gid =gu.gid
		left join  inventory_place_info pi on g.pi_ix = pi.pi_ix
		left join  inventory_product_stockinfo ips on gu.gid = ips.gid and ips.unit = gu.unit
		$where    
		 $stock_where 
		 group by gu.gid , gu.gu_ix, ips.pi_ix
		 $orderbyString 
		 LIMIT $start, $max
		 ";

	


$db->query($sql);
$goods_infos = $db->fetchall();

if(count($goods_infos) == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=11 align=center> 등록된 상품이 없습니다. <!--".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."--></td></tr>";

}else{

	for ($i = 0; $i < count($goods_infos); $i++)
	{
		$db->fetch($i);

		//if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$goods_infos[$i][id].".gif")){
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))) {
			$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c");
		}else{
			$img_str = "../image/no_img.gif";
		}
//".PrintStockByOptionToOrder($goods_infos[$i])."
	$innerview .= "<tr bgcolor='#ffffff' align=center>
						<td class='list_box_td list_bg_gray' align=center><input type=checkbox class=nonborder id='cpid' name='select_pid[]' value='".$goods_infos[$i][id]."'></td>
						<td class='list_box_td ' align=center>".getIventoryCategoryPathByAdmin($goods_infos[$i][cid], 4)."</td>
						<td class='list_box_td point' nowrap>
							<table cellpadding=0 cellspacing=0>
								<tr>
									";
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))) {
		$innerview .= "			<td bgcolor='#ffffff' align=center style='padding:3px 3px' >
										<a href='../inventory/inventory_goods_input.php?gid=".$goods_infos[$i][gid]."' class='screenshot'  rel='".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "basic")."'><img src='".$img_str."' width=30 height=30 style='border:1px solid #efefef'></a>
										</td>";
		}
		$innerview .= "
									
									<td bgcolor='#ffffff' align=left style='font-weight:normal;line-height:140%;'>
									<a href=\"javascript:PoPWindow3('../inventory/inventory_goods_input.php?mmode=pop&gid=".$goods_infos[$i][gid]."',970,800,'goods_info')\"><b>".$goods_infos[$i][gname]."</b></a>
									</td>
								</tr>
							</table>
						</td>
					<td class='list_box_td' style='padding:0px 5px;' nowrap>
						".SelectSupplyCompany($goods_infos[$i][ci_ix],'ci_ix','text','false')."
					</td>
					<td bgcolor=#ffffff>".$goods_infos[$i][gi_ix]."</td>
					<td bgcolor=#ffffff nowrap>".$goods_infos[$i][item_name]."</td>
					<td bgcolor=#ffffff nowrap>".$goods_infos[$i][place_name]."</td>
					<td bgcolor=#ffffff>".number_format($goods_infos[$i][input_price])."</td>
					<td bgcolor=#ffffff>".number_format($goods_infos[$i][basic_sellprice])."</td>
					
					<td bgcolor=#ffffff>".$goods_infos[$i][stock]."</td>
					<td bgcolor=#ffffff>".$goods_infos[$i][item_safestock]."</td>
					<td bgcolor=#ffffff>
						<a href=\"javascript:PoPWindow3('../inventory/order_pop.php?gid=".$goods_infos[$i][gid]."',750,700,'input_pop')\"><img src='../images/".$admininfo["language"]."/bts_order.gif'></a>
					</td>
					<!--td class='list_box_td list_bg_gray'>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_infos[$i][coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td' style='line-height:150%;'>";
if($goods_infos[$i][reserve_yn] == "Y"){
	$innerview .= "		<b>개별적용</b><br>";
}else{
	$innerview .= "		<b>전체정책</b><br>";
}
if ($goods_infos[$i][reserve_yn] == "Y"){
	$innerview .= "		".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_infos[$i][reserve])." ".$currency_display[$admin_config["currency_unit"]]["back"]."";
}else{
		$innerview .= "		".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_infos[$i][sellprice]*$reserve_data[goods_reserve_rate] /100)." ".$currency_display[$admin_config["currency_unit"]]["back"]."";
	}
$innerview .= "
					</td>

					<td class='list_box_td list_bg_gray' nowrap>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_infos[$i][listprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td' nowrap>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_infos[$i][sellprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td list_bg_gray' style='text-align:center;' nowrap>
						<table align=center>
							<tr>
								<td><a href='cart.php?act=add&id=".$goods_infos[$i][id]."&pcount=1' >발주서상품등록</a></td>
							</tr>
						</table>
					</td-->

				</tr>";
	}
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


$Script = "<link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' />
<script type='text/javascript' src='../js/ui/ui.core.js'></script>
<script type='text/javascript' src='../js/ui/ui.datepicker.js'></script>
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
	var area = new Array('batch_update_display','batch_update_category','batch_update_reserve'); //,'batch_update_sms','batch_update_coupon'

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';
		}else{
			document.getElementById(area[i]).style.display = 'none';
		}
	}
}

</script>
";


//$Contents .= HelpBox("발주(사입)작성", $help_text);

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
		var depth = sel.getAttribute('depth');

		if(sel.selectedIndex!=0) {
			window.frames['act'].location.href = 'inventory_category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		}
	}
	</script>";
	if($mmode == "pop"){
		$P = new ManagePopLayOut();
		$P->strLeftMenu = inventory_menu();
		$P->addScript = $Script;
		$P->Navigation = "재고관리 > 발주(사입)요청관리 > 발주(사입)작성 ";
		$P->NaviTitle = "발주(사입)작성 ";
		$P->strContents = $Contents;
		$P->jquery_use = false;

		$P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->strLeftMenu = inventory_menu();
		$P->addScript = $Script;
		$P->Navigation = "재고관리 > 발주(사입)요청관리 > 발주(사입)작성";
		$P->title = "발주(사입)작성";
		$P->strContents = $Contents;
		$P->jquery_use = false;

		$P->PrintLayOut();
	}
}
?>