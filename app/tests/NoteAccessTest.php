<?php
declare(strict_types=1);

namespace App\Tests\Security;

use App\Entity\Note;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class NoteAccessTest extends WebTestCase
{
    public function test_owner_can_view_and_non_owner_gets_403(): void
    {
        $client = static::createClient([], [
    'HTTP_HOST' => 'localhost',
    'HTTPS' => 'on',
]);
        $em = static::getContainer()->get(EntityManagerInterface::class);

        // create users
        $user1 = (new User())->setUsername('user1')->setPassword('x')->setRoles(['ROLE_USER']);
        $user2 = (new User())->setUsername('user2')->setPassword('x')->setRoles(['ROLE_USER']);

        $em->persist($user1);
        $em->persist($user2);
        $em->flush();

        // create notes
        $note1 = (new Note())->setOwner($user1)->setContent('note1')->setCreatedAt(new \DateTimeImmutable());
        $note2 = (new Note())->setOwner($user2)->setContent('note2')->setCreatedAt(new \DateTimeImmutable());

        $em->persist($note1);
        $em->persist($note2);
        $em->flush();

        // login as user1
        $client->loginUser($user1);

        // owner access -> 200
        $client->request('GET', '/note/'.$note1->getId());

        $this->assertSame(200, $client->getResponse()->getStatusCode(), 'Owner should access their own note successfully.');

        // non-owner access -> 403
        $client->request('GET', '/note/'.$note2->getId());
        $this->assertResponseStatusCodeSame(403, 'Non-owner should receive 403 Forbidden');
        
        // // cleanup
        // $em->remove($note1);
        // $em->remove($note2);
        // $em->remove($user1);
        // $em->remove($user2);
        // $em->flush();
    }
}
