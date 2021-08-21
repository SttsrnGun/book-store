<?php
namespace App\Controller;

use App\Entity\AppUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class GetMeAction
 */
final class GetMeAction extends AbstractController
{
    /**
     * @return AppUser
     */
    public function __invoke(): AppUser
    {
        /** @var AppUser $user */
        $user = $this->getUser();

        return $user;
    }
}
