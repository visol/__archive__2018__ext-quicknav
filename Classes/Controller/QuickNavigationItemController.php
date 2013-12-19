<?php
namespace Visol\Quicknav\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Lorenz Ulrich <lorenz.ulrich@visol.ch>, visol digitale Dienstleistungen GmbH
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\DebugUtility;

/**
 *
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class QuickNavigationItemController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * quickNavigationItemRepository
	 *
	 * @var \Visol\Quicknav\Domain\Repository\QuickNavigationItemRepository
	 * @inject
	 */
	protected $quickNavigationItemRepository;

	/**
	 * categoryRepository
	 *
	 * @var \Visol\Quicknav\Domain\Repository\QuickNavigationCategoryRepository
	 * @inject
	 */
	protected $categoryRepository;

	/**
	 * action render
	 *
	 * @return void
	 */
	public function renderAction() {
		/** @var \Visol\Quicknav\Domain\Model\QuickNavigationItem $initialItem */
		$initialItem = $this->quickNavigationItemRepository->findByUid($this->settings['initialItem']);

		//$level2Placeholder = $initialItem->getCategory()->getTitle();
		$level1Placeholder = $initialItem->getCategory()->getParent()->getTitle();
		$this->view->assignMultiple(array(
			'level1Placeholder' => $level1Placeholder,
		));
	}

	/**
	 * action getData
	 *
	 * @return string
	 */
	public function getDataAction() {

		$quickNavigationItems = $this->quickNavigationItemRepository->findAll();
		$quickNavigationData = array();
		foreach ($quickNavigationItems as $quickNavigationItem) {
			/** @var \Visol\Quicknav\Domain\Model\QuickNavigationItem $item */
			$item = $quickNavigationItem;
			$itemUid = $item->getUid();
			if ($item->getCategory()) {
				$categoryUid = $item->getCategory()->getUid();
				if ($item->getCategory()->getParent()) {
					// third level
					$parentCategoryUid = $item->getCategory()->getParent()->getUid();
					$quickNavigationData[$parentCategoryUid]['name'] = $item->getCategory()->getParent()->getTitle();
					$quickNavigationData[$parentCategoryUid]['uid'] = $item->getCategory()->getParent()->getUid();
					$quickNavigationData[$parentCategoryUid]['children'][$categoryUid]['name'] = $item->getCategory()->getTitle();
					$quickNavigationData[$parentCategoryUid]['children'][$categoryUid]['uid'] = $item->getCategory()->getUid();
					$quickNavigationData[$parentCategoryUid]['children'][$categoryUid]['parentUid'] = $parentCategoryUid;
					$quickNavigationData[$parentCategoryUid]['children'][$categoryUid]['children'][$itemUid]['name'] = $item->getName();
					$quickNavigationData[$parentCategoryUid]['children'][$categoryUid]['children'][$itemUid]['uid'] = $itemUid;
					$quickNavigationData[$parentCategoryUid]['children'][$categoryUid]['children'][$itemUid]['parentUid'] = $categoryUid;
					if ($item->getShortcut() !== '') {
						$quickNavigationData[$parentCategoryUid]['children'][$categoryUid]['children'][$itemUid]['targetUrl'] = $this->uriBuilder->setCreateAbsoluteUri(TRUE)->setTargetPageUid($item->getShortcut())->build();
					}
				} else {
					// second level
					$quickNavigationData[$categoryUid]['name'] = $item->getCategory()->getTitle();
					$quickNavigationData[$categoryUid]['uid'] = $item->getCategory()->getUid();
					$quickNavigationData[$categoryUid]['children'][$itemUid]['name'] = $item->getName();
					$quickNavigationData[$categoryUid]['children'][$itemUid]['uid'] = $itemUid;
					$quickNavigationData[$categoryUid]['children'][$itemUid]['parentUid'] = $categoryUid;
					if ($item->getShortcut() !== '') {
						$quickNavigationData[$categoryUid]['children'][$itemUid]['targetUrl'] = $this->uriBuilder->setCreateAbsoluteUri(TRUE)->setTargetPageUid($item->getShortcut())->build();
					}
				}
			} else {
				// first level
				$quickNavigationData[$itemUid]['name'] = $item->getName();
				$quickNavigationData[$itemUid]['uid'] = $itemUid;
				if ($item->getShortcut() !== '') {
					$quickNavigationData[$itemUid]['targetUrl'] = $this->uriBuilder->setCreateAbsoluteUri(TRUE)->setTargetPageUid($item->getShortcut())->build();
				}
			}
		}

		return json_encode($quickNavigationData);

	}

}
?>