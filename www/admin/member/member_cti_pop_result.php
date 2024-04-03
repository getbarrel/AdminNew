<?php
	/* CTI_POP 2014-06-12 JBG  
	*  오류나 수정사항 많을수 있습니다.
	*  수정시 주석부탁 드립니다 ~_~
	*/ 
	include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
	//include($_SERVER["DOCUMENT_ROOT"]."admin/include/admin.util.php");
	
	$db = new Database;
	$mdb = new Database;

	function align_tel($telNo) { 
        $telNo = preg_replace('/[^\d\n]+/', '', $telNo);
        if(substr($telNo,0,1)!="0" && strlen($telNo)>8) $telNo = "0".$telNo;
        $Pn3 = substr($telNo,-4);
        if(substr($telNo,0,2)=="01") $Pn1 =  substr($telNo,0,3);
        elseif(substr($telNo,0,2)=="02") $Pn1 =  substr($telNo,0,2);
        //elseif(substr($telNo,0,1)=="0") $Pn1 =  substr($telNo,0,3);
        $Pn2 = substr($telNo,strlen($Pn1),-4);
        if(!$Pn1) return $Pn2."-".$Pn3;
        else return $Pn1."-".$Pn2."-".$Pn3;
	}
	
	//전화번호 하이픈 처리함수사용 align_tel()
	if($tel){
		$tel	=	align_tel($tel);
	}
	if($search_type == 'tel'){
		$tel_length = strlen($search_text);
		if($tel_length == 11 || $tel_length == 12){
			$search_text = align_tel($search_text);
		}
	}

