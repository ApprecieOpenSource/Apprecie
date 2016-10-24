<script src="/js/compiled/public/js/raw/controllers/vault/arrange.min.js"></script>
<script src="/js/compiled/public/js/raw/library/vault.min.js"></script>
<script src="/js/compiled/public/js/raw/library/modal.min.js"></script>
<script src="/js/addressing/lookupWidget.js"></script>
<script type="text/javascript" src="/js/tinymce/tinymce.min.js"></script>
<script>var itemId=<?= $this->view->event->getItemId(); ?>;</script>
<script>
    $(document).ready(function(){
        tinymce.init({
            menubar: "format insert edit",
            plugins: 'link',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
            selector: '#request-notes'
        });

        $('#confirmed-starttime').clockpicker({
            placement: 'bottom',
            align: 'left',
            autoclose: true,
            'default': ''
        });
        $('#confirmed-endtime').clockpicker({
            placement: 'bottom',
            align: 'left',
            autoclose: true,
            'default': ''
        });

        var picker1 = new Pikaday(
            {
                field: document.getElementById('confirmed-startdate'),
                firstDay: 1,
                format: 'DD/MM/YYYY',
                minDate: new Date(),
                onSelect: function() {
                    var date = document.createTextNode(this.getMoment().format('Do MMMM YYYY') + ' ');
                    document.getElementById('selected').appendChild(date);
                }
            });
        var picker2 = new Pikaday(
            {
                field: document.getElementById('confirmed-enddate'),
                firstDay: 1,
                format: 'DD/MM/YYYY',
                minDate: new Date(),
                onSelect: function() {
                    var date = document.createTextNode(this.getMoment().format('Do MMMM YYYY') + ' ');
                    document.getElementById('selected').appendChild(date);
                }
            });
        $('#request-btn').click(function(){
            tinyMCE.triggerSave();
            if(! validate()){
                var errorString='';
                $(errors).each(function(key,value){
                    errorString=errorString+value+'<br/>';
                })

                $('#issues').html(errorString);
                $('#issues').show();
            }
            else{
                validate();
                var btn=$(this);
                $('#issues').hide();
                btn.prop('disabled',true);
                var arrangement = new ArrangeItem(itemId,$('#request-form').serialize());

                $.when(arrangement.fetch()).then(function(data){
                    console.log(data);
                    btn.prop('disabled',false);

                    if(data.status == 'failed') {
                        $('#issues').toggle();
                        $('#issues').html(data.message);
                    } else {
                        window.location = '/vault/myarranged/'+ data.itemId;
                    }
                })
            }
        })
    })

</script>
<div class="row">
    <div class="col-sm-12">
        <h2>Arrangement For: <?= $this->view->event->getTitle(); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div id="issues" class="alert alert-danger" style="display: none;"></div>
    </div>
</div>
<form class="form-horizontal" id="request-form" name="request-form">
    {{csrf()}}
