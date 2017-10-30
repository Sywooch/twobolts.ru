var user = {
    /**
     * Init
     */
    init: function () {
        var self = this;

        self.attachEvents();
    },

    attachEvents: function () {
        var self = this;

        $('.btn-test-username').on('click', function () {
            var elem = $('#user-username');

            if (elem.val().length > 2) {
                ajaxSpinner.button($(this), 'small-white');

                self.testUniqueness('username');
            }
        });

        $('.btn-test-email').on('click', function () {
            var elem = $('#user-email');

            if (elem.val().length > 2) {
                ajaxSpinner.button($(this), 'small-dark');

                self.testUniqueness('email');
            }
        });

        $('.btn-random-password').on('click', function () {
            $('#user-newpassword').val(randomString(8));
        });
    },

    testUniqueness: function (attr) {
        var elem = $('#user-' + attr),
            data;

        $.ajax({
            url: baseURL + 'admin/user/test-uniqueness',
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {
                id: $('#user-id').val(),
                attr: attr,
                value: elem.val()
            },
            success: function (response) {
                data = $.parseJSON(response);

                if (data.status === 'error') {
                    elem.parents('.form-group').removeClass('has-success').removeClass('has-error').addClass('has-error');
                } else {
                    elem.parents('.form-group').removeClass('has-success').removeClass('has-error').addClass('has-success');
                }

                elem.parents('.form-group').find('.help-block').html(data.text);
            },
            complete: function () {
                ajaxSpinner.stop(true);
            }
        });
    }
};