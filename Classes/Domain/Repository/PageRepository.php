<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Sjoerd Zonneveld  <code@bitpatroon.nl>
 *  Date: 30-4-2021 00:49
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

namespace BPN\BpnRequestAccess\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class PageRepository extends \TYPO3\CMS\Core\Domain\Repository\PageRepository
{
    /**
     * @param string $handle
     *
     * @return int
     */
    public function getIdsByHandle(string $handle)
    {
        /** @var \BPN\BpnHandle\Domain\Repository\PageRepository $pageRepository */
        $pageRepository = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(\BPN\BpnHandle\Domain\Repository\PageRepository::class);
        $pages = $pageRepository->findByHandle($handle);
        if ($pages){
            return (int)array_key_first($pages);
        }
        return 0;
    }
}
