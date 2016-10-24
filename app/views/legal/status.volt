<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Current Status'); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-content">
                <ul class="list-group">
                    <?php foreach ($this->view->statusList as $item): ?>
                        <?php if ($item['terms'] === null): ?>
                            <li class="list-group-item list-group-item-<?= $item['type']; ?>"><b><?= $item['role'] . ' (' . $item['portal'] . ')'; ?></b>: <?= _g('Not set. Please go to {LINK} page to set a document for this purpose.', array('LINK' => '<a href="/legal/manage">' . _g('All Documents') . '</a>')); ?></li>
                        <?php else: ?>
                            <li class="list-group-item list-group-item-<?= $item['type']; ?>"><b><?= $item['role'] . ' (' . $item['portal'] . ')'; ?></b>: <?= $item['terms']->getDefaultName(); ?> (<?= $item['terms']->getVersion(); ?>)</li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>