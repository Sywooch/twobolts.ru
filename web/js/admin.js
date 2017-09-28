var manufacturerID,
    manufacturerName,
    modelID,
    modelName,
    engineID,
    engineName,
    modelImageUpload,
    documentBody,

    admin = {
        messageDialog: $('#messageDlg'),
        promptDialog: $('#promptDlg'),

        gridElem: '',
        gridController: '',

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

	/*
	$('#carRequestsDlg').dialog({
		autoOpen: false,
		closeText: 'закрыть',
		dialogClass: 'carRequestsDlg',
		height: 320,
		width:640,
		modal: true,
        title: 'Запросы от пользователей',
		resizable: false,
		buttons: {
			'Отмена': function() {
				$(this).dialog('close');
			}
		},
		open: function() {
			$('.ui-dialog .ui-dialog-buttonpane button').blur();
		},
		close: function(event, ui) {
		}
	});

    $('#cropperDefaultFotoDlg').dialog({
        autoOpen: false,
        closeText: 'закрыть',
        dialogClass: 'cropperDefaultFotoDlg',
        height: 520,
        width:582,
        modal: true,
        resizable: false,
        buttons: {
            'Сохранить': function() {
                cropDefaultFoto();
            },
            'Отменить': function() {
                $(this).dialog('close');
            }
        },
        open: function() {
            $('.ui-dialog .ui-dialog-buttonpane button').blur();
        },
        close: function(event, ui) {
            $('#cropperDefaultFotoDlg .viewport').removeAttr('style');
            $('#cropped_default_foto_img').remove();
            $('#cropperDefaultFotoDlg .viewport').prepend('<img id="cropped_default_foto_img" />');
        }
    });*/
	
	$('#remove_image').on('click', function() {
		var fileName = $('[name=model_image]').val()
			modelID = $('[name=model_id]').val();
		$(this).after(ajaxLoaderTransp24);
		$('#ajaxLoaderTransp24').css({
			'position': 'absolute',
			'left':77,
			'top':33,
			'width':48,
			'height':48
		});
		$('#model_image_src img').css('opacity', 0.3);
		
		$.ajax({
			url: baseURL + 'ajax/delete_model_image', 
			async: true,
			type: 'POST',
			dataType: 'html',
			data: {
				modelID: modelID,
				fileName: fileName
			},
			success: function(response)
			{
				$('#model_image_src').html('');
				$('#add_image').show();
				$('#remove_image').hide();
				$('[name=model_image]').val('');
				$('#model_image_container').show();
				
				if (modelID)
				{
					var actionData = $('.fnModel' + modelID).metadata();
					actionData.image = '';
					$('.fnModel' + modelID).attr('data', JSON.stringify(actionData));
				}
			},
			complete: function() {
				$('#ajaxLoaderTransp24').remove();
				$('#model_image_src img').removeAttr('style');
			}
		});
	});
	
	if ($('#add_image').length)
	{
		modelImageUpload = $('#add_image').upload({
			name: 'model_image_file',
			action: '/uploader/upload',
			enctype: 'multipart/form-data',
			params: {
				field: 'model_image_file'
			},
			autoSubmit: true,
			onSubmit: function() {
				elemUpload = '#model_image_src';
				$('#model_image_container').append(ajaxLoader24);
				$('#ajaxLoader24').css({
					'position': 'absolute',
					'top': 0,
					'left': 120
				});
			},
			onComplete: function(response) { showModelImageCropper('#model_image_container', response); },
			onSelect: function() {}
		});
	}

    if ($('#add_default_foto').length)
    {
        modelImageUpload = $('#add_default_foto').upload({
            name: 'default_foto_file',
            action: '/uploader/upload',
            enctype: 'multipart/form-data',
            params: {
                field: 'default_foto_file'
            },
            autoSubmit: true,
            onSubmit: function() {
                elemUpload = '#default_foto_src';
                $('#default_foto_container').append(ajaxLoader24);
                $('#ajaxLoader24').css({
                    'position': 'absolute',
                    'top': 25,
                    'left': 135
                });
            },
            onComplete: function(response) { showDefaultForoCropper('#default_foto_container', response); },
            onSelect: function() {}
        });
    }
	
	$('#random_password_btn').on('click', function() {
		$(this).before(loader24);
		spinner24 = new Spinner(loaderOpts24).spin();
		$('.loader-24').css({
			'position': 'absolute',
			'top': 6,
			'right': 120
		}).append(spinner24.el);
		
		$.ajax({
			url: baseURL + 'ajax/get_random_password', 
			async: true,
			type: 'POST',
			dataType: 'html',
			success: function(response)
			{
				if (response.length) $('#password').val(response);
			},
			complete: function() {
				loader24Stop();
			}
		});
	});
	
	$('#remove_avatar_btn').on('click', function() {
		$(this).after(loader24);
		spinner24 = new Spinner(loaderOpts24).spin();
		$('.loader-24').css({
			'top': 38,
			'left': 38
		}).append(spinner24.el);
		$('#user_avatar').css('opacity', .1);

		$.ajax({
			url: baseURL + 'ajax/delete_user_avatar', 
			async: true,
			type: 'POST',
			dataType: 'html',
			data: {
				user_id: $('#user_id').val(),
				src: $('#user_avatar').attr('data-file')
			},
			success: function(response)
			{
				if (response == '200') $('.avatar-wrapper').fadeOut().remove();
				else display_text_message(response, 400, 200, 'Ошибка');
			},
			complete: function() {
				$('#user_avatar').css('opacity', 1);
				loader24Stop();
			}
		});
	});
	
	$('#submit_user').on('click', function() {
		
		var user_id = $('#user_id').val(),
			username = $('#username').val(),
			email = $('#email').val(),
			password = $('#password').val(),
			send_password = $('#admin_send_password').is(':checked'),
			activated = $('#activated').is(':checked'),
			banned = $('#banned').is(':checked'),
			ban_reason = $('#ban_reason').val(),		
			role = $('select[name="role"]').val(),
			error = false;
		
		$(this).after(loader24);
		spinner24 = new Spinner(loaderOpts24).spin();
		$('.loader-24').css({
			'margin':'18px 10px 0',
			'position': 'relative',
			'display': 'block',
			'float': 'right'
		}).append(spinner24.el);
		
		if (username.length < 3) {
			error = true;
			$('#username').addClass('input-error');
		} else {
			$.ajax({
				url: baseURL + 'ajax/admin_is_username_available', 
				async: true,
				type: 'POST',
				dataType: 'html',
				data: {
					user_id: user_id,
					username: username
				},
				success: function(response)
				{
					var data = $.parseJSON(response);
					if (data.error)
					{
						display_text_message(data.error, 400, 200, 'Ошибка');
						error = true;
						$('#username').addClass('input-error');
					}
				}
			});
		}
		
		if (email.length > 0) {
			if (!validateValueByPattern(email, emailPattern)) {
				error = true;
				$('#email').addClass('input-error');
			} else {
				$.ajax({
					url: baseURL + 'ajax/admin_is_email_available', 
					async: true,
					type: 'POST',
					dataType: 'html',
					data: {
						user_id: user_id,
						email: email
					},
					success: function(response)
					{
						var data = $.parseJSON(response);
						if (data.error)
						{
							display_text_message(data.error, 400, 200, 'Ошибка');
							error = true;
							$('#email').addClass('input-error');
						}
					}
				});
			}
		}
		
		if (error) {
			display_text_message('Не введены обязательные поля.', 400, 200, 'Ошибка');
			spinner24.stop();
			return;
		}
		
		$.ajax({
			url: baseURL + 'ajax/update_user', 
			async: true,
			type: 'POST',
			dataType: 'html',
			data: {
				user_id: user_id,
				username: username,
				email: email,
				password: password,
				send_password: send_password,
				activated: activated,
				banned: banned,
				ban_reason: ban_reason,
				role: role
			},
			success: function(response)
			{
				window.location = baseURL + 'admin/users/';
			},
			complete: function() {
				spinner24.stop();
			}
		});
		
	});
	
	refreshEventHandlers();

    $('.cars-requests').on('click', function() {
        $('#carRequestsDlg').dialog('open');
    });

    $('.fnRemoveRequest').on('click', function() {
        if (confirm('Удалить запрос пользователя?')) {
			var row = $(this).parents('tr');
            $(this).parent().prepend(loader12);
            spinner12 = new Spinner(loaderOpts12).spin();
            $('.loader-12').css({
                'margin':'0 15px 0',
                'position': 'relative'
            }).append(spinner12.el);

            var $id = $(this).parent().attr('data-id');

            $.ajax({
                url: baseURL + 'ajax/delete_car_request',
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {
                    id: $id
                },
                success: function(response) {
                    row.remove();
                    if ($('.cars-requests-content table tr').length == 0) {
                        $('#carRequestsDlg').dialog('close');
                        $('.cars-requests').remove();
                    } else {
                        $('.cars-requests').html('Запросы (' + $('.cars-requests-content table tr').length + ')');
                    }
                },
                complete: function() {
                    spinner12.stop();
                    $('.loader-12').remove();
                }
            });
        }
    });

    $('.fnApproveRequest').on('click', function() {
        var row = $(this).parents('tr');
        $(this).parent().prepend(loader12);
        spinner12 = new Spinner(loaderOpts12).spin();
        $('.loader-12').css({
            'margin':'0 15px 0',
            'position': 'relative'
        }).append(spinner12.el);

        var $id = $(this).parent().attr('data-id');

        $.ajax({
            url: baseURL + 'ajax/approve_car_request',
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {
                id: $id
            },
            success: function(response) {
                row.remove();
                if ($('.cars-requests-content table tr').length == 0) {
                    $('#carRequestsDlg').dialog('close');
                    $('.cars-requests').remove();
                } else {
                    $('.cars-requests').html('Запросы (' + $('.cars-requests-content table tr').length + ')');
                }
            },
            complete: function() {
                spinner12.stop();
                $('.loader-12').remove();
            }
        });
    });
});

