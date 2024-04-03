<?
include("../class/layout.class");
//include("inventory.lib.php");

if($max == ""){
	$max = 10; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;

$where ="where ot.".$goods_options_tmp_type." = '".$admininfo[$goods_options_tmp_type]."' ";

if($search_type !="" && $search_text !=""){
	if($search_type=="option_div" || $search_type=="opt_dt_code" ){
		$where .= " and opnt_ix in (select opnt_ix from shop_product_options_detail_tmp odt2 where odt2.".$search_type." like '%".$search_text."%' ) ";
	}else{
		$where .= " and ".$search_type." like '%".$search_text."%' ";
	}
}

if($disp !=""){
	$where .= " and disp = '".$disp."' ";
}

$db->query("SELECT count(*) as total FROM shop_product_options_tmp ot $where");
$db->fetch();
$total = $db->dt[total];

$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max","");

$db->query("SELECT *,(select count(*) as cnt from shop_product_options_detail_tmp odt where odt.opnt_ix=ot.opnt_ix) as opt_dt_cnt FROM shop_product_options_tmp ot $where order by regdate desc LIMIT $start,$max");

$mstring = "
<form name='search_form' method='get'>
<input type='hidden' name='mode' value='search'>
<table width='100%' cellpadding=0 cellspacing=0>
	<tr>
	    <td align='left' colspan=6 > ".GetTitleNavigation("옵션관리", "상품관리 > 상품분류관리 > 옵션관리")."</td>
	</tr>
	<tr height=25>
		<td colspan=2  align='left'  style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class=blk>자주쓰는 옵션 검색하기</b></td>
	</tr>
	<tr>
	    <td align='left' colspan=8 style='padding-bottom:14px;'>
			<table class='box_shadow' style='width:100%;padding-top:5px;'  cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:0px'>
						<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='input_table_box'>
							<col width='15%'>
							<col width='35%'>
							<col width='15%'>
							<col width='35%'>
							<tr>
								<td class='input_box_title' align=left style='padding:0px 0px 0px 10px;font-weight:bold'>  검색어</td>
								<td class='input_box_item' align=left  style='padding-right:5px;margin-top:3px;'>
									<table cellpadding=0 cellspacing=0>
										<tr>
											<td>
												<select name='search_type'  style=\"font-size:12px;\">
													<option value='option_name' ".CompareReturnValue('option_name',$search_type,' selected').">옵션명</option>
													<option value='opt_code' ".CompareReturnValue('opt_code',$search_type,' selected').">옵션코드</option>
													<option value='option_div' ".CompareReturnValue('option_div',$search_type,' selected').">옵션구분명</option>
													<option value='opt_dt_code' ".CompareReturnValue('opt_dt_code',$search_type,' selected').">옵션구분코드</option>
												</select>
											</td>
											<td style='padding-left:5px;'>
												<INPUT id=search_texts  class='textbox' value='".$search_text."' clickbool='false' style=' WIDTH: 180px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
											</td>
											<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
										</tr>
									</table>
								</td>
								<td class='input_box_title' align=left style='padding:0px 0px 0px 10px;font-weight:bold'>목록갯수</td>
								<td class='input_box_item'>
									<input type='radio' name='disp'  id='disp_' value='' ".ReturnStringAfterCompare($disp, "", " checked")."><label for='disp_'>전체</label>
									<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>사용</label>
									<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>사용안함</label>
								</td>
							</tr>
						</table>
					</td>
					<th class='box_06'></th>
				</tr>
				<tr>
					<th class='box_07'></th>
					<td class='box_08'></td>
					<th class='box_09'></th>
				</tr>
			</table>
	    </td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:20px 0 20px 0'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
	</tr>
</table>
</form>

<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
	<col width=5%>
	<col width='20%'>
	<col width='*'>
	<col width=10%>
	<col width=10%>
	<col width=15%>
	<col width=10%>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td'>번호</td>
		<td class='m_td'>등록일자/수정일자</td>
		<td class='m_td'>옵션명</td>
		<td class='m_td'>옵션코드</td>
		<td class='m_td'>옵션구분개수</td>
		<td class='m_td'>사용여부</td>
		<td class='e_td'>관리</td>
		</tr>";

if($db->total){
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);
		
		$no = $total - ($page - 1) * $max - $i;

		if($db->dt[option_kind] == "s"){
			$option_kind_str = "선택옵션";
		}else if($db->dt[option_kind] == "p"){
			$option_kind_str = "가격추가옵션";
		}

		$mstring .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'>".($no)."</td>
					<td class='list_box_td '>".$db->dt[regdate]."<br/>".$db->dt[editdate]."</td>
					<td class='list_box_td list_bg_gray'>".$db->dt[option_name]."</td>
					<td class='list_box_td point'>".$db->dt[opt_code]."</td>
					<td class='list_box_td'>".$db->dt[opt_dt_cnt]."</td>
					<td class='list_box_td list_bg_gray'>".($db->dt[disp] ? "사용":"사용안함")."</td>
					<td class='list_box_td' nowrap>";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
            $mstring .="
					<a href=\"javascript:PoPWindow3('./goods_options_input.php?opnt_ix=".$db->dt[opnt_ix]."',940,700,'goods_options_input')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
                    ";
        }else{
            $mstring.="
                    <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
            ";
        }
        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
            $mstring .="
					<a href=\"javascript:DeleteOptionTmpInfo('".$db->dt[opnt_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
                    ";
        }else{
            $mstring.="
                    <a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
            ";
        }
            $mstring.="
					</td>
				</tr>";
	}
	$mstring .=	"</table>";
	$mstring .=	"<table width='100%' cellpadding=0 cellspacing=0>";
}else{
	$mstring .= "
				<tr height=50><td colspan=7 align=center style='padding:30px 0px;'>등록된 임시 옵션정보가 없습니다.</td></tr>";
}

