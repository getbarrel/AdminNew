<?
include("../class/layout.class");
include_once("service.lib.php");
include_once("../inventory/inventory.lib.php");
//print_r($admin_config);
//echo exif_imagetype("/home/simpleline/www/data/simpleline/images/product/basic_1705.gif");

$db = new Database;
$db2 = new Database;
$adb = new Database;

$db->query("select idx from shop_icon where disp = 1 order by idx");
if($db->total){
	$icon_list = $db->fetchall();
	//print_r($icon_list);
}
$Script = "
<style>
div#drop_relation_product { width:100%;height:100%;overflow:auto;padding:1px;border:1px solid silver }
div#drop_relation_product.hover { border:5px dashed #aaa; background:#efefef; }

ul {
	LIST-STYLE-IMAGE: none; LIST-STYLE-TYPE: none;padding:0px;
}
li{
	list-style-tyle:none;
	margin:0px;
	padding:0px;
}
  #sortlist {
      list-style-type:none;
      margin:0;
      padding:0;
   }
   #sortlist li {
     font:13px Verdana;
     margin:0;
     padding:0px;
     cursor:move;
   }
  .ctr_1 {text-align:center};
</style>

<script type='text/javascript' src='/js/ui/jquery-ui-1.8.9.custom.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.core.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.widget.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.mouse.js'></script>
<script type='text/javascript' src='/admin/js/ms_productSearch.js'></script>

<script  id='dynamic'></script>
<Script Language='JavaScript'>
/*var bs_basic_air_shipping = '$bs_basic_air_shipping';
var bs_add_air_shipping = '$bs_add_air_shipping';
var duty_rate = '$bs_duty_rate';
var bs_supertax_rate = '$bs_supertax_rate';
var bs_clearance_fee = '$bs_clearance_fee';*/


function ChangeOptionName(pid, obj){
	var option_kind = obj[obj.selectedIndex].option_kind;


	document.getElementById('option_kind_value').innerHTML = getOptionKind(option_kind);
	if(option_kind == 'b'){
		document.getElementById('pricebyoption_title').innerHTML = '옵션별가격 *';
		var oobj = document.all.option_kind_view;
		for(i=0;i < oobj.length;i++){
			oobj[i].style.display = 'block';
		}

		var pobj = document.all.option_price_line;
		for(i=0;i < pobj.length;i++){
			pobj[i].style.display = 'block';
		}


	}else if(option_kind == 'p'){
		document.getElementById('pricebyoption_title').innerHTML = '옵션별 추가가격 *';
		var oobj = document.all.option_kind_view;
		for(i=0;i < oobj.length;i++){
			oobj[i].style.display = 'none';
		}

		var pobj = document.all.option_price_line;
		for(i=0;i < pobj.length;i++){
			pobj[i].style.display = 'block';
		}


	}else if(option_kind == 's'){
		document.getElementById('pricebyoption_title').innerHTML = '옵션별가격 *';
		var oobj = document.all.option_kind_view;
		for(i=0;i < oobj.length;i++){
			oobj[i].style.display = 'none';
		}

		var pobj = document.all.option_price_line;
		for(i=0;i < pobj.length;i++){
			pobj[i].style.display = 'none';
		}

	}

	window.forms['optionform'].option_kind.value = option_kind;
	window.frames['act'].location.href='option.act.php?act=view&pid='+pid+'&opn_ix='+obj.value;
	//document.getElementById('act').src='option.act.php?act=view&pid='+pid+'&opn_ix='+obj.value;//kbk
}

function getOptionKind(option_kind){

	if(option_kind == 'b'){
		return '가격재고 관리 옵션';
	}else if(option_kind == 'p'){
		return '가격추가옵션';
	}else if(option_kind == 's'){
		return '선택옵션';
	}else{
		return '';
	}

}

function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	//var depth = sel.depth; // 호환성 kbk
	var depth = sel.getAttribute('depth');
	//if(depth == 2){
	//document.write('category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	//}
	//alert(trigger);
	//dynamic.src = 'category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target; // 호환성 kbk

	if(sel.selectedIndex!=0) {
		// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
		//document.getElementById('act').src = 'category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		window.frames['act'].location.href = 'service_category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	}

}

function init()
{
";
	if ($id != ""){
		$db->query("SELECT * FROM service_product where id = '$id' ");

		if($db->total != 0)
		{
		$db->fetch(0);

		$hotcon_event_id = $db->dt[hotcon_event_id];
		$hotcon_pcode = $db->dt[hotcon_pcode];

		$pcode = $db->dt[pcode];
		$service_code = $pcode;


		$act = "update";
		$company = $db->dt[company];

		/*$buying_company = $db->dt[buying_company];
		$paper_pname = $db->dt[paper_pname];*/


		$state = $db->dt[state];
		//$brand = $db->dt[brand];
		$basicinfo = $db->dt[basicinfo];
		
		$parent_service_code=$db->dt[parent_service_code];
		$service_code=$db->dt[service_code];
		$shotinfo = $db->dt[shotinfo];
		//$hardware = $db->dt[hardware];
		//$software = $db->dt[software];
		//$system = $db->dt["system"];
		$etc = $db->dt["etc"];
		$pname = str_replace("'","&#39;",trim($db->dt[pname]));
		$sell_ing_cnt = $db->dt[sell_ing_cnt];
		$stock = $db->dt[stock];
		$safestock = $db->dt[safestock];

		$movie = $db->dt[movie];
		$search_keyword = $db->dt[search_keyword];
		$disp = $db->dt[disp];
		$surtax_yorn = $db->dt[surtax_yorn];
		//$supply_company = $db->dt[supply_company];
		$inventory_info = $db->dt[inventory_info];
		//echo $inventory_info;

		//$delivery_method = $db->dt[delivery_method];
		$product_type = $db->dt[product_type];
		//$delivery_company = $db->dt[delivery_company];
		$reserve = $db->dt[reserve];
		$reserve_yn = $db->dt[reserve_yn];
		$sns_btn_yn = $db->dt[sns_btn_yn];
		$sns_btn = $db->dt[sns_btn];

		$stock_use_yn = $db->dt[stock_use_yn];
		//$buyingservice_coprice = $db->dt[buyingservice_coprice];
		$coprice = $db->dt[coprice];
		$sellprice = $db->dt[sellprice];
		$listprice = $db->dt[listprice];
		$bimg_text = $db->dt[bimg];
		$admin = $db->dt[admin];


		$sell_priod_sdate = $db->dt[sell_priod_sdate];
		$sell_priod_edate = $db->dt[sell_priod_edate];
		$allow_order_type = $db->dt[allow_order_type];
		$allow_order_cnt_byonesell = $db->dt[allow_order_cnt_byonesell];
		$allow_order_cnt_byoneperson = $db->dt[allow_order_cnt_byoneperson];
		//$orgin = $db->dt[orgin];
		$make_date = $db->dt[make_date];
		$expiry_date = $db->dt[expiry_date];



		$car_defailt_validation = "false";
		$realestate_defailt_validation = "false";
		$hotel_default_validation = "false";
		$tour_default_validation="false";


		if ($FromYY == "" || $FromYY == "0000"){

			$after10day = mktime(date('H'), date('i'), 0, date("m")  , date("d")+20, date("Y"));

		//	$sDate = date("Y/m/d");
			$sDate = date("Y/m/d/H/i" );
			$eDate = date("Y/m/d/H/i",$after10day);
			$vintage = date("Y/m" );

			$startDate = date("YmdHi");
			$endDate = date("YmdHi",$after10day);
		}else{
			$sDate = $FromYY."/".$FromMM."/".$FromDD."/".$FromHH."/".$FromII;
			$eDate = $ToYY."/".$ToMM."/".$ToDD."/".$ToHH."/".$ToII;
			$startDate = $FromYY.$FromMM.$FromDD.$FromHH.$FromII;
			$endDate = $ToYY.$ToMM.$ToDD.$ToHH.$ToII;
		}

			$Script=$Script. "

			";


			$icons = explode(";",$db->dt["icons"]);
			for($i=0;$i<count($icons);$i++){
				$icons_checked[$icons[$i]] = "1";
			}
		}
	}else{
		$disp = "1";
		$act = "insert";
		$vintage = date("Y/m" );
		$admin = $admininfo["company_id"];

		$car_defailt_validation = "false";
		$realestate_defailt_validation = "false";
		$hotel_default_validation = "false";

		$surtax_yorn = "N";
		if ($FromYY == ""){

			$after10day = mktime(date('H'), date('i'), 0, date("m")  , date("d")+20, date("Y"));

		//	$sDate = date("Y/m/d");
			$sDate = date("Y/m/d/H/i" );
			$eDate = date("Y/m/d/H/i",$after10day);

			$startDate = date("YmdHi");
			$endDate = date("YmdHi",$after10day);
		}else{
			$sDate = $FromYY."/".$FromMM."/".$FromDD."/".$FromHH."/".$FromII;
			$eDate = $ToYY."/".$ToMM."/".$ToDD."/".$ToHH."/".$ToII;
			$startDate = $FromYY.$FromMM.$FromDD.$FromHH.$FromII;
			$endDate = $ToYY.$ToMM.$ToDD.$ToHH.$ToII;
		}
		if($admininfo[admin_level] == 9 && $admininfo[mall_type] == "O" && $admininfo[mem_type] != "MD"){
		$Script=$Script. "
		/*
			frm = document.product_input;
			document.getElementById('FromYY').disabled = true;
			document.getElementById('FromMM').disabled = true;
			document.getElementById('FromDD').disabled = true;
			document.getElementById('FromHH').disabled = true;
			document.getElementById('FromII').disabled = true;
			document.getElementById('ToYY').disabled = true;
			document.getElementById('ToMM').disabled = true;
			document.getElementById('ToDD').disabled = true;
			document.getElementById('ToHH').disabled = true;
			document.getElementById('ToII').disabled = true;
			document.getElementById('start_price').disabled = true;
		*/
		";
		}
	}
$Script .="

	Content_Input();

	Init(document.product_input);";
if($admininfo[admin_level] == 9 && $admininfo[mall_type] == "O"){
$Script .="
	onLoad('$sDate','$eDate');";
}
	if($bs_goods_url){
//	$Script .="document.getElementById('bs_site_frame').src= '".$bs_goods_url."';";
	}
	$Script .="
}
function Content_Input(){
	document.product_input.content.value = document.product_input.basicinfo.value;
	//alert(document.product_input.content.value);
}

function onDropAction(mode, pid,rp_pid)
{
	//outTip(img3);
	//alert(1);
	//parent.document.frames['act'].location.href='./relation.category.act.php?mode='+mode+'&pid='+pid+'&rp_pid='+rp_pid;
	//parent.document.getElementById('act').src='./relation.category.act.php?mode='+mode+'&pid='+pid+'&rp_pid='+rp_pid;
	parent.window.frames['act'].location.href='./relation.category.act.php?mode='+mode+'&pid='+pid+'&rp_pid='+rp_pid;

}
//var cate = new Array();";
if($admininfo[admin_level] == 9){
$Script=$Script. "
function init_date(FromDate,ToDate) {
	var frm = document.product_input;


	/*for(i=0; i<frm.FromYY.length; i++) {
		if(frm.FromYY.options[i].value == FromDate.substring(0,4))
			frm.FromYY.options[i].selected=true
	}
	for(i=0; i<frm.FromMM.length; i++) {
		if(frm.FromMM.options[i].value == FromDate.substring(5,7))
			frm.FromMM.options[i].selected=true
	}
	for(i=0; i<frm.FromDD.length; i++) {
		if(frm.FromDD.options[i].value == FromDate.substring(8,10))
			frm.FromDD.options[i].selected=true
	}
	for(i=0; i<frm.FromHH.length; i++) {
		if(frm.FromHH.options[i].value == FromDate.substring(11,13))
			frm.FromHH.options[i].selected=true
	}
	for(i=0; i<frm.FromII.length; i++) {
		if(frm.FromII.options[i].value == FromDate.substring(14,16))
			frm.FromII.options[i].selected=true
	}


	for(i=0; i<frm.ToYY.length; i++) {
		if(frm.ToYY.options[i].value == ToDate.substring(0,4))
			frm.ToYY.options[i].selected=true
	}
	for(i=0; i<frm.ToMM.length; i++) {
		if(frm.ToMM.options[i].value == ToDate.substring(5,7))
			frm.ToMM.options[i].selected=true
	}
	for(i=0; i<frm.ToDD.length; i++) {
		if(frm.ToDD.options[i].value == ToDate.substring(8,10))
			frm.ToDD.options[i].selected=true
	}
	for(i=0; i<frm.ToHH.length; i++) {
		if(frm.ToHH.options[i].value == ToDate.substring(11,13))
			frm.ToHH.options[i].selected=true
	}
	for(i=0; i<frm.ToII.length; i++) {
		if(frm.ToII.options[i].value == ToDate.substring(14,16))
			frm.ToII.options[i].selected=true
	}
	for(i=0; i<frm.vintage_year.length; i++) {
		if(frm.vintage_year.options[i].value == ToDate.substring(0,4))
			frm.vintage_year.options[i].selected=true
	}
	for(i=0; i<frm.vintage_month.length; i++) {
		if(frm.vintage_month.options[i].value == ToDate.substring(5,7))
			frm.vintage_month.options[i].selected=true
	}*/


}

function onLoad(FromDate, ToDate) {
	var frm = document.product_input;";
if($admininfo[admin_level] == 9 && $admininfo[mall_type] == "O" ){
$Script .= "
	//LoadValuesAuction(frm.FromYY, frm.FromMM, frm.FromDD,frm.FromHH,frm.FromII, FromDate);
	//LoadValuesAuction(frm.ToYY, frm.ToMM, frm.ToDD,frm.ToHH,frm.ToII, ToDate);";

$Script .= "
	//LoadValues(frm.vintage_year, frm.vintage_month, null, '".str_replace("-","/",$vintage)."');
	init_date(FromDate,ToDate);";
}
$Script .= "
}";
}
$Script=$Script. "

