<?php
$products = [
    "Молоко пастеризоване" => 12,
    "Хліб чорний" => 9,
    "Сир білий" => 21,
    "Сметана 20%" => 25,
    "Кефір 1%" => 19,
    "Вода газована" => 18,
    "Печиво \"Весна\"" => 14
];

$currentMenuState = 'main';

$my_card = [];
$current_product = '';

$userName = '';
$userAge = 0;

function showShopTitle() {
	echo "################################\n";
    echo "# ПРОДОВОЛЬЧИЙ МАГАЗИН \"ВЕСНА\" #\n";
    echo "################################\n";
}

function mainMenu() {
    echo "1 Вибрати товари\n";
    echo "2 Отримати підсумковий рахунок\n";
    echo "3 Налаштувати свій профіль\n";
    echo "0 Вийти з програми\n";
    echo "Введіть команду: ";
    setMenuState('main');
}

function shopCatalog() {
	global $products;

	echo "№   НАЗВА                     ЦІНА\n";

	$counter = 1;
	$width = 27;

	foreach ($products as $product => $price) {
		$length = iconv_strlen($product, 'UTF-8');
		$padding = $width - $length;
		if($price < 10) $padding--;
	    printf("%-3d %s %{$padding}d\n", $counter, $product, $price);
	    $counter++;
	}

	echo "-----------\n";
	echo "0  ПОВЕРНУТИСЯ\n";
	echo "Виберіть товар: ";

	setMenuState('catalog');
}

function setMenuState($state) {
    global $currentMenuState;
    $currentMenuState = $state;
}

function getMenuState() {
    global $currentMenuState;
    return $currentMenuState;
}

function getProductById($product_id) {
	global $products;
	global $current_product;
	global $my_card;
	$indexedProducts = array_values($products);

	if (is_numeric($product_id) && $product_id >= 1 && $product_id <= count($indexedProducts)) {
	    $chosenProduct = array_keys($products)[$product_id - 1];
	    $chosenPrice = $indexedProducts[$product_id - 1];
	    echo "Обрано: $chosenProduct\n";
	    echo "Введіть кількість, штук: ";
	    setMenuState('order');
	    $current_product = $chosenProduct;
	    $my_card[$chosenProduct] = 1;
	} else {
	    echo "Товар не існує. Оберіть інший товар: ";
	}
}

function shopCard() {
	global $products;
	global $my_card;

	if(empty($my_card)) {
		echo "КОШИК ПОРОЖНІЙ\n";
	} else {
		echo "У КОШИКУ:\n";
		echo "НАЗВА                     КІЛЬКІСТЬ\n";

		$width = 27;

		foreach ($my_card as $product => $count) {
			$length = iconv_strlen($product, 'UTF-8');
			$padding = $width - $length;
			if($count < 10) $padding--;
		    printf("%s %{$padding}d\n", $product, $count);
		}
	}
}

function addProductToCard($count) {
	global $current_product;
	global $my_card;

	if($count < 0 || $count > 100) {
		echo "Вказана невірна кількість, спробуйте ще раз: ";
	}
	else if($count == 0) {
		unset($my_card[$current_product]);
		echo "ВИДАЛЯЮ З КОШИКА\n";
		shopCard();
		shopCatalog();
	}
	else {
		$my_card[$current_product] = $count;
		shopCard();
		shopCatalog();
	}
}

function shopBill() {
	global $my_card;
	global $products;

	echo "№  НАЗВА                 ЦІНА  КІЛЬКІСТЬ  ВАРТІСТЬ\n";
	$counter = 1;
	$width = 22;
	$total_price = 0;

	foreach ($my_card as $product => $count) {
		$length = iconv_strlen($product, 'UTF-8');
		$padding = $width - $length;
		$price = $products[$product];

		if($price > 10) $padding++;
		$padding2 = 6 - strlen((string)abs($price));
		if($count > 10) $padding2++;
		$padding3 = 11 - strlen((string)abs($count));
		$padding3 += strlen((string)abs($price * $count)) - 1;

	    printf("%-2d %s %{$padding}d %{$padding2}d %{$padding3}d\n", 
	    	$counter, $product, $price, $count, $price * $count);
	    $counter++;
	    $total_price += $price * $count;
	}
	echo "РАЗОМ ДО CПЛАТИ: {$total_price}\n";
	echo "Введіть команду: ";
}

function accountSettings() {
	global $userName;
	global $userAge;

	if(getMenuState() != 'ageEdit')
		setMenuState('loginEdit');

	if(getMenuState() == 'loginEdit') {
		echo "Ваше імʼя: ";
	} else if(getMenuState() == 'ageEdit') {
		echo "Ваш вік: ";
	}
}

function editAccountSettings($data) {
	global $userName;
	global $userAge;

	if(getMenuState() != 'ageEdit')
		setMenuState('loginEdit');

	$length = iconv_strlen($data, 'UTF-8');

	if(getMenuState() == 'loginEdit') {
		if($length >= 1 && $length != '') {
			$userName = $data;
			setMenuState('ageEdit');
			accountSettings();
		} else {
			echo "Помилка, ви ввели недійсне ім'я\n";
			accountSettings();
		}
	} else if(getMenuState() == 'ageEdit') {
		if(!is_numeric($data)) {
			echo "Помилка, ви ввели недійсний рік\n";
			accountSettings();
		}
		if($data < 7 || $data > 150) {
			echo "Помилка, ви ввели недійсний рік\n";
			accountSettings();
		} else {
			$userAge = $data;
			echo "ДАНІ ЗМІНЕНІ\n";
			echo "Ім'я: {$userName}\n";
			echo "Вік: {$userAge}\n";
			mainMenu();
		}
	}
}

showShopTitle();
mainMenu();

$exit = false;

while($exit == false) {
	$command = trim(fgets(STDIN));

	switch (getMenuState()) {
		case 'main':
			{
				switch ($command) {
				    case '1':
				        shopCatalog();
				        break;
				    case '2':
				        shopBill();
				        break;
				    case '3':
				        accountSettings();
				        break;
				    case '0':
				        echo "Вихід з програми...\n";
				        $exit = true;
				        break;
				    default:
				        echo "ПОМИЛКА! Введіть правильну команду\n";
				        mainMenu();
				        break;
				}
			}
			break;
		case 'catalog':
			{
				switch ($command) {
					case '0':
						showShopTitle();
						mainMenu();
						break;
					default:
						getProductById($command);
						break;
				}
			}
			break;
		case 'order':
			{
				addProductToCard($command);
			}
			break;
		case 'loginEdit':
			{
				editAccountSettings($command);
			}
			break;
		case 'ageEdit':
			{
				editAccountSettings($command);
			}
			break;
	}
}

?>