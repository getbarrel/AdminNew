<?
include("../class/layout.class");

$db = new Database;

$Script = "
<script language='javascript' src='../js/table_changeorder.js'></script>
<script language='javascript'>
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

		function BoardDelete(bm_ix){
			if(confirm('해당 게시판을 정말로 삭제하시겠습니까? 게시판을 삭제 하시면 관련 데이타 모두가 삭제 됩니다.')){
				document.location.href='board.manage.act.php?act=delete&mmode=$mmode&bm_ix='+bm_ix
			}
		}

		function confirmModify(board_ename){
			if(confirm('게시물 수정은 팝업창에서 진행되지 않습니다. 팝업창을 닫고 부모창에서 게시물 수정을 계속하시겠습니까?')){
				opener.document.location.href='/admin/marketting/bbs_list.php?mmode=$mmode&board_ename='+board_ename;
				self.close();
			}
		}

		</script>";
/*
<tr>
	    <td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 사업자 정보</b></div>")."</td>
	  </tr>
*/
if($div_ix != ""){
	$where = " and bg.div_ix = '".$div_ix."' ";
}
$sql = "select bmc.* , bg.div_name as group_name from bbs_manage_config bmc , bbs_group bg where bmc.board_group = bg.div_ix and disp = 1 and bbs_templet_dir != 'basic_after' $where order by vieworder asc , bmc.board_group asc ";
$db->query($sql);



$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("게시판 목록", "게시판관리 > 게시판 목록 ")."</td>
		</tr>";
$bbs_info = getMyService("BASIC_ADD","BBS");
if($_SESSION["admininfo"][mall_type]  != "O"){
	/*
	if($bbs_info["si_status"] == "SR" && $bbs_info["service_unit_value"] || true){
	
	include ("graph/bbs_storage.graph.php");
	$mstring .="
			<tr>
				<td align='left' colspan=6 >
				<table class='input_table_box' cellpadding=0 cellspacing=0  border=0 style='width:100%;margin-bottom:20px;'>
					<tr>
						<td class='point ctr'>
							<table cellpadding=0 cellspacing=0 width=100% border=0 style='margin:15px 10px;' >
								<tr>
									<td style='border:1px solid silver;width:400px;'>".BbsStorageGraph()."</td>
									<td > 게시판 신청 용량 : <b class=blk>".$bbs_info["service_unit_value"]." ".$bbs_info["service_unit"]." </b>  </td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				</td>
			</tr>";
	}
	*/
}else{
	/*
	if($bbs_info["si_status"] == "SR" && $bbs_info["service_unit_value"] || true){
	
	include ("graph/bbs_storage.graph.php");
	
	$mstring .="
			<tr>
				<td align='left' colspan=6 >
				<table class='input_table_box' cellpadding=0 cellspacing=0  border=0 style='width:100%;margin-bottom:20px;'>
					<tr>
						<td class='point ctr'>
							<table cellpadding=0 cellspacing=0 width=100% border=0 style='margin:15px 10px;' >
								<tr>
									<td style='border:1px solid silver;width:400px;'>".BbsStorageGraph()."</td>
									<td >   </td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				</td>
			</tr>";
	}
	*/
}
$mstring .="
		<tr>
	    <td align='left' colspan=4 style='padding-bottom:15px;'>
	    	<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>";
						$mstring .= "
							<table id='tab_02' ".($_GET["div_ix"] == "" ? "class='on'":"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?div_ix='\">전체보기</td>
								<th class='box_03'></th>
							</tr>
							</table>";
						$bbs_groups = board_group();
						//print_r($bbs_groups);
						//echo count($bbs_grous);
						for($i=0;$i < count($bbs_groups);$i++){
						$mstring .= "
							<table id='tab_".($i+1)."' ".($bbs_groups[$i][div_ix] == $_GET["div_ix"] ? "class='on'":"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?div_ix=".$bbs_groups[$i][div_ix]."'\">".$bbs_groups[$i][div_name]."</td>
								<th class='box_03'></th>
							</tr>
							</table>";
						}
						$mstring .= "
						</td>
						<td style='width:320px;text-align:right;vertical-align:bottom;padding:0 0 10px 0;'>
							<!--총건수 :&nbsp;<b>".$total."</b>-->

						</td>
					</tr>
					</table>
				</div>
	    </td>
	</tr>
		<tr>
			<td>
			<form name=vieworderform method=post action='./board.manage.act.php' target='act'>
			<input type=hidden name='act' value='vieworder_change'>
			<table cellpadding=0 cellspacing=0 width='100%' class='list_table_box' id='changeable_table'>
				<col width='4%'>
				<col width='9%'>
				<col width='*'>
				<col width='10%'>
				<col width='7%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='10%'>
				<col width='8%'>
				<col width='10%'>
				<tr height=27 align=center style='font-weight:600;'>
					<td class='s_td'>NO.</td>
					<td class='m_td'>그룹</td>
					<td class='m_td'>게시판 타이틀</td>
					<td class='m_td'>게시판 이름</td>
					<td class='m_td'>템플릿</td>
					<td class='m_td'>비밀글</td>
					<td class='m_td'>답변</td>
					<td class='m_td'>댓글</td>
					<td class='m_td'>분류 관리</td>
					<td class='m_td'>문의게시판</td>
					<td class='e_td'>관리</td>

				</tr>
				";
