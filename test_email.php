<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

try {
    $dsn = 'smtp://fadisaidi02@gmail.com:foyjcfnkqyusygvx@smtp.gmail.com:587';
    $transport = Transport::fromDsn($dsn);
    $mailer = new Mailer($transport);

    $email = (new Email())
        ->from('fadisaidi02@gmail.com')
        ->to('fadisaidi02@gmail.com')
        ->subject('Test Email')
        ->text('Test message');

    $mailer->send($email);
    echo "Email sent successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}