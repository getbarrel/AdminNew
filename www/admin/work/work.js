
function ToggleWorkJob(){
	//alert($('#company_goal').css('display')+":::"+$.cookie('company_goal_view'));
	//alert($('#view_complete_job').attr('checked'));
		//alert($('#calendar_with_work').attr('checked'));
		if($('#calendar_with_work').attr('checked') == true || $('#calendar_with_work').attr('checked') == 'checked'){		
			$.cookie('view_work_job', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{		
			$.cookie('view_work_job', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
		//alert($.cookie('view_work_job'));
		//loadCalendar();
		$('#calendar').fullCalendar('render');
		if($.cookie('view_work_job') == 1){
			$('#calendar_with_work').attr('checked',true);		
		}else{
			$('#calendar_with_work').attr('checked',false);
		}
	
}


function init_date(FromDate,ToDate) {
	var frm = document.searchmember;
	
	
	for(i=0; i<frm.FromYY.length; i++) {
		if(frm.FromYY.options[i].value == FromDate.substring(0,4))
			frm.FromYY.options[i].selected=true
	}
	for(i=0; i<frm.FromMM.length; i++) {
		if(frm.FromMM.options[i].value == FromDate.substring(5,7))
			frm.FromMM.options[i].selected=true
	}
	for(i=0; i<frm.FromDD.length; i++) {
		if(frm.FromDD.options[i].value == FromDate.substring(8,10))
			frm.FromDD.options[i].selected=true
	}
	
	
	for(i=0; i<frm.ToYY.length; i++) {
		if(frm.ToYY.options[i].value == ToDate.substring(0,4))
			frm.ToYY.options[i].selected=true
	}
	for(i=0; i<frm.ToMM.length; i++) {
		if(frm.ToMM.options[i].value == ToDate.substring(5,7))
			frm.ToMM.options[i].selected=true
	}
	for(i=0; i<frm.ToDD.length; i++) {
		if(frm.ToDD.options[i].value == ToDate.substring(8,10))
			frm.ToDD.options[i].selected=true
	}
	
	
	
}



function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;
	
	if(dType == 1){
		for(i=0; i<frm.FromYY.length; i++) {
			if(frm.FromYY.options[i].value == FromDate.substring(0,4))
				frm.FromYY.options[i].selected=true
		}
		for(i=0; i<frm.FromMM.length; i++) {
			if(frm.FromMM.options[i].value == FromDate.substring(5,7))
				frm.FromMM.options[i].selected=true
		}
		for(i=0; i<frm.FromDD.length; i++) {
			if(frm.FromDD.options[i].value == FromDate.substring(8,10))
				frm.FromDD.options[i].selected=true
		}
		
		
		for(i=0; i<frm.ToYY.length; i++) {
			if(frm.ToYY.options[i].value == ToDate.substring(0,4))
				frm.ToYY.options[i].selected=true
		}
		for(i=0; i<frm.ToMM.length; i++) {
			if(frm.ToMM.options[i].value == ToDate.substring(5,7))
				frm.ToMM.options[i].selected=true
		}
		for(i=0; i<frm.ToDD.length; i++) {
			if(frm.ToDD.options[i].value == ToDate.substring(8,10))
				frm.ToDD.options[i].selected=true
		}
	}else{
		for(i=0; i<frm.vFromYY.length; i++) {
			if(frm.vFromYY.options[i].value == FromDate.substring(0,4))
				frm.vFromYY.options[i].selected=true
		}
		for(i=0; i<frm.vFromMM.length; i++) {
			if(frm.vFromMM.options[i].value == FromDate.substring(5,7))
				frm.vFromMM.options[i].selected=true
		}
		for(i=0; i<frm.vFromDD.length; i++) {
			if(frm.vFromDD.options[i].value == FromDate.substring(8,10))
				frm.vFromDD.options[i].selected=true
		}
		
		
		for(i=0; i<frm.vToYY.length; i++) {
			if(frm.vToYY.options[i].value == ToDate.substring(0,4))
				frm.vToYY.options[i].selected=true
		}
		for(i=0; i<frm.vToMM.length; i++) {
			if(frm.vToMM.options[i].value == ToDate.substring(5,7))
				frm.vToMM.options[i].selected=true
		}
		for(i=0; i<frm.vToDD.length; i++) {
			if(frm.vToDD.options[i].value == ToDate.substring(8,10))
				frm.vToDD.options[i].selected=true
		}
	}
	
}




function onLoad(FromDate, ToDate) {
	var frm = document.searchmember;
	
	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
//	LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate);
//	LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate);

	init_date(FromDate,ToDate);
	
}


function listAction(frm){
		
	PoPWindow('../sms.pop.php',450,300,'sendsms');
	frm.action = '../sms.pop.php';
	frm.target = 'sendsms';
	frm.submit();
}



function clearAll(frm){
		for(i=0;i < frm.ab_ix.length;i++){
				frm.ab_ix[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.ab_ix.length;i++){
				frm.ab_ix[i].checked = true;
		}
}

function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;
			
	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}

function zipcode() {
	var zip = window.open('zipcode.php','','width=440,height=300,scrollbars=yes,status=no');
}


/*************************** below code can be in a js file  *****************************/

var response = null 

	

	function prompt2(promptpicture, prompttitle, message, sendto) { 

		promptbox = document.createElement('div'); 

		promptbox.setAttribute ('id' , 'prompt') ;

			document.getElementsByTagName('body')[0].appendChild(promptbox) ;

			promptbox = eval("document.getElementById('prompt').style") ;

			promptbox.position = 'absolute'; 

			promptbox.top = 350 ;

			promptbox.left = 225 ;

			promptbox.width = 300 ;

			promptbox.border = 'outset 1 #bbbbbb' ;

			document.getElementById('prompt').innerHTML = "<table cellspacing='0' cellpadding='0' border='0' width='100%'><tr valign='middle'><td width='22' height='22' style='text-indent:2;' class='titlebar'><img src='" + promptpicture + "' height='18' width='18'></td><td class='titlebar'>" + prompttitle + "</td></tr></table>" ;

			document.getElementById('prompt').innerHTML = document.getElementById('prompt').innerHTML + "<table cellspacing='0' cellpadding='0' border='0' width='100%' class='promptbox'><tr><td>" + message + "</td></tr><tr><td><input type='text' id='promptbox' onblur='this.focus()' class='promptbox'></td></tr><tr><td align='right'><br><input type='button' class='prompt' value='OK' onMouseOver='this.style.border=\"1 outset #dddddd\"' onMouseOut='this.style.border=\"1 solid transparent\"' onClick='" + sendto + "(document.getElementById(\"promptbox\").value); document.getElementsByTagName(\"body\")[0].removeChild(document.getElementById(\"prompt\"))'> <input type='button' class='prompt' value='Cancel' onMouseOver='this.style.border=\"1 outset transparent\"' onMouseOut='this.style.border=\"1 solid transparent\"' onClick='" + sendto + "(\"\"); document.getElementsByTagName(\"body\")[0].removeChild(document.getElementById(\"prompt\"))'></td></tr></table>" ;

			document.getElementById("promptbox").focus() ;

		} 

function myfunction(value) { 

	if(value.length<=0)

		

		return false;

	else

		document.getElementById('output').innerHTML="<b>"+value+"</b>";

} 



function callPrompt(){

	prompt2('btn1p.gif', 'My Prompt','Please enter your name ,if you want to chat with our <B>customer support executive</B>', 'myfunction');

}



function loadWorkGroup(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	var depth = sel.depth;
	//document.write('workgroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2');
	//dynamic.src = 'workgroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2';
	window.frames['act'].location.href = 'workgroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2';
	
}

function loadUser(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	var depth = sel.depth;
	//document.write('campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2');
	window.frames['act'].location.href = 'user.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2';
	
}


function loadWorkUser(sel,target,wl_ix) {
	//alert(target);
	var trigger = "";
	//alert(sel.length);
	for(i=0;i < sel.length;i++){
		//alert(sel[i].selected);
		if(sel[i].selected){
			if(trigger == ""){
				trigger = sel[i].value;
			}else{
				trigger += ","+sel[i].value;
			}
		}
	}
	//var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	var depth = sel.depth;
	//alert(trigger);
	//document.write('workuser.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2');
	
	//document.write ('<script language=\"javascript\" src=\"workuser.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2\"></script>');
	//dynamic.src = 'workuser.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2';
	//document.write('workuser.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2&wl_ix='+wl_ix);
	//document.write('workuser.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2&wl_ix='+wl_ix);
	if(wl_ix){
		window.frames["act"].location.href = 'workuser.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2&wl_ix='+wl_ix;
	}else{
		window.frames["act"].location.href = 'workuser.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2&wl_ix=';
	}
	//alert(dynamic.src);
}



function DeleteWorkList(wl_ix, mmode, list_view_type){
	if(confirm('해당 업무를 정말로 삭제하시겠습니까?')){
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'delete', 'wl_ix':wl_ix, 'mmode':mmode},
			url: './work.act.php',  
			dataType: 'html', 
			async: true, 
			beforeSend: function(){ 
				
			},  
			success: function(calevents){ 
				//alert($('#row_'+wl_ix));
				alert('정상적으로 삭제되었습니다.');
				
				try{
					$('#row_'+wl_ix).slideRow('up',500);
				}catch(e){
					//alert($(opener.document));
					//$('#calendar',opener.document).fullCalendar('removeEvents');
					//alert(mmode);
					if(mmode == 'pop'){	
						
						if(list_view_type == "calendar"){
							parent.self.close();
							$('#calendar').fullCalendar('render');
							
						}else{
							parent.self.close();
						}
					}else{
						document.location.href='work_list.php?list_type=myjob';
					}
					//$('#calendar').fullCalendar('removeEvent',wl_ix);
					
					//$('#calendar',opener.document).fullCalendar('removeEvent',wl_ix);
					
					//opener.$('#calendar').fullCalendar('removeEvents');
					//alert($('#calendar'));
					/*
					$('#calendar').fullCalendar('removeEvents',function(event) {
					   return event.categoria_id == wl_ix;
					});
					*/
				}
			} 
		}); 
		//document.frames['act'].location.href='work.act.php?act=delete&wl_ix='+wl_ix;
	}
}

