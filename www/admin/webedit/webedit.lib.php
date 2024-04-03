<?

function WebEdit($path="..", $height='210px'){
global $admininfo;
$mstring = "

<table id='tblCtrls' width='100%' border='0' cellspacing='1' cellpadding='0' align='center'>
						        <tr> 
						          <td bgcolor='F5F6F5'>
									 <table width='100%' border='0' cellspacing='0' cellpadding='0'>
						              <tr>
						                <td width='18%' height='56'>
											 	<table width='100%' height='56' border='0' align='center' cellpadding='0' cellspacing='0'>
						                    <tr align='center' valign='bottom'> 
						                      <td height='26'><a href='javascript:doBold();' onMouseOver=\"MM_swapImage('editImage1','','".$path."/images/".$admininfo["language"]."/webedit/wtool1_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool1.gif' name='editImage1' width='19' height='18' border='0' id='editImage1'></a></td>
						                      <td><a href='javascript:doItalic();' onMouseOver=\"MM_swapImage('editImage2','','".$path."/images/".$admininfo["language"]."/webedit/wtool2_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool2.gif' name='editImage2' width='19' height='18' border='0' id='editImage2'></a></td>
						                      <td><a href='javascript:doUnderline();' onMouseOver=\"MM_swapImage('editImage3','','".$path."/images/".$admininfo["language"]."/webedit/wtool3_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool3.gif' name='editImage3' width='19' height='18' border='0' id='editImage3'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='3' colspan='3'></td>
						                    </tr>
						                    <tr align='center' valign='top'> 
						                      <td height='27'><a href='javascript:doLeft();' onMouseOver=\"MM_swapImage('editImage8','','".$path."/images/".$admininfo["language"]."/webedit/wtool8_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool8.gif' name='editImage8' width='19' height='18' border='0' id='editImage8'></a></td>
						                      <td><a href='javascript:doCenter();' onMouseOver=\"MM_swapImage('editImage9','','".$path."/images/".$admininfo["language"]."/webedit/wtool9_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool9.gif' name='editImage9' width='19' height='18' border='0' id='editImage9'></a></td>
						                      <td><a href='javascript:doRight();' onMouseOver=\"MM_swapImage('editImage10','','".$path."/images/".$admininfo["language"]."/webedit/wtool10_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool10.gif' name='editImage10' width='19' height='18' border='0' id='editImage10'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='".$path."/images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='19%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td width='100%' height='27' align='center' valign='bottom'><a href='javascript:doFont();' onMouseOver=\"MM_swapImage('editImage4','','".$path."/images/".$admininfo["language"]."/webedit/wtool4_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool4.gif' name='editImage4' height='22' border='0' id='editImage4'></a></td>
						                    </tr>
						                    <tr>
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doSize();' onMouseOver=\"MM_swapImage('editImage11','','".$path."/images/".$admininfo["language"]."/webedit/wtool11_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool11.gif' name='editImage11' height='22' border='0' id='editImage11'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='".$path."/images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='20%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doForcol();' onMouseOver=\"MM_swapImage('editImage5','','".$path."/images/".$admininfo["language"]."/webedit/wtool5_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool5.gif' name='editImage5' height='22' border='0' id='editImage5'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doBgcol();' onMouseOver=\"MM_swapImage('editImage12','','".$path."/images/".$admininfo["language"]."/webedit/wtool12_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool12.gif' name='editImage12' height='22' border='0' id='editImage12'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='".$path."/images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='18%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doImage();' onMouseOver=\"MM_swapImage('editImage6','','".$path."/images/".$admininfo["language"]."/webedit/wtool6_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool6.gif' name='editImage6' height='22' border='0' id='editImage6'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doTable();' onMouseOver=\"MM_swapImage('editImage13','','".$path."/images/".$admininfo["language"]."/webedit/wtool13_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool13.gif' name='editImage13'  height='22' border='0' id='editImage13'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='".$path."/images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='25%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doLink();' onMouseOver=\"MM_swapImage('editImage7','','".$path."/images/".$admininfo["language"]."/webedit/wtool7_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool7.gif' name='editImage7' height='22' border='0' id='editImage7'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doMultilink();' onMouseOver=\"MM_swapImage('editImage14','','".$path."/images/".$admininfo["language"]."/webedit/wtool14_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool14.gif' name='editImage14' height='22' border='0' id='editImage14'></a></td>
						                    </tr>
						                  </table>
											 </td>
						              </tr>
						            </table>
									 </td>
						        </tr>
						      </table>
						      <input type='hidden' name='content' value=''>
						      <iframe align='right' id='iView' frameborder=0 style='border-top:1px silver solid;width: 100%; height:".$height.";' scrolling='YES' hspace='0' vspace='0'></iframe>
						      <!-- html편집기 메뉴 종료 -->";
						      
						      
	return $mstring;

}



