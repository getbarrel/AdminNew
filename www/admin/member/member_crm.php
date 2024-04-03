<?	
	/* CRM 2014-06-10 JBG  
	*  오류나 수정사항 많을수 있습니다.
	*  수정시 주석부탁 드립니다 ~_~
	*/ 
//	include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
	include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");
	$db = new Database;
	$mdb = new Database;
	
	//회원 코드 값이 있을떄
	if($code){
		//회원정보
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
		//회원쿠폰 갯수
		$sql = "SELECT 
					count(*) as total_cnt 
				FROM 
					shop_cupon_regist 
				WHERE mem_ix = '$code'";
		$db->query($sql);
		$db->fetch();
		$cu_cnt	= $db->dt['total_cnt'];
		
		//금일 상담완료 건수
		$today = date('Y-m-d');
		$sql = "SELECT 
					count(*) as total_cnt 
				FROM 
					shop_member_talk_history 
				WHERE 
					qa_state = 'C' AND ta_counselor = '$admininfo[charger]' AND regdate between'$today 00:00:00' and '$today 23:59:59' ";
		$db->query($sql);
		$db->fetch();
		$today_cs = $db->dt['total_cnt'];
		//미처리 상담 건수
		$sql = "SELECT 
					count(*) as total_cnt 
				FROM 
					shop_member_talk_history 
				WHERE 
					qa_state != 'C' AND ta_counselor = '$admininfo[charger]'";
		$db->query($sql);
		$db->fetch();
		$yet_cs = $db->dt['total_cnt'];
		//CTI 내선번호 
		$sql = "SELECT 
					cti_num
				FROM 
					common_member_detail
				WHERE 
					code = '$admininfo[charger_ix]'";
		$db->query($sql);
		$db->fetch();
		$cti_num = $db->dt['cti_num'];

		$ta_code = date("Ymd")."-".rand(10000, 99999);
		
	}

$mstring .="
<link rel='stylesheet' href='../css/sample.css' media='all' type='text/css'  />
<link rel='stylesheet' href='../js/jquery-ui-1.10.2/themes/base/jquery-ui.css'>
<link rel='stylesheet' href='../css/facebox.css' type='text/css' >
<script type='text/javascript' src='../js/jquery-ui-1.10.2/ui/jquery-ui.js'></Script>
<script type='text/javascript' src='../js/facebox.js'></Script>
<script type='text/javascript' src='../js/jquery-1.8.3.js'></Script>
<script type='text/javascript' src='/include/ckeditor/ckeditor.js'></script>
<script type='text/javascript' src='../js/admin.js'></Script>
<script type='text/javascript'>

