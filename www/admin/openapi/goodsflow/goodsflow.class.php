<?php
/**
 * Created by PhpStorm.
 * User: Hyungsoo.Kim
 * Date: 2017-07-11
 * Time: 오전 10:28
 */
class Call_goodsflow {
    protected $actionUrl;

    /**
     * CALL
     *
     * @param string $actionUrl
     * @param string $postData
     * @return DomDocument
     */
    protected function call($api_key = '', $actionUrl = '', $postData = NULL , $ssl= false) {
        $this->actionUrl = $actionUrl;

        try {
            // Eos 서버 업데이트 관련 수정 요청 포비즈
			
			/*$headers = array();
            $headers[] = 'Content-Type: text/json; charset=utf-8';
            $headers[] = "goodsFLOW-Api-Key: ".$api_key."";
            $url = $this->actionUrl;
            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, $url );
            curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
            curl_setopt ( $ch, CURLOPT_POST, TRUE );
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postData );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, TRUE );

            $response = curl_exec ( $ch );

            curl_close ( $ch );*/

			$data = str_replace("\"","'",$postData);

			$url = $this->actionUrl;
			$cmd = sprintf("curl -H \"%s\" -H \"%s\" -d \"%s\" -X POST \"%s\"", 'Content-Type: text/json; charset=utf-8', 'goodsFLOW-Api-Key:'.$api_key, $data, $url);

			$response = shell_exec($cmd);

			// // Eos 서버 업데이트 관련 수정 요청 포비즈

            $response = json_decode($response);

            return $response;
        } catch ( Exception $e ) {
            echo $e->getMessages ();
        }
    }

    private function buildAuctionHeaders($api_key) {
        $headers = array (
            "Content-Type: text/xml; charset=utf-8",
            "goodsFLOW-Api-Key: $api_key"
        );

        return $headers;
    }

    /**
     * 객체의 attribute,array 를 array로 변환
     *
     * @param $object
     * @return $array
     */
    protected function myObject2Array( $object ){
        if($array == ''){
            echo "auction.lib.php > getInAddress > myObject2Array<br>auction.class.php > myObject2Array > \$object <br> 값이 안 들어옴";
            exit;
        }

        $return = array();
        foreach( $object as $key => $val ):
            $item = array();
            //attribute
            foreach( $val->attributes() as $attr_key => $attr_val ):
                $item[$attr_key] = (string)$attr_val;
            endforeach;
            //object
            foreach($val as $obj_key => $obj_val):
                $item[$obj_key] = $this->myObject2Array( $val->$obj_key ); //recursive call
            endforeach;
            array_push( $return, $item );
        endforeach;

        return $return;
    }
}