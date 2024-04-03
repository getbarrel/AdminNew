var SelectReport = 1;
var vNewdate = "";
function rollon(evt) {
	//oevent = window.event.srcElement.className;
	// 호환성 작업 2011-04-05 kbk //
	var target=evt.target?evt.target:evt.srcElement;
	if(window.addEventListener) {
		oevent=target.getAttribute("class");
	} else {
		oevent=target.className;
	}
	// 호환성 작업 2011-04-05 kbk //
		if (oevent == 'calendar' || oevent == 'calendarWeekHeadOn' || oevent == 'calendarWeekMiddleOn' || oevent == 'calendarWeekTailOn') {			
			//window.event.srcElement.className = 'calendar_on'; // 클래스 변경
			// 호환성 작업 2011-04-05 kbk //
			if(window.addEventListener) {
				target.setAttribute("class","calendar_on");
			} else {
				target.className='calendar_on';
			}
			// 호환성 작업 2011-04-05 kbk //
		}
}

function rolloff(evt) {
	//oevent = window.event.srcElement.className;
	// 호환성 작업 2011-04-05 kbk //
	var target=evt.target?evt.target:evt.srcElement;
	if(window.addEventListener) {
		oevent=target.getAttribute("class");
	} else {
		oevent=target.className;
	}
	// 호환성 작업 2011-04-05 kbk //
		if (oevent == 'calendar_on' || oevent == 'calendarWeekHeadOff' || oevent == 'calendarWeekMiddleOff' || oevent == 'calendarWeekTailOff') {
			//window.event.srcElement.className = 'calendar'; // 클래스 변경
			// 호환성 작업 2011-04-05 kbk //
			if(window.addEventListener) {
				target.setAttribute("class","calendar");
			} else {
				target.className='calendar';
			}
			// 호환성 작업 2011-04-05 kbk //
		}
}

function ChangeReport(value) {	
	SelectReport = value;	
	if(value == 1 || value == 2){
		ChangeCalenderView(value);
	}
	
	
}

function ViewReport(LinkPage, vDate){
	vNewdate = vDate;
	
	if(SelectReport == 1){
		//document.frames['act'].location.href=LinkPage+'?mode=iframe&vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport; // 호환성 오류 2011-04-05 kbk
		document.getElementById('act').src=LinkPage+'?mode=iframe&vdate='+vDate+'&SelectReport='+SelectReport;
	}else if(SelectReport == 2){
		//document.frames['act'].location.href=LinkPage+'?mode=iframe&vdate='+printWeek(vDate)+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport; // 호환성 오류 2011-04-05 kbk
		document.getElementById('act').src=LinkPage+'?mode=iframe&vdate='+printWeek(vDate)+'&SelectReport='+SelectReport;
	}else if(SelectReport == 3){
		//document.frames['act'].location.href=LinkPage+'?mode=iframe&vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport; // 호환성 오류 2011-04-05 kbk
		document.getElementById('act').src=LinkPage+'?mode=iframe&vdate='+vDate+'&SelectReport='+SelectReport;
	}
	loading();
}

function ViewTreeReport(LinkPage, vDate,vName, vreferid,vdepth){
	if(vNewdate != ""){
		vDate = vNewdate;
	}
	
	if(SelectReport == 1){
		//document.location.href=LinkPage+'?mode=iframe&vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport+'&depth='+vdepth+'&referer_id='+vreferid;
		//document.frames['act'].location.href=LinkPage+'?mode=iframe&vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport+'&depth='+vdepth+'&referer_id='+vreferid; // 호환성 오류 2011-04-05 kbk
		document.getElementById('act').src=LinkPage+'?mode=iframe&vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport+'&depth='+vdepth+'&referer_id='+vreferid;
	}else if(SelectReport == 2){
		//document.location.href=LinkPage+'?vdate='+printWeek(vDate)+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport+'&depth='+vdepth+'&referer_id='+vreferid;
		//document.frames['act'].location.href=LinkPage+'?mode=iframe&vdate='+printWeek(vDate)+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport+'&depth='+vdepth+'&referer_id='+vreferid; // 호환성 오류 2011-04-05 kbk
		document.getElementById('act').location.href=LinkPage+'?mode=iframe&vdate='+printWeek(vDate)+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport+'&depth='+vdepth+'&referer_id='+vreferid;
	}else if(SelectReport == 3){
		//document.frames['act'].location.href=LinkPage+'?mode=iframe&vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport+'&depth='+vdepth+'&referer_id='+vreferid; // 호환성 오류 2011-04-05 kbk
		document.getElementById('act').location.href=LinkPage+'?mode=iframe&vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport+'&depth='+vdepth+'&referer_id='+vreferid;
		//document.location.href=LinkPage+'?vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport
	}
	loading();
}


