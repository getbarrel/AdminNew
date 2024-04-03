<?
include("../class/layout.class");
//auth(9);

$db = new Database;
$db2 = new Database;
$mdb = new Database;
//$db->debug = true;//페이지에서 오류가 없는데 쿼리를 찍기에 주석처리함 kbk 13/02/04
$update_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U");
$delete_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D");
$create_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C");
$excel_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E");

$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
if ($FromYY == ""){

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

}

if ($vFromYY == ""){

	$sDate2 = date("Y/m/d", $before10day);
	$eDate2 = date("Y/m/d");

	$startDate2 = date("Ymd", $before10day);
	$endDate2 = date("Ymd");

}else{

	$sDate2 = $vFromYY."/".$vFromMM."/".$vFromDD;
	$eDate2 = $vToYY."/".$vToMM."/".$vToDD;
	$startDate2 = $vFromYY.$vFromMM.$vFromDD;
	$endDate2 = $vToYY.$vToMM.$vToDD;

}

if ($birYY == ""){

	$sDate3 = date("Y/m/d");
	$eDate3 = date("Y/m/d");

	$startDate3 = date("Ymd");
	$endDate3 = date("Ymd");
}else{

	$sDate3 = $birYY."/".$birMM."/".$birDD;
	$eDate3 = "none";
	$startDate3 = $birYY.$birMM.$birDD;
	$endDate3 = "none";
	$birDate = $birYY.$birMM.$birDD;
}



	$max = 15; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}
