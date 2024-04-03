<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
include("inventory.lib.php");


$db = new Database;


if(!$pid){
	$db->query("select * from inventory_place_info where disp = 'Y' order by exit_order asc ");
}else{
	$sql = "select pname from shop_product where id = '$pid'";
	$db->query($sql);
	$db->fetch();
	$pname = $db->dt[pname];

	//$sql = "select h.place_name,ip.* from inventory_info_productorder ip , inventory_place_info h where h.pi_ix = ip.i_ix and ip.pid = '$pid'";
	$sql = "select ps.pi_ix ,ps.exit_order, pi.place_name, pi.place_type, place_tel, place_fax, return_position, disp, ps.regdate 
				from inventory_product_stockinfo ps , inventory_place_info pi 
				where pi.pi_ix = ps.pi_ix and ps.pid = '".$pid."' 
				group by pi_ix order by ps.exit_order asc ";

	$db->query($sql);
}
	
	
$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			<tr>
			
			<td valign=top style='padding:0px;padding-top:0px;' id='product_orderarea'>			
			";
$innerview = "			
			<table width='100%' cellpadding=0 cellspacing=0 border=0>			
			<col width=10%>
			<col width=60%>
			<col width=15%>
			<col width=15%>
			<tr>
			    <td align='left' colspan=4 > ".GetTitleNavigation("창고 우선순위", "상점관리 > 창고 우선순위")."</td>
			</tr>";

if($mmode == "pop"){
$innerview .= "	
			<tr>
				<td colspan='4' height='25' style='padding:5px 0px;'>
					<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>".$pname." 창고 우선순위 </b>
				</td>
			</tr>";
}else{
$innerview .= "	
			<tr>
				<td align='left' colspan=8 style='padding-bottom:14px;'>
				<div class='tab'>
						<table class='s_org_tab'>
						<tr>
							<td class='tab'>
								<table id='tab_00' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='place_list.php'\">창고 등록</td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='tab_01' class='on'>
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='inventory_order.php'\">창고 우선순위조정</td>
									<th class='box_03'></th>
								</tr>
								</table>
								

							</td>
							<td align='right' style='text-align:right;vertical-align:bottom;'>";
							
		$innerview .= "
							</td>
						</tr>
						</table>
					</div>
				</td>
			</tr>";
}
$innerview .= "
			
			</table>
			<form name=vieworderform method=post action='./inventory_order.act.php' target='act'><!-- target='act'-->
			<input type=hidden name='act' value='".($pid != "" ? "product_exit_order_change":"exit_order_change")."'>
			<input type=hidden name='pid' value='".$pid."'>
			<table width='100%' cellpadding=2 cellspacing=0 border=0 class='list_table_box'>			
			<col width=15%>
			<col width=*>
			<col width=15%>
			<col width=15%>
			<col width=15%>
			<col width=10%>
			<col width=15%>
			
			<tr height=30 align=center>
				<td  class=s_td>입/출고 우선순서</td>
				<td  class=m_td>창고 타입</td>
				<td  class=m_td>창고명</td>
				<td class=m_td>창고 전화번호</td>
				<td class=m_td>창고 FAX</td>
				<td class=m_td>반품창고</td>
				<td  class=e_td>날짜</td>
			</tr>
			</table>
			
			<table cellpadding=2 cellspacing=0 width=100% onselectstart='return false;' ondragstart='return false;' id='product_order_table' class='list_table_box'><!--frame=hsides rules=rows-->
			<col width=15%>
			<col width=*>
			<col width=15%>
			<col width=15%>
			<col width=15%>
			<col width=10%>
			<col width=15%>
			";
	


if($db->total == 0){
	$innerview = $innerview."<tr bgcolor=#ffffff height=50><td colspan=8 align=center> 등록된 창고가 없습니다.</td></tr>";
}else{			
	$total = $db->total;
	for ($i = 0; $i < $db->total; $i++)
	{

		$db->fetch($i);
	
	$innerview .= "<tr height=30 class='dot_xx'  onclick=\"spoit(this)\" id='".$db->dt[pi_ix]."' style='cursor:pointer;'>					
					<td class='list_box_td ' style='text-align:center;'><b>".$db->dt[exit_order]."</b></td>
					<td class='list_box_td list_bg_gray'>".$inventory_places[$db->dt[place_type]]."</td>
					<td class='list_box_td' style='padding-left:10px'><b>".$db->dt[place_name]."</b></td>
					<td class='list_box_td list_bg_gray' align=center>".$db->dt[place_tel]."</td>
					<td class='list_box_td'>".$db->dt[place_fax]."</td>
					<td class='list_box_td list_bg_gray' align=center>".($db->dt[return_position] == "Y" ? "지정":"미지정")."</td>
					<td class='list_box_td'>
					".$db->dt[regdate]."
					<input type=hidden name=sno[] value='".$db->dt[pi_ix]."'>
					<input type=hidden name=sort[] value='".$db->dt[minusorder]."'> 
					</td>
					
				</tr>
				";
	
	}
}	
	$innerview .= "	
				
				</table>
				<table width='100%'>
					<tr height=50 bgcolor=#ffffff><td colspan=8 align=center><input type=image src='../image/b_save.gif' border=0 align=absmiddle></td></tr>
					
				</table></form>
				
				";
	
$Contents = $Contents.$innerview ."			
			
			
			</td>
			</tr>
		</table>";

$help_text = "
	<table>
		<tr>
			<td style='line-height:150%' class=small>
			<img src='../image/icon_list.gif' align=absmiddle>창고 우선순위는 재고가 소진될때 어느창고의 재고를 먼저 소진할지 지정해주는 메뉴입니다.<br>
			<img src='../image/icon_list.gif' align=absmiddle>우선순위를 변경하시고자 할때 <b>↑ ↓ 방향키</b>를 눌러서 이동하신후 <b>저장</b>버튼을 누르시면 저장됩니다<br>
			</td>
		</tr>
	</table>
	";
$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >창고 우선순위는 재고가 소진될때 어느창고의 재고를 먼저 소진할지 지정해주는 메뉴입니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >우선순위를 변경하시고자 할때 <b>↑ ↓ 방향키</b>를 눌러서 이동하신후 <b>저장</b>버튼을 누르시면 저장됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >상품별로 지정하기 위해선 상품리스트에서 '창고우선순위' 버튼을 클릭하시고 설정하면 됩니다.</td></tr>
</table>
";

$Contents .= HelpBox("창고 우선순위", $help_text, 171);




if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = "<script Language='JavaScript' src='../include/zoom.js'></script><script Language='JavaScript' src='inventory_order.js'></script>";
	$P->Navigation = "재고관리 > 창고 우선순위";	
	$P->NaviTitle = "창고 우선순위";
	$P->strLeftMenu = inventory_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = "<script Language='JavaScript' src='../include/zoom.js'></script><script Language='JavaScript' src='inventory_order.js'></script>";
	$P->Navigation = "재고관리 > 창고 우선순위";
	$P->title = "창고 우선순위";
	$P->strContents = $Contents;
	$P->PrintLayOut();
}





?>