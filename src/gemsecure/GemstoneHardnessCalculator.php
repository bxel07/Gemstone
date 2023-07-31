<?php

require __DIR__.'/../../vendor/autoload.php';
class GemstoneHardnessCalculator
{
    private $loop;
    private $gemstones;

    public function __construct()
    {
        $this->loop = \React\EventLoop\Factory::create();
        $this->initializeGemstones();
    }

    private function calculate_vickers_hardness($F, $d)
    {
        return 1.854 * ($F / pow($d, 2));
    }

    private function generateRandomRange($min, $max, $count)
    {
        if (!is_numeric($min) || !is_numeric($max) || !is_numeric($count) || $min >= $max || $count <= 0) {
            throw new InvalidArgumentException("Invalid input parameters.");
        }

        return array_map(function () use ($min, $max) {
            return rand($min, $max);
        }, range(1, $count));
    }

    private function calculateHardnessValuesAsync($data)
    {
        $promises = [];

        foreach ($data['F_range'] as $F) {
            $promises[] = \React\Promise\all(array_map(function ($d) use ($F) {
                return \React\Promise\resolve($this->calculate_vickers_hardness($F, $d));
            }, $data["d_range"]));
        }

        return \React\Promise\all($promises)->then(function ($results) use ($data) {
            $hardness_values = array_merge(...$results);
            $average_hardness = array_sum($hardness_values) / count($hardness_values);
            return $average_hardness / $data["divisor"];
        });
    }

    private function roundedvalue($data)
    {
        return $this->calculateHardnessValuesAsync($data)->then(function ($average_hardness) {
            return round($average_hardness);
        });
    }

    private function initializeGemstones()
    {
        $this->gemstones = [
            "Diamond" => ["F_range" => $this->generateRandomRange(1000, 10000, 5), "d_range" => [0.015, 0.030], "divisor" => 10],
            "Ruby" => ["F_range" => $this->generateRandomRange(500, 3000, 5), "d_range" => [0.020, 0.040], "divisor" => 9],
            "Sapphire" => ["F_range" => $this->generateRandomRange(1000, 2000, 5), "d_range" => [0.020, 0.040], "divisor" => 9],
            "Lapis Lazuli" => ["F_range" => $this->generateRandomRange(200, 500, 5), "d_range" => [0.030, 0.060], "divisor" => 5]
        ];
    }

    public function generateHashValues()
    {
        $hashPromises = [];
        $roundedPromises = [];

        foreach ($this->gemstones as $gemstone => $data) {
            $hashPromises[$gemstone] = $this->calculateHardnessValuesAsync($data);
            $roundedPromises[$gemstone] = $this->roundedvalue($data);
        }

        \React\Promise\all($hashPromises)->then(function ($hash_values) use ($roundedPromises) {
            $originalValues = [];
            foreach ($hash_values as $gemstone => $hash_value) {
                // echo "Original value for $gemstone: " . round($hash_value, 2) . PHP_EOL;
                $originalValues[$gemstone] = round($hash_value, 2);
            }

            \React\Promise\all($roundedPromises)->then(function ($rounded_values) use ($originalValues) {
                $roundedValues = [];
                foreach ($rounded_values as $gemstone => $rounded_value) {
                    // echo "Rounded value for $gemstone: " . $rounded_value . PHP_EOL;
                    $roundedValues[$gemstone] = $rounded_value;
                }

                $allValues = [
                    "original_values" => $originalValues,
                    "rounded_values" => $roundedValues
                ];

                $fullpath = __DIR__."/log/";

                if(!is_dir($fullpath)){
                    mkdir($fullpath);
                }

                $json = json_encode($allValues, JSON_PRETTY_PRINT);
                file_put_contents($fullpath.'values.json', $json);
            });
        });

        $this->loop->run();
    }
}

