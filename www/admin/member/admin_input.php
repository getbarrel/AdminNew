<? include("../admin_header.php");

?>

<form name='seller_join_input' id='seller_join_input' action='./member.act.php' method='post' enctype="multipart/form-data" target='ifram_act'>
<input type='hidden' name='act' value='seller_join_input'>
<input type='hidden' name='company_id' value='<?=$_REQUEST["company_id"]?>'>
<input type='hidden' name='code' value='<?=$_REQUEST["code"]?>'>
<input type='hidden' name='md_code' id='md_code' value=''>

<div class="daiso_admin_margin">
	<h2><img src="/admin/v3/images/member/admin_join_title2.gif" alt="" /></h2>
	<div class='daiso_admin_input'>
		<h4>사업자 정보 입력</h4>
		<div class='daiso_admin_table'>
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<col width='176'>
				<col width='*'>
				<tr>
					<th>
						<div>
						주요상품군 선택 <span>*</span>
						</div>
					</th>
					<td>
						<div class='daiso_admin_height'>
							<div class='daiso_admin_select'>
								<? 
									$db = new Database;
									$sql = "select * from shop_category_info where depth = '0' and category_use = '1' and category_type='C'";
									$db->query($sql);
									$cateinfos = $db->fetchall();
								?>
								<select name="seller_cid" id="seller_cid" style="border:0px;width:100%;" validation="true" title="주용상품군">
									<option>선택</option>
									<? for($i=0;$i<count($cateinfos);$i++){ ?>
									<option value="<?=$cateinfos[$i]['cid'];?>"><?=$cateinfos[$i]['cname'];?></option>
									<? } ?>
								</select>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<th>
						<div>
						주요판매상품<br/>내용 작성 <span>*</span>
						</div>
					</th>
					<td>
						<div class='daiso_admin_height'>
							<textarea name="seller_msg" validation='true' title='주요판매 상품 내용'></textarea>
						</div>
					</td>
				</tr>
				<tr>
					<th>
						<div>
						사업자등록증 <span>*</span>
						</div>
					</th>
					<td>
						<div class='daiso_admin_height' style='height:26px;'>
							<!--<input type="text" id="fileName" class="file_input_textbox app_table_text" validation='true' style='width:177px; padding:4px; margin:0px;' title='사업자등록증' readonly="readonly">
							<input type="button" value="Search files" class="file_input_button" onClick="file_input_click(this)" />
							<input type="file" class="file_input_hidden" name="business_file" validation="false" title="사업자등록증사본" onchange="javascript:$('.file_input_textbox').eq($('.file_input_hidden').index($(this))).val($(this).val())"  />-->
							<input type="file"  name="business_file" validation="true" title="사업자등록증사본" />
						</div>
					</td>
				</tr>
				<tr>
					<th>
						<div>
						기타자료
						</div>
					</th>
					<td>
						<div class='daiso_admin_height' style='height:26px;'>
							<!--<input type="text" id="fileName" class="file_input_textbox app_table_text" style='width:177px; padding:4px; margin:0px;'readonly="readonly">
							<input type="button" value="Search files" class="file_input_button" onClick="file_input_click(this)" />
							<input type="file" class="file_input_hidden" name="other_file" validation="false" title="기타자료" onchange="javascript:$('.file_input_textbox').eq($('.file_input_hidden').index($(this))).val($(this).val())"  />-->
							<input type="file"  name="other_file" validation="false" title="기타자료"/>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class='daiso_admin_buttom'>
		<ul>
			<li>
			<img src="/admin/v3/images/member/admin_join_ok.gif" alt="요청하기" style="cursor:pointer;" onclick="submit_form_click(document.getElementById('seller_join_input'));return false;"/>

			</li>
			<li style='margin-left:7px;'>
				<a href="javascript:history.go(-1)"><img src="/admin/v3/images/member/admin_join_no.gif" alt="취소하기" /></a>
			</li>
		</ul>
	</div>
</div>
</form>

<iframe name="ifram_act" id="ifram_act" style="display:none;"></iframe>

<script>

function submit_form_click(frm){
	if(CheckFormValue(frm)){
		frm.submit();
	}
}

$(function (){
	$('select[name=seller_cid]').change(function (){
		var value = $(this).val();

		$.ajax({
			type:"POST",
			url:"./member.act.php",
			dataType:"html",
			data:{
				act:'selectMD',
				cid : value},
			success: function(msg){
				if(msg != "N"){
					$('input[name=md_code]').val(msg);
				}
			}
		});
	});

});
</script>

<? include("../admin_copyright.php");?>