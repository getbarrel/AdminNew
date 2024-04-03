function CategoryMode(cmode)
{
	if(cmode == "edit"){
		document.getElementById('edit_category').style.display ='block';
		document.getElementById('add_subcategory').style.display ='none';
	}else{
		document.getElementById('edit_category').style.display ='none';
		document.getElementById('add_subcategory').style.display ='block';
	}
}

function CategorySave(frm,vMode)
{
	if(CheckFormValue(frm)){
		frm.mode.value = vMode;
		frm.submit();
	}
}

function thisCategorySave(frm,vMode)
{
	if (frm.cid.value.length < 1){
		alert('수정 하시고자 하는 상품사이즈관리를 선택해 주세요');
		return false;
	}
	if (frm.this_depth.value < 1){
		alert('상세정보는 SUB 카테고리구성에서만 등록이 가능합니다.');
		return false;
	}
	if(CheckFormValue(frm)){
		frm.mode.value = vMode;
		frm.submit();
	}


}

function SubCategorySave(frm,vMode)
{
	if (frm.title.value.length < 1){
		alert('분류명을 입력하여 주세요.');
		return false;
	}

	if (frm.sub_depth.value >= 2){
		alert('카테고리구성은 2단계까지만 가능합니다.');
		return false;
	}

	if(CheckFormValue(frm)){
		frm.mode.value = vMode;
		frm.submit();
	}
}

function setCategory(cname, cid, depth, size_use, title_img, contents_pc, contents_mo)
{
	cname = cname.replace("&quot;","\"");
	cname = cname.replace("&#39;","'");

	document.thisCategoryform.title.value			= cname;

	if(title_img == ''){
		document.thisCategoryform.title_img_old.value	= '';
		$("#show_title").html('');
	}else{
		document.thisCategoryform.title_img_old.value	= title_img;
		$("#show_title").html("<a href='/data/barrel_data/images/size/"+title_img+"' target='_new'><img src='/data/barrel_data/images/size/"+title_img+"' width='25px' height='25px' style='cursor:pointer'></a><input type='checkbox' name='title_img_del' value='y'>Del");
	}

	if(contents_pc == ''){
		document.thisCategoryform.contents_pc_old.value	= '';
		$("#show_pc").html('');
	}else{
		document.thisCategoryform.contents_pc_old.value	= contents_pc;
		$("#show_pc").html("<a href='/data/barrel_data/images/size/"+contents_pc+"' target='_new'><img src='/data/barrel_data/images/size/"+contents_pc+"' width='25px' height='25px' style='cursor:pointer'></a><input type='checkbox' name='contents_pc_del' value='y'>Del");
	}

	if(contents_mo == ''){
		document.thisCategoryform.contents_mo_old.value	= '';
		$("#show_mo").html('');
	}else{
		document.thisCategoryform.contents_mo_old.value	= contents_mo;
		$("#show_mo").html("<a href='/data/barrel_data/images/size/"+contents_mo+"' target='_new'><img src='/data/barrel_data/images/size/"+contents_mo+"' width='25px' height='25px' style='cursor:pointer'></a><input type='checkbox' name='contents_mo_del' value='y'>Del");
	}

	document.thisCategoryform.cid.value				= cid;
	document.thisCategoryform.this_depth.value		= depth;

	if(size_use == "1"){	//사용
		$('#size_use_id').attr('checked',true);
	}else if(size_use == "0"){	//미사용
		$('#size_use_id_0').attr('checked',true);
	}else{
		$('#size_use_id_0').attr('checked',true);
	}

	document.size_order.this_depth.value = depth;
	document.size_order.cid.value = cid;

	document.subCategoryform.cid.value = cid;
	document.subCategoryform.sub_depth.value = eval(depth+1);


	document.getElementById("calcufrm").src='calcurate_size.php?cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
}


