<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('ROI Reporting'); ?></h2>
    </div>
</div>
<div class="row" style="margin-top: 20px;">
    <?php if($this->view->role=='Manager' || $this->view->role=='Internal' || $this->view->role=='AffiliateSupplier' || $this->view->role=='ApprecieSupplier'):?>
        <div class="col-sm-3">
            <div class="jumbotron" style="padding:20px; padding-bottom: 10px; padding-top:10px;">
                <h2 style="font-size: 28px;">Events</h2>
                <p>Run a report against events you have created that are active or archived</p>
                <p><a class="btn btn-primary btn-lg" href="/roi/myevents" role="button">Generate Report</a></p>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="jumbotron" style="padding:20px; padding-bottom: 10px; padding-top:10px;">
                <h2 style="font-size: 28px;">By Arrangements</h2>
                <p>Run a report against by arrangements you have created that are active or archived</p>
                <p><a class="btn btn-primary btn-lg" href="/roi/mybyarrangements" role="button">Generate Report</a></p>
            </div>
        </div>
    <?php endif; ?>
    <?php if($this->view->role=='potato'):?>
        <div class="col-sm-3">
            <div class="jumbotron" style="padding:20px; padding-bottom: 10px; padding-top:10px;">
                <h2 style="font-size: 28px;">People</h2>
                <p>Run a report against people you have created or have visibility of</p>
                <p><a class="btn btn-primary btn-lg" href="/roi/mypeople" role="button">Generate Report</a></p>
            </div>
        </div>
    <?php endif; ?>
</div>
