<?php

namespace App\Services;

use App\Models\DTOs\ManufacturerDto;
use App\Models\Manufacturer;
use App\Services\Interfaces\ManufacturerServiceInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class ManufacturerService implements ManufacturerServiceInterface
{
    public function create(ManufacturerDto $model)
    {
        Log::info('Creating manufacturer', $model->toArray());
        $manufacturer = new Manufacturer;
        $manufacturer->name = $model->name;
        $manufacturer->description = $model->description;
        $manufacturer->owner_id = $model->ownerId;
        $manufacturer->save();
        Log::info('Manufacturer has been created');
    }

    public function update(ManufacturerDto $model)
    {
        Log::info('Updating manufacturer', $model->toArray());
        $manufacturer = $this->findById($model->id);
        $manufacturer->name = $model->name;
        $manufacturer->description = $model->description;
        $manufacturer->owner_id = $model->ownerId;
        $manufacturer->update();
        Log::info('Manufacturer has been updated');
    }

    public function findById($id)
    {
        $manufacturer =  Manufacturer::find($id);
        if ($manufacturer == null) {
            Log::warning("Not found manufacturer by ID $id");
            throw new NotFoundResourceException();
        }
        return $manufacturer;
    }

    public function getAll(ManufacturerDto $model = null)
    {
        $data = [];
        if ($model == null) {
            return Manufacturer::paginate();
        }

        Log::info('Getting manufacturer listings');
        $data = Manufacturer::paginate($model->perPage , ['*'], 'page', $model->page);
        return $data;
    }

    public function delete($id)
    {
        $manufacturer = $this->findById($id);
        Log::info('Deleting manufacturer data', (array) $manufacturer);
        $manufacturer->delete();
        Log::info("Deleted manufacturer by ID $manufacturer");
    }
}
