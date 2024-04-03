<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-02-26
 * Time: 오후 6:27
 */

require_once constant("CORE_ROOT") . "/model/mall/member/forbizMember.class.php";

class forbizAuth extends forbizMember
{
    /**
     * 로그인 여부
     * @return bool
     */
    public function isLogin()
    {
        return ($_SESSION["user"]["code"] != "" ? true : false);
    }

    /**
     * id, pw 로 회원 데이터 가지고 오기
     * @param $id
     * @param $pw
     * @param string $sleepUserUseYn
     * @return mixed
     */
    public function getUserAuthDataByIdPw($id, $pw, $sleepUserUseYn = 'N')
    {
        $stropp_passwd = strtoupper($pw);   //소문자를 대문자로
        $strlow_passwd = strtolower($pw);   //대문자를 소문자로

        $where = " id='" . $id . "'";

        $where .= " and (pw='" . $this->encryptUserPassword($stropp_passwd) . "' 
        OR pw='" . $this->encryptUserPassword($strlow_passwd) . "' 
        OR pw='" . $this->encryptUserPassword($pw) . "') ";

        $sql = $this->_getUserAuthDataSql($where, false);

        //휴면 회용 사용시 쿼리 추가
        if ($sleepUserUseYn == 'Y') {
            $sql .= " UNION ";
            $sql .= $this->_getUserAuthDataSql($where, true);
        }

