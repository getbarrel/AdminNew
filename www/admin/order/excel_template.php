<?
include("../class/layout.class");
include("../order/excel_columsinfo.php");


$Script = "
<script language='javascript'>
	
	$(document).ready(function(){

		$('#colums_info').dblclick(function(){
			MoveSelectBox('ADD');
		});

		$('#default_value').keyup(function(e){
			if(e.keyCode==13){
				DefaultAreaApply($('#default_apply').attr('index'));
			}
		})

		$('#oet_ix').change(function(){
			GetTemplateData($(this));
		});

		$('#select_colums_info').dblclick(function(){
			InputOptionValue($(this));
		});
	});

	function GetTemplateData(obj){
		var code_text;
		$.ajax({ 
			type: 'POST', 
			data: {'act': 'get_template_ajax', 'oet_ix':obj.val()},
			url: './excel_template.act.php',  
			dataType: 'json', 
			async: false, 
			beforeSend: function(){ 

			},  
			success: function(infos){ 
				
				$('#select_colums_info option').remove();
				
				$('#oet_name').val(infos.oet_name);
				$('input[name=oet_type][value='+infos.oet_type+']').attr('checked',true);
				if(infos.charger_check=='Y'){
					$('#charger_check').attr('checked',true);
				}else{
					$('#charger_check').attr('checked',false);
				}

				$('#oet_line').val(infos.oet_line);

				$.each(infos.select_colums_info,function(i,info){
					code_text = info.split('|');
					$('#select_colums_info').append('<option value=\"'+info+'\" >'+code_text[1]+'</option>');
				});

				ExcelLineChange();
			}
			
		});
	}

	var line = new Array(
		'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
		'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
		'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ'
	);

	function ExcelLineChange(){
		$('#select_colums_info option').each(function(i){
			var optoin_text = $(this).text();
			if(optoin_text.indexOf('[') >= 0){
				optoin_text = optoin_text.replace(/\[[\w\W]+\]/g,'');
			}
			$(this).text('['+line[i]+' 열] '+optoin_text);
		});
	}

	function MoveSelectBox(type){

		DefaultAreaHiden();

		if(type == 'ADD'){
			$('#colums_info option:selected').each(function(){
				$('#select_colums_info').append('<option value=\"'+$(this).val()+'\" >'+$(this).text()+'</option>');
			});
		}else{
			$('#select_colums_info option:selected').each(function(){
				$(this).remove();
			});
		}

		ExcelLineChange();
	}

	function MoveOption(type){

		DefaultAreaHiden();

		if(type == 'UP'){
			$('#select_colums_info option:selected').each(function(){
				if($(this).prev().length > 0){
					$(this).prev().insertAfter($(this));
				}else{
					ExcelLineChange();
					return false;
				}
			});
		}else{
			var obj;
			var next_obj;
			var total = $('#select_colums_info option:selected').length - 1;

			for(i=total;i>=0;i--){
				obj = $('#select_colums_info option:selected').eq(i);
				next_obj = obj.next();

				if(next_obj.length > 0){
					next_obj.insertBefore(obj);
				}else{
					ExcelLineChange();
					return false;
				}
			}
		}

		ExcelLineChange();
	}

	function AddOption(){

		DefaultAreaHiden();
		
		$('<option value=\"DEFAULT|\"></option>').appendTo('#select_colums_info');

		ExcelLineChange();
	}

	function InputOptionValue(select_obj){
		
		obj = select_obj.find(\"option[value^='DEFAULT|']:selected\");
	
		var data_array;
		var line_text;
		var index;

		if(obj.length){
			data_array = obj.val().split('|');
			line_text = obj.text().split(']');
			index = $('#select_colums_info option').index(obj);
			
			$('#default_apply').attr('index',index);
			$('#default_line').text(line_text[0]+']');
			$('#default_value').val(data_array[1]);
			$('#default_area').show();
			$('#default_value').focus();
		}
	}
	
	function DefaultAreaApply(index){
		var obj = $('#select_colums_info option').eq(index);
		var val = $('#default_value').val();
		obj.val('DEFAULT|'+val);
		obj.text(val);
		DefaultAreaHiden();
		ExcelLineChange();
	}
	
	function DefaultAreaHiden(){
		$('#default_area').hide();
	}

	function TemplateSumit(frm){
		
		if($('#oet_name').val().length==0){
			alert('양식명을 입력해주세요.');
			$('#oet_name').focus();
			return false;
		}
		

		if($('#select_colums_info option').length==0){
			alert('선택한 목록이 하나도 없습니다.');
			return false;
		}
		
		
		$('#select_colums_info option').attr('selected',true)
		frm.submit();
	}

	function DeleteTemplate(){
		if($('#oet_ix').val().length){
			if(confirm('정말로 `'+ $('#oet_ix option:selected').text() + '` 양식을 삭제하시겠습니까?')){
				window.frames['act'].location.href='./excel_template.act.php?act=template_delete&oet_ix='+$('#oet_ix').val();
			}
		}else{
			alert('삭제할 양식을 선택해주세요.');
		}
	}

</script>";


/*
		//입력용&전체 일때 필수값 체크
		if($('input[name=oet_type]:checked').val()=='I' || $('input[name=oet_type]:checked').val()=='A'){ ";
		
			foreach($essential_colums as $ec){
				$Jselect="";
				$Jalert="";
				$cnt=0;
				foreach($ec as $val){
					$Jselect.=",option[value^=".$val."]";
					$Jalert.="";
					$cnt++;
				}

				$Script .= "
				if($('#select_colums_info').find('".substr($Jselect,1)."').length ==0){
					var Jalert='';

					$('#colums_info').find('".substr($Jselect,1)."').each(function(i){
						if(i==0)			Jalert += $(this).text();
						else				Jalert += ','+$(this).text();
					}); ";
					
					if($cnt > 1){
						$Script .= "
						Jalert += ' 중 하나는 선택해야 합니다.' ";
					}else{
						$Script .= "
						Jalert += '은(는) 필수입니다.' ";
					}
					
					$Script .= "
					alert(Jalert);
					return false;
				} ";
			}

		$Script .= "
		}
*/
$Contents ="
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
<tr>
	<td colspan=8>
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
		<tr>
			<td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b>엑셀 양식 관리</b></div>")."</td>
		 </tr>
		</table>
	</td>
</tr>
</table>
<form name='template_frm' method='POST' action='../order/excel_template.act.php' target='act' >
<input type='hidden' name='act' value='template_update'>
	<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr>
		<td class='search_box_title'>
			양식목록
		</td> 
		<td class='search_box_item'>
			".orderExcelTemplateSelect()."
			<img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' align='absmiddle' onclick=\"DeleteTemplate();\" style='cursor:pointer;'/>
		</td>
	</tr>
	<tr>
		<td class='search_box_title'>
			양식명
		</td> 
		<td class='search_box_item'>
			<input type='text' class='textbox' name='oet_name' value='' id='oet_name' style='width:200px;'/>
			<input type='radio' name='oet_type' value='O' id='oet_type_o' checked/><label for='oet_type_o'>출력용</label>
			<input type='radio' name='oet_type' value='I' id='oet_type_i' /><label for='oet_type_i'>입력용</label> 
			<input type='radio' name='oet_type' value='A' id='oet_type_a'  /><label for='oet_type_a'>전체</label> 
			<input type='checkbox' name='charger_check' id='charger_check' value='Y' /><label for='charger_check'>나만사용하기</label>
			<b>데이터 시작 <input type='text' name='oet_line' id='oet_line' class='textbox numeric' value='' size='3'/> 행 부터!</b>
		</td>
	</tr>
	</table>
	<table border='0' cellspacing='0' cellpadding='0' style='margin:50px auto;'>
	<col width='300px' />
	<col width='100px' />
	<col width='300px' />
	<col width='100px' />
	<col width='300px' />
	<tr>
		<td>
			<table border='0' align='left' class='search_table_box'>
			<tr>
				<td align='center' class='search_box_title'>
					선택 가능한 목록
				</td>
			</tr>
			<tr>
				<td align='center'>
					<select style='border:solid 1px #ddd;width:300px;height:400px;font-size:12px;background:#fff;' id='colums_info'  multiple>";
						foreach($colums as $c){
							$Contents .="<option value='".$c[value]."|".$c[title]."'>".$c[title]."</option>";
						}
					$Contents .="
					</select>
				</td>
			</tr>
			</table>
		</td>
		<td align='center'>
			<div class='float01 email_btns01'>
				<ul>
					<li>
						<a href=\"javascript:MoveSelectBox('ADD');\"><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
					</li>
					<li>
						<a href=\"javascript:MoveSelectBox('REMOVE');\"><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
					</li>
					<li>
						<a href=\"javascript:\"><input type='button' value='공란\n추가' onclick=\"AddOption()\" ></a>
					</li>
				</ul>
			</div>
		</td>
		<td>
			<table border='0' align='left' class='search_table_box'>
			<tr>
				<td align='center' class='search_box_title'>
					선택한 목록
				</td>
			</tr>
			<tr>
				<td align='center'>
					<div style='position:relative;'>
						<select name='select_colums_info[]' style='border:solid 1px #ddd;width:300px;height:400px;font-size:12px;background:#fff;' id='select_colums_info' multiple>
					
						</select>
						<table border=0 id='default_area' style='position:absolute;width:230px;top:-20px;left:302px;display:none;' cellpadding=0 cellspacing=0>
						<col width=20>
						<col width=*>
						<col width=13>
						<col width=*>
						<col width=14>
						<tr style='height:20px;'>
							<th style='background:url(/admin/images/common/tooltip01/bg-tooltip_01.png) no-repeat right bottom;'></th>
							<td style='background:url(/admin/images/common/tooltip01/bg-tooltip_02.png) repeat-x bottom;' colspan=3></td>
							<th style='background:url(/admin/images/common/tooltip01/bg-tooltip_03.png) no-repeat left bottom;'></th>
						</tr>
						<tr>
							<th style='background:url(/admin/images/common/tooltip01/bg-tooltip_04.png) repeat-y right;'>
				
							</th>
							<td style='height:30px;line-height:130%;padding:5px;background-color: #ffffff;' colspan=3 >
								<b id='default_line'></b> <input type='text' class='textbox' id='default_value' style='width:70px;' value=''/> 
								<img src='../images/".$_SESSION["admininfo"]["language"]."/bts_ok.gif' style='cursor:pointer;' align='absmiddle' id='default_apply' index='' onclick=\"DefaultAreaApply($(this).attr('index'));\" />
							</td>
							<th style='background:url(/admin/images/common/tooltip01/bg-tooltip_06.png) repeat-y left;'></th>
						</tr>
						<tr style='height:20px;'>
							<th style='background:url(/admin/images/common/tooltip01/bg-tooltip_04_la.png) no-repeat left;text-align:right;' colspan='4'>
								<span class='small'>기본값을 입력해주세요.</span> <img src='/admin/images/".$_SESSION["admininfo"]["language"]."/btn_close.gif' border='0' align='absmiddle' height='20px;' style='cursor:pointer;' onclick=\"DefaultAreaHiden();\">
							</th>
							<th style='background:url(/admin/images/common/tooltip01/bg-tooltip_06.png) repeat-y right;'></th>
						</tr>
						<tr style='height:20px;'>
							<th style='background:url(/admin/images/common/tooltip01/bg-tooltip_07.png) no-repeat right bottom;'></th>
							<td style='background:url(/admin/images/common/tooltip01/bg-tooltip_08.png) repeat-x bottom;' colspan='3'></td>
							<th style='background:url(/admin/images/common/tooltip01/bg-tooltip_09.png) no-repeat right top;'></th>
						</tr>
						</table>
					</div>
				</td>
			</tr>
			</table>
		</td>
		<td align='center'>
			<div class='float01 email_btns01'>
				<ul>
					<li>
						<input type='button' value='▲\n위로' style='width:50px;height:40px;text-align:center;' onclick=\"MoveOption('UP')\" />
					</li>
					<li>
						<br/>
						순서
					</li>
					<li>
						<br/>
						<input type='button' value='▼\n아래로' style='width:50px;height:40px;text-align:center;' onclick=\"MoveOption('DOWN')\" />
					</li>
				</ul>
			</div>
		</td>
		<td valign='top'>";
			
		$help_text = "
		<table cellpadding=1 cellspacing=0 class='small' height='380px;'>
			<col width=8>
			<col width=*>
			<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small'>`--새고운 양식 생성--` 을 선택후 저장을 하시면 <br/>새로운 양식이 생성됩니다.</td></tr>
			<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small'>필요하신 항목은 선택 가능한 목록에서 한개 이상을 선택하신후 <img src='../images/icon/pop_plus_btn.gif' alt='추가' align='absmiddle' title='추가' /> 버튼을 클릭 또는 <b>더블클릭</b>으로 추가가 가능합니다.</td></tr>
			<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small'>선택한 목록에서 삭제하고 싶은목록을 한개 이상 선택하신후 <img src='../images/icon/pop_del_btn.gif' align='absmiddle' alt='삭제' title='삭제' /> 버튼을 틀릭하시면 삭제가 됩니다.</td></tr>
			<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small'>순서를 변경하고 싶으시면 목록을 한개이상 선택하신후 <b>위로</b> 또는 <b>아래로</b> 버튼을 클릭하시면 됩니다.</td></tr>
			<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small'>엑셀 입력용 사용시 필요없는 열을 추가하고 싶으실때 <b>공란추가</b> 버튼을 클릭하시면 됩니다.</td></tr>
			<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small'><b>기본값</b> 을 지정은 <b>공란추가</b> 버튼을 클릭후 생성된 목록에서 을 <b>더블클릭</b> 하시면 기본값을 설정할수 있는 창에 원하시는 값을 입력하신후 <img src='../images/".$_SESSION["admininfo"]["language"]."/bts_ok.gif' align='absmiddle' /> 버튼을 클릭해주세요.</td></tr>
			<tr><td valign=middle height='100px'></td></tr>
		</table>";

			$Contents .="
			".HelpBox("주문엑셀양식관리", $help_text, 200)."
		</td>
	</tr>
	</table>
	<table width='100%' border='0' cellspacing='0' cellpadding='0' style='margin-top:15px'>
	<tr>
		<td align='center'>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
					$Contents .= "
					<img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' onclick=\"TemplateSumit($(this).closest('form'))\" >";
				}else{
					$Contents .= "
					<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
				}
		$Contents .= "
		</td>
	</tr>
	</table>
</form>
";


$P = new LayOut();
$P->addScript = $Script;
$P->OnloadFunction = "";
if($view_type == 'inventory'){
$P->strLeftMenu = inventory_menu();
$P->Navigation = "WMS관리 > 주문엑셀양식관리";
}else{
$P->strLeftMenu = order_menu();
$P->Navigation = "주문관리 > 주문엑셀양식관리";
}
$P->title = "주문엑셀양식관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>