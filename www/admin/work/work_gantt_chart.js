function CopyRow(){
	//alert($('#project_architecture').setTable.sort);
	//alert($('#project_architecture tr[0]').html());
		//.after(html_string););
		$("#debug_text").val($('#project_architecture').html());
}

function zeroFill(sVal, nCnt){ // zeroFill(값, 채울갯수) 
    var zero = ''; 
    var ret  = sVal.toString(); 
    if(nCnt > 100) return sVal; // 100개 이상 채울 수 없음;; 
    for(var i=0 ; i < nCnt-ret.length ; i++){ 
        zero += '0'; 
    } 
    return zero + ret; 
} 


 
function fnAddRow(tbName, data) {  

 try {           // 테이블을 찾아서 로우 추가  
 
   var tbody = $('#' + tbName + ' tbody');  
   var total_rows = tbody.find('tr').length;  
   var rows = tbody.find('tr[depth^=1]').length;  
   if(rows < 10){
		_new_code = zeroFill((rows+1),2).replace("0","")+".";
   }else{
		_new_code = (rows+1)+".";
   }
   // alert(rows);
   //var newRow = tbody.find('tr:last').clone(true).appendTo(tbody);  
   //alert(tbody.find('tr[depth^=1]'));
   if($.browser.msie){
      var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
   }else{
	  var newRow = tbody.find('tr[depth^=1]:last').clone(true).insertAfter(tbody);  
   }
   

   fnControlInit(newRow, rows);  
   // rno가 있으면 숫자를 입력  
   //newRow.find("span[id^=rno]").html(rows + 1);  
   
   newRow.find("span[id^=rno]").html(_new_code);  
   //alert(newRow.find("input[id^=architecture_code]").html());
   newRow.find("input[id^=architecture_code]").val(_new_code);  
   newRow.find("input[id^=architecture_code]").attr("name","architecture["+(total_rows)+"][architecture_code]");
   newRow.find("input[id^=architecture_name]").attr("name","architecture["+(total_rows)+"][architecture_name]");
   newRow.find("input[id^=architecture_depth]").attr("name","architecture["+(total_rows)+"][architecture_depth]");
   newRow.find("input[id^=sub_archietcture_cnt]").attr("name","architecture["+(total_rows)+"][sub_archietcture_cnt]");
   newRow.find("input[id^=architecture_wl_ix]").attr("name","architecture["+(total_rows)+"][architecture_wl_ix]");
   newRow.find("input[id^=architecture_charger_ix]").attr("name","architecture["+(total_rows)+"][architecture_charger_ix]");
   newRow.attr('rno',_new_code);
 // alert(newRow.html());
   newRow.find("input[id^=architecture_sdate]").attr("name","architecture["+(total_rows)+"][architecture_sdate]");
  // newRow.find("input[id^=architecture_sdate]").val("architecture["+(rows)+"][architecture_sdate]"); //테스트 후 삭제
   newRow.find("input[id^=architecture_sdate]").attr("id","architecture_sdate_"+total_rows);
   //alert(total_rows);
   //alert(newRow.find("input[id^=architecture_wl_ix]").parent().html());
   newRow.find("input[id^=architecture_sdate_"+total_rows+"]").datepicker('destroy').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			//alert(dateText);
			if(newRow.find("input[id^=architecture_edate_"+total_rows+"]").val() == ""){
				var s_date = new Date(dateText.substring(0,4),dateText.substring(5,7),dateText.substring(8,10));
				var s_d = s_date.getDate()+1;
				var s_m = s_date.getMonth();
				var s_y = s_date.getFullYear();

				var change_s_date = new Date(s_y,s_m,s_d);
				var change_s_d = change_s_date.getDate();
				var change_s_m = change_s_date.getMonth();
				var change_s_y = change_s_date.getFullYear();

				
				newRow.find("input[id^=architecture_edate_"+total_rows+"]").val(change_s_y+"-"+zeroFill(change_s_m,2)+"-"+zeroFill(change_s_d,2));
			}

			//newRow.find("input[id^=architecture_edate_sub_"+_new_code+"]").datepicker('setDate','+1d');
		}

   });

   newRow.find("input[id^=architecture_edate]").attr("name","architecture["+(total_rows)+"][architecture_edate]");
  // newRow.find("input[id^=architecture_edate]").val("architecture["+(rows)+"][architecture_edate]"); //테스트 후 삭제
   newRow.find("input[id^=architecture_edate]").attr("id","architecture_edate_"+total_rows);
   newRow.find("input[id^=architecture_edate_"+total_rows+"]").datepicker('destroy').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'

   });
   

   if(data){
	   //alert(1);
		newRow.find("input[id^=architecture_wl_ix]").val(data.wl_ix);
		newRow.find("input[id^=architecture_code]").val(data.architecture_code);
		newRow.find("input[id^=architecture_depth]").val(data.depth);
		//newRow.find("input[id^=sub_archietcture_cnt]").val(data.sub_archietcture_cnt);
		
		newRow.find("input[id^=architecture_name]").val(data.work_title);		
		newRow.find("#architecture_charger_ix option").each(function () {
			if($(this).val() == data.charger_ix){
				$(this).attr('selected',true);
			}
		});
		newRow.find("input[id^=architecture_sdate_"+total_rows+"]").val(data.sdate.substring(0,4)+"-"+data.sdate.substring(4,6)+"-"+data.sdate.substring(6,8));
		//alert(data.dday.substring(0,4)+"-"+data.dday.substring(4,6)+"-"+data.dday.substring(6,8));
		newRow.find("input[id^=architecture_edate_"+total_rows+"]").val(data.dday.substring(0,4)+"-"+data.dday.substring(4,6)+"-"+data.dday.substring(6,8));
		if(data.is_schedule == '1'){
			newRow.find("img[id^=architecture_is_schedule]").css('display','inline');
		}else{
			newRow.find("img[id^=architecture_is_schedule]").css('display','none');
		}

		newRow.find("div[id^=architecture_scedule_bar]").css('width',((data.edate_num-data.sdate_num+1)*31)+"px");
		newRow.find("div[id^=architecture_scedule_bar]").css('margin-left',((data.sdate_num-data.project_sdate_display_num)*31)+"px");
		newRow.find("div[id^=architecture_scedule_bar]").attr('title',data.work_title+" "+data.sdate.substring(0,4)+"-"+data.sdate.substring(4,6)+"-"+data.sdate.substring(6,8)+"~"+data.dday.substring(0,4)+"-"+data.dday.substring(4,6)+"-"+data.dday.substring(6,8));
		newRow.find("div[id^=architecture_scedule_bar]").unbind('dblclick');
		newRow.find("div[id^=architecture_scedule_bar]").dblclick(function(){
			ShowModalWindow('work_add.php?mmode=pop&wl_ix='+data.wl_ix,680,750,'member_info');
		});
		//newRow.find("div[id^=architecture_scedule_bar]").draggable('destory');
		//newRow.find("div[id^=architecture_scedule_bar]").draggable({ grid: [ 30,30 ],axis: "x" });
		newRow.find("td[id^=architecture_status]").html(data.complete_rate+" %");
		newRow.find("div[id^=architecture_scedule_bar] div").css('width',data.complete_rate+" %");

		if(data.sub_archietcture_cnt == '0'){
			
			newRow.find("input[id^=architecture_sdate_sub_"+total_rows+"]").attr('disabled', false);
			newRow.find("input[id^=architecture_edate_sub_"+total_rows+"]").attr('disabled', false);
			newRow.find("div[id^=architecture_scedule_bar]").draggable({ grid: [ 31,31 ],axis: "x",
				start: function(e, ui) {    
				//this_position_x
					//$('#test_text').val(ui.position.left/31);
					//$('#test_text').val(event.offsetX);
					//alert('drag started');         
				},         
				drag: function(e, ui) {           
				
				},         
				stop: function(e, ui) {     
					var add_date = ui.position.left/31;
					//$('#test_text').val(ui.position.left/31);
					

					var s_date = new Date(data.sdate.substring(0,4),data.sdate.substring(4,6),data.sdate.substring(6,8));
					var s_d = s_date.getDate()+add_date;
					var s_m = s_date.getMonth();
					var s_y = s_date.getFullYear();

					newRow.find("input[id^=architecture_sdate_sub_"+_new_code+"]").val(s_y+"-"+zeroFill(s_m,2)+"-"+zeroFill(s_d,2));

					var e_date = new Date(data.dday.substring(0,4),data.dday.substring(4,6),data.dday.substring(6,8));
					var e_d = e_date.getDate()+add_date;
					var e_m = e_date.getMonth();
					var e_y = e_date.getFullYear();

					newRow.find("input[id^=architecture_edate_sub_"+_new_code+"]").val(e_y+"-"+zeroFill(e_m,2)+"-"+zeroFill(e_d,2));

					//alert('drag stopped');         
				}     
			});

		}else{
			//newRow.find("input[id^=architecture_sdate_sub_"+_new_code+"]").attr('disabled', true);
			//newRow.find("input[id^=architecture_edate_sub_"+_new_code+"]").attr('disabled', true);
		}
   }
   

	
	// 해외 유학경험의 달력 필드가 있으면 이벤트 바인딩  
   //newRow.find(":text[id^=txtStudyStartDate]").simpleDatepicker({ startdate: 1980 });  
   //newRow.find(":text[id^=txtStudyEndDate]").simpleDatepicker({ startdate: 1980 });  
   
	return newRow;
   }  
   catch (e) {  
     alert(e.Message);  
   }  

}  

