var news = {
    slug: '',

    init: function () {
        this.attachEvents();
    },

    attachEvents: function () {
        var self = this;

        $('#news-title').on('blur', function() {
            if ($(this).val().length > 0 && $('#isNew').val() == 1) {
                $.ajax({
                    url: baseURL + 'admin/news/url-title',
                    async: true,
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        title: $(this).val()
                    },
                    success: function(response) {
                        var data = $.parseJSON(response);

                        $('#edit-slug-box').removeClass('hidden');
                        $('#editable-post-name-full').html(data.full);
                        $('#news-url_title').val(data.full);
                        $('#editable-post-name').html(data.ellipsized);
                    }
                });
            }
        });

        $('#editPermalink').on('click', function() {
            var editable = $('#editable-post-name');

            self.slug = editable.html();
            editable.html('<input type="text" id="editable-post-name-input" value="' + $('#editable-post-name-full').html() + '" class="form-control input-sm" />');
            $(this).hide();

            $('#getPermalink, #savePermalink, #cancelPermalink').fadeIn();
        });

        $('#cancelPermalink').on('click', function() {
            $('#editable-post-name').html(self.slug);
            $('#getPermalink, #savePermalink, #cancelPermalink').hide();
            $('#editPermalink').fadeIn();
        });

        $('#getPermalink').on('click', function() {
            var editableInput = $('#news-title');

            if (editableInput.val().length > 0) {
                self.prepareUrl($(this), editableInput.val());
            }
        });

        $('#savePermalink').on('click', function() {
            var editableInput = $('#editable-post-name-input');

            if (editableInput.val().length > 0) {
                self.prepareUrl($(this), editableInput.val());
            }
        });

        $('.news-models').on('click', '.news-model-item .fa', function() {
            var parent = $(this).parent(),
                type = parent.data('type'),
                value = parent.attr('data-' + type);

            parent.remove();

            $('[data-type="' + type + '"][value="' + value + '"]').remove();
            $('[data-value="' + value + '"][value="' + type + '"]').remove();

            if ($('.news-model-item').length == 0) {
                $('.news-models-empty').show();
            }
        });

        $('#addNewsModel').on('click', function() {
            var manufacturer = $('#manufacturerAdding'),
                model = $('#modelAdding');

            if (model.val() != 0) {
                if ($('[data-model="' + model.val() + '"]').length == 0) {
                    $('.news-models').append(
                        '<input type="hidden" name="news_models_ids[]" value="' + model.val() + '" data-type="model">'
                        + '<input type="hidden" name="news_models_types[]" value="model" data-value="' + model.val() + '">'
                        + '<span class="news-model-item" data-model="' + model.val() + '" data-type="model">'
                        + '<i class="fa fa-times-circle"></i>'
                        + manufacturer.find('option:selected').text() + ' '
                        + model.find('option:selected').text()
                        + '</span>'
                    );

                    $('.news-models-empty').hide();
                }
            } else if ($('[data-manufacturer="' + manufacturer.val() + '"]').length == 0) {
                $('.news-models').append(
                    '<input type="hidden" name="news_models_ids[]" value="' + manufacturer.val() + '" data-type="manufacturer">'
                    + '<input type="hidden" name="news_models_types[]" value="manufacturer" data-value="' + manufacturer.val() + '">'
                    + '<span class="news-model-item" data-manufacturer="' + manufacturer.val() + '" data-type="manufacturer">'
                    + '<i class="fa fa-times-circle"></i>' + manufacturer.find('option:selected').text() +
                    '</span>'
                );

                $('.news-models-empty').hide();
            }
        });
    },

    prepareUrl:  function (el, title) {
        ajaxSpinner.button(el, 'small-dark');

        $.ajax({
            url: baseURL + 'admin/news/url-title',
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {
                title: title
            },
            success: function(response) {
                var data = $.parseJSON(response);
                $('#edit-slug-box').removeClass('hidden');
                $('#editable-post-name-full').html(data.full);
                $('#news-url_title').val(data.full);
                $('#editable-post-name').html(data.ellipsized);
                $('#getPermalink, #savePermalink, #cancelPermalink').hide();
                $('#editPermalink').fadeIn();
            },
            complete: function() {
                ajaxSpinner.stop(true);
            }
        });
    },

    galleryItemUploaded: function (data) {
        $('form').append('<input type="hidden" name="gallery[]" value="' + data.response.imageName + '">');
    },

    galleryUploaded: function (data) {
        var i,
            response = data.response;

        for (i = 0; i < response.initialPreview.length; ++i)
        {
            $('form').append('<input type="hidden" name="gallery[]" value="' + response.initialPreview[i] + '">');
        }
    },

    galleryItemRemoved: function (key) {
        $('[value="' + key+ '"]').remove();
    },

    gallerySorted: function (params) {
        $('[name="gallery[]"]').remove();

        for (var i = 0; i < params.stack.length; ++i)
        {
            $('form').append('<input type="hidden" name="gallery[]" value="' + params.stack[i].key + '">');
        }
    }
};