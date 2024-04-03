<?php

$db = new Database;
if($chs_ix){
	$sql = "SELECT * FROM co_client_hostservers where chs_ix = '".$chs_ix."'  ";
}else{
	$sql = "SELECT * FROM co_client_hostservers where basic = '1' order by regdate desc limit 1  ";
}
$db->query($sql); //where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'

if($db->total){
	$db->fetch();
	$chs_ix = $db->dt[chs_ix];
	$hostserver = "www.goodss.co.kr";//$db->dt[server_url];
	$server_name = $db->dt[server_name];
}else{
	//echo "<script language='javascript'>alert('호스트 서버 설정후 판매사이트 설정이 가능합니다.');location.href='/admin/cogoods/hostserver.php';</script>";
	//exit;
}


function getHostServer($selected="", $property="onchange=\"location.href='?chs_ix='+this.value\""){
	global $admininfo;
	$mdb = new Database;

	$sql = 	"SELECT ch.*
			FROM co_client_hostservers ch
			where disp = 1 order by server_name asc ";

	$mdb->query($sql);

	$mstring = "<select name='chs_ix' id='chs_ix' $property >";
	$mstring .= "<option value=''>호스트 서버</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[chs_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[chs_ix]."' selected>".$mdb->dt[server_name]."</option>";
			}else if($mdb->dt[basic] == '1'){
				$mstring .= "<option value='".$mdb->dt[chs_ix]."' selected>".$mdb->dt[server_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[chs_ix]."'>".$mdb->dt[server_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}



function getGoodssCategoryList($category_text ="기본카테고리 선택", $object_name="cid", $onchange_handler="", $depth=0, $cid="")
{
	$mdb = new Database;
	
	
	$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

	## 한글 인자의 경우 에러가 나므로 인코딩함.

//echo "cid : ".$cid;
	if($depth == "0" || $cid){
	$categorys = (array)$soapclient->call("getGoodssCategoryList",$params = array("cid"=> $cid, "depth"=> $depth),	$options);
		//echo $co_goodsinfo;
	//print_r($categorys);
	//$useable_service = $result;
	}
	if (count($categorys)){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler style='width:140px;font-size:12px;'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < count($categorys); $i++){
			$category = (array)$categorys[$i];
			//$mdb->fetch($i);
			//echo (substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1)));
			//echo substr($cid,0,($depth+1)*3) == substr($category[cid],0,($depth+1)*3);
			if($depth ==3){
			//	echo substr($cid,0,($depth+1)*3).":::".substr($category[cid],0,($depth+1)*3)."<br>";
			}
			if(substr($cid,0,($depth+1)*3) == substr($category[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$category[cid]."' selected>".$category[cname]."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$category[cid]."' >".$category[cname]."</option>\n";
			}
		}
		$mstring = $mstring."</Select>\n";
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler validation=false  style='width:140px;font-size:12px;'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
		$mstring = $mstring."</Select>\n";
	}

	

	return $mstring;
}



//상품아이디로 이미지 불러오기...
function PrintImageForGoodss($basedir, $Pid, $type="b", $noimgType="shop"){
	global $DOCUMENT_ROOT;

	$imgdir = UploadDirText($basedir, $Pid);
	$imgpath = $basedir.$imgdir;

	$imageSrc = $imgpath."/".$type."_".$Pid.".gif";

	//if(!file_exists($DOCUMENT_ROOT.$imageSrc)) $imageSrc = $basedir."/".$noimgType."/noimg_".$type.".gif";

	return $imageSrc;
}

