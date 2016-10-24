<div class="ibox float-e-margins" style="position: relative;">
    <div class="ibox-title">
        <h5>Rewards</h5>
    </div>
    <div class="ibox-content no-padding">
        <?php foreach($this->view->items as $item):
            $event=Event::findFirstBy('itemId',$item->getItemId());
            $creator=User::findFirstBy('userId',$event->getCreatorId());

            $organisation=$creator->getOrganisation()->getOrganisationName();
            ?>
            <div class="media" style="background-color: white; padding: 10px;">
                <div class="media-left">
                    <a href="/vault/event/<?= $event->getItemId(); ?>">
                        <img src="<?= Assets::getItemPrimaryImage($event->getItemId()); ?>" style="max-width: 150px;" class="img-responsive"/>
                    </a>
                </div>
                <div class="media-body">
                    <h4 class="media-heading"><a href="/vault/event/<?= $event->getItemId(); ?>"><?= $event->getTitle(); ?></a> <span class="pull-right"><?= $organisation; ?></span></h4>
                    <?= date('d-m-Y H:i:s',strtotime($event->getStartDateTime())); ?>
                    <p style="margin-top: 5px;"><?= $event->getSummary(); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if(count($this->view->items)==0): ?>
            <div class="alert alert-info" id="upload-error" role="alert" style="margin: 10px;">There are currently no rewards available</div>
        <?php endif; ?>
    </div>
</div>
