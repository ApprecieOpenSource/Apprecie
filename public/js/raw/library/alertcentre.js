function ajaxGetThreads(pageNumber){
    return $.ajax({
        url: "/alertcentre/ajaxGetThreads/" + pageNumber,
        type: 'post',
        dataType: 'json',
        data: {'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN},
        cache: false
    });
}