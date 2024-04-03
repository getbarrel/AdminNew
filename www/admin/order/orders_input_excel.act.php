<?php
include($_SERVER["DOCUMENT_ROOT"] . "/admin/class/layout.class");
include '../include/phpexcel/Classes/PHPExcel.php';
include './excel_columsinfo.php';
include '../inventory/inventory.lib.php';

if ($_SESSION["admininfo"]["company_id"] == "") {
    echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='../'</script>";
    //'관리자 로그인후 사용하실수 있습니다.'
    exit;
}

$db = new Database;
function pr($data = array(), $exit = true)
{
    echo '<hr><pre>';
    print_r($data);
    echo '<hr></pre>';
    if ($exit) exit;
}

/**
 * 처음 파일로 떨굴때... 일종의 폼을 파일로 만드는 곳.
 */
if ($act == "new_excel_input") {    //대량상품등록 엑셀정보 저장

    if ($excel_file_size > 0) {
        copy($excel_file, $_SERVER["DOCUMENT_ROOT"] . "" . $admin_config[mall_data_root] . "/images/upfile/" . $excel_file_name);
    }

    date_default_timezone_set('Asia/Seoul');

    $objPHPExcel = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"] . "" . $admin_config[mall_data_root] . "/images/upfile/" . $excel_file_name);

    $sql = "select * from shop_order_excel_template where oet_ix = '" . $oet_ix . "'";
    $db->query($sql);
    $db->fetch();

    $columsinfo = unserialize(urldecode($db->dt["oet_array"]));

    // 데이터는 정보 없으면 2줄부터 시작
    if ($db->dt["oet_line"]) {
        $rownum = $db->dt["oet_line"];
    } else {
        $rownum = 2;
    }

    $upload_excel_data = "";
    $wemakeprice_options_bool = false;
    $tmon_options_bool = false;
    for ($i = 0, $col = 'A'; $i < count($columsinfo); $i++, $col++) {
        if ($columsinfo[$col]["code"] != "DEFAULT") {
            $upload_excel_data[session_id()][0][$columsinfo[$col]["code"]] = $columsinfo[$col]["text"];
        }

        //기준 컬럼
        if ($columsinfo[$col]["code"] == "co_oid") {
            $basic_col = $col;
        }
    }

    $upload_excel_data[session_id()][0]["order_from"] = "제휴사";
    $upload_excel_data[session_id()][0]["inventory_text"] = "품목정보";
    $upload_excel_data[session_id()][0]["gid"] = "품목코드";
    $upload_excel_data[session_id()][0]["gu_ix"] = "품목단위시스템코드";

    $y = 1;
    $z = 0;

    //pr($objPHPExcel->getActiveSheet()->getCell($basic_col . $rownum));
    //pr($columsinfo);

    while (($objPHPExcel->getActiveSheet()->getCell($basic_col . $rownum)->getValue() != "") && ($rownum < 5000)) {
        $wemakeprice_options = "";
        for ($i = 0, $col = 'A'; $i < count($columsinfo); $i++, $col++) {
            if ($columsinfo[$col]["code"] != "DEFAULT") {
                //PHPExcel_RichText
                if (is_object($objPHPExcel->getActiveSheet()->getCell($col . $rownum)->getValue())) {
                    $objRichText = new PHPExcel_RichText($objPHPExcel->getActiveSheet()->getCell($col . $rownum));
                    $upload_excel_data[session_id()][$y][$columsinfo[$col]["code"]] = $objRichText->getPlainText();
                } else {
                    $upload_excel_data[session_id()][$y][$columsinfo[$col]["code"]] = $objPHPExcel->getActiveSheet()->getCell($col . $rownum)->getValue();
                }
            }
        }

        //커스텀 마이징 데이터 변경
        $order_from = $upload_excel_data[session_id()][$y]['order_from'];
        $gid = $upload_excel_data[session_id()][$y]['gid'];
        $pcnt = $upload_excel_data[session_id()][$y]['pcnt'];

        $addRow = array();

        $upload_excel_data[session_id()][$y]['co_oid'] = str_replace(array('_사은품'), '', $upload_excel_data[session_id()][$y]['co_oid']);

        $multiply_qty = 1;
        $division_count = 1;

        if (substr($gid, 0, 8) == 'DEWYTREE') {
            $sql = "select gid, qty from dewytree_product_linked where site_code='" . $order_from . "' and sg_code='" . $gid . "' ";
            $db->query($sql);
            if ($db->total > 0) {
                if ($db->total > 1) {
                    $linkeds = $db->fetchall('object');
                    $division_count = count($linkeds);
                    foreach ($linkeds as $key => $link) {
                        if ($key == 1) {
                            $gid = $link['gid'];
                            $multiply_qty = $link['qty'];
                        } else {
                            $addRow[] = array('gid' => $link['gid'], 'multiply_qty' => $link['qty']);
                        }
                    }
                } else {
                    $db->fetch();
                    $gid = $db->dt['gid'];
                    $multiply_qty = $db->dt['qty'];
                }
            }
        }

        order_excel_inventory_data($gid, $multiply_qty, $division_count);

        $y++;
        $z++;

        if (count($addRow) > 0) {
            foreach ($addRow as $key => $row) {
                //이전 데이터 복사
                $upload_excel_data[session_id()][$y] = $upload_excel_data[session_id()][($y - 1)];
                //가격은 나눈걸 복사 하기 때문에 0으로 넘김
                order_excel_inventory_data($row['gid'], $row['multiply_qty'], 0);

                $co_od_ix = $upload_excel_data[session_id()][$y]['co_od_ix'];
                $_co_od_ix = explode('_', $co_od_ix);
                if (count($_co_od_ix) > 1) {
                    unset($_co_od_ix[(count($_co_od_ix) - 1)]);
                }
                $upload_excel_data[session_id()][$y]['co_od_ix'] = implode('', $_co_od_ix) . '_' . ($key + 1);
                $y++;
                $z++;
            }
        }

        $rownum++;
    }

    include("../logstory/class/sharedmemory.class");
    $shmop = new Shared("upload_orders_excel_data_" . $_SESSION["admininfo"]["company_id"]);
    $shmop->filepath = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/_shared/";
    $shmop->SetFilePath();
    $shmop->setObjectForKey($upload_excel_data, "upload_orders_excel_data_" . $_SESSION["admininfo"]["company_id"]);
    echo "<script language='javascript' src='../js/message.js.php'></script><script>top.location.href='./orders_input_excel.php'</script>";
    exit;
}

