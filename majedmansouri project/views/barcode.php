<?php
$id=(int)($_GET['id']??0);$s=db()->prepare('SELECT barcode FROM cafe_items WHERE id=?');$s->execute([$id]);$code=(string)$s->fetchColumn();if(!$code){http_response_code(404);exit;}
header('Content-Type: image/svg+xml; charset=utf-8');$x=10;echo '<svg xmlns="http://www.w3.org/2000/svg" width="220" height="62" viewBox="0 0 220 62"><rect width="220" height="62" fill="#f4f1ea"/>';
foreach(str_split($code) as $ch){$n=ord($ch);for($i=0;$i<4;$i++){$w=(($n>>($i*2))&3)+1;echo '<rect x="'.$x.'" y="5" width="'.$w.'" height="42" fill="#0a0908"/>'; $x+=$w+2;}}echo '<text x="110" y="58" text-anchor="middle" font-family="monospace" font-size="9" fill="#0a0908">'.e($code).'</text></svg>';
