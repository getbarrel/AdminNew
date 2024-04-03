<?
include("../class/layout.class");
include_once("../openapi/openapi.lib.php");
include_once("sellertool.lib.php");

$db = new Database;

if(!empty($_POST['site_code'])){
    $site_code = $_POST['site_code'];
}else{    
	$sql = "select cr.*  from sellertool_site_info cr  order by vieworder asc limit 1";
	$db->query($sql);
	if($db->total){
		$db->fetch();
		$site_code = $db->dt[site_code];
	}else{
		$site_code = '';
	}
}

$list = getOutAddressInfoList($site_code);

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6 > ".GetTitleNavigation("출고지 관리", "제휴사연동 > 출고지 관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>출고지 추가/수정</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='15%'>
	  <col width='35'>
	  <col width='15%'>
	  <col width='35%'>";
if(false){
$Contents01 .= "
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b> 제휴사연동 구분 : </b></td>
	    <td class='input_box_item' colspan=3>
	    	<input type=radio name='depth' value='1' id='depth_1' onclick=\"document.getElementById('parent_si_ix').disabled=true;\" checked><label for='depth_1'>1차 구분 </label>
	    	<input type=radio name='depth' value='2' id='depth_2' onclick=\"document.getElementById('parent_si_ix').disabled=false;\" ><label for='depth_2'>2차 구분 </label>
	    	".getFirstDIV()." <span class='small'><!--2차 제휴사 관리 등록하기 위해서는 반드시 1차제휴사 관리를 선택하셔야 합니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
	    </td>
	  </tr>";
}

$Contents01 .= "
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>제휴사 명 :</b> </td>
		<td class='input_box_item' colspan='3'>
        ".getSellerToolSiteInfo($site_code)."
        </td>
	    
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>주소명 :</b> </td>
		<td class='input_box_item'>
            <input type=text class='textbox' name='addrNm' id='addrNm' value='' style='width:230px;'>
            <input type=hidden name='addrSeq' id='addrSeq' value=''>
            <input type=hidden name='memNo' id='memNo' value=''> 
        </td>
	    <td class='input_box_title'> <b>이름 : </b></td>
	    <td class='input_box_item'>
			<input type=text class='textbox' name='rcvrNm' id='rcvrNm' value='' style='width:230px;'>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>일반전화번호 :</b> </td>
		<td class='input_box_item'><input type=text class='textbox' name='gnrlTlphnNo' id='gnrlTlphnNo' value='' style='width:230px;'> <span class=small></span></td>
	    <td class='input_box_title'> <b>휴대전화번호 :</b> </td>
		<td class='input_box_item'>
			<input type=textbox class='textbox' name='prtblTlphnNo' id='prtblTlphnNo' value='' style='width:230px;'>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>주소 : </b></td>
		<td class='input_box_item'>
            <input type=text class='textbox' name='mailNO' id='mailNO' value='' style='width:80px;margin: 2px 3px 3px 0px;' readonly><img src='../images/korea/btn_search_address.gif' onclick=\"zipcode('".$site_code."');\" style='cursor:pointer;' align='absmiddle'><br/>
            <input type=hidden name='mailNOSeq' id='mailNOSeq' value=''>
            <input type=text class='textbox' id='addr' style='width:280px;margin: 2px 3px 3px 0px;' readonly><br/>
            <input type=text class='textbox' name='dtlsAddr' id='dtlsAddr' value='' style='width:280px;margin: 2px 3px 3px 0px;'> 상세주소
        </td>
	 
	    <td class='input_box_title'> <b>기본주소로 사용 : </b></td>
	    <td class='input_box_item'>
	    	<input type=radio name='baseAddrYN' id='base_Y' value='Y' ><label for='base_Y'>사용</label>
	    	<input type=radio name='baseAddrYN' id='base_N' value='N' checked><label for='base_N'>사용하지않음</label>
	    </td>
	  </tr>           
	  </table>";
      
/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <u>$board_name </u> 에 이용할 제휴사 관리명을  입력해주세요
	</td>
</tr>
</table>
";*/
$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');
/*$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<tr>
			<td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  제휴사 관리 목록</b></div>")."</td>
		</tr>
	</table>";*/
$Contents02 = "
	<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>출고지 목록</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
        <col width='*'>
        <col width=10%>
        <col width=10%>
        <col width=10%>
        <col width=10%>
        <col width=8%>
        <col width=25%>
        <col width=10%>
        <col width=10%>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='m_td'> 제휴사명</td>
		<td class='m_td'> 주소명</td>
		<td class='m_td'> 이름</td>
	    <td class='m_td'> 일반전화번호</td>
	    <td class='m_td'> 휴대전화번호</td>
        <td class='m_td'> 우편번호</td>
	    <td class='m_td'> 상세주소</td>
        <td class='m_td'> 기본주소여부</td>
	    <td class='m_td'> 관리</td>
	  </tr>";


/*

$sql = 	"SELECT bdiv.*, sum(case when bbs_div is NULL then 0 else 1 end) as  group_bbs_cnt , case when depth = 1 then si_ix  else parent_si_ix end as group_order
		FROM ".TBL_BBS_MANAGE_DIV." bdiv left join bbs_".$board_ename." bbs on bdiv.si_ix = bbs.bbs_div where bm_ix = '$bm_ix'
		group by si_ix
		order by group_order asc, depth asc";



//echo $sql;
$db->query($sql);
*/

