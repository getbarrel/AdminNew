<?

if(!$_GET['cid'] || !$_GET['content_type'] || !$_GET['depth']){
	echo "<Script Language='JavaScript'>alert('등록할 컨텐츠가 선택되지 않았습니다.')</Script>";
	echo "<Script Language='JavaScript'>document.location.href='./content_list.php';</Script>";
	exit;
}

include("../class/layout.class");
//include("../webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/menu.tmp.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/menuline.class");

$db = new Database;

$cid			= $_GET['cid'];
$content_type	= $_GET['content_type'];
$depth			= $_GET['depth'];
$mode			= $_GET['mode'];

if($mode == "copy"){
	$mode = "Ins";
}

$slave_db->query("SELECT * FROM shop_content where con_ix != '$con_ix'");
$etc_content = $slave_db->fetchall();

$slave_db->query("SELECT * FROM shop_content where con_ix= '$con_ix'");

if($slave_db->total) {
    $slave_db->fetch();
    $mall_ix        = $slave_db->dt[mall_ix];
    $title          = $slave_db->dt[title];
    $title_en       = $slave_db->dt[title_en];
    $b_title        = $slave_db->dt[b_title];
    $i_title        = $slave_db->dt[i_title];
    $u_title        = $slave_db->dt[u_title];
    $c_title        = $slave_db->dt[c_title];
    $s_title        = $slave_db->dt[s_title];
    $b_title_b      = $slave_db->dt[b_title_b];
    $i_title_b      = $slave_db->dt[i_title_b];
    $u_title_b      = $slave_db->dt[u_title_b];
    $c_title_b      = $slave_db->dt[c_title_b];
    $preface        = $slave_db->dt[preface];
    $preface_en     = $slave_db->dt[preface_en];
    $b_preface      = $slave_db->dt[b_preface];
    $i_preface      = $slave_db->dt[i_preface];
    $u_preface      = $slave_db->dt[u_preface];
    $c_preface      = $slave_db->dt[c_preface];
    $explanation    = $slave_db->dt[explanation];
    $explanation_en = $slave_db->dt[explanation_en];
    $b_explanation  = $slave_db->dt[b_explanation];
    $i_explanation  = $slave_db->dt[i_explanation];
    $u_explanation  = $slave_db->dt[u_explanation];
    $c_explanation  = $slave_db->dt[c_explanation];
    $list_img       = $slave_db->dt[list_img];
    $list_img_m     = $slave_db->dt[list_img_m];
    $recommend_img  = $slave_db->dt[recommend_img];
    $category_use   = $slave_db->dt[category_use];
    $b_category     = $slave_db->dt[b_category];
    $i_category     = $slave_db->dt[i_category];
    $u_category     = $slave_db->dt[u_category];
    $c_category     = $slave_db->dt[c_category];
    $r_category     = $slave_db->dt[r_category];
    $e_category     = $slave_db->dt[e_category];
    $ba_category    = $slave_db->dt[ba_category];
    $bo_category    = $slave_db->dt[bo_category];
    $display_gubun  = $slave_db->dt[display_gubun];
    $display_use    = $slave_db->dt[display_use];
    $display_state  = $slave_db->dt[display_state];
    $display_date_use = $slave_db->dt[display_date_use];
    $display_start  = date("Y-m-d H:i:s",$slave_db->dt[display_start]);
    $display_end    = date("Y-m-d H:i:s",$slave_db->dt[display_end]);
    $comment_board_ix   = $slave_db->dt[comment_board_ix];
    $content_text_pc    = $slave_db->dt[content_text_pc];
    $content_text_mo    = $slave_db->dt[content_text_mo];
    $style_img_num      = $slave_db->dt[style_img_num];
    $player_profile     = $slave_db->dt[player_profile];
    $player_profile_en  = $slave_db->dt[player_profile_en];
    $b_profile          = $slave_db->dt[b_profile];
    $i_profile          = $slave_db->dt[i_profile];
    $u_profile          = $slave_db->dt[u_profile];
    $c_profile          = $slave_db->dt[c_profile];
    $player_comment     = $slave_db->dt[player_comment];
    $player_comment_en  = $slave_db->dt[player_comment_en];
    $b_comment          = $slave_db->dt[b_comment];
    $i_comment          = $slave_db->dt[i_comment];
    $u_comment          = $slave_db->dt[u_comment];
    $c_comment          = $slave_db->dt[c_comment];

    $player_subject     = json_decode($slave_db->dt[player_subject]);
    $player_instar      = json_decode($slave_db->dt[player_instar]);
    $player_youtube     = json_decode($slave_db->dt[player_youtube]);

    $sort     = $slave_db->dt[sort];
}

function relationProductList($con_ix)
{
    global $admin_config;
    $db = new Database;

    $group_code = 9999;
    $disp_type = 'clipart';

    $sql = "SELECT cpr.*, p.id, p.pcode, p.shotinfo, p.pname, p.listprice, p.sellprice,  p.wholesale_sellprice, p.wholesale_price,  p.reserve, p.state, p.disp 
            FROM " . TBL_SHOP_PRODUCT . " p, shop_content_product_relation cpr WHERE p.id = cpr.pid AND cpr.con_ix = '" . $con_ix . "' ORDER BY cpr.sort ASC 
    ";
    $db->query($sql);
    $products = $db->fetchall();

    if ($db->total == 0) {
        if ($disp_type == "clipart") {
            $mString = '<ul id="productList_' . $group_code . '" name="productList" class="productList"></ul>';
        }
    } else {
        $i = 0;
        if ($disp_type == "clipart") {
            $mString = '<ul id="productList_' . $group_code . '" name="productList" class="productList"></ul>' . "\n";
            $mString .= '<script>' . "\n";
            $mString .= 'ms_productSearch.groupCode = ' . $group_code . ";\n";
            for ($i = 0; $i < count($products); $i++) {
                $db->fetch($i);
                $imgPath = PrintImage($admin_config['mall_data_root'] . '/images/product', $products[$i]['id'], 'c');
                $mString .= 'ms_productSearch._setProduct("productList_' . $group_code . '", "M", "' . $products[$i]['id'] . '", "' . $imgPath . '", "' . addslashes(addslashes(trim($products[$i]['pname']))) . '", "' . addslashes(addslashes(trim($products[$i]['brand_name']))) . '", "' . $products[$i]['sellprice'] . '", "' . $products[$i]['listprice'] . '", "' . $products[$i]['reserve'] . '", "' . $products[$i]['coprice'] . '", "' . $products[$i]['wholesale_price'] . '", "' . $products[$i]['wholesale_sellprice'] . '", "' . $products[$i]['disp'] . '", "' . $products[$i]['state'] . '", "' . $products[$i]['dcprice'] . '");' . "\n";

            }
            $mString .= '</script>' . "\n";
        }
    }
    return $mString;
}

function relationGroupContentList($cgr_ix, $group_code, $disp_type=""){
    global $admin_config;
    $db = new Database;

    $sql = "SELECT c.con_ix, c.title, c.list_img 
            FROM shop_content c, shop_content_group_relation_content cgrc 
            WHERE c.con_ix = cgrc.con_ix AND cgrc.cgr_ix = '" . $cgr_ix . "' 
            ORDER BY cgrc.sort ASC 
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
                $mString .= '<li id="li_contentImage_'.$group_code.'_3_'.$products[$i]['con_ix'].'" vieworder="'.$products[$i]['con_ix'].'" viewcnt="'.$products[$i]['con_ix'].'" style=float:left;width:110px;>' . "\n";
                $mString .= '<table width=95% cellspacing=1 cellpadding=0 style=table-layout:fixed;height:180px;>' . "\n";
                $mString .= '<tr><td align=center>' . "\n";
                $mString .= '<img src="'.$_SESSION["admin_config"][mall_data_root].'/images/content/'.$products[$i]['con_ix'].'/'.$products[$i]['list_img'].'" width=100px height=100px>' . "\n";
                $mString .= '<br>'.nl2br($products[$i]['title']).'' . "\n";
                $mString .= '<input type=hidden name=group_con_ix['.$group_code.'][] id=group_con_ix_'.$products[$i]['con_ix'].' value="'.$products[$i]['con_ix'].'">' . "\n";
                $mString .= '<input type=hidden name=group_con_gubun['.$group_code.'][] id=group_con_ix_'.$products[$i]['con_ix'].' value="'.$products[$i]['con_ix'].'">' . "\n";
                $mString .= '</td></tr>' . "\n";
                $mString .= '<tr><td align=center><button type=button onclick=imgGroupDel("'.$products[$i]['con_ix'].'",3,'.$group_code.')>삭제</td></tr>' . "\n";
                $mString .= '</table></li>' . "\n";
            }
            $mString .= '<script>' . "\n";
            $mString .= '$("#choiceGorupContent_'.$group_code.'").sortable();'."\n";
            $mString .= '</script>' . "\n";
        }
    }
    return $mString;
}

