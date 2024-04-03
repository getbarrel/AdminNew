<?
include("../class/layout.class");

if($_GET["type"] == "minishop"){
	// 미니샵이라면 getCategoryMinishopMultipleSelect 함수 호출
	$selectFunction1 = getCategoryMinishopMultipleSelect("--1차분류--", "cid0", "cid","onChange=\"loadCategory(this,'cid1',2)\" title='1차분류' ", 0, $cid);
	$selectFunction2 = getCategoryMinishopMultipleSelect("--2차분류--", "cid1",  "cid","onChange=\"loadCategory(this,'cid2',2)\" title='2차분류'", 1, $cid);
	$selectFunction3 = getCategoryMinishopMultipleSelect("--3차분류--", "cid2", "cid", "onChange=\"loadCategory(this,'cid3',2)\" title='3차분류'", 2, $cid);
	$selectFunction4 = getCategoryMinishopMultipleSelect("--4차분류--", "cid3", "cid", "", 3, $cid);

	$loadAct = "&type=".$_GET["type"]; // loadCategory 함수에서 미니샵 파라미터를 넘김
}else{
	// 미니샵이 아니라면 getCategoryMultipleSelect 함수 호출
	$selectFunction1 = getCategoryMultipleSelect("--1차분류--", "cid0", "cid","onChange=\"loadCategory(this,'cid1',2)\" title='1차분류' ", 0, $cid);
	$selectFunction2 = getCategoryMultipleSelect("--2차분류--", "cid1",  "cid","onChange=\"loadCategory(this,'cid2',2)\" title='2차분류'", 1, $cid);
	$selectFunction3 = getCategoryMultipleSelect("--3차분류--", "cid2", "cid", "onChange=\"loadCategory(this,'cid3',2)\" title='3차분류'", 2, $cid);
	$selectFunction4 = getCategoryMultipleSelect("--4차분류--", "cid3", "cid", "", 3, $cid);

	$loadAct = "";
}

$db = new Database;

$Script = "
<style type='text/css'>
  div#drop_relation_product { width:100%;height:100%;overflow:auto;padding:1px;border:1px solid silver }
  div#drop_relation_product.hover { border:5px dashed #aaa; background:#efefef; }
  table.tb {width:100%;cursor:move;}
</style>
<Script Language='JavaScript'>


function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	//var depth = sel.depth; // 호환성 kbk
	var depth = sel.getAttribute('depth');
	

	if(sel.selectedIndex!=0) {
		// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
		//document.write('./goods_category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
		window.frames['act'].location.href = './goods_category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target +'".$loadAct."';
	}

}

function categoryadd(group_code)
{
	var ret;
	var str = new Array();
	var obj = document.event_frm.cid;
	var depth;//추가 kbk 13/07/01
	for (i=0;i<obj.length;i++){
		if (obj[i].value){
			str[str.length] = obj[i][obj[i].selectedIndex].text;
			ret = obj[i].value;
			depth=i;
		}
	}
	if (!ret){
		alert(language_data['category_select.php']['A'][language]);//'카테고리를 선택해주세요'
		return;
	}
	//var cate = document.all._category;
	var cate=document.getElementsByName('category[\"+group_code+\"][]'); // 호환성 kbk
	//alert(cate.length);

	//if(is_array([cate])){
		//alert(cate.length);
		for(i=0;i < cate.length;i++){
			//alert(ret +'=='+ cate[i].value);
			//alert(cate[i].value);
			if(ret == cate[i].value){
				alert(language_data['category_select.php']['B'][language]);//'이미등록된 카테고리 입니다.'
				return;
			}
		}
	//}

	//cate.unshift(ret);
	var obj = opener.document.getElementById('objCategory_'+group_code);
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
	oTd.innerHTML = \"<input type=text name=category[\"+group_code+\"][] id='_category' value='\" + ret + \"' style='display:none'>\";
	oTd.innerHTML += \"<input type=text name=depth[\"+group_code+\"][] id='_depth' value='\" + depth + \"' style='display:none'>\";//추가 kbk 13/07/01
	oTd = oTr.insertCell(-1);
			//oTd.className = '';
	if(window.addEventListener) oTd.setAttribute('class','');
	else oTd.className = '';
			//if(oTr.rowIndex == 0){
			//	oTd.innerHTML = \"<input type=radio name='basic[\"+group_code+\"]' value='\"+ ret + \"' checked>\";
			//}else{
			//	oTd.innerHTML = \"<input type=radio name='basic[\"+group_code+\"]' value='\"+ ret + \"'>\";
			//}
			//oTd = oTr.insertCell(-1);
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
	else oTd.className = 'table_td_white';
	oTd.align = 'right';
	oTd.innerHTML = \" <a href='javascript:void(0)' onClick='category_del(".$group_code.",this.parentNode.parentNode)'><img src='../images/".$admininfo["language"]."/btc_del.gif'  border=0></a>\";


}

