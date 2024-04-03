<?
include("../class/layout.class");
//auth(9);
$script_times[page_start] = time();
$db = new Database;
$mdb = new Database;
//$db->debug = true;//페이지에서 오류가 없는데 쿼리를 찍기에 주석처리함 kbk 13/02/04
$update_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U");
$delete_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D");
$create_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C");
$excel_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E");

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

if ($admininfo[mall_type] == "O"){
	if($db->dbms_type == "oracle"){
		$where = " where cu.code = cmd.code and cu.mem_type in ('M','C','F','S') ";
	}else{
		$where = " where cu.code != '' and cu.code = cmd.code and cu.mem_type in ('M','C','F','S') ";
	}
}else{
	if($db->dbms_type == "oracle"){
		$where = " where cu.code = cmd.code and cu.mem_type in ('M','C','F') ";
	}else{
		$where = " where cu.code != '' and cu.code = cmd.code and cu.mem_type in ('M','C','F') ";
	}
}

if($region != ""){	//지역선택
	$where .= " and cmd.addr1 LIKE  '%".$region."%' ";
}

if($gp_ix != ""){	//회원그룹
	$where .= " and cmd.gp_ix = '".$gp_ix."' ";
}

if($mem_type != ""){	//회원타입
	$where .= " and mem_type =  '$mem_type' ";
}


$search_text = trim($search_text);
if($db->dbms_type == "oracle"){
	if($search_type != "" && $search_text != ""){
		if($search_type == "cmd.name" || $search_type == "cmd.mail" || $search_type == "cmd.pcs" || $search_type == "cmd.tel"  || $search_type == "cu.id" || $search_type == "cmd.addr1"){
			$where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			$count_where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
		}else{
			$where .= " and $search_type LIKE  '%$search_text%' ";
			$count_where .= " and $search_type LIKE  '%$search_text%' ";
		}
	}
}else{
	if($search_type != "" && $search_text != ""){
		if($search_type == "cmd.name" || $search_type == "cmd.mail" || $search_type == "cmd.pcs" || $search_type == "cmd.tel"  || $search_type == "cmd.addr1"){
			$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			$count_where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
		}else if($search_type == "id"){

			if(strpos($search_text,",") !== false){
				$search_array = explode(",",$search_text);
				$where .= "and ( ";
				$count_where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){

					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
						$count_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
						$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
				$where .= ")";
				$count_where .= ")";
			}else if(strpos($search_text,"\n") !== false){//\n

				$search_array = explode("\n",$search_text);

				$where .= "and ( ";
				$count_where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){

					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
						$count_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
						$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
				$where .= ")";
				$count_where .= ")";
			}else{
				$where .= "and ".$search_type." like '%".trim($search_text)."'%";
				$count_where .= "and ".$search_type." like '%".trim($search_text)."'%";
			}
		}else{
			$where .= " and $search_type LIKE  '%$search_text%' ";
			$count_where .= " and $search_type LIKE  '%$search_text%' ";
		}
	}
}

$startDate = $cmd_sdate;
$endDate = $cmd_edate;

if($regdate == '1'){	//가입일자
	if($startDate != "" && $endDate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(cu.date_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
			$count_where .= " and  to_char(cu.date_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
		}else{
			$where .= " and date_format(cu.date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
			$count_where .= " and date_format(cu.date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
		}
	}
}

$vstartDate = $order_sdate;
$vendDate = $order_edate;

if($visitdate == '1'){
	if($vstartDate != "" && $vendDate != ""){	//최근방문일
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(o.order_date , 'YYYY-MM-DD') between  '".$vstartDate."' and '".$vendDate."' ";
			$count_where .= " and  to_char(o.order_date , 'YYYY-MM-DD') between '".$vstartDate."' and '".$vendDate."' ";
		}else{
			$where .= " and date_format(o.order_date,'%Y-%m-%d') between  '".$vstartDate."' and '".$vendDate."' ";
			$count_where .= " and date_format(o.order_date,'%Y-%m-%d') between  '".$vstartDate."' and '".$vendDate."' ";
		}
	}
}