function order_excel_inventory_data($gid, $multiply_qty, $division_count)
{
    global $ITEM_UNIT, $db, $z, $y, $upload_excel_data;

    $sql = "select g.gname, g.standard, gu.gu_ix, gu.gid, gu.unit from inventory_goods_unit gu , inventory_goods g where gu.gid=g.gid and gu.gid='" . $gid . "' and gu.unit='1' ";
    $db->query($sql);
    if ($db->total) {
        $db->fetch();
        $gu_ix = $db->dt[gu_ix];
        $gid = $db->dt[gid];
        $standard = $db->dt[standard];
        $gname = $db->dt[gname];
        $unit_text = $ITEM_UNIT[$db->dt[unit]];

        $optiontext_1 = $upload_excel_data[session_id()][$y]['optiontext_1'];
        $_option = explode('▶', $optiontext_1);
        if (count($_option) > 1) {
            unset($_option[(count($_option) - 1)]);
        }
        $upload_excel_data[session_id()][$y]['optiontext_1'] = implode('', $_option) . ' ▶' . $gname . '(' . $gid . ')';
        $upload_excel_data[session_id()][$y]['pcnt'] = $upload_excel_data[session_id()][$y]['pcnt'] * $multiply_qty;

        if (!($upload_excel_data[session_id()][$y]['pcnt'] > 0)) {
            $upload_excel_data[session_id()][$y]['pcnt'] = 1;
        }

        if ($division_count > 0) {
            $upload_excel_data[session_id()][$y]['product_pt_price'] = round($upload_excel_data[session_id()][$y]['product_pt_price'] / $division_count);
        }

        $upload_excel_data[session_id()][$y]["inventory_text"] = "<span class='blue'>" . $gid . " [" . $unit_text . "]  &nbsp;&nbsp; 
        <span class='red' style='cursor:pointer;' onclick=\"ShowModalWindow('../inventory/inventory_search.php?type=excel_input_order&no=" . $z . "',1000,680,'inventory_search')\">변경</span><br/>" . $gname . ($standard ? "<br/>▶" . $standard : "") . "</span>";
        $upload_excel_data[session_id()][$y]["gid"] = $gid;
        $upload_excel_data[session_id()][$y]["gu_ix"] = $gu_ix;
    } else {
        $upload_excel_data[session_id()][$y]["inventory_text"] = "<span class='red' style='cursor:pointer;' onclick=\"ShowModalWindow('../inventory/inventory_search.php?type=excel_input_order&no=" . $z . "',1000,680,'inventory_search')\">품목연결안됨</span>";
        $upload_excel_data[session_id()][$y]["gid"] = "";
        $upload_excel_data[session_id()][$y]["gu_ix"] = "";
    }

    $upload_excel_data[session_id()][$y]["o_no"] = $z;
}

