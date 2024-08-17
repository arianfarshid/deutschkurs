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

        $data = array();

        try {
            // Verwende PDO für vorbereitete Anweisung
            $stmt = $this->_database->prepare('SELECT * FROM Woerter WHERE Begriff = :search_value');
            $stmt->bindValue(':search_value', $this->search_value, PDO::PARAM_STR);
            $stmt->execute();

            // Ergebnisse verarbeiten
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data = array(
                    "Begriff" => $row["Begriff"],
                    "Artikel" => $row["Artikel"],
                    "Satz" => $row["Satz"],
                    "PersischerSatz" => $row["PersischerSatz"],
                    "Persisch" => $row["Persisch"]
                );
            }
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo "Fehler bei der Abfrage: " . $e->getMessage();
        }

        return $data;
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
