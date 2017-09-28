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