/*
	if ($admininfo[mall_type] == "O"){
		if($db->dbms_type == "oracle"){
			$where = " where cu.code = cmd.code and cu.mem_type in ('M','C','F','S') ";
		}else{
			$where = " where cu.code != '' and cu.code = cmd.code and cu.mem_type in ('M','C','F','S') ";
		}
	}else{
		if($db->dbms_type == "oracle"){
			$where = " where cu.code = cmd.code and cu.mem_type in ('M','C','F') ";
		}else{
			$where = " where cu.code != '' and cu.code = cmd.code and cu.mem_type in ('M','C','F') ";
		}
	}

	if($region != ""){
		$where .= " and addr1 LIKE  '%".$region."%' ";
	}

	if($gp_level != ""){
		$where .= " and mg.gp_level = '".$gp_level."' ";
	}

	if($gp_ix != ""){
		$where .= " and cmd.gp_ix = '".$gp_ix."' ";
	}


	if($sex == "M" || $sex == "W"){
		$where .= " and sex_div =  '$sex' ";
	}

	if($mailsend_yn != "A" && $mailsend_yn != ""){
		$where .= " and info =  '$mailsend_yn' ";
	}

	if($mem_type != ""){
		$where .= " and mem_type =  '$mem_type' ";
	}



	if($smssend_yn != "A" && $smssend_yn != ""){
		$where .= " and sms =  '$smssend_yn' ";
	}

	$search_text = trim($search_text);
	if($db->dbms_type == "oracle"){
		if($search_type != "" && $search_text != ""){
			if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
				$where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			}else{
				$where .= " and $search_type LIKE  '%$search_text%' ";
			}
		}
	}else{
		if($search_type != "" && $search_text != ""){
			if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
				$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			}else{
				$where .= " and $search_type LIKE  '%$search_text%' ";
			}
		}
	}

	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

	if($startDate != "" && $endDate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(cu.date_ , 'YYYYMMDD') between  $startDate and $endDate ";
		}else{
			$where .= " and  MID(replace(cu.date,'-',''),1,8) between  $startDate and $endDate ";
		}
	}

	$vstartDate = $vFromYY.$vFromMM.$vFromDD;
	$vendDate = $vToYY.$vToMM.$vToDD;

	if($vstartDate != "" && $vendDate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(cu.last , 'YYYYMMDD') between  $vstartDate and $vendDate ";
		}else{
			$where .= " and  MID(replace(cu.last,'-',''),1,8) between  $vstartDate and $vendDate ";
		}
	}

	// 전체 갯수 불러오는 부분
	$db->query("SELECT count(*) as total FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd left join  ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix  $where ");
	$db->fetch();
	$total = $db->dt[total];
	//echo $total ;
	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&region=$region&gp_level=$gp_level&age=$age&birthday_yn=$birthday_yn&sex=$sex&mailsend_yn=$mailsend_yn&smssend_yn=$smssend_yn&mem_type=$mem_type&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");


	if($db->dbms_type == "oracle"){
		$sql = "select cu.code, cu.id,  cu.company_id, ccd.com_name,
			AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, mg.gp_level,
			AES_DECRYPT(cmd.mail,'".$db->ase_encrypt_key."') as mail, cu.authorized as authorized, cu.is_id_auth as is_id_auth, cu.mem_type as mem_type,
			cu.visit, date_format(cu.date_,'%Y.%m.%d') as regdate, mg.gp_name, cu.last AS last
			from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
			left join ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix
			, ".TBL_COMMON_COMPANY_DETAIL." ccd
			$where and cu.company_id = ccd.company_id
			ORDER BY cu.date_ DESC
			LIMIT $start, $max";
	}else{
		$sql = "select cu.code, cu.id,  cu.company_id, ccd.com_name,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, mg.gp_level,
			AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, cu.authorized as authorized, cu.is_id_auth as is_id_auth, cu.mem_type as mem_type,
			cu.visit, date_format(cu.date,'%Y.%m.%d') as regdate, mg.gp_name,  UNIX_TIMESTAMP(cu.last) AS last
			from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
			left join ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix
			, ".TBL_COMMON_COMPANY_DETAIL." ccd
			$where and cu.company_id = ccd.company_id
			ORDER BY cu.date DESC
			LIMIT $start, $max";
	}
	//echo nl2br($sql);
	$db->query($sql);

*/


	$max = 15; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}
	//echo substr_count($_SERVER["REQUEST_URI"],"member_black_list");

	if(substr_count($_SERVER["REQUEST_URI"],"member_black_list")){
		$black_list_where = " and black_list='Y' ";
	}
	if ($admininfo[mall_type] == "O"){
		if($db->dbms_type == "oracle"){
			$where = " where cu.mem_type in ('M','C','F') $black_list_where ";//,'S' //and cu.code = cmd.code //cu.date_ < '9999/12/31' and 
			$count_where = "where cu.mem_type in ('M','C','F') $black_list_where ";//,'S' //cu.date_ < '9999/12/31' and 
		}else{
			$where = " where cu.code != '' and cu.code = cmd.code and cu.mem_type in ('M','C','F') $black_list_where ";//,'S'
			$count_where = "where cu.mem_type in ('M','C','F') $black_list_where ";//,'S'
		}
	}else{
		if($db->dbms_type == "oracle"){
			$where = " where cu.mem_type in ('M','C','F') $black_list_where ";//and cu.code = cmd.code  //cu.date_ < '9999/12/31' and 
			$count_where = "where cu.mem_type in ('M','C','F') "; //cu.date_ < '9999/12/31' and 
		}else{
			$where = " where cu.code != '' and cu.code = cmd.code and cu.mem_type in ('M','C','F') $black_list_where "; //cu.date < '9999/12/31' and 
			$count_where = "where cu.mem_type in ('M','C','F') ";//,'S'
		}
	}

	if($region != ""){
		//$where .= " and addr1 LIKE  '%".$region."%' ";			//지역선택 검색안되는 오류 수정 2013-04-01 이학봉
		//$where .= " and addr1 LIKE  '%".$region."%'  ";
		//$count_where .= " and addr1 LIKE  '%".$region."%'  ";
		//$cmd_where .= "and addr1 LIKE  '%".$region."%'   ";

		$where .= " and AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') LIKE  '%$region%' ";
		$count_where .= " and AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') LIKE  '%$region%' ";
		$cmd_where .= " and AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') LIKE  '%$region%' ";
	}

	if($gp_level != ""){
		$where .= " and mg.gp_level = '".$gp_level."' ";
		$count_where .= " and mg.gp_level = '".$gp_level."' ";
		$mg_where .= " and mg.gp_level = '".$gp_level."' ";
	}

	if($gp_ix != ""){
		$where .= " and cmd.gp_ix = '".$gp_ix."' ";
		$count_where .= " and cmd.gp_ix = '".$gp_ix."' ";
		$cmd_where .= " and cmd.gp_ix = '".$gp_ix."' ";
	}