?>
<script language='JavaScript' src='../js/jquery-1.4.js'></Script>
<script type="text/javascript">
function crmChange(code,tel,mode){
	
	//팝업창에서 선택한 전화번호 입력
	$(top.document).find('input[name=TELNUM]').val(tel);
	//팝업창 닫기
	$('.close').click();
	//CRM 아이프레임에 code값 삽입 및 리로드
	//$(top.document).find('#member_crm').attr("src","member_crm.php?code="+ code +"&mem_ix=" + code);
	top.document.location.href='member_cti.php?code=' + code + '&mem_ix=' + code + '&con_view=' + mode;

}
</script>
<style type="text/css">
	body {margin:0px; padding:0px;}
	body,p,h1,h2,h3,h4,h5,h6,ul,ol,li,dl,dt,dd,table,th,td,form,fieldset,legend,input,textarea,button{margin:0;padding:0;font-size:12px;font-family:Dotum,Arial;color:#666;}
	h1,h2,h3,h4,h5,h6	{font-size:12px;}
	img,fieldset{border:0px;}
	ul,li,ol{list-style:none;}
	a{text-decoration:none;} a:link {color:#181818;} a:hover {text-decoration:underline;color:#585858;} a:visited {color:#181818;}
	em,address{font-style:normal}
	.nobr{text-overflow:ellipsis; overflow:hidden;white-space:nowrap;}
	table	{ border-collapse:collapse;table-layout:fixed;}
	td,th	{padding:0;margin:0;}
	input,label	{vertical-align:middle;border:0;}
	label {cursor:pointer;}
	
	.cti_layout_wrap {width:1100px; min-height:700px; background:#fff;}
	.cti_pop_state {width:1040px; height:130px; background:#424e69; margin:0px auto; margin-bottom:30px;}
	.cti_pop_state dl:after {content:''; display:block; clear:both;}
	.cti_pop_state dl dt {float:left; margin:0px 30px 0 26px; display:inline; line-height:0; font-size:0px;}
	.cti_pop_state dl dd {float:left;}
	#facebox .close img {opacity:1;}
	#facebox .close {top: 27px;right: 32px;}
	.cti_layout_wrap h3 {padding-top:30px; margin:0px 30px 20px; background:url('../images/cti_poptitle_background.png') 0 bottom repeat-x;}
	.cti_pop_state_type1 {margin-top:37px; margin-right:56px;}
	.cti_pop_state_top {line-height:0px; font-size:0px; margin-bottom:15px;}
	.cti_pop_state_top2 {color:#fefeff; font-size:24px;}
	.cti_pop_state_top2 img{margin-left:12px; margin-top:-4px !important;}
	.cti_pop_state_type2:after {content:''; display:block; clear:both;}
	.cti_pop_state_type2 ul {float:left;}
	.cti_pop_state_type2 ul li {float:left; margin-top:34px; display:inline; cursor:pointer;}
	.cti_pop_table_wrap {margin-left:30px; margin-right:30px; padding-top:30px;}
	.cti_pop_table_wrap h4 {margin-bottom:14px;}
	.cti_pop_table_wrap h5 {margin-bottom:14px; margin-top:37px;}
	.pop_top_bottom {margin-bottom:14px; margin-top:27px;}
	.cti_pop_table_search {background:#f0f0f0; width:100%; height:52px;}
	.cti_pop_table_search dl:after {content:''; display:block; clear:both;}
	.cti_pop_table_search dl dt {height:52px; line-height:52px; color:#363636; font-weight:bold; float:left;display:inline;}
	.cti_pop_table_search dl dt span {margin-left:27px;}
	.cti_pop_table_search dl dd {margin-left:52px; float:left; display:inline;}
	.cti_pop_table_search dl dd ul {float:left;}
	.cti_pop_table_search dl dd ul li {float:left; margin-top:12px; display:inline;}
	.cti_pop_table_li1 {width:138px; height:26px; border:1px solid #cccccc; background:#fff; margin-right:5px;}
	.cti_pop_table_li2 {width:233px; height:26px; border:1px solid #ccc; background:#fff; margin-right:10px; position:relative;}
	.cti_pop_table_li2 img {position:relative; cursor:pointer; top:3px;}
	.cti_pop_table_li3 {cursor:pointer;}
	.cti_table_list_1 {overflow-y:scroll; height:194px;}
	.cti_table_list_1 table {border-top:1px solid #cccccc;}
	.cti_table_list_1 table tr th {border-bottom:1px solid #e5e5e5; background:#f0f0f0; text-align:center; height:32px; font-weight:bold; color:#363636;}
	.cti_table_list_1 table tr td {border-bottom:1px solid #e5e5e5; text-align:center; height:32px; color:#363636;}
	.cti_table_list_1 table tr td span {color:#ff4c3e; font-weight:bold;}
	.cti_table_list_1 #page_area table tr td span {color:#252525; font-weight:bold;}
	.cti_table_list_1 #page_area table tr td input{border:1px solid #dadada; padding:5px;}
	.cti_table_list_1 table tr td img {cursor:pointer;}
	.cti_table_list_2 table tr td {height:49px !important;}
</style>
<h5 class='pop_top_bottom'><img src="../images/collpop_title_02.png" alt="회원정보" /></h5>
		<div class='cti_table_list_1'>
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<col width='150'>
				<col width='98'>
				<col width='*'>
				<col width='105'>
				<col width='140'>
				<col width='103'>
				<col width='150'>
				<col width='120'>
				<tr>
					<th>
						이름 (성별)
					</th>
					<th>
						ID
					</th>
					<th>
						회원유형
					</th>
					<th>
						회원등급
					</th>
					<th>
						회원레벨
					</th>
					<th>
						휴대폰
					</th>
					<th>
						연락처
					</th>
					<th>
						관리
					</th>
				</tr>
			<?php
				//검색조건이있을때
				if($search_type && $search_text){
					
					if($search_type == "tel"){
						$search_text = align_tel($search_text);
						$where = " AND AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' OR AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
					}else if($search_type == "name"){
						$where = " AND AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
					}else if($search_type == "id"){
						$where = " AND cu.id LIKE  '%$search_text%' ";
					}
					

					$sql = "SELECT 
								AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs ,
								AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel ,cmd.gp_ix, cmd.level_ix, cu.id , cu.code , cu.mem_type
							FROM 
								common_member_detail cmd
							LEFT JOIN
								common_user cu
							ON (cmd.code = cu.code)
							WHERE 1 $where
							LIMIT 0,5
							";

					$db->query($sql);
					$result = $db->fetchall();

					if($search_type == "oid"){
						$where = " AND oid = '$search_text' ";

						$sql = "SELECT
									uid
								FROM
									shop_order
								WHERE 1 $where
								LIMIT 0,5
								";
						$db->query($sql);
						$db->fetch();
						$code	=	$db->dt['uid'];
						
						$where = " AND cmd.code = '$code'";

						$sql = "SELECT 
								AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs ,
								AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel ,cmd.gp_ix, cmd.level_ix, cu.id , cu.code , cu.mem_type
							FROM 
								common_member_detail cmd
							LEFT JOIN
								common_user cu
							ON (cmd.code = cu.code)
							WHERE 1 $where
							LIMIT 0,5
							";
						$db->query($sql);
						$result = $db->fetchall();

					}

				}else{
					//전화유입되엇을떄
					if($tel){
					//기본값일때 유입전화번호로 데이터 검색
					$where = " AND AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') LIKE  '%$tel%' OR AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') LIKE  '%$tel%' ";

					$sql = "SELECT 
								AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs ,
								AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel ,cmd.gp_ix, cmd.level_ix, cu.id , cu.code , cu.mem_type
							FROM 
								common_member_detail cmd
							LEFT JOIN
								common_user cu
							ON (cmd.code = cu.code)
							WHERE 1 $where
							LIMIT 0,5
							";
					$db->query($sql);
					$result = $db->fetchall();

					}
				}
					
					

					if($result){

						foreach($result as $val){

						if($val['mem_type'] == "M"){
							$val['mem_type'] = "일반회원";
						}else if($val['mem_type'] == "C"){
							$val['mem_type'] = "사업자";
						}else if($val['mem_type'] == "M"){
							$val['mem_type'] = "직원";
						}else if($val['mem_type'] == "A"){
							$val['mem_type'] = "관리자";
						}

						if($val['gp_ix']){
							$sql = "select gp_name from shop_groupinfo where gp_ix = '".$val['gp_ix']."'";
							$mdb->query($sql);
							$mdb->fetch();

							$gp_name = $mdb->dt[gp_name];
						}
						
						if($val['level_ix']){
							$sql = "select lv_name from shop_level where level_ix = '".$val['level_ix']."'";
							$mdb->query($sql);
							$mdb->fetch();
								
							$lv_name = $mdb->dt[lv_name];
						}else{
							$lv_name = '-';
						}
						
			?>
				<tr>
					<td>
						<?
							if($search_type == 'name'){
						?>
						<span>
							<?=$val['name']?>
						</span>
						<?
							}else{
						?>
							<?=$val['name']?>
						<?
							}
						?>
					</td>
					<td>
						<?=$val['id']?>
					</td>
					<td>
						<?=$val['mem_type']?>
					</td>
					<td>
						<?=$gp_name?>
					</td>
					<td>
						<?=$lv_name?>
					</td>
					<td>
						<?
							if($search_type == 'tel'){
						?>
						<span>
							<?=$val['pcs']?>
						</span>
						<?
							}else{
						?>
							<?=$val['pcs']?>
						<?
							}
						?>
					</td>
					<td>
						<?=$val['tel']?>
					</td>
					<td>
						<img src="../images/collpop_seach_crm.png" alt="crm" onclick="crmChange('<?=$val['code']?>','<?=$val['pcs']?>','member')" />
					</td>
				</tr>
			<?php
				}

					}else{
			?>
				<tr>
					<td colspan='8'>
						항목에 해당하는 정보가 없습니다.
					</td>
				</tr>
			<?php
				}
			?>
			</table>
		</div>
		<h5 class='pop_top_bottom'><img src="../images/collpop_title_03.png" alt="주문정보(최근3개월)" /></h5>
		<div class='cti_table_list_1 cti_table_list_2'>
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<col width='115'>
				<col width='140'>
				<col width='145'>
				<col width='77'>
				<col width='114'>
				<col width='82'>
				<col width='*'>
				<col width='120'>
				<col width='110'>
				<tr>
					<th>
						회원여부
					</th>
					<th>
						주문일시/주문번호
					</th>
					<th>
						주문자/수취인
					</th>
					<th>
						판매처
					</th>
					<th>
						결제수단
					</th>
					<th>
						결제금액
					</th>
					<th>
						주문자번호
					</th>
					<th>
						수취인번호
					</th>
					<th>
						관리
					</th>
				</tr>
			<?php
				
				$max = 5; //페이지당 갯수

				if ($page == '')
				{
					$start = 0;
					$page  = 1;
				}
				else
				{
					$start = ($page - 1) * $max;
				}

				if($search_type && $search_text){
				//검색시
					if($search_type == "tel"){
						$where = " AND so.btel = '$search_text' or so.bmobile = '$search_text'";
					}else if($search_type == "name"){
						$where = " AND so.bname = '$search_text'";
					}else if($search_type == "oid"){
						$where = "AND so.oid = '$search_text'";
					}else if($search_type == "id"){
						$where = "AND so.buserid LIKE '$search_text'";
					}

					$end_date = date('Y-m-d H:i:s');
					$start_date= date("Y-m-d H:i:s", strtotime($end_date.'- 3month')); 
					
					$where .= " AND so.order_date between '$start_date' AND '$end_date' AND sod.status != 'SR' ";

					$sql = "SELECT 
								distinct so.oid ,
								so.user_code , so.buserid , so.bname , so.order_date , so.oid , so.com_name, so.bmobile , so.payment_agent_type , so.payment_price , odd.rname ,
								odd.rmobile , sop.method , sod.company_name
							FROM 
								shop_order so
							LEFT JOIN
								shop_order_detail sod
							ON (so.oid = sod.oid)
							LEFT JOIN
								shop_order_detail_deliveryinfo odd
							ON (so.oid = odd.oid)
							LEFT JOIN
								shop_order_payment sop
							ON (so.oid = sop.oid )
							WHERE 1 $where
							";
					$db->query($sql);
					$total = $db->total;

					$sql = "SELECT 
								distinct so.oid ,
								so.user_code , so.buserid , so.bname , so.order_date , so.oid , so.com_name, so.bmobile , so.payment_agent_type , so.payment_price , odd.rname ,
								odd.rmobile , sop.method ,sod.company_name
							FROM 
								shop_order so
							LEFT JOIN
								shop_order_detail sod
							ON (so.oid = sod.oid)
							LEFT JOIN
								shop_order_detail_deliveryinfo odd
							ON (so.oid = odd.oid)
							LEFT JOIN
								shop_order_payment sop
							ON (so.oid = sop.oid )
							WHERE 1 $where
							ORDER BY so.oid DESC
							LIMIT $start,$max
							";
					
					$db->query($sql);
					$result = $db->fetchall();

				}else if($tel){
					//전화유입시

					$where = " AND so.btel LIKE '%$tel%' OR so.bmobile LIKE '%$tel%' ";

					$end_date = date('Y-m-d H:i:s');
					$start_date= date("Y-m-d H:i:s", strtotime($end_date.'- 3month')); 
					
					$where .= " AND so.order_date between '$start_date' AND '$end_date' AND sod.status != 'SR' ";

					$sql = "SELECT 
								distinct so.oid ,
								so.user_code , so.buserid , so.bname , so.order_date , so.oid , so.com_name, so.bmobile , so.payment_agent_type , so.payment_price , odd.rname ,
								odd.rmobile , sop.method, sod.company_name
							FROM 
								shop_order so
							LEFT JOIN
								shop_order_detail sod
							ON (so.oid = sod.oid)
							LEFT JOIN
								shop_order_detail_deliveryinfo odd
							ON (so.oid = odd.oid)
							LEFT JOIN
								shop_order_payment sop
							ON (so.oid = sop.oid )
							WHERE 1 $where
							";
					$db->query($sql);
					$total = $db->total;

					$sql = "SELECT 
								distinct so.oid ,
								so.user_code , so.buserid , so.bname , so.order_date , so.oid , so.com_name, so.bmobile , so.payment_agent_type , so.payment_price , odd.rname ,
								odd.rmobile , sop.method ,sod.company_name
							FROM 
								shop_order so
							LEFT JOIN
								shop_order_detail sod
							ON (so.oid = sod.oid)
							LEFT JOIN
								shop_order_detail_deliveryinfo odd
							ON (so.oid = odd.oid)
							LEFT JOIN
								shop_order_payment sop
							ON (so.oid = sop.oid )
							WHERE 1 $where
							ORDER BY so.oid DESC
							LIMIT $start,$max
							";
					
					$db->query($sql);
					$result = $db->fetchall();

				}
					$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text","view");

					if($result){

						foreach($result as $val){

						if($val['method'] == "0"){
							$val['method'] = "무통장입금";
						}else if($val['method'] == "1"){
							$val['method'] = "카드결제";
						}else if($val['method'] == "4"){
							$val['method'] = "가상결제";
						}else if($val['method'] == "5"){
							$val['method'] = "실시간계좌이체";
						}else if($val['method'] == "10"){
							$val['method'] = "현금";
						}else if($val['method'] == "12"){
							$val['method'] = "예치금";
						}else if($val['method'] == "13"){
							$val['method'] = "적립금";
						}
			?>
				<tr>
					<td>
						<?
							if($search_type == 'name'){
						?>
						<span>
							<?=$val['bname']?><br/><?=$val['buserid']?>
						</span>
						<?
							}else{
						?>
							<?=$val['bname']?><br/><?=$val['buserid']?>
						<?
							}
						?>
					</td>
					<td>
						<?=$val['date']?><br/><?=$val['oid']?>
					</td>
					<td>
						<?=$val['bname']?>/<br/><?=$val['rname']?>
					</td>
					<td>
						<?=$val['company_name']?>
					</td>
					<td>
						<?=$val['method']?>
					</td>
					<td>
						<?=number_format($val['payment_price'])?>
					</td>
					<td>
						<?
							if($search_type == 'tel'){
						?>
						<span>
							<?=$val['bmobile']?>
						</span>
						<?
							}else{
						?>
							<?=$val['bmobile']?>
						<?
							}
						?>
					</td>
					<td>
						<?=$val['rmobile']?>
					</td>
					<td>
						<img src="../images/collpop_seach_crm.png" alt="crm" onclick="crmChange('<?=$val['user_code']?>','<?=$val['bmobile']?>','order')" />
					</td>
				</tr>
			<?php
				}
			?>
				<tr>
					<td colspan='9' style='text-align:center'>
						<?=$str_page_bar?>
					</td>
				</tr>
			<?

					}else{
			?>
				<tr>
					<td colspan='9' style='text-align:center'>
						항목에 해당하는 정보가 없습니다.
					</td>
				</tr>
			<?php
				}
			?>
			</table>
		</div>
<?php
	
function page_bar($total, $page, $max,$add_query="",$paging_type="inner"){
	//$page_string;
	global $cid,$depth,$category_load, $company_id;
	global $nset, $orderby;
	global $HTTP_URL, $admininfo;
	//echo $HTTP_URL;
	//if(!$add_query){		
		if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){			
			$add_query = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
		}else{			
			$add_query = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
			//echo $_SERVER["QUERY_STRING"];
		}
	//}
	if ($total % $max > 0){
		$total_page = floor($total / $max) + 1;
	}else{
		$total_page = floor($total / $max);
	}

	if ($nset == ""){
		$nset = 1;
	}

	$next = (($nset)*10+1);
	$prev = (($nset-2)*10+1);

	if($paging_type == "inner"){
		$paging_type_param = "view=innerview&";
		$paging_type_target = " target=act";
	}else{
		$paging_type_param = "";
		$paging_type_target = "";
	}


	//echo $total_page.":::".$next."::::".$prev."<br>";
	//&cid=$cid&depth=$depth&company_id=$company_id&orderby=$orderby
	if ($total){
		$prev_mark = ($prev > 0) ? "<a href='".$HTTP_URL."?".$paging_type_param."nset=".($nset-1)."&page=".(($nset-2)*10+1)."$add_query' ".$paging_type_target." style='padding:0px;margin:0px;' onclick='blockLoading();'><img src='/admin/images/paging/arrowleft02.gif' border=0  style='padding:0px;margin:0px; vertical-align:middle;' align='absmiddle'></a> " : "<img src='/admin/images/paging/arrowleft02.gif' border=0 style='vertical-align:middle;' align='absmiddle'> ";
		$next_mark = ($next <= $total_page) ? "<a href='".$HTTP_URL."?".$paging_type_param."nset=".($nset+1)."&page=".($nset*10+1)."$add_query' ".$paging_type_target." onclick='blockLoading();'><img src='/admin/images/paging/arrowright02.gif' border=0 style='vertical-align:middle;' align='absmiddle'></a>" :  " <img src='/admin/images/paging/arrowright02.gif' border=0  style='vertical-align:middle;' align='absmiddle'>";
	}

	$page_string = "";

//	for ($i = $page - 10; $i <= $page + 10; $i++)

	for ($i = ($nset-1)*10+1 ; $i <= (($nset-1)*10 + 10); $i++)
	{
		if ($i > 0)
		{
			if ($i <= $total_page)
			{
				if ($i != $page){
					if($i != (($nset-1)*10+1)){
						$page_string = $page_string.("<a href='".$HTTP_URL."?".$paging_type_param."nset=$nset&page=$i$add_query' style='font-weight:bold;color:gray' ".$paging_type_target." onclick='blockLoading();'><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 1px;cursor:pointer;height:24px;'>".$i."</span></a> ");
					}else{
						$page_string = $page_string.(" <a href='".$HTTP_URL."?".$paging_type_param."nset=$nset&page=$i$add_query' style='font-weight:bold;color:gray;margin:0px;' ".$paging_type_target." onclick='blockLoading();'><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 1px;cursor:pointer;height:24px;'>".$i."</span></a> ");
					}

				}else{
					if($i != (($nset-1)*10+1)){
						$page_string = $page_string.("<span style='color:#333333;border:1px solid #333;font-weight:bold;padding:5px 6px;margin:0 1px;color:#333;background:#fff7da;'>".$i."</span> ");
					}else{
						$page_string = $page_string.("<span style='color:#333333;border:1px solid #333;font-weight:bold;padding:5px 6px;margin:0 1px;color:#333;background:#fff7da;'>".$i."</span> ");
					}
				}


			}
		}
	}
	if($nset != "1"){
		$first_page_string = " <a href='".$HTTP_URL."?".$paging_type_param."nset=1&page=1$add_query' style='margin:0px;' ".$paging_type_target."><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 2px;cursor:pointer;vertical-align:middle;' title='첫 페이지로'>1</span></a> <!--font color='silver'>|</font--> <span style='color:gray'>...</span>";
	}

	if($nset < (floor($total_page/10)+1)){
		$last_page_string = "<span style='color:gray'>...</span>  <a href='".$HTTP_URL."?".$paging_type_param."nset=".(floor($total_page/10)+1)."&page=$total_page$add_query' style='margin:0px;'  ".$paging_type_target."><span style='color:#797979;border:1px solid #DCDCDC;font-weight:bold;padding:5px 6px;margin:0 2px;cursor:pointer;' title='마지막 페이지로'>".$total_page."</span></a> ";
	}
	if ($total){
	$page_string = "<div id='page_area'><table border=0 ><tr><td style='padding:0;margin:0; border:none;'>".$prev_mark."</td><td nowrap style='height:26px;_padding:6px 0;margin:0; border:none;'>".$first_page_string.$page_string.$last_page_string."</td><td style='padding:0;margin:0; border:none;'>".$next_mark."</td><td nowrap style='padding:0;margin:0; border:none;' > <span style='margin-left:20px;'>페이지로 이동 <input type='text' class='textbox number' name='page' id='page' value='' size=4 style='margin-left:3px;' onkeydown='page_num=this.value;' onkeyup='page_num=this.value;' > / ".$total_page." <span onclick=\"goPage(".$total_page.",'".$add_query."','".$paging_type_param."','".$paging_type."')\" style='padding:5px 6px;cursor:pointer;border:1px solid silver;margin-left:5px;font-weight:bold;'>이동</span> </span></td></tr></table>
	<script language='javascript'>
		//var paging_type = '$paging_type';
		
	</script></div>
	";
	}

	return $page_string;
}
?>