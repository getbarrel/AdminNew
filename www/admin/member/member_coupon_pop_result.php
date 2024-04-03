<?
	/* CRM 쿠폰내역 및 발급 2014-06-12 JBG  
	*  오류나 수정사항 많을수 있습니다.
	*  수정시 주석부탁 드립니다 ~_~
	*/ 

	include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
	if($_SESSION["admininfo"][admin_level] < 7 || !$_SESSION["admininfo"]){
        echo "<script>alert('관리자 로그인후 사용해주세요');top.document.location.href='/admin/admin.php'</script>";
        exit;
    }
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

		//회원 보유 쿠폰갯수
		$sql = 	"SELECT 
					count(*) as total
				FROM ".TBL_SHOP_CUPON_PUBLISH." cp , ".TBL_SHOP_CUPON_REGIST." cr , ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_CUPON." c
				WHERE cr.publish_ix = cp.publish_ix 
				and cu.code = cr.mem_ix 
				and cu.code = cmd.code 
				and c.cupon_ix = cp.cupon_ix
				and cr.mem_ix = '".$code."' 
				";
		

		$db->query($sql);
		$db->fetch();

		$total = $db->dt[total];
		//회원 보유 쿠폰정보
		$sql = 	"SELECT 
					cp.cupon_no, cp.cupon_ix, cp.use_date_type, cr.regdate, cr.regist_ix, c.cupon_kind, cmd.name as mem_name, cu.id as mem_id,
					cr.use_sdate,cp.use_edate, cr.use_date_limit as use_date_limit, cr.use_yn , case when use_yn = 1 then '사용완료' else '사용가능' end as use_yn_text , cp.publish_condition_price,cp.publish_limit_price,cp.use_product_type,c.cupon_div,c.cupon_sale_value,c.cupon_sale_type,cp.publish_name
				FROM ".TBL_SHOP_CUPON_PUBLISH." cp , ".TBL_SHOP_CUPON_REGIST." cr , ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_CUPON." c
				WHERE cr.publish_ix = cp.publish_ix 
				and cu.code = cr.mem_ix 
				and cu.code = cmd.code 
				and c.cupon_ix = cp.cupon_ix
				and cr.mem_ix = '".$code."'
				ORDER BY cr.regdate DESC
				LIMIT $start,$max ";
		$db->query($sql);
		$coupons = $db->fetchall();

	}

	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text","view");

	$sql =		"SELECT
					sp.*
				FROM
					shop_cupon_publish sp
				LEFT JOIN
					shop_cupon_regist cr
				ON (sp.publish_ix = cr.publish_ix)
				WHERE 
					sp.use_edate > now() AND sp.is_use = 1 AND cr.mem_ix != '".$code."' 
				";
	$db->query($sql);
	$pb_coupon = $db->fetchall();

?>

