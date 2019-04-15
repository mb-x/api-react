<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
    }

    protected function loadBlogPosts(ObjectManager $manager)
    {
        $user = $this->getReference('admin');
        $blogPost = new BlogPost();
        $blogPost
            ->setTitle('A first title')
            ->setPublished(new \DateTime())
            ->setContent('Post content')
            ->setSlug('a-first-title')
            ->setAuthor($user)
        ;
        $manager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost
            ->setTitle('A second title')
            ->setPublished(new \DateTime())
            ->setContent('Post content')
            ->setSlug('a-second-title')
            ->setAuthor($user)
        ;
        $manager->persist($blogPost);

        $manager->flush();
    }

    protected function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setUsername('admin')
            ->setEmail('mbengrich@manymore.fr')
            ->setName('Mohammed')
            ->setPassword(
                $this->passwordEncoder->encodePassword($user, 'secret123')
            )
        ;

        $this->addReference('admin', $user);

        $manager->persist($user);


        $manager->flush();
    }
}
