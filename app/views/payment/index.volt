<?php if($this->view->noorder == true): ?>
<div class="alert alert-info">
    You have no order set or the order number is invalid.  Did you want to see your pending orders? #link
</div>
<?php elseif($this->view->wrongstatus == true): ?>
    <div class="alert alert-warning">
        This order is already processing, complete, or cancelled
    </div>
<?php elseif(! $this->view->canPay): ?>
<div class="alert alert-danger">
    <?= _g('Payment is not configured for this supplier'); ?>
</div>
<?php else: ?>
<h2>Checkout</h2>
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5><?= _g("Order Details"); ?></h5>
        </div>
        <div class="ibox-content">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Item</th>
                    <th>Start Date</th>
                    <th>Supplier</th>
                    <th>Payment</th>
                    <th>Packages</th>
                    <th>Spaces per Package</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($this->view->order->getOrderItems() as $item): ?>
                    <?php
                    $event=Event::findFirstBy('itemId',$item->getItem()->getItemId());
                    ?>
                    <tr>
                        <td><a target="_blank" href="/vault/event/<?= $item->getItem()->getItemId(); ?>"><?= _eh($item->getItem()->getTitle()); ?></a></td>
                        <td><?= date('d-m-Y H:i:s',strtotime($event->getStartDateTime())); ?></td>
                        <td>
                            <?php
                            $supplier=User::findFirstBy('userId',$item->getItem()->getCreatorId());
                            echo $supplier->getOrganisation()->getOrganisationName();
                            ?>
                        </td>
                        <td><?= $item->getStatus(); ?></td>
                        <td><?= $item->getPackageQuantity(); ?></td>
                        <td><?= $item->getPackageSize(); ?></td>
                        <td><?= $item->getFormattedValue(); ?></td>
                        <td><?php if($item->getisPaidFull()==1){echo _g('Paid');}else{echo _g('Unpaid');}?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <h3><?= $order->getFormattedFullTotal(); ?></h3>
            <?php if($this->view->zeroPriceOrder): ?>
                <form action="/payment/complimentary/" method="post">
                    <input type='hidden' name='orderId' value='<?= $order->getOrderId(); ?>' />
                    {{csrf()}}
                    <button type="submit" class="btn btn-primary"><?= _g('Complete Order'); ?></button>
                </form>
            <?php else: ?>
            {{stripe}}
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>