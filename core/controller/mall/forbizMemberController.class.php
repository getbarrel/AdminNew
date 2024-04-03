<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-02-26
 * Time: 오후 5:36
 */

require_once constant("CORE_ROOT") . "/controller/forbizController.class.php";

class forbizMemberController extends forbizController
{
    /**
     * 로그인
     */
    public function login()
    {
        require_once constant("MALL_ROOT") . "/model/member/auth.class.php";
        require_once constant('CORE_ROOT') . "/common/di.class.php";

        $authClass = new auth();
        $diClass = new di('cookie', 'aes128');

        $id = $this->requestFilter($_POST['userId']);
        $pw = $this->requestFilter($_POST['userPw']);
        $autoLogin = $this->requestFilter($_POST['autoLogin']);
        $saveId = $this->requestFilter($_POST['saveId']);
        $url = $this->requestFilter($_POST['url']);

        //아이디 비밀번호로 회원 데이터 가지고 오기
        $userAuthData = $authClass->getUserAuthDataByIdPw($id, $pw, $_SESSION['privacy_config']['sleep_user_yn']);

        if (empty($userAuthData['code'])) { //로그인 실패
            $this->_setResponseResult("fail");
            return;
        } else { // 로그인 성공
            if ($userAuthData['authorized'] == "Y") { //승인
                require_once constant("MALL_ROOT") . "/model/store/privacy.class.php";
                require_once constant("MALL_ROOT") . "/model/shop/cart.class.php";
                require_once constant("MALL_ROOT") . "/model/app/push.class.php";

                $privacyClass = new privacy();
                $cartClass = new cart();
                $pushClass = new push();

                //로그인 세션 생성
                $authClass->setUserSession($userAuthData);

                //로그인 성공 관련 회원정보 업데이트
                $authClass->updateLoginUserData($userAuthData['code']);

                //비회원일때 카트 담은 정보 업데이트
                $cartClass->updateLoginUserCartData($userAuthData['code']);

                //비밀번호 변경안내 (휴면계정일때는 비밀번호 변경 처리를 유보함)
                if ($userAuthData['sleep_account'] != 'Y') {
                    //새션을 만들면 layout.class 에서 이동 처리함
                    $privacyClass->setChangePasswordSession($privacyClass->isChangePassword($userAuthData['change_pw_date'], $userAuthData['date']));
                }

                //로그인 히스토리 정보 삭제 ( 기존에 프로세스 대로 코딩했지만 크론으로 따로 빼야 하는거 아닌지? )
                $authClass->deleteConnectUserLog($_SESSION['privacy_config']['member_connect_delete_day']);

                //로그인 히스토리 정보 등록
                $authClass->connectUserLog($id, $userAuthData['code'], 'login');

                //deviceId로 회원 맵핑
                if (!empty($_SESSION['app_type']) && !empty($_SESSION["device_id"])) {
                    $pushClass->updateUserPushServiceByDeviceId($userAuthData['code'], $_SESSION["device_id"]);
                }

                //자동 로그인 여부 쿠키 세팅
                if ($autoLogin == 'Y') {
                    $auth_token = $diClass->aes128->encrypt($id . "|" . $pw);
                    $diClass->cookie->set('connection_no', $auth_token, (60 * 60 * 24 * 15));
                    $diClass->cookie->set('auto_login', 'Y', (60 * 60 * 24 * 15));
                } else {
                    $diClass->cookie->delete('connection_no');
                    $diClass->cookie->delete('auto_login');
                }

                //아이디 저장
                if ($saveId == 'Y') {
                    $diClass->cookie->set('userSaveLoginId', $id, (60 * 60 * 24 * 30));
                } else {
                    $diClass->cookie->delete('userSaveLoginId');
                }

                //로그&이커머스분석
                require_once(constant("MALL_ROOT") . "/admin/logstory/class/businessLogic.class");
                $bl = new BusinessLogic();
                $bl->MemberLoginUpdate($userAuthData['code']);

                //로그인후 이동할 url
                $responseData['url'] = '/';
                if (!empty($url)) {
                    $responseData['url'] = $url;
                }
                $this->_setResponseData($responseData);
            } else {
                if ($userAuthData['authorized'] == "N") { //승인 대기
                    $this->_setResponseResult("standby");
                } else { //승인 거부
                    $this->_setResponseResult("reject");
                }
                //로그인 히스토리 정보 등록
                $authClass->connectUserLog($id, '', 'login');
                return;
            }
        }
    }

