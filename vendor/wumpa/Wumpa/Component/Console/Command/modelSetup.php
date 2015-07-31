<?php

namespace Wumpa\Component\Console\Command;

use RuntimeException;
use Wumpa\Component\App\App;
use Wumpa\Component\App\AppFactory;
use Wumpa\Component\Database\Analyzer\PgAnalyzer;
use Wumpa\Component\FileSystem\File;
use Wumpa\Component\Renderer\Renderer;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Model generator command
 *
 * @author Léopold Jacquot <leopold.jacquot@gmail.com>
 * @author Bastien de Luca <dev@de-luca.io>
 */
class ModelSetup extends WumpaCommand {

    /**
     * @var null|PgAnalyzer
     */
    private $analyzer = null;

    /**
     * @var array
     */
    private $tables = [];

    protected function configure() {
        $this->setName('model')
             ->setDescription('Create a model for Wumpa framework');

        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        parent::execute($input, $output);

        $this->getOutput()
             ->writeln('<comment>Wumpa Model Generator started...</comment>');
        $this->getOutput()
             ->writeln('<comment>This tool will generate model classes from your database structure.</comment>');
        $this->getOutput()
             ->writeln('<comment>A configured database is required.</comment>');

        if ($this->getInput()->getArgument('path')) {
            $this->setProjectPath($this->getInput()->getArgument('path'));
        }
        else {
            $this->setProjectPath($this->askProjectPath());
        }

        App::init($this->getProjectPath(), Appfactory::APP_TERM);

        if (is_null(App::getDatabase())) {
            $question =
                new ConfirmationQuestion('Do you want to setup a database now? [Y/n] ',
                    true);

            if ($this->getQuestionHelper()
                     ->ask($this->getInput(), $this->getOutput(), $question)
            ) {
                $command = $this->getApplication()->find('db');

                $arguments = [
                    'command' => 'db',
                    'path' => $this->getProjectPath()
                ];

                $command->run(new ArrayInput($arguments), $this->getOutput());
            } else {
                $this->getOutput()
                     ->writeln('<error>A database connexion is required...</error>');
                return -1;
            }
        }

        $this->checkDatabaseConnexion();

        $this->generateModels();

        $this->getOutput()->writeln('<info>Model generation is done!</info>');

        return 0;
    }

    private function generateModels() {
        switch (App::getDatabase()->getDriver()) {
            case "pgsql":
                $this->setAnalyzer(new PgAnalyzer());
                break;
            default:
                throw new RuntimeException('Only PostgreSQL is supported for now');
        }

        $this->setTables([]);

        foreach ($this->getAnalyzer()->getTables() as $table) {
            $question =
                new Question('Class name for ' . $table . ' table [' . $table
                             . '] ', $table);
            $class    = $this->getQuestionHelper()
                             ->ask($this->getInput(), $this->getOutput(),
                                 $question);

            $this->addTable($table, $class);
        }

        foreach ($this->getTables() as $table => $class) {
            $this->generateModel($table, $class);
        }
    }

    /**
     * @param $tableName
     * @param $className
     */
    private function generateModel($tableName, $className) {
        $renderer = new Renderer(__DIR__ . '/../FileTemplate');

        $data                 = [];
        $data["className"]    = $className;
        $data["tableName"]    = $tableName;
        $data["columns"]      = $this->getAnalyzer()->getCols($tableName);
        $data["primaries"]    = $this->getAnalyzer()->getPK($tableName);
        $data["dependencies"] = $this->findDependencies($tableName);
        $data["compositions"] = $this->findCompositions($tableName);
        $model                =
            new File(App::get()->getModelDir() . $className . '.php');
        $model->open();
        fwrite($model->getResource(),
            $renderer->render('Model.php.twig', $data));
        $model->close();
    }

    /**
     * @param $tableName
     *
     * @return array
     */
    private function findCompositions($tableName) {
        $compositions = [];
        foreach ($this->getAnalyzer()->getTables() as $table) {
            foreach ($this->getAnalyzer()->getFK($table) as $fk => $targetTable)
            {
                if ($targetTable === $tableName) {
                    $compositions[$this->getTables()[$table]] = $fk;
                }
            }
        }

        return $compositions;
    }

    /**
     * @param $tableName
     *
     * @return array
     */
    private function findDependencies($tableName) {
        $dependencies = [];
        foreach ($this->getAnalyzer()->getFK($tableName) as $fk => $table) {
            $dependencies[$fk] = $this->getTables()[$table];
        }

        return $dependencies;
    }

    /**
     * @return mixed
     */
    public function getAnalyzer() {
        return $this->analyzer;
    }

    /**
     * @param $analyzer
     *
     * @return $this
     */
    public function setAnalyzer($analyzer) {
        $this->analyzer = $analyzer;

        return $this;
    }

    /**
     * @return array
     */
    public function getTables() {
        return $this->tables;
    }

    /**
     * @param $tables
     *
     * @return $this
     */
    public function setTables($tables) {
        $this->tables = $tables;

        return $this;
    }

    /**
     * @param $tableName
     * @param $className
     */
    public function addTable($tableName, $className) {
        $this->tables[$tableName] = $className;
    }
}