<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Http\Controllers\Api;

use CachetHQ\Cachet\Commands\Metric\AddMetricPointCommand;
use CachetHQ\Cachet\Commands\Metric\RemoveMetricPointCommand;
use CachetHQ\Cachet\Models\Metric;
use CachetHQ\Cachet\Models\MetricPoint;
use Carbon\Carbon;
use Exception;
use GrahamCampbell\Binput\Facades\Binput;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MetricPointController extends AbstractApiController
{
    use DispatchesJobs;

    /**
     * Get a single metric point.
     *
     * @param \CachetHQ\Cachet\Models\Metric      $metric
     * @param \CachetHQ\Cachet\Models\MetricPoint $metricPoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMetricPoints(Metric $metric, MetricPoint $metricPoint)
    {
        return $this->item($metricPoint);
    }

    /**
     * Create a new metric point.
     *
     * @param \CachetHQ\Cachet\Models\Metric $metric
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postMetricPoints(Metric $metric)
    {
        try {
            $metricPoint = $this->dispatch(new AddMetricPointCommand($metric, Binput::get('value'), Binput::get('timestamp')));
        } catch (Exception $e) {
            throw new BadRequestHttpException();
        }

        return $this->item($metricPoint);
    }

    /**
     * Updates a metric point.
     *
     * @param \CachetHQ\Cachet\Models\Metric      $metric
     * @param \CachetHQ\Cachet\Models\MetircPoint $metricPoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function putMetricPoint(Metric $metric, MetricPoint $metricPoint)
    {
        $metricPointData = Binput::all();
        $metricPointData['metric_id'] = $metric->id;

        if ($timestamp = array_pull($metricPointData, 'timestamp')) {
            $pointTimestamp = Carbon::createFromFormat('U', $timestamp);
            $metricPointData['created_at'] = $pointTimestamp->format('Y-m-d H:i:s');
        }

        $metricPoint->update($metricPointData);

        return $this->item($metricPoint);
    }

    /**
     * Destroys a metric point.
     *
     * @param \CachetHQ\Cachet\Models\Metric      $metric
     * @param \CachetHQ\Cachet\Models\MetricPoint $metricPoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMetricPoint(Metric $metric, MetricPoint $metricPoint)
    {
        $this->dispatch(new RemoveMetricPointCommand($metricPoint));

        return $this->noContent();
    }
}
