
function makeMonth(yyyymm, cnt)
{
	var  str1	= new String(yyyymm);

	var curYear	= eval(Number(str1.substr(0,4)));
	var curMonth	= eval(Number(str1.substr(4,2)));// 파폭에서는 숫자의 맨앞에 0 이 올경우 8진수로 인식(?) 그래서 Number 처리함 2011-04-06 kbk
	var reqMonth	= eval(cnt);
	var MakeMonth;

	if ( curMonth <= reqMonth-1){
		MakeMonth = curMonth + 12 - reqMonth + 1;
		curYear   = curYear-1;
	}
	else
	{
		MakeMonth = (curMonth - reqMonth + 1)%12;
	}
		var aaa = curYear;
		var bbb = toZero(MakeMonth);

	return aaa + bbb;
}


function LoadValues(fYear, fMonth, fDay, createdate)
{	
	var str1		= new String(createdate);
	var Today;
	var curYear	= eval(Number(str1.substr(0,4)));
	var curMonth	= eval(Number(str1.substr(5,2)));
	var curDate	= eval(Number(str1.substr(8,2)));

	var i;
	var nStep;

	// Year설정
	nStep=0;

	fYear.options[nStep]	= new Option("", "", false, false);

	nStep++;
	if(fYear.name=="birYY") { // 생일에 대한 구분 kbk
		var start_year=curYear-99;
		var current_year=curYear;
	} else {
		var start_year=2000;
		var current_year=curYear+10;
	}
	for(i=start_year; i<=current_year; i++)
	{
		fYear.options[nStep] = new Option(i, i, false, false);
		nStep++;
	}

	fYear.options[0].selected = true;


	// Month 설정
	nStep=0;

	fMonth.options[nStep] = new Option("", "", false, false);

	nStep++;

	for(i=1; i<=12; i++)
	{
		fMonth.options[nStep] = new Option(i, toZero(i), false, false);
		nStep++;
	}

	fMonth.options[0].selected = true;

	// Date 설정
	var LastDay = checkLastDay(curYear, curMonth);
	nStep=0;

	if(fDay != null) {
		fDay.options[nStep] = new Option("", "", false, false);
		nStep++;

		for(i=1; i<=LastDay; i++)
		{
			fDay.options[nStep] = new Option(i, toZero(i), false, false);
			nStep++;
		}
		fDay.options[0].selected			= true;
	}

}


function checkLastDay(Year2, Month2)
{
	var Days = 31;

	if ( (Month2 == 1) || (Month2 == 3) || (Month2 == 5) || (Month2 == 7)
		|| (Month2 == 8) || (Month2 == 10) || (Month2 == 12) )
	{
		Days = 31
	}
	else if( (Month2 == 4) || (Month2 == 6) || (Month2 == 9) || (Month2 == 11) )
	{
		Days = 30;
	}
	else
	{
		Days= 28;
		if(Year2 % 4 == 0)
		{
			Days= 29;
			if(Year2 % 100 == 0)
			{
				Days= 28;
				if(Year2 % 400 == 0)
				{
					Days= 29;
				}
			}
		}
	}

	return Days;
}


function onChangeWeek(fYear, fMonth, fWeek)
{
	
	var Days, DayOfWeek, WeekCount;
	var i;
	var curYear, curMonth;
	
	curYear = fYear.options[fYear.selectedIndex].value;
	curMonth= fMonth.options[fMonth.selectedIndex].value;
	

	// 2000/1/1은 토요일
	DayOfWeek = 6;
	for(i=2000; i<curYear; i++)
	{
		Days = 1;
		if(i % 4 == 0)
		{
			Days = 2;
			if(i % 100 == 0)
			{
				Days= 1;
				if(i % 400 == 0)
				{
					Days= 2;
				}
			}
		}
		
		DayOfWeek = DayOfWeek + Days;
		DayOfWeek = DayOfWeek%7;
	}
	
	for(i=1; i<curMonth; i++)
	{
		Days = checkLastDay(curYear, i);
		DayOfWeek = DayOfWeek + Days;
		DayOfWeek = DayOfWeek%7;
	}
	
	WeekCount = 5;
	if(checkLastDay(curYear, curMonth) == 28)
	{
		if(DayOfWeek == 0)
			WeekCount = 4;
	}
	else if(checkLastDay(curYear, curMonth) == 30)
	{
		if(DayOfWeek == 6)
			WeekCount = 6;
	}
	else if(checkLastDay(curYear, curMonth) == 31)
	{
		if(DayOfWeek == 5 || DayOfWeek == 6)
			WeekCount = 6;
	}

	for(i=0; i<WeekCount; i++)
	{
		if(fWeek.options[i] != null)
			fWeek.options[i] = null;
	}
	
	
	var nStep=0;
    fWeek.options[nStep]    = new Option("", "", false, false);

    nStep++;
    for(i=1; i<=WeekCount; i++)
    {
        fWeek.options[nStep] = new Option(i, i, false, false);
        nStep++;
    }
    fWeek.options[0].selected = true;
    return;
}

