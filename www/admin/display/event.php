<? 
include("../class/layout.class");


$Contents = "

<table width='660' border='0' align='left'>
  <tr>
    <td align='right'>[ <a href=\"javascript:swapObj('TG_INPUT')\">내용입력</a> ]</td>
  </tr>
  <tr>
    <td>
      <div id='TG_INPUT' style='position: relative; display: block;'>
        <form name='INPUT_FORM' method='post' enctype='multipart/form-data' onSubmit=\"return EventAct('insert',null)\"><input type='hidden' name=act value='insert'>
        <table border='0' width='658' cellspacing='1' cellpadding='0'>
          <tr>
            <td bgcolor='#6783A8'>
              <table border='0' cellspacing='0' cellpadding='15' width='100%'>
                <tr>
                  <td bgcolor='#F8F9FA'>
                    <table border='0' width='100%'>
                      <tr bgcolor='#F8F9FA'>
                        <td width='50' nowrap>제목</td>
                        <td>".SelectDiv($selectdiv)." <input class='input' type='text' name='subj' value='".$db->dt[subj]."' size='54' maxlength='50'></td>
                      </tr>
                      <tr bgcolor='#F8F9FA'>
                        <td colspan='2' style='padding-left:80px;'>
                        <!--메인화면<input type='checkbox' name='main' value='1'> 
                        팝업<input type='checkbox' name='pop' value='1'>--><input type='hidden' name='pop' value='1'>	 
	                    width : <input type='text' name='width' value='".$db->dt[width]."' size=4>
	                    height : <input type='text' name='height' value='".$db->dt[height]."' size=4>                       	
	                    표시<input type='checkbox' name='disp' value='1'>
	                    <!--HTML사용<input type='checkbox' name='html' value='1'--> 	                    
						<!--input class='button' type='button' value='입력' onClick=\"EventAct('insert',null)\">&nbsp;
						<input class='button' type='button' value='취소' onClick=\"swapObj('TG_INPUT')\"-->
						</td>
                      </tr>
                      <tr bgcolor='#F8F9FA'>                        
                        <td colspan=2>                        
                 <table width='620' border='0' cellspacing='0' cellpadding='0' height='25'>                   
                    <tr>
                      <td height='30' colspan='3'>						      
						      <table id='tblCtrls' width='100%' border='0' cellspacing='1' cellpadding='0' align='center'>
						        <tr> 
						          <td bgcolor='F5F6F5'>
									 <table width='100%' border='0' cellspacing='0' cellpadding='0'>
						              <tr>
						                <td width='18%' height='56'>
											 	<table width='100%' height='56' border='0' align='center' cellpadding='0' cellspacing='0'>
						                    <tr align='center' valign='bottom'> 
						                      <td height='26'><a href='javascript:doBold();' onMouseOver=\"MM_swapImage('editImage1','','../webedit/image/wtool1_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool1.gif' name='editImage1' width='19' height='18' border='0' id='editImage1'></a></td>
						                      <td><a href='javascript:doItalic();' onMouseOver=\"MM_swapImage('editImage2','','../webedit/image/wtool2_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool2.gif' name='editImage2' width='19' height='18' border='0' id='editImage2'></a></td>
						                      <td><a href='javascript:doUnderline();' onMouseOver=\"MM_swapImage('editImage3','','../webedit/image/wtool3_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool3.gif' name='editImage3' width='19' height='18' border='0' id='editImage3'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='3' colspan='3'></td>
						                    </tr>
						                    <tr align='center' valign='top'> 
						                      <td height='27'><a href='javascript:doLeft();' onMouseOver=\"MM_swapImage('editImage8','','../webedit/image/wtool8_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool8.gif' name='editImage8' width='19' height='18' border='0' id='editImage8'></a></td>
						                      <td><a href='javascript:doCenter();' onMouseOver=\"MM_swapImage('editImage9','','../webedit/image/wtool9_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool9.gif' name='editImage9' width='19' height='18' border='0' id='editImage9'></a></td>
						                      <td><a href='javascript:doRight();' onMouseOver=\"MM_swapImage('editImage10','','../webedit/image/wtool10_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool10.gif' name='editImage10' width='19' height='18' border='0' id='editImage10'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='19%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td width='100%' height='27' align='center' valign='bottom'><a href='javascript:doFont();' onMouseOver=\"MM_swapImage('editImage4','','../webedit/image/wtool4_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool4.gif' name='editImage4' width='84' height='22' border='0' id='editImage4'></a></td>
						                    </tr>
						                    <tr>
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doSize();' onMouseOver=\"MM_swapImage('editImage11','','../webedit/image/wtool11_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool11.gif' name='editImage11' width='84' height='22' border='0' id='editImage11'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='20%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doForcol();' onMouseOver=\"MM_swapImage('editImage5','','../webedit/image/wtool5_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool5.gif' name='editImage5' width='95' height='22' border='0' id='editImage5'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doBgcol();' onMouseOver=\"MM_swapImage('editImage12','','../webedit/image/wtool12_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool12.gif' name='editImage12' width='95' height='22' border='0' id='editImage12'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='18%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doImage();' onMouseOver=\"MM_swapImage('editImage6','','../webedit/image/wtool6_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool6.gif' name='editImage6' width='73' height='22' border='0' id='editImage6'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doTable();' onMouseOver=\"MM_swapImage('editImage13','','../webedit/image/wtool13_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool13.gif' name='editImage13' width='73' height='22' border='0' id='editImage13'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='25%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doLink();' onMouseOver=\"MM_swapImage('editImage7','','../webedit/image/wtool7_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool7.gif' name='editImage7' width='74' height='22' border='0' id='editImage7'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doMultilink();' onMouseOver=\"MM_swapImage('editImage14','','../webedit/image/wtool14_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool14.gif' name='editImage14' width='111' height='22' border='0' id='editImage14'></a></td>
						                    </tr>
						                  </table>
											 </td>
						              </tr>
						            </table>
									 </td>
						        </tr>
						      </table>
						      <input type='hidden' name='content' value=''>
						      <input type='hidden' name='text' value=''>
						      <iframe align='right' id='iView' style='width: 100%; height:310;' scrolling='YES' hspace='0' vspace='0'></iframe>
						      <!-- html편집기 메뉴 종료 -->						      
                      </td>
                    </tr>
                    <tr style='display:block;'>
          	          <td width='120' height='25' align='center' bgcolor='#F0F0F0'></td>
          		       <td colspan='2' align='right'>&nbsp; 
						      <a href='javascript:doToggleText();' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
          			      <a href='javascript:doToggleHtml();' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
                      </td>
                    </tr>
                    <tr> 
                      <td bgcolor='D0D0D0' height='1' colspan='4'></td>
                    </tr>
                    <tr><td colspan=3 align=right style='padding:10px;'><input type=image src='../image/b_save.gif' border=0></td></tr>
                  </table>
                        </td>
                        
                      </tr>
                      
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        </form>
      </div>
    </td>
  </tr>
  <tr>
    <td align='left'>
    
    

<table width='660' border='0' cellpadding='0' cellspacing='0' align='center'>
  <tr bgcolor='#CCCCCC' height='25'>
    <td width='80' class=s_td align='center'><font color='#000000'><b>번호</b></font></td>
    <td width='340' class=m_td  align='left'><font color='#000000'><b>제목</b></font></td>
    <td width='80' class=m_td align='center'><font color='#000000'><b>날짜</b></font></td>
    <td width='80' class=m_td align='center'><font color='#000000'><b>조회</b></font></td>
    <td width='80' class=e_td align='center'><font color='#000000'><b>관리</b></font></td>
  </tr>";


	$max = 15; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}
