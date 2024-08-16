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
            <header>
                <h1>Deutschkurs</h1>
            </header>
            <nav>
                <img src="logo.png" id="logo" alt="">
                <section class="navigation">
                    <a href="home.php?id=artikel" id="navArtikel">Deutsche Artikel</a>
                    <a href="home.php?id=uebersetzer" id="navUebersetzer">Übersetzer</a>
                    <a href="home.php?id=Grammatik" id="navGrammatik">Grammatik</a>
                    <a href="home.php?id=rechtschreibung" id="navRechtschreibung">Rechtschreibung</a>
                </section>
                <section class="login_data">
                    <section id="user_pic_name">
                        <img src="defaultProfile.png" id="user_pic" alt="">
                        <p id="login_user_name">Name</p>
                    </section>
                    <p>Score: <span id="user_score">-</span></p>
                </section>
            </nav>
        HTML;

        if(isset($_SESSION['answer'])){
            $this->message = $_SESSION['answer'];
        }


        switch($this->index){
            case "artikel":
                $this->generateArtikelPage($data);
                break;
            case "uebersetzer":
                $this->generateTranslatorPage();
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

    private function processAddWordData():void
    {
        if(isset($_POST['begriffEingabe']) &&
            isset($_POST['persischEingabe']) &&
            isset($_POST['artikelEingabe'])){
            $begriff = htmlspecialchars($_POST['begriffEingabe']);
            $persisch = htmlspecialchars($_POST['persischEingabe']);
            $artikel = htmlspecialchars($_POST['artikelEingabe']);
            $satzEingabe = (isset($_POST['satzEingabe'])) ? htmlspecialchars($_POST['satzEingabe']) : '';
            $persischerSatzEingabe = (isset($_POST['persischerSatzEingabe'])) ? htmlspecialchars($_POST['persischerSatzEingabe']) : '';


            $neuerEintrag = [
                "Begriff" => $begriff,
                "Artikel" => $artikel,
                "Satz" => $satzEingabe,
                "Persisch" => $persisch,
                "PersischerSatz" => $persischerSatzEingabe
            ];

            $jsonDatei = 'woerter.json';
            $jsonInhalt = file_get_contents($jsonDatei);

            $woerterbuch = json_decode($jsonInhalt, true);
            $woerterbuch['Woerterbuch'][] = $neuerEintrag;

            $neuerJsonInhalt = json_encode($woerterbuch, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            file_put_contents($jsonDatei, $neuerJsonInhalt);

            header("Location: home.php?id=uebersetzer");
        }
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

        $this->processAddWordData();
    }

    private function generateArtikelPage($data):void
    {
        echo <<< EOT
                    <script src="suche.js"></script>
                    <section class="container">
                        <section class="artikelsuche">
                            <h2>Artikelsuche</h2>
                            <p>Hier können Sie nach einem deutschen Begriff suchen, und der dazugehörige Artikel wird angezeigt.</p>
                            <p class="persischerText">در اینجا می‌توانید یک واژه آلمانی را جستجو کنید و حرف تعریفی مرتبط با آن نمایش داده خواهد شد</p>
                            <form action="home.php?id=artikel" method="post">
                                <input type="text" name="suche" id="userInput" placeholder="Suche nach...">
                            </form>
                            <p><span id="begriff"></span><span id="artikel"></span></p>
                            <p id="satz"></p>
                        </section>
                        <section class="artikelQuiz">
                            <h2>Artikel-Quiz</h2>
                            <p>Hier können Sie Ihr Wissen über deutsche Artikel testen.</p>
                            <p class="persischerText">در اینجا می‌توانید دانش خود را در زمینه‌ی حروف تعریف آلمانی بیازمایید</p>
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
    }

    private function generateTranslatorPage():void
    {
        echo <<< EOT
                <script src="uebersetzer.js"></script>
                    <section class="container">
                        <section class="wortUebersetzer">
                            <h2>Wortübersetzer</h2>
                            <p>Hier können Sie deutsche Begriffe eingeben und die persische Übersetzung sowie gegebenenfalls einen Beispielsatz erhalten.</p>
                            <p class="persischerText">در اینجا می‌توانید واژگان آلمانی را وارد کرده و ترجمه فارسی آن‌ها را همراه با یک جمله نمونه، در صورت وجود، دریافت کنید</p>
                            <form action="home.php?id=uebersetzer" method="post" accept-charset="UTF-8">
                                <input type="text" name="uebersetzungWort" id="gesuchtesWort" placeholder="Suche nach...">
                            </form>
                            <p><span id="uebersetzung"></span></p>
                            <p><span id="deutscherSatz"></span></p>
                            <p><span id="persischerSatz"></span></p>
                        </section>
                        <section class="addWord">
                            <h2>Wort hinzufügen</h2>
                            <p>Falls das Wort im Wörterbuch nicht vorhanden ist, können Sie es gerne hinzufügen. Geben Sie dazu einfach die erforderlichen Daten ein.</p>
                            <p class="persischerText">اگر واژه مورد نظر در فرهنگ لغت موجود نیست، می‌توانید آن را به‌راحتی اضافه کنید. برای این کار، داده‌های لازم را وارد کنید</p>
                            <br>
                            <form action="home.php?id=uebersetzer" method="post" accept-charset="UTF-8">
                                <table>
                                    <tr>
                                        <td>Begriff</td>
                                        <td><input type="text" name="begriffEingabe" id="begriffEingabe" placeholder="Wort eingeben..." required></td>
                                    </tr>
                                    <tr>
                                        <td>Persisch</td>
                                        <td><input type="text" name="persischEingabe" id="persischEingabe" placeholder="Persische Übersetzung..." required> </td>
                                    </tr>
                                    <tr>
                                        <td>Artikel</td>
                                        <td>
                                            <input type="radio" name="artikelEingabe" id="artikelEingabeDer" value="der">
                                            <label for="artikelEingabeDer">Der</label>
                                            <input type="radio" name="artikelEingabe" id="artikelEingabeDie" value="die">
                                            <label for="artikelEingabeDie">Die</label>
                                            <input type="radio" name="artikelEingabe" id="artikelEingabeDas" value="das">
                                            <label for="artikelEingabeDas">Das</label>
                                        </td>
                                    </tr>
                                </table>
                                <br>
                                <p>
                                    Hier können Sie einen Beispielssatz eingeben: <br>
                                    <textarea name="satzEingabe" id="satzEingabe" rows="5" cols="40"></textarea>
                                </p>
                                <p>
                                    Hier können Sie die persische Übersetzung des Satzes Eingeben: <br>
                                    <textarea name="persischerSatzEingabe" id="persischerSatzEingabe" rows="5" cols="40"></textarea>
                                </p>
                                <input type="submit" value="Abschicken">
                            </form>
                        </section>
                    </section>

                    
                EOT;
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
