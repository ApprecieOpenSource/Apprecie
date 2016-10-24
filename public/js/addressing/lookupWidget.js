/**
 * Created by Daniel on 14/12/14.
 */
var addressSearchForm = '';
var lastIdField = '';
var searchTermField = '';
var addressTable = '';
var addressIdField = '';
var selectedAddressContainer = '';
var selectedAddressValueContainer = '';
var addressLookupTableBody = '';

function initialiseAddressFinder(addressSearchFormId, lastAddressId, searchTermId, addressTableId, addressIdId, selectedAddressId, selectedAddressValueId, addressLookupTableBodyId) {
    addressSearchForm = $('#' + addressSearchFormId);
    lastIdField = $('#' + lastAddressId);
    searchTermField = $('#' + searchTermId);
    addressTable = $('#' + addressTableId);
    addressIdField = $('#' + addressIdId);
    selectedAddressContainer = $('#' + selectedAddressId);
    selectedAddressValueContainer = $('#' + selectedAddressValueId);
    addressLookupTableBody = $('#' + addressLookupTableBodyId);

    addressSearchForm.submit(function (event) {
        event.preventDefault();
        LookUp(1);
    });
}

function FindNextAddresses(lastId, text) {
    lastIdField.val(lastId);
    searchTermField.val(text);
    LookUp(0);
}

function RetrieveAddress(Id, Text) {
    $('#address-results').hide();
    addressTable.stop().fadeOut('fast', function () {
        addressIdField.val(Id);
        selectedAddressContainer.fadeIn('fast');
        selectedAddressValueContainer.html(Text);
    })
}

function LookUp(clear) {
    loader(true);
    $('#address-error').hide();
    addressTable.stop().fadeOut('fast');
    selectedAddressContainer.fadeOut('fast');
    selectedAddressValueContainer.empty();

    if (clear == 1) {
        lastIdField.val('');
    }
    $.getJSON("https://services.postcodeanywhere.co.uk/CapturePlus/Interactive/Find/v2.10/json3.ws?callback=?",
        {
            Key: 'DP67-TX78-UW19-AJ18',
            SearchTerm: searchTermField.val(),
            LastId: lastIdField.val(),
            SearchFor: 'Everything',
            Country: $('#country').val(),
            LanguagePreference: 'EN',
            MaxSuggestions: '20',
            MaxResults: '300'
        },
        function (data) {
            // Test for an error
            if (data.Items.length == 1 && typeof(data.Items[0].Error) != "undefined") {
                // Show the error message
                $('#address-error').html('No results found').fadeIn('fast');
                loader(false);
            }
            else {
                // Check if there were any items found
                if (data.Items.length == 0){
                    $('#address-error').html('No results found').fadeIn('fast');
                    loader(false);
                }
            else {
                    addressLookupTableBody.empty();
                    $(data.Items).each(function (key, value) {
                        var res = value.Text.replace(/'/g, "\\&apos;");
                        if (value.Next == "Find") {
                            var action = 'FindNextAddresses(\'' + value.Id + '\',\'' + res + '\')';
                        }
                        else {
                            var action = 'RetrieveAddress(\'' + value.Id + '\',\'' + res + '\')';
                        }
                        var row =
                            '<tr style="cursor: pointer;" onclick="' + action + '">' +
                                '<td>' + value.Text + '</td>' +
                                '<td>' + value.Description + '</td>'+
                        '</tr>';
                        $('#address-results').show();
                        addressLookupTableBody.append(row);
                        addressTable.stop().fadeIn('fast');
                        loader(false);
                    })
                }
            }
        });
}