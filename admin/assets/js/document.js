$(function () {
    $('.datepicker').datepicker({
        dateFormat: 'dd/mm/yy',
        autoclose: true,
        todayHighlight: true,
    });

    // Toggle readonly attribute for document_code input
    $('#enable_manual_code').change(function () {
        if ($(this).is(':checked')) {            
            $('#document_code').prop('readonly', false);            
            $('#document_code').focus();            
            $('#document_code').val('');
        } else {            
            $('#document_code').prop('readonly', true);
        }
    });
});