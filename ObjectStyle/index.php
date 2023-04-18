<?php

declare(strict_types=1);
error_reporting(E_ALL);
// Вывод ошибок 
ini_set('display_errors', 0);
// Включение лога ошибок и указания файла для записи.
ini_set('log_errors', 'On');
ini_set('error_log', 'logs.log');

require_once('Classes/AirWays.php');

try {
	$flights = [
		[
			'from'    => 'VKO',
			'to'      => 'DME',
			'depart'  => '01.01.2020 12:44',
			'arrival' => '01.01.2020 13:44',
		],
		[
			'from'    => 'DME',
			'to'      => 'JFK',
			'depart'  => '02.01.2020 23:00',
			'arrival' => '03.01.2020 11:44',
		],
		[
			'from'    => 'DME',
			'to'      => 'HKT',
			'depart'  => '01.01.2020 13:40',
			'arrival' => '01.01.2020 22:22',
		],
	];

	$ways = new \Airways\Airways($flights);
	// Получаем самый продолжительный маршрут
	$maxWay = $ways->maxLongWay();
	// Выводим полученные данные
	$ways->viewCli($maxWay);
} catch (Throwable $e) {
	error_log("ERROR: " . $e->getCode() . " " . $e->getMessage());
}
