<?php

namespace App\Services;

use App\Constants\StatusConstants;
use Symfony\Component\Validator\Constraints as Assert;

class ValidationCollectionService
{

    private function getConstraints($collection, $keys)
	{
		$returnCollection = array();
		foreach ($keys as $key) {
			$returnCollection[$key] = $collection[$key];
		}
		return $returnCollection;
    }
    
    public function todo($keys)
    {
        $collection = array(
			"name" =>  [new Assert\Type('string')],
			"description" =>  [new Assert\Type('string')],
            "status" =>  [new Assert\Choice(StatusConstants::$statusArray)]
		);
		return $this->getConstraints($collection, $keys);
    }

    public function idInteger($keys)
    {
        $collection = array(
            "id" =>  [new Assert\Optional([new Assert\Type('integer')])]
		);
		return $this->getConstraints($collection, $keys);
    }
}