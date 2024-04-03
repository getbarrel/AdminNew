<?php
include $_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class";
class bankda{
    protected $token;
    protected $partner_id;
    protected $partner_name;
    protected $service_type;
    protected $shopIx;
    protected $mallDataRoot;

    public function __construct(){
        $this->db = new Database();
        $sql = "select mall_domain_id, mall_domain, mall_domain_key from ".TBL_SHOP_SHOPINFO." where mall_domain_key = '".$_SESSION['admininfo']['mall_domain_key']."' ";
        $this->db->query($sql);
        $this->db->fetch();

        $this->mall_domain_id = $this->db->dt['mall_domain_id'];
        $this->mall_domain = $_SERVER["HTTP_HOST"];
        $this->mall_domain_key = $this->db->dt['mall_domain_key'];
        $this->protocall = "http://";

    }

    /**
     * 토큰 발행
     */
    public function getToken(){
        $post_data['mall_domain_id'] = $this->mall_domain_id;
        $post_data['mall_domain'] = $this->mall_domain;
        $post_data['mall_domain_key'] = $this->mall_domain_key;

        $post_data = http_build_query($post_data);

        $url = $this->protocall."www.mallstory.com/openapi/bankda/token.php";
        $headers[]   = 'Content-type: application/x-www-form-urlencoded;charset=utf-8';


        $response = json_decode($this->callApi($url,$post_data,$headers),true);


        if($response['code'] == 200){

            $this->token = $response['token'];
            $this->partner_id = $response['partner_id'];
            $this->partner_name = $response['partner_name'];
            $this->service_type = $response['service_type'];

            $this->init(); //shopIx, 이용자 정보 세팅
        }

        return $response;
    }