/*	
	function page_bar($total, $page, $max)
	{
		global $sort, $ctgr;

		if ($total % $max > 0)
		{
			$total_page = floor($total / $max) + 1;
		}
		else
		{
			$total_page = floor($total / $max);
		}

		$next = $page + 1;
		$prev = $page - 1;

		if ($total)
		{
			$prev_mark = ($prev > 0) ? "<a href=event.php?page=$prev>◀</a> " : "◁ ";
			$next_mark = ($next <= $total_page) ? " <a href=event.php?page=$next>▶</a>" : " ▷";
		}

		$page_string = $page_string.$prev_mark;

		for ($i = $page - 3; $i <= $page + 3; $i++)
		{
			if ($i > 0)
			{
				if ($i <= $total_page)
				{
					if ($i != $page)
					{
						$Contents = $Contents." <a href=event.php?page=$i>$i</a> ";
					}
					else
					{
						$Contents = $Contents."<font color=#FF0000>$i</font>";
					}
				}
			}
		}

		$page_string = $page_string.$next_mark;
	}

*/
	$db = new Database;

	$db->query("SELECT * FROM ".TBL_SHOP_EVENT_INFO." ORDER BY date DESC");

	$total = $db->total;

	$db->query("SELECT *, UNIX_TIMESTAMP(date) AS date FROM ".TBL_SHOP_EVENT_INFO." ORDER BY date DESC LIMIT $start, $max");

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;
		
