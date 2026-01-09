<?php

namespace App\DataFixtures;

use App\Entity\Note;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher) {}
    
    public function load(ObjectManager $manager): void
    {
        $admin = (new User())
            ->setUsername('admin')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'AdminPass123!'));

        $user1 = (new User())
            ->setUsername('user1')
            ->setRoles(['ROLE_USER']);
        $user1->setPassword($this->hasher->hashPassword($user1, 'User1Pass123!'));

        $user2 = (new User())
            ->setUsername('user2')
            ->setRoles(['ROLE_USER']);
        $user2->setPassword($this->hasher->hashPassword($user2, 'User2Pass123!'));

        $manager->persist($admin);
        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();

        $note1 = (new Note())
            ->setOwner($user1)
            ->setContent('Note from user 1')
            ->setCreatedAt(new \DateTimeImmutable());

        $note2 = (new Note())
            ->setOwner($user2)
            ->setContent('Note from user 2')
            ->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($note1);
        $manager->persist($note2);

        $manager->flush();
    }
}
