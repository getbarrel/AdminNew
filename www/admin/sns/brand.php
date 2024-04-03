<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');

$db = new Database;

$Contents = "
	<table cellpadding=3 cellspacing=0 border=0 width=100%>
	<tr>
		<td colspan=2>
			".GetTitleNavigation("브랜드관리", "SNS 상품관리 > 브랜드관리")."
		</td>
	</tr>
	<tr height=10>
		<td align=rihgt style='padding-right:20px;' valign=top>
		<table width='100%' border=0>
			<tr height=30><td width=100%><img src='/admin/images/dot_org.gif' align=absmiddle> <b>브랜드목록</b><br></td></tr>
		</table>
		<table width='100%' border=0>
			<form method='POST' action='brand.php?mmode=pop'>
				<tr>
					<td width=100% height=30>
						<select name='search_type' style='font-size:12px;' align=absmiddle>
							<option value='b_ix'>브랜드코드</option>
							<option value='brand_name'>브랜드이름</option>
						</select>
						<input type='text' class='textbox' name='search_text' align=absmiddle> <img src='../images/".$admininfo["language"]."/btc_search.gif' value='검색' align=absmiddle>
					</td>
				</tr>
			</form>
		</table>
		".BrandList()."
		</td>
		<td rowspan=6 width=60% valign=top>
		<!--table class='mbox04' style='width:400px;height:20px' >
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05 align=center' -->
					<form name='brandform' action='./brand.act.php' method='post'  onsubmit=\"return BrandInput(this,'insert');\" enctype='multipart/form-data' target=act>
					<input type=hidden name=mode value=insert>
					<input type=hidden name=b_ix value=''>
					<input type='hidden' name='top_design' value=''>
					<input type='hidden' name='mmode' value='$mmode'>

				  	<table border='0' cellspacing='1' cellpadding='7' width='100%'>
				        	<tr>
				        		<td bgcolor='#F8F9FA'>
							<table width='100%' border=0>
								<tr height=30><td width=100%><img src='/admin/images/dot_org.gif' align=absmiddle> <b>브랜드 추가하기 </b><br></td></tr>
							</table>
							<table cellpadding=0 cellspacing=0   border=0 width='100%' class='line_color'>
								<col width='30%'>
								<col width='70%'>
								<tr >
									<td class='leftmenu' ><b>카테고리 <img src='".$required2_path."'></b></td>
									<td style='padding-left:10px;'>
									".getCategoryList()."
									</td>
								</tr>
								<!--tr height=1><td colspan=3 class='dot-x'></td></tr-->
								<tr bgcolor=#ffffff>
									<td class='leftmenu'><b>사용유무 <img src='".$required2_path."'></b></td>
									<td style='padding-left:10px;'>";
