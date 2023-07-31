<?php
namespace Gemstone;
require __DIR__ . '/gemsecure/GemstoneLoader.php';
class main{
    private $instance;
    protected $Diamond;
    protected $Ruby;
    protected $Saphire;
    protected $Lapiz;
    public function __construct()
    {
        $this->instance = new \gemstone\gemsecure\GemstoneLoader($this->loadconfig(), 'log/config.php', 'log/values.json');
    }

    public function loadconfig() {
        require __DIR__ . "/gemsecure/log/config.php";
        $data = $config;
        // get independence value
        $this->Diamond = $data['Diamond'];
        $this->Ruby = $data['Ruby'];
        $this->Saphire = $data['Sapphire'];
        $this->Lapiz = $data['Lapis Lazuli'];
    }

    public function reGemstone() {
        $this->instance->ReGem();
    }

    public function ed($key, array $data = []) {
        switch ($key) {
            case 'Diamond' :
               return $this->instance->EncGem($data, $this->Diamond);
                break;
            case 'Ruby' :
                return $this->instance->EncGem($data, $this->Ruby);
                break;
            case 'Sapphire':
                return $this->instance->EncGem($data, $this->Saphire);
                break;
            case 'Lapis Lazuli':
                return $this->instance->EncGem($data, $this->Lapiz);
                break;
            default :
                echo "use valid salt type only use Diamond, Ruby, Sapphire, Lapis Lazuli";
        }
    }

    public function dd($key, $data) {
        switch ($key) {
            case 'Diamond' :
                return $this->instance->DecGem($data, $this->Diamond);
                break;
            case 'Ruby' :
                return $this->instance->DecGem($data, $this->Ruby);
                break;
            case 'Sapphire':
                return $this->instance->DecGem($data, $this->Saphire);
                break;
            case 'Lapis Lazuli':
                return $this->instance->DecGem($data, $this->Lapiz);
                break;
            default :
                echo "use valid salt type only use Diamond, Ruby, Sapphire, Lapis Lazuli";
        }
    }
}


$instance= new main();
$instance->reGemstone();
//  $dataArray = array(
//      'name' => 'John Doe',
//      'age' => 30,
//      'email' => 'johndoe@example.com'
//  );


// $encryptedData = $instance->ed('Diamond', $dataArray);

// echo $encryptedData;

// $data = $instance->dd('Diamond', $encryptedData);

// print_r($data);