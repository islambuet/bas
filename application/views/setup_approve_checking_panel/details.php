<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    if(isset($CI->permissions['action2'])&&($CI->permissions['action2']==1))
    {
        $action_data["action_edit_get"]=base_url($CI->controller_url."/index/edit/".$id);
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
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CHECKING_LIMIT');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $limit_checking; ?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-12">
                <?php
                foreach($employees as $row)
                {
                    if(in_array($row['value'],$assigned_employees))
                    {
                    ?>
                    <div class="checkbox">
                        <label class="control-label"><?php echo $row['text']; ?></label>
                    </div>
                    <?php
                    }
                }
                ?>
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
