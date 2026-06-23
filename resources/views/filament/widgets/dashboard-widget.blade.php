<x-filament-widgets::widget class="fi-registry-dashboard">
    @if (! $hasDashboardData)
        <div class="fi-registry-empty">
            <x-filament::icon
                icon="heroicon-o-circle-stack"
                class="fi-registry-empty__icon"
            />

            <h2 class="fi-registry-empty__title">
                No dashboard data yet
            </h2>

            <x-filament::link
                :href="$links['patients']"
                icon="heroicon-o-arrow-right"
                icon-position="after"
            >
                Open Patient Records
            </x-filament::link>
        </div>
    @else
        <div class="fi-registry-dashboard__layout">
            <section class="fi-registry-panel fi-registry-panel--clinical">
                <header class="fi-registry-panel__header">
                    <div>
                        <h2 class="fi-registry-panel__title">
                            Clinical Snapshot
                        </h2>

                        <p class="fi-registry-panel__description">
                            Diagnosis, treatment, and outcome distribution
                        </p>
                    </div>

                    <x-filament::link
                        :href="$links['diagnoses']"
                        class="fi-registry-panel__action"
                        icon="heroicon-o-arrow-right"
                        icon-position="after"
                    >
                        View Diagnoses
                    </x-filament::link>
                </header>

                <div class="fi-registry-bar-chart">
                    <svg
                        class="fi-registry-bar-chart__svg"
                        viewBox="0 0 360 230"
                        role="img"
                        aria-label="Clinical snapshot bar chart"
                    >
                        @foreach ($clinicalBarChart['ticks'] as $tick)
                            <line
                                class="fi-registry-bar-chart__grid"
                                x1="42"
                                x2="338"
                                y1="{{ $tick['y'] }}"
                                y2="{{ $tick['y'] }}"
                            />

                            <text
                                class="fi-registry-bar-chart__tick"
                                x="34"
                                y="{{ $tick['y'] + 4 }}"
                                text-anchor="end"
                            >
                                {{ $tick['label'] }}
                            </text>
                        @endforeach

                        <line
                            class="fi-registry-bar-chart__axis"
                            x1="42"
                            x2="338"
                            y1="170"
                            y2="170"
                        />

                        @foreach ($clinicalBarChart['bars'] as $bar)
                            <rect
                                class="fi-registry-bar-chart__track"
                                x="{{ $bar['x'] }}"
                                y="38"
                                width="{{ $bar['width'] }}"
                                height="132"
                                rx="10"
                            />

                            <rect
                                class="fi-registry-bar-chart__bar"
                                x="{{ $bar['x'] }}"
                                y="{{ $bar['y'] }}"
                                width="{{ $bar['width'] }}"
                                height="{{ $bar['height'] }}"
                                rx="10"
                                fill="{{ $bar['color'] }}"
                            />

                            <text
                                class="fi-registry-bar-chart__value"
                                x="{{ $bar['center'] }}"
                                y="{{ max($bar['y'] - 10, 20) }}"
                                text-anchor="middle"
                            >
                                {{ $bar['formattedCount'] }}
                            </text>

                            <text
                                class="fi-registry-bar-chart__label"
                                x="{{ $bar['center'] }}"
                                y="202"
                                text-anchor="middle"
                            >
                                {{ $bar['label'] }}
                            </text>
                        @endforeach
                    </svg>

                    <div class="fi-registry-bar-chart__legend">
                        @foreach ($clinicalBarChart['bars'] as $bar)
                            <article class="fi-registry-chart-legend-item">
                                <svg
                                    class="fi-registry-chart-legend-item__marker"
                                    viewBox="0 0 12 12"
                                    aria-hidden="true"
                                >
                                    <circle
                                        cx="6"
                                        cy="6"
                                        r="6"
                                        fill="{{ $bar['color'] }}"
                                    />
                                </svg>

                                <div class="fi-registry-chart-legend-item__text">
                                    <span>{{ $bar['fullLabel'] }}</span>
                                    <strong>{{ $bar['detail'] }}</strong>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="fi-registry-panel fi-registry-panel--treatments">
                <header class="fi-registry-panel__header">
                    <div>
                        <h2 class="fi-registry-panel__title">
                            Treatment Plan Coverage
                        </h2>

                        <p class="fi-registry-panel__description">
                            Selected diagnosis treatment plans
                        </p>
                    </div>
                </header>

                <div class="fi-registry-pie-chart">
                    <div class="fi-registry-pie-chart__visual">
                        <svg
                            class="fi-registry-pie-chart__svg"
                            viewBox="0 0 42 42"
                            role="img"
                            aria-label="Treatment plan coverage pie chart"
                        >
                            <circle
                                class="fi-registry-pie-chart__track"
                                cx="21"
                                cy="21"
                                r="15.9155"
                            />

                            @foreach ($treatmentPieChart['segments'] as $segment)
                                @if ($segment['percentage'] > 0)
                                    <circle
                                        class="fi-registry-pie-chart__segment"
                                        cx="21"
                                        cy="21"
                                        r="15.9155"
                                        stroke="{{ $segment['color'] }}"
                                        stroke-dasharray="{{ $segment['percentage'] }} {{ 100 - $segment['percentage'] }}"
                                        stroke-dashoffset="{{ -1 * $segment['offset'] }}"
                                        transform="rotate(-90 21 21)"
                                    />
                                @endif
                            @endforeach
                        </svg>

                        <div class="fi-registry-pie-chart__center">
                            <strong>{{ $treatmentPieChart['formattedTotal'] }}</strong>
                            <span>Selections</span>
                        </div>
                    </div>

                    <div class="fi-registry-pie-chart__legend">
                        @foreach ($treatmentPieChart['segments'] as $segment)
                            <article class="fi-registry-pie-chart__legend-item">
                                <span class="fi-registry-pie-chart__legend-label">
                                    <svg
                                        class="fi-registry-chart-legend-item__marker"
                                        viewBox="0 0 12 12"
                                        aria-hidden="true"
                                    >
                                        <circle
                                            cx="6"
                                            cy="6"
                                            r="6"
                                            fill="{{ $segment['color'] }}"
                                        />
                                    </svg>

                                    <span>{{ $segment['label'] }}</span>
                                </span>

                                <strong>
                                    {{ $segment['formattedCount'] }}
                                    <span>{{ $segment['formattedPercentage'] }}</span>
                                </strong>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="fi-registry-panel fi-registry-panel--recent">
                <header class="fi-registry-panel__header">
                    <div>
                        <h2 class="fi-registry-panel__title">
                            Recent Follow-Ups
                        </h2>

                        <p class="fi-registry-panel__description">
                            Latest patient follow-up records
                        </p>
                    </div>

                    <x-filament::link
                        :href="$links['followUps']"
                        class="fi-registry-panel__action"
                        icon="heroicon-o-arrow-right"
                        icon-position="after"
                    >
                        View Follow-Ups
                    </x-filament::link>
                </header>

                <div class="fi-registry-follow-ups">
                    @forelse ($recentFollowUps as $followUp)
                        <a
                            href="{{ $followUp['url'] }}"
                            wire:navigate
                            class="fi-registry-follow-up"
                        >
                            <div class="fi-registry-follow-up__patient">
                                <span class="fi-registry-follow-up__name">
                                    {{ $followUp['patient'] }}
                                </span>

                                <span class="fi-registry-follow-up__meta">
                                    {{ $followUp['patientId'] }} &middot; {{ $followUp['encounterDate'] }}
                                </span>
                            </div>

                            <div class="fi-registry-follow-up__badges">
                                <x-filament::badge color="warning">
                                    {{ $followUp['treatmentStatus'] }}
                                </x-filament::badge>

                                <x-filament::badge color="success">
                                    {{ $followUp['diseaseOutcome'] }}
                                </x-filament::badge>
                            </div>
                        </a>
                    @empty
                        <p class="fi-registry-empty-line">
                            No follow-up records yet.
                        </p>
                    @endforelse
                </div>
            </section>
        </div>
    @endif
</x-filament-widgets::widget>