    /**
     * 비회원 로그인
     */
    public function nonMemberLogin()
    {
        require_once constant("MALL_ROOT") . "/model/shop/order.class.php";
        require_once constant("MALL_ROOT") . "/model/member/auth.class.php";

        $orderClass = new order();
        $authClass = new auth();

        $bname = $this->requestFilter($_POST['buyerName']);
        $oid = $this->requestFilter($_POST['orderId']);
        $orderPw = $this->requestFilter($_POST['orderPassword']);

        $oid = $orderClass->orderIdRlue($oid);
        $orderPw = $orderClass->encryptOrderPassword($orderPw);

        //주문번호, 주문 비밀번호, 주문자명으로 비회원 주문 데이터 가지고 오기
        $orderDate = $orderClass->getNonMemerOrder($oid, $bname, $orderPw);
        if (empty($orderDate)) {
            $this->_setResponseResult('fail');
        } else {
            //비회원 주문 조회 경로 수정 필요! (mypage 와 통합)
            $responseData['url'] = '/member/order_nonmember.php';
            //비회원 세션 생성
            $authClass->setNonMemberSession($orderDate);
            $this->_setResponseData($responseData);
        }
    }

    /**
     * 비밀번호 변경 재알림
     */
    public function passwordContinue()
    {
        require_once constant("MALL_ROOT") . "/model/member/profile.class.php";
        require_once constant("MALL_ROOT") . "/model/store/privacy.class.php";

        $profileClass = new profile();
        $privacyClass = new privacy();

        $continueDate = date('Y-m-d H:i:s', strtotime("+ " . $_SESSION['privacy_config'][change_pw_continue_day] . " day"));

        $profileClass->updateChangePasswordDate($_SESSION['user']['code']);

        $privacyClass->setChangePasswordSession(false);
    }

    /**
     * 비밀번호 변경
     */
    public function changePassword()
    {
        require_once constant("MALL_ROOT") . "/model/member/profile.class.php";
        require_once constant('MALL_ROOT') . "/model/member/auth.class.php";
        require_once constant("MALL_ROOT") . "/model/store/privacy.class.php";

        $profileClass = new profile();
        $privacyClass = new privacy();
        $authClass = new auth();

        $pw = $this->requestFilter($_POST['pw']);
        $comparePw = $this->requestFilter($_POST['comparePw']);

        $changeType = $authClass->getChangePasswordAccessSessionType();
        $userCode = $authClass->getChangePasswordAccessSessionUserCode();

        if (empty($userCode) || $pw != $comparePw) {
            $this->_setResponseResult('false');
            return;
        }

        $encryptPw = hash("sha256", $pw);

        if ($changeType == 'sleep') { //휴면회원
            require_once constant('MALL_ROOT') . "/model/member/sleepMember.class.php";

            $sleepMemberClass = new sleepMember();

            //휴면회원 활성화
            if (!$sleepMemberClass->activeMember($userCode, "회원 로그인 시도 후 휴면 해지 진행")) {
                $this->_setResponseResult('failActiveMember');
                return;
            }

            $sleepMemberClass->setReleaseStep('complete');

            $responseData['url'] = $_SESSION['privacy_config']['sleep_user_release'];
        }

        $profileClass->updatePassword($userCode, $encryptPw);
        $profileClass->updateChangePasswordDate($userCode);
        $profileClass->insertEditHistory($userCode, 'pw', '', $encryptPw, $userCode, $_SESSION['user']['name']);

        $responseData['url'] = '/';

        if ($changeType == 'regular') { //정기 비밀번호 변경
            $privacyClass->setChangePasswordSession(false);
        } else if ($changeType == 'sleep') { //휴면회원
            $responseData['url'] = $_SESSION['privacy_config']['sleep_user_release'];
        }

        $authClass->resetChangePasswordAccessSession();

        $this->_setResponseData($responseData);
    }