function categoryadd()
{
	var ret;
	var str = new Array();
	var obj = document.product_input.cid;
	for (i=0;i<obj.length;i++){
		if (obj[i].value){
			str[str.length] = obj[i][obj[i].selectedIndex].text;
			ret = obj[i].value;
		}
	}
	if (!ret){
		alert(language_data['goods_input.php']['A'][language]);//'카테고리를 선택해주세요'
		return;
	}
	//var cate = document.all._category;
	var cate=document.getElementsByName('category[]'); // 호환성 kbk
	//alert(cate.length);

	//if(is_array([cate])){
		//alert(cate.length);
		for(i=0;i < cate.length;i++){
			//alert(ret +'=='+ cate[i].value);
			//alert(cate[i].value);
			if(ret == cate[i].value){
				alert(language_data['goods_input.php']['B'][language]);
				//'이미등록된 카테고리 입니다.'
				return;
			}
		}
	//}

	//cate.unshift(ret);
	var obj = document.getElementById('objCategory');
	//oTr = obj.insertRow();
	oTr = obj.insertRow(-1); // 크롬과 파폭에서는 td의 생성이 반대로 됨 -1 인자를 넣어주면 순서대로 형성됨 2011-04-07 kbk
	oTr.id = 'num_tr';
	oTr.height = '30px';
	//oTr.className = 'dot_xx';
	if(window.addEventListener) oTr.setAttribute('class','');
	else oTr.className = '';
	oTd = oTr.insertCell(-1);
	//oTd.className = '';
	if(window.addEventListener) oTd.setAttribute('class','');
	else oTd.className = '';
	oTd.innerHTML = \"<input type=text name=category[] id='_category' value='\" + ret + \"' style='display:none'>\";
	oTd = oTr.insertCell(-1);
	//oTd.className = '';
	if(window.addEventListener) oTd.setAttribute('class','');
	else oTd.className = '';
	if(oTr.rowIndex == 0){
		oTd.innerHTML = \"<input type=radio name=basic value='\"+ ret + \"' checked>\";
	}else{
		oTd.innerHTML = \"<input type=radio name=basic value='\"+ ret + \"'>\";
	}
	oTd = oTr.insertCell(-1);
	//oTd.id = \"currPosition\";
	if(window.addEventListener) oTd.setAttribute('id','currPosition');
	else oTd.id = 'currPosition';
	//oTd.className = '';
	if(window.addEventListener) oTd.setAttribute('class','');
	else oTd.className = '';
	oTd.innerHTML = str.join(\" > \");
	oTd = oTr.insertCell(-1);
	//oTd.className = '';
	if(window.addEventListener) oTd.setAttribute('class','');
	else oTd.className = '';
	oTd.innerHTML = \" <a href='javascript:void(0)' onClick='category_del(this.parentNode.parentNode)'><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>\";


}

function category_del(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objCategory');
	obj.deleteRow(idx);
	var cObj=\$('input[name=basic]');
	var cObj_num=0;
	if(cObj.length == null){
		//cObj[0].checked = true; // 0이 나오지 null이 나오지 않음 kbk
	}else{
		for(var i=0;i<cObj.length;i++){
			if(cObj[i].checked){
				cObj_num++;
			}
		}
		if(cObj_num==0) {
			cObj[0].checked = true;
		}
	}
	//cate.splice(idx,1);
}
</Script>";

$Contents = "
	<table cellpadding=0 width=100%>
		<tr>
		    <td align='left' colspan=4 > ".GetTitleNavigation("서비스상품등록", "서비스관리 > 서비스상품등록")."</td>
		</tr>";
if($admininfo[charger_id] == "forbiz" && false){
$Contents .= "
		<tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    	<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' class='on' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='goods_input.php'\">일반 상품 등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='goods_input_quick.php'\">빠른 상품 등록</td>
								<th class='box_03'></th>
							</tr>
							</table>

						</td>
						<td class='btn'>

						</td>
					</tr>
					</table>
				</div>
	    </td>
	</tr>";
}
$Contents .= "
	</table>";

$Contents .= "
			<!--form name='product_input' action='product_input.act.php' method='post' enctype='multipart/form-data'-->
			<form name=product_input action='../service/service_goods_input.act.php' method='post' enctype='multipart/form-data' onsubmit='return SubmitX(this);' target= 'act'><!--target= 'act'-->
			<input type='hidden' name=act value='insert'>
			<input type='hidden' name=admin value='".$admin."'>
			<input type='hidden' name=id value='".$id."'>
			<input type='hidden' name=bpid value='".$id."'>
			<input type='hidden' name=mmode value='".$mmode."'>
			<input type='hidden' name=mode value='".$mode."'>
			<table width=100%>
			<tr height=30 align=left>
				<td width=500>";
if($id){
/*$Contents .= "
				<a href=\"/shop/goods_view.php?id=".$id."\" target=_blank><img src='../images/".$admininfo["language"]."/btn_preview.gif' border=0 align=absmiddle style='cursor:pointer'></a>";*/
}
$Contents .= "  <!--a href=\"JavaScript:PoPWindow('/shop/goods_view.php?id=".$id."',980,800,'comparewindow');\">보기</a--></td>";
if ($id == "" || $mode == "copy"){
	$Contents .= "<td align=right>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
		$Contents .= "<img src='../images/".$admininfo["language"]."/btn_save_tmp.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'tmp_insert');\"> ";
		$Contents .= "<img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'insert');\">";
	}
	$Contents .= "</td>";
}else{
	$Contents .= "<td align=right>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
		$Contents .= "<img src='../images/".$admininfo["language"]."/btn_save_tmp.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'tmp_update');\"> ";
		$Contents .= "<img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'update')\"> ";
	}
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
		$Contents .= "<img src='../images/".$admininfo["language"]."/b_del.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'delete')\">";
	}
	$Contents .= "</td>";
}
$Contents = $Contents."
			</tr>
			</table>";
if($admininfo[mall_type] == "O"){
$Contents .= "			<table cellspacing=0 cellpadding=0 border=0 width='100%'>
			<tr bgcolor='#cccccc' height='36'>
					<td bgcolor=\"#efefef\" width=120 style='padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;top:-1px;'> <b>상품구분</b></td>
					<td colspan=3 bgcolor=\"#efefef\" align='left'>
						<table border=0 cellpadding=0 cellspacing=0 width=550>
							<tr>
								<td>
									<input type=radio name=product_type id='product_type_0' value='0' ".($product_type == "0" || $product_type == "" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('GoodsInfo');\"> <label for='product_type_0' >서비스상품 </label>";
		if($admininfo[charger_id] == "forbiz" ){
			$Contents .= "
									";
		}
			$Contents .= "
									
								</td>

							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height='3'></td>
				</tr>
			</table><br>";

/*
$goodsHelp_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >자사가 직접 소싱 하는 상품을 등록 관리 합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >경매 상품과 , 해외구매 대행 상품은 별도로 등록 되어야 합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 상품 타입에 따라 쇼핑몰 에서 구분되어 상품이 표시됩니다.</td></tr>

</table>
";*/

$goodsHelp_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');
$GoodsHelp = HelpBox("서비스 상품관리", $goodsHelp_text);



$buyingServiceHelp_text = "
<!--table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >구매대행 상품 등록하기 버튼을 클릭하셔서 구매대행 상품정보를 자동으로 가져 오실수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 구매대행 상품은 자동으로 원천사이트의 정보변경을 체크하여 관리 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >변경된 정보는 자동으 시스템에 반영됩니다.</td></tr>

</table-->  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'S')."
";
$buyingServiceHelp_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');
$buyingServiceHelp = HelpBox("구매대행 상품관리", $buyingServiceHelp_text);


$HotConHelp_text = "
<!--table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >하트콘은 MMS 를 통해 상품쿠폰을 수령할수 있는 상품입니다..</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >할인된 가격을 통해서 상품을 구매할 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >하트콘 상품을 사용하시기 위해서는 별도의 신청이 필요합니다. 문의전화 :  02-2058-2214 </td></tr>
</table-->
";
$HotConHelp_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');
$HotConHelp = HelpBox("HotCon 상품관리", $HotConHelp_text);


$Contents .= "
				<div id='GoodsInfo' ".($product_type == "0" || $product_type == "" ? "style='display:block;padding-bottom:20px;'":"style='display:none;'").">
				<table width=100% border=0>
					<tr>
						<td>".$GoodsHelp."</td>";

$Contents .= "
					</tr>
				</table>
				</div>";

}else if($admininfo[mall_type] == "B" || $admininfo[mall_type] == "F"){
	$Contents .= "			<table cellspacing=0 cellpadding=0 border=0 width='100%'>
			<tr bgcolor='#cccccc' height='36'>
					<td bgcolor=\"#efefef\" width=120 style='padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;top:-1px;'> <b>상품구분</b></td>
					<td colspan=3 bgcolor=\"#efefef\" align='left'>
						<table border=0 cellpadding=0 cellspacing=0 width=550>
							<tr>
								<td>
									<input type=radio name=product_type id='product_type_0' value='0' ".($product_type == "0" || $product_type == "" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('GoodsInfo');\"> <label for='product_type_0' >일반상품 </label>";
	
			$Contents .= "
									<!--input type=radio name=product_type id='product_type_2'  value='2' ".($product_type == "2" ? "checked":"")." onclick=\"dateSelect('2');ShowGoodsTypeInfo('AuctionInfo');\"> <label for='product_type_2' >최저가경매</label>
									<input type=radio name=product_type id='product_type_3'  value='3' ".($product_type == "3" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('hotcon');\"> <label for='product_type_3' >하트콘 상품</label-->
									
								</td>

							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height='3'></td>
				</tr>
			</table><br>";
}else{
	$Contents .= "<input type=hidden name=product_type id='product_type_0' value='".$product_type."' > ";
}

		$Contents .= "	<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;top:-4px;'><b> 카테고리등록 </b><span class=small><!--하단에 카테고리를 선택하신후 카테고리 등록하기 버튼을 클릭하세요.(다중 카테고리 등록지원)-->
		".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')." </span>
		</td><td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>")."</td></tr></table>";



			$Contents .= "<table cellpadding=0 cellspacing=0  border=0 width='100%' class='input_table_box'>
				<col width=15%>
				<col width=90%>
				<tr>
					<td class='input_box_title'  nowrap> <b>카테고리 *</b> </td>
					<td class='input_box_item'>
					<input type='hidden' name=selected_cid value='".$cid."'>
					<input type='hidden' name=selected_depth value=''>
					<input type='hidden' id='_category' value=''>
					<input type='hidden' id='_category' value=''>
					<input type='hidden' id='basic' value=''>
					<!--input type='hidden' name=cid_1 value=''>
					<input type='hidden' name=cid_2 value=''>
					<input type='hidden' name=cid_3 value=''-->
						<table width=100% border=0 cellpadding=0 cellspacing=0>
							<col width='25%'>
							<col width='25%'>
							<col width='25%'>
							<col width='25%'>
							<tr>
								<td style='padding:5px 0px 5px 2px;'>".getServiceCategoryMultipleSelect("--1차분류--", "cid0", "cid","onChange=\"loadCategory(this,'cid1',2)\" title='1차분류' ", 0, $cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getServiceCategoryMultipleSelect("--2차분류--", "cid1",  "cid","onChange=\"loadCategory(this,'cid2',2)\" title='2차분류'", 1, $cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getServiceCategoryMultipleSelect("--3차분류--", "cid2", "cid", "onChange=\"loadCategory(this,'cid3',2)\" title='3차분류'", 2, $cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getServiceCategoryMultipleSelect("--4차분류--", "cid3", "cid", "onChange=\"loadCategory(this,'cid_1',2)\" title='4차분류'", 3, $cid)."</td>
								<td style='padding:5px 4px 5px 6px;'><img src='../images/".$admininfo["language"]."/category_add.gif' align=absmiddle border=0 onclick=\"categoryadd()\" style='cursor:pointer;'></td>
							</tr>
						</table>";

				$Contents .= "	</td>
				</tr>
			</table><br>
			<table border=0 cellpadding=0 cellspacing=0 width='100%' style='padding:5px 10px 5px 10px;border:1px solid silver' >
				<col width=100%>
				<tr>
					<td>";
						if($id != ""){
							$Contents .= PrintRelation($id);
						}else{
						$Contents .= "<table width=100% cellpadding=0 cellspacing=0 id=objCategory >
										<col width=5>
										<col width=50>
										<col width=*>
										<col width=100>
						</table>";
						}
				$Contents .= "	</td>
				</tr>
				<tr><td class='small' height='25' style='padding-left:15px;'> <span class='small'> <!--* 첫번째 선택된 카테고리가 기본카테고리로 지정되며 라디오 버튼 클릭으로 기본카테고리를 변경 하실 수 있습니다>--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')." </span></td></tr>
			</table><br>
			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;top:-4px;'><b> 기본정보 : </b><span class=small><!--굵은 글씨로 되어 있는 항목이 필수 정보입니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')." </span> </td><td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>")."</td></tr></table>
			<table cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
				<col width=15%>
				<col width=35%>
				<col width=15%>
				<col width=35%>
				<tr>
					<td class='input_box_title' nowrap> <b>제품명 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item' colspan=3><input type=text class='textbox' name=pname size=28 style='width:90%' value='$pname' validation=true title='제품명'></td>
					<!--td class='input_box_title'> 장기(도매)명 </td>
					<td class='input_box_item'>
					<input type=text class='textbox' name=paper_pname size=28 style='width:90%' value='$paper_pname'  title='장기명'>
					</td-->
				</tr>
				";
				if($admininfo[admin_level] == 9){
					if($admininfo[mall_type] == "B" || $admininfo[mall_type] == "O"){
					$Contents .= "<tr>
						<td class='input_box_title'> <b>진열 <img src='".$required3_path."'></b></td>
						<td class='input_box_item'>".displayProduct($disp)."</td>
						<!--td class='input_box_title' nowrap> <b>입점업체등록 <img src='".$required3_path."'></b></td>
						<td class='input_box_item'>
						".companyAuthList($admin , "validation=true title='입점업체' ")."
						</td-->
						<td class='input_box_title'> <b>판매상태 <img src='".$required3_path."'></b></td>
						<td class='input_box_item'>".SellState($state)."</td>
					</tr>";

					}else{
					$Contents .= "<tr>
						<td class='input_box_title'> <b>진열 <img src='".$required3_path."'></b></td>
						<td class='input_box_item' colspan=3>".displayProduct($disp)."</td>

					</tr>";
					}
				}
				$Contents .= "
				";

