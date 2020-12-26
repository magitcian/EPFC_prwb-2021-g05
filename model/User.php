<?php

require_once "framework/Model.php";

class User extends Model {

    private $mail;
    private $hashed_password;
    private $full_name;

    public function __construct($mail, $hashed_password, $full_name) {
        $this->mail = $mail;
        $this->hashed_password = $hashed_password;
        $this->full_name = $full_name;
    }

    public function get_full_name(){
        return $this->full_name;
    }

    public function get_mail(){
        return $this->mail;
    }

    public function update() {
        if(self::get_user_by_mail($this->mail))
            self::execute("UPDATE User SET password=:password WHERE mail=:mail ", 
                          array("mail"=>$this->mail, "password"=>$this->hashed_password));
        else
            self::execute("INSERT INTO User(Mail, FullName, Password) VALUES(:mail,:full_name,:password)", 
                          array("mail"=>$this->mail, "full_name"=>$this->full_name, "password"=>$this->hashed_password));
        return $this;
    }

    public static function get_user_by_mail($mail) {
        $query = self::execute("SELECT * FROM User where mail = :mail", array("mail"=>$mail));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["Mail"], $data["Password"], $data["FullName"]);
        }
    }

    public static function get_users() {
        $query = self::execute("SELECT * FROM User", array());
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new User($row["Mail"], $row["Password"], $row['FullName']);
        }
        return $results;
    }

    public function validate(){
        $errors = array();
        $errors = User::validate_full_name($this->full_name);
        $errors = array_merge($errors, User::validate_email($this->mail));
        return $errors;
    }
    
    private static function validate_password($password){
        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = "Password length must be at least 8 characters.";
        } if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?\\-]/", $password))) {
            $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }
    
    public static function validate_passwords($password, $password_confirm){
        $errors = User::validate_password($password);
        if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }
        return $errors;
    }

    public static function validate_full_name($full_name){
        $errors = [];
        if (!isset($full_name) || !is_string($full_name) || strlen($full_name) < 3) {
            $errors[] = "Full name length must be at least 3 characters.";
        }
        return $errors;
    }

    public static function validate_email($mail){
        $errors = [];
        $user = self::get_user_by_mail($mail);
        if (!(isset($mail) && is_string($mail) && strlen($mail) > 0)) {
            $errors[] = "Email is required.";
        }
        if ($user) {
            $errors[] = "This email already exists.";
        } 
        $patternEmail = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
        if (!(isset($mail) && is_string($mail) && preg_match($patternEmail, $mail))) {
            $errors[] = "Email must start by a letter and must contain only letters and numbers.";
        }
        return $errors;
    }
    
    public static function validate_unicity($mail){
        $errors = [];
        $user = self::get_user_by_mail($mail);
        if ($user) {
            $errors[] = "This user already exists.";
        } 
        return $errors;
    }

    //indique si un mot de passe correspond à son hash
    private static function check_password($clear_password, $hash) {
        return $hash === Tools::my_hash($clear_password);
    }
    
    //renvoie un tableau d'erreur(s) 
    //le tableau est vide s'il n'y a pas d'erreur.
    public static function validate_login($mail, $password) {
        $errors = [];
        $user = User::get_user_by_mail($mail);
        if ($user) {
            if (!self::check_password($password, $user->hashed_password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find a user with the mail '$mail'. Please sign up.";
        }
        return $errors;
    }
}
