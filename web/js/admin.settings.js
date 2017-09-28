var settings = {
    init: function () {
        this.attachEvents();
    },

    attachEvents: function () {
        var self = this;

        $('.btnCriteriaDlg').on('click', function (e) {
            e.preventDefault();
            self.getForm('<i class="fa fa-bar-chart"></i> Пользовательский бал', baseURL + 'admin/criteria/get-form');
        });

        $('.edit-criteria-item').on('click', function (e) {
            e.preventDefault();
            self.getForm('<i class="fa fa-bar-chart"></i> Пользовательский бал', baseURL + 'admin/criteria/get?id=' + $(this).data('ajax'));
        });

        $('.btnTechnicalDlg').on('click', function (e) {
            e.preventDefault();
            self.getForm('<i class="fa fa-cogs"></i> Категория характеристик', baseURL + 'admin/technical/get-category-form');
        });

        $('.edit-technical-category-item').on('click', function (e) {
            e.preventDefault();
            self.getForm('<i class="fa fa-cogs"></i> Категория характеристик', baseURL + 'admin/technical/get-category?id=' + $(this).data('ajax'));
        });

        $('.btnTechnicalOptionDlg').on('click', function (e) {
            e.preventDefault();
            self.getForm('<i class="fa fa-cogs"></i> Техническая характеристика', baseURL + 'admin/technical/get-option-form');
        });

        $('.edit-technical-option-item').on('click', function (e) {
            e.preventDefault();
            self.getForm('<i class="fa fa-cogs"></i> Техническая характеристика', baseURL + 'admin/technical/get-option?id=' + $(this).data('ajax'));
        });

        $('.btn-toggle-nested').on('click', function (e) {
            e.preventDefault();

            var parent = $(this).parents('li'),
                fa = $(this).find('.fa'),
                $target = $('.' + $(this).data('target'));

            if (fa.hasClass('fa-plus-square-o')) {
                fa.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
            } else {
                fa.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            }

            parent.after($target);
            $target.slideToggle();
        });
    },

    sortCriteriaItems: function () {
        this.sortItems('.criteria-name', 'criteria/sort-items');
    },

    sortTechnicalCategoryItems: function () {
        var target;
        $('.btn-toggle-nested').each(function () {
            target = $('.' + $(this).data('target'));
            if (target.is(':visible')) {
                $($(this).parents('li')).after(target);
            }
        });

        this.sortItems('.category-name', 'technical/sort-category-items');
    },

    sortTechnicalOptionItems: function (id) {
        this.sortItems('.option-name-' + id, 'technical/sort-option-items');
    },

    sortItems: function (elem, url) {
        var items = [];
        $(elem + '[data-order]').each(function () {
            items.push($(this).data('order'));
        });

        if (items.length) {
            $.ajax({
                url: baseURL + 'admin/' + url,
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {
                    items: items
                }
            });
        }
    },

    getForm: function (title, url) {
        var formDialog = $('#formDialog'),
            form = $('.form-container');

        form.html('');
        form.append('<h4>Загрузка данных...</h4>');
        ajaxSpinner.add(form.find('h4'), 'small', 'append', {'margin-left': '20px', 'position': 'relative'});

        $.ajax({
            url: url,
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

        formDialog.find('.modal-title').html(title);
        formDialog.modal('show');
    }
};