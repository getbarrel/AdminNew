<?
$script_time[start] = time();
include("../class/layout.class");
//include("../class/calender.class");
$script_time[start] = time();
//print_r($_SESSION);

$db = new Database; 

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

</Script>
<style type='text/css'>
.c_t_box {}
.c_t_box tr {cursor:pointer;}
.c_t_box tr th {height:100px; background:#f4f4f4; border-bottom:1px solid #e3e3e3;}
.c_t_box tr td {height:100px; border-bottom:1px solid #e3e3e3;}
.c_t_box tr td.c_t_text {text-align:left; }
.c_t_box tr td.c_t_text dl dd {text-align:left; padding:5px 0 0 0; margin:0;}
.c_t_box tr td.c_t_text02 {text-align:left; background:#f4f4f4;}
.c_t_box tr td.c_t_text02 dl dd {text-align:left;padding:5px 0 0 0; margin:0;}
</style>
";
//$script_time[sms_start] = time();
//$sms_cnt = $sms_design->getSMSAbleCount($admininfo);
//$script_time[sms_end] = time();
$Contents01 = "
<table width=100% cellpadding=0 cellspacing=0 border='0' align='left' style='margin-bottom:27px;'>
	<tr>
		<td width=100% valign=top >
		<table width='100%' cellpadding=0 cellspacing=0 border='0' class='c_t_box'>";

$Contents01 .= "
			<col width='100' />
			<col width='*' />
			<col width='40' />
		    <tr onclick=\"location.href='add_goods_image.php'\">
				<td align='center'>
					<img src='./images/m_c_img01.png' width='70' />
				</td>
				<td class='c_t_text'>
					<dl>
						<dt><b class='middle_title'>신상품등록</b></dt>
						<dd>모바일 촬영 등록 : 판매상품을 모바일로 촬영하여 바로 등록할수 있는기능</dd>
					</dl>
				</td>
				<td align='center'>
					<img src='./images/m_c_btn.png' width='12' />
				</td>
			</tr>  
			<!--tr onclick=\"location.href='goods_list.php'\">
				<th align='center'>
					<img src='./images/m_c_img02.png' width='70' />
				</th>
				<td class='c_t_text02'>
					<dl>
						<dt><b class='middle_title'>품목리스트</b></dt>
						<dd>재고관리 품목상품으로 등록된 리스트에 상품이미지를 간편히 추가 등록</dd>
					</dl>
				</th>
				<th align='center'>
					<img src='./images/m_c_btn.png' width='12' />
				</th>
			</tr>
			<tr onclick=\"location.href='add_store_image.php'\">
				<td align='center'>
					<img src='./images/m_c_img03.png' width='70' />
				</td>
				<td class='c_t_text'>
					<dl>
						<dt><b class='middle_title'>상점사진등록</b></dt>
						<dd>상점관리의 상점이미지를 변경할 수 있으며, 인테리어 변경시 간단하게 이미지 등록</dd>
					</dl>
				</td>
				<td align='center'>
					<img src='./images/m_c_btn.png' width='12' />
				</td>
			</tr>		
			<tr onclick=\"location.href='add_profile_image.php'\">
				<th align='center'>
					<img src='./images/m_c_img04.png' width='70' />
				</th>
				<td class='c_t_text02'>
					<dl>
						<dt><b class='middle_title'>관리자사진 등록</b></dt>
						<dd>로그인한 관리자 사진(이미지)를 등록할 수 있습니다</dd>
					</dl>
				</th>
				<th align='center'>
					<img src='./images/m_c_btn.png' width='12' />
				</th>
			</tr-->
		</table>
		</td>
	</tr>
</table>";



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
