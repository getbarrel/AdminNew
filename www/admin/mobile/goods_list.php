<?
$script_time[start] = time();
include("../class/layout.class");
//include("../class/calender.class");
$script_time[start] = time();
//print_r($_SESSION);

$db = new Database; 

$max = 5;

$Script = "
<script language='javascript' src='shop_main_v3_calender.js'></script>
<script language='JavaScript'>

function sendMessage(msg){
        window.HybridApp.callAndroid(msg);
}

var list_start = $max;
var lastPostFuncBool = true;
function lastPostFunc() {   
	if(lastPostFuncBool){
		$.post('get_data_html.php?act=product_list&start=' + list_start + '&max=".$max."&search_text=".$search_text."',    
		function(data){ 
			if(data=='LOGIN'){
				alert('관리자 로그인후 사용하실수 있습니다.');
				location.href='/admin/mobile/admin.php';
				lastPostFuncBool = false;
			}else if(data.substr(0,6)=='{LAST}'){
				data=data.replace('{LAST}','');
				$('#goods_list_table tr:last').after(data);
				lastPostFuncBool = false;
			}else{
				$('#goods_list_table tr:last').after(data);
				list_start = list_start + ".$max." ;
			}
		});
	}
}

function dispUpdate(obj,pid){

	var change_disp;

	if(obj.attr('src')=='./images/goods_show.png'){
		change_disp=0;
	}else{
		change_disp=1;
	}

	window.frames['act'].location.href='./goods_input.act.php?act=disp_update&change_disp='+change_disp + '&pid=' + pid;
	if(change_disp==1){
		obj.attr('src','./images/goods_show.png');
	}else{
		obj.attr('src','./images/goods_hide.png');
	}
}


/*
$(window).scroll(function(){ 
	if  ($(window).scrollTop() >= $(document).height() - $(window).height()){ 
		lastPostFunc(); 
	}
});
*/

</script>

