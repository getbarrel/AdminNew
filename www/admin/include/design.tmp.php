<?
if(!function_exists("HelpBox_")){
	function HelpBox_($title, $text){

		$mstring = "<table cellpadding=0 cellspacing=0 width='100%' valign=top>
		<!--tr><td height=20><img src='/manage/image/icon_q.gif' align=absmiddle> <b style='color:0074ba' >$title</b></td></tr-->
		<tr>
			<td style='line-height:100%;'>
			".colorCirCleBox("#efefef","100%","<div style='padding:5 10 5 10;line-height:200%;' valign=top class=small>$text</div>")."
			</td>
		</tr>
		</table>";

		return colorCirCleBox("#efefef","100%","<div style='padding:5 10 5 10;' valign=top>$mstring</div>");
	}
}

if(!function_exists("text_button")){
	function text_button($link, $text, $width="156"){

	return "
	<table width=$width border=0 cellspacing=0 cellpadding=0 style='margin-top:7'>
					<tr>
					  <td height=1 width=1></td>
					  <td width=1></td>
					  <td width=1></td>
					  <td bgcolor=#FFFFFF width=1></td>
					  <td bgcolor=#FFFFFF width=".($width-8)."></td>
					  <td bgcolor=#FFFFFF width=1></td>
					  <td width=1></td>
					  <td width=1></td>
					  <td width=1></td>
					</tr>
					<tr>
					  <td height=1></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td></td>
					</tr>
					<tr>
					  <td height=1></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td></td>
					</tr>
					<tr>
					  <td bgcolor=#FFFFFF height=1></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					</tr>
				  </table>
				  <table width=$width border=0 cellspacing=0 cellpadding=0>
					<tr>
					  <td width=2 bgcolor=#FFFFFF></td>
					  <td bgcolor=D7EBF5 style='padding:4 2 2 9'><!--img src='/forbiz/img/squ_blue01.gif' width=11 height=11 style='margin:0 1 3 0' border=0 align=absbottom-->
						<a href='$link'><font color=316796>$text</font></a></td>
					  <td width=2 bgcolor=#FFFFFF></td>
					</tr>
				  </table>
				  <table width=$width border=0 cellspacing=0 cellpadding=0 style='margin-bottom:6'>
					<tr>
					  <td bgcolor=#FFFFFF height=1></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					</tr>
					<tr>
					  <td height=1></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td></td>
					</tr>
					<tr>
					  <td height=1></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td></td>
					</tr>
					<tr>
					  <td height=1 width=1></td>
					  <td width=1></td>
					  <td width=1></td>
					  <td bgcolor=#FFFFFF width=1></td>
					  <td bgcolor=#FFFFFF width=".($width-8)."></td>
					  <td bgcolor=#FFFFFF width=1></td>
					  <td width=1></td>
					  <td width=1></td>
					  <td width=1></td>
					</tr>
				  </table>";
	}
}

if(!function_exists("doubleline_BOX")){
	function doubleline_BOX($text, $width="240"){

	return "
	<table width=$width border=0 cellspacing=0 cellpadding=0 style='margin-top:7'>
					<tr>
					  <td height=1 width=1></td>
					  <td width=1></td>
					  <td width=1></td>
					  <td bgcolor=#FFFFFF width=1></td>
					  <td bgcolor=#FFFFFF width=".($width-8)."></td>
					  <td bgcolor=#FFFFFF width=1></td>
					  <td width=1></td>
					  <td width=1></td>
					  <td width=1></td>
					</tr>
					<tr>
					  <td height=1></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td></td>
					</tr>
					<tr>
					  <td height=1></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td></td>
					</tr>
					<tr>
					  <td bgcolor=#FFFFFF height=1></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					</tr>
				  </table>
				  <table width=$width border=0 cellspacing=0 cellpadding=0>
					<tr>
					  <td width=2 bgcolor=#FFFFFF></td>
					  <td bgcolor=D7EBF5 style='padding:4 2 2 4'>
						$text
					  </td>
					  <td width=2 bgcolor=#FFFFFF></td>
					</tr>
				  </table>
				  <table width=$width border=0 cellspacing=0 cellpadding=0 style='margin-bottom:6'>
					<tr>
					  <td bgcolor=#FFFFFF height=1></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					</tr>
					<tr>
					  <td height=1></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=D7EBF5></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td></td>
					</tr>
					<tr>
					  <td height=1></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td bgcolor=#FFFFFF></td>
					  <td></td>
					</tr>
					<tr>
					  <td height=1 width=1></td>
					  <td width=1></td>
					  <td width=1></td>
					  <td bgcolor=#FFFFFF width=1></td>
					  <td bgcolor=#FFFFFF width=".($width-8)."></td>
					  <td bgcolor=#FFFFFF width=1></td>
					  <td width=1></td>
					  <td width=1></td>
					  <td width=1></td>
					</tr>
				  </table>";
	}
}