function category_del(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objCategory');
	obj.deleteRow(idx);
	var cObj=\$('input[name=basic]');
	if(cObj.length == null){
		//cObj[0].checked = true; // 0이 나오지 null이 나오지 않음 kbk
	}else{
		for(var i=0;i<cObj.length;i++){
			if(cObj[i].checked){
				return true;
				break;
			}else{
				cObj[0].checked = true;
			}
		}
	}
	//cate.splice(idx,1);
}


</Script>";

$Contents = "
<table width='100%' border='0' align='left'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("카테고리 선택", "전시관리 > 카테고리 선택 ")."</td>
</tr>
  <tr>
    <td>

        <form name='event_frm' method='post' onSubmit=\"return SubmitX(this)\" style='display:inline;' enctype='multipart/form-data'>
		<input type='hidden' name=act value='$act'><input type='hidden' name=event_ix value='$event_ix'>
        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
            <td >
             <div style='padding:20px 0px;'>
			  <table width=100% border=0 cellpadding=0 cellspacing=0 >
					<col width='25%'>
					<col width='25%'>
					<col width='25%'>
					<col width='25%'>
					<tr>
						<td style='padding-right:2px;'>".$selectFunction1." </td>
						<td style='padding-right:2px;'>".$selectFunction2." </td>
						<td style='padding-right:2px;'>".$selectFunction3." </td>
						<td>".$selectFunction4."</td>
						<td style='padding-left:10px'><img src='../images/".$admininfo["language"]."/category_add.gif' align=absmiddle border=0 onclick=\"categoryadd('".($group_code)."')\" style='cursor:pointer;'></td>
					</tr>
				</table>
				
			</div>
            </td>
          </tr>
        </table>
        </form>
    </td>
  </tr>
</table>
  ";
  /*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >작업시에는 표시하지 않음으로 선택후 작업하시기 바랍니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 <u>이벤트는 </u> 사용으로 되어 있는 이벤트만 <a href='/event/promotion_list.php' target='_blank'>http://$HTTP_HOST/event/promotion_list.php</a> 에서 확인 하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기간이 만료된 이벤트는 자동으로 노출이 종료됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기획전 미리보기는 변경된 내용을 저장하신 후 사용하셔야 합니다.</td></tr>
</table>
";*/

//	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

//$help_text = HelpBox("이벤트/기획전  관리", $help_text);
//$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td><b>이벤트/기획전 관리</b></td><td></td></tr></table>", $help_text,160);


if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->Navigation = "HOME > 전시관리 > 카테고리 선택";
	$P->NaviTitle = "카테고리 선택";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->Navigation = "HOME > 전시관리 > 카테고리 선택";
	$P->OnloadFunction = "";//showSubMenuLayer('storeleft');
	$P->strLeftMenu = display_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


function FileList2 ( $path , $maxdepth = -1 , $mode = "FULL" , $d = 0 ){
global $page_name;
   if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }
   $dirlist = array () ;
   //if ( $mode != "FILES" ) { $dirlist[] = $path ; }
   if(!is_dir($path)){return false;};
   if ( $handle = opendir ( $path ) )
   {

       while ( false !== ( $file = readdir ( $handle ) ) )
       {
           if ( $file != '.' && $file != '..' )
           {
               $only_file = $file;
               $file = $path . $file ;
               if ( ! is_dir ( $file ) || $mode == "FULL"){
               		if(is_dir ( $file )){
               			//$mstring .=  "<option value='".$only_file ."'>".$only_file ."</option>";
               		}else{
               			if($page_name == $only_file){
               				$mstring .=  "<option value='".$only_file ."' selected>".$only_file ."</option>";
               			}else{
               				$mstring .=  "<option value='".$only_file ."'>".$only_file ."</option>";
               			}
               		}

               }elseif ($d >=0 && ($d < $maxdepth || $maxdepth < 0) ){
                   $mstring .= FileList2 ( $file . '/' , $maxdepth , $mode , $d + 1 ) ;
                  $mstring .=  "<option value='".Icon($only_file,"path",filetype($file))."'>".$only_file ."</option>";
               }
           }
       }
       closedir ( $handle ) ;
   }
   if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
   return ( $mstring ) ;
}

function SelectFileList2($path){
	global $DOCUMENT_ROOT, $mod, $SubID, $mmode;
	if($path == ""){
		$path = $_SERVER["DOCUMENT_ROOT"]."/data/sample/templet/basic";
	}

	$mstring =  "<select name='page_name' onchange=\"document.location.href='design.mod.php?SubID=$SubID&mod=$mod&page_name='+this.value+'&mmode=$mmode'\">";
	if(FileList2($path, 0, "FULL")){
		$mstring .= FileList2($path, 0, "FULL");
	}else{
		$mstring .= "<option>파일이 존재하지않습니다.</option>";
	}
	$mstring .=  "</select>";

	return $mstring;
}

