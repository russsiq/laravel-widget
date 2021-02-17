<?php

namespace Russsiq\Widget\Contracts;

interface ParameterBagContract
{
    /**
     * Returns the parameters.
     *
     * @return array
     */
    public function all();

    /**
     * Returns the parameter keys.
     *
     * @return array
     */
    public function keys();

    /**
     * Replaces the current parameters by a new set.
     *
     * @param  array  $parameters
     * @return void
     */
    public function replace(array $parameters = []);

    /**
     * Adds parameters.
     *
     * @return void
     */
    public function add(array $parameters = []);

    /**
     * Returns a parameter by name.
     *
     * @param  string  $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Sets a parameter by name.
     *
     * @param  string  $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function set(string $key, $value);

    /**
     * Returns true if the parameter is defined.
     *
     * @param  string  $key
     * @return bool
     */
    public function has(string $key);

    /**
     * Removes a parameter.
     *
     * @param  string  $key
     * @return void
     */
    public function remove(string $key);
}
