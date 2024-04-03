<?

include("../class/layout.class");



//echo exif_imagetype("/home/simpleline/www/data/simpleline/images/product/basic_1705.gif");

$db = new Database;

$sql = "select * from shop_buyingservice_info order by regdate desc limit 1 ";

$db->query ($sql);

if($db->total){
	$db->fetch();

	$exchange_rate = $db->dt[exchange_rate];
	$bs_basic_air_shipping = $db->dt[bs_basic_air_shipping];
	$bs_add_air_shipping = $db->dt[bs_add_air_shipping];
	$bs_duty_rate = $db->dt[bs_duty];
	$bs_supertax_rate = $db->dt[bs_supertax_rate];
	$bs_clearance_fee = $db->dt[clearance_fee];
}

if($id){
	$sql = "select * from shop_product_buyingservice_priceinfo where pid = '$id' order by regdate desc limit 1 ";

	$db->query ($sql);

	if($db->total){
		$db->fetch();

		$orgin_price = $db->dt[orgin_price];
		$exchange_rate = $db->dt[exchange_rate];
		$air_wt = $db->dt[air_wt];
		$air_shipping = $db->dt[air_shipping];
		$duty = $db->dt[duty];
		$clearance_fee = $db->dt[clearance_fee];
		$clearance_type = $db->dt[clearance_type];
		$bs_fee_rate = $db->dt[bs_fee_rate];
		$bs_fee = $db->dt[bs_fee];
	}
}
if($id == ""){
	$sql = "select commission from ".TBL_COMMON_SELLER_DELIVERY."  where company_id = '".$admininfo[company_id]."'";
}else{
	$sql = "select c.commission from ".TBL_COMMON_SELLER_DELIVERY."  c , shop_product p where p.id = '".$id."' and p.admin = c.company_id";
}
$db->query($sql);
$db->fetch();
$company_commission = $db->dt[commission];
$commission = $db->dt[commission];

$db->query("select idx from shop_icon where disp = 1 order by idx");
if($db->total){
	$icon_list = $db->fetchall();
}

$Script = "
<link rel=\"stylesheet\" type=\"text/css\" href=\"/js/image/style.css\" />
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

<script  id='dynamic'></script>
<Script Language='JavaScript'>
var bs_basic_air_shipping = '$bs_basic_air_shipping';
var bs_add_air_shipping = '$bs_add_air_shipping';
var duty_rate = '$bs_duty_rate';
var bs_supertax = '$bs_supertax';
var bs_clearance_fee = '$bs_clearance_fee';


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

	document.forms['optionform'].option_kind.value = option_kind;
	document.frames['act'].location.href='option.act.php?act=view&pid='+pid+'&opn_ix='+obj.value;
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

/*function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	var depth = sel.depth;
	//if(depth == 2){
	//document.write('category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	//}
	//alert(target);
	dynamic.src = 'category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}*/
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
	// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
	document.getElementById('act').src = 'category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}

function init()
{
";
	if ($id != ""){
		$db->query("SELECT * FROM ".TBL_SHOP_PRODUCT." where id = $id");

		if($db->total != 0)
		{
		$db->fetch(0);

		$pcode = $db->dt[pcode];
		$act = "update";
		$company = $db->dt[company];
		$state = $db->dt[state];
		$brand = $db->dt[brand];
		$basicinfo = $db->dt[basicinfo];
		$shotinfo = $db->dt[shotinfo];
		$hardware = $db->dt[hardware];
		$software = $db->dt[software];
		$system = $db->dt["system"];
		$etc = $db->dt["etc"];
		$pname = $db->dt[pname];
		$stock = $db->dt[stock];
		$safestock = $db->dt[safestock];
		$search_keyword = $db->dt[search_keyword];
		$disp = $db->dt[disp];
		$surtax_yorn = $db->dt[surtax_yorn];
		$delivery_method = $db->dt[delivery_method];
		$product_type = $db->dt[product_type];
		$delivery_company = $db->dt[delivery_company];
		$reserve = $db->dt[reserve];
		$reserve_yn = $db->dt[reserve_yn];
		$FromYY = substr($db->dt[startdate],0,4);
		$FromMM = substr($db->dt[startdate],5,2);
		$FromDD = substr($db->dt[startdate],8,2);
		$FromHH = substr($db->dt[startdate],11,2);
		$FromII = substr($db->dt[startdate],14,2);
		$ToYY = substr($db->dt[plusdate],0,4);
		$ToMM = substr($db->dt[plusdate],5,2);
		$ToDD = substr($db->dt[plusdate],8,2);
		$ToHH = substr($db->dt[plusdate],11,2);
		$ToII = substr($db->dt[plusdate],14,2);
		$startprice = $db->dt[startprice];
		$plus_count = $db->dt[plus_count];

		$buyingservice_coprice = $db->dt[buyingservice_coprice];
		$coprice = $db->dt[coprice];
		$sellprice = $db->dt[sellprice];
		$listprice = $db->dt[listprice];
		$bimg_text = $db->dt[bimg];
		$admin = $db->dt[admin];

		$one_commission = $db->dt[one_commission];
		$goods_commission = $db->dt[commission];
		if($one_commission == "Y"){
			$commission = $db->dt[commission];
		}
		$free_delivery_yn = $db->dt[free_delivery_yn];
		$free_delivery_count = $db->dt[free_delivery_count];
		$bs_goods_url = $db->dt[bs_goods_url];
		$bs_site = $db->dt[bs_site];
		//echo $FromYY;
		if ($FromYY == "" || $FromYY == "0000"){

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
		if(($product_type == "0" || $product_type == "") && $admininfo[admin_level] == 9){
			$Script=$Script. "
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

			";
		}else if($admininfo[admin_level] == 9){
			$Script=$Script. "
			frm = document.product_input;
			document.getElementById('FromYY').disabled = false;
			document.getElementById('FromMM').disabled = false;
			document.getElementById('FromDD').disabled = false;
			document.getElementById('FromHH').disabled = false;
			document.getElementById('FromII').disabled = false;
			document.getElementById('ToYY').disabled = false;
			document.getElementById('ToMM').disabled = false;
			document.getElementById('ToDD').disabled = false;
			document.getElementById('ToHH').disabled = false;
			document.getElementById('ToII').disabled = false;
			document.getElementById('start_price').disabled = false;

			";
		}

			$Script=$Script. "

			";




		}
	}else{
		$disp = "1";
		$act = "insert";

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
		if($admininfo[admin_level] == 9){
		$Script=$Script. "
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
		";
		}
	}
$Script=$Script. "

	Content_Input();

	Init(document.product_input);

	onLoad('$sDate','$eDate');

}
function Content_Input(){
	document.product_input.content.value = document.product_input.basicinfo.value;
	//alert(document.product_input.content.value);
}

function onDropAction(mode, pid,rp_pid)
{
	//outTip(img3);
	//alert(1);
	parent.document.frames['act'].location.href='./relation.category.act.php?mode='+mode+'&pid='+pid+'&rp_pid='+rp_pid;

}
//var cate = new Array();";
if($admininfo[admin_level] == 9){
$Script=$Script. "
function init_date(FromDate,ToDate) {
	var frm = document.product_input;


	for(i=0; i<frm.FromYY.length; i++) {
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


}

function onLoad(FromDate, ToDate) {
	var frm = document.product_input;

	LoadValuesAuction(frm.FromYY, frm.FromMM, frm.FromDD,frm.FromHH,frm.FromII, FromDate);
	LoadValuesAuction(frm.ToYY, frm.ToMM, frm.ToDD,frm.ToHH,frm.ToII, ToDate);

	init_date(FromDate,ToDate);

}";
}
$Script=$Script. "
/*function categoryadd()
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
		alert(language_data['goods_input_quick.php']['A'][language]);
		//'카테고리를 선택해주세요'
		return;
	}
	var cate = document.all._category;

	//if(is_array([cate])){
		//alert(cate.length);
		for(i=1;i < cate.length;i++){
			//alert(ret +'=='+ cate[i].value);
			if(ret == cate[i].value){
				alert(language_data['goods_input_quick.php']['B'][language]);
				//'이미등록된 카테고리 입니다.'
				return;
			}
		}
	//}

	//cate.unshift(ret);
	var obj = document.getElementById('objCategory');
	oTr = obj.insertRow();
	oTr.id = 'num_tr';
	oTr.height = '30px';
	oTr.className = 'dot_xx';
	oTd = oTr.insertCell();
	oTd.className = '';
	oTd.innerHTML = \"<input type=text name=category[] id='_category' value='\" + ret + \"' style='display:none'>\";
	oTd = oTr.insertCell();
	oTd.className = '';
	if(oTr.rowIndex == 0){
		oTd.innerHTML = \"<input type=radio name=basic value='\"+ ret + \"' checked>\";
	}else{
		oTd.innerHTML = \"<input type=radio name=basic value='\"+ ret + \"'>\";
	}
	oTd = oTr.insertCell();
	oTd.id = \"currPosition\";
	oTd.className = '';
	oTd.innerHTML = str.join(\" > \");
	oTd = oTr.insertCell();
	oTd.className = '';
	oTd.innerHTML = \" <a href='javascript:void(0)' onClick='category_del(this.parentNode.parentNode)'><img src='../images/i_close.gif' border=0></a>\";

}*/
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
		alert(language_data['goods_input_quick.php']['C'][language]);
		//'카테고리를 선택해주세요'
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
				alert(language_data['goods_input_quick.php']['B'][language]);
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
	oTd.innerHTML = \" <a href='javascript:void(0)' onClick='category_del(this.parentNode.parentNode)'><img src='../images/i_close.gif' border=0></a>\";


}

function category_del(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objCategory');
	obj.deleteRow(idx);
	if(document.product_input.basic.length == null){
		document.product_input.basic.checked = true;
	}else{
		for(var i=0;i<document.product_input.basic.length;i++){
			if(document.product_input.basic[i].checked){
				return true;
				break;
			}else{
				document.product_input.basic[0].checked = true;
			}
		}
	}
	//cate.splice(idx,1);
}
</Script>";

$Contents = "
	<table cellpadding=0 width=100%>
		<tr>
		    <td align='left' colspan=4 style='padding-bottom:5px;'> ".GetTitleNavigation("상품등록관리", "상품관리 > 상품등록관리")."</td>
		</tr>
		<tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    	<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='goods_input.php'\">일반 상품 등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' class='on'>
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
	</tr>
	</table>";

$Contents = $Contents."
			<!--form name='product_input' action='product_input.act.php' method='post' enctype='multipart/form-data'-->
			<form name=product_input action='goods_input_quick.act.php' method='post' enctype='multipart/form-data' onsubmit='return SubmitX(this);' style='display:inline;'>
			<input type='hidden' name='key1' value='val1'>
			<input type='hidden' name=act value='insert'>
			<input type='hidden' name=admin value='".$admin."'>
			<input type='hidden' name=id value='".$id."'>
			<input type='hidden' name=bpid value='".$id."'>
			<input type='hidden' name=mode value='".$mode."'>
			<table width=100%>
			<tr height=40 align=left>
				<td width=500><a href=\"JavaScript:PoPWindow('/shop/goods_view.php?id=".$id."',980,800,'comparewindow');\">보기</a></td>";
if ($id == "" || $mode == "copy"){
$Contents .= "<td align=right>";
$Contents .= "<img src='../image/b_save.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'insert');\">";
$Contents .= "</td>";
}else{
$Contents .= "<td align=right><img src='../image/b_save.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'update')\"> <img src='../image/b_del.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'delete')\"></td>";
}
$Contents = $Contents."
			</tr>
			</table>";
if($admininfo[admin_level] == 9){
$Contents = $Contents."			<table cellspacing=0 cellpadding=0 border=0 width='100%'>
			<tr bgcolor='#cccccc' height='36'>
					<td bgcolor=\"#efefef\" width=120 style='padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle> <b>상품구분</b></td>
					<td colspan=3 bgcolor=\"#efefef\" align='left'>
						<table border=0 cellpadding=0 cellspacing=0 width=550>
							<tr>
								<td>
									<input type=radio name=product_type id='product_type_0' value='0' ".($product_type == "0" || $product_type == "" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('GoodsInfo');\"> <label for='product_type_0' >일반상품 </label>
									<input type=radio name=product_type id='product_type_1'  value='1' ".($product_type == "1" ? "checked":"")." onclick=\"dateSelect('1');ShowGoodsTypeInfo('buyingServiceInfo');\"> <label for='product_type_1' >해외구매대행  </label>
									<input type=radio name=product_type id='product_type_2'  value='2' ".($product_type == "2" ? "checked":"")." onclick=\"dateSelect('2');ShowGoodsTypeInfo('AuctionInfo');\"> <label for='product_type_2' >최저가경매</label>
								</td>

							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height='3'></td>
				</tr>
			</table><br>";


$goodsHelp_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >자사가 직접 소싱 하는 상품을 등록 관리 합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >경매 상품과 , 해외구매 대행 상품은 별도로 등록 되어야 합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 상품 타입에 따라 쇼핑몰 에서 구분되어 상품이 표시됩니다.</td></tr>

</table>
";

$GoodsHelp = HelpBox("일반 상품관리", $goodsHelp_text);

$buyingServiceHelp_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >구매대행 상품 등록하기 버튼을 클릭하셔서 구매대행 상품정보를 자동으로 가져 오실수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 구매대행 상품은 자동으로 원천사이트의 정보변경을 체크하여 관리 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >변경된 정보는 자동으 시스템에 반영됩니다.</td></tr>

</table>
";

$buyingServiceHelp = HelpBox("구매대행 상품관리", $buyingServiceHelp_text);

$Contents .= "
				<div id='GoodsInfo' ".($product_type == "0" || $product_type == "" ? "style='display:block;padding-bottom:20px;'":"style='display:none;'").">
				$GoodsHelp
				</div>
				<div id='buyingServiceInfo' ".($product_type == "1" ? "style='display:block;'":"style='display:none;'").">
				<table width=100%>
				<tr>
					<td>$buyingServiceHelp</td>
					<td width=200 align=center><a href=\"javascript:PoPWindow('buyingService.php?mmode=pop',660,700,'buyingService')\"'><img src='../images/btn_buyingServiceReg.gif'></a></td>
				</tr>
				</table><br>
				</div>
				<div id='AuctionInfo' ".($product_type == "2" ? "style='display:block;'":"style='display:none;'").">
				<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:5px;'><img src='../images/dot_org.gif' align=absmiddle><b> 최저가경매 추가입력 </b></td><td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>
				<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver>
					<tr bgcolor='#ffffff' height=25>
						<td width=120 bgcolor=\"#efefef\" align=left class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle> 경매시간 </td>
						<td style='padding-left:10px' id='dateselect'><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY id=FromYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM id=FromMM></SELECT> 월 <SELECT name=FromDD id=FromDD></SELECT> 일 <SELECT name=FromHH id=FromHH></SELECT> 시 <SELECT name=FromII id=FromII></SELECT> 분~ <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY id=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM id=ToMM></SELECT> 월 <SELECT name=ToDD id=ToDD></SELECT> 일 <SELECT name=ToHH id=ToHH></SELECT> 시 <SELECT name=ToII id=ToII></SELECT> 분</td>
					</tr>
					<tr bgcolor='#ffffff' height=25>
						<td bgcolor=\"#efefef\" align=left class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle> 경매시작가 </td>
						<td style='padding-left:10px'><input type='text' id='start_price' name='startprice' value='1000' size=30></td>
					</tr>
					<tr bgcolor='#ffffff' height=25>
						<td bgcolor=\"#efefef\" align=left class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle> 시간연장 횟수 </td>
						<td style='padding-left:10px'><input type='text' name='plus_count' size=10 value='".$plus_count."'>회 &nbsp;&nbsp;&nbsp;&nbsp;* 0을 입력하면 사용안함이 됩니다.</td>
					</tr>
				</table><br></div>
				";
}

		$Contents .= "	<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 카테고리등록 </b><span class=small>상단에 카테고리를 선택하신후 카테고리 추가버튼을 클릭하세요.(다중 카테고리 등록지원)</span></td><td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>")."</td></tr></table>";



			$Contents .= "<table cellpadding=5 cellspacing=1 bgcolor=silver border=0 width='100%'>
				<col width=15%>
				<col width=90%>
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"  nowrap><img src='../image/ico_dot.gif' align=absmiddle> <b>카테고리 *</b> </td>
					<td  >
					<input type='hidden' name=selected_cid value='".$cid."'>
					<input type='hidden' name=selected_depth value=''>
					<input type='hidden' id='_category' value=''>
					<input type='hidden' id='_category' value=''>
					<input type='hidden' id='basic' value=''>
					<!--input type='hidden' name=cid_1 value=''>
					<input type='hidden' name=cid_2 value=''>
					<input type='hidden' name=cid_3 value=''-->
						<table width=100% border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:2px;'>".getCategoryMultipleSelect("--1차분류--", "cid0", "cid","onChange=\"loadCategory(this,'cid1',2)\" title='1차분류' ", 0, $cid)." </td>
								<td style='padding-right:2px;'>".getCategoryMultipleSelect("--2차분류--", "cid1",  "cid","onChange=\"loadCategory(this,'cid2',2)\" title='2차분류'", 1, $cid)." </td>
								<td style='padding-right:2px;'>".getCategoryMultipleSelect("--3차분류--", "cid2", "cid", "onChange=\"loadCategory(this,'cid3',2)\" title='3차분류'", 2, $cid)." </td>
								<td>".getCategoryMultipleSelect("--4차분류--", "cid3", "cid", "onChange=\"loadCategory(this,'cid_1',2)\" title='4차분류'", 3, $cid)."</td>
								<td style='padding-left:10px'><img src='../image/category_add.gif' align=absmiddle border=0 onclick=\"categoryadd()\" style='cursor:hand;'></td>
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
				<tr><td class='small'> * 첫번째 선택된 카테고리가 기본카테고리로 지정되며 라디오 버튼 클릭으로 기본카테고리를 변경 하실 수 있습니다</td></tr>
			</table><br>
			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 기본정보 : </b><span class=small>굵은 글씨로 되어 있는 항목이 필수 정보입니다.</span></td><td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>")."</td></tr></table>
			<table cellpadding=5 cellspacing=1 bgcolor=silver border=0 width='100%'>
				<col width=15%>
				<col width=35%>
				<col width=15%>
				<col width=35%>
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\" width=13% nowrap><img src='../image/ico_dot.gif' align=absmiddle> <b>제품명 *</b> </td>
					<td width='30%' colspan=3><input type=text class='textbox' name=pname size=28 style='width:100%' value='$pname'></td>
				</tr>
				<!--tr height=1><td colspan=4 class='dot-x'></td></tr-->
				";
				if($admininfo[admin_level] == 9){
				$Contents .= "<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 진열 </td>
					<td colspan=3>".displayProduct($disp)."</td>

				</tr>";
				}
				$Contents .= "<!--tr height=1><td colspan=4 class='dot-x'></td></tr-->
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 제품코드 *</td>
					<td>
					<input type=text class='textbox' name=pcode size=28 style='width:100%' value='$pcode'><br>
					<div style='margin:2 2' class=small>* 오프라인 관리 코드를 입력해주세요</div>
					</td>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 배송업체 </td>
					<td><input type='radio' name='delivery_company' value='WE' ".($delivery_company == "WE" || $delivery_company == "" ? "checked":"")."> ".getAdminName()."배송 <input type='radio' name='delivery_company' value='MI' ".($delivery_company == "MI" ? "checked":"")."> 업체배송

					</td>


				</tr>
				<tr bgcolor='#ffffff'>
				<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 아이콘노출 </td>
					<td colspan=3>
					<table>
						<tr>
							";
							if(count($icon_list) >1 ){
								for($i=0;$i<count($icon_list);$i++){
									$Contents .=	"<td><input type=\"checkbox\" name='icon_check[]' class=nonborder id=icon_check value=".$icon_list[$i][idx]." ".($icons_checked[$icon_list[$i][idx]] == "1" ? "checked":"")."></td><td><img src='".$admin_config[mall_data_root]."/images/icon/".$icon_list[$i][idx].".gif' align='absmiddle' style='vertical-align:middle'></td>";
								}
							}

							$Contents .=	"<td><a href=\"javascript:PoPWindow('../design/product_icon.php?mmode=pop',960,600,'brand')\"'><img src='../image/btn_pop_icon.gif' align=absmiddle border=0></a></td>
						</tr>
					</table>
					</td>
				</tr>
				<!--tr height=1><td colspan=4 class='dot-x'></td></tr-->
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 브랜드 </td>
					<td >
					<table cellpadding=3 cellspacing=0>
						<tr>
							<td><div id='brand_select_area'>".BrandListSelect($brand, $cid)."</div></td><td><a href=\"javascript:PoPWindow('brand.php?mmode=pop',960,600,'brand')\"'><img src='../image/btn_pop_manage.gif' align=absmiddle border=0></a></td>
						</tr>
					</table>
					</td>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 제조사</td>
					<td><!--input type=text class='textbox' name=company size=28 style='width:100%'-->
					<table cellpadding=3 cellspacing=0>
						<tr>
							<td><div id='company_select_area'>".MakerList($company,$cid)."</div></td><td><a href=\"javascript:PoPWindow('company.php?mmode=pop',960,600,'company')\"'><img src='../image/btn_pop_manage.gif' align=absmiddle border=0></a></td>
						</tr>
					</table>
					</td>
				</tr>
				<!--tr height=1><td colspan=4 class='dot-x'></td></tr-->

				<tr bgcolor='#ffffff'>

					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 판매상태 </td>
					<td >".SellState($state)."</td>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 재고관리</td>
					<td nowrap>
					<table width='100%' cellpadding=0 cellspacing=0>
					<tr>
						<td align=left>

						</td>
						<td align=left>
						재고 <input type=\"text\"  size=5 name=stock value='$stock'> 안전재고 <input type=\"text\"  size=5 name=safestock value='$safestock'>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<!--tr height=1><td colspan=4 class='dot-x'></td></tr-->

				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 면세제품 </td>
					<td ><input type=radio  name=surtax_yorn value='Y' ".($surtax_yorn == "Y" ? "checked":"")."> 면세 <input type=radio  name=surtax_yorn value='N' ".($surtax_yorn == "N" ? "checked":"")."> 면세아님</td>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 출고보관장소 </td>
					<td >".makeSelectBox('inventory_info','','inventory_code','등록된창고 가 없습니다.')."</td>
				</tr>";
				if($admininfo[admin_level] == 9){
			$Contents .=	"<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 입점업체등록</td>
					<td colspan=3>
					".companyAuthList($company_id)."
					</td>
				</tr>";
				}
				$Contents .=	"<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 검색키워드 </td>
					<td colspan=3 style='line-height:150%'>
					<input type=text class='textbox' name='search_keyword' size=28 style='width:100%' value='$search_keyword'><br>
					※<span class=small > 검색어를 등록하시면 검색이 검색어가 같이 포함되어 노출되게 됩니다</span></td>
				</tr>
				<!--tr height=1><td colspan=4 class='dot-x'></td></tr-->
			</table><br>";
