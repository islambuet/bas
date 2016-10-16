<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_data=array();
$action_data["action_back"]=base_url($CI->controller_url);
$action_data["action_save"]='#save_form';
$action_data["action_clear"]='#save_form';
$CI->load->view("action_buttons",$action_data);
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $account_id; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-xs-12" style="overflow-x: auto;">
            <table class="table table-hover table-bordered">
                <thead>
                <tr>
                    <th><?php echo $CI->lang->line("LABEL_CATEGORY_NAME");?></th>
                    <th><?php echo $CI->lang->line("LABEL_TYPE_NAME");?></th>
                    <th><?php echo $CI->lang->line("LABEL_EXPENSE_NAME");?></th>
                </tr>
                </thead>

                <tbody>
                <?php
                if(sizeof($expenses)>0)
                {
                    foreach($expenses as $category)
                    {
                        $start_category=true;
                        foreach ($category['types'] as $type)
                        {
                            $start_type=true;
                            foreach($type['expenses'] as $expense)
                            {
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                        if($start_category)
                                        {
                                            ?>
                                            <input type="checkbox" data-id='<?php echo $category['id']; ?>' class="category"><?php echo $category['name'];?>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($start_type)
                                        {
                                            ?>
                                            <input type="checkbox" data-id='<?php echo $type['id']; ?>' class="type category_<?php echo $category['id']; ?>"><?php echo $type['name'];?>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td><input type="checkbox" class="expense category_<?php echo $category['id']; ?> type_<?php echo $type['id']; ?>" <?php if(in_array($expense['id'],$expenses_status)){echo 'checked';}?> value="<?php echo $expense['id'];?>" name="expense[<?php echo $expense['id'];?>]"><?php echo $expense['name'];?></td>
                                </tr>
                                <?php
                                $start_category=false;
                                $start_type=false;
                            }
                        }
                    }
                }
                else
                {
                    ?>
                    <tr>
                        <td colspan="20" class="text-center alert-danger">
                            <?php echo $CI->lang->line("NO_DATA_FOUND"); ?>
                        </td>
                    </tr>
                <?php
                }
                ?>

                </tbody>
            </table>
        </div>


    </div>
    <div class="clearfix"></div>
</form>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();
        $(document).off("click", ".category");
        $(document).off("click", ".type");
        $(document).on("click",'.category',function()
        {
            //console.log('task_action clicked');
            if($(this).is(':checked'))
            {
                $('.category_'+$(this).attr('data-id')).prop('checked', true);

            }
            else
            {
                $('.category_'+$(this).attr('data-id')).prop('checked', false);
            }

        });
        $(document).on("click",'.type',function()
        {
            //console.log('task_action clicked');
            if($(this).is(':checked'))
            {
                $('.type_'+$(this).attr('data-id')).prop('checked', true);

            }
            else
            {
                $('.type_'+$(this).attr('data-id')).prop('checked', false);
            }

        });
    });

</script>
