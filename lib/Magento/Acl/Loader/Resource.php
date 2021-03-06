<?php
/**
 * ACL Resource Loader
 *
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Magento_Acl_Loader_Resource implements Magento_Acl_LoaderInterface
{
    /**
     * Acl resource config
     *
     * @var Magento_Acl_Loader_Resource_ConfigReaderInterface
     */
    protected $_configReader;

    /**
     * Resource factory
     *
     * @var Magento_Acl_ResourceFactory
     */
    protected $_resourceFactory;

    /**
     * @param Magento_Acl_Loader_Resource_ConfigReaderInterface $reader
     * @param Magento_Acl_ResourceFactory $resourceFactory
     */
    public function __construct(
        Magento_Acl_Loader_Resource_ConfigReaderInterface $reader,
        Magento_Acl_ResourceFactory $resourceFactory
    ) {
        $this->_configReader = $reader;
        $this->_resourceFactory = $resourceFactory;
    }

    /**
     * Populate ACL with resources from external storage
     *
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl)
    {
        $this->_addResourceTree($acl, $this->_configReader->getAclResources(), null);
    }

    /**
     * Add list of nodes and their children to acl
     *
     * @param Magento_Acl $acl
     * @param array $resources
     * @param Magento_Acl_Resource $parent
     * @throws InvalidArgumentException
     */
    protected function _addResourceTree(Magento_Acl $acl, array $resources, Magento_Acl_Resource $parent = null)
    {
        foreach ($resources as $resourceConfig) {
            if (!isset($resourceConfig['id'])) {
                throw new InvalidArgumentException('Missing ACL resource identifier');
            }
            /** @var $resource Magento_Acl_Resource */
            $resource = $this->_resourceFactory->createResource(array('resourceId' => $resourceConfig['id']));
            $acl->addResource($resource, $parent);
            if (isset($resourceConfig['children'])) {
                $this->_addResourceTree($acl, $resourceConfig['children'], $resource);
            }
        }
    }
}
