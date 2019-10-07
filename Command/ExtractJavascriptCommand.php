<?php

/*
 * This file is part of the JavascriptBundle package.
 *
 * © Enzo Innocenzi <enzo@innocenzi.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace SymfonyJavascript\JavascriptBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Enzo Innocenzi <enzo@innocenzi.dev>
 */
class ExtractJavascriptCommand extends Command
{
    const ARG_PRETTIFY = 'pretty';
    const ARG_FORMAT   = 'format';

    const FORMAT_JS   = 'js';
    const FORMAT_JSON = 'json';

    protected static $defaultName = 'javascript:extract:all';

    protected function configure()
    {
        $this
            ->setDescription('Exports both the routing file and the translation one.')
            ->addOption(self::ARG_PRETTIFY, 'p', InputOption::VALUE_NONE, 'Prettifies the exported content.')
            ->addArgument(self::ARG_FORMAT, null, 'Export format', self::FORMAT_JSON)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $arguments = [
            'format'   => $input->getArgument(self::ARG_FORMAT),
            '--pretty' => $input->getOption(self::ARG_PRETTIFY),
        ];

        $this->command($output, 'javascript:extract:routes', $arguments);
        $this->command($output, 'javascript:extract:translations', $arguments);
    }

    protected function command(OutputInterface $output, string $commandName, array $args = [])
    {
        $command = $this->getApplication()->find($commandName);
        $args    = array_merge($args, ['command' => $commandName]);

        return $command->run(new ArrayInput($args), $output);
    }
}
