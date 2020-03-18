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



namespace Mirasvit\Search\Model\ScoreRule;

use Magento\Framework\Model\AbstractModel;

class DataObject extends AbstractModel
{
    public function __construct()
    {
    }

    /**
     * @param int $modelId
     * @param null $field
     * @return $this|AbstractModel
     */
    public function load($modelId, $field = null)
    {
        return $this;
    }
}
