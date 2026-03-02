<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserUpdateDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'ID is required.')]
        #[Assert\Type('integer')]
        public readonly ?int $id,

        #[Assert\NotBlank(message: 'Login is required.')]
        #[Assert\Length(min: 8, max: 50)]
        public readonly ?string $login,

        #[Assert\NotBlank(message: 'Password is required.')]
        #[Assert\Length(min: 8, max: 50)]
        public readonly ?string $pass,

        #[Assert\NotBlank(message: 'Phone is required.')]
        #[Assert\Length(min: 8, max: 20)]
        public readonly ?string $phone
    ) {
    }
}
