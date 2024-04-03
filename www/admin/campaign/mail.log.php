<?
include("../class/layout.class");
//include("./mail.config.php");

$db = new Database;
$db->query("SELECT * FROM shop_mail_box where mail_ix= '$mail_ix'");
$db->fetch();

if($db->total){
	$mail_ix = $db->dt[mail_ix];
	$mail_title = $db->dt[mail_title];
	$mail_text = $db->dt[mail_text];
	$mail_type = $db->dt[mail_type];
	$mail_code = $db->dt[mail_code];
	$mail_send_type = $db->dt[mail_send_type];
	$regdate = $db->dt[regdate];
	$disp = $db->dt[disp];
	$act = "update";
}else{
	$act = "insert";
	$mail_use_sdate = "";
	$mail_use_edate = "";
	$disp = "1";
}
$Script = "
<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>
<Script Language='JavaScript'>
function SubmitX(frm){
	//alert(iView.document.body.innerHTML);
	frm.content.value = iView.document.body.innerHTML;
	return true;
}

function SendMailCheck(frm){
	if(frm.mt_ix.value == ''){
		alert('타겟군을 선택해주세요');
		return false;
	}

	frm.mail_content.value = iView.document.body.innerHTML;
	return true;
}



function init(){
	  CKEDITOR.replace('basicinfo',{
	  startupFocus : false,height:500
	  });
}


function clearAll(frm){
		for(i=0;i < frm.mh_ix.length;i++){
				frm.mh_ix[i].checked = false;
		}
}
function checkAll(frm){
       	for(i=0;i < frm.mh_ix.length;i++){
				frm.mh_ix[i].checked = true;
		}
}
function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;

	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}



function Selectdelete(frm){
	var check_bool = false;


		for(i=0;i < frm.mh_ix.length;i++){
			if(frm.mh_ix[i].checked){
				check_bool = true	;
			}
		}

		if(!check_bool){
			alert('한명 이상의 회원이 선택되어야 합니다');
			return false;
		}

		return true;

}

function deleteMailHistory(act, mh_ix){
	if(confirm('해당 메일목록을 정말 삭제하시겠습니까? '))
	{
		document.frames['iframe_act'].location.href= 'mail.act.php?act=history_delete&mh_ix='+mh_ix;
		//
	}
}


</Script>";