/*
$Contents .= ."
			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 부가정보</b></div>")."</td></tr></table>
			<table cellpadding=5 cellspacing=1 bgcolor=#ffffff width='100%'>
				<tr bgcolor='#ffffff'>
					<!--td bgcolor=\"#efefef\" width=15% nowrap><img src='../image/ico_dot.gif' align=absmiddle> 규격 *</td>
					<td><input type=text class='textbox' name=standard size=28 style='width:100%' value='$standard'></td-->
					<td bgcolor=\"#efefef\" width=15% nowrap><img src='../image/ico_dot.gif' align=absmiddle> 창고위치 *</td>
					<td width=35%><input type=text class='textbox' name=warehouse  style='width:100%' value='$warehouse'></td>
					<td bgcolor=\"#efefef\" width=15% nowrap><img src='../image/ico_dot.gif' align=absmiddle> 단위 *</td>
					<td width=35%><input type=text class='textbox' name=unit style='width:100%' value='$unit'></td>
				</tr>
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 검색키워드 </td>
					<td colspan=3 style='line-height:150%'>
					<input type=text class='textbox' name='search_keyword' size=28 style='width:100%' value='$search_keyword'><br>
					※<span class=small > 검색어를 등록하시면 검색이 검색어가 같이 포함되어 노출되게 됩니다</span></td>
				</tr>
				<tr height=1><td colspan=4 class='dot-x'></td></tr>
			</table><br-->";
*/


$Contents .= "
			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 가격정보</b> <a onclick=\"alert(language_data['goods_input_quick.php']['D'][language]);\"><img src='../image/movie_manual.gif' align=absmiddle></a></div>")."</td></tr></table>
			";
