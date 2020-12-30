<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('admin@sandelys.lt');
        $user->setPassword('$argon2id$v=19$m=65536,t=4,p=1$Yk83QmVjZjdhSzlqOXFaMA$hTzhdmJoaR+3G3GOBq2WdwKGa3SNhSa92ZztfMGaZ5E');
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        $user = new User();
        $user->setEmail('accountant@sandelys.lt');
        $user->setPassword('$argon2id$v=19$m=65536,t=4,p=1$Yk83QmVjZjdhSzlqOXFaMA$hTzhdmJoaR+3G3GOBq2WdwKGa3SNhSa92ZztfMGaZ5E');
        $user->setRoles(['ROLE_ACCOUNTANT']);

        $manager->persist($user);

        $user = new User();
        $user->setEmail('foremen@sandelys.lt');
        $user->setPassword('$argon2id$v=19$m=65536,t=4,p=1$Yk83QmVjZjdhSzlqOXFaMA$hTzhdmJoaR+3G3GOBq2WdwKGa3SNhSa92ZztfMGaZ5E');
        $user->setRoles(['ROLE_FOREMEN']);

        $manager->persist($user);

        $manager->flush();
    }
}