$Contents = "
<table width='100%' border='0' align='left' >
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("메일발송내역", "메일링/SMS > 메일발송내역 ")."</td>
</tr>
 ";
  $Contents .= "
  <tr>
    <td align='left'>"; 
		if(!$max){
			$max = 15; //페이지당 갯수
		}

		if ($page == '')
		{
			$start = 0;
			$page  = 1;
		}
		else
		{
			$start = ($page - 1) * $max;
		}


		$sql = 	"SELECT count(*) as total FROM shop_mailling_history mh left join shop_addressbook ml on mh.ab_ix = ml.ab_ix where mh.ab_ix != ''  ";
		$db->query($sql);
		$db->fetch();
		$total = $db->dt[total];

		if($db->dbms_type == "oracle"){
			// union 에선 class 에서 파서에 해당 안됨 ...
			$sql = "select * from (
								select a.*, ROWNUM rnum from (
					select mh.mh_ix, mh.sended_mail, mh.regdate, mh.mail_open, mh.mail_click, mh.open_date, ml.user_name, com_name, phone
					from shop_mailling_history mh
					left join shop_addressbook ml on mh.ab_ix = ml.ab_ix and mail_ix = '$mail_ix'
					where mail_ix = '$mail_ix' and mh.ab_ix = ml.ab_ix and mh.ab_ix != '0'
					union
					select mh.mh_ix, mh.sended_mail, mh.regdate, mh.mail_open, mh.mail_click, mh.open_date,
					AES_DECRYPT(IFNULL(cmd.name,'-'),'".$db->ase_encrypt_key."')  as user_name ,
					AES_DECRYPT(IFNULL(ccd.com_name,'-'),'".$db->ase_encrypt_key."') as com_name,
					AES_DECRYPT(IFNULL(cmd.tel,'-'),'".$db->ase_encrypt_key."')  as phone
					from shop_mailling_history mh
					left join common_member_detail cmd on mh.ucode = cmd.code  and mail_ix = '$mail_ix'
					left join common_user cu on mh.ucode = cu.code
					left join common_company_detail ccd on cu.company_id = ccd.company_id
					where  mh.ucode is not null
					order by regdate desc
							) a where ROWNUM <= ".($start+$max)."
					) where rnum >= ".$start."";
		}else{
			$sql = "SELECT 
						mh.mh_ix, mh.sended_mail, mh.regdate, mh.mail_open, mh.mail_click, mh.open_date,
						mb.mail_title , 
						AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$db->ase_encrypt_key."')  as user_name,
						AES_DECRYPT(UNHEX(IFNULL(cmd.tel,'-')),'".$db->ase_encrypt_key."')  as phone
					FROM
						shop_mailling_history mh 
					LEFT JOIN
						shop_mail_box mb 
					ON	
						mh.mail_ix = mb.mail_ix
					LEFT JOIN
						common_member_detail cmd
					ON	
						cmd.code = mh.ucode
					ORDER BY mh.regdate DESC
					LIMIT $start, $max 
			";
			/*$sql = "select mh.mh_ix, mh.sended_mail, mh.regdate, mh.mail_open, mh.mail_click, mh.open_date, ml.user_name, com_name, phone
					from shop_mailling_history mh
					left join shop_addressbook ml on mh.ab_ix = ml.ab_ix  
					where   mh.ab_ix != ''
					union
					select mh.mh_ix, mh.sended_mail, mh.regdate, mh.mail_open, mh.mail_click, mh.open_date,
					AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$db->ase_encrypt_key."')  as user_name ,
					AES_DECRYPT(UNHEX(IFNULL(ccd.com_name,'-')),'".$db->ase_encrypt_key."') as com_name,
					AES_DECRYPT(UNHEX(IFNULL(cmd.tel,'-')),'".$db->ase_encrypt_key."')  as phone
					from shop_mailling_history mh
					left join common_member_detail cmd on mh.ucode = cmd.code   
					left join common_user cu on mh.ucode = cu.code
					left join common_company_detail ccd on cu.company_id = ccd.company_id
					where  mh.ucode != ''
					order by regdate desc
					limit $start, $max ";
			*/
		}

		//echo nl2br($sql);
		$db->query($sql);

		$Contents .= "
			<form name='listform' action='mail.act.php' method='POST' onsubmit='return Selectdelete(this);' target='act'><input type='hidden' name='act' value='history_select_delete'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
			  <tr>
				<td align='left' colspan=11 style='padding:0px 0px 10px 0px'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  메일발송  목록 (총 : ".number_format($total)." 개)</b></div>")."</td>
			  </tr>
			  </table>
			  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
			  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
				<td class=s_td style='width:30px;'><input type=checkbox class=nonborder id='all_fix' name='all_fix' onclick='fixAll(document.listform)'></td>
				<td class=m_td style='width:50px;'> no</td>
				<td class=m_td style='width:200px;'> 메일제목</td>
				<td class=m_td style='width:100px;'> 회원명</td>
				<td class=m_td style='width:100px;'> 일반전화</td>
				<td class=m_td style='width:150px;'> 수신 이메일</td>
				<td class=m_td style='width:40px;'> 오픈</td>
				<td class=m_td style='width:40px;'> 방문</td>
				<td class=m_td style='width:100px;'> 수신일시</td>
				<td class=m_td style='width:100px;'> 발송일시</td>
				<td class=e_td style='width:50px;'> 관리</td>
			  </tr>";

		if($db->total){
			for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

			$Contents .= "

				  <tr bgcolor=#ffffff height=30 align=center>
					<td bgcolor='#ffffff'><input type=checkbox class=nonborder id='mh_ix' name='mh_ix[]' value='".$db->dt[mh_ix]."|".$db->dt[email]."'></td>
					<td align=center>".$no."</td>
					<td align=left>".$db->dt[mail_title]."</td>
					<td onclick=\"show_mailling_info(document.getElementById('mailling_info_".$db->dt[mh_ix]."'))\" style='cursor:hand;'>".$db->dt[user_name]."</td>
					<td>".$db->dt[phone]."</td>
					<td>".$db->dt[sended_mail]."</td>
					<td>".($db->dt[mail_open] == "0" ? "×":"○")."</td>
					<td>".($db->dt[mail_click] == "0" ? "×":"○")."</td>
					<td>".$db->dt[open_date]."</td>
					<td nowrap>".$db->dt[regdate]."</td>
					<td>";
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
						$Contents .= "
						<a href=\"javascript:deleteMailHistory('history_delete','".$db->dt[mh_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
					}else{
						$Contents .= "
						<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
					}
					$Contents.="
					</td>
				  </tr>
				  <tr height=70 style='display:none' id='mailling_info_".$db->dt[mh_ix]."'>
					<td colspan=10 style='padding:5px;'>
					팩스 : ".$db->dt[fax]."<br>
					일반전화 : ".$db->dt[phone]."<br>
					핸드폰 : ".$db->dt[mobile]."<br>
					홈페이지 : ".$db->dt[homepage]."<br>
					회사주소 : ".$db->dt[com_address]."<br>
					</td>
				  </tr>	  ";
				  
			}
		}else{
			$Contents .= "
				  <tr bgcolor=#ffffff height=50>
					<td align=center colspan=11>발송된 메일 발송 목록이 없습니다 </td>
				  </tr>  ";
		}

		$Contents .= "
			</table>
			<table width=100%>";
			$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_StrING) ;

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents .= "
				<tr height=50><td colspan=11 ><input type=image src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0></td></tr>";
			}else{
				$Contents .= "
				<tr height=50><td colspan=11 ><a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0></a></td></tr>";
			}

				$Contents .= " 
				<tr height=50><td colspan=11 align=center>".page_bar($total, $page, $max,$query_string,"")."</td></tr>
			</table>
			</form>
			
		  ";

$Contents .= "
	</td>
  </tr>
</table> 
<form name='lyrstat'><input type='hidden' name='opend' value=''></form>";

if($mmode == "personalization"){
	$P = new ManagePopLayOut();
	$P->addScript = "<script language='javascript' src='orders.js'></script>\n".$Script;
	$P->OnloadFunction = "";
	$P->strLeftMenu = campaign_menu();
	$P->Navigation = "메일링/SMS > 메일발송내역";
	$P->title =  "메일발송내역";
	$P->NaviTitle =  "메일발송내역";
	$P->strContents =  $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";
	echo $P->PrintLayOut();
}else if($mmode == "pop"){
    $P = new ManagePopLayOut();
    $P->NaviTitle = "메일발송내역";
	$P->addScript = $Script;
    $P->Navigation = "메일링/SMS > 메일발송내역";
    $P->strContents = $Contents;
	$P->OnloadFunction = "init();";//showSubMenuLayer('storeleft');
    echo $P->PrintLayOut();
}else{
    $P = new LayOut();
    $P->title = "메일발송내역";
	$P->strLeftMenu = campaign_menu();
	$P->addScript = $Script;
    $P->Navigation = "메일링/SMS > 메일발송내역";
    $P->strContents = $Contents;
	$P->OnloadFunction = "init();";//showSubMenuLayer('storeleft');
    echo $P->PrintLayOut();
}
   

?>