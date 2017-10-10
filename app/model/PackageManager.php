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
    private $url;

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

        if(isset($params['url'])) {
            $this->url = $params['url'];
        }
	}


	public function add($type, $url, $group)
	{
		$this->db->table(self::TABLE_NAME)->insert([
			'type' => $type,
			'url' => $url,
            'group' => $group,
		]);
	}


	public function getAll()
	{
		return $this->db->table(self::TABLE_NAME)->order('group ASC');
	}

	public function findByUrl($url)
    {
        return $this->db->table(self::TABLE_NAME)->where(['url' => $url])->fetch();
    }

    public function update($id, Array $data)
    {
        return $this->db->table(self::TABLE_NAME)->where(['id' => $id])->update($data);
    }

	public function delete($id)
	{
		$this->db->table(self::TABLE_NAME)->get($id)->delete();
	}


	public function compileConfig()
	{
		$repositories = $this->getAll();

		$config = [
			'homepage' => $this->url,
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
