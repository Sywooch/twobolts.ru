var documentBody,

    admin = {
        messageDialog: $('#messageDlg'),
        promptDialog: $('#promptDlg'),

        gridElem: '',
        gridController: '',

        user: {},

        urlTitle: {},

        settings: {},

        engine: {},

		car: {},

        news: {},

        /**
         * Init of object
         */
        init: function () {
            var self = this;

            $.getScript(baseURL + 'js/admin.user.js', function () {
                user.init();
                self.user = user;
            });

            $.getScript(baseURL + 'js/admin.urlTitle.js', function () {
                urlTitle.init();
                self.urlTitle = urlTitle;
            });

        	$.getScript(baseURL + 'js/admin.settings.js', function() {
                settings.init();
                self.settings = settings;
            });

            $.getScript(baseURL + 'js/admin.news.js', function () {
                news.init();
                self.news = news;
            });

            $.getScript(baseURL + 'js/admin.engine.js', function () {
                engine.init();
                self.engine = engine;
            });

            $.getScript(baseURL + 'js/admin.car.js', function () {
                car.init();
                self.car = car;
            });

            self.attachEvents();

            $(window).on('load', function () {
                self.fixedButtonsToolbar();
            })
        },

        /**
         * Bind events to elements
         */
        attachEvents: function () {
            var self = this,
                body = $('body');

            $('[title]').tooltip({
                container: 'body'
            });

            $('[name="form_frame"]').on('load', function () {
                ajaxSpinner.stop(true);

                var response = $(this).contents().find('body').text();
                var data = $.parseJSON(response);

                if (data.status === 'ok') {
                    window.location = window.location.href;
                } else {
                    $('.modal-btn-group').prepend(data.message);
                }
            });

            body.on('click', '.deleteSelectedBtn', function(e) {
                e.preventDefault();
                var selected = self.gridElem.yiiGridView('getSelectedRows');

                if (selected.length === 0) {
                    self.showMessage(localizationMessages["Nothing is selected"], localizationMessages["error"]);
                    return false;
                } else if (confirm(localizationMessages["Are you sure delete selected"])) {
                    $.post(
                        '/admin/' + self.gridController + '/delete-selected',
                        { selected : selected },
                        function () {
                            window.location.href = window.location;
                        }
                    );
                }
            });

            $('[data-confirmation]').on('click', function (e) {
                e.preventDefault();

                self.promptMessage($(this).data('confirmation'), $(this).data('callback'));
            });

            body.on('click', '[data-form]', function (e) {
                e.preventDefault();
                self.loadForm($(this));
            });
        },

        /**
         * Display general message to the user
         * @param message
         * @param title
         */
        showMessage: function(message, title) {
            var self = this;

            title = title || '';
            message = message || '';

            self.messageDialog.find('h4').html(title);
            self.messageDialog.find('.modal-body').html(message);
            self.messageDialog.modal('show');
        },

        /**
         * Display prompt message
         * @param message
         * @param callback
         */
        promptMessage: function (message, callback) {
            var self = this,
                fn;

            message = message || '';

            if (window[callback] instanceof Function) {
                fn = window[callback];
            } else if (callback instanceof Function) {
                fn = callback;
            } else {
                fn = function () {
                    window.location = callback;
                };
            }

            self.promptDialog.find('.modal-body').html(message);
            self.promptDialog.find('.btn-modal-confirm').on('click', function () {
                fn();
            });
            self.promptDialog.modal('show');
        },

        /**
         *
         * @return {boolean}
         */
        editorBrowserCallback: function () {
            return false;
        },

        /**
         * Callback for cropper
         * @param cb
         * @param value
         * @param meta
         */
        editorPickerCallback: function (cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            // Note: In modern browsers input[type="file"] is functional without
            // even adding it to the DOM, but that might not be the case in some older
            // or quirky browsers like IE, so you might want to add it to the DOM
            // just in case, and visually hide it. And do not forget do remove it
            // once you do not need it anymore.

            input.onchange = function() {
                var file = this.files[0];

                // Note: Now we need to register the blob in TinyMCEs image blob
                // registry. In the next release this part hopefully won't be
                // necessary, as we are looking to handle it internally.
                var id = 'blobid' + (new Date()).getTime();
                var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                var blobInfo = blobCache.create(id, file);
                blobCache.add(blobInfo);

                // call the callback and populate the Title field with the file name
                cb(blobInfo.blobUri(), { title: file.name });
            };

            input.click();
        },

        /**
         * Fix form toolbar to bottom
         */
        fixedButtonsToolbar: function () {
            var toolbar = $('.fixed-bottom-toolbar'),
                formContainer = $('.form-container');

            if (formContainer.height() > $(window).height()) {
                toolbar.addClass('fixed');
            }
        },

        /**
         * Load form via AJAX
         * @param elem
         */
        loadForm: function (elem) {
            var self = this,
                title = '',
                formDialog = $('#formDlg'),
                form = $('.form-container');

            if (elem.data('icon')) {
                title = '<i class="fa fa-' + elem.data('icon') + '"></i> ';
            }
            if (elem.data('title')) {
                title += elem.data('title');
            }

            formDialog.find('.modal-title').html(title);

            form.html('');
            form.append('<h4>Загрузка данных...</h4>');
            ajaxSpinner.add(form.find('h4'), 'small', 'append', {'margin-left': '20px', 'position': 'relative'});

            $.ajax({
                url: baseURL + 'admin/' + self.gridController + '/load-form?id=' + elem.data('id'),
                async: true,
                type: 'POST',
                dataType: 'html',
                success: function(response) {
                    var data = $.parseJSON(response);
                    form.html(data);
                },
                complete: function() {
                    ajaxSpinner.stop();
                }
            });

            formDialog.modal('show');
        },

        /**
         * Generate model name with body name
         * @param model
         */
        getModelName: function (model) {
            var result = model.name;

            if (model.body_id) {
                result += ' ' + model.body.body_name;
            }

            return result;
        },

        /**
         * Generate engine name
         * @param engine
         */
        getEngineName: function (engine) {
            return engine.engine_name + ' ' + localizationMessages['h.p.'];
        }
    };

$(function() {
    admin.init();

    documentBody = $('body');

	documentBody.on('change', '#comparison_manufacturer', function() {
		$('#comparison_model').val('').trigger('change');
	});
});