    /**
     * 회원 타입 선택
     */
    public function joinSelectType()
    {
        require_once constant("MALL_ROOT") . "/model/member/join.class.php";
        $joinClass = new join();

        $joinType = $this->requestFilter($_POST['joinType']);
        $joinClass->setJoinSessionType($joinType);
    }

    public function authenticationCompany()
    {
        require_once constant("MALL_ROOT") . "/model/member/join.class.php";
        $joinClass = new join();

        $comName = $this->requestFilter($_POST['comName']);
        $comNumber1 = $this->requestFilter($_POST['comNumber1']);
        $comNumber2 = $this->requestFilter($_POST['comNumber2']);
        $comNumber3 = $this->requestFilter($_POST['comNumber3']);

        $comNumber = $comNumber1 . '-' . $comNumber2 . '-' . $comNumber3;

        //중복 체크
        if (!$joinClass->checkCompanyNumber($comNumber)) {
            $this->_setResponseResult('doubleCompanyNumber');
            return;
        }

        $data['name'] = $comName;
        $data['number'] = $comNumber;
        //회원가입 세션 등록
        $joinClass->setJoinSessionCompany($data);
    }

    /**
     * 회원가입시 약관 동의
     */
    public function joinAgreePolicy()
    {
        require_once constant("MALL_ROOT") . "/model/member/join.class.php";
        $joinClass = new join();

        $agreePolicyIxList = $this->requestFilter($_POST['policyIx']);
        $email = $this->requestFilter($_POST['email']);
        $sms = $this->requestFilter($_POST['sms']);

        $setData = array();
        if (is_array($agreePolicyIxList)) {
            foreach ($agreePolicyIxList as $ix => $val) {
                if ($val == "Y") {
                    $setData[] = $ix;
                }
            }
        }
        $joinClass->setJoinSessionAgreePolicy($setData);

        $receiveData = array();
        $receiveData['email'] = $email;
        $receiveData['sms'] = $sms;
        $joinClass->setJoinSessionReceive($receiveData);
    }

    /**
     * 회원 아이디 체크
     */
    public function userIdCheck()
    {
        require_once constant("MALL_ROOT") . "/model/member/join.class.php";
        require_once constant("MALL_ROOT") . "/model/store/storeInfo.class.php";

        $joinClass = new join();
        $storeInfoClass = new storeInfo();

        $userId = $this->requestFilter($_POST['userId']);

        $storeData = $storeInfoClass->getConfig('mall_deny_id');

        if (!$joinClass->checkUserId($userId, explode(',', $storeData['mall_deny_id']))) {
            $this->_setResponseResult('fail');
            return;
        }
    }

    /**
     * 이메일 체크
     */
    public function emailCheck()
    {
        require_once constant("MALL_ROOT") . "/model/member/join.class.php";
        $joinClass = new join();

        $email = $this->requestFilter($_POST['email']);

        if (!$joinClass->checkEmail($email)) {
            $this->_setResponseResult('fail');
        }
    }

