<?
include("../class/layout.class");
include("./display.lib.php");
include("./category_main.lib.php");

$db = new Database;
$mdb = new Database;

$Script = "<script language='javascript'>
function eventDelete(cmg_ix){
	if(confirm(language_data['category_main.list.php']['A'][language]))
	{//'해당 프로모션  정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다.'
		window.frames['act'].location.href= 'category_main_goods.act.php?act=delete&cmg_ix='+cmg_ix;//kbk
		//document.getElementById('act').src= 'category_main_goods.act.php?act=delete&cmg_ix='+cmg_ix;
	}


}

function loadPosition(sel,target) {
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	window.frames['act'].location.href = '/admin/display/category_main_position.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target;
}

 function loadCategory(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;

	var depth = $('select[name='+sel.name+']').attr('depth');

	window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}

/*
$(document).ready(function() {
	$('.list_table_box').each(function() {
		$(this).selectable({ 
			'filter' : 'tr',
			'selected': function(event, ui) {
				$(ui.selected).find('input[type=\"checkbox\"]').each(function() {
					this.checked = true;
				});
			}
		});
	});
});
*/
</script>";


$mstring ="<form name=serchform ><input type='hidden' name='cid2' value=''><input type='hidden' name='depth' value=''>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style=''>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("분류별 메인관리", "전시관리 > 분류별 메인관리 ")."</td>
		</tr>";
/*
$mstring .= "
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
$sql = 	"SELECT * FROM shop_category_main_div where disp=1 ";

$db->query($sql);
$c_cnt=$db->total;
for($i=0;$i < $c_cnt;$i++){
	$db->fetch($i);
$mstring .= "<table id='tab_".($i+2)."' ".($div_ix == $db->dt[div_ix] ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?div_ix=".$db->dt[div_ix]."'\">".$db->dt[div_name]."</td>
								<th class='box_03'></th>
							</tr>
							</table>";
}
$mstring .= "<div style='padding:0px 0px 4px 30px'><select name='menu_div' id='div_ix' style='border:1px solid silver;padding:1px;margin-left:4px;' onchange=\"document.location.href='?div_ix='+this.value\">";
		$mstring .= "<option value=''>프로모션 분류</option>";
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

							$mstring .= "<a href='category_main_div.php'><img src='../images/".$admininfo["language"]."/btn_promotion_type.gif' align=absmiddle></a>";
$mstring .= "
						</td>
					</tr>
					</table>
					</div>
			</td>
		</tr>";
*/

