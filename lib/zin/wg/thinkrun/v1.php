<?php
declare(strict_types=1);
namespace zin;

require_once dirname(__DIR__) . DS . 'thinkradiolist' . DS . 'v1.php';
class thinkRun extends wg
{
    protected static array $defineProps = array(
        'item: object', // 模型信息
    );

    public static function getPageCSS(): ?string
    {
        return <<<CSS
        .think-run-form, .think-run-form .panel-body, .think-run-form.size-lg .panel-body {padding: 0;}
        CSS;
    }

    protected function buildQuestion():array|wg
    {
        global $lang;

        $item    = $this->prop('item');
        $options = json_decode($item->options);
        $answer  = json_decode($item->answer);

        if($options->questionType == 'radio')
        {
            $fields     = $options->fields ? explode(', ', $options->fields) : array();
            $showFields = array();
            foreach($fields as $field) $showFields[] = array('text' => $field, 'value' => $field);

            if($options->enableOther) $showFields[] = array('text' => $lang->thinkwizard->step->other, 'value' => 'other', 'isOther' => '1', 'showText' => isset($answer->other) ? $answer->other : '');

            return new thinkRadioList
            (
                setClass('mt-6'),
                set::items($showFields),
                set::name('result'),
                set::value(isset($answer->result) ? $answer->result : ''),
            );
        }
        return array();
    }

    protected function build(): array
    {
        $item = $this->prop('item');

        return array(
            thinktransitiondetail
            (
                set::item($item),
                formPanel
                (
                    setClass('think-run-form'),
                    set::actions(array()),
                    $item->type == 'question' ? $this->buildQuestion() : null
                )
            ),
            $this->children()
        );
    }
}
