function confirmUrl(question, confirmedUrl, buttonText, type) {
    if (!type)
        type = 'success'
    const modal = $('<div id="confirm-modal" class="modal fade" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">' + confirmText + '</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p>' + question + '</p></div><div class="modal-footer"><a class="btn btn-sm btn-' + type + '" href="' + confirmedUrl + '">' + buttonText + '</a></div></div></div></div>')
    modal.modal('show')
    modal.on('hidden.bs.modal', function () {
        modal.remove()
    })
}

function showAlert(message, type) {
    if (!type)
        type = 'success'
    const container_style = "transition: all .3s;" + "-webkit-transition: all .3s;" + "position: fixed;" + "top: 30px;" + "width: 100%;" + "z-index: 2000;" + "opacity: 0;" + "text-align: center;"
    const alert_style = "display: inline-block;" + "max-width: 800px;" + "box-shadow: 0 10px 20px -10px #000;" + "-webkit-box-shadow: 0 10px 20px -10px #000;"
    const alert = $('<div style="' + container_style + '"><div style="' + alert_style + '" class="alert alert-' + type + '">' + message + '</div></div>')
    $('body').append(alert)
    setTimeout(() => {
        alert.css('opacity', '100')
        alert.css('top', '40px')
        setTimeout(() => {
            alert.css('top', '20px')
            alert.css('opacity', '0')
            setTimeout(() => {
                alert.remove()
            }, 300)
        }, 2000)
    }, 1)
}

function selectMaskedForm(formId, value) {
    $('#' + formId).val(value);

    const base_form_id = formId.split('_').slice(0, -1);
    const main_form_element = $('#' + base_form_id.join('_'));

    $('.masked-container-' + formId + ' button').each((index, buttonElement) => {
        $(buttonElement).removeClass('active');
        $(buttonElement).data('form-ids').split(',').forEach((id) => {
            const sub_form_id = base_form_id.concat([id]).join('_');
            main_form_element.find('label[for="' + sub_form_id + '"]').parent().hide();
        })
    });

    const element = $('.masked-container-' + formId + ' button[data-value="' + value + '"]');
    if (element.length === 0) return;

    element.attr('data-form-ids').split(',').forEach(function (name) {
        const sub_form_id = base_form_id.concat([name]).join('_');
        main_form_element.find('label[for="' + sub_form_id + '"]').parent().show();
    });
    element.addClass('active');
  }