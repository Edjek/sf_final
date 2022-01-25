<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Licence;
use App\Entity\Product;
use App\Entity\Category;
use App\Repository\LicenceRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{

    private $categoryRepository;
    private $licenceRepository;
    private $productRepository;

    public function __construct(CategoryRepository $categoryRepository, LicenceRepository $licenceRepository, ProductRepository $productRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->licenceRepository = $licenceRepository;
        $this->productRepository = $productRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $category = new Category();

            $category->setName($faker->word);
            $category->setDescription($faker->text);
            $category->setMedia($faker->word);

            $manager->persist($category);
            $manager->flush();
        }

        for ($i = 0; $i < 10; $i++) {
            $licence = new Licence();

            $licence->setName($faker->word);
            $licence->setDescription($faker->text);
            $licence->setMedia($faker->word);

            $manager->persist($licence);
            $manager->flush();
        }

        for ($i = 0; $i < 10; $i++) {
            $product = new Product();

            $id_category = rand(1, 10);
            $id_licence = rand(1, 10);

            $category = $this->categoryRepository->find($id_category);
            $licence = $this->licenceRepository->find($id_licence);

            $product->setName($faker->word);
            $product->setPrice($faker->randomFloat(1, 10, 200));
            $product->setStock($faker->numberBetween(0, 100));
            $product->setCategory($category);
            $product->setLicence($licence);

            $manager->persist($product);
            $manager->flush();
        }

        for ($i = 0; $i < 10; $i++) {
            $image = new Image();

            $id_product = rand(0, 10);

            $product = $this->productRepository->find($id_product);

            $image->setSrc($faker->imageUrl(150, 100, true));
            $image->setTitle($faker->word);
            $image->setAlt($faker->word);
            $image->setProduct($product);

            $manager->persist($image);
            $manager->flush();
        }

        for ($i = 0; $i < 10; $i++) {
            $user = new User();

            $user->setEmail($faker->email);
            $user->setRoles(["ROLE_USER"]);
            $user->setPassword($faker->password);
            $user->setLastname($faker->lastname);
            $user->setFirstname($faker->firstname);
            $user->setCity($faker->city);
            $user->setAdress($faker->address);
            $user->setZipcode($faker->postcode);
            $user->setDate($faker->dateTimeThisYear);
            $user->setCardNumber($faker->creditCardNumber);
            $user->setCardName($faker->lastName);

            $manager->persist($user);
            $manager->flush();
        }
    }
}