$Contents = $Contents."
  <tr height='23' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; this.style.cursor='hand'\" onMouseOut=\"this.style.backgroundColor=''\">
    <td align='center' onClick=\"swapObj('".'TG_VIEW_'.$db->dt[no]."')\">".$no."</td>
    <td onClick=\"swapObj('".'TG_VIEW_'.$db->dt[no]."')\">&nbsp;".$db->dt[subj]."</td>
    <td align='center' onClick=\"swapObj('".'TG_VIEW_'.$db->dt[no]."')\">".date("Y.m.d", $db->dt[date])."</td>
    <td align='center' onClick=\"swapObj('".'TG_VIEW_'.$db->dt[no]."')\">".$db->dt[hits]."</td>
    <td align='center'>[<a href=\"javascript:swapObj('".'TG_EDIT_'.$db->dt[no]."','".$db->dt[no]."')\">수정</a>] [<a href=\"javascript:EventAct('delete','".$db->dt[no]."')\">삭제</a>]</td>
  </tr>
  <tr hegiht=1><td colspan=5 background='../image/dot.gif'></td></tr>
  <tr>
    <td colspan='5' align='center'>
      <div id='TG_VIEW_".$db->dt[no]."' style='position: relative; display: none;'>
      <table border='0' width='658' cellspacing='1' cellpadding='0'>
        <tr>
          <td bgcolor=#6783A8>
            <table border='0' cellspacing='1' cellpadding='15' width='100%'>
              <tr>
                <td bgcolor='#F8F9FA'>
<!-- 뉴스내용 시작 -->";

if ($db->dt[html]){
	$Contents = $Contents.$db->dt[text]."\n";
	//$Contents = $Contents.nl2br($db->dt[text])."\n";
}else{
	$Contents = $Contents.$db->dt[text]."\n";
	//$Contents = $Contents.nl2br($db->dt[text])."\n";
}

$Contents = $Contents."
<!-- 뉴스내용 마침 -->
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      </div>

      <div id='TG_EDIT_".$db->dt[no]."' style='position: relative; display: none;'>
        <form name='EDIT_".$db->dt[no]."' method='post' enctype='multipart/form-data' onSubmit='return false'>
        <table border='0' width='658' cellspacing='1' cellpadding='0'>
          <tr>
            <td bgcolor='#6783A8'>
              <table border='0' cellspacing='1' cellpadding='15' width='100%'>
                <tr>
                  <td bgcolor='#F8F9FA'>
                    <table border='0' width='100%'>
                      <tr bgcolor='#F8F9FA'>
                        <td width='80'>제목</td>
                        <td>".SelectDiv($db->dt[div])."<input class='input' type='text' name='subj' value='".$db->dt[subj]."' size='54' maxlength='50'></td>
                      </tr>
                      <tr bgcolor='#F8F9FA'> ";
 if ($db->dt[main])	$check_main = " checked"; 
 if ($db->dt[pop])	$check_pop = " checked"; 
 if ($db->dt[html])	$check_html = " checked"; 
 if ($db->dt[disp])	$check_disp = " checked";                       

$Contents .= "                                             
                        <td colspan=2 style='padding-left:80px;'>
                        <!--메인화면<input type='checkbox' name='main' value='1'".$check_main."> 팝업<input type='checkbox' name='pop' value='1'".$check_pop."--><input type='hidden' name='pop' value='1'>	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;width : <input type='text' name='width' value='".$db->dt[width]."' size=4> &nbsp;&nbsp;&nbsp;height : <input type='text' name='height' value='".$db->dt[height]."' size=4>
	                        	<a href=\"JavaScript:PoPWindow('./pop.php?no=".$db->dt[no]."','".$db->dt[width]."','".$db->dt[height]."','pop".$db->dt[no]."')\">팝업보기</a>
	                        	표시<input type='checkbox' name='disp' value='1'".$check_disp.">
                        </td>
                      </tr>
                      <tr bgcolor='#F8F9FA'>                        
                        <td colspan=2>
                        <textarea class='input' name='text' cols='82' rows='10' style='display:none'>".$db->dt[text]."</textarea>
                        <iframe id='iframeEDIT_".$db->dt[no]."' frameborder='0' scrolling='no' width='590' height='400' src='../webedit/iframeEdit.php'></iframe>
                        </td>
                      </tr>
                      <tr bgcolor='#F8F9FA'>
                        <td colspan='2' align='right'>";
                        
                        	
                        	 
$Contents .= "
                          	<table cellpadding=0 cellspacing=0 width='100%'>
	                        <tr>
	                        	<td align=left>
	                        	
	                        	</td>
	                        	<td align=right>	                        	
						<!--HTML사용<input type='checkbox' name='html' value='1'".$check_html."-->
						<input class='button' type='button' value='수정' onClick=\"EventAct('update','".$db->dt[no]."')\">&nbsp;
						<input class='button' type='button' value='취소' onClick=\"swapObj('TG_EDIT_".$db->dt[no]."')\">
	                        	</td>
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
        </table>
        </form>
      </div>

    </td>
  </tr>";
  
	$check_main = "";
	$check_pop = "";
	$check_html = "";
	$check_disp = "";
	}

	if (!$db->total){
  	$Contents = $Contents."<tr><td colspan='5' align='center'>등록된 데이타가 없습니다.</td></tr>";
	}
	