<script language='JavaScript' src='../js/jquery-1.4.js'></Script>
<script language='JavaScript' src='../js/facebox.js'></Script>
<LINK href="../css/facebox.css" type="text/css" rel="stylesheet">
<script>
$(function(){
	//팝업창 호출
	setProductModal();

	$('#coupon_form').submit(function(){

		if($('input[name="coupon_ix[]"]:checked').length == 0){
			alert('발급할 쿠폰을 선택해주세요');
			$(this).focus();
			return false;
		}
		
		var valuesToSubmit = $(this).serialize();
		
		$.ajax({
			url: $(this).attr('action'), 
			type : 'post',
			data: valuesToSubmit,
			dataType: 'html',

			error: function(data,error){
				alert('등록에 실패하였습니다\n다시 시도해주세요');
			},
			success: function(result){
				alert(result);
				$('.close_image',parent.document).trigger('click');
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
	<div class="tap_btn_wrap">
		<div class="tap_btn">
			보유쿠폰
		</div>
		<!--<div class="tap_btn tap_btn_off" id="tap_btn_2">
			발급가능
		</div>-->
		<div class="back_bar">
			
		</div>
	</div>
	<div class='cti_table_list cti_table_list_1'>
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<col width='90'>
			<col width='140'>
			<col width='*'>
			<col width='110'>
			<col width='110'>
			<col width='110'>
			<col width='100'>
			<tr>
				<th>
					쿠폰종류
				</th>
				<th>
					쿠폰명
				</th>
				<th>
					쿠폰발행일
				</th>
				<th>
					사용가능 상품
				</th>
				<th>
					발행일
				</th>
				<th>
					유효기간
				</th>
				<th>
					보유여부
				</th>
			</tr>
			<?php
				if($coupons){

					foreach($coupons as $val){

					if($val['cupon_div'] == "P"){
						$cp_area = "상품구매할인";
					}else{
						$cp_area = "배송비할인";
					}
			?>
			<tr height="47">
				<td>
					배송비
				</td>
				<td>
					<?=$val['publish_name']?>
				</td>
				<td>
					<?=$val['regdate']?>
				</td>
				<td>
					<?=$cp_area?>
				</td>
				<td>
					<?=$val['regdate']?>
				</td>
				<td>
					<?=date("Y-m-d" , $val['use_sdate'])?><br/>
					~<?=date("Y-m-d" , $val['use_date_limit'])?>
				</td>
				<td>
					보유
				</td>
			</tr>
			
			<?php
				}
			?>
				<tr>
					<td colspan='7' style='text-align:center'>
						<?=$str_page_bar?>
					</td>
				</tr>
			<?php

					}else{
			?>
				<tr>
					<td colspan='7'>
						항목에 해당하는 정보가 없습니다.
					</td>
				</tr>
			<?php
				}
			?>
		</table>
	</div>
	<form action="member_crm_act.php" method="post" id="coupon_form">
	<input type="hidden" name="act" value="coupon" />
	<input type="hidden" name="code" value="<?=$code?>" />
	<div class='cti_table_list cti_table_list_2' style="display:none;">
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<col width='40'>
			<col width='90'>
			<col width='140'>
			<col width='*'>
			<col width='110'>
			<col width='110'>
			<col width='110'>
			<tr>
				<th>
					<input type="checkbox" onclick="FnAllCheck();" id="all_check" class="check_list" />
				</th>
				<th>
					쿠폰종류
				</th>
				<th>
					쿠폰명
				</th>
				<th>
					쿠폰발행일
				</th>
				<th>
					사용가능 상품
				</th>
				<th>
					발행일
				</th>
				<th>
					유효기간
				</th>
			</tr>
			<?php
				if($pb_coupon){

					foreach($pb_coupon as $val){

					if($val['cupon_div'] == "P"){
						$cp_area = "상품구매할인";
					}else{
						$cp_area = "배송비할인";
					}
			?>
			<tr height="47">
				<td>
					<input type="checkbox" name="coupon_ix[]" value="<?=$val['publish_ix']?>" class="check_list" />
				</td>
				<td>
					배송비
				</td>
				<td>
					<?=$val['publish_name']?>
				</td>
				<td>
					<?=$val['regdate']?>
				</td>
				<td>
					<?=$cp_area?>
				</td>
				<td>
					<?=$val['regdate']?>
				</td>
				<td>
					<?=substr($val['use_sdate'],0,10)?><br/>
					~<?=substr($val['use_edate'],0,10)?>
				</td>
			</tr>
			<?php
				}
			?>
				
			<?php

					}else{
			?>
				<tr>
					<td colspan='6'>
						항목에 해당하는 정보가 없습니다.
					</td>
				</tr>
			<?php
				}
			?>
		</table>
	<div class="btn_area">
		<input type="image" src="../images/btn/btn_pay.gif" alt="지급" style="padding-right:10px;  vertical-align:top; cursor:pointer;"/>
		<img src="../images/btn/btn_cancel.gif" alt="취소" onclick="$('.close_image',parent.document).trigger('click');" style="cursor:pointer;"/>
	</div>
	</form>
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

<?php
	
function page_bar($total, $page, $max,$add_query="",$paging_type="inner"){
	//$page_string;
	global $cid,$depth,$category_load, $company_id;
	global $nset, $orderby;
	global $HTTP_URL, $admininfo;
	//echo $HTTP_URL;
	//if(!$add_query){		
		if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){			
			$add_query = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
		}else{			
			$add_query = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
			//echo $_SERVER["QUERY_STRING"];
		}
	//}
	if ($total % $max > 0){
		$total_page = floor($total / $max) + 1;
	}else{
		$total_page = floor($total / $max);
	}

	if ($nset == ""){
		$nset = 1;
	}

	$next = (($nset)*10+1);
	$prev = (($nset-2)*10+1);

	if($paging_type == "inner"){
		$paging_type_param = "view=innerview&";
		$paging_type_target = " target=act";
	}else{
		$paging_type_param = "";
		$paging_type_target = "";
	}


	//echo $total_page.":::".$next."::::".$prev."<br>";
	//&cid=$cid&depth=$depth&company_id=$company_id&orderby=$orderby
	if ($total){
		$prev_mark = ($prev > 0) ? "<a href='".$HTTP_URL."?".$paging_type_param."nset=".($nset-1)."&page=".(($nset-2)*10+1)."$add_query' ".$paging_type_target." style='padding:0px;margin:0px;' onclick='blockLoading();'><img src='/admin/images/paging/arrowleft02.gif' border=0  style='padding:0px;margin:0px; vertical-align:middle;' align='absmiddle'></a> " : "<img src='/admin/images/paging/arrowleft02.gif' border=0 style='vertical-align:middle;' align='absmiddle'> ";
		$next_mark = ($next <= $total_page) ? "<a href='".$HTTP_URL."?".$paging_type_param."nset=".($nset+1)."&page=".($nset*10+1)."$add_query' ".$paging_type_target." onclick='blockLoading();'><img src='/admin/images/paging/arrowright02.gif' border=0 style='vertical-align:middle;' align='absmiddle'></a>" :  " <img src='/admin/images/paging/arrowright02.gif' border=0  style='vertical-align:middle;' align='absmiddle'>";
	}

	$page_string = "";

//	for ($i = $page - 10; $i <= $page + 10; $i++)

	for ($i = ($nset-1)*10+1 ; $i <= (($nset-1)*10 + 10); $i++)
	{
		if ($i > 0)
		{
			if ($i <= $total_page)
			{
				if ($i != $page){
					if($i != (($nset-1)*10+1)){
						$page_string = $page_string.("<a href='".$HTTP_URL."?".$paging_type_param."nset=$nset&page=$i$add_query' style='font-weight:bold;color:gray' ".$paging_type_target." onclick='blockLoading();'><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 1px;cursor:pointer;height:24px;'>".$i."</span></a> ");
					}else{
						$page_string = $page_string.(" <a href='".$HTTP_URL."?".$paging_type_param."nset=$nset&page=$i$add_query' style='font-weight:bold;color:gray;margin:0px;' ".$paging_type_target." onclick='blockLoading();'><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 1px;cursor:pointer;height:24px;'>".$i."</span></a> ");
					}

				}else{
					if($i != (($nset-1)*10+1)){
						$page_string = $page_string.("<span style='color:#333333;border:1px solid #333;font-weight:bold;padding:5px 6px;margin:0 1px;color:#333;background:#fff7da;'>".$i."</span> ");
					}else{
						$page_string = $page_string.("<span style='color:#333333;border:1px solid #333;font-weight:bold;padding:5px 6px;margin:0 1px;color:#333;background:#fff7da;'>".$i."</span> ");
					}
				}


			}
		}
	}
	if($nset != "1"){
		$first_page_string = " <a href='".$HTTP_URL."?".$paging_type_param."nset=1&page=1$add_query' style='margin:0px;' ".$paging_type_target."><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 2px;cursor:pointer;vertical-align:middle;' title='첫 페이지로'>1</span></a> <!--font color='silver'>|</font--> <span style='color:gray'>...</span>";
	}

	if($nset < (floor($total_page/10)+1)){
		$last_page_string = "<span style='color:gray'>...</span>  <a href='".$HTTP_URL."?".$paging_type_param."nset=".(floor($total_page/10)+1)."&page=$total_page$add_query' style='margin:0px;'  ".$paging_type_target."><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 2px;cursor:pointer;' title='마지막 페이지로'>".$total_page."</span></a> ";
	}
	if ($total){
	$page_string = "<div id='page_area'><table border=0 ><tr><td style='padding:0;margin:0;'>".$prev_mark."</td><td nowrap style='height:26px;_padding:6px 0;margin:0;'>".$first_page_string.$page_string.$last_page_string."</td><td style='padding:0;margin:0;'>".$next_mark."</td><td nowrap style='padding:0;margin:0;' > <span style='margin-left:20px; color:#202020;'>페이지로 이동 <input type='text' class='textbox number' name='page' id='page' value='' size=4 style='margin-left:3px;' onkeydown='page_num=this.value;' onkeyup='page_num=this.value;' > / ".$total_page." <span onclick=\"goPage(".$total_page.",'".$add_query."','".$paging_type_param."','".$paging_type."')\" style='padding:5px 6px;cursor:pointer;border:1px solid silver;margin-left:5px;font-weight:bold; color:#202020;'>이동</span> </span></td></tr></table>
	<script language='javascript'>
		//var paging_type = '$paging_type';
		
	</script></div>
	";
	}

	return $page_string;
}
?>