$mstring .= "<tr>
			<td align='left' >								
				<div class='tab' style='width:100%;height:32px;margin:0px;'>
				<table width='100%' class='s_org_tab'>				
				<tr>							
					<td class='tab' >
						<table id='tab_1' class='on'>
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='category_main.list.php'\">분류별 전시관리</td>
							<th class='box_03'></th>							
						</tr>
						</table>
						<table id='tab_2' ".$tabmenu_class2.">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='category_main.report.php'\">분류별 전시 결과분석</td>
							<th class='box_03'></th>				
						</tr>
						</table>
					</td>							
					<td align='right'>
						<!--a href='#;' onclick='FnDisplayWrite()'><img src='../images/".$admininfo["language"]."/btn_disp_write.gif' align=absmiddle ></a>&nbsp;
						<a href='#;' onclick='location.href=\"display_div.php?display_div=".$display_div."\"'><img src='../images/".$admininfo["language"]."/btn_disp_div.gif' align=absmiddle ></a-->
						<a href='category_main_div.php'><img src='../images/".$admininfo["language"]."/btn_disp_div.gif' align=absmiddle></a>
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
					<col width=20%>
					<tr height=28>
						<td class='search_box_title'  nowrap> <b>분류 메인 전시 분류</b></td>
						<td class='search_box_item'>".getCategoryMainDiv($div_ix,"selectbox"," onchange=\"loadPosition(this,'display_position')\" ")."</td>
						<td class='search_box_title' nowrap> <b>분류 전시 위치</b></td>
						<td class='search_box_item'> 
							".getCategoryMainPosition($div_ix,$display_position)."
							<!--".($display_position == "" ? getCategoryMainPositionSelectBox("display_position",$display_position):getCategoryMainPositionSelectBox("display_position",$display_position,"text"))."-->
						</td>
					</tr>
					<tr>
						<td class='search_box_title' >  카테고리선택</td>
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
					<tr>
						<td class='search_box_title' > 검색어</td>
						<td class='search_box_item' >
							<table cellpadding=0 cellspacing=0>
								<tr>
									<td align=left>
										<select name=search_type>
											<option value='cmg_title' ".CompareReturnValue("cmg_title",$search_type,"selected")." style='vertical-align:middle;'>전시 분류명</option>
											<option value='name' ".CompareReturnValue("name",$search_type,"selected")." style='vertical-align:middle;'>담당 MD</option>
									  </select>
									 </td>
									 <td style='padding:0px 4px;'>
										<input type='text' class=textbox style='width: 210px; ' name=search_text  title='분류명'> 
									</td>
								</tr>
							</table>
						</td>
						<td class='search_box_title' > 노출순서</td>
						<td class='search_box_item'>
							<input type='radio' name=vieworder_type id=vieworder_type_1 value='1'  title='등록일자순' ".($vieworder_type == "1" || $vieworder_type == ""  ? "checked":"")."><label for='vieworder_type_1'>등록일자순</label>
							<input type='radio' name=vieworder_type id=vieworder_type_2 value='2'  title='전시우선순으로보기' ".($vieworder_type == "2"  ? "checked":"")."><label for='vieworder_type_2'>전시우선순으로보기</label>
						</td>
					</tr>
					<tr>
						<!--td class='search_box_title' > 담당 MD</td>
						<td class='search_box_item'><input type='text' class=textbox style='width: 210px; ' name=search_text  title='분류명'> </td-->
						<td class='search_box_title' >  전시여부  </td>
						<td class='search_box_item'>
							<input type='radio' name='disp'  id='disp_' value='' ".ReturnStringAfterCompare($disp, "", " checked")."><label for='disp_'>전체</label>
							<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>노출함</label>
							<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>노출안함</label>
						</td>
						<td class='search_box_title' >  진행상태  </td>
						<td class='search_box_item'>
							<input type='radio' name='status'  id='status_' value='' ".ReturnStringAfterCompare($status, "", " checked")."><label for='status_'>전체</label>
							<input type='radio' name='status'  id='status_1' value='1' ".ReturnStringAfterCompare($status, "1", " checked")."><label for='status_1'>진행예약</label>
							<input type='radio' name='status'  id='status_2' value='2' ".ReturnStringAfterCompare($status, "2", " checked")."><label for='status_2'>진행중</label>
							<input type='radio' name='status'  id='status_3' value='3' ".ReturnStringAfterCompare($status, "3", " checked")."><label for='status_3'>진행완료</label>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
				<td align ='center' style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle></td>
		</tr>
		<tr>
			<td align='left' colspan=4 style='padding-bottom:15px;'>
			    <div class='tab'>
					<table class='s_org_tab' style='width:100%' border=0>
					<tr>
						<td class='tab'>
							<!--table id='tab_01' ".($cid == "" ? "class='on'":"")."  >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?div_ix='\">전체보기</td>
								<th class='box_03'></th>
							</tr>
							</table-->";
$sql = 	"SELECT div_ix, div_name FROM shop_category_main_div where disp=1   ";

$db->query($sql);
for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
$mstring .= "<table id='tab_".($i+2)."' ".($div_ix == $db->dt[div_ix] ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?div_ix=".$db->dt[div_ix]."'\">".$db->dt[div_name]."".getCategoryPathByAdmin($db->dt[cid], $db->dt[depth])."</td>
								<th class='box_03'></th>
							</tr>
							</table>";
}
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
			".PrintPromotionGoods()."
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


$help_text = HelpBox("분류별 메인관리", $help_text);

$Contents = $mstring.$help_text;
$Contents .= "<div style='height:120px;'></div>";

$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션/전시 > 분류 전시관리 > 분류별 메인관리 목록";
$P->title = "분류별 메인관리 목록";
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();



