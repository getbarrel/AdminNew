<!--

var n4 = (document.layers)?true:false;
var e4 = (document.all)?true:false;

//숫자만입력(onKeypress='return keyCheckdot(event)')
function keyCheck(e) { 
if(n4) var keyValue = e.which
else if(e4) var keyValue = event.keyCode
if (((keyValue >= 48) && (keyValue <= 57)) || keyValue==13) return true; 
else return false
}

//숫자및돗트입력(onKeypress='return keyCheckdot(event)')
function keyCheckDot(e) { 
if(n4) var keyValue = e.which
else if(e4) var keyValue = event.keyCode
if (((keyValue >= 48) && (keyValue <= 57)) || keyValue==13 || keyValue==46) return true; 
else return false
}

//공백제거
function Trim(string) {
for(;string.indexOf(" ")!= -1;){
string=string.replace(" ","")
}
return string;
}

//입력검사
function Exists(input,types) {
if(types) if(!Trim(input.value)) return false;
return true;
}

//영문검사+숫자검사(첫글자는 반드시영문)
function EngNum(input,types) {
if(types) if(!Trim(input.value)) return false;
var error_c=0, i, val;
for(i=0;i<Byte(input.value);i++) {
val = input.value.charAt(i);
if(i == 0) if(!((val>='a' && val<='z') || (val>='A' && val<='Z'))) return false;
else if(!((val>=0 && val<=9) || (val>='a' && val<='z') || (val>='A' && val<='Z'))) return false;
}
return true;
}

//영문검사+숫자검사
function EngNumAll(input,types) {
if(types) if(!Trim(input.value)) return false;
var error_c=0, i, val;
for(i=0;i<Byte(input.value);i++) {
val = input.value.charAt(i);
if(!((val>=0 && val<=9) || (val>='a' && val<='z') || (val>='A' && val<='Z'))) return false;
}
return true;
}

//영문검사+숫자검사+'_'
function EngNumAll2(input,types) {
if(types) if(!Trim(input.value)) return false;
var error_c=0, i, val;
for(i=0;i<Byte(input.value);i++) {
val = input.value.charAt(i);
if(!((val>=0 && val<=9) || (val>='a' && val<='z') || (val>='A' && val<='Z') || val=='_')) return false;
}
return true;
}

//영문검사
function Eng(input,types) {
if(types) if(!Trim(input.value)) return false;
var error_c=0, i, val;
for(i=0;i<Byte(input.value);i++) {
val = input.value.charAt(i);
if(!((val>='a' && val<='z') || (val>='A' && val<='Z'))) return false;
}
return true;
}

//숫자만입력
/*
function numberonlyinput() {
var ob = event.srcElement;
ob.value = noSplitAndNumberOnly(ob);
return false;
}
*/

//돈(3단위마다 컴마를 붙인다.)
function checkNumber() {
var ob=event.srcElement;
ob.value = filterNum(ob.value);
ob.value = commaSplitAndNumberOnly(ob);
return false;
}

//한정액(일정금액 이상이 되면 올라기지 않게 한다.)
function chkhando(money) {
var ob=event.srcElement;
ob.value = noSplitAndNumberOnly(ob);
if(ob.value > money) ob.value = money;
return false;
}

//이자율(소수점 사용가능)
function checkNumberDot(llen,rlen) {
if(llen == "") llen = 8;
if(rlen == "") rlen = 2;
var ob=event.srcElement;
ob.value = filterNum(ob.value);

spnumber = ob.value.split('.');
if( spnumber.length >= llen && (spnumber[0].length >llen || spnumber[1].length >llen)) {
ob.value = spnumber[0].substring(0,llen) + "." + spnumber[1].substring(0,rlen);
ob.focus();
return false;
}
else if( spnumber[0].length > llen ) {
ob.value = spnumber[0].substring(0,llen) + ".";
ob.focus();
return false;
}
else if(ob.value && spnumber[0].length == 0) {
ob.value = 0 + "." + spnumber[1].substring(0,rlen);
ob.focus();
return false;
}
ob.value = commaSplitAndAllowDot(ob);
return false;
}

