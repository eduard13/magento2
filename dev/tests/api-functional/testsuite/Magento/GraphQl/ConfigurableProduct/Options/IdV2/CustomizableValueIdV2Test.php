<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQl\DownloadableProduct\Options\IdV2;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;

/**
 * Test for downloadable product links ID_V2
 */
class CustomizableValueIdV2Test extends GraphQlAbstract
{
    /**
     * @var Attribute
     */
    private $eavAttribute;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->eavAttribute = $objectManager->get(Attribute::class);
    }

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/configurable_product_with_one_simple.php
     */
    public function testQueryIdV2ForConfigurableSuperAttributes()
    {
        $productSku = 'configurable';
        $query = $this->getQuery($productSku);
        $response = $this->graphQlQuery($query);
        $responseProduct = $response['products']['items'][0];
        self::assertNotEmpty($responseProduct['variants']);

        foreach ($responseProduct['variants'] as $variant) {
            self::assertNotEmpty($variant['attributes']);

            foreach ($variant['attributes'] as $attribute) {
                $attributeId = (int) $this->eavAttribute->getIdByCode(Product::ENTITY, $attribute['code']);
                $idV2 = $this->getIdV2ByOptionIds($attributeId, $attribute['value_index']);
                self::assertEquals($idV2, $attribute['id_v2']);
            }
        }
    }

    /**
     * Get IdV2
     *
     * @param int $optionId
     * @param int $optionValueId
     *
     * @return string
     */
    private function getIdV2ByOptionIds(int $optionId, int $optionValueId): string
    {
        return base64_encode('configurable/' . $optionId . '/' . $optionValueId);
    }

    /**
     * Get query
     *
     * @param string $sku
     *
     * @return string
     */
    private function getQuery(string $sku): string
    {
        return <<<QUERY
query {
  products(filter: { sku: { eq: "$sku" } }) {
    items {
      ... on ConfigurableProduct {
        variants {
          attributes {
            id_v2
            code
            value_index
          }
        }
      }
    }
  }
}
QUERY;
    }
}
