<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-08-02
 * Time: 오후 6:31
 */

function ContentsDivName($div_depth){
    switch($div_depth){
        case 'protection':
            $div_name = '동물보호활동 > ';
            break;
        case 'adopt':
            $div_name = '반려동물 입양';
            break;
        case 'howau':
            $div_name = 'How Are You';
            break;
        case 'whos_next':
            $div_name = "Who's Next";
            break;
        case 'im_fine':
            $div_name = "I'm Fine > ";
            break;
        case 'thank_you':
            $div_name = 'Thank You';
            break;
        case 'and_you':
            $div_name = 'And You';
            break;
        default:
            $div_name = '선택된컨텐츠 타입이 존재 하지 않습니다.';
            break;
    }
    return $div_name;
}
function ContentsDivSubSelect($input_name,$div_depth,$div_sub_depth,$validation=''){

    $protection_array = array('group' => '단체','shelter' => '보호소','volunteer' => '봉사활동');
    $im_fine_array = array('news' => 'NEWS','features' => 'FEATURES','diary' => 'DIARY');
    if($div_depth == 'protection'){
        $select_array = $protection_array;
    }else if($div_depth == 'im_fine'){
        $select_array = $im_fine_array;
    }else{
        $display = " display:none;";
    }

    $html = "";
    if(is_array($select_array)){
        $html .="
        <select name='".$input_name."' id='".$input_name."'  style=\"font-size:12px; $display\" validation='".$validation."' title='분류'> 
            <option value='' ".CompareReturnValue('',$div_sub_depth,'selected').">선택하세요</option>\";
        ";
        foreach($select_array as $key => $val){
            $html .="
            <option value='".$key."' ".CompareReturnValue($key,$div_sub_depth,'selected').">".$val."</option>";
        }
        $html .="</select>";
    }

    return $html;
}

function SearchLikeCnt($co_ix){

    return 0;
}

