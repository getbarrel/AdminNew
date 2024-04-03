<?
include("../class/layout.class");


$db = new Database;

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
	

$sql = "select count(*) as total from contents_info $where ";
$db->query($sql);
$db->fetch();
$total = $db->dt[total];
//echo $sql;
$sql = "select * from contents_info $where $orderbyString 
				LIMIT $start, $max ";
$db->query($sql);

$goods_infos = $db->fetchall();


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


$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("컨텐츠정보검색", "컨텐츠정보검색", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > - 단위컨텐츠로 등록하고자 하는 항목을 검색해주세요</td></tr>
			<tr>
				<td align=center style='padding: 0 5px 0 5px'>
				<form name='z' method='post'  action=''  onSubmit='return CheckSearch(this)'>
				<input type='hidden' name='act' value='search'>
					<table class='box_shadow' style='width:100%;' cellpadding=0 cellspacing=0>
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05' align=right style='padding: 0 20 0 20'>
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box'>
									<col width='30%'>
									<col width='*'>
									<tr height='40' valign='middle'>
										<td align='center' class='input_box_title'><b>컨텐츠정보검색</b>
											<select name='search_type'>
												<option value='c_name' ".CompareReturnValue("c_name",$search_type).">컨텐츠명</option>
												<option value='ci_ix' ".CompareReturnValue("ci_ix",$search_type).">시스템코드</option>
											</select>
										</td>
										<td class='input_box_item' style='padding-left:15px;'>
											<input type='text' class='textbox' name='search_text' size='30' value='".$search_text."'>
											<input type='image' src='../images/".$admininfo['language']."/btn_search.gif' align=absmiddle>
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
				</form>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr height=30>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> ".($search_text == "" ? "단위 컨텐츠 전체 목록입니다.":"'".$search_text ."' 로 검색된 결과 입니다.")."</td>
		
	</tr>
	<tr>
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<tr height='28' bgcolor='#ffffff'>";
			if($_GET[type] != 'program'){
			$Contents .= "
			<td width=3% height='25' class=s_td><input type=checkbox class=nonborder name='all_fix' id='all_fix'  onclick='fixAll()'></td>";
			}
			$Contents .= "
			<td width='8%' align='center' class=m_td><font color='#000000'><b>시스템코드</b></font></td>
			<td width='10%' align='center' class=m_td><font color='#000000'><b>컨텐츠타입</b></font></td>
			<td width='*' align='center' class=m_td><font color='#000000' class=small><b >컨텐츠명</b></font></td>
			<td width='12%' align='center' class='m_td'><font color='#000000'><b>셀러명</b></font></td>
			<td width='12%' align='center' class=m_td><font color='#000000'><b>공급가</b></font></td>
			<td width='12%' align='center' class=m_td><font color='#000000'><b>판매가</b></font></td>
			<td width='7%' align='center' class=m_td><font color='#000000'><b>파일타입</b></font></td>
			<td width='7%' align='center' class=m_td><font color='#000000' class=small><b>사용여부</b></font></td>
			<td width='7%' align='center' class=m_td><font color='#000000' class=small><b>연령대</b></font></td>
		  </tr>";
		if($goods_infos > 0){
			for($i=0; $i < count($goods_infos); $i++){
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
				switch($goods_infos[$i][state]){
					case '':
					case '1':
						$state_text = "사용";
					break;
					case '0';
						$state_text = "사용안함";
					break;
				}
					if($_GET[type] != 'program'){
					$Contents .= "
				<tr height=25 style='text-align:center;' >
					<td class='list_box_td list_bg_gray'>
						<input type=checkbox class='nonborder ci_ix' name=ci_ix[] id='ci_ix_".$goods_infos[$i][ci_ix]."' value='".$goods_infos[$i][ci_ix]."' c_name='".$goods_infos[$i][c_name]."'  c_coprice='".$goods_infos[$i][c_coprice]."'  c_sellprice='".$goods_infos[$i][c_sellprice]."'  c_type = '".$c_type."' c_file_type = '".implode(',',unserialize($goods_infos[$i][c_file_type]))."' state = '".$state_text."'>
					</td>";
					}else{
						$Contents .= "
				<tr height=25 style='text-align:center;cursor:pointer;' onclick=\"MakeAddSingle('ci_ix_".$goods_infos[$i][ci_ix]."','".$_GET[file_type]."')\"  >
						<input type=hidden class='nonborder ci_ix' name=ci_ix[] id='ci_ix_".$goods_infos[$i][ci_ix]."' value='".$goods_infos[$i][ci_ix]."' c_name='".$goods_infos[$i][c_name]."'  c_coprice='".$goods_infos[$i][c_coprice]."'  c_sellprice='".$goods_infos[$i][c_sellprice]."' >";
					}
					$Contents .= "
					<td class='list_box_td list_bg_gray' >".$goods_infos[$i][ci_ix]."</td>
					<td class='list_box_td ' >".$c_type."</td>
					<td class='list_box_td point ' >".$goods_infos[$i][c_name]."</td>
					<td class='list_box_td point' >".get_com_name($goods_infos[$i][company_id])."</td>
					<td class='list_box_td'  >".$goods_infos[$i][c_coprice]."</td>
					<td class='list_box_td point' >".$goods_infos[$i][c_sellprice]."</td>
					<td class='list_box_td'  >".implode(',',unserialize($goods_infos[$i][c_file_type]))."</td>
					<td class='list_box_td' >".$state_text."</td>
					<td class='list_box_td' >".$goods_infos[$i][age]."</td>
				</tr>";
			}
		}else{
			$Contents .= "
			<tr height=300>
				<td align=center style='padding:0 10px 0 0px' colspan=10>
					품목정보가 존재하지 않습니다.
				</td>
			</tr>";
		}
		$Contents .= "
		</table>
		</td>
	</tr>
	

</table>
<table width='100%'>
	<tr>";
		if($_GET[type] != 'program'){
		$Contents .= "
		<td align=left style='padding:10px 10px 0 5px' >";
			if($_GET[type] == 'activity'){
				$Contents .= "
				<img src='../images/".$admininfo["language"]."/btn_goods_intoon.gif' border='0' align='absmiddle' onclick='MakeActivityOption()' style='cursor:pointer;'title='상품담기'>";
			}else{
				$Contents .= "
				<img src='../images/".$admininfo["language"]."/btn_goods_intoon.gif' border='0' align='absmiddle' onclick='MakeAddOption()' style='cursor:pointer;'title='상품담기'>";
			}
			$Contents .= "
		</td>";
		}
		$Contents .= "
		<td align=right style='padding:10px 0 0 0' >
			".$str_page_bar."
		</td>
	</tr>
</table>
	";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' style='line-height:120%' >
	<col width=8>
	<col width=*>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' >안내문구.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' >안내문구. </td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' >안내문구.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' >안내문구.</td></tr>
</table>
";



$Contents .= HelpBox("단위컨텐츠검색", $help_text,"100");
$Contents .= "
		</td>
	</tr>
</TABLE>
";

$Script = "
	<script type='text/javascript' src='./contents_search_pop.js'></script>

";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "단위컨텐츠검색";
$P->NaviTitle = "단위컨텐츠검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>