var isTouch = ((typeof window.ontouchstart) !== "undefined");
    var touchStart = isTouch ? "touchstart" : "mousedown";
    var touchEnd = isTouch ? "touchend" : "mouseup";

    $("#123keyboardButton").on(touchEnd, function(e){
        $("#123-keyboard").show();
        $("#xy-keyboard").hide();
        $("#flickmath-fullkeyboard").hide();
    });
    $("#xykeyboardButton").on(touchEnd, function(e){
        $("#123-keyboard").hide();
        $("#xy-keyboard").show();
        $("#flickmath-fullkeyboard").hide();
    });
    $("#fullkeyboardButton").on(touchEnd, function(e){
        $("#123-keyboard").hide();
        $("#xy-keyboard").hide();
        $("#flickmath-fullkeyboard").show();
    });
    
    $(".flick-median").on(touchStart, function(e){
        e.preventDefault();

        console.log("touchStart");
        console.log($focus);
        $(this).nextAll().show();
    });
    $(".flick-list li").on(touchEnd, function(e){
        e.preventDefault();
        var thisX, thisY;
        thisX = e.clientX || e.originalEvent.changedTouches[0].clientX;
        thisY = e.clientY || e.originalEvent.changedTouches[0].clientY;
        //console.log(e);

        //console.log("touchEnd");
        //console.log(this.id);
        var $select = $(document.elementFromPoint(thisX,thisY));
        if( $select.is( $(this).parent().children() ) ){
            var inputVal =$("#flick-preview").text() + $select.attr("id");;
            $("#flick-preview").text(inputVal)
        }
        $(this).parent().children(":not(.flick-median)").hide();
    });
