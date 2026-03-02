<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserDeleteDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'ID is required.')]
        #[Assert\Type('integer')]
        public readonly ?int $id
    ) {
    }
}