if(!function_exists("colorCirCleBox")){
	function colorCirCleBox($color,$width,$text){

		return  "<table cellpadding=5 cellspacing=0 width=$width>
				
				<tr>
					<td bgcolor='$color'>$text</td>
				</tr>
				
			</table>";
	}
}

if(!function_exists("colorCirCleBox_backup")){
	function colorCirCleBox_backup($color,$width,$text){

		return  "<table cellpadding=5 cellspacing=0 width=$width>
				<!--tr>
					<td>
						<table cellpadding=0 cellspacing=0 border=0 width=100% align=center style='table-layout:fixed;'>
						<col width='1' /><col width='1' /><col width='1' /><col width='*' /><col width='1' /><col width='1' /><col width='1' />
						  <tr height=1>
							<td rowspan=3></td>
							<td></td>
							<td></td>
							<td bgcolor=$color></td>
							<td></td>
							<td></td>
							<td rowspan=3></td>
						  </tr>
						  <tr height=1>
							<td colspan=2 bgcolor=$color></td>
							<td bgcolor=$color></td>
							<td colspan=2 bgcolor=$color></td>
						  </tr>
						  <tr height=1>
							<td bgcolor=$color></td>
							<td colspan=3 bgcolor=$color></td>
							<td bgcolor=$color></td>
						  </tr>
						</table>
					</td>
				</tr-->
				<tr>
					<td bgcolor='$color'>$text</td>
				</tr>
				<!--tr>
					<td>
						<table cellpadding=0 cellspacing=0 border=0 width=100% align=center style='table-layout:fixed;'>
						<col width='1' /><col width='1' /><col width='1' /><col width='*' /><col width='1' /><col width='1' /><col width='1' />
						  <tr height=1>
							<td rowspan=3></td>
							<td rowspan=2 bgcolor=$color></td>
							<td colspan=3 bgcolor=$color></td>
							<td rowspan=2 bgcolor=$color></td>
							<td rowspan=3></td>
						  </tr>
						  <tr height=1>
							<td bgcolor=$color></td>
							<td bgcolor=$color></td>
							<td bgcolor=$color></td>
						  </tr>
						  <tr height=1>
							<td colspan=2></td>
							<td bgcolor=$color></td>
							<td colspan=2></td>
						  </tr>
						</table>
					</td>
				</tr-->
			</table>";
	}
}

if(!function_exists("colorCirCleBoxStart")){
	function colorCirCleBoxStart($color,$width){

		return  "<table cellpadding=0 cellspacing=0 width=$width>
				<tr>
					<td bgcolor='$color'>";


	}
}

if(!function_exists("colorCirCleBoxEnd")){
function colorCirCleBoxEnd($color){
		return 	"		</td>
				
				</tr>
			</table>";
	}
}

if(!function_exists("MenuCirCleBoxStart")){
	function MenuCirCleBoxStart($color,$width){

		return  "<table cellpadding=0 cellspacing=0 width=$width>
				<tr>
					<td>
						<table cellpadding=0 cellspacing=0 border=0 width=100% align=center>
						  <tr height=1>
							<td rowspan=3 width=1></td>
							<td width=1></td>
							<td width=1></td>
							<td bgcolor=$color></td>
							<td width=1></td>
							<td width=1></td>
							<td rowspan=3 width=1></td>
						  </tr>
						  <tr height=1>
							<td colspan=2 bgcolor=$color></td>
							<td bgcolor=$color></td>
							<td colspan=2 bgcolor=$color></td>
						  </tr>
						  <tr height=1>
							<td bgcolor=$color></td>
							<td colspan=3 bgcolor=$color></td>
							<td bgcolor=$color></td>
						  </tr>
						</table>
					</td>
				</tr>
				<tr>
					<td bgcolor='$color' align=center style='color:#ffffff;font-weight:bold;padding-left:5px;padding-right:5px;' nowrap>";


	}
}

