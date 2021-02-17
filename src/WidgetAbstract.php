<?php

namespace Russsiq\Widget;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\Component;
use Russsiq\Widget\Contracts\ParameterBagContract;
use Russsiq\Widget\Contracts\WidgetContract;
use Russsiq\Widget\Rules\MustHaveViewRule;
use Russsiq\Widget\Support\Parameters;

abstract class WidgetAbstract extends Component implements WidgetContract
{
    /** @var Parameters */
    public $parameters;

    /** @var Container */
    protected $container;

    /** @var Validator */
    protected $validator;

    /** @var array */
    protected $validationData = [];

    /** @var string */
    protected $template;

    /**
     * Create a new component instance.
     */
    public function __construct(array $parameters = [])
    {
        $this->validationData = $parameters;
    }

    /**
     * Set the ParameterBag instance.
     *
     * @param  ParameterBagContract  $parameters
     * @return $this
     */
    public function setParameterBag(ParameterBagContract $parameters): self
    {
        $this->ignoreWidgetMethod(__METHOD__);

        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Set the container implementation.
     *
     * @param  Container  $container
     * @return $this
     */
    public function setContainer(Container $container): self
    {
        $this->ignoreWidgetMethod(__METHOD__);

        $this->container = $container;

        return $this;
    }

    /**
     * Get the attributes and values that were validated.
     *
     * @return array
     */
    public function validated(): array
    {
        $this->ignoreWidgetMethod(__METHOD__);

        if ($this->validator->fails()) {
            return [
                'errors' => $this->validator->errors(),
                'is_active' => false,
                'template' => 'laravel-widget::errors',
                'title' => basename(static::class),
            ];
        }

        return $this->validator->validated();
    }

    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validateResolved(): void
    {
        $this->ignoreWidgetMethod(__METHOD__);

        $this->createValidator();

        $this->setParameters(
            $this->validated()
        );
    }

    /**
     * Get data to be validated from the widget parameters.
     *
     * @return array
     */
    public function validationData(): array
    {
        $this->ignoreWidgetMethod(__METHOD__);

        $this->validationData['template'] = $this->validationData['template']
            ?? $this->template;

        return $this->validationData;
    }

    /**
     * Determine if the component should be rendered.
     *
     * @return bool
     */
    public function shouldRender(): bool
    {
        return $this->validator->fails()
            || $this->parameters()->get('is_active', true);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return Renderable
     */
    public function render(): Renderable
    {
        return $this->container->make(ViewFactory::class)
            ->make(
                $this->parameters()->get('template'),
                $this->parameters()->all()
            );
    }

    /**
     * Get the validator instance for the request.
     *
     * @return Validator
     */
    protected function getValidatorInstance(): Validator
    {
        return $this->validator ?: $this->createValidator();
    }

    /**
     * Create the default validator instance.
     *
     * @return Validator
     */
    protected function createValidator(): Validator
    {
        return $this->validator = $this->container->make(
            ValidationFactory::class
        )->make(
            $this->validationData(), $this->validationRules(),
            $this->messages(), $this->attributes()
        );
    }

    /**
     * Get the default validation rules that apply to the request.
     *
     * @return array
     */
    protected function validationRules(): array
    {
        $this->ignoreWidgetMethod('rules');

        return array_merge_recursive(
            [
                'template' => [
                    'required',
                    $this->container->make(MustHaveViewRule::class),
                ],
                'is_active' => [
                    'sometimes',
                    'required',
                    'boolean',
                ],
            ],
            $this->container->call([$this, 'rules']),
        );
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    protected function attributes(): array
    {
        return [];
    }

    /**
     * Set the validated parameters for the widget.
     *
     * @param  array  $parameters
     * @return $this
     */
    protected function setParameters(array $parameters = []): self
    {
        $this->parameters->replace($parameters);

        return $this;
    }

    /**
     * Returns the parameters.
     *
     * @return Parameters
     */
    protected function parameters(): Parameters
    {
        return $this->parameters;
    }

    /**
     * Add the method that should be ignored.
     *
     * @param  string  $method
     * @return self
     */
    protected function ignoreWidgetMethod(string $method): self
    {
        $this->except = array_merge($this->except, [
            $method,
        ]);

        return $this;
    }
}
