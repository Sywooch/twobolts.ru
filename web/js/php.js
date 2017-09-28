var trim = function(str, charlist) {
    //  discuss at: http://locutus.io/php/trim/
    // original by: Kevin van Zonneveld (http://kvz.io)
    // improved by: mdsjack (http://www.mdsjack.bo.it)
    // improved by: Alexander Ermolaev (http://snippets.dzone.com/user/AlexanderErmolaev)
    // improved by: Kevin van Zonneveld (http://kvz.io)
    // improved by: Steven Levithan (http://blog.stevenlevithan.com)
    // improved by: Jack
    //    input by: Erkekjetter
    //    input by: DxGx
    // bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
    //   example 1: trim('    Kevin van Zonneveld    ')
    //   returns 1: 'Kevin van Zonneveld'
    //   example 2: trim('Hello World', 'Hdle')
    //   returns 2: 'o Wor'
    //   example 3: trim(16, 1)
    //   returns 3: '6'

    var whitespace = [
        ' ',
        '\n',
        '\r',
        '\t',
        '\f',
        '\x0b',
        '\xa0',
        '\u2000',
        '\u2001',
        '\u2002',
        '\u2003',
        '\u2004',
        '\u2005',
        '\u2006',
        '\u2007',
        '\u2008',
        '\u2009',
        '\u200a',
        '\u200b',
        '\u2028',
        '\u2029',
        '\u3000'
    ].join('');
    var l = 0;
    var i = 0;
    str += '';

    if (charlist) {
        whitespace = (charlist + '').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^:])/g, '$1')
    }

    l = str.length;
    for (i = 0; i < l; i++) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {
            str = str.substring(i);
            break
        }
    }

    l = str.length;
    for (i = l - 1; i >= 0; i--) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {
            str = str.substring(0, i + 1);
            break
        }
    }

    return whitespace.indexOf(str.charAt(0)) === -1 ? str : ''
};

function explode(delimiter, string, limit) 
{
	if ( arguments.length < 2 || typeof delimiter === 'undefined' || typeof string === 'undefined' ) return null;
	if ( delimiter === '' || delimiter === false || delimiter === null) return false;
	if ( typeof delimiter === 'function' || typeof delimiter === 'object' || typeof string === 'function' || typeof string === 'object') {
		return { 0: '' };
	}
	if ( delimiter === true ) delimiter = '1';
	
	// Here we go...
	delimiter += '';
	string += '';
	
	var s = string.split( delimiter );
	
	
	if ( typeof limit === 'undefined' ) return s;
	
	// Support for limit
	if ( limit === 0 ) limit = 1;
	
	// Positive limit
	if ( limit > 0 ) {
		if ( limit >= s.length ) return s;
		return s.slice( 0, limit - 1 ).concat( [ s.slice( limit - 1 ).join( delimiter ) ] );
	}
	
	// Negative limit
	if ( -limit >= s.length ) return [];
	
	s.splice( s.length + limit );
	return s;
}

function stripos (f_haystack, f_needle, f_offset) 
{
	// From: http://phpjs.org/functions
	// +     original by: Martijn Wieringa
	// +      revised by: Onno Marsman
	// *         example 1: stripos('ABC', 'a');
	// *         returns 1: 0
	var haystack = (f_haystack + '').toLowerCase();
	var needle = (f_needle + '').toLowerCase();
	var index = 0;
	
	if ((index = haystack.indexOf(needle, f_offset)) !== -1) {
		return index;
	}
	
	return false;
}

function str_repeat (input, multiplier) 
{
	// From: http://phpjs.org/functions
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
	// +   improved by: Ian Carter (http://euona.com/)
	// *     example 1: str_repeat('-=', 10);
	// *     returns 1: '-=-=-=-=-=-=-=-=-=-='
	
	var y = '';
	while (true) 
	{
		if (multiplier & 1) {
			y += input;
		}
		multiplier >>= 1;
		if (multiplier) {
			input += input;
		} else {
			break;
		}
	}
	
	return y;
}