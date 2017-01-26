<?php
$config['system_site_short_name']='bas';
$config['offline_controllers']=array('home','sys_site_offline');//active controller when site is offline
$config['external_controllers']=array('home');//user can use them without login
$config['system_max_actions']=7;

//dbs
$config['system_db_login']='shaiful_arm_login';
$config['system_db_ems']='shaiful_arm_ems';
$config['system_db_bms']='shaiful_arm_ems';
$config['system_db_bas']='arm_bas';

$config['system_status_active']='Active';
$config['system_status_inactive']='In-Active';
$config['system_status_delete']='Deleted';
/*//bank related
$config['system_transaction_cash_in']='CASH_IN';
$config['system_transaction_bank_bank']='TRANSFER_BANK_TO_BANK';
$config['system_transaction_bank_petty_cash']='TRANSFER_BANK_TO_PETTY_CASH';

//petty cash related
$config['system_status_pending']='Pending';
$config['system_status_checked']='Checked';
$config['system_status_approved']='Approved';

$config['system_petty_iou_tour']='IOU_TOUR';
$config['system_petty_iou_cash']='IOU_CASH';
$config['system_petty_iou_daily_food']='IOU_DAILY_FOOD';
$config['system_petty_iou_monthly_purchase']='IOU_MONTHLY_PURCHASE';

$config['system_user_type_employee_id']=1;

*/