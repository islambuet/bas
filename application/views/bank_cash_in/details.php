<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    if(isset($CI->permissions['action2'])&&($CI->permissions['action2']==1))
    {
        $action_data["action_edit_get"]=base_url($CI->controller_url."/index/edit/".$item['id']);
    }
    $CI->load->view("action_buttons",$action_data);
?>
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date($item['date_transaction']);?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ACCOUNT_NO');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                foreach($accounts as $crop)
                {
                    if($crop['value']==$item['account_id'])
                    {
                    ?>
                    <label class="control-label"><?php echo $crop['text'];?></label>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CASH_IN_TYPE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                foreach($cash_in_types as $crop)
                {
                    if($crop['value']==$item['cash_in_type_id'])
                    {
                        ?>
                        <label class="control-label"><?php echo $crop['text'];?></label>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PAYMENT_WAY');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                foreach($payment_ways as $crop)
                {
                    if($crop['value']==$item['payment_way_id'])
                    {
                        ?>
                        <label class="control-label"><?php echo $crop['text'];?></label>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo number_format($item['amount'],2);?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TRANSACTION_NUMBER');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['transaction_number'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_BANK_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                foreach($banks as $crop)
                {
                    if($crop['value']==$item['bank_id'])
                    {
                        ?>
                        <label class="control-label"><?php echo $crop['text'];?></label>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_BANK_BRANCH_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['bank_branch'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['remarks'];?></label>
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
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PICTURE');?></label>
            </div>
            <div class="col-xs-8" id="container_picture">
                <?php
                $image=base_url().'images/no_image.jpg';
                if(strlen($item['picture_url'])>0)
                {
                    $image=$item['picture_url'];
                }
                ?>
                <img style="max-width: 250px;" src="<?php echo $image;?>">
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();
    });
</script>
