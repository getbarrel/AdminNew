<?
	/* CRM 예치금적립 2014-06-12 JBG  
	*  오류나 수정사항 많을수 있습니다.
	*  수정시 주석부탁 드립니다 ~_~
	*/ 
	include("../class/layout.class");
	$db = new Database;
	
	if($code){
		//회원정보가져오기
		$sql = "SELECT 
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs ,
					AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel ,cmd.gp_ix, cmd.level_ix, cmd.sex_div,cu.id , cu.code , cu.mem_type ,
					cu.mileage,cu.point,cu.deposit
				FROM 
					common_member_detail cmd
				LEFT JOIN
					common_user cu
				ON (cmd.code = cu.code)
				WHERE cmd.code = '$code'
				";
		$db->query($sql);
		$result = $db->fetch();
	}
?>

<script type='text/javascript' src='../js/jquery-1.4.js'></Script>
<script type='text/javascript' src='../js/facebox.js'></Script>
<LINK href="../css/facebox.css" type="text/css" rel="stylesheet">
<script>
$(function(){

	$('.info_detail').click(function(){
		$('.close_image').trigger('click');
		 var href = $('#deposit_view').attr('href');
		frames['member_personalization'].location.href = href;
	});
	
	//벨류데이션 체크 
	$('#deposit_form').submit(function(){

		if($('input[name=etc]').val() == ""){
			alert('적립내용을 입력해주세요.');
			$('input[name=etc]').focus();
			return false;
		}else if($('input[name=reserve]').val() == ""){
			alert('예치금을 입력해주세요.');
			$('input[name=reserve]').focus();
			return false;
		}

		var valuesToSubmit = $(this).serialize();
		
		$.ajax({
			url : 'member_crm_act.php',
			type : 'get',
			data: valuesToSubmit,
			dataType: 'html',

			error: function(data,error){
				alert('error')
			},
			success: function(result){
				alert(result);
				$('.close_image').trigger('click');
				location.reload();
			}
		});

		return false;

	});

});

function setProductModal()
{
	$('a[rel*=facebox]').facebox({
		loadingImage : '../images/loading.gif',
		closeImage   : '../images/coll_close.png'
	});

}

function ReserveReset(){
	var frm = document.forms['deposit_frm'];

	frm.reset();
	frm.act.value = 'deposit_insert';
}

function DeleteReserve(deposit_ix, uid){
	if(confirm('예치금 정보를 정말로 삭제하시겠습니까?')){
		window.frames['act'].location.href='member.act.php?act=reserve_delete&deposit_ix='+deposit_ix+'&uid='+uid;
	}
}

function UpdateReserve(deposit_ix, etc, deposit, state,use_state,use_type){

	$('input[name=deposit_ix]').val(deposit_ix);
	$('input[name=etc]').val(etc);
	$('input[name=deposit]').val(deposit);

	$('select[name=use_type]').val(use_type);
	changeUseType(use_type);
	chnageState(state);
	
	$('input[name=act]').val('deposit_update');

}

function CheckReserve(frm){

	if(frm.etc.value.length < 1){
		alert('적립내용을 입력해주세요');
		return false;
	}

	if(frm.deposit.value.length < 1){
		alert('마일리지를 입력해주세요');
		//frm.deposit.focus();
		return false;
	}

	return true;
}

function changeUseType(use_type){

	$('select[name=state]').empty();

	if(use_type == 'P'){
		$('select[name=state]').append('<option value=1>입금대기</option><option value=2>입금취소</option><!--<option value=3>입금완료</option>-->');
	}else{
		$('select[name=state]').append('<option value=4>사용완료</option><option value=5>출금요청</option><option value=6>출금취소</option><option value=7>출금확정</option><option value=8>출금완료</option>');
	}
}

function chnageState(state){
	
	$('select[name=use_state]').empty();

	if(state == '2'){
		$('select[name=use_state]').append('<option value=1>지연취소</option><option value=9>기타</option>');
	}else if(state == '3'){
		$('select[name=use_state]').append('<option value=2>고객입금</option><option value=3>주문취소</option><option value=4>주문교환</option><option value=5>주문반품입금</option><option value=6>마케팅</option><option value=9>기타</option>');
	}else if(state == '4'){
		$('select[name=use_state]').append('<option value=7>상품구매</option><option value=9>기타</option>');
	}else if(state == '5'){
		$('select[name=use_state]').append('<option value=2>고객요청</option><option value=9>기타</option>');
	}else{
		$('select[name=use_state]').append('<option value=9>기타</option>');
	}
}

