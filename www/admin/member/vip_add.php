<?
include("../class/layout.class");
//auth(9);

$db = new Database;
$mdb = new Database;

$Script = "

<script language='javascript'>

function view_go()
{
	var sort = document.view.sort[document.view.sort.selectedIndex].value;

	location.href = 'member.php?view='+sort;
}

function ChangeLecture(pid, status){
    //alert(pid);
	$.ajax({ 
			type: 'GET', 
			data: {'act': 'get_participatnt', 'pid':pid, 'status':status},
			url: 'sms_pop.act.php',  
			dataType: 'json', 
			async: true, 
			beforeSend: function(){ 
				
			},  
			success: function(datas){ 
				if(datas != null){
				    $('#participation option').each(function(){
					   $(this).remove();
					});
					$.each(datas, function(i,data){ 
						
						if($('#participation option:eq('+i+')').html() == null){
							$('#participation').append('<option value='+data.name+'|'+data.mobile+'>'+data.name+' ('+data.mobile+')</option>');
						}else{
							$('#participation option:eq('+i+')').val(data.mobile);
							$('#participation option:eq('+i+')').html(data.name+' '+data.mobile);
						}
					});
				}else{
					$('#participation option').each(function(){
						$(this).remove();
					});
				}

				$('#participation_total').html($('#participation option').size());
			
			} 
		}); 
}

