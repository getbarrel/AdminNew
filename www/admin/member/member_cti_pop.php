<?php
	/* CTI_POP 2014-06-12 JBG  
	*  오류나 수정사항 많을수 있습니다.
	*  수정시 주석부탁 드립니다 ~_~
	*/
	//include($_SERVER['DOCUMENT_ROOT'].'/class/layout.class');
	include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");
	$db = new Database;
	$mdb = new Database;

	//전화번호 하이픈 처리함수사용 align_tel()
	if($tel){
		$tel	=	align_tel($tel);
	}
	if($search_type == 'tel'){
		$tel_length = strlen($search_text);
		if($tel_length == 11 || $tel_length == 12){
			$search_text = align_tel($search_text);
		}
	}

$mstring .= "
<script language='JavaScript' src='../js/jquery-1.4.js'></Script>
<LINK href='../css/facebox.css' type='text/css' rel='stylesheet'>
<style type='text/css'>
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
	
	.cti_layout_wrap {width:1100px; min-height:700px; background:#fff;}
	.cti_pop_state {width:1040px; height:130px; background:#424e69; margin:0px auto; margin-bottom:30px;}
	.cti_pop_state dl:after {content:''; display:block; clear:both;}
	.cti_pop_state dl dt {float:left; margin:0px 30px 0 26px; display:inline; line-height:0; font-size:0px;}
	.cti_pop_state dl dd {float:left;}
	.cti_pop_state dl dd.cti_pop_state_type2{float:right;margin-right:40px;}
	#facebox .close img {opacity:1;}
	#facebox .close {top: 27px;right: 32px;}
	.cti_layout_wrap h3 {padding-top:30px; margin:0px 30px 20px; background:url('../images/cti_poptitle_background.png') 0 bottom repeat-x;}
	.cti_pop_state_type1 {margin-top:37px; margin-right:56px;}
	.cti_pop_state_top {line-height:0px; font-size:0px; margin-bottom:15px;}
	.cti_pop_state_top2 {color:#fefeff; font-size:24px;}
	.cti_pop_state_top2 img{margin-left:12px; margin-top:-4px !important;}
	.cti_pop_state_type2:after {content:''; display:block; clear:both;}
	.cti_pop_state_type2 ul {float:left;}
	.cti_pop_state_type2 ul li {float:left; margin-top:34px; display:inline; cursor:pointer;}
	.cti_pop_table_wrap {margin-left:30px; margin-right:30px; padding-top:30px;}
	.cti_pop_table_wrap h4 {margin-bottom:14px;}
	.cti_pop_table_wrap h5 {margin-bottom:14px; margin-top:37px;}
	.cti_pop_table_search {background:#f0f0f0; width:100%; height:52px;}
	.cti_pop_table_search dl:after {content:''; display:block; clear:both;}
	.cti_pop_table_search dl dt {height:52px; line-height:52px; color:#363636; font-weight:bold; float:left;display:inline;}
	.cti_pop_table_search dl dt span {margin-left:27px;}
	.cti_pop_table_search dl dd {margin-left:52px; float:left; display:inline;}
	.cti_pop_table_search dl dd ul {float:left;}
	.cti_pop_table_search dl dd ul li {float:left; margin-top:12px; display:inline;}
	.cti_pop_table_li1 {width:138px; height:26px; border:1px solid #cccccc; background:#fff; margin-right:5px;}
	.cti_pop_table_li2 {width:233px; height:26px; border:1px solid #ccc; background:#fff; margin-right:10px; position:relative;}
	.cti_pop_table_li2 img {position:relative; cursor:pointer; top:3px;}
	.cti_pop_table_li3 {cursor:pointer;}
	.cti_table_list_1  {border:1px solid red;}
	.cti_table_list_1 table {border-top:1px solid #cccccc;}
	.cti_table_list_1 table tr th {border-bottom:1px solid #e5e5e5; background:#f0f0f0; text-align:center; height:32px; font-weight:bold; color:#363636;}
	.cti_table_list_1 table tr td {border-bottom:1px solid #e5e5e5; text-align:center; height:32px; color:#363636;}
	.cti_table_list_1 table tr td span {color:#ff4c3e; font-weight:bold;}
	.cti_table_list_1 table tr td img {cursor:pointer;}
	.cti_table_list_2 table tr td {height:49px !important;}
	.input_backgorund{background:url(../images/search_pop_backgorund_img.png) 10px 7px no-repeat;}
</style>
<div class='cti_layout_wrap'>";
	
	if($tel){
	$mstring .= "
	<h3>
		<img src='../images/cti_title_images.png' alt='전화' />
	</h3>
	<div class='cti_pop_state'>
		<dl>
			<dt>
				<img src='../images/call_type1.gif' alt='통화중' />
			</dt>
			<dd class='cti_pop_state_type1'>
				<ul>
					<li class='cti_pop_state_top'>
						<img src='../images/cti_pop_state_list.png' alt='고객발신전화 왔습니다.' />
					</li>
					<li class='cti_pop_state_top2'>";
							$mstring .= $tel;
						
							if($tel_type == 0){
						
							$mstring .= "<img src='../images/cti_pop_state_01.png' alt='배송문의' align='absmiddle' />";
						
							}else if($tel_type == 2){
						
							$mstring .= "<img src='../images/cti_pop_state_02.png' alt='배송반품교환문의' align='absmiddle' />";
						
							}else if($tel_type == 1){
						
							$mstring .= "<img src='../images/cti_pop_state_03.png' alt='주문결제취소문의' align='absmiddle' />";
						
							}
						
							$mstring .= "<!--<img src='../images/cti_pop_state_04.png' alt='기타상담원' align='absmiddle' />-->
					</li>
				</ul>
			</dd>
			<dd class='cti_pop_state_type2'>
				<ul> 
					<li>
						<img src='../images/call_btn.png' alt='전화받기' id='callok' onclick='CALLACK()' />
						<img src='../images/nomember_btn.png' alt='비회원입력' id='nomembercrm' onclick='crmChange()' style='display:none' />
					</li>
				</ul>
			</dd>
		</dl>
	</div>
	";
		}
	$mstring .= "
	<form action='./member_cti_pop_result.php' id='cti_pop_frm' method='post' target='cti_result'>
	<div class='cti_pop_table_wrap'>
		<h4><img src='../images/collpop_title_01.png' alt='검색' /></h4>
		<div class='cti_pop_table_search'>
			<dl style=''>
				<dt>
					<span>
						조건검색
					</span>
				</dt>
				<dd>
					<ul>
						<li class='cti_pop_table_li1'>
							<select name='search_type' style='border: 0px; width:130px; margin:4px; '>
								<option value='tel' '".($search_type == 'tel' ? 'selected' : '')."'>유입전화번호</option>
								<option value='tel' '".($search_type == 'tel' ? 'selected' : '')."'>전화번호</option>
								<option value='name' '".($search_type == 'name' ? 'selected' : '')."'>회원명</option>
								<option value='id' '".($search_type == 'id' ? 'selected' : '')."'>ID</option>
								<!--<option value='oid' '".($search_type == 'oid' ? 'selected' : '')."'>주문번호</option>-->
							</select>
						</li>
						<li class='cti_pop_table_li2'>
							<input type='text' value='".($tel ? $tel : $search_text)."' class='background_sc' name='search_text' style='border:0px;width:202px; padding:6px 0;padding-left:5px; ' />
							<img src='../images/cti_search_buttom.png' alt='돋보기'>
						</li>
						<li class='cti_pop_table_li3'>
							<input type='image' src='../images/collpop_seach_buttom.png' id='sch_btn' alt='검색' />
						</li>
					</ul>
				</dd>
			</dl>
			<iframe src='member_cti_pop_result.php?search_type=$search_type&search_text=$search_text&tel=$tel' name='cti_result' width='100%' scrolling='no' height='550' frameborder='0'></iframe>
		</div>
	</form>
	</div>
</div>
<script type='text/javascript'>
<!--
	$(document).ready(function(){
		$('.background_sc').bind('focusin',function(){
			if($(this).val() != ''){
				$(this).addClass('input_backgorund');
			}else{
				$(this)removeClass('input_backgorund');
			}
		  }).bind('focusout', function(){
			var inputValue = $(this).val();
			if(inputValue == ''){
			  $(this).removeClass('input_backgorund');
			}else{
				$(this).addClass('input_backgorund');
			}
		 });
	});
//-->
</script>";

$Contents = $mstring;

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->OnloadFunction = '';
$P->strLeftMenu = member_menu();
//$P->Navigation = '회원관리 > CRM';
//$P->title = '전체회원';
//$P->NaviTitle =  'C/S 상담내역';
$P->strContents =  $Contents;
$P->layout_display = false;
//$P->view_type = 'personalization';
echo $P->PrintLayOut();