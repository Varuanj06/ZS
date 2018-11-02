
function show_message(css_class, title, message){
    var alerts_container = jQuery('.alerts-container');
    if(alerts_container[0] === undefined){
        alerts_container = jQuery('<div class="alerts-container"></div>').appendTo($('body'));
    }

    var message     = "<div class='alert-fixed alert "+css_class+"'><strong>"+title+"</strong> "+message+"</div>";
    var j_message   = jQuery(message);

    j_message.prependTo(alerts_container).hide().fadeIn(300);
    setTimeout(function(){
        j_message.fadeOut(300);
    }, 5000);
}