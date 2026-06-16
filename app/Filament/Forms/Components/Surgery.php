<?php

namespace App\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

class Surgery extends Field
{
    protected string $view = 'filament.forms.components.surgery';

    protected string | Closure | null $goalStatePath = null;

    protected string | Closure | null $goalLabel = null;

    protected string | Closure | null $goalPlaceholder = null;

    /**
     * @var array<string, string> | Closure
     */
    protected array | Closure $goalOptions = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(false);
        $this->goalStatePath('surgery_goal');
        $this->goalLabel('Surgery Goal');
        $this->goalPlaceholder('Select surgery goal');
    }

    public function goalStatePath(string | Closure | null $statePath): static
    {
        $this->goalStatePath = $statePath;

        return $this;
    }

    public function goalLabel(string | Closure | null $label): static
    {
        $this->goalLabel = $label;

        return $this;
    }

    public function goalPlaceholder(string | Closure | null $placeholder): static
    {
        $this->goalPlaceholder = $placeholder;

        return $this;
    }

    /**
     * @param  array<string, string> | Closure  $options
     */
    public function goalOptions(array | Closure $options): static
    {
        $this->goalOptions = $options;

        return $this;
    }

    public function getGoalStatePath(): string
    {
        return $this->resolveRelativeStatePath($this->evaluate($this->goalStatePath) ?? 'surgery_goal');
    }

    public function getGoalLabel(): string
    {
        return $this->evaluate($this->goalLabel) ?? 'Surgery Goal';
    }

    public function getGoalPlaceholder(): string
    {
        return $this->evaluate($this->goalPlaceholder) ?? 'Select surgery goal';
    }

    /**
     * @return array<string, string>
     */
    public function getGoalOptions(): array
    {
        return $this->evaluate($this->goalOptions) ?? [];
    }
}
