<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-06-27
 * Time: 오후 11:56
 */
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");
$db = new database;

if($act == 'push'){

    $sql = "select * from shop_product_stock_reminder where status='N' and pid = '".$pid."' and op_id = '".$op_id."'  ";

    $db->query($sql);
    $push_mail_array = $db->fetchall();

    if(is_array($push_mail_array) && count($push_mail_array) > 0){
        foreach($push_mail_array as $key => $val){
            $sql = "select cu.id                        
                        from 
                          ".TBL_COMMON_USER." as cu 
                        left join
                            ".TBL_COMMON_MEMBER_DETAIL." as cmd on cu.code = cmd.code
                        where cu.code = '".$val[user_code]."'
            ";

            $db->query($sql);
            $db->fetch();
            $id = $db->dt['id'];

            $sql = "select pname from ".TBL_SHOP_PRODUCT." where id = '".$val['pid']."' ";
            $db->query($sql);
            $db->fetch();
            $pname = $db->dt['pname'];

            $mail_info[mem_mail] = $val['user_mail'];
            $mail_info['mem_mobile'] = $val['pcs'];
            $mail_info[mem_id] = $id;
            $mail_info[mem_name] = $id;
            $mail_info[pname] = $pname;
			$mail_info[pid] = $val['pid'];

			$phonePattern = '/^(010|011|016|017|018|019)-[0-9]{3,4}-[0-9]{4}$/';

			//패턴 체크(유효성 검사)
			if(preg_match($phonePattern, $mail_info['mem_mobile'], $match)){

				sendMessageByStep('stock_reminder', $mail_info);
			} 

            $sql = "update shop_product_stock_reminder set status = 'Y', reminder_date = NOW() where sr_ix = '".$val['sr_ix']."'";
            $db->query($sql);
        }
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('재입고알림이 발송되었습니다.','parent_reload');</script>");
        exit;
    }else{
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('발송 대상자가 존재하지 않습니다.','parent_reload');</script>");
        exit;
    }
    
}
if($act == 'pushAll'){
    $srIx = explode(',',$srIx);
    if(is_array($srIx)){

        foreach($srIx as $key=>$val){
            $sql = "select pid, op_id from shop_product_stock_reminder where sr_ix='".$val."' ";
            $db->query($sql);
            $db->fetch();
            $pid = $db->dt['pid'];
            $op_id = $db->dt['op_id'];

            $sql = "select * from shop_product_stock_reminder where status='N' and pid = '".$pid."' and op_id = '".$op_id."'  ";
            $db->query($sql);
            $push_mail_array = $db->fetchall();

            if(is_array($push_mail_array) && count($push_mail_array) > 0){
                foreach($push_mail_array as $key => $val){
                    $sql = "select cu.id                        
                        from 
                          ".TBL_COMMON_USER." as cu 
                        left join
                            ".TBL_COMMON_MEMBER_DETAIL." as cmd on cu.code = cmd.code
                        where cu.code = '".$val[user_code]."' ";

                    $db->query($sql);
                    $db->fetch();
                    $id = $db->dt['id'];


                    $sql = "select pname from ".TBL_SHOP_PRODUCT." where id = '".$val['pid']."' ";
                    $db->query($sql);
                    $db->fetch();
                    $pname = $db->dt['pname'];

                    $mail_info[mem_mail] = $val['user_mail'];
                    $mail_info['mem_mobile'] = $val['pcs'];
                    $mail_info[mem_id] = $id;
                    $mail_info[mem_name] = $id;
                    $mail_info[pname] = $pname;
					$mail_info[pid] = $val['pid'];

                    sendMessageByStep('stock_reminder', $mail_info);

                    $sql = "update shop_product_stock_reminder set status = 'Y', reminder_date = NOW() where sr_ix = '".$val['sr_ix']."'";
                    $db->query($sql);
                }
            }
        }
    }
}

if($_POST['act'] == 'statusAll'){
    if(is_array($_POST['srIx'])){
        foreach($_POST['srIx'] as $key=>$val){
			$sql = "select pid, op_id from shop_product_stock_reminder where sr_ix='".$val."' ";
            $db->query($sql);
            $db->fetch();
            $pid = $db->dt['pid'];
            $op_id = $db->dt['op_id'];

            $sql = "select * from shop_product_stock_reminder where pid = '".$pid."' and op_id = '".$op_id."'  ";
            $db->query($sql);
            $push_mail_array = $db->fetchall();

            if(is_array($push_mail_array) && count($push_mail_array) > 0){
                foreach($push_mail_array as $key => $val){
					$sql = "update shop_product_stock_reminder set status = '".$_POST['status']."', reminder_date = NOW() where sr_ix = '".$val['sr_ix']."'";
					$db->query($sql);
				}
            }
        }
    }
}