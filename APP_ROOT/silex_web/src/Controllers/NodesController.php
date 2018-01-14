<?php

    namespace Keywords\Controllers;

    class Nodes {
        public function getNewForm(Application $app): string {
            return $app['twig']->render('new_keyword.twig');
        }
    }

?>