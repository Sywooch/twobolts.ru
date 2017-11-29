var User = {
    avatarImg: null,

    cropperAvatarOptions: {
        minCropBoxWidth: 200,
        minCropBoxHeight: 200,
        viewMode: 1,
        aspectRatio: 1,
        preview: '.img-preview'
    },

    /**
     * Init
     */
    init: function () {
        var self = this;

        self.avatarImg = $('#avatar_image');

        self.attachEvents();
    },

    /**
     * Attach elements' events
     */
    attachEvents: function () {
        var self = this;

        $('.btn-update-password').on('click', function () {
            self.updatePassword();
        });

        $('.btn-update-profile').on('click', function () {
            self.updateProfile();
        });

        $('.btn-update-email').on('click', function () {
            self.updateEmail();
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

        if (self.avatarImg.length) {
            self.avatarImg.upload({
                name: 'Upload[imageFile]',
                action: '/uploader/upload',
                enctype: 'multipart/form-data',
                params: {
                    field: 'Upload[imageFile]'
                },
                autoSubmit: true,
                onSubmit: function() {
                    Main.uploadHandler = '#current-avatar';
                    Main.cropperCallback = 'User.cropAvatarImage';

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
                onComplete: function(response) { self.showAvatarCropper('#avatar_container', response); },
                onSelect: function() {}
            });
        }
    },

    /**
     *
     * @param elem
     * @param response
     */
    showAvatarCropper: function (elem, response) {
        var self = this,
            data = $.parseJSON(response);

        $(elem + " form[target^='iframe']")[0].reset();

        ajaxSpinner.stop();

        if (data.resultCode === 'failed') {
            showMessage(data.result.imageFile.join('<br>'), 'Ошибка!');
            return;
        }

        $('#avatarCropperDlg').modal('show');
        $('.response-text').remove();

        Main.cropperImage.cropper('destroy');
        Main.cropperImage.attr('src', baseURL + 'uploads/temp/' + data.fileName.name).cropper(User.cropperAvatarOptions);
    },

    /**
     *
     * @param data
     */
    cropAvatarImage: function (data) {
        var src = Main.cropperImage.attr('src'),
            avatarCropperDlg = $('#avatarCropperDlg');

        $('.response-text').remove();

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
    },

    /**
     * Update profile info
     */
    updateProfile: function () {
        $('.response-text').remove();

        var profileDlg = $('#profileDlg'),
            timezone = $('#timezone'),
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
                timezone: timezone.val(),
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
    },

    /**
     * Update user password
     */
    updatePassword: function () {
        $('.response-text').remove();

        var passwordDlg = $('#passwordDlg');
        var password = $('#password');
        var newPassword = $('#newPassword');
        var confirmPassword = $('#confirmPassword');

        if (password.val().length < 4 || newPassword.val().length < 4 || confirmPassword.val().length < 4) {
            passwordDlg.find('.modal-footer .btn-group').prepend('<div class="response-text text-danger">Все поля обязательны к заполнению</div>');
            return;
        }

        if (newPassword.val() !== confirmPassword.val()) {
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
    },

    /**
     * Update user email
     */
    updateEmail: function () {
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
    },

    /**
     * Updates user profile notification
     */
    updateProfileNotification: function () {
        $.ajax({
            url: baseURL + 'profile/notification',
            async: true,
            type: 'POST',
            data: {
                status: $('#profile_notification').val()
            },
            dataType: 'html'
        });
    }
};