<?
include("sellertool.lib.php");
include("../openapi/openapi.lib.php");

$act = $_POST['act'];
$site_code = $_POST['site_code'];

if($act == 'outAddr_regist'){ //출고지 등록

    $data = "";
    $data[addrNm]          = $_POST['addrNm'];
    $data[rcvrNm]          = $_POST['rcvrNm'];
    $data[gnrlTlphnNo]     = $_POST['gnrlTlphnNo'];
    $data[prtblTlphnNo]    = $_POST['prtblTlphnNo'];
    $data[mailNO]          = str_replace("-","",$_POST['mailNO']);
    $data[mailNOSeq]       = $_POST['mailNOSeq'];
    $data[dtlsAddr]        = $_POST['dtlsAddr'];
    $data[baseAddrYN]      = $_POST['baseAddrYN'];
    
    $result = registOutAddress($site_code, $data);
    
    //return $result;
    if($result == 'success'){
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 등록되었습니다.');parent.document.location.reload();</script>");
    }else{
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('등록 실패하였습니다.');parent.document.location.reload();</script>");
    }

}else if($act == 'outAddr_update'){ // 출고지 수정
    
    $data = "";
    $data[memNo]           = $_POST['memNo'];
    $data[addrSeq]         = $_POST['addrSeq'];
    $data[addrNm]          = $_POST['addrNm'];
    $data[rcvrNm]          = $_POST['rcvrNm'];
    $data[gnrlTlphnNo]     = $_POST['gnrlTlphnNo'];
    $data[prtblTlphnNo]    = $_POST['prtblTlphnNo'];
    $data[mailNO]          = str_replace("-","",$_POST['mailNO']);
    $data[mailNOSeq]       = $_POST['mailNOSeq'];
    $data[dtlsAddr]        = $_POST['dtlsAddr'];
    $data[baseAddrYN]      = $_POST['baseAddrYN'];
    
    $result = updateOutAddress($site_code, $data);
    
    //return $result;
    if($result == 'success'){
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
    }else{
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('실패하였습니다.');parent.document.location.reload();</script>");
    }
    
}else if($act == 'inAddr_regist'){ //반품/교환지 등록
    $data = "";
    $data[addrNm]          = $_POST['addrNm'];
    $data[rcvrNm]          = $_POST['rcvrNm'];
    $data[gnrlTlphnNo]     = $_POST['gnrlTlphnNo'];
    $data[prtblTlphnNo]    = $_POST['prtblTlphnNo'];
    $data[mailNO]          = str_replace("-","",$_POST['mailNO']);
    $data[mailNOSeq]       = $_POST['mailNOSeq'];
    $data[dtlsAddr]        = $_POST['dtlsAddr'];
    $data[baseAddrYN]      = $_POST['baseAddrYN'];
    
    $result = registInAddress($site_code, $data);
    
    //return $result;
    if($result == 'success'){
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 등록되었습니다.');parent.document.location.reload();</script>");
    }else{
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('등록 실패하였습니다.');parent.document.location.reload();</script>");
    }
    
}else if($act == 'inAddr_update'){ //반품/교환지 수정
    $data = "";
    $data[memNo]           = $_POST['memNo'];
    $data[addrSeq]         = $_POST['addrSeq'];
    $data[addrNm]          = $_POST['addrNm'];
    $data[rcvrNm]          = $_POST['rcvrNm'];
    $data[gnrlTlphnNo]     = $_POST['gnrlTlphnNo'];
    $data[prtblTlphnNo]    = $_POST['prtblTlphnNo'];
    $data[mailNO]          = str_replace("-","",$_POST['mailNO']);
    $data[mailNOSeq]       = $_POST['mailNOSeq'];
    $data[dtlsAddr]        = $_POST['dtlsAddr'];
    $data[baseAddrYN]      = $_POST['baseAddrYN'];
    
    $result = updateInAddress($site_code, $data);
    
    //return $result;
    if($result == 'success'){
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
    }else{
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('실패하였습니다.');parent.document.location.reload();</script>");
    }
    
}else{
    //act가 없음
}
?>