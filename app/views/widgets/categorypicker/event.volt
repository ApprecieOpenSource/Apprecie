<style>
    .interest-picker-selectable,.interest-picker-selected{
        padding: 5px;
        background-color: #eeeeee;
        margin-bottom: 2px;
        cursor: pointer;
    }
    .interest-picker-selectable:hover, .interest-picker-selected:hover{
        background-color: #428bca;
        color:white;
    }
    .interest-picker-plus{
        float:right;
        margin-top: 2px;
        display: none;
    }
    #interest-picker-sub-interest{
        display:none;
    }
</style>
<script>
    var selectedArray=[];
    var availableInterests=null;
    $(document).ready(function(){
        RebindIcons();
        $('#interest-picker-sub-interest').fadeIn();
        $('#interest-picker-main-interest').change(function(){
            loader(true);
            var available=$('#interest-picker-available');
            available.css('opacity','0.5');
            if($(this).val()!='none'){
                $.when(getInterestPickerSubCategories($(this).val())).then(function(data){
                    availableInterests=data;
                    available.empty();
                    $.each(data,function(key, value){
                        available.append('<div class="interest-picker-selectable" onclick="addInterest('+key+');">'+value.interest+'<span class="interest-picker-plus"><i class="fa fa-plus"></i></span></div>');
                    })
                    RebindIcons();
                    available.css('opacity','1');
                    loader(false);
                });
            }
        })
    })

    function RebindIcons(){
        $('.interest-picker-selectable').hover(function(){
            $(this).find('.interest-picker-plus').show();
        },function(){
            $(this).find('.interest-picker-plus').hide();
        });
    }

    function addInterest(key){
        var selected=$('#interest-picker-selected');
        var interest=availableInterests[key];
        if($.inArray(interest.interestId,selectedArray)==-1){
            selectedArray.push(interest.interestId);
            selected.append('<div class="interest-picker-selected" id="selected-interest-'+interest.interestId+'" onclick="removeInterest('+interest.interestId+');">'+interest.interest+'<span class="interest-picker-plus"><i class="fa fa-minus"></i></span></div>');
            $('.interest-picker-selected').hover(function(){
                $(this).find('.interest-picker-plus').show();
            },function(){
                $(this).find('.interest-picker-plus').hide();
            });
        }
        updateInterestHidden();
    }

    function removeInterest(interestId){
        $('#selected-interest-'+interestId).remove();
        selectedArray = $.grep(selectedArray, function(value) {
            return value != interestId;
        });
        updateInterestHidden();
    }

    function getInterestPickerSubCategories(parentId){
        return $.ajax({
            url: "/callback/categorypicker/"+parentId,
            type: 'post',
            dataType: 'json',
            data : {'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN}
        });
    }

    function updateInterestHidden(){
        $('#hidden-interests').empty();
        $.each(selectedArray,function(key, value){
            $('#hidden-interests').append('<input type="hidden" id="interests" value="'+value+'" name="interests[]"/>');
        })
    }
</script>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <select class="form-control full-width" id="interest-picker-main-interest">
                <option value="none" disabled selected>Please select a primary interest</option>
                <?php foreach($this->view->toplevel as $interest):?>
                    <option value="<?= $interest->getInterestId(); ?>"><?= $interest->getInterest(); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>
<div id="interest-picker-sub-interest">
    <div class="row">
        <div class="col-sm-6">
            <h4>Available Interests</h4>
            <div id="interest-picker-available">

            </div>
        </div>
        <div class="col-sm-6">
            <h4>Selected Interests</h4>
            <div id="interest-picker-selected">
                <?php foreach($this->view->interests as $interest):?>
                    <script>selectedArray.push("<?= $interest->getInterestId(); ?>"); </script>
                    <div class="interest-picker-selected" id="selected-interest-<?= $interest->getInterestId(); ?>" onclick="removeInterest(<?= $interest->getInterestId(); ?>);">
                        <?= $interest->getInterest(); ?><span class="interest-picker-plus"><i class="fa fa-minus"></i></span></div>
                <?php endforeach; ?>
            </div>
        </div>
        <div id="hidden-interests">
            <?php foreach($this->view->interests as $interest):?>
                <input type="hidden" id="interests" value="<?= $interest->getInterestId(); ?>" name="interests[]"/>
            <?php endforeach; ?>
        </div>
    </div>
</div>