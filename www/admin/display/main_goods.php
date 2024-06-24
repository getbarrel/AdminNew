<?
/*
메인프로모션 분류 추가로 인해 이미지명이 중복되어 저장되므로 분류 코드를 이미지명 앞에 붙여서 중복 저장을 막음(기존에 쓰던 방식은 그대로 유지) kbk 13/05/09
*/
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

include("../class/layout.class");
include("../display/display.lib.php");
include("../display/main_display.lib.php");
if($admininfo[mall_type] == "H"){
	header("Location:./popup.list.php");
}

$sql = "SELECT * FROM shop_content_main WHERE conm_ix ='".$conm_ix."' ";

$slave_db->query($sql); //AND cid='$cid'
if($slave_db->total){
	$slave_db->fetch();
	$mall_ix 				= $slave_db->dt[mall_ix];
	$subject 				= $slave_db->dt[subject];
	$explanation 			= $slave_db->dt[explanation];
	$special_use			= $slave_db->dt[special_use];

	$special_title			= $slave_db->dt[special_title];
	$special_title_en		= $slave_db->dt[special_title_en];
	$b_special				= $slave_db->dt[b_special];
	$i_special				= $slave_db->dt[i_special];
	$u_special				= $slave_db->dt[u_special];
	$c_special				= $slave_db->dt[c_special];
	$s_special				= $slave_db->dt[s_special];

	$special_e				= $slave_db->dt[special_e];
	$special_e_en			= $slave_db->dt[special_e_en];
	$b_special_e			= $slave_db->dt[b_special_e];
	$i_special_e			= $slave_db->dt[i_special_e];
	$u_special_e			= $slave_db->dt[u_special_e];
	$c_special_e			= $slave_db->dt[c_special_e];

	$best_use				= $slave_db->dt[best_use];
	$best_title				= $slave_db->dt[best_title];
	$best_title_en			= $slave_db->dt[best_title_en];
	$b_best					= $slave_db->dt[b_best];
	$i_best					= $slave_db->dt[i_best];
	$u_best					= $slave_db->dt[u_best];
	$c_best					= $slave_db->dt[c_best];
	$s_best					= $slave_db->dt[s_best];
	$best_e					= $slave_db->dt[best_e];
	$best_e_en				= $slave_db->dt[best_e_en];
	$b_best_e				= $slave_db->dt[b_best_e];
	$i_best_e				= $slave_db->dt[i_best_e];
	$u_best_e				= $slave_db->dt[u_best_e];
	$c_best_e				= $slave_db->dt[c_best_e];
	$bast_cate				= $slave_db->dt[bast_cate];

	$best_use2				= $slave_db->dt[best_use2];
	$best_title2			= $slave_db->dt[best_title2];
	$best_title_en2			= $slave_db->dt[best_title_en2];
	$b_best2				= $slave_db->dt[b_best2];
	$i_best2				= $slave_db->dt[i_best2];
	$u_best2				= $slave_db->dt[u_best2];
	$c_best2				= $slave_db->dt[c_best2];
	$s_best2				= $slave_db->dt[s_best2];
	$best_e2				= $slave_db->dt[best_e2];
	$best_e_en2				= $slave_db->dt[best_e_en2];
	$b_best_e2				= $slave_db->dt[b_best_e2];
	$i_best_e2				= $slave_db->dt[i_best_e2];
	$u_best_e2				= $slave_db->dt[u_best_e2];
	$c_best_e2				= $slave_db->dt[c_best_e2];
	$bast_cate2				= $slave_db->dt[bast_cate2];

	$best_use3				= $slave_db->dt[best_use3];
	$best_title3			= $slave_db->dt[best_title3];
	$best_title_en3			= $slave_db->dt[best_title_en3];
	$b_best3				= $slave_db->dt[b_best3];
	$i_best3				= $slave_db->dt[i_best3];
	$u_best3				= $slave_db->dt[u_best3];
	$c_best3				= $slave_db->dt[c_best3];
	$s_best3				= $slave_db->dt[s_best3];
	$best_e3				= $slave_db->dt[best_e3];
	$best_e_en3				= $slave_db->dt[best_e_en3];
	$b_best_e3				= $slave_db->dt[b_best_e3];
	$i_best_e3				= $slave_db->dt[i_best_e3];
	$u_best_e3				= $slave_db->dt[u_best_e3];
	$c_best_e3				= $slave_db->dt[c_best_e3];
	$bast_cate3				= $slave_db->dt[bast_cate3];

	$best_use4				= $slave_db->dt[best_use4];
	$best_title4			= $slave_db->dt[best_title4];
	$best_title_en4			= $slave_db->dt[best_title_en4];
	$b_best4				= $slave_db->dt[b_best4];
	$i_best4				= $slave_db->dt[i_best4];
	$u_best4				= $slave_db->dt[u_best4];
	$c_best4				= $slave_db->dt[c_best4];
	$s_best4				= $slave_db->dt[s_best4];
	$best_e4				= $slave_db->dt[best_e4];
	$best_e_en4				= $slave_db->dt[best_e_en4];
	$b_best_e4				= $slave_db->dt[b_best_e4];
	$i_best_e4				= $slave_db->dt[i_best_e4];
	$u_best_e4				= $slave_db->dt[u_best_e4];
	$c_best_e4				= $slave_db->dt[c_best_e4];
	$bast_cate4				= $slave_db->dt[bast_cate4];

	$best_use5				= $slave_db->dt[best_use5];
	$best_title5			= $slave_db->dt[best_title5];
	$best_title_en5			= $slave_db->dt[best_title_en5];
	$b_best5				= $slave_db->dt[b_best5];
	$i_best5				= $slave_db->dt[i_best5];
	$u_best5				= $slave_db->dt[u_best5];
	$c_best5				= $slave_db->dt[c_best5];
	$s_best5				= $slave_db->dt[s_best5];
	$best_e5				= $slave_db->dt[best_e5];
	$best_e_en5				= $slave_db->dt[best_e_en5];
	$b_best_e5				= $slave_db->dt[b_best_e5];
	$i_best_e5				= $slave_db->dt[i_best_e5];
	$u_best_e5				= $slave_db->dt[u_best_e5];
	$c_best_e5				= $slave_db->dt[c_best_e5];
	$bast_cate5				= $slave_db->dt[bast_cate5];

	$best_use6				= $slave_db->dt[best_use6];
	$best_title6			= $slave_db->dt[best_title6];
	$best_title_en6			= $slave_db->dt[best_title_en6];
	$b_best6				= $slave_db->dt[b_best6];
	$i_best6				= $slave_db->dt[i_best6];
	$u_best6				= $slave_db->dt[u_best6];
	$c_best6				= $slave_db->dt[c_best6];
	$s_best6				= $slave_db->dt[s_best6];
	$best_e6				= $slave_db->dt[best_e6];
	$best_e_en6				= $slave_db->dt[best_e_en6];
	$b_best_e6				= $slave_db->dt[b_best_e6];
	$i_best_e6				= $slave_db->dt[i_best_e6];
	$u_best_e6				= $slave_db->dt[u_best_e6];
	$c_best_e6				= $slave_db->dt[c_best_e6];
	$bast_cate6				= $slave_db->dt[bast_cate6];

	$best_use7				= $slave_db->dt[best_use7];
	$best_title7			= $slave_db->dt[best_title7];
	$best_title_en7			= $slave_db->dt[best_title_en7];
	$b_best7				= $slave_db->dt[b_best7];
	$i_best7				= $slave_db->dt[i_best7];
	$u_best7				= $slave_db->dt[u_best7];
	$c_best7				= $slave_db->dt[c_best7];
	$s_best7				= $slave_db->dt[s_best7];
	$best_e7				= $slave_db->dt[best_e7];
	$best_e_en7				= $slave_db->dt[best_e_en7];
	$b_best_e7				= $slave_db->dt[b_best_e7];
	$i_best_e7				= $slave_db->dt[i_best_e7];
	$u_best_e7				= $slave_db->dt[u_best_e7];
	$c_best_e7				= $slave_db->dt[c_best_e7];
	$bast_cate7				= $slave_db->dt[bast_cate7];

	$best_use8				= $slave_db->dt[best_use8];
	$best_title8			= $slave_db->dt[best_title8];
	$best_title_en8			= $slave_db->dt[best_title_en8];
	$b_best8				= $slave_db->dt[b_best8];
	$i_best8				= $slave_db->dt[i_best8];
	$u_best8				= $slave_db->dt[u_best8];
	$c_best8				= $slave_db->dt[c_best8];
	$s_best8				= $slave_db->dt[s_best8];
	$best_e8				= $slave_db->dt[best_e8];
	$best_e_en8				= $slave_db->dt[best_e_en8];
	$b_best_e8				= $slave_db->dt[b_best_e8];
	$i_best_e8				= $slave_db->dt[i_best_e8];
	$u_best_e8				= $slave_db->dt[u_best_e8];
	$c_best_e8				= $slave_db->dt[c_best_e8];
	$bast_cate8				= $slave_db->dt[bast_cate8];

	$best_use9				= $slave_db->dt[best_use9];
	$best_title9			= $slave_db->dt[best_title9];
	$best_title_en9			= $slave_db->dt[best_title_en9];
	$b_best9				= $slave_db->dt[b_best9];
	$i_best9				= $slave_db->dt[i_best9];
	$u_best9				= $slave_db->dt[u_best9];
	$c_best9				= $slave_db->dt[c_best9];
	$s_best9				= $slave_db->dt[s_best9];
	$best_e9				= $slave_db->dt[best_e9];
	$best_e_en9				= $slave_db->dt[best_e_en9];
	$b_best_e9				= $slave_db->dt[b_best_e9];
	$i_best_e9				= $slave_db->dt[i_best_e9];
	$u_best_e9				= $slave_db->dt[u_best_e9];
	$c_best_e9				= $slave_db->dt[c_best_e9];
	$bast_cate9				= $slave_db->dt[bast_cate9];

	$best_use10				= $slave_db->dt[best_use10];
	$best_title10			= $slave_db->dt[best_title10];
	$best_title_en10		= $slave_db->dt[best_title_en10];
	$b_best10				= $slave_db->dt[b_best10];
	$i_best10				= $slave_db->dt[i_best10];
	$u_best10				= $slave_db->dt[u_best10];
	$c_best10				= $slave_db->dt[c_best10];
	$s_best10				= $slave_db->dt[s_best10];
	$best_e10				= $slave_db->dt[best_e10];
	$best_e_en10			= $slave_db->dt[best_e_en10];
	$b_best_e10				= $slave_db->dt[b_best_e10];
	$i_best_e10				= $slave_db->dt[i_best_e10];
	$u_best_e10				= $slave_db->dt[u_best_e10];
	$c_best_e10				= $slave_db->dt[c_best_e10];
	$bast_cate10			= $slave_db->dt[bast_cate10];

	$style_use				= $slave_db->dt[style_use];

	$style_title			= $slave_db->dt[style_title];
	$style_title_en			= $slave_db->dt[style_title_en];
	$b_style				= $slave_db->dt[b_style];
	$i_style				= $slave_db->dt[i_style];
	$u_style				= $slave_db->dt[u_style];
	$c_style				= $slave_db->dt[c_style];
	$s_style				= $slave_db->dt[s_style];
	$style_e				= $slave_db->dt[style_e];
	$style_e_en				= $slave_db->dt[style_e_en];
	$b_style_e				= $slave_db->dt[b_style_e];
	$i_style_e				= $slave_db->dt[i_style_e];
	$u_style_e				= $slave_db->dt[u_style_e];
	$c_style_e				= $slave_db->dt[c_style_e];


	$journal_use			= $slave_db->dt[journal_use];

	$journal_title			= $slave_db->dt[journal_title];
	$journal_title_en		= $slave_db->dt[journal_title_en];
	$b_journal				= $slave_db->dt[b_journal];
	$i_journal				= $slave_db->dt[i_journal];
	$u_journal				= $slave_db->dt[u_journal];
	$c_journal				= $slave_db->dt[c_journal];
	$s_journal				= $slave_db->dt[s_journal];
	$journal_e				= $slave_db->dt[journal_e];
	$journal_e_en			= $slave_db->dt[journal_e_en];
	$b_journal_e			= $slave_db->dt[b_journal_e];
	$i_journal_e			= $slave_db->dt[i_journal_e];
	$u_journal_e			= $slave_db->dt[u_journal_e];
	$c_journal_e			= $slave_db->dt[c_journal_e];

	$journal_frame_img		= $slave_db->dt[journal_frame_img];


	$content_use			= $slave_db->dt[content_use];

	$content_title			= $slave_db->dt[content_title];
	$content_title_en		= $slave_db->dt[content_title_en];
	$b_content				= $slave_db->dt[b_content];
	$i_content				= $slave_db->dt[i_content];
	$u_content				= $slave_db->dt[u_content];
	$c_content				= $slave_db->dt[c_content];
	$s_content				= $slave_db->dt[s_content];
	$content_e				= $slave_db->dt[content_e];
	$content_e_en			= $slave_db->dt[content_e_en];
	$b_content_e			= $slave_db->dt[b_content_e];
	$i_content_e			= $slave_db->dt[i_content_e];
	$u_content_e			= $slave_db->dt[u_content_e];
	$c_content_e			= $slave_db->dt[c_content_e];

	$main_use				= $slave_db->dt[main_use];
	$main_default			= $slave_db->dt[main_default];
	$main_start				= date("Y-m-d H:i:s",$slave_db->dt[main_start]);
	$main_end				= date("Y-m-d H:i:s",$slave_db->dt[main_end]);
}