function display_prompt_message(message, title, func, data, width, height, resizable, buttons) {
	if (!title) title = '';
    if (!width) width = 400;
    if (!height) height = 200;
    if (!resizable) resizable = false;
    if (buttons) 
    {
        $("#promptDlg").dialog('option','buttons', buttons);
    }
    else
	{
    	buttons = new Array();
		buttons[0] = {
			text:"Ok",
			click:function () {
				window[func](data);
				$(this).dialog('close');
			}
		}
		buttons[1] = {
			text:"Отмена",
			click:function () {
				$(this).dialog('close');
			}
		}
		$("#promptDlg").dialog("option", "buttons", buttons);
	}
	$("#promptDlg").dialog('option','title', title);
    $("#promptDlg").dialog('option','height', height);
    $("#promptDlg").dialog('option','width', width);
    $("#promptDlg").dialog('option','resizable', resizable);
	
    $("#promptDlg").html(message);
    $("#promptDlg").dialog('open');
}

var adminManufacturerChange = function (elem, type) {
    var model = $('#model' + type),
        engine = $('#engine' + type),
        car = $('#car' + type);

    model.hide();
    engine.hide();
    car.hide();

	if (elem.hasClass('news-manufacturers-list')) {
		if (elem.val() != 0) {
			$('#addNewsModel').fadeIn();
		} else {
			$('#addNewsModel').fadeOut();
		}
	}

    if (elem.val() != 0) {
        ajaxSpinner.add($(elem).parent(), 'medium-dark', 'append', {'right': 35, 'top': 17, 'position': 'absolute'});

        $.ajax({
            url: baseURL + 'admin/model/get-models/?id=' + elem.val(),
            async: true,
            type: 'POST',
            dataType: 'html',
            success: function (response) {
                var data = $.parseJSON(response),
                    list = [],
                    listPopular = [],
                    maxChars = 30,
                    models,
                    bodyName = '';

                models = data.models;

                $('#model' + type + ' option[value!="0"]').remove();

                for (var i = 0; i < models.length; ++i)
                {
                    bodyName = '';

                    if (models[i].body && models[i].body.body_name.length) {
                        bodyName = ' ' + models[i].body.body_name;
                    }

                    if (models[i].is_popular == 1) {
                        listPopular.push('<option value="' + models[i].id + '">' + models[i].name + bodyName + '</option>');
                    }

                    if (models[i].name.length > maxChars) {
                        maxChars = models[i].name.length;
                    }

                    list.push('<option value="' + models[i].id + '">' + models[i].name + bodyName + '</option>');
                }

                model.append(listPopular);
                if (listPopular.length) {
                    model.append('<option value="-1" disabled="disabled">' + str_repeat('-', maxChars - 7) + '</option>');
                }
                model.append(list).show();

            },
            complete: function () {
                ajaxSpinner.stop();
            }
        });
    }
};

