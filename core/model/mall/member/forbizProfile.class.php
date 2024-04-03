<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-20
 * Time: 오전 11:49
 */

require_once constant("CORE_ROOT") . "/model/mall/member/forbizMember.class.php";

class forbizProfile extends forbizMember
{
    /**
     * 비밀번호 변경일자 수정
     * @param $userCode
     * @param $date
     */
    public function updateChangePasswordDate($userCode)
    {
        $this->db->query("UPDATE common_user SET change_pw_date = NOW() WHERE code='" . $userCode . "'");
    }

    /**
     * 회원 비밀번호 수정
     * @param $userCode
     * @param $encryptPw
     */
    public function updatePassword($userCode, $encryptPw)
    {
        $this->db->query("UPDATE common_user SET pw = '" . $encryptPw . "' WHERE code='" . $userCode . "'");
    }

    /**
     * 회원정보 변경 수정 히스토리
     * @param $userCode
     * @param $columnName
     * @param $beforeEdit
     * @param $afterEdit
     * @param $chagerIx
     * @param $chagerName
     * @param string $regRrl
     */
    public function insertEditHistory($userCode, $columnName, $beforeEdit, $afterEdit, $chagerIx, $chagerName, $regRrl = "")
    {
        if (empty($regRrl)) {
            $regRrl = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        }

        switch ($columnName) {
            case 'pw';
                $columnText = "비밀번호";
                break;
            default:
                $columnText = "";
                break;
        }

        $sql = "SELECT cmd.gp_ix,
                   cu.mem_type,
                   cu.mem_div,
                   cu.id,
                   AES_DECRYPT(UNHEX(cmd.name), '" . $this->db->ase_encrypt_key . "')
                      AS name
            FROM common_user AS cu
                 INNER JOIN common_member_detail AS cmd ON (cu.code = cmd.code)
            WHERE cu.code = '" . $userCode . "'";
        $this->db->query($sql);
        $this->db->fetch();
        $memberInfo = $this->db->dt;

        $sql = "INSERT INTO common_member_edit_history(edit_date,
                                       code,
                                       gp_ix,
                                       mem_type,
                                       mem_div,
                                       name,
                                       id,
                                       column_name,
                                       column_text,
                                       before_edit,
                                       after_edit,
                                       chager_ix,
                                       chager_name,
                                       regdate,
                                       reg_url,
                                       ip)
                            VALUES (NOW(),
                                    '" . $userCode . "',
                                    '" . $memberInfo['gp_ix'] . "',
                                    '" . $memberInfo['mem_type'] . "',
                                    '" . $memberInfo['mem_div'] . "',
                                    '" . $memberInfo['name'] . "',
                                    '" . $memberInfo['id'] . "',
                                    '" . $columnName . "',
                                    '" . $columnText . "',
                                    '" . $beforeEdit . "',
                                    '" . $afterEdit . "',
                                    '" . $chagerIx . "',
                                    '" . $chagerName . "',
                                    NOW(),
                                    '" . $regRrl . "',
                                    '" . $_SERVER["REMOTE_ADDR"] . "')";
        $this->db->query($sql);
    }
}