if ($admininfo[admin_level] != 9){
	if($act == "update"){
		$dispString = "style='display:none;'";
		$readonlyString = " readonly";
		$colorString = ";background-color:#efefef;color:gray";
		$message = "onclick=\"alert(language_data['goods_input_quick.php']['E'][language]);\"";
		//'가격 정보를 수정하시고자 할대는 MD와 상의해 주세요'
	}else{
		//$dispString = "style='display:none;'";
		//$readonlyString = " readonly";
		//$colorString = ";background-color:#efefef;color:gray";
		//$message = "onclick=\"alert(language_data['goods_input_quick.php']['F'][language]);\"";
		//'입점업체는 공급가격만 입력하실수 있습니다'
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
							<div class='doong' id='price_info_box' style='display:block;vertical-align:top;height:250px;' >
								<table cellpadding=3 cellspacing=0 width=100%>
									<tr id=\"buyingServiceClearanceType\" style='".($product_type == "1" ? "display:block;":"display:none;")."'>
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
																<td style='padding:1 3 1 1' nowrap>
																<input type='radio' name='clearance_type' id='clearance_type_1' onclick='caculateBuyingServicePrice(this);' value='1' ".($clearance_type == "1" ? "checked":"")."><label for='clearance_type_1'>목록통관</label> <input type='radio' name='clearance_type' id='clearance_type_0' onclick='caculateBuyingServicePrice(this);' value='0' ".($clearance_type == "0" ? "checked":"")."> <label for='clearance_type_0'>일반통관</label>
																</td>
																<td bgcolor='#efefef'>상품 URL</td>
																<td ><input  type=hidden name='bs_site' value='".$bs_site."'><input  type=text name='bs_goods_url' value='".$bs_goods_url."' style='width:430px;'>  </td>
															</tr>
														</table>
													</td>
													<td align=center>".($product_type == "1" ? " <a href=\"javascript:PoPWindow('buyingService_pricehistory.php?mmode=pop&id=$id',960,600,'brand')\"'><img src='../image/btn_bs_priceinfo.gif' align=absmiddle></a>":"<a href=\"javascript:alert(language_data['goods_input_quick.php']['G'][language]);\"'><img src='../image/btn_bs_priceinfo.gif' align=absmiddle></a>")."</td>
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
												<!--td >딜러  ".($admininfo[admin_level] == 9 ? "<span class=small onclick=\"copyPrice(document.product_input, document.product_input.prd_dealer_price.value, 4);\" style='cursor:hand;color:red'>복사→</span>":"")."</td-->
												<td class='small'>구매대행 수수료율(%)/수수료  </td>


											</tr>
											<tr bgcolor='#fbfbfb' height=35 align=center>
												".($admininfo[admin_level] == 9 ? "<td ><input  type=hidden name='b_orgin_price' value='".$orgin_price."'><input  type=text class='textbox' size=10  name='orgin_price' value='".$orgin_price."' style='text-align:right;background-color:#efefef' readonly></td>":"")."
												<td>
													<input type=hidden name=b_exchange_rate value='$exchange_rate'>
													<input type=text class='textbox' name=exchange_rate size=10 value='$exchange_rate' onkeydown='onlyEditableNumber(this)' maxlength=16 onkeyup='this.value=FormatNumber3(this.value);' style='ime-mode:disabled;TEXT-ALIGN:right;padding-right:3px;background-color:#efefef;".$colorString." ' readonly> 원
												</td>
												<td >
													<input  type=hidden name='b_air_wt' value='$air_wt' >
													<input  type=text class='textbox' size=4  name='air_wt' value='$air_wt' style='text-align:right;' onkeydown='onlyEditableNumber(this);' maxlength=16 onkeyup='caculateBuyingServicePrice(this);this.value=FormatNumber3(this.value);'> lbs /
													<input  type=hidden name='b_air_shipping' value='$air_shipping' >
													<input  type=text class='textbox' size=4  name='air_shipping' value='$air_shipping'  style='text-align:right;background-color:#efefef' readonly> $
													</td>
												<td>
													<input type=hidden name=b_duty value='$duty'>
													<input type=text class='textbox' name=duty value='$duty' size=10 onkeydown='onlyEditableNumber(this)' style='ime-mode:disabled;TEXT-ALIGN:right;background-color:#efefef;".$colorString."'  maxlength=16 onkeyup='this.value=FormatNumber3(this.value);'   readonly> 원
												</td>
												<td >
													<input type=text class='textbox' name=clearance_fee size=10 style='text-align:right' value='$clearance_fee' onkeydown='onlyEditableNumber(this)' onkeyup='this.value=FormatNumber3(this.value);calcurate_maginrate(document.product_input)' style='ime-mode:disabled;TEXT-ALIGN:right;background-color:#efefef;".$colorString."' readonly >
													<input type=hidden name=b_clearance_fee value='$clearance_fee' > 원
												</td>
												<td>
													<input type=hidden name=b_bs_fee_rate value='$bs_fee_rate'>
													<input type=text class='textbox' name=bs_fee_rate size=4 value='$bs_fee_rate' onkeydown='onlyEditableNumber(this)' maxlength=16 onkeyup='caculateBuyingServicePrice(this);this.value=FormatNumber3(this.value);' style='ime-mode:disabled;TEXT-ALIGN:right;".$colorString."' $message  $readonlyString>
													<input type=hidden name=b_bs_fee value='$bs_fee'>
													<input type=text class='textbox' name=bs_fee size=13 value='$bs_fee' onkeydown='onlyEditableNumber(this)' maxlength=16 onkeyup='caculateBuyingServicePrice(this);this.value=FormatNumber3(this.value);' style='ime-mode:disabled;TEXT-ALIGN:right".$colorString."' $message  $readonlyString> 원
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
											<col width=25%>
											<col width=25%>
											<tr bgcolor='#efefef' height=35 align=center>
												<!--".($admininfo[admin_level] == 9 ? "<td>구매대행원가</td>":"")."-->
												<td >".($admininfo[admin_level] == 9 ? "구매단가(구매대행원가) *":"공급가격 *")." </td>
												<td >판매가(할인가) ".($admininfo[admin_level] == 9 ? "<span class=small onclick=\"copyPrice(document.product_input, document.product_input.sellprice.value, 1);calcurate_maginrate(document.product_input)\" style='cursor:hand;color:red'>복사→</span>":"")." </td>
												<td >정가 </td>


												<!--td >딜러  ".($admininfo[admin_level] == 9 ? "<span class=small onclick=\"copyPrice(document.product_input, document.product_input.prd_dealer_price.value, 4);\" style='cursor:hand;color:red'>복사→</span>":"")."</td>
												<td >대리점  </td-->
												<td >마진(%) </td>

											</tr>
											<tr bgcolor='#fbfbfb' height=35 align=center>
												<!--".($admininfo[admin_level] == 9 ? "<td ><input  type=text class='textbox' size=13  name='buyingservice_coprice' value='".$buyingservice_coprice."' style='text-align:right;background-color:#efefef' readonly></td>":"")."-->
												<td ><input type=text class='textbox' name=coprice size=13 style='text-align:right' value='$coprice' onkeydown='onlyEditableNumber(this)' onkeyup='this.value=FormatNumber3(this.value);calcurate_maginrate(document.product_input)' style='ime-mode:disabled;TEXT-ALIGN:right".$colorString."'  $message  $readonlyString ><input type=hidden name=bcoprice value='$coprice' > 원</td>
												<td> <input type=hidden name=bsellprice value='$sellprice'><input type=text class='textbox' name=sellprice size=13 value='$sellprice' onkeydown='onlyEditableNumber(this)' maxlength=16 onkeyup='this.value=FormatNumber3(this.value);calcurate_maginrate(document.product_input)' style='ime-mode:disabled;TEXT-ALIGN:right;padding-right:3px;".$colorString." ' $message  $readonlyString> 원</td>
												<td><input type=hidden name=blistprice value='$listprice'><input type=text class='textbox' name=listprice value='$listprice' size=13 onkeydown='onlyEditableNumber(this)' style='ime-mode:disabled;TEXT-ALIGN:right".$colorString."'  maxlength=16 onkeyup='this.value=FormatNumber3(this.value);'   $message  $readonlyString> 원</td>
												<!--td> <input type=text class='textbox' name=prd_dealer_price size=13 value='$prd_dealer_price' onkeydown='onlyEditableNumber(this)' maxlength=16 onkeyup='this.value=FormatNumber3(this.value);' style='Time-mode:disabled;EXT-ALIGN:right".$colorString."' $message  $readonlyString> 원</td>
												<td><input type=text class='textbox' name=prd_agent_price size=13 value='$prd_agent_price' onkeydown='onlyEditableNumber(this)' maxlength=16 onkeyup='this.value=FormatNumber3(this.value);' style='ime-mode:disabled;TEXT-ALIGN:right".$colorString."' $message  $readonlyString> 원</td-->
												<td > <input  type=text class='textbox' size=13  name='basic_margin' style='text-align:right;background-color:#efefef' readonly></td>

											</tr>
											</table>
										</td>
									</tr>";


								$Contents .="<tr ".($admininfo[admin_level] != 9 ? "style='display:none'":"").">
										<td>
											<table width=100% cellpadding=0 cellspacing=0>
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
																<td style='padding:1 3 1 1' nowrap>
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
																<td style='padding:1 3 1 1' nowrap>
																<table cellpadding=3 cellspacing=0>
																<tr>
																	<td >
																	<input type=text class='textbox' name=reserve size=13 style='text-align:right' onkeypress='onlyEditableNumber(this)'  value='$reserve'>
																	<input type=hidden name=breserve size=15 style='text-align:right' value='$reserve' readonly>

																	</td>
																	<td align=center>

																		<select name=rate1 style='font-size:12px;width:50' onchange=\"if(this.form.sellprice.value == ''){alert(language_data['goods_input_quick.php']['H'][language]);}else{this.form.reserve.value=Round2(filterNum(this.form.sellprice.value) * this.value/100,1,1);}\">
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
											</table>
										</td>
									</tr>";

									$Contents .="<tr>
										<td>";

$help_text2 = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><input size=8 type=text class='textbox' style='width:100;background-color:#efefef;height:15px;' value='' readonly> 색깔로 된 입력상자는 자동계산되서 입력되어 집니다. <!--<input size=8 type=text class='textbox' style='width:100;height:15px;' value='' readonly> 색깔의 입력상자에 정보를 입력하신후 --></td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >판매가(할인가)는 실제 노출되는 상품의 금액이며, 정가의 경우 상품의 정상가격을 나타내는 금액입니다. 구매단가는 상품의 공급가 입니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >가격정보가 동일할 경우는 <span class=small  style='cursor:hand;color:red'>복사→</span> 클릭해주세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >적립금을 백분율로 계산할때는 <b> 판매가(할인가)</b>를 기준으로 계산됩니다</td></tr>
</table>
";

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


$Contents .= "<div id='price_info' style='position:relative;'>".HelpBox("가격정보입력", $help_text2)."</div>";
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
$Contents .= "<table width='100%' cellpadding=0 cellspacing=0 ><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 구매수량당 무료배송설정 </b> <input type=checkbox name='delivery_setting_display_yn' id='delivery_setting_display_yn' onclick=\"(this.checked ? $('delivery_setting_zone').style.display='inline':$('delivery_setting_zone').style.display='none')\"><label for='delivery_setting_display_yn'>표시</label> </td><td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>")."</td></tr></table>
				<div style='display:none;' id='delivery_setting_zone'>
				<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver>
										<tr bgcolor='#ffffff' height=25>
											<td colapne=3 width=120 bgcolor=\"#efefef\" align=left class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle> 무료배송 사용 </td>
											<td class=small nowrap><input style='width:30px' type='radio' name='free_delivery_yn' value='N' ".($free_delivery_yn == 'N' || $free_delivery_yn == "" ? "checked":"").">사용안함<input type='radio' name='free_delivery_yn' value='Y' ".($free_delivery_yn == 'Y' ? "checked":"").">사용</td>
										</tr>

										<tr  bgcolor='#ffffff' height=25>
											<td  bgcolor=\"#efefef\" align=left class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle> 구매수량  </td>
											<td id='DP' style='display:block' class=small nowrap> <input style='width:40px' type='text' class=textbox size='30' name='free_delivery_count' value='".$free_delivery_count."' company_commission='".$company_commission."' style='ime-mode:disabled;TEXT-ALIGN:right'> </td>

										</tr>
				</table><br>
				</div>";
if($admininfo[mall_use_multishop]){
	if($admininfo[admin_level] ==9 || $admininfo[admin_level] == 8 ){
	$Contents .= "<table width='100%' cellpadding=0 cellspacing=0 ".($admininfo[admin_level] == 8 ? "style='display:none;'":"")."><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 수수료정보 </b> <input type=checkbox name='fee_setting_display_yn' id='fee_setting_display_yn' onclick=\"(this.checked ? $('fee_setting_zone').style.display='inline':$('fee_setting_zone').style.display='none')\"><label for='fee_setting_display_yn'>표시</label> </td><td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>")."</td></tr></table>
				<div style='display:none;' id='fee_setting_zone'>
				<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver ".($admininfo[admin_level] == 8 ? "style='display:none;'":"").">
										<tr bgcolor='#ffffff' height=25>
											<td colapne=3 width=120 bgcolor=\"#efefef\" align=left class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle> 개별수수료 사용 </td>
											<td class=small nowrap><input style='width:30px' type='radio' name='one_commission' value='N' ".($one_commission == 'N' || $one_commission == "" ? "checked":"")." onclick=\"commissionChange(this.form)\">사용안함<input type='radio' name='one_commission' value='Y' ".($one_commission == 'Y' ? "checked":"")." onclick=\"commissionChange(this.form)\">사용</td>
										</tr>

										<tr  bgcolor='#ffffff' height=25>
											<td  bgcolor=\"#efefef\" align=left class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle> 수수료  </td>
											<td id='DP' style='display:block' class=small nowrap> <input style='width:40px' type='text' class=textbox size='30' name='commission' value='".$commission."' goods_commission='".$goods_commission."' company_commission='".$company_commission."' onkeydown='onlyEditableNumber(this)'  style='ime-mode:disabled;TEXT-ALIGN:right' onkeyup=\"onlyEditableNumber(this);if(this.value.length > 0){this.goods_commission=this.value;commissionChange(this.form)}\">  % 단위로 입력하시기 바랍니다. * 개별수수료 사용 선택시에만 입력하실 수 있습니다.</td>

										</tr>
				</table><br>
				</div>";
	}
}

$Contents .="<table width='100%' cellpadding=0 cellspacing=0>
							<tr height=30>
							<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 가격+재고 관리 옵션정보 </b> <input type=checkbox name='price_option_display_yn' id='price_option_display_yn' onclick=\"(this.checked ? $('price_option_zone').style.display='inline':$('price_option_zone').style.display='none')\"><label for='price_option_display_yn'>표시</label> <!--span class=small > <img src='/admin/webedit/image/wtool6_1.gif' align=absmiddle> 이미지를 클릭해서 상품상세 이미지를 등록해주세요</span--></div>")."</td>
							</tr>
							</table>";


$sql = "select * from shop_product_options where pid = '".$id."' and pid != '' and option_kind = 'b' ";

$db->query($sql);
$db->fetch();
$options_price_stock = $db->dt;

