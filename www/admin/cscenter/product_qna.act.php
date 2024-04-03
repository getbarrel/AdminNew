<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
@include($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
//@include($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");

if(!$admininfo){
	echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert(language_data['common']['C'][language]);</script>";//'로그인후 이용하실수 있습니다'
	exit;
}

if($bbs_re_bool == "Y"){
	$act = "reply";
}

$db = new Database;

if($act == "update_cmt"){
	$sql = "update shop_product_qna_comment set cmt_contents = '".$cmt_contents."' where cmt_ix = '".$cmt_ix."'";
	$db->query($sql);

	echo("<script>top.document.location.href='./product_qna.modify.php?bbs_ix=".$bbs_ix."'</script>");
	exit;
}

if($act == "insert_cmt"){

	$sql = "update shop_product_qna set bbs_re_cnt = bbs_re_cnt+1 where bbs_ix = '".$bbs_ix."'";
	$db->query($sql);

	$db->query("insert into shop_product_qna_comment (bbs_ix, mem_ix, cmt_name, cmt_contents, cmt_ip_addr, regdate) 
												values('".$bbs_ix."','".$mem_ix."','".$cmt_name."','".$cmt_contents."','".$_SERVER['REMOTE_ADDR']."',NOW())");

	$sql = "select 
				*,
				(select pd.div_name from shop_product_qna_div pd  where pd.ix  = bbs_div) div_name ,
				(select cm.cmt_contents from shop_product_qna_comment cm where cm.bbs_ix = bbs_ix order by cmt_ix desc limit 1) cmt_contents
		  from 
		  	shop_product_qna where bbs_ix = '".$bbs_ix."' ";

	$db->query($sql);
	$db->fetch();
    $bbs_email_return = $db->dt['bbs_email_return'];

    if($bbs_email_return == 1){
		#답변 메일 처리 필요
        $mail_info['mem_mail'] = $db->dt['bbs_email'];
        $mail_info['regdate'] = $db->dt['regdate'];
        $mail_info['pname'] = $db->dt['pname'];
        $mail_info['div_name'] = $db->dt['div_name'];
        $mail_info['bbs_subject'] = $db->dt['bbs_subject'];
        $mail_info['bbs_contents'] = $db->dt['bbs_contents'];
        $mail_info['regist_date'] = date('Y-m-d');
        $mail_info['cmt_contents'] = $db->dt['cmt_contents'];

        sendMessageByStep('product_inquiry_reply', $mail_info);
	}


	
	echo("<script>top.document.location.href='./product_qna.modify.php?bbs_ix=".$bbs_ix."'</script>");
	exit;
}

if($act == "delete_cmt"){
	$sql = "update shop_product_qna set bbs_re_cnt = bbs_re_cnt-1 where bbs_ix = '".$bbs_ix."'";
	$db->query($sql);

	$sql = "delete from shop_product_qna_comment where cmt_ix = '".$cmt_ix."'";
	$db->query($sql);

	echo("<script>top.document.location.href='./product_qna.modify.php?bbs_ix=".$bbs_ix."'</script>");
	exit;
}

if ($act == "insert")
{
	$sql = "insert into ".TBL_SHOP_PRODUCT_QNA." (bbs_ix,pid, ucode,bbs_subject,bbs_id,bbs_pass,bbs_contents,bbs_hit,regdate)
	values
	('$bbs_ix','$pid','".$user[code]."','$bbs_subject','$bbs_id','$bbs_pass','$bbs_contents','0',NOW())";
	$db->sequences = "SHOP_GOODS_QNA_SEQ";
	$db->query($sql);
/*
	$db->query("SELECT ucode FROM shop_product WHERE id='$pid'");
	$db->fetch();

	if($db->total){
		$ucode = $db->dt[ucode];
		$sms_msg = "구매자의 문의내역이 있습니다. 확인하시기 바랍니다";
		send_sms($db, $sms_msg, $ucode);
	}
*/
	echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('제품 문의가 정상적으로 입력되었습니다.');window.close();</script>";
}


if($act == "update"){

	$sql = "update ".TBL_SHOP_PRODUCT_QNA." set
				bbs_hidden = '$bbs_hidden',
				bbs_div = '$bbs_div'
			where
				bbs_ix='".$bbs_ix."' ";
	$db->query($sql);

	echo "<script language='javascript'>alert('정상적으로 수정 되었습니다.');top.location.href='./product_qna.modify.php?bbs_ix=".$bbs_ix."'</script>";
	exit;
}


if($act == "reply" || $act == "reply2"){

	$sql = "update ".TBL_SHOP_PRODUCT_QNA." set
				bbs_response_title='$bbs_response_title',
				bbs_response='$bbs_response',
				response_id = '".$user[id]."', 
				response_date = NOW(), 
				bbs_re_bool = 'Y'
			where
				bbs_ix='$bbs_ix'  ";
	$db->query($sql);
	
	//제휴사 Qna 연동
	if(function_exists('sellerToolAnswerProductQna')){
		sellerToolAnswerProductQna($bbs_ix);
	}

	//$db->query("commit");

	if($act == "reply"){	// 데브에서 show_alert 이 안먹힘 
		echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>alert('답변이 정상적으로 처리되었습니다.-');window.opener.document.location.reload();self.close();</script>";
	}else{
		echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>alert('답변이 정상적으로 처리되었습니다. ');window.opener.document.location.reload();self.close();</script>";
	}
}


if($act == "delete"){

	$sql = "delete from ".TBL_SHOP_PRODUCT_QNA." where bbs_ix= '$bbs_ix'  ";
	$db->query($sql);

	$sql = "delete from shop_product_qna_comment where bbs_ix= '$bbs_ix'  ";
	$db->query($sql);
	
	if($mmode == "pop"){
		echo "<script language='javascript'>alert('정상적으로 삭제 되었습니다.');window.close();opener.document.location.reload();</script>";
	}else{
		echo "<script language='javascript'>alert('정상적으로 삭제 되었습니다.');window.close();parent.document.location.reload();</script>";
	}
	exit;
}


if($act == "response_delete"){

	$sql = "update ".TBL_SHOP_PRODUCT_QNA." set
		bbs_response='', response_id = '', response_date = '', bbs_re_bool = 'N'
		where bbs_ix='$bbs_ix'  ";
	$db->query($sql);

	//$db->query("commit");

	echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('답변이 정상적으로 삭제 되었습니다.');parent.document.location.reload();</script>";
}

//************SMS *************
if ($update_kind == "sms"){
	
	if($send_time_type == "1"){
		$send_time = $send_time_sms." ".$send_time_hour.":".$send_time_minite.":00";
	}else{
		$send_time = 0;
	}
	
	$cominfo = getcominfo();
	$sdb = new Database;
	$s = new SMS();
	$s->send_phone = $cominfo[com_phone];
	$s->send_name = $cominfo[com_name];
	$s->admin_mode = true;
	$s->send_type = $send_type;
	$s->send_date = substr($sFromYY,2,2).$sFromMM.$sFromDD;
	$s->send_time = $send_time;
	$s->send_title	=	$lms_title;
	
	if($update_type == 2){// 선택회원일때
		for($i=1; $i <count($code);$i++){
				$bbs_ix = $code[$i];
/*
				if($db->dbms_type == "oracle"){
					$sql = "select AES_DECRYPT(cmd.pcs,'".$db->ase_encrypt_key."') as pcs,
							AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, cu.id
							from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code
							where cu.code ='".$code[$i]."'";
				}else{
					$sql = "select AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
							AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, 
							cu.id , cmd.code
							from ".TBL_COMMON_USER." cu 
							LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code								
							where cu.code ='".$code[$i]."'  ";
				}
*/
				$sql = "select 
							pq.*,
							ccd.com_mobile,
							p.pname,
							ccd.com_ceo
						from
							".TBL_SHOP_PRODUCT_QNA." as pq
							inner join shop_product as p on (pq.pid = p.id)
							inner join common_company_detail as ccd on (p.admin = ccd.company_id)
						where
							1
							pq.bbs_ix = '".$bbs_ix."'";

				$db->query($sql);
				$db->fetch();

				$mc_sms_text = str_replace("{id}",$db->dt[id],$sms_text);
				$mc_sms_text = str_replace("{name}",$db->dt[name],$mc_sms_text);
				//$mc_sms_text = str_replace("{site}",$db->dt[name],$mc_sms_text);

				//echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
				$s->dest_phone = str_replace("-","",$db->dt["pcs"]);
				$s->dest_name = $db->dt["name"];
				$s->dest_code = $db->dt['code'];
				$s->msg_body =$mc_sms_text;					
				
				$s->sendbyone($admininfo);
		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원 전체에게 SMS 가 발송 되었습니다.');</script>");
	}else{// 검색회원일때
		
			if(!$max){
				$max = 100;
			}
			if ($sms_send_page == ''){
				$start = 0;
				$sms_send_page  = 1;
			}else{
				$start = ($sms_send_page - 1) * $max;
			}

			$where = " and pq.bbs_ix <> '0' ";

			if($mmode == "personalization"){
				$where .= " and pq.ucode = '".$mem_ix."' ";
			}

			if($search_type != "" && $search_text != ""){
				$where .= " and $search_type LIKE '%$search_text%' ";
			}
			if($re_bool != ""){
				if($re_bool == "Y"){
					$where .= " and pq.bbs_re_bool = 'Y' ";
				}else if($re_bool == "N"){
					$where .= " and pq.bbs_re_bool = 'N' ";
				}else{

				}
			}

			if($_REQUEST[regdate] == "1"){

				$sdate = str_replace("-","",$_REQUEST[sdate]);
				$edate = str_replace("-","",$_REQUEST[edate]);

				if($sdate != "" && $edate != ""){
					$where .= " and  date_format(pq.regdate, '%Y%m%d') between  $sdate and $edate ";
				}
			}

		
			/*
			
			$sql = "select count(*) as total
							from ".TBL_COMMON_USER." cu 
							inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code) 
							left join ".TBL_SHOP_GROUPINFO." as mg on (cmd.gp_ix = mg.gp_ix)
							left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (ccd.company_id = cu.company_id)
							left join shop_event_applicant as ea on (ea.mem_ix = cmd.code)
							$where  ";

			if($db->dbms_type == "oracle"){
				$sql = "select AES_DECRYPT(cmd.pcs,'".$db->ase_encrypt_key."') as pcs,
						AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, cu.id
						from ".TBL_COMMON_USER." cu , 
						".TBL_COMMON_MEMBER_DETAIL." cmd
						left join ".TBL_SHOP_GROUPINFO." as mg on (cmd.gp_ix = mg.gp_ix)
						left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (ccd.company_id = cu.company_id)
						left join shop_event_applicant as ea on (ea.mem_ix = cmd.code)
						$where
						ORDER BY cmd.date_ DESC
						limit $start,$max  ";
			}else{
				$sql = "select AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
						AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id
						from ".TBL_COMMON_USER." cu 
						inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code) 
						left join ".TBL_SHOP_GROUPINFO." as mg on (cmd.gp_ix = mg.gp_ix)
						left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (ccd.company_id = cu.company_id)
						left join shop_event_applicant as ea on (ea.mem_ix = cmd.code)
						$where
						ORDER BY cmd.date DESC
						limit $start,$max  ";
			}
			//echo nl2br($sql);
			//exit;
			*/
			if($admininfo[admin_level] == 9){
				$sql = "select 
							pq.*,
							ccd.com_mobile,
							p.pname,
							ccd.com_ceo
						from
							".TBL_SHOP_PRODUCT_QNA." as pq
							inner join shop_product as p on (pq.pid = p.id)
							inner join common_company_detail as ccd on (p.admin = ccd.company_id)
						where
							1
							$where 
							order by regdate desc limit $start, $max";
			}else{
				$sql = "select 
							pq.*,
							ccd.com_mobile,
							p.pname,
							ccd.com_ceo
						from
							".TBL_SHOP_PRODUCT_QNA." as pq
							inner join shop_product as p on (pq.pid = p.id)
							inner join common_company_detail as ccd on (p.admin = ccd.company_id)
						where
							1
							and company_id = '".$admininfo["company_id"]."'
							order by  regdate desc limit $start, $max ";
			}

			$db->query($sql);
			$db->fetch();
			$total = $db->total;

			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);

				$mc_sms_text = str_replace("{id}",$db->dt[id],$sms_text);
				$mc_sms_text = str_replace("{name}",$db->dt[name],$mc_sms_text);

				//echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
				$s->dest_phone = str_replace("-","",$db->dt["com_mobile"]);
				$s->dest_name = $db->dt["com_ceo"];
				$s->msg_body =$mc_sms_text;

				$s->sendbyone($admininfo);
				

			}

			if($total > ($start+$max)){
				echo("<script language='javascript' src='/admin/js/jquery-1.8.3.js'></script>
				<script> 
				$('#sended_sms_cnt', parent.document).html('".($start+$max)."');
				$('#remainder_sms_cnt', parent.document).html('".($total-($start+$max))."');
				
				if(!$('form[name=list_frm]',parent.document).find('input[name=stop]').is(':checked')){
					$('form[name=list_frm]',parent.document).find('input[name=sms_send_page]').val('".($sms_send_page+1)."');
					$('#confirm_bool', parent.document).val(0);
					$('form[name=list_frm]',parent.document).submit();
				}
				</script>");
			}else{
				echo("<script language='javascript' src='/admin/js/jquery-1.8.3.js'></script>
				<script language='javascript' src='../_language/language.php'></script>
				<script>
				$('#sended_sms_cnt', parent.document).html('".($total)."');
				$('#remainder_sms_cnt', parent.document).html(0);
				//parent.document.getElementById('sended_sms_cnt').innerHTML = '".($total)."';
				//parent.document.getElementById('remainder_sms_cnt').innerHTML = '0';
				alert('".$total." '+language_data['member_batch.act.php']['F'][language]);//건의 SMS 가 정상적으로 발송되었습니다
				</script>");
			}
	}
}



?>

