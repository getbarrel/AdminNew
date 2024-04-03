<?
	function tax_menu($default_path='/admin'){
		global $admininfo;
		$mstring = "
		<script>
		$(document).ready(function(){
			$('#tax_tab1').click(function(){
				$('#tab1_view').attr('style','display:');
				$('#tab8_view').attr('style','display:none');
			});

			$('#tax_tab8').click(function(){
				$('#tab8_view').attr('style','display:');
				$('#tab1_view').attr('style','display:none');
			});
		});
		</script>
		";
		$mstring .= "
		<table cellpadding=0  cellspacing=0 width=156 border=0>
				<tr><td align=center style='padding-bottom:5px;'><img src='../v3/images/".$admininfo[language]."/left_title_taxbill.gif'></td></tr>
		</table>
		<table cellpadding=0 cellspacing=1 width=156 border=0 bgcolor='#c0c0c0' style='border-collapse:separate; border-spacing:1px;'>";
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<span class='menu_style1_a' id='tax_tab1' style='cursor:hand'>세금계산서 발행 </span></td></tr>
		<tr id='tab1_view' style='display:none' bgcolor=#ffffff>
		  <td class='leftmenu'>
			<table>
			  <tr >
				<Td height='20'><a href='/admin/tax/sales_write.php?tax_type=1' class='menu_style1_a'>매출작성</a></td>
			  </tr>
			  <tr>
				<Td height='20'><a href='/admin/tax/purchase_write.php?tax_type=1' class='menu_style1_a'>매입작성(역발행)</a></td>
			  </tr>
			  <tr>
				<td height='10'></td>
			  </tr>
			</table>
		  </td>
		</tr>
		";
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='/admin/tax/sales_write2.php?publish_type=3&tax_type=1' id='tax_tab2' class='menu_style1_a'>위수탁발행</span></td></tr>";
		
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='/admin/tax/modify_main.php' id='tax_tab3' class='menu_style1_a'>수정발행</span></td></tr>";

		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='/admin/tax/sales_list_3.php' id='tax_tab4' class='menu_style1_a'>일괄발행</span></td></tr>";

		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='/admin/tax/sales_list2.php' id='tax_tab5' class='menu_style1_a'>발행예정내역(임시저장)</span></td></tr>";
		
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='/admin/tax/sales_list.php' id='tax_tab6' class='menu_style1_a'>매출/매입 문서조회</span></td></tr>";

		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='#' id='tax_tab7' class='menu_style1_a'>미처리문서</span></td></tr>";

		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='#' id='tax_tab8' class='menu_style1_a'>국세청전송</span></td></tr>
		<tr id='tab8_view' style='display:none' bgcolor=#ffffff>
		  <td class='leftmenu'>
			<table>
			  <tr>
				<Td height='20'><a href='./tax_send.php' class='menu_style1_a'>전송하기</a></td>
			  </tr>
			  <tr>
				<Td height='20'><a href='#' class='menu_style1_a'>전송완료 조회</a></td>
			  </tr>
			  <tr>
				<Td height='20'><a href='./tax_stats.php' class='menu_style1_a'>전송현황</a></td>
			  </tr>
			  <tr>
				<Td height='20'><a href='./tax_setMain.php' class='menu_style1_a'>전송설정</a></td>
			  </tr>
			  <tr>
				<td height='10'></td>
			  </tr>
			</table>
		  </td>
		</tr>
		";

		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='./state.php' id='tax_tab2' class='menu_style1_a'>통계</span></td></tr>";

		$mstring .= "<tr height=20 bgcolor='#FFFFFF'><td align=left class='leftmenu'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange_triangle.gif' border=0>&nbsp;<a href='$default_path/tax/company_list.php' id='tax_tab3' class='menu_style1_a'>거래처관리</td></tr>";
		$mstring .= "</table>";

		return $mstring;
	}
?>