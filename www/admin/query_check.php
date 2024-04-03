<?php
/**
 * 사이트 쿼리 사용 정보
 *
 * @author		Caesar <ddong0927@naver.com>
 * @copyright	2007-2011 ForBiz
 * @version		1.0
 * @package
 */
require_once($_SERVER['DOCUMENT_ROOT'].'/class/database.class');
$db = new Database;
$db->dbcon($db->db_name);

$page	= (empty($_GET['page']))	?	1:$_GET['page'];
$count	= (empty($_GET['count']))	?	20:$_GET['count'];

// total record
$sql	= 'SELECT COUNT(*) FROM caesar.useQuery uq '.$join_query.$search_query;

$offset	= ($page - 1) * $count;
$sql	= 'SELECT uq.* FROM caesar.useQuery uq '.$join_query.$search_query.' LIMIT '.$count.' OFFSET '.$offset;
$result	= mysql_query($sql, $db->dbcon) or die (mysql_error());
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<style>
<!--
*{margin:0;padding:0;font-size:12px;line-height:16px;font-family:"Malgun Gothic","돋움",Dotum,"굴림",Gulim,"Lucida Grande",AppleGothic,Sans-serif;color:#5D5D5D;}
body,html{height:100%;max-height:100%;}
body{*word-break:break-all;-ms-word-break:break-all;overflow:auto;margin:10px;}
fieldset,img,iframe{border:0 none;}
hr{display:none;}
dl,ul,ol,li{list-style:none;}

input,select,label,img{vertical-align:middle;}
label{cursor:pointer;}

a,a:link,a:active,a:visited{text-decoration:none;}
a:hover{text-decoration:underline;}

table{table-layout:fixed;min-width:100%;max-width:100%;width:100%;border-collapse:collapse;}
nobr{width:100%; overflow:hidden; text-overflow:ellipsis; white-space: nowrap;}
div.nobr{text-overflow:ellipsis; overflow:hidden;white-space: nowrap;}
table th{font-weight:bold;text-align:center;padding:3px;color:#000;background-color:#EBEBEB;}
table td{padding:3px;text-align:center;height:25px;}
.line01{height:0px;border-bottom:2px solid #C3C3C3;}
//-->
</style>
<title>MallStory Using Query List</title>

</head>
<body>

<table cellpadding="0" cellspacing="0" border="1" bordercolor="#CBCBCB">
<col width="100" />
<col width="100" />
<col width="100" />
<col width="100" />
<col width="*" />
<col width="100" />
<col width="100" />
<col width="100" />
	<tr>
		<td colspan="8" class="line01"></td>
	</tr>
	<tr>
		<th>대분류</th>
		<th>디렉토리</th>
		<th>타입 위험도</th>
		<th>rows 위험도</th>
		<th>파일</th>
		<th>쿼리</th>
		<th>실행계획</th>
		<th>확인여부</th>
	</tr>
<?php
while($row = mysql_fetch_assoc($result))	:
	$tmpLoc = explode('/', $row['uq_location']);
	if($tmpLoc[1] == 'admin')	{
		$ct = '관리자';
		$cd = $tmpLoc[2];
	}	else	{
		$ct = '프론트';
		$cd = $tmpLoc[1];
	}

	//
?>
	<tr>
		<td><?=$ct?></td>
		<td><?=$cd?></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<th>쿼리</th>
		<td colspan="7" style="text-align:left;"><?=$row['uq_sql']?></td>
	</tr>
<?php
endwhile;
?>
</table>



</body>
</html>