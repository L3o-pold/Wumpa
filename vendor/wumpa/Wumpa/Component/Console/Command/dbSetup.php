<?php

namespace Wumpa\Component\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Wumpa\Component\FileSystem\File;
use Wumpa\Component\Renderer\Renderer;
use Wumpa\Component\App\App;

/**
 * @package dbSetup
 * @author  Léopold Jacquot
 */
class dbSetup extends wumpaCommand {

    /**
     * @var array
     */
    private $database_infos = [];

    /**
     *
     */
    protected function configure() {
        $this->setName('db')
             ->setDescription('Configure Database for Wumpa framework')
             ->addArgument('project_path', InputArgument::OPTIONAL,
                 'Project path (absolute path)');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        parent::execute($input, $output);

        $this->getOutput()->writeln('<comment>Wumpa Database Setup started...</comment>');
        $this->getOutput()->writeln('<comment>This will setup your database connection for you.</comment>');

        if ($this->getInput()->getArgument('project_path')) {
            $this->project_path = $this->getInput()->getArgument('project_path');
        }
        else {
            $this->project_path = $this->askProjectPath();
        }

        $this->askDatabaseInformation();
        $this->generateDatabaseConfigurationFile();

        $output->writeln('<info>Database configuration is now done!</info>');

        $question =
            new ConfirmationQuestion('Do you want to test database connectivity? [Y/n] ', true);

        if ($this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(), $question)) {
            $this->checkDatabaseConnexion();
        }
    }

    /**
     * @return array
     */
    public function getDatabaseInfos() {
        return $this->database_infos;
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

        return strtolower($this->getQuestionHelper()->ask($this->getInput(),
            $this->getOutput(), $question));
    }

    /**
     *
     */
    private function askDatabaseInformation() {

        $arr_infos = [
            'dbName' => 'database name',
            'host' => 'database hostname',
            'port' => 'database port',
            'user' => 'database username',
            'password' => 'database password'
        ];

        $this->database_infos['driver'] = $this->askDatabaseDriver();

        foreach ($arr_infos as $str_key => $str_info) {
            $question = new Question('Enter the ' . $str_info . ' ');

            $question->setValidator(function ($answer) use (&$str_info) {
                if (empty(trim($answer)) === true) {
                    throw new \RuntimeException($str_info . ' cannot be empty');
                }

                return $answer;
            });

            $this->database_infos[$str_key] =
                $this->getQuestionHelper()->ask($this->getInput(), $this->getOutput(),
                    $question);
        }
    }

    /**
     *
     */
    private function generateDatabaseConfigurationFile() {
        $renderer = new Renderer(__DIR__ . '/../FileTemplate');

        $file = new File($this->getProjectPath() . 'config/database.php');
        $file->open();
        fwrite($resource = $file->getResource(),
            $renderer->render('database.php.twig',
                ['db' => $this->getDatabaseInfos()]));

        $file->close();
    }
}