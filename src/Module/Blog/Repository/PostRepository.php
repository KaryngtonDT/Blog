<?php

namespace App\Module\Blog\Repository;

use App\Framework\Repository\Repository;
use App\Module\Blog\Entity\Category;
use App\Module\Blog\Entity\Post;
use Pagerfanta\Adapter\CallbackAdapter;
use Pagerfanta\Pagerfanta;

class PostRepository extends Repository
{
    protected string $table = 'posts';
    protected string $entity = Post::class;


    public function findByWithCategories(int $id):?Post
    {
        $post= $this->findBy('id' , $id);
        if (is_null($post)) {
            return null;
        }

        $categories = $this->query
            ->from("categories")
            ->innerJoin("post_category ON categories.id = post_category.category_id")
            ->where("post_category.post_id" , $id)
            ->fetchAll();
        $categories=array_map(function ($item) {
            $category= new Category();

            $category->setId($item['id']);
            $category->setName($item['name']);
            $category->setSlug($item['slug']);

            return $category;
        }, $categories);

        $post->setCategories($categories);



        return $post;
    }

    public function findPaginatedForCategory(int $categoryId,int $perPage, int $currentPage):Pagerfanta
    {
        $query=$this->query->from($this->table);

        $query=  $query->innerJoin('post_category ON posts.id = post_category.post_id')
            -> where('post_category.category_id',$categoryId);

        $adapter= new CallbackAdapter(
            fn()=>$query->count(),
            fn($offset,$limit)=>array_map(
                [$this,'hydrate'],
                $query->limit($limit)->offset($offset)->fetchAll()),
        );
        $pager= new Pagerfanta($adapter);
        $pager->setMaxPerPage($perPage);
        $pager->setCurrentPage($currentPage);
        return $pager;
    }

    public function updatePostCategories(int $postId,array $categories):void{
        $this->query->deleteFrom("post_category")->where("post_id" , $postId)->execute();
        foreach ($categories as $category){
            $this->query->insertInto("post_category",['post_id' => $postId,'category_id' => $category])->execute();
        }
    }
}