if($goods_input_type != "inventory"){
if($admininfo[admin_level] == 9){
$Contents .=	"
				<tr>
					<td class='input_box_title'> 아이콘노출<br><br>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:PoPWindow('../design/product_icon.php?mmode=pop',960,700,'brand')\"'><img src='../images/".$admininfo["language"]."/btn_pop_icon.gif' align=absmiddle border=0></a> </td>
					<td class='input_box_item' colspan=3>
					<table width='100%'>
						<tr>
						<td>
						<table border=0>
						<tr>
							";
							if(count($icon_list) >0 ){
								for($i=0;$i<count($icon_list);$i++){
									$Contents .=	"<td><input type=\"checkbox\" name='icon_check[]' class=nonborder id=icon_check value=".$icon_list[$i][idx]." ".($icons_checked[$icon_list[$i][idx]] == "1" ? "checked":"")."></td><td><img src='".$admin_config[mall_data_root]."/images/icon/".$icon_list[$i][idx].".gif' align='absmiddle' style='vertical-align:middle'></td>";
									if($i == 8) $Contents .=	"</tr></table><table border=0><tr>";
								}
							}

							$Contents .=	"
						</tr>
						</table>
						</td>
						</tr>
					</table>
					</td>
				</tr>";
}
$sns_btn_arr = unserialize($sns_btn);

$Contents .=	"
				<tr'>
					<td class='input_box_title'> SNS공유버튼 노출 </td>
					<td class='input_box_item' style='line-height:150%'>
					<input type=\"checkbox\" name='sns_btn_yn' class=nonborder id=sns_btn_yn value='Y' ".($sns_btn_yn == "Y" ? "checked":"")."> <label for='sns_btn_yn'>사용함</label> (
					<input type=\"checkbox\" name='sns_btn[btn_use1]' class=nonborder id=btn_use1 value=facebook ".($sns_btn_arr[btn_use1] == "facebook" ? "checked":"")."> <label for='btn_use1'>페이스북</label>
					<input type=\"checkbox\" name='sns_btn[btn_use2]' class=nonborder id=btn_use2 value=twitter ".($sns_btn_arr[btn_use2] == "twitter" ? "checked":"")."> <label for='btn_use2'>트위터</label>

					<input type=\"checkbox\" name='sns_btn[btn_use3]' class=nonborder id=btn_use3 value=me2day ".($sns_btn_arr[btn_use3] == "me2day" ? "checked":"")."> <label for='btn_use3'>미투데이</label>
					<input type=\"checkbox\" name='sns_btn[btn_use4]' class=nonborder id=btn_use4 value=yozm ".($sns_btn_arr[btn_use4] == "yozm" ? "checked":"")."> <label for='btn_use4'>요즘</label>

					)
					</td>
					<td class='input_box_title'>서비스코드</td>
					<td class='input_box_item'>
					 ".getServiceInfoSelect('parent_service_code', '1차 서비스분류',$parent_service_code, $parent_service_code, 1, " onChange=\"loadService(this,'service_code')\" ")."
					 ".getServiceInfoSelect('service_code', '2차 서비스분류',$parent_service_code, $service_code, 2, "validation=true title='서비스코드'")."
					</td>
				</tr>";
if($admininfo[admin_level] == 9){
$Contents .=	"
				<tr>
					<td class='input_box_title'> 동영상 URL(NEWS) </td>
					<td class='input_box_item' colspan=3 style='line-height:150%'>
					<input type=text class='textbox' name='movie' style='width:90%' value='".$movie."'></td>
				</tr>";
}
$Contents .=	"
				<tr>
					<td class='input_box_title'> 검색키워드 </td>
					<td class='input_box_item' style='padding:5px 5px;line-height:150%' colspan=3 style=''>
					<input type=text class='textbox' name='search_keyword' style='width:90%' value='$search_keyword'><br>
					※<span class=small > <!--검색어를 등록하시면 검색이 검색어가 같이 포함되어 노출되게 됩니다-->  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'I')." </span></td>
				</tr>
				<!--tr height=1><td colspan=4 class='dot-x'></td></tr-->";

if ($admininfo[mall_type] == "O"){
$vdate = date("Ymd", time());
$today = date("Ymd", time());
$vyesterday = date("Ymd", time()+84600);
$voneweeklater = date("Ymd", time()+84600*7);
$vtwoweeklater = date("Ymd", time()+84600*14);
$vfourweeklater = date("Ymd", time()+84600*28);
$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24);
$voneweeklater = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*7);
$v15later = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*15);
$vfourweeklater = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*28);
$vonemonthlater = date("Ymd",mktime(0,0,0,substr($vdate,4,2)+1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthlater = date("Ymd",mktime(0,0,0,substr($vdate,4,2)+2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthlater = date("Ymd",mktime(0,0,0,substr($vdate,4,2)+3,substr($vdate,6,2)+1,substr($vdate,0,4)));


$Contents .=	"

				<tr>
					<td class='input_box_title'> 판매기간 </td>
					<td class='input_box_item' colspan=3 style='padding:5px 5px;line-height:150%'>
					<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
						<col width=70>
						<col width=20>
						<col width=70>
						<col width=*>
						<tr>
							<TD nowrap>
							<input type='text' name='sell_priod_sdate' class='textbox' value='".$sell_priod_sdate."' style='height:20px;width:70px;text-align:center;' id='start_datepicker'>
							</TD>
							<TD align=center> ~ </TD>
							<TD nowrap>
							<input type='text' name='sell_priod_edate' class='textbox' value='".$sell_priod_edate."' style='height:20px;width:70px;text-align:center;' id='end_datepicker'>
							</TD>
							<TD style='padding:0px 10px'>
								<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
								<a href=\"javascript:select_date('$today','$voneweeklater',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
								<a href=\"javascript:select_date('$today','$v15later',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
								<a href=\"javascript:select_date('$today','$vonemonthlater',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
								<a href=\"javascript:select_date('$today','$v2monthlater',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
								<a href=\"javascript:select_date('$today','$v3monthlater',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
							</TD>
						</tr>
					</table>
					- 판매시작 날짜가 현재일 이전이면 상품이 등록되지 않습니다.<br>
					</td>
				</tr>";
if($admininfo[admin_level] == 9){
$Contents .=	"
				<tr>
					<td class='input_box_title'> 최대구매 허용수량 </td>
					<td class='input_box_item' colspan=3 style='padding:3px;' style='line-height:150%'>
					<input type=radio name='allow_order_type' value='1' ".($allow_order_type == "1" ? "checked":"")."> 제한없음 : 최대구매수량 제한없음 <br>
					<input type=radio name='allow_order_type' value='2' ".($allow_order_type == "2" ? "checked":"")."> 1회 제한 : <input type=text class='textbox integer' name='allow_order_cnt_byonesell' style='width:60px;margin-left:20px;' value='$allow_order_cnt_byonesell'> 1회 구매시 최대로 구매할 수 있는 상품수량 <br>
					<input type=radio name='allow_order_type' value='3' ".($allow_order_type == "3" ? "checked":"")."> 1인 제한 : <input type=text class='textbox integer' name='allow_order_cnt_byoneperson' style='width:60px;margin-top:3px;margin-left:20px;' value='$allow_order_cnt_byoneperson'> 한 구매자가 최대로 구매할 수 있는 상품수량 <br>

					</td>
				</tr>";
}
$Contents .=	"
				
				<tr>
					<td class='input_box_title'> 제조일자 </td>
					<td class='input_box_item' style='padding:2px;'>
						<table>
						<tr>
							<td><input type='text' name='make_date' class='textbox' value='".$make_date."' style='height:20px;width:100px;text-align:center;' id='make_date_datepicker'></td>
							<td class=small>농축수산물/식품/화장품/분유/이유식일 경우<br> 제조일자나 유효일 중 하나를 꼭 입력해 주세요.</td>
						</tr>
						</table>

					</td>
					<td class='input_box_title'> 유효일</td>
					<td class='input_box_item'><!--input type=text class='textbox' name=company size=28 style='width:100%'-->
						<input type='text' name='expiry_date' class='textbox' value='".$expiry_date."' style='height:20px;width:100px;text-align:center;' id='expiry_date_datepicker'>
					</td>
				</tr>";
}
$Contents .=	"
			</table><br>";
}// 재고관리가 아닐때 까지...
$Contents .= "
			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding:10px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;top:-1px;'><b> 수수료정보</b></a></div>")."</td></tr></table>
			";
$Contents = $Contents."
			<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' ".($admininfo[admin_level] == 8 ? "style='display:none;'":"").">
				<col width=15%>
				<col width=85%>
				<tr>
					<td class='input_box_title' nowrap> <b>개별수수료 사용</b> </td>
					<td class='input_box_item'><input type='radio' name='one_commission' value='N' ".($one_commission == 'N' || $one_commission == "" ? "checked":"")." onclick=\"commissionChange(this.form)\">사용안함<input type='radio' name='one_commission' value='Y' ".($one_commission == 'Y' ? "checked":"")." onclick=\"commissionChange(this.form)\">사용</td>
				</tr>
				<tr>
					<td class='input_box_title' nowrap> 수수료  </td>
					<td class='input_box_item' id='DP' style='display:block padding-top:5px;' nowrap> <input style='width:40px;TEXT-ALIGN:right' type='text' class='textbox numeric' name='commission' value='".$commission."' validation='false' ".($one_commission == 'N' || $one_commission == "" ? "disabled":"").">  % 단위로 입력하시기 바랍니다. * 개별수수료 사용 선택시에만 입력하실 수 있습니다. ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'K')."</td>
				</tr>
			</table><br>";
$Contents .= "
			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding:10px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;top:-1px;'><b> 가격정보</b> <a onclick=\"alert(language_data['goods_input.php']['I'][language]);\"><img src='../image/movie_manual.gif' align=absmiddle></a></div>")."</td></tr></table>
			";
			//'동영상 메뉴얼 준비중입니다'
if ($admininfo[admin_level] != 9){
	if($act == "update"){
		$dispString = "style='display:none;'";
		$readonlyString = " readonly";
		$colorString = ";background-color:#efefef;color:gray";
		$message = "onclick=\"alert(language_data['goods_input.php']['C'][language]);\"";
		//'가격 정보를 수정하시고자 할대는 MD와 상의해 주세요'
	}else{
		//$dispString = "style='display:none;'";
		//$readonlyString = " readonly";
		//$colorString = ";background-color:#efefef;color:gray";
		//$message = "onclick=\"alert('입점업체는 공급가격만 입력하실수 있습니다');\"";
	}
}

$Contents = $Contents."
			<table cellpadding=0 cellspacing=1 bgcolor=#ffffff width='100%' >
				<tr>
				    <td align='left' colspan=4 style='padding-bottom:0px;'>
				    <div class='tab'>
							<table class='s_org_tab'>
							<tr>
								<td class='tab'>
									<table id='p_tab_01'  class='on'>
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"showPriceTabContents('price_info','p_tab_01');\">가격정보입력</td>
										<th class='box_03'></th>
									</tr>
									</table>
									<table id='p_tab_02'  ".($admininfo[admin_level] == 9 ? "style='display:none;":"style='display:none;' ").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"showPriceTabContents('detail_price_info','p_tab_02');\">수수료계산기</td>
										<th class='box_03'></th>
									</tr>
									</table>

								</td>
								<td class='btn'>

								</td>
							</tr>
							</table>
					</div>
					<div class='mallstory t_no'>
						<!-- my_movie start -->

						<div class='my_box'>
							<div class='doong' id='price_info_box' style='display:block;vertical-align:top;' >
								<table cellpadding=3 cellspacing=0 width=100%>
									<tr id=\"buyingServiceClearanceType\" style='".($product_type == "1" ? "":"display:none;")."'>
										<td>
											<table width=100% cellpadding=0 cellspacing=0>
												<col width=90%>
												<col width=10%>
												<tr>
													<td align=left>
														<table cellpadding=1 cellspacing=1 bgcolor=#c0c0c0 width=100%>
															<col width=10%>
															<col width=20%>
															<col width=10%>
															<col width=60%>
															<tr bgcolor='#ffffff' height=30 align=center>
																<td bgcolor='#efefef'>통관타입</td>
																<td style='padding:1px 3px 1px 1px' nowrap>
																<input type='radio' name='clearance_type' id='clearance_type_1' onclick='caculateBuyingServicePrice(this);' value='1' ".($clearance_type == "1" ? "checked":"")."><label for='clearance_type_1'>목록통관</label> 
																<input type='radio' name='clearance_type' id='clearance_type_0' onclick='caculateBuyingServicePrice(this);' value='0' ".($clearance_type == "0" ? "checked":"")."> <label for='clearance_type_0'>일반통관</label>
																<input type='radio' name='clearance_type' id='clearance_type_9' onclick='caculateBuyingServicePrice(this);' value='9' ".($clearance_type == "9" ? "checked":"")."> <label for='clearance_type_9'>국내배송</label>
																</td>
																<td bgcolor='#efefef'>상품 URL</td>
																<td ><input  type=hidden name='bs_site' value='".$bs_site."'><input  type=text class='textbox'  name='bs_goods_url' value='".$bs_goods_url."' style='width:430px;'>  </td>
															</tr>
														</table>
													</td>
													<td align=center>".($product_type == "1" ? " <a href=\"javascript:PoPWindow('../product/buyingService_pricehistory.php?mmode=pop&id=$id',960,600,'brand')\"'><img src='../image/btn_bs_priceinfo.gif' align=absmiddle></a>":"<a href=\"javascript:alert(language_data['goods_input.php']['E'][language]);\"'><img src='../image/btn_bs_priceinfo.gif' align=absmiddle></a>")."</td>
													<!--td align=left>

													</td>
													<td></td-->

												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<table cellpadding=1 cellspacing=1 bgcolor=silver width=100% id=\"buyingServiceInfTable\" style='".($product_type == "1" ? "display:block;":"display:none;")."'>
											<col width=10%>
											<col width=10%>
											<col width=16%>
											<col width=10%>
											<col width=12%>
											<col width=18%>
											<tr bgcolor='#efefef' height=35 align=center>
												".($admininfo[admin_level] == 9 ? "<td>Orgin 원가($)</td>":"")."
												<td >환율 </td>
												<td class='small'>예상무게(파운드)/항공운송료($)</td>
												<td >관세 / 부가세</td>
												<td >통관수수료 </td>
												<!--td >딜러  ".($admininfo[admin_level] == 9 ? "<span class=small onclick=\"copyPrice(document.product_input, document.product_input.prd_dealer_price.value, 4);\" style='cursor:pointer;color:red'>복사→</span>":"")."</td-->
												<td class='small'>구매대행 수수료율(%)/수수료  </td>


											</tr>
											<tr bgcolor='#fbfbfb' height=35 align=center>
												".($admininfo[admin_level] == 9 ? "<td ><input  type=hidden name='b_orgin_price' value='".$orgin_price."'><input  type=text class='textbox' size=10  name='orgin_price' value='".$orgin_price."' style='text-align:right;background-color:#efefef' readonly></td>":"")."
												<td>
													<input type=hidden name=b_exchange_rate value='$exchange_rate'>
													".$currency_display[$admin_config["currency_unit"]]["front"]." <input type=text class='textbox numeric' name=exchange_rate size=10 value='$exchange_rate' maxlength=16 onkeyup='this.value=FormatNumber3(this.value);' style='TEXT-ALIGN:right;padding-right:3px;background-color:#efefef;".$colorString." ' readonly> ".$currency_display[$admin_config["currency_unit"]]["back"]."
												</td>
												<td >
													<input  type=hidden name='b_air_wt' value='$air_wt' >
													<input  type=text class='textbox numeric' size=4  name='air_wt' value='$air_wt' style='text-align:right;' maxlength=16 onkeyup='caculateBuyingServicePrice(this);this.value=FormatNumber3(this.value);'> lbs /
													<input  type=hidden name='b_air_shipping' value='$air_shipping' >
													<input  type=text class='textbox numeric' size=4  name='air_shipping' value='$air_shipping'  style='text-align:right;background-color:#efefef' readonly> $
													</td>
												<td>
													<input type=hidden name=b_duty value='$duty'>
													".$currency_display[$admin_config["currency_unit"]]["front"]." <input type=text class='textbox numeric' name=duty value='$duty' size=10  style='TEXT-ALIGN:right;padding-right:3px;;background-color:#efefef;".$colorString."'  maxlength=16 onkeyup='this.value=FormatNumber3(this.value);'   readonly> ".$currency_display[$admin_config["currency_unit"]]["back"]."
												</td>
												<td >
													<input type=text class='textbox numeric' name=clearance_fee size=10 style='text-align:right' value='$clearance_fee'  onkeyup='this.value=FormatNumber3(this.value);calcurate_maginrate(document.product_input)' style='TEXT-ALIGN:right;padding-right:3px;;background-color:#efefef;".$colorString."' readonly >
													".$currency_display[$admin_config["currency_unit"]]["front"]." <input type=hidden name=b_clearance_fee value='$clearance_fee' > ".$currency_display[$admin_config["currency_unit"]]["back"]."
												</td>
												<td>
													<input type=hidden name=b_bs_fee_rate value='$bs_fee_rate'>
													<input type=text class='textbox numeric' name=bs_fee_rate size=4 value='$bs_fee_rate' maxlength=16 onkeyup='caculateBuyingServicePrice(this);this.value=FormatNumber3(this.value);' style='TEXT-ALIGN:right;padding-right:3px;;".$colorString."' $message  $readonlyString>
													<input type=hidden name=b_bs_fee value='$bs_fee'>
													".$currency_display[$admin_config["currency_unit"]]["front"]." <input type=text class='textbox' name=bs_fee size=13 value='$bs_fee' onkeydown='onlyEditableNumber(this)' maxlength=16 onkeyup='caculateBuyingServicePrice(this);this.value=FormatNumber3(this.value);' style='TEXT-ALIGN:right;padding-right:3px;".$colorString."' $message  $readonlyString> ".$currency_display[$admin_config["currency_unit"]]["back"]."
												</td>
											</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<table cellpadding=1 cellspacing=1 bgcolor=silver width=100% >
											<col width=25%>
											<col width=25%>
											<col width=25%>";