if(!function_exists("MenuCirCleBoxEnd")){
	function MenuCirCleBoxEnd($color){
		return 	"		</td>
				</tr>
				<tr>
					<td>
					<table cellpadding=0 cellspacing=0 border=0 width=100% align=center>
					  <tr height=1>
						<td rowspan=3 width=1></td>
						<td rowspan=2 width=1 bgcolor=$color></td>
						<td colspan=3 bgcolor=$color></td>
						<td rowspan=2 width=1 bgcolor=$color></td>
						<td rowspan=3 width=1></td>
					  </tr>
					  <tr height=1>
						<td width=1 bgcolor=$color></td>
						<td bgcolor=$color></td>
						<td width=1 bgcolor=$color></td>
					  </tr>
					  <tr height=1>
						<td colspan=2></td>
						<td bgcolor=$color></td>
						<td colspan=2></td>
					  </tr>
					</table>
					</td>
				</tr>
			</table>";
	}
}

if(!function_exists("ContentsBox")){
	function ContentsBox($vtitle,$vContents,$linecolor, $titleBgcolor, $ContentsBgcolor, $vwidth,$vheight){

		 return "	<table border=0 cellpadding=0 cellspacing=0 width=$vwidth height=1>
					<tr>
					  <td width=5 height=1></td>
					  <td width='".($vwidth-10)."' height=1 bgcolor=$linecolor></td>
					  <td width=5 height=1></td>
					</tr>
					</table>
					<table border=0 cellpadding=0 cellspacing=0 width=$vwidth height=1>
					<tr>
					  <td width=3 height=1></td>
					  <td width=2 height=1 bgcolor='$linecolor'></td>
					  <td width='".($vwidth-10)."' height=1 bgcolor='$titleBgcolor'></td>
					  <td width=2 height=1 bgcolor=$linecolor></td>
					  <td width=3 height=1></td>
					</tr>
					</table>
					<table border=0 cellpadding=0 cellspacing=0 width=$vwidth height=1>
					<tr>
					  <td width=2 height=1></td>
					  <td width=1 height=1 bgcolor=$linecolor></td>
					  <td width=3 height=1 bgcolor=$titleBgcolor></td>
					  <td width='".($vwidth-12)."' height=1 bgcolor=$titleBgcolor></td>
					  <td width=3 height=1 bgcolor=$titleBgcolor></td>
					  <td width=1 height=1 bgcolor=$linecolor></td>
					  <td width=2 height=1></td>
					</tr>
					</table>
					<table border=0 cellpadding=0 cellspacing=0 width=$vwidth height=1>
					<tr>
					  <td width=1 height=1></td>
					  <td width=1 height=1 bgcolor=$linecolor></td>
					  <td width=2 height=1 bgcolor=$titleBgcolor></td>
					  <td width='".($vwidth-8)."' height=1 bgcolor=$titleBgcolor></td>
					  <td width=2 height=1 bgcolor=$titleBgcolor></td>
					  <td width=1 height=1 bgcolor=$linecolor></td>
					  <td width=1 height=1></td>
					</tr>
					</table>
					<table border=0 cellpadding=0 cellspacing=0 width=$vwidth height=1>
					<tr>
					  <td width=1 height=1></td>
					  <td width=1 height=1 bgcolor=$linecolor></td>
					  <td width=1 height=1 bgcolor=$titleBgcolor></td>
					  <td width='".($vwidth-6)."' height=1 bgcolor=$titleBgcolor></td>
					  <td width=1 height=1 bgcolor=$titleBgcolor></td>
					  <td width=1 height=1 bgcolor=$linecolor></td>
					  <td width=1 height=1></td>
					</tr>
					</table>
					<table border=0 cellpadding=0 cellspacing=0 width=$vwidth height=1>
					<tr>
					  <td width=1 height=1 bgcolor=$linecolor></td>
					  <td width=2 height=1 bgcolor=$titleBgcolor></td>
					  <td width='".($vwidth-6)."' height=1 bgcolor=$titleBgcolor></td>
					  <td width=2 height=1 bgcolor=$titleBgcolor></td>
					  <td width=1 height=1 bgcolor=$linecolor></td>
					</tr>
					</table>
					<table border=0 cellpadding=0 cellspacing=0 width=$vwidth height=1>
					<tr>
					  <td width=1 height=1 bgcolor=$linecolor></td>
					  <td width=1 height=1 bgcolor=$titleBgcolor></td>
					  <td width='".($vwidth-4)."' height=1 bgcolor=$titleBgcolor></td>
					  <td width=1 height=1 bgcolor=$titleBgcolor></td>
					  <td width=1 height=1 bgcolor=$linecolor></td>
					</tr>
					</table>
					<table border=0 cellpadding=0 cellspacing=0 width=$vwidth height=22 style='TABLE-LAYOUT: fixed'>
					<tr>
					  <td width=1 height=22 bgcolor=$linecolor></td>
					  <td width=1 height=22 bgcolor=$titleBgcolor></td>
					  <td width=8 height=22 bgcolor=$titleBgcolor></td>
					  <td width='".($vwidth-12)."' height=22 bgcolor=$titleBgcolor>
						 $vtitle
					  <td width=1 height=22 bgcolor=$titleBgcolor></td>
					  <td width=1 height=22 bgcolor=$linecolor></td>
					</tr>
					<tr>
					  <td width=1 bgcolor=$linecolor></td>
					  <td width=1 bgcolor=$ContentsBgcolor></td>
					  <td width=8 bgcolor=$ContentsBgcolor></td>
					  <td width='".($vwidth-12)."' height=$vheight bgcolor=$ContentsBgcolor valign=top style='PADDING-RIGHT: 10px; PADDING-LEFT: 2px; PADDING-BOTTOM: 10px; PADDING-TOP: 10px'>
						$vContents
					  </td>
					  <td width=1 bgcolor='$ContentsBgcolor'></td>
					  <td width=1 bgcolor='$linecolor'></td>
					</tr>
					</table>
					<table border=0 cellpadding=0 cellspacing=0 width=$vwidth height=1>
					<tr>
					  <td width=1 height=2></td>
					  <td width=1 height=2 bgcolor=$linecolor></td>
					  <td width='".($vwidth-4)."' height=2 bgcolor=$ContentsBgcolor></td>
					  <td width=1 height=2 bgcolor=$linecolor></td>
					  <td width=1 height=2></td>
					</tr>
					</table>
					<table border=0 cellpadding=0 cellspacing=0 width=$vwidth height=1>
					<tr>
					  <td width=2 height=1></td>
					  <td width=1 height=1 bgcolor=$linecolor></td>
					  <td width='".($vwidth-6)."' height=1 bgcolor=$ContentsBgcolor></td>
					  <td width=1 height=1 bgcolor=$linecolor></td>
					  <td width=2 height=1></td>
					</tr>
					</table>
					<table border=0 cellpadding=0 cellspacing=0 width=$vwidth height=1>
					<tr>
					  <td width=3 height=1></td>
					  <td width=2 height=1 bgcolor=$linecolor></td>
					  <td width='".($vwidth-10)."' height=1 bgcolor=$ContentsBgcolor></td>
					  <td width=2 height=1 bgcolor=$linecolor></td>
					  <td width=3 height=1></td>
					</tr>
					</table>
					<table border=0 cellpadding=0 cellspacing=0 width=$vwidth height=1>
					<tr>
					  <td width=5 height=1></td>
					  <td width='".($vwidth-10)."' height=1 bgcolor=$linecolor></td>
					  <td width=5 height=1></td>
					</tr>
					</table>";

	}
}

