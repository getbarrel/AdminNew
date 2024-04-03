<?

include("../class/layout.class");

$db = new Database;
//select * from shop_cupon_regist where publish_ix  in ( SELECT publish_ix FROM `shop_cupon_publish` cp WHERE cp.publish_tmp_ix !='0' and date_format(cp.regdate,'%Y%m%d')='20140305' );
//delete from shop_cupon_regist where publish_ix  in ( SELECT publish_ix FROM `shop_cupon_publish` cp WHERE cp.publish_tmp_ix !='0' and date_format(cp.regdate,'%Y%m%d')='20140305' );
//delete from shop_cupon_publish WHERE publish_tmp_ix !='0' and date_format(regdate,'%Y%m%d')='20140305';
/*
$sql="select *,
				(select count(*) from common_member_detail where gp_ix=gi.gp_ix ) as gp_cnt ,
				(select count(*) from shop_cupon_publish_tmp cpt , shop_cupon_relation_group crg where cpt.publish_tmp_ix=crg.publish_tmp_ix and crg.gp_ix=gi.gp_ix) as coupon_cnt
		from shop_groupinfo gi ";
$db->query($sql);
$gp_list=$db->fetchall("object");


foreach($gp_list as $gl){
	$sql="select * from shop_group_month where year='".date("Y")."' and month='".date("m")."' and gp_ix='".$gl["gp_ix"]."' ";
	$db->query($sql);
	if(!$db->total){
		$sql="insert into shop_group_month (year,month,gp_ix,gp_name,gp_level,gp_cnt,coupon_cnt,regdate) values ('".date("Y")."','".date("m")."','".$gl["gp_ix"]."','".$gl["gp_name"]."','".$gl["gp_level"]."','".$gl["gp_cnt"]."','".$gl["coupon_cnt"]."',NOW())";
		$db->query($sql);
	}
}
*/

$sql = "select mall_ix,mall_data_root, mall_type from shop_shopinfo  where mall_div = 'B'  ";
$db->query($sql);
$db->fetch();

$admininfo[mall_ix] = $db->dt[mall_ix];
$admininfo[mall_data_root] = $db->dt[mall_data_root];
$admininfo[admin_level] = 9;
$admininfo[language] = 'korea';
$admininfo[mall_type] = $db->dt[mall_type];

$mall_data_root_ = $admininfo[mall_data_root];

$shmop = new Shared("cron_group_cupon");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$mall_data_root_."/_shared/";
$shmop->SetFilePath();

$cron_group_cupon = $shmop->getObjectForKey("cron_group_cupon");
$cron_group_cupon = unserialize(urldecode($cron_group_cupon));

