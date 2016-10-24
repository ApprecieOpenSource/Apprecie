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
            "iDisplayLength": 50,
            colReorder: true,
            statesave: true,
            columnDefs: [
                {responsivePriority: 1, targets: 0},
                {responsivePriority: 2, targets: 5},
                {responsivePriority: 3, targets: 2},
                {responsivePriority: 4, targets: 4},
                {responsivePriority: 5, targets: 7},
                {responsivePriority: 6, type: "currency", targets: 8},
                {responsivePriority: 7, "type": "date-uk", targets: 13},
                {"targets": 6, "type": "date-uk"},
                {"targets": [9, 10, 11], "type": "currency"}
            ],
            buttons: [
                'copy', 'csv', 'excel', 'colvis'
            ]
        });

        table.columns().every(function () {
            var that = this;

            $('input', this.footer()).on('keyup change', function () {
                if (that.search() !== this.value) {
                    that
                        .search(this.value)
                        .draw();
                }
            });
        });

        table.buttons().container()
            .appendTo($('.col-sm-6:eq(0)', table.table().container()));


        $('#portal, #orderStatus, #date, #date2').change( function() {
            table.draw();
        });
    });
</script>
<style>
    .rotate90 {
        -webkit-transform: rotate(-90deg);
        -moz-transform: rotate(-90deg);
        filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <h2>Order History</h2>
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
                            <label for="portal-name" class="control-label"><?= _g('Portal'); ?></label>
                            <select class="form-control" id="portal" name="portal">
                                <option value="" selected><?= _g('All'); ?></option>
                                <?php foreach (Portal::find() as $portal): ?>
                                    <option value="<?= $portal->getPortalName(); ?>"><?= $portal->getPortalName(); ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="organisationId" class="control-label"><?= _g('Order Status'); ?></label>
                            <select id="orderStatus" name="orderStatus" class="form-control">
                                <option value=""><?= _g('All'); ?></option>
                                <?php foreach (\Apprecie\Library\Orders\OrderStatus::getArray() as $status): ?>
                                    <option value="<?= $status ?>"><?= $status; ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label">From Date</label>
                            <div class="col-sm-8">
                                <input type="text" id="date" name="date" class="form-control" value=""/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label">To Date</label>
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
                        <th>Order ID</th>
                        <th>Purchasing Portal</th>
                        <th>Purchasing Org</th>
                        <th>Purchasing Person</th>
                        <th>Supplier</th>
                        <th>Item</th>
                        <th>Event Date</th>
                        <th>Spaces</th>
                        <th>Price</th>
                        <th>Admin Fee</th>
                        <th>Commission</th>
                        <th>Tax</th>
                        <th>Order Status</th>
                        <th>Purchase Date</th>
                        <th>Order Item ID</th>
                        <th>Item Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->view->orders as $order): ?>
                        <tr>
                            <?php $count = 0 ?>
                            <?php foreach ($order->toArray() as $field): ?>
                                <?php $count++ ?>
                                <td>
                                    <?php if ($count == 7 || $count == 14): ?>
                                        <?= _fd($field); ?>
                                    <?php else: ?>
                                        <?= $field; ?>
                                    <?php endif; ?>
                                </td>

                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>Order ID</th>
                        <th>Purchasing Portal</th>
                        <th>Purchasing Org</th>
                        <th>Purchasing Person</th>
                        <th>Supplier</th>
                        <th>Item</th>
                        <th>Event Date</th>
                        <th>Spaces</th>
                        <th>Price</th>
                        <th>Admin Fee</th>
                        <th>Commission</th>
                        <th>Tax</th>
                        <th>Order Status</th>
                        <th>Purchase Date</th>
                        <th>Order Item ID</th>
                        <th>Item Status</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>