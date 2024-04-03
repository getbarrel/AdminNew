<?
include("../class/layout.class");
include("../bbsmanage/bbs.lib.php");

$db = new Database;
$mdb = new Database;

$Script = "	<link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' />
<script type='text/javascript' src='../js/ui/ui.core.js'></script>
<script type='text/javascript' src='../js/ui/ui.datepicker.js'></script>

<script language='javascript'>
		$(function() {
			$(\"#start_datepicker\").datepicker({
			//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			//showMonthAfterYear:true,
			dateFormat: 'yymmdd',
			buttonImageOnly: true,
			buttonText: '달력',
			onSelect: function(dateText, inst){
				//alert(dateText);
				if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
					$('#end_datepicker').val(dateText);
				}else{
					$('#end_datepicker').datepicker('setDate','+0d');
				}
			}

			});

			$(\"#end_datepicker\").datepicker({
			//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			//showMonthAfterYear:true,
			dateFormat: 'yymmdd',
			buttonImageOnly: true,
			buttonText: '달력'

			});

			//$('#end_timepicker').timepicker();
		});



		function select_date(FromDate,ToDate,dType) {
			var frm = document.searchmember;

			$(\"#start_datepicker\").val(FromDate);
			$(\"#end_datepicker\").val(ToDate);
		}


		function showObj(id)
		{
			obj = eval(id+'.style');
			obj.display = 'block';

			document.lyrstat.opend.value = id;
		}

		function hideObj(id)
		{
			obj = eval(id+'.style');
			obj.display = 'none';

			document.lyrstat.opend.value = '';
		}

		function swapObj(id)
		{

			obj = eval(id+'.style');
			stats = obj.display;

			if (stats == 'none')
			{
				if (document.lyrstat.opend.value)
					hideObj(document.lyrstat.opend.value);

				showObj(id);
			}
			else
			{
				hideObj(id);
			}
		}

		function BoardDelete(bm_ix){
			if(confirm('해당 게시판을 정말로 삭제하시겠습니까? 게시판을 삭제 하시면 관련 데이타 모두가 삭제 됩니다.')){
				document.location.href='board.manage.act.php?act=delete&bm_ix='+bm_ix
			}
		}

		</script>";

$vdate = date("Ymd", time());
$today = date("Ymd", time());
$vyesterday = date("Ymd", time()-84600);
$voneweekago = date("Ymd", time()-84600*7);
$vtwoweekago = date("Ymd", time()-84600*14);
$vfourweekago = date("Ymd", time()-84600*28);
$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

/*
<tr>
	    <td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 사업자 정보</b></div>")."</td>
	  </tr>
*/
$db->query("SELECT * FROM bbs_manage_config ");

$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("최근게시물 목록 관리", "마케팅지원 > 최근게시물 목록 관리 ")."</td>
		</tr>
		<tr>
			<td>
				<form name='searchmember' style='display:inline;'>
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
					<td style='width:100%;' valign=top colspan=3>
						<table width=100%  cellpadding='0' cellspacing='0'  border=0>
							<!--tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>적립금 검색하기</b></td></tr-->
							<tr>
								<td align='left' colspan=2  width='100%' valign=top style='padding-top:0px;'>
									<table class='box_shadow' cellpadding=0 cellspacing=0 style='width:100%;' align=left>
										<tr>
											<th class='box_01'></th>
											<td class='box_02'></td>
											<th class='box_03'></th>
										</tr>
										<tr>
											<th class='box_04'></th>
											<td class='box_05' valign=top style='padding:0px;'>
												<TABLE height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
												<TR>
													<TD bgColor=#ffffff style='padding:0 0 0 0;'>
													<table cellpadding=3 cellspacing=1 width='100%' class='search_table_box'>

														<!--tr>
															<th bgcolor='#efefef' width='150' align=center>상태 </th>
															<td colspan=2>
															<select name='state' >
																<option value=''>상태값을 선택해 주세요</option>
																<option value='".RESERVE_STATUS_READY."' ".CompareReturnValue(RESERVE_STATUS_READY,$state,"selected").">적립대기</option>
																<option value='".RESERVE_STATUS_COMPLETE."' ".CompareReturnValue(RESERVE_STATUS_COMPLETE,$state,"selected").">적립완료</option>
																<option value='".RESERVE_STATUS_USE."' ".CompareReturnValue(RESERVE_STATUS_USE,$state,"selected").">사용내역</option>
																<option value='".RESERVE_STATUS_ORDER_CANCEL."' ".CompareReturnValue(RESERVE_STATUS_ORDER_CANCEL,$state,"selected").">적립취소</option>
															</select>
															</td>
														</tr>
														 <tr height=1><td colspan=4 class='dot-x'></td></tr-->
														 <tr height=27>
															<th class='search_box_title' bgcolor='#efefef' width='150' align=center>조건검색 </th>
															<td class='search_box_item'>
															<table cellpadding=0 cellspacing=0 width=100%>
																<col width=110>
																<col width=*>
																<tr>
																	<td>
																	<select name=search_type style='width:100px;'>
																		<option value='bbs_name' ".CompareReturnValue("bbs_name",$search_type,"selected").">작성자</option>
																		<option value='bbs_subject' ".CompareReturnValue("bbs_subject",$search_type,"selected").">제목</option>
																		<option value='bbs_contents' ".CompareReturnValue("bbs_contents",$search_type,"selected").">내용</option>
																	</select>
																	</td>
																	<td>
																	<input type=text name='search_text' class=textbox value='".$search_text."' style='width:50%' >
																	</td>
																</tr>
															</table>
															</td>
														</tr>
														<tr height=27>
														  <td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>작성일자</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);'".CompareReturnValue("1",$regdate,"checked")."></td>
														  <td class='search_box_item' align=left style='padding-left:5px;'>
															<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
																<col width=70>
																<col width=20>
																<col width=70>
																<col width=*>
																<tr>
																	<TD nowrap>
																	<input type='text' name='sdate' class='textbox' value='".$sdate."' style='height:20px;width:70px;text-align:center;' id='start_datepicker'>
																	<!--SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년
																	<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월
																	<SELECT name=FromDD></SELECT> 일 -->
																	</TD>
																	<TD align=center> ~ </TD>
																	<TD nowrap>
																	<input type='text' name='edate' class='textbox' value='".$edate."' style='height:20px;width:70px;text-align:center;' id='end_datepicker'>
																	<!--SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년
																	<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월
																	<SELECT name=ToDD></SELECT> 일 -->
																	</TD>
																	<TD style='padding:0px 10px'>
																		<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
																		<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
																		<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
																		<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
																		<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
																		<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
																		<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
																	</TD>
																</tr>
															</table>
														  </td>
														</tr>
													</table>
													</TD>
												</TR>

												</TABLE>
											</td>
											<th class='box_06'></th>
										</tr>
										<tr>
											<th class='box_07'></th>
											<td class='box_08'></td>
											<th class='box_09'></th>
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
					</td>
				</tr>
				</table>
				</form>
			</td>
		</tr>
		<tr>
			<td>
			".PrintBoardRecentList()."
			</td>
		</tr>
		";
$mstring .="</table>";

/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >생성된 게시판의 최근 게시물 모음 입니다. 모든 게시판을 확인할 필요가 없이 이곳에서 확인후 답글을 달아 주시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >제목을 클릭하시면 해당 게시판으로 넘어가게 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >고객센타 최근게시물에 노출여부를 노출함 으로 선택하신 게시판만 최근게시물에 노출되게 됩니다</td></tr>
</table>
";
*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$mstring .= HelpBox("고객센타 최근게시물", $help_text);

$Contents = $mstring;

//$Script = "<script language='javascript' src='basicinfo.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = cscenter_menu();
$P->Navigation = "고객센타 > 고객센타 최근게시물";
$P->title = "고객센타 최근게시물";
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintBoardRecentList(){
	global $db, $mdb, $page, $admin_config;
	global $sdate, $edate, $search_type, $search_text;

	//$sql = "select bmc.* , bg.div_name as group_name from bbs_manage_config bmc , bbs_group bg where bmc.board_group = bg.div_ix and disp = 1 $where";
	$sql = "select * from bbs_manage_config bmc , bbs_group bg 
				where bmc.board_group = bg.div_ix and disp = 1 and bmc.board_style = 'bbs' 
				and bmc.recent_list_display = 'Y' 
				and bg.div_ix = '7' ";
	$mdb->query($sql);

	$max = 20;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	if($mdb->dbms_type == "oracle"){
		$where = " where 1=1 ";
	}else{
		$where = " where 1=1 ";
	}

	if($sdate && $edate){
		$where .= " and DATE_FORMAT(regdate,'%Y%m%d') between ".$sdate." and ".$edate." ";
	}

	if($search_text && $search_type){
		$where .= " and $search_type LIKE '%".$search_text."%' ";
	}
	//echo $where;

	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver class='list_table_box'>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=30 style='font-weight:600;'><td class=s_td width='20%'>게시판</td><td class=m_td width='40%'>제목</td><td class=m_td width='10%'>작성자</td><td class=m_td width='20%'>등록일</td><td class=e_td width='10%'>조회수</td></tr>";
	if ($mdb->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td class='list_box_td' colspan=9 align=center>최근 게시물 내역이 존재 하지 않습니다..</td></tr>";
	}else{
		$j=0;

		if($mdb->dbms_type == "oracle"){
			//$sql = "create global temporary table   bbs_tmp   AS ";
			$sql = "  ";
			$rownum_sql = " and rownum < 30  ";
			$limit_sql = "";

			$db->query("delete from bbs_tmp ");
		}else{
			$sql = "create temporary table IF NOT EXISTS bbs_tmp ENGINE = MEMORY ";
			$limit_sql = " limit 30 ";
			$rownum_sql = "";
		}

		//$sql = "create temporary table bbs_tmp ENGINE = MEMORY ";

		for($j=0;$j < $mdb->total;$j++){
			$mdb->fetch($j);
			if($j == 0){
				$column_len1=strlen($mdb->dt[board_name]);
				$column_len2=strlen($mdb->dt[board_ename]);
				// 최초 입력되는 값이 1:1문의라서 column의 길이가 1:1문의에 맞춰짐 그래서 1:1문의보다 길이가 길면 스페이스바로 길이를 늘이고 1:1문의와 같다면 길이를 그대로 놔둔다. 정확한 이유는 파악못함 kbk 12/02/09
				if($mdb->dbms_type == "oracle"){
					$sql .= "select CASE WHEN length('".$mdb->dt[board_name]."')>".$column_len1." THEN '".$mdb->dt[board_name]."               ' ELSE '".$mdb->dt[board_name]."' END as board_name,
							CASE WHEN length('".$mdb->dt[board_ename]."')>".$column_len2." THEN '".$mdb->dt[board_ename]."               ' ELSE '".$mdb->dt[board_ename]."' END as board_ename,
							bbs_ix, bbs_subject, bbs_name, bbs_hit, bbs_re_cnt, regdate,
							case when regdate > SYSDATE - interval '3' day  then 1 else 0 end as new
							from bbs_".$mdb->dt[board_ename]."
							$where $rownum_sql
							order by regdate desc $limit_sql";
				}else{
					$sql .= "select if(length('".$mdb->dt[board_name]."')>".$column_len1.",'".$mdb->dt[board_name]."               ','".$mdb->dt[board_name]."') as board_name, if(length('".$mdb->dt[board_ename]."')>".$column_len2.",'".$mdb->dt[board_ename]."               ','".$mdb->dt[board_ename]."') as board_ename,  bbs_ix, bbs_subject, bbs_name, bbs_hit, bbs_re_cnt, regdate,
							case when regdate > DATE_SUB(now(), interval 3 day) then 1 else 0 end as new
							from bbs_".$mdb->dt[board_ename]."
							$where $rownum_sql
							order by regdate desc $limit_sql";
				}
			}else{
				$sql = "insert into bbs_tmp
						select '".$mdb->dt[board_name]."' as board_name,  '".$mdb->dt[board_ename]."' as board_ename,  bbs_ix, bbs_subject, bbs_name, bbs_hit, bbs_re_cnt, regdate, case when regdate > DATE_SUB(now(), interval '3' day) then 1 else 0 end as new
						from bbs_".$mdb->dt[board_ename]."
						$where $rownum_sql
						order by regdate desc $limit_sql";
			//	echo $sql;
			}
			//exit;
			//echo $sql;
			$db->query($sql);
		}
		//echo $sql;
		//$db->query($sql);

		$db->query("select count(*) as total from bbs_tmp order by  regdate desc");
		$db->fetch();
		$total = $db->dt[total];

		if($total){
			$db->query("select * from bbs_tmp order by  regdate desc limit $start, $max");

			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);

				//$no = $no + 1;

				$mString = $mString."<tr height=30 bgcolor=#ffffff align=center>
				<td class='list_box_td point' ><a href='./bbs.php?mode=list&board=".$db->dt[board_ename]."'>[".$db->dt[board_name]."]</td>
				<td class='list_box_td' align=left style='text-align:left;padding-left:20px;'><a href='./bbs.php?mode=read&board=".$db->dt[board_ename]."&bbs_ix=".$db->dt[bbs_ix]."'>".($db->dt[bbs_subject] ? $db->dt[bbs_subject]:"제목없음")."</a> ".($db->dt[bbs_re_cnt] != 0 ? "<img src='../../bbs_templet/admin/icon/icon_reply.gif' border='0' align=absmiddle>":"")." <font class='eboard_recount'> [".$db->dt[bbs_re_cnt]."] </font>".CheckNewContents($db->dt['new'], "../../bbs_templet/admin/")."</td>
				<td class='list_box_td list_bg_gray' >".$db->dt[bbs_name]."</td>
				<td class='list_box_td'>".str_replace("-",".",substr($db->dt[regdate],0,10))."</td>
				<td class='list_box_td list_bg_gray' >".$db->dt[bbs_hit]."</td>
				<!--td align=center>
					<a href=JavaScript:deleteDisplayOption('delete','".$db->dt[dp_ix]."','$pid')><img  src='../image/si_remove.gif' border=0></a>
				</td-->
				</tr>
				";
			}
		}else{
				$mString .= "<tr height=50 bgcolor=#ffffff><td colspan=5 class='list_box_td' >정보가 존재 하지 않습니다.</td></tr>";
		}

	}

	$mString .= "</table>";
	$mString .= "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver >";
	$mString .= "<tr height=50 bgcolor=#ffffff><td colspan=5 align=right>".page_bar($total, $page, $max,  "&max=$max&search_type=$search_type&search_text=$search_text&sdate=$sdate&edate=$edate","")."</td></tr>
					</table>";

	return $mString;
}


?>