/*

function loadCampaignGroup(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	//alert(target);
	var depth = sel.depth;
	//document.write('campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target);
	dynamic.src = 'campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';
	//document.location.href='campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';
	
}

*/

function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;
	
	
	if(dType == 1){
		frm.sdate.value = FromDate;
		frm.edate.value = ToDate;
	}else{
		frm.dday_sdate.value = FromDate;
		frm.dday_edate.value = ToDate;
	}
	
}

function ChangeReportDate(frm){
	if(frm.report_date.checked){
		frm.sdate.disabled = false;
		frm.edate.disabled = false;
	}else{
		frm.sdate.disabled = true;
		frm.edate.disabled = true;	
	}
}

function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		frm.sdate.disabled = false;
		frm.edate.disabled = false;
	}else{
		frm.sdate.disabled = true;
		frm.edate.disabled = true;	
	}
}

function ChangeDday(frm){
	if(frm.dday.checked){
		frm.dday_sdate.disabled = false;
		frm.dday_edate.disabled = false;
	}else{
		frm.dday_sdate.disabled = true;
		frm.dday_edate.disabled = true;	
	}
}

function ChangeUpdateForm(selected_id){
	var area = new Array('batch_update_sendemail','batch_update_group','batch_update_sms');
	
	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';			
		}else{			
			document.getElementById(area[i]).style.display = 'none';
		}
	}
}

