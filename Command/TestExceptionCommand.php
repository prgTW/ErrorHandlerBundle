<?php

namespace prgTW\ErrorHandlerBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestExceptionCommand extends Command
{
	/** {@inheritdoc} */
	protected function configure()
	{
		$this->setName('error-handler:test-exception');
		$this->setDescription('Creates a test exception that should be caught by error handler');
	}

	/** {@inheritdoc} */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		throw new \Exception('TEST EXCEPTION');
	}
}