$(document).ready(function(){
	
	var use_type = $('select[name=use_type] option:selected').val();
	changeUseType(use_type);

	$('select[name=use_type]').change(function (){
		var use_type = $(this).val();
		changeUseType(use_type);
	});
	
	var state = $('select[name=state] option:selected').val();
	chnageState(state);

	$('select[name=state]').change(function (){
		var state = $(this).val();
		chnageState(state);
	});

});
</script>
<style type="text/css">
	body {margin:0px; padding:0px;}
	body,p,h1,h2,h3,h4,h5,h6,ul,ol,li,dl,dt,dd,table,th,td,form,fieldset,legend,input,textarea,button{margin:0;padding:0;font-size:12px;font-family:Dotum,Arial;color:#666;}
	h1,h2,h3,h4,h5,h6	{font-size:12px;}
	img,fieldset{border:0px;}
	ul,li,ol{list-style:none;}
	a{text-decoration:none;} a:link {color:#181818;} a:hover {text-decoration:underline;color:#585858;} a:visited {color:#181818;}
	em,address{font-style:normal}
	.nobr{text-overflow:ellipsis; overflow:hidden;white-space:nowrap;}
	table	{ border-collapse:collapse;table-layout:fixed;}
	td,th	{padding:0;margin:0;}
	input,label	{vertical-align:middle;border:0;}
	label {cursor:pointer;}
	
	.cti_layout_wrap {width:600px; height:277px; padding:10px 30px 20px;  background:#fff; position:relative;}
	.cti_pop_state {width:1040px; height:130px; background:#424e69; margin:0px auto; margin-bottom:30px;}
	.cti_pop_state dl:after {content:''; display:block; clear:both;}
	.cti_pop_state dl dt {float:left; margin:0px 30px 0 26px; display:inline; line-height:0; font-size:0px;}
	.cti_pop_state dl dd {float:left;}
	#facebox .close img {opacity:1;}
	#facebox .close {top: 25px;right: 32px;}
	.cti_layout_wrap:after{content:""; display:block; clear:both;}
	.cti_layout_wrap h3 {padding-top:15px; background:url('../images/cti_poptitle_background.png') 0 bottom repeat-x;}
	.cti_layout_wrap .info_detail{width:80px; height:18px; line-height:180%;font-weight:normal; font-size:11px; text-align:center; padding-right:10px; float:right; border:1px solid #ddd; color:#363636; background:url(../images/small_arrow.gif) 80px 5px no-repeat;}
	
	.member_status{padding:14px 0;}
	.member_status span{font-weight:normal;}
	
	.cti_pop_table_li1 {width:138px; height:26px; border:1px solid #cccccc; background:#fff; margin-right:5px;}
	.cti_pop_table_li2 {width:233px; height:26px; border:1px solid #ccc; background:#fff; margin-right:10px; position:relative;}
	.cti_pop_table_li2 img {position:relative; cursor:pointer; top:3px;}
	.cti_pop_table_li3 {cursor:pointer;}
	
	.cti_table_list_1 table {border-top:1px solid #cccccc;}
	.cti_table_list_1 table tr th {border-bottom:1px solid #e5e5e5; background:#f0f0f0; text-align:center; height:32px; font-weight:normal; color:#363636;}
	.cti_table_list_1 table tr td {border-bottom:1px solid #e5e5e5; text-align:center; height:32px; color:#363636;}
	.cti_table_list_1 table tr td span {color:#ff4c3e; font-weight:bold;}
	.cti_table_list_1 table tr td img {cursor:pointer;}
	.cti_table_list_2 table tr td {height:49px !important;}
	
	.border_div{border:1px solid #ddd; margin:12px 10px;}
	.border_div select{border:none; width:100%; height:26px; word-break:nowrap;}
	.border_div input{height:26px;}
	.crm_btn{width:;}
	.btn_area {text-align:center; padding:20px 0; vertical-align:top;}
</style>
<div class='cti_layout_wrap'>
	<h3>
		<img src="../images/crm_pop3.gif" alt="전화" />
		
	</h3>

	<h4>
		<div class="member_status">
			<img src="../images/member_icon.gif" alt="회원" style="vertical-align:middle;" />
			<span><?=$result['name']?>(<?=$result['id']?>)회원 / </span><strong style="color:#ff4c3e;"><?=number_format($result['deposit'])?>C</strong>
			<a href="#" class="info_detail">상세내역 보기</a>
		</div>

	</h4>
	<form name='deposit_frm' method='post' id="deposit_form">
	<input type='hidden' name='act' value='deposit_insert'>
	<input type='hidden' name='uid' value='<?=$code?>'>
	<input type='hidden' name='deposit_ix' value=''>
	<div class='cti_table_list_1'>
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<col width='100'>
			<col width='100'>
			<col width='100'>
			<col width='100'>
			<col width='100'>
			<tr>
				<th>
					입출금 구분
				</th>
				<th>
					처리상태
				</th>
				<th>
					타입
				</th>
				<th>
					입출금 상세내역
				</th>
				<th>
					예치금
				</th>
			</tr>
			<tr>
				<td>
					<div class="border_div">
						<select name='use_type' id='state' >
							<option value='P'>입금</option>
							<option value='W'>출금</option>
						</select>
					</div>
				</td>
				<td>
					<div class="border_div">
						<select name='state' class='p11 ls1' id='state'>
						</select>
					</div>
				</td>
				<td>
					<div class="border_div">
						<select name='use_state' id='use_state'>
						</select>
					</div>
				</td>
				<td>
					<div class="border_div">
						<input type='text' class='textbox' name='etc' style="width:99%;" value=''>
					</div>
				</td>
				<td>
					<div class="border_div">
						<input type='text' class='textbox' name='deposit' style="width:99%;" size=10>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div class="btn_area">
		<input type="image" src="../images/btn/btn_pay.gif" alt="지급"  style="padding-right:10px; vertical-align:top;"/>
		<img src="../images/btn/btn_cancel.gif" alt="취소" onclick="$('.close_image').trigger('click');" style="cursor:pointer;"/>
	</div>
</div>
<script type="text/javascript">
<!--
	$(document).ready(function(){
		$('.background_sc').bind('focusin',function(){
			$(this).addClass('input_backgorund');
		  }).bind('focusout', function(){
			var inputValue = $(this).val();
			if(inputValue == ''){
			  $(this).removeClass('input_backgorund');
			}
		 });
		
	});
//-->
</script>