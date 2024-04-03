<?
include("../class/layout.class");
//auth(9);

$db = new Database;
$mdb = new Database;

$sql = "SHOW TABLES LIKE 'common_user_sleep_log'";
$db->query($sql);
if(!$db->total){
	$sql="CREATE TABLE `common_user_sleep_log` (
			  `sl_ix` int(10) unsigned zerofill NOT NULL auto_increment COMMENT '로그키값',
			  `code` varchar(32) NOT NULL COMMENT '회원코드',
			  `id` varchar(20) default NULL COMMENT '아이디',
			  `name` varchar(200) default NULL COMMENT '회원명',
			  `status` varchar(10) default NULL COMMENT '변경상태(자동, 수동 등 A:관리자, S:시스템, U:회원)',
			  `message` varchar(255) default NULL COMMENT '변경사유',
			  `charger_ix` varchar(32) default NULL COMMENT '관리자코드',
			  `change_type` enum('S','M') default NULL COMMENT '전환타입(S 휴면으로 전환, M 회원으로 재전환)',
			  `regdate` datetime default NULL COMMENT '등록일',
			  PRIMARY KEY  (`sl_ix`)
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
	";
	$db->query($sql);
}


//$db->debug = true;//페이지에서 오류가 없는데 쿼리를 찍기에 주석처리함 kbk 13/02/04
$update_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U");
$delete_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D");
$create_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C");
$excel_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E");

$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));


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

//include "member_query.php";


	$where = "where sl_ix !='' ";
	

	$search_text = trim($search_text);
	if($db->dbms_type == "oracle"){
		if($search_type != "" && $search_text != ""){
			if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
				$where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
				$count_where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			}else{
				$where .= " and $search_type LIKE  '%$search_text%' ";
				$count_where .= " and $search_type LIKE  '%$search_text%' ";
			}

		}
	}else{
		if($search_type != "" && $search_text != ""){
			if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
				$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
				$count_where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			}else{
				$where .= " and $search_type LIKE  '%$search_text%' ";
				$count_where .= " and $search_type LIKE  '%$search_text%' ";
			}
		}
	}


	$startDate = $cmd_sdate;
	$endDate = $cmd_edate;

	if($startDate != "" && $endDate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(regdate_ , 'YYYYMMDD') between  $startDate and $endDate ";
			$count_where .= " and  to_char(regdate__ , 'YYYYMMDD') between  $startDate and $endDate ";
		}else{
			$where .= " and  regdate between  '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";
			$count_where .= " and  regdate between  '".$startDate." 00:00:00' and '".$endDate." 23:59:59'  ";
		}
	}	

	if($status){
		$where .= " and status =  '$status' ";
	}
	
	$sql = "select * from common_user_sleep_log $where ";
	$db->query($sql);
	$total = $db->total;

	$sql = "select *,AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name from common_user_sleep_log $where order by sl_ix desc LIMIT $start,$max";
	$db->query($sql);


	if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
	}
	$str_page_bar = page_bar($total, $page, $max, $query_string,"");
	//$str_page_bar = page_bar($total, $page,$max, "&max=$max&mc_ix=$mc_ix&search_type=$search_type&search_text=$search_text&status=$status&regdate=$regdate&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD","view");//gp_level 을 사용하지 않아서

$Script = "
<script language='javascript'>


function deleteLogInfo(act, code){
 	if(confirm('해당로그 정보를 정말로 삭제하시겠습니까? \\n 삭제시 관련된 모든 정보가 삭제됩니다.')){
		window.frames['iframe_act'].location.href= 'sleep_member_log.act.php?act='+act+'&code='+code;
 	}
}


function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		$('input[name=cmd_sdate]').attr('disabled',false);
		$('input[name=cmd_edate]').attr('disabled',false);

	}else{
		$('input[name=cmd_sdate]').attr('disabled',true);
		$('input[name=cmd_edate]').attr('disabled',true);
	}
}


function init(){

	var frm = document.searchmember;
	onLoad('$sDate','$eDate');";

$Script .= "
}


</script>";

$Contents = "


<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>

<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("전체회원관리", "개인정보관리 > 휴면회원이력관리 ")."</td>
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
					<table id='tab_01'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='sleep_member.php?info_type=member'>일반회원</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' >
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents .= "<a href='sleep_member.php?info_type=sleep_member'>휴면회원</a>";

						$Contents .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' class='on' >
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents .= "<a href='sleep_member_log.php'>휴면이력</a>";

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
		    <col width='12%'>
			<col width='*'>
				<tr height=27>
				  <td class='search_box_title' >조건검색 </td>
				  <td class='search_box_item'>
						<table cellpadding=0 cellspacing=0 width=100%>
							<col width='80'>
							<col width='*'>
							<tr>
								<td>
								  <select name=search_type>
										<option value='name' ".CompareReturnValue("name",$search_type,"selected").">고객명</option>
										<option value='id' ".CompareReturnValue("id",$search_type,"selected").">아이디</option>
								  </select>
								</td>
								<td>
									<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:70%;font-size:12px;padding:1px;' >
								</td>
							</tr>
						</table>
				  </td>
				 <td class='search_box_title' >변경타입 </td>
				 <td class='search_box_item' >
					<select name='status' style='width:140px;font-size:12px;'>
					<option value=''>-- 선택 --</option>

					<option value='A'  ".CompareReturnValue("A",$status,"selected").">관리자 일관변경</option>
					<option value='S'  ".CompareReturnValue("S",$status,"selected").">시스템 자동</option>
					<option value='U'  ".CompareReturnValue("S",$status,"selected").">회원 휴면해지</option>
					
					</select>
				</td>
				</tr>
			
		    ";

