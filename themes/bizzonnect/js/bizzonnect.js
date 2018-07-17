(function ($, Drupal) {

  
  Drupal.behaviors.bizzonnect = {
    attach: function (context, settings) {

      jQuery(document).ready(function($) {
        $('.btn-btt').smoothScroll({speed: 1000});
        if($("#search-block-form [name='keys']").val() === "") {
          $("#search-block-form input[name='keys']").val(Drupal.t("Type to Search..."));
        }
        $("#search-block-form input[name='keys']").focus(function() {
          if($(this).val() === Drupal.t("Type to Search...")) {
            $(this).val("");
          }
        }).blur(function() {
          if($(this).val() === "") {
            $(this).val(Drupal.t("Type to Search..."));
          }
        });
  	  
        $(window).scroll(function() {
          if($(window).scrollTop() > 200) {
              $('.btn-btt').show();
            }
            else {
              $('.btn-btt').hide();
            }
       }).resize(function(){
          if($(window).scrollTop() > 200) {
              $('.btn-btt').show();
            }
            else {
              $('.btn-btt').hide();
            }
        });
  	  
  	  
  	  /* Search */
        $(".search-trigger").on('click', function() {
         var e = document.getElementById("search");
         if(e.style.display == 'block') {
            e.style.display = 'none';
  		  $(".search-trigger i").addClass("fa-search");
  		  $(".search-trigger i").removeClass("fa-times");
  	   }
         else {
            e.style.display = 'block';
  		  $(".search-trigger i").addClass("fa-times");
  		  $(".search-trigger i").removeClass("fa-search");
     		 }
        });
  	  
  	  /* Dropdown menu */
  	  $("header nav ul.menu .menu-item--expanded").hover(
  		function(){
  		  $(this).addClass("open");
  		},function(){
  		  $(this).removeClass("open");
  		}
  	  );
	  
	  });
	  
    }
  };
})(jQuery, Drupal);


(function ($) {
 'use strict';
  $(document).ready(function(){
      $(".ads-click").on('click',function(e){
      var nid = $(this).attr("data-nid");
      var url = window.location.href;
      var ad_url = $(this).attr("href");
      var title = $(this).attr("data-title");
      var type = $(this).attr("data-type");
      var device = $(this).attr("data-device");
      var post_data = 'nid='+nid+'&url='+url+'&ad_url='+ad_url+'&title='+title+'&type='+type+'&device='+device;
      $.ajax({
        type: 'POST',
        url: drupalSettings.path.baseUrl + "adclick",
        data: post_data,
        success: true,
      });
     });
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
      $(".menu.dropdown-menu li.expanded > a").click(function() {
        $(this).next('ul').toggleClass( "open" );
        return false;
      });
    }
  });
})(jQuery, drupalSettings);