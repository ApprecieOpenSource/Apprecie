<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Welcome to Your Dashboard'); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-4">
        {{ widget('AlertTimelineWidget') }}
    </div>
    <div class="col-sm-8">
        {{ widget('EventTimelineWidget','attending') }}
        {{ widget('EventTimelineWidget','acquired') }}
    </div>
</div>