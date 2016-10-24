<script>
    $(document).ready(function () {
        initialiseAddressFinder('address-search-form', 'last-id', 'search-term', 'address-table', 'address-id', 'selected-address', 'selected-address-value', 'address-lookup-table');
        $('input[name=addressType]').change(function(){
            var addressType=$("input:radio[name ='addressType']:checked").val();

            if(addressType=='finder'){
                $('#manual-address').hide();
                $('#country').prop('disabled',false);
                $('#search-term').prop('disabled',false);
                $('#searchBtn').prop('disabled',false);
            }
            else{
                $('#manual-address').show();
                $('#country').prop('disabled',true);
                $('#search-term').prop('disabled',true).val('');
                $('#searchBtn').prop('disabled',true);
                $('#selected-address-value').empty();
                $('#selected-address').hide();
                $('#address-results').hide();
                $('#last-id').val('');
                $('#address-id').val('');
            }
        })
    })
</script>
<style>
    #address-table, #selected-address{
        display:none;
    }
    .control-label{
        font-weight: normal;
    }
</style>
<div class="alert alert-danger" role="alert" id="address-error" style="display:none;"></div>
<div id="finder-address">
    <div class="form-group">
        <label for="country" class="col-sm-3 control-label">
            <?php if (isset($this->view->params['showFieldMarkings']) && $this->view->params['showFieldMarkings']): ?>
                *&nbsp;
            <?php endif; ?>
            <?= _g('Country'); ?>
        </label>
        <div class="col-sm-9">
            <select id="country" class="form-control full-width">
                <option value="GBR" selected>United Kingdom</option>
                <option value="USA">United States</option>
                <option value="ALB">Albania</option>
                <option value="AND">Andorra</option>
                <option value="ARM">Armenia</option>
                <option value="AUT">Austria</option>
                <option value="AZE">Azerbaijan</option>
                <option value="BLR">Belarus</option>
                <option value="BIH">Bosnia and Herzegovina</option>
                <option value="BGR">Bulgaria</option>
                <option value="HRV">Croatia</option>
                <option value="CYP">Cyprus</option>
                <option value="CZE">Czech Republic</option>
                <option value="DNK">Denmark</option>
                <option value="EST">Estonia</option>
                <option value="FIN">Finland</option>
                <option value="FRA">France</option>
                <option value="GEO">Georgia</option>
                <option value="DEU">Germany</option>
                <option value="GRC">Greece</option>
                <option value="HUN">Hungary</option>
                <option value="ISL">Iceland</option>
                <option value="IRL">Ireland</option>
                <option value="ITA">Italy</option>
                <option value="KAZ">Kazakhstan</option>
                <option value="LIE">Liechtenstein</option>
                <option value="LTU">Lithuania</option>
                <option value="LUX">Luxembourg</option>
                <option value="MKD">Macedonia</option>
                <option value="MLT">Malta</option>
                <option value="MDA">Moldova</option>
                <option value="MCO">Monaco</option>
                <option value="MNE">Montenegro</option>
                <option value="NLD">Netherlands</option>
                <option value="NOR">Norway</option>
                <option value="POL">Poland</option>
                <option value="PRT">Portugal</option>
                <option value="ROU">Romania</option>
                <option value="RUS">Russian Federation</option>
                <option value="SMR">San Marino</option>
                <option value="SRB">Serbia</option>
                <option value="SVK">Slovakia</option>
                <option value="SVN">Slovenia</option>
                <option value="ESP">Spain</option>
                <option value="SWE">Sweden</option>
                <option value="CHE">Switzerland</option>
                <option value="TUR">Turkey</option>
                <option value="UKR">Ukraine</option>
                <option value="GBR">United Kingdom</option>
                <option value="VAT">Holy See (Vatican City State)</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="country" class="col-sm-3 control-label">
            <?php if (isset($this->view->params['showFieldMarkings']) && $this->view->params['showFieldMarkings']): ?>
                *&nbsp;
            <?php endif; ?>
            <?= _g('Address'); ?>
        </label>
        <div class=" col-sm-9">
            <div class="input-group">
                <input type="text" id="search-term" placeholder="Address" class="form-control" />
                <div class="input-group-btn">
                    <input type="button" class="btn btn-primary" id="searchBtn" onclick="LookUp(1);" value="Search"/>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="selected-address" class="form-group">
    <label for="selected-address-value" class="col-sm-3 control-label"><?= _g('Selected'); ?></label>
    <div class="col-sm-9" id="selected-address-value"></div>
</div>
<input type="hidden" id="last-id">
<input type="hidden" id="address-id" name="address-id">
<div style="height:200px; display:none; overflow-y: auto; clear:both;" id="address-results">
    <table class="table table-hover" id="address-table">
        <thead>
        <th><?= _g('Search Results'); ?></th>
        <th></th>
        </thead>
        <tbody id="address-lookup-table">

        </tbody>
    </table>
</div>
