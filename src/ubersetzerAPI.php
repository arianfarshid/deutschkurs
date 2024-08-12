<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
require_once './Page.php';

class uebersetzerAPI extends Page
{
    protected $searched = '';

    protected function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    protected function getViewData(): array
    {
        if (isset($_GET['searched'])) {
            $this->searched = htmlspecialchars($_GET['searched']);
        }

        $data = array();

        $json = json_decode(file_get_contents('woerter.json'), true);
        if(is_array($json) && isset($json['Woerterbuch'])) {
            foreach($json['Woerterbuch'] as $woerterbuch) {
                if($woerterbuch['Begriff'] == $this->searched) {
                    $data = array(
                        'Persisch' => $woerterbuch['Persisch'],
                        'Satz' => $woerterbuch['Satz'],
                        'PersischerSatz' => $woerterbuch['PersischerSatz']
                    );
                    break;
                }
            }
        }


        return $data;
    }

    protected function generateView(): void
    {
        $data = $this->getViewData();
        header("Content-Type: application/json; charset=UTF-8");

        echo json_encode($data);
    }

    protected function processReceivedData(): void
    {
        parent::processReceivedData();
    }

    public static function main(): void
    {
        try {
            $page = new uebersetzerAPI();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-Type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

uebersetzerAPI::main();
