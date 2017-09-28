$(function() {
    $('.car-technical-spec-edit').on('click', function(e) {
        e.preventDefault();
        editCarTech($(this).attr('data-id'));
    });

    $('.btnEditCarTech').on('click', function() {
        saveCarTech();
    });

    $('#cloneButton').on('click', function() {
        var carId = $('#cloneList').val(),
            wrapper = $('.car-options-wrapper');

        if (carId > 0) {
            ajaxSpinner.button($(this), 'small-dark');

            $.ajax({
                url: baseURL + 'admin/car/get-tech-options',
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {
                    carId: carId
                },
                success: function(response) {
                    if (isJson(response)) {
                        var data = $.parseJSON(response),
                            options = data.options,
                            elem;

                        for (var key in options) {
                            if (options.hasOwnProperty(key)) {
                                for (var itemKey in options[key].items) {
                                    if (options[key].items.hasOwnProperty(itemKey)) {
                                        $('.car-tech-input[data-id="' + itemKey + '"]').val(options[key].items[itemKey].value);
                                    }
                                }
                            }
                        }

                        wrapper.find('.panel-collapse.collapse').removeClass('in');
                        wrapper.find('.panel-collapse.collapse:first').addClass('in');
                    }
                },
                complete: function() {
                    ajaxSpinner.stop(true);
                }
            });
        }
    });
});

var editCarTech = function(carId) {
    var carTechDlg = $('#carTechDlg');
    carTechDlg.find('.modal-title').html('Редактировать технические характеристики');
    carTechDlg.find('#car_form').before('<div class="pre-loader" style="font-size: 20px;">Загрузка... </div>');

    $('#editCarId').val(carId);

    ajaxSpinner.add($('.pre-loader'), 'medium', 'append', {'left': 117, 'top': 16});

    carTechDlg.modal('show');

    $.ajax({
        url: baseURL + 'admin/car/get-tech-options-form',
        async: true,
        type: 'POST',
        dataType: 'html',
        data: {
            carId: carId
        },
        success: function(response) {
            if (isJson(response)) {
                var data = $.parseJSON(response);

                if (data.error) {
                    showMessage(data.error, 'Ошибка');
                    carTechDlg.modal('hide');
                } else {
                    carTechDlg.find('h2').html(data.carName);

                    $('#car_form').show();
                    $('#car-accordion-wrapper').html(data.tree);

                    var cloneList = $('#cloneList');
                    cloneList.find('option[value!=0]').remove();

                    if (data.clones.length > 0) {
                        $('.clone-wrapper').show();
                    } else {
                        $('.clone-wrapper').hide();
                    }

                    for (var i = 0; i < data.clones.length; ++i)
                    {
                        cloneList.append('<option value="' + data.clones[i].id + '">' +
                            data.clones[i].manufacturer.name + ' ' +
                            data.clones[i].model.name + ', ' +
                            data.clones[i].engine.engine_name + ' л.с.'
                        );
                    }
                }
            }
        },
        complete: function() {
            $('.pre-loader').remove();
            ajaxSpinner.stop();
        }
    });
};

var saveCarTech = function() {
    var carId = $('#editCarId').val(),
        carData = [],
        optionId;

    $('.car-tech-input').each(function(index, element) {
        optionId = $(this).attr('data-id');
        carData.push({
            techOptionId: optionId,
            techOptionValue: $(this).val()
        });
    });

    ajaxSpinner.dialog($('#carTechDlg').find('.btn-group'));

    $.ajax({
        url: baseURL + 'admin/car/save-tech-options',
        async: true,
        type: 'POST',
        dataType: 'html',
        data: {
            carId: carId,
            carData: carData
        },
        success: function(response) {
            if (isJson(response)) {
                var data = $.parseJSON(response);
                if (data.error) {
                    showMessage(data.error, 'Ошибка');
                } else {
                    location.reload();
                }
            }
        },
        complete: function() {
            ajaxSpinner.stop();
        }
    });
};