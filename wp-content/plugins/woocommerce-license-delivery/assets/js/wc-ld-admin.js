(function ($) {
    'use strict';
    $(function () {

        // add thick box order item
        if (typeof(tb_click) == 'function') {
            jQuery('.item a.thickbox').click(tb_click);
        }

       $('.select2').select2();
        /**
         * Filter codes by product in wt_codes_repo page
         */
        var $filterSubmitButton = $('#code-query-submit');
        var $eventSelect = $('.select2-product-filter').select2();
        $filterSubmitButton.on('click', function(){
            var productFilter = $eventSelect.val();
            if (productFilter != '') {
                document.location.href = 'admin.php?page=license_codes' + productFilter;
            }
        });

        // confirmation message for bulk delete

        $('.toplevel_page_license_codes').find('#wt-product-filter').closest('form').on('submit', function(e){
            var bulkAction = $('#bulk-action-selector-top').val();
            if('bulk-delete' == bulkAction){
                var msgs = window.confirm('Are you sure to process a bulk delete?');
                if(msgs) {
                    return true;
                }
                return false;
            }
        });


        $('.toplevel_page_license_codes').find('.column-license_status').find('.dashicons-lock').closest('tr').addClass('sold-highlight');
        $('.toplevel_page_license_codes').find('.column-license_status').find('.dashicons-yes').closest('tr').addClass('unsold-highlight');



    });


})(jQuery);