function SubAddRow(tbName,tbObj, data) {  

 try {           // 테이블을 찾아서 로우 추가  
	//alert("aaa"+tbObj);
   var tbody = $('#' + tbName + ' tbody');  
   
   var total_rows = tbody.find('tr').length-1;  
   
   var rows = tbObj.find('tr').length+1;  
//alert(tbObj.html());
   var _depth = parseInt(tbObj.attr('depth'));
   var _sub_archietcture_cnt = parseInt(tbObj.find("input[id^=sub_archietcture_cnt]").val());
   //alert(tbObj.find("input[id^=sub_archietcture_cnt]").html());
   tbObj.find("input[id^=sub_archietcture_cnt]").val(_sub_archietcture_cnt+1);
   //alert(_sub_archietcture_cnt+1);
   //var rows = tbObj.find('tr[depth^='+_depth+']').length+1;  
   
   //var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
   
   
   //alert(tbObj.attr('rno').replace("\.","\\\."));
   //var tbody2 = $('tr:regex(rno, .*'+tbObj.attr('rno').replace(".","\.")+'.*)'); 
  
   if($.browser.msie){
      var newRow = tbObj.clone(true).appendTo(tbObj);  
   }else{
	  var newRow = tbObj.clone(true).insertAfter(tbObj);  
   }
  
   var _new_code = newRow.find("span[id^=rno]").html()+""+zeroFill(rows,2)+".";
   _new_code = _new_code.replace(".0",".");
   fnControlInit(newRow, rows);  
   // rno가 있으면 숫자를 입력  
   //newRow.find("span[id^=rno]").html(rows + 1);  
   //var _this_padding = parseInt(newRow.find("td[id^=tdWork_name]").css('padding-left').replace("px",""));
	var _this_padding = parseInt(newRow.find("span[id^=rno]").css('padding-left').replace("px",""));
   //alert(_this_padding+30);
   
   newRow.find("span[id^=rno]").css('padding-left',_this_padding+20);  
   newRow.find("span[id^=rno]").html(_new_code);  
   newRow.find("input[id^=architecture_code]").val(_new_code);  
   //alert(tbody.find('tr').length);
   newRow.find("input[id^=architecture_code]").attr("name","architecture["+(total_rows)+"][architecture_code]");
   newRow.find("input[id^=architecture_name]").attr("name","architecture["+(total_rows)+"][architecture_name]");
   newRow.find("input[id^=architecture_wl_ix]").attr("name","architecture["+(total_rows)+"][architecture_wl_ix]");
   newRow.find("input[id^=architecture_charger_ix]").attr("name","architecture["+(total_rows)+"][architecture_charger_ix]");
   newRow.find("input[id^=architecture_depth]").val(_depth+1);
   newRow.find("input[id^=sub_archietcture_cnt]").attr("name","architecture["+(total_rows)+"][sub_archietcture_cnt]");
   
   //newRow.find("input[id^=sub_archietcture_cnt]").val(0);
 //  newRow.find("input[id^=sub_archietcture_cnt]").attr("id","sub_archietcture_cnt_"+_new_code);
   newRow.attr('depth',_depth+1);
   newRow.attr('rno',_new_code);

   
   newRow.find("input[id^=architecture_sdate]").attr("name","architecture["+(total_rows)+"][architecture_sdate]");
   //newRow.find("input[id^=architecture_sdate]").val("architecture["+(rows)+"][architecture_sdate]");
   newRow.find("input[id^=architecture_sdate]").attr("id","architecture_sdate_sub_"+_new_code);
   newRow.find("input[id^=architecture_edate]").attr("name","architecture["+(total_rows)+"][architecture_edate]");
  // newRow.find("input[id^=architecture_edate]").val("architecture["+(rows)+"][architecture_edate]");
   newRow.find("input[id^=architecture_edate]").attr("id","architecture_edate_sub_"+_new_code);

   newRow.find("input[id^=architecture_sdate_sub_"+_new_code+"]").datepicker('destroy').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			//alert(dateText);
			if(newRow.find("input[id^=architecture_edate_sub_"+_new_code+"]").val() == ""){
				var s_date = new Date(dateText.substring(0,4),dateText.substring(5,7),dateText.substring(8,10));
				var s_d = s_date.getDate();
				var s_m = s_date.getMonth();
				var s_y = s_date.getFullYear();

				var change_s_date = new Date(s_y,s_m,s_d);
				var change_s_d = change_s_date.getDate();
				var change_s_m = change_s_date.getMonth();
				var change_s_y = change_s_date.getFullYear();

			
				newRow.find("input[id^=architecture_edate_sub_"+_new_code+"]").val(change_s_y+"-"+zeroFill(change_s_m,2)+"-"+zeroFill(change_s_d,2));
			}

			//newRow.find("input[id^=architecture_edate_sub_"+_new_code+"]").datepicker('setDate','+1d');
		}

   });

   newRow.find("input[id^=architecture_edate_sub_"+_new_code+"]").datepicker('destroy').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'

   });
   /*
   $(".architecture_sdate").each(function(){
	   //alert($(this).parent().html());
		$(this).datepicker('destroy').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'

		});
   });
	*/
   
  
