var engine = {
    manufacturerWrapper: null,

    manufacturerSelector: null,

    manufacturerTarget: null,

    modelWrapper: null,

    engineName: null,

    existingEngines: null,

    /**
     * Init of object
     */
    init: function () {
        this.manufacturerWrapper = $('.manufacturer-wrapper');
        this.manufacturerSelector = $('.manufacturer-selector');
        this.manufacturerTarget = $('.manufacturer-target');
        this.modelWrapper = $('.model-wrapper');
        this.engineName = $('#engine-engine_name');
        this.existingEngines = $('.existing-engines');

        this.attachEvents();
    },

    /**
     * Bind events to elements
     */
    attachEvents: function () {
        var self = this,
            body = $('body');

        self.manufacturerSelector.on('change', function () {
            self.loadManufacturerModels();
            self.toggleUrlGenerator();
        });

        self.manufacturerTarget.on('change', function () {
            self.loadModelEngines();
            self.toggleUrlGenerator();
        });

        $('#engines_select_all').on('click', function (e) {
            e.preventDefault();
            self.toggleEngines();
            self.validateEngine();
        });

        $('#engines_show_selected').on('click', function (e) {
            e.preventDefault();
            self.showSelected();
        });

        body.on('click', '.existing-engines :checkbox', function () {
            if ($(this).is(':checked')) {
                $(this).parents('.checkbox').addClass('selected');
            } else {
                $(this).parents('.checkbox').removeClass('selected');
            }

            self.validateEngine();
        });

        self.engineName.on('focus, blur, keyup', function () {
            self.toggleUrlGenerator();
        });

        $('#btnSaveEngine').on('click', function (e) {
            e.preventDefault();
            self.saveEngine();
        });
    },

    /**
     * Load models for selected manufacturer
     */
    loadManufacturerModels: function () {
        var self = this;

        self.manufacturerTarget.find('option[value!=""]').remove();

        if (self.manufacturerSelector.val()) {
            ajaxSpinner.add(self.manufacturerWrapper, 'small-dark', 'append', {'position': 'absolute', 'right': '60px', 'top': '42px'});

            $.ajax({
                url: baseURL + 'admin/model/get-models?id=' + self.manufacturerSelector.val(),
                async: true,
                type: 'POST',
                dataType: 'html',
                success: function (response) {
                    var data = $.parseJSON(response),
                        models = data.models;

                    for (var i = 0; i < models.length; ++i)
                    {
                        self.manufacturerTarget.append('<option value="' + models[i].id + '">' + admin.getModelName(models[i]) + '' + '</option>');
                    }

                    self.manufacturerTarget.trigger('change').attr('disabled', false);
                },
                complete: function () {
                    ajaxSpinner.stop();
                }
            });

            self.loadEngines();
        } else {
            self.manufacturerTarget.attr('disabled', true);
            self.existingEngines.html(localizationMessages['Select manufacturer to see existing engines...']);
            $('.existing-engines-toggle, .existing-engines-count').hide();
        }
    },

    /**
     * Load existing engines for selected manufacturer
     */
    loadEngines: function () {
        var self = this;

        if ($('#isNew').val() == 1) {
            $.ajax({
                url: baseURL + 'admin/manufacturer/get-engines?id=' + self.manufacturerSelector.val(),
                async: true,
                type: 'POST',
                dataType: 'html',
                success: function (response) {
                    var data = $.parseJSON(response),
                        engines = data.engines;

                    self.existingEngines.html('');

                    if (engines.length) {
                        $('.existing-engines-count').text(engines.length);
                        $('.existing-engines-toggle, .existing-engines-count').show();

                        for (var i = 0; i < engines.length; ++i) {
                            self.existingEngines.append('<div class="checkbox">' +
                                '<label>' +
                                '<input type="checkbox" name="existing_engines[]" value="' + engines[i].id + '"> ' +
                                '<span>' + admin.getEngineName(engines[i]) + '</span>' +
                                '</label>' +
                                '</div>'
                            );
                        }
                    } else {
                        $('.existing-engines-toggle, .existing-engines-count').hide();
                        self.existingEngines.html(localizationMessages['Manufacturer has no engines...']);
                    }
                },
                complete: function () {
                    ajaxSpinner.stop();
                }
            });
        }
    },

    /**
     * Load engines for selected model
     */
    loadModelEngines: function () {
        var self = this,
            item;

        self.existingEngines.find('label .fa').remove();
        self.existingEngines.find('.bg-success').removeClass('bg-success');

        if ($('#isNew').val() == 1 && self.manufacturerTarget.val().length) {
            ajaxSpinner.add(self.modelWrapper, 'small-dark', 'append', {'position': 'absolute', 'right': '60px', 'top': '42px'});

            $.ajax({
                url: baseURL + 'admin/model/get-engines?id=' + self.manufacturerTarget.val(),
                async: true,
                type: 'POST',
                dataType: 'html',
                success: function (response) {
                    var data = $.parseJSON(response),
                        engines = data.engines;

                    if (engines.length) {
                        for (var i = 0; i < engines.length; ++i) {
                            item = self.existingEngines.find('label span').filter(function() {
                                return $(this).text() === admin.getEngineName(engines[i]);
                            });
                            if (item.length) {
                                item.parents('.checkbox').addClass('bg-success');
                                item.append('<i class="fa fa-check text-success"></i>');
                            }
                        }
                    }
                },
                complete: function () {
                    ajaxSpinner.stop();
                }
            });
        }
    },

    /**
     * Toggle on/off checkboxes
     */
    toggleEngines: function () {
        var button = $('#engines_select_all');

        if (button.data('checked') === false) {
            button.html('<i class="fa fa-toggle-off" aria-hidden="true"></i>' + localizationMessages['Deselect all'])
                .data('checked', true)
                .blur();

            $('.existing-engines :checkbox').prop('checked', true).parents('.checkbox').addClass('selected');
        } else {
            button.html('<i class="fa fa-toggle-on" aria-hidden="true"></i>' + localizationMessages['Select all'])
                .data('checked', false)
                .blur();

            $('.existing-engines :checkbox').prop('checked', false).parents('.checkbox').removeClass('selected');
        }
    },

    /**
     * Show selected/all checkboxes
     */
    showSelected: function () {
        var button = $('#engines_show_selected');

        if (button.data('show') === 'all') {
            button.html('<i class="fa fa-eye-slash" aria-hidden="true"></i>' + localizationMessages['Show all'])
                .data('show', 'selected')
                .blur();

            $('.existing-engines .checkbox').hide();
            $('.existing-engines .checkbox.selected').show();
        } else {
            button.html('<i class="fa fa-eye" aria-hidden="true"></i>' + localizationMessages['Show selected'])
                .data('show', 'all')
                .blur();

            $('.existing-engines .checkbox').show();
        }
    },

    /**
     * Toggle enable/disable URL generator field
     */
    toggleUrlGenerator: function () {
        var self = this;

        if (trim(self.engineName.val()).length && trim(self.manufacturerSelector.val()).length && trim(self.manufacturerTarget.val()).length) {
            $('#engine-url_title, .get-url-link').attr('disabled', false);
        } else {
            $('#engine-url_title, .get-url-link').attr('disabled', true);
        }

        self.validateEngine();
    },
    /**
     * Submit form
     */
    saveEngine: function () {
        var self = this;

        if (self.validateEngine()) {
            $('form').submit();
        }
    },

    /**
     * Validate Engine model
     * @returns {boolean}
     */
    validateEngine: function () {
        var self = this,
            form = $('form'),
            hasError = false;

        form.find('.help-block').text('');
        form.find('.has-error').removeClass('has-error');

        if (self.manufacturerTarget.val().length === 0) {
            self.manufacturerTarget.parent('.form-group').addClass('has-error');
            self.manufacturerTarget.parent('.form-group').find('.help-block').text('Не выбрана модель.');

            hasError = true;
        }



        if (trim(self.engineName.val()).length === 0 && self.existingEngines.find(':checked').length === 0) {
            self.engineName.parent('.form-group').addClass('has-error');
            self.engineName.parent('.form-group').find('.help-block').text('Не введено название двигателя или не выбран ни один из существующих.');

            hasError = true;
        }

        return !hasError;
    }
};