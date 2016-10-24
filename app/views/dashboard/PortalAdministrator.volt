<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Welcome to your Dashboard'); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-4">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5>Quick Links</h5>
            </div>
            <div class="ibox-content">
                <a href="/people/create">Create New Person</a><br/>
            </div>
        </div>
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5>Account Health</h5>
            </div>
            <div class="ibox-content">
                {{ widget('RegistrationStatsWidget','index') }}
            </div>
        </div>
        {{ widget('AlertTimelineWidget') }}
    </div>
    <div class="col-sm-8">

    </div>
</div>