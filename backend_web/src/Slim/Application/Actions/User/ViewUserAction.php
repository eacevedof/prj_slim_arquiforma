<?php

declare(strict_types=1);

namespace App\Slim\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

final class ViewUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('id');
        $user = $this->userRepositoryInterface->findUserOfId($userId);

        $this->logger->info("User of id `$userId` was viewed.");

        return $this->respondWithData($user);
    }
}
