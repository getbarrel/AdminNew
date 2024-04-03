<?
include("../class/layout.class");
//include($_SERVER["DOCUMENT_ROOT"]."/class/bbs.lib.php");
//include_once($_SERVER["DOCUMENT_ROOT"]."/include/sns.config.php");

$db = new Database;

if($option_type == ""){
	$option_type = "basic";
}
if($_GET["mi_ix"] != ""){
	$option_input_act = "update";
}else{
	$option_input_act = "insert";
}

if($type == 'add'){
	$mstring = "<form name='mandatory_info' method='POST' action='product_mandatory.act.php' onsubmit='return SubmitX(this)' target='act'>";
}else{
	$mstring = "<form name='mandatory_info' method='POST' action='product_mandatory.act.php' onsubmit='return CheckFormValue(this)' target='act'>";
}

$mstring .= "
		<input type=hidden name='act' value='".$option_input_act."'>
		<input type=hidden name='mi_ix' value='".$mi_ix."'>
		<input type='hidden' name='mi_code_check' id='mi_code_check' value='' validation=true title='코드중복확인'>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=8 style='padding-bottom:14px;'>
			<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_00'  ".($option_type == "basic" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >상품고시정보</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td align='right' style='text-align:right;vertical-align:bottom;padding:0 0 6px 4px;'>";

	$mstring .= "
						</td>
					</tr>
					</table>
				</div>
			</td>
		</tr>";

$mstring .="
		<tr>
			<td height=560 valign=top>
		";

$mstring .="
			<table width='100%' cellpadding=0 cellspacing=0>
			<tr height=30>
				<td style='padding-bottom:5px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'> <b class=blk> 상품고시 정보 추가/수정  </b></div>")."
				</td>
			</tr>
			</table>";

$sql = "select * from shop_mandatory_info where mi_ix = '".$mi_ix."'";
$db->query($sql);
$db->fetch();


$mstring .= "
			<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
			<col width='20%' />
			<col width='30%' />
			<col width='20%' />
			<col width='30%' />
			<tr>
                <td class='input_box_title' > 프론트 전시 구분</td>
                <td class='input_box_item' colspan=3>".GetDisplayDivision($db->dt['mall_ix'], "select")." </td>
            </tr>
            <tr>
				<td class='input_box_title'>상품고시명 <img src='".$required3_path."'></td>
				<td class='input_box_item'>
					<input type='text' name='mandatory_name' id='mandatory_name' value='".$db->dt[mandatory_name]."' class='textbox' validation=true title='상품고시명'>
				</td>
				<td class='input_box_title'>코드 <img src='".$required3_path."'></td>
				<td class='input_box_item'>
					<input type='text' name='mi_code' id='mi_code' value='".$db->dt[mi_code]."' class='textbox' style='width:70px;' validation=true title='코드' ".($type == 'update'?'readonly':'')." onkeyup=\"check_mi_code();\">
					<span class='small blu' id='check_mi_code_text'> * 코드 중복확인</span>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'>사용유무 <img src='".$required3_path."'></td>
				<td class='input_box_item' colspan='3'>
					<input type='radio' name='is_use' id='is_use_0' value='0' title='미사용' ".($db->dt[is_use] == "0" || $db->dt[is_use] == ""?'checked':'')."> <label for='is_use_0'>미사용</label>
					<input type='radio' name='is_use' id='is_use_1' value='1' title='사용' ".($db->dt[is_use] == "1"?'checked':'')."> <label for='is_use_1'>사용</label>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'>
					상품고시 정보
					<img src='../images/icon/pop_plus_btn.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"AddMultTable('mandatory_detail');\" />
				</td>
				<td class='input_box_item' colspan='3'>
					<table width='96%' cellpadding=0 cellspacing=0 bgcolor=silver border='0' class='list_table_box' id='mandatory_detail' style='margin:10px;' >
					<col width='5%'/>
					<col width='8%'/>
					<col width='10%'/>
					<col width='*'/>
					<col width='30%'/>
					<col width='5%'/>
					<tr height=25 bgcolor='#ffffff' align=center>
						<td bgcolor=\"#efefef\" class=small> 순서</td>
						<td bgcolor=\"#efefef\" class=small> 상세코드</td>
						<td bgcolor=\"#efefef\" class=small> 상품정보 고시명 </td>
						<td bgcolor=\"#efefef\" class=small> 내용  </td>
						<td bgcolor=\"#efefef\" class=small> 설명  </td>";
		if($admininfo[admin_level] == '9'){
		$mstring .="
						<td bgcolor=\"#efefef\" class=small> 관리  </td>";
		}
		$mstring .="
					</tr>";

		$sql = "select * from shop_mandatory_detail where mi_ix = '".$mi_ix."' order by seq asc ";
		$db->query($sql);
		$mandatory_detail_array = $db->fetchall();

		for($i =0;$i<count($mandatory_detail_array) || $i<=0;$i++){
			$no = $i + 1;
		$mstring .="
					<tr height=30  bgcolor='#ffffff' align=center depth=1 id='mandatory_detail_tr'>
						<td class='list_box_td'><span id='mandatory_seq'>".($mandatory_detail_array[$i][seq]?$mandatory_detail_array[$i][seq]:$no)."</span>
						</td>
						<td class='list_box_td'>
							<input type=text class='textbox' name='mandatory[details][".$i."][detail_code]' id='mandatory_code' validation=true style='width:80%' value='".$mandatory_detail_array[$i][detail_code]."' title='상세코드'>
						</td>
						<td class='list_box_td'>
							<input type='hidden' name='option_length' id ='option_length' value='".$no."'>
							<input type='hidden' name='mandatory[details][".$i."][mid_ix]' value='".$mandatory_detail_array[$i][mid_ix]."'>
							<input type=text class='textbox' name='mandatory[details][".$i."][mid_title]' id='mandatory_title' validation=true value='".$mandatory_detail_array[$i][mid_title]."' title='상품고시명'>
						</td>
						<td class='list_box_td'>
							<input type=text class='textbox' name='mandatory[details][".$i."][mid_desc]' id='mandatory_desc' validation=false style='width:90%' value='".$mandatory_detail_array[$i][mid_desc]."' title='상품고시내용'>
						</td>
						<td class='list_box_td'>
							<input type=text class='textbox' name='mandatory[details][".$i."][mid_comment]' id='mandatory_comment' validation=false style='width:90%' value='".$mandatory_detail_array[$i][mid_comment]."' title='상품고시 설명'>
						</td>";
		if($admininfo[admin_level] == '9'){
		$mstring .="
						<td class='list_box_td'>
							<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' id='del_mandatory_table'  onclick=\"DelMultTable('mandatory_detail');\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
						</td>";
		}
		$mstring .="
					</tr>";
		}
		$mstring .="
					</table>
				</td>
			</tr>
			</table><br><br>";