function relationContentList($conm_ix, $gubun, $disp_type=""){
	global $admin_config;
	$db = new Database;

	if($gubun == 1){
		$content_gubun = "E";
	}else if($gubun == 2){
		$content_gubun = "S";
	}else if($gubun == 3){
		$content_gubun = "C";
	}

	$sql = "SELECT c.con_ix, c.title, c.list_img 
            FROM shop_content c, shop_content_main_content cmc 
            WHERE c.con_ix = cmc.con_ix AND cmc.conm_ix = '" . $conm_ix . "' AND content_gubun = '".$content_gubun."'
            ORDER BY cmc.sort ASC 
    ";

	$db->query($sql);
	$products = $db->fetchall();

	if ($db->total == 0) {
		if ($disp_type == "clipart") {
			$mString = '';
		}
	} else {
		$i = 0;
		if ($disp_type == "clipart") {
			$mString = '';
			for ($i = 0; $i < count($products); $i++) {
				$mString .= '<li id="li_contentImage_'.$gubun.'_'.$products[$i]['con_ix'].'" vieworder="'.$products[$i]['con_ix'].'" viewcnt="'.$products[$i]['con_ix'].'" style=float:left;width:110px;>' . "\n";
				$mString .= '<table width=95% cellspacing=1 cellpadding=0 style=table-layout:fixed;height:180px;>' . "\n";
				$mString .= '<tr><td align=center>' . "\n";
				$mString .= '<img src="'.$_SESSION["admin_config"][mall_data_root].'/images/content/'.$products[$i]['con_ix'].'/'.$products[$i]['list_img'].'" width=100px height=100px>' . "\n";
				$mString .= '<br>'.nl2br($products[$i]['title']).'' . "\n";
				$mString .= '<input type=hidden name=con_ix_'.$gubun.'[] id=con_ix_'.$products[$i]['con_ix'].' value="'.$products[$i]['con_ix'].'">' . "\n";
				$mString .= '</td></tr>' . "\n";
				$mString .= '<tr><td align=center><button type=button onclick=imgDel("'.$products[$i]['con_ix'].'",'.$gubun.')>삭제</td></tr>' . "\n";
				$mString .= '</table></li>' . "\n";
			}

		}
	}
	return $mString;
}

function relationGroupContentList($cmgr_ix, $group_code, $disp_type=""){
	global $admin_config;
	$db = new Database;

	/*$sql = "SELECT c.con_ix, c.title, c.list_img, cmgrc.group_con_gubun
            FROM shop_content c, shop_content_main_group_content_relation cmgrc 
            WHERE c.con_ix = cmgrc.con_ix AND cmgrc.cmgr_ix = '" . $cmgr_ix . "' 
            ORDER BY cmgrc.sort ASC 
    ";*/

	$sql = "SELECT con_ix, title, list_img, group_con_gubun, sort FROM (
				(SELECT c.con_ix, c.title, c.list_img, cmgrc.group_con_gubun, cmgrc.sort FROM 
				shop_content c, 
				shop_content_main_group_content_relation cmgrc 
				WHERE c.con_ix = cmgrc.con_ix AND cmgrc.cmgr_ix = '" . $cmgr_ix . "' AND cmgrc.group_con_gubun = 'S' 
				order by cmgrc.sort asc)
				UNION ALL
				(SELECT b.banner_ix as con_ix, b.banner_name as title, b.banner_img as list_img, cmgrc.group_con_gubun, cmgrc.sort FROM 
				shop_bannerinfo b, 
				shop_content_main_group_content_relation cmgrc 
				WHERE b.banner_ix = cmgrc.con_ix AND cmgrc.cmgr_ix = '" . $cmgr_ix . "' AND cmgrc.group_con_gubun = 'B'
				order by cmgrc.sort asc)
			) AS a order by sort asc
	";

	$db->query($sql);
	$products = $db->fetchall();

	if ($db->total == 0) {
		if ($disp_type == "clipart") {
			$mString = '';
		}
	} else {
		$i = 0;
		if ($disp_type == "clipart") {
			$mString = "";
			for ($i = 0; $i < count($products); $i++) {
				if($products[$i]['group_con_gubun'] == 'S'){
					$gubun = 2;
					$name = '스타일';
					$img = $_SESSION["admin_config"][mall_data_root].'/images/content/'.sprintf('%010d', $products[$i]['con_ix']).'/'.$products[$i]['list_img'];
				}else if($products[$i]['group_con_gubun'] == 'B'){
					$gubun = 4;
					$name = '배너';
					$img = $_SESSION["admin_config"][mall_data_root].'/images/banner/'.$products[$i]['con_ix'].'/'.$products[$i]['list_img'];
				}

				$mString .= '<li id="li_contentImage_'.$group_code.'_'.$gubun.'_'.$products[$i]['con_ix'].'" vieworder="'.$products[$i]['con_ix'].'" viewcnt="'.$products[$i]['con_ix'].'" style=float:left;width:110px;>' . "\n";
				$mString .= '<table width=95% cellspacing=1 cellpadding=0 style=table-layout:fixed;height:180px;>' . "\n";
				$mString .= '<tr><td class=small style=background-color:gray;color:#ffffff;height:25%;width:100%;text-align:center; nowrap>'.$name.' 이미지</td></tr>' . "\n";
				//$mString .= '<tr><td align=center>'.$name.'<br>' . "\n";
				$mString .= '<tr><td align=center>' . "\n";
				$mString .= '<img src="'.$img.'" width=100px height=100px>' . "\n";
				$mString .= '<br>'.nl2br($products[$i]['title']).'' . "\n";
				$mString .= '<input type=hidden name=group_con_ix['.$group_code.'][] id=group_con_ix_'.$products[$i]['con_ix'].' value="'.$products[$i]['con_ix'].'">' . "\n";
				$mString .= '<input type=hidden name=group_con_gubun['.$group_code.'][] id=group_con_ix_'.$products[$i]['con_ix'].' value="'.$products[$i]['group_con_gubun'].'">' . "\n";
				$mString .= '</td></tr>' . "\n";
				$mString .= '<tr><td align=center><button type=button onclick=imgGroupDel("'.$products[$i]['con_ix'].'",'.$gubun.','.$group_code.')>삭제</td></tr>' . "\n";
				$mString .= '</table></li>' . "\n";
			}
			$mString .= '<script>' . "\n";
			$mString .= '$("#choiceGorupContent_'.$group_code.'").sortable();'."\n";
			$mString .= '</script>' . "\n";
		}
	}
	return $mString;
}


$Script = "
<style type='text/css'>
    .slide-up-down-link { display:block;position:absolute;right:55px;top:13px; }
	.slide-up-down-link .plus { display:none; }
	.slide-up-down-link.closed .minus { display:none; }
	.slide-up-down-link.closed .plus { display:inline; }
	.slide-up-down-all { margin-bottom:10px; }
	.slide-up-down-all .slide-up-down-link { 
		position:static; 
		font-weight:bold;
	}
	.slide-up-down-all .slide-up-down-link:hover { text-decoration:none; }
	.slide-up-down-all .slide-up-down-link .label {
		display: inline-block;
		font-size:18px;
		margin-top:2px;
		vertical-align:top;
		*display:inline;
		*zoom:1;
	}
</style>
<script type='text/javascript' src='/admin/js/ui/jquery-ui-1.8.9.custom.js'></script>
<script type='text/javascript' src='/admin/js/ui/jquery.ui.core.js'></script>
<script type='text/javascript' src='/admin/js/ui/jquery.ui.widget.js'></script>
<script type='text/javascript' src='/admin/js/ui/jquery.ui.mouse.js'></script-->
<script type='text/javascript' src='/admin/js/jquery.easing-1.3.js'></script>
<script type='text/javascript' src='/admin/js/jquery.quicksand.js'></script>
<script type='text/javascript' src='../js/ms_productSearch.js'></script>
<script language='javascript' src='./color/jscolor.js'></script>
<script type='text/javascript' src='/admin/js/jquery.multisortable.js'></script>
<script Language='JavaScript'>
// let's set defaults for all color pickers
jscolor.presets.default = {
	width: 141,               // make the picker a little narrower
	position: 'right',        // position it to the right of the target
	previewPosition: 'right', // display color preview on the right
	previewSize: 40,          // make the color preview bigger
	palette: [
		'#000000', '#7d7d7d', '#870014', '#ec1c23', '#ff7e26',
		'#fef100', '#22b14b', '#00a1e7', '#3f47cc', '#a349a4',
		'#ffffff', '#c3c3c3', '#b87957', '#feaec9', '#ffc80d',
		'#eee3af', '#b5e61d', '#99d9ea', '#7092be', '#c8bfe7',
	],
};

$(function() {
    $('#itemBoxWrap').sortable({
        placeholder:'itemBoxHighlight',
        start: function(event, ui) {
            ui.item.data('start_pos', ui.item.index());
        }
    });
    $( '#choiceSpecial' ).sortable();

    $( '#choiceStyle' ).sortable();

    $( '#choiceContent' ).sortable();
});



