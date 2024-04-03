<?php
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");

ini_set('memory_limit','-1');
set_time_limit(9999999);

$P = new msLayOut("000000000000000");

$slave_mdb = new Database;
$slave_mdb->slave_db_setting();


class NaverEP{

    private $db;
    private $cdb;
    private $ep_text;
    private $file_src;

    public function __construct(){

        $this->db = new Database;
        $this->cdb = new Database;
        $this->ep_text = '';
        //$this->file_src = $_SERVER['DOCUMENT_ROOT'].'/dburl/new/naver.tsv';
        $uploadFolder = $_SERVER['DOCUMENT_ROOT'].'/data/naver_data/';
        if(!is_dir($uploadFolder)) {
            mkdir($uploadFolder,0777,true);
        }
        $this->file_src = $uploadFolder.'/naver.tsv';

        @unlink( $this->file_src );
    }

    public function startCall(){

        $total = getTotalProductListEP(); // 전체 상품 수


        $max = 10000;   // 처리 분할 크기
        $round = (int)($total / $max) + 1;

        for($i = 1; $i <= $round; $i++){
            $products = $this->getProducts($i, $max);
            if(!empty($products['list'])){
                $this->writeTxt($products['list'], $i);
            }
        }
    }

    private function getProducts($page, $max){

        $result = getProductListEP(" state='1' ", (($page-1)*$max) , $max, "");

        return array(
            'total' => $result['total'],
            'list'  => $result['products']
        );
    }

    private function writeTxt($products, $round){

        if($round == "1"){
            $ep_text .="id\ttitle\tprice_pc\tprice_mobile\tnormal_price\tlink\tmobile_link\timage_link\tadd_image_link\tcategory_name1\tcategory_name2\tcategory_name3\tcategory_name4\tnaver_category\tnaver_product_id\tcondition\timport_flag\tparallel_import\torder_made\tproduct_flag\tadult\tgoods_type\tbarcode\tmanufacture_define_number\tmodel_number\tbrand\tmaker\torigin\tcard_event\tevent_words\tcoupon\tpartner_coupon_download\tinterest_free_event\tpoint\tinstallation_costs\tpre_match_code\tsearch_tag\tgroup_id\tvendor_id\tcoordi_id\tminimum_purcgase_quantity\treview_count\tshipping\tdeliver_grade\tdeliver_detail\tattirbute\toption_detail\tseller_id\tage_group\tgender\tclass\tupdate_time\n";
        }

        $img_domain = constant("IMAGE_SERVER_DOMAIN");
        $shop_domain = "http://".$_SESSION['layout_config']['mall_domain'];
        if(empty($img_domain)){
            $img_domain = $shop_domain;
        }

        foreach($products as $val):

            if($val['is_adult'] =='1'){
                $is_adult = 'Y';
            }else{
                $is_adult = '';
            }

            $sql = "SELECT 
						delivery_policy, delivery_basic_policy, (SELECT delivery_basic_terms FROM shop_delivery_terms WHERE dt_ix = sdt.dt_ix LIMIT 1) AS delivery_basic_terms,
						(case 
							when delivery_policy = '3' and delivery_basic_policy in ('1','5') then (SELECT delivery_price FROM shop_delivery_terms WHERE dt_ix = sdt.dt_ix AND seq = '0' limit 1) 
							when delivery_policy = '4' and delivery_basic_policy in ('1','5') then sdt.delivery_cnt_price
							when delivery_basic_policy = '2' then '-1'
							else sdt.delivery_price 
						end)AS delivery_price
					FROM 
						shop_delivery_template sdt 
					WHERE 
						dt_ix = (SELECT dt_ix FROM shop_product_delivery WHERE pid = '".$val['id']."' limit 1)
						AND delivery_policy in ('2','3','4','6')
						AND delivery_basic_policy in ('1','2','5')";
            $this->db->query($sql);
            $result = $this->db->fetch();

            if(count($result) > 0 && $result['delivery_price'] != null){
                $shipping = $result['delivery_price'];
            }elseif($result['delivery_price'] == null){
                $shipping = 0;
            }else{
                $shipping = 0;
            }

            // 결제금액당 배송비가 붙을경우 상품금액을 확인하여 제한금액이상일 경우 배송비 0원 처리
            if($result['delivery_policy'] == '3' && ($result['delivery_basic_policy'] == '1' || $result['delivery_basic_policy'] == '5') && $val['dcprice'] > $result['delivery_basic_terms']){
                $shipping = 0;
            }

            $coupon_saleprice_pc = array();
            $coupon_saleprice_m = array();

            $search_keyword = str_replace(",", "|", $val['search_keyword']);
            $search_keyword = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"",$search_keyword);

