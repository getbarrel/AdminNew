<?
include("../class/layout.class");


$db = new Database;

$addScript = "
<SCRIPT LANGUAGE='JavaScript'>
<!--

//-->
</SCRIPT>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='800px;'  align=left border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("FTP 사용안내", "FTP 사용안내", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 10px 0px'>
					<table width='100%' cellpadding=0 cellspacing=0 border=0'>
					<tr>
						<td align=left><img src='../images/".$admininfo[language]."/ftp_menual_01.gif'></td>
					</tr>
					<tr>
						<td align=left>
							<div style='width:754px; height:272px; position:relative;'>
							<img src='../images/".$admininfo[language]."/ftp_menual_02.gif'>
							<a href='http://www.altools.co.kr/product/alftp_intro.aspx' style='font-size:11px; color:#ffffff; position:absolute; top:118px; left:272px; ;' target='_blank'>URL 경로 : http://www.altools.co.kr/product/alftp_intro.aspx</a>
							</div>
						</td>
					</tr>
					<tr>
						<td align=left><img src='../images/".$admininfo[language]."/ftp_menual_03.gif'></td>
					</tr>
					<tr>
						<td align=left><img src='../images/".$admininfo[language]."/ftp_menual_04.gif'></td>
					</tr>
					</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</TABLE>";


$P = new ManagePopLayOut();
$P->addScript = $addScript;
$P->Navigation = "FTP 사용안내";
$P->NaviTitle = "FTP 사용안내 ";
$P->strContents = $Contents;
echo $P->PrintLayOut();
?>





