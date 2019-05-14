<?php

namespace App\DataFixtures;

use App\Entity\Giocatore;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {

        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUser($manager);
    }

    public function loadUser(ObjectManager $manager)
    {
        $user = new Giocatore();
        $user->setEmail('marconatalini.75@gmail.com');
        $user->setNickname('markone');
        $password = $this->passwordEncoder->encodePassword($user, 'andrea');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();
    }
}