/*
   $(".architecture_edate").each(function(){
	   //alert($(this).parent().html());
		$(this).datepicker('destroy').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'

		});
   });
*/   
   if(data){
	  
		newRow.find("input[id^=architecture_wl_ix]").val(data.wl_ix);
		newRow.find("input[id^=architecture_code]").val(data.architecture_code);
		newRow.find("input[id^=architecture_depth]").val(data.depth);
		//newRow.find("input[id^=sub_archietcture_cnt]").val(data.sub_archietcture_cnt);
		newRow.find("input[id^=architecture_name]").val(data.work_title);
		
		newRow.find("#architecture_charger_ix option").each(function () {
			if($(this).val() == data.charger_ix){
				$(this).attr('selected',true);
			}
		});
		newRow.find("input[id^=architecture_sdate_sub_"+_new_code+"]").val(data.sdate.substring(0,4)+"-"+data.sdate.substring(4,6)+"-"+data.sdate.substring(6,8));
		newRow.find("input[id^=architecture_edate_sub_"+_new_code+"]").val(data.dday.substring(0,4)+"-"+data.dday.substring(4,6)+"-"+data.dday.substring(6,8));
		//alert(data.is_schedule);
		if(data.is_schedule == '1'){
			newRow.find("img[id^=architecture_is_schedule]").css('display','inline');
		}else{
			newRow.find("img[id^=architecture_is_schedule]").css('display','none');
		}

		newRow.find("div[id^=architecture_scedule_bar]").css('width',((data.edate_num-data.sdate_num+1)*31)+"px");
		newRow.find("div[id^=architecture_scedule_bar]").css('margin-left',((data.sdate_num-data.project_sdate_display_num)*31)+"px");
		newRow.find("div[id^=architecture_scedule_bar]").attr('title',data.work_title+" "+data.sdate.substring(0,4)+"-"+data.sdate.substring(4,6)+"-"+data.sdate.substring(6,8)+"~"+data.dday.substring(0,4)+"-"+data.dday.substring(4,6)+"-"+data.dday.substring(6,8));
		newRow.find("div[id^=architecture_scedule_bar]").unbind('dblclick');
		newRow.find("div[id^=architecture_scedule_bar]").dblclick(function(){
			ShowModalWindow('work_add.php?mmode=pop&wl_ix='+data.wl_ix,680,750,'member_info');
		});
		//newRow.find("div[id^=architecture_scedule_bar]").draggable('destory');
		//var this_position_x = 0;
		//alert((data.sub_archietcture_cnt == '0')+"::::"+data.sub_archietcture_cnt)
		if(data.sub_archietcture_cnt == '0'){
			
			//newRow.find("div[id^=architecture_scedule_bar]").resizable().parent('.ui-wrapper').draggable(); 
			newRow.find("input[id^=architecture_sdate_sub_"+_new_code+"]").attr('disabled', false);
			newRow.find("input[id^=architecture_edate_sub_"+_new_code+"]").attr('disabled', false);
			
			newRow.find("div[id^=architecture_scedule_bar]").draggable({ grid: [ 31,31 ],axis: "x",
				start: function(e, ui) {    
				//this_position_x
					//$('#test_text').val(ui.position.left/31);
					//$('#test_text').val(event.offsetX);
					//alert('drag started');         
				},         
				drag: function(e, ui) {           
				
				},         
				stop: function(e, ui) {     
					var add_date = ui.position.left/31;
					//$('#test_text').val(ui.position.left/31);
					
					
					var s_date = new Date(data.sdate.substring(0,4),data.sdate.substring(4,6),data.sdate.substring(6,8));
					var s_d = s_date.getDate()+add_date;
					var s_m = s_date.getMonth();
					var s_y = s_date.getFullYear();

					var change_s_date = new Date(s_y,s_m,s_d);
					var change_s_d = change_s_date.getDate();
					var change_s_m = change_s_date.getMonth();
					var change_s_y = change_s_date.getFullYear();

					
					newRow.find("input[id^=architecture_sdate_sub_"+_new_code+"]").val(change_s_y+"-"+zeroFill(change_s_m,2)+"-"+zeroFill(change_s_d,2));

					var e_date = new Date(data.dday.substring(0,4),data.dday.substring(4,6),data.dday.substring(6,8));
					var e_d = e_date.getDate()+add_date;
					var e_m = e_date.getMonth();
					var e_y = e_date.getFullYear();

					var change_e_date = new Date(e_y,e_m,e_d);
					var change_e_d = change_e_date.getDate();
					var change_e_m = change_e_date.getMonth();
					var change_e_y = change_e_date.getFullYear();

					newRow.find("input[id^=architecture_edate_sub_"+_new_code+"]").val(change_e_y+"-"+zeroFill(change_e_m,2)+"-"+zeroFill(change_e_d,2));

					//alert('drag stopped');         
				}     
			});
			/*
			newRow.find("div[id^=architecture_scedule_bar]").resizable({         
				start: function(e, ui) {             
					//alert('resizing started');         
				},         
				resize: function(e, ui) {           
				
				},         
				stop: function(e, ui) {             
					//alert('resizing stopped');         
				}     
			});
			*/

		}else{
			//newRow.find("input[id^=architecture_sdate_sub_"+_new_code+"]").attr('disabled', true);
			//newRow.find("input[id^=architecture_edate_sub_"+_new_code+"]").attr('disabled', true);
		}
		
		
		
		newRow.find("td[id^=architecture_status]").html(data.complete_rate+" %");
		newRow.find("div[id^=architecture_scedule_bar] div").css('width',data.complete_rate+" %");
		//alert(data.project_sdate_display_num+":::"+data.sdate_num);
		//alert(((data.project_sdate_display_num-data.sdate_num)*30)+"px");
   }
	

	return newRow;
   // 해외 유학경험의 달력 필드가 있으면 이벤트 바인딩  
   //newRow.find(":text[id^=txtStudyStartDate]").simpleDatepicker({ startdate: 1980 });  
   //newRow.find(":text[id^=txtStudyEndDate]").simpleDatepicker({ startdate: 1980 });  
   }  
   catch (e) {  
     alert("sub_depth : "+e.Message + " ::: " + data.work_title);  
   }  

}

