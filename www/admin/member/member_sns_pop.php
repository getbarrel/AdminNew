<?
	include("../class/layout.class");
	include("$DOCUMENT_ROOT/class/sms.class");

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
<script type="text/javascript" src="../js/jquery-1.8.3.js"></Script>
<link rel="stylesheet" href="../js/jquery-ui-1.10.2/themes/base/jquery-ui.css">
<script type="text/javascript" src="../js/jquery-ui-1.10.2/ui/jquery-ui.js"></Script>
<script type='text/javascript' src='../js/facebox.js'></Script>
<LINK href="../css/facebox.css" type="text/css" rel="stylesheet">

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
	
	.cti_layout_wrap {width:640px; min-height:400px; padding:10px 30px 70px 30px;  background:#fff; position:relative;}
	
	#facebox .close img {opacity:1;}
	#facebox .close {top: 25px;right: 32px;}
	.cti_layout_wrap:after{content:""; display:block; clear:both;}
	.cti_layout_wrap h3 {margin:15px 0 16px; background:url('../images/cti_poptitle_background.png') 0 bottom repeat-x;}
	
	
	.member_status{padding:14px 0;}
	.member_status span{font-weight:normal;}
	
	.cti_pop_table_li1 {width:138px; height:26px; border:1px solid #cccccc; background:#fff; margin-right:5px;}
	.cti_pop_table_li2 {width:233px; height:26px; border:1px solid #ccc; background:#fff; margin-right:10px; position:relative;}
	.cti_pop_table_li2 img {position:relative; cursor:pointer; top:3px;}
	.cti_pop_table_li3 {cursor:pointer;}
	
	.cti_table_list{width:;}
	.cti_table_list:after{content:""; display:block; clear:both;}
	.cti_table_list .phone_left{float:left; width:210px; margin-left:20px;}
	.cti_table_list .phone_right{float:left; width:380px;}
	.cti_table_list table {border:1px solid #cccccc;}
	.cti_table_list table tr th {border-bottom:1px solid #e5e5e5; background:#f0f0f0; text-align:center; height:32px; font-weight:normal; color:#363636; font-weight:bold;}
	.cti_table_list table tr td {border-bottom:1px solid #e5e5e5; text-align:left; height:32px; color:#363636; padding-left:15px;}
	.cti_table_list table tr td img {cursor:pointer;}
	.cti_table_list table tr td div.select_warp{ border:1px solid #ccc; width:227px;  float:left;}
	.cti_table_list table tr td div.select_warp_2{width:226px;}
	.cti_table_list table tr td div.select_warp select{border:none; width:100%; padding:3px 5px; margin:0;}
	.cti_table_list table tr td input.addr{border:1px solid #ccc; padding:5px 5px; height:25px; height:25px\9; line-height:140%; width:228px;}
		
	.border_div{border:1px solid #ddd; margin:12px 10px;}
	.border_div select{border:none; width:100%; height:26px;}
	.border_div input{height:26px;}
	.crm_btn{width:;}
	.btn_area {text-align:center; padding:20px 0; float:left;width:64%;}
	
	
	
	.mail_select{}
	.mail_select:after{content:""; display:block; clear:both;}
	.mail_select li{float:left;}
	.mail_select li input.mail_select_btn{}
	.mail_select li input.calendar{width:80px; height:25px; line-height:140%; border:1px solid #ccc; background:url('../images/gradation.gif') repeat-x; float:left;}
	.mail_select li div{display:inline-block; }

	.cti_table_list table tr td input.title{border:1px solid #ccc; padding:5px 5px; height:25px; height:15px\9; line-height:140%; width:228x;}
	
	
	.mail_select_radio{display:inline-block; line-height:140%; font-weight:bold; vertical-align:middle; margin:5px; position:relative; top:0;left:0;}
	.time_warp:after{clear:both; display:block; content:"";}
	.phone_left_inner{width:180px; height:351px; background:url('../images/phone_03.gif'); position:relative;top:0;left:0;}
	.background_wp{position:absolute; top:2px;left:4px; padding:10px;background:#e8e8e8;margin:61px 0 0 12px;}
	.mail_content_text{border:none; width:130px;  height:205px; /**width:132px; width:132px\9; *height:207px; height:207px\9; **/   overflow:hidden; background:#e8e8e8; resize:none;}
	.mail_content_text:focus{outline:none;}
</style>
<div class='cti_layout_wrap'>
	<h3>
		<img src="../images/crm_pop1.gif" alt="전화" />
	</h3>
	<h4>
		<div class="member_status">
			<img src="../images/member_icon.gif" alt="회원" style="vertical-align:middle;" />
			<span><?if($_GET["name"]==""){echo $result['name'];}else{echo $_GET["name"];}?>(<?if($_GET["user_id"]==""){echo $result['id'];}else{echo $_SESSION["user"]["id"];}?>)회원 </span>
		</div>
	</h4>
	<form action="member_crm_act.php" id="sms_form">
	<input type="hidden" name="mode" value="sms" />
	<input type="hidden" name="oid" value="<?=$oid?>" />
	<input type="hidden" name="code" value="<?=$code?>" />
	<input type="hidden" name="dest_name" value="<?=$result['name']?>" />
	<input type='hidden' name='sms_text_array[]' id='sms_text_array' value='' />
	<div class='cti_table_list'>
		<div class="phone_left">
			<div class="phone_left_inner" >
				<div class="background_wp">
					<textarea name='sms_text' class='sms_text mail_content_text' id="sms_text" onkeyup="fc_chk_lms(this,80,1000, this.form.sms_text_count,'sms');" style="margin:0; padding:0;"></textarea>
				</div>
			</div>
			<div align='right' style='height:30px;line-height:30px; text-align:left;padding-left:25px;'>
				<b id='msg_type'>SMS</b>
				<input type='text' name='sms_text_count' style='display:inline;border:0px;text-align:right;width:35px' maxlength=4 value=0> byte/<span id='byte'>80byte</span>
				<span id='lms_type'></span><br/>
				<p style="line-height:120%; font-size:11px;">80바이트 이상 작성 시<br/>자동으로 LMS으로 변경됩니다.<br/>LMS = SMS(3건)</p>
			</div>
			
		</div>
		<div class="phone_right">
			<table cellspacing="0" cellpadding="0" border="0" width="100%" class="main_table">
				<col width="120">
				<col width="*">
				<!--<tr height="43">
					<th>
						<strong>답변 템플릿</strong>
					</th>
					<td>
						<div class="select_warp select_warp_2">
							<select id="" style="padding:3px 3px;">
								<option value="" selected="selected">업무전달</option>
								<option value="">업무전달</option>
								<option value="">업무전달</option>
								<option value="">업무전달</option>
							</select>
						</div>
					</td>
				</tr>-->
				<div id='select_sms_type' style='padding-bottom:5px;'></div>
				<tr height="43">
					<th>
						<strong>발신번호</strong>
					</th>
					<td>
						<?
							$cominfo = getcominfo();
						?>
						<input type="text" name="send_phone" class="addr" value="<?=$cominfo['com_phone']?>" />
					</td>
				</tr>
				<tr height="43">
					<th>
						<strong>수신번호</strong>
					</th>
					<td>
						<input type="text" name="receive_phone" class="addr" value="<?if($_GET["pcs"]==""){echo $result['pcs'];}else{echo $_GET["pcs"];}?>" />
					</td>
				</tr>
				<tr height="43">
					<th>
						<strong>예약구분</strong>
					</th>
					<td>
						<ul class="mail_select">
							<li>
								<span class="mail_select_radio">
									<input type="radio" value="0" name="send_time_type" id="btn_now" class="mail_select_btn" checked style="border:none;"/>
									<label for="btn_now" style="padding-left:5px;">즉시발송</label>
								</span>
							</li>
							<li style="padding-left:10px;">
								<span class="mail_select_radio">
									<input type="radio" value="1" name="send_time_type" id="btn_reservation" class="mail_select_btn" style="border:none;"/>
									<label for="btn_reservation" style="padding-left:5px;">예약발송</label>
								</span>
							</li>
						</ul>
					</td>
				</tr>
				<tr height="43">
					<th>
						<strong>예약시간</strong>
					</th>
					<td>
						<ul class="mail_select">
							<li class="time_warp">
								<input type="text" name="send_time_sms" id="send_sms_date" class="calendar sms_date" style="width:88px; height:27px; height:25px; text-align:center " />
								<div class="select_warp" style="width:63px;height:25px;margin-left:5px;">
									<select name='send_time_hour'>
										<?
											for($i=0;$i < 24;$i++){
										?>
											<option value=<?=sprintf("%02d", $i)?> <?=($sTime == sprintf("%02d", $i) ? "selected":"")?> > <?=sprintf("%02d", $i)."시"?></option>
										<?
										  }
										?>
									</select>
								</div>
								<div class="select_warp" style="width:63px;height:25px;margin-left:5px;">
									<select name='send_time_minite'>
										<?
											for($i=0;$i < 60;$i++){
										?>
											<option value=<?=sprintf("%02d", $i)?> <?=($sMinute == sprintf("%02d", $i) ? "selected":"")?> > <?=sprintf("%02d", $i)."분"?></option>
										<?
										  }
										?>
									</select>
								</div>

							</li>
						</ul>
					</td>
				</tr>
			</table>
		</div>
		<div class="btn_area"style="vertical-align:top;">
			<img src="../images/btn/mail_confirm.gif" alt="지급" onclick="member_crm_sns_pop_submit($(this).closest('form'));" style="padding-right:10px; vertical-align:top; cursor:pointer;"/>
			<img src="../images/btn/btn_cancel.gif" alt="취소" onclick=<?if($_GET["pcs"]==""){echo "$('.close_image').trigger('click');";}else{echo "window.close();";}?> style="cursor:pointer;"  />
		</div>
	</div>
	</form>
<script type="text/javascript">
<!--
$(document).ready(function(){

	 $('input[name=send_time_sms]').attr('disabled',true).css('background-color','#fff');
	 $('select[name=send_time_hour]').attr('disabled',true).css('color','#ccc');
	 $('select[name=send_time_minite]').attr('disabled',true).css('color','#ccc');

	 $('#btn_now').click(function(){
        if(this.checked){
			 $('#send_sms_date').val('');
             $('input[name=send_time_sms]').attr('disabled',true).css('background-color','#fff');
			 $('select[name=send_time_hour]').attr('disabled',true).css('color','#ccc');
			 $('select[name=send_time_minite]').attr('disabled',true).css('color','#ccc');
        }
    });
	
	$('#btn_reservation').click(function(){
        if(this.checked){
			var now = new Date();
			  var year= now.getFullYear();
			  var mon = (now.getMonth()+1)>9 ? ''+(now.getMonth()+1) : '0'+(now.getMonth()+1);
			  var day = now.getDate()>9 ? ''+now.getDate() : '0'+now.getDate();
					  
			  var chan_val = year + '-' + mon + '-' + day;
			$('#send_sms_date').val(chan_val);
            $('input[name=send_time_sms]').attr('disabled',false).css('background-color','#fff7da');
            $('select[name=send_time_hour]').attr('disabled',false).css('color','#666');
			$('select[name=send_time_minite]').attr('disabled',false).css('color','#666');
        }
    });

});

function member_crm_sns_pop_submit(from_obj){

	if($('#sms_text').val() == ''){
		alert('SMS 내용을 입력해주세요');
		$('#sms_text').focus();
		return false;
	}else if ($('input[name="receive_phone"]').val() == ''){
		alert('수신번호를 입력해주세요');
		$('input[name="receive_phone"]').focus();
		return false;
	}

	var valuesToSubmit = from_obj.serialize();
	$.ajax({
		url: from_obj.attr('action'), 
		data: valuesToSubmit,
		dataType: "html" 
	}).success(function(data){
		alert(data);
		$('.close_image').trigger('click');
	});

	return false;
}

function fc_chk_lms(aro_name,ari_min,ari_max, view_length, type)
{

   var ls_str     = aro_name.value; // 이벤트가 일어난 컨트롤의 value 값
   var li_str_len = ls_str.length;  // 전체길이

   // 변수초기화
   var li_min	   = ari_min; // SMS 제한할 글자수 크기
   var li_max      = ari_max; // LMS 제한할 글자수 크기
   var i           = 0;  // for문에 사용
   var li_byte     = 0;  // 한글일경우는 2 그밗에는 1을 더함
   var li_len      = 0;  // substring하기 위해서 사용
   var ls_one_char = ""; // 한글자씩 검사한다
   var ls_str2     = ""; // 글자수를 초과하면 제한할수 글자전까지만 보여준다.
   
   var text_array  = new Array();
   var text_array0 = "";
   var text_array1 = "";
   var text_array2 = "";

   for(i=0; i< li_str_len; i++)
   {
      // 한글자추출
      ls_one_char = ls_str.charAt(i);

      // 한글이면 2를 더한다.
      if (escape(ls_one_char).length > 4)
      {
         li_byte += 2;
      }
      // 그밗의 경우는 1을 더한다.
      else
      {
         li_byte++;
      }

      // 전체 크기가 li_max를 넘지않으면
      if(li_byte <= li_max)
      {
         li_len = i + 1;
      }

	  if(li_byte < 75){
		text_array0 = text_array0 + ls_one_char;
		//console.log(text_array);
		//text_array_lan += 1;
	  }else if(li_byte > 74 && li_byte < 145){
		text_array1 = text_array1 + ls_one_char;
		
		//console.log(text_array);
	  }else if(li_byte > 144 && li_byte < 220){
		text_array2 = text_array2 + ls_one_char;
		//console.log(text_array);
	  }
   }

   $('#sms_text_array').val(''+text_array0+'^|^'+text_array1+'^|^'+text_array2+'');
   // SMS  길이를 초과하면
   if(li_byte > li_min)
   {	
	   
      if(type == 'sms'){
		  document.getElementById("byte").innerHTML ="1000byte";
		  document.getElementById("msg_type").innerHTML ="LMS";
		  document.getElementById("lms_type").innerHTML ="<input type='hidden' name='send_type' style='width:100px' value='3'>";
		  document.getElementById("select_sms_type").innerHTML ="<strong style='color:red;'>타입 선택</strong> : <input type='radio' name='select_sms_type' value='SMS'>SMS <input type='radio' name='select_sms_type' value='LMS' checked>LMS";
	  }
   
   }

   if(li_byte < li_min)
   {
	  
	   if(type == 'sms'){
		  document.getElementById("byte").innerHTML ="80byte";
		  document.getElementById("msg_type").innerHTML ="SMS";
		  document.getElementById("lms_type").innerHTML ="<input type='hidden' name='send_type' style='width:100px' value='1'>";
		  document.getElementById("select_sms_type").innerHTML ="";
	  }
   
   }
   
   if(li_byte > li_max)
   {
      alert( li_max + " byte 를 초과 입력할수 없습니다. \n초과된 내용은 자동으로 삭제 됩니다. ");
      ls_str2 = ls_str.substr(0, li_len);
      
      aro_name.value = ls_str2;
      //alert(aro_name.focusbool);
   }else{
   	view_length.value = li_byte;
   }
   aro_name.focus();   
}

$(document).ready(function(){
	$('body').on({
		click : function(e){

			var check_select_sms = $('input[type=radio][name=select_sms_type]:checked').val();
			//alert(check_select_sms);
			var check_max_byte = $('input[type=text][name=sms_text_count]').val();
			if(check_select_sms == 'SMS' && check_max_byte > 219){
				alert('SMS 타입 전송은 220byte 를 넘을 수 없습니다.');
				e.preventDefault();
			}
			
		}
	},'input[type=radio][name=select_sms_type]')
});

//-->
</script>
