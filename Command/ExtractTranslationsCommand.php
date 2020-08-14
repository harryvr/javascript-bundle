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

use SymfonyJavascript\JavascriptBundle\Translation\Extractor\ExtractorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Enzo Innocenzi <enzo@innocenzi.dev>
 */
class ExtractTranslationsCommand extends Command
{
    const ARG_PRETTIFY         = 'pretty';
    const ARG_FORMAT           = 'format';
    const ARG_EXTRACT_OVERRIDE = 'path';

    const FORMAT_JS   = 'js';
    const FORMAT_JSON = 'json';

    protected static $defaultName = 'javascript:extract:translations';

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
            ->setDescription('Exports the Javascript translation file')
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
        $locales     = $container->getParameter('javascript.translation.locales');
        $domains     = $container->getParameter('javascript.translation.domains');
        $extractPath = $input->getArgument(self::ARG_EXTRACT_OVERRIDE)
            ?? $container->getParameter('javascript.translation.extract_path')
            ?? 'public/build/messages';
        $extractPath = $this->replaceExtension($extractPath, $format);

        $translations = $this->extractor->extract($domains, $locales);
        switch ($format) {
            case self::FORMAT_JS:
                $content = $this->getJavascript($translations, $prettify);

                break;

            case self::FORMAT_JSON:
                $content = $this->getJson($translations, $prettify);

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

        $io->success(sprintf('Translations messages have been written to %s.', $extractPath));

        return Command::SUCCESS;
    }

    protected function replaceExtension(string $path, string $format)
    {
        return preg_replace('/(\.js)|(\.json)/', null, $path) . '.' . $format;
    }

    protected function getJson(array $translations, bool $prettify = false)
    {
        return json_encode($translations, $prettify ? JSON_PRETTY_PRINT : 0);
    }

    protected function getJavascript(array $translations, bool $prettify = false)
    {
        return sprintf(
            'export default %s;',
            $this->getJson($translations, $prettify)
        );
    }
}
