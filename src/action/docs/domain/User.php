<?php
namespace App\action\docs\domain;
use JsonSerializable;
/**
 *
 * @OA\Schema(
 *     title="User",
 *     description="A user model response"
 * )
 *
 */
abstract class User implements JsonSerializable
{
    /**
     * @OA\Property(type="integer", format="int64", readOnly=true, example=1)
     */
    private $id;

    /**
     * @OA\Property(type="string", example="Test")
     */
    private $name;

    /**
     * @OA\Property(type="string", example="test@gmail.com")
     */
    private $email;
}