function relationGroupProductList($cgr_ix, $group_code, $disp_type=""){

    global $start,$page, $orderby, $admin_config, $erpid,$slave_db;

    /*if($cgr_ix == ''){
        $products = 0;
    }else{*/
        $sql = "SELECT p.id, p.pcode, p.shotinfo, p.pname, p.listprice, p.sellprice,  p.wholesale_sellprice, p.wholesale_price,  p.reserve, p.view_cnt, p.regdate, p.state, p.disp, cgpr.cgpr_ix, cgpr.con_ix, cgpr.sort, p.brand_name
					FROM ".TBL_SHOP_PRODUCT." p, shop_content_group_product_relation cgpr
					WHERE p.id = cgpr.pid AND cgpr.cgr_ix = '$cgr_ix' ORDER BY cgpr.sort ASC "; //and p.disp = 1 limit $start,$max
        $slave_db->query($sql);
        $products = $slave_db->fetchall();
    //}

    if(count($products) == 0){
        if($disp_type == "clipart"){
            $mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>';
        }
    }else{
        $i=0;
        if($disp_type == "clipart"){
            $mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>'."\n";
            $mString .= '<script>'."\n";
            $mString .= 'ms_productSearch.groupCode = '.$group_code.";\n";
            for($i=0;$i<count($products);$i++){
                //$imgPath = PrintImage($admin_config['mall_data_root'].'/images/product', $products[$i]['id'], 'c');
                $imgPath = PrintImage($admin_config['mall_data_root'].'/images/addimgNew', $products[$i]['id'], 'slist');
                $mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$products[$i]['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($products[$i]['pname']))).'", "'.addslashes(addslashes(trim($products[$i]['brand_name']))).'", "'.$products[$i]['sellprice'].'", "'.$products[$i]['listprice'].'", "'.$products[$i]['reserve'].'", "'.$products[$i]['coprice'].'", "'.$products[$i]['wholesale_price'].'", "'.$products[$i]['wholesale_sellprice'].'", "'.$products[$i]['disp'].'", "'.$products[$i]['state'].'", "'.$products[$i]['dcprice'].'", "'.$products[$i]['vieworder'].'", "'.$products[$i]['view_cnt'].'", "'.$products[$i]['regdate'].'");'."\n";
            }
            $mString .= '</script>'."\n";
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
<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>
<script type='text/javascript' src='/admin/js/ui/jquery-ui-1.8.9.custom.js'></script>
<script type='text/javascript' src='/admin/js/ui/jquery.ui.core.js'></script>
<script type='text/javascript' src='/admin/js/ui/jquery.ui.widget.js'></script>
<script type='text/javascript' src='/admin/js/ui/jquery.ui.mouse.js'></script>
<script type='text/javascript' src='/admin/js/jquery.easing-1.3.js'></script>
<script type='text/javascript' src='/admin/js/jquery.quicksand.js'></script>
<script type='text/javascript' src='/admin/js/jquery.multisortable.js'></script>
<script type='text/javascript' src='/admin/js/ms_productSearch.js?=".rand()."'></script>
<script language='javascript' src='../search.js'></script>
<script language='javascript' src='../display/content_edit.js'></script>
<script language='javascript' src='./color/jscolor.js'></script>
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
    $( '#image_area' ).sortable();
});

function SubmitX(frm){
	/*for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}*/

	return true;
}

function categoryadd()
{
	var ret;
	var str = new Array();
	var dupe_bool = false;
	var obj = $('form[name=thisContentform]').find('select[class^=cid]');
	var admin_level = '" . $_SESSION["admininfo"]["admin_level"] . "';

	if(admin_level == 8){
		if($('input[type=radio][name=basic]').length > 0){
			alert('카테고리 입력은 한개만 가능합니다. ');
			return false;
		}
	}

	obj.each(function(index){
		if($(this).find('option:selected').val()){
			str[str.length] =  $(this).find('option:selected').text();
			ret = $(this).find('option:selected').val();
		}
	});

	if (!ret){
		alert('카테고리를 선택해주세요');//'카테고리를 선택해주세요'
		return;
	}
    
    var category = 'category[]';
    var cnt = document.getElementsByName(category);
    var totalCnt = 0;
    
    for(i=0;i<cnt.length;i++) {  
        if(cnt[i].value == ret){
            alert('이미등록된 카테고리 입니다.');
			//'이미등록된 카테고리 입니다.'
			return;
        }
    }

	var obj = $('#objCategory');

	obj.append(\"<tr id='num_tr' height=30 class=''><td><input type=hidden name=category[] id='_category' value='\" + ret + \"' style='display:none'><input type=hidden name=depth[] id='_depth' value='\" + $('form[name=form_cupon]').find('input[name=selected_depth]').val() + \"' style='display:none'></td><td></td><td > \"+str.join(\" > \")+\" </td><td align=right style='padding:5px 25px 5px 5px;'><img src='../images/" . $_SESSION["admininfo"]["language"] . "/btc_del.gif' border=0 onClick=\\\" $(this).closest('tr').remove();\\\" style='cursor:point;'></td></tr>\");
	 
}

function category_del(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objCategory');
	obj.deleteRow(idx);
}

function imgGroupDel(val, gubun, groupNum){
	$('#li_contentImage_'+groupNum+'_'+gubun+'_'+val).remove();
}

//상품이미지 신규개발 HMPart
var sel_file;
 
$(document).ready(function() {
	$('#productImg').on('change', handleImgFileSelect);
});

function handleImgFileSelect(e) {
	var files = e.target.files;
	var filesArr = Array.prototype.slice.call(files);

	var reg = /(.*?)\/(jpg|jpeg|png|bmp|gif)$/;

	filesArr.forEach(function(f) {
		if (!f.type.match(reg)) {
			alert('이미지만 사용 가능합니다.');
			$('#productImg').val('');
			return;
		}

		/* 이미지 미리보기 
		sel_file = f;

		var reader = new FileReader();
		reader.onload = function(e) {
			$('#imgView').attr('src', e.target.result);
		}
		reader.readAsDataURL(f);
		*/
	});
}

var formData

function imgAdd(imgFrm){
	var fileCheck = $('#productImg').val();

	if(!fileCheck){
		alert('업로드할 파일을 선택하세요.');
		return false;
	}

	formData = new FormData(imgFrm);

	$.ajax({
		url: './fileupload_ok.php', // url where upload the image
		type : 'POST',
		dataType : 'json',
		enctype : 'multipart/form-data',
		processData : false,
		contentType : false,
		data : formData,
		async : false,
		success : function(datas){
			var ele = document.getElementById('image_area');
			var eleCount = ele.childElementCount;
			eleCount = eleCount + 1;

			var str = '';

			str += '<li id=li_productImage_'+eleCount+' vieworder=' + eleCount + ' viewcnt=' + eleCount + ' style=float:left;width:110px;>';
			str += '<table width=95% cellspacing=1 cellpadding=0 style=table-layout:fixed;height:180px;>';
			//str += '<tr><td class=small style=background-color:gray;color:#ffffff;height:25%;width:100%;text-align:center; nowrap>'+eleCount+' 이미지</td></tr>';
			str += '<tr><td><img src='+datas.dir+'/'+datas.img+' width=100px height=100px>';
			str += '<input type=hidden name=imgName[] id=imgName_'+eleCount+' value='+datas.img+' />';
			str += '<input type=hidden name=imgTemp[] id=imgTemp_'+eleCount+' value='+datas.dir+' />';
			str += '</td></tr>';
			str += '<tr><td align=center><button type=button onclick=ingDel('+eleCount+')>삭제</td></tr>';
			str += '</table>';
			str += '</li>';

			$('#image_area').append(str);

			$('#productImg').val('');
		}
	});
}

function ingDel(imgCnt){
	$.ajax({
		url: './fileupload_ok.php', // url where upload the image
		type : 'POST',
		dataType : 'json',
		data : {'mode':'del', 'imgTemp':$('#imgTemp_'+imgCnt).val(), 'imgName':$('#imgName_'+imgCnt).val(), 'content_type':$('#content_type').val()},
		success : function(backData){
			$('#imgInsYN').prop('checked', true);
			$('#li_productImage_'+imgCnt).remove();
		}
	});
}
// //상품이미지 신규개발

function loadCategory(obj,target) {
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
				$('select[class=cid]').each(function(){
					if(parseInt($(this).attr('depth')) > parseInt(depth)){
						$(this).find('option').not(':first').remove();
					}
				});
				 
				if(datas != null){
					$.each(datas, function(i, data){ 
							$('select[name='+target+']').append(\"<option value='\"+data.cid+\"'>\"+data.cname+\"</option>\");
					});  
				}
			} 
		}); 
 
}

function init(){
    var frm = document.thisContentform;
";
    $Script .= "
    CKEDITOR.replace('content_text_pc',{
        docType : '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">',
        font_defaultLabel : '굴림',
        font_names : '굴림/Gulim;돋움/Dotum;바탕/Batang;궁서/Gungsuh;Arial/Arial;Tahoma/Tahoma;Verdana/Verdana',
        fontSize_defaultLabel : '12px',
        fontSize_sizes : '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;',
        language :'ko',
        resize_enabled : false,
        enterMode : CKEDITOR.ENTER_BR,
        shiftEnterMode : CKEDITOR.ENTER_P,
        startupFocus : false,
        uiColor : '#EEEEEE',
        toolbarCanCollapse : false,
        menu_subMenuDelay : 0,
        allowedContent : true,";
    if($admininfo[admin_level]==8 && $act=='update' && $disp !='9' ){
        $Script .= "readOnly : true, ";
    }

    $Script .= "
        toolbar : [['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','TextColor','BGColor','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','-','Find','Replace','SelectAll','RemoveFormat','-','Image','Flash','Table','SpecialChar'],'/',['Source','-','ShowBlocks','-','Font','FontSize','Undo','Redo','-','About','ImageMaps']],
        filebrowserImageUploadUrl : '/admin/ckeditor/upload.php',
        height:500});";


    $Script .= "
    CKEDITOR.replace('content_text_mo',{
        docType : '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">',
        font_defaultLabel : '굴림',
        font_names : '굴림/Gulim;돋움/Dotum;바탕/Batang;궁서/Gungsuh;Arial/Arial;Tahoma/Tahoma;Verdana/Verdana',
        fontSize_defaultLabel : '12px',
        fontSize_sizes : '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;',
        language :'ko',
        resize_enabled : false,
        enterMode : CKEDITOR.ENTER_BR,
        shiftEnterMode : CKEDITOR.ENTER_P,
        startupFocus : false,
        uiColor : '#EEEEEE',
        toolbarCanCollapse : false,
        menu_subMenuDelay : 0,
        allowedContent : true,";
    if($admininfo[admin_level]==8 && $act=='update' && $disp !='9' ){
        $Script .= "readOnly : true, ";
    }

    $Script .= "
        toolbar : [['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','TextColor','BGColor','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','-','Find','Replace','SelectAll','RemoveFormat','-','Image','Flash','Table','SpecialChar'],'/',['Source','-','ShowBlocks','-','Font','FontSize','Undo','Redo','-','About','ImageMaps']],
        filebrowserImageUploadUrl : '/admin/ckeditor/upload.php',
        height:500});
}

function init_arry(num){
    var frm = document.thisContentform;
";
$Script .= "
    CKEDITOR.replace('group_text_pc_'+num,{
        docType : '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">',
        font_defaultLabel : '굴림',
        font_names : '굴림/Gulim;돋움/Dotum;바탕/Batang;궁서/Gungsuh;Arial/Arial;Tahoma/Tahoma;Verdana/Verdana',
        fontSize_defaultLabel : '12px',
        fontSize_sizes : '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;',
        language :'ko',
        resize_enabled : false,
        enterMode : CKEDITOR.ENTER_BR,
        shiftEnterMode : CKEDITOR.ENTER_P,
        startupFocus : false,
        uiColor : '#EEEEEE',
        toolbarCanCollapse : false,
        menu_subMenuDelay : 0,
        allowedContent : true,";
if($admininfo[admin_level]==8 && $act=='update' && $disp !='9' ){
    $Script .= "readOnly : true, ";
}

$Script .= "
        toolbar : [['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','TextColor','BGColor','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','-','Find','Replace','SelectAll','RemoveFormat','-','Image','Flash','Table','SpecialChar'],'/',['Source','-','ShowBlocks','-','Font','FontSize','Undo','Redo','-','About','ImageMaps']],
        filebrowserImageUploadUrl : '/admin/ckeditor/upload.php',
        height:500});";


$Script .= "
    CKEDITOR.replace('group_text_mo_'+num,{
        docType : '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">',
        font_defaultLabel : '굴림',
        font_names : '굴림/Gulim;돋움/Dotum;바탕/Batang;궁서/Gungsuh;Arial/Arial;Tahoma/Tahoma;Verdana/Verdana',
        fontSize_defaultLabel : '12px',
        fontSize_sizes : '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;',
        language :'ko',
        resize_enabled : false,
        enterMode : CKEDITOR.ENTER_BR,
        shiftEnterMode : CKEDITOR.ENTER_P,
        startupFocus : false,
        uiColor : '#EEEEEE',
        toolbarCanCollapse : false,
        menu_subMenuDelay : 0,
        allowedContent : true,";
if($admininfo[admin_level]==8 && $act=='update' && $disp !='9' ){
    $Script .= "readOnly : true, ";
}

$Script .= "
        toolbar : [['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','TextColor','BGColor','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','-','Find','Replace','SelectAll','RemoveFormat','-','Image','Flash','Table','SpecialChar'],'/',['Source','-','ShowBlocks','-','Font','FontSize','Undo','Redo','-','About','ImageMaps']],
        filebrowserImageUploadUrl : '/admin/ckeditor/upload.php',
        height:500});
}
</script>";

$Contents = "<link rel='stylesheet' href='../colorpicker/farbtastic.css' type='text/css' />

<table cellpadding=0 cellspacing=0 border=0 width='100%'>
	<tr>
		<td align='left' colspan=3> ".GetTitleNavigation("컨텐츠관리", "컨텐츠관리 > 컨텐츠등록")."</td>
	</tr>";
//실적용 시 아래 if에서  && $mode =="all" 제거
if($mode !="Upd") {
$Contents .= "<tr>
		<td align='left' colspan=6 >
		<div style='width:97%;background-color:#fff7da;padding:20px;text-align:center;margin-bottom:4px;'>
		<b style='font-size:17px;'> 기획전 불러오기 : </b>
		<select name='' onchange=\"document.location.href='?mode=copy&cid=".$cid."&content_type=".$content_type."&depth=".$depth."&con_ix='+this.value\" style='font-size:15px;height:30px;'>";
		$Contents .= "<option value=''>등록된 기획전 목록</option>";
		for($i=0; $i < count($etc_content);$i++){
			$Contents .= "<option value='".$etc_content[$i][con_ix]."' ".($etc_content[$i][con_ix] == $_GET["con_ix"] ? "selected":"")."> ".$etc_content[$i][title]."</option>";
		}
	$Contents .= "
		</select>
		</div>
		</td>
	</tr>";
}
	$Contents .= "
	<tr>
		<td valign=top width='100%' align='left' style=''>";
if($content_type == 1){			// 기획전
$Contents .= "<table cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td width='100%' align='right' valign='top'>
						<form name='thisContentform' method='post' onSubmit='return SubmitX(this)' enctype='multipart/form-data' action='../display/content.save.php' target='calcufrm' style='display:inline;'>
						<input type='hidden' name='content_type' id='content_type' value='$content_type'>
						<input type='hidden' name='mode' value='$mode'>
						<input type='hidden' name='this_depth' value='$depth'>
						<input type='hidden' name='cid' value='$cid'>
						<input type='hidden' name='con_ix' value='$con_ix'>
						<table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
							<col width='12%'>
							<col width='30%'>
							<col width='12%'>
							<col width='38%'>
							<tr bgcolor=#ffffff>
								<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류위치설정 </b></td>
								<td class='input_box_item' nowrap colspan='3'>
									<div >".getContentPathByAdmin($cid, $depth)."</div>
								</td>
							</tr>
							<tr bgcolor=#ffffff>
								<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>프론트전시여부 </b></td>
								<td class='input_box_item' nowrap colspan='3'>
									<div >".GetDisplayDivision($mall_ix, "select")."</div>
								</td>
							</tr>
							<tr bgcolor=#ffffff>
								<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>기획전 제목 </b><img src='".$required3_path."'></td>
								<td class='input_box_item' nowrap>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='title' id='title' style='width:85%;height:15px'>".$title."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='title_en' id='title_en' style='width:85%;height:15px'>".$title_en."</textarea></td></tr>
									</table>
								</td>
								<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>기획전 제목 설정 </b></td>
								<td class='input_box_item' nowrap>
									<input type='radio' name='s_title' id='s_title_L' value='L' ".("L" == $s_title || "" == $s_title  ? "checked":"")."><label for='s_title_L'> 좌측정렬</label>
									<input type='radio' name='s_title' id='s_title_C' value='C' ".("C" == $s_title ? "checked":"")."><label for='s_title_C'> 가운데정렬</label>
									<input type='radio' name='s_title' id='s_title_R' value='R' ".("R" == $s_title ? "checked":"")."><label for='s_title_R'> 우측정렬</label><br><br>
									진하게<input type='checkbox' name='b_title' id='b_title' ".("Y" == $b_title ? "checked":"").">
									기울기<input type='checkbox' name='i_title' id='i_title' ".("Y" == $i_title ? "checked":"").">
									밑줄<input type='checkbox' name='u_title' id='u_title' ".("Y" == $u_title ? "checked":"").">
									글자색 <input type='text' name='c_title' id='c_title' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_title ? "#000000":$c_title)."'>
								</td>
							</tr>
							<tr bgcolor=#ffffff>
								<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>기획전 머리말 </b></td>
								<td class='input_box_item' nowrap>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='preface' id='preface' style='width:85%;height:15px'>".$preface."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='preface_en' id='preface_en' style='width:85%;height:15px'>".$preface_en."</textarea></td></tr>
									</table>
								</td>
								<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>기획전 머리말 설정 </b></td>
								<td class='input_box_item' nowrap>
									진하게<input type='checkbox' name='b_preface' id='b_preface' ".("Y" == $b_preface ? "checked":"").">
									기울기<input type='checkbox' name='i_preface' id='i_preface' ".("Y" == $i_preface ? "checked":"").">
									밑줄<input type='checkbox' name='u_preface' id='u_preface' ".("Y" == $u_preface ? "checked":"").">
									글자색 <input type='text' name='c_preface' id='c_preface' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_preface ? "#000000":$c_preface)."'>
									
								</td>
							</tr>
							<tr bgcolor=#ffffff>
								<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>컨텐츠 간단설명 </b></td>
								<td class='input_box_item' nowrap>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><textarea name='explanation' id='explanation' style='width:85%;height:15px'>".$explanation."</textarea></td></tr>
										<tr height=28><td>영문</td><td><textarea name='explanation_en' id='explanation_en' style='width:85%;height:15px'>".$explanation_en."</textarea></td></tr>
									</table>
								</td>
								<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>기획전 머리말 설정 </b></td>
								<td class='input_box_item' nowrap>
									진하게<input type='checkbox' name='b_explanation' id='b_explanation' ".("Y" == $b_explanation ? "checked":"").">
									기울기<input type='checkbox' name='i_explanation' id='i_explanation' ".("Y" == $i_explanation ? "checked":"").">
									밑줄<input type='checkbox' name='u_explanation' id='u_explanation' ".("Y" == $u_explanation ? "checked":"").">
									글자색 <input type='text' name='c_explanation' id='c_explanation' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_explanation ? "#000000":$c_explanation)."'>
								</td>
							</tr>
							<tr bgcolor=#ffffff>
								<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>리스트이미지<font size='font-size:small'>(1:1)</font> </b></td>
								<td class='input_box_item' colspan='3'>
                                    <table border=0>
                                        <col width='505px'>
                                        <col width='*'>
                                        <tr>
                                            <td>
                                                <input type=file name='list_img' id='list_img' class='textbox' size=25 style='font-size:8pt'>
                                            </td>
                                            <td ".$img_view_style." rowspan=2>
                                                <a class='screenshot'  rel='".$_SESSION["admin_config"][mall_data_root]."/images/content/".$con_ix."/".$list_img."'><img src='../v3/images/btn/bt_preview.png'   style='cursor:pointer'></a>";
                                            if($list_img != ""){
                                                $Contents .= "<input type='checkbox' name='list_img_del' id='list_img_del'>리스트이미지 삭제";
                                            }
                                        $Contents .= "</td>
                                        </tr>
                                        <tr height=10><td colspan= class='small' style='padding-top:5px;'>※ 기획전 목록, 메인 페이지 추천 기획전, 상품상세 페이지 등 1:1 비율 이미지</td></tr>
                                    </table>
                                </td>
							</tr>
							<tr bgcolor=#ffffff>
								<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>추천컨텐츠이미지<font size='font-size:small'>(3:4)</font> </b></td>
								<td class='input_box_item' colspan='3'>
                                    <table border=0>
                                        <col width='505px'>
                                        <col width='*'>
                                        <tr>
                                            <td>
                                                <input type=file name='recommend_img' id='recommend_img' class='textbox' size=25 style='font-size:8pt'>
                                            </td>
                                            <td ".$img_view_style." rowspan=2>
                                                <a class='screenshot'  rel='".$_SESSION["admin_config"][mall_data_root]."/images/content/".$con_ix."/".$recommend_img."'><img src='../v3/images/btn/bt_preview.png'   style='cursor:pointer'></a>";
                                            if($list_img != ""){
                                                $Contents .= "<input type='checkbox' name='recommend_img_del' id='recommend_img_del'>추천컨텐츠이미지 삭제";
                                            }
                                        $Contents .= "</td>
                                        </tr>
                                        <tr height=10><td colspan= class='small' style='padding-top:5px;'>※ 메인 페이지 추천 컨텐츠 등 3:4 비율 이미지</td></tr>
                                    </table>
                                </td>
							</tr>
							<tr bgcolor=#ffffff>
								<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>상품분류별 연관컨턴츠 </b></td>
								<td class='input_box_item' colspan='3'>
                                    <input type='hidden' name='selected_cid' value=''>
                                    <input type='hidden' name='selected_depth' value=''>
                                    <input type='hidden' id='_category'>
                                    <table border=0 cellpadding=0 cellspacing=0>
                                        <tr>
                                            <td style='padding-right:5px;'>" . getCategoryList3("대분류", "cid0", " class='cid' onChange=\"loadCategory($(this),'cid1',2)\" title='대분류' ", 0, $cid, "cid0") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("중분류", "cid1", " class='cid' onChange=\"loadCategory($(this),'cid2',2)\" title='중분류'", 1, $cid, "cid1") . " </td>
                                            <td style='padding-right:5px;'>" . getCategoryList3("소분류", "cid2", " class='cid' onChange=\"loadCategory($(this),'cid3',2)\" title='소분류'", 2, $cid, "cid2") . " </td>
                                            <td>" . getCategoryList3("세분류", "cid3", " class='cid' onChange=\"loadCategory($(this),'cid_1',2)\" title='세분류'", 3, $cid, "cid3") . "</td>
                                            <td style='padding-left:10px'><img src='../images/" . $admininfo["language"] . "/btn_add.gif' align=absmiddle border=0 onclick=\"categoryadd()\"></td>
                                        </tr>
                                    </table>
                                    <table width=90% cellpadding=0 cellspacing=0 border=0 id=objCategory style='margin-top:5px;'>
                                        <col width=1>
                                        <col width=10>
                                        <col width=545>
                                        <col width=*>";
                                    if ($con_ix != "") {
                                        $sql = "SELECT cr.cid, cr.cr_ix FROM shop_content_relation cr, shop_category_info c
                                                WHERE c.cid = cr.cid AND con_ix = '" . $con_ix . "'
                                                ORDER BY cr.cr_ix ASC
                                        ";
                                    }
                                    $db->query($sql);

                                    for ($j = 0; $j < $db->total; $j++) {
                                        $db->fetch($j);
                                        $Contents .= "<tr height=23><td><input type=text name=category[] id='_category' value='" . $db->dt[cid] . "' style='display:none'></td><td></td><td>" . getCategoryPathByAdmin($db->dt[cid], 4) . "</td><td align=right style='padding:5px 25px 5px 5px;'><a href='javascript:void(0)' onClick='category_del(this.parentNode.parentNode)'><img src='../images/" . $_SESSION["admininfo"]["language"] . "/btc_del.gif' border=0></a></td></tr>";
                                    }
                                    $Contents .= "
                                    </table>
                                </td>
							</tr>
							<tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>상품별 연관컨텐츠 </b></td>
                                <td class='search_box_item' style='padding:10px 10px;' colspan=3><a name='goods_display_type_9999'></a>
                                    <div id='goods_manual_area_9999' style='display:block;'>
                                        <div style='width:100%;padding:5px;' id='group_product_area_9999' >".relationProductList($con_ix)."</div>
                                        <div style='width:100%;float:left;'>
                                            <span class=small>* 드래그앤 드롭으로 전시 순서를 변경할 수 있습니다.<br /><font style='color:red'>* 더블클릭 으로 등록된 이미지 개별 삭제 가능합니다.</font></span>
                                        </div>
                                        <div style='display:block;float:left;margin-top:10px;'>
                                            <a href=\"#goods_display_type_9999\" id='btn_goods_search_add' onclick=\"ms_productSearch.show_productSearchBox(event,9999,'productList_9999','clipart','77');\"><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>카테고리 사용여부 </b></td>
                                <td class='input_box_item' colspan=3>
                                    <input type='radio' name='category_use' id='category_use_Y' value='Y' ".("Y" == $category_use || "" == $category_use  ? "checked":"")."><label for='category_use_Y' >사용</label>
                                    <input type='radio' name='category_use' id='category_use_N' value='N' ".("N" == $category_use ? "checked":"")."> <label for='category_use_N' >미사용</label>
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff style='height:100px;'>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>카테고리 명 설정 </b></td>
                                <td class='input_box_item'>
                                    진하게<input type='checkbox' name='b_category' id='b_category' ".("Y" == $b_category ? "checked":"").">
									기울기<input type='checkbox' name='i_category' id='i_category' ".("Y" == $i_category ? "checked":"").">
									밑줄<input type='checkbox' name='u_category' id='u_category' ".("Y" == $u_category ? "checked":"").">
									글자색 <input type='text' name='c_category' id='c_category' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_category ? "#000000":$c_category)."'>
                                </td>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>카테고리 설정 </b></td>
                                <td class='input_box_item'>
                                    <input type='radio' name='r_category' id='r_category_A' value='A' ".("A" == $r_category || "" == $r_category  ? "checked":"")."><label for='r_category_A' >테두리 직각</label>
                                    <input type='radio' name='r_category' id='r_category_R' value='R' ".("R" == $r_category ? "checked":"")."> <label for='r_category_R' >테두리 라운드</label>
                                    <input type='checkbox' name='e_category' id='e_category' ".("Y" == $e_category ? "checked":"").">진하게<br><br>
                                    배경색 <input type='text' name='ba_category' id='ba_category' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $ba_category ? "#000000":$ba_category)."'>
									테두리색 <input type='text' name='bo_category' id='bo_category' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $bo_category ? "#000000":$bo_category)."'>
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>전시 버전 </b></td>
                                <td class='input_box_item' colspan=3>
                                    <input type='radio' name='display_gubun' id='display_gubun_P' value='P' ".("P" == $display_gubun || "" == $display_gubun  ? "checked":"")."><label for='display_gubun_P' >PC</label>
                                    <input type='radio' name='display_gubun' id='display_gubun_M' value='M' ".("M" == $display_gubun ? "checked":"")."> <label for='display_gubun_M' >MOBILE</label>
                                    <span class=small>* MOBILE 버전 선택 시 본문 및 그룹내용은 MOBILE 내용만 표출 됩니다.</span>
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>사용 여부 </b></td>
                                <td class='input_box_item'>
                                    <input type='radio' name='display_use' id='display_use_Y' value='Y' ".("Y" == $display_use || "" == $display_use  ? "checked":"")."><label for='display_use_Y' >사용</label>
                                    <input type='radio' name='display_use' id='display_use_N' value='N' ".("N" == $display_use ? "checked":"")."> <label for='display_use_N' >미사용</label>
                                </td>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>전시 상태 </b></td>
                                <td class='input_box_item'>
                                    <input type='radio' name='display_state' id='display_state_D' value='D' ".("D" == $display_state || "" == $display_state  ? "checked":"")."><label for='display_state_D' >전시중</label>
                                    <input type='radio' name='display_state' id='display_state_W' value='W' ".("W" == $display_state ? "checked":"")."> <label for='display_state_W' >전시대기</label>
                                    <input type='radio' name='display_state' id='display_state_E' value='E' ".("E" == $display_state ? "checked":"")."> <label for='display_state_E' >종료</label>
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>전시 기간 </b></td>
                                <td class='input_box_item' colspan=3>
                                    ".search_date('display_start','display_end',$display_start,$display_end,'Y',' ' ,' validation=true title="전시 기간" ')."
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>전시 기간 노출 여부</b></td>
                                <td class='input_box_item' colspan=3>
                                    <input type='radio' name='display_date_use' id='display_date_use_Y' value='Y' ".("Y" == $display_date_use || "" == $display_date_use  ? "checked":"")."><label for='display_date_use_Y' >노출</label>
                                    <input type='radio' name='display_date_use' id='display_date_use_N' value='N' ".("N" == $display_date_use ? "checked":"")."> <label for='display_date_use_N' >미노출</label>
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>댓글 게시판 등록 </b></td>
                                <td class='input_box_item' colspan=3>
                                    <select name='comment_board_ix' id='comment_board_ix' style='width:250px;'>
                                        <option value=''>선택안함</option>";

                                        $sql = "SELECT cmt_ix, title FROM shop_comment
                                                WHERE comment_use = 'Y' AND comment_state = 'Y' AND comment_start <= '".time()."' AND comment_end >= '".time()."'
                                                ORDER BY title ASC
                                        ";

                                        $db->query($sql);

                                        for ($j = 0; $j < $db->total; $j++) {
                                            $db->fetch($j);
                                            $Contents .= "<option value='" . $db->dt[cmt_ix] . "' ".($db->dt[cmt_ix] == $comment_board_ix ? "selected":"")." >" . $db->dt[title] . "</option>";
                                        }
                                        $Contents .= "                                        
                                    </select>
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' style='text-align:center;' nowrap oncontextmenu='init2();return false;' colspan=4><b>기획전 본문 </b></td>
                            </tr>
                            <tr bgcolor='#F8F9FA'>
                                <td colspan=4>
                                    <table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
                                        <tr>
                                            <td height='30' colspan='3' style='padding:10px;'>
                                                <div>PC 노출</div>
                                                <textarea name='content_text_pc' id='content_text_pc' style='width:98%;height:1000px;display:block'>".$content_text_pc."</textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height='30' colspan='3' style='padding:10px;'>
                                                <div>MOBILE 노출</div>
                                                <textarea name='content_text_mo' id='content_text_mo' style='width:98%;height:1000px;display:block'>".$content_text_mo."</textarea>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' style='text-align:center;' nowrap oncontextmenu='init2();return false;' colspan=4><b>기획전 그룹 관리 </b></td>
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
                                    $gdb->query("SELECT * FROM shop_content_group_relation WHERE con_ix= '$con_ix' ORDER BY group_code ASC");

                                    if($gdb->total || true){
                                        $group_total = $gdb->total;

                                        for($i=0;($i < $gdb->total || $i == 0);$i++){

                                            $gdb->fetch($i);

                                            $cgr_ix = $gdb->dt[cgr_ix];
                                            if($gdb->dt[group_code]){
                                                $group_no = $gdb->dt[group_code];
                                                $group_display_start = date("Y-m-d H:i:s",$gdb->dt[group_display_start]);
                                                $group_display_end = date("Y-m-d H:i:s",$gdb->dt[group_display_end]);
                                            }else{
                                                $group_no = $i+1;
                                            }

                                            $Contents .= "
                                                <div id='group_info_area".$i."' data-id='group_info_area".$i."' class='group_info_area_wrapper' group_code='".$group_no."'>
                                                    <div class='group_info_header' style='position:relative;padding:10px 10px;width:100%;'><img src='/admin/images/dot_org.gif' > <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>상품그룹  (GROUP ".$group_no.")</b> <a onclick=\"add_table()\"><img src='../images/".$admininfo["language"]."/btn_goods_group_add.gif' border=0 align=absmiddle style='position:relative;top:-2px;cursor:pointer;'></a> ".($i == 0 ? "<!--삭제버튼-->":"<a onClick=\"del_table('group_info_area".$i."','".$group_no."');\"><img src='../images/".$admininfo["language"]."/btn_goods_group_del.gif' border=0 align=absmiddle style='position:relative;top:-2px;'></a>").
                                                        "<a href='javascript:void(0);' class='slide-up-down-link'>
                                                        <span class='plus'><img src='/admin/images/btn_group_close.png' alt='Plus'></span>
                                                        <span class='minus'><img src='/admin/images/btn_group_open.png' alt='Minus'></span>
                                                        </a>"."
                                                        <input type='hidden'  name='cgr_ix[" . $group_no . "]' value='" . $cgr_ix . "'>
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
                                                                    <tr height=28 id='tableTitleK_".$group_no."'><td>국문</td><td><textarea name='group_title[".$group_no."]' id='group_title_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[group_title]."</textarea></td></tr>
                                                                    <tr height=28 id='tableTitleE_".$group_no."'><td>영문</td><td><textarea name='group_title_en[".$group_no."]' id='group_title_en_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[group_title_en]."</textarea></td></tr>
                                                                </table>
                                                            </td>
                                                            <td class='input_box_title'> <b>그룹명 설정</b></td>
                                                            <td class='input_box_item'>
                                                                <input type='radio' name='s_group_title[".$group_no."]' id='s_group_title_L_".$group_no."' value='L' ".($gdb->dt[s_group_title] == "L" || $gdb->dt[s_group_title] == "" ? "checked":"")."><label for='s_group_title_L_".$group_no."'> 좌측정렬</label>
                                                                <input type='radio' name='s_group_title[".$group_no."]' id='s_group_title_C_".$group_no."' value='C' ".($gdb->dt[s_group_title] == "C" ? "checked":"")."><label for='s_group_title_C_".$group_no."'> 가운데정렬</label>
                                                                <input type='radio' name='s_group_title[".$group_no."]' id='s_group_title_R_".$group_no."' value='R' ".($gdb->dt[s_group_title] == "R" ? "checked":"")."><label for='s_group_title_R_".$group_no."'> 우측정렬</label><br><br>
                                                                진하게<input type='checkbox' name='b_group_title[".$group_no."]' id='b_group_title_".$group_no."' ".($gdb->dt[b_group_title] == "Y" ? "checked":"").">
                                                                기울기<input type='checkbox' name='i_group_title[".$group_no."]' id='i_group_title_".$group_no."' ".($gdb->dt[i_group_title] == "Y" ? "checked":"").">
                                                                밑줄<input type='checkbox' name='u_group_title[".$group_no."]' id='u_group_title_".$group_no."' ".($gdb->dt[u_group_title] == "Y" ? "checked":"").">
                                                                글자색 <input type='text' name='c_group_title[".$group_no."]' id='c_group_title_".$group_no."' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $gdb->dt[c_group_title] ? "#000000":$gdb->dt[c_group_title])."'>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class='input_box_title'> <b>그룹머리말</b></td>
                                                            <td class='input_box_item'>
                                                                <table border=0 width=100%>
									                                <col width=50px><col width=*>
                                                                    <tr height=28><td>국문</td><td><textarea name='group_preface[".$group_no."]' id='group_preface_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[group_preface]."</textarea></td></tr>
                                                                    <tr height=28><td>영문</td><td><textarea name='group_preface_en[".$group_no."]' id='group_preface_en_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[group_preface_en]."</textarea></td></tr>
                                                                </table>
                                                            </td>
                                                            <td class='input_box_title'> <b>그룹머리말 설정</b></td>
                                                            <td class='input_box_item'>
                                                                진하게<input type='checkbox' name='b_group_preface[".$group_no."]' id='b_group_preface_".$group_no."' ".($gdb->dt[b_group_preface] == "Y" ? "checked":"").">
                                                                기울기<input type='checkbox' name='i_group_preface[".$group_no."]' id='i_group_preface_".$group_no."' ".($gdb->dt[i_group_preface] == "Y" ? "checked":"").">
                                                                밑줄<input type='checkbox' name='u_group_preface[".$group_no."]' id='u_group_preface_".$group_no."' ".($gdb->dt[u_group_preface] == "Y" ? "checked":"").">
                                                                글자색 <input type='text' name='c_group_preface[".$group_no."]' id='c_group_preface_".$group_no."' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $gdb->dt[c_group_preface] ? "#000000":$gdb->dt[c_group_preface])."'>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class='input_box_title'> <b>그룹간단설명</b></td>
                                                            <td class='input_box_item'>
                                                                <table border=0 width=100%>
									                                <col width=50px><col width=*>
                                                                    <tr height=28><td>국문</td><td><textarea name='group_explanation[".$group_no."]' id='group_explanation_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[group_explanation]."</textarea></td></tr>
                                                                    <tr height=28><td>영문</td><td><textarea name='group_explanation_en[".$group_no."]' id='group_explanation_en_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[group_explanation_en]."</textarea></td></tr>
                                                                </table>
                                                            </td>
                                                            <td class='input_box_title'> <b>그룹간단설명 설정</b></td>
                                                            <td class='input_box_item'>
                                                                진하게<input type='checkbox' name='b_group_explanation[".$group_no."]' id='b_group_explanation_".$group_no."' ".($gdb->dt[b_group_explanation] == "Y" ? "checked":"").">
                                                                기울기<input type='checkbox' name='i_group_explanation[".$group_no."]' id='i_group_explanation_".$group_no."' ".($gdb->dt[i_group_explanation] == "Y" ? "checked":"").">
                                                                밑줄<input type='checkbox' name='u_group_explanation[".$group_no."]' id='u_group_explanation_".$group_no."' ".($gdb->dt[u_group_explanation] == "Y" ? "checked":"").">
                                                                글자색 <input type='text' name='c_group_explanation[".$group_no."]' id='c_group_explanation_".$group_no."' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $gdb->dt[c_group_explanation] ? "#000000":$gdb->dt[c_group_explanation])."'>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class='input_box_title' style='text-align:center;' colspan='4'> <b>그룹내용</b></td>
                                                        </tr>
                                                        <tr bgcolor='#F8F9FA'>
                                                            <td colspan=4>
                                                                <table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
                                                                    <tr>
                                                                        <td height='30' colspan='3' style='padding:10px;'>
                                                                            <div>PC 노출(TOP)</div>
                                                                            <textarea name='group_text_pc[".$group_no."]' id='group_text_pc_".$group_no."' style='width:98%;height:1000px;display:block'>".$gdb->dt[group_text_pc]."</textarea>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td height='30' colspan='3' style='padding:10px;'>
                                                                            <div>MOBILE 노출(TOP)</div>
                                                                            <textarea name='group_text_mo[".$group_no."]' id='group_text_mo_".$group_no."' style='width:98%;height:1000px;display:block'>".$gdb->dt[group_text_mo]."</textarea>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class='input_box_title'> <b>상품등록</b></td>
                                                            <td class='input_box_item' colspan='3'>
                                                                <div id='goods_manual_area_".$group_no."' style='display:block;' class='goods_manual_area'>
                                                                    <div class='filterBar'>
                                                                        <div class='searchBar'>
                                                                            <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,".$group_no.",'productList_".$group_no."');\">
                                                                                <img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class='products_area'>
                                                                        <div style='width:100%;padding:5px;' id='group_product_area_".$group_no."' >".relationGroupProductList($cgr_ix, $group_no, "clipart")."</div>
                                                                        <div style='clear:both;width:100%;'><span class=small>* 드래그앤 드롭으로 전시 순서를 변경할 수 있습니다.<br /><font style='color:red'>* 더블클릭 으로 등록된 이미지 개별 삭제 가능합니다.</font></span></div>
                                                                    </div>
                                                                </div>
                                                                <div style='padding:0px 0px;display:none;' id='goods_auto_area_".$group_no."'>
                                                                    <a href=\"javascript:PoPWindow3('category_select.php?mmode=pop&group_code=".$group_no."',660,300,'category_select')\"'><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
                                                                    <table border=0 cellpadding=0 cellspacing=0 width='100%' style='margin-top:5px;padding:5px 10px 5px 10px;border:1px solid silver' >
                                                                    <col width=100%>
                                                                        <tr>
                                                                            <td>";
                                                                if($id != ""){
                                                                                $Contents .= PrintRelation($id);
                                                                }else{
                                                                                $Contents .= "<table cellpadding=0 cellspacing=0 id='objCategory_".$group_no."' >
                                                                                    <col width=5>
                                                                                    <col width=30>
                                                                                    <col width=*>
                                                                                    <col width=100>
                                                                                </table>";
                                                                }
                                                                                $Contents .= "	
                                                                            </td>
                                                                        </tr>
                                                                    <tr><td>자동등록기능은 준비중입니다.</td></tr>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                          <td class='input_box_title'> <b>사용여부</b></td>
                                                          <td class='input_box_item' colspan='3'>
                                                              <input type='radio' name='group_use[".$group_no."]' id='group_use_".$group_no."_y' size=50 value='Y' ".($gdb->dt[group_use] == "Y" || $gdb->dt[group_use] == "" ? "checked":"")."><label for='group_use_".$group_no."_y'>사용</label>
                                                              <input type='radio' name='group_use[".$group_no."]' id='group_use_".$group_no."_n' size=50 value='N' ".($gdb->dt[group_use] == "N" ? "checked":"")."><label for='group_use_".$group_no."_n'>미사용</label>
                                                          </td>
                                                        </tr>
                                                        <tr>
                                                          <td class='input_box_title'> <b>전시기간</b></td>
                                                          <td class='input_box_item' colspan='3'>
                                                              ".search_date_arry('group_display_start','group_display_end',$group_display_start,$group_display_end,'Y',' ' ,' validation=true title="전시 기간" ',$group_no)."
                                                          </td>
                                                        </tr>
                                                    </table>
                                                    <Script Language='JavaScript'>
                                                        init_arry(".$group_no.");
                                                    </Script>";
                                          }
                                      }
                                  $Contents .= "</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan=3 align=right style='padding:10px;'>
                                                <table width=100%  border=0>
                                                    <col width= '*' >
                                                    <col width= '100' >
                                                    <col width= '100' >
                                                    <col width= '100' >
                                                    <tr>";
                                                        //if($mode == "Upd") {
                                                        $Contents .= "
                                                            <td align='left'>";
                                                                if($display_gubun == "P"){
                                                                    if($_SERVER['HTTP_HOST'] == "0925admintest.barrelmade.co.kr") {
                                                                        $Contents .= "<a href='https://qa.barrelmade.co.kr/content/focusNow2/".$con_ix."/preview' target='_blank' >";
                                                                    }else{
                                                                        $Contents .= "<a href='https://www.getbarrel.com/content/focusNow2/".$con_ix."/preview' target='_blank' >";
                                                                    }
                                                                }else{
                                                                    if($_SERVER['HTTP_HOST'] == "0925admintest.barrelmade.co.kr") {
                                                                        $Contents .= "<a href='https://qa.barrelmade.co.kr/content/focusNow4/".$con_ix."/preview' target='_blank' >";
                                                                    }else{
                                                                        $Contents .= "<a href='https://www.getbarrel.com/content/focusNow4/".$con_ix."/preview' target='_blank' >";
                                                                    }
                                                                }
                                                            $Contents .= "<img src='../images/".$admininfo["language"]."/btn_promotion_preview.gif' align=absmiddle>
                                                                </a>
                                                            </td> ";
                                                        //}
                                                        $Contents .= "
                                                        <td><input type='checkbox' name='delete_cache' id='delete_cache' value='Y'><label for='delete_cache'>캐쉬삭제하기</label></td>
                                                        <td><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle></td>
                                                        <td><a href='content_list.php'><img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle></a></td>
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
			</table>";
}else if($content_type == 2){	// 스타일큐레이션
$Contents .= "<table cellpadding=0 cellspacing=0 width=100% >
            <tr>
                <td width='100%' align='right' valign='top'>
                    <form name='thisContentform' method='post' onSubmit='return SubmitX(this)' enctype='multipart/form-data' action='../display/content.save.php' target='calcufrm' style='display:inline;'>
                    <input type='hidden' name='content_type' id='content_type' value='$content_type'>
                    <input type='hidden' name='mode' value='$mode'>
                    <input type='hidden' name='this_depth' value='$depth'>
                    <input type='hidden' name='cid' value='$cid'>
                    <input type='hidden' name='con_ix' value='$con_ix'>
                    <table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
                        <col width='12%'>
                        <col width='30%'>
                        <col width='12%'>
                        <col width='38%'>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류위치설정 </b></td>
                            <td class='input_box_item' nowrap colspan='3'>
                                <div >".getContentPathByAdmin($cid, $depth)."</div>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>프론트전시여부 </b></td>
                            <td class='input_box_item' nowrap colspan='3'>
                                <div >".GetDisplayDivision($mall_ix, "select")."</div>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>기획전 제목 </b><img src='".$required3_path."'></td>
                            <td class='input_box_item' nowrap>
                                <table border=0 width=100%>
                                <col width=50px><col width=*>
                                    <tr height=28><td>국문</td><td><textarea name='title' id='title' style='width:85%;height:15px'>".$title."</textarea></td></tr>
                                    <tr height=28><td>영문</td><td><textarea name='title_en' id='title_en' style='width:85%;height:15px'>".$title_en."</textarea></td></tr>
                                </table>
                            </td>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>기획전 제목 설정 </b></td>
                            <td class='input_box_item' nowrap>
                                <input type='radio' name='s_title' id='s_title_L' value='L' ".("L" == $s_title || "" == $s_title  ? "checked":"")."><label for='s_title_L'> 좌측정렬</label>
                                <input type='radio' name='s_title' id='s_title_C' value='C' ".("C" == $s_title ? "checked":"")."><label for='s_title_C'> 가운데정렬</label>
                                <input type='radio' name='s_title' id='s_title_R' value='R' ".("R" == $s_title ? "checked":"")."><label for='s_title_R'> 우측정렬</label><br><br>
                                진하게<input type='checkbox' name='b_title' id='b_title' ".("Y" == $b_title ? "checked":"").">
                                기울기<input type='checkbox' name='i_title' id='i_title' ".("Y" == $i_title ? "checked":"").">
                                밑줄<input type='checkbox' name='u_title' id='u_title' ".("Y" == $u_title ? "checked":"").">
                                글자색 <input type='text' name='c_title' id='c_title' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_title ? "#000000":$c_title)."'>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>기획전 머리말 </b></td>
                            <td class='input_box_item' nowrap>
                                <table border=0 width=100%>
                                <col width=50px><col width=*>
                                    <tr height=28><td>국문</td><td><textarea name='preface' id='preface' style='width:85%;height:15px'>".$preface."</textarea></td></tr>
                                    <tr height=28><td>영문</td><td><textarea name='preface_en' id='preface_en' style='width:85%;height:15px'>".$preface_en."</textarea></td></tr>
                                </table>
                            </td>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>기획전 머리말 설정 </b></td>
                            <td class='input_box_item' nowrap>
                                진하게<input type='checkbox' name='b_preface' id='b_preface' ".("Y" == $b_preface ? "checked":"").">
                                기울기<input type='checkbox' name='i_preface' id='i_preface' ".("Y" == $i_preface ? "checked":"").">
                                밑줄<input type='checkbox' name='u_preface' id='u_preface' ".("Y" == $u_preface ? "checked":"").">
                                글자색 <input type='text' name='c_preface' id='c_preface' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_preface ? "#000000":$c_preface)."'>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>컨텐츠 간단설명 </b></td>
                            <td class='input_box_item' nowrap>
                                <table border=0 width=100%>
                                <col width=50px><col width=*>
                                    <tr height=28><td>국문</td><td><textarea name='explanation' id='explanation' style='width:85%;height:15px'>".$explanation."</textarea></td></tr>
                                    <tr height=28><td>영문</td><td><textarea name='explanation_en' id='explanation_en' style='width:85%;height:15px'>".$explanation_en."</textarea></td></tr>
                                </table>
                            </td>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>기획전 머리말 설정 </b></td>
                            <td class='input_box_item' nowrap>
                                진하게<input type='checkbox' name='b_explanation' id='b_explanation' ".("Y" == $b_explanation ? "checked":"").">
                                기울기<input type='checkbox' name='i_explanation' id='i_explanation' ".("Y" == $i_explanation ? "checked":"").">
                                밑줄<input type='checkbox' name='u_explanation' id='u_explanation' ".("Y" == $u_explanation ? "checked":"").">
                                글자색 <input type='text' name='c_explanation' id='c_explanation' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_explanation ? "#000000":$c_explanation)."'>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>상세 이미지 </b></td>
                            <td class='input_box_item' colspan='3'>
                                <table border=0 style=width:100%;>
                                    <input type='hidden' id='imgCnt'>
                                    <col width='225px'>
                                    <col width='*'>
                                    <tr>
                                        <td><input type=file name='productImg' id='productImg' class='textbox' size=25 style='font-size:8pt'></td>
                                        <td><button type='button' onclick='imgAdd(this.form)'>이미지추가</button></td>
                                    </tr>
                                    <tr>
                                        <td colspan=2><input type='checkbox' name='imgInsYN' id='imgInsYN'>이미지등록요청(상품이미지 등록 및 추가시 반듯이 체크박스에 체크가 되어야 등록 됩니다.)</td>
                                    </tr>
                                    <tr>
                                        <td class='search_box_item' style='padding:10px 10px;' colspan=2>
                                            <div id='goods_manual_area_1'>
                                                <div style='width:100%;padding:5px;' id='group_product_area_1'>
                                                    <ui id='image_area'>".getImageUploadHtmlNew($con_ix, $content_type)."</ui>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan=2>※ 삭제 클릭시 이미지 복구가 불가능합니다. 삭제 클릭 후 반듯이 하단 저장 버튼을 누르시기 바랍니다.</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>스타일리스트 이미지(PC) </b></td>
                            <td class='input_box_item' colspan='3'>
                                <table border=0>
                                    <col width='505px'>
                                    <col width='100px'>
                                    <col width='*'>
                                    <tr>
                                        <td>
                                            <input type=file name='list_img' id='list_img' class='textbox' size=25 style='font-size:8pt'>";
                                            if($list_img != ""){
                                                $Contents .= "<input type='checkbox' name='list_img_del' id='list_img_del'>리스트이미지(PC) 삭제";
                                            }
                                            $Contents .= "
                                        </td>
                                        <td ".$img_view_style." rowspan=2>
                                            <a class='screenshot'  rel='".$_SESSION["admin_config"][mall_data_root]."/images/content/".$con_ix."/".$list_img."'><img src='../v3/images/btn/bt_preview.png'   style='cursor:pointer'></a>
                                        </td>
                                    </tr>
                                    <tr height=10><td colspan= class='small' style='padding-top:5px;'>※ 메인 페이지 추천 컨텐츠 등 3:4 비율 이미지</td></tr>
                                </table>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>스타일리스트 이미지(Mobile) </b></td>
                            <td class='input_box_item' colspan='3'>
                                <table border=0>
                                    <col width='505px'>
                                    <col width='100px'>
                                    <col width='*'>
                                    <tr>
                                        <td>
                                            <input type=file name='list_img_m' id='list_img_m' class='textbox' size=25 style='font-size:8pt'>";
                                            if($list_img_m != ""){
                                                $Contents .= "<input type='checkbox' name='list_img_m_del' id='list_img_m_del'>리스트이미지(Mobile) 삭제";
                                            }
                                            $Contents .= "
                                        </td>
                                        <td ".$img_view_style." rowspan=2>
                                            <a class='screenshot'  rel='".$_SESSION["admin_config"][mall_data_root]."/images/content/".$con_ix."/".$list_img_m."'><img src='../v3/images/btn/bt_preview.png'   style='cursor:pointer'></a>
                                        </td>
                                    </tr>
                                    <tr height=10><td colspan= class='small' style='padding-top:5px;'>※ 메인 페이지 추천 컨텐츠 등 1:1 비율 이미지</td></tr>
                                </table>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>사용 여부 </b></td>
                                <td class='input_box_item'>
                                    <input type='radio' name='display_use' id='display_use_Y' value='Y' ".("Y" == $display_use || "" == $display_use  ? "checked":"")."><label for='display_use_Y' >사용</label>
                                    <input type='radio' name='display_use' id='display_use_N' value='N' ".("N" == $display_use ? "checked":"")."> <label for='display_use_N' >미사용</label>
                                </td>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>전시 상태 </b></td>
                                <td class='input_box_item'>
                                    <input type='radio' name='display_state' id='display_state_D' value='D' ".("D" == $display_state || "" == $display_state  ? "checked":"")."><label for='display_state_D' >전시중</label>
                                    <input type='radio' name='display_state' id='display_state_W' value='W' ".("W" == $display_state ? "checked":"")."> <label for='display_state_W' >전시대기</label>
                                    <input type='radio' name='display_state' id='display_state_E' value='E' ".("E" == $display_state ? "checked":"")."> <label for='display_state_E' >종료</label>
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>전시 기간 </b></td>
                                <td class='input_box_item' colspan=3>
                                    ".search_date('display_start','display_end',$display_start,$display_end,'Y',' ' ,' validation=true title="전시 기간" ')."
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>전시 기간 노출 여부</b></td>
                                <td class='input_box_item' colspan=3>
                                    <input type='radio' name='display_date_use' id='display_date_use_Y' value='Y' ".("Y" == $display_date_use || "" == $display_date_use  ? "checked":"")."><label for='display_date_use_Y' >노출</label>
                                    <input type='radio' name='display_date_use' id='display_date_use_N' value='N' ".("N" == $display_date_use ? "checked":"")."> <label for='display_date_use_N' >미노출</label>
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' style='text-align:center;' nowrap oncontextmenu='init2();return false;' colspan=4><b>스타일 상품 그룹 관리 </b></td>
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
                                    $gdb->query("SELECT * FROM shop_content_group_relation WHERE con_ix= '$con_ix' ORDER BY group_code ASC");

                                    if($gdb->total || true){
                                        $group_total = $gdb->total;

                                        for($i=0;($i < $gdb->total || $i == 0);$i++){

                                            $gdb->fetch($i);

                                            $cgr_ix = $gdb->dt[cgr_ix];
                                            if($gdb->dt[group_code]){
                                                $group_no = $gdb->dt[group_code];
                                                $group_display_start = date("Y-m-d H:i:s",$gdb->dt[group_display_start]);
                                                $group_display_end = date("Y-m-d H:i:s",$gdb->dt[group_display_end]);
                                            }else{
                                                $group_no = $i+1;
                                            }

                                            $Contents .= "
                                                <div id='group_info_area".$i."' data-id='group_info_area".$i."' class='group_info_area_wrapper' group_code='".$group_no."'>
                                                    <div class='group_info_header' style='position:relative;padding:10px 10px;width:100%;'><img src='/admin/images/dot_org.gif' > <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>상품그룹  (GROUP ".$group_no.")</b> <a onclick=\"add_table_style()\"><img src='../images/".$admininfo["language"]."/btn_goods_group_add.gif' border=0 align=absmiddle style='position:relative;top:-2px;cursor:pointer;'></a> ".($i == 0 ? "<!--삭제버튼-->":"<a onClick=\"del_table('group_info_area".$i."','".$group_no."');\"><img src='../images/".$admininfo["language"]."/btn_goods_group_del.gif' border=0 align=absmiddle style='position:relative;top:-2px;'></a>").
                                                        "<a href='javascript:void(0);' class='slide-up-down-link'>
                                                        <span class='plus'><img src='/admin/images/btn_group_close.png' alt='Plus'></span>
                                                        <span class='minus'><img src='/admin/images/btn_group_open.png' alt='Minus'></span>
                                                        </a>"."
                                                        <input type='hidden'  name='cgr_ix[" . $group_no . "]' value='" . $cgr_ix . "'>
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
                                                                    <tr height=28 id='tableTitleK_".$group_no."'><td>국문</td><td><textarea name='group_title[".$group_no."]' id='group_title_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[group_title]."</textarea></td></tr>
                                                                    <tr height=28 id='tableTitleE_".$group_no."'><td>영문</td><td><textarea name='group_title_en[".$group_no."]' id='group_title_en_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[group_title_en]."</textarea></td></tr>
                                                                </table>
                                                            </td>
                                                            <td class='input_box_title'> <b>그룹명 설정</b></td>
                                                            <td class='input_box_item'>
                                                                <input type='radio' name='s_group_title[".$group_no."]' id='s_group_title_L_".$group_no."' value='L' ".($gdb->dt[s_group_title] == "L"  || $gdb->dt[s_group_title] == "" ? "checked":"")."><label for='s_group_title_L_".$group_no."'> 좌측정렬</label>
                                                                <input type='radio' name='s_group_title[".$group_no."]' id='s_group_title_C_".$group_no."' value='C' ".($gdb->dt[s_group_title] == "C" ? "checked":"")."><label for='s_group_title_C_".$group_no."'> 가운데정렬</label>
                                                                <input type='radio' name='s_group_title[".$group_no."]' id='s_group_title_R_".$group_no."' value='R' ".($gdb->dt[s_group_title] == "R" ? "checked":"")."><label for='s_group_title_R_".$group_no."'> 우측정렬</label><br><br>
                                                                진하게<input type='checkbox' name='b_group_title[".$group_no."]' id='b_group_title_".$group_no."' ".($gdb->dt[b_group_title] == "Y" ? "checked":"").">
                                                                기울기<input type='checkbox' name='i_group_title[".$group_no."]' id='i_group_title_".$group_no."' ".($gdb->dt[i_group_title] == "Y" ? "checked":"").">
                                                                밑줄<input type='checkbox' name='u_group_title[".$group_no."]' id='u_group_title_".$group_no."' ".($gdb->dt[u_group_title] == "Y" ? "checked":"").">
                                                                글자색 <input type='text' name='c_group_title[".$group_no."]' id='c_group_title_".$group_no."' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $gdb->dt[c_group_title] ? "#000000":$gdb->dt[c_group_title])."'>
                                                            </td>
                                                        </tr>                                                        
                                                        <tr>
                                                            <td class='input_box_title'> <b>스타일 등록</b></td>
                                                            <td class='input_box_item' colspan='3'>
                                                                <div id='goods_manual_area_".$group_no."' style='display:block;' class='goods_manual_area'>
                                                                    <div class='filterBar'>
                                                                        <div class='searchBar'>
                                                                            <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,".$group_no.",'productList_".$group_no."');\">
                                                                                <img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class='products_area'>
                                                                        <div style='width:100%;padding:5px;' id='group_product_area_".$group_no."' >".relationGroupProductList($cgr_ix, $group_no, "clipart")."</div>
                                                                        <div style='clear:both;width:100%;'><span class=small>* 드래그앤 드롭으로 전시 순서를 변경할 수 있습니다.<br /><font style='color:red'>* 더블클릭 으로 등록된 이미지 개별 삭제 가능합니다.</font></span></div>
                                                                    </div>
                                                                </div>
                                                                <div style='padding:0px 0px;display:none;' id='goods_auto_area_".$group_no."'>
                                                                    <a href=\"javascript:PoPWindow3('category_select.php?mmode=pop&group_code=".$group_no."',660,300,'category_select')\"'><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
                                                                    <table border=0 cellpadding=0 cellspacing=0 width='100%' style='margin-top:5px;padding:5px 10px 5px 10px;border:1px solid silver' >
                                                                    <col width=100%>
                                                                        <tr>
                                                                            <td>";
                                                                if($id != ""){
                                                                                $Contents .= PrintRelation($id);
                                                                }else{
                                                                                $Contents .= "<table cellpadding=0 cellspacing=0 id='objCategory_".$group_no."' >
                                                                                    <col width=5>
                                                                                    <col width=30>
                                                                                    <col width=*>
                                                                                    <col width=100>
                                                                                </table>";
                                                                }
                                                                                $Contents .= "	
                                                                            </td>
                                                                        </tr>
                                                                    <tr><td>자동등록기능은 준비중입니다.</td></tr>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                          <td class='input_box_title'> <b>사용여부</b></td>
                                                          <td class='input_box_item' colspan='3'>
                                                              <input type='radio' name='group_use[".$group_no."]' id='group_use_".$group_no."_y' size=50 value='Y' ".($gdb->dt[group_use] == "Y" || $gdb->dt[group_use] == "" ? "checked":"")."><label for='group_use_".$group_no."_y'>사용</label>
                                                              <input type='radio' name='group_use[".$group_no."]' id='group_use_".$group_no."_n' size=50 value='N' ".($gdb->dt[group_use] == "N" ? "checked":"")."><label for='group_use_".$group_no."_n'>미사용</label>
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
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan=3 align=right style='padding:10px;'>
                                                <table width=100%  border=0>
                                                    <col width= '*' >
                                                    <col width= '100' >
                                                    <col width= '100' >
                                                    <col width= '100' >
                                                    <tr>";
                                                    //if($mode == "Upd") {
                                                        if($_SERVER['HTTP_HOST'] == "0925admintest.barrelmade.co.kr") {
                                                            $Contents .= "<td align='left'><a href='https://qa.barrelmade.co.kr/content/styleDetail/".$con_ix."/".$cid."/preview' target='_blank' ><img src='../images/".$admininfo["language"]."/btn_promotion_preview.gif' align=absmiddle></a></td> ";
                                                        }else{
                                                            $Contents .= "<td align='left'><a href='https://www.getbarrel.com/content/styleDetail/".$con_ix."/".$cid."/preview' target='_blank' ><img src='../images/".$admininfo["language"]."/btn_promotion_preview.gif' align=absmiddle></a></td> ";
                                                        }
                                                    //}
                                                    $Contents .= "
                                                        <td><input type='checkbox' name='delete_cache' id='delete_cache' value='Y'><label for='delete_cache'>캐쉬삭제하기</label></td>
                                                        <td><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle></td>
                                                        <td><a href='content_list.php'><img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle></a></td>
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
			</table>";
}else if($content_type == 3){	// 팀배럴
    $Contents .= "<table cellpadding=0 cellspacing=0 width=100% >
            <tr>
                <td width='100%' align='right' valign='top'>
                    <form name='thisContentform' method='post' onSubmit='return SubmitX(this)' enctype='multipart/form-data' action='../display/content.save.php' target='calcufrm' style='display:inline;'>
                    <input type='hidden' name='content_type' id='content_type' value='$content_type'>
                    <input type='hidden' name='mode' value='$mode'>
                    <input type='hidden' name='this_depth' value='$depth'>
                    <input type='hidden' name='cid' value='$cid'>
                    <input type='hidden' name='con_ix' value='$con_ix'>
                    <table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
                        <col width='12%'>
                        <col width='30%'>
                        <col width='12%'>
                        <col width='38%'>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류위치설정 </b></td>
                            <td class='input_box_item' nowrap colspan='3'>
                                <div >".getContentPathByAdmin($cid, $depth)."</div>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>프론트전시여부 </b></td>
                            <td class='input_box_item' nowrap colspan='3'>
                                <div >".GetDisplayDivision($mall_ix, "select")."</div>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>프로필명(A) </b><img src='".$required3_path."'></td>
                            <td class='input_box_item' nowrap>
                                <textarea name='title' id='title' style='width:85%;height:15px'>".$title."</textarea>
                            </td>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>프로필명(A) 설정 </b></td>
                            <td class='input_box_item' nowrap>
                                <input type='radio' name='s_title' id='s_title_L' value='L' ".("L" == $s_title || "" == $s_title  ? "checked":"")."><label for='s_title_L'> 좌측정렬</label>
                                <input type='radio' name='s_title' id='s_title_C' value='C' ".("C" == $s_title ? "checked":"")."><label for='s_title_C'> 가운데정렬</label>
                                <input type='radio' name='s_title' id='s_title_R' value='R' ".("R" == $s_title ? "checked":"")."><label for='s_title_R'> 우측정렬</label><br><br>
                                진하게<input type='checkbox' name='b_title' id='b_title' ".("Y" == $b_title ? "checked":"").">
                                기울기<input type='checkbox' name='i_title' id='i_title' ".("Y" == $i_title ? "checked":"").">
                                밑줄<input type='checkbox' name='u_title' id='u_title' ".("Y" == $u_title ? "checked":"").">
                                글자색 <input type='text' name='c_title' id='c_title' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_title ? "#000000":$c_title)."'>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>프로필명(B) </b></td>
                            <td class='input_box_item' nowrap>
                                <textarea name='title_en' id='title_en' style='width:85%;height:15px'>".$title_en."</textarea>
                            </td>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>프로필명(B) 설정 </b></td>
                            <td class='input_box_item' nowrap>
                                진하게<input type='checkbox' name='b_title_b' id='b_title_b' ".("Y" == $b_title_b ? "checked":"").">
                                기울기<input type='checkbox' name='i_title_b' id='i_title_b' ".("Y" == $i_title_b ? "checked":"").">
                                밑줄<input type='checkbox' name='u_title_b' id='u_title_b' ".("Y" == $u_title_b ? "checked":"").">
                                글자색 <input type='text' name='c_title_b' id='c_title_b' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_title_b ? "#000000":$c_title_b)."'>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>프로필 머리말 </b></td>
                            <td class='input_box_item' nowrap>
                                <table border=0 width=100%>
                                <col width=50px><col width=*>
                                    <tr height=28><td>국문</td><td><textarea name='preface' id='preface' style='width:85%;height:15px'>".$preface."</textarea></td></tr>
                                    <tr height=28><td>영문</td><td><textarea name='preface_en' id='preface_en' style='width:85%;height:15px'>".$preface_en."</textarea></td></tr>
                                </table>
                            </td>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>프로필 설정 </b></td>
                            <td class='input_box_item' nowrap>
                                진하게<input type='checkbox' name='b_preface' id='b_preface' ".("Y" == $b_preface ? "checked":"").">
                                기울기<input type='checkbox' name='i_preface' id='i_preface' ".("Y" == $i_preface ? "checked":"").">
                                밑줄<input type='checkbox' name='u_preface' id='u_preface' ".("Y" == $u_preface ? "checked":"").">
                                글자색 <input type='text' name='c_preface' id='c_preface' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_preface ? "#000000":$c_preface)."'>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>프로필 간단설명 </b></td>
                            <td class='input_box_item' nowrap>
                                <table border=0 width=100%>
                                <col width=50px><col width=*>
                                    <tr height=28><td>국문</td><td><textarea name='explanation' id='explanation' style='width:85%;height:15px'>".$explanation."</textarea></td></tr>
                                    <tr height=28><td>영문</td><td><textarea name='explanation_en' id='explanation_en' style='width:85%;height:15px'>".$explanation_en."</textarea></td></tr>
                                </table>
                            </td>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>프로필 간단설명 설정 </b></td>
                            <td class='input_box_item' nowrap>
                                진하게<input type='checkbox' name='b_explanation' id='b_explanation' ".("Y" == $b_explanation ? "checked":"").">
                                기울기<input type='checkbox' name='i_explanation' id='i_explanation' ".("Y" == $i_explanation ? "checked":"").">
                                밑줄<input type='checkbox' name='u_explanation' id='u_explanation' ".("Y" == $u_explanation ? "checked":"").">
                                글자색 <input type='text' name='c_explanation' id='c_explanation' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_explanation ? "#000000":$c_explanation)."'>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>선수 프로필 </b></td>
                            <td class='input_box_item' nowrap>
                                <table border=0 width=100%>
                                <col width=50px><col width=*>
                                    <tr height=28><td>국문</td><td><textarea name='player_profile' id='player_profile' style='width:85%;height:15px'>".$player_profile."</textarea></td></tr>
                                    <tr height=28><td>영문</td><td><textarea name='player_profile_en' id='player_profile_en' style='width:85%;height:15px'>".$player_profile_en."</textarea></td></tr>
                                </table>
                            </td>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>선수 프로필 설정 </b></td>
                            <td class='input_box_item' nowrap>
                                진하게<input type='checkbox' name='b_profile' id='b_profile' ".("Y" == $b_profile ? "checked":"").">
                                기울기<input type='checkbox' name='i_profile' id='i_profile' ".("Y" == $i_profile ? "checked":"").">
                                밑줄<input type='checkbox' name='u_profile' id='u_profile' ".("Y" == $u_profile ? "checked":"").">
                                글자색 <input type='text' name='c_profile' id='c_profile' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_profile ? "#000000":$c_profile)."'>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>선수 코멘트 </b></td>
                            <td class='input_box_item' nowrap>
                                <table border=0 width=100%>
                                <col width=50px><col width=*>
                                    <tr height=28><td>국문</td><td><textarea name='player_comment' id='player_comment' style='width:85%;height:15px'>".$player_comment."</textarea></td></tr>
                                    <tr height=28><td>영문</td><td><textarea name='player_comment_en' id='player_comment_en' style='width:85%;height:15px'>".$player_comment_en."</textarea></td></tr>
                                </table>
                            </td>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>선수 코멘트 설정 </b></td>
                            <td class='input_box_item' nowrap>
                                진하게<input type='checkbox' name='b_comment' id='b_comment' ".("Y" == $b_comment ? "checked":"").">
                                기울기<input type='checkbox' name='i_comment' id='i_comment' ".("Y" == $i_comment ? "checked":"").">
                                밑줄<input type='checkbox' name='u_comment' id='u_comment' ".("Y" == $u_comment ? "checked":"").">
                                글자색 <input type='text' name='c_comment' id='c_comment' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_comment ? "#000000":$c_comment)."'>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'>
                                <b>종목 설정 </b>
                                <input type='button' value='설정' onclick=\"PoPWindow('./player_subject_list.php?mmode=pop',500,500,'color_code')\" />
                            </td>
                            <td class='input_box_item' nowrap colspan='3'>";
                            $slave_db->query("SELECT idx, subject FROM shop_content_player_subject WHERE disp = 'Y' ");
                            $palyerSubject = $slave_db->fetchall();

                            for($s=0; $s < count($palyerSubject);$s++){
                                $subjectIdx = $palyerSubject[$s]['idx'];
                                $checked = "";
                                foreach($player_subject as $key => $val){
                                    if($subjectIdx == $key){
                                        $checked = "checked";
                                        break;
                                    }
                                }
                                $Contents .= $palyerSubject[$s]['subject']." <input type='checkbox' name='player_subject[$subjectIdx]' id='player_subject[$subjectIdx]' $checked>";
                            }

                                /*서핑<input type='checkbox' name='player_subject_surf' id='player_subject_surf' ".("Y" == $player_subject->surf ? "checked":"").">
                                수영<input type='checkbox' name='player_subject_swim' id='player_subject_swim' ".("Y" == $player_subject->swim ? "checked":"").">
                                프리다이빙<input type='checkbox' name='player_subject_free' id='player_subject_free' ".("Y" == $player_subject->free ? "checked":"").">
                                스쿠버다이빙<input type='checkbox' name='player_subject_scuba' id='player_subject_scuba' ".("Y" == $player_subject->scuba ? "checked":"").">
                                요가<input type='checkbox' name='player_subject_yoga' id='player_subject_yoga' ".("Y" == $player_subject->yoga ? "checked":"").">
                                필라테스<input type='checkbox' name='player_subject_pila' id='player_subject_pila' ".("Y" == $player_subject->pila ? "checked":"").">*/
                        $Contents .= "
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>SNS 연동(인스타그램) </b></td>
                            <td class='input_box_item' nowrap colspan='3'>
                                <table width='100%' id='instar_area'>
                                    <col width='600px' />
                                    <col width='*' />";
                            if($player_instar){
                                for($i = 0;$i < count($player_instar);$i++){
                                $Contents .= "
                                    <tr id='add_table'>
                                        <input type='hidden' name='instar_seq[]' id='option_length' value='$i'>       
                                        <td>
                                            <input type='text' name='player_instar[$i]' id='player_instar' style='width:500px;' value='".$player_instar[$i]."'>
                                        </td>
                                        <td>
                                            <input type='button' id='instar_add' value='추가' title='추가' style='cursor:pointer;' onclick=\"AddCopyRow('instar_area','player_instar')\">
                                            <input type='button' id='instar_del' value='삭제' title='삭제' style='cursor:pointer;' >
                                        </td>
                                    </tr>";
                                }
                            }else{
                                $Contents .= "
                                    <tr id='add_table'>
                                        <input type='hidden' name='instar_seq[]' id='option_length' value='0'>       
                                        <td>
                                            <input type='text' name='player_instar[0]' id='player_instar' style='width:500px;'>
                                        </td>
                                        <td>
                                            <input type='button' id='instar_add' value='추가' title='추가' style='cursor:pointer;' onclick=\"AddCopyRow('instar_area','player_instar')\">
                                            <input type='button' id='instar_del' value='삭제' title='삭제' style='cursor:pointer;' >
                                        </td>
                                    </tr>";
                            }
                                $Contents .= "
                                </table>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>SNS 연동(유튜브) </b></td>
                            <td class='input_box_item' nowrap colspan='3'>
                                <table width='100%' id='youtube_area'>
                                    <col width='600px' />
                                    <col width='*' />";
                            if($player_instar){
                                for($i = 0;$i < count($player_instar);$i++){
                                $Contents .= "
                                    <tr id='add_table'>
                                        <input type='hidden' name='youtube_seq[]' id='option_length' value='$i'>       
                                        <td>
                                            <input type='text' name='player_youtube[$i]' id='player_youtube' style='width:500px;' value='".$player_youtube[$i]."'>
                                        </td>
                                        <td>
                                            <input type='button' id='youtube_add' value='추가' title='추가' style='cursor:pointer;' onclick=\"AddCopyRow('youtube_area','player_youtube')\">
                                            <input type='button' id='youtube_del' value='삭제' title='삭제' style='cursor:pointer;' >
                                        </td>
                                    </tr>";
                                }
                            }else{
                                $Contents .= "
                                    <tr id='add_table'>
                                        <input type='hidden' name='youtube_seq[]' id='option_length' value='0'>       
                                        <td>
                                            <input type='text' name='player_youtube[0]' id='player_youtube' style='width:500px;'>
                                        </td>
                                        <td>
                                            <input type='button' id='youtube_add' value='추가' title='추가' style='cursor:pointer;' onclick=\"AddCopyRow('youtube_area','player_youtube')\">
                                            <input type='button' id='youtube_del' value='삭제' title='삭제' style='cursor:pointer;' >
                                        </td>
                                    </tr>";
                            }
                                $Contents .= "
                                </table>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>리스트 선수 이미지<font size='font-size:small'>(1:1)</font> </b></td>
                            <td class='input_box_item' colspan='3'>
                                <table border=0>
                                    <col width='505px'>
                                    <col width='100px'>
                                    <col width='*'>
                                    <tr>
                                        <td>
                                            <input type=file name='list_img' id='list_img' class='textbox' size=25 style='font-size:8pt'>
                                            <a href='javascript:' onclick=\"del('shop_logo','".$admin_config."');\"><img src='../images/korean/btn_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:20px;' title='삭제'></a></div>
                                        </td>
                                        <td ".$img_view_style." rowspan=2>
                                            <a class='screenshot'  rel='".$_SESSION["admin_config"][mall_data_root]."/images/content/".$con_ix."/".$list_img."'><img src='../v3/images/btn/bt_preview.png'   style='cursor:pointer'></a>
                                        </td>
                                    </tr>
                                    <tr height=10><td colspan= class='small' style='padding-top:5px;'>※ 프로필 리스트 1:1 비율 이미지</td></tr>
                                </table>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>상세 선수 이미지<font size='font-size:small'>(4:5)</font> </b></td>
                            <td class='input_box_item' colspan='3'>
                                <table border=0>
                                    <col width='505px'>
                                    <col width='100px'>
                                    <col width='*'>
                                    <tr>
                                        <td>
                                            <input type=file name='recommend_img' id='recommend_img' class='textbox' size=25 style='font-size:8pt'>
                                            <a href='javascript:' onclick=\"del('shop_logo','".$admin_config."');\"><img src='../images/korean/btn_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:20px;' title='삭제'></a></div>
                                        </td>
                                        <td ".$img_view_style." rowspan=2>
                                            <a class='screenshot'  rel='".$_SESSION["admin_config"][mall_data_root]."/images/content/".$con_ix."/".$recommend_img."'><img src='../v3/images/btn/bt_preview.png'   style='cursor:pointer'></a>
                                        </td>
                                    </tr>
                                    <tr height=10><td colspan= class='small' style='padding-top:5px;'>※ 프로필 상세페이지 선수 4:5 비율 이미지</td></tr>
                                </table>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>상세 이미지 </b></td>
                            <td class='input_box_item' colspan='3'>
                                <table border=0 style=width:100%;>
                                    <input type='hidden' id='imgCnt'>
                                    <col width='225px'>
                                    <col width='*'>
                                    <tr>
                                        <td><input type=file name='productImg' id='productImg' class='textbox' size=25 style='font-size:8pt'></td>
                                        <td><button type='button' onclick='imgAdd(this.form)'>이미지추가</button></td>
                                    </tr>
                                    <tr>
                                        <td colspan=2><input type='checkbox' name='imgInsYN' id='imgInsYN'>이미지등록요청(상품이미지 등록 및 추가시 반듯이 체크박스에 체크가 되어야 등록 됩니다.)</td>
                                    </tr>
                                    <tr>
                                        <td class='search_box_item' style='padding:10px 10px;' colspan=2>
                                            <div id='goods_manual_area_1'>
                                                <div style='width:100%;padding:5px;' id='group_product_area_1'>
                                                    <ui id='image_area'>".getImageUploadHtmlNew($con_ix, $content_type)."</ui>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan=2>※ 삭제 클릭시 이미지 복구가 불가능합니다. 삭제 클릭 후 반듯이 하단 저장 버튼을 누르시기 바랍니다.</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>상품분류별 연관컨턴츠 </b></td>
                            <td class='input_box_item' colspan='3'>
                                <input type='hidden' name='selected_cid' value=''>
                                <input type='hidden' name='selected_depth' value=''>
                                <input type='hidden' id='_category'>
                                <table border=0 cellpadding=0 cellspacing=0>
                                    <tr>
                                        <td style='padding-right:5px;'>" . getCategoryList3("대분류", "cid0", " class='cid' onChange=\"loadCategory($(this),'cid1',2)\" title='대분류' ", 0, $cid, "cid0") . " </td>
                                        <td style='padding-right:5px;'>" . getCategoryList3("중분류", "cid1", " class='cid' onChange=\"loadCategory($(this),'cid2',2)\" title='중분류'", 1, $cid, "cid1") . " </td>
                                        <td style='padding-right:5px;'>" . getCategoryList3("소분류", "cid2", " class='cid' onChange=\"loadCategory($(this),'cid3',2)\" title='소분류'", 2, $cid, "cid2") . " </td>
                                        <td>" . getCategoryList3("세분류", "cid3", " class='cid' onChange=\"loadCategory($(this),'cid_1',2)\" title='세분류'", 3, $cid, "cid3") . "</td>
                                        <td style='padding-left:10px'><img src='../images/" . $admininfo["language"] . "/btn_add.gif' align=absmiddle border=0 onclick=\"categoryadd()\"></td>
                                    </tr>
                                </table>
                                <table width=90% cellpadding=0 cellspacing=0 border=0 id=objCategory style='margin-top:5px;'>
                                    <col width=1>
                                    <col width=10>
                                    <col width=545>
                                    <col width=*>";
                                if ($con_ix != "") {
                                    $sql = "SELECT cr.cid, cr.cr_ix FROM shop_content_relation cr, shop_category_info c
                                                                            WHERE c.cid = cr.cid AND con_ix = '" . $con_ix . "'
                                                                            ORDER BY cr.cr_ix ASC
                                                                    ";
                                }
                                $db->query($sql);

                                for ($j = 0; $j < $db->total; $j++) {
                                    $db->fetch($j);
                                    $Contents .= "<tr height=23><td><input type=text name=category[] id='_category' value='" . $db->dt[cid] . "' style='display:none'></td><td></td><td>" . getCategoryPathByAdmin($db->dt[cid], 4) . "</td><td align=right style='padding:5px 25px 5px 5px;'><a href='javascript:void(0)' onClick='category_del(this.parentNode.parentNode)'><img src='../images/" . $_SESSION["admininfo"]["language"] . "/btc_del.gif' border=0></a></td></tr>";
                                }
                                $Contents .= "
                                </table>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>상품별 연관컨텐츠 </b></td>
                            <td class='search_box_item' style='padding:10px 10px;' colspan=3><a name='goods_display_type_9999'></a>
                                <div id='goods_manual_area_9999' style='display:block;'>
                                    <div style='width:100%;padding:5px;' id='group_product_area_9999' >".relationProductList($con_ix)."</div>
                                    <div style='width:100%;float:left;'>
                                        <span class=small>* 드래그앤 드롭으로 전시 순서를 변경할 수 있습니다.<br /><font style='color:red'>* 더블클릭 으로 등록된 이미지 개별 삭제 가능합니다.</font></span>
                                    </div>
                                    <div style='display:block;float:left;margin-top:10px;'>
                                        <a href=\"#goods_display_type_9999\" id='btn_goods_search_add' onclick=\"ms_productSearch.show_productSearchBox(event,9999,'productList_9999','clipart','77');\"><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>사용 여부 </b></td>
                            <td class='input_box_item'>
                                <input type='radio' name='display_use' id='display_use_Y' value='Y' ".("Y" == $display_use || "" == $display_use  ? "checked":"")."><label for='display_use_Y' >사용</label>
                                <input type='radio' name='display_use' id='display_use_N' value='N' ".("N" == $display_use ? "checked":"")."> <label for='display_use_N' >미사용</label>
                            </td>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>전시 상태 </b></td>
                            <td class='input_box_item'>
                                <input type='radio' name='display_state' id='display_state_D' value='D' ".("D" == $display_state || "" == $display_state  ? "checked":"")."><label for='display_state_D' >전시중</label>
                                <input type='radio' name='display_state' id='display_state_W' value='W' ".("W" == $display_state ? "checked":"")."> <label for='display_state_W' >전시대기</label>
                                <input type='radio' name='display_state' id='display_state_E' value='E' ".("E" == $display_state ? "checked":"")."> <label for='display_state_E' >종료</label>
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>전시 기간 </b></td>
                            <td class='input_box_item' colspan=3>
                                ".search_date('display_start','display_end',$display_start,$display_end,'Y',' ' ,' validation=true title="전시 기간" ')."
                            </td>
                        </tr>
                        <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>전시 기간 노출 여부</b></td>
                                <td class='input_box_item' colspan=3>
                                    <input type='radio' name='display_date_use' id='display_date_use_Y' value='Y' ".("Y" == $display_date_use || "" == $display_date_use  ? "checked":"")."><label for='display_date_use_Y' >노출</label>
                                    <input type='radio' name='display_date_use' id='display_date_use_N' value='N' ".("N" == $display_date_use ? "checked":"")."> <label for='display_date_use_N' >미노출</label>
                                </td>
                            </tr>
                        <tr bgcolor=#ffffff>
                            <td class='input_box_title' style='text-align:center;' nowrap oncontextmenu='init2();return false;' colspan=4><b>프로필 그룹 관리 </b></td>
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
                                $gdb->query("SELECT * FROM shop_content_group_relation WHERE con_ix= '$con_ix' ORDER BY group_code ASC");

                                if($gdb->total || true){
                                    $group_total = $gdb->total;

                                    for($i=0;($i < $gdb->total || $i == 0);$i++){

                                        $gdb->fetch($i);

                                        $cgr_ix = $gdb->dt[cgr_ix];
                                        if($gdb->dt[group_code]){
                                            $group_no = $gdb->dt[group_code];
                                            $group_display_start = date("Y-m-d H:i:s",$gdb->dt[group_display_start]);
                                            $group_display_end = date("Y-m-d H:i:s",$gdb->dt[group_display_end]);
                                        }else{
                                            $group_no = $i+1;
                                        }

                                        $Contents .= "
                                            <div id='group_info_area".$i."' data-id='group_info_area".$i."' class='group_info_area_wrapper' group_code='".$group_no."'>
                                                <div class='group_info_header' style='position:relative;padding:10px 10px;width:100%;'><img src='/admin/images/dot_org.gif' > <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>상품그룹  (GROUP ".$group_no.")</b> <a onclick=\"add_table_player()\"><img src='../images/".$admininfo["language"]."/btn_goods_group_add.gif' border=0 align=absmiddle style='position:relative;top:-2px;cursor:pointer;'></a> ".($i == 0 ? "<!--삭제버튼-->":"<a onClick=\"del_table('group_info_area".$i."','".$group_no."');\"><img src='../images/".$admininfo["language"]."/btn_goods_group_del.gif' border=0 align=absmiddle style='position:relative;top:-2px;'></a>").
                                                    "<a href='javascript:void(0);' class='slide-up-down-link'>
                                                    <span class='plus'><img src='/admin/images/btn_group_close.png' alt='Plus'></span>
                                                    <span class='minus'><img src='/admin/images/btn_group_open.png' alt='Minus'></span>
                                                    </a>"."
                                                    <input type='hidden'  name='cgr_ix[" . $group_no . "]' value='" . $cgr_ix . "'>
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
                                                                <tr height=28 id='tableTitleK_".$group_no."'><td>국문</td><td><textarea name='group_title[".$group_no."]' id='group_title_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[group_title]."</textarea></td></tr>
                                                                <tr height=28 id='tableTitleE_".$group_no."'><td>영문</td><td><textarea name='group_title_en[".$group_no."]' id='group_title_en_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[group_title_en]."</textarea></td></tr>
                                                            </table>
                                                        </td>
                                                        <td class='input_box_title'> <b>그룹명 설정</b></td>
                                                        <td class='input_box_item'>
                                                            <input type='radio' name='s_group_title[".$group_no."]' id='s_group_title_L_".$group_no."' value='L' ".($gdb->dt[s_group_title] == "L"  || $gdb->dt[s_group_title] == "" ? "checked":"")."><label for='s_group_title_L_".$group_no."'> 좌측정렬</label>
                                                            <input type='radio' name='s_group_title[".$group_no."]' id='s_group_title_C_".$group_no."' value='C' ".($gdb->dt[s_group_title] == "C" ? "checked":"")."><label for='s_group_title_C_".$group_no."'> 가운데정렬</label>
                                                            <input type='radio' name='s_group_title[".$group_no."]' id='s_group_title_R_".$group_no."' value='R' ".($gdb->dt[s_group_title] == "R" ? "checked":"")."><label for='s_group_title_R_".$group_no."'> 우측정렬</label><br><br>
                                                            진하게<input type='checkbox' name='b_group_title[".$group_no."]' id='b_group_title_".$group_no."' ".($gdb->dt[b_group_title] == "Y" ? "checked":"").">
                                                            기울기<input type='checkbox' name='i_group_title[".$group_no."]' id='i_group_title_".$group_no."' ".($gdb->dt[i_group_title] == "Y" ? "checked":"").">
                                                            밑줄<input type='checkbox' name='u_group_title[".$group_no."]' id='u_group_title_".$group_no."' ".($gdb->dt[u_group_title] == "Y" ? "checked":"").">
                                                            글자색 <input type='text' name='c_group_title[".$group_no."]' id='c_group_title_".$group_no."' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $gdb->dt[c_group_title] ? "#000000":$gdb->dt[c_group_title])."'>
                                                        </td>
                                                    </tr>         
                                                    <tr>
                                                        <td class='input_box_title'> <b>그룹간단설명</b></td>
                                                        <td class='input_box_item'>
                                                            <table border=0 width=100%>
                                                                <col width=50px><col width=*>
                                                                <tr height=28><td>국문</td><td><textarea name='group_explanation[".$group_no."]' id='group_explanation_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[group_explanation]."</textarea></td></tr>
                                                                <tr height=28><td>영문</td><td><textarea name='group_explanation_en[".$group_no."]' id='group_explanation_en_".$group_no."' style='width:85%;height:15px'>".$gdb->dt[group_explanation_en]."</textarea></td></tr>
                                                            </table>
                                                        </td>
                                                        <td class='input_box_title'> <b>그룹간단설명 설정</b></td>
                                                        <td class='input_box_item'>
                                                            진하게<input type='checkbox' name='b_group_explanation[".$group_no."]' id='b_group_explanation_".$group_no."' ".($gdb->dt[b_group_explanation] == "Y" ? "checked":"").">
                                                            기울기<input type='checkbox' name='i_group_explanation[".$group_no."]' id='i_group_explanation_".$group_no."' ".($gdb->dt[i_group_explanation] == "Y" ? "checked":"").">
                                                            밑줄<input type='checkbox' name='u_group_explanation[".$group_no."]' id='u_group_explanation_".$group_no."' ".($gdb->dt[u_group_explanation] == "Y" ? "checked":"").">
                                                            글자색 <input type='text' name='c_group_explanation[".$group_no."]' id='c_group_explanation_".$group_no."' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $gdb->dt[c_group_explanation] ? "#000000":$gdb->dt[c_group_explanation])."'>
                                                        </td>
                                                    </tr>       
                                                    <tr>
                                                        <td class='input_box_title'> <b>컨텐츠 등록</b></td>
                                                        <td class='input_box_item' colspan='3'>
                                                            <table border=0 style=width:100%;>
                                                            <col width='225px'>
                                                            <col width='*'>
                                                                <tr>
                                                                    <td class='search_box_item' style='padding:10px 10px;' colspan=2>
                                                                        <div id='goods_manual_area_1'>
                                                                            <div style='width:100%;padding:5px;' id='group_product_area_1'>
                                                                                <ui id='choiceGorupContent_".$group_no."'>".relationGroupContentList($cgr_ix, $group_no, "clipart")."</ui>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class='search_box_item' style='padding:5px 5px;' colspan=2>
                                                                        <button type='button' onclick='callGroupConetne(3, ".$group_no.");'>추천컨텐츠 불러오기</button>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class='input_box_title'> <b>상품 등록</b></td>
                                                        <td class='input_box_item' colspan='3'>
                                                            <div id='goods_manual_area_".$group_no."' style='display:block;' class='goods_manual_area'>
                                                                <div class='filterBar'>
                                                                    <div class='searchBar'>
                                                                        <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,".$group_no.",'productList_".$group_no."');\">
                                                                            <img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle>
                                                                        </a>
                                                                        <!--input type='text' class='textbox' name='search_goods' id='search_goods' size='20' value='' onkeyup=\"SearchGoods($(this), '".$group_no."')\"> 
                                                                        <img type='image' src='../images/korean/btn_search.gif' style='cursor:pointer;' onclick=\"SearchGoods($(this), '".$group_no."')\" align='absmiddle'> 
                                                                        <img src='../images/".$admininfo["language"]."/btc_del.gif' onclick=\"SearchGoodsDelete($(this))\" border='0'  style='cursor:pointer;vertical-align:middle;'-->
                                                                    </div>
                                                                </div>
                                                                <div class='products_area'>
                                                                    <div style='width:100%;padding:5px;' id='group_product_area_".$group_no."' >".relationGroupProductList($cgr_ix, $group_no, "clipart")."</div>
                                                                    <div style='clear:both;width:100%;'><span class=small>* 드래그앤 드롭으로 전시 순서를 변경할 수 있습니다.<br /><font style='color:red'>* 더블클릭 으로 등록된 이미지 개별 삭제 가능합니다.</font></span></div>
                                                                </div>
                                                            </div>
                                                            <div style='padding:0px 0px;display:none;' id='goods_auto_area_".$group_no."'>
                                                                <a href=\"javascript:PoPWindow3('category_select.php?mmode=pop&group_code=".$group_no."',660,300,'category_select')\"'><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
                                                                <table border=0 cellpadding=0 cellspacing=0 width='100%' style='margin-top:5px;padding:5px 10px 5px 10px;border:1px solid silver' >
                                                                <col width=100%>
                                                                    <tr>
                                                                        <td>";
                                                                        if($id != ""){
                                                                            $Contents .= PrintRelation($id);
                                                                        }else{
                                                                            $Contents .= "<table cellpadding=0 cellspacing=0 id='objCategory_".$group_no."' >
                                                                                    <col width=5>
                                                                                    <col width=30>
                                                                                    <col width=*>
                                                                                    <col width=100>
                                                                                </table>";
                                                                        }
                                                                        $Contents .= "	
                                                                        </td>
                                                                    </tr>
                                                                <tr><td>자동등록기능은 준비중입니다.</td></tr>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                      <td class='input_box_title'> <b>사용여부</b></td>
                                                      <td class='input_box_item' colspan='3'>
                                                          <input type='radio' name='group_use[".$group_no."]' id='group_use_".$group_no."_y' size=50 value='Y' ".($gdb->dt[group_use] == "Y" || $gdb->dt[group_use] == "" ? "checked":"")."><label for='group_use_".$group_no."_y'>사용</label>
                                                          <input type='radio' name='group_use[".$group_no."]' id='group_use_".$group_no."_n' size=50 value='N' ".($gdb->dt[group_use] == "N" ? "checked":"")."><label for='group_use_".$group_no."_n'>미사용</label>
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
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan=3 align=right style='padding:10px;'>
                                            <table width=100%  border=0>
                                                <col width= '*' >
                                                <col width= '100' >
                                                <col width= '100' >
                                                <col width= '100' >
                                                <tr>";
                                                //if($mode == "Upd") {
                                                if($_SERVER['HTTP_HOST'] == "0925admintest.barrelmade.co.kr") {
                                                    $Contents .= "<td align='left'><a href='https://qa.barrelmade.co.kr/content/teamDetail/".$con_ix."' target='_blank' ><img src='../images/".$admininfo["language"]."/btn_promotion_preview.gif' align=absmiddle></a></td> ";
                                                }else{
                                                    $Contents .= "<td align='left'><a href='https://www.getbarrel.com/content/teamDetail/".$con_ix."' target='_blank' ><img src='../images/".$admininfo["language"]."/btn_promotion_preview.gif' align=absmiddle></a></td> ";
                                                }
                                                //}
                                                $Contents .= "
                                                    <td><input type='checkbox' name='delete_cache' id='delete_cache' value='Y'><label for='delete_cache'>캐쉬삭제하기</label></td>
                                                    <td><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle></td>
                                                    <td><a href='content_list.php'><img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle></a></td>
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
        </table>";
}else if($content_type == 4){	// 리뷰
$Contents .= "
            <table cellpadding=0 cellspacing=0 width=100% >
                <tr>
                    <td width='100%' align='right' valign='top'>
                        <form name='thisContentform' method='post' onSubmit='return SubmitX(this)' action='../display/content.save.php' target='calcufrm' style='display:inline;'>
                        <input type='hidden' name='content_type' id='content_type' value='$content_type'>
                        <input type='hidden' name='mode' value='$mode'>
                        <input type='hidden' name='this_depth' value='$depth'>
                        <input type='hidden' name='cid' value='$cid'>
                        <input type='hidden' name='con_ix' value='$con_ix'>
                        <table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
                            <col width='12%'>
                            <col width='30%'>
                            <col width='12%'>
                            <col width='38%'>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류위치설정 </b></td>
                                <td class='input_box_item' nowrap colspan='3'>
                                    <div >".getContentPathByAdmin($cid, $depth)."</div>
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>프론트전시여부 </b></td>
                                <td class='input_box_item' nowrap colspan='3'>
                                    <div >".GetDisplayDivision($mall_ix, "select")."</div>
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>리뷰제목 </b><img src='".$required3_path."'></td>
                                <td class='input_box_item' nowrap colspan='3'>
                                    <input type='text' name='title' id='title' style='width:550px;' value='".$title."'>
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>리뷰간단설명 </b></td>
                                <td class='input_box_item' nowrap colspan='3'>
                                    <input type='text' name='explanation' id='explanation' style='width:550px;' value='".$explanation."'>
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>사용 여부 </b></td>
                                <td class='input_box_item'>
                                    <input type='radio' name='display_use' id='display_use_Y' value='Y' ".("Y" == $display_use || "" == $display_use  ? "checked":"")."><label for='display_use_Y' >사용</label>
                                    <input type='radio' name='display_use' id='display_use_N' value='N' ".("N" == $display_use ? "checked":"")."> <label for='display_use_N' >미사용</label>
                                </td>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>전시 상태 </b></td>
                                <td class='input_box_item'>
                                    <input type='radio' name='display_state' id='display_state_D' value='D' ".("D" == $display_state || "" == $display_state  ? "checked":"")."><label for='display_state_D' >전시중</label>
                                    <input type='radio' name='display_state' id='display_state_W' value='W' ".("W" == $display_state ? "checked":"")."> <label for='display_state_W' >전시대기</label>
                                    <input type='radio' name='display_state' id='display_state_E' value='E' ".("E" == $display_state ? "checked":"")."> <label for='display_state_E' >종료</label>
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>전시 기간 </b></td>
                                <td class='input_box_item' colspan=3>
                                    ".search_date('display_start','display_end',$display_start,$display_end,'Y',' ' ,' validation=true title="전시 기간" ')."
                                </td>
                            </tr>
                            <tr bgcolor=#ffffff>
                                <td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>전시 기간 노출 여부</b></td>
                                <td class='input_box_item' colspan=3>
                                    <input type='radio' name='display_date_use' id='display_date_use_Y' value='Y' ".("Y" == $display_date_use || "" == $display_date_use  ? "checked":"")."><label for='display_date_use_Y' >노출</label>
                                    <input type='radio' name='display_date_use' id='display_date_use_N' value='N' ".("N" == $display_date_use ? "checked":"")."> <label for='display_date_use_N' >미노출</label>
                                </td>
                            </tr>
                            <tr bgcolor='#F8F9FA'>
                                <td colspan=4>
                                    <table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
                                        <tr>
                                            <td height='30' colspan='3' style='padding:10px;'>
                                                <div>PC 노출</div>
                                                <textarea name='content_text_pc' id='content_text_pc' style='width:98%;height:1000px;display:block'>".$content_text_pc."</textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height='30' colspan='3' style='padding:10px;'>
                                                <div>MOBILE 노출</div>
                                                <textarea name='content_text_mo' id='content_text_mo' style='width:98%;height:1000px;display:block'>".$content_text_mo."</textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan=3 align=right style='padding:10px;'>
                                                <table width=100%  border=0>
                                                    <col width= '*' >
                                                    <col width= '100' >
                                                    <col width= '100' >
                                                    <col width= '100' >
                                                    <tr>
                                                        <td><input type='checkbox' name='delete_cache' id='delete_cache' value='Y'><label for='delete_cache'>캐쉬삭제하기</label></td>
                                                        <td><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle></td>
                                                        <td><a href='content_list.php'><img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle></a></td>
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
            </table>";
}
$Contents .= "
		</td>
	</tr>
