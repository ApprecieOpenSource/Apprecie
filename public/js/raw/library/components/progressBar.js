function progressBar(progressSelector,numberOfSteps,onComplete,onCompleteArgs){
    var progress=0;
    var stepValue=parseInt(100/numberOfSteps);
    var currentstep=0;
    $(progressSelector).find('.progress-bar').css('width',progress+'%');
    $(progressSelector).show();

    this.setTitle=function(title){
        var titleSelector=$(progressSelector).find('.progress-title');

        $(titleSelector).stop().fadeOut('fast',function(){
            $(this).html(title);
            $(this).stop().fadeIn('fast');
        }).find('.progress-title')
    }

    this.completeStep=function(){
        currentstep++;
        progress=parseInt(Math.ceil(currentstep*stepValue));
        $(progressSelector).find('.progress-bar').css('width',progress+'%');

        if(progress==100){
            $(progressSelector).fadeOut('fast');
            if(onComplete!=null){
                window[onComplete](onCompleteArgs)
            }
        }
    }
}