<?
include("../class/layout.class");
//auth(9);

$db = new Database;
$mdb = new Database;

if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
	$menu_name = "관리자등록";
}else{
	$menu_name = "관리자등록";
}

$Contents = "
<!--<script language='javascript' src='../basic/member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script> 이건 뭐지용?-->

<table width='100%' border='0'  cellpadding=0 cellspacing=0 align='center'>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("전체사원리스트", "기초정보관리 > 본사관리 ")."</td>
</tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:20px;'> 
			<div class='tab'>
				<table class='s_org_tab'>
					<col width='550px'>
					<col width='*'>
					<tr>
						<td class='tab'>
							<table id='tab_01'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='./admin_manage_list.php'>관리자목록</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' class='on' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='./admin_manage.php'>관리자등록</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<!--
							<table id='tab_03' ".(($info_type == "basic" ) ? "class='on' ":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='./department.add.php?info_type=basic&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;본부/부서&nbsp;&nbsp; </a></td>
								<th class='box_03'></th>
							</tr>
							</table>
					
							<table id='tab_04' ".($info_type == "post_info" ? "class='on' ":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >";
								if($company_id == ""){
									$Contents .= "<a href='./department.add.php?info_type=post_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;직위&nbsp;&nbsp; </a>";
								}else{
									$Contents .= "<a href='./department.add.php?info_type=post_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;직위&nbsp;&nbsp; </a>";
								}
								$Contents .= "
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_05' ".($info_type == "position_info" ? "class='on' ":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >";
								if($company_id == ""){
									$Contents .= "<a href='./department.add.php?info_type=position_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;직책&nbsp;&nbsp; </a>";
								}else{
									$Contents .= "<a href='./department.add.php?info_type=position_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;직책&nbsp;&nbsp; </a>";
								}
								$Contents .= "

								</td>
								<th class='box_03'></th>
							</tr>
							</table>-->
						</td>
						<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'></td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>";

$page_type = 'store';
include ('../basic/member.add.php');
?>