if ($act == "single_orders_update") {

    include("../logstory/class/sharedmemory.class");
    $shmop = new Shared("upload_orders_excel_data_" . $_SESSION["admininfo"]["company_id"]);
    $shmop->filepath = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admininfo"]["mall_data_root"] . "/_shared/";
    $shmop->SetFilePath();
    $upload_excel_data = $shmop->getObjectForKey("upload_orders_excel_data_" . $_SESSION["admininfo"]["company_id"]);

    $select_excel_orders_infos = filter_by_value($upload_excel_data[session_id()], 'o_no', $o_no);

    if (count($select_excel_orders_infos) > 0) {
        foreach ($select_excel_orders_infos as $key => $value) {

            $optiontext_1 = $upload_excel_data[session_id()][$key]['optiontext_1'];
            $_option = explode('▶', $optiontext_1);
            if (count($_option) > 1) {
                unset($_option[(count($_option) - 1)]);
            }
            $upload_excel_data[session_id()][$key]['optiontext_1'] = implode('', $_option) . ' ▶' . $gname . '(' . $gid . ')';

            $upload_excel_data[session_id()][$key]["inventory_text"] = "<span class='blue'>" . $gid . " [" . $unit_text . "]  &nbsp;&nbsp; 
			<span class='red' style='cursor:pointer;' onclick=\"ShowModalWindow('../inventory/inventory_search.php?type=excel_input_order&no=" . $o_no . "',1000,680,'inventory_search')\">변경</span><br/>" . $gname . ($standard ? "<br/>▶" . $standard : "") . "</span>";
            $upload_excel_data[session_id()][$key]["gid"] = $gid;
            $upload_excel_data[session_id()][$key]["gu_ix"] = $gu_ix;

        }

        echo $upload_excel_data[session_id()][$key]["inventory_text"];
    } else {
        echo "실패 하였습니다. 관리자에게 문의 바랍니다.";
    }

    $shmop->setObjectForKey($upload_excel_data, "upload_orders_excel_data_" . $_SESSION["admininfo"]["company_id"]);
    exit;
}

/**
 * 파일로 만들어진 폼 내용을 DB에 저장 하는 곳.
 */
