<?php

namespace prgTW\ErrorHandlerBundle\Command;

use prgTW\ErrorHandlerBundle\ErrorHandler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
	public $count;

	/** {@inheritdoc} */
	protected function configure()
	{
		$this->setName('error-handler:test');
		$this->addArgument('project', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Project name(s)');
		$this->addOption('count', null, InputOption::VALUE_REQUIRED, 'How many test errors will be created', 1);
		$this->setDescription('Sends a test error to handlers connected to a given project(s)');
	}

	/** {@inheritdoc} */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$count    = $input->getOption('count');
		$digits   = floor(log10($count) + 1);
		$projects = $input->getArgument('project');

		/** @var ErrorHandler $errorHandler */
		$errorHandler = $this->getContainer()->get('error_handler');

		$output->writeln('<fg=cyan>Creating errors:</fg=cyan>');
		foreach ($projects as $project)
		{
			$output->writeln(sprintf('- <comment>%s</comment>', $project));
			for ($i = 1; $i <= $count; ++$i)
			{
				$errorHandler->handleError($project, E_USER_ERROR, 'TEST ERROR', __FILE__, __LINE__);
				$output->writeln(sprintf("  - <comment>[%{$digits}d/%{$digits}d]</comment> %s", $i, $count, !empty($eventId) ? sprintf('<info>eventId: %s</info>', $eventId) : '<error>eventId: empty</error>'));
			}
		}

		$output->writeln('<info>DONE</info>');
	}
}

