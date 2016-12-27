<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Yaf\Exception;

class GeneratePluginCommand extends Command
{
    private $type = 'Plugin';
    private $templatePath = TEMPLATE_PATH . '/plugin.php';
    private $outputDir = APP_PATH . '/application/plugins/';
    private $outputFileName;
    private $classNameSuffix = 'Plugin';
    private $fileNameSuffix = '';
    private $name = 'add:plugin';
    private $description = 'Adds new plugin.';
    private $help = "This command allows you to create plugins...";
    private $usage = 'Sample';

    protected function configure()
    {
        //记得修改argument
        $this->setName($this->name)
            ->setDescription($this->description)
            ->setHelp($this->help)
            ->addArgument('plugin', InputArgument::REQUIRED, 'The name of the model.')
            ->addUsage($this->usage);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $arg = $input->getArgument('plugin');
        $this->outputFileName = $this->outputDir . ucfirst($arg) . $this->fileNameSuffix . '.php';
        $template = require_once $this->templatePath;
        $data = sprintf($template, ucfirst($arg).$this->classNameSuffix, ucfirst($arg).$this->classNameSuffix);

        $style = new OutputFormatterStyle('red', 'yellow', ['bold', 'blink']);
        $output->getFormatter()->setStyle('fire', $style);

        $output->writeln(['<info>' .
            ucfirst($arg) . ' ' . $this->type . ' Adding' . '</info>',
            "\n<fire>=============================</fire>\n",
        ]);

        if (file_exists($this->outputDir)) {
            if (!is_writable($this->outputDir)) {
                throw new Exception('<error>File can\'t writable</error>');
            }

            if (file_exists($this->outputFileName)) {
                $helper = $this->getHelper('question');
                $question = new ConfirmationQuestion('<question>File exists,Continue with this action,overwrite it?(y|n)?</question>', false);

                if (!$helper->ask($input, $output, $question)) {
                    return;
                } else {
                    file_put_contents($this->outputFileName, $data);
                    $output->writeln('<comment>Congratulation!</comment>');
                    $output->writeln('<fg=green;options=bold>Overwrite a ' . ucfirst($arg) . ' ' . $this->type . ' successfully</>');
                    return;
                }
            }
            file_put_contents($this->outputFileName, $data);
            $output->writeln('<comment>Congratulation!</comment>');
            $output->writeln('<fg=green;options=bold>Add a ' . ucfirst($arg) . ' ' . $this->type . ' successfully</>');
        } else {
            throw new Exception('<error>Dirctory not exists!</error>' . $this->outputDir);
        }
    }
}