var ajaxSpinner = {
    elem: '',
    size: 'small',
    css: {},
    loader: '',
    spinner: '',
    loaderClass: '',
    buttonText: '',
    buttonWidth: 0,
    buttonHeight: 0,

    loaderOpts24: {
        lines: 9, // The number of lines to draw
        length: 0, // The length of each line
        width: 4, // The line thickness
        radius: 8, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 44, // The rotation offset
        direction: 1, // 1: clockwise, -1: counterclockwise
        color: '#ff4500', // #rgb or #rrggbb or array of colors
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: false, // Whether to render a shadow
        hwaccel: true, // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: '50%', // Top position relative to parent
        left: '50%' // Left position relative to parent
    },
    loader24: '<span class="loader-24"></span>',

    loaderDarkOpts24: {
        lines: 9, // The number of lines to draw
        length: 0, // The length of each line
        width: 4, // The line thickness
        radius: 8, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 44, // The rotation offset
        direction: 1, // 1: clockwise, -1: counterclockwise
        color: '#000', // #rgb or #rrggbb or array of colors
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: false, // Whether to render a shadow
        hwaccel: true, // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: '50%', // Top position relative to parent
        left: '50%' // Left position relative to parent
    },
    loaderDark24: '<span class="loader-dark-24"></span>',

    loaderWhiteOpts24: {
        lines: 9, // The number of lines to draw
        length: 0, // The length of each line
        width: 4, // The line thickness
        radius: 8, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 44, // The rotation offset
        direction: 1, // 1: clockwise, -1: counterclockwise
        color: '#fff', // #rgb or #rrggbb or array of colors
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: false, // Whether to render a shadow
        hwaccel: true, // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: '50%', // Top position relative to parent
        left: '50%' // Left position relative to parent
    },
    loaderWhite24: '<span class="loader-white-24"></span>',

    loaderOpts12: {
        lines: 7, // The number of lines to draw
        length: 0, // The length of each line
        width: 4, // The line thickness
        radius: 5, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 44, // The rotation offset
        direction: 1, // 1: clockwise, -1: counterclockwise
        color: '#ff4500', // #rgb or #rrggbb or array of colors
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: false, // Whether to render a shadow
        hwaccel: true, // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: '50%', // Top position relative to parent
        left: '50%' // Left position relative to parent
    },
    loader12: '<span class="loader-12"></span>',

    loaderDarkOpts12: {
        lines: 7, // The number of lines to draw
        length: 0, // The length of each line
        width: 4, // The line thickness
        radius: 5, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 44, // The rotation offset
        direction: 1, // 1: clockwise, -1: counterclockwise
        color: '#000', // #rgb or #rrggbb or array of colors
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: false, // Whether to render a shadow
        hwaccel: true, // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: '50%', // Top position relative to parent
        left: '50%' // Left position relative to parent
    },
    loaderDark12: '<span class="loader-dark-12"></span>',

    loaderWhiteOpts12: {
        lines: 7, // The number of lines to draw
        length: 0, // The length of each line
        width: 4, // The line thickness
        radius: 5, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 44, // The rotation offset
        direction: 1, // 1: clockwise, -1: counterclockwise
        color: '#fff', // #rgb or #rrggbb or array of colors
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: false, // Whether to render a shadow
        hwaccel: true, // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: '50%', // Top position relative to parent
        left: '50%' // Left position relative to parent
    },
    loaderWhite12: '<span class="loader-white-12"></span>',

    add: function(elem, size, position, css) {
        this.elem = elem;
        this.size = size;
        this.css = css;

        this.init();

        switch (position) {
            case 'prepend': this.prepend(); break;
            case 'append': this.append(); break;
            case 'before': this.before(); break;
            default: this.after(); break;
        }

        $('.' + this.loaderClass).css(this.css).append(this.spinner.el);
    },

    dialog: function (elem, size) {
        this.size = size || 'medium';

        this.init();

        elem.prepend(this.loader24);

        $('.' + this.loaderClass).css({
            'position': 'relative',
            'margin-right': 10
        }).append(this.spinner.el);
    },

    button: function (elem, size) {
        this.elem = elem;
        this.size = size || 'medium-dark';

        this.buttonText = elem.html();
        this.buttonWidth = this.elem.width();
        this.buttonHeight = this.elem.height();

        this.elem.addClass('disabled');
        this.elem.addClass('relative');
        this.elem.width(this.buttonWidth);
        this.elem.height(this.buttonHeight);
        this.elem.css({'opacity': 1});
        this.elem.html('');

        this.init();

        this.elem.append(this.loader);

        $('.' + this.loaderClass).css({
            'position': 'absolute',
            'left': '50%',
            'top': '50%'
        }).append(this.spinner.el);
    },

    init: function () {
        switch (this.size) {
            case 'small':
                this.loader = this.loader12;
                this.loaderClass = 'loader-12';
                this.spinner = new Spinner(this.loaderOpts12).spin();
                break;
            case 'small-dark':
                this.loader = this.loaderDark12;
                this.loaderClass = 'loader-dark-12';
                this.spinner = new Spinner(this.loaderDarkOpts12).spin();
                break;
            case 'small-white':
                this.loader = this.loaderWhite12;
                this.loaderClass = 'loader-white-12';
                this.spinner = new Spinner(this.loaderWhiteOpts12).spin();
                break;
            case 'medium':
                this.loader = this.loader24;
                this.loaderClass = 'loader-24';
                this.spinner = new Spinner(this.loaderOpts24).spin();
                break;
            case 'medium-dark':
                this.loader = this.loaderDark24;
                this.loaderClass = 'loader-dark-24';
                this.spinner = new Spinner(this.loaderDarkOpts24).spin();
                break;
            case 'medium-white':
                this.loader = this.loaderWhite24;
                this.loaderClass = 'loader-white-24';
                this.spinner = new Spinner(this.loaderWhiteOpts24).spin();
                break;
        }
    },

    prepend: function() {
        this.elem.prepend(this.loader);
    },

    append: function () {
        this.elem.append(this.loader);
    },

    before: function () {
        this.elem.before(this.loader);
    },

    after: function () {
        this.elem.after(this.loader);
    },

    stop: function (isButton) {
        isButton = isButton || false;

        this.spinner.stop();
        $('.' + this.loaderClass).remove();

        if (isButton) {
            this.elem.removeClass('disabled');
            this.elem.removeClass('relative');
            this.elem.html(this.buttonText);
        }
    }
};