<?
$script_time[start] = time();
include("../class/layout.class");
include("../class/calender.class");
include("./member_region.chart.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
$script_time[start] = time();
//print_r($_SESSION);

$db = new Database;
$mdb = new Database;
$sms_design = new SMS;
//print_r($admininfo);
//print_r($admin_config); //
$Script = "


<script language='javascript' src='shop_main_v3_calender.js'></script>
<script language='JavaScript'>
function sendMessage(msg){
        window.HybridApp.callAndroid(msg);
}
</script>
<Script language='javascript'>
function showTabContents(vid, tab_id){
	var area = new Array('recent_order','recent_contents','recent_use_after');
	var tab = new Array('tab_01','tab_02','tab_03');

	for(var i=0; i<area.length; ++i){
		if(area[i]==vid){
			document.getElementById(vid).style.display = 'block';
			document.getElementById(tab_id).className = 'on';
		}else{
			document.getElementById(area[i]).style.display = 'none';
			document.getElementById(tab[i]).className = '';
		}
	}
}

</Script>";
//$script_time[sms_start] = time();
$sms_cnt = $sms_design->getSMSAbleCount($admininfo);
//$script_time[sms_end] = time();
$Contents01 = "
<table width=100% cellpadding=0 cellspacing=0 border='0' align='left' '>
	<tr>
		<td width=100% valign=top >
		<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>";

$Contents01 .= "
		  <tr height=20><td style='padding:10px 0px 5px 0px;border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' > <b class='middle_title'>메인메뉴</b></td></tr>		  
			<tr>
				<td>
				".mobile_main_menu()."
				</td>
			</tr>
		  <tr height=20><td style='padding:10px 0px 5px 0px;border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' > <b class='middle_title'>최근 주문현황</b></td></tr>
		  <tr>
			<td style='padding:3px 0px'>";
	$Contents01 .= "".PrintOrderHistory($agent_type)."		";
	$Contents01 .= "
			</td>
		  </tr>";

$Contents01 .= "
		  <tr>
			<td style='padding:0px;margin:0px;'>";

$Contents01 .= "
				<!--업무관리 및 캘린더 [S]-->
				<table cellpadding='0' cellspacing='0' border='0' width='100%' class='title_area'>
					<col width='50%' />
					<col width='*' />
					<tr>
						<td align='left'>
							<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>업무관리 및 캘린더</b>
						</td>
						<td align='right'>
							<a href='#'><img src='../v3/images/btns//configuration_btn.gif' alt='환경설정' title='환경설정' /></a>
						</td>
					</tr>
				</table>
				<table cellpadding='0' cellspacing='0' border='0' width='100%' style='margin:0 auto;margin-bottom:10px;' >
					<tr>						
						<td valign='top' style='border:solid 1px #C5C5C5;background:#FFF7DA;'>
							<div style='margin-top:18px;'>
								<table cellpadding='0' cellspacing='0' border='0' width='96%' style='margin:0 2%;'>
									<tr>
										<td style='border:0;padding-bottom:10px;' align='left'>
											<strong>오늘의 스케쥴</strong> - ".date("Y년 m월 d일")."
										</td>
									</tr>
									<tr><td class='line_bg01' style='border:0;'></td></tr>
									<tr>
										<td style='border:0;text-align:center;padding-top:0px;'>";

										$today_schedules = DateBySchedule(date("Ymd"));
					$Contents01 .= "<table width=100% border='0' cellspacing='0' cellpadding='0' height='25' style='margin:3px 2px;float:left;' > 
											<col width='100px;'>
											<col width='*'>
											";

					if(count($today_schedules) == 0){
						$Contents01 .= "<tr ><td colspan=4 style='height:145px;text-align:center;'>등록된   스케줄 정보가 없습니다.</td></tr>";
						//$Contents01 .= "<tr><td colspan=4 class='dot-x' style='height:1px;'></td></tr>";
					}else{
							for ($i = 0; $i < count($today_schedules); $i++)
							{
								
						$Contents01 .= "<tr height='25px'>
										<td align='left' style='padding:0 5px 0 0' nowrap><img src='../image/icon_list.gif' border=0 align=bottom ><b>".$today_schedules[$i][stime]." ~ ".$today_schedules[$i][dtime]."</b></td>
										<td align=left>
										".($today_schedules[$i][is_hidden] == '1' ? "<img src='../images/key.gif' border=0 align=bottom title='비밀글'> ":"")."
										".($today_schedules[$i][is_schedule] == '1' ? "<img src='../images/orange/calendar.gif' border=0 align=absmiddle title='스케줄'> ":"")."
										".($today_schedules[$i][co_charger_yn] == 'Y' ? "<img src='../images/orange/cowork.gif' border=0 align=absmiddle title='협업업무'> ":"")."
										<a href=\"../work/work_view.php?mmode=&wl_ix=".$today_schedules[$i][wl_ix]."\" align=absmiddle><b>".Cut_Str($today_schedules[$i][work_title],15)."</b></a></td>
										
									</tr>";
						$Contents01 .= "<tr height=1><td colspan=2 class='dot-x'></td></tr>";
						}
				}
				$Contents01 .= "	</table>";

$Contents01 .= "
											
										</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
					<tr><td colspan=2 height=10></td></tr>
					<tr>
						<td valign='top' style='border:solid 1px #C5C5C5;background:#FFF7DA;'>
							<div style='margin-top:18px;'>
								<table cellpadding='0' cellspacing='0' border='0' width='96%' style='margin:0 2%;'>
									<tr>
										<td style='border:0;padding-bottom:10px;' align='left'>
											<strong>오늘의 할일</strong> - ".date("Y년 m월 d일")." 
										</td>
									</tr>
									<tr><td class='line_bg01' style='border:0;'></td></tr>
									<tr>
										<td style='border:0;text-align:center;padding-top:0px;'>";

										$today_schedules = DateByWork(date("Ymd"));
					$Contents01 .= "<table width=100% border='0' cellspacing='0' cellpadding='0' height='25' style='margin:3px 2px;float:left;' > 
											<col width='100px;'>
											<col width='*'>
											";

					if(count($today_schedules) == 0){
						$Contents01 .= "<tr ><td colspan=4 style='height:145px;text-align:center;'>등록된  스케줄 정보가 없습니다.</td></tr>";
						//$Contents01 .= "<tr><td colspan=4 class='dot-x' style='height:1px;'></td></tr>";
					}else{
							for ($i = 0; $i < count($today_schedules); $i++)
							{
								
						$Contents01 .= "<tr height='25px'>
										<td align='left' style='padding:0 5px 0 0' nowrap><img src='../image/icon_list.gif' border=0 align=bottom ><b>".$today_schedules[$i][stime]." ~ ".$today_schedules[$i][dtime]."</b></td>
										<td align=left>
										".($today_schedules[$i][is_hidden] == '1' ? "<img src='../images/key.gif' border=0 align=bottom title='비밀글'> ":"")."
										".($today_schedules[$i][is_schedule] == '1' ? "<img src='../images/orange/calendar.gif' border=0 align=absmiddle title='스케줄'> ":"")."
										".($today_schedules[$i][co_charger_yn] == 'Y' ? "<img src='../images/orange/cowork.gif' border=0 align=absmiddle title='협업업무'> ":"")."
										<a href=\"../work/work_view.php?mmode=&wl_ix=".$today_schedules[$i][wl_ix]."\" align=absmiddle><b>".Cut_Str($today_schedules[$i][work_title],15)."</b></a></td>
										
									</tr>";
						$Contents01 .= "<tr height=1><td colspan=2 class='dot-x'></td></tr>";
						}
				}
				$Contents01 .= "	</table>";

$Contents01 .= "
											
										</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
				<!--업무관리 및 캘린더 [E]-->
				</td>
			</tr>
			
			</table>		
		</td>
	</tr>
	
</table>";



$Contents = $Contents01;



if(substr_count($_SERVER["HTTP_HOST"],"m.") || $type == "mobile" || substr_count($_SERVER["HTTP_USER_AGENT"],"Mobile") ){
	$P = new MobileLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = store_menu();
	$P->strContents = $Contents;
	$P->Navigation = "메인화면";
	$P->TitleBool = false;
	$P->ServiceInfoBool = true;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = store_menu();
	$P->strContents = $Contents;
	$P->Navigation = "메인화면";
	$P->TitleBool = false;
	$P->ServiceInfoBool = true;
	echo $P->PrintLayOut();
}

$script_time[end] = time();
if($admininfo[charger_id] == "forbiz"){
	//print_r($script_time);
}


function mobile_main_menu($default_path='/admin/v3/images/topmenu'){
global $admininfo, $admin_config, $PHP_SELF;
//print_r($admininfo);
//print_r($admin_config);
//print_r(strpos($admininfo[permit], "01-01"));
		$mstring = "";
	$mdb = new Database;
	//print_r($admininfo);

	if($admin_config[mall_use_inventory] == "N"){
		$inventory_str = " and menu_div != 'inventory' ";
	}

	if($admininfo[mall_type] == "O"){// 입점형
		//$sql = 	"SELECT * FROM admin_menu_div where disp = 1 and gnb_use_biz = 'Y' ".$inventory_str." order by vieworder asc ";
		$sql = "Select distinct am.menu_div, amd.div_name, amd.gnb_name, amd.basic_link
				from admin_menus am left join admin_auth_templet_detail aatd on am.menu_code = aatd.menu_code
				and auth_templet_ix = '".$admininfo[charger_roll]."'
				and am.disp_auth = 'Y' , admin_menu_div amd
				where amd.div_name = am.menu_div and amd.gnb_use_openmarket = 'Y' and disp_auth = 'Y' and auth_read = 'Y'
				and am.use_openmarket = 'Y' $inventory_str
				order by amd.vieworder asc ";
	}else if($admininfo[mall_type] == "B"){// 입점형
		//$sql = 	"SELECT * FROM admin_menu_div where disp = 1 and gnb_use_biz = 'Y' ".$inventory_str." order by vieworder asc ";
		$sql = "Select distinct am.menu_div, amd.div_name, amd.gnb_name, amd.basic_link
				from admin_menus am left join admin_auth_templet_detail aatd on am.menu_code = aatd.menu_code
				and auth_templet_ix = '".$admininfo[charger_roll]."'
				and am.disp_auth = 'Y' , admin_menu_div amd
				where amd.div_name = am.menu_div and amd.gnb_use_biz = 'Y' and disp_auth = 'Y' and auth_read = 'Y'
				and am.use_business = 'Y' $inventory_str
				order by amd.vieworder asc ";

	}else if($admininfo[mall_type] == "F" || $admininfo[mall_type] == "R"){ // 무료형 , 임대형
		//$sql = 	"SELECT * FROM admin_menu_div where disp = 1 and gnb_use_soho = 'Y' ".$inventory_str." order by vieworder asc ";
		$sql = "Select distinct am.menu_div, amd.div_name, amd.gnb_name, amd.basic_link
				from admin_menus am left join admin_auth_templet_detail aatd on am.menu_code = aatd.menu_code
				and auth_templet_ix = '".$admininfo[charger_roll]."'
				and am.disp_auth = 'Y' , admin_menu_div amd
				where amd.div_name = am.menu_div and amd.gnb_use_soho = 'Y' and disp_auth = 'Y' and auth_read = 'Y'
				and am.use_soho = 'Y' $inventory_str
				order by amd.vieworder asc ";
	}else if($admininfo[mall_type] == "H"){ // 무료형 , 임대형
		//$sql = 	"SELECT * FROM admin_menu_div where disp = 1 and gnb_use_soho = 'Y' ".$inventory_str." order by vieworder asc ";
		$sql = "Select distinct am.menu_div, amd.div_name, amd.gnb_name, amd.basic_link
				from admin_menus am left join admin_auth_templet_detail aatd on am.menu_code = aatd.menu_code
				and auth_templet_ix = '".$admininfo[charger_roll]."'
				and am.disp_auth = 'Y' , admin_menu_div amd
				where amd.div_name = am.menu_div and amd.gnb_use_home = 'Y' and disp_auth = 'Y' and auth_read = 'Y'
				and am.use_home = 'Y' $inventory_str
				order by amd.vieworder asc ";
	}else{
		if($admin_config[mall_use_inventory] == "N"){
			$inventory_str = " and div_name != 'inventory' ";
		}

		$sql = 	"SELECT * FROM admin_menu_div where disp = 1 ".$inventory_str." order by vieworder asc ";
	}


	//echo nl2br($sql);
	//exit;
	$mdb->query($sql);
	$mdb->fetch();
	//echo "auth_read : ".$mdb->dt[auth_read] .":::".$mdb->total;
	if(!$mdb->total){
		echo "<script language='javascript'>alert('해당메뉴에 대한 접근권한이 없습니다.');history.back();</script>";
		exit;
	}else{

		$mstring .= "
						<div align=center  STYLE='float:left;text-align:center;position:relative;width:73px;height:80px;'   nowrap>
						<div style='display:block;'>
							<img class='top_menu_img' src='$default_path/camera.gif' vspace=2  width=49 height=48 onclick=\"sendMessage('upload')\">
						</div>
						<strong>모바일촬영</strong>
						</div>
						<div align=center  STYLE='float:left;text-align:center;position:relative;width:73px;height:80px;'   nowrap>
						<div style='display:block;'>
							<a href='../mobile/choice_camera.php'><img class='top_menu_img' src='$default_path/camera.gif' vspace=2  width=49 height=48 ></a>
						</div>
						<strong>모바일촬영(new)</strong>
						</div>";

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			if($mdb->dt[div_name] == "bbsmanage" || $mdb->dt[div_name] == "order"  || $mdb->dt[div_name] == "product"){
				$menu_on_src = str_replace("/","_",$mdb->dt[div_name])."_on.gif";
				$menu_off_src = str_replace("/","_",$mdb->dt[div_name]).".gif";
				if(substr_count ($PHP_SELF, "/".str_replace("/","_",$mdb->dt[div_name])."/")){
					$menu_src = str_replace("/","_",$mdb->dt[div_name])."_on.gif";
				}else{
					$menu_src = str_replace("/","_",$mdb->dt[div_name]).".gif";
				}
				//echo $admininfo[admin_level];
				if($admininfo[admin_level] == 9){
					$gnb_name = $mdb->dt[gnb_name];
				}else{
					$gnb_name = str_replace("셀러관리","상점관리",$mdb->dt[gnb_name]);
				}

				if($admininfo[mall_type] == "H"){
					$gnb_name = str_replace("상점관리","사이트관리",$mdb->dt[gnb_name]);
					$gnb_name = str_replace("프로모션/전시","운영관리",$gnb_name);
				}
				//ONMOUSEOVER=\"javascript:showSubMenuLayer('".$mdb->dt[div_name]."');/*relationOnMouseOut();*/\" ONMOUSEOUT=\"javascript:hideSubMenuLayer('".$mdb->dt[div_name]."')\"
				$mstring .= "
							<div align=center  STYLE='float:left;text-align:center;position:relative;width:73px;height:80px;' onmouseover=\"MM_swapImage('Image".($i+1)."','','$default_path/".$menu_on_src."',1)\" onmouseout='MM_swapImgRestore()'  nowrap>
							<A HREF='".$mdb->dt[basic_link]."' STYLE='position:relative;text-decoration:none;' nowrap>
							<div style='display:block;'>
								<img class='top_menu_img' src='$default_path/".$menu_src."' vspace=2  ID='gnb_link_text_".str_replace("/","_",$mdb->dt[div_name])."' border=0 name=Image".($i+1)." onmouseover=\"this.src='$default_path/".$menu_on_src."'\"  onmouseout=\"this.src='$default_path/".$menu_src."'\" width=49 height=48 ".($_COOKIE[TOP_MENU_HIDDEN] == "N" ? "style='display:none'":"").">
							</div>
							<strong>".$gnb_name."</strong>
							</A>
							</div>";
			}
		}

	}


	$mstring .= "";

	return $mstring;
	exit;

}



