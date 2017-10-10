<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 10.10.2017
 * Time: 10:33
 */

namespace Console\Commands\Schema;

use Davajlama\SchemaBuilder\Bridge\NetteDatabaseAdapter;
use Davajlama\SchemaBuilder\Driver\MySqlDriver;
use Davajlama\SchemaBuilder\Schema;
use Davajlama\SchemaBuilder\SchemaBuilder;
use Davajlama\SchemaBuilder\SchemaCreator;
use Nette\Database\Connection;
use Nette\DI\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{
    /** @var Container */
    private $container;

    /**
     * Create constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    public function configure()
    {
        $this->setName('schema:create');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $schema = new Schema();

        foreach($this->container->findByType(Schema\Table::class) as $table) {
            $schema->addTable($this->container->getService($table));
        }

        $connection = $this->container->getByType(Connection::class);
        $adapter    = new NetteDatabaseAdapter($connection);
        $driver     = new MySqlDriver($adapter);
        $builder    = new SchemaBuilder($driver);
        $creator    = new SchemaCreator($driver);

        $creator->applyPatches($builder->buildSchemaPatches($schema));
    }

}