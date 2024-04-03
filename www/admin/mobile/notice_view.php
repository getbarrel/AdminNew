<?
$script_time[start] = time();
include("../class/layout.class");
//include("../class/calender.class");
$script_time[start] = time();
//print_r($_SESSION);

$db = new Database; 

$Script = "
<script language='javascript' src='shop_main_v3_calender.js'></script>
";

$Contents01 = "
<h2 class='notice_view_h2'><span>게시물 상세 확인</span></h2>
<div class='notice_view'>
	<h3>회원정보</h3>
	<table cellpadding='0' cellspacing='0' border='0' width='100%' class=''>
	<col width='30%' />
	<col width='*' />
		<tr>
			<th>이름</th>
			<td>박수철</td>
		</tr>
		<tr>
			<th>아이디</th>
			<td>아이디</td>
		</tr>
		<tr>
			<th>이메일</th>
			<td>이메일</td>
		</tr>
		<tr>
			<th>핸드폰번호</th>
			<td>핸드폰번호</td>
		</tr>
	</table>
	<table cellpadding='0' cellspacing='0' border='0' width='100%' class='notice_view_inputs'>
	<col width='25%' />
	<col width='*' />
		<tr>
			<th>분류</th>
			<td>
				<select name='' style='width:45%;'>
					<option value='' selected>1댑스</option>
					<option value=''></option>
				</select>
				&nbsp;
				<select name='' style='width:45%;'>
					<option value='' selected>2댑스</option>
					<option value=''></option>
				</select>
			</td>
		</tr>
		<tr>
			<th>제목</th>
			<td><input type='text' name='' style='width:90%;padding:0 2%;height:28px;'></td>
		</tr>
		<tr>
			<th valign='top'>내용</th>
			<td><textarea style='width:90%;padding:2%;height:150px;resize:none;'></textarea></td>
		</tr>
		<tr>
			<th valign='top'>답글/댓글</th>
			<td>
				<div>
					<select name='' style='width:80%;'>
						<option value='' selected>2댑스</option>
						<option value=''></option>
					</select>
					<textarea style='width:90%;padding:2%;margin:10px 0 0 0;height:75px;resize:none;'></textarea>
				</div>
			</td>
		</tr>
	</table>
	<div style='text-align:center;'>
		<input type='image' src='./images/btn_OK.png' width='30%' alt='확인' />&nbsp;<input type='image' src='./images/btn_cancel.png' width='30%' alt='취소' />
	</div>
</div>
";



$Contents = $Contents01;




	$P = new MobileLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = store_menu();
	$P->strContents = $Contents;
	$P->Navigation = "상품리스트";
	$P->TitleBool = false;
	$P->ServiceInfoBool = true;
	echo $P->PrintLayOut();



$script_time[end] = time();
if($admininfo[charger_id] == "forbiz"){
	//print_r($script_time);
}

?>