function MoveSelectBox(type,level_ix){
	if(type == 'ADD'){
		if(level_ix == '4'){
			$('#participation option:selected').each(function(){
				$('#selected').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('#participation option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
			//$('#participation option:eq('+$('#participation option:selected').val()+')').html()
		}else if(level_ix == '5'){
			$('#participation_1 option:selected').each(function(){
				$('#selected_1').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('#participation_1 option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
			//$('#participation option:eq('+$('#participation option:selected').val()+')').html()
		}else if(level_ix == '6'){
			$('#participation_2 option:selected').each(function(){
				$('#selected_2').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('#participation_2 option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
			//$('#participation option:eq('+$('#participation option:selected').val()+')').html()
		}
	}else{
		if(level_ix == '4'){
				$('#selected option:selected').each(function(){
					$('#participation').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
					var selected_value = $(this).val();
					$('#selected option').each(function(){
						if($(this).val() == selected_value){
							$(this).remove();
						}
					});
				});
			}else if(level_ix == '5'){
				$('#selected_1 option:selected').each(function(){
					$('#participation_1').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
					var selected_value = $(this).val();
					$('#selected_1 option').each(function(){
						if($(this).val() == selected_value){
							$(this).remove();
						}
					});
				});
			}else if(level_ix == '6'){
				$('#selected_2 option:selected').each(function(){
					$('#participation_2').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
					var selected_value = $(this).val();
					$('#selected_2 option').each(function(){
						if($(this).val() == selected_value){
							$(this).remove();
						}
					});
				});
			}
	}

	//$('#participation_total').html($('#participation option').size());
	//$('#selected_total').html($('#selected option').size());

}



function SelectedAll(jquery_obj, selected){
	$(jquery_obj).each(function(){
		$(this).attr('selected', selected);
	});
}
function UnSelected(jquery_obj){
    $('#selected option:selected').each(function(){
	    $('#participation').append('<option value='+$(this).val()+'>'+$(this).html()+'</option>');
		var selected_value = $(this).val();
		$('#selected option').each(function(){
			if($(this).val() == selected_value){
				$(this).remove();
			}
		});
	});
}
function CheckEmailForm(frm){
//alert($('#selected option:selected').size());
	if($('#selected option:selected').size() < 1){
		if($('#selected option').size() < 1){
			alert('SMS 발송대상을 한명이상 지정 해야 합니다.');
			return false;
		}else{
			alert('SMS 발송대상을 한명이상 선택 해야 합니다.');
			return false;
		}
	}
	if(!CheckFormValue(frm)){
		return false;
	}
}
function getByteLength(str) {
 //alert('str = ' + str + ',str.length = ' + str.length);
 var len = 0;
    for (var i = 0; i < str.length; i++) {
        var oneChar = escape(str.charAt(i));
        if ( oneChar.length == 1 ) {
            len ++;
        } else if (oneChar.indexOf('%u') != -1) {
            len += 2;
        } else if (oneChar.indexOf('%') != -1) {
            len+= oneChar.length/3;
        }
    }
 return len;
}
function check_byte(obj){
    var len = getByteLength(obj.value);
    //alert(len);
    if(len > 80){
        if(event.keyCode != '8'){
            alert('글자입력 제한을 초과 하였습니다.');
        }
        obj.value = obj.value.substring(0,obj.value.length-1);
        len = getByteLength(obj.value);
    }
    $('#count').html(len + '/80 byte');
    
}
$(document).ready(function() {
	$('#participation_total').html($('#participation option').size());

});

</script>";

$Contents = "
<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>
<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("VIP회원관리", "회원관리 > VIP회원관리 ")."</td>
  </tr>
  <tr>
  	<td>
		<table width='100%' cellpadding=0 cellspacing=0 border='0' >
		<col width='15%' />
		<col width='35%' />
		<col width='15%' />
		<col width='35%' />
		  <tr>
			<td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "본사관리 > $menu_name")."</td>
		  </tr>
		  <tr>
			<td align='left' colspan=4 style='padding-bottom:20px;'>
			<div class='tab'>
				<table class='s_org_tab'>
				<col width='550px'>
				<col width='*'>
				<tr>
					<td class='tab'>
						<table id='tab_01' ".(($info_type == "list" || $info_type == "") ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02'  ><a href='member_vip_list.php?info_type=list'>VIP회원리스트</a> <span style='padding-left:2px' class='helpcloud' help_width='410' help_height='70' help_html='사업자 정보를 작성하여 등록한 후에 해당 사업자에 관리자화면 로그인 정보를 발급해 줄 수 있습니다.<br />사업자 정보 추가 후 [목록관리] - [사용자 추가]를 통해 관리자에 계정을 발급해 주시기 바랍니다.'><img src='/admin/images/icon_q.gif' /></span></td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_02' ".($info_type == "add" ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' >";

								$Contents .= "<a href='vip_add.php?info_type=add'>VIP수동회원관리</a>";

							$Contents .= "
							</td>
							<th class='box_03'></th>
						</tr>
						</table>
					</td>
					<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

					</td>
				</tr>
			</table>
			</div>
			</td>
		  </tr>
		</table>
	</td>
	</tr>
</table>";

$Contents .="
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
<tr>
	<td colspan=8>
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
		<tr>
			<td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b>VIP회원 수동등록관리</b> ".($company_id != "" ? "[ ".$db->dt[com_name]." : ".$company_id." ]" : "")." </div>")."</td>
		  </tr>
		</table>
	</td>
</tr>
</table>


<form name='vip_frm' method='POST' action='vip.act.php' target='act'>
<input type='hidden' name='act' value='vip_update'>
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
<tr>
	<td class='search_box_title' rowspan='3'>
		VIP 회원관리
	</td> 
	<td class='search_box_item'>
		<table width='60%' border='0' align='left'>
		<tr>
			<td colspan='4'>
				<span> <b> 1) VIP 회원관리</b> </span>
			</td>
		</tr>
		<tr>
			<td width='300'>
				<table width='100%' border='0' align='center'>
				<tr align='right'>
					<td width='230'>
						<img src='../v3/images/korea/btn_member_search.gif' align='absmiddle' onclick=ShowModalWindow('./charger_search.php?company_id=3444fde7c7d641abc19d5a26f35a12cc&target=4&amp;code=',700,530,'charger_search') style='cursor:pointer;'>
					</td>
					<td align='center'>
						<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택' onclick=\"SelectedAll($('#participation option'),'selected')\" style='cursor:pointer;'/>
					</td>
				</tr>
				<tr>
					<td colspan='2' >
						<select name='vip_delete[]' style='border:solid 1px #ddd;width:300px;height:148px;font-size:12px;background:#fff;' class='participation' id='participation'  multiple>
					
						</select>
					</td>
				</tr>
				</table>
			</td>
			<td align='center'>
				<div class='float01 email_btns01'>
					<ul>
						<li>
							<a href=\"javascript:MoveSelectBox('ADD','4');\"><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
						</li>
						<li>
							<a href=\"javascript:MoveSelectBox('REMOVE','4');\"><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
						</li>
					</ul>
				</div>
			</td>
			<td>
			<td width='300' style='vertical-align:bottom;'>
				<table width='100%' border='0' align='center'>
				<tr align='left'>
					<td width='230'>
						<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>VIP 회원</b>
					</td>
				</tr>
				<tr>
					<td colspan='2' >
						<select name='vip_list[]' style='border:solid 1px #ddd;width:300px;height:148px;font-size:12px;background:#fff;padding:5px;' id='selected' validation=true title='vip회원 대상' multiple>
						";
						$vip_array = get_vip_member('4');
						for($i = 0; $i<count($vip_array); $i++){
							$Contents .="<option value='".$vip_array[$i][code]."'>".$vip_array[$i][name]."(".$vip_array[$i][id].")</option>";
						}
						$Contents .="
						</select>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
</tr>


<tr>
	<td class='search_box_item'>
		<table width='60%' border='0' align='left'>
		<tr>
			<td colspan='3'>
				<span> <b>2) VVIP 회원관리</b> </span>
			</td>
		</tr>
		<tr>
			<td width='300'>
				<table width='100%' border='0' align='center'>
				<tr align='right'>
					<td width='230'>
						<img src='../v3/images/korea/btn_member_search.gif' align='absmiddle' onclick=ShowModalWindow('./charger_search.php?company_id=3444fde7c7d641abc19d5a26f35a12cc&target=5&amp;code=',700,530,'charger_search') style='cursor:pointer;'>
					</td>
					<td align='center'>
						<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택' onclick=\"SelectedAll($('#participation_1 option'),'selected')\" style='cursor:pointer;'/>
					</td>
					</tr>
				<tr>
					<td colspan='2'>
						<select name='vvip_delete[]' style='border:solid 1px #ddd;width:300px;height:148px;font-size:12px;background:#fff;' id='participation_1' class='participation_1' multiple>
						</select>
					</td>
				</tr>
				</table>
			</td>
			<td align='center'>
				<div class='float01 email_btns01'>
					<ul>
						<li>
							<a href=\"javascript:MoveSelectBox('ADD','5');\"><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
						</li>
						<li>
							<a href=\"javascript:MoveSelectBox('REMOVE','5');\"><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
						</li>
					</ul>
				</div>
			</td>
			<td>
			<td width='300' style='vertical-align:bottom;'>
				<table width='100%' border='0' align='center'>
				<tr align='left'>
					<td width='230'>
						<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>VVIP 회원</b>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<select name='vvip_list[]' style='border:solid 1px #ddd;width:300px;height:148px;font-size:12px;background:#fff;padding:5px;' id='selected_1' validation=true title='메일수신 대상' multiple>
							";
						$vip_array = get_vip_member('5');
						for($i = 0; $i<count($vip_array); $i++){
							$Contents .="<option value='".$vip_array[$i][code]."'>".$vip_array[$i][name]."(".$vip_array[$i][id].")</option>";
						}
						$Contents .="
						</select>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class='search_box_item'>
		<table width='60%' border='0' align='left'>
		<tr>
			<td colspan='3'>
				<span> <b>3) VVVIP 회원관리 </b></span>
			</td>
		</tr>
		<tr>
			<td width='300'>
				<table width='100%' border='0' align='center'>
				<tr align='right'>
					<td width='230'>
						<img src='../v3/images/korea/btn_member_search.gif' align='absmiddle' onclick=ShowModalWindow('./charger_search.php?company_id=3444fde7c7d641abc19d5a26f35a12cc&target=6&amp;code=',700,530,'charger_search') style='cursor:pointer;'>
					</td>
					<td align='center'>
						<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택' onclick=\"SelectedAll($('#participation_2 option'),'selected')\" style='cursor:pointer;'/>
					</td>
					</tr>
				<tr>
					<td colspan='2'>
						<select name='vvvip_delete[]' style='border:solid 1px #ddd;width:300px;height:148px;font-size:12px;background:#fff;' id='participation_2' class='participation_2' multiple>
						</select>
					</td>
				</tr>
				</table>
			</td>
			<td align='center'>
				<div class='float01 email_btns01'>
					<ul>
						<li>
							<a href=\"javascript:MoveSelectBox('ADD','6');\"><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
						</li>
						<li>
							<a href=\"javascript:MoveSelectBox('REMOVE','6');\"><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
						</li>
					</ul>
				</div>
			</td>
			<td>
			<td width='300' style='vertical-align:bottom;'>
				<table width='100%' border='0' align='center'>
				<tr align='left'>
					<td width='230'>
						<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>VVVIP 회원</b>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<select name='vvvip_list[]' style='border:solid 1px #ddd;width:300px;height:148px;font-size:12px;background:#fff;padding:5px;' id='selected_2' validation=true title='메일수신 대상' multiple>
						";
						$vip_array = get_vip_member('6');
						for($i = 0; $i<count($vip_array); $i++){
							$Contents .="<option value='".$vip_array[$i][code]."'>".$vip_array[$i][name]."(".$vip_array[$i][id].")</option>";
						}
						$Contents .="
						</select>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<br><br>
";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
$Contents .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ><!--img src='../images/".$admininfo["language"]."/b_save.gif' border='0'  style='cursor:pointer;border:0px;' onClick='CheckFormValue(document.group_frm)' /--> </td></tr>
</table>
";
}else{
$Contents .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff >
    <td colspan=4 align=center>
        <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>
    </td>
</tr>
</table>
";
}
$Contents .= "
</form>
";



$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "";
$P->strLeftMenu = member_menu();
$P->Navigation = "회원관리 > VIP수동회원관리";
$P->title = "VIP수동회원관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>



