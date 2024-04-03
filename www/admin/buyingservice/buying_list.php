<?
include("../class/layout.class");
include("buying.lib.php");

if($max == ""){
	$max = 10; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;


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

$mstring = "
<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
<input type='hidden' name='mode' value='search'>
<input type='hidden' name='cid2' value='$cid2'>
<input type='hidden' name='depth' value='$depth'>
<input type='hidden' name='sprice' value='0' />
<input type='hidden' name='eprice' value='1000000' />
<table width='100%' cellpadding=0 cellspacing=0>
	<tr>
	    <td align='left' colspan=8 > ".GetTitleNavigation("사입신청리스트", "사입관리 > 사입목록")."</td>
	</tr>
	
			<tr height=150>
				<td colspan=2>
					<table width='100%' cellpadding=0 cellspacing=0>
						<tr>
							<td align='left' colspan=8 style='padding-bottom:14px;'>
							<div class='tab'>
									<table class='s_org_tab'>
									<tr>
										<td class='tab'>
											<table id='tab_00'  ".($buying_status == "" ? "class='on'":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?buying_status='\">전체</td>
												<th class='box_03'></th>
											</tr>
											</table>
											<table id='tab_01'  ".($buying_status == "IR" ? "class='on'":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?buying_status=IR'\">입금예정</td>
												<th class='box_03'></th>
											</tr>
											</table>
											<table id='tab_01'  ".($buying_status == "IC" ? "class='on'":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?buying_status=IC'\">입금확인</td>
												<th class='box_03'></th>
											</tr>
											</table>

										</td>
										<td align='right' style='text-align:right;vertical-align:bottom;padding:0 0 6px 4px;'>";
										
					$mstring .= "
										</td>
									</tr>
									</table>
								</div>
							</td>
						</tr>
					</table>					
					<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='input_table_box'>
						<col width='150' >
						<col width='*' >
						<col width='150' >
						<col width='*' >
						<tr>
							<td class='input_box_title'>  <b>처리상태</b>  </td>
							<td class='input_box_item' colspan=3>";
								$mstring .= "<input type='radio' name='buying_status'  id='buying_status_all' value='' ".($_GET["bs_status"] == "" ? " checked":"")."><label for='buying_status_all' style='margin-right:10px;'>전체</label>";
							foreach($_buyingservice_status as $key => $value){
								$mstring .= "<input type='radio' name='buying_status'  id='buying_status_".$key."' value='".$key."' ".ReturnStringAfterCompare($key, $_GET["buying_status"], " checked")."><label for='buying_status_".$key."' style='margin-right:10px;'>". $value."</label>";
							}

					$mstring .= "
							</td>
						</tr>
						
						";
						
						$mstring .=	"
						<tr>
							<td class='input_box_title'>  <b>검색어</b>  </td>
							<td class='input_box_item' valign='top' style='padding-right:5px;padding-top:7px;'>
								<table cellpadding=0 cellspacing=0>
									<tr>
										<td><select name='search_type'  style=\"font-size:12px;height:22px;\">
													<option value='buying_mem_name'>이름</option>
													<!--option value='pcode'>아이디</option>
													<option value='id'>상품코드(key)</option-->
													</select>
													</td>
										<td style='padding-left:5px;'><INPUT id=search_texts  class='textbox' value='' onclick='findNames();'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
										<DIV id=popup style='DISPLAY: none; WIDTH: 160px; POSITION: absolute; HEIGHT: 150px; BACKGROUND-COLOR: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
											<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100% bgColor=#efefef>
												<tr height=20>
													<td width=100%  style='padding:0 0 0 5'>
														<table width=100% cellpadding=0 cellspacing=0 border=0>
															<tr>
																<td class='p11 ls1'>검색어 자동완성</td>
																<td class='p11 ls1' onclick='focusOutBool=false;clearNames()' style='cursor:hand;padding:0 10 0 0' align=right>닫기</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr height=100% >
													<td valign=top bgColor=#efefef style='padding:0 6 5 6' colspan=2>
														<table width=100% height=100% bgcolor=#ffffff>
															<tr>
																<td valign=top >
																<div style='POSITION: absolute; overflow-y:auto;HEIGHT: 120px;' id='search_data_area'>
																	<TABLE id=search_table style='table-layout:fixed;'  width=100% cellSpacing=0 cellPadding=1 bgColor=#ffffff border=0>
																	<TBODY id=search_table_body></TBODY>
																	</TABLE>
																<div>
																</td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
											</DIV>
										</td>
										<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
									</tr>
								</table>
							</td>
							<td class='input_box_title'><b>목록갯수</b></td>
							<td class='input_box_item'><select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle>
							<option value='5' ".CompareReturnValue(5,$max).">5</option>
							<option value='10' ".CompareReturnValue(10,$max).">10</option>
							<option value='20' ".CompareReturnValue(20,$max).">20</option>
							<option value='50' ".CompareReturnValue(50,$max).">50</option>
							<option value='100' ".CompareReturnValue(100,$max).">100</option>
							</select> <span class='small'>한페이지에 보여질 갯수를 선택해주세요</span>
							</td>
						</tr>
						<tr height=27>
							  <td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>등록일자</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);'".CompareReturnValue("1",$regdate,"checked")."></td>
							  <td class='search_box_item' align=left style='padding-left:5px;' colspan=3>
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
							
				</td>
			</tr>
			<tr >
				<td colspan=8 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>				
			</tr>
		</table>
		</form>

