<?
include("../class/layout.class");


$db = new Database;
//print_r($admininfo);

$sql = "SELECT * FROM co_client_hostservers where chs_ix = '".$chs_ix."'  ";
//echo $sql;
$db->query($sql); //where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'

if($db->total){
	$db->fetch();

	$act = "hostserver_update";
}else{
	$act = "hostserver_insert";
}
//echo md5("wooho".$db->dt[mall_domain].$db->dt[mall_domain_id]);


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
			<td align='left'> ".GetTitleNavigation("공유 서버관리", "상점관리 > 공유 서버관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left'>
			<table cellpadding='0' cellspacing='0' border='0' width='100%'>
				<tr>
					<td>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b>공유 서버 정보</b></div>")."</td>
				</tr>
			</table>
		</td>
	  </tr>
	  <tr>
		<td>
			<table cellpadding='0' cellspacing='0' border='0' width='100%' class='input_table_box' style='margin-top:3px;'>
				<col width='200' />
				<col width='*' />
			   <tr bgcolor=#ffffff >
				<td class='input_box_title'> 공유 서버명</td>
				<td class='input_box_item'>
					<table>
						<tr>
							<td><input type=text class='textbox' name='server_name' value='".$db->dt[server_name]."' validation=true title='공유 서버명' style='width:230px;'></td>
							<td class='input_box_item'> <span class=small><!--도메인 아이디는 관리자 아이디와는 다른 아이디 입니다. <br>쇼핑몰을 인증받기 위해서 필요한 아이디 입니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span></td>
						</tr>
					</table>
				</td>
			  </tr>
			  <tr>
				<td class='input_box_title'> 공유 서버 URL</td>
				<td class='input_box_item'>
					<table>
						<tr>
							<td>http:// <input type=text class='textbox' name='server_url' value='".$db->dt[server_url]."' validation=true title='공유 서버 URL'  style='width:200px;'></td>
							<td class='input_box_item'><span class=small><!--공유 서버 URL 을 입력해주세요.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span></td>
						</tr>
					</table>
				</td>
			  </tr>
			  <tr>
				<td class='input_box_title'> 기본공유서버 : </td>
				<td class='input_box_item'>
					<input type=radio name='basic' id='basic_1' value='1' ".($db->dt[basic] == "1" ? "checked":"")." ><label for='basic_1'>기본</label>
					<input type=radio name='basic' id='basic_0' value='0' ".($db->dt[basic] == "0" || $db->dt[basic] == "" ? "checked":"")."><label for='basic_0'>기본아님</label>
				</td>
			  </tr>
			  <tr>
				<td class='input_box_title'> 사용유무 : </td>
				<td class='input_box_item'>
					<input type=radio name='disp' id='disp_1' value='1' ".($db->dt[disp] == "1" || $db->dt[disp] == "" ? "checked":"")."><label for='disp_1'>사용</label>
					<input type=radio name='disp' id='disp_0' value='0' ".($db->dt[disp] == "0" ? "checked":"")."><label for='disp_0'>사용하지않음</label>
				</td>
			  </tr>
			  <!--tr bgcolor=#ffffff >
				<td height='40'> 도메인 아이디</td>
				<td><input type=text class='textbox' name='mall_domain_id' value='".$db->dt[mall_domain_id]."' style='width:230px;border:0px;' readonly></td>
				<td> <span class=small>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</span></td>
			  </tr-->
			  <tr>
				<td class='input_box_title'> 도메인 key</td>
				<td class='input_box_item'>
					<table>
						<tr>
							<td><input type=text class='textbox' name='mall_domain_key' value='".$db->dt[mall_domain_key]."' style='width:230px;' readonly></td>
							<td class='input_box_item'><span class=small>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</span><!--발급받은 32 자리 key--></td>
						</tr>
					</table>
				</td>
			  </tr>
			 </table>
		</td>
	</tr>
	  </table>";

$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <!--상품공유에 관련된 인증 및 관련 정보를 관리 하는 서버 정보입니다. 상품 공유 그룹에 따라서 변경 될수 있으며 도메인 아이디와 도메인 키에 의해서 인증 관리 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')."
	</td>
</tr>
</table>
";



$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff height=30><td colspan=4 align=center style='padding:0px 0px 20px 0px;'><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	  <tr>
	    <td align='left' colspan=6> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>공유 서버 목록</b></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
	  <tr height=25  align=center style='font-weight:bold'>
	    <td style='width:10%;' bgcolor=#efefef> 기본</td>
		<td style='width:20%;' bgcolor=#efefef> 공유 서버명</td>
	    <td style='width:20%;' bgcolor=#efefef> 공유 서버 URL</td>
	    <td style='width:10%;' bgcolor=#efefef> 사용유무</td>
	    <td style='width:20%;' bgcolor=#efefef> 등록일자</td>
	    <td style='width:20%;' bgcolor=#efefef> 관리</td>
	  </tr>";
$db = new Database;


$db->query("SELECT * FROM co_client_hostservers ");


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td>".($db->dt[basic] == "1" ?  "기본":"-")."</td>
		    <td>".$db->dt[server_name]."</td>
		    <td>".$db->dt[server_url]."</td>
		    <td>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td>".$db->dt[regdate]."</td>
		    <td>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents02 .= "<a href=\"?chs_ix=".$db->dt[chs_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
			}else{
				$Contents02 .= "
		    	<a href=\"javascript:alert('수정권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
			}
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$Contents02 .= "
	    		<a href=\"javascript:DeleteHostServer('hostserver_delete','".$db->dt[chs_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
			$Contents02 .= "
	    		<a href=\"javascript:alert('삭제권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
	$Contents02 .= "
		    </td>
		  </tr>  ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 서버 정보가 없습니다. </td>
		  </tr>  ";
}
$Contents02 .= "
	  <!--tr height=1><td colspan=6 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->

	  </table>";
//print_r($db->dt);
$Contents = "<table width='100%'   border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<form name='edit_form' action='hostserver.act.php' method='post' onsubmit='return CheckFormValue(this)' target='iframe_act'><!--enctype='multipart/form-data'  -->
<input name='act' type='hidden' value='".$act."'>
<input name='chs_ix' type='hidden' value='".$_GET[chs_ix]."'>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents05."<br></td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table >";


$Script = "<script language='javascript' >
function DeleteHostServer(act, chs_ix){
	if(confirm('해당 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.edit_form;
 		frm.act.value = act;
 		frm.chs_ix.value = chs_ix;
 		frm.submit();
 	}

}


</script>";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = cogoods_menu();
$P->Navigation = "상품공유관리 > 공유 서버관리";
$P->title = "공유 서버관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();

/*
create table co_client_hostservers (
chs_ix	int(8) unsigned auto_increment,
server_name varchar(50) not null,
domain varchar(100) not null,
regdate datetime not null,
primary key(chs_ix))




*/
?>