    /**
     * 일반 회원 가입
     */
    public function joinInputBasic()
    {
        require_once constant("MALL_ROOT") . "/model/member/join.class.php";
        require_once constant("MALL_ROOT") . "/model/member/auth.class.php";
        require_once constant("MALL_ROOT") . "/model/member/mileage.class.php";
        require_once constant("MALL_ROOT") . "/model/marketing/coupon.class.php";
        require_once constant("MALL_ROOT") . "/model/store/policy.class.php";
        require_once constant("MALL_ROOT") . "/model/store/storeInfo.class.php";
        require_once constant("MALL_ROOT") . "/model/global/language.class.php";
        require_once constant('CORE_ROOT') . "/common/di.class.php";
        require_once constant("MALL_ROOT") . "/admin/logstory/class/businessLogic.class";

        $joinClass = new join();
        $authClass = new auth();
        $policyClass = new policy();
        $mileageClass = new mileage();
        $couponClass = new coupon();
        $languageClass = new language();
        $storeInfoClass = new storeInfo();
        $diClass = new di('util', 'shardMemory');

        $userId = $this->requestFilter($_POST['userId']);
        $pw = $this->requestFilter($_POST['pw']);
        $emailId = $this->requestFilter($_POST['emailId']);
        $emailHost = $this->requestFilter($_POST['emailHost']);
        $pcs1 = $this->requestFilter($_POST['pcs1']);
        $pcs2 = $this->requestFilter($_POST['pcs2']);
        $pcs3 = $this->requestFilter($_POST['pcs3']);
        $tel1 = $this->requestFilter($_POST['tel1']);
        $tel2 = $this->requestFilter($_POST['tel2']);
        $tel3 = $this->requestFilter($_POST['tel3']);
        $zip = $this->requestFilter($_POST['zip']);
        $addr1 = $this->requestFilter($_POST['addr1']);
        $addr2 = $this->requestFilter($_POST['addr2']);

        $email = $emailId . '@' . $emailHost;
        $pcs = $pcs1 . '-' . $pcs2 . '-' . $pcs3;
        $tel = $tel1 . '-' . $tel2 . '-' . $tel3;
        $pw = $authClass->encryptUserPassword($pw);

        //인증 데이터
        $authData = $authClass->getAuthSessionData();

        //몰 config 정보
        $storeData = $storeInfoClass->getConfig('mall_deny_id');

        //가입 데이터 (type, policy, receive)
        $joinData = $joinClass->getJoinSession();
        if ($joinData['type'] != 'B') { //일반회원
            $this->_setResponseResult('sessionIssue');
            return;
        } else {
            //약관 동의
            $policy = $joinData['policy'];
            //수신 동의
            $info = $joinData['receive']['email'];
            $sms = $joinData['receive']['sms'];
        }

        //인증 데이터
        if (empty($authData['ci'])) {
            $this->_setResponseResult('authIssue');
            return;
        } else {
            $name = $authData['name'];
            $ci = $authData['ci'];
            $di = $authData['di'];
            $birthday = $authData['birthday'];
            $birthdayDiv = $authData['birthdayDiv'];
            $sexDiv = $authData['sexDiv'];
            $pcs = $authData['pcs'];
        }

        //아이디 체크
        if (!$joinClass->checkUserId($userId, explode(',', $storeData['mall_deny_id']))) {
            $this->_setResponseResult('doubleId');
            return;
        }

        $memberRegRule = $diClass->shardMemory->getData('member_reg_rule');
        $authorized = "Y"; //자동승인
        if ($memberRegRule['auth_type'] != "A") {
            $authorized = "N"; //수동승인
        }

        $agentType = "W";
        if ($diClass->util->isMobile()) {
            $agentType = "M";
        }

        //set
        $registData = array();
        $registData['userId'] = $userId;
        $registData['pw'] = $pw;
        $registData['authorized'] = $authorized;
        $registData['agentType'] = $agentType;
        $registData['name'] = $name;
        $registData['ci'] = $ci;
        $registData['di'] = $di;
        $registData['birthday'] = $birthday;
        $registData['birthdayDiv'] = $birthdayDiv;
        $registData['email'] = $email;
        $registData['info'] = $info;
        $registData['pcs'] = $pcs;
        $registData['sms'] = $sms;
        $registData['tel'] = $tel;
        $registData['sexDiv'] = $sexDiv;
        $registData['zip'] = $zip;
        $registData['addr1'] = $addr1;
        $registData['addr2'] = $addr2;

        //회원 등록
        $userCode = $joinClass->registMember($registData);

        //개인정보동의 로그
        if (is_array($policy)) {
            foreach ($policy as $piIx) {
                $policyClass->insertAgreementHistory($piIx, $userCode, $registData['userId'], $registData['name'], "M");
            }
        }

        //가입시 적립금 지급
        $mileageClass->giveJoin($userCode, $languageClass->trans("회원가입 축하 지급"));

        //가입시 쿠폰 지급
        $couponClass->giveJoin($userCode, $agentType);

        //메세지 보내기
        $sendData['userName'] = $name;
        $sendData['userId'] = $userId;
        $sendData['emailAcceptanceText'] = ($info == '1' ? "수신" : "수신거부");
        $sendData['smsAcceptanceText'] = ($sms == '1' ? "수신" : "수신거부");

        $diClass->util->sendMessage('member_reg', $email, $pcs, $sendData);

        //이커머스 관련
        $bl = new BusinessLogic();
        $bl->agent_type = $agentType;
        $bl->MemberRegLogic($userCode, 6);

        //세션 삭제
        $authClass->resetAuthSession();
        $joinClass->resetJoinSession();

        $responseData['userCode'] = $userCode;
        $this->_setResponseData($responseData);
    }

