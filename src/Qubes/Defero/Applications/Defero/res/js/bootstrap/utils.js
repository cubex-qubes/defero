
jQuery(document).ready(function() {
  (function($) {

    $.fn.bsUtilPreventDefault = function(event) {
      if($(this).data("prevent-default")) {
        event.preventDefault();
      }
      return $(this);
    };

  })(jQuery);
});