function fnRemove(obj) {  
     try {  
         // 현재 선택된 오브젝트의 상위 tr을 찾아서 삭제~  
         var rows = $(obj).parent().parent().parent().parent().parent("tbody").find('> tr');  
         var table = $(obj).parent().parent().parent().parent().parent().parent();  
            
         if (table.attr("id") != "tbWDHChild" && rows.length > 2) {  
             $(obj).parent().parent().parent().parent().remove();  
             // 삭제시 아이디 값이 중복되는 현상으로 인해 아이디 값 재 설정.  
             $(table).find("table[id^=tbWDHChild]").each(function (i) {  
                 var id = this.id  
                 if (id)  
                     this.id = this.id.split('_')[0] + '_' + i;  
             });  
         }  
         else if(table.attr("id") == "tbWDHChild" && rows.length > 1){  
             $(obj).parent().parent().parent().parent().remove();  
             var i = 0;  
             // 삭제시 아이디 값이 중복되는 현상으로 인해 아이디 값 재 설정.  
             $(table).find("table[id^=tbWDHChild]").each(function (i) {  
                 var id = this.id  
                 if (id)  
                     this.id = this.id.split('_')[0] + '_' + i;  
             });  
         }  
         table = $(rows).parent().parent();  
         if (table.attr("id").indexOf("tbWDHChild") >= 0) {  
             var tbody = $(table).find("tbody");  
             tbody.find('> tr').each(function (i) {  
                 $(this).find("> td > div > span[id^=rno]").html(i + 1);  
             });  
         }  
     }  
     catch (e) {  
         alert(e.Message);  
     }  
 }  

 function fnControlInit(jRowobj, rowCnt){  

     // input tag를 찾아서 value 지움  
     jRowobj.find(':input[id^=architecture_name]').val('').each(function () {  
         var id = this.id  
         if (id) {  
             //this.id = this.id.split('_')[0] + '_' + rowCnt;  
         }  
     });  
	 jRowobj.find(':input[id^=architecture_wl_ix]').val('');
	 //jRowobj.find(':input[id^=sub_archietcture_cnt]').val(0);
	 
 } 
 
 
 