var adminModelChange = function (elem, type) {
    var engine = $('#engine' + type),
        car = $('#car' + type);

    engine.hide();
    car.hide();

    if (elem.val() != 0 && ! elem.hasClass('news-models-list')) {
        ajaxSpinner.add($(elem).parent(), 'medium-dark', 'append', {'right': 35, 'top': 17, 'position': 'absolute'});

        $.ajax({
            url: baseURL + 'admin/engine/get-engines/?id=' + elem.val(),
            async: true,
            type: 'POST',
            dataType: 'html',
            success: function(response) {
                var data = $.parseJSON(response),
                    engines;

                engines = data.engines;

                $('#engine' + type + ' option[value!="0"]').remove();
                for (var i = 0; i < engines.length; ++i)
                {
                    engine.append('<option value="' + engines[i].id + '">'
                        + engines[i].engine_name + '</option>'
                    );
                }
                engine.show();

            },
            complete: function() {
                ajaxSpinner.stop();
            }
        });
    }
};

function adminEngineChange(elem, type)
{
	$('#car' + type).hide();
	
	if (elem.val() != 0)
	{
		$('#car' + type).show();
	}
}

function refreshEventHandlers()
{
	$('.fnManufacturerDlgOpen').on('click', function(e) {
		e.preventDefault();
		$('#manufacturerDlg').dialog('open');
		var id = $(this).metadata().id;
		var name = $(this).metadata().name;
		var isPopular = $(this).metadata().isPopular;
		$('[name="manufacturer_id"]').val(id);
		$('[name="manufacturer_name"]').val(name);
		$('#is_popular').jqxCheckBox({checked: isPopular});
	});
	
	$('.fnDeleteManufacturer').on('click', function(e) {
		e.preventDefault();
		display_prompt_message(
			'Вы действительно хотите удалить производителя?', 
			'Удаление производителя',
			'deleteManufacturer', 
			$(this)
		);
	});
	
	$('.fnModelDlgOpen').on('click', function(e) {
		e.preventDefault();
		$('#modelDlg').dialog('open');
		var id = $(this).metadata().id;
		var manufacturer = $(this).metadata().manufacturer;
		var modelBody = $(this).metadata().body_id;
		var name = $(this).metadata().name;
		$('[name="model_id"]').val(id);
		$('[name="model_name"]').val(name);
		$('[name="model_manufacturer"]').val(manufacturer);
		$('[name="model_manufacturer_name"]').val($('[name="model_manufacturer"] option:selected').text());
		$('[name="model_body"]').val(modelBody);
		$('[name="model_body_name"]').val($('[name="model_body"] option:selected').text());
		var image = $(this).metadata().image;
		if (image.length)
		{
			$('#model_image_src').show().append('<img src="'+image+'" />');
			$('#add_image').hide();
			$('#remove_image').show();
			$('[name=model_image]').val(image);
			$('#model_image_container').hide();
		}
		var isPopular = $(this).metadata().isPopular;
		$('#is_popular').jqxCheckBox({checked: isPopular});
	});
	
	$('.fnDeleteModel').on('click', function(e) {
		e.preventDefault();
		display_prompt_message(
			'Вы действительно хотите удалить модель?', 
			'Удаление модели',
			'deleteModel', 
			$(this)
		);
	});
	
	$('.fnBodyDlgOpen').on('click', function(e) {
		e.preventDefault();
		$('#bodyDlg').dialog('open');
		var id = $(this).metadata().id;
		var name = $(this).metadata().name;
		$('[name="body_id"]').val(id);
		$('[name="body_name"]').val(name);
	});
	
	$('.fnDeleteBody').on('click', function(e) {
		e.preventDefault();
		display_prompt_message(
			'Вы действительно хотите удалить кузов?', 
			'Удаление кузова',
			'deleteBody', 
			$(this)
		);
	});
	
	$('.fnEngineDlgOpen').on('click', function(e) {
		e.preventDefault();
		$('#engineDlg').dialog('open');
		var id = $(this).metadata().id;
		var name = $(this).metadata().name;
		var model = $(this).metadata().model;
		var manufacturer = $(this).metadata().manufacturer;
		$.ajax({
			url: baseURL + 'ajax/get_manufacturer_models', 
			async: true,
			type: 'POST',
			dataType: 'html',
			data: {
				manufacturer: manufacturer
			},
			success: function(response)
			{
				var data = $.parseJSON(response);
				if (data.error)
					display_text_message(data.error, 400, 200, 'Ошибка');
				else
				{
					$('#engine_model option[value!="0"]').remove();
					for (var i = 0; i < data.length; ++i)
					{
						if (data[i].is_popular != 0) {
							var body_name = data[i].body_name ? ' ' + data[i].body_name : '';
							$('#engine_model').append('<option value='+data[i].id+'>'+data[i].name+body_name+'</option>');
						}
					}
					if ($('#engine_model option[value!="0"]').length)
					{
						$('#engine_model').append('<option value="-1">---------------</option>');
					}
					for (var i = 0; i < data.length; ++i)
					{
						var body_name = data[i].body_name ? ' ' + data[i].body_name : '';
						$('#engine_model').append('<option value='+data[i].id+'>'+data[i].name+body_name+'</option>');
					}
					$('#engine_model').attr('disabled', false);
					$('#engine_model').val(model);
				}
			},
			complete: function() {
				$('#ajaxLoader24').remove();
			}
		});
		$('[name="engine_id"]').val(id);
		$('[name="engine_name"]').val(name);
		$('[name="engine_manufacturer"]').val(manufacturer);
		$('[name="engine_list[]"], .engines-checkbox label').attr('disabled', true);
		$('.engines-checkbox').addClass('disabled');
	});
	
	$('.fnDeleteEngine').on('click', function(e) {
		e.preventDefault();
		display_prompt_message(
			'Вы действительно хотите удалить двигатель?', 
			'Удаление двигателя',
			'deleteEngine', 
			$(this)
		);
	});
}

