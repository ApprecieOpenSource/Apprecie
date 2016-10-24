<div class="timeline-item" id="item_{{notice.getNoticeId()}}">
    <h4>
        {{notice.getTitle()}}
        <a class="pull-right" href="#" onclick="dismissAndNotify('api', 'dismissNotice', 'item_', '{{notice.getNoticeId()}}')" title="{{dismissText}}"><span class="glyphicon glyphicon-remove"></span></a>
    </h4>
    <p>
        {{notice.getBody()}}
    </p>
    <p>
        {{notice.getDate()}}
        <span class="pull-right" style="clear:both;"><a href="{{notice.getUrl()}}">More Details ></a></span>
    </p>
</div>