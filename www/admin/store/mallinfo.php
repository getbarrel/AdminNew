<?
include("../class/layout.class");

if($admininfo[admin_level] < 9){
    header("Location:../admin.php");
}
//print_r($_SESSION["admin_config"]);
$db = new Database;
$db_payment = new Database;

$db->query("SELECT * FROM `shop_mall_config` where mall_ix = '".$admininfo[mall_ix]."'   ");
if($db->total){
    for($i=0; $i < $db->total;$i++){
        $db->fetch($i);
        $mall_config[$db->dt[config_name]] = $db->dt[config_value];
    }
}
$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'  ");
$db->fetch();

$phone = explode("-",$db->dt[phone]);
$fax = explode("-",$db->dt[fax]);
///////////////바로빌 정보 추가 //////////////////////	2013-05-03 이학봉
$sql = "
		select
			case when config_name = 'barobill_key' then config_value end as barobill_key,
			case when config_name = 'barobill_id' then config_value end as barobill_id,
			case when config_name = 'barobill_pw' then config_value end as barobill_pw
		from
			shop_payment_config
		where
			mall_ix = '".$admininfo[mall_ix]."'
			and config_name like 'barobill%'
";

$db_payment->query($sql);
$payment_array = $db_payment->fetchall();
if(!empty($payment_array)) {
    foreach($payment_array as $key => $value){
        foreach($value as $ky =>$val){
            if($ky == "barobill_key" && $val ){
                $barobill_key = $val;
            }elseif($ky == "barobill_id" && $val){
                $barobill_id = $val;
                //echo $ky."::::".$val."<br/>";
            }elseif($ky == "barobill_pw" && $val){
                $barobill_pw = $val;
            }
        }
    }
}
/////////////////////////////////////////////////////

///////////////////장바구니 비움기간등 새로 추가 shop_mall_config 에 추가되고 가져옴///////////////////////////////2013-05-03 이학봉


$sql = "select
			*
		from
			shop_mall_config
		where
			mall_ix = '".$admininfo[mall_ix]."'
			and config_value is not null
";

$db_payment->query($sql);
$payment_array = $db_payment->fetchall();

//print_r($admininfo[mall_ix]);exit;

for($i=0; $i < count($payment_array);$i++){
    $_shop_config[$payment_array[$i][config_name]] = $payment_array[$i][config_value];
    //echo $payment_array[$i][config_name];
}

$i = 0;
while(count($payment_array)> $i){

    if($payment_array[$i][config_name] == "cart_delete_day")	$cart_delete_day = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "cancel_auto_day")	$cancel_auto_day = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "check_order_day")	$check_order_day = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "check_sos_order_day")	$check_sos_order_day = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "seller_account_status")	$seller_account_status = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "product_prohibition_text")	$product_prohibition_text = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "holiday_text")	$holiday_text = $payment_array[$i][config_value];

    if($payment_array[$i][config_name] == "admin_access_yn")	$admin_access_yn = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "admin_access_ip")	$admin_access_ip = $payment_array[$i][config_value];

    if($payment_array[$i][config_name] == "wholesale_retail_use")	$wholesale_retail_use = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "seller_account_day")	$seller_account_day = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "image_policy")	$image_policy = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "ftp_url")	$ftp_url = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "ftp_id")		$ftp_id = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "ftp_pass")	$ftp_pass = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "b_ftp_pass")	$b_ftp_pass = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "gift_selling")	$gift_selling = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "search_engine_yn")	$search_engine_yn = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "search_engine_type")	$search_engine_type = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "sns_link_yn")	$sns_link_yn = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "sns")	$sns = $payment_array[$i][config_value];

    if($payment_array[$i]['config_name'] == "logstory_url")	$logstory_url = $payment_array[$i]['config_value'];
    if($payment_array[$i]['config_name'] == "front_url")	$front_url = $payment_array[$i]['config_value'];

    if($payment_array[$i][config_name] == "naverpay_yn")	$naverpay_yn = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "naverpay_key")	$naverpay_key = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "naverpay_button_key")	$naverpay_button_key = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "naverpay_id")	$naverpay_id = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "naverpay_type")	$naverpay_type = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "naverpay_ep")	$naverpay_ep = $payment_array[$i][config_value];

    if($payment_array[$i][config_name] == "add_sattle_module_naverpay_pg")	$add_sattle_module_naverpay_pg = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "naverpay_pg_service_type")	$naverpay_pg_service_type = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "naverpay_pg_partner_id")	$naverpay_pg_partner_id = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "naverpay_pg_client_id")	$naverpay_pg_client_id = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "naverpay_pg_client_secret")	$naverpay_pg_client_secret = $payment_array[$i][config_value];


    if($payment_array[$i][config_name] == "add_sattle_module_toss")	$add_sattle_module_toss = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "toss_service_type")	$toss_service_type = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "toss_api_key")	$toss_api_key = $payment_array[$i][config_value];

    if(strstr($payment_array[$i][config_name],"sheet_name")){
        //$deail_array[$payment_array[$i][config_name]][sheet_value] = $payment_array[$i][sheet_value];
        //$deail_array[$payment_array[$i][config_name]][text] = $payment_array[$i][text];
    }

    $i++;
}

/////////////////////////////////////////////////////////////////////////////
//echo md5("wooho".$db->dt[mall_domain].$db->dt[mall_domain_id]);

$display_yn_hidden = 'display:none;'; //강제로 숨김

$Contents01 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='200px'>
	<col width='*'>
	<col width='*'>
		<tr>
			<td align='left' colspan='3'> ".GetTitleNavigation("쇼핑몰정보설정", "상점관리 > 쇼핑몰 환경설정 > 쇼핑몰정보설정 ")."</td>
		</tr>
</table>

<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-top:10px;' >
	<col width='200px'>
	<col width='*'>

	<tr height=27>
		<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>쇼핑몰 기본 정보</b>  
			<img src='../images/".$admininfo["language"]."/btn_open1.gif' border='0' align='right' onclick=\"show_info('logo_info');\" style='cursor:pointer;'></div>")."
		</td>
	</tr>
</table>

	<div class='logo_info'>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
		<col width='200px'>
		<col width='*'>
		<tr bgcolor=#ffffff >
			<td class='input_box_title' > 쇼핑몰 이름</td>
			<td class='input_box_item' >
				<table cellpadding=0 cellspacing=0>
					<tr>
						<td><input type=text class='textbox' name='mall_name' value='".$mall_config['mall_name']."' style='width:230px;' > <span> ※PC/Mobile 프론트에서 고객에게 노출되는 정보입니다.</span></td>
					</tr>
				</table>
			</td>
		</tr>
	<!--tr bgcolor=#ffffff height=30>
		<td class='input_box_title' ><b>관리자로고</b></td>
		<td class='input_box_item' >
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td class='input_box_title' > 기본 </td>
					<td>
					<input type=file name='admin_logo' class='textbox'  style='width:280px'>
					<div style='padding-left:20px;'>";
if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/admin_logo.gif") || true){
    $Contents01 .= "<img src='".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/admin_logo.gif'>
					";
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
        $Contents01 .= "
                    <a href='javascript:' onclick=\"del('admin_logo');\">삭제</a>";
    }
}
$Contents01 .= "
					</div>
					</td>

				</tr>
			</table>
			<table border=0 class='input_table_box' width=100%>
							<col width='100px'>
							<col width='*'>
							<tr>
								<td class='input_box_title' > 기본 </td>
								<td class='input_box_item' style='padding-top:8px;'>
								<input type='file' class=textbox name='category_img' style='padding-bottom:4px;'> 분류명(상단메뉴)으로 사용할 이미지 <input type='checkbox' name='ch_category_img' id='ch_category_img' value='Y' /> <label for='ch_category_img'>삭제</label>
								<div id='category_img_area' style='padding:5px 0px 5px 10px;'></div>
								</td>
							</tr>
							<tr>
								<td class='input_box_title' > 마우스오버 </td>
								<td class='input_box_item' style='padding-top:8px;'>
								<input type='file' class=textbox name='category_img_on' style='padding-bottom:4px;'> 분류명(상단메뉴)으로 사용할 이미지 <input type='checkbox' name='ch_category_img_on' id='ch_category_img_on' value='Y' /> <label for='ch_category_img_on'>삭제</label>
								<div id='category_img_on_area' style='padding:5px 0px 5px 10px;'></div>
								</td>
							</tr>
						</table>
		</td>
	</tr-->

	<tr bgcolor=#ffffff height=30>
		<td class='input_box_title' > <b>쇼핑몰로고</b><span style='padding-left:2px' class='helpcloud' help_width='410' help_height='30' help_html='상품의 이미지의 불법 저장 등을 제한하기 위한 기능이며, 상품 썸네일과 상세페이지 이미지 등 위에 등록한 이미지가 함께 보여집니다.'><img src='/admin/images/icon_q.gif' /></span>  </td>
		<td class='input_box_item' style='padding:5px 5px 5px 5px;'>
			<table cellpadding=0 cellspacing=0 class='input_table_box' width=100%>
				<tr>
					<td class='input_box_title' > 기본 </td>
					<td class='input_box_item' style='padding-top:10px;'>
						<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
								<input type=file name='shop_logo' class='textbox'  style='width:280px'>
							</td>
					<td style='padding:10px;'>";
if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/shop_logo.gif")){
    $Contents01 .= "<img src='".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/shop_logo.gif' width=100>";
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
        $Contents01 .= "
						<div style='display:inline;padding:0px 0px 10px 10px;'>
						<a href='javascript:' onclick=\"del('shop_logo','".$admin_config."');\"><img src='../images/korean/btn_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:20px;' title='삭제'></a></div>";
    }
}
$Contents01 .= "
					</td
					</tr>
					</table>
				</td>
				</tr>

				<tr>
					<td class='input_box_title' > 마우스오버 </td>
					<td class='input_box_item' style='padding-top:10px;'>
						<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
								<input type=file name='shop_logo_over' class='textbox'  style='width:280px'>
							</td>
					<td style='padding:10px;'>";
if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/shop_logo_over.gif")){
    $Contents01 .= "<img src='".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/shop_logo_over.gif' width=100 align='absmiddle' style='vertical-align:middle;'>";
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
        $Contents01 .= "
						<div style='display:inline;padding:0px 0px 10px 10px;'>
						<a href='javascript:' onclick=\"del('shop_logo_over','".$admin_config."');\"><img src='../images/korean/btn_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:20px;' title='삭제'></a></div>";
    }
}
$Contents01 .= "
						</td>
						</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30 style='".$display_yn."'>
		<td class='input_box_title' > <b>모바일로고</b> </td>
		<td class='input_box_item'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>
					<input type=file name='mobile_logo' class='textbox'  style='width:280px'>
					</td>
					<td style='padding:10px;'>";
if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/mobile_logo.gif")){
    $Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/mobile_logo.gif' width=100 align='absmiddle' style='vertical-align:middle;'> ";
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
        $Contents01 .= "
						<div style='display:inline;padding:0px 0px 10px 10px;'>
						<a href='javascript:' onclick=\"del('mobile_logo','".$admin_config."');\"><img src='../images/korean/btn_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:20px;' title='삭제'></a></div>";
    }
}else{
    $Contents01 .= "
					<span> ※모바일 로고가 등록 되면 샘플로고 이미지가 등록된 로고 이미지로 변경됩니다.(로고이미지는 png 파일로 올리세요)</span>";
}
$Contents01 .= "

					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30 style='".$display_yn."'>
		<td class='input_box_title' > <b>메일폼로고</b>   </td>
		<td class='input_box_item'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>
					<input type=file name='mail_logo' class='textbox'  style='width:280px'>
					</td>
					<td style='padding:10px;'>";