if($total == ""){
	// 전체 갯수 불러오는 부분
	$sql = "SELECT count(*) as total FROM ".TBL_COMMON_USER." cu
				join ".TBL_SHOP_ORDER." as o on (cu.code = o.user_code)
				join ".TBL_SHOP_ORDER_DETAIL." as od on (o.oid = od.oid)
				, ".TBL_COMMON_MEMBER_DETAIL." cmd $where ";
				//left join  ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix 
	//echo nl2br($sql);
	//exit;
	$script_times[query_count_start] = time();
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];
}
$script_times[query_count_end] = time();
$str_page_bar = page_bar($total, $page,$max, "&info_type=cust_member&max=$max&search_type=$search_type&search_text=$search_text&region=$region&gp_level=$gp_level&age=$age&birthday_yn=$birthday_yn&sex=$sex&mailsend_yn=$mailsend_yn&smssend_yn=$smssend_yn&mem_type=$mem_type&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD&total=$total","view");

if($db->dbms_type == "oracle"){
	$sql = "select cu.code, cu.id,  cu.company_id, ccd.com_name,
		AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, mg.gp_level,
		AES_DECRYPT(cmd.mail,'".$db->ase_encrypt_key."') as mail, cu.authorized as authorized, cu.is_id_auth as is_id_auth, cu.mem_type as mem_type,
		cu.visit, date_format(cu.date_,'%Y.%m.%d') as regdate, mg.gp_name, cu.last AS last
		from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
		left join ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix
		, ".TBL_COMMON_COMPANY_DETAIL." ccd
		$where and cu.company_id = ccd.company_id
		ORDER BY cu.date_ DESC
		LIMIT $start, $max";
}else{
	$sql = "select 
			cu.code, 
			cu.id, 
			cu.company_id, 
			ccd.com_name,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			
			AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, 
			cu.authorized as authorized, 
			cu.is_id_auth as is_id_auth,
			cu.mem_type as mem_type,
			cu.visit, 
			date_format(cu.date,'%Y.%m.%d') as regdate, 
			
			UNIX_TIMESTAMP(cu.last) AS last,
			sum(if(od.status not in ('IR'),od.pcnt,0)) as order_cnt,
			sum(if(od.status not in ('IR'),od.psprice,0)) as order_price,
			sum(if(od.status  in ('DC'),od.ptprice,0)) as mem_ptprice,

			sum(if(od.status in ('CA','CC','SO'),od.pcnt,0)) as mem_cancel_cnt,
			sum(if(od.status in ('EA','EI','ED','EC'),od.pcnt,0)) as mem_exchange_cnt,
			sum(if(od.status in ('RA','RI','RD','RC','FA','FC'),od.pcnt,0)) as mem_return_cnt,
			sum(if(od.status in ('DC'),od.pcnt,0)) as DC_order_cnt

		from 
			".TBL_COMMON_USER." cu 
			join ".TBL_SHOP_ORDER." as o on (cu.code = o.user_code)
			join ".TBL_SHOP_ORDER_DETAIL." as od on (o.oid = od.oid)
			left join ".TBL_COMMON_MEMBER_DETAIL." cmd on (cu.code = cmd.code)
			
			left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (cu.company_id = ccd.company_id)
			
		$where 
			group by cu.code
			ORDER BY cu.date DESC
			LIMIT $start, $max";
			//mg.gp_level, mg.gp_name,
			//left join ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix
}
$script_times[query_start] = time();
$db->query($sql);
$script_times[query_end] = time();

$Script = "
<script language='javascript'>

function view_go()
{
	var sort = document.view.sort[document.view.sort.selectedIndex].value;

	location.href = 'member.php?view='+sort;
}


function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		$('input[name=cmd_sdate]').attr('disabled',false);
		$('input[name=cmd_edate]').attr('disabled',false);

	}else{
		$('#cmd_sdate').attr('disabled',true);
		$('#cmd_edate').attr('disabled',true);
	}
}

function ChangeVisitDate(frm){
	if(frm.visitdate.checked){
		$('input[name=order_sdate]').attr('disabled',false);
		$('input[name=order_edate]').attr('disabled',false);
	}else{
		$('input[name=order_sdate]').attr('disabled',true);
		$('input[name=order_edate]').attr('disabled',true);
	}
}