$(function(){
	$('.member_coupon').hover(function(){
		$(this).mouseover(function(){
			$(this).css({'font-weight':'bold','letter-spacing':'-1px'});
		});
		
	},function(){
		$(this).mouseleave(function(){
			$(this).css({'font-weight':'normal','letter-spacing':'0px'});
		});
	});
});
$(function(){

	setProductModal();
	$('a[rel*=facebox]').on({
		click: function(e) {
			e.stopPropagation();
		}
	});
	
	//$('.aw_time').datepicker();

	$('#ca_schtext').keydown(function (key) {
		if (key.keyCode == 13) {
			$('#pop_btn').click();
		}
	});
	
	//전화번호,회원명 검색 팝업
	$('#pop_btn').click(function(){

		if($('#call_num').val() == 'tel'){
			var search_type = 'tel';
			if($('#ca_schtext').val() == ''){
				alert('검색할 번호를 입력해주세요');
				$('#ca_schtext').focus();
				return false;
			}
		}else if($('#call_num').val() == 'user_name'){
			var search_type = 'name';
			if($('#ca_schtext').val() == ''){
				alert('검색할 회원명을 입력해주세요');
				$('#ca_schtext').focus();
				return false;
			}
		}else if($('#call_num').val() == 'id'){
			var search_type = 'id';
			if($('#ca_schtext').val() == ''){
				alert('검색할 회원ID를 입력해주세요');
				$('#ca_schtext').focus();
				return false;
			}
		}

		var search_text = $('#ca_schtext').val();
		console.log(search_text);
		//팝업창 검색타입&검색어 get으로 넘김
		$('#pop_modal').attr(\"href\",\"member_cti_pop.php?search_type=\"+ search_type + \"&search_text=\"+ encodeURIComponent(search_text) + \"&request=\");
		//팝업창 호출
		$('#pop_modal')[0].click();
	});

	//고객상담 서브밋 함수 
	$('#ca_form').submit(function(){

		var exit = false;

			//value = $(this).serialize();
			$('select[name=\"qa_state[]\"]', '#ca_form').each(function(){
				if($(this).val() == ''){
					exit = true;
					empty_value = $(this);
					return false;
				}
			});

			if(exit){

				alert('처리상태를 선택해주세요');
				empty_value.focus();
				return false;
			}

			$('select[name=\"user_qa_group[]\"]', '#ca_form').each(function(){
				if($(this).val() == ''){
					exit = true;
					empty_value = $(this);
					return false;
				}
			});

			if(exit){

				alert('문의분류를 선택해주세요');
				empty_value.focus();
				return false;
			}

			$('textarea[name=\"ta_memo[]\"]' , '#ca_form').each(function(){
				if($(this).val() == ''){
					exit = true;
					empty_value = $(this);
					return false;
				}
			});
			
			if(exit){

				alert('상담내용을 입력해주세요');
				empty_value.focus();
				return false;

			}



	});

	//셀러상담 서브밋 함수 
	$('#se_form').submit(function(){

		var exit = false;
			
			$('input[name=\"md_name[]\"]' , '#se_form').each(function(){
				if($(this).val() == ''){
					exit = true;
					empty_value = $(this);
					return false;
				}
			});

			if(exit){
				
				alert('셀러를 선택해주세요');
				empty_value.focus();
				return false;
			}
			
			$('select[name=\"type[]\"]' , '#se_form').each(function(){
				if($(this).val() == ''){
					exit = true;
					empty_value = $(this);
					return false;
				}
			});

			if(exit){
				
				alert('요청사항을 선택해주세요');
				empty_value.focus();
				return false;
			}

			$('textarea[name=\"ta_memo[]\"]' , '#se_form').each(function(){
				if($(this).val() == ''){
					exit = true;
					empty_value = $(this);
					return false;
				}
			});
			
			if(exit){

				alert('상담내용을 입력해주세요');
				empty_value.focus();
				return false;

			}

	});

	//상담메모 서브밋 함수 
	$('#memo_form').submit(function(){

		if($('#tm_memo').val() == ''){
			alert('메모를 입력해주세요');
			$(this).focus();
			return false;
		}

		var valuesToSubmit = $(this).serialize();
		
		$.ajax({
			url : $(this).attr('action'), 
			type : 'get',
			data: valuesToSubmit,
			dataType: 'html',

			error: function(data,error){
				alert('error')
			},
			success: function(result){
				alert(result);
				location.reload();
			}
		});

		return false;

	});

});

function memo_delete(idx){

	$.ajax({
		url : 'member_talk_act.php', 
		type : 'post',
		data: {act : 'memo_delete' , tm_ix : idx} ,
		dataType: 'html',
		success: function(result){
			alert(result);
			location.reload();
		}
	});

	return false;
}


function setProductModal(){
	$('a[rel*=facebox]').facebox({
		loadingImage : '../images/loading.gif',
		closeImage   : '../images/close_btn01.gif'
	});
}
function bbsloadCategory(sel,target, depth) {
	var trigger = sel.options[sel.selectedIndex].value;	// 첫번째 selectbox의 선택된 텍스트
	var form = sel.form.name;
	window.frames['iframe_act'].location.href='/bbs/category.crm.php?form=' + form + '&trigger=' + trigger + '&depth=' + depth + '&target=' + target;

}
$(document).ready(function(){
	$('.cti_contant_menu dl').last().css('border-bottom','none')
});
</script>
<style type='text/css'>
	.mouse_show_middle ol li span{position:relative; top:0; left:0;}
	.mouse_show_middle ol li span a{position:relative; top:0; left:-20px; padding-left:20px;}
	.po_r{position:relative; top:0; left:0;}
	.po_a{position:absolute; top:0; left:0; padding-left:85px;}
</style>
<div class='header_wp'>
	<div class='header_inner'>
		<img src='../images/cti_headertop2_menu01.png' alt='crm' />
	</div>
	<div class='header_inner member_cti_header2'>
		<ul>
	";
					if($result){
					
						if($result['mem_type'] == "M"){
							$result['mem_type'] = "일반회원";
						}else if($result['mem_type'] == "C"){
							$result['mem_type'] = "사업자";
						}else if($result['mem_type'] == "M"){
							$result['mem_type'] = "직원";
						}else if($result['mem_type'] == "A"){
							$result['mem_type'] = "관리자";
						}

						if($result['gp_ix']){
							$sql = "select gp_name from shop_groupinfo where gp_ix = '".$result['gp_ix']."'";
							$mdb->query($sql);
							$mdb->fetch();

							$gp_name = $mdb->dt[gp_name];
						}
						
						if($result['level_ix']){
							
							$sql = "select lv_name from shop_level where level_ix = '".$result['level_ix']."'";
							$mdb->query($sql);
							$mdb->fetch();
								
							$lv_name = $mdb->dt[lv_name];
						}else{
							$lv_name = '-';
						}

						if($result['sex_div'] == "M"){
							$sex	=	"남";
						}else if($result['sex_div'] == "W"){
							$sex	=	"여";
						}else{
							$sex	=	"미입력";
						}

			$mstring .="
			<li class='member_cti_text member_cti_textwidth1'>
				<img src='../images/men_icon.png' alt='' />
				<span class='member_grave'>".$result['name']."(".$sex.")".$result['id']."</span>
			</li>
			<li class='member_cti_text member_cti_textwidth2'>
				".$result['mem_type']."
			</li>
			<li class='member_cti_text member_cti_textwidth2'>
				".$gp_name." 
			</li>
			<li class='member_cti_text member_cti_textwidth2'>
				".$result['pcs']." 
			</li>
			<!--
			<li class='member_cti_text member_cti_textwidth2 member_cti_backgroundnone'>
				<span class='member_coupon' >보유 적립금 및 쿠폰
					<div class='mouse_show_coupon'>
						<ul style='background:none;'>
							<li class='mouse_show_top'>
							</li>
							<li class='mouse_show_middle'>
								<ol>
									<li>
										<span class='mouse_show_middle_01'>
											<a href='/admin/member/member_balance_pop.php?code=".$code."' rel='facebox' style='display:block; widrth:50px;' alt='예치금' title='예치금'>'".number_format($result['deposit'])."'</a>
										</span>
									</li>
									<li>
										<span class='mouse_show_middle_02'>
											<a href='/admin/member/member_reserve_pop.php?code=".$code."' rel='facebox' style='display:block; widrth:50px;' alt='적립금' title='적립금>'".number_format($result['mileage'])."</a>
										</span>
									</li>
									<li>
										<span class='mouse_show_middle_03'>
											<a href='/admin/member/member_mileage_pop.php?code=".$code."' rel='facebox' style='display:block; widrth:50px;' alt='포인트' title='포인트'>'".number_format($result['point'])."</a>
										</span>
									</li>
									<li class='po_r'>
										사용가능 쿠폰 <em><a href='/admin/member/member_coupon_pop.php' rel='facebox' style='' class='po_a'>".$cu_cnt."</a></em>
									</li>
								</ol>
							</li>
							<li class='mouse_show_bottom'>
							</li>
						</ul>
					</div>
				</span>
			</li>
			-->
			<!--
			<li class='member_cti_text member_cti_backgroundnone member_cti_ol' style='float:right;'>
			
				<ol>
					<li class='point_cti_ol'>
						미처리 ".$yet_cs."
					</li>
					<li>
						금일상담완료 ".$today_cs."
					</li>
					<li class='member_cti_backgroundnone'>
						 ".$admininfo['charger']."(".$cti_num.")
					</li>
					<li class='member_cti_backgroundnone'>
						<a href='#'><img src='../images/cti_headertop2_q.png' style='padding-top:10px\9;' alt='물음표'  align='absmiddle' /></a>
					</li>
				</ol>
			
			</li>
			-->
			";

			}else{

			$mstring .="
				<li class='member_cti_text member_cti_textwidth1' style='width:1140px;display:inline-block'>
					<span class='member_grave'></span>
				</li>
			";
			
			}

	$mstring .="
		</ul>
	</div>
	<div class='header_inner'>
		<img src='../images/cti_headertop2_background.png' alt='crm' />
	</div>
</div>
<div class='member_cti_contant_wrap'>
	<div class='member_cti_contant'>
		<div class='cti_contant_type1 click_height'>
			<div class='cti_contant_topback'></div>
			<div class='cti_contant_menu click_height' >
				<dl>
					<dt>
						<b>
							상담내역
						</b>
					</dt>
					<dd>
						<ul>
							<li>
								<span>
									<a href='../member/member_cscall.php?mmode=personalization&menu=non&mem_ix=$mem_ix' target='member_personalization'>
										C/S 상담내역
									</a>
								</span>	
							</li>
							<li>
								<span>
									<a href='../order/orders_memo.php?mmode=personalization&mem_ix=$mem_ix' target='member_personalization'>
										주문상담내역
									</a>
								</span>		
							</li>
							<li>
								<span>
									<a href='../cscenter/product_qna.php?mmode=personalization&mem_ix=$mem_ix' target='member_personalization'>
										상품문의 <!--strong>(2)</strong-->
									</a>
								</span>
							</li>
							<li>
								<span>
									<a href='../cscenter/bbs.php?mmode=personalization&mem_ix=$mem_ix&mode=list&board=qna' target='member_personalization'>
										1:1 문의 <!--strong>(2)</strong-->
									</a>
								</span>
							</li>
						</ul>
					</dd>
				</dl>
				<dl>
					<dt>
						<b>
							 회원정보
						</b>
					</dt>
					<dd>
						<ul>
							<li>
								<span>
									<a href='../member/member_info.php?mmode=personalization&code=$code&mem_ix=$code' target='member_personalization'>
										종합정보
									</a>
								</span>	
							</li>
							<li>
								<span>
									<a href='../member/mileage.pop.php?mmode=personalization&code=$mem_ix' id='reserve_view' target='member_personalization'>
										마일리지(적립금)
									</a>
								</span>
							</li>
							<li>
								<span>
									<a href='../promotion/cupon_user_regist_list.php?mmode=personalization&mem_ix=$mem_ix' id='coupont_view' target='member_personalization'>
										쿠폰
									</a>
								</span>
							</li>
							<li>
								<span>
									<a href='../member/member_info.php?mmode=personalization&info_type=shipping_addr&code=$mem_ix' target='member_personalization'>
										배송지 관리
									</a>
								</span>
							</li>
						</ul>
					</dd>
				</dl>
				<dl>
					<dt>
						<b>
							주문/견적내역
						</b>
					</dt>
					<dd>
						<ul>
							<li>
								<span> 
									<a href='../order/orders.list.php?mmode=personalization&code=$code&mem_ix=$code' target='member_personalization'>
										주문내역 <!--strong>(2)</strong-->
									</a>
								</span>	
							</li>
							<li>
								<span> 
									<a href='../buyer_accounts/refund.php?mmode=personalization&mem_ix=$mem_ix' target='member_personalization'>
										환불내역
									</a>
								</span>	
							</li>
							<!--<li>
								<span>
									<a href='#'>
										견적내역
									</a>
								</span>	
							</li>
							<li>
								<span>
									<a href='../member/member_info.php?mmode=personalization&info_type=file_member&code=$mem_ix' target='member_personalization'>
										증빙서류
									</a>
								</span>	
							</li>-->
						</ul>
					</dd>
				</dl>
				<dl>
					<dt>
						<b>
							기타
						</b>
					</dt>
					<dd>
						<ul>
							<li>
								<span>
									<a href='../member/view_cart.php?mmode=personalization&info_type=file_member&mem_ix=$mem_ix' target='member_personalization'>
										장바구니
									</a>
								</span>		
							</li>
							<li>
								<span>
									<a href='../member/view_wishlist.php?mmode=personalization&info_type=file_member&mem_ix=$mem_ix' target='member_personalization'>
										찜상품
									</a>
								</span>
							</li>
							<!--li>
								<span>
									<a href='../store/sms.log.detail.php?mmode=personalization&info_type=file_member&code=$mem_ix' target='member_personalization'>
										SMS 발송내역
									</a>
								</span>	
							</li>
							<li>
								<span>
									<a href='../campaign/mail.log.php?mmode=personalization&info_type=file_member&code=$mem_ix' target='member_personalization'>
										메일 발송내역
									</a>
								</span>	
							</li-->
						</ul>
					</dd>
				</dl>
				
			</div>
		</div>
		
		<!--con-->
		<div class='cti_centent_wrap'  style='float:left; width:1090px;'>
			<div class='cti_contant_topback'></div>
		";

				if($con_view == 'member'){
				$mstring .="
				<iframe src='../member/member_info.php?mmode=personalization&code=$code' name='member_personalization' id='con_view' width='100%' height='720px' topmargin='0' scrolling='auto'  frameborder='0' allowtransparency='true'></iframe>";
				
				}else if($con_view == 'order'){
				$mstring .="
				<iframe src='../order/orders.list.php?mmode=personalization&mem_ix=$code' name='member_personalization' id='con_view' width='100%' height='720px' topmargin='0' scrolling='auto'  frameborder='0' allowtransparency='true'></iframe>
				";
				}else{
				$mstring .="
				<iframe src='../order/orders_memo.php?mmode=personalization&mem_ix=$code' name='member_personalization' id='con_view' width='100%' height='720px' topmargin='0' scrolling='auto'  frameborder='0' allowtransparency='true'></iframe>
				";
				}
			$mstring .="
		</div>
		<!--con-->
		<!--tap menu S-->
		";
			if($mode == "cti"){
			$mstring .="
			<div class='cti_contant_type2' style='position:fixed; right:0px; top:42px; '>
			";
			}else{
			$mstring .="
			<div class='cti_contant_type2' style='position:fixed; right:0px; top:42px; width:0px;'>
			";
			}
			$mstring .="
			<div class='right_tap_menu'>
				<ul class='right_tap_ul'>
					<li class='s_point_z right_tap_menu_li'>
						<img src='../images/right_menu_left01_ov.png' alt='고객상담' />
					</li>
                    <!--
					<li class='right_tap_menu_li'>
						<img src='../images/right_menu_left02.png' alt='셀러' />
					</li>
					-->
					<!--li class='right_tap_menu_li'>
						<img src='../images/right_menu_left03.png' alt='공급자' />
					</li-->
					<li class='right_tap_menu_li'>
						<img src='../images/right_menu_left04.png' alt='메모' />
					</li>
				</ul>
			</div>
			<!--tap menu E-->
			<!--print 1 S-->
			<div class='hidden_wrap' style='position:relative; min-width:415px; '>
				<div style='overflow:hidden; width:100%;'>
						<div class='print print_1'>
							<div class='print_1_inner'>
								<div class='cti_fixed_wrap'>
									<div class='search_scro_wrap' style='height:24px; margin-right:5px; line-height:0px; font-size:0px; padding:1px;'>
										<select name='ca_searhtype' id='call_num' style='width:94%; border:0px; margin:4px;'>
											<option value='tel'>핸드폰/전화</option>
											<option value='user_name'>회원명</option>
											<option value='id'>ID</option>
										</select>
									</div>
									<script type='text/javascript'>
									<!--
										$(document).ready(function(){
											//alert($('.cti_contant_type2').height());
										});
									//-->
									</script>
									<div class='search_call_num'>
										<input type='text' class='input_class input_background_04' name='searh_text' id='ca_schtext' style='border:0px;width:90%; height:26px; padding-left:5px; ' />
										<img src='../images/cti_search_buttom.png' onblur='back_none(this)' onfocus='back_show(this)' id='pop_btn' alt='돋보기'><a href='member_cti_pop.php' rel='facebox' id='pop_modal'></a>
									</div>
								</div>
							<form action='member_talk_act.php' method='post' id='ca_form' name='ca_form'>
								<input type='hidden' name='act' value='pop_insert' />
								<input type='hidden' name='ta_type' value='C' />
								<input type='hidden' name='ucode' value='".$code."' />
								<input type='hidden' name='user_name' value='".$result['name']."' />
								<input type='hidden' name='user_id' value='".$result['id']."' />
								<input type='hidden' name='user_group' value='".$result['gp_ix']."' />
								<input type='hidden' name='user_level' value='".$result['level_ix']."' />
								<input type='hidden' name='user_tel' value='".$result['tel']."' />
								<input type='hidden' name='user_phone' value='".$result['pcs']."' />
								<input type='hidden' name='ta_counselor' value='".$admininfo['charger']."' />
								<div class='cti_fixed_wrap2'>
									<ul class='cti_fixed_title_ul1'>
										<li class='cti_fixed_title_li2'>
											<img src='../images/cti_fixed_title_ul1.png' alt='상담작성' />
										</li>
										<li class='cti_fixed_title_li cti_fixed_title_li2'  >
											<!--<label for='coll_back_wrap'  >콜백 응대요청 완료처리</label>
											<input type='checkbox' name='aw_state[]' id='coll_back_wrap' value='F' -->
										</li>
									</ul>
									<ul class='cti_fixed_title_ul2'>
										<li>
											<input type='text' name='ta_code' value='".$ta_code."' readonly style='border:0px;width:405px;border:1px solid #cccccc; height:28px;  padding-left:5px; ' >
										</li>
										<li class='member_cti_height'>
											<!--img src='../images/coll_buttom_s_01.png' alt='전화' /-->
										</li>
										<!--<li class='member_cti_height add_coll_con' style='margin-left:5px;'>
											<img src='../images/coll_buttom_s_02.png' alt='추가' />
										</li>-->
									</ul>
								</div>
								<div class='content_coll_wrap content_coll_one'>
									<dl>
										<dt>
											<div class='search_coll_wrap'>
												<input type='text' value='' class='input_class input_background_01' id='ta_charger' onblur='back_none(this)' onfocus='back_show(this)' name='ta_charger[]' style='border:0px;width:143px; padding:6px 0; height:26px\9; padding-left:5px; ' onclick='ShowModalWindow(\"../ca_search.php?form=ca_form\",600,380,\"origin_search\")' readonly />
												<input type='hidden' name='ta_charger_ix[]' class='ta_charger_ix' id='ta_charger_ix' />
												<img src='../images/cti_search_buttom.png' alt='돋보기' class='ca_search' onclick='ShowModalWindow(\"../ca_search.php?form=ca_form\",600,380,\"origin_search\")' />
											</div>
											<div class='search_scro_wrap'>
												<select name='qa_state[]' id='coll_val' style='border: 0px; width: 140px; margin:4px; '>
													<option value=''>처리상태</option>
													<option value='W'>접수중</option>
													<option value='I'>처리중</option>
													<option value='D'>처리지연</option>
													<option value='F'>처리완료</option>
													<option value='C'>처리취소</option>
												</select>
											</div>
											<div class='search_coll_close' onclick='close_coll(this)'>
												<img src='../images/coll_close.png' alt='닫기' />
											</div>
										</dt>
										<dd class='coll_division'>
											<div class='coll_division_div1'>
											";
													$sql = "SELECT 
																*
															FROM 
																shop_member_talk_category
															where disp = 1
															";
															$db->query($sql);
															$bbs_divs = $db->fetchall();
												$mstring .="
												<select name='user_qa_group[]' id='coll2_val' onChange='bbsloadCategory(this,\"user_subgroup\",1)' style='border: 0px; width: 90px; margin:4px;'>
													<option value=''>분류선택</option>
													";
														for($d=0;$d<count($bbs_divs);$d++){
													
															$mstring .="<option value='".$bbs_divs[$d]['tc_code']."'>".$bbs_divs[$d]['tc_name']."</option>";
													
														}
													$mstring .="
												</select>
											</div>
											<div class='coll_division_div3'>
												<input type='text' value='' class='input_class input_background_02' name='oid[]'  onblur='back_none(this)' onfocus='back_show(this)' style='border:0px;width:168px;  padding:6px 0;padding-left:5px; height:26px\9; ' />
											</div>
										</dd>
										<dd class='coll_content_creation'>
											<textarea class='input_class input_background_03' id='text_wrap_id2'  onblur='back_none(this)' onfocus='back_show(this)' style='width: 100%; padding:5px;  border: 1px solid #ccc; height: 288px; ' name='ta_memo[]'></textarea>
											<div>
												<ul class='coll_content_creation_ul'>
													<li class='coll_content_creation_li1'>
														<input type='checkbox' name='ans_sms[]' id='coll_massage' class='coll_massage' value='S' />
														<label for='coll_massage'>문자메세지</label>
													</li>
													<li class='coll_content_creation_li2'>
														<input type='checkbox' name='ans_email[]' id='coll_email' class='coll_email' value='E' />
														<label for='coll_email'>이메일</label>
													</li>
													<li class='coll_content_creation_li3'>
														<input type='checkbox' name='aw_call[]' id='coll_back' class='coll_back'  value='T' onclick='all_agree_check_mem(this)' />
														<label for='coll_back'>콜백</label>
													</li>
													<script type='text/javascript'>
													<!--
														function all_agree_check_mem(object_coll){
															var now = new Date();
															var year= now.getFullYear();
															var mon = (now.getMonth()+1)>9 ? ''+(now.getMonth()+1) : '0'+(now.getMonth()+1);
															var day = now.getDate()>9 ? ''+now.getDate() : '0'+now.getDate();
																	  
															var chan_val = year + '-' + mon + '-' + day;

															if ($(object_coll).is(':checked'))
															{
																$(object_coll).parents('.coll_content_creation').next('.coll_content_date').show();
																$(object_coll).parents('.coll_content_creation').next('.coll_content_date').find('.aw_time').val(chan_val);
															}else {
																$(object_coll).parents('.coll_content_creation').next('.coll_content_date').hide();
																$(object_coll).parents('.coll_content_creation').next('.coll_content_date').find('.aw_time').val('');
															}
															var right_wrap = $('.cti_contant_type2').height()+70;

															$('.click_height').height(right_wrap);
														}
													//-->
													</script>
												</ul>
											</div>
										</dd>
										<dd class='coll_content_date' style='display:none;'>
											<div class='coll_content_div_date1'>
												<input type='text' name='aw_date[]' class='aw_time' AUTOCOMPLETE='off' style='width:100%;height:100%;text-align:center;'>
											</div>
											<div class='coll_content_div_date2'>
												<select name='aw_hour[]' id='time_val1' style='border: 0px; width: 55px; margin:4px; ' title='시간'>";
														for($i=0;$i < 24;$i++){
													
														$mstring .="<option value='".sprintf('%02d', $i)." '".($sTime == sprintf('%02d', $i) ? 'selected':'')." > '".sprintf('%02d', $i).'시'."</option>";
													
													  }
													$mstring .="
												</select>
											</div>
											<div class='coll_content_div_date2'>
												<select name='aw_minute[]' id='time_val2' style='border: 0px; width: 55px; margin:4px; ' title='분'>";
													
														for($i=0;$i < 60;$i++){
													
															$mstring .="<option value='".sprintf('%02d', $i)." '".($sMinute == sprintf('%02d', $i) ? 'selected':'')." > '".sprintf('%02d', $i).'분'."</option>";
													
													  }
													$mstring .="
												</select>
											</div>
											<div class='coll_content_div_date3'>
												<input type='text' value='' class='background_sc' id='' name='' style='border:0px;width:143px; background:url(../images/search_backgorund_img4.png) 10px 7px no-repeat; padding:6px 0;padding-left:5px; ' title=''>
												<!--img src='../images/coll_buttom_s_01.png' alt='전화' /-->
											</div>
										</dd>
									</dl>
								</div>
							</div>
							<div class='coll_content_save'>
								<input type='image' src='../images/save_coll_cen.png' alt='저장' />
							</div>
						</div>
					</form>
					<!--print 1 E-->
					<!--print 2 S-->
					<form action='member_talk_act.php' method='post' id='se_form' name='se_form'>
					<input type='hidden' name='act' value='seller_call' />
					<input type='hidden' name='code' value=''".$code."' />
					<input type='hidden' name='id' value=''".$result['id']."' />
					<div class='print print_2' style='display:none;'>
						<div class='print_2_inner'>
							<div class='cti_fixed_wrap2'>
								<ul class='cti_fixed_title_ul1'>
									<li class='cti_fixed_title_li2'>
										<img src='../images/cti_fixed_title_ul1.png' alt='상담작성' />
									</li>
								</ul>
								<!--<ul class='cti_fixed_title_ul2'>
									<li>
										<input type='text' value='".$ta_code."' name='ta_code' readonly style='border:0px;width:365px;border:1px solid #cccccc; padding:6px 0;  padding-left:5px; ' title=''>
									</li>
									<li class='member_cti_height'>
										<img src='../images/coll_buttom_s_01.png' alt='전화' />
									</li>
									<li class='member_cti_height add_coll_con_2' style='margin-left:5px;'>
										
									</li>
								</ul>-->
							</div>
							<div class='content_coll_wrap_2 content_coll_one'>
								<dl>";
									/*
									$mstring .="
									<dd class='coll_division'>
										<div class='coll_division_div1'>
											";
												$sql = "SELECT div_ix,bm_ix,parent_div_ix,div_name,div_depth,view_order,disp,regdate
														FROM ".TBL_BBS_MANAGE_DIV."
														where bm_ix = '1' and div_depth = 1
														group by div_ix,bm_ix,parent_div_ix,div_name,div_depth,view_order,disp,regdate
														order by view_order asc, div_depth asc,div_ix asc ";
														$db->query($sql);
														$bbs_divs = $db->fetchall();
											$mstring .="
											<select name='bbs_div' id='coll2_val' onChange='bbsloadCategory(this,'sub_bbs_div2',1)' style='border: 0px; width: 90px; margin:4px; '>
												<option value=''>분류선택</option>
												";
													for($d=0;$d<count($bbs_divs);$d++){
												
														$mstring .="<option value=''".$bbs_divs[$d]['div_ix']."'>'".$bbs_divs[$d]['div_name']."</option>";
												
													}
												$mstring .="
											</select>
										</div>
										<div class='coll_division_div2'>
											<select name='sub_bbs_div2' id='user_subgroup' style='border:none;margin:4px 0'>
												<option value=''>분류선택</option>
											</select>
										</div>
										<div class='coll_division_div3'>
											<input type='text' value='' class='input_class input_background_02'  onblur='back_none(this)' onfocus='back_show(this)' name='oid[]' style='border:0px;width:168px;  padding:6px 0;padding-left:5px; ' >
										</div>
									</dd>";
									*/
									
									$mstring .="
									<dd class='coll_division'>
										<div class='coll_division_div1 seller_input'>
											<input type='text' value='' class='input_class input_background_05' name='title[]' style='border:0px;width:343px;  padding:6px 0;padding-left:5px; ' >
										</div>
										<img src='../images/coll_buttom_s_02.png' alt='추가' style='float:right' class='add_coll_con_2' />
									</dd>
									<dt>
										<div class='search_coll_wrap'>
											<input type='text' value='' class='input_class input_background_01_seller' onblur='back_none(this)' onfocus='back_show(this)' id='md_name' name='md_name[]' style='border:0px;width:143px; padding:6px 0; padding-left:5px; ' readonly onclick='ShowModalWindow(\"../se_search.php?form=se_form\",600,380,\"origin_search\")' />
											<input type='hidden' name='md_code[]' id='md_code' />
											<input type='hidden' name='company_id[]' id='company_id' />
											<img src='../images/cti_search_buttom.png' alt='돋보기' id='se_search' onclick='ShowModalWindow(\"../se_search.php?form=se_form\",600,380,\"origin_search\")' />
										</div>
										<div class='search_scro_wrap'>
											<select name='type[]' id='coll_val' style='border: 0px; width: 140px; margin:4px; '>
												<option value='C'>문의요청</option>
												<!--<option value='Q'>1:1문의</option>
												<option value='S'>상품제안</option>
												<option value='A'>정산문의</option>-->
											</select>
										</div>
										<div class='search_coll_close' onclick='close_coll(this)'>
											<img src='../images/coll_close.png' alt='닫기' />
										</div>
									</dt>
									<dd class='coll_content_creation'>
										<textarea class='input_class input_background_03' id='text_wrap_id2'  onblur='back_none(this)' onfocus='back_show(this)' style='width: 100%; padding:5px;  border: 1px solid #ccc; min-height: 88px; ' name='ta_memo[]'></textarea>
									</dd>
								</dl>
							</div>	
						</div>
						<div class='coll_content_save'>
							<input type='image' src='../images/save_coll_cen.png' alt='저장' />
						</div>
					</div>
					</form>
					<!--print 2 E-->";
					/*
					$mstring .="
					<!--print 3 S-->
					<!--form action='member_talk_act.php' method='post' id='po_form' name='po_form'>
						<input type='hidden' name='act' value='pop_insert' />
						<input type='hidden' name='ta_type' value='P' />
						<input type='hidden' name='ucode' value=''".$code."' />
						<input type='hidden' name='ta_code' value='".$ta_code."' />
						<div class='print print_3' style='display:none;'>
							<!--div class='print_3_inner'>
								<div class='cti_fixed_wrap2'>
									<ul class='cti_fixed_title_ul1'>
										<li class='cti_fixed_title_li2'>
											<img src='../images/cti_fixed_title_ul1.png' alt='상담작성' />
										</li>
									</ul>
									<ul class='cti_fixed_title_ul2'>
										<li>
											<input type='text' value='".$ta_code."' name='ta_code' readonly style='border:0px;width:365px;border:1px solid #cccccc; padding:6px 0;  padding-left:5px; ' title=''>
										</li>
										<li class='member_cti_height'>
											<!--img src='../images/coll_buttom_s_01.png' alt='전화' />
										</li>
										<li class='member_cti_height add_coll_con_3' style='margin-left:5px;'>
											<img src='../images/coll_buttom_s_02.png' alt='추가' />
										</li>
									</ul>
								</div>
							
								<div class='content_coll_wrap_3 content_coll_one'>
									<dl>
										<dd class='coll_division'>
											<div class='coll_division_div1'>";
												
													$sql = "SELECT div_ix,bm_ix,parent_div_ix,div_name,div_depth,view_order,disp,regdate
															FROM ".TBL_BBS_MANAGE_DIV."
															where bm_ix = '1' and div_depth = 1
															group by div_ix,bm_ix,parent_div_ix,div_name,div_depth,view_order,disp,regdate
															order by view_order asc, div_depth asc,div_ix asc ";
															$db->query($sql);
															$bbs_divs = $db->fetchall();
												$mstring .="
												<select name='bbs_div' id='coll2_val' onChange='bbsloadCategory(this,'user_subgroup',1)' style='border: 0px; width: 90px; margin:4px;'>
													<option value=''>분류선택</option>
													";
														for($d=0;$d<count($bbs_divs);$d++){
													
														$mstring .="<option value=''".$bbs_divs[$d]['div_ix']."'>'".$bbs_divs[$d]['div_name']."</option>";
													
														}
													$mstring .="
												</select>
											</div>
											<div class='coll_division_div2'>								
												<select name='sub_bbs_div2' id='user_subgroup' style='border:none;margin:4px 0'>
													<option value=''>분류선택</option>
												</select>
											</div>
											<div class='coll_division_div3'>
												<input type='text' value='' class='input_class input_background_02'  onblur='back_none(this)' onfocus='back_show(this)' id='' name='' style='border:0px;width:168px;  padding:6px 0;padding-left:5px; ' title=''>
											</div>
										</dd>
										<dt>
											<div class='search_coll_wrap'>
												<input type='text' value='' class='input_class input_background_01' onblur='back_none(this)' onfocus='back_show(this)' id='ta_charger' name='ta_charger[]' style='border:0px;width:143px; padding:6px 0; padding-left:5px; '>
												<input type='hidden' name='ta_charger_ix[]' class='ta_charger_ix' />
												<img src='../images/cti_search_buttom.png' alt='돋보기' id='po_search' onclick='ShowModalWindow('../po_search.php?form=po_form',600,380,'origin_search')' />
											</div>
											<div class='search_scro_wrap'>
												<select name='qa_state[]' id='coll_val' style='border: 0px; width: 140px; margin:4px; '>
													<option value=''>처리상태</option>
													<option value='W'>접수중</option>
													<option value='I'>처리중</option>
													<option value='D'>처리지연</option>
													<option value='F'>처리완료</option>
													<option value='C'>처리취소</option>
												</select>
											</div>
											<div class='search_coll_close' onclick='close_coll(this)'>
												<img src='../images/coll_close.png' alt='닫기' />
											</div>
										</dt>
										<dd class='coll_content_creation'>
											<textarea class='input_class input_background_03' id='text_wrap_id2'  onblur='back_none(this)' onfocus='back_show(this)' style='width: 100%; width:96.5%\9; padding:5px;  border: 1px solid #ccc; min-height: 88px; ' validation='true' name='contents'></textarea>
										</dd>
									</dl>
								</div>
							</div>
							<div class='coll_content_save'>
								<input type='image' src='../images/save_coll_cen.png' alt='저장' />
							</div>
						</div>
					</form>
					<!--print 3 E-->";
					*/
					$mstring .="
					<!--print 4 S-->
					<form action='member_talk_act.php' method='post' id='memo_form' name='memo_form'>
						<input type='hidden' name='act' value='memo' />
						<div class='print print_4' style='display:none;'>
							<div class='print_4_inner'>
								<div class='cti_fixed_wrap2'>
									<ul class='cti_fixed_title_ul1'>
										<li class='cti_fixed_title_li2'>
											<img src='../images/cti_fixed_title_ul1_2.png' alt='상담작성' />
										</li>
										<!--li class='member_cti_height add_coll_con_4' style='margin-left:5px;'>
											<img src='../images/coll_buttom_s_02.png' alt='추가' />
										</li-->
									</ul>
								</div>
								<div class='content_coll_wrap_3 content_coll_one' style='position:relative; top:0; left:0;'>
								";

									$sql = "SELECT
												tm_memo,tm_ix
											FROM
												shop_member_talk_memo
											WHERE
												ca_ix = '".$admininfo['charger_ix']."'
											
											";
									$db->query($sql);
									$memo = $db->fetchall();

									if($memo){
										foreach($memo as $val){
									$mstring .="
									<dl>
										<dd class='coll_content_creation' style='padding-bottom:10px;'>
											<b style='float:right;cursor:pointer; padding-bottom:10px;' onclick=\"memo_delete('".$val['tm_ix']."')\">
												<img src='../images/coll_close.png' alt='닫기'>
											</b>
											<textarea class='input_class ' id='text_wrap_id2' style='width:100%;width:96.5%\9;padding:5px;border:1px solid #ccc;min-height:100px;' readonly>'".$val['tm_memo']."'</textarea>
										</dd>
										<dd> 
											<div class='search_coll_close' onclick='close_coll(this)' style='top:105px; right:10px;'>
												<img src='../images/coll_close.png' alt='닫기'>
											</div>
										</dd>
									</dl>
										";
										}
									}
									$mstring .="
									<dl>
										<dd class='coll_content_creation' style='padding-bottom:10px;'>
											<textarea class='input_class input_background_03' id='text_wrap_id2'  onblur='back_none(this)' onfocus='back_show(this)' style='width: 100%; width:96.5%\9; padding:5px;  border: 1px solid #ccc; min-height: 100px; ' name='tm_memo'></textarea>
										</dd>
										<dd> 
											<div class='search_coll_close' onclick='close_coll(this)' style='top:105px; right:10px;'>
												<img src='../images/coll_close.png' alt='닫기'>
											</div>
										</dd>
									</dl>
								</div>
							</div>
							<div class='coll_content_save'>
								<input type='image' src='../images/save_coll_cen.png' alt='저장' />
							</div>
						</div>
					</form>
				</div>
			<!--print 4 E-->
				<div class='cti_buttom_wrap'>
					<ul>
						<li>
							<a href='/admin/member/member_sns_pop.php?code=".$code."' rel='facebox' onfocus='this.blur()'>
								<img src='../images/member_cti_buttom5.png' alt='문자' />
							</a>
						</li>
						<li>
							<a href='/admin/member/member_mail.php?code=".$code."' rel='facebox' onfocus='this.blur()'>
								<img src='../images/member_cti_buttom6.png' alt='이메일' />
							</a>
						</li>
						";
							if($code){
						$mstring .="
						<li>
							<a href='/admin/member/member_balance_pop.php?code=".$code."' rel='facebox' onfocus='this.blur()'>
								<img src='../images/member_cti_buttom1.png' alt='예치금' />
							</a>
						</li>
						<li>
							<a href='/admin/member/member_reserve_pop.php?code=".$code."' rel='facebox' onfocus='this.blur()'>
								<img src='../images/member_cti_buttom2.png' alt='적립금' />
							</a>
						</li>
						<li>
							<a href='/admin/member/member_mileage_pop.php?code=".$code."' rel='facebox' onfocus='this.blur()'>
								<img src='../images/member_cti_buttom3.png' alt='마일리지' />
							</a>
						</li>
						<li>
							<a href='/admin/member/member_coupon_pop.php?code=".$code."' rel='facebox' onfocus='this.blur()'>
								<img src='../images/member_cti_buttom4.png' alt='쿠폰' />
							</a>
						</li>
						";
							}
						$mstring .="
					</ul>
				</div>
			</div>
		</div>
		
	</div>
</div>
<div class='member_cti_contant_bottom'>
</div>

<script type='text/javascript'>
		<!--
			function close_coll(ob){
				var remove_copy = $(ob).parent().parent().parent().hasClass('content_coll_one');
				if (!remove_copy)
				{
					$(ob).parent().parent().parent().remove();	
				}

				var right_wrap = $('.cti_contant_type2').height()+70;
				$('.click_height').height(right_wrap);
			}
			
			$(document).ready(function(){
				$('.right_tap_menu_li').dblclick(function(){
			
					var menu_width = $('.cti_contant_type2').width();
					if (menu_width <= 0)
					{
						$('.cti_contant_type2').css({'width':''})
					}else {
						$('.cti_contant_type2').css({'width':'0px'})	
					}
				})
				$('.right_tap_menu_li img').click(function(){
					
					//alert($(this).attr('src'));

					//$(this).attr('src',$(this).attr('src').replace('.png','_ov.png'));
					
					if ($(this).attr('src').indexOf('ov') < 0 ){

						$('.right_tap_ul li img').each(function(){
							$(this).attr('src',$(this).attr('src').replace('_ov.png','.png'));	
						})
						$(this).attr('src',$(this).attr('src').replace('.png','_ov.png'));
					}
					
				});


				$('.right_tap_ul li').click(function(){
					var tap_menu = $('.right_tap_ul li').index($(this));
						$('.print').hide();
						$('.print').eq(tap_menu).show();
				});
				
				$('.input_class').click(function(){
					var input_text =$('.input_class').index($(this));
					$(this).css('background','none');
				});
			});
		//-->

		</script>
<script type='text/javascript'>
<!--
	function back_none(back){
		var inputValue = $(back).val();
		if(inputValue == ''){
			$(back).removeClass('input_background_none');
		}
	}
	function back_show(back_show){
		$(back_show).addClass('input_background_none');
	}
	$(document).ready(function(){

		$('.member_coupon').mouseover(function(){
			$(this).find('.mouse_show_coupon').show();
		}).mouseleave(function(){
			$(this).find('.mouse_show_coupon').hide();
		});
		$('.background_sc').bind('focusin',function(){
			$(this).addClass('input_backgorund');
		  }).bind('focusout', function(){
			var inputValue = $(this).val();
			if(inputValue == ''){
			  $(this).removeClass('input_backgorund');
			}
		 });

		var __index = 0;
		// content_coll_wrap
		 $('.add_coll_con').click(function(){
			__index = __index+1;

			var now = new Date();
			var year= now.getFullYear();
			var mon = (now.getMonth()+1)>9 ? ''+(now.getMonth()+1) : '0'+(now.getMonth()+1);
			var day = now.getDate()>9 ? ''+now.getDate() : '0'+now.getDate();
					  
			var today_val = year + '-' + mon + '-' + day;
			var new_contant = $('.content_coll_wrap:first').clone().removeClass('content_coll_one').appendTo('.cti_contant_type2 .print_1 .print_1_inner');
			new_contant.find('.input_class').removeClass('input_background_none').val('');
			new_contant.find('input[type=text],select').val('');
			new_contant.find('input[type=checkbox]').attr('checked',false);
			new_contant.find('#ta_charger').attr(\"onclick\", \"ShowModalWindow('../ca_search.php?form=ca_form&num=' + __index + ',600,380)'\");
			new_contant.find('#ta_charger').attr('id','ta_charger'+__index);
			new_contant.find('#ta_charger_ix').attr('id','ta_charger_ix'+__index);
			new_contant.find('#user_subgroup').attr('id','user_subgroup'+__index);
			new_contant.find('#sub_cate_table').attr('id','sub_cate_table'+__index);
			//new_contant.find('#coll2_val').attr('onchange','bbsloadCategory(this,'user_subgroup' + __index + '',1, ' + __index +')');
			new_contant.find('.ca_search').attr(\"onclick\", \"ShowModalWindow('../ca_search.php?form=ca_form&num=' + __index + ',600,380)'\");
			new_contant.find('.aw_time').val(today_val);
			new_contant.find('ul.coll_content_creation_ul input[type=checkbox]').each(function(){
				$(this).attr('id',$(this).attr('class')+'_'+__index);
				$(this).next('label').attr('for',$(this).attr('class')+'_'+__index);
			});

			new_contant.find('.aw_time').removeClass('aw_time').addClass('aw_time'+__index).removeClass('hasDatepicker').datepicker();

			$('.content_coll_wrap:not(:eq(0))').find('.search_coll_close').show();
			var right_wrap = $('.cti_contant_type2').height()+70;
			$('.click_height').height(right_wrap);

		});

		// content_coll_wrap_2
		  $('.add_coll_con_2').click(function(){
			__index = __index+1;
			var new_contant = $('.content_coll_wrap_2:first').clone().removeClass('content_coll_one').appendTo('.cti_contant_type2 .print_2 .print_2_inner');
			new_contant.find('.input_class').removeClass('input_background_none').val('');
			new_contant.find('input[type=text],select').val('');
			new_contant.find('input[type=checkbox]').attr('checked',false);
			new_contant.find('#md_code').attr('id','md_code'+__index);
			new_contant.find('#company_id').attr('id','company_id'+__index);
			new_contant.find('#md_name').attr(\"onclick\", \"ShowModalWindow('../se_search.php?form=se_form&num=' + __index + ',600,380)'\");
			new_contant.find('#md_name').attr('id','md_name'+__index);
			//new_contant.find('#user_subgroup').attr('id','user_subgroup'+__index);
			//new_contant.find('#sub_cate_table').attr('id','sub_cate_table'+__index);
			new_contant.find('#coll2_val').attr(\"onchange\",\"bbsloadCategory(this,'user_subgroup' + __index + ',1, ' + __index +')'\");
			new_contant.find('#se_search').attr(\"onclick\", \"ShowModalWindow('../se_search.php?form=se_form&num=' + __index + ',600,380)'\");
			new_contant.find('ul.coll_content_creation_ul input[type=checkbox]').each(function(){
				$(this).attr('id',$(this).attr('class')+'_'+__index);
				$(this).next('label').attr('for',$(this).attr('class')+'_'+__index);
			})

			$('.content_coll_wrap_2:not(:eq(0))').find('.search_coll_close').show();
			var right_wrap = $('.cti_contant_type2').height()+70;
			$('.click_height').height(right_wrap);

		 });
		// content_coll_wrap_3
		   $('.add_coll_con_3').click(function(){
			__index = __index+1;
			var new_contant = $('.content_coll_wrap_3:first').clone().removeClass('content_coll_one').appendTo('.cti_contant_type2 .print_3 .print_3_inner');
			new_contant.find('.input_class').removeClass('input_background_none').val('');
			new_contant.find('input[type=text],select').val('');
			new_contant.find('input[type=checkbox]').attr('checked',false);
			new_contant.find('#ta_charger').attr('id','ta_charger'+__index);
			new_contant.find('#user_subgroup').attr('id','user_subgroup'+__index);
			new_contant.find('#sub_cate_table').attr('id','sub_cate_table'+__index);
			new_contant.find('#coll2_val').attr(\"onchange\",\"bbsloadCategory(this,'user_subgroup' + __index + '',1, ' + __index +')'\");
			new_contant.find('#po_search').attr(\"onclick\", \"ShowModalWindow('../po_search.php?form=po_form&num=' + __index + '',600,380)'\");
			

			new_contant.find('ul.coll_content_creation_ul input[type=checkbox]').each(function(){
				$(this).attr('id',$(this).attr('class')+'_'+__index);
				$(this).next('label').attr('for',$(this).attr('class')+'_'+__index);
			})

			$('.content_coll_wrap_3:not(:eq(0))').find('.search_coll_close').show();
			var right_wrap = $('.cti_contant_type2').height()+70;
			$('.click_height').height(right_wrap);


		 });

		 // content_coll_wrap_4
		   $('.add_coll_con_4').click(function(){
			__index = __index+1;
			var new_contant = $('.content_coll_wrap_4:first').clone().removeClass('content_coll_one').appendTo('.cti_contant_type2 .print_4 .print_4_inner');
			new_contant.find('.input_class').removeClass('input_background_none').val('');
			new_contant.find('input[type=text],select').val('');
			new_contant.find('input[type=checkbox]').attr('checked',false);
			
			

			new_contant.find('ul.coll_content_creation_ul input[type=checkbox]').each(function(){
				$(this).attr('id',$(this).attr('class')+'_'+__index);
				$(this).next('label').attr('for',$(this).attr('class')+'_'+__index);
			})

			$('.content_coll_wrap_4:not(:eq(0))').find('.search_coll_close').show();
			var right_wrap = $('.cti_contant_type2').height()+70;
			$('.click_height').height(right_wrap);


		 });
	});

	function insertData(field, data, field1, data1) {
		$(field).val(data);
		$(field1).val(data1);
	}
	function insertCominfo(field, data) {
		$(field).val(data);
	}
//-->
</script>
<iframe src='' name='iframe_act' frameborder='0' style='display:none'></iframe>
<style type='text/css'>
	.input_background_01 {background:url(../images/search_backgorund_img.png) 10px 7px no-repeat;}
	.input_background_01_seller{background:url(../images/search_backgorund_img_seller.gif) 10px 7px no-repeat;}
	.input_background_02 {background:url(../images/search_backgorund_img2.png) 10px 7px no-repeat;}
	.input_background_03 {background:url(../images/search_backgorund_img3.png) 5px 5px no-repeat;}
	.input_background_04 {background:url(../images/search_backgorund_img4.png) 8px 8px no-repeat;}
	.input_background_05 {background:url(../images/add_backgorund_img.png) 8px 8px no-repeat;}
	.input_background_none {background:none !important;}
	body {background-color:transparent; margin:0px; padding:0px; overflow-y:scroll;}
</style>
<script type='text/javascript'>
<!--
	$(document).ready(function(){

		var center_height = $('.cti_centent_wrap').height();
		var right_height = $('.cti_contant_type2').outerHeight();
		if (right_height <= center_height)
		{
			$('.cti_contant_type2').css({'min-height':center_height-90,'height':center_height-90});
		}

		$('.hidden_wrap').css({'min-height':right_height-44});

		
		
	});
//-->
</script>";

$Contents = $mstring;

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->OnloadFunction = "";
$P->strLeftMenu = member_menu();
//$P->Navigation = "회원관리 > CRM";
//$P->title = "전체회원";
//$P->NaviTitle =  "C/S 상담내역";
$P->strContents =  $Contents;
$P->layout_display = false;
//$P->view_type = "personalization";
echo $P->PrintLayOut();