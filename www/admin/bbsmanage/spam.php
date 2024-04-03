<?
include("../class/layout.class");

$db = new Database;

$db->query("SELECT * FROM bbs_spam_config limit 0,1");
$db->fetch();

if($db->total){
	$db->fetch();
	$act = "update";
	$sc_ix = $db->dt[sc_ix];
	$spam_word = $db->dt[spam_word];
	$block_ip = $db->dt[block_ip];
	$spam_usable = $db->dt[spam_usable];
}else{
	$act = "insert";
	$spam_word = "성인, 포커\n성-인, 성+인, 성☆인, 포-커, 포^커";
	$block_ip = "";
	$spam_usable = 1;

}



$mstring ="<form name=bbs_manage_frm action='spam.act.php' onsubmit='return CheckFormValue(this)'>
		<input type=hidden name=act value='$act'>
		<input type=hidden name=sc_ix value='$sc_ix'>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed'>
		<col width='18%'>
		<col width='32%'>
		<col width='18%'>
		<col width='32%'>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("스팸관리", "게시판관리 > 스팸관리 ")."</td>
		</tr>
		<tr>
			<td align='left' colspan=4 style='padding:2px 0 2px 0;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle><b> 스팸단어 관리</b></div>")."</td>
		</tr>
		</table>
		<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='input_table_box'>
		<col width='20%'>
		<col width='*'>
		<tr >
			<td  class='input_box_title' > 스팸단어 </td>
			<td class='input_box_item' colspan=3 style='padding:5px;'>
				<table cellpadding=0 cellsapcing=0 width=100%>
					<tr height=30>
						<td>
							<textarea name='spam_word' style='width:95%;height:150'>".$spam_word."</textarea>
						</td>
					</tr>
					<tr height=25>
						<td >
							<span class='small' style='line-height:120%;'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
						</td>
					</tr>
				</table>

			</td>
		</tr>
		<tr >
			<td class='input_box_title' > 차단 IP 목록 </td>
			<td class='input_box_item' colspan=3 style='padding:5px;'>
				<table cellpadding=0 cellsapcing=0 width=100%>
					<tr height=30>
						<td>
							<textarea name='block_ip' style='width:95%;height:150'>".$block_ip."</textarea>
						</td>
					</tr>
					<tr height=25>
						<td >
							<span class='small'  style='line-height:120%;'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr >
			<td class='input_box_title' > 스팸적용여부 </td>
			<td class='input_box_item'  colspan=3 style='padding:5px;'>
				<table cellpadding=0 cellsapcing=0 width=100%>
					<tr height=30>
						<td>
							<input type=radio name='spam_usable' value='1' id='spam_usable_1' ".CompareReturnValue("1",$spam_usable,"checked")."><label for='spam_usable_1'>적용</label>
							<input type=radio name='spam_usable' value='0' id='spam_usable_2' ".CompareReturnValue("0",$spam_usable,"checked")."><label for='spam_usable_2'>적용하지 않음</label>
						</td>
					</tr>
					<tr height=25>
						<td >
							<span class='small' ><!--개별 게시판 설정에 관계없이 전체 게시판에 적용됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')." </span>
						</td>
					</tr>
				</table>

			</td>
		</tr>
		</table>
		<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
			<col width='20%'>
			<col width='*'>
			<tr bgcolor=#ffffff >
                <td colspan=4 align=right style='padding:10px 0px;'>";
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                    $mstring .="
                    <input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' >";
                }else{
                    $mstring .="
                    <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' align=absmiddle  border=0 style='cursor:hand;border:0px;' ></a>";
                }
                $mstring .="
                    <img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0  align=absmiddle style='cursor:hand;border:0px;' onclick='history.back();'>
                </td>
            </tr>
			</form>
		</table>";
/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>스팸단어 필터링을 이용한 방법</b></td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>IP를 이용해 차단 하는 방법</b></td></tr>
	<tr><td valign=top></td><td class='small' ><img src='/admin/image/icon_list.gif' align=absmiddle>첫째로 IP주소 목록을 만들어 차단하는 방법이다.</td></tr>
	<tr><td valign=top></td><td class='small' ><img src='/admin/image/icon_list.gif' align=absmiddle>둘째로 IP대역폭을 조사하여 해당 국가 전체를 차단한다.</td></tr>
	<tr><td valign=top></td><td class='small' ><img src='/admin/image/icon_list.gif' align=absmiddle>셋째로 IP사용 기관 담당자에게 연락하여 올리지 못하도록 하는 방법이다. </td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$help_text = HelpBox("스팸관리", $help_text);

