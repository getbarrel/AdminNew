<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

$db = new Database;
$sdb = new Database;


if($m_useopt==0){
	$title="소득공제신청";
}elseif($m_useopt==1){
	$title="지출증빙신청";
}


$Script = "
<script type='text/javascript'>
<!--
	function receipt_apply (frm){ 

		if(!CheckFormValue(frm)){
			return false; 
		}

		if(!confirm('".$title." 하시겠습니까 ? ')){
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


$db->query("SELECT * from shop_order where oid = '$oid' ");
$db->fetch();

$sdb->query("select cu.id,cmd.*,AES_DECRYPT(name,'".$db->ase_encrypt_key."') as rname  from common_user cu , common_member_detail cmd where cu.code=cmd.code and cu.code='".$db->dt[user_code]."'");
$sdb->fetch();

if($db->dt[uid]!="" && $sdb->total){

	
	$id=$sdb->dt[id];
	
	if($m_useopt==0){
		if($sdb->dt[voucher_num_div]=='1'){//1:휴대폰 2:현금영수증카드
			$m_number=$sdb->dt[voucher_phone];
		}elseif($sdb->dt[voucher_num_div]=='2'){
			$rname=$sdb->dt[card_voucher_name];
			$m_number=$sdb->dt[voucher_card];
		}else{
			$rname=$sdb->dt[rname];
			$m_number="";
		}

		if($m_number==""){
			$m_number=$db->dt[bmobile];
		}

	}elseif($m_useopt==1){

		if($sdb->dt[expense_num]!=""&&$sdb->dt[expense_num]!="--"){
			$m_number=$sdb->dt[expense_num];
		}else{
			$m_number="";
		}

		$rname=$sdb->dt[rname];
	}

}else{
	$id="";
	$rname=$db->dt[bname];
	$m_number=$db->dt[bmobile];
}

if($rname==""){
	$rname=$db->dt[bname];
}



$m_number = str_replace('-','',$m_number);

$Contents = "<form name='inputform' method='post' action='notax.act.php' onsubmit='return receipt_apply(this)' target='act'>
<input type='hidden' name='act' value='receipt_apply'>
<input type='hidden' name='m_useopt' value='".$m_useopt."'>
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
										<td class='m_td' >고객명</td>
										<td class='input_box_item' ><input type='hidden' name='id' value='".$id."'><input type='hidden' name='rname' value='".$rname."'>".$rname.($id !="" ? "(".$id.")" : "")."</td>
									</tr>
									<tr>
										<td class='m_td' >발행번호</td>
										<td class='input_box_item' ><input type='text' name='m_number' value='".$m_number."' validation='true' title='발행번호' ></td>
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
$P->NaviTitle = $title;
$P->strContents = $Contents;
echo $P->PrintLayOut();