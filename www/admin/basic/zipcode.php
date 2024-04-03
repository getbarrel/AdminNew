<?
include("../class/layout.class");
//include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
//include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<TD align=center >
			<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
				<tr >
					<td align='left' colspan=2> ".GetTitleNavigation("우편번호찾기", "회원관리 > 우편번호찾기", false)."</td>
				</tr>
				<tr height=30><td class='p11 ls1' style='padding:0 0 5px 20px;'> - 찾으실 주소지(동,읍/면단위)를 입력하세요.</td></tr>
				<tr>
					<td align=center>
					<form name='z' method='post'  onSubmit='return CheckFormValue(z);'>
					<input type='hidden' name='act' value='search'>

						<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'></td>
								<th class='box_03'></th>
							</tr>
							<tr>
								<th class='box_04'></th>
								<td class='box_05' align=center>
									<table border='0' width='100%' cellspacing='1' cellpadding='0'>
										<col width='80'>
										<col width='*'>
										<col width='80'>
										<tr height='30' valign='middle'>
											<td align='center' ><b>검색주소</b></td>
											<td align='left' width='*'>
												<input type='text' class='textbox' name='qstr' size='44' value='' validation=true title='검색주소'>
											</td>
											<td width='80'>
												<input type='image' src='../images/".$admininfo["language"]."/btn_search.gif' style='border:0px;' align=absmiddle>
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
			<div id='comment' align='center'></div>
			<table border='0' width='400' cellpadding='0' cellspacing='1' align='center' style='margin:10px 0px 0px 0px'>
				<tr>
					<td >
					<div style='overflow:auto;width:100%;height:155px;'>
						<table border='0' width='100%' cellspacing='0' cellpadding='0'>
							<tr  height=2><td align=center bgcolor=gray colspan=6></td></tr>
							<tr bgcolor='#efefef' height=25>
								<td width='80' align='center'><b>우편번호</b></td>
								<td width='*'><b>주소</b></td>
							</tr>
							<tr  height=1><td align=center bgcolor=silver colspan=6></td></tr>";

$db = new Database;
$qstr = trim($qstr);

if($act == "search"){

	if($db->dbms_type=='oracle'){
		$db->query("SELECT substr(zip_code,0,3) as code1, substr(zip_code,-3) as code2, address, (sido||' '||sigugun||' '||dong||' '||building_name) as address_input FROM ".TBL_SHOP_ZIP." WHERE sido LIKE '%$qstr%' OR sigugun LIKE '%$qstr%' OR dong LIKE '%$qstr%'  OR ri LIKE '%$qstr%'  ORDER BY sido, sigugun,dong");
	}else{
		$db->query("SELECT left(zip_code,3) as code1, right(zip_code,3) as code2, address, CONCAT(sido,' ',sigugun,' ',dong,' ', building_name) as address_input FROM ".TBL_SHOP_ZIP." WHERE sido LIKE '%$qstr%' OR sigugun LIKE '%$qstr%' OR dong LIKE '%$qstr%'  OR ri LIKE '%$qstr%'  ORDER BY sido, sigugun,dong");
	}

	if($db->total){
		for($i=0;$i < $db->total ; $i++){
			$db->fetch($i);
			$code1 = $db->dt[code1];
			$code2 = $db->dt[code2];
			$address_input = $db->dt[address_input];
			$tel = $db->dt[tel];
			$address = $db->dt[address];

			$Contents .= "
					<tr height=25 bgcolor='#F8F9FA' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; this.style.cursor='pointer';\" onMouseOut=\"this.style.backgroundColor='';\" onClick=\"zipcode('$code1','$code2','$address_input','$tel','$type');\">
								<td align='center'><b>$code1 - $code2</b></td>
								<td>$address</td>
							</tr>
							<tr  height=1><td align=center background='/admin/image/dot.gif' colspan=6></td></tr>";

		}
	}else{
		$Contents .= "
			<tr bgcolor='#F8F9FA' height=70>
				<td align='center' colspan=2>
					검색어를 입력해주시기 바랍니다.
				</td>
			</tr>";


	}
}

$Contents .= "

						</table>
					</div>
					</td>
				</tr>
			</table>
		</TD>
	</TR>
</TABLE>";

$Script = "<script language='javascript' src='./zipcode.js'></script>";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "우편번호찾기";
$P->NaviTitle = "우편번호찾기";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>
