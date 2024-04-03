<?php

/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-09-14
 * Time: 오후 3:43
 */
class password_validity_check{
    protected $password;
    public function __construct($password){
        $this->password = $password;

        $this->pw_combi = $_SESSION['privacy_config']['pw_combi']; // 비밀번호 조합 정보
        $this->min_pw_length = $_SESSION['privacy_config']['min_pw_length']; // 비밀번호 최소 입력 단위
        $this->max_pw_length = $_SESSION['privacy_config']['max_pw_length']; // 비밀번호 최대 입력 단위
        $this->pw_continuous_check = $_SESSION['privacy_config']['pw_continuous_check']; //비밀번호 연속문자 사용 처리 항목
        $this->pw_min_continuous_check = $_SESSION['privacy_config']['pw_min_continuous_check']; //비밀번호 연속문자 처리 개수 정보
        $this->pw_same_check = $_SESSION['privacy_config']['pw_same_check']; //비밀번호 동일문자 사용 처리 항목
        $this->pw_min_same_check = $_SESSION['privacy_config']['pw_min_same_check']; //비밀번호 동일문자 사용 개수 정보

        if(is_array($this->pw_combi)) {
            foreach ($this->pw_combi as $key => $val) {
                switch ($val) {
                    case 'number':
                        $this->check_str[] = "[0-9]";
                        $this->pw_combi_text_array[] = '숫자';
                        break;
                    case 'small':
                        $this->check_str[] = "[a-z]";
                        $this->pw_combi_text_array[] = '소문자';
                        break;
                    case 'capital':
                        $this->check_str[] = "[A-Z]";
                        $this->pw_combi_text_array[] = '대문자';
                        break;
                    case 'special':
                        $this->check_str[] = "[~!@#$%^&*()_|]";
                        $this->pw_combi_text_array[] = '특수문자';
                        break;
                    default:
                        $this->check_str[] = "";
                        $this->pw_combi_text_array[] = '';
                        break;
                }
            }
        }
    }

    public function check_pass($type='json'){
        $check_pass_ok = true;
        $check_fail_msg = "";

        $match_flag = false;

        for($i=0; $i < count($this->check_str); $i++){
            $regex = "/".$this->check_str[$i]."/";
            $match_flag = preg_match($regex,$this->password);

            if($match_flag == false){
                break;
            }
        }

        if($match_flag == false){
            $check_pass_ok = false;
            $check_fail_msg = "문자열 조합이 필요 합니다.";
        }

        if(is_array($this->pw_continuous_check) && $this->pw_min_continuous_check > 0){
            foreach($this->pw_continuous_check as $key => $val){
                switch($val){
                    case 'number':
                        $check_str = "01234567890";
                        break;
                    case 'small':
                        $check_str = "abcdefghijklmnopqrstuvwxyz";
                        break;
                    case 'capital':
                        $check_str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                        break;
                    case 'special':
                        $check_str = "~!@#$%^&*()_|";
                        break;
                }
                for($i=0; $i < $this->max_pw_length +1; $i++){

                    $check_pass_cut = substr($check_str,$i,$this->pw_min_continuous_check);

                    if(strpos($this->password,$check_pass_cut) !== false ){

                        $check_pass_ok = false;
                        $check_fail_msg = "연속적인 문자열 (".$check_pass_cut.") 은 사용하실 수 없습니다.";
                    }
                }

            }
        }

        if(is_array($this->pw_same_check) && $this->pw_min_same_check > 0){
            $same_check_text = "";
            $befor_check_text = "";
            $same_check_num = 1;
            $same_check_bool = false;
            foreach($this->pw_same_check as $key => $val) {
                switch ($val) {
                    case 'number':
                        $this->same_check_str[] = "[0-9]";
                        break;
                    case 'small':
                        $this->same_check_str[] = "[a-z]";
                        break;
                    case 'capital':
                        $this->same_check_str[] = "[A-Z]";
                        break;
                    case 'special':
                        $this->same_check_str[] = "[~!@#$%^&*()_+|]";
                        break;
                    default:
                        $this->same_check_str[] = "";
                        break;
                }

                for($i=0; $i < count($this->same_check_str); $i++){
                    $same_regex = "/".$this->same_check_str[$i]."/";
                    $match_flag = preg_match($same_regex,$this->password);

                    if($match_flag == true){
                        for ($z = 0; $z < strlen($this->password) + 1; $z++) {
                            $same_check_text = (substr($this->password, $z, 1));

                            if ($same_check_text == $befor_check_text) {
                                $same_check_num++;
                            }else{
                                $same_check_num = 1;
                            }
                            if ($same_check_num >= $this->pw_min_same_check) {
                                $check_pass_ok = false;
                                $check_fail_msg = "동일한 문자열 " . $this->pw_min_same_check . " 자리는 사용할 수 없습니다.";
                                break;
                            }
                            if(preg_match($same_regex,$same_check_text)){
                                $befor_check_text = $same_check_text;
                            }
                        }
                    }
                }
            }
        }

        if($this->min_pw_length > strlen($this->password) || $this->max_pw_length < strlen($this->password)){
            $check_pass_ok = false;
            $check_fail_msg = "".$this->min_pw_length."이상 ".$this->max_pw_length." 이하로 입력해 주세요";
        }


        $return['success'] = $check_pass_ok;
        $return['fail_msg'] = $check_fail_msg;

        if($type== 'json'){
            return json_encode($return);
        }else{
            return $return;
        }

    }
}