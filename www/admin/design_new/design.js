
{ // 변수 정의

	var isDOM = ( document.getElementById ? true : false );	// 브라우저 종류 체크
	var isIE4 = ( document.all ? true : false );
	var isNS4 = ( !document.all ? true : false );

	var textarea_copy_body	= 'copy_body';
	var textarea_user_body	= 'user_body';
	var textarea_base_body	= 'base_body';
	var textarea_user_view	= 'user_view';
	var textarea_base_view	= 'base_view';

	var textarea_view_id	= textarea_user_body;
}


/*-------------------------------------
 TEXTAREA 탭키 사용가능
-------------------------------------*/
function textarea_useTab( el, e ){
	
	e = (e) ? e : ((event) ? event : null );

	if ( isNS4 == true );
	else {

		if ( event.shiftKey == false && 9 == event.keyCode ){

			var t = ( el.selection = document.selection.createRange() );

			if ( t.text == '' ){
				t.text = '\t';
			}
			else {

				var str = '\t' + t.text.replace( /\n/gi, '\n\t' );
				t.text = str;
			}

			event.returnValue = false;
		}
		else if ( event.shiftKey == true && 9 == event.keyCode ){

			var t = ( el.selection = document.selection.createRange() );

			if ( t.text != '' ){

				var str = t.text.replace( /^\t/gi, '' );
				str = str.replace( /\n\t/gi, '\n' );
				t.text = str;
			}

			event.returnValue = false;
		}
	}
}




/* ----------------------------------------
	아래는 사용하지 않음. 참고 자료
 ----------------------------------------*/






/*-------------------------------------
 소스보기 선택처리
-------------------------------------*/
function textarea_view( obj ){

	if ( obj.id == textarea_base_view ){

		textarea_view_id = textarea_base_body;

		document.getElementById( textarea_user_body ).style.display = 'none';
		document.getElementById( textarea_base_body ).style.display = 'block';

		document.getElementById( textarea_user_view ).style.color = '#FFFFFF';
		document.getElementById( textarea_user_view ).style.background = '#7F7F7F';

		document.getElementById( textarea_base_view ).style.color = '#222222';
		document.getElementById( textarea_base_view ).style.background = '#ECE9D8';

	}
	else {

		textarea_view_id = textarea_user_body;

		document.getElementById( textarea_user_body ).style.display = 'block';
		document.getElementById( textarea_base_body ).style.display = 'none';

		document.getElementById( textarea_user_view ).style.color = '#222222';
		document.getElementById( textarea_user_view ).style.background = '#ECE9D8';

		document.getElementById( textarea_base_view ).style.color = '#FFFFFF';
		document.getElementById( textarea_base_view ).style.background = '#7F7F7F';
	}
}



/*-------------------------------------
 기본소스 입력
-------------------------------------*/
function codeBaseInput( CObj, auto ){

	var idObj = document.getElementById('resetting');

	var codyObj = document.getElementById( textarea_copy_body );
	var userObj = document.getElementById( textarea_user_body );
	var baseObj = document.getElementById( textarea_base_body );

	if ( CObj.checked ){

		if ( baseObj.value == '' ){

			if ( auto != true ) alert( "본 위치는 기본값을 지원하지 않습니다." );
			CObj.checked = false;
			idObj.style.color = '#000000';
			idObj.style.fontWeight = 'normal';
		}
		else {

			codyObj.value = userObj.value;
			userObj.value = baseObj.value;
			idObj.style.color = '#bf0000';
			idObj.style.fontWeight = 'bold';
		}
	}
	else {

		userObj.value = codyObj.value;
		idObj.style.fontColor = '#000000';
		idObj.style.color = '#000000';
		idObj.style.fontWeight = 'normal';
	}

	textarea_view( userObj );
}



/*-------------------------------------
 TEXTAREA 줄수 조절 시작
-------------------------------------*/

var control_stop = 1;

function row_start(){
	control_stop = 0;
}



/*-------------------------------------
 TEXTAREA 줄수 조절 멈춤
-------------------------------------*/
function row_stop(){
	control_stop = 1;
}



/*-------------------------------------
 TEXTAREA 줄수 조절
-------------------------------------*/
function row_control( plug ){

	var TObj = eval( "document.getElementById( '" + textarea_view_id + "' )" );

	if ( control_stop != 1 && ( plug == '+' || plug == '-' ) ){

		if ( plug == '+' && TObj.rows >= 40 ){

			alert( "40라인 까지만 증가할 수 있습니다." );
			row_stop();
			return;
		}
		else if ( plug == '-' && TObj.rows <= 1 ){

			alert( "1라인 까지만 감소할 수 있습니다." );
			row_stop();
			return;
		}


		TObj.rows = eval( "TObj.rows " + plug + " 1" );
		setTimeout( "row_control( '"  + plug + "' )", 100 );
	}
	else {
		row_stop();
		return;
	}
}



/*-------------------------------------
 TEXTAREA 줄수 변경
-------------------------------------*/
function row_direct( num ){

	var TObj = eval( "document.getElementById( '" + textarea_view_id + "' )" );
	TObj.rows = num;
}



/*-------------------------------------
 TEXTAREA 줄바꿈 설정/해지
-------------------------------------*/
function textarea_wrap(){

	if ( isNS4 == true ) alert( '익스플로러에서만 지원됩니다.' );
	else {

		var TObj = eval( "document.getElementById( '" + textarea_view_id + "' )" );

		if ( TObj.wrap == 'off' ) TObj.wrap = 'soft';
		else TObj.wrap = 'off';
	}
}





/*-------------------------------------
 코디소스입력
-------------------------------------*/
function put_codi(){

	var userObj = document.getElementById( textarea_user_body );

	userObj.value = "{ # header }" + "\n\n" + "{ # footer }" + "\n" + userObj.value;
}



//파일 새창으로 열기
function open_window(page){
var y = screen.availHeight - 100
var x = screen.availWidth - 100
var win_option = "top=50, left=50, scrollbars=yes, width="+x+", height="+y+", resizable=yes"
window.open(page, 'open_win' , win_option)
}


// Copy displayed code to user's clipboard.
function copy2Clipboard(obj)
{
  var textRange = document.body.createTextRange();
  textRange.moveToElementText(obj);
  textRange.execCommand("Copy");
}