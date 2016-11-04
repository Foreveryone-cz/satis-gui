<?php

namespace App\Presenters;

/**
 * Description of FilesPresenter
 *
 * @author David Bittner <david.bittner@seznam.cz>
 */
class FilesPresenter extends BasePresenter
{

    public function actionDownload($key, $file)
    {
        if($key === $this->context->parameters['secretKey']) {
            $outputDir  = realpath($this->context->parameters['outputDir']);
            $file       = realpath($outputDir . '/' . $file);

            if($file && strpos($file, $outputDir) === 0) {
                echo file_get_contents($file);
                $this->terminate();
            }
        }

        $httpResponse = $this->context->getByType('Nette\Http\Response');
        $httpResponse->setCode(\Nette\Http\Response::S403_FORBIDDEN);
        $this->terminate();
    }
    
}
