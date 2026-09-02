# DEKA ERP production image.
#
# Built from the REPOSITORY ROOT as the build context, so the image contains
# this fork. Build with:
#
#   docker build -f docker/production/Dockerfile -t dekaerp:latest .
#
# Most container platforms build this way by default (repo as context, with the
# Dockerfile path set), which is why the image must not fetch its own source.
#
# The database is external - Supabase, or any managed Postgres/MySQL. The image
# ships no database server and runs no installer at build time; schema
# creation is a deploy step, see docs/supabase-database.md.

FROM ubuntu:24.04

ARG PHP_VERSION=8.4
ARG NODE_VERSION=22

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

RUN apt-get update && apt-get install -y \
        apt-transport-https \
        ca-certificates \
        curl \
        git \
        gnupg \
        software-properties-common \
        unzip \
    && add-apt-repository ppa:ondrej/php -y \
    && apt-get update && apt-get install -y \
        imagemagick \
        libmagickwand-dev \
        nginx \
        supervisor \
    && rm -rf /var/lib/apt/lists/*

# pgsql for Supabase and any managed Postgres; mysql retained so an external
# MySQL/MariaDB remains a supported DB_CONNECTION.
RUN apt-get update && apt-get install -y \
        php${PHP_VERSION}-bcmath \
        php${PHP_VERSION}-cli \
        php${PHP_VERSION}-curl \
        php${PHP_VERSION}-dev \
        php${PHP_VERSION}-exif \
        php${PHP_VERSION}-fpm \
        php${PHP_VERSION}-gd \
        php${PHP_VERSION}-gmp \
        php${PHP_VERSION}-intl \
        php${PHP_VERSION}-mbstring \
        php${PHP_VERSION}-mysql \
        php${PHP_VERSION}-pgsql \
        php${PHP_VERSION}-redis \
        php${PHP_VERSION}-soap \
        php${PHP_VERSION}-xml \
        php${PHP_VERSION}-zip \
        php-pear \
    && rm -rf /var/lib/apt/lists/*

RUN pecl channel-update pecl.php.net \
    && mkdir -p /tmp/imagick-build && cd /tmp/imagick-build \
    && pecl download imagick \
    && tar xzf imagick-*.tgz \
    && cd imagick-*/ \
    && phpize${PHP_VERSION} \
    && ./configure --with-php-config=/usr/bin/php-config${PHP_VERSION} \
    && make -j"$(nproc)" \
    && make install \
    && echo "extension=imagick.so" > /etc/php/${PHP_VERSION}/mods-available/imagick.ini \
    && phpenmod -v ${PHP_VERSION} imagick \
    && rm -rf /tmp/imagick-build

# Excimer powers Sentry profiling. Built the same way as imagick, and placed
# here so it lands before php-dev and php-pear are purged below.
#
# Deliberately best-effort: profiling is optional and the SDK degrades to a
# logged warning without the extension, so a release that fails to build
# against a new PHP must not take the whole image - and every deploy - down
# with it. The failure prints, so a missing profiler is findable in the log.
RUN set -eux; \
    if mkdir -p /tmp/excimer-build && cd /tmp/excimer-build \
        && pecl download excimer \
        && tar xzf excimer-*.tgz \
        && cd excimer-*/ \
        && phpize${PHP_VERSION} \
        && ./configure --with-php-config=/usr/bin/php-config${PHP_VERSION} \
        && make -j"$(nproc)" \
        && make install; then \
        echo "extension=excimer.so" > /etc/php/${PHP_VERSION}/mods-available/excimer.ini; \
        phpenmod -v ${PHP_VERSION} excimer; \
        echo "excimer installed; Sentry profiling available"; \
    else \
        echo "WARNING: excimer build failed; image ships without Sentry profiling"; \
    fi; \
    rm -rf /tmp/excimer-build

RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get purge -y php${PHP_VERSION}-dev php-pear \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/dekaerp

# The application source, from the build context. .dockerignore at the
# repository root keeps secrets, vendor, node_modules and dev tooling out.
COPY . .

