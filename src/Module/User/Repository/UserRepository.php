<?php

namespace App\Module\User\Repository;

use App\Framework\Repository\Repository;
use App\Module\User\Entity\User;

class UserRepository extends Repository
{

    protected string $table="users";
    protected string $entity=User::class;

}