    /**
     * 사업자 회원 가입
     */
    public function joinInputCompany()
    {
        require_once constant("MALL_ROOT") . "/model/member/join.class.php";
        require_once constant("MALL_ROOT") . "/model/member/auth.class.php";
        require_once constant("MALL_ROOT") . "/model/member/mileage.class.php";
        require_once constant("MALL_ROOT") . "/model/marketing/coupon.class.php";
        require_once constant("MALL_ROOT") . "/model/global/language.class.php";
        require_once constant("MALL_ROOT") . "/model/store/policy.class.php";
        require_once constant("MALL_ROOT") . "/model/store/storeInfo.class.php";
        require_once constant('CORE_ROOT') . "/common/di.class.php";
        require_once constant("MALL_ROOT") . "/admin/logstory/class/businessLogic.class";

        $joinClass = new join();
        $authClass = new auth();
        $policyClass = new policy();
        $mileageClass = new mileage();
        $couponClass = new coupon();
        $languageClass = new language();
        $storeInfoClass = new storeInfo();
        $diClass = new di('util', 'shardMemory', 'file');

        $userId = $this->requestFilter($_POST['userId']);
        $pw = $this->requestFilter($_POST['pw']);
        $name = $this->requestFilter($_POST['userName']);
        $emailId = $this->requestFilter($_POST['emailId']);
        $emailHost = $this->requestFilter($_POST['emailHost']);

        $email = $emailId . '@' . $emailHost;
        $pw = $authClass->encryptUserPassword($pw);

        $comCeo = $this->requestFilter($_POST['comCeo']);
        $comEmailId = $this->requestFilter($_POST['comEmailId']);
        $comEmailHost = $this->requestFilter($_POST['comEmailHost']);
        $comZip = $this->requestFilter($_POST['comZip']);
        $comAddr1 = $this->requestFilter($_POST['comAddr1']);
        $comAddr2 = $this->requestFilter($_POST['comAddr2']);
        $comPcs1 = $this->requestFilter($_POST['comPcs1']);
        $comPcs2 = $this->requestFilter($_POST['comPcs2']);
        $comPcs3 = $this->requestFilter($_POST['comPcs3']);
        $comPhone1 = $this->requestFilter($_POST['comPhone1']);
        $comPhone2 = $this->requestFilter($_POST['comPhone2']);
        $comPhone3 = $this->requestFilter($_POST['comPhone3']);

        $comEmail = $comEmailId . '@' . $comEmailHost;
        $comPhone = $comPhone1 . '-' . $comPhone2 . '-' . $comPhone3;
        $comPcs = $comPcs1 . '-' . $comPcs2 . '-' . $comPcs3;

        //인증 데이터
        $authData = $authClass->getAuthSessionData();

        //몰 config 정보
        $storeData = $storeInfoClass->getConfig('mall_deny_id', 'mall_data_root');

        //가입 데이터 (type, policy, receive)
        $joinData = $joinClass->getJoinSession();
        if ($joinData['type'] != 'C') { //사업자 회원
            $this->_setResponseResult('sessionIssue');
            return;
        } else {
            //약관 동의
            $policy = $joinData['policy'];
            //수신 동의
            $info = $joinData['receive']['email'];
            $sms = $joinData['receive']['sms'];
            //company
            $comName = $joinData['company']['name'];
            $comNumber = $joinData['company']['number'];
        }

        //인증 데이터
        if (empty($authData['ci'])) {
            $this->_setResponseResult('authIssue');
            return;
        } else {
            $ci = $authData['ci'];
            $di = $authData['di'];
            $birthday = $authData['birthday'];
            $birthdayDiv = $authData['birthdayDiv'];
            $sexDiv = $authData['sexDiv'];
            $pcs = $authData['pcs'];
        }

        //아이디 체크
        if (!$joinClass->checkUserId($userId, explode(',', $storeData['mall_deny_id']))) {
            $this->_setResponseResult('doubleId');
            return;
        }

        $memberRegRule = $diClass->shardMemory->getData('member_reg_rule');
        $authorized = "Y"; //자동승인
        if ($memberRegRule['b2b_auth_type'] != "A") {
            $authorized = "N"; //수동승인
        }

        $agentType = "W";
        if ($diClass->util->isMobile()) {
            $agentType = "M";
        }

        //company set
        $companyRegistData = array();
        $companyRegistData['comName'] = $comName;
        $companyRegistData['comCeo'] = $comCeo;
        $companyRegistData['comEmail'] = $comEmail;
        $companyRegistData['comNumber'] = $comNumber;
        $companyRegistData['comPhone'] = $comPhone;
        $companyRegistData['comMobile'] = $comPcs;
        $companyRegistData['comZip'] = $comZip;
        $companyRegistData['comAddr1'] = $comAddr1;
        $companyRegistData['comAddr2'] = $comAddr2;

        //사업자 가입
        $companyId = $joinClass->registCompany($companyRegistData);

        //user set
        $registData = array();
        $registData['companyId'] = $companyId;
        $registData['memType'] = 'C';
        $registData['requestInfo'] = 'C';
        $registData['userId'] = $userId;
        $registData['pw'] = $pw;
        $registData['authorized'] = $authorized;
        $registData['agentType'] = $agentType;
        $registData['name'] = $name;
        $registData['ci'] = $ci;
        $registData['di'] = $di;
        $registData['birthday'] = $birthday;
        $registData['birthdayDiv'] = $birthdayDiv;
        $registData['email'] = $email;
        $registData['info'] = $info;
        $registData['pcs'] = $pcs;
        $registData['sms'] = $sms;
        $registData['tel'] = $tel;
        $registData['sexDiv'] = $sexDiv;

        //회원 등록
        $userCode = $joinClass->registMember($registData);

        //개인정보동의 로그
        if (is_array($policy)) {
            foreach ($policy as $piIx) {
                $policyClass->insertAgreementHistory($piIx, $userCode, $registData['userId'], $registData['name'], "M");
            }
        }

        //사업자등록증 이미지
        $path = constant("MALL_ROOT") . $storeData['mall_data_root'] . "/images/basic/" . $companyId;
        $fileName = "business_file_" . $companyId . ".jpg";
        if ($diClass->file->copy($_FILES['businessFile'], $path, $fileName)) {
            $joinClass->insertCompanyFile($companyId, 'business_file', $fileName, $_FILES['businessFile']['name']);
        }

        //가입시 적립금 지급
        $mileageClass->giveJoin($userCode, $languageClass->trans("회원가입 축하 지급"));

        //가입시 쿠폰 지급
        $couponClass->giveJoin($userCode, $agentType);

        //메세지 보내기
        $sendData['userName'] = $name;
        $sendData['userId'] = $userId;
        $sendData['emailAcceptanceText'] = ($info == '1' ? "수신" : "수신거부");
        $sendData['smsAcceptanceText'] = ($sms == '1' ? "수신" : "수신거부");

        $diClass->util->sendMessage('member_reg', $email, $pcs, $sendData);

        //이커머스 관련
        $bl = new BusinessLogic();
        $bl->agent_type = $agentType;
        $bl->MemberRegLogic($userCode, 6);

        //세션 삭제
        $authClass->resetAuthSession();
        $joinClass->resetJoinSession();

        $responseData['userCode'] = $userCode;
        $this->_setResponseData($responseData);
    }

