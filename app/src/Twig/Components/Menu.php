<?php
namespace App\Twig\Components;

use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\Security\Core\Security;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Menu
{
    public function __construct(private readonly SecurityBundleSecurity $security) {}

    public function isLoggedIn(): bool
    {
        return $this->security->getUser() !== null;
    }

    public function isAdmin(): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
