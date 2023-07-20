function confirmUrl(question, confirmedUrl, buttonText, type) {
    if (!type)
        type = 'success'
    const modalElement = $('<div id="confirm-modal" class="modal fade" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">' + confirmText + '</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p>' + question + '</p></div><div class="modal-footer"><a class="btn btn-sm btn-' + type + '" href="' + confirmedUrl + '">' + buttonText + '</a></div></div></div></div>')
    const modal = new bootstrap.Modal(modalElement)
    modal.show()
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
    const target_input_element = $('#' + formId)
    target_input_element.val(value);

    const parent_element = target_input_element.parent();
    const base_form_id = formId.split('_').slice(0, -1);

    if (!value) {
        $('.masked-container-' + formId + ' button').first().trigger('click');
        return
    }

    $('.masked-container-' + formId + ' button').each((index, buttonElement) => {
        $(buttonElement).addClass('btn-secondary');
        $(buttonElement).removeClass('btn-primary');
        $(buttonElement).data('form-ids').split(',').forEach((id) => {
            const sub_form_id = base_form_id.concat([id]).join('_');
            parent_element.find('label[for="' + sub_form_id + '"]').parent().hide();
        })
    });

    const element = $('.masked-container-' + formId + ' button[data-value="' + value + '"]');
    if (element.length === 0) return;

    element.attr('data-form-ids').split(',').forEach(function (name) {
        const sub_form_id = base_form_id.concat([name]).join('_');
        parent_element.find('label[for="' + sub_form_id + '"]').parent().show();
    });

    element.addClass('btn-primary');
    element.removeClass('btn-secondary');
}

tinymce.PluginManager.add('media', function(editor, url) {
    editor.ui.registry.addButton('media', {
        icon: 'gallery',
        onAction: async function() {
            mediaModalObject.show()
            const listUrl = jquery(editor.targetElm).data('list-url')

            let glue = "?"
            if (listUrl.includes("?")) {
                glue = "&"
            }
            const params = {
                "t": editor.id
            }

            const response = await fetch(listUrl + glue + jquery.param(params), {method: "GET"})
            const html = await response.text()
            mediaModelBody.html(html)
        }
    })
    editor.on('ObjectResized', async function(event) {
        if (!event.width || !event.height) return

        const params = {
            "id": jquery(event.target).data('id'),
            "w": event.width,
            "h": event.height
        }

        const insertUrl = jquery(editor.targetElm).data('insert-url')
        const response = await fetch(insertUrl + "?" + jquery.param(params), {method: "GET"})
        const html = await response.text()

        jquery(event.target).replaceWith(jquery(html))
    })
})

export {confirmUrl, showAlert, selectMaskedForm}
