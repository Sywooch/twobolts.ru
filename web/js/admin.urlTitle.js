var urlTitle = {
    /**
     * Init
     */
    init: function () {
        var self = this;

        $('.get-url-link').on('click', function (e) {
            e.preventDefault();
            self.getTitle($(this));
        });
    },

    /**
     *
     * @param elem
     */
    getTitle: function (elem) {
        var self = this,
            target = $(elem.data('target')),
            source = $(elem.data('source')),
            sourceString = [],
            value = '';

        if (source.length) {
            $.each(source, function () {
                if ($(this).prop('tagName').toLowerCase() === 'select') {
                    sourceString.push($(this).find('option:selected').text());
                } else {
                    sourceString.push($(this).val());
                }
            });

            if (sourceString.length) {
                ajaxSpinner.button(elem, 'small-dark');

                $.ajax({
                    url: baseURL + 'admin/url-title/get',
                    async: true,
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        source: sourceString.join(' ')
                    },
                    success: function (response) {
                        value = $.parseJSON(response);
                        self.setValue(target, value);
                    },
                    complete: function () {
                        ajaxSpinner.stop(true);
                    }
                });
            }
        }
    },

    /**
     *
     * @param target
     * @param value
     */
    setValue: function (target, value) {
        var self = this,
            tagName = target.prop('tagName').toLowerCase();

        if (tagName === 'input' || tagName === 'textarea') {
            target.val(value['full']);
        } else {
            target.text(value['full']);
        }
    }
};
