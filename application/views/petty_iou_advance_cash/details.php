<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    if(isset($CI->permissions['action2'])&&($CI->permissions['action2']==1))
    {
        $action_data["action_edit_get"]=base_url($CI->controller_url."/index/edit/".$item['id']);
    }
    if(isset($CI->permissions['action4'])&&($CI->permissions['action4']==1))
    {
        $action_data["action_print_page"]='Cash Requisition';
    }
    $CI->load->view("action_buttons",$action_data);
?>
<div class="hidden-print">
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_COMPANY_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if(sizeof($companies)>0)
                {
                    ?>
                    <label class="control-label"><?php echo $companies[0]['full_name'];?></label>
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_EMPLOYEE_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if(sizeof($employee)>0)
                {
                    ?>
                    <label class="control-label"><?php echo $employee['name'];?></label>
                <?php
                }
                ?>

            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PURPOSE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['title'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo number_format($item['amount_advance'],2);?></label>
            </div>
        </div>
        <div style="" class="row show-grid" id="remarks_advance">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['remarks_advance'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CREATED_BY');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $users[$item['user_created']]['name'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CREATED_TIME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date_time($item['date_created']);?></label>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</div>
<div class="print_container">
    <div class="row show-grid">
        <div class="col-xs-12 text-center">
            <h3>CASH REQUISITION</h3>
        </div>
        <div class="col-xs-12 text-center">
            <label class="control-label">
                <?php
                if(sizeof($companies)>0)
                {
                    ?>
                    <label class="control-label"><?php echo $companies[0]['full_name'];?></label>
                <?php
                }
                ?>
            </label>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-8">
        </div>
        <div class="col-xs-4">
            <label class="control-label"><?php echo $CI->lang->line('LABEL_DATE');?> : <?php echo System_helper::display_date($item['date_created']);?></label>
        </div>
        <div class="col-xs-8">
            <label class="control-label"><?php echo $CI->lang->line('LABEL_NAME');?> :
                <?php
                if(sizeof($employee)>0)
                {
                    ?>
                    <?php echo $employee['name'];?>
                <?php
                }
                ?>
            </label>
        </div>
        <div class="col-xs-4">
            <label class="control-label"><?php echo $CI->lang->line('LABEL_DEPARTMENT_NAME');?> :
                <?php
                if(sizeof($employee)>0)
                {
                    ?>
                    <?php echo $employee['department_name'];?>
                <?php
                }
                ?>
            </label>
        </div>
        <div class="col-xs-8">
            <label class="control-label"><?php echo $CI->lang->line('LABEL_DESIGNATION_NAME');?> :
                    <?php
                    if(sizeof($employee)>0)
                    {
                        ?>
                        <?php echo $employee['designation_name'];?>
                    <?php
                    }
                    ?>
            </label>
        </div>
        <div class="col-xs-4">
            <label class="control-label"><?php echo $CI->lang->line('LABEL_EMPLOYEE_ID');?> :
                    <?php
                    if(sizeof($employee)>0)
                    {
                        ?>
                        <?php echo $employee['employee_id'];?>
                    <?php
                    }
                    ?>
            </label>
        </div>
    </div>
    <div class="row show-grid">

    </div>
    <div class="row show-grid">

    </div>

</div>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        turn_off_triggers();
    });
</script>