if($db->total){
for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
$mstring .="		<tr height=27 align=center onclick=\"spoit(this)\" id='".$db->dt[bm_ix]."' changeable=1 style='cursor:pointer;'>
					<td class='list_box_td' align=center bgcolor=#ffffff>
						".($i+1)."
						<input type=hidden name=sno[] value='".$db->dt[bm_ix]."'>
						<input type=hidden name=sort[".$i."] value='".$db->dt[vieworder]."'> 
					</td>
					<td class='list_box_td point' bgcolor=#efefef nowrap class=small>".$db->dt[group_name]."</td>
					<td class='list_box_td'  align=left style='padding-left:10px;'>";
	if($mmode=="pop"){
$mstring .="		<table cellpadding=0 cellspacing=0 width=100%>
						<tr>
							<td align=left><a onclick=\"javascript:confirmModify('".$db->dt[board_ename]."')\">".$db->dt[board_name]."</a></td>
							<td align=right style='padding-right:10px;'><a onclick=\"javascript:confirmModify('".$db->dt[board_ename]."')\"><img src='../images/".$admininfo["language"]."/btn_contents_view.gif' border=0></a></td>
						</tr>
						</table>";
	}else{
$mstring .="				<!--table cellpadding=0 cellspacing=0 width=100%>
						<tr>
							<td align=left><a href='bbs.php?mode=list&mmode=$mmode&board=".$db->dt[board_ename]."'>".$db->dt[board_name]."</a></td>
							<td align=right style='padding-right:10px;'><a href='bbs.php?mmode=$mmode&board=".$db->dt[board_ename]."'><img src='../images/".$admininfo["language"]."/btn_contents_view.gif' border=0></a></td>
						</tr>
						</table-->
						<dl  style='float:left;width:100%;'>
							<dt style='float:left;position:relative;top:5px;'><a href='bbs.php?mode=list&mmode=$mmode&board=".$db->dt[board_ename]."'>".$db->dt[board_name]."</a></dt>
							<dd style='float:right;'><a href='bbs.php?mmode=$mmode&board=".$db->dt[board_ename]."'><img src='../images/".$admininfo["language"]."/btn_contents_view.gif' border=0></a></dd>
						</dl>
						<div style='clear:both;height:0px;'></div>";
	}

$mstring .="			</td>
					<td class='list_box_td' bgcolor=#efefef>
					".$db->dt[board_ename]."
					</td>
					<td class='list_box_td'  bgcolor=#ffffff>
					".$db->dt[bbs_templet_dir]."
					</td>
					<td class='list_box_td'  bgcolor=#efefef>
					".$db->dt[board_hidden_yn]."
					</td>
					<td class='list_box_td' bgcolor=#ffffff>
					".$db->dt[board_response_yn]."
					</td>
					<td class='list_box_td' bgcolor=#efefef>
					".$db->dt[board_comment_yn]."
					</td>
					<td class='list_box_td' >";
                    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
    					$mstring.="
                        <a href='board_category.php?mmode=$mmode&bm_ix=".$db->dt[bm_ix]."'>".($db->dt[board_category_use_yn] == 'Y' ? "<img src='../images/".$admininfo["language"]."/btn_setup.gif' border=0>":"")."</a>";
                    }else{
                        $mstring.="
                        <a href=\"".$auth_update_msg."\">".($db->dt[board_category_use_yn] == 'Y' ? "<img src='../images/".$admininfo["language"]."/btn_setup.gif' border=0>":"")."</a>";
                    }
                    $mstring.="
					</td>
					<td class='list_box_td' bgcolor=#efefef>
					<a href='board_handling_status.php?mmode=$mmode&bm_ix=".$db->dt[bm_ix]."'>".($db->dt[board_qna_yn] == 'Y' ? "설정":"")."</a>
					</td>
					<td class='list_box_td' align=center style='padding:5px;' nowrap>";
                    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
    					$mstring.="
                        <a href='board.manage.php?mmode=$mmode&bm_ix=".$db->dt[bm_ix]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
    					<a href='board.manage.php?mmode=$mmode&act=copy&bm_ix=".$db->dt[bm_ix]."'><img src='../images/".$admininfo["language"]."/btn_copy.gif' border=0></a> ";
                    }else{
                        $mstring.="
                        <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
    					<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_copy.gif' border=0></a> ";
                    }

