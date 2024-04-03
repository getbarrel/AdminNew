<?
$script_time[start] = time();
include("../class/layout.class");
include("./member_region.chart.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
$script_time[start] = time();
//print_r($_SESSION);

$db = new Database;
$mdb = new Database;
$sms_design = new SMS;
//print_r($admininfo);
//print_r($admin_config);

$year = date("Y"); 
$month = date("m"); 
$day = date("d");  

$w = date('w',mktime(0,0,0,$month,$day,$year)); 
// strtotime(sprintf('%4d-%02d-%02d',$year,$month,$day)) 

$week_first_day =  date('Ymd',mktime(0,0,0,$month,$m=$day-($w+6)%7,$year))."\n"; // 2010-04-10 
$week_last_day =  date('Y-m-d',mktime(0,0,0,$month,$m+6,$year))."\n"; // 2010-04-10 
/* 
echo date('Y-m-d',mktime(0,0,0,$month,$day-($w+6)%7,$year))."\n"; 
echo date('Y-m-d',mktime(0,0,0,$month,$day+(7-$w)%7,$year))."\n"; 
*/ 


$Script = "


<Script language='javascript'>
function showTabContents(vid, tab_id){
	var area = new Array('recent_order','recent_contents','recent_use_after');
	var tab = new Array('tab_01','tab_02','tab_03');

	for(var i=0; i<area.length; ++i){
		if(area[i]==vid){
			document.getElementById(vid).style.display = 'block';
			document.getElementById(tab_id).className = 'on';
		}else{
			document.getElementById(area[i]).style.display = 'none';
			document.getElementById(tab[i]).className = '';
		}
	}
}

function useAfterDelete(uf_ix){
	if(confirm('사용후기를 정말로 삭제하시겠습니까? ')){
		document.frames['act'].location.href='../marketting/useafter.act.php?act=delete&uf_ix='+uf_ix
	}
}
function checkAllkrDomain(obj){
	var frm = document.domain_search;
	for(var i=0;i<frm.kdomain.length;i++){
		if(obj.checked){
			frm.kdomain[i].checked = true;
		}else{
			frm.kdomain[i].checked = false;
		}
	}
}

function checkAllcomDomain(obj){
	var frm = document.domain_search;
	for(var i=0;i<frm.edomain.length;i++){
		if(obj.checked){
			frm.edomain[i].checked = true;
		}else{
			frm.edomain[i].checked = false;
		}
	}
}
</Script>";
//$script_time[sms_start] = time();
$sms_cnt = $sms_design->getSMSAbleCount($admininfo);
//$script_time[sms_end] = time();
$Contents01 = "
<table cellpadding=3 cellspacing=0 border='0' align='left'>
	<tr>
		<td width=80% valign=top>
		<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
		  <tr>
			<td valign=top height=300>
			<div class='tab' style='margin: 0px 0px ;'>
				<table class='s_org_tab'>
				<tr>
					<td class='tab'>
						<!--table id='tab_01' class='on' >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"showTabContents('recent_order','tab_01')\" style='padding-left:20px;padding-right:20px;'>신규주문리스트</td>
							<th class='box_03'></th>
						</tr>
						</table-->
						<table id='tab_02' 	class='on' ".($admininfo[admin_level] == 9 ? "style='display:inline'":"style='display:none'").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"showTabContents('recent_contents','tab_02')\" style='padding-left:20px;padding-right:20px;'>최근게시물현황</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<!--table id='tab_03' ".($admininfo[admin_level] == 9 ? "style='display:inline'":"style='display:none'").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"showTabContents('recent_use_after','tab_03')\" style='padding-left:20px;padding-right:20px;' nowrap>최근사용후기</td>
							<th class='box_03'></th>
						</tr>
						</table-->
					</td>
					<td class='btn'>

					</td>
				</tr>
				</table>
			</div>
			<div class='t_no' style='margin: 2px 0px ; border-top: solid 3px #c6c6c6; '>
				<!-- my_movie start -->
				<div class='my_box' >
					<div id='recent_order' style='padding:5px 5px 5px 0px;display:none;'>
					</div>
					<div id='recent_contents' style='padding:5px 5px 5px 0px;height:200px;'>";
	if($admininfo[admin_level] == 9 ){
	$script_time[board_summary_start] = time();
	$Contents01 .= "
						 ".PrintBoardSummary()."";
	$script_time[board_summary_end] = time();
	}
	$Contents01 .= "
					</div>
					<div id='recent_use_after' style='padding:5px 5px 5px 0px;display:none;'>
	
					</div>
				</div>
			</div>
			</td>
			
		  </tr>
		  <tr height=20><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>주간 방문횟수</b></td></tr>
		  <tr>
			<td style='padding:15px 0px 0 0' align=center>
			<img src='../logstory/report/visit.chart.php?vdate=".$week_first_day."&SelectReport=2' >
			</td>
		  </tr>
		  <tr>
			<td style='padding:15px 0px 0 0' align=left>
				<table width=100% border=0>
					<col width=50%>
					<col width=50%>
					<tr>
						<td>
						<table width=100% border=0 >
							<col width=100%>
							<tr height=25><td align=left><img src='../images/dot_org.gif' align=absmiddle> <b>공지사항</b></td></tr>
							<tr>
								<td valign=top height=130 style='border:1px solid #c0c0c0;padding:5px;'>
									<table width=100% border=0 >
										
										";
				$bbs_datas = fetch_bbs("bbs_notice", 5);
				for($i=0;$i < count($bbs_datas);$i++){
				$Contents01 .= "<tr>
									<td height=22>
										<img src='../images/no_".($i+1).".gif' align=absmiddle> 
										<a href='/admin/bbsmanage/bbs.php?mode=read&board=notice&bbs_ix=".$bbs_datas[$i][bbs_ix]."&page=1'> ".cut_str($bbs_datas[$i][bbs_subject],25)."</a>
									</td>
									<td align=right>".$bbs_datas[$i][regdate]."</td>
								</tr>";
				}

				$Contents01 .= "
										</table>
								</td>
							</tr>
						</table>
						</td>
						<td>
							<table width=100% border=0>
							<col width=100%>
							<tr height=25><td align=left><img src='../images/dot_org.gif' align=absmiddle> <b>1:1 문의</b></td><tr>
							<tr>
								<td valign=top style='border:1px solid #c0c0c0;padding:5px;'>
									<table width=100% border=0 >
										
										";
				$bbs_datas = fetch_bbs("bbs_qna", 5);
				for($i=0;$i < count($bbs_datas);$i++){
				$Contents01 .= "<tr>
									<td height=22><img src='../images/ico_answer.gif' > <a href='/admin/bbsmanage/bbs.php?mode=read&board=qna&bbs_ix=".$bbs_datas[$i][bbs_ix]."&page=1'>".cut_str($bbs_datas[$i][bbs_subject],25)."</a></td>
									<td align=right>".$bbs_datas[$i][regdate]."</td>
								</tr>";
				}
				

				$Contents01 .= "
										</table>
								</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>

			</td>
		  </tr>";



$Contents01 .= "
			<!--tr height=25><td style='padding:20 0 0 0;border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>도메인검색/등록</b></td></tr>
			<tr height=100>
			<td style='padding:8 0 0 0;'>
				<form method='post' name='domain_search' action='http://www.mallstory.com/addition/domain_search.php' target=_blank>
							<table cellpadding=2 cellspacing=0 border=0 style='width:100%'>
									<tr height=20 >
										<td colspan=4 style='padding:0 0 0 0' >
											<table class='box_06' width=100%>
												<tr>
													<th class='box_01'></th>
													<td class='box_02'></td>
													<th class='box_03'></th>
												</tr>
												<tr>
													<th class='box_04'></th>
													<td class='box_05' align=center>
														<table cellpadding=2 cellspacing=0 border=0 style='width:450px'>
															<tr><td colspan=4 align=left style='padding-left:85px;'><span>  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span> </td></tr>
															<tr >
																<td width=70 align=right nowrap style='font-size:12px;'></td>
																<td width=60 align=right nowrap>
																	<b style='font-size:15pt;'><b>WWW.</b></b>
																</td>
																<td >
																	<input type='text' id='fsearch' name='search_word' maxlength='50' class='input_board' style='background-color:#8b8b8b;width:320px;height:22px;color:white;border:1px solid #575757;font-size:14pt;text-align:center;font-weight:bold;' align='middle'>
																</td>
																<td style='padding:0 0 0 10'><input type=image src='../images/".$admininfo["language"]."/btc_search.gif' align='absmiddle' border='0' style='border:0px;' onFocus='this.blur();'></td>
															</tr>
														</table>
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
									<tr height=20 >
										<td colspan=4 style='padding:10 0 0 20' align=center >
											<table >
												<tr>
													<td style='padding:0 20 0 0' nowrap><input type=checkbox name='kr_all' id='kr_all' value='kr_all'  style='border:0px;' onclick='checkAllkrDomain(this);'><label for='kr_all'><b>KR 도메인</b></label></td>
													<td><input type=checkbox name='kdomain[]' id='kdomain' value='kr' conn_div='KR' style='border:0px;' checked><label for='kr'>.kr</label></td>
													<td><input type=checkbox name='kdomain[]' id='kdomain' value='co.kr' conn_div='KR' style='border:0px;' checked><label for='co.kr'>.co.kr</label></td>
													<td><input type=checkbox name='kdomain[]' id='kdomain' value='or.kr' conn_div='KR' style='border:0px;'><label for='or.kr'>.or.kr</label></td>
													<td><input type=checkbox name='kdomain[]' id='kdomain' value='ne.kr' conn_div='KR' style='border:0px;'><label for='ne.kr'>.ne.kr</label></td>
													<td><input type=checkbox name='kdomain[]' id='kdomain' value='pe.kr' conn_div='KR' style='border:0px;'><label for='pe.kr'>.pe.kr</label></td>
												</tr>
												<tr>
													<td style='padding:0 20 0 0' nowrap><input type=checkbox name='com_all' id='com_all' value='com_all'  style='border:0px;' onclick='checkAllcomDomain(this);'><label for='com_all'><b>국제 도메인</b></label></td>
													<td><input type=checkbox name='edomain[]' id='edomain' value='com' conn_div='COM' style='border:0px;' checked><label for='com'>.com</label></td>
													<td><input type=checkbox name='edomain[]' id='edomain' value='net' conn_div='COM' style='border:0px;' checked><label for='net'>.net</label></td>
													<td><input type=checkbox name='edomain[]' id='edomain' value='org' conn_div='COM' style='border:0px;'><label for='org'>.org</label></td>
													<td><input type=checkbox name='edomain[]' id='edomain' value='info' conn_div='COM' style='border:0px;'><label for='info'>.info</label></td>
													<td><input type=checkbox name='edomain[]' id='edomain' value='biz' conn_div='COM' style='border:0px;'><label for='biz'>.biz</label></td>
												</tr>
											</table>
										</td>
									</tr>
							  </table>

						</form>
			</td>
		  </tr>
			<tr height=50><td colspan=5></td></tr-->
		  </table>
		</td>
		<td valign=top ".($admininfo[admin_level] == 9 ? "style='display:inline'":"style='display:none'").">
			<table class='box_12' style='width:241px;height:70px' cellpadding=0 cellspacing=0>
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05' style='padding:0 0 0 0'>
						<table border=0>
							<tr>
								<td height=20 padding-top:0px;>
									<table width=223 border=0>
									<tr height=25>
										<td style='border-bottom:2px solid #efefef'>
											<table cellpadding=0 cellspacing=0>
												<tr>
													<td width=13><img src='../images/dot_org.gif' align=absmiddle> </td>
													<td><b>사이트 자원 현황</b></td>
												</tr>
											</table>
										
										</td>
									</tr>
									<tr>
										<td align='left' colspan=2 height=100 width='223px' valign=top style='padding-top:5px;'>";

	$Contents01 .= "
											<table cellpadding=0 cellspacing=1 width=100% bgcolor=#c0c0c0 border=0 style='border-collapse:separate; border-spacing:1px;'>
													<tr bgcolor=#ffffff height=22>
														<td class='leftmenu' style='padding:0 0 0 10px'><img src='../image/title_head.gif'> <b class=small>솔루션 타입 :</b> </td>
														<td align=right style='padding:0 5px 0 0px' >".getSolutionType($admininfo[mall_type])."  </td>
													</tr>";
	/*
	$script_time[hard_start] = time();
	$Contents01 .= "										    	<tr bgcolor=#ffffff><td class='leftmenu' style='padding:0 0 0 10'><img src='../image/title_head.gif'> <b class=small>사용하드용량 :</b> </td><td align=right>".@dirsize($_SERVER["DOCUMENT_ROOT"]."")." </td></tr>";
	$script_time[hard_end] = time();
	*/
	$script_time[hardimage_start] = time();

	//$Contents01 .= "										    	<tr bgcolor=#ffffff><td class='leftmenu' style='padding:0 0 0 10'><img src='../image/title_head.gif'> <b class=small>사용이미지용량 :</b></td><td align=right> </td></tr>	    	";
												$script_time[hardimage_end] = time();
												$Contents01 .= "<tr bgcolor=#ffffff height=22>
													<td class='leftmenu' style='padding:0 0 0 10px'><img src='../image/title_head.gif'> <b class=small>SMS 잔여 용량 : </td>
													<td align=right style='padding:0 5px 0 0px'><a href='../store/sms.point.php'>".$sms_cnt." 건 </a></td>
													</tr>	";


												$Contents01 .= "<tr bgcolor=#ffffff height=22>
													<td class='leftmenu' style='padding:0 0 0 10px'><img src='../image/title_head.gif'> <b class=small>총 상품갯수 : </td>
													<td align=right style='padding:0 5px 0 0px'> 개</td>
													</tr>";

												$Contents01 .= "<tr bgcolor=#ffffff height=22>
													<td class='leftmenu' style='padding:0 0 0 10px'><img src='../image/title_head.gif'> <b class=small>사용DB 용량 : </td>
													<td align=right style='padding:0 5px 0 0px'>".number_format(getMyDBinfo())."</td>
													</tr>";

	$Contents01 .= "
											</table>";

	$Contents01 .= "
										</td>
									</tr>
									</table>

								</td>
							</tr>
							<tr>
								<td>
								<table width=223 border=0>
									<tr height=25>
										<td style='border-bottom:2px solid #efefef'>
										<table cellpadding=0 cellspacing=0>
											<tr>
												<td width=13><img src='../images/dot_org.gif' align=absmiddle> </td>
												<td><b>지역별 회원분포</b></td>
											</tr>
										</table>
											 
										</td>";
	$script_time[pie_start] = time();
	$Contents01 .= "
									<tr height=30><td style='padding:0 0 0 0' class=small>".PieChart()."</td></tr><!--company_3DPie()-->";
	$script_time[pie_end] = time();
	$Contents01 .= "
								</table>
								</td>
							</tr>";

	$yesterday = mktime (0,0,0,date("m"), date("d")-1,   date("Y"));
	$lastmonth = mktime (0,0,0,date("m")-1, date("d"),   date("Y"));
if ($admininfo[mall_type] == "O"){
	$sql	=	"SELECT COUNT(*) AS total_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-d ')."%' and mem_type in ('M','C','F','S') ) AS today_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-d ', $yesterday)."%' and mem_type in ('M','C','F','S') ) AS yesterday_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-')."%' and mem_type in ('M','C','F','S') ) AS thismonth_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-', $lastmonth)."%' and mem_type in ('M','C','F','S') ) AS lastmonth_member "
			.	"FROM ".TBL_COMMON_USER ." cu where  cu.mem_type in ('M','C','F','S') ";
}else{
	$sql	=	"SELECT COUNT(*) AS total_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-d ')."%' and mem_type in ('M','C','F') ) AS today_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-d ', $yesterday)."%' and mem_type in ('M','C','F') ) AS yesterday_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-')."%' and mem_type in ('M','C','F') ) AS thismonth_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-', $lastmonth)."%' and mem_type in ('M','C','F') ) AS lastmonth_member "
			.	"FROM ".TBL_COMMON_USER ." cu where  cu.mem_type in ('M','C','F') ";
}
	$db->query($sql);
	$db->fetch();


	$Contents01 .= "		<tr>
								<td height=20 padding-top:0px;>
									<table width=223 border=0>
									<tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>회원가입현황</b></td>
									<tr>
										<td align='left' colspan=2 height=150 width='223px' valign=top style='padding-top:5px;'>
											<table cellpadding=3 cellspacing=1 width=100% bgcolor=#c0c0c0 style='border-collapse:separate; border-spacing:1px;'>
													<tr bgcolor=#ffffff><td class='leftmenu' style='padding:0 0 0 10' width=130><img src='../image/title_head.gif'> <b class=small>오늘가입한 회원수 :</b> </td><td align=right>".$db->dt[today_member]." 명 </td></tr>
													<tr bgcolor=#ffffff><td class='leftmenu' style='padding:0 0 0 10'><img src='../image/title_head.gif'> <b class=small>어제가입한 회원수 :</b></td><td align=right>".$db->dt[yesterday_member]." 명  </td></tr>
													<tr bgcolor=#ffffff><td class='leftmenu' style='padding:0 0 0 10'><img src='../image/title_head.gif'> <b class=small>이번달 가입한 회원수 </td><td align=right>".$db->dt[thismonth_member]." 명 </td></tr>
													<tr bgcolor=#ffffff><td class='leftmenu' style='padding:0 0 0 10'><img src='../image/title_head.gif'> <b class=small>지난달 가입한 회원수</td><td align=right>".$db->dt[lastmonth_member]." 명</td></tr>
													<tr bgcolor=#ffffff><td class='leftmenu' style='padding:0 0 0 10'><img src='../image/title_head.gif'> <b class=small>총회원수</td><td align=right>".$db->dt[total_member]." 명</td></tr>
											</table>
										</td>
									</tr>
									</table>

								</td>
							</tr>
						</table>
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
</table>";



