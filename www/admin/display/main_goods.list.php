<?
include("../class/layout.class");
include("../display/main_display.lib.php");
//print_r($_SESSION["admin_config"][front_multiview] );

$db = new Database;
$mdb = new Database;

if(!$agent_type){
	$agent_type = "W";
}
//echo $agent_type;
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
		window.frames['act'].location.href= '../display/main_goods.act.php?act=delete&agent_type=".$agent_type."&mg_ix='+mg_ix;//kbk
	}


} 

function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	
	var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
	//document.write('main_position.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	window.frames['act'].location.href = '../display/main_position.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
}

function mainDelete(conmIx){
	var allData = { 'act': 'delete', 'conm_ix': conmIx};
    
    if(confirm('삭제 하시겠습니까?')) {
            $.ajax({
                url:'./main_goods.act.php',
                type:'POST',
                data: allData,
                success:function(data){
                    alert('삭제 되었습니다.');
                    location.reload();
                },error:function(jqXHR, textStatus, errorThrown){
                    alert('에러 발생~~' + textStatus + ' : ' + errorThrown);
                }
            });
        }
}


</script>";


$mstring ="<form name=serchform ><input type='hidden' name='cid2' value=''><input type='hidden' name='depth' value=''>
		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center style=''>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("컨텐츠관리", "메인페이지 관리 > 메인페이지 관리")."</td>
		</tr>";
 

$mstring .= "
		<tr>
			<td>
				<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
					<col width=20%>
					<col width=30%>
					<col width=20%>
					<col width=30%>
					<tr>
						<td class='search_box_title'> 전시구분</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
					</tr>
					<tr>
						<td class='search_box_title' > 조건검색</td>
						<td class='search_box_item' colspan=3>
							<select name='search_type'>
								<option value='' ".(($search_type == '')?"selected":"").">전체</option>
								<option value='title' ".(($search_type == 'title')?"selected":"").">제목</option>
							</select>
							<input type='text' class=textbox style='width: 800px; ' name=search_text value='$search_text'> 
						</td>
					</tr>
					<tr>
						<td class='search_box_title' > 사용여부</td>
						<td class='search_box_item'>
							<input type='radio' name='display_use'  id='display_use_' value='' ".ReturnStringAfterCompare($display_use, "", " checked")."><label for='display_use_'>전체</label>
							<input type='radio' name='display_use'  id='display_use_Y' value='Y' ".ReturnStringAfterCompare($display_use, "Y", " checked")."><label for='display_use_Y'>사용중</label>
							<input type='radio' name='display_use'  id='display_use_N' value='N' ".ReturnStringAfterCompare($display_use, "N", " checked")."><label for='display_use_N'>종료</label>
						</td>
						<td class='search_box_title' >  전시여부</td>
						<td class='search_box_item'>
							<input type='radio' name='display_state'  id='display_state_' value='' ".ReturnStringAfterCompare($display_state, "", " checked")."><label for='display_state_'>전체</label>
							<input type='radio' name='display_state'  id='display_state_D' value='D' ".ReturnStringAfterCompare($display_state, "D", " checked")."><label for='display_state_D'>전시중</label>
							<input type='radio' name='display_state'  id='display_state_E' value='E' ".ReturnStringAfterCompare($display_state, "E", " checked")."><label for='display_state_E'>전시대기</label>
							<input type='radio' name='display_state'  id='display_state_W' value='W' ".ReturnStringAfterCompare($display_state, "W", " checked")."><label for='display_state_W'>종료</label>
						</td>
					</tr>
					<tr>
						<td class='search_box_title' nowrap>
							<label for='search_start_date'><b>시작일자</b></label><input type='checkbox' name='search_start_date' id='search_start_date' value='1' onclick='ChangeRegistDate(document.search_banner);' ".(($search_start_date==1)?"checked":"").">
						  </td>
						  <td class='search_box_item'  colspan=3>
						  ".search_date('start_sdate','start_edate',$start_sdate,$start_edate,'N','D')."
						  </td>
					</tr>
					<tr>
						<td class='search_box_title' nowrap>
							<label for='search_end_date'><b>종료일자</b></label><input type='checkbox' name='search_end_date' id='search_end_date' value='1' onclick='ChangeRegistDate(document.search_banner);' ".(($search_end_date==1)?"checked":"").">
						  </td>
						  <td class='search_box_item'  colspan=3>
						  ".search_date('end_sdate','end_edate',$end_sdate,$end_edate,'N','D')."
						  </td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align ='center'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle></td>
		</tr>
		
		<tr>
			<td>
			".PrintPromotionGoods()."
			</td>
		</tr>
		</form>";
$mstring .="</table>";

$Contents = $mstring.$help_text;
$Contents .= "<div style='height:120px;'></div>";

