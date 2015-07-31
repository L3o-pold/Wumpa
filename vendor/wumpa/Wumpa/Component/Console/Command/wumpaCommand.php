<?php

namespace Wumpa\Component\Console\Command;

use Exception;
use RuntimeException;
use Wumpa\Component\App\App;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Base class for all Wumpa framework commands.
 *
 * @author Léopold Jacquot <leopold.jacquot@gmail.com>
 */
abstract class WumpaCommand extends Command {

    /**
     * @var null|InputInterface
     */
    protected $input = null;

    /**
     * @var null|OutputInterface
     */
    protected $output = null;

    /**
     * @var null|QuestionHelper
     */
    protected $questionHelper = null;

    /**
     * @var null|string
     */
    protected $projectPath;

    protected function configure() {
        $this->addArgument('path', InputArgument::OPTIONAL,
            'Project path (absolute path)');
    }

    /**
     * @param $projectPath
     *
     * @return $this
     */
    protected function setProjectPath($projectPath) {
        if (substr($projectPath, -1) != '/') {
            $projectPath .= '/';
        }

        $this->projectPath = $projectPath;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function getProjectPath() {
        return $this->projectPath;
    }

    /**
     * @return null|InputInterface
     */
    public function getInput() {
        return $this->input;
    }

    /**
     * @return null|OutputInterface
     */
    public function getOutput() {
        return $this->output;
    }

    /**
     * @return null|QuestionHelper
     */
    public function getQuestionHelper() {
        return $this->questionHelper;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->questionHelper = $this->getHelper('question');
        $this->input          = $input;
        $this->output         = $output;
    }

    /**
     * @return mixed
     */
    protected function askProjectPath() {
        $question = new Question('Enter the path of the project ');

        $question->setValidator(function ($answer) {
            if (empty(trim($answer)) === true) {
                throw new RuntimeException('Project path cannot be empty');
            }

            return $answer;
        });

        return $this->questionHelper->ask($this->input, $this->output, $question);
    }

    /**
     * Check database connexion
     * @return bool
     */
    protected function checkDatabaseConnexion() {
        try {
            App::getDatabase()->connect();
            $this->getOutput()->writeln('<info>Connexion OK</info>');
        } catch (Exception $e) {
            $this->getOutput()->writeln('<error>Connexion fail: ' . $e->getMessage() . '</error>');
            return false;
        }
        return true;
    }
}