if($admininfo[mall_type] != "O"){
		$Contents .= "						<col width=25%>";
}
		$Contents .= "
											<tr bgcolor='#efefef' height=35 align=center>";

		$Contents .= "
												<td ".($admininfo[mall_type] == "O" ? "style='display:none;'":"").">".($admininfo[admin_level] == 9 ? "<b>구매단가(공급가) <img src='".$required3_path."'></b>":"<b>공급가격 <img src='".$required3_path."'></b>")." </td>";

		$Contents .= "
												<td ><b>정가 <img src='".$required3_path."'></b> ".($admininfo[admin_level] == 9 ? "<span class=small onclick=\"copyPrice(document.product_input, document.product_input.listprice.value, 1);calcurate_maginrate(document.product_input)\" style='cursor:pointer;color:red'> 복사→</span>":"")."</td>
												<td ><b>판매가(할인가) <img src='".$required3_path."'></b> </td>


												<!--td >딜러  ".($admininfo[admin_level] == 9 ? "<span class=small onclick=\"copyPrice(document.product_input, document.product_input.prd_dealer_price.value, 4);\" style='cursor:pointer;color:red'>복사→</span>":"")."</td>
												<td >대리점  </td-->";
if($admininfo[mall_type] != "O"){
		$Contents .= "
												<td >마진(%) </td>";
}
		$Contents .= "

											</tr>
											<tr bgcolor='#fbfbfb' height=35 align=center>";

		$Contents .= "							<td ".($admininfo[mall_type] == "O" ? "style='display:none;'":"")."><input type=hidden name=bcoprice value='$coprice' >
												".$currency_display[$admin_config["currency_unit"]]["front"]."
												<input type=text class='textbox numeric' name=coprice size=13 style='text-align:right;padding-right:3px;' value='$coprice'  onkeyup='calcurate_maginrate(document.product_input)' style='TEXT-ALIGN:right;padding-right:3px;".$colorString."'  $message  $readonlyString ".($admininfo[mall_type] == "O" ? "":"validation=true")." title='구매단가(공급가)'>
													".$currency_display[$admin_config["currency_unit"]]["back"]."
												</td>
												<td><input type=hidden name=blistprice value='$listprice'> ".$currency_display[$admin_config["currency_unit"]]["front"]." <input type=text class='textbox numeric' name=listprice value='$listprice' size=13  style='TEXT-ALIGN:right;padding-right:3px;".$colorString."'  maxlength=16  $message  $readonlyString validation=true title='정가'> ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
												<td>
												<input type=hidden name=bsellprice value='$sellprice'>
												".$currency_display[$admin_config["currency_unit"]]["front"]."
												<input type=text class='textbox numeric' name=sellprice size=13 value='$sellprice'  maxlength=16 onkeyup='calcurate_maginrate(document.product_input)' style='TEXT-ALIGN:right;padding-right:3px;".$colorString." ' $message  $readonlyString validation=true title='판매가(할인가)'>
												".$currency_display[$admin_config["currency_unit"]]["back"]."
												</td>
												<td ".($admininfo[mall_type] == "O" ? "style='display:none;'":"")."> <input  type=text class='textbox' size=13  name='basic_margin' style='text-align:right;padding-right:3px;;background-color:#efefef' readonly></td>";



		$Contents .= "
											</tr>
											</table>
										</td>
									</tr>";


								$Contents .="<tr ".($admininfo[admin_level] != 9 ? "style='display:none'":"").">
										<td>
											<table cellpadding=1 cellspacing=1 bgcolor=silver width=100% border=0>
											<col width=25% />
											<col width=25% />
											<col width=25% />
											<col width=25% />
											<tr bgcolor='#efefef' height=35 align=center>

												<td nowrap>개별 적립금 사용유무</td>
												<td bgcolor='#fbfbfb'>
												<input type='radio' name='reserve_yn' value='Y' ".($reserve_yn == "Y" ? "checked":"").">적용 <input type='radio' name='reserve_yn' value='N' ".($reserve_yn == "N" || $reserve_yn == "" ? "checked":"")."> 적용안함
												</td>
												<td nowrap>개별 적립금 </td>
												<td bgcolor='#fbfbfb'>
													<input type=text class='textbox1 integer' name=reserve size=13 style='text-align:right;padding-right:3px;' value='$reserve'>
													<input type=hidden name=breserve size=15 style='text-align:right;padding-right:3px; vertical-align:middle;' value='$reserve' readonly>
													<select name=rate1 style='font-size:12px;width:50px; height:20px;  vertical-align:middle;' onchange=\"if(this.form.sellprice.value == ''){alert(language_data['goods_input.php']['F'][language]);}else{this.form.reserve.value=Round2(filterNum(this.form.sellprice.value) * this.value/100,1,1);}\">
														<option value=0>0%</option>
														<option value='0.5'>0.5%</option>
														<option value=1>1%</option>
														<option value='1.5'>1.5%</option>
														<option value='2' selected>2%</option>
														<option value='2.5'>2.5%</option>
														<option value=3>3%</option>
														<option value=5>5%</option>
														<option value=7>7%</option>
														<option value=10>10%</option>
														<option value=37>37%</option>
													</select>
												</td>

											</tr>
											</table>
											<!--table width=100% cellpadding=0 cellspacing=0>
												<col width=33%>
												<col width=1%>
												<col width=33%>
												<col width=1%>
												<col width=32%>
												<tr>
													<td align=left>
														<table cellpadding=1 cellspacing=1 bgcolor=#c0c0c0 width=100%>
															<tr bgcolor='#ffffff' height=30 align=center>
																<td colspan=2 bgcolor='#fbfbfb'>개별 적립금 사용유무</td>
																<td nowrap>
																<input type='radio' name='reserve_yn' value='Y' ".($reserve_yn == "Y" ? "checked":"").">적용 <input type='radio' name='reserve_yn' value='N' ".($reserve_yn == "N" || $reserve_yn == "" ? "checked":"")."> 적용안함
																</td>
															</tr>
														</table>
													</td>
													<td></td>
													<td align=left>
														<table cellpadding=1 cellspacing=1 bgcolor=#c0c0c0 width=100%>
															<tr bgcolor='#ffffff' height=30 align=center>
																<td colspan=2 bgcolor='#fbfbfb'>개별 적립금 </td>
																<td nowrap>
																<table cellpadding=3 cellspacing=0>
																<tr>
																	<td >
																	<input type=text class='textbox numeric' name=reserve size=13 style='text-align:right'  value='$reserve'>
																	<input type=hidden name=breserve size=15 style='text-align:right' value='$reserve' readonly>
																	</td>
																	<td align=center>

																		<select name=rate1 style='font-size:12px;width:50px' onchange=\"if(this.form.sellprice.value == ''){alert(language_data['goods_input.php']['F'][language]);}else{this.form.reserve.value=Round2(filterNum(this.form.sellprice.value) * this.value/100,1,1);}\">
																		<option value=0>0%</option>
																		<option value='0.5'>0.5%</option>
																		<option value=1>1%</option>
																		<option value='1.5'>1.5%</option>
																		<option value='2' selected>2%</option>
																		<option value='2.5'>2.5%</option>
																		<option value=3>3%</option>
																		<option value=5>5%</option>
																		<option value=7>7%</option>
																		<option value=10>10%</option>
																		<option value=37>37%</option>
																	</select>
																	</td>
																</tr>
																</table>
																</td>
															</tr>
														</table>
													</td>
													<td></td>

												</tr>
											</table-->
										</td>
									</tr>";

									$Contents .="<tr>
										<td>";


$help_text2 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'J');


$help_text = "
<table cellpadding=1 cellspacing=0 bgcolor=#c0c0c0 width=100%>
						<col width=20%>
						<col width=20%>
						<col width=20%>
						<col width=20%>
						<col width=20%>
						<tr bgcolor='#ffffff' height=25 align=center>
							<td align=left>카드수수료(<input type=text class='textbox' name=card_pay value='4' size=2 readonly style='border:1px;text-align:center'>% 기준)</td>
							<td>적립금사용(현금) </td>
							<td >무이자 수수료</td>
							<td ><b>토탈수수료</b></td>
							<td rowspan=2> &nbsp;<input type=button onclick='calcurate_margin(document.product_input);' value='계산하기'></td>
						</tr>
						<tr bgcolor='#ffffff' height=25 align=center>

							<td align=left><input type=text class='textbox' name=card_price value='' style='width:80%;text-align:right;background-color:#efefef' size=8 readonly></td>
							<td> + <input size=8 type=text class='textbox' name='reserve_price' style='text-align:right;width:80%;background-color:#efefef' readonly></td>
							<td> + <input size=8 type=text class='textbox' name='nointerest_price' style='text-align:right;width:80%;background-color:#efefef' value='' readonly></td>
							<td> = <input size=8 type=text class='textbox' name='margin' style='text-align:right;width:80%;background-color:#efefef' value='' readonly></td>
						</tr>
						<tr bgcolor='#ffffff'>
							<td  colspan=5 style='padding-top:10px;'>
							$help_text2
							</td>
						</tr>
						</table>";


$Contents .= "<div id='price_info' style='width:100%;'>".HelpBox("가격정보입력", $help_text2)."</div>";
$Contents .= "<div id='detail_price_info' style='position:relative;display:none'>".HelpBox("판매수수료 상세내역", $help_text, 200)."</div>";


$Contents .= "								</td>
									</tr>
								</table><br>

							</div>
						</div>
					</div>
				    </td>
				</tr>";



$Contents .="	</table>";


if($goods_input_type == "inventory"){
	$_Contents = "
	<table cellpadding=0 cellspacing=0><tr><td style='width:15px;'><img src='../images/dot_org.gif' align=absmiddle ></td><td><b> 옵션정보  </b></td><td><input type=checkbox name='basic_option_display_yn' id='basic_option_display_yn' onclick=\"$('#basic_option_zone').toggle();\"></td><td><label for='basic_option_display_yn'>표시</label></td></tr></table>";
}else{
	$_Contents = "<img src='../images/dot_org.gif' align=absmiddle style='position:relative;'> <b> 옵션정보  </b>";
}


$Contents .="<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'>".$_Contents."</div>")."</td></tr></table>";

$Contents .= "<div ".($goods_input_type == "inventory" ? "style='display:none;'":"")." id='basic_option_zone'>";

$Contents .= "<table><tr><td><img src='../images/".$admininfo["language"]."/btn_option_add.gif' border=0 align=absmiddle style='cursor:hand;margin:0 0 3px 0;' onclick=\"copyOptions('options_input')\"  ></td><td><input type=checkbox name='option_all_use' valign='middle' id='option_all_use' value='Y' align=absmiddle><label for='option_all_use' >옵션전체사용안함</label> <span class=small><!--(선택 후 저장하시면 옵션정보가 모두 삭제됩니다)--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'L')."</span></td></tr></table><input type=hidden id='option_name' inputid='option_name' value=''><input type=hidden id='options_option_use'  value='1'>";

$sql = "select * from service_product_options where pid = '".$id."' and option_kind != 'b' order by regdate asc ";
//echo $sql;
$db->query($sql);

