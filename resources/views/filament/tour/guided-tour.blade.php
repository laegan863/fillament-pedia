@if ($tour)
    <script type="application/json" id="hbcr-tour-data" data-user="{{ auth()->id() }}">@json($tour)</script>
@endif

<script data-navigate-once>
    (() => {
        if (window.HbcrTour) {
            return;
        }

        const SPOTLIGHT_PADDING = 6;
        const SCREEN_MARGIN = 12;
        const AUTO_START_DELAY = 700;

        let active = null;

        const readTour = () => {
            const node = document.getElementById('hbcr-tour-data');

            if (!node) {
                return null;
            }

            try {
                const tour = JSON.parse(node.textContent);

                return { ...tour, storageKey: `hbcr-tour:${node.dataset.user}:${tour.id}` };
            } catch (error) {
                return null;
            }
        };

        const hasSeen = (tour) => {
            try {
                return window.localStorage.getItem(tour.storageKey) !== null;
            } catch (error) {
                return true;
            }
        };

        const markSeen = (tour) => {
            try {
                window.localStorage.setItem(tour.storageKey, new Date().toISOString());
            } catch (error) {
                //
            }
        };

        const targetsOf = (step) => {
            if (!step.target) {
                return [];
            }

            let nodes = Array.from(document.querySelectorAll(step.target)).filter((node) => {
                const rect = node.getBoundingClientRect();

                return rect.width > 0 && rect.height > 0;
            });

            if (step.match === 'first') {
                nodes = nodes.slice(0, 1);
            }

            if (step.match === 'last') {
                nodes = nodes.slice(-1);
            }

            return nodes;
        };

        const boundsOf = (nodes) => {
            const rects = nodes.map((node) => node.getBoundingClientRect());

            const top = Math.min(...rects.map((rect) => rect.top));
            const left = Math.min(...rects.map((rect) => rect.left));
            const bottom = Math.max(...rects.map((rect) => rect.bottom));
            const right = Math.max(...rects.map((rect) => rect.right));

            return { top, left, bottom, right, width: right - left, height: bottom - top };
        };

        const element = (tag, className, text = null) => {
            const node = document.createElement(tag);
            node.className = className;

            if (text !== null) {
                node.textContent = text;
            }

            return node;
        };

        const build = () => {
            const root = element('div', 'hbcr-tour');
            const blocker = element('div', 'hbcr-tour__blocker');
            const spotlight = element('div', 'hbcr-tour__spotlight');
            const popover = element('div', 'hbcr-tour__popover');

            popover.setAttribute('role', 'dialog');
            popover.setAttribute('aria-modal', 'true');
            popover.setAttribute('aria-labelledby', 'hbcr-tour-title');

            const progress = element('p', 'hbcr-tour__progress');
            const title = element('h2', 'hbcr-tour__title');
            title.id = 'hbcr-tour-title';
            const body = element('p', 'hbcr-tour__body');

            const footer = element('div', 'hbcr-tour__footer');
            const skip = element('button', 'hbcr-tour__button hbcr-tour__button--link', 'Skip tour');
            const nav = element('div', 'hbcr-tour__nav');
            const back = element('button', 'hbcr-tour__button hbcr-tour__button--secondary', 'Back');
            const next = element('button', 'hbcr-tour__button hbcr-tour__button--primary', 'Next');

            [skip, back, next].forEach((button) => button.setAttribute('type', 'button'));

            nav.append(back, next);
            footer.append(skip, nav);
            popover.append(progress, title, body, footer);
            root.append(blocker, spotlight, popover);

            skip.addEventListener('click', () => finish());
            back.addEventListener('click', () => go(active.index - 1));
            next.addEventListener('click', () => go(active.index + 1));

            return { root, spotlight, popover, progress, title, body, back, next };
        };

        const position = () => {
            if (!active) {
                return;
            }

            const { ui } = active;
            const nodes = targetsOf(active.steps[active.index]);
            const popoverWidth = ui.popover.offsetWidth;
            const popoverHeight = ui.popover.offsetHeight;
            const viewportWidth = document.documentElement.clientWidth;
            const viewportHeight = document.documentElement.clientHeight;

            if (nodes.length === 0) {
                ui.root.classList.add('hbcr-tour--centered');
                Object.assign(ui.spotlight.style, {
                    top: `${viewportHeight / 2}px`,
                    left: `${viewportWidth / 2}px`,
                    width: '0px',
                    height: '0px',
                });
                Object.assign(ui.popover.style, {
                    top: `${Math.max(SCREEN_MARGIN, (viewportHeight - popoverHeight) / 2)}px`,
                    left: `${Math.max(SCREEN_MARGIN, (viewportWidth - popoverWidth) / 2)}px`,
                });

                return;
            }

            ui.root.classList.remove('hbcr-tour--centered');

            const bounds = boundsOf(nodes);

            Object.assign(ui.spotlight.style, {
                top: `${bounds.top - SPOTLIGHT_PADDING}px`,
                left: `${bounds.left - SPOTLIGHT_PADDING}px`,
                width: `${bounds.width + SPOTLIGHT_PADDING * 2}px`,
                height: `${bounds.height + SPOTLIGHT_PADDING * 2}px`,
            });

            const gap = SPOTLIGHT_PADDING + 10;
            let top;
            let left = bounds.left;

            if (viewportHeight - bounds.bottom >= popoverHeight + gap + SCREEN_MARGIN) {
                top = bounds.bottom + gap;
            } else if (bounds.top >= popoverHeight + gap + SCREEN_MARGIN) {
                top = bounds.top - gap - popoverHeight;
            } else if (viewportWidth - bounds.right >= popoverWidth + gap + SCREEN_MARGIN) {
                top = bounds.top;
                left = bounds.right + gap;
            } else {
                top = viewportHeight - popoverHeight - SCREEN_MARGIN;
            }

            Object.assign(ui.popover.style, {
                top: `${Math.min(Math.max(SCREEN_MARGIN, top), viewportHeight - popoverHeight - SCREEN_MARGIN)}px`,
                left: `${Math.min(Math.max(SCREEN_MARGIN, left), viewportWidth - popoverWidth - SCREEN_MARGIN)}px`,
            });
        };

        const isAvailable = (step) => !step.target || targetsOf(step).length > 0;

        /**
         * Steps are checked when reached, not when the tour starts, so targets that load lazily
         * (e.g. dashboard widgets) are still shown. Missing targets are skipped in the direction of travel.
         */
        const nextAvailableIndex = (from, direction) => {
            for (let index = from; index >= 0 && index < active.steps.length; index += direction) {
                if (isAvailable(active.steps[index])) {
                    return index;
                }
            }

            return null;
        };

        const go = (requestedIndex) => {
            if (!active) {
                return;
            }

            const direction = requestedIndex < active.index ? -1 : 1;
            const index = nextAvailableIndex(Math.max(0, requestedIndex), direction);

            if (index === null) {
                if (direction === 1) {
                    finish();
                }

                return;
            }

            active.index = index;

            const { ui, steps } = active;
            const step = steps[index];
            const isLast = nextAvailableIndex(index + 1, 1) === null;

            ui.progress.textContent = `${active.tour.title} · ${index + 1} of ${steps.length}`;
            ui.title.textContent = step.title;
            ui.body.textContent = step.body;
            ui.back.hidden = nextAvailableIndex(index - 1, -1) === null;
            ui.next.textContent = isLast ? 'Finish' : 'Next';

            const [firstTarget] = targetsOf(step);

            if (firstTarget) {
                firstTarget.scrollIntoView({ block: 'center', inline: 'nearest', behavior: 'smooth' });
            }

            position();
            window.setTimeout(position, 400);
            ui.next.focus({ preventScroll: true });
        };

        const onKeydown = (event) => {
            if (!active) {
                return;
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                finish();
            } else if (event.key === 'ArrowRight') {
                event.preventDefault();
                go(active.index + 1);
            } else if (event.key === 'ArrowLeft') {
                event.preventDefault();
                go(active.index - 1);
            }
        };

        const teardown = () => {
            if (!active) {
                return;
            }

            active.ui.root.remove();
            window.removeEventListener('resize', position);
            window.removeEventListener('scroll', position, true);
            document.removeEventListener('keydown', onKeydown, true);
            active = null;
        };

        const finish = () => {
            if (active) {
                markSeen(active.tour);
            }

            teardown();
        };

        const start = () => {
            teardown();

            const tour = readTour();

            if (!tour) {
                return false;
            }

            if (!tour.steps.some(isAvailable)) {
                return false;
            }

            const ui = build();
            document.body.append(ui.root);
            active = { tour, steps: tour.steps, ui, index: 0 };

            window.addEventListener('resize', position);
            window.addEventListener('scroll', position, true);
            document.addEventListener('keydown', onKeydown, true);

            go(0);

            return true;
        };

        const autoStart = () => {
            const tour = readTour();

            if (!tour || hasSeen(tour)) {
                return;
            }

            const tourId = tour.id;

            window.setTimeout(() => {
                const current = readTour();

                if (!active && current && current.id === tourId) {
                    start();
                }
            }, AUTO_START_DELAY);
        };

        window.HbcrTour = { start, finish };

        document.addEventListener('livewire:navigating', teardown);
        document.addEventListener('livewire:navigated', autoStart);

        if (!window.Livewire) {
            document.addEventListener('DOMContentLoaded', () => {
                if (!window.Livewire) {
                    autoStart();
                }
            });
        }
    })();
</script>
