<?php

declare (strict_types=1);

namespace Tools;

use PDO;
use Tools\Connexion;
use App\Exceptions\AppException;
use Exception;
use Integer;

abstract class Repository {

// la methode getRepository permet de déclarer et instancier un objet de la classe Repository en fonction d'une classe
// $entity = A l'objet qui est lier au repo -> Si client -> ClientRepository
// 
// classNameLong -> Nom de la classe avec le chemin
// ClassNamespace -> namespace de la classe
// table -> Nom de la table en fonction de mon objet
// connexion -> Connexion a la base

    private string $classNameLong;
    private string $classNamespace;
    private string $table;
    private PDO $connexion;

    private function __construct(string $entity) {
        $tablo = explode("\\", $entity);
        $this->table = array_pop($tablo);
        $this->classNamespace = implode("\\", $tablo);
        $this->classNameLong = $entity;
        $this->connexion = Connexion::getConnexion();
    }

    public static function getRepository(string $entity): Repository {
        $repositoryName = str_replace('Entity', 'Repository', $entity) . 'Repository';
        $repository = new $repositoryName($entity);
        return $repository;
    }

    public function findAll(): Array {
        try {
            $sql = "SELECT * FROM " . $this->table;
            $lignes = $this->connexion->query($sql);
            $lignes->setFetchMode(PDO::FETCH_CLASS, $this->classNameLong, null);
            return $lignes->fetchAll();
        } catch (Exception $ex) {
            throw new AppException('Erreur application');
        }
    }

    public function findIds(): array {
        $sql = "SELECT id FROM " . $this->table;
        $lignes = $this->connexion->query($sql);
        $lignes->setFetchMode(PDO::FETCH_ASSOC);
        return $lignes->fetchAll();
    }

    public function find(int $id): ?object {
        $sql = "SELECT * FROM " . $this->table . " WHERE id=:id";
        $ligne = $this->connexion->prepare($sql);
        $ligne->bindValue(':id', $id, PDO::PARAM_INT);
        $ligne->execute();
        $objet = $ligne->fetchObject($this->classNameLong);
        return $objet == false ? null : $objet;
    }

    public function insert(object $objet): void {
        $attributs = (array) $objet;
        array_shift($attributs);
        $colonnes = "(";
        $colonnesParams = "(";
        $parametres = array();
        foreach ($attributs as $key => $value) {
            $key = str_replace("\0", "", $key);
            $c = str_replace($this->classNameLong, "", $key);
            $p = ":" . $c;
            if ($c != "id") {
                $colonnes .= $c . " ,";
                $colonnesParams .= " ? ,";
                $parametres[] = $value;
            }
        }
        $cols = substr($colonnes, 0, -1);
        $colsParams = substr($colonnesParams, 0, -1);
        $sql = "INSERT INTO " . $this->table . " " . $cols . ") values " . $colsParams . ")";
        $unObjetPDO = Connexion::getConnexion();
        $req = $unObjetPDO->prepare($sql);
        $req->execute($parametres);
    }

    public function countRows(): int {
        $sql = "SELECT COUNT(*) FROM " . $this->table;
        $ligne = $this->connexion->prepare($sql);
        $ligne->execute();
        $objet = $ligne->fetchColumn();
        return $objet;
    }
}
