<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ConfigurableProductGraphQl\Model\Resolver\Variant\Attributes;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Format new option id_v2 in base64 encode for super attribute options
 */
class ConfigurableAttributeIdV2 implements ResolverInterface
{
    /**
     * Option type name
     */
    private const OPTION_TYPE = 'configurable';

    /**
     * @var Attribute
     */
    private $eavAttribute;

    /**
     * @param Attribute $eavAttribute
     */
    public function __construct(Attribute $eavAttribute)
    {
        $this->eavAttribute = $eavAttribute;
    }

    /**
     * Create a option id_v2 for super attribute in "<option-type>/<attribute-id>/<value-index>" format
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return string
     *
     * @throws GraphQlInputException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['code']) || empty($value['code'])) {
            throw new GraphQlInputException(__('Wrong format option data: "code" should not be empty.'));
        }

        $attributeId = $this->eavAttribute->getIdByCode(Product::ENTITY, $value['code']);

        if (empty($attributeId)) {
            throw new GraphQlInputException(__('Wrong format option data: "attribute_id" should not be empty.'));
        }

        if (!isset($value['value_index']) || empty($value['value_index'])) {
            throw new GraphQlInputException(__('Wrong format option data: "value_index" should not be empty.'));
        }

        $optionDetails = [
            self::OPTION_TYPE,
            $attributeId,
            $value['value_index']
        ];

        $content = implode('/', $optionDetails);

        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        return base64_encode($content);
    }
}
