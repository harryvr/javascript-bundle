<?php

/*
 * This file is part of the JavascriptBundle package.
 *
 * © Enzo Innocenzi <enzo@innocenzi.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Hawezo\JavascriptBundle\Translation\Extractor;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Enzo Innocenzi <enzo@innocenzi.dev>
 */
class TranslationExtractor implements ExtractorInterface
{
    protected $translator;
    protected $locales;
    protected $domains;

    public function __construct(TranslatorInterface $translator, $locales = null, $domains = null)
    {
        $this->translator = $translator;
        $this->locales    = $locales;
        $this->domains    = $domains;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($domains = null, $locales = null): array
    {
        if (!is_array($locales)) {
            $locales = array_filter([$locales]);
        }

        if (empty($locales)) {
            $locales = array_unique(
                    array_merge(
                        [$this->translator->getLocale()],
                        $this->translator->getFallbackLocales()
                    )
                );
        }

        if (!empty($this->locales)) {
            $locales = array_intersect_key($this->locales, $locales);
        }

        if (!is_array($domains)) {
            $domains = array_filter([$domains]);
        }

        $translations = [];

        foreach ($locales as $_locale) {
            if (empty($_locale)) {
                continue;
            }

            $catalogue = $this->translator->getCatalogue($_locale);
            $_domains  = empty($domains) ? $catalogue->getDomains() : array_intersect_key($catalogue->getDomains(), $domains);

            if (!empty($this->domains)) {
                $_domains = array_intersect_key($this->domains, $_domains);
            }

            foreach ($_domains as $_domain) {
                $translations[$_locale][$_domain] = $catalogue->all($_domain);
            }
        }

        return $translations;
    }
}