<div class="row">
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Please suggest a venue for this event</h5>
            </div>
            <div class="ibox-content">
                <?php if($this->view->event->getAddressId()== NULL): ?>
                <div class="form-group">
                    {{ widget('AddressFinderWidget','index') }}
                </div>
                <?php else: $address=$this->view->event->getAddress();?>
                <?php if($address->getLine1()!=null){ echo $address->getLine1().',<br/>';} ?>
                <?php if($address->getLine2()!=null){ echo $address->getLine2().',<br/>';} ?>
                <?php if($address->getLine3()!=null){ echo $address->getLine3().',<br/>';} ?>
                <?php if($address->getCity()!=null){ echo $address->getCity().',<br/>';} ?>
                <?php if($address->getPostalCode()!=null){ echo $address->getPostalCode().',<br/>';} ?>
                <?php if($address->getCountryName()!=null){ echo $address->getCountryName();} ?>
                    <input type="hidden" id="address-id" name="address-id" value="<?= $address->getAddressId(); ?>"/>
                <?php endif; ?>
            </div>
            <div class="ibox-title">
                <h5>What dates would you like the event to be held?</h5>
            </div>
            <div class="ibox-content">
                <?php if($this->view->event->getStartDateTime()==null): ?>
                    <div class="form-group">
                        <label for="title" class="col-sm-4 control-label">Start Date & Time<br/>(24 hour format)</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" id="confirmed-startdate" name="confirmed-startdate" value="" class="form-control" style="max-width: 120px; display: inline" placeholder="DD/MM/YYYY">
                                <input type="text" value="" id="confirmed-starttime" name="confirmed-starttime" class="form-control" style="max-width: 120px; display: inline" placeholder="HH:MM">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="col-sm-4 control-label">End Date & Time<br/>(24 hour format)</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" id="confirmed-enddate" name="confirmed-enddate" value="" class="form-control" style="max-width: 120px; display: inline" placeholder="DD/MM/YYYY">
                                <input type="text" id="confirmed-endtime" name="confirmed-endtime" value="" class="form-control" style="max-width: 120px; display: inline" placeholder="HH:MM">
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label for="title" class="col-sm-4 control-label">Start Date & Time</label>
                        <div class="col-sm-8" style="padding-top: 7px;">
                            <?= date('d-m-Y H:i:s',strtotime($this->view->event->getStartDateTime())); ?>
                        </div>
                        <input type="hidden" id="confirmed-startdate" name="confirmed-startdate" value="<?= date('d-m-Y',strtotime($this->view->event->getStartDatetime())); ?>"/>
                        <input type="hidden" id="confirmed-starttime" name="confirmed-starttime" value="<?= date('H:i',strtotime($this->view->event->getStartDatetime())); ?>"/>

                    </div>
                    <div class="form-group">
                        <label for="title" class="col-sm-4 control-label">End Date & Time</label>
                        <div class="col-sm-8" style="padding-top: 7px;">
                            <?= date('d-m-Y H:i:s',strtotime($this->view->event->getEndDateTime())); ?>
                        </div>
                        <input type="hidden" id="confirmed-enddate" name="confirmed-enddate" value="<?= date('d-m-Y',strtotime($this->view->event->getEndDatetime())); ?>"/>
                        <input type="hidden" id="confirmed-endtime" name="confirmed-endtime" value="<?= date('H:i',strtotime($this->view->event->getEndDatetime())); ?>"/>
                    </div>
                <?php endif; ?>
            </div>

                <?php if($this->view->event->getPackageSize()==null): ?>
                <div class="ibox-title">
                    <h5>How many people will be attending the event?</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label for="title" class="col-sm-4 control-label">Number of attendees</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" value="" name="package-size" id="package-size" class="form-control">
                            </div>
                        </div>
                        <input type="hidden" id="number-packages" name="number-packages" value="1"/>
                    </div>
                </div>
                <?php else: ?>
                <div class="ibox-title">
                    <h5>How many packages would you like?</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Package Size</label>
                        <div class="col-sm-8" style="padding-top: 7px;">
                            <?= $this->view->event->getPackageSize(); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="col-sm-4 control-label">Number of packages</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" value="" name="number-packages" id="number-packages" class="form-control">
                                <input type="hidden" value="<?= $this->view->event->getPackageSize(); ?>" name="package-size" id="package-size">
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Additional Information</h5>
            </div>
            <div class="ibox-content">
                <textarea class="form-control" id="request-notes" name="request-notes" style="height:350px;"></textarea>
            </div>
        </div>
    </div>
</div>
</form>
<div class="row">
    <div class="col-sm-12">
        <a href="/vault/arranged/{{event.getItemId()}}" class="btn btn-default">Cancel</a>
        <button class="btn btn-primary pull-right" style="margin-bottom:15px;" id="request-btn" name="reguest-btn">Submit Request</button>
    </div>
</div>