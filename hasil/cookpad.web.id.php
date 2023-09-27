<?php 
require_once 'dom.php';
$url = 'https://cookpad.web.id/resep/18-steak-tempe-saus-lada-hitam-hanya-14-bahan';
$html = file_get_html($url);
$h1 = $html->find('h1',0)->plaintext;
echo $h1;
?>