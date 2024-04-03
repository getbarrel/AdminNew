<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

$db = new Database;
$sdb = new Database;


$Script = "
<script type='text/javascript'>
<!--
	function taxbill_apply (frm){ 

		if(!CheckFormValue(frm)){
			return false; 
		}

		if(!confirm('세금&계산서 신청하시겠습니까 ? ')){
			return false; 
		}
	}
//-->
</script>

";

if(empty($oid)){
	echo " 잘못된 접근입니다.";
	exit;
}


$Contents = "<form name='inputform' method='post' action='notax.act.php' onsubmit='return taxbill_apply(this)' target='act'>
<input type='hidden' name='act' value='taxbill_apply'>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
			<tr>
				<td align=center style='padding: 0 10 0 10'>
					<table border='0' width='100%' cellspacing='1' cellpadding='0' >
						<tr>
							<td >
								<table border='0' width='100%' cellspacing='1' cellpadding='0' class='input_table_box' style='table-layout:fixed;' >
									<col width='40%'>
									<col width='*'>
									<tr>
										<td class='m_td' >주문번호</td>
										<td class='input_box_item' ><input type='hidden' name='oid' value='".$oid."'>".$oid."</td>
									</tr>
									<tr>
										<td class='m_td' >상호명</td>
										<td class='input_box_item' ><input type='text' name='tax_com_name' value='".$tax_com_name."' validation='true' title='상호명'></td>
									</tr>
									<tr>
										<td class='m_td' >대표자명</td>
										<td class='input_box_item' ><input type='text' name='tax_com_ceo' value='".$tax_com_ceo."' validation='true' title='대표자명'></td>
									</tr>
									<tr>
										<td class='m_td' >사업자번호</td>
										<td class='input_box_item' ><input type='text' name='tax_com_number' value='".$tax_com_number."' validation='true' title='사업자번호'> ex)000-00-00000 </td>
									</tr>
								</table>
							<td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan=2 align=center style='padding:10px 0px;'>
				<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle><!--btn_inquiry.gif-->
				</td>
			</tr>
		</table>
</form>";



$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "판매자정산관리 > $title";
$P->NaviTitle = "세금&계산서신청";
$P->strContents = $Contents;
echo $P->PrintLayOut();