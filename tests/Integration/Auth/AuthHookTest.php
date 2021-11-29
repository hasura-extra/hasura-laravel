<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Laravel\Tests\Integration\Auth;

use Hasura\Laravel\Tests\TestCase;
use Illuminate\Auth\GenericUser;

final class AuthHookTest extends TestCase
{
    public function testAnonymousRequest(): void
    {
        $response = $this->post('/hasura_auth_hook');

        $response->assertSuccessful();
        $response->assertExactJson(['x-hasura-role' => 'anonymous']);
    }

    public function testUserRequestUseDefaultRole(): void
    {
        $this->loginWithRoles('user');

        $response = $this->post('/hasura_auth_hook');

        $response->assertSuccessful();
        $response->assertExactJson(['x-hasura-role' => 'user']);
    }

    public function testUserRequestWithRole(): void
    {
        $this->loginWithRoles('manager');

        $response = $this->post('/hasura_auth_hook', headers: ['x-hasura-role' => 'manager']);

        $response->assertSuccessful();
        $response->assertExactJson(['x-hasura-role' => 'manager']);
    }

    public function testUserRequestWithUnauthorizedRole(): void
    {
        $this->loginWithRoles('user');

        $response = $this->post('/hasura_auth_hook', headers: ['x-hasura-role' => 'manager']);

        $response->assertUnauthorized();
    }

    private function loginWithRoles(array|string $withRoles): void
    {
        $user = new class($withRoles) extends GenericUser {
            public function __construct(private array|string $roles)
            {
                parent::__construct(['id' => 1]);
            }

            public function getRoles(): array|string
            {
                return $this->roles;
            }
        };

        $this->app['auth']->login($user);
    }
}