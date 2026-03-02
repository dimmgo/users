<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\AbstractApiController;
use App\Service\UserService;
use App\Dto\UserCreateDto;
use App\Dto\UserUpdateDto;
use App\Dto\UserGetDto;
use App\Dto\UserDeleteDto;
use App\Dto\UserPatchDto;
use App\Security\Voter\UserVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[Route('/v1/api/users')]
class UserController extends AbstractApiController
{
    public function __construct(private readonly UserService $userService)
    {
    }

    #[Route('/{id<\d+>}', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $dto = new UserGetDto($id);

        $user = $this->userService->getUser($dto);
        $this->denyAccessUnlessGranted(UserVoter::VIEW, $user);

        return $this->jsonResponse([
            'login' => $user->getLogin(),
            'phone' => $user->getPhone(),
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(UserVoter::CREATE);

        $data = json_decode($request->getContent(), true) ?? [];
        $dto = new UserCreateDto(
            $data['login'] ?? null,
            $data['pass'] ?? null,
            $data['phone'] ?? null,
            $data['role'] ?? null
        );

        $user = $this->userService->createUser($dto);

        return $this->jsonResponse([
            'id' => $user->getId(),
            'login' => $user->getLogin(),
            'phone' => $user->getPhone(),
            'token' => $user->getApiToken(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id<\d+>}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $user = $this->userService->getUser(new UserGetDto($id));
        $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);

        $data = json_decode($request->getContent(), true) ?? [];
        $dto = new UserUpdateDto(
            $id,
            $data['login'] ?? null,
            $data['pass'] ?? null,
            $data['phone'] ?? null
        );

        $user = $this->userService->updateUser($dto);

        return $this->jsonResponse(['id' => $user->getId()]);
    }

    #[Route('/{id<\d+>}', methods: ['PATCH'])]
    public function patch(int $id, Request $request): JsonResponse
    {
        $user = $this->userService->getUser(new UserGetDto($id));
        $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);

        $data = json_decode($request->getContent(), true) ?? [];
        $dto = new UserPatchDto(
            $id,
            $data['login'] ?? null,
            $data['pass'] ?? null,
            $data['phone'] ?? null,
            $data['role'] ?? null
        );

        $user = $this->userService->patchUser($dto);

        return $this->jsonResponse(['id' => $user->getId()]);
    }

    #[Route('/{id<\d+>}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $dto = new UserDeleteDto($id);
        $user = $this->userService->getUser(new UserGetDto($dto->id));
        $this->denyAccessUnlessGranted(UserVoter::DELETE, $user);

        $this->userService->deleteUser($dto);

        return $this->jsonResponse();
    }
}
