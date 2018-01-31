var emailPattern = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
    cropperImage = $('#cropped_img'),
    cropperOptions = {
        minCropBoxWidth: 480,
        minCropBoxHeight: 270,
        viewMode: 1,
        aspectRatio: 16/9,
        preview: '.img-preview'
    },
    messageDialog = $('#messageDlg'),
    compareCompareUpload,
    isAdmin = false,
    newsCount,
    newsPage,
    newsPerPage,
    $body = $('body'),

    /**
     *
     * @type {{}}
     */
	Main = {
        cropperImage: null,
        cropperCallback: 'Main.cropImage',
        uploadHandler: null,

        /**
         * Init of object
         */
        init: function () {
            var self = this;

            self.cropperImage = $('#cropped_img');

            $.getScript('/js/user.js', function () {
                User.init();
            });

            self.attachEvents();
        },

        /**
         * Attach events
         */
        attachEvents: function () {
            // Cropper Methods
            $(document.body).on('click', '[data-crop]', function () {
                var data = $(this).data(),
                    $target,
                    result;

                if (data.crop) {
                    data = $.extend({}, data); // Clone a new one

                    if (typeof data.target !== 'undefined') {
                        $target = $(data.target);

                        if (typeof data.option === 'undefined') {
                            try {
                                data.option = JSON.parse($target.val());
                            } catch (e) {
                                //console.log(e.message);
                            }
                        }
                    }

                    result = Main.cropperImage.cropper(data.crop, data.option);

                    if (data.crop === 'getData') {
                        Main.getFunction(Main.cropperCallback)(result);
                    }

                    if ($.isPlainObject(result) && $target) {
                        try {
                            $target.val(JSON.stringify(result));
                        } catch (e) {
                            //console.log(e.message);
                        }
                    }
                }
            });

            // Notification handler
			$('.user-notification').on('shown.bs.dropdown', function () {
			    if ($(this).find('.notification-counter').length) {
                    $.ajax({
                        url: baseURL + 'profile/mark-notification',
                        async: true,
                        type: 'POST',
                        dataType: 'html'
                    });
                }

				$(this).find('.notification-counter').remove();
            }).on('hidden.bs.dropdown', function () {
                $('.new-notification').removeClass('new-notification');
            });
        },

        /**
         *
         * @param string
         * @return {*}
         */
        getFunction: function(string) {
            var i,
                scope = window,
                scopeSplit = string.split('.');

            for (i = 0; i < scopeSplit.length - 1; i++)
            {
                scope = scope[scopeSplit[i]];
                if (scope === undefined) return;
            }

            return scope[scopeSplit[scopeSplit.length - 1]];
        }
	};