    private function callApi($url,$post_data,$headers = ""){
        $ch      = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if($headers){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response    = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * 쇼핑몰 아이디 설정
     */
    public function init(){
        $shopInfo = $this->getShopInfo();

        if(!empty($shopInfo)){
            $this->shopIx		 = $shopInfo["mall_ix"];
            $this->mallDataRoot	 = $shopInfo["mall_data_root"];
        }else{
            $this->error["code"] = "2001";
            $this->error["msg"]	 = "상점정보를 가져오지 못했습니다.";
            $this->errorHandler();
        }

        $userInfo = $this->getUserInfo();

        if(!empty($userInfo)){
            $this->userId = $userInfo["bankda_userid"];
            $this->userPw = $userInfo["bankda_userpw"];
        }/*else{
			//이부분은 미가입으로 간주하고 가입페이지로 이동시킴.
			$this->error["script"] = "location.href='/admin/order/bankda/join.bankda.php?view=popup';";
			$this->error["code"] = "2002";
			$this->error["msg"]	 = "가입되지 않았습니다. 신청페이지로 이동합니다.";
			$this->errorHandler();
		}*/
    }

    /**
     * 쇼핑몰 정보 가져오기
     * @return array	쇼핑몰 정보
     */
    private function getShopInfo(){

        $sql = "SELECT
					mall_data_root,
					mall_type, 
					mall_ix,
					mall_ename 
				FROM
					shop_shopinfo 
				WHERE
					mall_div = 'B'
				";
        $this->db->query($sql);
        if($this->db->total){
            $result = $this->db->fetch();
        }

        return $result;
    }

    /**
     * 이용자 정보 가져오기
     * @return array	이용자 정보
     */
    public function getUserInfo(){

        $post_data['mall_domain_id'] = $this->mall_domain_id;
        $post_data['mall_domain'] = $this->mall_domain;
        $post_data['mall_domain_key'] = $this->mall_domain_key;
        $post_data['mall_ix'] = $this->shopIx;

        $post_data = http_build_query($post_data);

        $url = $this->protocall."www.mallstory.com/openapi/bankda/user.php";
        $headers[]   = 'Authorization: token '.$this->token;
        $headers[]   = 'Content-type: application/x-www-form-urlencoded;charset=utf-8';


        $response = $this->callApi($url,$post_data,$headers);

        return json_decode($response,true);
    }

    /**
     * 등록 계좌 정보
     */
    public function getAccountInfo($seq){
        $post_data['mall_domain_id'] = $this->mall_domain_id;
        $post_data['mall_domain'] = $this->mall_domain;
        $post_data['mall_domain_key'] = $this->mall_domain_key;
        $post_data['seq'] = $seq;

        $post_data = http_build_query($post_data);

        $url = $this->protocall."www.mallstory.com/openapi/bankda/account.php";
        $headers[]   = 'Authorization: token '.$this->token;
        $headers[]   = 'Content-type: application/x-www-form-urlencoded;charset=utf-8';


        $response = $this->callApi($url,$post_data,$headers);

        return json_decode($response,true);
    }

    /**
     * 서비스 등록된 최대 허용 계좌 수 조회
     */
    public function serviceUnit(){
        $post_data['mall_domain_id'] = $this->mall_domain_id;
        $post_data['mall_domain'] = $this->mall_domain;
        $post_data['mall_domain_key'] = $this->mall_domain_key;

        $post_data = http_build_query($post_data);

        $url = $this->protocall."www.mallstory.com/openapi/bankda/unit.php";
        $headers[]   = 'Authorization: token '.$this->token;
        $headers[]   = 'Content-type: application/x-www-form-urlencoded;charset=utf-8';


        $response = $this->callApi($url,$post_data,$headers);

        return json_decode($response,true);
    }

    /**
     * 회원코드로 쇼핑몰 회원정보 가져오기
     * @param string	ucode
     */
    public function getMemberInfoByCode($ucode){
        return $this->model->memberInfoByCode($ucode);
    }
    /**
     * 거래내역+요약정보 가져오기
     * @param array		search option
     * @return array	list,summary
     */
    public function getTransactionList($search = ""){
        $post_data['mall_domain_id'] = $this->mall_domain_id;
        $post_data['mall_domain'] = $this->mall_domain;
        $post_data['mall_domain_key'] = $this->mall_domain_key;

        $post_data['shopId'] = $this->userId;
        $post_data['search'] = $search;

        $post_data = http_build_query($post_data);

        $url = $this->protocall."www.mallstory.com/openapi/bankda/list.php";
        $headers[]   = 'Authorization: token '.$this->token;
        $headers[]   = 'Content-type: application/x-www-form-urlencoded;charset=utf-8';


        $response = $this->callApi($url,$post_data,$headers);

        return json_decode($response,true);
//        $result = $this->mallstoryModel->transactionList($this->userId,$search);
//        return $result;
    }

    /**
     * 개별거래내역+요약정보 가져오기
     * @param array		search option
     * @return array	list,summary
     */
    public function getTransactionListByOne($Bkid = ""){
        $result = $this->mallstoryModel->transactionListByOne($this->userId,$Bkid);
        return $result;
    }

    /**
     * 입금내역만 가져오기
     * @param array		search option
     * @return array	list,summary
     */
    public function getTransactionInList($search = "",$type = "in"){
        $result = $this->mallstoryModel->transactionList($this->userId,$search,$type);
        return $result;
    }

    /**
     * 무통장입금 주문내역 가져오기
     * @param array 검색조건
     */
    public function getOrderList($search){
        $result = $this->model->orderListAll($this->getAccountList(), $search);
        return $result;
    }
    public function test(){
        $result = $this->getBankCodeByName('국민은행');
        echo $result."\n";
    }
    /**
     * 사용중인 계좌정보 가져오기
     * 2013.09.30 실시간 정보로 변경
     * # 은행코드를 안줘서 은행코드 구하는 부분 추가
     * @param	string	accountNo
     * @return	array	account info
     */
    public function getAccountList($accountNo=''){
        $return_array = array();
        $result = $this->callShopAccountInfo();

        if(!empty($result)){
            foreach($result['receive']->account->account_info as $rt):

                $bkcode = $this->getBankCodeByName((string)$rt['bkname']);
                $accountInfo = array(
                    'mid'=> (string)$rt['mid'],
                    'Bkacctno' => (string)$rt['actaccountnum'],
                    'Bkname' => (string)$rt['bkname'],
                    'bkcode' => $bkcode,
                    'accounttype' => (string)$rt['accounttype'],
                    'acttag' => (string)$rt['acttag'],
                    'regdate' => (string)$rt['regdate'],
                    'last_scraping_dtm' => (string)$rt['last_scraping_dtm'],
                    'act_status' => (string)$rt['act_status']
                );
                /* 특정 계좌 조회 */
                if(!empty($accountNo)){
                    if($rt['actaccountnum'] == $accountNo){
                        return $accountInfo;
                    }
                    /* 전체 계좌 조회 */
                }else{
                    array_push($return_array,$accountInfo);
                    $accountInfo = null;
                }
            endforeach;
        }
        return $return_array;
    }
    private function getBankCodeByName($bkname){
        $result = null;

        $bank_xml = $this->callBankJoinInfo();
        if(!empty($bank_xml)){
            foreach($bank_xml as $bx):
                if($bx['name'] == $bkname){
                    $result = $bx['code'];
                }
            endforeach;
        }
        return $result;
    }
    /**
     * 사용 계좌 잔액 조회
     * @return array	list
     */
    public function getAccountBalance(){
        $post_data['mall_domain_id'] = $this->mall_domain_id;
        $post_data['mall_domain'] = $this->mall_domain;
        $post_data['mall_domain_key'] = $this->mall_domain_key;

        $post_data['shopId'] = $this->userId;

        $post_data = http_build_query($post_data);

        $url = $this->protocall."www.mallstory.com/openapi/bankda/balance.php";
        $headers[]   = 'Authorization: token '.$this->token;
        $headers[]   = 'Content-type: application/x-www-form-urlencoded;charset=utf-8';


        $response = $this->callApi($url,$post_data,$headers);

        return json_decode($response,true);
//
//        $result = $this->mallstoryModel->accountBalance($this->userId);
//        return $result;
    }

    /**
     * 이용자 확인
     * @return Boolean	사용중 여부
     */
    public function getShopVerify(){
        $this->result = null;
        $this->result["receive"] = $this->mallstoryModel->shopVerify($this->userId);
        $this->result["action"] = "shop verify";
        return $this->resultHandler();
    }


    /**
    메모 업데이트
     */

    public function transactionMemo($memo_text,$bkid){
        $this->mallstoryModel->MemoUpdate($this->userId,$bkid,$memo_text);
        return true;
    }
    /*==================================================================================================================
     *		입금확인 관련 Function
    *================================================================================================================*/
    /**
     * 개별입금확인처리
     */
    public function transactionMatchByOne($oid,$bkid){
        /* 입금된 대상 가져오기 */

        $checkListByOne = $this->mallstoryModel->checkListByOne($this->userId,$bkid);
        if(!empty($checkListByOne)){
            foreach($checkListByOne as $cl):
                /* 수동 매칭하는 주문 데이터 가져오기 */
                $orderMatch = null;
                $orderMatch = $this->model->orderMatch($oid);

                if(!empty($orderMatch)){
                    $this->orderUpdate($orderMatch['result'],$cl);

                    break;
                }

            endforeach;
        }
        return true;
    }

    /**
     * 개별입금확인처리(동명이인일 경우) 170822 추가
     */
    public function transactionMatchByOneDuplicate($oid,$bkid){
        /* 입금된 대상 가져오기 */

        $checkListByOne = $this->mallstoryModel->checkListByOneDuplicate($this->userId,$bkid);

        if(!empty($checkListByOne)){
            foreach($checkListByOne as $cl):
                /* 수동 매칭하는 주문 데이터 가져오기 */
                $orderMatch = null;
                $orderMatch = $this->model->orderMatch($oid);

                if(!empty($orderMatch)){
                    $this->orderUpdateDuplicate($orderMatch['result'],$cl);

                    break;
                }

            endforeach;
        }
        return true;
    }

    /**
     * 자동입금확인처리
     */
    public function transactionMatch($bool = false){
        /* 입금된 목록 가져오기 */
        $checkList = $this->mallstoryModel->checkList($this->userId);
        if(!empty($checkList)){
            foreach($checkList as $cl):

                /* 매칭되는 주문가져오기 */
                $orderCheck = null;
                $orderCheck = $this->model->orderCheck($cl);

                //syslog(LOG_INFO,'checkList : ' . print_r($orderCheck));

                if(!empty($orderCheck)){
                    //syslog(LOG_INFO,'checkList : ' . $orderCheck['count']);

                    switch($orderCheck['count']){
                        case '1':
                            //매칭되는 주문 한건 -> 입금확인 처리
                            //syslog(LOG_INFO,'case 1 / time : ' . date('h:i:s'));
                            //고객사 요청으로 즉시조회 시 입금확인처리 제외 (0026541) 2019-03-26 by lsc
                            if(!$bool){
                                $this->orderUpdate($orderCheck['result'],$cl);
                            }
                            break;
                        case '0':
                            if($orderCheck['result']['state'] == '1'){
                                $this->duplicateDeposit($orderCheck['result'],$cl); // 이미 입금확인 처리 된 주문일 경우 (M포인트 입금시에만 적용)
                                break;
                            }else{
                                //매칭되는 주문 없음. 미확인 리스트에서 확인 후 처리(입금자명이 틀릴때)
                                //syslog(LOG_INFO,'case 0 / time : ' . date('h:i:s'));
                                break;
                            }
                        default:
                            //동명이인처리
                            //syslog(LOG_INFO,'in default / time : ' . date('h:i:s'));
                            if($orderCheck['count'] > 1){
                                $this->duplicateDeposit($orderCheck['result'],$cl);
                            }
                            break;
                    }

                }
            endforeach;
        }
    }

    /**
     * 입금확인 처리
     * @param array		orderInfo
     * @param array		뱅크다 입금정보
     */
    public function orderUpdate($orderInfo, $depositInfo){
        $this->result = null;
        $this->result['action'] = 'auto update';
        if($this->model->orderUpdate($orderInfo[0],$depositInfo)){
            /* 매칭리스트에 남기기 */
            if(!$this->model->insertMatch($orderInfo, $depositInfo, 'success')){
                $this->error['code'] = '4003';
                $this->error['msg'] = '매칭정보테이블 입력 실패(성공처리)';
                $this->errorHandler();
            }
            $this->mallstoryModel->transactionMatch($this->userId,$depositInfo['Bkcode'],$orderInfo[0]['oid']);
            $this->result['receive'] = 'success / oid : '.$orderInfo['oid'];
            $this->fileLog();
            $result = true;
        }else{
            //error
            $this->error['code'] = '4001';
            $this->error['msg'] = '입금확인 처리 실패';
            $this->errorHandler();
            $result = false;
        }

        if($mode == 'manual'){
            return $this->resultHandler();
        }

        return $result;
    }

    /**
     * 입금확인 처리
     * @param array		orderInfo
     * @param array		뱅크다 입금정보
     */
    public function orderUpdateDuplicate($orderInfo, $depositInfo){
        $this->result = null;
        $this->result['action'] = 'auto update';
        if($this->model->orderUpdate($orderInfo[0],$depositInfo)){ //oid 중복안됨
            /* 매칭리스트에 남기기 */
            if(!$this->model->insertMatchDuplicate($orderInfo, $depositInfo, 'success')){
                $this->error['code'] = '4003';
                $this->error['msg'] = '매칭정보테이블 입력 실패(성공처리)';
                $this->errorHandler();
            }
            //Bkcode 중복 안 되는 코드.
            $this->mallstoryModel->transactionMatch($this->userId,$depositInfo['Bkcode'],$orderInfo[0]['oid']);
            $this->result['receive'] = 'success / oid : '.$orderInfo['oid'];
            $this->fileLog();
            $result = true;
        }else{
            //error
            $this->error['code'] = '4001';
            $this->error['msg'] = '입금확인 처리 실패';
            $this->errorHandler();
            $result = false;
        }

        if($mode == 'manual'){
            return $this->resultHandler();
        }

        return $result;
    }

    /**
     * 동명이인처리
     * @param array2차	orderInfo
     * @param array		뱅크다 입금정보
     */
    public function duplicateDeposit($orderInfo, $depositInfo){
        if($this->mallstoryModel->duplicateCheck($depositInfo)){

            /* 매칭리스트에 남기기 */
            if(!$this->model->insertMatch($orderInfo, $depositInfo, 'duplicate')){
                $this->error['code'] = '4004';
                $this->error['msg'] = '매칭정보테이블 입력 실패(동명이인처리)';
                $this->errorHandler();
            }

            $this->result['receive'] = 'duplicate / NAME : '.$depositInfo['Bkjukyo'].", PRICE : ".$depositInfo['Bkinput'];
            $this->fileLog();
        }else{
            //error
            $this->error['code'] = '4002';
            $this->error['msg'] = '동명이인 처리 실패';
            $this->errorHandler();
        }
    }

    /*==================================================================================================================
     *		뱅크다 Call Function
     *================================================================================================================*/
    /**
     * 이용자의 회원가입
     * (쇼핑몰 관리자)
     * @param array $join
     */
    public function callInsertUser($join){
        $this->result = null;
        /**
         * input value
         *
         * user_id		이용자ID
         * user_pw		이용자PW
         * user_name	이용자이름(업체명)
         * user_tel		전화번호
         * user_email	E-mail
         * accea		이용할계좌갯수(기본값은 1)
         */
        $post = array();
        $post = $join;

        /* fixed value */
        $fixedValue = array(
            "directAccess"	=> "y",
            "partner_id"	=> $this->partner_id,
            "partner_name"	=> $this->partner_name,
            "service_type"	=> $this->service_type
        );
        $post = array_merge((array)$post,(array)$fixedValue);

        $url = "https://ssl.bankda.com/partnership/user/user_join_prs.php";


        /* 뱅크다 호출 */
        $this->result["receive"] = $this->call($url,$post);
        $this->result["action"] = "add User";

        /* DB처리 */
        if(strtolower($this->result["receive"]) == "ok"){
            $this->mallstoryModel->addUser($join);
            return $this->resultHandler();
        }else{
            $this->error["code"] = "1001";
            $this->error["msg"] = "이용자 추가 실패(".$this->result["receive"].")";
            $this->errorHandler();
        }
    }

    /**
     * 이용자 삭제
     */
    public function callDropUser($dropInfo){
        $this->result = null;


        /* fixed value */
        /*
        $fixedValue = array(
        "user_id"		=> $this->userId,
        "user_pw"		=> $this->userPw,
        "directAccess"	=> "y",
        "service_type"	=> $this->service_type,
        "command"		=> "excute",
        "partner_id"	=> $this->partner_id
        );
        */
        $fixedValue = array(
            "user_id"		=> $dropInfo['user_id'],
            "user_pw"		=> $dropInfo['user_pw'],
            "directAccess"	=> "y",
            "service_type"	=> $this->service_type,
            "command"		=> "excute",
            "partner_id"	=> $this->partner_id
        );

        $url = "https://ssl.bankda.com/partnership/user/user_withdraw.php";

        /* 뱅크다 호출 */
        $this->result["receive"] = $this->call($url,$fixedValue);
        $this->result["action"] = "drop User";

        /* DB처리 */
        if(strtolower($this->result["receive"]) == "ok"){
            //acc삭제
            $this->mallstoryModel->deleteAccount('drop',$fixedValue);
            //userinfo 삭제
            $this->mallstoryModel->dropUser($fixedValue);
            return $this->resultHandler();
        }else{
            $this->error["code"] = "1002";
            $this->error["msg"] = "이용자 삭제 실패(".$this->result["receive"].")";
            $this->errorHandler();
        }
    }

    /**
     * 이용자의 계좌추가
     * @param array $accInfo
     */
    public function callAddAccount($accInfo){
        $this->result = null;
        /**
         * input value
         *
         * bkdiv		법인/개인구분 (법인:C, 개인:P)
         * bkcode		은행코드
         * bkacctno		계좌번호 '-'없이 전송
         * bkacctpno_pw	계좌비밀번호
         * Mjumin_1		주민등록번호 앞 6자리
         * Mjumin_2		주민등록번호 뒤 7자리
         * Bjumin_1		사업자등록번호 앞 3자리
         * Bjumin_2		사업자등록번호 중간 2자리
         * Bjumin_3		사업자등록번호 뒤 5자리
         * webid		인터넷뱅킹ID (신한-간편조회용ID, 대구-안심계좌번호)
         * webpw		인터넷뱅킹PW (신한-간편조회용PW, 대구-안심계좌비밀번호)
         */
        /* 은행별 필요값으로 맞춤 */
        $post = array();
        $post = $this->adjustAccountValue($this->callBankJoinInfo(),$accInfo);

        /* fixed value */
        $fixedValue = array(
            "user_id"		=> $this->userId,
            "user_pw"		=> $this->userPw,
            "directAccess"	=> "y",
            "service_type"	=> $this->service_type,
            "partner_id"	=> $this->partner_id,
            "Command"		=> "update"
        );
        $post = array_merge((array)$post,(array)$fixedValue);

        $url = "https://ssl.bankda.com/partnership/user/account_add.php";

        /* 뱅크다 호출 */
        $this->result['receive'] = $this->call($url,$post);
        $this->result['action'] = 'add Account';

        /* DB처리 */
        if(strtolower($this->result["receive"]) == "ok"){
            $this->mallstoryModel->addAccount($post);
            return $this->resultHandler();
        }else{
            $this->error["code"] = "1003";
            $this->error["msg"] = "이용자 계좌추가 실패(".$this->result["receive"].")";
            $this->errorHandler();
        }

    }

    /**
     * 이용자의 계좌수정
     * @param array $accInfo
     */
    public function callModifyAccount($accInfo){
        $this->result = null;
        /**
         * input value
         *
         * bkdiv		법인/개인구분 (법인:C, 개인:P)
         * bkacctno		계좌번호 '-'없이 전송
         * bkacctpno_pw	계좌비밀번호
         * Mjumin_1		주민등록번호 앞 6자리
         * Mjumin_2		주민등록번호 뒤 7자리
         * Bjumin_1		사업자등록번호 앞 3자리
         * Bjumin_2		사업자등록번호 중간 2자리
         * Bjumin_3		사업자등록번호 뒤 5자리
         * webid		인터넷뱅킹ID (신한-간편조회용ID, 대구-안심계좌번호)
         * webpw		인터넷뱅킹PW (신한-간편조회용PW, 대구-안심계좌비밀번호)
         */
        /* 은행별 필요값으로 맞춤 */
        $post = array();
        $post = $this->adjustAccountValue($this->callBankJoinInfo(),$accInfo);

        /* fixed value */
        $fixedValue = array(
            "user_id"		=> $this->userId,
            "user_pw"		=> $this->userPw,
            "directAccess"	=> "y",
            "service_type"	=> $this->service_type,
            "partner_id"	=> $this->partner_id,
            "Command"		=> "update"
        );
        $post = array_merge((array)$post,(array)$fixedValue);

        $url = "https://ssl.bankda.com/partnership/user/account_fix.php";

        /* 뱅크다 호출 */
        $this->result["receive"] = $this->call($url,$post);
        $this->result["action"] = "modify Account";

        /* 결과 처리 */
        if(strtolower($this->result["receive"]) == "ok"){
            return $this->resultHandler();
        }else{
            $this->error["code"] = "1004";
            $this->error["msg"] = "이용자 계좌수정 실패(".$this->result["receive"].")";
            $this->errorHandler();
        }
    }

    /**
     * 이용자의 계좌삭제
     * # 여기서만 대문자 OK반환 -> strtolower 추가
     * @param array $accInfo
     */
    public function callDeleteAccount($bkacctno){
        $this->result = null;
        /**
         * input value
         *
         * bkacctno	계좌번호 '-'없이 전송
         */
        $post = array(
            "bkacctno" => $bkacctno
        );

        /* fixed value */
        $fixedValue = array(
            "user_id"		=> $this->userId,
            "user_pw"		=> $this->userPw,
            "directAccess"	=> "y",
            "service_type"	=> $this->service_type,
            "partner_id"	=> $this->partner_id,
            "Command"		=> 'update'
        );
        $post = array_merge((array)$post,(array)$fixedValue);

        $url = "https://ssl.bankda.com/partnership/user/account_del.php";

        /* 뱅크다 호출 */
        $this->result["receive"] = $this->call($url,$post);
        $this->result["action"] = "delete Account";

        /* DB처리 */
        if(strtolower($this->result["receive"]) == "ok"){
            $this->mallstoryModel->deleteAccount($bkacctno);
            return $this->resultHandler();
        }else{
            $this->error["code"] = "1005";
            $this->error["msg"] = "이용자 계좌삭제 실패(".$this->result["receive"].")";
            $this->errorHandler();
        }
    }

    /**
     * 이용자 계좌 즉시조회
     * @param string 계좌번호
     */
    public function callImmediatelyUpdate($bkacctno){
        $this->result = null;
        /**
         * input value
         *
         * bkacctno	계좌번호 '-'없이 전송
         */
        $post = array(
            "bkacctno" => $bkacctno
        );

        /* fixed value */
        $fixedValue = array(
            "user_id"		=> $this->userId,
            "user_pw"		=> $this->userPw,
            "directAccess"	=> "y",
            "service_type"	=> $this->service_type,
            "partner_id"	=> $this->partner_id
        );
        $post = array_merge((array)$post,(array)$fixedValue);

        $url = "https://ssl.bankda.com/partnership/user/renovation.php";

        /* 뱅크다 호출 */
        $this->result["receive"] = $this->call($url,$post);
        $this->result["action"] = "update without delay";

        /* 결과 처리 */
        if(strtolower($this->result["receive"]) == "ok"){
            return $this->resultHandler();
        }else{
            $this->error["code"] = "1006";
            $this->error["msg"] = "계좌 즉시조회 실패(".$this->result["receive"].")";
            $this->errorHandler();
        }
    }
    /*==================================================================================================================
     *		뱅크다 정보 XML 조회
     *================================================================================================================*/

    /**
     * 제휴사 서비스 정보 조회
     */
    public function callPartnerShipInfo(){
        $this->result = null;
        $post = array();

        /* fixed value */
        $fixedValue = array(
            "service_type"	=> $this->service_type,
            "partner_id"	=> $this->partner_id,
            "partner_pw"	=> "shin0606",
            "char_set"		=> "utf-8"
        );
        $post = array_merge((array)$post,(array)$fixedValue);

        $url = "https://ssl.bankda.com/partnership/partner/partnership_info.php";

        /* 뱅크다 호출 */
        $this->result["receive"] = $this->call($url,$post,"XML");
        $this->result["action"] = "get PartnerShipInfo";

        return $this->result;
    }

    /**
     * 은행 계좌 등록 정보 가져오기
     * @return XMLobject	은행별 계좌등록 필요정보
     */
    public function callBankJoinInfo(){
        $url = "http://www.bankda.com/bankda_requestinfo.xml";
        return $this->call($url,null,'XML');
    }

    /**
     * 전체계좌 조회
     */
    public function callAllAccountInfo(){
        $this->result = null;
        $post = array();

        /* fixed value */
        $fixedValue = array(
            "service_type"	=> $this->service_type,
            "partner_id"	=> $this->partner_id,
            "partner_pw"	=> "shin0606",
            "char_set"		=> "utf-8"
        );
        $post = array_merge((array)$post,(array)$fixedValue);

        $url = "https://ssl.bankda.com/partnership/partner/account_list_partnerid_xml.php";

        /* 뱅크다 호출 */
        $this->result["receive"] = $this->call($url,$post,"XML");
        $this->result["action"] = "get AllAccountInfo";

        return $this->result;
    }
    /**
     * 이용자의 전체 계좌 조회
     */
    public function callShopAccountInfo(){
        $this->result = null;
        $post = array();

        /* fixed value */
        $fixedValue = array(
            "service_type"	=> $this->service_type,
            "partner_id"	=> $this->partner_id,
            "user_id"		=> $this->userId,
            "user_pw"		=> $this->userPw,
            "char_set"		=> "utf-8"
        );
        $post = array_merge((array)$post,(array)$fixedValue);

        $url = "https://ssl.bankda.com/partnership/partner/account_list_userid_xml.php";

        /* 뱅크다 호출 */
        $this->result["receive"] = $this->call($url,$post,"XML");
        $this->result["action"] = "get User AccountInfo";

        return $this->result;
    }
    /**
     * 전체회원 조회
     */
    public function callUserList(){
        $this->result = null;
        $post = array();

        /* fixed value */
        $fixedValue = array(
            "service_type"	=> $this->service_type,
            "partner_id"	=> $this->partner_id,
            "partner_pw"	=> "shin0606",
            "char_set"		=> "utf-8"
        );
        $post = array_merge((array)$post,(array)$fixedValue);

        $url = "https://ssl.bankda.com/partnership/partner/user_list_partnerid_xml.php";

        /* 뱅크다 호출 */
        $this->result["receive"] = $this->call($url,$post,"XML");
        $this->result["action"] = "get UserList";

        return $this->result;
    }
    /*==================================================================================================================
     *		뱅크다 CALL
     *================================================================================================================*/

    /**
     * CURL call
     * @param String	URL
     * @param array 	POSTVALUE
     */
    public function call($url,$postData,$returnType = "String"){
        if($this->debug){
            syslog(LOG_INFO,"URL : ".$url);
            syslog(LOG_INFO,"Data : ".serialize($postData));
        }
        if(!empty($postData)){
            /* EUC-KR로 변환 */
            $postData = $this->convertArrayToEUCKR($postData);

            /* Query String 으로 변환*/
            $postData = $this->buildHttpQuery($postData);
        }

//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
//        curl_setopt($ch, CURLOPT_HEADER, 0);
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
//        curl_setopt($ch, CURLOPT_FAILONERROR,true);

           $cmd = sprintf("curl -d \"%s\" -X POST \"%s\"", $postData, $url);
           $result = shell_exec($cmd);

//        $result = curl_exec($ch);
//        if(curl_errno($ch)){
//            $this->error["code"] = "1100";
//            $this->error["msg"] = curl_error($ch);
//            $this->errorHandler();
//        }
        switch ($returnType){
            case "String":
                /* UTF-8로 변환*/
                $result = $this->convertUTF($result);

                if($this->debug){
                    syslog(LOG_INFO,"RESULT : ".$result);
                }
                break;

            case "XML":
                try{
                    @$result = new SimpleXMLElement($result, LIBXML_NOCDATA);

                }catch(Exception $e){
                    $this->errorHandler("3001","XML형식이 잘못 되었습니다.");
                    exit;
                }
                if($this->debug){
                    syslog(LOG_INFO,"RESULT : XML");
                }
                break;
        }
        return $result;
    }

    /*==================================================================================================================
     *		Utility Function
     *================================================================================================================*/

    /**
     * 계좌추가 필수값 맞추기
     * @param object	은행별 필요정보 XML
     * @param array		계좌정보
     */
    private function adjustAccountValue($xml,$val){

        $return_array = array();

        foreach($xml as $rt):

            if($rt['code'] == $val['bkcode']){
                switch($val['bkdiv']){
                    case 'C':
                        $value_str = $rt->requestinfo[0];
                        break;
                    case 'P':
                        $value_str = $rt->requestinfo[1];
                        break;
                }

                /* exception 신규등록 일시중지 은행 */
                if(substr_count($value_str,'일시중지') > 0){
                    $this->error["code"] = "1007";
                    $this->error["msg"] = "계좌추가실패(신규 등록 일시정지 은행)";
                    $this->errorHandler();
                }

                /* default value */
                $return_array['bkname'] = (string)$rt['name'];
                $return_array['bkdiv'] = $val['bkdiv'];
                $return_array['bkcode'] = $val['bkcode'];

                /* 은행별 필요 값 */
                $value_array = explode(",",$value_str);
                if(!empty($value_array)){
                    foreach($value_array as $va):
                        switch(trim($va)){
                            case '계좌번호':
                                $return_array['bkacctno'] = $val['bkacctno'];
                                break;
                            case '계좌비밀번호':
                                $return_array['bkacctpno_pw'] = $val['bkacctpno_pw'];
                                break;
                            case '사업자등록번호':
                                $return_array['Bjumin_1'] = $val['Bjumin_1'];
                                $return_array['Bjumin_2'] = $val['Bjumin_2'];
                                $return_array['Bjumin_3'] = $val['Bjumin_3'];
                                break;
                            case '생년월일(YYMMDD0000000)':
                            case '생년월일':
                                $return_array['Mjumin_1'] = $val['Mjumin_1'];
                                $return_array['Mjumin_2'] = $val['Mjumin_2'];
                                break;
                            case '인터넷뱅킹ID':
                            case '간편조회용ID':
                            case '간편조회ID':
                            case 'e농협회원ID':
                                $return_array['webid'] = $val['webid'];
                                break;
                            case '인터넷뱅킹PW':
                            case '간편조회용PW':
                            case '간편조회PW':
                            case 'e농회원협PW':
                                $return_array['webpw'] = $val['webpw'];
                                break;
                        }
                    endforeach;
                }
            }
        endforeach;
        return $return_array;
    }
    /**
     * UTF8 변환
     * utf-8인지 체크하고 아닌경우 변환
     * @param 	String 	$var
     * @return 	String
     */
    private function convertUTF($var) {
        if(iconv("UTF-8","UTF-8",$var)==$var){
            return $var;
        }else{
            return iconv("EUC-KR","UTF-8",$var);
        }
    }

    /**
     * UTF-8(array) to EUC-KR(array) 변환
     * @param 	array 	$array
     * @return 	array 	$array
     */
    private function convertArrayToEUCKR($array){
        $value = null;
        if(!empty($array)){
            foreach($array as &$value):
                if(iconv("EUC-KR","EUC-KR",$value) == $value){
                    $value = $value;
                }else{
                    $value = iconv("UTF-8","EUC-KR",$value);
                }
            endforeach;
        }
        return $array;
    }
    /**
     * Array를 QueryString으로 변환
     * @param array		$array
     * @param Char		urlEncode Y/N
     * @return string   http query string
     **/
    public function buildHttpQuery( $array, $urlencode = 'Y'){
        $query_array = array();
        foreach( $array as $key => $key_value ){
            if($key != 'page' && $key != 'act'){
                if($urlencode == 'Y'){
                    $query_array[] = urlencode( $key ) . '=' . urlencode( $key_value );
                }else{
                    $query_array[] =  $key . '=' .  $key_value;
                }
            }
        }
        return implode( '&', $query_array );
    }

    /**
     * 파일에 로그남기기
     * TODO: 건당 로그 넣도록 사용할 것.
     * @param string $type
     */
    public function fileLog($type = 'result'){
        $dateTime = date("Y-m-d H:i:s");
        $write = "";
        switch($type){
            case 'result':
                $write = "TIME : ".$dateTime.", ACTION : ".$this->result["action"].", RESULT : ".$this->result['receive']."\n\r";
                break;
            case 'error':
                $write = "TIME : ".$dateTime.", ERRORCODE : ".$this->error["code"].", MSG : ".$this->error['msg']."\n\r";
                break;
        }
        $logPath = $_SERVER["DOCUMENT_ROOT"]."".$this->mallDataRoot."/_logs/";

        $date = date("Ymd");
        $fp = fopen($logPath."bankda_".$date."_".$type.".txt","a+");
        fwrite($fp,$write);
        fclose($fp);
    }


    /**
     * 시간계산함수
     * @param string 	시작일
     * @param string	종료일
     * @param string	반환타입 (minute,day,default)
     */
    public function dateDiff($sStartDate, $sEndDate, $option = ''){
        $sStartTime = strtotime($sStartDate);
        $sEndTime = strtotime($sEndDate);

        if($sStartTime > $sEndTime){
            return false;
        }
        $sDiffTime = $sEndTime - $sStartTime;

        if($option == 'minute'){
            $aReturnValue = sprintf("%02d", ($sDiffTime/60));

            return $aReturnValue;

        }else if($option == 'day'){

            $aReturnValue = floor($sDiffTime/60/60/24);


            return $aReturnValue;

        }else{
            $aReturnValue['d'] = floor($sDiffTime/60/60/24);
            //$aReturnValue['d'] = $sDiffTime/60/60/24;
            $aReturnValue['H'] = sprintf("%02d", ($sDiffTime/60/60)%24);
            $aReturnValue['i'] = sprintf("%02d", ($sDiffTime/60)%60);

            return $aReturnValue;
        }
    }
    /**
     *
     * Convert an object to an array
     * Also converts objects within objects / arrays
     *
     * @param    object  $object The object to convert
     * @reeturn  array
     *
     */
    public function objectToArray($data){
        if(is_array($data) || is_object($data)){
            $result = array();
            foreach ($data as $key => $value)
            {
                $result[$key] = $this->objectToArray($value);
            }
            return $result;
        }
        return $data;
    }

    /**
     * 결과 핸들러
     */
    public function resultHandler(){
        //$this->result["msg"];
        $this->fileLog('result');
        return $this->result;
    }

    /**
     * 에러 핸들러
     */
    public function errorHandler(){
        /* 줄바꿈기호 제거 */
        $needle = array("\r\n","\n","</br>","\r","\t");
        $this->error["msg"] = str_replace($needle,"",$this->error["msg"]);
        if($this->debug){
            syslog(LOG_INFO,"[".$this->error["code"]."] ".$this->error["msg"]);
        }
        $this->fileLog('error');
        echo "<script>alert(\"[".$this->error["code"]."] ".$this->error["msg"]."\");".$this->error['script']."</script>";
        exit;
    }

    /**
     * DEBUG MODE
     */
    public function setDebug(){
        $this->debug = true;
    }
}
