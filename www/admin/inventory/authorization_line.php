<?
include("../class/layout.class");
//include("inventory.lib.php");

//print_r($admininfo);
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

$db->query("SELECT count(*) as total FROM common_authline_info");
$db->fetch();
$total = $db->dt[total];


$db->query("SELECT * FROM common_authline_info limit $start,$max ");


$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max","");
$db->query("SELECT * FROM common_authline_info order by regdate desc LIMIT $start,$max");
$mstring = "
<table width='100%' cellpadding=0 cellspacing=0>
	<tr>
	    <td align='left' colspan=6 > ".GetTitleNavigation("결제라인 관리", "재고관리 > 기초정보관리 > 결제라인 관리")."</td>
	</tr>
	<tr height=30><td style='padding-bottom:5px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'> <b class=blk> 결제라인 목록  </b></div>")."</td></tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
	<col width=5%>
	<col width='20%'>
	<col width='*'>
	<col width=15%>
	<col width=15%>
	<col width=20%>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td'>번호</td>
		<td class='m_td'>결제라인명</td>
		<td class='m_td'>결제라인종류</td>
		<td class='m_td'>사용여부</td>
		<td class='m_td'>등록일자</td>
		<td class='e_td'>관리</td>
		</tr>";

if($db->total){
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

		if($db->dt[authline_kind] == "b"){
			$authline_kind_str = "기본";
		}else if($db->dt[authline_kind] == "c"){
			$authline_kind_str = "사용자 정의";
		}

		/*
		if($db->dt[option_type] == "9"){
			$option_type_str = "기본옵션";
		}else if($db->dt[option_type] == "1"){
			$option_type_str = "가격추가옵션";
		}
		*/

		$mstring .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'>".($i+1)."</td>
					<td class='list_box_td point'>".$db->dt[authline_name]."</td>
					<td class='list_box_td list_bg_gray'>".$authline_kind_str."</td>
					<td class='list_box_td'>".($db->dt[disp] ? "사용":"사용안함")."</td>
					<td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
					<td class='list_box_td' nowrap>";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			$mstring .="<a href=\"javascript:PoPWindow3('./authorization_line.edit.php?mmode=pop&al_ix=".$db->dt[al_ix]."',980,700,'goods_options_input')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
		}else{
			$mstring .="<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
		}

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$mstring .=" <a href=\"javascript:DeleteAuthLineInfo('".$db->dt[al_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
		}else{
			$mstring .=" <a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
		}
		$mstring .="
					</td>
				</tr>";
	}
	$mstring .=	"</table>";
	$mstring .=	"<table width='100%' cellpadding=0 cellspacing=0>";
}else{
	$mstring .= "
				<tr height=50><td colspan=6 align=center style='padding:30px 0px;'>등록된 결제라인 정보가 없습니다.</td></tr>";
}

$mstring .="</table>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$mstring .= "
	<table cellpadding=1 cellspacing=0 width=100% >
		<tr hegiht=30>
			<td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'><a href=\"javascript:PoPWindow3('./authorization_line.edit.php?mmode=pop',980,700,'authorization_line_edit')\"><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0></a></td>
		</tr>
	</table><br>";
}else{
$mstring .= "
	<table cellpadding=1 cellspacing=0 width=100% >
		<tr hegiht=30>
			<td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'><a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0></a></td>
		</tr>
	</table><br>";
}
$Contents = $mstring;

$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >귀사에서 관리하는 결제라인을 등록 관리하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >자주쓰는 결제라인 정보를 저장하신 후 발주시 선택하여 사용 하실 수 있습니다</td></tr>
</table>
";


$help_text = HelpBox("결제라인 관리", $help_text);
$Contents .= $help_text;

$Script = "<script language='javascript' >

 function DeleteAuthLineInfo(al_ix){
 	if(confirm('해당 임시옵션 정보를 정말로 삭제 하시겠습니까?')){

 	f    = document.createElement('form');
    f.name = 'optionfrm';
    f.id = 'optionfrm';
    f.method    = 'post';
    f.target = 'iframe_act';
    f.action    = 'authorization_line.act.php';

    i0          = document.createElement('input');
    i0.type     = 'hidden';
    i0.name     = 'act';
    i0.id     = 'act';
    i0.value    = 'delete';
    f.insertBefore(i0);

    i1          = document.createElement('input');
    i1.type     = 'hidden';
    i1.name     = 'al_ix';
    i1.id     = 'al_ix';
    i1.value    = al_ix;
    f.insertBefore(i1);

		document.insertBefore(f);
		f.submit();

 	}
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
$P->strLeftMenu = inventory_menu();
$P->Navigation = "재고관리 > 기초정보관리 > 결제라인 관리";
$P->title = "결제라인 관리";
$P->strContents = $Contents;
$P->PrintLayOut();

/*
CREATE TABLE IF NOT EXISTS `common_authline_info` (
  `al_ix` int(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '결제라인 인덱스',
  `authline_name` varchar(100) NOT NULL COMMENT '결제라인명',
  `authline_kind` char(1) NOT NULL COMMENT '결제라인 종류',
  `disp` char(1) NOT NULL DEFAULT '1' COMMENT '사용여부',
  `charger_ix` varchar(100) NOT NULL COMMENT '소유자',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY (`al_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='결제라인 정보'  ;


CREATE TABLE IF NOT EXISTS `common_authline_detail_info` (
  `aldt_ix` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `al_ix` int(6) DEFAULT NULL COMMENT '결제라인 인덱스값',
  `department` int(10) DEFAULT NULL COMMENT '부서',
  `charger_ix` int(10) DEFAULT NULL COMMENT '담당자',
  `charger_name` varchar(255) DEFAULT NULL COMMENT '담당자이름',
  `position` int(10) DEFAULT NULL COMMENT '직급',
  `disp_name` varchar(100) DEFAULT '' COMMENT '표시 이름',
  `insert_yn` enum('Y','N') DEFAULT 'Y' COMMENT '수정시구분값',
  PRIMARY KEY (`aldt_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='결제라인 상세정보'


*/
?>