function init(){
	var frm = document.searchmember;
	onLoad('$sDate','$eDate','$sDate2','$eDate2','$sDate3');
}

function deleteMemberInfo(act, code){
	if(confirm('해당회원 정보를 정말로 삭제하시겠습니까? \\n 삭제시 관련된 모든 정보가 삭제됩니다.')){
		window.frames['iframe_act'].location.href= 'member.act.php?act='+act+'&code='+code;
	}
}

</script>";

$Contents = "


<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>

<table width='100%' border='0' align='center'>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("전체회원관리", "회원관리 > 전체회원관리 ")."</td>
</tr>

<tr>
	<td>";

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	<tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "본사관리 > $menu_name")."</td>
	</tr>
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "member" || $info_type == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='member.php?info_type=member'>전체회원리스트</a> <span style='padding-left:2px' class='helpcloud' help_width='410' help_height='70' help_html='사업자 정보를 작성하여 등록한 후에 해당 사업자에 관리자화면 로그인 정보를 발급해 줄 수 있습니다.<br />사업자 정보 추가 후 [목록관리] - [사용자 추가]를 통해 관리자에 계정을 발급해 주시기 바랍니다.'><img src='/admin/images/icon_q.gif' /></span></td>
						<th class='box_03'></th>
					</tr>
					</table>";
/*
$Contents .= "
					<table id='tab_02' ".($info_type == "basic_member" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents .= "<a href='basic_member.php?info_type=basic_member'>회원가입/매출기초분석</a>";

						$Contents .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>";
*/
$Contents .= "
					<table id='tab_03' ".($info_type == "cust_member" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents .= "<a href='cust_member.php?info_type=cust_member'>회원별상세매출분석</a>";

						$Contents .= "

						</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>
				</td>
			</tr>
			</table>
		</div>
	    </td>
	</tr>
	</table>
";

