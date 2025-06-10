<?php

namespace App\Command\Traits;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

trait AskTrait
{
    private InputInterface $input;
    private OutputInterface $output;
    private Helper $helper;

    private function askTraitInit(InputInterface $input, OutputInterface $output): void {
        $this->input = $input;
        $this->output = $output;
        $this->helper = $this->getHelper('question');
    }

    private function confirm($message): mixed
    {
        return $this->helper->ask(
            $this->input,
            $this->output,
            new ConfirmationQuestion($message . "[y/n]: ", false, "/^y/i")
        );
    }

    private function ask($message, $default = false, array $autoComplete = null): mixed
    {
        $question = new Question($message, $default);
        if($autoComplete) {
            $question->setAutocompleterValues($autoComplete);
        }
        return $this->helper->ask(
            $this->input,
            $this->output,
            $question
        );
    }

    private function choice($message, array $choiceList, mixed $default): mixed
    {
        $question = new ChoiceQuestion($message, $choiceList, $default);
        return $this->helper->ask(
            $this->input,
            $this->output,
            $question
        );
    }
}