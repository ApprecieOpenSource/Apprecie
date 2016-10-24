<div class="modal fade" id="videoFinder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h2 class="modal-title">Add Video</h2></div>
            <div class="modal-body">
                <div>
                    <div class="row" id="media-add-video-container" style="margin-top: 15px;">
                        <div class="col-sm-12">
                            <p>Please select the provider and enter the video id for the video you would like to add to this gallery.</p>
                            <div class="videoWrapper" style="background-image: url('/img/temp/noimage.png'); margin-bottom: 15px; background-size: contain; background-repeat: no-repeat; background-position: center center;"></div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Provider</label>
                                <div class="col-sm-9">
                                    <select class="form-control" style="max-width: 250px;">
                                        <option>Vimeo</option>
                                        <option>YouTube</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Video ID</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="text" placeholder="Vimeo Video ID e.g. 12345678" class="form-control">
                                          <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" data-loading-text="Searching...">
                                                Find
                                            </button>
                                          </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div>
                    <button class="btn btn-primary disabled">Add</button>
                </div>
            </div>
        </div>
    </div>
</div>