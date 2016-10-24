<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= _g('Latest Users'); ?></h5>
            </div>
            <div class="ibox-content">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th><?= _g('Name'); ?></th>
                        <th class="hidden-xs"><?= _g('Join Date'); ?></th>
                        <th class="hidden-xs"><?= _g('Portal'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($this->view->latestUsers as $user):?>
                        <tr>
                            <td><a href="adminusers/viewuser"><?= $user->getUserProfile()->firstname.' '.$user->getUserProfile()->lastname; ?></a></td>
                            <td class="hidden-xs"><?= date('d/m/Y',strtotime($user->creationDate)); ?></td>
                            <td class="hidden-xs"><?= $user->portalName; ?></td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>