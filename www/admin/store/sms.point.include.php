<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
//include($_SERVER["DOCUMENT_ROOT"]."/shop_bbs/shop_board.lib.php");

$db = new Database;
$mdb = new Database;

$Script = "	<script language='javascript'>


		function showObj(id)
		{
			obj = eval(id+'.style');
			obj.display = 'block';

			document.lyrstat.opend.value = id;
		}

		function hideObj(id)
		{
			obj = eval(id+'.style');
			obj.display = 'none';

			document.lyrstat.opend.value = '';
		}

		function swapObj(id)
		{

			obj = eval(id+'.style');
			stats = obj.display;

			if (stats == 'none')
			{
				if (document.lyrstat.opend.value)
					hideObj(document.lyrstat.opend.value);

				showObj(id);
			}
			else
			{
				hideObj(id);
			}
		}



		</script>";

		$sms_design = new SMS;
/*
$help_text = "	-  SMS 발송서비스는 포인트충전식으로 포인트가 있어야만 발송이 가능합니다. <br>
		- 충전금액은 발송건수에 따라 최저 20원입니다. (충전금액은 부가세 별도) <br>
		- 충전한 SMS 포인트는 환불되지 않습니다. <br>
		";*/

  $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' style='padding:0 0 0px 0;'> ".GetTitleNavigation($page_title, $page_navigation)."</td>
		</tr>
		<tr>
			<td align='left' colspan=4 style='padding-bottom:11px;'>
				 <div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' class='on' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='sms.point.php'>SMS 충전하기<span style='padding-left:2px' class='helpcloud' help_width='270' help_height='30' help_html='SMS 충전 신청 후 반드시 고객센터로 연락을 주셔야 정상적으로 적용이 가능합니다.'><img src='/admin/images/icon_q.gif' /></span></a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='sms.log.php'>SMS 발송목록</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='sms.log.detail.php'>SMS 발송 상세리스트</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
			</td>
		</tr>";
$mstring .= "
		<tr>
			<td align='left' style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' ><b class=blk> SMS 보유현황</b></div>")."</td>
		</tr>";
$mstring .= "
		<tr>
			<td align=left>
				".$sms_design->getSMSStatsUTF8($admininfo)."
			</td>
		</tr>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$mstring .= "
		<tr>
			<td align='left' style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' ><b class=blk> SMS 상품목록</b></div>")."</td>
		</tr>";
$mstring .= "
		<tr>
			<td align=right>
			<form name=smsp_form action='board.manage.act.php'><input type=hidden name=act value=insert>
			".$sms_design->getSMSProductListUTF8($admininfo)."
			</td>
		</tr>";
}
$mstring .= "
		<tr>
			<td align='left' style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif'><b class=blk> SMS 충전 목록</b></div>")."</td>
		</tr>
		<tr>
			<td>
			".$sms_design->getSMSProductRegistListUTF8($admininfo)."
			</td>
		</tr>
		<tr><td style='padding-bottom:10px;'>".HelpBox($page_title, $help_text)."</td></tr>
		</form>";
$mstring .="</table>";

$Contents = $mstring;

$P = new LayOut();
$P->addScript = $Script;
if($display_position == "store"){
	$P->strLeftMenu = store_menu();
}else{
	$P->strLeftMenu = campaign_menu();
}
$P->Navigation = $page_navigation;
$P->title = $page_title;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>
