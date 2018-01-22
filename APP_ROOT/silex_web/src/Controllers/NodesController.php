<?php

    namespace Keywords\Controllers;

    use Silex\Application;
    use Symfony\Component\Form\Form;
    use Symfony\Component\Form\FormError;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Form\Extension\Core\Type\FormType;

    class NodesController {

        public function getAll(Application $app): string {
            $this->checkSchema($app);

            $sql = 'SELECT * FROM nodes';

            $nodes = $app['db']->fetchAll($sql);

            return $app['twig']->render('get_all.twig', array('nodes' => $nodes));
        }

        public function getNewForm(Application $app): string {
            $form = $this->getForm($app);

            return $app['twig']->render('new_keyword.twig', array('form' => $form->createView()));
        }

        public function newLinkForm(Application $app): string {
            $form = $this->getForm($app);

            return $app['twig']->render('new_link.twig', array('form' => $form->createView()));
        }

        public function create(Application $app, Request $request): string {
            $this->checkSchema($app);

            $form = $this->getForm($app);

            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                if ($this->checkNodeExists($app, $data['node'])) {
                    $form->addError(new FormError('That node already exists'));
                }
            }

            if ($form->isValid()) {
                $data = $form->getData();

                $sql = 'INSERT INTO nodes (node) VALUES (:node)';
                $result = $app['db']->executeUpdate($sql, array('node' => $data['node']));

                // redirect somewhere
                return $app->redirect('/');
            }

            return $app['twig']->render('new_keyword.twig', array('form' => $form->createView()));
        }

        private function getForm($app) {
            return $form = $app['form.factory']->createBuilder(FormType::class, null)
                ->add('node', null, array(
                    'label' => false,
                    'attr' => array('class'=>'form-control form-control-lg')
                ))
                ->getForm();
        }

        private function checkSchema($app)
        {
            $schemaResult = $app['db']->fetchAll('PRAGMA table_info(nodes)');

            if (empty($schemaResult)) {
                $schema = [
                    'CREATE TABLE nodes(id INTEGER PRIMARY KEY NOT NULL, node TEXT NOT NULL)',
                    'CREATE TABLE links(id INTEGER PRIMARY KEY NOT NULL, node_1 INTEGER NOT NULL, node_2 INTEGER NOT NULL, FOREIGN KEY(node_1) REFERENCES nodes(id), FOREIGN KEY(node_2) REFERENCES nodes(id))'
                ];
                foreach ($schema as $sql) {
                    $app['db']->executeUpdate($sql);
                }
            } else {
                // make sure schema matches what we want and update if not
            }
        }

        /**
         * @param $app
         * @param $node
         * @return true if exists, false if not
         */
        private function checkNodeExists($app, $node)
        {
            $sql = 'SELECT id FROM nodes WHERE node=:node';

            $result = $app['db']->fetchAll($sql, array('node' => $node));

            if (count($result) > 0) {
                return true;
            }

            return false;
        }
    }
