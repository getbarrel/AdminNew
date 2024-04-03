var SelectReport = 1;
var vNewdate = "";
function rollon() {
	oevent = window.event.srcElement.className;
		if (oevent == 'calendar' || oevent == 'calendarWeekHeadOn' || oevent == 'calendarWeekMiddleOn' || oevent == 'calendarWeekTailOn') {			
			window.event.srcElement.className = 'calendar_on'; // 클래스 변경
		}
}

function rolloff() {
	oevent = window.event.srcElement.className;
		if (oevent == 'calendar_on' || oevent == 'calendarWeekHeadOff' || oevent == 'calendarWeekMiddleOff' || oevent == 'calendarWeekTailOff') {
			window.event.srcElement.className = 'calendar'; // 클래스 변경
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
		document.frames['act'].location.href=LinkPage+'?mode=iframe&vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport;	
	//	document.location.href=LinkPage+'?vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport
	}else if(SelectReport == 2){
		document.frames['act'].location.href=LinkPage+'?mode=iframe&vdate='+printWeek(vDate)+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport;
	//	document.location.href=LinkPage+'?vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport
	}else if(SelectReport == 3){
		document.frames['act'].location.href=LinkPage+'?mode=iframe&vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport;
	//	document.location.href=LinkPage+'?vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport
	}
}

function ViewTreeReport(LinkPage, vDate,vName, vreferid,vdepth){
	if(vNewdate != ""){
		vDate = vNewdate;
	}
	
	if(SelectReport == 1){
		//document.location.href=LinkPage+'?vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport+'&depth='+vdepth+'&referer_id='+vreferid;	
		document.frames['act'].location.href=LinkPage+'?mode=iframe&vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport+'&depth='+vdepth+'&referer_id='+vreferid;	
	}else if(SelectReport == 2){
		//document.location.href=LinkPage+'?vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport+'&depth='+vdepth+'&referer_id='+vreferid;
		document.frames['act'].location.href=LinkPage+'?mode=iframe&vdate='+printWeek(vDate)+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport+'&depth='+vdepth+'&referer_id='+vreferid;
	}else if(SelectReport == 3){
		document.frames['act'].location.href=LinkPage+'?mode=iframe&vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport+'&depth='+vdepth+'&referer_id='+vreferid;
	//	document.location.href=LinkPage+'?vdate='+vDate+'&SubID='+SMinitiallyOpenSub11464+'&SelectReport='+SelectReport
	}
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
	alert(nowDayOfWeek);
	var weekEndDate = new Date(nowYear, nowMonth, nowDay + (6 - nowDayOfWeek));
//	document.write('이번주는 :  ' + formatDate(weekStartDate) + ' - ' + formatDate(weekEndDate) + ' 까지 입니다');
}


function ChangeCalenderView(vSelectReport){
	var oTD;
	var len;
	
	if(vSelectReport == 1 || vSelectReport == 3){
		try{
			for(seq = 0; seq < 6; seq++){
				oTD = eval('document.all.week' + seq);
				len = oTD.length;
					
				for(var i =0; i < len ; i++){				
						oTD[i].className = 'calendar';				
				}
			}
		}catch(e){
			
		}
	}else{
		try{
			for(seq = 0; seq < 6; seq++){
				oTD = eval('document.all.week' + seq);
				len = oTD.length;
					
				for(var i =0; i < len ; i++){
					if(i == 0){
						oTD[i].className = 'calendarWeekHeadOff';
					}else if(i == 7){
						oTD[i].className = 'calendarWeekTailOff';
					}else{
						oTD[i].className = 'calendarWeekMiddleOff';
					}
				}
			}
		}catch(e){
			
		}
	}
	
}


function mouseOnTD2(seq, bool)
{
	if(SelectReport == 1){
		
		if (bool){
			rollon();
		}else{
			rolloff();
		}
	}else if(SelectReport == 2){
			
	
		var oTD = eval('document.all.week' + seq);
		var len = oTD.length;
		var borderStyle = '#ffffff 1px solid';
		var borderStyle2 = '1px solid #000000';
		var borderStyle3 = '0px solid #ffffff';
			
		if (bool){
			
			for(var i =0; i < len ; i++){
				if(i == 0){
					oTD[i].className = 'calendarWeekHeadOn';
				}else if(i == 7){
					oTD[i].className = 'calendarWeekTailOn';
				}else{
					oTD[i].className = 'calendarWeekMiddleOn';
				}
		
			}
		
		}else{
		
			for(var i =0; i < len ; i++){
				if(i == 0){
					oTD[i].className = 'calendarWeekHeadOff';
				}else if(i == 7){
					oTD[i].className = 'calendarWeekTailOff';
				}else{
					oTD[i].className = 'calendarWeekMiddleOff';
				}
				
			}
		
		}
	
	}
}