$(function() {
	Main.init();

    showVisible();

    $body.on("ajaxSend", function(elm, xhr, s) {
        if (s.type === "POST") {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr("content"));
        }
    });

	$body.on('click', 'a', function(e) {
		if (stripos($(this).attr('href'), '#') !== false) e.preventDefault();
	});

    $body.on('click', '.btn', function() {
        $(this).blur();
    });

    $body.on('mouseout', '.btn', function() {
        $(this).blur();
    });

	$('.top-button').css({
		'width': function() {
			return ($body.width() - $('.container').width()) / 2 - 20;
		}
	});

    updateTooltip();

    var heightWrapper = $('.height-wrapper');

	$(window).on('resize', function() {
        showVisible();

        if (heightWrapper.length) {
            heightWrapper.height('auto');

            var container = heightWrapper.parent('.container'),
                footer = $('footer'),
                initHeight = heightWrapper.actual('height');

            if (container.actual('height') < $body.height()) {
                heightWrapper.height($body.height() - container.actual('height') + initHeight - footer.actual('outerHeight', {includeMargin: true}));
            }
        }

        var catalogViewContent = $('.catalog-view-content'),
            catalogViewTitle = $('.catalog-view-title');
        if (catalogViewContent.length) {
            catalogViewContent.height('auto');
            if (catalogViewContent.actual('height') + catalogViewTitle.actual('outerHeight', {includeMargin: true}) < heightWrapper.actual('height')) {
                catalogViewContent.height(heightWrapper.actual('height') - catalogViewTitle.actual('outerHeight', {includeMargin: true}));
            }
        }

		var topWidth = ($body.width() - $('.container').width()) / 2 - 20;
		var topButton = $('.top-button');
		if (topWidth < 110) {
			$('.top-button .top-button-text').hide();
			topButton
                .addClass('top-button-small')
                .css({
                    'width': 39
                });
		} else {
			$('.top-button .top-button-text').show();
			topButton
                .removeClass('top-button-small')
                .css({
                    'width': topWidth
                });
		}
	});

    $(window).trigger('resize');

	$(window).on('scroll', function() {
        var topButton = $('.top-button-inner.top-button');
		if ($(this).scrollTop() >= 18) {
			topButton
                .show()
                .css({
                    'opacity': function() {
                        return ($(window).scrollTop() - 18) / 100;
                    }
                });
		} else {
			topButton
                .hide()
                .css({
                    'opacity': 0
                });
		}
	});

	$('.top-button-inner.top-button').on('click', function() {
		if ($.browser.mozilla) {
			$('html').animate({scrollTop: 0}, 1000);
		} else {
			$body.animate({scrollTop: 0}, 1000);
		}
	});

	/*
	$('#pleaseWaitDlg').dialog({
		autoOpen: false,
		closeText: 'закрыть',
		dialogClass: 'pleaseWaitDlg',
		resizable: false,
		draggable: false,
		height: 200,
		width:245,
		modal: true
    });
	
	$('#pleaseWaitDlg').dialog('open');
	$('#pleaseWaitDlg').dialog('close');
	
	$('#cropperDlg').dialog({
		autoOpen: false,
		closeText: 'закрыть',
		dialogClass: 'cropperDlg',
		height: 500,
		width:582,
		modal: true,
		resizable: false,
		title:'Загрузка фото',
		buttons: {
			'Сохранить': function() {
				cropImage();
			},
			'Отменить': function() {
				$(this).dialog('close');
			}
		},
		open: function() {
			$('.ui-dialog .ui-dialog-buttonpane button').blur();
		},
		close: function(event, ui) {
			$('#cropperDlg .viewport').removeAttr('style');
			$('#cropped_img').remove();
			$('#cropperDlg .viewport').prepend('<img id="cropped_img" />');
		}
    });
	
	*/

    $('[data-video]').each(function () {
        var $this = $(this);

        ajaxSpinner.add($this, 'medium', 'append', {'margin-left': '20px', 'position': 'relative'});

        $.ajax({
            url: baseURL + 'video/show',
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {
                video: $this.data('video')
            },
            success: function(response) {
                $this.html($.parseJSON(response));
                $(window).trigger('resize');
            }
        });
    });
	
	$('#authOpen, .authOpen').on('click', function(e) {
		e.preventDefault();
		$('.fnErrorMsg').removeClass('fnShowErrorMsg');

        $('.auth-reset-form, .auth-register-form').slideUp();
		$('.auth-sign-form').slideDown();

        $('.resetBtnGroup, .signupBtnGroup').hide();
        $('.signinBtnGroup').show();

        var authDlg = $('#authDlg');
        authDlg.find('input').removeClass('input_error');
        authDlg.find('.modal-title').text('Авторизация');
        authDlg.modal('show');
	});
	
	$('#signin_lost_pass, #register_lost_pass').on('click', function() {
		$('.auth-sign-form, .auth-register-form').slideUp();
		$('.auth-reset-form').slideDown();

        $('.signinBtnGroup, .signupBtnGroup').hide();
        $('.resetBtnGroup').show();

        var authDlg = $('#authDlg');
        authDlg.find('.modal-title').text('Восстановление пароля');
	});

	$('#lost_pass_signin, #register_signin').on('click', function() {
        $('.auth-reset-form, .auth-register-form').slideUp();
        $('.auth-sign-form').slideDown();

        $('.resetBtnGroup, .signupBtnGroup').hide();
        $('.signinBtnGroup').show();

        var authDlg = $('#authDlg');
        authDlg.find('.modal-title').text('Авторизация');
	});
	
	$('#signin_register, #lost_pass_register').on('click', function() {
        $('.auth-reset-form, .auth-sign-form').slideUp();
        $('.auth-register-form').slideDown();

        $('.resetBtnGroup, .signinBtnGroup').hide();
        $('.signupBtnGroup').show();

        var authDlg = $('#authDlg');
        authDlg.find('.modal-title').text('Регистрация');
	});
	
	$('#authDlg').find('input').on('focus', function() {
		$(this).removeClass('input_error');
		$(this).next('p').removeClass('fnShowErrorMsg');
		$(this).next('span').next('p').removeClass('fnShowErrorMsg');
	});

    $('.btnSigin').on('click', function() {
        signinUser();
    });

    $('#signin_password, #signin_login').on('keyup', function (e) {
        if (e.which === 13) {
            signinUser();
        }
    });

    $('.btnRecover').on('click', function () {
        resetPassword();
    });

    $('.btnSignup').on('click', function () {
        registerUser();
    });

	$('#signOff').on('click', function(e) {
        e.preventDefault();

	    ajaxSpinner.add($('.login-link'), 'small', 'append', {'margin-left': 5});

		$.ajax({
			url: baseURL + 'sign-out',
			async: true,
			type: 'POST',
			dataType: 'html',
			data: {},
			success: function() {
				location.reload();
			}
		});
	});
	
	$('#closeFlashMsg').on('click', function() {
		$('[class*="flash_msg_"]').slideUp();
	});
	
	$('.loginVK').on('click', function() {
	    Comparison.prepareData();
	    Comparison.saveData();

		window.location = vkAuthUrl;
	});
	
	$('.loginFB').on('click', function() {
        Comparison.prepareData();
        Comparison.saveData();

		window.location = fbAuthUrl;
	});
	
	$('.loginTW').on('click', function() {
        Comparison.prepareData();
        Comparison.saveData();

		window.location = twAuthUrl;
	});
	
	$('.loginGoogle').on('click', function() {
        Comparison.prepareData();
        Comparison.saveData();

		window.location = googleAuthUrl;
	});
	
	$body.on('focus', '.input_error', function() {
		$(this).next('p').removeClass('fnShowErrorMsg');
		$(this).removeClass('input_error');
	});
	
	$('.btnLoadMoreNews').on('click', function() {
	    var $this = $(this);

        ajaxSpinner.button($this);

		$.ajax({
			url: baseURL + 'news/get-news',
			async: true,
			type: 'POST',
			dataType: 'html',
			data: {
				page: newsPage
			},
			success: function(response) {
                var data = $.parseJSON(response);

				if (data.error) {
                    showMessage(data.error, localizationMessages['error']);
				} else {
					$('.news-list-wrapper').append(data);
					++newsPage;
					if (Math.ceil(newsCount / newsPerPage) < newsPage) {
						$this.fadeOut();
					}
				}
			},
            complete: function() {
                ajaxSpinner.stop(true);
            }
		});
	});
	
	$('.catalog-btn-view-all a').on('click', function() {
		$('.catalog-popular-list, .catalog-all-list').slideToggle();

		if ($('.catalog-all-list').text() === 'популярные') {
			$(this).text('все марки');
		} else {
			$(this).text('популярные');
		}
	});
	
	$('.catalog-vs-list-item').on('mouseenter', function() {
		var value = $(this).find('p.catalog-model-comparison-value').attr('data-grade');
		
		$('.model-avg-main').hide();
		$('.model-compare-main').text(value).show();
	});
	
	$('.catalog-vs-list').on('mouseleave', function() {
		$('.model-avg-main').show();
		$('.model-compare-main').hide();
	});
});