    /**
     * 인증으로 인한 회원 조회
     */
    public function searchUserByCertify()
    {
        require_once constant("MALL_ROOT") . "/model/member/auth.class.php";
        $authClass = new auth();

        $authData = $authClass->getAuthSessionData();

        $userData = $authClass->getUserDataByCi($authData['ci']);
        if (empty($userData['code'])) {
            $this->_setResponseResult('noSearchUser');
            return;
        }
    }

    /**
     * 비밀번호 찾기에서 회원 조회
     */
    public function searchUserByCertifyAndUserData()
    {
        require_once constant("MALL_ROOT") . "/model/member/auth.class.php";
        $authClass = new auth();

        $userId = $this->requestFilter($_POST['userId']);
        $userName = $this->requestFilter($_POST['userName']);

        $authData = $authClass->getAuthSessionData();

        $userData = $authClass->getUserDataByCi($authData['ci']);

        if (empty($userData['code'])) {
            $this->_setResponseResult('noSearchUser');
            return;
        }
        if ($userData['id'] != $userId || $userData['name'] != $userName) {
            $this->_setResponseResult('noMatchData');
            return;
        }

        //비밀번호 변경 권한 set
        $authClass->setChangePasswordAccessSessionType('searchPassword');
        $authClass->setChangePasswordAccessSessionUserCode($userData['code']);
    }