$vdate = date("Y-m-d", time());
$today = date("Y/m/d", time());
$vyesterday = date("Y/m/d", time()-84600);
$voneweekago = date("Y/m/d", time()-84600*7);
$vtwoweekago = date("Y/m/d", time()-84600*14);
$vfourweekago = date("Y/m/d", time()-84600*28);
$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

 $Contents .= "
		    <tr height=27>
		      <td class='search_box_title' ><label for='regdate'>변경일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
		      <td class='search_box_item'  colspan=3 >
				".search_date('cmd_sdate','cmd_edate',$cmd_sdate,$cmd_edate)."		      	
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
</table>";

$Contents .= "
    </td>
  </tr>
  <tr height=50>
    	<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
    </tr>
</table><br></form>

<form name='list_frm'  method='POST' onsubmit='return SleepSubmit(this);' action='sleep_member.act.php'  target='act'>
";
if($info_type == 'sleep_member'){
$Contents .= "
<input type= 'hidden' name='act' value='move_member' />";
}else{
$Contents .= "
<input type= 'hidden' name='act' value='move_sleep' />";
}
$Contents .= "
<input type='hidden' name='code[]' id='code'>
<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
  <tr height=30 >
  	<td colspan=5>
    </td>
  	<td colspan=5 align=right>";
	/*if($excel_auth){
	$Contents .= "<a href='member_excel2003.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}*/
	$Contents .= "
	</td>
  </tr>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='27' bgcolor='#ffffff'>
   
	<!--td width='10' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td-->
    <td width='5%' align='center' class='m_td'><font color='#000000'><b>순번</b></font></td>
	<td width='10%' align='center' class='m_td'><font color='#000000'><b>변경일시</b></font></td>
    <td width='10%' align='center' class='m_td'><font color='#000000'><b>이름</b></font></td>
	<td width='10%' align='center' class='m_td' nowrap><font color='#000000'><b>아이디</b></font></td>
    <td width='16%' align='center' class='m_td' nowrap><font color='#000000'><b>변경타입</b></font></td>
	<td width='25%' align='center' class='m_td' nowrap><font color='#000000'><b>사유</b></font></td>
    <td width='10%' align='center' class=m_td nowrap><font color='#000000'><b>처리담당자</b></font></td>
	<td width='5%' align='center' class=m_td nowrap><font color='#000000'><b>관리</b></font></td>
	
  </tr>";
if ($db->total){

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;

	
        $Contents = $Contents."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">";
            

			switch($db->dt[status]){
				case "A":
				$sleep_status = "관리자 일관변경";
				break;
				case "S":
				$sleep_status = "시스템 자동";
				break;
				case "U":
				$sleep_status = "회원 휴면해지";
				break;
			}
			$Contents .= "
			<!--td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td-->
            <td class='list_box_td' >".$no."</td>
            <td class='list_box_td' style='padding:0px 5px;' nowrap>".$db->dt[regdate]."</td>
			<td class='list_box_td' nowrap>".wel_masking_seLen($db->dt[name], 1, 1)."</td>
            <td class='list_box_td' nowrap>".$db->dt[id]."</td>
			<td class='list_box_td' nowrap>".$sleep_status."</td>
            
            <td class='list_box_td' >".$db->dt[message]."</span> </td>";
			if($db->dt[charger_ix]){
				$Contents .= "
            <td class='list_box_td' >".wel_masking_seLen(getChargerinfo($db->dt[charger_ix],'name'), 1, 1)."<br> ( ".getChargerinfo($db->dt[charger_ix],'id')." )</td>";
			}else{
				$Contents .= "
				<td class='list_box_td' >-</td>";
			}
			$Contents .= "
            <td class='list_box_td ctr'  style='padding:5px;' nowrap>";
			

			//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
			if($delete_auth && $_SESSION[admininfo][personal_information] != '0'){
				$Contents .= "<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"deleteLogInfo('delete','".$db->dt[sl_ix]."')\" style='cursor:pointer;' alt='삭제' title='삭제'/> ";
			}else{
				$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle ></a> ";
			}
            
            $Contents .= "
			 </td>
  </tr>";

	}

}else{

$Contents = $Contents."
  <tr height=50>
    <td colspan='15' align='center'>등록된 회원 데이타가 없습니다.</td>
  </tr>";
}

$Contents .= "
</table>

<table width=100%>
	<tr>
		<td><div style='width:100%;text-align:right;padding:5px 0px;'>".$str_page_bar."</div></td>
		<td align=right>
		<!--img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\" /-->
		<!--div style='cursor:pointer' onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\">회원수동등록</div-->
	</td>
</tr>
</table>";

$help_text = "

<table cellpadding=0 cellspacing=0 class='small' width=100% >
	<col width=8>
	<col width=*>
	<tr>
		<td>
			* 휴면회원의 변경 이력을 관리하는 페이지 입니다.
		</td>
	</tr>
</table>
";
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("휴면회원이력관리", $help_text,'70')."</form>";


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "";//init();
$P->strLeftMenu = member_menu();
$P->Navigation = "개인정보관리 > 휴면회원이력관리";
$P->title = "휴면회원관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();

function getChargerinfo($code,$type){
	$db = new MySQL;
	
	$sql = "select cu.id, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code where cu.code = '".$code."'";
	$db->query($sql);
	$db->fetch();

	if($type == 'name'){
		$data = $db->dt[name];
	}else if ($type == 'id'){
		$data = $db->dt[id];
	}
	return $data;
}

//	웰숲 우클릭 방지
include_once("../order/wel_drag.php");
?>