//참조함수
function filterNum(str) {
re = /^\$|,/g;
return str.replace(re, "");
}

//참조함수(컴마불가)
function commaSplitAndNumberOnly(ob) {

var txtNumber = '' + ob.value;
if (isNaN(txtNumber) || txtNumber.indexOf('.') != -1 ) {
ob.value = ob.value.substring(0, ob.value.length-1 );
ob.value = commaSplitAndNumberOnly(ob);
ob.focus();
return ob.value;
}
else {
var rxSplit = new RegExp('([0-9])([0-9][0-9][0-9][,.])');
var arrNumber = txtNumber.split('.');
arrNumber[0] += '.';
do {
arrNumber[0] = arrNumber[0].replace(rxSplit, '$1,$2');
}
while (rxSplit.test(arrNumber[0]));

if (arrNumber.length > 1) {
return arrNumber.join('');
}
else {
return arrNumber[0].split('.')[0];
}
}
}

//참조함수(컴마가능)
function commaSplitAndAllowDot(ob) {

var txtNumber = '' + ob.value;
if (isNaN(txtNumber) ) {
ob.value = ob.value.substring(0, ob.value.length-1 );
ob.focus();
return ob.value;
}
else {
var rxSplit = new RegExp('([0-9])([0-9][0-9][0-9][,.])');
var arrNumber = txtNumber.split('.');
arrNumber[0] += '.';
do {
arrNumber[0] = arrNumber[0].replace(rxSplit, '$1,$2');
}
while (rxSplit.test(arrNumber[0]));

if (arrNumber.length > 1) {
return arrNumber.join('');
}
else {
return arrNumber[0].split('.')[0];
}
}
}

//숫자만가능
function noSplitAndNumberOnly(ob) {
var txtNumber = '' + ob.value;
if (isNaN(txtNumber) || txtNumber.indexOf('.') != -1 ) {
ob.value = ob.value.substring(0, ob.value.length-1 );
ob.focus();
return ob.value;
}
else return ob.value;
}


//바이트검사
function Byte(input) {
var i, j=0;
for(i=0;i<input.length;i++) {
val=escape(input.charAt(i)).length;
if(val== 6) j++;
j++;
}
return j;
}

//팝업메뉴
function popupmenu_show(layername, thislayer, thislayer2) {
thislayerfield.value = thislayer;
thislayerfield2.value = thislayer2;
var obj = document.all[layername];
var _tmpx,_tmpy, marginx, marginy;
_tmpx = event.clientX + parseInt(obj.offsetWidth);
_tmpy = event.clientY + parseInt(obj.offsetHeight);
_marginx = document.body.clientWidth - _tmpx;
_marginy = document.body.clientHeight - _tmpy ;
if(_marginx < 0) _tmpx = event.clientX + document.body.scrollLeft + _marginx ;
else _tmpx = event.clientX + document.body.scrollLeft ;
if(_marginy < 0) _tmpy = event.clientY + document.body.scrollTop + _marginy + 20;
else _tmpy = event.clientY + document.body.scrollTop ;
obj.style.posLeft = _tmpx - 5;
obj.style.posTop = _tmpy;

layer_set_visible(obj, true);
layer_set_pos(obj, event.clientX, event.clientY);
}
function layer_set_visible(obj, flag) {
if (navigator.appName.indexOf('Netscape', 0) != -1) obj.visibility = flag ? 'show' : 'hide';
else obj.style.visibility = flag ? 'visible' : 'hidden';
}
function layer_set_pos(obj, x, y) {
if (navigator.appName.indexOf('Netscape', 0) != -1) {
obj.left = x;
obj.top = y;
} else {
obj.style.pixelLeft = x + document.body.scrollLeft;
obj.style.pixelTop = y + document.body.scrollTop;
}
}


//페이지이동
function move(url) {
location.href = url;
}

//닫기
function toclose() {
self.close();
}

//위치변경
function winsize(w,h,l,t) {
if(window.opener) resizeTo(w,h);
}

