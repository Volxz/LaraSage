<?php
namespace Microcad\LaraSage;

use ArrayAccess;
use GuzzleHttp\Client as GClient;
use GuzzleHttp\RequestOptions;

class SageCRUD implements ArrayAccess
{
    static protected $endpoint;
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

        $response = $client->post(config("larasage.url" . self::$endpoint), [
            RequestOptions::AUTH => [self::getUsername(),self::getPassword()],
            RequestOptions::JSON => $data
        ]);

        return $response->getBody();

    }

    public function find($id) {
        $client = new GClient();
        $response = $client->get(config(), [
            GuzzleHttp\RequestOptions::AUTH => [self::getUsername(),self::getPassword()],
            GuzzleHttp\RequestOptions::JSON => $this->data,
        ]);

        return $response->getBody();
    }

    function delete() {
        $client = new GClient();
        $response = $client->delete(config(), [
            GuzzleHttp\RequestOptions::AUTH => [self::getUsername(),self::getPassword()],
            GuzzleHttp\RequestOptions::JSON => $this->data,
        ]);

        return $response->getBody();
    }

    static function getUsername() {
        return substr(config("SAGE_KEY"), 0, strpos(config("SAGE_KEY"), ':'));
    }

    static function getPassword() {
        return substr(config("SAGE_KEY"), strpos(config("SAGE_KEY"), ':') + 1, -1);
    }


}