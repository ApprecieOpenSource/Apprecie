<style>
    #user-lookup-table{
        display: none;
    }
</style>
<div class="input-group">
    <input type="text" value="" class="form-control" disabled id="user-lookup-name" name="user-lookup-name"/>
    <input type="hidden" value="" id="user-lookup-value" name="user-lookup-value"/>
    <div class="input-group-btn">
        <button type="button" class="btn btn-primary" data-toggle="modal" disabled id="user-lookup-search" data-target="#user-lookup-modal">
            Find User
        </button>
    </div>
</div>

<div class="modal fade" id="user-lookup-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">User Search</h4>
            </div>
            <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <p>Please select an owner for the new user. The owner is the person responsible for the distribution of content to the new user. The owner must always have a role above that of the new user.</p>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="user-lookup-first-name" class="control-label">First Name</label>
                                <input type="text" class="form-control" name="user-lookup-first-name" id="user-lookup-first-name" value=""/>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="user-lookup-last-name" class="control-label">Last Name</label>
                                <input type="text" id="user-lookup-last-name"  name="user-lookup-last-name" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="user-lookup-role" class="control-label">Role</label>
                                <select class="form-control" name="user-lookup-role" id="user-lookup-role">
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <input type="button" class="btn btn-primary pull-right" id="search-widget-btn" value="Search" onclick="UserLookupAdvancedSearch($('#user-lookup-role').val())" style="margin-top: 25px;"/>
                        </div>
                    </div>
                <div style="max-height: 200px; overflow-y: auto">
                    <table class="table table-hover" id="user-lookup-table">
                        <thead>
                        <th>Reference</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email Address</th>
                        </thead>
                        <tbody id="user-lookup-results-table">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>