$Contents = $Contents."
  <tr><td height='20' colspan='5' align='center' bgcolor='#CCCCCC'>&nbsp;".page_bar($total, $page, $max)."&nbsp;</td></tr>
</table>
<br>
	</td>
  </tr>
</table>
<!--iframe id='act' frameborder='0' scrolling='no' width='0' height='0' src=''></iframe-->
<form name='lyrstat'><input type='hidden' name='opend' value=''></form>";


$Script = "<Script Language='JavaScript' src='/include/redsun.js'></Script>\n<script language='javascript' src='tglib_event.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->OnloadFunction = "Init(document.INPUT_FORM);MM_preloadImages('../webedit/images/wtool1_1.gif','../webedit/images/wtool2_1.gif','../webedit/images/wtool3_1.gif','../webedit/images/wtool4_1.gif','../webedit/images/wtool5_1.gif','../webedit/images/wtool6_1.gif','../webedit/images/wtool7_1.gif','../webedit/images/wtool8_1.gif','../webedit/images/wtool9_1.gif','../webedit/images/wtool11_1.gif','../webedit/images/wtool13_1.gif','../webedit/images/wtool10_1.gif','../webedit/images/wtool12_1.gif','../webedit/images/wtool14_1.gif','../webedit/images/bt_html_1.gif','../webedit/images/bt_source_1.gif')";//showSubMenuLayer('storeleft');
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();


function SelectDiv($selectdiv)
{
	$divname = array ("공지구분선택","메인이벤트","쇼핑이벤트","아카데미이벤트","커뮤니티이벤트","이벤트/기획전","  ");
	
	return "<input type=hidden name=div value='2'>";
	
	$pos = 0;
	$strDiv = "<Select name='div'>\n";
//	$strDiv = $strDiv."<option value=0>공지구분선택</option>\n";
	while(hasMoreElements(&$divname))
	{
	       	if( $pos == $selectdiv )
	       	{
	        	$strDiv = $strDiv."<option value='".($pos)."' Selected>".$divname[$pos]."</option>\n";
	       	}else{
	       		$strDiv = $strDiv."<option value='".($pos)."'>".$divname[$pos]."</option>\n";
		}	       
	       	$pos++;
	}	

	$strDiv = $strDiv."</Select>\n";
	
	return $strDiv;
	
//	$strDiv = $strDiv."<option value=1>전체공지</option>\n";
//	$strDiv = $strDiv."<option value=2>쇼핑공지</option>\n";
//	$strDiv = $strDiv."<option value=3>아카데미공지</option>\n";
//	$strDiv = $strDiv."<option value=4>커뮤니티공지</option>\n";
}


?>