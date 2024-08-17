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

        try {
            // Verwende PDO für vorbereitete Anweisung
            $stmt = $this->_database->prepare('SELECT Persisch, Satz, PersischerSatz FROM Woerter WHERE Begriff = :search_value');
            $stmt->bindValue(':search_value', $this->searched, PDO::PARAM_STR);
            $stmt->execute();

            // Ergebnisse verarbeiten
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data = array(
                    "Persisch" => $row["Persisch"],
                    "Satz" => $row["Satz"],
                    "PersischerSatz" => $row["PersischerSatz"]
                );
            }
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo "Fehler bei der Abfrage: " . $e->getMessage();
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
