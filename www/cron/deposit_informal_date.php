<?
include_once("../class/layout.class");

$startdate = $voneweekago = date("Y-m-d", time()-86400*2)." 00:00:00";
$enddate = $voneweekago = date("Y-m-d", time()-86400*2)." 23:59:59";

$db = new MySQL;
$db2 = new MySQL;

$sql = "select 
			sum(use_deposit) as total_deposit
		from 
			shop_deposit 
		where
			edit_date between '".$startdate."' and '".$enddate."'
			group by uid having max(UNIX_TIMESTAMP(edit_date))";
$db2->query($sql);
$db2->fetch();

$total_deposit = $db2->dt[total_deposit];

$startdate = date("Y-m-d", time()-86400)." 00:00:00";
$enddate = date("Y-m-d", time()-86400)." 23:59:59";

$sql = "select
			sum(if(d.state='1',d.deposit,0)) as wait_deposit,
			sum(if(d.state='2',d.deposit,0)) as cancel_deposit,
			sum(if(d.state='3',d.deposit,0)) as complete_deposit,
			sum(if(d.state='4',d.deposit,0)) as use_deposit,
			sum(if(d.state='5',d.deposit,0)) as request_deposit,
			sum(if(d.state='6',d.deposit,0)) as request_cancel_deposit,
			sum(if(d.state='7',d.deposit,0)) as confirm_deposit,
			sum(if(d.state='8',d.deposit,0)) as withdraw_deposit,
			date_format(d.edit_date,'%Y-%m-%d') as edit_date
		from 
			shop_deposit as d 
		where
			1
			and d.edit_date between '".$startdate."' and '".$enddate."'
			group by date_format(d.edit_date,'%Y-%m-%d')
			order by d.edit_date ASC
			";

$db->query($sql);
$data_info = $db->fetch();

$sql = "insert into shop_deposit_informal (dei_ix,complete_deposit,use_deposit,withdraw_deposit,total_deposit,edit_date)
		values
		('','".$data_info[complete_deposit]."','".$data_info[use_deposit]."','".$data_info[withdraw_deposit]."','".($total_deposit + $data_info[complete_deposit] -$data_info[use_deposit]- $data_info[withdraw_deposit])."','".$data_info[edit_date]."')";
$db->query($sql);

?>