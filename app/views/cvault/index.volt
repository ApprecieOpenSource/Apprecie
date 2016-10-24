<?php $auth = new \Apprecie\Library\Security\Authentication(); ?>

    <script src="/js/compiled/public/js/raw/library/cvault.js"></script>
    <script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
    <script src="/js/compiled/public/js/raw/library/modal.min.js"></script>
    <script src="/js/compiled/public/js/raw/library/generic.ajax.min.js"></script>
<?php $this->partial("partials/jparts/suggestedusers"); ?>
    <script>
        var userCollection=new cUserCollection(true);
        $(document).ready(function(){
            inviteSearch(1);
        });

        var pageData=null;
        function inviteSearch(pageNumber){
                var suggested=new cvaultUsers();
                var accountActive=$('#account-active').is(':checked');
                var accountDeactivated=$('#account-deactivated').is(':checked');
                var accountPending=$('#account-pending').is(':checked');

                var loginSuspended=$('#login-suspended').is(':checked');
                var loginEnabled=$('#login-enabled').is(':checked');
                var suggestions=$('#show-suggestions').is(':checked');

                suggested.setAccountActive(accountActive);
                suggested.setAccountDeactivated(accountDeactivated);
                suggested.setAccountPending(accountPending);
                suggested.setPageNumber(pageNumber);
                suggested.setSuggestions(suggestions);
                suggested.setPageNumber(pageNumber);
                if(loginSuspended===true && loginEnabled===true){
                    suggested.setLogin('All');
                }
                else if(loginSuspended===true && loginEnabled==false){
                    suggested.setLogin('suspended');
                }
                else if(loginSuspended==false && loginEnabled==true){
                    suggested.setLogin('enabled');
                }

                suggested.setEmail($('#email').val());
                suggested.setName($('#name').val());
                suggested.setReference($('#reference').val());
                suggested.setRole($('#roleName').val());
                suggested.setGroup($('#groupId').val());
                var template = $.templates("#suggestedusers");
                var page=null;
                $.when(suggested.fetch()).then(function(data){
                    pageData=data;
                    $("#user-results").html(template.render(data));
                    Pagination(data,'inviteSearch',$('#user-search-pagination'));
                    refreshHighlight();
                });
        }

        function toggleRow(userId,credit){
            userCollection.toggleUser(userId,credit);
            refreshHighlight();
            var userName=$('#userlist-'+userId).children(":eq(1)").text();
            if(userName==''){
                userName=$('#userlist-'+userId).children(":eq(2)").text();
            }
            if($.inArray( userId, userCollection.getUsers())!=-1){
                var buffer='<div id="selected-'+userId+'" style=" margin-right:10px;background-color:#5bc0de; float:left; margin-bottom:15px;padding: 5px;color: white;border-radius: 4px;"><span class="badge" style="background-color: white; color:black;cursor:pointer;" onclick="toggleRow('+userId+')">X</span> '+userName+'</div>';
                $('#selected-users').append(buffer);
            }
            else{
                $('#selected-'+userId).remove();
            }
            $('#remainingCount').html(userCollection.getRemainingUnits());
        }

        function refreshHighlight(){
            $('.highlight').removeClass('highlight');
            $.each(userCollection.getUsers(), function( index, value ) {
                $('#userlist-'+value).addClass('highlight');
            });
        }

        function selectAll(){
            var currentUsers=userCollection.getUsers();
            $.each(pageData.items, function( index, value ) {
                var userId=parseInt(value.userid);
                if($.inArray(userId,currentUsers)==-1){
                    var attr = $('#userlist-'+userId).attr('disabled');
                    if (typeof attr !== typeof undefined && attr !== false) {
                    }
                    else{
                        toggleRow(userId);

                    }
                }
            });
        }
        function deselectAll(){
            var currentUsers=userCollection.getUsers();
            $.each(pageData.items, function( index, value ) {
                var userId=parseInt(value.userid);
                if($.inArray(userId,currentUsers)!=-1){
                    toggleRow(userId);
                }
            });
        }

        function clearInviteSelected(){
            userCollection.clear();
            $('#selected-users').empty();
            refreshHighlight();
            $('#remainingCount').html(userCollection.getRemainingUnits());
        }

        function findEvents(){

        }

    </script>
    <div class="row">
        <div class="col-sm-12">
            <h2>Client Vault</h2>
            <p>To find events that best match people you can select them from the table below and press "Find Events"</p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <h3><?= _g('Selected People'); ?></h3>
            <p>
                <a class="btn btn-default" onclick="clearInviteSelected()">Clear Selection</a>
                <input style="margin-right: 15px;" type="button" class="btn btn-primary" id="process-selected" onclick="findEvents()" value="Find Events">
                <button style="display: none;" id="hidden-btn"></button>
            </p>
            <div id="selected-users">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins" style="position: relative;">
                <div class="ibox-title">
                    <h5><?= _g('Find People In Your Network'); ?></h5>
                    <span class="pull-right"><a style="text-decoration: none; cursor: pointer;" onclick="toggleFilter('#filter-container');"><i class="fa fa-filter"></i> Filter people</a></span>
                </div>
                <div class="ibox-content">
                    <div id="filter-container" style="display: none;">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="roleName" class="control-label"><?= _g('Role'); ?></label>
                                            <select id="roleName" name="roleName" class="form-control search-change">
                                                <option value="All"><?= _g('All'); ?></option>
                                                <?php $roleHierarchy = new \Apprecie\Library\Users\RoleHierarchy($auth->getSessionActiveRole()); ?>
                                                <?php foreach($roleHierarchy->getVisibleRoles() as $roleName => $roleText):?>
                                                    <option <?= ($roleName === $this->view->selectedRole) ? 'selected' : ''; ?> value="<?= $roleName;?>"><?= $roleText; ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input name="email" id="email" type="text" class="form-control search-text-change" value=""/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="name">Name</label>
                                        <input name="name" id="name" type="text" class="form-control search-text-change" value=""/>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="reference">Reference</label>
                                            <input name="reference" id="reference" type="text" class="form-control search-text-change" value=""/>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
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
                            <div class="col-sm-4">
                                <div class="checkbox" style="margin-top: 0px;">
                                    <label style="font-weight: normal; margin-right: 10px;" for="account-active"><input class="search-click" type="checkbox" id="account-active" name="account-active" <?php if($this->request->get('a')!='false'){echo 'checked';} ?>>Show active & registered people</label><br/>
                                </div>
                                <div class="checkbox">
                                    <label style="font-weight: normal; margin-right: 10px;" for="account-pending"><input class="search-click" type="checkbox" id="account-pending" name="account-pending" <?php if($this->request->get('u')!='false'){echo 'checked';} ?>> Show unregistered people</label>
                                </div>
                                <div class="checkbox">
                                    <label style="font-weight: normal; margin-right: 10px;" for="account-deactivated"><input class="search-click" type="checkbox" id="account-deactivated" name="account-deactivated"> Show deactivated people</label>
                                </div>
                                <div class="checkbox">
                                    <label style="font-weight: normal; margin-right: 10px;" for="login-enabled"><input class="search-click" type="checkbox" id="login-enabled" name="login-enabled"> Show people with login enabled</label>
                                </div>
                                <div class="checkbox">
                                    <label style="font-weight: normal; margin-right: 10px;" for="login-suspended"><input class="search-click" type="checkbox" id="login-suspended" name="login-suspended"> Show people with login suspended</label>
                                </div>
                                <div class="checkbox">
                                    <label style="font-weight: normal; margin-right: 10px;" for="show-suggestions"><input class="search-click" type="checkbox" id="show-suggestions" name="show-suggestions"> Show Suggestions only</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="button" class="btn btn-default" value="Search" onclick="inviteSearch(1);" style="margin-bottom: 15px;">
                            </div>
                        </div>
                    </div>
                    <a onclick="selectAll();" class="btn btn-default">Select All</a>
                    <a onclick="deselectAll();" class="btn btn-default">Deselect All</a>
                    <button class="btn btn-default" id="create-user-btn" data-target="#create-user-modal" data-toggle="modal">
                        <?= ($auth->getAuthenticatedUser()->hasRole(\Apprecie\Library\Users\UserRole::CLIENT)) ? _g('Create New Contact') : _g('Create New Client'); ?>
                    </button>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th class="hidden-xs"><?= _g('Reason'); ?></th>
                            <th class="hidden-xs"><?= _g('Name'); ?></th>
                            <th><?= _g('Reference'); ?></th>
                            <th><?= _g('Group'); ?></th>
                            <th><?= _g('Organisation'); ?></th>
                            <th class="hidden-xs"><?= _g('Email Address'); ?></th>
                            <th class="hidden-xs"><?= _g('Role'); ?></th>
                            <th><?= _g('Account'); ?></th>
                            <th><?= _g('Login'); ?></th>
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
            <div class="ibox float-e-margins" id="invite-links-container" style="display:none;">
                <div class="ibox-title">
                    <h5>Invitation Links</h5>
                </div>
                <div class="ibox-content">
                    <div id="invitation-links">

                    </div>
                </div>
            </div>
        </div>
    </div>
<?= (new CreateUserWidget('withOptionalEmail', array('refreshOnSuccess' => true)))->getContent(); ?>