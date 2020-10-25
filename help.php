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
echo "  <li>メッセージの文字数は全角 250 文字まで記入できます。</li>\n";
echo '  <li>メールアドレスを記入すると自動で <img src="' . XOOPS_URL . "/modules/mbbs/images/m.gif\" alt=\"\"> に変換されます。</li>\n";
echo '  <li>URL を記入すると自動で <img src="' . XOOPS_URL . "/modules/mbbs/images/h.gif\" alt=\"\"> に変換されます。</li>\n";
echo "  <li>[code], [quote], [img] などは NG コードです。</li>\n";
echo '  <li>NG コードを使用すると <img src="' . XOOPS_URL . "/modules/mbbs/images/s.gif\" alt=\"\"> に変換されます。</li>\n";
echo "  <li>その他、使用可能なコードは [b], [i], [u], [color] などがあります。</li>\n";
echo "</ul>\n";
echo "</body>\n";
echo "</html>\n";
