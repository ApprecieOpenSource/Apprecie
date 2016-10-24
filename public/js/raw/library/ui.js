var OverlayMessage=function(message){
    var template = $.templates("#overlay");
    $("body").append(template.render({'message':message}));
}