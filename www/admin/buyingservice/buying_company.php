<?
include("../class/layout.class");


$db = new Database;

$Contents = "
	<table cellpadding=0 cellspacing=0 border=0 width=100%>
	 <tr>
	    <td align='left' colspan=6 > ".GetTitleNavigation("사입처관리", "사입관리 > 사입처관리")."</td>
	</tr>
	<tr height=10>
		<td align=rihgt style='padding-left:0px;' valign=top>
		<table width='100%' border=0 cellspacing='0' cellpadding='0'>
			<tr height=30><td width=100%><img src='/admin/images/dot_org.gif' align=absmiddle> <b>사입처 목록 </b><br></td></tr>
		</table>
		".printBuyingCompany()."
		</td>

		<td rowspan=6 width=40% valign=top style='padding-left:5px;'>

					<form name='buying_company_frm' action='./buying_company.act.php' method='post' enctype='multipart/form-data'>
					<input type=hidden name=mode value=insert>
					<input type=hidden name=bc_ix value=''>
					<input type=hidden name=mmode value='$mmode'>

				  	<table border='0' cellspacing='0' cellpadding='0' width='100%'>
				        	<tr>
				        		<td bgcolor='#F8F9FA'>
							<table width='100%' border=0>
								<tr height=25><td width=100%><img src='/admin/images/dot_org.gif' align=absmiddle> <b>사입처 추가하기 </b><br></td></tr>
							</table>
							<table cellpadding=0 cellspacing=0  border=0 width='100%' class='input_table_box'>
								<col width='40%'>
								<col width='60%'>
								<tr>
									<td class='input_box_title' style='padding:0px 10px;'nowrap><b>카테고리 <img src='".$required3_path."'></b></td>
									<td class='input_box_item'>
									".getCategoryList()."
									</td>
								</tr>
								<!--tr height=1><td colspan=3 class='dot-x'></td></tr-->
								<tr>
									<td class='input_box_title'><b>사입처명 <img src='".$required3_path."'></b></td>
									<td class='input_box_item'>
									<input type=text class=textbox1 name=bc_name size=21 validation='true' title='사입처명'>
									</td>
								</tr>
								<tr>
									<td class='input_box_title'><b>상가명 <img src='".$required3_path."'></b></td>
									<td class='input_box_item'>
									<input type=text class=textbox1 name=building size=21 validation='true' title='상가명'>
									</td>
								</tr>
								<tr>
									<td class='input_box_title'><b>층수 <img src='".$required3_path."'></b></td>
									<td class='input_box_item'>
									<input type=text class=textbox1 name=floor size=21 validation='true' title='층수'>
									</td>
								</tr>
								<tr>
									<td class='input_box_title'><b>호수 <img src='".$required3_path."'></b></td>
									<td class='input_box_item'>
									<input type=text class=textbox1 name=bc_no size=21 validation='true' title='호수'>
									</td>
								</tr>
								<tr>
									<td class='input_box_title'><b>전화번호 <img src='".$required3_path."'></b></td>
									<td class='input_box_item'>
									<input type=text class=textbox1 name=bc_phone size=21 validation='true' title='전화번호'>
									</td>
								</tr>
								<tr>
									<td class='input_box_title'>사용유무 </td>
									<td class='input_box_item'>
									<input type=checkbox name=disp class=nonborder value=1> 사입처 사용
									</td>
								</tr>
								<!--tr height=1><td colspan=3 class='dot-x'></td></tr-->
							</table>

							</td>
						</tr>
						<tr bgcolor=#ffffff height=30>
							<td align=right nowrap width=60%>
								<table cellpadding=0 cellspacing=0>
									<tr>
										<td style='padding-right:5px;'>";
										if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
											$Contents .= "<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 id=delete style='cursor:hand;display:none' onclick=\"CompanyInput(document.buying_company_frm,'delete')\">";
										}else{
											$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 id=delete style='cursor:pointer;display:none' ></a>";
										}
										$Contents .= "
										</td>
										<td>";
										if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
											$Contents .= "<img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 id=modify style='cursor:hand;display:none' onclick=\"CompanyInput(document.buying_company_frm,'update')\">";
										}else{
											$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 id=modify style='cursor:pointer;display:none' ></a>";
										}
										$Contents .= "
										</td>
										<td>";
										if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
											$Contents .= "<img src='../images/".$admininfo["language"]."/btn_s_ok.gif' border=0 id=ok align=absmiddle style='cursor:hand' onclick=\"CompanyInput(document.buying_company_frm,'insert')\">";
										}else{
											$Contents .= "<a href=\"".$auth_insert_msg."\"><img src='../images/".$admininfo["language"]."/btn_s_ok.gif' border=0 id=ok  ></a>";
										}
										$Contents .= "
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					</form>
		</td>
	</tr>
	</table>

	<iframe name='extand' id='extand' src='' width=0 height=0></iframe>";
	/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록하실 사입처에 카테고리를 선택하시고 사입처명을 입력하시고 등록하시면 됩니다. </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사입처 정보 수정을 원하시면 사입처 이름을 클릭해주세요 사입처 정보를 수정한다음 수정하기 버튼을 클릭합니다</td></tr>
