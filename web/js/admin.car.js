var car = {
    manufacturerWrapper: null,
    manufacturerSelector: null,
    modelWrapper: null,
    modelSelector: null,
    existingEngines: null,

    /**
     * Init of object
     */
    init: function () {
        this.manufacturerWrapper = $('.car-manufacturer-wrapper');
        this.manufacturerSelector = $('.car-manufacturer-selector');
        this.modelWrapper = $('.car-model-wrapper');
        this.modelSelector = $('.car-model-selector');
        this.existingEngines = $('.car-engine-wrapper');

        this.attachEvents();
    },

    /**
     * Bind events to elements
     */
    attachEvents: function () {
        var self = this,
            body = $('body');

        $('.btn-delete-request').on('click', function (e) {
            e.preventDefault();
            self.processRequest($(this), 'delete-request');
        });

        $('.btn-approve-request').on('click', function (e) {
            e.preventDefault();
            self.processRequest($(this), 'approve-request');
        });

        self.manufacturerSelector.on('change', function () {
            self.loadModels();
        });

        self.modelSelector.on('change', function () {
            self.loadEngines();
        });

        body.on('click', '.existing-engines :checkbox', function () {
            if ($(this).is(':checked')) {
                $(this).parents('.checkbox').addClass('selected');
            } else {
                $(this).parents('.checkbox').removeClass('selected');
            }

            //self.validateCar();
        });
    },

    /**
     * Load models for selected manufacturer
     */
    loadModels: function () {
        var self = this;

        self.modelSelector.find('option[value!=""]').remove();

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
                        self.modelSelector.append('<option value="' + models[i].id + '">' + admin.getModelName(models[i]) + '' + '</option>');
                    }

                    self.modelSelector.trigger('change').attr('disabled', false);
                },
                complete: function () {
                    ajaxSpinner.stop();
                }
            });
        } else {
            self.modelSelector.attr('disabled', true);
            self.existingEngines.html(localizationMessages['Select manufacturer and model to see existing engines...']);
        }
    },

    /**
     * Load existing engines for selected model
     */
    loadEngines: function () {
        var self = this;

        self.existingEngines.html('');

        if (parseInt($('#isNew').val()) === 1 && self.modelSelector.val()) {
            $.ajax({
                url: baseURL + 'admin/model/get-engines?id=' + self.modelSelector.val(),
                async: true,
                type: 'POST',
                dataType: 'html',
                success: function (response) {
                    var data = $.parseJSON(response),
                        engines = data.engines;

                    if (engines.length) {
                        for (var i = 0; i < engines.length; ++i) {
                            self.existingEngines.append('<div class="checkbox">' +
                                '<label>' +
                                '<input type="checkbox" name="existing_engines[]" value="' + engines[i].id + '"> ' +
                                '<span>' + admin.getEngineName(engines[i]) + '</span>' +
                                '</label>' +
                                '</div>'
                            );
                        }

                        self.loadModelCars();
                    } else {
                        self.existingEngines.html(localizationMessages['Model has no engines...']);
                    }
                },
                complete: function () {
                    ajaxSpinner.stop();
                }
            });
        } else {
            self.existingEngines.html(localizationMessages['Select manufacturer and model to see existing engines...']);
        }
    },

    /**
     * Load engines for selected model
     */
    loadModelCars: function () {
        var self = this,
            item;

        self.existingEngines.find('label .fa').remove();
        self.existingEngines.find('.bg-success').removeClass('bg-success');

        if (parseInt($('#isNew').val()) === 1 && self.modelSelector.val().length) {
            ajaxSpinner.add(self.modelWrapper, 'small-dark', 'append', {'position': 'absolute', 'right': '60px', 'top': '42px'});

            $.ajax({
                url: baseURL + 'admin/model/get-cars?id=' + self.modelSelector.val(),
                async: true,
                type: 'POST',
                dataType: 'html',
                success: function (response) {
                    var data = $.parseJSON(response),
                        cars = data.cars;

                    if (cars.length) {
                        for (var i = 0; i < cars.length; ++i) {
                            item = self.existingEngines.find('[value="' + cars[i]['engine_id'] + '"]');
                            if (item.length) {
                                item.parents('.checkbox').addClass('bg-success');
                                item.siblings('span').append('<i class="fa fa-check text-success"></i>');
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
     *
     * @param elem
     * @param action
     */
    processRequest: function (elem, action) {
        var counterElem = $('.car-request-counter'),
            data,
            counter;

        $.ajax({
            url: baseURL + 'admin/catalog/' + action + '?id=' + elem.parents('.list-group-item').data('key'),
            async: true,
            type: 'POST',
            dataType: 'html',
            success: function (response) {
                data = $.parseJSON(response);

                if (data.status === 'ok') {
                    elem.parents('.list-group-item').remove();
                    counter = parseInt(counterElem.text()) - 1;

                    if (counter === 0) {
                        counterElem.parents('.btn').remove();
                    } else {
                        counterElem.text(counter);
                    }
                }
            }
        });
    }
};