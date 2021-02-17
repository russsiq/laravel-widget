<?php

namespace Russsiq\Widget\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\View\Factory as ViewFactory;

class MustHaveViewRule implements Rule, ImplicitRule
{
    /** @var ViewFactory */
    protected $viewFactory;

    /** @var string */
    protected $view;

    /**
     * Create a new rule instance.
     */
    public function __construct(ViewFactory $viewFactory)
    {
        $this->viewFactory = $viewFactory;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $value = trim($value);

        if (empty($value)) {
            return false;
        }

        if (is_null($this->view)) {
            $this->setView($value);
        }

        return $this->viewFactory->exists($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return is_null($this->view)
            ? "View path not defined."
            : "View [{$this->view}] not found.";
    }

    /**
     * Set the name of the view.
     *
     * @param  string  $name
     * @return void
     */
    protected function setView(string $name): void
    {
        $this->view = $name;
    }
}
