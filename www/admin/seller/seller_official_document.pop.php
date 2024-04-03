<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
$db = new Database;

$db->query("SELECT * FROM seller_official_popup where popup_ix='".$no."' ");

$db->fetch(0);

if($db->dt["popup_div"] == '2'){

 $popup_div= '공문서';

} else{
 $popup_div= '동의서';
}



?>

<html>
<head>
<title><?=$db->dt["popup_title"]?></title>
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
</head>

<SCRIPT language="JavaScript">
<!--

var win;
function pop() {
win = window.open();
}

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

function closeWin_of()
{
        if ( document.forms[0].popup_confirm.checked ){

              //  setCookie( "noticeDayClose", "done" , 1);
                setCookie( "Notice<?=$no?>", "done" , 1);
		}
        self.close();
}

function closeWindow(mainLink) {
  	opener.location.href = mainLink;
	  window.close();
}


function close_self(){

	document.form1.submit();
	//window.close();

}

function closeWindow_n() {
    window.close();
}
//-->
</script>
<style type="text/css">
	body, html { overflow-y:auto; height:100%; }
	.pop_tbl_area { margin:10px; border:2px solid #dadada; border-bottom:1px solid #dadada; }
	.pop_tbl { }
	.pop_tbl th, .pop_tbl td { padding:10px 7px; border-bottom:1px solid #dadada; }
	.pop_tbl th { background:#f0f0f0; border-right:1px solid #dadada; }
	.pop_tbl td { padding-left:10px; }
</style>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="background-color:white;">

<div class="admin_pop_wrap">
	<div class="pop_tbl_area">
		<table width='100%' border='0' cellpadding=0 cellspacing=0 class="pop_tbl">
			<colgroup>
				<col width="10%" />
				<col width="" />
			</colgroup>
			<tr>
				<th>
				제  목
				
				</th>
				<TD>
				<?=$db->dt["popup_title"]?>
				
				</TD>
			</tr>
			<tr>
				<th>
				구  분
				
				</th>
				<TD>
				<?=$popup_div?>
				
				</TD>
			</tr>
			<tr>
				<th>
				번  호
				
				</th>
				<TD>
				<?=$db->dt["popup_ix"]?>
				
				</TD>
			</tr>
			<tr>
				<th>
				기  간
				
				</th>
				<TD>
				<?=substr($db->dt[popup_use_sdate], 0, 10)?> ~ <?=substr($db->dt[popup_use_edate], 0, 10)?>
				
				</TD>
			</tr>
			<tr>
				<th valign="top">
				내  용
				</th>
				<TD>
				<?=
				$contents = str_replace("{seller_name}", $_SESSION['admininfo']['company_name'], $db->dt["popup_text"]);
				
				$contents;?>
				
				</TD>
			</tr>
<?
	if(!empty($db->dt['popup_file'])){
		echo "
			<tr>
				<th>
				첨부파일 
				</th>
				<td><a href='seller_official_document.down.php?popup_ix=".$db->dt[popup_ix]."&file_name=".urlencode($db->dt['popup_file'])."'>".$db->dt['popup_file']."</a></td>
			</tr>";
	} else{

	}
?>
		</table>
	</div>

	<table width='100%' border='0' cellpadding=0 cellspacing=0 class="pop_tbl">
		<form  id=form1 name=form1 action='seller_official_document.act.php' target="iframe_act" method='post'>
		<input type='hidden' name='act' value='confirm_yn'>
		<input type='hidden' name='popup_ix' value='<?=$db->dt["popup_ix"]?>'>
		<input type='hidden' name='charger_ix' value='<?=$admininfo['charger_ix']?>'>
		<input type='hidden' name='seller_id' value='<?=$admininfo['charger_id']?>'>
		<input type='hidden' name='popup_ip' value='<?=$_SERVER['REMOTE_ADDR']?>'>
		<tr>
			<TD colspan=2 align=center>
			<? if($db->dt["popup_div"] == '2'){?>
				<input type="CHECKBOX" id="popup_confirm" name="popup_confirm" value="1"/><label for="popup_confirm">내용을 확인하였습니다</label></br>
				<input type='image' id=-'btn_confirm' src='../images/<?=$admininfo["language"]?>/bt_ok.gif' Onclick='close_self();'>
			<? } else{ ?>
                <input type="radio" id="popup_confirm_1" name="popup_confirm" value="1"/><label for="popup_confirm_1">동의</label>
				<input type="radio" id="popup_confirm_0" name="popup_confirm" value="0"/><label for="popup_confirm_0">미동의</label></br></br>
				<input type='image' id='btn_confirm' src='../images/<?=$admininfo["language"]?>/bt_ok.gif' Onclick='close_self();'>
			<? }?>
			</TD>
		</tr>
		<? if($db->dt[popup_today] == '1'){?>
		<tr height=30>
			<td colspan=2 align=center bgcolor=silver>
				<input type="CHECKBOX" name="Notice" value="" OnClick="javascript:closeWin();" /><font size="2" color="#000000">오늘 하루 동안 열지않기</font>
			</td>
		</tr>
		<? } else{

			} ?>
		</form>
	</table>
	<iframe name='iframe_act' id='iframe_act' style='display:none;'></iframe>
</div>
</body>
</html>