function PrintPromotionGoods(){
	global $db, $mdb, $admin_config, $div_ix ,$admininfo, $cid, $search_type;
	global $auth_write_msg, $auth_delete_msg, $auth_update_msg,$product_image_column_str;
	global $nset, $page, $max;

	//$where =" 1 "; 
	if($_GET[div_ix] !="" ){
		$where .=" and cmg.div_ix = '".$_GET[div_ix]."' ";
	}

	if($_GET[cid2] !="" ){
		//$where ="and cmd.cid = '".$_GET[cid2]."' ";
		$where .= " and cmg.display_cid LIKE '".substr($_GET[cid2], 0,($_GET["depth"]+1)*3)."%' ";
	}

	if($_GET[display_position] !="" ){
		$where ="and cmg.display_position = '".$_GET[display_position]."' ";
	}

	if($_GET[cid] !="" ){
		//$where ="and cmd.cid = '".$_GET[cid]."' ";
		$where ="and cmg.display_cid LIKE '".substr($_GET[cid],0,3)."%' ";
	}

	if($_GET[search_text] !="" ){
		if($_GET[search_type] =="name" ){
			$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%".$_GET[search_text]."%' ";
		}else{
			$where .="and ".$search_type." like '%".$_GET[search_text]."%' ";
		}
	}

	if($_GET[disp] !="" ){
		$where .="and cmg.disp = '".$_GET[disp]."' ";
	}

	if($_GET[status] == "1" ){//진행예약
		$where .="and cmg.cmg_use_sdate > NOW() ";
	}else if($_GET[status] == "2" ){//진행중
		$where .="and NOW() between cmg.cmg_use_sdate and cmg.cmg_use_edate  ";
	}else if($_GET[status] == "3" ){//진행완료
		$where .="and cmg.cmg_use_edate < NOW() ";
	}

	
	/*
	if($div_ix){
		$sql = "select count(*) as total 
					from shop_category_main_goods cmg 
					left join shop_category_main_div cmd on cmg.div_ix = cmd.div_ix 
					left join common_member_detail cmd2 on cmg.md_mem_ix = cmd2.code
					left join shop_category_main_position cmp on cmg.display_position = cmp.cmp_ix
					where  cmd.disp = 1 
					and cmg.div_ix = '$div_ix'";
	}else{
	*/
		$sql = "select count(*) as total 
					from shop_category_main_goods cmg 
					left join shop_category_main_div cmd on cmg.div_ix = cmd.div_ix 
					left join shop_category_main_position cmp on cmg.display_position = cmp.cmp_ix
					left join common_member_detail cmd2 on cmg.md_mem_ix = cmd2.code
					where cmd.disp = 1 $where";

	//}
	//echo nl2br($sql);
//echo "<br><br>";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];
//echo $total;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=4 cellspacing=0 border=0 width=100% class='list_table_box' >
						<col width=3%>
						<col width=5%>
						<col width='10%'>
						<col width='10%'>						
						<col width='15%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='13%'>
						<col width='11%'>";
	$mString .= "<tr height=30 align=center>
		<td class=s_td align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
		<td class=m_td align='center' class='m_td'><font color='#000000'><b>번호</b></font></td>
		<td class=m_td >노출카테고리</td>
		<td class=m_td >전시분류</td>
		<td class=m_td >전시명/전시위치</td>
		<td class=m_td >상품수</td>
		<td class=m_td >담당 MD</td>
		<td class=m_td >노출여부</td>
		<!--td class=m_td >전시타입</td-->
		<td class=m_td >노출기간</td>
		<td class=e_td >관리</td>
		</tr>
		<tr>
			
		</tr>";
		//echo ($total);
	if ($total == 0){
		$mString .= "<tr bgcolor=#ffffff><td height=70 colspan=10 align=center>분류별 전시내역이 존재 하지 않습니다.</td></tr>";
	}else{

		$sql ="select * from shop_category_main_product_group where cmpg_ix <>'0' ";
		$db->query($sql);
		$group_info = $db->fetch();

		/*if($div_ix){
			$sql = "select cmg.cmg_ix, cmg.cmg_title, cmd.div_name, cmd.cid, cmg.display_cid,  cmg.disp, cmg.cmg_use_sdate, cmg.cmg_use_edate , cmg.md_mem_ix,cmp.cmp_name, 
						case when '".date("Ymd")."' between cmg.cmg_use_sdate and cmg.cmg_use_edate then 1 else 0 end as is_live
						from shop_category_main_goods cmg 
						left join shop_category_main_div cmd  on cmg.div_ix = cmd.div_ix 
						left join shop_category_main_position cmp on cmg.display_position = cmp.cmp_ix
						left join common_member_detail cmd2 on cmg.md_mem_ix = cmd2.code
						where  cmd.disp = 1 and cmg.div_ix = '$div_ix' 
						order by  cmg.regdate desc 
						limit $start, $max";
		}else{
		*/
		if($_GET["vieworder_type"] == "2"){
			$orderby_str = " order by  cmp.vieworder desc ";
		}else{
			$orderby_str = " order by  cmg.regdate desc ";
		}
			$sql = "select cmg.cmg_ix, cmg.cmg_title, cmd.div_name, cmd.cid, cmg.display_cid, cmg.disp, cmg.cmg_use_sdate, cmg.cmg_use_edate , cmg.md_mem_ix, cmp.cmp_name, 
						case when '".date("Ymd")."' between cmg.cmg_use_sdate and cmg.cmg_use_edate then 1 else 0 end as is_live
						from shop_category_main_goods cmg 
						left join shop_category_main_div cmd on cmg.div_ix = cmd.div_ix 
						left join shop_category_main_position cmp on cmg.display_position = cmp.cmp_ix
						left join common_member_detail cmd2 on cmg.md_mem_ix = cmd2.code
						where cmd.disp = 1 $where 
						".$orderby_str."
						limit $start, $max";
		//}
		//echo nl2br($sql);
		$db->query($sql);

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
			//$no = $no + 1;
			$no = $total - ($page - 1) * $max - $i;


			$sql = "select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from ".TBL_COMMON_MEMBER_DETAIL." cmd where code= '".$db->dt[md_mem_ix]."' ";
			//echo $sql;
			$mdb->query($sql);
			if($mdb->total){
				$mdb->fetch();
				$md_name = $mdb->dt[name];
			}else{
				$md_name = "-";
			}

			$sql = "SELECT distinct p.id, p.pcode, p.pname, p.sellprice,
							 p.reserve, cmg_ix, cmpr_ix, cmpr.vieworder ".$product_image_column_str."
							FROM ".TBL_SHOP_PRODUCT." p, shop_category_main_product_relation cmpr
							where p.id = cmpr.pid and cmpr.cmg_ix = '".$db->dt[cmg_ix]."'  and p.disp = 1 
							order by cmpr.vieworder asc ";
			$mdb->query($sql);

			$mString .= "
			<tr height=27 style='".($db->dt[is_live] ? "font-weight:bold;":"")."'>
			<td class='list_box_td' ><input type=checkbox name=code[] id='code' value='".$display_ix."'></td>
			<td class='list_box_td' >".$no."</td>
			<td align=center><b>".getCategoryPathByAdmin($db->dt[display_cid],4)."</td>
			<td class='list_box_td list_bg_gray' style='line-height:150%;padding:5px 0px;'>
			";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				//$mString .= "<a href='category_main_goods.php?cmg_ix=".$db->dt[cmg_ix]."'>".$db->dt[div_name]."</a>";
				$mString .= "<b>".$db->dt[div_name]."</b> ";
			}else{
				$mString .= "<a href=\"".$auth_update_msg."\">".$db->dt[div_name]."</a>";
			}
			//$mString .= "<div >상품수 : </div>";
			$mString .= "</td>
			<td class='list_box_td' style='text-align:left;line-height:150%;padding:5px;'>".$db->dt[cmg_title]."<br><b>".$db->dt[cmp_name]."</b></td>
			<td class='list_box_td' >".($mdb->total)."</td>
			<td class='list_box_td' >".$md_name."</td>";
			/*
			$mString .= "
			<td class='list_box_td' style='padding:10px'>";
			$mString .= "<div style='display:block'>";
			for($j=0;($j < 7 && $j < $mdb->total);$j++){
				$mdb->fetch($j);

				//$mString .= "<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif' title='[".$db->dt[id]."]".$db->dt[pname]."'>\n";

				$mString .= "<div style='border:1px solid silver;display:block;float:left;text-align:center;margin:2px;'><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[id], "c",$mdb->dt)."' ></div>";

			}
			$mString .= "</div>";
			$mString .= "
			</td>";
			*/
			$mString .= "
			<td class='list_box_td list_bg_gray' >".$db->dt[vieworder]."<br>".($db->dt[disp] == "1" ? "노출":"노출안함")."</td>
			<!--td class='list_box_td ' style='padding:5px;' >";

			switch($group_info['display_type']) {
				case (0) : $display_type_txt= "기본형(5EA 배열)"; $display_img = "g_5.gif";
				break;
				case (1) : $display_type_txt= "기본형(4EA 배열)"; $display_img = "g_4.gif";
				break;
				case (2) : $display_type_txt= "기본형2(3EA 배열)"; $display_img = "g_3.gif";
				break;
				case (3) : $display_type_txt= "슬라이드형(4EA 배열)"; $display_img = "slide_4.gif";
				break;
				case (4) : $display_type_txt= "기본형4(1/*EA 배열) "; $display_img = "g_16.gif";
				break;
				case (5) : $display_type_txt= "기본형(4EA 배열)"; $display_img = "g_17.gif";
				break;
				case (6) : $display_type_txt= "기본형(2/4EA 배열)"; $display_img = "g_24.gif";
				break;
				default : $display_type_txt= "기본형(5EA 배열)";  $display_img = "g_5.gif";
				break;
			}

			$mString .= "<div style='margin-top:5px;'>".$display_type_txt."</div></td-->
			<td class='list_box_td list_bg_gray' >".date("Y-m-d",$db->dt[cmg_use_sdate])." ~ ".date("Y-m-d",$db->dt[cmg_use_edate])."</td>
			<td class='list_box_td' >";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				//$mString .= "<a href='promotion_goods.php?mode=copy&pg_ix=".$db->dt[pg_ix]."&SubID=SM22464243Sub'><img src='../images/".$admininfo["language"]."/btn_list_copy.gif' border=0 alt='복사'></a> ";
				//$mString .= "<a href='category_main_goods.php?mode=copy&cmg_ix=".$db->dt[cmg_ix]."'><img src='../images/".$admininfo["language"]."/btn_list_copy.gif' border=0 alt='복사'></a> ";
				//$mString .= "<a href='category_main_goods.php?cmg_ix=".$db->dt[cmg_ix]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a> ";
				$mString .= "<a href=\"javascript:PoPWindow3('../display/category_main_goods.php?mode=copy&mmode=pop&cmg_ix=".$db->dt[cmg_ix]."&SubID=SM22464243Sub',1200,800,'category_main_goods')\"><img src='../images/".$admininfo["language"]."/btn_list_copy.gif' border=0 align=absmiddle></a> ";
				$mString .= "<a href=\"javascript:PoPWindow3('../display/category_main_goods.php?mmode=pop&cmg_ix=".$db->dt[cmg_ix]."&SubID=SM22464243Sub',1200,800,'category_main_goods')\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0  align=absmiddle></a> ";
			}else{
				$mString .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0  align=absmiddle></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$mString .= "<a href=\"JavaScript:eventDelete('".$db->dt[cmg_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0  align=absmiddle></a>";
			}else{
			$mString .= "<a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0  align=absmiddle></a>";
			}
			$mString .= "
			</td>
			</tr> 
			";
		}


	}
	$mString .= "</table>";
	$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100% >
				<tr height=50 bgcolor=#ffffff>
					<td colspan=3 align=left>".page_bar($total, $page, $max,  "","")."</td>
					<td colspan=3 align=right>";
					
					
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
						$mString .= "<a href='category_main_goods.php?div_ix=$div_ix'><img src='../images/".$admininfo["language"]."/b_promotionadd.gif' border=0 ></a>";
					}else{
						$mString .= "<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_promotionadd.gif' border=0 ></a>";
					}
					$mString .= "
					</td>
				</tr>";
	$mString .= "</table>";

	return $mString;
}


?>