function miniWebEdit($path="..", $height='210'){
global $admininfo;
$mstring = "
						
						<table id='tblCtrls' width='100%' border='0' cellspacing='1' cellpadding='0' align='center'>
						        <tr> 
						          <td bgcolor='F5F6F5'>
									 <table width='100%' border='0' cellspacing='0' cellpadding='0'>
						              <tr>
						                <td width='18%' height='56'>
											 	<table width='100%' height='56' border='0' align='center' cellpadding='0' cellspacing='0'>
						                    <tr align='center' valign='bottom'> 
						                      <td height='26'><a href='javascript:doBold();' onMouseOver=\"MM_swapImage('editImage1','','".$path."/images/".$admininfo["language"]."/webedit/wtool1_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool1.gif' name='editImage1' width='19' height='18' border='0' id='editImage1'></a></td>
						                      <td><a href='javascript:doItalic();' onMouseOver=\"MM_swapImage('editImage2','','".$path."/images/".$admininfo["language"]."/webedit/wtool2_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool2.gif' name='editImage2' width='19' height='18' border='0' id='editImage2'></a></td>
						                      <td><a href='javascript:doUnderline();' onMouseOver=\"MM_swapImage('editImage3','','".$path."/images/".$admininfo["language"]."/webedit/wtool3_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool3.gif' name='editImage3' width='19' height='18' border='0' id='editImage3'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='3' colspan='3'></td>
						                    </tr>
						                    <tr align='center' valign='top'> 
						                      <td height='27'><a href='javascript:doLeft();' onMouseOver=\"MM_swapImage('editImage8','','".$path."/images/".$admininfo["language"]."/webedit/wtool8_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool8.gif' name='editImage8' width='19' height='18' border='0' id='editImage8'></a></td>
						                      <td><a href='javascript:doCenter();' onMouseOver=\"MM_swapImage('editImage9','','".$path."/images/".$admininfo["language"]."/webedit/wtool9_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool9.gif' name='editImage9' width='19' height='18' border='0' id='editImage9'></a></td>
						                      <td><a href='javascript:doRight();' onMouseOver=\"MM_swapImage('editImage10','','".$path."/images/".$admininfo["language"]."/webedit/wtool10_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool10.gif' name='editImage10' width='19' height='18' border='0' id='editImage10'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='".$path."/images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='19%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td width='100%' height='27' align='center' valign='bottom'><a href='javascript:doFont();' onMouseOver=\"MM_swapImage('editImage4','','".$path."/images/".$admininfo["language"]."/webedit/wtool4_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool4.gif' name='editImage4' height='22' border='0' id='editImage4'></a></td>
						                    </tr>
						                    <tr>
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doSize();' onMouseOver=\"MM_swapImage('editImage11','','".$path."/images/".$admininfo["language"]."/webedit/wtool11_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool11.gif' name='editImage11' height='22' border='0' id='editImage11'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='".$path."/images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='20%'>
								<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doForcol();' onMouseOver=\"MM_swapImage('editImage5','','".$path."/images/".$admininfo["language"]."/webedit/wtool5_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool5.gif' name='editImage5' height='22' border='0' id='editImage5'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doBgcol();' onMouseOver=\"MM_swapImage('editImage12','','".$path."/images/".$admininfo["language"]."/webedit/wtool12_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool12.gif' name='editImage12' height='22' border='0' id='editImage12'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='".$path."/images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='18%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doImage();' onMouseOver=\"MM_swapImage('editImage6','','".$path."/images/".$admininfo["language"]."/webedit/wtool6_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool6.gif' name='editImage6' width='73' height='22' border='0' id='editImage6'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doTable();' onMouseOver=\"MM_swapImage('editImage13','','".$path."/images/".$admininfo["language"]."/webedit/wtool13_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool13.gif' name='editImage13' height='22' border='0' id='editImage13'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='".$path."/images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='25%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doLink();' onMouseOver=\"MM_swapImage('editImage7','','".$path."/images/".$admininfo["language"]."/webedit/wtool7_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool7.gif' name='editImage7' height='22' border='0' id='editImage7'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doMultilink();' onMouseOver=\"MM_swapImage('editImage14','','".$path."/images/".$admininfo["language"]."/webedit/wtool14_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='".$path."/images/".$admininfo["language"]."/webedit/wtool14.gif' name='editImage14' height='22' border='0' id='editImage14'></a></td>
						                    </tr>
						                  </table>
											 </td>
						              </tr>
						            </table>
									 </td>
						        </tr>
						      </table>
						      <input type='hidden' name='content' value=''>
						      <iframe align='right' id='iView'  style='width: 100%; height:".$height.";' scrolling='YES' hspace='0' vspace='0'></iframe>
						      <!-- html편집기 메뉴 종료 -->";
						      
						      
	return $mstring;

}

?>