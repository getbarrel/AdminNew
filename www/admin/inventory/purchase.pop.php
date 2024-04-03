<?
include("../class/layout.class");
include("inventory.lib.php");



$db = new Database;

$Script="
<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>
<script type='text/javascript'>
<!--
$(document).ready(function(){

	$('input[name=delivery_type]').click(function(){
		delivery_type_click($(this));
	})
	
	$('input.real_cnt,input.cancel_cnt').keyup(function(){
		input_cnt_check($(this));
	})
	
	$('input[class*=remain_cnt_]').keyup(function(){
		remain_cnt_check($(this));
	})


	$('.expiry_date:not(.point_color)').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'
	});

});


function remain_cnt_check(obj){
	var maxCnt = parseInt(obj.attr('maxCnt'));
	var iod_ix = obj.closest('tr').attr('iod_ix');
	var sum_remain_cnt=0;
	var tmp_cnt=0;

	$('.remain_cnt_'+iod_ix).each(function(){
		sum_remain_cnt+=parseInt($(this).val());
	});

	if(sum_remain_cnt > maxCnt){
		obj.val(obj.val()-(sum_remain_cnt-maxCnt));
	}
}

function input_cnt_check(obj){
	var tr_obj = obj.closest('tr');
	var real_cnt = parseInt(tr_obj.find('#real_cnt').val());
	var cancel_cnt = parseInt(tr_obj.find('#cancel_cnt').val());
	var remain_cnt = parseInt(tr_obj.find('#remain_cnt').val());
	if(remain_cnt < real_cnt+cancel_cnt){
		if(obj.attr('id')=='real_cnt'){
			obj.val(remain_cnt-cancel_cnt);
		}else{
			obj.val(remain_cnt-real_cnt);
		}
	}
}

