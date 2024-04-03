

function MandatoryChange(select) {

	mandatory_1 = $("#mandatory_select_1");
	mandatory_2 = $("#mandatory_select_2");
	mandatory_3 = $("#mandatory_select_3");

	if(select==1){
		if(mandatory_1.val()==""){
			mandatory_2.hide();
			mandatory_3.hide();
			parameter_1 = "";
			parameter_2 = "";
			parameter_3 = "";

			$("#mandatory_info_pmi_ix_a").val("");
			$("#mandatory_info_pmi_code_a").val("");
			$("#mandatory_info_title_a").val("");
			$("#mandatory_info_desc_a").val("");
			$("#mandatory_info_desc_a").attr('validation','false');
			$("#mandatory_info_pmi_ix_b").val("");
			$("#mandatory_info_pmi_code_b").val("");
			$("#mandatory_info_title_b").val("");
			$("#mandatory_info_desc_b").val("");
			$("#mandatory_info_desc_b").attr('validation','false');
			$(".mandatory_info").remove();

		}else{
			mandatory_2.show();
			mandatory_3.hide();
			parameter_1 = mandatory_1.val();
			mandatory_2.val("1");
			parameter_2 = mandatory_2.val();
			mandatory_3.val("1");
			parameter_3 = mandatory_3.val();
		}
	}else if(select==2){
		if(mandatory_2.val()==2){
			mandatory_3.show();
		}else{
			mandatory_3.hide();
			mandatory_3.val("1");
		}
		parameter_1 = mandatory_1.val();
		parameter_2 = mandatory_2.val();
		parameter_3 = mandatory_3.val();
	}else{
		parameter_1 = mandatory_1.val();
		parameter_2 = mandatory_2.val();
		parameter_3 = mandatory_3.val();
	}

	if(parameter_1 != ""){
		$.ajax({ 
			type: 'GET', 
			data: {'act':'get_mandatory_info','parameter_1':parameter_1,'parameter_2':parameter_2,'parameter_3':parameter_3},
			url: '../product/product_mandatory.act.php',
			dataType: 'json', 
			async: false,
			beforeSend: function(){ 
				//alert(1);
			}, 
			success: function(mandatory_infos){
				//alert(mandatory_infos);
				if(mandatory_infos != null){

					$("#mandatory_info_pmi_ix_a").val("");
					$("#mandatory_info_pmi_code_a").val("");
					$("#mandatory_info_title_a").val("");
					$("#mandatory_info_desc_a").val("");
					$("#mandatory_info_desc_a").attr('validation','false');
					$("#mandatory_info_pmi_ix_b").val("");
					$("#mandatory_info_pmi_code_b").val("");
					$("#mandatory_info_title_b").val("");
					$("#mandatory_info_desc_b").val("");
					$("#mandatory_info_desc_b").attr('validation','false');

					$(".mandatory_info").remove();

					$.each(mandatory_infos, function(i,mandatory_info){ 
						if(i%2==0 && i > 1)	CopyMandatory('mandatory_info');
						$("input[name='mandatory_info["+i+"][pmi_code]']").val(mandatory_info.code);
						$("input[name='mandatory_info["+i+"][pmi_title]']").val(mandatory_info.title);
						$("input[name='mandatory_info["+i+"][pmi_desc]']").val(mandatory_info.desc);
						$("input[name='mandatory_info["+i+"][pmi_desc]']").attr('validation',mandatory_info.validation);
						$(".mandatory_info_comment_"+i+"").text(mandatory_info.comment);

						if(mandatory_info.type=='date'){
							nowdate = new Date();
							var s_y = nowdate.getFullYear();

							var comment = "";
							comment += "<select class='mandatory_year_"+i+"' onchange='MandatoryDateSelect("+i+")'>";
							for(y=s_y;y > s_y-10;y--){
								comment  += "<option value='"+y+"'>"+y+"</option>";
							}
							comment += "</select>";
							comment += "<select class='mandatory_month_"+i+"' onchange='MandatoryDateSelect("+i+")'>";
							for(m=1;m < 13;m++){
								comment  += "<option value='"+m+"'>"+m+"</option>";
							}
							comment += "</select>";
							$(".mandatory_info_comment_"+i+"").html(comment);
						}else{
							$(".mandatory_info_comment_"+i+"").text(mandatory_info.comment);
						}

						if(mandatory_infos.length%2==1 && mandatory_infos.length-1==i){
                            $("input[name='mandatory_info["+(i+1)+"][pmi_title]']").attr('validation','false');
                            $("input[name='mandatory_info["+(i+1)+"][pmi_desc]']").attr('validation','false');
						}
					});
					
				}
			}
		}); 
	}
}

