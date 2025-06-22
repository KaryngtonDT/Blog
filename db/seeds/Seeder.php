<?php

declare(strict_types=1);

use Cake\Utility\Hash;
use Faker\Factory;
use Phinx\Seed\AbstractSeed;

class Seeder extends AbstractSeed
{


    public function run(): void
    {


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
