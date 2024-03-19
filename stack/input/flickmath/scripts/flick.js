var isTouch = ((typeof window.ontouchstart) !== "undefined");
var touchStart = isTouch ? "touchstart" : "mousedown";
var touchEnd = isTouch ? "touchend" : "mouseup";

$(".flick-median").on(touchStart, function(e){
    e.preventDefault();

    console.log("touchStart");

    $(this).nextAll().show();
});
$(".flick-list li").on(touchEnd, function(e){
    e.preventDefault();
    var thisX, thisY;
    thisX = e.clientX || e.originalEvent.changedTouches[0].clientX;
    thisY = e.clientY || e.originalEvent.changedTouches[0].clientY;
    console.log(e);

    console.log("touchEnd");
    console.log(this);
    //var inputVal =$("#flickInput").val() + $(this).html();
    var $select = $(document.elementFromPoint(thisX,thisY));
    if( $select.is( $(this).parent().children() ) ){
        var inputVal =$("#flickInput").val() + $select.html();
        $("#flickInput").val(inputVal)
    }
    $(this).parent().children(":not(.flick-median)").hide();
});