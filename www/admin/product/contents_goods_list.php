<?
include("../class/layout.class");

$db = new database;

if($_COOKIE[inventory_goods_max_limit]){
	$max = $_COOKIE[inventory_goods_max_limit]; //페이지당 갯수
}else{
	$max = 20;
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


if($admininfo[admin_level] == 9){
	if($company_id){
		$where = "where company_id Is NOT NULL and company_id ='".$company_id."'";
	}else{
		$where = "where company_id Is NOT NULL ";
	}
}else{
	$where = "where company_id Is NOT NULL and company_id ='".$admininfo[company_id]."' ";
}

if(is_array($c_type)){
	for($i=0;$i < count($c_type);$i++){
		if($c_type[$i]){
			if($c_type_str == ""){
				$c_type_str .= "'".$c_type[$i]."'";
			}else{
				$c_type_str .= ", '".$c_type[$i]."' ";
			}
		}
	}

	if($c_type_str != ""){
		$where .= "and c_type in ($c_type_str) ";
	}
}else{
	if($c_type){
		$where .= "and c_type = '$c_type' ";
	}
}

if($state !=''){
	$where .=" and state = '$state'";
}

if($search_type !="" && $search_text != ""){
	$where .= "and ".$search_type." LIKE '%".$search_text."%' ";
}

if($sdate !='' && $edate !=''){
	$where .=" and ".$date_type." between '".$sdate." 00:00:00' and '".$edate." 23:59:59' ";
}

if($orderby !='' && $ordertype != ''){
	$orderbyString = " order by ".$orderby." ".$ordertype."";
}else{
	$orderbyString = "order by regdate desc";
}
if($mode=="search"){
	

	$sql = "select count(*) as total from contents_info $where ";
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];
//echo $sql;
	$sql = "select * from contents_info $where $orderbyString 
					LIMIT $start, $max ";
	$db->query($sql);

	$goods_infos = $db->fetchall();
}

if($mode == "search"){
	if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
	}
	$str_page_bar = page_bar($total, $page, $max, $query_string,"");
}else{
	$str_page_bar = page_bar($total, $page, $max, "&max=$max","");
}
$Contents =	"

