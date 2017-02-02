<?php

return [
    'MailTransport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => 'smtp.gmail.com',
        'username' => 'username@gmail.com',
        'password' => 'password',
        'port' => '587',
        'encryption' => 'tls',
    ],
];
