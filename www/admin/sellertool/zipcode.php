<?
include("../class/layout.class");
//include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
//include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include_once("../openapi/openapi.lib.php");
include_once("sellertool.lib.php");

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<TD align=center >
			<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
				<tr >
					<td align='left' colspan=2> ".GetTitleNavigation("우편번호찾기", "회원관리 > 우편번호찾기", false)."</td>
				</tr>
				<tr height=30>
                    <td class='p11 ls1' style='padding:0 0 5px 20px;'> - 찾고자하는 주소의 동(읍/면/리/가)명을 입력하세요.<br />
                        예) '서울시 중구 명동' 이라면 '명동'만 입력해주세요. <br />
                        검색 후 우편번호를 클릭해주세요.
                    </td>
                </tr>
				<tr>
					<td align=center>
					<form name='z' method='post'  onSubmit='return CheckFormValue(z);'>
					<input type='hidden' name='act' value='search'>
                    <input type='hidden' name='site_code' value='".$_GET['site_code']."'>

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


$qstr = trim($qstr);


$_site_code = $_GET['site_code'];


if($act == "search"){
    $result = getZipCode($_site_code,$qstr);
    
    
    //print_r($result);
	if(!empty($result)){
		foreach($result as $rt):
			$Contents .= "
					<tr height=25 bgcolor='#F8F9FA' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; this.style.cursor='pointer';\" onMouseOut=\"this.style.backgroundColor='';\" onClick=\"setZipcode('".number_format($rt->mailNO,0,'','-')."','".$rt->mailNOSeq."','".$rt->addr."');\">
								<td align='center'><b>".number_format($rt->mailNO,0,'','-')."</b></td>
								<td>".$rt->addr."</td>
							</tr>
							<tr  height=1><td align=center background='/admin/image/dot.gif' colspan=6></td></tr>";
			
		endforeach;
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

$Script = "<script language='javascript'>
    function setZipcode(a,b,c){
			opener.document.getElementById('mailNO').value = a;
			opener.document.getElementById('mailNOSeq').value = b;
			opener.document.getElementById('addr').value = c;
            opener.document.getElementById('dtlsAddr').value = '';		
			opener.document.getElementById('dtlsAddr').focus();
			self.close();
	}
</script>";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "우편번호찾기";
$P->NaviTitle = "우편번호찾기";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>
