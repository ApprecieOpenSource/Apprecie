/**
 * Only returns the brands that match items in the current users vault
 */
function getVaultBrands(){
    this.ajax= function (){
        return $.ajax({
            url: "/api/AjaxGetVaultEventBrands/",
            type: 'get',
            dataType: 'json'
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}