<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
$page_title = "S/LMS 건별상세 통계";
$page_navigation = "메일링/SMS > SMS 발송 분석기 > S/LMS 건별상세 통계";
$include_menu = "campaign";


$db = new MySQL;
$mdb = new MySQL;
$sms_design = new SMS;

	//검색 1주일단위 디폴트
		if ($startDate == ""){
			$before7day = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));

			$startDate = date("Y-m-d", $before7day);
			$endDate = date("Y-m-d");

		}
	
	$mstring .="<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='margin-top:10px;'>
			<tr>
				<td align='left' colspan=6 >".GetTitleNavigation("SMS 발송로그", "마케팅지원 > SMS 발송목록 ")."</td>
			</tr>
			<tr>
				<td>
				<form name='searchmember' method='GET'>
				<input type='hidden' name='mode' value='search' />
				<input type=hidden name='license' value='".$admininfo[mall_domain_key]."'><!-- license로 변경 kbk 13/09/23 -->
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
					<td style='width:100%;' valign=top colspan=3>
						<table width=100%  border=0 cellpadding='0' cellspacing='0'>
							<tr>
								<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>

										<TABLE cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
										<TR>
											<TD bgColor=#ffffff style='padding:0 0 0 0;'>
											<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>발송일자
													</th>
													<td class='search_box_item'>
														".search_date('startDate','endDate',$startDate,$endDate)."
													</td>
												 </tr>
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>
														발송구분
													</th>
													<td class='search_box_item'>
														<input type='checkbox' name='type' value='hand' checked />수동발송
													</td>
												 </tr>
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>
														조건검색
													</th>
													<td class='search_box_item'>
														<table>
															<tr>
																<td>
																  <select name=search_type>
																		<option value='subject' ".CompareReturnValue("subject",$search_type,"selected").">제목</option>
																		<option value='opt_post' ".CompareReturnValue("opt_post",$search_type,"selected").">발송자</option>
																  </select>
																 </td>
																 <td><input type=text name='search_text' class=textbox value='".$search_text."' style='width:100%' ></td>
															</tr>
														</table>
													</td>
												 </tr>
											</table>
											</TD>
										</TR>
										</TABLE>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr >
					<td colspan=3 align=center style='padding:10px 0 20px 0'>
						<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
					</td>
				</tr>
				</table>
				</form>
				</td>
			</tr>";
	$mstring .="</table>";
	
	$mstring .="
			<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
			<tr>
				<td>";
	if($_GET["license"]){
		$mstring .=  $sms_design->getSMSProductLogCaseUTF8($_GET, $regdate);
	}else{
		$mstring .=  $sms_design->getSMSProductLogCaseUTF8($admininfo, $regdate);
	}
	
	$mstring .="
				</td>
			</tr>
			</table>
			";
	
	$Contents = $mstring;
	
	$Script = "<script language='javascript' src='../include/DateSelect.js'></script>
<script language='javascript' >

</script>";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = campaign_menu();
$P->Navigation = $page_navigation;
$P->title = $page_title;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>