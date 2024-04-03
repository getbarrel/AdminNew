<?
include("../class/layout.class");
include ("./company.lib.php");
//auth(9);

$db = new Database;
$mdb = new Database;
//$db->debug = true;//페이지에서 오류가 없는데 쿼리를 찍기에 주석처리함 kbk 13/02/04
$update_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U");
$delete_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D");
$create_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C");
$excel_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E");
if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
	$menu_name = "사원 일괄등록";
}else{
	$menu_name = "사원 일괄등록";
}

$info_type = "member_lump";


	$max = 15; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}


include "member_query.php";

$Script = "
<script language='javascript'>

function view_go()
{
	var sort = document.view.sort[document.view.sort.selectedIndex].value;

	location.href = 'member.php?view='+sort;
}

function deleteMemberInfo(act, code){
 	if(confirm('해당회원 정보를 정말로 삭제하시겠습니까? \\n 삭제시 관련된 모든 정보가 삭제됩니다.')){
		window.frames['iframe_act'].location.href= 'member.act.php?act='+act+'&code='+code;
 	}
}

</script>";

$Contents = "


<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>

<table width='100%' border='0'  cellpadding=0 cellspacing=0 align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("사원 일괄등록", "기초정보관리 > 본사관리 ")."</td>
  </tr>
	<tr>
			    <td align='left' colspan=4 style='padding-bottom:20px;'> 
			    	<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='member.list.php'\">전체사원 리스트</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='member.resign.php'\">퇴사사원 리스트</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03'  class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='member.lump.php'\">일괄등록하기</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<!--<table id='tab_04' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >탭 메뉴 4</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_05' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >탭 메뉴 5</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_06' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >탭 메뉴 6</td>
								<th class='box_03'></th>
							</tr>
							</table-->
						</td>
						<td class='btn'>						
							
						</td>
					</tr>
					</table>	
				</div>
			    </td>
			</tr>

			<tr>
			 	<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:2px 5px 2px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 사원 일괄등록</b>&nbsp;&nbsp;&nbsp;<b id='select_category_path1'>전체 <span class=small><!--선택된 카테고리가 없습니다. 좌측 카테고리에서 선택해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span></b> <a href='".$download_excel_file."'><img src='../images/".$admininfo["language"]."/btn_sample_excel_save.gif' align=absmiddle></a><- 대량등록엑셀샘플 파일이 업데이트(2012-08-12) 되었습니다. 파일을 다시 다운받아서 사용해주시기 바랍니다.</div>")."</td>
			 </tr>
			 <tr>
			 	<td colspan=3>
			 	<form name='excel_input_form' method='post' action='product_input_excel_2003.act.php' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target='iframe_act'>
			 	<!--form name='excel_input_form' method='post' action='product_input_excel_2003.act.php' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)'-->
			 	<input type='hidden' name='act' value='excel_input'>
			 	<input type='hidden' name='cid' value=''>
			 	<input type='hidden' name='depth' value=''>
				<table width='100%' border=0 cellpadding=0 cellspacing=1 class='input_table_box'>
					<col width=17%>
									<col width=33%>
									<col width=17%>
									<col width=33%>
									<tr>
					<tr height=30 align=center>
						<td class='input_box_title' ><b>엑셀파일 입력</b>  </td>
						<td class='input_box_item' colspan=3><input type=file class='textbox' name='excel_file' style='height:22px;width:90%' validation=true title='엑셀파일 입력'></td>
					</tr>
					</table>
					<table width='100%' border=0 cellpadding=0 cellspacing=1 >
					<tr height=20>
						<td style='padding:6px;line-height:140%;' colspan=2><img src='../image/emo_3_15.gif' border=0 align=absmiddle><!-- 엑셀정보에는 <b>' (따옴표)</b>는 사용하실수 없습니다. <b>엑셀정보에 카테고리를 지정</b>하시면 카테고리를 선택 안하셔도 해당 카테고리로 상품이 등록 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</td></tr>
					<tr height=30><td colspan=2 style='padding:10px 0px;' align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0></td></tr>
				</table>
				</form>
			 	</td>
			 </tr>

  <tr>
  	<td>";
