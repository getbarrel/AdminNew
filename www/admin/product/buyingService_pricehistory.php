<? 
include("../class/layout.class");

$db = new Database;

$sql = "select * from shop_product where id = '$id' limit 1 ";

$db->query ($sql);

if($db->total){
	$db->fetch();
	
	$pname = $db->dt[pname];
}




$Contents01 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	  <tr >
			<td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("구매대행 가격 정보 ", "상품관리 > 구매대행 가격 정보 ")."</td>
	  </tr>
	  <!--tr>
			    <td align='left' colspan=4 style='padding-bottom:20px;'> 
			    	<div class='tab'>
							<table class='s_org_tab'>
							<tr>
								<td class='tab'>
									<table id='tab_01'  >
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='buyingService.php?mmode=pop'\">구매대행 상품 등록</td>
										<th class='box_03'></th>
									</tr>
									</table>
									<table id='tab_02' class='on'>
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='buyingServiceInfo.php?mmode=pop'\">구매대행 환율/수수료 관리</td>
										<th class='box_03'></th>
									</tr>
									</table>
									
								</td>
								<td class='btn'>						
									
								</td>
							</tr>
							</table>	
						</div>
			    </td>
			</tr-->	 
	  <tr>
	    <td align='left' colspan=4> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>[$pname ] 상품 구매대행 가격 정보</b></div>")."</td>
	  </tr>
	  
	  </table>";
	  
$Contents02 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>	  
	  <!--tr>
	    <td align='left' colspan=7> ".colorCirCleBox("#efefef","100%","<div style='padding:5 5 5 15;'><img src='../image/title_head.gif' align=absmiddle> <b>적립금 목록</b></div>")."</td>
	  </tr>
	  <tr height=10><td colspan=6 ></td></tr-->	
	  <tr bgcolor=#efefef align=center height=28>
			<!--td class='s_td' width=5%><input type=checkbox class=nonborder id='all_fix' onclick='fixAll(document.reserve_list)'></td-->
			<td class='s_td' width=15%>날짜 </td>
			<td class='m_td' width=7%>Orgin 원가</td>
			<td class='m_td' width=7%>환율</td>
			<td class='m_td' width=7%>상품<br>예상무게</td>
			<td class='m_td' width=7%>항공운송료 </td>
			<td class='m_td' width=7%>관세/부가세 </td>
			<td class='m_td' width=7%>통관수수료</td>
			<td class='m_td' width=7%>통관타입</td>
			<td class='m_td' width=7%>구매대행<br>수수료율</td>
			<td class='e_td' width=7%>구매대행<br>수수료</td>
			<!--td class='e_td' width=7%>관리 </td-->
		</tr>";


$max = 20; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}


$sql = "select count(*) as total from shop_product_buyingservice_priceinfo where pid = '$id'  ";

$db->query($sql);

$db->fetch();
$total = $db->dt[total];

//echo $total;
$sql = "select * from shop_product_buyingservice_priceinfo where pid = '$id'  order by regdate desc limit $start, $max ";
//echo $sql;
$db->query("$sql "); //where uid = '$code'


if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
	
		
		$Contents02 .= "<tr height=28 align=center>
				<!--td bgcolor='#efefef'><input type=checkbox class=nonborder id='reserve_id' name=rid[] value='".$db->dt[id]."'></td-->
				<td bgcolor='#efefef'>".$db->dt[regdate]." </td>
				<td bgcolor='#ffffff'>".$db->dt[orgin_price]." $</td>
				<td bgcolor='#efefef'>".$db->dt[exchange_rate]." 원</td>
				<td bgcolor='#ffffff'>".$db->dt[air_wt]." lbs</td>
				<td bgcolor='#efefef'>".$db->dt[air_shipping]." $</td>
				<td bgcolor='#ffffff' >".$db->dt[duty]." 원</td>
				<td bgcolor='#efefef'>".number_format($db->dt[clearance_fee])."</td>
				<td bgcolor='#ffffff'>".($db->dt[clearance_type] == 1 ? "목록통관":"일반통관")." </td>
				<td bgcolor='#efefef' >".$db->dt[bs_fee_rate]." %</td>
				<td bgcolor='#ffffff' >".$db->dt[bs_fee]." 원</td>
				<!--td bgcolor='#efefef'>".($i != 0 ? "<a href=\"javascript:DeleteBSInfo('".$db->dt[bsi_ix]."')\"><img src='../image/btc_del.gif' border=0></a>":"")."</td-->
			</tr>";
		//$Contents02 .= "<tr hegiht=1><td colspan=10 class='dot-x'></td></tr>";
	}	
	$Contents02 .= "";
}else{
		$Contents02 .= "
			<tr height=60><td colspan=10 align=center>구매대행 환율/수수료  정보가 없습니다.</td></tr>";

}

