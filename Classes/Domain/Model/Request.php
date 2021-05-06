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

namespace BPN\BpnRequestAccess\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Request extends AbstractEntity
{
    const RESULT_UNVOTED = 0;
    const RESULT_ALLOWED = 1;
    const RESULT_DENIED = 2;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $verificationCode;

    /**
     * @var \DateTime
     */
    protected $start;

    /**
     * @var string
     */
    protected $duration;

    /**
     * @var FrontendUser
     */
    protected $userRequestTarget;

    /**
     * @var FrontendUser
     */
    protected $userRequestSource;

    /**
     * @var FrontendUserGroup
     */
    protected $usergroup;

    /**
     * @var int
     */
    protected $requestResult;

    /**
     * Gets the requestResult property.
     *
     * @return int
     */
    public function getRequestResult()
    {
        return $this->requestResult;
    }

    /**
     * Sets the requestResult property.
     *
     * @param int $requestResult
     */
    public function setRequestResult($requestResult)
    {
        $this->requestResult = (int)$requestResult;

        return $this;
    }

    /**
     * Gets the title property.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title property.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title ?? '';

        return $this;
    }

    /**
     * Gets the verificationCode property.
     *
     * @return string
     */
    public function getVerificationCode()
    {
        return $this->verificationCode;
    }

    /**
     * Sets the verificationCode property.
     *
     * @param string $verificationCode
     *
     * @return $this
     */
    public function setVerificationCode($verificationCode)
    {
        $this->verificationCode = $verificationCode ?? '';

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
     *
     * @return $this
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Gets the duration property.
     *
     * @return string
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Sets the duration property.
     *
     * @return $this
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Gets the target user property.
     *
     * @return FrontendUser
     */
    public function getUserRequestTarget()
    {
        return $this->userRequestTarget;
    }

    /**
     * Sets the target user property.
     *
     * @return $this
     */
    public function setUserRequestTarget($userRequestTarget)
    {
        $this->userRequestTarget = $userRequestTarget;

        return $this;
    }

    /**
     * Gets the userRequestSource property.
     *
     * @return FrontendUser
     */
    public function getUserRequestSource()
    {
        return $this->userRequestSource;
    }

    /**
     * Sets the userRequestSource property.
     *
     * @param FrontendUser $userRequestSource
     *
     * @return $this
     */
    public function setUserRequestSource($userRequestSource)
    {
        $this->userRequestSource = $userRequestSource;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEnd()
    {
        if ($this->start && $this->duration) {
            $endTime = clone $this->start;
            $endTime->add(\DateInterval::createFromDateString($this->duration));

            return $endTime;
        }

        return null;
    }

    public function getUsergroup() : ?FrontendUserGroup
    {
        return $this->usergroup ?? null;
    }

    public function setUsergroup(FrontendUserGroup $usergroup) : Request
    {
        $this->usergroup = $usergroup;

        return $this;
    }
}
