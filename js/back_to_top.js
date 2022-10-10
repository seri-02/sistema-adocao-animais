$(document).ready(function () {
    $("body").scroll(() => {
        $(".back-to-top").css("display", "block");
        if($(this).scrollTop() > 0){
            $(".back-to-top").css("display", "block");
        } 
        else {
            $(".back-to-top").css("display", "none");
        }
    });
    $(".back-to-top").click(function() {
        $("html, body").animate({scrollTop: 0}, 800);
    });
});