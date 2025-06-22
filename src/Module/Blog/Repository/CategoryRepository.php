<?php

namespace App\Module\Blog\Repository;

use App\Framework\Repository\Repository;
use App\Module\Blog\Entity\Category;

class CategoryRepository extends Repository
{
    protected string $table = 'categories';
    protected string $entity = Category::class;
}