function loadCategory(obj,target,num) {
    console.log(obj);
    console.log(target);
    console.log(num);
	var trigger = obj.find('option:selected').val();
	var form = obj.closest('form').attr('name');
	var depth = obj.attr('depth');//sel.getAttribute('depth');
	$('form[name=form_cupon]').find('input[name=selected_depth]').val(trigger) ;
	$('form[name=form_cupon]').find('input[name=selected_depth]').val(depth) ;

	$.ajax({ 
			type: 'GET', 
			data: {'return_type': 'json', 'form':form, 'trigger':trigger, 'depth':depth, 'target':target},
			url: '../product/category.load.php',  
			dataType: 'json', 
			async: true, 
			beforeSend: function(){ 
				
			},  
			success: function(datas){
                if(num == 1){
					$('select[class=cid1]').each(function(){
						if(parseInt($(this).attr('depth')) > parseInt(depth)){
							$(this).find('option').not(':first').remove();
						}
					});    
                }else if(num == 2){
                    $('select[class=cid2]').each(function(){
						if(parseInt($(this).attr('depth')) > parseInt(depth)){
							$(this).find('option').not(':first').remove();
						}
					});
                }else if(num == 3){
                    $('select[class=cid3]').each(function(){
						if(parseInt($(this).attr('depth')) > parseInt(depth)){
							$(this).find('option').not(':first').remove();
						}
					});
                }else if(num == 4){
                    $('select[class=cid4]').each(function(){
						if(parseInt($(this).attr('depth')) > parseInt(depth)){
							$(this).find('option').not(':first').remove();
						}
					});
                }else if(num == 5){
                    $('select[class=cid5]').each(function(){
						if(parseInt($(this).attr('depth')) > parseInt(depth)){
							$(this).find('option').not(':first').remove();
						}
					});
                }else if(num == 6){
                    $('select[class=cid6]').each(function(){
						if(parseInt($(this).attr('depth')) > parseInt(depth)){
							$(this).find('option').not(':first').remove();
						}
					});
                }else if(num == 7){
                    $('select[class=cid7]').each(function(){
						if(parseInt($(this).attr('depth')) > parseInt(depth)){
							$(this).find('option').not(':first').remove();
						}
					});
                }else if(num == 8){
                    $('select[class=cid8]').each(function(){
						if(parseInt($(this).attr('depth')) > parseInt(depth)){
							$(this).find('option').not(':first').remove();
						}
					});
                }else if(num == 9){
                    $('select[class=cid9]').each(function(){
						if(parseInt($(this).attr('depth')) > parseInt(depth)){
							$(this).find('option').not(':first').remove();
						}
					});
                }else if(num == 10){
                    $('select[class=cid10]').each(function(){
						if(parseInt($(this).attr('depth')) > parseInt(depth)){
							$(this).find('option').not(':first').remove();
						}
					});
                }
				
				 
				if(datas != null){
					$.each(datas, function(i, data){ 
							$('select[name='+target+']').append(\"<option value='\"+data.cid+\"'>\"+data.cname+\"</option>\");
					});  
				}
			} 
		});  
}

function imgDel(val, gubun){
	$('#li_contentImage_'+gubun+'_'+val).remove();
}

function imgGroupDel(val, gubun, groupNum){
	$('#li_contentImage_'+groupNum+'_'+gubun+'_'+val).remove();
}
</Script>";

$Contents = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("컨텐츠관리", "메인페이지 관리 > 메인페이지 등록")."</td>
	</tr>";
$Contents .= "
	<tr>
    	<td>
			<form name='main_frm' method='POST' onSubmit=\"return CheckFormValue(this)\" action='../display/main_goods.act.php' style='display:inline;' target='calcufrm' enctype='multipart/form-data'><!--SubmitX-->
			<input type='hidden' name=act value='".($act == "" ? "insert":"update")."'>
			<input type='hidden' name=conm_ix value='".$conm_ix."'>

			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05'  valign=top style='padding:0px'>
						<table border='0' cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
						<col width='15%'>
						<col width='35%'>
						<col width='15%'>
						<col width='35%'>
							<tr>
								<td class='search_box_title' > 프론트 전시 구분</td>
								<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." <span class=small>전시 구분을 선택하실경우 해당 사이트에 노출됩니다.</span></td>
							</tr>
							<tr>
								<td class='search_box_title' nowrap> <b>메인페이지 제목</b></td>
								<td class='search_box_item' colspan=3>
									<input class='textbox' type='text' name='subject' value='".$subject."' validation=true title='메인페이지 제목' maxlength='50' style='width:400px'>
								</td>
							</tr>
							<tr>
								<td class='search_box_title' nowrap> <b>메인페이지 간단설명</b></td>
								<td class='search_box_item' colspan=3>
									<input class='textbox' type='text' name='explanation' value='".$explanation."' maxlength='50' style='width:400px'>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>추천기획전 사용유무</b></td>
								<td class='search_box_item' colspan=3>
									<input type='radio' class='textbox' name='special_use' id='special_use_y' size=50 value='Y' checked style='border:0px;' ".($special_use == "Y" ? "checked":"")."><label for='special_use_y'> 사용</label>
									<input type='radio' class='textbox' name='special_use' id='special_use_n' size=50 value='N' style='border:0px;' ".($special_use == "N" ? "checked":"")."><label for='special_use_n'> 미사용</label>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>추천기획전 제목</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='special_title' id='special_title' style='width:85%;height:15px'>".$special_title."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='special_title_en' id='special_title_en' style='width:85%;height:15px'>".$special_title_en."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>추천기획전 제목 설정</b></td>
								<td class='search_box_item'>
									<input type='radio' name='s_special' id='s_special_L' value='L' ".("L" == $s_special || "" == $s_special  ? "checked":"")."><label for='s_special_L'> 좌측정렬</label>
									<input type='radio' name='s_special' id='s_special_C' value='C' ".("C" == $s_special ? "checked":"")."><label for='s_special_C'> 가운데정렬</label>
									<input type='radio' name='s_special' id='s_special_R' value='R' ".("R" == $s_special ? "checked":"")."><label for='s_special_R'> 우측정렬</label><br><br>
									진하게<input type='checkbox' name='b_special' id='b_special' ".("Y" == $b_special ? "checked":"").">
									기울기<input type='checkbox' name='i_special' id='i_special' ".("Y" == $i_special ? "checked":"").">
									밑줄<input type='checkbox' name='u_special' id='u_special' ".("Y" == $u_special ? "checked":"").">
									글자색 <input type='text' name='c_special' id='c_special' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_special ? "#000000":$c_special)."'>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>추천기획전 간단설명</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='special_e' id='special_title' style='width:85%;height:15px'>".$special_e."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='special_e_en' id='special_title_en' style='width:85%;height:15px'>".$special_e_en."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>추천기획전 간단설명 설정</b></td>
								<td class='search_box_item'>
									진하게<input type='checkbox' name='b_special_e' id='b_special_e' ".("Y" == $b_special_e ? "checked":"").">
									기울기<input type='checkbox' name='i_special_e' id='i_special_e' ".("Y" == $i_special_e ? "checked":"").">
									밑줄<input type='checkbox' name='u_special_e' id='u_special_e' ".("Y" == $u_special_e ? "checked":"").">
									글자색 <input type='text' name='c_special_e' id='c_special_e' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_special_e ? "#000000":$c_special_e)."'>
								</td>
							</tr>
							<tr>
								<td class='search_box_title' nowrap> <b>추천기획전 불러오기</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 style=width:100%;>
                                    <col width='225px'>
                                    <col width='*'>
										<tr>
											<td class='search_box_item' style='padding:10px 10px;' colspan=2>
												<div id='goods_manual_area_1'>
													<div style='width:100%;padding:5px;' id='group_product_area_1'>
														<ui id='choiceSpecial'>".relationContentList($conm_ix, 1, "clipart")."</ui>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<td class='search_box_item' style='padding:5px 5px;' colspan=2>
												<button type='button' onclick='callConetne(1);'>기획전 불러오기</button>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>베스트아이템1 사용유무</b></td>
								<td class='search_box_item' colspan=3>
									<input type='radio' class='textbox' name='best_use' id='best_use_y' size=50 value='Y' style='border:0px;' ".($best_use == "" || $best_use == "Y" ? "checked":"")." onclick='clickChk(1, 0)'><label for='best_use_y'> 사용</label>
									<input type='radio' class='textbox' name='best_use' id='best_use_n' size=50 value='N' style='border:0px;' ".($best_use == "N" ? "checked":"")." onclick='clickChk(1, 1)'><label for='best_use_n'> 미사용</label>
								</td>
							</tr>
							<tr id='bestTId1'>
								<td class='search_box_title'><b>베스트아이템1 제목</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_title' id='best_title' style='width:85%;height:15px'>".$best_title."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_title_en' id='best_title_en' style='width:85%;height:15px'>".$best_title_en."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템1 제목 설정</b></td>
								<td class='search_box_item'>
									<input type='radio' name='s_best' id='s_best_L' value='L' ".("L" == $s_best || "" == $s_best  ? "checked":"")."><label for='s_best_L'> 좌측정렬</label>
									<input type='radio' name='s_best' id='s_best_C' value='C' ".("C" == $s_best ? "checked":"")."><label for='s_best_C'> 가운데정렬</label>
									<input type='radio' name='s_best' id='s_best_R' value='R' ".("R" == $s_best ? "checked":"")."><label for='s_best_R'> 우측정렬</label><br><br>
									진하게<input type='checkbox' name='b_best' id='b_best' ".("Y" == $b_best ? "checked":"").">
									기울기<input type='checkbox' name='i_best' id='i_best' ".("Y" == $i_best ? "checked":"").">
									밑줄<input type='checkbox' name='u_best' id='u_best' ".("Y" == $u_best ? "checked":"").">
									글자색 <input type='text' name='c_best' id='c_best' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best ? "#000000":$c_best)."'>
								</td>
							</tr>
							<tr id='bestEId1'>
								<td class='search_box_title'><b>베스트아이템1 간단설명</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_e' id='best_e' style='width:85%;height:15px'>".$best_e."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_e_en' id='best_e_en' style='width:85%;height:15px'>".$best_e_en."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템1 간단설명 설정</b></td>
								<td class='search_box_item'>
									진하게<input type='checkbox' name='b_best_e' id='b_best_e' ".("Y" == $b_best_e ? "checked":"").">
									기울기<input type='checkbox' name='i_best_e' id='i_best_e' ".("Y" == $i_best_e ? "checked":"").">
									밑줄<input type='checkbox' name='u_best_e' id='u_best_e' ".("Y" == $u_best_e ? "checked":"").">
									글자색 <input type='text' name='c_best_e' id='c_best_e' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best_e ? "#000000":$c_best_e)."'>
								</td>
							</tr>
							<tr id='bestCId1'>
								<td class='search_box_title'><b>베스트아이템1 설정</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
                                        <tr>
											<td style='padding-right:5px;'>" . getCategoryList3("대분류", "cid0", " class='cid1' onChange=\"loadCategory($(this),'cid1',1)\" title='대분류' ", 0, $bast_cate, "cid0") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("중분류", "cid1", " class='cid1' onChange=\"loadCategory($(this),'cid2',1)\" title='중분류'", 1, $bast_cate, "cid1") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("소분류", "cid2", " class='cid1' onChange=\"loadCategory($(this),'cid3',1)\" title='소분류'", 2, $bast_cate, "cid2") . " </td>
                                            <td>" . getCategoryList3("세분류", "cid3", " class='cid1' onChange=\"loadCategory($(this),'cid_1',1)\" title='세분류'", 3, $bast_cate, "cid3") . "</td>
                                        </tr>
                                    </table>
                                </td>
							</tr>
							<tr>
								<td class='search_box_title'><b>베스트아이템2 사용유무</b></td>
								<td class='search_box_item' colspan=3>
									<input type='radio' class='textbox' name='best_use2' id='best_use2_y' size=50 value='Y' style='border:0px;' ".($best_use2 == "Y" ? "checked":"")." onclick='clickChk(2, 0)'><label for='best_use2_y'> 사용</label>
									<input type='radio' class='textbox' name='best_use2' id='best_use2_n' size=50 value='N' style='border:0px;' ".($best_use2 == "" || $best_use2 == "N" ? "checked":"")." onclick='clickChk(2, 1)'><label for='best_use2_n'> 미사용</label>
								</td>
							</tr>
							<tr id='bestTId2' style='display:none'>
								<td class='search_box_title'><b>베스트아이템2 제목</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_title2' id='best_title2' style='width:85%;height:15px'>".$best_title2."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_title_en2' id='best_title_en2' style='width:85%;height:15px'>".$best_title_en2."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템2 제목 설정</b></td>
								<td class='search_box_item'>
									<input type='radio' name='s_best2' id='s_best2_L' value='L' ".("L" == $s_best2 || "" == $s_best2  ? "checked":"")."><label for='s_best2_L'> 좌측정렬</label>
									<input type='radio' name='s_best2' id='s_best2_C' value='C' ".("C" == $s_best2 ? "checked":"")."><label for='s_best2_C'> 가운데정렬</label>
									<input type='radio' name='s_best2' id='s_best2_R' value='R' ".("R" == $s_best2 ? "checked":"")."><label for='s_best2_R'> 우측정렬</label><br><br>
									진하게<input type='checkbox' name='b_best2' id='b_best2' ".("Y" == $b_best2 ? "checked":"").">
									기울기<input type='checkbox' name='i_best2' id='i_best2' ".("Y" == $i_best2 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best2' id='u_best2' ".("Y" == $u_best2 ? "checked":"").">
									글자색 <input type='text' name='c_best2' id='c_best2' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best2 ? "#000000":$c_best2)."'>
								</td>
							</tr>
							<tr id='bestEId2' style='display:none'>
								<td class='search_box_title'><b>베스트아이템2 간단설명</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_e2' id='best_e2' style='width:85%;height:15px'>".$best_e2."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_e_en2' id='best_e_en2' style='width:85%;height:15px'>".$best_e_en2."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템2 간단설명 설정</b></td>
								<td class='search_box_item'>
									진하게<input type='checkbox' name='b_best_e2' id='b_best_e2' ".("Y" == $b_best_e2 ? "checked":"").">
									기울기<input type='checkbox' name='i_best_e2' id='i_best_e2' ".("Y" == $i_best_e2 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best_e2' id='u_best_e2' ".("Y" == $u_best_e2 ? "checked":"").">
									글자색 <input type='text' name='c_best_e2' id='c_best_e2' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best_e2 ? "#000000":$c_best_e2)."'>
								</td>
							</tr>
							<tr id='bestCId2' style='display:none'>
								<td class='search_box_title'><b>베스트아이템2 설정</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
                                        <tr>
											<td style='padding-right:5px;'>" . getCategoryList3("대분류", "cate2cid0", " class='cid2' onChange=\"loadCategory($(this),'cate2cid1',2)\" title='대분류' ", 0, $bast_cate2, "cate2cid0") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("중분류", "cate2cid1", " class='cid2' onChange=\"loadCategory($(this),'cate2cid2',2)\" title='중분류'", 1, $bast_cate2, "cate2cid1") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("소분류", "cate2cid2", " class='cid2' onChange=\"loadCategory($(this),'cate2cid3',2)\" title='소분류'", 2, $bast_cate2, "cate2cid2") . " </td>
                                            <td>" . getCategoryList3("세분류", "cate2cid3", " class='cid2' onChange=\"loadCategory($(this),'cate2cid_1',2)\" title='세분류'", 3, $bast_cate2, "cate2cid3") . "</td>
                                        </tr>
                                    </table>
                                </td>
							</tr>
							<tr>
								<td class='search_box_title'><b>베스트아이템3 사용유무</b></td>
								<td class='search_box_item' colspan=3>
									<input type='radio' class='textbox' name='best_use3' id='best_use3_y' size=50 value='Y' style='border:0px;' ".($best_use3 == "Y" ? "checked":"")." onclick='clickChk(3, 0)'><label for='best_use3_y'> 사용</label>
									<input type='radio' class='textbox' name='best_use3' id='best_use3_n' size=50 value='N' style='border:0px;' ".($best_use3 == "" || $best_use3 == "N" ? "checked":"")." onclick='clickChk(3, 1)'><label for='best_use3_n'> 미사용</label>
								</td>
							</tr>
							<tr id='bestTId3' style='display:none'>
								<td class='search_box_title'><b>베스트아이템3 제목</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_title3' id='best_title3' style='width:85%;height:15px'>".$best_title3."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_title_en3' id='best_title_en3' style='width:85%;height:15px'>".$best_title_en3."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템3 제목 설정</b></td>
								<td class='search_box_item'>
									<input type='radio' name='s_best3' id='s_best3_L' value='L' ".("L" == $s_best3 || "" == $s_best3  ? "checked":"")."><label for='s_best3_L'> 좌측정렬</label>
									<input type='radio' name='s_best3' id='s_best3_C' value='C' ".("C" == $s_best3 ? "checked":"")."><label for='s_best3_C'> 가운데정렬</label>
									<input type='radio' name='s_best3' id='s_best3_R' value='R' ".("R" == $s_best3 ? "checked":"")."><label for='s_best3_R'> 우측정렬</label><br><br>
									진하게<input type='checkbox' name='b_best3' id='b_best3' ".("Y" == $b_best3 ? "checked":"").">
									기울기<input type='checkbox' name='i_best3' id='i_best3' ".("Y" == $i_best3 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best3' id='u_best3' ".("Y" == $u_best3 ? "checked":"").">
									글자색 <input type='text' name='c_best3' id='c_best3' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best3 ? "#000000":$c_best3)."'>
								</td>
							</tr>
							<tr id='bestEId3' style='display:none'>
								<td class='search_box_title'><b>베스트아이템3 간단설명</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_e3' id='best_e3' style='width:85%;height:15px'>".$best_e3."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_e_en3' id='best_e_en3' style='width:85%;height:15px'>".$best_e_en3."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템3 간단설명 설정</b></td>
								<td class='search_box_item'>
									진하게<input type='checkbox' name='b_best_e3' id='b_best_e3' ".("Y" == $b_best_e3 ? "checked":"").">
									기울기<input type='checkbox' name='i_best_e3' id='i_best_e3' ".("Y" == $i_best_e3 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best_e3' id='u_best_e3' ".("Y" == $u_best_e3 ? "checked":"").">
									글자색 <input type='text' name='c_best_e3' id='c_best_e3' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best_e3 ? "#000000":$c_best_e3)."'>
								</td>
							</tr>
							<tr id='bestCId3' style='display:none'>
								<td class='search_box_title'><b>베스트아이템3 설정</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
                                        <tr>
											<td style='padding-right:5px;'>" . getCategoryList3("대분류", "cate3cid0", " class='cid3' onChange=\"loadCategory($(this),'cate3cid1',3)\" title='대분류' ", 0, $bast_cate3, "cate3cid0") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("중분류", "cate3cid1", " class='cid3' onChange=\"loadCategory($(this),'cate3cid2',3)\" title='중분류'", 1, $bast_cate3, "cate3cid1") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("소분류", "cate3cid2", " class='cid3' onChange=\"loadCategory($(this),'cate3cid3',3)\" title='소분류'", 2, $bast_cate3, "cate3cid2") . " </td>
                                            <td>" . getCategoryList3("세분류", "cate3cid3", " class='cid3' onChange=\"loadCategory($(this),'cate3cid_1',3)\" title='세분류'", 3, $bast_cate3, "cate3cid3") . "</td>
                                        </tr>
                                    </table>
                                </td>
							</tr>
							<tr>
								<td class='search_box_title'><b>베스트아이템4 사용유무</b></td>
								<td class='search_box_item' colspan=3>
									<input type='radio' class='textbox' name='best_use4' id='best_use4_y' size=50 value='Y' style='border:0px;' ".($best_use4 == "Y" ? "checked":"")." onclick='clickChk(4, 0)'><label for='best_use4_y'> 사용</label>
									<input type='radio' class='textbox' name='best_use4' id='best_use4_n' size=50 value='N' style='border:0px;' ".($best_use4 == "" || $best_use4 == "N" ? "checked":"")." onclick='clickChk(4, 1)'><label for='best_use4_n'> 미사용</label>
								</td>
							</tr>
							<tr id='bestTId4' style='display:none'>
								<td class='search_box_title'><b>베스트아이템4 제목</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_title4' id='best_title4' style='width:85%;height:15px'>".$best_title4."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_title_en4' id='best_title_en4' style='width:85%;height:15px'>".$best_title_en4."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템4 제목 설정</b></td>
								<td class='search_box_item'>
									<input type='radio' name='s_best4' id='s_best4_L' value='L' ".("L" == $s_best4 || "" == $s_best4  ? "checked":"")."><label for='s_best4_L'> 좌측정렬</label>
									<input type='radio' name='s_best4' id='s_best4_C' value='C' ".("C" == $s_best4 ? "checked":"")."><label for='s_best4_C'> 가운데정렬</label>
									<input type='radio' name='s_best4' id='s_best4_R' value='R' ".("R" == $s_best4 ? "checked":"")."><label for='s_best4_R'> 우측정렬</label><br><br>
									진하게<input type='checkbox' name='b_best4' id='b_best4' ".("Y" == $b_best4 ? "checked":"").">
									기울기<input type='checkbox' name='i_best4' id='i_best4' ".("Y" == $i_best4 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best4' id='u_best4' ".("Y" == $u_best4 ? "checked":"").">
									글자색 <input type='text' name='c_best4' id='c_best4' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best4 ? "#000000":$c_best4)."'>
								</td>
							</tr>
							<tr id='bestEId4' style='display:none'>
								<td class='search_box_title'><b>베스트아이템4 간단설명</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_e4' id='best_e4' style='width:85%;height:15px'>".$best_e4."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_e_en4' id='best_e_en4' style='width:85%;height:15px'>".$best_e_en4."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템4 간단설명 설정</b></td>
								<td class='search_box_item'>
									진하게<input type='checkbox' name='b_best_e4' id='b_best_e4' ".("Y" == $b_best_e4 ? "checked":"").">
									기울기<input type='checkbox' name='i_best_e4' id='i_best_e4' ".("Y" == $i_best_e4 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best_e4' id='u_best_e4' ".("Y" == $u_best_e4 ? "checked":"").">
									글자색 <input type='text' name='c_best_e4' id='c_best_e4' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best_e4 ? "#000000":$c_best_e4)."'>
								</td>
							</tr>
							<tr id='bestCId4' style='display:none'>
								<td class='search_box_title'><b>베스트아이템4 설정</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
                                        <tr>
											<td style='padding-right:5px;'>" . getCategoryList3("대분류", "cate4cid0", " class='cid4' onChange=\"loadCategory($(this),'cate4cid1',4)\" title='대분류' ", 0, $bast_cate4, "cate4cid0") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("중분류", "cate4cid1", " class='cid4' onChange=\"loadCategory($(this),'cate4cid2',4)\" title='중분류'", 1, $bast_cate4, "cate4cid1") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("소분류", "cate4cid2", " class='cid4' onChange=\"loadCategory($(this),'cate4cid3',4)\" title='소분류'", 2, $bast_cate4, "cate4cid2") . " </td>
                                            <td>" . getCategoryList3("세분류", "cate4cid3", " class='cid4' onChange=\"loadCategory($(this),'cate4cid_1',4)\" title='세분류'", 3, $bast_cate4, "cate4cid3") . "</td>
                                        </tr>
                                    </table>
                                </td>
							</tr>
							<tr>
								<td class='search_box_title'><b>베스트아이템5 사용유무</b></td>
								<td class='search_box_item' colspan=3>
									<input type='radio' class='textbox' name='best_use5' id='best_use5_y' size=50 value='Y' style='border:0px;' ".($best_use5 == "Y" ? "checked":"")." onclick='clickChk(5, 0)'><label for='best_use5_y'> 사용</label>
									<input type='radio' class='textbox' name='best_use5' id='best_use5_n' size=50 value='N' style='border:0px;' ".($best_use5 == "" || $best_use5 == "N" ? "checked":"")." onclick='clickChk(5, 1)'><label for='best_use5_n'> 미사용</label>
								</td>
							</tr>
							<tr id='bestTId5' style='display:none'>
								<td class='search_box_title'><b>베스트아이템5 제목</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_title5' id='best_title5' style='width:85%;height:15px'>".$best_title3."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_title_en5' id='best_title_en5' style='width:85%;height:15px'>".$best_title_en3."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템5 제목 설정</b></td>
								<td class='search_box_item'>
									<input type='radio' name='s_best5' id='s_best5_L' value='L' ".("L" == $s_best5 || "" == $s_best5  ? "checked":"")."><label for='s_best5_L'> 좌측정렬</label>
									<input type='radio' name='s_best5' id='s_best5_C' value='C' ".("C" == $s_best5 ? "checked":"")."><label for='s_best5_C'> 가운데정렬</label>
									<input type='radio' name='s_best5' id='s_best5_R' value='R' ".("R" == $s_best5 ? "checked":"")."><label for='s_best5_R'> 우측정렬</label><br><br>
									진하게<input type='checkbox' name='b_best5' id='b_best5' ".("Y" == $b_best5 ? "checked":"").">
									기울기<input type='checkbox' name='i_best5' id='i_best5' ".("Y" == $i_best5 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best5' id='u_best5' ".("Y" == $u_best5 ? "checked":"").">
									글자색 <input type='text' name='c_best5' id='c_best5' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best5 ? "#000000":$c_best5)."'>
								</td>
							</tr>
							<tr id='bestEId5' style='display:none'>
								<td class='search_box_title'><b>베스트아이템5 간단설명</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_e5' id='best_e5' style='width:85%;height:15px'>".$best_e5."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_e_en5' id='best_e_en5' style='width:85%;height:15px'>".$best_e_en5."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템5 간단설명 설정</b></td>
								<td class='search_box_item'>
									진하게<input type='checkbox' name='b_best_e5' id='b_best_e5' ".("Y" == $b_best_e5 ? "checked":"").">
									기울기<input type='checkbox' name='i_best_e5' id='i_best_e5' ".("Y" == $i_best_e5 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best_e5' id='u_best_e5' ".("Y" == $u_best_e5 ? "checked":"").">
									글자색 <input type='text' name='c_best_e5' id='c_best_e5' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best_e5 ? "#000000":$c_best_e5)."'>
								</td>
							</tr>
							<tr id='bestCId5' style='display:none'>
								<td class='search_box_title'><b>베스트아이템5 설정</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
                                        <tr>
											<td style='padding-right:5px;'>" . getCategoryList3("대분류", "cate5cid0", " class='cid5' onChange=\"loadCategory($(this),'cate5cid1',5)\" title='대분류' ", 0, $bast_cate5, "cate5cid0") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("중분류", "cate5cid1", " class='cid5' onChange=\"loadCategory($(this),'cate5cid2',5)\" title='중분류'", 1, $bast_cate5, "cate5cid1") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("소분류", "cate5cid2", " class='cid5' onChange=\"loadCategory($(this),'cate5cid3',5)\" title='소분류'", 2, $bast_cate5, "cate5cid2") . " </td>
                                            <td>" . getCategoryList3("세분류", "cate5cid3", " class='cid5' onChange=\"loadCategory($(this),'cate5cid_1',5)\" title='세분류'", 3, $bast_cate5, "cate5cid3") . "</td>
                                        </tr>
                                    </table>
                                </td>
							</tr>
							<tr>
								<td class='search_box_title'><b>베스트아이템6 사용유무</b></td>
								<td class='search_box_item' colspan=3>
									<input type='radio' class='textbox' name='best_use6' id='best_use6_y' size=50 value='Y' style='border:0px;' ".($best_use6 == "Y" ? "checked":"")." onclick='clickChk(6, 0)'><label for='best_use6_y'> 사용</label>
									<input type='radio' class='textbox' name='best_use6' id='best_use6_n' size=50 value='N' style='border:0px;' ".($best_use6 == "" || $best_use6 == "N" ? "checked":"")." onclick='clickChk(6, 1)'><label for='best_use6_n'> 미사용</label>
								</td>
							</tr>
							<tr id='bestTId6' style='display:none'>
								<td class='search_box_title'><b>베스트아이템6 제목</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_title6' id='best_title6' style='width:85%;height:15px'>".$best_title6."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_title_en6' id='best_title_en6' style='width:85%;height:15px'>".$best_title_en6."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템6 제목 설정</b></td>
								<td class='search_box_item'>
									<input type='radio' name='s_best6' id='s_best6_L' value='L' ".("L" == $s_best6 || "" == $s_best6  ? "checked":"")."><label for='s_best6_L'> 좌측정렬</label>
									<input type='radio' name='s_best6' id='s_best6_C' value='C' ".("C" == $s_best6 ? "checked":"")."><label for='s_best6_C'> 가운데정렬</label>
									<input type='radio' name='s_best6' id='s_best6_R' value='R' ".("R" == $s_best6 ? "checked":"")."><label for='s_best6_R'> 우측정렬</label><br><br>
									진하게<input type='checkbox' name='b_best6' id='b_best6' ".("Y" == $b_best6 ? "checked":"").">
									기울기<input type='checkbox' name='i_best6' id='i_best6' ".("Y" == $i_best6 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best6' id='u_best6' ".("Y" == $u_best6 ? "checked":"").">
									글자색 <input type='text' name='c_best6' id='c_best6' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best6 ? "#000000":$c_best6)."'>
								</td>
							</tr>
							<tr id='bestEId6' style='display:none'>
								<td class='search_box_title'><b>베스트아이템6 간단설명</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_e6' id='best_e6' style='width:85%;height:15px'>".$best_e6."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_e_en6' id='best_e_en6' style='width:85%;height:15px'>".$best_e_en6."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템6 간단설명 설정</b></td>
								<td class='search_box_item'>
									진하게<input type='checkbox' name='b_best_e6' id='b_best_e6' ".("Y" == $b_best_e6 ? "checked":"").">
									기울기<input type='checkbox' name='i_best_e6' id='i_best_e6' ".("Y" == $i_best_e6 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best_e6' id='u_best_e6' ".("Y" == $u_best_e6 ? "checked":"").">
									글자색 <input type='text' name='c_best_e6' id='c_best_e6' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best_e6 ? "#000000":$c_best_e6)."'>
								</td>
							</tr>
							<tr id='bestCId6' style='display:none'>
								<td class='search_box_title'><b>베스트아이템6 설정</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
                                        <tr>
											<td style='padding-right:5px;'>" . getCategoryList3("대분류", "cate6cid0", " class='cid6' onChange=\"loadCategory($(this),'cate6cid1',6)\" title='대분류' ", 0, $bast_cate6, "cate6cid0") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("중분류", "cate6cid1", " class='cid6' onChange=\"loadCategory($(this),'cate6cid2',6)\" title='중분류'", 1, $bast_cate6, "cate6cid1") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("소분류", "cate6cid2", " class='cid6' onChange=\"loadCategory($(this),'cate6cid3',6)\" title='소분류'", 2, $bast_cate6, "cate6cid2") . " </td>
                                            <td>" . getCategoryList3("세분류", "cate6cid3", " class='cid6' onChange=\"loadCategory($(this),'cate6cid_1',6)\" title='세분류'", 3, $bast_cate6, "cate6cid3") . "</td>
                                        </tr>
                                    </table>
                                </td>
							</tr>
							<tr>
								<td class='search_box_title'><b>베스트아이템7 사용유무</b></td>
								<td class='search_box_item' colspan=3>
									<input type='radio' class='textbox' name='best_use7' id='best_use7_y' size=50 value='Y' style='border:0px;' ".($best_use7 == "Y" ? "checked":"")." onclick='clickChk(7, 0)'><label for='best_use7_y'> 사용</label>
									<input type='radio' class='textbox' name='best_use7' id='best_use7_n' size=50 value='N' style='border:0px;' ".($best_use7 == "" || $best_use7 == "N" ? "checked":"")." onclick='clickChk(7, 1)'><label for='best_use7_n'> 미사용</label>
								</td>
							</tr>
							<tr id='bestTId7' style='display:none'>
								<td class='search_box_title'><b>베스트아이템7 제목</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_title7' id='best_title7' style='width:85%;height:15px'>".$best_title7."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_title_en7' id='best_title_en7' style='width:85%;height:15px'>".$best_title_en7."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템7 제목 설정</b></td>
								<td class='search_box_item'>
									<input type='radio' name='s_best7' id='s_best7_L' value='L' ".("L" == $s_best7 || "" == $s_best7  ? "checked":"")."><label for='s_best7_L'> 좌측정렬</label>
									<input type='radio' name='s_best7' id='s_best7_C' value='C' ".("C" == $s_best7 ? "checked":"")."><label for='s_best7_C'> 가운데정렬</label>
									<input type='radio' name='s_best7' id='s_best7_R' value='R' ".("R" == $s_best7 ? "checked":"")."><label for='s_best7_R'> 우측정렬</label><br><br>
									진하게<input type='checkbox' name='b_best7' id='b_best7' ".("Y" == $b_best7 ? "checked":"").">
									기울기<input type='checkbox' name='i_best7' id='i_best7' ".("Y" == $i_best7 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best7' id='u_best7' ".("Y" == $u_best7 ? "checked":"").">
									글자색 <input type='text' name='c_best7' id='c_best7' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best7 ? "#000000":$c_best7)."'>
								</td>
							</tr>
							<tr id='bestEId7' style='display:none'>
								<td class='search_box_title'><b>베스트아이템7 간단설명</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_e7' id='best_e7' style='width:85%;height:15px'>".$best_e7."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_e_en7' id='best_e_en7' style='width:85%;height:15px'>".$best_e_en7."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템7 간단설명 설정</b></td>
								<td class='search_box_item'>
									진하게<input type='checkbox' name='b_best_e7' id='b_best_e7' ".("Y" == $b_best_e7 ? "checked":"").">
									기울기<input type='checkbox' name='i_best_e7' id='i_best_e7' ".("Y" == $i_best_e7 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best_e7' id='u_best_e7' ".("Y" == $u_best_e7 ? "checked":"").">
									글자색 <input type='text' name='c_best_e7' id='c_best_e7' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best_e7 ? "#000000":$c_best_e7)."'>
								</td>
							</tr>
							<tr id='bestCId7' style='display:none'>
								<td class='search_box_title'><b>베스트아이템7 설정</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
                                        <tr>
											<td style='padding-right:5px;'>" . getCategoryList3("대분류", "cate7cid0", " class='cid7' onChange=\"loadCategory($(this),'cate7cid1',7)\" title='대분류' ", 0, $bast_cate7, "cate7cid0") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("중분류", "cate7cid1", " class='cid7' onChange=\"loadCategory($(this),'cate7cid2',7)\" title='중분류'", 1, $bast_cate7, "cate7cid1") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("소분류", "cate7cid2", " class='cid7' onChange=\"loadCategory($(this),'cate7cid3',7)\" title='소분류'", 2, $bast_cate7, "cate7cid2") . " </td>
                                            <td>" . getCategoryList3("세분류", "cate7cid3", " class='cid7' onChange=\"loadCategory($(this),'cate7cid_1',7)\" title='세분류'", 3, $bast_cate7, "cate7cid3") . "</td>
                                        </tr>
                                    </table>
                                </td>
							</tr>
							<tr>
								<td class='search_box_title'><b>베스트아이템8 사용유무</b></td>
								<td class='search_box_item' colspan=3>
									<input type='radio' class='textbox' name='best_use8' id='best_use8_y' size=50 value='Y' style='border:0px;' ".($best_use8 == "Y" ? "checked":"")." onclick='clickChk(8, 0)'><label for='best_use8_y'> 사용</label>
									<input type='radio' class='textbox' name='best_use8' id='best_use8_n' size=50 value='N' style='border:0px;' ".($best_use8 == "" || $best_use8 == "N" ? "checked":"")." onclick='clickChk(8, 1)'><label for='best_use8_n'> 미사용</label>
								</td>
							</tr>
							<tr id='bestTId8' style='display:none'>
								<td class='search_box_title'><b>베스트아이템8 제목</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_title8' id='best_title8' style='width:85%;height:15px'>".$best_title8."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_title_en8' id='best_title_en8' style='width:85%;height:15px'>".$best_title_en8."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템8 제목 설정</b></td>
								<td class='search_box_item'>
									<input type='radio' name='s_best8' id='s_best8_L' value='L' ".("L" == $s_best8 || "" == $s_best8  ? "checked":"")."><label for='s_best8_L'> 좌측정렬</label>
									<input type='radio' name='s_best8' id='s_best8_C' value='C' ".("C" == $s_best8 ? "checked":"")."><label for='s_best8_C'> 가운데정렬</label>
									<input type='radio' name='s_best8' id='s_best8_R' value='R' ".("R" == $s_best8 ? "checked":"")."><label for='s_best8_R'> 우측정렬</label><br><br>
									진하게<input type='checkbox' name='b_best8' id='b_best8' ".("Y" == $b_best8 ? "checked":"").">
									기울기<input type='checkbox' name='i_best8' id='i_best8' ".("Y" == $i_best8 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best8' id='u_best8' ".("Y" == $u_best8 ? "checked":"").">
									글자색 <input type='text' name='c_best8' id='c_best8' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best8 ? "#000000":$c_best8)."'>
								</td>
							</tr>
							<tr id='bestEId8' style='display:none'>
								<td class='search_box_title'><b>베스트아이템8 간단설명</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_e8' id='best_e8' style='width:85%;height:15px'>".$best_e8."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_e_en8' id='best_e_en8' style='width:85%;height:15px'>".$best_e_en8."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템8 간단설명 설정</b></td>
								<td class='search_box_item'>
									진하게<input type='checkbox' name='b_best_e8' id='b_best_e8' ".("Y" == $b_best_e8 ? "checked":"").">
									기울기<input type='checkbox' name='i_best_e8' id='i_best_e8' ".("Y" == $i_best_e8 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best_e8' id='u_best_e8' ".("Y" == $u_best_e8 ? "checked":"").">
									글자색 <input type='text' name='c_best_e8' id='c_best_e8' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best_e8 ? "#000000":$c_best_e8)."'>
								</td>
							</tr>
							<tr id='bestCId8' style='display:none'>
								<td class='search_box_title'><b>베스트아이템8 설정</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
                                        <tr>
											<td style='padding-right:5px;'>" . getCategoryList3("대분류", "cate8cid0", " class='cid8' onChange=\"loadCategory($(this),'cate8cid1',8)\" title='대분류' ", 0, $bast_cate8, "cate8cid0") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("중분류", "cate8cid1", " class='cid8' onChange=\"loadCategory($(this),'cate8cid2',8)\" title='중분류'", 1, $bast_cate8, "cate8cid1") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("소분류", "cate8cid2", " class='cid8' onChange=\"loadCategory($(this),'cate8cid3',8)\" title='소분류'", 2, $bast_cate8, "cate8cid2") . " </td>
                                            <td>" . getCategoryList3("세분류", "cate8cid3", " class='cid8' onChange=\"loadCategory($(this),'cate8cid_1',8)\" title='세분류'", 3, $bast_cate8, "cate8cid3") . "</td>
                                        </tr>
                                    </table>
                                </td>
							</tr>
							<tr>
								<td class='search_box_title'><b>베스트아이템9 사용유무</b></td>
								<td class='search_box_item' colspan=3>
									<input type='radio' class='textbox' name='best_use9' id='best_use9_y' size=50 value='Y' style='border:0px;' ".($best_use9 == "Y" ? "checked":"")." onclick='clickChk(9, 0)'><label for='best_use9_y'> 사용</label>
									<input type='radio' class='textbox' name='best_use9' id='best_use9_n' size=50 value='N' style='border:0px;' ".($best_use9 == "" || $best_use9 == "N" ? "checked":"")." onclick='clickChk(9, 1)'><label for='best_use9_n'> 미사용</label>
								</td>
							</tr>
							<tr id='bestTId9' style='display:none'>
								<td class='search_box_title'><b>베스트아이템9 제목</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_title9' id='best_title9' style='width:85%;height:15px'>".$best_title9."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_title_en9' id='best_title_en9' style='width:85%;height:15px'>".$best_title_en9."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템9 제목 설정</b></td>
								<td class='search_box_item'>
									<input type='radio' name='s_best9' id='s_best9_L' value='L' ".("L" == $s_best9 || "" == $s_best9  ? "checked":"")."><label for='s_best9_L'> 좌측정렬</label>
									<input type='radio' name='s_best9' id='s_best9_C' value='C' ".("C" == $s_best9 ? "checked":"")."><label for='s_best9_C'> 가운데정렬</label>
									<input type='radio' name='s_best9' id='s_best9_R' value='R' ".("R" == $s_best9 ? "checked":"")."><label for='s_best9_R'> 우측정렬</label><br><br>
									진하게<input type='checkbox' name='b_best9' id='b_best9' ".("Y" == $b_best9 ? "checked":"").">
									기울기<input type='checkbox' name='i_best9' id='i_best9' ".("Y" == $i_best9 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best9' id='u_best9' ".("Y" == $u_best9 ? "checked":"").">
									글자색 <input type='text' name='c_best9' id='c_best9' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best9 ? "#000000":$c_best9)."'>
								</td>
							</tr>
							<tr id='bestEId9' style='display:none'>
								<td class='search_box_title'><b>베스트아이템9 간단설명</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_e9' id='best_e9' style='width:85%;height:15px'>".$best_e9."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_e_en9' id='best_e_en9' style='width:85%;height:15px'>".$best_e_en9."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템9 간단설명 설정</b></td>
								<td class='search_box_item'>
									진하게<input type='checkbox' name='b_best_e9' id='b_best_e9' ".("Y" == $b_best_e9 ? "checked":"").">
									기울기<input type='checkbox' name='i_best_e9' id='i_best_e9' ".("Y" == $i_best_e9 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best_e9' id='u_best_e9' ".("Y" == $u_best_e9 ? "checked":"").">
									글자색 <input type='text' name='c_best_e9' id='c_best_e9' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best_e9 ? "#000000":$c_best_e9)."'>
								</td>
							</tr>
							<tr id='bestCId9' style='display:none'>
								<td class='search_box_title'><b>베스트아이템9 설정</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
                                        <tr>
											<td style='padding-right:5px;'>" . getCategoryList3("대분류", "cate9cid0", " class='cid9' onChange=\"loadCategory($(this),'cate9cid1',9)\" title='대분류' ", 0, $bast_cate9, "cate9cid0") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("중분류", "cate9cid1", " class='cid9' onChange=\"loadCategory($(this),'cate9cid2',9)\" title='중분류'", 1, $bast_cate9, "cate9cid1") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("소분류", "cate9cid2", " class='cid9' onChange=\"loadCategory($(this),'cate9cid3',9)\" title='소분류'", 2, $bast_cate9, "cate9cid2") . " </td>
                                            <td>" . getCategoryList3("세분류", "cate9cid3", " class='cid9' onChange=\"loadCategory($(this),'cate9cid_1',9)\" title='세분류'", 3, $bast_cate9, "cate9cid3") . "</td>
                                        </tr>
                                    </table>
                                </td>
							</tr>
							<tr>
								<td class='search_box_title'><b>베스트아이템10 사용유무</b></td>
								<td class='search_box_item' colspan=3>
									<input type='radio' class='textbox' name='best_use10' id='best_use10_y' size=50 value='Y' style='border:0px;' ".($best_use10 == "Y" ? "checked":"")." onclick='clickChk(10, 0)'><label for='best_use10_y'> 사용</label>
									<input type='radio' class='textbox' name='best_use10' id='best_use10_n' size=50 value='N' style='border:0px;' ".($best_use10 == "" || $best_use10 == "N" ? "checked":"")." onclick='clickChk(10, 1)'><label for='best_use10_n'> 미사용</label>
								</td>
							</tr>
							<tr id='bestTId10' style='display:none'>
								<td class='search_box_title'><b>베스트아이템10 제목</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_title10' id='best_title10' style='width:85%;height:15px'>".$best_title10."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_title_en10' id='best_title_en10' style='width:85%;height:15px'>".$best_title_en10."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템10 제목 설정</b></td>
								<td class='search_box_item'>
									<input type='radio' name='s_best10' id='s_best10_L' value='L' ".("L" == $s_best10 || "" == $s_best10  ? "checked":"")."><label for='s_best10_L'> 좌측정렬</label>
									<input type='radio' name='s_best10' id='s_best10_C' value='C' ".("C" == $s_best10 ? "checked":"")."><label for='s_best10_C'> 가운데정렬</label>
									<input type='radio' name='s_best10' id='s_best10_R' value='R' ".("R" == $s_best10 ? "checked":"")."><label for='s_best10_R'> 우측정렬</label><br><br>
									진하게<input type='checkbox' name='b_best10' id='b_best10' ".("Y" == $b_best10 ? "checked":"").">
									기울기<input type='checkbox' name='i_best10' id='i_best10' ".("Y" == $i_best10 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best10' id='u_best10' ".("Y" == $u_best10 ? "checked":"").">
									글자색 <input type='text' name='c_best10' id='c_best10' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best10 ? "#000000":$c_best10)."'>
								</td>
							</tr>
							<tr id='bestEId10' style='display:none'>
								<td class='search_box_title'><b>베스트아이템10 간단설명</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='best_e10' id='best_e10' style='width:85%;height:15px'>".$best_e10."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='best_e_en10' id='best_e_en10' style='width:85%;height:15px'>".$best_e_en10."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>베스트아이템10 간단설명 설정</b></td>
								<td class='search_box_item'>
									진하게<input type='checkbox' name='b_best_e10' id='b_best_e10' ".("Y" == $b_best_e10 ? "checked":"").">
									기울기<input type='checkbox' name='i_best_e10' id='i_best_e10' ".("Y" == $i_best_e10 ? "checked":"").">
									밑줄<input type='checkbox' name='u_best_e10' id='u_best_e10' ".("Y" == $u_best_e10 ? "checked":"").">
									글자색 <input type='text' name='c_best_e10' id='c_best_e10' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_best_e10 ? "#000000":$c_best_e10)."'>
								</td>
							</tr>
							<tr id='bestCId10' style='display:none'>
								<td class='search_box_title'><b>베스트아이템10 설정</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
                                        <tr>
											<td style='padding-right:5px;'>" . getCategoryList3("대분류", "cate10cid0", " class='cid10' onChange=\"loadCategory($(this),'cate10cid1',10)\" title='대분류' ", 0, $bast_cate10, "cate10cid0") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("중분류", "cate10cid1", " class='cid10' onChange=\"loadCategory($(this),'cate10cid2',10)\" title='중분류'", 1, $bast_cate10, "cate10cid1") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("소분류", "cate10cid2", " class='cid10' onChange=\"loadCategory($(this),'cate10cid3',10)\" title='소분류'", 2, $bast_cate10, "cate10cid2") . " </td>
                                            <td>" . getCategoryList3("세분류", "cate10cid3", " class='cid10' onChange=\"loadCategory($(this),'cate10cid_1',10)\" title='세분류'", 3, $bast_cate10, "cate10cid3") . "</td>
                                        </tr>
                                    </table>
                                </td>
							</tr>
							<tr>
								<td class='search_box_title'><b>추천스타일 사용유무</b></td>
								<td class='search_box_item' colspan=3>
									<input type='radio' class='textbox' name='style_use' id='style_use_y' size=50 value='Y' checked style='border:0px;' ".($style_use == "Y" ? "checked":"")."><label for='style_use_y'> 사용</label>
									<input type='radio' class='textbox' name='style_use' id='style_use_n' size=50 value='N' style='border:0px;' ".($style_use == "N" ? "checked":"")."><label for='style_use_n'> 미사용</label>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>추천스타일 제목</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='style_title' id='style_title' style='width:85%;height:15px'>".$style_title."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='style_title_en' id='style_title_en' style='width:85%;height:15px'>".$style_title_en."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>추천스타일 제목 설정</b></td>
								<td class='search_box_item'>
									<input type='radio' name='s_style' id='s_style_L' value='L' ".("L" == $s_style || "" == $s_style  ? "checked":"")."><label for='s_style_L'> 좌측정렬</label>
									<input type='radio' name='s_style' id='s_style_C' value='C' ".("C" == $s_style ? "checked":"")."><label for='s_style_C'> 가운데정렬</label>
									<input type='radio' name='s_style' id='s_style_R' value='R' ".("R" == $s_style ? "checked":"")."><label for='s_style_R'> 우측정렬</label><br><br>
									진하게<input type='checkbox' name='b_style' id='b_style' ".("Y" == $b_style ? "checked":"").">
									기울기<input type='checkbox' name='i_style' id='i_style' ".("Y" == $i_style ? "checked":"").">
									밑줄<input type='checkbox' name='u_style' id='u_style' ".("Y" == $u_style ? "checked":"").">
									글자색 <input type='text' name='c_style' id='c_style' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_style ? "#000000":$c_style)."'>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>추천스타일 간단설명</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='style_e' id='style_title' style='width:85%;height:15px'>".$style_e."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='style_e_en' id='style_title_en' style='width:85%;height:15px'>".$style_e_en."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>추천스타일 간단설명 설정</b></td>
								<td class='search_box_item'>
									진하게<input type='checkbox' name='b_style_e' id='b_style_e' ".("Y" == $b_style_e ? "checked":"").">
									기울기<input type='checkbox' name='i_style_e' id='i_style_e' ".("Y" == $i_style_e ? "checked":"").">
									밑줄<input type='checkbox' name='u_style_e' id='u_style_e' ".("Y" == $u_style_e ? "checked":"").">
									글자색 <input type='text' name='c_style_e' id='c_style_e' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_style_e ? "#000000":$c_style_e)."'>
								</td>
							</tr>
							<tr>
								<td class='search_box_title' nowrap> <b>추천스타일 불러오기</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 style=width:100%;>
                                    <col width='225px'>
                                    <col width='*'>
										<tr>
											<td class='search_box_item' style='padding:10px 10px;' colspan=2>
												<div id='goods_manual_area_1'>
													<div style='width:100%;padding:5px;' id='group_product_area_1'>
														<ui id='choiceStyle'>".relationContentList($conm_ix, 2, "clipart")."</ui>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<td class='search_box_item' style='padding:5px 5px;' colspan=2>
												<button type='button' onclick='callConetne(2);'>추천스타일 불러오기</button>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>저널 사용유무</b></td>
								<td class='search_box_item' colspan=3>
									<input type='radio' class='textbox' name='journal_use' id='journal_use_y' size=50 value='Y' checked style='border:0px;' ".($journal_use == "Y" ? "checked":"")."><label for='journal_use_y'> 사용</label>
									<input type='radio' class='textbox' name='journal_use' id='journal_use_n' size=50 value='N' style='border:0px;' ".($journal_use == "N" ? "checked":"")."><label for='journal_use_n'> 미사용</label>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>저널 제목</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='journal_title' id='journal_title' style='width:85%;height:15px'>".$journal_title."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='journal_title_en' id='journal_title_en' style='width:85%;height:15px'>".$journal_title_en."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>저널 제목 설정</b></td>
								<td class='search_box_item'>
									<input type='radio' name='s_journal' id='s_journal_L' value='L' ".("L" == $s_journal || "" == $s_journal  ? "checked":"")."><label for='s_journal_L'> 좌측정렬</label>
									<input type='radio' name='s_journal' id='s_journal_C' value='C' ".("C" == $s_journal ? "checked":"")."><label for='s_journal_C'> 가운데정렬</label>
									<input type='radio' name='s_journal' id='s_journal_R' value='R' ".("C" == $s_journal ? "checked":"")."><label for='s_journal_R'> 우측정렬</label><br><br>
									진하게<input type='checkbox' name='b_journal' id='b_journal' ".("Y" == $b_journal ? "checked":"").">
									기울기<input type='checkbox' name='i_journal' id='i_journal' ".("Y" == $i_journal ? "checked":"").">
									밑줄<input type='checkbox' name='u_journal' id='u_journal' ".("Y" == $u_journal ? "checked":"").">
									글자색 <input type='text' name='c_journal' id='c_journal' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_journal ? "#000000":$c_journal)."'>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>저널 간단설명</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='journal_e' id='journal_title' style='width:85%;height:15px'>".$journal_e."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='journal_e_en' id='journal_title_en' style='width:85%;height:15px'>".$journal_e_en."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>저널 간단설명 설정</b></td>
								<td class='search_box_item'>
									진하게<input type='checkbox' name='b_journal_e' id='b_journal_e' ".("Y" == $b_journal_e ? "checked":"").">
									기울기<input type='checkbox' name='i_journal_e' id='i_journal_e' ".("Y" == $i_journal_e ? "checked":"").">
									밑줄<input type='checkbox' name='u_journal_e' id='u_journal_e' ".("Y" == $u_journal_e ? "checked":"").">
									글자색 <input type='text' name='c_journal_e' id='c_journal_e' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_journal_e ? "#000000":$c_journal_e)."'>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>저널 프레임 등록</b></td>
								<td class='search_box_item' colspan='3'>
                                    <table border=0>
                                        <col width='505px'>
                                        <col width='*'>
                                        <tr>
                                            <td>
                                                <input type=file name='journal_frame_img' id='journal_frame_img' class='textbox' size=25 style='font-size:8pt'>
                                            </td>
                                            <td ".$img_view_style." rowspan=2>
                                                <a class='screenshot'  rel='".$_SESSION["admin_config"][mall_data_root]."/images/frame/".$conm_ix."/".$journal_frame_img."'><img src='../v3/images/btn/bt_preview.png'   style='cursor:pointer'></a>";
