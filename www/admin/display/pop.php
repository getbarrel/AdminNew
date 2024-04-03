<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
$db = new Database;

$db->query("SELECT popup_ix,popup_title, popup_text FROM ".TBL_SHOP_POPUP." where popup_ix='$no'  ");

$db->fetch(0);
?>
<html>
<head>
<title><?=$db->dt["popup_title"]?></title>
</head>

<SCRIPT language="JavaScript">
<!--
function setCookie( name, value, expiredays )
{
	var todayDate = new Date();
	todayDate.setDate( todayDate.getDate() + expiredays );
	document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";"
	}

function closeWin()
{
        if ( document.forms[0].Notice.checked ){

              //  setCookie( "noticeDayClose", "done" , 1);
                setCookie( "Notice<?=$no?>", "done" , 1);
		}
        self.close();
}

function closeWindow(mainLink) {
  	opener.location.href = mainLink;
	  window.close();
}

function closeWindow_n() {
    window.close();
}
//-->
</script>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<TABLE WIDTH='100%' HEIGHT='100%' BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD>
		<?=$db->dt["popup_text"]?>
		
		</TD>
	</TR>
	

<form  id=form1 name=form1>
	<tr height=30><td align=center bgcolor=silver><input type="CHECKBOX" name="Notice" value="" OnClick="javascript:closeWin();" /><font size="2" color="#000000">오늘 하루 동안 열지않기</font></td></tr>
</form>
</table>
</body>
</html>


