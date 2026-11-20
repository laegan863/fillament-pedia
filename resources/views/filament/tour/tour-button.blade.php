<x-filament::icon-button
    icon="heroicon-o-academic-cap"
    color="gray"
    label="Take a tour of this page"
    tooltip="Take a tour of this page"
    data-tour="tour-button"
    x-on:click="window.HbcrTour && window.HbcrTour.start()"
/>