$mstring .="</table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
$mstring .= "
	<table cellpadding=1 cellspacing=0 width=100% >
		<tr hegiht=30>
			<td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'><a href=\"javascript:PoPWindow3('./goods_options_input.php',940,700,'goods_options_input')\"><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0></a></td>
		</tr>
	</table><br>";
}else{
$mstring .= "
	<table cellpadding=1 cellspacing=0 width=100% >
		<tr hegiht=30>
			<td colspan=5>".$str_page_bar."</td>
		</tr>
	</table><br>";
}

$Contents = $mstring;

$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >귀사에서 관리하는 옵션을 등록 관리하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >자주쓰는 옵션정보를 저장하신 후 상품등록시 선택하여 사용 하실 수 있습니다</td></tr>
</table>
";


$help_text = HelpBox("옵션관리", $help_text);
$Contents .= $help_text;

$Script = "<script language='javascript' >

 function DeleteOptionTmpInfo(opnt_ix){
 	if(confirm('해당 임시옵션 정보를 정말로 삭제 하시겠습니까?')){
		window.frames['act'].location.href='../product/goods_options_input.act.php?opnt_ix='+opnt_ix+'&act=delete';
 	}
}
</script>";
/*
$P = new AdminPage();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
echo $P->AdminFrame();
*/
$P = new LayOut;
$P->addScript = "$Script";
if($pre_type=="inventory"){
	$P->strLeftMenu = inventory_menu();
	$P->Navigation = "재고관리 > 자주쓰는옵션관리";
	$P->title = "자주쓰는 옵션관리";
}else{
	$P->strLeftMenu = product_menu();
	$P->Navigation = "상품관리 > 상품등록 > 옵션관리";
	$P->title = "자주쓰는 옵션관리";
}
$P->strContents = $Contents;
$P->PrintLayOut();

/*
CREATE TABLE IF NOT EXISTS `shop_product_options_tmp` (
  `opnt_ix` int(6) unsigned NOT NULL auto_increment COMMENT '인덱스',
  `option_name` varchar(100) NOT NULL COMMENT '옵션명',
  `option_kind` char(1) NOT NULL COMMENT '옵션종류',
  `option_type` char(1) default '9' COMMENT '옵션타입',
  disp char(1) NOT NULL default '1' COMMENT '옵션사용여부',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY  (`opnt_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='상품 옵션 임시정보'  ;


CREATE TABLE IF NOT EXISTS `shop_product_options_detail_tmp` (
  `opnd_ix` int(10) unsigned NOT NULL auto_increment COMMENT '인덱스',
  `opn_ix` int(6) default NULL COMMENT '옵션인덱스값',
  `option_div` varchar(255) default NULL COMMENT '옵션구분',
  `option_code` varchar(50) default '' COMMENT '옵션오프라인관리코드',
  `option_price` int(4) NOT NULL default '0' COMMENT '옵션가격',
  `option_coprice` int(4) unsigned default '0' COMMENT '옵션공급가',
  `option_stock` int(4) NOT NULL default '0' COMMENT '옵션별재고',
  `option_safestock` int(4) NOT NULL default '0' COMMENT '안전재고',
  `option_etc1` varchar(100) NOT NULL default '',
  `insert_yn` enum('Y','N') default 'Y' COMMENT '수정시구분값',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='상품 옵션상세정보' AUTO_INCREMENT=67869 ;




*/
?>