if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/mail_logo.gif")){
    $Contents01 .= "<img src='".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/mail_logo.gif' width=100>
					</td>";
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
        $Contents01 .= "
                    <td>
                    <a href='javascript:' onclick=\"del('mail_logo','".$admin_config."');\"><img src='../images/korean/btn_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:20px;' title='삭제'></a>";
    }
}
$Contents01 .= "
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='input_box_title' > <b>파비콘</b>   </td>
		<td class='input_box_item'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>
					<input type=file name='favicon' class='textbox'  style='width:280px'>
					</td>
					<td style='padding-left:20px;'>";
if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/favicon.ico")){
    $Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/favicon.ico' width='16' height='16' />
                    </td>";
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
        $Contents01 .= "
                    <td>
                    <a href='javascript:' onclick=\"del('favicon','".$admin_config."');\"><img src='../images/korean/btn_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:20px;' title='삭제'></a>";
    }
}
$Contents01 .= "
					</td>
				</tr>
			</table>
		</td>
	</tr>";

$timeSaleDel = "";
$timeSaleIcon = "";
if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/time_sale.png")) {
    $timeSaleIcon = "<img src='".$admin_config[mall_data_root]."/images/time_sale.png'  />";
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
        $timeSaleDel = "<a href='javascript:' onclick=\"del('time_sale','".$admin_config."');\"><img src='../images/korean/btn_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:20px;' title='삭제'></a>";
    }
}
$Contents01 .= "
    <tr bgcolor=#ffffff height=30 >
		<td class='input_box_title' > <b>타임세일아이콘(PC)</b>   </td>
		<td class='input_box_item'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>
					    <input type=file name='time_sale' class='textbox'  style='width:280px'>
					</td>
					<td style='padding-left:20px;'>
					    ".$timeSaleIcon."
                    </td>
                    <td>
                        ".$timeSaleDel."
                        <span> * 브라우저 캐시가 동작 중 에는 캐시가 초기화 되어야 변경된 이미지를 확인 할 수있습니다.</span>
					</td>
					
				</tr>
			</table>
		</td>
	</tr>";

$timeSaleMDel = "";
$timeSaleMIcon = "";
if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/time_sale_mobile.png")) {
    $timeSaleMIcon = "<img src='".$admin_config[mall_data_root]."/images/time_sale_mobile.png' />";
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
        $timeSaleMDel = "<a href='javascript:' onclick=\"del('time_sale_mobile','".$admin_config."');\"><img src='../images/korean/btn_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:20px;' title='삭제'></a>";
    }
}
$Contents01 .= "
    <tr bgcolor=#ffffff height=30 >
		<td class='input_box_title' > <b>타임세일아이콘(MOBILE)</b> </td>
		<td class='input_box_item'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>
					<input type=file name='time_sale_mobile' class='textbox'  style='width:280px'>
					</td>
					<td style='padding-left:20px;'>
					    ".$timeSaleMIcon."
                    </td>
                    <td>
                        ".$timeSaleMDel."
                         <span> * 브라우저 캐시가 동작 중 에는 캐시가 초기화 되어야 변경된 이미지를 확인 할 수있습니다.</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>";
if($admininfo[mall_type] != "H"){

    $Contents01 .= "
	<tr bgcolor=#ffffff height=30 style='".$display_yn_hidden."'>
		<td class='input_box_title' > <b>워터마크 이미지</b> (350 * 49) (PNG 등록) </td>
		<td class='input_box_item'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>
					<input type=file name='watermark' class='textbox'  style='width:280px'>
					</td>
					<td style='padding-left:10px;height:80px;'>";
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/watermark/watermark.png")){
        $Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/watermark/watermark.png' width=200 />
                    </td>";
        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
            $Contents01 .= "
                    <td>
                    <a href='javascript:' onclick=\"del('watermark','".$admin_config."');\"><img src='../images/korean/btn_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:20px;' title='삭제'></a>";
        }
    }
    $Contents01 .= "
					</td>
				</tr>
			</table>
		</td>
	</tr>";
}
$Contents01 .= "
	
</table>
<script>
function del(name){
    var select = confirm('삭제하시겠습니까?');

    if(select){
        $.ajax({
    			url: 'mallinfo.act.php',
    			type: 'get',
    			dataType: 'html',
    			data: ({del: name
    			}),
    			success: function(result){
    			    document.location.reload();
    			}
    	});
    }
    else{
        exit;
    }

}
</script>
	<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-top:10px;' >
		<tr>
			<td>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</td>
		</tr>
	</table>
 </div>
";



if($admininfo[mall_type] != "R" && $admininfo[mall_type] != "B"){
    $Contents01 .= "

	<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-top:10px;".$display_yn_hidden."'>
	<col width='200px'>
	<col width='*'>
		<tr onclick=\"show_info('barobill_info');\" style='cursor:pointer'>
			<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>바로빌 정보</b><span style='padding-left:2px' class='helpcloud' help_width='430' help_height='50' help_html='바로빌 정보.'><img src='/admin/images/icon_q.gif' /></span></div>")."</td>
		</tr>
	</table>

	<div class='barobill_info'>
	<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;".$display_yn_hidden."' class='input_table_box'>
	<col width='200px'>
	<col width='*'>
		<tr bgcolor=#ffffff >
			<td class='input_box_title' > 바로빌 인증키</td>
			<td class='input_box_item' >
				<table cellpadding=0 cellspacing=0>
					<tr>
						<td><input type=text class='textbox' name='barobill_key' value='".$barobill_key."' style='width:230px;' ></td>
						<!--<td style='padding-left:20px;'><span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>-->
					</tr>
				</table>
			</td>
		</tr>
		<tr bgcolor=#ffffff >
			<td class='input_box_title'> 바로빌 아이디</td>
			<td class='input_box_item'>
				<table cellpadding=0 cellspacing=0>
					<tr>
						<td><input type=text class='textbox' name='barobill_id' value='".$barobill_id."' style='width:230px; line-height:120%;' ></td>
						<!--<td style='padding-left:20px;'> <span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')." </span>  </td>-->
					</tr>
				</table>
			</td>
		</tr>
		<tr bgcolor=#ffffff >
			<td class='input_box_title'> 바로빌 비밀번호 </td>
			<td class='input_box_item'>
				<table cellpadding=0 cellspacing=0>
					<tr>
						<td>
						<input type=text class='textbox' id='barobill_pw' name='barobill_pw' value='".$barobill_pw."' style='width:230px;' >
						<!--<input type='hidden' name='mall_domain_key' id='mall_domain_key2' value='".$db->dt[mall_domain_key]."'>
						</td>
						<td style='padding-left:20px;'><span class=small>발급받은 32 자리 key</span> <input type='checkbox' name='auth_check' id='auth_check' onclick=\"domainKey(this)\" style='vertical-align:middle'><label for='auth_check' style='vertical-align:middle'> 도메인 Key 변경</label>--></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</div>
	
	<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-top:10px;".$display_yn_hidden."' >
	<col width='200px'>
	<col width='*'>
		<tr onclick=\"show_info('popbill_info');\" style='cursor:pointer'>
			<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>팝빌 정보</b><span style='padding-left:2px' class='helpcloud' help_width='430' help_height='50' help_html='팝빌 정보.'><img src='/admin/images/icon_q.gif' /></span></div>")."</td>
		</tr>
	</table>
	<div class='popbill_info'>
	<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;".$display_yn_hidden."' class='input_table_box'>
	<col width='200px'>
	<col width='*'>
		<tr bgcolor=#ffffff >
			<td class='input_box_title'> 팝빌 아이디</td>
			<td class='input_box_item'>
				<table cellpadding=0 cellspacing=0>
					<tr>
						<td><input type=text class='textbox' name='popbill_id' value='".$mall_config[popbill_id]."' style='width:230px; line-height:120%;' ></td>
						<!--<td style='padding-left:20px;'> <span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')." </span>  </td>-->
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</div>
	";

}


if($admininfo[mall_type] != "R" && $admininfo[mall_type] != "B"){
    $Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-top:10px;".$display_yn."' >
		<col width='200px'>
		<col width='*'>
			<tr onclick=\"show_info('domain_info');\" style='cursor:pointer'>
				<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>도메인 인증정보</b><span style='padding-left:2px' class='helpcloud' help_width='430' help_height='50' help_html='도메인과 도메인아이디에 경우는 직접 변경이 불가능 하며, 도메인 Key값은 몰스토리에서 검수 후 발급되며 사용자가 임의로 Key값을 변경 시 사이트 이용에 제한이 발생할 수 있습니다.'><img src='/admin/images/icon_q.gif' /></span></div>")."</td>
			</tr>
		</table>

		<div class='domain_info'>
		<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;".$display_yn."' class='input_table_box'>
		<col width='200px'>
		<col width='*'>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > 도메인</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=text class='textbox' name='mall_domain' value='".$db->dt[mall_domain]."' style='width:230px;' readonly></td>
							<td style='padding-left:20px;'><span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title'> 도메인 아이디</td>
				<td class='input_box_item'>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=text class='textbox' name='mall_domain_id' value='".$db->dt[mall_domain_id]."' style='width:230px; line-height:120%;' readonly></td>
							<td style='padding-left:20px;'> <span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')." </span>  </td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title'> 도메인 key</td>
				<td class='input_box_item'>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
							<input type=text class='textbox' id='mall_domain_key' name='mall_domain_key' value='".$db->dt[mall_domain_key]."' style='width:230px;' disabled>
							<input type='hidden' name='mall_domain_key' id='mall_domain_key2' value='".$db->dt[mall_domain_key]."'>
							</td>
							<td style='padding-left:20px;'><span class=small>발급받은 32 자리 key</span> <input type='checkbox' name='auth_check' id='auth_check' onclick=\"domainKey(this)\" style='vertical-align:middle'><label for='auth_check' style='vertical-align:middle'> 도메인 Key 변경</label></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' >
			<tr>
				<td>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</td>
			</tr>
		</table>
		</div>
		";
}

if($admininfo[mall_type] == "O"){
    $Contents02 = "
		<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin:10px 0px 0px 0px;' >
		<col width='200px'>
		<col width='*'>
			<tr onclick=\"show_info('img_info');\" style='cursor:pointer'>
				<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>이미지 관련 설정</b></div>")."</td>
			</tr>
		</table>

		<div class='img_info'>
		<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin:0px 0px 20px 0px;' class='input_table_box'>
		<col width='200px'>
		<col width='*'>
			<tr bgcolor=#ffffff height=30>
				<td class='input_box_title'> <b>이미지 환경설정</b></td>
				<td class='input_box_item' >
				   <input type=radio name='image_policy' id='image_policy_local' value='Y' ".CompareReturnValue("Y",$image_policy,"checked")."><label for='image_policy_local'>로컬 이미지정책 사용</label>
				   <input type=radio name='image_policy' id='image_policy_ftp' value='N' ".CompareReturnValue("N",$image_policy,"checked")."><label for='image_policy_ftp'>이미지 호스팅(이미지 서버) 사용</label> 
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > 이미지 호스팅 주소</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=text class='textbox' name='ftp_url' value='".$ftp_url."' style='width:230px;' ></td>
							<td style='padding-left:20px;'><span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title'> 아이디</td>
				<td class='input_box_item'>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=text class='textbox' name='ftp_id' value='".$ftp_id."' style='width:230px; line-height:120%;' ></td>
							<td style='padding-left:20px;'> <span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')." </span>  </td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title'> 비밀번호</td>
				<td class='input_box_item'>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
							<input type=text class='textbox' id='mall_domain_key' name='ftp_pass' value='".$ftp_pass."' style='width:230px;' >
							<input type='hidden' name='b_ftp_pass' id='b_ftp_pass' value='".$b_ftp_pass."'>
							</td>
							<td style='padding-left:20px;'><input type='checkbox' name='auth_check' id='auth_check' onclick=\"domainKey(this)\" style='vertical-align:middle'><label for='auth_check' style='vertical-align:middle'> 비밀번호 변경</label></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</div>
		";
}
$Contents02 .= "
";


