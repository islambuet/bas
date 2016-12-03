<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_save"]='#save_form';
    $action_data["action_clear"]='#save_form';
    $CI->load->view("action_buttons",$action_data);
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $id; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CHECKING_LIMIT');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="limit_checking" id="limit_checking" class="form-control float_type_all" value="<?php echo $limit_checking; ?>" >
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-12">
                <?php
                foreach($employees as $row)
                {
                    ?>
                    <div class="checkbox">
                        <label title="<?php echo $row['text']; ?>">
                            <input type="checkbox" name="employees[]" value="<?php echo $row['value']; ?>" <?php if(in_array($row['value'],$assigned_employees)){echo 'checked';} ?>><?php echo $row['text']; ?>
                        </label>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();

    });
</script>
