var User = {
    /**
     * Init
     */
    init: function () {
        var self = this;

        self.attachEvents();
    },

    attachEvents: function () {

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