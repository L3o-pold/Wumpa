<?php

namespace Wumpa\Component\Console\Command;

use RuntimeException;
use Wumpa\Component\App\AppFactory;
use Wumpa\Component\App\App;
use Wumpa\Component\FileSystem\File;
use Wumpa\Component\Renderer\Renderer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Setup a database command
 *
 * @author Léopold Jacquot <leopold.jacquot@gmail.com>
 * @author Bastien de Luca <dev@de-luca.io>
 */
class DbSetup extends WumpaCommand {

    /**
     * @var array
     */
    private $databaseInfos = [];

    /**
     *
     */
    protected function configure() {
        $this->setName('db')
             ->setDescription('Configure Database for Wumpa framework');

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
             ->writeln('<comment>Wumpa Database Setup started...</comment>');
        $this->getOutput()
             ->writeln('<comment>This will setup your database connection for you.</comment>');

        if ($this->getInput()->getArgument('path')) {
            $this->setProjectPath($this->getInput()->getArgument('path'));
        }
        else {
            $this->setProjectPath($this->askProjectPath());
        }

        $this->askDatabaseInformation();
        $this->generateDatabaseConfigurationFile();

        $output->writeln('<info>Database configuration is now done!</info>');

        $question =
            new ConfirmationQuestion('Do you want to test database connectivity? [Y/n] ',
                true);

        if ($this->getQuestionHelper()
                 ->ask($this->getInput(), $this->getOutput(), $question)
        ) {
            App::init($this->getProjectPath(), Appfactory::APP_TERM);
            $this->checkDatabaseConnexion();
        }
    }

    /**
     * @return array
     */
    public function getDatabaseInfos() {
        return $this->databaseInfos;
    }

    /**
     * @return string
     */
    private function askDatabaseDriver() {

        $question = new ChoiceQuestion('Enter the driver of the database ', [
            'mysql',
            'pgsql'
        ], 0);

        $question->setErrorMessage('Database driver %s is invalid.');

        return strtolower($this->getQuestionHelper()
                               ->ask($this->getInput(), $this->getOutput(),
                                   $question));
    }

    private function askDatabaseInformation() {

        $databaseInfos = [
            'dbName' => 'database name',
            'host' => 'database hostname',
            'port' => 'database port',
            'user' => 'database username',
            'password' => 'database password'
        ];

        $this->databaseInfos['driver'] = $this->askDatabaseDriver();

        foreach ($databaseInfos as $key => $info) {
            $question = new Question('Enter the ' . $info . ' ');

            $question->setValidator(function ($answer) use (&$info) {
                if (empty(trim($answer)) === true) {
                    throw new RuntimeException($info . ' cannot be empty');
                }

                return $answer;
            });

            $this->databaseInfos[$key] = $this->getQuestionHelper()
                                                   ->ask($this->getInput(),
                                                       $this->getOutput(),
                                                       $question);
        }
    }

    private function generateDatabaseConfigurationFile() {
        $renderer = new Renderer(__DIR__ . '/../FileTemplate');

        $file = new File($this->getProjectPath() . 'config/database.php');
        $file->open();
        fwrite(
            $file->getResource(),
            $renderer->render(
                'database.php.twig',
                ['db' => $this->getDatabaseInfos()]
            )
        );

        $file->close();
    }
}