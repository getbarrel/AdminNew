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


$month= date("Ym");
$time = time();
$month_1 = date("Ym", strtotime("-1 month", $time));
$month_2 = date("Ym", strtotime("-2 month", $time));

if($regdate == "") $regdate = $month;

$mstring .="<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 >".GetTitleNavigation("키워드 광고", "마케팅지원 > 키워드 광고 ")."</td>
		</tr>
		<tr>
			<td align='left' colspan=4 style='padding-bottom:15px;'>
			    <div class='tab'>
					<table class='s_org_tab' style='width:100%' border=1>
					<tr>
						<td class='tab'>
							<table id='tab_01' ".($regdate == ""||$regdate == $month ? "class='on'":"")."  >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?regdate=$month'\">키워드 광고 안내</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<!--table id='tab_02' ".($regdate == $month_1 ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?regdate=".$month_1."'\">키워드 광고 신청</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' ".($regdate == $month_2 ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?regdate=".$month_2."'\">기타</td>
								<th class='box_03'></th>
							</tr>
							</table-->";
$mstring .= "
						</td>
						<td class='btn' align=right>
						</td>
					</tr>
					</table>
					</div>
			</td>
		</tr>";
$mstring .="</table>";

$mstring .="<table cellpadding=5 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td>
				<div class='contentsbox_area'>
	<div class='title_01'>
		<strong>신청하기</strong>	<span>*신청 클릭 후 문의사항을 남겨주시면 상담연락 드리겠습니다.</span>
	</div>
	<table cellpadding='0' cellspacing='0' border='0' width='100%' class='application_box'>
		<col width='110' />
		<col width='133' />
		<col width='*' />
		<col width='166' />
		<tr>
			<td>
				<img src='../images/marketing/naver_icon.jpg' alt='네이버' title='네이버' />
			</td>
			<td class='company_text'>
				<strong>네이버 키워드</strong>
			</td>
			<td style='text-align:left;'>
				<div style='margin-left:10px;'>
					<a href='#'><img src='../images/marketing/popularity_btn.gif' alt='인기' title='인기' /></a>
					<a href='#'><img src='../images/marketing/recommend_btn.gif' alt='추천' title='추천' /></a>
				</div>
			</td>
			<td>
				<a href='https://www.mallstory.com/customer/bbs.php?board=qna' target='_blank'><img src='../images/marketing/apply_now_btn.gif' alt='빠른상담 신청하기' title='빠른상담 신청하기' /></a>
			</td>
		</tr>
		<tr>
			<td>
				<img src='../images/marketing/daum_icon.jpg' alt='다음' title='다음' />
			</td>
			<td class='company_text'>
				<strong>다음 키워드</strong>
			</td>
			<td style='text-align:left;'>
				<div style='margin-left:10px;'>
					<a href='#'><img src='../images/marketing/popularity_btn.gif' alt='인기' title='인기' /></a>
					<a href='#'><img src='../images/marketing/recommend_btn.gif' alt='추천' title='추천' /></a>
				</div>
			</td>
			<td>
				<a href='https://www.mallstory.com/customer/bbs.php?board=qna' target='_blank'><img src='../images/marketing/apply_now_btn.gif' alt='빠른상담 신청하기' title='빠른상담 신청하기' /></a>
			</td>
		</tr>
		<tr>
			<td>
				<img src='../images/marketing/nate_icon.jpg' alt='네이트' title='네이트' />
			</td>
			<td class='company_text'>
				<strong>네이트 키워드</strong>
			</td>
			<td style='text-align:left;'>
				<div style='margin-left:10px;'>
					<a href='#'><img src='../images/marketing/popularity_btn.gif' alt='인기' title='인기' /></a>
					<a href='#'><img src='../images/marketing/recommend_btn.gif' alt='추천' title='추천' /></a>
				</div>
			</td>
			<td>
				<a href='https://www.mallstory.com/customer/bbs.php?board=qna' target='_blank'><img src='../images/marketing/apply_now_btn.gif' alt='빠른상담 신청하기' title='빠른상담 신청하기' /></a>
			</td>
		</tr>
		<tr>
			<td>
				<img src='../images/marketing/overture_icon.jpg' alt='오버츄어' title='오버츄어' />
			</td>
			<td class='company_text'>
				<strong>오버츄어</strong>
			</td>
			<td style='text-align:left;'>
				<div style='margin-left:10px;'>
					<a href='#'><img src='../images/marketing/popularity_btn.gif' alt='인기' title='인기' /></a>
					<a href='#'><img src='../images/marketing/recommend_btn.gif' alt='추천' title='추천' /></a>
				</div>
			</td>
			<td>
				<a href='https://www.mallstory.com/customer/bbs.php?board=qna' target='_blank'><img src='../images/marketing/apply_now_btn.gif' alt='빠른상담 신청하기' title='빠른상담 신청하기' /></a>
			</td>
		</tr>
	</table>
	<div class='title_01'>
		<strong>키워드/검색광고란?</strong>
	</div>
	<div>
		<img src='../images/marketing/keywords_img.jpg' alt='키워드/검색광고' title='키워드/검색광고' class='img_box'/>
	</div>
</div>
			</td>
		</tr>
		</table>
		";
$Contents = $mstring;

//$Script = "<script language='javascript' src='basicinfo.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = marketting_menu();
$P->Navigation = "마케팅지원 > 키워드/검색광고";
$P->title = "키워드/검색광고";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>