function SelectEventCate($category){
	$db = new Database;

	$sql = "SELECT * FROM shop_event_relation ORDER BY regdate ";
	$db->query($sql);
	$cateArr = $db->fetchall();

	$mstring =  "<select name='er_ix'>";
	$mstring .=  "<option value=''>선택하세요.</option>";
	if(is_array($cateArr)){
		foreach($cateArr as $_KEY=>$_VALUE) {
			$mstring .= "<option value='".$_VALUE[er_ix]."' ".($_VALUE[er_ix] == $category ? " selected ":"").">".$_VALUE[title]."</option>";
		}
	}
	$mstring .=  "</select>";

	return $mstring;
}
SelectEventCate('123');

function relationEventGroupProductList($event_ix, $group_code, $disp_type=""){

	global $start,$page, $orderby, $admin_config, $erpid;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$sql = "SELECT p.id,p.pname, p.sellprice,  p.reserve, event_ix , p.brand_name
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix' and group_code = '$group_code' and p.disp = 1   ";
	$db->query($sql);
	$total = $db->total;

	$sql = "SELECT p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, event_ix, erp_ix, erp.vieworder, erp.group_code, p.brand_name
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix' and group_code = '$group_code' and p.disp = 1 order by erp.vieworder asc limit $start,$max";
	//echo $sql."<br><br>";
	$db->query($sql);


	if ($db->total == 0){
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>';
		}
	}else{
		$i=0;
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>'."\n";
			$mString .= '<script>'."\n";
			$mString .= 'ms_productSearch.groupCode = '.$group_code.";\n";
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);
				$imgPath = PrintImage($admin_config['mall_data_root'].'/images/product', $db->dt['id'], 'c');
				$mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$db->dt['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($db->dt['pname']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['sellprice'].'");'."\n";
			}
			$mString .= '</script>'."\n";
		}
	}
	return $mString;

}

function relationProductList2($event_ix){

	global $start,$page, $orderby, $admin_config;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$sql = "SELECT distinct p.id,p.pname, p.sellprice,  p.reserve, event_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix' and p.disp = 1   ";
	$db->query($sql);
	$total = $db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.reserve, event_ix, erp_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix'  and p.disp = 1 order by erp.vieworder limit $start,$max";
	$db->query($sql);




	if ($db->total){

		$mString = "<div id='sortlist'>";

		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString .= "<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif' id='image_".$db->dt[id]."' title='".cut_str($db->dt[pname],30)."' ondblclick=\"document.frames['act'].location.href='relation.category.act.php?mode=delete&pid=".$pid."&rp_ix=".$db->dt[rp_ix]."'\"width=50 height=50 style='border:1px solid silver' vpace=2 hspace=2>";
		}
	}
	$mString .= "</div>";

	return $mString;

}

function relationProductList($event_ix, $disp_type=""){

	global $start,$page, $orderby, $admin_config, $erpid;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$sql = "SELECT distinct p.id,p.pname, p.sellprice,  p.reserve, event_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix' and p.disp = 1   ";
	$db->query($sql);
	$total = $db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, event_ix, erp_ix, erp.vieworder, erp.group_code
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix'  and p.disp = 1 order by erp.vieworder asc limit $start,$max";
	$db->query($sql);




	if ($db->total == 0){
		$mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";
		$mString = $mString."<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'><!--등록된 이벤트/기획전 상품 정보가 없습니다 . <br> 좌측 상품을 이곳으로 드래그하시면 <br>이벤트/기획전 상품으로 등록됩니다. --> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')."</td></tr>";
		$mString .= "</table>";
	}else{
//		$mString = "<ul id='sortlist' >";

		$i=0;
		if($disp_type == "clipart"){
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);

				$mString .= "<div id='seleted_tb_".$db->dt[id]."' style='border:1px solid #efefef;margin:0 5 5 0;display:inline;width:50px;height:50px;text-align:center'>\n";
				$mString .= "<table id='seleted_tb_".$db->dt[id]."' cellpadding=0 cellspacing=0 border=0 style='display:inline;'>\n";
				$mString .= "<tr>\n";
				$mString .= "<td style='display:none;'></td>\n";
				$mString .= "<td><img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif' ></td>\n";
				$mString .= "<td style='display:none;'>".$db->dt[pname]."</td>\n";
				$mString .= "<td style='display:none;'><input type='hidden' name='rpid[".$db->dt[group_code]."][]' value='"+spid+"'></td>\n";
				$mString .= "</tr>\n";
				$mString .= "</table>\n";
				$mString .= "</div>\n";

			}
		}else{
	  	$mString .= "<!--li id='image_".$db->dt[id]."' -->
							<table width=100% cellpadding=0 cellspacing=0 id=tb_relation_product class=tb border=0 >
							<col width='60'>
							<col width='*'>
							<col width='60'>";

			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);
				//ondblclick=\"$('tb_relation_product').deleteRow(this.rowIndex);$('tb_relation_product').deleteRow(this.rowIndex);	\"
				$mString .= "<tr height=27 bgcolor=#ffffff onclick=\"spoit(this)\" ondblclick=\"$('tb_relation_product').deleteRow(this.rowIndex);\" style='background: url(../images/dot.gif) repeat-x left bottom; '>
							<td class=table_td_white align=center style='padding:5px;'>
								<div style='border:1px solid #efefef;margin:0 5 5 0;display:inline;width:50px;height:50px;text-align:center'><img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif'></div>
							</td>
							<td class=table_td_white>".cut_str($db->dt[pname],30)."<br>".number_format($db->dt[sellprice])."</td>
							<td><input type='hidden' name='rpid[]' value='".$db->dt[id]."'><!--a href='relation.category.act.php?mode=delete&event_ix=".$event_ix."&erp_ix=".$db->dt[erp_ix]."'  target=act><img src='../image/btc_del.gif'></a--></td>
							</tr>
							";
				//$mString .= "</li>";
			}
			$mString .= "</table>";
		}
	}

	//$mString = $mString."</ul>";

	return $mString;

}


