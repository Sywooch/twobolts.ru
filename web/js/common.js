/**
 *
 * @param data
 * @returns {boolean}
 */
var isJson = function(data) {
    var isJSON = true;

    try {
        var obj = $.parseJSON(data);
    } catch (error) {
        isJSON = false;
    }
    return isJSON;
};

/**
 * Function generates a random string for use in unique IDs, etc
 */
var randomString = function (n) {
    var text = '',
        possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!#$%&*_-=<>?';

    if (!n) {
        n = 5;
    }

    for (var i = 0; i < n; i++)
    {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    }

    return text;
};