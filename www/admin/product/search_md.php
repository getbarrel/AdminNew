<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include("../class/layout.class");

$db = new Database;

$Script = "
<script language='JavaScript' >

if(window.dialogArguments){

	var opener = window.dialogArguments;
}else{

	var opener = window.opener;
}

function CheckSMS(frm){

	if(frm.sms_contents.value.length < 1){
		alert('SMS 내용을 입력해주세요');
		return false;
	}

	if(frm.mobiles.value.length < 1){
		alert('SMS 보낼 셀러이 한명이상이어야 합니다.');
		return false;
	}

	return true;
}

function CheckSearch(frm){
	if(frm.search_text.value.length < 1){
		alert('검색어를 입력해주세요');
		return false;
	}
}

function SearchCharger(md_code,md_name){
//	alert($('#charger_ix',opener.document).parent().html());
	$('#md_code".($group_code == "" ? "":"_".$group_code)."',opener.document).val(md_code);
	$('#md_name".($group_code == "" ? "":"_".$group_code)."',opener.document).val(md_name);
	self.close();
}

function changeBgColor(obj){
	var objTop = obj.parentNode.parentNode;	
	for(j=0;j < objTop.rows.length;j++){
		$(objTop.rows[j]).find('td').each(function(){
			$(this).css('background-color','');	
		});
	}
	$(obj).find('td').css('background-color','#f9ded1');
}

</Script>";

$Contents = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "상품관리 > $menu_name")."</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:20px;'>
		<div class='tab'>
			<table class='s_org_tab'>
			<col width='850px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "basic" || $info_type == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'><a href='?info_type=basic&mmode=$mmode&code=$code'>부서/담당자 선택</a> </td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "search" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'><a href='?info_type=search&mmode=$mmode&code=$code'>검색어 검색</a></td>
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
	</table>";

if($info_type == "" || $info_type == "basic"){
$Contents .= "
<table cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<tr>
		<td align=center colspan=2>
			<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<col width='32%'>
			<col width='32%'>
			<col width='32%'>
			<tr >
				<td class='p11 ls1' style='padding:0 0 5px 5px;' nowrap><b> 본부 </b></td>
				<td class='p11 ls1' style='padding:0 0 5px 5px;' nowrap><b> 부서 </b></td>
				<td class='p11 ls1' style='padding:0 0 5px 5px;' nowrap><b> 담당자 </b></td>
			</tr>
			<tr>
				<td>
					<select name='group_list' id='group_list' style='border:solid 1px #ddd;width:98%;height:148px;font-size:12px;background:#fff;' class='participation' multiple>";
					$db->query("select * from shop_company_group where 1 order by group_ix");
					$group_array = $db->fetchall();
					for($i=0;$i<count($group_array); $i++){
						$Contents .= "<option value='".$group_array[$i][group_ix]."'>".$group_array[$i][group_name]."</option>";
					}
	$Contents .= "
					</select>
				</td>
				<td>
					<select name='department_list' id='department_list' style='border:solid 1px #ddd;width:98%;height:148px;font-size:12px;background:#fff;' class='participation' multiple>

					</select>
				</td>
				<td>
					<select name='person_list' id='person_list' style='border:solid 1px #ddd;width:98%;height:148px;font-size:12px;background:#fff;' class='participation' multiple>

					</select>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr height=50 align='center'>
		<td colspan='2'><input type='button' value='리스트 담기' onclick=\"add_list();\"></td>
	</tr>
	<tr height=30>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap><b> > 추가 리스트 </b></td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> </td>
	</tr>
	<tr>
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
		<tr>
			<td>
				<select name='add_list' id='add_list' style='border:solid 1px #ddd;width:100%;height:100px;font-size:12px;background:#fff;' class='participation' multiple>

				</select>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style='padding-top:10px;'><span class='small blu'> * '더블클릭' 하시면 '추가리스트'에서 삭제됩니다.</span></td>
	</tr>
	<tr height=50 align='center'>
		<td colspan='2'><input type='button' value='추가하기' onclick=\"add_department();\"></td>
	</tr>
</TABLE>
";

}

