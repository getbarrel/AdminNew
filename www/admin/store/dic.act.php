<?
include("../../class/database.class");

//오라클 때문에 추가
/*
$desc_trans_korea=str_replace('\\','',$desc_trans_korea);
$desc_trans_english=str_replace('\\','',$desc_trans_english);
$desc_trans_indonesian=str_replace('\\','',$desc_trans_indonesian);
*/
$db = new Database;

if ($act == "insert")
{

	if($etc == ""){
		$bank_name = $bank_name;
	}else{
		$bank_name = $etc;
	}
	if($dic_type == "WORD"){
		$sql = "select * from admin_dic where dic_type = '$dic_type' and menu_div = '$menu_div' and menu_code = '$menu_code' and language_type = '$language_type' and text_korea = '".$text_korea."'";
		$db->query($sql);

		if(!$db->total){
			$sql = "insert into admin_dic
					(dic_ix,dic_type,dic_code,menu_div,menu_code,language_type,text_korea,text_trans,desc_trans,disp,regdate)
					values
					('$dic_ix','$dic_type','$dic_code','$menu_div','$menu_code','$language_type','$text_korea','$text_trans','$desc_trans','$disp',NOW()) ";
			$db->sequences = "ADMIN_DIC_SEQ";
			$db->query($sql);
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('사전항목이 정상적으로 등록되었습니다.');</script>");
			echo("<script>document.location.href='dic.php?menu_div=$menu_div&menu_code=$menu_code&language_type=$language_type&dic_type=$dic_type';</script>");
		}else{

			echo("<script>alert('이미 사전항목 목록입니다.');</script>");
			echo("<script>document.location.href='dic.php?menu_div=$menu_div&menu_code=$menu_code&language_type=$language_type&dic_type=$dic_type';</script>");
		}
	}else{
		$sql = "select * from admin_dic where dic_type = '$dic_type' and menu_div = '$menu_div' and menu_code = '$menu_code' and language_type = '$language_type' and text_korea = '".$text_korea."' and dic_code = '$dic_code' ";
		$db->query($sql);

		if(!$db->total){
			$desc_trans = $desc_trans_korea;
			$sql = "insert into admin_dic
					(dic_ix,dic_type,dic_code,menu_div,menu_code,language_type,text_korea,text_trans,desc_trans,disp,regdate)
					values
					('$dic_ix','$dic_type','$dic_code','$menu_div','$menu_code','$language_type','$text_korea','$text_trans','$desc_trans','$disp',NOW()) ";
			$db->sequences = "ADMIN_DIC_SEQ";
			$db->query($sql);

			$desc_trans = $desc_trans_english;
			$sql = "insert into admin_dic
					(dic_ix,dic_type,dic_code,menu_div,menu_code,language_type,text_korea,text_trans,desc_trans,disp,regdate)
					values
					('$dic_ix','$dic_type','$dic_code','$menu_div','$menu_code','english','$text_korea','$text_trans','$desc_trans','$disp',NOW()) ";
			$db->sequences = "ADMIN_DIC_SEQ";
			$db->query($sql);
			$desc_trans = $desc_trans_indonesian;
			$sql = "insert into admin_dic
					(dic_ix,dic_type,dic_code,menu_div,menu_code,language_type,text_korea,text_trans,desc_trans,disp,regdate)
					values
					('$dic_ix','$dic_type','$dic_code','$menu_div','$menu_code','indonesian','$text_korea','$text_trans','$desc_trans','$disp',NOW()) ";
			$db->sequences = "ADMIN_DIC_SEQ";
			$db->query($sql);
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('사전항목이 정상적으로 등록되었습니다.');</script>");
			echo("<script>document.location.href='dic.php?menu_div=$menu_div&menu_code=$menu_code&language_type=$language_type&dic_type=$dic_type';</script>");
		}else{

			echo("<script>alert('이미 사전항목 목록입니다.');</script>");
			echo("<script>document.location.href='dic.php?menu_div=$menu_div&menu_code=$menu_code&language_type=$language_type&dic_type=$dic_type';</script>");
		}

	}


}


