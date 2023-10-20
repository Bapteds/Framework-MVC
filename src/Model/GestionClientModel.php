<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Client;
use Tools\Connexion;
use Exception;
use App\Exceptions\AppException;
use PDO;

class GestionClientModel {

    public function find(int $id): Client {
        try {
            $unObjetPdo = Connexion::getConnexion();
            $sql = "SELECT * FROM CLIENT where id=:id";
            $ligne = $unObjetPdo->prepare($sql);
            $ligne->bindValue(':id', $id, PDO::PARAM_INT);
            $ligne->execute();
            return $ligne->fetchObject(Client::class); // Permet de récuperer en ligne les valeur de la base
        } catch (Exception $ex) {
            throw new AppException("Erreur inattendue");
        }
    }

    public function findAll(): Array {
        try {
            $unObjetPdo = Connexion::getConnexion();
            $lignes = $unObjetPdo->query("SELECT * FROM CLIENT");
            return $lignes->fetchAll(PDO::FETCH_CLASS, Client::class);
        } catch (Exception $ex) {
            throw new AppException("Erreur d'application");
        }
    }

    public function enregistreClient(Client $client) {
        try {
            $unObjetPdo = Connexion::getConnexion();
            $sql = "INSERT INTO CLIENT(titreCli,nomCli,prenomCli,adresseRue1Cli,adresseRue2Cli,cpCli,villeCli,telCli)VALUES(:titreCli,:nomCli,:prenomCli,:adresseRue1Cli,:adresseRue2Cli,:cpCli,:villeCli,:telCli)";
            $s = $unObjetPdo->prepare($sql);
            $s->bindValue(':titreCli', $client->getTitreCli(), PDO::PARAM_STR);
            $s->bindValue(':nomCli', $client->getNomCli(), PDO::PARAM_STR);
            $s->bindValue(':prenomCli', $client->getPrenomCli(), PDO::PARAM_STR);
            $s->bindValue(':adresseRue1Cli', $client->getAdresseRue1Cli(), PDO::PARAM_STR);
            $s->bindValue(':adresseRue2Cli', ($client->getAdresseRue2Cli() == "") ? (null) : ($client->getAdresseRue2Cli()), PDO::PARAM_STR);
            $s->bindValue(':cpCli', $client->getCpCli(), PDO::PARAM_STR);
            $s->bindValue(':villeCli', $client->getVilleCli(), PDO::PARAM_STR);
            $s->bindValue(':telCli', $client->getTelCli(), PDO::PARAM_STR);
            $s->execute();
        } catch (Exception $ex) {
            throw new AppException("Erreur technique inattendue");
        }
    }
}
