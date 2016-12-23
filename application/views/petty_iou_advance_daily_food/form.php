<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
echo '<PRE>';
print_r($daily_food_items);
echo '</PRE>';
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
            $daily_total=0;
            foreach($daily_items as $row)
            {
                ?>
                <div style="" class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $row['text'];?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <input type="text" name="daily[<?php echo $row['value']; ?>]" class="daily form-control float_type_positive" style="text-align: left;" value="<?php if(isset($daily[$row['value']])){echo $daily[$row['value']];$daily_total+=$daily[$row['value']]; }else{echo '0';} ?>" >
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
                    <label class="control-label" id="daily_total"><?php echo number_format($daily_total,2); ?></label>
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
            <?php
            $fixed_total=0;
            foreach($fixed_items as $row)
            {
                ?>
                <div style="" class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $row['text'];?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <input type="text" name="fixed[<?php echo $row['value']; ?>]" class="fixed form-control float_type_positive" style="text-align: left;" value="<?php if(isset($fixed[$row['value']])){echo $fixed[$row['value']];$fixed_total+=$fixed[$row['value']]; }else{echo '0';} ?>" >
                    </div>
                </div>
            <?php
            }
            ?>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Variance Total</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label" id="fixed_total"><?php echo number_format($fixed_total,2); ?></label>
                </div>
            </div>
        </div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Total</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label" id="total"><?php echo number_format($daily_total+$fixed_total,2); ?></label>
        </div>
    </div>
</div>