$Contents = $Contents01;





$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
$P->Navigation = "HOME > 메인화면";
echo $P->PrintLayOut();

$script_time[end] = time();
if($admininfo[charger_id] == "forbiz"){
	//print_r($script_time);
}

function getSolutionType($solution_type){
	if($solution_type == "H"){
		return "홈빌더";//무료형
	}else if($solution_type == "F"){
		return "소호형";//무료형
	}else if($solution_type == "R"){
		return "임대형";
	}else if($solution_type == "S"){
		return "독립형";
	}else if($solution_type == "B"){
		return "비즈니스형";//입점형
	}else if($solution_type == "O"){
		return "오픈마켓형";
	}else{

	}
}

function PrintBoardRecentList(){
	global $db, $mdb, $admininfo;

	$sql = "select COUNT(*) from ".TBL_SHOP_BBS_USEAFTER."  ";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[0];

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=0 cellspacing=0 width='100%' bgcolor=silver>
		<tr align=center bgcolor=#efefef height=27 style='font-weight:bold'>
			<td width='30%' class='s_td'>제품</td>
			<td class='m_td'>내용</td>
			<td width='10%' class='m_td' nowrap>작성자</td>
			<td width='15%' class='m_td'>등록일</td>
			<td width='10%' class='e_td'>관리</td>
		</tr>";
	$mString = $mString."<tr height=2 bgcolor=#ffffff><td colspan=5 ></td></tr>";
	//$mString = $mString."<tr height=1><td colspan=5 class=dot-x></td></tr>";
	//$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class=s_td width='25%'>제품</td><td class=m_td width='40%'>내용</td><td class=m_td width='10%' nowrap>작성자</td><td class=m_td width='15%'>등록일</td><td class=e_td width='10%'>관리</td></tr>";
	


	//<tr height=50 bgcolor=#ffffff><td colspan=6 align=left>".page_bar($total, $page, $max,  "&max=$max")."</td></tr>
	$mString .= "</table>";

	return $mString;
}



