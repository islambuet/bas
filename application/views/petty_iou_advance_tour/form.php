<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
?>


<div class="panel-group" id="accordion">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a class="accordion-toggle external" data-toggle="collapse"  data-target="#collapse_daily" href="#">Daily Allowance</a>
            </h4>
        </div>
        <div id="collapse_daily" class="panel-collapse collapse in">
            <?php
            foreach($tour_allowance_items as $row)
            {
                ?>
                <div style="" class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $row['text'];?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php if(isset($DAILY[$row['value']])){echo $DAILY[$row['value']]; }else{echo '0';} ?></label>
                    </div>
                </div>
                <?php
            }
            ?>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Daily Total</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label" id="daily_total"><?php echo $daily_total; ?></label>
                </div>
            </div>

        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a class="accordion-toggle external" data-toggle="collapse"  data-target="#collapse_fixed" href="#">Variable allowance</a>
            </h4>
        </div>
        <div id="collapse_fixed" class="panel-collapse collapse in">
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_HOTEL_BILL');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="tour[FIXED][HOTEL]" id="hotel" class="form-control float_type_positive" style="text-align: left;" value="<?php echo $FIXED['HOTEL'] ?>" >
                </div>
            </div>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TRANSPORT_BILL');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="tour[FIXED][TRANSPORT]" id="transport" class="form-control float_type_positive" style="text-align: left;" value="<?php echo $FIXED['TRANSPORT'] ?>" >
                </div>
            </div>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OTHER_BILL');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="tour[FIXED][OTHER]" id="other" class="form-control float_type_positive" style="text-align: left;" value="<?php echo $FIXED['OTHER'] ?>" >
                </div>
            </div>
        </div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Total</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label" id="total"><?php echo $total; ?></label>
        </div>
    </div>
</div>