$options = $db->fetchall();
//print_r($options);
if($db->total){
	for($i=0;$i < count($options);$i++){
		$Contents .= "
							<table width='100%' cellpadding=0 cellspacing=1 bgcolor=silver id='options_input' class='options_input' idx=".$i." style='margin-bottom:10px' >
								<col width='4%'>
								<col width='17%'>
								<col width='*'>
								<col width='14%'>
								<col width='14%'>
								<col width='14%'>
								<col width='14%'>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class=small nowrap>
									사용
									</td>
									<td bgcolor=\"#efefef\" class=small nowrap>
									 옵션명
									</td>
									<td bgcolor=\"#efefef\" class=small nowrap>
									 옵션종류
									</td>
									<td bgcolor=\"#efefef\" class=small> 옵션구분 *</td>
									<td bgcolor=\"#efefef\" class=small> 가격 *</td>
									<td bgcolor=\"#efefef\" class=small> 할인가격 *</td>
									<td bgcolor=\"#efefef\" class=small> 옵션코드  </td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td valign=top style='padding-top:6px;'>
										<input type=hidden name='options[".$i."][opn_ix]' id='option_opn_ix' value='".$options[$i][opn_ix]."'>
										<input type=hidden name='options[".$i."][option_type]' value='".($options[$i][option_type] ? $options[$i][option_type]:"9")."'>
										<input type=checkbox name='options[".$i."][option_use]' id='options_option_use' value='1' ".(($options[$i][option_use] == 1) ? "checked":"")." style='margin:0 0 0 0' align=absmiddle>
									</td>
									<td valign=top style='padding-top:4px;'>
										";

//$Contents .= "			<input type=text class='textbox' name='options[".$i."][option_name]' id='option_name' size=28 style='width:115;vertical-align:middle' value='".$options[$i][option_name]."'> <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if(document.all.options_input.length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>";
$Contents .= "			<span id='".$i."'><input type=text class='textbox' name='options[".$i."][option_name]' id='option_name' inputid='option_name' style='width:115px;vertical-align:middle' value='".$options[$i][option_name]."'> </span>

										".($i == 0 ? "<!-- 옵션 삭제 -->":"<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.options_input').length > 1){ \$('.options_input[idx=".$i."]').remove();showMessage('options_input_status_area_".$i."','해당 옵션 구분정보가 삭제 되었습니다.');}else{alert(language_data['goods_input.php']['G'][language]);}\" title='더블클릭시 해당 테이블이 삭제 됩니다.'>")." ";

$Contents .= "
									</td>
									<td valign=top style='padding-top:4px;'>
										<select name='options[".$i."][option_kind]' id='option_kind_1' style='font-size:12px;'>
											<option value=s ".($options[$i][option_kind] == "s" ? "selected":"").">선택옵션</option>
											<option value=p ".($options[$i][option_kind] == "p" ? "selected":"").">가격추가옵션</option>

										</select>
										<img src='../images/".$admininfo["language"]."/btn_option_detail_add.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"copyOptions('options_item_input_".$i."');showMessage('options_input_status_area_".$i."','해당옵션 구분정보가 추가 되었습니다.');\" />
									</td>
									<td colspan=4 id='options_basic_item_input_table_".$i."'><input type=hidden id='options_item_option_div_".$i."' inputid='options_item_option_div_".$i."' value=''><input type=hidden id='options_item_option_code_".$i."' value=''>
									";

			$sql = "select * from service_product_options_detail where pid = '".$id."' and opn_ix = '".$options[$i][opn_ix]."' order by id asc ";
			//echo $sql;
			$db->query($sql);

			if($db->total){
				for($j=0;$j < $db->total;$j++){
				$db->fetch($j);

		$Contents .= "<table width=100% id='options_item_input_".$i."' class='options_item_input_".$i."' idx=".$i." detail_idx=".$j." cellspacing=4 cellpadding=0 ><!--ondblclick=\"if(document.all.options_item_input_0.length > 1){this.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\"-->
											<col width='25%'>
											<col width='25%'>
											<col width='25%'>
											<col width='25%'>
											<tr>
												<td>
												<input type=hidden name='options[".$i."][details][".$j."][opd_ix]' value='".$db->dt[id]."'>
												<input type=text class='textbox' name='options[".$i."][details][".$j."][option_div]' id='options_item_option_div_".$i."' inputid='options_item_option_div_".$i."' style='width:90%;vertical-align:middle' value='".$db->dt[option_div]."'>
												</td>
												<td><input type=text class='textbox' name='options[".$i."][details][".$j."][coprice]' style='width:90%' value='".$db->dt[option_coprice]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
												<td><input type=text class='textbox' name='options[".$i."][details][".$j."][price]' style='width:90%' value='".$db->dt[option_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>";
			$Contents .= "			<td><input type=text class='textbox' name='options[".$i."][details][".$j."][code]' id='options_item_option_code_".$i."' style='width:70%' value='".$db->dt[option_code]."'> <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.options_item_input_".$i."').length > 1){document.getElementById('options_basic_item_input_table_".$i."').removeChild(this.parentNode.parentNode.parentNode.parentNode);showMessage('options_input_status_area_".$i."','해당옵션 구분정보가 삭제되었습니다.');}else{clearInputBox('options_item_input_".$i."');showMessage('options_input_status_area_".$i."','해당옵션 구분정보가 삭제되었습니다.');}\" title='더블클릭시 해당 라인이 삭제 됩니다.'></td>";
	//	$Contents .= "			<td><input type=text class='textbox' name='options[".$i."][details][".$j."][code]' size=28 style='width:85%' value='".$db->dt[option_code]."'> ".($j == 0 ? "<!-- 옵션 삭제 -->":"<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if(document.all.options_item_input_".$i.".length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>")."</td>";

		$Contents .= "
											</tr>
										</table>";
					}
			}else{
		$Contents .= "<table width=100% id='options_item_input_0' class='options_item_input_0' idx=0 detail_idx=0 cellspacing=4 cellpadding=0 ><!--ondblclick=\"if(document.all.options_item_input_0.length > 1){this.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\"-->
											<col width='25%'>
											<col width='25%'>
											<col width='25%'>
											<col width='25%'>
											<tr>
												<td>
													<input type=hidden name='options[0][details][0][opd_ix]' value=''>
													<input type=text class='textbox' name='options[0][details][0][option_div]' id='options_item_option_div_0' inputid='options_item_option_div_0' style='width:90%;vertical-align:middle' value=''>
												</td>
												<td><input type=text class='textbox' name='options[0][details][0][price]'  style='width:90%' value='' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
												<td><input type=text class='textbox' name='options[0][details][0][coprice]'  style='width:90%' value='' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
												<td><input type=text class='textbox' name='options[0][details][0][code]' id='options_item_option_code_0' style='width:70%' value=''><img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.options_item_input_0').length > 1){document.getElementById('options_basic_item_input_table_".$i."').removeChild(this.parentNode.parentNode.parentNode.parentNode);}else{clearInputBox('options_item_input_0');alert(language_data['goods_input.php']['G'][language]);}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'></td>
											</tr>
										</table>";
			}
$Contents .= "
									</td>
								</tr>
								<tr><td colspan=7 style='background-color:#ffffff;'><div style='height:30px;text-align:right;color:gray;line-height:220%;' id='options_input_status_area_".$i."'></div></td></tr>
							</table>
							";
		}
}else{
	$Contents .= "
							<table width='100%' cellpadding=0 cellspacing=1 bgcolor=silver border='0' id='options_input' class='options_input' idx=0 style='margin-bottom:10px;' ><!--ondblclick=\"if(document.all.options_input.length > 1){this.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\"-->
								<col width='4%'>
								<col width='17%'>
								<col width='*'>
								<col width='14%'>
								<col width='14%'>
								<col width='14%'>
								<col width='14%'>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class=small nowrap>
									사용
									</td>
									<td bgcolor=\"#efefef\" class=small nowrap>
									 옵션명
									</td>
									<td bgcolor=\"#efefef\" class=small nowrap>
									 옵션종류
									</td>
									<td bgcolor=\"#efefef\" class=small> 옵션구분 *</td>
									<td bgcolor=\"#efefef\" class=small> 가격 *</td>
									<td bgcolor=\"#efefef\" class=small> 할인가격 *</td>
									<td bgcolor=\"#efefef\" class=small> 옵션코드  </td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td valign='top' style='padding-top:6px;'>
									<input type=hidden name='options[0][option_type]' value='9'>
									<input type=checkbox name='options[0][option_use]' id='options_option_use'  value='1' checked>
									</td>
									<td valign='top' align=center style='padding-top:4px;'>

									<span id=''><input type=text class='textbox' name='options[0][option_name]' id='option_name' inputid='option_name' style='width:115px;vertical-align:middle' value='$option_div'></span><!-- 옵션 삭제 --></td>
									<td valign='top' align=center style='padding-top:4px;'>
									<select name='options[0][option_kind]' id='option_kind_1' style='font-size:12px;'>
										<option value=s>선택옵션</option>
										<option value=p>가격추가옵션</option>
									</select>

									<img src='../images/".$admininfo["language"]."/btn_option_detail_add.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"copyOptions('options_item_input_0');showMessage('options_input_status_area_0','해당옵션 구분정보가 추가 되었습니다.');\" />
									</td>
									<td colspan='4' id='options_basic_item_input_table_0'>
										<input type=hidden id='options_item_option_div_0' inputid='options_item_option_div_0' value=''>
										<input type=hidden id='options_item_option_code_0' value=''>
										

										<table width=100% id='options_item_input_0' class='options_item_input_0' idx=0 detail_idx=0 cellspacing=4 cellpadding=0 ><!--ondblclick=\"if($('.options_item_input_0').length > 1){this.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\"-->
											<col width='25%'>
											<col width='25%'>
											<col width='25%'>
											<col width='25%'>
											<tr align='center'>
												<td><input type=text class='textbox' name='options[0][details][0][option_div]' id='options_item_option_div_0' inputid='options_item_option_div_0' style='width:90%;vertical-align:middle' value='$option_div'></td>
												<td><input type=text class='textbox' name='options[0][details][0][coprice]'  style='width:90%' value='$option_coprice' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
												<td><input type=text class='textbox' name='options[0][details][0][price]'  style='width:90%' value='$option_price' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>";

$Contents .= "					<td><input type=text class='textbox' name='options[0][details][0][code]' id='options_item_option_code_0' style='width:70%' value='$option_code'> <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.options_item_input_0').length > 1){document.getElementById('options_basic_item_input_table_0').removeChild(this.parentNode.parentNode.parentNode.parentNode);showMessage('options_input_status_area_0','해당옵션 구분정보가 삭제되었습니다.');}else{clearInputBox('options_item_input_0');showMessage('options_input_status_area_0','해당옵션 구분정보가 삭제되었습니다.');}\" title='더블클릭시 해당 라인이 삭제 됩니다.'></td>";

//$Contents .= "					<td><input type=text class='textbox' name='options[0][details][0][code]' size=28 style='width:85%' value='$option_code'> <!-- 옵션 삭제 --></td>";

$Contents .= "
											</tr>
										</table>
									</td>
								</tr>
								<tr><td colspan=7 style='background-color:#ffffff;'><div style='height:30px;text-align:right;color:gray;line-height:220%;' id='options_input_status_area_0'></div></td></tr>
							</table>

							";
}
$Contents .="				<div style='line-height:130%;padding:10px 0px 20px 0px'>
							재고관리가 필요 없는 상품일 경우 옵션 추가를 이용하여 아래 예와 같이 옵션을 분리 적용 하실 수 있습니다.<br>
							예) 옵션1 – 옵션명 : 색상 / 옵션구분 : RED, BLUE<br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;옵션2 – 옵션명 : 사이즈 / 옵션구분 : 95size, 100size, 105size<br>

							</div>
		</div>";



