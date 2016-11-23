<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_refresh"]=base_url($CI->controller_url."/index/details/".$item['id']);
    $CI->load->view("action_buttons",$action_data);
?>
    <div class="row widget">
        <div class="widget-header hidden-print">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label"><?php echo $CI->lang->line('LABEL_COMPANY_NAME');?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                foreach($companies as $row)
                {
                    if($row['id']==$item['company_id'])
                    {
                        ?>
                        <label class="control-label"><?php echo $row['full_name'];?></label>
                        <?php
                        break;
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