<table width='100%' cellpadding=0 cellspacing=0 border=0 class='list_table_box'>
	<col width='7%'>
	<col width=7%>
	<col width=7%>
	<col width=7%>
	<col width=7%>
	<col width=7%>
	<col width=7%>
	<col width=10%>
	<col width=15%>
	<col width=7%>
	<col width=10%>
	<col width=*>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td' rowspan='2'>번호</td>
		<td class='m_td' rowspan='2'>사입신청일(시간)</td>
		<td class='m_td' rowspan='2'>사업자명</td>
		<td class='m_td' rowspan='2'>이름(ID)</td>
		<td class='m_td' colspan='3'>사입가능 보유수량</td>
		<td class='m_td' rowspan='2'>상품수</td>
		<td class='m_td' rowspan='2'>반품/교환/샘플</td>
		<td class='m_td' rowspan='2'>대납요청금액</td>
		<td class='m_td' rowspan='2'>처리상태</td>
		<td class='m_td' rowspan='2'>관리</td>
	</tr>
	<tr bgcolor=#efefef align=center height=27>
		<td class='m_td'>신청전</td>
		<td class='m_td'>매장수</td>
		<td class='m_td'>사용후</td>
	</tr>
		
		";

$where = " where ai.bai_ix = aid.bai_ix  and ai.mem_ix = cmd.code and cmd.code=cu.code  ";
if($buying_status != ""){
	$where .= " and ai.buying_status = '".$buying_status."'  ";
}

if($sdate && $edate){
	$where .= " and DATE_FORMAT(ai.regdate,'%Y%m%d') between ".$sdate." and ".$edate." ";
}

if($search_text && $search_type){
	$where .= " and $search_type LIKE '%".$search_text."%' ";
}

$sql = "SELECT count(*) as total 
			FROM buyingservice_apply_info ai , buyingservice_apply_info_detail aid , 
			".TBL_COMMON_MEMBER_DETAIL." cmd,
			common_user cu 
			left join common_company_detail ccd on cu.company_id=ccd.company_id
			$where ";

$db->query($sql);
$db->fetch();
$total = $db->dt[total];


$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&buying_status=$buying_status&sdate=$sdate&edate=$edate","");


