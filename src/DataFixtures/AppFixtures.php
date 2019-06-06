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

    const USERS = [
        [
            'username' => 'admin',
            'email' => 'mbengrich@manymore.fr',
            'name' => 'Mohammed',
            'password' => 'secret123',
        ],
        [
            'username' => 'test1',
            'email' => 'test1@test.fr',
            'name' => 'Mohammed',
            'password' => 'secret123',
        ],
        [
            'username' => 'test2',
            'email' => 'test2@test.fr',
            'name' => 'Mohammed',
            'password' => 'secret123',
        ]
    ];

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
        $usersCount = count(self::USERS) - 1;
        for ($i = 0; $i < 100; $i++) {
            $blogPost = new BlogPost();
            $blogPost
                ->setTitle($this->faker->realText(30))
                ->setPublished($this->faker->dateTimeThisYear)
                ->setContent($this->faker->realText())
                ->setSlug($this->faker->slug)
                ->setAuthor($this->getRandomUserReference($usersCount))
            ;
            $this->setReference("blog_post_$i", $blogPost);
            $manager->persist($blogPost);
        }

        $manager->flush();
    }

    protected function loadComments(ObjectManager $manager)
    {
        $usersCount = count(self::USERS) - 1;
        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < rand(1, 10); $j++) {
                $comment = new Comment();
                $comment
                    ->setPublished($this->faker->dateTimeThisYear)
                    ->setContent($this->faker->realText())
                    ->setAuthor($this->getRandomUserReference($usersCount))
                    ->setBlogPost($this->getReference("blog_post_$i"))
                ;
                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    public function getRandomUserReference(int $max)
    {
        return $this->getReference('user_'.self::USERS[rand(0, $max)]['username']);
    }

    protected function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $u) {
            $user = new User();
            $user
                ->setUsername($u['username'])
                ->setEmail($u['email'])
                ->setName($u['name'])
                ->setPassword(
                    $this->passwordEncoder->encodePassword($user, $u['password'])
                )
            ;

            $this->addReference('user_' . $u['username'], $user);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