if ($act == "update"){

	if($dic_type == "DESC"){
		if($desc_trans_korea){

			$sql = "select * from admin_dic where dic_type='$dic_type' and dic_code='$dic_code'
					and menu_div='$menu_div' and menu_code='$menu_code'
					and language_type='korea' ";
			$db->query($sql);
			if($db->total){
				$sql = "update admin_dic set
						desc_trans='$desc_trans_korea'
						where dic_type='$dic_type' and dic_code='$dic_code'
						and menu_div='$menu_div' and menu_code='$menu_code'
						and language_type='korea' ";

				//echo nl2br($sql)."<br><br>";
				$db->query($sql);
			}else{
				$desc_trans = $desc_trans_korea;
				$sql = "insert into admin_dic
						(dic_ix,dic_type,dic_code,menu_div,menu_code,language_type,text_korea,text_trans,desc_trans,disp,regdate)
						values
						('','$dic_type','$dic_code','$menu_div','$menu_code','korea','$text_korea','$text_trans','$desc_trans','$disp',NOW()) ";
				$db->sequences = "ADMIN_DIC_SEQ";
				$db->query($sql);
			}
		}

		if($desc_trans_english){
			$sql = "select * from admin_dic where dic_type='$dic_type' and dic_code='$dic_code'
					and menu_div='$menu_div' and menu_code='$menu_code'
					and language_type='english' ";
			$db->query($sql);
			if($db->total){
				$sql = "update admin_dic set
						desc_trans='$desc_trans_english'
						where dic_type='$dic_type' and dic_code='$dic_code'
						and menu_div='$menu_div'
						and menu_code='$menu_code'
						and language_type='english' ";
				//echo nl2br($sql)."<br><br>";
				$db->query($sql);
			}else{
				$desc_trans = $desc_trans_english;
				$sql = "insert into admin_dic
						(dic_ix,dic_type,dic_code,menu_div,menu_code,language_type,text_korea,text_trans,desc_trans,disp,regdate)
						values
						('','$dic_type','$dic_code','$menu_div','$menu_code','english','$text_korea','$text_trans','$desc_trans','$disp',NOW()) ";
				$db->sequences = "ADMIN_DIC_SEQ";
				$db->query($sql);
			}
		}

		if($desc_trans_indonesian){
			$sql = "select * from admin_dic where dic_type='$dic_type' and dic_code='$dic_code'
					and menu_div='$menu_div' and menu_code='$menu_code'
					and language_type='english' ";
			$db->query($sql);
			if($db->total){
				$sql = "update admin_dic set
						desc_trans='$desc_trans_indonesian'
						where dic_type='$dic_type' and dic_code='$dic_code'
						and menu_div='$menu_div'
						and menu_code='$menu_code' and language_type='indonesian' ";
				//echo nl2br($sql)."<br><br>";
				$db->query($sql);
			}else{
				$desc_trans = $desc_trans_indonesian;
				$sql = "insert into admin_dic
						(dic_ix,dic_type,dic_code,menu_div,menu_code,language_type,text_korea,text_trans,desc_trans,disp,regdate)
						values
						('','$dic_type','$dic_code','$menu_div','$menu_code','indonesian','$text_korea','$text_trans','$desc_trans','$disp',NOW()) ";
				$db->query($sql);
			}
		}
		//echo "sql:".$sql;

	}else{
		$sql = "update admin_dic set
			dic_type='$dic_type',dic_code='$dic_code',menu_div='$menu_div',menu_code='$menu_code',language_type='$language_type',text_korea='$text_korea',
			text_trans='$text_trans',desc_trans='$desc_trans',disp='$disp',regdate=NOW()
			where dic_ix='$dic_ix' ";
		//echo $sql;
		//	exit;
		$db->query($sql);
	}



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('사전항목이 정상적으로 수정되었습니다.');</script>");
	//echo("<script>document.location.href='dic.php?menu_div=$menu_div&menu_code=$menu_code&language_type=$language_type&dic_type=$dic_type&dic_code=$dic_code';</script>");
	echo("<script>document.location.href='dic.php?dic_ix=$dic_ix';</script>");
}

if ($act == "delete"){

	$sql = "delete from admin_dic where dic_ix='$dic_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('사전항목이 정상적으로 삭제되었습니다.');</script>");
	echo("<script>document.location.href='dic.php?menu_div=$menu_div&menu_code=$menu_code&language_type=$language_type&dic_type=$dic_type';</script>");
}

?>
