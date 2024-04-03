<?
include("../class/layout.class");


$db = new Database;
if(!$agent_type){
	$agent_type = "W";
}

$Contents = "
	<table cellpadding=3 cellspacing=0 border=0 width=100%>
	 <tr >
	    <td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("분류관리", "프로모션/전시 > 분류관리")."</td>
	</tr>
	<tr height=10>
		<td align=rihgt style='padding-left:20px;' valign=top>
		<table width='100%' border=0>
			<tr height=30><td width=100%><img src='/admin/images/dot_org.gif' align=absmiddle> <b>분류 목록 </b><br></td></tr>
		</table>
		".MakerListevent()."
		</td>

		<td rowspan=6 width=50% valign=top style='padding-left:20px;'>

					<form name='cateform' action='../display/event.act.php' onsubmit='return CheckFormValue(this)'  method='post' enctype='multipart/form-data' target='iframe_act'>
					<input type=hidden name=act value=cate_insert>
					<input type=hidden name=er_ix value=''>
					<input type=hidden name=mmode value='$mmode'>
					<input type=hidden name=agent_type value='$agent_type'>

				  	<table border='0' cellspacing='1' cellpadding='7' width='100%'>
				        	<tr>
				        		<td >
							<table width='100%' border=0 >
								<tr height=25><td width=100%><img src='/admin/images/dot_org.gif' align=absmiddle> <b>분류 추가하기 </b><br></td></tr>
							</table>
							<table cellpadding=0 cellspacing=0    border=0 width='100%' class='input_table_box'>
								<col width='25%'>
								<col width='75%'>
								<tr bgcolor=#ffffff>
									<td class='input_box_title'><b>분류명 <img src='".$required2_path."'></b></td>
									<td class='input_box_item' style='padding-left:10px;'>
									<input type=text class=textbox1 name=title size=21 validation='true' title='분류명'>
									</td>
								</tr>
								<tr bgcolor=#ffffff>
									<td class='input_box_title'>등록이미지</td>
									<td class='input_box_item' style='padding-left:10px;'>
									<input type=file class=textbox1 name=file size=21 >
									</td>
								</tr>
								<tr bgcolor=#ffffff>
									<td class='input_box_title'>사용유무 </td>
									<td class='input_box_item' style='padding-left:10px;'>
									<input type=checkbox name=use_yn id=use_yn class=nonborder value='Y'> <label for='use_yn'>분류 사용</label>
									</td>
								</tr> 
							</table>

							</td>
						</tr>
						<tr bgcolor=#ffffff height=30>
							<td align=right nowrap width=60%>
								<table cellpadding=0 cellspacing=0>
									<tr>
										<td style='padding-right:5px;'>";
										if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
											$Contents .= "<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 id=delete style='cursor:hand;display:none' onclick=\"CategoryInput(document.cateform,'cate_delete')\">";
										}else{
											$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 id=delete style='cursor:pointer;display:none' ></a>";
										}
										$Contents .= "
										</td>
										<td>";
										if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
											$Contents .= "<img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 id=modify style='cursor:hand;display:none' onclick=\"CategoryInput(document.cateform,'cate_update')\">";
										}else{
											$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 id=modify style='cursor:pointer;display:none' ></a>";
										}
										$Contents .= "
										</td>
										<td>";
										if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
											$Contents .= "<img src='../images/".$admininfo["language"]."/btn_s_ok.gif' border=0 id=ok align=absmiddle style='cursor:hand' onclick=\"CategoryInput(document.cateform,'cate_insert')\">";
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
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록하실 제조사에 카테고리를 선택하시고 제조사명을 입력하시고 등록하시면 됩니다. </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >제조사 정보 수정을 원하시면 제조사 이름을 클릭해주세요 제조사 정보를 수정한다음 수정하기 버튼을 클릭합니다</td></tr>
</table>
";*/
	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');
$Contents .= HelpBox("분류관리", $help_text);

if($agent_type == "M"){
	if($mmode == "pop"){
		$Script = "<script language='JavaScript' src='../display/event.write.js'></script><script language='JavaScript' src='/admin/js/jquery-1.4.js'></Script>
	";
		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->Navigation = "HOME > 프로모션/전시 > 모바일 이벤트 분류관리";
		$P->NaviTitle = "모바일 이벤트 분류관리";
		$P->strLeftMenu = mshop_menu();
		$P->strContents = $Contents;
		echo $P->PrintLayOut();

	}else{
		$P = new LayOut();
		$P->addScript = $Script;
		$P->Navigation = $navigation;
		$P->title = $title;
		$P->strLeftMenu = mshop_menu();
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
}else{
	if($mmode == "pop"){
		$Script = "<script language='JavaScript' src='../display/event.write.js'></script><script language='JavaScript' src='/admin/js/jquery-1.4.js'></Script>
	";
		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->Navigation = "HOME > 프로모션/전시 > 이벤트 분류관리";
		$P->NaviTitle = "이벤트 분류관리";
		$P->strLeftMenu = product_menu();
		$P->strContents = $Contents;
		echo $P->PrintLayOut();

	}else{
		$Script = "<script language='JavaScript' src='event.write.js'></script>";
		$P = new LayOut();
		$P->addScript = $Script;
		$P->Navigation = "HOME > 프로모션/전시 > 이벤트 분류관리";
		$P->strLeftMenu = product_menu();
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
}


function MakerListevent()
{
global $db,$nset,$page, $auth_update_msg;
global $agent_type;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
	$where = " where 1 ";
	if($agent_type){
		$where .= " and agent_type = '".$agent_type."' ";
	}

	$db->query("SELECT mb.* FROM shop_event_relation mb $where ");
	$total = $db->total;
	$pagestring = page_bar($total, $page, $max, "&cid=$cid&depth=$depth&orderby=$orderby","");

	$bl = "<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>
		<tr height=30 bgcolor=#efefef align=center><td class='s_td'>번호</td><td class='m_td'>코드</td><td class='m_td'>이미지</td><td class='m_td'>분류명</td><td class='e_td'>사용유무</td></tr>";

	if ($db->total == 0)	{
		$bl = $bl."<tr height=100><td colspan=5 align=center>이벤트 분류 리스트가 없습니다.</td></tr>";
	}else{

		for($i=0 ; $i <$db->total ; $i++)
		{
			$db->fetch($i);
			if($db->dt[use_yn] == "Y"){
				$display_string = "사용";
			}else{
				$display_string = "사용안함";
			}

			$bl .= "<tr height=30 align=center>
				<td >".($i+1)."</td>
				<td id='er_ix_".$db->dt[er_ix]."'>".$db->dt[er_ix]."</td>
				<td>".($db->dt[file] == "" ? "-":$db->dt[file])."</td>
				<td id='title_".$db->dt[er_ix]."' rel='".$db->dt[use_yn]."' align=center>".(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") ? "<a href=\"javascript:cateEdit(document.cateform,'".$db->dt[er_ix]."');\">":"")."<u>".$db->dt[title]."</u></a>
				</td>
				<td>".$display_string."</td></tr>";
			//$bl = $bl."<tr height=1><td colspan=6 class='dot-x'></td></tr>";
		}
	}
	$bl = $bl."</table>";
	$bl = $bl."<table width=100% style='margin-top:10px;'><tr><td   align=center>".$pagestring."</td></tr></table>";

		return $bl;
}
/*
create table ".TBL_SHOP_COMPANY." (
	c_ix int(3) unsigned zerofill not null auto_increment,
	cid varchar(15) not null ,
	company_name varchar(100) null default null,
	disp char(1) null default '0',
	primary key(c_ix)
);
*/
?>