$Contents .= "
<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<th class='box_01'></th>
		<td class='box_02'></td>
		<th class='box_03'></th>
	</tr>

	
		   
		    ";

$vdate = date("Ymd", time());
$today = date("Y/m/d", time());
$vyesterday = date("Y/m/d", time()-84600);
$voneweekago = date("Y/m/d", time()-84600*7);
$vtwoweekago = date("Y/m/d", time()-84600*14);
$vfourweekago = date("Y/m/d", time()-84600*28);
$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

 $Contents .= "
		   
		    </table>
		</td>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
</table>";

$Contents .= "
    </td>

  </tr>
</table><br></form>";


$Contents .= "

<form name='list_frm'>
<input type='hidden' name='code[]' id='code'>
<table width='98%' border='0' cellpadding='0' cellspacing='0' align='center' >
	<tr>
		<td colspan=1>
		</td>
		<td align='right' colspan=3 style='padding:5px 0 5px 0;'>
		<a href=\"javascript:PoPWindow3('member.report.php?mmode=report&info_type=".$info_type."&".$QUERY_STRING."',970,800,'stock_report')\"> <img src='../images/".$admininfo["language"]."/btn_report_print.gif'></a>
		<a href='?mmode=pop'> </a> ";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
			<a href='excel_config.php?".$QUERY_STRING."&info_type=".$info_type."' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
		}else{
			$Contents .= "
			<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
		}

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= " <a href='member.list.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$Contents .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}
		$Contents .= "
				</td>
			</tr>
		</table>";

$Contents .= "
<table width='98%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='28' bgcolor='#ffffff'>
    <td width='3%' align='center' class=s_td rowspan='2'><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td width='3%' align='center' class='m_td' rowspan='2'><font color='#000000'><b>순번</b></font></td>
    <td width='5%' align='center' class='m_td' rowspan='2'><font color='#000000'><b>사원코드</b></font></td>
    <td width='6%' align='center' class='m_td' rowspan='2'><font color='#000000'><b>입사일<br>(근무년수)</b></font></td>
    <td width='6%' align='center' class=m_td rowspan='2'><font color='#000000'><b>이름</b></font></td>

    <td width='10%' align='center' class=m_td rowspan='2'><font color='#000000'><b>근무사업장</b></font></td>
    <td width='10%' align='center' class=m_td colspan='4'><font color='#000000'><b>부서및직책</b></font></td>

	<td width='6%' align='center' class=m_td rowspan='2'><font color='#000000'><b>연락처</b></font></td>
	<td width='6%' align='center' class=m_td rowspan='2'><font color='#000000'><b>이메일</b></font></td>
    <!--<td width='6%' align='center' class=m_td rowspan='2'><font color='#000000'><b>메신저</b></font></td>-->

    <td width='20%' align='center' class=e_td rowspan='2'><font color='#000000'><b>관리</b></font></td>
  </tr>";
$Contents .= "
	<tr height='28' bgcolor='#ffffff'>

		<td width='6%' align='center' class=m_td><font color='#000000'><b>부서그룹</b></font></td>
		<td width='6%' align='center' class=m_td><font color='#000000'><b>부서</b></font></td>
		<td width='6%' align='center' class=m_td><font color='#000000'><b>직위</b></font></td>
		<td width='6%' align='center' class=m_td><font color='#000000'><b>직책</b></font></td>
		
	</tr>
