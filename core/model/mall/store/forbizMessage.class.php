<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-04-02
 * Time: 오전 10:51
 */

require_once constant("CORE_ROOT") . "/model/mall/store/forbizStore.class.php";

class forbizMessage extends forbizStore
{
    public function getConfigByMcCode($mcCode)
    {
        $sql = "SELECT mc_title,
                   mc_mail_title,
                   mc_mail_text,
                   mc_sms_text,
                   mc_mail_usersend_yn,
                   mc_mail_adminsend_yn,
                   mc_sms_usersend_yn,
                   mc_sms_adminsend_yn,
                   mc_mail_chargersend_yn,
                   mc_sms_chargersend_yn,
                   kakao_alim_talk_template_code
            FROM shop_mailsend_config
            WHERE mc_code = '" . $mcCode . "'";

        $this->db->query($sql);
        $this->db->fetch();
        return $this->db->dt;
    }
}