<?php

// $Id: module.textsanitizer.php,v 1.26 2004/12/26 19:11:48 onokazu Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <https://www.xoops.org>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (http://www.myweb.ne.jp/, http://jp.xoops.org/)        //
//         Goghs Cheng (http://www.eqiao.com, http://www.devbeez.com/)       //
// Project: The XOOPS Project (https://www.xoops.org/)                        //
// ------------------------------------------------------------------------- //

/**
 * Class to "clean up" text for various uses
 *
 * <b>Singleton</b>
 *
 *
 * @author           Kazumi Ono    <onokazu@xoops.org>
 * @author           Goghs Cheng
 * @copyright    (c) 2000-2003 The Xoops Project - www.xoops.org
 */
class mbbsTextSanitizer
{
    /**
     * @var    array
     */

    public $smileys = [];

    public $censorConf;

    /*
    * Constructor of this class
    *
    * Gets allowed html tags from admin config settings
    * <br> should not be allowed since nl2br will be used
    * when storing data.
    *
    * @access	private
    *
    * @todo Sofar, this does nuttin' ;-)
    */

    public function __construct()
    {
    }

    /**
     * Access the only instance of this class
     *
     * @return    object
     *
     * @static
     * @staticvar   object
     */
    public function &getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Get the smileys
     *
     * @return    array
     */
    public function getSmileys()
    {
        return $this->smileys;
    }

    /**
     * Replace emoticons in the message with smiley images
     *
     * @param string $message
     *
     * @return    string
     */
    public function &smiley($message)
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        if (0 == count($this->smileys)) {
            if ($getsmiles = $db->query('SELECT * FROM ' . $db->prefix('smiles'))) {
                while (false !== ($smiles = $db->fetchArray($getsmiles))) {
                    $message = str_replace($smiles['code'], '<img src="' . XOOPS_UPLOAD_URL . '/' . htmlspecialchars($smiles['smile_url'], ENT_QUOTES | ENT_HTML5) . '" alt="">', $message);

                    $this->smileys[] = $smiles;
                }
            }
        } elseif (is_array($this->smileys)) {
            foreach ($this->smileys as $smile) {
                $message = str_replace($smile['code'], '<img src="' . XOOPS_UPLOAD_URL . '/' . htmlspecialchars($smile['smile_url'], ENT_QUOTES | ENT_HTML5) . '" alt="">', $message);
            }
        }