</table>
";*/
	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("사입처관리", $help_text);


if($mmode == "pop"){
	$Script = "<script language='JavaScript' src='buying_company.js'></script>";
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->Navigation = "사입관리 > 사입처관리";
	$P->title = "사입처관리";
	$P->strLeftMenu = buyingservice_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$Script = "<script language='JavaScript' src='buying_company.js'></script>";
	$P = new LayOut();
	$P->addScript = $Script;
	$P->Navigation = "사입관리 > 사입처관리";
	$P->title = "사입처관리";
	$P->strLeftMenu = buyingservice_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}



function printBuyingCompany()
{
global $db,$nset,$page, $auth_update_msg;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
	$db->query("SELECT bc.* FROM shop_buying_company bc ");
	$total = $db->total;
	$pagestring = page_bar($total, $page, $max, "&cid=$cid&depth=$depth&orderby=$orderby","");
	$db->query("SELECT bc.* FROM shop_buying_company bc  LIMIT $start,$max");

	$bl = "<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>
		<tr height=25 bgcolor=#efefef align=center>
			<td class='s_td'>번호</td>
			<td class='m_td'>코드</td>
			<td class='m_td'>사입처이름</td>
			<td class='m_td'>상가명</td>
			<td class='m_td'>층수</td>
			<td class='m_td'>호수</td>
			<td class='m_td'>전화번호</td>
			<td class='e_td'>사용유무</td>
			<!--td class='e_td'>검색표시유무</td-->
			</tr>";

	if ($db->total == 0)	{
		$bl = $bl."<tr height=100><td colspan=8 align=center>사입처 리스트가 존재 없습니다.</td></tr>";
	}else{

		for($i=0 ; $i <$db->total ; $i++)
		{
			$db->fetch($i);
			if($db->dt[disp] == 1){
				$display_string = "사용";
			}else{
				$display_string = "사용안함";
			}

			if($db->dt[search_disp] == 1){
				$search_disp_string = "표시";
			}else{
				$search_disp_string = "표시하지않음";
			}


			$bl .= "<tr height=25 align=center>
				<td class='list_box_td list_bg_gray'>".($i+1)."</td>
				<td class='list_box_td'>".$db->dt[bc_ix]."</td>
				<td class='list_box_td list_bg_gray'>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			$bl .= "<a href=\"JavaScript:ViewCompanyImage('".$db->dt[bc_ix]."')\">".($db->dt[bc_name] == "" ? "-":$db->dt[bc_name])."</a>";
			}else{
			$bl .= "<a href=\"".$auth_update_msg."\"><u>".$db->dt[bc_name]."</u></a>";
			}
			$bl .= "
				</td>
				<td class='list_box_td'>".$db->dt[building]."</td>
				<td class='list_box_td list_bg_gray'>".$db->dt[floor]."</td>
				<td class='list_box_td'>".$db->dt[bc_no]."</td>
				<td class='list_box_td list_bg_gray'>".$db->dt[bc_phone]."</td>
				<td class='list_box_td'>".$display_string."</td><!--td>".$search_disp_string."</td--></tr>";
			$bl = $bl."";
		}
	}
	$bl = $bl."</table>
				<table cellpadding=0 cellspacing=0 width=100% style='padding:3px;'>
					<tr>
						<td colspan=8 align=center>".$pagestring."</td>
					</tr>
				</table>";

		return $bl;
}
/*
create table shop_buying_company (
	bc_ix int(3) unsigned zerofill not null auto_increment,
	cid varchar(15) not null ,
	bc_name varchar(100) null default null,
	building varchar(50) null default null,
	floor varchar(50) null default null,
	bc_phone varchar(20) null default null,
	disp char(1) null default '0',
	primary key(bc_ix)
);
*/
?>