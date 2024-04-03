<?
include("../class/layout.class");

session_start();

$P = new msLayOut();

$db = new Database;
$rdb = new Database;
if ($id != ""){
	$db->query("update ".TBL_SHOP_PRODUCT." set view_cnt = view_cnt + 1 where id = $id");
	$db->query("SELECT * FROM ".TBL_SHOP_PRODUCT." where id = $id");
		//echo("SELECT * FROM ".TBL_SHOP_PRODUCT." where id = $id");


	if($db->total != 0)
	{
	$db->fetch(0);

	$brand = $db->dt[brand];
	$pcode = $db->dt[pcode];
	$price = $db->dt[sellprice];
	$basicinfo = $db->dt[basicinfo];
	$compose = $db->dt[compose];
	$company = $db->dt[company];
	$spec = $db->dt[spec];
	$".TBL_SHOP_PRODUCT."_name = $db->dt[pname];
	$shotinfo = $db->dt[shotinfo];
	$stock = $db->dt[stock];

	$bl->CommerceLogic($user[code],0,$cid, $id,1,$db->dt[sellprice]);



	if($db->dt[state] == 0 or $db->dt[stock] == 0){
		$strState = "<font color=gray>".($db->dt[sellprice] == 0 ? "문의 요망": DisplayRecommendPrice(DispalyProductPrice($db, "number"),$db->dt[recomm_saveprice])." 원")."</font>&nbsp;&nbsp;&nbsp;<font color=red>sold out</font>";
		$CheckStateJavaScript = "alert('".$db->dt[pname]." '+language_data['pinfo.php']['A'][language]);\nreturn false;";
		//은 sold out 상품 입니다.

		$strState = DispalyProductPrice($db, "text")." ";
	}else{
		$strState = "<font color=gray>".($db->dt[sellprice] == 0 ? "문의 요망": DisplayRecommendPrice(DispalyProductPrice($db, "number"),$db->dt[recomm_saveprice])." 원")."</font>";
		if($db->dt[sellprice] == 0){
			$CheckStateJavaScript = "alert('".$db->dt[pname]." ' + language_data['pinfo.php']['B'][language]);\nreturn false;";
			//의 구매를 원하시면 마이데조로로 문의해 주시기 바랍니다.
		}
		$strState = DispalyProductPrice($db, "text")." ";
	}

	if (!$HISTORY[$id]){
		$HISTORY[$id] = array($cid,$db->dt[pname], $db->dt[sellprice],$PHP_SELF);
		session_register("HISTORY");
	}

	}
}
if ($db->dt[noninterest] == 0){
	$nonInterestString = "일시불";
}else{
	$nonInterestString = $db->dt[noninterest]."개월 (".(number_format($db->dt[sellprice]/$db->dt[noninterest],0))."원 × ".$db->dt[noninterest].")";
}


if ($db->dt["new"] == 1){
	$iconstring = "<img src='/icon/icon_new.gif' border=0 align=absmiddle> ";
}
if ($db->dt[hot] == 1){
	$iconstring = $iconstring."<img src='/icon/icon_hot.gif' border=0 align=absmiddle> ";
}

if ($db->dt[event] == 1){
	$iconstring = $iconstring."<img src='/icon/icon_event.gif' border=0 align=absmiddle> ";
}


/*
$Contents1 = OneLineBox(DisPlaySmallProduct("new", 12, true),"#efefef", "#F8F8F8", 910,480);
$Contents2 = OneLineBox(new_product("hit", 3, false),"#efefef", "#F8F8F8", 540,270);
$Contents3 = OneLineBox(new_product("recommend", 2, false),"#efefef", "#efefef", 360,270);
$Contents4 = OneLineBox(DisplayRecommendProduct("recommend", 2, false),"#efefef", "#efefef", 380,260);
$Contents5 = OneLineBox(PrintMainPoll(),"#efefef", "#ffffff", 220,290);
*/

$OneLineBoxStart = OneLineBoxStart("#efefef", "#efefef", 560,280);
$OneLineBoxEnd = OneLineBoxEnd("#efefef", "#efefef", 560,280);

$OneLineBoxStart2 = OneLineBoxStart("#efefef", "#ffffff", 535,206);
$OneLineBoxEnd2 = OneLineBoxEnd("#efefef", "#ffffff", 535,206);

