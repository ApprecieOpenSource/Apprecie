/**
 * Created by Daniel Dimmick on 01/07/15.
 */
var videoDetails = [];

$(document).ready(function () {

    var imageError = $('#picture-error');
    var imageUploadBtn = $('#upload-image-button');

    $('.carousel').carousel({
        interval: false
    });

    imageUploadBtn.click(function () {

        imageError.fadeOut();

        var profileImageUpload = new FileUpload();
        profileImageUpload.setFileInput($('#image-file'));
        profileImageUpload.validateFile();
        profileImageUpload.validateImageType();

        if (profileImageUpload.errors.length > 0) {
            imageError.html(profileImageUpload.getErrorHTML());
            imageError.fadeIn();
        } else {
            $(this).prop('disabled', true).html('<img src="/img/ajax-loader.gif"/> Uploading...');
            $('#picture-form').submit();
        }
    });

    $('#image-file').on('change', function () {
        imageError.fadeOut();

        var profileImageUpload = new FileUpload();
        profileImageUpload.setFileInput($('#image-file'));
        profileImageUpload.validateFile();
        profileImageUpload.validateImageType();

        if (profileImageUpload.errors.length > 0) {
            imageError.html(profileImageUpload.getErrorHTML());
            imageError.fadeIn();
            imageUploadBtn.prop('disabled', true);
        } else {
            imageUploadBtn.prop('disabled', false);
        }
    });

    $('#picture-iframe').load(function () {
        imageError.fadeOut();
        if ($('#picture-iframe').contents().text() != '') {
            var result = $.parseJSON($('#picture-iframe').contents().text());
            if (result.status == 'success') {
                window.location = window.location.href;
            } else {
                imageError.html(result.message);
                imageUploadBtn.prop('disabled', false).html('Upload');
                imageError.fadeIn();
            }
        }
    });

    $('.image-container').hover(function () {
        var hover = $(this).find('.image-container-hover');
        hover.show();
    }, function () {
        var hover = $(this).find('.image-container-hover');
        hover.hide();
    });

    $('#video-type').change(function () {
        $('#upload-video-button').prop('disabled', true);
        $('#video-error').stop().hide();
        $('#video-success').stop().hide();

        $('#youtube-link-container').hide();
        $('#youtube-link-iframe').attr('src', '').hide();
        $('#vimeo-link-container').hide();
        $('#vimeo-link-iframe').attr('src', '').hide();

        switch ($('#video-type').val()) {
            case "youtube":
                $('#youtube-link-container').show();
                break;
            case "vimeo":
                $('#vimeo-link-container').show();
                break;
        }
    });

    $('#youtube-video-id').on('input', function () {
        $('#upload-video-button').prop('disabled', true);
        $('#video-error').stop().hide();
        $('#video-success').stop().hide();
        $('#youtube-link-iframe').attr('src', '').hide();
    });

    $('#vimeo-video-id').on('input', function () {
        $('#upload-video-button').prop('disabled', true);
        $('#video-error').stop().hide();
        $('#video-success').stop().hide();
        $('#vimeo-link-iframe').attr('src', '').hide();
    });
});

$(function () {

    $("#sortable").sortable({
        placeholder: "ui-state-highlight"
    });

    $("#sortable").disableSelection();

    $("#sortable").on("sortupdate", function () {

        var order = [];

        $(this).find('.sortable-img').each(function () {
            order.push($(this).attr('id'));
        });

        $.when(AjaxUpdateOrder(order)).then(function () {
            $('#order-update-message').stop().fadeOut('fast').fadeIn('fast');
        })
    });
});

function deleteMedia(mediaId) {
    var overlay = new OverlayMessage('Reloading, please wait...');
    $.when(AjaxDeleteMedia(mediaId)).then(function () {
        location.reload();
    });
}

function AjaxUpdateOrder(order) {
    return $.ajax({
        url: "/itemcreation/ajaxUpdateOrder/" + itemId,
        type: 'post',
        dataType: 'json',
        cache: false,
        data: {images: order, 'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN}
    });
}
function AjaxDeleteMedia(mediaId) {
    return $.ajax({
        url: "/itemcreation/AjaxDeleteMedia/" + mediaId,
        type: 'post',
        dataType: 'json',
        data: {'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN}
    });
}

function AddVideo() {
    $('#video-error').stop().hide();
    $('#upload-video-button').prop('disabled', true);
    $.when(AjaxAddVideo()).then(function (data) {
        $('#upload-video-button').prop('disabled', false);
        location.reload();
    });
}

function AjaxAddVideo() {
    return $.ajax({
        url: "/itemcreation/addvideo/" + itemId,
        type: 'post',
        contentType: "application/x-www-form-urlencoded",
        data: videoDetails
    });
}

function getVideo() {

    videoDetails = {};
    $('#video-success').stop().hide();
    $('#video-error').stop().hide();

    $('#validate-video').prop('disabled', true);

    switch ($('#video-type').val()) {
        case "youtube":
            $.when(AjaxGetYoutubeVideo($('#youtube-video-id').val())).then(function (data) {
                if (data.items.length == 0) {
                    $('#video-error').stop().fadeOut('fast').empty().html('Invalid video ID').fadeIn('fast');
                    $('#youtube-link-iframe').attr('src', '').hide();
                }
                else {
                    $('#video-success').stop().fadeIn('fast');
                    $('#youtube-link-iframe').attr('src', 'https://www.youtube.com/embed/' + data.items[0].id).show();
                    $('#upload-video-button').prop('disabled', false);
                    videoDetails.type = 'youtube';
                    videoDetails.id = data.items[0].id;
                    if (data.items[0].snippet.thumbnails.maxres != null) {
                        thumbnail = data.items[0].snippet.thumbnails.maxres.url;

                    } else if (data.items[0].snippet.thumbnails.standard != null) {
                        thumbnail = data.items[0].snippet.thumbnails.standard.url;
                    }
                    else {
                        thumbnail = data.items[0].snippet.thumbnails.medium.url;
                    }
                    videoDetails.thumbnail = thumbnail;
                }
                $('#validate-video').prop('disabled', false);
            }, function (xhr) {
                $('#video-error').stop().fadeOut('fast').empty().html('Failed to contact Youtube').fadeIn('fast');
                $('#link-iframe').attr('src', '').hide();
            });
            break;
        case "vimeo":
            $.when(AjaxGetVimeoVideo($('#vimeo-video-id').val())).then(function (data) {
                $('#video-success').stop().fadeIn('fast');
                $('#vimeo-link-iframe').attr('src', 'https://player.vimeo.com/video/' + data.video_id).show();
                $('#upload-video-button').prop('disabled', false);
                $('#validate-video').prop('disabled', false);
                videoDetails.type = 'vimeo';
                videoDetails.id = data.video_id;
                videoDetails.thumbnail = data.thumbnail_url;
            }, function () {
                $('#video-error').stop().fadeOut('fast').empty().html('Failed to contact Vimeo').fadeIn('fast');
                $('#link-iframe').attr('src', '').hide();
            })
            break;
    }
}

function AjaxGetYoutubeVideo(videoId) {
    return $.ajax({
        url: "https://www.googleapis.com/youtube/v3/videos?part=snippet&id=" + videoId + "&key=AIzaSyAVzCW7rLklcCn7_FJRKtyLF2fsZxQEZyU",
        type: 'get',
        dataType: 'jsonp'
    });
}

function AjaxGetVimeoVideo(videoId) {
    return $.ajax({
        url: "https://vimeo.com/api/oembed.json?url=https%3A//vimeo.com/" + videoId,
        type: 'get',
        dataType: 'jsonp'
    });
}