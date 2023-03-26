<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Calendar;
use App\Models\Calendars;
use App\Models\Properties;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class PropertiesProcess implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * Create a new job instance.
   */
  public $propertydata;
  public $authtoken;
  public $addressObject;
  public function __construct($token)
  {
    $this->authtoken = $token;
    $client = new \GuzzleHttp\Client();
    $address = [
      "full" => "6918 E Colfax Ave, Denver, CO 80220, USA",
      "lng" => -104.9076316,
      "lat" => 39.7398381,
      "street" => "East Colfax Avenue 6918",
      "city" => "Denver",
      "country" => "United States",
      "zipcode" => "80220",
      "state" => "Colorado"
    ];
    $this->addressObject = json_encode((object)$address);

    $response = $client->request('GET', 'https://open-api-sandbox.guesty.com/v1/listings?fields=632fb8c0679d6c005e81cbaa titlesample ' . $this->addressObject . '&active=true&listed=true&limit=25', [
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

      $response = $client->request('GET', 'https://open-api-sandbox.guesty.com/v1/availability-pricing/api/calendar/listings/' . $properties->listingId . '?startDate=' . Carbon::now()->format("Y-m-d") . '&endDate=' . Carbon::now()->addMonth()->format("Y-m-d") . '&includeAllotment=false', [
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