$Payment02 = "
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' >
<col width='200px'>
<col width='*' />
	<tr>
		<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>PAYCO 결제설정</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-bottom:0px;' class='input_table_box'>
<col width='200px' />
<col width='*' />
<col width='200px' />
<col width='*' />
	<tr bgcolor=#ffffff height=40>
		<td class='input_box_title' > <b>PAYCO 사용 선택</b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='add_sattle_module_payco' id='payco_y' value='Y' ".CompareReturnValue("Y",$mall_config[add_sattle_module_payco],"checked")."><label for='payco_y'>사용함</label>
		   <input type=radio name='add_sattle_module_payco' id='payco_n' value='N' ".CompareReturnValue("N",$mall_config[add_sattle_module_payco],"checked")."><label for='payco_n'>사용안함</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40>
		<td class='input_box_title' > <b>PAYCO 사용 타입</b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='payco_service_type' id='payco_type_text' value='test' ".CompareReturnValue("test",$mall_config[payco_service_type],"checked")."><label for='payco_type_text'>테스트</label>
		   <input type=radio name='payco_service_type' id='payco_type_service' value='service' ".CompareReturnValue("service",$mall_config[payco_service_type],"checked")."><label for='payco_type_service'>서비스</label> 
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 >
		<td class='input_box_title'> <b>페이코 가맹점코드(sellerKey)</b></td>
		<td class='input_box_item' colspan=3>
			<input type='text' name='payco_seller_key' id='payco_sellerKey' title='페이코 가맹점코드' size='30' value='".$mall_config[payco_seller_key]."' />
		  <span style='margin-left:10px;' class='small blu'>발급받은 가맹점코드 값을 입력 해 주세요.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 >
		<td class='input_box_title'> <b>페이코 상점ID(cpId)</b></td>
		<td class='input_box_item' colspan=3>
			<input type='text' name='payco_cp_id' id='payco_cpId' title='페이코 상점ID(cpId)' size='30' value='".$mall_config[payco_cp_id]."' />
		  <span style='margin-left:10px;' class='small blu'>발급받은 페이코 상점ID(cpId) 값을 입력 해 주세요.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 >
		<td class='input_box_title'> <b>페이코 상품ID(productID)</b></td>
		<td class='input_box_item' colspan=3>
			<input type='text' name='payco_product_id' id='payco_productID' title='페이코 상품ID(productID)' size='30' value='".$mall_config[payco_product_id]."' />
		  <span style='margin-left:10px;' class='small blu'>발급받은 페이코 상품ID(productID) 값을 입력 해 주세요.</span>
		</td>
	</tr>
</table>

";

