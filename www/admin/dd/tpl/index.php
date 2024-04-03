<?

$ary_search_type = array();
$ary_search_type['con_id'] = '접속 ID';
$ary_search_type['ip'] = '접속 IP';

?>
<table>
<tr>
<td style='width:30%'>조건검색</td>
<td>
<select name='search_type'>
<?=options($ary_search_type);?>
<input type='text' name='search_text' value='' />
</select>
</td>
</tr>
<tr>
<td colspan='2' style='align:center'><input type='submit' name='' value='검색' /></td>
</tr>
</table>
<br />
<table style='width:500px'>
<?if ( sizeof( $rows ) > 1 ) {?>
<tr>
<td>번호</td>
<td>업체명</td>
<td>접속ID</td>
<td>로그시간</td>
<td>로그정보</td>
<td>접속IP</td>
</tr>
<?

	$i =1;
	foreach($rows as $k=>$v)
	{
	$log_div  = ($v['log_div']=='I') ? '로그인' : '로그아웃'; 

?>
	<tr>
	<td><?=$i++?></td>
	<td><?=$v['con_name']?></td>
	<td><?=$v['con_id']?></td>
	<td><?=$v['log_date']?></td>
	<td><?=$log_div?></td>
	<td><?=$v['ip']?></td>
	</tr>
<?
	}
?>

<?} else {?>
<tr>
<td colspan='7'></td>
</tr>
<?}?>
</table>