function formatDate(date) {
	var mymonth = date.getMonth();
	var myweekday = date.getDate();
	var myyear = date.getYear();
	
/*	alert(date);
	alert(mymonth);
	alert(myweekday);
*/	
	
//	alert(myyear+''+SetZefoFill(2,'0',mymonth)+''+SetZefoFill(2,'0',myweekday));
	return (myyear+''+SetZefoFill(2,'0',mymonth+1)+''+SetZefoFill(2,'0',myweekday));
//	return (date);
}

function SetZefoFill(nchar,fillstr,nValue){	
    	if (nValue.toString().length == nchar){
    		return nValue;
    	}else{
    		return fillstr+nValue;
    	}
    }



function printWeek(vdate) {

	var now = new Date();	
	var nowDay = vdate.substring(6,8);	
	var nowMonth = vdate.substring(4,6);
	var nowYear = vdate.substring(0,4);
	var thisDate = new Date(nowYear, nowMonth-1, nowDay);
	var nowDayOfWeek = thisDate.getDay();
	
//	alert(formatDate(thisDate));
//	alert(nowYear+''+nowMonth+''+nowDay);
//	alert(nowDayOfWeek);
//	nowYear += (nowYear < 2000) ? 1900 : 0;
	var weekStartDate = new Date(nowYear, nowMonth-1, nowDay - nowDayOfWeek);
//	alert(formatDate(new Date('2005', '1', '28')));
//	alert(formatDate(weekStartDate));
	return formatDate(weekStartDate);
	//var weekEndDate = new Date(nowYear, nowMonth, nowDay + (6 - nowDayOfWeek));
}

function printWeekx(vdate) {
	var now = new Date();
	var nowDayOfWeek = now.getDay();
	var nowDay = now.getDate();	
	var nowMonth = now.getMonth();
	var nowYear = now.getYear();
	nowYear += (nowYear < 2000) ? 1900 : 0;
	var weekStartDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek);
//	alert(nowDayOfWeek);
	var weekEndDate = new Date(nowYear, nowMonth, nowDay + (6 - nowDayOfWeek));
//	document.write('이번주는 :  ' + formatDate(weekStartDate) + ' - ' + formatDate(weekEndDate) + ' 까지 입니다');
}


