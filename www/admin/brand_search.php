<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include("./class/layout.class");



if($max == ""){
	$max = 10; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


$db = new Database;

/*
if($db->dbms_type == "oracle"){
	if($search_type && $search_text){
		if($search_type == "name" || $search_type == "id" || $search_type == "pcs"){
			$search_str = " and AES_DECRYPT(".$search_type.",'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}else{
			$search_str = " and $search_type LIKE '%$search_text%' ";
		}
	}
}else{
	if($search_type && $search_text){
		if($search_type == "name" || $search_type == "id" || $search_type == "pcs"){
			$search_str = " and AES_DECRYPT(UNHEX(".$search_type."),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}else{
			$search_str = " and $search_type LIKE '%$search_text%' ";
		}
	}
}
*/
if($search_type && $search_text){
	$search_str = " and $search_type LIKE '%$search_text%' ";
}

$db->query("select count(*) as total from shop_brand where disp='1' $search_str ");
$db->fetch();

$total = $db->dt[total];


$sql = "select * from shop_brand where disp='1' $search_str order by brand_name limit $start, $max";
//echo $sql;
$db->query($sql);

$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&buying_status=$buying_status&sdate=$sdate&edate=$edate&input_id=$input_id&b_ix_id=$b_ix_id&search_type=$search_type&search_text=$search_text","");

$Script = "
<script language='JavaScript' >

function CheckSearch(frm){
	if(frm.search_text.value.length < 1){
		alert('검색어를 입력해주세요');
		return false;
	}
}

function SelectBrand(b_ix, brand_name){
	//alert($('#company_id',opener.document).parent().html());
	$('#".$b_ix_id."',opener.document).val(b_ix);
	$('#".$input_id."',opener.document).val(brand_name);
	self.close();
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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 브랜드검색
							</td>
							<td width='90%' align='right' valign='top' >
								&nbsp;
							</td>
						</tr>
					</table>
				</td>
			</tr-->
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("브랜드검색", "브랜드검색", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > - 찾으실 브랜드명 또는 브랜드코드을 입력하세요.</td></tr>
			<tr>
				<td align=center style='padding: 0 5px 0 5px'>
				<form name='z' method='get'  action=''  onSubmit='return CheckSearch(this)'>
				<input type='hidden' name='act' value='search'>
				<input type='hidden' name='b_ix_id' value='$b_ix_id'>
				<input type='hidden' name='input_id' value='$input_id'>
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
									<col width='170'>
									<col width='*'>
									<tr height='40' valign='middle'>
										<td align='center'  colspan=2 class='input_box_title'><b>브랜드검색</b>
											<select name='search_type'>
												<option value='brand_name'> 브랜드명</option>
												<option value='b_ix'> 브랜드코드(key)</option>
											</select>
										</td>
										<td class='input_box_item' style='padding-left:15px;'>
											<input type='text' class='textbox' name='search_text' size='30' value=''>
											<input type='image' src='./images/".$admininfo['language']."/btn_search.gif' align=absmiddle>
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
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> ".($search_text == "" ? "브랜드 전체 목록입니다.":"'".$search_text ."' 로 검색된 결과 입니다.")."</td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> </td>
	</tr>
	<tr><form name='send_mail' method='post' action='sms.act.php' onsubmit='return CheckSMS(this);' ><input type=hidden name='act' value='send_mail' >
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<tr height='28' bgcolor='#ffffff'>
			<td width='20%' align='center' class='m_td'><font color='#000000'><b>브랜드 코드</b></font></td>
			<td width='*' align='center' class=m_td><font color='#000000'><b>브랜드 명</b></font></td>
			<td width='30%' align='center' class=m_td><font color='#000000'><b>등록일</b></font></td>
		  </tr>";


if($db->total){
	for($i=0;$i  < $db->total; $i++){ 
		$db->fetch($i);

		$Contents .= "<tr height=25 style='text-align:center;cursor:pointer;' >
								<td class='list_box_td '>".$db->dt[brand_code]."</td>
								<td class='list_box_td point' onclick=\"SelectBrand('".$db->dt[b_ix]."','".$db->dt[brand_name]."');\">".$db->dt[brand_name]."</td>
								<td class='list_box_td list_bg_gray' >".$db->dt[regdate]."</td>
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
	</tr></form>
</TABLE>
";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "브랜드검색";
$P->NaviTitle = "브랜드검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>