</table>
";
$Contents .= "
		<iframe name='calcufrm' id='calcufrm' src='' width=0 height=0></iframe>
<Script Language='JavaScript'>
    if($content_type == 1 || $content_type == 4){
        init();
    }
    my_init($group_total);
    sortGroup(true);
	createProducts();
    
    function AddCopyRow(target_id, option_var_name){
    
        var table_target_obj = $('table[id='+target_id+']');
        var option_obj = $('#'+target_id);
        
        var option_length = 0;
        table_target_obj.find('tr:last').each(function(){
             option_length = $(this).find('#option_length').val();
        });
        rows_total = parseInt(option_length) + 1;
    
        var newRow = option_obj.find('tr:first').clone(true).wrapAll('<table/>').appendTo('#'+option_obj.attr('id'));  //
        
        newRow.find('input[id=option_length]').val(rows_total);
        if(target_id == 'youtube_area'){
            newRow.find('input[id=player_youtube]').attr('name',option_var_name+'['+rows_total+']');
            newRow.find('input[id=player_youtube]').val('');
        }else{
            newRow.find('input[id=player_instar]').attr('name',option_var_name+'['+rows_total+']');
            newRow.find('input[id=player_instar]').val('');
        }
        
    }
    
    $('#instar_del').live('click',function() {
        if($('#instar_area tr').size() > 1) $(this).parents('#add_table').remove();
    });
    
    $('#youtube_del').live('click',function() {
        if($('#youtube_area tr').size() > 1) $(this).parents('#add_table').remove();
    });
