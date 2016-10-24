<script src="/js/validation/errors.min.js"></script>
<script src="/js/validation/terms.min.js"></script>
<script src="/js/compiled/public/js/raw/library/terms.min.js"></script>
<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<script>
    $(document).ready(function(){
        searchDocuments(1);
    });

    function create(){
        clearErrors('#success-box', '#error-box');
        validateTitle($('#document-title'));
        validateVersion($('#document-version'));
        if (errors.length != 0){
            displayErrors('#error-box');
        } else {
            $('#create-btn').prop('disabled',true);
            $.when(ajaxCreate()).then(function(data){
                $('#create-btn').prop('disabled',false);
                if(data.status == 'true'){
                    $('#create').modal('hide');
                    location.replace('/legal/edit/' + data.termsId);
                }
            });
        }
    }

    function ajaxCreate(){
        return $.ajax({
            url: "/legal/ajaxCreate",
            type: 'post',
            dataType: 'json',
            cache: false,
            data: $('#create-form').serialize()
        });
    }

    function searchDocuments(pageNumber){
        var documents = new Terms();
        documents.setPageNumber(pageNumber);
        documents.setPostData({'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN});

        var results = $('#document-search-results');

        results.css('opacity',0.4);
        $.when(documents.fetch()).then(function(data) {
            $('#no-results-found').css('display','none');
            results.empty();
            results.css('opacity',1);
            if(data.items.length > 0) {
                $.each(data.items, function(key, value) {
                    var buffer = '<tr>';
                    buffer += '<td>' + value.title + '</td>';
                    buffer += '<td>' + value.version + '</td>';
                    buffer += '<td>' + value.creationDate + '</td>';
                    if (value.state === '1') {
                        buffer += '<td><span class="label label-success">On</span></td>';
                    } else {
                        buffer += '<td><span class="label label-danger">Off</span></td>';
                    }
                    buffer += '<td>' + value.settings + '</td>';
                    buffer += '<td>';

                    buffer += '<div class="btn-group pull-right">';
                    buffer += '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Options <span class="caret"></span></button>';
                    buffer += '<ul class="dropdown-menu" role="menu">';
                    buffer += '<li><a href="/legal/view/' + value.termsId + '" style="cursor: pointer;" target="_blank">View Document</a></li>';
                    buffer += '<li><a href="/legal/edit/' + value.termsId + '" style="cursor: pointer;">Edit Document</a></li>';
                    buffer += '<li><a href="/legal/settings/' + value.termsId + '" style="cursor: pointer;">Edit Settings</a></li>';
                    buffer += '</ul>';
                    buffer += '</div>';

                    buffer += '</td>';
                    buffer += '</tr>';
                    results.append(buffer);

                });
                Pagination(data,'searchDocuments',$('#document-pagination'));
            }
        });
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('All Documents'); ?></h2>
    </div>
</div>
<button class="btn btn-default" style="margin-bottom: 15px;" data-target="#create" data-toggle="modal"><?= _g('New Document'); ?></button>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-content">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th><?= _g('Title'); ?></th>
                        <th><?= _g('Version'); ?></th>
                        <th><?= _g('Creation Date'); ?></th>
                        <th><?= _g('State'); ?></th>
                        <th><?= _g('Settings'); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="document-search-results"></tbody>
                </table>
                <nav>
                    <ul class="pagination" id="document-pagination">

                    </ul>
                </nav>
                <div style="display: none;" class="alert alert-info" id="no-results-found" role="alert"><strong><?= _g('No Documents!'); ?></strong> <?= _g("Please click New Document to create a new one."); ?></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">New Document</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" id="success-box" role="alert" style="display: none;"></div>
                <div class="alert alert-danger" id="error-box" role="alert" style="display: none;"></div>
                <form class="form-horizontal" id="create-form" name="create-form">
                    <div class="form-group">
                        <label for="document-title">Title</label>
                        <input type="text" class="form-control" id="document-title" name="document-title" maxlength="100"/>
                    </div>
                    <div class="form-group">
                        <label for="document-version">Version</label>
                        <input type="text" class="form-control" id="document-version" name="document-version" maxlength="45"/>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="create-btn" onclick="create();">Create and Continue to Edit Document</button>
            </div>
        </div>
    </div>
</div>