<table cellpadding=0 cellspacing=0 width='100%'>
	<tr>
		<td align='left' colspan=4 > ".GetTitleNavigation("단위 컨텐츠 리스트", "컨텐츠 상품 관리 > 단위 컨텐츠 리스트")."</td>
	</tr>

	<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
		<input type='hidden' name='mode' value='search'>
		<input type='hidden' name='cid2' value='$cid2'>
		<input type='hidden' name='depth' value='$depth'>
		<tr height=150>
			<td colspan=2>
				<table class='box_shadow' style='width:100%;height:20px' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
					<tr>
						<th class='box_01'></th>
						<td class='box_02'></td>
						<th class='box_03'></th>
					</tr>
					<tr>
						<th class='box_04'></th>
						<td class='box_05 align=center' style='padding:0px'>
							<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='input_table_box'>
								<col width=15%>
								<col width=35%>
								<col width=15%>
								<col width=35%>
								<tr style='display:none'>
									<td class='input_box_title'>컨텐츠 타입</td>
									<td class='input_box_item' colspan='3'>
										<input type=checkbox name='c_type[]' class='c_type' id='c_type_document'  value='document'  ".CompareReturnValue("document",$c_type,'checked')."><label for='c_type_document'> 문서</label>
										<input type=checkbox name='c_type[]' class='c_type' id='c_type_image'  value='image'  ".CompareReturnValue("image",$c_type,'checked')."><label for='c_type_image'> 이미지</label>
										<input type=checkbox name='c_type[]' class='c_type' id='c_type_music'  value='music'  ".CompareReturnValue("music",$c_type,'checked')."><label for='c_type_music'> 음원</label>
										<input type=checkbox name='c_type[]' class='c_type' id='c_type_video'  value='video'  ".CompareReturnValue("video",$c_type,'checked')."><label for='c_type_video'> 영상</label>
									</td>
								</tr>
								<tr style='display:none'>
									<td class='input_box_title'>셀러업체</td>
									<td class='input_box_item' >
										".companyAuthList($company_id , "validation=false title='셀러업체' ",'company_id','company_id','com_name','input')."
									</td>
									<td class='input_box_title'>사용여부</td>
									<td class='input_box_item'>
										<input type=radio name='state' id='state'  value=''  ".CompareReturnValue("",$state,'checked')."><label for='state'> 전체</label>
										<input type=radio name='state' id='state_1'  value='1'  ".CompareReturnValue("1",$state,'checked')."><label for='state_1'> 사용</label>
										<input type=radio name='state' id='state_0'  value='0'  ".CompareReturnValue("0",$state,'checked')."><label for='state_0'> 사용하지 않음</label>
									</td>
								</tr>
								<tr>
									<td class='input_box_title'>검색어</td>
									<td class='input_box_item' >
										<table cellpadding=0 cellspacing=0 border='0'>
											<tr>
												<td valign='top'>
													<div style='padding-top:5px;'>
													<select name='search_type' id='search_type'  style=\"font-size:12px;\">
													<option value='c_name' ".CompareReturnValue("c_name",$search_type).">컨텐츠명</option>
													<option value='ci_ix' ".CompareReturnValue("ci_ix",$search_type).">시스템코드</option>
													</select>
													</div>
												</td>
												<td style='padding:5px;'>
													<div id='search_text_input_div'>
														<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
													</div>
												</td>
											</tr>
										</table>
									</td>
									<td class='input_box_title'>
										<div style='padding-top:5px;'>
												<select name='date_type' id='date_type'  style='font-size:12px; width:150px;'>
												<option value='regdate' ".CompareReturnValue("regdate",$date_type).">등록일자</option>
												<option value='editdate' ".CompareReturnValue("editdate",$date_type).">수정일자</option>
												</select>
										</div>
									</td>
									<td class='input_box_item'>
										<div style='float:left; padding-left:10px;'>
											".search_date('sdate','edate',$sDate,$eDate)."
										</div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr >
			<td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
			</form>
		</tr>

		<tr>
			<td>
				<span>단위컨텐츠 총 : ".number_format($total)." 개</span>
			</td>
		</tr>
		<tr>
			<td valign=top colspan='2' style='padding:0px;padding-top:0px;' >
				<div style='overflow-x:hidden;width:100%;'>
					<table cellpadding=0 cellspacing=0  width='100%' class='list_table_box' style='min-width:1100px;'>
					<col width='4%'>
					<col width='10%'>
					<col width='10%'>
					<col width='*'>
					<col width='10%'>
					<col width='15%'>
					<col width='10%'>
					<col width='10%'>
					<col width='10%'>
					<tr align=center height=30>
						<td class=s_td >번호</td>
						<td class=m_td >시스템코드</td>
						<td class=s_td >컨텐츠 타입</td>
						<td class=m_td >컨텐츠 명</td>
						<td style='display:none' class=m_td >셀러</td>
						<td class=m_td >".OrderByLink("등록일", "regdate", $ordertype)."/".OrderByLink("수정일", "editdate", $ordertype)."</td>
						<td style='display:none' class=m_td >최종수정 아이디</td>
						<td style='display:none' class=m_td >".OrderByLink("주문건수", "c_order_cnt", $ordertype)."</td>
						<td class=m_td >관리</td>
					</tr>	
					";

					if(count($goods_infos) == 0){
						if($mode=="search"){
							$Contents .= "<tr bgcolor=#ffffff height=50><td colspan=14 align=center> 해당되는 컨텐츠가 없습니다.</td></tr>";
						}else{
							$Contents .= "<tr bgcolor=#ffffff height=50><td colspan=14 align=center> 원하시는 컨텐츠를 검색해주세요.</td></tr>";
						}
					$Contents .= "</table>";
					}else{
						for ($i = 0; $i < count($goods_infos); $i++){
							$no = $total - ($page - 1) * $max - $i;

							switch ($goods_infos[$i][c_type]) {
								case 'document':
									$c_type = "문서";
									break;
								case 'image':
									$c_type = "이미지";
									break;
								case 'music':
									$c_type = "음원";
									break;
								case 'video':
									$c_type = "영상";
									break;
							}
							$Contents .= "
						<tr height=35 align=center>
							<td bgcolor=#ffffff>".$no."</td>
							<td bgcolor=#ffffff>".$goods_infos[$i][ci_ix]."</td>
							<td bgcolor=#ffffff>".$c_type."</td>
							<td bgcolor=#ffffff>".$goods_infos[$i][c_name]."</td>
							<td style='display:none' bgcolor=#ffffff>".get_com_name($goods_infos[$i][company_id]). "</td>
							<td bgcolor=#ffffff>".$goods_infos[$i][regdate]."<br>/".$goods_infos[$i][editdate]."</td>
							<td style='display:none' bgcolor=#ffffff>".last_modify($goods_infos[$i][ci_ix],$goods_infos[$i][company_id])."</td>
							<td style='display:none' bgcolor=#ffffff>".number_format($goods_infos[$i][c_order_cnt])."</td>
							<td bgcolor=#ffffff>";
							//if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
								$Contents .= "
									<a href=\"contents_goods_input.php?ci_ix=".$goods_infos[$i][ci_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
							//}else{
							//	$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
							//}

							//if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
								$Contents .= "
											<a href=\"javascript:ContentsDelete('".$goods_infos[$i][ci_ix]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";	
							//}else{
							//	$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
							//}
							
							$Contents .= "
							
							</td>
						</tr>
							";
						}
						$Contents .= "
					</table>

					<table width='100%' cellpadding=0 cellspacing=0>
						<tr height=40><td></td>
						<td align=right nowrap>".$str_page_bar."</td></tr>

					</table>
						";
					}
					$Contents .= "
				</div>
			</td>
		</tr>
</table>";

$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td   > 각 품목별 및 규격(옵션)별로 재고현황을 보실 수 있습니다
</td></tr>
	<!--tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td > 매입가나 , 기본 도소매가가 다른 경우는 별도의 품목으로 등록합니다 </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td > 바코드가 다른 품목은 별도의 품목으로 등록한다. </td></tr-->
</table>
";

$Contents .= HelpBox("품목리스트", $help_text);

$Script = "
<script type='text/javascript' >
	function ContentsDelete(ci_ix){
		if(confirm('컨텐츠 파일을 삭제 하시겠습니까?')){
		var formData = new FormData();

		formData.append('act', 'delete');
		formData.append('ci_ix', ci_ix);

		$.ajax({
			url: './contents_goods_input.act.php',
			data: formData,
			processData: false,
			contentType: false,
			type: 'POST',
			success: function(result){
				if(result == 0){
					alert('컨텐츠파일이 삭제 되었습니다.');
					location.reload();
				}else{
					alert('삭제실패');
				}
				
			}
		});
	}
	}

</script>

";

$P = new LayOut();
$P->strLeftMenu = product_menu();
$P->addScript = $Script;
$P->Navigation = "상품관리 > 컨텐츠 상품 관리 > 단위 컨텐츠 리스트";
$P->title = "단위 컨텐츠 리스트";
$P->strContents = $Contents;



$P->PrintLayOut();

function last_modify($ci_ix,$company_id){
	$db = new database;

	$sql = "select charger_name from contents_history where ci_ix = '".$ci_ix."' order by ch_ix desc limit 1";
	$db->query($sql);
	if($db->total){
		$db->fetch();
		return $db->dt[charger_name];
	}else{
		return get_com_name($company_id);
	}
}
?>