if(!function_exists("ShadowBox")){
	function ShadowBox($vContents){

	return "
	<TABLE cellSpacing=0 cellPadding=0 border=0>
			<TBODY>
			<TR height=1>
				<TD colSpan=3></TD>
				<TD bgColor=#717171 colSpan=3></TD>
				<TD colSpan=3></TD>
			</TR>
			<TR height=1>
				<TD ></TD>
				<TD bgColor=#717171></TD>
				<TD bgColor=#717171></TD>
				<TD bgColor=#ffffff colSpan=2 ></TD>
				<TD bgColor=#000000 colSpan=3 ></TD>
				<TD></TD>
			</TR>
			<TR height=1>
				<TD></TD>
				<TD bgColor=#717171></TD>
				<TD bgColor=#ffffff></TD>
				<TD bgColor=#ffffff></TD>
				<TD bgColor=#ffffff class=gnbSubMenu rowspan=5 style='PADDING: 3px; '  valign=top>
				$vContents
				</TD>
				<TD bgColor=#ffffff></TD>
				<TD bgColor=#717171 ></TD>
				<TD bgColor=#717171 ></TD>
				<TD></TD>
			</TR>
			<TR id=gnb_layer_storeleft_2 vAlign=bottom bgColor=#ffffff height=100>
				<TD bgColor=#717171></TD>
				<TD ></TD>
				<TD ></TD>
				<TD ></TD>
				<TD colSpan=2></TD>
				<TD bgColor=#717171></TD>
				<TD bgColor=#000000></TD>
			</TR>

			<TR id=gnb_layer_storeleft_10 vAlign=bottom bgColor=#ffffff height=16>
				<TD bgColor=#717171></TD>
				<TD></TD>
				<TD rowSpan=2></TD>
				<TD rowSpan=3></TD>
				<TD rowSpan=2></TD>
				<TD></TD>
				<TD bgColor=#717171></TD>
				<TD bgColor=#000000 rowSpan=2></TD>
			</TR>
			<TR height=1>
				<TD rowSpan=2></TD>
				<TD bgColor=#717171 rowSpan=2></TD>
				<TD bgColor=#717171 rowSpan=2></TD>
				<TD bgColor=#000000 rowSpan=2></TD>
			</TR>
			<TR height=1>
				<TD bgColor=#717171></TD>
				<TD bgColor=#717171></TD>
				<TD></TD>
			</TR>
			<TR height=1>
				<TD colSpan=2></TD>
				<TD bgColor=#000000></TD>
				<TD bgColor=#717171 colSpan=2></TD>
				<TD bgColor=#000000 colSpan=3></TD>
				<TD></TD>
			</TR>
			<TR height=1>
				<TD colSpan=4></TD>
				<TD bgColor=#000000 colSpan=2></TD>
				<TD colSpan=3></TD>
			</TR>
			</TABLE>";

	}
}

