<?
$script_time[start] = time();
include("../class/layout.class");
//include("../class/calender.class");
$script_time[start] = time();
//print_r($_SESSION);

$db = new Database;

$Script = "
<script language='JavaScript' src='../js/jquery.imagetick.min.js'></Script>
<link rel='stylesheet' type='text/css' href='../v3/css/common.css' />

<Script language='javascript'>

function ProductInput(frm){

	if($('input[name=cid2]').val() ==''){
		alert('카테고리를 선택해주세요.');
		return false;
	}

	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}

	return false;
}

function loadCategory(sel,target) {
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)

	if(target == 'cid3_1'){
		$('input[name=cid2]').val(trigger);
		$('input[name=depth]').val(depth);
	}else{
		window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	}
}

function CopyOption(){
  
	var tbody = $('#option_table tbody');  

	var newRow = tbody.find('tr.option_tr:last').clone(true).appendTo(tbody);  
	newRow.find('.options').val('');
}

function RemoveOption(this_obj){
	if($('#option_table tbody').find('tr.option_tr').length > 1){
		this_obj.parent().parent().remove();
	}else{
		this_obj.parent().parent().find('input').val('');
	}
}

function readURL(input,_id) {
	if (input.files && input.files[0]) {
		var reader = new FileReader(); //파일을 읽기 위한 FileReader객체 생성
		reader.onload = function (e) { 
		//파일 읽어들이기를 성공했을때 호출되는 이벤트 핸들러
			$('#'+_id).attr('src', e.target.result);
			//이미지 Tag의 SRC속성에 읽어들인 File내용을 지정
			//(아래 코드에서 읽어들인 dataURL형식)
		}                    
		reader.readAsDataURL(input.files[0]);
		//File내용을 읽어 dataURL형식의 문자열로 저장
	}
}

function basic_img_click(this_obj){

	var _index = $('.checkbox_image_tick04').index(this_obj);

	$('input.select_check').each(function(e){
		if($(this).is(':checked') && (e != _index)){
			$(this).attr('checked',false);
			var in_id=$(this).attr('id');
			var txt='#tick_img_'+in_id;
			if($(txt)) {
				$(txt).attr('src','./images/checkbox.png');
			}
		}
		
		if(!$(this).is(':checked') && (e == _index)){
			$(this).attr('checked',true);
			var in_id=$(this).attr('id');
			var txt='#tick_img_'+in_id;
			if($(txt)) {
				$(txt).attr('src','./images/checkbox_on.png');
			}
		}

	});
}


function filterNum(str) {
	if(str){
		return str.replace(/[^0-9]/g, '');
	}else{
		return '';
	}
}



$(document).ready(function(){

	$('.btn_slide').next().next().hide();
	$('.btn_slide').click(function(){
		if ($(this).next().next().css('display') == 'none')
		{
			$(this).find('img').attr('src','./images/btn_top.png');
			$(this).next().next().fadeToggle('fast');
		}
		else if	($(this).next().next().css('display') == 'table-row')
		{
			$(this).find('img').attr('src','./images/btn_show.png');
			$(this).next().next().fadeToggle('fast');
		}
	});

	$('input.select_check').imageTick({
		tick_image_path: './images/checkbox_on.png',
		no_tick_image_path: './images/checkbox.png',
		image_tick_class: 'checkbox_image_tick04',
		act_value : 'basic_img_click($(this));'
	});


	$('.delete_check').click(function(){
		var _id = $(this).attr('id');

		if($(this).attr('id').replace('img_delete_','') == 'detail_'+$(this).attr('id').replace('img_delete_','').replace('detail_','')){
			var _index = $(this).attr('id').replace('detail_img_delete_','');
			
			$('#detail_img_delete_'+_index).hide();
			$('#detail_img_file_'+_index).val('');
			$('#detail_good_img_'+_index).attr('src','./images/goods_null.png');

		}else{
			var _index = $(this).attr('id').replace('img_delete_','');

			$('#img_delete_'+_index).hide();
			$('#img_file_'+_index).val('');
			$('#good_img_'+_index).attr('src','./images/goods_null.png');

			if($('input.select_check').eq(_index).is(':checked')){

				$(this).attr('checked',false);
				var in_id=$(this).attr('id');
				var txt='#tick_img_basic_img_'+_index;
				if($(txt)) {
					$(txt).attr('src','./images/checkbox.png');
					$(txt).hide();
				}
				
				//살아있는 첫번째 클릭하기프로세스
				if($('.checkbox_image_tick04:visible:first')){
					$('.checkbox_image_tick04:visible:first').trigger('click');
				}

			}else{
				$('.checkbox_image_tick04').eq(_index).hide();
			}
		}
	})

	//대표이미지  바꾸기 
	$('[id^=img_file_]').change(function(){
		var _index = $(this).attr('id').replace('img_file_','');
		$('#img_delete_'+_index).show();
		$('.checkbox_image_tick04').eq(_index).show();

		if($('.checkbox_image_tick04:visible').length == 1){
			$('.checkbox_image_tick04').eq(_index).trigger('click');
		}
		readURL(this,'good_img_'+_index);
	});
	
	//상품상세이미지 바꾸기 
	$('[id^=detail_img_file_]').change(function(){
		var _index = $(this).attr('id').replace('detail_img_file_','');
		$('#detail_img_delete_'+_index).show();

		readURL(this,'detail_good_img_'+_index);
	});


});