if($info_type == "search"){

if($mode == 'search'){
	if($search_text != ""){
		if(strpos($search_text,',') == false){
		
			$where = " and (cd.dp_name like '".$search_text."%' or AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') like '%".$search_text."%')";
		}else{
			$search_text_array = explode(",",$search_text);
			$where .=" and (";
			for($i=0;$i<count($search_text_array);$i++){
				if($i == count($search_text_array) -1){
					$where .= " cd.dp_name like '%".$search_text_array[$i]."%' ";
				}else{
					$where .= " cd.dp_name like '%".$search_text_array[$i]."%' or ";
				}
			}

			$search_text_array = explode(",",$search_text);
			for($i=0;$i<count($search_text_array);$i++){
				if($i == count($search_text_array) -1){
					$where .= "  AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') like '%".$search_text_array[$i]."%' ";
				}else{
					$where .= " or AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') like '%".$search_text_array[$i]."%' or ";
				}
			}
			$where .=")";
		}
	}

	$sql = "select
				cmd.code,
				AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
				cg.group_name,
				cd.dp_name,
				cp.ps_name,
				scd.duty_name
			from
				common_member_detail as cmd 
				inner join shop_company_department as cd on (cmd.department = cd.dp_ix)
				inner join shop_company_group as cg on (cd.group_ix = cg.group_ix)
				left join shop_company_position as cp on (cmd.position = cp.ps_ix)
				left join shop_company_duty as scd on (cmd.duty = scd.cu_ix)
			where
				1
				$where
				order by cd.seq  ASC";
				//echo nl2br($sql);
	$db->query($sql);
	$data_array = $db->fetchall();

}

$Contents .= "
<table cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<tr>
		<td align=center >
		<form name='search_frm' action='./search_md.php' method='get' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target=''>
		<input type='hidden' name='mode' value='search'>
		<input type='hidden' name='info_type' value='search'>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center' class='input_table_box'>
		<col width='20%'>
		<col width='*'>
		<tr>
			<td  class='input_box_title' style='padding:0 0 5px 5px;' nowrap>
				검색어
			</td>
			<td class='input_box_item' nowrap>
				 <input type='text' class='textbox' name='search_text' value='".$search_text."'> <input type='image' src='../v3/images/korea/btn_search.gif' align='absmiddle' style='cursor:pointer;'>
			</td>
		</tr>
		</table><br>

		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
		<tr>
			<td>
				<select name='group_department' id='group_department' style='border:solid 1px #ddd;width:100%;height:148px;font-size:12px;background:#fff;' class='participation' id='department'  multiple>";
				for($i=0;$i<count($data_array); $i++){
					$Contents .= "<option value='".$data_array[$i][code]."'>".$data_array[$i][group_name]." > ".$data_array[$i][dp_name]." > ".$data_array[$i][ps_name]." > ".$data_array[$i][duty_name]." > ".$data_array[$i][name]."</option>";
				}
$Contents .= "
				</select>
			</td>
		</tr> 
		</table>
		</from>
		</td>
	</tr>
	<tr height=30 style='padding:0 0 0 5px;'>
		<td ><span class='small blu'> * 부서/담당자 추가시 해당자에 '더블클릭' 하시면 '추가리스트'에 추가됩니다.</td>
	</tr>
	<tr style='padding:0 0 0 5px;'>
		<td ><span class='small blu'> * 한번에 여러 부서를 검색하시려면 검색어위에 ','를 분여 구분해주시면 됩니다. Ex:)영업팀,기획팀,마케팅팀 </td>
	</tr>
	<tr height=30>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap><b> > 추가 리스트 </b></td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> </td>
	</tr>
	<tr>
		<td width=100% valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
		<tr>
			<td>
				<select name='add_list' id='add_list' style='border:solid 1px #ddd;width:100%;height:100px;font-size:12px;background:#fff;' class='participation' id='department'  multiple> 

				</select>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style='padding-top:10px;'><span class='small blu'> * '더블클릭' 하시면 '추가리스트'에서 삭제됩니다.</span></td>
	</tr>
	<tr height=50 align='center'>
		<td colspan='2'><input type='button' value='추가하기' onclick=\"add_department();\"></td>
	</tr>
