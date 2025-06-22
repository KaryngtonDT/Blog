<?php

namespace App\Module\User\Service;

use App\Module\User\Entity\User;
use App\Module\User\Repository\UserRepository;

class AuthService implements AuthServiceInterface
{

    private ?User $currentUser = null;
    public function __construct(
        private UserRepository $repository,
    )
    {
        if(isset($_SESSION['auth'])){
            $this->currentUser = $this->repository->findBy('id',$_SESSION['auth']);
        }

    }

    public function register(string $email, string $password):false|int{
        $user= $this->repository->findBy('email',$email);
        if($user){

            return false;
        }

        return $this->repository->insert(
            [
                'email' => $email,
                'password' => password_hash($password,PASSWORD_DEFAULT),

            ]
        );
    }

    public function login(string $email, string $password):bool{
        $user= $this->repository->findBy('email',$email);
        if($user && password_verify($password,$user->getPassword())){
            $_SESSION['auth']=$user->getId();
            $this->currentUser = $user;
            return true;
        }
        return false;
    }

    public function logout():void{

            unset($_SESSION['auth']);
            $this->currentUser= null;



    }

    public function  isLoggedIn():bool
    {
        return $this->user() !== null;
    }

    public function isAdmin():bool{
        return $this->isLoggedIn() && $this->currentUser->isAdmin();
    }
    public function user(): ?User{
        return $this->currentUser;
    }

}