//$Contents02 .= "<tr height=40><td colspan=8 align=left><a href=\"JavaScript:SelectDelete(document.forms['reserve_list']);\"><img  src='../image/bt_all_del.gif' border=0 align=absmiddle ></a></td></tr>";
$Contents02 .= "<tr height=40><td colspan=10 align=center>".page_bar($total, $page, $max,"&mmode=$mmode&id=$id&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD","")."</td></tr>";
$Contents02 .= "</table>";

	  
$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."";
$Contents = $Contents."</td></tr>";

$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >구매대행 상품의 가격정보 변동에 대한 히스토리 입니다.</td></tr>

</table>
";


$Contents .= HelpBox("구매대행 환율/수수료 관리", $help_text);

 $Script = "
 <script language='javascript'>
 function CheckBsInfo(frm){
 	if(frm.exchange_rate.value == frm.b_exchange_rate.value && frm.bs_duty.value == frm.b_bs_duty.value && frm.bs_supertax_rate.value == frm.b_bs_supertax_rate.value && frm.bs_basic_air_shipping.value == frm.b_bs_basic_air_shipping.value && frm.bs_add_air_shipping.value == frm.b_bs_add_air_shipping.value && frm.clearance_fee.value == frm.b_clearance_fee.value){
 		alert(language_data['buyingService_pricehistory.php']['A'][language]);
		//'변경된 환율/수수료 정보가 없습니다. 변경된 정보가 없으면 저장이 되지 않습니다.'
 		return false;
 	}
 	
 	if(confirm(language_data['buyingService_pricehistory.php']['B'][language])){//'환율/수수료 정보가 변경되면 구매대행 상품 전체 가격이 재 산정되게됩니다. 환율/수수료 정보를 정말로 변경하시겠습니까? '
 		return true;
 	}else{
 		return false;
 	}
 }
 
	
 function DeleteBSInfo(bsi_ix){
 	if(confirm(language_data['buyingService_pricehistory.php']['C'][language])){//'해당그룹 정보를 정말로 삭제하시겠습니까?'
 	//	var frm = document.group_frm; 	
 	//	frm.act.value = act;
 	//	frm.gp_ix.value = gp_ix;
 	//	frm.submit();
 		
 		f    = document.createElement('form');
    f.name = 'bsform';
    f.id = 'bsform';
    f.method    = 'post'; 
    f.target = 'act';
    f.action    = 'buyingServiceInfo.act.php';

    i0          = document.createElement('input');
    i0.type     = 'hidden';
    i0.name     = 'act';
    i0.id     = 'act';
    i0.value    = 'delete';
    f.insertBefore(i0);
    
    i1          = document.createElement('input');
    i1.type     = 'hidden';
    i1.name     = 'bsi_ix';
    i1.id     = 'bsi_ix';
    i1.value    = bsi_ix;
    f.insertBefore(i1);

		document.insertBefore(f); 
		f.submit();

 	}	
}
 </script>
 ";
	
if($mmode == "pop"){
	
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->NaviTitle = "구매대행 상품관리";
	$P->Navigation = "HOME > 상품관리 > 구매대행 상품관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
	
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->Navigation = "HOME > 상품관리 > 구매대행 상품관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

create table shop_buyingservice_info (
bsi_ix int(4) unsigned not null auto_increment  ,
exchange_type enum('USD','KRW') null default 'USD',
exchange_rate int(2)  default '9' ,

bs_tax int(8) null default null,
bs_orgin_shipping int(8) null default 0,
bs_air_shipping int(8) null default 0,
bs_packing_fee int(8) null default 0,
bs_tariff int(8) null default 0,
disp char(1) default '1' ,
regdate datetime not null, 
primary key(gp_ix));
*/
?>