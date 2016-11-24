<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_save"]='#save_form';
    $action_data["action_save_new"]='#save_form';
    $action_data["action_clear"]='#save_form';
    $CI->load->view("action_buttons",$action_data);
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_transaction]" id="date_transaction" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_transaction']);?>"/>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">From <?php echo $CI->lang->line('LABEL_ACCOUNT_NO');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="from_account_id" name="item[from_account_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($accounts as $crop)
                    {?>
                        <option value="<?php echo $crop['value']?>" <?php if($crop['value']==$item['from_account_id']){ echo "selected";}?>><?php echo $crop['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">To <?php echo $CI->lang->line('LABEL_ACCOUNT_NO');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="to_account_id" name="item[to_account_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($accounts as $crop)
                    {?>
                        <option value="<?php echo $crop['value']?>" <?php if($crop['value']==$item['to_account_id']){ echo "selected";}?>><?php echo $crop['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PAYMENT_WAY');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="payment_way_id" name="item[payment_way_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($payment_ways as $crop)
                    {?>
                        <option value="<?php echo $crop['value']?>" <?php if($crop['value']==$item['payment_way_id']){ echo "selected";}?>><?php echo $crop['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[amount]" id="amount" class="form-control float_type_all" value="<?php echo $item['amount'] ?>" >
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TRANSACTION_NUMBER');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[transaction_number]" id="transaction_number" class="form-control" value="<?php echo $item['transaction_number'] ?>" >
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks]" id="remarks" class="form-control"><?php echo $item['remarks'];?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PICTURE');?></label>
            </div>
            <div class="col-xs-4" id="container_picture">
                <?php
                $image=base_url().'images/no_image.jpg';
                if(strlen($item['picture_url'])>0)
                {
                    $image=$item['picture_url'];
                }
                ?>
                <img style="max-width: 250px;" src="<?php echo $image;?>">
            </div>
            <div class="col-xs-4">
                <input type="file" class="browse_button" data-preview-container="#container_picture" name="picture">
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();
        $(".datepicker").datepicker({dateFormat : display_date_format});
        $(".browse_button").filestyle({input: false,icon: false,buttonText: "Upload",buttonName: "btn-primary"});

    });
</script>
