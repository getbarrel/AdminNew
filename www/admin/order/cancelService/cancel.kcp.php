<?
/**
 * kcp 취소 모듈
 *
 * @author hong
 * @date 2015.09.24
 */


require $_SERVER["DOCUMENT_ROOT"] . "/shop/kcp/sample/pp_ax_hub_lib.php";

class kcp
{

    private $c_PayPlus;
    private $result;

    public function __construct()
    {
        $result = null;
        $this->c_PayPlus = new C_PP_CLI;
    }

    public function cancelService($data)
    {

        $this->c_PayPlus->mf_clear();

        $g_conf_home_dir = $_SERVER["DOCUMENT_ROOT"] . "/shop/kcp/";

        $tran_cd = "00200000";        //고정값
        $g_conf_gw_port = "8090";    //고정값
        $oid = $data['oid'];

        //가상 계좌일때
        if ($data['method'] == ORDER_METHOD_VBANK) {
            $this->c_PayPlus->mf_set_modx_data("mod_bankcode", $this->getBankCode($data['bank_code']));      // 환불 요청 은행 코드
            $this->c_PayPlus->mf_set_modx_data("mod_account", $data['bank_number']);      // 환불 요청 계좌
            $this->c_PayPlus->mf_set_modx_data("mod_depositor", trim($data['bank_owner']));      // 환불 요청 계좌주명
            $this->c_PayPlus->mf_set_modx_data("mod_comp_type", 'MDCP01');      // 계좌인증 + 환불등록 – MDCP01, (계좌+실명)인증 + 환불등록 – MDCP02
//            if ($mod_comp_type == "MDCP02") {
//                $this->c_PayPlus->mf_set_modx_data("mod_socno", $mod_socno);      // 실명확인 주민번호
//                $this->c_PayPlus->mf_set_modx_data("mod_socname", $cancelData->bankOwner);      // 실명확인 성명
//            }

            if ($data['cancel_type'] == "part") {//부분 취소
                $mod_type = 'STPD';
                if ($data['tax_flag'] == "TG03") {
                    $this->c_PayPlus->mf_set_modx_data("mod_sub_type", "MDSC04");      // 변경 유형 - 복합과세 부분환불
                } else {
                    $this->c_PayPlus->mf_set_modx_data("mod_sub_type", "MDSC03");      // 변경 유형 - 부분환불
                }
            } else {
                $mod_type = 'STHD';
                $this->c_PayPlus->mf_set_modx_data("mod_sub_type", "MDSC00");      // 변경 유형 - 전체환불
            }
        } else {
            if ($data['cancel_type'] == "part") {//부분 취소
                $mod_type = 'STPC';
            } else {
                $mod_type = 'STSC';
            }
        }

        if ($data['cancel_type'] == "part") {//부분 취소
            $this->c_PayPlus->mf_set_modx_data("mod_mny", (int)$data['cancel_amount']); // 취소 요청 금액
            $this->c_PayPlus->mf_set_modx_data("rem_mny", (int)$data['remain_price']); // 부분취소 이전에 남은금액
        }

        if ($data['kcp_type'] == "test") {
            $g_conf_gw_url = "testpaygw.kcp.co.kr";
        } else {
            $g_conf_gw_url = "paygw.kcp.co.kr";
        }

        $cust_ip = $_SERVER['REMOTE_ADDR'];

        if($data['method'] == ORDER_METHOD_KAKAOPAY){
            $g_conf_site_cd = 'A8P9X';
            $g_conf_site_key = '2cerp6Ai.tnCvn2I8LRsh04__';
        } else {
            $g_conf_site_cd = $data['kcp_id'];
            $g_conf_site_key = $data['kcp_key'];
        }

        $this->c_PayPlus->mf_set_modx_data("tno", $data['tid']); // KCP 원거래 거래번호
        $this->c_PayPlus->mf_set_modx_data("mod_type", $mod_type); // 원거래 변경 요청 종류
        $this->c_PayPlus->mf_set_modx_data("mod_ip", $cust_ip); // 변경 요청자 IP
        $this->c_PayPlus->mf_set_modx_data("mod_desc", ''); // 변경 사유 (사유 정보 KCP 전송 시 잦은 인코딩 및 자리수 문제로 넘기지 않도록 처리)
//        $this->c_PayPlus->mf_set_modx_data("mod_desc", str_replace(array("\"", "'", "\"", "\n", "\r"), array("¨", "＇", "＇", " ", " "), $data['reason'])); // 변경 사유

//		if($data['tax_flag']=="TG03"){
//			$this->c_PayPlus->mf_set_modx_data( "tax_flag",      $data['tax_flag']      ); //복합과세 거래 구분 값
//			$this->c_PayPlus->mf_set_modx_data( "mod_tax_mny",      $data['']      ); //공급가 부분취소 요청금액
//			$this->c_PayPlus->mf_set_modx_data( "mod_vat_mny",      $data['']      ); //부과세 부분 취소 요청금액
//			$this->c_PayPlus->mf_set_modx_data( "mod_free_mny",      $data['']      ); //비과세 부분 취소 요청금액
//		}

        $this->c_PayPlus->mf_do_tx($trace_no, $g_conf_home_dir, $g_conf_site_cd, $g_conf_site_key, $tran_cd, "",
            $g_conf_gw_url, $g_conf_gw_port, "payplus_cli_slib", $oid,
            $cust_ip, "3", 0, 0, $g_conf_key_dir, $g_conf_log_dir);
        /*
        $c_PayPlus->mf_do_tx( "", $home_dir, $site_cd, $site_key, $tran_cd, "",
                              $gw_url, $gw_port, "payplus_cli_slib", "",c
                              $cust_ip, "3", 0, 0, $log_path ); // 응답 전문 처리
        */
        $this->result["res_cd"] = $this->c_PayPlus->m_res_cd;  // 결과 코드
        $this->result["res_msg"] = iconv("EUC-KR", "UTF-8", $this->c_PayPlus->m_res_msg); // 결과 메시지

        return $this->result;
    }