if($admininfo[mall_type] != "H"){
    $Contents02 .= "
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' >
<col width='200px'>
<col width='*' />
	<tr  onclick=\"show_info('mall_info');\" style='cursor:pointer'>
		<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>쇼핑몰 설정</b></div>")."</td>
	</tr>
</table>

<div class='mall_info'>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-bottom:0px;' class='input_table_box'>
<col width='200px' />
<col width='*' />
<col width='200px' />
<col width='*' />

	<tr bgcolor=#ffffff height=40>
		<td class='input_box_title'> <b>결제모듈 선택</b></td>
		<td class='input_box_item' colspan=3>";

//$sattle_module 는 admin_util.php 에 선언
    foreach($sattle_module as $sattle_module_value => $sattle_module_name){
        $Contents02 .= "
		   <input type=radio name='sattle_module' id='sattle_module_".$sattle_module_value."' value='".$sattle_module_value."' ".CompareReturnValue($sattle_module_value,$mall_config[sattle_module],"checked")."><label for='sattle_module_".$sattle_module_value."'>".$sattle_module_name."</label>";
    }
    /*
    $Contents02 .= "
            </td>
        </tr>
        <tr bgcolor=#ffffff height=40>
            <td class='input_box_title'> <b>모바일 결제모듈 선택</b></td>
            <td class='input_box_item' colspan=3>";

    //$sattle_module 는 admin_util.php 에 선언
    foreach($mobile_sattle_module as $sattle_module_value => $sattle_module_name){
    $Contents02 .= "
               <input type=radio name='mobile_sattle_module' id='mobile_sattle_module_".$sattle_module_value."' value='".$sattle_module_value."' ".CompareReturnValue($sattle_module_value,$mall_config[mobile_sattle_module],"checked")."><label for='mobile_sattle_module_".$sattle_module_value."'>".$sattle_module_name."</label>";
    }
    */

    $mall_config[kakaopay_yn] = "N"; //강제로 숨김
    $mall_config[paypal_yn] = "N"; //강제로 숨김
    $Contents02 .= "
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40>
		<td class='input_box_title' > <b>배송비 부과 정책 </b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='delivery_with_coupon' id='with_coupon_y' value='Y' ".CompareReturnValue("Y",$mall_config[delivery_with_coupon],"checked")."><label for='with_coupon_y'>쿠폰할인가 반영</label>
		   <input type=radio name='delivery_with_coupon' id='with_coupon_n' value='N' ".CompareReturnValue("N",$mall_config[delivery_with_coupon],"checked")." ".CompareReturnValue("",$mall_config[delivery_with_coupon],"checked")."><label for='with_coupon_n'>쿠폰할인가 미반영</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40>
		<td class='input_box_title' > <b>카카오알림톡 </b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='kakao_alim_talk_yn' id='kakao_alim_talk_y' value='Y' ".CompareReturnValue("Y",$mall_config[kakao_alim_talk_yn],"checked")."><label for='kakao_alim_talk_y'>사용함</label>
		   <input type=radio name='kakao_alim_talk_yn' id='kakao_alim_talk_n' value='N' ".CompareReturnValue("N",$mall_config[kakao_alim_talk_yn],"checked")."><label for='kakao_alim_talk_n'>사용안함</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 class='kakao_alim_talk' ".($mall_config[kakao_alim_talk_yn] == 'Y' ? "" : "style='display:none;")."'>
		<td class='input_box_title'> <b>카카오알림톡 memberCode</b></td>
		<td class='input_box_item' colspan=3>
			<input type='text' name='kakao_alim_talk_memberCode' id='kakao_alim_talk_memberCode' title='카카오알림톡 memberCode' size='30' value='".$mall_config[kakao_alim_talk_memberCode]."' />
			<span style='margin-left:10px;' class='small blu'>발급받은 memberCode 값을 입력 해 주세요.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 class='kakao_alim_talk' ".($mall_config[kakao_alim_talk_yn] == 'Y' ? "" : "style='display:none;")."'>
		<td class='input_box_title'> <b>카카오알림톡 apiKey</b></td>
		<td class='input_box_item' colspan=3>
			<input type='text' name='kakao_alim_talk_apiKey' id='kakao_alim_talk_apiKey' title='카카오알림톡 apiKey' size='50' value='".$mall_config[kakao_alim_talk_apiKey]."' />
			<span style='margin-left:10px;' class='small blu'>발급받은 apiKey 값을 입력 해 주세요.</span>
		</td>
	</tr>
	<!--
	<tr bgcolor=#ffffff height=40 ".($mall_config[kakao_alim_talk_yn] == 'Y' ? "" : "style='display:none;").">
		<td class='input_box_title' > <b>카카오페이 사용 선택</b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='kakaopay_yn' id='kakaopay_y' value='Y' ".CompareReturnValue("Y",$mall_config[kakaopay_yn],"checked")."><label for='kakaopay_y'>사용함</label>
		   <input type=radio name='kakaopay_yn' id='kakaopay_n' value='N' ".CompareReturnValue("N",$mall_config[kakaopay_yn],"checked")."><label for='kakaopay_n'>사용안함</label> <span style='margin-left:10px;' class='small blu'>카카오페이는 모바일 버전에서만 지원가능 합니다.</span>
		</td>
	</tr> -->
	<tr bgcolor=#ffffff height=40 class='kakao_key' ".($mall_config[kakaopay_yn] == 'Y' ? "" : "style='display:none;")."'>
		<td class='input_box_title'> <b>카카오 MID</b></td>
		<td class='input_box_item' colspan=3>
			<input type='text' name='kakao_mid' id='kakao_mid' title='카카오 MID' size='30' value='".$mall_config[kakao_mid]."' />
		  <span style='margin-left:10px;' class='small blu'>발급받은 MID 값을 입력 해 주세요.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 class='kakao_key' ".($mall_config[kakaopay_yn] == 'Y' ? "" : "style='display:none;")."'>
		<td class='input_box_title'> <b>카카오 EncKey</b></td>
		<td class='input_box_item' colspan=3>
			<input type='text' name='kakao_enckey' id='kakao_enckey' title='카카오 EncKey' size='30' value='".$mall_config[kakao_enckey]."' />
		  <span style='margin-left:10px;' class='small blu'>발급받은 EncKey 값을 입력 해 주세요.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 class='kakao_key' ".($mall_config[kakaopay_yn] == 'Y' ? "" : "style='display:none;")."'>
		<td class='input_box_title'> <b>거래취소 비밀번호</b></td>
		<td class='input_box_item' colspan=3>
			<input type='text' name='kakao_cancel_pw' id='kakao_cancel_pw' title='거래취소 비밀번호' size='30' value='".$mall_config[kakao_cancel_pw]."' />
		  <span style='margin-left:10px;' class='small blu'>발급받은 거래취소 비밀번호 값을 입력 해 주세요.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 class='kakao_key' ".($mall_config[kakaopay_yn] == 'Y' ? "" : "style='display:none;")."'>
		<td class='input_box_title'> <b>카카오 HashKey</b></td>
		<td class='input_box_item' colspan=3>
			<input type='text' name='kakao_hashkey' id='kakao_hashkey' title='카카오 HashKey' size='30' value='".$mall_config[kakao_hashkey]."' />
		  <span style='margin-left:10px;' class='small blu'>발급받은 HashKey 값을 입력 해 주세요.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 class='kakao_key' ".($mall_config[kakaopay_yn] == 'Y' ? "" : "style='display:none;")."'>
		<td class='input_box_title'> <b>카카오 상점키</b></td>
		<td class='input_box_item' colspan=3>
			<input type='text' name='kakao_shop_key' id='kakao_shop_key' title='카카오 상점키' size='100' value='".$mall_config[kakao_shop_key]."' />
		  <span style='margin-left:10px;' class='small blu'>발급받은 상점키 값을 입력 해 주세요.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 ".($mall_config[paypal_yn] == 'Y' ? "" : "style='display:none'").">
		<td class='input_box_title'> <b>paypal 사용여부</b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='paypal_yn' id='paypal_yn_y' value='Y' ".CompareReturnValue("Y",$mall_config[paypal_yn],"checked")."><label for='paypal_yn_y'>사용함</label>
		   <input type=radio name='paypal_yn' id='paypal_yn_n' value='N' ".CompareReturnValue("N",$mall_config[paypal_yn],"checked")."><label for='paypal_yn_n'>사용안함</label> 
		   <span style='margin-left:10px;' class='small blu'>언어팩 영문일때만 페이팔 사용가능.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=34 class='paypal_view' ".($mall_config[paypal_yn] == 'Y' ? "" : "style='display:none'").">
	    <td class='input_box_title'> <b>페이팔 계정 </b></td>
		<td class='input_box_item' colspan=3><input type=text class='textbox' name='paypal_id' id='paypal_id' value='".$mall_config[paypal_id]."' style='width:230px;' ".($mall_config[paypal_yn] == 'Y' ? "validation='true'" : "validation='false'")." title='페이팔 계정'> <span class='small blu'><b>페이팔</b> 에서 발급 받은 코드를 입력해주세요</span></td>
	  </tr>";

    if(getOrderSecondYN()){
        $Contents02 .= "
	  <tr bgcolor=#ffffff height=34 class='paypal_view' ".($mall_config[paypal_yn] == 'Y' ? "" : "style='display:none'").">
	    <td class='input_box_title' colspan=3> <b>페이팔 2차결제 계정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'><input type=text class='textbox' name='paypal_id_2' id='paypal_id_2' value='".$mall_config[paypal_id_2]."' style='width:230px;' ".($mall_config[paypal_yn] == 'Y' ? "validation='true'" : "validation='false'")." title='페이팔 계정'> <span class='small blu'><b>페이팔</b> 에서 발급 받은 코드를 입력해주세요</span></td>
	  </tr>";
    }
    $Contents02 .= "
	<tr bgcolor=#ffffff height=34 class='paypal_view' ".($mall_config[paypal_yn] == 'Y' ? "" : "style='display:none'").">
		<td class='input_box_title'> <b>페이팔 서비스타입 </b></td>
		<td class='input_box_item' colspan=3><input type='radio' name='paypal_type' value='test' ".($mall_config[paypal_type] == "test" ? "checked":"").">TEST <input type='radio' name='paypal_type' value='service' ".($mall_config[paypal_type] == "service" ? "checked":"").">SERVICE <span class='small blu'><b>페이팔</b> 에서 테스트키이면 TEST, 실제키를 발급받았으면 SERVICE 를 선택하시기 바랍니다.</span><input type='hidden' name='paypal_type_befor' value='".$mall_config[paypal_type]."' /></td>
	  </tr>
	  <tr bgcolor=#ffffff height=34 class='paypal_view' ".($mall_config[paypal_yn] == 'Y' ? "" : "style='display:none'").">
		<td class='input_box_title'> <b>페이팔 결제 통화 타입 </b></td>
		<td class='input_box_item' colspan=3>
			<select name='paypal_currency_type'>
				<option value='' ".($mall_config[paypal_currency_type] == "" ? "selected":"").">선택해주세요</option>
				<option value='USD' ".($mall_config[paypal_currency_type] == "USD" ? "selected":"").">$ 달러 (USD)</option>
			</select>
		</td>
	  </tr>
	<tr bgcolor=#ffffff height=40>
		<td class='input_box_title'> <b>도매/소매 사용여부</b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='selling_type' id='selling_type_r' value='R' ".CompareReturnValue("R",$mall_config[selling_type],"checked")."><label for='selling_type_r'>소매</label>
		   <input type=radio name='selling_type' id='selling_type_w' value='W' ".CompareReturnValue("W",$mall_config[selling_type],"checked")."><label for='selling_type_w'>도매</label> 
		   <input type=radio name='selling_type' id='selling_type_wr' value='WR' ".CompareReturnValue("WR",$mall_config[selling_type],"checked")."><label for='selling_type_wr'>도매+소매</label> 
		   <span style='margin-left:10px;' class='small blu'>복합과세를 사용하시려면 반드시 PG사에 복합과세 결제를 신청하신 후 사용해주세요.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 style='".$display_yn_hidden."'>
		<td class='input_box_title'> <b>복합과세 사용 선택</b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='compound_tax' id='compound_tax_y' value='Y' ".CompareReturnValue("Y",$db->dt[compound_tax],"checked")."><label for='compound_tax_Y'>사용함</label>
		   <input type=radio name='compound_tax' id='compound_tax_n' value='N' ".CompareReturnValue("N",$db->dt[compound_tax],"checked")."><label for='compound_tax_n'>사용안함</label> <span style='margin-left:10px;' class='small blu'>복합과세를 사용하시려면 반드시 PG사에 복합과세 결제를 신청하신 후 사용해주세요.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 style='".$display_yn_hidden."'>
		<td class='input_box_title'> <b>네이버 체크아웃 설정</b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='naver_checkout' id='naver_checkout_y' value='Y' ".CompareReturnValue("Y",$db->dt[naver_checkout],"checked")."><label for='naver_checkout_Y'>사용함</label>
		   <input type=radio name='naver_checkout' id='naver_checkout_n' value='N' ".CompareReturnValue("N",$db->dt[naver_checkout],"checked")."><label for='naver_checkout_n'>사용안함</label> <span style='margin-left:10px;' class='small blu'>네이버 체크아웃 설정을 하시면 관련 기능이 자동으로 활성화 됩니다.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 style='".$display_yn_hidden."'>
		<td class='input_box_title'> <b>제휴판매 설정</b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='use_sellertool' id='use_sellertool_y' value='Y' ".CompareReturnValue("Y",$mall_config[use_sellertool],"checked")."><label for='use_sellertool_y'>사용함</label>
		   <input type=radio name='use_sellertool' id='use_sellertool_n' value='N' ".CompareReturnValue("N",$mall_config[use_sellertool],"checked")."><label for='use_sellertool_n'>사용안함</label> <span style='margin-left:10px;' class='small blu'>제휴판매 설정을 하시면 관련 기능이 자동으로 활성화 됩니다.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 style='".$display_yn_hidden."'>
		<td class='input_box_title'> <b>컨텐츠 판매 설정</b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='contents_selling' id='contents_selling_y' value='Y' ".CompareReturnValue("Y",$db->dt[contents_selling],"checked")."><label for='contents_selling_Y'>사용함</label>
		   <input type=radio name='contents_selling' id='contents_selling_n' value='N' ".CompareReturnValue("N",$db->dt[contents_selling],"checked")."><label for='contents_selling_n'>사용안함</label> <span style='margin-left:10px;' class='small blu'>컨텐츠 판매 설정을 하실경우 상품등록시 컨텐츠 다운로드 파일을 같이 업로드 해야 합니다.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40>
		<td class='input_box_title'> <b>구매 금액대별 사은품 설정</b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='gift_selling' id='gift_selling_y' value='Y' ".CompareReturnValue("Y",$gift_selling,"checked")."><label for='gift_selling_y'>사용함</label>
		   <input type=radio name='gift_selling' id='gift_selling_n' value='N' ".CompareReturnValue("N",$gift_selling,"checked")."><label for='gift_selling_n'>사용안함</label> <span style='margin-left:10px;' class='small blu'>사은품 상품 등록시 설정된 금액 대 별로 사은품 제공 기능을 이용할시에 사용함</span>
		</td>
	</tr>";

    $Contents02 .= "
		<tr bgcolor=#ffffff height=40>
			<td class='input_box_title'> <b>디자인 템플릿 선택 </b><img src='".$required3_path."'></td>
			<td class='input_box_item'>
			<input type=hidden name='bmall_use_templete' value='".$db->dt[mall_use_templete]."'>
			".SelectDirList("mall_use_templete", $DOCUMENT_ROOT.$db->dt[mall_data_root]."/templet", $db->dt[mall_use_templete])."
			</td>
			<td class='input_box_title'> <b>모바일 스킨 템플릿 선택 </b><img src='".$required3_path."'></td>
			<td class='input_box_item'>".SelectDirList("mall_use_mobile_templete", $DOCUMENT_ROOT.$admininfo[mall_data_root]."/mobile_templet", $db->dt[mall_use_mobile_templete])."</td>
		</tr>
		<tr bgcolor=#ffffff height=40 style='".$display_yn_hidden."'>
			<td class='input_box_title'> <b>도로명주소 DB TYPE 설정</b></td>
			<td class='input_box_item' colspan=3>
				<input type=radio name='zipcode_type' id='zipcode_type_Y' value='M' ".CompareReturnValue("M",$mall_config[zipcode_type],"checked")."><label for='zipcode_type_Y'>몰스토리 DB</label>
				<input type=radio name='zipcode_type' id='zipcode_type_N' value='P' ".CompareReturnValue("P",$mall_config[zipcode_type],"checked")."><label for='zipcode_type_N'>우체국 DB</label>
				<input type=radio name='zipcode_type' id='zipcode_type_L' value='L' ".CompareReturnValue("L",$mall_config[zipcode_type],"checked")."><label for='zipcode_type_L'>LINK_HUB</label>
				<input type=radio name='zipcode_type' id='zipcode_type_D' value='D' ".CompareReturnValue("D",$mall_config[zipcode_type],"checked")." ><label for='zipcode_type_D' >DAUM DB사용</label>
				<input type=radio name='zipcode_type' id='zipcode_type_J' value='J' ".CompareReturnValue("J",$mall_config[zipcode_type],"checked")." ><label for='zipcode_type_J' >JAPAN</label>
				<span style='margin-left:10px;' class='small blu'>우체국 DB 사용 시 <a href='http://www.data.go.kr' target=_blank class='small blu'>www.data.go.kr</a> 에서 도로명주소조회서비스 API 인증 KEY 를 발급 받으셔야 합니다.</span>
			</td>
		</tr>
		<tr bgcolor=#ffffff height=40 class='zipcode_key' ".($mall_config[zipcode_type] == 'P' ? "" : "style='display:none;")."'>
			<td class='input_box_title'> <b>우편번호 KEY 설정</b></td>
			<td class='input_box_item' colspan=3>
				<input type='text' name='zipcode_key' size='130' value='".$mall_config[zipcode_key]."' />
			  <span style='margin-left:10px;' class='small blu'>발급받은 KEY 값을 입력 해 주세요.</span>
			</td>
		</tr>
		<tr bgcolor=#ffffff height=40 class='linkhub_id' ".($mall_config[zipcode_type] == 'L' ? "" : "style='display:none;")."'>
			<td class='input_box_title'> <b>링크허브 ID 설정</b></td>
			<td class='input_box_item' colspan=3>
				<input type='text' name='linkhub_id' size='30' value='".$mall_config[linkhub_id]."' />
			  <span style='margin-left:10px;' class='small blu'>발급받은 ID 값을 입력 해 주세요.</span>
			</td>
		</tr>
		
		<tr bgcolor=#ffffff height=40 class='linkhub_key' ".($mall_config[zipcode_type] == 'L' ? "" : "style='display:none;")."'>
			<td class='input_box_title'> <b>링크허브 KEY 설정</b></td>
			<td class='input_box_item' colspan=3>
				<input type='text' name='linkhub_key' size='80' value='".$mall_config[linkhub_key]."' />
			  <span style='margin-left:10px;' class='small blu'>발급받은 KEY 값을 입력 해 주세요.</span>
			</td>
		</tr>
		
		<tr bgcolor=#ffffff height=40 style='".$display_yn_hidden."'>
			<td class='input_box_title'> <b>검색엔진 사용 설정</b></td>
			<td class='input_box_item' colspan=3>
			   <input type=radio name='search_engine_yn' id='search_engine_y' value='Y' ".CompareReturnValue("Y",$search_engine_yn,"checked")."><label for='search_engine_y'>사용함</label>
			   <input type=radio name='search_engine_yn' id='search_engine_n' value='N' ".CompareReturnValue("N",$search_engine_yn,"checked")."><label for='search_engine_n'>사용안함</label> <span style='margin-left:10px;' class='small blu'>상품 검색 시 검색엔진을 사용하실 경우 설정 기능</span>
			</td>
		</tr>
		<tr bgcolor=#ffffff height=40 class='search_engine' ".($search_engine_yn == 'Y' ? "" : "style='display:none;")."'>
			<td class='input_box_title'> <b>검색엔진 선택</b></td>
			<td class='input_box_item' colspan=3>
			   <input type=radio name='search_engine_type' id='search_engine_type_s' value='S' ".CompareReturnValue("S",$search_engine_type,"checked")."><label for='search_engine_type_s'>스핑크스</label>
			   <input type=radio name='search_engine_type' id='search_engine_type_d' value='D' ".CompareReturnValue("D",$search_engine_type,"checked")."><label for='search_engine_type_d'>다이퀘스트</label> <span style='margin-left:10px;' class='small blu'>다이퀘스트 검색엔진 사용시 별도 가입 절차 필요 솔루션 관리자에 문의바랍니다.</span>
			</td>
		</tr>
		<!--
		<tr bgcolor=#ffffff height=40>
			<td class='input_box_title'> <b>신규 프로모션 전시 설정</b></td>
			<td class='input_box_item' colspan=3>
			   <input type=radio name='new_promotion' id='new_promotion_y' value='Y' ".CompareReturnValue("Y",$db->dt[new_promotion],"checked")."><label for='contents_selling_Y'>사용함</label>
			   <input type=radio name='new_promotion' id='new_promotion_n' value='N' ".CompareReturnValue("N",$db->dt[new_promotion],"checked")."><label for='contents_selling_n'>사용안함</label> 
			   <span style='margin-left:10px;' class='small blu'>광고결과 분석,담당MD 설정, 광고영역분류 세분화 등이 추가된 기능을 사용할 수 있으며 사용시, 기존에 이미 사용하시던 프로모션전시 데이터는 사용할 수 없습니다.</span>
			</td>
		</tr>
		-->
		<!--
		<tr bgcolor=#ffffff height=40>
			<td class='input_box_title'> <b>SNS 공유 사용 설정</b></td>
			<td class='input_box_item' colspan=3>
			   <input type=radio name='sns_link_yn' id='sns_link_y' value='Y' ".CompareReturnValue("Y",$sns_link_yn,"checked")."><label for='sns_link_y'>사용함</label>
			   <input type=radio name='sns_link_yn' id='sns_link_n' value='N' ".CompareReturnValue("N",$sns_link_yn,"checked")."><label for='sns_link_n'>사용안함</label> <span style='margin-left:10px;' class='small blu'>상품 공유 설정 기능</span>
			</td>
		</tr>
		<tr bgcolor=#ffffff height=40 class='sns_link' ".($sns_link_yn == 'Y' ? "" : "style='display:none;")."'>
			<td class='input_box_title'> <b>SNS 및 공유 항목 설정</b></td>
			<td class='input_box_item' colspan=3>
				<input type=checkbox name='sns[]' id='url_copy' value='url-copy' ".getStrpos("url-copy",$sns,"checked")."><label for='url_copy'>URL 복사</label>
				<input type=checkbox name='sns[]' id='sns_fb' value='facebook' ".getStrpos("facebook",$sns,"checked")."><label for='sns_fb'>Facebook</label>
				<input type=checkbox name='sns[]' id='sns_t' value='twitter' ".getStrpos("twitter",$sns,"checked")."><label for='sns_t'>Twitter</label>
				<input type=checkbox name='sns[]' id='sns_p' value='pinterest' ".getStrpos("pinterest",$sns,"checked")."><label for='sns_p'>Pinterest</label>
				<input type=checkbox name='sns[]' id='sns_n' value='naver' ".getStrpos("naver",$sns,"checked")."><label for='sns_n'>Naver</label>
				<input type=checkbox name='sns[]' id='sns_ks' value='kakaostory' ".getStrpos("kakaostory",$sns,"checked")."><label for='sns_ks'>Kakaostory</label>
				<input type=checkbox name='sns[]' id='sns_k' value='kakaotalk' ".getStrpos("kakaotalk",$sns,"checked")."><label for='sns_k'>KakaoTalk</label>
				<input type=checkbox name='sns[]' id='sns_b' value='band' ".getStrpos("band",$sns,"checked")."><label for='sns_b'>Band</label>
				<input type=checkbox name='sns[]' id='sns_g' value='google' ".getStrpos("google",$sns,"checked")."><label for='sns_g'>Google+</label>
                <input type=checkbox name='sns[]' id='sns_l' value='line' ".getStrpos("line",$sns,"checked")."><label for='sns_l'>Line</label>
			</td>
		</tr>
		-->
		<tr bgcolor=#ffffff height=40>
			<td class='input_box_title'> <b>상품 색상 코드 설정</b></td>
			<td class='input_box_item' colspan=3>
			   <input type='button' value='색상코드설정' onclick=\"PoPWindow('./color_code.php?mmode=pop',500,500,'color_code')\" />
			</td>
		</tr>
		<tr bgcolor=#ffffff height=40>
			<td class='input_box_title'> <b>통계관리 도메인 등록</b></td>
			<td class='input_box_item' colspan=3>
			   <input type='text' class='textbox' name='logstory_url' style='width:230px;' value='".$mall_config['logstory_url']."' placeholder='ex.)https://www.xxx.com' />
			</td>
		</tr>
		<tr bgcolor=#ffffff height=40>
			<td class='input_box_title'> <b>프론트 도메인 등록</b></td>
			<td class='input_box_item' colspan=3>
			   <input type='text' class='textbox' name='front_url' style='width:230px;' value='".$mall_config['front_url']."' placeholder='ex.)https://www.xxx.com' />
			</td>
		</tr>
</table>

<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' >
	<tr><td height='5'></td></tr>
	<tr>
		<td><img src='../image/emo_3_15.gif' align=absmiddle > ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')."</td>
	</tr>
</table>
 </div>
";
}


