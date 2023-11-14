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

}
