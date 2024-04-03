<?
	/* CRM 쿠폰내역 및 발급 2014-06-12 JBG  
	*  오류나 수정사항 많을수 있습니다.
	*  수정시 주석부탁 드립니다 ~_~
	*/ 

	include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
	$db = new Database;

	$max = 5; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}
	
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

<script language='JavaScript' src='../js/jquery-1.4.js'></Script>
<script language='JavaScript' src='../js/facebox.js'></Script>
<LINK href="../css/facebox.css" type="text/css" rel="stylesheet">
<script>
$(function(){
	$('.info_detail').click(function(){
		$('.close_image').trigger('click');
		 var href = $('#coupont_view').attr('href');
		frames['member_personalization'].location.href = href;
	});
	//팝업창 호출
	setProductModal();
});

function setProductModal()
{
	$('a[rel*=facebox]').facebox({
		loadingImage : '../images/loading.gif',
		closeImage   : '../images/coll_close.png'
	});

}
function FnAllCheck(){
	if($("#all_check").is(":checked")){
		$("input[type=checkbox]").each(function (){
			$(this).attr("checked",true);
		});
	}else{
		$("input[type=checkbox]").each(function (){
			$(this).attr("checked",false);
		});
	}
}
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
	
	.cti_layout_wrap {width:900px; min-height:300px; padding:10px 30px 20px;  background:#fff; position:relative;}
	
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
	
	.cti_table_list{}
	.cti_table_list table {border-top:1px solid #cccccc;}
	.cti_table_list table tr th {border-bottom:1px solid #e5e5e5; background:#f0f0f0; text-align:center; height:32px; font-weight:normal; color:#363636; font-weight:bold;}
	.cti_table_list table tr td {border-bottom:1px solid #e5e5e5; text-align:center; height:32px; color:#363636;}
	.cti_table_list table tr td span {color:#ff4c3e; font-weight:bold;}
	.cti_table_list table tr td img {cursor:pointer;}
		
	.border_div{border:1px solid #ddd; margin:12px 10px;}
	.border_div select{border:none; width:100%; height:26px;}
	.border_div input{height:26px;}
	.crm_btn{width:;}
	.btn_area {text-align:center; padding:20px 0;}
	.tap_btn_wrap{overflow:hidden; position:relative; top:0; left:0; margin-bottom:20px;}
	.tap_btn_wrap:after{content:""; display:block; clear:both; }
	.tap_btn{width:98px; height:28px; border:1px solid #ccc; background:#fff; margin-bottom:-1px; float:left; position:relative; top:0;left:0; z-index:100; text-align:center; line-height:230%; border-radius:4px; -webkit-border-radius:4px 4px 0 0 ; moz-border-radius:4px 4px 0 0 ; -o-border-radius:4px 4px 0 0 ; cursor:pointer;}
	.tap_btn_off{background:#f0f0f0; color:#959595; z-index:10; border-bottom:1px solid #ccc; margin-bottom:0; height:27px;}
	#tap_btn_2{width:102px; left:-5px;}
	.back_bar{width:100%; height:28px;background:#fff; border-bottom:1px solid #ccc; position:absolute; top:0; left:0; z-index:1;}
	
</style>
<script type="text/javascript">
<!--
	$(document).ready(function(){
		$('.tap_btn').click(function(){
		var click_btn = $('.tap_btn').index($(this));
		$('.cti_table_list').hide();
		$('.cti_table_list').eq(click_btn).show();

		if($('.tap_btn').hasClass('tap_btn_off')){
			$('.tap_btn').addClass('tap_btn_off')
			$(this).removeClass('tap_btn_off')
		}
		});
	});
//-->
</script>
<div class='cti_layout_wrap'>
	<h3>
		<img src="../images/crm_pop6.gif" alt="전화" />
		
	</h3>

	<h4>
		<div class="member_status">
			<img src="../images/member_icon.gif" alt="회원" style="vertical-align:middle;" />
			<span><?=$result['name']?>(<?=$result['id']?>)회원 </span>
			<a href="#" class="info_detail">상세내역 보기</a>
		</div>
		
	</h4>
	<iframe src="member_coupon_pop_result.php?code=<?=$code?>" width="100%" height="400px" scrolling="yes" frameborder='0'/ >
	</form>
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