function UrlCopy(url) {
	 // window.clipboardData.setData('Text', url);
	 // alert('['+url+']\n'+language_data['laundry.js']['D'][language]);
	 //선택한 주소가 클립보드에 복사되었습니다.!
    var copyUrl = $('#copyUri');
    copyUrl.select();
    document.execCommand('Copy');
    alert('url이 복사되었습니다.');

}

function order_up(frm){
	frm.mode.value = "up";
	
	if (frm.this_depth.value == 1){
		frm.submit();
	} else {
		alert("중분류 카테고리만 순서변경이 가능합니다.");
		//'상품카테고리를 선택해주세요'
		return false;
	}
	
	/*if (frm.this_depth.value.length < 1){
		alert("중분류를 선택해주세요");
		return false;
	}

	frm.submit();*/
}

function order_down(frm){
	frm.mode.value = "down";

	if (frm.this_depth.value == 1){
		frm.submit();
	} else {
		alert("중분류 카테고리만 순서변경이 가능합니다.");
		//'상품카테고리를 선택해주세요'
		return false;
	}

	/*if (frm.this_depth.value.length < 1){
		alert("중분류를 선택해주세요");
		return false;
	}

	frm.submit();*/
}

function SelectedAll(jquery_obj, selected){
	$(jquery_obj).each(function(){
		$(this).attr('selected', selected);
	});
}