// naver 페이 PG 추가
$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-top:10px;' >
		<col width='200px'>
		<col width='*'>
			<tr onclick=\"show_info('naver_pay_info');\" style='cursor:pointer'>
				<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>네이버페이(PG) 정보</b><span style='padding-left:2px' class='helpcloud' help_width='430' help_height='50' help_html='네이버 페이를 사용 시 네이버를 통해 관련된 설정 값을 받은 이후 사용 가능 합니다..'><img src='/admin/images/icon_q.gif' /></span></div>")."</td>
			</tr>
		</table>

		<div class='naver_pay_info'>
		<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
		<col width='200px'>
		<col width='*'>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > 네이버페이(PG) 사용유무</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
								<input type='radio' name='add_sattle_module_naverpay_pg' id='add_sattle_module_naverpay_pg_y' value='Y' ".CompareReturnValue("Y",$add_sattle_module_naverpay_pg,"checked")."><label for='add_sattle_module_naverpay_pg_y' >사용함</label>
								<input type='radio' name='add_sattle_module_naverpay_pg' id='add_sattle_module_naverpay_pg_n' value='N' ".CompareReturnValue("N",$add_sattle_module_naverpay_pg,"checked")."><label for='add_sattle_module_naverpay_pg_n' >사용안함</label> 
							</td>
							<td style='padding-left:20px;'></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > 서비스 타입</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
								<input type='radio' name='naverpay_pg_service_type' id='naverpay_pg_service_type_y' value='test' ".CompareReturnValue("test",$naverpay_pg_service_type,"checked")."><label for='naverpay_pg_service_type_y' >test</label>
								<input type='radio' name='naverpay_pg_service_type' id='naverpay_pg_service_type_n' value='service' ".CompareReturnValue("service",$naverpay_pg_service_type,"checked")."><label for='naverpay_pg_service_type_n' >service</label> 
							</td>
							<td style='padding-left:20px;'></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > 파트너 ID</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=text class='textbox' name='naverpay_pg_partner_id' value='".$naverpay_pg_partner_id."' style='width:280px;' ></td>
							<td style='padding-left:20px;'></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > Client ID</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=text class='textbox' name='naverpay_pg_client_id' value='".$naverpay_pg_client_id."' style='width:280px;' ></td>
							<td style='padding-left:20px;'></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title'> Client Secret</td>
				<td class='input_box_item'>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=text class='textbox' name='naverpay_pg_client_secret' value='".$naverpay_pg_client_secret."' style='width:280px; line-height:120%;' ></td>
							<td style='padding-left:20px;'>  </td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</div>";

$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-top:10px;' >
		<col width='200px'>
		<col width='*'>
			<tr style='cursor:pointer'>
				<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>엑심베이 정보</b></div>")."</td>
			</tr>
		</table>
		<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
		<col width='200px'>
		<col width='*'>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > 서비스 타입</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
								<input type='radio' name='eximbay_service_type' id='eximbay_service_type_n' value='test' ".CompareReturnValue("test",$mall_config[eximbay_service_type],"checked")."><label for='eximbay_service_type_n' >test</label>
								<input type='radio' name='eximbay_service_type' id='eximbay_service_type_y' value='service' ".CompareReturnValue("service",$mall_config[eximbay_service_type],"checked")."><label for='eximbay_service_type_y' >service</label> 
							</td>
							<td style='padding-left:20px;'></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > mid</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=text class='textbox' name='eximbay_mid' value='".$mall_config[eximbay_mid]."' style='width:280px;' ></td>
							<td style='padding-left:20px;'></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > secret key</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=text class='textbox' name='eximbay_secret_key' value='".$mall_config[eximbay_secret_key]."' style='width:280px;' ></td>
							<td style='padding-left:20px;'></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>";

$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-top:10px;' >
		<col width='200px'>
		<col width='*'>
			<tr onclick=\"show_info('toss_info');\" style='cursor:pointer'>
				<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>토스 정보</b></div>")."</td>
			</tr>
		</table>

		<div class='toss_info'>
		<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
		<col width='200px'>
		<col width='*'>
		<tr bgcolor=#ffffff >
				<td class='input_box_title' > 토스 사용유무</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
								<input type='radio' name='add_sattle_module_toss' id='add_sattle_module_toss_y' value='Y' ".CompareReturnValue("Y",$add_sattle_module_toss,"checked")."><label for='add_sattle_module_toss_y' >사용함</label>
								<input type='radio' name='add_sattle_module_toss' id='add_sattle_module_toss_n' value='N' ".CompareReturnValue("N",$add_sattle_module_toss,"checked")."><label for='add_sattle_module_toss_n' >사용안함</label> 
							</td>
							<td style='padding-left:20px;'></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > 서비스 타입</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
								<input type='radio' name='toss_service_type' id='toss_service_type_y' value='test' ".CompareReturnValue("test",$toss_service_type,"checked")."><label for='toss_service_type_y' >test</label>
								<input type='radio' name='toss_service_type' id='toss_service_type_n' value='service' ".CompareReturnValue("service",$toss_service_type,"checked")."><label for='toss_service_type_n' >service</label> 
							</td>
							<td style='padding-left:20px;'></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > apiKey</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=text class='textbox' name='toss_api_key' value='".$toss_api_key."' style='width:280px;' ></td>
							<td style='padding-left:20px;'></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</div>";

