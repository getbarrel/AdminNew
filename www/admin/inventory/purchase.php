<?
include("../class/layout.class");
include("./inventory.lib.php");

$db = new Database;

$Contents ="
<form  name='input_frm' method='post' onsubmit='return purchaseSubmit(this)' action='./purchase.act.php' target='act'>
<input type=hidden name=act value='order_ready'>
<input type=hidden name=mmode value='".$mmode."'>
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("개별발주서작성", "발주관리 > 개별발주서작성")."</td>
</tr>
<tr >
	<td colspan=2 width='100%' valign=top style='padding-top:3px;'>
	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center height='120px'>
		<col width='50%'>
		<col width='50%'>

		<tr>
			<td colspan='2' height='25' style='padding:5px 0px;'>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>개별 발주서 작성</b>
			</td>
		</tr>
		<tr>
			<td colspan='2' style='padding:0px 0px;'>
				<table cellpadding=0 cellspacing=0 border=0 width=100% align=center class='input_table_box'>
					<col width='15%'>
					<col width='40%'>
					<col width='15%'>
					<col width='30%'>
					<tr height='30'>
						<td class='input_box_title' ><b>발주일자(작성자)</b> <img src='".$required3_path."'></td>
						<td class='input_box_item' >
							<input type='hidden' name='charger' value='".$_SESSION["admininfo"]["charger"]."'>
							<input type='hidden' name='charger_ix' value='".$_SESSION["admininfo"]["charger_ix"]."'>
							".date("Y-m-d")." (".$_SESSION["admininfo"]["charger"].")
						</td>
						<td class='input_box_title'> 납기일자 <img src='".$required3_path."'></td>
						<td class='input_box_item'>
							<img src='../images/".$admininfo["language"]."/calendar_icon.gif' align='absmiddle'>
							<input type='text' class='textbox point_color' name='limit_date' value='' style='height:20px;width:70px;text-align:center;' id='limit_datepicker' validation='true' title='납기일'> 까지
						</td>
					</tr>
					<tr height='30'>
						<td class='input_box_title'> 매입처 <img src='".$required3_path."'></td>
						<td class='input_box_item'>".SelectSupplyCompany("","ci_ix","select", "true", "1")."</td>
						<td class='input_box_title'>배송비 조건 <img src='".$required3_path."'> </td>
						<td class='input_box_item'><input type='text' class='textbox numeric' name='delivery_price' value='' validation='true' title='배송비' style='width:70px;'> 원 추가</td>
					</tr>
					<tr height='30'>
						<td class='input_box_title'>납품장소 <img src='".$required3_path."'></td>
						<td class='input_box_item'>
							<input type='radio' name='delivery_type' value='A' id='delivery_type_a' checked/><label for='delivery_type_a'>본사</label>
							<input type='radio' name='delivery_type' value='O' id='delivery_type_o' /><label for='delivery_type_o'>외부직배송</label>
						</td>
						<td class='input_box_title'>납품처 <img src='".$required3_path."'></td>
						<td class='input_box_item' id='td_delivery_type'>
							<div id='div_delivery_type_a'>
								".SelectEstablishment("","company_id","select","true","onChange=\"loadPlace(this,'pi_ix')\" ")."
								".SelectInventoryInfo("","",'pi_ix','select','true', "title='창고' onChange=\"getPlaceAddr($(this))\" ")."
							</div>
							<div id='div_delivery_type_o' style='display:none;'>
								<input type='text' class='textbox helpcloud' help_width='70' help_height='15' help_html='납품처명' name='delivery_name' value='' validation='false' title='납품처명' style='width:185px;'>
							</div>
						</td>
					</tr>
					<tr>
						<td class='input_box_title'>납품지 주소 <img src='".$required3_path."'></td>
						<td class='input_box_item' colspan=3 style='padding:5px 10px;'>
							<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
							<col width='80px'>
							<col width='100px'>
							<col width='*'>
							<tr>
								<td height=26>
									<input type='text' class='textbox' name='delivery_zip1' id='zipcode1' size='7' maxlength='7' value='' validation='true' title='배달주소 우편번호' readonly>
								</td>
								<td style='padding:1px 0 0 5px;'>
									<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('1');\" style='cursor:pointer;'>
								</td>
								<td></td>
							</tr>
							<tr>
								<td height=26 colspan='3'>
									<input type=text name='delivery_addr1'  id='addr1' value='' size=50 class='textbox' validation='true' title='배달주소' style='width:450px'>
								</td>
							</tr>
							<tr>
								<td height=26 colspan='3'>
									<input type=text name='delivery_addr2'  id='addr2'  value='' size=70 class='textbox' validation='false' title='배달주소' style='width:450px'> (상세주소)
								</td>
							</tr>
							</table>
						</td>
					</tr>
					<tr height='30'>
						<td class='input_box_title'>기타사항</td>
						<td class='input_box_item' colspan=3><input type=text class='textbox' name='msg' value='' id='msg' style='width:90%'></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</td>