function MandatoryChangeGlobal(select) {

    mandatory_1 = $("#mandatory_select_1_global");
    mandatory_2 = $("#mandatory_select_2_global");
    mandatory_3 = $("#mandatory_select_3_global");

    if(select==1){
        if(mandatory_1.val()==""){
           // mandatory_2.hide();
            mandatory_3.hide();
            parameter_1 = "";
            parameter_2 = "";
            parameter_3 = "";

            $("#mandatory_info_pmi_ix_a_global").val("");
            $("#mandatory_info_pmi_code_a_global").val("");
            $("#mandatory_info_title_a_global").val("");
            $("#mandatory_info_desc_a_global").val("");
            $("#mandatory_info_desc_a_global").attr('validation','false');
            $("#mandatory_info_pmi_ix_b_global").val("");
            $("#mandatory_info_pmi_code_b_global").val("");
            $("#mandatory_info_title_b_global").val("");
            $("#mandatory_info_desc_b_global").val("");
            $("#mandatory_info_desc_b_global").attr('validation','false');
            $(".mandatory_info_global").remove();

        }else{
          //  mandatory_2.show();
            mandatory_3.hide();
            parameter_1 = mandatory_1.val();
            mandatory_2.val("1");
            parameter_2 = mandatory_2.val();
            mandatory_3.val("1");
            parameter_3 = mandatory_3.val();
        }
    }else if(select==2){
        if(mandatory_2.val()==2){
            mandatory_3.show();
        }else{
            mandatory_3.hide();
            mandatory_3.val("1");
        }
        parameter_1 = mandatory_1.val();
        parameter_2 = mandatory_2.val();
        parameter_3 = mandatory_3.val();
    }else{
        parameter_1 = mandatory_1.val();
        parameter_2 = mandatory_2.val();
        parameter_3 = mandatory_3.val();
    }

    if(parameter_1 != ""){
        $.ajax({
            type: 'GET',
            data: {'act':'get_mandatory_info','parameter_1':parameter_1,'parameter_2':parameter_2,'parameter_3':parameter_3},
            url: '../product/product_mandatory.act.php',
            dataType: 'json',
            async: false,
            beforeSend: function(){
                //alert(1);
            },
            success: function(mandatory_infos){
                //alert(mandatory_infos);
                if(mandatory_infos != null){

                    $("#mandatory_info_pmi_ix_a_global").val("");
                    $("#mandatory_info_pmi_code_a_global").val("");
                    $("#mandatory_info_title_a_global").val("");
                    $("#mandatory_info_desc_a_global").val("");
                    $("#mandatory_info_desc_a_global").attr('validation','false');
                    $("#mandatory_info_pmi_ix_b_global").val("");
                    $("#mandatory_info_pmi_code_b_global").val("");
                    $("#mandatory_info_title_b_global").val("");
                    $("#mandatory_info_desc_b_global").val("");
                    $("#mandatory_info_desc_b_global").attr('validation','false');

                    $(".mandatory_info_global").remove();

                    $.each(mandatory_infos, function(i,mandatory_info){
                        if(i%2==0 && i > 1)	CopyMandatoryGlobal('mandatory_info_global');
                        $("input[name='mandatory_info_global["+i+"][pmi_code]']").val(mandatory_info.code);
                        $("input[name='mandatory_info_global["+i+"][pmi_title]']").val(mandatory_info.title);
                        $("input[name='mandatory_info_global["+i+"][pmi_desc]']").val(mandatory_info.desc);
                        $("input[name='mandatory_info_global["+i+"][pmi_desc]']").attr('validation',mandatory_info.validation);
                        $(".mandatory_info_comment_global_"+i+"").text(mandatory_info.comment);

                        if(mandatory_info.type=='date'){
                            nowdate = new Date();
                            var s_y = nowdate.getFullYear();

                            var comment = "";
                            comment += "<select class='mandatory_year_global_"+i+"' onchange='MandatoryDateSelectGlobal("+i+")'>";
                            for(y=s_y;y > s_y-10;y--){
                                comment  += "<option value='"+y+"'>"+y+"</option>";
                            }
                            comment += "</select>";
                            comment += "<select class='mandatory_month_global_"+i+"' onchange='MandatoryDateSelectGlobal("+i+")'>";
                            for(m=1;m < 13;m++){
                                comment  += "<option value='"+m+"'>"+m+"</option>";
                            }
                            comment += "</select>";
                            $(".mandatory_info_comment_global_"+i+"").html(comment);
                        }else{
                            $(".mandatory_info_comment_global_"+i+"").text(mandatory_info.comment);
                        }

                        if(mandatory_infos.length%2==1 && mandatory_infos.length-1==i){
                            $("input[name='mandatory_info_global["+(i+1)+"][pmi_title]']").attr('validation','false');
                            $("input[name='mandatory_info_global["+(i+1)+"][pmi_desc]']").attr('validation','false');
                        }
                    });

                }
            }
        });
    }
}