if($admininfo[admin_level] == 9){
$Contents .= "		<input type=radio name=disp class=nonborder value=0 id='disp_0' validation=false title='사용유무' ><label for='disp_0'>사용하지않음</label>
									<input type=radio name=disp class=nonborder value=1 id='disp_1' validation=false title='사용유무'><label for='disp_1'>사용</label>
									<input type=radio name=disp class=nonborder value=2 id='disp_2' validation=false title='사용유무' checked><label for='disp_2'>신청</label>";
}else if($admininfo[admin_level] == 8){

$Contents .= "		<div style='display:none;'><input type=radio name=disp class=nonborder value=0 id='disp_0' validation=false title='사용유무'><label for='disp_0'>사용하지않음</label>
									<input type=radio name=disp class=nonborder value=1 id='disp_1' validation=false title='사용유무'><label for='disp_1'>사용</label></div>
									<input type=radio name=disp class=nonborder value=2 id='disp_2' validation=false title='사용유무' checked><label for='disp_2'>신청</label>";
}
$Contents .= "		</td>
								</tr>
								<!--tr height=1><td colspan=3 class='dot-x'></td></tr-->
								<tr bgcolor=#ffffff style='display:none;'>
									<td class='leftmenu'>검색표시 </td>
									<td style='padding-left:10px;' >
									<input type=checkbox name=search_disp class=nonborder value=1>
									</td>
								</tr>
								<!--tr height=1><td colspan=3 class='dot-x'></td></tr-->

								<tr bgcolor=#ffffff>
									<td class='leftmenu'><b>브랜드명 <img src='".$required2_path."'></b></td>
									<td style='padding-left:10px;'>
									<input type=text name=brand size=15 validation=true title='브랜드명'>
									</td>
								</tr>
								<tr bgcolor=#ffffff>
									<td class='leftmenu' nowrap><b>브랜드 간략설명 <img src='".$required2_path."'></b></td>
									<td style='padding-left:10px;' class=small>
									<input type='text' name='shotinfo' style='width:95%;height:40px' value='".$shotinfo."' maxlength='80'>
									</td>
								</tr>
								<!--tr height=1><td colspan=3 class='dot-x'></td></tr-->
								<tr bgcolor=#ffffff>
									<td class='leftmenu' nowrap>브랜드 이미지 <span class=small>(130*50)</span></td>
									<td style='padding-left:10px;'>
									<input type=file name='brandimg' class='textbox' size=15 style='font-size:8pt;'>
									</td>
								</tr>
								<tr bgcolor=#ffffff>
									<td class='leftmenu' nowrap>브랜드 이미지</td>
									<td style='padding-left:10px;' id='brandimgarea' class=small>

									</td>
								</tr>

							</table>
							<table width='100%' border=0>
								<tr height=30><td width=100%><img src='/admin/images/dot_org.gif' align=absmiddle> <b>브랜드페이지 상단 디자인</b><br></td></tr>
							</table>
							<table cellpadding=3 cellspacing=1  bgcolor=#c0c0c0 border=0 width='100%'>
								<tr bgcolor=#ffffff>
									<td colspan=2 style='padding:0px;'>".WebEdit()."</td>
								</tr>
							</table>
								<!--tr height=1><td colspan=3 class='dot-x'></td></tr-->
							<table width='100%' cellpadding=0 cellspacing=0 border=0>
								<tr >
									<td colspan=2 align=right valign=top style='padding:0px;padding-right:0px;'>
									<a href='javascript:doToggleText();' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
				          			      <a href='javascript:doToggleHtml();' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
									</td>
								</tr>
								<tr >
									<td align=right nowrap width=100%>
										<table cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 id=delete style='cursor:pointer;display:none' onclick=\"BrandSubmit(document.brandform,'delete')\"></td>
											<td><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 id=modify style='cursor:pointer;display:none' onclick=\"BrandSubmit(document.brandform,'update')\"></td>
										</tr>
										</table>
									</td>
									<td align=right><img src='../images/".$admininfo["language"]."/btn_s_ok.gif' id=ok border=0 align=absmiddle style='cursor:pointer' onclick=\"BrandSubmit(document.brandform,'insert')\"></td>
								</tr></form>
							</table>
							</td>
						</tr>
					</table>
				<!--/td>
				<th class='box_06'></th>
			</tr>
			<tr>
				<th class='box_07'></th>
				<td class='box_08'></td>
				<th class='box_09'></th>
			</tr>
		</table--><br>
		</td>

	</tr>
	</table>
	<iframe name='extand' id='extand' src='' width=0 height=0></iframe>";

/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >브랜드 상단별 페이지 상단 부분의 디자인을 하여 <b>브랜드 페이지</b>를 구성할수 있습니다 </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >브랜드 정보 수정을 원하시면 브랜드 이름을 클릭해주세요 브랜드 정보를 수정한다음 수정하기 버튼을 클릭합니다</td></tr>
</table>
";*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("브랜드관리", $help_text);


