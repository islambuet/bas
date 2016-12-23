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
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TITLE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[title]" id="title" class="form-control" style="text-align: left;" value="<?php echo $item['title'] ?>" >
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_COMPANY_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="company_id" name="item[company_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($companies as $row)
                    {?>
                        <option value="<?php echo $row['id']?>" <?php if($row['id']==$item['company_id']){ echo "selected";}?>><?php echo $row['full_name'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="<?php if(!($item['employee_id']>0)){echo 'display:none';} ?>" class="row show-grid" id="employee_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_EMPLOYEE_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="employee_id" name="item[employee_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($employees as $row)
                    {?>
                        <option value="<?php echo $row['value']?>" <?php if($row['value']==$item['employee_id']){ echo "selected";}?>><?php echo $row['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_start]" id="date_start" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_start']);?>"/>
            </div>
        </div>
        <div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="num_days_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NUM_PERSON');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="date_end" name="date_end" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    for($i=1;$i<=100;$i++)
                    {?>
                        <option value="<?php echo $i;?>" <?php if($i==$item['date_end']){ echo "selected";}?>><?php echo $i;?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid" id="remarks_advance">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" name="item[remarks_advance]"><?php echo $item['remarks_advance']; ?></textarea>
            </div>
        </div>
        <div id="system_report_container">
        <?php
        if(($item['id'])>0)
        {
            $this->load->view($CI->controller_url."/form",$details);
        }
        ?>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    /*function calculate_total()
    {
        var daily_total=0;
        var fixed_total=0;
        $('.daily').each(function()
        {
            var value=parseFloat($(this).val()) || 0;
            daily_total+=value;
        });
        $('.fixed').each(function()
        {
            var value=parseFloat($(this).val()) || 0;
            fixed_total+=value;
        });
        $('#daily_total').html(number_format(daily_total,2));
        $('#fixed_total').html(number_format(fixed_total,2));
        $('#total').html(number_format(daily_total+fixed_total,2));
    }*/
    jQuery(document).ready(function()
    {
        $(document).off("change", "#company_id");
        $(document).off("change", "#employee_id");
        $(document).off("change", "#date_end");
        //$(document).off("change", ".fixed");
        //$(document).off("change", ".daily");
        $(".datepicker").datepicker({dateFormat : display_date_format});
        $(document).on("change","#company_id",function()
        {
            $("#employee_id").val("");
            $("#date_end").val("");
            var company_id=$('#company_id').val();
            $('#employee_id_container').hide();
            $('#num_days_container').hide();
            $('#system_report_container').html('');
            if(company_id>0)
            {
                $('#employee_id_container').show();
                $.ajax({
                    url: '<?php echo site_url("common_controller/get_dropdown_employees_by_company_id/"); ?>',
                    type: 'POST',
                    datatype: "JSON",
                    data:{company_id:company_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
        });
        $(document).on("change","#employee_id",function()
        {
            $("#date_end").val("");
            $('#num_days_container').hide();
            $('#system_report_container').html('');
            var employee_id=$('#employee_id').val();
            if(employee_id>0)
            {
                $('#num_days_container').show();
            }
        });
        $(document).on("change","#date_end",function()
        {
            $('#system_report_container').html('');
            var employee_id=$('#employee_id').val();
            var num_person=$('#date_end').val();
            if(num_person>0)
            {
                $.ajax({
                    url: '<?php echo site_url($CI->controller_url."/index/get_form");?>',
                    type: 'POST',
                    datatype: "JSON",
                    data:{num_person:num_person},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
        });
        /*$(document).on("change",".fixed",function()
        {
            calculate_total();
        });
        $(document).on("change",".daily",function()
        {
            calculate_total();
        });*/


    });
</script>
