<?php

include_once("../class/layout.class");
include_once("./curl5.2.class.php");

//$db = new Database();

define('CREMA_APP_ID', 'e6248128c2f5fbb79b736a9eb132c9308da2933d786fc909ae0127bc0aa53723');
define('CREMA_SECRET', 'c11c03c9452f522387cd351de92f1f1cd3699cc3bde775a7d68a85f1d6893e94');
define('CREMA_API_URL', 'https://sapi.cre.ma');

//phpinfo();

class CremaHandler
{

    private $curl;
    protected $apiServer = 'https://sapi.cre.ma';
    protected $uri = array(
        'oauth' => '/oauth/token'
    , 'brands' => '/v1/brands'
    , 'sub_brands' => '/v1/sub_brands'
    , 'user_grades' => '/v1/user_grades'
    , 'users' => '/v1/users'
    , 'caterorys' => '/v1/categories'
    , 'products' => '/v1/products'
    , 'reviews' => '/v1/reviews'
    , 'review_code' => '/v1/comments'
    , 'cart_items' => '/v1/cart_items'
    , 'review_sms' => '/v1/review_sms'
    , 'review_biz_messages' => '/v1/review_biz_messages'
    , 'orders' => '/v1/orders'
    , 'sub_orders' => '/v1/sub_orders'
    );

    public function __construct($config = array())
    {

        //init api url
        $this->setServer(CREMA_API_URL);

        //init curl
        $this->curl = new Curl();

        //init token
        $this->initAuth();
    }

    public function setServer($server)
    {
        $this->apiServer = $server;
    }

    /**
     * api 주소 리턴
     * @param type $uri
     * @return boolean
     */
    public function getUrl($uri)
    {
        if ($uri) {
            return $this->apiServer . $this->uri[$uri];
        } else {
            return false;
        }
    }

    /**
     * 인증 엑세스
     * @return type
     */
    public function getAccess()
    {
        $url = $this->getUrl('oauth');
        $data = array('grant_type' => 'client_credentials'
        , 'client_id' => CREMA_APP_ID
        , 'client_secret' => CREMA_SECRET
        );

        return $this->curl->method('POST')->setUri($url)->params($data)->call();
    }

    /**
     * 엑세스한 것에서 토큰 값을 출력
     * 값이 없으면 error 값이 채워져서 넘어옴
     * response
     * @return json
     */
    public function getToken()
    {
        return json_decode($this->getAccess()->result);
    }

    /**
     * 공통으로 토큰 발급 받아서 셋해서 넘겨줌
     */
    public function initAuth()
    {

        $token = $this->getToken();

        $access_token = false;
        if (isset($token->{'access_token'})) {
            $access_token = $token->{'access_token'};
        } else {
            $access_token = false;
        }

        $token_type = false;
        if (isset($token->{'token_type'})) {
            $token_type = $token->{'token_type'};
        } else {
            $token_type = false;
        }
        $this->curl->httpHeader('Authorization', $token_type . " " . $access_token);;
    }

    /**
     * 해당 업체의 브랜드명 가져오기
     * response
     * ["id": , "name": "", "created_at": "2018-08-30T10:54:52.000+09:00", "updated_at": "2019-01-06T19:22:39.000+09:00"]
     * return json
     */
    public function getBrands($id = null)
    {
        $url = $this->getUrl('brands');
        if ($id) {
            $url .= "/" . $id;
        }

        $url = $this->getUrl('brands');
        return $this->result(
                    $this->curl
                        ->method('GET')
                        ->setUri($url)
                        ->call()
            );
    }

    /**
     * 주문생성
     * @param type $param
     * @return type
     */
    public function putOrder($param = array())
    {
        $url = $this->getUrl('orders');
        return $this->result(
                $this->curl
                    ->method('POST')
                    ->setUri($url)
                    ->params($param)
                    ->call()
        );

    }

    /**
     * 주문 리스트 상세 아이템 생성
     * @param type $param
     * @return type
     */
    public function putSubOrder($param = array())
    {
        $url = $this->getUrl('sub_orders');
        return $this->result(
            $this->curl
                ->method('POST')
                ->setUri($url)
                ->params($param)
                ->call()
        );
    }

    /**
     * 상세 주문 정보 수정
     * @param array $param
     * @return Curl
     * @throws Exception
     */
    public function postSubOrder($param = array())
    {
        $url = $this->getUrl('orders');
        $url =  $url . '/'.$param['order_id'].'/sub_orders';
        //return $this->result($this->curl->post($url, $param));
        return            $this->curl
                ->method('POST')
                ->setUri($url)
                ->params($param)
                ->call();

    }


    /**
     * json decode
     * @param type $obj
     * @return type
     */
    public function result($obj)
    {
        if (isset($obj->result) OR $obj->result) {
            return json_decode($obj->result, true);
        } else {
            return $obj;
        }

    }
}


/**
 * use exmple
$crema = new CremaHandler();
$data = $crema->getBrands();
print_r($data);
 *
 */

?>