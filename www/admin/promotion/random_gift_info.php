<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-12-06
 * Time: 오후 3:05
 */
include("../class/layout.class");

if($gc_ix){
    $sql = "select * from shop_gift_random_certificate where gc_ix = '".$gc_ix."' and status = 'Y' ";
    $db->query($sql);
    if($db->total){
        $db->fetch();
        $act = 'update';
        $gift_certificate_name = $db->dt['gift_certificate_name'];
        $memo = $db->dt['memo'];
        $gift_start_date = $db->dt['gift_start_date'];
        $gift_end_date = $db->dt['gift_end_date'];
        $is_use = $db->dt['is_use'];
        $create_cnt = $db->dt['create_cnt'];
        $percentage = $db->dt['percentage'];
        $gift_file_name = $db->dt['gift_file_name'];
        $gift_file_path = $db->dt['gift_file_path'];
        $gift_img = "";
        if(file_exists($_SERVER['DOCUMENT_ROOT'].$gift_file_path)){
            $gift_img = "<img src='".$gift_file_path."' style='vertical-align:middle; max-width:100px;'  >";
        }
        

        $sql = "select *,count(*) as cnt from shop_gift_random_certificate_detail where gc_ix = '".$gc_ix."' group by gift_type, gift_value order by gcd_ix asc";
        $db->query($sql);
        $gift_details = $db->fetchall();
        $giftCouponArray = array();
        $giftMileageArray = array();
        if(is_array($gift_details)){
            foreach($gift_details as $key=>$val){
                if($val['gift_type'] == 'C'){
                    $sql = "select cupon_no,publish_name from shop_cupon_publish where cupon_ix = '".$val['gift_value']."' ";
                    $db->query($sql);
                    if($db->total){
                        $db->fetch();
                        $cupon_no = $db->dt['cupon_no'];
                        $publish_name = $db->dt['publish_name'];

                        $giftCouponArray[$key]  = $publish_name ." ".$cupon_no ." ". $val['cnt']." 건 발행";
                    }

                }else{
                    $giftMileageArray[$key] = "지급액 ". number_format($val['gift_value']) ." 수량 ".number_format($val['cnt'])." 건 발행";
                }
            }
        }


    }else{
        $act = 'insert';
    }
}else{
    $act = 'insert';
}

