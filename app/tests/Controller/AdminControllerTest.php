<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\BaseTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AdminControllerTest extends BaseTestCase
{
    private KernelBrowser $client;
    private string $adminUsername = 'exampleAdmin';
    private string $adminPassword = 'password';

    protected function setUp(): void
    {
        $this->client = $this->httpsClient();
        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);

        // Remove any existing users from the test database
        foreach ($userRepository->findAll() as $user) {
            $em->remove($user);
        }

        $em->flush();

        // Create a User fixture
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get('security.user_password_hasher');

        $user = (new User())->setUsername($this->adminUsername);
        $user->setPassword($passwordHasher->hashPassword($user, $this->adminPassword));
        $user->setRoles(['ROLE_ADMIN']);

        $em->persist($user);
        $em->flush();
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/admin');
        self::assertResponseRedirects('/login');

        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            '_username' => $this->adminUsername,
            '_password' => $this->adminPassword,
        ]);
        $this->client->request('GET', '/admin');

        self::assertResponseIsSuccessful();
    }
}
