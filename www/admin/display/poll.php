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

		</script>";
if($pg_ix != ""){
	$db->query("select *  from shop_poll_group where pg_ix = '$pg_ix' ");
	$db->fetch();
	$g_title = $db->dt[g_title];

}

$mstring ="<form name=poll action='poll.act.php'><input type=hidden name=act value=insert>";
$mstring .="<input type=hidden name=id value=$id>";
$mstring .="<table cellpadding=5 cellspacing=0 width=100% border=0 align=center>
						 <tr>
							<td align='left' colspan=6 > ".GetTitleNavigation("설문관리", "마케팅지원 > 설문관리 ")."</td>
						 </tr>";
$mstring .= "<tr align=center bgcolor=#ffffff>
							<td colspan='3'>
								<table border='0' width='100%' cellspacing='1' cellpadding='0'>
								<tr>
									<td >
										<table border='0' width='100%' cellspacing='1' cellpadding='5' bgcolor='#c0c0c0' class='search_table_box'>
											<tr height=30 bgcolor='#ffffff' >
												<td class='search_box_title' align='left' style='padding-left:10px;' width=15%> 설문 제목</td>
												<td class='search_box_item' width='35%'>&nbsp;".$db->dt[g_title]."</td>
												<td class='search_box_title' align='left' style='padding-left:10px;' width=15%> 관련 상품 </td>
												<td class='search_box_item' width='35%'>&nbsp;".$db->dt[pname]."</td>
											</tr>
											<tr height=30 bgcolor='#ffffff' >
												<td class='search_box_title' align='left' style='padding-left:10px;' width=15%> 설명</td>
												<td class='search_box_item' width='75%' colspan=3>&nbsp;".$db->dt[g_desc]."</td>
											</tr>
											<tr height=25 >
												<td class='search_box_title' align='left' style='padding-left:10px;' width=15%> 설문항목</td>
												<td class='search_box_item' width='75%' colspan=3 style='padding:10 10 10 10'>&nbsp;
												<a href=\"javascript:PopSWindow('poll_write.php?mmode=pop&pg_ix=".$pg_ix."',900,600,'cupon_detail_pop');\"  class=blue>
												설문 문제 추가하기
												</a>
												";
$mstring .="<br>
							<table cellpadding=0 cellspacing=0 border=0 width='100%' >
							<col width=70 align=center>
							<col width=* align=center>
							<col width=120 align=center>
							<col width=120 align=center>
";
//$mstring .="<tr bgcolor=#efefef ><td class='s_td'>번호</td><td class='m_td'>질문제목</td><td class='m_td'>항목수</td><td class='e_td'>관리</td></tr>";

$db->query("SELECT * FROM ".TBL_SHOP_POLL_TITLE." where pg_ix = '$pg_ix' order by pt_ix asc");

if($db->total > 0){
	$dbm = new Database;
	for($i=0;$i<$db->total;$i++){
	$db->fetch($i);


	$mstring .="<tr height=30  >
							<td ><b>[질문 ".($i+1)."]</b></td>
							<td  style='padding:0 0 0 0' align=left><b>".$db->dt[title]."</b></td>
							<td>
							<a href=\"javascript:PopSWindow('poll_write.php?mmode=pop&pg_ix=".$pg_ix."&pt_ix=".$db->dt[pt_ix]."',900,600,'cupon_detail_pop');\"  class=blue><img src='/admin/image/btc_modify.gif' border=0></a>
							<a href=\"javascript:if(confirm(language_data['poll.php']['A'][language])){document.location.href='poll.act.php?act=delete&pg_ix=".$pg_ix."&pt_ix=".$db->dt[pt_ix]."&id=$id'}\"><img src='/admin/image/btc_del.gif' border=0></a></td>
							</tr>";
//	$mstring .="<tr height=100 id='TG_VIEW_".$db->dt[id]."' style='display: block;'>";

$dbm->query("SELECT * FROM ".TBL_SHOP_POLL_FIELD." where pt_ix = '".$db->dt[pt_ix]."'  order by fieldnumber");


	for($j=0;$j < $dbm->total ;$j++){
		$dbm->fetch($j);

			$mstring .= "<tr height=30>
										<td>".($j+1)."</td>
										<td align=left>".$dbm->dt[fielddesc]." (".$dbm->dt[result].")</td>
										<td  valign=top style='padding-left:10px;' ></td>
										</tr>";

	}

	$mstring .="<tr height=10><td colspan=4 ></td></tr>
							<tr height=1><td colspan=4 background='/admin/image/dot.gif'></td></tr>
							<tr height=10><td colspan=4 ></td></tr>";
	}

}else{
	$mstring .="<tr height=100 ><td colspan=5 align=center>등록된 질문이 없습니다.</td></tr>";
}
	$mstring .="</table>

												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>

							</td>
						</tr>";
$mstring .="</table>";

//$mstring = ShadowBox($mstring);


$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >설문문제 추가히기를 클릭하셔서 문제를 추가하실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 문제는 수정버튼을 통해서 수정하실 수 있습니다.</td></tr>

</table>
";


//$help_text = HelpBox("게시판 관리", $help_text);
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:4px;'><table><tr><td valign=bottom><b>설문 관리</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_메일SMS리스트관리(090322)_config.xml',800,517,'manual_view')\"  title='메일/SMS 관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a></td></tr></table></div>", $help_text,200)."</div>";
	$mstring .= "<table border=0 cellpadding=0 width=100%>";
	$mstring .= "<tr height='50'><td colspan='5' align='right'>$help_text</td></tr>";
	$mstring .="</table>
	<form name='lyrstat'>
			<input type='hidden' name='opend' value=''>
		</form>
	";
$Contents = $mstring;

//$Script = "<script language='javascript' src='basicinfo.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "마케팅지원 > 설문리스트 > 질문항목추가";
$P->title = "질문항목추가";
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();


function FieldInsert($pollnumber, $fieldnumber, $disp){
global $id;
$dbm = new Database;
$dbm->query("SELECT * FROM ".TBL_SHOP_POLL_FIELD." where number = '$pollnumber'  order by fieldnumber");
if($dbm->total > 0){
	$actstring = "fieldupdate";
	$submitstring = "수정하기";
}else{
	$actstring = "fieldinsert";
	$submitstring = "저장하기";
}


	$mstring = "<table cellapdding=0 cellspaicng=0 width=70% border=0 >
								<form name='field$pollnumber' action='poll.act.php' style='display:inline;'>
							<input type=hidden name=pollnumber value='$pollnumber'>
							<input type=hidden name=act value='$actstring'>
							<input type=hidden name=fieldsize value='$fieldnumber'>
							<input type=hidden name=id value='$id'>";
	for($i=0;$i<$fieldnumber;$i++){
		$dbm->fetch($i);
		if($i==0){
			$mstring .= "<tr>
										<td>".($i+1)."</td>
										<td><input type=text name=fielddesc".($i)." size=60 value='".$dbm->dt[fielddesc]."'> (".$dbm->dt[result].")</td>
										<td  valign=top style='padding-left:10px;' >표시 : <input type='checkbox' name='disp' style='border:1px solid #ffffff' value=1 ".($disp==1 ? "checked":"")."> <input type=image src='/admin/image/btc_modify.gif' border=0></td>
										</tr>";
		}else{
			$mstring .= "<tr>
											<td>".($i+1)."</td>
											<td><input type=text name=fielddesc".($i)." size=60 value='".$dbm->dt[fielddesc]."'> (".$dbm->dt[result].")</td>
									</tr>";
		}
	}
	$mstring .= "</form></table>";

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