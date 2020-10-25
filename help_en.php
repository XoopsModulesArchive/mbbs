<?php

require dirname(__DIR__, 2) . '/mainfile.php';

echo "<html>\n";
echo "<head>\n";
echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=<{$xoops_charset}>\">\n";
echo "<meta http-equiv=\"content-language\" content=\"<{$xoops_langcode}>\">\n";
echo "<title>miniBBS Help</title>\n";
echo "</head>\n";
echo "<body>\n";
echo "<ul style=\"font-size: 12px;\">\n";
echo "  <li>You can input message to 500 letters.</li>\n";
echo '  <li>Mail address is converted to <img src="' . XOOPS_URL . "/modules/mbbs/images/m.gif\" alt=\"\"> automatically.</li>\n";
echo '  <li>URL address is converted to <img src="' . XOOPS_URL . "/modules/mbbs/images/h.gif\" alt=\"\"> automatically.</li>\n";
echo "  <li>You can't use [code], [quote] and [img] etc. These become NG.</li>\n";
echo '  <li>NG codes are converted to <img src="' . XOOPS_URL . "/modules/mbbs/images/s.gif\" alt=\"\"> automatically.</li>\n";
echo "  <li>You can use [b], [i], [u] and [color].</li>\n";
echo "</ul>\n";
echo "</body>\n";
echo "</html>\n";