    /**
     * 사업자 조회
     */
    public function searchCompany()
    {
        require_once constant("MALL_ROOT") . "/model/member/auth.class.php";
        $authClass = new auth();

        $comName = $this->requestFilter($_POST['comName']);
        $comCeo = $this->requestFilter($_POST['comCeo']);
        $comNumber1 = $this->requestFilter($_POST['comNumber1']);
        $comNumber2 = $this->requestFilter($_POST['comNumber2']);
        $comNumber3 = $this->requestFilter($_POST['comNumber3']);

        $comNumber = $comNumber1 . '-' . $comNumber2 . '-' . $comNumber3;

        $comData = $authClass->searchCompany($comName, $comNumber, $comCeo);
        if (empty($comData['company_id'])) {
            $this->_setResponseResult('noSearchCompany');
            return;
        }

        $data['companyId'] = $comData['company_id'];

        //인증 세션 등록
        $authClass->setAuthSessionData($data);
    }

    /**
     * 비밀번호 찾기에서 사업자 조회
     */
    public function searchCompanyByCertifyAndCompanyData()
    {
        require_once constant("MALL_ROOT") . "/model/member/auth.class.php";
        $authClass = new auth();

        $userId = $this->requestFilter($_POST['userId']);
        $comName = $this->requestFilter($_POST['comName']);
        $comNumber1 = $this->requestFilter($_POST['comNumber1']);
        $comNumber2 = $this->requestFilter($_POST['comNumber2']);
        $comNumber3 = $this->requestFilter($_POST['comNumber3']);

        $comNumber = $comNumber1 . '-' . $comNumber2 . '-' . $comNumber3;

        $authData = $authClass->getAuthSessionData();

        $userData = $authClass->getUserDataByCi($authData['ci']);
        if (empty($userData['code'])) {
            $this->_setResponseResult('noSearchUser');
            return;
        }

        $comData = $authClass->getCompanyData($userData['company_id']);
        if ($userData['id'] != $userId || $comData['com_name'] != $comName || $comData['com_number'] != $comNumber) {
            $this->_setResponseResult('noMatchData');
            return;
        }

        //비밀번호 변경 권한 set
        $authClass->setChangePasswordAccessSessionType('searchPassword');
        $authClass->setChangePasswordAccessSessionUserCode($userData['code']);
    }