function PrintRecentProductList($stock_status=""){
	global $db, $mdb, $admin_config, $admininfo, $DOCUMENT_ROOT, $currency_display;

	$where = array();
	if($stock_status == "soldout"){
		$where[] = "(option_stock_yn = 'Y' or stock = 0)";
	}else if($stock_status == "shortage"){
		$where[] = "(option_stock_yn = 'S' or (stock < safestock && stock != 0 ))";
	}
	$where = (count($where) > 0)	?	' WHERE '.implode(' AND ', $where):'';


	if($admininfo[admin_level] == 9){
		$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1 $where order by mp1.regdate desc limit 5  ";
	}else{
		$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1 $where and mp1.admin ='".$admininfo[company_id]."'  order by mp1.regdate desc limit 5  ";
	}
	//echo $sql;
	$mdb->query($sql);


	$mString = "<table cellpadding=4 cellspacing=0 border=0 width='100%' bgcolor=silver>";

	if ($mdb->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=2 align=center>등록된 상품이 없습니다.</td></tr>";
	}else{

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);


			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[id], "s"))){
				$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[id], "s");
			}else{
				$img_str = "../image/no_img.gif";
			}

			$mString = $mString."<tr height=45 bgcolor=#ffffff align=center>
			<td bgcolor='#ffffff' width=50><a href='../product/goods_input.php?id=".$mdb->dt[id]."'><img src='".$img_str."' width=50 height=50 border=0 style='border:1px solid #c0c0c0'></a></td>
			<td align=left style='padding:4 4 4 10'>
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td>".($mdb->dt[brand_name] ? "[".$mdb->dt[brand_name]."]":"")."</td>
					<tr>
						<td><a href='../product/goods_input.php?id=".$mdb->dt[id]."'>".cut_str($mdb->dt[pname],20)."</a></td>
					</tr>
					<tr>
						<td >".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($mdb->dt[sellprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					</tr>
				</table>
			</td>
			</tr>
			<tr height=1><td colspan=6 class=dot-x></td></tr>
			";
		}

	}


	$mString = $mString."</table>";

	return $mString;
}
?>

