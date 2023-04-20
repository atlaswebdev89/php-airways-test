<?php

declare(strict_types=1);

namespace Airways;

class AirWays
{
	protected array $waysArr=[];

    public function __construct(array $data = null)
	{
		if (!empty($data)) {
			$this->waysArr = $data;
		} else
			throw new \Exception ("Set in constructor class array air ways");
	}

	public function maxLongWay(): array
	{
		// Получение всех возможных марштуров
		$result = [];
		foreach ($this->waysArr as $item) {
			$result[] = $this->getWays($this->waysArr, $item);
		}
		$wayTime = $this->wayTime($result);
		arsort($wayTime); // Сохраняет соотвествие значения и ключа
		$keyLong = array_key_first($wayTime);
		return $result[$keyLong];
	}

	protected function getWays(array $data, array $query):array
	{
		$result[] = $query;

		$positive = array_filter($data, function ($number) use ($query) {
			if ($number['from'] == $query['to'] && strtotime($number['depart']) > strtotime($query['arrival'])) {
				return true;
			}
            return FALSE;
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

		if (!empty($max) && ($this->getNextWay($max, $data) > 0)) {
			$arr = $this->getWays($data, $max);
			$result = array_merge($result, $arr);
		} elseif ($max) {
			$result[] = $max;
		}
		return $result;
	}

	protected function getNextWay(array $StartPoint,  array $listWays): int
	{
		$i = 0;
		foreach ($listWays as $point) {
			if ($point['from'] === $StartPoint['to']) {
				$i++;
			}
		}
		return $i;
	}

	// Функция рассчета длительности всего маршрута
	protected function delay(array $array): int
	{
		return abs(strtotime($array[0]['depart']) - strtotime($array[count($array) - 1]['arrival']));
	}

	protected function wayTime(array $array): array
	{
		$result = [];
		foreach ($array as $item) {
			$result[] = $this->delay($item);
		}
		return $result;
	}

	public function viewCli(array $array):void
	{
		echo "По указанному набору рейсов самый продолжительный маршрут будет такой:" . PHP_EOL;
		foreach ($array as $key => $value) {
			$key++;
			echo $key . ")" . $value['from'] . " -> " . $value['to'] . " " . $value['depart'] . " " . $value['arrival'] . PHP_EOL;
		}
		echo "Итого: c " . $array[0]['depart'] . " по " . $array[0]['arrival'] . PHP_EOL;
	}
}
