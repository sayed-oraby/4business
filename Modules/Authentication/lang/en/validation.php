<?php

return [
    'email' => [
        'required' => 'Email address is required.',
        'email' => 'Enter a valid email address.',
        'exists' => 'We cannot find an administrator with that email.',
    ],
    'password' => [
        'required' => 'Password is required.',
        'min' => 'Password must be at least :min characters.',
        'confirmed' => 'Password confirmation does not match.',
    ],
    'otp' => [
        'required' => 'Please enter the verification code.',
        'digits' => 'The verification code must be :digits digits.',
    ],
    'remember' => [
        'boolean' => 'Remember me value is invalid.',
    ],
];
