<?
include("../class/layout.class");
include("sellertool.lib.php");
include("../openapi/openapi.lib.php");

//echo 11;

$db = new Database;
$sql = "select * from sellertool_category_linked_relation where clr_ix = '$clr_ix'";
//echo $sql;

$db->query($sql);
$db->fetch();


if($db->total){
	$basic_info = $db->dt;
	//print_r($basic_info);
    $sql = "select * from sellertool_category_linked_relation as clr 
    			left join ".$category_table." as ci on clr.target_cid = ci.cid  
    			where clr_ix = '".$clr_ix."'
    			";
    
    $db->query("$sql "); //where uid = '$code'
    
    if($db->total){
    	$db->fetch();
    	$relation_info = $db->dt;
    	$act = "update";
    }else{//relation값에 따른 루틴에서 exception
        $act = "insert";
    }
	

}else{
    $act = "insert";
	$basic_info[origin_cid] = $cid;
	$basic_info[origin_depth] = $depth;
}


$Contents01 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr >
		<td align='left' colspan=4> ".GetTitleNavigation("카테고리 연동 정보 수정", "상품관리 > 카테고리연동 정보 수정 ")."</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>카테고리 연동 정보 수정</b></div>")."</td>
	</tr>
</table>
<form name='category_linked_edit_form' method='get' action='category.linked.act.php' style='' target='act'>
	<input type='hidden' name='act' value='$act'>
	<input type='hidden' name='clr_ix' value='$clr_ix'>
	<input type='hidden' name='cid2' value='".$relation_info[target_cid]."'>
	<input type='hidden' name='depth' value='".$relation_info[target_depth]."'>
	<input type='hidden' name='cname' value='".$basic_info[target_name]."'>
	<input type='hidden' name='origin_depth' value='".$basic_info[origin_depth]."'>
	<input type='hidden' name='origin_cid' value='".$basic_info[origin_cid]."'>
    <input type='hidden' name='site_name' value='".$basic_info[sitename]."'>
	<input type='hidden' name='cla_ix' value='$cla_ix'>
    <input type='hidden' name='clr_ix' value='".$relation_info[clr_ix]."'>

	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
		<tr>
			<td colspan=2>
				<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
					<col width='25%' />
					<col width='30%' />
					<col width='25%' />
					<col width='30%' />
					<tr>
								<td class='input_box_title'>제휴사 선택</td>
								<td class='input_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'>
												".getSellerToolSiteInfo($site_code,"","text")."
											</td>
										</tr>
									</table>
								</td>
							</tr>
					<tr>
						<td class='input_box_title'>카테고리선택</td>
						<td class='input_box_item' colspan=3 style='padding:5px;'>
							<table border=0 cellpadding=0 cellspacing=0>
								<tr ".($basic_info[target_name] == "" ? "style='display:none;'":"").">
									<td colspan=5 style='padding:8px 0px;'>선택된 카테고리 : <b class=blk>".$basic_info[target_name]."</b></td>
								</tr>
								<tr>
									<td style='padding-right:5px;'>".getLinkCategory($site_code,"대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1','".$site_code."')\" title='대분류' ", 0)."</td>
									<td style='padding-right:5px;'>".getLinkCategory($site_code,"중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1','".$site_code."')\" title='중분류'", 1)."</td>
									<td style='padding-right:5px;'>".getLinkCategory($site_code,"소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1','".$site_code."')\" title='소분류'", 2)."</td>
									<td style='padding-right:5px;'>".getLinkCategory($site_code,"세분류", "cid3_1", "onChange=\"loadCategory(this,'cid4_1','".$site_code."')\" title='세분류'", 3)."</td>
									<td>".getLinkCategory($site_code,"상세분류", "cid4_1", "onChange=\"loadCategory(this,'cid2', '".$site_code."')\" title='상세분류'", 4)."</td>
								</tr>
							</table>
						</td>
					</tr>
				";
				
$Contents01 .=	"				
				
				</table>
			</td>
		</tr>
		<tr>
			<td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
		</tr>
		<tr>
			<td colspan=2 align=center style='padding:10px 0px;'>
			
			</td>
		</tr>
	</table>
</form>";


$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >카테고리 매핑시 11번가의 경우 최종뎁스까지(3~4) 선택 필수</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >카테고리 변경을 원하시면 변경을 원하시는 카테고리를 선택후 저장하시면 카테고리가 변경됩니다.</td></tr>
	</table>
	";


$help_text = HelpBox("제휴사(오픈마켓) 관리", $help_text);
$Contents01 = $Contents01.$help_text;

$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";

$Contents = $Contents."</table >";

 $Script = "
 <script Language='JavaScript' type='text/javascript'>
	function loadCategory(sel,target,site_code ) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		var obj = sel.name;
		var depth = sel.getAttribute('depth');
		//console.log('category.load.php?form=' + form + '&obj=' + obj + '&trigger=' + trigger + '&depth='+depth+'&target=' + target +'&site_code='+site_code);

		//var depth = sel.depth;//kbk
		
		//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		//document.write('category.load.php?form=' + form + '&obj=' + obj + '&trigger=' + trigger + '&depth='+depth+'&target=' + target +'&site_code='+site_code);
		window.frames['act'].location.href = 'category.load.php?form=' + form + '&obj=' + obj + '&trigger=' + trigger + '&depth='+depth+'&target=' + target +'&site_code='+site_code;

	}

	
	</script>
 ";

if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->NaviTitle = "카테고리 연동 정보 수정";
	$P->Navigation = "상품관리 > 상품분류관리 > 카테고리 연동 수정";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->title = "카테고리 연동 정보 수정";
	$P->Navigation = "상품관리 > 상품분류관리 > 카테고리 연동 수정";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}
?>