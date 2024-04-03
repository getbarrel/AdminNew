<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-08-01
 * Time: 오후 7:54
 */
include $_SERVER['DOCUMENT_ROOT']."/admin/class/layout.class";
include $_SERVER['DOCUMENT_ROOT']."/admin/bienview/bienview_lib.php";

if($_GET['co_ix']){
    $act = 'update';

    $sql = "select * from bienview_contents_info where co_ix = '".$_GET['co_ix']."' ";
    $db->query($sql);
    $db->fetch();
    $co_ix = $db->dt['co_ix'];
    $div_depth = $db->dt['div_depth'];
    $div_depth_sub = $db->dt['div_depth_sub'];
    $file_name = $db->dt['file_name'];
    $file_url = $db->dt['file_url'];
    $title = $db->dt['title'];
    $contents = $db->dt['contents'];
    $disp = $db->dt['disp'];
    $term_sdate = $db->dt['term_sdate'];
    $term_edate = $db->dt['term_edate'];

    $dirpath = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/bienview/".$div_depth."/";
    $nextpath = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/bienview/".$div_depth."/".$co_ix."/";
    $filepath = $_SERVER["DOCUMENT_ROOT"].$file_url;

    if(file_exists($nextpath) && $file_url){
        $image_exists = 'Y';
    }else{
        $image_exists = 'N';
    }
    $sql = "SHOW TABLES LIKE 'bienview_contents_add_" . $div_depth . "'";
    $db->query($sql);

    if(is_array($db->fetchall())) {
        $sql = "select add_key,add_value from bienview_contents_add_" . $div_depth . " where co_ix = '" . $_GET['co_ix'] . "' ";
        $db->query($sql);
        $add_contents = $db->fetchall(0);

        if (is_array($add_contents)) {
            foreach ($add_contents as $add_colum) {
                if (is_array($add_colum)) {
                    foreach ($add_colum as $key => $val) {
                        if ($key == 'add_key') {
                            $target = $val;
                        } else if ($key == 'add_value') {
                            $target_value = $val;
                        }
                    }
                    $add_info[$target] = $target_value;
                }
            }
        }
    }


}else{
    $act = 'insert';
}

$Contents01 .= "
    
	<form name='contents_frm' method='POST' action='./contents_input.act.php' target='act' onsubmit='return ContentsSubmit(this)' enctype='multipart/form-data'>
	<input type='hidden' name='co_ix' value='".$co_ix."'>
	<input type='hidden' name='act' value='".$act."'>
    <input type='hidden' name='div_depth' id='div_depth' value='".$div_depth."' />
    
	<table border='0' cellpadding='0' cellspacing='0' width='100%'>	
        <tr>
            <td align='left' colspan=2  width='100%' valign=top style='padding-top:0px;'>
                <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                <tr>
                    <td class='box_05' valign=top style='padding:0px;'>
                        <table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
                    
                        <col width=15%>
                        <col width=*>
                        <tr height=27>
                            <td class='search_box_title' >분류 <span class='red'>*</span></td>
                            <td class='search_box_item div_depth' >
                                ".ContentsDivName($div_depth)."
                                ".ContentsDivSubSelect('div_depth_sub',$div_depth,$div_depth_sub,'true')."
                                
                            </td>
                        </tr>
                        <tr height=27>
                            <td class='search_box_title' >대표이미지 </td>
                            <td class='search_box_item div_depth' >
                                <input type='file'	name='file_name' />";
                                if($image_exists == 'Y'){
                                    $Contents01 .= "
                                    <span>".$file_name."</span> 
                                    <img src='../images/".$admininfo["language"]."/btn_del.gif' onclick=\"DelImg('".$co_ix."','".$filepath."')\" style='vertical-align: middle'/>";
                                }
                                $Contents01 .= "
                            </td>
                        </tr>
                        <tr height=27>						
                            <td class='search_box_title' >제목 <span class='red'>*</span></td>
                            <td class='search_box_item' >
                                <input class='textbox' value='".$title."' style='width:80%' autocomplete='off' name=title  title='제목' validation='true'>							
                            </td>
                        </tr>
                        <tr height=27>						
                            <td class='search_box_title' >내용 </td>
                            <td class='search_box_item' >
                                <div style='padding:5px 5px 5px 0;'> 
                                    <textarea rows='5' style='width:70%' name='contents' title='내용' > ".$contents."</textarea>
                                </div>
                            </td>
                        </tr>					
                        <tr height=27>						
                            <td class='search_box_title' >전시상태 </td>
                            <td class='search_box_item' >
                                <input type='radio' name='disp' id='disp_y' value='Y' ".CompareReturnValue('Y',$disp,'checked')." checked ><label for='disp_y'>전시</label>
                                <input type='radio' name='disp' id='disp_n' value='N' ".CompareReturnValue('N',$disp,'checked')." ><label for='disp_n'>미전시</label>
                            </td>
                        </tr>
                        </table>
                    </td>
                </tr>
                </table>
            </td>
        </tr>
	</table>
    
    ".AddInfoArea($div_depth)."	
    
    <table border='0' cellpadding='0' cellspacing='0' width='100%'>	
	<tr>
		<td colspan=3 align=center style='padding:10px 0;'>
			<img src='../images/".$admininfo["language"]."/b_cancel.gif' onclick=\"history.back();\" style='vertical-align:middle; cursor:pointer;' border=0>
			<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0>
		</td>
	</tr>
	</table>
	</form>";

$Contents = $Contents01;
$P = new LayOut();
$P->addScript = "
    <script language='javascript' src='./bienview.js'></script>\n
    <script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n
    ".$Script;//<script language='javascript' src='../include/DateSelect.js'></script>\n
if($div_depth == 'and_you'){
    $P->OnloadFunction = "CKEDITOR.replace('contents').config.height = '200px';CKEDITOR.replace('add_info[add_contents]').config.height = '200px';";
}else{
    $P->OnloadFunction = "CKEDITOR.replace('contents').config.height = '200px';";
}

$P->strLeftMenu = bienview_menu();
$P->Navigation = "BIENVIEW관리 > 콘텐츠 관리 > 콘텐츠 등록/수정 ";
$P->title = "콘텐츠 등록/수정";
$P->strContents = $Contents;
echo $P->PrintLayOut();