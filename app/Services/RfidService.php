<?php

namespace App\Services;


use App\Models\Rfid;
use App\Models\DTOs\RfidDto;
use App\Services\Interfaces\RfidServiceInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Translation\Exception\NotFoundResourceException;


class RfidService extends ServiceBase implements RfidServiceInterface
{
    public function create(RfidDto $model)
    {
        Log::info('Creating Rfid', $model->toArray());
        $rfid = new Rfid();
        $rfid->rfid = $model->rfid;
        $rfid->save();
        Log::info('Rfid has been created');
    }

    public function update(RfidDto $model)
    {
        Log::info('Updating Rfid', $model->toArray());
        $rfid = $this->findById($model->id);
        $rfid->rfid = $model->rfid;
        $rfid->update();
        Log::info('Rfid has been updated');
    }

    public function findById($id)
    {
        $rfid =  Rfid::find($id);
        if ($rfid == null) {
            Log::warning("Not found Rfid by ID $id");
            throw new NotFoundResourceException();
        }
        return $rfid;
    }


    public function findAll()
    {
        $Rfids =  Rfid::all();
        if ($Rfids->count() == 0) {
            Log::warning("Not found Rfids");
            throw new NotFoundResourceException();
        }
        return $Rfids;
    }

    public function delete($id)
    {
        $rfid = $this->findById($id)->first();
        Log::info('Deleting Rfid data', (array)  $rfid);
        $rfid->delete();
        Log::info("Deleted Rfid by ID $id");
    }
}
