<?php

    namespace Keywords\Controllers;
    
    use Silex\Application;

    class NodesController {
        public function getNewForm(Application $app): string {
            return $app['twig']->render('new_keyword.twig');
        }
    }

?>