function saveManufacturer()
{
	error = false;
	var id = $('[name="manufacturer_id"]').val();
	var name = $('[name="manufacturer_name"]').val();
	var isPopular = $('#is_popular').jqxCheckBox('checked');
	
	if (name.length < 2)
	{
		error = true;
		$('[name="manufacturer_name"]').addClass('input_error');
		$('[name="manufacturer_name"]').next('p').addClass('fnShowErrorMsg');
	}
	
	if (id > 0)
	{
		var action = 'update_manufacturer';
	}
	else
	{
		var action = 'add_manufacturer';
	}
	
	if (error) return;
	
	$('.manufacturerDlg .ui-dialog-buttonset').prepend(ajaxLoader24);
	
	$.ajax({
		url: baseURL + 'ajax/' + action, 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			id: id,
			name: name,
			isPopular: isPopular
		},
		success: function(response)
		{
			var data = $.parseJSON(response);
			if (data.error)
			{
				display_text_message(data.error, 400, 200, 'Ошибка');
			}
			else
			{
				location.reload();
			}
		},
		complete: function() {
			$('#ajaxLoader24').remove();
		}
	});
}

function deleteManufacturer(elem)
{
	var id = elem.metadata().id;
	
	$(elem).after(ajaxLoader24);
	$('#ajaxLoader24').css('float', 'none').css('width', 12).css('height', 12);

	$.ajax({
		url: baseURL + 'ajax/delete_manufacturer', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			id: id
		},
		success: function(response)
		{
			location.reload();
		},
		complete: function() {
			$('#ajaxLoader24').remove();
		}
	});
}

