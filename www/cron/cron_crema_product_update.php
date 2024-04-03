<?php

include_once("../class/layout.class");
include_once("./crema_lib.php");


/**
 * use exmple
$crema = new CremaHandler();
$data = $crema->getBrands();
print_r($data);
 *
 */

$crema = new CremaHandler();

$sdate = date('Y-m-d 00:00:00', strtotime('-1 day'));
$edate = date('Y-m-d 23:59:59', strtotime('-1 day'));

$db->query("
    SELECT `id`, `editdate`, `pname`, `listprice`, `sellprice`, `disp`, `bimg` 
    FROM `shop_product`
    WHERE editdate BETWEEN '".$sdate."' AND '".$edate."' 
    ");

$rows = $db->fetchall();


print_r($rows);

if($rows) {

    //crema api
    $crema = new CremaHandler();

    foreach ($rows as $key => $val) {
        $cremaCate = $this->getCremaCate($val['id']);
        $param = [
            'code' => (int) $val['id']
            , 'name' => $val['pname']
            , 'url' => 'http://' . FORBIZ_BASEURL . '/shop/goodsView/' . $val['id']
            , 'org_price' => $val['listprice']
            , 'final_price' => $val['sellprice']
            , 'category_codes' => array($cremaCate)  //카테고리 arr 타입
            , 'display' => $val['disp']   //사용여부
            , 'image_url' => $val['bimg']
            , 'stock_count' => 1  // 재고있음 여부
            , 'product_options' => "[{'name': 'size', 'values': ['S', 'M']}, {'name': 'color', 'values': ['black', 'white']}]"    //jsoncode
            , 'sub_product_codes' => [] //셋트상품코드
            , 'shop_builder_created_at' => date('Y-m-d\TH:i:sO')  //datetinme ISO8601
            , 'shop_builder_updated_at' => date('Y-m-d\TH:i:sO') //datetinme ISO8601
        ];

        $data = $this->cremaModel->putProduct($param); //없으면 생성
}


    function getCremaCate($pid)
    {

        $rows = $this->qb
            ->select('pr.pid')
            ->select('p.pname')
            ->select('ci.co_no')
            ->select('ci.cid')
            ->from(TBL_SHOP_PRODUCT_RELATION . ' AS pr')
            ->join(TBL_SHOP_PRODUCT . ' AS p', 'p.id = pr.pid')
            ->join(TBL_SHOP_CATEGORY_INFO . ' AS ci', 'ci.cid = pr.cid')
            ->where('p.id', $pid)
            ->exec()
            ->getResultArray();
        //카테고리 분류 별로 3자리로 끊어서 배열형태로 넣음
        $cate_code = [];
        $cremaCate = [];
        foreach ($rows as $key => $val) {
            array_push($cate_code, substr($val['cid'], 0, 3));
            array_push($cate_code, substr($val['cid'], 3, 3));
            array_push($cate_code, substr($val['cid'], 6, 3));
            array_push($cate_code, substr($val['cid'], 9, 3));
            array_push($cate_code, substr($val['cid'], 12, 3));

            $cremaCate = $this->splite_crema_cate($cate_code);
            $cate_code = [];
        }
        return $cremaCate;
    }

    /**
     * 크리마 카테고리 배열
     * @param type $cate_code
     * @return string
     */
    function splite_crema_cate($cate_code)
    {
        $cid = "";
        $parent_catetory = [];
        $crema_cate = [];
        foreach ($cate_code as $key => $val) {
            //코드가 000 인 것은 없는 카테고리
            if ($val != "000") {
                $cid .= $val;

                $data = $this->qb
                    ->select('cid')
                    ->select('cname')
                    ->select('depth')
                    ->select('category_use')
                    ->select(' "" as parent_category_code ', false)
                    ->where('depth', $key)
                    ->like('cid', $cid, 'after')
                    ->from(TBL_SHOP_CATEGORY_INFO)
                    ->exec()
                    ->getRowArray();

                //상위 카테고리를 구하기 위해서 배열로 쌓음
                array_push($parent_catetory, $data['cid']);

                foreach ($data as $k => $v) {
                    $cid_key = $data['depth'];
                    if ($k == 'depth') {
                        $data[$k] = $v + 1;
                        //0번 이상부터 2뎁스 시작으로 상위 카테고리가 있음
                        if ($cid_key > 0) {
                            $data['parent_category_code'] = $parent_catetory[$cid_key];
                        } else {
                            $data['parent_category_code'] = null;
                        }
                    }
                    if ($k == 'category_use') {
                        if ($v == 1) {
                            $data[$k] = 'visible';
                        } else {
                            $data[$k] = 'hidden';
                        }
                    }
                }
                $crema_cate[$key] = $data;
            }
        }

        return $crema_cate;
    }

    /**
     * 크리마 카테고리 생성 API
     * @param type $crema_cate
     */
    function putCremaCate($crema_cate)
    {
        //crema api
        $this->cremaModel = new CremaHandler(['environment' => DB_CONNECTION_DIV]);
        foreach ($crema_cate as $key => $val) {
            $param = ['code' => $val['cid']
                , 'name' => $val['cname']
                , 'parent_category_id' => null
                , 'parent_category_code' => $val['parent_category_code']
                , 'status' => $val['category_use']
            ];
            $data = $this->cremaModel->putCategory($param); //없으면 생성
        }
    }

?>