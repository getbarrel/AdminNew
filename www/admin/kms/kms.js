
function ToggleKmsTreeUser(){
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


$(document).ready(function() {
	/*
	$('#kms_tree').mouseover(function(){
		//$('#tree_user').animate({width:"200px",height:"300px"},500);
		$('#kms_tree').css('position','absolute');
		$('#kms_tree').css('z-index','100');
		$('#kms_tree').css('width','200px');
		$('#kms_tree').css('height','200px');
		$('#kms_tree').css('background-color','#ffffff');
		$('#kms_tree').css('border','2px solid silver');

		
	});


	

	$('#result_area').mouseover(function(){
		
		$('#kms_tree').css('width','155px').delay(3000);
		$('#kms_tree').css('height','180px').delay(3000);
		$('#kms_tree').css('position','relative');
		$('#kms_tree').css('border','');
		$('#kms_tree').css('z-index','0');

	});
	*/
});











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
	//document.write('campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2');
	dynamic.src = 'workgroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2';
	
}

function loadUser(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	var depth = sel.depth;
	//document.write('campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2');
	dynamic.src = 'user.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2';
	
}


function loadWorkUser(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	var depth = sel.depth;
	//document.write('workuser.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2');
	dynamic.src = 'workuser.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2';
	
}



function DeleteWorkList(wl_ix){
	if(confirm('해당 업무 목록을 정말로 삭제하시겠습니까?')){
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'delete', 'wl_ix':wl_ix},
			url: './work.act.php',  
			dataType: 'html', 
			async: true, 
			beforeSend: function(){ 
				
			},  
			success: function(calevents){ 
				//alert($('#row_'+wl_ix));
				$('#row_'+wl_ix).slideRow('up',500);
			} 
		}); 
		//document.frames['act'].location.href='work.act.php?act=delete&wl_ix='+wl_ix;
	}
}



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


function print_r(array){
  var str="";
  for(var i in array){
      str += i+':'+array[i]+'\n'
  }
  return str;
} 



function ToggleComapnyGoal(){
	//alert($('#company_goal').css('display')+":::"+$.cookie('company_goal_view'));
	if($('#company_goal').css('display') == "block"){
		$('#company_goal').hide();
		$.cookie('company_goal_view', '0', {expires:1,domain:document.domain, path:'/', secure:0});
	}else{
		$('#company_goal').show();
		$.cookie('company_goal_view', '1', {expires:1,domain:document.domain, path:'/', secure:0});
	}
}


function ViewContents(wl_ix, mode){
	if(mode == 'over'){
		var offset = $('#magnifier_'+wl_ix).offset();
		//$('#contents_box').fadeIn(3000);
		$('#contents_box').css('display','block');
		//$('#contents_box').toggle();
		$('#contents_box').css('position','absolute');
		$('#contents_box').css('left',offset.left+25);
		$('#contents_box').css('top',offset.top);
		$('#contents_desc').html($('#work_title_'+wl_ix).html()+"<br><br>"+$('#work_title_'+wl_ix).attr('desc'));

	}else{
		//$('#contents_box').fadeOut(3000);
		$('#contents_box').css('display','none');
	}
	//alert(offset.left+":::"+offset.top);
	
}