if($journal_frame_img != ""){
	$Contents .= "<input type='checkbox' name='journal_frame_img_del' id='journal_frame_img_del'>저널 프레임 이미지 삭제";
}
$Contents .= "</td>
                                        </tr>
                                    </table>
                                </td>
							</tr>
							<tr>
								<td class='search_box_title'><b>추천컨텐츠 사용유무</b></td>
								<td class='search_box_item' colspan=3>
									<input type='radio' class='textbox' name='content_use' id='content_use_y' size=50 value='Y' checked style='border:0px;' ".($content_use == "Y" ? "checked":"")."><label for='content_use_y'> 사용</label>
									<input type='radio' class='textbox' name='content_use' id='content_use_n' size=50 value='N' style='border:0px;' ".($content_use == "N" ? "checked":"")."><label for='content_use_n'> 미사용</label>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>추천컨텐츠 제목</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='content_title' id='style_title' style='width:85%;height:15px'>".$content_title."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='content_title_en' id='style_title_en' style='width:85%;height:15px'>".$content_title_en."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>추천컨텐츠 제목 설정</b></td>
								<td class='search_box_item'>
									<input type='radio' name='s_content' id='s_content_L' value='L' ".("L" == $s_content || "" == $s_content  ? "checked":"")."><label for='s_content_L'> 좌측정렬</label>
									<input type='radio' name='s_content' id='s_content_C' value='C' ".("C" == $s_content ? "checked":"")."><label for='s_content_C'> 가운데정렬</label>
									<input type='radio' name='s_content' id='s_content_R' value='R' ".("C" == $s_content ? "checked":"")."><label for='s_content_R'> 우측정렬</label><br><br>
									진하게<input type='checkbox' name='b_content' id='b_content' ".("Y" == $b_content ? "checked":"").">
									기울기<input type='checkbox' name='i_content' id='i_content' ".("Y" == $i_content ? "checked":"").">
									밑줄<input type='checkbox' name='u_content' id='u_content' ".("Y" == $u_content ? "checked":"").">
									글자색 <input type='text' name='c_content' id='c_content' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_content ? "#000000":$c_content)."'>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>추천컨텐츠 간단설명</b></td>
								<td class='search_box_item'>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='content_e' id='content_title' style='width:85%;height:15px'>".$content_e."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='content_e_en' id='content_title_en' style='width:85%;height:15px'>".$content_e_en."</textarea></td></tr>
									</table>
								</td>
								<td class='search_box_title'><b>추천컨텐츠 간단설명 설정</b></td>
								<td class='search_box_item'>
									진하게<input type='checkbox' name='b_content_e' id='b_content_e' ".("Y" == $b_content_e ? "checked":"").">
									기울기<input type='checkbox' name='i_content_e' id='i_content_e' ".("Y" == $i_content_e ? "checked":"").">
									밑줄<input type='checkbox' name='u_content_e' id='u_content_e' ".("Y" == $u_content_e ? "checked":"").">
									글자색 <input type='text' name='c_content_e' id='c_content_e' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_content_e ? "#000000":$c_content_e)."'>
								</td>
							</tr>
							<tr>
								<td class='search_box_title' nowrap> <b>추천컨텐츠 불러오기</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 style=width:100%;>
                                    <col width='225px'>
                                    <col width='*'>
										<tr>
											<td class='search_box_item' style='padding:10px 10px;' colspan=2>
												<div id='goods_manual_area_1'>
													<div style='width:100%;padding:5px;' id='group_product_area_1'>
														<ui id='choiceContent'>".relationContentList($conm_ix, 3, "clipart")."</ui>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<td class='search_box_item' style='padding:5px 5px;' colspan=2>
												<button type='button' onclick='callConetne(3);'>추천컨텐츠 불러오기</button>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>사용여부</b></td>
								<td class='search_box_item'>
									<input type='radio' class='textbox' name='main_use' id='main_use_y' size=50 value='Y' checked style='border:0px;' ".($main_use == "Y" ? "checked":"")."><label for='main_use_y'> 사용</label>
									<input type='radio' class='textbox' name='main_use' id='main_use_n' size=50 value='N' style='border:0px;' ".($main_use == "N" ? "checked":"")."><label for='main_use_n'> 미사용</label>
								</td>
								<td class='search_box_title'><b>기본페이지 여부</b></td>
								<td class='search_box_item'>
									<input type='radio' class='textbox' name='main_default' id='main_default_y' size=50 value='Y' checked style='border:0px;' ".($main_default == "Y" ? "checked":"")."><label for='main_default_y'> 사용</label>
									<input type='radio' class='textbox' name='main_default' id='main_default_n' size=50 value='N' style='border:0px;' ".($main_default == "N" ? "checked":"")."><label for='main_default_n'> 미사용</label>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'> <b>메인페이지 전시기간</b></td>
								<td class='search_box_item' colspan=3>
									".search_date('main_start','main_end',$main_start,$main_end,'Y',' ' ,' validation=true title="메인페이지 전시기간" ')."
								</td>
							</tr>
							<tr>
								<td class='search_box_title' style='text-align:center;' colspan=4><b>메인페이지 그룹관리 </b></td>
							</tr>
							<tr bgcolor='#F8F9FA'>
								<td colspan=4>
									<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>                                    
										<tr>
											<td colspan='4' style='padding:10px;' id='group_area_parent'>
												<div class='slide-up-down-all'>
													<a href='javascript:slideUpDownAll();' class='slide-up-down-link'>
														<span class='plus'><img src='/admin/images/btn_group_all_open.png' alt='Plus'></span>
														<span class='minus'><img src='/admin/images/btn_group_all_close.png' alt='Minus'></span>
														<span class='label'>전체닫기/열기
													</a>
												</div>";
