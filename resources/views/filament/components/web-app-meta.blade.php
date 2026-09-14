{{--
    Home-screen install metadata for the admin panel (rendered at HEAD_END, so the
    login page carries it too - that's where most people add the app).

    The manifest uses display "minimal-ui", not "standalone": Android installs it
    with a slim back/reload bar, and iOS (which only honours standalone) opens it
    as a normal Safari tab. A fully chromeless app has no back button, and this
    panel opens PDFs and downloads (invoice print, reports) that would leave a
    user stuck on the file with no way back. For the same reason there is no
    apple-mobile-web-app-capable tag here - it forces standalone on iOS.
--}}
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<link rel="apple-touch-icon" href="{{ asset('images/icons/apple-touch-icon.png') }}">
<meta name="apple-mobile-web-app-title" content="DEKA ERP">
<meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#18181b" media="(prefers-color-scheme: dark)">