function ChangeCalenderView(vSelectReport){
	var oTR;
	var oTD;
	var len;
	
	if(vSelectReport == 1 || vSelectReport == 3){
		try{
			for(seq = 0; seq < 6; seq++){
				//oTD = eval('document.all.week' + seq);
				oTR=document.getElementById("week"+seq); // 호환성 작업 2011-04-05 kbk
				oTD=oTR.getElementsByTagName("td"); // 호환성 작업 2011-04-05 kbk
				len = oTD.length;
					
				for(var i =0; i < len ; i++){
					if(window.addEventListener) oTD[i].setAttribute("class","calendar"); // 호환성 작업 2011-04-05 kbk
					else oTD[i].className = 'calendar';				
				}
			}
		}catch(e){
			
		}
	}else{
		try{
			
			for(seq = 0; seq < 6; seq++){
				//oTD = eval('document.all.week' + seq);
				oTR=document.getElementById("week"+seq); // 호환성 작업 2011-04-05 kbk
				oTD=oTR.getElementsByTagName("td"); // 호환성 작업 2011-04-05 kbk
				len = oTD.length;
				for(var i =0; i < len ; i++){
					
					if(i == 0){
						if(window.addEventListener) oTD[i].setAttribute("class","calendarWeekHeadOff"); // 호환성 작업 2011-04-05 kbk
						else oTD[i].className = 'calendarWeekHeadOff';
						//oTD[i].className = 'calendarWeekHeadOff';
					}else if(i == 7){
						if(window.addEventListener) oTD[i].setAttribute("class","calendarWeekTailOff"); // 호환성 작업 2011-04-05 kbk
						else oTD[i].className = 'calendarWeekTailOff';
						//oTD[i].className = 'calendarWeekTailOff';
					}else{
						if(window.addEventListener) oTD[i].setAttribute("class","calendarWeekMiddleOff"); // 호환성 작업 2011-04-05 kbk
						else oTD[i].className = 'calendarWeekMiddleOff';
						//oTD[i].className = 'calendarWeekMiddleOff';
					}
				}
			}
		}catch(e){
			
		}
	}
	try{
		parent.unloading();
	}catch(e){}
	
}


function mouseOnTD2(seq, bool,evt)
{
	// event(evt) 를 받아와서 씀 2011-04-05 kbk
	if(SelectReport == 1){
		
		if (bool){
			//rollon();
			rollon(evt);// 호환성 작업 2011-04-05 kbk
		}else{
			//rollon();
			rolloff(evt);// 호환성 작업 2011-04-05 kbk
		}
	}else if(SelectReport == 2){
			
	
		//var oTD = eval('document.all.week' + seq);
		var oTR = document.getElementById("week" + seq); // 호환성 작업 2011-04-05 kbk
		var oTD = oTR.getElementsByTagName("td"); // 호환성 작업 2011-04-05 kbk
		//var len = oTD.length;
		var len=oTD.length;
		var borderStyle = '#ffffff 1px solid';
		var borderStyle2 = '1px solid #000000';
		var borderStyle3 = '0px solid #ffffff';
			
	
		if (bool){
			
			for(var i =0; i < len ; i++){
				if(i == 0){
					if(window.addEventListener) oTD[i].setAttribute('class','calendarWeekHeadOn'); // 호환성 작업 2011-04-05 kbk
					else oTD[i].className = 'calendarWeekHeadOn';
					//oTD[i].className = 'calendarWeekTailOn';
				}else if(i == 7){
					if(window.addEventListener) oTD[i].setAttribute('class','calendarWeekTailOn'); // 호환성 작업 2011-04-05 kbk
					else oTD[i].className = 'calendarWeekTailOn';
					//oTD[i].className = 'calendarWeekTailOn';
				}else{
					if(window.addEventListener) oTD[i].setAttribute('class','calendarWeekMiddleOn'); // 호환성 작업 2011-04-05 kbk
					else oTD[i].className = 'calendarWeekMiddleOn';
					//oTD[i].className = 'calendarWeekMiddleOn';
				}
		
			}
		
		}else{
		
			for(var i =0; i < len ; i++){
				if(i == 0){
					if(window.addEventListener) oTD[i].setAttribute('class','calendarWeekHeadOff'); // 호환성 작업 2011-04-05 kbk
					else oTD[i].className = 'calendarWeekHeadOff';
					//oTD[i].className = 'calendarWeekHeadOff';
				}else if(i == 7){
					if(window.addEventListener) oTD[i].setAttribute('class','calendarWeekTailOff'); // 호환성 작업 2011-04-05 kbk
					else oTD[i].className = 'calendarWeekTailOff';
					//oTD[i].className = 'calendarWeekTailOff';
				}else{
					if(window.addEventListener) oTD[i].setAttribute('class','calendarWeekMiddleOff'); // 호환성 작업 2011-04-05 kbk
					else oTD[i].className = 'calendarWeekMiddleOff';
					//oTD[i].className = 'calendarWeekMiddleOff';
				}
				
			}
		
		}
	
	}
}