if($mmode == "pop"){
	$Script = "<script language='JavaScript' src='brand.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>";
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
//	$P->OnloadFunction = "Init(document.brandform);MM_preloadImages('../webedit/images/wtool1_1.gif','../webedit/images/wtool2_1.gif','../webedit/images/wtool3_1.gif','../webedit/images/wtool4_1.gif','../webedit/images/wtool5_1.gif','../webedit/images/wtool6_1.gif','../webedit/images/wtool7_1.gif','../webedit/images/wtool8_1.gif','../webedit/images/wtool9_1.gif','../webedit/images/wtool11_1.gif','../webedit/images/wtool13_1.gif','../webedit/images/wtool10_1.gif','../webedit/images/wtool12_1.gif','../webedit/images/wtool14_1.gif','../webedit/images/bt_html_1.gif','../webedit/images/bt_source_1.gif')"; //showSubMenuLayer('storeleft');
	$P->OnloadFunction = "Init(document.brandform);";
	$P->Navigation = "HOME > 상품관리 > 브랜드관리";
	$P->NaviTitle = "브랜드관리";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$Script = "<script language='JavaScript' src='brand.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>";
	$P = new LayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "Init(document.brandform);MM_preloadImages('../webedit/image/wtool1_1.gif','../webedit/image/wtool2_1.gif','../webedit/image/wtool3_1.gif','../webedit/image/wtool4_1.gif','../webedit/image/wtool5_1.gif','../webedit/image/wtool6_1.gif','../webedit/image/wtool7_1.gif','../webedit/image/wtool8_1.gif','../webedit/image/wtool9_1.gif','../webedit/image/wtool11_1.gif','../webedit/image/wtool13_1.gif','../webedit/image/wtool10_1.gif','../webedit/image/wtool12_1.gif','../webedit/image/wtool14_1.gif','../webedit/image/bt_html_1.gif','../webedit/image/bt_source_1.gif')"; //showSubMenuLayer('storeleft');
	$P->Navigation = "HOME > 상품관리 > 브랜드관리";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}