if ($act == "single_orders_reg" && strlen($o_no) > 0) {

    include("../logstory/class/sharedmemory.class");
    $shmop = new Shared("upload_orders_excel_data_" . $_SESSION["admininfo"]["company_id"]);
    $shmop->filepath = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admininfo"]["mall_data_root"] . "/_shared/";
    $shmop->SetFilePath();
    $upload_excel_data = $shmop->getObjectForKey("upload_orders_excel_data_" . $_SESSION["admininfo"]["company_id"]);

    $select_excel_orders_infos = filter_by_value($upload_excel_data[session_id()], 'o_no', $o_no);

    foreach ($select_excel_orders_infos as $key => $value) {

        $result_massage = "";
        $insert_yn = true;

        foreach ($value as $_key => $_value) {
            $$_key = trim(str_replace("'", "&#39;", $_value));
        }

        if ($upload_excel_data[session_id()][$key][status] != 'C') {
            if (!empty($gu_ix)) {


                $oid = '';    //주문번호 초기화 ???

                ///////DEFAULT///////
                $buyer_type = "1";//1:소매,2:도매
                $sex = "D";
                $status = ORDER_STATUS_INCOM_COMPLETE;
                $payment_agent_type = "W";
                $cid = "";
                $pid = "";
                $pcode = $gu_ix;
                $product_type = 0;
                $stock_use_yn = "Y";
                $nPaymethod = ORDER_METHOD_BANK;

                if ($pname_1) {
                    $pname = $pname_1;
                } elseif ($pname_2) {
                    $pname = $pname_2;
                } elseif ($pname_3) {
                    $pname = $pname_3;
                }

                if ($optiontext_1) {
                    $option_text = $optiontext_1;
                } elseif ($optiontext_2) {
                    $option_text = $optiontext_2;
                }

                if ($zip_1 != "") {
                    $rzip = substr($zip_1, 0, 3) . "-" . substr($zip_1, 3, 3);
                } else {
                    $rzip = $zip_2;
                }

                if ($addr_1 != "" || $addr_2 != "") {
                    $addr1 = $addr_1;
                    $addr2 = $addr_2;
                } else {
                    $addr1 = $addr;
                }

                //정산 수수료설정
                $one_commission = "N";
                $commission = 0;

                $delivery_type = "1";//통합배송여부 1:통합배송, 2:입점업체배송
                $delivery_package = "N";//개별배송 사용유무 Y:개별배송 N:묶음배송
                $delivery_policy = "2";//1:무료배송 2:고정배송비 3:주문결제금액 할인 4:수량별할인 5:출고지별 배송비 6: 상품1개단위 배송비
                $delivery_method = "1";//배송방법(1:택배,2:화물,3:직배송,4:방문수령)
                $delivery_pay_method = "1";//배송결제 방법	(1.선불 2. 착불)
                $delivery_addr_use = "0";//출고지별 배송비 사용 1:사용 0:미사용
                ///////DEFAULT///////

                $sql = "select count(*) as total,oid,od_ix from shop_order_detail where order_from ='" . $order_from . "' and co_oid='" . $co_oid . "' and co_od_ix='" . $co_od_ix . "' ";
                $db->query($sql);
                $db->fetch();
                $oid = $db->dt["oid"];
                $od_ix = $db->dt["od_ix"];
                //주문이 없을때
                if (!$db->dt[total]) {

                    $sql = "select 
						g.b_ix,g.brand,gu.barcode,g.ci_ix,gu.buying_price,gu.sellprice,g.admin,g.surtax_div,
						csd.account_type,csd.account_info,csd.ac_delivery_type,csd.ac_expect_date,csd.account_method,
						(select com_name from common_company_detail where company_id=g.admin) as company_name,
						(select com_name from common_company_detail where company_id=g.ci_ix) as trade_company_name
					from 
						inventory_goods_unit gu , inventory_goods g left join common_seller_delivery csd on (csd.company_id=g.admin)
					where 
						gu.gid=g.gid and gu.gu_ix='" . $gu_ix . "' ";
                    $db->query($sql);
                    $db->fetch();

                    $brand_code = $db->dt["b_ix"];
                    $brand_name = $db->dt["brand"];
                    $barcode = $db->dt["barcode"];
                    $trade_company = $db->dt["ci_ix"];
                    $trade_company_name = $db->dt["trade_company_name"];

                    $co_product_price = ereg_replace("[^0-9+]", "", $co_product_price);
                    if ($co_product_price > 0) {//co_product_price : 제휴사(연동)정산상품금액
                        $coprice = $co_product_price;
                    } else {
                        $coprice = $db->dt["buying_price"];
                    }

//                    $listprice = $db->dt["sellprice"];
//                    $psprice = $listprice;
//                    $dcprice = $listprice;
//                    $ptprice = $dcprice * $pcnt;
//                    $pt_dcprice = $ptprice;
                    $pt_dcprice = $product_pt_price;
                    $dcprice = round($pt_dcprice / $pcnt);
                    $psprice = $dcprice;
                    $listprice = $dcprice;
                    $ptprice = $pt_dcprice;
                    $company_id = $db->dt["admin"];
                    $company_name = $db->dt["company_name"];
                    $ori_company_id = $HEAD_OFFICE_CODE;

                    if ($db->dt["surtax_div"] == "4") {
                        $surtax_yorn = "Y";
                    } else {
                        $surtax_yorn = "N";
                    }

                    if ($company_id == $HEAD_OFFICE_CODE) {
                        $account_type = "3";//정산방식 1:수수료 2:매입(공급가로 정산) 3:미정산
                        $account_info = "1";//정산 설정1 : 기간별 2:상품별
                        $ac_delivery_type = ORDER_STATUS_DELIVERY_ING;//정산기준상태
                        $ac_expect_date = "3";//정산예정일
                        $account_method = ORDER_METHOD_CASH;//정산지급방식 현금:10 예치금 :12
                    } else {
                        $account_type = "2";//정산방식 1:수수료 2:매입(공급가로 정산) 3:미정산
                        $account_info = $db->dt["account_info"];//정산 설정1 : 기간별 2:상품별
                        $ac_delivery_type = $db->dt["ac_delivery_type"];//정산기준상태
                        $ac_expect_date = $db->dt["ac_expect_date"];//정산예정일
                        $account_method = $db->dt["account_method"];//정산지급방식 현금:10 예치금 :12
                    }

                    $co_delivery_price = ereg_replace("[^0-9+]", "", $co_delivery_price);
                    if ($co_delivery_price > 0) {
                        $delivery_price = $co_delivery_price;
                    } else {
                        $delivery_price = 0;
                    }

                    $delivery_dcprice = $delivery_price;

                    $org_product_price = $pt_dcprice;
                    $product_price = $org_product_price;
                    $total_price = $product_price + $delivery_dcprice;
                    $payment_price = $total_price;

                    $tax_price = $payment_price;
                    $tax_free_price = 0;

                    $sql = " select count(*) as total, oid from shop_order_detail where order_from ='" . $order_from . "' and co_oid='" . $co_oid . "' ";
                    //echo nl2br($sql)."<br><br>";

                    $db->query($sql);
                    $db->fetch();
                    $oid = $db->dt["oid"];
                    //echo nl2br($oid)."<br><br>";
                    if ($db->dt["total"] == '0') {
                        sleep('1');                //아래 rand(10000, 99999)가 중복이 되어서 새로운 주문일경우 1초 쉬엇다 가기
                        $oid = make_shop_order_oid();
                        //echo nl2br($oid)."<br><br>";
                        $sql = "insert into " . TBL_SHOP_ORDER . "
						(oid, buyer_type, buserid, bname, sex, btel, bmobile, bmail, order_date, static_date, status, user_ip, user_agent, payment_agent_type, org_delivery_price, delivery_price, org_product_price, product_price, total_price,payment_price)
						values
						('" . $oid . "','" . $buyer_type . "','" . $buserid . "','" . $bname . "','" . $sex . "','" . $btel . "','" . $bmobile . "','" . $bmail . "',NOW()," . date("Ymd") . ",'" . $status . "','" . $_SERVER["REMOTE_ADDR"] . "','" . $_SERVER["HTTP_USER_AGENT"] . "','" . $payment_agent_type . "','" . $delivery_price . "','" . $delivery_price . "','" . $org_product_price . "','" . $product_price . "','" . $total_price . "','" . $payment_price . "')";
                        $db->query($sql);

                        table_order_payment_data_creation($oid, 'G', $status, $nPaymethod, $tax_price, $tax_free_price, $payment_price, "");
                        set_order_status($oid, $status, "엑셀주문등록", "시스템(등록자:" . $_SESSION["admininfo"]["charger"] . ")", $od_ix);

                    } else {
                        $sql = "UPDATE " . TBL_SHOP_ORDER . " SET
							org_delivery_price = org_delivery_price + '" . $delivery_price . "',
							delivery_price = delivery_price + '" . $delivery_price . "',
							org_product_price = org_product_price + '" . $org_product_price . "',
							product_price = product_price + '" . $product_price . "',
							total_price = total_price + '" . $total_price . "',
							payment_price = payment_price + '" . $payment_price . "'
						where oid='" . $oid . "' ";
                        $db->query($sql);

                        $sql = "UPDATE shop_order_payment SET
							tax_price = tax_price + '" . $tax_price . "',
							tax_free_price = tax_free_price + '" . $tax_free_price . "',
							payment_price = payment_price + '" . $payment_price . "'
						where oid='" . $oid . "' ";
                        $db->query($sql);
                    }

                    $sql = " select count(*) as total, ode_ix from shop_order_delivery where oid ='" . $oid . "' and ori_company_id='" . $ori_company_id . "' ";
                    $db->query($sql);
                    $db->fetch();
                    $ode_ix = $db->dt["ode_ix"];

                    if (!$db->dt["total"]) {

                        $sql = "insert into shop_order_delivery (ode_ix,oid,company_id,ori_company_id,delivery_type,delivery_package,delivery_policy,delivery_method,delivery_pay_type,delivery_addr_use,pid,delivery_price,delivery_dcprice,regdate) values ('','" . $oid . "','" . $company_id . "','" . $ori_company_id . "','" . $delivery_type . "','" . $delivery_package . "','" . $delivery_policy . "','" . $delivery_method . "','" . $delivery_pay_method . "','" . $delivery_addr_use . "','" . $pid . "','" . $delivery_price . "','" . $delivery_dcprice . "',NOW())";
                        $db->query($sql);
                        $ode_ix = $db->insert_id();

                        table_order_price_data_creation($oid, '', $company_id, 'G', 'D', $delivery_dcprice, $delivery_dcprice, "", 0, 0, 0);
                    } else {
                        if ($delivery_price > 0) {
                            $sql = "update shop_order_delivery set 
								delivery_price = delivery_price + '" . $delivery_price . "',
								delivery_dcprice = delivery_dcprice + '" . $delivery_dcprice . "'
							where ode_ix='" . $ode_ix . "' ";
                            $db->query($sql);
                            table_order_price_data_creation($oid, '', $company_id, 'G', 'D', $delivery_dcprice, $delivery_dcprice, "", 0, 0, 0);
                        }
                    }

                    $sql = "select * from shop_order_detail_deliveryinfo where oid='" . $oid . "' and addr1='" . $addr1 . "' and addr2='" . $addr2 . "' ";
                    $db->query($sql);
                    if ($db->total) {
                        $db->fetch();
                        $odd_ix = $db->dt[odd_ix];
                    } else {
                        $sql = "insert into shop_order_detail_deliveryinfo (odd_ix,oid,od_ix,order_type,rname,rtel,rmobile,rmail,zip,addr1,addr2,regdate) values('','" . $oid . "','','1','" . $rname . "','" . $rtel . "','" . $rmobile . "','" . $rmail . "','" . $rzip . "','" . $addr1 . "','" . $addr2 . "',NOW())";
                        $db->query($sql);

                        if ($db->dbms_type == "oracle") {
                            $odd_ix = $db->last_insert_id;
                        } else {
                            $odd_ix = $db->insert_id();
                        }
                    }

                    /*
                    $sql = "select
                                    IFNULL((select id from shop_product where pcode = '".$gu_ix."' and stock_use_yn='Y' limit 0,1),(select pid from shop_product_options_detail where option_code = '".$gu_ix."' limit 0,1)) as pid
                                from
                                    inventory_goods_unit
                                where
                                    gu_ix = '".$gu_ix."'";
                    $db->query($sql);
                    $db->fetch();
                    $product_id = $db->dt[pid];

                    $pid = zerofill($product_id);
                    */

                    $sql = "insert into " . TBL_SHOP_ORDER_DETAIL . "
					(od_ix,mall_ix,oid,order_from,buyer_type,cid,pid,brand_code,brand_name,pcode,barcode,product_type,pname,gid,gu_ix,trade_company,trade_company_name,option_text,pcnt,coprice,listprice,psprice,dcprice,ptprice,odd_ix,company_id,company_name,one_commission,commission,surtax_yorn,stock_use_yn,regdate,delivery_type,account_type,delivery_package,account_info,ac_delivery_type,ac_expect_date,account_method,pt_dcprice,delivery_policy,delivery_method,delivery_pay_method,ori_company_id,delivery_addr_use,msgbyproduct,status,co_oid,co_od_ix,co_delivery_no,ode_ix,ic_date)
					values
					('','" . $_SESSION["admininfo"]["mall_ix"] . "','" . $oid . "','" . $order_from . "','" . $buyer_type . "','" . $cid . "','" . $pid . "','" . $brand_code . "','" . $brand_name . "','" . $pcode . "','" . $barcode . "','" . $product_type . "','" . $pname . "','" . $gid . "','" . $gu_ix . "','" . $trade_company . "','" . $trade_company_name . "','" . $option_text . "','" . $pcnt . "','" . $coprice . "','" . $listprice . "','" . $psprice . "','" . $dcprice . "','" . $ptprice . "','" . $odd_ix . "','" . $company_id . "','" . $company_name . "','" . $one_commission . "','" . $commission . "','" . $surtax_yorn . "','" . $stock_use_yn . "',NOW(),'" . $delivery_type . "','" . $account_type . "','" . $delivery_package . "','" . $account_info . "','" . $ac_delivery_type . "','" . $ac_expect_date . "','" . $account_method . "','" . $pt_dcprice . "','" . $delivery_policy . "','" . $delivery_method . "','" . $delivery_pay_method . "','" . $ori_company_id . "','" . $delivery_addr_use . "','" . trim($msg) . "','" . $status . "','" . $co_oid . "','" . $co_od_ix . "','" . $co_delivery_no . "','" . $ode_ix . "',NOW())";
                    $db->sequences = "SHOP_ORDER_DT_SEQ";
                    $db->query($sql);

                    if ($db->dbms_type == "oracle") {
                        $od_ix = $db->last_insert_id;
                    } else {
                        $od_ix = $db->insert_id();
                    }

                    //추가 필드 삽입 -- 사방넷, 외부몰 용도
                    $db_data = array("sodd_code" => $od_ix,
                        "sodda_id" => null,
                        "sbmall_pr_cd" => $sbmall_pr_cd,
                        "sbmall_od_cd" => $sbmall_od_cd,
                        "sbmall_be_dt" => $sbmall_be_dt,
                        "exmall_vat_fg" => $exmall_vat_fg,
                        "exmall_umat_fg" => $exmall_umat_fg,
                        "exmall_tr_co" => $exmall_tr_co,
                        "exmall_so_fg" => $exmall_so_fg,
                        "exmall_shipreq_dt" => $exmall_shipreq_dt,
                        "exmall_mgmt_cd" => $exmall_mgmt_cd,
                        "exmall_exch_cd" => $exmall_exch_cd,
                        "exmall_due_dt" => $exmall_due_dt);

                    insert_ex_cel(&$db, $db_data);

                    table_order_price_data_creation($oid, '', '', 'G', 'P', $product_price, $product_price, "", 0, 0, 0);


                    $sql = "select od.id as opnd_ix ,pid from " . TBL_SHOP_PRODUCT . " p inner join " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od on (p.id=od.pid) where p.stock_use_yn='Y' and option_code = '" . $gu_ix . "' ";
                    $db->query($sql);
                    if ($db->total) {
                        $option_dt_info = $db->fetchall("object");
                        for ($j = 0; $j < count($option_dt_info); $j++) {
                            $db->query("update " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " set option_sell_ing_cnt = option_sell_ing_cnt + '" . $pcnt . "' where id = '" . $option_dt_info[$j][opnd_ix] . "' ");
                        }

                        $sql = "update " . TBL_SHOP_PRODUCT . " p set p.sell_ing_cnt = (select sum(option_sell_ing_cnt) from " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od where od.pid=p.id) where p.pcode ='" . $val[gu_ix] . "' and p.stock_use_yn='Y' ";
                        $db->query($sql);
                    }

                    $db->query("update " . TBL_SHOP_PRODUCT . " set sell_ing_cnt = sell_ing_cnt + '" . $pcnt . "', order_cnt = order_cnt + '" . $pcnt . "' where pcode ='$gu_ix' and stock_use_yn='Y' ");

                    $db->query("update inventory_goods_unit set sell_ing_cnt = sell_ing_cnt + '" . $pcnt . "', order_cnt = order_cnt + '" . $pcnt . "' where gu_ix ='$gu_ix' ");

                    //real_lack_stock update
                    if ($gu_ix) {

                        $sql = "select real_lack_stock from shop_order_detail  where gu_ix = '" . $gu_ix . "' and status in ('IR','IC','DR','DD') and oid !='" . $oid . "' order by regdate desc limit 0,1";
                        $db->query($sql);
                        if ($db->total) {
                            $db->fetch();

                            $item_stock_sum = $db->dt[real_lack_stock];
                        } else {
                            $sql = "select sum(ps.stock) as stock
							from inventory_goods_unit gu  left join inventory_product_stockinfo ps on (ps.unit = gu.unit and ps.gid=gu.gid)
							where gu.gu_ix = '" . $gu_ix . "' ";
                            $db->query($sql);
                            $db->fetch();

                            $item_stock_sum = $db->dt[stock];
                        }

                        $sql = "select od_ix, pcnt from shop_order_detail  where oid='" . $oid . "' and gu_ix = '" . $gu_ix . "'";
                        $db->query($sql);

                        if ($db->total) {
                            $od_info = $db->fetchall("object");

                            $real_lack_stock = $item_stock_sum;

                            for ($j = 0; $j < count($od_info); $j++) {
                                $real_lack_stock -= $od_info[$j][pcnt];
                                $sql = "update shop_order_detail set real_lack_stock='" . $real_lack_stock . "' where od_ix='" . $od_info[$j][od_ix] . "' ";
                                $db->query($sql);
                            }
                        }
                    }


                } else {
                    $insert_yn = false;
                    $result_massage .= "<span class='red'>기존에 등록한 주문이 이미 존재합니다.<br/>주문번호 : " . $oid . "<br/>주문상세번호 : " . $od_ix . "</span><br/>";
                }

            } else {
                $insert_yn = false;
                $result_massage .= "<span class='red'>품목연결이 안되었습니다.</span><br/>";
            }

        } else {
            $insert_yn = false;
            echo "<span class='blue'>PASS</span>";
        }

        if ($insert_yn) {
            $upload_excel_data[session_id()][$key][status] = "C";
            $upload_excel_data[session_id()][$key][status_message] = "등록완료";
            echo "<span class='blue'>등록완료</span>";
        } else {
            echo $result_massage;
        }
    }

    $shmop->setObjectForKey($upload_excel_data, "upload_orders_excel_data_" . $_SESSION["admininfo"]["company_id"]);
    exit;
}

