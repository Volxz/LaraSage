<?php
namespace Microcad\LaraSage;

use ArrayAccess;
use GuzzleHttp\Client as GClient;
use GuzzleHttp\RequestOptions;

class SageCRUD implements ArrayAccess
{
    static protected  $endpoint;
    protected $data;

    public function &__get ($key) {
        return $this->data[$key];
    }

    public function __set($key,$value) {
        $this->data[$key] = $value;
    }

    public function __isset ($key) {
        return isset($this->data[$key]);
    }

    public function __unset($key) {
        unset($this->data[$key]);
    }

    public function offsetGet($offset) {
        return $this->offsetExists($offset) ? $this->data[$offset] : null;
    }

    public function toArray() {
        return $this->data;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->data[$offset]);
        }
    }

    /**
     * @return array
     */
    public function &__invoke()
    {
        return $this->data;
    }
    static function create($data) {
        $client = new GClient();
        $response = $client->post(config("larasage.url" . static::$endpoint), [
            RequestOptions::AUTH => [self::getUsername(),self::getPassword()],
            RequestOptions::JSON => $data
        ]);
        $instance = new self;

        $instance->data = json_decode($response->getBody(), true);
        return $instance;

    }

    public static function find($id) {
        $client = new GClient();
        $uri = config("larasage.url" ) . static::$endpoint . "('" . $id . "')";
        $response = $client->get($uri, [
            RequestOptions::AUTH => [self::getUsername(),self::getPassword()],
            RequestOptions::HTTP_ERRORS => false
        ]);

        if($response->getStatusCode() !== 200) {
            return null;
        }

        $instance = new static;
        $instance->data = json_decode($response->getBody(), true);
        return $instance;
    }

    public function save() {
        $client = new GClient();
        $uri = config("larasage.url" ) . static::$endpoint . "('" . $this->data['CustomerNumber'] . "')";
        $response = $client->put($uri, [
            RequestOptions::AUTH => [self::getUsername(),self::getPassword()],
            RequestOptions::JSON => $this->data
        ]);
        return;
    }

    static function getUsername() {
        $apikey = config("larasage.api_key");
        return substr($apikey, 0, strpos($apikey, ':'));
    }

    static function getPassword() {
        $apikey = config("larasage.api_key");
        return substr($apikey, strpos($apikey, ':') + 1, strlen($apikey));
    }


}