function saveModel()
{
	error = false;
	var id = $('[name="model_id"]').val();
	var manufacturer = $('[name="model_manufacturer"]').val();
	var manufacturerName = $('[name="model_manufacturer_name"]').val();
	var name = $('[name="model_name"]').val();
	var image = $('[name="model_image"]').val();
	var modelBody = $('[name="model_body"]').val();
	var bodyName = $('[name="model_body_name"]').val();
	var isPopular = $('#is_popular').jqxCheckBox('checked');
	
	if (manufacturer == 0)
	{
		error = true;
		$('[name="model_manufacturer"]').parent().addClass('input_error');
		$('[name="model_manufacturer"]').parent().next('p').addClass('fnShowErrorMsg');
	}
	
	if (name.length < 2)
	{
		error = true;
		$('[name="model_name"]').addClass('input_error');
		$('[name="model_name"]').next('p').addClass('fnShowErrorMsg');
	}
	
	if (modelBody == 0)
	{
		error = true;
		$('[name="model_body"]').parent().addClass('input_error');
		$('[name="model_body"]').parent().next('p').addClass('fnShowErrorMsg');
	}
	
	if (id > 0)
	{
		var action = 'update_model';
	}
	else
	{
		var action = 'add_model';
	}
	
	if (error) return;
	
	$('.modelDlg .ui-dialog-buttonset').prepend(ajaxLoader24);
	
	$.ajax({
		url: baseURL + 'ajax/' + action, 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			id: id,
			manufacturer: manufacturer,
			manufacturerName: manufacturerName,
			name: name,
			image: image,
			modelBody: modelBody,
			bodyName: bodyName,
			isPopular: isPopular
		},
		success: function(response)
		{
			var data = $.parseJSON(response);
			if (data.error)
			{
				display_text_message(data.error, 400, 200, 'Ошибка');
			}
			else
			{
				location.reload();
			}
		},
		complete: function() {
			$('#ajaxLoader24').remove();
		}
	});
}

