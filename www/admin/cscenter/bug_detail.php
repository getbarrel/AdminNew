<?
include("../class/layout.class");

$db = new Database;
$db->query("select * from shop_bug where ix = '$ix'");


$db->fetch();
/*
<html>
<title>버그신고 자세히보기</title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
*/
$Script = "
<SCRIPT LANGUAGE='JavaScript'>
<!--
function go_con_del(){
	yes = confirm('정말로 삭제하시 겠습니까?');
	if(yes){
		location.href = '/company/contacus.act.php?act=delete&con_ix=".$con_ix."';
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
				<td align='left' colspan=2> ".GetTitleNavigation("버그신고", "고객센타 > 버그신고", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 10px 0px'>
					<table border='0' width='100%' cellpadding=0 cellspacing=0 class='input_table_box'>
						<colgroup>
							<col width='16%' />
							<col width='*' style='padding:0px 0px 0px 10px'/>

						  </colgroup>

						<tr>
							<td class='input_box_title'> <b>고객명</b></td>
							<td class='input_box_item'>".$db->dt[name]."</td>
						</tr>
						<tr>
							<td class='input_box_title'> <b>제목 </b></td>
							<td class='input_box_item'>".$db->dt[subject]."</td>
						</tr>
						<tr>
							<td class='input_box_title'> <b>내용</b></td>

							<td bgcolor=#ffffff style='padding:5px;'><textarea rows=20 style='height:200px; width:95%'>".$db->dt[content]."</textarea></td>
						</tr>
					</table>

				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:10 0 0 0' colspan=2>
            <a href='javascript:self.close();'><img src='../images/".$admininfo['language']."/btn_close.gif' border=0></a>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                $Contents .= "
                <a href='bug_detai.act.php?ix=".$_GET[ix]."&mode=del'><img src='../images/".$admininfo["language"]."/btn_del.gif' border=0></a>";
            }else{
                $Contents .= "
                <a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btn_del.gif' border=0></a>";
            }
            $Contents.="
		</td>
	</tr>
</TABLE>";







$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "고객센타 > 버그신고 ";
$P->NaviTitle = "버그신고 ";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>


