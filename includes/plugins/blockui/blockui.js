/* ====================================================================== *
      BLOCK UI HELPER
 * ====================================================================== */

    function blockUI($el){
        $('.spinner-container').addClass('show-spinner');
    }

    function unblockUI($el){
        $('.spinner-container').removeClass('show-spinner');
    }