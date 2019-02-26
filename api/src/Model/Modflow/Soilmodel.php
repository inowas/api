<?php

namespace App\Model\Modflow;

final class Soilmodel
{
    private $properties = [];
    private $layers = [];

    public static function fromArray(array $arr): self
    {
        $self = new self();
        $self->properties = $arr['properties'] ?? [];
        $self->layers = $arr['layers'] ?? [];
        return $self;
    }

    public static function create(): Soilmodel
    {
        $self = new self();
        return $self;
    }

    private function __construct()
    {
    }

    public function addLayer(Layer $layer): void
    {
        $this->updateLayer($layer);
    }

    /**
     * @param string $layerId
     * @param string $newLayerId
     * @throws \Exception
     */
    public function cloneLayer(string $layerId, string $newLayerId): void
    {
        /** @var Layer $layer */
        $layer = $this->findLayer($layerId);
        if (!$layer instanceof Layer) {
            throw new \Exception('Layer not found');
        }

        $newLayer = $layer->clone($newLayerId);
        $this->addLayer($newLayer);
    }

    public function updateLayer(Layer $layer): void
    {
        $this->layers[$layer->id()] = $layer->toArray();
    }

    public function findLayer(string $id): ?Layer
    {
        if (!array_key_exists($id, $this->layers)) {
            return null;
        }
        return Layer::fromArray($this->layers[$id]);
    }

    public function removeLayer(string $id): void
    {
        if (array_key_exists($id, $this->layers)) {
            unset($this->layers[$id]);
        }
    }

    public function firstLayer(): ?Layer
    {
        if (count($this->layers) === 0) {
            return null;
        }

        /** @noinspection PhpParamsInspection */
        return Layer::fromArray(array_values($this->layers)[0]);
    }

    public function updateProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function properties(): array
    {
        return $this->properties;
    }

    public function layers(): array
    {
        return array_values($this->layers);
    }

    public function toArray(): array
    {
        return [
            'properties' => $this->properties,
            'layers' => $this->layers
        ];
    }
}
