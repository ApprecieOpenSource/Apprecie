/**
 * Only returns the top level interests that match items in the current users vault
 */
function getVaultInterests(){
    this.ajax= function (){
        return $.ajax({
            url: "/api/AjaxGetVaultEventInterests/",
            type: 'get',
            dataType: 'json'
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}