        $this->db->query($sql);
        $this->db->fetch();
        return $this->db->dt;
    }

    /**
     * getUserAuthDataByIdPw  에서 가지고온 데이터로 회원 세션 생성
     * @param $userAuthData
     */
    public function setUserSession($userAuthData)
    {
        $_SESSION["user"][company_id] = $userAuthData[company_id];
        $_SESSION["user"][code] = $userAuthData[code];
        $_SESSION["user"][name] = $userAuthData[name];
        $_SESSION["user"][nick_name] = $userAuthData[nick_name];
        $_SESSION["user"][mail] = $userAuthData[mail];
        $_SESSION["user"][id] = $userAuthData[id];
        $_SESSION["user"][gp_level] = $userAuthData[gp_level];
        $_SESSION["user"][gp_name] = $userAuthData[gp_name];
        $_SESSION["user"][perm] = $userAuthData[gp_level];
        $_SESSION["user"][mem_type] = $userAuthData[mem_type];
        $_SESSION["user"][gp_ix] = $userAuthData[gp_ix];
        $_SESSION["user"][sex] = $userAuthData[sex];
        $_SESSION["user"][age] = $userAuthData[age];
        $_SESSION["user"][use_mall_yn] = $userAuthData[use_mall_yn];
        $_SESSION["user"][birthday] = $userAuthData[birthday];                        //19금 사용여부를 위하여 추가 2014-02-04 이학봉

        if ($userAuthData[retail_dc]) {
            $_SESSION["user"][sale_rate] = $userAuthData[retail_dc];
        } else {
            $_SESSION["user"][sale_rate] = '0';
        }

        if ($userAuthData["shipping_dc_yn"] == "Y") {//회원등급별 배송비 kbk 13/06/17
            $_SESSION["user"]["shipping_dc_price"] = ($userAuthData["shipping_dc_price"] > 0 ? $userAuthData["shipping_dc_price"] : 0);
        } else {
            $_SESSION["user"]["shipping_dc_price"] = 0;
        }
        $_SESSION["user"][pcs] = $userAuthData[pcs];
        $_SESSION["user"][use_discount_type] = $userAuthData[use_discount_type];    //회원그룹 할인율 타입 c:카테고리할인 g:일반할인(그룹) w:품목별가격 적용
        $_SESSION["user"][round_depth] = $userAuthData[round_depth];
        $_SESSION["user"][round_type] = $userAuthData[round_type];
        $_SESSION["user"][selling_type] = $userAuthData[selling_type];            //회원그룹별 도소매 구분 소매 :R 도매:W
        $_SESSION["user"][mem_reg_date] = $userAuthData[mem_reg_date];

        $_SESSION["user"][dc_standard_price] = $userAuthData[dc_standard_price];//가격 노출 타입
        $_SESSION["user"][use_coupon_yn] = $userAuthData[use_coupon_yn];//쿠폰 사용여부
        $_SESSION["user"][use_reserve_yn] = $userAuthData[use_reserve_yn];//마일리지 사용/적립 가능여부

        if ($_SESSION['app_type'] != '' && $userAuthData[app_dc_yn] == '1') {
            $_SESSION["user"][app_dc_rate] = $userAuthData[app_dc_rate];
        }

        $_SESSION["user"][sleep_account] = $userAuthData[sleep_account];
    }

    /**
     * visit 방문횟수 +1, last 최근방문일 = now, ip 최근 방문 IP 업데이트
     * @param $code
     */
    public function updateLoginUserData($code)
    {
        $this->db->query("UPDATE common_user SET visit=visit+1, last=NOW(), ip='" . $_SERVER['REMOTE_ADDR'] . "' WHERE code='" . $code . "'");
    }

    /**
     * 회원 로그인 데이터 삭제
     * @param $connectDeleteDay
     */
    public function deleteConnectUserLog($connectDeleteDay)
    {
        //회원 접속 로그 기록 유지 기간 없을때 180일
        if (empty($connectDeleteDay) || !($connectDeleteDay > 0)) {
            $connectDeleteDay = 180;
        }

        $delete_log_time = date('Y-m-d H:i:s', strtotime("- " . $connectDeleteDay . " days"));

        //로그 기록 기준이 만료된 데이터 삭제
        $sql = "delete from common_member_connect_log where connect_time < '" . $delete_log_time . "'";
        $this->db->query($sql);
    }

    /**
     * 회원 로그인 기록 남기기
     * @param $id
     * @param $code
     * @param $connectType
     */
    public function connectUserLog($id, $code, $connectType)
    {
        $session_out_time = ini_get('session.gc_maxlifetime');    // 아무 행동하지 않았을때 세션이 만료되는 시간 가져오기
        $now_time = time(); // 현재 시간 가져오기
        $expired_time = date('Y-m-d H:i:s', $now_time + $session_out_time); // 현재 시간과 세션이 만료되는 시간을 더해 세션 종료예정 시간 등록

        if ($connectType == 'login') {
            if ($code) {
                //회원 코드값이 존재하면, 로그인 성공
                $sql = "insert into common_member_connect_log (code, id, connect_yn, connect_time, expired_time, connect_ip) values ('" . $code . "','" . $id . "','Y',NOW(),'" . $expired_time . "','" . $_SERVER["REMOTE_ADDR"] . "')";
                $this->db->query($sql);
            } else {
                $sql = "select code from common_user where id = '" . $id . "' ";
                $this->db->query($sql);
                if ($this->db->total) {
                    $this->db->fetch();
                    $user_code = $this->db->dt['code'];
                    //회원 코드 값이 존재하지 않기 때문에 접속 실패로 간주
                    $sql = "insert into common_member_connect_log (code, id, connect_yn, connect_time, expired_time, connect_ip) values ('" . $user_code . "','" . $id . "','N',NOW(),'','" . $_SERVER["REMOTE_ADDR"] . "')";
                    $this->db->query($sql);
                }
            }
        } else if ($connectType == 'logout') {
            //회원이 로그아웃 버튼 클릭으로 실제 로그아웃 했을때 기록 업데이트
            $sql = "select max(lo_ix) lo_ix from common_member_connect_log where code = '" . $code . "' and connect_yn = 'Y'";
            $this->db->query($sql);
            if ($this->db->total) {
                $this->db->fetch();
                $lo_ix = $this->db->dt['lo_ix'];

                $sql = "update common_member_connect_log set expired_time = NOW() where code = '" . $code . "' and lo_ix = '" . $lo_ix . "'";
                $this->db->query($sql);
            }
        } else if ($connectType == 'maintain') {
            //접속 시간이 유지되는 상태 즉 회원이 사이트 내에서 활동 중일때는 예정된 expired_time 이 증가 됨으로 해당 시간 값을 업데이트 한다.
            $sql = "select max(lo_ix) lo_ix from common_member_connect_log where code = '" . $code . "' and connect_yn = 'Y'";
            $this->db->query($sql);

            if ($this->db->total) {
                $this->db->fetch();
                $lo_ix = $this->db->dt['lo_ix'];

                $sql = "update common_member_connect_log set expired_time = '" . $expired_time . "' where code = '" . $code . "' and lo_ix = '" . $lo_ix . "'";
                $this->db->query($sql);
            }
        }
    }

    /**
     * 비회원 세션 생성
     * @param $orderDate
     */
    public function setNonMemberSession($orderDate)
    {
        $_SESSION['nonMember']['oid'] = $orderDate['oid'];
    }

    /**
     * 로그인 회원 정보 sql 문 (회원과 휴먼회원은 컬럼 동일해야함)
     * @param $where
     * @param bool $isSleepQuery
     * @return string
     */
    protected function _getUserAuthDataSql($where, $isSleepQuery = false)
    {
        if ($isSleepQuery) {
            $sleepAccount = 'Y';
            $userTableName = "common_user_sleep";
            $memberDetailTableName = "common_member_detail_sleep";
        } else {
            $sleepAccount = 'N';
            $userTableName = "common_user";
            $memberDetailTableName = "common_member_detail";
        }

        $sql = "SELECT
					'" . $sleepAccount . "' as sleep_account,
					cu.code,
					cu.id,
					cu.company_id,
					AES_DECRYPT(UNHEX(cmd.name),'" . $this->db->ase_encrypt_key . "') as name,
					AES_DECRYPT(UNHEX(cmd.pcs),'" . $this->db->ase_encrypt_key . "') as pcs,
					AES_DECRYPT(UNHEX(cmd.mail),'" . $this->db->ase_encrypt_key . "') as mail,
					cmd.nick_name,
					mg.gp_level,
					mg.gp_name, mg.sale_rate, cmd.gp_ix,
					cmd.sex_div as sex,
					cu.mem_type,
					cu.authorized,
					cu.is_id_auth,
					date_format(cu.date,'%Y%m%d') as mem_reg_date, 
					mg.wholesale_dc,
					mg.retail_dc,
					mg.mem_type AS mg_mem_type,
					mg.shipping_dc_yn,mg.use_discount_type,mg.round_depth,mg.round_type,
					mg.shipping_dc_price,mg.selling_type,
					mg.dc_standard_price,mg.use_coupon_yn,mg.use_reserve_yn,
					mg.app_dc_yn,mg.app_dc_rate,
					" . (date("Y") + 1) . "-date_format(birthday,'%Y') as age,
					cu.pw_issued,
					cu.pw_issued_date,
					cu.change_pw_date,
					cu.date
				FROM 
					" . $userTableName . " cu ,
					" . $memberDetailTableName . " cmd ,
					shop_groupinfo mg
				WHERE 
					cu.mem_type in ('M','F','C','A')
					and cu.code = cmd.code
					and cmd.gp_ix = mg.gp_ix
					and mg.gp_level != 0
					and " . $where;

        return $sql;
    }

    /**
     * 인증 세션 삭제
     */
    public function resetAuthSession()
    {
        $_SESSION['auth'] = "";
    }

    /**
     * 인증 모듈 set
     * @param $module
     */
    public function setAuthSessionModule($module)
    {
        $_SESSION['auth']['module'] = $module;
    }

    /**
     * 인증 모듈 get
     */
    public function getAuthSessionModule()
    {
        return $_SESSION['auth']['module'];
    }

    /**
     * 인증 정보 set
     */
    public function setAuthSessionData($data)
    {
        $_SESSION['auth']['data'] = $data;
    }

    /**
     * 인증 정보 get
     */
    public function getAuthSessionData()
    {
        return $_SESSION['auth']['data'];
    }

    /**
     * 비밀번호 변경 타입 세션 set
     */
    public function setChangePasswordAccessSessionType($type)
    {
        $_SESSION['changePasswordAccess']['type'] = $type;
    }

    /**
     * 비밀번호 변경 타입 세션 get
     */
    public function getChangePasswordAccessSessionType()
    {
        return $_SESSION['changePasswordAccess']['type'];
    }

    /**
     * 비밀번호 변경 타입 대상 회원 User code set
     */
    public function setChangePasswordAccessSessionUserCode($userCode)
    {
        $_SESSION['changePasswordAccess']['userCode'] = $userCode;
    }

    /**
     * 비밀번호 변경 타입 대상 회원 User code get
     */
    public function getChangePasswordAccessSessionUserCode()
    {
        return $_SESSION['changePasswordAccess']['userCode'];
    }

    /**
     * 비밀번호 변경 세션 삭제
     */
    public function resetChangePasswordAccessSession()
    {
        $_SESSION['changePasswordAccess'] = "";
    }

    /**
     * 회원 아이디 암호화
     * @param $pw
     * @return string
     */
    public function encryptUserPassword($pw)
    {
        return hash("sha256", $pw);
    }

    /**
     * ci 정보로 회원정보 가지고 오기
     * @param $ci
     * @return mixed
     */
    public function getUserDataByCi($ci)
    {
        $sql = "SELECT
					cu.code
					,cu.id
					,AES_DECRYPT(UNHEX(cmd.name),'" . $this->db->ase_encrypt_key . "') as name
					,cu.company_id
					,cu.date
				FROM 
					common_user cu ,
					common_member_detail cmd
				WHERE 
					cu.mem_type in ('M','C')
					and cu.code = cmd.code
					and cmd.ci = '" . $ci . "'";

        $this->db->query($sql);
        $this->db->fetch();
        return $this->db->dt;
    }

    /**
     * 사업자명, 사업자번호, 대표자명 으로 업체 조회
     * @param $comName
     * @param $comNumber
     * @param $comCeo
     * @return mixed
     */
    public function searchCompany($comName, $comNumber, $comCeo)
    {
        $sql = "SELECT
					ccd.company_id
				FROM 
					common_company_detail ccd
				WHERE 
				   ccd.com_type = 'G'
				   AND ccd.com_name = '" . $comName . "'
				   AND ccd.com_number = '" . $comNumber . "'
				   AND ccd.com_ceo = '" . $comCeo . "'";

        $this->db->query($sql);
        $this->db->fetch();
        return $this->db->dt;
    }

    /**
     * company id 로 회워 정보 가지고 오기
     * @param $companyId
     * @return mixed
     */
    public function getUserDataByCompanyId($companyId)
    {
        $sql = "SELECT
					cu.code,
					cu.id,
					cu.date
				FROM 
					common_user cu 
				WHERE 
					cu.mem_type = 'C'
					and cu.company_id = '" . $companyId . "'";

        $this->db->query($sql);
        $this->db->fetch();
        return $this->db->dt;
    }

    /**
     * 사업자 정보 가지고 오기
     * @param $companyId
     * @return mixed
     */
    public function getCompanyData($companyId)
    {
        $sql = "SELECT
					ccd.company_id
					, ccd.com_name
					, ccd.com_number
				FROM 
					common_company_detail ccd
				WHERE 
				   ccd.com_type = 'G'
				   AND ccd.company_id = '" . $companyId . "' ";

        $this->db->query($sql);
        $this->db->fetch();
        return $this->db->dt;
    }

    /**
     * 휴면회원 전환 완료로 인한 접근 처리
     */
    public function sleepMemberActiveComplete()
    {
        $_SESSION['user']['sleep_account'] = 'N';
    }
}
