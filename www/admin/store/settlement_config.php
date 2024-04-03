<?

include("../../class/database.class");
//echo $admininfo[sattle_module];

if($_GET[sattle_module]){
	if($_GET[sattle_module] == "allthegate"){
		include("alltehgate.php");
	}else if($_GET[sattle_module] == "lgdacom"){
		include("lgdacom.php");
	}else if($_GET[sattle_module] == "lguplus"){
        include("lguplus.php");
    }else if($_GET[sattle_module] == "kcp"){
		include("kcp.php");
	}else if($_GET[sattle_module] == "ksnet"){
		include("ksnet.php");
	}else if($_GET[sattle_module] == "inipay"){
		include("inipay.php");
	}else if($_GET[sattle_module] == "inipay_standard"){
        include("inipay_standard.php");
    }else if($_GET[sattle_module] == "plugnpay"){
		include("plugnpay.php");
	}else if($_GET[sattle_module] == "nicepay"){//nicepay_tx
        include("nicepay.php");
    }else if($_GET[sattle_module] == "mobilians"){
		include("mobilians.php");
	}else if($_GET[sattle_module] == "billgate"){
		include("billgate.php");
	}else if($_GET[sattle_module] == "kspay"){
		include("kspay.php");
	}else if($_GET[sattle_module] == "payline"){
		include("payline.php");
	}else if($_GET[sattle_module] == "inicis_alipay"){
		include("inicis_alipay.php");
	}else if($_GET[sattle_module] == "inicis_unionpay"){
		include("inicis_unionpay.php");
	}else if($_GET[sattle_module] == "tenpay"){
		include("tenpay.php");
	}else if ($_GET[sattle_module] == "gmopg"){
		include("gmopg.php");
	}else{
		echo "<script language='javascript'>alert('".$_GET[sattle_module]."선택된 PG 가 없습니다. PG 선택후 설정정보를 입력해주세요');document.location.href='mallinfo.php';</script>";
		exit;
	}
}else{
	if($admininfo[sattle_module] == "allthegate"){
		include("alltehgate.php");
	}else if($admininfo[sattle_module] == "lgdacom"){
		include("lgdacom.php");
	}else if($admininfo[sattle_module] == "lguplus"){
        include("lguplus.php");
    }else if($admininfo[sattle_module] == "kcp"){
		include("kcp.php");
	}else if($admininfo[sattle_module] == "ksnet"){
		include("ksnet.php");
	}else if($admininfo[sattle_module] == "inicis"){
		include("inipay.php");
	}else if($admininfo[sattle_module] == "inipay_standard"){
        include("inipay_standard.php");
    }else if($admininfo[sattle_module] == "plugnpay"){
		include("plugnpay.php");
	}else if($admininfo[sattle_module] == "nicepay"){
        include("nicepay.php");
    }else if($admininfo[sattle_module] == "nicepay_tx"){
        include("nicepay_tx.php");
    }else if($admininfo[sattle_module] == "mobilians"){
		include("mobilians.php");
	}else if($admininfo[sattle_module] == "billgate"){
		include("billgate.php");
	}else if($admininfo[sattle_module] == "kspay"){
		include("kspay.php");
	}else if($admininfo[sattle_module] == "payline"){
		include("payline.php");
	}else if($admininfo[sattle_module] == "gmopg"){
		include("gmopg.php");
	}else{
		echo "<script language='javascript'>alert('".$admininfo[sattle_module]."선택된 PG 가 없습니다. PG 선택후 설정정보를 입력해주세요');document.location.href='mallinfo.php';</script>";
		exit;
	}
}


?>
