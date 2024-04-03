<?
include("../class/layout.class");


	//print_r($_SESSION);
	$db = new Database;
	$sql = "select * from mallstory_large_order where lo_ix ='".$lo_ix."'  ";
		
	$db->query($sql);		
	$db->fetch();
?>

<html>
<title></title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>

<style>

input {border:1px solid #c6c6c6}
</style>
<body topmargin=0 leftmargin=0 >
<TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
	<TR>		
		<td align=center colspan=2>
		<table border="0" width="100%" cellpadding="0" cellspacing="1" align="center">	
				<tr><td  align=left class='top_orange'  ></td></tr>
				<tr height=35 bgcolor=#efefef>
					<td  style='padding:0 0 0 0;'> 
						<table width='100%' border='0' cellspacing='0' cellpadding='0' >
							<tr> 
								<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap><!--border-bottom:2px solid #ff9b00;-->
									<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 맞춤견적처리
								</td>
								<td width='90%' align='right' valign='top' ><!--style='border-bottom:2px solid #efefef;'-->
									&nbsp;
								</td>
							</tr>
							<!--tr height=10><td colspan=2></td></tr-->
						</table>
					</td>
				</tr>
			<form name='product_qna_frm' method="post" enctype='multipart/form-data' action='estimate.act.php' >
			<input type='hidden' name='act' value='estimate_update'>
			<input type='hidden' name='lo_ix' value='<?=$lo_ix?>'>
			<tr>				
				<td align=center style='padding: 0 10 0 10'>
				<table border='0' width='100%' cellspacing='0' cellpadding='5' >
					<tr>
						<td >	
						<table border='0' width='100%' cellspacing='1' cellpadding='0' style='border:5px solid #F8F9FA'>
							<tr>
								<td >
									<table border='0' width='100%' cellspacing='1' cellpadding='4' bgcolor='#c0c0c0'>
										<tr>
											<td class=leftmenu width=90 align='left'><img src='../image/title_head.gif' > 처리상태</td>
											<td bgcolor='#ffffff'>&nbsp;
											<select name='es_status'>
											<option value=''>선택</option>
											<option value='N' <?if($db->dt[es_status] == "N"){?>selected<?}?> style="background-color:#f59db3;">견적대기</option>
											<option value='R' <?if($db->dt[es_status] == "R"){?>selected<?}?>>견적진행</option>
											<option value='A' <?if($db->dt[es_status] == "A"){?>selected<?}?>>견적완료</option>
											<option value='Y' <?if($db->dt[es_status] == "Y"){?>selected<?}?>>주문완료</option>
											</select>
											</td>
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../image/title_head.gif' > 학교명</td>
											<td  bgcolor='#ffffff'>&nbsp;<?=$db->dt[es_sc_nm]?></td>
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../image/title_head.gif' > 전화번호</td>
											<td  bgcolor='#ffffff'>&nbsp;<?=$db->dt[es_tel]?></td>
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../image/title_head.gif' > 팩스번호</td>
											<td  bgcolor='#ffffff'>&nbsp;<?=$db->dt[es_pcs]?></td>
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../image/title_head.gif' > 담당자</td>
											<td  bgcolor='#ffffff'>&nbsp;<?=$db->dt[es_damdang]?></td>
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../image/title_head.gif' > 전자우편</td>
											<td  bgcolor='#ffffff'>&nbsp;<?=$db->dt[es_email]?></td>
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../image/title_head.gif' > 예산금액</td>
											<td  bgcolor='#ffffff'>&nbsp;<?=$db->dt[es_price]?></td>
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../image/title_head.gif' > 첨부파일</td>
											<td  bgcolor='#ffffff'>&nbsp;<?if($db->dt[es_file_name]){?><a href="./download.php?lo_ix=<?=$lo_ix?>&file=<?=$db->dt[es_file_name]?>" target="">다운로드</a><?}else{?>첨부파일이 없습니다.<?}?></td>
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../image/title_head.gif' > 제목</td>
											<td  bgcolor='#ffffff'>&nbsp;<?=$db->dt[es_title]?></td>
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../image/title_head.gif' > 상품목록 및 내용</td>
											<td bgcolor='#ffffff' style="width:700px; height:100px"><textarea rows=20 cols=50 readonly="readonly"><?=$db->dt[es_etc]?></textarea></td>
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../image/title_head.gif' > 견적금액</td>
											<td  bgcolor='#ffffff'>&nbsp;
											<input type="text" name="es_amount"  value="<?=$db->dt[es_amount]?>">
											<span style="color:#3CB4FF;">*(원)단위로 실 견적 금액을 입력해주세요.</span></td>
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../image/title_head.gif' > 견적파일</td>
											<td  bgcolor='#ffffff'>&nbsp;
											<input type="file" name="es_file_re"><br>
											등록된 파일을 <input type="radio" name="file_status" value="A" checked> 보존, 
											<input type="radio" name="file_status" value="E"> 변경, 
											<input type="radio" name="file_status" value="D"> 삭제 합니다. <br>
											<?=$db->dt[es_file_re]?></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td height="40" align="center">
									<span id="show_pay_btn">
										<input type="submit" value="저장"  class="box">
									</span>
								</td>
							</tr>
						</table><br>
			</td>
			
  		</tr>
		</form>
  		</table>
		
				
		</td>
	</tr>
	
</TABLE>

</body>
</html>





