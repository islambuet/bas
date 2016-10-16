<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_data=array();
if(isset($CI->permissions['action2'])&&($CI->permissions['action2']==1))
{
    $action_data["action_edit"]=base_url($CI->controller_url."/index/edit");
}
if(isset($CI->permissions['action4'])&&($CI->permissions['action4']==1))
{
    $action_data["action_print"]='print';
}
if(isset($CI->permissions['action5'])&&($CI->permissions['action5']==1))
{
    $action_data["action_download"]='download';
}
$action_data["action_refresh"]=base_url($CI->controller_url."/index/list");
$CI->load->view("action_buttons",$action_data);
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    if(isset($CI->permissions['action6'])&&($CI->permissions['action6']==1))
    {
        ?>
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <div class="col-xs-12" style="margin-bottom: 20px;">
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="id"><?php echo $CI->lang->line('LABEL_ID'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="bank_name"><?php echo $CI->lang->line('LABEL_BANK_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="account_no"><?php echo $CI->lang->line('LABEL_ACCOUNT_NO'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="total_expense">Number of Expense</label>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        turn_off_triggers();
        var url = "<?php echo base_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'bank_name', type: 'string' },
                { name: 'account_no', type: 'string' },
                { name: 'num_expense', type: 'int' }
            ],
            id: 'id',
            url: url
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                source: dataAdapter,
                pageable: true,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                pagesize:50,
                pagesizeoptions: ['20', '50', '100', '200','300','500'],
                selectionmode: 'singlerow',
                altrows: true,
                autoheight: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', dataField: 'id',width:'50',cellsAlign:'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_BANK_NAME'); ?>', dataField: 'bank_name'},
                    { text: '<?php echo $CI->lang->line('LABEL_ACCOUNT_NO'); ?>', dataField: 'account_no'},
                    { text: 'Number Of Expense', dataField: 'num_expense',width:'150',cellsalign: 'right'}

                ]
            });
    });
</script>