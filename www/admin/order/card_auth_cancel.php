<html>
<head>
<title>승인취소 최종확인</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel="stylesheet" type="text/css" href="../include/admin.css">
</head>

<script language=javascript>
	function auth_cancel(tid)
	{
		location.href = '/shop/inicis/securepay_cancel.php?mid=mytesoro00&tid=' + tid + '&merchantreserved=예비필드&msg=고객요청';
	}
</script>

<body bgcolor=white topmargin=0 leftmargin=0 marginheight=0>

<form>
<table border=0 width=100%>
	<tr>
		<td align=center>
			<br>
			승인을 취소할 경우 재승인 받으려면 다시 주문해야 합니다.
			<br>
			정말로 승인을 취소 하시겠습니까?
		</td>
	</tr>
	<tr>
		<td align=center>
			<input type=button class=button value="승인취소" onClick="auth_cancel('<?=$tid?>');">
			<input type=button class=button value="취소안함" onClick="self.close();">
		</td>
	</tr>
</table>
</form>

</body>

</html>
