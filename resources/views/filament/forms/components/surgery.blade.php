@php
    use Filament\Support\View\Components\ToggleComponent;
    use Illuminate\Support\Arr;

    $fieldWrapperView = $getFieldWrapperView();
    $goalStatePath = $getGoalStatePath();
    $id = $getId();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();

    $toggleOffClasses = Arr::toCssClasses([
        'fi-toggle-off',
        ...\Filament\Support\get_component_color_classes(ToggleComponent::class, 'gray'),
    ]);

    $toggleOnClasses = Arr::toCssClasses([
        'fi-toggle-on',
        ...\Filament\Support\get_component_color_classes(ToggleComponent::class, 'primary'),
    ]);
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    :inline-label-vertical-alignment="\Filament\Support\Enums\VerticalAlignment::Center"
    :state-path="$goalStatePath"
    x-data="{
        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
        goal: $wire.$entangle(@js($goalStatePath)),
    }"
    x-effect="if (! state) goal = null"
>
    <x-slot name="labelPrefix">
        <button
            id="{{ $id }}"
            type="button"
            role="switch"
            x-bind:aria-checked="state ? 'true' : 'false'"
            x-bind:class="state ? @js($toggleOnClasses) : @js($toggleOffClasses)"
            x-on:click="state = ! state"
            @disabled($isDisabled)
            class="fi-toggle fi-fo-toggle"
        >
            <div>
                <div aria-hidden="true"></div>
                <div aria-hidden="true"></div>
            </div>
        </button>
    </x-slot>

    <div
        x-cloak
        x-show="state"
        {{ $getExtraAttributeBag()->class(['fi-fo-field fi-fo-select-wrp']) }}
    >
        <label
            for="{{ $id }}-goal"
            class="fi-fo-field-label"
        >
            <span class="fi-fo-field-label-content">
                {{ $getGoalLabel() }}<sup class="fi-fo-field-label-required-mark">*</sup>
            </span>
        </label>

        <x-filament::input.wrapper
            :valid="! $errors->has($goalStatePath)"
            class="fi-fo-select fi-fo-select-native"
        >
            <select
                id="{{ $id }}-goal"
                x-model="goal"
                x-bind:disabled="! state || @js($isDisabled)"
                x-bind:required="state"
                class="fi-select-input"
            >
                <option value="">
                    {{ $getGoalPlaceholder() }}
                </option>

                @foreach ($getGoalOptions() as $value => $label)
                    <option value="{{ $value }}">
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </x-filament::input.wrapper>
    </div>
</x-dynamic-component>
