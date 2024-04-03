<?
include("../class/layout.class");
/*
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='pragma' content='no-cache'>

<title>탈퇴회원정보</title>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
*/

$Script = "
<style>
input {border:1px solid #c6c6c6}
</style>";


$db = new Database;
$db->query("SELECT * FROM common_dropmember  where code = '$code'");
$db->fetch();

$reason = $db->dt[reason];
$message = $db->dt[message];
$dropdate = $db->dt[dropdate];

$Contents = "
	<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
		<tr height=40>
			<td align='left' colspan=2> ".GetTitleNavigation("탈퇴회원 사유보기", "회원관리 > 탈퇴회원 사유보기", false)."</td>
		</tr>
		<tr>
			<td align=center style='padding: 0 10px 0 10px'>
			<table border='0' width='100%' cellspacing='0' cellpadding='5' >
				<tr>
					<td >
					<table border='0' width='100%' cellspacing='1' cellpadding='0' style='border:5px solid #F8F9FA' >
						<tr>
							<td >
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box'>
									<tr>
										<td class='input_box_title' width=90>탈퇴이유</td>
										<td class='input_box_item'>&nbsp;".$reason."</td>
									</tr>
									<tr>
										<td class='input_box_title' width=90>남긴말</td>
										<td bgcolor='#ffffff' align='left' valign=top style='line-height:130%;padding:10px;'>&nbsp;".nl2br($message)."</td>
									</tr>
									<tr>
										<td class='input_box_title' width=90>탈퇴일</td>
										<td class='input_box_item'>&nbsp;".$dropdate."</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>

			</tr>
		</table>


		</td>
	</tr>

</TABLE>";




$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "회원관리 > 탈퇴회원 사유보기";
$P->NaviTitle = "탈퇴회원 사유보기";
$P->strContents = $Contents;
echo $P->PrintLayOut();





