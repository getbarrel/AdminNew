<?
include("../class/layout.class");
include("../inventory/inventory.lib.php");

$db = new MySQL;

//[S] 쉐어드메모리 데이터 로드
$warehouse_data = sharedControll("warehouse_data");
//[E] 쉐어드메모리 데이터 로드

//[S] 데이터 저장
if(!empty($_POST)){
	sharedControll("warehouse_data", "insert", $_POST); // shared명, act, 저장 데이터
	echo "
		<script type='text/javascript'>
			alert('정상적으로 변경되었습니다.');
			window.close();
			opener.location.reload();
		</script>
	";
}
//[E] 데이터 저장

$sql = "SELECT
			pi.place_name,
			c.com_name
		FROM inventory_place_info pi
		INNER JOIN common_company_detail c
		ON pi.company_id = c.company_id
		WHERE pi.pi_ix = '".$warehouse_data["regist_pi_ix"]."'
		LIMIT 1
";
$db->query($sql);
$db->fetch();

$Script .="
	<script language='javascript' src='../inventory/placesection.js'></script>
	<script type='text/javascript'>
		function checkUpdate(){
			if($('#regist_company_id').val() == ''){
				alert('사업장을 선택해주세요.');
				return false;
			}
			if($('#regist_pi_ix').val() == ''){
				alert('창고를 선택해주세요.');
				return false;
			}
		}
	</script>
";

$Contents = "

<form name='listform' method='post' onsubmit='return checkUpdate()'>
	<input type='hidden' name='regdate' value='".date("Y-m-d H:i:s")."' />
";

if($db->total > 0){

	$Contents .= "

		<p>
			<img src='../images/dot_org.gif' align='absmiddle'>
			<b class='blk'>기본(이전) 설정 * 변경시간 " . $warehouse_data["regdate"] . "</b>
		</p>

		<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
			<colgroup><col width='15%'>
				<col width='35%'>
				<col width='15%'>
				<col width='35%'>
			</colgroup>
			<tbody>
				<tr>
					<td class='input_box_title' nowrap=''> 사업장</td>
					<td class='input_box_item'>".$db->dt["com_name"]."</td>
					<td class='input_box_title' nowrap=''> 창고</td>
					<td class='input_box_item'>".$db->dt["place_name"]."</td>
				</tr>
			</tbody>
		</table>
	";

}

$Contents .= "
	<p style='margin-top:20px;'>
		<img src='../images/dot_org.gif' align='absmiddle'>
		<b class='blk'>변경</b>
	</p>

	<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
		<colgroup><col width='15%'>
			<col width='35%'>
			<col width='15%'>
			<col width='35%'>
		</colgroup>
		<tbody>
			<tr>
				<td class='input_box_title' nowrap=''> 사업장 <img src='/admin/icon/required3.gif'></td>
				<td class='input_box_item'>".SelectEstablishment("","regist_company_id","select","true","onChange=\"loadPlaceData(this)\" ")."</td>
				<td class='input_box_title' nowrap=''> 창고 <img src='/admin/icon/required3.gif'></td>
				<td class='input_box_item'>".SelectInventoryInfo("","",'regist_pi_ix','select','true', "title='창고' ")."</td>
			</tr>
		</tbody>
	</table>

	<p style='text-align:center;'>
		<input type='image' src='../images/korea/b_save.gif' border='0' style='cursor:pointer;border:0px;' align='absmiddle'>
	</p>

</form>

";

$P = new ManagePopLayOut();
$P->addScript = "<script language='javascript' src='../basic/company.add.js'></script>".$Script;
$P->Navigation = "기본 사업장 변경";
$P->NaviTitle = "기본 사업장 변경";
$P->title = "기본 사업장 변경";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>