function MoveSelectBox(type,level_ix){
	if(type == 'ADD'){
		if(level_ix == '4'){
			$('#participation option:selected').each(function(){
				$('#selected').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('#participation option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
			//$('#participation option:eq('+$('#participation option:selected').val()+')').html()
		}else if(level_ix == '5'){
			$('#participation_1 option:selected').each(function(){
				$('#selected_1').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('#participation_1 option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
			//$('#participation option:eq('+$('#participation option:selected').val()+')').html()
		}else if(level_ix == '6'){
			$('#participation_2 option:selected').each(function(){
				$('#selected_2').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('#participation_2 option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
			//$('#participation option:eq('+$('#participation option:selected').val()+')').html()
		}
	}else{
		if(level_ix == '4'){
			$('#selected option:selected').each(function(){
				$('#participation').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('#selected option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
		}else if(level_ix == '5'){
			$('#selected_1 option:selected').each(function(){
				$('#participation_1').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('#selected_1 option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
		}else if(level_ix == '6'){
			$('#selected_2 option:selected').each(function(){
				$('#participation_2').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('#selected_2 option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
		}
	}

	//$('#participation_total').html($('#participation option').size());
	//$('#selected_total').html($('#selected option').size());

}

function CateGoryMoveSelectBox(type,level_ix){

	if(type == 'ADD'){
		if(level_ix == '4'){
			$('#participation option:selected').each(function(){
				$('#selected').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('#participation option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
			//$('#participation option:eq('+$('#participation option:selected').val()+')').html()
		}else if(level_ix == '5'){
			$('#participation_1 option:selected').each(function(){
				$('#selected_1').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('#participation_1 option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
			//$('#participation option:eq('+$('#participation option:selected').val()+')').html()
		}else if(level_ix == '6'){
			$('#participation_2 option:selected').each(function(){
				$('#selected_2').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('#participation_2 option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
			//$('#participation option:eq('+$('#participation option:selected').val()+')').html()
		}
	}else{
		if(level_ix == '4'){
			$('#selected option:selected').each(function(){
				$('#participation').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('#selected option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
		}else if(level_ix == '5'){
			$('#selected_1 option:selected').each(function(){
				$('#participation_1').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('#selected_1 option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
		}else if(level_ix == '6'){
			$('#selected_2 option:selected').each(function(){
				$('#participation_2').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('#selected_2 option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
		}
	}

	//$('#participation_total').html($('#participation option').size());
	//$('#selected_total').html($('#selected option').size());

}


function showTabContents(vid, tab_id){
	var area = new Array('edit_category','add_subcategory'); 
	var tab = new Array('tab_01','tab_02'); 

	for(var i=0; i<area.length; ++i){

		if(area[i]==vid){

			document.getElementById(vid).style.display = 'block';
			//document.getElementById(tab_id).className = 'on';
			if(window.addEventListener) { // 호환성 kbk
				document.getElementById(tab_id).setAttribute("class","on");
			} else {
				document.getElementById(tab_id).className = 'on';
			}
		}else{

			document.getElementById(area[i]).style.display = 'none';
			//document.getElementById(tab[i]).className = '';
			if(window.addEventListener) { // 호환성 kbk
				document.getElementById(tab[i]).setAttribute("class","");
			} else {
				document.getElementById(tab[i]).className = '';
			}
		}
	}

}

function change_display_type(type){

	if(!type){
		return false;
	}

	var display_name;
	var good_cnt_x;
	if(type == '0'){
		display_name = '갤러리형 5열';
		good_cnt_x = '5';
	}else if(type == '1'){
		display_name = '갤러리형 4열';
		good_cnt_x = '4';
	}else if(type == '2'){
		display_name = '갤러리형 3열';
		good_cnt_x = '3';
	}else if(type == '4'){
		display_name = '갤러리형 1.6열';
		good_cnt_x = '8';
	}else if(type == '5'){
		display_name = '갤러리형 크로스';
		good_cnt_x = '4';
	}else if(type == '6'){
		display_name = '갤러리형 2.4열';
		good_cnt_x = '6';
	}

	$('#display_text').html(display_name);
	$('#good_cnt_x').val(good_cnt_x);
	$('#good_cnt_y').val('');
	$('#goods_max_cnt').html('0');
}


function get_goods_max(){
	var good_cnt_y = $('#good_cnt_y').val();
	var good_cnt_x = $('#good_cnt_x').val();

	if(good_cnt_x.length == '0'){	// 선택을 안하고 상품행을 입력햇을경우 자동으로 갤러리형 5열로 지정
		$('#display_text').html('갤러리형 5열');
		$('#good_cnt_x').val('5');
		$('#display_type_0').attr('checked',true);
		good_cnt_x = '5';
	}

	var good_max_cnt = parseInt(good_cnt_y) * parseInt(good_cnt_x);

	$('#goods_max').val(good_max_cnt);
	$('#goods_max_cnt').html(good_max_cnt);

}

/*
카테고리 할인율 설정 스크립트 시작 2014-04-20 이학봉
*/
function setCategoryDiscount(cname,cid,depth, category_display_type,category_use,category_access,category_code,cname_on,is_adult,is_layout_apply)
{

	cname = cname.replace("&quot;","\"");
	cname = cname.replace("&#39;","'");

	cname_on = cname_on.replace("&quot;","\"");
	cname_on = cname_on.replace("&#39;","'");

	$('input[name=this_category]').val(cname);
	document.addCategoryDiscount.cid.value = cid;
	document.addCategoryDiscount.this_depth.value = depth;

	document.size_order.this_depth.value = depth;
	document.size_order.cid.value = cid;

	$('#category_name').html(cname);
	document.getElementById("calcufrm").src='discount_calcurate.php?cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
	//document.getElementById("act").src='category.save.php?mode=infoupdate_discount&cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...

	if(depth == '0'){

		$('#is_use_li_2').css('display','none');
		$('#is_use_li_3').css('display','');
	}else{

		$('#is_use_li_2').css('display','');
		$('#is_use_li_3').css('display','');
	}

	//다시 넣기 위해서 기존 TR내역을 하나만 남기고 삭제하기 시작
	var option_obj = $('#group_discount_table');
	option_obj.find('tr[depth=1]:not(:first)').each(function(){
		//group_length = $(this).find('#group_length').val();
		$(this).remove();
	});
	//다시 넣기 위해서 기존 TR내역을 하나만 남기고 삭제하기 끝

	$.ajax({
	    url : './category_discount.act.php',
	    type : 'GET',
	    data : {cid:cid,
				depth:depth,
				mode:'category_discount',
				access_type : 'U'
				},
	    dataType: 'json',
	    error: function(data,error){// 실패시 실행함수
			setTimeout($.unblockUI, 500);
	        alert(error);
		},
		beforeSend: function(){
			$.blockUI.defaults.css = {};
			$.blockUI({ message: $('#loading'), css: {left:'40%', top:'40%',  width: '10px' , height: '10px' ,padding:  '10px'} });
			//alert('test');
		},
	    success: function(args){
				if(args != null){
					console.log(args);
					$.each(args, function(index, entry){
						console.log("index : " + index + ", entry : " + entry);
						if(index == 'is_use'){
							//alert(entry);
							$('input[name=is_use][value='+entry+']').attr('checked',true);
							if(entry == '3' || entry == '1'){					//개별설정일 경우에만 하단 그룹 테이블 생성
								option_obj.css('display','');
							}else{
								option_obj.css('display','none');
							}
						}

						if(index == 'set_group'){

							$.each(entry, function(set_group, detail){
								$('#selected_'+set_group).empty();					//다시 클릭시 기존값 삭제후 새로 넣어주기
								if(set_group > '1'){
									AddMultTable('group_discount_table', set_group);	//TR 내역 복사하기
								}
								$.each(detail, function(key, data_array){			//select되엇던group 값 넣어주기
									$('#selected_'+set_group).append("<option value='"+data_array.gp_ix+"' selected>"+data_array.gp_name+"</option>");
								});
							});
							$('tr[depth=1]:not(:first)').each(function (){
								var length = $(this).find('select[id^=selected_]').length;
								var value = $(this).find('select[id^=selected_]').val();
									if(value == null){
										$(this).remove();	//TR내역이 하나더 생성되어서 삭제하기
									}
							});
							//$('tr[depth=1]:last').remove();		//TR내역이 하나더 생성되어서 삭제하기
						}

						if(index == 'basic_group_discount'){	//추가되고 나머지 그룹값 불러오기

							$('#basic_group_discount').empty();
							$.each(entry, function(set_group, detail){
								$('#basic_group_discount').append("<option value='"+detail.gp_ix+"'>"+detail.gp_name+"</option>");
							});
							Basic_gruop_info();	//기본정보에 그룹값 불러오기
						}

						if(index == 'discount'){			//도매,소매 수술 설정
							$.each(entry, function(set_group, detail){
								console.log(set_group + "  " + detail.wholesale_dc_rate + " " + detail.dc_rate);
								$('#wholesale_dc_rate_'+set_group).val(detail.wholesale_dc_rate);
								$('#dc_rate_'+set_group).val(detail.dc_rate);
								//alert(set_group+' '+detail.wholesale_commission+' '+detail.commission);
							});
						}

						if(index == 'nodata_basic_group'){		//데이타가 없을경우 기본 그룹값 노출하기

							$('#basic_group_discount').empty();
							$('#selected_1').empty();
							$.each(entry, function(set_group, detail){
								$('#basic_group_discount').append("<option value='"+detail.gp_ix+"'>"+detail.gp_name+"</option>");
							});
							Basic_gruop_info();	//기본정보에 그룹값 불러오기
							$('input[id^=wholesale_dc_rate_').val('0');
							$('input[id^=dc_rate_').val('0');
							//$('input[name=is_use][value=1]').attr('checked',true);
						}
						//alert(index);
					});
				}else{

				alert('데이타가 없습니다.');
				}
				setTimeout($.unblockUI, 500);
        	}
    	});

}

function AddMultTable(target_id, set_group){	//추가

	var table_target_obj = $('table[id='+target_id+'] tr');
	var total_rows = table_target_obj.length;
	var option_obj = $('#'+target_id);

	/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 항상 appendTo 윗부분에 잇어야함*/
	var group_length = 1;
	option_obj.find('tr[depth=1]:last').each(function(){
		 group_length = $(this).find('#group_length').val();
	});
	option_rows_total = parseInt(group_length) + 1;
	/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */

	var newRow = option_obj.find('tr[depth=1]:last').clone(true).wrapAll("<table/>").appendTo("#"+option_obj.attr("id"));  //

	if(set_group){
		option_rows_total = set_group;
	}

	newRow.find("#add_table_img").css("display","none");	//추가버튼 숨김
	newRow.find("select[name^=group_list]").empty('');		//추가값 비우기
	newRow.find("input[name^=wholesale_dc_rate]").val('');	//도매수수료
	newRow.find("input[name^=dc_rate]").val('');				//소매수수료
	newRow.find("input[id^=group_length]").val(option_rows_total);	//set_group 라인수
	put_No(target_id);		//그룹설정 번호

	newRow.find("input[id^=wholesale_dc_rate]").attr("name","group_discount["+option_rows_total+"][wholesale_dc_rate]");
	newRow.find("input[id^=dc_rate]").attr("name","group_discount["+option_rows_total+"][dc_rate]");

	//### 2016.03.22
	newRow.find("input[id^=wholesale_dc_rate]").attr("id","wholesale_dc_rate_"+option_rows_total);
	newRow.find("input[id^=dc_rate]").attr("id","dc_rate_"+option_rows_total);

	var add_html = "<img src='../images/icon/pop_plus_btn.gif' style='cursor:pointer;' alt='추가' title='추가' onclick=\"CateGoryMoveSelectBox_discount('ADD','"+option_rows_total+"');\"/>";
	newRow.find("#add_gruop_img").html(add_html);		//추가버튼 이미지

	var del_html = "<img src='../images/icon/pop_del_btn.gif' alt='삭제' style='cursor:pointer;' title='삭제' onclick=\"javascript:CateGoryMoveSelectBox_discount('REMOVE','"+option_rows_total+"');\"/>";
	newRow.find("#del_gruop_img").html(del_html);		//삭제버튼 이미지

	newRow.find("select[id^=participation_]").attr("name","group_discount["+option_rows_total+"][vip_delete][]");
	newRow.find("select[id^=participation_]").attr("id","participation_"+option_rows_total);
	newRow.find("select[id^=participation_]").attr("class","participation_"+option_rows_total);

	newRow.find("select[id^=selected_]").attr("name","group_discount["+option_rows_total+"][group_list][]");
	newRow.find("select[id^=selected_]").attr("id","selected_"+option_rows_total);
	newRow.find("select[id^=selected_]").empty();

	newRow.find("input[id^=wholesale_commission_]").attr("id","wholesale_commission_"+option_rows_total);
	newRow.find("input[id^=commission_]").attr("id","commission_"+option_rows_total);

}

function DelMultTable(target_id,seq){		//삭제

	$("#del_discount_table_tr").live("click",function() {

		if($("tr[id^=group_discount_tr]").size() > 1){
			var NowRow = $(this).parents("#group_discount_tr");
			var level_ix = NowRow.find('#group_length').val();

			CateGoryMoveSelectBox_discount('DEL',level_ix);	//삭제시 해당 선택된 그룹 원상복귀

			$(this).parents("#group_discount_tr").remove();
			put_No(target_id);
		}else{
			//alert("더 이상 삭제할수 없습니다.");
		}
	});
}

function put_No(target_id){	//그룹설정 번호

	var table_target_obj = $('table[id='+target_id+'] tr[depth=1]');
	var total_rows = table_target_obj.length;
	var option_obj = $('#'+target_id);

	table_target_obj.each(function(i,value){
		var no = parseInt(i) + 1;
		$(this).find('#set_group_text').html(no);
	});
}

function CateGoryMoveSelectBox_discount(type,level_ix){		//그룹 추가/삭제

	if(type == 'ADD'){
		var check_cid = $('input[name=cid]').val();	//추가전 카테고리를 반든시 선택해야함
		if(!check_cid){
			alert('수정할 카테고리를 선택해 주세요.');
			return false;
		}

		$('#participation_'+level_ix+' option:selected').each(function(){
			$('#selected_'+level_ix).append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
			var selected_value = $(this).val();
			$('#basic_group_discount option').each(function(){
				if($(this).val() == selected_value){
					$(this).remove();
				}
			});
		});
		Basic_gruop_info();
	}else{
		$('#selected_'+level_ix+' option:selected').each(function(){
			$('#basic_group_discount').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
			var selected_value = $(this).val();
			$('#selected_'+level_ix+' option').each(function(){
				if($(this).val() == selected_value){
					$(this).remove();
				}
			});
		});
		Basic_gruop_info();
	}
}


function Basic_gruop_info(){		//기본 그룹정보 (그룹정보를 기보정보에서 삭제,추가하면서 이정보를 불러옴)

	$('select[id^=participation_]').empty();
	$('#basic_group_discount option').each(function(){
		var value =$(this).val();
		var text = $(this).text();
		$('select[id^=participation_]').each(function (){
			$(this).append('<option value='+value+'>'+text+'</option>');
		});
	});

}

function Initialize_discount(){		//카테고리 수수료 초기화(shop_category_discount)

	var value = confirm("초기화시 모든 데이타가 삭제됩니다.");

	if(value == true){
			$.ajax({
			    url : './category_discount.act.php',
			    type : 'POST',
			    data : {
						mode:'Initialize_discount'
						},
			    dataType: 'html',
			    error: function(data,error){// 실패시 실행함수
			        alert(error);},
			    success: function(args){
					if(args == 'Y'){
						alert('할인율 초기화가 완료되었습니다.');
						document.location.reload();
					}else{
						alert('초기화 권한이 없습니다.');
					}
		        }
		    });
	}else{
		return false;
	}

}

$(document).ready(function (){

	$('input[name^= is_use]').click(function (){
		var value= $(this).val();

		if(value == '3'){
			$('#group_discount_table').css('display','');
		}else{
			$('#group_discount_table').css('display','none');
		}

	});

});

/*
카테고리 할인율 설정 스크립트 끝 2014-04-20 이학봉
*/


/*
카테고리 할인율 설정 스크립트 시작 2014-04-20 이학봉
*/
function setCategoryMandatory(cname,cid,depth, category_display_type,category_use,category_access,category_code,cname_on,is_adult,is_layout_apply,mandatory_type)
{

	cname = cname.replace("&quot;","\"");
	cname = cname.replace("&#39;","'");

	cname_on = cname_on.replace("&quot;","\"");
	cname_on = cname_on.replace("&#39;","'");

	$('input[name=this_category]').val(cname);
	document.addCategoryDiscount.cid.value = cid;
	document.addCategoryDiscount.this_depth.value = depth;

	document.size_order.this_depth.value = depth;
	document.size_order.cid.value = cid;

	$('#category_name').html(cname);
	document.getElementById("calcufrm").src='discount_calcurate.php?cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
	//document.getElementById("act").src='category.save.php?mode=infoupdate_discount&cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...

	$.ajax({
	    url : './category_mandatory.save.php',
	    type : 'GET',
	    data : {mandatory_type:mandatory_type,
				cid:cid,
				mode:'category_mandatory',
				access_type : 'U'
				},
	    dataType: 'json',
	    error: function(data,error){// 실패시 실행함수
			setTimeout($.unblockUI, 500);
	        alert(error);
		},
		beforeSend: function(){
			$.blockUI.defaults.css = {};
			$.blockUI({ message: $('#loading'), css: {left:'40%', top:'40%',  width: '10px' , height: '10px' ,padding:  '10px'} });
			//alert('test');
		},
	    success: function(args){

				if(args != null){
					$('#mi_ix').val(args.mi_code);
					$('#mandatory_name').val(args.mandatory_name);
				}else{

				//alert('데이타가 없습니다.');
				}
				setTimeout($.unblockUI, 500);
        	}
    });

}
