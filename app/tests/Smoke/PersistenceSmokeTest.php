<?php
declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Entity\Note;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PersistenceSmokeTest extends KernelTestCase
{
    public function test_can_persist_user_and_note(): void
    {
        self::bootKernel();
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $u = (new User())
            ->setUsername('smoke-user')
            ->setPassword('x')
            ->setRoles(['ROLE_USER']);

        $em->persist($u);
        $em->flush();

        $note = (new Note())
            ->setOwner($u)
            ->setContent('hello')
            ->setCreatedAt(new \DateTimeImmutable());

        $em->persist($note);
        $em->flush();

        $this->assertNotNull($u->getId());
        $this->assertNotNull($note->getId());
    }
}
