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
                    <section class="container">
                        <section class="flex-box">
                            <h1 class="margin-bottom">Login</h1>
                            <form action="login.php?action=login" method="post" accept-charset="utf-8">
                                <label for="benutzername">Benuztername:</label>
                                <input type="text" class="margin-bottom" name="benutzernameLogin" id="benutzername" value="" required>
                                <label for="passwort">Passwort:</label>
                                <input type="password" class="margin-bottom" name="passwortLogin" id="passwort" value="" required>
                                <input type="submit" value="Login">
                            </form>
                            <p class="login-link"><a href="login.php?action=registration">Sie haben noch keinen Account?</a></p>
                        </section>
                    </section>
                HTML;
            break;
            case 'registration':
                $errorMessagePasswort = '';
                $errorMessageBenutzername = '';
                if(isset($_SESSION['errorMessagePasswort'])) {
                    $errorMessagePasswort = $_SESSION['errorMessagePasswort'];
                }
                if(isset($_SESSION['errorMessageBenutzername'])) {
                    $errorMessageBenutzername = $_SESSION['errorMessageBenutzername'];
                }
                echo <<< HTML
                    <script src="login.js"></script>
                    <section class="container"> 
                        <section class="flex-box">
                        <h1 class="margin-bottom">Registrieren</h1>
                            <form method="post" action="login.php?action=registration" accept-charset="UTF-8">
                                <label for="benutzername">Benuztername:</label>
                                <input type="text" class="margin-bottom" name="benutzername" id="benutzername" value="" required>
                                {$errorMessageBenutzername}
                                <label for="passwort">Passwort:</label>
                                <input type="password" class="margin-bottom" name="passwort" id="passwort" value="" required>
                                <label for="bestaetigen">Passwort bestätigen:</label>
                                <input type="password" class="margin-bottom" name="bestaetigen" id="bestaetigen" value="" required>
                                {$errorMessagePasswort}
                                <label for="avatarSelect">Avatar wählen:</label>
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
                    <section class="select-selected margin-bottom">
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
                    <p class="login-link"><a href="login.php?action=login">Sie haben bereits einen Account?</a></p>
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
        $stmt->closeCursor();
        return $row['pfad'];
    }

    private function checkBenutzername($benutzername):bool
    {
        $stmt = $this->_database->prepare('SELECT benutzername FROM nutzer WHERE benutzername = :username');
        $stmt->bindValue(':username', $benutzername, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $row['benutzername'] == $benutzername;
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();
        if(isset($_POST['benutzername']) && isset($_POST['passwort']) && isset($_POST['bestaetigen']) && isset($_POST['avatarSelect'])) {
            $benutzername = htmlspecialchars($_POST['benutzername']);
            $passwort = htmlspecialchars($_POST['passwort']);
            $bestaetigen = htmlspecialchars($_POST['bestaetigen']);
            $avatarId = $_POST['avatarSelect'];
            $avatar = $this->getAvatarPath($avatarId);

            if($passwort != $bestaetigen) {
                $_SESSION['errorMessagePasswort'] = "<p style='color: red;'>Passwörter stimmen nicht überein</p>";
            } else if($this->checkBenutzername($benutzername)){
                $_SESSION['errorMessageBenutzername'] = "<p style='color: red;'>Benutzername existiert bereit</p>";
            } else {
                $passwort = hash('sha3-256', $passwort);
                $stmt = $this->_database->prepare(
                    'INSERT INTO nutzer(benutzername, passwort, score, avatarId)
                     VALUES (:benutzername, :passwort, 0, :avatarId)'
                );
                $stmt->bindValue(':benutzername', $benutzername, PDO::PARAM_STR);
                $stmt->bindValue(':passwort', $passwort, PDO::PARAM_STR);
                $stmt->bindValue(':avatarId', $avatarId, PDO::PARAM_STR);
                $stmt->execute();

                $_SESSION['benutzername'] = $benutzername;
                $_SESSION['score'] = 0;
                $_SESSION['avatar'] = $avatar;

                header('Location: home.php?id=artikel');
            }
        }
        if(isset($_POST['benutzernameLogin']) && isset($_POST['passwortLogin'])) {
            $benutzername = htmlspecialchars($_POST['benutzernameLogin']);
            $passwort = htmlspecialchars($_POST['passwortLogin']);
            $passwort = hash('sha3-256', $passwort);

            $stmt = $this->_database->prepare(
                'SELECT passwort, score, pfad
                 FROM nutzer
                 NATURAL JOIN avatar
                 WHERE benutzername = :benutzername'
            );
            $stmt->bindValue(':benutzername', $benutzername, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if($row == false){

            } else if($passwort != $row['passwort']) {

            } else{
                $_SESSION['benutzername'] = $benutzername;
                $_SESSION['score'] = $row['score'];
                $_SESSION['avatar'] = $row['pfad'];
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
