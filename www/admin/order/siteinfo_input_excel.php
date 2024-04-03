<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
//auth(8);


$db = new Database;


$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			 <tr>
			    <td align='left' colspan=4> ".GetTitleNavigation("일괄송장입력(창고)", "상품관리 > 일괄송장입력(창고)")."</td>
			</tr>";

$Contents .= "
			<tr>
				<td align='left' colspan=4 style='padding-bottom:20px;'>
					<div class='tab'>
						<table class='s_org_tab'>
							<tr>
								<td class='tab'>
									<table id='tab_01' class='on'>
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='?acc_view_type='\">일괄송장입력</td>
											<th class='box_03'></th>
										</tr>
									</table>
									<table id='tab_02' >
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='oversea_warehouse_delivery_ready.php?fix_type_type=excel'\"> 해외프로세싱중 상품목록</td>
											<th class='box_03'></th>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			 <tr>
			 	<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'> 상단 배송준비중 상품목록에서 엑셀 다운로드 후 택배사에 등록후 사용하실수 있습니다. </b></div>")."</td>
			 </tr>
			 <tr>
			 	<td colspan=3>

			 	<form name='excel_input_form' method='post' action='siteinfo_input_excel.act.php' enctype='multipart/form-data' target='iframe_act' onsubmit='return CheckFormValue(this)' >
			 	<input type='hidden' name='act' value='excel_input'>
			 	<input type='hidden' name='cid' value=''>
			 	<input type='hidden' name='depth' value=''>
				<table width='100%' border=0 cellpadding=0 cellspacing=1 bgcolor=silver>
					<tr height=28 bgcolor=#ffffff align=center>
						<td bgcolor=#efefef width=20%><b>송장번호업테이트</b></td>
						<td id='select_category_path3' align=left style='padding-left:10px;'>송장번호가 등록되어 있는 주문에도 다시 업데이트 합니다.</td>
						<td rowspan=3 width=20%><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0></td>
					</tr>
					<tr height=28 bgcolor=#ffffff  align=center>
						<td bgcolor=#efefef><b>엑셀파일 입력</b>  </td>
						<td align=left style='padding-left:10px;'><input type=file class='textbox' name='excel_file' style='width:90%' validation=true title='엑셀파일 입력'></td>
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
								<td class='m_td'>상품코드</td>
								<td class='m_td'>해외사이트주문번호</td>
								<td class='e_td'>해외운송장번호</td>
							</tr>
							<tr height=25 bgcolor=#ffffff align=center>
								<td class='point'>201103102018-2234</td>
								<td >58415</td>
								<td >aoe-232123</td>
								<td class='point'>1234123113455</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr height=25>
						<td style='line-height:140%;padding:10px 0px 0px 0px'>
						1. 주문번호 : 주문번호를 – 포함하여 정확히 입력해 주세요. (A 열에 입력해주세요)<br>
						2. 상품코드 : 엑셀에서 출력하신 상품코드를 입력해 주시면 됩니다. (B 열에 입력해주세요)<br>
						3. 해외사이트주문번호 : 해외사이트에서 받은 주문번호를 입력해 주시기 바랍니다. (C 열에 입력해주세요)<br>
						4. 운송장번호 : 해외운송장 번호를 정확히 입력해 주세요. (D 열에 입력해주세요)<br>
						5. 일괄송장입력(창고)은 해외프로세싱중인 상품에 대해서만 일괄 업데이트 됩니다.<br>
						</td>
					</tr>
				</table>
				</td>
			 </tr>
			 </table>";


$Contents .=	"";


//$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";

if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";

	$inner_category_path = getCategoryPathByAdmin($cid, $depth);
	echo "<Script language='javascript'>
		parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
		parent.document.getElementById('select_category_path1').innerHTML='".$inner_category_path."';
		parent.document.getElementById('select_category_path2').innerHTML='".$inner_category_path."(".$total."개) ';
		parent.document.getElementById('select_category_path3').innerHTML='".$inner_category_path."';
		parent.document.forms['excel_input_form'].cid.value = '".$cid."';
		parent.document.forms['excel_input_form'].depth.value = '".$depth."';
		</Script>";
}else{
	$P = new LayOut();
	$P->strLeftMenu = order_menu("/admin",$category_str);
	$P->addScript = "<script Language='JavaScript' src='../include/zoom.js'></script><script Language='JavaScript' src='product_input_excel.js'></script>";
	$P->Navigation = "주문관리 > 일괄송장입력(창고)";
	$P->title = "일괄송장입력(창고)";
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}


?>