<style type='text/css'>
.goods_input_header{padding:0 0 0 8px ;background:#ebebeb;border-bottom:2px solid #d3d3d3;}
.goods_input_header:after{content:'';clear:both;display:block;} 
.goods_input_header span{padding-left:9px;background:url('./images/li_bg.gif') 0 center no-repeat;background-size:3px 3px;font-size:15px;font-weight:bold;}
.goods_input_header tr td {height:46px;}
.s_r_box {padding:15px 10px; margin:10px;border:1px solid #c5c5c5;}
.s_r_box h3 {font-size:15px;color:#000000; letter-spacing:-1px; padding:0 0 10px 0; margin:0;}
.s_r_box h3 span {font-size:12px; color:#666666;  }
.search_box {position:relative;}
//.search_box .text_box {margin-right:130px;}
.search_box .text_box {margin-right:90px;}
.search_box .text_box input {border:1px solid #c5c5c5; padding:8px; width:100%;}
//.search_btn {position:absolute; top:1px; right:40px;}
.search_btn {position:absolute; top:1px; right:0px;}
.qrcode {position:absolute; top:1px; right:0;}

.goods_table01 {border-top:1px solid #909090;margin:20px 0;}
.goods_table01 tr th {padding:20px 0; border-bottom:1px solid #c5c5c5;}
.goods_table01 tr td {border-bottom:1px solid #c5c5c5;}
.img_box {position:relative; border:1px solid #c5c5c5; width:80px;}
.goods_img img{width:100%;}

.goods_t_list { letter-spacing:-1px; text-align:left; }
.goods_t_list dt {font-size:15px; color:#000; padding:0 0 10px 0; margin:0;}
.goods_t_list dd {font-size:14px; padding:0; margin:0;}
.goods_t_list dd span{color:#666666;}
.goods_t_list dd b {color:#ec581e; font-size:15px;}
.delete_check{width:40%;position:absolute;top:-7%;right:-9%;cursor:pointer;}
</style>

";
//$script_time[sms_start] = time();
//$sms_cnt = $sms_design->getSMSAbleCount($admininfo);
//$script_time[sms_end] = time();
$Contents01 = "
<div class='goods_input_header'>
	<table border='0' cellpadding='0' cellspacing='0' width=100%'>
	<col width='50%' />
	<col width='50%' />
		<tr>
			<td><span>상품리스트</span></td>
		</tr>
	</table>
</div>
";

$where = "where p.id Is NOT NULL and p.id = r.pid and r.basic = 1 and p.product_type NOT IN ('".implode("','",$sns_product_type)."')  ";


if($admininfo[admin_level] == 9){
	if($admininfo[mem_type] == "MD"){
		$where .= " and p.admin in (".getMySellerList($_SESSION["admininfo"]["charger_ix"]).") ";
	}
}else{
	$where .= " and p.admin ='".$_SESSION["admininfo"]["company_id"]."' ";
}

if($search_text != ""){
	$where .= "and ( p.pname LIKE '%".trim($search_text)."%' OR p.pcode LIKE '%".trim($search_text)."%' OR p.id LIKE '%".trim($search_text)."%' ) ";
}

/*
$sql="select count(id) as total from ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r $where";
$db->query($sql);
$db->fetch();
$total = $db->dt[total];
*/

$sql="select p.* from ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r $where order by p.regdate desc limit 0,".$max."";
$db->query($sql);

$Contents01 .= "
<div class='s_r_box'>
	<h3>단어검색 <span><br /><br />상품명, 상품코드,상품시스템코드로 검색이 가능합니다.</span></h3>
	<div class='search_box'>
		<form name='search_form' method='get' onsubmit='return CheckFormValue(this);' >
			<input type='hidden' name='mode' value='search'>
			<div class='text_box'><input type='text' name='search_text' value='".$search_text."'/></div>
			<div class='search_btn'><input type='image' src='./images/serch_btn01.gif' alt='검색' height='35' /></div>
			<!--div class='qrcode'><img src='./images/qrcode_btn.gif' alt='' height='35' /></div-->
		</form>
	</div>
</div>
<div style='padding:0 15px;'>
	<table border='0' cellpadding='0' cellspacing='0' width=100%' class='goods_table01' id='goods_list_table'>
	<col width='100' />
	<col width='*' />
	<col width='50' />";

	if($db->total > 0){
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);

			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "s", $db->dt)) || $image_hosting_type=='ftp'){
				$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "s", $db->dt);
			}else{
				$img_str = "../image/no_img.gif";
			}

			$Contents01 .= "
				<tr>
					<th align='center'>
						<div class='img_box'>
							<div class='goods_img'><a href='mobile_alert.php?msg_type=no_product_update' rel='facebox'><img src='".$img_str."' alt='".$db->dt[pname]."' width='80' /></a></div>
							<!--img src='./images/delete_check.png' alt='' class='delete_check' /--><!-- <--삭제버튼 차후 개발-->
						</div>
					</th>
					<td align='left'>
						<dl class='goods_t_list'>
							<dt>".$db->dt[pname]."</dt>
							<dd><span>상품코드</span> ".($db->dt[pcode] ? $db->dt[pcode] : "-")."</dd>
							<dd><span>판매가</span> <b>".number_format($db->dt[sellprice])."</b>원</dd>
							<dd><span>도매가</span> <strike>".number_format($db->dt[wholesale_sellprice])."</strike>원</dd>
						</dl>
					</td>
					<td>";
						if($db->dt[disp]=='1'){
							$Contents01 .= "
							<img src='./images/goods_show.png' alt='노출함' width='50' class='' style='cursor:pointer;' onclick=\"dispUpdate($(this),'".$db->dt[id]."');\"/>";
						}else{
							$Contents01 .= "
							<img src='./images/goods_hide.png' alt='노출안함' width='50' class='' style='cursor:pointer;' onclick=\"dispUpdate($(this),'".$db->dt[id]."');\"/>";
						}
					$Contents01 .= "
					</td>
				</tr>";
		}
	}else{
		$Contents01 .= "
		<tr>
			<th align='center' colspan='3'>
				<dl class='goods_t_list' style='text-align:center;'>
					<dt>검색된 상품이 없습니다.</dt>
				</dl>
			</td>
		</tr>";
	}

$Contents01 .= "
	</table>
	<div style='text-align:center;font-weight:bold;font-size:16px;cursor:pointer;padding:10px 0;' onclick=\"lastPostFunc();\">
		더보기
	</div>
</div>
";



$Contents = $Contents01;




$P = new MobileLayOut();
$P->addScript = $Script;
//$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
$P->Navigation = "카메라선택";
$P->TitleBool = false;
$P->ServiceInfoBool = true;
echo $P->PrintLayOut();



$script_time[end] = time();
if($admininfo[charger_id] == "forbiz"){
	//print_r($script_time);
}

?>
