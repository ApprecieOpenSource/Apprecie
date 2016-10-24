<script src="/js/compiled/public/js/raw/controllers/itemcreation/media.min.js"></script>
<script src="/js/compiled/public/js/raw/library/ui.min.js"></script>
<script src="/js/compiled/public/js/raw/library/fileUpload.min.js"></script>
<script>
    var itemId = <?= $this->view->item->getItemId(); ?>;

    $(document).ready(function () {

        var imageError = $('#banner-error');

        $('#banner').change(function () {

            loader(true);
            imageError.fadeOut();

            var profileImageUpload = new FileUpload();
            profileImageUpload.setFileInput($(this));
            profileImageUpload.validateFile();
            profileImageUpload.validateImageType();

            if (profileImageUpload.errors.length > 0) {
                imageError.html(profileImageUpload.getErrorHTML());
                imageError.fadeIn();
                loader(false);
            } else {
                $('#banner-form').submit();
            }
        });

        $('#banner-iframe').load(function () {

            imageError.fadeOut();

            if ($('#banner-iframe').contents().text() != '') {
                var result = $.parseJSON($('#banner-iframe').contents().text());
                if (result.status == 'success') {
                    d = new Date();
                    $('#banner-img').attr('src', result.url + '?' + d.getTime());
                }
                else {
                    imageError.html(result.message);
                    imageError.fadeIn();
                }
            }

            loader(false);
        });
    });
