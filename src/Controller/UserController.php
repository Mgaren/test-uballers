<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/17
 * Time: 16:07
 * PHP version 7
 */

namespace App\Controller;

use App\Model\UserManager;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class UserController
 *
 */
class UserController extends AbstractController
{
    /**
     * Display user listing
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(): string
    {
        $userManager = new UserManager();
        $users = $userManager->selectAll();

        return $this->twig->render('user/index.html.twig', ['users' => $users]);
    }


    /**
     * Display user informations specified by $id
     *
     * @param int $id
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(int $id): string
    {
        $userManager = new userManager();
        $user = $userManager->selectOneById($id);

        return $this->twig->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * Verify password and email
     */
    public function verifyUser(): string
    {
        $userEmail  = '';
        $userPassword = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['incoming_email'], $_POST['incoming_password'])) {
                $userEmail = trim($_POST['incoming_email']);
                $userPassword = trim($_POST['incoming_password']);
            }
            $userManager = new UserManager();
            $user = $userManager->getUser($userEmail, $userPassword);
            if ($user === 'error') {
                return $this->twig->render('user/add.html.twig', ['error' => $user]);
            } else {
                return $this->twig->render('user/good.html.twig', ['user' => $user]);
            }
        }
    }

    /**
     * Display user creation page
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function add(): string
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['email'])) {
                $userManager = new userManager();
                $emailExists = $userManager->getUserByMail($_POST['email']);
                if (false === $emailExists) {
                    if ($_POST['email'] !== $_POST['emailconfirmation']) {
                        return "'l\'email n\'existe pas'";
                    } else {
                        $user = [
                            'firstname' => $_POST['firstname'],
                            'lastname' => $_POST['lastname'],
                            'email' => $_POST['email'],
                            'birth_date' => $_POST['birth_date'],
                            'password' => $_POST['password'],
                            'gender' => $_POST['gender'],
                        ];
                        $id = $userManager->insert($user);
                        header('Location:/user/show/' . $id);
                    }
                } else {
                    return $this->twig->render(
                        'user/add.html.twig',
                        ['double' => 'Cet email a déjà été utilisé']
                    );
                }
            }
        }
        return $this->twig->render('user/add.html.twig');
    }


    /**
     * Handle user deletion
     *
     * @param int $id
     */
    public function delete(int $id): void
    {
        $userManager = new userManager();
        $userManager->delete($id);
        header('Location:/user/index');
    }
}
