<?php require __DIR__ . '/bootstrap.php';
$where=['c.status="active"']; $params=[];
foreach(['brand'=>'brand','transmission'=>'transmission'] as $key=>$field){if(!empty($_GET[$key])){$where[]="c.$field=?";$params[]=$_GET[$key];}}
if(!empty($_GET['max_price']) && is_numeric($_GET['max_price'])){$where[]='c.price<=?';$params[]=$_GET['max_price'];}
if(!empty($_GET['year']) && ctype_digit($_GET['year'])){$where[]='c.year>=?';$params[]=$_GET['year'];}
$sql='SELECT c.*,u.name owner_name,(SELECT image_path FROM car_images WHERE car_id=c.id ORDER BY sort_order,id LIMIT 1) image_path FROM cars c JOIN users u ON u.id=c.owner_id WHERE '.implode(' AND ',$where).' ORDER BY c.created_at DESC LIMIT 30';
$q=db()->prepare($sql);$q->execute($params);$cars=$q->fetchAll();
page_top('Find used cars'); ?>
<section class="hero"><h1>Find your next dealer car</h1><p class="muted">Browse active dealer listings and contact the owner directly.</p></section>
<form class="filters" method="get"><input name="brand" placeholder="Brand" value="<?=e($_GET['brand']??'')?>"><input name="max_price" type="number" placeholder="Maximum price" value="<?=e($_GET['max_price']??'')?>"><input name="year" type="number" placeholder="Minimum year" value="<?=e($_GET['year']??'')?>"><select name="transmission"><option value="">Any transmission</option><?php foreach(['Manual','Automatic','AMT'] as $v): ?><option <?=$v===($_GET['transmission']??'')?'selected':''?>><?=$v?></option><?php endforeach ?></select><button class="button">Search</button></form>
<section class="grid"><?php foreach($cars as $car): ?><article class="card"><img loading="lazy" src="<?=car_image($car)?>" alt="<?=e($car['title'])?>"><div class="card-body"><h2><?=e($car['title'])?></h2><div class="price">₹<?=number_format((float)$car['price'])?></div><p class="muted"><?=e($car['year'])?> · <?=e($car['mileage']?:'Mileage not listed')?> km · <?=e($car['transmission'])?></p><a class="button small" href="car-details.php?id=<?=$car['id']?>">View details</a></div></article><?php endforeach; if(!$cars): ?><p>No matching cars yet.</p><?php endif ?></section><?php page_bottom();