if(!function_exists("LineBox")){
	function LineBox($contents="test",$linecolor="#e6d7c2", $bgcolor="#f9f6f1", $width="400"){

		return "<TABLE id=idMainContent style='TABLE-LAYOUT: fixed' cellSpacing=0 cellPadding=0 width='$width' border=0>
	<TBODY>
	<TR>
	<TD vAlign=top width='$width'>
	<TABLE height=1 cellSpacing=0 cellPadding=0 width='$width' border=0>
	<TBODY>
	<TR>
	<TD width=5 height=1></TD>
	<TD width='".($width-10)."' bgColor=$linecolor height=1></TD>
	<TD width=5 height=1></TD></TR></TBODY></TABLE>
	<TABLE height=1 cellSpacing=0 cellPadding=0 width='$width' border=0>
	<TBODY>
	<TR>
	<TD width=3 height=1></TD>
	<TD width=2 bgColor=$linecolor height=1></TD>
	<TD width='".($width-10)."' bgColor=$bgcolor height=1></TD>
	<TD width=2 bgColor=$linecolor height=1></TD>
	<TD width=3 height=1></TD></TR></TBODY></TABLE>
	<TABLE height=1 cellSpacing=0 cellPadding=0 width='$width' border=0>
	<TBODY>
	<TR>
	<TD width=2 height=1></TD>
	<TD width=1 bgColor=$linecolor height=1></TD>
	<TD width='".($width-6)."' bgColor=$bgcolor height=1></TD>
	<TD width=1 bgColor=$linecolor height=1></TD>
	<TD width=2 height=1></TD></TR></TBODY></TABLE>
	<TABLE height=2 cellSpacing=0 cellPadding=0 width='$width' border=0>
	<TBODY>
	<TR>
	<TD width=1 height=2></TD>
	<TD width=1 bgColor=$linecolor height=2></TD>
	<TD width='".($width-4)."' bgColor=$bgcolor height=2></TD>
	<TD width=1 bgColor=$linecolor height=2></TD>
	<TD width=1 height=2></TD></TR></TBODY></TABLE>
	<TABLE height=3 cellSpacing=0 cellPadding=0 width='$width' border=0>
	<TBODY>
	<TR>
	<TD width=1 bgColor=$linecolor height=3></TD>
	<TD width='".($width-2)."' bgColor=$bgcolor height=3></TD>
	<TD width=1 bgColor=$linecolor height=3></TD></TR></TBODY></TABLE>
	<TABLE height=11 cellSpacing=0 cellPadding=0 width='$width' border=0>
	<TBODY>
	<TR>
	<TD width=1 bgColor=$linecolor height=11></TD>
	<TD width=7 bgColor=$bgcolor></TD>
	<TD width='".($width-16)."' bgColor=$bgcolor>
	$contents
	</TD>
	<TD width=7 bgColor=$bgcolor></TD>
	<TD width=1 bgColor=$linecolor></TD></TR></TBODY></TABLE>

	<TABLE height=3 cellSpacing=0 cellPadding=0 width='$width' border=0>
	<TBODY>
	<TR>
	<TD width=1 bgColor=$linecolor height=3></TD>
	<TD width='".($width-2)."' bgColor=$bgcolor height=3></TD>
	<TD width=1 bgColor=$linecolor height=3></TD></TR></TBODY></TABLE>
	<TABLE height=2 cellSpacing=0 cellPadding=0 width='$width' border=0>
	<TBODY>
	<TR>
	<TD width=1 height=2></TD>
	<TD width=1 bgColor=$linecolor height=2></TD>
	<TD width='".($width-4)."' bgColor=$bgcolor height=2></TD>
	<TD width=1 bgColor=$linecolor height=2></TD>
	<TD width=1 height=2></TD></TR></TBODY></TABLE>
	<TABLE height=1 cellSpacing=0 cellPadding=0 width='$width' border=0>
	<TBODY>
	<TR>
	<TD width=2 height=1></TD>
	<TD width=1 bgColor=$linecolor height=1></TD>
	<TD width='".($width-6)."' bgColor=$bgcolor height=1></TD>
	<TD width=1 bgColor=$linecolor height=1></TD>
	<TD width=2 height=1></TD></TR></TBODY></TABLE>
	<TABLE height=1 cellSpacing=0 cellPadding=0 width='$width' border=0>
	<TBODY>
	<TR>
	<TD width=3 height=1></TD>
	<TD width=2 bgColor=$linecolor height=1></TD>
	<TD width='".($width-10)."' bgColor=$bgcolor height=1></TD>
	<TD width=2 bgColor=$linecolor height=1></TD>
	<TD width=3 height=1></TD></TR></TBODY></TABLE>
	<TABLE height=1 cellSpacing=0 cellPadding=0 width='$width' border=0>
	<TBODY>
	<TR>
	<TD width=5 height=1></TD>
	<TD width='".($width-10)."' bgColor=$linecolor height=1></TD>
	<TD width=5 height=1></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE>";
	}
}

