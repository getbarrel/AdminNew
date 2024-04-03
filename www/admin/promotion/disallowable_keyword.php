<?
include("../class/layout.class");

$db = new Database;

$db->query("SELECT * FROM shop_disallowable_config limit 0,1");
$db->fetch();

if($db->total){
	$db->fetch();
	$act = "update";
	$dc_ix = $db->dt[dc_ix];
	$spam_word = $db->dt[spam_word];
	$block_ip = $db->dt[block_ip];
	$disallowable_search_keyword = $db->dt[disallowable_search_keyword];
	$disallowable_search_keyword_use = $db->dt[disallowable_search_keyword_use];

	$disallowable_pname_keyword = $db->dt[disallowable_pname_keyword];
	$disallowable_pname_keyword_use = $db->dt[disallowable_pname_keyword_use];
}else{
	$act = "insert";

	$disallowable_search_keyword = "";
	$disallowable_search_keyword_use = 1;

	$disallowable_pname_keyword = "성인, 포커\n성-인, 성+인, 성☆인, 포-커, 포^커";
	$block_ip = "";
	$disallowable_pname_keyword_use = 1;

}



$mstring ="<form name=bbs_manage_frm action='disallowable_keyword.act.php' onsubmit='return CheckFormValue(this)' method='POST' target='act'>
		<input type=hidden name=act value='$act'>
		<input type=hidden name=dc_ix value='$dc_ix'>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed'>
		<col width='18%'>
		<col width='32%'>
		<col width='18%'>
		<col width='32%'>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("검색창 키워드 관리", "프로모션(마케팅) > 키워드/검색창 관리 > 검색창 키워드 관리 ")."</td>
		</tr>
		<tr>
			<td align='left' colspan=4 style='padding:2px 0 2px 0;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>키워드 검색 불가단어</b></div>")."</td>
		</tr>
		</table>
		<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'>
		<col width='20%'>
		<col width='*'>
		<tr >
			<td class='input_box_title' > 사용유무 </td>
			<td class='input_box_item'  colspan=3 style='padding:5px;'>
				<table cellpadding=0 cellsapcing=0 width=100%>
					<tr height=30>
						<td>
							<input type=radio name='disallowable_search_keyword_use' value='1' id='disallowable_search_keyword_use_1' ".CompareReturnValue("1",$disallowable_search_keyword_use,"checked")."><label for='disallowable_search_keyword_use_1'>사용</label>
							<input type=radio name='disallowable_search_keyword_use' value='0' id='disallowable_search_keyword_use_2' ".CompareReturnValue("0",$disallowable_search_keyword_use,"checked")."><label for='disallowable_search_keyword_use_2'>미사용</label>
						</td>
					</tr> 
				</table
			</td>
		</tr>
		<tr >
			<td  class='input_box_title' > 키워드 검색 불가단어 </td>
			<td class='input_box_item' colspan=3 style='padding:5px;'>
				<table cellpadding=0 cellsapcing=0 width=100%>
					<tr height=30>
						<td style='padding:5px;'>
							<textarea name='disallowable_search_keyword' style='width:95%;height:150px'>".$disallowable_search_keyword."</textarea>
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
		</table><br><br>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed;margin-top:10px;'>
			<col width='18%'>
			<col width='32%'>
			<col width='18%'>
			<col width='32%'> 
			<tr>
				<td align='left' colspan=4 style='padding:2px 0 2px 0;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>상품명/간단설명 등록 불가단어</b></div>")."</td>
			</tr>
			</table>
			<table width='100%' cellpadding=0 cellspacing=0 border='0'  class='input_table_box'>
			<col width='20%'>
			<col width='*'>
			<tr >
				<td class='input_box_title' > 사용유무 </td>
				<td class='input_box_item'  colspan=3 style='padding:5px;'>
					<table cellpadding=0 cellsapcing=0 width=100%>
						<tr height=30>
							<td>
								<input type=radio name='disallowable_pname_keyword_use' value='1' id='disallowable_pname_keyword_use_1' ".CompareReturnValue("1",$disallowable_pname_keyword_use,"checked")."><label for='disallowable_pname_keyword_use_1'>사용</label>
								<input type=radio name='disallowable_pname_keyword_use' value='0' id='disallowable_pname_keyword_use_2' ".CompareReturnValue("0",$disallowable_pname_keyword_use,"checked")."><label for='disallowable_pname_keyword_use_2'>미사용</label>
							</td>
						</tr> 
					</table
				</td>
			</tr>
			<tr >
				<td  class='input_box_title' > 상품명/간단설명 등록 불가단어 </td>
				<td class='input_box_item' colspan=3 style='padding:5px;'>
					<table cellpadding=0 cellsapcing=0 width=100%>
						<tr height=30>
							<td style='padding:5px;'>
								<textarea name='disallowable_pname_keyword' style='width:95%;height:150px'>".$disallowable_pname_keyword."</textarea>
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

$help_text = HelpBox("검색창 키워드 관리", $help_text);

$Contents = $mstring.$help_text."<br><br><br><br>";

//$Script = "<script language='javascript' src='basicinfo.js'></script>";
if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = promotion_menu();
	$P->Navigation = "프로모션(마케팅) > 게시판 키워드/검색창 관리 > 검색창 키워드 관리";
	$P->title = "검색창 키워드 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = promotion_menu();
	$P->Navigation = "프로모션(마케팅) > 게시판 키워드/검색창 관리 > 검색창 키워드 관리";
	$P->title = "검색창 키워드 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

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

CREATE TABLE IF NOT EXISTS `shop_disallowable_config` (
  `dc_ix` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT '검색창 키워드 관리키',
  `disallowable_search_keyword` mediumtext COMMENT '키워드 검색 불가단어',
  `disallowable_search_keyword_use` char(1) NOT NULL DEFAULT '0' COMMENT '키워드 검색 불가단어',
  `disallowable_pname_keyword` mediumtext COMMENT '상품명/간단설명 불가단어',
  `disallowable_pname_keyword_use` char(1) NOT NULL DEFAULT '0' COMMENT '상품명/간단설명 불가단어 사용여부',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일자',
  PRIMARY KEY (`dc_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 comment '검색창 키워드 관리'


*/
?>