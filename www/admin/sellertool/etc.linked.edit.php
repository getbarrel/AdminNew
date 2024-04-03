<?
include("../class/layout.class");


if($max == ""){
	$max = 10; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

switch($etc_div){
	case "B":
		$etc_div_name = "브랜드";
		break;
	case "C";
		$etc_div_name = "제조사";
		break;
	case "N";
		$etc_div_name = "원산지(제조국)";
		break;
	default:
		echo "잘못된 접근입니다.";
		exit;
		break;
	
}

$db = new Database;

if($act!="search"){
	$search_text = $shop_name;
}

if($search_text){
	$search_str = " and code_name LIKE '%$search_text%' ";
}

$db->query("select count(*) as total from sellertool_received_etc where site_code='".$site_code."' and etc_div='".$etc_div."' $search_str ");
$db->fetch();

$total = $db->dt[total];


$sql = "select * from sellertool_received_etc where site_code='".$site_code."' and etc_div='".$etc_div."' $search_str order by code_name limit $start, $max";
//echo $sql;
$db->query($sql);

$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&elr_ix=$elr_ix&site_code=$site_code&etc_div=$etc_div&shop_code=$shop_code&shop_name=$shop_name&act=$act&search_text=$search_text","");

$Script = "
<script language='JavaScript' >

function CheckSearch(frm){
	return true;
	if(frm.search_text.value.length < 1){
		alert('검색어를 입력해주세요');
		return false;
	}
}

function SelectInfo(target_code, target_name){
	
	var act;

	";
	
if( empty($elr_ix) ){
	$Script .= "
	var act = 'json_insert'";
}else{
	$Script .= "
	var act = 'json_update'";
}

$Script .= "

	var elr_ix = '".$elr_ix."';
	var etc_div = '".$etc_div."';
	var site_code = '".$site_code."';
	var origin_code = '".$shop_code."';
	var origin_name = '".str_replace("'","&#39;",$shop_name)."';


	$.ajax({
		type:'POST',
		data: {'act': act,'elr_ix': elr_ix,'etc_div': etc_div,'site_code': site_code,'origin_code': origin_code,'origin_name': origin_name,'target_code': target_code,'target_name': target_name},
		url:'etc.linked.act.php',
		dataType: 'html',
		error: function(data,error){// 실패시 실행
			alert(error);},
		success: function(transport){
			if(transport == 'SUCCESS'){
				opener.location.reload();
				self.close();
			}
		}
	});
}
</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<!--tr><td  align=left class='top_orange'  ></td></tr>
			<tr height=35 bgcolor=#efefef>
				<td  style='padding:0 0 0 0;'>
					<table width='100%' border='0' cellspacing='0' cellpadding='0' >
						<tr>
							<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap>
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 제휴사".$etc_div_name."검색
							</td>
							<td width='90%' align='right' valign='top' >
								&nbsp;
							</td>
						</tr>
					</table>
				</td>
			</tr-->
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("제휴사".$etc_div_name."검색", "제휴사".$etc_div_name."검색", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 5px 0 5px'>
				<form name='z' method='get'  action=''  onSubmit='return CheckSearch(this)'>
				<input type='hidden' name='act' value='search'>
				<input type='hidden' name='elr_ix' value='".$elr_ix."'>
				<input type='hidden' name='site_code' value='".$site_code."'>
				<input type='hidden' name='etc_div' value='".$etc_div."'>
				<input type='hidden' name='shop_code' value='".$shop_code."'>
				<input type='hidden' name='shop_name' value='".$shop_name."'>
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
									<col width='220'>
									<col width='*'>
									<tr height='40' valign='middle'>
										<td align='center'  colspan=2 class='input_box_title'><b>제휴사".$etc_div_name."검색</b></td>
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
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> ".($search_text == "" ? "원산지 전체 목록입니다.":"'".$search_text ."' 로 검색된 결과 입니다.")."</td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> </td>
	</tr>
	<tr>
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<tr height='28' bgcolor='#ffffff'>
			<td width='20%' align='center' class='m_td'><font color='#000000'><b>제휴사".$etc_div_name." 코드</b></font></td>
			<td width='*' align='center' class=m_td><font color='#000000'><b>제휴사".$etc_div_name." 명</b></font></td>
			<td width='30%' align='center' class=m_td><font color='#000000'><b>등록일</b></font></td>
		  </tr>";


if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);

		$Contents .= "<tr height=25 style='text-align:center;cursor:pointer;' >
								<td class='list_box_td '>".$db->dt[code]."</td>
								<td class='list_box_td point' onclick=\"SelectInfo('".$db->dt[code]."','".$db->dt[code_name]."');\">".$db->dt[code_name]."</td>
								<td class='list_box_td list_bg_gray' >".$db->dt[insert_date]."</td>
								</tr>";
	}
}else{

}


$Contents .= "
		</table>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:0 10px 0 10px' colspan=2>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:10px 0 0 0' colspan=2>
			".$str_page_bar."
		</td>
	</tr>
</TABLE>
";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "제휴사".$etc_div_name."검색";
$P->NaviTitle = "제휴사".$etc_div_name."검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>





