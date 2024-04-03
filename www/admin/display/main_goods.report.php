<?
include("../class/layout.class");
include("../display/main_display.lib.php");
//print_r($_SESSION["admin_config"][front_multiview] );

if(!$agent_type){
	$agent_type = "W";
}

$db = new Database;
$mdb = new Database;

$Script = "<script language='javascript'>


$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		//}else{
			//$('#end_datepicker').datepicker('setDate','+0d');
		}
	}

	});

	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});


function eventDelete(mg_ix){
	if(confirm(language_data['category_main.list.php']['A'][language]))
	{//'해당 프로모션  정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다.'
		window.frames['act'].location.href= 'main_goods.act.php?act=delete&mg_ix='+mg_ix;//kbk
		//document.getElementById('act').src= 'category_main_goods.act.php?act=delete&mg_ix='+mg_ix;
	}


}

/*
 function loadCategory(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;

	var depth = $('select[name='+sel.name+']').attr('depth');
	//alert('category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}
*/

function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	
	var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
	//document.write('main_position.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	window.frames['act'].location.href = 'main_position.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
}


</script>";


$mstring ="<form name=serchform ><input type='hidden' name='cid2' value=''><input type='hidden' name='depth' value=''>
		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center style=''>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("메인페이지 전시 결과분석", "전시관리 > 메인페이지 전시 결과분석 ")."</td>
		</tr>";
 

