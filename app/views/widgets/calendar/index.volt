<style>
    .day{
        width: 14.285714285714285714285714285714%;
        height: 100px;
        border: 1px solid #eeeeee;
        float: left;
        position: relative;
        padding: 5px;
        padding-top: 40px;
        font-size: 10px;
    }
    .day-header{
        width: 14.285714285714285714285714285714%;
        float:left;
        padding:5px;
        background-color: #5C5E62;
        color:white;
    }
    .day-number{
        position: absolute;
        top:0;
        left:0;
        padding: 5px;
        background-color: #e7e7e7;
    }

    .event-container{
        position: absolute;
        padding: 5px;
        color: white;
        opacity: 0.8;
        bottom:0;
        width:100%;
        background-color: black;
        margin-left: -5px;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="day-header"><?= _g('Monday'); ?></div>
        <div class="day-header"><?= _g('Tuesday'); ?></div>
        <div class="day-header"><?= _g('Wednesday'); ?></div>
        <div class="day-header"><?= _g('Thursday'); ?></div>
        <div class="day-header"><?= _g('Friday'); ?></div>
        <div class="day-header"><?= _g('Saturday'); ?></div>
        <div class="day-header"><?= _g('Sunday'); ?></div>
            <?php
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, 11, 2014);
            $firstOffset=date("w", strtotime("2014-11-01"));
            for($ii=1;$ii<$firstOffset;$ii++){
                ?>
                    <div class="day"></div>
                <?php
            }
            for($i=0;$i<$daysInMonth;$i++){
                ?>
                        <?php if($i==6 or $i==8 or $i==10){?>
                        <div class="day" style="background-image: url('/img/temp/ice-calendar.jpg')">
                            <span class="day-number">.<?= $i+1; ?></span>
                            <div class="event-container">
                                <a href="#" style="color:white;">Power on ice</a>
                            </div>
                        </div>
                        <?php }elseif($i>22 and $i<25){?>
                            <div class="day" style="background-image: url('/img/temp/shooting-calendar.jpg')">
                                <span class="day-number">.<?= $i+1; ?></span>
                                <div class="event-container">
                                    <a href="#" style="color:white;">Shooting Break</a>
                                </div>
                            </div>
                        <?php }
                        else{?>
                            <div class="day">
                                <span class="day-number">.<?= $i+1; ?></span>
                            </div>
                        <?php
                        }?>
                <?php
            }
            ?>
    </div>
</div>