/*


	$birthday = $birYY.$birMM.$birDD;
	$birthday2 = substr($birYY,2,2).$birMM.$birDD;

*/

	if($sex == "M" || $sex == "W"){
		$where .= " and sex_div =  '$sex' ";
		$count_where .= " and sex_div =  '$sex' ";
		$cmd_where .= " and sex_div =  '$sex' ";
	}

	if($mailsend_yn != "A" && $mailsend_yn != ""){
		$where .= " and info =  '$mailsend_yn' ";
		$count_where .= " and info =  '$mailsend_yn' ";
		$cmd_where .= " and info =  '$mailsend_yn' ";
	}

	if($mem_type != ""){
		$where .= " and mem_type =  '$mem_type' ";
		$count_where .= " and mem_type =  '$mem_type' ";
		$cmd_where .= " and mem_type =  '$mem_type' ";
	}



	if($smssend_yn != "A" && $smssend_yn != ""){
		$where .= " and sms =  '$smssend_yn' ";
		$count_where .= " and sms =  '$smssend_yn' ";
		$cmd_where .= " and sms =  '$smssend_yn' ";
	}

	$search_text = trim($search_text);
	if($db->dbms_type == "oracle"){
		if($search_type != "" && $search_text != ""){
			if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
				$where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
				$count_where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			}else{
				$where .= " and $search_type LIKE  '%$search_text%' ";
				$count_where .= " and $search_type LIKE  '%$search_text%' ";
			}

		}
	}else{
		if($search_type != "" && $search_text != ""){
			if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
				$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
				$count_where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			}else{
				$where .= " and $search_type LIKE  '%$search_text%' ";
				$count_where .= " and $search_type LIKE  '%$search_text%' ";
			}
		}
	}

	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

	if($startDate != "" && $endDate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(cu.date_ , 'YYYYMMDD') between  $startDate and $endDate ";
			$count_where .= " and  to_char(cu.date_ , 'YYYYMMDD') between  $startDate and $endDate ";
		}else{
			$where .= " and  MID(replace(cu.date,'-',''),1,8) between  $startDate and $endDate ";
			$count_where .= " and  MID(replace(cu.date,'-',''),1,8) between  $startDate and $endDate ";
		}
	}

	$vstartDate = $vFromYY.$vFromMM.$vFromDD;
	$vendDate = $vToYY.$vToMM.$vToDD;

	if($vstartDate != "" && $vendDate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(cu.last , 'YYYYMMDD') between  $vstartDate and $vendDate ";
			$count_where .= " and  to_char(cu.last , 'YYYYMMDD') between  $vstartDate and $vendDate ";
		}else{
			$where .= " and  MID(replace(cu.last,'-',''),1,8) between  $vstartDate and $vendDate ";
			$count_where .= " and  MID(replace(cu.last,'-',''),1,8) between  $vstartDate and $vendDate ";
		}
	}
$script_time[count_start] = time();
	// 전체 갯수 불러오는 부분
	$sql = "SELECT count(*) as total FROM ".TBL_COMMON_USER." cu
			left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
			$count_where ";
			//left join  ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix
	//echo nl2br($sql);
	$db->query($sql);
	//
	$db->fetch();
	$total = $db->dt[total];
$script_time[count_end] = time();
	//echo $total ;



