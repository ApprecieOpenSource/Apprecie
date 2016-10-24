<script src="/js/compiled/public/js/raw/library/contacts.min.js"></script>
<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<script>
    $(document).ready(function() {
        $('.search-click').click(function(){
            peopleSearch(1);
        });
        $('.search-change').change(function(){
            peopleSearch(1);
        });
        peopleSearch(1);

        var searchTimeout = null;
        $('.search-text-change').keyup(function(){
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                peopleSearch(1);
            }, 700);
        })
    });

    function peopleSearch(pageNumber){
        var search = new SearchContacts();

        search.setPageNumber(pageNumber);

        search.setEmail($('#email').val());
        search.setName($('#name').val());
        search.setReference($('#reference').val());
        search.setGroup($('#groupId').val());

        $.when(search.fetch()).then(function(data){
            if(data.status == 'failed'){
                buffer = '<tr><td colspan="8">' + data.message + '</td></tr>';
            } else {
                var buffer = '';
                $.each(data.items, function(key, value) {
                    buffer += '<tr>';
                    buffer += '<td>' + value.image + '</td>';
                    buffer += '<td class="hidden-xs"><a href="/contacts/viewuser/' + value.userid + '">' + value.profile.firstname + ' ' + value.profile.lastname + '</a></td>';
                    buffer += '<td><a href="/contacts/viewuser/' + value.userid + '">' + value.reference + '</a></td>';
                    buffer += '<td>' + value.groups + '</td>';
                    buffer += '<td>'+ value.organisation + '</td>';
                    buffer += '<td class="hidden-xs">' + value.profile.email + '</td>';
                    buffer += '</tr>';
                });
                Pagination(data, 'peopleSearch', $('#user-search-pagination'));
            }
            $('#user-results').html(buffer);
        })
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2>
            <div class="pull-right dropdown">
                <span class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true" style="margin-right: 10px;cursor: pointer;">
                    <button class="btn" style="margin-top: 5px; margin-right: -10px;"><i class="fa fa-ellipsis-v"></i> Actions</button>
                </span>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                    <li><a role="menuitem" tabindex="-1" href="/contacts/create">New Contact</a></li>
                    <li><a role="menuitem" tabindex="-1" href="/groups/index">Group Management</a></li>
                </ul>
            </div>
            <?= _g('All Contacts'); ?>
        </h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5><?= _g('Contacts in your network'); ?></h5>
                <span class="pull-right"><a style="text-decoration: none; cursor: pointer;" onclick="toggleFilter('#filter-container');"><i class="fa fa-filter"></i> Filter contacts</a></span>
            </div>
            <div class="ibox-content">
                <div id="filter-container" style="display: none;">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row">
                                {{csrf()}}
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input name="email" id="email" type="text" class="form-control search-text-change" value=""/>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label for="name">Name</label>
                                    <input name="name" id="name" type="text" class="form-control search-text-change" value=""/>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="reference">Reference</label>
                                        <input name="reference" id="reference" type="text" class="form-control search-text-change" value=""/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="group">Group</label>
                                        <select class="form-control search-change" id="groupId" name="groupId">
                                            <option value="all">All</option>
                                            <?php foreach($this->view->groups as $group): ?>
                                                <option value="<?= $group->getGroupId(); ?>"><?= $group->getGroupName(); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th></th>
                        <th class="hidden-xs"><?= _g('Name'); ?></th>
                        <th><?= _g('Reference'); ?></th>
                        <th><?= _g('Group'); ?></th>
                        <th><?= _g('Organisation'); ?></th>
                        <th class="hidden-xs"><?= _g('Email Address'); ?></th>
                    </tr>
                    </thead>
                    <tbody id="user-results">

                    </tbody>
                </table>
                <nav>
                    <ul class="pagination pagination-sm" id="user-search-pagination">

                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>