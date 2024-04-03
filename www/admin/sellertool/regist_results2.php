<?
include("../class/layout.class");
include_once("sellertool.lib.php");

$db = new Database;

$max = 30; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}

$where = "where srr.pid = sp.id ";

if($search_text != ""){
	$where .= "and site_name LIKE '%".trim($search_text)."%' ";
}

if($site_code != ""){
	$where .= "and site_code = '".trim($site_code)."' ";
}

if($disp){ //TODO sellertool_category_linked_relation 에 있는지없는지 에따라 보여지도록 변경해야함
	$where .= " and disp = '".$disp."'";
}

if($link_type != ""){
	$where .= "and type = '".trim($link_type)."' ";
}



$sql = "SELECT count(*) as total FROM sellertool_regist_relation srr , shop_product sp  $where ";



$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6 > ".GetTitleNavigation("제휴가 연동로그", "제휴사연동 > 기본정보 설정 > 제휴가 연동로그 ")."</td>
	  </tr>";

$Contents01 .= " <tr>
			<td colspan=6 >
				<div class='tab' style='width:100%;height:30px;margin:0px 0 0 0;'>
				<table class='s_org_tab' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td class='tab'>
							<table id='tab_01' ".( $link_type == "" ? "class='on'" : "" )." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"location.href='?link_type='\" style='padding-left:20px;padding-right:20px;'>
									전체
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".( $link_type == "regist" ? "class='on'" : "" ).">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"location.href='?link_type=regist'\" style='padding-left:20px;padding-right:20px;'>
									상품등록
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' ".( $link_type == "delivery" ? "class='on'" : "" ).">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"location.href='?link_type=delivery'\" style='padding-left:20px;padding-right:20px;'>
									주문/배송관련
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td class='btn'>
						</td>
					</tr>
					</table>
					</div>
			</td> 
		</tr>
        <tr>
            <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>제휴가 연동로그 검색</b></div>")."</td>
        </tr>
		
    </table>
        <form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' style='display:inline;'>
			<input type=hidden name='link_type' value='".$link_type."'>
            <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='input_table_box'>
                <col width='15%'>
                <col width='35'>
                <col width='15%'>
                <col width='35%'>
                <tr>
                    <td class='input_box_title'>제휴사 선택</td>
                    <td class='input_box_item' >
                    	<table border=0 cellpadding=0 cellspacing=0>
                    		<tr>
                    			<td style='padding-right:5px;'>
                                    ".getSellerToolSiteInfo($site_code)."
                                </td>
                    		</tr>
                    	</table>
                    </td>
                    <td class='input_box_title'> <b>처리상태 : </b></td>
                    <td class='input_box_item'>
                    	<input type=radio name='result_code' id='result_code_Y' value='Y' checked><label for='result_code_Y'>성공</label>
                    	<input type=radio name='result_code' id='result_code_N' value='N' ><label for='result_code_N'>실패</label>
                    </td>
                </tr>
                <tr>
                	<td class='input_box_title'>  검색어  </td>
                	<td class='input_box_item' colspan=3>
                		<table cellpadding=0 cellspacing=0 width=400>
                			<col width='120px'>
							<col width='*'>
							<tr>
                				<td >
                				    <select name='search_type'  style=\"font-size:12px;\">
										<option value='pname'>상품명</option>
										<option value='pcode'>상품코드</option>
										<option value='id'>상품코드(키)</option>
										<option value='id'>제휴사 상품코드</option>
									</select>
                				</td>
								<td >
                				    <input id=search_texts  class='textbox' value='' style=' FONT-SIZE: 12px; WIDTH: 90%; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
                				</td>
                				<td colspan=2 style='padding-left:5px;'>
                                    
                                </td>
                			</tr>
                		</table>
                    </td>
                </tr>        
            </table>
            <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
                <tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:20px 0px;'><input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
            </table>
        </form>";

$Contents02 = "
	<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>제휴사 연동 결과</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
        <col width=9%>
        <col width=7%>
		<col width=7%>
        <col width=*>
        <col width=15%>
        <col width=8%>
        <col width=25%>
		<col width=13%>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='m_td'> 상품코드</td>
		<td class='m_td'> 제휴사</td>
		<td class='m_td'> 연동구분</td>
		<td class='m_td'> 상품명</td>
		<td class='m_td'> 제휴사 상품코드</td>
		<td class='m_td'> 처리상태</td>
		<td class='m_td'> 메세지</td>
        <td class='m_td'> 등록일자</td> 
	  </tr>";