if($admininfo[admin_level] == 9){

		if($goods_input_type == "inventory"){
			$_Contents = "
			<table cellpadding=0 cellspacing=0><tr><td style='width:15px;'><img src='../images/dot_org.gif' align=absmiddle ></td><td><b> 가격+재고 관리 옵션정보  </b></td><td><input type=checkbox name='price_option_display_yn' id='price_option_display_yn' onclick=\"$('#price_option_zone').toggle();\"></td><td><label for='price_option_display_yn'>표시</label></td></tr></table>";
		}else{
			$_Contents = "<img src='../images/dot_org.gif' align=absmiddle style='position:relative;'> <b> 가격+재고 관리 옵션정보  </b>";
		}

		$Contents .="
							<table width='100%' cellpadding=0 cellspacing=0>
									<tr height=30>
									<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'>".$_Contents." </div>")."</td>
									</tr>
									</table>";

		$Contents .="<div ".($goods_input_type == "inventory" ? "style='display:none;'":"")." id='price_option_zone'>";
		$sql = "select * from service_product_options where pid = '".$id."' and pid != '' and option_kind = 'b' order by opn_ix ";

		$db->query($sql);
		if($db->total) {
			$db->fetch();
			$options_price_stock = $db->dt;
		}
		$Contents .= "
							<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver id='options_basic_input' class='options_basic_input' opt_idx=0 style='margin-bottom:10px'>
								<col width='4%'>
								<col width='*'>
								<col width='12%'>
								<col width='12%'>
								<col width='12%'>
								<col width='12%'>
								<col width='12%'>
								<col width='14%'>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class=small nowrap>
									사용
									</td>
									<td bgcolor=\"#efefef\" class=small nowrap>
									 옵션명
									</td>
									<td bgcolor=\"#efefef\" class=small> 옵션구분 *</td>
									<td bgcolor=\"#efefef\" class=small> 옵션공급가 *</td>
									<td bgcolor=\"#efefef\" class=small> 옵션가격 *</td>
									<td bgcolor=\"#efefef\" class=small> 옵션별재고 *</td>
									<td bgcolor=\"#efefef\" class=small> 옵션안전재고 </td>
									<td bgcolor=\"#efefef\" class=small> 옵션기타 </td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td valign=top style='padding-top:10px;'>
									<input type=hidden name='options_price_stock[option_kind]' value='b'><input type=hidden name='options_price_stock[option_type]' value='9'>
									<input type=checkbox name='options_price_stock[option_use]' id='options_price_stock_option_use'  value='1' ".(($options_price_stock[option_use] == 1) ? "checked":"").">
									</td>
									<td valign=top>
									<input type=hidden name='options_price_stock[opn_ix]' id='option_opn_ix' value='".$options_price_stock[opn_ix]."' style='vertical-align:middle;'>
									<input type=text class='textbox' name='options_price_stock[option_name]' id='options_price_stock_option_name' style='width:120px;vertical-align:middle;' value='".$options_price_stock[option_name]."'>

									<a onclick=\"copyOptions('options_basic_item_input_0');showMessage('options_basic_item_input_status_area','가격+재고 옵션 구분정보가 추가 되었습니다.');\" ><img src='../images/".$admininfo["language"]."/btn_option_detail_add.gif' border=0 style='margin:5px 0 3px 0; vertical-align:middle;'></a>
									</td>
									<td colspan=6 id='options_basic_item_input_table'><input type=hidden id='options_price_stock_option_div' inputid='options_price_stock_option_div' value=''><input type=hidden id='options_price_stock_option_price' value=''><input type=hidden id='options_price_stock_option_etc1' value=''><input type=hidden id='options_price_stock_option_stock' value=''>";

		$sql = "select * from service_product_options_detail where pid = '".$id."' and pid != ''  and opn_ix = '".$options_price_stock[opn_ix]."' order by id asc ";
		//echo $sql;
		$db->query($sql);

		if($db->total){
			for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

		$Contents .= "
										<table width=100% id='options_basic_item_input_0' class='options_basic_item_input_0' opt_idx=0 cellspacing=0 cellpadding=0 >
											<col width='15%'>
											<col width='17%'>
											<col width='17%'>
											<col width='17%'>
											<col width='17%'>
											<col width='17%'>
											<tr >
												<td height='30'>
													<input type=hidden name='options_price_stock[opd_ix][]' value='".$options_price_stock[id]."'>
													<input type=text class='textbox' name='options_price_stock[option_div][]' id='options_price_stock_option_div' inputid='options_price_stock_option_div' style='width:98px;vertical-align:middle' value='".$db->dt[option_div]."'>
												</td>
												<td><input type=text class='textbox number' name='options_price_stock[coprice][]' id='options_price_stock_option_coprice'  style='width:98px' value='".$db->dt[option_coprice]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' ></td>
												<td><input type=text class='textbox number' name='options_price_stock[price][]' id='options_price_stock_option_price'  style='width:98px' value='".$db->dt[option_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' ></td>
												<td><input type=text class='textbox' name='options_price_stock[option_sell_ing_cnt][]' style='width:30px;margin:0px 3px;'  value='".$db->dt[option_sell_ing_cnt]."' title='판매진행중 재고' readonly><input type=text class='textbox number' name='options_price_stock[stock][]' id='options_price_stock_option_stock' style='width:50px;".($layout_config["mall_use_inventory"] == "Y" ? "background:#efefef;":"")."' value='".$db->dt[option_stock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' ".($layout_config["mall_use_inventory"] == "Y" ? "readonly onclick=\"alert('재고관리 사용시는 재고 수정을 임의로 하실 수 없습니다. 재고관리 입고, 출고 하기를 이용하시기 바랍니다.');\"":"")."></td>
												<td><input type=text class='textbox number' name='options_price_stock[safestock][]' style='width:98px' value='".$db->dt[option_safestock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>";

		$Contents .= "					<td><input type=text class='textbox' name='options_price_stock[option_etc1][]' id='options_price_stock_option_etc1' style='width:78px' value='".$db->dt[option_etc1]."'> <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' title='더블클릭시 해당 라인이 삭제 됩니다.' ondblclick=\"if($('.options_basic_item_input_0').length > 1){document.getElementById('options_basic_item_input_table').removeChild(this.parentNode.parentNode.parentNode.parentNode);/*this.parentNode.parentNode.parentNode.parentNode.removeNode(true);*/showMessage('options_basic_item_input_status_area','가격+재고 옵션 구분정보가 삭제 되었습니다.');}else{clearInputBox('options_basic_input');$('options_price_stock_option_name').value='';showMessage('options_basic_item_input_status_area','가격+재고 옵션 구분정보가 삭제 되었습니다.');}\"> </td>";

		//$Contents .= "					<td><input type=text class='textbox' name='options_price_stock[option_etc1][]' size=28 style='width:80%' value='".$db->dt[option_etc1]."'> ".($i == 0 ? "<!-- 옵션 삭제 -->":"<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' alt='더블클릭시 해당 라인이 삭제 됩니다.'>")." </td>";

		$Contents .= "
													</tr>
												</table>";
			}
		}else{

			$Contents .= "
										<table width=100% border=0 id='options_basic_item_input_0' class='options_basic_item_input_0' opt_idx=0 cellspacing=0 cellpadding=0 >
											<col width='15%'>
											<col width='17%'>
											<col width='17%'>
											<col width='17%'>
											<col width='17%'>
											<col width='17%'>
											<tr align='center'>
												<td height='30'>
													<input type=hidden name='options_price_stock[opd_ix][]' value=''>
													<input type=text class='textbox' name='options_price_stock[option_div][]' id='options_price_stock_option_div' inputid='options_price_stock_option_div' style='width:98px;vertical-align:middle' value=''>
												</td>
												<td><input type=text class='textbox number' name='options_price_stock[coprice][]' id='options_price_stock_option_coprice'  style='width:98px' value='' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
												<td><input type=text class='textbox number' name='options_price_stock[price][]' id='options_price_stock_option_price'  style='width:98px' value='' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
												<td><input type=text class='textbox' name='options_price_stock[option_sell_ing_cnt][]' style='width:30px;margin:0px 3px;'  value='' title='판매진행중 재고' readonly><input type=text class='textbox number' name='options_price_stock[stock][]' id='options_price_stock_option_stock' style='width:50px;".($layout_config["mall_use_inventory"] == "Y" ? "background:#efefef;":"")."' value='' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' ".($layout_config["mall_use_inventory"] == "Y" ? "readonly onclick=\"alert('재고관리 사용시는 재고 수정을 임의로 하실 수 없습니다. 재고관리 입고, 출고 하기를 이용하시기 바랍니다.');\"":"")."></td>
												<td><input type=text class='textbox number' name='options_price_stock[safestock][]' style='width:98px' value='' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
												<td><input type=text class='textbox' name='options_price_stock[option_etc1][]' id='options_price_stock_option_etc1' style='width:78px' value=''> <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' title='더블클릭시 해당 라인이 삭제 됩니다.' ondblclick=\"if($('.options_basic_item_input_0').length > 1){document.getElementById('options_basic_item_input_table').removeChild(this.parentNode.parentNode.parentNode.parentNode);/*this.parentNode.parentNode.parentNode.parentNode.removeNode(true);*/showMessage('options_basic_item_input_status_area','가격+재고 옵션 구분정보가 삭제 되었습니다.');}else{clearInputBox('options_basic_input');$('options_price_stock_option_name').value='';showMessage('options_basic_item_input_status_area','가격+재고 옵션 구분정보가 삭제 되었습니다.');}\"> </td>
											</tr>
										</table>";
		}
		$Contents .= "
									</td>
								</tr>
							</table>
							<div style='height:10px;text-align:right;color:gray;line-height:220%;' id='options_basic_item_input_status_area'></div><br>";
			$Contents .="	<div style='line-height:130%;padding:0px 0px 20px 0px'>
							각 옵션 별 재고관리가 필요한 상품일 경우 아래 예와 같이 입력하여 옵션별 재고를 관리 하실 수 있습니다.<br>
							예) 옵션명 : 색상/사이즈<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;옵션구분 : RED / 95size, RED / 100size<br>
							</div>
			</div>";
}


$Contents .="<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;top:-1px;'><b> 제품상세정보</b> <span class=small > <img src='../images/".$admininfo["language"]."/webedit/wtool6_1.gif' align=absmiddle> <!--이미지를 클릭해서 상품상세 이미지를 등록해주세요-->   ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'N')."</span><br><input type=checkbox name='goods_desc_copy' id='goods_desc_copy' value='1'><label for='goods_desc_copy'><b>상품상세 이미지  복사</b></label> <!--(체크시 입력한 URL의 이미지를 현재 서버에 복사해 오게 됩니다. 이미지 호스팅을 이용할 경우 체크 하지 마세요.)--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'O')."  </div>    ")."</td></tr></table>
		<table width='100%' border='0' cellspacing='0' cellpadding='0' class='input_table_box'>
					<tr bgcolor='#ffffff'>
						<td width='15%' class='input_box_title' nowrap> 제품간략소개 *</td>
						<td width='85%' class='input_box_item' style='padding:4px;'><textarea name=\"shotinfo\" style='padding:2px;height:40px;width:98%' class='tline'>".$shotinfo."</textarea></td>
					</tr>
					<tr>
					  <td height='30' colspan='2'>
							  <table id='tblCtrls' width='100%' border='0' cellspacing='0' cellpadding='0' align='center'>
								<tr>
								  <td bgcolor='F5F6F5'>
									 <table width='100%' border='0' cellspacing='0' cellpadding='0'>
									  <tr>
										<td width='18%' height='56'>
										 <table width='100%' height='56' border='0' align='center' cellpadding='0' cellspacing='0'>
											<tr align='center' valign='bottom'>
											  <td height='26'><a href='javascript:doBold();' onMouseOver=\"MM_swapImage('editImage1','','../images/".$admininfo["language"]."/webedit/wtool1_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool1.gif' name='editImage1' width='19' height='18' border='0' id='editImage1'></a></td>
											  <td><a href='javascript:doItalic();' onMouseOver=\"MM_swapImage('editImage2','','../images/".$admininfo["language"]."/webedit/wtool2_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool2.gif' name='editImage2' width='19' height='18' border='0' id='editImage2'></a></td>
											  <td><a href='javascript:doUnderline();' onMouseOver=\"MM_swapImage('editImage3','','../images/".$admininfo["language"]."/webedit/wtool3_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool3.gif' name='editImage3' width='19' height='18' border='0' id='editImage3'></a></td>
											</tr>
											<tr>
											  <td height='3' colspan='3'></td>
											</tr>
											<tr align='center' valign='top'>
											  <td height='27'><a href='javascript:doLeft();' onMouseOver=\"MM_swapImage('editImage8','','../images/".$admininfo["language"]."/webedit/wtool8_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool8.gif' name='editImage8' width='19' height='18' border='0' id='editImage8'></a></td>
											  <td><a href='javascript:doCenter();' onMouseOver=\"MM_swapImage('editImage9','','../images/".$admininfo["language"]."/webedit/wtool9_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool9.gif' name='editImage9' width='19' height='18' border='0' id='editImage9'></a></td>
											  <td><a href='javascript:doRight();' onMouseOver=\"MM_swapImage('editImage10','','../images/".$admininfo["language"]."/webedit/wtool10_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool10.gif' name='editImage10' width='19' height='18' border='0' id='editImage10'></a></td>
											</tr>
										 </table>
										</td>
										<td width='2'><img src='../images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
										<td width='19%'>
										  <table width='100%' border='0' cellspacing='0' cellpadding='0'>
											<tr>
											  <td width='100%' height='27' align='center' valign='bottom'><a href='javascript:doFont();' onMouseOver=\"MM_swapImage('editImage4','','../images/".$admininfo["language"]."/webedit/wtool4_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool4.gif' name='editImage4' width='84' height='22' border='0' id='editImage4'></a></td>
											</tr>
											<tr>
											  <td height='2'></td>
											</tr>
											<tr>
											  <td height='27' align='center' valign='top'><a href='javascript:doSize();' onMouseOver=\"MM_swapImage('editImage11','','../images/".$admininfo["language"]."/webedit/wtool11_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool11.gif' name='editImage11' width='84' height='22' border='0' id='editImage11'></a></td>
											</tr>
										  </table>
											 </td>
										<td width='2'><img src='../images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
										<td width='20%'>
										  <table width='100%' border='0' cellspacing='0' cellpadding='0'>
											<tr>
											  <td height='27' align='center' valign='bottom'><a href='javascript:doForcol();' onMouseOver=\"MM_swapImage('editImage5','','../images/".$admininfo["language"]."/webedit/wtool5_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool5.gif' name='editImage5' width='95' height='22' border='0' id='editImage5'></a></td>
											</tr>
											<tr>
											  <td height='2'></td>
											</tr>
											<tr>
											  <td height='27' align='center' valign='top'><a href='javascript:doBgcol();' onMouseOver=\"MM_swapImage('editImage12','','../images/".$admininfo["language"]."/webedit/wtool12_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool12.gif' name='editImage12' width='95' height='22' border='0' id='editImage12'></a></td>
											</tr>
										  </table>
											 </td>
										<td width='2'><img src='../images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
										<td width='18%'>
										  <table width='100%' border='0' cellspacing='0' cellpadding='0'>
											<tr>
											  <td height='27' align='center' valign='bottom'><a href='javascript:doImage();' onMouseOver=\"MM_swapImage('editImage6','','../images/".$admininfo["language"]."/webedit/wtool6_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool6.gif' name='editImage6' width='73' height='22' border='0' id='editImage6'></a></td>
											</tr>
											<tr>
											  <td height='2'></td>
											</tr>
											<tr>
											  <td height='27' align='center' valign='top'><a href='javascript:doTable();' onMouseOver=\"MM_swapImage('editImage13','','../images/".$admininfo["language"]."/webedit/wtool13_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool13.gif' name='editImage13' width='73' height='22' border='0' id='editImage13'></a></td>
											</tr>
										  </table>
											 </td>
										<td width='2'><img src='../images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
										<td width='25%'>
										  <table width='100%' border='0' cellspacing='0' cellpadding='0'>
											<tr>
											  <td height='27' align='center' valign='bottom'><a href='javascript:doLink();' onMouseOver=\"MM_swapImage('editImage7','','../images/".$admininfo["language"]."/webedit/wtool7_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool7.gif' name='editImage7' width='74' height='22' border='0' id='editImage7'></a></td>
											</tr>
											<tr>
											  <td height='2'></td>
											</tr>
											<tr>
											  <td height='27' align='center' valign='top'><a href='javascript:doMultilink();' onMouseOver=\"MM_swapImage('editImage14','','../images/".$admininfo["language"]."/webedit/wtool14_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool14.gif' name='editImage14' width='111' height='22' border='0' id='editImage14'></a></td>
											</tr>
										  </table>
										</td>
									  </tr>
									 </table>
								  </td>
								</tr>
							  </table>
							<table width='100%' border='0' cellspacing='3' cellpadding='0' >
								<tr>
								<td  bgcolor='#ffffff'>
									<textarea name=\"basicinfo\"  style='display:none' >".$basicinfo."</textarea>
									<input type='hidden' name='content' value=''>
									<iframe align='right' id='iView' style='width:100%; height:510px;' scrolling='YES' hspace='0' vspace='0'></iframe>
								</td>
								</tr>
							</table>
							  <!-- html편집기 메뉴 종료 -->
					  </td>
					</tr>
		</table>
		<table width='100%' border='0' cellspacing='0' cellpadding='0' >
		<tr>
		   <td colspan='2' align='right' class='input_box_item' style='text-align:right;'>&nbsp;
				<a href='javascript:doToggleText(document.product_input);' onMouseOver=\"MM_swapImage('editImage15','','../images/".$admininfo["language"]."/webedit/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
				<a href='javascript:doToggleHtml(document.product_input);' onMouseOver=\"MM_swapImage('editImage16','','../images/".$admininfo["language"]."/webedit/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
		  </td>
		</tr>
	  </table><br>";
