<?php
namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
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
    private $className = '';
    private $fileNameSuffix = '';
    private $exampleName = 'example';
    private $name = 'add:migration';
    private $description = 'Add new migration.';
    private $help = "This command allows you to create migration record...";
    private $usage = 'like create_users_table --create=users';

    protected function configure()
    {
        $this->setName($this->name)
            ->setDescription($this->description)
            ->setHelp($this->help)
            ->addUsage($this->usage)
            ->addArgument('migration', InputArgument::REQUIRED, 'require table name like "create_user_table".')
            ->addOption('table', null, InputOption::VALUE_REQUIRED, 'require table name like "create_user_table".', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $arg = $input->getArgument('migration');
        $tableName = $input->getOption('table');
        $pathinfo = pathinfo($arg);
        $fileName = $pathinfo['filename'] . '.php';
        // 讲字符串参数转换数组,在拼接成首字母大写的类名
        foreach (explode('_', $arg) as $value) {
            $this->className .= ucfirst($value);
        }
        $created = date("Y_m_d_His");
        $this->outputFileName = $this->outputDir . $created . '_' . $arg . $this->fileNameSuffix . '.php';

        $template = require_once $this->templatePath;
        if ($tableName != 1) {
            $tableName = $tableName;
        } else {
            $tableName = $this->exampleName;
        }
        $data = sprintf($template, $this->className, $tableName, $tableName);
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
            // 读取文件夹下面的文件名
            $handle = opendir($this->outputDir . ".");
            $files = [];
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    // 去除时间戳
                    $file = substr($file, 18);
                    $files[] = $file;
                }
            }
            closedir($handle);

            if (in_array($fileName, $files)) {
                throw new Exception('<error>File exists!!!</error>');
            }
            file_put_contents($this->outputFileName, $data);
            $output->writeln('<comment>Congratulation!</comment>');
            $output->writeln('<fg=green;options=bold>Add a ' . ucfirst($arg) . ' ' . $this->type . ' successfully</>');
        } else {
            throw new Exception('Dirctory not exists!' . $this->outputDir);
        }
    }


}