if($cron_group_cupon[disp]==1){
	//if($act=="cron"){
		$sql = "select * from shop_cupon_publish_tmp";
		$db->query($sql);
		$cupons_info = $db->fetchall();


		for($c=0;$c < count($cupons_info);$c++){
			
			extract($cupons_info[$c]);
				
			$sql="select * from shop_cupon_publish where date_format(regdate,'%Y%m%d') = '".date('Ymd')."' and publish_tmp_ix= '".$publish_tmp_ix."' ";
			$db->query($sql);
			if(!$db->total){

				$cupon_no =  GetCuponNo();
				
				if($use_date_type == 1){ //발행일로부터
					if($publish_date_type == 1){
						//바로 발행하기 떄문에 이렇게 해도 됨!
						$publish_year = date("Y") + $publish_date_differ;
					}else{
						$publish_year = date("Y");
					}
					if($publish_date_type == 2){
						$publish_month = date("m") + $publish_date_differ;
					}else{
						$publish_month = date("m");
					}
					if($publish_date_type == 3){
						$publish_day = date("d") + $publish_date_differ;
					}else{
						$publish_day = date("d");
					}

					$use_date_limit = mktime(0,0,0,$publish_month,$publish_day,$publish_year);
					$use_sdate=date("Ymd");
					$use_edate = date("Ymd",mktime(0,0,0,$publish_month,$publish_day,$publish_year));
					if($db->dbms_type == "oracle"){
					//TO_DATE('10-04-2010 20:37:50','MM-DD-YYYY HH24:MI:SS')
						$use_sdate = date("m-d-Y H:i:s");
						$use_edate = date("m-d-Y H:i:s",mktime(0,0,0,$publish_month,$publish_day,$publish_year));
					}
					//$event_date = mktime(0,0,0,$event_meonth,$event_day+$order[end_date_differ],$evet_year);

				}else if($use_date_type == 2){ //발급일로부터
					if($regist_date_type == 1){
						$regist_year = date("Y") + $regist_date_differ;
					}else{
						$regist_year = date("Y");
					}
					if($regist_date_type == 2){
						$regist_month = date("m") + $regist_date_differ;
					}else{
						$regist_month = date("m");
					}
					if($regist_date_type == 3){
						$regist_day = date("d") + $regist_date_differ;
					}else{
						$regist_day = date("d");
					}

					$use_date_limit = mktime(0,0,0,$regist_month,$regist_day,$regist_year);
					$use_sdate=date("Ymd");
					$use_edate = date("Ymd",mktime(0,0,0,$regist_month,$regist_day,$regist_year));
					if($db->dbms_type == "oracle"){
					//TO_DATE('10-04-2010 20:37:50','MM-DD-YYYY HH24:MI:SS')
						$use_sdate = date("m-d-Y H:i:s");
						$use_edate = date("m-d-Y H:i:s",mktime(0,0,0,$publish_month,$publish_day,$publish_year));
					}
				}
				/*
				else if($use_date_type == 3){ //사용기간 지정
					
					if($db->dbms_type == "oracle"){
						$use_sdate = $FromMM."-".$FromDD."-".$FromYY." ".$FromHH.":".$FromII.":".$FromSS;
						$use_edate = $ToMM."-".$ToDD."-".$ToYY." ".$ToHH.":".$ToII.":".$ToSS;
					}else{
						$use_sdate = $FromYY."-".$FromMM."-".$FromDD." ".$FromHH.":".$FromII.":".$FromSS;
						$use_edate = $ToYY."-".$ToMM."-".$ToDD." ".$ToHH.":".$ToII.":".$ToSS;
					}
					
				}else { //기간 사용하지 않음 -> 현재날짜 넣음.
					$use_sdate=date("Ymd");
					if($db->dbms_type == "oracle"){
					//TO_DATE('10-04-2010 20:37:50','MM-DD-YYYY HH24:MI:SS')
					   $use_sdate = date("m-d-Y H:i:s");
					}
				}
				*/

				 $sql = "insert into ".TBL_SHOP_CUPON_PUBLISH."
				   (publish_ix,cupon_ix,cupon_no, publish_name,use_date_type,use_sdate, use_edate, use_product_type , publish_date_differ,publish_date_type,regist_date_differ,regist_date_type,publish_condition_price,publish_limit_price,publish_type,mem_ix,regdate,publish_tmp_ix)
				   values
				   ('','$cupon_ix','$cupon_no','[".date('Y년m월')."]".$publish_name."','$use_date_type','$use_sdate','$use_edate','$use_product_type','$publish_date_differ','$publish_date_type','$regist_date_differ','$regist_date_type','$publish_condition_price','$publish_limit_price','1','$mem_ix',NOW(),'$publish_tmp_ix')";

				$db->sequences = "SHOP_CUPON_PUBLISH_SEQ";
				$db->query($sql);
				
				if($db->dbms_type == "oracle"){
					$publish_ix = $db->last_insert_id;
				}else{
					$db->query("Select publish_ix from ".TBL_SHOP_CUPON_PUBLISH." where publish_ix = LAST_INSERT_ID()");
					$db->fetch();
					$publish_ix = $db->dt[publish_ix];
				}

				$publish_ix_list[]=$publish_ix;

				if($use_product_type=="3"){
					
					$db->query("Select * from shop_cupon_relation_product where publish_tmp_ix = '".$publish_tmp_ix."' ");
					$rpid = $db->fetchall();

					for($i=0;$i < count($rpid);$i++){
						$sql = "insert into shop_cupon_relation_product (cpr_ix,publish_ix,pid, regdate)
							values ('','".$publish_ix."','".$rpid[$i][pid]."',NOW())";
						$db->sequences = "SHOP_CUPON_LINK_GOODS_SEQ";
						$db->query($sql);
					}
				}elseif($use_product_type=="2"){

					$db->query("Select * from shop_cupon_relation_category where publish_tmp_ix = '".$publish_tmp_ix."' ");
					$category = $db->fetchall();

					for($i=0;$i < count($category);$i++){
						$sql = "insert into shop_cupon_relation_category (cpc_ix,publish_ix,cid, depth, regdate)
										values ('','".$publish_ix."','".$category[$i][cid]."','".$$category[$i][depth]."',NOW())";
						$db->sequences = "SHOP_CUPON_LINK_CT_SEQ";
						$db->query($sql);
					}
				}elseif($use_product_type=="4"){
					
					$db->query("Select * from shop_cupon_relation_brand where publish_tmp_ix = '".$publish_tmp_ix."' ");
					$brand = $db->fetchall();

					for($i=0;$i < count($brand);$i++){
						$sql = "insert into shop_cupon_relation_brand (crb_ix,publish_ix,b_ix, regdate)
										values ('','".$publish_ix."','".$brand[$i][b_ix]."',NOW())";
						$db->sequences = "SHOP_CUPON_LINK_BRAND_SEQ";
						$db->query($sql);
					}
				}
				
				$use_date_limit = date("Ymd",$use_date_limit);

				$where = " where cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0 and cmd.gp_ix in (select gp_ix from shop_cupon_relation_group where publish_tmp_ix ='".$publish_tmp_ix."' )";
				
				$sql = "insert into ".TBL_SHOP_CUPON_REGIST."  select '' as regist_ix , '".$publish_ix."' as publish_ix, cu.code,1,0,
								'$use_sdate','$use_date_limit',null,null, NOW() , null, null
								from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_SHOP_GROUPINFO." mg
								$where ";

				//echo $sql."<br/>";

				$db->sequences = "SHOP_CUPON_REGIST_SEQ";
				$db->query($sql);
			}
		}
		
		/*
		$sql="select 
					ccd.gp_ix,count(ccd.code) as publish_cnt 
				from shop_cupon_regist cr left join common_member_detail ccd on (cr.mem_ix=ccd.code) where cr.publish_ix in ('".implode("','",$publish_ix_list)."') group by ccd.gp_ix ";
		//echo $sql;
		$db->query($sql);
		$gp_cnt=$db->fetchall("object");
		
		foreach($gp_cnt as $gc){
			$sql="update shop_group_month set
				publish_cnt='".$gc["publish_cnt"]."'
			where
				year='".date("Y")."' and month='".date("m")."' and gp_ix='".$gc["gp_ix"]."'
			";
			$db->query($sql);
		}
		*/


		/*
		echo "
		<script language='JavaScript' src='../js/jquery-1.5.2.min.js'></Script>\n
		<script type='text/javascript'>
		<!--
		";

		for($i=0;$i<count($_info);$i++){
			echo "
					$.ajax({ 
						type: 'GET', 
						data: {'act': 'regist_search_update', 'mmode':'cron','publish_ix':'".$_info[$i][publish_ix]."','gp_ix':'".$_info[$i][gp_ix]."'},
						url: '../admin/display/cupon.act.php',  
						dataType: 'html', 
						async: true, 
						beforeSend: function(){ 
						},  
						success: function(data){ 
							//alert(data);
						} 
					});
			";
		}

		echo "

		//-->
		</script>
		";
		*/

	//}
}
exit;


function GetCuponNo(){
	$mdb = new Database();
	$cupon_no =  "MSCP".date("ymd").rand(1000000,9999999);

	$mdb->query("select cupon_no from ".TBL_SHOP_CUPON_PUBLISH." where cupon_no = '$cupon_no'");

	If($mdb->total){
		GetCuponNo();
	}else{
		return $cupon_no;
	}
}

?>