$Contents .= "<div style='display:none;' id='price_option_zone'>
							<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver id='options_basic_input' opt_idx=0 style='margin-bottom:10'>
								<col width='4%'>
								<col width='26%'>
								<col width='14%'>
								<col width='14%'>
								<col width='14%'>
								<col width='14%'>
								<col width='14%'>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class=small nowrap>
									사용
									</td>
									<td bgcolor=\"#efefef\" class=small nowrap>
									<img src='../image/ico_dot.gif' align=absmiddle> 옵션명
									</td>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> 옵션구분 *</td>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> 옵션가격 *</td>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> 옵션별재고 *</td>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> 안전재고 </td>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> 기타 </td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td valign=top style='padding-top:10px;'>
									<input type=hidden name='options_price_stock[option_kind]' value='b'><input type=hidden name='options_price_stock[option_type]' value='9'>
									<input type=checkbox name='options_price_stock[option_use]' value='1' ".($options_price_stock[option_use] == 1 ? "checked":"").">
									</td>
									<td valign=top style='padding-top:10px;'>
									<input type=hidden name='options_price_stock[opn_ix]' id='option_opn_ix' value='".$options_price_stock[opn_ix]."'>
									<input type=text class='textbox' name='options_price_stock[option_name]' id='options_price_stock_option_name' size=28 style='width:120;vertical-align:middle' value='".$options_price_stock[option_name]."'>

									<a onclick=\"copyOptions('options_basic_item_input_0');showMessage('options_basic_item_input_status_area','가격+재고 옵션 구분정보가 추가 되었습니다.');\" ><img src='../image/btn_option_detail_add.gif' border=0 align=absmiddle style='margin:0 0 3 0;'></a>
									</td>
									<td colspan=5><input type=hidden id='options_price_stock_option_div' value=''>";

$sql = "select * from shop_product_options_detail where pid = '".$id."' and pid != ''  and opn_ix = '".$options_price_stock[opn_ix]."' order by id asc ";
//echo $sql;
$db->query($sql);

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);

$Contents .= "
										<table width=100% id='options_basic_item_input_0' opt_idx=0 cellspacing=4 cellpadding=0 >
											<col width='20%'>
											<col width='20%'>
											<col width='20%'>
											<col width='20%'>
											<col width='20%'>
											<tr>
												<td>
													<input type=hidden name='options_price_stock[opd_ix][]' value='".$options_price_stock[id]."'>
													<input type=text class='textbox' name='options_price_stock[option_div][]' id='options_price_stock_option_div' size=28 style='width:100%;vertical-align:middle' value='".$db->dt[option_div]."'>
												</td>
												<td><input type=text class='textbox' name='options_price_stock[price][]'  style='width:100%' value='".$db->dt[option_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' ></td>
												<td><input type=text class='textbox' name='options_price_stock[stock][]' style='width:100%' value='".$db->dt[option_stock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
												<td><input type=text class='textbox' name='options_price_stock[safestock][]' size=28 style='width:100%' value='".$db->dt[option_safestock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>";

$Contents .= "					<td><input type=text class='textbox' name='options_price_stock[etc1][]' size=28 style='width:80%' value='".$db->dt[option_etc1]."'> <img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' alt='더블클릭시 해당 라인이 삭제 됩니다.' ondblclick=\"if(document.all.options_basic_item_input_0.length > 1){this.parentNode.parentNode.parentNode.parentNode.removeNode(true);showMessage('options_basic_item_input_status_area','가격+재고 옵션 구분정보가 삭제 되었습니다.');}else{clearInputBox('options_basic_input');$('options_price_stock_option_name').value='';showMessage('options_basic_item_input_status_area','가격+재고 옵션 구분정보가 삭제 되었습니다.');}\"> </td>";

//$Contents .= "					<td><input type=text class='textbox' name='options_price_stock[etc1][]' size=28 style='width:80%' value='".$db->dt[option_etc1]."'> ".($i == 0 ? "<!-- 옵션 삭제 -->":"<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' alt='더블클릭시 해당 라인이 삭제 됩니다.'>")." </td>";

$Contents .= "
											</tr>
										</table>";
	}
}else{

	$Contents .= "
										<table width=100% id='options_basic_item_input_0' opt_idx=0 cellspacing=4 cellpadding=0 >
											<col width='20%'>
											<col width='20%'>
											<col width='20%'>
											<col width='20%'>
											<col width='20%'>
											<tr>
												<td>
													<input type=hidden name='options_price_stock[opd_ix][]' value=''>
													<input type=text class='textbox' name='options_price_stock[option_div][]' id='options_price_stock_option_div' size=28 style='width:100%;vertical-align:middle' value=''>
												</td>
												<td><input type=text class='textbox' name='options_price_stock[price][]'  style='width:100%' value='' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
												<td><input type=text class='textbox' name='options_price_stock[stock][]' style='width:100%' value='' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
												<td><input type=text class='textbox' name='options_price_stock[safestock][]' size=28 style='width:100%' value='' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
												<td><input type=text class='textbox' name='options_price_stock[etc1][]' size=28 style='width:80%' value=''> <img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' alt='더블클릭시 해당 라인이 삭제 됩니다.' ondblclick=\"if(document.all.options_basic_item_input_0.length > 1){this.parentNode.parentNode.parentNode.parentNode.removeNode(true);showMessage('options_basic_item_input_status_area','가격+재고 옵션 구분정보가 삭제 되었습니다.');}else{clearInputBox('options_basic_input');$('options_price_stock_option_name').value='';showMessage('options_basic_item_input_status_area','가격+재고 옵션 구분정보가 삭제 되었습니다.');}\"> </td>
											</tr>
										</table>";
}
$Contents .= "
									</td>
								</tr>
							</table>
							<div style='height:30px;text-align:right;color:gray' id='options_basic_item_input_status_area'></div><br>
							</div>";
$Contents .="<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 옵션정보 </b> <input type=checkbox name='basic_option_display_yn' id='basic_option_display_yn' onclick=\"(this.checked ? $('basic_option_zone').style.display='inline':$('basic_option_zone').style.display='none')\"><label for='basic_option_display_yn'>표시</label><!--span class=small > <img src='/admin/webedit/image/wtool6_1.gif' align=absmiddle> 이미지를 클릭해서 상품상세 이미지를 등록해주세요</span--></div>")."</td></tr></table>";
$Contents .= "<div style='display:none;' id='basic_option_zone'>";
$Contents .= "<table><tr><td><a onclick=\"copyOptions('options_input')\"><img src='../image/btn_option_add.gif' border=0 align=absmiddle style='margin:0 0 3 0;'></a></td><td><input type=checkbox name='option_all_use' valign='middle' id='option_all_use' value='Y' align=absmiddle><label for='option_all_use' >옵션전체사용안함</label> <span class=small>(선택 후 저장하시면 옵션정보가 모두 삭제됩니다)</span></td></tr></table><input type=hidden id='option_name' value=''>";

$sql = "select * from shop_product_options where pid = '".$id."' and option_kind != 'b' order by regdate asc ";
//echo $sql;
$db->query($sql);

$options = $db->fetchall();
//print_r($options);
if($db->total){
	for($i=0;$i < count($options);$i++){
		$Contents .= "
							<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver id='options_input' idx=".$i." style='margin-bottom:10' >
								<col width='4%'>
								<col width='17%'>
								<col width='22%'>
								<col width='19%'>
								<col width='19%'>
								<col width='19%'>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" width=4% class=small nowrap>
									사용
									</td>
									<td bgcolor=\"#efefef\" width=9% class=small nowrap>
									<img src='../image/ico_dot.gif' align=absmiddle> 옵션명
									</td>
									<td bgcolor=\"#efefef\" width=13% class=small nowrap>
									<img src='../image/ico_dot.gif' align=absmiddle> 옵션종류
									</td>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> 옵션구분 *</td>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> 추가가격 *</td>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> 기타  </td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td valign=top align=left style='padding:10px 0px 0px 7px;'>
										<input type=hidden name='options[".$i."][opn_ix]' id='option_opn_ix' value='".$options[$i][opn_ix]."'>
										<input type=hidden name='options[".$i."][option_type]' value='".($options[$i][option_type] ? $options[$i][option_type]:"9")."'>
										<input type=checkbox name='options[".$i."][option_use]' value='1' ".($options[$i][option_use] == 1 ? "checked":"")." style='margin:0 0 0 0' align=absmiddle>
									</td>
									<td valign=top align=left style='padding:10px 0px 0px 10px;'>
										";

//$Contents .= "			<input type=text class='textbox' name='options[".$i."][option_name]' id='option_name' size=28 style='width:115;vertical-align:middle' value='".$options[$i][option_name]."'> <img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all.options_input.length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{alert(language_data['goods_input_quick.php']['I'][language]);}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>";
$Contents .= "			<div id='option_name_area_".$i."' style='display:inline'><input type=text class='textbox' name='options[".$i."][option_name]' id='option_name' size=28 style='width:115;vertical-align:middle' value='".$options[$i][option_name]."'> </div>

										".($i == 0 ? "<!-- 옵션 삭제 -->":"<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all.options_input.length > 1){this.parentNode.parentNode.parentNode.removeNode(true);showMessage('options_input_status_area_".$i."','해당 옵션 구분정보가 삭제 되었습니다.');}else{alert(language_data['goods_input_quick.php']['I'][language]);}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>")." ";

$Contents .= "
									</td>
									<td valign=top style='padding-top:10px;'>
										<select name='options[".$i."][option_kind]' id='option_kind_1' style='font-size:12px;'>
											<option value=p ".($options[$i][option_kind] == "p" ? "selected":"").">가격추가옵션</option>
											<option value=s ".($options[$i][option_kind] == "s" ? "selected":"").">선택옵션</option>
										</select>
										<a onclick=\"copyOptions('options_item_input_".$i."');showMessage('options_input_status_area_".$i."','해당옵션 구분정보가 추가 되었습니다.');\" ><img src='../image/btn_option_detail_add.gif' border=0 align=absmiddle></a>
									</td>
									<td colspan=5><input type=hidden id='options_item_option_div_".$i."' value=''>
									";

			$sql = "select * from shop_product_options_detail where pid = '".$id."' and opn_ix = '".$options[$i][opn_ix]."' order by id asc ";
			//echo $sql;
			$db->query($sql);

			if($db->total){
				for($j=0;$j < $db->total;$j++){
				$db->fetch($j);

		$Contents .= "<table width=100% id='options_item_input_".$i."' idx=".$i." detail_idx=".$j." cellspacing=4 cellpadding=0 ><!--ondblclick=\"if(document.all.options_item_input_0.length > 1){this.removeNode(true);}else{alert(language_data['goods_input_quick.php']['I'][language]);}\"-->
											<col width='33%'>
											<col width='33%'>
											<col width='33%'>
											<tr>
												<td>
												<input type=hidden name='options[".$i."][details][".$j."][opd_ix]' value='".$db->dt[id]."'>
												<input type=text class='textbox' name='options[".$i."][details][".$j."][option_div]' id='options_item_option_div_".$i."' size=28 style='width:100%;vertical-align:middle' value='".$db->dt[option_div]."'>
												</td>
												<td><input type=text class='textbox' name='options[".$i."][details][".$j."][price]' style='width:100%' value='".$db->dt[option_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>";
			$Contents .= "			<td><input type=text class='textbox' name='options[".$i."][details][".$j."][etc1]' id='options_item_option_etc_".$i."' size=28 style='width:85%' value='".$db->dt[option_etc1]."'> <img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all.options_item_input_".$i.".length > 1){this.parentNode.parentNode.parentNode.parentNode.removeNode(true);showMessage('options_input_status_area_".$i."','해당옵션 구분정보가 삭제되었습니다.');}else{clearInputBox('options_item_input_".$i."');showMessage('options_input_status_area_".$i."','해당옵션 구분정보가 삭제되었습니다.');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'></td>";
	//	$Contents .= "			<td><input type=text class='textbox' name='options[".$i."][details][".$j."][etc1]' size=28 style='width:85%' value='".$db->dt[option_etc1]."'> ".($j == 0 ? "<!-- 옵션 삭제 -->":"<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all.options_item_input_".$i.".length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{alert(language_data['goods_input_quick.php']['I'][language]);}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>")."</td>";

		$Contents .= "
											</tr>
										</table>";
					}
			}else{
		$Contents .= "<table width=100% id='options_item_input_0' idx=0 detail_idx=0 cellspacing=4 cellpadding=0 ><!--ondblclick=\"if(document.all.options_item_input_0.length > 1){this.removeNode(true);}else{alert(language_data['goods_input_quick.php']['I'][language]);}\"-->
											<col width='33%'>
											<col width='33%'>
											<col width='33%'>
											<tr>
												<td>
													<input type=hidden name='options[0][details][0][opd_ix]' value=''>
													<input type=text class='textbox' name='options[0][details][0][option_div]' id='options_item_option_div_0' size=28 style='width:100%;vertical-align:middle' value=''>
												</td>
												<td><input type=text class='textbox' name='options[0][details][0][price]'  style='width:100%' value='' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
												<td><input type=text class='textbox' name='options[0][details][0][etc1]' id='options_item_option_etc_0' size=28 style='width:85%' value=''><img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all.options_item_input_0.length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{clearInputBox('options_item_input_0');alert(language_data['goods_input_quick.php']['I'][language]);}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'></td>
											</tr>
										</table>";
			}
$Contents .= "
									</td>
								</tr>
								<tr><td colspan=6 style='background-color:#ffffff;'><div style='height:20px;text-align:right;color:gray;' id='options_input_status_area_".$i."'></div></td></tr>
							</table>
							";
		}
}else{
	$Contents .= "
							<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver id='options_input' idx=0 style='margin-bottom:10' ><!--ondblclick=\"if(document.all.options_input.length > 1){this.removeNode(true);}else{alert(language_data['goods_input_quick.php']['I'][language]);}\"-->
								<col width='4%'>
								<col width='17%'>
								<col width='22%'>
								<col width='19%'>
								<col width='19%'>
								<col width='19%'>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" width=4% class=small nowrap>
									사용
									</td>
									<td bgcolor=\"#efefef\" width=13% class=small nowrap>
									<img src='../image/ico_dot.gif' align=absmiddle> 옵션명
									</td>
									<td bgcolor=\"#efefef\" width=13% class=small nowrap>
									<img src='../image/ico_dot.gif' align=absmiddle> 옵션종류
									</td>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> 옵션구분 *</td>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> 추가가격 *</td>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> 기타  </td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td valign=top align=left style='padding:10px 0px 0px 7px;'>
									<input type=hidden name='options[0][option_type]' value='9'>
									<input type=checkbox name='options[0][option_use]' value='1'>
									</td>
									<td valign=top align=left style='padding:10px 0px 0px 10px;'>

									<div id='option_name_area_0' style='display:inline'><input type=text class='textbox' name='options[0][option_name]' id='option_name' size=28 style='width:115;vertical-align:middle' value='$option_div'> </div><!-- 옵션 삭제 --> </td>
									<td valign=top style='padding-top:10px;'>
									<select name='options[0][option_kind]' id='option_kind_1' style='font-size:12px;'>
										<option value=p>가격추가옵션</option>
										<option value=s>선택옵션</option>
									</select>

									<!--a onclick=\"copyOptions('options_item_input_0')\" >옵션구분추가</a-->
									<a onclick=\"copyOptions('options_item_input_0');showMessage('options_input_status_area_0','해당옵션 구분정보가 추가 되었습니다.');\" ><img src='../image/btn_option_detail_add.gif' border=0 align=absmiddle></a>
									</td>
									<td colspan=5><input type=hidden id='options_item_option_div_0' value=''><input type=hidden id='options_item_option_etc_0' value=''>
										<table width=100% id='options_item_input_0' idx=0 detail_idx=0 cellspacing=4 cellpadding=0 ><!--ondblclick=\"if(document.all.options_item_input_0.length > 1){this.removeNode(true);}else{alert(language_data['goods_input_quick.php']['I'][language]);}\"-->
											<col width='33%'>
											<col width='33%'>
											<col width='33%'>
											<tr>
												<td><input type=text class='textbox' name='options[0][details][0][option_div]' id='options_item_option_div_0' size=28 style='width:100%;vertical-align:middle' value='$option_div'></td>
												<td><input type=text class='textbox' name='options[0][details][0][price]'  style='width:100%' value='$option_price' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>";

$Contents .= "					<td><input type=text class='textbox' name='options[0][details][0][etc1]' id='options_item_option_etc_0' size=28 style='width:85%' value='$option_etc1'> <img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all.options_item_input_0.length > 1){this.parentNode.parentNode.parentNode.parentNode.removeNode(true);showMessage('options_input_status_area_0','해당옵션 구분정보가 삭제되었습니다.');}else{clearInputBox('options_item_input_0');showMessage('options_input_status_area_0','해당옵션 구분정보가 삭제되었습니다.');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'></td>";

//$Contents .= "					<td><input type=text class='textbox' name='options[0][details][0][etc1]' size=28 style='width:85%' value='$option_etc1'> <!-- 옵션 삭제 --></td>";

$Contents .= "
											</tr>
										</table>
									</td>
								</tr>
								<tr><td colspan=6 style='background-color:#ffffff;'><div style='height:20px;text-align:right;color:gray;' id='options_input_status_area_0'></div></td></tr>
							</table>
							</div>";
}
$Contents .="<table width='100%' cellpadding=0 cellspacing=0>
							<tr height=30>
							<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 디스플레이옵션 </b> <input type=checkbox name='display_option_display_yn' id='display_option_display_yn' onclick=\"(this.checked ? $('display_option_zone').style.display='inline':$('display_option_zone').style.display='none')\"><label for='display_option_display_yn'>표시</label><!--span class=small > <img src='/admin/webedit/image/wtool6_1.gif' align=absmiddle> 이미지를 클릭해서 상품상세 이미지를 등록해주세요</span--></div>")."</td>
							</tr>
							</table>";

