<?
include("../class/layout.class");


$db = new Database;

$Script = "	<script language='javascript'>


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

		function mailDelete(mc_ix){
			if(confirm('해당 메일 목록을 정말로 삭제하시겠습니까? 메일 목록을  삭제 하시면 관련 데이타 모두가 삭제 됩니다.')){
				document.location.href='mail.manage.act.php?act=delete&mc_ix='+mc_ix;
			}
		}

		</script>";

$db->query("SELECT * FROM shop_mailsend_config where mc_ix != 0008");

$mstring ="<form name=poll action='board.manage.act.php'><input type=hidden name=act value=insert>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("메일/SMS 목록", "마케팅지원 > 메일/SMS 목록 <a onClick=\"PoPWindow('/admin/_manual/manual.php?config=".urlencode("몰스토리동영상메뉴얼_메일SMS리스트관리(090322)_config.xml")."',800,517,'manual_view')\"  title='메일/SMS 관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a>")."</td>
		</tr>
		<!--tr>
			<td align='left' colspan=4 style='padding-bottom:10px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 메일/SMS 목록 관리</b></div>")."</td>
		</tr-->
		<tr>
			<td>
			<table cellpadding=3 cellspacing=0 width='100%'>
				<tr align=center>
					<td height=27 width='5%' class='s_td small' nowrap> NO.</td>
					<td width='40%' class='m_td small' nowrap>메일/SMS 발송 관리목록 </td>
					<!--td width='35%' class='m_td'>메일발송제목 </td-->
					<td width='11%' class='m_td small' nowrap>관리자 발송(메일) </td>
					<td width='11%' class='m_td small' nowrap>사용자 발송(메일) </td>
					<td width='11%' class='m_td small' nowrap>관리자 발송(SMS) </td>
					<td width='11%' class='m_td small' nowrap>사용자 발송(SMS) </td>
					<td width='11%' class='e_td small' nowrap>관리 </td>
				</tr>
				";
if($db->total){
for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
$mstring .="		<tr align=center>
					<td height=27 align=center bgcolor=#efefef>
						".($i+1)."
					</td>
					<td align=left style='padding-left:20px;'>
						<a href='mail.manage.php?mc_ix=".$db->dt[mc_ix]."'>".$db->dt[mc_title]."</a>
					</td>
					<!--td bgcolor=#efefef align=left style='padding-left:5px;'>
					".$db->dt[mc_mail_title]."
					</td-->
					<td  bgcolor=#efefef>
					".$db->dt[mc_mail_adminsend_yn]."
					</td>
					<td bgcolor=#ffffff>
					".$db->dt[mc_mail_usersend_yn]."
					</td>
					<td  bgcolor=#efefef>
					".$db->dt[mc_sms_adminsend_yn]."
					</td>
					<td bgcolor=#ffffff>
					".$db->dt[mc_sms_usersend_yn]."
					</td>
					<td  bgcolor=#efefef align=left style='padding-left:10px;' nowrap>
					<a href='mail.manage.php?mc_ix=".$db->dt[mc_ix]."'><img src='../image/btc_modify.gif' border=0></a> ";


$mstring .="			<a href=\"JavaScript:mailDelete('".$db->dt[mc_ix]."')\"><img src='../image/btc_del.gif' border=0></a>";



$mstring .="			</td>
				</tr>
				<tr hegiht=1><td colspan=8 class='dot-x'></td></tr>
				<tr >";
}
}else{
$mstring .="		<tr height=50 align=center >
					<td align=center colspan=9 bgcolor=#ffffff>
						게시판이 존재 하지 않습니다.
					</td>
				</tr>
				<tr hegiht=1><td colspan=8 class='dot-x'></td></tr>
				";
}

$mstring .="
				</tr>
			</table>
			</td>
		</tr>
		</form>";
$mstring .="</table>";
/*
//colorCirCleBox("#efefef",660,"<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> 영업정보</div>")
$mstring .="<tr align=center bgcolor=#ffffff><td width=400><input type=text name=title size=60></td><td width=100>".SelectFieldNumber($selectfield)."</td><td width=100><input type=submit value='save'></td></tr>";
$mstring .="<tr height=40><td align=left colspan=3>";
$mstring .= "<img src='../image/emo_3_15.gif' align=absmiddle> 설문항목과 문항수를 입력해주세요";
$mstring .="</td></tr></form>";
$mstring .="</table>";
*/
//$mstring = ShadowBox($mstring);