if($db->dt[board_default_yn] == "Y"){
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
        $mstring .= "<a href=\"JavaScript:BoardDelete('".$db->dt[bm_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
    }else{
        $mstring .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
    }
}


$mstring .="			</td>
				</tr>";
}
}else{
$mstring .="		<tr height=50 align=center >
					<td class='list_box_td' align=center colspan=11 bgcolor=#ffffff>
						게시판이 존재 하지 않습니다.
					</td>
				</tr>
				";
}

$mstring .="</table>
				<table cellpadding=0 cellspacing=0 width='100%' style='margin-top:5px;'>
				</tr>";
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
                    $mstring.="
    				<tr bgcolor=#ffffff ><td  align=right><a href='board.manage.php'><img src='../images/".$admininfo["language"]."/b_bbsadding.gif' border=0 ></a></td></tr>";
                }else{
                    $mstring.="
    				<tr bgcolor=#ffffff ><td  align=right><a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_bbsadding.gif' border=0 ></a></td></tr>";
                }
            $mstring.="    
			</table>
			<table width='100%'>
					<tr height=50 bgcolor=#ffffff><td colspan=8 align=center><input type=hidden name='change_all' id='change_all' value='1'><!--label for='change_all'>노출순서 재조정</label--> <input type=image src='../image/b_save.gif' border=0 align=absmiddle></td></tr>
					
				</table>
			</td>
		</tr>
		</form>";
$mstring .="</table>";

/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>게시판 추가</b>를 원하시면 게시판 추가버튼을 클릭해주세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>게시판 분류</u> 관리는 원하시면 <b>SETUP</b> 버튼을 클릭하신후 분류를 작성하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >게시물을 수정하시려면 게시판 타이틀을 또는 <b>'게시물확인하기'</b>를 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>일반게시판 그룹</b>은 자동으로 노출되지 않으며 페이지 디자인후 게시판 <u>치환함수</u>를 통해 노출되게 됩니다 게시판 설정 아래 부분에 보시면 게시판 치환함수가 있습니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );

//$help_text = HelpBox("게시판 관리", $help_text);
//$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:4px;'><table><tr><td valign=bottom><b>게시판 관리</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_게시판관리(090322)_config.xml',800,517,'manual_view')\"  title='게시판관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a></td></tr></table></div>", $help_text,170)."</div>";
$help_text =HelpBox("<div style='position:relative;top:-2px;'><table cellpadding='0' cellspacing='0' border='0'><tr><td style='vertical-align:middle;'><b>게시판 관리</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_게시판관리(090322)_config.xml',800,517,'manual_view')\"  title='게시판관리 동영상 메뉴얼입니다' style='cursor:pointer;'><img src='../image/movie_manual.gif' align=abstop></a></td></tr></table></div>", $help_text,170);

$Contents = $mstring.$help_text;


if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = bbsmanage_menu();
	$P->Navigation = "게시판관리 > 게시판 목록";
	$P->title = "게시판 목록";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = bbsmanage_menu();
	$P->Navigation = "게시판관리 > 게시판 목록";
	$P->title = "게시판 목록";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


function board_group()
{
	global $board_group;

	$mdb = new Database;

	$sql = "select div_ix,div_name from bbs_group where disp = '1'";
	$mdb->query($sql);
	$datas = $mdb->fetchall();

	return $datas;
	exit;
	if($mdb->total)
	{
		for($i = 0;$i < $mdb->total;$i++)
		{
			$mdb->fetch($i);

			$mstring .= "<input type=radio name='board_group' id='group_".$mdb->dt["div_ix"]."' value='".$mdb->dt["div_ix"]."' ".($board_group == $mdb->dt["div_ix"] ? "checked":"")." validation='true' title='게시판 그룹'><label for='group_".$mdb->dt["div_ix"]."'>".$mdb->dt["div_name"]."</label>";
		}
	}

	return $mstring;
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
