/**
 * Created by Daniel on 08/12/14.
 */
var apiKey='DP67-TX78-UW19-AJ18';

function findByPostcode(postcode){
    var url='/callback/getAddresses/'+postcode;
    return $.ajax({
        type: 'GET',
        url: url,
        async: false,
        contentType: "application/json",
        dataType: 'json'
    });
}