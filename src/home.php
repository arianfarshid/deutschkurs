<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
require_once './Page.php';

class home extends Page
{
    protected $index;
    protected $message;
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
        $result = array();
        $json = file_get_contents('woerter.json');
        $json_data = json_decode($json, true);


        if (is_array($json_data) && isset($json_data['Woerterbuch'])) {
            $random_key = array_rand($json_data['Woerterbuch']);
            $random_entry = $json_data['Woerterbuch'][$random_key];

            $result = array(
                'Begriff' => $random_entry['Begriff'],
                'Artikel' => $random_entry['Artikel'],
                'Satz' => $random_entry['Satz']
            );
        }

        return $result;
    }


    protected function generateView():void
    {
        $data = $this->getViewData();
        $this->generatePageHeader('Homepage','home.css');
        echo <<< HTML
            <nav>
                <img src="logo.jpg" alt="" id="logo">
                <a href="home.php?id=artikel" id="navArtikel">Deutsche Artikel</a>
                <a href="home.php?id=uebersetzer" id="navUebersetzer">Übersetzer</a>
                <a href="home.php?id=Grammatik" id="navGrammatik">Grammatik</a>
                <a href="home.php?id=rechtschreibung" id="navRechtschreibung">Rechtschreibung</a>
            </nav>
            <header>
                <h1>Deutschkurs</h1>
            </header>
        HTML;

        if(isset($_SESSION['answer'])){
            $this->message = $_SESSION['answer'];
        }


        switch($this->index){
            case "artikel":
                echo <<< EOT
                    <script src="suche.js"></script>
                    <section class="container">
                        <section class="artikelsuche">
                            <h2>Artikelsuche</h2>
                            <form action="home.php?id=artikel" method="post">
                                <input type="text" name="suche" id="userInput" placeholder="Suche nach...">
                            </form>
                            <p><span id="begriff"></span><span id="artikel"></span></p>
                            <p id="satz"></p>
                        </section>
                        <section class="artikelQuiz">
                            <h2>Artikel-Quiz</h2>
                            <p>Wie lautet der Artikel von <span id="quizBegriff">{$data['Begriff']}</span>?</p>
                            <p>
                                <form action="home.php?id=artikel" method="post" id="radioForm" >
                                    <input type="radio" name="artikelRadio" id="artikelDer" value="der" onclick="document.forms['radioForm'].submit()">
                                    <label for="artikelDer">Der</label>
                                    <input type="radio" name="artikelRadio" id="artikelDie" value="die" onclick="document.forms['radioForm'].submit()">
                                    <label for="artikelDie">Die</label>
                                    <input type="radio" name="artikelRadio" id="artikelDas" value="das" onclick="document.forms['radioForm'].submit()">
                                    <label for="artikelDas">Das</label>
                                    <input type="hidden" name="begriffHidden" value="{$data['Begriff']}">
                                </form>
                            </p>
                            {$this->message}
                        </section>
                    </section>
                EOT;
                break;
            case "uebersetzer":
                echo <<< EOT
                <script src="uebersetzer.js"></script>
                    <section class="container">
                        <section class="wortUebersetzer">
                            <h2>Wortübersetzer</h2>
                            <form action="home.php?id=uebersetzer" method="post" accept-charset="UTF-8">
                                <input type="text" name="uebersetzungWort" id="gesuchtesWort" placeholder="Suche nach...">
                            </form>
                            <p>Persische Übersetzung: <span id="uebersetzung"></span></p>
                            <p><span id="deutscherSatz"></span></p>
                            <p><span id="persischerSatz"></span></p>
                        </section>
                    </section>
                EOT;
                break;
            default:
                echo <<< EOT
                    
                EOT;
        }

        echo <<< EOT
            <footer>
                <p>&copy; Arian Farzad</p>
            </footer>
        EOT;


        $this->generatePageFooter();
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();
        if(isset($_GET['id'])){
            echo "<script src='navigation.js'></script>";
            $this->index = $_GET['id'];
            echo "<input type='hidden' name='index' id='index' value='{$this->index}'>";
        }
        if(isset($_POST['artikelRadio']) && isset($_POST['begriffHidden'])){
            $radio = $_POST['artikelRadio'];
            $begriff = $_POST['begriffHidden'];

            $json_data = json_decode(file_get_contents('woerter.json'), true);
            $data = array();
            if (is_array($json_data) && isset($json_data['Woerterbuch'])) {
                foreach ($json_data['Woerterbuch'] as $entry) {
                    if ($entry['Begriff'] == $begriff) {
                        $data = array(
                            'Begriff' => $entry['Begriff'],
                            'Artikel' => $entry['Artikel'],
                            'Satz' => $entry['Satz']
                        );
                        break;
                    }
                }
            }



            if ($data['Artikel'] == $radio) {
                $_SESSION['answer'] = "<p style='color: green'>Richtig, der Artikel von {$begriff} ist: {$data['Artikel']}</p>";
            } else {
                $_SESSION['answer'] = "<p style='color: red'>Leider Falsch, der Artikel von {$begriff} ist: {$data['Artikel']}</p>";
            }


            header("Location: home.php?id=".$this->index);
        }
    }

    public static function main():void
    {
        try {
            session_start();
            $page = new home();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

home::main();
