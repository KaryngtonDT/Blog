<?php

declare(strict_types=1);

use Cake\Utility\Hash;
use Faker\Factory;
use Phinx\Seed\AbstractSeed;

class Seeder extends AbstractSeed
{


    public function run(): void
    {

        $faker = Factory::create('fr_FR');


        $categories = [];
        for ($i = 0; $i < 10; $i++) {
            $categories[] = [
                'name' => $faker->unique()->word(),
                'slug' => $faker->unique()->slug,
            ];
        }
        $this->table('categories')->insert($categories)->saveData();


        $posts = [];
        for ($i = 0; $i < 30; $i++) {
            $posts[] = [
                'title' => $faker->sentence(6),
                'slug' => $faker->unique()->slug,
                'content' => $faker->paragraph(10),
                'image' => 'https://picsum.photos/seed/' . $faker->word . '/280/150',
                'created_at' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
                'updated_at' => null,
            ];
        }
        $this->table('posts')->insert($posts)->saveData();


        $postCategory = [];
        $postIds = range(1, 30);
        $categoryIds = range(1, 10);

        foreach ($postIds as $postId) {
            $randomCategories = (array) array_rand($categoryIds, random_int(1, 3));
            foreach ($randomCategories as $index) {
                $postCategory[] = [
                    'post_id' => $postId,
                    'category_id' => $categoryIds[$index],
                ];
            }
        }
        $this->table('post_category')->insert($postCategory)->saveData();

        $users=[
            [
                'email'=>'admin@gmail.com',
                'password'=> password_hash('admin123', PASSWORD_DEFAULT),
                'admin'=>true

            ],
            [
                'email'=>'user@gmail.com',
                'password'=> password_hash('user123', PASSWORD_DEFAULT),
                'admin'=>false

            ]

        ];
        $this->table('users')->insert($users)->saveData();

    }
}
