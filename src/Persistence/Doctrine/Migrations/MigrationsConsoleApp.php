<?php
namespace Flaubert\Persistence\Doctrine\Migrations;

use Symfony\Component\Console\Application as ConsoleApp;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM;
use Doctrine\ORM\Tools\Console\ConsoleRunner as DoctrineConsoleRunner;
use Doctrine\DBAL\Migrations\Tools\Console\Command as DoctrineCommands;

class MigrationsConsoleApp extends ConsoleApp
{
    public function __construct(EntityManager $em, array $config = [])
    {
        parent::__construct();
        
        $config += [
            'ignoredTables' => null
        ];

        if (!empty($config['ignoredTables'])) {
            $doctrineConfig = $em->getConnection()->getConfiguration();
            $doctrineConfig->setFilterSchemaAssetsExpression('/^(?!(' . implode('|', $config['ignoredTables']) . ')).*$/');      
        }

        $helperSet = new \Symfony\Component\Console\Helper\HelperSet([
            'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
            'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em),
            'dialog' => new \Symfony\Component\Console\Helper\QuestionHelper()
        ]);

        $this->setCatchExceptions(true);
        $this->setHelperSet($helperSet);
        $this->setName('Doctrine Command Line Interface');
        $this->setVersion(ORM\Version::VERSION);;
        
        DoctrineConsoleRunner::addCommands($this);
        
        $this->addCommands([
            new DoctrineCommands\DiffCommand(),
            new DoctrineCommands\ExecuteCommand(),
            new DoctrineCommands\GenerateCommand(),
            new DoctrineCommands\MigrateCommand(),
            new DoctrineCommands\StatusCommand(),
            new DoctrineCommands\VersionCommand()
        ]);
    }
}