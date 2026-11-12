<?php

/**
 * Sentry DEKAERP-16 (2026-11-13): changing a dashboard filter re-renders every
 * widget in one batched Livewire request. With the limit at 20 the Overview
 * (23 components) failed outright. Keep the limit well above the largest
 * dashboard.
 */
it('allows a batched update large enough for the busiest dashboard', function () {
    expect(config('livewire.payload.max_components'))->toBeGreaterThanOrEqual(100);
});
