/**
 * Created by Daniel Dimmick on 24/03/15.
 */
function VAT(successbox,errorbox){
    this.vatNumber=null;
    this.message=null;

    var parent = this;

    this.getVatNumber=function(){
        return this.vatNumber;
    }
    this.setVatNumber=function(vatNumber){
        this.vatNumber=vatNumber;
    }

    this.save= function (){
        $.when(this.ajaxCall()).then(function(data){
            if(data.status=='success'){
                errorbox.stop().hide();
                successbox.find('#message').html(data.message);
                successbox.stop().fadeOut('fast').fadeIn('fast');
            }
            else{
                successbox.stop().hide();
                errorbox.find('#message').html(data.message);
                errorbox.stop().fadeOut('fast').fadeIn('fast');
            }
        })
    }

    this.ajaxCall= function(){
        return $.ajax({
            url: "/payment/vat/",
            type: 'post',
            dataType: 'json',
            data: {"vatnumber":this.getVatNumber(),'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN}
        });
    }
}
