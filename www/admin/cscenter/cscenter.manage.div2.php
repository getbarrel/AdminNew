<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");

$db = new Database;

if(! empty($ix)){
	$sql = "select * from shop_product_qna_div where ix='".$ix."'";
	$db->query($sql);
	$db->fetch();
	$infos = $db->dt;

	$act = "qna_setting_update";
}else{
	$act = "qna_setting_insert";
}

$sql = "select * from shop_product_qna_div";
$db->query($sql);
$datas = $db->fetchall("object");

$Script = "
<style>
.width_class {width:150px;}
input {border:1px solid #c6c6c6;padding:3px;}
.member_table td {text-align:left;}
</style>

<script>


</script>
";

$Contents = "
<form name=bbs_manage_frm action='cscenter.manage.act.php' method='post' onsubmit='return CheckFormValue(this)'>
<input type=hidden name=act value='".$act."'>
<input type=hidden name=page_type value='$page_type_str'>
<input type=hidden name=ix value='".$ix."'>
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("분류 설정", "분류 설정", false)."</td>
			</tr>
			<tr>
				<td align=center> <!-- style='padding: 0 10px 0 10px;height:569px;vertical-align:top' -->
				<table border='0' cellspacing='1' cellpadding='5' width='100%'>
				<tr>
				  <td bgcolor='#F8F9FA'>
					<table border='0' width='100%' cellspacing='1' cellpadding='0'>
						<tr>
							<td >
							<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
									<col width=15%>
									<col width=35%>
									<col width=15%>
									<col width=35%>
									<tr>
										<td class='input_box_title' > 프론트 전시 구분</td>
										<td class='input_box_item' colspan=3>".GetDisplayDivision($infos['mall_ix'], "select")." </td>
									</tr>
								<tr>
									<td class='input_box_title' nowrap> 분류명 <img src='".$required3_path."'> </td>
									<td class='input_box_item' colspan='3'><input type=text class='textbox' style='width: 60%' name='div_name' value='".$infos[div_name]."'></td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 분류 사용 여부 <img src='".$required3_path."'> </td>
									<td class='input_box_item' colspan='3'>
										<input type=radio name='disp' value='1' id='disp1' ".($infos[disp] == "1" || $infos[disp] == "" ? "checked" : "").">
										<label for='disp1'>사용</label>
										<input type=radio name='disp' value='0' id='disp0' ".($infos[disp] == "0" ? "checked" : "").">
										<label for='disp0'>사용 안함</label>
									</td>
								</tr>
								</tr>
							</table>
						</td>
					</tr>
					</table>
				  </td>
				</tr>
				</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</TABLE>";

$Contents .="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed' >
		<tr bgcolor=#ffffff >
            <td colspan=4 align=center style='padding:10px 0px;'>
				<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' >
            </td>
        </tr>
		</form>";
$Contents .="</table>";

$Contents .= "
<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver class='list_table_box'>
	<!--col width='5%'-->
	<col width='10%'>
	<col width='23%'>
	<col width='23%'>
	<col width='23%'>
	<col width='10%'>
	<tr align=center height=30>
		<!--td class='m_td' ><input type='checkbox' id='all_check'></td-->
		<td class='m_td' >전시구분</td>
		<td class='m_td' >분류명</td>
		<td class='m_td' >분류 사용여부</td>
		<td class='m_td' >등록일</td>
		<td class='m_td' >관리</td>
	</tr>";
	
	if(is_array($datas) && ! empty($datas)){
		foreach($datas as $k => $v){
			$Contents .= "<tr >
							<!--td align=center ><input type='checkbox' name='div_ix[".$v[ix]."]'></td-->
							<td  align=center>".GetDisplayDivision($v['mall_ix'], "text")."</td>
							<td class='input_box_item' >".$v[div_name]."</td>
							<td class='input_box_item' >".($v[disp] == 1 ? "사용" : "사용 안함")."</td>
							<td class='input_box_item' >".$v[regdate]."</td>
							<td align=center ><input type='button' value='수정' onclick=\"top.location.href='?ix=".$v[ix]."'\"></td>
						</tr>";
		}
	}else{
			$Contents .= "<tr>
							<td align=center bgcolor=#ffffff height=50 colspan='6'>추가된 분류 목록이 없습니다.</td>
						</tr>";
	}

$Contents .= "
</table>
	";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "분류 설정";
$P->NaviTitle = "분류 설정";
$P->title = "분류 설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>
