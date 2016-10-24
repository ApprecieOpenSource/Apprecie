<link rel="stylesheet" type="text/css"
      href="https://cdn.datatables.net/s/bs/jq-2.1.4,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,cr-1.3.0,fc-3.2.0,fh-3.1.0,r-2.0.0/datatables.min.css"/>
<script type="text/javascript"
        src="https://cdn.datatables.net/s/bs/jq-2.1.4,dt-1.10.10,b-1.1.0,b-flash-1.1.0,b-html5-1.1.0,cr-1.3.0,fc-3.2.0,fh-3.1.0,r-2.0.0/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.1.0/js/buttons.colVis.min.js"></script>
<script>
    $.extend(jQuery.fn.dataTableExt.oSort, {
        "date-uk-pre": function (a) {
            return parseInt(moment(a, "DD/MM/YYYY").format("X"), 10);
        },
        "date-uk-asc": function (a, b) {
            return a - b;
        },
        "date-uk-desc": function (a, b) {
            return b - a;
        }
    });

    $.extend(jQuery.fn.dataTableExt.oSort, {
        "currency-pre": function (a) {
            a = (a === "-") ? 0 : a.replace(/[^\d\-\.]/g, "");
            return parseFloat(a);
        },
        "currency-asc": function (a, b) {
            return a - b;
        },
        "currency-desc": function (a, b) {
            return b - a;
        }
    });
    /*
     $.fn.dataTable.ext.search.push(
     function( settings, data, dataIndex ) {
     var iFini = document.getElementById('date').value;
     var iFfin = document.getElementById('date2').value;
     var iStartDateCol = 13;
     var iEndDateCol = 13;

     var orderstatus = document.getElementById('orderStatus').value;
     var portal = document.getElementById('portal').value;
     var portalCol = 1;
     var orderStatusCol = 12;

     if(orderstatus != "") {
     if (data[orderStatusCol] != orderstatus) {
     return false;
     }
     }

     if(portal != "") {
     if(data[portalCol] != portal) {
     return false;
     }
     }

     iFini=iFini.substring(6,10) + iFini.substring(3,5)+ iFini.substring(0,2);
     iFfin=iFfin.substring(6,10) + iFfin.substring(3,5)+ iFfin.substring(0,2);

     var datofini=data[iStartDateCol].substring(6,10) + data[iStartDateCol].substring(3,5)+ data[iStartDateCol].substring(0,2);
     var datoffin=data[iEndDateCol].substring(6,10) + data[iEndDateCol].substring(3,5)+ data[iEndDateCol].substring(0,2);

     if ( iFini === "" && iFfin === "" )
     {
     return true;
     }
     else if ( iFini <= datofini && iFfin === "")
     {
     return true;
     }
     else if ( iFfin >= datoffin && iFini === "")
     {
     return true;
     }
     else if (iFini <= datofini && iFfin >= datoffin)
     {
     return true;
     }
     return false;
     }
     );
     */
    $(document).ready(function () {
        var picker = new Pikaday(
            {
                field: document.getElementById('date'),
                firstDay: 1,
                format: 'DD/MM/YYYY',
                onSelect: function() {
                    var date = document.createTextNode(this.getMoment().format('Do MMMM YYYY') + ' ');
                    document.getElementById('selected').appendChild(date);
                }
            });
        var picker2 = new Pikaday(
            {
                field: document.getElementById('date2'),
                firstDay: 1,
                format: 'DD/MM/YYYY',
                onSelect: function() {
                    var date = document.createTextNode(this.getMoment().format('Do MMMM YYYY') + ' ');
                    document.getElementById('selected').appendChild(date);
                }
            });

        var table = $('#orders-table').DataTable({
            responsive: true,
            "order": [0, "desc"],
            "iDisplayLength": 10,
            filter: false,
            processing : true,
            serverSide: true,
            ajax: {
                url: '/items/ajaxitems',
                type: 'POST',
                "data": function (d) {
                    return $.extend( {}, d, {
                        'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN,
                        'portal': $('#portal').val(),
                        'type': $('#itemType').val(),
                        'status': $('#eventStatus').val(),
                        'tier': $('#itemTier').val(),
                        'startDate': $('#date').val(),
                        'endDate': $('#date2').val()})
                }

            },
            statesave: true,
            columnDefs: [
                {responsivePriority: 1, targets: 0},
                {responsivePriority: 2, targets: 1, "orderable": false},
                {responsivePriority: 3, targets: 2},
                {responsivePriority: 4, targets: 3, "orderable": false},
                {responsivePriority: 5, targets: 4},
                {responsivePriority: 6, targets: 16},
                {responsivePriority: 7, targets: 5},
            ]
        });

        $('#portal, #eventStatus, #date, #date2, #itemType, #itemTier').change( function() {
            table.draw();
        });
    });