/*function DateBySchedule($date){
	$mdb = new Database;
	
	$sql = "SELECT wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, cmd.name
			FROM work_list wl , work_group wg, common_member_detail cmd
			where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
			and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code
			and wl.charger_ix =  '".$_SESSION['admininfo']['charger_ix']."' and is_schedule = '1'
			and ".$date." between sdate and dday 
			order by sdate desc, stime asc
			limit 5";	
	//echo nl2br($sql);
	$mdb->query($sql);
	return $mdb->fetchall();
}

/*function DateByWork($date){
	$mdb = new Database;
	
	$sql = "SELECT wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, cmd.name
			FROM work_list wl , work_group wg, common_member_detail cmd
			where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
			and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code
			and wl.charger_ix =  '".$_SESSION['admininfo']['charger_ix']."' and is_schedule = '0'
			and ".$date." between sdate and dday 
			order by sdate desc, stime asc
			limit 5";	
	//echo nl2br($sql);
	$mdb->query($sql);
	return $mdb->fetchall();
}*///모바일에서 에러 발생하여 주석처리함 비슷한 이름의 함수를 admin_util.php 에서 사용하고 있음 kbk 13/03/11


function PrintOrderHistory($agent_type="pc"){
	global $admininfo, $currency_display, $admin_config ;
	$odb = new Database;

	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));



	if($admininfo[admin_level] == 9){
		$sql = "
					select '기간      ','매출액(".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"].") ', '주문건수','입금예정', '입금확인','배송준비/배송중', '교환 ','주문취소'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." where date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."' AND '".date("Y-m-d 23:59:59",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." where date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."' AND '".date("Y-m-d 23:59:59",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."'
					union
					Select '".date("m/d")." 오늘 ',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." where date between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
					union
					Select '최근1주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." where date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))."' and '".date("Y-m-d 00:00:00")."'
				 	union
					Select '금주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." where date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($firstday,4,2),substr($firstday,6,2),substr($firstday,0,4)))."' and '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($lastday,4,2),substr($lastday,6,2),substr($lastday,0,4)))."'
				 	union
					Select '최근1개월',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." where date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."'
				 	union
					Select '전체',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." ";
		//echo $sql;
		//exit;
		/*
		$sql = "Select
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then total_price else '0' end) as today_total_price,
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m%d') between '".$firstday."' and $lastday then total_price else '0' end) as thisweek_total_price,
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else '0' end) as thismonth_total_price,
					sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else '0' end) as thismonth_cancel_total_price,
					sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end) as ready_cnt,
					sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end) as order_end_cnt,
					sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end) as thismonth_return_total_cnt
				 	from ".TBL_SHOP_ORDER."  ";
				 	*/
	//echo $sql;
	}else if($admininfo[admin_level] == 8){
		$sql = "
					select '기간      ','매출액(".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"].") ', '주문건수','입금예정', '입금확인','배송준비/배송중', '교환 ','주문취소'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."' AND '".date("Y-m-d 23:59:59",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."' AND '".date("Y-m-d 23:59:59",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."'
					union
					Select '".date("m/d")." 오늘 ',
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and date between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
					union
					Select '최근1주',
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."'  and date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."'
				 	union
					Select '금주',
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($firstday,4,2),substr($firstday,6,2),substr($firstday,0,4)))."' and '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($lastday,4,2),substr($lastday,6,2),substr($lastday,0,4)))."'
				 	union
					Select '최근1개월',
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' and date between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."'
				 	union
					Select '전체',
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then total_price else '0' end),0) as total_price,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
					IFNULL(sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_end_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then total_price else '0' end),0) as delivery_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end),0) as return_total_cnt,
					IFNULL(sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then total_price else '0' end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' ";
	}




	$odb->query($sql);
	$datas = $odb->getrows();

	if($agent_type == "mobile"){
				$mstring = "
						<table cellpadding=0 cellspacing=1 width=100% class='list_table_box'>
						<col width=35%>
						<col width=32%>
						<col width=33%>
				";
			for($i=0;$i<count($datas)-1;$i++){
				//print_r($datas);
					if($datas[$i][0] == "입금예정"){
						$title = "<a href='/admin/order/before_payment.php'>".$datas[$i][0]."</a>";
					}else if($datas[$i][0] == "입금확인"){
						$title = "<a href='/admin/order/incom_complete.php'>".$datas[$i][0]."</a>";
					}else if($datas[$i][0] == "배송준비/배송중"){
						$title = "<a href='/admin/order/delivery_ing.php'>".$datas[$i][0]."</a>";
					}else if(trim($datas[$i][0]) == "교환"){
						$title = "<a href='/admin/order/exchange_apply.php'>".$datas[$i][0]."</a>";
					}else if($datas[$i][0] == "주문취소"){
						$title = "<a href='/admin/order/cancel_apply.php'>".$datas[$i][0]."</a>";
					}else{
						$title = $datas[$i][0];
					}
					if($i == 0){
						$mstring .= "<tr bgcolor=#ffffff align=center height='30'>
							<td class='s_td' >".$title."</td>
							<td class='m_td'>".$datas[$i][2]."</td>
							<td class='m_td' nowrap><b>".$datas[$i][3]."</b></td>
							</tr>";
					}else{
						$mstring .= "<tr bgcolor=#ffffff height=30 align=right>
							<td class='list_box_td list_bg_gray' nowrap><b>".$title."</b></td>
							<td class='list_box_td number' >".number_format($datas[$i][2])." </td>
							<td class='list_box_td point number' ><b>".number_format($datas[$i][3])."</b></td>
							</tr>";
					}
			}
			$mstring .= "</table>";

	}else{
			$mstring = "
						<table cellpadding=0 cellspacing=1 width=100% class='list_table_box'>
						<col width=20%>
						<col width=10%>
						<col width=10%>
						<col width=10%>
						<col width=10%>
						<col width=10%>
						<col width=15%>
						<col width=15%>
				";
			for($i=0;$i<count($datas)-1;$i++){
				//print_r($datas);
					if($i == 0){
						$mstring .= "<tr bgcolor=#ffffff align=center height='30'>
							<td class='s_td' >".$datas[$i][0]."</td>
							<td class='m_td'>".$datas[$i][1]."</td>
							<td class='m_td'>".$datas[$i][2]."</td>
							<td class='m_td' nowrap><b>".$datas[$i][3]."</b></td>
							<td class='m_td'>".$datas[$i][4]."</td>
							<td class='m_td'>".$datas[$i][5]."</td>
							<td class='m_td'>".$datas[$i][6]."</td>
							<td class='e_td'>".$datas[$i][7]."</td>
							</tr>";
					}else{
						$mstring .= "<tr bgcolor=#ffffff height=30 align=right>
							<td class='list_box_td list_bg_gray' nowrap><b>".$datas[$i][0]."</b></td>
							<td class='list_box_td number' >".number_format($datas[$i][1])."</td>
							<td class='list_box_td number' >".number_format($datas[$i][2])." </td>
							<td class='list_box_td point number' ><b>".number_format($datas[$i][3])."</b></td>
							<td class='list_box_td number' >".number_format($datas[$i][4])."</td>
							<td class='list_box_td number' >".number_format($datas[$i][5])."</td>
							<td class='list_box_td number' >".number_format($datas[$i][6])."</td>
							<td class='list_box_td point number' >".number_format($datas[$i][7])."</td>
							</tr>";
					}
			}
			$mstring .= "</table>";
	}

	return $mstring;


}