/*
$Contents01 .= "
		<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-top:10px;' >
		<col width='200px'>
		<col width='*'>
			<tr onclick=\"show_info('naver_pay_info');\" style='cursor:pointer'>
				<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>네이버페이 정보</b><span style='padding-left:2px' class='helpcloud' help_width='430' help_height='50' help_html='네이버 페이를 사용 시 네이버를 통해 관련된 설정 값을 받은 이후 사용 가능 합니다..'><img src='/admin/images/icon_q.gif' /></span></div>")."</td>
			</tr>
		</table>

		<div class='naver_pay_info'>
		<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
		<col width='200px'>
		<col width='*'>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > 네이버페이 사용유무</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
								<input type='radio' name='naverpay_yn' id='naverpay_yn_y' value='Y' ".CompareReturnValue("Y",$naverpay_yn,"checked")."><label for='naverpay_yn_y' >사용함</label>
								<input type='radio' name='naverpay_yn' id='naverpay_yn_n' value='N' ".CompareReturnValue("N",$naverpay_yn,"checked")."><label for='naverpay_yn_n' >사용안함</label> 
							</td>
							<td style='padding-left:20px;'></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > 서비스 타입</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
								<input type='radio' name='naverpay_type' id='naverpay_type_y' value='test' ".CompareReturnValue("test",$naverpay_type,"checked")."><label for='naverpay_type_y' >test</label>
								<input type='radio' name='naverpay_type' id='naverpay_type_n' value='service' ".CompareReturnValue("service",$naverpay_type,"checked")."><label for='naverpay_type_n' >service</label> 
							</td>
							<td style='padding-left:20px;'></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > 상점 ID</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=text class='textbox' name='naverpay_id' value='".$naverpay_id."' style='width:280px;' ></td>
							<td style='padding-left:20px;'></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > 네이버 EP 사용여부</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
								<input type='radio' name='naverpay_ep' id='naverpay_ep_y' value='Y' ".CompareReturnValue("Y",$naverpay_ep,"checked")."><label for='naverpay_ep_y' >사용함</label>
								<input type='radio' name='naverpay_ep' id='naverpay_ep_n' value='N' ".CompareReturnValue("N",$naverpay_ep,"checked")."><label for='naverpay_ep_n'>사용안함</label>
							</td>
							<td style='padding-left:20px;' class='small blu'>지식쇼핑 가맹점일 경우 사용함 를 필수로 선택 바랍니다.</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title' > 가맹점 인증키</td>
				<td class='input_box_item' >
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=text class='textbox' name='naverpay_key' value='".$naverpay_key."' style='width:280px;' ></td>
							<td style='padding-left:20px;'></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title'> 버튼 인증키</td>
				<td class='input_box_item'>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=text class='textbox' name='naverpay_button_key' value='".$naverpay_button_key."' style='width:280px; line-height:120%;' ></td>
							<td style='padding-left:20px;'>  </td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</div>";


*/



//$admininfo[mall_type] = "H"; //글로벌 지원설정 강제로 막음
if($admininfo[mall_type] != "H" && FALSE){

    $Contents02 .= "
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-top:20px;' >
<col width='200px'>
<col width='*' />
	<tr onclick=\"show_info('money_info');\" style='cursor:pointer'>
		<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>글로벌 지원설정</b></div>")."</td>
	</tr>
</table>
<div class='money_info'>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-bottom:0px;' class='input_table_box'>
<col width='200px' />
<col width='*' />
<col width='200px' />
<col width='*' />
	<tr bgcolor=#ffffff height=40>
		<td class='input_box_title' style='width:150px;'> <b>국가 선택</b></td>
		<td class='input_box_item' colspan='3'>";

    $sql = "SELECT gn.*, gc.currency_name, gl.language_name 
			FROM global_nation gn 
			left join global_currency gc on gn.currency_ix  = gc.currency_ix  
			left join global_language gl on gn.language_ix  = gl.language_ix   ";
    $db->query($sql);
    $NATION = $db->fetchall("object");

    $Contents02 .= "<select name='nation_code'>";
    $Contents02 .= "<option value=''>국가</option>";
    foreach($NATION as $key => $value){
        $Contents02 .= "<option value='".$value[nation_code]."' ".($mall_config[nation_code] == $value[nation_code] ? "selected":"").">".$value[nation_name]." (언어:".$value[language_name].") (환율:".$value[currency_name].") </option>";
    }
    $Contents02 .= "		   
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40>
		<td class='input_box_title'> <b>번역기 설정</b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='translator' id='translator_y' value='Y' ".CompareReturnValue("Y",$db->dt[translator],"checked")."><label for='translator_Y'>사용함</label>
		   <input type=radio name='translator' id='translator_n' value='N' ".CompareReturnValue("N",$db->dt[translator],"checked")."><label for='translator_n'>사용안함</label> <span style='margin-left:10px;' class='small blu'>설정시 상품 상세 화면에 번역기가 자동으로 노출됩니다.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40>
		<td class='input_box_title'> <b>단위 환산기 설정</b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='unit_conversion' id='unit_conversion_y' value='Y' ".CompareReturnValue("Y",$db->dt[unit_conversion],"checked")."><label for='unit_conversion_Y'>사용함</label>
		   <input type=radio name='unit_conversion' id='unit_conversion_n' value='N' ".CompareReturnValue("N",$db->dt[unit_conversion],"checked")."><label for='unit_conversion_n'>사용안함</label> <span style='margin-left:10px;' class='small blu'>설정시 상품 상세 화면에 단위환산기가 자동으로 노출됩니다.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40>
		<td class='input_box_title'> <b>다중 가격표시 설정</b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='currency_view' id='currency_view_y' value='Y' ".CompareReturnValue("Y",$db->dt[currency_view],"checked")."><label for='currency_view_Y'>사용함</label>
		   <input type=radio name='currency_view' id='currency_view_n' value='N' ".CompareReturnValue("N",$db->dt[currency_view],"checked")."><label for='currency_view_n'>사용안함</label> <span style='margin-left:10px;' class='small blu'>설정시 입력된 환율정보에 따라서 국가별 가격이 자동 노출되게 됩니다</span>
		</td>
	</tr>
	</table>";

    /*
    $Contents05_1 = "
    <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
    <col width=150 />
    <col width=* />
        <tr>
            <td align='left' colspan=2> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b>페이지 형식 선택</b></div>")."</td>
        </tr>
        <tr bgcolor=#ffffff height=40>
            <td style='width:170px;'> <b>페이지 형식 선택 </b><img src='".$required3_path."'></td>
            <td>
                <input type='radio' name='mall_page_type' id='mall_page_type_P' value='P' ".CompareReturnValue("P",$db->dt[mall_page_type],"checked")." /> <label for='mall_page_type_P'>일반 형식</label>
                <!--input type='radio' name='mall_page_type' id='mall_page_type_S' value='S' ".CompareReturnValue("S",$db->dt[mall_page_type],"checked")." /> <label for='mall_page_type_S'>SNS 형식</label-->
                <input type='radio' name='mall_page_type' id='mall_page_type_M' value='M' ".CompareReturnValue("M",$db->dt[mall_page_type],"checked")." /> <label for='mall_page_type_M'>모바일 형식</label>
            </td>
        </tr>
        <tr height=1><td colspan=2 class=dot-x></td></tr>
        <tr height=30><td colspan=2 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 페이지 형식을 선택해 주세요 </span></td></tr>
    </table>";
    */

    $ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr>
		<td align=left style='padding:10px;'>
			<img src='../image/emo_3_15.gif' align=absmiddle>  거래은행 및 계좌 정보는 결제시 이용됩니다. 정확하게 입력해주세요
		</td>
	</tr>
</table>
</div>
";
}
$Contents03 = "
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' >
<col width='200px'>
<col width='*' />
	<tr onclick=\"show_info('title_info');\" style='cursor:pointer'> 
		<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:2px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>타이틀 및 검색엔진 키워드</b> <a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_타이틀변경(090108)_config.xml',800,517,'manual_view')\"  title='쇼핑몰 타이틀 변경 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a></div>")."</td>
	</tr>
</table>

<div class='title_info'>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
<col width='200px'>
<col width='*' />
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>타이틀 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding:5px;'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>
					<input type=text class='textbox' name='mall_title' value='".$db->dt[mall_title]."' style='width:230px;' validation='true' title='타이틀'>
					</td>
					<td style='padding-left:10px;'>
					<span class=small>  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'G')."</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>검색엔진키워드</b></td>
		<td class='input_box_item'><input type=text class='textbox' name='mall_keyword' value='".$db->dt[mall_keyword]."' style='width:98%;'> <span class=small></span></td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>다중전시 사용여부 </b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>
					<input type=radio name='front_multiview' id='front_multiview_y' value='Y' ".CompareReturnValue("Y",$_shop_config["front_multiview"],"checked")."><label for='front_multiview_y'>사용함</label>
					<input type=radio name='front_multiview' id='front_multiview_n' value='N' ".CompareReturnValue("N",$_shop_config["front_multiview"],"checked")."><label for='front_multiview_n'>사용안함</label>
					</td>
					<td align=left style='line-height:150%;padding-left:10px;' class='small blu'>&nbsp;다중전시 사용함을 선택할경우 전시관리에서 각 프론트 뷰에 따라 전시 설정을 할수 있습니다.</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' >
	<tr><td height='5'></td></tr>
	<tr>
		<td><img src='../image/emo_3_15.gif' align=absmiddle > ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'H')."</td>
	</tr>
</table>
  </div>
  ";

/*

$Contents04 = "
<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='150' />
<col width='250' />
<col width='*' />
	<tr>
		<td align='left' colspan=3> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b>회원전용 사용여부</b></div>")."</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td> 사용권한설정</td>
		<td>
			<input type=radio name='mall_open_yn' id='mall_open_y' value='Y' ".CompareReturnValue("Y",$db->dt[mall_open_yn],"checked")."><label for='mall_open_y'>회원전용</label>
			<input type=radio name='mall_open_yn' id='mall_open_n' value='N' ".CompareReturnValue("N",$db->dt[mall_open_yn],"checked")."><label for='mall_open_n'>전체</label>
		</td>
		<td align=left><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>회원전용을 선택하실 경우 첫화면은 로그인 페이지가 됩니다.  </span></td>
	</tr>
	<tr height=1><td colspan=3 class=dot-x></td></tr>
	<!--tr bgcolor=#ffffff >
		<td > 인트로사용</td>
		<td>
			<input type=radio name='mall_intro_use' id='mall_intro_use_y' value='Y' ".CompareReturnValue("Y",$db->dt[mall_intro_use],"checked")."><label for='mall_intro_use_y'>사용</label>
			<input type=radio name='mall_intro_use' id='mall_intro_use_n' value='N' ".CompareReturnValue("N",$db->dt[mall_intro_use],"checked")."><label for='mall_intro_use_n'>사용 하지않음</label>
			<span class=small style='color:gray'>디자인 관리에서 인트로 화면에 대한 디자인 작업을 하셔야 합니다.</span>
		</td>
		<td align=left></td>
	</tr-->
	<tr height=1><td colspan=3 style='padding-left:160px;'></td></tr>
</table>";
*/

