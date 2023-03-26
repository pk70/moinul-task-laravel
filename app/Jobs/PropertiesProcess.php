<?php

namespace App\Jobs;

use App\Models\Calendar;
use App\Models\Calendars;
use App\Models\Properties;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PropertiesProcess implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * Create a new job instance.
   */
  public $propertydata;
  public $authtoken;
  public function __construct($token)
  {
    $this->authtoken = $token;
    $client = new \GuzzleHttp\Client();

    $response = $client->request('GET', 'https://open-api-sandbox.guesty.com/v1/listings?fields=_id&active=true&listed=true&limit=25', [
      'headers' => [
        'accept' => 'application/json',
        'authorization' => 'Bearer ' . $this->authtoken . '',
      ],
    ]);


    $contents = json_decode($response->getBody()->getContents());
    $this->propertydata = $contents;
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    foreach ($this->propertydata->results as $key => $value) {
      $properties = Properties::updateOrCreate(
        ['listingId' => $value->_id],
        [
          'listingId' => $value->_id,
          'title' => $this->propertydata->title ?? null,
          'address' => $this->propertydata->address ?? null,
          'id_customer' => 6
        ]
      );
      $client = new \GuzzleHttp\Client();

      $response = $client->request('GET', 'https://open-api-sandbox.guesty.com/v1/availability-pricing/api/calendar/listings/' . $properties->listingId . '?startDate=2022-09-16&endDate=2022-09-17&includeAllotment=false', [
        'headers' => [
          'accept' => 'application/json',
          'authorization' => 'Bearer ' . $this->authtoken . '',
        ],
      ]);

      $contents = json_decode($response->getBody()->getContents());
      foreach ($contents->data->days as $key2 => $value2) {
        Calendars::updateOrCreate(
          ['id_property' => $properties->id, 'date' => $value2->date],
          [
            'price' => $value2->price, 'status' => $value2->status,
            'id_property' => $properties->id, 'minNight' => $value2->minNights,
            'date' => $value2->date
          ]
        );
      }
    }
  }
}