if(!empty($list)){
	foreach($list as $lt):
	
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td point' style='padding-left:20px;'>".$site_code."</td>
			<td class='list_box_td list_bg_gray'>".$lt[addrNm]."</td>
		    <td class='list_box_td'>".$lt[rcvrNm]."</td>
			<td class='list_box_td'>".$lt[gnrlTlphnNo]."</td>
		    <td class='list_box_td list_bg_gray'>".$lt[prtblTlphnNo]."</td>
		    <td class='list_box_td' style='padding:0px 5px;'>".number_format($lt[mailNO],0,'','-')."</td>
            <td class='list_box_td list_bg_gray'>".$lt[addr]." ".$lt[dtlsAddr]."</td>
            <td class='list_box_td'>".$lt[baseAddrYN]."</td>
		    <td class='list_box_td' style='padding:0px 5px;' nowrap>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $Contents02 .= "
		    	<a href=\"javascript:updateOutAddressInfo('".$lt[memNo]."','".$lt[addrSeq]."','".$lt[addrNm]."','".$lt[rcvrNm]."','".$lt[gnrlTlphnNo]."','".$lt[prtblTlphnNo]."','".number_format($lt[mailNO],0,'','-')."','".$lt[mailNOSeq]."','".$lt[addr]."','".$lt[dtlsAddr]."','".$lt[baseAddrYN]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
            }else{
                $Contents02 .= "
		    	<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
            }
            $Contents02 .= "    
		    </td>
		  </tr> ";
	endforeach;
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=9>등록된 출고지 정보가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "

	  </table>";


if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
    $ButtonString = "
    <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
    <tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
    </table>
    ";
}else{
    $ButtonString = "
    <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
    <tr bgcolor=#ffffff ><td colspan=4 align=center><a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></a></td></tr>
    </table>
    ";
}


$Contents = "<form name='address_form' action='in.outaddress.act.php' method='post' onsubmit='return CheckValue(this)' target=act>
<input name='site_code' type='hidden' value='$site_code'>
<input name='act' type='hidden' value='outAddr_regist'>
";
$Contents .= "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents .= "<tr><td width='100%'>";
$Contents .= $Contents01."<br>";
$Contents .= "</td></tr>";
$Contents .= "<tr><td>".$ContentsDesc01."</td></tr>";
$Contents .= "<tr height=10><td></td></tr>";
$Contents .= "<tr><td>".$ButtonString."</td></tr>";

$Contents .= "<tr height=10><td></td></tr>";
$Contents .= "<tr><td>".$Contents02."<br></td></tr>";
$Contents .= "<tr height=30><td></td></tr>";
$Contents .= "</table >";
$Contents .= "</form>";

$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주소를 수정 하시면 해당 주소로 등록되어 있는 상품에 즉시 적용됩니다.</td></tr> 
        <tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주소를 변경하시려면 주소를 추가하여 사용하시기 바랍니다.</td></tr>
        <tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주소를 추가하시려면 내용을 기입하시고 저장버튼을 클릭하시면 됩니다.</td></tr>
        <tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >출고지 별로 조건부 무료정책을 적용하시려면 해당 출고지 주소에 배송정책을 등록하셔야 됩니다.(예정)</td></tr>
        <tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >출고지 배송정책을 변경하면 해당 출고지 배송비로 설정되어있는 상품에 즉시 적용됩니다.(예정)</td></tr> 
	</table>
	";
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

	$help_text = HelpBox("출고지 관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>
 function CheckValue(frm){
 	if(frm.depth[1].checked){
 		if(frm.parent_si_ix.value == ''){
	 		alert(language_data['site.php']['A'][language]);
			//'2차 제휴사 관리을 등록하기 위해서는 1차제휴사 관리을 반드시 선택하셔야 합니다.'
	 		return false;
 		}
 	}

 	if(frm.site_name.value.length < 1){
 		alert(language_data['site.php']['B'][language]);
		//'등록하시고자 하는 제휴사연동 제휴사 관리명을 입력해주세요'
 		frm.site_name.focus();
 		return false;
 	}

 }
 function updateOutAddressInfo(memNo,addrSeq,addrNm,rcvrNm,gnrlTlphnNo,prtblTlphnNo,mailNO,mailNOSeq,addr,dtlsAddr,baseAddrYN){
    
    var frm = document.address_form;
    
    frm.act.value = 'outAddr_update';
    
    frm.memNo.value = memNo;
    frm.addrSeq.value = addrSeq;
    frm.addrNm.value = addrNm;
    frm.rcvrNm.value = rcvrNm;
    frm.gnrlTlphnNo.value = gnrlTlphnNo;
    frm.prtblTlphnNo.value = prtblTlphnNo;
    frm.mailNO.value = mailNO;
    frm.mailNOSeq.value = mailNOSeq;
    frm.addr.value = addr;
    frm.dtlsAddr.value = dtlsAddr;
    
    if(baseAddrYN=='Y') {
		frm.baseAddrYN[0].checked = true;
	} else {
		frm.baseAddrYN[1].checked = true;
	}
    
 }
  
 function zipcode(site_code){
	var zip = window.open('zipcode.php?site_code='+site_code,'','width=440,height=350,scrollbars=yes,status=no');
 }
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->Navigation = "제휴사연동 > 출고지 관리";
	$P->title = "출고지 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->Navigation = "제휴사연동 > 출고지 관리";
	$P->title = "출고지 관리";
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='제휴사 정보'

*/
?>