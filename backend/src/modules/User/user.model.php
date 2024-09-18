<?php
class User
{
    public $id;
    public $name;
    public $password;
    public $userType;

    public function __construct($userData)
    {
        $this->id = $userData['id'] ?? null;
        $this->name = $userData['name'];
        $this->password = $userData['password'];
        $this->userType = $userData['userType'] ?? 'Annotator';
    }

    public function saveUser($conn)
    {
        $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (name, password, userType) VALUES ('$this->name', '$hashedPassword', '$this->userType')";
        return mysqli_query($conn, $query);
    }

    public function login($conn)
    {
        $query = "SELECT * FROM users WHERE name='$this->name'";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);
        if (!$user || !password_verify($this->password, $user['password'])) {
            return false;
        }
        return $user;
    }

    public static function fetchUser($conn, $id)
    {
        $query = "SELECT * FROM users WHERE id='$id'";
        $result = mysqli_query($conn, $query);
        return mysqli_fetch_assoc($result);
    }
    public static function fetchAllUsers($conn)
    {
        $query = "SELECT * FROM users";
        $result = mysqli_query($conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function updateUser($conn)
    {
        $query = "UPDATE users SET name='$this->name', userType='$this->userType' WHERE id='$this->id'";
        return mysqli_query($conn, $query);
    }
}