function WorkViewTab(selected_id){
	
	if(selected_id == 'tab_01'){
		$('#report_area').css('display','');
		$('#comment_area').css('display','none');
		$('#issue_area').css('display','none');
		
		$('#tab_01').attr('class','on');
		$('#tab_02').removeClass();
		$('#tab_03').removeClass();
	}else if(selected_id == 'tab_02'){
		$('#report_area').css('display','none');
		$('#comment_area').css('display','');
		$('#issue_area').css('display','none');
		
		$('#tab_01').removeClass();
		$('#tab_02').attr('class','on');
		$('#tab_03').removeClass();
	}else if(selected_id == 'tab_03'){
		$('#report_area').css('display','none');
		$('#comment_area').css('display','none');
		$('#issue_area').css('display','');
		
		$('#tab_01').removeClass();
		$('#tab_02').removeClass();
		$('#tab_03').attr('class','on');
	}
	/*
	var area = new Array('batch_update_sendemail','batch_update_group','batch_update_sms');
	
	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';			
		}else{			
			document.getElementById(area[i]).style.display = 'none';
		}
	}
	*/
}


function print_r(array){
  var str="";
  for(var i in array){
      str += i+':'+array[i]+'\n'
  }
  return str;
} 



function ToggleComapnyGoal(){
	//alert($('#company_goal').css('display')+":::"+$.cookie('company_goal_view'));
	if($('#company_goal').css('display') == "block" || $('#company_goal').css('display') == "inline"){
		//alert(1);
		$('#company_goal').hide();
		$.cookie('company_goal_view', '0', {expires:1,domain:document.domain, path:'/', secure:0});
	}else{
		//alert(2+":::"+$('#company_goal').css('display'));
		$('#company_goal').show();
		$.cookie('company_goal_view', '1', {expires:1,domain:document.domain, path:'/', secure:0});
	}
}

