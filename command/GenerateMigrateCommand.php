<?php
namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;

use Yaf\Exception;

class GenerateMigrateCommand extends Command
{
    private $type = 'Migrate';
    private $name = 'migrate';
    private $description = 'Migrate';
    private $outputDir = APP_PATH . '/databases/migrations/';
    private $help = "This command allows you to migrate table...";
    private $usage = 'like create_users_table --create=users';
    private $resolver;
    private $repository;

    protected function configure()
    {
        $this->setName($this->name)
            ->setDescription($this->description)
            ->setHelp($this->help)
            ->addUsage($this->usage);
            //->addOption('table', null, InputOption::VALUE_REQUIRED, 'require table name like "create_user_table".', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handle = opendir($this->outputDir . ".");
        $this->files = [];
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $file = substr($file, 0, -4);
                $this->files[] = $file;
            }
        }
        closedir($handle);
print_r($this->files);
        $fileSystem = new Filesystem();
        $migrate = new Migrator($repository, $resolver, $fileSystem);
        $migrations = $migrate->run($this->outputDir);
        print_r($migrations);exit;

    }
}