$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >위 항목은 자동메일링 , 자동 SMS 발송에 관련된 메일 목록 입니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기 정의된 목록에서 수정하시고자 하는 항목의 수정버튼을 클릭합니다</td></tr>

</table>
";


//$help_text = HelpBox("게시판 관리", $help_text);
$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td valign=top><b>메일/SMS 관리</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_메일SMS리스트관리(090322)_config.xml',800,517,'manual_view')\"  title='메일/SMS 관리 동영상 메뉴얼입니다' style='cursor:pointer;'><img src='../image/movie_manual.gif' align=absmiddle style='position:absolute;top:-1px;' borer='0'></a></td></tr></table>", $help_text,200);

$Contents = $mstring.$help_text;

//$Contents = $mstring;

//$Script = "<script language='javascript' src='basicinfo.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = marketting_menu();
$P->Navigation = "HOME > 마케팅지원 > 메일/SMS 관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();


function FieldInsert($pollnumber, $fieldnumber, $disp){
$dbm = new Database;
$dbm->query("SELECT * FROM shop_poll_field where number = '$pollnumber' order by fieldnumber");
if($dbm->total > 0){
	$actstring = "fieldupdate";
	$submitstring = "수정하기";
}else{
	$actstring = "fieldinsert";
	$submitstring = "저장하기";
}

	$mstring = "<div id='TG_VIEW_".$pollnumber."' style='position: relative; display: none;'>";
	$mstring .="<form name='field$pollnumber' action='poll.act.php'><input type=hidden name=pollnumber value=$pollnumber><input type=hidden name=act value=$actstring><input type=hidden name=fieldsize value='$fieldnumber'>";
	$mstring .= "<table cellapdding=0 cellspaicng=0>";
	for($i=0;$i<$fieldnumber;$i++){
		$dbm->fetch($i);
		if($i==0){
			$mstring .= "<tr><td>".($i+1)."</td><td><input type=text name=fielddesc".($i)." size=40 value='".$dbm->dt[fielddesc]."'></td><td  valign=top style='padding-left:10px;' rowspan=10>표시 : <input type='checkbox' name='disp' style='border:1px solid #ffffff' value=1 ".($disp==1 ? "checked":"")."> &nbsp;&nbsp;&nbsp;&nbsp;<input type=submit value='$submitstring'></td></tr>";
		}else{
			$mstring .= "<tr><td>".($i+1)."</td><td><input type=text name=fielddesc".($i)." size=40 value='".$dbm->dt[fielddesc]."'></td></tr>";
		}
	}
	$mstring .= "</table></form></div>";

	return $mstring;

}


function SelectFieldNumber($selectfield)
{
	$divname = array ("1","2","3","4","5","6","7","8","9");

	$pos = 0;
	$strDiv = "<Select name='fieldnum'>\n";
	$strDiv = $strDiv."<option value=0>항목수</option>\n";
	while(hasMoreElements(&$divname))
	{
	       	if( $pos == $selectdiv )
	       	{
	        	$strDiv = $strDiv."<option value='".($pos+1)."' Selected>".$divname[$pos]."</option>\n";
	       	}else{
	       		$strDiv = $strDiv."<option value='".($pos+1)."'>".$divname[$pos]."</option>\n";
		}
	       	$pos++;
	}

	$strDiv = $strDiv."</Select>\n";

	return $strDiv;

}



/*

create table companyinfo (
company_id varchar(32) not null ,
company_name varchar(50) null default null,
business_number varchar(40) null default null,
business_kind varchar(40) null default null,
ceo varchar(20) null default null,
business_item varchar(50) null default null,
company_address varchar(200) null default null,
bank_owner varchar(20) null default null,
bank_name varchar(20) null default null,
bank_number varchar(30) null default null,
business_day datetime null default null,
admin_id varchar(20) null default null,
admin_pass varchar(32) null default null,
phone varchar(20) null default null,
fax varchar(20) null default null,
charger varchar(20) null default null,
charger_email varchar(20) null default null,
homepage varchar(50) null default null,
shipping_company varchar(30) null default null,
primary key(company_id));
*/
?>