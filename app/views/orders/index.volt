<div class="row">
    <div class="col-sm-12">
        <h2>Order History</h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div role="tabpanel" id="messages-tabpanel">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#item" aria-controls="home" role="tab" data-toggle="tab">Orders</a></li>
            </ul>
            <div class="tab-content" style="background-color: #ffffff; padding: 10px; margin-bottom: 15px;">
                <div role="tabpanel" class="tab-pane active" id="item">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Item</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($this->view->orders as $order): ?>
                                <tr>
                                    <td><a href="/orders/order/<?= $order->getOrderId(); ?>"><?= $order->getOrderId(); ?></a></td>
                                    <td><?= date('d-m-Y H:i:s',strtotime($order->getCreatedDate())); ?></td>
                                    <td>
                                        <?php
                                        $supplier=User::findFirstBy('userId',$order->getSupplierId());
                                        echo $supplier->getOrganisation()->getOrganisationName();
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        foreach($order->getOrderItems() as $item){
                                            echo _eh($item->getItem()->getTitle());
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?= ucfirst($order->getStatus()); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>