$mstring1 .="<div style='line-height:130%;padding:10px 0px 20px 0px'>
				재고관리가 필요 없는 상품일 경우 옵션 추가를 이용하여 아래 예와 같이 옵션을 분리 적용 하실 수 있습니다.<br>
				예) 옵션1 – 옵션명 : 색상 / 옵션구분 : RED, BLUE<br>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;옵션2 – 옵션명 : 사이즈 / 옵션구분 : 95size, 100size, 105size<br>
			</div>
		</div>";

$mstring .= "
			<div align=center>
				<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer'>
			</div>
		</td>
	</tr>
	</table>
	</form>";

$mstring = $mstring;

$Script = "
<SCRIPT type='text/javascript'>
<!--
	
function AddMultTable(target_id){
	
	var table_target_obj = $('table[id='+target_id+'] tr');
	var total_rows = table_target_obj.length;
	var option_obj = $('#'+target_id);
		
	/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */
	var option_length = 0;
	option_obj.find('tr[depth=1]:last').each(function(){
		 option_length = $(this).find('#option_length').val();
	});
	option_rows_total = parseInt(option_length) + 1;
	/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */

	var newRow = option_obj.find('tr[depth=1]:first').clone(true).wrapAll('<table/>').appendTo('#'+option_obj.attr('id'));  //

	newRow.find('input[id=mandatory_title]').val('');
	newRow.find('input[id=mandatory_desc]').val('');
	newRow.find('input[id=mandatory_comment]').val('');
	newRow.find('input[id=mandatory_code]').val('');
	newRow.find('input[id=option_length]').val(option_rows_total);
	newRow.find('#mandatory_seq').html(total_rows);

	newRow.find('input[id^=mandatory_title]').attr('name','mandatory[details]['+option_rows_total+'][mid_title]');
	newRow.find('input[id^=mandatory_desc]').attr('name','mandatory[details]['+option_rows_total+'][mid_desc]');
	newRow.find('input[id^=mandatory_comment]').attr('name','mandatory[details]['+option_rows_total+'][mid_comment]');
	newRow.find('input[id^=mandatory_code]').attr('name','mandatory[details]['+option_rows_total+'][detail_code]');

}

function DelMultTable(target_id,seq){

	$('#del_mandatory_table').live('click',function() {
		if($('#'+target_id+' tr[depth=1]').size() > 1){
			$(this).parents('#'+target_id+'_tr').remove();
			put_No(target_id);
		}else{
			//alert('더 이상 삭제할수 없습니다.');
		}
	});

	
}

function put_No(target_id){

	var table_target_obj = $('table[id='+target_id+'] tr[depth=1]');
	var total_rows = table_target_obj.length;
	var option_obj = $('#'+target_id);

	table_target_obj.each(function(i,value){
		var no = parseInt(i) + 1;
		$(this).find('#mandatory_seq').html(no);
	});

}


function check_mi_code(){
	
	var mi_code = $('#mi_code').val();

	if(mi_code){
	
		$.ajax({
		    url : './product_mandatory.act.php',
		    type : 'POST',
		    data : {mi_code:mi_code,
					mode:'search_mi_code'
					},
		    dataType: 'html',
		    error: function(data,error){// 실패시 실행함수 
		        alert(error);},
		    success: function(args){
				if(args == 'Y'){
					$('#check_mi_code_text').html('사용가능한 코드입니다.');
					$('#mi_code_check').val('Y');
				}else{
					$('#check_mi_code_text').html('이미 사용중인 코드입니다.');
					$('#mi_code_check').val('N');
				}
        	}
    	});
	}

}

function SubmitX(frm){
	if(!CheckFormValue(frm)){
		return false;
	}
	
	var value = $('#mi_code_check').val();

	if(value == 'Y'){
		return true;
	}else{
		alert('이미 사용중인 고시코드 입니다.');
		return false;
	}
}


//-->
</SCRIPT>
";

	$P = new ManagePopLayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = $Script;
	$P->Navigation = "상품관리 > 상품고시정보 > 추가하기 ";
	$P->NaviTitle = "상품고시 추가/수정 ";
	$P->strContents = $mstring;
	$P->jquery_use = false;

	$P->PrintLayOut();


?>