//------------------------------------------
//Display general message to the user
//------------------------------------------
var showMessage = function(message, title) {
	if (!title) title = '';

    messageDialog.find('h4').html(title);
    messageDialog.find('.modal-body').html(message);
    messageDialog.modal('show');
};

var hideMessage = function() {
	messageDialog.modal('hide');
};

var updateTooltip = function () {
    $('[title]').attr('data-toggle', 'tooltip').attr('data-placement', 'top');
    $('[data-toggle="tooltip"]').tooltip();
};

var ajaxForm = function($form, onComplete) {
    var iframe;
    if (!$form.attr('target')) {
        //create a unique iframe for the form
        iframe = $("<iframe></iframe>").attr('name', 'ajax_form_' + Math.floor(Math.random() * 999999)).hide().appendTo($('body'));
        $form.attr('target', iframe.attr('name'));
    }

    $form.submit();

    iframe = iframe || $('iframe[name=" ' + $form.attr('target') + ' "]');
    iframe.load(function () {
        if (onComplete) {
            var response = iframe.contents().find('body').text();
            window[onComplete](response);
        }
    });
};

var registerUser = function() {
    $('.response-text').remove();
    
	var login = $('#register_login');
	var email = $('#register_email');
	var password = $('#register_password');
	var confirm_password = $('#register_confirm_password');
	var error = false;

    if (login.val().length < 3) {
		error = true;
		login.addClass('input_error');
		login.next('p').addClass('fnShowErrorMsg');
	}
	if (email.val().length <= 0 || !validateValueByPattern(email.val(), emailPattern)) {
		error = true;
		email.addClass('input_error');
		email.next('p').addClass('fnShowErrorMsg');
	}
	if (password.val().length < 4) {
		error = true;
		password.addClass('input_error');
		password.next('p').addClass('fnShowErrorMsg');
	}
	if (password.val() !== confirm_password.val()) {
		error = true;
		confirm_password.addClass('input_error');
		confirm_password.next('p').addClass('fnShowErrorMsg');
	}
	
	if (error) return;

    ajaxSpinner.dialog($('#authDlg').find('.signupBtnGroup'));
	
	$.ajax({
		url: baseURL + 'register', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
            User: {
                username: login.val(),
                email: email.val(),
                password: password.val(),
                confirmPassword: confirm_password.val()
            }
		},
		success: function(response) {
			var data = $.parseJSON(response);
			if (data.error) {
                $('#authDlg').find('.signupBtnGroup').prepend('<div class="response-text text-danger">' + data.error + '</div>');
            } else {
                Comparison.saveData();
                location.reload();
            }
		},
		complete: function() {
			ajaxSpinner.stop();
		}
	});
};