if($agent_type == "M" || $agent_type == "mobile"){
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
	$P->Navigation = "컨텐츠전시 > 메인페이지 관리 > 메인페이지 목록";
	$P->title = "메인페이지 목록";
	$P->strLeftMenu = display_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}
function PrintPromotionGoods(){
	global $db, $mdb, $admin_config, $div_ix ,$admininfo;
	global $auth_write_msg, $auth_delete_msg, $auth_update_msg,$product_image_column_str;
	global $page, $max, $nset;
	global $agent_type ;

	$where = " WHERE 1=1 ";

	if($mall_ix!=""){
		$where .= " AND c.mall_ix ='".$mall_ix."' ";
	}

	if($search_type != "" && $search_text != ""){
		$where .= " AND $search_type LIKE  '%$search_text%' ";
	}

	if($display_use != ""){
		$where .= " AND c.display_use =  '$display_use' ";
	}

	if($display_state != ""){
		$where .= " AND c.display_state =  '$display_state' ";
	}

	if($search_start_date == "1"){
		$start_sdate_where = mktime(0,0,0,substr($start_sdate,5,2),substr($start_sdate,8,2),substr($start_sdate,0,4));
		$start_edate_where = mktime(0,0,0,substr($start_edate,5,2),substr($start_edate,8,2),substr($start_edate,0,4));
		$where .= " AND  c.display_start BETWEEN  $start_sdate_where AND $start_edate_where ";
	}

	if($search_end_date == "1"){
		$end_sdate_where = mktime(0,0,0,substr($end_sdate,5,2),substr($end_sdate,8,2),substr($end_sdate,0,4));
		$end_edate_where = mktime(0,0,0,substr($end_edate,5,2),substr($end_edate,8,2),substr($end_edate,0,4));
		$where .= " AND  c.display_end BETWEEN  $end_sdate_where AND $end_edate_where ";
	}

	$sql = "SELECT count(*) AS total 
			FROM shop_content_main  
			$where 
	";

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
					<col width='3%'>
					<col width='6%'>
					<col width='*'>
					<col width='15%'>
					<col width='5%'>						
					<col width='5%'>
					<col width='5%'>
					<col width='7%'>
					<col width='10%'>
					<col width='10%'>
						<tr height=30 align=center>
							<td align='center' class='m_td'><font color='#000000'><b>번호</b></font></td>
							<td class=s_td >전시그룹</td>
							<td class=s_td >제목</td>
							<td class=s_td >전시기간</td>
							<td class=m_td >전시</td>
							<td class=m_td >사용</td>
							<td class=m_td >기본</td>
							<td class=m_td >작업자</td>
							<td class=m_td >수정일</td>
							<td class=e_td >관리</td>
						</tr>";
	if ($total == 0){
		$mString .= "<tr bgcolor=#ffffff><td height=70 colspan=13 align=center>내역이 존재 하지 않습니다.</td></tr>";
	}else{
		$sql = "SELECT * FROM shop_content_main
					$where 
				ORDER BY conm_ix desc 
				LIMIT $start, $max

		";
		$db->query($sql);

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
			$no = $total - ($page - 1) * $max - $i;

			$sql = "SELECT AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') AS name FROM ".TBL_COMMON_MEMBER_DETAIL." cmd WHERE code= '".$db->dt[worker_ix]."' ";
			$mdb->query($sql);
			if($mdb->total){
				$mdb->fetch();
				$worker_name = $mdb->dt[name];
			}else{
				$worker_name = "-";
			}

			$mString .= "<tr height=27 style='".($db->dt[is_live] ? "font-weight:bold;":"")."'>
				<td class='list_box_td'>".$no."</td>
				<td class='list_box_td' >".GetDisplayDivision($db->dt[mall_ix], "text")."</td>
				<td class='list_box_td' >".$db->dt[subject]."</td>
				<td class='list_box_td' >".date("Y.m.d", $db->dt[main_start])." ~ ".date("Y.m.d",$db->dt[main_end])."</td>
				<td class='list_box_td' >-</td>
				<td class='list_box_td' >".($db->dt[main_use] == "Y" ? "사용":"미사용")."</td>
				<td class='list_box_td' >".($db->dt[main_default] == "Y" ? "사용":"미사용")."</td>
				<td class='list_box_td' >".$worker_name."</td>
				<td class='list_box_td' >".$db->dt[upddate]."</td>
				<td class='list_box_td list_bg_gray' nowrap>
					<a href='main_goods.php?conm_ix=".$db->dt[conm_ix]."&act=update'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정'></a>
					<a href=\"JavaScript:mainDelete('".$db->dt[conm_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:0px 5px;' alt='삭제' title='삭제'></a>
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
						$mString .= "<a href='main_goods.php?div_ix=$div_ix'><img src='../images/btm_reg.gif' border=0 ></a>";
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