function MandatoryDateSelect(i) {
    year = $(".mandatory_year_"+i+"").val();
    month = $(".mandatory_month_"+i+"").val();
    $("input[name='mandatory_info["+i+"][pmi_desc]']").val(year+"년"+month+"월");
}

function MandatoryDateSelectGlobal(i) {
    year = $(".mandatory_year_global_"+i+"").val();
    month = $(".mandatory_month_global_"+i+"").val();
    $("input[name='mandatory_info_global["+i+"][pmi_desc]']").val(year+"년"+month+"월");
}


/*
function CopyMandatory(target){
	//var option_target_obj = $('#mandatory_info_zone').find('table[id^='+target+']');

	var option_obj = $('#mandatory_info_zone');
	var option_obj_ = $('#mandatory_info_td');
    var total_rows = Number(option_obj.find('table[id^='+target+']:last').attr("mandatory_info_cnt"));

	total_rows_1 =  total_rows + 1;
	total_rows_2 =  total_rows + 2;
	total_rows_3 =  total_rows + 3;

	var newRow = option_obj.find('table[id^='+target+']:last').clone(true).wrapAll("<table/>").parent();

	newRow.find("table[id^=mandatory_info]").attr("mandatory_info_cnt",""+(total_rows_2)+"");

	newRow.find("input[id^=mandatory_info_pmi_ix_a]").attr("name","mandatory_info["+(total_rows_2)+"][pmi_ix]");
	newRow.find("input[id^=mandatory_info_pmi_ix_a]").val("");
	newRow.find("input[id^=mandatory_info_pmi_code_a]").attr("name","mandatory_info["+(total_rows_2)+"][pmi_code]");
	newRow.find("input[id^=mandatory_info_pmi_code_a]").val("");
	newRow.find("input[id^=mandatory_info_title_a]").attr("name","mandatory_info["+(total_rows_2)+"][pmi_title]");
	newRow.find("input[id^=mandatory_info_title_a]").val("");
	newRow.find("input[id^=mandatory_info_desc_a]").attr("name","mandatory_info["+(total_rows_2)+"][pmi_desc]");
	newRow.find("input[id^=mandatory_info_desc_a]").val("");

	newRow.find("div[class^=mandatory_info_comment_"+(total_rows)+"]").text("");
	newRow.find("div[class^=mandatory_info_comment_"+(total_rows)+"]").attr("class","mandatory_info_comment_"+(total_rows_2)+" small");

	newRow.find("input[id^=mandatory_info_pmi_ix_b]").attr("name","mandatory_info["+(total_rows_3)+"][pmi_ix]");
	newRow.find("input[id^=mandatory_info_pmi_ix_b]").val("");
	newRow.find("input[id^=mandatory_info_pmi_code_b]").attr("name","mandatory_info["+(total_rows_3)+"][pmi_code]");
	newRow.find("input[id^=mandatory_info_pmi_code_b]").val("");
	newRow.find("input[id^=mandatory_info_title_b]").attr("name","mandatory_info["+(total_rows_3)+"][pmi_title]");
	newRow.find("input[id^=mandatory_info_title_b]").val("");
	newRow.find("input[id^=mandatory_info_desc_b]").attr("name","mandatory_info["+(total_rows_3)+"][pmi_desc]");
	newRow.find("input[id^=mandatory_info_desc_b]").val("");

	newRow.find("div[class^=mandatory_info_comment_"+(total_rows_1)+"]").text("");
	newRow.find("div[class^=mandatory_info_comment_"+(total_rows_1)+"]").attr("class","mandatory_info_comment_"+(total_rows_3)+" small");

	copyObjectText = newRow.html();

	copyObjectText = copyObjectText.replace("mandatory_info_basic","mandatory_info");
	copyObjectText = copyObjectText.replace("<!-- 옵션 삭제 -->","<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;margin:3px;' ondblclick=\"if($('.mandatory_info').length > 1){$('.options_input[idx="+total_rows+"]').remove();}else{alert('마지막 한개는 삭제 하실 수 없습니다.');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>");
	$(copyObjectText).appendTo(option_obj_);
}
*/

