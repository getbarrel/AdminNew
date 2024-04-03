<?
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;

	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");
	
	$idx = $_POST[idx];
	$company = $_POST[company];
	$email = $_POST[r_email1]."@".$_POST[r_email2];
	$name = $_POST[name];

		$db->query("SELECT * FROM tax_sales WHERE idx = '$idx'");
		$db->fetch();
		
		$publish_type		= $db->dt[publish_type];					// 발행타입 (1.매출 2.매입 3.위수탁)
		$tax_type			= $db->dt[tax_type];						// 계산서타입 (1.세금계산서 2.계산서)

		$numbering_k		= $db->dt[numbering_k];						// 권
		$numbering_h		= $db->dt[numbering_h];						// 호
		$numbering			= $db->dt[numbering];						// 일련번호
		
		$s_company_number	= $db->dt[s_company_number];				// 등록번호
		$s_company_j		= $db->dt[s_company_j];						// 종사업장
		$s_company_name		= $db->dt[s_company_name];					// 상호(업체명)
		$s_name				= $db->dt[s_name];							// 성명
		$s_address			= $db->dt[s_address];						// 사업장주소
		$s_state			= $db->dt[s_state];							// 업태
		$s_items			= $db->dt[s_items];							// 종목
		$s_personin			= $db->dt[s_personin];						// 담당자
		$s_tel				= $db->dt[s_tel];							// 연락처
		$s_email			= $db->dt[s_email];							// 이메일

		$r_company_number	= $db->dt[r_company_number];				// 등록번호
		$r_company_j		= $db->dt[r_company_j];						// 종사업장
		$r_company_name		= $db->dt[r_company_name];					// 상호(업체명)
		$r_name				= $db->dt[r_name];							// 성명
		$r_address			= $db->dt[r_address];						// 사업장주소
		$r_state			= $db->dt[r_state];							// 업태
		$r_items			= $db->dt[r_items];							// 종목
		$r_personin			= $db->dt[r_personin];						// 담당자
		$r_tel				= $db->dt[r_tel];							// 연락처
		$r_email			= $db->dt[r_email];							// 이메일

		$tax_per			= $db->dt[tax_per];							// 과세형태
		$marking			= $db->dt[marking];							// 비고
		$supply_price		= $db->dt[supply_price];					// 공급가액
		$tax_price			= $db->dt[tax_price];						// 세액
		$total_price		= $db->dt[total_price];						// 합계금액
		$cash				= $db->dt[cash];							// 현금
		$cheque				= $db->dt[cheque];							// 수표
		$pro_note			= $db->dt[pro_note];						// 어음
		$outstanding		= $db->dt[outstanding];						// 외상미수금
		$claim_kind			= $db->dt[claim_kind];						// 영수/청구 구분
		$signdate			= $db->dt[signdate];						// 등록일
		$re_signdate		= $db->dt[re_signdate];						// 수정일

		$memo				= nl2br($db->dt[memo]);
		$national_tax_no	= $db->dt[national_tax_no];
		$status				= $db->dt[status];
		


		$mail_info[mem_name] = $name;
		$mail_info[mem_mail] = $email;

		function getStr() 
		{ 
			$arr = range('A','Z');
			for($i = 0; $i < 5; $i++) 
			{ 
			$tmp = mt_rand(0,25); 
			$str .= $arr[$tmp]; 
			} 
			return $str; 
		} 

		$mail_content = "
			<link rel='stylesheet' href='http://dev.forbiz.co.kr/admin/tax/css/VAT_invoice.css' type='text/css' />
			<SCRIPT language=JavaScript src='js/png.js'></SCRIPT>
			<style type='text/css'>
			#warp	{float:left;width:600px;margin-left:50px;}
			#header	{margin:0px 0px 5px 5px;}
			/*컨텐츠*/
			/*타이틀영역*/
			#contents	{float:left;width:100%;}
			.contentsarea	{border-left:solid 1px #c3d6de;border-right:solid 1px #c3d6de;float:left;598px;}
			#contents	.warpLine	{font-size:1px;line-height:0px;height:4px;}
			#contents	.title_area	{float:left;width:598px;background:#f7fafb;}
			#contents	.title_area		ul		{float:left;width:560px;padding:14px 0 18px 18px;}
			#contents	.title_area		ul	li	{float:left;font-size:22px;font-weight:bold;color:#404040;}
			#contents	.title_area		ul	li	span	{color:#0268c1;}
			.gap01	{margin:5px 0px 0px 10px;}

			/*언더라인영역*/
			.under_line	{background:url(http://dev.forbiz.co.kr/admin/tax/mail_img/dotted_line.gif) repeat-x;height:1px; width:598px;clear:both;font-size:1px;line-height:0px;padding:0;margin:0px;}

			/*세금계산서 편지 */
			.letter	{}
			.letter		ul	{width:560px;margin:17px 0 0 20px;}
			.letter		ul	li	{line-height:150%;color:#6f6f6f;}
			.letter		ul	li	strong.approval	{color:#0268c1;text-decoration:underline;font-weight:bold;}
			.letter		ul	li	strong.refusal	{color:#cf0000;text-decoration:underline;font-weight:bold;}
			.letter_title	{font-size:14px;font-weight:bold;}
			.btns	{text-align:center;padding:28px 0;}

			/*세금계산서 정보*/
			.supplier_area	{float:left;width:598px;}	
			.supplier_area	ul	{float:left;width:560px;margin-left:20px;display:inline;margin-bottom:50px;}
			.supplier_area	ul	li	{float:left;}
			h2	{background:url(http://dev.forbiz.co.kr/admin/tax/mail_img/blue_point.gif) no-repeat 0 center;margin:10px 0px 5px 0px; }
			h2	strong	{margin-left:10px;}
			.box01	{}
			.box01	td	{border-bottom:solid 1px #e8e8e8;}
			.topLine	td	{border-top:solid 1px #e8e8e8;}
			.box01	td	div	{padding:7px 0;margin-left:10px;background:url(http://dev.forbiz.co.kr/admin/tax/mail_img/point.gif) no-repeat 0 center;}
			.box01	td	div	span	{margin-left:5px;}
			.box01	td	strong	{margin-left:10px;}
			.email	a{text-decoration:underline;color:#0268c1;}
			.title	{background:#f6f6f6;}
			.tbox01	{clear:both;width:560px;margin-left:20px;margin-bottom:20px;}

			/*세금계산서 설명*/
			.ulbox01	{background:#f6f6f6;padding:20px 0;border-top:solid 2px #e8e8e8;border-bottom:solid 2px #e8e8e8;}
			.ulbox01_1	{margin-left:30px;margin-bottom:5px;}
			.ulbox01_1	strong	{margin-left:10px;}
			.ulbox01_1	span	{margin-left:25px;}

			.point_inle	{background:url(http://dev.forbiz.co.kr/admin/tax/mail_img/dotted_line.gif) repeat-x;width:100%;height:1px;}


			/*풋터영역*/
			#footer	{clear:both;width:100%;}
			.footerarea	{padding:20px 0;}
			.footerarea	ul	{margin-left:20px;}
			.footerarea	ul	li	{line-height:140%;}
			.footeline	{background:url(http://dev.forbiz.co.kr/admin/tax/mail_img/footer_line.gif) repeat-x; height:3px;width:100%;margin-bottom:20px;}

			</style>
			";

			$mail_sql = "SELECT * FROM shop_mailsend_config WHERE mc_ix = '0018'";
			$db->query($mail_sql);
			$db->fetch();
			
			$mail_title = $db->dt[mc_mail_title];
			$mail_content .= $db->dt[mc_mail_text];
			$search_code = substr(time(),0,5).getStr().substr(time(),0,5);
			
			$mail_title = str_replace("{회사명}",$s_company_name,$mail_title);
			$mail_title = str_replace("{담당자}",$s_personin,$mail_title);

			$mail_content = str_replace("{r_company_name}",$r_company_name,$mail_content);
			$mail_content = str_replace("{s_company_name}",$s_company_name,$mail_content);
			$mail_content = str_replace("{s_personin}",$s_personin,$mail_content);
			$mail_content = str_replace("{s_company_number}",$s_company_number,$mail_content);
			$mail_content = str_replace("{s_tel}",$s_tel,$mail_content);
			$mail_content = str_replace("{s_email}",$s_email,$mail_content);
			$mail_content = str_replace("{r_company_name}",$r_company_name,$mail_content);
			$mail_content = str_replace("{r_company_number}",$r_company_number,$mail_content);
			$mail_content = str_replace("{r_personin}",$r_personin,$mail_content);
			$mail_content = str_replace("{r_tel}",$r_tel,$mail_content);
			$mail_content = str_replace("{r_email}",$r_email,$mail_content);
			$mail_content = str_replace("{signdate}",$signdate,$mail_content);
			$mail_content = str_replace("{re_signdate}",$re_signdate,$mail_content);
			$mail_content = str_replace("{supply_price}",number_format($supply_price),$mail_content);
			$mail_content = str_replace("{tax_price}",number_format($tax_price),$mail_content);
			$mail_content = str_replace("{total_price}",number_format($total_price),$mail_content);
			$mail_content = str_replace("{search_code}",$search_code,$mail_content);
			$mail_content = str_replace("{send_link}","<a href='http://".$_SERVER[HTTP_HOST]."/admin/tax/print_sales.php?idx=$idx&f_submit=Y'>",$mail_content);
	
	
			SendMail($mail_info, $mail_title, $mail_content, "");

			
?>
<script>
alert ("메일이 발송되었습니다.");
</script>