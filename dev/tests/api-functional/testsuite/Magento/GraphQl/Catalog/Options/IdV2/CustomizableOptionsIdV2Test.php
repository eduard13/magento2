<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQl\Catalog\Options\IdV2;

use Magento\TestFramework\TestCase\GraphQlAbstract;

/**
 * Test for product custom options ID_V2
 */
class CustomizableOptionsIdV2Test extends GraphQlAbstract
{
    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple_with_full_option_set.php
     */
    public function testQueryIdV2ForCustomizableOptions()
    {
        $productSku = 'simple';
        $query = $this->getQuery($productSku);
        $response = $this->graphQlQuery($query);
        $responseProduct = $response['products']['items'][0];
        self::assertNotEmpty($responseProduct['options']);

        foreach ($responseProduct['options'] as $option) {
            if (isset($option['entered_option'])) {
                $enteredOption = $option['entered_option'];
                $idV2 = $this->getIdV2ForEnteredValue($option['option_id']);

                self::assertEquals($idV2, $enteredOption['id_v2']);
            } elseif (isset($option['selected_option'])) {
                $this->assertNotEmpty($option['selected_option']);

                foreach ($option['selected_option'] as $selectedOption) {
                    $idV2 = $this->getIdV2ForSelectedValue($option['option_id'], $selectedOption['option_type_id']);
                    self::assertEquals($idV2, $selectedOption['id_v2']);
                }
            }
        }
    }

    /**
     * Get IdV2 for entered option
     *
     * @param int $optionId
     *
     * @return string
     */
    private function getIdV2ForEnteredValue(int $optionId): string
    {
        return base64_encode('custom-option/' . $optionId);
    }

    /**
     * Get IdV2 for selected option
     *
     * @param int $optionId
     * @param int $optionValueId
     *
     * @return string
     */
    private function getIdV2ForSelectedValue(int $optionId, int $optionValueId): string
    {
        return base64_encode('custom-option/' . $optionId . '/' . $optionValueId);
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

      ... on CustomizableProductInterface {
        options {
          option_id
          title

          ... on CustomizableRadioOption {
            option_id
            selected_option: value {
              option_type_id
              id_v2
            }
          }

          ... on CustomizableDropDownOption {
            option_id
            selected_option: value {
              option_type_id
              id_v2
            }
          }

          ... on CustomizableMultipleOption {
            option_id
            selected_option: value {
              option_type_id
              id_v2
            }
          }

          ... on CustomizableCheckboxOption {
            option_id
            selected_option: value {
              option_type_id
              id_v2
            }
          }

          ... on CustomizableAreaOption {
            option_id
            entered_option: value {
              id_v2
            }
          }

          ... on CustomizableFieldOption {
            option_id
            entered_option: value {
              id_v2
            }
          }

          ... on CustomizableFileOption {
            option_id
            entered_option: value {
              id_v2
            }
          }

          ... on CustomizableDateOption {
            option_id
            entered_option: value {
              id_v2
            }
          }
        }
      }
    }
  }
}
QUERY;
    }
}
