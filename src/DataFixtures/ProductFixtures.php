<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i<20; $i++) {
            $product = new Product();
            $product->setName($this->createRandomName());
            $product->setReference($this->createRandomReference());
            $product->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
            $product->setPrice(10.25);
            $manager->persist($product);
        }
        $manager->flush();
    }

    private function createRandomName(): string
    {
        $string = $this->generateRandomString(10, 'lowercase');
        return join(" ", str_split(ucfirst($string), 5));
    }

    private function createRandomReference(): string
    {
        $numericPart = random_int(1000, 5000);
        return $numericPart . '-' . $this->generateRandomString(5);
    }

    private function generateRandomString(int $length = 10, string $mode = 'alnum'): string
    {
        switch ($mode) {
            case 'lowercase':
                $characters = 'abcdefghijklmnopqrstuvwxyz';
                break;
            case 'alnum':
            default:
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
        }

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