<?
if($act == "old"){
$script_time[start] = time();
include("../class/layout.class");
include("./member_region.chart.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
$script_time[start] = time();
//print_r($_SESSION);

$db = new Database;
$mdb = new Database;
$sms_design = new SMS;
//print_r($admininfo);
//print_r($admin_config);

$year = date("Y"); 
$month = date("m"); 
$day = date("d");  

$w = date('w',mktime(0,0,0,$month,$day,$year)); 
// strtotime(sprintf('%4d-%02d-%02d',$year,$month,$day)) 

$week_first_day =  date('Ymd',mktime(0,0,0,$month,$m=$day-($w+6)%7,$year))."\n"; // 2010-04-10 
$week_last_day =  date('Y-m-d',mktime(0,0,0,$month,$m+6,$year))."\n"; // 2010-04-10 
/* 
echo date('Y-m-d',mktime(0,0,0,$month,$day-($w+6)%7,$year))."\n"; 
echo date('Y-m-d',mktime(0,0,0,$month,$day+(7-$w)%7,$year))."\n"; 
*/ 


$Script = "


<Script language='javascript'>
function showTabContents(vid, tab_id){
	var area = new Array('recent_order','recent_contents','recent_use_after');
	var tab = new Array('tab_01','tab_02','tab_03');

	for(var i=0; i<area.length; ++i){
		if(area[i]==vid){
			document.getElementById(vid).style.display = 'block';
			document.getElementById(tab_id).className = 'on';
		}else{
			document.getElementById(area[i]).style.display = 'none';
			document.getElementById(tab[i]).className = '';
		}
	}
}

function useAfterDelete(uf_ix){
	if(confirm('사용후기를 정말로 삭제하시겠습니까? ')){
		document.frames['act'].location.href='../marketting/useafter.act.php?act=delete&uf_ix='+uf_ix
	}
}
function checkAllkrDomain(obj){
	var frm = document.domain_search;
	for(var i=0;i<frm.kdomain.length;i++){
		if(obj.checked){
			frm.kdomain[i].checked = true;
		}else{
			frm.kdomain[i].checked = false;
		}
	}
}

function checkAllcomDomain(obj){
	var frm = document.domain_search;
	for(var i=0;i<frm.edomain.length;i++){
		if(obj.checked){
			frm.edomain[i].checked = true;
		}else{
			frm.edomain[i].checked = false;
		}
	}
}
</Script>";
//$script_time[sms_start] = time();
$sms_cnt = $sms_design->getSMSAbleCount($admininfo);
//$script_time[sms_end] = time();
$Contents01 = "
<table cellpadding=3 cellspacing=0 border='0' align='left'>
	<tr>
		<td width=80% valign=top>
		<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
		  <tr>
			<td valign=top height=100>
			<div class='tab' style='margin: 0px 0px ;'>
				<table class='s_org_tab'>
				<tr>
					<td class='tab'>
						<!--table id='tab_01' class='on' >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"showTabContents('recent_order','tab_01')\" style='padding-left:20px;padding-right:20px;'>신규주문리스트</td>
							<th class='box_03'></th>
						</tr>
						</table-->
						<table id='tab_02' 	class='on' ".($admininfo[admin_level] == 9 ? "style='display:inline'":"style='display:none'").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"showTabContents('recent_contents','tab_02')\" style='padding-left:20px;padding-right:20px;'>최근게시물현황</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<!--table id='tab_03' ".($admininfo[admin_level] == 9 ? "style='display:inline'":"style='display:none'").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"showTabContents('recent_use_after','tab_03')\" style='padding-left:20px;padding-right:20px;' nowrap>최근사용후기</td>
							<th class='box_03'></th>
						</tr>
						</table-->
					</td>
					<td class='btn'>

					</td>
				</tr>
				</table>
			</div>
			<div class='t_no' style='margin: 2px 0px ; border-top: solid 3px #c6c6c6; '>
				<!-- my_movie start -->
				<div class='my_box' >
					<div id='recent_order' style='padding:5px 5px 5px 0px;display:none;'>";
	$script_time[order_summary_start] = time();
	$Contents01 .= "
						".PrintOrderSummary()."";
	$script_time[order_summary_end] = time();
	$Contents01 .= "
					</div>
					<div id='recent_contents' style='padding:5px 5px 5px 0px;height:200px;'>";
	if($admininfo[admin_level] == 9 ){
	$script_time[board_summary_start] = time();
	$Contents01 .= "
						 ".PrintBoardSummary()."";
	$script_time[board_summary_end] = time();
	}
	$Contents01 .= "
					</div>
					<div id='recent_use_after' style='padding:5px 5px 5px 0px;display:none;'>";
	if($admininfo[admin_level] == 9 ){
	$script_time[board_recent_start] = time();
	$Contents01 .= "
						".PrintBoardRecentList()."";
	$script_time[board_recent_end] = time();
	}
	$Contents01 .= "
					</div>
				</div>
			</div>
			</td>
			
		  </tr>
		  <tr height=20><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>주간 방문횟수</b></td></tr>
		  <tr>
			<td style='padding:15px 0px 0 0' align=center>
			<img src='../logstory/report/visit.chart.php?vdate=".$week_first_day."&SelectReport=2' >
			</td>
		  </tr>
		  <tr>
			<td style='padding:15px 0px 0 0' align=left>
				<table width=100% border=0>
					<col width=50%>
					<col width=50%>
					<tr>
						<td>
						<table width=100% border=0 >
							<col width=100%>
							<tr height=25><td align=left><img src='../images/dot_org.gif' align=absmiddle> <b>공지사항</b></td></tr>
							<tr>
								<td valign=top height=130 style='border:1px solid #c0c0c0;padding:5px;'>
									<table width=100% border=0 >
										
										";
				$bbs_datas = fetch_bbs("bbs_notice", 5);
				for($i=0;$i < count($bbs_datas);$i++){
				$Contents01 .= "<tr>
									<td height=22>
										<img src='../images/no_".($i+1).".gif' align=absmiddle> 
										<a href='/admin/bbsmanage/bbs.php?mode=read&board=notice&bbs_ix=".$bbs_datas[$i][bbs_ix]."&page=1'> ".cut_str($bbs_datas[$i][bbs_subject],25)."</a>
									</td>
									<td align=right>".$bbs_datas[$i][regdate]."</td>
								</tr>";
				}

				$Contents01 .= "
										</table>
								</td>
							</tr>
						</table>
						</td>
						<td>
							<table width=100% border=0>
							<col width=100%>
							<tr height=25><td align=left><img src='../images/dot_org.gif' align=absmiddle> <b>1:1 문의</b></td><tr>
							<tr>
								<td valign=top style='border:1px solid #c0c0c0;padding:5px;'>
									<table width=100% border=0 >
										
										";
				$bbs_datas = fetch_bbs("bbs_qna", 5);
				for($i=0;$i < count($bbs_datas);$i++){
				$Contents01 .= "<tr>
									<td height=22><img src='../images/ico_answer.gif' > <a href='/admin/bbsmanage/bbs.php?mode=read&board=qna&bbs_ix=".$bbs_datas[$i][bbs_ix]."&page=1'>".cut_str($bbs_datas[$i][bbs_subject],25)."</a></td>
									<td align=right>".$bbs_datas[$i][regdate]."</td>
								</tr>";
				}
				

				$Contents01 .= "
										</table>
								</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>

			</td>
		  </tr>";



$Contents01 .= "
			<!--tr height=25><td style='padding:20 0 0 0;border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>도메인검색/등록</b></td></tr>
			<tr height=100>
			<td style='padding:8 0 0 0;'>
				<form method='post' name='domain_search' action='http://www.mallstory.com/addition/domain_search.php' target=_blank>
							<table cellpadding=2 cellspacing=0 border=0 style='width:100%'>
									<tr height=20 >
										<td colspan=4 style='padding:0 0 0 0' >
											<table class='box_06' width=100%>
												<tr>
													<th class='box_01'></th>
													<td class='box_02'></td>
													<th class='box_03'></th>
												</tr>
												<tr>
													<th class='box_04'></th>
													<td class='box_05' align=center>
														<table cellpadding=2 cellspacing=0 border=0 style='width:450px'>
															<tr><td colspan=4 align=left style='padding-left:85px;'><span>  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span> </td></tr>
															<tr >
																<td width=70 align=right nowrap style='font-size:12px;'></td>
																<td width=60 align=right nowrap>
																	<b style='font-size:15pt;'><b>WWW.</b></b>
																</td>
																<td >
																	<input type='text' id='fsearch' name='search_word' maxlength='50' class='input_board' style='background-color:#8b8b8b;width:320px;height:22px;color:white;border:1px solid #575757;font-size:14pt;text-align:center;font-weight:bold;' align='middle'>
																</td>
																<td style='padding:0 0 0 10'><input type=image src='../images/".$admininfo["language"]."/btc_search.gif' align='absmiddle' border='0' style='border:0px;' onFocus='this.blur();'></td>
															</tr>
														</table>
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
									<tr height=20 >
										<td colspan=4 style='padding:10 0 0 20' align=center >
											<table >
												<tr>
													<td style='padding:0 20 0 0' nowrap><input type=checkbox name='kr_all' id='kr_all' value='kr_all'  style='border:0px;' onclick='checkAllkrDomain(this);'><label for='kr_all'><b>KR 도메인</b></label></td>
													<td><input type=checkbox name='kdomain[]' id='kdomain' value='kr' conn_div='KR' style='border:0px;' checked><label for='kr'>.kr</label></td>
													<td><input type=checkbox name='kdomain[]' id='kdomain' value='co.kr' conn_div='KR' style='border:0px;' checked><label for='co.kr'>.co.kr</label></td>
													<td><input type=checkbox name='kdomain[]' id='kdomain' value='or.kr' conn_div='KR' style='border:0px;'><label for='or.kr'>.or.kr</label></td>
													<td><input type=checkbox name='kdomain[]' id='kdomain' value='ne.kr' conn_div='KR' style='border:0px;'><label for='ne.kr'>.ne.kr</label></td>
													<td><input type=checkbox name='kdomain[]' id='kdomain' value='pe.kr' conn_div='KR' style='border:0px;'><label for='pe.kr'>.pe.kr</label></td>
												</tr>
												<tr>
													<td style='padding:0 20 0 0' nowrap><input type=checkbox name='com_all' id='com_all' value='com_all'  style='border:0px;' onclick='checkAllcomDomain(this);'><label for='com_all'><b>국제 도메인</b></label></td>
													<td><input type=checkbox name='edomain[]' id='edomain' value='com' conn_div='COM' style='border:0px;' checked><label for='com'>.com</label></td>
													<td><input type=checkbox name='edomain[]' id='edomain' value='net' conn_div='COM' style='border:0px;' checked><label for='net'>.net</label></td>
													<td><input type=checkbox name='edomain[]' id='edomain' value='org' conn_div='COM' style='border:0px;'><label for='org'>.org</label></td>
													<td><input type=checkbox name='edomain[]' id='edomain' value='info' conn_div='COM' style='border:0px;'><label for='info'>.info</label></td>
													<td><input type=checkbox name='edomain[]' id='edomain' value='biz' conn_div='COM' style='border:0px;'><label for='biz'>.biz</label></td>
												</tr>
											</table>
										</td>
									</tr>
							  </table>

						</form>
			</td>
		  </tr>
			<tr height=50><td colspan=5></td></tr-->
		  </table>
		</td>
		<td valign=top ".($admininfo[admin_level] == 9 ? "style='display:inline'":"style='display:none'").">
			<table class='box_12' style='width:241px;height:70px' cellpadding=0 cellspacing=0>
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05' style='padding:0 0 0 0'>
						<table border=0>
							<tr>
								<td height=20 padding-top:0px;>
									<table width=223 border=0>
									<tr height=25>
										<td style='border-bottom:2px solid #efefef'>
											<table cellpadding=0 cellspacing=0>
												<tr>
													<td width=13><img src='../images/dot_org.gif' align=absmiddle> </td>
													<td><b>사이트 자원 현황</b></td>
												</tr>
											</table>
										
										</td>
									</tr>
									<tr>
										<td align='left' colspan=2 height=100 width='223px' valign=top style='padding-top:5px;'>";

	$Contents01 .= "
											<table cellpadding=0 cellspacing=1 width=100% bgcolor=#c0c0c0 border=0 style='border-collapse:separate; border-spacing:1px;'>
													<tr bgcolor=#ffffff height=22>
														<td class='leftmenu' style='padding:0 0 0 10px'><img src='../image/title_head.gif'> <b class=small>솔루션 타입 :</b> </td>
														<td align=right style='padding:0 5px 0 0px' >".getSolutionType($admininfo[mall_type])."  </td>
													</tr>";
	/*
	$script_time[hard_start] = time();
	$Contents01 .= "										    	<tr bgcolor=#ffffff><td class='leftmenu' style='padding:0 0 0 10'><img src='../image/title_head.gif'> <b class=small>사용하드용량 :</b> </td><td align=right>".@dirsize($_SERVER["DOCUMENT_ROOT"]."")." </td></tr>";
	$script_time[hard_end] = time();
	*/
	$script_time[hardimage_start] = time();

	//$Contents01 .= "										    	<tr bgcolor=#ffffff><td class='leftmenu' style='padding:0 0 0 10'><img src='../image/title_head.gif'> <b class=small>사용이미지용량 :</b></td><td align=right> </td></tr>	    	";
												$script_time[hardimage_end] = time();
												$Contents01 .= "<tr bgcolor=#ffffff height=22>
													<td class='leftmenu' style='padding:0 0 0 10px'><img src='../image/title_head.gif'> <b class=small>SMS 잔여 용량 : </td>
													<td align=right style='padding:0 5px 0 0px'><a href='../store/sms.point.php'>".$sms_cnt." 건 </a></td>
													</tr>	";


												$Contents01 .= "<tr bgcolor=#ffffff height=22>
													<td class='leftmenu' style='padding:0 0 0 10px'><img src='../image/title_head.gif'> <b class=small>총 상품갯수 : </td>
													<td align=right style='padding:0 5px 0 0px'>".number_format(regProductCnt())." 개</td>
													</tr>";

												$Contents01 .= "<tr bgcolor=#ffffff height=22>
													<td class='leftmenu' style='padding:0 0 0 10px'><img src='../image/title_head.gif'> <b class=small>사용DB 용량 : </td>
													<td align=right style='padding:0 5px 0 0px'>".number_format(getMyDBinfo())."</td>
													</tr>";

	$Contents01 .= "
											</table>";

	$Contents01 .= "
										</td>
									</tr>
									</table>

								</td>
							</tr>
							<tr>
								<td>
								<table width=223 border=0>
									<tr height=25>
										<td style='border-bottom:2px solid #efefef'>
										<table cellpadding=0 cellspacing=0>
											<tr>
												<td width=13><img src='../images/dot_org.gif' align=absmiddle> </td>
												<td><b>지역별 회원분포</b></td>
											</tr>
										</table>
											 
										</td>";
	$script_time[pie_start] = time();
	$Contents01 .= "
									<tr height=30><td style='padding:0 0 0 0' class=small>".PieChart()."</td></tr><!--company_3DPie()-->";
	$script_time[pie_end] = time();
	$Contents01 .= "
								</table>
								</td>
							</tr>";

	$yesterday = mktime (0,0,0,date("m"), date("d")-1,   date("Y"));
	$lastmonth = mktime (0,0,0,date("m")-1, date("d"),   date("Y"));
if ($admininfo[mall_type] == "O"){
	$sql	=	"SELECT COUNT(*) AS total_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-d ')."%' and mem_type in ('M','C','F','S') ) AS today_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-d ', $yesterday)."%' and mem_type in ('M','C','F','S') ) AS yesterday_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-')."%' and mem_type in ('M','C','F','S') ) AS thismonth_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-', $lastmonth)."%' and mem_type in ('M','C','F','S') ) AS lastmonth_member "
			.	"FROM ".TBL_COMMON_USER ." cu where  cu.mem_type in ('M','C','F','S') ";
}else{
	$sql	=	"SELECT COUNT(*) AS total_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-d ')."%' and mem_type in ('M','C','F') ) AS today_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-d ', $yesterday)."%' and mem_type in ('M','C','F') ) AS yesterday_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-')."%' and mem_type in ('M','C','F') ) AS thismonth_member, "
			.	"(SELECT COUNT(*) FROM ".TBL_COMMON_USER." WHERE date LIKE '".date('Y-m-', $lastmonth)."%' and mem_type in ('M','C','F') ) AS lastmonth_member "
			.	"FROM ".TBL_COMMON_USER ." cu where  cu.mem_type in ('M','C','F') ";
}
	$db->query($sql);
	$db->fetch();


	$Contents01 .= "		<tr>
								<td height=20 padding-top:0px;>
									<table width=223 border=0>
									<tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>회원가입현황</b></td>
									<tr>
										<td align='left' colspan=2 height=150 width='223px' valign=top style='padding-top:5px;'>
											<table cellpadding=3 cellspacing=1 width=100% bgcolor=#c0c0c0 style='border-collapse:separate; border-spacing:1px;'>
													<tr bgcolor=#ffffff><td class='leftmenu' style='padding:0 0 0 10' width=130><img src='../image/title_head.gif'> <b class=small>오늘가입한 회원수 :</b> </td><td align=right>".$db->dt[today_member]." 명 </td></tr>
													<tr bgcolor=#ffffff><td class='leftmenu' style='padding:0 0 0 10'><img src='../image/title_head.gif'> <b class=small>어제가입한 회원수 :</b></td><td align=right>".$db->dt[yesterday_member]." 명  </td></tr>
													<tr bgcolor=#ffffff><td class='leftmenu' style='padding:0 0 0 10'><img src='../image/title_head.gif'> <b class=small>이번달 가입한 회원수 </td><td align=right>".$db->dt[thismonth_member]." 명 </td></tr>
													<tr bgcolor=#ffffff><td class='leftmenu' style='padding:0 0 0 10'><img src='../image/title_head.gif'> <b class=small>지난달 가입한 회원수</td><td align=right>".$db->dt[lastmonth_member]." 명</td></tr>
													<tr bgcolor=#ffffff><td class='leftmenu' style='padding:0 0 0 10'><img src='../image/title_head.gif'> <b class=small>총회원수</td><td align=right>".$db->dt[total_member]." 명</td></tr>
											</table>
										</td>
									</tr>
									</table>

								</td>
							</tr>
						</table>
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
</table>";



