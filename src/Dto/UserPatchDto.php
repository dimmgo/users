<?php

namespace App\Dto;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class UserPatchDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'ID is required.')]
        #[Assert\Type('integer')]
        public readonly int $id,

        #[Assert\Length(min: 8, max: 50)]
        public readonly ?string $login = null,

        #[Assert\Length(min: 8, max: 50)]
        public readonly ?string $pass = null,

        #[Assert\Length(min: 8, max: 20)]
        public readonly ?string $phone = null,

        #[Assert\Choice(choices: [User::ROLE_USER, User::ROLE_ROOT], message: 'Invalid role.')]
        public readonly ?string $role = null
    ) {
    }
}