function getSolutionType($solution_type){
	if($solution_type == "H"){
		return "홈빌더";//무료형
	}else if($solution_type == "F"){
		return "소호형";//무료형
	}else if($solution_type == "R"){
		return "임대형";
	}else if($solution_type == "S"){
		return "독립형";
	}else if($solution_type == "B"){
		return "비즈니스형";//입점형
	}else if($solution_type == "O"){
		return "오픈마켓형";
	}else{

	}
}


function PrintBoardRecentList(){
	global $db, $mdb, $admininfo;

	$sql = "select COUNT(*) from ".TBL_SHOP_BBS_USEAFTER."  ";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[0];

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=0 cellspacing=0 width='100%' bgcolor=silver class='list_table_box'>
		<tr align=center bgcolor=#efefef height=30 style='font-weight:bold'>
			<td width='30%' class='s_td'>제품</td>
			<td class='m_td'>내용</td>
			<td width='10%' class='m_td' nowrap>작성자</td>
			<td width='15%' class='m_td'>등록일</td>
			<td width='10%' class='e_td'>관리</td>
		</tr>";
	//$mString = $mString."<tr height=1><td colspan=5 class=dot-x></td></tr>";
	//$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class=s_td width='25%'>제품</td><td class=m_td width='40%'>내용</td><td class=m_td width='10%' nowrap>작성자</td><td class=m_td width='15%'>등록일</td><td class=e_td width='10%'>관리</td></tr>";
	if ($total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=150><td colspan=5 align=center>사용후기 내역이 존재 하지 않습니다.</td></tr>";
	}else{

		$db->query("select p.pname, u.* from ".TBL_SHOP_BBS_USEAFTER." u , ".TBL_SHOP_PRODUCT." p where u.pid = p.id  order by  regdate desc limit 6");

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

			//$no = $no + 1;

			$mString .= "<tr height=27 bgcolor=#ffffff align=center>
			<td bgcolor='#efefef' align='left' style='padding:4px 20px;'>".$db->dt[pname]."</td>
			<td align=left style='padding-left:20px;'>".cut_str(strip_tags($db->dt[uf_contents]),30)."</td>
			<td bgcolor='#efefef'>".$db->dt[uf_name]."</td>
			<td>".str_replace("-",".",substr($db->dt[regdate],0,10))."</td>
			<td bgcolor='#efefef' align=center>
				<a href=JavaScript:useAfterDelete('".$db->dt[uf_ix]."')><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
			</td>
			</tr>
			";
		}

	}

	//<tr height=50 bgcolor=#ffffff><td colspan=6 align=left>".page_bar($total, $page, $max,  "&max=$max")."</td></tr>
	$mString .= "</table>";

	return $mString;
}



