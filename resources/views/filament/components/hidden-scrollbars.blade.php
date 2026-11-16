{{--
    Hide scrollbars everywhere in the panel (user, 2026-11-15: bars on some
    pages and on the far right looked untidy). Scrolling still works by wheel,
    touch, trackpad and keyboard; only the bar is not drawn. Lives in the
    head so it needs no CSS rebuild.
--}}
<style>
    html, body, * {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    html::-webkit-scrollbar,
    body::-webkit-scrollbar,
    *::-webkit-scrollbar {
        display: none;
        width: 0;
        height: 0;
    }
</style>