    /**
     * 휴면 계정 전환 본인인증페이지 요청
     */
    public function nextSleepMemberReleaseAuth()
    {
        require_once constant('MALL_ROOT') . "/model/member/sleepMember.class.php";

        $sleepMemberClass = new sleepMember();

        $sleepMemberClass->setReleaseStep('auth');
    }

    /**
     * 휴면 계정 전환 약관동의페이지 요청 - 일반 회원
     */
    public function nextSleepMemberReleasePolicyBasic()
    {
        require_once constant('MALL_ROOT') . "/model/member/sleepMember.class.php";
        require_once constant("MALL_ROOT") . "/model/member/auth.class.php";

        $sleepMemberClass = new sleepMember();
        $authClass = new auth();

        $authData = $authClass->getAuthSessionData();

        if (empty($authData['ci'])) {
            $this->_setResponseResult('fail');
            return;
        }

        $sleepMemberClass->setReleaseStep('policy');
    }

    /**
     * 휴면 계정 전환 약관동의페이지 요청 - 사업자 회원 회원
     */
    public function nextSleepMemberReleasePolicyCompany()
    {
        require_once constant('MALL_ROOT') . "/model/member/sleepMember.class.php";
        require_once constant("MALL_ROOT") . "/model/member/auth.class.php";

        $sleepMemberClass = new sleepMember();
        $authClass = new auth();

        $comName = $this->requestFilter($_POST['comName']);
        $comNumber1 = $this->requestFilter($_POST['comNumber1']);
        $comNumber2 = $this->requestFilter($_POST['comNumber2']);
        $comNumber3 = $this->requestFilter($_POST['comNumber3']);

        $comNumber = $comNumber1 . '-' . $comNumber2 . '-' . $comNumber3;

        $comData = $authClass->getCompanyData($_SESSION['user']['company_id']);
        if ($comData['com_name'] != $comName || $comData['com_number'] != $comNumber) {
            $this->_setResponseResult('noMatchData');
            return;
        }

        $data['companyId'] = $_SESSION['user']['company_id'];

        //인증 세션 등록
        $authClass->setAuthSessionData($data);

        $sleepMemberClass->setReleaseStep('policy');
    }

    /**
     * 비밀번호 변경
     */
    public function nextSleepMemberReleaseChangePassword()
    {
        require_once constant('MALL_ROOT') . "/model/member/sleepMember.class.php";
        require_once constant("MALL_ROOT") . "/model/member/auth.class.php";
        require_once constant("MALL_ROOT") . "/model/store/policy.class.php";

        $sleepMemberClass = new sleepMember();
        $policyClass = new policy();
        $authClass = new auth();

        $agreePolicyIxList = $this->requestFilter($_POST['policyIx']);

        //개인정보동의 로그
        if (is_array($agreePolicyIxList)) {
            foreach ($agreePolicyIxList as $ix => $val) {
                if ($val == "Y") {
                    $policyClass->insertAgreementHistory($ix, $_SESSION['user']['code'], $_SESSION['user']['id'], $_SESSION['user']['name'], "M");
                }
            }
        }

        $sleepMemberClass->setReleaseStep('password');

        //비밀번호 변경 권한 set
        $authClass->setChangePasswordAccessSessionType('sleep');
        $authClass->setChangePasswordAccessSessionUserCode($_SESSION['user']['code']);
    }

    public function sleepMemberReleaseComplete()
    {
        require_once constant('MALL_ROOT') . "/model/member/sleepMember.class.php";
        require_once constant("MALL_ROOT") . "/model/member/auth.class.php";

        $sleepMemberClass = new sleepMember();
        $authClass = new auth();

        $sleepMemberClass->resetReleaseStep();

        $authClass->sleepMemberActiveComplete();
    }
}