function relationProductList_backup($event_ix){

	global $start,$page, $orderby, $admin_config, $erpid;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$sql = "SELECT distinct p.id,p.pname, p.sellprice,  p.reserve, event_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix' and p.disp = 1   ";
	$db->query($sql);
	$total = $db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, event_ix, erp_ix, erp.vieworder
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix'  and p.disp = 1 order by erp.vieworder asc limit $start,$max";
	$db->query($sql);




	if ($db->total == 0){
		$mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";
		$mString = $mString."<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'><!--등록된 이벤트/기획전 상품 정보가 없습니다 . <br> 좌측 상품을 이곳으로 드래그하시면 <br>이벤트/기획전 상품으로 등록됩니다. -->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'G')."</td></tr>";
	}else{
		$mString = "<ul id='sortlist'>";

		$i=0;



		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);

			$mString .= "<li id='image_".$db->dt[id]."' >
						<table width=99% border=0 >
						<col width='60'>
						<col width='*'>
						<col width='60'>
						<tr height=27 bgcolor=#ffffff >
						<td class=table_td_white align=center style='padding:5px;'>
							<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif'>
						</td>
						<td class=table_td_white>".cut_str($db->dt[pname],30)."</td>
						<td><a href='relation.category.act.php?mode=delete&event_ix=".$event_ix."&erp_ix=".$db->dt[erp_ix]."'  target=act><img src='../images/".$admininfo["language"]."/btc_del.gif'></a></td>
						</tr><tr height=1><td colspan=5 class=td_underline></td></tr>
						</table></li>";
		}
	}

	$mString = $mString."</ul>";

	return $mString;

}

/*
CREATE TABLE `shop_event_product_relation` (
  `erid` int(10) unsigned zerofill NOT NULL auto_increment,
  `event_ix` int(4) unsigned zerofill NOT NULL default '',
  `pid` int(6) unsigned zerofill default NULL,
  `disp` char(1) default '1',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`erid`)
) TYPE=MyISAM COMMENT='이벤트/기획전 상품등록';

CREATE TABLE `shop_event_product_relation` (
  `erp_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `pid` int(6) unsigned zerofill NOT NULL default '000000',
  `event_ix` int(4) unsigned zerofill NOT NULL default '',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`erp_ix`)
) TYPE=MyISAM COMMENT='이벤트/기획전 상품등록' ;

// 김수현 110916 추가
ALTER TABLE `shop_event` ADD `er_ix` INT( 8 ) UNSIGNED ZEROFILL NULL COMMENT '이벤트기획전 카테고리' AFTER `cid` ;

ALTER TABLE `shop_event` ADD `kind` ENUM( 'E', 'P' ) NOT NULL DEFAULT 'E' COMMENT '이벤트/기획전 구분' AFTER `er_ix`;

CREATE TABLE IF NOT EXISTS `shop_event_relation` (
  `er_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `title` varchar(150) default NULL COMMENT '카테고리 명',
  `file` varchar(150) default NULL COMMENT '파일명',
  `use_yn` enum('Y','N') default 'Y' COMMENT '사용유무',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`er_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='이벤트/기획전 카테고리'
*/
?>