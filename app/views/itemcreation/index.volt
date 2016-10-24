<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    function advancedSearch(){
        $('#advanced-search').toggle('fast');
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2>The Vault</h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <a class="btn btn-warning" href="/curation" style="margin-bottom: 15px;">
            <i class="fa fa-exclamation-triangle"></i> 4 Items require curation
        </a>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>All Warehouse Items</h5>
                <span class="pull-right"><a onclick="advancedSearch()" style="cursor: pointer">Search &gt;</a></span>
            </div>
            <div class="ibox-content">
                <div id="advanced-search" style="display: none;">
                    <form method="post" enctype="multipart/form-data" action="#" class="form-horizontal">
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="date-range" class="control-label">Date Range</label>
                                <input type="text" class="form-control" id="date-range" name="date-range">
                            </div>
                            <div class="col-sm-8">
                                <label for="item-name" class="control-label">Item Name</label>
                                <input type="text" class="form-control" id="item-name" name="item-name">
                            </div>
                            <div class="col-sm-4">
                                <label for="supplier" class="control-label">Supplier</label>
                                <select class="form-control" id="supplier" name="supplier">
                                    <option>Any</option>
                                    <option>Bentley</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label for="status" class="control-label">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option>Any</option>
                                    <option>Available Packages</option>
                                    <option>Fully Booked</option>
                                    <option>Canceled</option>
                                    <option>Completed</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label for="type" class="control-label">Type</label>
                                <select class="form-control" id="type" name="type">
                                    <option>Any</option>
                                    <option>Event</option>
                                    <option>Offer</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-primary" style="margin-top: 25px;">Search</button>
                    </form>
                </div>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="hidden-xs">Creation Date</th>
                        <th>Item</th>
                        <th class="hidden-xs">Type</th>
                        <th class="hidden-xs">Supplier</th>
                        <th class="hidden-xs">Creator</th>
                        <th class="hidden-xs">Delivery</th>
                        <th class="hidden-xs">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="hidden-xs">12/11/2014</td>
                        <td><a href="/adminwarehouse/view">Power on ice - The ultimate winter driving experience</a></td>
                        <td class="hidden-xs">Event</td>
                        <td class="hidden-xs"><a href="/portals/profile/441">Bentley</a></td>
                        <td class="hidden-xs"><a href="/adminusers/viewuser/171">Daniel Dimmick</a></td>
                        <td class="hidden-xs">Marketplace</td>
                        <td class="hidden-xs"><span class="label label-success">Fully Booked</span></td>
                    </tr>
                    <tr>
                        <td class="hidden-xs">10/09/2014</td>
                        <td><a href="/adminwarehouse/view">Power on ice - The ultimate winter driving experience</a></td>
                        <td class="hidden-xs">Event</td>
                        <td class="hidden-xs"><a href="/portals/profile/441">Bentley</a></td>
                        <td class="hidden-xs"><a href="/adminusers/viewuser/171">Daniel Dimmick</a></td>
                        <td class="hidden-xs">Marketplace</td>
                        <td class="hidden-xs"><span class="label label-warning">5 Packages Available</span></td>
                    </tr>
                    <tr>
                        <td class="hidden-xs">17/08/2014</td>
                        <td><a href="/adminwarehouse/view">Power on ice - The ultimate winter driving experience</a></td>
                        <td class="hidden-xs">Event</td>
                        <td class="hidden-xs"><a href="/portals/profile/441">Bentley</a></td>
                        <td class="hidden-xs"><a href="/adminusers/viewuser/171">Daniel Dimmick</a></td>
                        <td class="hidden-xs">Curation</td>
                        <td class="hidden-xs"><span class="label label-danger">Waiting For Curation</span></td>
                    </tr>
                    <tr>
                        <td class="hidden-xs">15/08/2014</td>
                        <td><a href="/adminwarehouse/view">Power on ice - The ultimate winter driving experience</a></td>
                        <td class="hidden-xs">Event</td>
                        <td class="hidden-xs"><a href="/portals/profile/441">Bentley</a></td>
                        <td class="hidden-xs"><a href="/adminusers/viewuser/171">Daniel Dimmick</a></td>
                        <td class="hidden-xs">Marketplace</td>
                        <td class="hidden-xs"><span class="label label-success">Fully Booked</span></td>
                    </tr>
                    <tr>
                        <td class="hidden-xs">10/09/2014</td>
                        <td><a href="/adminwarehouse/view">Power on ice - The ultimate winter driving experience</a></td>
                        <td class="hidden-xs">Event</td>
                        <td class="hidden-xs"><a href="/portals/profile/441">Bentley</a></td>
                        <td class="hidden-xs"><a href="/adminusers/viewuser/171">Daniel Dimmick</a></td>
                        <td class="hidden-xs">Curation</td>
                        <td class="hidden-xs"><span class="label label-success">Fully Booked</span></td>
                    </tr>
                    <tr>
                        <td class="hidden-xs">17/08/2014</td>
                        <td><a href="/adminwarehouse/view">Power on ice - The ultimate winter driving experience</a></td>
                        <td class="hidden-xs">Event</td>
                        <td class="hidden-xs"><a href="/portals/profile/441">Bentley</a></td>
                        <td class="hidden-xs"><a href="/adminusers/viewuser/171">Daniel Dimmick</a></td>
                        <td class="hidden-xs">Curation</td>
                        <td class="hidden-xs"><span class="label label-warning">15 Packages Available</span></td>
                    </tr>
                    <tr>
                        <td class="hidden-xs">15/08/2014</td>
                        <td><a href="/adminwarehouse/view">Power on ice - The ultimate winter driving experience</a></td>
                        <td class="hidden-xs">Event</td>
                        <td class="hidden-xs"><a href="/portals/profile/441">Bentley</a></td>
                        <td class="hidden-xs"><a href="/adminusers/viewuser/171">Daniel Dimmick</a></td>
                        <td class="hidden-xs">Marketplace</td>
                        <td class="hidden-xs"><span class="label label-warning">1 Packages Available</span></td>
                    </tr>
                    <tr>
                        <td class="hidden-xs">10/09/2014</td>
                        <td><a href="/adminwarehouse/view">Power on ice - The ultimate winter driving experience</a></td>
                        <td class="hidden-xs">Event</td>
                        <td class="hidden-xs"><a href="/portals/profile/441">Bentley</a></td>
                        <td class="hidden-xs"><a href="/adminusers/viewuser/171">Daniel Dimmick</a></td>
                        <td class="hidden-xs">Marketplace</td>
                        <td class="hidden-xs"><span class="label label-success">Fully Booked</span></td>
                    </tr>
                    <tr>
                        <td class="hidden-xs">17/08/2014</td>
                        <td><a href="/adminwarehouse/view">Power on ice - The ultimate winter driving experience</a></td>
                        <td class="hidden-xs">Event</td>
                        <td class="hidden-xs"><a href="/portals/profile/441">Bentley</a></td>
                        <td class="hidden-xs"><a href="/adminusers/viewuser/171">Daniel Dimmick</a></td>
                        <td class="hidden-xs">Marketplace</td>
                        <td class="hidden-xs"><span class="label label-success">Fully Booked</span></td>
                    </tr>
                    <tr>
                        <td class="hidden-xs">15/08/2014</td>
                        <td><a href="/adminwarehouse/view">Power on ice - The ultimate winter driving experience</a></td>
                        <td class="hidden-xs">Event</td>
                        <td class="hidden-xs"><a href="/portals/profile/441">Bentley</a></td>
                        <td class="hidden-xs"><a href="/adminusers/viewuser/171">Daniel Dimmick</a></td>
                        <td class="hidden-xs">Marketplace</td>
                        <td class="hidden-xs"><span class="label label-success">Fully Booked</span></td>
                    </tr>
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination pagination-sm" style="margin: 0px;">
                        <li><a href="#">«</a></li>
                        <li><a href="#">1</a></li>
                        <li><a href="#">2</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#">»</a></li>
                    </ul>
                            <span class="pull-right">
                                <a style="cursor: pointer"><i class="fa fa-file-excel-o"></i> Export</a>
                            </span>
                </nav>
            </div>
        </div>
    </div>
</div>