$Contents .= "
<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<th class='box_01'></th>
		<td class='box_02'></td>
		<th class='box_03'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<td class='box_05'  valign=top style='padding:5px 0px 5px 0px'>
 			<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
			<form name=searchmember method='get'><!--SubmitX(this);'-->
			<input type='hidden' name=mc_ix value='".$mc_ix." '>
			<input type='hidden' name=info_type value='cust_member'>
			<col width='18%'>
			<col width='32%'>
			<col width='18%'>
			<col width='32%'>
				<tr height=27>
				<td class='search_box_title' >회원구분 </td>
				<td class='search_box_item' >
					<input type=radio name='nationality' value='I' id='nationality_I'  ".CompareReturnValue("I",$nationality,"checked")." checked><label for='nationality_I'>국내회원</label>
					<input type=radio name='nationality' value='O' id='nationality_O'  ".CompareReturnValue("O",$nationality,"checked")."><label for='nationality_O'>해외회원</label>
					<input type=radio name='nationality' value='D' id='nationality_D' ".CompareReturnValue("D",$nationality,"checked")."><label for='nationality_D'>기타회원</label>
				</td>
				<td class='search_box_title' >회원타입 </td>
				<td class='search_box_item' >
					<input type=radio name='mem_type' value='' id='mem_type_'  ".CompareReturnValue("",$mem_type,"checked")." checked><label for='mem_type_'>모두</label>
					<input type=radio name='mem_type' value='M' id='mem_type_m'  ".CompareReturnValue("M",$mem_type,"checked")."><label for='mem_type_m'>일반회원</label>
					<input type=radio name='mem_type' value='C' id='mem_type_c' ".CompareReturnValue("C",$mem_type,"checked")."><label for='mem_type_c'>사업자회원</label>
				</td>
			</tr>
			<tr height=27>
				<td class='search_box_title'  >지역선택</td>
				<td class='search_box_item'>
					<select name='region' style='width:140px;font-size:12px;'>
						<option value=''>-- 선택 --</option>
						<option value='서울'  ".CompareReturnValue("서울",$region,"selected").">서울</option>
						<option value='충북'  ".CompareReturnValue("충북",$region,"selected").">충북</option>
						<option value='충남'  ".CompareReturnValue("충남",$region,"selected").">충남</option>
						<option value='전북'  ".CompareReturnValue("전북",$region,"selected").">전북</option>
						<option value='제주'  ".CompareReturnValue("제주",$region,"selected").">제주</option>
						<option value='전남'  ".CompareReturnValue("전남",$region,"selected").">전남</option>
						<option value='경북'  ".CompareReturnValue("경북",$region,"selected").">경북</option>
						<option value='경남'  ".CompareReturnValue("경남",$region,"selected").">경남</option>
						<option value='경기'  ".CompareReturnValue("경기",$region,"selected").">경기</option>
						<option value='부산'  ".CompareReturnValue("부산",$region,"selected").">부산</option>
						<option value='대구'  ".CompareReturnValue("대구",$region,"selected").">대구</option>
						<option value='인천'  ".CompareReturnValue("인천",$region,"selected").">인천</option>
						<option value='광주'  ".CompareReturnValue("광주",$region,"selected").">광주</option>
						<option value='대전'  ".CompareReturnValue("대전",$region,"selected").">대전</option>
						<option value='울산'  ".CompareReturnValue("울산",$region,"selected").">울산</option>
						<option value='강원'  ".CompareReturnValue("강원",$region,"selected").">강원</option>
					</select>
				</td>
				<td class='search_box_title' >회원그룹 </td>
				<td class='search_box_item' >
					".makeGroupSelectBox($mdb,"gp_ix",$gp_ix)."
				</td>
				<!--
				<td class='search_box_title' >성별 </td>
				<td class='search_box_item' >
					<input type=radio name='sex' value='' id='sex_all'  ".CompareReturnValue("0",$sex,"checked")." checked><label for='sex_all'>전체</label>
					<input type=radio name='sex' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
					<input type=radio name='sex' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label>
				</td>-->
			</tr>
			<tr height=27>
				<td class='search_box_title' ><label for='regdate'>가입일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
				<td class='search_box_item'  colspan=3 >
					".search_date('cmd_sdate','cmd_edate',$cmd_sdate,$cmd_edate)."
				</td>
			</tr>
			<tr height=27>
				<td class='search_box_title' ><label for='visitdate'>주문일자</label><input type='checkbox' name='date' id='visitdate' value='1' onclick='ChangeVisitDate(document.searchmember);' ".CompareReturnValue("1",$date,"checked")."></td>
				<td class='search_box_item'  colspan=3  >
					".search_date('order_sdate','order_edate',$order_sdate,$order_edate)."
				</td>
			</tr>
			<tr height=27>
				<td class='search_box_title' >조건검색 </td>
				<td class='search_box_item' colspan='3'>
					<table cellpadding=0 cellspacing=0 width=100%>
						<col width='80'>
						<col width='*'>
						<tr>
							<td>
							  <select name=search_type>
									<option value='cmd.name' ".CompareReturnValue("cmd.name",$search_type,"selected").">고객명</option>
									<option value='cu.id' ".CompareReturnValue("cu.id",$search_type,"selected").">아이디</option>
									<option value='cmd.tel' ".CompareReturnValue("cu.tel",$search_type,"selected").">전화번호</option>
									<option value='cmd.pcs' ".CompareReturnValue("cmd.pcs",$search_type,"selected").">휴대전화</option>
									<option value='ccd.com_name' ".CompareReturnValue("ccd.com_name",$search_type,"selected").">회사명</option>
									<option value='ccd.com_phone' ".CompareReturnValue("ccd.com_phone",$search_type,"selected").">회사전화</option>
									<option value='ccd.com_fax' ".CompareReturnValue("ccd.com_fax",$search_type,"selected").">회사팩스</option>
									<option value='cmd.mail' ".CompareReturnValue("cmd.mail",$search_type,"selected").">이메일</option>
									<option value='cmd.addr1' ".CompareReturnValue("cmd.addr1",$search_type,"selected").">주소</option>
							  </select>
							</td>
							<td>
								<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:250px;font-size:12px;padding:1px;' >
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
</table>			";

$Contents .= "
    </td>

  </tr>
  <tr height=50>
    	<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
    </tr>
