<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Sjoerd Zonneveld  <code@bitpatroon.nl>
 *  Date: 6-5-2021 21:38
 *
 *  All rights reserved
 *
 *  This script is part of a Bitpatroon project. The project is
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

namespace BPN\BpnRequestAccess\Domain\Form;

class AccessForm
{
    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $user;

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup
     */
    protected $userGroup;

    /**
     * @var \DateTime
     */
    protected $start;

    /**
     * @var string
     */
    protected $permittedDuration;

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup $userGroup
     *
     * @return $this
     */
    public function setUserGroup($userGroup)
    {
        $this->userGroup = $userGroup;
        return $this;
    }

    /**
     * Gets the permittedDuration property
     * @return string
     */
    public function getPermittedDuration()
    {
        return $this->permittedDuration;
    }

    /**
     * Sets the permittedDuration property
     * @param string $permittedDuration
     * @return $this instance for chaining
     */
    public function setPermittedDuration($permittedDuration)
    {
        $this->permittedDuration = $permittedDuration;
        return $this;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup
     */
    public function getUserGroup()
    {
        return $this->userGroup;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }
}
