<?
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");

$db = new Database;

if($act=="manual_logout"){
	session_unregister("user");
	session_unregister("URL");

	$sql = "delete from shop_cart where cart_key = '".session_id()."' and charger_ix = '".$_SESSION["admininfo"]["charger_ix"]."'";
	$db->query($sql);
	echo("<script>parent.location.reload();</script>");
	exit;
}

if($act=="manual_login"){

	$stropp_passwd=strtoupper($admin_pw);	//소문자를 대문자로
	$strlow_passwd=strtolower($admin_pw);	//대문자를 소문자로

	$query="(pw='".crypt($stropp_passwd,"mo")."' OR pw='".crypt($strlow_passwd,"mo")."' ";
	$query.="OR pw='".md5($stropp_passwd)."' OR pw='".md5($strlow_passwd)."'";
	$query.="OR pw='".hash("sha256", $stropp_passwd)."' OR pw='".hash("sha256", $strlow_passwd)."' OR pw='".hash("sha256", $admin_pw)."' OR pw='".hash("sha256", md5($admin_pw))."' OR pw='".md5(hash("sha256",$admin_pw))."')";
	
	//관리자 비밀번호 체크!!
	$sql="select * from common_user where code='".$_SESSION["admininfo"]["charger_ix"]."' and $query ";
	$db->query($sql);
	if(!$db->total){
		echo("<script>alert('관리자 비밀번호가 틀립니다. 다시한번 확인해주세요.');</script>");
		exit;
	}
	
	session_unregister("user");
	session_unregister("URL");

	if($nonmember!="Y"){//회원 선택시!
		$sql = "SELECT 
			cu.code,cu.id, cu.company_id, 
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
			cmd.nick_name,
			AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
			mg.gp_level,mg.mall_ix,mg.gp_name, mg.sale_rate, cmd.gp_ix,
			cmd.sex_div as sex, cu.mem_type, cu.authorized, cu.is_id_auth, 
			mg.wholesale_dc, mg.retail_dc,
			mg.mem_type AS mg_mem_type,
			mg.shipping_dc_yn,mg.use_discount_type,mg.round_depth,mg.round_type,
			mg.shipping_dc_price,
			".(date("Y")+1)."-date_format(birthday,'%Y') as age,
			cmd.niceid_di,cmd.ipin_safekey
		FROM 
			".TBL_COMMON_USER." cu ,
			".TBL_COMMON_MEMBER_DETAIL." cmd ,
			".TBL_SHOP_GROUPINFO." mg
		WHERE 
			cu.code=TRIM('".$charger_ix."') and cu.mem_type in ('M','F','C','A') 
			and cu.code = cmd.code
			and cmd.gp_ix = mg.gp_ix
			and mg.gp_level != 0";

		$db->query($sql);
		if(!$db->total){
			echo("<script>alert('회원 로그인에 실패하였습니다. 다시 시도해주세요.');</script>");
			exit;
		}

		$db->fetch();

		if($db->dt[is_id_auth] != "Y"){
			echo("<script>alert('이메일 인증이 필요한 회원입니다.');</script>");
			exit;
		}
		
		if($db->dt[authorized] == "Y"){

			$_SESSION["user"][company_id]  = $db->dt[company_id];
			$_SESSION["user"][code]  = $db->dt[code];
			$_SESSION["user"][name]  = $db->dt[name];
			$_SESSION["user"][nick_name]  = $db->dt[nick_name];
			$_SESSION["user"][mail]  = $db->dt[mail];
			$_SESSION["user"][id]    = $db->dt[id];
			$_SESSION["user"][gp_level]   = $db->dt[gp_level];
			$_SESSION["user"][gp_name]   = $db->dt[gp_name];
			$_SESSION["user"][perm]   = $db->dt[gp_level];
			$_SESSION["user"][mem_type] = $db->dt[mem_type];
			$_SESSION["user"][gp_ix] = $db->dt[gp_ix];
			$_SESSION["user"][sex] = $db->dt[sex];
			$_SESSION["user"][age] = $db->dt[age];
			$_SESSION["user"][use_mall_yn]   = $db->dt[use_mall_yn];
			$_SESSION["user"][birthday] = $db->dt[birthday];						//19금 사용여부를 위하여 추가 2014-02-04 이학봉
			//$_SESSION["user"][sale_rate]   = $db->dt[sale_rate];
			if($db->dt["mg_mem_type"]=="M") {//회원 타입에 따라서 할인율 적용 kbk 13/06/14
				$_SESSION["user"][sale_rate]   = $db->dt[retail_dc];
			} else {
				$_SESSION["user"][sale_rate]   = $db->dt[wholesale_dc];
			}
			if($db->dt["shipping_dc_yn"]=="Y") {//회원등급별 배송비 kbk 13/06/17
				$_SESSION["user"]["shipping_dc_price"] = ($db->dt["shipping_dc_price"] > 0 ? $db->dt["shipping_dc_price"]:0);
			} else {
				$_SESSION["user"]["shipping_dc_price"] = 0;
			}
			$_SESSION["user"][pcs] = $db->dt[pcs];
			$_SESSION["user"][use_discount_type] = $db->dt[use_discount_type];	//회원그룹 할인율 타입 c:카테고리할인 g:일반할인(그룹) w:품목별가격 적용
			$_SESSION["user"][round_depth] = $db->dt[round_depth];
			$_SESSION["user"][round_type] = $db->dt[round_type];

		}else if($db->dt[authorized] == "X"){
			echo("<script>alert('승인 거절 회원입니다.</script>");
			exit;
		}else{
			echo("<script>alert('승인 대기 회원입니다.');</script>");
			exit;
		}

	}


	$db->query("SELECT mall_ename, mall_domain FROM shop_shopinfo  WHERE  mall_ix = '".$db->dt[mall_ix]."' ");
	if($db->total){
		$db->fetch();
		$_SESSION["user"]["group_site_type"]   = $db->dt[mall_ename];
		$FROM = "http://".$db->dt[mall_domain];
	}else{
		$FROM = '/';
	}


	$_SESSION["user"]["manual_order"] = "Y";

	echo("<script>parent.approve_manual_order('".$FROM."');</script>");
}

?>