function SetTableDragDrop(tbName) {
		//jQuery("#" + tableID).setTable();
        
		jQuery("#" + tbName).tableDnD({
            onDragClass: "test",
            onDrop: function(table, row) {
				

				var tbody = $('#' + tbName + ' tbody');  
				var rows = tbody.find('tr').length;  
				var i = 0;
				$('#' + tbName).find("input[id^=architecture_wl_ix]").each(function(){
					$(this).attr("name","architecture["+(i)+"][architecture_wl_ix]"); 
					i++;
				});
				var i = 0;
				$('#' + tbName).find("input[id^=architecture_code]").each(function(){
					$(this).attr("name","architecture["+(i)+"][architecture_code]"); 
					i++;
				});
				var i = 0;
				$('#' + tbName).find("input[id^=architecture_name]").each(function(){					
					$(this).attr("name","architecture["+(i)+"][architecture_name]"); 
					i++;
				});
				var i = 0;
				$('#' + tbName).find("input[id^=architecture_depth]").each(function(){
					$(this).attr("name","architecture["+(i)+"][architecture_depth]"); 
					i++;
				});
				var i = 0;
				$('#' + tbName).find("input[id^=sub_archietcture_cnt]").each(function(){					
					$(this).attr("name","architecture["+(i)+"][sub_archietcture_cnt]"); 
					i++;
				});
				var i = 0;
				$('#' + tbName).find("select[id^=architecture_charger_ix]").each(function(){
					$(this).attr("name","architecture["+(i)+"][architecture_charger_ix]"); 
					i++;
				});
				var i = 0;
				$('#' + tbName).find("input[id^=architecture_sdate]").each(function(){					
					$(this).attr("name","architecture["+(i)+"][architecture_sdate]"); 
					i++;
				});
				var i = 0;
				$('#' + tbName).find("input[id^=architecture_edate]").each(function(){					
					$(this).attr("name","architecture["+(i)+"][architecture_edate]"); 
					i++;
				});
            }
        });
}

