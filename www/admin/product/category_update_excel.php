<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-11-20
 * Time: 오후 1:27
 */
include("../class/layout.class");


$Contents ="
<table cellpadding=0 cellspacing=0 width='100%'>
    <tr>
        <td colspan=3>
            <form name='excel_input_form' method='post' action='category_update_excel.act.php' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target='iframe_act'><!--iframe_act-->
            <input type='hidden' name='act' value='new_excel_input'>
            <input type='hidden' name='cid' value=''>
            <input type='hidden' name='depth' value=''>
            <input type='hidden' name='page_type' value='update'>
                <table width='100%' border=0 cellpadding=0 cellspacing=1 class='input_table_box'>
                <col width=18%>
                <col width=*>
                    <tr height=30 align=center>
                        <td class='input_box_title' ><b>엑셀파일 입력</b></td>
                        <td class='input_box_item'>
                            <input type=file class='textbox' name='excel_file' style='height:22px;width:200px;' validation=true title='엑셀파일 입력'>
                            <a href='./category_update_excel.act.php?act=get_category_code' ><input type='button' value='카테고리 다운로드' /></a>
                        </td>
                    </tr>
                </table>
                <table width='100%' border=0 cellpadding=0 cellspacing=1>
                    <tr height=20>
                        <td style='padding:6px;line-height:140%;' colspan=2>
                            <div>
                            <ol>
                                <li>
                                    <img src='../image/emo_3_15.gif' border=0 align=absmiddle>
                                    엑셀정보에는 <b>' (따옴표)</b>는 사용하실수 없습니다. 
                                </li>                                
                                <li>
                                    <img src='../image/emo_3_15.gif' border=0 align=absmiddle>
                                    <span class='red'>주의사항</span>
                                </li>
                                <li style='padding-left:20px;'>
                                    <span class='red'>
                                    1)	카테고리ID(CID) 는 필수 정보로 수정 KEY 로 사용되기때문에 변조 되지 않도록 유의 바랍니다.
                                    </span>
                                </li>
                            </ol>
                            </div>
    
                        </td>
                    </tr>
                    <tr height=30>
                        <td colspan=2 style='padding:10px 0px;' align=center>
                            <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
    <tr>
        <td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 업로드 엑셀정보 </b>&nbsp;</div>")."</td>
    </tr>
    <tr>
        <td colspan=3 align=left style='padding-bottom:10px;'><div style='width:1400px;height:300px;overflow:auto;'>".MakeUploadExcelDataCategory('update')."</div></td>
    </tr>
    <tr>
        <td colspan=3 align=center style='padding-bottom:10px;'><img src='../images/korean/bt_modify.gif' alt='상품등록하기' onclick=\"UploadExcelCategoryReg('update');\" style='cursor:pointer;'/></div></div></td>
    </tr>
</table>";

$P = new LayOut();
$P->strLeftMenu = product_menu();
$P->addScript = "<script Language='JavaScript' src='category_update_excel.js'></script>".$script;
$P->Navigation = "상품관리 > 상품분류관리 > 카테고리일괄수정";
$P->title = "카테고리일괄수정";
$P->strContents = $Contents;

$P->PrintLayOut();

function MakeUploadExcelDataCategory($input_type = 'input'){

    //대량수정시 제외되는 체크박스 부분 2014-08-19 이학봉
    $check_out_array = array('cid','category_info');

    include("../logstory/class/sharedmemory.class");
    //auth(8);
    $shmop = new Shared("upload_category_data_".$_SESSION["admininfo"]["charger_ix"]);
    //	$shmop->clear();
    $shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";
    $shmop->SetFilePath();
    $upload_excel_data = $shmop->getObjectForKey("upload_category_data_".$_SESSION["admininfo"]["charger_ix"]);
    //echo "<pre>";
    //print_r($upload_excel_data);exit;

    if($upload_excel_data[session_id()]){
        $mstring = "<table cellpadding=3 cellspacing=0 class='list_table_box' >\n";
        $i = 0;
        $z = 0;

        foreach($upload_excel_data[session_id()] as $key => $value){

            $mstring .= "<tr align=center height=25 depth='".$key."'>\n";
            $mstring .= "\t<td ".($i == 0 ? "class=m_td style='padding:0px 50px;'   nowrap":" class='point ' nowrap")."> ".($i == 0 && $input_type == 'update'? "처리현황":"<span id='status_message_".$value["p_no"]."'>".$value["status_message"]."")."</span></td>\n";
            foreach($value as $_key => $_value){

                if($_key != "p_no"){
                    $mstring .= "\t<td ".($i == 0 ? "class='m_td' nowrap  style='max-width:300px;'":"  nowrap style='max-width:300px; '").">
											".($i==0 && $input_type == 'update' && !in_array($_key,$check_out_array)?"":"")."
											<label for='update_check_".$_key."'>".@htmlspecialchars($_value)."</label>";
                    if($_key == "cid" && $i != 0){
                        $mstring .= "<input type=hidden class='upload_excel_infos' id='p_no' name='upload_excel_infos[".$value["p_no"]."][p_no]' value='".$value["p_no"]."' >";
                        $z++;
                    }

                    $mstring .= "
											</td>\n";
                }
            }
            $mstring .= "</tr>\n";

            $i++;
        }
        $mstring .= "</table>\n";
    }
    return $mstring;
}