	// 에스크로 상태변경 관련
	public function statusService($data){
		$this->c_PayPlus->mf_clear();

		$g_conf_home_dir = $_SERVER["DOCUMENT_ROOT"] . "/shop/kcp/";

        $g_conf_gw_port = "8090";    //고정값
        $oid = $data['oid'];
		
		if($data['mod_type'] == "STE1"){											//배송시작
			$tran_cd = "00200000";       //고정값
			$this->c_PayPlus->mf_set_modx_data("mod_type", $data['mod_type']);				// 원거래 변경 요청 종류
			$this->c_PayPlus->mf_set_modx_data("deli_numb", $data['deli_numb']);			// 운송장 번호
            $this->c_PayPlus->mf_set_modx_data("deli_corp", $data['deli_corp']);			// 택배 업체명
			$this->c_PayPlus->mf_set_modx_data("mod_desc", "배송시작" );						// 변경 사유
		} else if($data['mod_type'] == "STE2" || $data['mod_type'] == "STE4"){		//즉시취소(배송 전 취소) / 취소 (배송 후 취소)=> 해당 설정은 주문상세에서 취소요청 > 취소완료 후 환불쪽에서 처리해야하는 부분
			$tran_cd = "00200000";       //고정값
			$this->c_PayPlus->mf_set_modx_data("mod_type", $data['mod_type']);				// 원거래 변경 요청 종류
			$this->c_PayPlus->mf_set_modx_data("refund_account", $data['refund_account']);	// 환불수취계좌번호
			$this->c_PayPlus->mf_set_modx_data("refund_nm",	$data['refund_nm']);			// 환불수취계좌주명
			$this->c_PayPlus->mf_set_modx_data("bank_code", $this->getBankCode($data['bank_code']));			// 환불수취은행코드
			$this->c_PayPlus->mf_set_modx_data("mod_desc", "취소요청" );						// 변경 사유
		} else if($data['mod_type'] == "STE9_V"){									//가상계좌 구매 확인 후 환불
			$tran_cd = "70200200";       //고정값
			$this->c_PayPlus->mf_set_modx_data( "mod_type", "STE9");
            $this->c_PayPlus->mf_set_modx_data( "mod_desc_cd", "CA06" );
            $this->c_PayPlus->mf_set_modx_data( "mod_desc", "환불완료");
			$this->c_PayPlus->mf_set_modx_data( "sub_mod_type", "STHD");							
			$this->c_PayPlus->mf_set_modx_data( "mod_mny", "4500");
			$this->c_PayPlus->mf_set_modx_data( "mod_sub_type", "MDSC00");
			$this->c_PayPlus->mf_set_modx_data( "mod_account", $data['refund_account']);
			$this->c_PayPlus->mf_set_modx_data( "mod_bankcode", $this->getBankCode($data['bank_code']));
			$this->c_PayPlus->mf_set_modx_data( "mod_depositor", $data['refund_nm']);
		} else if($data['mod_type'] == "STE9_VP"){									//가상계좌 구매 확인 후 부분환불 
			$tran_cd = "70200200";       //고정값
			$this->c_PayPlus->mf_set_modx_data( "mod_type", "STE9");
            $this->c_PayPlus->mf_set_modx_data( "mod_desc_cd", "CA06" );
            $this->c_PayPlus->mf_set_modx_data( "mod_desc", $data['cancel_msg']);
			$this->c_PayPlus->mf_set_modx_data( "sub_mod_type", "STPD");
			$this->c_PayPlus->mf_set_modx_data( "mod_mny", $data['cancel_amount']);
			$this->c_PayPlus->mf_set_modx_data( "rem_mny", $data['real_price']); 
			$this->c_PayPlus->mf_set_modx_data( "mod_sub_type", "MDSC04");
			$this->c_PayPlus->mf_set_modx_data( "mod_account", $data['refund_account']);
			$this->c_PayPlus->mf_set_modx_data( "mod_bankcode", $this->getBankCode($data['bank_code']));
			$this->c_PayPlus->mf_set_modx_data( "mod_depositor", $data['refund_nm']);
			$this->c_PayPlus->mf_set_modx_data( "part_canc_yn", "Y");
			//$this->c_PayPlus->mf_set_modx_data( "tax_flag", "TG03"); // 복합과세 부분취소                   
            //$this->c_PayPlus->mf_set_modx_data( "mod_tax_mny","6363"); // 공급가 부분취소 금액
            //$this->c_PayPlus->mf_set_modx_data( "mod_free_mny",0); // 비과세 부분취소 금액
            //$this->c_PayPlus->mf_set_modx_data( "mod_vat_mny", "637"); // 부가세 부분취소 금액
		} else if($data['mod_type'] == "STE3"){
			$tran_cd = "00200000";

            $this->c_PayPlus->mf_set_modx_data( "mod_type", "STE3");
		}

		if ($data['kcp_type'] == "test") {
            $g_conf_gw_url = "testpaygw.kcp.co.kr";
        } else {
            $g_conf_gw_url = "paygw.kcp.co.kr";
			//$g_conf_gw_url = 'testpaygw.kcp.co.kr';
        }

		$cust_ip = $_SERVER['REMOTE_ADDR'];

        if($data['method'] == ORDER_METHOD_KAKAOPAY){
            $g_conf_site_cd = 'A8P9X';
            $g_conf_site_key = '2cerp6Ai.tnCvn2I8LRsh04__';
        } else {
            $g_conf_site_cd = $data['kcp_id'];
            $g_conf_site_key = $data['kcp_key'];
        }

		$this->c_PayPlus->mf_set_modx_data( "tno",        $data['tno']);      // KCP 원거래 거래번호
        $this->c_PayPlus->mf_set_modx_data( "mod_ip",     $cust_ip);      // 변경 요청자 IP

		 $this->c_PayPlus->mf_do_tx("", $g_conf_home_dir, $g_conf_site_cd, $g_conf_site_key, $tran_cd, "",
            $g_conf_gw_url, $g_conf_gw_port, "payplus_cli_slib", $oid,
            $cust_ip, "3", 0, 0, $g_conf_key_dir, $g_conf_log_dir);

        $this->result["res_cd"] = $this->c_PayPlus->m_res_cd;  // 결과 코드
        $this->result["res_msg"] = iconv("EUC-KR", "UTF-8", $this->c_PayPlus->m_res_msg); // 결과 메시지

        return $this->result;
	}

    private function getBankCode($bankCode)
    {
        $data = array(
            "su" => "BK02", "ku" => "BK03", "km" => "BK04", "yh" => "BK81",
            "ss" => "BK07", "nh" => "BK11", "nh2" => "BK11", "ch" => "",
            "wr" => "BK20", "sh" => "BK88", "jh" => "BK88", "shjh" => "BK88",
            "sc" => "BK23", "hn" => "BK81", "hn2" => "BK81", "hc" => "BK27",
            "dk" => "BK31", "bs" => "BK32", "kj" => "BK34", "jj" => "BK35",
            "jb" => "BK37", "gw" => "", "kn" => "BK39", "bc" => "",
            "ct" => "BK27", "hks" => "", "po" => "BK71", "ph" => "BK20",
            "ssg" => "", "sl" => "BK64", "sk" => "BK45", "sn" => "BK48",
            "sj" => "", "hsbc" => "BK54", "kb" => "BK89", "kkao" => "BK90"
        );

        return $data[$bankCode] ? $data[$bankCode] : '';
    }
}
