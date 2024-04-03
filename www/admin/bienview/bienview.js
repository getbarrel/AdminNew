/**
 * Created by moon on 2017-08-02.
 */
function BienViewInput(div_depth,co_ix){
    location.href='./contents_input.php?co_ix='+co_ix+'&div_depth='+div_depth;
}

function ContentsSubmit(frm){
    if(!CheckFormValue(frm)){
        return false;
    }else{
        return true;
    }
}

function DelImg(co_ix,path){
    if(confirm('대표이미지를 삭제 하시겠습니까?')){
        window.frames['iframe_act'].location.href= './contents_input.act.php?act=img_del&co_ix='+co_ix+'&path='+path;
    }
}

function BienViewDelete(div_depth,co_ix){
    if(confirm('콘텐츠 내용을 삭제 하시겠습니까?')){
        window.frames['iframe_act'].location.href= './contents_input.act.php?act=delete&co_ix='+co_ix+'&div_depth='+div_depth;
    }
}