$OneLineBoxStart3 = OneLineBoxStart("#efefef", "#efefef", 210,480);
$OneLineBoxEnd3 = OneLineBoxEnd("#efefef", "#efefef", 210,480);

$desc_title = colorCirCleBox("#efefef","100%","<div align=center><b>제품 상세정보</b></div>");
//$desc_title = OneLineBox("<div align=center><b>제품 상세정보</b></div>","silver", "#efefef", 690,"30");



//$ms_template = $_SERVER["DOCUMENT_ROOT"]."/shop_templete/".$layout_config[mall_use_templete]."/ms_product.info.htm";
$ms_template = $P->ms_template_path."/ms_product.info.htm";

$tcontent = load_template($ms_template);

if(strrpos($tcontent, "{{MALLSTORY_RECOMMENT_LOOP_START}}")){
	$recommend_tmp   = get_tags("{{MALLSTORY_RECOMMENT_LOOP_START}}","{{MALLSTORY_RECOMMENT_LOOP_END}}",$tcontent);
	$loop_tmp = $recommend_tmp["re-content"];


	$rdb->query("SELECT distinct p.id,p.pname, p.shotinfo, p.sellprice, p.noninterest, p.reserve, r.rid,r.cid  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and p.disp = 1 and p.sellprice != 0 and r.cid = '".$cid."' order by rand() limit 0,4");


	for($i=0;$i < $rdb->total;$i++){
		$rdb->fetch($i);
		$before_pid = $rdb->dt[id];

		$loop_tmp_ing = $loop_tmp;

		$loop_tmp_ing = eregi_replace("{{MALLSTORY_RECOMMEND_PRODUCT_ID}}",$rdb->dt[id], $loop_tmp_ing);
		$loop_tmp_ing = eregi_replace("{{MALLSTORY_RECOMMEND_PRODUCT_CID}}",$rdb->dt[cid], $loop_tmp_ing);
		$loop_tmp_ing = eregi_replace("{{MALLSTORY_RECOMMEND_PRODUCT_NAME}}",cut_str($rdb->dt[pname],28), $loop_tmp_ing);
		$loop_tmp_ing = eregi_replace("{{MALLSTORY_RECOMMEND_PRODUCT_PRICE}}",($rdb->dt[sellprice] == 0 ? "문의 요망": DispalyProductPrice($db, "text")), $loop_tmp_ing);


		$loop_tmp_result .= $loop_tmp_ing;

	}

	$tcontent = substr($tcontent,0,$recommend_tmp["ab-begin"]).$loop_tmp_result.substr($tcontent,$recommend_tmp["ab-end"],strlen($tcontent));
}

$tcontent = eregi_replace("{{MALLSTORY_LEFT_MENU}}",LeftTextMenu(), $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_PRODUCT_NAME}}",$".TBL_SHOP_PRODUCT."_name." ".$iconstring, $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_PRODUCT_PRICE}}",$strState, $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_PRODUCT_PRICE_ONLY}}",$price, $tcontent);

if($user[code]){
	$tcontent = eregi_replace("{{MALLSTORY_PRODUCT_PRICE_STYLE}}","style='display:block;'", $tcontent);
}else{
	$tcontent = eregi_replace("{{MALLSTORY_PRODUCT_PRICE_STYLE}}","style='display:none;'", $tcontent);
}


$tcontent = eregi_replace("{{MALLSTORY_PRODUCT_COMPANY}}",$company, $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_PRODUCT_ID}}",$id, $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_PRODUCT_CODE}}",$pcode, $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_PRODUCT_CID}}",$cid, $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_PRODUCT_STOCK}}",$stock, $tcontent);


$tcontent = eregi_replace("{{MALLSTORY_PRODUCT_NONINTEREST}}",$nonInterestString, $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_PRODUCT_SHOTINFO}}",$shotinfo, $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_PRODUCT_DESC}}",$basicinfo, $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_DESIGN_ONELINEBOX_START}}",$OneLineBoxStart, $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_DESIGN_ONELINEBOX_END}}",$OneLineBoxEnd, $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_DESIGN_ONELINEBOX_START2}}",$OneLineBoxStart2, $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_DESIGN_ONELINEBOX_END2}}",$OneLineBoxEnd2, $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_DESIGN_ONELINEBOX_START3}}",$OneLineBoxStart3, $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_DESIGN_ONELINEBOX_END3}}",$OneLineBoxEnd3, $tcontent);

