<?php

use App\Exceptions\AppException;
use Tools\MyTwig;


define('DS', DIRECTORY_SEPARATOR); // Define permet de definir des Constantes
define('RACINE', new DirectoryIterator(dirname(__FILE__)) . DS . ".." . DS);

include_once(RACINE . DS . 'config/conf.php');
include_once(PATH_VENDOR . "autoload.php");
include_once(RACINE . DS . "includes/params.php");
$vue = "errors\\error.html.twig";

try {
    if ((!array_key_exists('c', $_GET)) || (!array_key_exists('a', $_GET))) { // Permet de vérifier si l'url est correctement saisie
        throw new Exception("Erreur, cette page n'existe pas");
    }

    $BaseController = filter_input(INPUT_GET, 'c', FILTER_SANITIZE_SPECIAL_CHARS); // Filtre sur l'url pour éviter prblm injection.
    $action = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_SPECIAL_CHARS); // Pareil sur le a 
// En gros ici, c'est comme mes routes sur laravel, je donne mon controller, et la méthode à exécuter sur le controller
    $controller = "App\\Controller\\" . $BaseController . "Controller";
    if (class_exists($controller, true)) { // Si par le nom de classe définie par mon url existe, alors
        $c = new $controller(); // Je créer un objet portant le nom de ma classe controller, pour ensuite faire appeler les méthodes de cette classe en passant par $c
        $params = array(array_slice($_REQUEST, 2));
        call_user_func_array(array($c, $action), $params); // -> Route::controller('Nom')->group(function(){Route::get('/',méthode)}) Similaire à ça
    } else {
        throw new Error("Le contrôleur demandé n'existe pas");
    }
} catch (Error $ex) {
    if (MODE_DEV == 'on') {
        print_r($ex->getMessage());
    }
    MyTwig::afficherVue($vue,array());
} catch (AppException $ex) {
    if (MODE_DEV == 'on') {
        print_r($ex->getMessage());
    }
    MyTwig::afficherVue($vue,array());
} catch (Exception $ex) {
    if (MODE_DEV == 'on') {
        print_r($ex->getMessage());
    }
    MyTwig::afficherVue($vue,array());
}



/* * Donnez une raison pour effectuer ce test : 
  Permet de vérifier que l'url soit complète.

 * *Que va faire cette instruction ?
  Déclare et instancie l'objet de la classe d'un controller, donné en URL

 * *Que font ces instructions ?
  array(array_slice) permet de récupérer le deuxième element d'un array
  call_user_func_array Permet d'appeler une fonction en fournissant le nom de la méthode ainsi que les paramètres.

 * *Pourquoi utilise-t-on le tableau $_REQUEST plutôt que $_GET ou $_POST ?
  Car Request contient tous les éléments de ma requête.

  La sequence permet de retourner la meme vue avec un message d'erreur different exemple error 403

 * *Essayez avec cette URL : http://monpetitmvc/?c=GestionClient&a=chercheTous
  Car les méthodes n'existe pas

 * * Permet de rendre nul une variable. Et la méthode pour récupérer l'adresse d'un client. 


 */
