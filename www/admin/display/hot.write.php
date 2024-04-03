<?
include("../class/layout.class");


$db = new Database;
$db->query("SELECT * FROM shop_recommend where md_ix= '$md_ix'");
$db->fetch();

if($db->total){
	$md_ix = $db->dt[md_ix];
	$md_title = $db->dt[md_title];
	$div_ix = $db->dt[div_ix];
	$md_use_sdate = $db->dt[md_use_sdate];
	$md_use_edate = $db->dt[md_use_edate];
	$disp = $db->dt[disp];
	$act = "update";



	$sDate = date("Y/m/d", mktime(0, 0, 0, substr($db->dt[md_use_sdate],4,2)  , substr($db->dt[md_use_sdate],6,2), substr($db->dt[md_use_sdate],0,4)));
	$eDate = date("Y/m/d",mktime(0, 0, 0, substr($db->dt[md_use_edate],4,2)  , substr($db->dt[md_use_edate],6,2), substr($db->dt[md_use_edate],0,4)));

	$startDate = $md_use_sdate;
	$endDate = $md_use_edate;

}else{
	$act = "insert";
	$md_use_sdate = "";
	$md_use_edate = "";
	$disp = "1";

	$next10day = mktime(0, 0, 0, date("m")  , date("d")+10, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d");
	$eDate = date("Y/m/d",$next10day);

	$startDate = date("Ymd");
	$endDate = date("Ymd",$next10day);
}


$Script = "
<style type='text/css'>
  div#drop_relation_product { width:100%;height:100%;overflow:auto;padding:1px;border:1px solid silver }
  div#drop_relation_product.hover { border:5px dashed #aaa; background:#efefef; }
  table.tb {width:100%;cursor:move;}

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
.productList { list-style-type: none; margin: 0; padding: 0; }
.productList li { cursor:move;margin: 3px 3px 3px 0; padding: 2px; float: left; width: 50px; height:50px;text-align:center;border:1px solid #efefef;}
._productList { list-style-type: none; margin: 0; padding: 0; }
._productList li { position:relative;height:60px;clear:both;background:url(../images/dot.gif) repeat-x bottom;}
</style>

<script type='text/javascript' src='/js/ui/jquery-ui-1.8.9.custom.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.core.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.widget.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.mouse.js'></script>
<script type='text/javascript' src='/admin/js/ms_productSearch.js'></script>

<Script Language='JavaScript'>
function SubmitX(frm){
	//alert(iView.document.body.innerHTML);
	//frm.content.value = iView.document.body.innerHTML;


	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
}




function init(){
	var frm = document.hot_frm;

	//Init(frm);
	onLoadDate('$sDate','$eDate');
}
/*
function onDropAction(mode, md_ix,pid)
{
	//outTip(img3);
	//alert(1);
	//parent.document.frames['act'].location.href='./relation.category2.act.php?mode='+mode+'&md_ix='+md_ix+'&pid='+pid;

}
*/

</Script>";


$Contents = "
<table width='100%' border='0' align='left'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("메인추천상품관리", "마케팅지원 > 메인추천상품관리 ")."</td>
</tr>
  <tr>
    <td valign=top>

        <form name='hot_frm' method='post' onSubmit=\"return SubmitX(this)\" action='hot.act.php' enctype='multipart/form-data'><input type='hidden' name=act value='$act'><input type='hidden' name=md_ix value='$md_ix'>
        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
            <td bgcolor='#6783A8'>
              <table border='0' cellspacing='0' cellpadding='0' width='100%'>
                <tr>
                  <td bgcolor='#ffffff'>
                    <table border='0' cellpadding=3 cellspacing=0 width='100%'>
					<tr height=1><td colspan=2 class=td_underline></td></tr>
						<tr height=28>
                        <td width='25%' bgcolor=#efefef align=left nowrap><img src='/admin/image/ico_dot.gif'> <b>메인 프로모션 분류 <img src='".$required3_path."'></b></td>
                        <td>".getFirstDIV2($div_ix)."</td>
                      </tr>
                      <tr height=1><td colspan=2 class=td_underline></td></tr>
                      <tr height=28>
                        <td width='20%' bgcolor=#efefef align=left nowrap><img src='/admin/image/ico_dot.gif'> <b>메인추천상품관리 제목 <img src='".$required3_path."'></b></td>
                        <td><input class='input' type='text' name='md_title' value='".$db->dt[md_title]."' maxlength='50' style='width:100%' validation=true title='메인추천상품관리 제목'></td>
                      </tr>
                      <tr height=1><td colspan=2 class=td_underline></td></tr>
                      <tr height=27 >
												<td bgcolor='#efefef' align=left><img src='/admin/image/ico_dot.gif'> <b><label for='regdate'>메인추천상품관리 기간</label> <img src='".$required3_path."'></b></td>
												<td align=left >
													<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
														<tr>
															<TD width=210 nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
															<TD width=20 align=center> ~ </TD>
															<TD width=210 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
															</tr>
													</table>
												 </td>
												</tr>
												<tr hegiht=1><td colspan=2 class=td_underline></td></tr>
												<tr height=27>
													<td bgcolor=#efefef align=left><img src='/admin/image/ico_dot.gif'> <b>표시여부 <img src='".$required3_path."'></b> </td>
													<td  >
													<input type='hidden' name='pop' value='1'> <input type='radio' name='disp' id='disp_1' value='1' ".CompareReturnValue("1",$disp,"checked")."> <label for='disp_1' >표시</label> <input type='radio' name='disp' id='disp_0' value='0' ".CompareReturnValue("0",$disp,"checked")."><label for='disp_0' >표시하지 않음</label>
													</td>
												</tr>
												<tr height=1><td colspan=2 class=td_underline></td></tr>
												<tr bgcolor='#ffffff'>
								          <td bgcolor=#efefef><img src='/admin/image/ico_dot.gif'> <b>전시상품 <img src='".$required3_path."'></b></td>
								          <td>
									          <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,1,'productList_1');\"><img src='/admin/images/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
											  <div style='width:100%;padding:5px;' id='group_product_area_1' >".relationProductList($md_ix, "clipart")."</div>
                                              <div style='width:100%;float:left;'><span class=small>* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.</span></div>
								          </td>
								        </tr>
												<tr bgcolor='#F8F9FA'>
													<td colspan=2>";
$Contents .= "
														<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
															<tr>
															  <td bgcolor='D0D0D0' height='1' colspan='4'></td>
															</tr>
															<tr>
																<td colspan=3 align=right style='padding:10px;'><input type=image src='../image/b_save.gif' border=0> <a href='hot_stuff.php'><img src='../image/b_cancel.gif' border=0></a></td>
															</tr>
														 </table>
													</td>
												</tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        </form>

    </td>
  </tr>

  ";


$Contents .= "
  <tr>
    <td align='left'>

    $help_text

    </td>
  </tr>

  <script language='javascript'>
	function setCategory2(mode,cname,cid,depth,pid,search_type,search_text)
		{
			//alert(search_text);
			//outTip(img3);
			parent.document.frames['act'].location.href='./relation.category.act.php?mode='+mode+'&cid='+cid+'&pid='+pid+'&search_type='+search_type+'&search_text='+search_text;
		}
  </script>

  <tr>
  	<td >";

/*
$Contents .= "
		<div class='doong' id='relation_product_area'  style='display:inline;vertical-align:top;height:360px;'   >
		<table bgcolor=#ffffff border=0 cellpadding=0 cellspacing=0 width=100%' >
		<tr height=25>
			<td width='15%'  ><div style='overflow:auto;height:460px;width:200px;border:1px solid silver'><iframe  src='relation.category.php' width=100% height=100% frameborder=0 onmouseover=\"onLoad();\" ></iframe></div></td>
			<td colspan=2 width='100%' valign=top>
			<div id='divarea' style='width:100%;height:460;overflow:auto;padding:0px;padding-left:10px;padding-right:2px;'>
			<table border=0 cellpadding=0 cellspacing=0 width=100% height=100% >
				<tr height=80%>
					<td width=50%><div ondragstart='return false' onselectstart='return false' id='reg_product' style='width:100%;height:100%;overflow:auto;padding:1px;padding-left:10px;padding-right:10px;border:1px solid silver;' align=center ><table width=100% height=100%><tr><td align=center class='small'>좌측카테고리를 선택해주세요</td></tr></table></div></td>
					<td width=50% style='padding:0 0 0 10'>
						<div ondragstart='return false' onselectstart='return false' id='relation_product' style='width:100%;height:100%;overflow:auto;padding:1px;padding-left:10px;padding-right:10px;border:1px solid silver' ondragover=\"this.style.border='3px solid silver';\"
ondragout=\"this.style.border='1px solid silver';\" dropzone='true' ondrop=\"onDropAction('insert','".$md_ix."',arguments[0].id);\" >".relationProductList($md_ix)."</div>
					</td>
				</tr>

			</table>
			</div>";

*/
$Contents .= "
			</td>
		</tr>
		<tr height=20%>
				<td colspan=3 >";
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
<col width=8>
<col width=*>
<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >작업시에는 표시하지 않음으로 선택후 작업하시기 바랍니다.</td></tr>
<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기간이 만료된 메인추천상품관리는 자동으로 노출이 종료됩니다</td></tr>
<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >좌측에 상품카테고리 등록하시면 왼쪽에 선택된 카테고리에 등록된 상품이 나타납니다 </td></tr>
<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >메인추천상품관리 상품으로 등록을 원하시는 상품을 클릭해서 드래그해서 오른쪽창에 Drop 시키면 관련상품으로 등록됩니다</td></tr>
<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 상품의 진열순서는 원하는 위치로 드래그 하시면 됩니다</td></tr>
</table>
";

$help_text = "".HelpBox("<table><tr><td valign=bottom nowrap><b>메인추천상품관리 상품 등록관리</b></td><td><a onclick=\"alert(language_data['hot.write.php']['A'][language]);\" title='관련상품 등록관리 동영 동영상 메뉴얼입니다' style='cursor:pointer;'><img src='../image/movie_manual.gif' align=absmiddle width=26 height=20 style='position:absolute;top:-1px;'></a></td></tr></table>", $help_text, 210)."";

$Contents .= "
  <tr>
    <td align='left' style='padding-bottom:200px;'>
    $help_text

    </td>
  </tr>";

$Contents .= "
  	</td>
  </tr>

</table>
<script type='text/javascript' src='relationAjaxForHot.js'></script>
<script type='text/javascript' src='hot.write.js'></script>
<!--script type='text/javascript'>
Sortable.create('sortlist',
{

	onUpdate: function()
	{
		//alert(Sortable.serialize('sortlist'));
		new Ajax.Request('/admin/marketting/hot.act.php',
		{
			method: 'POST',
			parameters: Sortable.serialize('sortlist')+'&act=vieworder_update&md_ix=$md_ix',
			onComplete: function(transport){
			//alert(transport.responseText);
			}
		});
	}
});
</script-->
<!--script type='text/javascript'>
// 외부로 drag 되는 스크립트 셋팅
Sortable.create('sortlist',
{ghosting:true,constraint:false,
	onUpdate: function()
	{
		alert(Sortable.serialize('sortlist')+'&act=vieworder_update&md_ix=$md_ix&erpid=".serialize($erpid).");
		new Ajax.Request('http://".$HTTP_HOST."/admin/marketting/hot.act.php',
		{
			method: 'post',
			parameters: { data: Sortable.serialize('sortlist')+'&act=vieworder_update&md_ix=$md_ix&erpid=".serialize($erpid)." }
		});
	}
});
</script-->
<!--iframe id='act' frameborder='0' scrolling='no' width='0' height='0' src=''></iframe-->
<form name='lyrstat'><input type='hidden' name='opend' value=''></form>";



$Script = "<script language='JavaScript' src='../js/scriptaculous.js'></script>\n

<!--script language='JavaScript' src='../js/mozInnerHTML.js'></script-->\n

<script language='javascript' src='../include/DateSelect.js'></script>\n
$Script";
$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "HOME > 마케팅지원 > 메인추천상품관리";
$P->OnloadFunction = "init();";//showSubMenuLayer('storeleft');
$P->strLeftMenu = marketting_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();




function relationProductList($md_ix, $disp_type=""){

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

	$sql = "SELECT distinct p.id,p.pname, p.sellprice,p.reserve, md_ix
					FROM ".TBL_SHOP_PRODUCT." p, shop_recommend_product_relation rpr
					where p.id = rpr.pid and rpr.md_ix = '$md_ix' and p.disp = 1   ";
	$db->query($sql);
	$total = $db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, md_ix, rpr_ix, rpr.vieworder
					FROM ".TBL_SHOP_PRODUCT." p, shop_recommend_product_relation rpr
					where p.id = rpr.pid and rpr.md_ix = '$md_ix'  and p.disp = 1 order by rpr.vieworder asc limit $start,$max";
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

function getFirstDIV2($selected=""){
	$mdb = new Database;

	$sql = 	"SELECT *
			FROM shop_recommend_div
			where disp=1 ";

	$mdb->query($sql);

	$mstring = "<select name='div_ix' id='div_ix' validation=true title='메인추천상품 분류'>";
	$mstring .= "<option value=''>메인추천상품 분류</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[div_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[div_ix]."' selected>".$mdb->dt[div_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[div_ix]."'>".$mdb->dt[div_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
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

*/
?>