//포커스위치
function formfocus(form) {
var len = form.elements.length;
for(i=0;i<len;i++) {
if((form.elements[i].type == "text" || form.elements[i].type == "password") && Trim(form.elements[i].value) == "") {
form.elements[i].value = "";
form.elements[i].focus();
break;
}
}
}

// 날짜,시간 format 함수 = php의 date()
function date(arg_format, arg_date) {
if(!arg_date) arg_date = new Date();

var M = new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
var F = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
var K = new Array("일","월","화","수","목","금","토");
var k = new Array("日","月","火","水","木","金","土");
var D = new Array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");
var l = new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
var o = new Array("전","후");
var O = new Array("오전","오후");
var a = new Array("am","pm");
var A = new Array("AM","PM");

var org_year = arg_date.getFullYear();
var org_month = arg_date.getMonth();
var org_date = arg_date.getDate();
var org_wday = arg_date.getDay();
var org_hour = arg_date.getHours();
var org_minute = arg_date.getMinutes();
var org_second = arg_date.getSeconds();
var hour = org_hour % 12; hour = (hour) ? hour : 12;
var ampm = Math.floor(org_hour / 12);

var value = new Array();
value["Y"] = org_year;
value["y"] = String(org_year).substr(2,2);
value["m"] = String(org_month+1).replace(/^([0-9])$/,"0$1");
value["n"] = org_month+1;
value["d"] = String(org_date).replace(/^([0-9])$/,"0$1");
value["j"] = org_date;
value["w"] = org_wday;
value["H"] = String(org_hour).replace(/^([0-9])$/,"0$1");
value["G"] = org_hour;
value["h"] = String(hour).replace(/^([0-9])$/,"0$1");
value["g"] = hour;
value["i"] = String(org_minute).replace(/^([0-9])$/,"0$1");
value["s"] = String(org_second).replace(/^([0-9])$/,"0$1");
value["t"] = (new Date(org_year, org_month+1, 1) - new Date(org_year, org_month, 1)) / 86400000;
value["z"] = (new Date(org_year, org_month, org_date) - new Date(org_year, 0, 1)) / 86400000;
value["L"] = ((new Date(org_year, 2, 1) - new Date(org_year, 1, 1)) / 86400000) - 28;
value["M"] = M[org_month];
value["F"] = F[org_month];
value["K"] = K[org_wday];
value["k"] = k[org_wday];
value["D"] = D[org_wday];
value["l"] = l[org_wday];
value["o"] = o[ampm];
value["O"] = O[ampm];
value["a"] = a[ampm];
value["A"] = A[ampm];

var str = "";
var tag = 0;
for(i=0;i<arg_format.length;i++) {
var chr = arg_format.charAt(i);
switch(chr) {
case "<" : tag++; break;
case ">" : tag--; break;
}
if(tag || value[chr]==null) str += chr; else str += value[chr];
}

return str;
}

// 해상도에 맞는 크기 사용
function screensize() {
self.moveTo(0,0);
self.resizeTo(screen.availWidth,screen.availHeight);
}

// 주민등록번호체크( 입력폼 1개)
function check_jumin(jumin) { 
var weight = "234567892345"; // 자리수 weight 지정 
var val = jumin.replace("-",""); // "-"(하이픈) 제거 
var sum = 0; 

if(val.length != 13) { return false; } 

for(i=0;i<12;i++) { 
sum += parseInt(val.charAt(i)) * parseInt(weight.charAt(i)); 
} 

var result = (11 - (sum % 11)) % 10; 
var check_val = parseInt(val.charAt(12)); 

if(result != check_val) { return false; } 
return true; 
}

