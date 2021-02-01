<?php

/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 18:20
 * PHP version 7
 */

namespace App\Model;

/**
 *
 */
class UserManager extends AbstractManager
{
    public const TABLE = 'UBALLERS';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    /**
     * @param array $user
     * @return int
     */
    public function insert(array $user): int
    {
        // prepared request
        $statement = $this->pdo->prepare(
            "INSERT INTO " . self::TABLE . " (`firstname`, `lastname`, `email`, `birth_date`, `password`, `gender`) 
        VALUES (:firstname, :lastname, :email, :birth_date, :password, :gender) "
        );
        $statement->bindValue('firstname', $user['firstname'], \PDO::PARAM_STR);
        $statement->bindValue('lastname', $user['lastname'], \PDO::PARAM_STR);
        $statement->bindValue('email', $user['email'], \PDO::PARAM_STR);
        $statement->bindValue('birth_date', $user['birth_date'], \PDO::PARAM_STR);
        $statement->bindValue('password', password_hash($user['password'], PASSWORD_DEFAULT), \PDO::PARAM_STR);
        $statement->bindValue('gender', $user['gender'], \PDO::PARAM_STR);


        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Get the mail and password of the user
     * @param string $userEmail
     * @param string $userPassword
     * @return mixed|string
     */

    public function getUser(string $userEmail, string $userPassword)
    {
        if (password_verify($userPassword, $this->getPassword($userEmail)) === true) {
            $hashedPassword = $this->getPassword($userEmail);
        } else {
            return 'error';
        }
        $user = $this->pdo->prepare(
            "SELECT * FROM " . self::TABLE . " WHERE email = :userEmail AND password = :hashedPassword"
        );
        $user->bindValue(':userEmail', $userEmail, \PDO::PARAM_STR);
        $user->bindValue(':hashedPassword', $hashedPassword, \PDO::PARAM_STR);
        $user->execute();
        $user = $user->fetch();

        return $user;
    }

    /**
     * Get the mail of the user
     * @param string $userEmail
     * @return mixed
     */
    public function getUserByMail(string $userEmail)
    {
        $user = $this->pdo->prepare(
            "SELECT * FROM " . self::TABLE . " WHERE email = :userEmail"
        );
        $user->bindValue(':userEmail', $userEmail, \PDO::PARAM_STR);
        $user->execute();
        $userMail = $user->fetch();
        return $userMail;
    }

    /**
     * Get the password in db
     * @param string $userEmail
     * @return mixed
     */
    public function getPassword(string $userEmail)
    {
        $password = $this->pdo->prepare(
            "SELECT password FROM " . self::TABLE . " WHERE email = :userEmail"
        );
        $password->bindValue(':userEmail', $userEmail, \PDO::PARAM_STR);
        $password->execute();
        $pass = $password->fetch();
        $pass = $pass['password'];
        return $pass;
    }

    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }
}
