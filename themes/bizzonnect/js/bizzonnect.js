(function ($, Drupal) {

  
  Drupal.behaviors.bizzonnect = {
    attach: function (context, settings) {
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
	  
	  
	  
    }
  };
})(jQuery, Drupal);