$Contents = $Contents01;





$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
$P->Navigation = "HOME > 메인화면";
echo $P->PrintLayOut();

$script_time[end] = time();
if($admininfo[charger_id] == "forbiz"){
	//print_r($script_time);
}

function PrintOrderHistory(){
	global $admininfo, $currency_display, $admin_config ;
	$odb = new Database;

	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));



	if($admininfo[admin_level] == 9){
		$sql = "
					select '기간      ','매출액(".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"]."1) ', '주문건수','입금예정', '입금확인','배송준비/배송중', '교환 ','주문취소'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." where date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."' AND '".date("Y-m-d 23:59:59",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." where date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."' AND '".date("Y-m-d 23:59:59",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."'
					union
					Select '".date("m/d")." 오늘 ',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." where date between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
					union
					Select '최근1주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." where date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))."' and '".date("Y-m-d 00:00:00")."'
				 	union
					Select '금주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." where date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($firstday,4,2),substr($firstday,6,2),substr($firstday,0,4)))."' and '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($lastday,4,2),substr($lastday,6,2),substr($lastday,0,4)))."'
				 	union
					Select '최근1개월',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." where date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."'
				 	union
					Select '전체',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." ";
		//echo $sql;
		//exit;
		/*
		$sql = "Select
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then total_price else '0' end) as today_total_price,
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m%d') between '".$firstday."' and $lastday then total_price else '0' end) as thisweek_total_price,
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else '0' end) as thismonth_total_price,
					sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else '0' end) as thismonth_cancel_total_price,
					sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end) as ready_cnt,
					sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end) as order_end_cnt,
					sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end) as thismonth_return_total_cnt
				 	from ".TBL_SHOP_ORDER."  ";
				 	*/
	//echo $sql;
	}else if($admininfo[admin_level] == 8){
		$sql = "
					select '기간      ','매출액(".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"].") ', '주문건수','입금예정', '입금확인','배송준비/배송중', '교환 ','주문취소'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."' AND '".date("Y-m-d 23:59:59",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."' AND '".date("Y-m-d 23:59:59",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."'
					union
					Select '".date("m/d")." 오늘 ',
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and date between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
					union
					Select '최근1주',
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."'  and date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."'
				 	union
					Select '금주',
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($firstday,4,2),substr($firstday,6,2),substr($firstday,0,4)))."' and '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($lastday,4,2),substr($lastday,6,2),substr($lastday,0,4)))."'
				 	union
					Select '최근1개월',
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."'
				 	union
					Select '전체',
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' ";
	}




	$odb->query($sql);
	$datas = $odb->getrows();
	$mstring = "
	<table border='0' cellspacing='1' cellpadding='5' width='100%'>
      <tr>
        <td bgcolor='#F8F9FA'>
				<table cellpadding=0 cellspacing=1 width=100% bgcolor=#c0c0c0>
				<col width=20%>
				<col width=10%>
				<col width=10%>
				<col width=10%>
				<col width=10%>
				<col width=10%>
				<col width=15%>
				<col width=15%>
		";
	for($i=0;$i<count($datas)-1;$i++){
		//print_r($datas);
			if($i == 0){
				$mstring .= "<tr bgcolor=#ffffff align=center height='30'>
					<td bgcolor='#efefef' >".$datas[$i][0]."</td>
					<td bgcolor='#efefef'>".$datas[$i][1]."</td>
					<td bgcolor='#efefef'>".$datas[$i][2]."</td>
					<td bgcolor='#efefef' nowrap><b>".$datas[$i][3]."</b></td>
					<td bgcolor='#efefef'>".$datas[$i][4]."</td>
					<td bgcolor='#efefef'>".$datas[$i][5]."</td>
					<td bgcolor='#efefef'>".$datas[$i][6]."</td>
					<td bgcolor='#efefef'>".$datas[$i][7]."</td>
					</tr>";
			}else{
				$mstring .= "<tr bgcolor=#ffffff height=24 align=right>
					<td align=left style='padding:4px 0 0 10px;' class='small' bgcolor='#efefef'  nowrap><img src='../image/title_head.gif'> ".$datas[$i][0]."</td>
					<td style='padding:0px 3px; 0px 0px;'>".number_format($datas[$i][1])."</td>
					<td style='padding:0px 3px; 0px 0px;'>".number_format($datas[$i][2])." </td>
					<td style='padding:0px 3px; 0px 0px;'><b>".number_format($datas[$i][3])."</b></td>
					<td style='padding:0px 3px; 0px 0px;' >".number_format($datas[$i][4])."</td>
					<td style='padding:0px 3px; 0px 0px;'>".number_format($datas[$i][5])."</td>
					<td style='padding:0px 3px; 0px 0px;'>".number_format($datas[$i][6])."</td>
					<td style='padding:0px 3px; 0px 0px;'>".number_format($datas[$i][7])."</td>
					</tr>";
			}
	}
	$mstring .= "</table>";
	$mstring .= "</td></tr></table>";

	return $mstring;


}