$Contents = $mstring.$help_text."<br><br><br><br>";

//$Script = "<script language='javascript' src='basicinfo.js'></script>";
if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = bbsmanage_menu();
	$P->Navigation = "게시판관리 > 게시판 스팸관리";
	$P->title = "게시판 스팸관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = bbsmanage_menu();
	$P->Navigation = "게시판관리 > 게시판 스팸관리";
	$P->title = "게시판 스팸관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}

function makeSelectBox($mdb,$select_name,$gp_level){
	$mdb->query("SELECT * FROM ".TBL_SHOP_GROUPINFO." where disp=1 order by gp_level asc ");

	$mstring = "<select name='$select_name' class=small style='width:100px;'>";
	$mstring .= "<option value='0'>전체보기</option>";
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[gp_level]."' ".($mdb->dt[gp_level] == $gp_level ? "selected":"").">".$mdb->dt[gp_name]."  (레벨 : ".$mdb->dt[gp_level].")</option>";
		}
	}else{
		$mstring .= "<option value=''>".$msg."</option>";
	}
	$mstring .= "</select>";

	return $mstring;
}

function FieldInsert($pollnumber, $fieldnumber, $disp){
$dbm = new Database;
$dbm->query("SELECT * FROM ".TBL_SHOP_POLL_FIELD." where number = '$pollnumber' order by fieldnumber");
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



/*
alter table bbs_manage_config add board_titlemax_cnt int(3) default 20 after board_max_cnt; -- 제목글자수 제한
alter table bbs_manage_config add design_width varchar(10) default '100%' after board_titlemax_cnt; -- 게시판 넓이
alter table bbs_manage_config add design_new_priod int(3) default 24 after design_width; -- NEW 아이콘 효력 시간
alter table bbs_manage_config add design_hot_limit int(3) default 50 after design_new_priod; -- HOT 아이콘 제한
alter table bbs_manage_config add board_searchable enum('0','1') default '1' after design_hot_limit; -- 통합검색 노출여부
alter table bbs_manage_config add board_ip_viewable enum('0','1') default '1' after board_searchable; -- IP 노출여부
alter table bbs_manage_config add board_ip_encoding enum('0','1') default '1' after board_ip_viewable; -- IP 암호화 여부
alter table bbs_manage_config add board_group enum('H','C','G') default 'H' after board_ip_encoding; -- 게시판 그룹 H (help):  고객센타 , C(community) : 커뮤니티, G(general) : 일반게시판
alter table bbs_manage_config add board_list_auth int(2) default '1' after board_group; -- 리스트 보기 사용자 권한
alter table bbs_manage_config add board_read_auth int(2) default '1' after board_list_auth ; -- 읽기 사용자 권한
alter table bbs_manage_config add board_comment_auth int(2) default '1' after board_read_auth  ; -- 콤멘트 쓰기 사용자 권한
alter table bbs_manage_config add board_write_auth int(2) default '1' after board_comment_auth ; -- 쓰기 사용자권한

alter table bbs_manage_config add view_check_yn enum('0','1') default '1' after board_write_auth ;  -- 리스트에  체크박스 노출여부
alter table bbs_manage_config add view_no_yn enum('0','1') default '1' after view_check_yn ;  -- 리스트에 넘버 노출여부
alter table bbs_manage_config add view_title_yn enum('0','1') default '1' after view_no_yn ;   -- 리스트에 제목 노출여부
alter table bbs_manage_config add view_name_yn enum('0','1') default '1' after view_title_yn  ;  -- 리스트에 이름 노출여부
alter table bbs_manage_config add view_file_yn enum('0','1') default '1' after view_name_yn ;   -- 리스트에 파일 노출여부
alter table bbs_manage_config add view_date_yn enum('0','1') default '1' after view_file_yn  ;   -- 리스트에 날짜 노출여부
alter table bbs_manage_config add view_viewcnt_yn enum('0','1') default '1' after view_date_yn;   -- 리스트에 조회수 노출여부

alter table bbs_manage_config add image_click enum('V','P','LP') default 'V' after view_viewcnt_yn;  -- 이미지 클릭시 액션 여부 V : 읽기 페이지로 이동, P :  팝업 , LP : 레이어 팝업
alter table bbs_manage_config add break_autowrite enum('0','1') default '0' after image_click;   -- 자동글쓰기 방지 기능
alter table bbs_manage_config add break_autocomment enum('0','1') default '0' after break_autowrite;  -- 자동 컴멘트 달기 방지 기능

*/

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