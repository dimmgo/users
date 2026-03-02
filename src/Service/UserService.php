<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Dto\UserCreateDto;
use App\Dto\UserUpdateDto;
use App\Dto\UserGetDto;
use App\Dto\UserDeleteDto;
use App\Dto\UserPatchDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $userRepository,
        private readonly ValidatorInterface $validator,
        private readonly Security $security,
    ) {
    }

    public function createUser(UserCreateDto $dto): User
    {
        $this->validate($dto);

        if ($this->userRepository->findOneBy(['login' => $dto->login])) {
            throw new ConflictHttpException(sprintf('User with login "%s" already exists.', $dto->login));
        }

        $user = new User();
        $user->setLogin($dto->login);
        $user->setPhone($dto->phone);
        $user->setPassword($dto->pass);
        $user->createApiToken();

        $user->setRoles([$dto->role ?? User::ROLE_USER]);
        if (!$this->security->isGranted(User::ROLE_ROOT)) {
            $user->setRoles([User::ROLE_USER]);
        }

        $this->validate($user);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function updateUser(UserUpdateDto $dto): User
    {
        $this->validate($dto);

        /** @var User $user */
        $user = $this->userRepository->find($dto->id);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $existingUser = $this->userRepository->findOneBy(['login' => $dto->login]);
        if ($existingUser && $existingUser->getId() !== $user->getId()) {
            throw new ConflictHttpException(sprintf('User with login "%s" already exists.', $dto->login));
        }

        $user->setLogin($dto->login);
        $user->setPhone($dto->phone);
        $user->setPassword($dto->pass);

        $this->validate($user);
        $this->em->flush();

        return $user;
    }

    public function patchUser(UserPatchDto $dto): User
    {
        $this->validate($dto);

        /** @var User $user */
        $user = $this->userRepository->find($dto->id);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        if ($dto->login !== null) {
            /** @var User $existingUser */
            $existingUser = $this->userRepository->findOneBy(['login' => $dto->login]);
            if ($existingUser && $existingUser->getId() !== $user->getId()) {
                throw new ConflictHttpException(sprintf('User with login "%s" already exists.', $dto->login));
            }
            $user->setLogin($dto->login);
        }

        if ($dto->phone !== null) {
            $user->setPhone($dto->phone);
        }

        if ($dto->pass !== null) {
            $user->setPassword($dto->pass);
        }

        if ($dto->role !== null) {
            if (!$this->security->isGranted(User::ROLE_ROOT)) {
                throw new AccessDeniedException('Only admin can change roles.');
            }
            $user->setRoles([$dto->role]);
        }

        $this->validate($user);
        $this->em->flush();

        return $user;
    }

    public function getUser(UserGetDto $dto): User
    {
        $this->validate($dto);

        /** @var User $user */
        $user = $this->userRepository->find($dto->id);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        return $user;
    }

    public function deleteUser(UserDeleteDto $dto): void
    {
        $this->validate($dto);

        /** @var User $user */
        $user = $this->userRepository->find($dto->id);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $this->em->remove($user);
        $this->em->flush();
    }

    private function validate(object $object): void
    {
        $errors = $this->validator->validate($object);

        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }

            throw new BadRequestHttpException(implode('; ', $messages));
        }
    }
}