$gdb = new Database;
$gdb->query("SELECT * FROM shop_content_main_group_relation WHERE conm_ix= '$conm_ix' ORDER BY main_group_code ASC");

if($gdb->total || true){
	$group_total = $gdb->total;

	for($i=0;($i < $gdb->total || $i == 0);$i++){

		$gdb->fetch($i);

		$cmgr_ix = $gdb->dt[cmgr_ix];
		if($gdb->dt[main_group_code]){
			$group_no = $gdb->dt[main_group_code];
			$group_display_start = date("Y-m-d H:i:s",$gdb->dt[main_group_display_start]);
			$group_display_end = date("Y-m-d H:i:s",$gdb->dt[main_group_display_end]);
		}else{
			$group_no = $i+1;
		}

		$Contents .= "
												<div id='group_info_area".$i."' data-id='group_info_area".$i."' class='group_info_area_wrapper' group_code='".$group_no."'>
                                                <div class='group_info_header' style='position:relative;padding:10px 10px;width:100%;'><img src='/admin/images/dot_org.gif' > <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>상품그룹  (GROUP ".$group_no.")</b> <a onclick=\"add_table_main()\"><img src='../images/".$admininfo["language"]."/btn_goods_group_add.gif' border=0 align=absmiddle style='position:relative;top:-2px;cursor:pointer;'></a> ".($i == 0 ? "<!--삭제버튼-->":"<a onClick=\"del_table('group_info_area".$i."','".$group_no."');\"><img src='../images/".$admininfo["language"]."/btn_goods_group_del.gif' border=0 align=absmiddle style='position:relative;top:-2px;'></a>").
			"<a href='javascript:void(0);' class='slide-up-down-link'>
                                                    <span class='plus'><img src='/admin/images/btn_group_close.png' alt='Plus'></span>
                                                    <span class='minus'><img src='/admin/images/btn_group_open.png' alt='Minus'></span>
                                                    </a>"."
                                                    <input type='hidden'  name='cmgr_ix[" . $group_no . "]' value='" . $cmgr_ix . "'>
                                                    <input type='hidden' class='input-order' name='group_order[" . $group_no . "]' value='" . $group_no . "'>
                                                </div>
                                                <table width='100%' border='0' cellpadding='10' cellspacing='1' class='input_table_box'>
                                                    <col width='12%'>
                                                    <col width='30%'>
                                                    <col width='12%'>
                                                    <col width='38%'>
                                                    <input type='hidden' class='input-number' value='".$group_no."'>
                                                    <tr>
                                                        <td class='input_box_title'> <b>그룹명</b></td>
                                                        <td class='input_box_item'>
                                                            <table border=0 width=100%>
                                                                <col width=50px><col width=*>
                                                                <tr height=28 id='tableTitleK_".$group_no."'><td>국문</td><td><textarea name='group_title[".$group_no."]' id='group_title_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[main_group_title]."</textarea></td></tr>
                                                                <tr height=28 id='tableTitleE_".$group_no."'><td>영문</td><td><textarea name='group_title_en[".$group_no."]' id='group_title_en_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[main_group_title_en]."</textarea></td></tr>
                                                            </table>
                                                        </td>
                                                        <td class='input_box_title'> <b>그룹명 설정</b></td>
                                                        <td class='input_box_item'>
                                                            <input type='radio' name='s_group_title[".$group_no."]' id='s_group_title_L_".$group_no."' value='L' ".($gdb->dt[s_main_group_title] == "L" ? "checked":"")."><label for='s_group_title_L_".$group_no."'> 좌측정렬</label>
                                                            <input type='radio' name='s_group_title[".$group_no."]' id='s_group_title_C_".$group_no."' value='C' ".($gdb->dt[s_main_group_title] == "C" ? "checked":"")."><label for='s_group_title_C_".$group_no."'> 가운데정렬</label>
                                                            <input type='radio' name='s_group_title[".$group_no."]' id='s_group_title_R_".$group_no."' value='R' ".($gdb->dt[s_main_group_title] == "R" ? "checked":"")."><label for='s_group_title_R_".$group_no."'> 우측정렬</label><br><br>
                                                            진하게<input type='checkbox' name='b_group_title[".$group_no."]' id='b_group_title_".$group_no."' ".($gdb->dt[b_main_group_title] == "Y" ? "checked":"").">
                                                            기울기<input type='checkbox' name='i_group_title[".$group_no."]' id='i_group_title_".$group_no."' ".($gdb->dt[i_main_group_title] == "Y" ? "checked":"").">
                                                            밑줄<input type='checkbox' name='u_group_title[".$group_no."]' id='u_group_title_".$group_no."' ".($gdb->dt[u_main_group_title] == "Y" ? "checked":"").">
                                                            글자색 <input type='text' name='c_group_title[".$group_no."]' id='c_group_title_".$group_no."' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $gdb->dt[c_main_group_title] ? "#000000":$gdb->dt[c_main_group_title])."'>
                                                        </td>
                                                    </tr>         
                                                    <tr>
                                                        <td class='input_box_title'> <b>그룹간단설명</b></td>
                                                        <td class='input_box_item'>
                                                            <table border=0 width=100%>
                                                                <col width=50px><col width=*>
                                                                <tr height=28><td>국문</td><td><textarea name='group_explanation[".$group_no."]' id='group_explanation_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[main_group_explanation]."</textarea></td></tr>
                                                                <tr height=28><td>영문</td><td><textarea name='group_explanation_en[".$group_no."]' id='group_explanation_en_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[main_group_explanation]."</textarea></td></tr>
                                                            </table>
                                                        </td>
                                                        <td class='input_box_title'> <b>그룹간단설명 설정</b></td>
                                                        <td class='input_box_item'>
                                                            진하게<input type='checkbox' name='b_group_explanation[".$group_no."]' id='b_group_explanation_".$group_no."' ".($gdb->dt[b_main_group_explanation] == "Y" ? "checked":"").">
                                                            기울기<input type='checkbox' name='i_group_explanation[".$group_no."]' id='i_group_explanation_".$group_no."' ".($gdb->dt[i_main_group_explanation] == "Y" ? "checked":"").">
                                                            밑줄<input type='checkbox' name='u_group_explanation[".$group_no."]' id='u_group_explanation_".$group_no."' ".($gdb->dt[u_main_group_explanation] == "Y" ? "checked":"").">
                                                            글자색 <input type='text' name='c_group_explanation[".$group_no."]' id='c_group_explanation_".$group_no."' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $gdb->dt[c_main_group_explanation] ? "#000000":$gdb->dt[c_main_group_explanation])."'>
                                                        </td>
                                                    </tr>       
                                                    <tr>
                                                        <td class='input_box_title'> <b>컨텐츠 불러오기</b></td>
                                                        <td class='search_box_item' colspan=3>
															<table border=0 style=width:100%;>
															<col width='225px'>
															<col width='*'>
																<tr>
																	<td class='search_box_item' style='padding:10px 10px;' colspan=2>
																		<div id='goods_manual_area_1'>
																			<div style='width:100%;padding:5px;' id='group_product_area_1'>
																				<ui id='choiceGorupContent_".$group_no."'>".relationGroupContentList($cmgr_ix, $group_no, "clipart")."</ui>
																			</div>
																		</div>
																	</td>
																</tr>
																<tr>
																	<td class='search_box_item' style='padding:5px 5px;' colspan=2>
																		<button type='button' onclick='callGroupConetne(2, ".$group_no.");'>스타일 불러오기</button>
																		<button type='button' onclick='callGroupConetne(4, ".$group_no.");'>배너 불러오기</button>
																	</td>
																</tr>
															</table>
														</td>
                                                    </tr>
                                                    <tr>
                                                      <td class='input_box_title'> <b>사용여부</b></td>
                                                      <td class='input_box_item' colspan='3'>
                                                          <input type='radio' name='group_use[".$group_no."]' id='group_use_".$group_no."_y' size=50 value='Y' ".($gdb->dt[main_group_use] == "Y" || $gdb->dt[main_group_use] == "" ? "checked":"")."><label for='group_use_".$group_no."_y'>사용</label>
                                                          <input type='radio' name='group_use[".$group_no."]' id='group_use_".$group_no."_n' size=50 value='N' ".($gdb->dt[main_group_use] == "N" ? "checked":"")."><label for='group_use_".$group_no."_n'>미사용</label>
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td class='input_box_title'> <b>컨텐츠 전시기간</b></td>
                                                      <td class='input_box_item' colspan='3'>
                                                          ".search_date_arry('group_display_start','group_display_end',$group_display_start,$group_display_end,'Y',' ' ,' validation=true title="전시 기간" ',$group_no)."
                                                      </td>
                                                    </tr>
                                                </table>";
	}
}
$Contents .= " 
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<tr>
							<td colspan=3 align=right style='padding:10px;'>
								<table>
									<tr>
										<td><input type='checkbox' name='delete_cache' id='delete_cache' value='Y'><label for='delete_cache'>캐쉬삭제하기</label></td>
										<td><input type=image src='../images/".$admininfo["language"]."/b_save.gif' align=absmiddle border=0></td>
									</tr>
								</table>
							</td>
						</tr>
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
        


