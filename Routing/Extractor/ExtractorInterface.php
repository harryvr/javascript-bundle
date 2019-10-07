<?php

/*
 * This file is part of the JavascriptBundle package.
 *
 * © Enzo Innocenzi <enzo@innocenzi.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace SymfonyJavascript\JavascriptBundle\Routing\Extractor;

/**
 * @author Enzo Innocenzi <enzo@innocenzi.dev>
 */
interface ExtractorInterface
{
    /**
     * Returns an array containing the exposed routes.
     */
    public function extract(): array;
}
