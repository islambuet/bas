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
<div class="print_container visible-print">
    <div class="row show-grid">
        <div class="col-xs-12 text-center">
            <h3>CASH REQUISITION</h3>
        </div>
        <div class="col-xs-12 text-center">
            <label class="control-label">
                <?php
                if(sizeof($companies)>0)
                {
                    echo $companies[0]['full_name'];
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
                    echo $employee['name'];
                }
                ?>
            </label>
        </div>
        <div class="col-xs-4">
            <label class="control-label"><?php echo $CI->lang->line('LABEL_DEPARTMENT_NAME');?> :
                <?php
                if(sizeof($employee)>0)
                {
                    echo $employee['department_name'];
                }
                ?>
            </label>
        </div>
        <div class="col-xs-8">
            <label class="control-label"><?php echo $CI->lang->line('LABEL_DESIGNATION_NAME');?> :
                    <?php
                    if(sizeof($employee)>0)
                    {
                        echo $employee['designation_name'];
                    }
                    ?>
            </label>
        </div>
        <div class="col-xs-4">
            <label class="control-label"><?php echo $CI->lang->line('LABEL_EMPLOYEE_ID');?> :
                    <?php
                    if(sizeof($employee)>0)
                    {
                        echo $employee['employee_id'];
                    }
                    ?>
            </label>
        </div>
    </div>
    <div class="row show-grid">
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th class="text-center"><?php echo $CI->lang->line("LABEL_PURPOSE");?></th>
                <th class="text-center" style="width: 150px;"><?php echo $CI->lang->line("LABEL_AMOUNT");?></th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $item['title'];?></td>
                    <td><?php echo number_format($item['amount_advance'],2);?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row show-grid">
        <div class="col-xs-4 text-center">
            <div>
                <label class="control-label"><?php
                    if(sizeof($employee)>0)
                    {
                        echo $employee['name'];
                    }
                    ?></label>
            </div>
            <div><label class="control-label" style="border-top: 2px solid #000000;"><?php echo $CI->lang->line('LABEL_RECEIVED_BY');?></label></div>


        </div>
        <div class="col-xs-4 text-center">
            <div>
                <label class="control-label"><?php
                    if(sizeof($employee)>0)
                    {
                        //echo $employee['name'];
                    }
                    ?></label>
            </div>
            <div><label class="control-label" style="border-top: 2px solid #000000;"><?php echo $CI->lang->line('LABEL_CHECKED_BY');?></label></div>
        </div>
        <div class="col-xs-4 text-center">
            <div>
                <label class="control-label"><?php
                    if(sizeof($employee)>0)
                    {
                        echo $employee['name'];
                    }
                    ?></label>
            </div>
            <div><label class="control-label" style="border-top: 2px solid #000000;"><?php echo $CI->lang->line('LABEL_APPROVED_BY');?></label></div>
        </div>
    </div>

</div>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        turn_off_triggers();
    });
</script>