$Contents4 = DisplayRecommendProduct("recommend", 4, false);
//$Contents4 = OneLineBox(DisplayRecommendProduct("recommend", 4, false),"#efefef", "#efefef", 220,1100);
$tcontent = eregi_replace("{{MALLSTORY_RECOOMEND_PRODUCT}}",$Contents4, $tcontent);

/*
$tcontent = eregi_replace("{{MALLSTORY_CHECK_JAVASCRIPT}}",$CheckStateJavaScript, $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_CHECK_OPTION_JAVASCRIPT}}",CheckOptionJavaScript($id), $tcontent);
// 자바스크립트 수정 일단 공백으로 처리
*/
$tcontent = eregi_replace("{{MALLSTORY_CHECK_JAVASCRIPT}}","", $tcontent);
$tcontent = eregi_replace("{{MALLSTORY_CHECK_OPTION_JAVASCRIPT}}","", $tcontent);

$tcontent = eregi_replace("{{MALLSTORY_PRODUCT_OPTION}}",MakeOption($id), $tcontent);

$tcontent = eregi_replace("{{MALLSTORY_DESC_TITLE}}",$desc_title, $tcontent);

$displayinfo_tmp   = get_tags("{{MALLSTORY_PRODUCT_DISPLAYINFO_LOOP_START}}","{{MALLSTORY_PRODUCT_DISPLAYINFO_LOOP_END}}",$tcontent);
$display_loop_tmp = $displayinfo_tmp["re-content"];

$rdb->query("SELECT *  FROM ".TBL_SHOP_PRODUCT_DISPLAYINFO." where pid = '$id' ");


for($i=0;$i < $rdb->total;$i++){
	$rdb->fetch($i);
	$before_pid = $rdb->dt[id];

	$loop_tmp_ing = $display_loop_tmp;

        $loop_tmp_ing = eregi_replace("{{MALLSTORY_PRODUCT_DISPLAYINFO_IX}}",$rdb->dt[dp_ix], $loop_tmp_ing);
	$loop_tmp_ing = eregi_replace("{{MALLSTORY_PRODUCT_DISPLAYINFO_TITLE}}",$rdb->dt[dp_title], $loop_tmp_ing);
	$loop_tmp_ing = eregi_replace("{{MALLSTORY_PRODUCT_DISPLAYINFO_DESC}}",$rdb->dt[dp_desc], $loop_tmp_ing);

	$diaplay_loop_tmp_result .= $loop_tmp_ing;

}

$tcontent = substr($tcontent,0,$displayinfo_tmp["ab-begin"]).$diaplay_loop_tmp_result.substr($tcontent,$displayinfo_tmp["ab-end"],strlen($tcontent));

if($user[code] == ""){
	$tcontent = eregi_replace("{{AFTER_STYLE_DISPLAY}}","style='display:none;'", $tcontent);
}else{
	$tcontent = eregi_replace("{{AFTER_STYLE_DISPLAY}}","style='display:block;'", $tcontent);
}


if(strrpos($tcontent, "{{AFTER_LIST_LOOP_START}}")){
	$after_tmp   = get_tags("{{AFTER_LIST_LOOP_START}}","{{AFTER_LIST_LOOP_END}}",$tcontent);
	$loop_tmp = $after_tmp["re-content"];


	$db->query("SELECT *  FROM ".TBL_SHOP_BBS_USEAFTER."  where pid ='$id'");

	if($db->total){
		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
			$loop_tmp_ing = $loop_tmp;

		        $loop_tmp_ing = eregi_replace("{{AFTER_NO}}",$db->dt[uf_ix], $loop_tmp_ing);
		        $loop_tmp_ing = eregi_replace("{{AFTER_CONTENTS}}",$db->dt[uf_contents], $loop_tmp_ing);
			$loop_tmp_ing = eregi_replace("{{AFTER_NAME}}",$db->dt[uf_name], $loop_tmp_ing);
			$loop_tmp_ing = eregi_replace("{{AFTER_DATE}}",$db->dt[regdate], $loop_tmp_ing);
			$loop_tmp_ing = eregi_replace("{{AFTER_HIT}}",$db->dt[uf_hit], $loop_tmp_ing);

			if($db->dt[ucode] == $user[code]){
				$loop_tmp_ing = eregi_replace("{{AFTER_STYLE_DLETE}}","", $loop_tmp_ing);
			}else{
				$loop_tmp_ing = eregi_replace("{{AFTER_STYLE_DLETE}}","style='display:none;'", $loop_tmp_ing);
			}


			$after_loop_tmp_result .= $loop_tmp_ing;

		}

		$tcontent = substr($tcontent,0,$after_tmp["ab-begin"]).$after_loop_tmp_result.substr($tcontent,$after_tmp["ab-end"],strlen($tcontent));

	}else{
		$after_loop_tmp_result = "<tr height=50><td colspan=10 align=center>등록된 후기가 없습니다.</td></tr>";
		$tcontent = substr($tcontent,0,$after_tmp["ab-begin"]).$after_loop_tmp_result.substr($tcontent,$after_tmp["ab-end"],strlen($tcontent));
	}
}