jQuery(document).ready(function() {

    SetTableDragDrop("project_architecture");
	
	//alert($.getJSON);
	$.ajax({ 
		type: 'GET', 
		data: 
			{'act': 'personal_job_json','charger_ix': charger_ix,'rand': '1'},  
		url: '/admin/work/work_project_architecture.act.php',  
		dataType: 'json', 
		async: true, 
		beforeSend: function(){ 

			 //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open'); 
			 //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events'); 
		},  
		success: function(datas){ 
			//alert(1);
				//alert(datas);
				//alert(calevents[0].work_title);
			var tbObj = new Array(5);	
			//alert(datas);
			if(datas != null){
				$.each(datas, function(i,data){ 
					//alert(data.work_title);
					if(i == 0){
						$("#architecture_row_0").find("input[id^=architecture_wl_ix]").val(data.wl_ix);
						$("#architecture_row_0").find("input[id^=architecture_code]").val(data.architecture_code);
						$("#architecture_row_0").find("input[id^=architecture_depth]").val(data.depth);
						$("#architecture_row_0").find("input[id^=sub_archietcture_cnt]").val(data.sub_archietcture_cnt);
						$("#architecture_row_0").find("input[id^=architecture_name]").val(data.work_title);
						$("#architecture_row_0").find("#architecture_charger_ix option").each(function () {
							if($(this).val() == data.charger_ix){
								$(this).attr('selected',true);
							}
						});
						$("#architecture_row_0").find("input[id^=architecture_sdate]").val(data.sdate.substring(0,4)+"-"+data.sdate.substring(4,6)+"-"+data.sdate.substring(6,8));
						$("#architecture_row_0").find("input[id^=architecture_edate]").val(data.dday.substring(0,4)+"-"+data.dday.substring(4,6)+"-"+data.dday.substring(6,8));

						$("#architecture_row_0").find("input[id^=architecture_sdate]").datepicker('destroy').datepicker({
							//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
							dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
							//showMonthAfterYear:true,
							dateFormat: 'yy-mm-dd',
							buttonImageOnly: true,
							buttonText: '달력',
							onSelect: function(dateText, inst){
								//alert(dateText);
								//$("#architecture_row_0").find("input[id^=architecture_edate]").datepicker('setDate','+1d');
								if($("#architecture_row_0").find("input[id^=architecture_edate").val() == ""){
									var s_date = new Date(dateText.substring(0,4),dateText.substring(5,7),dateText.substring(8,10));
									var s_d = s_date.getDate();
									var s_m = s_date.getMonth();
									var s_y = s_date.getFullYear();

									var change_s_date = new Date(s_y,s_m,s_d);
									var change_s_d = change_s_date.getDate();
									var change_s_m = change_s_date.getMonth();
									var change_s_y = change_s_date.getFullYear();

								
									$("#architecture_row_0").find("input[id^=architecture_edate").val(change_s_y+"-"+zeroFill(change_s_m,2)+"-"+zeroFill(change_s_d,2));
								}
							}

					   });

					   $("#architecture_row_0").find("input[id^=architecture_edate]").datepicker('destroy').datepicker({
							//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
							dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
							//showMonthAfterYear:true,
							dateFormat: 'yy-mm-dd',
							buttonImageOnly: true,
							buttonText: '달력'

					   });

					   

						if(data.is_schedule == '1'){
							$("#architecture_row_0").find("img[id^=architecture_is_schedule]").css('display','inline');
						}else{
							$("#architecture_row_0").find("img[id^=architecture_is_schedule]").css('display','none');
						}


						$("#architecture_row_0").find("div[id^=architecture_scedule_bar]").css('width',((data.edate_num-data.sdate_num+1)*31)+"px");
						//alert(data.project_sdate_num+":::"+data.project_sdate_display_num);
						//alert(((data.sdate_num-data.project_sdate_display_num)*30)+"px");
						$("#architecture_row_0").find("div[id^=architecture_scedule_bar]").css('margin-left',((data.sdate_num-data.project_sdate_display_num)*31)+"px");
						$("#architecture_row_0").find("div[id^=architecture_scedule_bar]").attr('title',data.work_title+" "+data.sdate.substring(0,4)+"-"+data.sdate.substring(4,6)+"-"+data.sdate.substring(6,8)+"~"+data.dday.substring(0,4)+"-"+data.dday.substring(4,6)+"-"+data.dday.substring(6,8));
						$("#architecture_row_0").find("div[id^=architecture_scedule_bar]").dblclick(function(){
							ShowModalWindow('work_add.php?mmode=pop&wl_ix='+data.wl_ix,680,750,'member_info');
						});

						if(data.sub_archietcture_cnt == '0'){
							//newRow.find("div[id^=architecture_scedule_bar]").resizable().parent('.ui-wrapper').draggable(); 
							$("#architecture_row_0").find("input[id^=architecture_sdate]").attr('disabled', false);
							$("#architecture_row_0").find("input[id^=architecture_edate]").attr('disabled', false);

							$("#architecture_row_0").find("div[id^=architecture_scedule_bar]").draggable({ grid: [ 31,31 ],axis: "x",
								start: function(e, ui) {    
								//this_position_x
									//$('#test_text').val(ui.position.left/31);
									//$('#test_text').val(event.offsetX);
									//alert('drag started');         
								},         
								drag: function(e, ui) {           
								
								},         
								stop: function(e, ui) {     
									var add_date = ui.position.left/31;
									//$('#test_text').val(ui.position.left/31);
									
									
									var s_date = new Date(data.sdate.substring(0,4),data.sdate.substring(4,6),data.sdate.substring(6,8));
									var s_d = s_date.getDate()+add_date;
									var s_m = s_date.getMonth();
									var s_y = s_date.getFullYear();

									var change_s_date = new Date(s_y,s_m,s_d);
									var change_s_d = change_s_date.getDate();
									var change_s_m = change_s_date.getMonth();
									var change_s_y = change_s_date.getFullYear();

									
									$("#architecture_row_0").find("input[id^=architecture_sdate]").val(change_s_y+"-"+zeroFill(change_s_m,2)+"-"+zeroFill(change_s_d,2));

									var e_date = new Date(data.dday.substring(0,4),data.dday.substring(4,6),data.dday.substring(6,8));
									var e_d = e_date.getDate()+add_date;
									var e_m = e_date.getMonth();
									var e_y = e_date.getFullYear();

									var change_e_date = new Date(e_y,e_m,e_d);
									var change_e_d = change_e_date.getDate();
									var change_e_m = change_e_date.getMonth();
									var change_e_y = change_e_date.getFullYear();

									$("#architecture_row_0").find("input[id^=architecture_edate]").val(change_e_y+"-"+zeroFill(change_e_m,2)+"-"+zeroFill(change_e_d,2));

									//alert('drag stopped');         
								}     
							});

						}else{
							//$("#architecture_row_0").find("input[id^=architecture_sdate]").attr('disabled', true);
							//$("#architecture_row_0").find("input[id^=architecture_edate]").attr('disabled', true);
						}
						//$("#architecture_row_0").find("div[id^=architecture_scedule_bar]").draggable({ grid: [ 30,30 ],axis: "x", revert:true });
						/*
						$("#architecture_row_0").find("div[id^=architecture_scedule_bar]").resizable({         
							start: function(e, ui) {             
								alert('resizing started');         
							},         
							resize: function(e, ui) {           
							
							},         
							stop: function(e, ui) {             
								alert('resizing stopped');         
							}     
						});
						*/

						$("#architecture_row_0").find("td[id^=architecture_status]").html(data.complete_rate+" %");
						$("#architecture_row_0").find("div[id^=architecture_scedule_bar] div").css('width',data.complete_rate+" %");
						
						tbObj[0] = $("#architecture_row_0");
						//alert(tbObj);
						//	newRow.find("input[id^=architecture_name]").val(data.work_title);
					}else{
						if(data.depth == 1 || true){
							
							tbObj[0] = fnAddRow('project_architecture', data);
						}else if(data.depth == 2){
							//alert(tbObj);
							tbObj[1] = SubAddRow('project_architecture',tbObj[0], data);
						}else if(data.depth == 3){
							//alert("tbObj2:"+tbObj2);
							tbObj[2] = SubAddRow('project_architecture',tbObj[1], data);
						}else if(data.depth == 4){
							//alert("tbObj2:"+tbObj2);
							tbObj[3] = SubAddRow('project_architecture',tbObj[2], data);
						}
					}
					//alert(data.work_title);
				  
				}); 
			}
		} 
	}); 
/*
	$.getJSON("/admin/work/work_project_architecture.act.php", 
	{ 
	act:'project_json',
	group_ix: group_ix,
	format:'json'
	}, 
	function(datas) { 
		alert(datas);
		$.each(datas, function(i,data){ 
		//alert(data.src);
		alert(data.work_title);
		  //$("<img/>").attr("src", data.src).appendTo("<div/>").css('margin','3px').appendTo("#VEScontainer"); 
		  //if ( i == 3 ) return false; 
		}); 
	})
*/
});