</TABLE>
";
}


$Script = "
<SCRIPT type='text/javascript'>
<!--
	$(document).ready(function(){	//검색어 검색 관련
		$('#group_department').dblclick(function(){
			var value = $('#group_department').find('option:selected').val();
			var text = $('#group_department').find('option:selected').text();
			
			if(value){
				$('#add_list').append('<option value='+value+' selected>'+text+'</option>');
			}
		});
	});
	

	$(document).ready(function(){	//부서 선택
		$('#group_list').click(function(){
			var group_ix = $('#group_list').find('option:selected').val();
		
			if(group_ix){
				$.ajax({
				    url : './category.save.php',
				    type : 'POST',
				    data : {group_ix:group_ix,
							mode:'select_department_md'
							},
				    dataType: 'json',
				    error: function(data,error){// 실패시 실행함수 
				        alert(error);},
				    success: function(args){

						if(args){
							$('#department_list').empty();
							$.each(args, function(index, entry){
								$('#department_list').append(\"<option value=\"+index+\">\"+entry+\"</option>\");
							});
						}else{
							alert('해당 부서가 없습니다.');
						}
		        	}
		    	});
			}else{
				alert('본사를 선택해 주세요.');
			}
		});

		$('#department_list').click(function(){
			var dp_ix = $('#department_list').find('option:selected').val();
		
			if(dp_ix){
				$.ajax({
				    url : './category.save.php',
				    type : 'POST',
				    data : {dp_ix:dp_ix,
							mode:'select_position_md'
							},
				    dataType: 'json',
				    error: function(data,error){// 실패시 실행함수 
				        alert(error);},
				    success: function(args){

						if(args){
							$('#person_list').empty();
							$.each(args, function(index, entry){
								$('#person_list').append(\"<option value=\"+index+\">\"+entry+\"</option>\");
							});
						}else{
							alert('해당 담당자가 없습니다.');
						}
		        	}
		    	});
			}else{
				alert('본사를 선택해 주세요.');
			}
		});

		$('#add_list').dblclick(function(){
			var selected_value = $(this).val();
			$('#add_list option').each(function(){
				if($(this).val() == selected_value){
					$(this).remove();
				}
			});
		});

	});

function add_list(){
	$('#person_list option:selected').each(function(){
		var gp_name = $('#group_list').find('option:selected').text();
		var dp_name = $('#department_list').find('option:selected').text();
		$('#add_list').append('<option value='+$(this).val()+' selected>'+gp_name+' > '+dp_name+' > '+$(this).html()+'</option>');
	});
}

function add_department(){
	var tbody = $('#objMd',opener.document);
	var thisRow = tbody.find('tr[depth^=1]:last');
	$('#add_list option').each(function(){
		var dp_ix = $(this).val();
		var dp_text = $(this).html();
		var add_html = '<tr style=\"height:26px;\" id=\"row_'+dp_ix+'\"><td><input type=hidden name=md_code[] id=\"person_'+dp_ix+'\" value=\"'+dp_ix+'\"></td><td>'+dp_text+'</td><td><a href=\"javascript:void(0)\" onClick=\"person_del(\''+dp_ix+'\')\"><img src=\"../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif\" border=0></a></td></tr>';
		tbody.append(add_html);
	});
}


//-->
</SCRIPT>

";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "담당자 선택";
$P->NaviTitle = "담당자 선택";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>