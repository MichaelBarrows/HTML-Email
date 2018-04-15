function send_form(event) {
    event.preventDefault();
    var contact_form = $(this);
    var promise = $.ajax('https://michaelbarrows.com/html-email/', {
        data: contact_form.serialize(),
        method: 'POST'
    });
    promise.done(function(data) {
        contact_form.find('input[name=name]').val('nope');
        contact_form.find('input[name=email]').val('');
        contact_form.find('input[name=phone]').val('');
        contact_form.find('textarea').val('');
        contact_form.hide();
        contact_form.parent().append('<p><strong>Your message has been sent.</strong></p>');
    });
}

$(document).on('ready', function(event) {
    $('form').submit(send_form(event));
});
