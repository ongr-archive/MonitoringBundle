<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Tests\app\fixture\Acme\TestBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;
use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use ONGR\ElasticsearchBundle\Document\DocumentTrait;

/**
 * Product document.
 *
 * @ES\Document
 */
class Product implements DocumentInterface
{
    use DocumentTrait;

    /**
     * @var string
     *
     * @ES\Property(name="title", type="string", search_analyzer="standard")
     */
    public $title;

    /**
     * @var float
     *
     * @ES\Property(name="price", type="float")
     */
    public $price;
}