$mstring .= "<tr>
			<td align='left' >								
				<div class='tab' style='width:100%;height:29px;margin:0px;'>
				<table width='100%' class='s_org_tab'>				
				<tr>							
					<td class='tab' >
						<table id='tab_1' >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='main_goods.list.php'\">".($agent_type == "M" ? "모바일":"")." 메인페이지 전시목록</td>
							<th class='box_03'></th>							
						</tr>
						</table>
						<table id='tab_2' class='on'>
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='main_goods.report.php'\">".($agent_type == "M" ? "모바일":"")." 메인페이지 전시 결과분석</td>
							<th class='box_03'></th>				
						</tr>
						</table>
					</td>							
					<td align='right'>
						<!--a href='#;' onclick='FnDisplayWrite()'><img src='../images/".$admininfo["language"]."/btn_disp_write.gif' align=absmiddle ></a>&nbsp;
						<a href='#;' onclick='location.href=\"display_div.php?display_div=".$display_div."\"'><img src='../images/".$admininfo["language"]."/btn_disp_div.gif' align=absmiddle ></a-->
						<a href='main_goods_category.php'><img src='../images/".$admininfo["language"]."/btn_disp_div.gif' align=absmiddle></a>
					</td>
				</tr>
				</table>										
				</div>					
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
					<col width=10%>
					<col width=25%>
					<col width=10%>
					<col width=25%>
					<col width=20%>";
					if($_SESSION["admin_config"][front_multiview] == "Y"){
					$mstring .= "
					<tr>
						<td class='search_box_title' > 프론트 전시 구분</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
					</tr>";
					}
					$mstring .= "
					<tr>
						<td class='search_box_title' > 메인전시 위치</td>
						<td class='search_box_item' colspan=3>
						".getMainGroupInfo($div_ix)."
                        ".getMainGroupPosition($div_ix, $mp_ix)."
						</td>
					</tr>
					<tr>
						<td class='search_box_title' > 전시 분류명</td>
						<td class='search_box_item'>
						<select name='search_type'>
							<option value='combination' ".(($search_type == 'combination')?"selected":"").">전시명+전시코드</option>
							<option value='div_name' ".(($search_type == 'div_name')?"selected":"").">전시 분류명</option>
							<option value='mg_title' ".(($search_type == 'mg_title')?"selected":"").">전시명</option>
						</select>
						<input type='text' class=textbox style='width: 210px; ' name=search_text  title='분류명'> 
						</td>
						<td class='search_box_title' >  전시여부  </td>
						<td class='search_box_item'>
							<input type='radio' name='disp'  id='disp_' value='' ".ReturnStringAfterCompare($disp, "", " checked")."><label for='disp_'>전체</label>
							<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>노출함</label>
							<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>노출안함</label>
						</td>
					</tr>
					<tr>
						<td class='search_box_title' > 담당 MD</td>
						<td class='search_box_item'> ".MDSelect($md_mem_ix,"md_code","md_name","","")."</td>
						<td class='search_box_title' >  진행상태  </td>
						<td class='search_box_item'>
							<input type='radio' name='display_status'  id='display_status_' value='' ".ReturnStringAfterCompare($display_status, "", " checked")."><label for='display_status_'>전체</label>
							<input type='radio' name='display_status'  id='display_status_1' value='1' ".ReturnStringAfterCompare($display_status, "1", " checked")."><label for='display_status_1'>진행예약</label>
							<input type='radio' name='display_status'  id='display_status_2' value='2' ".ReturnStringAfterCompare($display_status, "2", " checked")."><label for='display_status_2'>진행중</label>
							<input type='radio' name='display_status'  id='display_status_3' value='3' ".ReturnStringAfterCompare($display_status, "3", " checked")."><label for='display_status_3'>진행완료</label>
						</td>
					</tr>
					<tr>
						<td class='search_box_title' nowrap>
							<label for='search_date'><b>날짜 검색</b></label><input type='checkbox' name='search_date' id='search_date' value='1' onclick='ChangeRegistDate(document.search_banner);' ".(($search_date==1)?"checked":"").">
							<select name='date_type'>
								<option value='use' ".(($date_type == 'use')?"selected":"").">만료기간</option>
								<option value='reg' ".(($date_type == 'reg')?"selected":"").">등록일자</option>
							</select>
						  </td>
						  <td class='search_box_item'  colspan=3>
						  ".search_date('use_sdate','use_edate',$use_sdate,$use_edate,'N','D')."	";
						  /*
						$mstring .= "
							<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100%>
								<col width=35>
								<col width=15>
								<col width=35>
								<col width=20>
								<col width=35>
								<col width=15>
								<col width=35>
								<col width=*>
								<tr>
									<td  nowrap>
									<input type='text' name='use_sdate' class='textbox' value='".$use_sdate."' style='height:18px;width:70px;text-align:center;'  id='start_datepicker'>
									</td>
									<td>  &nbsp;일</td>
									<td nowrap>
									<SELECT name=FromHH>";
													for($i=0;$i < 24;$i++){
									$mstring.= "<option value='".$i."' ".($FromHH == $i ? "selected":"").">".$i."</option>";
													}
									$mstring.= "
													</SELECT> 시
													<SELECT name=FromMI>";
													for($i=0;$i < 60;$i++){
									$mstring.= "<option value='".$i."' ".($FromMI == $i ? "selected":"").">".$i."</option>";
													}
									$mstring.= "
													</SELECT> 분
									</td>
									<td align=center> ~ </td>
									<td nowrap>
									<input type='text' name='use_edate' class='textbox' value='".$use_edate."' style='height:18px;width:70px;text-align:center;' id='end_datepicker'>
									</td>
									<td> &nbsp;일</td>
									<td nowrap>
									<SELECT name=ToHH>";
													for($i=0;$i < 24;$i++){
									$mstring.= "<option value='".$i."' ".(($ToHH == $i || $i == 23) ? "selected":"").">".$i."</option>";
													}
									$mstring.= "
													</SELECT> 시
													<SELECT name=ToMI>";
													for($i=0;$i < 60;$i++){
									$mstring.= "<option value='".$i."' ".(($ToMI == $i || $i == 59) ? "selected":"").">".$i."</option>";
													}
									$mstring.= "
													</SELECT> 분
									</td>
									<td style='padding:0px 10px'>
										<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
										<a href=\"javascript:select_date('$today','$voneweeklater',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
										<a href=\"javascript:select_date('$today','$v15later',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
										<a href=\"javascript:select_date('$today','$vonemonthlater',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
										<a href=\"javascript:select_date('$today','$v2monthlater',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
										<a href=\"javascript:select_date('$today','$v3monthlater',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
									</td>
								</tr>
							</table>";
*/
$mstring .= "
						  </td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
				<td align ='center'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle></td>
		</tr>
		<tr>
			<td align='left' colspan=4 style='padding-bottom:10px;'>
			    <div class='tab'>
					<table class='s_org_tab' style='width:100%' border=0>
					<tr>
						<td class='tab'>
							<table id='tab_01' ".($div_ix == "" ? "class='on'":"")."  >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?div_ix='\">전체보기</td>
								<th class='box_03'></th>
							</tr>
							</table>";
