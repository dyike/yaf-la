<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Yaf\Exception;

class GenerateControllerCommand extends Command
{
    private $type = 'Controller';
    private $templatePath = TEMPLATE_PATH . '/controller.php';
    private $defaultDir = APP_PATH . '/application/controllers/';
    private $moduleDir = APP_PATH . '/application/modules/';
    private $outputFileName;
    private $classNameSuffix = 'Controller';
    private $fileNameSuffix = '';
    private $name = 'add:controller';
    private $description = 'Adds new controller.';
    private $help = "This command allows you to add controllers...";
    private $usage = '{Module/Controller} like index@index';

    protected function configure()
    {
        $this->setName($this->name)
            ->setDescription($this->description)
            ->setHelp($this->help)
            ->addArgument('controller', InputArgument::REQUIRED, 'require module name and controller name .')
            ->addUsage($this->usage);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $arg = $input->getArgument('controller');
        $pathinfo = pathinfo($arg);

        if (pathinfo($pathinfo['dirname'])['dirname'] !== '.') {
            throw  new Exception('Controller 参数设置错误,无法解析！' . $arg);
        }
        // 创建 application/controllers 下的控制器
        if ($pathinfo['dirname'] === '.') {
            $moduleName = $this->defaultDir;
            $controllerName = $pathinfo['filename'];
        } else {
            $moduleName = $pathinfo['dirname'];
            $controllerName = $pathinfo['filename'];
        }

        //index模块与其他模块分开处理
        switch ($moduleName) {
            case "$this->defaultDir":
                $this->outputFileName = $this->defaultDir . ucfirst($controllerName) . '.php';
                $template = require_once $this->templatePath;
                $data = sprintf($template, ucfirst($controllerName), ucfirst($controllerName) . $this->classNameSuffix);
                break;
            case 'index':
                $this->outputFileName = $this->defaultDir . ucfirst($controllerName) . $this->fileNameSuffix . '.php';
                $template = require_once $this->templatePath;
                $data = sprintf($template, ucfirst($controllerName) . $this->classNameSuffix,ucfirst($controllerName) . $this->classNameSuffix);
                break;
            default:
                if (!is_dir($this->moduleDir)) {
                    mkdir($this->moduleDir);
                } else if (!is_dir($this->moduleDir . '/' . $moduleName)) {
                    mkdir($this->moduleDir . '/' . $moduleName);
                    mkdir($this->moduleDir . '/' . $moduleName . '/controllers');
                } else if (!is_dir($this->moduleDir . '/' . $moduleName . '/controllers')) {
                    mkdir($this->moduleDir . '/' . $moduleName . '/controllers');
                }
                $this->outputFileName = $this->moduleDir . $moduleName . '//controllers/' . ucfirst($controllerName) . $this->fileNameSuffix . '.php';
                $template = require_once $this->templatePath;
                $data = sprintf($template, ucfirst($controllerName) . $this->classNameSuffix,ucfirst($controllerName) . $this->classNameSuffix);
                break;
        }

        $style = new OutputFormatterStyle('red', 'yellow', ['bold', 'blink']);
        $output->getFormatter()->setStyle('fire', $style);

        $output->writeln(['<info>' .
            ucfirst($arg) . ' ' . $this->type . ' Adding' . '</info>',
            "\n<fire>=============================</fire>\n",
        ]);

        if (is_dir(dirname($this->outputFileName))) {
            if (!is_writable(dirname($this->outputFileName))) {
                throw new Exception('<error>File can\'t writable</error>');
            }

            if (file_exists($this->outputFileName)) {
                $helper = $this->getHelper('question');
                $question = new ConfirmationQuestion('<question>File exists,Continue with this action,overwrite it?(y|n)?</question>', false);

                if (!$helper->ask($input, $output, $question)) {
                    return;
                } else {
                    file_put_contents($this->outputFileName, $data);
                    $output->writeln("<comment>Congratulation!</comment>");
                    $output->writeln('<fg=green;options=bold>Overwrite a ' . ucfirst($arg) . ' ' . $this->type . " successfully</>");
                    return;
                }
            }
            file_put_contents($this->outputFileName, $data);
            $output->writeln("<comment>Congratulation!</comment>");
            $output->writeln('<fg=green;options=bold>Add a ' . ucfirst($arg) . ' ' . $this->type . " successfully</n>");
        } else {
            throw new Exception('<error>Dirctory not exists!' . dirname($this->outputFileName) . '</error>');
        }
    }
}