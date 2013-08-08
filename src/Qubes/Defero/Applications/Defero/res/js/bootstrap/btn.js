/**
 * Default class handles for bootstrap button js;
 * http://twitter.github.io/bootstrap/javascript.html#buttons
 *
 * @author  gareth.evans
 * @package bs
 */
jQuery(document).ready(function() {
  /**
   * Anonymous function gives us local scope and our own local copy of the
   * jQuery object via `$`. Only use `jQuery` in global scope.
   */
  (function($) {
    /**
     * Loading
     *
     * @handle         .js-btn-loading
     * @data-attribute data-loading-text
     */
    $('.js-btn-loading').click(function() {
      $(this).button('loading');
    });

    /**
     * Reset
     *
     * Reset has an optional parameter enabling you to have multiple text
     * options for a button, this can be achieve by setting different data
     * attributes and passing the unique part through to the function.
     *
     * <button type="button" class="btn"
     *   data-starting-text="Starting"
     *   data-ending-text="Ending">...</button>
     * <script>
     *   $('.btn').btnReset('starting'); // Button text: "Starting"
     *   $('.btn').btnReset('ending');   // Button text: "Ending"
     * </script>
     *
     * $('#my-btn').btnReset();
     *
     * @data-attribute data-state-text
     */
    $.fn.btnReset = function(state) {
      if(state === undefined) {
        state = 'reset';
      }
      $(this).button(state);
    };
  })(jQuery);
});
