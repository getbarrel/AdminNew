<?
include("../class/layout.class");

if(!$agent_type){
	$agent_type = "W";
}

$db = new Database;
$mdb = new Database;

$Script = "<script language='javascript'>
function eventDelete(pg_ix){
	if(confirm(language_data['category_main.list.php']['A'][language]))
	{//'해당 프로모션  정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다.'
		window.frames['act'].location.href= '../display/promotion_goods.act.php?act=delete&pg_ix='+pg_ix;//kbk
		//document.getElementById('act').src= 'promotion_goods.act.php?act=delete&pg_ix='+pg_ix;
	}


}

 function loadCategory(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;

	var depth = $('select[name='+sel.name+']').attr('depth');
	//alert('category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}

</script>";


$mstring ="<form name=serchform ><input type='hidden' name='cid2' value=''><input type='hidden' name='depth' value=''>
		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center style=''>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("컨텐츠관리", "프로모션 전시관리 > 프로모션 상품관리")."</td>
		</tr>";


$mstring .= "<tr>
			<td align='left' >								
				<div class='tab' style='width:100%;height:29px;margin:0px;'>
				<table width='100%' class='s_org_tab'>				
				<tr>							
					<td class='tab' >
						<table id='tab_1' class='on'>
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='promotion_goods.list.php'\">프로모션 전시관리</td>
							<th class='box_03'></th>							
						</tr>
						</table>
						<!--table id='tab_2' >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"alert('준비중입니다.');/document.location.href='promotion_goods.report.php'*/\">프로모션 전시 결과분석</td>
							<th class='box_03'></th>				
						</tr>
						</table-->
					</td>							
					<td align='right'>
						<!--a href='#;' onclick='FnDisplayWrite()'><img src='../images/".$admininfo["language"]."/btn_disp_write.gif' align=absmiddle ></a>&nbsp;
						<a href='#;' onclick='location.href=\"display_div.php?display_div=".$display_div."\"'><img src='../images/".$admininfo["language"]."/btn_disp_div.gif' align=absmiddle ></a-->
						<a href='promotion_category.php'><img src='../images/".$admininfo["language"]."/btn_disp_div.gif' align=absmiddle></a>
					</td>
				</tr>
				</table>										
				</div>					
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
					<col width=20%>
					<col width=30%>
					<col width=20%>
					<col width=30%>";
					if($_SESSION["admin_config"][front_multiview] == "Y"){
					$mstring .= "
					<tr>
						<td class='search_box_title' > 프론트 전시 구분</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
					</tr>";
					}
					$mstring .= "
					<tr>
						<td class='search_box_title' > 전시 분류명</td>
						<td class='search_box_item'><input type='text' class=textbox style='width: 210px; ' name=search_text  title='분류명'> </td>
						<td class='search_box_title' >  전시여부  </td>
						<td class='search_box_item'>
							<input type='radio' name='disp'  id='disp_' value='' ".ReturnStringAfterCompare($disp, "", " checked")."><label for='disp_'>전체</label>
							<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>노출함</label>
							<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>노출안함</label>
						</td>
					</tr>
					<tr>
						<td class='search_box_title' > 담당 MD</td>
						<td class='search_box_item'><input type='text' class=textbox style='width: 210px; ' name=search_text  title='분류명'> </td>
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
						  ".search_date('use_sdate','use_edate',$use_sdate,$use_edate,'N','D')."
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
$sql = 	"SELECT * FROM shop_promotion_div where disp=1 and tab_view = '1' and agent_type = '".$agent_type."'  ";
//echo $sql;
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

$mstring .= "<div style='padding:0px 0px 4px 30px'>";
		if($db->total){
$mstring .= "<select name='menu_div' id='menu_div' style='border:1px solid silver;padding:1px;margin-left:4px;' onchange=\"document.location.href='?div_ix='+this.value\">";
		$mstring .= "<option value=''>메뉴분류</option>";

			for($i=$i;$i < $db->total;$i++){
				$db->fetch($i);
				if($db->dt[div_name] == $selected){
					$mstring .= "<option value='".$db->dt[div_ix]."' selected>".$db->dt[div_name]."</option>";
				}else{
					$mstring .= "<option value='".$db->dt[div_ix]."'>".$db->dt[div_name]."</option>";
				}
			}		
		$mstring .= "</select>";
}
$mstring .= "
		</div>";
$mstring .= "
						</td>
						<td class='btn' align=right><input type='checkbox' name='is_change_function' id='is_change_function' value='1' onclick='toggleChangeFunction();' ".($_COOKIE["is_change_function"] ? "checked":"")."><label for='is_change_function'> 치환코드 보기</label>";
                     
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
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


//$help_text = HelpBox("프로모션 메인관리", $help_text);

$Contents = $mstring.$help_text;
$Contents .= "<div style='height:120px;'></div>";


if($agent_type == "M"){
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
	$P->Navigation = "컨텐츠관리 > 프로모션 전시관리 > 프로모션 상품관리";
	$P->title = "프로모션 상품관리";
	$P->strLeftMenu = display_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


function PrintPromotionGoods(){
	global $db, $mdb, $admin_config, $div_ix ,$admininfo;
	global $auth_write_msg, $auth_delete_msg, $auth_update_msg,$product_image_column_str;
	global $agent_type;

	if($_GET[cid2] !="" ){
		$where ="and cmd.cid = '".$_GET[cid2]."' ";
	}

	if($_GET[date_type] == "use" && $_GET["use_sdate"] && $_GET["use_edate"]){
		$where .="and FROM_UNIXTIME(mg.pg_use_edate,'%Y-%m-%d') between '".$_GET["use_sdate"]."'  and  '".$_GET["use_edate"]."' ";
	}

	if($_GET[date_type] == "reg" && $_GET["use_sdate"] && $_GET["use_edate"]){
		$where .="and date_format(mg.regdate,'%Y-%m-%d') between '".$_GET["use_sdate"]."'  and  '".$_GET["use_edate"]."' ";
	}

	if($_GET[search_text] !="" ){
		$where .="and cmd.div_name like '%".$_GET[search_text]."%' ";
	}

	if($_GET[disp] !="" ){
		$where .="and mg.disp = '".$_GET[disp]."' ";
	}

	if($_GET[mall_ix] !="" ){
		$where .="and mg.mall_ix = '".$_GET[mall_ix]."' ";
	}

	if($_GET[display_status] == "1" ){
		$where .="and ".time()." <  mg.pg_use_sdate ";
	}else if($_GET[display_status] == "2" ){
		$where .="and ".time()." between   mg.pg_use_sdate  and  mg.pg_use_edate ";
	}else if($_GET[display_status] == "3" ){
		$where .="and ".time()." > mg.pg_use_edate ";
	}

	if($div_ix){
		$sql = "select count(*) as total from shop_promotion_goods mg left join shop_promotion_div cmd on mg.div_ix = cmd.div_ix where  mg.disp = 1 and mg.agent_type = '".$agent_type."' and mg.div_ix = '$div_ix' ";
	}else{
		$sql = "select count(*) as total from shop_promotion_goods mg left join shop_promotion_div cmd on mg.div_ix = cmd.div_ix where mg.disp = 1 and mg.agent_type = '".$agent_type."'  $where";
	}
	
	//echo nl2br($sql);
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=4 cellspacing=0 border=0 width=100% class='list_table_box' >
						<col width=4%>
						<col width=4%>
						".($_SESSION["admin_config"]["front_multiview"] == "Y" ? "<col style='width:7%;'>":"")."
						<col width='10%'>
						<col width='*'>						
						<col width='5%'>
						<col width='6%'>
						<col width='7%'>
						<col width='12%'>
						<col width='20%'>
						<col width='14%'>";
	$mString .= "<tr height=30 align=center>
		<td align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
		<td align='center' class='m_td'><font color='#000000'><b>번호</b></font></td>
		".($_SESSION["admin_config"][front_multiview] == "Y" ? "<td class='m_td'> 프론트전시</td>":"")."
		<td class=m_td >전시분류</td>
		<td class=m_td >전시명</td>
		<td class=m_td >상품수</td>
		<td class=m_td >담당 MD</td>
		<td class=m_td >노출여부</td>
		<td class=m_td >전시타입</td>
		<td class=m_td >노출기간</td>
		<td class=e_td >관리</td>
		</tr>";
		//echo ($total);
	if ($total == 0){
		$mString .= "<tr bgcolor=#ffffff><td height=70 colspan=11 align=center>메인전시 내역이 존재 하지 않습니다.</td></tr>";
	}else{

		$sql ="select * from shop_promotion_product_group where ppg_ix <>'0' ";
		$db->query($sql);
		$group_info = $db->fetch();

		if($div_ix){
			$sql = "select mg.pg_ix, mg.mall_ix, mg.md_mem_ix, mg.pg_title, cmd.div_name, cmd.div_code, mg.disp, mg.pg_use_sdate, mg.pg_use_edate ,
						case when '".date("Ymd")."' between mg.pg_use_sdate and mg.pg_use_edate then 1 else 0 end as is_live
						from shop_promotion_goods mg left join shop_promotion_div cmd on mg.div_ix = cmd.div_ix						
						where mg.disp = 1 and mg.div_ix = '$div_ix' and mg.agent_type = '".$agent_type."'
						order by  mg.regdate desc 
						limit $start, $max";
		}else{
			$sql = "select mg.pg_ix, mg.mall_ix, mg.md_mem_ix, mg.pg_title, cmd.div_name, cmd.div_code, mg.disp, mg.pg_use_sdate, mg.pg_use_edate ,
						case when '".date("Ymd")."' between mg.pg_use_sdate and mg.pg_use_edate then 1 else 0 end as is_live
						from shop_promotion_goods mg left join shop_promotion_div cmd on mg.div_ix = cmd.div_ix
						where mg.disp = 1 and mg.agent_type = '".$agent_type."'
						$where 
						order by  mg.regdate desc 
						limit $start, $max";
		}
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
							 p.reserve, pg_ix, ppr_ix, cmpr.vieworder ".$product_image_column_str."
							FROM ".TBL_SHOP_PRODUCT." p, shop_promotion_product_relation cmpr
							where p.id = cmpr.pid and cmpr.pg_ix = '".$db->dt[pg_ix]."'  and p.disp = 1 order by cmpr.vieworder asc ";
			$mdb->query($sql);



			$mString .= "<tr height=27 style='".($db->dt[is_live] ? "font-weight:bold;":"")."'>
			<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$display_ix."'></td>
			<td class='list_box_td'>".$no."</td>
			";
if($_SESSION["admin_config"]["front_multiview"] == "Y"){
	$mString .= "
		    <td class='list_box_td list_bg_gray'>".GetDisplayDivision($db->dt[mall_ix], "text")."</td>";
}
	$mString .= "
			<td class='list_box_td list_bg_gray' style='line-height:150%;'>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$mString .= "<!--<a href='category_promotion_goods.php?pg_ix=".$db->dt[pg_ix]."'>-->".$db->dt[div_name]."<!--</a>-->";
			}else{
				$mString .= "<!--<a href=\"".$auth_update_msg."\">-->".$db->dt[div_name]."<!--</a>-->";
			}
			
			$mString .= "</td>
			<td class='list_box_td' style='padding:5px;' nowrap>
			";

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$mString .= "<a href=\"javascript:PoPWindow3('promotion_goods.php?mmode=pop&pg_ix=".$db->dt[pg_ix]."&SubID=SM22464243Sub',1100,800,'main_goods')\">".$db->dt[pg_title]."</a> ";
			}else{
				$mString .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a> ";
			}

			if($_COOKIE["is_change_function"]){
				$mString .= "<div style='padding-top:5px;'>{@ getDisplayProGoods('".$db->dt[div_code]."')}</div>";
			}
			$mString .= "
			</td>
			<td class='list_box_td' >".($mdb->total)."</td>
			<td class='list_box_td' >".$md_name."</td>";
			 
			$mString .= "
			<td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ? "노출":"노출안함")."</td>
			<td class='list_box_td ' style='padding:5px;'>

			";
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
			//$mString .= "<img src='../images/".$admininfo["language"]."/". $display_img."' align=center ><br>";
			$mString .= "<div style='margin-top:5px;'>".$display_type_txt."</div></td>
			<td class='list_box_td list_bg_gray'>".date("Y.m.d H:i:s",$db->dt[pg_use_sdate])." ~ <br>".date("Y.m.d H:i:s",$db->dt[pg_use_edate])."</td>
			<td class='list_box_td'>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				//$mString .= "<a href='promotion_goods.php?mode=copy&pg_ix=".$db->dt[pg_ix]."&SubID=SM22464243Sub'><img src='../images/".$admininfo["language"]."/btn_list_copy.gif' border=0 alt='복사'></a> ";
				$mString .= "<a href=\"javascript:PoPWindow3('promotion_goods.php?mode=copy&mmode=pop&pg_ix=".$db->dt[pg_ix]."&SubID=SM22464243Sub',1100,800,'main_goods_copy')\"><img src='../images/".$admininfo["language"]."/btn_list_copy.gif' border=0 alt='복사'></a> ";

				//$mString .= "<a href='promotion_goods.php?pg_ix=".$db->dt[pg_ix]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a> ";
				$mString .= "<a href=\"javascript:PoPWindow3('promotion_goods.php?mmode=pop&pg_ix=".$db->dt[pg_ix]."&SubID=SM22464243Sub',1100,800,'main_goods')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}else{
				$mString .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$mString .= "<a href=\"JavaScript:eventDelete('".$db->dt[pg_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
			$mString .= "<a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
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
					<td colspan=3 align=left>".page_bar($total, $page, $max,  "&max=$max","")."</td>
					<td colspan=3 align=right>";
					
					
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
						$mString .= "<a href='promotion_goods.php?div_ix=$div_ix'><img src='../images/".$admininfo["language"]."/b_promotionadd.gif' border=0 ></a>";
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