        return $message;
    }

    /**
     * Make links in the text clickable
     *
     * @param string $text
     * @return  string
     **/
    public function makeClickable($text)
    {
        $patterns = [
            "/(^|[^]_a-z0-9-=\"'\/])([a-z]+?):\/\/([^, \r\n\"\(\)'<>]+)/i",
            "/(^|[^]_a-z0-9-=\"'\/])www\.([a-z0-9\-]+)\.([^, \r\n\"\(\)'<>]+)/i",
            "/(^|[^]_a-z0-9-=\"'\/])ftp\.([a-z0-9\-]+)\.([^, \r\n\"\(\)'<>]+)/i",
            "/(^|[^]_a-z0-9-=\"'\/:\.])([a-z0-9\-_\.]+?)@([^, \r\n\"\(\)'<>\[\]]+)/i",
        ];

        $replacements = [
            '\\1<a href="\\2://\\3" target="_blank"><img src="' . XOOPS_URL . '/modules/mbbs/images/h.gif" alt=""></a>',
            '\\1<a href="http://www.\\2.\\3" target="_blank"><img src="' . XOOPS_URL . '/modules/mbbs/images/h.gif" alt=""></a>',
            '\\1<a href="ftp://ftp.\\2.\\3" target="_blank"><img src="' . XOOPS_URL . '/modules/mbbs/images/h.gif" alt=""></a>',
            '\\1<a href="mailto:\\2@\\3"><img src="' . XOOPS_URL . '/modules/mbbs/images/m.gif" alt=""></a>',
        ];

        return preg_replace($patterns, $replacements, $text);
    }

    /**
     * Replace XoopsCodes with their equivalent HTML formatting
     *
     * @param string $text
     * @param int    $allowimage    Allow images in the text?
     *                              On FALSE, uses links to images.
     * @return  string
     */
    public function xoopsCodeDecode($text, $allowimage = 1)
    {
        $patterns = [];

        $replacements = [];

        //$patterns[] = "/\[code](.*)\[\/code\]/esU";

        //$replacements[] = "'<div class=\"xoopsCode\"><code><pre>'.wordwrap(mbbsTextSanitizer::htmlSpecialChars('\\1'), 100).'</pre></code></div>'";

        // RMV: added new markup for intrasite url (allows easier site moves)

        // TODO: automatically convert other URLs to this format if XOOPS_URL matches??

        $patterns[] = "/\[siteurl=(['\"]?)([^\"'<>]*)\\1](.*)\[\/siteurl\]/sU";

        $replacements[] = '<a href="' . XOOPS_URL . '/\\2" target="_blank"><img src="' . XOOPS_URL . '/modules/mbbs/images/h.gif" alt=""></a>';

        #		$replacements[] = '<a href="'.XOOPS_URL.'/\\2" target="_blank">\\3</a>';

        $patterns[] = "/\[url=(['\"]?)(http[s]?:\/\/[^\"'<>]*)\\1](.*)\[\/url\]/sU";

        $replacements[] = '<a href="\\2" target="_blank"><img src="' . XOOPS_URL . '/modules/mbbs/images/h.gif" alt=""></a>';

        #		$replacements[] = '<a href="\\2" target="_blank">\\3</a>';

        $patterns[] = "/\[url=(['\"]?)(ftp?:\/\/[^\"'<>]*)\\1](.*)\[\/url\]/sU";

        $replacements[] = '<a href="\\2" target="_blank"><img src="' . XOOPS_URL . '/modules/mbbs/images/h.gif" alt=""></a>';

        #		$replacements[] = '<a href="\\2" target="_blank">\\3</a>';

        $patterns[] = "/\[url=(['\"]?)([^\"'<>]*)\\1](.*)\[\/url\]/sU";

        $replacements[] = '<a href="http://\\2" target="_blank"><img src="' . XOOPS_URL . '/modules/mbbs/images/h.gif" alt=""></a>';

        #		$replacements[] = '<a href="http://\\2" target="_blank">\\3</a>';

        $patterns[] = "/\[color=(['\"]?)([a-zA-Z0-9]*)\\1](.*)\[\/color\]/sU";

        $replacements[] = '<span style="color: #\\2;">\\3</span>';

        $patterns[] = "/\[size=(['\"]?)([a-z0-9-]*)\\1](.*)\[\/size\]/sU";

        $replacements[] = '\\3';

        #		$replacements[] = '<span style="font-size: \\2;">\\3</span>';

        $patterns[] = "/\[font=(['\"]?)([^;<>\*\(\)\"']*)\\1](.*)\[\/font\]/sU";

        $replacements[] = '\\3';

        #		$replacements[] = '<span style="font-family: \\2;">\\3</span>';

        $patterns[] = "/\[email]([^;<>\*\(\)\"']*)\[\/email\]/sU";

        $replacements[] = '<a href="mailto:\\1"><img src=\"" . XOOPS_URL . "/modules/mbbs/images/m.gif\" alt=\"\"></a>';

        #		$replacements[] = '<a href="mailto:\\1">\\1</a>';

        $patterns[] = "/\[b](.*)\[\/b\]/sU";

        $replacements[] = '<b>\\1</b>';

        $patterns[] = "/\[i](.*)\[\/i\]/sU";

        $replacements[] = '<i>\\1</i>';

        $patterns[] = "/\[u](.*)\[\/u\]/sU";

        $replacements[] = '<u>\\1</u>';

        $patterns[] = "/\[d](.*)\[\/d\]/sU";

        $replacements[] = '\\1';

        #		$replacements[] = '<del>\\1</del>';

        //$patterns[] = "/\[li](.*)\[\/li\]/sU";

        //$replacements[] = '<li>\\1</li>';

        $patterns[] = "/\[img align=(['\"]?)(left|center|right)\\1]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";

        $patterns[] = "/\[img]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";

        $patterns[] = "/\[img align=(['\"]?)(left|center|right)\\1 id=(['\"]?)([0-9]*)\\3]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";

        $patterns[] = "/\[img id=(['\"]?)([0-9]*)\\1]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";

        $replacements[] = '<img src="' . XOOPS_URL . '/modules/mbbs/images/s.gif" alt="">';

        $replacements[] = '<img src="' . XOOPS_URL . '/modules/mbbs/images/s.gif" alt="">';

        $replacements[] = '<img src="' . XOOPS_URL . '/modules/mbbs/images/s.gif" alt="">';

        $replacements[] = '<img src="' . XOOPS_URL . '/modules/mbbs/images/s.gif" alt="">';

        #		if ($allowimage != 1) {

        #			$replacements[] = '<a href="\\3" target="_blank">\\3</a>';

        #			$replacements[] = '<a href="\\1" target="_blank">\\1</a>';

        #			$replacements[] = '<a href="'.XOOPS_URL.'/image.php?id=\\4" target="_blank">\\4</a>';

        #			$replacements[] = '<a href="'.XOOPS_URL.'/image.php?id=\\2" target="_blank">\\3</a>';

        #		} else {

        #			$replacements[] = '<img src="\\3" align="\\2" alt="">';

        #			$replacements[] = '<img src="\\1" alt="">';

        #			$replacements[] = '<img src="'.XOOPS_URL.'/image.php?id=\\4" align="\\2" alt="\\4">';

        #			$replacements[] = '<img src="'.XOOPS_URL.'/image.php?id=\\2" alt="\\3">';

        #		}

        $patterns[] = "/\[quote]/sU";

        $replacements[] = '<img src="' . XOOPS_URL . '/modules/mbbs/images/s.gif" alt="">';

        #		$replacements[] = _QUOTEC.'<div class="xoopsQuote"><blockquote>';

        //$replacements[] = 'Quote: <div class="xoopsQuote"><blockquote>';

        $patterns[] = "/\[\/quote]/sU";

        $replacements[] = '';

        #		$replacements[] = '</blockquote></div>';

        $patterns[] = '/javascript:/si';

        $replacements[] = 'java script:';

        $patterns[] = '/about:/si';

        $replacements[] = 'about :';

        return preg_replace($patterns, $replacements, $text);
    }

    /**
     * Convert linebreaks to <br> tags
     *
     * @param string $text
     *
     * @return    string
     */
    public function nl2Br($text)
    {
        return preg_replace("/(\015\012)|(\015)|(\012)/", '<br>', $text);
    }

    /**
     * Add slashes to the text if magic_quotes_gpc is turned off.
     *
     * @param string $text
     * @return  string
     **/
    public function &addSlashes($text)
    {
        if (!get_magic_quotes_gpc()) {
            $text = addslashes($text);
        }

        return $text;
    }

    /*
    * if magic_quotes_gpc is on, stirip back slashes
    *
    * @param	string  $text
    *
    * @return	string
    */

    public function &stripSlashesGPC($text)
    {
        if (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc()) {
            $text = stripslashes($text);
        }

        return $text;
    }

    /*
    *  for displaying data in html textbox forms
    *
    * @param	string  $text
    *
    * @return	string
    */

    public function htmlSpecialChars($text)
    {
        //return preg_replace("/&amp;/i", '&', htmlspecialchars($text, ENT_QUOTES));

        return preg_replace(['/&amp;/i', '/&nbsp;/i'], ['&', '&amp;nbsp;'], htmlspecialchars($text, ENT_QUOTES));
    }

    /**
     * Reverses {@link htmlSpecialChars()}
     *
     * @param string $text
     * @return  string
     **/
    public function undoHtmlSpecialChars($text)
    {
        return preg_replace(['/&gt;/i', '/&lt;/i', '/&quot;/i', '/&#039;/i'], ['>', '<', '"', "'"], $text);
    }

    /**
     * Filters textarea form data in DB for display
     *
     * @param string $text
     * @param int    $html   allow html?
     * @param int    $smiley allow smileys?
     * @param int    $xcode  allow xoopscode?
     * @param int    $image  allow inline images?
     * @param int    $br     convert linebreaks?
     * @return  string
     */
    public function &displayTarea(&$text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1)
    {
        if (1 != $html) {
            // html not allowed

            $text = $this->htmlSpecialChars($text);
        }

        $text = $this->codePreConv($text, $xcode); // Ryuji_edit(2003-11-18)

        $text = $this->makeClickable($text);

        if (0 != $smiley) {
            // process smiley

            $text = &$this->smiley($text);
        }

        if (0 != $xcode) {
            // decode xcode

            if (0 != $image) {
                // image allowed

                $text = $this->xoopsCodeDecode($text);
            } else {
                // image not allowed

                $text = $this->xoopsCodeDecode($text, 0);
            }
        }

        if (0 != $br) {
            $text = $this->nl2Br($text);
        }

        $text = $this->codeConv($text, $xcode, $image);    // Ryuji_edit(2003-11-18)

        return $text;
    }

    /**
     * Filters textarea form data submitted for preview
     *
     * @param string $text
     * @param int    $html   allow html?
     * @param int    $smiley allow smileys?
     * @param int    $xcode  allow xoopscode?
     * @param int    $image  allow inline images?
     * @param int    $br     convert linebreaks?
     * @return  string
     */
    public function &previewTarea(&$text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1)
    {
        $text = &$this->stripSlashesGPC($text);

        if (1 != $html) {
            // html not allowed

            $text = $this->htmlSpecialChars($text);
        }

        $text = $this->codePreConv($text, $xcode); // Ryuji_edit(2003-11-18)

        $text = $this->makeClickable($text);

        if (0 != $smiley) {
            // process smiley

            $text = &$this->smiley($text);
        }

        if (0 != $xcode) {
            // decode xcode

            if (0 != $image) {
                // image allowed

                $text = $this->xoopsCodeDecode($text);
            } else {
                // image not allowed

                $text = $this->xoopsCodeDecode($text, 0);
            }
        }

        if (0 != $br) {
            $text = $this->nl2Br($text);
        }

        $text = $this->codeConv($text, $xcode, $image);    // Ryuji_edit(2003-11-18)

        return $text;
    }

    /**
     * Replaces banned words in a string with their replacements
     *
     * @param string $text
     * @return  string
     *
     * @deprecated
     **/
    public function &censorString(&$text)
    {
        if (!isset($this->censorConf)) {
            $configHandler = xoops_getHandler('config');

            $this->censorConf = $configHandler->getConfigsByCat(XOOPS_CONF_CENSOR);
        }

        if (1 == $this->censorConf['censor_enable']) {
            $replacement = $this->censorConf['censor_replace'];

            foreach ($this->censorConf['censor_words'] as $bad) {
                if (!empty($bad)) {
                    $bad = quotemeta($bad);

                    $patterns[] = "/(\s)" . $bad . '/siU';

                    $replacements[] = '\\1' . $replacement;

                    $patterns[] = '/^' . $bad . '/siU';

                    $replacements[] = $replacement;

                    $patterns[] = "/(\n)" . $bad . '/siU';

                    $replacements[] = '\\1' . $replacement;

                    $patterns[] = '/]' . $bad . '/siU';

                    $replacements[] = ']' . $replacement;

                    $text = preg_replace($patterns, $replacements, $text);
                }
            }
        }

        return $text;
    }

    /**#@+
     * Sanitizing of [code] tag
     * @param     $text
     * @param int $xcode
     * @return string|string[]|null
     */

    public function codePreConv($text, $xcode = 1)
    {
        if (0 != $xcode) {
            $patterns = "/\[code](.*)\[\/code\]/esU";

            $replacements = "'<img src=\"" . XOOPS_URL . "/modules/mbbs/images/s.gif\" alt=\"\">'";

            #			$replacements = "'[code]'.base64_encode('$1').'[/code]'";

            $text = preg_replace($patterns, $replacements, $text);
        }

        return $text;
    }

    public function codeConv($text, $xcode = 1, $image = 1)
    {
        if (0 != $xcode) {
            $patterns = "/\[code](.*)\[\/code\]/esU";

            if (0 != $image) {
                // image allowed

                $replacements = "'<img src=\"" . XOOPS_URL . "/modules/mbbs/images/s.gif\" alt=\"\">'";

            #				$replacements = "'<div class=\"xoopsCode\"><code><pre>'.mbbsTextSanitizer::codeSanitizer('$1').'</pre></code></div>'";
                //$text =& $this->xoopsCodeDecode($text);
            } else {
                // image not allowed

                $replacements = "'<img src=\"" . XOOPS_URL . "/modules/mbbs/images/s.gif\" alt=\"\">'";

                #				$replacements = "'<div class=\"xoopsCode\"><code><pre>'.mbbsTextSanitizer::codeSanitizer('$1', 0).'</pre></code></div>'";
                //$text =& $this->xoopsCodeDecode($text, 0);
            }

            $text = preg_replace($patterns, $replacements, $text);
        }

        return $text;
    }

    public function codeSanitizer($str, $image = 1)
    {
        if (0 != $image) {
            $str = $this->xoopsCodeDecode(
                $this->htmlSpecialChars(str_replace('\"', '"', base64_decode($str, true)))
            );
        } else {
            $str = $this->xoopsCodeDecode(
                $this->htmlSpecialChars(str_replace('\"', '"', base64_decode($str, true))),
                0
            );
        }

        return $str;
    }

    /**#@-*/

    ##################### Deprecated Methods ######################

    /**#@+
     * @param     $text
     * @param int $allowhtml
     * @param int $smiley
     * @param int $bbcode
     * @return string
     * @deprecated
     */

    public function sanitizeForDisplay($text, $allowhtml = 0, $smiley = 1, $bbcode = 1)
    {
        if (0 == $allowhtml) {
            $text = $this->htmlSpecialChars($text);
        } else {
            //$config =& $GLOBALS['xoopsConfig'];

            //$allowed = $config['allowed_html'];

            //$text = strip_tags($text, $allowed);

            $text = $this->makeClickable($text);
        }

        if (1 == $smiley) {
            $text = $this->smiley($text);
        }

        if (1 == $bbcode) {
            $text = $this->xoopsCodeDecode($text);
        }

        $text = $this->nl2Br($text);

        return $text;
    }

    public function sanitizeForPreview($text, $allowhtml = 0, $smiley = 1, $bbcode = 1)
    {
        $text = $this->stripSlashesGPC($text);

        if (0 == $allowhtml) {
            $text = $this->htmlSpecialChars($text);
        } else {
            //$config =& $GLOBALS['xoopsConfig'];

            //$allowed = $config['allowed_html'];

            //$text = strip_tags($text, $allowed);

            $text = $this->makeClickable($text);
        }

        if (1 == $smiley) {
            $text = $this->smiley($text);
        }

        if (1 == $bbcode) {
            $text = $this->xoopsCodeDecode($text);
        }

        $text = $this->nl2Br($text);

        return $text;
    }

    public function makeTboxData4Save($text)
    {
        //$text = $this->undoHtmlSpecialChars($text);

        return $this->addSlashes($text);
    }

    public function makeTboxData4Show($text, $smiley = 0)
    {
        $text = $this->htmlSpecialChars($text);

        return $text;
    }

    public function makeTboxData4Edit($text)
    {
        return $this->htmlSpecialChars($text);
    }

    public function makeTboxData4Preview($text, $smiley = 0)
    {
        $text = $this->stripSlashesGPC($text);

        $text = $this->htmlSpecialChars($text);

        return $text;
    }

    public function makeTboxData4PreviewInForm($text)
    {
        $text = $this->stripSlashesGPC($text);

        return $this->htmlSpecialChars($text);
    }

    public function makeTareaData4Save($text)
    {
        return $this->addSlashes($text);
    }

    public function &displayTarea(&$text, $html = 1, $smiley = 1, $xcode = 1)
    {
        return $this->displayTarea($text, $html, $smiley, $xcode);
    }

    public function makeTareaData4Edit($text)
    {
        return $this->htmlSpecialChars($text);
    }

    public function &makeTareaData4Preview(&$text, $html = 1, $smiley = 1, $xcode = 1)
    {
        return $this->previewTarea($text, $html, $smiley, $xcode);
    }

    public function makeTareaData4PreviewInForm($text)
    {
        //if magic_quotes_gpc is on, do stipslashes

        $text = $this->stripSlashesGPC($text);

        return $this->htmlSpecialChars($text);
    }

    public function makeTareaData4InsideQuotes($text)
    {
        return $this->htmlSpecialChars($text);
    }

    public function &oopsStripSlashesGPC($text)
    {
        return $this->stripSlashesGPC($text);
    }

    public function &oopsStripSlashesRT($text)
    {
        if (get_magic_quotes_runtime()) {
            $text = stripslashes($text);
        }

        return $text;
    }

    public function &oopsAddSlashes($text)
    {
        return $this->addSlashes($text);
    }

    public function oopsHtmlSpecialChars($text)
    {
        return $this->htmlSpecialChars($text);
    }

    public function oopsNl2Br($text)
    {
        return $this->nl2Br($text);
    }

    /**#@-*/
}
