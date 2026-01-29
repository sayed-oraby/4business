<?php

return [
    'title.required' => 'The title field is required.',
    'title.array' => 'The title must be an array.',
    'title.en.required_without' => 'The title.en field is required when title.ar is not present.',
    'title.ar.required_without' => 'The title.ar field is required when title.en is not present.',
    'title.en.string' => 'The title (English) must be a string.',
    'title.ar.string' => 'The title (Arabic) must be a string.',
    'title.en.max' => 'The title (English) must not exceed 255 characters.',
    'title.ar.max' => 'The title (Arabic) must not exceed 255 characters.',
    'description.array' => 'The description must be an array.',
    'description.en.string' => 'The description (English) must be a string.',
    'description.ar.string' => 'The description (Arabic) must be a string.',
    'slug.string' => 'The slug must be a string.',
    'slug.max' => 'The slug must not exceed 255 characters.',
    'slug.alpha_dash' => 'The slug may only contain letters, numbers, dashes and underscores.',
    'status.required' => 'The status field is required.',
    'status.in' => 'The selected status is invalid.',
    'image.image' => 'The file must be an image.',
    'image.max' => 'The image size must not exceed 4MB.',
];

