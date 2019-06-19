<?php

/*
 * This file is part of the JavascriptBundle package.
 * 
 * © Enzo Innocenzi <enzo.inno@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Hawezo\JavascriptBundle\Translation\Extractor;

/**
 * @author Enzo Innocenzi <enzo.inno@gmail.com>
 */
interface ExtractorInterface
{
    
    /**
     * Returns an array containing the translation messages.
     * 
     * @param array|string|null $domains
     * @param array|string|null $locales
     * 
     * @return array translation[$locale][$domain][$messageKey] = message
     */
    public function extract($domains = null, $locales = null): array;

}