function CheckArchitectureInfo(frm, tbName){
	
	var tbody = $('#'+tbName+' tbody');  
	var rows = tbody.find('tr').length+1;  
	var i = 0;
	$('#' + tbName).find("input[id^=architecture_wl_ix]").each(function(){
		$(this).attr("name","architecture["+(i)+"][architecture_wl_ix]"); 
		i++;
	});
	var i = 0;
	$('#' + tbName).find("input[id^=architecture_code]").each(function(){
		$(this).attr("name","architecture["+(i)+"][architecture_code]"); 
		i++;
	});
	var i = 0;
	$('#' + tbName).find("input[id^=architecture_name]").each(function(){					
		$(this).attr("name","architecture["+(i)+"][architecture_name]"); 
		i++;
	});

	var i = 0;
	$('#' + tbName).find("input[id^=architecture_depth]").each(function(){					
		$(this).attr("name","architecture["+(i)+"][architecture_depth]"); 
		i++;
	});
	var i = 0;
	$('#' + tbName).find("input[id^=sub_archietcture_cnt]").each(function(){					
		$(this).attr("name","architecture["+(i)+"][sub_archietcture_cnt]"); 
		i++;
	});
	var i = 0;
	$('#' + tbName).find("select[id^=architecture_charger_ix]").each(function(){
		$(this).attr("name","architecture["+(i)+"][architecture_charger_ix]"); 
		i++;
	});
	var i = 0;
	$('#' + tbName).find("input[class^=textbox architecture_sdate]").each(function(){					
		$(this).attr("name","architecture["+(i)+"][architecture_sdate]"); 
		i++;
	});

	var i = 0;
	$('#' + tbName).find("input[class^=textbox architecture_edate]").each(function(){
		$(this).attr("name","architecture["+(i)+"][architecture_edate]"); 
		i++;
	});

	return true;
}


