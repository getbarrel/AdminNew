<?
$script_time[start] = time();
include("../class/layout.class");
//include("../class/calender.class");
$script_time[start] = time();
//print_r($_SESSION);

$db = new Database; 

$Script = "
<script language='javascript' src='shop_main_v3_calender.js'></script>
<style type='text/css'>

</style>
<script language='JavaScript'>

</script>
";
	
$vdate = date("Ymd", time());
$befor30day = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)));

if($_SESSION["admininfo"]["admin_level"]==9){
	/*
	union
	select '프리미엄구매후기' as title , count(*) as cnt , sum(case when date_format(regdate , '%Y%m%d')='".$vdate."' then 1 else 0 end) as new_cnt ,'/admin/bbsmanage/bbs.php?mode=list&board=primium_after' as link from bbs_primium_after where date_format(regdate , '%Y%m%d') between '".$befor30day."'  and '".$vdate."'
	union
	select '-' as title , '0' as cnt , '0' as new_cnt ,'' as link
	*/

	$sql="select '1:1문의' as title , count(*) as cnt , sum(case when date_format(regdate , '%Y%m%d')='".$vdate."' then 1 else 0 end) as new_cnt ,'/admin/bbsmanage/bbs.php?mode=list&mmode=&board=qna' as link from bbs_qna where date_format(regdate , '%Y%m%d') between '".$befor30day."'  and '".$vdate."'
	union
	select '상품문의' as title , count(*) as cnt , sum(case when date_format(regdate , '%Y%m%d')='".$vdate."' then 1 else 0 end) as new_cnt ,'/admin/cscenter/product_qna.php' as link from shop_product_qna where date_format(regdate , '%Y%m%d') between '".$befor30day."'  and '".$vdate."'
	union
	select '일반구매후기' as title , count(*) as cnt , sum(case when date_format(regdate , '%Y%m%d')='".$vdate."' then 1 else 0 end) as new_cnt ,'/admin/bbsmanage/bbs.php?mode=list&board=after' as link from bbs_after where date_format(regdate , '%Y%m%d') between '".$befor30day."'  and '".$vdate."'

	union
	select '제휴문의' as title , count(*) as cnt , sum(case when date_format(regdate , '%Y%m%d')='".$vdate."' then 1 else 0 end) as new_cnt ,'/admin/cscenter/contactus_info.php' as link from shop_cooperation where date_format(regdate , '%Y%m%d') between '".$befor30day."'  and '".$vdate."'

	"; 

	$db->query($sql);
	$bbs_data = $db->fetchall();

	$Contents01 = "
	<div class='view_post'>
		<h3>게시물 상세내역 <span style='color:#ff3e0c;'>(최근 30일 자료만 노출)</span></h3>
		<table cellpadding='0' cellspacing='0' border='0' width='100%' class=''>
		<col width='35%' />
		<col width='15%' />
		<col width='35%' />
		<col width='15%' />
			<tr>
				<th>게시판 분류</th>
				<th style='padding-left:0;text-align:center;'>게시판수</th>
				<th>게시판 분류</th>
				<th style='padding-left:0;text-align:center;'>게시판수</th>
			</tr>";
			for($i=0;$i<count($bbs_data);$i=$i+2){
				$Contents01 .= "
				<tr>
					<td class='other_bg'><!--a href='".$bbs_data[$i][link]."' target='_blank'-->".$bbs_data[$i][title]." ".($bbs_data[$i][new_cnt] > 0 ? "<img src='./images/icon_new.png' width='16' style='position:relative;top:4px;' />" : "")."<!--/a--></td>
					<td>".number_format($bbs_data[$i][cnt])."</td>
					<td class='other_bg'><!--a href='".$bbs_data[$i+1][link]."' target='_blank'-->".$bbs_data[$i+1][title]." ".($bbs_data[$i+1][new_cnt] > 0 ? "<img src='./images/icon_new.png' width='16' style='position:relative;top:4px;' />" : "")."<!--/a--></td>
					<td>".number_format($bbs_data[$i+1][cnt])."</td>
				</tr>";
			}
		$Contents01 .= "
		</table>
	</div>
	";
}else{
	$Contents01 = "
	<div class='view_post'>
		<h3>게시물 상세내역 <span style='color:#ff3e0c;'>(최근 30일 자료만 노출)</span></h3>
		<table cellpadding='0' cellspacing='0' border='0' width='100%' class=''>
		<col width='35%' />
		<col width='15%' />
		<col width='35%' />
		<col width='15%' />
			<tr>
				<td colpan='3' align='center' height='40'>셀러는 이 매뉴를 사용하실수 없습니다.</td>
			</tr>
		</table>
	</div>
	";
}


$Contents = $Contents01;




	$P = new MobileLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = store_menu();
	$P->strContents = $Contents;
	$P->Navigation = "상품리스트";
	$P->TitleBool = false;
	$P->ServiceInfoBool = true;
	echo $P->PrintLayOut();



$script_time[end] = time();
if($admininfo[charger_id] == "forbiz"){
	//print_r($script_time);
}

?>
