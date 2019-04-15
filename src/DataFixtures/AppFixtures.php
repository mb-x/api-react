<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $passwordEncoder;

    private $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker           = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    protected function loadBlogPosts(ObjectManager $manager)
    {
        $user = $this->getReference('admin');

        for ($i = 0; $i < 100; $i++) {
            $blogPost = new BlogPost();
            $blogPost
                ->setTitle($this->faker->realText(30))
                ->setPublished($this->faker->dateTimeThisYear)
                ->setContent($this->faker->realText())
                ->setSlug($this->faker->slug)
                ->setAuthor($user)
            ;
            $this->setReference("blog_post_$i", $blogPost);
            $manager->persist($blogPost);
        }

        $manager->flush();
    }

    protected function loadComments(ObjectManager $manager)
    {
        $user = $this->getReference('admin');

        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < rand(1, 10); $j++) {
                $comment = new Comment();
                $comment
                    ->setPublished($this->faker->dateTimeThisYear)
                    ->setContent($this->faker->realText())
                    ->setAuthor($user)
                    ->setBlogPost($this->getReference("blog_post_$i"))
                ;
                $manager->persist($comment);
            }
        }

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
