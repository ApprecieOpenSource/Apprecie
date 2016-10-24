<script src="/js/validation/errors.js"></script>
<script>
    var steps=0;
    var stepper=0;
    function setSteps(numberOfSteps){
        steps=numberOfSteps;
        stepper=(100/steps);
    }
    function setStep(stepID){
        if(typeof(tinyMCE) != "undefined")
        {
            tinyMCE.triggerSave();
        }

        if(stepper*stepID==100){
            $('.progress-bar').addClass('progress-bar-complete');
        }
        else{
            $('.progress-bar').removeClass('progress-bar-complete');
        }
        $('.progress-bar').css('width',(stepper*stepID)+'%');
        $('.step').hide();
        $('#step-'+stepID).show();
    }

    function nextStep(stepID){
        if(errors.length==0){
            setStep(stepID+1);
            clearErrors();
        }
        else{
            displayErrors();
        }
    }
    $(document).ready(function(){
        $("#item-creation-form").submit(function(e){
            e.preventDefault();
        });
        setStep(1);
        $('#get-steps-btn').click(function () {
            clearErrors();
            var itemType=$('#item-type').val();
            if(itemType=='none'){
                errors.push('You must select an item type');
                displayErrors();
            }
            else{
                var btn=$(this);
                btn.html('<img style="height: 20px;margin-right: 5px;" src="/img/ajax-loader.gif"/> Please wait').attr('disabled',true).addClass('btn-loading');
                $.ajax({
                    method: 'POST',
                    url: "/itemcreation/getCreateSteps",
                    data: {'type': $('#item-type').val(), 'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN}
                }).done(function(data) {
                        $('#steps').html(data);
                        btn.html('Next').attr('disabled',false).removeClass('btn-loading');
                        setStep(2);
                    });
            }

        });
    });

    function previewEvent(){
        var form=$('#item-creation-form');
        form.unbind('submit').submit();
        form.attr('target','_blank').attr('method','post').attr('action','/itemcreation/previewevent');
        form.submit();
        form.removeAttr('target').removeAttr('method').removeAttr('action');
    }
</script>
<style>
    .btn-loading{
        background-color: black;
        border-color: black;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <h2 id="processTitle">Vault Item Wizard</h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="progress">
            <div class="progress-bar" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width: 1%;">
            </div>
        </div>
    </div>
</div>
<form id="item-creation-form"  autocomplete="off" name="item-creation-form" class="form-horizontal">
    {{csrf()}}
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-danger" role="alert" id="error-box" style="display: none;"></div>
        </div>
    </div>
<div class="row step" id="step-1">
    <div class="col-sm-8">
        <div  class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>1. <?= _g('Vault Item Type'); ?></h5>
            </div>
            <div class="ibox-content">
                <p><?= _g('Please select the type of Vault Item you would like to create'); ?></p>
                <div class="form-group">
                    <select class="form-control" name="item-type" id="item-type">
                        <option value="none"><?= _g('Please select...'); ?></option>
                        <option value="confirmed"><?= _g('Confirmed Event'); ?></option>
                        <option value="arranged"><?= _g('By Arrangement'); ?></option>
                    </select>
                </div>
                <p><?= _g('Please select the language you would like to create the event in. You can add additional languages for this event once it has been created.'); ?></p>
                <div class="form-group">
                    {{ widget('LanguageWidget','index') }}
                </div>
            </div>
            <div class="panel-footer" style="height:55px;">
                <button type="button" id="get-steps-btn" data-loading-text="Loading..." class="btn btn-primary pull-right">
                    <?= _g('Next'); ?>
                </button>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5>Help</h5>
            </div>
            <div class="ibox-content">
                <p><?= _g("Welcome to the Item Creation wizard. This process is designed to guide you step-by-step through the creation of a single Vault Item for you to then keep in draft or publish out to other users as you wish."); ?></p>
                <p><?= _g("PLEASE NOTE: Items are not saved until the end of the process, so if you leave or close the wizard before finishing, you will need to begin again."); ?></p>
                <p><?= _g("On this initial page you are required to choose the type of Vault Item you want to create."); ?></p>
                <p><?= _g("Confirmed Event: A Confirmed Event is a standard event where all the details are defined, and no interaction with the host is required. Users can reserve and purchase the item directly."); ?></p>
                <p><?= _g("By Arrangement Event: A By Arrangement is a bespoke item tailored to the needs of the client. This means that the overall details are available to view but some, such as the date and time or the venue, have not yet been established or is available from a range of options. A client is able to view these events and contact the host (i.e. you) via internal messaging to arrange the final details of their own arrangement of the event."); ?></p>

            </div>
        </div>
    </div>
</div>
<div id="steps">

</div>
</form>