if($admininfo[mall_type] != "H"){

    if($admininfo[mall_type] == "B" || $admininfo[mall_type] == "O" || $admininfo[mall_type] == "E"){
        $Contents06 = "
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' >
<col width='200px'>
<col width='250' />
<col width='*' />
	<tr onclick=\"show_info('inventory_info');\" style='cursor:pointer'>
		<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>재고관리 사용여부</b></div>")."</td>
	</tr>
</table>

<div class='inventory_info'>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-bottom:20px;' class='input_table_box'>
<col width='200px'>
<col width='*' />
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>재고관리설정</b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>
					<input type=radio name='mall_use_inventory' id='mall_use_inventory_y' value='Y' ".CompareReturnValue("Y",$db->dt[mall_use_inventory],"checked")."><label for='mall_use_inventory_y'>사용함</label>
					<input type=radio name='mall_use_inventory' id='mall_use_inventory_n' value='N' ".CompareReturnValue("N",$db->dt[mall_use_inventory],"checked")."><label for='mall_use_inventory_n'>사용안함</label>
					</td>
					<td align=left style='line-height:150%;padding-left:10px;'>&nbsp;".getTransDiscription(md5($_SERVER["PHP_SELF"]),'I')."</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>품목분류설정</b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>";
        if($_SESSION["admininfo"]["charger_id"]=="forbiz"){
            $Contents06 .= "
						<input type=radio name='mall_inventory_category_div' id='mall_inventory_category_i' value='I' ".CompareReturnValue("I",$mall_config[mall_inventory_category_div],"checked")."><label for='mall_inventory_category_i'>품목분류 개별사용</label>
						<input type=radio name='mall_inventory_category_div' id='mall_inventory_category_p' value='P' ".CompareReturnValue("P",$mall_config[mall_inventory_category_div],"checked")."><label for='mall_inventory_category_p'>품목분류 상품분류와 동일하게 사용</label>";
        }else{
            $Contents06 .= "
						<input type=hidden name='mall_inventory_category_div' value='".$mall_config[mall_inventory_category_div]."' /> ";

            if($mall_config[mall_inventory_category_div]=="P")
                $Contents06 .= "품목분류 상품분류와 동일하게 사용";
            else
                $Contents06 .= "품목분류 개별사용";
        }
        $Contents06 .= "
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
  ";

        $Contents06 .= "
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' >
<col width='200px'>
<col width='250' />
<col width='*' />
	<tr onclick=\"show_info('inventory_info');\" style='cursor:pointer'>
		<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>표준카테고리 사용여부</b></div>")."</td>
	</tr>
</table>

<div class='inventory_info'>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-bottom:20px;' class='input_table_box'>
<col width='200px'>
<col width='*' />
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>표준카테고리 설정</b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>
					<input type=radio name='mall_use_standard_category' id='mall_use_standard_category_y' value='Y' ".CompareReturnValue("Y",$mall_config[mall_use_standard_category],"checked")."><label for='mall_use_standard_category_y'>사용함</label>
					<input type=radio name='mall_use_standard_category' id='mall_use_standard_category_n' value='N' ".CompareReturnValue("N",$mall_config[mall_use_standard_category],"checked")."><label for='mall_use_standard_category_n'>사용안함</label>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
  ";

    }
}
/*
$Contents07 = "
<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='150' />
<col width='250' />
<col width='*' />
	<tr>
		<td align='left' colspan=3> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b>회원가입시 실명인증 사용여부</b></div>")."</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td style='width:150px;'> 실명인증 사용여부</td>
		<td>
			<input type='checkbox' id='mall_use_identificationUse' name='mall_use_identificationUse' value='Y'".(($db->dt[mall_use_identification])	?	' checked':'')." onclick=\"document.getElementById('mall_use_identification').style.display = (this.checked)	?	'':'none';\" style='vertical-align:middle;'' /> <label for='mall_use_identificationUse' style='vertical-align:middle;'>사용함</label>
			<input type='text' id='mall_use_identification' name='mall_use_identification' class='textbox' value='".$db->dt[mall_use_identification]."' style='display:".(($db->dt[mall_use_identification])	?	'':'none').";'  />
			<!--input type=radio name='mall_use_identification' id='mall_use_identification_y' value='Y' ".CompareReturnValue("Y",$db->dt[mall_use_identification],"checked")."><label for='mall_use_identification_y'>사용함</label>
			<input type=radio name='mall_use_identification' id='mall_use_identification_n' value='N' ".CompareReturnValue("N",$db->dt[mall_use_identification],"checked")."><label for='mall_use_identification_n'>사용안함</label-->
		</td>
		<td align=left><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>몰스토리를 통해 한국신용정보와 계약을 맺으신후 진행을 하셔야 합니다.   </span></td>
	</tr>
	<tr height=1><td colspan=3 class=dot-x></td></tr>
	<tr height=1><td colspan=3 style='padding-left:160px;'></td></tr>
</table>";
*/
if($admininfo[mall_type] != "H"){
    $Contents08 = "
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' >
<col width='200px'>
<col width='*' />
	<tr  onclick=\"show_info('order_info');\" style='cursor:pointer'>
		<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>자동주문상태 변경 설정</b></div>")."</td>
	</tr>
</table>
<div class='order_info'>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
<col width='200px'>
<col width='*' />
	<tr bgcolor=#ffffff >
		<td class='input_box_title'>
		<b>부가세 비율 설정 <img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item' style='padding:5px;'>
			<table cellpadding=0 cellspacing=0 width='100%'>
				<tr>
					<select name='surtax_vat' style='width:50px;'>
					<option value='0' ".($mall_config[surtax_vat] == '0' ? "selected":"")."> 0 </option>
					<option value='10' ".($mall_config[surtax_vat] == '10' || $mall_config[surtax_vat] == '' ? "selected":"")."> 10 </option>
					<option value='12' ".($mall_config[surtax_vat] == '12' ? "selected":"")."> 12 </option>
					</select> % &nbsp;&nbsp;* 주문시 상품 부가세 비율
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'>
		<b>자동배송완료기간 <img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item' style='padding:5px;'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td nowrap><input type=text class='textbox' name='mall_dc_interval'  value='".$db->dt[mall_dc_interval]."' size=5 validation='true' title='자동구매완료기간'> 일 이상된 배송중 주문</td>
					<td class='input_box_item' style='padding:5px  5px 5px 25px;' align=left><span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'J')."</span> </td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>자동주문취소기간 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding:5px'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td nowrap><input type=text class='textbox' name='mall_cc_interval'  value='".$db->dt[mall_cc_interval]."' size=5 validation='true' title='자동주문취소기간'> 일 이상된 입금예정 주문</td>
					<td class='input_box_item' style='padding:5px  5px 5px 15px;' align=left> <span class=small > ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'K')."</span> </td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<table>
	<tr><td height='10'></td></tr></table>

<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
<col width='200px'>
<col width='*' />
	<tr bgcolor=#ffffff >
		<td class='input_box_title'>
		<b>장바구니 비움기간 <img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item' style='padding:5px;'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td nowrap><input type=text class='textbox' name='cart_delete_day'  value='".$cart_delete_day."' size=5 validation='true' title='장바구니 비움기간'> 일 이상된 장바구니 상품 비움 기간에 대한 설정입니다.</td>
					<!--<td class='input_box_item' style='padding:5px  5px 5px 25px;' align=left><span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'J')."</span> </td>-->
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>취소요청 자동완료기간 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding:5px'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td nowrap><input type=text class='textbox' name='cancel_auto_day'  value='".$cancel_auto_day."' size=5 validation='true' title='취소요청 자동완료기간'> 일 이상된 취소요청 주문을 자동 '취소완료' 처리상태로 변경되는 기간에 대한 설정입니다.</td>
					<!--<td class='input_box_item' style='padding:5px  5px 5px 15px;' align=left> <span class=small > ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'K')."</span> </td>-->
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>구매확정일 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding:5px'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td nowrap> 배송완료일로 부터 회원이 구매확정을 하지 않을 경우 <input type=text class='textbox' name='check_order_day'  value='".$check_order_day."' size=4 validation='true' title='구매확정일 설정'> 일후 자동 구매확정으로 상태변경할 수 있게 설정합니다.</td>
					<!--<td class='input_box_item' style='padding:5px  5px 5px 15px;' align=left> <span class=small > ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'K')."</span> </td>-->
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>SOS티켓 구매확정일 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding:5px'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td nowrap> 입금완료일 부터 회원이 주문취소 하지 않을 경우 <input type=text class='textbox' name='check_sos_order_day'  value='".$check_sos_order_day."' size=4 validation='true' title='구매확정일 설정'> 일후 자동 구매확정으로 상태변경할 수 있게 설정합니다.</td>					
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>공휴일등록</b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding=0 cellspacing=0 width='100%'>
				<tr>
					<td class='input_box_item'>
						<textarea type=text class='textbox' name='holiday_text' style='width:98%;height:85px;padding:2px;'>".$holiday_text."</textarea>
					</td>
				</tr>
				<tr><td>ex) 2015-05-05,2015-05-25</td></tr>
			</table>
		</td>
	</tr>
	<!--관리기능이 개인정보 관리 설정 페이지로 이동함에 따른 기능 숨김-->
   
	<tr bgcolor=#ffffff style='display:none;'>
		<td class='input_box_title'> <b>관리자접근허용설정</b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding=0 cellspacing=0 width='100%'>
				<tr>
					<td>
						<input type=radio name='admin_access_yn' id='admin_access_y' value='Y' ".CompareReturnValue("Y",$admin_access_yn,"checked")."><label for='admin_access_y'>사용함</label>
						<input type=radio name='admin_access_yn' id='admin_access_n' value='N' ".CompareReturnValue("N",$admin_access_yn,"checked")."><label for='admin_access_n'>사용안함</label>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	
	<tr bgcolor=#ffffff class='admin_access_input' ".($admin_access_yn == 'Y' ? "" : "style='display:none;'")." style='display:none;'>
		<td class='input_box_title'> <b>관리자접근허용IP입력</b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding=0 cellspacing=0 width='100%'>
				<tr>
					<td class='input_box_item'>
						<textarea type=text class='textbox' name='admin_access_ip' style='width:98%;height:85px;padding:2px;'>".$admin_access_ip."</textarea>
					</td>
				</tr>
				<tr><td>ex) 221.151.188.11,XXX.XXX.XXX.XXX (공유기IP 대역이 아닌 통신사로 부여받은 고유 IP) IP확인 방법 <a href='http://www.ipconfig.co.kr' target=_blank ><span class='blu'>IP확인</span></a> 접속 후 확인</td></tr>
			</table>
		</td>
	</tr>
	
	<!--tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>셀러 정산일 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding:5px'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td nowrap> 처리 상태 
					<select name='order_status'>
						<option value='' >상태변경</option>
						<option value='".ORDER_STATUS_INCOM_READY."' >".getOrderStatus(ORDER_STATUS_INCOM_READY)."</option>
						<option value='".ORDER_STATUS_INCOM_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
						<option value='' >=============</option>
						<option value='".ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY."' >".getOrderStatus(ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY)."</option>
						<option value='".ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_ING."' >".getOrderStatus(ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_ING)."</option>
						<option value='".ORDER_STATUS_AIR_TRANSPORT_READY."' >".getOrderStatus(ORDER_STATUS_AIR_TRANSPORT_READY)."</option>
						<option value='".ORDER_STATUS_AIR_TRANSPORT_ING."' >".getOrderStatus(ORDER_STATUS_AIR_TRANSPORT_ING)."</option>
						<option value='' >=============</option>
						<option value='".ORDER_STATUS_WAREHOUSING_STANDYBY."' >".getOrderStatus(ORDER_STATUS_WAREHOUSING_STANDYBY)."</option>
						<option value='".ORDER_STATUS_DELIVERY_READY."' >".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</option>
						<option value='".ORDER_STATUS_DELIVERY_ING."' >".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option>
						<option value='".ORDER_STATUS_DELIVERY_COMPLETE."' >".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</option>
						<option value='".ORDER_STATUS_CANCEL_APPLY."' >".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</option>
						<option value='".ORDER_STATUS_CANCEL_COMPLETE."' >".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</option>
						<option value='".ORDER_STATUS_SOLDOUT_CANCEL."' >".getOrderStatus(ORDER_STATUS_SOLDOUT_CANCEL)."</option>
						<option value='".ORDER_STATUS_RETURN_APPLY."' >".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</option>
						<option value='".ORDER_STATUS_EXCHANGE_APPLY."' >".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</option>
					</select>
					후 <input type=text class='textbox' name='seller_account_day'  value='".$seller_account_day."' size=5 validation='true' title='셀러 정산일 설정'> 일 경과후 셀러 판매정산을 설정합니다.</td>
				</tr>
			</table>
		</td>
	</tr-->