if($total){
	
	$sql = "SELECT ai.bai_ix, cu.id, apply_date, ai.buying_status, ccd.com_name, buying_mem_name, sum(amount) as amount, mg.gp_name, sum(buying_complete_cnt) as buying_complete_cnt , 
				sum(pre_payment_price) as pre_payment_price
				
				FROM buyingservice_apply_info ai , buyingservice_apply_info_detail aid , ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_SHOP_GROUPINFO." mg , common_user cu, common_company_detail ccd
				".$where." group by ai.bai_ix order by ai.apply_date desc
				limit $start , $max ";
	//echo $sql;
	$db->query($sql);
	$buyingservice_infos = $db->fetchall("object");
	for($i=0;$i<count($buyingservice_infos);$i++){
		//$db->fetch($i);
		
		$sql = "SELECT count(distinct ws_ix) as ws_cnt,
				(
					select sum(amount) 
					from buyingservice_apply_info_detail
					where division='EA'
					and bai_ix = '".$buyingservice_infos[$i][bai_ix]."'
				)as EA_cnt,
				(
					select sum(amount) 
					from buyingservice_apply_info_detail
					where division='RA'
					and bai_ix = '".$buyingservice_infos[$i][bai_ix]."'
				)as RA_cnt,
				(
					select sum(amount) 
					from buyingservice_apply_info_detail
					where division='SR'
					and bai_ix = '".$buyingservice_infos[$i][bai_ix]."'
				)as SR_cnt
				FROM  buyingservice_apply_info_detail aid 
				where aid.bai_ix = '".$buyingservice_infos[$i][bai_ix]."' ";
		//echo $sql;
		$db->query($sql);
		$db->fetch();
		$ws_cnt = $db->dt[ws_cnt];
		$EA_cnt = $db->dt[EA_cnt];
		$RA_cnt = $db->dt[RA_cnt];
		$SR_cnt = $db->dt[SR_cnt];
		/*
		if($buyingservice_infos[$i][option_kind] == "s"){
			$option_kind_str = "선택옵션";
		}else if($buyingservice_infos[$i][option_kind] == "p"){
			$option_kind_str = "가격추가옵션";
		}
		
		
		if($buyingservice_infos[$i][option_type] == "9"){
			$option_type_str = "기본옵션";
		}else if($buyingservice_infos[$i][option_type] == "1"){
			$option_type_str = "가격추가옵션";
		}
		*/


		$mstring .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'>".($i+1)."</td>
					<td class='list_box_td point'>".$buyingservice_infos[$i][apply_date]."</td>
					<td class='list_box_td'>".$buyingservice_infos[$i][com_name]."</td>
					<td class='list_box_td'>".$buyingservice_infos[$i][buying_mem_name]."(".$buyingservice_infos[$i][id].")</td>
					<td class='list_box_td list_bg_gray'>".$buyingservice_infos[$i][amount]."</td>
					<td class='list_box_td'>".$ws_cnt."</td>
					<td class='list_box_td list_bg_gray'>".($buyingservice_infos[$i][amount]-$buyingservice_infos[$i][buying_complete_cnt])."</td>
					<td class='list_box_td'>".($buyingservice_infos[$i][amount])."</td>
					<td class='list_box_td list_bg_gray'>".number_format($RA_cnt)." / ".number_format($EA_cnt)." / ".number_format($SR_cnt)."</td>
					<td class='list_box_td'>".number_format($buyingservice_infos[$i][pre_payment_price])."</td>
					<td class='list_box_td list_bg_gray'>".($_buyingservice_status[$buyingservice_infos[$i][buying_status]])."</td>
					<td class='list_box_td' nowrap>";
			$mstring .="
					<a href=\"javascript:PoPWindow3('./buying_input.php?bai_ix=".$buyingservice_infos[$i][bai_ix]."',1100,700,'buying_input')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
			if($buyingservice_infos[$i][buying_status] == "IR"){
			$mstring .="
					<a href=\"javascript:DeleteBuyingServiceInfo('".$buyingservice_infos[$i][bai_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$mstring .="
					</td>
				</tr>";
	}
	$mstring .=	"</table>";
	$mstring .=	"<table width='100%' cellpadding=0 cellspacing=0>";
}else{
	$mstring .= "
				<tr height=50><td colspan=12 align=center style='padding:30px 0px;'>등록된 사입신청 목록이 없습니다.</td></tr>";
}

$mstring .="</table>";
$mstring .= "
	<table cellpadding=1 cellspacing=0 width=100% >
		<tr hegiht=30>
			<td colspan=5 style='text-align:left;'>".$str_page_bar."</td>
			<td colspan=1 align=right style='padding-top:10px;'><a href=\"javascript:PoPWindow3('./buying_input.php?mmode=pop',940,700,'buying_input')\"><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0></a></td>
		</tr>
	</table><br>";
$Contents = $mstring;

$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사입항목을 입력하기 위해서는 추가하기 버튼을 클릭하시면 사입정보를 입력할 수 있는 팝업이 노출됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >시입신청된 목록이 입금확인 되면 사입상품관리에서 사입항목을 관리 하 실수 있습니다.</td></tr>
</table>
";


$help_text = HelpBox("<div style='padding-top:7px;'>사입신청관리</div>", $help_text);
$Contents .= $help_text;

$Script = "	<link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' />
<script type='text/javascript' src='../js/ui/ui.core.js'></script>
<script type='text/javascript' src='../js/ui/ui.datepicker.js'></script>
<script language='javascript' >
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
 function DeleteBuyingServiceInfo(bai_ix){
 	if(confirm('해당 사입 정보를 정말로 삭제 하시겠습니까?')){
 	
 	f    = document.createElement('form');
    f.name = 'optionfrm';
    f.id = 'optionfrm';
    f.method    = 'post';
    f.target = 'iframe_act';
    f.action    = 'buying_input.act.php';

    i0          = document.createElement('input');
    i0.type     = 'hidden';
    i0.name     = 'act';
    i0.id     = 'act';
    i0.value    = 'delete';
    f.insertBefore(i0);

    i1          = document.createElement('input');
    i1.type     = 'hidden';
    i1.name     = 'bai_ix';
    i1.id     = 'bai_ix';
    i1.value    = bai_ix;
    f.insertBefore(i1);

		document.insertBefore(f);
		f.submit();

 	}
}

function select_date(FromDate,ToDate,dType) {
			var frm = document.searchmember;

			$(\"#start_datepicker\").val(FromDate);
			$(\"#end_datepicker\").val(ToDate);
		}

</script>";
/*
$P = new AdminPage();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
echo $P->AdminFrame();
*/
$P = new LayOut;
$P->addScript = "$Script";
$P->strLeftMenu = buyingservice_menu();
$P->Navigation = "사입관리 > 사입신청리스트";
$P->title = "사입신청리스트";
$P->strContents = $Contents;
$P->PrintLayOut();

/*
CREATE TABLE IF NOT EXISTS buyingservice_ws_info (
  si_ix int(10) unsigned NOT NULL auto_increment COMMENT '도매처 정보키값',
  mem_ix varchar(32) NOT NULL COMMENT '회원코드',
  ws_name varchar(50) NOT NULL COMMENT '도매처명',
  floor varchar(10) NOT NULL COMMENT '층',
  line varchar(10) NOT NULL COMMENT '라인',
  number varchar(10) NOT NULL COMMENT '호수',
  phone varchar(50) NOT NULL COMMENT '연락처',
  si_desc varchar(50) NOT NULL COMMENT '부가설명',
  disp ENUM( '1', '0' ) NOT NULL DEFAULT '1' COMMENT '도매처 사용여부' ,
  regdate datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY  (si_ix)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='도매처 정보'  ;


CREATE TABLE IF NOT EXISTS buyingservice_apply_info (
  bai_ix int(10) unsigned NOT NULL auto_increment COMMENT '사입정보키값',
  mem_ix varchar(32) NOT NULL COMMENT '회원코드',
  buying_mem_name varchar(50) NOT NULL COMMENT '회원이름',
  apply_date varchar(10) NOT NULL COMMENT '처리일자',
  buying_status varchar(2) NOT NULL COMMENT '처리상태',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY  (bai_ix)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='사입 정보'  ;


CREATE TABLE IF NOT EXISTS buyingservice_apply_info_detail (
  baid_ix int(10) unsigned NOT NULL auto_increment COMMENT '사입 상세정보 키값',
  bai_ix int(10) default NULL COMMENT '사입정보 키값',
  ws_ix int(10) default NULL COMMENT '도매처 키값',
  paper_name varchar(255) default NULL COMMENT '장기명',
  color varchar(50) default '' COMMENT '색상',
  size varchar(50) NOT NULL default '0' COMMENT '사이즈',
  amount int(4) unsigned default '0' COMMENT '수량',
  buying_complete_cnt int(4) unsigned default '0' COMMENT '사입완료 수량',
  soldout_cancel_cnt int(4) unsigned default '0' COMMENT '품절취소 수량',
  incom_ready_cnt int(4) unsigned default '0' COMMENT '입고대기 수량',
  buying_price int(10) NOT NULL default '0' COMMENT '도매가',
  pre_payment_price int(10) NOT NULL default '0' COMMENT '대납신청금',s
  exchange_yn enum('Y','N') NOT NULL default 'N' COMMENT '반품교환여부' ,
  buying_detail_status varchar(2) NOT NULL default '',
  regdate datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY  (baid_ix)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='사입 상세 정보'  ;

ALTER TABLE `buyingservice_apply_info_detail` ADD `buying_complete_cnt` INT( 4 ) NOT NULL COMMENT '사입완료 수량' AFTER `amount` ;
ALTER TABLE `buyingservice_apply_info_detail` ADD soldout_cancel_cnt INT( 4 ) NOT NULL COMMENT '품절취소 수량' AFTER `buying_complete_cnt` ;
ALTER TABLE `buyingservice_apply_info_detail` ADD incom_ready_cnt INT( 4 ) NOT NULL COMMENT '품절취소 수량' AFTER soldout_cancel_cnt ;

*/
?>