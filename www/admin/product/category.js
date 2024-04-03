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
	//alert(frm);

	if (frm.this_category.value.length < 1){
		alert(language_data['category.js']['A'][language]);
		//'수정/삭제 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;
	}

	//alert(iView.document.body.innerHTML);

	//frm.category_top_view.value = iView.document.body.innerHTML;
	//$('input[name=category_top_view]').val(document.getElementById("iView").contentWindow.document.body.innerHTML); // 호환성 2011-04-07 kbk

	if(CheckFormValue(frm)){
		frm.mode.value = vMode;
		frm.submit();
	}
}

function thisCategorySave(frm,vMode)
{
	//alert(frm);

	if (frm.this_category.value.length < 1){
		alert('수정/삭제 하시고자 하는 상품카테고리를 선택해 주세요');
		//'수정/삭제 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;
	}
	if(CheckFormValue(frm)){
		frm.mode.value = vMode;
		frm.submit();
	}


}

function SubCategorySave(frm,vMode)
{

	if (frm.sub_cid.value.length != 15){
		alert(language_data['category.js']['B'][language]);
		//'추가 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;
	}

	if (frm.cid.value.length != 15){
		alert(language_data['category.js']['B'][language]);
		//'추가 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;
	}



	if (frm.sub_category.value.length < 1){
		alert(language_data['category.js']['B'][language]);
		//'추가 하시고자 하는 상품카테고리를 입력해 주세요'
		return false;
	}

	if (frm.sub_depth.value >= 5){
		alert(language_data['category.js']['C'][language]);
		//'카테고리구성은 4단계까지만 가능합니다.'
		return false;
	}

	if(CheckFormValue(frm)){
		frm.mode.value = vMode;
		frm.submit();
	}
}

