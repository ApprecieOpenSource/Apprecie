<script>
    $(document).ready(function(){
        $('.carousel').carousel({
            interval: false
        })
    })
</script>
<style>
    .video-thumb{
        position: absolute;
        top: 10px;
        left: 25px;
        font-size: 15px;
        color: white;
    }
</style>
<?php if($this->view->item->getItemMedia()->count()==0): ?>
    <img src="/img/no-item-image.jpg" class="img-responsive"/>
<?php else: ?>
    <div id="media-carousel" class="carousel slide" data-ride="carousel">
    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox">
        <?php $loop=0; ?>
        <?php foreach($this->view->itemMedia as $media): ?>
        <?php if($loop==0): ?>
        <div class="item active">
            <?php else: ?>
            <div class="item">
                <?php endif; ?>
                <?php if($media->getType()=='image'): ?>
                    <img src="<?= $media->getSrc(); ?>" alt="..." class="img-responsive">
                <?php endif; ?>
                <?php if($media->getType()=='youtube'): ?>
                    <div class="videowrapper">
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/<?= $media->getSrc(); ?>" frameborder="0" allowfullscreen></iframe>
                    </div>
                <?php endif; ?>
                <?php if($media->getType()=='vimeo'): ?>
                    <div class="videowrapper">
                        <iframe width="560" height="315" src="https://player.vimeo.com/video/<?= $media->getSrc(); ?>" frameborder="0" allowfullscreen></iframe>
                    </div>
                <?php endif; ?>
            </div>
            <?php $loop++; ?>
            <?php endforeach; ?>
        </div>
    </div>
        <?php if(count($this->view->itemMedia)>1): ?>
        <div class="row hidden-xs">
        <?php $loop=0; ?>
        <?php foreach($this->view->itemMedia as $media): ?>
            <?php if($media->getType()=='image'): ?>
                <div class="col-sm-3">
                    <img src="<?= $media->getSrc(); ?>" data-target="#media-carousel" data-slide-to="<?= $loop; ?>" class="img-responsive" style="margin-top: 5px; cursor: pointer;">
                </div>                            <?php endif; ?>
            <?php if($media->getType()=='youtube' or $media->getType()=='vimeo'): ?>
                <div class="col-sm-3">
                    <div class="video-thumb" data-target="#media-carousel" data-slide-to="<?= $loop; ?>" style="cursor: pointer;">
                        <i class="fa fa-video-camera"></i>
                    </div>
                    <img src="<?= $media->getThumbnail(); ?>" data-target="#media-carousel" data-slide-to="<?= $loop; ?>" class="img-responsive" style="margin-top: 5px; cursor: pointer;">
                </div>
            <?php endif; ?>

            <?php $loop++; ?>
        <?php endforeach; ?>
    </div>
        <?php endif; ?>
<?php endif; ?>