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

use SymfonyJavascript\JavascriptBundle\Routing\Extractor\ExtractorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Enzo Innocenzi <enzo@innocenzi.dev>
 */
class ExtractRoutesCommand extends Command
{
    const ARG_PRETTIFY         = 'pretty';
    const ARG_FORMAT           = 'format';
    const ARG_EXTRACT_OVERRIDE = 'path';

    const FORMAT_JS   = 'js';
    const FORMAT_JSON = 'json';

    protected static $defaultName = 'javascript:extract:routes';

    /**
     * @var ExtractorInterface
     */
    private $extractor;

    public function __construct(ExtractorInterface $extractor)
    {
        parent::__construct();

        $this->extractor = $extractor;
    }

    protected function configure()
    {
        $this
            ->setDescription('Exports the Javascript routes file')
            ->addOption(self::ARG_PRETTIFY, 'p', InputOption::VALUE_NONE, 'Prettifies the exported content.')
            ->addArgument(self::ARG_FORMAT, null, 'Export format', self::FORMAT_JSON)
            ->addArgument(self::ARG_EXTRACT_OVERRIDE, null, 'Overrides the export location')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $container   = $this->getApplication()->getKernel()->getContainer();
        $prettify    = $input->getOption(self::ARG_PRETTIFY);
        $format      = $input->getArgument(self::ARG_FORMAT);
        $root        = $container->getParameter('kernel.project_dir');
        $extractPath = $input->getArgument(self::ARG_EXTRACT_OVERRIDE)
            ?? $container->getParameter('javascript.routing.extract_path')
            ?? 'public/build/routes';
        $extractPath = $this->replaceExtension($extractPath, $format);

        $routes = $this->extractor->extract();
        switch ($format) {
            case self::FORMAT_JS:
                $content = $this->getJavascript($routes, $prettify);

                break;

            case self::FORMAT_JSON:
                $content = $this->getJson($routes, $prettify);

                break;

            default:
                $io->error(sprintf('Invalid format %s.', $format));

                return Command::FAILURE;
        }

        $filesystem = new Filesystem();
        $filesystem->dumpFile(
            $root . DIRECTORY_SEPARATOR . $extractPath,
            $content
        );

        $io->success(sprintf('Route file have been written to %s.', $extractPath));

        return Command::SUCCESS;
    }

    protected function replaceExtension(string $path, string $format)
    {
        return preg_replace('/(\.js)|(\.json)/', null, $path) . '.' . $format;
    }

    protected function getJson(array $routes, bool $prettify = false)
    {
        return json_encode($routes, $prettify ? JSON_PRETTY_PRINT : 0);
    }

    protected function getJavascript(array $routes, bool $prettify = false)
    {
        return sprintf(
            'export default %s;',
            $this->getJson($routes, $prettify)
        );
    }
}