$(document).ready(function(){
	var images_height = $('.goods_check_list li').width()-2;
	$('.goods_check_list p').find('img').height(images_height);
});

$(window).resize(function(){
	var images_height = $('.goods_check_list li').width()-2;
	$('.goods_check_list p').find('img').height(images_height);
});

</Script>

";
//$script_time[sms_start] = time();
//$sms_cnt = $sms_design->getSMSAbleCount($admininfo);
//$script_time[sms_end] = time();

$Contents01 = "
<div class='goods_input_header'>
	<table border='0' cellpadding='0' cellspacing='0' width=100%' height='46'>
	<col width='50%' />
	<col width='50%' />
		<tr>
			<td><span>신상품 등록</span></td>
			<td style='padding-right:10px;'><input type='image' src='./images/btn_upload.png' style='width:63px;float:right;' onclick=\"$('form[name=input_frm]').submit();\" /></td>
		</tr>
	</table>
</div>";
$Contents01 .= "
<div class='goods_input_content'>
	<form name='input_frm' action='goods_input.act.php' method='POST' onsubmit='return ProductInput(this);' enctype='multipart/form-data' target='act'>
	<input type='hidden' name='act' value='insert'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<table width=100% cellpadding=0 cellspacing=0 border='0' align='left' '>
		<tr>
			<td width=100% valign=top >
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >";

	$Contents01 .= "
				<tr height='41'><td class='td_BG'><b class='goods_input_title'>카테고리</b>&nbsp;<img src='./images/img_checked.png' width='11' align=absmiddle></td></tr>		
				<tr height='41'>
					<td>
						<div class='select_table'>
							<ul>
								<li>".getMobileCategoryList("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</li>
								<li>".getMobileCategoryList("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</li>
								<li style='padding:0;'>".getMobileCategoryList("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</li>
							</ul>
							<script type='text/javascript'>
							<!--
								$('[name=cid0_1]').css('width','');
								$('[name=cid1_1]').css('width','');
								$('[name=cid2_1]').css('width','');
							//-->
							</script>
						</div>
					</td>
				</tr>	
				<tr>
					<td>
						<table cellpadding='0' cellspacing='0' border='0' width='100%' class='table_BG'>
							<col width='110' />
							<col width='*' />
							<tr>
								<th><b class='goods_input_title'>상품명</b>&nbsp;<img src='./images/img_checked.png' width='11' align=absmiddle></th>
								<td><div><input type='text' name='pname' value='".$pname."' validation='true' title='상품명'></div></td>
							</tr>
							<tr>
								<th><b class='goods_input_title'>상품코드</b></th>
								<td><div><input type='text' name='pcode' value='".$pcode."' title='상품코드'></div></td>
							</tr>
							<tr>
								<th><b class='goods_input_title'>원 가</b>&nbsp;<img src='./images/img_checked.png' width='11' align=absmiddle></th>
								<td><div><input type='text' name='coprice' value='".$coprice."' validation='true' title='원 가' onkeyup='this.value=filterNum(this.value);' ></div></td>
							</tr>
							<tr>
								<th><b class='goods_input_title'>도매가</b>&nbsp;<img src='./images/img_checked.png' width='11' align=absmiddle></th>
								<td><div><input type='text' name='wholesale_price' value='".$wholesale_price."' validation='true' title='도매가' onkeyup='this.value=filterNum(this.value);'></div></td>
							</tr>
							<tr>
								<th><b class='goods_input_title'>소매가</b>&nbsp;<img src='./images/img_checked.png' width='11' align=absmiddle></th>
								<td><div><input type='text' name='listprice' value='".$listprice."' validation='true'  title='소매가' onkeyup='this.value=filterNum(this.value);'></div></td>
							</tr>
							<tr>
								<th colspan='2'><b class='goods_input_title'>옵션등록</b>&nbsp;<img src='./images/img_checked.png' width='11' align=absmiddle></th>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td style='border-bottom:1px solid #c5c5c5;'>
				<div style='margin:0 10px 20px 10px;'>
					<p class='info_text'>상품의 색상+사이즈, 규격 등을 입력하여 상품을 관리할 수 있습니다. ex) Blus+S, Blus+n, 250mm</p>
					<table cellpadding='0' cellspacing='0' border='0' width='100%' id='option_table'>
					<col width='45%' />
					<col width='25%' />
					<col width='*' />
					<tbody>
						<tr>
							<td class='PgapB5'><span class='bg_li_orange'>옵션</span></td>
							<td class='PgapB5'><span class='bg_li_orange'>수량</span></td>
							<td class='PgapB5'><span class='bg_li_orange'>옵션추가</span></td>
						</tr>
						<tr class='option_tr'>
							<td><input type='text' name='option_div[]' class='options' validation='true' title='옵션' value='' style='padding:7px 3%;width:89%;' /></td>
							<td><input type='text' name='option_stock[]' class='options' validation='true' title='옵션수량' value='' onkeyup='this.value=filterNum(this.value);'  style='padding:7px 3%;width:82%;' /></td>
							<td>
								<img src='./images/btn_plus.png' width='40' onclick=\"CopyOption()\" />
								<img src='./images/btn_minus.png' width='40' style='float:right;' onclick=\"RemoveOption($(this))\"/>
							</td>
						</tr>
					</tbody>
					</table>
				</div>
			</td>
		</tr>
		<tr class='table_BG'>
			<th><b class='goods_input_title'>대표이미지 등록</b>&nbsp;<img src='./images/img_checked.png' width='11' align=absmiddle></th>
		</tr>
		<tr>
			<td style='border-bottom:1px solid #c5c5c5;'>
				<div class='goods_check_list' style='margin-top:10px;'>
					<ul>
						<li style='margin:0'>
							<div class='check_goods'>
								<img src='./images/delete_check.png' alt='' class='delete_check' id='img_delete_0' style='display:none;' />
								<input type='checkbox' name='basic_img' id='basic_img_0' value='0' class='select_check' />
								<p>
									<img src='./images/goods_null.png' alt='' width='100%' id='good_img_0' onclick=\"$('#img_file_0').trigger('click')\" style='z-index:'/>
									<input type='file' name='img_list[0]' id='img_file_0' style='width:0px;position:absolute;'/>
								</p>
							</div>
						</li>
						<li>
							<div class='check_goods'>
								<img src='./images/delete_check.png' alt='' class='delete_check' id='img_delete_1' style='display:none;' />
								<input type='checkbox' name='basic_img' id='basic_img_1' value='1' class='select_check' />
								<p>
									<img src='./images/goods_null.png' alt='' width='100%' id='good_img_1' onclick=\"$('#img_file_1').trigger('click')\" />
									<input type='file' name='img_list[1]' id='img_file_1' style='width:0px;position:absolute;'/>
								</p>
							</div>
						</li>
						<li>
							<div class='check_goods'>
								<img src='./images/delete_check.png' alt='' class='delete_check' id='img_delete_2' style='display:none;' />
								<input type='checkbox' name='basic_img' id='basic_img_2' value='2' class='select_check' />
								<p>
									<img src='./images/goods_null.png' alt='' width='100%' id='good_img_2' onclick=\"$('#img_file_2').trigger('click')\" />
									<input type='file' name='img_list[2]' id='img_file_2' style='width:0px;position:absolute;'/>
								</p>
							</div>
						</li>
						<li style='margin:0'>
							<div class='check_goods'>
								<img src='./images/delete_check.png' alt='' class='delete_check' id='img_delete_3' style='display:none;' />
								<input type='checkbox' name='basic_img' id='basic_img_3' value='3' class='select_check' />
								<p>
									<img src='./images/goods_null.png' alt='' width='100%' id='good_img_3' onclick=\"$('#img_file_3').trigger('click')\" />
									<input type='file' name='img_list[3]' id='img_file_3' style='width:0px;position:absolute;'/>
								</p>
							</div>
						</li>
						<li>
							<div class='check_goods'>
								<img src='./images/delete_check.png' alt='' class='delete_check' id='img_delete_4' style='display:none;' />
								<input type='checkbox' name='basic_img' id='basic_img_4' value='4' class='select_check' />
								<p>
									<img src='./images/goods_null.png' alt='' width='100%' id='good_img_4' onclick=\"$('#img_file_4').trigger('click')\" />
									<input type='file' name='img_list[4]' id='img_file_4' style='width:0px;position:absolute;'/>
								</p>
							</div>
						</li>
						<li>
							<div class='check_goods'>
								<img src='./images/delete_check.png' alt='' class='delete_check' id='img_delete_5' style='display:none;' />
								<input type='checkbox' name='basic_img' id='basic_img_5' value='5' class='select_check' />
								<p>
									<img src='./images/goods_null.png' alt='' width='100%' id='good_img_5' onclick=\"$('#img_file_5').trigger('click')\" />
									<input type='file' name='img_list[5]' id='img_file_5' style='width:0px;position:absolute;'/>
								</p>
							</div>
						</li>
					</ul>
				</div>
			<td>
		</tr>
		<tr class='table_BG btn_slide'>
			<th><b class='goods_input_title'>상세이미지 등록 및 설명</b><img src='./images/btn_show.png' width='19' align=absmiddle class='btn_top' /></th>
		</tr>
		<tr>
			<td><p class='info_text'>대표이미지외에 상품의 디테일한 상품을 촬영하여 올릴 수 있는 영역입니다.</p></td>
		</tr>
		<tr>
			<td>
				<div class='goods_check_list'>
					<ul>
						<li style='margin:0'>
							<div class='check_goods'>
								<img src='./images/delete_check.png' alt='' class='delete_check' id='detail_img_delete_0' style='display:none;'  />
								<p>
									<img src='./images/goods_null.png' alt='' width='100%' id='detail_good_img_0' onclick=\"$('#detail_img_file_0').trigger('click')\" />
									<input type='file' name='detail_img_list[0]' id='detail_img_file_0' style='width:0px;position:absolute;'/>
								</p>
							</div>
						</li>
						<li>
							<div class='check_goods'>
								<img src='./images/delete_check.png' alt='' class='delete_check' id='detail_img_delete_1' style='display:none;'  />
								<p>
									<img src='./images/goods_null.png' alt='' width='100%' id='detail_good_img_1' onclick=\"$('#detail_img_file_1').trigger('click')\" />
									<input type='file' name='detail_img_list[1]' id='detail_img_file_1' style='width:0px;position:absolute;'/>
								</p>
							</div>
						</li>
						<li>
							<div class='check_goods'>
								<img src='./images/delete_check.png' alt='' class='delete_check' id='detail_img_delete_2' style='display:none;'  />
								<p>
									<img src='./images/goods_null.png' alt='' width='100%' id='detail_good_img_2' onclick=\"$('#detail_img_file_2').trigger('click')\" />
									<input type='file' name='detail_img_list[2]' id='detail_img_file_2' style='width:0px;position:absolute;'/>
								</p>
							</div>
						</li>
						<li style='margin:0'>
							<div class='check_goods'>
								<img src='./images/delete_check.png' alt='' class='delete_check' id='detail_img_delete_3' style='display:none;'  />
								<p>
									<img src='./images/goods_null.png' alt='' width='100%' id='detail_good_img_3' onclick=\"$('#detail_img_file_3').trigger('click')\" />
									<input type='file' name='detail_img_list[3]' id='detail_img_file_3' style='width:0px;position:absolute;'/>
								</p>
							</div>
						</li>
						<li>
							<div class='check_goods'>
								<img src='./images/delete_check.png' alt='' class='delete_check' id='detail_img_delete_4' style='display:none;'  />
								<p>
									<img src='./images/goods_null.png' alt='' width='100%' id='detail_good_img_4' onclick=\"$('#detail_img_file_4').trigger('click')\" />
									<input type='file' name='detail_img_list[4]' id='detail_img_file_4' style='width:0px;position:absolute;'/>
								</p>
							</div>
						</li>
						<li>
							<div class='check_goods'>
								<img src='./images/delete_check.png' alt='' class='delete_check' id='detail_img_delete_5' style='display:none;'  />
								<p>
									<img src='./images/goods_null.png' alt='' width='100%' id='detail_good_img_5' onclick=\"$('#detail_img_file_5').trigger('click')\" />
									<input type='file' name='detail_img_list[5]' id='detail_img_file_5' style='width:0px;position:absolute;'/>
								</p>
							</div>
						</li>
					</ul>
				</div>
				<div class='goods_detail'>
					<p><span class='bg_li_orange'>상품상세설명</span></p>
					<textarea name='basicinfo'>".$basicinfo."</textarea>
				</div>
			<td>
		</tr>
		<tr class='table_BG btn_slide'>
			<th  style='border-top:1px solid #c5c5c5;'><b class='goods_input_title'>기타정보 입력</b><img src='./images/btn_show.png' width='19' align=absmiddle class='btn_top' /></th>
		</tr>
		<tr>
			<td  colspan='2'><p class='info_text'>고객에게 원활한 판매를 위해 상세 상품 정보를 입력하세요</p></td>
		 </tr>
		<tr>
			<td class='other_info'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%' class=''>
				<col width='20%' />
				<col width='80%' />
					<tr>
						<td><span class='bg_li_orange'>원산지</span></td>
						<td><input type='text' name='origin' value='".$origin."' style='padding:7px 3%;width:93%;'></td>
					</tr>
					<tr>
						<td><span class='bg_li_orange'>제조사</span></td>
						<td><input type='text' name='company' value='".$company."' style='padding:7px 3%;width:93%;'></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</form>
</div>";



$Contents = $Contents01;




	$P = new MobileLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = store_menu();
	$P->strContents = $Contents;
	$P->Navigation = "상품등록";
	$P->TitleBool = false;
	$P->ServiceInfoBool = true;
	echo $P->PrintLayOut();

function getMobileCategoryList($category_text ="기본카테고리 선택", $object_name="cid", $onchange_handler="", $depth=0, $cid="")
{
	$mdb = new Database;
	$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
	//echo "<script>alert('1')</script>";
	if($depth == 0 || $cid != ""){
		$sql = "SELECT * FROM ".$tb." where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";		
		$mdb->query($sql);
	}




	if ($mdb->total){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler style='width:140px;font-size:12px;height:28px;'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			//echo substr($cid,0,($depth+1)*3)." ".substr($mdb->dt[cid],0,($depth+1)*3)."<BR>";
			if(substr($cid,0,($depth+1)*3) == substr($mdb->dt[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler validation=false  style='width:140px;font-size:12px;height:28px;'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}

$script_time[end] = time();
if($admininfo[charger_id] == "forbiz"){
	//print_r($script_time);
}

?>
