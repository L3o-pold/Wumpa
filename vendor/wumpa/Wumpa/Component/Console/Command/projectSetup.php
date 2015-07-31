<?php

namespace Wumpa\Component\Console\Command;

use RuntimeException;
use Wumpa\Component\FileSystem\File;
use Wumpa\Component\Renderer\Renderer;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Create a new Wumpa project command
 *
 * @author Léopold Jacquot <leopold.jacquot@gmail.com>
 * @author Bastien de Luca <dev@de-luca.io>
 */
class ProjectSetup extends WumpaCommand {

    protected function configure() {
        $this->setName('new')
             ->setDescription('Create a new project with Wumpa framework');

        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        parent::execute($input, $output);

        $this->getOutput()
             ->writeln('<comment>Wumpa project Generator started...</comment>');
        $this->getOutput()
             ->writeln('<comment>This will generate a new project in desired directory.</comment>');

        $this->createProjectFolder();
        $this->generateFiles();

        $this->getOutput()->writeln('<info>Project generation is done!</info>');

        $question =
            new ConfirmationQuestion('Do you want to setup a database now? [Y/n] ',
                true);

        if ($this->questionHelper->ask($this->input, $this->output,
            $question)
        ) {
            $command = $this->getApplication()->find('db');

            $arguments = [
                'command' => 'db',
                'path' => $this->getProjectPath()
            ];

            $command->run(new ArrayInput($arguments), $this->output);
        }
    }

    /**
     * @return bool
     * @internal param $input
     * @internal param $output
     */
    private function createProjectFolder() {

        if ($this->getInput()->getArgument('path')) {
            $this->setProjectPath($this->getInput()->getArgument('path'));
        }
        else {
            $this->setProjectPath($this->askProjectPath());
        }

        $paths = [
            'config',
            'controller',
            'model',
            'view',
            'view/templates'
        ];

        if (is_dir($this->getProjectPath()) === false
            && (!mkdir($this->getProjectPath())
                || !is_writable($this->getProjectPath())
            )
        ) {
                throw new RuntimeException(
                    'The project structure cannot be created because the destinaton folder '
                    . $this->getProjectPath() . ' is not writable'
                );
        }

        foreach ($paths as $path) {
            if (mkdir($this->getProjectPath() . $path) === false) {
                throw new RuntimeException('Project structure cannot be created');
            }
        }

        return true;
    }

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
        $database = new File($this->getProjectPath() . 'config/database.php');
        $database->open();
        fwrite($database->getResource(), $renderer->render('database.php.twig', []));
        $database->close();

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