</table>
</div>
";

    $Contents10 = "
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' >
<col width='200px'>
<col width='250' />
<col width='*' />
	<tr  onclick=\"show_info('product_info');\" style='cursor:pointer'>
		<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>상품명, 간략설명 사용 불가 단어 관리</b></div>")."</td>
	</tr>
</table>
<div class='product_info'>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-bottom:20px;' class='input_table_box'>
<col width='200px'>
<col width='*' />
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>불가 단어 입력</b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding=0 cellspacing=0 width='100%'>
				<tr>
					<td class='input_box_item'><textarea type=text class='textbox' name='product_prohibition_text' style='width:98%;height:85px;padding:2px;'>".$product_prohibition_text."</textarea></td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' >
	<tr><td height='5'></td></tr>
	<tr>
		<td><img src='../image/emo_3_15.gif' align=absmiddle > ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'M')."</td>
	</tr>
</table>
</div>
  ";
}

if(false){
    $Contents09 = "
	<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' align='left' style='table-layout:fixed;' >
	<col width='200px'>
	<col width='*' />
		<tr onclick=\"show_info('zip_info');\" style='cursor:pointer'>
			<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align='absmiddle'> <b class='blk'>우편번호 DB업데이트</b></div>")."</td>
		</tr>
	</table>
	<div class='zip_info'>
	<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='200px'>
	<col width='*' />
		<tr bgcolor=#ffffff >
			<td class='input_box_title'> <b>우편번호 DB업로드 </b></td>
			<td class='input_box_item'><input type=file name='excel_file' style='width:250px' class='textbox' style='vertical-align:middle;' />  <a href='javascript:update_zipcode()'><img src='../images/".$admininfo["language"]."/btn_upload.gif' style='vertical-align:middle;' /></a></td>
		</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' >
		<tr><td height='5'></td></tr>
		<tr>
			<td><img src='../image/emo_3_15.gif' align=absmiddle > ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'L')."</td>
		</tr>
	</table>
	</div>
	  ";
}
/*
$Contents11 = "
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' >
<col width='200px'>
<col width='250' />
<col width='*' />
	<tr>
		<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>재고관리 사용여부</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-bottom:20px;' class='input_table_box'>
<col width='200px'>
<col width='*' />
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>가격 정책(도소매 판매 설정)</b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>
					<input type=radio name='wholesale_retail_use' id='wholesale_retail_use' value='A' ".CompareReturnValue("A",$db->dt[wholesale_retail_use],"checked")."><label for='mall_use_inventory_y'>도소매 사용</label>
					<input type=radio name='wholesale_retail_use' id='wholesale_retail_use' value='W' ".CompareReturnValue("W",$db->dt[wholesale_retail_use],"checked")."><label for='mall_use_inventory_n'>도매전용</label>
						<input type=radio name='wholesale_retail_use' id='wholesale_retail_use' value='R' ".CompareReturnValue("R",$db->dt[wholesale_retail_use],"checked")."><label for='mall_use_inventory_n'>소매전용</label>

					</td>
					<!--<td align=left style='line-height:150%;padding-left:10px;'>&nbsp;".getTransDiscription(md5($_SERVER["PHP_SELF"]),'I')."</td>-->
				</tr>
			</table>
		</td>
	</tr>
</table>

  ";*/

$Contents12 = "
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' >
<col width='200px'>
<col width='250' />
<col width='*' />
	<tr>
		<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>상품할인율 사용여부</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-bottom:20px;' class='input_table_box'>
<col width='200px'>
<col width='*' />
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>할인율 표기 설정</b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>
                        <label><input type=radio name='product_percent_display' value='Y' ".CompareReturnValue("Y",$mall_config[product_percent_display],"checked").">사용</label>
                        <label><input type=radio name='product_percent_display' value='N' ".CompareReturnValue("N",$mall_config[product_percent_display],"checked").">사용안함</label>					
					</td>					
				</tr>
			</table>
		</td>
	</tr>
</table>
";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
    $ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff height=70><td align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}

$Contents = "<table width='100%' height='100%'  border=0>";
$Contents = $Contents."<form name='edit_form' action='mallinfo.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='act'>
		<input name='act' type='hidden' value='update'><input name='mall_ix' type='hidden' value='".$db->dt[mall_ix]."'>
		<input name='mall_div' type='hidden' value='".$db->dt[mall_div]."'>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=20><td></td></tr>";
$Contents = $Contents."<tr><td>".$Payment02."<br></td></tr>";
if($admininfo[mall_type] != "H"){
    $Contents = $Contents."<tr height=20><td></td></tr>";
    $Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
    $Contents = $Contents."<tr><td>".$Contents05."<br></td></tr>";
    $Contents = $Contents."<tr ><td height=20></td></tr>";
}
//$Contents = $Contents."<tr><td>".$Contents05_1."<br></td></tr>";
//$Contents = $Contents."<tr ><td height=20></td></tr>";
$Contents = $Contents."<tr><td>".$Contents03."<br></td></tr>";
$Contents = $Contents."<tr ><td height=20></td></tr>";
//$Contents = $Contents."<tr><td>".$Contents04."<br></td></tr>";
//$Contents = $Contents."<tr ><td height=20></td></tr>";
$Contents = $Contents."<tr><td>".$Contents06."<br></td></tr>";
$Contents = $Contents."<tr><td>".$Contents12."<br></td></tr>";
//$Contents = $Contents."<tr><td>".$Contents07."<br></td></tr>";
//$Contents = $Contents."<tr ><td height=20></td></tr>";
$Contents = $Contents."<tr><td>".$Contents08."<br></td></tr>";
$Contents = $Contents."<tr ><td height=20></td></tr>";
$Contents = $Contents."<tr><td>".$Contents10."</td></tr>";
$Contents = $Contents."<tr ><td height=20></td></tr>";
$Contents = $Contents."<tr><td>".$Contents09."<br></td></tr>";
$Contents = $Contents."<tr ><td height=10></td></tr>";
$Contents = $Contents."<tr><td>".$Contents11."</td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table >";

//$Contents = "<div style=height:1000px;'></div>";


$Script = "

<script language='javascript' src='basicinfo.js'></script>
<script language='javascript'>
$(function(){
	$('#zipcode_type_N').click(function(){
		$('.zipcode_key').show();
		$('.linkhub_id').hide();
		$('.linkhub_key').hide();
	});
	
	$('#zipcode_type_Y').click(function(){
		$('.zipcode_key').hide();
		$('.linkhub_id').hide();
		$('.linkhub_key').hide();
	});
	
	$('#zipcode_type_D').click(function(){
		$('.zipcode_key').hide();
		$('.linkhub_id').hide();
		$('.linkhub_key').hide();
	});
	
	$('#zipcode_type_L').click(function(){
		$('.zipcode_key').hide();
		$('.linkhub_id').show();
		$('.linkhub_key').show();
	});
	
	$('#zipcode_type_J').click(function(){
		$('.zipcode_key').hide();
		$('.linkhub_id').hide();
		$('.linkhub_key').hide();
	});
	
	$('#paypal_yn_y').click(function(){
		$('#paypal_id').attr('validation',true);
		$('#paypal_id_2').attr('validation',true);
	});
	
	$('#paypal_yn_n').click(function(){
		$('#paypal_id').attr('validation',false);
		$('#paypal_id_2').attr('validation',false);
	});
	
	$('#search_engine_y').click(function(){
		$('.search_engine').show();
	});
	
	$('#search_engine_n').click(function(){
		$('.search_engine').hide();
	});

	$('#sns_link_y').click(function(){
		$('.sns_link').show();
	});
	
	$('#sns_link_n').click(function(){
		$('.sns_link').hide();
	});
	
    $('#kakaopay_n').click(function(){
		$('.kakao_key').hide();
		$('#kakao_mid').attr('validation',false);
		$('#kakao_enckey').attr('validation',false);
		$('#kakao_cancel_pw').attr('validation',false);
		$('#kakao_hashkey').attr('validation',false);
		$('#kakao_shop_key').attr('validation',false);
	});
    
    $('#kakaopay_y').click(function(){
		$('.kakao_key').show();
		$('#kakao_mid').attr('validation',true);
		$('#kakao_enckey').attr('validation',true);
		$('#kakao_cancel_pw').attr('validation',true);
		$('#kakao_hashkey').attr('validation',true);
		$('#kakao_shop_key').attr('validation',true);
	});
    
    $('#kakao_alim_talk_n').click(function(){
		$('.kakao_alim_talk').hide();
		$('#kakao_alim_talk_memberCode').attr('validation',false);
		$('#kakao_alim_talk_apiKey').attr('validation',false);
	});
    
    $('#kakao_alim_talk_y').click(function(){
		$('.kakao_alim_talk').show();
		$('#kakao_alim_talk_memberCode').attr('validation',true);
		$('#kakao_alim_talk_apiKey').attr('validation',true);
	});
    
    $('#admin_access_y').click(function(){
		$('.admin_access_input').show();
	})
	
    $('#admin_access_n').click(function(){
		$('.admin_access_input').hide();
	})
});

function update_zipcode(){
	form = document.edit_form;
	form.action = './zip_act.php';
	form.act.value = 'zipcode';
	form.submit();
}

function show_info(tr_id){
	var id = tr_id;
	$(function (){
	var selectedEffect = 'Clip';
	 var options = {};      // some effects have required parameters     
		 if ( selectedEffect === 'scale' ) { 
		 options = { percent: 0 };    
		 } else if ( selectedEffect === 'size' ) {
			 options = { to: { width: 200, height: 60 } }; 
		}
		$('div.'+id).toggle(500);
	});
}
</script>
";

if($admininfo[mall_type] == "H"){
    $Contents = str_replace("쇼핑몰","사이트",$Contents);
}

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 쇼핑몰 환경설정 > 쇼핑몰정보설정";
$P->title = "쇼핑몰정보설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();


/*
create table admin_menus (
menu_code varchar(32) not null ,
menu_name varchar(255) null default null,
menu_path varchar(255) null default null,
auth_read enum('Y','N') null default 'Y',
auth_write enum('Y','N') null default 'Y',
shipping_company varchar(30) null default null,
primary key(menu_code));


CREATE TABLE IF NOT EXISTS `shop_mall_config` (
  `mall_ix` varchar(32) NOT NULL COMMENT '쇼핑몰키',
  `config_name` varchar(100) NOT NULL DEFAULT '' COMMENT 'PG 환경설정 변수이름',
  `config_value` varchar(255) DEFAULT NULL COMMENT 'PG 환경설정 변수값',
  PRIMARY KEY (`mall_ix`,`config_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='쇼핑몰 환경설정 정보';


*/
?>
