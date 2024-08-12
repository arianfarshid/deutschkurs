<?php declare(strict_types=1);

abstract class Page
{
    protected function __construct()
    {
        error_reporting(E_ALL);

    }

    public function __destruct()
    {
        // to do: close database
    }

    protected function generatePageHeader(string $title = "", string $style = "", bool $autoreload = false): void
    {
        $title = htmlspecialchars($title);
        header("Content-type: text/html; charset=UTF-8");

        echo <<<EOT
        <!DOCTYPE html>
        <html lang="fa">
            <head>
                <meta charset="UTF-8">
                <title>$title</title>
                <link rel="stylesheet" href="$style" />
            </head>
            <body>
        EOT;
    }

    protected function generatePageFooter(): void
    {
        echo <<<EOT
            </body>
        </html>
        EOT;
    }

    protected function processReceivedData(): void
    {

    }
}