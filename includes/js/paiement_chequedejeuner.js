jQuery(document).ready(function ($) {
    if ($('#wc_pcd_display').prop('checked')) {
        $('#hidden_content').show();
    }

    $('#wc_pcd_display').on('change', function() {
        if ($(this).prop('checked')) {
            $('#hidden_content').show();
        } else {
            $('#hidden_content').hide();
        }
    })

    $('#wc_pcd_montant').on('keyup', function() {
        var montant = $(this).val();

        $.ajax({
            url: wc_pcd_ajax,
            type: 'POST',
            data: {
              'action': 'compute_new_price',
              'pcd_montant': montant
            }
        }).done(function(response) {
            $('#hidden_montant').text(response);
            $('#hidden_montant').show();
            $('body').trigger('update_checkout');
        }).fail(function(error) {
            console.log(error);
        });
    });
});