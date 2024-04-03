<?php
/**
 * Created by PhpStorm.
 * User: FB_sc
 * Date: 2019-06-18
 * Time: 오후 4:25
 */

//개발진행 중 중단 2019.07
class Sabangnet {

    private $path;
    private $data_path;
    private $call_url;
    private $param_url;
    private $folder_name;
    private $key;

    public function __construct() {
        $this->path = '';
        $this->call_url = '';
        $this->param_url = 'http://imagedevbarrel.forbiz.co.kr';
        $this->key = '';
        $this->folder_name = 'sabangnet';
    }

    /**
     * desc: 경로체크
     * issue : 보안때문에 data folder 밑에는 명령어로 생성불가, 수동생성
     **/
    private function checkPath(){
        if(!is_dir($this->path)){
            mkdir($this->path, 0777, true);
            chmod($this->path, 0777);
        }
    }

    /**
     * desc: 샘플읽기
     * params: api type
     **/
    private function readSample($type) {
        $sample_path = '/data'.$this->path.'/'.$this->folder_name.'/sample';
        if(is_dir($sample_path)) {
            $file = $sample_path.'/'.$type.'.xml';
            if(is_file($file)) {
                $xml = simplexml_load_file($file);
                return $xml;
            }
        }
        return '';
    }

    /**
     * desc: 사방넷 API
     * params: api type
     **/
    private function setCallUrl($type) {
        switch($type) {
            case 'insertProduct' : //상품등록&수정
                $this->call_url = 'http://r.sabangnet.co.kr/RTL_API/xml_goods_info.html?xml_url=';
                break;
            case 'searchProperty' : //상품속성코드조회
                $this->call_url = 'http://r.sabangnet.co.kr/RTL_API/xml_goods_prop_code_info.html?xml_url=';
                break;
            case 'updateOption' : //상품요약수정
                $this->call_url = 'http://r.sabangnet.co.kr/RTL_API/xml_goods_info2.html?xml_url=';
                break;
            case 'insertPackage' : //추가상품등록&수정
                $this->call_url = 'http://r.sabangnet.co.kr/RTL_API/xml_package_info.html?xml_url=';
                break;
            case 'getCategory' : //카테고리조회
                $this->call_url = 'http://r.sabangnet.co.kr/RTL_API/xml_category_info.html?xml_url=';
                break;
            case 'insertCategory' : //카테고리등록&수정
                $this->call_url = 'http://r.sabangnet.co.kr/RTL_API/xml_category_info2.html?xml_url=';
                break;
            case 'getOrder' : //주문수집
                $this->call_url = 'http://r.sabangnet.co.kr/RTL_API/xml_order_info.html?xml_url=';
                break;
            case 'insertInvoice' : //송장등록
                $this->call_url = 'http://r.sabangnet.co.kr/RTL_API/xml_order_invoice.html?xml_url=';
                break;
            case 'insertClaim' : //클레임수집
                $this->call_url = 'http://r.sabangnet.co.kr/RTL_API/xml_clm_info.html?xml_url=';
                break;
            case 'updateProductData' : //상품 쇼핑몰별 DATA 수정
                $this->call_url = 'http://r.sabangnet.co.kr/RTL_API/xml_goods_info3.html?xml_url=';
                break;
            case 'getCS' : //문의사항 수집
                $this->call_url = 'http://r.sabangnet.co.kr/RTL_API/xml_cs_info.html?xml_url=';
                break;
            case 'setCSReply' : //문의사항 답변등록
                $this->call_url = 'http://r.sabangnet.co.kr/RTL_API/xml_cs_ans.html?xml_url=';
                break;
            default :
                break;
        }
    }

    private function makeXml($sample, $data) {

    }

    /**
     * desc: 사방넷 연동
     * params: api type, data
     **/
    private function call($url) {
        $ch = curl_init ();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $response = curl_exec($ch);
        curl_close ($ch);

        $response = json_decode($response);
    }

    /**
     * desc: 경로체크
     * params: 데이터경로
     **/
    public function setPath($data_path){
        $this->path = $data_path;
    }

    /**
     * desc: 사방넷 메인
     * params: api type, data
     **/
    public function execute($type, $data) {

        if(!empty($type) && !empty($data)) {
            $this->setCallUrl($type);
            $sample = $this->readSample($type);
            echo '<pre>';
            print_r($sample);
            echo '</pre>';
            $this->makeXml($sample, $data);

            $url = $this->call_url.$this->param_url.$this->path.'/'.$this->folder_name.'/sample/'.$type.'.xml';

            return;


            return $this->call($url);
        }else {
            echo '파라미터가 누락되었습니다.';
        }
    }


}