function PrintRecentProductList($stock_status=""){
	global $db, $mdb, $admin_config, $admininfo, $DOCUMENT_ROOT, $currency_display;

	$where = array();
	if($stock_status == "soldout"){
		$where[] = "(option_stock_yn = 'Y' or stock = 0 ) and stock_use_yn = 'Y'  ";
	}else if($stock_status == "shortage"){
		$where[] = "(option_stock_yn = 'S' or (stock < safestock && stock != 0 )) and stock_use_yn = 'Y'   ";
	}
	$where = (count($where) > 0)	?	' WHERE '.implode(' AND ', $where):'';


	if($admininfo[admin_level] == 9){
		$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1 $where order by mp1.regdate desc limit 5  ";
	}else{
		$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1 $where and mp1.admin ='".$admininfo[company_id]."'  order by mp1.regdate desc limit 5  ";
	}
	//echo $sql;
	$mdb->query($sql);


	$mString = "<table cellpadding=4 cellspacing=0 border=0 width='100%' bgcolor=silver>";

	if ($mdb->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=2 align=center>등록된 상품이 없습니다.</td></tr>";
	}else{

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);


			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[id], "s"))){
				$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[id], "s");
			}else{
				$img_str = "../image/no_img.gif";
			}

			$mString = $mString."<tr height=45 bgcolor=#ffffff align=center>
			<td bgcolor='#ffffff' width=50><a href='../product/goods_input.php?id=".$mdb->dt[id]."'><img src='".$img_str."' width=50 height=50 border=0 style='border:1px solid #c0c0c0'></a></td>
			<td align=left style='padding:4 4 4 10'>
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td>".($mdb->dt[brand_name] ? "[".$mdb->dt[brand_name]."]":"")."</td>
					<tr>
						<td><a href='../product/goods_input.php?id=".$mdb->dt[id]."'>".cut_str($mdb->dt[pname],20)."</a></td>
					</tr>
					<tr>
						<td >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($mdb->dt[sellprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					</tr>
				</table>
			</td>
			</tr>
			<tr height=1><td colspan=6 class=dot-x></td></tr>
			";
		}

	}


	$mString = $mString."</table>";

	return $mString;
}
?>
