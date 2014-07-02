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
	 * @var \Visol\Quicknav\Domain\Repository\CategoryRepository
	 * @inject
	 */
	protected $categoryRepository;

	/**
	 * @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
	 * @inject
	 */
	protected $uriBuilder;

	/**
	 * action render
	 *
	 * @return void
	 */
	public function renderAction() {
		$dataUrl = $this->uriBuilder->setTargetPageUid(1)->setTargetPageType(20140702)->setCreateAbsoluteUri(TRUE)->setAbsoluteUriScheme('https')->build();
		$this->view->assign('dataUrl', $dataUrl);
	}

	/**
	 * action getData
	 *
	 * @return string
	 */
	public function getDataAction() {

		$data = array();

		$level1Categories = $this->categoryRepository->findByParent((int)$this->settings['rootCategoryUid']);
		if ($level1Categories->count()) {
			$level1Iterator = 1;
			foreach ($level1Categories as $level1Category) {
				/** @var \Visol\Quicknav\Domain\Model\Category $level1Category */
				$data[$level1Iterator]['label'] = $level1Category->getTitle();
				$data[$level1Iterator]['uid'] = $level1Category->getUid();
				$data[$level1Iterator]['parent'] = $level1Category->getParentcategory()->getUid();
				$data[$level1Iterator]['sorting'] = $level1Category->getSorting();

				$level2Categories = $this->categoryRepository->findByParent($level1Category->getUid());
				if ($level2Categories->count()) {
					$level2Iterator = 1;
					foreach ($level2Categories as $level2Category) {
						/** @var \Visol\Quicknav\Domain\Model\Category $level2Category */
						$data[$level1Iterator]['sub'][$level2Iterator]['label'] = $level2Category->getTitle();
						$data[$level1Iterator]['sub'][$level2Iterator]['uid'] = $level2Category->getUid();
						$data[$level1Iterator]['sub'][$level2Iterator]['parent'] = $level2Category->getParentcategory()->getUid();
						$data[$level1Iterator]['sub'][$level2Iterator]['sorting'] = $level2Category->getSorting();

						$level3Items = $this->quickNavigationItemRepository->findByCategory($level2Category);
						if ($level3Items->count()) {
							$level3ItemIterator = 1;
							foreach ($level3Items as $level3Item) {
								/** @var \Visol\Quicknav\Domain\Model\QuickNavigationItem $level3Item */
								$data[$level1Iterator]['sub'][$level2Iterator]['items']['item-' . $level3ItemIterator]['label'] = $level3Item->getName();
								$data[$level1Iterator]['sub'][$level2Iterator]['items']['item-' . $level3ItemIterator]['uid'] = $level3Item->getUid();
								$data[$level1Iterator]['sub'][$level2Iterator]['items']['item-' . $level3ItemIterator]['link'] = $this->uriBuilder->setCreateAbsoluteUri(TRUE)->setTargetPageUid($level3Item->getShortcut())->build();
								$data[$level1Iterator]['sub'][$level2Iterator]['items']['item-' . $level3ItemIterator]['parent'] = $level2Category->getUid();
								$level3ItemIterator++;
							}
						}
						$level2Iterator++;
					}
				}
				$level2Items = $this->quickNavigationItemRepository->findByCategory($level1Category);
				if ($level2Items->count()) {
					$level2ItemIterator = 1;
					foreach ($level2Items as $level2Item) {
						/** @var \Visol\Quicknav\Domain\Model\QuickNavigationItem $level2Item */
						$data[$level1Iterator]['items']['item-' . $level2ItemIterator]['label'] = $level2Item->getName();
						$data[$level1Iterator]['items']['item-' . $level2ItemIterator]['uid'] = $level2Item->getUid();
						$data[$level1Iterator]['items']['item-' . $level2ItemIterator]['link'] = $this->uriBuilder->setCreateAbsoluteUri(TRUE)->setTargetPageUid($level2Item->getShortcut())->build();
						$data[$level1Iterator]['items']['item-' . $level2ItemIterator]['parent'] = $level1Category->getUid();
						$level2ItemIterator++;
					}
				}
				$level1Iterator++;

			}
		}
		$level1Items = $this->quickNavigationItemRepository->findByCategory((int)$this->settings['rootCategoryUid']);
		if ($level1Items->count()) {
			$level1ItemIterator = 1;
			foreach ($level1Items as $level1Item) {
				/** @var \Visol\Quicknav\Domain\Model\QuickNavigationItem $level1Item */
				$data['item-' . $level1ItemIterator]['label'] = $level1Item->getName();
				$data['item-' . $level1ItemIterator]['uid'] = $level1Item->getUid();
				$data['item-' . $level1ItemIterator]['link'] = $this->uriBuilder->setCreateAbsoluteUri(TRUE)->setTargetPageUid($level1Item->getShortcut())->build();
				$data['item-' . $level1ItemIterator]['parent'] = (int)$this->settings['rootCategoryUid'];
				$level1ItemIterator++;
			}
		}
		return json_encode($data);

	}

}
?>