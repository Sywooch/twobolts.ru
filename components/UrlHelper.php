<?php
namespace app\components;

use yii\helpers\BaseUrl;

class UrlHelper extends BaseUrl
{
    /**
     * @param string $url
     * @param bool $scheme
     * @return string
     */
    public static function absolute($url = '', $scheme = true)
    {
        $pos = strpos($url, self::home($scheme));
        if ($pos === false) {
            return self::home($scheme) . trim($url, '/');
        }

        return trim($url, '/');
    }

    /**
     * Auto-linker
     *
     * Taken from CI3
     *
     * Automatically links URL and Email addresses.
     * Note: There's a bit of extra code here to deal with
     * URLs or emails that end in a period. We'll strip these
     * off and add them after the link.
     *
     * @param	string	$str
     * @param	string	$type: email, url, or both
     * @param	bool	$popup whether to create pop-up links
     * @return	string
     */
    public static function autoLink($str, $type = 'both', $popup = false)
    {
        // Find and replace any URLs.
        if ($type !== 'email' && preg_match_all('#(\w*://|www\.)[^\s()<>;]+\w#i', $str, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            // Set our target HTML if using popup links.
            $target = ($popup) ? ' target="_blank"' : '';

            // We process the links in reverse order (last -> first) so that
            // the returned string offsets from preg_match_all() are not
            // moved as we add more HTML.
            foreach (array_reverse($matches) as $match)
            {
                // $match[0] is the matched string/link
                // $match[1] is either a protocol prefix or 'www.'
                //
                // With PREG_OFFSET_CAPTURE, both of the above is an array,
                // where the actual value is held in [0] and its offset at the [1] index.
                $a = '<a href="'.(strpos($match[1][0], '/') ? '' : 'http://').$match[0][0].'"'.$target.'>'.$match[0][0].'</a>';
                $str = substr_replace($str, $a, $match[0][1], strlen($match[0][0]));
            }
        }

        // Find and replace any emails.
        if ($type !== 'url' && preg_match_all('#([\w\.\-\+]+@[a-z0-9\-]+\.[a-z0-9\-\.]+[^[:punct:]\s])#i', $str, $matches, PREG_OFFSET_CAPTURE)) {
            foreach (array_reverse($matches[0]) as $match)
            {
                if (filter_var($match[0], FILTER_VALIDATE_EMAIL) !== FALSE) {
                    $str = substr_replace($str, self::safeMailTo($match[0]), $match[1], strlen($match[0]));
                }
            }
        }

        return $str;
    }

    /**
     * Encoded MailTo Link
     *
     * Taken from CI3
     *
     * Create a spam-protected mailto link written in Javascript
     *
     * @param	string	$email address
     * @param	string	$title
     * @param	mixed	$attributes
     * @return	string
     */
    public static function safeMailTo($email, $title = '', $attributes = '')
    {
        $title = (string) $title;

        if ($title === '') {
            $title = $email;
        }

        $x = str_split('<a href="mailto:', 1);

        for ($i = 0, $l = strlen($email); $i < $l; $i++)
        {
            $x[] = '|'.ord($email[$i]);
        }

        $x[] = '"';

        if ($attributes !== '') {
            if (is_array($attributes)) {
                foreach ($attributes as $key => $val)
                {
                    $x[] = ' '.$key.'="';
                    for ($i = 0, $l = strlen($val); $i < $l; $i++)
                    {
                        $x[] = '|'.ord($val[$i]);
                    }
                    $x[] = '"';
                }
            } else {
                for ($i = 0, $l = strlen($attributes); $i < $l; $i++)
                {
                    $x[] = $attributes[$i];
                }
            }
        }

        $x[] = '>';

        $temp = [];
        $count = 0;
        for ($i = 0, $l = strlen($title); $i < $l; $i++)
        {
            $ordinal = ord($title[$i]);

            if ($ordinal < 128) {
                $x[] = '|'.$ordinal;
            } else {
                if (count($temp) === 0) {
                    $count = ($ordinal < 224) ? 2 : 3;
                }

                $temp[] = $ordinal;
                if (count($temp) === $count) {
                    $number = ($count === 3)
                        ? (($temp[0] % 16) * 4096) + (($temp[1] % 64) * 64) + ($temp[2] % 64)
                        : (($temp[0] % 32) * 64) + ($temp[1] % 64);
                    $x[] = '|'.$number;
                    $count = 1;
                    $temp = array();
                }
            }
        }

        $x[] = '<'; $x[] = '/'; $x[] = 'a'; $x[] = '>';

        $x = array_reverse($x);

        $output = "<script type=\"text/javascript\">\n"
            ."\t//<![CDATA[\n"
            ."\tvar l= [];\n";

        for ($i = 0, $c = count($x); $i < $c; $i++)
        {
            $output .= "\tl[".$i."] = '".$x[$i]."';\n";
        }

        $output .= "\n\tfor (var i = l.length-1; i >= 0; i=i-1) {\n"
            ."\t\tif (l[i].substring(0, 1) === '|') document.write(\"&#\"+unescape(l[i].substring(1))+\";\");\n"
            ."\t\telse document.write(unescape(l[i]));\n"
            ."\t}\n"
            ."\t//]]>\n"
            .'</script>';

        return $output;
    }
}