function AddInfoArea($div_depth){
    global $add_info,$term_sdate,$term_edate;
    $html = "";
    if($div_depth == 'protection'){
        $html .="
        </br>
        <table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' >   
            <tr>
                <td align='left' style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>추가정보 입력</b></div>")."</td>
            </tr>
        </table>
        <table border='0' cellpadding='0' cellspacing='0' width='100%'>	
            <tr> 
                <td> 
                    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                        <tr>
                            <td class='box_05' valign=top style='padding:0px;'>
                                <table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
                            
                                <col width=15%>
                                <col width=*>
                                <tr height=27>
                                    <td class='search_box_title' >후원하기 버튼 사용여부 </td>
                                    <td class='search_box_item div_depth' >
                                        <input type='radio' name='add_info[btn_use]' id='btn_use_y' value='Y' ".CompareReturnValue('Y',$add_info['btn_use'],'checked')." checked ><label for='btn_use_y'>사용</label>
                                        <input type='radio' name='add_info[btn_use]' id='btn_use_n' value='N' ".CompareReturnValue('N',$add_info['btn_use'],'checked')." ><label for='btn_use_n'>사용하지않음</label>
                                    </td>
                                </tr>
                                <tr height=27>
                                    <td class='search_box_title' >URL 입력 </td>
                                    <td class='search_box_item div_depth' >
                                        <input type='radio' name='add_info[link_target]' id='link_target_s' value='_self' ".CompareReturnValue('_self',$add_info['link_target'],'checked')." checked ><label for='link_target_s'>페이지이동</label>
                                        <input type='radio' name='add_info[link_target]' id='link_target_b' value='_blank' ".CompareReturnValue('_blank',$add_info['link_target'],'checked')." ><label for='link_target_b'>새창열림</label>
                                        <span>http://</span>
                                        <input type='text' class='textbox' name='add_info[target_url]' value='".$add_info['target_url']."' style='width:70%' />
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        ";
    }else if($div_depth == 'adopt'){
        $html .="
        </br>
        <table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' >   
            <tr>
                <td align='left' style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>추가정보 입력</b></div>")."</td>
            </tr>
        </table>
        <table border='0' cellpadding='0' cellspacing='0' width='100%'>	
            <tr> 
                <td> 
                    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                        <tr>
                            <td class='box_05' valign=top style='padding:0px;'>
                                <table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
                            
                                <col width=15%>
                                <col width=*>
                                <tr height=27>
                                    <td class='search_box_title' >입양스토리 버튼 사용여부 </td>
                                    <td class='search_box_item div_depth' >
                                        <input type='radio' name='add_info[btn_use]' id='btn_use_y' value='Y' ".CompareReturnValue('Y',$add_info['btn_use'],'checked')." checked ><label for='btn_use_y'>사용</label>
                                        <input type='radio' name='add_info[btn_use]' id='btn_use_n' value='N' ".CompareReturnValue('N',$add_info['btn_use'],'checked')." ><label for='btn_use_n'>사용하지않음</label>
                                    </td>
                                </tr>
                                <tr height=27>
                                    <td class='search_box_title' >URL 입력 </td>
                                    <td class='search_box_item div_depth' >
                                        <input type='radio' name='add_info[link_target]' id='link_target_s' value='_self' ".CompareReturnValue('_self',$add_info['link_target'],'checked')." checked ><label for='link_target_s'>페이지이동</label>
                                        <input type='radio' name='add_info[link_target]' id='link_target_b' value='_blank' ".CompareReturnValue('_blank',$add_info['link_target'],'checked')." ><label for='link_target_b'>새창열림</label>
                                        <span>http://</span>
                                        <input type='text' class='textbox' name='add_info[target_url]' value='".$add_info['target_url']."' style='width:70%' />
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        ";
    }else if($div_depth == 'thank_you'){
        $html .="
        </br>
        <table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' >   
            <tr>
                <td align='left' style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>추가정보 입력</b></div>")."</td>
            </tr>
        </table>
        <table border='0' cellpadding='0' cellspacing='0' width='100%'>	
            <tr> 
                <td> 
                    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                        <tr>
                            <td class='box_05' valign=top style='padding:0px;'>
                                <table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
                            
                                <col width=15%>
                                <col width=*>
                                <tr height=27>
                                    <td class='search_box_title' >모금기간 </td>
                                    <td class='search_box_item div_depth' >
                                        ".search_date('term_sdate','term_edate',$term_sdate,$term_edate,'N','')."
                                    </td>
                                </tr>
                                <tr height=27>
                                    <td class='search_box_title' >후원 버튼 사용여부 </td>
                                    <td class='search_box_item div_depth' >
                                        <input type='radio' name='add_info[btn_use]' id='btn_use_y' value='Y' ".CompareReturnValue('Y',$add_info['btn_use'],'checked')." checked ><label for='btn_use_y'>사용</label>
                                        <input type='radio' name='add_info[btn_use]' id='btn_use_n' value='N' ".CompareReturnValue('N',$add_info['btn_use'],'checked')." ><label for='btn_use_n'>사용하지않음</label>
                                    </td>
                                </tr>
                                <tr height=27>
                                    <td class='search_box_title' >URL 입력 </td>
                                    <td class='search_box_item div_depth' >
                                        <input type='radio' name='add_info[link_target]' id='link_target_s' value='_self' ".CompareReturnValue('_self',$add_info['link_target'],'checked')." checked ><label for='link_target_s'>페이지이동</label>
                                        <input type='radio' name='add_info[link_target]' id='link_target_b' value='_blank' ".CompareReturnValue('_blank',$add_info['link_target'],'checked')." ><label for='link_target_b'>새창열림</label>
                                        <span>http://</span>
                                        <input type='text' class='textbox' name='add_info[target_url]' value='".$add_info['target_url']."' style='width:70%' />
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        ";
    }else if($div_depth == 'and_you'){
        $html .="
        </br>
        <table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' >   
            <tr>
                <td align='left' style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>추가정보 입력</b></div>")."</td>
            </tr>
        </table>
        <table border='0' cellpadding='0' cellspacing='0' width='100%'>	
            <tr> 
                <td> 
                    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                        <tr>
                            <td class='box_05' valign=top style='padding:0px;'>
                                <table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
                            
                                <col width=15%>
                                <col width=*>
                                <tr height=27>
                                    <td class='search_box_title' >진행기간 </td>
                                    <td class='search_box_item div_depth' >
                                        ".search_date('term_sdate','term_edate',$term_sdate,$term_edate,'N','')."
                                    </td>
                                </tr>
                                <tr height=27>
                                    <td class='search_box_title' >당첨자안내 </td>
                                    <td class='search_box_item div_depth' >
                                        <div style='padding:5px 5px 5px 0;'> 
                                            <textarea rows='5' style='width:70%' name='add_info[add_contents]' title='내용' > ".$add_info[add_contents]."</textarea>
                                        </div>
                                    </td>
                                </tr>                                
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        ";
    }



    return $html;
}