<iframe name='calcufrm' id='calcufrm' src='' width=0 height=0></iframe>
<Script Language='JavaScript'>

$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		//}else{
			//$('#end_datepicker').datepicker('setDate','+0d');
		}
	}

	});

	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});



function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}

function clickChk(num, val){
    if(val == 0){
        $('#bestTId'+num).css('display','');
        $('#bestEId'+num).css('display','');
        $('#bestCId'+num).css('display','');
    }else if(val == 1){
        $('#bestTId'+num).css('display','none');
        $('#bestEId'+num).css('display','none');
        $('#bestCId'+num).css('display','none');
    }
}



if('$best_use' == 'Y' || '$best_use' == ''){
	clickChk(1, 0)
}else{
	clickChk(1, 1)
}

if('$best_use2' == 'Y'){
	clickChk(2, 0)
}else{
	clickChk(2, 1)
}

if('$best_use3' == 'Y'){
	clickChk(3, 0)
}else{
	clickChk(3, 1)
}

if('$best_use4' == 'Y'){
	clickChk(4, 0)
}else{
	clickChk(4, 1)
}

if('$best_use5' == 'Y'){
	clickChk(5, 0)
}else{
	clickChk(5, 1)
}

if('$best_use6' == 'Y'){
	clickChk(6, 0)
}else{
	clickChk(6, 1)
}