$Contents .= "<div style='display:none;' id='display_option_zone'>
							<a onclick=\"copyOptions('display_options_input_item')\" ><img src='../image/btn_display_option_detail_add.gif' border=0 align=absmiddle style='margin:0 0 3 0;'></a> <br>
							<table width='49%' cellpadding=5 cellspacing=1 bgcolor=silver id='display_options_input' opt_idx=0 style='margin-bottom:10'>
								<col width='8%'>
								<col width='42%'>
								<col width='50%'>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class=small>사용</td>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> 추가정보명 *</td>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> 추가정보내용 *</td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td colspan=5><input type=hidden id='display_option_title' value=''>";



$sql = "select * from shop_product_displayinfo where pid = '".$id."' order by regdate asc ";

$db->query($sql);

$display_options = $db->fetchall();
//print_r($options);
if($db->total){
	for($i=0;$i < count($display_options);$i++){

$Contents .= "			<table width=100% id='display_options_input_item' opt_idx=".$i." cellspacing=4 cellpadding=0 >
											<col width='8%'>
											<col width='42%'>
											<col width='50%'>
											<tr>
												<td><input type=checkbox name='display_options[".$i."][dp_use]' value='1' ".($display_options[$i][dp_use] == "1" ? "checked":"") ."></td>
												<td><input type=text class='textbox' name='display_options[".$i."][dp_title]' id='display_option_title' size=28 style='width:100%;vertical-align:middle' value='".$display_options[$i][dp_title]."'></td>";

$Contents .= "					<td><input type=text class='textbox' name='display_options[".$i."][dp_desc]'  style='width:90%' value='".$display_options[$i][dp_desc]."'> <img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all.display_options_input_item.length > 1){this.parentNode.parentNode.parentNode.parentNode.removeNode(true);}else{clearInputBox('display_options_input_item');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'></td>";
//$Contents .= "					<td><input type=text class='textbox' name='display_options[".$i."][dp_desc]'  style='width:90%' value='".$display_options[$i][dp_desc]."'> ".($i == 0 ? "<!-- 옵션 삭제 -->":"<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all.display_options_input_item.length > 1){this.parentNode.parentNode.removeNode(true);}else{clearInputBox('display_options_input_item');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>")." </td>";

$Contents .= "
											</tr>
										</table>";
	}
}else{
	$Contents .= "
										<table width=100% id='display_options_input_item' opt_idx=0 cellspacing=4 cellpadding=0 ><!--ondblclick=\"if(document.all.display_options_input_item.length > 1){this.removeNode(true);}else{alert(language_data['goods_input_quick.php']['I'][language]);}\"-->
											<col width='8%'>
											<col width='42%'>
											<col width='50%'>
											<tr>
												<td><input type=checkbox name='display_options[".$i."][dp_use]' value='1' checked></td>
												<td><input type=text class='textbox' name='display_options[0][dp_title]' id='display_option_title'  size=28 style='width:100%;vertical-align:middle' value='$option_div'></td>
												<td><input type=text class='textbox' name='display_options[0][dp_desc]'  style='width:90%' value='$option_price'> <img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all.display_options_input_item.length > 1){this.parentNode.parentNode.parentNode.parentNode.removeNode(true);}else{clearInputBox('display_options_input_item');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'> </td>
											</tr>
										</table>";
}
$Contents .= "
									</td>

								</tr>
								<!--tr>
									<td></td>
									<td colspan=2 align=right valign=top>
									<input type=image src='../images/btn/ok.gif' aligb=absmiddle border=0>
									<a href=\"javascript:document.forms['optionform'].reset();\"><img src='../images/btn/cancel.gif' aligb=absmiddle border=0></a>
									</td>
								</tr-->
							</table><br></div>";
$Contents .="<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=30>
					<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 관련상품등록 </b> <a onclick=\"showLayer('relation_product_area','1')\"><img src='/admin/images/btn_goods_search_add.gif' border=0 align=absmiddle></a></div>")."</td>
					</tr>
					</table>";

$Contents .="		<table border=0 cellpadding=0 cellspacing=0 width='100%'>
						<tr bgcolor='#ffffff'>
							<!--td bgcolor=#efefef><img src='/admin/image/ico_dot.gif'> <b>관련상품</b></td-->
							<td >
								  <div style='height:65px;padding:5px;' id='group_product_area_1' >".relationProductList($id, "clipart")."</div>
								  <span class=small>* 이미지를 선택하신 후 방향키로 노출 순서를 조정하실수 있습니다.</span>
							  </td>
						</tr>
						<tr bgcolor='#F8F9FA'>
							<td colspan=2>";

$Contents .= "
	                     <!-- 카테고리 및 상품 검색 인터페이스 -->
								<div class='doong' id='relation_product_area' style='z-index:100;position:absolute;display:none;vertical-align:top;height:260px;border:4px solid silver;padding:5px;background-color:#ffffff'   >
								<table bgcolor=#ffffff border=0 cellpadding=0 cellspacing=0 width=100%' >
								<tr height=25 >
									<td width='15%' style='padding-right:5px;' valign=top>
										<div class='tab' style='margin: 0px 0px ;'>
											<table class='s_org_tab'>
											<tr>
												<td class='tab'>
													<table id='tab_01' class='on' >
													<tr>
														<th class='box_01'></th>
														<td class='box_02 small' onclick=\"showTabContents('category_search','tab_01')\" style='padding-left:5px;padding-right:5px;'>카테고리검색</td>
														<th class='box_03'></th>
													</tr>
													</table>
													<table id='tab_02'>
													<tr>
														<th class='box_01'></th>
														<td class='box_02 small' onclick=\"showTabContents('keyword_search','tab_02')\" style='padding-left:5px;padding-right:5px;'>키워드검색</td>
														<th class='box_03'></th>
													</tr>
													</table>

												</td>
												<td class='btn'>

												</td>
											</tr>
											</table>
										</div>
										<div class='t_no' style='margin: 2px 0px ; '>
											<div class='my_box' >
												<div id='category_search' style='overflow:auto;height:370px;width:200px;border:1px solid silver'><iframe  src='relationAjax.category.php' width=100% height=100% frameborder=0 ></iframe></div>
												<div id='keyword_search' style='display:none;height:370px;width:200px;border:1px solid silver;padding-top:10px;'>

													<table align=center>
														<tr>
															<td bgcolor='#efefef' align=center>입점업체</td>
															<td>
																".CompanyList($company_id,"","")."
															</td>
														</tr>
														<tr>
															<td>
																<select name='search_type' id='search_type'>
																	<option value='p.pname'>상품명</option>
																	<option value='p.pcode'>상품코드</option>
																	<option value='brand_name'>브랜드명</option>
																</select>
															</td>
															<td><input type='text' name='search_text' id='search_text' size='15'onkeypress=\"if(event.keyCode == 13){SearchProduct(document.product_input);return false;}\" ></td>
														</tr>
														<tr>
															<td colspan=2 align=right><img src='../image/search01.gif' onclick=\"SearchProduct(document.product_input);\"></td>
														</tr>
														</table>

												</div>
											</div>
										</div>
										</td>
										<td colspan=2 width='100%' valign=top>
												<table border=0 cellpadding=0 cellspacing=0 width=100% height=100% >
													<tr height=25>
														<td style='padding:1px;padding-left:10px;padding-right:10px;border:1px solid silver;border-bottom:0px;' align=center >
														<table width=100% height=100%>
														<tr>
															<td align=left width='10' ><input type=hidden id='cpid' value=''><input type=checkbox name='all_fix' onclick='fixAll(document.product_input)' >

															</td>
															<td  align=center>
															<img src='../image/btn_selected_reg.gif' border='0' align='left' onclick='selectGoodsList(document.product_input);' style='cursor:hand;'>
															<!--img src='../image/btn_searched_reg.gif' border='0' align='right'-->
															<select name='list_max' id='list_max' align=right onchange='getRelationProduct(_mode,_nset, _page,_cid,_depth);'>
																<option value='3'>3</option>
																<option value='5' >5</option>
																<option value='10' selected>10</option>
																<option value='15'>15</option>
																<option value='20'>20</option>
																<option value='30' >30</option>
																<option value='40'>40</option>
																<option value='50'>50</option>
																<option value='100'>100</option>
															</select>
															</td>
														</tr>
														</table>
														</td>
														<td style='padding:0 0 0 5' rowspan=3></td>
														<td style='padding:1px;padding-left:10px;padding-right:10px;border:1px solid silver;border-bottom:0px;' align=center >
														<b>선택된 상품</b>
														</td>
													</tr>
													<tr height=100%>
														<td width=50%>
														<div id='reg_product' style='width:100%;height:350px;width:100%;height:100%;padding:1px;padding-left:10px;padding-right:10px;border:1px solid silver;' align=center >
														<table width=100% height=100%><tr><td align=center class='small'>좌측카테고리를 선택해주세요</td></tr></table>
														</div>
														</td>
														<td width=50% height=100% style='padding:0 0 0 0'>
															<div id='drop_relation_product'  >".relationProductList($id)."</div>
														</td>
													</tr>
													<tr height=25 >
														<td id='view_paging' style='padding:1px;padding-left:0px;padding-right:2px;border:1px solid silver;border-top:0px;' align=center >

														</td>
														<td style='padding:1px;padding-left:0px;padding-right:2px;border:1px solid silver;border-top:0px;'>
														<img src='../image/btn_whole_del.gif' border='0' align='left' onclick='deleteWhole(true);' style='cursor:hand;'>
														<a onclick=\"showLayer('relation_product_area',select_gorup_code)\"><img src='../image/btn_win_close.gif' border='0' align='left' ></a>
														</td>
														</tr>
												</table>
										<!--/div-->
										</td>
								</tr>
								</table>
								</div>
							</td>
						</tr>
					</table><br>";

