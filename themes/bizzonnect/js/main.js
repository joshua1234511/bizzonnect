jQuery(document).ready(function($) {
    "use strict";

    /* Search */
    $(".search-trigger").on('click', function() {
        $(".search").slideToggle("slow", function() {});
	   $("i").toggleClass( "fa-times" , "fa-search");
    });

});

/* Simple tabs */
$('#tabs li a').on('click', function(e) {
    $('#tabs li, #content .current').removeClass('current').removeClass('fadeInLeft');
    $(this).parent().addClass('current');
    var currentTab = $(this).attr('href');
    $(currentTab).addClass('current fadeInLeft');
    e.preventDefault();

});

/* Responsive Menu */
$(".menu-trigger").on('click', function() {
    $("header nav").slideToggle("slow", function() {});
    return false;
});

/* Backtotop */
$('.copy1 a').on('click', function() {
    $('html, body').animate({
        scrollTop: 0
    }, 'slow');
    return false;
});


