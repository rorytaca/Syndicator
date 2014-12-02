$(window).resize(function() {
    //Watch for resizes while collapsed navbar is out
    console.log('resize');
    if (window.innerWidth < 760) {
        //if has class "in"
        if ($("#navbar-collapse-1").hasClass("in")) {
            $("#navbar-collapse-1").toggleClass("in");
        }
    }

});