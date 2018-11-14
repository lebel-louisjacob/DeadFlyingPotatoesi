<?php

namespace App\Mail;

use App\Http\Repositories\StationRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class StationsInformation extends Mailable
{
    use Queueable, SerializesModels;

    private $stationRepository;
    protected $station;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->stationRepository = app()->make(StationRepository::class);
        $this->station = $this->stationRepository->first();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('test@hotmail.com', 'Revolvair Test')
            ->view('emails.send')
            ->with([
                'station_id' => $this->station->id,
                'station_name' => $this->station->name,
                'station_city' => $this->station->city,
                'station_latitude' => $this->station->latitude,
                'station_longitude' => $this->station->longitude,
            ]);
    }
}
