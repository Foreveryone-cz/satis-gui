<?php

namespace App\Presenters;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends SecuredPresenter
{

	public function renderDefault()
	{
		$this->template->webhookPassword = $this->context->parameters['webhook']['password'];
		$this->template->wwwDir = $this->context->parameters['wwwDir'];

        $this->template->output = '';

        $dir = $this->context->parameters['outputDir'];
        $index = $dir . '/index.html';
        if(file_exists($index)) {
            $this->template->output = file_get_contents($index);
        }
	}


}