function CopyMandatory(target){

	var option_obj = $('#mandatory_info_zone');
	var option_obj_ = $('#mandatory_info_td');
    var total_rows = Number(option_obj.find('table[id^='+target+']:last').attr("mandatory_info_cnt"));

	total_rows_1 =  total_rows + 1;
	total_rows_2 =  total_rows + 2;
	total_rows_3 =  total_rows + 3;

	var newRow = option_obj.find('table[id^='+target+']:last').clone(true);

	//newRow.find("table[id^=mandatory_info]").attr("mandatory_info_cnt",""+(total_rows_2)+"");
	newRow.attr("mandatory_info_cnt",""+(total_rows_2)+"");

	newRow.find("input[id^=mandatory_info_pmi_ix_a]").attr("name","mandatory_info["+(total_rows_2)+"][pmi_ix]");
	newRow.find("input[id^=mandatory_info_pmi_ix_a]").val("");
	newRow.find("input[id^=mandatory_info_pmi_code_a]").attr("name","mandatory_info["+(total_rows_2)+"][pmi_code]");
	newRow.find("input[id^=mandatory_info_pmi_code_a]").val("");
	newRow.find("input[id^=mandatory_info_title_a]").attr("name","mandatory_info["+(total_rows_2)+"][pmi_title]");
	newRow.find("input[id^=mandatory_info_title_a]").val("");
	newRow.find("input[id^=mandatory_info_desc_a]").attr("name","mandatory_info["+(total_rows_2)+"][pmi_desc]");
	newRow.find("input[id^=mandatory_info_desc_a]").val("");

	newRow.find("div[class^=mandatory_info_comment_"+(total_rows)+"]").text("");
	newRow.find("div[class^=mandatory_info_comment_"+(total_rows)+"]").attr("class","mandatory_info_comment_"+(total_rows_2)+" small");

	newRow.find("input[id^=mandatory_info_pmi_ix_b]").attr("name","mandatory_info["+(total_rows_3)+"][pmi_ix]");
	newRow.find("input[id^=mandatory_info_pmi_ix_b]").val("");
	newRow.find("input[id^=mandatory_info_pmi_code_b]").attr("name","mandatory_info["+(total_rows_3)+"][pmi_code]");
	newRow.find("input[id^=mandatory_info_pmi_code_b]").val("");
	newRow.find("input[id^=mandatory_info_title_b]").attr("name","mandatory_info["+(total_rows_3)+"][pmi_title]");
	newRow.find("input[id^=mandatory_info_title_b]").val("");
	newRow.find("input[id^=mandatory_info_desc_b]").attr("name","mandatory_info["+(total_rows_3)+"][pmi_desc]");
	newRow.find("input[id^=mandatory_info_desc_b]").val("");

	newRow.find("div[class^=mandatory_info_comment_"+(total_rows_1)+"]").text("");
	newRow.find("div[class^=mandatory_info_comment_"+(total_rows_1)+"]").attr("class","mandatory_info_comment_"+(total_rows_3)+" small");

	//copyObjectText = newRow.html();
	
	newRow.attr("class","mandatory_info");

	//copyObjectText = copyObjectText.replace("mandatory_info_basic","mandatory_info");
	//copyObjectText = copyObjectText.replace("<!-- 옵션 삭제 -->","<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;margin:3px;' ondblclick=\"if($('.mandatory_info').length > 1){$('.options_input[idx="+total_rows+"]').remove();}else{alert('마지막 한개는 삭제 하실 수 없습니다.');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>");
	//$(copyObjectText).appendTo(option_obj_);

	newRow.appendTo(option_obj_);
}

