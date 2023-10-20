<?php
namespace Tools;


abstract class MyTwig {

    private static function getLoader(){
        $loader = new \Twig\Loader\FilesystemLoader(PATH_VIEW);
        $twigExt = new \Twig\Environment($loader , array('cache'=>false,'debug'=>true,));
        $twigExt->addExtension(new \Twig\Extension\DebugExtension());
        return $twigExt;
        
    }
    
    public static function afficherVue($vue, $params){
        $twig = self::getLoader();
        $template = $twig->load($vue);
        echo $template->render($params);
    }
    
    
}