if($act == 'insert') {
    $giftInputItem = "
    <tr height=27>
        <td class='input_box_title'>발행 쿠폰 설정 <img src='" . $required3_path . "'> </td>
        <td class='input_box_item' style='padding: 10px;'>
        <table width='100%' id='coupon_area'>
            <col width='280px' />
            <col width='100px' />
            <col width='*' />
            <tr id='add_table'>                                                             
                <input type='hidden' name='coupon_seq[]' id='option_length' value='0'>                                                                
                <td> 
                    발행쿠폰 : " . CouponPublishSelectBox('', "publish_ix[0]") . "
                </td>
                <td> 
                    수량 : <input type='text' name='issued_quantity[0]' id='issued_quantity' class='textbox devInputCnt' style='width:50px;' />
                </td>
                <td>
                    <input type='button' id='coupon_add' value='추가' title='추가' style='cursor:pointer;' onclick=\"AddCopyRow('coupon_area','publish_ix')\">
                    <input type='button' id='coupon_del' value='삭제' title='삭제' style='cursor:pointer;' >
                </td>
            </tr>
        </table>
        </td>
    </tr>                                                
    <tr height=27>
        <td class='input_box_title'>발행 적립금 설정 <img src='" . $required3_path . "'> </td>
        <td class='input_box_item' style='padding: 10px;'>
        <table width='100%' id='mileage_area'>
            <col width='120px' />
            <col width='100px' />
            <col width='*' />
            <tr id='add_table'>                                                                
                <input type='hidden' name='mileage_seq[]' id='option_length' value='0'>                                                                
                <td> 
                    지급액 : <input type='text' class='textbox' name='mileage[0]' id='mileage' style='width:50px;' />
                </td>
                <td> 
                    수량 : <input type='text' name='mileage_value[0]' id='mileage_value' class='textbox devInputCnt' style='width:50px;' />
                </td>
                <td>
                    <input type='button' id='mileage_add' value='추가' title='추가' style='cursor:pointer;' onclick=\"AddCopyRow('mileage_area','mileage')\">
                    <input type='button' id='mileage_del' value='삭제' title='삭제' style='cursor:pointer;' >
                </td>
            </tr>
        </table>
        </td>
    </tr>
    ";
}else{
    $giftCouponItem = "";
    if(isset($giftCouponArray)){
        $giftCouponItem = implode('<br>',$giftCouponArray);
    }
    $giftMileageItem = "";
    if(isset($giftMileageArray)){
        $giftMileageItem = implode('<br>',$giftMileageArray);
    }
    $giftInputItem = "
    <tr height=27>
        <td class='input_box_title'>발행 쿠폰 정보 <img src='" . $required3_path . "'> </td>
        <td class='input_box_item' style='padding: 10px;'>
        <table width='100%' id='coupon_area'>
            <col width='280px' />
            <col width='100px' />
            <col width='*' />
            <tr id='add_table'>                                                             
                ".$giftCouponItem."
            </tr>
        </table>
        </td>
    </tr>                                                
    <tr height=27>
        <td class='input_box_title'>발행 적립금 정보 <img src='" . $required3_path . "'> </td>
        <td class='input_box_item' style='padding: 10px;'>
        <table width='100%' id='mileage_area'>
            <col width='120px' />
            <col width='100px' />
            <col width='*' />
            <tr id='add_table'>                                                                
                ".$giftMileageItem."
            </tr>
        </table>
        </td>
    </tr>
    ";
}
$Contents = "
<table width='100%' border='0' align='left'>
    <tr>
        <td align='left' colspan=6 > ".GetTitleNavigation("오프라인 상품권 생성", "프로모션(마케팅) > 오프라인 상품권 생성 ")."</td>
    </tr>
    <tr>
        <td>
            <div id='TG_INPUT' style='position: relative; display: block;'>
                <form name='INPUT_FORM' method='post' onSubmit=\"return randomMileage(this)\" action='random_gift.act.php' enctype='multipart/form-data' target='iframe_act'>
                <input type='hidden' name=act value='$act'>
                <input type='hidden' name=gc_ix value='$gc_ix'>
                    <table border='0' width='100%' cellspacing='1' cellpadding='0'>
                        <tr>
                            <td bgcolor='#6783A8'>
                                <table border='0' cellspacing='0' cellpadding='0' width='100%'>
                                    <tr>
                                        <td bgcolor='#ffffff'>
                                            <table border='0' cellpadding=2 cellspacing=0 width='100%' class='input_table_box'>
                                            <col width='15%' />
                                            <col width='*' />
                                                <tr height=27>
                                                    <td class='input_box_title' nowrap>상품권 이름 <img src='".$required3_path."'></td>
                                                    <td class='input_box_item'>
                                                        <input type='text' name='gift_certificate_name' class='textbox' value='$gift_certificate_name' validation='true' title='상품권 이름' style='width:400px;' >                                                        
                                                    </td>
                                                </tr>
                                                <tr height=27>
                                                    <td class='input_box_title' nowrap>상품권 설명 <img src='".$required3_path."'></td>
                                                    <td class='input_box_item'>
                                                        <input type='text' name='memo' class='textbox' value='".$memo."' validation='true' title='상품권 설명' style='width:400px;' >                                                        
                                                    </td>
                                                </tr>
                                                ".$giftInputItem."
                                                <tr>
                                                    <td class='input_box_title' nowrap > <b>상품권 사용기간 <img src='".$required3_path."'></b></td>
                                                    <td class='input_box_item' style='padding:10px;' >
                                                    ".search_date('gift_start_date','gift_end_date',$gift_start_date,$gift_end_date,'N','A','title=사용기간 validation=true')."
                                                    </td> 
                                                </tr>   
                                                <tr>
                                                    <td class='input_box_title' nowrap > <b>상품권 노출확률 <img src='".$required3_path."'></b></td>
                                                    <td class='input_box_item' style='padding:10px;' >
                                                        <input type='text' class='textbox' value='".$percentage."' name='percentage' size='3' maxlength='3' /> %
                                                    </td>                                             
                                                </tr>   
                                                <tr>
                                                    <td class='input_box_title' nowrap > <b>상품권 이미지 <img src='".$required3_path."'></b></td>
                                                    <td class='input_box_item' style='padding:10px;' >
                                                        <input type='hidden' name='b_gift_img' value='".$gift_file_path."' />
                                                        <input type='file' name='gift_img' />
                                                        <span> ".$gift_file_name." ".$gift_img." </span>
                                                    </td>                                             
                                                </tr>
                                                <tr>
                                                    <td class='input_box_title' nowrap > <b>상품권 사용여부 <img src='".$required3_path."'></b></td>
                                                    <td class='input_box_item' style='padding:10px;' >
                                                        <input type='radio' name='is_use' id='is_use_1' value='1' ".CompareReturnValue('1',$is_use,"checked")." checked><label for='is_use_1'>사용함</label>
                                                        <input type='radio' name='is_use' id='is_use_0' value='0' ".CompareReturnValue('0',$is_use,"checked")."><label for='is_use_0'>사용안함</label>
                                                    </td> 
                                                </tr>
                                                
                                            </table>
                                            <table border='0' cellspacing='0' cellpadding='0' width='100%' style='margin-top:5px;'>
                                                <tr> 
                                                    <td>
                                                    <input type='hidden' name='create_cnt' id='total_count_input' value='0' />
                                                    총 발행 수량 : <span id='total_count'>".$create_cnt."</span> <span style='color:blue;'>* 최대 발행 가능 수량은 10000 건 입니다.</span>
                                                    </td>
                                                </tr>
                                            </table>
                                            <table border='0' cellspacing='0' cellpadding='0' width='100%' style='margin-top:5px;'>
                                                <tr>
                                                    <td style='text-align:left;'></td>
                                                    <td style='text-align:right;' >
                                                        <table align=right>
                                                            <tr>										
                                                                <td>
                                                                    <input type=image src='../image/b_save.gif' border=0> 
                                                                    <a href='random_gift_list.php'><img src='../image/b_cancel.gif' border=0 align=absmiddle></a>
                                                                </td>
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
            </div>
        </td>
    </tr>
