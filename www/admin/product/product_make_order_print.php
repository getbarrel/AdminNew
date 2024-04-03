<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
session_start();
$db = new Database();
$db->query("select * from ".TBL_SHOP_PRODUCT." where id ='$id'");
$db->fetch();
?>

<html>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<style>
TD {font-size:12px;}
.border_l_t_r_b_red {border-left:1px solid red;border-top:1px solid red;border-right:1px solid red;border-bottom:1px solid red;color:red;font-weight:bold}
.border_t_r_b_red {border-top:1px solid red;border-right:1px solid red;border-bottom:1px solid red;color:red;font-weight:bold}
.border_l_r_b_red {border-left:1px solid red;border-right:1px solid red;border-bottom:1px solid red;color:red;font-weight:bold}
.border_r_b_red {border-right:1px solid red;border-bottom:1px solid red;color:red;font-weight:bold}

.border_l_t_r_b_green {border-left:1px solid green;border-top:1px solid green;border-right:1px solid green;border-bottom:1px solid green;color:green;font-weight:bold}
.border_t_r_b_green {border-top:1px solid green;border-right:1px solid green;border-bottom:1px solid green;color:green;font-weight:bold}
.border_l_r_b_green {border-left:1px solid green;border-right:1px solid green;border-bottom:1px solid green;color:green;font-weight:bold}
.border_r_b_green {border-right:1px solid green;border-bottom:1px solid green;color:green;font-weight:bold}

.border_l_t_r_b_blue {border-left:1px solid blue;border-top:1px solid blue;border-right:1px solid blue;border-bottom:1px solid blue;color:blue;font-weight:bold}
.border_t_r_b_blue {border-top:1px solid blue;border-right:1px solid blue;border-bottom:1px solid blue;color:blue;font-weight:bold}
.border_l_r_b_blue {border-left:1px solid blue;border-right:1px solid blue;border-bottom:1px solid blue;color:blue;font-weight:bold}
.border_r_b_blue {border-right:1px solid blue;border-bottom:1px solid blue;color:blue;font-weight:bold}
</style>
<body>
<table cellpadding=5 cellspacing=0>
	<tr>
		<td style='border-right:1px dotted gray'><?=MakeOrderSheet($db,"red","(구매.자재부 보관용)")?></td>
		<td style='border-right:1px dotted gray'><?=MakeOrderSheet($db,"blue","(생산부 보관용)")?></td>
		<td><?=MakeOrderSheet($db,"green","(생산완료회신용)")?></td>
	</tr>
	
</table>		
</body>
</html>
<?

function MakeOrderSheet($mdb,$color="red",$title=""){
global $standard, $admin_config;
$mstring = "
<table cellpadding=3 cellspacing=0>
	<tr><td style='color:".$color.";'><b style='font-size:20;'>생산지시서</b><b> $title</b></td></tr>
	<tr><td style='color:".$color.";font-weight:bold'>생산번호  ".date("Ymd-his")."</td></tr>
	<tr>
		<td>
			<table cellpadding=3 cellspacing=0 width='100%'>
			<tr align=center><td class='border_l_t_r_b_".$color."'>생산부서</td><td class='border_t_r_b_".$color."' width=90><input type='text' style='color:".$color.";width:100%;border:0px;font-weight:bold'></td><td class='border_t_r_b_".$color."' rowspan=2>생산책임자<br>확&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;인</td><td class='border_t_r_b_".$color."' width=80 rowspan=2>&nbsp;</td></tr>
			<tr align=center><td class='border_l_r_b_".$color."' >생산완료일</td><td class='border_r_b_".$color."'  align=right><input type='text' style='color:".$color.";width:100%;border:0px;font-weight:bold' value='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;월 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;일'> </td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style='padding-top:10px;'>
			<table cellpadding=5 cellspacing=0 width='100%'>
			<tr align=center height=27 bgcolor='cccccc'><td class='border_l_t_r_b_".$color."' nowrap>상품코드</td><td class='border_t_r_b_".$color."' width=100 nowrap>상품명 / 색상</td><td class='border_t_r_b_".$color."' nowrap>규격</td><td class='border_t_r_b_".$color."' width=60 nowrap>단위</td><td class='border_t_r_b_".$color."' width=45 nowrap>수량</td></tr>			
			<tr align=center height=27><td class='border_l_r_b_".$color."' nowrap>&nbsp;".$mdb->dt[pcode]."</td><td class='border_r_b_".$color."' width=130 nowrap>".$mdb->dt[pname]."&nbsp;</td><td class='border_r_b_".$color."' >".$standard."&nbsp;</td><td class='border_r_b_".$color."' width=80 >".$mdb->dt[unit]."&nbsp;</td><td class='border_r_b_".$color."' width=80 ><input type='text' style='color:".$color.";width:100%;border:0px;font-weight:bold'></td></tr>
			<tr bgcolor='cccccc'><td colspan=5 class='border_l_r_b_".$color."'>상품이미지</td></tr>
			<tr><td colspan=5 class='border_l_r_b_".$color."' align=center><img src='".$admin_config[mall_data_root]."/images/product/m_".$mdb->dt[id].".gif' width=300 height=300></td></tr>
			<tr bgcolor='cccccc'><td colspan=5 class='border_l_r_b_".$color."'>특이사항</td></tr>
			<tr height=150><td colspan=5 class='border_l_r_b_".$color."'><textarea style='color:".$color.";height:100%;width:100%;border:0px;font-weight:bold;overflow:hidden'></textarea></td></tr>
			</table>
		</td>
	</tr>
</table>";

return $mstring;	
}
?>