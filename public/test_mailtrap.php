<?php
// test_mailtrap.php - À la racine de votre projet
require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

echo "<h1>Test Mailtrap Direct</h1>";

// Différentes configurations à tester
$configs = [
    'Port 587 TLS' => 'smtp://api:76741ca5094888d111e04b39cb37bab1@send.api.mailtrap.io:587',
    'Port 2525 TLS' => 'smtp://api:76741ca5094888d111e04b39cb37bab1@send.api.mailtrap.io:2525',
    'Port 25 TLS' => 'smtp://api:76741ca5094888d111e04b39cb37bab1@send.api.mailtrap.io:25',
    'Port 465 SSL' => 'smtp://api:76741ca5094888d111e04b39cb37bab1@send.api.mailtrap.io:465',
    'Sans TLS' => 'smtp://api:76741ca5094888d111e04b39cb37bab1@send.api.mailtrap.io:587?encryption=null',
];

foreach ($configs as $name => $dsn) {
    echo "<h3>Test: $name</h3>";
    echo "<p>DSN: $dsn</p>";

    try {
        $transport = Transport::fromDsn($dsn);
        $mailer = new Mailer($transport);

        $email = (new \Symfony\Component\Mime\Email())
            ->from('hello@demomailtrap.co')
            ->to('azizbenabdallah0412@gmail.com')
            ->subject("Test $name - " . date('H:i:s'))
            ->text('Test direct Mailtrap');

        $mailer->send($email);
        echo "<p style='color:green;'>✅ SUCCÈS</p>";
    } catch (\Exception $e) {
        echo "<p style='color:red;'>❌ ERREUR: " . $e->getMessage() . "</p>";
    }
    echo "<hr>";
}