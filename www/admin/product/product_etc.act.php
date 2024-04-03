<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-12-16
 * Time: 오후 4:09
 */
include $_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class";


if($act == "favorites_insert"){
    $dataArray = getExcelData($checked_colums);
    if($dataArray){
        $data = urlencode(json_encode($dataArray));
        $sql = "insert into shop_product_favorites_excel_info (title,data,editdate,regdate) values ('".$favorites_excel_name."','".$data."',NOW(),NOW()) ";
        echo $db->query($sql);
        exit;
    }

}

if($act == "favorites_update"){
    $dataArray = getExcelData($checked_colums);
    if($dataArray){
        //print_r($goods_basic_sample);

        $data = urlencode(json_encode($dataArray));
        $sql = "update shop_product_favorites_excel_info set data = '".$data."' , title = '".$favorites_excel_name."' where idx = '".$favorites_excel_key."' ";

        echo $db->query($sql);
        exit;
    }
}
if($act == "favorites_delete"){
    if($favorites_excel_key){
        $sql = "delete from shop_product_favorites_excel_info where idx = '".$favorites_excel_key."' ";
        echo $db->query($sql);
        exit;
    }
}

if($act == "getFavoritesData"){
    if($idx){
        $sql = "select data from shop_product_favorites_excel_info where idx = '".$idx."' ";
        $db->query($sql);
        $db->fetch();
        $excelInfo = json_decode(urldecode($db->dt['data']),true);

        $infoHtml = "";
        if(is_array($excelInfo)){
            foreach ($excelInfo as $key => $value) {
                $infoHtml .= "
                <li class='ui-state-default".($value[color] ? " ui-state-disabled":"")."' id='".$key."' style='float:left;height:28px;width:153px;margin:1px; background-color:".$value[color]."' >
                    <table width=153 border=0 style='border:0px;'>
                    <col width=5>
                    <col width=20>
                    <col width=*>
                    <tr style='background:none;'>
                        <td>
    
                        </td>
                        <td>
                        <input type='checkbox' id='colums' class='colums' style='cursor:pointer;' name='checked_colums[".$value[code]."]' value='".$key."'  validation='false' code_group='".$value[code_group]."' title='".$value[title]."'  ".($value['checked'] == "1" ? "checked":"")." onclick=\"single_toggle($(this))\">
                        </td>
                        <td style='padding-left:0px' ondblclick=\"colum_toggle($(this));\">
                            <div style='white-space:nowrap;text-overflow:ellipsis; overflow:hidden; width:126px;'>
                                <label for='_colums_".$key."'>".$value[title]."</label>
                            </div>
                        </td>
                    </tr>
                    </table>
                </li>";
            }
        }

        echo $infoHtml;
        exit;
    }
}


function getExcelData($checked_colums){

    $page_type = 'update';
    include("goods_mandatory_info.lib2.php");


    $dataArray = array();
    if(is_array($checked_colums)){
        foreach($checked_colums as $key=>$val){
            if(is_array($goods_basic_sample)){
                foreach($goods_basic_sample as $k=>$v){
                    if($key == $v['code']){
                        $dataArray[$k] =  $v;
                        $dataArray[$k]['checked'] =  $val;
                    }
                }
            }
        }
    }
    return $dataArray;
}