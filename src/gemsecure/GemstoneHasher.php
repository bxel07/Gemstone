<?php


class GemstoneHasher
{
    private $jsonFile;
    private $configFile;
    private $data;

    public function __construct()
    {
        $directorypath = __DIR__."/log/";

        if(!is_dir($directorypath)) {
            mkdir($directorypath);
        }
        $this->jsonFile = $directorypath;
        $this->configFile = $directorypath;
    }

    public function readJsonData()
    {
        $jsonString = file_get_contents($this->jsonFile."values.json");
        $this->data = json_decode($jsonString, true);

        if (!isset($this->data['rounded_values'])) {
            throw new RuntimeException('Invalid JSON format. "rounded_values" key not found.');
        }
    }

    public function hashRoundedValues()
    {
        if (!$this->data) {
            $this->readJsonData();
        }

        $roundedValues = $this->data['rounded_values'];
        $hashedRoundedValues = array_map(function ($value) {
            return $this->hashSha256($value);
        }, array_values($roundedValues));

        $this->data['hashed_rounded_values'] = array_combine(array_keys($roundedValues), $hashedRoundedValues);
    }

    private function hashSha256($value)
    {
        return hash('sha256', $value);
    }

    public function storeHashedValues()
    {
        if (!isset($this->data['hashed_rounded_values'])) {
            $this->hashRoundedValues();
        }

        $configContent = '<?php' . PHP_EOL . '$config = ' . var_export($this->data['hashed_rounded_values'], true) . ';' . PHP_EOL;
        file_put_contents($this->configFile."config.php", $configContent);
    }
}

//$hasher = new GemstoneHasher();
//$hasher->storeHashedValues();

// // Output a success message
// echo 'Hashed rounded values have been stored in config.php';
?>