";

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;
	/*
		if ($db->dt[mem_level] == "E")	{ $perm = "탈퇴회원"; }
		if ($db->dt[mem_level] == "M")	{ $perm = "일반회원"; }
		if ($db->dt[mem_level] == "B")	{ $perm = "입점업체"; }
		if ($db->dt[mem_level] == "C")	{ $perm = "특별회원"; }
	*/
		
        if($db->dt[is_id_auth] != "Y"){
            $is_id_auth = "미인증";
        }else{
            $is_id_auth = "";
        }

        switch($db->dt[authorized]){

        case "Y":
            $authorized = "승인";
            break;
        case "N":
            $authorized = "승인대기";
            break;
        case "X":
            $authorized = "승인거부";
            break;
        default:
            $authorized = "알수없음";
            break;
        }

        switch($db->dt[mem_type]){

        case "M":
            $mem_type = "일반";
            break;
        case "C":
            $mem_type = "기업".($db->dt[com_name] != "" ? "(".$db->dt[com_name].")":"");
            break;
        case "F":
            $mem_type = "외국인";
            break;
        case "S":
            $mem_type = "셀러";
            break;
        case "A":
            $mem_type = "관리자";
            break;
        case "MD":
            $mem_type = "MD";
            break;
        default:
            $mem_type = "일반";
            break;
        }

		
        $Contents = $Contents."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
            <td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
            <td class='list_box_td' >".$no."</td>
            <td class='list_box_td' style='padding:0px 5px;'>".$db->dt[mem_code]."</td>
            <td class='list_box_td' nowrap>".$db->dt[join_date]."</td>
            <td class='list_box_td' ><a href='javascript:PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')' style='cursor:pointer' >".Black_list_check($db->dt[code],$db->dt[name])."</td>
            <td class='list_box_td point' nowrap>".$db->dt[com_name]."</td>
            <td class='list_box_td ' nowrap>".getGroupname('group',$db->dt[com_group])."</td>
			<td class='list_box_td point' >".getGroupname('department',$db->dt[department])."</td>
			<td class='list_box_td' >".getGroupname('position',$db->dt[position])."</td>
			<td class='list_box_td point' >".getGroupname('duty',$db->dt[duty])."</td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
			<td class='list_box_td' >".$db->dt[mail]."</td>

            <!--<td class='list_box_td ctr point' >".$db->dt[mail]."</a></td>-->
            <td class='list_box_td ctr'  style='padding:5px;' nowrap>";

			if($update_auth){
				$Contents .= "<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member.add.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_info')\" /> ";
			}else{
				$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle ></a> ";
			}

			//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
			if($delete_auth){
				$Contents .= "<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"deleteMemberInfo('delete','".$db->dt[code]."')\" /> ";
			}else{
				//$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0 align=absmiddle ></a> ";
			}
            if($create_auth){
			     $Contents .= "
        	     <img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../sms.pop.php?code=".$db->dt[code]."',500,380,'sendsms')\">
        	     <img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?code=".$db->dt[code]."',550,535,'sendmail')\">
                 ";
            }else{
                $Contents .= "
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle></a>
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle></a>
                 ";
            }
            $Contents .= "
    </td>
  </tr>";

	}

if (!$db->total){

$Contents = $Contents."
  <tr height=50>
    <td colspan='13' align='center'>등록된 회원 데이타가 없습니다.</td>
  </tr>";
}

$Contents .= "
</table>
</form>";

$Contents .= "
<table width=98% align='right'>
<tr hegiht=30><td colspan=8 align=right style='padding:10px 0px;'>".$str_page_bar."</td></tr>
</table>";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사원에게 SMS 또는 메일을 보내실려면 보내고자 하는 사원을 선택하신후 '선택사원 SMS 보내기' 버튼을 클릭하신후 메일을 보내실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사원정보를 백업하기 위해서는 사원정보 검색후 엑셀로 저장 버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색기능을 통해서 조건에 맞는 사원을 빠르게 검색하실수 있습니다</td></tr>
</table>
";

$Contents .= HelpBox("사원관리", $help_text,'70');



$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "";
$P->strLeftMenu = basic_menu();
$P->Navigation = "기초정보관리 > 본사관리 > $menu_name";
$P->title = "사원 일괄등록";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>



