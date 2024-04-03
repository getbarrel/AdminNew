<?php
include ("../class/layout.class");
include_once ("sellertool.lib.php");

$db = new MySQL ();

$max = 30; // 페이지당 갯수

$sql = "SELECT
			* 
		FROM 
			sellertool_site_info
		WHERE
			site_code = '" . $site_code . "'";
			
$db->query ( $sql );
$info = $db->fetch ();
$api_key = $info['api_key'];

if ($page == '') {
	$start = 0;
	$page = 1;
} else {
	$start = ($page - 1) * $max;
}

$where = "WHERE api_key is not null ";

if ( ! empty($search_text)) {
	$where .= "AND site_name LIKE '%" . trim ( $search_text ) . "%'  ";
}

if ( ! empty($api_key)) {
	$where .= "AND api_key = '".$api_key."' ";
}

if ( ! empty($disp)) { 
	$where .= " AND disp = '" . $disp . "' ";
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6 > " . GetTitleNavigation ( "상품등록 옵션 관리", "제휴사연동 > 기본정보 설정 > 상품등록 옵션 관리 " ) . "</td>
	  </tr>";

$Contents01 .= " 
        <tr>
            <td align='left' colspan=4 style='padding:3px 0px;'> " . colorCirCleBox ( "#efefef", "100%", "<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>상품부가정보 검색</b></div>" ) . "</td>
        </tr>
    </table>
        <form name='search_form' method='get' action='" . $HTTP_URL . "' onsubmit='return CheckFormValue(this);' style='display:inline;'>
            <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='input_table_box'>
                <col width='15%'>
                <col width='35'>
                <col width='15%'>
                <col width='35%'>
                <tr>
                    <td class='input_box_title'>제휴사연동 선택</td>
                    <td class='input_box_item' >
                    	<table border=0 cellpadding=0 cellspacing=0>
                    		<tr>
                    			<td style='padding-right:5px;'>
                                    " . getSellerToolSiteInfo ( $site_code ) . "
                                </td>
                    		</tr>
                    	</table>
                    </td>
                    <td class='input_box_title'> <b>사용유무 : </b></td>
                    <td class='input_box_item'>
                    	<input type=radio name='disp' id='disp_Y' value='Y' checked><label for='disp_Y'>사용</label>
                    	<input type=radio name='disp' id='disp_N' value='N' ><label for='disp_N'>사용하지않음</label>
                    </td>
                </tr>
                <tr>
                	<td class='input_box_title'>  검색어  </td>
                	<td class='input_box_item' colspan=3>
                		<table cellpadding=0 cellspacing=0 width=100%>
                			<tr>
                				<td >
                				    <input id=search_texts  class='textbox' value='' style=' FONT-SIZE: 12px; WIDTH: 90%; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
                				</td>
                				<td colspan=2 style='padding-left:5px;'>
                                    <span class='p11 ls1'></span>
                                </td>
                			</tr>
                		</table>
                    </td>
                </tr>        
            </table>
            <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
                <tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:20px 0px;'><input type='image' src='../images/" . $admininfo ["language"] . "/bt_search.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
            </table>
        </form>";

$Contents02 = "
	<div style='width:100%;'>" . colorCirCleBox ( "#efefef", "100%", "<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>상품부가정보 목록</b></div>" ) . "</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
        <col width=10%>
        <col width=10%>
        <col width=30%>
        <col width=15%>
        <col width=13%>
        <col width=13%>
        <col width=15%>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='m_td'> 번호</td>
		<td class='m_td'> 제휴사</td>
		<td class='m_td'> 옵션이름</td>
		<td class='m_td'> 사용유무</td>
        <td class='m_td'> 등록일자</td>
        <td class='m_td'> 수정일자</td>
	    <td class='m_td'> 관리</td>
	  </tr>";

$sql = "SELECT * FROM sellertool_add_info $where";
$db->query ( $sql );

if ($db->total) {
	$add_info = $db->fetchAll ( 'array', MYSQL_ASSOC );
	
	$site_info = null;
	
	foreach ( $add_info as $ai ) :
		$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td point' style='padding-left:20px;'>
				" . $ai['add_info_id'] . "
			</td>
			<td class='list_box_td list_bg_gray'>" . $ai ['site_name'] . "</td>
			<td class='list_box_td'>" . $ai ['add_info_name'] . "</td>
		    <td class='list_box_td list_bg_gray'>" . ($ai ['disp'] == "Y" ? "사용" : "사용하지않음") . "</td>		    
            <td class='list_box_td' style='padding:5px;'>" . $ai ['reg_date'] . "</td>
            <td class='list_box_td list_bg_gray' style='padding:5px;'>" . $ai ['update_date'] . "</td>
		    <td class='list_box_td'>";
		if (checkMenuAuth ( md5 ( $_SERVER ["PHP_SELF"] ), "U" )) {
			$Contents02 .= "
		    	<a href='site_add_info_input.php?api_key=".$ai['api_key']."&add_info_id=" . $ai['add_info_id'] . "'><img src='../images/" . $admininfo ["language"] . "/btc_modify.gif' border=0></a>";
		} else {
			$Contents02 .= "
		    	<a href=\"" . $auth_update_msg . "\"><img src='../images/" . $admininfo ["language"] . "/btc_modify.gif' border=0></a>";
		}
		if (checkMenuAuth ( md5 ( $_SERVER ["PHP_SELF"] ), "D" )) {
			$Contents02 .= "
	    		<a href=\"javascript:deleteGroupInfo('" . $ai['add_info_id'] . "')\"><img src='../images/" . $admininfo ["language"] . "/btc_del.gif' border=0></a>";
		} else {
			$Contents02 .= "    
                <a href=\"" . $auth_delete_msg . "\"><img src='../images/" . $admininfo ["language"] . "/btc_del.gif' border=0></a>";
		}
		$Contents02 .= "
		    </td>
		  </tr> ";
	endforeach
	;
} else {
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=7>등록된 쇼핑몰통합관리 제휴사연동 정보가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "<table width=100%>
						<tr>
						<td>" . page_bar ( $total, $page, $max, "&search_text=$search_text&site_code=$site_code&dips=$disp", "" ) . "</td>
						</tr>
				</table>";

$Contents .= "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents .= "<tr><td width='100%'>";
$Contents .= $Contents01 . "<br>";
$Contents .= "</td></tr>";
$Contents .= "<tr><td>" . $ContentsDesc01 . "</td></tr>";
$Contents .= "<tr height=10><td></td></tr>";

$Contents .= "<tr height=10><td></td></tr>";
$Contents .= "<tr><td>" . $Contents02 . "<br></td></tr>";
$Contents .= "<tr height=30><td></td></tr>";
$Contents .= "</table >";
$Contents .= "</form>";
/*
 * $help_text = " <table cellpadding=1 cellspacing=0 class='small' > <col width=8> <col width=*> <tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >쇼핑몰통합관리 상품부가정보 관리 설정은 <b>2단계</b>까지 가능합니다</td></tr> <tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>상품부가정보 관리명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr> </table> ";
 */
$help_text = getTransDiscription ( md5 ( $_SERVER ["PHP_SELF"] ), 'D' );

$help_text = HelpBox ( "상품부가정보 관리", $help_text );
$Contents = $Contents . $help_text;

$Script = "
 <script language='javascript'>
 
 function updateSiteInfo(si_ix,site_name,site_code,site_id,site_pw,site_domain,api_key,depth, vieworder,group_order,disp){
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
	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
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

 function deleteGroupInfo(add_info_id){
        var select = confirm('삭제하시겠습니까?');
        if(select){
            $.ajax({
                type:'POST',
                data: {'act': 'delete','add_info_id': add_info_id},
                url:'site_add_info.act.php',
                dataType: 'html',
                error: function(data,error){// 실패시 실행
    		        alert(error);},
                success: function(transport){
                    if(transport == 'SUCCESS'){
                        alert('삭제되었습니다'),
                        location.reload();
                    }
                }
            });
        }
    }   
 </script>
 ";

if ($mmode == "pop") {
	$P = new ManagePopLayOut ();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu ();
	$P->Navigation = "셀러툴 > 상품등록 옵션 관리";
	$P->title = "상품등록 옵션 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut ();
} else {
	$P = new LayOut ();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu ();
	$P->Navigation = "셀러툴 > 상품등록 옵션 관리";
	$P->title = "상품등록 옵션 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut ();
}