</script>
<?php $this->partial("partials/jparts/overlay"); ?>
<style>
    #sortable { list-style-type: none; margin: 0; padding: 0; margin-left: 10px;}
    #sortable li { margin: 3px 3px 3px 0; padding: 1px; float: left; width: 125px; height: 90px; font-size: 4em; text-align: center; }
    .ui-state-highlight{
        background-color: lightgray; border: 1px solid #808080;
    }
    .image-container{
        position: relative;;
    }
    .image-container-hover{
        position: absolute;
        height:100%;
        width:100%;
        display:none;
        background-color: rgba(0, 0, 0, 0.48);
        top:0;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <h2><?= $this->view->item->getTitle(); ?></h2>
    </div>
</div>
<a href="/mycontent/eventmanagement/<?=$this->view->item->getEvent()->getEventId(); ?>" class="btn btn-default">Back to Event Management</a>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Item Banner'); ?></h2>
    </div>
    <div class="col-sm-12">
        <div class="ibox float-e-margins" style="position: relative; margin-bottom: 0px;">
            <div class="ibox-content">
                <p>Must be JPG 1170 x 350 or greater</p>
                <div class="alert alert-danger" id="banner-error" style="display: none;" role="alert"></div>
                <form method="post" enctype="multipart/form-data" action="/itemcreation/uploadbanner/<?=$this->view->item->getItemId(); ?>" id="banner-form" name="banner-form" target="banner-iframe">
                    <img src="<?= Assets::getItemBannerImage($this->view->item->getItemId()); ?>" style=" margin-bottom: 15px; margin-top: 15px;" id="banner-img" class="img-responsive">
                    {{csrf()}}
                    <input type="file" id="banner" name="banner"/>
                    <iframe id="banner-iframe" name="banner-iframe" style="width: 100%; height: 1px; display: none;"></iframe>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Item Images'); ?></h2>
    </div>
    <div class="col-sm-6">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-content">
                <div id="media-carousel" class="carousel slide" data-ride="carousel">
                    <!-- Wrapper for slides -->
                    <div class="carousel-inner" role="listbox">
                        <?php if($this->view->item->getItemMedia()->count()==0): ?>
                            <div class="item active">
                                <img src="/img/no-item-image.jpg" alt="..." class="img-responsive">
                            </div>
                        <?php else: ?>
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
                        <?php endif; ?>
            </div>
            <div class="row hidden-xs">
                <?php $loop=0; ?>
                <?php foreach($this->view->itemMedia as $media): ?>
                    <?php if($media->getType()=='image'): ?>
                        <div class="col-sm-3">
                            <img src="<?= $media->getSrc(); ?>" data-target="#media-carousel" data-slide-to="<?= $loop; ?>" class="img-responsive" style="margin-top: 5px; cursor: pointer;">
                        </div>                            <?php endif; ?>
                    <?php if($media->getType()=='youtube' or $media->getType()=='vimeo'): ?>
                        <div class="col-sm-3">
                            <img src="<?= $media->getThumbnail(); ?>" data-target="#media-carousel" data-slide-to="<?= $loop; ?>" class="img-responsive" style="margin-top: 5px; cursor: pointer;">
                        </div>
                    <?php endif; ?>

                    <?php $loop++; ?>
                <?php endforeach; ?>
            </div>
            </div>
        </div>
    </div>
    </div>
    <div class="col-sm-6">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5><?= _g('Media Upload & Order'); ?></h5>
            </div>
            <div class="ibox-content">
                <div class="alert alert-success" style="display: none;" id="order-update-message" role="alert">
                    The order of your media has been updated, please refresh to update the preview.
                </div>
                <?php if ($this->view->itemMedia->count() < 8): ?>
                    <a class="btn btn-default" style=" margin-bottom: 15px;" data-toggle="modal" data-target="#upload-image">Upload Image</a>
                    <a class="btn btn-default" style=" margin-bottom: 15px;" data-toggle="modal" data-target="#upload-video">Upload Video</a>
                <?php endif; ?>
                <p>Please drag and drop the media into the order that you would prefer. The first piece of media will be used as the thumbnail for this item.</p>
                <p><?= _g('Note: You may only upload a maximum of 8 images or videos per Item. If you have reached your maximum, in order to upload another, you must remove one or more of your existing media items first.'); ?></p>
                <div class="row">
                    <ul id="sortable" style="list-style: none;">
                    <?php foreach($this->view->itemMedia as $media): ?>
                        <?php if($media->getType()=='image'): ?>
                        <li class="ui-state-default">
                            <div class="image-container">
                            <img src="<?= $media->getSrc(); ?>" id="<?=$media->getMediaId(); ?>" class="img-responsive sortable-img" alt="...">
                                <div class="image-container-hover">
                                    <div title="Delete Media" style="margin: 10px;float:left; cursor: pointer;" onclick="deleteMedia(<?= $media->getMediaId(); ?>)">
                                        <i class="fa fa-trash" style="color: white; font-size: 16px;"></i>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php endif; ?>
                        <?php if($media->getType()=='youtube' or $media->getType()=='vimeo'): ?>
                            <li class="ui-state-default">
                                <div class="image-container">
                                    <img src="<?= $media->getThumbnail(); ?>" id="<?=$media->getMediaId(); ?>" class="img-responsive sortable-img" alt="...">
                                    <div class="image-container-hover">
                                        <div title="Delete Media" style="margin: 10px;float:left; cursor: pointer;" onclick="deleteMedia(<?= $media->getMediaId(); ?>)">
                                            <i class="fa fa-trash" style="color: white; font-size: 16px;"></i>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="upload-image" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Upload Image</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="picture-error" style="display: none;" role="alert">

                </div>
                <p>Please select an image from your computer:</p>
                <p>
                    Images must be 877x493 or larger, your image will be resized to fit.
                </p>
                <form method="post" enctype="multipart/form-data" action="/itemcreation/uploadimage/<?= $this->view->item->getItemId(); ?>" id="picture-form" name="picture-form" target="picture-iframe">
                    {{csrf()}}
                    <input type="file" id="image-file" name="image-file"/>
                    <iframe id="picture-iframe" name="picture-iframe" style="width: 100%; display: none;"></iframe>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="upload-image-button" disabled>Upload</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="upload-video" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Link Video</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <select id="video-type" name="video-type" class="form-control">
                            <option value="none" disabled selected>Please select</option>
                            <option value="youtube">Youtube</option>
                            <option value="vimeo">Vimeo</option>
                        </select>
                    </div>
                    <div class="alert alert-danger" id="video-error" style="display: none;" role="alert">

                    </div>
                    <div class="alert alert-success" id="video-success" style="display: none;" role="alert">
                        Your video was validated
                    </div>
                    <div id="youtube-link-container" style="display:none;">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1">https://www.youtube.com/watch?v=</span>
                                <input type="text" id="youtube-video-id" class="form-control" placeholder="Video ID" aria-describedby="basic-addon1">
                              <span class="input-group-btn">
                                    <a class="btn btn-default" onclick="getVideo()" id="validate-video">Validate</a>
                              </span>
                            </div>
                        </div>
                        <iframe width="560" height="315" id="youtube-link-iframe" style="display:none;" src="" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <div id="vimeo-link-container" style="display:none;">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1">https://www.vimeo.com/</span>
                                <input type="text" id="vimeo-video-id" class="form-control" placeholder="Video ID" aria-describedby="basic-addon1">
                              <span class="input-group-btn">
                                    <a class="btn btn-default" onclick="getVideo()" id="validate-video">Validate</a>
                              </span>
                            </div>
                        </div>
                        <iframe width="560" height="315" id="vimeo-link-iframe" style="display:none;" src="" frameborder="0" allowfullscreen></iframe>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" disabled id="upload-video-button" onclick="AddVideo()">Add</button>
            </div>
        </div>
    </div>
</div>
