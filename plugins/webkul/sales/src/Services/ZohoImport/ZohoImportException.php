<?php

namespace Webkul\Sale\Services\ZohoImport;

use RuntimeException;

/**
 * A refusal or failure of the Zoho Books import, worded for the person
 * running the command. Never reported to Sentry: messages name client
 * documents, and client data stays out of logs.
 */
class ZohoImportException extends RuntimeException {}
