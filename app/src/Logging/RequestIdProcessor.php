<?php
declare(strict_types=1);

namespace App\Logging;

use App\EventSubscriber\RequestIdSubscriber;
use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestIdProcessor
{
    public function __construct(private RequestStack $requestStack) {}

    public function __invoke(LogRecord $record): LogRecord {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return $record;
        }

        $rid = $request->attributes->get(RequestIdSubscriber::ATTR);
        if (!is_string($rid) || $rid === '') {
            return $record;
        }

        // LogRecord is immutable-ish: use with() to return a modified copy
        $extra = $record->extra;
        $extra['request_id'] = $rid;

        return $record->with(extra: $extra);
    }
}
