<div class="ibox float-e-margins" style="position: relative;">
    <div class="ibox-title">
        <h5><?= _g('Notifications'); ?></h5>
    </div>
    <div class="ibox-content" style="max-height: 400px; overflow-y: scroll">
            {% for notice in notices %}
            {{widget('AlertTimelineWidget', 'item', ['noticeId':notice.getNoticeId()])}}
            {% endfor %}
            {% if notices.count()==0 %}
                <div class="alert alert-info" style="margin-bottom: 0px;" role="alert"><?= _g('You do not have any unread notifications'); ?></div>
            {% endif %}
        <script src="/js/compiled/public/js/raw/library/generic.ajax.min.js"></script>
    </div>
</div>