if ($id != ""){
	$img_view_style = " style='display:block;'";
}else{
	$img_view_style = " style='display:none;''"	;
}
$image_db = new Database;
$image_db->query("select * from service_image_resizeinfo order by idx");
$image_info = $image_db->fetchall();
$Contents = $Contents."	<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;top:-1px;'><b> 이미지 추가</b> <span class=small >   ".$image_info[0][width]."*".$image_info[0][height]." <!--이미지를 등록하시면 체크된 작은 이미지는 자동으로 등록됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'X')."</span> <a href=\"javascript:PoPWindow('../service/service_product_resize.php?mmode=pop',560,600,'brand')\"'><img src='../images/".$admininfo["language"]."/btn_pop_image.gif' border=0></a><br> <span style='line-height:140%; margin-left:90px;'><!--* 위 아래로 긴 이미지를 사용하실 경우 가로 사이즈만 맞춰주시면 자동으로 조정되어 집니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'P')."</div>")." </span></td></tr></table>
			<table border=0 cellpadding=5 cellspacing=0 bgcolor=#ffffff width=100% class='input_table_box'>
				<col width=13%>
				<col width=45%>
				<col width=*>

				<tr bgcolor='#ffffff' height=30>
					<td class='input_box_title'  nowrap  >
						 ".$image_info[0][width]."*".$image_info[0][height]." *<br>
						<b>이미지복사</b><input type=checkbox name='chk_allimg' value=1 id='copy_allimg' inputid='copy_allimg' onclick='copyImageCheckAll();'><br>
						".($admininfo[admin_level] == 9 ? "<b> deepzoom 생성</b><input type=checkbox name='chk_deepzoom' value=1 >":"")."
					</td>
					<td class='small'  >
						<table>
							<tr>
								<td><input type=file name='allimg' class='textbox' size=25 style='font-size:8pt'></td>
								<td ".$img_view_style." rowspan=2><img src='../images/".$admininfo["language"]."/btn_view_img.gif' onclick=\"ChnageImg('".PrintImage($admin_config[mall_data_root]."/images/service_product", $id, "b")."');\" style='cursor:pointer'></td>
							</tr>
							<tr height=10><td colspan= class='small'>※ ".$image_info[0][width]."*".$image_info[0][height]." <!--이미지 복사를 클릭하시면 나머지 이미지가 복사됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'Y')."</td></tr>
						</table>
					</td>

					<td rowspan=5 class='input_box_item' style='padding:20px;border:0px solid silver;text-align:center;' align=center valign=middle id=viewimg>
					";
				if(file_exists($DOCUMENT_ROOT.PrintImage($admin_config[mall_data_root]."/images/service_product", $id, "m"))){
					$Contents = $Contents."<img src='".PrintImage($admin_config[mall_data_root]."/images/service_product", $id, "m")."' onerror=\"this.src='../images/noimage_152_148.gif'\" style='border:1px solid silver' id=chimg>";
				}else{
					$Contents = $Contents."<img src='../images/noimage_152_148.gif' style='border:1px solid silver' id=chimg>";
				}
					$Contents = $Contents."</td>
				</tr>
				<tr bgcolor='#ffffff' height=50>
					<td class='input_box_title'  nowrap >
						 ".$image_info[1][width]."*".$image_info[1][height]." *<br>이미지복사
						<input type=checkbox name='chk_mimg' id='copy_img' inputid='copy_img' value=1>
					</td>
					<td class='input_box_item'>
						<table>
							<tr>
								<td><input type=file name='mimg' class='textbox' size=25 style='font-size:8pt'></td>
								<td ".$img_view_style."><img src='../images/".$admininfo["language"]."/btn_view_img.gif' onclick=\"ChnageImg('".PrintImage($admin_config[mall_data_root]."/images/service_product", $id, "m")."');\" style='cursor:pointer'></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor='#ffffff' height=50>
					<td class='input_box_title'  nowrap >
						 ".$image_info[2][width]."*".$image_info[2][height]." *<br>
						이미지복사<input type=checkbox name='chk_msimg' id='copy_img' inputid='copy_img' value=1>
					</td>
					<td class='input_box_item'>
						<table>
							<tr>
								<td><input type=file name='msimg' class='textbox' size=25 style='font-size:8pt'></td>
								<td ".$img_view_style."><img src='../images/".$admininfo["language"]."/btn_view_img.gif' onclick=\"ChnageImg('".PrintImage($admin_config[mall_data_root]."/images/service_product", $id, "ms")."');\" style='cursor:pointer'></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor='#ffffff' height=50>
					<td class='input_box_title'  nowrap>
						  ".$image_info[3][width]."*".$image_info[3][height]." *<br>
						이미지복사<input type=checkbox name='chk_simg' id='copy_img' inputid='copy_img' value=1>
					</td>
					<td class='input_box_item'>
						<table>
							<tr>
								<td><input type=file name='simg' class='textbox' size=25 style='font-size:8pt'></td>
								<td ".$img_view_style."><img src='../images/".$admininfo["language"]."/btn_view_img.gif' onclick=\"ChnageImg('".PrintImage($admin_config[mall_data_root]."/images/service_product", $id, "s")."');\" style='cursor:pointer'></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor='#ffffff' height=50>
					<td class='input_box_title'  nowrap>
						 ".$image_info[4][width]."*".$image_info[4][height]." *<br>
						이미지복사<input type=checkbox name='chk_cimg' id='copy_img' inputid='copy_img' value=1>
					</td>
					<td class='input_box_item' >
						<table>
							<tr>
								<td><input type=file name='cimg' class='textbox' size=25 style='font-size:8pt'></td>
								<td ".$img_view_style."><img src='../images/".$admininfo["language"]."/btn_view_img.gif' onclick=\"ChnageImg('".PrintImage($admin_config[mall_data_root]."/images/service_product", $id, "c")."');\" style='cursor:pointer'></td>
							</tr>
						</table></td>

				</tr>
			<!--/table>
			<table border=0 cellpadding=5 cellspacing=0 bgcolor=#ffffff width=100%>
				<col width=13%>
				<col width=*-->
				<tr bgcolor='#ffffff' height=50>
					<td class='input_box_title'  nowrap>
					 이미지 URL <br>
					</td>
					<td class='input_box_item' colspan=2 style='padding:10px 5px;'>
					<input type=checkbox name='img_url_copy' id='img_url_copy' value=1 > <label for='img_url_copy' >URL 이미지복사</label> <img src='../images/".$admininfo["language"]."/btn_view_img.gif' onclick=\"ChnageImg('".$bimg_text."');\" style='cursor:pointer' align=absmiddle><br>
					<input type=text name='bimg_text' class='textbox' style='width:750px;font-size:8pt;margin:3px;' value='".$bimg_text."'>
					<div class=small> <!--URL 이미지복사를 체크하시면 입력된 이미지 URL 정보를 바탕으로 이미지가 복사됩니다. 단 해당이미지 서버에서 이미지 복사를 차단한 경우는 이미지 복사가 거부될 수 있습니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'Q')." </div>
					</td>

				</tr>
			</table><br>";

$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'R');

$Contents .= HelpBox("개별상품등록", $help_text);



$Contents = $Contents."
			<table width='100%'>
			<tr height=30 align=left><td width=500></td>";

if ($id == "" || $mode == "copy"){
	$Contents .= "<td align=right>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
		$Contents .= "<img src='../images/".$admininfo["language"]."/btn_save_tmp.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'tmp_insert');\"> ";
		$Contents .= "<img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'insert');\">";
	}
	$Contents .= "</td>";
}else{
	$Contents .= "<td align=right>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
		$Contents .= "<img src='../images/".$admininfo["language"]."/btn_save_tmp.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'tmp_update');\"> ";
		$Contents .= "<img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'update')\"> ";
	}
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
		$Contents .= "<img src='../images/".$admininfo["language"]."/b_del.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"ProductInput(document.product_input,'delete')\">";
	}
	$Contents .= "</td>";
}
$Contents .= "

			</td></tr>
			</table>
			</form>";
if($_relation_view_type =="small"){
	$ajax_add_string = "tag:'img',overlap:'horizontal',constraint:false, ";
}

if ($id && $mode != "copy"){
$Contents .= "
<script type='text/javascript'>
/*
Sortable.create('sortlist',
{
	$ajax_add_string
	onUpdate: function()
	{
		//alert(Sortable.serialize('sortlist'));
		new Ajax.Request('/admin/product/product_input.act.php',
		{
			method: 'POST',
			parameters: Sortable.serialize('sortlist')+'&act=vieworder_update&pid=$id',
			onComplete: function(transport){
			//alert(transport.responseText);
			}
		});
	}
});
*/
</script>";
}

/*
			<div class=box id=img3  style='Z-INDEX: 110; FILTER: revealTrans(duration=1); LEFT: 10px; VISIBILITY: hidden; WIDTH: 200px; POSITION: absolute; TOP: 10px'>
				<table cellpadding=0 cellspacing=0 bgcolor=#ffffff style='border:1px solid #000000'>
				<tr height=30 align=center bgcolor='#efefef'><td onclick='outTip(img3)'>상품카테고리정보</td></tr>
				<tr><td>".Category()."</td></tr>
				</table>
				</div>
			</div>
			<br>";
*/



$Script = "<link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' />
<!--script language='JavaScript' src='../js/scriptaculous.js'></script-->
<script language='JavaScript' src='../webedit/webedit.js'></script>
<script Language='JavaScript' src='../include/zoom.js'></script>
<script Language='JavaScript' src='../product/addoption.js'></script>
<script language='JavaScript' src='../js/dd.js'></script>
<!--script language='JavaScript' src='../js/mozInnerHTML.js'></script-->
<script type='text/javascript' src='../marketting/relationAjaxForEvent.js'></script>
<script Language='JavaScript' src='../include/DateSelect.js'></script>
<script Language='JavaScript' src='../service/service_goods_input.js'></script>
\n$Script";


if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	
	if ($id != ""){
		if ($admininfo[admin_level] == 9){
			//$P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);deliveryTypeView('".$delivery_policy."')";
			$P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);";//kbk 11/11/08
		}else{
			//$P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);deliveryTypeView('".$delivery_policy."')";
			$P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);";//kbk 11/11/08
		}
	}else{
		$P->OnloadFunction = "init();";
	}

	$P->strLeftMenu = service_menu();
	$P->strContents = $Contents;
	$P->Navigation = "서비스관리 > 서비스상품등록";
	$P->NaviTitle = "서비스상품등록";
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	if ($id != ""){
		if ($admininfo[admin_level] == 9){
			//$P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);deliveryTypeView('".$delivery_policy."')";
			$P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);";//kbk 11/11/08
		}else{
			//$P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);deliveryTypeView('".$delivery_policy."')";
			$P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);";//kbk 11/11/08
		}
	}else{
		$P->OnloadFunction = "init();";
	}

	if($goods_input_type == "inventory"){
		$P->strLeftMenu = inventory_menu();
		$P->Navigation = "재고관리 > 재고상품등록";
		$P->title = "재고상품등록";
	}else{
		$P->strLeftMenu = service_menu();
		$P->Navigation = "서비스관리 > 서비스상품등록";
		$P->title = "서비스상품등록";
	}

	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}




function search_select($pid){
$listdb = new Database;

$listdb->query("SELECT m.id,m.myshopping_desc,s.disp  FROM myshopping m left outer join search_relation s on m.id = s.search_id and s.pid = '$pid'");



	$mstr = "	<select name=search_id[] style='height:130px;width:630px;'multiple>";


	for($i=0;$i < $listdb->total;$i++){
	$listdb->fetch($i);
		if($listdb->dt[disp] == 1){
			$mstr = $mstr."		<option value='".$listdb->dt[id]."' selected>".($i+1).". ".$listdb->dt[myshopping_desc]." ".$listdb->dt[disp]."</option>";
		}else{
			$mstr = $mstr."		<option value='".$listdb->dt[id]."'>".($i+1).". ".$listdb->dt[myshopping_desc]." ".$listdb->dt[disp]."</option>";
		}
	}


	$mstr = $mstr."  </select>";

	return $mstr;

}


function PrintRootNode($cname){

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

	$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";

	return $vPrintRootNode;
}