</table><br></form>
<form name='list_frm'>
<input type='hidden' name='code[]' id='code'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >

  <tr height=30 >
  	<td colspan=5>";

    if($create_auth){
        $Contents .= "
        <a href='javascript:listAction(document.list_frm);'><img src='../images/".$admininfo["language"]."/btn_selected_sms.gif' align=absmiddle  ></a>";
    }else{
        $Contents .= "
        <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_selected_sms.gif' align=absmiddle ></a>";
    }
    $Contents .= "
    </td>
  	<td colspan=5 align=right>";
	if($excel_auth){
	$Contents .= "<a href='member_excel2003.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}

$Contents .= "
	</td>
  </tr>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='27' bgcolor='#ffffff'>
    <td width='3%' align='center' class=s_td rowspan='2'><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td width='5%' align='center' class='m_td' rowspan='2'><font color='#000000'><b>순번</b></font></td>
    <td width='7%' align='center' class='m_td' rowspan='2'><font color='#000000'><b>이름</b></font></td>
	<td width='6%' align='center' class='m_td' nowrap rowspan='2'><font color='#000000'><b>아이디</b></font></td>
    <td width='6%' align='center' class='m_td' nowrap colspan='5'><font color='#000000'><b>주문수량</b></font></td>
    <td width='12%' align='center' class=m_td nowrap rowspan='2'><font color='#000000'><b>총주문금액</b></font></td>
    <td width='12%' align='center' class=m_td rowspan='2'><font color='#000000'><b>총매출금액</b></font></td>
    <td width='40' align='center' class=m_td rowspan='2'><font color='#000000'><b>**매출전환율</b><br>(주문대비)</br></font></td>
    <td width='40' align='center' class=m_td rowspan='2'><font color='#000000'><b>**매출점유율</b><br>(회원대비)</br></font></td>
    <td width='7%' align='center' class=e_td rowspan='2'><font color='#000000'><b>관리</b></font></td>
  </tr>
  <tr height='27' bgcolor='#ffffff'>
    <td width='5%' align='center' class=s_td ><font color='#000000'>주문</b></font></td>
    <td width='5%' align='center' class='m_td'><font color='#000000'><b>취소</b></font></td>
    <td width='5%' align='center' class='m_td'><font color='#000000'><b>교환</b></font></td>
	<td width='5%' align='center' class='m_td' nowrap ><font color='#000000'><b>반품</b></font></td>
    <td width='5%' align='center' class=e_td ><font color='#000000'><b>구매확정</b></font></td>
  </tr>
  ";


	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;
	/*
		if ($db->dt[mem_level] == "E")	{ $perm = "탈퇴회원"; }
		if ($db->dt[mem_level] == "M")	{ $perm = "일반회원"; }
		if ($db->dt[mem_level] == "B")	{ $perm = "입점업체"; }
		if ($db->dt[mem_level] == "C")	{ $perm = "특별회원"; }
	*/
		
		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT gp_name FROM ".TBL_SHOP_GROUPINFO." WHERE gp_ix = '".$db->dt[gp_ix]."'  ");
		}else{
			$mdb->query("SELECT gp_name FROM ".TBL_SHOP_GROUPINFO." WHERE gp_ix = '".$db->dt[gp_ix]."'  ");
		}
		$mdb->fetch(0);
		$gp_name = $mdb->dt[gp_name];

		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT sum(reserve) as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid_ = '".$db->dt[code]."' and state in ('1','2','5','6','7')");
		}else{
			$sql = "SELECT IFNULL(sum(reserve),'0') as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid = '".$db->dt[code]."' and state in ('1','2','5','6','7')";
			//echo $sql;
			$mdb->query($sql);
		}
		$mdb->fetch(0);
		$reserve_sum = number_format($mdb->dt[reserve_sum]);

		$order_cnt = $db->dt[order_cnt];	//주문수량
		$mem_cancel_cnt = $db->dt[mem_cancel_cnt];	//주문취소수량
		$mem_exchange_cnt = $db->dt[mem_exchange_cnt];	//주문교환수량
		$mem_return_cnt = $db->dt[mem_return_cnt];	//주문교환수량
		$DC_order_cnt = $db->dt[DC_order_cnt];	//구매확정수량

		$ptprice = $db->dt[mem_ptprice];	//구매확정수량
		$order_price = $db->dt[order_price];	//구매확정수량
		if($order_price > 0){
			$ptprice_rate = $ptprice / $order_price * 100 ;		//매출전환율
		}

		//////////////////////////매출점유율 시작///////////////////////////////

		$sql = "select sum(ptprice) total_price from shop_order_detail where status = 'DC'";
		$mdb->query($sql);
		$mdb->fetch();
		$total_price = $mdb->dt[total_price];
		if($total_price > 0){
			$order_price_rate = $ptprice / $total_price *100;	//매출점유율
		}

		//////////////////////////매출점유율 끝///////////////////////////////
        $Contents = $Contents."
          <tr height='27' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
            <td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
            <td class='list_box_td' >".$no."</td>
			 <td class='list_box_td point' nowrap><a href=\"javascript:PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\" style='cursor:pointer' >".Black_list_check($db->dt[code],$db->dt[name])."</td>
			  <td class='list_box_td' nowrap><a href=\"javascript:PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\" style='cursor:pointer' >".$db->dt[id]."</a></td>
            <td class='list_box_td' style='padding:0px 5px;' nowrap><span title='".$db->dt[organization_name]."'>".$order_cnt."</span></td>
			<td class='list_box_td' nowrap>".$mem_cancel_cnt."</td>
            <td class='list_box_td' >".$mem_exchange_cnt."</td>
			<td class='list_box_td' >".$mem_return_cnt."</td>
            <td class='list_box_td' >".$DC_order_cnt."</td>
			<td class='list_box_td' ><font color=red> ".number_format($order_price)."원</font></td>
            <td class='list_box_td' ><font color=red> ".number_format($ptprice)."원</font></td>
            <td class='list_box_td' >".round($ptprice_rate,2)."%</td>
			<td class='list_box_td' >".round($order_price_rate,2)."%</td>
            <td class='list_box_td ctr'  style='padding:5px;' nowrap>";

			if($update_auth){
				$Contents .= "<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_info')\" style='cursor:pointer;' /> ";
			}else{
				$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle ></a> ";
			}

			//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
			if($delete_auth){
				$Contents .= "<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"deleteMemberInfo('delete','".$db->dt[code]."')\" style='cursor:pointer;' /> ";
			}else{
				//$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0 align=absmiddle ></a> ";
			}
            if($create_auth){
			     $Contents .= "
        	     <img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../sms.pop.php?code=".$db->dt[code]."',500,380,'sendsms')\" style='cursor:pointer;' alt='문자발송' title='문자발송'>
        	     <img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?code=".$db->dt[code]."',550,535,'sendmail')\" style='cursor:pointer;' alt='메일발송' title='메일발송'>
                 ";
            }else{
                $Contents .= "
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle alt='문자발송' title='문자발송'></a>
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle alt='메일발송' title='메일발송'></a>
                 ";
            }
            $Contents .= "
    </td>
  </tr>";

	}

if (!$db->total){

$Contents = $Contents."
  <tr height=50>
    <td colspan='14' align='center'>등록된 회원 데이타가 없습니다.</td>
  </tr>";
}

$Contents .= "
</table>
</form>
<table width=100%>
<tr>
	<td><div style='width:100%;text-align:right;padding:5px 0px;'>".$str_page_bar."</div></td>
	<td align=right>
	<!--img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\" /-->
	<!--div style='cursor:pointer' onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\">회원수동등록</div-->
	</td>
</tr>
</table>";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원에게 SMS 또는 메일을 보내실려면 보내고자 하는 회원을 선택하신후 '선택회원 SMS 보내기' 버튼을 클릭하신후 메일을 보내실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원정보를 백업하기 위해서는 회원정보 검색후 엑셀로 저장 버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색기능을 통해서 조건에 맞는 회원을 빠르게 검색하실수 있습니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("회원관리", $help_text,'70');

$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";
$script_times[page_end] = time();
//print_r($script_times);
$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "";//init();
$P->strLeftMenu = member_menu();
$P->Navigation = "회원관리 > 전체회원";
$P->title = "전체회원";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>