/*
	$sql = "SELECT cu.*, date_format(cu.date,'%Y.%m.%d') as regdate, UNIX_TIMESTAMP(last) AS last, sum(case when mr.state in (1,2,5,6,7) then reserve else 0 end) as reserve
	FROM  ".TBL_COMMON_USER." mm left outer join ".TBL_SHOP_RESERVE_INFO." mr on cu.code = mr.uid
	$where
	group by com_name, com_number, com_phone, com_fax, com_business_status, com_business_category, code, name, mail, visit, perm,gp_level, last
	ORDER BY date DESC LIMIT $start, $max";
*/
	$script_time[query_start] = time();
	if($db->dbms_type == "oracle"){//ccd.com_name, mg.gp_level,mg.gp_name, 
		//$db->add_select_query = " func_get_group_name(gp_ix) as gp_name ";
		$sql = "select cu.code, cu.id,  cu.company_id,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, cu.authorized as authorized, cu.is_id_auth as is_id_auth, cu.mem_type as mem_type,
			cu.visit, date_format(cu.date_,'%Y.%m.%d') as regdate,  cu.last AS last, cmd.gp_ix
			from ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
			$where
			LIMIT $start, $max";
			//ORDER BY cu.date_ DESC
			//left join ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix
			//, ".TBL_COMMON_COMPANY_DETAIL." ccd
			//and cu.company_id = ccd.company_id
			//
		//echo nl2br($sql);
	}else{
		$sql = "select cu.code, cu.id,  cu.company_id, ccd.com_name, ccd.com_number,ccd.com_phone,cu.charger_ix,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, mg.gp_level,
			AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, cu.authorized as authorized, cu.is_id_auth as is_id_auth, cu.mem_type as mem_type,
			cu.visit, date_format(cu.date,'%Y.%m.%d') as regdate, mg.gp_name,  UNIX_TIMESTAMP(cu.last) AS last, cmd.gp_ix
			from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
			left join ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix
			, ".TBL_COMMON_COMPANY_DETAIL." ccd
			$where and cu.company_id = ccd.company_id
			ORDER BY cu.date DESC
			LIMIT $start, $max";
	}
	//echo nl2br($sql);
	$db->query($sql);
	$script_time[query_end] = time();

	$str_page_bar = page_bar($total, $page,$max, "&max=$max&update_kind=$update_kind&search_type=$search_type&search_text=$search_text&region=$region&gp_ix=$gp_ix&age=$age&birthday_yn=$birthday_yn&sex=$sex&mailsend_yn=$mailsend_yn&smssend_yn=$smssend_yn&mem_type=$mem_type&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");//gp_level 을 사용하지 않아서 gp_ix 로 바꿈 kbk 13/02/19

$Script = "
<script language='javascript'>

function view_go()
{
	var sort = document.view.sort[document.view.sort.selectedIndex].value;

	location.href = 'member.php?view='+sort;
}


function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		frm.FromYY.disabled = false;
		frm.FromMM.disabled = false;
		frm.FromDD.disabled = false;
		frm.ToYY.disabled = false;
		frm.ToMM.disabled = false;
		frm.ToDD.disabled = false;
	}else{
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;
	}
}

function ChangeVisitDate(frm){
	if(frm.visitdate.checked){
		frm.vFromYY.disabled = false;
		frm.vFromMM.disabled = false;
		frm.vFromDD.disabled = false;
		frm.vToYY.disabled = false;
		frm.vToMM.disabled = false;
		frm.vToDD.disabled = false;
	}else{
		frm.vFromYY.disabled = true;
		frm.vFromMM.disabled = true;
		frm.vFromDD.disabled = true;
		frm.vToYY.disabled = true;
		frm.vToMM.disabled = true;
		frm.vToDD.disabled = true;
	}
}
function ChangeBirDate(frm){
	if(frm.bir.checked){
		frm.birYY.disabled = false;
		frm.birMM.disabled = false;
		frm.birDD.disabled = false;
	}else{
		frm.birYY.disabled = true;
		frm.birMM.disabled = true;
		frm.birDD.disabled = true;
	}
}

function init(){

	var frm = document.searchmember;
	onLoad('$sDate','$eDate','$sDate2','$eDate2','$sDate3');";

if($regdate != "1"){
$Script .= "
	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;";
}
if($visitdate != "1"){
$Script .= "
	frm.vFromYY.disabled = true;
	frm.vFromMM.disabled = true;
	frm.vFromDD.disabled = true;
	frm.vToYY.disabled = true;
	frm.vToMM.disabled = true;
	frm.vToDD.disabled = true;	";
}
if($bir != "1"){
$Script .= "
/*
	frm.birYY.disabled = true;
	frm.birMM.disabled = true;
	frm.birDD.disabled = true;
*/";

}
$Script .= "
}


function deleteMemberInfo(act, code){
 	if(confirm('해당회원 정보를 정말로 삭제하시겠습니까? \\n 삭제시 관련된 모든 정보가 삭제됩니다.')){
		window.frames['iframe_act'].location.href= 'member.act.php?act='+act+'&code='+code;
 	}
}