function getSolutionType($solution_type){
	if($solution_type == "H"){
		return "홈빌더";//무료형
	}else if($solution_type == "F"){
		return "소호형";//무료형
	}else if($solution_type == "R"){
		return "임대형";
	}else if($solution_type == "S"){
		return "독립형";
	}else if($solution_type == "B"){
		return "비즈니스형";//입점형
	}else if($solution_type == "O"){
		return "오픈마켓형";
	}else{

	}
}

function PrintBoardRecentList(){
	global $db, $mdb, $admininfo;

	$sql = "select COUNT(*) from ".TBL_SHOP_BBS_USEAFTER."  ";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[0];

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=0 cellspacing=0 width='100%' bgcolor=silver>
		<tr align=center bgcolor=#efefef height=27 style='font-weight:bold'>
			<td width='30%' class='s_td'>제품</td>
			<td class='m_td'>내용</td>
			<td width='10%' class='m_td' nowrap>작성자</td>
			<td width='15%' class='m_td'>등록일</td>
			<td width='10%' class='e_td'>관리</td>
		</tr>";
	$mString = $mString."<tr height=2 bgcolor=#ffffff><td colspan=5 ></td></tr>";
	//$mString = $mString."<tr height=1><td colspan=5 class=dot-x></td></tr>";
	//$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class=s_td width='25%'>제품</td><td class=m_td width='40%'>내용</td><td class=m_td width='10%' nowrap>작성자</td><td class=m_td width='15%'>등록일</td><td class=e_td width='10%'>관리</td></tr>";
	if ($total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=5 align=center>사용후기 내역이 존재 하지 않습니다.</td></tr>";
	}else{

		$db->query("select p.pname, u.* from ".TBL_SHOP_BBS_USEAFTER." u , ".TBL_SHOP_PRODUCT." p where u.pid = p.id  order by  regdate desc limit 6");

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

			//$no = $no + 1;

			$mString .= "<tr height=25 bgcolor=#ffffff align=center>
			<td bgcolor='#efefef' align='left' style='padding:4px 20px;'>".$db->dt[pname]."]</td>
			<td align=left style='padding-left:20px;'>".cut_str(strip_tags($db->dt[uf_contents]),30)."</td>
			<td bgcolor='#efefef'>".$db->dt[uf_name]."</td>
			<td>".str_replace("-",".",substr($db->dt[regdate],0,10))."</td>
			<td bgcolor='#efefef' align=center>
				<a href=JavaScript:useAfterDelete('".$db->dt[uf_ix]."')><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
			</td>
			</tr>
			<tr height=1><td colspan=5 class=dot-x></td></tr>
			";
		}

	}

	//<tr height=50 bgcolor=#ffffff><td colspan=6 align=left>".page_bar($total, $page, $max,  "&max=$max")."</td></tr>
	$mString .= "</table>";

	return $mString;
}