$Contents .="<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 제품상세정보</b> <span class=small > <img src='/admin/webedit/image/wtool6_1.gif' align=absmiddle> 이미지를 클릭해서 상품상세 이미지를 등록해주세요</span></div>")."</td></tr></table>
		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
                    <tr bgcolor='#ffffff'>
			<td width='15%' bgcolor=\"#efefef\"  nowrap><img src='../image/ico_dot.gif' align=absmiddle> 제품간략소개 *</td>
			<td width='85%' bgcolor=\"#efefef\" colspan=2 style='padding:4px;'><textarea name=\"shotinfo\" rows=\"3\" cols=\"10\" style='padding:2px;height:40px;width:100%' class='textbox'>".$shotinfo."</textarea></td>
		</tr>
                    <tr>
                      <td height='30' colspan='3'>
						      <table id='tblCtrls' width='100%' border='0' cellspacing='1' cellpadding='0' align='center'>
						        <tr>
						          <td bgcolor='F5F6F5'>
									 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						              <tr>
						                <td width='18%' height='56'>
											 	<table width='100%' height='56' border='0' align='center' cellpadding='0' cellspacing='0'>
						                    <tr align='center' valign='bottom'>
						                      <td height='26'><a href='javascript:doBold();' onMouseOver=\"MM_swapImage('editImage1','','../webedit/image/wtool1_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool1.gif' name='editImage1' width='19' height='18' border='0' id='editImage1'></a></td>
						                      <td><a href='javascript:doItalic();' onMouseOver=\"MM_swapImage('editImage2','','../webedit/image/wtool2_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool2.gif' name='editImage2' width='19' height='18' border='0' id='editImage2'></a></td>
						                      <td><a href='javascript:doUnderline();' onMouseOver=\"MM_swapImage('editImage3','','../webedit/image/wtool3_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool3.gif' name='editImage3' width='19' height='18' border='0' id='editImage3'></a></td>
						                    </tr>
						                    <tr>
						                      <td height='3' colspan='3'></td>
						                    </tr>
						                    <tr align='center' valign='top'>
						                      <td height='27'><a href='javascript:doLeft();' onMouseOver=\"MM_swapImage('editImage8','','../webedit/image/wtool8_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool8.gif' name='editImage8' width='19' height='18' border='0' id='editImage8'></a></td>
						                      <td><a href='javascript:doCenter();' onMouseOver=\"MM_swapImage('editImage9','','../webedit/image/wtool9_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool9.gif' name='editImage9' width='19' height='18' border='0' id='editImage9'></a></td>
						                      <td><a href='javascript:doRight();' onMouseOver=\"MM_swapImage('editImage10','','../webedit/image/wtool10_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool10.gif' name='editImage10' width='19' height='18' border='0' id='editImage10'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='19%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr>
						                      <td width='100%' height='27' align='center' valign='bottom'><a href='javascript:doFont();' onMouseOver=\"MM_swapImage('editImage4','','../webedit/image/wtool4_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool4.gif' name='editImage4' width='84' height='22' border='0' id='editImage4'></a></td>
						                    </tr>
						                    <tr>
						                      <td height='2'></td>
						                    </tr>
						                    <tr>
						                      <td height='27' align='center' valign='top'><a href='javascript:doSize();' onMouseOver=\"MM_swapImage('editImage11','','../webedit/image/wtool11_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool11.gif' name='editImage11' width='84' height='22' border='0' id='editImage11'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='20%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr>
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doForcol();' onMouseOver=\"MM_swapImage('editImage5','','../webedit/image/wtool5_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool5.gif' name='editImage5' width='95' height='22' border='0' id='editImage5'></a></td>
						                    </tr>
						                    <tr>
						                      <td height='2'></td>
						                    </tr>
						                    <tr>
						                      <td height='27' align='center' valign='top'><a href='javascript:doBgcol();' onMouseOver=\"MM_swapImage('editImage12','','../webedit/image/wtool12_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool12.gif' name='editImage12' width='95' height='22' border='0' id='editImage12'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='18%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr>
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doImage();' onMouseOver=\"MM_swapImage('editImage6','','../webedit/image/wtool6_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool6.gif' name='editImage6' width='73' height='22' border='0' id='editImage6'></a></td>
						                    </tr>
						                    <tr>
						                      <td height='2'></td>
						                    </tr>
						                    <tr>
						                      <td height='27' align='center' valign='top'><a href='javascript:doTable();' onMouseOver=\"MM_swapImage('editImage13','','../webedit/image/wtool13_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool13.gif' name='editImage13' width='73' height='22' border='0' id='editImage13'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='25%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr>
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doLink();' onMouseOver=\"MM_swapImage('editImage7','','../webedit/image/wtool7_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool7.gif' name='editImage7' width='74' height='22' border='0' id='editImage7'></a></td>
						                    </tr>
						                    <tr>
						                      <td height='2'></td>
						                    </tr>
						                    <tr>
						                      <td height='27' align='center' valign='top'><a href='javascript:doMultilink();' onMouseOver=\"MM_swapImage('editImage14','','../webedit/image/wtool14_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool14.gif' name='editImage14' width='111' height='22' border='0' id='editImage14'></a></td>
						                    </tr>
						                  </table>
											 </td>
						              </tr>
						            </table>
									 </td>
						        </tr>
						      </table>
<textarea name=\"basicinfo\"  style='display:none' >".$basicinfo."</textarea>
						      <input type='hidden' name='content' value=''>
						      <iframe align='right' id='iView' style='width: 100%; height:310;' scrolling='YES' hspace='0' vspace='0'></iframe>
						      <!-- html편집기 메뉴 종료 -->
                      </td>
                    </tr>
                    <tr style='display:block;'>
          	          <td width='120' height='25' align='center' bgcolor='#F0F0F0'></td>
          		       <td colspan='2' align='right'>&nbsp;
						      <a href='javascript:doToggleText(document.product_input);' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
          			      <a href='javascript:doToggleHtml(document.product_input);' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
                      </td>
                    </tr>
                    <tr>
                      <td bgcolor='D0D0D0' height='1' colspan='4'></td>
                    </tr>
                  </table><br>";
