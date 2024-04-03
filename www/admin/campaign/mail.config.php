<?


function mailTargetSelect($mt_ix)
{
//global $db;

	$mdb = new Database;


	$mdb->query("SELECT * FROM shop_mailling_target ");/*where disp=1*/


	$mstring = "<Select name='mt_ix' style='font-size:12px;width:330px;' >";
	if ($mdb->total == 0)	{
		$mstring .= "<option value=''>타겟군 선택</option>";
	}else{
		if($return_type == ""){
			$mstring .= "<option value=''>타겟군 선택</option>";
			for($i=0 ; $i <$mdb->total ; $i++){
				$mdb->fetch($i);
				$mstring .= "<option value='".$mdb->dt[mt_ix]."' ".($mt_ix == $mdb->dt[mt_ix] ? " selected":"").">".$mdb->dt[target_name]."(".$mdb->dt[target_cnt]." 명)</option>";

			}
		}else{
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($mt_ix == $mdb->dt[mt_ix]){
					return $mdb->dt[target_name];
				}
			}
		}
	}

	$mstring .= "</Select>";

	return $mstring;
}


function getMailList($selected="", $return_type="",$style="")
{
//global $db;

	$mdb = new Database;


	$mdb->query("SELECT * FROM shop_mail_box where disp = 1 order by regdate desc  ");/*where disp=1*/


	$mstring = "<Select name='mail_ix' id='email_subject_select'  style='font-size:12px;width:330px;".$style."' >";
	if ($mdb->total == 0)	{
		$mstring .= "<option value=''>발송메일 선택</option>";
	}else{
		if($return_type == ""){
			$mstring .= "<option value=''>발송메일 선택</option>";
			for($i=0 ; $i <$mdb->total ; $i++){
				$mdb->fetch($i);
				$mstring .= "<option value='".$mdb->dt[mail_ix]."' ".($selected == $mdb->dt[mail_ix] ? " selected":"").">".$mdb->dt[mail_title]."</option>";

			}
		}else{
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($mt_ix == $mdb->dt[mail_ix]){
					return $mdb->dt[mail_title];
				}
			}
		}
	}

	$mstring .= "</Select>";

	return $mstring;
}
?>