$sql = 	"SELECT * FROM shop_main_div where disp=1  and agent_type ='".$agent_type."'    ";

$db->query($sql);
for($i=0;($i < 5 && $i < $db->total);$i++){
	$db->fetch($i);
$mstring .= "<table id='tab_".($i+2)."' ".($div_ix == $db->dt[div_ix] ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?div_ix=".$db->dt[div_ix]."'\">".$db->dt[div_name]."</td>
								<th class='box_03'></th>
							</tr>
							</table>";
}

$mstring .= "<div style='padding:0px 0px 4px 30px'><select name='menu_div' id='menu_div' style='border:1px solid silver;padding:1px;margin-left:4px;' onchange=\"document.location.href='?div_ix='+this.value\">";
		$mstring .= "<option value=''>메뉴분류</option>";
		if($db->total){
			for($i=$i;$i < $db->total;$i++){
				$db->fetch($i);
				if($db->dt[div_name] == $selected){
					$mstring .= "<option value='".$db->dt[div_ix]."' selected>".$db->dt[div_name]."</option>";
				}else{
					$mstring .= "<option value='".$db->dt[div_ix]."'>".$db->dt[div_name]."</option>";
				}
			}

		}
		$mstring .= "</select></div>";
$mstring .= "
						</td>
						<td class='btn' align=right>";
                     
                        $mstring.="
						</td>
					</tr>
					</table>
					</div>
			</td>
		</tr>";

$mstring .= "
		<tr>
			<td>
			".mainPromotionStatistics()."
			</td>
		</tr>
		</form>";
$mstring .="</table>";

/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>프로모션 상품 추가</b>를 원하시면 이벤트 추가버튼을 클릭해주세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기간이 만료된 프로모션 상품은 자동으로 노출이 종료됩니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


$help_text = HelpBox("메인페이지 전시 결과분석", $help_text);

$Contents = $mstring.$help_text;
$Contents .= "<div style='height:120px;'></div>";
if($agent_type == "M"  || $agent_type == "mobile"){
	$P = new LayOut();
	$P->addScript = $Script;
	$P->Navigation = $navigation;
	$P->title = $title;
	$P->strLeftMenu = mshop_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->Navigation = "메인페이지 전시관리 > 메인페이지 전시관리 > 메인페이지 전시 결과분석";
	$P->title = "메인페이지 전시 결과분석";
	$P->strLeftMenu = display_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function mainPromotionStatistics(){
	global $db, $mdb, $admin_config, $div_ix ,$admininfo;
	global $auth_write_msg, $auth_delete_msg, $auth_update_msg,$product_image_column_str;
	global $page, $max, $nset;
	global $agent_type;

	if($_GET[cid2] !="" ){
		$where ="and cmd.cid = '".$_GET[cid2]."' ";
	}

	if($_GET[search_type] !="" || $_GET[search_text] !="" ){
		if($_GET["search_type"] == "combination"){
			$where .="and ( mg.mg_title like '%".$_GET[search_text]."%' ) ";
		}else{
			$where .="and ".$_GET["search_type"]." like '%".$_GET[search_text]."%' ";
		}
	}

	if($_GET[disp] !="" ){
		$where .="and mg.disp = '".$_GET[disp]."' ";
	}

	if($_GET[mall_ix] !="" ){
		$where .="and mg.mall_ix = '".$_GET[mall_ix]."' ";
	}

	if($_GET[mp_ix] !="" ){
		$where .="and mg.mp_ix = '".$_GET[mp_ix]."' ";
	}

	if($_GET[div_ix] !="" ){
		$where .="and mg.div_ix = '".$_GET[div_ix]."' ";
	}

	if($_GET[display_status] == "1" ){
		$where .="and ".time()." <  mg.mg_use_sdate ";
	}else if($_GET[display_status] == "2" ){
		$where .="and ".time()." between   mg.mg_use_sdate  and  mg.mg_use_edate ";
	}else if($_GET[display_status] == "3" ){
		$where .="and ".time()." > mg.mg_use_edate ";
	}

	
	$sql = "select count(*) as total 
				from shop_main_goods mg left join shop_main_div cmd on mg.div_ix = cmd.div_ix 
				where mg.disp = 1 and mg.agent_type ='".$agent_type."'  
				$where";
	
	//echo nl2br($sql);
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];

	$max = 20;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=4 cellspacing=0 border=0 width=100% class='list_table_box' >
						<col width=3%> 
						".($_SESSION["admin_config"]["front_multiview"] == "Y" ? "<col style='width:5%;'>":"")."
						<col width='9%'>
						<col width='5%'>
						<col width='12%'>						
						<col width='5%'>
						<col width='7%'>
						<col width='7%'>
						<col width='5%'>
						<col width='6%'>
						<col width='6%'>
						<col width='5%'>
						<col width='5%'>
						<col width='5%'>
						<col width='5%'>
						<col width='7%'>";
	$mString .= "<tr height=30 align=center>
		<td align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
		".($_SESSION["admin_config"][front_multiview] == "Y" ? "<td class='m_td' nowrap> 프론트전시</td>":"")."
		<td class=s_td >전시그룹</td>
		<td class=m_td nowrap>전시위치</td>
		<td class=m_td >전시명</td>
		<td class=m_td >상품수</td>
		<td class=m_td >담당 MD</td>
		<td class=m_td >노출기간</td>
		<td class=m_td >클릭건</td>
		<td class=m_td nowrap>구매건/수량</td>
		<td class=m_td nowrap>취소건/수량</td>
		<td class=m_td nowrap>구매전환율</td>
		<td class=m_td >매출액</td>
		<td class=m_td nowrap>목표매출액</td>
		<td class=m_td nowrap>목표달성율</td>
		<td class=e_td >관리</td>
		</tr>";
		//echo ($total);
	if ($total == 0){
		$mString .= "<tr bgcolor=#ffffff><td height=70 colspan=16 align=center>".($agent_type == "M" ? "모바일":"")." 메인전시 내역이 존재 하지 않습니다.</td></tr>";
	}else{

		$sql ="select * from shop_main_product_group where mpg_ix <>'0' ";
		$db->query($sql);
		$group_info = $db->fetch();

	 
		$sql = "select mg.mg_ix, mg.mall_ix, mg.md_mem_ix, mg.mg_title, cmd.div_name, mg.disp, mg.mg_use_sdate, mg.mg_use_edate , mg.goal_amount ,
					case when '".date("Ymd")."' between mg.mg_use_sdate and mg.mg_use_edate then 1 else 0 end as is_live, 
					IFNULL(sum(lmc.ncnt),0) as ncnt, IFNULL(sum(od.pcnt),0) as pcnt, 
					sum(case when od.status in ('CC','SC') then pcnt else 0 end) as cancel_cnt,
					IFNULL(sum(od.ptprice),0) as ptprice
					from shop_main_goods mg 					
					left join shop_main_product_relation mpr on mg.mg_ix = mpr.mg_ix
					left join shop_order_detail od on mpr.mpr_ix = od.mpr_ix and mpr.pid = od.pid and od.mpr_ix != '' 
					left join shop_main_div cmd on mg.div_ix = cmd.div_ix
					left join logstory_maingoods_click lmc on mpr.mpr_ix = lmc.mpr_ix					
					where mg.disp = 1 and mg.agent_type ='".$agent_type."'  
					$where 
					group by mg.mg_ix
					order by  mg.regdate desc 
					limit $start, $max";
	
		//echo nl2br($sql);
		//exit;
		$db->query($sql);

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
			//$no = $no + 1;
			$no = $total - ($page - 1) * $max - $i;

			$sql = "select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from ".TBL_COMMON_MEMBER_DETAIL." cmd where code= '".$db->dt[md_mem_ix]."' ";
			//echo $sql;
			$mdb->query($sql);
			$mdb->fetch();
			$md_name = $mdb->dt[name];

			$sql = "SELECT distinct p.id, p.pcode, p.pname, p.sellprice,
							 p.reserve, mg_ix, mpr_ix, cmpr.vieworder ".$product_image_column_str."
							FROM ".TBL_SHOP_PRODUCT." p, shop_main_product_relation cmpr
							where p.id = cmpr.pid and cmpr.mg_ix = '".$db->dt[mg_ix]."'  and p.disp = 1 order by cmpr.vieworder asc ";
			$mdb->query($sql);



			$mString .= "<tr height=27 style='".($db->dt[is_live] ? "font-weight:bold;":"")."'>
			<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$display_ix."'></td>
			";
if($_SESSION["admin_config"]["front_multiview"] == "Y"){
	$mString .= "
		    <td class='list_box_td list_bg_gray'>".GetDisplayDivision($db->dt[mall_ix], "text")."</td>";
}
	$mString .= "
			<td class='list_box_td ' style='line-height:150%;'>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$mString .= "<a href='main_goods.php?mg_ix=".$db->dt[mg_ix]."'>".$db->dt[div_name]."</a>";
			}else{
				$mString .= "<a href=\"".$auth_update_msg."\">".$db->dt[div_name]."</a>";
			}
			//$mString .= "<div >상품수 : </div>";
			$mString .= "</td>
			<td class='list_box_td list_bg_gray' >".$db->dt[mp_name]."</td>
			<td class='list_box_td' >".$db->dt[mg_title]."</td>
			<td class='list_box_td' >".($mdb->total)."</td>
			<td class='list_box_td' >".$md_name."</td>";
		 
			$mString .= "
			<!--td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ? "노출":"노출안함")."</td-->			 
			<td class='list_box_td list_bg_gray'>".date("Y.m.d", $db->dt[mg_use_sdate])." ~ ".date("Y.m.d",$db->dt[mg_use_edate])."</td>
			<td class='list_box_td '>".number_format($db->dt[ncnt])."</td>
			<td class='list_box_td list_bg_gray'>".number_format($db->dt[pcnt])."</td>
			<td class='list_box_td '>".number_format($db->dt[cancel_cnt])."</td>
			<td class='list_box_td list_bg_gray'>".($db->dt[ncnt] == "0" ? "-":number_format($db->dt[pcnt]/$db->dt[ncnt],2)." %")."</td>
			<td class='list_box_td number'>".number_format($db->dt[ptprice])."</td>
			<td class='list_box_td list_bg_gray'>".($db->dt[goal_amount])."</td>
			<td class='list_box_td '>".($db->dt[goal_amount] == 0 ? "-":$db->dt[ptprice]/$db->dt[goal_amount]." %")."</td>
			<td class='list_box_td list_bg_gray'> <!--상세 확인-->";
			/*
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$mString .= "<a href='main_goods.php?mg_ix=".$db->dt[mg_ix]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a> ";
			}else{
				$mString .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$mString .= "<a href=\"JavaScript:eventDelete('".$db->dt[mg_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
			$mString .= "<a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			*/
			$mString .= "
			</td>
			</tr>
			";
		}


	}
	$mString .= "</table>";
	$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100% >
				<tr height=50 bgcolor=#ffffff>
					<td colspan=3 align=left>".page_bar($total, $page, $max,  "&max=$max","")."</td>
					<td colspan=3 align=right>";
					
					/*
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
						$mString .= "<a href='main_goods.php?div_ix=$div_ix'><img src='../images/".$admininfo["language"]."/b_promotionadd.gif' border=0 ></a>";
					}else{
						$mString .= "<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_promotionadd.gif' border=0 ></a>";
					}
					*/
					$mString .= "
					</td>
				</tr>";
	$mString .= "</table>";

	return $mString;
}


?>
