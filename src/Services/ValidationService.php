<?php

namespace App\Services;

use Symfony\Component\Validator\Validation;
use App\Services\ValidationCollectionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ValidationService
{
    protected $validationCollection;

    public function __construct(ValidationCollectionService $validationCollection)
    {
        $this->validationCollection = $validationCollection;
    }
    public function validateCollection(Request $request, $collectionKeys, $collectionType = 'user', $arrayRequest = null, $allOptional = false)
    {
        if ($arrayRequest) {
            $data = $arrayRequest;
        } else {
            $data = json_decode($request->getContent(), true);
        }

        if ($allOptional == false && empty($data)) {
            return "Blank request received! Please pass in valid request";
        }

        switch ($collectionType) {
            case 'todo':
                $collection = $this->validationCollection->todo($collectionKeys);
                break;

            case 'idInteger':
                $collection = $this->validationCollection->idInteger($collectionKeys);
                break;
        }

        $constraint = new Assert\Collection($collection);
        return $this->validate($data, $constraint);
    }

    public function validate($data, $constraint)
    {
        $validator = Validation::createValidator($data, $constraint);
        $validatorRes = $validator->validate($data, $constraint);
        if (count($validatorRes) > 0) {
            return $this->parseValidationErrors($validatorRes);
        } else {
            return true;
        }
    }

    private function parseValidationErrors($validatorRes)
    {
        $violatedKeys = array();
        foreach ($validatorRes as $i => $violation) {
            $key = $violation->getPropertyPath();
            preg_match_all('[\[(.*?)\]]', $key, $matches);
            // $violatedKeys[$matches[1]] = $violation->getMessage();

            $error = preg_replace('/(\This value)|(\This field)/', end($matches[1]), $violation->getMessage());
            // add request key
            if (strpos($error, end($matches[1])) === false) {
                $error = end($matches[1]) . " : " . $error;
            }

            return $error;
        }
        return $violatedKeys;
    }
}
