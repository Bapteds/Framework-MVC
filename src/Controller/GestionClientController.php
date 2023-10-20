<?php
declare(strict_types=1);

namespace App\Controller;
use App\Model\GestionClientModel;
use ReflectionClass;
use App\Exceptions\AppException;
use Tools\MyTwig;
use App\Entity\Client;


class GestionClientController {

    public function chercheUn(array $params){
        $modele = new GestionClientModel();
        $id = filter_var(intval($params["id"]),FILTER_VALIDATE_INT);
        $unClient = $modele->find($id);
        if($unClient){ // Pour vérifier le fait que l'on récupère bien un client.
            $r = new ReflectionClass($this); //Pour créer un objet de type client
            $vue = str_replace('Controller','View',$r->getShortName())."/unClient.html.twig";
            MyTwig::afficherVue($vue,array('unClient'=>$unClient));
           //include_once PATH_VIEW . str_replace('Controller', 'View', $r->getShortName()). "/unClient.php";
        }else{
            throw new AppException("Client " .$id ." inconnu");
        }
    }
    
    public function chercherTous(){
        $modele = new GestionClientModel();
        $clients = $modele->findAll();
          if($clients){
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller','View',$r->getShortName())."/plusieursClients.html.twig";
            MyTwig::afficherVue($vue, array('desClients'=>$clients,'nombreClient'=>count($clients)));
            //include_once PATH_VIEW . str_replace('Controller','View',$r->getShortName()). "/plusieursClients.php";
        }
        else {
            throw new AppException("Aucun client à afficher");
        }
    }
    
    public function creerClient(){
        $vue = "GestionClientView\\creerClient.html.twig";
        MyTwig::afficherVue($vue,array());
    }
    
    public function enregistreClient(array $params){
        try{
            $client = new Client($params);
            $modele = new GestionClientModel();
            $modele->enregistreClient($client);
        } catch (Exception $ex) {
            throw new AppException("Erreur à l'enregistrement d'un nouveau client");
        }
    }
    
}