function deleteModel(elem)
{
	var id = elem.metadata().id;
		
	elem.after(ajaxLoader24);
	$('#ajaxLoader24').css('float', 'none').css('width', 12).css('height', 12);

	$.ajax({
		url: baseURL + 'ajax/delete_model', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			id: id
		},
		success: function(response)
		{
			location.reload();
		},
		complete: function() {
			$('#ajaxLoader24').remove();
		}
	});
}

function saveBody()
{
	error = false;
	var id = $('[name="body_id"]').val();
	var name = $('[name="body_name"]').val();
	
	if (name.length < 2)
	{
		error = true;
		$('[name="body_name"]').addClass('input_error');
		$('[name="body_name"]').next('p').addClass('fnShowErrorMsg');
	}
	
	if (id > 0)
	{
		var action = 'update_body';
	}
	else
	{
		var action = 'add_body';
	}
	
	if (error) return;
	
	$('.bodyDlg .ui-dialog-buttonset').prepend(ajaxLoader24);
	
	$.ajax({
		url: baseURL + 'ajax/' + action, 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			id: id,
			name: name
		},
		success: function(response)
		{
			var data = $.parseJSON(response);
			if (data.error)
			{
				display_text_message(data.error, 400, 200, 'Ошибка');
			}
			else
			{
				location.reload();
			}
		},
		complete: function() {
			$('#ajaxLoader24').remove();
		}
	});
}

function deleteBody(elem)
{
	var id = elem.metadata().id;
	
	$(elem).after(ajaxLoader24);
	$('#ajaxLoader24').css('float', 'none').css('width', 12).css('height', 12);

	$.ajax({
		url: baseURL + 'ajax/delete_body', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			id: id
		},
		success: function(response)
		{
			location.reload();
		},
		complete: function() {
			$('#ajaxLoader24').remove();
		}
	});
}

function saveEngine()
{
	error = false;
	var id = $('[name="engine_id"]').val();
	var model = $('[name="engine_model"]').val();
	var name = $('[name="engine_name"]').val();
	
	if (model == 0)
	{
		error = true;
		$('[name="engine_model"]').parent().addClass('input_error');
		$('[name="engine_model"]').parent().next('p').addClass('fnShowErrorMsg');
	}
	
	var eList = $('[name="engine_list[]"]:checked');
	
	if (id > 0)
	{
		var action = 'update_engine';
		
		if (name.length < 3)
		{
			error = true;
			$('[name="engine_name"]').addClass('input_error');
			$('[name="engine_name"]').next('p').addClass('fnShowErrorMsg');
		}
	}
	else
	{
		var action = 'add_engine';
	
		if (name.length < 3 && eList.length == 0)
		{
			error = true;
			$('[name="engine_name"]').addClass('input_error');
			$('[name="engine_name"]').next('p').addClass('fnShowErrorMsg');
		}
	}
	
	if (error) return;
	
	var engineList = [];
	eList.each(function() {
		engineList.push($(this).val());
	});
	
	$('.modelDlg .ui-dialog-buttonset').prepend(ajaxLoader24);
	
	$.ajax({
		url: baseURL + 'ajax/' + action, 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			id: id,
			model: model,
			model_name: $('[name="engine_model"] option:selected').text(),
			manufacturer_name: $('[name="engine_manufacturer"] option:selected').text(),
			name: name,
			engineList: engineList
		},
		success: function(response)
		{
			var data = $.parseJSON(response);
			if (data.error)
			{
				display_text_message(data.error, 400, 200, 'Ошибка');
			}
			else
			{
				location.reload();
			}
		},
		complete: function() {
			$('#ajaxLoader24').remove();
		}
	});
}

function deleteEngine(elem)
{
	var id = elem.metadata().id;
		
	elem.after(ajaxLoader24);
	$('#ajaxLoader24').css('float', 'none').css('width', 12).css('height', 12);

	$.ajax({
		url: baseURL + 'ajax/delete_engine', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			id: id
		},
		success: function(response)
		{
			location.reload();
		},
		complete: function() {
			$('#ajaxLoader24').remove();
		}
	});
}