/**
 * 추가 엑셀 데이터 삽입
 * @param $db_datad
 */
function insert_ex_cel($db, $db_data)
{

    $sql = "insert into  shop_order_excel_ex_info ";
    $keys = "";
    $vals = "";
    foreach ($db_data as $key => $val) {
        $keys .= "`" . $key . "`,";
        if (!$val && $val !== 0) {
            $vals .= "NULL,";
        } else {
            $vals .= "'" . $val . "',";
        }

    }
    $keys = substr($keys, 0, -1);
    $vals = substr($vals, 0, -1);

    $sql .= "(" . $keys . ") values ";
    $sql .= "(" . $vals . ")";

    $db->query($sql);
}

/*
if($act == "bad_orders_info_excel"){
	include("../logstory/class/sharedmemory.class");
	$shmop = new Shared("upload_orders_excel_data_".$_SESSION["admininfo"]["company_id"]);
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_shared/";
	//echo $shmop->filepath;
	$shmop->SetFilePath();
	$upload_excel_data = $shmop->getObjectForKey("upload_orders_excel_data_".$_SESSION["admininfo"]["company_id"]);

	$title_info=array();
	$bad_info_excel_data=array();

	foreach($upload_excel_data[session_id()] as $key => $val){
		if($key==0){
			$title_info=$val;
		}elseif($val[status]!="C"){
			array_push($bad_info_excel_data,$val);
		}
	}

	if(count($bad_info_excel_data) > 0){

		PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

		date_default_timezone_set('Asia/Seoul');

		$etc_excel = new PHPExcel();

		// 속성 정의
		$etc_excel->getProperties()->setCreator("포비즈 코리아")
									 ->setLastModifiedBy("Mallstory.com")
									 ->setTitle("etc code List")
									 ->setSubject("etc code List")
									 ->setDescription("generated by forbiz korea")
									 ->setKeywords("mallstory")
									 ->setCategory("etc code List");

		$etc_excel->setActiveSheetIndex(0);
		$etc_excel->getActiveSheet()->setTitle('등록실패품목');

		$col = 'A';
		foreach($title_info as $key => $val){
			$etc_excel->getActiveSheet()->mergeCells($col . "1:". $col. "4");
			$etc_excel->getActiveSheet()->setCellValue($col . "1", $val);
			//$etc_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
			$col++;
		}
		

		for($i=0,$z=5;$i<count($bad_info_excel_data);$i++,$z++){
			$col = 'A';
			
			foreach($title_info as $key => $val){
				if($key == "category"){
					$etc_excel->getActiveSheet()->getStyle($col . $z)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT);
					$etc_excel->getActiveSheet()->setCellValue($col . $z , " ".$bad_info_excel_data[$i][$key]);
				}else{
					$etc_excel->getActiveSheet()->setCellValue($col . $z , $bad_info_excel_data[$i][$key]);
				}

				$col++;
			}
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=bad_orders_info_excel.xls');
		header('Cache-Control: max-age=0');

		$etc_excel = PHPExcel_IOFactory::createWriter($etc_excel, 'Excel5');
		$etc_excel->save('php://output');
	}else{
		echo "<script type='text/javascript'>
		<!--
			location.href='./product_input_excel.php?up_mode=new_upload';
			alert('등록 실패한 품목이 없습니다.');
		//-->
		</script>";
	}
	exit;
}
*/
?>