function BrandList()
{
global $db, $admininfo,$nset,$page,$search_text,$search_type;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	if($search_text == ""){
		if($admininfo[admin_level] == "9"){
			$db->query("SELECT mb.*, mc.cname FROM ".TBL_SNS_BRAND." mb left join ".TBL_SNS_CATEGORY_INFO." mc on mb.cid = mc.cid order by regdate desc ");
		}else{
			$db->query("SELECT mb.*, mc.cname FROM ".TBL_SNS_BRAND." mb left join ".TBL_SNS_CATEGORY_INFO." mc on mb.cid = mc.cid where mb.company_id = '".$admininfo[company_id]."' order by regdate desc ");
		}
	}else{
		if($search_type == "brand_name"){
			if($admininfo[admin_level] == "9"){
				$db->query("SELECT mb.*, mc.cname FROM ".TBL_SNS_BRAND." mb left join ".TBL_SNS_CATEGORY_INFO." mc on mb.cid = mc.cid where $search_type LIKE '%$search_text%' order by regdate desc  ");
			}else{
				$db->query("SELECT mb.*, mc.cname FROM ".TBL_SNS_BRAND." mb left join ".TBL_SNS_CATEGORY_INFO." mc on mb.cid = mc.cid where mb.company_id = '".$admininfo[company_id]."' and $search_type LIKE '%$search_text%' order by regdate desc  ");
			}
		}else{
			if($admininfo[admin_level] == "9"){
				$db->query("SELECT mb.*, mc.cname FROM ".TBL_SNS_BRAND." mb left join ".TBL_SNS_CATEGORY_INFO." mc on mb.cid = mc.cid where $search_type = '$search_text' order by regdate desc  ");
			}else{
				$db->query("SELECT mb.*, mc.cname FROM ".TBL_SNS_BRAND." mb left join ".TBL_SNS_CATEGORY_INFO." mc on mb.cid = mc.cid where mb.company_id = '".$admininfo[company_id]."' and $search_type = '$search_text' order by regdate desc  ");
			}
		}
	}
	$total = $db->total;

	if($search_text == ""){
		if($admininfo[admin_level] == "9"){
			$db->query("SELECT mb.*, mc.cname FROM ".TBL_SNS_BRAND." mb left join ".TBL_SNS_CATEGORY_INFO." mc on mb.cid = mc.cid order by regdate desc  limit $start,$max");
		}else{
			$db->query("SELECT mb.*, mc.cname FROM ".TBL_SNS_BRAND." mb left join ".TBL_SNS_CATEGORY_INFO." mc on mb.cid = mc.cid where mb.company_id = '".$admininfo[company_id]."' order by regdate desc  limit $start,$max");
		}
	}else{
		if($search_type == "brand_name"){
			if($admininfo[admin_level] == "9"){
				$db->query("SELECT mb.*, mc.cname FROM ".TBL_SNS_BRAND." mb left join ".TBL_SNS_CATEGORY_INFO." mc on mb.cid = mc.cid where $search_type LIKE '%$search_text%' order by regdate desc  limit $start,$max");
			}else{
				$db->query("SELECT mb.*, mc.cname FROM ".TBL_SNS_BRAND." mb left join ".TBL_SNS_CATEGORY_INFO." mc on mb.cid = mc.cid where mb.company_id = '".$admininfo[company_id]."' and $search_type LIKE '%$search_text%' order by regdate desc  limit $start,$max");
			}
		}else{
			if($admininfo[admin_level] == "9"){
				$db->query("SELECT mb.*, mc.cname FROM ".TBL_SNS_BRAND." mb left join ".TBL_SNS_CATEGORY_INFO." mc on mb.cid = mc.cid where $search_type = '$search_text' order by regdate desc  limit $start,$max");
			}else{
				$db->query("SELECT mb.*, mc.cname FROM ".TBL_SNS_BRAND." mb left join ".TBL_SNS_CATEGORY_INFO." mc on mb.cid = mc.cid where mb.company_id = '".$admininfo[company_id]."' and $search_type = '$search_text' order by regdate desc  limit $start,$max");
			}
		}
	}
	$pagestring = page_bar($total, $page, $max, "&cid=$cid&depth=$depth&orderby=$orderby","");
	$bl = "<table cellpadding=0 cellspacing=0 width=350>
		<tr height=25 bgcolor=#efefef align=center><td class='s_td'>브랜드코드</td><!--td class='m_td'>코드</td--><td class='m_td'>카테고리</td><td class='m_td'>브랜드이름</td><td class='e_td'>사용유무</td><!--td class='e_td'>검색표시유무</td--></tr>";

	if ($db->total == 0)	{
		$bl = $bl."<tr height=100><td colspan=6 align=center>브랜드 리스트가 존재 없습니다.</td></tr>";
	}else{

		for($i=0 ; $i <$db->total ; $i++)
		{
			$db->fetch($i);
			if($db->dt[disp] == 1){
				$display_string = "사용";
			}else if($db->dt[disp] == 2){
				$display_string = "신청";
			}else{
				$display_string = "사용안함";
			}

			if($db->dt[search_disp] == 1){
				$search_disp_string = "표시";
			}else{
				$search_disp_string = "표시하지않음";
			}


			if($admininfo[admin_level] == 9){
				$brand_name = "<a href=\"JavaScript:ViewBrandImage('".$db->dt[b_ix]."')\">".$db->dt[brand_name]."</a>";
			}else if($admininfo[admin_level] == 8){
				if($admininfo[company_id] == $db->dt[company_id]){
					$brand_name = "<a href=\"JavaScript:ViewBrandImage('".$db->dt[b_ix]."')\"><u>".$db->dt[brand_name]."</u></a>";
				}else{
					$brand_name = $db->dt[brand_name];
				}
			}

			$bl = $bl."<tr height=27 align=center><td >".$db->dt[b_ix]."</td><!--td><a href=\"JavaScript:ViewBrandImage('".$db->dt[b_ix]."')\">".$db->dt[b_ix]."</a></td-->
			<td class=small>".($db->dt[cname] == "" ? "전체":$db->dt[cname])."</td><td align=center>$brand_name</td><td>".$display_string."</td><!--td class=small>".$search_disp_string."</td--></tr>";
			$bl = $bl."<tr height=1><td colspan=6 class='dot-x'></td></tr>";
		}
	}

	$bl = $bl."<tr height=20><td></td></tr><tr><td colspan=4 align=center>".$pagestring."</td></tr></table>";

		return $bl;
}
/*
create table ".TBL_SNS_BRAND." (
	b_ix int(3) unsigned zerofill not null auto_increment,
	brand_name varchar(100) null default null,
	disp char(1) null default '0',
	primary key(b_ix)
);
*/
?>