</table>";

$Script ="
<script type='text/javascript'>
    $(document).ready(function(){
       $('.devInputCnt').keyup(function(){
           
          var totalCnt = 0;
          $('.devInputCnt').each(function(){
              var cnt = parseInt($(this).val());
              if(cnt > 0){
                totalCnt += cnt;    
              }
              
          });
          
          if(totalCnt > 10000){
              alert('최대 발행 수량을 초과 하였습니다. 확인 후 등록 바랍니다.');
          }
          
          $('#total_count').text(totalCnt);
          $('#total_count_input').val(totalCnt);
       });
       
       
		$('#coupon_del').live('click',function() {
			if($('#coupon_area tr').size() > 1) $(this).parents('#add_table').remove();
		});
		
		
		$('#mileage_del').live('click',function() {
			if($('#mileage_area tr').size() > 1) $(this).parents('#add_table').remove();
		});
    });
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
        newRow.find('select[id^=publish_ix]').attr('name',option_var_name+'['+rows_total+']');
        newRow.find('input[id=issued_quantity]').attr('name','issued_quantity['+rows_total+']');
        newRow.find('input[id=issued_quantity]').val('');
       // newRow.find('input[id=coupon_add]').remove();
        
        
        
        newRow.find('input[id=mileage]').attr('name',option_var_name+'['+rows_total+']');
        newRow.find('input[id=mileage_value]').attr('name','mileage_value['+rows_total+']');
        newRow.find('input[id=mileage]').val('');
        newRow.find('input[id=mileage_value]').val('');
       // newRow.find('input[id=mileage_add]').remove();
    
    }
    
    var submitBool = true;
    function randomMileage(frm){
        if(submitBool == false){
            alert('발급 중 입니다.');
            return false;
        }       
        
        if(CheckFormValue(frm)){
            
            if(frm.act.value == 'update'){
                return true;   
            }
            
            //발급 항목 사전 안내 처리
            var msg = ''
            var totalInputCnt = 0;
            $('input[name^=coupon_seq]').each(function(){
                var seq = $(this).val();
                
                var coupon_name = $('select[name=\"publish_ix['+seq+']\"]').find('option:selected').text();
                var coupon_cnt = parseInt($('input[name=\"issued_quantity['+seq+']\"]').val());
                
                if(coupon_cnt > 0){
                    totalInputCnt = totalInputCnt+coupon_cnt
                    msg += coupon_name.substr( 0, 30 )+'...' + ' [수량 :' + coupon_cnt +'] \\n';                
                }
            });
            
            $('input[name^=mileage_seq]').each(function(){
                var seq = $(this).val();
                
                var mileage = parseInt($('input[name=\"mileage['+seq+']\"]').val());
                var mileage_value = parseInt($('input[name=\"mileage_value['+seq+']\"]').val());
                
                if(mileage > 0 && mileage_value > 0){
                    totalInputCnt = totalInputCnt+mileage_value
                    msg += '지급액 :'+mileage + 'P [수량 :' + mileage_value +'] \\n';                
                }
            });         
            
            if(msg){
                msg += '해당 내역을 발행 하시겠습니까?';
                if(confirm(msg)){
                    submitBool = false;
                    return true;
                }else{
                    submitBool = true;
                    return false;
                }
            }else{
                alert('발행 할 쿠폰 또는 적립금을 입력 해 주세요');
                submitBool = true;
                return false;
            }
        }else{
            return false;
        }    
    }
</script>
";

$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션(마케팅) > 랜덤 상품권 생성";
$P->title = "랜덤 상품권 생성";
$P->strLeftMenu = promotion_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();