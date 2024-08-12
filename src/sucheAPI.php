<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
require_once './Page.php';

class sucheAPI extends Page
{
    protected $search_value = '';
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
        if (isset($_GET['word'])) {
            $this->search_value = htmlspecialchars($_GET['word']);
        }

        $result = array();
        $json = file_get_contents('woerter.json');
        $json_data = json_decode($json, true);

        if (is_array($json_data) && isset($json_data['Woerterbuch'])) {
            foreach ($json_data['Woerterbuch'] as $entry) {
                if ($entry['Begriff'] == $this->search_value) {
                    $result = array(
                        'Begriff' => $entry['Begriff'],
                        'Artikel' => $entry['Artikel'],
                        'Satz' => $entry['Satz']
                    );
                    break;
                }
            }
        }

        return $result;
    }

    protected function generateView():void
    {
        $data = $this->getViewData();
        header("Content-Type: application/json; charset=UTF-8");

        echo json_encode($data);
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();

    }

    public static function main():void
    {
        try {
            $page = new sucheAPI();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

sucheAPI::main();