# Composer pulls most dists from GitHub's API, which rate-limits unauthenticated
# requests and returns 504s under load. A single failed zipball fails the whole
# build, so: retry twice, then fall back to installing from source rather than
# giving up. Pass a token as the COMPOSER_AUTH build arg to avoid the limit
# altogether on a busy CI host.
#
# If rate limiting becomes chronic, supply a token as a BuildKit secret rather
# than a build arg - an ARG or ENV named for a credential leaks into image
# history or the image itself:
#
#   RUN --mount=type=secret,id=composer_auth,env=COMPOSER_AUTH composer install ...
#   docker build --secret id=composer_auth,env=COMPOSER_AUTH ...
#
RUN set -eux; \
    for attempt in 1 2 3; do \
        if composer install \
            --no-dev \
            --no-interaction \
            --prefer-dist \
            --optimize-autoloader \
            --no-scripts; then \
            break; \
        fi; \
        if [ "$attempt" = "3" ]; then \
            echo "dist downloads kept failing; falling back to source"; \
            composer install \
                --no-dev \
                --no-interaction \
                --prefer-source \
                --optimize-autoloader \
                --no-scripts; \
        else \
            echo "composer install failed (attempt ${attempt}); retrying"; \
            sleep 15; \
        fi; \
    done; \
    rm -rf /root/.composer/cache

# A baseline .env. Every value that matters in production is rewritten by
# entrypoint.sh from the container environment; this only guarantees the file
# exists and is not in debug mode should a variable go unset.
# Every artisan boot calls Package::isPluginInstalled(), which opens a PDO
# connection. No database exists during the build and the failure is caught -
# but pointing at an unresolvable hostname makes each of the artisan commands
# below wait out a DNS timeout first. 127.0.0.1 is refused instantly, which is
# the difference between a slow build and a stalled one.
#
# Keep these comments outside the RUN: a # inside a line continuation is joined
# into the command and comments out everything after it, which silently dropped
# the DB_HOST substitution when it lived below.
RUN cp .env.example .env \
    && sed -i 's/^APP_ENV=.*/APP_ENV=production/' .env \
    && sed -i 's/^APP_DEBUG=.*/APP_DEBUG=false/' .env \
    && sed -i 's/^DB_HOST=.*/DB_HOST=127.0.0.1/' .env \
    && grep -q '^DB_HOST=127.0.0.1$' .env

# A key is required for artisan to boot during the build. It is a placeholder:
# entrypoint.sh overwrites it whenever APP_KEY is supplied, and production
# MUST supply one - otherwise every container from this image shares a key.
RUN php artisan key:generate --force --no-interaction

RUN npm ci --no-audit --no-fund \
    && npm run build \
    && rm -rf node_modules /root/.npm \
    && php artisan package:discover --ansi \
    && php artisan filament:assets \
    && php artisan vendor:publish --tag=laravel-assets --force --ansi \
    && php artisan storage:link

COPY docker/production/php.ini /etc/php/${PHP_VERSION}/fpm/conf.d/99-aureus.ini
COPY docker/production/php.ini /etc/php/${PHP_VERSION}/cli/conf.d/99-aureus.ini
COPY docker/production/php-fpm.conf /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf

RUN rm -f /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf
COPY docker/production/nginx.conf /etc/nginx/conf.d/aureus.conf

COPY docker/production/supervisord.conf /etc/supervisor/conf.d/aureus.conf

# Laravel requires these to exist; .dockerignore strips their contents.
RUN mkdir -p /run/php /var/log/supervisor /var/log/aureus \
        storage/app/public \
        storage/app/private \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache

RUN chown -R www-data:www-data /var/www/dekaerp /var/log/aureus \
    && chmod -R 775 storage bootstrap/cache \
    && find storage bootstrap/cache -type d -exec chmod g+s {} +

COPY docker/production/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN apt-get purge -y git nodejs \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=60s --retries=3 \
    CMD curl -fsS http://127.0.0.1/health || exit 1

ENTRYPOINT ["bash", "/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