function PrintNode($cname,$cid,$depth)
{
	global $id;
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"setCategory('insert','$cname','$cid',$depth,'$id')\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($cname,$cid,$depth)
{
	global $id;
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$Parentdepth = $depth - 1;

	if ($depth+1 == 1){
		$cid1 = "000";
	}else if($depth+1 == 2){
		$cid2 = "000";
	}else if($depth+1 == 3){
		$cid3 = "000";
	}else if($depth+1 == 4){
		$cid4 = "000";
	}else if($depth+1 == 3){
		$cid5 = "000";
	}

	$parent_cid = "$cid1$cid2$cid3$cid4$cid5";

	if ($depth ==1){
		$ParentNodeCode = "node$parent_cid";
	}else if($depth ==2){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==3){
		$ParentNodeCode = "groupnode$parent_cid";
	}

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

	return "		var groupnode$cid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$cid.tooltip = '$cname';
		groupnode$cid.id ='nodeid$cid';
		groupnode$cid.action = \"setCategory('insert','$cname','$cid',$depth,'$id')\";
		$ParentNodeCode.addNode(groupnode$cid);\n\n";
}

function Category()
{
	global $id;
	global $db;

$cate = "
<script language=\"JavaScript\" src=\"../include/manager.js\"></script>
<script language=\"JavaScript\" src=\"../include/Tree.js\"></script>
<script>

/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = \"black\";
	tree.bgColor = \"white\";
	tree.borderWidth = 0;


/*	Create Root node	*/
	var rootnode = new TreeNode(\"상품카테고리\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategory('상품카테고리','000000000000000',-1,0,'".$id."')\";
	rootnode.expanded = true;";



$db->query("SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

$total = $db->total;
for ($i = 0; $i < $db->total; $i++)
{

	$db->fetch($i);

	if ($db->dt["depth"] == 0){
		$cate = $cate. PrintNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 1){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 2){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 3){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 4){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}
}

$cate = $cate."	tree.addNode(rootnode);";
$cate = $cate."
</script>
<form>
<div id=TREE_BAR style=\"margin:5;\">
<script>
tree.draw();
tree.nodes[0].select();
</script>
</div>
</form>";

return $cate;
}

function PrintRelation($pid){
	global $db ,$admininfo;

	$sql = "select c.cid,c.cname,c.depth,r.basic, r.rid, r.regdate  from service_product_relation r, service_category_info c where pid = '$pid' and c.cid = r.cid ORDER BY r.regdate ASC ";


	$db->query($sql);

	$mString = "<table width=100% cellpadding=0 cellspacing=0 id=objCategory>
						";

	if ($db->total == 0){
		//$mString = $mString."<tr bgcolor=#ffffff height=45><td colspan=5 align=center>선택된 카테고리 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentServiceCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr>
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='category[]' id='_category' value='".$db->dt[cid]."' style='display:none'></td>
				<td class='table_td_white small' width='50'><input type='radio' name='basic' value='".$db->dt[cid]."' ".($db->dt[basic] == 1 ? "checked":"")."></td>
				<td class='table_td_white small ' width='*'>".($parent_cname != "" ? $parent_cname." > ":"").$db->dt[cname]."</td>
				<td class='table_td_white' width='100'><!--a href=\"JavaScript:void(0)\" onClick='category_del(this.parentNode.parentNode)'--><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle' onClick='category_del(this.parentNode.parentNode)' style='cursor:pointer;' /><!--/a--></td>
				</tr>";
		}
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString = $mString."</table>";

	return $mString;
}

function GetParentServiceCategory($subcid,$subdepth) {
	$mdb = new Database;

	$sql = "select c.cid,c.cname from service_category_info c where cid LIKE '".substr($subcid,0,$subdepth*3)."%' and depth = ".($subdepth-1)."  ";

	$mdb->query($sql);
	$mdb->fetch(0);

	$category_string = $mdb->dt[cname];

	/*if ($subdepth > 1){
		$sql = "select c.cid,c.cname from ".TBL_SHOP_CATEGORY_INFO." c where cid LIKE '".substr($subcid,0,($subdepth-1)*3)."%' and depth = ".($subdepth-2)."  ";
		$mdb->query($sql);
		$mdb->fetch(0);

		$category_string = $mdb->dt[cname]." > ".$category_string;
	}*/

	if ($subdepth > 1){// 3depth 이상일 경우 카테고리 값을 제대로 못 불러와서 위의 것을 수정함 kbk 12/02/27
		for($i=($subdepth-1);$i>=1;$i--) {
			$sql = "select c.cid,c.cname from service_category_info c where cid LIKE '".substr($subcid,0,($i)*3)."%' and depth = ".($i-1)."  ";
			$mdb->query($sql);
			$mdb->fetch(0);
			$category_string = $mdb->dt[cname]." > ".$category_string;
		}
	}

	return $category_string	;
}


function PrintAddImage($pid){
	global $db, $admin_config;

	$sql = "select id from ".TBL_SHOP_ADDIMAGE." a where pid = '$pid' ";
	$db->query($sql);

	$mString = "<table cellpadding=5 cellspacing=0 width='100%' bgcolor=silver>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25>
						<td class='s_td'>번호</td>
						<td  class='m_td' colspan=2>클립아트 ID</td>
						<td  class='m_td'>중간이미지</td>
						<td  class='m_td'>큰이미지</td>
						<td  class='e_td'>삭제</td>
					</tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=80><td colspan=6 align=center>입력된 추가이미지가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff><td  align=center  class=small>".($i+1)."</td><td  ><img src='".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[id]."_add.gif' align=absmiddle style='border:1px solid gray'></td><td  class=small><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[id]."_add.gif')\">c_".$db->dt[id]."_add.gif</a></td><td  class=small><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/m_".$db->dt[id]."_add.gif')\">m_".$db->dt[id]."_add.gif</a></td><td><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[id]."_add.gif')\">b_".$db->dt[id]."_add.gif</a></td><td align=center  class=small><a href=\"JavaScript:deleteAddimage('delete','".$db->dt[id]."','$pid')\"><img src='../image/btc_del.gif'></a></td></tr>";
			$mString = $mString."<tr height=1><td colspan=6 class='dot-x'></td></tr>";
		}
	}
	$mString = $mString."</table>";

	return $mString;
}

/*
function PrintOption($pid, $opn_ix =''){
	global $db;

	$sql = "select id, option_div,option_price, option_m_price, option_d_price, option_a_price, option_useprice, option_stock, option_safestock,option_code from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a where pid = '$pid' and opn_ix = '$opn_ix' order by id asc";
	$db->query($sql);

	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver>";
	$mString .=  "<tr height=1><td colspan=9 ></td></tr>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td rowspan=3 class=small>번호</td><td rowspan=3 class=small>옵션구분</td><td colspan=4 class=small>옵션가격</td><td colspan=2 class=small>옵션재고</td><td rowspan=3 class=small>기타(색상)</td><td rowspan=3 class=small>관리</td></tr>";
	$mString .=  "<tr height=1><td colspan=6 class='dot-x'></td></tr>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class=small>비회원가</td><td class=small>회원가</td><td class=small>딜러가</td><td class=small>대리점가</td><td class=small>재고</td><td class=small>안전재고</td></tr>";
	$mString .=  "<tr height=1><td colspan=9 ></td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=120><td colspan=10 align=center class=small>수정 /  추가 하시고자 하는 옵션이름을 선택해주세요</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff>
			<td  align=center>".($i+1)."</td>
			<td><a href=\"JavaScript:UpdateOption('".$db->dt[id]."','".str_replace("\"","&quot;",$db->dt[option_div])."','".$db->dt[option_price]."','".$db->dt[option_m_price]."','".$db->dt[option_d_price]."','".$db->dt[option_a_price]."','".$db->dt[option_stock]."','".$db->dt[option_safestock]."','".$db->dt[option_code]."')\" ><u>".$db->dt[option_div]."</u></a></td>
			<td>".$db->dt[option_price]."</td>
			<td>".$db->dt[option_m_price]."</td>
			<td>".$db->dt[option_d_price]."</td>
			<td>".$db->dt[option_a_price]."</td>
			<td>".$db->dt[option_stock]."</td>
			<td>".$db->dt[option_safestock]."</td>
			<td>".$db->dt[option_code]."</td>
			<td align=center>
				<a href=\"JavaScript:deleteOption('delete','".$db->dt[id]."','$pid')\"><img  src='../image/btc_del.gif' border=0></a>
			</td>
			</tr>
			<tr height=1><td colspan=9 class='dot-x'></td></tr>
			";
		}
	}
	$mString = $mString."</table>";

	return $mString;
}
*/

/*
function _PrintOption($pid){
	global $db;

	$sql = "select id, option_div,option_price, option_useprice, option_stock from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a where pid = '$pid' ";
	$db->query($sql);

	$mString = "<table cellpadding=2 cellspacing=1 width=100% bgcolor=silver>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td>번호</td><td>옵션이름</td><td>옵션구분</td><td>옵션가격</td><td>옵션재고</td><td>옵션표시</td><td>관리</td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=40><td colspan=7 align=center>입력된 옵션이 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff>
			<td  align=center>".($i+1)."</td><td>".$db->dt[option_name]."</td><td>".$db->dt[option_div]."</td><td>".$db->dt[option_price]."</td><td>".$db->dt[option_stock]."</td>
			<td>".PrintSelect($db->dt[option_name],$db->dt[option_div],$db->dt[option_price],$db->dt[option_useprice])."</td>
			<td align=center>
				<a href=\"JavaScript:UpdateOption('".$db->dt[id]."','".$db->dt[option_name]."','".$db->dt[option_div]."','".$db->dt[option_price]."','".$db->dt[option_stock]."')\">○</a>
				<a href=\"JavaScript:deleteOption('delete','".$db->dt[id]."','$pid')\">×</a>
			</td>
			</tr>";
		}
	}
	$mString = $mString."</table>";

	return $mString;
}
*/

function PrintSelect($op_name,$op_div,$op_price,$op_useprice)
{
	$aryOp_div = explode("|",$op_div);
	$aryOp_price = explode("|",$op_price);
	$size = count($aryOp_div);

	$SelectString = "<Select>";

	if ($size == 0){
		$SelectString = $SelectString."<option>옵션이 없습니다.</option>";
	}else{
		if($op_useprice ==1){
			for($i=0; $i < $size; $i++){
				$SelectString = $SelectString."<option value='".$aryOp_div[$i]."'>".$aryOp_div[$i]."</option>";
			}
		}else{
			for($i=0; $i < $size; $i++){
				$SelectString = $SelectString."<option value='".$aryOp_price[$i]."'>".$aryOp_div[$i]."</option>";
			}
		}
	}

	$SelectString = $SelectString."</Select>";

	return $SelectString;
}


function SellState($vstate){
	global $admininfo;

	if($admininfo[admin_level] == 9){
		$mstring = "
		<Select name=state style='height:23px;'>
			<option value=0 ".($vstate == 0 ? "selected":"").">일시품절</option>
			<option value=1 ".(($vstate == 1 || $vstate == "") ? "selected":"").">판매중</option>";
		if($admininfo[mall_use_multishop]){
		$mstring .= "<option value=6 ".($vstate == 6 ? "selected":"").">입점업체 등록신청</option>";
		}
		$mstring .= "</Select>";
	}else if($admininfo[admin_level] == 8){
		$mstring = "
		<Select name=state>
			<option value=0 ".($vstate == 0 ? "selected":"").">일시품절</option>";
		if ($vstate == 1 ){
		$mstring .= "<option value=1 ".($vstate == 1 ? "selected":"").">판매중</option>";
		}
		if($admininfo[mall_use_multishop]){
		$mstring .= "<option value=6 ".(($vstate == 6 || $vstate == "") ? "selected":"").">입점업체 등록신청</option>";
		}
		$mstring .= "</Select>";
	}
	return $mstring;
}


function displayProduct($disp){
if ($disp == 1 || $disp == ""){
	$Selectedstr00 = "";
	$Selectedstr01 = " selected";
}else{
	$Selectedstr00 = " selected";
	$Selectedstr01 = "";
}
return "

<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>노출함</label>
<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>노출안함</label>
<!--input type='radio' name='disp'  id='disp_0' value='2' ".ReturnStringAfterCompare($disp, "2", " checked")."><label for='disp_2'>포인트몰</label>
<input type='radio' name='disp'  id='disp_0' value='3' ".ReturnStringAfterCompare($disp, "3", " checked")."><label for='disp_3'>현금,포인트몰</label>
<input type='radio' name='disp'  id='disp_0' value='9' ".ReturnStringAfterCompare($disp, "9", " checked")."><label for='disp_9'>공동구매</label-->

<!--Select name=state>
	<option value=0 $Selectedstr00>일시품절</option>
	<option value=1 $Selectedstr01>판매중</option>
</Select-->";
}







function printBuyingCompany($bc_ix, $cid, $return_type ="")
{
//global $db;

	$mdb = new Database;

	if($cid){
		$mdb->query("SELECT * FROM shop_buying_company where disp=1 and cid = '$cid'");
	}else{
		$mdb->query("SELECT * FROM shop_buying_company where disp=1 ");
	}

	$bl = "<Select name='buying_company' style='height:23px;'>";
	if ($mdb->total == 0)	{
		$bl = $bl."<Option>등록된 사입업체가 없습니다.</Option>";
	}else{
		if($return_type == ""){
			$bl = $bl."<Option value=''>사입업체 선택</Option>";
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($bc_ix == $mdb->dt[bc_ix]){
					$strSelected = "Selected";
				}else{
					$strSelected = "";
				}

				$bl = $bl."<Option value='".$mdb->dt[bc_ix]."' $strSelected>".$mdb->dt[bc_name]."</Option>";

			}
		}else{
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($brand == $mdb->dt[bc_ix]){
					return $mdb->dt[bc_name];
				}
			}
		}
	}

	$bl = $bl."</Select>";

	return $bl;
}

/*
function MakerList($company, $cid, $return_type ="")
{
//global $db;

	$mdb = new Database;

	if($cid){
		$mdb->query("SELECT * FROM ".TBL_SHOP_COMPANY." where disp=1 and cid = '$cid' order by company_name asc");
	}else{
		$mdb->query("SELECT * FROM ".TBL_SHOP_COMPANY." where disp=1 order by company_name asc");
	}

	$bl = "<Select name='company' style='height:23px;'>";
	if ($mdb->total == 0)	{
		$bl = $bl."<Option>등록된 제조사가 없습니다.</Option>";
	}else{
		if($return_type == ""){
			$bl = $bl."<Option value=''>제조사 선택</Option>";
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($company == $mdb->dt[company_name]){
					$strSelected = "Selected";
				}else{
					$strSelected = "";
				}

				$bl = $bl."<Option value='".$mdb->dt[company_name]."' $strSelected>".$mdb->dt[company_name]."</Option>";

			}
		}else{
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($brand == $mdb->dt[c_ix]){
					return $mdb->dt[comapny_name];
				}
			}
		}
	}

	$bl = $bl."</Select>";

	return $bl;
}
*/

function getServiceCategoryMultipleSelect($category_text ="기본카테고리 선택", $object_name="cid",$id="cid", $onchange_handler="", $depth=0, $cid="")
{
	$mdb = new Database;
	$tb = "service_category_info";
	//echo "<script>alert('1')</script>";
	if($depth == 0 || $cid != ""){
		$sql = "SELECT * FROM ".$tb." where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
		//echo $sql;
		$mdb->query($sql);
	}




	if ($mdb->total){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' validation=true multiple style='1px solid silver;height:105px;width:100%;'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			if(substr($cid,0,($depth+1)*3) == substr($mdb->dt[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' validation=false multiple  style='border:1px solid silver;height:105px;width:100%;'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}

?>