function onChangeDate(fYear, fMonth, fDay)
{
	// Date 설정
	var curYear;
	var curMonth;
	var curDate;
	var nStep;
	var bcheckLastDay;
	var LastDay;

	curYear = fYear.options[fYear.selectedIndex].value;
	curMonth= fMonth.options[fMonth.selectedIndex].value;

	if(curYear == "")
	{
		fMonth.options[0].selected = true;
		if(fDay != null)
			fDay.options[0].selected = true;
		return ;
	}

	if(curMonth == "")
	{
		if(fDay != null)
			fDay.options[0].selected = true;
		return ;
	}

	if(fDay != null) {
		curDate = fDay.options[fDay.selectedIndex].value;

		LastDay = checkLastDay(curYear, curMonth);

		for(i=0; i<31; i++)
		{
			if(fDay.options[i] != null)
				fDay.options[i] = null;
		}

		nStep = 0;
		bcheckLastDay = false;
		fDay.options[nStep] = new Option("", "", false, false);
		nStep++;
		for(i=1; i<=LastDay; i++)
		{
			fDay.options[nStep] = new Option(i, toZero(i), false, false);
			if(curDate == i)
			{
				fDay.options[nStep].selected = true;
				bcheckLastDay = true;
			}
			else
				fDay.options[nStep].selected = false;
			nStep++;
		}
	}	// end of if
}

function toZero(str)
{
	if (eval(str<10))
	{
		str = "0" + str;
	}
	else
	{
		str = "" + str;
	}
	return str;
}

function LoadValuesAuction( fYear, fMonth, fDay, fHour, fMinute,createdate)
{
	var str1 = new String(createdate);

	var Today;
	var curYear	= eval(Number(str1.substr(0,4)));
	var curMonth	= eval(Number(str1.substr(5,2)));
	var curDate	= eval(Number(str1.substr(8,2)));

	var i;
	var nStep;

	// Year설정
	nStep=0;

	fYear.options[nStep] = new Option("", "", false, false);

	nStep++;

	for(i=2000; i<=curYear+10; i++)
	{
		fYear.options[nStep] = new Option(i, i, false, false);
		nStep++;
	}

	fYear.options[0].selected = true;


	// Month 설정
	nStep=0;

	fMonth.options[nStep] = new Option("", "", false, false);

	nStep++;

	for(i=1; i<=12; i++)
	{
		fMonth.options[nStep] = new Option(i,toZero(i),false, false);
		nStep++;
	}

	fMonth.options[0].selected = true;

	// Date 설정
	var LastDay = checkLastDay(curYear, curMonth);
	nStep=0;

	fDay.options[nStep] = new Option("", "", false, false);
	nStep++;

	for(i=1; i<=LastDay; i++)
	{
		fDay.options[nStep] = new Option(i, toZero(i), false, false);
		nStep++;
	}
	fDay.options[0].selected	= true;

	// Hour 설정
	nStep=0;

	fHour.options[nStep] = new Option("", "", false, false);

	nStep++;

	for(i=1; i<=24; i++)
	{
		fHour.options[nStep] = new Option(i, toZero(i), false, false);
		nStep++;
	}
	fHour.options[0].selected = true;

	// Minute 설정
	nStep=0;

	fMinute.options[nStep] = new Option("", "", false, false);

	nStep++;

	for(i=0; i<=60; i++)
	{
		fMinute.options[nStep] = new Option(toZero(i),toZero(i),false, false);
		nStep++;
	}
	fMinute.options[0].selected	= true;
}

function initdate(oYear,oMonth,oDay, strDate) {
//	var frm = document.form2;
//alert(strDate);
	
	for(i=0; i<oYear.length; i++) {
		if(oYear.options[i].value == strDate.substring(0,4))
			oYear.options[i].selected=true
	}
	for(i=0; i<oMonth.length; i++) {
		if(oMonth.options[i].value == strDate.substring(5,7))
			oMonth.options[i].selected=true
	}
	for(i=0; i<oDay.length; i++) {
		if(oDay.options[i].value == strDate.substring(8,10))
			oDay.options[i].selected=true
	}
	
	
}