</script>";

$Contents = "


<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>

<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("전체회원관리", "회원관리 > 전체회원관리 ")."</td>
  </tr>

  <tr>
  	<td>";
$Contents .= "
<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<th class='box_01'></th>
		<td class='box_02'></td>
		<th class='box_03'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<td class='box_05'  valign=top style='padding:5px 0px 5px 0px'>
 		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
		<form name=searchmember method='get'><!--SubmitX(this);'-->
            <input type='hidden' name=mc_ix value='".$mc_ix." '>
		    <col width='12%'>
			<col width='*'>
			<tr  height=27>
		      <td class='search_box_title'  >지역선택</td>
		      <td class='search_box_item' >
		      <select name='region' >
                        <option value=''>-- 선택 --</option>
                        <option value='서울'  ".CompareReturnValue("서울",$region,"selected").">서울</option>
                        <option value='충북'  ".CompareReturnValue("충북",$region,"selected").">충북</option>
                        <option value='충남'  ".CompareReturnValue("충남",$region,"selected").">충남</option>
                        <option value='전북'  ".CompareReturnValue("전북",$region,"selected").">전북</option>
                        <option value='제주'  ".CompareReturnValue("제주",$region,"selected").">제주</option>
                        <option value='전남'  ".CompareReturnValue("전남",$region,"selected").">전남</option>
                        <option value='경북'  ".CompareReturnValue("경북",$region,"selected").">경북</option>
                        <option value='경남'  ".CompareReturnValue("경남",$region,"selected").">경남</option>
                        <option value='경기'  ".CompareReturnValue("경기",$region,"selected").">경기</option>
                        <option value='부산'  ".CompareReturnValue("부산",$region,"selected").">부산</option>
                        <option value='대구'  ".CompareReturnValue("대구",$region,"selected").">대구</option>
                        <option value='인천'  ".CompareReturnValue("인천",$region,"selected").">인천</option>
                        <option value='광주'  ".CompareReturnValue("광주",$region,"selected").">광주</option>
                        <option value='대전'  ".CompareReturnValue("대전",$region,"selected").">대전</option>
                        <option value='울산'  ".CompareReturnValue("울산",$region,"selected").">울산</option>
                        <option value='강원'  ".CompareReturnValue("강원",$region,"selected").">강원</option>
                        </select>
		      </td>
			  <td class='search_box_title' >성별검색 </td>
				<td class='search_box_item' >
				  <input type=radio name='sex' value='' id='sex_all'  ".CompareReturnValue("0",$sex,"checked")." checked><label for='sex_all'>모두</label>
				  <input type=radio name='sex' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
				  <input type=radio name='sex' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label>
				</td>
		    </tr>
		    <!--tr height=27>
			<td class='search_box_title' >연령</td>
		      <td class='search_box_item' >
		      <select name='age' >
                        <option value=''> -- 선택 -- </option>
                        <option value='10' ".CompareReturnValue("10",$age,"selected").">10대</option>
                        <option value='20' ".CompareReturnValue("20",$age,"selected").">20대</option>
                        <option value='30' ".CompareReturnValue("30",$age,"selected").">30대</option>
                        <option value='40' ".CompareReturnValue("40",$age,"selected").">40대</option>
                        <option value='50' ".CompareReturnValue("50",$age,"selected").">50대</option>
                        <option value='60' ".CompareReturnValue("60",$age,"selected").">60대</option>
                        </select>
		      </td>
				<td class='search_box_title' >
					<label for='bir'>생일</label><input type='checkbox' name='bir' id='bir' value='1' onclick='ChangeBirDate(document.searchmember);' ".CompareReturnValue("1",$bir,"checked").">
				</td>
				<td class='search_box_item' >
				 <SELECT onchange=javascript:onChangeDate(this.form.birYY,this.form.birMM,this.form.birDD) name=birYY ></SELECT> 년
				 <SELECT onchange=javascript:onChangeDate(this.form.birYY,this.form.birMM,this.form.birDD) name=birMM></SELECT> 월
				 <SELECT name=birDD></SELECT> 일
				</td>
		    </tr-->
		    <tr height=27>
		      <td class='search_box_title' >회원그룹 </td>
		      <td class='search_box_item' >
		      ".makeGroupSelectBox($mdb,"gp_ix",$gp_ix)."
		      </td>
		      <td class='search_box_title' >회원구분 </td>
		      <td class='search_box_item' >
					<input type=radio name='mem_type' value='' id='mem_type_'  ".CompareReturnValue("",$mem_type,"checked")." checked><label for='mem_type_'>모두</label>
				  <input type=radio name='mem_type' value='M' id='mem_type_m'  ".CompareReturnValue("M",$mem_type,"checked")."><label for='mem_type_m'>일반회원</label>
				  <input type=radio name='mem_type' value='C' id='mem_type_c' ".CompareReturnValue("C",$mem_type,"checked")."><label for='mem_type_c'>기업회원</label>
				  <input type=radio name='mem_type' value='F' id='mem_type_f' ".CompareReturnValue("F",$mem_type,"checked")."><label for='mem_type_f'>외국인회원</label>
		      </td>
		    </tr>
		    <tr height=27>
		      <td class='search_box_title' >발송여부 </td>
		      <td class='search_box_item'  >
			   <input type=radio name='mailsend_yn' value='A' id='mailsend_a'  ".CompareReturnValue("A",$mailsend_yn,"checked")." checked><label for='mailsend_a'>모두</label>
		       <input type=radio name='mailsend_yn' value='1' id='mailsend_y'  ".CompareReturnValue("1",$mailsend_yn,"checked")."><label for='mailsend_y'>수신회원만</label><input type=radio name='mailsend_yn' value='0' id='mailsend_n' ".CompareReturnValue("0",$mailsend_yn,"checked")."><label for='mailsend_n'>수신거부회원포함</label>
		      </td>
		      <td class='search_box_title' >SMS 발송여부 </td>
		      <td class='search_box_item'  >
				  <input type=radio name='smssend_yn' value='A' id='smssend_a'  ".CompareReturnValue("A",$smssend_yn,"checked")." checked><label for='smssend_a'>모두</label>
				  <input type=radio name='smssend_yn' value='1' id='smssend_y'  ".CompareReturnValue("1",$smssend_yn,"checked")."><label for='smssend_y'>수신회원만</label>
				  <input type=radio name='smssend_yn' value='0' id='smssend_n' ".CompareReturnValue("0",$smssend_yn,"checked")."><label for='smssend_n'>수신거부회원포함</label>
		      </td>
		    </tr>
		    ";