if ($id != ""){
	$img_view_style = " style='display:inline;'";
}else{
	$img_view_style = " style='display:none;''"	;
}
$image_db = new Database;
$image_db->query("select * from shop_image_resizeinfo order by idx");
$image_info = $image_db->fetchall();
$Contents = $Contents."	<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 이미지 추가</b> <span class=small >   ".$image_info[0][width]."*".$image_info[0][height]." 이미지를 등록하시면 체크된 작은 이미지는 자동으로 등록됩니다.</span> <a href=\"javascript:PoPWindow('product_resize.php?mmode=pop',960,600,'brand')\"'><img src='../image/btn_pop_manage.gif' align=absmiddle border=0></a></div>")."</td></tr></table>
			<div id=\"innopu_style_wrapper\" style='width:100%;'>
			<table border=0 cellpadding=5 cellspacing=0 bgcolor=#ffffff width=100%>
				<col width=100>
				<col width=300>
				<!--col width=100-->
				<col width=*>
				<tr height=1>
					<td colspan=3 class='dot-x'></td>
					<td rowspan=11 style='border:1px solid silver' align=center valign=middle >
					<div style='overflow:auto;width:380px;height:500px;vertical-align:middle;' id=viewimg>
					";
				if(file_exists($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/product/m_".$id.".gif")){
					$Contents = $Contents."<img src='".$admin_config[mall_data_root]."/images/product/m_".$id.".gif' onerror=\"this.src='../images/noimage_152_148.gif'\" style='border:1px solid silver' id=chimg>";
				}else{
					$Contents = $Contents."<img src='../images/noimage_152_148.gif' style='border:1px solid silver' id=chimg>";
				}
					$Contents = $Contents."
					</div>
					</td>
				</tr>
				<tr bgcolor='#ffffff' height=30>
					<td bgcolor=\"#efefef\" rowspan=2 class=small nowrap>
					<div id=\"pu_drop1\" class=\"drop_multi\">
					<script language=\"JavaScript\">
					LoadMultiImageLoader('pu_preview1', 160, 120, 'http://www.innorix.com/PU/images/innopu_drop.gif', 67, 85);
					</script>
					</div>
					</td>
					<td class='small' nowrap>
					<!--input type=file name='allimg' size=20 style='font-size:8pt'-->
					<img src='../image/ico_dot.gif' align=absmiddle > ".$image_info[0][width]."*".$image_info[0][height]." *<b>이미지복사</b>
					<input type=checkbox name='chk_allimg' value=1 id='copy_allimg' onclick='copyImageCheckAll();'>
					<img src='../image/view_img.gif' onclick=\"ChnageImg('b','".$id."', '".$admin_config[mall_data_root]."/images/product');\" align=absmiddle $img_view_style style='cursor:hand'><br>
					<input type=text name='allimg' id='pu_file1' size=29 style='font-size:8pt'>
					<input type=\"button\" onClick=\"addPoint='1'; document.InnoAP.OpenFile()\" value=\"찾아보기...\" class=\"input_button\">
					<input type=\"button\" onClick=\"RemoveImageLoader('1')\" value=\"삭제\" class=\"input_button\">
					<div id=\"pu_exif1\" class=\"exif_multi\" style='height:58px;'>&nbsp;</div>
					<textarea style='display:none;' name=\"pu_desc1\" cols=\"38\" rows=\"6\" class=\"desc_multi\"></textarea>
					</td>
					<!--td width=5% $img_view_style><img src='../image/view_img.gif' onclick=\"ChnageImg('b','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'></td-->

				</tr>
				<tr height=10><td colspan=1 class='small'>※ ".$image_info[0][width]."*".$image_info[0][height]." 이미지 복사를 클릭하시면 나머지 이미지가 복사됩니다</td></tr>
				<tr height=1><td colspan=3 class='dot-x'></td></tr>
				<tr bgcolor='#ffffff' height=50>
					<td bgcolor=\"#efefef\"  class=small nowrap>
					<div id=\"pu_drop2\" class=\"drop_multi\">
					<script language=\"JavaScript\">
					LoadMultiImageLoader('pu_preview2', 160, 120, 'http://www.innorix.com/PU/images/innopu_drop.gif', 67, 85);
					</script>
					</div>
					</td>
					<td class=small>
					<img src='../image/ico_dot.gif' align=absmiddle > ".$image_info[1][width]."*".$image_info[1][height]." *이미지복사<input type=checkbox name='chk_mimg' id='copy_img' value=1>
					<img src='../image/view_img.gif' onclick=\"ChnageImg('m','".$id."', '".$admin_config[mall_data_root]."/images/product');\" align=absmiddle $img_view_style style='cursor:hand'><br>
					<input type=text name='mimg' id='pu_file2' size=29 style='font-size:8pt'>
					<input type=\"button\" onClick=\"addPoint='2'; document.InnoAP.OpenFile()\" value=\"찾아보기...\" class=\"input_button\">
					<input type=\"button\" onClick=\"RemoveImageLoader('2')\" value=\"삭제\" class=\"input_button\">
					<div id=\"pu_exif2\" class=\"exif_multi\" style='height:78px;'>&nbsp;</div>
					<textarea style='display:none;' name=\"pu_desc2\" cols=\"38\" rows=\"6\" class=\"desc_multi\"></textarea>
					</td>
					<!--td $img_view_style></td-->

				</tr>
				<tr height=1><td colspan=3 class='dot-x'></td></tr>
				<tr bgcolor='#ffffff' height=50>
					<!--td bgcolor=\"#efefef\" ></td-->
					<td bgcolor=\"#efefef\"  class=small nowrap>
					<div id=\"pu_drop3\" class=\"drop_multi\">
					<script language=\"JavaScript\">
					LoadMultiImageLoader('pu_preview3', 160, 120, 'http://www.innorix.com/PU/images/innopu_drop.gif', 67, 85);
					</script>
					</div>
					</td>
					<td class=small>
					<img src='../image/ico_dot.gif' align=absmiddle > ".$image_info[2][width]."*".$image_info[2][height]." *이미지복사<input type=checkbox name='chk_msimg' id='copy_img' value=1>
					<img src='../image/view_img.gif' onclick=\"ChnageImg('ms','".$id."', '".$admin_config[mall_data_root]."/images/product');\" align=absmiddle $img_view_style  style='cursor:hand'><br>
					<input type=text name='msimg' id='pu_file3' size=29 style='font-size:8pt'>
					<input type=\"button\" onClick=\"addPoint='3'; document.InnoAP.OpenFile()\" value=\"찾아보기...\" class=\"input_button\">
					<input type=\"button\" onClick=\"RemoveImageLoader('3')\" value=\"삭제\" class=\"input_button\">
					<div id=\"pu_exif3\" class=\"exif_multi\" style='height:78px;'>&nbsp;</div>
					<textarea style='display:none;' name=\"pu_desc3\" cols=\"38\" rows=\"6\" class=\"desc_multi\"></textarea>
					</td>
					<!--td $img_view_style></td-->

				</tr>
				<tr height=1><td colspan=3 class='dot-x'></td></tr>
				<tr bgcolor='#ffffff' height=50>
					<!--td bgcolor=\"#efefef\" ></td-->
					<td bgcolor=\"#efefef\" class=small  nowrap>
					<div id=\"pu_drop4\" class=\"drop_multi\">
					<script language=\"JavaScript\">
					LoadMultiImageLoader('pu_preview4', 160, 120, 'http://www.innorix.com/PU/images/innopu_drop.gif', 67, 85);
					</script>
					</div>
					</td>
					<td class=small>
					<img src='../image/ico_dot.gif' align=absmiddle >  ".$image_info[3][width]."*".$image_info[3][height]." *이미지복사<input type=checkbox name='chk_simg' id='copy_img' value=1>
					<img src='../image/view_img.gif' onclick=\"ChnageImg('s','".$id."', '".$admin_config[mall_data_root]."/images/product');\" align=absmiddle $img_view_style style='cursor:hand'><br>
					<input type=text name='simg' id='pu_file4' size=29 style='font-size:8pt'>
					<input type=\"button\" onClick=\"addPoint='4'; document.InnoAP.OpenFile()\" value=\"찾아보기...\" class=\"input_button\">
					<input type=\"button\" onClick=\"RemoveImageLoader('4')\" value=\"삭제\" class=\"input_button\">
					<div id=\"pu_exif4\" class=\"exif_multi\" style='height:78px;'>&nbsp;</div>
					<textarea style='display:none;' name=\"pu_desc4\" cols=\"38\" rows=\"6\" class=\"desc_multi\"></textarea>
					</td>
					<!--td $img_view_style></td-->

				</tr>
				<tr height=1><td colspan=3 class='dot-x'></td></tr>
				<tr bgcolor='#ffffff' height=50>
					<!--td bgcolor=\"#efefef\" ></td-->
					<td bgcolor=\"#efefef\"  class=small nowrap>
					<div id=\"pu_drop5\" class=\"drop_multi\">
					<script language=\"JavaScript\">
					LoadMultiImageLoader('pu_preview5', 160, 120, 'http://www.innorix.com/PU/images/innopu_drop.gif', 67, 85);
					</script>
					</div>
					</td>
					<td valign=top class=small>
					<img src='../image/ico_dot.gif' align=absmiddle > ".$image_info[4][width]."*".$image_info[4][height]." * 이미지복사<input type=checkbox name='chk_cimg' id='copy_img' value=1>
					<img src='../image/view_img.gif' onclick=\"ChnageImg('c','".$id."', '".$admin_config[mall_data_root]."/images/product');\" align=absmiddle $img_view_style style='cursor:hand'><br>
					<input type=text name='cimg' id='pu_file5' size=29 style='font-size:8pt'>
					<input type=\"button\" onClick=\"addPoint='5'; document.InnoAP.OpenFile()\" value=\"찾아보기...\" class=\"input_button\">
					<input type=\"button\" onClick=\"RemoveImageLoader('5')\" value=\"삭제\" class=\"input_button\">
					<div id=\"pu_exif5\" class=\"exif_multi\" style='height:78px;'>&nbsp;</div>
					<textarea style='display:none;' name=\"pu_desc5\" cols=\"38\" rows=\"6\" class=\"desc_multi\"></textarea>
					</td>
					<!--td $img_view_style></td-->

				</tr>
				<tr height=1><td colspan=3 class='dot-x'></td></tr>
				<tr bgcolor='#ffffff' height=50>
					<td bgcolor=\"#efefef\"  class=small nowrap>
					<img src='../image/ico_dot.gif' align=absmiddle > 이미지 URL <br>
					</td>
					<td colspan=3 class=small>
					<input type=checkbox name='img_url_copy' id='img_url_copy' value=1> <label for='img_url_copy'>URL 이미지복사</label> <img src='../image/view_img.gif' onclick=\"ChnageImg('c','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'><br>
					<input type=text name='bimg_text' style='width:100%;font-size:8pt' value='".$bimg_text."'>
					<div class=small> URL 이미지복사를 체크하시면 입력된 이미지 URL 정보를 바탕으로 이미지가 복사됩니다. 단 해당이미지 서버에서 이미지 복사를 차단한 경우는 이미지 복사가 거부될 수 있습니다.</div>
					</td>

				</tr>
				<tr height=1><td colspan=4 class='dot-x'></td></tr>
				<tr height=1></td></tr>
			</table>
			</div><br>";


$Contents .="<table width='100%' cellpadding=0 cellspacing=0>
							<tr height=30>
							<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 추가이미지 등록 </b> <!--span class=small > <img src='/admin/webedit/image/wtool6_1.gif' align=absmiddle> 이미지를 클릭해서 상품상세 이미지를 등록해주세요</span--></div>")."</td>
							</tr>
							</table>";

$Contents .= "
							<a onclick=\"copyOptions('add_img_input_item')\" ><img src='../image/btn_addimage_add.gif' border=0 align=absmiddle style='margin:0 0 3 0;'></a> <br>
							<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver id='display_options_input' opt_idx=0 style='margin-bottom:10'>
								<!--col width='4%'-->
								<col width='32%'>
								<col width='32%'>
								<col width='32%'>
								<tr height=25 bgcolor='#ffffff' align=center>
									<!--td bgcolor=\"#efefef\" class=small>삭제</td-->
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> ".$image_info[0][width]."*".$image_info[0][height]." *</td>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> ".$image_info[1][width]."*".$image_info[1][height]." *</td>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> ".$image_info[4][width]."*".$image_info[4][height]." *</td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td colspan=5>";


$sql = "select * from shop_addimage where pid = '".$id."' order by regdate asc  ";

$db->query($sql);

$add_images = $db->fetchall();

if($db->total){
	$Contents .= "
										<table width=100% id='add_img_input_item' opt_idx=0 cellspacing=4 cellpadding=0 ><!--ondblclick=\"if(document.all.add_img_input_item.length > 1){this.removeNode(true);}else{alert(language_data['goods_input_quick.php']['I'][language]);}\"-->
											<!--col width='3%'-->
											<col width='7%'>
											<col width='25%'>
											<col width='7%'>
											<col width='25%'>
											<col width='7%'>
											<col width='25%'>
											<tr>
												<!--td><input type=checkbox name='addimages[0][addbimg]' value='1' checked></td-->
												<td></td>
												<td>
													<b >이미지복사</b><input type=checkbox name='addimages[0][add_copy_allimg]' onclick=\"copyAddImageCheckAll('add_copy_img_0');\" id='add_copy_img_0' value=1 checked>
													<input type=file class='textbox' name='addimages[0][addbimg]' size=28 style='width:98%;vertical-align:middle' value='$option_div'>
												</td>
												<td></td>
												<td>
													복사함<input type=checkbox name='addimages[0][add_chk_mimg]' id='add_copy_img_0' value=1 checked>
													<input type=file class='textbox' name='addimages[0][addmimg]'  style='width:98%' value='$option_price'>
												</td>
												<td></td>
												<td>
													복사함<input type=checkbox name='addimages[0][add_chk_cimg]' id='add_copy_img_0' value=1 checked>
													<input type=file class='textbox' name='addimages[0][addcimg]'  style='width:90%' value='$option_price'> <!-- 옵션 삭제 -->
												</td>
											</tr>
										</table>";
	for($i=0;$i < count($add_images);$i++){

$Contents .= "
										<table width=100% id='add_img_input_item' opt_idx=".($i+1)." cellspacing=4 cellpadding=0 border=0><!--ondblclick=\"if(document.all.add_img_input_item.length > 1){this.removeNode(true);}else{alert(language_data['goods_input_quick.php']['I'][language]);}\"-->
											<!--col width='3%'-->
											<col width='7%'>
											<col width='25%'>
											<col width='7%'>
											<col width='25%'>
											<col width='7%'>
											<col width='25%'>
											<tr>
												<!--td><input type=checkbox name='addimages[".($i+1)."][del]' value='1' checked></td-->
												<td><input type=hidden name='addimages[".($i+1)."][ad_ix]' value='".$add_images[$i][id]."' >
													<img src='".$admin_config[mall_data_root]."/images/addimg/b_".$add_images[$i][id]."_add.gif' width=50 align=absmiddle style='border:1px solid gray'>
												</td>
												<td>
													<b >
													이미지복사</b><input type=checkbox name='addimages[".($i+1)."][add_copy_allimg]' onclick=\"copyAddImageCheckAll('add_copy_img_".$i."');\" id='add_copy_img_".($i+1)."' value=1 checked><br>
													<input type=file class='textbox' name='addimages[".($i+1)."][addbimg]' size=28 style='width:98%;vertical-align:middle' value='$option_div'>
												</td>
												<td>
												<img src='".$admin_config[mall_data_root]."/images/addimg/m_".$add_images[$i][id]."_add.gif' width=50 align=absmiddle style='border:1px solid gray'>
												</td>
												<td>
													복사<input type=checkbox name='addimages[".($i+1)."][add_chk_mimg]' id='add_copy_img_".($i+1)."' value=1 checked><br>
													<input type=file class='textbox' name='addimages[".($i+1)."][addmimg]'  style='width:98%' value='$option_price'>
												</td>
												<td>
												<img src='".$admin_config[mall_data_root]."/images/addimg/c_".$add_images[$i][id]."_add.gif' width=50 align=absmiddle style='border:1px solid gray'>
												</td>
												<td>
													복사<input type=checkbox name='addimages[".($i+1)."][add_chk_cimg]' id='add_copy_img_".($i+1)."' value=1 checked><br>
													<input type=file class='textbox' name='addimages[".($i+1)."][addcimg]'  style='width:90%' value='$option_price'> ".($i+1 == 0 ? "<!-- 옵션 삭제 -->":"<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all.add_img_input_item.length > 1){this.parentNode.parentNode.removeNode(true);}else{alert(language_data['goods_input_quick.php']['I'][language]);}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>")."
												</td>
											</tr>
										</table>";
		}
}else{
	$Contents .= "
										<table width=100% id='add_img_input_item' opt_idx=0 cellspacing=4 cellpadding=0 ><!--ondblclick=\"if(document.all.add_img_input_item.length > 1){this.removeNode(true);}else{alert(language_data['goods_input_quick.php']['I'][language]);}\"-->
											<!--col width='3%'-->
											<col width='7%'>
											<col width='25%'>
											<col width='7%'>
											<col width='25%'>
											<col width='7%'>
											<col width='25%'>
											<tr>
												<!--td><input type=checkbox name='addimages[0][addbimg]' value='1' checked></td-->
												<td></td>
												<td>
													<b >이미지복사</b><input type=checkbox name='addimages[0][add_copy_allimg]' onclick=\"copyAddImageCheckAll('add_copy_img_0');\" id='add_copy_img_0' value=1 checked>
													<input type=file class='textbox' name='addimages[0][addbimg]' size=28 style='width:98%;vertical-align:middle' value='$option_div'>
												</td>
												<td></td>
												<td>
													복사함<input type=checkbox name='addimages[0][add_chk_mimg]' id='add_copy_img_0' value=1 checked>
													<input type=file class='textbox' name='addimages[0][addmimg]'  style='width:98%' value='$option_price'>
												</td>
												<td></td>
												<td>
													복사함<input type=checkbox name='addimages[0][add_chk_cimg]' id='add_copy_img_0' value=1 checked>
													<input type=file class='textbox' name='addimages[0][addcimg]'  style='width:90%' value='$option_price'> <!-- 옵션 삭제 -->
												</td>
											</tr>
										</table>";
}
$Contents .= "
									</td>

								</tr>
							</table><br>";



$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>500×500 이미지</b>를 등록한후 이미지 복사를 클릭하시고 저장하시면 <u>300×300, 137×137, 90×90, 50×50</u> 이미지가 자동으로 생성됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >별도의 이미지 복사를 원하시면 이미지복사 체크를 푸신 상태에서 원하시는 이미지를 찾아서 등록하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상품정보의 일괄 관리를 위해서 상품상세 이미지의 경우 외부사이트에서 HTML을 복사해서 넣은경우 복사해온 <u>서버측에서 외부사이트 링크가 허용된 경우</u> <b>이미지가 자동</b>으로 복사되게 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >제품 상세정보페이지에 다른 사이트에 있는 이미지를 붙여 넣고 싶은경우 다른 사이트에 있는 이미지를 드래그해서 넣으면 자동으로 복사 됩니다<br>단, 이미지 원본측에 이미지 복사가 허용된 경우에 한함</td></tr>
</table>
";

$Contents .= HelpBox("상품등록관리", $help_text);



$Contents = $Contents."
			<table width='100%'>
			<tr height=30 align=left><td width=500></td>";


if ($id == "" || $mode == "copy"){
$Contents = $Contents."
				<td align=right><img src='../image/b_save.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'insert');\"></td>";
}else{
$Contents = $Contents."
				<td align=right><img src='../image/b_save.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'update')\"> <img src='../image/b_del.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'delete')\"></td>";
}
$Contents .= "
			<!--td><img src='../image/b_save.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'insert');\"></td>
			<td><img src='../image/b_edit.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'update')\"></td>
			<td><img src='../image/b_del.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'delete')\"-->
			</td></tr>
			</table>
			</form>";
if($_relation_view_type =="small"){
	$ajax_add_string = "tag:'img',overlap:'horizontal',constraint:false, ";
}

