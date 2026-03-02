<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

class UserCreateDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Login is required.')]
        #[Assert\Length(min: 8, max: 50)]
        public readonly ?string $login,

        #[Assert\NotBlank(message: 'Password is required.')]
        #[Assert\Length(min: 8, max: 50)]
        public readonly ?string $pass,

        #[Assert\NotBlank(message: 'Phone is required.')]
        #[Assert\Length(min: 8, max: 20)]
        public readonly ?string $phone,

        #[Assert\Choice(choices: [User::ROLE_USER, User::ROLE_ROOT], message: 'Invalid role.')]
        public readonly ?string $role = null
    ) {
    }
}
