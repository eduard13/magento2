<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQl\DownloadableProduct\Options\IdV2;

use Magento\TestFramework\TestCase\GraphQlAbstract;

/**
 * Test for downloadable product links ID_V2
 */
class DownloadableLinksValueIdV2Test extends GraphQlAbstract
{
    /**
     * @magentoApiDataFixture Magento/Downloadable/_files/downloadable_product_with_files_and_sample_url.php
     */
    public function testQueryIdV2ForDownloadableLinks()
    {
        $productSku = 'downloadable-product';
        $query = $this->getQuery($productSku);
        $response = $this->graphQlQuery($query);
        $responseProduct = $response['products']['items'][0];

        self::assertNotEmpty($responseProduct['downloadable_product_links']);

        foreach ($responseProduct['downloadable_product_links'] as $productLink) {
            $idV2 = $this->getIdV2ByLinkId((int) $productLink['id']);
            self::assertEquals($idV2, $productLink['id_v2']);
        }
    }

    /**
     * Get IdV2 by link id
     *
     * @param int $linkId
     *
     * @return string
     */
    private function getIdV2ByLinkId(int $linkId): string
    {
        return base64_encode('downloadable/' . $linkId);
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
      sku

      ... on DownloadableProduct {
        downloadable_product_links {
          id
          id_v2
        }
      }
    }
  }
}
QUERY;
    }
}