//if ($id && $mode != "copy"){
$Script = "<script language='JavaScript' src='../js/scriptaculous.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>\n<script Language='JavaScript' src='../include/zoom.js'></script>\n<script Language='JavaScript' src='addoption.js'></script>\n<script language='JavaScript' src='../js/dd.js'></script>\n
<!--script language='JavaScript' src='../js/mozInnerHTML.js'></script-->
\n<script type='text/javascript' src='../marketting/relationAjaxForEvent.js'></script>\n<script Language='JavaScript' src='../include/DateSelect.js'></script>\n<script Language='JavaScript' src='goods_input_quick.js'></script>\n<script Language='JavaScript' src='buyingService.js'></script>\n
$Script";
$Script .= "
<script type=\"text/javascript\" language=\"JavaScript\" src=\"/js/InnoPU_AP.js\"></script>
<script type=\"text/javascript\" language=\"JavaScript\" src=\"/js/InnoPU.js\"></script>
<script type=\"text/javascript\" language=\"JavaScript\">
<!--
var ip_SaveThumbnailSize = new Array();
ip_SaveThumbnailSize[0] = new Array(".($image_info[0][width] ? $image_info[0][width]:"500").", ".($image_info[0][height] ? $image_info[0][height]:"500").");
ip_ActionFilePath = \"goods_input_quick.act.php\";
ip_SendForm = \"product_input\";
ip_InputName = \"image_file\";
ip_InputType = \"array\";

LoadInnoPU();

isSubmit = false; // 업로드 때 OnAddFile 이벤트 ignore 를 위해...
addPoint = ''; // 멀티 영역 드래그&드롭 처리등에 필요
//-->
</script>

<script type=\"text/javascript\" language=\"JavaScript\" src=\"/js/InnoPU_embed.js\"></script>
<script language=\"JavaScript\">
<!--
InnoAPInit(ip_TotalUploadSize, ip_UnitUploadSize, ip_MaxFileCount, 0, '1', '1');

try {
    document.InnoAP.AppendFilter(\"사진 파일\", \"*.jpg; *.jpeg; *.gif; *.png; *.bmp;\"); // 찾아보기 확장자 필터
    document.InnoAP.AppendDragDrop(\"pu_drop1\", \"1\"); // 드롭영역 태그의 ID 값
    document.InnoAP.AppendDragDrop(\"pu_drop2\", \"2\");
    document.InnoAP.AppendDragDrop(\"pu_drop3\", \"3\");
    document.InnoAP.AppendDragDrop(\"pu_drop4\", \"4\");
    document.InnoAP.AppendDragDrop(\"pu_drop5\", \"5\");
} catch (e) { }

function StartUpload()
{
    // 폼의 유효성 검사등을 여기서 수행

    // 사진설명1/2를 test_f 폼에 입력
 //   document.getElementsByName('pu_desc1')[0].value = document.getElementsByName('pu_desc1')[1].value;
  //  document.getElementsByName('pu_desc2')[0].value = document.getElementsByName('pu_desc2')[1].value;
  //  document.getElementsByName('pu_desc3')[0].value = document.getElementsByName('pu_desc3')[1].value;
  //  document.getElementsByName('pu_desc4')[0].value = document.getElementsByName('pu_desc4')[1].value;
  //  document.getElementsByName('pu_desc5')[0].value = document.getElementsByName('pu_desc5')[1].value;

    // ip_SendForm 변수에 명시된 test_f 폼의 값들을 전송시에 포함
    InnoPUStartUpload_Behind('2');
}
//-->
</script>

<script for=\"InnoAP\" event=\"OnAddFile(strFilePath, intFileSize);\">
<!--
if (!isSubmit)
{
    document.getElementById(\"pu_file\" + addPoint).value = strFilePath;
    document.getElementById(\"pu_exif\" + addPoint).innerHTML = '&nbsp;' + GetExifArray(strFilePath).join('&nbsp;|&nbsp;');
    document.getElementById(\"pu_preview\" + addPoint).ShowImage(strFilePath, 180, 135);
}
//-->
</script>

<script for=\"InnoAP\" event=\"OnUploadComplete(ResponseData);\">
<!--
document.writeln(ResponseData);
//-->
</script>

<script for=\"InnoAP\" event=\"OnBeforeDrop(strFilePath, strInputName);\">
<!--
addPoint = strInputName;
//-->
</script>
";
//}






$P = new LayOut();
$P->addScript = $Script;
if ($id != ""){
	if ($admininfo[admin_level] == 9){
		$P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);";
	}else{
		$P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);";
	}
}else{
	$P->OnloadFunction = "init();";
}

$P->strLeftMenu = product_menu();
$P->Navigation = "HOME > 상품관리 > 상품등록관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();


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
	global $db;

	$sql = "select c.cid,c.cname,c.depth,r.basic, r.rid, r.regdate  from ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_CATEGORY_INFO." c where pid = '$pid' and c.cid = r.cid ";


	$db->query($sql);

	$mString = "<table width=100% cellpadding=0 cellspacing=0 id=objCategory>
										<col width=1>
										<col width=50>
										<col width=545>
										<col>
						";

	if ($db->total == 0){
		//$mString = $mString."<tr bgcolor=#ffffff height=45><td colspan=5 align=center>선택된 카테고리 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr>
				<td class='table_td_white small '><input type='text' name='category[]' id='_category' value='".$db->dt[cid]."' style='display:none'></td>
				<td class='table_td_white small'><input type='radio' name='basic' value='".$db->dt[cid]."' ".($db->dt[basic] == 1 ? "checked":"")."></td>
				<td class='table_td_white small '>".($parent_cname != "" ? $parent_cname." > ":"").$db->dt[cname]."</td>
				<td class='table_td_white'><a href=\"JavaScript:void(0)\" onClick='category_del(this.parentNode.parentNode)'><img src='../image/btc_del.gif' border=0></a></td>
				</tr>";
		}
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString = $mString."</table>";

	return $mString;
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


function PrintOption($pid, $opn_ix =''){
	global $db;

	$sql = "select id, option_div,option_price, option_m_price, option_d_price, option_a_price, option_useprice, option_stock, option_safestock,option_etc1 from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a where pid = '$pid' and opn_ix = '$opn_ix' order by id asc";
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
			<td><a href=\"JavaScript:UpdateOption('".$db->dt[id]."','".str_replace("\"","&quot;",$db->dt[option_div])."','".$db->dt[option_price]."','".$db->dt[option_m_price]."','".$db->dt[option_d_price]."','".$db->dt[option_a_price]."','".$db->dt[option_stock]."','".$db->dt[option_safestock]."','".$db->dt[option_etc1]."')\" ><u>".$db->dt[option_div]."</u></a></td>
			<td>".$db->dt[option_price]."</td>
			<td>".$db->dt[option_m_price]."</td>
			<td>".$db->dt[option_d_price]."</td>
			<td>".$db->dt[option_a_price]."</td>
			<td>".$db->dt[option_stock]."</td>
			<td>".$db->dt[option_safestock]."</td>
			<td>".$db->dt[option_etc1]."</td>
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


function PrintDisplayOption($pid){
	global $db;

	$sql = "select * from ".TBL_SHOP_PRODUCT_DISPLAYINFO." a where pid = '$pid' ";
	$db->query($sql);

	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class='s_td small' >번호</td><td class='m_td small' >추가정보명</td><td class='m_td small' >추가정보내용</td><td class='e_td small' >관리</td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=9 align=center class=small >입력된 상품추가정보 항목이  없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff align=center>
			<td  class=small >".($i+1)."</td>
			<td class=small ><a href=\"JavaScript:UpdateDisplayOption('".$db->dt[dp_ix]."','".$db->dt[dp_title]."','".$db->dt[dp_desc]."')\" ><u>".$db->dt[dp_title]."</u></a></td>
			<td class=small >".$db->dt[dp_desc]."</td>
			<td align=center>
				<a href=\"JavaScript:deleteDisplayOption('delete','".$db->dt[dp_ix]."','$pid')\"><img  src='../image/si_remove.gif' border=0></a>
			</td>
			</tr>
			<tr height=1><td colspan=4 class='dot-x'></td></tr>
			";
		}
	}
	$mString = $mString."</table>";

	return $mString;
}

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
		<Select name=state class=small>
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
<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>노출안함</label>
<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>노출함</label>
<!--input type='radio' name='disp'  id='disp_0' value='2' ".ReturnStringAfterCompare($disp, "2", " checked")."><label for='disp_2'>포인트몰</label>
<input type='radio' name='disp'  id='disp_0' value='3' ".ReturnStringAfterCompare($disp, "3", " checked")."><label for='disp_3'>현금,포인트몰</label>
<input type='radio' name='disp'  id='disp_0' value='9' ".ReturnStringAfterCompare($disp, "9", " checked")."><label for='disp_9'>공동구매</label-->

<!--Select name=state>
	<option value=0 $Selectedstr00>일시품절</option>
	<option value=1 $Selectedstr01>판매중</option>
</Select-->";
}





function relationProductList($pid, $disp_type=""){

	global $start,$page, $orderby, $admin_config, $erpid;

	$max = 105;
	$group_code = 1;
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$db->query("SELECT distinct p.id,p.pname, p.sellprice, p.reserve, rp_ix FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_RELATION_PRODUCT." rp where p.id = rp.rp_pid and rp.pid = '$pid' and p.disp = 1   ");
	$total = $db->total;

	$db->query("SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.reserve, rp_ix  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_RELATION_PRODUCT." rp where p.id = rp.rp_pid and rp.pid = '$pid'  and p.disp = 1 order by rp.vieworder limit $start,$max");




	if ($db->total == 0){
		if($disp_type == "clipart"){

		}else{
			$mString = "<table cellpadding=0 id=tb_relation_product cellspacing=0 width=100%  >
									<col width='60'>
								<col width='*'>
								<col width='60'>";

			$mString .= "</table>";
		}
		//$mString .= "<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'>등록된 HOT STUFF 상품 정보가 없습니다 . <br> 좌측 상품을 이곳으로 드래그하시면 <br>HOT STUFF 상품으로 등록됩니다. </td></tr>";
	}else{
		//$mString = "<ul id='sortlist'>";

		$i=0;
		if($disp_type == "clipart"){
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);

				$mString .= "<div id='_group_product_code_".$db->dt[id]."' pid='".$db->dt[id]."' _select_gorup_code='1' style='border:1px solid #efefef;margin:0 3 3 3;padding:2px;display:inline;width:57px;height:57px;text-align:center' onclick='spoitDIV(this)' ondblclick='this.removeNode(this)'>\n";
				$mString .= "<table id='seleted_tb_".$db->dt[id]."' cellpadding=0 cellspacing=0 border=0 style='display:inline;'>\n";
				$mString .= "<tr>\n";
				$mString .= "<td style='display:none;'></td>\n";
				$mString .= "<td><img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif' title='[".$db->dt[id]."]".$db->dt[pname]."'></td>\n";
				$mString .= "<td style='display:none;'>".$db->dt[pname]."</td>\n";
				$mString .= "<td style='display:none;'><input type='hidden' name='rpid[]' value='".$db->dt[id]."'></td>\n";
				$mString .= "</tr>\n";
				$mString .= "</table>\n";
				$mString .= "</div>\n";

				//$mString .= "<div style='border:1px solid #efefef;margin:0 5 5 0;display:inline;width:50px;height:50px;text-align:center'><img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif' ></div>";

			}
		}else{
	  	$mString = "<!--li id='image_".$db->dt[id]."' -->
							<table width=99% id='tb_relation_product' border=0 >
							<col width='60'>
							<col width='*'>
							<col width='60'>";

			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);

				$mString .= "

							<tr height=27 bgcolor=#ffffff ondblclick=\"$('tb_relation_product').deleteRow(this.rowIndex);\" onclick=\"spoit(this)\" style='background: url(../images/dot.gif) repeat-x left bottom; '>
							<td class=table_td_white align=center style='padding:5px;'>
								<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif'>
							</td>
							<td class=table_td_white>".cut_str($db->dt[pname],30)."</td>
							<td><img src='../image/btc_del.gif' style='cursor:hand;'></td>
							</tr>
							";
			}
			$mString = $mString."</table><!--/li-->";
		}
	}

	//$mString = $mString."</ul>";

	return $mString;

}





function MakerList($company, $cid, $return_type ="")
{
//global $db;

	$mdb = new Database;

	if($cid){
		$mdb->query("SELECT * FROM ".TBL_SHOP_COMPANY." where disp=1 and cid = '$cid' order by company_name asc");
	}else{
		$mdb->query("SELECT * FROM ".TBL_SHOP_COMPANY." where disp=1 order by company_name asc");
	}

	$bl = "<Select name='company' class=small>";
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

function PrintOptionName($pid, $select_opn_ix)
{
	$mdb = new Database;
	$mdb->query("SELECT * FROM ".TBL_SHOP_PRODUCT_OPTIONS." where pid ='$pid'");

	$SelectString = "<Select name='opn_ix' onchange=\"ChangeOptionName('$pid', this);\">";

	if ($mdb->total){
			$SelectString = $SelectString."<option value=''>옵션이름 선택</option>";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			$SelectString = $SelectString."<option value='".$mdb->dt[opn_ix]."' option_kind='".$mdb->dt[option_kind]."'>".$mdb->dt[option_name]."</option>";
		}
	}else{
	$SelectString = $SelectString."<option value=''> 옵션이 없습니다.</option>";
	}

	$SelectString = $SelectString."</Select>";

	return $SelectString;
}
function makeSelectBox($table,$where,$select_name,$msg){
	global $id,$main_inventory;

	$db = new Database;
	$db->query("SELECT * FROM ".$table." ".$where." ");

	$mstring = "<select name='$select_name' class=small>";
	$mstring .= "<option value=''>출고 보관장소를 선택해주세요</option>";
		if($db->total){
			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);
				$mstring .= "<option value='".$db->dt[inventory_code]."' ".($db->dt[inventory_code] == $main_inventory ? "selected":"").">".$db->dt[inventory_name]."</option>";
			}
		}else{
			$mstring .= "<option value=''>".$msg."</option>";
		}
		$mstring .= "</select>";

	return $mstring;
}

?>