function ToggleTreeWorkGroup(){
	//alert($('#company_goal').css('display')+":::"+$.cookie('company_goal_view'));
	if($('#tree_work_group').css('display') == "block"){
		$('#tree_work_group').hide();
		$('#tree_work_group').parent().css('height','0px');
		$.cookie('tree_work_group_view', '0', {expires:1,domain:document.domain, path:'/', secure:0});
	}else{
		$('#tree_work_group').show();
		$('#tree_work_group').parent().css('height','140px');
		$.cookie('tree_work_group_view', '1', {expires:1,domain:document.domain, path:'/', secure:0});
	}
}

function ToggleTreeUser(){
	//alert($('#company_goal').css('display')+":::"+$.cookie('company_goal_view'));
	if($('#tree_user').css('display') == "block"){
		$('#tree_user').hide();
		$('#tree_user').parent().css('height','0px');
		$.cookie('tree_user_view', '0', {expires:1,domain:document.domain, path:'/', secure:0});
	}else{
		$('#tree_user').show();
		$('#tree_user').parent().css('height','150px');
		$.cookie('tree_user_view', '1', {expires:1,domain:document.domain, path:'/', secure:0});
	}
}

function ToggleStatusSelect(wl_ix){
	
	if($('#quick_complate_rate_'+wl_ix).css('display') == "none"){
		$('#quick_complate_rate_'+wl_ix).slideDown(500);
	}else{
		$('#quick_complate_rate_'+wl_ix).slideUp(500);
	}
}


function WorkStatusSelect(work_status) {
  var str = "";
  //alert($("#complete_rate"));
  if(work_status == "WC"){
	$("#complete_rate option").each(function () {
		//str += $(this).text() + " ";
		if($(this).val() == '100'){
			$(this).attr('selected',true);
		}
	});
  }
  //$("div").text(str);

}