if('$best_use7' == 'Y'){
	clickChk(7, 0)
}else{
	clickChk(7, 1)
}

if('$best_use8' == 'Y'){
	clickChk(8, 0)
}else{
	clickChk(8, 1)
}

if('$best_use9' == 'Y'){
	clickChk(9, 0)
}else{
	clickChk(9, 1)
}

if('$best_use10' == 'Y'){
	clickChk(10, 0)
}else{
	clickChk(10, 1)
}

sortGroup(true);
my_init('$group_total');
</Script>";



$Script = "<script language='javascript' src='../display/main_goods.js'></script>\n
$Script";

if($agent_type == "M"  || $agent_type == "mobile"){
	if($mmode == "pop"){
		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->OnloadFunction = "";
		$P->strLeftMenu = mshop_menu();
		$P->Navigation = $navigation;
		$P->title = $title;
		$P->NaviTitle = $title;
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->addScript = $Script;
		$P->Navigation = $navigation;
		$P->title = $title;
		$P->strLeftMenu = mshop_menu();
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
}else{
	if($mmode == "pop"){

		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->OnloadFunction = "";
		$P->strLeftMenu = display_menu();
		$P->Navigation = "컨텐츠전시 > 메인페이지 관리 > 메인페이지 등록";
		$P->title = "메인페이지 등록";
		$P->NaviTitle = "메인페이지 등록";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->addScript = $Script;
		$P->Navigation = "컨텐츠전시 > 메인페이지 관리 > 메인페이지 등록";
		$P->title = "메인페이지 등록";
		$P->OnloadFunction = "";//showSubMenuLayer('storeleft');
		$P->strLeftMenu = display_menu();
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
}


?>
