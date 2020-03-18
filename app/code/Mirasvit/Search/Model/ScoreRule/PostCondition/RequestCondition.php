<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search
 * @version   1.0.141
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Search\Model\ScoreRule\PostCondition;

use Magento\Rule\Model\Condition\AbstractCondition;

class RequestCondition extends AbstractCondition
{
    /**
     * @param null $attribute
     * @return array|mixed
     */
    public function getAttributeOption($attribute = null)
    {
        $attributes = [
            'query' => 'Search Query',
        ];

        return $attribute ? $attributes[$attribute] : $attributes;
    }
}
