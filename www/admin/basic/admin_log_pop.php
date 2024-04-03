<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;

$sql = "SELECT admin_id, ip, crud_div, request_method, http_host, send_data, regdate FROM admin_log_new WHERE log_ix = '$log_ix' ";
$db->query($sql);
$db->fetch();

$admin_id = $db->dt[admin_id];
$ip = $db->dt[ip];
$request_method = $db->dt[request_method];
$send_data = $db->dt[send_data];

if($request_method == "GET"){
	$send_data = str_replace("&", "<br>", $send_data);
}else if($request_method == "POST"){
	$send_data = str_replace("}","}<br>",str_replace("{","{<br>",str_replace(";", "<br>", $send_data)));
}


$regdate = $db->dt[regdate];
switch($db->dt[crud_div]){
	case "C":
		$crud_div = "생성";
		break;
	case "R":
		$crud_div = "읽기";
		break;
	case "U":
		$crud_div = "수정";
		break;
	case "D":
		$crud_div = "삭제";
		break;
}

$http_host = explode('/',$db->dt[http_host]);
$cnt = strlen($http_host)-2;

$sql = "select menu_name from admin_menus where menu_link like '%$http_host[$cnt]%'; ";
//echo $sql;
$mdb->query($sql);
$mdb->fetch();
$menu_name = $mdb->dt[menu_name];

if($mdb->dt[menu_name] == ""){
	$menu_name = str_replace("ssladmin.getbarrel.com/","",str_replace("stg","",$db->dt[http_host]));
}else{
	$menu_name = $mdb->dt[menu_name]."<br>(".str_replace("ssladmin.getbarrel.com/","",str_replace("stg","",$db->dt[http_host])).")";
}

$Contents .= "
<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
	<tr>
		<td align='left' colspan=2> ".GetTitleNavigation("작업 Request 정보", "기초정보 > 작업 Request 정보", false)."</td>
	</tr>";
$Contents .= "
</table>
";

$Contents .= "
<table cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<tr>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr>
				<td align='left' colspan=2> ".GetTitleNavigation("작업 Request 정보", "기초정보 > 작업 Request 정보", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 0px 0 0px;height:569px;vertical-align:top'>
				<table border='0' width='100%' cellspacing='1' cellpadding='0'>
					<tr>
						<td>
							<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
								<col width=15%>
								<col width=35%>
								<col width=15%>
								<col width=35%>
								<tr>
									<td class='input_box_title' nowrap> 관리자ID</td>
									<td class='input_box_item'>&nbsp;".$admin_id."</td>
									<td class='input_box_title' nowrap> 관리자IP</td>
									<td class='input_box_item'>&nbsp;".$ip."</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 작업페이지(URL)</td>
									<td class='input_box_item'>&nbsp;".$menu_name."</td>
									<td class='input_box_title' nowrap> 작업방식</td>
									<td class='input_box_item'>&nbsp;".$request_method."</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> CRUD</td>
									<td class='input_box_item'>&nbsp;".$crud_div."</td>
									<td class='input_box_title' nowrap> 작업날짜</td>
									<td class='input_box_item'>&nbsp;".$regdate."</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 작업Request</td>
									<td class='input_box_item' colspan='3'>
										".$send_data."
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
";

$P = new ManagePopLayOut();
$P->addScript = "<script language='javascript' src='../basic/company.add.js'></script>".$Script;
$P->Navigation = "관리자 작업로그 > 작업 Request 정보";
$P->NaviTitle = "작업 Request 정보";
$P->title = "작업 Request 정보";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>