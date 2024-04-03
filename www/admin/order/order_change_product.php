<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-10-01
 * Time: 오후 12:19
 */
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

$db = new Database;
$db->query("SELECT od.pid,od.pname,od.option_id, od.option_text,od.gid FROM shop_order_detail od where od.od_ix = '$od_ix' and od.oid = '$oid' ");
$db->fetch();


$Script = "
<script> 
    $(document).ready(function(){
       $('#product_search').click(function(){
          var search_type = $('#search_type').val();
          var search_text = $('#search_text').val();
          
          if(!search_type){
              alert('검색타입을 지정해 주세요');
              return false;
          }
          if(!search_text){
              alert('검색어를 입력해 주세요');
              return false;
          }
          
            $('#pname_text').text('');
            $('#option_id').val('');
            $('#option_name_text').text('');
            $('#gid').val('');
            $('#gid_text').text('');
            $('#searchArea').hide();
            
          PoPWindow('./order_change_product_search.php?mmode=pop&search_type='+search_type+'&search_text='+encodeURIComponent(search_text),650,600,'order_change_product_search')
       });
    });
</script>
";

$Contents = "<form name='order_change_frm' method='POST' action='order_change_product.act.php' style='display:inline;' target='iframe_act'>
<input type='hidden' name='act' value='update'>
<input type='hidden' name='org_pid' value='".$db->dt[pid]."'>
<input type='hidden' name='org_gid' value='".$db->dt[gid]."'>
<input type='hidden' name='org_option_id' value='".$db->dt[option_id]."'>
<input type='hidden' name='oid' value='".$oid."'>
<input type='hidden' name='od_ix' value='".$od_ix."'>
    <table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
        <tr>
            <td align=center style='padding: 0 10 0 10'>

                <table border='0' width='100%' cellspacing='1' cellpadding='0' >
                    <tr>
                        <td >
                            <table border='0' width='100%' cellspacing='1' cellpadding='3' class='input_table_box' style='table-layout:fixed;' >
                                <col width='150px'>
                                <col width='*'>
                                <tr>
                                    <td class='input_box_title' align='left' >  주문번호</td>
                                    <td class='input_box_item point'><b class=blk>".$oid."</b></td>
                                </tr>
                                <tr>
                                    <td class='input_box_title' align='left' > 현재 상품정보</td>
                                    <td class='input_box_item'>
                                        <table cellpadding=0 cellspacing=0>
                                            <tr>
                                                <td rowspan='3'>
                                                    <img src='".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], "m")."'  onerror=\"this.src='".$admin_config[mall_data_root]."/images/noimg_52.gif'\" width=50 height=50 style='margin:5px;border:1px solid gray'>
                                                </td>
                                                <td style='line-height:150%;'>
                                                    상품명 : ".$db->dt[pname]."
                                                </td>								    
                                            </tr>
                                            <tr>
                                                <td>
                                                    옵션명 : ".$db->dt['option_text']."
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    품목코드 : ".$db->dt['gid']."
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr height='200px'>
                                    <td class='input_box_title' align='left'> 변경 상품정보</td>
                                    <td class='input_box_item'>
                                        <table cellpadding=0 cellspacing=0>
                                            <tr> 
                                                <td > 
                                                    <div> 
                                                        <select style='vertical-align: middle' id='search_type'>
                                                            <option value='id'>상품코드</option>
                                                            <option value='pname'>상품명</option>
                                                        </select>
                                                        <input type='text' class='textbox' id='search_text' style='vertical-align: middle'>
                                                        <button type='button' style='vertical-align: middle' id='product_search'>검색</button>
                                                    </div>
                                                    <div id='searchArea' style='padding-top:10px; display:none;'> 
                                                        <table cellpadding=0 cellspacing=0> 
                                                            <tr>
                                                                <td rowspan='3'>
                                                                    <div id='imageArea'></div>
                                                                </td>
                                                                <td style='line-height:150%;'>                                                                    
                                                                    상품명 : <span id='pname_text'> </span> 
                                                                </td>								    
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <input type='hidden' class='textbox' name='option_id' id='option_id' readonly>
                                                                    옵션명 : <span id='option_name_text' > </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <input type='hidden' class='textbox' name='gid' id='gid' readonly>
                                                                    품목코드 : <span id='gid_text'> </span>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table><br>
            </td>
        </tr>
        <tr>
            <td colspan=2 align=center style='padding:10px 0px;'>
            <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle><!--btn_inquiry.gif-->
            </td>
        </tr>
    </table>
</form>";



$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "주문관리 > 교환 상품 변경 ";
$P->NaviTitle = "교환 상품 변경";
$P->strContents = $Contents;
echo $P->PrintLayOut();