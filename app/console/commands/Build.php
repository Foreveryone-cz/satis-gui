<?php

namespace Console\Commands;

use Nette\DI\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;



/**
 * @author Martin Bažík <martin@bazik.sk>
 */
class Build extends Command
{

	/** @var Container */
	private $container;



	function __construct(Container $container)
	{
		parent::__construct();
		$this->container = $container;
	}


	protected function configure()
	{
		$this->setName('satis:build')
				->addArgument('packages', InputArgument::OPTIONAL, 'list of packages you want to rebuild', '')
                //->addOption('with-compile', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'compile config and build')
                //->addOption('only-compile', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'only compile config')
				->setDescription('build');
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
        $output->write('compiling satis.json ... ');

        /* @var $manager \App\Model\PackageManager */
        $manager = $this->container->getByType(\App\Model\PackageManager::class);
        $manager->compileConfig();

        $output->writeln('done');

        $output->writeln('building package.json...');
        $packages = explode(' ', $input->getArgument('packages'));

        $builder = $this->container->getByType('App\Model\Builder');
        $builder->build($packages, function ($type, $buffer) use($output) {
            if ($type === Process::ERR) {
                $output->writeln(sprintf('<error>%s</error>', $buffer));
            } else {
                $output->writeln(sprintf('<info>%s</info>', $buffer));
            }
        });

        $output->writeln('built package.json');
	}


}
