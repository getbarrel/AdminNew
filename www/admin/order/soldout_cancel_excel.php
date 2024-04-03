<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
//auth(8);


$db = new Database;


$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			 <tr>
			    <td align='left' colspan=4> ".GetTitleNavigation("일괄품절처리", "주문관리 > 일괄품절처리")."</td>
			</tr>";

$Contents .= "
			 <tr>
			 	<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'> 일괄품절처리하실 엑셀리스트를 아래와 같이 작성해주신후 사용하실수 있습니다. </b></div>")."</td>
			 </tr>
			 <tr>
			 	<td colspan=3>

			 	<form name='excel_input_form' method='post' action='soldout_cancel_excel.act.php' enctype='multipart/form-data' target='iframe_act' onsubmit='return CheckFormValue(this)' >
			 	<input type='hidden' name='act' value='excel_input'>
				<table width='100%' border=0 cellpadding=0 cellspacing=1 bgcolor=silver>
					<tr height=28 bgcolor=#ffffff align=center>
						<td bgcolor=#efefef><b>엑셀파일 입력</b>  </td>
						<td align=left style='padding-left:10px;'><input type=file class='textbox' name='excel_file' style='width:90%' validation=true title='엑셀파일 입력'></td>
						<td width=20%><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0></td>
					</tr>
				</table>
				</form>
			 	</td>
			 </tr>
			 <tr>
				<td style='padding:30px 0px 0px 0px'>
				<table width=100%  border=0>
					<tr height=25>
						<td style='border-bottom:2px solid #efefef'>
						<img src='../images/dot_org.gif' align=absmiddle> <b>샘플작성 참고사항</b>
						</td>
					</tr>
					<tr height=25>
						<td >
						엑셀정보에는 \" 따옴표를 사용하실 수 없습니다.
						</td>
					</tr>
					<tr height=25>
						<td >
						<table width=100%  border=0 cellpadding=0 cellspacing=1 class='list_table_box'>
							<tr height=25 bgcolor=#ffffff align=center>
								<td class='s_td'>주문번호</td>
								<td class='e_td'>상품코드</td>
							</tr>
							<tr height=25 bgcolor=#ffffff align=center>
								<td class='point'>201103102018-2234</td>
								<td class=''>35101</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr height=25>
						<td style='line-height:140%;padding:10px 0px 0px 0px'>
						1. 주문번호 : 주문번호를 – 포함하여 정확히 입력해 주세요. (A 열에 입력해주세요)<br>
						2. 상품코드 : 엑셀에서 출력하신 상품코드를 입력해 주시면 됩니다. (B 열에 입력해주세요)<br>
						3. 일괄 품절처리는 입금예정,입급확인,배송준비중인 상태일때만 가능합니다.
						</td>
					</tr>
				</table>
				</td>
			 </tr>
			 </table>";

$Contents .=	"";


$P = new LayOut();
$P->strLeftMenu = order_menu();
$P->addScript = "<script Language='JavaScript' src='../include/zoom.js'></script><script Language='JavaScript' src='product_input_excel.js'></script>";
$P->Navigation = "주문관리 > 일괄품절처리";
$P->title = "일괄품절처리";
$P->strContents = $Contents;
$P->PrintLayOut();


?>