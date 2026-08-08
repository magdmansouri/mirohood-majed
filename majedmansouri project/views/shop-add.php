<?php
try { verify_csrf(); $id=(int)($_POST['id']??0); $qty=max(1,(int)($_POST['qty']??1)); $stmt=db()->prepare('SELECT id FROM shop_products WHERE id=? AND status=1'); $stmt->execute([$id]); if(!$stmt->fetch()) throw new RuntimeException('محصول پیدا نشد'); add_cart($id,$qty); } catch(Throwable $e) { $_SESSION['flash']=$e->getMessage(); }
redirect('shop/cart');
