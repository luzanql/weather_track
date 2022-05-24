<?php
namespace App\action\docs\domain;
use JsonSerializable;
/**
 *
 * @OA\Schema(
 *     title="History",
 *     description="Get History model (Logs) response"
 * )
 *
 */
abstract class History implements JsonSerializable
{
    /**
     * @OA\Property(type="integer", format="int64", readOnly=true, example=1)
     */
    private $page_number;

    /**
     * @OA\Property(type="integer", format="int64", readOnly=true, example=10)
     */
    private $page_size;

    /**
     * @OA\Property(type="integer", format="int64", readOnly=true, example=1)
     */
    private $total_record_count;

    /**
     * @OA\Property(
     *      type="array",
     *      @OA\Items(),
     *      example="[{'date': '2020-04-23T09:32:19.000000Z', 'temp': '8.07', 'feels_like': '7'}]"))
     */
    private $records;

}