$product_image_path = $P->ms_product_imgpath;
$templet_path = $P->ms_template_webpath;
$tcontent = eregi_replace("@_","\$", $tcontent);
$tcontent = eregi_replace("\"","\\\"", $tcontent);
eval("\$tcontent = \"".$tcontent."\";");

$P->Contents = $tcontent;
$P->shop_left = "";
echo $P->LoadLayOut();



function CheckOptionJavaScript($pid){
	$mdb = new Database;

	//$sql = "select id,option_name, option_div,option_price,option_m_price,option_d_price,option_a_price, option_useprice from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a where pid = '$pid' ";
	$sql = "select id,option_name, option_div,option_price from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a where pid = '$pid' ";
	$mdb->query($sql);

	$mString = "";
	if ($mdb->total != 0){

		$i=0;
		for($i=0;$i<$mdb->total;$i++){
			$mdb->fetch($i);
			$mString = $mString."

			if(document.pinfo.option".($i+1).".value == 0){
				alert('".$mdb->dt[option_name]." ' + language_data['pinfo.php']['C'][language]);
				return false;
				//을 선택하세요
			}

			";
		}
	}

	return $mString;
}


function DisplayRecommendProduct($type, $pcnt=4, $multi_line=true)
{
	global $db;
	if($type == "hit"){
		$db->query("SELECT distinct p.id,p.pname, p.shotinfo, p.sellprice, p.noninterest, p.reserve, r.rid,r.cid  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and p.disp = 1 and p.sellprice != 0 order by rand() limit 0,$pcnt");
		//$db->query("SELECT distinct p.id,p.pname, p.sellprice,p.noninterest, p.reserve, r.rid,r.cid  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and p.disp = 1 and p.sellprice != 0 order by vieworder limit 0,$pcnt");
	}elseif($type = "new" || $type = "recommend"){
		$db->query("SELECT distinct p.id,p.pname, p.shotinfo, p.sellprice, p.noninterest, p.reserve, r.rid,r.cid  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and p.disp = 1 and p.sellprice != 0 order by rand() limit 0,$pcnt");
	}
	//echo("SELECT distinct p.id,p.pname, p.sellprice,p.noninterest, p.reserve  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and p.new = 1 order by vieworder limit 0,5");



	$before_pid = -1;
	$strNP = "<table cellpadding=0 cellspacing=0 width='100%' border=0>";

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
			$before_pid = $db->dt[id];



$strNP .= "<tr>
			<td width=180 valign=top >
					<table cellpadding=3 cellspacing=0 border=0 height=120 >
						<tr>
						<td align=center height=120 width=180 >
						<a href='/shop/goods_view.php?id=".$db->dt[id]."&cid=".$db->dt[cid]."'><img src='/shop/images/product/s_".$db->dt[id].".gif'  border=0></a>
						</td>
						</tr>
						<tr>
						<td>
						<table cellpadding=0 cellspacing=0>
						<tr><td align=left style='padding-top:5px;'><a href='/shop/goods_view.php?id=".$db->dt[id]."&cid=".$db->dt[cid]."'><b>".cut_str($db->dt[pname],28)."</b></a></td></tr>
						<tr><td align=left style='color:orange;font-weight:bold'>".($db->dt[sellprice] == 0 ? "문의 요망": number_format(DispalyProductPrice($db, "number"),0)." 원")."  ".$product01."</td></tr>
						<tr><td align=left>".cut_str($db->dt[shotinfo],130)." </td></tr>
						<tr><td align=left nowrap> </td></tr>
						</table>
						</td>
						</tr>

					</table>
			</td>
		</tr>";



		}

$strNP .= "	</table>";

	return $strNP;
}

?>