function setCategory(cname,cid,depth, category_display_type,category_use,category_access,category_code,cname_on,is_adult,is_layout_apply,is_layout_emphasis,category_sort)
{
	cname = cname.replace("&quot;","\"");
	cname = cname.replace("&#39;","'");

	cname_on = cname_on.replace("&quot;","\"");
	cname_on = cname_on.replace("&#39;","'");

	document.thisCategoryform.this_category.value = cname;
	document.thisCategoryform.category_code.value = category_code;	//분류코드
	document.thisCategoryform.cid.value = cid;
	document.thisCategoryform.this_depth.value = depth;
	document.thisCategoryform.category_sort.value = category_sort;	//분류코드

	if(category_display_type == "T"){
		document.design_subcategory.category_display_type[0].checked = true;
		document.design_subcategory.category_display_type[1].checked = false;
	}else{
		document.design_subcategory.category_display_type[0].checked = false;
		document.design_subcategory.category_display_type[1].checked = true;
	}
	document.design_subcategory.ch_category_img.checked = false;
	document.design_subcategory.ch_leftcategory_img.checked = false;
	document.design_subcategory.ch_rightcategory_img.checked = false;
	document.design_subcategory.ch_sub_img.checked = false;
	document.design_subcategory.cid.value = cid;
	document.design_subcategory.this_category.value = cname;
	document.design_subcategory.this_category_on.value = cname_on;

	if(category_use == "1"){	//사용
		$('#category_use_id').attr('checked',true);
	}else if(category_use == "0"){	//미사용
		$('#category_use_id_0').attr('checked',true);
	}else if (category_use == "2"){	//숨김카테고리
		$('#category_use_id_2').attr('checked',true);
	}else{
		$('#category_use_id_0').attr('checked',true);
	}

	if(is_adult == "1"){	//19금사용
		$('#is_adult_1').attr('checked',true);
	}else if(is_adult == "0"){	//미사용
		$('#is_adult_0').attr('checked',true);
	}else{
		$('#is_adult_0').attr('checked',true);
	}

	if(is_layout_apply == "Y"){	//카테고리 관리적용
		$('#is_layout_apply_y').attr('checked',true);
	}else if(is_adult == "N"){	//미사용
		$('#is_layout_apply_n').attr('checked',true);
	}else{
		$('#is_layout_apply_n').attr('checked',true);
	}

	if(is_layout_emphasis == "Y"){	//카테고리 강조설정
		$('#is_layout_emphasis_y').attr('checked',true);
	}else{
		$('#is_layout_emphasis_n').attr('checked',true);
	}

	if(category_access == "D"){
		$('#category_access_d').attr('checked',true);
		$('#category_access_d').attr('disabled',false);
		$('#category_access_m').attr('disabled',false);
		$('#category_access_g').attr('disabled',false);
		$('#category_access_md').attr('disabled',false);
		$('#selected').empty();
		$('#selected_1').empty();
	}else if(category_access == "M"){
		$('#category_access_d').attr('disabled','disabled');
		$('#category_access_m').attr('disabled',false);
		$('#category_access_g').attr('disabled',false);
		$('#category_access_md').attr('disabled',false);
		$('#category_access_m').attr('checked',true);
		$('#selected').empty();
		$('#selected_1').empty();
	}else if(category_access == "G"){
		$('#category_access_d').attr('disabled','disabled');
		$('#category_access_m').attr('disabled','disabled');
		$('#category_access_g').attr('disabled',false);
		$('#category_access_md').attr('disabled',false);
		$('#category_access_g').attr('checked',true);

		$.ajax({
		    url : './category.save.php',
		    type : 'POST',
		    data : {cid:cid,
					depth:depth,
					mode:'category_access',
					access_type : 'G'
					},
		    dataType: 'json',
		    error: function(data,error){// 실패시 실행함수
		        alert(error);},
		    success: function(args){

				if(args != null){
					$('#selected').empty();
					$.each(args, function(index, entry){
						$('#selected').append("<option value='"+index+"' selected>"+entry+"</option>");
					});
				}
        	}
    	});

		$('#selected_1').empty();

	}else if(category_access == "U"){

		$('#category_access_d').attr('disabled','disabled');
		$('#category_access_m').attr('disabled','disabled');
		$('#category_access_g').attr('disabled','disabled');
		$('#group_delete').attr('disabled','disabled');
		$('#group_list').attr('disabled','disabled');
		$('#category_access_md').attr('checked',true);
		$('#selected').empty();

		$.ajax({
		    url : './category.save.php',
		    type : 'POST',
		    data : {cid:cid,
					depth:depth,
					mode:'category_access',
					access_type : 'U'
					},
		    dataType: 'json',
		    error: function(data,error){// 실패시 실행함수
		        alert(error);},
		    success: function(args){
				if(args != null){
					$('#selected_1').empty();
					$.each(args, function(index, entry){
						$('#selected_1').append("<option value='"+index+"' selected>"+entry+"</option>");
					});
				}
        	}
    	});

	}else{

		$('#category_access_d').attr('checked',true);
		$('#category_access_d').attr('disabled',false);
		$('#category_access_m').attr('disabled',false);
		$('#category_access_g').attr('disabled',false);
		$('#group_delete').attr('disabled',false);
		$('#group_list').attr('disabled',false);
		$('#selected').empty();
		$('#selected_1').empty();
	}
	var front_url = $('#front_url').val();
	document.getElementById("category_display_link").innerHTML = "<img src='/admin/image/url_new.gif' style='cursor:hand;' align=absmiddle onclick=\"UrlCopy();\"> &nbsp;<input type='text' class='textbox' style='width:400px;border:0;' readonly value='"+front_url+"/shop/goodsList/"+cid+"' id='copyUri' > ";
	document.category_order.this_depth.value = depth;
	document.category_order.cid.value = cid;

	document.subCategoryform.cid.value = cid;
	document.subCategoryform.sub_depth.value = eval(depth+1);

	document.AddPersonForm.this_category.value = cname;
	document.AddPersonForm.cid.value = cid;

	//document.add_field.cid.value = cid;
	document.getElementById("category_name").innerHTML = cname;
	document.add_field.cid.value = cid;

/*
	if (depth+1 >=4){
		document.getElementById("add_subcategory").style.display = "none";
	}else{
		document.getElementById("add_subcategory").style.display = "block";
	}
*/
	//dynamic.src = 'addfield.load.php?mode=view&cid=' + cid +'&form=add_field';
	//document.frames["calcufrm"].location.href='calcurate.php?cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
	//document.frames["act"].location.href='category.save.php?mode=infoupdate&cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
	document.getElementById("calcufrm").src='calcurate.php?cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
	document.getElementById("act").src='category.save.php?mode=infoupdate&cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
//alert(cname);

	$.ajax({
	    url : './category.save.php',
	    type : 'POST',
	    data : {cid:cid,
				depth:depth,
				mode:'product_cnt'
				},
	    dataType: 'json',
	    error: function(data,error){// 실패시 실행함수
	        alert(error);},
	    success: function(args){
			if(args != null){
				$('#product_cnt').html(args.product_cnt+' 개');
				$('#product_total_cnt').html(args.product_total_cnt+' 개');
			}
        }
    });

	$.ajax({
	    url : './category.save.php',
	    type : 'POST',
	    data : {cid:cid,
				depth:depth,
				mode:'category_design'
				},
	    dataType: 'json',
	    error: function(data,error){// 실패시 실행함수
	        alert(error);},
	    success: function(args){
			if(args != null){
				$.each(args.use, function(index, entry){
					if(entry == '1'){
						$('#'+index).attr('checked',true);
					}else if(entry == '0'){
						$('#'+index).attr('checked',false);
					}
				});

				$.each(args.cname_style, function(index, entry){
					$('#'+index).val(entry);
				});

				$.each(args.cname_on_style, function(index, entry){
					$('#'+index).val(entry);
				});

				$.each(args.product_border, function(index, entry){
					$('#'+index).val(entry);
				});

				$.each(args.pname_style, function(index, entry){
					$('#'+index).val(entry);
				});

				$.each(args.product_info, function(index, entry){
					$('#'+index).val(entry);
				});

				$.each(args.product_listprice, function(index, entry){
					$('#'+index).val(entry);
				});

				$.each(args.product_sellprice, function(index, entry){
					$('#'+index).val(entry);
				});

				$.each(args.basic, function(index, entry){
					if(index == 'order_type'){
						$('#order_type_'+entry).attr('checked',true);
					}else if(index == 'order_type_date'){
						$('#order_type_date').val(entry);
					}else if(index == 'display_type'){
						$('#display_type_'+entry).attr('checked',true);
						change_display_type(entry);
					}else if(index == 'goods_max'){
						var type_cnt = $('#good_cnt_x').val();
						$('#good_cnt_y').val(parseInt(entry)/parseInt(type_cnt));
						$('#goods_max_cnt').html(entry);
					}
				});

			}
        }
    });

	$.ajax({
	    url : './category.save.php',
	    type : 'POST',
	    data : {cid:cid,
				depth:depth,
				mode:'add_person'
				},
	    dataType: 'json',
	    error: function(data,error){// 실패시 실행함수
				alert(error);},
	    success: function(args){
			var md_tbody = $('#objMd tbody');
			var de_tbody = $('#objDepartment tbody');

			$('#objMd tbody tr').remove();
			$('#objDepartment tbody tr').remove();

			if(args.MD) {
				$.each(args.MD, function(index, entry){
					var add_html = '<tr style="height:26px;" id="row_'+index+'"><td><input type=hidden name=md_code[] id="person_'+index+'" value="'+index+'"></td><td>'+entry+'</td><td><a href=\"javascript:void(0)\" onClick="person_del(\''+index+'\')"><img src=\"../images/korea/btc_del.gif\" border=0></a></td></tr>';
					md_tbody.append(add_html);
				});
			}

			if(args.DE) {
				$.each(args.DE, function(index, entry){
					var add_html = "<tr style='height:26px;' id='department_row_"+index+"'><td><input type=hidden name=department_list[] id='department_"+index+"' value='"+index+"'></td><td>"+entry+"</td><td><a href='javascript:void(0)' onClick='department_del("+index+")'><img src='../images/korea/btc_del.gif' border=0></a></td></tr>";
					de_tbody.append(add_html);
				});
			}

			$.each(args.auth, function(index, entry){
				if(entry == '1'){
					$('#md_use_1').attr('checked',true);
					$('#md_use_0').attr('checked',false);
				}else{
					$('#md_use_1').attr('checked',false);
					$('#md_use_0').attr('checked',true);
				}
			});

        }
    });


	$.ajax({
	    url : './category.save.php',
	    type : 'POST',
	    data : {cid:cid,
				depth:depth,
				mode:'get_add_fields'
				},
	    dataType: 'json',
	    error: function(data,error){// 실패시 실행함수
				alert(error);},
	    success: function(datas){
			//alert(datas);
			
			var add_field_table = $('table#add_field_table');


			$.each(datas, function(i, data){ 
				//alert(data.f_ename);
				add_field_table.find("[name=etc"+(i+1)+"_ename]").val(data.f_ename);
				add_field_table.find("[name=etc"+(i+1)+"]").val(data.f_name);
				add_field_table.find("[name=etc"+(i+1)+"_type]").val(data.f_type);
				add_field_table.find("[name=etc"+(i+1)+"_value]").val(data.f_value);
				//alert(data.f_search);
				if(data.f_search){
					add_field_table.find("[name=etc"+(i+1)+"_search]").attr("checked","checked");
				}
				

					//	$('select[name='+target+']').append(\"<option value='\"+data.cid+\"'>\"+data.cname+\"</option>\");
			});  

        }
    });


}


