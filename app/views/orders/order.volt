<div class="row">
    <div class="col-sm-12">
        <h2>Order <?= $this->view->order[0]->getOrderId();?> <div class="pull-right"><?= date('d-m-Y H:i:s',strtotime($this->view->order[0]->getCreatedDate()));?></div> </h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <p>
            <a href="/orders/" class="btn btn-default">Back</a>
            <?php if($this->view->order[0]->getStatus()=='pending'): ?>
            <a class="btn btn-primary" href="/payment/index/<?= $this->view->order[0]->getOrderId(); ?>">Make Payment</a>
            <?php endif; ?>
        </p>
        <div role="tabpanel" id="messages-tabpanel">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#item" aria-controls="home" role="tab" data-toggle="tab">Items</a></li>
            </ul>
            <div class="tab-content" style="background-color: #ffffff; padding: 10px; margin-bottom: 15px;">
                <div role="tabpanel" class="tab-pane active" id="item">
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
                            <?php foreach($this->view->order[0]->getOrderItems() as $item): ?>
                                <?php
                                $event = $item->getItem()->getEvent()->getHTMLEncodeAdapter();
                                ?>
                                <tr>
                                    <td><a target="_blank" href="/vault/event/<?= $item->getItem()->getItemId(); ?>"><?= $event->getTitle(); ?></a></td>
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
                </div>
            </div>
        </div>
    </div>
</div>