</script>
<style>
    .datatable td{
        max-width: 430px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <h2>Item Management</h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5><?= _g('Search'); ?></h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="portal" class="control-label"><?= _g('Source Portal'); ?></label>
                            <select class="form-control" id="portal" name="portal">
                                <option value="" selected><?= _g('All'); ?></option>
                                <?php foreach (Portal::findAll('portalName') as $portal): ?>
                                    <option value="<?= $portal->getPortalId(); ?>"><?= $portal->getPortalName(); ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="eventStatus" class="control-label"><?= _g('Event Status'); ?></label>
                            <select id="eventStatus" name="eventStatus" class="form-control">
                                <option value=""><?= _g('All'); ?></option>
                                <option value="<?= 'pending' ?>"><?= _g('pending (awaiting approval)'); ?></option>
                                <?php foreach (\Apprecie\Library\Items\EventStatus::getArray() as $status): ?>
                                    <option value="<?= $status ?>"><?= $status; ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="itemType" class="control-label"><?= _g('Item Type'); ?></label>
                            <select id="itemType" name="itemType" class="form-control">
                                <option value=""><?= _g('All'); ?></option>
                                <option value="event"><?= _g('confirmed event'); ?></option>
                                <option value="by-arrangement"><?= _g('by-arrangement'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-sm-4">
                            <label for="itemTier" class="control-label"><?= _g('Item Tier'); ?></label>
                            <select id="itemTier" name="itemTier" class="form-control">
                                <option value=""><?= _g('All'); ?></option>
                                <?php foreach (\Apprecie\Library\Users\Tier::getArray() as $tier): ?>
                                    <option value="<?= $tier ?>"><?= (new \Apprecie\Library\Users\Tier($tier))->getText(); ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label for="date" class="col-sm-4 control-label">From Created Date</label>
                            <div class="col-sm-8">
                                <input type="text" id="date" name="date" class="form-control" value=""/>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label for="date2" class="col-sm-4 control-label">To Created Date</label>
                            <div class="col-sm-8">
                                <input type="text" id="date2" name="date2" class="form-control" value=""/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ibox">
            <div class="ibox-content" style="background-color: #ffffff; padding: 10px; margin-bottom: 15px;">

                <table id="orders-table" class="display nowrap table table-hover" width="100%">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Item</th>
                        <th>Supplier</th>
                        <th>Spaces</th>
                        <th>Type</th>
                        <th>Tier</th>
                        <th>Booking end</th>
                        <th>Event start</th>
                        <th>Event end</th>
                        <th>Packages</th>
                        <th>Package size</th>
                        <th>Contact</th>
                        <th>Price</th>
                        <th>Admin Fee</th>
                        <th>Commission</th>
                        <th>Tax</th>
                        <th>Status</th>
                        <th>Publish count</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>Id</th>
                        <th>Item</th>
                        <th>Supplier</th>
                        <th>Spaces</th>
                        <th>Type</th>
                        <th>Tier</th>
                        <th>Booking end</th>
                        <th>Event start</th>
                        <th>Event end</th>
                        <th>Packages</th>
                        <th>Package size</th>
                        <th>Contact</th>
                        <th>Price</th>
                        <th>Admin Fee</th>
                        <th>Commission</th>
                        <th>Tax</th>
                        <th>Status</th>
                        <th>Publish count</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>