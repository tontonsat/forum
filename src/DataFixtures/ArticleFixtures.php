<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use Faker;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // On configure dans quelles langues nous voulons nos données
        $faker = Faker\Factory::create('fr_FR');

        for($i=1; $i<= 10; $i++) {
            $article = new Article();
            $article->setTitle($faker->name)
                    ->setContent($faker->text($maxNbChars = 200))
                    ->setCreatedAt($faker->dateTime($max = 'now', $timezone = null))
                    ->setImage("http://placehold.it/350x150");
            $manager->persist($article);
        }
        $manager->flush();
    }
}