$vdate = date("Ymd", time());
$today = date("Y/m/d", time());
$vyesterday = date("Y/m/d", time()-84600);
$voneweekago = date("Y/m/d", time()-84600*7);
$vtwoweekago = date("Y/m/d", time()-84600*14);
$vfourweekago = date("Y/m/d", time()-84600*28);
$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

 $Contents .= "
		    <tr height=27>
		      <td class='search_box_title' ><label for='regdate'>가입일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
		      <td class='search_box_item'  colspan=3 >
		      	<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
					<tr>
						<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY  style='width:57px;'></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM style='width:43px;'></SELECT> 월 <SELECT name=FromDD style='width:43px;'></SELECT> 일 </TD>
						<TD style='padding:0 5px;' align=center>~</TD>
						<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY style='width:57px;'></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM style='width:43px;'></SELECT> 월 <SELECT name=ToDD style='width:43px;'></SELECT> 일</TD>
						<TD style='padding-left:10px;' >
							<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
							<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
							<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
							<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
							<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
							<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
							<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
						</TD>
					</tr>
				</table>
		      </td>
		    </tr>
		    <tr height=27>
		      <td class='search_box_title' ><label for='visitdate'>최근방문일</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeVisitDate(document.searchmember);' ".CompareReturnValue("1",$visitdate,"checked")."></td>
		      <td class='search_box_item'  colspan=3  >
		      	<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
					<tr>
						<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY style='width:57px;'></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM style='width:43px;'></SELECT> 월 <SELECT name=vFromDD style='width:43px;'></SELECT> 일 </TD>
						<TD style='padding:0 5px;' align=center>~</TD>
						<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY style='width:57px;'></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM style='width:43px;'></SELECT> 월 <SELECT name=vToDD style='width:43px;'></SELECT> 일</TD>
						<TD style='padding-left:10px;'>
							<a href=\"javascript:select_date('$today','$today',2);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
							<a href=\"javascript:select_date('$vyesterday','$vyesterday',2);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
							<a href=\"javascript:select_date('$voneweekago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
							<a href=\"javascript:select_date('$v15ago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
							<a href=\"javascript:select_date('$vonemonthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
							<a href=\"javascript:select_date('$v2monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
							<a href=\"javascript:select_date('$v3monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
						</TD>
					</tr>

				</table>
		      </td>
		    </tr>
			<tr height=27>
				  <td class='search_box_title' >조건검색 </td>
				  <td class='search_box_item' colspan=3>
						<table cellpadding=0 cellspacing=0 width=100%>
							<col width='80'>
							<col width='*'>
							<tr>
								<td>
								  <select name=search_type>
										<option value='name' ".CompareReturnValue("name",$search_type,"selected").">고객명</option>
										<option value='id' ".CompareReturnValue("id",$search_type,"selected").">아이디</option>
										
										<option value='tel' ".CompareReturnValue("tel",$search_type,"selected").">전화번호</option>
										<option value='pcs' ".CompareReturnValue("pcs",$search_type,"selected").">휴대전화</option>
										<option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected").">회사명</option>
										<option value='com_phone' ".CompareReturnValue("com_phone",$search_type,"selected").">회사전화</option>
										<option value='com_fax' ".CompareReturnValue("com_fax",$search_type,"selected").">회사팩스</option>
										<option value='mail' ".CompareReturnValue("mail",$search_type,"selected").">이메일</option>
										<option value='addr1' ".CompareReturnValue("addr1",$search_type,"selected").">주소</option>
								  </select>
								</td>
								<td>
									<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:70%;font-size:12px;padding:1px;' >
								</td>
							</tr>
						</table>
				  </td>
				</tr>
		    </table>
		</td>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