</Script>
";

$P = new LayOut();

$P->addScript = $Script; /**/
$P->OnloadFunction = ""; //showSubMenuLayer('storeleft'); MenuHidden(false);
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
$P->Navigation = "컨텐츠관리 > 컨텐츠관리 > 컨텐츠 등록";
$P->title = "컨텐츠 등록";
echo $P->PrintLayOut();


function getImageUploadHtmlNew($id, $content_type){

    if($content_type == '2'){
        $imgpath	= $_SESSION["admin_config"]["mall_data_root"] . "/images/content/style/".$id;
    }else if($content_type == '3'){
        $imgpath	= $_SESSION["admin_config"]["mall_data_root"] . "/images/content/player/".$id;
    }
    $mstring = "";
    if (file_exists($imgpath)) {
        $handle  = opendir($imgpath); // 디렉토리 open

        $files = array();
        $eleCount = 1;

        // 디렉토리의 파일을 저장
        while (false !== ($filename = readdir($handle))) {
            // 파일인 경우만 목록에 추가한다.
            if(is_file($imgpath . "/" . $filename)){
                $files[] = $filename;

            }
        }
        closedir($handle); // 디렉토리 close

        sort($files);

        foreach ($files as $f) { // 파일명 출력
            $mstring .= '<li id=li_productImage_'.$eleCount.' vieworder='.$eleCount.' viewcnt='.$eleCount.' style=float:left;width:110px;>';
            $mstring .= '<table width=95% cellspacing=1 cellpadding=0 style=table-layout:fixed;height:180px;>';
            $mstring .= "<tr><td><img src='".$imgpath."/".$f."' width=100px height=100px>";
            $mstring .= "<input type=hidden name=imgName[] id='imgName_".$eleCount."' value='".$f."' />";
            $mstring .= '<input type=hidden name=imgTemp[] id=imgTemp_'.$eleCount.' value='.$imgpath.' />';
            $mstring .= '</td></tr>';
            $mstring .= '<tr><td align=center><button type=button onclick=ingDel('.$eleCount.')>삭제</td></tr>';
            $mstring .= '</table>';
            $mstring .= '</li>';
            $eleCount++;
        }
    }

    return $mstring;
}
?>