function saveCar(title)
{
	$('#carDlg').dialog('option', 'title', title);
	$('#carDlg h2').html(
		manufacturerName + ' ' + modelName + ', ' + engineName
	);
	$('#carDlg').dialog('open');
	
	$('#carDlg h2').after(ajaxLoader24);
	$('#ajaxLoader24').css({
		'width':21,
		'height': 21,
		'margin-top':-2
	});
	$.ajax({
		url: baseURL + 'ajax/get_tech_options_tree_form', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			manufacturerID: manufacturerID,
			modelID: modelID,
			engineID: engineID
		},
		success: function(response)
		{
			if (isJson(response))
			{
				var data = $.parseJSON(response);
				if (data.error)
				{
					display_text_message(data.error, 400, 200, 'Ошибка');
					$('#carDlg').dialog('close');
				}
				else
				{
					$('#car-accordion-wrapper').html(data.tree_form);
					$('#carAccordion').accordion({
						heightStyle: "content"
					}).accordion('refresh');
					
					$('.option-input').jqxInput({
						theme: 'web',
						height: 25, 
						width: '97%', 
					});
					
					$('#cloneList option[value!=0]').remove();
					if (data.clone_list.length > 0)
					{
						$('.clone-wrapper').show();
					}
					else
					{
						$('.clone-wrapper').hide();
					}
					for (var i = 0; i < data.clone_list.length; ++i)
					{
						$('#cloneList').append('<option value="' + data.clone_list[i].car_id + '">' + 
							data.clone_list[i].manufacturer_name + ' ' + 
							data.clone_list[i].model_name + ', ' + 
							data.clone_list[i].engine_name
						);
					}
				}
			}
		},
		complete: function() {
			$('#ajaxLoader24').remove();
		}
	});
}

function showModelImageCropper(elem, response)
{
	var data = $.parseJSON(response);
	$(elem + " form[target^='iframe']")[0].reset();
	$('#ajaxLoader24').remove();
	if (data)
	{
		$('#cropperModelDlg').dialog('open');
		$('#cropped_img').attr('src', baseURL + 'uploads/temp/' + data.upload_data.file_name).cropsy();
	}
}

function cropModelImage() 
{
	var $image = $('#cropped_img'),
		src    = $image.attr('src'),
		imageTop = parseInt($image.css('top')) - 40,
		imageLeft = parseInt($image.css('left')) - 40;
	
	var imageData = {
		img_src: src,
		top: imageTop,
		left: imageLeft,
		cropHeight: $image.height(),
		cropWidth: $image.width()
	} 

	$('.cropperModelDlg .ui-dialog-buttonset').prepend(ajaxLoader24);
	
	$.ajax({
		url: baseURL + 'uploader/crop', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			src: src,
			left: imageLeft,
			top: imageTop,
			imgHeight: $image.height(),
			imgWidth: $image.width(),
			cropWidth: 480,
			cropHeight: 270
		},
		success: function(response)
		{
			
			var data = $.parseJSON(response);
			if (data.error)
				display_text_message(data.error, 400, 200, 'Ошибка');
			else
			{
				$(elemUpload).show().append('<img src="' + baseURL + 'uploads/temp/' + data.src + '" />');
				$('#model_image_container').hide();
				$('#remove_image').show();
				$('#model_image').val('/uploads/temp/' + data.src);
			}
			$('#cropperModelDlg').dialog('close');
		},
		complete: function() {
			$('#ajaxLoader24').remove();
		}
	});
}

function searchObjects(obj, key, val) 
{
    var newObj = []; 
    $.each(obj, function()
    {
        var testObject = this; 
        $.each(testObject, function(k,v)
        {
            
            if(stripos(v, val) !== false && k == key)
            {
                newObj.push(testObject);
            }
        });
    });

    return newObj;
}

function showAdminLoader(el)
{
	$(el).prepend(ajaxLoaderTransp24);
	$('#ajaxLoaderTransp24').css({
		position: 'absolute',
		left: 105,
		top: 3
	});
}

