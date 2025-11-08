<?php

namespace App\Data\User;

/**
 * @OA\Schema(
 *     schema="RegisterDTO",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="password", type="string"),
 *     @OA\Property(property="is_admin", type="boolean", default=false),
 * )
 */

class RegisterDTO
{
    public string $name;
    public string $email;
    public string $password;
    public bool $is_admin;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->is_admin = $data['is_admin'] ?? false;
    }
}
