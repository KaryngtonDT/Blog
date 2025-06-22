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

    public function register(string $email, string $password,?bool $admin=false):false|int{
        $user= $this->repository->findBy('email',$email);
        if($user){

            return false;
        }

        return $this->repository->insert(
            [
                'email' => $email,
                'password' => password_hash($password,PASSWORD_DEFAULT),
                'admin' => $admin,
            ]
        );
    }

    public function login(string $email, string $password):bool{
        $user= $this->repository->findBy('email',$email);
        if($user && password_verify($password,$user->getPassword())){
            $this->currentUser = $user;
            return true;
        }
        return false;
    }

    public function logout():bool{
        try{
            unset($_SESSION['auth']);
            return true;
        }catch (\Exception){
            return false;
        }


    }

    public function  isLoggedIn():bool
    {
        return $this->currentUser !== null;
    }

    public function isAdmin():bool{
        return $this->currentUser->isAdmin();
    }
    public function user(): ?User{
        return $this->currentUser;
    }

}
