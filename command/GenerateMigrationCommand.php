<?php
namespace Command;

use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Yaf\Exception;


class GenerateMigrationCommand extends Command
{
    private $type = 'Migration';
    private $templatePath = TEMPLATE_PATH . '/migration.php';
    private $outputDir = APP_PATH . '/databases/migrations/';
    private $outputFileName;
    private $fileNameSuffix = '';
    private $name = 'add:migration';
    private $description = 'Create new table.';
    private $help = "This command allows you to create table...";
    private $usage = 'like create_users_table --create=users';

    protected function configure()
    {
        $this->setName($this->name)
            ->setDescription($this->description)
            ->setHelp($this->help)
            ->addArgument('migration', InputArgument::REQUIRED, 'require table name.')
            ->addUsage($this->usage);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $arg = $input->getArgument('migration');
        $this->outputFileName = $this->outputDir . ucfirst($arg) . $this->fileNameSuffix . '.php';
        $template = require_once $this->templatePath;

        $data = sprintf($template, ucfirst($arg), ucfirst($arg).$this->classNameSuffix);

        $output->writeln([
            ucfirst($arg) . ' ' . $this->type . ' Adding',
            '============',
        ]);

        if (file_exists($this->outputDir)) {
            if (!is_writable($this->outputDir)) {
                throw new Exception('File can\'t writable');
            }

            if (file_exists($this->outputFileName)) {
                throw new Exception('File exists!!!');
            }
            file_put_contents($this->outputFileName, $data);
            $output->writeln('Congratulation!');
            $output->writeln('Create a ' . ucfirst($arg) . ' ' . $this->type . ' successfully');
        } else {
            throw new Exception('Dirctory not exists!' . $this->outputDir);
        }
    }


}