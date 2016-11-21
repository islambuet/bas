<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    if(isset($CI->permissions['action1'])&&($CI->permissions['action1']==1))
    {
        $action_data["action_new"]=base_url($CI->controller_url."/index/add");
    }
    if(isset($CI->permissions['action2'])&&($CI->permissions['action2']==1))
    {
        $action_data["action_edit"]=base_url($CI->controller_url."/index/edit");
    }
    if(isset($CI->permissions['action0'])&&($CI->permissions['action0']==1))
    {
        $action_data["action_details"]=base_url($CI->controller_url."/index/details");
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
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="amount"><?php echo $CI->lang->line('LABEL_AMOUNT'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="company_name"><?php echo $CI->lang->line('LABEL_COMPANY_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="advance_for">Advance For</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="created_by">Created By</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="created_date">Created Date</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="status_checkin_advance">Check In</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="status_approval_advance">Approval</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="status_payment_advance">Payment</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="remarks_advance">Remarks</label>
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
                { name: 'amount', type: 'string' },
                { name: 'company_name', type: 'string' },
                { name: 'advance_for', type: 'string' },
                { name: 'created_by', type: 'string' },
                { name: 'created_date', type: 'string' },
                { name: 'status_checkin_advance', type: 'string' },
                { name: 'status_approval_advance', type: 'string' },
                { name: 'status_payment_advance', type: 'string' },
                { name: 'remarks_advance', type: 'string' }
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
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT'); ?>', dataField: 'amount',width:'100',cellsAlign:'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_COMPANY_NAME'); ?>', dataField: 'company_name',filtertype: 'list',width:'150',cellsalign: 'right'},
                    { text: 'Advance For', dataField: 'advance_for',width:'100'},
                    { text: 'Created By', dataField: 'created_by',width:'100'},
                    { text: 'Created Date', dataField: 'created_date',width:'100'},
                    { text: 'Check In', dataField: 'status_checkin_advance',filtertype: 'list',width:'150',cellsalign: 'right'},
                    { text: 'Approval', dataField: 'status_approval_advance',filtertype: 'list',width:'150',cellsalign: 'right'},
                    { text: 'Payment', dataField: 'status_payment_advance',filtertype: 'list',width:'150',cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_REMARKS'); ?>', dataField: 'remarks_advance'}

                ]
            });
    });
</script>