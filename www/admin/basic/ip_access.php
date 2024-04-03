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

$i = 0;
while(count($payment_array)> $i){
    if($payment_array[$i][config_name] == "admin_access_yn")	$admin_access_yn = $payment_array[$i][config_value];
    if($payment_array[$i][config_name] == "admin_access_ip")	$admin_access_ip = $payment_array[$i][config_value];

    $i++;
}

/////////////////////////////////////////////////////////////////////////////
//echo md5("wooho".$db->dt[mall_domain].$db->dt[mall_domain_id]);

$display_yn_hidden = 'display:none;'; //강제로 숨김



if($admininfo[mall_type] != "H"){
    $Contents08 = "
<table>
	<tr><td height='10'></td></tr></table>

<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
<col width='200px'>
<col width='*' />
	<tr bgcolor=#ffffff >
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
	
	<tr bgcolor=#ffffff class='admin_access_input' ".($admin_access_yn == 'Y' ? "" : "style='display:none;'").">
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
</table>
</div>
";
}



if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
    $ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff height=70><td align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}

$Contents = "<table width='100%' height='100%'  border=0>";
$Contents = $Contents."<form name='edit_form' action='ip_access.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act'>
		<input name='act' type='hidden' value='update'><input name='mall_ix' type='hidden' value='".$db->dt[mall_ix]."'>
		<input name='mall_div' type='hidden' value='".$db->dt[mall_div]."'>";

$Contents = $Contents."<tr><td>".$Contents08."<br></td></tr>";
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
$P->strLeftMenu = basic_menu();
$P->Navigation = "기초정보관리 > 본사관리 > 관리자접근제어";
$P->title = "관리자접근제어";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>
