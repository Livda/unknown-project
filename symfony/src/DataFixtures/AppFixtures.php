<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setEmail('user@a.a');
        $user1->setActive(true);
        $user1->setFirstName('Jean');
        $user1->setLastName('Dupond');
        $user1->setPassword($this->encoder->encodePassword($user1, 'password'));
        $manager->persist($user1);

        $admin1 = new User();
        $admin1->setEmail('admin@a.a');
        $admin1->setActive(true);
        $admin1->setFirstName('Unicorn');
        $admin1->setLastName('Rainbow');
        $admin1->setRoles(['ROLE_ADMIN']);
        $admin1->setPassword($this->encoder->encodePassword($admin1, 'password'));
        $manager->persist($admin1);

        for ($i = 1; $i < 11; ++$i) {
            $product = new Product();
            $product->setName('Product ' . $i);
            $product->setSlug('product-' . $i);
            $product->setPrice(intval($i * 1000));
            $manager->persist($product);
        }

        $manager->flush();
    }
}
