<?php

namespace app\logics\business;

use app\logics\BaseLogic;
use app\models\Tinymce;
use yii\db\StaleObjectException;

class OtherLogic extends BaseLogic
{
    /**
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function saveTinymce($content)
    {
        $tinymce = Tinymce::findOne(['id' => 1]);
        $tinymce->content = $content;
        $tinymce->update();
    }
}