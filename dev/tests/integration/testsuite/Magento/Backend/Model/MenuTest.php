<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Model;

/**
 * Test class for \Magento\Backend\Model\Auth.
 *
 * @magentoAppArea adminhtml
 * @magentoCache all disabled
 */
class MenuTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Menu
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        \Magento\TestFramework\Helper\Bootstrap::getInstance()
            ->loadArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Backend\Model\Auth::class);
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\Config\ScopeInterface::class
        )->setCurrentScope(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
    }

    public function testMenuItemManipulation()
    {
        /* @var $menu \Magento\Backend\Model\Menu */
        $menu = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Backend\Model\Menu\Config::class
        )->getMenu();
        /* @var $itemFactory \Magento\Backend\Model\Menu\Item\Factory */
        $itemFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Backend\Model\Menu\Item\Factory::class
        );

        // Add new item in top level
        $menu->add(
            $itemFactory->create(
                [
                    'id' => 'Magento_Backend::system2',
                    'title' => 'Extended System',
                    'module' => 'Magento_Backend',
                    'resource' => 'Magento_Backend::system2',
                ]
            )
        );

        //Add submenu
        $menu->add(
            $itemFactory->create(
                [
                    'id' => 'Magento_Backend::system2_acl',
                    'title' => 'Acl',
                    'module' => 'Magento_Backend',
                    'action' => 'admin/backend/acl/index',
                    'resource' => 'Magento_Backend::system2_acl',
                ]
            ),
            'Magento_Backend::system2'
        );

        // Modify existing menu item
        $menu->get('Magento_Backend::system2')->setTitle('Base system')->setAction('admin/backend/system/base');
        // remove dependency from config

        // Change sort order
        $menu->reorder('Magento_Backend::system', 40);

        // Remove menu item
        $menu->remove('Magento_Backend::catalog_attribute');

        // Move menu item
        $menu->move('Magento_Catalog::catalog_products', 'Magento_Backend::system2');
    }

    public function testSerializeUnserialize()
    {
        /** @var Menu $menu */
        $menu = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Backend\Model\MenuFactory::class
        )->create();
        /* @var \Magento\Backend\Model\Menu\Item\Factory $itemFactory */
        $itemFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Backend\Model\Menu\Item\Factory::class
        );

        // Add new item in top level
        $menu->add(
            $itemFactory->create(
                [
                    'id' => 'Magento_Backend::system3',
                    'title' => 'Extended System',
                    'module' => 'Magento_Backend',
                    'resource' => 'Magento_Backend::system3',
                ]
            )
        );

        //Add submenu
        $menu->add(
            $itemFactory->create(
                [
                    'id' => 'Magento_Backend::system3_acl',
                    'title' => 'Acl',
                    'module' => 'Magento_Backend',
                    'action' => 'admin/backend/acl/index',
                    'resource' => 'Magento_Backend::system3_acl',
                ]
            ),
            'Magento_Backend::system3'
        );
        $serializedString = $menu->serialize();
        /** @var Menu $menu2 */
        $menu2 = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Backend\Model\MenuFactory::class
        )->create();
        $menu2->unserialize($serializedString);
        $this->assertEquals($menu, $menu2);
    }
}
