/**
 * Created by moon on 2019-11-20.
 */

var UploadExcelGoodsReg_i = 0;
var p_no = new Array();

function UploadExcelGoodsImageReg(page_type){
    $('.upload_excel_infos').each(function(i){
        p_no[i] = $(this).val();
    });


    var check_array = new Array();
    $('input[name^=update_check_]:checked').each(function (){

        var value = $(this).val();
        var value_name = $(this).attr('name');
        //alert(value_name);
        check_array.push('{'+value_name+' : '+value+'}');
        //alert(value_name);
    });

    //var json = eval(check_array);
    //var json = eval("(" + check_array + ")");
    //var check_data = JSON.stringify(check_array);

    //대량수정시 체크박스 선택된건만 수정됨 2014-08-18 이학봉
    var check_data = $('input[name^=update_check_]:checked').serializeArray();

    UploadExcelGoodsRegAjax(p_no.length,UploadExcelGoodsReg_i,page_type,check_data);
}


function UploadExcelGoodsRegAjax(total_no,now_no,page_type,check_data){

    $.ajax({
        type: 'GET',
        data: {'act': 'single_goods_reg', 'p_no':p_no[now_no], 'page_type':page_type, 'check_data':check_data},
        url: './product_img_update_excel.act.php',
        dataType: 'html',
        async: true,
        beforeSend: function(){
            //$('#status_message_'+p_no[now_no]).html('상품등록 진행중...');
            $('#status_message_'+p_no[now_no]).html("상품등록 진행중...<img src='/admin/images/indicator.gif' border=0 width=20 height=20 align=absmiddle> ")
        },
        success: function(data){
            UploadExcelGoodsReg_i++;
            //alert(data);
            try{
                if(total_no > now_no){
                    $('#status_message_'+p_no[now_no]).html(data);
                    UploadExcelGoodsRegAjax(total_no,UploadExcelGoodsReg_i,page_type,check_data);
                }else{

                     if(confirm('등록완료되었습니다. 새로고침 하시겠습니까?')){
                        parent.document.location.reload();
                     }else{

                     }


                }
            }catch(e){
                alert(e.message);
            }

        } ,
        error:function(x, o, e){
            alert(x.status + " : "+ o +" : "+e);
        }
    });

}

$(document).ready(function (){


    $('#check_all').click(function (){

        var value = $(this).attr('checked');

        if(value == 'checked'){
            $('input[name^=update_check_]').attr('checked','checked');

        }else{
            $('input[name^=update_check_]').attr('checked',false);
        }

    });

});