var signinUser = function() {
    $('.response-text').remove();

	var login = $('#signin_login');
	var password = $('#signin_password');
	var error = false;

    if (login.val().length < 3) {
		error = true;
		login.addClass('input_error');
		login.next('p').addClass('fnShowErrorMsg');
	}

	if (password.val().length < 4) {
		error = true;
		password.addClass('input_error');
		password.next('p').addClass('fnShowErrorMsg');
	}
	
	if (error) return;

	ajaxSpinner.dialog($('#authDlg').find('.signinBtnGroup'));

	var remember = $('#signin_remember').is(':checked') ? 1 : 0;

	$.ajax({
		url: baseURL + 'sign-in', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
            User: {
                username: login.val(),
                password: password.val(),
                rememberMe: remember
            }
		},
		success: function(response) {
			var data = $.parseJSON(response);

			if (data.error) {
                $('#authDlg').find('.signinBtnGroup').prepend('<div class="response-text text-danger">' + data.error + '</div>');
            } else {
			    Comparison.saveData();

                if (returnUrl.length) {
                    window.location.href = baseURL + trim(returnUrl, '/');
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

var resetPassword = function() {
    $('.response-text').remove();
    
	var email = $('#lost_pass_email');
	var error = false;

	if (email.val().length <= 0 || !validateValueByPattern(email.val(), emailPattern)) {
		error = true;
		email.addClass('input_error');
		email.next('p').addClass('fnShowErrorMsg').text('Неверный формат.');
	}
	
	if (error) return;

    ajaxSpinner.dialog($('#authDlg').find('.resetBtnGroup'));
	
	$.ajax({
		url: baseURL + 'reset-password',
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			email: email.val()
		},
		success: function(response) {
			var data = $.parseJSON(response);

            if (data.error) {
                $('#authDlg').find('.resetBtnGroup').prepend('<div class="response-text text-danger">' + data.error + '</div>');
			} else {
                $('#authDlg').find('.resetBtnGroup').prepend('<div class="response-text text-success">' + data.message + '</div>');
			}
		},
		complete: function() {
			ajaxSpinner.stop();
		}
	});
};

var showCropper = function(elem, response) {
    var data = $.parseJSON(response);
    $(elem + " form[target^='iframe']")[0].reset();
    ajaxSpinner.stop(true);

    if (data.resultCode === 'failed') {
        showMessage(data.result.imageFile.join('<br>'), localizationMessages['error']);
        return;
    }

    $('#cropperDlg').modal('show');
    $('.response-text').remove();
    cropperImage.cropper('destroy');
    cropperImage.attr('src', baseURL + 'uploads/temp/' + data.fileName.name).cropper(cropperOptions);
};

var cropImage = function(data) {
    $('.response-text').remove();

    var src = cropperImage.attr('src'),
        cropperDlg = $('#cropperDlg');

    ajaxSpinner.dialog(cropperDlg.find('.modal-footer .btn-group'));

    $.ajax({
        url: baseURL + 'uploader/crop',
        async: true,
        type: 'POST',
        dataType: 'html',
        data: {
            src: src,
            x: data.x,
            y: data.y,
            height: data.height,
            width: data.width,
            cropWidth: 480,
            cropHeight: 270,
            dirName: 'temp'
        },
        success: function(response) {
            var data = $.parseJSON(response);
            if (data.error) {
                cropperDlg.find('.modal-footer .btn-group').prepend('<div class="response-text text-danger">' + data.error + '</div>');
            } else {
                if (!isAdmin) {
                    $(Main.uploadHandler + ' img').attr('src', baseURL + data.src);
                } else {
                    $('#featured_image').attr('src', baseURL + data.src);
                    $('#no_featured_image, #featured_addition').hide();
                    $('#featured_removement, #featured_image, #featured_image_caption').show();
                    $('[name="featured_image"]').val(baseURL + data.src);
                }
                cropperDlg.modal('hide');
            }
        },
        complete: function () {
            ajaxSpinner.stop();
        }
    });
};

var manufacturerChange = function(holder) {
	var _photo = $('#' + holder + '_photo'),
		_image = $('#' + holder + '_image_container'),
        _time = $('#' + holder + '_time'),
        _manufacturer = $('#' + holder + '_manufacturer'),
        _model = $('#' + holder + '_model'),
        _engine = $('#' + holder + '_engine');

	_photo.hide();
	_image.hide();
	_time.addClass('disabled').val(0);
	
	$('.comparison-add-point-handler').removeClass('active');
	$('#comare_values').slideUp();
	
	if (parseInt(_manufacturer.val()) !== 0) {
	    ajaxSpinner.add(
	        _manufacturer,
            'medium',
            'before',
            {
                'top': 6,
                'right': 37
            }
        );

		$.ajax({
			url: baseURL + 'comparison/get-manufacturer-models',
			async: true,
			type: 'POST',
			dataType: 'html',
			data: {
                manufacturerId: _manufacturer.val()
			},
			success: function(response) {
				var data = $.parseJSON(response);
				if (data.error) {
				    showMessage(data.error, localizationMessages['error']);
                } else {
					$('#' + holder + '_model option[value!="0"]').remove();
					_model.append(data.models).removeClass('disabled');
					$('#'+holder+'_engine option[value!="0"]').remove();
					_engine.addClass('disabled');
				}

                $('.height-wrapper').height('auto');
                $(window).trigger('resize');
			},
			complete: function() {
				ajaxSpinner.stop();
			}
		});
	} else {
		$('#' + holder + '_model option[value!="0"]').remove();
		_model.addClass('disabled');
		$('#' + holder + '_engine option[value!="0"]').remove();
		_engine.addClass('disabled');
		_photo.hide();
		_image.hide();
		_time.addClass('disabled').val(0);

        $('.height-wrapper').height('auto');
        $(window).trigger('resize');
	}
};

var modelChange = function(holder) {
    var _photo = $('#' + holder + '_photo'),
        _image = $('#' + holder + '_image_container'),
        _time = $('#' + holder + '_time'),
        _model = $('#' + holder + '_model'),
        _engine = $('#' + holder + '_engine');

	$('.comparison-add-point-handler').removeClass('active');
	$('#comare_values').slideUp();
	
	if (parseInt(_model.val()) !== 0) {
        ajaxSpinner.add(
            _model,
            'medium',
            'before',
            {
                'top': 6,
                'right': 37
            }
        );

        $.ajax({
            url: baseURL + 'comparison/get-model',
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {
                modelId: _model.val()
            },
            success: function(response) {
                var data = $.parseJSON(response);
                if (data.error) {
                    showMessage(data.error, localizationMessages['error']);
                } else {
                    _photo.html(data.image).show();
                    _image.show();

                    $('#'+holder+'_engine option[value!="0"]').remove();
                    _engine.append(data.engines).removeClass('disabled');

                    _time.removeClass('disabled');
                }

                $('.height-wrapper').height('auto');
                $(window).trigger('resize');
            },
            complete: function() {
                ajaxSpinner.stop();
            }
        });
	} else {
		$('#'+holder+'_engine option[value!="0"]').remove();
		_engine.addClass('disabled');
		_photo.hide();
		_image.hide();
		_time.addClass('disabled').val(0);

        $('.height-wrapper').height('auto');
        $(window).trigger('resize');
	}
};

var validateValueByPattern = function(value, needPattern) {
	if (value) {
		var pattern = new RegExp(needPattern);
		if (pattern.test(value)) {
			return true;
		}
	}
	return false;
};

/**
 * Проверяет элемент на попадание в видимую часть экрана.
 * Для попадания достаточно, чтобы верхняя или нижняя границы элемента были видны.
 */
var isVisible = function(elem) {
    var coords = elem.getBoundingClientRect();
    var windowHeight = document.documentElement.clientHeight;

    // верхняя граница elem в пределах видимости ИЛИ нижняя граница видима
    var topVisible = coords.top > 0 && coords.top < windowHeight;
    var bottomVisible = coords.bottom < windowHeight && coords.bottom > 0;

    return topVisible || bottomVisible;
};

/**
 Вариант проверки, считающий элемент видимым,
 если он не более чем -1 страница назад или +1 страница вперед

 function isVisible(elem) {

      var coords = elem.getBoundingClientRect();

      var windowHeight = document.documentElement.clientHeight;

      var extendedTop = -windowHeight;
      var extendedBottom = 2 * windowHeight;

      // top visible || bottom visible
      var topVisible = coords.top > extendedTop && coords.top < extendedBottom;
      var bottomVisible = coords.bottom < extendedBottom && coords.bottom > extendedTop;

      return topVisible || bottomVisible;
    }
 */

var showVisible = function() {
    var imgs = document.getElementsByTagName('img');
    for (var i = 0; i < imgs.length; i++)
    {
        var img = imgs[i];

        var realsrc = img.getAttribute('realsrc');
        if (!realsrc) continue;

        if (isVisible(img)) {
            img.src = realsrc;
            img.setAttribute('realsrc', '');
        }
    }

};

window.onscroll = showVisible;
showVisible();