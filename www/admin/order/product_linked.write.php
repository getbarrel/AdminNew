<?php
include("../class/layout.class");

$db = new Database;

$slave_db->query("select * from sellertool_site_info where disp='1' ");
$sell_order_from = $slave_db->fetchall('object');

$Script = "
	<Script Language='JavaScript'>
	$(document).ready(function (){
	   $('#addRowBtn').click(function (){
	       var addNum = parseInt($('#addNum').val());
	       if(addNum > 0){
	           for(var i=0; i < addNum; i++){
	               var trObj = $('#product_linked_table tr:last').clone();
	               trObj.find('select,input').val('');
	               $('#product_linked_table').append(trObj);
	           }
	       }else{
	           alert('행수를 입력해주세요');
	       }
	   });
	   
	   $('#batchSiteCodeBtn').click(function (){
	       $('.site_code').val($('#batchSiteCode').val());
	   });
	   
	   $('#batchQtyBtn').click(function (){
	       $('.qty').val($('#batchQty').val());
	   });
	   
	   $('.delRow').click(function (){
	       var trObj = $('#product_linked_table tr');
	       if(trObj.length == 2){
               trObj.find('select,input').val('');
	       }else{
	           $(this).closest('tr').remove();
	       }
	   })
	});
	
	function SubmitX(frm){
		for(i=0;i < frm.elements.length;i++){
			if(!CheckForm(frm.elements[i])){
				return false;
			}
		}
	}
	</Script>";


$Contents = "
	<table width='100%' border='0' align='left' cellspacing='0' cellpadding='0'>
	 <tr>
		<td align='left' colspan=6 > " . GetTitleNavigation("제휴품목 코드구성 등록", "주문관리 > 수동주문 > 제휴품목 코드구성 등록") . "</td>
	</tr>
	  <tr>
		<td>
			<form name='main_frm' method='post' onSubmit='return SubmitX(this)' action='product_linked.act.php' style='display:inline;' enctype='multipart/form-data' target='act'>
			<input type='hidden' name='act' value='insert'>
			<table border='0' width='100%' cellspacing='1' cellpadding='0'>
			  <tr>
				<td style='padding:0px 0px 20px 0px'>
				<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02'></td>
						<th class='box_03'></th>
					</tr>
					<tr>
						<th class='box_04'></th>
						<td class='box_05'  valign=top style='padding:0px'>
						<table border='0' cellpadding=0 cellspacing=0 width='100%' class='search_table_box' id='product_linked_table'>
						  <col width='15%'>
						  <col width='25%'>
						  <col width='20%'>
						  <col width='10%'>
						  <col width='*'>
						  <col width='5%'>
						  <tr height=27>
								<td class='s_td'> <b>판매처</b> <img src='" . $required3_path . "'> </td>
								<td class='m_td'> <b>제휴 품목코드</b> <img src='" . $required3_path . "'></td>
								<td class='m_td'> <b>품목코드(ERP)</b> <img src='" . $required3_path . "'></td>
								<td class='m_td'> <b>품목수량</b> <img src='" . $required3_path . "'></td>
								<td class='m_td'> <b>비고</b></td>
								<td class='e_td'> <b>관리</b></td>
						    </tr>
						    <tr height=27>
							  <td class='search_box_item'>
								    <select name='site_code[]' class='site_code' title='판매처' validation=true>
								        <option value=''>선택해주세요.</option>";
foreach ($sell_order_from as $orderFrom) {
    $Contents .= "<option value='" . $orderFrom['site_code'] . "'>" . $orderFrom['site_name'] . "</option>";
}
$Contents .= "				    
                                </select>
							  </td>
							  <td class='search_box_item'>
								    DEWYTREE <input type='text' name='sg_code[]' class='textbox' style='width:70%' align='absmiddle' title='제휴 품목코드' validation=true>
							  </td>
							  <td class='search_box_item'>
								    <input type='text' name='gid[]' class='textbox' style='width:90%' align='absmiddle' title='품목코드(ERP)' validation=true>
							  </td>
							  <td class='search_box_item'>
								    <input type='text' name='qty[]' class='textbox numeric qty' style='width:85%' align='absmiddle' title='품목수량' validation=true>
							  </td>
							  <td class='search_box_item'>
                                <input type='text' name='memo[]' class='textbox' style='width:90%' align='absmiddle' title='비고'>
							  </td>
							  <td class='search_box_item'>
                                <img  src='../images/" . $admininfo["language"] . "/btc_del.gif' border=0 class='delRow' style='cursor:pointer;'>
							  </td>
							</tr>
						</table>
						</td>
						<th class='box_06'></th>
					</tr>
					<tr>
						<th class='box_07'></th>
						<td class='box_08'></td>
						<th class='box_09'></th>
					</tr>
				</table>
				</td>
			  </tr>
			  <tr>
				<td bgcolor='#ffffff'>
				  <table border='0' cellspacing='0' cellpadding='0' width='100%'>
					<tr>
					  <td >
						<table border='0' cellpadding=0 cellspacing=0 width='100%'>
						<col width='55%'>
						<col width='*'>
                    <tr>
                        <td align='right'><input type=image src='../images/" . $admininfo["language"] . "/b_save.gif' align=absmiddle border=0></td>
                        <td align='right'>
                            <input type='text' class='textbox numeric' id='addNum' style='width:30px' align='absmiddle' value='10'> 행
                            <input type='button' id='addRowBtn' value='추가'>
                            |
                            <select id='batchSiteCode'>
                                    <option value=''>선택해주세요.</option>";
foreach ($sell_order_from as $orderFrom) {
    $Contents .= "<option value='" . $orderFrom['site_code'] . "'>" . $orderFrom['site_name'] . "</option>";
}
$Contents .= "				    
                            </select> <input type='button' id='batchSiteCodeBtn' value='판매처 일괄 변경'>
                            |
                            <input type='text' class='textbox numeric' id='batchQty' style='width:30px' align='absmiddle' value='1'> 개
                            <input type='button' id='batchQtyBtn' value='품목수량 일괄 변경'>
                        </td>
                    </tr>
					  </table>

					  </td>
					</tr>
				  </table>
				</td>
			  </tr>
			</table>
			</form>
		</td>
	  </tr>
  </table>";

$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "주문관리 > 수동주문 > 제휴품목 코드구성 등록";
$P->title = "제휴품목 코드구성 등록";
$P->OnloadFunction = "";
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();