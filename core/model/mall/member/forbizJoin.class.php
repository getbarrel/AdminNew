<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-21
 * Time: 오후 12:18
 */

require_once constant("CORE_ROOT") . "/model/mall/member/forbizMember.class.php";

class forbizJoin extends forbizMember
{
    /**
     * 회원가입 세션에 가입타입 저장
     * @param $type
     */
    public function setJoinSessionType($type)
    {
        $_SESSION['join']['type'] = $type;
    }

    /**
     * get 가입타입
     * @param $type
     */
    public function getJoinSessionType()
    {
        return $_SESSION['join']['type'];
    }

    /**
     * 회원가입 세션에 약관동의항목 저장(array(12,23,56,policy_ix) 형태)
     * @param $data
     */
    public function setJoinSessionAgreePolicy($data)
    {
        $_SESSION['join']['policy'] = $data;
    }

    /**
     * 회원가입 세션에 수신동의 set (eamil, sms)
     * @param $data
     */
    public function setJoinSessionReceive($data)
    {
        $_SESSION['join']['receive'] = $data;
    }

    /**
     * 회원가입 세션에 사업자 정보 set (comName, comNumber)
     * @param $data
     */
    public function setJoinSessionCompany($data)
    {
        $_SESSION['join']['company'] = $data;
    }

    /**
     * 회원가입 세션 삭제
     */
    public function resetJoinSession()
    {
        $_SESSION['join'] = "";
    }

    /**
     * 회원가입 세션
     * @return mixed
     */
    public function getJoinSession()
    {
        return $_SESSION['join'];
    }

    /**
     * 회원가입시 id 체크
     * @param $userId
     * @return bool
     */
    public function checkUserId($userId, $denyIdAdd = array())
    {
        $denyId = array("test", "admin", "master", "police", "webmaster", "help", "cancel", "service", "avarta", "regist", "pay", "administrator", "fuck", "sex", "roopy", "center", "email", "monitor", "helpdesk", "doumi", "helpdesk", "operate", "operator", "message", "menu", "member", "poll", "point", "communication", "comment", "manager", "management", "plan", "planning", "partner", "board", "notice", "dosirak", "dosirack", "naraadmin", "http", "ftp", "telnet", "administrator", "root", "www", "widget", "trackback", "tag", "spamfilter", "session", "rss", "page", "opage", "module", "layout", "krzip", "integration_search", "install", "importer", "file", "editor", "document", "counter", "autoinstall", "addon");

        if (is_array($denyIdAdd)) {
            $denyId = array_merge($denyId, $denyIdAdd);
        }

        for ($i = 0; $i < count($denyId); $i++) {
            if ($userId == $denyId[$i]) {
                return false;
            }
        }

        $this->db->query("SELECT code FROM common_user WHERE id='" . $userId . "'");
        if ($this->db->total > 0) {
            return false;
        }
        $this->db->query("SELECT * FROM common_dropmember WHERE id='" . $userId . "' ");
        if ($this->db->total > 0) {
            return false;
        }

        return true;
    }

