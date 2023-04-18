<?php

declare(strict_types=1);

error_reporting(E_ALL);
// Вывод ошибок 
ini_set('display_errors', 0);
// Включение лога ошибок и указания файла для записи.
ini_set('log_errors', 'On');
ini_set('error_log', 'logs.log');


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

	// Проверка наличия следующего маршрута
	function getNextWay(array $StartPoint,  array $listWays): int
	{
		$i = 0;
		foreach ($listWays as $point) {
			if ($point['from'] === $StartPoint['to']) {
				$i++;
			}
		}
		return $i;
	}

	// Рекурсивная функция обхода массива в поисках всех маршрутов
	function getWays(array $data, array $query): array
	{
		$result[] = $query;

		$positive = array_filter($data, function ($number) use ($query) {
			if ($number['from'] == $query['to'] && strtotime($number['depart']) > strtotime($query['arrival'])) {
				return true;
			}
		});

		$max = array_reduce($positive, function ($acc, $item) {
			if (isset($acc["arrival"])) {
				if (strtotime($acc["arrival"]) < strtotime($item["arrival"])) {
					$acc = $item;
				}
			} else {
				$acc = $item;
			}
			return $acc;
		});

		if (isset($max) && !empty($max) && (getNextWay($max, $data) > 0)) {
			$arr = getWays($data, $max);
			$result = array_merge($result, $arr);
		} elseif ($max) {
			$result[] = $max;
		}
		return $result;
	}

	// Получение всех возможных марштуров
	$result = [];
	foreach ($flights as $key => $item) {
		$result[] = getWays($flights, $item);
	}

	// Функция рассчета длительности всего маршрута
	function delay(array $array): int
	{
		return abs(strtotime($array[0]['depart']) - strtotime($array[count($array) - 1]['arrival']));
	}

	$wayTime = array_map("delay", $result);
	arsort($wayTime); // Сохраняет соотвествие значения и ключа
	$keyLong = array_key_first($wayTime);

	echo "По указанному набору рейсов самый продолжительный маршрут будет такой:" . PHP_EOL;
	foreach ($result[$keyLong] as $key => $value) {
		$key++;
		echo $key . ")" . $value['from'] . " -> " . $value['to'] . " " . $value['depart'] . " " . $value['arrival'] . PHP_EOL;
	}
	echo "Итого: c " . $result[$keyLong][0]['depart'] . " по " . $result[$keyLong][0]['arrival'] . PHP_EOL;
} catch (Throwable $e) {
	error_log("ERROR: " . $e->getCode() . " " . $e->getMessage());
}
