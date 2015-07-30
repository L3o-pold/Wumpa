<?php

namespace Wumpa\Component\Console\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Wumpa\Component\FileSystem\File;
use Wumpa\Component\Renderer\Renderer;


/**
 * @package projectSetup
 * @author  Léopold Jacquot
 */
class projectSetup extends wumpaCommand {

    protected function configure() {
        $this->setName('new')
             ->setDescription('Create a new project with Wumpa framework');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        parent::execute($input, $output);

        $this->getOutput()->writeln('<comment>Wumpa project Generator started...</comment>');
        $this->getOutput()->writeln('<comment>This will generate a new project in desired directory.</comment>');

        $this->createProjectFolder();
        $this->generateFiles();

        $this->getOutput()->writeln('<info>Project generation is done!</info>');

        $question = new ConfirmationQuestion(
            'Do you want to setup a database now? [Y/n] ',
            false
        );

        if ($this->questionHelper->ask($this->input, $this->output, $question)) {
            $command = $this->getApplication()->find('db');

            $arguments = [
                'command' => 'db',
                'project_path' => $this->project_path
            ];

            $command->run(new ArrayInput($arguments), $this->output);
        }
    }

    /**
     * @param $input
     * @param $output
     *
     * @throws \Exception
     * @return bool True if project structure is created
     */
    private function createProjectFolder() {
        $this->project_path = $this->askProjectPath();

        if (substr($this->project_path, -1) != '/') {
            $this->project_path .= '/';
        }

        $arr_path = ['config', 'controller', 'model', 'view', 'view/templates'];

        if (is_dir($this->getProjectPath()) === false) {
            if (is_writable($this->getProjectPath()) === false) {
                throw new \Exception('The project structure cannot be created because the destinaton folder '
                                     . $this->getProjectPath() . ' is not writable');
            } else {
                mkdir($this->getProjectPath());
            }
        } else {
            throw new \Exception('The project structure cannot be created because the destinaton folder '
                                 . $this->getProjectPath() . ' already exists');
        }

        foreach ($arr_path as $str_path) {
            if (mkdir($this->getProjectPath() . $str_path) === false) {
                throw new \Exception('Project structure cannot be created');
            }
        } return true;
    }

    /**
     *
     */
    private function generateFiles() {
        $renderer = new Renderer(__DIR__ . '/../FileTemplate');
        //INDEX
        $index = new File($this->getProjectPath() . 'index.php');
        $index->open();
        fwrite($index->getResource(), $renderer->render('index.php.twig',
            ['wumpaPath' => realpath('Wumpa/app/app.php')]));
        $index->close();

        //HTACCESS
        $htaccess = new File($this->getProjectPath() . '.htaccess');
        $htaccess->open();
        fwrite($htaccess->getResource(),
            $renderer->render('htaccess.txt.twig', []));
        $htaccess->close();

        //DATABASE CONFIG
        $db = new File($this->getProjectPath() . 'config/database.php');
        $db->open();
        fwrite($db->getResource(),
            $renderer->render('database.php.twig', []));
        $db->close();

        //ROUTES CONFIG
        $routes = new File($this->getProjectPath() . 'config/routes.php');
        $routes->open();
        fwrite($routes->getResource(),
            $renderer->render('routes.php.twig', []));
        $routes->close();

        //SYSTEM CONFIG
        $system = new File($this->getProjectPath() . 'config/system.php');
        $system->open();
        fwrite($system->getResource(),
            $renderer->render('system.php.twig', []));
        $system->close();

        //EXEMPLE CONTROLLER
        $controller =
            new File($this->getProjectPath() . 'controller/HomeController.php');
        $controller->open();
        fwrite($controller->getResource(),
            $renderer->render('HomeController.php.twig', []));
        $controller->close();

        //EXEMPLE TEMPLATE
        $template = new File($this->getProjectPath()
                             . 'view/templates/defaultTemplate.html.twig');
        $template->open();
        fwrite($template->getResource(),
            $renderer->render('defaultTemplate.html.twig', []));
        $template->close();
    }
}