function PrintRecentProductList($stock_status=""){
	global $db, $mdb, $admin_config, $admininfo, $DOCUMENT_ROOT, $currency_display;

	$where = array();
	if($stock_status == "soldout"){
		$where[] = "(option_stock_yn = 'Y' or stock = 0)";
	}else if($stock_status == "shortage"){
		$where[] = "(option_stock_yn = 'S' or (stock < safestock && stock != 0 ))";
	}
	$where = (count($where) > 0)	?	' WHERE '.implode(' AND ', $where):'';


	if($admininfo[admin_level] == 9){
		$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1 $where order by mp1.regdate desc limit 5  ";
	}else{
		$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1 $where and mp1.admin ='".$admininfo[company_id]."'  order by mp1.regdate desc limit 5  ";
	}
	//echo $sql;
	$mdb->query($sql);


	$mString = "<table cellpadding=4 cellspacing=0 border=0 width='100%' bgcolor=silver>";

	if ($mdb->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=2 align=center>등록된 상품이 없습니다.</td></tr>";
	}else{

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);


			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[id], "s"))){
				$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[id], "s");
			}else{
				$img_str = "../image/no_img.gif";
			}

			$mString = $mString."<tr height=45 bgcolor=#ffffff align=center>
			<td bgcolor='#ffffff' width=50><a href='../product/goods_input.php?id=".$mdb->dt[id]."'><img src='".$img_str."' width=50 height=50 border=0 style='border:1px solid #c0c0c0'></a></td>
			<td align=left style='padding:4 4 4 10'>
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td>".($mdb->dt[brand_name] ? "[".$mdb->dt[brand_name]."]":"")."</td>
					<tr>
						<td><a href='../product/goods_input.php?id=".$mdb->dt[id]."'>".cut_str($mdb->dt[pname],20)."</a></td>
					</tr>
					<tr>
						<td >".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($mdb->dt[sellprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					</tr>
				</table>
			</td>
			</tr>
			<tr height=1><td colspan=6 class=dot-x></td></tr>
			";
		}

	}


	$mString = $mString."</table>";

	return $mString;
}
}
?>
