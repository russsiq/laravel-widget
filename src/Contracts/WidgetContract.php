<?php

namespace Russsiq\Widget\Contracts;

use Illuminate\Contracts\Container\Container;
use Russsiq\Widget\Contracts\ParameterBagContract;

interface WidgetContract
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array;

    /**
     * Set the ParameterBag instance.
     *
     * @param  ParameterBagContract  $parameters
     * @return self
     */
    public function setParameterBag(ParameterBagContract $parameters): self;

    /**
     * Set the Container implementation.
     *
     * @param  Container  $container
     * @return self
     */
    public function setContainer(Container $container): self;

    /**
     * Get the attributes and values that were validated.
     *
     * @return array
     */
    public function validated(): array;

    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validateResolved(): void;

    /**
     * Get data to be validated from the widget parameters.
     *
     * @return array
     */
    public function validationData(): array;
}
