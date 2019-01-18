(function($) {
    'use strict';
    $(document).ready(function(){
      $.AMUI.progress.start();
    });
    $(window).load(function(){
      $.AMUI.progress.done();
    });
  
})(jQuery);