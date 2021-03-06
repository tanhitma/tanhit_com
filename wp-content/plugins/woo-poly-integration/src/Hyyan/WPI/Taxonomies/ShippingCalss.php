<?php

/**
 * This file is part of the hyyan/woo-poly-integration plugin.
 * (c) Hyyan Abo Fakher <tiribthea4hyyan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hyyan\WPI\Taxonomies;

/**
 * ShippingCalss
 *
 * @author Hyyan
 */
class ShippingCalss implements TaxonomiesInterface
{

    /**
     * @{inheritdoc}
     */
    public static function getNames()
    {
        return array('product_shipping_class');
    }

}
