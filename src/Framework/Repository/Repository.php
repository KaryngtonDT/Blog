<?php

namespace App\Framework\Repository;


use Envms\FluentPDO\Query;
use Pagerfanta\Adapter\CallbackAdapter;
use Pagerfanta\Pagerfanta;

class Repository implements RepositoryInterface
{

    protected string $table;
    protected string $entity;

    public function __construct(private Query $query)
    {
    }

    public function findPaginated(int $perpage,int $currentpage):Pagerfanta{

        $query=$this->query->from($this->table);

        $adapter= new CallbackAdapter(
           fn ()=> $query->count()
           ,
            fn ($offset,$limit) => array_map([$this,'hydrate'] ,$query->limit($limit)
                      ->offset($offset)
                      ->fetchAll())


        );

        $pager=new Pagerfanta($adapter);
        $pager->setMaxPerPage($perpage);
        $pager->setCurrentPage($currentpage);
        return  $pager;


    }
    public function findAll(): ?array
    {
       $result=  $this->query->from($this->table)->fetchAll();

       return array_map( function ($row) {
           $this->hydrate($row);
       },$result);

    }

    public function findBy(string $key,  $value): ?object{

        if($value===null){
            return null;
        }
        $result=  $this->query->from($this->table)->where($key,$value)->fetch();
         return $this->hydrate($result);
    }

    public function insert(array $data): int{
         return $this->query->insertInto($this->table, $data)->execute();
    }

    public function update(int $id, array $data): void{

        $this->query->update($this->table,$data)->where("id",$id)->execute();
    }

    public function delete(int $id): void
    {
       $this->query->delete($this->table)->where("id",$id)->execute();
    }



    public function hydrate(array $data):?object{
        if (empty($data)) {
            return null;
        }
        $object= new $this->entity();

        foreach($data as $key=>$value){
            $setter= 'set'.str_replace('_','',ucwords($key,'_'));
           $object->{$setter}($value);
        }
        return $object;
    }

}
