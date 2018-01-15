<?php

    namespace Keywords\Controllers;
    
    use Silex\Application;
    use Symfony\Component\HttpFoundation\Request;

    class NodesController {

        public function getNewForm(Application $app): string {
            return $app['twig']->render('new_keyword.twig');
        }

        public function create(Application $app, Request $request): string {
            $params = $request->request->all();
            $errors = [];

            if(empty($params['node'])){
                $errors[] = 'Please, enter a new keyword.';
            }
            if(!empty($errors)){
                $params = array_merge($params, ['errors' => $errors]);
                return $app['twig']->render('new_keyword.twig', $params);
            }
        }

    }

?>