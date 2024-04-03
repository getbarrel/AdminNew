<?
/**
 * 제휴사 상품관리 controler
 * 
 */
include("../openapi/openapi.lib.php");
include("sellertool.lib.php");

$act = $_POST['act'];

//등록 재시도
if($act == 'retry'){
    $list = $_POST['select_pid'];
    
    if(is_array($list)){
        foreach($list as $lt):
            $site_code = getSiteCodeBySeq($lt);
            //TODO:등록재시도 부분 넣기
            
        endforeach;
    }

/**
 * 제휴사 상품상태 DB업데이트 
 * ajax호출
 */
}else if($act == 'updateStatus'){
    $list = $_POST['list'];
    //print_r($list);
    $count = count($list);
    if($count >= 1){
        $result_codes = "";
        foreach($list as $lt):
            $site_code = getSiteCodeBySeq($lt);
            $targetCode = getProductCodeBySeq($lt);
            
            $result = getProductInfo($site_code,$targetCode);
            if($result->resultCode != '200'){
                $result_msg = $result->message;
                $result_codes .= 'fail';
            }   
            //print_r($result);
        endforeach;
        
        if(substr_count($result_codes,'fail')){
            echo $result_msg;
        }else{
            echo 'success';
        }
        
    }else{
        echo '선택된 상품이 없습니다.';
    }
    
    
    //echo 'success';
    //echo "<script>alert('here');</script>";
    
//등록결과 로그 삭제
}else if($act == 'delete_log'){
    $srl_ix = $_POST['srl_ix'];
    
    $result = delete_reg_log($srl_ix);
     
    if($result){
        echo 'success';
    }else{
        echo 'fail';
    }
    
//판매중지,해제
}else if($act == 'display'){
    

    $list = $_POST['select_pid'];

    $type = $_POST['display'];
    
    $_resultCodes = "";
    if(is_array($list)){
        foreach($list as $lt):
            $site_code = getSiteCodeBySeq($lt);
            $targetCode = getProductCodeBySeq($lt);
            
            if($type == 'stop'){
                $result = stopDisplay($site_code,$targetCode);
                $_resultCodes .= $result->resultCode;
                $_type = '판매중지';
            
            }else if($type == 'restart'){
                $result = restartDisplay($site_code,$targetCode);
                $_resultCodes .= $result->resultCode;
                $_type = '판매중지 해제';
                
            }else{
                //error
            }
            
        endforeach;
        
        if(substr_count($_resultCodes,"fail")){
            echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('$_type 처리 실패.');</script>");
        }else{
            echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('$_type 처리 완료.');</script>");
        }
        //아래꺼 왜안됌?
        echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
        
        exit;
    }

   
//가격수정 
}else if($act == 'price'){
    $list = $_POST['select_pid'];
    
    if(is_array($list)){
        
        foreach($list as $lt):
            $site_code = getSiteCodeBySeq($lt);
            $targetCode = getProductCodeBySeq($lt);
            
            $originPrice = getPriceBySeq($lt); //원래가격
            $editValue = $_POST['editValue']; //수정할 값
            $editType = $_POST['editType']; //원,%
            $editKind = $_POST['editKind']; //인상,인하
            
            if($editType == 'won'){
                if($editKind == 'up'){
                    $newPrice = $originPrice + $editValue;
                    
                }else if($editKind == 'down'){
                    $newPrice = $originPrice - $editValue;
                    
                }else{
                    //exception
                    $newPrice = $originPrice;
                }
                
            }else if($editType == 'percent'){
                if($editKind == 'up'){
                    $newPrice = $originPrice + ($originPrice * ($editValue / 100));
                    
                }else if($editKind == 'down'){
                    $newPrice = $originPrice - ($originPrice * ($editValue / 100));
                    
                }else{
                    //exception
                    $newPrice = $originPrice;
                }
            }else{
                //exception
                $newPrice = $originPrice;
            }
            //원단위 절삭
            $newPrice = floor($newPrice / 10) * 10;
            $result = editPrice($site_code, $targetCode, $newPrice);
            $_resultCodes .= $result->resultCode;
        endforeach;
    }
    if($_resultCodes){
        if(substr_count($_resultCodes,"fail")){
            echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('가격수정 처리 실패.');</script>");
        }else{
            echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('가격수정 처리 완료.');</script>");
        }
        
        echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
        
        exit;
    }else{
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택된 상품이 없습니다.');</script>");
        echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
    }
    

//가격+즉시할인 수정
}else if($act == 'coupon'){
    $list = $_POST['select_pid'];
    
    if(is_array($list)){
        //원 기능은 가격+즉시할인 수정이지만 가격수정은 사용하지 않는다.-> 11번가 셀러오피스 프로세스에 맞춤
        
        $coupon[cuponcheck]      = $_POST['cuponcheck'];
        $coupon[dscAmtPercnt]    = $_POST['dscAmtPercnt'];
        $coupon[cupnDscMthdCd]   = $_POST['cupnDscMthdCd'];
        $coupon[cupnUseLmtDyYn]  = $_POST['cupnUseLmtDyYn'];
        $coupon[cupnIssEndDy]    = $_POST['cupnIssEndDy'];
        print_r($coupon);
        foreach($list as $lt):
            $site_code = getSiteCodeBySeq($lt);
            $targetCode = getProductCodeBySeq($lt);
            $originPrice = getPriceBySeq($lt);
            
            $result = editPriceCoupon($site_code, $targetCode, $originPrice, $coupon);
            print_r($result);
            $_resultCodes .= $result->resultCode;
        endforeach;
    }
    /*
        array - coupon
        
        cuponcheck      = 쿠폰 사용 여부 Y: 설정함, N: 설정안함
        dscAmtPercnt    = 할인수치 (판매가에서 할인될 정율/정액 수치를 입력하세요.)
        cupnDscMthdCd   = 할인 단위코드 (정율/정액 중 선택할 할인단위를 입력하세요.) 01:할인액(원), 02:할인율(%)
        cupnUseLmtDyYn  = 쿠폰 지급기간 설정 (할인쿠폰 지급기간을 설정합니다.) Y:설정함, N:설정안함
        cupnIssEndDy    = 쿠폰지급기간 종료일 쿠폰 지급기간 시작일은 입력 불가능하며, 종료일만 입력 가능합니다.(2012/04/30)
     */
    if($_resultCodes){
        if(substr_count($_resultCodes,"fail")){
            echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('즉시할인 처리 실패.');</script>");
        }else{
            echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('즉시할인 처리 완료.');</script>");
        }
        
        echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
        
        exit;
    }else{
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택된 상품이 없습니다.');</script>");
        echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
    }

//no act    
}else if($act == 'fail_data_delete'){
	$db = new Database();
	$db2 = new Database();
	$sql = "select * from sellertool_regist_fail where pid = '".$pid."' and site_code = '".$site_code."'";
	$db->query($sql);
	if($db->total){
		for($i=0; $i < $db->total; $i++){
			$db->fetch($i);
			
			if($site_code == 'interpark_api'){
				$xml_data = str_replace('&dataUrl=http://'.$_SERVER[HTTP_HOST].'','',$db->dt[data_url]);
				//$path = $_SESSION["admininfo"]["mall_data_root"]."/openapi/interpark/xml/".$xml_data;
				$xml_data = $_SERVER["DOCUMENT_ROOT"].$xml_data;
				//echo $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/openapi/interpark/xml/UpdateProductAPIData_kgf936iufpertddmkbevrk22m7.xml";
			
				//exit;
				unlink($xml_data);
				
			}
			$sql = "delete from sellertool_regist_fail where sf_ix = '".$db->dt[sf_ix]."'";
			$db2->query($sql);

		}
	}
	 echo 'success';
	 exit;
}else if($act == 'display_check'){
	$OAL = new OpenAPI($site_code);
	$result = $OAL->lib->GetItemList($sell_id);
	

	if($result != 'fail'){
		$data = get_object_vars($result);
		$data_array = $data['@attributes'];
		exit(json_encode($data_array));
	}
	

}else{
    //nothing
}


?>