<?php

    namespace Keywords\Controllers;

    use Silex\Application;
    use Symfony\Component\Form\Form;
    use Symfony\Component\Form\FormError;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Form\Extension\Core\Type\FormType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

    class NodesController {

        public function getAll(Application $app): string {
            /*$this->checkSchema($app);

            $sql = 'SELECT * FROM nodes';

            $nodes = $app['db']->fetchAll($sql);

            return new JsonResponse($nodes);*/
            echo "I am working!!!";
        }

        /**
         * Retrieve and view a single node
         *
         * @param Application $app
         * @param $id
         * @return string
         * @throws \Exception
         */
        public function getNode(Application $app, $id): string {
            $node = $this->getNodeById($app, $id);

            /** If the node was not found, throw an exception */
            if (!$node) {
                throw new \Exception('this is not the node you are looking for');
            }
            /** also retrieve the links to this node to complete the picture */
            $node['links'] = $this->getNodeLinks($app, $node);

            return new JsonResponse($node);
        }

        public function postNode(Application $app, Request $request): string {
            $this->checkSchema($app);

            $form = $this->getForm($app);

            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                if ($this->getNodeByName($app, $data['node'])) {
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

            return new JsonResponse($form->getErrors(true, true));
        }

        /**
         * Handle POST of the new link form
         *
         * @param Application $app
         * @param Request $request
         * @param $id
         * @return string
         */
        public function createLink(Application $app, Request $request, $id): string {
            $this->checkSchema($app);

            $node = $this->getNodeById($app, $id);
            if (!$node) {
                throw new \Exception('The node you are trying to link to does not exist');
            }

            $existingLinks = $this->getNodeLinks($app, $node);
            $form = $this->getLinkForm($app, $node);

            $form->handleRequest($request);

            if ($form->isValid()){
                $data = $form->getData();

                /** Deal with new links */
                foreach ($data['links'] as $linkId) {
                    $found = false;
                    foreach ($existingLinks as $link) {
                        if ($link['id'] == $linkId) {
                            $found = true;
                        }
                    }
                    // if found is false, it is new in the form, but not in the DB
                    if (!$found) {
                        $sql = 'INSERT INTO links VALUES (null, :node_1, :node_2)';
                        $result = $app['db']->executeUpdate($sql, array('node_1' => $linkId, 'node_2' => $id));
                        $result = $app['db']->executeUpdate($sql, array('node_1' => $id, 'node_2' => $linkId));
                    }
                }
                /** Deal with removed links */
                foreach ($existingLinks as $link) {
                    $found = false;
                    foreach ($data['links'] as $linkId) {
                        if ($link['id'] == $linkId) {
                            $found = true;
                        }
                    }
                    // if found is false, it was not in the form, but in the DB, so must be removed
                    if (!$found) {
                        $sql = 'DELETE FROM links WHERE (node_1=:id and node_2=:linkid) OR (node_1=:linkid and node_2=:id)';
                        $result = $app['db']->executeUpdate($sql, array('id' => $id, 'linkid' => $link['id']));
                    }
                }

                return $app->redirect('/');
            }

            return new JsonResponse($form->getErrors(true, true));
        }

        private function getForm($app) {
            return $form = $app['form.factory']->createBuilder(FormType::class, null)
                ->add('node', null, array(
                    'label' => false,
                    'attr' => array('class'=>'form-control form-control-lg')
                ))
                ->getForm();
        }

        private function getLinkForm($app, $node) {
            /** get all nodes except the one we are linking (don't want to link to ourself) */
            $sql = 'SELECT * FROM nodes WHERE id != :id';
        
            $nodes = $app['db']->fetchAll($sql, array('id' => $node['id']));

            /** create a choices array as needed by the form type key=name value=id */
            $choices = array();
            foreach ($nodes as $n) {
                $choices[$n['node']] = $n['id'];
            }

            /** Get the existing links so that they are pre-selected on the form */
            $links = $this->getNodeLinks($app, $node);
            $data = array();
            foreach ($links as $link) {
                $data[] = $link['id'];
            }

            return $form = $app['form.factory']->createBuilder(FormType::class, null)
                ->add('links', ChoiceType::class, array(
                    'label' => false,
                    'choices' => $choices,
                    'multiple' => true,
                    'data' => $data // pre-selected data
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
         * Retrieve a node by name (case insensitive)
         *
         * @param $app
         * @param $node
         * @return array|null
         */
        private function getNodeByName($app, $node)
        {
            $sql = 'SELECT * FROM nodes WHERE lower(node)=:node';

            $result = $app['db']->fetchAll($sql, array('node' => strtolower($node)));

            if (count($result) > 0) {
                return $result[0];
            }

            return null;
        }

        /**
         * Retrieve a node by ID
         *
         * @param $app
         * @param $id
         * @return array|null
         */
        private function getNodeById($app, $id)
        {
            $sql = 'SELECT * FROM nodes WHERE id=:id';

            $result = $app['db']->fetchAll($sql, array('id' =>$id));

            if (count($result) > 0) {
                return $result[0];
            }

            return null;
        }

        /**
         * Fetch all the links for a node.
         *
         * NOTE: only 1 side is checked, this should be enough as we double link everything
         *
         * @param $app
         * @param $node
         * @return mixed
         */
        private function getNodeLinks($app, $node)
        {
            $sql = 'SELECT n.id,n.node FROM links l LEFT JOIN nodes n on n.id=l.node_1 WHERE l.node_2=:id';

            $nodes = $app['db']->fetchAll($sql, array('id' => $node['id']));

            return $nodes;
        }
    }
