<?php

namespace App\Model;

use Nette\Database\Context;
use Nette\Http\Request;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;



/**
 * @author Martin Bažík <martin@bazik.sk>
 */
class PackageManager
{

    const TABLE_NAME = 'packages';

    /** @var string */
    private $params;

	/** @var array */
	private $parameters;

	/** @var Context */
	private $db;

	/** @var Request */
	private $httpRequest;

    /** @var string */
	private $configFile;

    /** @var string */
    private $homepageSuffix;

	public function __construct($params, $parameters, Context $db, Request $httpRequest)
	{
		$this->params = $params;
		$this->parameters = $parameters;
		$this->db = $db;
		$this->httpRequest = $httpRequest;

        if(empty($params['configFile'])) {
            throw new \Exception('Parameter configFile missing.');
        }

        $this->configFile = $params['configFile'];

        if(isset($params['homepageSuffix'])) {
            $this->homepageSuffix = $params['homepageSuffix'];
        }
	}


	public function add($type, $url)
	{
		$this->db->table(self::TABLE_NAME)->insert([
			'type' => $type,
			'url' => $url
		]);
	}


	public function getAll()
	{
		return $this->db->table(self::TABLE_NAME);
	}


	public function delete($id)
	{
		$this->db->table(self::TABLE_NAME)->get($id)->delete();
	}


	public function compileConfig()
	{
		$repositories = $this->getAll();

		$domain = $this->httpRequest->getUrl()->getHostUrl();

        if($this->homepageSuffix) {
            $domain .= '/' . $this->homepageSuffix;
        }

		$config = [
			'homepage' => $domain,
			'repositories' => [
			]
		];

		foreach ($this->parameters as $property => $value) {
			$config[$property] = $value;
		}

		foreach ($repositories as $repository) {
			$config['repositories'][] = [
				'type' => $repository->type,
				'url' => $repository->url
			];
		}

		$json = Json::encode($config, Json::PRETTY);

		FileSystem::write($this->configFile, $json);
	}


}
