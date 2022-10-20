$(document).ready(function () {
    $(".back-to-top").css("display", "none");
    $(window).scroll(() => {
        if($(this).scrollTop() > 20){
            $(".back-to-top").css("display", "block");
        } 
        else {
            $(".back-to-top").css("display", "none");
        }
    });
    $(".back-to-top").click(function() {
        $("html").animate({scrollTop: 0}, 800);
    });
});