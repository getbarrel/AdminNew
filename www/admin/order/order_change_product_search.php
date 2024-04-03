<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-10-01
 * Time: 오후 1:51
 */
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

if(!$search_type && !$search_text){
    echo "<script>alert('검색 정보가 누락 되었습니다.');self.close();</script>";
    exit;
}

$where = " and ".$search_type." LIKE '%".$search_text."%' ";


$sql = "select count(*) cnt from shop_product where is_delete = 0 and product_type='0' and state = '1' $where";
$db->query($sql);
$db->fetch();
$total = $db->dt['cnt'];
if($total > 50){
    echo "<script>alert('검색 된 상품이 많습니다. 보다 정확한 검색어를 입력 해 주세요');self.close();</script>";
    exit;
}

$sql = "select * from shop_product where is_delete = 0 and product_type='0' and state = '1' $where";

$db->query($sql);
$products = $db->fetchall();
$item_area = "";
if(count($products) > 0){
    if(is_array($products)){

        foreach($products as $key=>$val){

            $status_text = getStateText($val['state']);
            $options_select = getOptionSelect($val['id']);

            $item_area .= "
                <tr>
                    <td bgColor='#ffffff'>
                        <table cellpadding=0 cellspacing=0> 
                            <tr>
                                <td>
                                   <img src='".PrintImage($admin_config[mall_data_root]."/images/product", $val['id'], "m")."'   width=50 height=50 style='margin:5px;border:1px solid gray'' />
                                </td>
                                <td style='line-height:150%;'>  
                                    <input type='hidden' id='pname_data_".$val['id']."' value='".$val['pname']."' />
                                    상품명 : ".$val['pname']."
                                </td>								    
                            </tr>
                        </table>
                    </td>
                    <td bgColor='#ffffff' align='center'>".$status_text."</td>
                    <td bgColor='#ffffff' align=center>".$options_select."</td>
                    <td bgColor='#ffffff' align=center>
                        <button class='select_product' select_pid='".$val['id']."'>선택</button>
                    </td>
                </tr>
            ";
        }
    }
}else{
    echo "<script>alert('검색된 상품이 존재하지 않습니다.');self.close();</script>";
    exit;
}
$Contents = "
<table width='100%' border='0' cellpadding='0' cellspacing='1' bgcolor=silver>
    <tr>
		<td bgcolor='silver'>
			<table border='0' width='100%' cellspacing='0' cellpadding='2'>
				<tr>
					<td bgcolor='#ffffff'>
						<table border='0' width='100%'>
							<tr>
								<td>
									<table border='0' width='100%' cellspacing='1' cellpadding='3' class='input_table_box' style='table-layout:fixed;' >
										<col width='*' />
										<col width='10%' />
										<col width='35%' />
										<col width='10%' />
										<tr height='30' align=center>											
											<td class='m_td'><b>상품명</b></td>
											<td class='m_td '><b>상태</b></td>
											<td class='m_td'><b>옵션 선택</b></td>
											<td class='m_td '><b>적용</b></td>											
                                        </tr>
                                        ".$item_area."
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
";

$Script = "
<script>
    $(document).ready(function(){
        $('.select_product').on('click',function(){
            var pid = $(this).attr('select_pid');
            var option_id = $('#option_data_'+pid+' option:selected').val();    
            var pname_text = $('#pname_data_'+pid).val();    
            var option_text = $.trim($('#option_data_'+pid+' option:selected').text());   
            var option_gid = $.trim($('#option_data_'+pid+' option:selected').attr('option_gid'));        
              
            if(option_id){
                $(opener.document).find('#pname_text').text(pname_text);
                $(opener.document).find('#option_id').val(option_id);
                $(opener.document).find('#option_name_text').text(option_text);
                $(opener.document).find('#gid').val(option_gid);
                $(opener.document).find('#gid_text').text(option_gid);
                $(opener.document).find('#searchArea').show();
                
                self.close();
            }else{
                alert('옵션을 선택해주세요')
            }

        });
    });
</script>
";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "주문관리 > 교환 상품 검색 ";
$P->NaviTitle = "교환 상품 검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();

function getStateText($state){
    switch ($state){
        case '1':
            $status_text = "판매중";
            break;
        case '0':
            $status_text = "일시품절";
            break;
        case '2':
            $status_text = "판매중지";
            break;
        case '6':
            $status_text = "등록신청중";
            break;
        case '4':
            $status_text = "판매예정";
            break;
        case '5':
            $status_text = "판매종료";
            break;
        default:
            $status_text = "기타";
            break;
    }

    return $status_text;
}

function getOptionSelect($pid){
    global $db;

    $sql = "select * from shop_product_options po left join shop_product_options_detail pod on po.opn_ix = pod.opn_ix where po.pid = '".$pid."' 
    and po.option_kind = 'b' ";
    $db->query($sql);
    $options = $db->fetchall();

    $options_select = "";
    if(is_array($options) && count($options) > 0){
        $options_select .= "<select name='option_data' id='option_data_".$pid."'>";
        $options_select .= "<option value=''>선택해주세요</option>";
        foreach($options as $key=>$val){
            $stock = $val['option_stock'] - $val['option_sell_ing_cnt'];
            if($stock > 0){
                $stock_text = "(재고: ".$stock.")";
                $stock_disabled = "";
            }else{
                $stock_text = "(품절)";
                $stock_disabled = "disabled";
            }
            $options_select .= "
            <option value='".$val['id']."' ".$stock_disabled." option_gid='".$val['option_gid']."'>
             ".$val['option_div']." $stock_text
            </option>
            ";
        }
    }
    $options_select .= "</select>";

    return $options_select;
}