function getAdminManufacturerEngines(manufacturer, engineList)
{
	engineList = engineList || false;
	
	$.ajax({
		url: baseURL + 'ajax/get_manufacturer_models', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			manufacturer: manufacturer,
			engineList: engineList
		},
		success: function(response)
		{
			var data = $.parseJSON(response);
			if (data.error)
				display_text_message(data.error, 400, 200, 'Ошибка');
			else
			{
				$('#engine_model option[value!="0"]').remove();
				$('.engines-checkbox').html('');
				
				var list = [],
					listPopular = [],
					maxChars = 30;
				
				var models = data.models;
				var engines = data.engines;
				
				for (var i = 0; i < models.length; ++i)
				{
					var name = models[i].name;
					if (models[i].body_name != null)
					{
						name += ' ' + models[i].body_name;
					}
					if (models[i].is_popular == 1) listPopular.push('<option value="'+models[i].id+'">' + name + '</option>');
					if (name.length > maxChars) maxChars = name.length;
					list.push('<option value="'+models[i].id+'">' + name + '</option>');
				}
				
				$('#engine_model').append(listPopular);
				if (listPopular.length)
				{
					$('#engine_model').append('<option value="-1" disabled="disabled">' + str_repeat('-', maxChars - 7) + '</option>');
				}
				$('#engine_model').append(list);
				$('#engine_model').attr('disabled', false);
				
				if ($('#engine_model option[value="' + $('[name="engine_models"]').val() + '"]').length)
				{
					$('[name="engine_model"]').val($('[name="engine_models"]').val());
				}
				
				var tmp = [];
				for (var i = 0; i < engines.length; ++i)
				{
					var e_name = trim(engines[i].engine_name);
					if ($.inArray(e_name, tmp) == -1)
					{
						$('.engines-checkbox').append('<p><input type="checkbox" name="engine_list[]" value="'
							+ e_name + '" id="engine_' + engines[i].id + '" style=""> <label for="engine_'
							+ engines[i].id + '">' + e_name + '</label></p>');
						tmp.push(e_name);
					}
				}
			}
		},
		complete: function() {
			$('#ajaxLoader24').remove();
		}
	});
}

function showDefaultForoCropper(elem, response)
{
    var data = $.parseJSON(response);
    $(elem + " form[target^='iframe']")[0].reset();
    $('#ajaxLoader24').remove();
    if (data)
    {
        $('#cropperDefaultFotoDlg').dialog('open');
        $('#cropped_default_foto_img').attr('src', baseURL + 'uploads/temp/' + data.upload_data.file_name).cropsy();
    }
}

function cropDefaultFoto()
{
    var $image = $('#cropped_default_foto_img'),
        src    = $image.attr('src'),
        imageTop = parseInt($image.css('top')) - 40,
        imageLeft = parseInt($image.css('left')) - 40;

    var imageData = {
        img_src: src,
        top: imageTop,
        left: imageLeft,
        cropHeight: $image.height(),
        cropWidth: $image.width()
    };

    $('.cropperDefaultFotoDlg .ui-dialog-buttonset').prepend(ajaxLoader24);

    $.ajax({
        url: baseURL + 'uploader/crop',
        async: true,
        type: 'POST',
        dataType: 'html',
        data: {
            src: src,
            left: imageLeft,
            top: imageTop,
            imgHeight: $image.height(),
            imgWidth: $image.width(),
            cropWidth: 480,
            cropHeight: 270
        },
        success: function(response)
        {
            var data = $.parseJSON(response);
            if (data.error)
                display_text_message(data.error, 400, 200, 'Ошибка');
            else
            {

                $.ajax({
                    url: baseURL + 'ajax/move_default_foto',
                    async: true,
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        src: data.src
                    },
                    success: function(response)
                    {
                        var data = $.parseJSON(response);
                        if (data.error)
                            display_text_message(data.error, 400, 200, 'Ошибка');
                        else
                        {
                            $('#default_foto_src').html('').append('<img src="' + baseURL + 'uploads/' + data.src + '?' + new Date().getTime() + '" />');
                        }
                    }
                });
            }
            $('#cropperDefaultFotoDlg').dialog('close');
        },
        complete: function() {
            $('#ajaxLoader24').remove();
        }
    });
}

var ajaxForm = function($form, onComplete) {
    var iframe;

    if (!$form.attr('target')) {
        //create a unique iframe for the form
        iframe = $("<iframe></iframe>").attr('name', 'ajax_form_' + Math.floor(Math.random() * 999999)).hide().appendTo($('body'));
        $form.attr('target', iframe.attr('name'));

        $form.append('<input type="hidden" name="isAjax" value="1">');
    }

    $form.submit();

    if (onComplete) {
        iframe = iframe || $('iframe[name=" ' + $form.attr('target') + ' "]');
        iframe.load(function () {
            //get the server response
            var response = iframe.contents().find('body').text();
            window[onComplete](response);
        });
    }
};