</tr>
<tr>
	<td colspan='2' height=35></td>
</tr>
<tr>
	<td height='25' style='padding:10px 0px;'>
		<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>발주서 품목</b>
	</td>
	<td align=right>
		<a href=\"javascript:PopGoodsSelect();\"><img src='../images/".$admininfo["language"]."/btc_goods_add.gif' border='0' align='absmiddle'  style='cursor:pointer;'></a>
		<input type=text class='textbox number' value='바코드 입력&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' id='barcode' onclick=\"Submit_bool=false;$(this).val('')\">
	</td>
</tr>
<tr>
	<td colspan='2' height=300 style='vertical-align:top;'>
	";

$Contents .= "
	<table cellpadding=3 cellspacing=0 border=0 bgcolor=#ffffff width=100% onselect='return false;' align=center class='list_table_box' id='regist_item_list'>
				<col width=4% >
				<col width=6% >
				<col width='15%' >
				<col width=6% >
				<col width=6% >
				<col width=6% >
				<col width=6% >
				<col width=6% >
				<col width=7% >
				<col width=6% >
				<col width=7% >
				<col width=7% >
				<tr align=center height=30 style='font-weight:bold;' >
					<td class=s_td rowspan='2'><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.input_frm)'></td>
					<td class=m_td rowspan='2'>품목코드</td>
					<td class=m_td rowspan='2'>품목정보</td>
					<td class=m_td rowspan='2'>규격</td>
					<td class=m_td rowspan='2'>단위</td>
					<td class=m_td rowspan='2'>환산수량</td>
					<td class=m_td rowspan='2' nowrap>매입가</td>
					<td class=m_td rowspan='2' nowrap>발주수량</td>
					<td class=m_td colspan=2>재고현황</td>
					<td class=e_td rowspan='2' nowrap>합계</td>
				</tr>
				<tr align=center height=30>
					<td class=m_td>재고</td>
					<td class=m_td>부족재고</td>
				</tr>
				<tr height=30 depth=1 lack_bool=false style='cursor:pointer;' >
					<td align=center><input type=checkbox class='nonborder select_gid_unit' id='select_gid_unit'  name='select_gid_unit' value=''></td>
					<td align=center id='gid_text'></td>
					<td style='padding:3px;' nowrap>
						<input type=hidden name='item_infos[0][gid_unit]'  id='gid_unit' value=''>
						<input type=hidden name='item_infos[0][gname]' id='gname' value=''>
						<b id='gname_text'></b>
					</td>
					<td align=center><span  id='standard_text'></span><input type=hidden class='textbox numeric' name='item_infos[0][standard]' id='standard' value=''> </td>
					<td align=center id='unit_text'></td>
					<td style='text-align:center;' id='change_amount_text'></td>

					<td align=center class='point'><input type=text class='textbox numeric' name='item_infos[0][buy_price]' validation=true  id='buy_price' value='' size=8 title='매입가' onkeyup=\"$(this).closest('tr').find('#total_price').html(FormatNumber($(this).val()*$(this).closest('tr').find('#cnt').val()))\" ></td>

					<td style='text-align:center;' class='point'>
						<input type=text class='textbox numeric' name='item_infos[0][cnt]'  id='cnt' value='' size=8 title='수량' validation=true  style='width:80%;text-align:right;padding:0 5px 0 0' onkeyup=\"if($(this).val()=='0'){alert('수량 0을 입력하실수 없습니다.');$(this).val('1')}$(this).closest('tr').find('#total_price').html(FormatNumber($(this).val()*$(this).closest('tr').find('#buy_price').val()))\"> 
					</td>
					<td style='text-align:center;' id='stock'></td>
					<td style='text-align:center;' id='lack_stock'></td>
					<td align=center id='total_price'></td>
				</tr>
			</table>
			<table cellpadding=0 cellspacing=0 border=0 width=100%>
				<tr>
					<td colspan=4 align=left>
						<table cellpadding=0>
							<tr height=40>
								<td><img src='../images/".$admininfo["language"]."/btc_select_goods_delete.gif' border='0' align='absmiddle' onclick='checkDelete()' style='cursor:pointer;'></td>
							</tr>
						</table>
					</td>
					<td colspan=4>
						<table cellpadding=10 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
							<tr>
								<td align='right' style='font-size:12px;font-weight:bold;'></td>
							</tr>
						</table>
					</td>
				</tr>
			</table> ";

$Contents .= "
	</td>
