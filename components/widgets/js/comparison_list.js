var _comparisonListControllerAction,
    _comparisonListParams,
    _comparisonListSorting,
    _comparisonListPageNum,
    _comparisonListOptions;

$('.btnLoadMore').on('click', function () {
    if (!$(this).hasClass('disabled') && _comparisonListControllerAction.length) {
        ajaxSpinner.button($(this));
        ++_comparisonListPageNum;
        loadComparisonItems(true);
    }
});

var loadComparisonItems = function (isButton) {
    isButton = isButton || false;

    $.ajax({
        url: baseURL + _comparisonListControllerAction,
        async: true,
        type: 'POST',
        dataType: 'html',
        data: {
            params: _comparisonListParams,
            sorting: _comparisonListSorting,
            pageNum: _comparisonListPageNum,
            options: _comparisonListOptions
        },
        success: function(response) {
            var data = $.parseJSON(response),
                btnLoadMore = $('.btnLoadMore');
            $('.comparison-list-wrap').html(data.html);
            _comparisonListPageNum = data.pageNum;

            ajaxSpinner.stop(isButton);

            if (data.itemsRemain > 0) {
                btnLoadMore.show();
                btnLoadMore.find('.badge').text(data.itemsRemain);
            } else {
                btnLoadMore.hide();
            }

            updateTooltip();
        }
    });
};