function file_select(vdata_type, data_typename){
	

	document.getElementById('file_text').innerHTML = data_typename;


	switch(vdata_type){
		case "0":		
			document.getElementById('file_tr').style.display = 'none';
			document.getElementById('file_exp').innerHTML = "<img src=../img/01.gif align=absmiddle> 파일을 입력하기 위해서 파일 종류를 선택해 주세요";
			break;
		case "1":
			document.getElementById('file_tr').style.display = 'block';
			document.getElementById('file_exp').innerHTML = "<img src=../img/01.gif align=absmiddle> .txt, .doc, .hwp 등의 데이타 파일";
			break;
		case "2":
			document.getElementById('file_tr').style.display = 'block';
			document.getElementById('file_exp').innerHTML = "<img src=../img/01.gif align=absmiddle> .gif, .jpg, .psd, .ai ... 등 이미지 파일";
			break;
		case "3":
			document.getElementById('file_tr').style.display = 'block';
			document.getElementById('file_exp').innerHTML = "<img src=../img/01.gif align=absmiddle> .mp3, .asf, .ogg";
			break;
		case "4":
			document.getElementById('file_tr').style.display = 'block';
			document.getElementById('file_exp').innerHTML = "<img src=../img/01.gif align=absmiddle> .asf, .avi, .wmv, .mpg ... ";
			break;
		case "5":
			document.getElementById('file_tr').style.display = 'block';
			document.getElementById('file_exp').innerHTML = "<img src=../img/01.gif align=absmiddle> .zip, .rar, .tar, .cab ... ";
			break;
	}
}

function MySetCategory(cname,cid,depth){
	document.frames['act'].location.href='./kms.bbs/write.act.php?act=category&mycid='+cid+'&depth='+depth;
	document.kms_input.mycid.value = cid;	
	document.kms_input.depth.value = depth;	
}


function GlobalSetCategory(cname,cid,depth){
	document.frames['act'].location.href='./kms.bbs/write.act.php?act=category&gcid='+cid+'&depth='+depth;
	document.kms_input.gcid.value = cid;	
//	document.kms_input.gcname.value = cname;	
//	document.kms_input.gcid.value = cid;	
}

function SubmitX(frm){
	
	if(frm.gcid.value.length < 1){
		alert('전체 지식 카테고리를 선택해 주세요');
		return false;	
	}
	
	if(frm.mycid.value.length < 1){
		alert('내 지식 카테고리를 선택해 주세요');
		return false;	
	}
	
	if(frm.data_name.value.length < 1){
		alert('지식 제목을 선택해 주세요');
		return false;	
	}
	frm.data_text.value = iView.document.body.innerHTML;
	if(frm.data_text.value.length < 1){
		alert('지식 내용을 선택해 주세요');
		return false;	
	}
}

function Content_Input(){
document.kms_input.content.value = document.contentsfrm.contents.value;	
}
































 
 function updateGroupInfo(group_ix,group_name,disp, vieworder, is_project, group_depth, parent_group_ix){
 	var frm = document.group_form;
 	
 	frm.act.value = 'update';
 	frm.group_ix.value = group_ix;
 	frm.group_name.value = group_name;
 	frm.vieworder.value = vieworder;
	if(is_project == '1'){
		frm.is_project.checked = true;
	}else{
		frm.is_project.checked = false;
	}
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

	if(group_depth == '2'){ 
		frm.group_depth[1].checked = true;
		frm.parent_group_ix.disabled = false;

		$("#parent_group_ix option").each(function () {
			//str += $(this).text() + " ";
			if($(this).val() == parent_group_ix){
				$(this).attr('selected',true);
			}
		});
	}else if(group_depth == '1'){ 
		frm.group_depth[0].checked = true;
		frm.parent_group_ix.disabled = true;
		frm.parent_group_ix[0].selected = true;
	}

 
}
 
 function deleteGroupInfo(act, group_ix){
 	if(confirm('해당그룹  정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.group_form; 	
 		frm.act.value = act;
 		frm.group_ix.value = group_ix;
 		frm.submit();
 	}	
}

function ToggleAllGroup(){
	//alert($('#company_goal').css('display')+":::"+$.cookie('company_goal_view'));
	//alert($('#view_complete_job').attr('checked'));
	if($('#view_all_group').attr('checked') == true){		
		$.cookie('view_all_group', '1', {expires:1,domain:document.domain, path:'/', secure:0});
	}else{		
		$.cookie('view_all_group', '0', {expires:1,domain:document.domain, path:'/', secure:0});
	}

	/*
	var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
		return node.data.key;
	});

	// Get a list of all selected TOP nodes
	var selRootNodes = node.tree.getSelectedNodes(true);
	// ... and convert to a key array:
	var selRootKeys = $.map(selRootNodes, function(node){
		return node.data.key;
	});

	group_ix = selKeys.join(',');
	alert(group_ix);
	*/
	$.ajax({ 
		type: 'GET', 
		data: {'mmode': 'inner_list'},  
		url: './work_group.php',  
		dataType: 'html', 
		async: true, 
		beforeSend: function(){ 
			//$('#loading').show();
			$.blockUI.defaults.css = {}; 
			$.blockUI({ message: $('#loading'), css: { width: '100px' , height: '100px' ,padding:  '10px'} });  
			
			 //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open'); 
			 //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events'); 
		},  
		success: function(calevents){ 

					//alert(calevents);
					$('#result_area').html(calevents);
					//alert($('#result_area').html());
					$.unblockUI(); 
			
				
		} 
	}); 
}