    /**
     * 회원 가입시 eamil 체크
     * @param $userId
     */
    public function checkEmail($email)
    {
        $sql = "SELECT code FROM common_member_detail WHERE mail=HEX(AES_ENCRYPT('" . $email . "','" . $this->db->ase_encrypt_key . "'))";
        $this->db->query($sql);
        if ($this->db->total > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 회원 등록
     * @param $data
     * @return string
     */
    public function registMember($data)
    {
        $userCode = md5(uniqid(rand()));

        $companyId = $data['companyId'];
        $userId = $data['userId'];
        $pw = $data['pw'];
        $memType = (!empty($data['memType']) ? $data['memType'] : "M"); //회원 타입 : M:일반회원 C: 사업자 A: 직원
        $memDiv = (!empty($data['memDiv']) ? $data['memDiv'] : "D"); //S: 셀러 MD : MD담당자 D:아무도 아닌경우
        $authorized = (!empty($data['authorized']) ? $data['authorized'] : "Y"); //Y : 자동승인, N : 수동승인
        $auth = (!empty($data['auth']) ? $data['auth'] : "0");
        $requestInfo = (!empty($data['requestInfo']) ? $data['requestInfo'] : "M"); //M : 일반회원 요청
        $requestYn = (!empty($data['requestYn']) ? $data['requestYn'] : "Y"); //Y : 요청 승인
        $agentType = (!empty($data['agentType']) ? $data['agentType'] : "W"); //W : pc, M : mobile

        $sql = "INSERT INTO common_user
        (code, company_id, id, pw, mem_type, mem_div
         , authorized, auth, request_info, request_yn, agent_type
         , ip, user_agent, date, regdate_desc, last)
        VALUES ('" . $userCode . "','" . $companyId . "','" . $userId . "','" . $pw . "','" . $memType . "','" . $memDiv . "'
        ,'" . $authorized . "','" . $auth . "','" . $requestInfo . "','" . $requestYn . "','" . $agentType . "'
        ,'" . $_SERVER['REMOTE_ADDR'] . "','" . $_SERVER["HTTP_USER_AGENT"] . "',NOW(),'" . (time() * -1) . "',NOW())";
        $this->db->query($sql);

        $name = $data['name'];
        $ci = $data['ci'];
        $di = $data['di'];
        $birthday = $data['birthday'];
        $birthdayDiv = (!empty($data['birthdayDiv']) ? $data['birthdayDiv'] : "1"); //1:양력, 0:음력
        $email = $data['email'];
        $info = (!empty($data['info']) ? $data['info'] : "0"); // 1,0
        $pcs = $data['pcs'];
        $sms = (!empty($data['sms']) ? $data['sms'] : "0"); // 1,0
        $tel = $data['tel'];
        $sexDiv = (!empty($data['sexDiv']) ? $data['sexDiv'] : "M"); //M:남성, W:여성, D:기타
        $zip = $data['zip'];
        $addr1 = $data['addr1'];
        $addr2 = $data['addr2'];
        $gpIx = (!empty($data['gpIx']) ? $data['gpIx'] : "1"); //1 : 일반회원 하드코딩

        $sql = "INSERT INTO common_member_detail
		 (code, ci, di, birthday, birthday_div
		 , name, mail, pcs, tel, zip, addr1, addr2
		 , info, sms, sex_div, gp_ix, date)
		VALUES ('" . $userCode . "','" . $ci . "','" . $di . "','" . $birthday . "','" . $birthdayDiv . "'
		,HEX(AES_ENCRYPT('" . $name . "','" . $this->db->ase_encrypt_key . "'))
		,HEX(AES_ENCRYPT('" . $email . "','" . $this->db->ase_encrypt_key . "'))
		,HEX(AES_ENCRYPT('" . $pcs . "','" . $this->db->ase_encrypt_key . "'))
		,HEX(AES_ENCRYPT('" . $tel . "','" . $this->db->ase_encrypt_key . "'))
		,HEX(AES_ENCRYPT('" . $zip . "','" . $this->db->ase_encrypt_key . "'))
		,HEX(AES_ENCRYPT('" . $addr1 . "','" . $this->db->ase_encrypt_key . "'))
		,HEX(AES_ENCRYPT('" . $addr2 . "','" . $this->db->ase_encrypt_key . "'))
		,'" . $info . "','" . $sms . "','" . $sexDiv . "','" . $gpIx . "',NOW())";
        $this->db->query($sql);

        return $userCode;
    }

    /**
     * 가입 완료 데이터
     * @param $data
     * @return string
     */
    public function getJoinEndData($userCode)
    {
        $sql = "SELECT 
                cu.mem_type
                , cu.authorized
                , AES_DECRYPT(UNHEX(cmd.name),'" . $this->db->ase_encrypt_key . "') as name
                , ccd.com_name
         FROM common_member_detail cmd, common_user cu 
         LEFT JOIN common_company_detail ccd ON (ccd.company_id=cu.company_id)
         WHERE cmd.code=cu.code
         AND cu.code = '" . $userCode . "' ";
        $this->db->query($sql);
        $this->db->fetch();
        return $this->db->dt;
    }

    /**
     * 사업자 번호 중복 체크
     * @param $comNumber
     * @return bool
     */
    public function checkCompanyNumber($comNumber)
    {
        $sql = "SELECT company_id FROM common_company_detail WHERE com_number='" . $comNumber . "'";
        $this->db->query($sql);
        if ($this->db->total > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 사업자 회원 등록
     * @param $data
     * @return string
     */
    public function registCompany($data)
    {
        $companyId = md5(uniqid(rand()));

        $comName = $data['comName'];
        $sellerType = (!empty($data['sellerType']) ? $data['sellerType'] : "1"); //거래처 유형 1 : 국내매출, 2 : 국내매입, 3: 해외수입, 4:해외수출
        $comCeo = $data['comCeo'];
        $comEmail = $data['comEmail'];
        $comBusinessStatus = $data['comBusinessStatus'];
        $comBusinessCategory = $data['comBusinessCategory'];
        $onlineBusinessNumber = $data['onlineBusinessNumber'];
        $comNumber = $data['comNumber'];
        $comPhone = $data['comPhone'];
        $comMobile = $data['comMobile'];
        $comZip = $data['comZip'];
        $comAddr1 = $data['comAddr1'];
        $comAddr2 = $data['comAddr2'];
        $sellerAuth = (!empty($data['sellerAuth']) ? $data['sellerAuth'] : "Y"); //거래처 유형 N:승인대기, Y:승인, X:승인거부

        $sql = "INSERT INTO common_company_detail
		    (company_id, com_type, com_name, seller_type, com_ceo
		    , com_email, com_business_status, com_business_category, online_business_number, com_number
		    , com_phone, com_mobile, com_zip, com_addr1, com_addr2
		    , seller_auth, regdate)
            VALUES
            ('" . $companyId . "', 'G', '" . $comName . "', '" . $sellerType . "', '" . $comCeo . "'
            , '" . $comEmail . "', '" . $comBusinessStatus . "', '" . $comBusinessCategory . "', '" . $onlineBusinessNumber . "', '" . $comNumber . "'
            , '" . $comPhone . "', '" . $comMobile . "', '" . $comZip . "', '" . $comAddr1 . "', '" . $comAddr2 . "'
            , '" . $sellerAuth . "', NOW())";
        $this->db->query($sql);

        $shopName = $data['shopName'];
        $mdCode = $data['mdCode'];
        $authorized = (!empty($data['authorized']) ? $data['authorized'] : "1"); //N:승인대기, Y:승인, X:승인거부
        $sellerDate = (!empty($data['sellerDate']) ? $data['sellerDate'] : date('Y-m-d')); //거래일

        $sql = "INSERT INTO common_seller_detail 
            (company_id, shop_name, md_code, authorized, seller_date, regdate)
            VALUES
            ('" . $companyId . "', '" . $shopName . "', '" . $mdCode . "', '" . $authorized . "', '" . $sellerDate . "', NOW())";
        $this->db->query($sql);

        return $companyId;
    }

    /**
     * 사업자 파일 등록
     * @param $companyId
     * @param $sheetName
     * @param $sheetValue
     */
    public function insertCompanyFile($companyId, $sheetName, $sheetValue, $text = '')
    {
        $sql = "UPDATE common_company_file SET
            sheet_value = '" . $sheetValue . "'
            , text = '" . $text . "'
            , edit_date = NOW()
            WHERE company_id = '" . $companyId . "'
            AND sheet_name = '" . $sheetName . "'";
        $this->db->query($sql);
        if ($this->db->affected_rows() == 0) {
            $sql = "INSERT INTO common_company_file 
              (company_id, sheet_name, sheet_value, text, reg_date)
              VALUE
              ('" . $companyId . "', '" . $sheetName . "', '" . $sheetValue . "', '" . $text . "', NOW())";
            $this->db->query($sql);
        }
    }
}