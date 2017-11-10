<?php

$plugin->onAjax(
	'get_feedback_by_category',
    'WPMNForm\Plugin\Modules\AjaxHandler@handle'
);