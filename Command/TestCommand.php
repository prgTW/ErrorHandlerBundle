<?php

namespace prgTW\ErrorHandlerBundle\Command;

use prgTW\ErrorHandler\ErrorHandler;
use prgTW\ErrorHandler\Metadata\Metadata;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
	/** {@inheritdoc} */
	protected function configure()
	{
		$this->setName('error-handler:test');
		$this->addArgument('category', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Category name(s)');
		$this->addOption('count', null, InputOption::VALUE_REQUIRED, 'How many test errors will be created', 1);
		$this->addOption('type', null, InputOption::VALUE_REQUIRED, 'error|exception', 'error');
		$this->setDescription('Sends a test error to handlers connected to a given categories');
	}

	/** {@inheritdoc} */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$type = $input->getOption('type');
		if (!in_array($type, array('error', 'exception')))
		{
			throw new \LogicException('Type must be either "error" or "exception"');
		}
		$count  = intval($input->getOption('count'));
		$digits = floor(log10($count) + 1);
		/** @var ErrorHandler $errorHandler */
		$errorHandler = $this->getContainer()->get('error_handler');

		$metadata = new Metadata();
		$metadata->addCategories($input->getArgument('category'));

		$output->writeln('<fg=cyan>Creating errors</fg=cyan>');
		for ($i = 1; $i <= $count; ++$i)
		{
			switch ($type)
			{
				case 'error':
					$errorHandler->handleError(E_USER_ERROR, 'TEST ERROR', __FILE__, __LINE__, array(), $metadata);
					break;

				case 'exception':
					$errorHandler->handleException(new \Exception(), $metadata);
					break;
			}
			$output->writeln(sprintf("<comment>[%{$digits}d/%{$digits}d]</comment> <info>OK</info>", $i, $count));
		}

		$output->writeln('<info>DONE</info>');
	}
}

