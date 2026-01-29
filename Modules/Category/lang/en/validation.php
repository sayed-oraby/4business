<?php

return [
    'title.required' => 'The title field is required.',
    'title.array' => 'The title must be an array.',
    'title.en.required_without' => 'The title.en field is required when title.ar is not present.',
    'title.ar.required_without' => 'The title.ar field is required when title.en is not present.',
    'status.required' => 'The status field is required.',
    'status.in' => 'The selected status is invalid.',
    'parent_id.integer' => 'The parent category must be an integer.',
    'parent_id.exists' => 'The selected parent category does not exist.',
    'parent_id.not_in' => 'The category cannot be its own parent.',
    'image.image' => 'The file must be an image.',
    'image.max' => 'The image size must not exceed 4MB.',
    'position.integer' => 'The position must be an integer.',
    'position.min' => 'The position must be at least 0.',
    'position.max' => 'The position must not exceed 9999.',
    'featured_order.integer' => 'The featured order must be an integer.',
    'featured_order.min' => 'The featured order must be at least 0.',
    'featured_order.max' => 'The featured order must not exceed 9999.',
];