function updateWorkStatus(wl_ix, complete_rate){
	//alert(1);
	$('#quick_complate_rate_'+wl_ix).hide();
	//alert(2);
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'complete_rate_update', 'wl_ix':wl_ix,'complete_rate':complete_rate},
		url: './work.act.php',  
		dataType: 'html', 
		async: true, 
		beforeSend: function(){ 
			/*/admin/images/indicator.gif*/
			//alert(wl_ix);
				$('#quick_complate_rate_'+wl_ix).hide();
				$('#charger_'+wl_ix).hide();
				$('#s_loading_'+wl_ix).show();
				//$('#s_loading_'+wl_ix).html("");
				//alert(1);
				
				
		},  
		success: function(calevents){ 
			//alert(calevents);
			/*
			var complete_rate_str ="";
			if(complete_rate == "100"){
				complete_rate_str = "작업완료";
			}else if(complete_rate == "0"){
				complete_rate_str = "작업대기";
			}*/
			$('#work_status_text_'+wl_ix).text(calevents);
			//alert(complete_rate);
			if(complete_rate == 0){				
				complete_rate = 1;
			}
			$('#graph_'+wl_ix).animate({width:complete_rate+"%"},1000);
			$('#charger_'+wl_ix).show();
			
			$('#quick_complate_rate_'+wl_ix).hide();
			$('#quick_complate_rate_'+wl_ix).css('display','none');
			$('#s_loading_'+wl_ix).hide().delay(5000);
			//alert(1);
			//alert($('#quick_complate_rate_'+wl_ix).parent().html());
			//$('#s_loading_'+wl_ix).html("");
			
		} 
	}); 
	return false;

}

function TreeView(type){
	if(type == 'over'){
		$('#tree_user').css('position','absolute');
		$('#tree_user').css('z-index','100');
		//$('#tree_user').css('width','200px');
		//$('#tree_user').css('height','300px');
		$('#tree_user').css('background-color','#ffffff');
		$('#tree_user').css('border','2px solid silver');
		//$('#tree_user').animate({width:"200px",height:"300px"},500);
	}else{
		$('#tree_user').css('position','');
		$('#tree_user').css('z-index','');
		//$('#tree_user').css('width','150px');
		//$('#tree_user').css('height','150px');
		$('#tree_user').css('background-color','#ffffff');
		$('#tree_user').css('border','');
		$('#tree_user').animate({width:"150px",height:"150px"},500);

	}
}


function TreeWorkGroupView(type){
	if(type == 'over'){
		$('#tree_work_group').css('position','absolute');
		$('#tree_work_group').css('z-index','100');
		$('#tree_work_group').css('width','200px');
		$('#tree_work_group').css('height','300px');
		$('#tree_work_group').css('background-color','#ffffff');
		$('#tree_work_group').css('border','2px solid silver');
	}else{
		$('#tree_work_group').css('position','');
		$('#tree_work_group').css('z-index','');
		$('#tree_work_group').css('width','150px');
		$('#tree_work_group').css('height','140px');
		$('#tree_work_group').css('background-color','#ffffff');
		$('#tree_work_group').css('border','');

	}
}



$(document).ready(function() {
	

	$('#result_area').mouseover(function(){
		
		$('#tree_user').css('width','195px');
		$('#tree_user').css('height','149px');
		$('#tree_user').css('border','');
		$('#tree_user').css('z-index','0');

		$('#tree_work_group').css('width','195px');
		$('#tree_work_group').css('height','149px');
		$('#tree_work_group').css('border','');
		$('#tree_work_group').css('z-index','0');
/*
		$('#work_tmp_box').css('width','153px');
		$('#work_tmp_input').css('width','153px');
		$('#work_tmp_box').css('z-index','0');
		$('#work_tmp_box').css('position','');
		$('#external-events').css('border','0px solid silver');
*/		
	});

	$('#calendar').mouseover(function(){
		
		$('#tree_user').css('width','195px');
		$('#tree_user').css('height','150px');
		$('#tree_user').css('border','');
		$('#tree_user').css('z-index','0');

		$('#tree_work_group').css('width','195px');
		$('#tree_work_group').css('height','150px');
		$('#tree_work_group').css('border','');
		$('#tree_work_group').css('z-index','0');
		
	});
	

});


function ChangeSheet(oSheet){
	
	window.frames['act'].location.href='sheet.php?report_type='+oSheet;
}



// <-- Textarea Resize 1,2,3,4,5
function TextareaResize(que, area)
{
	//var area = document.getElementById('CommentTextAreaLay');
	var heit = parseInt(area.css('height').replace('px',''));
	if (que == '-')
	{
		if (heit - 50 >= 50) area.css('height',(heit - 50));
	}
	else if (que == '+')
	{
		if (heit + 50 <= 700) area.css('height',(heit + 50));
	}
	else {
		area.css('height',50);
	}
}