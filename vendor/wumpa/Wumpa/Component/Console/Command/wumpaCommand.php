<?php

namespace Wumpa\Component\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\QuestionHelper;
use Wumpa\Component\App\App;

/**
 * @package wumpaCommand
 * @author  Léopold Jacquot
 */
abstract class wumpaCommand extends Command {

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
    protected $project_path;

    /**
     * @return mixed
     */
    protected function getProjectPath() {
        return $this->project_path;
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
     * @return null|Question
     */
    public function getQuestionHelper() {
        return $this->questionHelper;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->questionHelper = $this->getHelper('question');
        $this->input          = $input;
        $this->output         = $output;
    }

    /**
     * @param $input
     * @param $output
     *
     * @return mixed
     */
    protected function askProjectPath() {
        $question = new Question('Enter the path of the project ');

        $question->setValidator(function ($answer) {
            if (empty(trim($answer)) === true) {
                throw new \RuntimeException('Project path cannot be empty');
            }

            return $answer;
        });

        return $this->questionHelper->ask($this->input, $this->output,
            $question);
    }

    protected function checkDatabaseConnexion() {
        try {
            App::getDatabase()->connect();
            $this->getOutput()->writeln('<info>Connexion OK</info>');
        } catch (\Exception $e) {
            $this->getOutput()->writeln('<error>Connexion fail: ' . $e->getMessage() . '</error>');
        }
    }
}