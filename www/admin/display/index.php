<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;

$Script = "<script language='javascript'>
function eventDelete(pg_ix){
	if(confirm('해당 프로모션  정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다.'))
	{
		window.frames['act'].location.href= 'promotion_goods.act.php?act=delete&pg_ix='+pg_ix;//kbk
		//document.getElementById('act').src= 'promotion_goods.act.php?act=delete&pg_ix='+pg_ix;
	}


}
</script>";
if(!$guide_type) $guide_type = "main";

$mstring ="<form name=poll action='board.manage.act.php'><input type=hidden name=act value=insert>
		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("빠른배너 관리", "전시관리 > 빠른배너 관리 ")."</td>
		</tr>
		<tr>
			<td align='left' colspan=4 style='padding-bottom:15px;'>
			    <div class='tab'>
					<table class='s_org_tab' style='width:100%' border=0>
					<tr>
						<td class='tab'>
";
$mstring .= "<table id='tab_01' ".($guide_type == "main" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?guide_type=main'\">사이트 메인</td>
								<th class='box_03'></th>
							</tr>
							</table>";

$mstring .= "<table id='tab_06' ".($guide_type == "SOHO" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?guide_type=SOHO'\">SOHO</td>
								<th class='box_03'></th>
							</tr>
							</table>";
$mstring .= "<table id='tab_05' ".($guide_type == "DESIGNER" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?guide_type=DESIGNER'\">DESIGNER</td>
								<th class='box_03'></th>
							</tr>
							</table>";
//if($admininfo[admin_level] == 9 && $admininfo[mall_type] == "O"){
$mstring .= "<table id='tab_04' ".($guide_type == "MIRROPICK" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?guide_type=MIRROPICK'\">MIRROPICK</td>
								<th class='box_03'></th>
							</tr>
							</table>";
$mstring .= "<table id='tab_02' ".($guide_type == "BRAND" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?guide_type=BRAND'\">BRAND</td>
								<th class='box_03'></th>
							</tr>
							</table>";
$mstring .= "<table id='tab_03' ".($guide_type == "DC" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?guide_type=DC'\">DC</td>
								<th class='box_03'></th>
							</tr>
							</table>";
$mstring .= "<table id='tab_03' ".($guide_type == "MODONG" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?guide_type=MODONG'\">MODONG</td>
								<th class='box_03'></th>
							</tr>
							</table>";
$mstring .= "<table id='tab_03' ".($guide_type == "EVENTS" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?guide_type=EVENTS'\">EVENTS</td>
								<th class='box_03'></th>
							</tr>
							</table>";
//}
$mstring .= "
						</td>
						<td class='btn' align=right>
							<!--a href='promotion_category.php'><img src='../images/".$admininfo["language"]."/btn_promotion_type.gif' align=absmiddle></a-->
						</td>
					</tr>
					</table>
					</div>
			</td>
		</tr>
		<tr>
			<td>
			<img src='../images/promotion_guide/{$guide_type}.jpg' usemap='#{$guide_type}'>
			</td>
		</tr>
		</form>";
$mstring .="</table>";

if($guide_type == "main"){
	$mstring .="
		<map id=\"{$guide_type}\" name=\"{$guide_type}\" >
		  <area shape=\"rect\" coords=\"45,149,245,324\" alt='' title='메인배너_이벤트' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=46&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"45,332,245,509\" alt='' title='메인배너_베스트셀링' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=47&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"45,446,245,687\" alt='' title='메인배너_신상품' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=48&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"45,687,245,870\" alt='' title='메인배너_스타일트렌디' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=49&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"45,872,245,1050\" alt='' title='메인배너_브랜드스토리' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=92&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"45,1199,1237,5673\" alt='' title='메인페이지_상품전시' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/main_goods.php?mmode=pop&mg_ix=10&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		</map>
		";
} else if($guide_type == "SOHO"){
	$mstring .="
		<map id=\"{$guide_type}\" name=\"{$guide_type}\" >
		  <area shape=\"rect\" coords=\"230,314,1228,763\" alt='' title='소호_움직이는배너' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=51&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"47,788,617,912\" alt='' title='소호_일반좌측배너' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=52&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"639,787,1207,911\" alt='' title='소호_일반우측배너' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=53&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"30,982,1230,3452\" alt='' title='소호_전시상품' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/promotion_goods.php?mmode=pop&pg_ix=10&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">
		</map>
		";
} else if($guide_type == "DESIGNER"){
	$mstring .="
		<map id=\"{$guide_type}\" name=\"{$guide_type}\" >
		  <area shape=\"rect\" coords=\"247,282,1237,726\" alt='' title='디자이너_움직이는배너' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=54&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"68,751,636,878\" alt='' title='디자이너_일반좌측배너' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=55&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"657,750,1223,879\" alt='' title='디자이너_일반우측배너' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=86&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"46,1047,1234,3442\" alt='' title='디자이너_전시상품' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/promotion_goods.php?mmode=pop&pg_ix=11&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">
		</map>
		";
} else if($guide_type == "MIRROPICK"){
	$mstring .="
		<map id=\"{$guide_type}\" name=\"{$guide_type}\" >
		  <area shape=\"rect\" coords=\"39,280,935,730\" alt='' title='미러픽_움직이는배너' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=57&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"938,280,1237,729\" alt='' title='미러픽_전시상품' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/promotion_goods.php?mmode=pop&pg_ix=12&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"37,926,1237,2861\" alt='' title='미러픽_전시상품_BEST/NEW' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/promotion_goods.php?mmode=pop&pg_ix=12&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">
		</map>
		";
} else if($guide_type == "BRAND"){
	$mstring .="
		<map id=\"{$guide_type}\" name=\"{$guide_type}\" >
		  <area shape=\"rect\" coords=\"34,560,620,1057\" alt='' title='브랜드_탑셀러노출관리' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/seller/company.add.php?info_type=seller_info&company_id=31a07ce237d0baa0b0f168ee5c8cd683&mmode=&code=4b5e226aa6d42cf7e5e7fa659b02b2c8','pop','width=900,height=700,scrollbar=auto'); \">
		</map>
		";
} else if($guide_type == "DC"){
	$mstring .="
		<map id=\"{$guide_type}\" name=\"{$guide_type}\" >
		  <area shape=\"rect\" coords=\"1014,280,1215,429\" alt='' title='DC_움직이는배너1' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=91&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"1014,431,1215,577\" alt='' title='DC_움직이는배너2' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=90&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"1014,581,1215,727\" alt='' title='DC_움직이는배너3' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=58&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"14,856,1202,1320\" alt='' title='DC_기획전' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/event.write.php?event_ix=41','pop','width=900,height=700,scrollbar=auto'); \">
		</map>
		";
} else if($guide_type == "MODONG"){
	$mstring .="
		<map id=\"{$guide_type}\" name=\"{$guide_type}\" >
		  <area shape=\"rect\" coords=\"30,281,1234,1002\" alt='' title='MODONG_상단전시' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/product/contents_goods_input.php?ci_ix=1','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"30,1040,1223,2470\" alt='' title='MODONG_하단리스트' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/product/contents_goods_list.php','pop','width=900,height=700,scrollbar=auto'); \">

		</map>
		";
} else if($guide_type == "EVENTS"){
	$mstring .="
		<map id=\"{$guide_type}\" name=\"{$guide_type}\" >
		  <area shape=\"rect\" coords=\"26,281,1225,730\" alt='' title='EVENT_상단배너' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/banner_write.php?mmode=pop&banner_ix=60&SubID=SM22464243Sub','pop','width=900,height=700,scrollbar=auto'); \">

		  <area shape=\"rect\" coords=\"26,793,1228,1327\" alt='' title='MODONG_하단리스트' href='javascript:void(0);' onclick=\"javascript:window.open('/admin/display/event.list.php','pop','width=900,height=700,scrollbar=auto'); \">

		</map>
		</map>
		";
}
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


$help_text = HelpBox("빠른배너 관리", $help_text);

$Contents = $mstring.$help_text;


$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션/전시 > 전시관리 메인";
$P->title = "전시관리 메인";
$P->TitleBool = false;
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>
