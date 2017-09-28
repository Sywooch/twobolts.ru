var emailPattern = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
    cropperImage = $('#cropped_img'),
    cropperAvatarOptions = {
        minCropBoxWidth: 200,
        minCropBoxHeight: 200,
        viewMode: 1,
        aspectRatio: 1,
        preview: '.img-preview'
    },
    cropperOptions = {
        minCropBoxWidth: 480,
        minCropBoxHeight: 270,
        viewMode: 1,
        aspectRatio: 16/9,
        preview: '.img-preview'
    },
    cropFunc = 'cropImage',
    messageDialog = $('#messageDlg'),
    compareListPage = 1,
    elemUpload,
    compareCompareUpload,
    avatarUpload,
    isAdmin = false,
    newsCount,
    newsPage,
    newsPerPage;

$(function() {
    showVisible();

    var $body = $('body');

    $body.on("ajaxSend", function(elm, xhr, s) {
        if (s.type == "POST") {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr("content"));
        }
    });

	$body.on('click', 'a', function(e) {
		if (stripos($(this).attr('href'), '#') !== false) e.preventDefault();
	});

    $body.on('click', '.btn', function(e) {
        $(this).blur();
    });

    $body.on('mouseout', '.btn', function(e) {
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
                obo = $('.obo'),
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

    $('#aboutDlg').dialog({
        autoOpen: false,
        closeText: 'закрыть',
        dialogClass: 'aboutDlg',
        title: 'О себе',
        height: 275,
        width:345,
        resizable: false,
        modal: true,
        buttons: {
            'Сохранить': function() {
                saveAbout();
            },
            'Отменить': function() {
                $(this).dialog('close');
            }
        }
    });

    $('#aboutDlg textarea').jqxInput({
        theme: 'web',
        height: 150,
        width: 313,
        minLength: 1
    });
	
	*/

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

            result = cropperImage.cropper(data.crop, data.option);

            if (data.crop === 'getData') {
                window[cropFunc](result);
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

	$('#edit_email').on('click', function(e) {
		e.preventDefault();
		$('.fnErrorMsg').removeClass('fnShowErrorMsg');
        var emailDlg = $('#emailDlg');
        emailDlg.find('input').removeClass('input_error');
        emailDlg.modal('show');
	});

	$('#edit_password').on('click', function(e) {
		e.preventDefault();
		var passwordDlg = $('#passwordDlg');
        passwordDlg.find('input').removeClass('input_error');
        passwordDlg.find('.response-text').remove();
        passwordDlg.modal('show');
	});

    $('#edit_profile').on('click', function(e) {
        e.preventDefault();
        $('.fnErrorMsg').removeClass('fnShowErrorMsg');
        var profileDlg = $('#profileDlg');
        profileDlg.find('input').removeClass('input_error');
        profileDlg.modal('show');
    });

    $('.btnEditPassword').on('click', function () {
        editPassword();
    });

    $('.btnEditEmail').on('click', function () {
        editEmail();
    });

    $('.btnEditProfile').on('click', function () {
        editProfile();
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

    $('.btnRecover').on('click', function () {
        resetPassword();
    });

    $('.btnSignup').on('click', function () {
        registerUser();
    });

	$('#signOff').on('click', function(e) {
        e.preventDefault();

	    ajaxSpinner.add($('.login-link'), 'small', 'after', {'top': 16, 'margin-right': 10});

		$.ajax({
			url: baseURL + 'sign-out',
			async: true,
			type: 'POST',
			dataType: 'html',
			data: {},
			success: function(response) {
				location.reload();
			}
		});
	});

    var avatarImg = $('#avatar_image');
	if (avatarImg.length) {
		avatarUpload = avatarImg.upload({
			name: 'Upload[imageFile]',
			action: '/uploader/upload',
			enctype: 'multipart/form-data',
			params: {
				field: 'Upload[imageFile]'
			},
			autoSubmit: true,
			onSubmit: function() {
				elemUpload = '#current-avatar';
                cropFunc = 'cropAvatarImage';
                ajaxSpinner.add(
                    $('#avatar_container'),
                    'medium',
                    'prepend',
                    {
                        'position': 'absolute',
                        'top': 6,
                        'left': -30
                    }
                );
			},
			onComplete: function(response) { showAvatarCropper('#avatar_container', response); },
			onSelect: function() {}
		});
	}
	
	$('#closeFlashMsg').on('click', function() {
		$('[class*="flash_msg_"]').slideUp();
	});
	
	$('.loginVK').on('click', function() {
		window.location = vkAuthUrl;
	});
	
	$('.loginOK').on('click', function() {
		window.location = okAuthUrl;
	});
	
	$('.loginFB').on('click', function() {
		window.location = fbAuthUrl;
	});
	
	$('.loginTW').on('click', function() {
		window.location = twAuthUrl;
	});
	
	$('.loginGoogle').on('click', function() {
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
	
	if ($('.splitter-list li').length > 0) {
		splitList();
	}
	
	$('.catalog-btn-view-all a').on('click', function() {
		$('.catalog-popular-list, .catalog-all-list').slideToggle();

		if ($('.catalog-all-list').text() == 'популярные') {
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

	var remember = $('#signin_remeber').is(':checked') ? 1 : 0;

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

var editPassword = function () {
    $('.response-text').remove();

    var passwordDlg = $('#passwordDlg');
    var password = $('#password');
    var newPassword = $('#newPassword');
    var confirmPassword = $('#confirmPassword');

    if (password.val().length < 4 || newPassword.val().length < 4 || confirmPassword.val().length < 4) {
        passwordDlg.find('.modal-footer .btn-group').prepend('<div class="response-text text-danger">Все поля обязательны к заполнению</div>');
        return;
    }

    if (newPassword.val() != confirmPassword.val()) {
        newPassword.addClass('input_error');
        confirmPassword.addClass('input_error');
        passwordDlg.find('.modal-footer .btn-group').prepend('<div class="response-text text-danger">Пароли не совпадают</div>');
        return;
    }

    ajaxSpinner.dialog(passwordDlg.find('.modal-footer .btn-group'));

    $.ajax({
        url: baseURL + 'profile/edit-password',
        async: true,
        type: 'POST',
        dataType: 'html',
        data: {
            User: {
                password: password.val(),
                newPassword: newPassword.val(),
                confirmPassword: confirmPassword.val()
            }
        },
        success: function(response) {
            var data = $.parseJSON(response);
            if (data.error) {
                passwordDlg.find('.modal-footer .btn-group').prepend('<div class="response-text text-danger">' + data.error + '</div>');
            } else {
                window.location.href = baseURL;
            }
        },
        complete: function() {
            ajaxSpinner.stop();
        }
    });
};

var editEmail = function () {
    $('.response-text').remove();

    var emailDlg = $('#emailDlg');
    var email = $('#email');

    if (!validateValueByPattern(email.val(), emailPattern)) {
        email.addClass('input_error');
        emailDlg.find('.modal-footer .btn-group').prepend('<div class="response-text text-danger">Неверный формат e-mail</div>');
        return;
    }

    ajaxSpinner.dialog(emailDlg.find('.modal-footer .btn-group'));

    $.ajax({
        url: baseURL + 'profile/save',
        async: true,
        type: 'POST',
        dataType: 'html',
        data: {
            User: {
                email: email.val()
            },
            scenario: 'edit-email'
        },
        success: function(response) {
            var data = $.parseJSON(response);
            if (data.error) {
                var error = true;
                email.addClass('input_error');
                emailDlg.find('.modal-footer .btn-group').prepend('<div class="response-text text-danger">' + data.error + '</div>');
            } else {
                emailDlg.find('.modal-footer .btn-group').prepend('<div class="response-text text-success">' + data.message + '</div>');
            }
        },
        complete: function() {
            ajaxSpinner.stop();
        }
    });
};

var editProfile = function() {
    $('.response-text').remove();

    var profileDlg = $('#profileDlg'),
        city = $('#city'),
        country = $('#country'),
        about = $('#about'),
        error = false;

    ajaxSpinner.dialog(profileDlg.find('.modal-footer .btn-group'));

    $.ajax({
        url: baseURL + 'profile/save',
        async: true,
        type: 'POST',
        dataType: 'html',
        data: {
            UserProfile: {
                country: country.val(),
                city: city.val(),
                about: about.val()
            },
            scenario: 'edit-profile'
        },
        success: function(response) {
            var data = $.parseJSON(response);
            if (data.error) {
                profileDlg.find('.modal-footer .btn-group').prepend('<div class="response-text text-danger">' + data.error + '</div>');
            } else {
                profileDlg.find('.modal-footer .btn-group').prepend('<div class="response-text text-success">' + data.message + '</div>');
                window.location.href = window.location;
            }
        },
        complete: function() {
            ajaxSpinner.stop();
        }
    });
};

function showLoader(elem, css)
{
	$(elem).prepend(loader24);
	spinner24 = new Spinner(loaderOpts24).spin();
		
	if (css) {
		$('.loader-24').css(css).append(spinner24.el);
	} else {
		$('.loader-24').css({
			'left': -30,
			'top': 7
		}).append(spinner24.el);
	}
}

var showCropper = function(elem, response) {
    var data = $.parseJSON(response);
    $(elem + " form[target^='iframe']")[0].reset();
    ajaxSpinner.stop(true);

    if (data.resultCode == 'failed') {
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
                var src = data.src;
                if (!isAdmin) {
                    $(elemUpload + ' img').attr('src', baseURL + data.src);
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

var showAvatarCropper = function (elem, response) {
    var data = $.parseJSON(response);
    $(elem + " form[target^='iframe']")[0].reset();
    ajaxSpinner.stop();

    if (data.resultCode == 'failed') {
        showMessage(data.result.imageFile.join('<br>'), 'Ошибка!');
        return;
    }

    $('#avatarCropperDlg').modal('show');
    $('.response-text').remove();
    cropperImage.cropper('destroy');
    cropperImage.attr('src', baseURL + 'uploads/temp/' + data.fileName.name).cropper(cropperAvatarOptions);
};

var cropAvatarImage = function (data) {
    $('.response-text').remove();

	var src = cropperImage.attr('src'),
        avatarCropperDlg = $('#avatarCropperDlg');

    ajaxSpinner.dialog(avatarCropperDlg.find('.modal-footer .btn-group'));

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
			cropWidth: 200,
			cropHeight: 200,
			dirName: 'avatars'
		},
		success: function(response) {
			var data = $.parseJSON(response);
			if (data.error) {
                avatarCropperDlg.find('.modal-footer .btn-group').prepend('<div class="response-text text-danger">' + data.error + '</div>');
			} else {
				var src = data.src;
				$.ajax({
					url: baseURL + 'profile/save',
					async: true,
					type: 'POST',
					dataType: 'html',
					data: {
                        User: {
                            avatar: data.name
                        }
					},
					success: function(response) {
						var data = $.parseJSON(response);
						if (data.error) {
                            avatarCropperDlg.find('.modal-footer .btn-group').prepend('<div class="response-text text-danger">' + data.error + '</div>');
						} else {
							$('.current-avatar img, .comparison-author-avatar-32.in_header img, .my_profile_allview .avat_50 img').prop('src', baseURL + src);
						}
					},
					complete: function() {
						avatarCropperDlg.modal('hide');
					}
				});
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
	
	if (_manufacturer.val() != 0) {
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
        _manufacturer = $('#' + holder + '_manufacturer'),
        _model = $('#' + holder + '_model'),
        _engine = $('#' + holder + '_engine');

	$('.comparison-add-point-handler').removeClass('active');
	$('#comare_values').slideUp();
	
	if (_model.val() != 0) {
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

var isJson = function(data) {
	var IS_JSON = true;
	try {
		var obj = $.parseJSON(data);
	} catch (error) {
		IS_JSON = false;
	}
	return IS_JSON;
};

var splitList = function() {
	$('.splitter-list').each(function(index, element) {
		var listLength = $(this).children('li').length;
		var cols = 4;
		var colLength = Math.ceil(listLength / cols);
		var newLists = [];
		var splitWidth = $(this).width() / cols;
		for (i = 1; i <= cols; ++i)
		{
			var list = $('<ul class="splitted-list split-'+i+'" style="width:'+splitWidth+'px;"></ul>');
			var start = (i - 1) * colLength;
			var end = i * colLength;
			$(this).children('li').each(function(index, element) {
				if (index >= start && index < end) {
					list.append($(this).clone(true));
				}
			});
			newLists.push(list);
		}
		$(this).replaceWith(newLists);
	});
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

var loader12Stop = function () {
	spinner12.stop();
	$('.loader-12').remove();
};

var loader24Stop = function () {
	spinner24.stop();
	$('.loader-24').remove();
};

var loaderDark24Stop = function () {
    spinnerDark24.stop();
    $('.loader-dark-24').remove();
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