<?
/*
include("../class/layout.class");

$db = new Database;
*/

$Contents = "
<script language='JavaScript' >

	$(document).ready(function(){
		$('input#invoice_no').focus();
	})
	
	$(window.document).click(function(){
		$('input#invoice_no').focus();
	})
	
	
	//창이 닫힐때 opener 창 리로드
	$(window).unload(function() {
		opener.location.reload();
	});
	

	function sys_msg_text (data){
		$('#sys_msg_text').html(data);
	}

	function invoice_no_clean (){
		$('input#invoice_no').val('');
	}

</Script>";

$Contents .= "

<style type='text/css'>

.modal-content {position:relative; min-width:890px; border:3px solid #000; padding:12px; border-radius:6px; background:url(../images/korea/modal-bg.gif) repeat;}
.madal-totle-box {height:38px;}
.modal-title {margin:0; position:absolute; top:18px; left:25px; z-index:3;}
.dot-bg {margin:10px 56px 0 112px; height:12px; display:block; background:url(../images/korea/dot-icon-01.png) 0 0 repeat-x;}
.close-btn {margin:0; position:absolute; top:12px; right:17px; z-index:3;}
.madal-top {background:#ff4c3e; border-radius:5px; padding:18px 16px; margin-bottom:10px;}
.madal-top .mt_top_title {}
.madal-top .mt_top_title h3 {float:left; margin:0;  font-size:16px; font-weight:bold;}
.madal-top .mt_top_title .modal-checkbox {float:left; font-size:12px; color:#fff;}
.madal-top .mt_top_content {clear:both; padding-top:10px;}
.mt_top_content:after {content:''; display:block; clear:both;}
.mt_top_content .p-num-box {float:left; margin-right:6px; padding-left:35px; height:40px; background:#fff url(../images/korea/point-img-03.png) 9px 9px no-repeat; display:block; border:1px solid #c21f12; display:inline;}
.mt_top_content .p-num-box .p-num-input {font-size:18px; width:150px; color:#363636; border:0; padding:7px 0;}
.mt_top_content .system-box {float:left; position:relative; background:#ffbeb9; color:#363636; border:1px solid #c21f12;  font-size:12px; display:block;}
.mt_top_content .system-box .massege-box {width:500px; padding:0 12px;height:40px; line-height:40px;}
.mt_top_content .post-btn {float:left; margin-left:6px; display:block;}

.madal-content {border:2px solid #f3ad03; background:#ffd236; border-radius:5px;}

.post-list {background:#fff; min-height:460px;}
.packing-view {border:2px solid #f3ad03; border-radius:6px; background:#ffd236;}


#set_butten_no_tr {opacity:0.5;filter: alpha(opacity=50);}
.close {display:none;}

</style>
<div class='modal-content'>
	<div class='madal-totle-box'>
		<h2 class='modal-title'><img src='../images/korea/packing-title02.png' alt='' /></h2>
		<span class='dot-bg'></span>
		<span class='close-btn'><img src='../images/korea/close_btn.png' alt='close' style='cursor:pointer;' onclick='$.facebox.close();'></span>
	</div>
	<div class='madal-top'>
		<form name='invoice_frm' method='post' action='../order/delivery_barcode_invoice.iframe.php' target='order_view'>
			<input type='hidden' name='act' value='invoice' />
			<input type='hidden' id='sub_auto_set' name='sub_auto_set' value='' />
			<input type='hidden' id='sub_invoice_no' name='sub_invoice_no' value='' />
			<div class='mt_top_title'>
				<h3>송장번호입력</h3>
				<div class='modal-checkbox'><input type='checkbox' checked=''name='auto_set' value='Y' id='auto_set' /> <label for='auto_set'>자동 검수 확정 처리</label></div>
			</div>
			<div class='mt_top_content'>
				<span class='p-num-box'><input type='text' name='invoice_no' id='invoice_no' value='' class='p-num-input' /></span>
				<span class='system-box'><div id='sys_msg_text' class='massege-box' style='color:#000'>&nbsp;</div></span>
				<span id='set_butten_no_tr'  class='post-btn'><img src='../images/korea/packing-btn02.gif' alt='출고완료 확인' /></span>
				<span id='set_butten_tr' class='post-btn' style='display:none;'><img src='../images/korea/packing-btn02.gif' alt='출고완료 확인' onclick=\"$('form[name=invoice_frm]').submit();\" style='cursor:pointer;' /></span>
			</div>
		</form>
	</div>
	<div id='iframe_div' class='packing-view'>
		<iframe name='order_view' id='order_view' style='width:100%;height:400px' frameborder='0' src='../order/delivery_barcode_invoice.iframe.php' ></iframe>
	</div>
</div>
";
echo $Contents;
/*
$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "배송관리 > 배송확정검수";
$P->NaviTitle = "배송확정검수";
$P->strContents = $Contents;
echo $P->PrintLayOut();
*/

?>



