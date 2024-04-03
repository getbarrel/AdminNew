<?
include("../class/layout.class");


$db = new Database;
$db->query("select * from shop_cooperation where ix = '$ix'");


$db->fetch();

/*


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>제휴문의 자세히보기</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='pragma' content='no-cache'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>

*/
$addScript = "
<SCRIPT LANGUAGE='JavaScript'>
<!--
function go_con_del(con_ix){
	yes = confirm('정말로 삭제하시 겠습니까?');
	if(yes){
		location.href = '/company/contacus.act.php?act=delete&con_ix='+con_ix;
	}else{
		return;
	}
}
//-->
</SCRIPT>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("제휴문의", "고객센타 > 제휴문의", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 10px 0px'>
					<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box'>
						 <colgroup>
							<col width='16%' />
							<col width='34%' style='padding:0px 0px 0px 10px'/>
							<col width='16%' />
							<col width='34%' style='padding:0px 0px 0px 10px'/>
						  </colgroup>
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>고객명</b></td>
							<td class='input_box_item' width='20%'>
								".$db->dt[name]."
							</td>
							<td class='input_box_title'> <b>이메일 </b></td>
							<td class='input_box_item' width='*'>
								".$db->dt[email]."
							</td>
						</tr>
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>회사명</b></td>
							<td class='input_box_item' width='20%'>
								".$db->dt[com_name]."
							</td>
							<td class='input_box_title'> <b>연락처 </b></td>
							<td class='input_box_item' width='*'>
								".$db->dt[tel]."
							</td>
						</tr>
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>제목 </b></td>
							<td class='input_box_item' colspan='3'>".$db->dt[subject]."</td>
						</tr>
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>첨부파일 </b></td>
							<td class='input_box_item' colspan='3'><a href='download.php?ix=".$db->dt[ix]."&file_name=".urlencode($db->dt[file_name])."'>".$db->dt[file_name]."</a></td>
						</tr>
						<tr valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>내용</b></td>
							<td colspan=3 style='padding:5px'><div rows=30 style='height:180px;width:95%;'>".$db->dt[content]."</div></td>
                        
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:10px 0px' colspan=2>
			 <a href='javascript:self.close();'><img src='../images/".$admininfo[language]."/btn_close.gif' border=0></a>";
             if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                $Contents .= "
			     <a href=\"javascript:go_con_del('".$ix."');\"><img src='../images/".$admininfo[language]."/btn_del.gif' border=0></a>";
             }else{
                $Contents .= "
                <a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo[language]."/btn_del.gif' border=0></a>";
             }
             $Contents .= "
		</td>
	</tr>
</TABLE>";


$P = new ManagePopLayOut();
$P->addScript = $addScript;
$P->Navigation = "고객센타 > 제휴문의";
$P->NaviTitle = "제휴문의 ";
$P->strContents = $Contents;
echo $P->PrintLayOut();
?>





