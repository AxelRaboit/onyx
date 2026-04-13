<?php

return [
    'accountDeleted' => [
        'subject'   => 'Farewell from :app',
        'greeting'  => 'Hello :name,',
        'body'      => 'Your :app account has been successfully deleted. All your data has been removed from our servers. Thank you for trusting us — we hope to see you again someday!',
        'closing'   => 'Take care.',
        'signature' => 'The :app team',
    ],

    'app_invitation' => [
        'subject'            => 'You have been invited to Onyx',
        'greeting'           => 'Hello!',
        'credentials_intro'  => 'Here are your access credentials:',
        'credential_email'   => 'Email: :email',
        'credential_password' => 'Password: :password',
        'action'             => 'Access Onyx',
        'footer'             => 'If you were not expecting this invitation, you can safely ignore this email.',
    ],
];
