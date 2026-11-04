<?php

/**
 * Sentry must not be wired up while the test suite runs.
 *
 * The DSN comes from `.env`, and `phpunit.xml` used to say nothing about it, so
 * a developer whose `.env` carries the production `SENTRY_LARAVEL_DSN` sent
 * every deliberately `report()`ed exception - plus traced transactions - to the
 * production project, tagged `environment=testing`. Sentry issue 155782383
 * ("RuntimeException: smtp down") is one of those: a SecurityFeature test
 * throwing a simulated mail failure on purpose, recorded as a production error
 * on 2026-11-02. The noise is not cosmetic - it burns quota and hides the
 * failures that are real.
 *
 * `phpunit.xml` now sets `SENTRY_LARAVEL_DSN` to an empty string with
 * `force="true"`. This pins that line from the SDK's side rather than by
 * reading the file: with a DSN the client boots and events leave the machine,
 * with an empty one `BaseServiceProvider::hasDsnSet()` is false and nothing -
 * client, transport or tracing integration - is built at all.
 */
it('leaves Sentry unconfigured while the test suite runs', function () {
    expect(app()->environment('testing'))->toBeTrue()
        ->and(config('sentry.dsn'))->toBeEmpty();
});