// 주민등록번호체크( 입력폼 2개)
function check_jumin2(input, input2) {
input.value=Trim(input.value);
input2.value=Trim(input2.value);
var left_j=input.value;
var right_j=input2.value;
if(input.value.length != 6) {
alert('주민등록번호를 정확히 입력하세요.');
input.focus();
return true;
}
if(right_j.length != 7) {
alert('주민등록번호를 정확히 입력하세요.');
input2.focus();
return true;
}
var i2=0;
for(var i=0;i<left_j.length;i++) {
var temp=left_j.substring(i,i+1);
if(temp<0 || temp>9) i2++;
}
if((left_j== '') || (i2 != 0)) {
alert('주민등록번호가 잘못 입력되었습니다.');
j_left.focus();
return true;
}
var i3=0;
for(var i=0;i<right_j.length;i++) {
var temp=right_j.substring(i,i+1);
if (temp<0 || temp>9) i3++;
}
if((right_j== '') || (i3 != 0)) {
alert('주민등록번호가 잘못 입력되었습니다.');
input2.focus();
return true;
}
var l1=left_j.substring(0,1);
var l2=left_j.substring(1,2);
var l3=left_j.substring(2,3);
var l4=left_j.substring(3,4);
var l5=left_j.substring(4,5);
var l6=left_j.substring(5,6);
var hap=l1*2+l2*3+l3*4+l4*5+l5*6+l6*7;
var r1=right_j.substring(0,1);
var r2=right_j.substring(1,2);
var r3=right_j.substring(2,3);
var r4=right_j.substring(3,4);
var r5=right_j.substring(4,5);
var r6=right_j.substring(5,6);
var r7=right_j.substring(6,7);
hap=hap+r1*8+r2*9+r3*2+r4*3+r5*4+r6*5;
hap=hap%11;
hap=11-hap;
hap=hap%10;
if(hap != r7) {
alert('주민등록번호가 잘못 입력되었습니다.');
input2.focus();
return true;
}
return false;
}

// 비밀번호 체크
function check_passwd(input, input2, min) {
if(!input.value) {
alert('비밀번호를 입력해 주십시오.');
input.focus();
return false;
}
else if(BYTE(input.value) < min) {
alert('비밀번호의 길이가 너무 짧습니다.');
input.focus();
input.value='';
input2.value='';
return false;
}
else if(!input2.value) {
alert('확인비밀번호를 입력해 주십시오.');
input2.focus();
return false;
}
else if(input.value != input2.value) {
alert('비밀번호가 서로 다르게 입력되었습니다.');
input2.value='';
input2.focus();
return false;
}
else return true; 
}

//콤마 넣기(정수만 해당) 
function comma(val) { 
val = get_number(val); 
if(val.length <= 3) return val; 

var loop = Math.ceil(val.length / 3); 
var offset = val.length % 3; 

if(offset==0) offset = 3; 
var ret = val.substring(0, offset); 

for(i=1;i<loop;i++) { 
ret += "," + val.substring(offset, offset+3); 
offset += 3; 
} 
return ret; 
} 

//문자열에서 숫자만 가져가기 
function get_number(str) { 
var val = str; 
var temp = ""; 
var num = ""; 

for(i=0; i<val.length; i++) { 
temp = val.charAt(i); 
if(temp >= "0" && temp <= "9") num += temp; 
} 
return num; 
}

//주민등록번호를 나이로 변환
function agechange(lno,rno) {
var refArray = new Array(18,19,19,20,20,16,16,17,17,18);
var refyy = rno.substring(0,1);
var refno = lno.substring(0,2);
var biryear = refArray[refyy] * 100 + eval(refno);

var nowDate = new Date();
var nowyear = nowDate.getYear();
return nowyear - biryear + 1;
}

//레디오박스 체크검사
function radio_chk(input, msg) {
var len = input.length;
for(var i=0;i<len;i++) if(input[i].checked == true && input[i].value) return true;
alert(msg);
return false;
}

//셀렉트박스 체크검사
function select_chk(input, msg) {
if(input[0].selected == true) {
alert(msg);
return false;
} 
return true;
}

//새창띄우기
function open_window(url, target, w, h, s) {
if(s) s = 'yes';
else s = 'no';
var its = window.open(url,target,'width='+w+',height='+h+',top=0,left=0,scrollbars='+s);
its.focus();
}
//-->



