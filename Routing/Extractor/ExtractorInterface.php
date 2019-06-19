<?php

/*
 * This file is part of the JavascriptBundle package.
 * 
 * © Enzo Innocenzi <enzo.inno@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Hawezo\JavascriptBundle\Routing\Extractor;

/**
 * @author Enzo Innocenzi <enzo.inno@gmail.com>
 */
interface ExtractorInterface
{
    
    /**
     * Returns an array containing the exposed routes.
     */
    public function extract(): array;

}
