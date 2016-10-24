<link href="//amp.azure.net/libs/amp/latest/skins/amp-default/azuremediaplayer.min.css" rel="stylesheet">
<script src= "//amp.azure.net/libs/amp/latest/azuremediaplayer.min.js"></script>
<script>
    amp.options.flashSS.swf = "//amp.azure.net/libs/amp/latest/techs/StrobeMediaPlayback.2.0.swf"
    amp.options.flashSS.plugin = "//amp.azure.net/libs/amp/latest/techs/MSAdaptiveStreamingPlugin-osmf2.0.swf"
    amp.options.silverlightSS.xap = "//amp.azure.net/libs/amp/latest/techs/SmoothStreamingPlayer.xap"
</script>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Welcome to Your Dashboard'); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-4">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5>Quick Links</h5>
            </div>
            <div class="ibox-content">
                <a href="/itemcreation/create">Create New Event</a><br/>
                <a href="/people/create">Create New Person</a><br/>
                <a href="/mycontent/events">Events I'm Hosting</a><br/>
                <a href="/eventmanagement/purchases">Events I'm Organising</a><br/>
                <a href="/eventmanagement/attending">Events I'm Attending</a><br/>
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
        {{ widget('EventTimelineWidget','hosting') }}
        {{ widget('EventTimelineWidget','acquired') }}
        {{ widget('EventTimelineWidget','attending') }}
    </div>
</div>