</tr>
<tr height=20>
	<td colspan='2' style='padding:3px;' align=center>
		<input type='checkbox' name='is_continue' id='is_continue' value='1'><label for='is_continue'>작성후 계속 작성</label> <img src='../images/".$admininfo["language"]."/b_save.gif' onclick=\"Submit_bool=true;$(this).closest('form').submit();\" border=0 align=absmiddle style='cursor:pointer;'>
	</td>
</tr>
</table>
</form>";

$help_text = "
<table cellpadding=2 cellspacing=0 class='small' style='line-height:120%' >
	<col width=8>
	<col width=*>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td>발주서 작성은 매입처가 다를 경우 별도로 작성을 하셔야 합니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td>발주고자 하는 정보와 품목의 수량정보를 입력하신후 저장버튼을 눌러 발주 작성을 완료 하실 수 있습니다. </td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td>재고와 부족재고는 납품처를 선택한 사업장에 대한 기준으로 재고가 나옵니다.</td></tr>
</table>
";

$Contents .= HelpBox("발주서", $help_text,"100");

$Script = "
<!--link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' /-->
<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>

<script language='JavaScript' >

$(document).ready(function(){
	$('#limit_datepicker').datepicker({
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'
	});

	$('input[name=delivery_type]').click(function(){
		delivery_type_click($(this));
	})
	
	$('#barcode').keypress(function(e){
		if(e.keyCode==13){
			BarcodeGoodsSelect($(this));
		}
	})

});

Submit_bool=true;
function purchaseSubmit(frm) {
	if(!Submit_bool){
		return false;
	}

	if(!CheckFormValue(frm)){
		return false;
	}
}

function zipcode(type) {
	var zip = window.open('../member/zipcode.php?zip_type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}

function delivery_type_click(obj){
	var id_str = obj.attr('id');
	$('#td_delivery_type div').hide();
	$('#td_delivery_type div [validation=true]').attr('validation','false');

	$('#div_'+id_str).show();
	$('#div_'+id_str+' [validation=false]').attr('validation','true');
}


function getPlaceAddr(obj){
	//getPlaceData 함수는 placesection.js에~
	json_data = getPlaceData(obj);

	zip=json_data.place_zip.split('-');

	$('#zip1').val(zip[0]);
	$('#zip2').val(zip[1]);
	$('#addr1').val(json_data.place_addr1);
	$('#addr2').val(json_data.place_addr2);
}


function clearAll(frm){
	$('.select_gid_unit').each(function(){
		$(this).attr('checked',false);
	});
}

function checkAll(frm){
	$('.select_gid_unit').each(function(){
		$(this).attr('checked','checked');
	});
}

function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;
	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}

function PopGoodsSelect(){
	ShowModalWindow('goods_select.php?page_type=stocked&stock_company_id='+$('#company_id').val(),1000,800,'goods_select');
	get_company_safestock();
}

function get_company_safestock(){
	
	var safestock=0;
	var lack_stock=0;
	var bool=false;
	
	obj=$('#regist_item_list tr[lack_bool=false]:first');
	
	if(obj.length){
		gid_unit=obj.find('#gid_unit').val().split('|');
		gid=gid_unit[0];
		unit=gid_unit[1];
		stock=obj.find('#stock').html();
		bool=true;
	}

	if(bool){
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'get_company_safestock', 'company_id':$('#company_id').val(), 'gid':gid, 'unit':unit},
			url: './purchase.act.php',  
			dataType: 'json', 
			error: function(x, o, e){
				 alert(x.status + ' : '+ o +' : '+e);
			},
			success: function(data){ 
				safestock=data.safestock;
				if(safestock==0){
					lack_stock=stock;
				}else{
					lack_stock=safestock-stock
				}
				obj.find('#lack_stock').html(lack_stock);
				obj.attr('lack_bool',true);
				get_company_safestock();
			}
		});
	}
}

function BarcodeGoodsSelect(obj){
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'get_goods_barcode', 'company_id':$('#company_id').val(), 'barcode':obj.val()},
		url: './purchase.act.php',  
		dataType: 'json', 
		error: function(x, o, e){
			 alert(x.status + ' : '+ o +' : '+e);
		},
		success: function(data){
			if(data.gid!=null){
				GoodsInsert(data);
				get_company_safestock();
			}else{
				alert('검색된 품목이 없습니다.');
			}
		}
	});

	obj.val('');
}

function GoodsSelect(gid,gname,unit,unit_text,standard, buying_price, company_name, place_name, section_name, pi_ix, ps_ix, vdate, expiry_date, stock,wholesale_price,sellprice,surtax_div,surtax_text,change_amount){
	var data = {};
	data['gid']=gid;
	data['gname']=gname;
	data['unit']=unit;
	data['unit_text']=unit_text;
	data['standard']=standard;
	data['change_amount']=change_amount;
	data['buying_price']=buying_price;
	data['stock']=stock;
	GoodsInsert(data);
	get_company_safestock();
}

