<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\User;

use Domain\User\User;
use Domain\User\UserNotFoundException;
use Tests\TestCase;

class InMemoryUserRepositoryTest extends TestCase
{
    public function testFindAll()
    {
        $user = new User(1, 'bill.gates', 'Bill', 'Gates');

        $userRepository = new \Infrastructure\Persistence\User\InMemoryUserRepository([1 => $user]);

        $this->assertEquals([$user], $userRepository->findAll());
    }

    public function testFindAllUsersByDefault()
    {
        $users = [
            1 => new User(1, 'bill.gates', 'Bill', 'Gates'),
            2 => new User(2, 'steve.jobs', 'Steve', 'Jobs'),
            3 => new User(3, 'mark.zuckerberg', 'Mark', 'Zuckerberg'),
            4 => new User(4, 'evan.spiegel', 'Evan', 'Spiegel'),
            5 => new User(5, 'jack.dorsey', 'Jack', 'Dorsey'),
        ];

        $userRepository = new \Infrastructure\Persistence\User\InMemoryUserRepository();

        $this->assertEquals(array_values($users), $userRepository->findAll());
    }

    public function testFindUserOfId()
    {
        $user = new User(1, 'bill.gates', 'Bill', 'Gates');

        $userRepository = new \Infrastructure\Persistence\User\InMemoryUserRepository([1 => $user]);

        $this->assertEquals($user, $userRepository->findUserOfId(1));
    }

    public function testFindUserOfIdThrowsNotFoundException()
    {
        $userRepository = new \Infrastructure\Persistence\User\InMemoryUserRepository([]);
        $this->expectException(UserNotFoundException::class);
        $userRepository->findUserOfId(1);
    }
}
