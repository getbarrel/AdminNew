<?
include("../class/layout.class");


$db = new Database;
//print_r($admininfo);

$sql = "SELECT * FROM co_myserver_info   ";
//echo $sql;
$db->query($sql); //where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'
$_my_serverinfo = $db->fetchall("object");


if($db->total){
	$db->fetch();

	for($i=0;$i < count($_my_serverinfo);$i++){
		$my_serverinfo[$_my_serverinfo[$i]["server_property"]] =  $_my_serverinfo[$i]["server_value"];
	}
	$server_url = $my_serverinfo[server_url];

	$authinfo[mall_domain] = $server_url;
	$authinfo[mall_domain_id] = "myserver";
	if($my_serverinfo[myserver_key] == ""){
		$myserver_key = makelicensekey($authinfo);
	}else{
		$myserver_key = $my_serverinfo[myserver_key];
	}
	//print_r($my_serverinfo);
}else{
	$server_url = $_SERVER[HTTP_HOST];

	$authinfo[mall_domain] = $server_url;
	$authinfo[mall_domain_id] = "myserver";

	$myserver_key = makelicensekey($authinfo);

}
//echo md5("wooho".$db->dt[mall_domain].$db->dt[mall_domain_id]);


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
			<td align='left'> ".GetTitleNavigation("내 공유서버 설정", "상점관리 > 내 공유서버 설정 ")."</td>
	  </tr>
	  <tr>
	    <td align='left'>
			<table cellpadding='0' cellspacing='0' border='0' width='100%'>
				<tr>
					<td>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b>내공유 서버 정보</b></div>")."</td>
				</tr>
			</table>
		</td>
	  </tr>
	  <tr>
		<td>
			<table cellpadding='0' cellspacing='0' width='100%' style='margin-top:3px;' class='input_table_box'>
				<col width='180' />
				<col width='*' />
			   <tr>
				<td class='input_box_title'> <b>공유서버 사용유무 : </b></td>
				<td class='input_box_item'>
					<input type=radio name='myserver_info[disp]' id='disp_1' value='1' ".($my_serverinfo[disp] == "1" ? "checked":"")." onclick=\"$('#my_server_info').show();\"><label for='disp_1'>사용</label>
					<input type=radio name='myserver_info[disp]' id='disp_0' value='0' ".($my_serverinfo[disp] == "0" || $my_serverinfo[disp] == "" ? "checked":"")." onclick=\"$('#my_server_info').hide();\"><label for='disp_0'>사용하지않음</label>
				</td>
			  </tr>
			 </table>
			 <table cellpadding='0' cellspacing='0' width='100%' style='margin-top:-1px;' class='input_table_box' id='my_server_info' ".($my_serverinfo[disp] == "0" || $my_serverinfo[disp] == "" ? "style='display:none;'":"").">
				<col width='180' />
				<col width='*' />
			   <tr>
				<td class='input_box_title'> <b>내 공유 서버명 : </b></td>
				<td class='input_box_item'>
				<table>
					<tr>
						<td><input type=text class='textbox' name='myserver_info[server_name]' value='".$my_serverinfo[server_name]."' style='width:230px;'></td>
						<td class='input_box_item'> <span class=small><!--도메인 아이디는 관리자 아이디와는 다른 아이디 입니다. <br>쇼핑몰을 인증받기 위해서 필요한 아이디 입니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span></td>
					</tr>
				</table>
			  </tr>
			  <tr>
				<td class='input_box_title'> <b>내 공유 서버 URL : </b></td>
				<td class='input_box_item'>
				<table>
					<tr>
						<td>http:// <input type=text class='textbox' name='myserver_info[server_url]' value='".$server_url."' style='width:200px;' readonly></td>
						<td class='input_box_item'><span class=small><!--공유 서버 URL 을 입력해주세요.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span></td>
					</tr>
				</table>
			  </tr>
			  <tr>
				<td class='input_box_title'> <b>공유서버 회원가입 승인 :</b> </td>
				<td class='input_box_item'>
				<table>
					<tr>
						<td>
							<input type=radio name='myserver_info[reg_auth]' id='reg_auth_1' value='1' ".($my_serverinfo[reg_auth] == "1" ? "checked":"")."><label for='reg_auth_1'>자동승인</label>
							<input type=radio name='myserver_info[reg_auth]' id='reg_auth_0' value='0' ".($my_serverinfo[reg_auth] == "0" ? "checked":"")."><label for='reg_auth_0'>확인후 승인</label>
						</td>
						<td class='input_box_item' align=left></td>
					</tr>
				</table>
			  </tr>
			  <!--tr bgcolor=#ffffff >
				<td height='40'> <b>도메인 아이디 : </b></td>
				<td><input type=text class='textbox' name='myserver_info[mall_domain_id]' value='".$my_serverinfo[mall_domain_id]."' style='width:230px;border:0px;' readonly></td>
				<td> <span class=small>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</span></td>
			  </tr>
			  <tr height=1><td colspan=4 class=dot-x></td></tr-->
			  <tr>
				<td class='input_box_title'> <b>도메인 key</b></td>
				<td class='input_box_item'>
				<table>
					<tr>
						<td><input type=text class='textbox' name='myserver_info[myserver_key]' value='".$myserver_key."' style='width:230px;' readonly></td>
						<td class='input_box_item'><span class=small>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</span><!--발급받은 32 자리 key--></td>
					</tr>
				</table>
			  </tr>
			 </table>
		</td>
	</tr>
	  </table>";

$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr height=30>
	<td style='padding-left:10px;' ><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small> 해당 서버를 공유서버로 설정하기 위해서는 공유서버가 설정되어야 합니다.".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')."
	</td>
</tr>
</table>
";



$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff height=30><td colspan=4 align=center style='padding:0px 0px 20px 0px;'><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";

//print_r($db->dt);
$Contents = "<table width='100%'   border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<form name='edit_form' action='serverinfo.act.php' method='post' CheckValue(this)'  target='iframe_act'><!--enctype='multipart/form-data'  target='iframe_act'-->
<input name='act' type='hidden' value='replace'>
<input name='myserver_info[company_id]' type='hidden' value='".$admininfo[company_id]."'>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents05."<br></td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table >";


//$Script = "<script language='javascript' src='basicinfo.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = cogoods_menu();
$P->Navigation = "공유상품관리 > 내공유 서버설정";
$P->title = "내공유 서버설정";
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