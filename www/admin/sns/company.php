<?
include("../class/layout.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');

$db = new Database;

$Contents = "
	<table cellpadding=3 cellspacing=0 border=0 width=100%>
	 <tr height=40>
	    <td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("제조사관리", "SNS 상품관리 > 제조사관리")."</td>
	</tr>
	<tr height=10>
		<td align=rihgt style='padding-left:20px;' valign=top>
		<table width='100%' border=0>
			<tr height=30><td width=100%><img src='/admin/images/dot_org.gif' align=absmiddle> <b>제조사 목록 </b><br></td></tr>
		</table>
		".MakerList()."
		</td>

		<td rowspan=6 width=50% valign=top style='padding-left:20px;'>

					<form name='companyform' action='./company.act.php' method='post' enctype='multipart/form-data'>
					<input type=hidden name=mode value=insert>
					<input type=hidden name=c_ix value=''>
					<input type=hidden name=mmode value='$mmode'>

				  	<table border='0' cellspacing='0' cellpadding='0' width='100%'>
				        	<tr>
				        		<td bgcolor='#F8F9FA'>
							<table width='100%' border=0>
								<tr height=30><td width=100%><img src='/admin/images/dot_org.gif' align=absmiddle> <b>제조사 추가하기 </b><br></td></tr>
							</table>
							<table cellpadding=0 cellspacing=0  border=0 width='100%' class='line_color' >
								<col width='25%'>
								<col width='75%'>
								<tr bgcolor=#ffffff>
									<td class='leftmenu' ><b>카테고리 <img src='".$required2_path."'></b></td>
									<td style='padding-left:20px;'>
									".getCategoryList()."
									</td>
								</tr>
								<!--tr height=1><td colspan=3 class='dot-x'></td></tr-->

								<tr bgcolor=#ffffff>
									<td class='leftmenu'><b>제조사명 <img src='".$required2_path."'></b></td>
									<td style='padding-left:20px;'>
									<input type=text name=company size=15 validation='true' title='제조사명'>
									</td>

								</tr>
								<tr bgcolor=#ffffff>
									<td class='leftmenu'>사용유무 </td>
									<td style='padding-left:20px;'>
									<input type=checkbox name=disp class=nonborder value=1> 제조사 사용
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
										<td style='padding-right:5px;'><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 id=delete style='cursor:hand;display:none' onclick=\"CompanyInput(document.companyform,'delete')\"></td>
										<td><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 id=modify style='cursor:hand;display:none' onclick=\"CompanyInput(document.companyform,'update')\"></td>
										<td><img src='../images/".$admininfo["language"]."/btn_s_ok.gif' border=0 id=ok align=absmiddle style='cursor:hand' onclick=\"CompanyInput(document.companyform,'insert')\"></td>
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

$Contents .= HelpBox("제조사관리", $help_text);


if($mmode == "pop"){
	$Script = "<script language='JavaScript' src='company.js'></script>";
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->Navigation = "HOME > 상품관리 > 제조사관리";
	$P->NaviTitle = "제조사관리";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$Script = "<script language='JavaScript' src='company.js'></script>";
	$P = new LayOut();
	$P->addScript = $Script;
	$P->Navigation = "HOME > 상품관리 > 제조사관리";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}



function MakerList()
{
global $db,$nset,$page;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
	$db->query("SELECT mb.* FROM ".TBL_SNS_COMPANY." mb ");
	$total = $db->total;
	$pagestring = page_bar($total, $page, $max, "&cid=$cid&depth=$depth&orderby=$orderby","");
	$db->query("SELECT mb.*, mc.cname FROM ".TBL_SNS_COMPANY." mb left join ".TBL_SNS_CATEGORY_INFO." mc on mb.cid = mc.cid LIMIT $start,$max");

	$bl = "<table cellpadding=0 cellspacing=0 width=100%>
		<tr height=25 bgcolor=#efefef align=center><td class='s_td'>번호</td><td class='m_td'>코드</td><td class='m_td'>카테고리</td><td class='m_td'>제조사이름</td><td class='e_td'>사용유무</td><!--td class='e_td'>검색표시유무</td--></tr>";

	if ($db->total == 0)	{
		$bl = $bl."<tr height=100><td colspan=6 align=center>제조사 리스트가 존재 없습니다.</td></tr>";
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


			$bl = $bl."<tr height=25 align=center><td >".($i+1)."</td><td>".$db->dt[c_ix]."</td><td>".$db->dt[cname]."</td><td align=center><a href=\"JavaScript:ViewCompanyImage('".$db->dt[c_ix]."')\">".$db->dt[company_name]."</a></td><td>".$display_string."</td><!--td>".$search_disp_string."</td--></tr>";
			$bl = $bl."<tr height=1><td colspan=6 class='dot-x'></td></tr>";
		}
	}
	$bl = $bl."<tr height=20><td></td></tr><tr><td colspan=4 align=center>".$pagestring."</td></tr></table>";

		return $bl;
}
/*
create table ".TBL_SNS_COMPANY." (
	c_ix int(3) unsigned zerofill not null auto_increment,
	cid varchar(15) not null ,
	company_name varchar(100) null default null,
	disp char(1) null default '0',
	primary key(c_ix)
);
*/
?>