function CopyMandatoryGlobal(target){

    var option_obj = $('#mandatory_info_zone_global');
    var option_obj_ = $('#mandatory_info_td_global');
    var total_rows = Number(option_obj.find('table[id^='+target+']:last').attr("mandatory_info_cnt_global"));

    total_rows_1 =  total_rows + 1;
    total_rows_2 =  total_rows + 2;
    total_rows_3 =  total_rows + 3;

    var newRow = option_obj.find('table[id^='+target+']:last').clone(true);

    //newRow.find("table[id^=mandatory_info]").attr("mandatory_info_cnt",""+(total_rows_2)+"");
    newRow.attr("mandatory_info_cnt_global",""+(total_rows_2)+"");

    newRow.find("input[id^=mandatory_info_pmi_ix_a_global]").attr("name","mandatory_info_global["+(total_rows_2)+"][pmi_ix]");
    newRow.find("input[id^=mandatory_info_pmi_ix_a_global]").val("");
    newRow.find("input[id^=mandatory_info_pmi_code_a_global]").attr("name","mandatory_info_global["+(total_rows_2)+"][pmi_code]");
    newRow.find("input[id^=mandatory_info_pmi_code_a_global]").val("");
    newRow.find("input[id^=mandatory_info_title_a_global]").attr("name","mandatory_info_global["+(total_rows_2)+"][pmi_title]");
    newRow.find("input[id^=mandatory_info_title_a_global]").val("");
    newRow.find("input[id^=mandatory_info_desc_a_global]").attr("name","mandatory_info_global["+(total_rows_2)+"][pmi_desc]");
    newRow.find("input[id^=mandatory_info_desc_a_global]").val("");

    newRow.find("div[class^=mandatory_info_comment_global_"+(total_rows)+"]").text("");
    newRow.find("div[class^=mandatory_info_comment_global_"+(total_rows)+"]").attr("class","mandatory_info_comment_global_"+(total_rows_2)+" small");

    newRow.find("input[id^=mandatory_info_pmi_ix_b_global]").attr("name","mandatory_info_global["+(total_rows_3)+"][pmi_ix]");
    newRow.find("input[id^=mandatory_info_pmi_ix_b_global]").val("");
    newRow.find("input[id^=mandatory_info_pmi_code_b_global]").attr("name","mandatory_info_global["+(total_rows_3)+"][pmi_code]");
    newRow.find("input[id^=mandatory_info_pmi_code_b_global]").val("");
    newRow.find("input[id^=mandatory_info_title_b_global]").attr("name","mandatory_info_global["+(total_rows_3)+"][pmi_title]");
    newRow.find("input[id^=mandatory_info_title_b_global]").val("");
    newRow.find("input[id^=mandatory_info_desc_b_global]").attr("name","mandatory_info_global["+(total_rows_3)+"][pmi_desc]");
    newRow.find("input[id^=mandatory_info_desc_b_global]").val("");

    newRow.find("div[class^=mandatory_info_comment_global_"+(total_rows_1)+"]").text("");
    newRow.find("div[class^=mandatory_info_comment_global_"+(total_rows_1)+"]").attr("class","mandatory_info_comment_global_"+(total_rows_3)+" small");

    //copyObjectText = newRow.html();

    newRow.attr("class","mandatory_info_global");

    //copyObjectText = copyObjectText.replace("mandatory_info_basic","mandatory_info");
    //copyObjectText = copyObjectText.replace("<!-- 옵션 삭제 -->","<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;margin:3px;' ondblclick=\"if($('.mandatory_info').length > 1){$('.options_input[idx="+total_rows+"]').remove();}else{alert('마지막 한개는 삭제 하실 수 없습니다.');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>");
    //$(copyObjectText).appendTo(option_obj_);

    newRow.appendTo(option_obj_);
}

function loadLaundry(sel,target,select_type){
	var relation_code = $('select[name='+sel+']').val();
	var target_depth = $('select[name='+target+']').attr('depth');

	$.ajax({
	    url : '../product/product_mandatory.act.php',
	    type : 'GET',
	    data : {relation_code:relation_code,
				target_depth:target_depth,
				act:'select_laundry',
				select_type : select_type
				},
	    dataType: 'json',
	    error: function(data,error){// 실패시 실행함수 
	        alert(error);},
	    success: function(args){
		console.log(args);
			if(args != null){
				$('select[name='+target+']').empty();
				$('select[name='+target+']').append("<option value='' selected>선택</option>");
				$.each(args, function(index, entry){
					$('select[name='+target+']').append("<option value='"+entry.cid+"' >"+entry.title+"</option>");
				});
			}else{
				$('select[name='+target+']').empty();
				$('select[name='+target+']').append("<option value='' selected>선택</option>");
			}
        }
    });
}