function GoodsInsert(data){
	var gid=data.gid;
	var unit=data.unit;
	var gname=data.gname;
	var standard=data.standard;
	var unit_text=data.unit_text;
	var change_amount=data.change_amount;
	var buying_price=data.buying_price;
	if(data.stock!=null){
		var stock=data.stock;
	}else{
		var stock='0';
	}

	var tbody = $('#regist_item_list tbody');
	var thisRow = tbody.find('tr[depth^=1]:last');
	var total_rows = parseInt(thisRow.find('#gid_unit').attr('name').replace(/[^0-9]/g,''))+1;
	var safestock=0;
	if(thisRow.find('#gid_unit').val() == ''){
		thisRow.attr('lack_bool',false);
		thisRow.find('#gid_unit').val(gid+'|'+unit);   
		thisRow.find('#gid_text').html(gid);   
		thisRow.find('#gname_text').html(gname);
		thisRow.find('#gname').val(gname);
		thisRow.find('#standard_text').html(standard);
		thisRow.find('#standard').html(standard);
		thisRow.find('#unit_text').html(unit_text);
		thisRow.find('#change_amount_text').html(change_amount);
		thisRow.find('#buy_price').val(buying_price);
		thisRow.find('#stock').html(stock);
	}else{

		var newRow = tbody.find('tr[depth^=1]:first').clone(true).appendTo(tbody);  
		
		newRow.attr('lack_bool',false);
		newRow.find('#gid_unit').attr('name','item_infos['+(total_rows)+'][gid_unit]');
		newRow.find('#gname').attr('name','item_infos['+(total_rows)+'][gname]');
		newRow.find('#standard').attr('name','item_infos['+(total_rows)+'][standard]');
		newRow.find('#buy_price').attr('name','item_infos['+(total_rows)+'][buy_price]');
		newRow.find('#cnt').attr('name','item_infos['+(total_rows)+'][cnt]');

		newRow.find('#gid_unit').val(gid+'|'+unit);   
		newRow.find('#gid_text').html(gid);   
		newRow.find('#gname_text').html(gname);
		newRow.find('#gname').val(gname);
		newRow.find('#standard_text').html(standard);
		newRow.find('#standard').html(standard);
		newRow.find('#unit_text').html(unit_text);
		newRow.find('#change_amount_text').html(change_amount);
		newRow.find('#buy_price').val(buying_price);
		newRow.find('#stock').html(stock);
		newRow.find('#cnt').val('');
		newRow.find('#total_price').html('');
	}
}

function checkDelete(){
   var tbody = $('#regist_item_list tbody');
	var thisRow = tbody.find('tr[depth^=1]:last');  

	$('.select_gid_unit').each(function(){
		if($(this).attr('checked') == 'checked'){
			var total_rows = tbody.find('tr[depth^=1]').length;  
			if(total_rows > 1){
				$(this).closest('tr').remove();
			}else{
				
				thisRow = $(this).closest('tr');
				thisRow.attr('lack_bool',false);
				thisRow.find('#gid_unit').attr('name','item_infos[0][gid_unit]');
				thisRow.find('#gname').attr('name','item_infos[0][gname]');
				thisRow.find('#standard').attr('name','item_infos[0][standard]');
				thisRow.find('#buy_price').attr('name','item_infos[0][buy_price]');
				thisRow.find('#cnt').attr('name','item_infos[0][cnt]');
				thisRow.find('#gid_unit').val('');   
				thisRow.find('#gid_text').html('');   
				thisRow.find('#gname_text').html('');
				thisRow.find('#gname').val('');
				thisRow.find('#standard_text').html('');
				thisRow.find('#standard').html('');
				thisRow.find('#unit_text').html('');
				thisRow.find('#change_amount_text').html('');
				thisRow.find('#buy_price').val('');
				thisRow.find('#stock').html('');
				thisRow.find('#cnt').val('');
				thisRow.find('#total_price').html('');
				thisRow.find('#lack_stock').html('');
				
			}
		}
	});
}

 



</Script>
";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = $Script;
	$P->Navigation = "재고관리 > 발주관리 > 개별발주서작성 ";
	$P->NaviTitle = "개별발주서작성";
	$P->strContents = $Contents;
	$P->PrintLayOut();
}else{
	$P = new LayOut;
	$P->addScript = $Script;
	$P->OnloadFunction = "";
	$P->strLeftMenu = inventory_menu();
	$P->strContents = $Contents;
	$P->Navigation = "재고관리 > 발주관리 > 개별발주서작성 ";
	$P->title = "개별발주서작성";
	$P->PrintLayOut();
}

?>