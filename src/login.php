<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
require_once './Page.php';

class Login extends Page
{
    protected $action;
    protected function __construct()
    {
        parent::__construct();
        if (isset($_GET['action'])) {
            $this->action = $_GET['action'];
        }
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    protected function getViewData():array
    {
        $data = array();
        try {
            $stmt = $this->_database->prepare('SELECT avatarId, pfad FROM avatar');
            $stmt->execute();

            // Ergebnisse verarbeiten
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = array(
                    "avatarId" => $row['avatarId'],
                    "pfad" => $row['pfad']
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
        $this->generatePageHeader('Login', 'login.css');
        switch ($this->action) {
            case 'login':
                echo <<< HTML
                    
                HTML;
            break;
            case 'registration':
                echo <<< HTML
                    <script src="login.js"></script>
                    <section class="registration">
                        <h1>Registrieren</h1>
                        <section class="flex-box">
                            <form method="post" action="login.php?action=registration" accept-charset="UTF-8">
                                <input type="text" name="benutzername" id="benutzername" value="" placeholder="Benutzernamen eingeben..." required>
                                <input type="password" name="passwort" id="passwort" value="" placeholder="Passwort eingeben..." required>
                                <input type="password" name="bestaetigen" id="bestaetigen" value="" placeholder="Passwort wiederholen..." required>
                                <section class="custom-select">
                                    <select class="select-hide" id="avatarSelect" name="avatarSelect">
                HTML;
                foreach ($data as $avatar){
                    $avatarId = $avatar['avatarId'];
                    $avatarpfad = htmlspecialchars($avatar['pfad']);
                    echo "<option value='{$avatarId}' data-image='{$avatarpfad}'>Avatar $avatarId</option>\n";
                }
                echo <<< HTML
                    </select>
                    <section class="select-selected">
                        <img id="selectedImage" src="avatar/avatar1.png" alt="">
                    </section>
                    <section class="select-items select-hide">
                HTML;
                foreach ($data as $avatar){
                    $avatarId = $avatar['avatarId'];
                    $avatarpfad = htmlspecialchars($avatar['pfad']);
                    echo "<section data-value='{$avatarId}' data-image='{$avatarpfad}'><img src='{$avatarpfad}' alt=''>Avatar $avatarId</section>\n";
                }

                echo <<< HTML
                    </section>
                    </section>
                    <input type="submit" value="Registrieren">
                    </form>
                    </section>
                    </section>
                HTML;



            break;
            default:
            break;
        }

        $this->generatePageFooter();
    }

    private function getAvatarPath($id):string{
        $stmt = $this->_database->prepare('SELECT pfad FROM avatar WHERE avatarId = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['pfad'];
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();
        if(isset($_POST['benutzername']) && isset($_POST['passwort']) && isset($_POST['bestaetigen']) && isset($_POST['avatarSelect'])) {
            $benutzername = htmlspecialchars($_POST['benutzername']);
            $passwort = htmlspecialchars($_POST['passwort']);
            $bestaetigen = htmlspecialchars($_POST['bestaetigen']);
            $avatar = $_POST['avatarSelect'];
            $avatar = $this->getAvatarPath($avatar);

            if($passwort != $bestaetigen) {

            } else {
                $passwort = hash('sha3-256', $passwort);
                $stmt = $this->_database->prepare(
                    'INSERT INTO nutzer(benutzername, passwort, score, avatarId)
                     VALUES (:benutzername, :passwort, 0, :avatar)'
                );
                $stmt->bindValue(':benutzername', $benutzername, PDO::PARAM_STR);
                $stmt->bindValue(':passwort', $passwort, PDO::PARAM_STR);
                $stmt->bindValue(':avatar', $avatar, PDO::PARAM_STR);
                $stmt->execute();

                $_SESSION['benutzername'] = $benutzername;
                $_SESSION['score'] = 0;
                $_SESSION['avatar'] = $avatar;

                header('Location: home.php?id=artikel');
            }
        }

    }

    public static function main():void
    {
        try {
            session_start();
            $page = new Login();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Login::main();