            $pname = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n",";","&#39","\t","\n")," ",$val['pname']);
            $origin = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n",";","&#39","\t","\n")," ",$val['origin']);
            $company = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n",";","&#39","\t","\n")," ",$val['company']);
            $brand_name = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n",";","&#39","\t","\n")," ",$val['brand_name']);
            $shotinfo = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n",";","&#39","\t","\n")," ",$val['shotinfo']); //이벤트필드
            $shotinfo = trim(preg_replace('/\s+/', ' ', $shotinfo));
            if($shotinfo=="") $shotinfo="전 상품 무료배송 / 회원가입 즉시 10% 쿠폰 / 무통장결제 시 5% 적립";// 공통 적용 kbk 18/11/21

            $dep1 = substr($val['id'], 0,2);
            $dep2 = substr($val['id'], 2,2);
            $dep3 = substr($val['id'], 4,2);
            $dep4 = substr($val['id'], 6,2);
            $dep5 = substr($val['id'], 8,2);

            $image_dir = $dep1 .'/'. $dep2 .'/'. $dep3 .'/'. $dep4 .'/'. $dep5;
            $imgpath = $_SESSION['layout_config']['mall_product_imgpath']."/".$image_dir;

            $img_url = $img_domain."/".$imgpath."/m_".$val['id'].".gif";

            if(preg_match('/[^A-Z0-9]/',$val['pcode'])){
                $pcode = '';
            }else{
                $pcode = $val['pcode'];
            }

            $sql = "select            
			  config_value as front_url
			from
			  shop_mall_config
		    where
			mall_ix = '".$_SESSION['layout_config']['mall_ix']."'
			and config_name = 'front_url'";

            $this->db->query($sql);
            $this->db->fetch();
            $front_url = $this->db->dt['front_url'];

            $link = $front_url."/shop/goodsView/".$val[id];

            $cate_length = 15;

            /*
            $cat0 = str_pad(substr($val['cid'],0,3), $cate_length, '0', STR_PAD_RIGHT);
            $cat1 = str_pad(substr($val['cid'],0,6), $cate_length, '0', STR_PAD_RIGHT);
            $cat2 = str_pad(substr($val['cid'],0,9), $cate_length, '0', STR_PAD_RIGHT);
            $cat3 = str_pad(substr($val['cid'],0,12), $cate_length, '0', STR_PAD_RIGHT);

            if(count($coupon_saleprice_m)>0 && ! empty($coupon_saleprice_m)){
                $price_mobile = floor( ($val['dcprice']-max($coupon_saleprice_m)) / 10 ) * 10;
            }else{
                $price_mobile = $val['dcprice'];
            }

            if(count($coupon_saleprice_pc)>0 && ! empty($coupon_saleprice_pc)){
                $price_pc = floor( ($val['dcprice']-max($coupon_saleprice_pc)) / 10 ) * 10;
            }else{
                $price_pc = $val['dcprice'];
            }
            */

            // *** 네이버 EP 쿠폰의 경우 사용
            $price_mobile = $val['dcprice'];
            $price_pc = $val['dcprice'];
            $listprice = $val['listprice'];
            $ep_text .="".$val['id']."\t".$pname."\t".$price_mobile."\t".$price_pc."\t".$listprice."\t".$link."\t".$link."\t".$img_url."\t\t".$val['cat0']."\t".$val['cat1']."\t".$val['cat2']."\t".$val['cat3']."\t\t\t\t\t\t\t\t".$is_adult."\t\t\t".$pcode."\t\t".$brand_name."\t".$company."\t".$origin."\t\t".$shotinfo."\t\t\t\t\t\t\t".$search_keyword."\t\t\t\t\t".$val['after_cnt']."\t".$shipping."\t\t\t\t\t\t\t\t\t\n";

        endforeach;

        //$ep_text = iconv("UTF-8","EUC-KR//IGNORE",$ep_text);
        //$ep_text = mb_convert_encoding($ep_text, "EUC-KR" , "UTF-8");
        //$file = fopen( $_SERVER['DOCUMENT_ROOT'].'/dburl/naver_result/naver_'.str_pad($round, 3,'0',STR_PAD_LEFT).'.txt', 'w');
        $file = fopen( $this->file_src , 'a');
        fwrite($file,$ep_text);
        fclose($file);
        unset($text);
    }
}


$naver = new NaverEP();
$naver->startCall();