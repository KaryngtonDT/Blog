<?php

namespace App\Framework\Service;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

class ValidationService implements ValidationServiceInterface
{

    public function vaidator(array $data)
    {
        $errors = [];
        $rules = [
           'email'=>v::notEmpty()->email(),
           'password'=>v::notEmpty()->length(6)
        ];

        foreach ($rules as $field => $rule) {
            if (!isset($data[$field])) {
                continue;
            }
            try{
                $rule->assert($data[$field]);
            }catch (NestedValidationException $e){
                $messages= $e->getMessages();
                $errors[$field] = reset($messages);
            }

        }
        return $errors;
    }

}