</table>			";

$Contents .= "
    </td>

  </tr>
  <tr height=50>
    	<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
    </tr>
</table><br></form>
<form name='list_frm'>
<input type='hidden' name='code[]' id='code'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >

  <tr height=30 >
  	<td colspan=5>";

    if($create_auth){
        $Contents .= "
        <a href='javascript:listAction(document.list_frm);'><img src='../images/".$admininfo["language"]."/btn_selected_sms.gif' align=absmiddle  ></a>";
    }else{
        $Contents .= "
        <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_selected_sms.gif' align=absmiddle ></a>";
    }
    $Contents .= "
    </td>
  	<td colspan=5 align=right>";
	if($excel_auth){
	$Contents .= "<a href='member_excel2003.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}
	$Contents .= "
	</td>
  </tr>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='28' bgcolor='#ffffff'>
    <td width='3%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td width='5%' align='center' class='m_td'><font color='#000000'><b>번호</b></font></td>
    <td width='7%' align='center' class='m_td'><font color='#000000'><b>그룹</b></font></td>
    <td width='7%' align='center' class='m_td'><font color='#000000'><b>상호명</b></font></td>
    <td width='7%' align='center' class='m_td'><font color='#000000'><b>사업자번호</b></font></td>
    <td width='6%' align='center' class='m_td' nowrap><font color='#000000'><b>회원구분</b></font></td>
    <td width='6%' align='center' class=m_td nowrap><font color='#000000'><b>승인여부</b></font></td>
    <td width='6%' align='center' class=m_td><font color='#000000'><b>연락처</b></font></td>
    <td width='6%' align='center' class=m_td><font color='#000000'><b>부서</b></font></td>
    <td width='6%' align='center' class=m_td><font color='#000000'><b>담당자</b></font></td>
    <td width='6%' align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
  </tr>";


	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;
	/*
		if ($db->dt[mem_level] == "E")	{ $perm = "탈퇴회원"; }
		if ($db->dt[mem_level] == "M")	{ $perm = "일반회원"; }
		if ($db->dt[mem_level] == "B")	{ $perm = "입점업체"; }
		if ($db->dt[mem_level] == "C")	{ $perm = "특별회원"; }
	*/
		$sql="SELECT * FROM shop_company_department where disp=1 and company_id = '".$_SESSION["admininfo"][company_id]."' and dp_ix = '".$db->dt[charger_ix]."' order by dp_level asc";
		$db2->query($sql);
		$db2->fetch();
		
		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT gp_name FROM ".TBL_SHOP_GROUPINFO." WHERE gp_ix = '".$db->dt[gp_ix]."'  ");
		}else{
			$mdb->query("SELECT gp_name FROM ".TBL_SHOP_GROUPINFO." WHERE gp_ix = '".$db->dt[gp_ix]."'  ");
		}
		$mdb->fetch(0);
		$gp_name = $mdb->dt[gp_name];

		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT sum(reserve) as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid_ = '".$db->dt[code]."' and state in ('1','2','5','6','7')");
		}else{
			$sql = "SELECT IFNULL(sum(reserve),'0') as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid = '".$db->dt[code]."' and state in ('1','2','5','6','7')";
			//echo $sql;
			$mdb->query($sql);
		}
		$mdb->fetch(0);
		$reserve_sum = number_format($mdb->dt[reserve_sum]);

		



        if($db->dt[is_id_auth] != "Y"){
            $is_id_auth = "미인증";
        }else{
            $is_id_auth = "";
        }

        switch($db->dt[authorized]){

        case "Y":
            $authorized = "승인";
            break;
        case "N":
            $authorized = "승인대기";
            break;
        case "X":
            $authorized = "승인거부";
            break;
        default:
            $authorized = "알수없음";
            break;
        }

        switch($db->dt[mem_type]){

        case "M":
            $mem_type = "일반";
            break;
        case "C":
            $mem_type = "기업".($db->dt[com_name] != "" ? "(".$db->dt[com_name].")":"");
            break;
        case "F":
            $mem_type = "외국인";
            break;
        case "S":
            $mem_type = "셀러";
            break;
        case "A":
            $mem_type = "관리자";
            break;
        case "MD":
            $mem_type = "MD";
            break;
        default:
            $mem_type = "일반";
            break;
        }


        $Contents = $Contents."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
            <td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
            <td class='list_box_td' >".$no."</td>
            <td class='list_box_td' style='padding:0px 5px;' nowrap><span title='".$db->dt[organization_name]."'>".$gp_name."</span></td>
            <td class='list_box_td' style='padding:0px 5px;' nowrap>".$db->dt[com_name]."</td>
            <td class='list_box_td' style='padding:0px 5px;' nowrap>".$db->dt[com_number]."</td>
            <td class='list_box_td' nowrap>".$mem_type."</td>
            <td class='list_box_td' >".$authorized."</td>
            <td class='list_box_td' >".$db->dt[com_phone]."</td>
            <td class='list_box_td' >".$db2->dt[dp_name]."</td>
            <td class='list_box_td' >".$db->dt[charger_ix]."</td>
            <td class='list_box_td' ><a href=\"javascript:PopSWindow('tax_document_input.php?code=".$db->dt[code]."',700,600,'tax_document_input')\" style='cursor:pointer' >등록하기</a></td>";
            $Contents .= "
    </td>
  </tr>";

	}

if (!$db->total){

$Contents = $Contents."
  <tr height=50>
    <td colspan='12' align='center'>등록된 회원 데이타가 없습니다.</td>
  </tr>";
}



$Contents .= "
</table>
</form>
<table width=100%>
<tr>
	<td><div style='width:100%;text-align:right;padding:5px 0px;'>".$str_page_bar."</div></td>
	<td align=right>
	<!--img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\" /-->
	<!--div style='cursor:pointer' onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\">회원수동등록</div-->
	</td>
</tr>
</table>";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원에게 SMS 또는 메일을 보내실려면 보내고자 하는 회원을 선택하신후 '선택회원 SMS 보내기' 버튼을 클릭하신후 메일을 보내실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원정보를 백업하기 위해서는 회원정보 검색후 엑셀로 저장 버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색기능을 통해서 조건에 맞는 회원을 빠르게 검색하실수 있습니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("세무기장관리", $help_text,'70');


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->Navigation = "세무서비스 > 세무기장관리";
$P->title = "세무기장관리";
$P->strLeftMenu = tax_receipt();
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>



