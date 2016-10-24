<div class="row">
    <div class="col-md-12">
        <h2><?= _g('Terms and Conditions'); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-content">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th><?= _g('Title'); ?></th>
                        <th><?= _g('Version'); ?></th>
                        <th><?= _g('Acceptance Date'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->view->myTerms as $termsId => $myTermsRow): ?>
                        <tr>
                            <td><a target="_blank" href="/legal/view/<?= $termsId; ?>"><?= $myTermsRow['terms']->getDefaultName(); ?></a></td>
                            <td><?= $myTermsRow['terms']->getVersion(); ?></td>
                            <td><?= _fdt($myTermsRow['userTerms']->getAcceptedDate()); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>