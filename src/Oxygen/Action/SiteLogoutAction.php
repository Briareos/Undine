<?php

namespace Undine\Oxygen\Action;

class SiteLogoutAction extends AbstractAction
{
    /**
     * @var string
     */
    private $userUid;

    /**
     * @param string $userUid
     */
    public function __construct($userUid)
    {
        $this->userUid = $userUid;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site.logout';
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return [
            'userUid'=>$this->userUid,
        ];
    }
}
