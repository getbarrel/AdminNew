<? 
include("../class/layout.work.class");
include("work.lib.php");

$db = new Database;
$mdb = new Database;


$Contents01 = "
	<table width='100%' height=100% cellpadding=3 cellspacing=0 border='0' align='left'>";
if($mmode == ""){
$Contents01 .= "
	  <tr >
		<td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation("동영상 메뉴얼", "업무관리 > 동영상 메뉴얼 ")."</td>
	  </tr>";
}
$Contents01 .= "
	  <tr >
	    <td align='left' colspan=4>";

$Contents01 .= "		
		 <!--[if IE]>
         <object width='1100' height='680' classid='CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6' codebase='http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701' standby='Loading Microsoft Windows Media Player components...' type='video/x-ms-wmv'>
          <param name='filename' value='/images/unimind/unimind_manual.wmv' />
          <param name='Url' value='/images/unimind/unimind_manual.wmv' />
          <param name='animationatstart' value='1' />
          <param name='autostart' value='1' />
          <param name='balance' value='0' />
          <param name='currentmarker' value='0' />
          <param name='currentPosition' value='0' />
          <param name='displaymode' value='4' />
          <param name='enablecontextmenu' value='0' />
          <param name='enabled' value='1' />
          <param name='fullscreen' value='0' />
          <param name='invokeurls' value='1' />
          <param name='PlayCount' value='1' />
          <param name='rate' value='1' />
          <param name='showcontrols' value='1' />
          <param name='showstatusbar' value='1' />
          <param name='stretchtofit' value='1' />
          <param name='transparentatstart' value='1' />
          <param name='captioningID' value='captions' />
          <param name='displaybackcolor' value='0' />
         </object>
         <![endif]-->
         <!--[if !IE]>
         <object type='video/x-ms-asf-plugin' data='/images/unimind/unimind_manual.wmv' width='100%' height='100%'></object>
         <![endif]-->
	    </td>
	  </tr>
	  
	  </table>";
	  

		

$Contents = "<table width='100%' border=0 cellpadding=0 cellspacing=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."<tr><td>".$help_text."</td></tr>";
$Contents = $Contents."</table >";

	
	
 $Script = "
<script  id='dynamic'></script>
<style>
/* css for timepicker */
#ui-timepicker-div dl{ text-align: left; }
#ui-timepicker-div dl dt{ height: 25px; }
#ui-timepicker-div dl dd{ margin: -25px 0 10px 65px; }

</style>
<link rel='stylesheet' media='all' type='text/css' href='css/jquery-ui-1.8.custom.css' />
<link type='text/css' href='./js/themes/base/ui.all.css' rel='stylesheet' />
<link type='text/css' href='./js/themes/demos.css' rel='stylesheet' />

 <script language='javascript'>
$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
    dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
    //showMonthAfterYear:true,
    dateFormat: 'yymmdd',
    buttonImageOnly: true,
    buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		$('#end_datepicker').datepicker('setDate','+0d');
	}

	});

	//$('#start_timepicker').timepicker();

	
	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
    dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
    //showMonthAfterYear:true,
    dateFormat: 'yymmdd',
    buttonImageOnly: true,
    buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});
$().ready(function() {
//	$(\"#work_detail\").focus().autocomplete(cities);
	
	$('#work_detail').keypress(function(event) { 
		
	  if (event.which == '13') { 
		 //event.preventDefault(); 
		 //alert(parseInt($('#work_detail').css('height').replace('px',''))+20);
		 $('#work_detail').css('height',parseInt($('#work_detail').css('height').replace('px',''))+20);
	   } 
	 
	}); 

});
	 
function showTabContents(vid, tab_id){
	var area = new Array('mailling_insert_form','mailling_search_form');
	var tab = new Array('tab_01','tab_02');
	
	for(var i=0; i<area.length; ++i){
		if(area[i]==vid){
			document.getElementById(vid).style.display = 'block';			
			document.getElementById(tab_id).className = 'on';
		}else{			
			document.getElementById(area[i]).style.display = 'none';
			document.getElementById(tab[i]).className = '';
		}
	}
	
}

 function updateBankInfo(div_ix,div_name,disp){
 	var frm = document.div_form;
 	
 	frm.act.value = 'update';
 	frm.div_ix.value = div_ix;
 	frm.div_name.value = div_name;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}
 
}

function CheckForm(frm){
	if(frm.parent_group_ix.value == ''){
	 		alert('1차 그룹은 반드시 선택하셔야 합니다.');
	 		return false;
 	}
 		
	if(frm.charger_ix.value.length < 1){
		alert('담당자를 입력해주세요');
		frm.charger_ix.focus();
		return false;
	}
	
	if(frm.sdate.value.length < 1){
		alert('시작 날짜를 입력해주세요');
		frm.sdate.focus();
		return false;
	}
	
	if(frm.dday.value.length < 1){
		alert('작업완료 기한을 입력해주세요');
		frm.dday.focus();
		return false;
	}
	
	if(frm.work_title.value.length < 1){
		alert('업무내용을 입력해주세요');
		frm.work_title.focus();
		return false;
	}
	//else{
	/*
		var PT_email = /[a-z0-9_]{2,}@[a-z0-9-]{2,}\.[a-z0-9]{2,}/i;  // 이메일
		if (!PT_email.test(frm.email.value)){
			alert('이메일 형식이 아닙니다. 확인후 다시 시도해주세요');
			frm.email.focus();
			return false;
		}
	
	}*/
	
	return true;
}

 
 </script>
 ";
	
if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = "<!--script type='text/javascript' src='./js/jquery-1.4.2.min.js'></script-->
<script type='text/javascript' src='./js/ui/ui.core.js'></script>
<script type='text/javascript' src='./js/ui/ui.datepicker.js'></script>
<!--script type='text/javascript' src='./js/ui/jquery-ui-timepicker-addon-0.5.js'></script-->
<script type='text/javascript' src='work.js'></script>".$Script;
	$P->strLeftMenu = work_menu();
	$P->Navigation = "HOME > 업무관리 > 업무 등록관리";
	$P->strContents = $Contents;
	$P->NaviTitle = "업무관리 동영상 메뉴얼";
	$P->prototype_use = false;
	
	echo $P->PrintLayOut();	
}else{
	$P = new LayOut();
	$P->addScript = "<!--script type='text/javascript' src='./js/jquery-1.4.2.min.js'></script-->
<script type='text/javascript' src='./js/ui/ui.core.js'></script>
<script type='text/javascript' src='./js/ui/ui.datepicker.js'></script>
<!--script type='text/javascript' src='./js/ui/jquery-ui-timepicker-addon-0.5.js'></script-->
<script type='text/javascript' src='work.js'></script>".$Script;
	$P->strLeftMenu = work_menu();
	$P->Navigation = "HOME > 업무관리 > 업무 등록관리";
	$P->strContents = $Contents;
	$P->footer_menu = footMenu()."".footAddContents();
	$P->prototype_use = false;
	
	echo $P->PrintLayOut();
}

?>