Submit_bool=true;
function stockedSubmit(frm) {
	if(!Submit_bool){
		return false;
	}

	if(!$('.select_iod_ix:checked').length){
		alert('입고하려는 품목을 선택하셔야 합니다.');
		return false;
	}

	if(!CheckFormValue(frm)){
		return false;
	}
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

function zipcode(type) {
	var zip = window.open('../member/zipcode.php?type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}

function change_act(status){

	if(status=='ACC'){
		str='청구확정취소';
	}else{
		str='발주예정';
	}

	if(confirm(str+'로 변경하시겠습니까?')){
		$('input[type=hidden][name=status]').val(status);
		$('form[name=order_pop]').submit();
	}
}

function clearAll(frm){
	$('.select_iod_ix:not(:disabled)').each(function(){
		$(this).attr('checked',false);
	});
}

function checkAll(frm){
	$('.select_iod_ix:not(:disabled)').each(function(){
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

function row_division(iod_ix){
	var thisRow='no_obj';
	$('#table_stocked tr[iod_ix='+iod_ix+'] #remain_cnt').each(function(){
		if(parseInt($(this).val()) > 1){
			if(thisRow=='no_obj'){
				thisRow=$(this).closest('tr');
			}
		}
	});
	
	if(thisRow=='no_obj'){
		alert('분할할수 있는 로우가 없습니다.');
	}else{
		
		var total_row = $('#table_stocked tr').length;
		var newRow = thisRow.clone(true).insertAfter(thisRow);

		thisRow.find('#remain_cnt').removeClass('point_color').attr('readonly',false).val(parseInt(thisRow.find('#remain_cnt').val())-1);
		thisRow.find('#real_cnt').val('0');
		thisRow.find('#cancel_cnt').val('0');


		newRow.find('[name^=item_infos]').each(function(){
			$(this).attr('name',$(this).attr('name').replace(/item_infos\[[0-9]+\]/g,'item_infos['+total_row+']'));
		});
		newRow.find('#remain_cnt').removeClass('point_color').attr('readonly',false).val('1');
		newRow.find('.expiry_date').removeClass('point_color').attr('readonly',false).attr('id','expiry_date_'+total_row).val('').datepicker('destroy').datepicker({
			//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			//showMonthAfterYear:true,
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			buttonText: '달력'
	   });
		
		newRow.find('#real_cnt').val('0');
		newRow.find('#cancel_cnt').val('0');
		newRow.find('#select_iod_ix').val('clone');
	}
}


function checkRemainCnt(){

	$('#table_stocked tbody').find('tr').each(function (){
		
		var remain_cnt = $(this).find('#remain_cnt').val();
		$(this).find('#real_cnt').val(remain_cnt);
	});

}


//-->
</script>
";

$db->query("SELECT * FROM inventory_order where ioid = '".$ioid."'");
$io_info=$db->fetch();

$input_view=false;
if(!in_array($io_info["status"],array("AC","ACC","OR","ORC"))){
	$input_view=true;
}

if($view_type=="stocked"){
	$Contents = "
	<form  name='input_frm' method='post' onsubmit='return stockedSubmit(this)' action='./purchase.act.php' target='act'>
	<input type=hidden name='act' value='part_stocked'>";
}else{
	$Contents = "
	<form name='order_pop' method='post' onsubmit='return CheckFormValue(this)' action='purchase.act.php' target='act'>
	<input type='hidden' name='act' value='status_update_pop'>
	<input type='hidden' name='status' value=''>";
}

$Contents .= "
<input type='hidden' name='ioid' value='".$ioid."'>
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr height=25 align='left'>
				<td style='padding:10px 0px 0px 0px'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title' onclick='CheckFormValue(document.order_pop)'>발주정보</b></td>
			</tr>
			<tr>
				<td style='padding:5px 0px 0px 0px'>
						<table border=0 cellpadding=0 cellspacing=0 width='100%'>
							<tr>
								<td width='*' valign=top>
									<table border=0 cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
										<col width='20%' >
										<col width='30%' >
										<col width='20%' >
										<col width='30%' >
										<tr align='left' >
											<td class='search_box_title'><b>청구요청일자(작성자)</b></td>
											<td class='search_box_item'>".$io_info[regdate]."(".$io_info[charger].")</td>
											<td class='search_box_title'><b>납기일자</b></td>
											<td class='search_box_item'>".$io_info[charger]."</td>
										</tr>
										<tr align='left' >
											<td class='search_box_title'><b>매입업체</b></td>
											<td class='search_box_item'>".$io_info[ci_name]."</td>
											<td class='search_box_title'><b>배송비조건</b></td>
											<td class='search_box_item'>".number_format($io_info[delivery_price])."</td>
										</tr>";
										if($view_type=="update"){
											$Contents .= "
											<tr align='left'>
												<td class='search_box_title'><b>납품장소 </b></td>
												<td class='search_box_item'>
													<input type='radio' name='delivery_type' value='A' id='delivery_type_a' ".($io_info[pi_ix] ? "checked" : "")."/><label for='delivery_type_a'>본사</label>
													<input type='radio' name='delivery_type' value='O' id='delivery_type_o' ".(!$io_info[pi_ix] ? "checked" : "")."/><label for='delivery_type_o'>외부직배송</label>
												</td>
												<td class='search_box_title'><b>납품처 </b></td>
												<td class='search_box_item' id='td_delivery_type'>
													<div id='div_delivery_type_a'>
														".SelectEstablishment($io_info[company_id],"company_id","select","true","onChange=\"loadPlace(this,'pi_ix')\" ")."
														".SelectInventoryInfo($io_info[company_id],$io_info[pi_ix],'pi_ix','select','true', "title='창고' onChange=\"getPlaceAddr($(this))\" ")."
													</div>
													<div id='div_delivery_type_o' style='display:none;'>
														<input type='text' class='textbox helpcloud' help_width='70' help_height='15' help_html='납품처명' name='delivery_name' value='' validation='false' title='납품처명' style='width:185px;'>
													</div>
												</td>
											</tr>
											<tr align='left'>
												<td class='search_box_title'><b>납품지 주소</b></td>
												<td class='search_box_item' colspan='3' style='padding:5px 10px;'>
													<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
														<col width='120px'>
														<col width='100px'>
														<col width='*'>
														<tr>
															<td height=26>";

																$delivery_zip = explode("-",$io_info[delivery_zip]);
																
																$Contents .= "
																<input type='text' class='textbox' name='delivery_zip1' id='zip1' size='5' maxlength='5' value='".$delivery_zip[0]."' validation='true' title='배달주소 우편번호' readonly><!-- -
																<input type='text' class='textbox' name='delivery_zip2' id='zip2' size='5' maxlength='5' value='".$delivery_zip[1]."' validation='true' title='배달주소 우편번호' readonly>-->
															</td>
															<td style='padding:1px 0 0 5px;'>
																<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('1');\" style='cursor:pointer;'>
															</td>
															<td></td>
														</tr>
														<tr>
															<td height=26 colspan='3'>
																<input type=text name='delivery_addr1'  id='addr1' value='".$io_info[delivery_addr1]."' size=50 class='textbox' validation='true' title='배달주소' style='width:450px'>
															</td>
														</tr>
														<tr>
															<td height=26 colspan='3'>
																<input type=text name='delivery_addr2'  id='addr2'  value='".$io_info[delivery_addr2]."' size=70 class='textbox' validation='false' title='배달주소' style='width:450px'> (상세주소)
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr align='left'>
												<td class='search_box_title'><b>기타사항 </b></td>
												<td class='search_box_item' colspan='3'><input type=text class='textbox' name='msg' value='".$io_info[msg]."' id='msg' style='width:90%'></td>
											</tr>";
										}else{
											$Contents .= "
											<tr align='left'>
												<td class='search_box_title'><b>납품장소 </b></td>
												<td class='search_box_item'>
													".($io_info[pi_ix] ? "본사" : "외부직배송")."
												</td>
												<td class='search_box_title'><b>납품처 </b></td>
												<td class='search_box_item'>
													".$io_info[delivery_name]."
												</td>
											</tr>
											<tr align='left'>
												<td class='search_box_title'><b>납품지 주소</b></td>
												<td class='search_box_item' colspan='3' style='padding:5px 10px;'>
													[".$io_info[delivery_zip]."] ".$io_info[delivery_addr1]." ".$io_info[delivery_addr2]."
												</td>
											</tr>
											<tr align='left'>
												<td class='search_box_title'><b>기타사항 </b></td>
												<td class='search_box_item' colspan='3'>".$io_info[msg]."</td>
											</tr>";
										}
									$Contents .= "
									</table>
								</td>
							</tr>
						</table>
				</td>
			</tr>
			<tr>
				<td align='left' style='padding:30px 0px 0px 0px' height=25><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>품목정보</b></td>
			</tr>
			<tr>
				<td style='padding:5px 0px 0px 0px'>";
					if($view_type=="stocked"){
						$Contents .= "
						<table border=0 cellpadding=0 cellspacing=1 width='100%' class='list_table_box' id='table_stocked'>
							<col width=3%>
							<col width=*>
							<col width=12%>
							<col width=8%>
							<col width=8%>
							<col width=10%>
							<col width=8%>
							<col width=8%>
							<col width=12%>
							<col width=6%>
							<tr height=30px bgcolor=#e5e5e5>
								<td class='m_td' align=center><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.input_frm)'></td>
								<td class='m_td' align=center>품명</td>
								<td class='m_td' align=center>규격</td>
								<td class='m_td' align=center>단위</td>
								<td class='m_td' align=center>잔여수량</td>
								<td class='m_td' align=center>유통기한</td>
								<td class='m_td' align=center>
								입고수량
								<a href=\"javascript:checkRemainCnt(this);\">-></a>
								</td>
								<td class='m_td' align=center>취소수량</td>
								<td class='m_td' align=center>기본입고보관장소</td>
								<td class='e_td' align=center>관리</td>
							</tr>";
							
							$sql="SELECT iod.*, ips.ps_ix, ips.section_name FROM 
									inventory_order_detail iod
									left join inventory_goods_basic_place igbp 
										on (igbp.company_id='".$io_info[company_id]."' and igbp.pi_ix='".$io_info[pi_ix]."' and igbp.gid=iod.gid and igbp.unit=iod.unit)
									left join inventory_place_section ips on (ips.ps_ix=igbp.ps_ix)
									where iod.ioid = '".$ioid."'";
							$db->query($sql);

							for($i=0;$i < $db->total;$i++){
								$db->fetch($i);
								
								if(($db->dt[cnt]-$db->dt[real_cnt]-$db->dt[cancel_cnt]) > 0){
									$Contents .= "
									<tr bgcolor='#ffffff' height=27 iod_ix='".$db->dt[iod_ix]."'>
										<td align=center>
											<input type=checkbox class='nonborder select_iod_ix' id='select_iod_ix'  name=item_infos[".$i."][iod_ix] value='".$db->dt[iod_ix]."' />
											<input type=hidden name=item_infos[".$i."][b_iod_ix] value='".$db->dt[iod_ix]."' />
										</td>
										<td align=left style='padding-left:3px;'>".$db->dt[gname]."</td>
										<td align=center>".$db->dt[standard]."</td>
										<td align=center>".getUnit($db->dt[unit], "","","text")."</td>
										<td align=center>
											<input type=text class='textbox numeric point_color remain_cnt_".$db->dt[iod_ix]."' name='item_infos[".$i."][remain_cnt]' maxCnt='".($db->dt[cnt]-$db->dt[real_cnt]-$db->dt[cancel_cnt])."' id='remain_cnt' value='".($db->dt[cnt]-$db->dt[real_cnt]-$db->dt[cancel_cnt])."' size=8 readonly />
										</td>
										<td align=center>
											<input type=text class='textbox expiry_date ".($db->dt[expiry_date]!='' ? "point_color" :"")."' name='item_infos[".$i."][expiry_date]'  id='expiry_date_".$i."' value='".$db->dt[expiry_date]."' size=10 title='유통기한' ".($db->dt[expiry_date]!='' ? "readonly" :"")."/> 
										</td>
										<td align=center>
											<input type=text class='textbox numeric real_cnt' name='item_infos[".$i."][real_cnt]' id='real_cnt' value='0' size=8 title='입고수량' validation=true style='width:80%;text-align:right;padding:0 5px 0 0' >
										</td>
										<td align=center>
											<input type=text class='textbox numeric cancel_cnt' name='item_infos[".$i."][cancel_cnt]' id='cancel_cnt' value='0' size=8 title='취소수량' validation=true style='width:80%;text-align:right;padding:0 5px 0 0'>
										</td>
										<td align=center>";
											if($io_info[pi_ix]){
												if($db->dt[ps_ix]){
													$Contents .= $db->dt[section_name];
												}else{
													$Contents .= "<b class='red'>지정안됨</b>";
												}
											}else{
												$Contents .= "외부직배송";
											}
										$Contents .= "
										</td>
										<td align=center>
											<input type='button' value='분할' onclick=\"row_division('".$db->dt[iod_ix]."');\" />
										</td>
									</tr>";
								}else{
									$Contents .= "
									<tr bgcolor='#ffffff' height=27>
										<td align=center></td>
										<td align=left style='padding-left:3px;'>".$db->dt[gname]."</td>
										<td align=center>".$db->dt[standard]."</td>
										<td align=center>".getUnit($db->dt[unit], "","","text")."</td>
										<td align=center>".number_format($db->dt[cnt]-$db->dt[real_cnt]-$db->dt[cancel_cnt])."</td>
										<td align=center>".$db->dt[expiry_date]."</td>
										<td align=center>".number_format($db->dt[real_cnt])."</td>
										<td align=center>".number_format($db->dt[cancel_cnt])."</td>
										<td align=center>";
											if($io_info[pi_ix]){
												if($db->dt[ps_ix]){
													$Contents .= $db->dt[section_name];
												}else{
													$Contents .= "<b class='red'>지정안됨</b>";
												}
											}else{
												$Contents .= "외부직배송";
											}
										$Contents .= "
										</td>
										<td align=center></td>
									</tr>";
								}
							}
						
						$Contents .= "
						</table>";
					}else{
						$Contents .= "
						<table border=0 cellpadding=0 cellspacing=1 width='100%' class='list_table_box' >
							<tr height=30px bgcolor=#e5e5e5>
								<td class='m_td' align=center width='3%'>순번</td>
								<td class='m_td' align=center width='10%'>품목</td>
								<td class='m_td' align=center width='*'>품명</td>
								<td class='m_td' align=center width='10%'>규격</td>
								<td class='m_td' align=center width='8%'>단위</td>
								<td class='m_td' align=center width='7%'>수량</td>";
								if($input_view){
									$Contents .= "
									<td class='m_td' align=center width='7%'>입고수량</td>";
								}
								$Contents .= "
								<!--<td class='m_td' align=center width='13%'>객단가</td>-->
								<td class='m_td' align=center width='13%'>공급가</td>
								<td class='m_td' align=center width='13%'>세액</td>
								<td class='e_td' align=center width='13%'>금액</td>
							</tr>";

							$db->query("SELECT * FROM inventory_order_detail where ioid = '".$ioid."'");

							for($i=0;$i < $db->total ;$i++){
								$db->fetch($i);

                                /*
                                $db->query("select * from inventory_goods_unit where gid = '".$db->dt[gid]."' order by gu_ix ASC");
                                $igu_info = $db->fetchall("object");
                                */

								$Contents .= "
								<tr bgcolor='#ffffff' height=27>
									<td align=center>".($i+1)."</td>
									<td align=left style='padding-left:3px;'>".$db->dt[gid]."</td>
									<td style='padding-left:3px;text-align:center;'>".$db->dt[gname]."</td>
									<td align=center>".$db->dt[standard]."</td>
									<td align=center>".getUnit($db->dt[unit], "","","text")."</td>
									<td align=center>".number_format($db->dt[cnt])."</td>";
									if($input_view){
										$Contents .= "
										<td align=center>".number_format($db->dt[real_cnt])."</td>";
									}
									$Contents .= "
									<!--<td align=center>".number_format($igu_info[0][buying_price])."</td>-->
									<td align=center>".number_format($db->dt[coprice])."</td>
									<td align=center>".number_format($db->dt[tax_price])."</td>
									<td align=center>".number_format($db->dt[buying_price])."</td>
								</tr>";

								$sum_cnt+=$db->dt[cnt];
								$sum_real_cnt+=$db->dt[real_cnt];
								//$sum_buying_price_unit+=$igu_info[0][buying_price];
								$sum_coprice+=$db->dt[coprice];
								$sum_tax_price+=$db->dt[tax_price];
								$sum_buying_price+=$db->dt[buying_price];
							}

							$Contents .= "
							<tr bgcolor='#ffffff'>
								<td height=30 align=center colspan=5><b>합계</b></td>
								<td align=center>".number_format($sum_cnt)."</td>";
								if($input_view){
									$Contents .= "
									<td align=center>".number_format($sum_real_cnt)."</td>";
								}
								$Contents .= "
								<!--<td align=center>".number_format($sum_buying_price_unit)."</td>-->
								<td align=center>".number_format($sum_coprice)."</td>
								<td align=center>".number_format($sum_tax_price)."</td>
								<td align=center>".number_format($sum_buying_price)."</td>
							</tr>
						</table>";
					}
				$Contents .= "
				</td>
			</tr>";

			if($view_type=="update"){
				$Contents .= "
				<tr>
					<td align=center style='padding:20px 0px;'>
						<input type='button' value='청구확정취소' onclick=\"change_act('ACC');\"/>
						<input type='button' value='발주예정' onclick=\"change_act('OR');\"/>
					</td>
				</tr>";
			}elseif($view_type=="stocked"){
				$Contents .= "
				<tr>
					<td align=center style='padding:20px 0px;'>
						<img type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"Submit_bool=true;$(this).closest('form').submit();\" >
					</td>
				</tr>";
			}
		$Contents .= "
		</table>
		</td>
	</tr>

</TABLE>
</form>";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "재고관리 > 발주확인하기";
$P->NaviTitle = "발주확인하기";
$P->title = "발주확인하기";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>
