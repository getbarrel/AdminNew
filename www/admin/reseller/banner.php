<?
include("../class/layout.class");


$db = new Database();


$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
			<td align='left' colspan=9> ".GetTitleNavigation("베너(위젯)관리", "리셀러관리> 환경설정> 베너(위젯)관리")."</td>
	  </tr>
		<tr>
			<td align='right' colspan=9 style='padding-bottom:10px;'></td>
		</tr>
	  <tr>
		<td align='left' colspan='7' style='padding-bottom:15px;'>
		
		</td>
	   </tr>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
	    <col style='width:50px;'>

	    <col style='width:150px;'>
	    <col style='width:140px;'>
		<col style='width:*;'>
	    <col style='width:150px;'>
	    <col style='width:120px;'>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 번호</td>
		
	    <td class='m_td'> 배너명</td>
	    <td class='m_td'> 치환함수</td>
		<td class='m_td'> 이미지</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";
$db = new Database;
global $page;
$max = 20;

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}
$paging = "view";

	$db->query("select * from reseller_banner order by regdate DESC");
	$total = $db->total;

	$db->query("select * from reseller_banner order by regdate DESC limit $start , $max");
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max",$paging);



if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$no = $total - ($page - 1) * $max - $i;


	$Contents02 .= "
		  <tr bgcolor=#ffffff align=center>
		    <td class='list_box_td list_bg_gray'>".$no."</td>
			
		    <td class='list_box_td point'> ".$db->dt[banner_name]."</td>
			<td class='list_box_td'>{=getBannerInfo(".$db->dt[rsl_banner_ix].")}</td>";
if(substr_count($db->dt[banner_img],'.swf') > 0){
	$Contents02 .= "
			<td class='list_box_td list_bg_gray' style='padding:5px;'>
				<script language='javascript'>generate_flash('".$admin_config[mall_data_root]."/images/banner/".$db->dt[rsl_banner_ix]."/".$db->dt[banner_img]."', '".$db->dt[banner_width]."', '".$db->dt[banner_height]."');</script>
			</td>";
}else{
	$image_info = getimagesize ($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$db->dt[rsl_banner_ix]."/".$db->dt[banner_img]);
	//print_r($image_info);
	if($image_info[0] > 300){
		$Contents02 .= "<td class='list_box_td list_bg_gray' style='padding:5px;'><img src='".$admin_config[mall_data_root]."/images/banner/".$db->dt[rsl_banner_ix]."/".$db->dt[banner_img]."' width=210 style='vertical-align:middle'></td>";
	}else{
		$Contents02 .= "<td class='list_box_td list_bg_gray' style='padding:5px;'><img src='".$admin_config[mall_data_root]."/images/banner/".$db->dt[rsl_banner_ix]."/".$db->dt[banner_img]."' style='vertical-align:middle'></td>";
	}
}

$Contents02 .= "<td class='list_box_td'>".$db->dt[regdate]."</td>
				<td class='list_box_td list_bg_gray'>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") ){
		    	$Contents02 .= "<a href='banner_write.php?rsl_banner_ix=".$db->dt[rsl_banner_ix]."&SubID=SM22464243Sub'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}else{
				$Contents02 .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D") ){
				$Contents02 .= "<a href=\"javascript:deleteBanner('delete','".$db->dt[rsl_banner_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
				$Contents02 .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
		    	//if($db->dt[basic] =="N"){
		    	//$Contents02 .= " <a href=\"javascript:deleteGroupInfo('delete','".$db->dt[gp_ix]."')\"><img src='../image/btc_del.gif' border=0></a>";
	    		//}
	    		$Contents02 .= "
		    </td>
		  </tr>
		 ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=7>등록된 배너가 없습니다. </td>
		  </tr>	  ";
}
$Contents02 .="</table>
				<table cellpadding=0 cellspacing=0 width='100%' style='padding-top:3px;'>";
$Contents02 .= "
		<tr height=1>
			<td align=center colspan=6>".$str_page_bar."</td>
			<td colspan=1 align=right>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") ){
			$Contents02 .= "<a href='banner_write.php?SubID=SM22464243Sub' ><img src='../images/".$admininfo["language"]."/btn_reg.gif' align=absmiddle ></a>";
			}
			$Contents02 .= "
			</td>
		</tr>

		</table>";



$Contents = "<table width='100%' border=0>";

$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >배너를 등록하신후 치환함수를 이용해 디자인에 적용하실 수 있습니다</td></tr>

</table>
";
	//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("베너(위젯)관리", $help_text);

 $Script = "
 <script language='javascript'>

	 function deleteBanner(act, rsl_banner_ix){
		if(confirm(language_data['banner.php']['A'][language])){//'배너를 정말로 삭제하시겠습니까?'
			document.location.href = 'banner.act.php?act='+act+'&rsl_banner_ix='+rsl_banner_ix+'&SubID=SM22464243Sub';
		}
	 }

 </script>
 ";

$P = new LayOut();
$P->addScript = $Script;
//$P->strLeftMenu = design_menu();
$P->strLeftMenu = reseller_menu("/admin",$category_str);
$P->Navigation = "리셀러관리> 환경설정> 베너(위젯)관리";
$P->title = "베너(위젯)관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table shop_bannerinfo (
rsl_banner_ix int(4) unsigned not null auto_increment  ,
banner_name varchar(20) null default null,
banner_link varchar(255)  null default null,
banner_target varchar(20) null default null,
banner_desc varchar(255)  null default null,
regdate datetime not null,
primary key(rsl_banner_ix));
*/
?>