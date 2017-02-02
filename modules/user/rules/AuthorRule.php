<?php

namespace app\modules\user\rules;
use yii\rbac\Rule;


class AuthorRule extends Rule
{
    public $name = 'isAuthor';
    public function execute($user_id, $item, $params)
    {
        return isset($params['post']) ? $params['post']->createdBy == $user_id : false;
    }
}