if(!function_exists("FullLineBox")){
	function FullLineBox($contents="test",$linecolor="#e6d7c2", $bgcolor="#f9f6f1", $width="400"){

		return "<TABLE  style='TABLE-LAYOUT: fixed'  cellSpacing=0 cellPadding=0 width='100%' border=0 >
	<TBODY>
	<TR>
	<TD vAlign=top width='100%' >
		<TABLE height=1 cellSpacing=0 cellPadding=0 width='100%' border=0 >
		<TBODY>
		<TR>
		<TD width='5%' height=1></TD>
		<TD width='99%' bgColor=$linecolor height=1></TD>
		<TD width='5%' height=1></TD></TR></TBODY></TABLE>
		<TABLE height=1 cellSpacing=0 cellPadding=0 width='100%' border=0>
		<TBODY>
		<TR>
		<TD width=3 height=1></TD>
		<TD width=2 bgColor=$linecolor height=1></TD>
		<TD width='99%' bgColor=$bgcolor height=1></TD>
		<TD width=2 bgColor=$linecolor height=1></TD>
		<TD width=3 height=1></TD></TR></TBODY></TABLE>
	<TABLE height=1 cellSpacing=0 cellPadding=0 width='100%' border=0>
	<TBODY>
	<TR>
	<TD width=2 height=1></TD>
	<TD width=1 bgColor=$linecolor height=1></TD>
	<TD width='99%' bgColor=$bgcolor height=1></TD>
	<TD width=1 bgColor=$linecolor height=1></TD>
	<TD width=2 height=1></TD></TR></TBODY></TABLE>
	<TABLE height=2 cellSpacing=0 cellPadding=0 width='100%' border=0>
	<TBODY>
	<TR>
	<TD width=1 height=2></TD>
	<TD width=1 bgColor=$linecolor height=2></TD>
	<TD width='99%' bgColor=$bgcolor height=2></TD>
	<TD width=1 bgColor=$linecolor height=2></TD>
	<TD width=1 height=2></TD></TR></TBODY></TABLE>
	<TABLE height=3 cellSpacing=0 cellPadding=0 width='100%' border=0>
	<TBODY>
	<TR>
	<TD width=1 bgColor=$linecolor height=3></TD>
	<TD width='99%' bgColor=$bgcolor height=3></TD>
	<TD width=1 bgColor=$linecolor height=3></TD></TR></TBODY></TABLE>
	<TABLE height=11 cellSpacing=0 cellPadding=0 width='100%' border=0>
	<TBODY>
	<TR>
	<TD width=1 bgColor=$linecolor height=11></TD>
	<TD width=7 bgColor=$bgcolor></TD>
	<TD width='99%' bgColor=$bgcolor>
	$contents
	</TD>
	<TD width=7 bgColor=$bgcolor></TD>
	<TD width=1 bgColor=$linecolor></TD></TR></TBODY></TABLE>

	<TABLE height=3 cellSpacing=0 cellPadding=0 width='100%' border=0>
	<TBODY>
	<TR>
	<TD width=1 bgColor=$linecolor height=3></TD>
	<TD width='99%' bgColor=$bgcolor height=3></TD>
	<TD width=1 bgColor=$linecolor height=3></TD></TR></TBODY></TABLE>
	<TABLE height=2 cellSpacing=0 cellPadding=0 width='100%' border=0>
	<TBODY>
	<TR>
	<TD width=1 height=2></TD>
	<TD width=1 bgColor=$linecolor height=2></TD>
	<TD width='99%' bgColor=$bgcolor height=2></TD>
	<TD width=1 bgColor=$linecolor height=2></TD>
	<TD width=1 height=2></TD></TR></TBODY></TABLE>
	<TABLE height=1 cellSpacing=0 cellPadding=0 width='100%' border=0>
	<TBODY>
	<TR>
	<TD width=2 height=1></TD>
	<TD width=1 bgColor=$linecolor height=1></TD>
	<TD width='99%' bgColor=$bgcolor height=1></TD>
	<TD width=1 bgColor=$linecolor height=1></TD>
	<TD width=2 height=1></TD></TR></TBODY></TABLE>
	<TABLE height=1 cellSpacing=0 cellPadding=0 width='100%' border=0>
	<TBODY>
	<TR>
	<TD width=3 height=1></TD>
	<TD width=2 bgColor=$linecolor height=1></TD>
	<TD width='99%' bgColor=$bgcolor height=1></TD>
	<TD width=2 bgColor=$linecolor height=1></TD>
	<TD width=3 height=1></TD></TR></TBODY></TABLE>
	<TABLE height=1 cellSpacing=0 cellPadding=0 width='100%' border=0>
	<TBODY>
	<TR>
	<TD width=5 height=1></TD>
	<TD width='99%' bgColor=$linecolor height=1></TD>
	<TD width=5 height=1></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE>";
	}
}
?>