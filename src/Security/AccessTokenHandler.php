<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['apiToken' => $accessToken]);

        if (null === $user) {
            throw new BadCredentialsException('Invalid Bearer token.');
        }

        return new UserBadge($user->getUserIdentifier());
    }
}