function UrlCopy(url) {
	 // window.clipboardData.setData('Text', url);
	 // alert('['+url+']\n'+language_data['category.js']['D'][language]);
	 //선택한 주소가 클립보드에 복사되었습니다.!
    var copyUrl = $('#copyUri');
    copyUrl.select();
    document.execCommand('Copy');
    alert('url이 복사되었습니다.');

}

function order_up(frm){
	frm.mode.value = "up";
	if (frm.this_depth.value.length < 1){
		alert(language_data['category.js']['E'][language]);
		//'상품카테고리를 선택해주세요'
		return false;
	}

	frm.submit();
}

function order_down(frm){
	//alert(frm.view);


	frm.mode.value = "down";
	if (frm.this_depth.value.length < 1){
		alert(language_data['category.js']['E'][language]);
		//'상품카테고리를 선택해주세요'
		return false;
	}

	frm.submit();
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
	var area = new Array('edit_category','add_subcategory','add_person','input_design_subcategory','input_addfield'); /*,'input_addfield'*/
	var tab = new Array('tab_01','tab_02','tab_04','tab_03','tab_05'); /*,'tab_03'*/

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

	document.category_order.this_depth.value = depth;
	document.category_order.cid.value = cid;

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

	document.category_order.this_depth.value = depth;
	document.category_order.cid.value = cid;

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