$sql = "select *  from sellertool_log srr , shop_product sp $where limit $start , $max ";
$db->query($sql);
/*

$sql = 	"SELECT bdiv.*, sum(case when bbs_div is NULL then 0 else 1 end) as  group_bbs_cnt , case when depth = 1 then si_ix  else parent_si_ix end as group_order
		FROM ".TBL_BBS_MANAGE_DIV." bdiv left join bbs_".$board_ename." bbs on bdiv.si_ix = bbs.bbs_div where bm_ix = '$bm_ix'
		group by si_ix
		order by group_order asc, depth asc";



//echo $sql;
$db->query($sql);
*/

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);

	if($db->dt[type] == "regist"){
		$type_str = "상품등록";
	}else if($db->dt[type] == "delivery"){
		$type_str = "주문";
	}

	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td point' >
				".$db->dt[pid]."
			</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[site_code]."</td>
			<td class='list_box_td'>".$type_str."</td>
			<td class='list_box_td' style='text-align:left;'>".$db->dt[pname]."</td>
		    <td class='list_box_td list_bg_gray'>".($db->dt[result_pno])."</td>	
			<td class='list_box_td'>".($db->dt[result_code] == "200" ? "성공":"에러")."</td>
		    <td class='list_box_td list_bg_gray' style='padding:10px 5px;text-align:left;line-height:130%;'>".($db->dt[result_msg])."</td>
            <td class='list_box_td' style='padding:5px;'>".$db->dt[regist_date]."</td> 
		    <!--td class='list_box_td'>
		    	<a href='site_add_info_input.php?ssai_ix=".$db->dt[ssai_ix]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
	    		<a href=\"javascript:deleteGroupInfo('delete','".$db->dt[si_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td-->
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=8> 제휴사연동 로그정보가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "<table width=100%>
						<tr>
						<td>".page_bar($total, $page, $max,"&search_text=$search_text&site_code=$site_code&dips=$result_code","")."</td>
						</tr>
				</table>";



$Contents .= "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents .= "<tr><td width='100%'>";
$Contents .= $Contents01."<br>";
$Contents .= "</td></tr>";
$Contents .= "<tr><td>".$ContentsDesc01."</td></tr>";
$Contents .= "<tr height=10><td></td></tr>";


$Contents .= "<tr height=10><td></td></tr>";
$Contents .= "<tr><td>".$Contents02."<br></td></tr>";
$Contents .= "<tr height=30><td></td></tr>";
$Contents .= "</table >";
$Contents .= "</form>";
/*
$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >쇼핑몰통합관리 상품부가정보 관리 설정은 <b>2단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>상품부가정보 관리명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

	$help_text = HelpBox("상품부가정보 관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>
 
 function updateSiteInfo(si_ix,site_name,site_code,site_id,site_pw,site_domain,api_key,depth, vieworder,group_order,result_code){
 	var frm = document.site_form;

 	frm.act.value = 'update';
 	frm.si_ix.value = si_ix;
 	frm.site_name.value = site_name;
	frm.site_code.value = site_code;
	frm.site_id.value = site_id;
	frm.site_pw.value = site_pw;
	frm.site_domain.value = site_domain;
    frm.api_key.value = api_key;
 	frm.vieworder.value = vieworder;
	if(result_code=='1') {
		frm.result_code[0].checked = true;
	} else {
		frm.result_code[1].checked = true;
	}

 	if(depth == '1'){
 		frm.depth[0].checked = true;
		//document.getElementById('parent_si_ix').disabled=true;
		//document.getElementById('parent_si_ix').options[0].selected='selected';
 	}else{
 		frm.depth[1].checked = true;
		//document.getElementById('parent_si_ix').disabled=false;
		//document.getElementById('parent_si_ix').value=group_order;
 	}

}

 function deleteGroupInfo(act, si_ix){
 	if(confirm(language_data['site.php']['C'][language])){//'해당상품부가정보 관리 정보를 정말로 삭제하시겠습니까?'
 		var frm = document.site_form;
 		frm.act.value = act;
 		frm.si_ix.value = si_ix;
 		frm.submit();
 	}
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->Navigation = "제휴사연동 > 제휴가 연동로그";
	$P->title = "제휴가 연동로그";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->Navigation = "제휴사연동 > 제휴가 연동로그";
	$P->title = "제휴가 연동로그";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

CREATE TABLE IF NOT EXISTS `sellertool_site_info` (
  `si_ix` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `site_name` varchar(20) DEFAULT NULL COMMENT '제휴사연동명',
  `site_code` varchar(20) DEFAULT NULL COMMENT '제휴사연동 코드',
  `site_domain` varchar(255) DEFAULT NULL COMMENT '제휴사연동 도메인',
  `site_id` varchar(50) DEFAULT NULL COMMENT '제휴사연동 아이디',
  `site_pw` varchar(255) DEFAULT NULL COMMENT '제휴사연동 비밀번호',  
  `disp` char(1) DEFAULT '1' COMMENT '사용여부',
  `vieworder` int(8) DEFAULT '0' COMMENT '노출순서',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY (`si_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='쇼핑몰(오픈마켓) 정보'

*/
?>